<?php
/**
 * Created by PhpStorm.
 * User: 4erk
 * Date: 30.06.2018
 * Time: 22:19
 */

namespace app;

use Error;
use function array_key_exists;
use function explode;
use function in_array;
use function json_encode;
use function str_replace;

class Message
{
	private $attributes = [
//		'__urlp'          => '/messages/send/?logid=$logid',
		'ajax_call'       => 1,
		'x-email'         => '',
		'tarball'         => '',
		'tab-time'        => 0,
		'email'           => '',
		'htmlencoded'     => 'false',
		'id'              => '',
		'source'          => [
			'draft'    => '',
			'reply'    => '',
			'forward'  => '',
			'schedule' => '',
		],
		'template'        => '',
		'sign'            => 0,
		'remind'          => '',
		'receipt'         => 'false',
		'subject'         => '',
		'priority'        => '',
		'capcha'          => '',
		'correspondents'  => [
			'to'  => '',
			'cc'  => '',
			'bcc' => '',
		],
		'edited_contacts' => [],
		'attaches'        => [
			'list' => [],
		],
		'body'            => [
			'html' => '',
		],
		'api'             => 1,
		'token'           => '',
	
	];
	private $toJson = [
		'source',
		'correspondents',
		'edited_contacts',
		'attaches',
		'body',
	];
	private $params = [
		'logid'    => '',
		'tarball'  => '',
		'tab-time' => 0,
		'id'       => '',
		'token'    => '',
		'email'    => '',
		'to'       => '',
		'subject'  => '',
		'body'     => '',
	];
	private $data = [];
	
	/* @var $files File[] */
	private $files = [];
	
	public function __construct(array $params = [])
	{
	
	}
	
	public function setParams(array $params)
	{
		foreach ($this->params as $k => $v) {
			if (array_key_exists($k, $params)) $this->params[$k] = $params[$k];
		}
		$this->applyParams();
		return $this;
	}
	
	
	private function applyParams()
	{
//		$this->attributes['__urlp']               = str_replace('$logid', $this->params['logid'], $this->attributes['__urlp']);
		$this->attributes['tarball']              = $this->params['tarball'];
		$this->attributes['tab-time']             = $this->params['tab-time'];
		$this->attributes['id']                   = $this->params['id'];
		$this->attributes['token']                = $this->params['token'];
		$this->attributes['email']                = $this->params['email'];
		$this->attributes['x-email']              = $this->params['email'];
		$this->attributes['subject']              = $this->params['subject'];
		$this->attributes['body']['html']         = $this->params['body'];
		$this->attributes['correspondents']['to'] = $this->params['to'];
	}
	
	private function prepareData()
	{
		$this->data                           = [];
		$this->attributes['attaches']['list'] = [];
		foreach ($this->files as $name => $file) {
			$data                                   = $file->getAttachData();
			$this->attributes['attaches']['list'][] = $data;
			if ($data['type'] == 'inline') {
				$this->attributes['body']['html'] = $this->replaceImageTag($this->attributes['body']['html'], $name, $data);
			}
		}
		foreach ($this->attributes as $k => $v) {
			$this->data[$k] = in_array($k, $this->toJson) ? json_encode($v) : $v;
		}
	}
	
	private function replaceImageTag($html, $name, $data)
	{
		$replace = '<img id="' . genStr(22, 1, 1, 1) . '" alt="" style="" src="cid:' . $data['content_id'] . '">';
		$needle  = '$image[' . $name . ']';
		$html    = str_replace($needle, $replace, $html);
		return $html;
	}
	
	public function getData()
	{
		$this->prepareData();
		return $this->data;
	}
	
	public function to(string $email)
	{
		$name               = explode('@', $email);
		$to                 = $name[0] . ' <' . $email . '>';
		$this->params['to'] = $to;
		return $this;
	}
	
	public function message(string $msg)
	{
		$this->params['body'] = $msg;
		return $this;
	}
	
	public function subject(string $subject)
	{
		$this->params['subject'] = $subject;
		return $this;
	}
	
	
	public function queryParams()
	{
		return [
			'email'       => $this->attributes['email'],
			'htmlencoded' => $this->attributes['htmlencoded'],
			'rnd'         => mtime(),
			'message_id'  => $this->attributes['id'],
			'token'       => $this->attributes['token'],
		];
	}
	
	public function addFile(string $file)
	{
		try {
			$file          = new File($this, $file);
			$this->files[] = $file;
			return $this;
		} catch (Error $e) {
			return false;
		}
	}
	
	public function addImage(string $file, string $name)
	{
		try {
			$file               = new File($this, $file, true);
			$this->files[$name] = $file;
			return $this;
		} catch (Error $e) {
			return false;
		}
	}
	
	
	public function getFiles()
	{
		return $this->files;
	}
	
	
}