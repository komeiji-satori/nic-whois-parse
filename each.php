<?php
//error_reporting(0);
include './idn.php';
$whoisservers = json_decode(file_get_contents('./whois-list.json'), true);

function LookupDomain($domain) {
	global $whoisservers;
	$whoisserver = "";

	$dotpos = strpos($domain, ".");
	$domtld = substr($domain, $dotpos + 1);

	$whoisserver = $whoisservers[$domtld];

	if (!$whoisserver) {
		return "Whois Nic Server Not Found";
	}
	$result = QueryWhoisServer($whoisserver, $domain);
	if (!$result) {
		return "Whois Nic Server Empty Response";
	}

	preg_match("/Whois Server: (.*)/", $result, $matches);
	$secondary = @$matches[1];
	if ($secondary) {
		$result = QueryWhoisServer($secondary, $domain);
	}
	return $result;
}

function QueryWhoisServer($whoisserver, $domain) {
	$port = 43;
	$timeout = 10;
	$fp = @fsockopen($whoisserver, $port, $errno, $errstr, $timeout) or die("Socket Error " . $errno . " - " . $errstr);
	fputs($fp, $domain . "\r\n");
	$out = "";
	while (!feof($fp)) {
		$out .= fgets($fp);
	}
	fclose($fp);
	return $out;
}
$domain_list = explode(PHP_EOL, file_get_contents('domain.txt'));
foreach ($domain_list as $key => $domain) {
	if ($domain) {
		if (preg_match("/^([-a-z0-9]{1,100})\.([a-z\.]{1,8})$/i", $domain)) {
			$result = LookupDomain($domain);
			$result = explode(PHP_EOL, $result);
			foreach ($result as $key => $value) {
				if (strstr($value, 'Expiry')) {
					print_r($domain . " " . $value . PHP_EOL);
					break;
				}
			}
		} else {
			$domain = IDN::decodeIDN($domain);
			$result = LookupDomain($domain);
			$result = explode(PHP_EOL, $result);
			print_r($result);
		}
	}
}
