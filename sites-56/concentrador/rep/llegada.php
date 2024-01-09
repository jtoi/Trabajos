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

//xdebug_break();

$miObj = new RedsysAPI;
$temp = new ps_DB;
//$dms=new dms_send;
$correo = new correo();
$ent = new entrada;

#Datos de acceso a la plataforma
//$dms->autentificacion->idcli='126560';
//$dms->autentificacion->username='amfglobalitems';
//$dms->autentificacion->passwd='Mario107';


#id de la pasarela EurocoinPay
$pasaEuroP = 248; #TODO Revisar si este es el id de la pasarela

/*********************************************************************************************************************/
if (stripos(_MOS_CONFIG_DEBUG)) {
	// $_REQUEST['Ds_SignatureVersion'] = 'HMAC_SHA256_V1';
	// $_REQUEST['Ds_MerchantParameters'] = 'eyJEc19EYXRlIjoiMjElMkYwNCUyRjIwMjIiLCJEc19Ib3VyIjoiMTklM0E1NyIsIkRzX1NlY3VyZVBheW1lbnQiOiIxIiwiRHNfQ2FyZF9UeXBlIjoiRCIsIkRzX0V4cGlyeURhdGUiOiIyNDAxIiwiRHNfTWVyY2hhbnRfSWRlbnRpZmllciI6IjMyMmU2YmRkN2QwOTkxN2Y0NGEzN2Y5NGY0ZjFhZDMzNWE2YjM5ZWEiLCJEc19DYXJkX0NvdW50cnkiOiI3MjQiLCJEc19BbW91bnQiOiIyNTAxIiwiRHNfQ3VycmVuY3kiOiI5NzgiLCJEc19PcmRlciI6IjIyMDQyMTE5NTUzNSIsIkRzX01lcmNoYW50Q29kZSI6IjA1OTM4MDc4MiIsIkRzX1Rlcm1pbmFsIjoiMDAxIiwiRHNfUmVzcG9uc2UiOiIwMDAwIiwiRHNfTWVyY2hhbnREYXRhIjoiIiwiRHNfVHJhbnNhY3Rpb25UeXBlIjoiMCIsIkRzX0NvbnN1bWVyTGFuZ3VhZ2UiOiIxIiwiRHNfQXV0aG9yaXNhdGlvbkNvZGUiOiIyNzE5MzYiLCJEc19DYXJkX0JyYW5kIjoiMSIsIkRzX01lcmNoYW50X0NvZl9UeG5pZCI6IjIxMTE3MTg1ODA0NDQ5MyIsIkRzX1Byb2Nlc3NlZFBheU1ldGhvZCI6Ijc4In0=';
	// $_REQUEST['Ds_Signature'] = '6t83_3_k91s_7cJlBk91fx9XqzyDhnOtpHpPxU23eO8=';
	
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
	error_log("recibe la respuesta aca");
	error_log(file_get_contents('php://input'));
}


$lleg .= "Entra<br>\n";

foreach ($d as $key => $value) {
	$lleg .= $key." = ".$value."<br>\n";
}
$correoMi .= $lleg;
$q = "insert into tbl_traza (titulo,traza,fecha) values ('".$titulo." entrada datos','".preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($correoMi, ENT_QUOTES))."',".time().")";
$temp->query($q);

if(isset($d['origen'])){
	$correoMi .= "<br>Respuesta enviada desde rep/index.php";
}

//$correo->todo(13, 'ver otro', $correoMi);

// if (isset($d['Ds_TransactionType'])) $echo = 'hola';

//$handle = fopen("salsa.txt", "w");
//fwrite($handle, "INICIO<br>\n");
if ($d['SHASIGN']){
	$pasarela = 218; //Moneytigo
}elseif ($d['peticion']){
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
    $pasarela = 2; //BiPay
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
} elseif ($d['orderid']){
	$pasarela = 183; //Stripe
} elseif ($d['data'] && $d['sig']){ // EurocoinPay
	$pasarela = $pasaEuroP;
}else {

    if (!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/') && 
                !strstr($_SERVER['DOCUMENT_ROOT'], '/var/www/html') && 
                !strstr($_SERVER['DOCUMENT_ROOT'], '/home/julio/www') && 
                !strstr($_SERVER['DOCUMENT_ROOT'], '/wamp/www/')){
        $correoMi .= "<br>Pasarela invalida";
        $correo->todo(13,$titulo,$correoMi);
        $q = "insert into tbl_traza (titulo,traza,fecha) values ('$titulo','".preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($correoMi, ENT_QUOTES))."',".time().")";
        $temp->query($q);
        exit;
    } else $pasarela = 1;
}

/*****************************Borrame*****************************************************/
/*   $salida = "<tpv><respago><idterminal>999999</idterminal><idcomercio>B9550206800006</idcomercio><nombrecomercio>TRAVELS AND DISCOVERY</nombrecomercio>
				<idtransaccion>450108050011</idtransaccion><moneda>840</moneda><importe>83.64</importe><fechahora>17-03-2013 04:05:33</fechahora>
				<estado>2</estado><coderror>0000</coderror><codautorizacion>063132</codautorizacion><firma>E69F9B0DB8AE40467055B34FB7DD5B8E23975613</firma>
  				</respago></tpv>"; */
/*****************************Borrame*****************************************************/

switch ($pasarela) {
    case 1:
		$va = "BBVA o BBVA3D o BBVA 3D onL";
	break;
	case 2:
		$va = "Sabadel, Caja Madrid, BBVA11 3D, BBVA12 3D, BBVA13 3D, BBVA14 3D, BBVA15 AMEX, Bankias, BiPay";
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
	case 183:
		$va = "Stripe";
	break;
	case 218:
		$va = "Moneytigo";
	break;
	case $pasaEuroP: 
		$va = "EurocoinPay";
	break;
}
$correoMi .= "pasarela->".$va."||<br>\ndesde->".$_SERVER['HTTP_REFERER']."<br>\n";
$cojin = '';

$correoMi .= $salida."||<br>\n";

$str = '';
$dserror = $uspytpv = '';
$firma = false;
$tipoOper = 'P';

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
if ($pasarela == $pasaEuroP) {//EurocoinPay
	include_once '../include/eurocoinpay/api/eurocoinpay-class.php';
	$ecp = new EurocoinPayApi();

	#hasta ahora hay una sola moneda en esta pasarela, por lo que se puede obtener la clave y el terminal directamente
	#si apareciera otra moneda en la pasarela, se debería usar el c�digo de la moneda correspondiente0
	$q = "select terminal, clave FROM tbl_colPasarMon where idmoneda = '978' and estado = 1 and idpasarela = " . $pasaEuroP;
	$temp->query($q);
	$codi = $temp->f('clave');
	$term = $temp->f('terminal');

	$correoMi .= "<br>\nEntra en Eurocoinpay<br>\n";
	$correoMi .= "codi->".$codi."<br>\n";
	$correoMi .= "term->".$term."<br>\n";

	$res = $ecp->cliObtenParametrosPost($d['data'], $d['sig'], $codi);
	logEcp("res:" . var_export ($res,TRUE));
	
	if (!$idtrans = $ent->isNumero($res->order_number,12)) $correoMi .= "No es v�lido el n�mero de la operaci�n {$res->order_number} <br>";
	else {
		if ($res->error == 'OK') { //operación aceptada
		$estado = '2';
		$correoMi .= "Aceptada<br>\n";
		$importe = $res->amount;
		$codautorizacion = $res->operation_id;
		} else {
			$correoMi .= "Denegada<br>\n";
			$estado = '3';
			$importe = null;
			$codautorizacion = null;
			$ok = 1;
			$iderror = $res->error;
		}
	}

	

}elseif ($pasarela == 218) {//Moneytigo
	if (!$idtrans = $ent->isNumero($d['MerchantRef'],12)) $correoMi .= "No es v�lido el n�mero de la operaci�n {$d['MerchantRef']} <br>";
	else {
		$correoMi .= "<br>Entra en Moneytigo<br>";

		$q = sprintf("select t.valor_inicial, t.moneda from tbl_colPasarMon c, tbl_transacciones t where t.idtransaccion = '%s' and t.pasarela = c.idpasarela and c.idmoneda = t.moneda", $idtrans);
		$correoMi .= "<br>$q";
		$temp->query($q);
		$importe = $temp->f('valor_inicial');
		$moneda = $temp->f('moneda');
		$correoMi .= "<br>".$d['Status'];

		if (count($d['moneytigo']) > 2) {
			$aarDaMo = json_decode($d['moneytigo']);
			$moEstado = $aarDaMo->Transaction_Status->State;
			$moBankCod = substr($aarDaMo->Transaction_Status->Bank_Code,3);
			$moCardNumb = $aarDaMo->Card->Number;
			$moCardType = $aarDaMo->Card->Type;
		} else {
			$moEstado = $d['Status'];
			$moBankCod = substr($d['BankTrxID'],3);
			$moCardNumb = $d['CardNumber'];
			$moCardType = $d['CardType'];
		}
		$correoMi .= "<br>ESTADO>".$moEstado;

		if ($moEstado == '2') {//operaci�n Aceptada
			if (!$codautorizacion = $ent->isAlfanumerico($moBankCod,20))$correoMi .= "No es v�lido el n�mero de autorizo ".$d['auth']." <br>";
			$estado = 2;

			$pedazo = "tarjetas = '".$moCardNumb."', identificadorBnco = '".$d['TransId']."', ";
		} else {
			$estado = '3';
			$importe = null;
			$codautorizacion = null;
			$ok = 1;
			$iderror = $moBankCod." ". $aarDaMo->Transaction_Status->Bank_Code_Description;
		}
		$correoMi .= "<br>IDERROR>".$iderror;

	}
} elseif ($pasarela == 183) { //Stripe
	if (!$idtrans = $ent->isNumero($d['orderid'],12)) $correoMi .= "No es v�lido el n�mero de la operaci�n {$d['data']} <br>";
	else {
	$correoMi .= "<br>Entra en Stripe2<br>";
		$q = sprintf("select c.clave, t.valor_inicial, t.moneda from tbl_colPasarMon c, tbl_transacciones t where t.idtransaccion = '%s' and t.pasarela = c.idpasarela and c.idmoneda = t.moneda", $idtrans);
		$correoMi .= "<br>$q";
		$temp->query($q);
		$codigo = $temp->f('clave');
		$importe = $temp->f('valor_inicial');
		$moneda = $temp->f('moneda');
		$correoMi .= "<br>".$d['status'];

		if ($d['status'] == 'succeeded' ) {
			if (!$codautorizacion = $ent->isAlfanumerico($d['auth'],28))$correoMi .= "No es v�lido el n�mero de autorizo ".$d['auth']." <br>";
			$estado = 2;

			// Calculo del hash
			$cade = $d['orderid'].$d['moneda'].$d['importe'].'ao5psiDHTnr6Hb3qZNdT870btgSgWaYz';
			$firma = hash("sha512", $cade);
			$correoMi .= "<br>firmas->$firma==".$d['firma'];

			if ($firma != $d['firma']){
				$correoMi .= "<br>Las firmas no coinciden<br>";

				$correo->todo(13,$titulo,str_replace('Flight', 'Flht', str_replace('flight', 'flht', str_replace('vuelo', 'vulo', str_replace('Vuelo', 'Vulo', $correoMi)))));
				$q = "insert into tbl_traza (titulo,traza,fecha) values ('$titulo','".preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($correoMi, ENT_QUOTES))."',".time().")";
				exit;
			}

			$temp->query("select id from tbl_tarjetas where lower(nombre) like '%".strtolower($d['marca'])."%' limit 0,1");
			$marca = $temp->f('id');
			$correoMi .= "<br>select id from tbl_tarjetas where lower(nombre) like '%".strtolower($d['marca'])."%' limit 0,1";

			$temp->query(sprintf("update tbl_transacciones set tarjetas = '%s', id_tarjeta = '%f' where idtransaccion = '%s'", $d['tarjeta'], $marca, $idtrans));
			$correoMi .= "<br>".sprintf("update tbl_transacciones set tarjetas = '%s', id_tarjeta = '%f' where idtransaccion = '%s'", $d['tarjeta'], $marca, $idtrans);

		} else {
			$iderror = $d['mensaje']. " - ". $d['code'];
			$importe = 0;
			$estado = 3;
			$coderror = $d['code'];
		}
		$correoMi .= "<br>codigo=$codigo";
		$correoMi .= "<br>importe=$importe";
		$correoMi .= "<br>moneda=$moneda";
		$correoMi .= "<br>estado=$estado<br>";
	}
} elseif ($pasarela == 115) { //Papam
	if (!$idtrans = $ent->isNumero($d['data'],12)) $correoMi .= "No es v�lido el n�mero de la operaci�n {$d['data']} <br>";
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
		// 	$error = 'No validada la operaci�n';
		// 	error_log($error);
		// 	$correoMi .= $error;
		// 	$correo->todo(13,$titulo,$correoMi);
		// 	exit;
		// }

		if ($d['result'] == 'OK' ) {
			$estado = 2;
		} else {
			if(isset($d['errors'])){
				$iderror = $d['errors'][0]['message'];
				$importe = 0;
				$estado = 3;
				$coderror = null;
			} else{
				$iderror = "282 - Abandoned transaction";
				$importe = 0;
				$estado = 3;
				$coderror = 282;
			}
		}
	}
} elseif ($pasarela == 92) { //Xilema
	$correoMi .= "Entra en pasarela Xilema||<br>\n";
//	error_log("operacion=".$d['trx']->reference);
//	error_log("errrrroooorr=".$d['errors'][0]['message']);
//	error_log('status='.$d['status']);
//	error_log("operacion=".$d['trx']['reference']);
	$correoMi .= "Datos de Xilema<br>";
	$correoMi .= json_encode($d). "<br><br>";
	foreach ($d['trx'] as $value => $item) {
		$correoMi .= $value . "=" . $item . "<br>\n";
	}
	if (!$idtrans = $ent->isNumero($d['trx']['reference'],12)) $correoMi .= "No es v�lido el n�mero de la operaci�n ".$d['trx']['reference']." <br>";
//	error_log("idtrans=$idtrans");
	
	$q = "select moneda, tipoOperacion from tbl_transacciones where idtransaccion = ".$idtrans;
	$correoMi .= "$q<br>\n";
	$temp->query($q);
	$tipoOper = $temp->f('tipoOperacion');
	$moneda = $temp->f('moneda');
	if ($d['status'] == 'Error' || $d['status'] == 'Denied') {// la operaci�n vino con error
		$correoMi .= "<br>entra aca<br>";
		$iderror = $d['errors'][0]['message'];
		$importe = 0;
		$estado = 3;
		$coderror = $d['trx']['actionCode'];
		if (stripos($iderror,'createHangUpError') > 1 ) {$coderror = '2012'; $iderror = '';}
		elseif (stripos($iderror,'Transactions DAO') > 1) {$coderror = '5559'; $iderror = '';}
		elseif (stripos($iderror,'There was an error with communications with the acquirer') > 1) {$coderror = '5557'; $iderror = '';}
		elseif (stripos($iderror,'object is not valid. &quot;PAN&quot;') > 1) {$coderror = '2011';  $iderror = ''; $dserror = '';}
		elseif (stripos($iderror,'Status: 401') > 1) {$coderror = '5558'; $iderror = '';}
		elseif (stripos($iderror,'Status: 500') > 1 ) {$coderror = '5555'; $iderror = '';}
	} else {
		if (!$codautorizacion = $ent->isAlfanumerico($d['trx']['authCode'],6))$correoMi .= "No es v�lido el n�mero de autorizo ".$d['trx']['authCode']." <br>";
		if (!$importe = $ent->isNumero($d['trx']['amount'], 11)) $correoMi .= "No es importe v�lido ".$d['trx']['amount']." <br>";
		$importe = $importe*100;
//		error_log("moneda=$moneda");
//		error_log("importe=$importe");
//		error_log("codautorizacion=$codautorizacion");
		
    	$pedazo = "tarjetas = '".$d['customer']['card']['PANObfuscated']."', identificadorBnco = '". $d['trx']['datetime'] . "&" . $d['customer']['card']['token'] . "&". $d['id'] ."', ";
//		error_log('pedazo='.$pedazo);
		$estado = 2;
	}
	$correoMi .= "coderror=$coderror<br>";
	$correoMi .= "id_error=$id_error<br>";
	$correoMi .= "dserror=$dserror<br>";
	
} elseif ($pasarela == 71) { //PayTpv nuevo
	$correoMi .= "Entra en pasarela PayTpv nuevo||<br>\n";
	if (strpos($d['TransactionName'], 'evoluci') > 0 && $d['TransactionType'] == '2') exit;
	if (!$idtrans = $ent->isNumero($d['Order'],12)) $correoMi .= "No es v�lido el n�mero de la operaci�n {$d['Order']} <br>";
	else {
		$q = "select idmoneda from tbl_moneda where moneda = '".$d['Currency']."'";
		$temp->query($q);
		$correoMi .= "$q<br>\n";
		$importe = $d['Amount'];
		$moneda = $temp->f('idmoneda');

        $q = "select p.idcenauto from tbl_transacciones t, tbl_pasarela p where t.pasarela = p.idPasarela and t.idtransaccion = '$idtrans'";
        $correoMi .= "$q<br>\n";
        $temp->query($q);
        if ($temp->f('idcenauto') == 21) $pasarela = 21;
		
		if ($d['Response'] == 'KO') {//la pasarela devolvi� error
			$iderror = $d['ErrorID']." - ".$d['ErrorDescription'];
			$importe = 0;
			$estado = 3;
			$coderror = $d['ErrorID'];
		} else {
			$correoMi .= "La operaci�n est� Aceptada, salvo iduser y token<br>\n";
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
//	    	$correoMi .= "<br><br>\n\nError en la comprobaci�n de la firma en Wirecard";
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

} elseif ($pasarela == 2) { //Pasarela Sabadel, caja madrid, caixa, BBVA11 3D, Bankia3, BBVA12 3D, BBVA13 3D, BBVA14 3D, BBVA15 AMEX Bankia, BiPay
	$correoMi .= "Entra en pasarela Bipay, Sabadel, caja madrid, caixa, BBVA11 3D, Bankia3, BBVA12 3D, BBVA13 3D, BBVA14 3D, BBVA15 AMEX, Bankia<br>\n";
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
	if (stripos($respuesta, 'SIS') > -1) {
		$coderror = str_replace('SIS', '', $respuesta)*1;
        $error = $respuesta;
    } else {
		$coderror = $respuesta;
    	$error = (int)$respuesta;
	}
    $terminal = $d['Ds_Terminal'];

    $query = "select tipoEntorno, pasarela, idcenauto from tbl_transacciones t, tbl_pasarela p where p.idPasarela = t.pasarela and t.idtransaccion = '$idtrans'";
	$temp->query($query);
	if ($temp->f('idcenauto') == 20) $pasarela = 60;
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

    if ($coderror < 100 && strlen($d['Ds_AuthorisationCode']) > 3) {
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

    $q = "select moneda, estado, tarjetas, pasarela, tipoOperacion from tbl_transacciones where idtransaccion = '$idtrans'";
    $temp->query($q);
    $moneda = $temp->f('moneda');
    $estaIn = $temp->f('estado');
    $tarja = $temp->f('tarjetas');
	$TefPasa = $temp->f('pasarela');
	$tipoOper = $temp->f('tipoOperacion');
    $paseEst = "'P','D','A','N'";
    if ($TefPasa != 37) $paseEst = "'P','D','N'";
	$cojin = " and estado = 'A'";
	$correoMi .= strlen($d['Ds_AuthorisationCode'])."<br>\n";
    if ($d['Ds_Code'] == 100 && strlen($d['Ds_AuthorisationCode']) > 3) {// operaci�n Aceptada
    	$correoMi .= "Operaci�n Aceptada<br>\n";
    	$estado = '2';
    	
    	if ($d['Ds_Amount']) $importe = $d['Ds_Amount'];
    	elseif ($d['Ds_Merchant_Amount']) $importe = $d['Ds_Merchant_Amount'];
    	
    	$pedazo = "tarjetas = '************".$d['Ds_PanMask']."', identificadorBnco = '".$d['Ds_Date']."', ";
    	
    } elseif ($d['Ds_Code'] == 703 ) {// operaci�n en Dudas
    	$correoMi .= "Operaci�n en Dudas con 703<br>\n";
//     	if ($estaIn == 'A') { //Si estaba antes Aceptada sigue Aceptada
//     		$correoMi .= "Operaci�n Denegada por Titanes<br>\n";
// 	    	$estado = '2';
	    	
// 	    	if ($d['Ds_Amount']) $importe = $d['Ds_Amount'];
// 	    	elseif ($d['Ds_Merchant_Amount']) $importe = $d['Ds_Merchant_Amount'];
	    	
// 	    	$pedazo = " tarjetas = '**** **** **** ".$d['Ds_PanMask']."', ";
//     	} else {
    		$correoMi .= "Operaci�n Denegada por Titanes<br>\n";
    		$estado = '3';
    		$importe = 0;
    		$codautorizacion = null;
    		$ok = 1;
    		$iderror = $d['Titanes_Description']." ".$d['Titanes_Messages'];
    		$coderror = $d['Ds_Code'];
//     	}
    	$correo->todo(51, "Operaci�n con Error 703 de Tefpay", $correoMi);
    	
    } elseif ($d['Ds_Code'] == 700) {// operaci�n Aceptada
    	$correoMi .= "Operaci�n Aceptada<br>\n";
    	$estado = '2';
    	
    	if ($d['Ds_Amount']) $importe = $d['Ds_Amount'];
    	elseif ($d['Ds_Merchant_Amount']) $importe = $d['Ds_Merchant_Amount'];
    	
    	$pedazo = "tarjetas = '************".$d['Ds_PanMask']."', identificadorBnco = ".$d['Ds_Date'].", ";
    	
    	if (isset($d['Titanes_OrderId']) && $d['Titanes_OrderCode'] != 3) { //denegada por Titanes aunque de Tefpay viene Aceptada
    	$correoMi .= "Operaci�n Denegada por Titanes<br>\n";
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
    } else { //operaci�n denegada
		$correoMi .= "Operaci�n Denegada por TefPay<br>\n";
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
    
    // if ($d['Titanes_OrderId']) {
    // 	$q = "update tbl_aisOrden set titOrdenId = '{$d['Titanes_OrderId']}' where idtransaccion = '$idtrans'";
    // 	$temp->query($q);
    // }
    
    if ($estado == '2' && $TefPasa == '37'){
		include_once '../admin/classes/tcpdf/config/tcpdf_config.php';
		include_once '../admin/classes/tcpdf/tcpdf.php';
		creatitVou($idtrans); //est� Aceptada, genero voucher y lo env�o a Titanes
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
//	$iderror = $coderror." ".$dserror; Reina
	$iderror = $coderror." ".$deserror;

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
	echo '<HTML><HEAD><TITLE>Respuesta correcta a la comunicaci�n ON-LINE</TITLE></HEAD><BODY>$*$OKY$*$</BODY></HTML>'; //Confirmando al TPV la llegada de la respuesta
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
	$importe = $importe*1;
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
	$importe = $importe*1;
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
	$importe = $importe*1;
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
		
	} else {
		$estado = '3';
		$importe = null;
		$codautorizacion = null;
		$ok = 1;
	}
	$correoMi .= "Ds_TransactionType=".$decodec->Ds_TransactionType."\n<br>
					Ds_Merchant_Identifier=".$decodec->Ds_Merchant_Identifier."\n<br>
					Ds_ExpiryDate=".$decodec->Ds_ExpiryDate."\n<br>
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
//	$comprueba = 'hola'; temporal - Reina

	if (strlen($decodec->Ds_Merchant_Identifier) > 10) {//pagos por referencia
		$q = "select identificador, idcomercio, pasarela from tbl_transacciones where idtransaccion = '".$decodec->Ds_Order."'";
		$correoMi .= $q."<br>";
		$temp->query($q);

		if ($temp->num_rows() > 0) {
			$comRef = $temp->f('idcomercio');
			$ideRef = $temp->f('identificador');
			$idpasaa = $temp->f('pasarela');

			$q = "select count(*) total from tbl_referencia where codBanco = '".$decodec->Ds_Merchant_Identifier."' and idpasarela = '$idpasaa'";
			$temp->query($q);

			if ($temp->f('total') == 0) {
				$correoMi .= "FECHA VENCIMIENTO = 01/".substr($decodec->Ds_ExpiryDate,2)."/".substr($decodec->Ds_ExpiryDate,0,2)."<br>";
				$fvenc = mktime(0, 0, 0, substr($decodec->Ds_ExpiryDate,2), 1, "20".substr($decodec->Ds_ExpiryDate,0,2));
				$correoMi .= "fvenc=$fvenc=mktime(0, 0, 0, ".substr($decodec->Ds_ExpiryDate,2).", 1, 20".substr($decodec->Ds_ExpiryDate,0,2).")<br>";

				$correoMi .= "cadena->{$decodec->Ds_Order}.{$decodec->Ds_AuthorisationCode}.$comRef.$ideRef<br>";
				$identConc = hash("sha1",$decodec->Ds_Order.$decodec->Ds_AuthorisationCode.$comRef.$ideRef);

				$q = "insert into tbl_referencia (idtransaccion, idpasarela, codBanco, codConc, fechavig, fecha ) values ('{$decodec->Ds_Order}', '$idpasaa', '{$decodec->Ds_Merchant_Identifier}', '$identConc', $fvenc, unix_timestamp())";
				$correoMi .= $q."<br>";
				$temp->query($q);
			}
		} else $correoMi .= "La operaci�n no aparece en transacciones.<br>";
	}
	
}

$correoMi .= "$comprueba=$firma||<br>\n";

if ($comprueba == $firma||1==1||$ok=1) {
	
	if ($comprueba != $firma && $ok=0) {
		$correoMis =  "<br>\nNo concuerda la firma=".$firma."<br>\ncon la comprobaci�n=$comprueba realizada<br>\npara la operaci�n $idtrans<br>\n";
		$correoMi .= $correoMis;
		$correo->todo(13,"Fallo en firma de la operaci�n",$correoMis);
	}
	
	if ($estado == '') $estado = '4';
    $firma = true;
	$tipoError = '0';
$correoMi .=  "<br>\nfirma=".$firma."||<br>\n";

$correoMi .=  "<br>\ncoderror=".$coderror."||<br>\n";

	$correoMi .= "tipoerror=$tipoError<br>";
	$correoMi .= "coderror=$coderror<br>";
$correoMi .= "id_error=$id_error<br>";
$correoMi .= "dserror=$dserror<br>";
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
			$sql = "select id_error, texto, idtipo from tbl_errores where codigo = '$error' and idpasarela = $psrl";
			$correoMi .=  "<br>\n".$sql;
	// 		$temp->setQuery();
			$temp->query($sql);
			if (!$temp->num_rows() && $error != 0) {
				$correo->todo(12, 'No existe el error en la BD', "La operaci�n $idtrans devolvi� un c�digo de error $error en el terminal $psrl del TPV $va");
			} else {
				$iderror = $iderror ." ". $error." - ".$coderror." - ".$temp->f("texto")." ".$dserror;
				$tipoError = $temp->f('idtipo');
			}
			
			$errorAMF = $temp->f("id_error");
		}
		if ($estado == ' ' && $pasarela == 1) $estado = 3;
	}
	$correoMi .= "\n<br>moneda=$moneda\n<br>";
	
//	busca la conversion de moneda
	if (strlen($moneda) >0 ) {
		$q = "select moneda, factmult from tbl_moneda where idmoneda = ".$moneda;
		$correoMi .=  "<br>".$q;
		$temp->query($q);
		$mon = $temp->f('moneda');
		$factmult = $temp->f('factmult');
		if ($moneda == '978') $cambioRate = 1;
		else {
			$cambioRate = leeSetup ($mon);
			(date('G') < 14) ? $f = date('dmy',strtotime('-1 day')) : $f = date('dmy');
			$temp->query("select count(*) total from tbl_setup where nombre = '$mon' and from_unixtime(fecha, '%d%m%y%H%i') like '".$f."140%'");
			error_log("select count(*) total from tbl_setup where nombre = '$mon' and from_unixtime(fecha, '%d%m%y%H%i') like '".$f."140%'");
			if ($temp->f('total') == 0) {
				$temp->query("select from_unixtime(fecha, '%d%m%y %H:%i:%s') 'fecha' from tbl_setup where nombre = '$mon' and from_unixtime(fecha, '%d%m%y%H%i') like '".$f."140%'");
				//$correo->todo(2,'Cambio en la tabla setup','Se cambi� la fecha de la moneda '.$mon.' antes de tiempo ahora tiene la fecha '.$temp->f('fecha').' y el valor ahora es '.$cambioRate."<br>select from_unixtime(fecha, '%d%m%y %H:%i:%s') 'fecha' from tbl_setup where nombre = '$mon' and from_unixtime(fecha, '%d%m%y%H%i') like '".$f."140%'");
			}

			// $temp->query("select distinct truncate(tasa,4) 'tasa' from tbl_transacciones where moneda = $moneda and tipoOperacion = 'P' and estado = 'A' and idtransaccion > ".date('ymd')."140400 and idcomercio != '527341458854'");
			// error_log("select distinct truncate(tasa,4) from tbl_transacciones where moneda = $moneda and tipoOperacion = 'P' and estado = 'A' and idtransaccion > ".date('ymd')."140400 and idcomercio != '527341458854'");
			// error_log("cambioRate=".$cambioRate);
			// error_log("number_format=".number_format($cambioRate, 4, '.',''));
			// error_log("tasa=".$temp->f('tasa'));
			// error_log("number_format=".number_format($temp->f('tasa')));
			// if ($temp->num_rows() == 1 && number_format($cambioRate, 4, '.','') != $temp->f('tasa')) {
			// 	$correo->todo(2,'Cambio de tasa '.date('d/m/Y H:i:s'),'Se cambi� la tasa con la que se ven�a registrando la moneda '.$mon.' antes era de '.$temp->f('tasa').' y el valor ahora es '.$cambioRate."<br>select tasa from tbl_transacciones where moneda = $moneda and tipoOperacion = 'P' and estado = 'A' and idtransaccion > ".date('ymd')."140400 and idcomercio != '527341458854'");
			// } else if ($temp->num_rows() > 1){
			// 	$correo->todo(2,'Mas de una tasa anterior '.date('d/m/Y H:i:s'),'Se obtiene mas de una tasa ('.$temp->num_rows().') con la que se ven�a registrando la moneda '.$mon.' correr el siguiente select'."<br>select tasa from tbl_transacciones where moneda = $moneda and tipoOperacion = 'P' and estado = 'A' and idtransaccion > ".date('ymd')."140400 and idcomercio != '527341458854'");
			// }
		}
	}
	
$correoMi .=  "<br>\ncambioRate=".$cambioRate;
$correoMi .=  "<br>\niderror=".$iderror;
$correoMi .=  "<br>\nestado=".$estado;
$correoMi .=  "<br>\nerrorAMF=".$errorAMF;
$correoMi .=  "<br>\factmult=".$factmult;

//echo $correoMi;
	$est = "X";
	$q = "select estado, from_unixtime(fecha_mod,'%d/%m/%Y %H:%i:%s') fe, codigo, idcomercio, directa, identificador, tipoPago from tbl_transacciones where idtransaccion = '$idtrans'";
	$correoMi .=  "<br>\n".$q."<br>\n";
	$temp->query($q);
	$estadoOp = $est = $temp->f('estado');
	$fe = $temp->f('fe');
	$code = $temp->f('codigo');
	$comerid = $temp->f('idcomercio');
	$directa = $temp->f('directa');
	$refComercio = $temp->f('identificador');
	$tipoPago = $temp->f('tipoPago');

	$correoMi .=  "<br>\nEstado Op=".$estadoOp."<br>\n";
	$correoMi .=  "<br>\nRespuesta Directa=".$directa."<br>\n";
	
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
	
	if (($comerid == '163430526040' || $comerid == '169453189889') && $moneda == '840') {//Si el comercio es Tocopay le afecto la tasa de cambio
		$cambioRate = leeSetup('cambioCimex');
		$correoMi .=  "<br>\ncambioRateTocopay=".$cambioRate;
	}

	if (($comerid == '166975114294' || $comerid == '167707944853') && $moneda == '840') { //Si el comercio es Vidaipay o Vidaipay Bolsa y la moneda es USD, la tasa de cambio esta en setup
		$cambioRate = leeSetup('cambioVidaipay');
		$correoMi .=  "<br>\ncambioRateVidaipay=".$cambioRate;
	}

	if ($comerid == '160253960650' && $moneda == '840') { //Si el comercio es Etecsa Bolsa y la moneda es USD, la tasa de cambio esta en setup
		$cambioRate = leeSetup('cambioBolsa');
		$correoMi .=  "<br>\ncambioRateBolsa=".$cambioRate;
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
        $correoMi .= "La operaci�n estaba con estado $est a las $fe y se volvi� a recibir informaci�n del Banco como $estadoC, no se reliza ninguna acci�n en el Concentrador ni se env�an datos a los ".
                "comercios<br>";
        // $salta = true; //Cambiado por Umbrella que no recib�a este primer llamado cuando la operaci�n se actualizaba con el segundo llamado
    } elseif ( 
            (($est == "N" || $est == "D") && ( $estado == '2')) ||
            ($est == "P") ||
    		($pasarela == 3 )
            ) {
		$query = "update tbl_transacciones set ";
		switch ($estado) {
			case '2': //Aceptada
				if ($tipoOper == 'P') $estado = 'A'; elseif ($tipoOper == 'A') $estado = 'E';
				$query .= " codigo = '$codautorizacion', valor = ($importe*$factmult), id_error = null, tasa = ".$cambioRate.", 
					euroEquiv = ($importe/100*$factmult)/($cambioRate), ".$pedazo;
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
		$query .= " estado = '$estado', fecha_mod = ".time()." where idtransaccion = '$idtrans' ";

		// verificar si se ejecuta la consulta para actualizar la operacion segun el estado en que se encuentre
		$correoMi .=  "<br>\n"."Verificar si se ejecuta la consulta para actualizar la operacion segun el estado en que se encuentre.";
		$actualizaOp = false;
		if(in_array($estadoOp, array('P', 'D', 'N'))){
			$actualizaOp = true;

			if($estadoOp === 'D'){
				if($estado === 'A') {
					// hay que ver si el comercio quiere recibir la respuesta de Aceptada
					// Solo se permite si el pago es Diferido y generado en el Concentrador
					$q = "select count(*) total from tbl_reserva where codigo = '$refComercio' and id_comercio = '$comerid'";
					$correoMi .= "query=$q<br>\n";
					$temp->query($q);

					if ($temp->f('total') > 0 && $tipoPago === 'D') {    //la transaccion ha sido realizada directamente en el concentrador
						$correoMi .= "<br>\n" . "Pago Diferido desde el Concentrador. Se notifica la Aceptada despues de Denegada";
						$recibeAceptada = true;
					} else {
						$correoMi .= "<br>\n" . "Hay que ver si el comercio quiere recibir la respuesta de Aceptada.";
						$q = "select AdespuesD from tbl_comercio where idcomercio = '$comerid'";
						$correoMi .= "<br>\n" . $q;
						$temp->query($q);

						$recibeAceptada = $temp->f('AdespuesD');
						$correoMi .= "<br>\nRecibe Aceptada despues Denegada=" . $recibeAceptada . "<br>\n";
					}
					$actualizaOp = $recibeAceptada;

					// Alertar los casos que no se acepta Aceptada despues de Denegada para que se devuelvan en el TPV
					if (!$recibeAceptada) {
						$lab = 'Recibida transacci�n Aceptada duplicada que debe devolverse!!!';
						$mes = "La transacci�n $idtrans estaba en la base de datos con el estado $estadoOp y se recibi� con estado $estado";
						$mes .= "<br>\n" . "Devolver en TPV!!! El comercio no acepta la segunda respuesta.";
						sendTelegram($lab . "<br>$mes", null, 'RL');
					}
				} elseif ($estado === 'D'){
					$actualizaOp = false;
				}
			}
			$query .= " and estado in ($paseEst)";
		} else{
			// Verificar si fue que entro primero por rep/index porque llego primero la respuesta del navegador
			if($estadoOp === 'A' && !$directa){
				$actualizaOp = true;
			}
		}
		$correoMi .=  "<br>\nEjecuta update transaccion=".$actualizaOp."<br>\n";
		if($actualizaOp){
			$correoMi .=  "<br>\n".$query;
			$temp->query($query);

			$query = "update tbl_transacciones set directa = 1 where idtransaccion = '$idtrans'";
			$correoMi .=  "<br>\n".$query;
			$temp->query($query);
		}

        $q = "select fecha_mod, tipoEntorno, (valor*$factmult/100) val, idcomercio, identificador from tbl_transacciones where idtransaccion = '$idtrans'";
        $temp->query($q);
		$elcomercio = $temp->f('idcomercio');

//		if($estadoOp != 'A'){
			$referencia = leeSetup('refOpPruebas');
			if($temp->f('identificador') != $referencia) {
				if($actualizaOp) {
					//Actualiza la tabla de las reservas con el resultado de la transaccion
					$query = "update tbl_reserva set id_transaccion = '" . $idtrans . "', bankId = '" . $codautorizacion . "', fechaPagada = " . $temp->f('fecha_mod') . ",
								estado = '" . $estado . "', est_comer = '" . $temp->f('tipoEntorno') . "', valor = " . $temp->f('val') . "
							where codigo = '" . $temp->f('identificador') . "' and estado in ($paseEst) and id_comercio = " . $temp->f('idcomercio');
					//	echo $query;
					$temp->query($query);
					$correoMi .= "<br>\n" . $query . "\n<br><br>\n";

					if ($estado == 'A' || $estado == 'R') {
						$temp->query("update tbl_transacciones set codigo = '$codautorizacion', estado = 'A' where idtransaccion = $idtrans");
						$temp->query("update tbl_reserva set bankId = '" . $codautorizacion . "', estado = 'A' where id_transaccion = $idtrans");

						//Si la operaci�n fu� Aceptada actualizo la operaci�n en la tabla de los lotes si existe
						$temp->query("select lotes, id from tbl_comercio where idcomercio = '$elcomercio'");
						if ($temp->f('lotes') == 1) { // si el comercio tiene permitidas operaciones por lotes
							$temp->query("select idlote, valor, moneda from tbl_reserva where id_transaccion = $idtrans");
							if ($temp->f('idlote') > 0) {
								$q = "update tbl_lotes set valor = " . ($temp->f('valor') * 100) . ", moneda = " . $temp->f('moneda') . " fechaLanz = unix_timestamp(), valida = 0 where id = " . $temp->f('idlote');
								$temp->query("update tbl_lotes set valor = " . ($temp->f('valor') * 100) . ", moneda = " . $temp->f('moneda') . ", fechaLanz = unix_timestamp(), valida = 0 where id = " . $temp->f('idlote'));
							}
						}
					}
				}
			}
//		}
	}

	// Es que NO es la primera vez que entra por aqui
	if($directa){
		//Aviso de transacci�n duplicada desde el Banco
		if ($est == "N" || $est == "D" || $est == "A" || $est == "B" || $est == "V") {
			$enviaTelegram = false;

			$lab = 'Recibido datos duplicados desde el banco';
			$mes = "fecha=".date('d/m/Y H:i:s')."<br>\n"."Se han recibido duplicado los datos de la transacci�n $idtrans. La misma estaba en la base de datos con el estado $est el $fec ". "y se recibi� con estado $estadoC";
			$correo->todo (20, $lab, $mes);

			if ($est == 'A' && $estadoC == 'A') {
				$lab = 'Recibida transacci�n Aceptada duplicada desde el banco';

				$mes = "Fecha=".date('d/m/Y H:i:s')."<br>\n"."Se han recibido duplicado los datos de la transacci�n $idtrans."."<br>\n";
				if($codautorizacion !== $code){
					$q = "select estado from tbl_comercio where idcomercio = '$comerid'";
					$correoMi .= "<br>\n" . $q;
					$temp->query($q);
					$estadoComercio = $temp->f('estado');

					$enviaTelegram = ($estadoComercio == 'P');	// solo se envia por Telegram si el comercio esta en Produccion
					$mes .= "Se ha recibido con c�digo de autorizo diferente!!! Anteriormente ten�a $code y ahora recibimos $codautorizacion. Se debe revisar si se devuelve.";
				} else{
					$mes .= "Se ha recibido con igual c�digo de autorizo ($codautorizacion).";
				}
				if (!isset($TefPasa)){
					$correo->todo (20, $lab, $mes);
				}
			}
			$correoMi .= "<br>\n".$mes."<br>\n";

			if($enviaTelegram) sendTelegram($lab."<br>$mes",null, 'RL');
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

//	if($estadoOp != 'A') {
	// Si es la primera vez que entra por aqui o la operacion fue actualizada en BDatos, se notifica al comercio
	if(!$directa || $actualizaOp){
		$correoMi .=  "<br>\nSe notifica al comercio<br>\n";

		//Lee los datos de la transacci�n
		$query = "select idtransaccion, t.idcomercio, identificador, codigo, idioma, fecha_mod, valor, moneda, t.estado, c.nombre, 
					c.url, t.tipoEntorno, t.valor/100 precio, c.url_llegada, p.nombre, (select a.nombre from tbl_agencias a where p.idagencia = a.id) comercio, 
					(select idusrToken from tbl_usuarios u where u.idtransaccion = t.idtransaccion) usuario,
					(select nombre from tbl_reserva r where r.id_transaccion = t.idtransaccion) usr, 
					t.sesion, c.resp, t.bipayId, t.pasarela, t.id_tarjeta
				from tbl_transacciones t, tbl_comercio c, tbl_pasarela p
				where t.idcomercio = c.idcomercio
					and p.idPasarela = t.pasarela
					and idtransaccion = '$idtrans'";
		$temp->query($query);
		$valores = $temp->loadRow();
		$comercioN = $valores[9];
		$correoMi .= "<br>\n" . $query;

		//Env�o al sitio del cliente de la info de la transacci�n
		if (count($valores) > 0) {  // Se encontro la operacion registrada
			// Se busca si la operacion tiene registrada url de llegada
			$q = "select urlLlegada as url from tbl_ComerTransUrl where idcomercio = '$valores[1]' and idOperacion = '$valores[2]'";
			$temp->query($q);
			$correoMi .= "\n<br>Se busca si la la operacion tiene registrada url de llegada <br>\n";
			$correoMi .= "$q<br>\n";
			if ($temp->num_rows() > 0 && strlen($temp->f('url')) > 0) {
				$valores[13] = $temp->f('url');
			}
			$correoMi .= "urlLlegada = '$valores[13]' <br>\n";
		}

		$q = "select id_reserva from tbl_reserva where id_transaccion = '$valores[0]'";
		$temp->query($q);

		//el pago es atrav�s de web y el sitio solicita env�o directo de datos
		if (($temp->num_rows() == 0) && (strlen($valores[13]) > 1) && $salta == false) {
			if(isset($valores[20])){	// es una operacion BiPay
				$firma = convierte256($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));
			} else{
				if (strlen($valores[18]) == 32)
					$firma = convierte($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));
				else
					$firma = convierte256($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));
			}
			if (strlen($firma) > 2) {
				$correoMi .= "<br>firma={$valores[1]}, {$valores[2]}, {$valores[6]}, {$valores[7]}, {$valores[8]},	{$valores[0]}, " .
					date('d/m/y h:i:s', $valores[5]) . "<br>\n";
				//			$iderror = urlencode($iderror); Reina
				$iderror = $iderror;
				$correoMi .= "<br>valores19=" . $valores[19];
				if ($valores[19] == 0
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

					$cadenaEnv = "?" . "comercio=" . $valores[1] . "&transaccion=" . $valores[2] . "&importe=" . $valores[6] .
						"&moneda=" . $valores[7] . "&resultado=" . $valores[8] . "&codigo=" . $valores[0] . "&idioma=" . $valores[4] .
						"&firma=$firma&fecha=" . urlencode(date('d/m/y h:i:s', $valores[5])) . "&error=" . urlencode($iderror) . "&tasa=$cambioRate" .
						"&comerc=" . urlencode($valores[15]) . "&usuario=" . urlencode($valores[16]) . "&autorizo=" . $codautorizacion . "&tipoerror=" . $tipoError;

					if(isset($valores[20])){    // es una operacion BiPay
						$cadenaEnv .=  "&bipay=".$valores[20]."&pasarela=" . $valores[21]."&tarjeta=" . $valores[22];
					}
					$cadenaEnvia = $valores[13] . $cadenaEnv;
					if (strlen($identConc) > 10) $cadenaEnvia .= "&identConc=$identConc";
					$correoMi .= $cadenaEnvia . "<br>\n";

					// remplaza las cascaras que agrega la funcion urlencode
					$strValores = str_replace('%2F', '/', $cadenaEnvia);
					$strValores = str_replace('%3A', ':', $strValores);
					$strValores = str_replace('+', ' ', $strValores);
					$correoMi .= '<br> Valores limpios: ' . $strValores . "<br>\n";

					$ch = curl_init($cadenaEnvia);

					curl_setopt($ch, CURLOPT_HEADER, false);
					curl_setopt($ch, CURLOPT_POST, false);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$output = curl_exec($ch);
					$curl_info = curl_getinfo($ch);
					// 						echo "error=".curl_errno($ch);
					if (curl_errno($ch)) $correoMi .= "Error en la comunicaci�n al comercio:" . curl_error($ch) . "<br>\n";
					$crlerror = curl_error($ch);
					// 						echo "otroerror=".$crlerror;
					if ($crlerror) {
						$correoMi .= "La comunicaci�n al comercio ha dado error:" . $crlerror . "<br>\n";
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
						"comercio" => $valores[1],
						"transaccion" => $valores[2],
						"importe" => $valores[6],
						"moneda" => $valores[7],
						"resultado" => $valores[8],
						"codigo" => $valores[0],
						"idioma" => $valores[4],
						"firma" => $firma,
						"fecha" => urlencode(date('d/m/y h:i:s', $valores[5])),
						"error" => urlencode($iderror),
						"comerc" => $valores[15],
						"usuario" => $valores[16],
						"tasa" => $cambioRate,
						"autorizo" => $codautorizacion,
						"tipoerror" => $tipoError
					);

					//env�a identificador para operaciones de pagos por referencia cuando se genere
					if (strlen($identConc) > 10) {
						$data = array(
							"comercio" => $valores[1],
							"transaccion" => $valores[2],
							"importe" => $valores[6],
							"moneda" => $valores[7],
							"resultado" => $valores[8],
							"codigo" => $valores[0],
							"idioma" => $valores[4],
							"firma" => $firma,
							"fecha" => urlencode(date('d/m/y h:i:s', $valores[5])),
							"error" => urlencode($iderror),
							"comerc" => $valores[15],
							"usuario" => $valores[16],
							"tasa" => $cambioRate,
							"autorizo" => $codautorizacion,
							"tipoerror" => $tipoError,
							"identConc" => $identConc
						);
					}
					if(isset($valores[20])){    // es una operacion BiPay
						$data['bipay'] = $valores[20];
						$data['pasarela'] = $valores[21];
						$data['tarjeta'] = $valores[22];
					}
					$options = array(
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_SSL_VERIFYPEER => false,
						CURLOPT_POST => true,
						CURLOPT_VERBOSE => true,
						CURLOPT_URL => $valores[13],
						CURLOPT_POSTFIELDS => $data
					);
					$correoMi .= "<br><br>
						CURLOPT_RETURNTRANSFER	=> true,<br>
						CURLOPT_SSL_VERIFYPEER	=> false,<br>
						CURLOPT_POST			=> true,<br>
						CURLOPT_VERBOSE			=> true,<br>
						CURLOPT_URL				=> " . $valores[13] . ",
						CURLOPT_POSTFIELDS		=> " . http_build_query($data) . "<br>";

					// remplaza las cascaras que agrega la funcion urlencode
					$strValores = http_build_query($data);
					$strValores = str_replace('%252F', '/', $strValores);
					$strValores = str_replace('%253A', ':', $strValores);
					$strValores = str_replace('%2B', ' ', $strValores);

					$correoMi .= "<br> Valores limpios: <br><br>
						CURLOPT_RETURNTRANSFER	=> true,<br>
						CURLOPT_SSL_VERIFYPEER	=> false,<br>
						CURLOPT_POST			=> true,<br>
						CURLOPT_VERBOSE			=> true,<br>
						CURLOPT_URL				=> " . $valores[13] . ",
						CURLOPT_POSTFIELDS		=> " . $strValores . "<br>";

					$ch = curl_init();
					curl_setopt_array($ch, $options);
					$output = curl_exec($ch);
					// 						echo "error=".curl_errno($ch);
					if (curl_errno($ch)) $correoMi .= "Error en la comunicaci�n al comercio:" . curl_strerror(curl_errno($ch)) . "<br>\n";
					$crlerror = curl_error($ch);
					// 						echo "otroerror=".$crlerror;
					if ($crlerror) {
						$correoMi .= "La comunicaci�n al comercio ha dado error:" . $crlerror . "<br>\n";
					}
					$curl_info = curl_getinfo($ch);
					curl_close($ch);

				}

				foreach ($curl_info as $key => $value) {
					$correoMi .= $key . " = " . $value . "<br>\n";
				}
				$correoMi .= "respuCurl=$output||<br>\n";
			}
		}

		if(!isset($valores[20])) {   // NO es una operacion BiPay
			//	env�o de correos y voucher este �ltimo en caso de pagos online Aceptados
			// 	$q = "select nombre, email from tbl_admin where correoT = 1 and idcomercio = '$valores[1]' and activo = 'S'";
			$q = "select email from tbl_admin a, tbl_colAdminComer c, tbl_comercio e where c.idAdmin = a.idadmin 
					and e.id = c.idComerc and e.idcomercio = '$valores[1]' and a.activo = 'S' and a.correoT = 1";
			$temp->query($q);
			$correoMi .= "<br>\n" . $q . "<br>\n";
			//	$arrayTo[] = $temp->loadRowList();
			$cc = false;
			while ($temp->next_record()) {
				if (!strlen($correo->to)) $correo->to($temp->f('email'));
				else {
					if (!$cc) $cc = $temp->f('email');
					else $cc .= "," . $temp->f('email');
				}
			}
			if ($cc) $correo->add_headers("Cc: " . $cc);

			$query = "select moneda from tbl_moneda where idmoneda = {$valores[7]}";
			$temp->query($query);
			$mon = $temp->f('moneda');

			if ($salta == false) {
				$subject = "Transacci�n realizada y $texto de " . $comercioN . " monto " . number_format(($valores[6] / 100), 2, '.', ' ') . " $mon";
				$message = "Estimado Cliente,<br><br> Se ha realizado una operaci�n con los siguientes datos:<br>
					Cliente: " . $valores[17] . " <br>
					Referencia del Comercio: " . $valores[2] . "<br>
					N�mero de transaccion: " . $valores[0] . " <br>
					C�digo entregado por el banco: " . $valores[3] . "<br>
					Estado de la transacci�n: $texto <br>
					Fecha: " . date('d/m/y h:i:s', $valores[5]) . "<br>
					Valor: " . number_format(($valores[6] / 100), 2, '.', ' ') . $mon;

				$correoMi .= "<br>\nCorreo Estado transaccion";
				$correo->todo(14, $subject, $message);
				$correo->destroy();

				//env�o de voucher
				if ($valores[8] == 'A') {
					$q = "select r.nombre, r.email, a.dominio from tbl_reserva r, tbl_transacciones t, tbl_pasarela p, tbl_agencias a 
							where r.id_transaccion = '$idtrans' 
								and r.id_transaccion = t.idtransaccion 
								and t.pasarela = p.idPasarela
								and p.idagencia = a.id";
					//echo $q."<br>";
					$temp->query($q);
					//echo "cant=".$temp->num_rows()."<br>";
					if ($temp->num_rows() > 0) {
						$correoMi .= "<br>FROM=tpv@" . $temp->f('dominio');
						$correo->from = "tpv@" . $temp->f('dominio');
						if (_MOS_CONFIG_DEBUG) echo _ESTA_URL . "/voucher.php?tr=" . $valores[2] . "&co=" . $valores[1] . "<br>";
						$correoMi .= "<br>\ncurl_init(" . _ESTA_URL . "/voucher.php?tr=" . $valores[2] . "&co=" . $valores[1] . ")";
						$ch = curl_init(_ESTA_URL . "/voucher.php?tr=" . $valores[2] . "&co=" . $valores[1]);
						curl_setopt($ch, CURLOPT_HEADER, false);
						curl_setopt($ch, CURLOPT_POST, false);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						$contents = curl_exec($ch);
						curl_close($ch);

						$correoMi .= "<br>\nCorreo Voucher<br>\n" . $contents;
						if (_MOS_CONFIG_DEBUG) echo "voucher=$contents<br>";

						if (strpos($temp->f('email'), ' - ') !== false) {
							$arrCor = explode(" - ", $temp->f('email'));
							$corr = $arrCor[0];
						} else $corr = $temp->f('email');
						$correoMi .= "<br>\ncorr=$corr";
						$arrayTo[] = array($temp->f('nombre'), $corr);
						// 			$arrayTo[] = array($temp->f('nombre'), $temp->f('email'));
						foreach ($arrayTo as $todale) {
							if (!strlen($correo->to))
								$correo->to = $todale[1];
							else
								$correo->set_headers("Cc: " . $todale[1]);
						}
						$subject = "Voucher";

						$q = "select email from tbl_admin a, tbl_colAdminComer o, tbl_comercio c where o.idAdmin = a.idadmin " .
							" and o.idComerc = c.id and a.idrol in (11,12,14) and a.activo = 'S' and c.idcomercio = " . $valores[1] .
							" order by a.fecha_visita desc limit 0,1";
						$temp->query($q);
						//				if (strlen($temp->f('email'))) $correo->reply = $temp->f('email');
						$correoMi .= "<br>El correo se env�a con from desde: " . $correo->from;
						$correo->todo(15, $subject, $contents);
						$correo->destroy();
					}
				}
			}
		}
	}
//	}
}

//env�o de sms
$sql = "select sms, telf, t.ip, t.pasarela, p.LimMinOper, p.LimMaxOper, p.LimDiar, p.LimMens, p.LimAnual, p.LimOperIpDia, p.LimOperTarDia, p.LimOperDia, 
				t.valor_inicial, p.nombre
			from tbl_comercio c, tbl_transacciones t, tbl_pasarela p 
			where t.pasarela = p.idPasarela and t.idcomercio = c.idcomercio and t.idtransaccion = '$idtrans'";
$correoMi .= "<br>\n".$sql."<br>\n";
$temp->query($sql);
$sms 		= $temp->f("sms");
$telf 		= $temp->f("telf"); //borrar el n�mero m�o!!!!!!!!!!
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

//env�o de avisos de errores por l�mites
if (strstr($iderror, 'SIS0261')) {
	$etiqueta = "Avisos de L�mites alcanzados en TPV";
	$mes = "La operaci�n $idtrans ha pasado a trav�s de la pasarela $pasnom con un monto de $valini<br>\n";
	
	//para el l�mite m�nimo por operaci�n
	$mes .= "El l�mite m�nimo para este TPV es de $minlim<br>\n";
	
	//para el l�mite m�ximo por operaci�n
	$mes .= "El l�mite m�ximo para este TPV es de $maxlim<br>\n";
	
	//para el l�mite m�nimo por operaci�n
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
	$mes .= "El l�mite diario para este TPV es de $limdiar y el acumulado hasta ahora es de ".number_format($temp->f('valor'),2)."<br>\n";
	
	//para el n�mero de transacciones desde una misma IP en el d�a
	$q = "select count(t.idtransaccion) 'total'
			FROM tbl_transacciones t
			where t.estado in ('A','V','B','R')
				and t.tipoEntorno = 'P'
				and t.fecha > unix_timestamp('".date('Y')."-".date('m')."-".date('d')." 00:00:00')
				and t.ip = '$ip'
				and t.pasarela = ".$pasa;
	$temp->query($q);
	$mes .= "El n�mero m�ximo de operaciones desde una misma IP al d�a para este TPV es de $operip y en el d�a de hoy han pasado ".
				number_format($temp->f('total'),2). "<br>\n";
	
	//para el l�mite mensual
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
	$mes .= "El l�mite mensual para este TPV es de $limen y el acumulado hasta ahora es de ".number_format($temp->f('valor'),2)."<br>\n";
	
	//para el l�mite anual
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
	$mes .= "El l�mite anual para este TPV es de $liman y el acumulado hasta ahora es de ".number_format($temp->f('valor'),2)."<br>\n";
	
	//n�mero de operaciones al d�a
	$q = "select count(t.idtransaccion) 'total'
		FROM tbl_transacciones t 
		where t.estado in ('A','V','B','R','D') 
			and t.tipoEntorno = 'P' 
			and t.fecha > unix_timestamp('".date('Y')."-".date('m')."-".date('d')." 00:00:00')
			and t.pasarela = ".$pasa;
	$temp->query($q);
	$mes .= "El n�mero m�ximo de operaciones al d�a para este TPV es de $operdia y en el d�a de hoy han pasado ".
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
	$asunto = "Transacci�n No: $idtrans $texto valor:$importe100 $monedaNom";
}

if (_MOS_CONFIG_DEBUG) echo $correoMi;
$correo->todo(13,$titulo,str_replace('Flight', 'Flht', str_replace('flight', 'flht', str_replace('vuelo', 'vulo', str_replace('Vuelo', 'Vulo', $correoMi)))));
$q = "insert into tbl_traza (titulo,traza,fecha) values ('$titulo','".preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($correoMi, ENT_QUOTES))."',".time().")";
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

