<?php

define( '_VALID_ENTRADA', 1 );
require_once( '../configuration.php' );
require_once( '../include/mysqli.php' );
require_once( '../include/hoteles.func.php' );
//include_once("../admin/classes/class_dms.php");
require_once( '../include/sendmail.php' );
require_once( '../include/correo.php' );
include_once("../admin/adminis.func.php");

$temp = new ps_DB;
//$dms=new dms_send;
$correo = new correo();

#Datos de acceso a la plataforma
//$dms->autentificacion->idcli='126560';
//$dms->autentificacion->username='amfglobalitems';
//$dms->autentificacion->passwd='Mario107';

/*********************************************************************************************************************/

$_REQUEST['Ds_Date'] = '08/10/2015';
$_REQUEST['Ds_Hour'] = '20:16';
$_REQUEST['Ds_SecurePayment'] = '0';
$_REQUEST['Ds_Amount'] = '47800';
$_REQUEST['Ds_Currency'] = '840';
$_REQUEST['Ds_Order'] = '151008200340';
$_REQUEST['Ds_MerchantCode'] = '126350073';
$_REQUEST['Ds_Terminal'] = '002';
$_REQUEST['Ds_Signature'] = 'B4EDB3965C9EA10894110447F66BEC7EC7261F46';
$_REQUEST['Ds_Response'] = '0913';
$_REQUEST['Ds_MerchantData'] = '';
$_REQUEST['Ds_TransactionType'] = '0';
$_REQUEST['Ds_ConsumerLanguage'] = '1';
$_REQUEST['Ds_ErrorCode'] = 'SIS0051';
$_REQUEST['Ds_AuthorisationCode'] = '';

/*********************************************************************************************************************/

$correoMi = "fecha=".date('d/m/Y H:i:s')."<br>\n";
$pasarela = null;
$dirIp = $_SERVER['REMOTE_ADDR'];
$correoMi .= $dirIp."<br>\n";
$titulo = "Llegada a llega";

if (count($_REQUEST) > 1) $d = $_REQUEST;
else $d = json_decode(file_get_contents('php://input'), true);

$lleg .= "Entra<br>\n";

foreach ($d as $key => $value) {
	$lleg .= $key." = ".$value."<br>\n";
}
$correoMi .= $lleg;
$q = "insert into tbl_traza (titulo,traza,fecha) values ('".$titulo." entrada datos','".html_entity_decode($correoMi)."',".time().")";
$temp->query($q);
//$correo->todo(13, 'ver otro', $correoMi);

//$handle = fopen("salsa.txt", "w");
//fwrite($handle, "INICIO<br>\n");
if ($d['peticion']){
    $salida = $d['peticion'];
    $pasarela = 1;
} elseif ($d['Ds_PanMask']) {
    $salida = $d['Ds_AuthorisationCode'].' / '.$d['Ds_Signature'];
    $pasarela = 3;
} elseif ($d['Ds_AuthorisationCode']) {
    $salida = $d['Ds_AuthorisationCode'].' / '.$d['Ds_Signature'];
    $pasarela = 2;
} elseif ($d['pszPurchorderNum']) {
	$pasarela = 4;
	$salida = $d['result'];
} elseif ($d['AcquirerBIN']) {
	$pasarela = 12;
} elseif ($d['BankDateTime']) {
	$pasarela = 24;
} elseif ($d['event']) {
	$pasarela = 40; //Pagantis
} elseif (isset($d['ResultCode'])) {
	$pasarela = 39; //Sipay
} else {
    if (!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/') && 
                !strstr($_SERVER['DOCUMENT_ROOT'], '/var/www/html') && 
                !strstr($_SERVER['DOCUMENT_ROOT'], '/home/julio/www') && 
                !strstr($_SERVER['DOCUMENT_ROOT'], '/wamp/www/')){
        $correoMi .= "<br>Pasarela inválida";
        $correo->todo(12,$titulo,$correoMi);
        $q = "insert into tbl_traza (titulo,traza,fecha) values ('$titulo','".html_entity_decode($correoMi)."',".time().")";
        $temp->query($q);
        exit;
    } else $pasarela = 1;
}

/*****************************Bórrame*****************************************************/
/*   $salida = "<tpv><respago><idterminal>999999</idterminal><idcomercio>B9550206800006</idcomercio><nombrecomercio>TRAVELS AND DISCOVERY</nombrecomercio>
				<idtransaccion>450108050011</idtransaccion><moneda>840</moneda><importe>83.64</importe><fechahora>17-03-2013 04:05:33</fechahora>
				<estado>2</estado><coderror>0000</coderror><codautorizacion>063132</codautorizacion><firma>E69F9B0DB8AE40467055B34FB7DD5B8E23975613</firma>
  				</respago></tpv>"; */
/*****************************Bórrame*****************************************************/

switch ($pasarela) {
    case 1:
		$va = "BBVA ó BBVA3D ó BBVA 3D onL";
	break;
	case 2:
		$va = "Sabadel, Caja Madrid, BBVA11 3D, BBVA12 3D, BBVA13 3D, BBVA14 3D, BBVA15 AMEX, Bankias";
	break;
	case 4:
		$va = "Banesto, Santander y Prueba";
	break;
	case 12:
		$va = "EVO";
	break;
	case 24:
		$va = "PayTpv";
	break;
	case 39:
		$va = "Sipay";
	break;
	case 40:
		$va = "Pagantis";
	break;
}
$correoMi .= "pasarela->".$va."||<br>\ndesde->".$_SERVER['HTTP_REFERER']."<br>\n";


$correoMi .= $salida."||<br>\n";

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
if ($pasarela == 1) { //pasarela BBVA, BBVA3D, BBVA 3D onL, BBVA3, BBVA4, BBVA9 3D y BBVA10 3D
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
//		echo (GetElementByName($d['peticion'], "<coderror>", "</coderror>"))."<br><br><br>";
		if (GetElementByName($d['peticion'], "<coderror>", "</coderror>")) $coderror = GetElementByName($d['peticion'], "<coderror>", "</coderror>");
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
        $query = "select c.estado, t.pasarela from tbl_comercio c, tbl_transacciones t where c.idcomercio = t.idcomercio and idtransaccion = '$idtrans'";
        $temp->query($query);
    //fwrite($handle, "query= $query <br>\n");
$correoMi .= "query= $query ||<br>\n";
        if ($temp->f('estado') == 'P') {
        	$clave = '';
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
			}elseif ($temp->f('pasarela') == '14') {
				$correoMi .=  $temp->f('pasarela')."||<br>\n" ;
				$clave = desofuscar(_3BBVAPALABR_OFUS, _3BBVACONTRASENA_OFUS, _3BBVAID_COMERCIO);
			} elseif ($temp->f('pasarela') == '15') {
				$correoMi .=  $temp->f('pasarela')."||<br>\n" ;
				$clave = desofuscar(_4BBVAPALABR_OFUS, _4BBVACONTRASENA_OFUS, _4BBVAID_COMERCIO);
			} elseif ($temp->f('pasarela') == '16') {
				$correoMi .=  $temp->f('pasarela')."||<br>\n" ;
				$clave = desofuscar(_5BBVAPALABR_OFUS, _5BBVACONTRASENA_OFUS, _5BBVAID_COMERCIO);
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

} elseif ($pasarela == 2) { //Pasarela Sabadel, caja madrid, caixa, BBVA11 3D, Bankia3, BBVA12 3D, BBVA13 3D, BBVA14 3D, BBVA15 AMEX Bankia
	$correoMi .= "Entra en pasarela Sabadel, caja madrid, caixa, BBVA11 3D, Bankia3, BBVA12 3D, BBVA13 3D, BBVA14 3D, BBVA15 AMEX, Bankia<br>\n";
//    $d = $_REQUEST;

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
		} elseif ($temp->f('pasarela') == 19 ) {
			if ($moneda == '978') { $terminal = '1'; $clave='qwertyasdf0123456789';}
			elseif ($moneda == '840') {$terminal = '2'; $clave='qwertyasdf0123456789';}
			elseif ($moneda == '826') {$terminal = '3'; $clave='qwertyasdf0123456789';}
		} elseif ($temp->f('pasarela') == 20 ) {
			if ($moneda == '978') { $terminal = '1'; $clave='ddk03gfhj9rf394nfd02';}
		} elseif ($temp->f('pasarela') == 21 ) {
			if ($moneda == '978') { $terminal = '2'; $clave='ddk03gfhj9rf394nfd02';}
		} elseif ($temp->f('pasarela') == 23 ) {
			if ($moneda == '978') { $terminal = '004'; $clave='qwertyasdf0123456789';}
		} elseif ($temp->f('pasarela') == 22 ) {
			if ($moneda == '978') {$terminal = '1'; $clave = 'qwertyasdf0123456789';}       //EUR
			elseif ($moneda == '840') {$terminal = '3'; $clave = 'lkjhyuiopm0123456789';}   //USD OK
			elseif ($moneda == '826') {$terminal = '4'; $clave = 'njiuhbvgyt0123456789';}   //GBP OK
			elseif ($moneda == '392') {$terminal = '5'; $clave = 'poiuyasdfg0123456789';}   //JPY OK
			elseif ($moneda == '124') {$terminal = '6'; $clave = 'qwertyasdf0123456789';}   //CAD
			elseif ($moneda == '152') {$terminal = '7'; $clave = 'qwertyasdf0123456789';}   //CLP
			elseif ($moneda == '32') {$terminal = '9'; $clave = 'qwertyasdf0123456789';}	//ARS
			elseif ($moneda == '356') {$terminal = '10'; $clave = 'qwertyasdf0123456789';}  //INR
			elseif ($moneda == '484') {$terminal = '11'; $clave = 'qwertyasdf0123456789';}  //MXN
			elseif ($moneda == '604') {$terminal = '12'; $clave = 'qwertyasdf0123456789';}  //PEN
			elseif ($moneda == '937') {$terminal = '13'; $clave = 'qwertyasdf0123456789';}  //VEF
			elseif ($moneda == '949') {$terminal = '14'; $clave = 'qwertyasdf0123456789';}  //TRY
			elseif ($moneda == '170') {$terminal = '15'; $clave = 'qwertyasdf0123456789';}  //COP
		} elseif ($temp->f('pasarela') == 25 ) {
			if ($moneda == '978') {$terminal = '1'; $clave = '580N12S4468871P3';}
		} elseif ($temp->f('pasarela') == 26 ) {
			if ($moneda == '978') {$terminal = '6'; $clave = 'dfcvsdxcde2145875236';}       //EUR OK
			elseif ($moneda == '840') {$terminal = '7'; $clave = 'dvbasxlmgb2145782365';}   //USD OK
			elseif ($moneda == '826') {$terminal = '8'; $clave = 'lpoklijkun2145203258';}   //GBP OK
			elseif ($moneda == '392') {$terminal = '9'; $clave = 'dfcvxcxsde2145235896';}   //JPY OK
			elseif ($moneda == '124') {$terminal = '10'; $clave = 'AZSXDCFVGB1010101010';}  //CAD OK
			elseif ($moneda == '32') {$terminal = '11'; $clave = 'AZSXDCFVGB1111111111';}   //ARS OK
			elseif ($moneda == '152') {$terminal = '12'; $clave = 'AZSXDCFVGB1212121212';}  //CLP OK
			elseif ($moneda == '170') {$terminal = '13'; $clave = 'AZSXDCFVGB1313131313';}  //COP OK
			elseif ($moneda == '356') {$terminal = '14'; $clave = 'AZSXDCFVGB1414141414';}  //INR OK
			elseif ($moneda == '484') {$terminal = '15'; $clave = 'AZSXDCFVGB1515151515';}  //MXN OK
			elseif ($moneda == '604') {$terminal = '16'; $clave = 'AZSXDCFVGB1616161616';}  //PEN OK
			elseif ($moneda == '937') {$terminal = '17'; $clave = 'AZSXDCFVGB1717171717';}  //VEF OK
			elseif ($moneda == '949') {$terminal = '18'; $clave = 'AZSXDCFVGB1818181818';}  //TRY OK
		} elseif ($temp->f('pasarela') == 27 ) {
			if ($moneda == '978') {$terminal = '6'; $clave = 'dfgvbgfrtg2145785235';}       //OK EUR
			elseif ($moneda == '840') {$terminal = '7'; $clave = 'slokmnbzxs2145785358';}   //OK USD
			elseif ($moneda == '826') {$terminal = '8'; $clave = 'dcvfdszwtg2147854258';}   //OK GBP
			elseif ($moneda == '392') {$terminal = '9'; $clave = 'uytrvcxwed2457896325';}   //OK JPY
			elseif ($moneda == '124') {$terminal = '10'; $clave = 'AZSXDCFVGB1010101010';}  //OK CAD
			elseif ($moneda == '32') {$terminal = '11'; $clave = 'AZSXDCFVGB1111111111';}   //OK ARS
			elseif ($moneda == '152') {$terminal = '12'; $clave = 'AZSXDCFVGB1212121212';}  //OK CLP
			elseif ($moneda == '170') {$terminal = '13'; $clave = 'AZSXDCFVGB1313131313';}  //OK COP
			elseif ($moneda == '356') {$terminal = '14'; $clave = 'AZSXDCFVGB1414141414';}  //OK INR
			elseif ($moneda == '484') {$terminal = '15'; $clave = 'AZSXDCFVGB1515151515';}  //OK MXN
			elseif ($moneda == '604') {$terminal = '16'; $clave = 'AZSXDCFVGB1616161616';}  //OK PEN
			elseif ($moneda == '937') {$terminal = '17'; $clave = 'AZSXDCFVGB1717171717';}  //OK VEF
			elseif ($moneda == '949') {$terminal = '18'; $clave = 'AZSXDCFVGB1818181818';}  //OK TRY
		} elseif ($temp->f('pasarela') == 28 ) {
			if ($moneda == '978') {$terminal = '4'; $clave = 'dscvfderdf2145785635';}
			elseif ($moneda == '840') {$terminal = '5'; $clave = 'vbghynsxcd2145368523';}
            elseif ($moneda == '826') {$terminal = '6'; $clave = 'AZSXDCFVGB6666666666';}
            elseif ($moneda == '392') {$terminal = '7'; $clave = 'AZSXDCFVGB7777777777';}
            elseif ($moneda == '32') {$terminal = '8'; $clave = 'AZSXDCFVGB8888888888';}
            elseif ($moneda == '124') {$terminal = '9'; $clave = 'AZSXDCFVGB9999999999';}
            elseif ($moneda == '152') {$terminal = '10'; $clave = 'AZSXDCFVGB1010101010';}
            elseif ($moneda == '170') {$terminal = '11'; $clave = 'AZSXDCFVGB1111111111';}
            elseif ($moneda == '356') {$terminal = '12'; $clave = 'AZSXDCFVGB1212121212';}
            elseif ($moneda == '484') {$terminal = '13'; $clave = 'azsxdcfvgb1313131313';}
            elseif ($moneda == '604') {$terminal = '14'; $clave = 'azsxdcfvgb1414141414';}
            elseif ($moneda == '937') {$terminal = '15'; $clave = 'azsxdcfvgb1515151515';}
            elseif ($moneda == '949') {$terminal = '17'; $clave = 'AZSXDCFVGB1717171717';}
		} elseif ($temp->f('pasarela') == 29 ) {
			if ($moneda == '978') {$terminal = '7'; $clave = 'qzhbmcxxlniu53048259';}
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
} elseif ($pasarela == 3) { //Pasarela TefPAy
	$correoMi .= "Entra en pasarela TefPAy<br>\n";
//    $d = $_REQUEST;

    $idtrans = substr($d['Ds_Merchant_MatchingData'], 0, 12);
    $idtransMod = $d['Ds_Merchant_MatchingData'];
    $comercio = $d['Ds_Merchant_MerchantCode'];
    $firma = $d['Ds_Signature'];
    $importe = $d['Ds_Amount'];
    $codautorizacion = $d['Ds_AuthorisationCode'];
	$tipoTr = $d['Ds_Merchant_TransactionType'];
	$ddate = $d['Ds_Date'];

//    $query = "select tipoEntorno, pasarela from tbl_transacciones where idtransaccion = '$idtrans'";
//    $temp->query($query);
//	$correoMi .= "query= $query <br>\n";
	
	$clave='5397355c219ed1.63436565';

    $comprueba = sha1($importe . $idtransMod . $codautorizacion . $tipoTr . $ddate . $clave);
//	$comprueba = $firma;
	$correoMi .=  "firma=".$firma."<br>\n";
	$correoMi .=  "comprueba=".$comprueba."<br>\n";
	$correoMi .=  "respuesta=".$coderror."<br>\n";

    if (strlen($codautorizacion) > 2) {
        $estado = '2';
        $importe = $d['Ds_Amount'];
        $codautorizacion = $d['Ds_AuthorisationCode'];
    } else {
        $estado = '3';
        $importe = null;
        $codautorizacion = null;
		$iderror = $d['Ds_Message'];
    }

    //fwrite($handle, "firma=".$firma."<br>\n");
    //fwrite($handle, "comprueba=".$comprueba."<br>\n");

} elseif ($pasarela == 4) { //Pasarela Banesto y Santander
	$correoMi .= "Entra en pasarela Banesto, Santander y la de prueba\n";
//    $d = $_REQUEST;
	
	if ($_SERVER['REMOTE_ADDR'] )
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

    $query = "select valor_inicial, tipoEntorno, moneda from tbl_transacciones where idtransaccion = '$idtrans'";
    $temp->query($query);
	$correoMi .= "\nquery= $query <br>\n";
	$importe = $temp->f('valor_inicial');
	$moneda = $temp->f('moneda');

	$correoMi .=  "respuesta=".$respuesta."\n";


    if ($respuesta == 0) {
        $estado = '2';
    } else {
        $estado = '3';
    }
	$correoMi .= "estado=$estado\n";

    //fwrite($handle, "firma=".$firma."<br>\n");
    //fwrite($handle, "comprueba=".$comprueba."<br>\n");

} elseif ($pasarela == 12) { //TPV EVO
	$correoMi .= "Entra en la pasarela del TPV EVO<br />\n";
//	$d = $_REQUEST;
	$comercio		= $d['MerchantID'];
	$AcquirerBIN	= $d['AcquirerBIN'];
	$TerminalID		= $d['TerminalID'];
	$idtrans		= $d['Num_operacion'];
	$importe		= $d['Importe'];
	$moneda			= $d['TipoMoneda'];
	$Exponente		= $d['Exponente'];
	$Referencia		= $d['Referencia'];
	$firma			= $d['Firma'];
	$codautorizacion= $d['Num_aut'];
	$Idioma			= $d['Idioma'];
	$Pais			= $d['Pais'];
	$Descripcion	= $d['Descripcion'];
	$clave			= _CLAVE_EVO;
	if ($d['Codigo_error']) {
		$coderror = $d['Codigo_error'];
		$estado = "3";
	} else $estado = "2";
	
	$correoMi .= "MerchantIdD $comercio | AcquirerBIN $AcquirerBIN | TerminalID $TerminalID | Num_operacion $idtrans | Importe $importe | TipoMoneda $moneda |".
					" Exponente $Exponente | Referencia $Referencia | Firma $firma | Num_aut $codautorizacion | Idioma $Idioma | Pais $Pais | Descripcion $Descripcion<br />\n";

	$comprueba = sha1($clave.$comercio.$AcquirerBIN.$TerminalID.$idtrans.$importe.$moneda.$Exponente.$Referencia);
	$impòrte = $importe*1;
	$correoMi .= $comprueba." / ".$firma."<br>\n";
	//$comprueba=$firma;
	echo '<HTML><HEAD><TITLE>Respuesta correcta a la comunicación ON-LINE</TITLE></HEAD><BODY>$*$OKY$*$</BODY></HTML>'; //Confirmando al TPV la llegada de la respuesta
} elseif ($pasarela == 24) { //PayTpv
	$correoMi .= "Entra en la pasarela de PayTpv<br />\n";
//	$d = $_REQUEST;
            
    if ($d['Response'] != 'KO') {
        $estado = '2';
        $importe = $d['Amount'];
        $codautorizacion = $d['AuthCode'];
    } else {
        $estado = '3';
        $importe = null;
        $codautorizacion = null;
    }
    
	$TerminalID		= $d['TpvID'];
	$idtrans		= $d['Order'];
	$moneda			= $d['Currency'];
	$firma			= $d['Signature'];
	$Idioma			= $d['Language'];
	$Pais			= $d['CardCountry'];
	$Descripcion	= $d['Concept'];
    $comercio       = $d['AccountCode'];
    $codautorizacion= $d['AuthCode'];
    $usercode 		= 'gDp0rFNXPs3fYydQT6zn';
    $terminal       = $d['TpvID'];
	
	$correoMi .= "MerchantIdD $comercio | AcquirerBIN $AcquirerBIN | TerminalID $TerminalID | Num_operacion $idtrans | Importe $importe | TipoMoneda $moneda |".
					" Referencia $Referencia | Firma $firma | Num_aut $codautorizacion | Idioma $Idioma | Pais $Pais | Descripcion $Descripcion<br />\n";

	$comprueba = md5($comercio . $usercode . $terminal . $idtrans . $importe . $moneda . md5('pCF2s3TVtmhHSgX6MyvN'));
	$impòrte = $importe*1;
	$correoMi .= $comprueba." / ".$firma."<br>\n";
} elseif($pasarela == 40) { //Pagantis
	if ($d['event'] != 'charge.failed') {
        $estado = '2';
        $importe = $d['data']['amount'];
        $codautorizacion = $d['data']['authorization_code'];
    } else {
        $estado = '3';
        $importe = null;
        $codautorizacion = null;
    }
	
	$q = "select idmoneda from tbl_moneda where moneda = '".$d['data']['currency'] ."'";
	$correoMi .= $q."<br>\n";
	$temp->query($q);
	$moneda			= $temp->f('idmoneda');
	
	$TerminalID		= $d['TpvID'];
	$idtrans		= $d['data']['order_id'];
	$firma			= $d['data']['Signature'];
	$Idioma			= $d['Language'];
	$Pais			= $d['CardCountry'];
	$Descripcion	= $d['Concept'];
    $comercio       = $d['AccountCode'];
    $usercode		= 'gDp0rFNXPs3fYydQT6zn';
    $terminal       = $d['TpvID'];
	
	$correoMi .= "MerchantIdD $comercio | AcquirerBIN $AcquirerBIN | TerminalID $TerminalID | Num_operacion $idtrans | Importe $importe | TipoMoneda $moneda |".
					" Referencia $Referencia | Firma $firma | Num_aut $codautorizacion | Idioma $Idioma | Pais $Pais | Descripcion $Descripcion<br />\n";

	$comprueba = md5($comercio . $usercode . $terminal . $idtrans . $importe . $moneda . md5('pCF2s3TVtmhHSgX6MyvN'));
	$impòrte = $importe*1;
	$correoMi .= $comprueba." / ".$firma."<br>\n";
} elseif ($pasarela == 39) { //Sipay
	$correoMi .= "Entra en Sipay<br>\n";
	if ($d['ResultCode'] == '0') {
		$estado = '2';
        $importe = ($d['Amount']*100);
        $codautorizacion = $d['ApprovalCode'];
    } else {
        $estado = '3';
        $importe = null;
        $codautorizacion = null;
	}
	$comprueba;
	$comercio;
	$firma;
	$moneda;
	$coderror;
	$idtrans = $d['TicketNumber'];
	
	$q = "update tbl_dataSipay set TransactionId = '{$d['TransactionId']}', SequenceNumber = '{$d['SequenceNumber']}', 
				ApprovalCode = '{$d['ApprovalCode']}', Authorizator = '{$d['Authorizator']}'
			where idtransaccion = '$idtrans'";
	$temp->query($q);
	$correoMi = $q."\n<br>";

	$comprueba = $firma = md5($comercio . $usercode . $terminal . $idtrans . $importe . $moneda . md5('pCF2s3TVtmhHSgX6MyvN'));
	$impòrte = $importe*1;
	$correoMi .= $comprueba." / ".$firma."<br>\n";
	
	if ($moneda * 1 > 1) {
		$q = "select idmoneda from tbl_moneda where moneda = '$mo'";
		$temp->query($q);
		$correoMi = $q."\n<br>";
		$moneda = $temp->f('idmoneda');
	}
}

$correoMi .= "$comprueba=$firma||<br>\n";

if ($comprueba == $firma||1==1) {
	
	if ($comprueba != $firma) {
		$correoMis =  "<br>\nNo concuerda la firma=".$firma."<br>\ncon la comprobación=$comprueba realizada<br>\npara la operación $idtrans<br>\n";
		$correoMi .= $correoMis;
		$correo->todo(13,"Fallo en firma de la operación",$correoMis);
	}
	
	if ($estado == '') $estado = '4';
    $firma = true;
$correoMi .=  "<br>\nfirma=".$firma."||<br>\n";

	//Busca el id del error
	if ($coderror != '') {
		if ($pasarela == 1 
				&& $pasarela == 3 
				&& $pasarela == 8 
				&& $pasarela == 9 
				&& $pasarela == 11 
				&& $pasarela == 14 
				&& $pasarela == 15 
				&& $pasarela == 17 
				&& $pasarela == 18
				) $psrl = 1;
		elseif ($pasarela == 2) $psrl = 2;
		elseif ($pasarela == 12) $psrl = 12;
		elseif ($pasarela == 4) $psrl = 13;
		elseif ($pasarela == 40) $psrl = 40;
		$iderror = $errorAMF = null;
		if ($psrl) {
			$sql = "select id_error, texto from tbl_errores where codigo = '$coderror' and idpasarela = $psrl";
			$correoMi .=  "<br>\n".$sql;
	// 		$temp->setQuery();
			$temp->query($sql);
			$iderror = $temp->f("texto")." ".$dserror;
			$errorAMF = $temp->f("id_error");
		}
		if ($estado == ' ' && $pasarela == 1) $estado = 3;
	}
	$correoMi .= "moneda=$moneda\n<br>";
	
//	busca la conversion de moneda
	if ($moneda == '840') $cambioRate = leeSetup ('USD');
	elseif ($moneda == '826') $cambioRate = leeSetup ('GBP');
	elseif ($moneda == '124') $cambioRate = leeSetup ('CAD');
	elseif ($moneda == '392') $cambioRate = leeSetup ('JPY');
	elseif ($moneda == '152') $cambioRate = leeSetup ('CLP');
	elseif ($moneda == '32') $cambioRate = leeSetup ('ARS');
	elseif ($moneda == '032') $cambioRate = leeSetup ('ARS');
	elseif ($moneda == '356') $cambioRate = leeSetup ('INR');
	elseif ($moneda == '484') $cambioRate = leeSetup ('MXN');
	elseif ($moneda == '604') $cambioRate = leeSetup ('PEN');
	elseif ($moneda == '937') $cambioRate = leeSetup ('VEF');
	elseif ($moneda == '949') $cambioRate = leeSetup ('TRY');
	elseif ($moneda == '170') $cambioRate = leeSetup ('COP');
	elseif ($moneda == 'USD') $cambioRate = leeSetup ('USD');
	elseif ($moneda == 'GBP') $cambioRate = leeSetup ('GBP');
	elseif ($moneda == 'CAD') $cambioRate = leeSetup ('CAD');
	elseif ($moneda == 'JPY') $cambioRate = leeSetup ('JPY');
	elseif ($moneda == 'CLP') $cambioRate = leeSetup ('CLP');
	elseif ($moneda == 'ARS') $cambioRate = leeSetup ('ARS');
	elseif ($moneda == 'ARS') $cambioRate = leeSetup ('ARS');
	elseif ($moneda == 'INR') $cambioRate = leeSetup ('INR');
	elseif ($moneda == 'MXN') $cambioRate = leeSetup ('MXN');
	elseif ($moneda == 'PEN') $cambioRate = leeSetup ('PEN');
	elseif ($moneda == 'VEF') $cambioRate = leeSetup ('VEF');
	elseif ($moneda == 'TRY') $cambioRate = leeSetup ('TRY');
	elseif ($moneda == 'COP') $cambioRate = leeSetup ('COP');
	else $cambioRate = 1;
	
$correoMi .=  "<br>\ncambioRate=".$cambioRate;
$correoMi .=  "<br>\niderror=".$iderror;
$correoMi .=  "<br>\nestado=".$estado;
$correoMi .=  "<br>\nerrorAMF=".$errorAMF;

//echo $correoMi;
	$est = "X";
	$q = "select estado, from_unixtime(fecha_mod,'%d/%m/%Y %H:%i:%s') fe from tbl_transacciones where idtransaccion = '$idtrans'";
	$correoMi .=  $q."<br>\n";
	$temp->query($q);
	$est = $temp->f('estado');
	$fe = $temp->f('fe');
	$q = "select count(idtransaccion) total from tbl_transacciones where idtransaccion = '$idtrans' and estado != 'P'";
	$correoMi .=  $q."<br>\n";
	$temp->query($q);
	$total = $temp->f('total');
    $salta = false;
    

    switch ($estado) {
        case '2': //Aceptada
            $estadoC = 'A';
            break;
        case '3': //Denegada
            $estadoC = 'D';
            break;
        case '4': //No Procesada
            $estadoC = 'N';
            break;
        case '5': //No Procesada
            $estadoC = 'N';
            break;
    }

    if ($est == "A" || $est == "B" || $est == "V") {
        $correoMi .= "La operación estaba con estado $est a las $fe y se volvió a recibir información del Banco como $estadoC, no se reliza ninguna acción en el Concentrador ni se envían datos a los ".
                "comercios<br>";
        $salta = true;
    } elseif ( 
            (($est == "N" || $est == "D") && ( $estado == '2')) ||
            ($est == "P")
            ) {
		$query = "update tbl_transacciones set ";
		switch ($estado) {
			case '2': //Aceptada
				$estado = 'A';
				$query .= " codigo = '$codautorizacion', valor = $importe, id_error = null, tasa = ".$cambioRate.", euroEquiv = ($importe/100)/($cambioRate), ";
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
        
        $q = "select fecha_mod, tipoEntorno, valor/100 val, idcomercio, identificador from tbl_transacciones where idtransaccion = '$idtrans'";
        $temp->query($q);
        
        //Actualiza la tabla de las reservas con el resultado de la transaccion
        $query = "update tbl_reserva set id_transaccion = '".$idtrans."', bankId = '".$codautorizacion."', fechaPagada = ".$temp->f('fecha_mod').",
                        estado = '".$estado."', est_comer = '".$temp->f('tipoEntorno')."', valor = ".$temp->f('val')."
                    where codigo = '".$temp->f('identificador')."' and id_comercio = ".$temp->f('idcomercio');
    //	echo $query;
        $temp->query($query);
        $correoMi .=  "<br>\n".$query."\n<br><br>\n";
	}
    
    //Aviso de transacción duplicada desde el Banco
    if ($est == "N" || $est == "D" || $est == "A" || $est == "B" || $est == "V") {
        $lab = 'Recibido datos duplicados desde el banco';
        $mes = "fecha=".date('d/m/Y H:i:s')."<br>\n"."Se ha recibido duplicado los datos de la transacción $idtrans. La misma estaba en la base de datos con el estado $est el $fec ".
                "y se recibió con estado $estadoC";
        $correo->todo (20, $lab, $mes);
        
        $correoMi .= $mes."<br>\n";
		
		if ($est == 'A' && $estadoC == 'A') {
			$lab = 'Recibida transacción Aceptada duplicada desde el banco';
			$mes = "fecha=".date('d/m/Y H:i:s')."<br>\n"."Se han recibido los siguentes datos de la transacción $idtrans:<br>\n $lleg ";
			$correo->todo (20, $lab, $mes);

			$correoMi .= $mes."<br>\n";
		}
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

	//Envío al sitio del cliente de la info de la transacción
	//Lee los datos de la transacción
	$query = "select idtransaccion, t.idcomercio, identificador, codigo, idioma, fecha_mod, valor, moneda, t.estado, c.nombre, 
				c.url, t.tipoEntorno, t.valor/100, c.url_llegada, p.nombre, p.comercio
			from tbl_transacciones t, tbl_comercio c, tbl_pasarela p
			where t.idcomercio = c.idcomercio
                and p.idPasarela = t.pasarela
				and idtransaccion = '$idtrans'";
	$temp->query($query);
	$valores = $temp->loadRow();
	$comercioN = $valores[9];
	$correoMi .=  "<br>\n".$query;

	$q = "select id_reserva from tbl_reserva where id_transaccion = '$valores[0]'";
	$temp->query($q);
	
	if ( ($temp->num_rows() == 0) && (strlen($valores[13]) > 1) && $salta == false ) { //el pago es através de web y el sitio solicita envío directo de datos

		$firma = convierte($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));

		if (strlen($firma) > 2) {
			$correoMi .=  "<br>firma={$valores[1]}, {$valores[2]}, {$valores[6]}, {$valores[7]}, {$valores[8]},	{$valores[0]}, ".
								date('d/m/y h:i:s', $valores[5])."<br>\n";
			if ($valores[1] != '140778652871' && $valores[1] != '140784511377') {//para todos los comercios excepto el de Cubana nuevo y el Saratoga
				
				$cadenaEnv = "?"."comercio=".$valores[1]."&transaccion=".$valores[2]."&importe=".$valores[6].
							"&moneda=".$valores[7]."&resultado=".$valores[8]."&codigo=".$valores[0]."&idioma=".$valores[4].
							"&firma=$firma&fecha=". urlencode(date('d/m/y h:i:s', $valores[5]))."&error=$errorAMF&tasa=$cambioRate".
							"&comerc=".$valores[15];
				$cadenaEnvia = $valores[13].$cadenaEnv;
				$correoMi .= $cadenaEnvia."<br>\n";
	
				$ch = curl_init($cadenaEnvia);
	
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_POST, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$output = curl_exec($ch);
	            $curl_info = curl_getinfo($ch);
	// 						echo "error=".curl_errno($ch);
				if (curl_errno($ch)) $correoMi .=  "Error en la comunicación al comercio:".curl_strerror(curl_errno($ch))."<br>\n";
				$crlerror = curl_error($ch);
	// 						echo "otroerror=".$crlerror;
				if ($crlerror) {
					$correoMi .=  "La comunicación al comercio ha dado error:".$crlerror."<br>\n";
				}
				curl_close($ch);
	
	//			$ch = curl_init("https://www.concentradoramf.com/recgDatos.php".$cadenaEnv);
	//			curl_setopt($ch, CURLOPT_HEADER, false);
	//			curl_setopt($ch, CURLOPT_POST, false);
	//			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//			$output = curl_exec($ch);
	//			curl_close($ch);
	//			$correoMi .=  "respuCurl=".str_replace('<script', '<scr|ipt', $output)."||<br>\n";
				
			} else {
				$correoMi .= "Funcionalidad de Cubana y Saratoga<br>\n";
				$data = array(
						"comercio"		=> $valores[1],
						"transaccion"	=> $valores[2],
						"importe"		=> $valores[6],
						"moneda"		=> $valores[7],
						"resultado"		=> $valores[8],
						"codigo"		=> $valores[0],
						"idioma"		=> $valores[4],
						"firma"			=> $firma,
						"fecha"			=> urlencode(date('d/m/y h:i:s', $valores[5])),
						"error"			=> $errorAMF,
						"comerc"		=> $valores[15]
								);
				$options = array(
						CURLOPT_RETURNTRANSFER	=> true,
						CURLOPT_SSL_VERIFYPEER	=> false,
						CURLOPT_POST			=> true,
						CURLOPT_VERBOSE			=> true,
						CURLOPT_URL				=> $valores[13],
						CURLOPT_POSTFIELDS		=> $data
				);
				$correoMi .= "<br><br>
					CURLOPT_RETURNTRANSFER	=> true,<br>
					CURLOPT_SSL_VERIFYPEER	=> false,<br>
					CURLOPT_POST			=> true,<br>
					CURLOPT_VERBOSE			=> true,<br>
					CURLOPT_URL				=> ".$valores[13].",
					CURLOPT_POSTFIELDS		=> ".http_build_query($data)."<br>";
				$ch = curl_init();
				curl_setopt_array($ch , $options);
				$output = curl_exec($ch);
	// 						echo "error=".curl_errno($ch);
				if (curl_errno($ch)) $correoMi .=  "Error en la comunicación al comercio:".curl_strerror(curl_errno($ch))."<br>\n";
				$crlerror = curl_error($ch);
	// 						echo "otroerror=".$crlerror;
				if ($crlerror) {
					$correoMi .=  "La comunicación al comercio ha dado error:".$crlerror."<br>\n";
				}
				$curl_info = curl_getinfo($ch);
				curl_close($ch);
				
			}
	            
	            foreach ($curl_info as $key => $value) {
	                $correoMi .=  $key." = ".$value."<br>\n";
	            }
				$correoMi .=  "respuCurl=$output||<br>\n";
		}
	}
	
    //Funcionalidad de Cubana
	if ( 
            $salta == false && (
//			$valores[1] == '122327460662' //Comentar esta línea para deshabilitar Prueba
//			|| //Descomentar esta lnea para usar los dos comercios
			$valores[1] == '129025985109'	//Descomentar esta línea para habilitar Cubana
			)) {
		$correoMi .=  "<br>\nEntra en la parte del chuchuchú <br>\n";
		$vale = number_format(($valores[6]/100), 2);
		$correoMi .= $valores[2].'.'.$valores[0].'.'.$vale.'.'.$valores[7].'.'.$valores[8].'.'.$valores[1]."<br>\n";
		
		$fr=md5($valores[2].$valores[0].$vale.$valores[7].$valores[8].$valores[1]);
        
        //Actualiza la tabla amadeus con los datos de la transacción
        $q = "update tbl_amadeus set idtransaccion = '".$valores[0]."', estado = '".$valores[8]."', fechamod = ".time().", codigo = '".$valores[3]
                ."' where idcomercio = '".$valores[1]."' and rl = '".$valores[2]."'";
        $correoMi .= "<br>\n". $q;
        $temp->query($q);
		
        //busca en la tabla amadeus los datos para enviar de vuelta a amadeus
        $q = "select urlko url, ";
        if ($valores[8] == 'A') $q = "select urlok url, ";
        $q .= " sesion, enc from tbl_amadeus  where idcomercio = '{$valores[1]}' and rl = '{$valores[2]}'";
        $correoMi .= "<br>\n". $q;
        $temp->query($q);
        $urlEnv = $temp->f('url')."&FINAL_CONF=FALSE";
		$enc = $temp->f('enc');
        $sessionid = $temp->f('sesion');

        $encTime = date('YmdHis',time());
        $arrMerc = array('129025985109' => 'AAWOAAWO', '122327460662' => 'ADMPADMP'); //array para site
        $arrClave = array('129025985109' => 'fgrt34sdsw2', '122327460662' => 'fgrt34sdsw2');

        $url = _ESTA_URL."/cubanaLand.php?fac=".$valores[2]."&com=".$valores[1];
        
        if ($enc == 'HMACSHA512') {
        	$calmd5 = hash_hmac('sha512',$sessionid.$valores[0].urlencode($url),$arrClave[$valores[1]]);
        	$correoMi .= "<br>\n hash_hmac('sha512',$sessionid.$valores[0].urlencode($url),{$arrClave[$valores[1]]})";
        } else {
	        $calmd5 = strtoupper(md5($sessionid.$valores[0].urlencode($url).$arrClave[$valores[1]]));
	        $correoMi .= "<br>\n md5($sessionid.$valores[0].urlencode($url).{$arrClave[$valores[1]]})";
        }


        $correoMi .= "<br>\nurlEnv=$urlEnv";
//        $correoMi .= "<br>\nSITE=".$arrMerc[$valores[1]]; 
//        $correoMi .= "<br>\nENC_TYPE=1"; 
//        $correoMi .= "<br>\nENC="; 
//        $correoMi .= "<br>\nENC_TIME=".$encTime; 
//        $correoMi .= "<br>\nPAYMENT_REFERENCE=".$valores[0];
//        $correoMi .= "<br>\nACKNOWLEDGEMENT_URL=".$url; 
//        $correoMi .= "<br>\nCHECKSUM=".$calmd5;
//        $correoMi .= "<br>\nAPPROVAL_CODE=".$valores[3]; 
//        $correoMi .= "<br>\nCANCELLATION_URL="._ESTA_URL."/amadeus/cancel.php"; 

        $correoMi .=  "<br>\nEnvío del formulario por curl a Amadeus<br>\n";
        //Envío del formulario por Cdonts a Amadeus
        $data = array(
            "SITE"=>$arrMerc[$valores[1]],
            "ENC_TYPE"=>'1',
            "ENC"=>"",
            "ENC_TIME"=>$encTime,
            "PAYMENT_REFERENCE"=>$valores[0],
            "ACKNOWLEDGEMENT_URL"=>$url,
            "CHECKSUM"=>$calmd5,
            "APPROVAL_CODE"=>$valores[3],
            "CANCELLATION_URL"=>_ESTA_URL."/amadeus/cancel.php"
        );
        
        foreach ($data as $key => $value) {
            $correoMi .=  "<br>\n$key = $value";
        }

//        $urlEnv = _ESTA_URL."/cubanaLand.php"; //Comentar esta línea para dejar el desarrollo normal
        $correoMi .=  $urlEnv."<br>\n";
        $salidaCurl = '';$i = 1;
        $ch = curl_init($urlEnv);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        while (strlen($salidaCurl) == 0 && $i < 4) {
            $salidaCurl = curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            
            foreach ($curl_info as $key => $value) {
                $correoMi .=  $key." = ".$value."<br>\n";
            }
            
            if(strlen(curl_error($ch))) $correoMi .= "Curl error: ".curl_error($ch)."<br>\n";
            
            $correoMi .= "Enviado a Amadeus por detrás, envío $i - ".$salidaCurl."<br>\n";
            $i++;
        }
        curl_close($ch);
		
	}
	
//	envío de correos y voucher este último en caso de pagos online Aceptados
	$q = "select nombre, email from tbl_admin where correoT = 1 and idcomercio = '$valores[1]' and activo = 'S'";
	$q = "select email from tbl_admin a, tbl_colAdminComer c, tbl_comercio e where c.idAdmin = a.idadmin 
			and e.id = c.idComerc and e.idcomercio = '$valores[1]' and a.activo = 'S' and a.correoT = 1";
	$temp->query($q);
	$correoMi .= "<br>\n".$q."<br>\n";
//	$arrayTo[] = $temp->loadRowList();
	$cc = false;
	while($temp->next_record()) {
        if(!strlen($correo->to)) $correo->to($temp->f('email'));
        else {
        	if (!$cc) $cc = $temp->f('email');
        	else $cc .= ",".$temp->f('email');
        }
	}
	if ($cc) $correo->add_headers ("Cc: ".$cc);
	
	$query = "select moneda from tbl_moneda where idmoneda = {$valores[7]}";
	$temp->query($query);
	$mon = $temp->f('moneda');

    if ( $salta == false ) {
        $subject = "Transacción realizada y $texto de ".$comercioN." monto ".number_format(($valores[6]/100),2,'.',' ') ." $mon";
        $message = "Estimado Cliente,<br><br> Se ha realizado una operación con los siguientes datos:<br>
            Comercio: ".$valores[1]." <br>
            Número de transaccion: ".$valores[0]." <br>
            Código entregado por el banco: ".$valores[3]."<br>
            Estado de la transacción: $texto <br>
            Fecha: ".date('d/m/y h:i:s', $valores[5])."<br>
            Valor: ".number_format(($valores[6]/100),2,'.',' ') .$mon;

    	$correoMi .= "<br>\nCorreo Estado transaccion";
        $correo->todo(14, $subject, $message);
        $correo->destroy();

        //envío de voucher
        if ($valores[8] == 'A') {
            $q = "select nombre, email from tbl_reserva where id_transaccion = '$idtrans'";
    //echo $q."<br>";
            $temp->query($q);
    //echo "cant=".$temp->num_rows()."<br>";
            if($temp->num_rows() > 0){
    if (_MOS_CONFIG_DEBUG) echo _ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]."<br>";
    $correoMi .= "<br>\ncurl_init("._ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1].")";
                $ch = curl_init( _ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $contents = curl_exec($ch);
                curl_close($ch);

    $correoMi .= "<br>\nCorreo Voucher<br>\n".$contents;
    if (_MOS_CONFIG_DEBUG) echo "voucher=$contents<br>";

                if (strpos($temp->f('email'), ' - ') !== false){
                    $arrCor = explode(" - ", $temp->f('email'));
                    $corr = $arrCor[0];
    $correoMi .= "<br>\ncorr=$corr";
                } else $corr = $temp->f('email');
                $arrayTo[] = array($temp->f('nombre'),$corr);
    // 			$arrayTo[] = array($temp->f('nombre'), $temp->f('email'));
                foreach ($arrayTo as $todale) {
                    if (!strlen($correo->to))
                        $correo->to = $todale[1];
                    else 
                        $correo->set_headers ("Cc: ".$todale[1]);
                }
                $subject = "Voucher";

                $correo->todo(15, $subject, $contents);
                $correo->destroy();
            }
        }
	}
}

//envío de sms
$sql = "select sms, telf from tbl_comercio c, tbl_transacciones t where t.idcomercio = c.idcomercio and t.idtransaccion = '$idtrans'";
$correoMi .= "<br>\n".$sql."<br>\n";
$temp->setQuery($sql);
$temp->query();
$sms = $temp->f("sms");
$telf = $temp->f("telf"); //borrar el número mío!!!!!!!!!!
$pag = 1;

$sql = "select count(*) total from tbl_reserva r, tbl_transacciones t where t.identificador = r.codigo and t.idtransaccion = '$idtrans' and pMomento = 'S'";
$temp->setQuery($sql);
$temp->query();
$correoMi .=  "<br><br>\n".$sql;
$tot = $temp->f('total');
$correoMi .=  "<br>\nTotal=$tot";

if ($total == 1 && $temp->f('pMomento') == 'S') $pag = 0;
$correoMi .=  "<br>\ntot=".$pag;

if ($sms == 1 && $tot != 0 &&  $texto == 'Aceptada') {
$correoMi = "<br>\nEnviando SMS";
	$arrayDest = explode(',', $telf);
	$importe100 = $importe/100;
	$asunto = "Transacción No: $idtrans $texto valor:$importe100 $monedaNom";
}

if (_MOS_CONFIG_DEBUG) echo $correoMi;
$correo->todo(13,$titulo,str_replace('Flight', 'Flht', str_replace('flight', 'flht', str_replace('vuelo', 'vulo', str_replace('Vuelo', 'Vulo', $correoMi)))));
$q = "insert into tbl_traza (titulo,traza,fecha) values ('$titulo','".html_entity_decode($correoMi)."',".time().")";
// $temp->query($q);
	

if (_MOS_CONFIG_DEBUG) {
	echo "<hr /><br>Datos:<br>";
	echo $database->_ticker . ' queries executed<br>';
 	foreach ($database->_log as $k=>$sql) {
 		echo $k+1 . "<br>\n" . $sql . '<hr />';
	}
}

if ($pasarela == 39) { //Sipay
	$_REQUEST['resp'] = $d['TicketNumber'];
	include 'index.php';
}

?>

