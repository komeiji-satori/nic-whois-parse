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
	private $rawResult;
	private $result = NULL;

	public function __construct(ParsedRequest $request)
	{
		if (empty($request->getStatus())) {
			$fp = @fsockopen($request->whoisServer, $this->port, $errno, $errstr, $this->timeout) or die("Socket Error " . $errno . " - " . $errstr);
			fputs($fp, $request->domain . "\r\n");
			$out = "";
			while (!feof($fp)) {
				$out .= trim(fgets($fp));
				$out .= "<br>";
			}
			fclose($fp);

			$this->rawResult = $out == NULL ? false : $out;
		}

		if ($this->rawResult != NULL) {
			$this->parsedResult();
		}

	}

	public function getRawResult()
	{
		return $this->rawResult == false ? "No Query result returned, try again later" : $this->rawResult;
	}

	public function getParsedResult()
	{
		return $this->result == NULL ? "No Query result returned" : $this->result;
	}

	public function parsedResult()
	{
		$this->result = [];
		preg_match("/(>>> Last update of whois database: )(.*)( <<<)/i", $this->rawResult, $matches, PREG_OFFSET_CAPTURE);
		$this->result['last_update'] = trim($matches[2][0]);
		$split = array_filter(explode("<br>", $this->rawResult));

		// Domain No Found
		if (strpos($split[0], "NOT FOUND") !== false || strpos($split[0], "NO MATCH") !== false) {
			$this->result['status'] = 'domain no found';

			return;
		}

		// Start Dash
		foreach ($split as $line) {

			if ($line === "URL of the ICANN Whois Inaccuracy Complaint Form: https://www.icann.org/wicf/") {
				break;
			}

			$array = explode(":", $line);
			$key = trim($array[0]);
			$array[0] = ""; // Set empty to get rest Content
			$value = trim(implode(":", array_filter($array)));

			if (empty($this->result[$key])) {
				$this->result[$key] = $value;
			} else {
				if (is_string($this->result[$key])) {
					$this->result[$key] = [$this->result[$key]];
				}
				$this->result[$key][] = $value;
			}
		}
	}
}
