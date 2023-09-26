<?php

$d = $_POST;

$d['operacion'] 	= '220328211123';
$d['comercio']		= '160253960650';
$d['firma'] 		= hash("sha256", $d['comercio'].$d['operacion'].'NhNEMDjcMMXYnBweRAKh');

echo $d['firma'];


$options = array (
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_POST => true,
		CURLOPT_VERBOSE => true,
		// CURLOPT_URL => "http://192.168.0.1/concentrador/serv/",
		CURLOPT_URL => "https://servicios.administracomercios.com/index.php",
		CURLOPT_POSTFIELDS => $d ,
		CURLOPT_SSL_VERIFYHOST => false
	);
					
	$ch = curl_init ();
	curl_setopt_array ( $ch, $options );
	echo json_encode($options);
	$salida = curl_exec ( $ch );
	echo $salida;
//		exit;
	if (curl_errno ( $ch ))
		error_log("Error en la del Concentrador:" . curl_strerror ( curl_errno ( $ch ) ));
	$crlerror = curl_error ( $ch );
	
	if ($crlerror) {
		error_log("Error en la resp del Concentrador:" . $crlerror);
	}
	$curl_info = curl_getinfo ( $ch );
	curl_close ( $ch );

?>