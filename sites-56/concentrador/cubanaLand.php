<?php

/*
 * Página que recibe la respuesta de la confirmación
 * de la operación enviada a Amadeus
 */

define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';
require_once( 'include/correo.php' );
require_once( 'admin/classes/entrada.php' );
$corCreo = new correo();
$temp = new ps_DB;
$ent = new entrada;

$correoMi = '';
$d = $_REQUEST;
$cont = date('d/m/y H:i:s')." - ";

foreach ($d as $key => $value) {
	$cont .= $key." => ".$value."\n";
}

if (!($cod = $ent->isAlfanumerico($d['fac'], 8))) {$correoMi .= "<!-- falla por badnumber -->"; $correoC->todo(17, $subject, $correoMi); exit;}
if (!($empr = $ent->isEntero($d['com'], 14))) {$correoMi .= "<!-- falla por badnumber -->"; $correoC->todo(17, $subject, $correoMi); exit;}
		
$q = "update tbl_amadeus set recibida = 1 where idcomercio = '$empr' and rl = '$cod'";
$correoMi .= "<br>\n". $q;
$temp->query($q);
if ($temp->getErrorMsg()) $correoMi .= "<br>\n". $temp->getErrorMsg();

echo '0';
$corCreo->todo(17,"Recepción del resultado por Amadeus", $cont.$correoMi);
//mail('jotate@amfglobalitems.com', 'Envío de datos a Amadeus otro', $cont, $headers);

?>