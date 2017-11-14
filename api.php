<?php

require_once "Class/autoload.php";

if (!empty($domain = $_GET["d"])) {
	$request = new ParsedRequest($domain);
	//echo $request->getJson();
	if (!empty($message = $request->getStatus())) {
		echo $message;
		die(1);
	}
	$query = new ParsedQuery($request);
	echo $query->getRawResult();
}