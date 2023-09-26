<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors', 0);
error_reporting(0);

define('_VALID_ENTRADA', 1);
if (!session_start())
	session_start();
require_once( 'admin/classes/entrada.php' );
require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/hoteles.func.php' );
require_once( 'admin/adminis.func.php' );

$temp = new ps_DB;
$ent = new entrada;
$correoMi = '';
$cadena = "Cadena devuelta:\n";
//$send_m = new sendmail();

$d = $_REQUEST;
foreach ($d as $key => $value) {
	$correoMi .= "$key => $value\n";
}

$comercio = $ent->isAlfanumerico($d['store'], 15);
$trans = $ent->isAlfanumerico($d['order'], 15);

if ($trans && $comercio) {
	$q = sprintf("select t.valor_inicial valor, t.moneda, c.nombre from tbl_transacciones t, tbl_comercio c where t.idcomercio = 
			c.idcomercio and t.idtransaccion = '%s'",$trans);
	$correoMi .= $q."\n";
	$temp->query($q);
	$val = $temp->f("valor");
	$mon = $temp->f("moneda");
	$com = $temp->f("nombre");
	
	$cane = "M".$mon.$val."\r\n1\r\n$trans\r\nPago de servicio o productos al comercio $com\r\n1\r\n$val";
	$correoMi .= $cane;
	echo $cane;
}

$correoMi .= "\nFIN";
mail('jtoirac@gmail.com', 'cadena de santander', $correoMi);
?>
