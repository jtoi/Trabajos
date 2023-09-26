<?php define( '_VALID_ENTRADA', 1 );
error_log(json_encode($_REQUEST));
error_log($_SERVER['REMOTE_ADDR']);
//if ($_SERVER['REMOTE_ADDR'] != '217.160.140.131' && $_SERVER['REMOTE_ADDR'] != '152.206.69.166' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') die ("acceso prohibido");

//echo(json_encode($_REQUEST));
include_once "php/cors.php";

require_once( 'configuration.php' );
require_once 'include/mysqli.php';
$temp = new ps_DB;


$d = $_REQUEST;
//error_log(($_REQUEST));

$temp->query("select t.idtransaccion, c.nombre, format(t.valor_inicial/100, 2) valini, format(t.valor,2) val, t.moneda, t.estado  from tbl_transacciones t, tbl_comercio c where t.idcomercio = c.idcomercio order by fecha desc limit 0,10");

$arrResp = $temp->loadAssocList();

echo json_encode($arrResp);
?>
