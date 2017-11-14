<?php
//error_reporting(0);
include './idn.php';


function LookupDomain($domain) {
	global $whoisservers;
	$whoisserver = "";

	$dotpos = strpos($domain, ".");
	$domtld = substr($domain, $dotpos + 1);

	$whoisserver = $whoisservers[$domtld];

	if (!$whoisserver) {
		return "Error: 没有找到适合您（ <b>$domain</b> ）这个域名的whois服务器! 也可能是该域名的服务商并没有公开whois查询的服务器！";
	}
	$result = QueryWhoisServer($whoisserver, $domain);
	if (!$result) {
		return "Error: 服务器没有返回任何结果 $domain !";
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
print_r(explode(PHP_EOL, LookupDomain('888.as')));