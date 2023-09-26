<?php define('_VALID_ENTRADA', 1);
require_once( '../configuration.php' );
include '../include/mysqli.php';
require_once( '../admin/adminis.func.php' );
require_once( '../include/hoteles.func.php' );
$temp = new ps_DB;

$d = $_REQUEST;

if (_MOS_CONFIG_DEBUG) {
	// $d['comercio'] = '163430526040';
	// $d['operacion'] = 'CO';
	// $d['fecha'] = time();
	// $temp->query("select palabra from tbl_comercio where idcomercio = '".$d['comercio']."'");
	// $d['firma'] = hash("sha256", $d['comercio'].$d['transaccion'].$d['operacion'].$temp->f('palabra'));
}

// error_log("servicios".json_encode($d));

$titulo = "Servicios ";
$traza = "Fecha: ". date('d/m/Y H:i:s') ."\n ".json_encode($d) . "\n";

rec ($titulo, $traza);

if (_MOS_CONFIG_DEBUG) echo $traza;

if (isset($d['firma']) && isset($d['comercio']) && $d['fecha'] > 1000 && $d['operacion'] == 'CO') {
	$titulo .= 'Solicitud de tasa';
	$d['metodo'] = 'tasa';
	$salida = consulta($d);
	echo $salida;
	rec($titulo, $traza);
	exit;

} elseif (isset($d['firma']) && isset($d['operacion']) && $d['operacion'] == 'P') {//operación
		$impr = "<form name=\"envia\" action=\"https://www.administracomercios.com\" method=\"POST\">";
		foreach ($d as $key => $value) {
			$impr .= "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
			error_log("envia - $key => $value");
			
		}
		$impr .= "</form><script language='javascript'>document.envia.submit();</script>";

		echo $impr;

} elseif (isset($d['firma']) && $d['operacion'] == 'S' && strlen($d['motivo']) < 180) { //solicitud de devolución
	$titulo .= "Solicitud de devolucion";
	$salida = devuelve($d);
	echo $salida;
	rec($titulo, $traza);
	exit;
} elseif (isset($d['firma']) && isset($d['comercio']) && ($d['transaccion']*1 > 100)) { //consulta de una operación
	$titulo .= "Consulta de la operacion";
	$d['metodo'] = 'consulta';
	$salida = consulta($d);
	echo $salida;
	rec($titulo, $traza);
	exit;
} elseif (isset($d['firma']) && isset($d['comercio']) && isset($d['transaccion']) && $d['operacion'] == 'XD') { //cambia la operación de Solc dev a Aceptada
	//datos a enviar firma=comercio.operacion
	$titulo .= "Cambia operación de No procesada a Aceptada";
	// echo $titulo;
	$d['metodo'] = 'rem3';
	$salida = consulta($d);
	if (strpos($salida, 'correctamente Actualizada')) $salida = 'ok';
	echo $salida;
	rec($titulo, $traza);
	exit;
	//devuelve ok o el error
} elseif ($d['comercio'] && !$d['firma']) { //tarjetas asociadas al comercio
	$titulo .= "Tarjetas asociadas al comercio";
	$salida = tarjeta($d);
	echo $salida;
	rec($titulo, $traza);
	exit;
} elseif (isset($d['firma']) && isset($d['operacion']) && isset($d['comercio']) && $d['operacion'] == 'rem') { //datos de remesas
	//datos a enviar firma=comercio.operacion
	//devuelve json con clientes, activos, nuevos, beneficiarios
	$titulo .= "Datos de remesas";
	$d['metodo'] = 'rem1';
	$salida = consulta($d);
	echo $salida;
	rec($titulo, $traza);
	exit;
} elseif (isset($d['firma']) && isset($d['comercio']) && isset($d['remitente']) && isset($d['estado'])) { //des/activación de remitentes
	//datos a enviar firma=comercio.remitente.estado
	$titulo .= "Activa o desactiva Clientes";
	$d['metodo'] = 'rem2';
	$salida = consulta($d);
	echo $salida;
	rec($titulo, $traza);
	exit;
	//devuelve ok o el error
} elseif (isset($d['encuesta']) && $d['encuesta'] == 'concentrador') {//libera la ip de la oficina desde el fichero liberaIP.php
	$titulo .= "Libera la IP de la oficina";
	$d['metodo'] = 'encuesta';
	$salida = consulta($d);
	echo $salida;
	rec($titulo, $traza);
	exit;
}

function enviacurl($url, $d){
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

	if (curl_errno ( $ch ))
		error_log("Error en la del Concentrador:" . curl_strerror ( curl_errno ( $ch ) ));
	$crlerror = curl_error ( $ch );
	
	if ($crlerror) {
		error_log("Error en la resp del Concentrador:" . $crlerror);
	}
	$curl_info = curl_getinfo ( $ch );
	curl_close ( $ch );

	return $salida;
}

function consulta($d) {
	// echo $GLOBALS['sitio_pago']."/ejecu.php";exit;
	return enviacurl($GLOBALS['sitio_pago']."/ejecu.php", $d); 
}

function devuelve ($d) {
	return enviacurl($GLOBALS['sitio_pago']."/", $d);
}


function tarjeta ($d){
	return enviacurl($GLOBALS['sitio_pago']."/sendtar.php", $d);
}

function rec($titulo, $traza) {
	global $temp;
	$temp->query("insert into tbl_traza values (null, '$titulo', '$traza', unix_timestamp())");
}

?>
