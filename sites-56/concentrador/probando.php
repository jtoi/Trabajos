<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('_VALID_ENTRADA', 1);
//ini_set("display_errors", 1);
//error_reporting(E_ALL & ~E_NOTICE);
if (!session_start())
	session_start();
require_once( 'admin/classes/entrada.php' );
//require_once( '../include/sendmail.php' );
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'admin/adminis.func.php' );
$temp = new ps_DB;

echo trIdent('97');

$q = "insert into tbl_transacciones (idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor_inicial, tipoEntorno, moneda, estado, sesion, idioma, pasarela, ip, idpais) values ('040305185447', '527341458854', '131925', 'P', 1394042051, 1394042051, 11732, 'P', 840, 'P', 'd3f8ccc6e3aaae029d6190b26cf9080a', 'es', 18, '74.59.99.51', 38)";
echo $temp->query($q);
echo $temp->getErrorMsg()

?>