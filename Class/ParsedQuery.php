<?php
/**
 * Created by PhpStorm.
 * User: NeverBehave
 * Date: 2017/11/14
 * Time: 上午8:23
 */

class ParsedQuery
{
	public $port = 43;
	public $timeout = 10;
	public $result;

	public function __construct(ParsedRequest $request)
	{
		if (empty($request->getStatus())) {
			$fp = @fsockopen($request->whoisServer, $this->port, $errno, $errstr, $this->timeout) or die("Socket Error " . $errno . " - " . $errstr);
			fputs($fp, $request->domain . "\r\n");
			$out = "";
			while (!feof($fp)) {
				$out .= fgets($fp);
			}
			fclose($fp);

			$this->result = $out == NULL ? false : $out;
		}

	}

	public function getRawResult()
	{
		return $this->result == false ? "No Query result returned" : $this->result;
	}

	public function runTemplate()
	{

	}
}