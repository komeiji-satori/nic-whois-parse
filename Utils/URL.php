<?php

function cleanURL(String $url)
{
	if (filter_var($url, FILTER_VALIDATE_URL)) {
		$url = explode("/", $url);
		$url = $url[2];

		return $url;
	}

	$url = explode("/", $url);
	$url = $url[0];

	return $url;
}

function getPossibleTLD(String $domain)
{
	if (strpos($domain, '.') === false) {
		return false;
	}
	$domain = explode(".", $domain);
	$domain = array_filter($domain);
	$length = count($domain);

	return [$domain[$length - 2] . '.' . $domain[$length - 1], $domain[$length - 1]];
}

