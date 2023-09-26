<?php

$f = "plantilla.php";
$file = new SplFileObject($f, 'r');
$contents = $file->fread($file->getSize());

if (isset($_REQUEST['valor']) && stripos($_REQUEST['valor'], '@') > 3) {
	include('mysqli.php');
	$temp = new ps_DB;

	$sale = '';

	$temp->query("SELECT id FROM `mp_newsletter_user` WHERE sha2(email,'224') = sha2('" . $_REQUEST['valor'] . "','224')");

	if ($temp->num_rows() == 1) {
		$temp->query("delete from mp_newsletter_user where id = " . $temp->f('id'));
		$sale = '<div class="okDiv">La suscripción a nuestro Newsletter ha sido cancelada.</div>';
	} else $sale = '<div class="koDiv">Error: Lo sentimos, su correo no está en nuestra Base de Datos.</div>';
} else {
}


echo str_replace('{{cont}}', $sale, $contents);
$file = null;
