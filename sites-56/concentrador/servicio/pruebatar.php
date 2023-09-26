<?php
$url = "https://152.206.118.32:8443/";
// $url = "http://192.168.0.1/concentrador/serv/";

$d['comercio'] = '163474342422';

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