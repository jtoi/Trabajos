<?php
define( '_VALID_ENTRADA', 1 );
require_once( 'include/curlenv.class.php' );

$post = array();
$post['comercio'] = "140778652871";
$post['transaccion'] = "ba7ffc2cde0d";
$post['importe'] = "49400";
$post['moneda'] = "840";
$post['operacion'] = "P";
$post['firma'] = "6768362a768f688a8152ae5d3cc18952";

$http = new curlenv();
$http->init();
$urldest = "http://localhost/concentrador/curlResp.php";
print_r($post);
echo $http->post($urldest,$post,false);
echo $http->get_error();
$http->close();
?>