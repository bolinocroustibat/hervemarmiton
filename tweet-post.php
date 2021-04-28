<?php

header('Content-Type: text/html; charset=utf-8');
include("connect.php");
$db = database_connect();
include("radondin_id_generator.php");
include("radondin_plat_generator.php");
$id_array=generateId($db); // lance la fonction pour générer un ID de plat au hasard, qui sera renvoyé sous forme de tableau
$tweet=generatePlat($db,$id_array).'.';  // crée le plat en fonction de l'ID

// require codebird
require_once(__DIR__.'/twitter-codebird/codebird.php');

\Codebird\Codebird::setConsumerKey("REDACTED", "REDACTED");
$cb = \Codebird\Codebird::getInstance();
$cb->setToken("REDACTED", "REDACTED");
 
$params = array('status' => $tweet);
$reply = $cb->statuses_update($params);

if ($reply){
	echo 'Le tweet suivant a ete poste :<br />"'.$tweet.'"';
} else { // CAREFUL : ERROR RETURN DOES NOT WORK
	echo "Erreur ! Le tweet n'a pas ete poste";
}

?>