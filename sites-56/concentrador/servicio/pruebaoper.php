<?php

$dirIp = '127.0.0.1';
$d['comercio']= '163474342422'; //Transfermovil
$d['transaccion']=substr(time(), -8);
$d['importe']='5000';
$d['moneda']='978';
$d['operacion']='P';
$d['idioma']='es';
$d['pasarela']='31';
$d['amex']='2';
$d['firma']	= hash("sha256", $d['comercio']. $d['transaccion']. $d['importe']. $d['moneda']. $d['operacion']. 'xRLAmFr982MRUL9UzbbV');

$impr = "<form name=\"envia\" action=\"https://servicios.administracomercios.com/\" method=\"POST\">";
// $impr = "<form name=\"envia\" action=\"http://192.168.0.1/concentrador/serv/\" method=\"POST\">";
foreach ($d as $key => $value) {
	$impr .= "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
	error_log("envia - $key => $value");
	
}
$impr .= "</form><script language='javascript'>document.envia.submit();</script>";

echo $impr;
?>