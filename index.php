<?php

include("connex.php");
database_connect();
mysql_query("SET NAMES UTF8");
include("radondin_id_generator.php");
include("radondin_plat_generator.php");


if (empty($_GET['id'])) { /* si rien n'est reçu dans l'URL (si la ligne "id" du tableau GET est vide)*/
	$id_array=radondin_id(); /* alors on lance la fonction pour générer un ID de plat au hasard, qui sera renvoyé sous forme de tableau */	
	$id = implode ("-",$id_array); /* on transforme le tableau d'ID en variable dont les éléments dont séparés par des tirets */
	$ip = $_SERVER["REMOTE_ADDR"];
	mysql_query("INSERT INTO plats_complets(id,date,ip) VALUES('".$id."',now(),'".$ip."')"); /* archive en base l'ID créé à la date du moment */
	header('Location: http://www.hervemarmiton.org/'.$id.'.html');/* on envoie dans l'URL la variable ID */
}
else {
	$id = $_GET['id']; /* pour les URLs de partages, on met l'ID de l'URL dans une variable */
	$id_array = explode("-",mysql_real_escape_string($_GET['id'])); /* on recoit l'ID par l'URL dans la première ligne d'un tableau GET, on la sépare selon les tirets et la remet dans un tableau */
}

$plat=radondin_plat($id_array);  // Crée le plat en fonction de l'ID !

?>

<!DOCTYPE html>
<html lang="fr">

<head>

	<meta charset="UTF-8" />		
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php
		/*
		header( "Content-Type: text/html; charset=utf8" );
		setlocale (LC_ALL, 'fr_FR.utf8');
		date_default_timezone_set('Europe/Paris');
		mb_internal_encoding("UTF-8");
		*/
	?>
			
	<link href='http://fonts.googleapis.com/css?family=Great+Vibes' rel='stylesheet' type='text/css'>
	<!-- 
	<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT:regular,italic&amp;v1' rel='stylesheet' type='text/css' />
	-->

	<title>Hervé Marmiton : <?php echo $plat; ?></title>
	
	<meta name="Description" content="Hervé Marmiton : une sélection à faire frémir les papilles pour petits et grands gourmands !" />
	<meta name="Keywords" content="masterchouf, herve mariton, herve marmiton, marmiton, plats, hasard, delices, délices, barnabé, menu, restaurant, générateur, generator" />
	<meta name="Generator" content="Notepad++" />
	<link rel="stylesheet" type="text/css" href="style/normalize.css" />	
	<link rel="stylesheet" type="text/css" href="style/style.css" />
	<link rel="icon" type="image/ico" href="style/favicon.ico" />
	<meta property="og:locale" content="fr_FR">
	<meta property="og:title" content="<?php echo $plat; ?>">
	<meta property="og:type" content="website">
    <meta property="fb:app_id" content="747210042019979" />
	<meta property="fb:admins" content="bolino">
	<meta property="og:url" content="http://www.hervemarmiton.org/<?php echo $id; ?>.html">
	<meta property="og:image" content="http://www.hervemarmiton.org/style/hervemarmiton.png">
	<meta property="og:site_name" content="HerveMarmiton.org : Plus d'un milliard de recettes de cuisine de droite !">
	<meta property="og:description" content="C'est mon plat de droite généré par HerveMarmiton.org. C'est quoi le tien ?">
	
	<!-- Start of StatCounter Code -->
	<script type="text/javascript">
	<!-- 
		var sc_project=8658855; 
		var sc_security="93a06730"; 
		var sc_invisible=1;
	//-->
	</script>
	<script type="text/javascript" src="http://www.statcounter.com/counter/counter_xhtml.js"></script>
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

	<div id="menu">
		<img src="style/deco-vigne_1.png" alt="" />
		<div id="plat">
			<?php echo $plat; ?>
		</div>
		<img src="style/deco-vigne_2.png" alt="" />
	</div>

	<div id="share">
		<a href="http://twitter.com/?status=<?php echo urlencode($plat); ?>,+sur+hervemarmiton.org+cci+@vpecresse">Tweet</a> - 
		<a href="http://www.facebook.com/sharer/sharer.php?u=http://www.hervemarmiton.org/<?php echo $id; ?>.html">Share</a>
	</div>	
	
	<div id="bouton-actualiser"><input type="button" OnClick="window.location='http://www.hervemarmiton.org/'" value="Plus de délices..."></div>

	<!--
	<div id="centrer">
		<div class="fb-share-button" data-layout="link" data-href="http://www.hervemarmiton.org/<?php echo $id; ?>"></div>
	</div>
	-->
	
	<a href="http://www.hervemarmiton.org/liste.html" id="lien-liste">Tous les plats créés</a>
	
</body>

</html>