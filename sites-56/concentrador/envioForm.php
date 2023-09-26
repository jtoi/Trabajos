<?php
/**
* Simula contra llegada.php una respuesta del TPV
*/
define( '_VALID_ENTRADA', 1 );
require_once( 'include/hoteles.func.php' );
$d = _REQUEST;
if ($d['comercio']) {
	if ($d['firma']) $firma = $d['firma']; else {
		$firma = convierte256($d['comercio'], $d['transaccion'], $d['importe'], $d['moneda'], $d['operacion'], $d['codigo'], $d['fecha']);
	}

	$cadenaEnv = "?comercio={$d['comercio']}&transaccion={$d['transaccion']}&importe={$d['importe']}&moneda={$d['moneda']}&resultado={$d['operacion']}&
							codigo={$d['codigo']}&idioma={$d['idioma']}&fecha=".$d['fecha']."&firma=$firma";

	//$ch = curl_init("https://www.rrsol.com/payment/amf_accepteddirect.asp".$cadenaEnv);

	//curl_setopt($ch, CURLOPT_HEADER, 0);
	//curl_setopt($ch, CURLOPT_POST, 1);
	//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//$output = curl_exec($ch);
	//curl_close($ch);
	//echo  "respuCurl=$output||\n";
}


?>


<form action="https://www.administracomercios.com/index.php" method="post">
comercio:<input type="text" name="comercio" /><br />
transaccion: <input type="text" name="transaccion" /><br />
importe: <input type="text" name="importe" /><br />
moneda: <input type="text" name="moneda" /><br />
operación: <input type="text" name="operacion" value="P" /><br />
pasarela: <input type="text" name="pasarela" /><br />
firma: <input type="text" name="firma" /><br />
<input type="submit" name="Envia" />
</form>
