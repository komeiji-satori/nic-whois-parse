<?php

require_once "Class/autoload.php";

$result = '';

if (!empty($domain = $_GET["d"])) {
	$request = new ParsedRequest($domain);

	if (empty($message = $request->getStatus())) {

		$query = new ParsedQuery($request);

		if ($_GET['raw'] === 'on') {
			$result = $query->getRawResult();
		} else {
			$result = json_encode($query->getParsedResult());
		}

	} else {
		$result = $message;
	}

	echo $result;
} else {
	echo "";
}

