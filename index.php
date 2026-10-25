<?php
$host = $_SERVER['HTTP_HOST'] ?? 'adriencarpentier.com';
if (!preg_match('/^[A-Za-z0-9.-]+(?::[0-9]{1,5})?$/', $host)) {
	$host = 'adriencarpentier.com';
}
$actual_base_url = "https://" . $host . "/hervemarmiton/";

require_once __DIR__ . '/connect.php';
$db = database_connect();
require_once __DIR__ . '/generator_plat.php';
require_once __DIR__ . '/generator_id.php';

if (empty($_GET['id'])) { // si rien n'est reçu dans l'URL
	$id_array = generateId($db);
	$id = implode("-", $id_array); // tableau d'ID séparé par des tirets
	$ip = $_SERVER["REMOTE_ADDR"] ?? '0.0.0.0';
	$query = $db->prepare("INSERT INTO generated (id,ip) VALUES (?,?)");
	$query->execute([$id, $ip]);

	header('Location: ' . $actual_base_url . $id . '.html'); // on envoie dans l'URL la variable ID
	exit;
}

$id = $_GET['id']; // pour les URLs de partage, on reprend l'ID de l'URL
$id_array = parse_dish_id($id);
if ($id_array === null) {
	http_response_code(404);
	exit('Not found');
}

$plat = generatePlat($db, $id_array);
if ($plat === null) {
	http_response_code(404);
	exit('Not found');
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href='//fonts.googleapis.com/css?family=Great+Vibes' rel='stylesheet' type='text/css'>
	<title>Hervé Marmiton : <?php echo h($plat); ?></title>
	<meta name="Description" content="Hervé Marmiton : une sélection à faire frémir les papilles pour petits et grands gourmands !" />
	<meta name="Keywords" content="herve marmiton, herve mariton, marmiton, generateur, generator, plats, hasard, delices, délices, menu, restaurant" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css" />
	<link rel="stylesheet" type="text/css" href="style/style.css" />
	<link rel="icon" type="image/ico" href="style/favicon.ico" />
	<meta property="og:locale" content="fr_FR">
	<meta property="og:title" content="<?php echo h($plat); ?>">
	<meta property="og:type" content="website">
	<meta property="fb:app_id" content="747210042019979" />
	<meta property="fb:admins" content="bolino">
	<meta property="og:url" content="<?php echo h($actual_base_url . $id); ?>.html">
	<meta property="og:image" content="//adriencarpentier.com/hervemarmiton/style/hervemarmiton.png">
	<meta property="og:site_name" content="HerveMarmiton.org : Plus d'un milliard de recettes de cuisine de droite !">
	<meta property="og:description" content="C'est mon plat de droite généré par HerveMarmiton.org. C'est quoi le tien ?">
	<!-- Start of StatCounter Code -->
	<script type="text/javascript">
		var sc_project = 8658855;
		var sc_security = "93a06730";
		var sc_invisible = 1;
	</script>
	<script type="text/javascript" src="//www.statcounter.com/counter/counter_xhtml.js"></script>
	<!-- End of StatCounter Code -->
	<script>
		(function(i, s, o, g, r, a, m) {
			i['GoogleAnalyticsObject'] = r;
			i[r] = i[r] || function() {
				(i[r].q = i[r].q || []).push(arguments)
			}, i[r].l = 1 * new Date();
			a = s.createElement(o),
				m = s.getElementsByTagName(o)[0];
			a.async = 1;
			a.src = g;
			m.parentNode.insertBefore(a, m)
		})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
		ga('create', 'UA-55996840-1', 'auto');
		ga('send', 'pageview');
	</script>
</head>

<body>

	<div id="menu">
		<img src="style/deco-vigne_1.png" alt="" />
		<div id="plat"><?php echo h($plat); ?></div>
		<img src="style/deco-vigne_2.png" alt="" />
	</div>

	<div id="share">
		Partager ce plat sur
		<a href="//twitter.com/?status=<?php echo h(urlencode($plat)); ?>,+sur+hervemarmiton.org+via+@HerveMarmiton">Twitter</a>
		| sur <a href="//www.facebook.com/sharer/sharer.php?u=https://adriencarpentier.com/hervemarmiton/<?php echo h($id); ?>.html">Facebook</a>
	</div>

	<div id="button-generate"><input type="button" OnClick="window.location='<?php echo h($actual_base_url); ?>'" value="Engendrer plus de délices..."></div>

	<a href="liste.html" id="lien-liste">Tous les plats créés</a>

</body>

</html>
