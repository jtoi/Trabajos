<?php
define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';

$temp = new ps_DB;

$d = $_POST;
$correoMi = '';

foreach ($d as $key => $value) {
	$correoMi .= "$key => $value<br>";
}
error_log("STRIPE!!!!!!".$correoMi);


if ($d['session']) {
	$q = "insert into tbl_stripeSess (sesion, pintent, idtransaccion, fecha) values ('".$d['session']."', '".$d['intent']."', '".$d['trans']."', unix_timestamp())";
	$temp->query($q);
	error_log($q);
}

if ($d['di']) {
	$q = "select estado, pintent from tbl_stripeSess where sesion = '".$d['id']."' and idtransaccion = '".$d ['di']."' and estado != 'A'";
	$temp->query($q);
	error_log($q);

	$cant = $temp->num_rows();
	echo json_encode(array("cant" => $cant, "intent" => $temp->f('pintent')));
}
