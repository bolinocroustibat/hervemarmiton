<html>

<?php

$base = new Imagick('style/FB-background.png');
$over = new Imagick('style/FB-overlay.png');

// Add overlay
$base->compositeImage($over, Imagick::COMPOSITE_DEFAULT, 0, 0);
header("Content-Type: image/png");

echo $base;


?>

</html>