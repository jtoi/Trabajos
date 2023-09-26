<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors', 0);
error_reporting(0);

define( '_VALID_ENTRADA', 1 );
if (!session_start()) session_start();
require_once( 'admin/classes/entrada.php' );
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );
include_once( "include/sendmail.php" );

$temp = new ps_DB;
$send_m = new sendmail();
$ent = new entrada;

$cadenSal = '';
$d = $_REQUEST;
$arrayEnt = array();
$cont = 0;
foreach ($d as $item) {
	$cadenSal .= $item." | ";
	$arrayEnt[$cont++] = $item;

}
$cadenSal .= $_SERVER['REMOTE_ADDR']."\n";

if ($ent->isAlfanumerico($arrayEnt[0], 14)) $idtran = $arrayEnt[0]; else exit;
if ($ent->isAlfanumerico($arrayEnt[1], 12)) $comerCod = $arrayEnt[1]; else exit;

$cadenSal .= "idtran = $idtran\n";
$cadenSal .= "comerCod = $comerCod\n";

if ($comerCod == _BANESTO_CLAV_COMER) {
	$query = "select valor_inicial, identificador, moneda, idcomercio from tbl_transacciones t where idtransaccion = '".$idtran."'";
	$temp->query($query);
	$cadenSal .= "\n$query";

	$valor = $temp->f('valor_inicial');
	$ident = $temp->f('identificador');
	$moneda = $temp->f('moneda');
	$comercio = $temp->f('idcomercio');

	$query = "select servicio from tbl_reserva where codigo = '$ident' and id_comercio = '$comercio'";
	$temp->query($query);
	$cadenSal .= "\n$query";
	
	if (strlen($temp->f('servicio')) == 0 ) $descripcion = 'Ver descripción de la compra en voucher'; else $descripcion = $temp->f('servicio');
	$unidad = 1;
	$cadenSal .= "\n$query";

	$salida = "M$moneda$valor\n\n1\n\n$ident\n\n$descripcion\n\n$unidad\n\nM$moneda$valor\n\n";
	echo $salida;
	$cadenSal .= "\n$salida";

}

$to =	'jotate@amfglobalitems.com';
$from = 'info@amfglobalitems.com';
$headers  = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text; charset=iso-8859-1' . "\n";
$headers .= 'To: '.$todale[0].'<'. $to . ">\n";
$headers .= 'From: Administrador de Comercios AMF Global - '.$comercioN.' <'. $from . ">\n";
$subject = 'Cadena Llegada y Salida Banesto';

$send_m->from($from);
$send_m->to($to);
$send_m->set_message($cadenSal);
$send_m->set_subject($subject);
$send_m->set_headers($headers);
$enviado = $send_m->send();

//echo $cadenSal;

?>
