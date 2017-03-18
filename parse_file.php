<?php

require_once("ParseCode.php");

if($argc == 2) {
	$parsed_file = ParseCode::parseToString($argv[1]);

	// we can add furthor commands to indicate what to do with this 
	// or we can redirect standard output
	echo $parsed_file;
} else {
	echo "Example usage: php parse_file.php filename.csv\r\n\r\n";
	exit();
}