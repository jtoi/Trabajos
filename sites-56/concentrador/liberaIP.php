<?php

//encuesta el concentrador con la ip de la oficina para que borre la ip si está bloqueada
	$d['encuesta'] = 'concentrador';



	$options = array (
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_VERBOSE => true,
		CURLOPT_URL => "checkip.amazonaws.com",
		CURLOPT_SSL_VERIFYHOST => false
	);
					
	$ch = curl_init ();
	curl_setopt_array ( $ch, $options );
	// echo json_encode($options);
	$d['IP'] = curl_exec ( $ch );
	if (curl_errno ( $ch ))
		error_log("Error en la del Concentrador:" . curl_strerror ( curl_errno ( $ch ) ));
	$crlerror = curl_error ( $ch );
	if ($crlerror) {
		echo("Error en la resp del Concentrador:" . $crlerror);
	}
	$curl_info = curl_getinfo ( $ch );
	curl_close ( $ch );

	$d['firma'] = hash("sha256", $d['encuesta'].$d['IP']."Te he respondido cualquier cosa para que te calles. Tengo que ocuparme de cosas serias");

	$options = array (
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_POST => true,
		CURLOPT_VERBOSE => true,
		CURLOPT_URL => "https://servicios.administracomercios.com/",
		CURLOPT_POSTFIELDS => $d ,
		CURLOPT_SSL_VERIFYHOST => false
	);
					
	$ch = curl_init ();
	curl_setopt_array ( $ch, $options );
	// echo json_encode($options);
	$salida = curl_exec ( $ch );
	if (curl_errno ( $ch ))
		error_log("Error en la del Concentrador:" . curl_strerror ( curl_errno ( $ch ) ));
	$crlerror = curl_error ( $ch );
	if ($crlerror) {
		echo("Error en la resp del Concentrador:" . $crlerror);
	}
	$curl_info = curl_getinfo ( $ch );
	curl_close ( $ch );

?>