<?php

/* 
 * Recibe las llamadas de las agencias nuestras para buscar los 
 * datos de los TPV a donde deben ser enviadas las operaciones
 */
define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';
$temp = new ps_DB;



$inputJson = file_get_contents('php://input');
$input = json_decode($inputJson, TRUE);

//echo $input."<br>";
//foreach ($input as $key => $value) {
//	echo "$key => $value";0
//}$d['id'] > 100000000 && strlen($d['id']) == 12
error_log($input['id']);
if ($input['id'] > 100000000 && strlen($input['id']) == 12) {
	error_log("select case p.estado when 'P' then c.urlPro else c.urlDes end url from tbl_cenAuto c, tbl_pasarela p, tbl_transacciones t where t.pasarela = p.idPasarela and c.id = p.idcenauto and t.idtransaccion = ".$input['id']);
	$temp->query("select case p.estado when 'P' then c.urlPro else c.urlDes end url from tbl_cenAuto c, tbl_pasarela p, tbl_transacciones t where t.pasarela = p.idPasarela and c.id = p.idcenauto and t.idtransaccion = ".$input['id']);
	echo json_encode(array("url" => $temp->f("url")));
	$temp->query("update tbl_transacciones set fechaAgen = unix_timestamp() where idtransaccion = '".$input['id']."'");
	error_log("update tbl_transacciones set fechaAgen = unix_timestamp() where idtransaccion = '".$input['id']."'");
}

?>