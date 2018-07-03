<?php
/**
 * Created by PhpStorm.
 * User: 4erk
 * Date: 30.06.2018
 * Time: 5:28
 */

namespace app;


use app\Parsers\ParserCompose;
use app\Parsers\ParserSuccessAuth;
use app\Parsers\ParserToken;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Exception\GuzzleException;
use function array_key_exists;
use function http_build_query;
use function json_decode;
use const PATHINFO_BASENAME;


class Mailer
{
	const URL_LOGIN = 'https://mail.ru/';
	const URL_AUTH = 'https://auth.mail.ru/cgi-bin/auth';
	const URL_COMPOSE = 'https://e.mail.ru/compose/';
	const URL_API = 'https://e.mail.ru/api/v1';
	const URL_API_SEND = 'https://e.mail.ru/api/v1/messages/send';
	const URL_API_ADDFILE = 'https://e.mail.ru/api/v1/messages/attaches/add';
	const URL_API_CHECKAUTH = 'https://e.mail.ru/messages/inbox';
	
	public $client;
	public $cookie;
	public $history;
	public $login;
	public $errors = [];
	public $log = [];
	public $isAuth = false;
	
	public function __construct($login, $pass)
	{
		$this->cookie = new FileCookieJar($login.md5($login.':'.$pass).'.cookie');
		$this->client = new Client(['cookies' => $this->cookie]);
		if (!$this->checkAuth()) {
			$this->addLog('New Auth', [$login,$pass]);
			$this->auth($login,$pass);
		}
		else {
			$this->addLog('Authed', [$login,$pass]);
		}
	}
	
	public function auth($login, $pass)
	{
		$response = $this->get(self::URL_LOGIN);
		$raw      = $response->getBody();
		$parser   = new ParserToken($raw);
		$token    = $parser->getResult();
		
		$data        = [
			'new_auth_form' => 1,
			'FromAccount'   => 1,
			'saveauth'      => 1,
			'token'         => $token,
		];
		$this->login = $login;
		$login       = explode('@', $login);
		if (sizeof($login) < 2) {
			$this->errors[] = 'Invalid login';
			return false;
		}
		$data['Login']    = $login[0];
		$data['Password'] = $pass;
		$data['Domain']   = $login[1];
		
		$response = $this->post(self::URL_AUTH, $data);
		$raw      = $response->getBody();
		$parser   = new ParserSuccessAuth($raw);
		$result   = $parser->getResult();
		if ($this->login == $result) {
			$this->isAuth = true;
			return true;
		}
		else {
			$this->isAuth = false;
			$this->errors[] = 'Auth failed';
			return false;
		}
	}
	
	public function checkAuth() {
		$response = $this->get(self::URL_API_CHECKAUTH);
		$raw      = $response->getBody();
		$parser   = new ParserSuccessAuth($raw);
		$result   = $parser->getResult();
		return $this->isAuth = $this->login == $result;
	}
	
	
	
	public function sendMessage(Message $msg)
	{
		if ($this->isAuth) {
			$params = $this->getMsgParams();
			$msg->setParams($params);
			
			foreach ($msg->getFiles() as $file) {
				$data = $this->attachFile($file);
				if ($data) $file->setAttachData($data);
			}
			$data     = $msg->getData();
			$response = $this->post(self::URL_API_SEND . '?logid=' . mtime(1) . genStr(10, 0, 1), $data, [
				'X-Requested-Id'   => genStr(32, 1, 1),
				'X-Requested-With' => 'XMLHttpRequest',
			]);
			$raw = $response->getBody();
			$this->addLog('Send message',['data'=>$data,'result'=>$raw]);
			$result = json_decode($raw, true);
			if (!$result || !array_key_exists('status', $result) || $result['status']!==200) {
				$this->errors[] = 'Send message failed';
			}
		}
		return $this;
	}
	
	
	
	/**************************************************************************************************/
	/**************************************************************************************************/
	/**************************************************************************************************/
	/***********************                                                    ***********************/
	/***********************                     PRIVATES                       ***********************/
	/***********************                                                    ***********************/
	/**************************************************************************************************/
	/**************************************************************************************************/
	/**************************************************************************************************/
	
	private function getMsgParams()
	{
		$response         = $this->get(self::URL_COMPOSE . '?' . mtime());
		$raw              = $response->getBody();
		$parser           = new ParserCompose($raw);
		$data             = $parser->getResult();
		$data['tab-time'] = time() - rand(2000, 10000);
		$data['logid']    = mtime(1) . genStr(10, 0, 1);
		$data['id']       = genStr(31, 1, 1, 1);
		$data['email']    = $this->login;
		return $data;
	}
	
	private function attachFile(File $file) {
		$data = $file->getData();
		$query = $file->queryParams();
		$queryraw = http_build_query($query);
		$response = $this->files(self::URL_API_ADDFILE.'?'.$queryraw,$data,['file'=>$file->getFile()]);
		$raw = $response->getBody();
		$this->addLog('Send message',['data'=>$data,'result'=>$raw,'query'=>$query]);
		$result = json_decode($raw, true);
		if (!$result || !array_key_exists('status', $result) || $result['status']!==200) {
			$this->errors[] = 'Send message failed';
		}
		return $result;
	}
	
	private function addLog($msg, $data) {
		$this->log[] = [
			'date' => date('Y-m-d H:i:s'),
			'message' => $msg,
			'data' => $data,
		];
	}
	
	private function get(string $url, array $data = [], array $headers = [])
	{
		try {
			$response = $this->client->request('GET', $url, [
				'query'   => $data,
				'headers' => $headers,
			]);
			return $response;
		} catch (GuzzleException $e) {
			$this->errors[] = $e->getMessage();
			return false;
		}
	}
	
	private function post(string $url, array $data = [], array $headers = [])
	{
		try {
			$response = $this->client->request('POST', $url, [
				'form_params' => $data,
				'headers'     => $headers,
			]);
			return $response;
		} catch (GuzzleException $e) {
			$this->errors[] = $e->getMessage();
			return false;
		}
	}
	
	private function files(string $url, array $data = [], array $files = [], array $headers = [])
	{
		$multipart = [];
		foreach ($data as $k => $v) {
			$multipart[] = [
				'name'     => $k,
				'contents' => $v,
			];
		}
		foreach ($files as $k => $v) {
			if (file_exists($v)) {
				$multipart[] = [
					'name'     => $k,
					'contents' => fopen($v, 'r'),
					'filename' => pathinfo($v, PATHINFO_BASENAME),
				];
			}
		}
		try {
			$response = $this->client->request('POST', $url, [
				'multipart' => $multipart,
				'headers'   => $headers,
			]);
			return $response;
		} catch (GuzzleException $e) {
			$this->errors[] = $e->getMessage();
			return false;
		}
	}
	
	
}
