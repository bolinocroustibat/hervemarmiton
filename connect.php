<?php
function database_connect()
{
	try {
		$db = new PDO('sqlite:hervemarmiton.sqlite');
		// $db = new PDO('mysql:host=localhost;port=8889;dbname=hervemarmiton', 'root', 
		// 'root');
		$db->exec("SET CHARACTER SET utf8");
	} catch (Exception $e) {
		die('Error : ' . $e->getMessage());
	}
	return $db;
}
?>
