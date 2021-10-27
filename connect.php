<?php
function database_connect()
{
	try {
		$db = new PDO('sqlite:REDACTED'); // prod - absolute
		// $db = new PDO('sqlite:hervemarmiton.sqlite3'); // local
		// $db = new PDO('mysql:host=localhost;port=8889;dbname=hervemarmiton', 'root', 'root'); // mysql
		// define('DB_PATH', $_SERVER['DOCUMENT_ROOT'] . '/hervemarmiton.sqlite3'); // relative
		// $db = new PDO('sqlite:' . DB_PATH);
		$db->exec("SET CHARACTER SET utf8");
	} catch (Exception $e) {
		die('Error: ' . $e->getMessage());
	}
	return $db;
}
?>
