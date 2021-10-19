<?php
function database_connect()
{
	try {
		define('DB_PATH', $_SERVER['DOCUMENT_ROOT'] . '/hervemarmiton.sqlite3');
		$db = new PDO('sqlite:' . DB_PATH);
		// $db = new PDO('mysql:host=localhost;port=8889;dbname=hervemarmiton', 'root', 
		// 'root');
		$db->exec("SET CHARACTER SET utf8");
	} catch (Exception $e) {
		die('Error : ' . $e->getMessage());
	}
	return $db;
}
?>
