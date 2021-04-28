<?php

function chooseGenre(){
	$genre_nb=rand(1,10);
	if ($genre_nb<=4) {
		$genre='ms'; /* masculin singulier */
	}
	elseif ($genre_nb > 4 && $genre_nb <= 8) {
		$genre='fs'; /* féminin singulier */
	}
	elseif ($genre_nb==9) {
		$genre='mp'; /* masculin pluriel */
	}
	elseif ($genre_nb==10) {
		$genre='fp'; /* féminin pluriel */
	}
	return $genre;
}

function chooseFromTable($db, $table_name){
	$query = $db->prepare("SELECT SUM(poids) FROM ".$table_name);
	$query->execute();
	$poids_total = $query->fetch();
	$aleatoire = rand(1, $poids_total[0]);
	$query = $db->prepare("SELECT poids, id FROM ".$table_name);
	$query->execute();
	$choice_array= $query->fetchAll();
	foreach ($choice_array as $row) {
		$aleatoire -= $row[0];
		if ($aleatoire <= 0) {
			return $row[1];
			break;
		}
	}
}

function generateId($db){

	/* CHOIX DU GENRE POUR LA PARTIE DE PLAT 1*/
	$genre1 = chooseGenre();
	$id_array = array($genre1);
	
	/* ARTICLE - PARTIE DE PLAT 1 */
	$table_name = 'articles_'.$genre1;
	array_push ($id_array, chooseFromTable($db,$table_name));

	/* ADJECTIF - PARTIE DE PLAT 1 */
	$table_name = "adjectifs_".$genre1;
	array_push ($id_array, chooseFromTable($db,$table_name));
	
	/* QUANTIFIANT - PARTIE DE PLAT 1 */
	$table_name= "quantifiants_".$genre1;
	array_push ($id_array, chooseFromTable($db,$table_name));
	
	/* CHOIX DU GENRE POUR LA PARTIE DE PLAT 2 */
	$genre2 = chooseGenre();
	array_push ($id_array, $genre2);
	
	/* INGREDIENT - PARTIE DE PLAT 2 */
	$table_name = "ingredients_".$genre2;
	array_push ($id_array, chooseFromTable($db,$table_name));

	/* QUALIFICATIF - PARTIE DE PLAT 2 */
	$table_name = "qualificatifs_".$genre2;
	array_push ($id_array, chooseFromTable($db,$table_name));	
	
	/* FIN */	
	$table_name = "fin";
	array_push ($id_array, chooseFromTable($db,$table_name));

	return $id_array;

}

?>
