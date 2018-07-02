<?php
/**
 * Created by PhpStorm.
 * User: 4erk
 * Date: 30.06.2018
 * Time: 9:15
 */

namespace app\Parsers;


class ParserToken extends BaseParser
{
	public function parse() {
		$this->result = $this->between($this->raw_data,'"token" value="','"');
	}
}