<?php

function radondin_id(){

	/* CHOIX DU GENRE POUR LA PARTIE DE PLAT 1*/
	$genre_nb_1=rand(1,10);
	if ($genre_nb_1<=4) {
		$genre1='ms'; /* masculin singulier */
	}
	elseif ($genre_nb_1 > 4 && $genre_nb_1 <= 8) {
		$genre1='fs'; /* féminin singulier */
	}
	elseif ($genre_nb_1==9) {
		$genre1='mp'; /* masculin pluriel */
	}
	elseif ($genre_nb_1==10) {
		$genre1='fp'; /* féminin pluriel */
	}
	
	$id_array = array($genre1);
	
	/* ARTICLE - PARTIE DE PLAT 1 */
	$req = mysql_query("SELECT SUM(poids) FROM articles_".$genre1);
	$poids_total = mysql_result($req, 0, 0);
	$aleatoire = rand (1,$poids_total);
	$req = mysql_query("SELECT poids,id FROM articles_".$genre1);
	while($rep = mysql_fetch_row($req)) {
		$aleatoire -= $rep[0];
		if ($aleatoire <= 0) {
			array_push ($id_array, $rep[1]);				
			break;
		}
	}

	/* ADJECTIF - PARTIE DE PLAT 1 */	
	$req = mysql_query("SELECT SUM(poids) FROM adjectifs_".$genre1);
	$poids_total = mysql_result($req, 0, 0);
	$aleatoire = rand (1,$poids_total);
	$req = mysql_query("SELECT poids,id FROM adjectifs_".$genre1);
	while($rep = mysql_fetch_row($req)) {
		$aleatoire -= $rep[0];
		if ($aleatoire <= 0) {
			array_push ($id_array, $rep[1]);	
			break;
		}
	}
	
	/* QUANTIFIANT - PARTIE DE PLAT 1 */	
	$req = mysql_query("SELECT SUM(poids) FROM quantifiants_".$genre1);
	$poids_total = mysql_result($req, 0, 0);
	$aleatoire = rand (1,$poids_total);
	$req = mysql_query("SELECT poids,id FROM quantifiants_".$genre1);
	while($rep = mysql_fetch_row($req)) {
		$aleatoire -= $rep[0];
		if ($aleatoire <= 0) {
			array_push ($id_array, $rep[1]);	
			break;
		}
	}
	
	/* CHOIX DU GENRE POUR LA PARTIE DE PLAT 2 */
	$genre_nb_2=rand(1,4);
	if ($genre_nb_2<=4) {
		$genre2='ms'; /* masculin singulier */
	}
	elseif ($genre_nb_2 > 4 && $genre_nb_2 <= 8) {
		$genre2='fs'; /* féminin singulier */
	}
	elseif ($genre_nb_2==9) {
		$genre2='mp'; /* masculin pluriel */
	}
	elseif ($genre_nb_1==10) {
		$genre2='fp'; /* féminin pluriel */
	}
	array_push ($id_array, $genre2);
	
	/* INGREDIENT - PARTIE DE PLAT 2 */	
	$req = mysql_query("SELECT SUM(poids) FROM ingredients_".$genre2);
	$poids_total = mysql_result($req, 0, 0);
	$aleatoire = rand (1,$poids_total);
	$req = mysql_query("SELECT poids,id FROM ingredients_".$genre2);
	while($rep = mysql_fetch_row($req)) {
		$aleatoire -= $rep[0];
		if ($aleatoire <= 0) {
			array_push ($id_array, $rep[1]);	
			break;
		}
	}

	/* QUALIFICATIF - PARTIE DE PLAT 2 */	
	$req = mysql_query("SELECT SUM(poids) FROM qualificatifs_".$genre2);
	$poids_total = mysql_result($req, 0, 0);
	$aleatoire = rand (1,$poids_total);
	$req = mysql_query("SELECT poids,id FROM qualificatifs_".$genre2);
	while($rep = mysql_fetch_row($req)) {
		$aleatoire -= $rep[0];
		if ($aleatoire <= 0) {
			array_push ($id_array, $rep[1]);	
			break;
		}
	}	
	
	/* FIN */	
	$req = mysql_query("SELECT SUM(poids) FROM fin");
	$poids_total = mysql_result($req, 0, 0);
	$aleatoire = rand (1,$poids_total);
	$req = mysql_query("SELECT poids,id FROM fin");
	while($rep = mysql_fetch_row($req)) {
		$aleatoire -= $rep[0];
		if ($aleatoire <= 0) {
			array_push ($id_array, $rep[1]);	
			break;
		}
	}

	return $id_array;
	
}
?>