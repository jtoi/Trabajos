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

$miObj = new RedsysAPI;
$temp = new ps_DB;
//$dms=new dms_send;
$correo = new correo();

#Datos de acceso a la plataforma
//$dms->autentificacion->idcli='126560';
//$dms->autentificacion->username='amfglobalitems';
//$dms->autentificacion->passwd='Mario107';

/*********************************************************************************************************************/

// $_REQUEST['Ds_Amount'] = '1957';
// $_REQUEST['Ds_Date'] = '160609214847';
// $_REQUEST['Ds_AuthorisationCode'] = '';
// $_REQUEST['Ds_Bank'] = '';
// $_REQUEST['Ds_Message'] = 'Aceptada';
// $_REQUEST['Ds_Code'] = '100';
// $_REQUEST['Ds_Merchant_MatchingData'] = '160609214644000000000';
// $_REQUEST['Ds_Merchant_TransactionType'] = '46';
// $_REQUEST['Ds_PanMask'] = '8968';
// $_REQUEST['Ds_Merchant_Guarantees'] = '100';
// $_REQUEST['Ds_Signature'] = '606e2e68c1b97db6e18fa49d2bf94acdd5873e31';
// $_REQUEST['Ds_Merchant_MerchantCode'] = '003277589';
// $_REQUEST['Ds_Merchant_NumTransaction'] = '177';

$_REQUEST['Ds_Date'] = '160613161531';
$_REQUEST['Ds_Merchant_MatchingData'] = '160613161136000000000';
$_REQUEST['Ds_PanMask'] = '8968';
$_REQUEST['Ds_Merchant_TransactionType'] = '46';
$_REQUEST['Ds_Merchant_MerchantCode'] = '003277589';
$_REQUEST['Ds_Merchant_Amount'] = '1656';
$_REQUEST['Ds_Code'] = '700';
$_REQUEST['Ds_Merchant_ClientId'] = '327304';
$_REQUEST['Ds_Merchant_BeneficiaryId'] = '966371';
$_REQUEST['Titanes_OrderId'] = '4404785';
$_REQUEST['Titanes_OrderStatusCode'] = '3';
$_REQUEST['Titanes_OrderStatus'] = 'Available';
$_REQUEST['Titanes_Description'] = 'Money has been received.';
$_REQUEST['Ds_Signature'] = '27c68a28f351b988d782972c6dc1cd26e84779693c75f81f60c36b95595b75c88f9385fca59e562171161d0a17aec6c8dabda803cb006072a7bbdc96497619f5';
$_REQUEST['Titanes_OrderCode'] = '3';

/*********************************************************************************************************************/

$correoMi = "fecha=".date('d/m/Y H:i:s')."<br>\n";
$pasarela = null;
$dirIp = $_SERVER['REMOTE_ADDR'];
$correoMi .= $dirIp."<br>\n";
$titulo = "Llegada a llega";
$ok = 0;
$iderror = $errorAMF = null;

if (count($_REQUEST) > 1) $d = $_REQUEST;
else $d = json_decode(file_get_contents('php://input'), true);

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
    $pasarela = 3;
} elseif ($d['Ds_SignatureVersion']) {
    $salida = $d['Ds_AuthorisationCode'].' / '.$d['Ds_Signature'];
    $pasarela = 60;
} elseif (isset($d['Ds_AuthorisationCode'])) {
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
        $correoMi .= "<br>Pasarela inv�lida";
        $correo->todo(12,$titulo,$correoMi);
        $q = "insert into tbl_traza (titulo,traza,fecha) values ('$titulo','".html_entity_decode($correoMi)."',".time().")";
        $temp->query($q);
        exit;
    } else $pasarela = 1;
}

/*****************************B�rrame*****************************************************/
/*   $salida = "<tpv><respago><idterminal>999999</idterminal><idcomercio>B9550206800006</idcomercio><nombrecomercio>TRAVELS AND DISCOVERY</nombrecomercio>
				<idtransaccion>450108050011</idtransaccion><moneda>840</moneda><importe>83.64</importe><fechahora>17-03-2013 04:05:33</fechahora>
				<estado>2</estado><coderror>0000</coderror><codautorizacion>063132</codautorizacion><firma>E69F9B0DB8AE40467055B34FB7DD5B8E23975613</firma>
  				</respago></tpv>"; */
/*****************************B�rrame*****************************************************/

switch ($pasarela) {
    case 1:
		$va = "BBVA � BBVA3D � BBVA 3D onL";
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
$pedazo='';
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
    if ($d['Ds_Code'] == 100 || $d['Ds_Code'] == 700) {// operaci�n Aceptada
    	$correoMi .= "Operaci�n Aceptada<br>\n";
    	$estado = '2';
    	
    	if ($d['Ds_Amount']) $importe = $d['Ds_Amount'];
    	elseif ($d['Ds_Merchant_Amount']) $importe = $d['Ds_Merchant_Amount'];
    	
    	$codautorizacion = $d['Ds_AuthorisationCode'];
    	$pedazo = "tarjetas = '**** **** **** ".$d['Ds_PanMask']."', ";
    	
    	if ($d['Titanes_OrderCode'] != 3) { //denegada por Titanes aunque de Tefpay viene Aceptada
    	$correoMi .= "Operaci�n Denegada por Titanes<br>\n";
    		$estado = '3';
    		$importe = 0;
    		$codautorizacion = null;
    		$ok = 1;
    		$iderror = $d['Titanes_Description']." ".$d['Titanes_Messages'];
    		$coderror = $d['Ds_Code'];
    	}
    } else { //operaci�n denegada
        $estado = '3';
        $importe = 0;
        $codautorizacion = null;
        $ok = 1;
        $iderror = $d['Titanes_Description']." ".$d['Titanes_Messages'];
        $coderror = $d['Ds_Code'];
    }
    
    if ($estado == '2'){
		include_once '../admin/classes/tcpdf/config/tcpdf_config.php';
		include_once '../admin/classes/tcpdf/tcpdf.php';
		creatitVou($idtrans); //est� Aceptada, genero voucher y lo env�o a Titanes
    }
    
    if ($d['Titanes_OrderId']) {
    	$q = "update tbl_aisOrden set titOrdenId = '{$d['Titanes_OrderId']}' where idtransaccion = '$idtrans'";
    	$temp->query($q);
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
	$imp�rte = $importe*1;
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
	$imp�rte = $importe*1;
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
	$imp�rte = $importe*1;
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
	$imp�rte = $importe*1;
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
	
	$q = "select clave from tbl_colPasarMon 
			where terminal = '{$decodec->Ds_Terminal}' 
				and length(clave) = 32 
				and comercio = '{$decodec->Ds_MerchantCode}'";
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
		$correoMis =  "<br>\nNo concuerda la firma=".$firma."<br>\ncon la comprobaci�n=$comprueba realizada<br>\npara la operaci�n $idtrans<br>\n";
		$correoMi .= $correoMis;
		$correo->todo(13,"Fallo en firma de la operaci�n",$correoMis);
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
		elseif ($pasarela == 12) $psrl = 12;
		elseif ($pasarela == 4) $psrl = 13;
		elseif ($pasarela == 40) $psrl = 40;
		elseif ($pasarela == 60) $psrl = 100;
		elseif ($pasarela == 3 ) {
			$psrl = 5;
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

    if (($est == "A" || $est == "B" || $est == "V") && $pasarela != 3 ) {
        $correoMi .= "La operaci�n estaba con estado $est a las $fe y se volvi� a recibir informaci�n del Banco como $estadoC, no se reliza ninguna acci�n en el Concentrador ni se env�an datos a los ".
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
    
    //Aviso de transacci�n duplicada desde el Banco
    if ($est == "N" || $est == "D" || $est == "A" || $est == "B" || $est == "V") {
        $lab = 'Recibido datos duplicados desde el banco';
        $mes = "fecha=".date('d/m/Y H:i:s')."<br>\n"."Se ha recibido duplicado los datos de la transacci�n $idtrans. La misma estaba en la base de datos con el estado $est el $fec ".
                "y se recibi� con estado $estadoC";
        $correo->todo (20, $lab, $mes);
        
        $correoMi .= $mes."<br>\n";
		
		if ($est == 'A' && $estadoC == 'A') {
			$lab = 'Recibida transacci�n Aceptada duplicada desde el banco';
			$mes = "fecha=".date('d/m/Y H:i:s')."<br>\n"."Se han recibido los siguentes datos de la transacci�n $idtrans:<br>\n $lleg ";
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

	//Env�o al sitio del cliente de la info de la transacci�n
	//Lee los datos de la transacci�n
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
	
	//el pago es atrav�s de web y el sitio solicita env�o directo de datos
	if ( ($temp->num_rows() == 0) && (strlen($valores[13]) > 1) && $salta == false ) { 

		$firma = convierte($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));

		if (strlen($firma) > 2) {
			$correoMi .=  "<br>firma={$valores[1]}, {$valores[2]}, {$valores[6]}, {$valores[7]}, {$valores[8]},	{$valores[0]}, ".
								date('d/m/y h:i:s', $valores[5])."<br>\n";
			if (
					$valores[1] != '140778652871' //Prueba Cubana TODO Cubana
					&& $valores[1] != '129025985109' //Cubana TODO Cubana
					&& $valores[1] != '140784511377' // Saratoga
					&& $valores[1] != '146161323238' // Claim
				) {//para todos los comercios excepto el de Cubana nuevo y el Saratoga
				
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
				if (curl_errno($ch)) $correoMi .=  "Error en la comunicaci�n al comercio:".curl_error($ch)."<br>\n";
				$crlerror = curl_error($ch);
	// 						echo "otroerror=".$crlerror;
				if ($crlerror) {
					$correoMi .=  "La comunicaci�n al comercio ha dado error:".$crlerror."<br>\n";
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
				if (curl_errno($ch)) $correoMi .=  "Error en la comunicaci�n al comercio:".curl_strerror(curl_errno($ch))."<br>\n";
				$crlerror = curl_error($ch);
	// 						echo "otroerror=".$crlerror;
				if ($crlerror) {
					$correoMi .=  "La comunicaci�n al comercio ha dado error:".$crlerror."<br>\n";
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
// // 			$valores[1] == '129025985109'	//Descomentar esta l�nea para habilitar Cubana TODO Cubana
// //			|| $valores[1] == '122327460662' //Comentar esta l�nea para deshabilitar Prueba
// // 			)
// 				) {
// 		$correoMi .=  "<br>\nEntra en la parte del chuchuch� <br>\n";
// 		$vale = number_format(($valores[6]/100), 2);
// 		$correoMi .= $valores[2].'.'.$valores[0].'.'.$vale.'.'.$valores[7].'.'.$valores[8].'.'.$valores[1]."<br>\n";
		
// 		$fr=md5($valores[2].$valores[0].$vale.$valores[7].$valores[8].$valores[1]);
        
//         //Actualiza la tabla amadeus con los datos de la transacci�n
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

//         $correoMi .=  "<br>\nEnv�o del formulario por curl a Amadeus<br>\n";
//         //Env�o del formulario por Cdonts a Amadeus
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

// //        $urlEnv = _ESTA_URL."/cubanaLand.php"; //Comentar esta l�nea para dejar el desarrollo normal
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
            
//             $correoMi .= "Enviado a Amadeus por detr�s, env�o $i - ".$salidaCurl."<br>\n";
//             $i++;
//         }
//         curl_close($ch);
//         $correoMi .= "<br>\nTerminado el env�o a Amadeus <br>\n";
// 	}
	
//	env�o de correos y voucher este �ltimo en caso de pagos online Aceptados
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
        $subject = "Transacci�n realizada y $texto de ".$comercioN." monto ".number_format(($valores[6]/100),2,'.',' ') ." $mon";
        $message = "Estimado Cliente,<br><br> Se ha realizado una operaci�n con los siguientes datos:<br>
            Comercio: ".$valores[1]." <br>
            N�mero de transaccion: ".$valores[0]." <br>
            C�digo entregado por el banco: ".$valores[3]."<br>
            Estado de la transacci�n: $texto <br>
            Fecha: ".date('d/m/y h:i:s', $valores[5])."<br>
            Valor: ".number_format(($valores[6]/100),2,'.',' ') .$mon;

    	$correoMi .= "<br>\nCorreo Estado transaccion";
        $correo->todo(14, $subject, $message);
        $correo->destroy();

        //env�o de voucher
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

