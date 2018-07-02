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
use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\History\HistoryPlugin;
use function rand;

class Mailer
{
	const URL_LOGIN = 'https://mail.ru/';
	const URL_AUTH = 'https://auth.mail.ru/cgi-bin/auth';
	const URL_COMPOSE = 'https://m.mail.ru/compose/';
	const URL_API = 'https://e.mail.ru/api/v1';
	const URL_API_SEND = 'https://e.mail.ru/api/v1/messages/send';
	
	public $client;
	public $cookie;
	public $history;
	public $login;
	public $errors = [];
	public $isAuth = false;
	
	public function __construct()
	{
		$this->client = new Client();
		$this->history = new HistoryPlugin();
		$this->cookie  = new CookiePlugin(new ArrayCookieJar());
		$this->client->addSubscriber($this->history);
		$this->client->addSubscriber($this->cookie);
	}
	
	public function getHistoryUrl()
	{
		$url = [];
		foreach ($this->history->getAll() as $item) {
			$url[] = $item['request']->getUrl();
		}
		return $url;
	}
	
	public function auth($login, $pass)
	{
		$response = $this->request(self::URL_LOGIN);
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
		
		$response = $this->request(self::URL_AUTH, 'post', $data);
		$raw      = $response->getBody();
		$parser   = new ParserSuccessAuth($raw);
		$result   = $parser->getResult();
		return $this->isAuth = $this->login == $result;
	}
	
	public function request(string $url, string $type = 'get', array $data = [], array $headers = [], array $options = [])
	{
		switch ($type) {
			case 'post':
				$request = $this->client->post($url, $headers, $data, $options);
				break;
			case 'get':
			default:
				$request = $this->client->get($url, $headers, $options);
		}
		$response = $request->send();
		return $response;
	}
	
	public function sendMessage(Message $msg) {
		$params = $this->getMsgParams();
		$msg->setParams($params);
		$data = $msg->getData();
		$response = $this->request(self::URL_API_SEND.'?logid='.$params['logid'], 'post', $data);
		$raw = $response->getBody();
		echo $raw;
	}
	
	public function getMsgParams() {
		$response = $this->request(self::URL_COMPOSE.'?'.mtime());
		$raw = $response->getBody();
		$parser = new ParserCompose($raw);
		$data = $parser->getResult();
		$data['tab-time'] = time()-rand(2000,10000);
		$data['logid'] = mtime(1).genStr(10,0,1);
		$data['id'] = genStr(1,0,0,1).genStr(31,1,1,1);
		$data['email'] = $this->login;
		return $data;
	}
	
	
	
}