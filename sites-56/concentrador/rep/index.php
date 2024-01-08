<?php

define( '_VALID_ENTRADA', 1 );
require_once( '../configuration.php' );
require_once( '../include/mysqli.php' );
require_once( '../include/correo.php' );
require_once( '../include/hoteles.func.php' );
require_once( '../admin/adminis.func.php' );
require_once( '../include/apiRedsys.php' );

//xdebug_break();

$miObj = new RedsysAPI;
$temp = new ps_DB;
$correo = new correo();

$d = $_REQUEST;

/**************************************************************************************/
if (stripos(_ESTA_URL, 'localhost') > 0) {
// $d['Ds_SignatureVersion'] = "HMAC_SHA256_V1";
// $d['Ds_MerchantParameters'] = "eyJEc19EYXRlIjoiMjMlMkYxMCUyRjIwMTUiLCJEc19Ib3VyIjoiMTQlM0EyMSIsIkRzX1NlY3VyZVBheW1lbnQiOiIxIiwiRHNfQW1vdW50IjoiMTAwIiwiRHNfQ3VycmVuY3kiOiI5NzgiLCJEc19PcmRlciI6IjE1MTAyMzE0MTc1NSIsIkRzX01lcmNoYW50Q29kZSI6IjAzMDYzMTcyNSIsIkRzX1Rlcm1pbmFsIjoiMDA0IiwiRHNfUmVzcG9uc2UiOiIwMDAwIiwiRHNfVHJhbnNhY3Rpb25UeXBlIjoiMCIsIkRzX01lcmNoYW50RGF0YSI6IiIsIkRzX0F1dGhvcmlzYXRpb25Db2RlIjoiNDg4NjgxIiwiRHNfQ29uc3VtZXJMYW5ndWFnZSI6IjEiLCJEc19DYXJkX0NvdW50cnkiOiI3MjQifQ==";
// $d['Ds_Signature'] = "OECk1a2fvbSS7g0DA4cIAwCon1ktRHvIq7ted5Y6UpQ=";

//$d['resp'] = '180101003323';
//$d['est'] = 'ko';
//$d['reason'] = '208';
//$d['result'] = 'KO';
//$d['transactionId'] = '180101003323';
// $d['r'] = '170411224279';
// $d['h'] = 'deb3ea24b5b91020561568d435eb8012';
// $d['ret'] = '0';
// $d['i'] = '600';
// $d['Ds_Date'] = '23/10/2015';
// $d['Ds_Hour'] = '15:33';
// $d['Ds_SecurePayment'] = '1';
// $d['Ds_Amount'] = '100';
// $d['Ds_Currency'] = '978';
// $d['Ds_Order'] = '151023153225';
// $d['Ds_MerchantCode'] = '285772844';
// $d['Ds_Terminal'] = '005';
// $d['Ds_Signature'] = '3C8FB7453B8F69E8AD6E226EC05D12E9E559078D';
// $d['Ds_Response'] = '0000';
// $d['Ds_TransactionType'] = '0';
// $d['Ds_MerchantData'] = '';
// $d['Ds_AuthorisationCode'] = '335771';
// $d['Ds_ConsumerLanguage'] = '2';
// $d['Ds_Card_Country'] = '724';

/**************************************************************************************/

// $d['resp'] = '180129201560';
// $d['est'] = 'ko';
// $d['Ds_Message'] = 'Error en autenticacion';
// $d['Ds_Code'] = '202';
// $d['Ds_Merchant_MatchingData'] = '180129201560000000000';
// $d['Ds_Merchant_MerchantCode'] = '030672877';
// $d['Ds_Bank'] = '0081';
// $d['Ds_Date'] = '180129201719';
// $d['Ds_AuthorisationCode'] = '0';
// $d['Ds_PanMask'] = '1001';
// $d['Ds_Merchant_TransactionType'] = '1';
// $d['Ds_Amount'] = '816';
// $d['Ds_Merchant_Guarantees'] = '0';
// $d['Ds_Expiry'] = '2011';
// $d['Ds_Signature'] = 'e202473f698bf9febc464df7f44d338f6381ad43';

// $d['Titanes_OrderId'] = '4863803';
// $d['Titanes_OrderCode'] = '3';
// $d['Titanes_OrderStatus'] = 'Available';
// $d['Titanes_Description'] = 'Money has been received.';

// $d['amount'] = '8';
// $d['currency'] = '840';
// $d['paymentType'] = 'CCARD';
// $d['financialInstitution'] = 'MC';
// $d['language'] = 'en';
// $d['orderNumber'] = '1398119';
// $d['paymentState'] = 'SUCCESS';
// $d['shopname_customParameter1'] = 'shopname_customParameter1';
// $d['shopname_customParameter2'] = 'shopname_customParameter2';
// $d['instrumentCountry'] = '';
// $d['authenticated'] = 'No';
// $d['anonymousPan'] = '0001';
// $d['expiry'] = '12/2019';
// $d['cardholder'] = 'Jonh Doe';
// $d['maskedPan'] = '950000******0001';
// $d['gatewayReferenceNumber'] = 'DGW_1398119_RN';
// $d['gatewayContractNumber'] = 'DemoContractNumber123';
// $d['responseFingerprintOrder'] = 'amount,currency,paymentType,financialInstitution,language,orderNumber,paymentState,shopname_customParameter1,shopname_customParameter2,instrumentCountry,authenticated,anonymousPan,expiry,cardholder,maskedPan,gatewayReferenceNumber,gatewayContractNumber,secret,responseFingerprintOrder';
// $d['responseFingerprint'] = 'c986f700842f3808e9f6464b8edf7b391b20a8e3e8d84d771c9dbe84d3da92a7df1301a1b45550db0e4ad1e7f628afd419c3c9df4ec4e13b0e79622d81efc6bc';
}
/**************************************************************************************/

$correoMi = "fecha=".date('d/m/Y H:i:s')."<br>\n";
$correoMi .= "Entrada de valores:<br>\n";
foreach ($d as $key => $value) {
	$correoMi .= $key." = ".$value."<br>\n";
}

$q = "insert into tbl_traza (titulo,traza,fecha) values ('Entrada entrada datos','".preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($correoMi, ENT_QUOTES))."',".time().")";
$temp->query($q);

$cookie = $d['resp'];
$sabad = $d['est'];
$arrayTo = array();
$codigo = '';
if (isset($d['pszPurchorderNum'])) { //Santander
	$cookie = $d['pszPurchorderNum'];
	if ($d['result'] == 0) {
		$codigo = $d['pszApprovalCode'];
		$sabad = 'ok';
	}
} elseif (isset ($d['Ds_Order'])) $cookie = $d['Ds_Order']; //bankia, caixa

if ($d['Ds_SignatureVersion'] == "HMAC_SHA256_V1") {
	$decodec = json_decode($miObj->decodeMerchantParameters($d['Ds_MerchantParameters']));
	$cookie = $decodec->Ds_Order;
	$codigo = $decodec->Ds_AuthorisationCode;
}

if ($d['reason'] && $d['result']) { //Papam
	error_log('Papam');
	$cookie = $d['resp'];
	($d['result'] == 'OK') ? $sabad = 'ok' : $sabad = 'ko';
	error_log('cookie='.$cookie);
	error_log('sabad='.$sabad);
	if ($d['est'] == 'ko') {//si la operación  es denegada pongo en la tabla transacciones el error
        $q = "select texto from tbl_errores where idpasarela = 115 and codigo = '".$d['codeReponse']."'";
        $correoMi .= $q."<br>\n";
        $temp->query($q);
        if(strlen($temp->f('texto')) > 0){
            $textoError = $temp->f('texto');
        } else{
            $textoError =  $d['reason'];
        }
        $correoMi .= "Texto del Error: $textoError <br>\n";

//		$q = "update tbl_transacciones set id_error = (select texto from tbl_errores where idpasarela = 115 and codigo = ".$d['codeReponse'].") where idtransaccion = $cookie"; Reina
		$q = "update tbl_transacciones set id_error = '$textoError' where idtransaccion = '$cookie'";
        $correoMi .= $q."<br>\n";
        error_log($q);
		$temp->query($q);
	}
}

if (strlen($codigo) > 1) $correoMi .= "Código de Aceptada $codigo<br>\n";


/**************************************************************************************/
//$cookie='140708051097';
//$codigo='994724';
//$sabad = 'ok';
/**************************************************************************************/


//Revisa si la operación no ha sido trabajada en llegada.php
$q = "select t.idcomercio, identificador, idioma, t.valor_inicial, t.moneda idMon, m.moneda, t.estado, p.nombre pasarela, c.nombre comercio, c.url, t.tipoEntorno, "
			. "t.valor_inicial/100 vale, c.url_llegada, t.pasarela idpas, p.idcenauto "
		. "from tbl_transacciones t, tbl_comercio c, tbl_pasarela p, tbl_moneda m "
		. "where m.idmoneda = t.moneda and t.idcomercio = c.idcomercio and p.idPasarela = t.pasarela and t.idtransaccion = '%s' and t.estado in ('P','N')";
$q = sprintf($q, $cookie);
$correoMi .= "<br>\n$q<br>\n";
$temp->query($q);
$can = $temp->num_rows();
$moneda = $temp->f('moneda');
$vale = $temp->f('vale');
$identif = $temp->f('identificador');
$idcomercio = $temp->f('idcomercio');
$tasa = leeSetup($moneda);
$comercio = $temp->f('comercio');
$pasarr = $temp->f('pasarela');
$idpas = $temp->f('idpas');
$valor_inicial = $temp->f('valor_inicial');
$idcenauto = $temp->f('idcenauto');
$estadoOp = $temp->f('estado');
$correoMi .=  "<br>\nEstado Op=".$estadoOp."<br>\n";

if ($can > 0) {
	$q = "select * from tbl_pasarela where idPasarela = $idpas and idbanco = 19";
	$correoMi .= "$q<br>\n";
	$temp->query($q);
	$wcp = $temp->num_rows();
}

if ($can == 1 && $wcp == 0) {
	$correoMi .= "<br>\nOperación Pendiente en Concentrador Tratamos de modificarla<br>\n<br>\n";
	$q = "select nombre, email from tbl_reserva where codigo = '$identif' and id_comercio = '$idcomercio'";
	$correoMi .= $q."<br>\n";
	$temp->query($q);
	if ($temp->getErrorMsg()) {$correoMi .= "Error: ".$temp->getErrorMsg()."<br>\n<br>\n";}
	$clien = $temp->f('nombre');
	$corCli = $temp->f('email');
	$pases = false;
	
// 	if (strlen($codigo) > 4) {
	$correoMi .= "La operación estaba Pendiente trato de Cambiarle el estado<br>\n";
	
	if ($d['Ds_SignatureVersion'] == "HMAC_SHA256_V1") {// si la operación es del Redsys nuevo
		$pases = true;
		$data = array(
				'Ds_SignatureVersion' => "HMAC_SHA256_V1",
				'Ds_MerchantParameters' => $d['Ds_MerchantParameters'],
				"Ds_Signature" => $d["Ds_Signature"]
				);
		$correoMi .= "Entra en redsys nuevo<br>";
	} elseif (
			$idpas == 12		// Abanca 3D
			|| $idpas == 20		// CX
			|| $idpas == 21		// CX 3D
			|| $idpas == 23		// Bankia3 3D DCC
			|| $idpas == 29		// Sabadell Plus 3D DCC
			|| $idpas == 30 	// La Caixa
			|| $idpas == 31		// Sabadell2 3D
			|| $idpas == 32		// Bankia4 3D
			|| $idpas == 36		// CX2 3D
			|| $idpas == 38		// La Caixa2 3D
			|| $idpas == 41		// Bankia5 3D
			|| $idpas == 44		// Sabadell3
			|| $idpas == 45		// AndBank 3D
			|| $idpas == 46		// BancaSabadell 3D
			|| $idpas == 50		// Ibercaja 3D
			|| $idpas == 51 	// LabKutxa 3D
			|| $idpas == 52		// Bankia6
			|| $idpas == 53		// Abanca2
			|| $idpas == 56		// La Caixa3
			|| $idpas == 58		// Navarra1 3D DCC
			|| $idpas == 59		// Navarra2 3D
			|| $idpas == 60		// Navarra2 3D PR
			|| $idpas == 61		// CX SL
			|| $idpas == 63 	// CaixaGeral 3D
			|| $idpas == 68 	// Abanca3
			|| $idpas == 80 	// Ibercaja
		|| $idpas == 145 	// AbancaIT 3D
		|| $idpas == 164 	// Abanca CSC 3D
			) {
		$pases = true;
		if ($moneda*1>0) {
			$moneda = $moneda;
		} else {
			$q = "select idmoneda from tbl_moneda where moneda like '$moneda'";
			$temp->query($q);
			$moneda = $temp->f('idmoneda');
		}
		$correoMi .= "Entra en pasarela $idpas<br>";
		
		if ($sabad == 'ko') {
			$data = array(
				'Ds_TransactionType'=>0,
				'Ds_Card_Country'=>484,
				'Ds_Date'=>date('d/m/Y'),
				'Ds_SecurePayment'=>1,
				'Ds_Signature'=>'',
				'Ds_Order'=>$cookie,
				'Ds_Hour'=>date('H:i'),
				'Ds_Response'=>'0190',
				'Ds_AuthorisationCode'=>'',
				'Ds_Currency'=>$moneda,
				'Ds_ConsumerLanguage'=>1,
				'Ds_MerchantCode'=>'163047095',
				'Ds_Amount'=>'',
				'Ds_Terminal'=>'001',
				'Ds_MerchantParameters'=>'',
				'Ds_ErrorCode'=>'' 
			);

			$correoMi .= "Carga datos para ko<br>\n";
		} elseif($sabad == 'ok') {
			$data = array(
				'Ds_TransactionType'=>0,
				'Ds_Card_Country'=>156,
				'Ds_Date'=>date('d/m/Y'),
				'Ds_SecurePayment'=>1,
				'Ds_Signature'=>'',
				'Ds_Order'=>$cookie,
				'Ds_Hour'=>date('H:i'),
				'Ds_Response'=>'0000',
				'Ds_AuthorisationCode'=>'verif',
				'Ds_Currency'=>$moneda,
				'Ds_ConsumerLanguage'=>1,
				'Ds_MerchantCode'=>'163046568',
				'Ds_Amount'=>$valor_inicial,
				'Ds_Terminal'=>'001',
				'Ds_MerchantParameters'=>'',
				'Ds_ErrorCode'=>''
			);
			$correoMi .= "Carga datos para ok<br>\n";
		}
		$correoMi .= "entra el listado de pasarelas otro<br>";
	} elseif ($idcenauto == 13) {
	    $pases = true;
	    
	    if ($sabad == 'ok') {
	        $data = array(
	            'Ds_Date' => date('ymdHis'),
	            'Ds_Merchant_MatchingData' => $d['Ds_Merchant_MatchingData'],
	            'Ds_PanMask' => $d['Ds_PanMask'],
	            'Ds_Merchant_Amount' => $valor_inicial,
	            'Ds_Code' => '700',
				'Ds_AuthorisationCode' => $d['Ds_AuthorisationCode']
	        );
			if (strlen($d['Ds_AuthorisationCode']) > 2 && $d['Ds_AuthorisationCode'] < 12) {
				$q = "update tbl_transacciones set codigo = '".$d['Ds_AuthorisationCode']."', estado = 'A' where idtransaccion = ".$cookie;
				$correoMi .= "$q<br>\n";
				$temp->query($q);

				$temp->query("update tbl_reserva set bankId = '".$d['Ds_AuthorisationCode']."', estado = 'A' where id_transaccion = ".$cookie);
				$correoMi .= "Hace el update de c&oacute;digo de banco<br>\nupdate tbl_transacciones set codigo = ".$d['Ds_AuthorisationCode']." where idtransaccion = ".$cookie."\n<br>";
			}
	    } elseif ($sabad == 'ko') {
	        $data = array(
	            'Ds_Amount' => $valor_inicial,
	            'Ds_Date' => date('ymdHis'),
	            'Ds_AuthorisationCode' => '0',
	            'Ds_Bank' => $d['Ds_Bank'],
	            'Ds_Message' => 'Error en autenticacion',
	            'Ds_Code' => $d['Ds_Code'],
	            'Ds_CodeBank' => '202',
	            'Ds_Merchant_MatchingData' => $d['Ds_Merchant_MatchingData'],
	            'Ds_Merchant_TransactionType' => '1',
	            'Ds_PanMask' => $d['Ds_PanMask'],
	            'Ds_Signature' => '',
	            'Ds_Merchant_MerchantCode' => $d['Ds_Merchant_MerchantCode']
	        );
		}
		
		$correoMi .= "entra en el centro autorizador 13<br>";
	} elseif ($idpas == 37) {
	    $pases = true;
	    
	    $q = "select c.idtitanes cliente, b.idtitanes benef
				from tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b
				where o.idcliente = c.id and o.idbeneficiario = b.id and o.idtransaccion = '$cookie'";
	    $temp->query($q);
	    $idcliente = $temp->f('cliente');
	    $idbenef = $temp->f('benef');
	    
	    if ($sabad == 'ok') {
	        $data = array(
	            'Ds_Date' => date('ymdHis'),
	            'Ds_Merchant_MatchingData' => $d['Ds_Merchant_MatchingData'],
	            'Ds_PanMask' => $d['Ds_PanMask'],
	            'Ds_Merchant_TransactionType' => '46',
	            'Ds_Merchant_MerchantCode' => '003277589',
	            'Ds_Merchant_Amount' => $valor_inicial,
	            'Ds_Code' => '700',
	            'Ds_Merchant_ClientId' => $idcliente,
	            'Ds_Merchant_BeneficiaryId' => $idbenef,
	            'Titanes_OrderId' => $d['Titanes_OrderId'],
	            'Titanes_OrderStatusCode' => '3',
	            'Titanes_OrderStatus' => 'Available',
	            'Titanes_Description' => 'Money has been received.',
	            'Ds_Signature' => '',
	            'Titanes_OrderCode' => '3'
	        );
	    } elseif ($sabad == 'ko') {
	        $data = array(
	            'Ds_Amount' => $valor_inicial,
	            'Ds_Date' => date('ymdHis'),
	            'Ds_AuthorisationCode' => '0',
	            'Ds_Bank' => $d['Ds_Bank'],
	            'Ds_Message' => 'Error en autenticacion',
	            'Ds_Code' => '202',
	            'Ds_CodeBank' => '202',
	            'Ds_Merchant_MatchingData' => $d['Ds_Merchant_MatchingData'],
	            'Ds_Merchant_TransactionType' => '46',
	            'Ds_PanMask' => $d['Ds_PanMask'],
	            'Ds_Merchant_Guarantees' => '0',
	            'Titanes_OrderId' => $d['Titanes_OrderId'],
	            'Ds_Signature' => '',
	            'Ds_Merchant_MerchantCode' => '003277589',
	        );
			
			if ($d['Ds_Code']  == '703') $correo->todo(51, "Operación con Error 703 de Tefpay ".$cookie, "La operación $cookie de Fincimex ha entrado con error 703 revisar en TefPay y Titanes");
		}
		
		$correoMi .= "entra en pasarela 37<br>";
	} elseif ($idcenauto == 12) {
		$correoMi .= "La oper es de PayTpv la actualizo";
		($d['est'] == 'ok') ? $est = 'A' : $est = 'D';
		
		$q = "update tbl_transacciones set valor = valor_inicial, id_error = null, tasa = $tasa, euroEquiv = (valor/100)/(tasa), estado = '$est', fecha_mod = ".time()." where idtransaccion = '$cookie'";
		$correoMi .= "$q<br>\n";
		$temp->query($q);
		if ($temp->getErrorMsg()) $correoMi .= $temp->getErrorMsg()."<br>\n";
		

		$text = "La operación $cookie de $comercio con identificador $identif realizada ahora (".date('d/m/Y H:i:s').") no entró por llegada.php pero fué actualizada acá con estado $est, si está Aceptada buscar el código del banco";
		$correo->todo(29,$subject,$text);

		$correoMi .= "entra en el centro autorizador 12<br>";
	} else if ($idcenauto == 14) { //operación de Xilema
		$correoMi .= "La oper es de Xilema la actualizo";
		
		foreach ($d as $key => $value) {
			$correoMi .= $key." = ".$value."<br>\n";
		}
		if ($d['est'] == 'ok') {
			$est = 'A';
			$val = "valor = valor_inicial, ";
			$tas = "tasa = $tasa, euroEquiv = ((valor/100)/($tasa)), codigo = '".$d['authCode']."', ";
			$err = '';
		} else {
			$est = 'D';
			$val = "valor = 0, ";
			$tas = "valor = 0, ";
			$err = 'A petición del usuario el pago ha sido cancelado';
		}
		
		$q = "update tbl_transacciones set $val id_error = '$err', $tas estado = '$est', fecha_mod = ".time()." where idtransaccion = '$cookie'";
		$correoMi .= "$q<br>\n";
		$temp->query($q);
		if ($temp->getErrorMsg()) $correoMi .= $temp->getErrorMsg()."<br>\n";
		

		$text = $correoMi .= "La operación $cookie de $comercio con identificador $identif realizada ahora (".date('d/m/Y H:i:s').") no entró por llegada.php pero fué actualizada acá con estado $est, si está Aceptada buscar el código del banco";
		//$correo->todo(29,'Operación de Xilema por index',$text);
		$correoMi .= "entra en el centro autorizador 14<br>";
	} else{
		$correoMi .= "No se conoce el Centro Autorizador<br>";

		if ($sabad == 'ok') {
			$est = 'A';
			$val = "valor = valor_inicial, tasa = $tasa, euroEquiv = ((valor/100)/($tasa)) ";
		} elseif ($sabad == 'ko') {
			$est = 'D';
			$val = "valor = 0 ";
		}
		$q = "update tbl_transacciones set $val, estado = '$est', fecha_mod = ".time()." where idtransaccion = '$cookie'";
		$correoMi .= "$q<br>\n";
		$temp->query($q);
	}

	$correoMi .= "Pases = $pases<br>\n";
	if ($pases) {
		$correoMi .= "Se lanza el update en llegada<br>";

		$data["origen"] = "browser";

		$options = array(
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_SSL_VERIFYPEER	=> false,
				CURLOPT_POST			=> true,
				CURLOPT_VERBOSE			=> true,
				CURLOPT_URL				=> 'https://www.administracomercios.com/rep/llegada.php',
				CURLOPT_POSTFIELDS		=> $data
		);
		$ch = curl_init();
		curl_setopt_array($ch , $options);
		$output = curl_exec($ch);
		// 						echo "error=".curl_errno($ch);
		if (curl_errno($ch)) $correoMi .=  "Error en la comunicación a llegada.php:".(curl_errno($ch))."<br>\n";
		$crlerror = curl_error($ch);
		// 						echo "otroerror=".$crlerror;
		if ($crlerror) {
			$correoMi .=  "La comunicación a llegada.php ha dado error:".$crlerror."<br>\n";
		} else 
			$correoMi .= "Se ha actualizado la operación $cookie<br>\n";
		$curl_info = curl_getinfo($ch);
		curl_close($ch);
	}

//		$q = "update tbl_transacciones set codigo = '$codigo', valor = valor_inicial, id_error = null, tasa = $tasa, euroEquiv = (valor/100)/(tasa), "
//				. "estado = 'A', fecha_mod = ".time()." where idtransaccion = '$cookie'";
//		$correoMi .= "$q<br>\n";
//		$temp->query($q);
//		if ($temp->getErrorMsg()) $correoMi .= $temp->getErrorMsg()."<br>\n";
//		$q = "update tbl_reserva set id_transaccion = '$cookie', bankId = '$codigo', fechaPagada = ".time().", estado = 'A', est_comer = 'P', valor = $vale "
//				. "where codigo = '$identif' and id_comercio = $idcomercio";
//		$correoMi .= "$q<br>\n";
//		$temp->query($q);
//		if ($temp->getErrorMsg()) $correoMi .= $temp->getErrorMsg()."<br>\n";
//		$q = "update tbl_amadeus set idtransaccion = '$cookie', estado = 'A', fechamod = ".time().", codigo = '$codigo' "
//				. "where idcomercio = '$idcomercio' and rl = '$identif'";
//		$correoMi .= "$q<br>\n";
//		$temp->query($q);
//		if ($temp->getErrorMsg()) $correoMi .= $temp->getErrorMsg()."<br>\n";
//	} else {
//		$correo->todo(16, 'Operación que está Pendiente y viene Aceptada del Banco', "Operación $cookie Pendiente en la Base de datos y entra Aceptada del banco pero viene sin código.");
	$subject = "Verificar operación";
	if ($d['est'] == 'ko')  {
		$q = "select * from tbl_transacciones where estado in ('P', 'D', 'N') and idtransaccion = '$cookie'";
		$temp->query($q);
		if ($temp->num_rows() != 0) {
			$text = "La operación $cookie de $comercio con identificador $identif realizada ahora (".date('d/m/Y H:i:s').") no se ha actualizado y ha quedado Pendiente en el Concentrador pero vino con otro estado ({$d['est']}) del TPV $pasarr, revisar a ver";
			$correo->todo(16,$subject,$text);
		}
	} elseif ($d['est'] == 'ok') {
		$q = "select * from tbl_transacciones where estado in ('A') and idtransaccion = '$cookie'";
		$temp->query($q);
		if ($temp->num_rows() != 0) {
			$text = "La operación $cookie de $comercio con identificador $identif realizada ahora (".date('d/m/Y H:i:s').") no se ha actualizado y ha quedado Pendiente en el Concentrador pero vino con otro estado ({$d['est']}) del TPV $pasarr, revisar a ver";
			$correo->todo(16,$subject,$text);
		}
	}
// 	} else {
		
// 	}
// 	$text = "La operación $cookie de $comercio con identificador $identif realizada ahora (".date('d/m/Y H:i:s').") está Pendiente en el Concentrador pero viene Aceptada del TPV $pasarr. "
// 			. "Debe devolverse en Banco y en el Concentrador a cargo nuestro. También se le debe avisar a $comercio que al cliente {$temp->f('nombre')} con correo {$temp->f('email')}, "
// 			. "se le devolvió el importe y que no tiene validez el pago que realizó";
//	$text = "Se necesita devolver completa esta operación en {$arrEntr[4]} del comercio ".$arrEntr[7]." con identificador ".$arrEntr[8].
//			", la misma tiene número ".$arrEntr[6]." y código de autorización ".$arrEntr[0]." en el TPV ".$arrEntr[9].", fué realizada el día ".$arrEntr[5].
//			"\n\nDebe tener en cuenta que se realiza por haber quedado en proceso en el Concentrador, por lo que los cargos de la devolución no deben correr a cuanta del comercio\n";
}


//echo $correoMi;

$query = sprintf("select idtransaccion, t.idcomercio, identificador, codigo, idioma, fecha_mod, valor_inicial, moneda, t.estado,
		c.nombre, c.url, t.tipoEntorno, t.valor/100, t.tpv, t.pasarela, t.tasa, p.idcenauto, t.id_error, t.sesion, t.bipayId, t.pasarela,
		(select a.nombre from tbl_agencias a where p.idagencia = a.id) comercio
			from tbl_transacciones t, tbl_comercio c, tbl_pasarela p
			where t.idcomercio = c.idcomercio
				and p.idPasarela = t.pasarela
				and idtransaccion = '%s'",
			quote_smart($cookie));
$temp->query($query);
$valores = $temp->loadRow();
//echo "query=".$query;

$correoMi .= "$query<br>\n";
$correoMi .= "\n<br>Lectura de valores:<br>\n";
if(is_array($valores)) {
    foreach ($valores as $key => $value) {
        $correoMi .= $key." = ".$value."<br>\n";
    }
}

if (count($valores) > 0) {  // Se encontro la operacion registrada
    // Se busca si la operacion tiene registrada url de retorno
    $q = "select urlRetorno as url from tbl_ComerTransUrl where idcomercio = '$valores[1]' and idOperacion = '$valores[2]'";
    $temp->query($q);
    $correoMi .= "\n<br>Se busca si la la operacion tiene registrada url de retorno <br>\n";
    $correoMi .= "$q<br>\n";
    if ($temp->num_rows() > 0 && strlen($temp->f('url')) > 0) {
        $valores[10] = $temp->f('url');
    }
    $correoMi .= "urlRetorno = '$valores[10]' <br>\n";
}

//carga el código javascript para destruir iframes excepto para el nuevo sitio de cubana TODO Cubana
$dstrIframe = '';
if (
		$valores[1] != '140778652871' && // Comercio Prueba Cubana
		$valores[1] != '129025985109' //Comercio Cubana
		
	) $dstrIframe = 'if (parent.frames.length > 0) parent.location.href = self.document.location;';

$correoMi .= "dstrIframe = $dstrIframe<br>\n";
$correoMi .= "comercio = ".$valores[1]."<br>\n";

if (count($valores) > 0) {
 	$correoMi .=  "\n<br>cookies= ".$cookie."<br>\n";

    $query = "select * from tbl_reserva where id_comercio = '".$valores[1]."' and codigo = '".$valores[2]."'";
    $temp->query($query);
 //   echo $query;
    $pago = $temp->loadRow();
	$correoMi .=  "\n<br>$query<br>\n";
	$correoMi .= "Lectura de pago:<br>\n";
    if (is_array($pago)) {
        foreach ($pago as $key => $value) {
            $correoMi .= $key." = ".$value."<br>\n";
        }
	}
	
	if ($valores[14] == 12 || $valores[14] == 53 || $valores[14] == 50) {
		if ($sabad == 'ok') $pasEs = 'A'; else $pasEs = 'D';
		if ($valores[8] != $pasEs) {
			$correoMi .= "El estado de la operación no es el mismo que el que está en la tabla transacciones<br>\n";
			if ($valores[8] != 'A') {
				$valores[8] = $pasEs;
				if ($valores[3] == '' && $d['Ds_AuthorisationCode']) $valores[3] = $d['Ds_AuthorisationCode'];
				if ($idpas != 91) {
//					$q = "update tbl_transacciones set estado = '".$valores[8]."', codigo = '{$valores[3]}', estado = 'A' where idtransaccion = '".$valores[0]."'";  Reina
					$q = "update tbl_transacciones set estado = '".$valores[8]."', codigo = '{$valores[3]}' where idtransaccion = '".$valores[0]."'";
					$temp->query($q);
					$correoMi .= $q."<br>\n";
				}
			}
			$correo->todo(16, 'Operación con estado distintos revisar', $correoMi);
		}
	}
	
	if ($wcp > 0 && $sabad == 'ok') { //Aceptada en Wirecard
		$valores[3] = $d['orderNumber']; //código del banco
		$valores[12] = $d['amount']*100; //valor
		$valores[8] = 'A'; //estado de la operación
	} elseif ($wcp > 0 && $sabad == 'ko') { //Denegada en Wirecard
		$valores[3] = ''; //código del banco
		$valores[12] = 0; //valor
		$valores[8] = 'D'; //estado de la operación
	}
	
	if ($wcp > 0) { //Wirecard actualiza la operación en la tabla de transacciones

		// reviso los parámetros mandados y chequeo fingerprint
//		$q = "select clave from tbl_colPasarMon where idpasarela = $idpas and idmoneda = ".$d['currency'];
		$correoMi .= $q."<br>\n";
//		$temp->query($q);
//		$d['secret'] = $temp->f('clave');
		$arrClstr = explode(',', $d['responseFingerprintOrder']);
		$ret = '';
		foreach ($arrClstr as $item){
			$ret .= $d[$item];
			$correoMi .= "$item,";
		}
		$correoMi .= "<br>$ret<br>";
//		$Digest = hash_hmac("sha512", $ret, $d['secret']);
		$correoMi .= "<br>fingerprint generado:$Digest<br>\n";
		
//		if ($Digest != $d['responseFingerprint']) {
//			$echo = "Error en la firma";
//			$correoMi .= "<br>Error en la firma: entra - {$d['responseFingerprint']}<br>\ncálculo - $Digest<br>\n";
//			$correo->todo(16, $subject, $correoMi);
// 			exit;
//		}
		
		$valores[5] = time(); //fecha
		
		//revisa que la operación este Pediente en transacciones para actualizarla
		$q = "select * from tbl_transacciones where estado in ('P','N') and idtransaccion = ".$valores[0];
		$correoMi .= $q."<br>\n";
		$temp->query($q);
		
		if ($temp->num_rows() > 0) {
			$d['currency'] = $temp->f('moneda');
			$correoMi .= "La operación estaba Pediente la actualizo como";
			//busca la tasa de cambio
			$q = "select moneda from tbl_moneda where idmoneda = ".$d['currency'];
			$temp->query($q);
			$mon = $temp->f('moneda');
			if ($d['currency'] == '978') $cambioRate = 1;
			else $cambioRate = leeSetup ($mon);
			
			//hace el update de la operación con los nuevos datos
			$query = "update tbl_transacciones set ";
			switch ($valores[8]) {
				case 'A': //Aceptada
					$query .= " codigo = '{$valores[3]}', valor = {$valores[12]}, id_error = null, tasa = ".$cambioRate.", 
						euroEquiv = ($valores[12]/100)/($cambioRate), tarjetas = '".$d['maskedPan']."', ";
					$correoMi .=  "\n<br> Aceptada";
					break;
				case 'D': //Denegada
					$query .= " id_error = '".htmlspecialchars($d['message'], ENT_QUOTES)."', ";
					$correoMi .=  "\n<br> Denegada";
					break;
			}
			$query .= " estado = '{$valores[8]}', fecha_mod = ".time()." where idtransaccion = '{$valores[0]}'";
			$correoMi .=  "\n<br> $query <br><br>\n\n";
//	echo $correoMi;
//	exit;
			$temp->query($query);
			
			$valores[12] = $valores[12]/100; //wirecard ahora manda las operaciones mult. por 100
		}
	}

	$referencia = leeSetup('refOpPruebas');
	if($valores[2] != $referencia) {
		$query = "update tbl_reserva set id_transaccion = '" . $valores[0] . "', bankId = '" . $valores[3] . "', fechaPagada = " . $valores[5] . ",
						estado = '" . $valores[8] . "', est_comer = '" . $valores[11] . "', valor = " . $valores[12] . "
					where codigo = '" . $valores[2] . "' and id_comercio = " . $valores[1];
		$correoMi .= "\n<br> $query <br><br>\n\n";
		$temp->query($query);
	}
	
//	$correoMi .= "valores= {$valores[1]} && {$pago[18]} && {$valores[13]} <br>\n";
	$correoMi .= "valores= $valores[1] && $pago[18] && $valores[13] <br>\n";
	$correoMi .= "count(pago)= ".count($pago)."<br>\n";
	
	//TODO Cubana
	if (count($pago) == 0){ //no hay pago online o el comercio es Cubana
//         if ($valores[1] != '129025985109') { //Camino de todos los comercios excepto Cubana TODO Cubana
		if(isset($valores[19])){	// es una operacion BiPay
			$firma = convierte256($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));
		} else{
			if (strlen($valores[18]) == 32)
				$firma = convierte($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));
			else
				$firma = convierte256($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));
		}

		$correoMi .=  "firma={$valores[1]}, {$valores[2]}, {$valores[6]}, {$valores[7]}, {$valores[8]}, {$valores[0]}, ".date('d/m/y h:i:s', $valores[5])."<br>";
	
		$cont = "<input type=\"hidden\" name=\"comercio\" value=\"".$valores[1]."\">
				<input type=\"hidden\" name=\"transaccion\" value=\"".$valores[2]."\">
				<input type=\"hidden\" name=\"importe\" value=\"".$valores[6]."\">
				<input type=\"hidden\" name=\"moneda\" value=\"".$valores[7]."\">
				<input type=\"hidden\" name=\"resultado\" value=\"".$valores[8]."\">
				<input type=\"hidden\" name=\"codigo\" value=\"".$valores[0]."\">
				<input type=\"hidden\" name=\"idioma\" value=\"".$valores[4]."\">
				<input type=\"hidden\" name=\"fecha\" value=\"".date('d/m/y h:i:s', $valores[5])."\">
				<input type=\"hidden\" name=\"tasa\" value=\"".$valores[15]."\">
				<input type=\"hidden\" name=\"firma\" value=\"$firma\">";
		if(isset($valores[19])) {    // es una operacion BiPay
			$cont .= "<input type=\"hidden\" name=\"bipay\" value=\"$valores[19]\">";
			$cont .= "<input type=\"hidden\" name=\"pasarela\" value=\"$valores[20]\">";
			$cont .= "<input type=\"hidden\" name=\"comerc\" value=\"$valores[21]\">";
		}

		$correoMi .= "idcenauto=$valores[16] && sabad=$sabad<br>";
		
		if (($valores[16] == 13 || $valores[16] == 4 ) && $sabad == 'ko') {
			$correoMi .= "entra a crear la pagina de error<br>";
			$plant = 'modtefpay.html';
			if (is_file($plant)) {
				$correoMi .= "entra a leer el fichero<br>";
				$cadena = leeFicheros($plant);
				$cadena = str_replace('{error}', $valores[17], str_replace('{accion}', $valores[10], str_replace('{formulario}', $cont, $cadena)));
			}
		} else {

			/*$cadena = "<script>$dstrIframe
                    </script>
                    <form id=\"envia\" action=\"".$valores[10]."\" method=\"post\">$cont</form>";*/

            $cadena = "<script>$dstrIframe
                    </script>
                    <form id=\"envia\" action=\"".$valores[10];

			if(isset($valores[19])) {    // es una operacion BiPay
				$cadena .= "\" method=\"get\">$cont</form>";
			} else{
				$cadena .= "\" method=\"post\">$cont</form>";
			}

            $cadena .= '<script>document.writeln("<div style=\"margin:"+
                       window.innerHeight/2
                       +"px 0 0 "+
                       ((window.innerWidth)-400)/2
                       +"px; width:400px; text-align:center;\">"
                       )</script>
                       Espere unos segundos..... En breve lo redireccionamos al sitio.<br>Wait for a few seconds.... You will be redirected shortly to the website.';
            $correoMi .= $cadena."<br>";
            $cadena .= "<script language=\"JavaScript\">
                        document.forms[0].submit();
                    </script>";
		}
		
		echo $cadena;
//         } else {
        
//             $arrMerc = array('122327460662' => 'ADMPADMP','129025985109' => 'AAWOAAWO'); //array para site Cubana
//             echo "<script>$dstrIframe</script>";

//             //lee los datos tanto para transacciones aceptadas como denegadas
//             $q = "select lastName, urlko from tbl_amadeus where rl = '".$valores[2]."' and idcomercio = '".$valores[1]."'";
//             $correoMi .= "<br>\n".$q;
//             $temp->query($q);
//             $ape = 	str_replace("ñ", "n", 
//             		str_replace("ú", "u", 
//             		str_replace("í", "i", 
//             		str_replace("é", "e", 
//           			str_replace("á", "a", 
//             		str_replace("ó", "o", 
//             					$temp->f('lastName')))))));
//             $url = $temp->f('urlko');
//             if ($valores[8] == 'A') {
//                 $correoMi .= "<br>\nTransacción aceptada";
//                 if ($valores[1] == '129025985109') $url  = "http://wftc1.e-travel.com/plnext/cubanaairlines/RetrievePNR.action?"; //Para Cubana
// //                if ($valores[1] == '122327460662') $url = "https://siteacceptance.wftc1.e-travel.com/plnext/pspcuba/RetrievePNR.action?"; //Para Prueba
//                 $url .= "SITE=".$arrMerc[$valores[1]]."&LANGUAGE=ES&EXTERNAL_ID=CU&DIRECT_RETRIEVE=TRUE&REC_LOC=".$valores[2]."&DIRECT_RETRIEVE_LASTNAME=".$ape;
                
//             } else $correoMi .= "<br>\nTransacción denegada";
            
//             $correoMi .= "<br>\n".$url;
//             $cadena = "<script language=\"JavaScript\">window.open('$url','_self');</script>";
//             echo $cadena;
//         }
    } else { //hay pago online
		if(!isset($valores[19])) {    // NO es una operacion BiPay
			if (($valores[16] == 13 || $valores[16] == 4 ) && $sabad == 'ko') {
					$correoMi .= "entra a crear la pagina de error<br>";
				$plant = 'modtefpay.html';
				if (is_file($plant)) {
					$correoMi .= "entra a leer el fichero<br>";
					$cadena = leeFicheros($plant);
					$cadena = str_replace('{error}', $valores[17], str_replace('{accion}', $valores[10], str_replace('{formulario}', $cont, $cadena)));
				}
			}
		
			$correoMi .=  "valor=".$pago[18]."<br>\n" ;

			(strstr($pago[23], "admin.admin")) ?
				$sit = "https://".$pago[23]."/index.php?componente=comercio&pag=cliente":
				$sit = "https://".$pago[23]."/admin/index.php?componente=comercio&pag=cliente";

			if ($valores[8] == 'A') {
				if ($pago[18] == 'N') { //no es pago al momento
					echo "<script language=\"JavaScript\">$dstrIframe"
							. "window.open('"._ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]."', '_self');
						</script>";
				} else { //es pago al momento
					if ($valores[13] != '1') {
						$correoMi .= "Aceptada al Concentrador <br>\n$sit.<br>\n";
						echo "<script language=\"JavaScript\">$dstrIframe
							window.open('"._ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]."', '_new');
							window.open('"._ESTA_URL."/ticket.php?tr=".$valores[2]."&co=".$valores[1]."', '_new');
							window.open('".$sit."', '_self');
							</script>";
					} else {
						$correoMi .= "Aceptada al TPVV <br>\n";
						echo "<script language=\"JavaScript\">$dstrIframe
							window.open('"._ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]."', '_new');
							window.open('"._ESTA_URL."/ticket.php?tr=".$valores[2]."&co=".$valores[1]."', '_new');
							window.open('https://tpvv.administracomercios.com', '_self');
							</script>";
					}
				}
			} else {
				if ($pago[18] == 'N') {  //no es pago al momento
					$q = "select nombre, datos from tbl_comercio where idcomercio = ".$valores[1];
					$temp->query($q);
					$comNom = $temp->f('nombre');
					$comDat = $temp->f('datos');

					$q = "select a.nombre, a.email, t.id_error from tbl_reserva r, tbl_admin a, tbl_transacciones t where r.id_admin = a.idadmin and t.idtransaccion = r.id_transaccion and r.id_transaccion = '$cookie' ";
					$temp->query($q);
					$nomAd = $temp->f('nombre');
					$corAd = $temp->f('email');
					$fallo = $temp->f('id_error');
					?>
					<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
					<title><?php echo $titulo ?></title>
					<link href="../admin/template/css/admin.css" rel="stylesheet" type="text/css" />
					<!--<link href="../template/css/calendar.css" rel="stylesheet" type="text/css" />-->
					<script>
						<?php echo $dstrIframe; ?>
					</script>
					</head>
					<body>
						<div id="encabPago">
							<div id="logoPago"><img src="../admin/template/images/banner2.png" /> </div>
							<div class="inf"></div>
						</div>
						<div id="cuerpoPago">
							El pago realizado ha reportado un error. <br /><br /><?php echo $fallo; ?><br /><br />Contacte su proveedor en: /
							The Payment was reported as null. Contact your provider at:<br />
							<?php echo $nomAd; ?><br />
							<a href="mailto:<?php echo $corAd; ?>" > <?php echo $corAd; ?> </a>
						</div>
						<div>
							<div class="inf2"></div>
							<!-- Copyright &copy; Travels &amp; Discovery, <?php echo date('Y', time()); ?><br /><br /> -->
						</div>
					</body>
					</html>
				<?php
				} else { //es denegada con pago al momento
						if ($valores[13] != '1') {
							$correoMi .= "Denegada al Concentrador<br>";
							echo "<script language=\"JavaScript\">$dstrIframe
								window.open('".$sit."', '_self');
								</script>";
						} else {
							$correoMi .= 'Denegada al TPVV<br>';
							echo "<script language=\"JavaScript\">$dstrIframe
							window.open('https://tpvv.administracomercios.com', '_self');
							</script>";
						}
				}
			}
		}
    }
}
$subject = "Llegada del banco a rep-index";

//correoAMi($subject,$correoMi);
$correo->todo(16, $subject, $correoMi);

//$query = "insert into tbl_traza (titulo, traza, fecha) values ('$subject', '".htmlentities($correoMiMi, ENT_QUOTES)."', '".date('d/m/Y H:i:s')."')";
//$correoMi .= "\n$query";
//mail($to, $subject, $correoMi, $headers);
//$temp->query($query);


echo "<script>$dstrIframe</script>";
?>
