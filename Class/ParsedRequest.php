<?php
/**
 * Created by PhpStorm.
 * User: NeverBehave
 * Date: 2017/11/14
 * Time: 上午8:23
 */

class ParsedRequest
{
	public $domain;
	public $tld;
	public $whoisServer = NULL;
	public $whoisList;
	public $whoisListPath;
	public $status = "";


	public function __construct(String $domain, String $whoisListPath = __DIR__ . "/../Resources/whois-list.json")
	{
		require_once __DIR__ . '/../Utils/URL.php';
		$this->domain = $domain;
		$this->tld = getPossibleTLD(cleanURL($domain));

		// Get Whois Server List
		$this->whoisListPath = $whoisListPath;
		$this->whoisList = json_decode(file_get_contents($whoisListPath), true);

		// Match Domain
		if ($this->whoisList[$this->tld[0]] != NULL) {
			$this->whoisServer = $this->whoisList[$this->tld[0]];
			$this->tld = $this->tld[0];
		} else if ($this->whoisList[$this->tld[1]] != NULL) {
			$this->whoisServer = $this->whoisList[$this->tld[1]];
			$this->tld = $this->tld[1];
		} else {
			$this->status = "Whois Server No Found";
			$this->tld = NULL;
		}

		// Recover Correct Domain
		if ($this->whoisServer != NULL && $this->tld != NULL) {
			$this->domain = str_replace('.' . $this->tld, "", $this->domain);
			$rest = array_filter(explode(".", $this->domain)); // prevent empty array cause error
			$count = count($rest);
			$this->domain = $rest[$count - 1] . '.' . $this->tld;
		}

	}

	public function getWhoisServer()
	{
		return $this->whoisServer;
	}

	public function getWhoisList()
	{
		return $this->whoisList;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getJson()
	{
		return json_encode(get_object_vars($this));
	}

}