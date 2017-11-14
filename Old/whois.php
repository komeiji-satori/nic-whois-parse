<?php
error_reporting(0);
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
function parse_time($text, $domain) {
	if (strstr($text, 'T')) {
		if (strstr($text, '-')) {
			return $domain . " " . substr(str_replace('T', '', $text), 0, 10) . PHP_EOL;
		} else {
			return $domain . " " . $text . PHP_EOL;
		}
	}
	if (strstr($text, ".")) {
		return $domain . " " . implode('-', array_reverse(explode('-', str_replace('.', '-', $text)))) . PHP_EOL;
	}
	$_month = explode(',', 'January,February,March,April,May,June,July,August,September,October, November,December');
	foreach ($_month as $key => $value) {
		if (strstr($text, $value)) {
			$text = explode(' ', $text);
			$day = intval($text[0]);
			if (strlen($day) == 1) {
				$day = "0" . $day;
			}
			$month = array_search($text[1], $_month) + 1;
			if (strlen($month) == 1) {
				$month = "0" . $month;
			}
			$yeah = intval($text[2]);
			return $domain . " " . "$yeah-$month-$day" . PHP_EOL;
		}
	}
}
foreach ($domain_list as $key => $domain) {
	if ($domain) {
		if (preg_match("/^([-a-z0-9]{1,100})\.([a-z\.]{1,8})$/i", $domain)) {
			$result = LookupDomain($domain);
			$result = explode(PHP_EOL, $result);
			foreach ($result as $key => $value) {
				if (strstr($value, 'Domain Name Commencement Date')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'status...............: Registered')) {
					$reg_time = parse_time(trim(explode(':', $domain . " " . $result[$key + 1])[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'created..............:')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'Creation Date')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'Registration Time')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'Registration date')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'Registered on')) {
					print_r(parse_time(trim(explode('Registered on', $value)[1]), $domain));
					break;
				}
				if (strstr($value, 'Registered')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'created')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'Fecha de registro')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'Domain registered')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'Created')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'registered')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, '[登録年月日]')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
					break;
				}
				if (strstr($value, 'Activation')) {
					$reg_time = parse_time(trim(explode(':', $value)[1]), $domain);
					echo $reg_time;
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
