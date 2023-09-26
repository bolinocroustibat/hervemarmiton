<?php

/* MET LA PREMIERE LETTRE D'UNE VARCHAR EN MAJUSCULES, MEME UTF-8 */
function ucfirst_utf8($string){
    $firstChar = mb_substr($string, 0, 1, "utf-8");
    $then = mb_substr($string, 1, null, "utf-8");
    return mb_strtoupper($firstChar, "utf-8") . $then;
}

function generatePlat($db,$id_array){

	$plat='';

	/* ARTICLE - PARTIE DE PLAT 1 */
	$query = $db->prepare("SELECT id, article FROM articles_".$id_array[0]." WHERE id=".$id_array[1]);
	$query->execute();
	$rep = $query->fetch();
	if (!empty($rep[1])) {
		$article = $rep[1];
		$plat = $article;
	}

	/* ADJECTIF - PARTIE DE PLAT 1 */	
	$query = $db->prepare("SELECT id, adjectif FROM adjectifs_".$id_array[0]." WHERE id=".$id_array[2]);
	$query->execute();
	$rep = $query->fetch();
	if ($rep[1]) {
		$adjectif = $rep[1];
		if (empty($article)) {
			$plat = $adjectif;
		} else{
			if ((preg_match('#^[aeiou]#', $adjectif) && ($article == "le" || "la"))) { /* teste si il y a une voyelle en début de chaîne */
				$plat = 'l\''.$adjectif;
			} else {
				$plat = $plat.' '.$adjectif;
			}
		}
	}

	/* QUANTIFIANT - PARTIE DE PLAT 1 */
	$query = $db->prepare("SELECT id, quantifiant FROM quantifiants_".$id_array[0]." WHERE id=".$id_array[3]);
	$query->execute();
	$rep = $query->fetch();
	$quantifiant = $rep[1];
	if((empty($article))&&(empty($adjectif))) {
		$plat = $quantifiant;
		/* preg_replace('#^é#','#^É#',$quantifiant); /* si le quantifiant commence par "é" (pour "émincé") on remplace par É (ne marche pas) */
	} else{
		$plat = $plat.' '.$quantifiant;
	}

	/* INGREDIENT - PARTIE DE PLAT 2 */
	$query = $db->prepare("SELECT id, ingredient FROM ingredients_".$id_array[4]." WHERE id=".$id_array[5]);
	$query->execute();
	$rep = $query->fetch();
	if (preg_match('#^[aeioué]#', $rep[1])) { /* teste si il y a une voyelle en début de chaîne */
		$plat = $plat.' d\''.$rep[1];
	}
	else {
		$plat = $plat.' de '.$rep[1];
	}
	
	/* QUALIFICATIF - PARTIE DE PLAT 2 */
	$query = $db->prepare("SELECT id, qualificatif FROM qualificatifs_".$id_array[4]." WHERE id=".$id_array[6]);
	$query->execute();
	$rep = $query->fetch();
	$plat=$plat.' '.$rep[1]; 
	
	/* FIN */
	$query = $db->prepare("SELECT id, fin FROM fin WHERE id=".$id_array[7]);
	$query->execute();
	$rep = $query->fetch();
	if (!empty($rep[1])) {
		if (preg_match('#^,#', $rep[1])) { /* teste si il y a une virgule en début de chaîne */
			$plat = $plat.$rep[1];
		}
		else {
			$plat = $plat.' '.$rep[1];
		}
	}

	return ucfirst_utf8($plat);

}

?>
