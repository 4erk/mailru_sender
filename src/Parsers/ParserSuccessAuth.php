<?php
/**
 * Created by PhpStorm.
 * User: 4erk
 * Date: 30.06.2018
 * Time: 21:11
 */

namespace app\Parsers;


class ParserSuccessAuth extends BaseParser
{
	public function parse()
	{
		$this->result = $this->between($this->raw_data, 'ActiveEmail: \'','\'');
	}
}