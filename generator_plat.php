<?php

/**
 * Uppercase the first letter of a string, including UTF-8 characters.
 *
 * @param string $string
 * @return string
 */
function ucfirst_utf8($string)
{
	$firstChar = mb_substr($string, 0, 1, 'utf-8');
	$then = mb_substr($string, 1, null, 'utf-8');
	return mb_strtoupper($firstChar, 'utf-8') . $then;
}

function allowed_genres()
{
	return ['ms', 'fs', 'mp', 'fp'];
}

function is_allowed_genre($genre)
{
	return in_array($genre, allowed_genres(), true);
}

function allowed_word_columns()
{
	return ['article', 'adjectif', 'quantifiant', 'ingredient', 'qualificatif', 'fin'];
}

function is_allowed_table($table_name)
{
	static $allowed = null;
	if ($allowed === null) {
		$allowed = ['fin' => true];
		foreach (allowed_genres() as $genre) {
			foreach (['articles', 'adjectifs', 'quantifiants', 'ingredients', 'qualificatifs'] as $prefix) {
				$allowed[$prefix . '_' . $genre] = true;
			}
		}
	}
	return isset($allowed[$table_name]);
}

function parse_dish_id($id)
{
	if (!is_string($id)) {
		return null;
	}
	if (!preg_match('/^(ms|fs|mp|fp)-(\d+)-(\d+)-(\d+)-(ms|fs|mp|fp)-(\d+)-(\d+)-(\d+)$/', $id, $match)) {
		return null;
	}
	return [$match[1], $match[2], $match[3], $match[4], $match[5], $match[6], $match[7], $match[8]];
}

function is_valid_id_array($id_array)
{
	return is_array($id_array)
		&& count($id_array) === 8
		&& is_allowed_genre($id_array[0])
		&& is_allowed_genre($id_array[4])
		&& ctype_digit((string) $id_array[1])
		&& ctype_digit((string) $id_array[2])
		&& ctype_digit((string) $id_array[3])
		&& ctype_digit((string) $id_array[5])
		&& ctype_digit((string) $id_array[6])
		&& ctype_digit((string) $id_array[7]);
}

function lookup_word($db, $table_name, $column, $id)
{
	if (!is_allowed_table($table_name) || !in_array($column, allowed_word_columns(), true)) {
		return null;
	}

	$query = $db->prepare("SELECT {$column} FROM {$table_name} WHERE id = ?");
	$query->execute([$id]);
	$value = $query->fetchColumn();
	if ($value === false) {
		return null;
	}
	return $value;
}

function map_word($maps, $table_name, $id)
{
	$key = (string) $id;
	if (!isset($maps[$table_name][$key])) {
		return null;
	}
	return $maps[$table_name][$key];
}

function fetch_id_text_map($db, $table_name, $column)
{
	if (!is_allowed_table($table_name) || !in_array($column, allowed_word_columns(), true)) {
		return [];
	}

	$query = $db->query("SELECT id, {$column} FROM {$table_name}");
	$map = [];
	foreach ($query as $row) {
		$map[(string) $row[0]] = $row[1];
	}
	return $map;
}

function load_vocabulary_maps($db)
{
	$maps = [];
	foreach (allowed_genres() as $genre) {
		$tables = [
			'articles_' . $genre => 'article',
			'adjectifs_' . $genre => 'adjectif',
			'quantifiants_' . $genre => 'quantifiant',
			'ingredients_' . $genre => 'ingredient',
			'qualificatifs_' . $genre => 'qualificatif',
		];
		foreach ($tables as $table_name => $column) {
			$maps[$table_name] = fetch_id_text_map($db, $table_name, $column);
		}
	}
	$maps['fin'] = fetch_id_text_map($db, 'fin', 'fin');
	return $maps;
}

function assemble_plat($article, $adjectif, $quantifiant, $ingredient, $qualificatif, $fin)
{
	$plat = '';

	/* ARTICLE - PARTIE DE PLAT 1 */
	if (!empty($article)) {
		$plat = $article;
	}

	/* ADJECTIF - PARTIE DE PLAT 1 */
	if ($adjectif) {
		if (empty($article)) {
			$plat = $adjectif;
		} else {
			// Keep ($article == "le" || "la"): "la" is always true, so any
			// vowel-initial adjective after an article becomes l'...
			if (preg_match('#^[aeiou]#', $adjectif) && ($article == "le" || "la")) { /* teste si il y a une voyelle en début de chaîne */
				$plat = 'l\'' . $adjectif;
			} else {
				$plat = $plat . ' ' . $adjectif;
			}
		}
	}

	/* QUANTIFIANT - PARTIE DE PLAT 1 */
	if (empty($article) && empty($adjectif)) {
		$plat = $quantifiant;
	} else {
		$plat = $plat . ' ' . $quantifiant;
	}

	/* INGREDIENT - PARTIE DE PLAT 2 */
	if (preg_match('#^[aeioué]#', (string) $ingredient)) { /* teste si il y a une voyelle en début de chaîne */
		$plat = $plat . ' d\'' . $ingredient;
	} else {
		$plat = $plat . ' de ' . $ingredient;
	}

	/* QUALIFICATIF - PARTIE DE PLAT 2 */
	$plat = $plat . ' ' . $qualificatif;

	/* FIN */
	if (!empty($fin)) {
		if (preg_match('#^,#', $fin)) { /* teste si il y a une virgule en début de chaîne */
			$plat = $plat . $fin;
		} else {
			$plat = $plat . ' ' . $fin;
		}
	}

	return ucfirst_utf8($plat);
}

function plat_parts_from_db($db, $id_array)
{
	if (!is_valid_id_array($id_array)) {
		return null;
	}

	$genre1 = $id_array[0];
	$genre2 = $id_array[4];

	return [
		'article' => lookup_word($db, 'articles_' . $genre1, 'article', $id_array[1]),
		'adjectif' => lookup_word($db, 'adjectifs_' . $genre1, 'adjectif', $id_array[2]),
		'quantifiant' => lookup_word($db, 'quantifiants_' . $genre1, 'quantifiant', $id_array[3]),
		'ingredient' => lookup_word($db, 'ingredients_' . $genre2, 'ingredient', $id_array[5]),
		'qualificatif' => lookup_word($db, 'qualificatifs_' . $genre2, 'qualificatif', $id_array[6]),
		'fin' => lookup_word($db, 'fin', 'fin', $id_array[7]),
	];
}

function generatePlat($db, $id_array)
{
	$parts = plat_parts_from_db($db, $id_array);
	if ($parts === null) {
		return null;
	}

	return assemble_plat(
		$parts['article'],
		$parts['adjectif'],
		$parts['quantifiant'],
		$parts['ingredient'],
		$parts['qualificatif'],
		$parts['fin']
	);
}

function generatePlatFromMaps($maps, $id_array)
{
	if (!is_valid_id_array($id_array)) {
		return null;
	}

	$genre1 = $id_array[0];
	$genre2 = $id_array[4];

	return assemble_plat(
		map_word($maps, 'articles_' . $genre1, $id_array[1]),
		map_word($maps, 'adjectifs_' . $genre1, $id_array[2]),
		map_word($maps, 'quantifiants_' . $genre1, $id_array[3]),
		map_word($maps, 'ingredients_' . $genre2, $id_array[5]),
		map_word($maps, 'qualificatifs_' . $genre2, $id_array[6]),
		map_word($maps, 'fin', $id_array[7])
	);
}

?>
