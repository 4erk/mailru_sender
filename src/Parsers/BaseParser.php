<?php
/**
 * Created by PhpStorm.
 * User: 4erk
 * Date: 31.05.2018
 * Time: 10:02
 */

namespace app\Parsers;

abstract class BaseParser implements ParserInterface
{
	public $raw_data;
	public $result;
	
	public function __construct($raw_data)
	{
		$this->raw_data = $raw_data;
		$this->parse();
	}
	
	public function parse()
	{
		$this->result = $this->raw_data;
	}
	
	public function __toString()
	{
		return (string)$this->result;
	}
	
	public function between($str, $before, $after)
	{
		$res = explode($before, $str);
		if (sizeof($res) < 2) return null;
		$res = explode($after, $res[1]);
		if (sizeof($res) < 2) return null;
		return $res[0];
	}
	
	public function betweens($str, $before, $after)
	{
		$results = [];
		$res     = explode($before, $str);
		if (sizeof($res) < 2) return [];
		for ($i = 1; $i < sizeof($res); $i++) {
			$result = explode($after, $res[$i]);
			if (sizeof($result) < 2) continue;
			$results[] = $result[0];
		}
		return $results;
	}
	
	public function getResult()
	{
		return $this->result;
	}
	
	
}