<?php

/* 
 * Fichero ejecutar querys desde usrFinc.php
 */

//ini_set('display_errors', 0);
//error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");
	
define('_VALID_ENTRADA', 1);
require_once( 'configuration.php' );
require_once("admin/classes/SecureSession.class.php");
$Session = new SecureSession(_TIEMPOSES); //la sessión cambiada a una duración de 5 horas a partir del 17/01/18	

require_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';

$temp = new ps_DB;
//$correo = new correo;
$ent = new entrada;
$d = $_POST;

//pone al Cliente como que no hay que mandarle correo
if ($d['func'] == 'actli') {
	if ($d['idss'] * 1 < 10000000) {
		//quita los errores del Cliente
		$temp->query("delete from tbl_aisClienteError where idcliente = ".$d['idss']);
		//quita los Clientes del envío de correos
		$q = "update tbl_aisCliente set correoenv= 0 where id = ".$d['idss'];
		$temp->query($q);
		echo utf8_encode(json_encode(array("lim"=>"1", "q"=>$q)));
	}
}

?>