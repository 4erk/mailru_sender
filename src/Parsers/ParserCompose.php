<?php
/**
 * Created by PhpStorm.
 * User: 4erk
 * Date: 01.07.2018
 * Time: 22:01
 */

namespace app\Parsers;


class ParserCompose extends BaseParser
{
	public function parse()
	{
		$data = [];
		$data['tarball'] = $this->between($this->raw_data, 'build: \'', '\',');
		$data['token'] = $this->between($this->raw_data, 'patron.updateToken("', '");');
		$this->result = $data;
	}
}