<?php

date_default_timezone_set('Europe/Berlin');
$dirIp = '127.0.0.1';
$d['comercio']= '163474342422'; //Transfermovil
$d['transaccion']='211112210797';
$d['importe']='500';
$d['moneda']='978';
$d['operacion']='S';
$d['motivo']='Pruebas de devolución';
$d['firma']	= hash("sha256", $d['comercio']. $d['transaccion']. $d['importe']. $d['moneda']. $d['operacion']. 'xRLAmFr982MRUL9UzbbV');

error_log('entra');
// $url = "https://152.206.118.32:8443/";
$url = "http://192.168.0.1/concentrador/serv/";
// $url = "http://192.168.0.243:8080/concentrador/serv/";

$options = array (
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_POST => true,
	CURLOPT_VERBOSE => true,
	CURLOPT_URL => $url,
	CURLOPT_POSTFIELDS => $d ,
	CURLOPT_SSL_VERIFYHOST => false
);
				
$ch = curl_init ();
curl_setopt_array ( $ch, $options );
$salida = curl_exec ( $ch );
echo $salida;

if (curl_errno ( $ch ))
	error_log("Error en la del Concentrador:" . curl_strerror ( curl_errno ( $ch ) ));
$crlerror = curl_error ( $ch );

if ($crlerror) {
	error_log("Error1 en la resp del Concentrador:" . $crlerror);
}
$curl_info = curl_getinfo ( $ch );
curl_close ( $ch );


?>