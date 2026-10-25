<?php

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/connect.php';

$expected_token = env_value('TWEET_POST_TOKEN');
$provided_token = $_GET['token'] ?? $_POST['token'] ?? '';
if ($expected_token === '' || !hash_equals($expected_token, (string) $provided_token)) {
	http_response_code(403);
	exit('Forbidden');
}

$db = database_connect();
require_once __DIR__ . '/generator_plat.php';
require_once __DIR__ . '/generator_id.php';

// create dish and store it in DB
$id_array = generateId($db);
$tweet = generatePlat($db, $id_array) . '.'; // crée le plat en fonction de l'ID
$id = implode("-", $id_array);
$query = $db->prepare("INSERT INTO generated (id,ip) VALUES (?,?)"); /* archive en base l'ID créé à la date du moment */
$query->execute([$id, "localhost"]);

$twitter_api_key = env_value('TWITTER_API_KEY');
$twitter_api_secret = env_value('TWITTER_API_SECRET');
$twitter_access_token = env_value('TWITTER_ACCESS_TOKEN');
$twitter_access_token_secret = env_value('TWITTER_ACCESS_TOKEN_SECRET');

if (
	$twitter_api_key === ''
	|| $twitter_api_secret === ''
	|| $twitter_access_token === ''
	|| $twitter_access_token_secret === ''
) {
	http_response_code(500);
	exit('Twitter is not configured');
}

require_once(__DIR__ . '/twitter-codebird/codebird.php');

\Codebird\Codebird::setConsumerKey($twitter_api_key, $twitter_api_secret);
$cb = \Codebird\Codebird::getInstance();
$cb->setToken($twitter_access_token, $twitter_access_token_secret);

$params = array('status' => $tweet);
$reply = $cb->statuses_update($params);

if ($reply) {
	echo 'Le tweet suivant a ete poste :<br />"' . h($tweet) . '"';
} else { // CAREFUL : ERROR RETURN DOES NOT WORK
	echo "Erreur ! Le tweet n'a pas ete poste";
}

?>
