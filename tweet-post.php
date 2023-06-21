<?php

header('Content-Type: text/html; charset=utf-8');

include("connect.php");

$db = database_connect();

include("generator_id.php");
include("generator_plat.php");

// create dish and store in in DB
$id_array=generateId($db); // lance la fonction pour générer un ID de plat au hasard, qui sera renvoyé sous forme de tableau
$tweet=generatePlat($db,$id_array).'.';  // crée le plat en fonction de l'ID
$id = implode("-", $id_array); /* on transforme le tableau d'ID en variable dont les éléments dont séparés par des tirets */
$query = $db->prepare("INSERT INTO generated (id,ip) VALUES (?,?)"); /* archive en base l'ID créé à la date du moment */
$query->execute([$id,"localhost"]);

// set twitter secrets
$twitter_api_key = "REDACTED";
$twitter_api_secret =  "REDACTED";
$twitter_access_token = "REDACTED";
$twitter_access_token_secret = "REDACTED";

// require codebird
require_once(__DIR__.'/twitter-codebird/codebird.php');

\Codebird\Codebird::setConsumerKey($twitter_api_key, $twitter_api_secret);
$cb = \Codebird\Codebird::getInstance();
$cb->setToken($twitter_access_token, $twitter_access_token_secret);

$params = array('status' => $tweet);
$reply = $cb->statuses_update($params);

if ($reply){
	echo 'Le tweet suivant a ete poste :<br />"'.$tweet.'"';
} else { // CAREFUL : ERROR RETURN DOES NOT WORK
	echo "Erreur ! Le tweet n'a pas ete poste";
}

?>