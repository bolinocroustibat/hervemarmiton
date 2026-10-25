<?php
require_once __DIR__ . '/connect.php';
$db = database_connect();
require_once __DIR__ . '/generator_plat.php';

// Définit le décalage horaire par défaut de toutes les fonctions date/heure
date_default_timezone_set("Europe/Paris");

// Convertir une date US vers une date en français affichant le jour de la semaine
function dateLongue($date, $heure = 'yes')
{
	$timestamp = strtotime($date);
	if ($timestamp === false) {
		return '';
	}

	$weekdays = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
	$months = [
		1 => 'janvier',
		2 => 'février',
		3 => 'mars',
		4 => 'avril',
		5 => 'mai',
		6 => 'juin',
		7 => 'juillet',
		8 => 'août',
		9 => 'septembre',
		10 => 'octobre',
		11 => 'novembre',
		12 => 'décembre',
	];

	$formatted = $weekdays[(int) date('w', $timestamp)]
		. ' ' . date('d', $timestamp)
		. ' ' . $months[(int) date('n', $timestamp)]
		. ' ' . date('Y', $timestamp);

	if ($heure === 'yes') {
		$formatted .= ' à ' . date('H', $timestamp) . 'h' . date('i', $timestamp);
	}

	return $formatted;
}

$maps = load_vocabulary_maps($db);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8" />
	<link href='//fonts.googleapis.com/css?family=Great+Vibes' rel='stylesheet' type='text/css'>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="Description" content="Une sélection à faire frémir les papilles pour Hervé Mariton et pour petits et grands gourmands !" />
	<meta name="Keywords" content="herve marmiton, herve mariton, marmiton, generateur, generator, plats, hasard, delices, délices, menu, restaurant" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css" />	
	<link rel="stylesheet" type="text/css" href="style/style.css" />
	<link rel="icon" type="image/ico" href="style/favicon.ico" />
	<link href='//fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'>
	<meta property="og:locale" content="fr_FR">
	<meta property="og:title" content="HerveMarmiton.org : plus d'un milliard de recettes de cuisine de droite - Tous les plats créés">
	<meta property="og:type" content="website">
	<meta property="fb:app_id" content="747210042019979" />	
	<meta property="fb:admins" content="bolino">
	<meta property="og:url" content="//adriencarpentier.com/hervemarmiton">
	<meta property="og:image" content="//adriencarpentier.com/hervemarmiton/style/hervemarmiton.png">
	<meta property="og:site_name" content="HerveMarmiton.org : Plus d'un milliard de recettes de cuisine de droite !">
	<meta property="og:description" content="C'est mon plat de droite généré par HerveMarmiton.org. C'est quoi le tien ?">
	<title>Hervé Marmiton : plus d'un milliard de recettes de cuisine de droite - Tous les plats créés</title>
	<!-- Start of StatCounter Code -->
	<script type="text/javascript">
	<!-- 
		var sc_project=8658855; 
		var sc_security="93a06730"; 
		var sc_invisible=1;
	//-->
	</script>
	<script type="text/javascript" src="//www.statcounter.com/counter/counter_xhtml.js"></script>
	<noscript><div class="statcounter"><a title="web analytics" href="//statcounter.com/"><img class="statcounter" src="//c.statcounter.com/8658855/0/93a06730/1/" alt="web analytics" /></a></div></noscript>	
	<!-- End of StatCounter Code -->
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-55996840-1', 'auto');
		ga('send', 'pageview');
	</script>
</head>

<body>

	<div id="menu-liste">
		<table>
		
		<tr id="tete-liste">
			<td>Hervé Marmiton a déjà suggéré :</td>
			<td>en plat du jour :</td>
		</tr>

		<?php
		$query = $db->prepare("SELECT id,date FROM generated ORDER BY date DESC");
		$query->execute();
		foreach ($query as $row) {
			$id_array = parse_dish_id($row[0]);
			if ($id_array === null) {
				continue;
			}
			$plat = generatePlatFromMaps($maps, $id_array);
			if ($plat === null) {
				continue;
			}
			echo '<tr>';
			echo '<td><a href="' . h($row[0]) . '.html">' . h($plat) . '</a></td>';
			echo '<td>' . h(dateLongue($row[1])) . '</td>';
			echo '</tr>';
		}
		?>

		</table>
	</div>

</body>

</html>
