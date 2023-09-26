<?php
function GetElementByName ($xml, $start, $end) {

   global $pos;
   $startpos = strpos($xml, $start);
   if ($startpos === false) {
	   return false;
   }
   $endpos = strpos($xml, $end);
   $endpos = $endpos+strlen($end);
   $pos = $endpos;
   $endpos = $endpos-$startpos;
   $endpos = $endpos - strlen($end);
   $tag = substr ($xml, $startpos, $endpos);
   $tag = substr ($tag, strlen($start));

   return $tag;

}

function desofuscar ($pal_sec_ofuscada, $clave) {
// las siguientes dos l�neas son ejemplos del formato  de entrada de datos
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

$count = 0;
$pos = 0;
if ($_POST) {
	$peticion = $_POST['peticion'];

	//Goes throw XML file and creates an array of all <XML_TAG> tags.
	while ($node = GetElementByName($peticion, "<oppago>", "</oppago>")) {
	   $Nodes[$count] = $node;
	   $count++;
	   $peticion = substr($peticion, $pos);
	}

	//Gets infomation from tag siblings.
	$pase = false;
	for ($i=0; $i<$count; $i++) {
		$terminal = GetElementByName($Nodes[$i], "<idterminal>", "</idterminal>");
		$comercio = GetElementByName($Nodes[$i], "<idcomercio>", "</idcomercio>");
		$idtrans = GetElementByName($Nodes[$i], "<idtransaccion>", "</idtransaccion>");
		$importe = str_replace(".", "", GetElementByName($Nodes[$i], "<importe>", "</importe>"));
		$urlcomercio = GetElementByName($Nodes[$i], "<urlcomercio>", "</urlcomercio>");
		$idioma = GetElementByName($Nodes[$i], "<idioma>", "</idioma>");
		$pais = GetElementByName($Nodes[$i], "<pais>", "</pais>");
		$firma = GetElementByName($Nodes[$i], "<firma>", "</firma>");
		$urledir = GetElementByName($Nodes[$i], "<urlredir>", "</urlredir>");
		$localizador = GetElementByName($Nodes[$i], "<localizador>", "</localizador>");
		$moneda = GetElementByName($Nodes[$i], "<moneda>", "</moneda>");
		$pase = true;
	}

	echo "
			<form action='' method='get'>
				Terminal: <input type='text' value='$terminal' name='idterminal'><br>
				id Comercio: <input type='text' value='$comercio' name='idcomercio'><br>
				Comercio: <input type='text' value='' name='nombrecomercio'><br>
				Transaccion: <input type='text' value='$idtrans' name='idtransaccion'><br>
				Moneda: <input type='text' value='$moneda' name='moneda'><br>
				Importe: <input type='text' value='$importe' name='importe'><br>
				Fecha: <input type='text' value='".date( 'd/m/Y H:i:s')."' name='fechahora'><br>
				Estado :<select name='estado'>
					<option value='1'>En Proceso</option>
					<option value='2'>Aceptada</option>
					<option value='3'>Denegada</option>
					<option value='4'>No Procesada</option>
					<option value='5'>Anulada</option>
				</select><br>
				Código de error: <input type='text' value='' name='coderror'><br>
				Código de Autorización: <input type='text' value='' name='codautorizacion'><br>
				<input type='text' value='$urlcomercio' name='urledir'>
				<input type='submit' value='Enviar'>
			</form>
	";
} elseif ($_GET) {
	$d = $_GET;
	$idterminal = $d['idterminal'];
	$idcomercio = $d['idcomercio'];
	$nombrecomercio = $d['nombrecomercio'];
	$idtransaccion = $d['idtransaccion'];
	$moneda = $d['moneda'];
	$importe = $d['importe'];
	$fechahora = $d['fechahora'];
	$estado = $d['estado'];
	$coderror = $d['coderror'];
	$codautorizacion = $d['codautorizacion'];
	$urledir = $d['urledir'];
	$clave = desofuscar("43;52;28;35;22;57;5A;28;7B;09;01;03;74;70;73;04;79;13;1C;1D", "santaemilia");
	$firma = strtoupper(sha1($idterminal.$idcomercio.$idtransaccion.$importe.$moneda.$estado.$coderror.$codautorizacion.$clave));


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
	$peticion=$xml;

	echo '<form name="envia" action="'.$urledir .'" method="get">
			 <input type="hidden" name="peticion" value="' .$peticion. '"/>
			 <input type="submit" />
		 </form>';
}
?>
