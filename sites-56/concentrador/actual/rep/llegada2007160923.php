<?php

define( '_VALID_ENTRADA', 1 );
require_once( '../configuration.php' );
require_once( '../include/mysqli.php' );
require_once( '../include/hoteles.func.php' );
//include_once("../admin/classes/class_dms.php");
require_once( '../include/sendmail.php' );
require_once( '../include/correo.php' );
include_once("../admin/adminis.func.php");
require_once( '../include/apiRedsys.php' );
require_once( '../admin/classes/entrada.php' );

$miObj = new RedsysAPI;
$temp = new ps_DB;
//$dms=new dms_send;
$correo = new correo();
$ent = new entrada;

#Datos de acceso a la plataforma
//$dms->autentificacion->idcli='126560';
//$dms->autentificacion->username='amfglobalitems';
//$dms->autentificacion->passwd='Mario107';

/*********************************************************************************************************************/
if (stripos(_ESTA_URL, 'localhost') > 0) {
//	$_REQUEST['idTpe'] = 'HOM-061-170';
//	$_REQUEST['idTransaction'] = '190806144927';
//	$_REQUEST['montant'] = '2.00';
//	$_REQUEST['result'] = 'OK';
//	$_REQUEST['data'] = '190806144927';
//	$_REQUEST['sec'] = 'FAD14F61FC68F3A3D196D95925F579C3092781D322DEAEA16BB085A51FA91C979459251E09DE33D6C208F37990DF14DB8FB13ABCB0652F7F64723FE0A802DF0F';
// 	$_REQUEST['Response'] = 'OK';
// 	$_REQUEST['ErrorID'] = '0';
// 	$_REQUEST['ErrorDescription'] = 'Sin error';
// 	$_REQUEST['AuthCode'] = '259922/291505949362751650696507262175';
// 	$_REQUEST['Currency'] = 'EUR';
// 	$_REQUEST['Amount'] = '500';
// 	$_REQUEST['AmountEur'] = '5';
// 	$_REQUEST['Language'] = 'es';
// 	$_REQUEST['AccountCode'] = 'knwb61pq';
// 	$_REQUEST['TpvID'] = '5330';
// 	$_REQUEST['Concept'] = '';
// 	$_REQUEST['ExtendedSignature'] = '2e3330e1d2c48d7bf08d5fc12104c7c1';
// 	$_REQUEST['IdUser'] = '6059054';
// 	$_REQUEST['TokenUser'] = 'ZG5CeWExcHFOU2h';
// 	$_REQUEST['SecurePayment'] = '1';
// 	$_REQUEST['CardBrand'] = 'DISCOVER';
// 	$_REQUEST['BicCode'] = 'PAYTPVMMXXX';
	
//$_REQUEST['Ds_Amount'] = '23760';
//$_REQUEST['Ds_Date'] = '171116221123';
//$_REQUEST['Ds_AuthorisationCode'] = '0';
//$_REQUEST['Ds_Bank'] = '2038';
//$_REQUEST['Ds_Message'] = 'Operacion denegada';
//$_REQUEST['Ds_Code'] = '201';
//$_REQUEST['Ds_CodeBank'] = '0190';
//$_REQUEST['Ds_Merchant_MatchingData'] = '171116221050000000000';
//$_REQUEST['Ds_Merchant_TransactionType'] = '22';
//$_REQUEST['Ds_PanMask'] = '0005';
//$_REQUEST['Ds_Expiry'] = '1805';
//$_REQUEST['Ds_Merchant_Guarantees'] = '0';
//$_REQUEST['Ds_Signature'] = '206468d41e317ff572ca84e43176ad920226f519';
//$_REQUEST['Ds_Merchant_MerchantCode'] = '126791813';
//$_REQUEST['Ds_CostumerCreditCardBin'] = '553397';
//$_REQUEST['Ds_CostumerCreditCardBrand'] = 'MASTERCARD';
//$_REQUEST['Ds_CostumerCreditCardOrganization'] = 'OPTAL FINANCIAL, LTD.';
//$_REQUEST['Ds_CostumerCreditCardType'] = 'CREDIT';
//$_REQUEST['Ds_CostumerCreditCardCategory'] = 'BUSINESS';
//$_REQUEST['Ds_CostumerCreditCardCountry'] = 'UNITED KINGDOM';
//$_REQUEST['Ds_CostumerCreditCardCountryCode2'] = 'GB';
//$_REQUEST['Ds_CostumerCreditCardCountryCode'] = 'GBR';
//$_REQUEST['Ds_CostumerCreditCardCountryNumber'] = '826';
//$_REQUEST['Ds_CostumerCreditCardOrganizationWWW'] = '';
//$_REQUEST['Ds_CostumerCreditCardOrganizationPhone'] = '';
	
// $_REQUEST['Ds_Date'] = '171121032119';
// $_REQUEST['Ds_Merchant_MatchingData'] = '171121031953000000000';
// $_REQUEST['Ds_PanMask'] = '2011';
// $_REQUEST['Ds_Merchant_TransactionType'] = '46';
// $_REQUEST['Ds_Merchant_MerchantCode'] = '160324919';
// $_REQUEST['Ds_Merchant_Amount'] = '23000';
// $_REQUEST['Ds_Code'] = '700';
// $_REQUEST['Ds_Merchant_ClientId'] = '399147';
// $_REQUEST['Ds_Merchant_BeneficiaryId'] = '1132572';
// $_REQUEST['Titanes_OrderId'] = '5470635';
// $_REQUEST['Titanes_OrderStatusCode'] = '3';
// $_REQUEST['Titanes_OrderStatus'] = 'Available';
// $_REQUEST['Titanes_Description'] = 'Money has been received.';
// $_REQUEST['Ds_Signature'] = '';
// $_REQUEST['Titanes_OrderCode'] = '3';

// $_REQUEST['amount'] = '8';
// $_REQUEST['currency'] = '840';
// $_REQUEST['paymentType'] = 'CCARD';
// $_REQUEST['financialInstitution'] = 'MC';
// $_REQUEST['language'] = 'en';
// $_REQUEST['orderNumber'] = '1398119';
// $_REQUEST['paymentState'] = 'SUCCESS';
// $_REQUEST['shopname_customParameter1'] = '160714231513';
// $_REQUEST['shopname_customParameter2'] = 'shopname_customParameter2';
// $_REQUEST['authenticated'] = 'Yes';
// $_REQUEST['anonymousPan'] = '0001';
// $_REQUEST['expiry'] = '12/2016';
// $_REQUEST['cardholder'] = 'john Doe';
// $_REQUEST['maskedPan'] = '950000******0001';
// $_REQUEST['gatewayReferenceNumber'] = 'DGW_1398119_RN';
// $_REQUEST['gatewayContractNumber'] = 'DemoContractNumber123';
// $_REQUEST['avsResponseCode'] = 'X';
// $_REQUEST['avsResponseMessage'] = 'Demo AVS ResultMessage';
// $_REQUEST['avsProviderResultCode'] = 'X';
// $_REQUEST['avsProviderResultMessage'] = 'Demo AVS ProviderResultMessage';
// $_REQUEST['responseFingerprintOrder'] = 'amount,currency,paymentType,financialInstitution,language,orderNumber,paymentState,shopname_customParameter1,shopname_customParameter2,authenticated,anonymousPan,expiry,cardholder,maskedPan,gatewayReferenceNumber,gatewayContractNumber,avsResponseCode,avsResponseMessage,avsProviderResultCode,avsProviderResultMessage,secret,responseFingerprintOrder';
// $_REQUEST['responseFingerprint'] = 'c986f700842f3808e9f6464b8edf7b391b20a8e3e8d84d771c9dbe84d3da92a7df1301a1b45550db0e4ad1e7f628afd419c3c9df4ec4e13b0e79622d81efc6bc';
}
/*********************************************************************************************************************/

$correoMi = "fecha=".date('d/m/Y H:i:s')."<br>\n";
$pasarela = null;
$dirIp = $_SERVER['REMOTE_ADDR'];
$correoMi .= $dirIp."<br>\n";
$titulo = "Llegada a llega";
$ok = 0;
$iderror = $errorAMF = null;
$paseEst = "'P', 'D', 'N'";

if (count($_REQUEST) > 1) $d = $_REQUEST;
else {
	$d = json_decode(file_get_contents('php://input'), true);
	error_log("recibe la respuesta acá");
	error_log(file_get_contents('php://input'));
}


$lleg .= "Entra<br>\n";

foreach ($d as $key => $value) {
	$lleg .= $key." = ".$value."<br>\n";
}
$correoMi .= $lleg;
$q = "insert into tbl_traza (titulo,traza,fecha) values ('".$titulo." entrada datos','".html_entity_decode($correoMi, ENT_QUOTES)."',".time().")";
$temp->query($q);
//$correo->todo(13, 'ver otro', $correoMi);

// if (isset($d['Ds_TransactionType'])) $echo = 'hola';

//$handle = fopen("salsa.txt", "w");
//fwrite($handle, "INICIO<br>\n");
if ($d['peticion']){
    $salida = $d['peticion'];
    $pasarela = 1;
} elseif ($d['Ds_Merchant_MatchingData']) {
    $salida = $d['Ds_AuthorisationCode'].' / '.$d['Ds_Signature'];
    $pasarela = 3; //TefPay
} elseif ($d['Ds_SignatureVersion']) {
    $salida = $d['Ds_AuthorisationCode'].' / '.$d['Ds_Signature'];
    $pasarela = 60; //Redsys
} elseif (isset($d['Ds_AuthorisationCode'])) {
    $salida = $d['Ds_AuthorisationCode'].' / '.$d['Ds_Signature'];
    $pasarela = 2;
} elseif ($d['pszPurchorderNum']) {
	$pasarela = 4;
	$salida = $d['result'];
} elseif ($d['AcquirerBIN']) {
	$pasarela = 12; //EVO
} elseif ($d['BankDateTime']) {
	$pasarela = 71; //PayTpv nuevo
} elseif ($d['event']) {
	$pasarela = 40; //Pagantis
} elseif (isset($d['ResultCode'])) {
	$pasarela = 39; //Sipay
}elseif (isset($d['shopname_customParameter1'])) {
	$pasarela = 64; //Wirecard
	error_log("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!Wirecard!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
} elseif ($d['trx']){
	$pasarela = 92; //Xilema
} elseif ($d['montant']){
	$pasarela = 115; //Papam
}else {

    if (!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/') && 
                !strstr($_SERVER['DOCUMENT_ROOT'], '/var/www/html') && 
                !strstr($_SERVER['DOCUMENT_ROOT'], '/home/julio/www') && 
                !strstr($_SERVER['DOCUMENT_ROOT'], '/wamp/www/')){
        $correoMi .= "<br>Pasarela inválida";
        $correo->todo(12,$titulo,$correoMi);
        $q = "insert into tbl_traza (titulo,traza,fecha) values ('$titulo','".html_entity_decode($correoMi, ENT_QUOTES)."',".time().")";
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
	case 60:
		$va = "Nueva variante RedSys";
	break;
	case 64:
		$va = "Wirecard";
	break;
	case 3:
		$va = "TefPay";
	break;
	case 71:
		$va = "PayTpv nuevo";
	break;
	case 92:
		$va = "Xilema";
	break;
	case 115:
		$va = "Papam";
	break;
}
$correoMi .= "pasarela->".$va."||<br>\ndesde->".$_SERVER['HTTP_REFERER']."<br>\n";
$cojin = '';

$correoMi .= $salida."||<br>\n";

$str = '';
$dserror = $uspytpv = '';
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
$pedazo='';

if ($pasarela == 115) { //Papam
	if (!$idtrans = $ent->isNumero($d['data'],12)) $correoMi .= "No es válido el número de la operación {$d['data']} <br>";
	else {
		include_once ('../include/payment.php');
		$temp->query("select c.clave, t.valor_inicial, t.moneda from tbl_colPasarMon c, tbl_transacciones t where t.idtransaccion = ".$idtrans." and t.pasarela = c.idpasarela and c.idmoneda = t.moneda");
		$codigo = $temp->f('clave');
		$importe = $temp->f('valor_inicial');
		$moneda = $temp->f('moneda');

		$correoMi .= "<br>".$moneda;

		// var_dump($d);
		// echo "<br>".$codigo;

		// if(!validSec($d,$codigo)){
		// 	$error = 'No validada la operación';
		// 	error_log($error);
		// 	$correoMi .= $error;
		// 	$correo->todo(13,$titulo,$correoMi);
		// 	exit;
		// }

		if ($d['result'] == 'OK' ) {
			$estado = 2;
		} else {
			$iderror = $d['errors'][0]['message'];
			$importe = 0;
			$estado = 3;
			$coderror = null;
		}
	}
} elseif ($pasarela == 92) { //Xilema
	$correoMi .= "Entra en pasarela Xilema||<br>\n";
//	error_log("operacion=".$d['trx']->reference);
//	error_log("errrrroooorr=".$d['errors'][0]['message']);
//	error_log('status='.$d['status']);
//	error_log("operacion=".$d['trx']['reference']);
	$correoMi .= "Datos de Xilema<br>\n";
	foreach ($d['trx'] as $value => $item) {
		$correoMi .= $value . "=" . $item . "<br>\n";
	}
	if (!$idtrans = $ent->isNumero($d['trx']['reference'],12)) $correoMi .= "No es válido el número de la operación ".$d['trx']['reference']." <br>";
//	error_log("idtrans=$idtrans");
	
	if ($d['status'] == 'Error' || $d['status'] == 'Denied') {// la operación vino con error
		$iderror = $d['errors'][0]['message'];
		$importe = 0;
		$estado = 3;
		$coderror = $d['trx']['actionCode'];
	} else {
		if (!$codautorizacion = $ent->isAlfanumerico($d['trx']['authCode'],6))$correoMi .= "No es válido el número de autorizo ".$d['trx']['authCode']." <br>";
		if (!$importe = $ent->isNumero($d['trx']['amount'], 11)) $correoMi .= "No es importe válido ".$d['trx']['amount']." <br>";
		$q = "select moneda from tbl_transacciones where idtransaccion = ".$idtrans;
		$correoMi .= "$q<br>\n";
		$temp->query($q);
		$moneda = $temp->f('moneda');
		$importe = $importe*100;
//		error_log("moneda=$moneda");
//		error_log("importe=$importe");
//		error_log("codautorizacion=$codautorizacion");
		
    	$pedazo = "tarjetas = '".$d['customer']['card']['PANObfuscated']."', ";
//		error_log('pedazo='.$pedazo);
		$estado = 2;
	}
	
} elseif ($pasarela == 71) { //PayTpv nuevo
	$correoMi .= "Entra en pasarela PayTpv nuevo||<br>\n";
	if (!$idtrans = $ent->isNumero($d['Order'],12)) $correoMi .= "No es válido el número de la operación {$d['Order']} <br>";
	else {
		$q = "select idmoneda from tbl_moneda where moneda = '".$d['Currency']."'";
		$temp->query($q);
		$correoMi .= "$q<br>\n";
		$importe = $d['Amount'];
		$moneda = $temp->f('idmoneda');
		
		if ($d['Response'] == 'KO') {//la pasarela devolvió error
			$iderror = $d['ErrorID']." - ".$d['ErrorDescription'];
			$importe = 0;
			$estado = 3;
			$coderror = $d['ErrorID'];
		} else {
			$correoMi .= "La operación está Aceptada, salvo iduser y token<br>\n";
			$uspytpv = $d['IdUser']."/".$d['TokenUser'];
			$arrAuto = explode('/', $d['AuthCode']);
			$codautorizacion = $arrAuto[0];
			$q = "update tbl_usuarios set idusrToken = '$uspytpv' where idtransaccion = '$idtrans'";
			$temp->query($q);
			$correoMi .= "$q<br>\n";
			$q = "delete from tbl_usuarios where idusrToken is null";
			$temp->query($q);
			$correoMi .= "$q<br>\n";
			$estado = 2;
		}
	}
} else if ($pasarela == 64) { //Wirecard
	$correoMi .= "Entra en pasarela Wirecard||<br>\n";
	$idtrans = $d['shopname_customParameter1'];
// 	$comercio = $d[]
	$importe = $d['amount']*100;
// 	$iderror
	$firma = $d['responseFingerprint'];
	$codautorizacion = $d['orderNumber'];
	$moneda = $d['currency'];
	
	if (strlen($moneda)>2) {
		$q = "select t.pasarela, c.clave 
				from tbl_transacciones t, tbl_colPasarMon c 
				where t.pasarela = c.idpasarela 
					and t.idtransaccion = '%s' 
					and t.estado = 'P' 
					and c.idmoneda = '%d'";
		$q = sprintf($q, $idtrans, $moneda);
		$correoMi .= "$q<br>\n";
//		$temp->query($q);
//		$d['secret'] = $temp->f('clave');
//		$arrClstr = explode(',', $d['responseFingerprintOrder']);
		$ret = '';
		foreach ($arrClstr as $item){
			$ret .= $d[$item];
			$correoMi .= "$item,";
			}
		$correoMi .= "<br>$ret<br>";
		$comprueba = hash_hmac("sha512", $ret, $d['secret']);
		$correoMi .= "<br>fingerprint generado:$comprueba<br>\n";
	    $pedazo = "tarjetas = '".$d['maskedPan']."', ";
//	    if ($comprueba != $d['responseFingerprint']) {
//	    	$correoMi .= "<br><br>\n\nError en la comprobación de la firma en Wirecard";
//	     	$correo->todo(13,$titulo,$correoMi);
// 	    	exit;
//	    }
	}
	
	$estado = 3;
	if ($d['paymentState'] == 'SUCCESS') $estado = 2;
	
// 	echo $correoMi; exit;

} elseif ($pasarela == 1) { //pasarela BBVA, BBVA3D, BBVA 3D onL, BBVA3, BBVA4, BBVA9 3D y BBVA10 3D
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
    $error = (int)$respuesta;
    $terminal = $d['Ds_Terminal'];

    $query = "select tipoEntorno, pasarela from tbl_transacciones where idtransaccion = '$idtrans'";
    $temp->query($query);
	$correoMi .= "query= $query <br>\n";
	
	$q = "select clave from tbl_colPasarMon
				where (1*terminal) = '".($terminal*1)."'
					and length(clave) < 32
					and comercio = '{$comercio}'";
	$temp->query($q);
	$correoMi .= "q= $q <br>\n";
	$clave = $temp->f('clave');

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
        $ok = 1;
    }

    //fwrite($handle, "firma=".$firma."<br>\n");
    //fwrite($handle, "comprueba=".$comprueba."<br>\n");
} elseif ($pasarela == 3) { //Pasarela TefPAy
	$correoMi .= "Entra en pasarela TefPay<br>\n";
//    $d = $_REQUEST;

    $idtrans = substr($d['Ds_Merchant_MatchingData'], 0, 12);
    $idtransMod = $d['Ds_Merchant_MatchingData'];
    $comercio = $d['Ds_Merchant_MerchantCode'];
    $firma = $d['Ds_Signature'];
    $codautorizacion = $d['Ds_AuthorisationCode'];
    $q = "select moneda, estado, tarjetas, pasarela from tbl_transacciones where idtransaccion = '$idtrans'";
    $temp->query($q);
    $moneda = $temp->f('moneda');
    $estaIn = $temp->f('estado');
    $tarja = $temp->f('tarjetas');
    $TefPasa = $temp->f('pasarela');
    $paseEst = "'P','D','A','N'";
    if ($TefPasa != 37) $paseEst = "'P','D','N'";
	$cojin = " and estado = 'A'";
	$correoMi .= strlen($d['Ds_AuthorisationCode'])."<br>\n";
    if ($d['Ds_Code'] == 100 && strlen($d['Ds_AuthorisationCode']) > 3) {// operación Aceptada
    	$correoMi .= "Operación Aceptada<br>\n";
    	$estado = '2';
    	
    	if ($d['Ds_Amount']) $importe = $d['Ds_Amount'];
    	elseif ($d['Ds_Merchant_Amount']) $importe = $d['Ds_Merchant_Amount'];
    	
    	$pedazo = "tarjetas = '************".$d['Ds_PanMask']."', identificadorBnco = ".$d['Ds_Date'].", ";
    	
    } elseif ($d['Ds_Code'] == 703 ) {// operación en Dudas
    	$correoMi .= "Operación en Dudas con 703<br>\n";
//     	if ($estaIn == 'A') { //Si estaba antes Aceptada sigue Aceptada
//     		$correoMi .= "Operación Denegada por Titanes<br>\n";
// 	    	$estado = '2';
	    	
// 	    	if ($d['Ds_Amount']) $importe = $d['Ds_Amount'];
// 	    	elseif ($d['Ds_Merchant_Amount']) $importe = $d['Ds_Merchant_Amount'];
	    	
// 	    	$pedazo = " tarjetas = '**** **** **** ".$d['Ds_PanMask']."', ";
//     	} else {
    		$correoMi .= "Operación Denegada por Titanes<br>\n";
    		$estado = '3';
    		$importe = 0;
    		$codautorizacion = null;
    		$ok = 1;
    		$iderror = $d['Titanes_Description']." ".$d['Titanes_Messages'];
    		$coderror = $d['Ds_Code'];
//     	}
    	$correo->todo(51, "Operación con Error 703 de Tefpay", $correoMi);
    	
    } elseif ($d['Ds_Code'] == 700) {// operación Aceptada
    	$correoMi .= "Operación Aceptada<br>\n";
    	$estado = '2';
    	
    	if ($d['Ds_Amount']) $importe = $d['Ds_Amount'];
    	elseif ($d['Ds_Merchant_Amount']) $importe = $d['Ds_Merchant_Amount'];
    	
    	$pedazo = "tarjetas = '************".$d['Ds_PanMask']."', identificadorBnco = ".$d['Ds_Date'].", ";
    	
    	if (isset($d['Titanes_OrderId']) && $d['Titanes_OrderCode'] != 3) { //denegada por Titanes aunque de Tefpay viene Aceptada
    	$correoMi .= "Operación Denegada por Titanes<br>\n";
    		$estado = '3';
    		$importe = 0;
    		$codautorizacion = null;
    		$ok = 1;
			$coerr = '';
			if ($posi = stripos($d['Titanes_Description'], 'Ds_CodeBank')) {
				$sale = substr($d['Titanes_Description'], $posi+14);
				$correoMi .= "select texto from tbl_errores where idpasarela = 13 and codigo = '$sale'\n<br>";
				$temp->query("select texto from tbl_errores where idpasarela = 13 and codigo = '$sale'");
				$coerr = $temp->f('texto');
			}
			$iderror = $d['Titanes_Description']." ".$d['Titanes_Messages']. " / ".$coerr;
    		$coderror = $d['Ds_Code'];
    	}
    } else { //operación denegada
		$correoMi .= "Operación Denegada por TefPay<br>\n";
        $estado = '3';
        $importe = 0;
        $codautorizacion = null;
        $ok = 1;
        if ($TefPasa == 37) {
			$coerr = '';
			if ($posi = stripos($d['Titanes_Description'], 'Ds_CodeBank')) {
				$sale = substr($d['Titanes_Description'], $posi);
				$correoMi .= "select texto from tbl_errores where idpasarela = 13 and codigo = $posi\n<br>";
				$temp->query("select texto from tbl_errores where idpasarela = 13 and codigo = $posi");
				$coerr = $temp->f('texto');
			}
			$iderror = $d['Titanes_Description']." ".$d['Titanes_Messages']. " / ".$coerr;
		} else {
			$temp->query("select texto from tbl_errores where codigo = '".ltrim($d['Ds_CodeBank'],'0')."' and idpasarela = 37");
			$texerr = "(".ltrim($d['Ds_CodeBank'],'0').") ".$temp->f('texto');
			$temp->query("select texto from tbl_errores where codigo = '".ltrim($d['Ds_Code'],'0')."' and idpasarela = 37 limit 0,1");
			$texerr = $texerr." (".ltrim($d['Ds_Code'],'0').") ".$temp->f('texto');
        }
		$correoMi .= "iderror=$iderror<br>\n";
        $iderror .= $d['Ds_Message']." - ". $texerr;
    }
    
    if ($d['Titanes_OrderId']) {
    	$q = "update tbl_aisOrden set titOrdenId = '{$d['Titanes_OrderId']}' where idtransaccion = '$idtrans'";
    	$temp->query($q);
    }
    
    if ($estado == '2' && $TefPasa == '37'){
		include_once '../admin/classes/tcpdf/config/tcpdf_config.php';
		include_once '../admin/classes/tcpdf/tcpdf.php';
		creatitVou($idtrans); //está Aceptada, genero voucher y lo envío a Titanes
    }
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

//     if (strlen($codautorizacion) > 2) {
//         $estado = '2';
//         $importe = $d['Ds_Amount'];
//         $codautorizacion = $d['Ds_AuthorisationCode'];
//     } else {
//         $estado = '3';
//         $importe = null;
//         $codautorizacion = null;
// 		$iderror = $d['Ds_Message'];
//         $ok = 1;
//     }

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
        $ok = 1;
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
		$ok = 1;
	} else $estado = "2";
	
	$correoMi .= "MerchantIdD $comercio | AcquirerBIN $AcquirerBIN | TerminalID $TerminalID | Num_operacion $idtrans | Importe $importe | TipoMoneda $moneda |".
					" Exponente $Exponente | Referencia $Referencia | Firma $firma | Num_aut $codautorizacion | Idioma $Idioma | Pais $Pais | 
					Descripcion $Descripcion<br />\n";
	
	$q = "select clave from tbl_colPasarMon
				where (1*terminal) = '".($TerminalID*1)."'
					and length(clave) < 32
					and comercio = '{$comercio}'";
	$temp->query($q);
	$correoMi .= "q= $q <br>\n";
	$clave = $temp->f('clave');
			

	$comprueba = sha1($clave.$comercio.$AcquirerBIN.$TerminalID.$idtrans.$importe.$moneda.$Exponente.$Referencia);
	$importe = $importe*1;
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
        $ok = 1;
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
        $ok = 1;
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
        $ok = 1;
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
	$correoMi .= $q."\n<br>";

	$comprueba = $firma = md5($comercio . $usercode . $terminal . $idtrans . $importe . $moneda . md5('pCF2s3TVtmhHSgX6MyvN'));
	$impòrte = $importe*1;
	$correoMi .= $comprueba." / ".$firma."<br>\n";
	
	if ($moneda * 1 > 1) {
		$q = "select idmoneda from tbl_moneda where moneda = '$mo'";
		$temp->query($q);
		$correoMi .= $q."\n<br>";
		$moneda = $temp->f('idmoneda');
	}
} elseif ($pasarela == 60) { //nueva variante RedSys
	$version = $d["Ds_SignatureVersion"];
	$datos = $d["Ds_MerchantParameters"];
	$firma = $d["Ds_Signature"];
	
	$decodec = json_decode($miObj->decodeMerchantParameters($datos));
// 	{"Ds_Date":"22%2F10%2F2015","Ds_Hour":"17%3A41","Ds_SecurePayment":"1","Ds_Card_Country":"724",
// 	"Ds_Amount":"100","Ds_Currency":"978","Ds_Order":"151022173937","Ds_MerchantCode":"030631725","Ds_Terminal":"004",
// 	"Ds_Response":"0000","Ds_MerchantData":"","Ds_TransactionType":"0","Ds_ConsumerLanguage":"1","Ds_AuthorisationCode":"105096"}
// 	$arrVer = json_decode($decodec);
	
	$q = "select clave from tbl_colPasarMon where terminal = '{$decodec->Ds_Terminal}' and length(clave) = 32 and comercio = '{$decodec->Ds_MerchantCode}'";
	$temp->query($q);
	$correoMi .= $q."\n<br>";
	$kc = $temp->f('clave');
	$moneda = $decodec->Ds_Currency;
	if (isset($decodec->Ds_ErrorCode) && strstr($decodec->Ds_ErrorCode, 'SIS')) $error = $decodec->Ds_ErrorCode; else $error = 1 * $decodec->Ds_Response; 
	$coderror = $decodec->Ds_Response;
// 	$error = $decodec->Ds_ErrorCode;
	$idtrans = $decodec->Ds_Order;
	$bytes = array(0,0,0,0,0,0,0,0); //byte [] IV = {0, 0, 0, 0, 0, 0, 0, 0}
	$iv = implode(array_map("chr", $bytes)); //PHP 4 >= 4.0.2
	
	if ($coderror < 100) {
		$estado = '2';
		$importe = $decodec->Ds_Amount;
		$codautorizacion = $decodec->Ds_AuthorisationCode;
		
		//chequeo si la operación es del TPV Titanes
//		$q = "select pasarela from tbl_transacciones where idtransaccion = ".$decodec->Ds_Order;
//		$correoMi .= $q."\n<br>";
//		$temp->query($q);
//		if ($temp->f('pasarela') == 91) {//entra en la pasarela de Titanes
//			$correoMi .= "****Entra en la pasarela de Titanes\n<br>";
//			$q = "select o.envia 'AmountToSend', o.recibe 'AmountToReceive', o.comision 'Charge', t.valor_inicial 'TotalAmount', c.idtitanes 'CustomerId', b.idtitanes 'BeneficiaryId', b.ciudad 'City', m.moneda 'CurrencyToSend', o.idrazon 'Reason', o.titOrdenId from tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t, tbl_moneda m where o.idbeneficiario = b.id and t.idtransaccion = o.idtransaccion and t.moneda = m.idmoneda and o.idcliente = c.id and length(o.titOrdenId) = 0 and o.idtransaccion = ".$decodec->Ds_Order;
//			$correoMi .= $q."\n<br>";
//			$correoMi .= "cantRecords=".$temp->num_rows()."\n<br>";
//			$temp->query($q);
//			
//			if ($temp->num_rows() && strlen($temp->f('titOrdenId')) < 5) {
//				($temp->f('City') == NULL || $temp->f('City') == '') ? $city = 'La Habana' : $city = $temp->f('City');
//
//				$data = array(
//					'CustomerId'				=> $temp->f('CustomerId'),
//					'BeneficiaryId'				=> $temp->f('BeneficiaryId'),
//					'Country'					=> 'CU',
//					'City'						=> $city,
//					'DeliveryType'				=> '4',
//					'AmountToSend'				=> number_format(($temp->f('AmountToSend')/100),2,".",""),
//					'CurrencyToSend'			=> $temp->f('CurrencyToSend'),
//					'AmountToReceive'			=> number_format(($temp->f('AmountToReceive')/100),2,".",""),
//					'CurrencyToReceive'			=> 'CUC',
//					'Charge'					=> number_format(($temp->f('Charge')/100),2,".",""),
//					'TotalAmount'				=> number_format(($temp->f('TotalAmount')/100),2,".",""),
//					'Correspondent'				=> 'T086',
//					'SubCorrespondent'			=> '1',
//					'Branch'					=> 'T0860001',
//					'Reason'					=> $temp->f('Reason'),
//					'BenefBankName'				=> '',
//					'BenefBankCity'				=> '',
//					'BenefBankAccountNumber'	=> '-1',
//					'BenefBankAccountType'		=> '3',
//					'BenefBankAccountAgency'	=> '',
//					'Ds_Merchant_Order'			=> $decodec->Ds_Order
//				);
//				$data = array_merge($data, array(
//				"Signature"					=> $temp->f('CustomerId').$temp->f('BeneficiaryId').(number_format(($temp->f('AmountToReceive')/100),2,".","")).'CUC'));
//				$tipo = 'O';
//				$correoMi .= "sale=". json_encode($data)."<br>\n";
//				$sale = datATitanes($data,$tipo,91);
//				$correoMi .= "sale=$sale<br>\n";
//
//				$arrVales = json_decode($sale);
//
//				if ($arrVales->Id > 0) {
//					$idTit			= $arrVales->Id;
//					$StatusCode		= $arrVales->StatusCode;
//					$Status			= $arrVales->Status;
//					$Description	= $arrVales->Description;
//					$Issues			= $arrVales->Issues;
//
//					$correoMi .= "idTit=$idTit<br>";
//					$correoMi .= "StatusCode=$StatusCode<br>";
//					$correoMi .= "Status=$Status<br>";
//					$correoMi .= "Description=$Description<br>";
//					$correoMi .= "Issues=$Issues<br>";
//
//					$q = "update tbl_aisOrden set titOrdenId = '$idTit' where length(titOrdenId) = 0 and idtransaccion = ".$decodec->Ds_Order;
//					$correoMi .= $q."<br>";
//					$temp->query($q);
//					
//					if ($Status != 'Ready') {
//
//						$correo->todo(59, 'Operación con error al enviarla a Titanes', "La operación ".$decodec->Ds_Order." de Cimex ha devuelto error en Titanes. Error devuelto: $sale");
//					}
//					
//				} else {
//
//						$correo->todo(59, 'Operación sin código de Titanes', "La operación ".$decodec->Ds_Order." de Cimex ha sido Revocada por Titanes ver si se devuelve. Error devuelto ". $sale);
//					
//				}
//			} else $correo->todo(59, 'Existe con IDTitanes', "La operación ".$decodec->Ds_Order." ya tiene id de Titanes por lo que no se vuelve a enviar.");
//		}
	} else {
		$estado = '3';
		$importe = null;
		$codautorizacion = null;
		$ok = 1;
	}
	$correoMi .= "Ds_TransactionType=".$decodec->Ds_TransactionType."\n<br>
					Ds_Card_Country=".$decodec->Ds_Card_Country."\n<br>
					Ds_Date=".$decodec->Ds_Date."\n<br>
					Ds_SecurePayment=".$decodec->Ds_SecurePayment."\n<br>
					Ds_Signature=".$decodec->Ds_Signature."\n<br>
					Ds_Order=".$decodec->Ds_Order."\n<br>
					Ds_Hour=".$decodec->Ds_Hour."\n<br>
					Ds_Response=".$decodec->Ds_Response."\n<br>
					Ds_AuthorisationCode=".$decodec->Ds_AuthorisationCode."\n<br>
					Ds_Currency=".$decodec->Ds_Currency."\n<br>
					Ds_ConsumerLanguage=".$decodec->Ds_ConsumerLanguage."\n<br>
					Ds_MerchantCode=".$decodec->Ds_MerchantCode."\n<br>
					Ds_Amount =".$decodec->Ds_Amount ."\n<br>
					Ds_Terminal=".$decodec->Ds_Terminal."\n<br>
					Ds_MerchantParameters=".$decodec->Ds_MerchantParameters."\n<br>
					Ds_ErrorCode=".$decodec->Ds_ErrorCode."\n<br>";
	$correoMi .= "kc=$kc\n<br>moneda=$moneda\n<br>coderror=$coderror\n<br>idtrans=$idtrans\n<br>estado=$estado\n<br>importe=$importe\n<br>
					codautorizacion=$codautorizacion\n<br>";
	
	$comprueba = $miObj->createMerchantSignatureNotif($kc,$datos);
	
}

$correoMi .= "$comprueba=$firma||<br>\n";

if ($comprueba == $firma||1==1||$ok=1) {
	
	if ($comprueba != $firma && $ok=0) {
		$correoMis =  "<br>\nNo concuerda la firma=".$firma."<br>\ncon la comprobación=$comprueba realizada<br>\npara la operación $idtrans<br>\n";
		$correoMi .= $correoMis;
		$correo->todo(13,"Fallo en firma de la operación",$correoMis);
	}
	
	if ($estado == '') $estado = '4';
    $firma = true;
$correoMi .=  "<br>\nfirma=".$firma."||<br>\n";

$correoMi .=  "<br>\ncoderror=".$coderror."||<br>\n";
	//Busca el id del error
	if ($coderror != '') {
		if ($pasarela == 1 
				&& $pasarela == 8 
				&& $pasarela == 9 
				&& $pasarela == 11 
				&& $pasarela == 14 
				&& $pasarela == 15 
				&& $pasarela == 17 
				&& $pasarela == 18
				) $psrl = 1;
		elseif ($pasarela == 2) $psrl = 2;
		elseif ($pasarela == 12) {
			$psrl = 12;
			$error = $coderror;
		}
		elseif ($pasarela == 4) $psrl = 13;
		elseif ($pasarela == 40) $psrl = 40;
		elseif ($pasarela == 60) $psrl = 100;
		elseif ($pasarela == 3 ) {
		    $psrl = 5;
		    if ($TefPasa == 75) $psrl = 75;
		    $error = $coderror;
		}
		elseif ($pasarela == 71) $psrl = 71;
		elseif ($pasarela == 92) {
			$psrl = 92;
			$error = $coderror;
		}
		
		if ($psrl) {
			$sql = "select id_error, texto from tbl_errores where codigo = '$error' and idpasarela = $psrl";
			$correoMi .=  "<br>\n".$sql;
	// 		$temp->setQuery();
			$temp->query($sql);
			$iderror = $iderror ." ". $error." - ".$coderror." - ".$temp->f("texto")." ".$dserror;
			$errorAMF = $temp->f("id_error");
		}
		if ($estado == ' ' && $pasarela == 1) $estado = 3;
	}
	$correoMi .= "\n<br>moneda=$moneda\n<br>";
	
//	busca la conversion de moneda
	if (strlen($moneda) >0 ) {
		$q = "select moneda from tbl_moneda where idmoneda = ".$moneda;
		$temp->query($q);
		$mon = $temp->f('moneda');
		if ($moneda == '978') $cambioRate = 1;
		else {
			$cambioRate = leeSetup ($mon);
			(date('G') < 14) ? $f = date('dmy',strtotime('-1 day')) : $f = date('dmy');
			$temp->query("select count(*) total from tbl_setup where nombre = '$mon' and from_unixtime(fecha, '%d%m%y%H%i') like '".$f."140%'");
			error_log("select count(*) total from tbl_setup where nombre = '$mon' and from_unixtime(fecha, '%d%m%y%H%i') like '".$f."140%'");
			if ($temp->f('total') == 0) {
				$temp->query("select from_unixtime(fecha, '%d%m%y %H:%i:%s') 'fecha' from tbl_setup where nombre = '$mon' and from_unixtime(fecha, '%d%m%y%H%i') like '".$f."140%'");
				$correo->todo(2,'Cambio en la tabla setup','Se cambió la fecha de la moneda '.$mon.' antes de tiempo ahora tiene la fecha '.$temp->f('fecha').' y el valor ahora es '.$cambioRate."<br>select from_unixtime(fecha, '%d%m%y %H:%i:%s') 'fecha' from tbl_setup where nombre = '$mon' and from_unixtime(fecha, '%d%m%y%H%i') like '".$f."140%'");
			}

			// $temp->query("select distinct truncate(tasa,4) 'tasa' from tbl_transacciones where moneda = $moneda and tipoOperacion = 'P' and estado = 'A' and idtransaccion > ".date('ymd')."140400 and idcomercio != '527341458854'");
			// error_log("select distinct truncate(tasa,4) from tbl_transacciones where moneda = $moneda and tipoOperacion = 'P' and estado = 'A' and idtransaccion > ".date('ymd')."140400 and idcomercio != '527341458854'");
			// error_log("cambioRate=".$cambioRate);
			// error_log("number_format=".number_format($cambioRate, 4, '.',''));
			// error_log("tasa=".$temp->f('tasa'));
			// error_log("number_format=".number_format($temp->f('tasa')));
			// if ($temp->num_rows() == 1 && number_format($cambioRate, 4, '.','') != $temp->f('tasa')) {
			// 	$correo->todo(2,'Cambio de tasa '.date('d/m/Y H:i:s'),'Se cambió la tasa con la que se venía registrando la moneda '.$mon.' antes era de '.$temp->f('tasa').' y el valor ahora es '.$cambioRate."<br>select tasa from tbl_transacciones where moneda = $moneda and tipoOperacion = 'P' and estado = 'A' and idtransaccion > ".date('ymd')."140400 and idcomercio != '527341458854'");
			// } else if ($temp->num_rows() > 1){
			// 	$correo->todo(2,'Mas de una tasa anterior '.date('d/m/Y H:i:s'),'Se obtiene mas de una tasa ('.$temp->num_rows().') con la que se venía registrando la moneda '.$mon.' correr el siguiente select'."<br>select tasa from tbl_transacciones where moneda = $moneda and tipoOperacion = 'P' and estado = 'A' and idtransaccion > ".date('ymd')."140400 and idcomercio != '527341458854'");
			// }
		}
	}
	
$correoMi .=  "<br>\ncambioRate=".$cambioRate;
$correoMi .=  "<br>\niderror=".$iderror;
$correoMi .=  "<br>\nestado=".$estado;
$correoMi .=  "<br>\nerrorAMF=".$errorAMF;

//echo $correoMi;
	$est = "X";
	$q = "select estado, from_unixtime(fecha_mod,'%d/%m/%Y %H:%i:%s') fe, codigo, idcomercio from tbl_transacciones where idtransaccion = '$idtrans'";
	$correoMi .=  "<br>\n".$q."<br>\n";
	$temp->query($q);
	$est = $temp->f('estado');
	$fe = $temp->f('fe');
	$code = $temp->f('codigo');
	$comerid = $temp->f('idcomercio');
	
	if ($comerid == '527341458854' && $cambioRate != 1) {//Si el comercio es Cimex le afecto la tasa de cambio
		$q = "select tasa from tbl_colCambBanco where idmoneda = '$moneda' and idbanco = 17 order by fecha desc limit 0,1";
		$temp->query($q);
		$correoMi .=  "<br>\n".$q;
		$ta = $temp->f('tasa');
		$correoMi .=  "<br>\nta=".$ta;
		$cambioRate = $ta + leeSetup('descCimex');
//		$cambioRate = $cambioRate - leeSetup('descCimex');
		$correoMi .=  "<br>\ncambioRateCimex=".$cambioRate;
//		error_log("<br>\ncambioRateCimex=".$cambioRate);
	}

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

    if (($est == "A" || $est == "B" || $est == "V") && $pasarela != 3 ) {
        $correoMi .= "La operación estaba con estado $est a las $fe y se volvió a recibir información del Banco como $estadoC, no se reliza ninguna acción en el Concentrador ni se envían datos a los ".
                "comercios<br>";
        $salta = true;
    } elseif ( 
            (($est == "N" || $est == "D") && ( $estado == '2')) ||
            ($est == "P") ||
    		($pasarela == 3 )
            ) {
		$query = "update tbl_transacciones set ";
		switch ($estado) {
			case '2': //Aceptada
				$estado = 'A';
				$query .= " codigo = '$codautorizacion', valor = $importe, id_error = null, tasa = ".$cambioRate.", 
					euroEquiv = ($importe/100)/($cambioRate), ".$pedazo;
				$texto = 'Aceptada';
				break;
			case '3': //Denegada
				$estado = 'D';
				$query .= " id_error = '".htmlspecialchars($iderror, ENT_QUOTES)."', ";
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
		$query .= " estado = '$estado', fecha_mod = ".time()." where idtransaccion = '$idtrans' and estado in ($paseEst)";
		//fwrite($handle, "<br>\n".$query);
		$correoMi .=  "<br>\n".$query;
		$temp->query($query);
        
        $q = "select fecha_mod, tipoEntorno, valor/100 val, idcomercio, identificador from tbl_transacciones where idtransaccion = '$idtrans'";
        $temp->query($q);
		$elcomercio = $temp->f('idcomercio');
        
        //Actualiza la tabla de las reservas con el resultado de la transaccion
        $query = "update tbl_reserva set id_transaccion = '".$idtrans."', bankId = '".$codautorizacion."', fechaPagada = ".$temp->f('fecha_mod').",
                        estado = '".$estado."', est_comer = '".$temp->f('tipoEntorno')."', valor = ".$temp->f('val')."
                    where codigo = '".$temp->f('identificador')."' and estado in ($paseEst) and id_comercio = ".$temp->f('idcomercio');
    //	echo $query;
        $temp->query($query);
        $correoMi .=  "<br>\n".$query."\n<br><br>\n";
		
		if ($estado == 'A') {
			$temp->query("update tbl_transacciones set codigo = '$codautorizacion' where idtransaccion = $idtrans");
			$temp->query("update tbl_reserva set bankId = '".$codautorizacion."' where id_transaccion = $idtrans");

			//Si la operación fué Aceptada actualizo la operación en la tabla de los lotes si existe
			$temp->query("select lotes, id from tbl_comercio where idcomercio = '$elcomercio'");
			if ($temp->f('lotes') == 1) { // si el comercio tiene permitidas operaciones por lotes
				$temp->query("select idlote, valor, moneda from tbl_reserva where id_transaccion = $idtrans");
				if ($temp->f('idlote') > 0) {
					$q = "update tbl_lotes set valor = ".($temp->f('valor') * 100).", moneda = ".$temp->f('moneda')." fechaLanz = unix_timestamp(), valida = 0 where id = ".$temp->f('idlote');
					$temp->query("update tbl_lotes set valor = ".($temp->f('valor') * 100).", moneda = ".$temp->f('moneda').", fechaLanz = unix_timestamp(), valida = 0 where id = ".$temp->f('idlote'));
				}
			}
		}
	}
    
    //Aviso de transacción duplicada desde el Banco
    if ($est == "N" || $est == "D" || $est == "A" || $est == "B" || $est == "V") {
        $lab = 'Recibido datos duplicados desde el banco';
        $mes = "fecha=".date('d/m/Y H:i:s')."<br>\n"."Se ha recibido duplicado los datos de la transacción $idtrans. La misma estaba en la base de datos con el estado $est el $fec ". "y se recibió con estado $estadoC";
        $correo->todo (20, $lab, $mes);
        
        $correoMi .= $mes."<br>\n";
		
		if ($est == 'A' && $estadoC == 'A') {
			$lab = 'Recibida transacción Aceptada duplicada desde el banco';
			$mes = "Fecha=".date('d/m/Y H:i:s')."<br>\n"."Se han recibido los siguentes datos de la transacción $idtrans:<br>\n $lleg <br><br>Anteriormente tenía código de banco $code. Se debe revisar si se devuelve.";
			if (!isset($TefPasa))
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
				c.url, t.tipoEntorno, t.valor/100 precio, c.url_llegada, p.nombre, (select a.nombre from tbl_agencias a where p.idagencia = a.id) comercio, 
				(select idusrToken from tbl_usuarios u where u.idtransaccion = t.idtransaccion) usuario,
				(select nombre from tbl_reserva r where r.id_transaccion = t.idtransaccion) usr, 
				t.sesion, c.resp
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
	
	//el pago es através de web y el sitio solicita envío directo de datos
	if ( ($temp->num_rows() == 0) && (strlen($valores[13]) > 1) && $salta == false ) { 
		if (strlen($valores[18]) == 32)
			$firma = convierte($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));
		else 
			$firma = convierte256($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));

		if (strlen($firma) > 2) {
			$correoMi .=  "<br>firma={$valores[1]}, {$valores[2]}, {$valores[6]}, {$valores[7]}, {$valores[8]},	{$valores[0]}, ".
								date('d/m/y h:i:s', $valores[5])."<br>\n";
			$iderror = urlencode($iderror);
			$correoMi .=  "<br>valores19=".$valores[19];
			if ( $valores[19] == 0
				// $valores[1] != '140778652871' 		//Prueba Cubana 
			    // && $valores[1] != '129025985109' 	//Cubana
			    // && $valores[1] != '140784511377' 	// Saratoga
			    // && $valores[1] != '135334103888' 	// Nacional
				// && $valores[1] != '146161323238' 	// Claim
				// && $valores[1] != '151560722836' 	// IZ- IslazulCuba WEB
				// && $valores[1] != '138668374494' 	// PruebaHN
				// && $valores[1] != '144172448713' 	// PruebaCimex 
				// && $valores[1] != '159136992102' 	// Umbrella
				// && $valores[1] != '159171392542' 	// Havanatursa
				// && $valores[1] != '527341458854' 	// Cimex
				) {
				$correoMi .= "<br>Funcionalidad GET para todos los comercios excepto los indicados<br>";
				
				$cadenaEnv = "?"."comercio=".$valores[1]."&transaccion=".$valores[2]."&importe=".$valores[6].
							"&moneda=".$valores[7]."&resultado=".$valores[8]."&codigo=".$valores[0]."&idioma=".$valores[4].
							"&firma=$firma&fecha=". urlencode(date('d/m/y h:i:s', $valores[5]))."&error=$iderror&tasa=$cambioRate".
							"&comerc=".$valores[15]."&usuario=".$valores[16];
				$cadenaEnvia = $valores[13].$cadenaEnv;
				$correoMi .= $cadenaEnvia."<br>\n";
	
				$ch = curl_init($cadenaEnvia);
	
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_POST, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$output = curl_exec($ch);
	            $curl_info = curl_getinfo($ch);
	// 						echo "error=".curl_errno($ch);
				if (curl_errno($ch)) $correoMi .=  "Error en la comunicación al comercio:".curl_error($ch)."<br>\n";
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
				
			} elseif ($valores[19] == 1) {
				$correoMi .= "<br>Funcionalidad por POST<br>\n";
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
						"error"			=> $iderror,
						"comerc"		=> $valores[15],
						"usuario"		=> $valores[16],
						"tasa"			=> $cambioRate
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
// 	if ( 
//             $salta == false 
// // 			&& (
// // 			$valores[1] == '129025985109'	//Descomentar esta línea para habilitar Cubana 
// //			|| $valores[1] == '122327460662' //Comentar esta línea para deshabilitar Prueba
// // 			)
// 				) {
// 		$correoMi .=  "<br>\nEntra en la parte del chuchuchú <br>\n";
// 		$vale = number_format(($valores[6]/100), 2);
// 		$correoMi .= $valores[2].'.'.$valores[0].'.'.$vale.'.'.$valores[7].'.'.$valores[8].'.'.$valores[1]."<br>\n";
		
// 		$fr=md5($valores[2].$valores[0].$vale.$valores[7].$valores[8].$valores[1]);
        
//         //Actualiza la tabla amadeus con los datos de la transacción
//         $q = "update tbl_amadeus set idtransaccion = '".$valores[0]."', estado = '".$valores[8]."', fechamod = ".time().", codigo = '".$valores[3]
//                 ."' where idcomercio = '".$valores[1]."' and rl = '".$valores[2]."'";
//         $correoMi .= "<br>\n". $q;
//         $temp->query($q);
		
//         //busca en la tabla amadeus los datos para enviar de vuelta a amadeus
//         $q = "select urlko url, ";
//         if ($valores[8] == 'A') $q = "select urlok url, ";
//         $q .= " sesion, enc from tbl_amadeus  where idcomercio = '{$valores[1]}' and rl = '{$valores[2]}'";
//         $correoMi .= "<br>\n". $q;
//         $temp->query($q);
//         $urlEnv = $temp->f('url')."&FINAL_CONF=FALSE";
// 		$enc = $temp->f('enc');
//         $sessionid = $temp->f('sesion');

//         $encTime = date('YmdHis',time());
//         $arrMerc = array('129025985109' => 'AAWOAAWO', '122327460662' => 'ADMPADMP'); //array para site
//         $arrClave = array('129025985109' => 'fgrt34sdsw2', '122327460662' => 'fgrt34sdsw2');

//         $url = _ESTA_URL."/cubanaLand.php?fac=".$valores[2]."&com=".$valores[1];
        
//         if ($enc == 'HMACSHA512') {
//         	$calmd5 = hash_hmac('sha512',$sessionid.$valores[0].urlencode($url),$arrClave[$valores[1]]);
//         	$correoMi .= "<br>\n hash_hmac('sha512',$sessionid.$valores[0].urlencode($url),{$arrClave[$valores[1]]})";
//         } else {
// 	        $calmd5 = strtoupper(md5($sessionid.$valores[0].urlencode($url).$arrClave[$valores[1]]));
// 	        $correoMi .= "<br>\n md5($sessionid.$valores[0].urlencode($url).{$arrClave[$valores[1]]})";
//         }


//         $correoMi .= "<br>\nurlEnv=$urlEnv";
// //        $correoMi .= "<br>\nSITE=".$arrMerc[$valores[1]]; 
// //        $correoMi .= "<br>\nENC_TYPE=1"; 
// //        $correoMi .= "<br>\nENC="; 
// //        $correoMi .= "<br>\nENC_TIME=".$encTime; 
// //        $correoMi .= "<br>\nPAYMENT_REFERENCE=".$valores[0];
// //        $correoMi .= "<br>\nACKNOWLEDGEMENT_URL=".$url; 
// //        $correoMi .= "<br>\nCHECKSUM=".$calmd5;
// //        $correoMi .= "<br>\nAPPROVAL_CODE=".$valores[3]; 
// //        $correoMi .= "<br>\nCANCELLATION_URL="._ESTA_URL."/amadeus/cancel.php"; 

//         $correoMi .=  "<br>\nEnvío del formulario por curl a Amadeus<br>\n";
//         //Envío del formulario por Cdonts a Amadeus
//         $data = array(
//             "SITE"=>$arrMerc[$valores[1]],
//             "ENC_TYPE"=>'1',
//             "ENC"=>"",
//             "ENC_TIME"=>$encTime,
//             "PAYMENT_REFERENCE"=>$valores[0],
//             "ACKNOWLEDGEMENT_URL"=>$url,
//             "CHECKSUM"=>$calmd5,
//             "APPROVAL_CODE"=>$valores[3],
//             "CANCELLATION_URL"=>_ESTA_URL."/amadeus/cancel.php"
//         );
        
//         foreach ($data as $key => $value) {
//             $correoMi .=  "<br>\n$key = $value";
//         }

// //        $urlEnv = _ESTA_URL."/cubanaLand.php"; //Comentar esta línea para dejar el desarrollo normal
//         $correoMi .=  $urlEnv."<br>\n";
//         $salidaCurl = '';$i = 1;
//         $ch = curl_init($urlEnv);
//         curl_setopt($ch, CURLOPT_HEADER, false);
//         curl_setopt($ch, CURLOPT_POST, true);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//         while (strlen($salidaCurl) == 0 && $i < 4) {
//             $salidaCurl = curl_exec($ch);
//             $curl_info = curl_getinfo($ch);
            
//             foreach ($curl_info as $key => $value) {
//                 $correoMi .=  $key." = ".$value."<br>\n";
//             }
            
//             if(strlen(curl_error($ch))) $correoMi .= "Curl error: ".curl_error($ch)."<br>\n";
            
//             $correoMi .= "Enviado a Amadeus por detrás, envío $i - ".$salidaCurl."<br>\n";
//             $i++;
//         }
//         curl_close($ch);
//         $correoMi .= "<br>\nTerminado el envío a Amadeus <br>\n";
// 	}
	
//	envío de correos y voucher este último en caso de pagos online Aceptados
// 	$q = "select nombre, email from tbl_admin where correoT = 1 and idcomercio = '$valores[1]' and activo = 'S'";
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
            Cliente: ".$valores[17]." <br>
            Referencia del Comercio: ".$valores[2]."<br>
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
            $q = "select r.nombre, r.email, a.dominio from tbl_reserva r, tbl_transacciones t, tbl_pasarela p, tbl_agencias a 
            		where r.id_transaccion = '$idtrans' 
            			and r.id_transaccion = t.idtransaccion 
            			and t.pasarela = p.idPasarela
            			and p.idagencia = a.id";
    //echo $q."<br>";
            $temp->query($q);
    //echo "cant=".$temp->num_rows()."<br>";
            if($temp->num_rows() > 0){
            	$correoMi .= "<br>FROM=tpv@".$temp->f('dominio');
            	$correo->from = "tpv@".$temp->f('dominio');
    if (_MOS_CONFIG_DEBUG) echo _ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]."<br>";
    $correoMi .= "<br>\ncurl_init("._ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1].")";
                $ch = curl_init( _ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_POST, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $contents = curl_exec($ch);
                curl_close($ch);

    $correoMi .= "<br>\nCorreo Voucher<br>\n".$contents;
    if (_MOS_CONFIG_DEBUG) echo "voucher=$contents<br>";

                if (strpos($temp->f('email'), ' - ') !== false){
                    $arrCor = explode(" - ", $temp->f('email'));
                    $corr = $arrCor[0];
                } else $corr = $temp->f('email');
    $correoMi .= "<br>\ncorr=$corr";
                $arrayTo[] = array($temp->f('nombre'),$corr);
    // 			$arrayTo[] = array($temp->f('nombre'), $temp->f('email'));
                foreach ($arrayTo as $todale) {
                    if (!strlen($correo->to))
                        $correo->to = $todale[1];
                    else 
                        $correo->set_headers ("Cc: ".$todale[1]);
                }
                $subject = "Voucher";
					
                $q = "select email from tbl_admin a, tbl_colAdminComer o, tbl_comercio c where o.idAdmin = a.idadmin ".
                		" and o.idComerc = c.id and a.idrol in (11,12,14) and a.activo = 'S' and c.idcomercio = ".$valores[1].
                		" order by a.fecha_visita desc limit 0,1";
                $temp->query($q);
//				if (strlen($temp->f('email'))) $correo->reply = $temp->f('email');
				$correoMi .= "<br>El correo se envía con from desde: ".$correo->from;
                $correo->todo(15, $subject, $contents);
                $correo->destroy();
            }
        }
	}
}

//envío de sms
$sql = "select sms, telf, t.ip, t.pasarela, p.LimMinOper, p.LimMaxOper, p.LimDiar, p.LimMens, p.LimAnual, p.LimOperIpDia, p.LimOperTarDia, p.LimOperDia, 
				t.valor_inicial, p.nombre
			from tbl_comercio c, tbl_transacciones t, tbl_pasarela p 
			where t.pasarela = p.idPasarela and t.idcomercio = c.idcomercio and t.idtransaccion = '$idtrans'";
$correoMi .= "<br>\n".$sql."<br>\n";
$temp->query($sql);
$sms 		= $temp->f("sms");
$telf 		= $temp->f("telf"); //borrar el número mío!!!!!!!!!!
$ip 		= $temp->f("ip");
$pasa 		= $temp->f("pasarela");
$minlim 	= number_format($temp->f("LimMinOper"),2);
$maxlim 	= number_format($temp->f("LimMaxOper"),2);
$limdiar 	= number_format($temp->f("LimDiar"),2);
$limen 		= number_format($temp->f("LimMens"),2);
$liman 		= number_format($temp->f("LimAnual"),2);
$operip 	= number_format($temp->f("LimOperIpDia"),2);
$opertarj 	= number_format($temp->f("LimOperTarDia"),2);
$operdia 	= number_format($temp->f("LimOperDia"),2);
$valini 	= number_format($temp->f("valor_inicial")/100,2);
$pasnom 	= $temp->f("nombre");
$arrSale 	= $temp->loadAssocList();
$passe 		= "";
foreach ($arrSale as $key => $value) {
	$passe .= "$key => $value - ";
}
$correoMi .= "<br>\nPasarela:".$pasa."<br>\n";
$correoMi .= "<br>\n".$passe."<br>\n";
$pag = 1;

//envío de avisos de errores por límites
if (strstr($iderror, 'SIS0261')) {
	$etiqueta = "Avisos de Límites alcanzados en TPV";
	$mes = "La operación $idtrans ha pasado a través de la pasarela $pasnom con un monto de $valini<br>\n";
	
	//para el límite mínimo por operación
	$mes .= "El límite mínimo para este TPV es de $minlim<br>\n";
	
	//para el límite máximo por operación
	$mes .= "El límite máximo para este TPV es de $maxlim<br>\n";
	
	//para el límite mínimo por operación
	$q = "select sum(case t.estado
			when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
			when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
			when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
			when 'A' then (t.valor/100/t.tasa)
			else '0.00' end) 'valor'
		FROM tbl_transacciones t
		where t.estado in ('A','V','B','R')
			and t.tipoEntorno = 'P'
			and t.fecha > unix_timestamp('".date('Y')."-".date('m')."-".date('d')." 00:00:00')
			and t.pasarela = ".$pasa;
	$temp->query($q);
	$mes .= "El límite diario para este TPV es de $limdiar y el acumulado hasta ahora es de ".number_format($temp->f('valor'),2)."<br>\n";
	
	//para el número de transacciones desde una misma IP en el día
	$q = "select count(t.idtransaccion) 'total'
			FROM tbl_transacciones t
			where t.estado in ('A','V','B','R')
				and t.tipoEntorno = 'P'
				and t.fecha > unix_timestamp('".date('Y')."-".date('m')."-".date('d')." 00:00:00')
				and t.ip = '$ip'
				and t.pasarela = ".$pasa;
	$temp->query($q);
	$mes .= "El número máximo de operaciones desde una misma IP al día para este TPV es de $operip y en el día de hoy han pasado ".
				number_format($temp->f('total'),2). "<br>\n";
	
	//para el límite mensual
	$q = "select sum(case t.estado 
				when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
				when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
				when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
				when 'A' then (t.valor/100/t.tasa) 
				else '0.00' end) 'valor'
		FROM tbl_transacciones t 
		where t.estado in ('A','V','B','R') 
			and t.tipoEntorno = 'P' 
			and t.fecha > unix_timestamp('".date('Y')."-".date('m')."-01 00:00:00')
			and t.pasarela = ".$pasa;
	$temp->query($q);
	$mes .= "El límite mensual para este TPV es de $limen y el acumulado hasta ahora es de ".number_format($temp->f('valor'),2)."<br>\n";
	
	//para el límite anual
	$q = "select sum(case t.estado 
				when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
				when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
				when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
				when 'A' then (t.valor/100/t.tasa) 
				else '0.00' end) 'valor'
		FROM tbl_transacciones t 
		where t.estado in ('A','V','B','R') 
			and t.tipoEntorno = 'P' 
			and t.fecha > unix_timestamp('".date('Y')."-01-01 00:00:00')
			and t.pasarela = ".$pasa;
	$temp->query($q);
	$mes .= "El límite anual para este TPV es de $liman y el acumulado hasta ahora es de ".number_format($temp->f('valor'),2)."<br>\n";
	
	//número de operaciones al día
	$q = "select count(t.idtransaccion) 'total'
		FROM tbl_transacciones t 
		where t.estado in ('A','V','B','R','D') 
			and t.tipoEntorno = 'P' 
			and t.fecha > unix_timestamp('".date('Y')."-".date('m')."-".date('d')." 00:00:00')
			and t.pasarela = ".$pasa;
	$temp->query($q);
	$mes .= "El número máximo de operaciones al día para este TPV es de $operdia y en el día de hoy han pasado ".
				number_format($temp->f('total'),2). "<br>\n";
	
	$correo->todo(44, $etiqueta, $mes);
	$correoMi .= "<br><br>\n$etiqueta<br>\n$mes";
}

$sql = "select count(*) total from tbl_reserva r, tbl_transacciones t where t.identificador = r.codigo and t.idtransaccion = '$idtrans' and pMomento = 'S'";
$temp->setQuery($sql);
$temp->query();
$correoMi .=  "<br><br>\n".$sql;
$tot = $temp->f('total');
$correoMi .=  "<br>\nTotal=$tot";

if ($total == 1 && $temp->f('pMomento') == 'S') $pag = 0;
$correoMi .=  "<br>\ntot=".$pag;

if ($sms == 1 && $tot != 0 &&  $texto == 'Aceptada') {
$correoMi .= "<br>\nEnviando SMS";
	$arrayDest = explode(',', $telf);
	$importe100 = $importe/100;
	$asunto = "Transacción No: $idtrans $texto valor:$importe100 $monedaNom";
}

if (_MOS_CONFIG_DEBUG) echo $correoMi;
$correo->todo(13,$titulo,str_replace('Flight', 'Flht', str_replace('flight', 'flht', str_replace('vuelo', 'vulo', str_replace('Vuelo', 'Vulo', $correoMi)))));
$q = "insert into tbl_traza (titulo,traza,fecha) values ('$titulo','".html_entity_decode($correoMi, ENT_QUOTES)."',".time().")";
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

