<?php
require_once '../vendor/autoload.php';
require_once 'functions.php';

use app\Mailer;
use app\Message;

$data = $_POST;

if (!array_key_exists('auth', $data))
	response('error', ['Auth data not found']);

$auth = $data['auth'];

if (!array_key_exists('login', $auth) || !array_key_exists('pass', $auth))
	response('error', ['Login or password not found']);

$mailer = new Mailer($auth['login'], $auth['pass']);

if ($mailer->hasErrors())
	response('error', $mailer->errors);
if (!array_key_exists('data', $data))
	response('error', ['Message not found']);

$dmsg = $data['data'];
$msg  = new Message();

if (!array_key_exists('email', $dmsg))
	response('error', ['Recepient not found']);

$msg->to($dmsg['email']);

if (!array_key_exists('subject', $dmsg))
	response('error', ['Subject not found']);

$msg->subject($dmsg['subject']);

if (!array_key_exists('message', $dmsg))
	response('error', ['Message not found']);

$msg->message($dmsg['message']);

if (array_key_exists('files', $dmsg)) {
	$files = $dmsg['files'];
	foreach ($files as $file) {
		if (!is_array($file))
			response('error', ['File data must be array']);
		
		if (!array_key_exists('file', $file))
			response('error', ['Path to file not found']);
		if (!file_exists($file['file']))
			response('error', ['File "' . $file['file'] . '" not found']);
		if (!array_key_exists('type', $file))
			$file['type'] = 'attach';
		if (!in_array($file['type'], ['attach', 'inline']))
			response('error', ['File type must me attach or inline']);
		if ($file['type'] == 'inline' && !array_key_exists('name', $file))
			response('error', ['Inline file must have name']);
		switch ($file['type']) {
			case 'inline':
				$msg->addImage($file['file'], $file['name']);
				break;
			case 'attach':
				$msg->addFile($file['file']);
		}
	}
}

$mailer->sendMessage($msg);

if ($mailer->hasErrors())
	response('error', $mailer->errors);

response('ok');




