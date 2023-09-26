<?php
/**
 * Al no tener el Beneficiario en la base de datos lo busco en Fincimex
*/

function buscaBen($idcliente) {
	global $temp, $correo, $correoMi;

	$arrDatos["idCliente"] 		= $idcliente;

	$output = enviaCurl($arrDatos,"https://www.aisremesascuba.com/busca/");

	$correoMi .= "$output<br>";
	$arrDat = json_decode($output);
	$idBen = $arrDat['Id'];
	if ($idBen == enviaCurl($arrDat, "https://www.administracomercios.com/datInscr.php")) return true;
	else return false;
}

function enviaCurl($arrDatos, $url) {
	global $correoMi;

	$header = array(
		'Connection: keep-alive',
		'Content-Type: application/x-www-form-urlencoded'
	);

	$options = array(
		CURLOPT_URL				=> $url,
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_HEADER 			=> false,
		CURLOPT_USERAGENT		=> 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
		CURLOPT_SSL_VERIFYPEER	=> true,
		CURLOPT_CONNECTTIMEOUT	=> 30,
		CURLOPT_TIMEOUT			=> 120,
		CURLOPT_POST			=> true,
		CURLOPT_FOLLOWLOCATION	=> true,
		CURLOPT_POSTFIELDS		=> $arrDatos,
		CURLOPT_CUSTOMREQUEST	=> 'POST',
		CURLOPT_HTTPHEADER		=> $header
	);

	$ch = curl_init();
	curl_setopt_array($ch, $options);
	$correoMi .= "options:<br>";
	foreach ($options as $key => $value) {
		$correoMi .= "$key => $value<br>";
	}
	$correoMi .= "<br>";

	$output = curl_exec($ch);
	$responseInfo = curl_getinfo($ch);
	curl_close($ch);
	return $output;
}

?>