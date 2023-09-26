
<?php
/*
 * Envia los datos a llegada.php como si fuese una respuesta de BBVA
 */

define( '_VALID_ENTRADA', 1 );
require_once 'configuration.php';

$url = "http://localhost/concentrador/rep/llegada.php?peticion=";

$d = $_POST;
if ($d['idterminal']) {
	$idterminal			= $d['idterminal'];
	$idcomercio			= $d['idcomercio'];
	$nombrecomercio		= $d['nombrecomercio'];
	$idtransaccion		= $d['idtransaccion'];
	$moneda				= $d['moneda'];
	$importe			= $d['importe'];
	$fechahora			= $d['fechahora'];
	$estado				= $d['estado'];
	$coderror			= $d['coderror'];
	$codautorizacion	= $d['codautorizacion'];
	$localizador		= $d['localizador'];

	$clave = desofuscar(_PALABR_OFUS, _CONTRASENA_OFUS);
	echo "$idterminal . $idcomercio . $idtransaccion . $importe . $moneda . $estado . $coderror . $codautorizacion . $clave||<br>";
	$firma = strtoupper(sha1($idterminal . $idcomercio . $idtransaccion . $importe . $moneda . $estado . $coderror . $codautorizacion . $clave ));


	$lt="&lt;";
	$gt="&gt;";
		 $xml.=$lt."tpv".$gt;
		 $xml.=$lt."respago".$gt;
			 $xml.=$lt."idterminal".$gt.$idterminal.$lt."/idterminal".$gt;
			 $xml.=$lt."idcomercio".$gt.$idcomercio.$lt."/idcomercio".$gt;
			 $xml.=$lt."nombrecomercio".$gt.$nombrecomercio.$lt."/nombrecomercio".$gt;
			 $xml.=$lt."idtransaccion".$gt.$idtransaccion.$lt."/idtransaccion".$gt;
			 $xml.=$lt."moneda".$gt.$moneda.$lt."/moneda".$gt;
			 $xml.=$lt."importe".$gt.$importe.$lt."/importe".$gt;
			 $xml.=$lt."fechahora".$gt.$fechahora.$lt."/fechahora".$gt;
			 $xml.=$lt."estado".$gt.$estado.$lt."/estado".$gt;
			 $xml.=$lt."coderror".$gt.$coderror.$lt."/coderror".$gt;
			 $xml.=$lt."deserror".$gt.$lt."/deserror".$gt;
			 $xml.=$lt."codautorizacion".$gt.$codautorizacion.$lt."/codautorizacion".$gt;
			 $xml.=$lt."firma".$gt.$firma.$lt."/firma".$gt;
		 $xml.=$lt."/respago".$gt;
	 $xml.=$lt."/tpv".$gt;
	$peticion=$url.$xml;
	
//	echo "<script type='text/javascript'>window.open('$peticion','_self')</script>";
	echo "$peticion";
} else {
?>
<form action="" method="post">
	Terminal: <input type='text' value='999999' name='idterminal'><br>
	id Comercio: <input type='text' value='B9550206800001' name='idcomercio'><br>
	Comercio: <input type='text' value='CARIBBEAN ONLINE' name='nombrecomercio'><br>
	Transaccion: <input type='text' value='$idtrans' name='idtransaccion'><br>
	Moneda: <select name='moneda'><option value="978" selected>EUR</option><option value="840">USD</option><option value="826">GBP</option><option value="124">CAD</option></select><br>
	Importe: <input type='text' value='$importe' name='importe'><br>
	Fecha: <input type='text' value='<?php echo date( 'd/m/Y H:i:s')?>' name='fechahora'><br>
	Estado :<select name='estado'>
		<option value='1'>En Proceso</option>
		<option value='2'>Aceptada</option>
		<option value='3'>Denegada</option>
		<option value='4'>No Procesada</option>
		<option value='5'>Anulada</option>
	</select><br>
	Código de error: <input type='text' value='' name='coderror'><br>
	Código de Autorización: <input type='text' value='' name='codautorizacion'><br>
	Localizador: <input type='text' value='234623452343' name='localizador'>
	<input type='submit' value='Enviar'>
</form>
<?php
}

function desofuscar ($pal_sec_ofuscada, $clave) {
// las siguientes dos lï¿½neas son ejemplos del formato  de entrada de datos
//	$pal_sec_ofuscada = "5D;7F;0A;27;09;0D;25;5D;04;01;0B;00;06;01;00;70;06;1C;19;19";
//	$clave_xor = "eH2dJ9gkB82915026***";

	$clave_xor = substr($clave,0,8).substr("B9550206800001",0,9)."***";
/*	while (strlen($clave_xor) < 20){
		$clave_xor .= '*';
	}*/

if (_MOS_CONFIG_DEBUG) echo "clave_xor= ".$clave_xor."<br>";
if (_MOS_CONFIG_DEBUG) echo "pal_sec_ofuscada = $pal_sec_ofuscada<br>";

	$cad1_0 = "0";
	$cad2_0 = "00";
	$cad3_0 = "000";
	$cad4_0 = "0000";
	$cad5_0 = "00000";
	$cad6_0 = "000000";
	$cad7_0 = "0000000";
	$cad8_0 = "00000000";
	$pal_sec = "";

	//valores devueltos por bbva
// 	$valor = rand (0, 99);
// 	$id_trans = date("mdHis").$valor;
// 	$localizador="1234567890";
// 	$numtarjeta=$_POST["bbva_number"];
// 	$fechacad="20" . $_POST["bbva_expires"];
// 	$importe = $_POST["card_total"];

	$trozos = explode (";", $pal_sec_ofuscada);
	$tope = count($trozos);

	for ($i=0; $i<$tope ; $i++) {
		$res = "";
		$pal_sec_ofus_bytes[$i] = decbin(hexdec($trozos[$i]));
		if (strlen($pal_sec_ofus_bytes[$i]) == 7){ $pal_sec_ofus_bytes[$i] = $cad1_0.$pal_sec_ofus_bytes[$i]; }
		if (strlen($pal_sec_ofus_bytes[$i]) == 6){ $pal_sec_ofus_bytes[$i] = $cad2_0.$pal_sec_ofus_bytes[$i]; }
		if (strlen($pal_sec_ofus_bytes[$i]) == 5){ $pal_sec_ofus_bytes[$i] = $cad3_0.$pal_sec_ofus_bytes[$i]; }
		if (strlen($pal_sec_ofus_bytes[$i]) == 4){ $pal_sec_ofus_bytes[$i] = $cad4_0.$pal_sec_ofus_bytes[$i]; }
		if (strlen($pal_sec_ofus_bytes[$i]) == 3){ $pal_sec_ofus_bytes[$i] = $cad5_0.$pal_sec_ofus_bytes[$i]; }
		if (strlen($pal_sec_ofus_bytes[$i]) == 2){ $pal_sec_ofus_bytes[$i] = $cad6_0.$pal_sec_ofus_bytes[$i]; }
		if (strlen($pal_sec_ofus_bytes[$i]) == 1){ $pal_sec_ofus_bytes[$i] = $cad7_0.$pal_sec_ofus_bytes[$i]; }
		$pal_sec_xor_bytes[$i] = decbin(ord($clave_xor[$i]));

		if (strlen($pal_sec_xor_bytes[$i]) == 7){ $pal_sec_xor_bytes[$i] = $cad1_0.$pal_sec_xor_bytes[$i]; }
		if (strlen($pal_sec_xor_bytes[$i]) == 6){ $pal_sec_xor_bytes[$i] = $cad2_0.$pal_sec_xor_bytes[$i]; }
		if (strlen($pal_sec_xor_bytes[$i]) == 5){ $pal_sec_xor_bytes[$i] = $cad3_0.$pal_sec_xor_bytes[$i]; }
		if (strlen($pal_sec_xor_bytes[$i]) == 4){ $pal_sec_xor_bytes[$i] = $cad4_0.$pal_sec_xor_bytes[$i]; }
		if (strlen($pal_sec_xor_bytes[$i]) == 3){ $pal_sec_xor_bytes[$i] = $cad5_0.$pal_sec_xor_bytes[$i]; }
		if (strlen($pal_sec_xor_bytes[$i]) == 2){ $pal_sec_xor_bytes[$i] = $cad6_0.$pal_sec_xor_bytes[$i]; }
		if (strlen($pal_sec_xor_bytes[$i]) == 1){ $pal_sec_xor_bytes[$i] = $cad7_0.$pal_sec_xor_bytes[$i]; }

		for ($j=0; $j<8; $j++) {
			(string)$res .= (int)$pal_sec_ofus_bytes[$i][$j] ^ (int)$pal_sec_xor_bytes[$i][$j];
		}
		$xor[$i] = $res;
		$pal_sec .= chr(bindec($xor[$i]));
	}
	return $pal_sec;
}


?>
