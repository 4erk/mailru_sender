<?php
/**
 * Created by PhpStorm.
 * User: 4erk
 * Date: 30.06.2018
 * Time: 22:19
 */

namespace app;

use function explode;
use function in_array;
use function json_encode;

class Message
{
	private $attributes = [
		'__urlp'          => '/messages/send/?logid=$logid',
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
		'tarball'   => '',
		'tab-time' => 0,
		'id'       => '',
		'token'    => '',
		'email'    => '',
		'to'       => '',
		'subject'  => '',
		'body'     => '',
	];
	private $data = [];
	
	public function __construct(array $params = [])
	{
	
	}
	
	public function setParams(array $params)
	{
		foreach ($this->params as $k => $v) {
			if (array_key_exists($k, $params)) $this->params[$k] = $params[$k];
		}
		return $this;
	}
	
	public function getData()
	{
		$this->applyParams();
		$this->prepareData();
		return $this->data;
	}
	
	private function applyParams()
	{
		$this->attributes['__urlp']               = str_replace('$logid', $this->params['logid'], $this->attributes['__urlp']);
		$this->attributes['tarball']               = $this->params['tarball'];
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
		$this->data = [];
		foreach ($this->attributes as $k => $v) {
			$this->data[$k] = in_array($k, $this->toJson) ? json_encode($v) : $v;
		}
	}
	
	public function to(string $email) {
		$name = explode('@', $email);
		$to = $name[0].' <'.$email.'>';
		$this->params['to'] = $to;
		return $this;
	}
	
	public function message(string $msg) {
		$this->params['body'] = $msg;
		return $this;
	}
	
	public function subject(string $subject) {
		$this->params['subject'] = $subject;
		return $this;
	}
}