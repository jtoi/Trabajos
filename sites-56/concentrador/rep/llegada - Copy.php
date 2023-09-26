<?php

define( '_VALID_ENTRADA', 1 );
require_once( '../configuration.php' );
require_once( '../include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( '../include/ps_database.php' );
require_once( '../include/hoteles.func.php' );
include_once("../admin/classes/class_dms.php");
require_once( '../include/sendmail.php' );
include_once("../admin/adminis.func.php");

$temp = new ps_DB;
$dms=new dms_send;

#Datos de acceso a la plataforma
$dms->autentificacion->idcli='126560';
$dms->autentificacion->username='amfglobalitems';
$dms->autentificacion->passwd='Mario107';

$correoMi = 'inicio||';
$pasarela = null;

//$handle = fopen("salsa.txt", "w");
//fwrite($handle, "INICIO<br>\n");
if ($_REQUEST['peticion']){
    $salida = $_REQUEST['peticion'];
    $pasarela = 1;
} elseif ($_REQUEST['Ds_AuthorisationCode']) {
    $salida = $_REQUEST['Ds_AuthorisationCode'].' / '.$_REQUEST['Ds_Signature'];
    $pasarela = 2;
} elseif ($_REQUEST['pszPurchorderNum']) {
	$pasarela = 4;
	$salida = $_REQUEST['result'];
}

//$correoMi .= "\n\nLa pasarela es la ".$pasarela."\n<br>\n";
$correoMi .= $salida."||<br>\n";//lanzCorreo($correoMi);
//$headers = 'From: koldo@amfglobalitems.com' . "\r<br>\n" .
//		 'Reply-To: jotate@amfglobalitems.com' . "\r<br>\n" .
//		 'X-Mailer: PHP/' . phpversion();
//$to      = 'jotate@amfglobalitems.com';
//$subject = 'Salsa Especial Resultado del banco background ';
//$correoMi .= "\n$query";
//mail($to, $subject, $correoMi, $headers);

//fwrite($handle, $salida."<br>\n");
$str = '';
$dserror = '';
$firma = false;

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

$correoMi .= "pasarela=$pasarela||<br>\n";
//echo $correoMi;
if ($pasarela == 1) { //pasarela BBVA y BBVA3D y BBVA 3D onL
$correoMi .= "Entra en pasarela BBVA||<br>\n";
    $count = 0;
    $pos = 0;
	$pase1 = null;

	$pase1 = GetElementByName($salida, "<oppago>", "</oppago>");
$correoMi .= "pase1=$pase1||<br>\n";
	if ($pase1 == null || $pase1 == '') {
$correoMi .= "no coderror||<br>\n";
		//Goes throw XML file and creates an array of all <XML_TAG> tags.
		while ($node = GetElementByName($salida, "<respago>", "</respago>")) {
		   $Nodes[$count] = $node;
		   $count++;
		   $salida = substr($salida, $pos);
		}
	} else {
$correoMi .= "si coderror||<br>\n";
		while ($node = GetElementByName($salida, "<oppago>", "</oppago>")) {
		   $Nodes[$count] = $node;
		   $count++;
		   $salida = substr($salida, $pos);
		}
	}

$correoMi .= "count=$count||<br>\n";
	//Gets infomation from tag siblings.
	$pase = false;
	for ($i=0; $i<$count; $i++) {
		if (GetElementByName($Nodes[$i], "<estado>", "</estado>")) $estado = GetElementByName($Nodes[$i], "<estado>", "</estado>");
		else $estado = " ";
		$idtrans = GetElementByName($Nodes[$i], "<idtransaccion>", "</idtransaccion>");
		$comercio = GetElementByName($Nodes[$i], "<nombrecomercio>", "</nombrecomercio>");
		$importe = str_replace(".", "", GetElementByName($Nodes[$i], "<importe>", "</importe>"));
//		echo (GetElementByName($_REQUEST['peticion'], "<coderror>", "</coderror>"))."<br><br><br>";
		if (GetElementByName($_REQUEST['peticion'], "<coderror>", "</coderror>")) $coderror = GetElementByName($_REQUEST['peticion'], "<coderror>", "</coderror>");
		else $coderror = GetElementByName($Nodes[$i], "<coderror>", "</coderror>");
		if (GetElementByName($Nodes[$i], "<codautorizacion>", "</codautorizacion>")) $codautorizacion = GetElementByName($Nodes[$i], "<codautorizacion>", "</codautorizacion>");
		else $codautorizacion = " ";
		$firma = GetElementByName($Nodes[$i], "<firma>", "</firma>");
		$fechahora = GetElementByName($Nodes[$i], "<fechahora>", "</fechahora>");
		$idterminal = GetElementByName($Nodes[$i], "<idterminal>", "</idterminal>");
		$moneda = GetElementByName($Nodes[$i], "<moneda>", "</moneda>");
		$idcomercio = GetElementByName($Nodes[$i], "<idcomercio>", "</idcomercio>");
		if (GetElementByName($Nodes[$i], "<deserror>", "</deserror>")) $dserror = GetElementByName($Nodes[$i], "<deserror>", "</deserror>");
		$pase = true;
	}


    if ($pase) {
        $query = "select c.estado, t.pasarela from tbl_comercio c, tbl_transacciones t where c.idcomercio = t.idcomercio and idtransaccion = $idtrans";
        $temp->setQuery($query);
        $temp->query();
    //fwrite($handle, "query= $query <br>\n");
$correoMi .= "query= $query ||<br>\n";
        if ($temp->f('estado') == 'P') {
			if ($temp->f('pasarela') == '1') {
				$correoMi .=  $temp->f('pasarela')."||<br>\n" ;
				$clave = desofuscar(_PALABR_OFUS, _CONTRASENA_OFUS);
			} elseif ($temp->f('pasarela') == '3') {
				$correoMi .=  $temp->f('pasarela')."||<br>\n" ;
				$clave = desofuscar(_3DPALABR_OFUS, _3DCONTRASENA_OFUS);
			} elseif ($temp->f('pasarela') == '8') {
				$correoMi .=  $temp->f('pasarela')."||<br>\n" ;
				$clave = desofuscar(_MEXPALABR_OFUS, _MEXCONTRASENA_OFUS);
			} elseif ($temp->f('pasarela') == '11') {
				$correoMi .=  $temp->f('pasarela')."||<br>\n" ;
				$clave = desofuscar(_3DOPALABR_OFUS, _3DOCONTRASENA_OFUS, _3DOID_COMERCIO);
			}
        } elseif ($temp->f('estado') == 'D'){
            $clave = desofuscar(_TESTPALABR_OFUS_TEST, _TESTCONTRASENA_OFUS_TEST);
		}
    //fwrite($handle, $clave."<br>\n");
$correoMi .=  "clave=".$clave."||<br>\n";
        
$correoMi .= "$idterminal . $idcomercio . $idtrans . $importe . $moneda . $estado . $coderror . $codautorizacion . $clave||<br>\n";
        $comprueba = strtoupper(sha1($idterminal . $idcomercio . $idtrans . $importe . $moneda . $estado . $coderror . $codautorizacion . $clave ));

        //fwrite($handle, "firma=".$firma."<br>\n");
        //fwrite($handle, "comprueba=".$comprueba."<br>\n");
//$comprueba = $firma;
		$correoMi .=  "firma=".$firma."||<br>\n";
		$correoMi .=  "comprueba=".$comprueba."||<br>\n";

    }

} elseif ($pasarela == 2) { //Pasarela Sabadel
	$correoMi .= "Entra en pasarela Sabadel<br>\n";
    $d = $_REQUEST;

	$correoMi .= "Ds_Date {$d['Ds_Date']} | Ds_Hour {$d['Ds_Hour']} | Ds_Amount {$d['Ds_Amount']} | Ds_Currency {$d['Ds_Currency']} | Ds_Order {$d['Ds_Order']} |
				Ds_MerchantCode {$d['Ds_MerchantCode']} | Ds_Terminal {$d['Ds_Terminal']} | Ds_Signature {$d['Ds_Signature']} | Ds_Response {$d['Ds_Response']} |
				Ds_MerchantData {$d['Ds_MerchantData']} | Ds_SecurePayment {$d['Ds_SecurePayment']} | Ds_TransactionType {$d['Ds_TransactionType']} |
				Ds_Card_Country {$d['Ds_Card_Country']} | Ds_AuthorisationCode {$d['Ds_AuthorisationCode']} | Ds_ConsumerLanguage {$d['Ds_ConsumerLanguage']} |
				Ds_Card_Type {$d['Ds_Card_Type']} <br>\n";

    $respuesta = $d['Ds_Response'];
    $moneda = $d['Ds_Currency'];
    $idtrans = $d['Ds_Order'];
    $comercio = $d['Ds_MerchantCode'];
    $firma = $d['Ds_Signature'];
    $importe = $d['Ds_Amount'];
    $codautorizacion = $d['Ds_AuthorisationCode'];
    $coderror = (int)$respuesta;

    $query = "select tipoEntorno, pasarela from tbl_transacciones where idtransaccion = '$idtrans'";
    $temp->query($query);
	$correoMi .= "query= $query <br>\n";
    if ($temp->f('tipoEntorno') == 'P') {
		if ($temp->f('pasarela') == 10 ) {
			if ($moneda == '978') { $terminal = '4'; $clave='0B81Q46902U73925';}
			elseif ($moneda == '840') {$terminal = '6'; $clave='M0P7062T65014683';}
			elseif ($moneda == '826') {$terminal = '5'; $clave='0A4C407VP7792U93';}
		} else {
			if ($moneda == '978') $clave = 'shajklHJLKDSHlkhdlkh';
			elseif ($moneda == '840') $clave = 'rw6yerdsuhje5udjt654';
		}
    } else {
    	$clave = _SABADEL_CLAVE_DESA;
    }

    //fwrite($handle, ((int)$respuesta * 1)."<br>\n");
    //fwrite($handle, ($respuesta * 1)."<br>\n");
    //fwrite($handle, $espr."<br>\n");
    //fwrite($handle, (($espr*1) < 100)."<br>\n");

//    if (strlen($valor) > 1) $estado = 2; else $estado = 3;

    $comprueba = strtoupper(sha1($importe . $idtrans . $comercio . $moneda . $respuesta . $clave));
//	$comprueba = $firma;
	$correoMi .=  "firma=".$firma."<br>\n";
	$correoMi .=  "comprueba=".$comprueba."<br>\n";
	$correoMi .=  "respuesta=".$coderror."<br>\n";

    if ($coderror < 100) {
        $estado = '2';
        $importe = $d['Ds_Amount'];
        $codautorizacion = $d['Ds_AuthorisationCode'];
    } else {
        $estado = '3';
        $importe = null;
        $codautorizacion = null;
    }

    //fwrite($handle, "firma=".$firma."<br>\n");
    //fwrite($handle, "comprueba=".$comprueba."<br>\n");

} elseif ($pasarela == 4) { //Pasarela Banesto
	$correoMi .= "Entra en pasarela Banesto<br>\n";
    $d = $_REQUEST;
	
	if ($_SERVER['REMOTE_ADDR'] ) exit;
	$correoMi .= "result {$d['result']} | pszPurchorderNum {$d['pszPurchorderNum']} | pszTxnDate {$d['pszTxnDate']} | tipotrans {$d['tipotrans']} | store {$d['store']} |
				pszApprovalCode {$d['pszApprovalCode']} | pszTxnID {$d['pszTxnID']} | coderror {$d['coderror']} | deserror {$d['deserror']} | MAC {$d['MAC']} ";

    $respuesta = $d['result'];
    $idtrans = $d['pszPurchorderNum'];
    $comercio = $d['pszTxnDate'];
    $firma = $comprueba = 1;
    $codautorizacion = $d['pszApprovalCode'];
    $coderror = $d['coderror'];
    $deserror = $d['deserror'];
	$iderror = $coderror." ".$dserror;
	

$correoMi .=  "\n coderror=".$coderror;
$correoMi .=  "\n deserror=".$deserror;
$correoMi .=  "\n iderror=".$iderror;

	$coderror = null;

    $query = "select valor_inicial, tipoEntorno from tbl_transacciones where idtransaccion = '$idtrans'";
    $temp->query($query);
	$correoMi .= "\nquery= $query <br>\n";
	$importe = $temp->f('valor_inicial');

	$correoMi .=  "respuesta=".$respuesta."<br>\n";


    if ($respuesta == 0) {
        $estado = '2';
    } else {
        $estado = '3';
    }

    //fwrite($handle, "firma=".$firma."<br>\n");
    //fwrite($handle, "comprueba=".$comprueba."<br>\n");

}

$correoMi .= "$comprueba=$firma||<br>\n";
if ($comprueba == $firma) {
	if ($estado == '') $estado = '4';
    $firma = true;
$correoMi .=  "\nfirma=".$firma."||<br>\n";

	//Busca el id del error
	if ($coderror != '') {
		$iderror = null;
		$sql = "select id_error, texto from tbl_errores where codigo = '$coderror' and idpasarela = $pasarela";
		$correoMi .=  "<br>\n".$sql;
		$temp->setQuery($sql);
		$temp->query();
		$iderror = $temp->f("texto")." ".$dserror;
		$errorAMF = $temp->f("id_error");
	}
	
//	busca la conversion de moneda
	if ($moneda == '978') $cambioRate = 1;
	else {
		
		$query = "select valor from tbl_cambio c, tbl_moneda m  where m.moneda = c.moneda and m.idmoneda = '$moneda' and valor > 0 order by fecha desc limit 0,1";
		$correoMi .=  "<br>\n".$query;
		$temp->setQuery($query);
		$cambioRate = $temp->f('valor');
	}
	
$correoMi .=  "\ncambioRate=".$cambioRate;
$correoMi .=  "\niderror=".$iderror;
$correoMi .=  "\nestado=".$estado;
$correoMi .=  "\nerrorAMF=".$errorAMF;

//echo $correoMi;

    $query = "update tbl_transacciones set ";
    switch ($estado) {
        case '2': //Aceptada
            $estado = 'A';
            $query .= " codigo = '$codautorizacion', valor = $importe, id_error = null, ";
//            $query .= " tasa = $cambioRate, euroEquiv = ".(($importe / 100) / $cambioRate).", ";
			$texto = 'Aceptada';
            break;
        case '3': //Denegada
            $estado = 'D';
			$query .= " id_error = '$iderror', ";
			$texto = 'Denegada';
            break;
        case '4': //No Procesada
            $estado = 'N';
			$query .= " id_error = '$iderror', ";
			$texto = 'No Procesada';
            break;
        case '5': //No Procesada
            $estado = 'N';
			$query .= " id_error = '$iderror', ";
			$texto = 'No Procesada';
            break;
    }
	$query .= " estado = '$estado', fecha_mod = ".time()." where idtransaccion = '$idtrans'";
    //fwrite($handle, "<br>\n".$query);
	$correoMi .=  "<br>\n".$query;
    $temp->query($query);
	
	if ($estado == 'A') {
		if ($moneda == '840') {
			$monedaNom = "USD";
			$query = "update tbl_transacciones set tasa = (select valor from tbl_cambio where fecha < fecha_mod and moneda = 'USD' and valor > 0 order by fecha desc limit 0,1), 
						euroEquiv = (valor/100)/(select valor from tbl_cambio where fecha < fecha_mod and moneda = 'USD' and valor > 0 order by fecha desc limit 0,1) 
					where euroEquiv = 0 and estado in ('A','V','B') and moneda = 840 and fecha_mod > 1275368402";
		} elseif ($moneda == '826') {
			$monedaNom = "GBP";
			$query = "update tbl_transacciones set tasa = (select valor from tbl_cambio where fecha < fecha_mod and moneda = 'GBP' and valor > 0 order by fecha desc limit 0,1), 
						euroEquiv = (valor/100)/(select valor from tbl_cambio where fecha < fecha_mod and moneda = 'GBP' and valor > 0 order by fecha desc limit 0,1) 
					where euroEquiv = 0 and estado in ('A','V','B') and moneda = 826 and fecha_mod > 1275368402";
		} elseif ($moneda == '978') {
			$monedaNom = "EUR";
			$query = "update tbl_transacciones set tasa = 1, euroEquiv = (valor/100)/tasa 
						where euroEquiv = 0 and estado in ('A','V','B') and moneda = 978 and fecha_mod > 1275368402";
		}
		$correoMi .=  "<br>\n".$query;
		$temp->query($query);
	}

	if ($_SESSION['codProdReserv']) {
		if ($estado != 2) {
			$query = "select idProd, fechaIni, fechaFin, cant from tbl_productosReserv where codigo = '{$_SESSION['codProdReserv']}'";
			$temp->query($query);
			$prod = $temp->f('idProd');
			$fecha1 = $temp->f('fechaIni');
			$fecha2 = $temp->f('fechaFin');
			$cant = $temp->f('cant');
			$fecha = $fecha1;
			$cantCheq = $idCant = 0;
			$paso = false;

			while ($fecha <= $fecha2) {
				$query = "select id, cant from tbl_productosCant where idProd = ".$prod." and fechaIni <= $fecha and fechaFin >= $fecha";
	//			echo $query."<br>";
				$temp->query($query);
				$cantObt = $temp->f('cant');
				$idObt = $temp->f('id');
				if ($cantCheq == 0) $cantCheq = $cantObt;
	//			if ($idCant == 0) $cantCheq = $idObt;
				if ($cantCheq != $cantObt) {
	//				echo "$prod, $fecha1, ($fecha2-86400), ($cantCheq-$cant)<br>";
					if (insertaCantidad($prod,$fecha1,$fecha-86400,$cantCheq+$cant)) $paso = true;
					$fecha1 = $fecha;
					$cantCheq = $cantObt;
				}
				$fecha += 86400;
			}

		}
		$query = "delete from tbl_productosReserv where codigo = '{$_SESSION['codProdReserv']}'";
		$temp->query($query);
		$correoMi .=  "<br>\n".$query;
	}

	//Envï¿½o al sitio del cliente de la info de la transaccion
	//Lee los datos de la transacción
	$query = "select idtransaccion, t.idcomercio, identificador, codigo, idioma, fecha_mod, valor, moneda, t.estado, c.nombre, c.url, t.tipoEntorno,
				t.valor/100, c.url_llegada
			from tbl_transacciones t, tbl_comercio c
			where t.idcomercio = c.idcomercio
				and idtransaccion = '$idtrans'";
	$temp->query($query);
	$valores = $temp->loadRow();
	$correoMi .=  "<br>\n".$query;

	//Actualiza la tabla de las reservas con el resultado de la transaccion
	$query = "update tbl_reserva set id_transaccion = '".$valores[0]."', bankId = '".$valores[3]."', fechaPagada = ".$valores[5].",
					estado = '".$valores[8]."', est_comer = '".$valores[11]."', valor = ".$valores[12]."
				where codigo = '".$valores[2]."' and id_comercio = ".$valores[1];
//	echo $query;
	$temp->query($query);
	$correoMi .=  "<br>\n".$query."\n<br>\n";
	$q = "select id_reserva from tbl_reserva where id_transaccion = '$valores[0]'";
	$temp->query($q);
	
	if ( ($temp->num_rows() == 0) && (strlen($valores[13]) > 1) ) { //el pago es atravï¿½s de web y el sitio solicita envï¿½o directo de datos

		$firma = convierte($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));

		if (strlen($firma) > 2) {
			$correoMi .=  "firma={$valores[1]}, {$valores[2]}, {$valores[6]}, {$valores[7]}, {$valores[8]},	{$valores[0]}, ".date('d/m/y h:i:s', $valores[5])."<br>\n";
			
			$cadenaEnv = "?"."comercio=".$valores[1]."&transaccion=".$valores[2]."&importe=".$valores[6]."&moneda=".$valores[7]."&resultado=".$valores[8]."&codigo=".$valores[0]."&idioma=".$valores[4]."&firma=$firma&fecha=". urlencode(date('d/m/y h:i:s', $valores[5]))."&error=$errorAMF";
			$cadenaEnvia = $valores[13].$cadenaEnv;
			$correoMi .= $cadenaEnvia."<br>\n";

			$ch = curl_init($cadenaEnvia);

			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
			curl_exec($ch);
//			$output = curl_exec($ch);
			curl_close($ch);
			$correoMi .=  "respuCurl=$output||<br>\n";

			$ch = curl_init("https://www.concentradoramf.com/recgDatos.php".$cadenaEnv);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			$correoMi .=  "respuCurl=$output||<br>\n";

		}
	}
	
//	envío de correos y voucher este último en caso de pagos online Aceptados
	$send_m = new sendmail();
	$from = "tpv@caribbeanonlineweb.com";
	$q = "select nombre, email from tbl_admin where correoT = 1 and idcomercio = '$valores[1]'";
	$temp->query($q);
	while($temp->next_record()) {
		$arrayTo[] = array($temp->f('nombre'),$temp->f('email'));
	}
	
	$query = "select moneda from tbl_moneda where idmoneda = {$valores[7]}";
	$temp->query($query);
	$mon = $temp->f('moneda');

	$arrayTo[] = array('Julio Toirac', 'jotate@amfglobalitems.com');
	$subject = "Transacción realizada y $texto de ".$valores[9]." monto ".money_format('%.2n', ($valores[6]/100)) ." $mon";
	$message = "Estimado Cliente,<br><br> Se ha realizado una operación con los siguientes datos:<br>
		Comercio: ".$valores[1]." <br>
		Número de transaccion: ".$valores[0]." <br>
		Código entregado por el banco: ".$valores[3]."<br>
		Estado de la transacción: $texto <br>
		Fecha: ".date('d/m/y h:i:s', $valores[5])."<br>
		Valor: ".money_format('%.2n', ($valores[6]/100)) ." $mon";

$correoMi .= "\nCorreo Estado transaccion";
	foreach ($arrayTo as $todale) {
		$to = $todale[1];
$correoMi .= "\n$to";
		$headers  = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
		$headers .= 'To: '.$todale[0].'<'. $to . ">\n";
		$headers .= 'From: Administrador de Comercios Caribbean Online - '.$comercioN.' <'. $from . ">\n";

		$send_m->from($from);
		$send_m->to($to);
		$send_m->set_message($message);
		$send_m->set_subject($subject);
		$send_m->set_headers($headers);
		$enviado = $send_m->send();

if (_MOS_CONFIG_DEBUG) echo "mensaje$message<br>";
if (_MOS_CONFIG_DEBUG) echo "header$headers<br>";

	}

	//envío de voucher
	if ($valores[8] == 'A') {
		$q = "select nombre, email from tbl_reserva where id_transaccion = '$idtrans'";
//echo $q."<br>";
		$temp->query($q);
//echo "cant=".$temp->num_rows()."<br>";
		if($temp->num_rows() > 0){
			$ch = curl_init( _ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$contents = curl_exec($ch);
			curl_close($ch);

$correoMi .= "\nCorreo Voucher";

if (_MOS_CONFIG_DEBUG) echo "voucher=$contents<br>";
			$arrayTo = array();
			$arrayTo[] = array('Julio Toirac', 'jotate@amfglobalitems.com');
			$arrayTo[] = array($temp->f('nombre'), $temp->f('email'));
			$subject = "Voucher";
			foreach ($arrayTo as $todale) {
				$to = $todale[1];
	$correoMi .= "\n$to";
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
				$headers .= 'To: '.$todale[0].'<'. $to . ">\n";
				$headers .= 'From: Administrador de Comercios Caribbean Online - '.$comercioN.' <tpv@caribbeanonlineweb.com>'."\n";

				$send_m->from($from);
//				$send_m->to($to);
				$send_m->set_message($contents);
				$send_m->set_subject($subject);
				$send_m->set_headers($headers);
				$enviado = $send_m->send();
			}
		}
	}
}

//envío de sms
$sql = "select sms, telf from tbl_comercio c, tbl_transacciones t where t.idcomercio = c.idcomercio and t.idtransaccion = '$idtrans'";
$correoMi .= "\n".$sql."\n";
$temp->setQuery($sql);
$temp->query();
$sms = $temp->f("sms");
$telf = $temp->f("telf").',005352738723'; //borrar el número mío!!!!!!!!!!
$pag = 1;

$sql = "select count(*) total from tbl_reserva r, tbl_transacciones t where t.identificador = r.codigo and t.idtransaccion = '$idtrans' and pMomento = 'S'";
$temp->setQuery($sql);
$temp->query();
$correoMi .=  "<br>\n".$sql;
$tot = $temp->f('total');
$correoMi .=  "\nTotal=$tot";

if ($total == 1 && $temp->f('pMomento') == 'S') $pag = 0;
$correoMi .=  "\ntot=".$pag;

if ($sms == 1 && $tot != 0 &&  $texto == 'Aceptada') {
$correoMi = "\nEnviando SMS";
	$arrayDest = explode(',', $telf);
	$importe100 = $importe/100;
	$asunto = "Transacción No: $idtrans $texto valor:$importe100 $monedaNom";

	foreach($arrayDest as $destin) {
		$dms->mensajes->add(generaCodEmp(),$destin,$asunto, 'AMFAdmin');
	}

	#Enviar mensajes a plataforma
	$dms->send();

	#Verificar Resultado
	if ($dms->autentificacion->error){
		#Error de autentificacion con la plataforma
		$correoMi .=  "\nerror=".$dms->autentificacion->mensajeerror;

	}else{
		#Autentificacion correcta
		$correoMi .= "\nSaldo = ".$dms->autentificacion->saldo;
		$correoMi .= "\nMensajes = ".(count($dms->mensajes->get())-$dms->mensajes->errores);
		if ($dms->mensajes->errores>0){
			$correoMi .=  "\nMensajes con errores: ".$dms->mensajes->errores."<br>\n";
			$correoMi .=  "\nDetalles:<br>\n";
			foreach ($dms->mensajes->get() as $msg){
				if ($msg->error){
					$correoMi .=  "  - " . $msg->destino . "->" . $msg->mensajeerror . "<br>\n";
				}
			}
		}
	}
}
//echo str_replace("<br>\n", "<br>", $correoMi);
//fclose($handle);

correoAMi($subject,$correoMi);

//function lanzCorreo($correoMi) {
//	$temp = new ps_DB;
//	$headers = 'From: koldo@amfglobalitems.com' . "\r<br>\n" .
//		 'Reply-To: jotate@amfglobalitems.com' . "\r<br>\n" .
//		 'X-Mailer: PHP/' . phpversion();
//	$to      = 'jotate@amfglobalitems.com';
//	$subject = 'Salsa Resultado del banco background ';
////	$query = "insert into tbl_traza (titulo, traza, fecha) values ('$subject', '".htmlentities($correoMi, ENT_QUOTES)."', '".date('d/m/Y H:i:s')."')";
////	$correoMi .= "\n$query";
//	mail($to, $subject, $correoMi, $headers);
//	//$temp->query($query);
//	//echo $str;
//}
	

if (_MOS_CONFIG_DEBUG) {
	echo "<hr /><br>Datos:<br>";
	echo $database->_ticker . ' queries executed<br>';
 	foreach ($database->_log as $k=>$sql) {
 		echo $k+1 . "\n" . $sql . '<hr />';
	}
}

?>

