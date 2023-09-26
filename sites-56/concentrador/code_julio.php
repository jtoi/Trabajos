<?php


    $data = array(
			"comercio"		=> 'prueba',
			"transaccion"           => 'prueba',
			"importe"		=> 'prueba',
			"moneda"		=> 'prueba',
			"resultado"		=> 'prueba',
			"codigo"		=> 'prueba',
			"idioma"		=> 'prueba',
			"firma"			=> 'prueba',
			"fecha"			=> 'prueba',
			"error"			=> 'prueba',
	);

	$options = array(
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_SSL_VERIFYPEER	=> false,
			CURLOPT_POST			=> true,
			CURLOPT_VERBOSE			=> true,
			CURLOPT_URL			=> 'http://www.hotel-saratoga.com/con_reservacion/pasarela',
			CURLOPT_POSTFIELDS		=> $data
	);

	$ch = curl_init();
	curl_setopt_array($ch , $options);

	$output = curl_exec($ch);

        $correoMi = '';
	if (curl_errno($ch)) {
            $correoMi .=  "Error en la comunicacion al comercio:".curl_strerror(curl_errno($ch))."<br>\n";
        }

	$crlerror = curl_error($ch);
	if ($crlerror) {
            $correoMi .=  "La comunicacion al comercio ha dado error:".$crlerror."<br>\n";
	}

    echo 'Errrorrrr:\n'. $crlerror.$correoMi;

	$curl_info = curl_getinfo($ch);
	curl_close($ch);

