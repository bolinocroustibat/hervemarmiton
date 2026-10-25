<?php

function chooseGenre()
{
	$genre_nb = rand(1, 10);
	if ($genre_nb <= 4) {
		$genre = 'ms'; /* masculin singulier */
	} elseif ($genre_nb > 4 && $genre_nb <= 8) {
		$genre = 'fs'; /* féminin singulier */
	} elseif ($genre_nb == 9) {
		$genre = 'mp'; /* masculin pluriel */
	} else {
		$genre = 'fp'; /* féminin pluriel */
	}
	return $genre;
}

function chooseFromTable($db, $table_name)
{
	if (!is_allowed_table($table_name)) {
		return null;
	}

	$query = $db->prepare('SELECT SUM(poids) FROM ' . $table_name);
	$query->execute();
	$poids_total = $query->fetch();
	$aleatoire = rand(1, $poids_total[0]);
	$query = $db->prepare('SELECT poids, id FROM ' . $table_name);
	$query->execute();
	$choice_array = $query->fetchAll();
	foreach ($choice_array as $row) {
		$aleatoire -= $row[0];
		if ($aleatoire <= 0) {
			return $row[1];
		}
	}
}

function generateId($db)
{
	/* CHOIX DU GENRE POUR LA PARTIE DE PLAT 1 */
	$genre1 = chooseGenre();
	$id_array = array($genre1);

	/* ARTICLE - PARTIE DE PLAT 1 */
	array_push($id_array, chooseFromTable($db, 'articles_' . $genre1));
	/* ADJECTIF - PARTIE DE PLAT 1 */
	array_push($id_array, chooseFromTable($db, 'adjectifs_' . $genre1));
	/* QUANTIFIANT - PARTIE DE PLAT 1 */
	array_push($id_array, chooseFromTable($db, 'quantifiants_' . $genre1));

	/* CHOIX DU GENRE POUR LA PARTIE DE PLAT 2 */
	$genre2 = chooseGenre();
	array_push($id_array, $genre2);
	/* INGREDIENT - PARTIE DE PLAT 2 */
	array_push($id_array, chooseFromTable($db, 'ingredients_' . $genre2));
	/* QUALIFICATIF - PARTIE DE PLAT 2 */
	array_push($id_array, chooseFromTable($db, 'qualificatifs_' . $genre2));
	/* FIN */
	array_push($id_array, chooseFromTable($db, 'fin'));

	return $id_array;
}

?>
