<?php
/**
 * Created by PhpStorm.
 * User: 4erk
 * Date: 03.07.2018
 * Time: 5:38
 */

namespace app;

use Error;
use function array_key_exists;
use function array_merge;
use function file_exists;
use function pathinfo;
use const PATHINFO_BASENAME;

class File
{
	/* @var $message Message */
	private $message;
	private $file;
	private $isEmbed = false;
	private $attributes = [
		'swf' => 1,
		'fuid' => 0,
		'url_charset' => 'utf-8',
		'FileName' => '',
		'message' => '',
		'groupcode' => '',
		'upmode' => '',
		'upload' => 1,
		'sourcehost' => 'e.mail.ru',
		'hidelinkcode' => 1,
	];
	
	private $attach_data = [];
	
	
	
	public function __construct(Message $message, string $file, $is_embed = false) {
		$this->message = $message;
		if (!file_exists($file)) throw new Error('File "'.$file.'" not found');
		$this->file = $file;
		$this->isEmbed = $is_embed;
		$this->loadParams();
	}
	
	private function loadParams() {
		$data = [
			'FileName'=> pathinfo($this->file, PATHINFO_BASENAME),
			'message' => $this->queryParams()['message_id'],
		];
		$this->attributes = array_merge($this->attributes, $data);
		return $this;
	}
	
	public function queryParams() {
		return $this->message->queryParams();
	}
	
	public function getData() {
		$this->loadParams();
		return $this->attributes;
	}
	
	public function getFile() {
		return $this->file;
	}
	
	public function getAttachData() {
		return $this->attach_data;
	}
	
	public function setAttachData($data) {
		if (array_key_exists('status', $data) && $data['status'] == 200) {
			$file = [
				'id' => $data['body']['attach']['id'],
				'type' => $this->isEmbed ? 'inline': 'attach',
			];
			if ($this->isEmbed) $file['content_id'] = genStr(4,1,1,1).'@'.genStr('8',1,1,1).'.'.genStr('8',1,1,1);
			$this->attach_data = $file;
		}
		return $this;
	}
}