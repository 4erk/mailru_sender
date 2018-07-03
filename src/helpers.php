<?php

namespace app;




use Exception;
use function bin2hex;

function genStr(int $length, $number = true, $lower = false, $upper = false)
{
	$str    = '';
	$gen    = [
		'number' => '0123456789',
		'lower'  => 'abcdefghijklmnopqrstuvwxyz',
		'upper'  => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
	];
	$genstr = '';
	if ($number) $genstr .= $gen['number'];
	if ($lower) $genstr .= $gen['lower'];
	if ($upper) $genstr .= $gen['upper'];
	foreach (range(0, $length) as $i) {
		$str.= $genstr{rand(0,strlen($genstr)-1)};
	}
	return $str;
}

function mtime($micro = false) {
	if ($micro) return floor(microtime(1)*1000);
	return time();
}

function genHex(int $length, $upper = false) {
	try {
		$str = random_bytes($length);
		$str = bin2hex($str);
		if ($upper) $str = strtoupper($str);
		return $str;
	} catch (Exception $exception) {
		return '';
	}
}