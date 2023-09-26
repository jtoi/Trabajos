<?php define( '_VALID_ENTRADA', 1 );
error_log(json_encode($_REQUEST));
error_log($_SERVER['REMOTE_ADDR']);

require_once( 'configuration.php' );
require_once 'include/mysqli.php';
$temp = new ps_DB;
require_once 'include/correo.php';
require_once( 'include/hoteles.func.php' );
require_once( 'admin/classes/entrada.php' );

$correo = new correo();
$ent = new entrada;


/**
 * Datos de entrada:
 * transaccion: identificador de la operacion en el comercio
 * comercio: identificador del comercio
 * importe: importe de la operación a cobrar, si es una liberación importe=0
 * operacion: tipo de operación a realizar, L-liberar, C-cobrar
 * firma: sha256 de (comercio.transaccion.importe.moneda.operacion.palabrasecreta)
 */

foreach ($_SERVER as $value => $item) {
	if (
				$value == 'HTTP_USER_AGENT' 
			|| 	$value == 'HTTP_ACCEPT_LANGUAGE' 
			|| 	$value == 'HTTP_REFERER'
			|| 	$value == 'REMOTE_ADDR'
			) {
		$correoMi = "Preautorizzo<br>entrada SERVER ".$value . "=" . $item . "<br>\n";
			}
}

/***************************************************************************************************************** */
// $_POST['comercio'] = '122327460662';
// $_POST['transaccion'] = '1207164892'; //TefPay
// $_POST['transaccion'] = '1112223878'; //Xilema
// $_POST['importe'] = '3';
// $_POST['operacion'] = 'L';
// $_POST['operacion'] = 'A';
// $_POST['moneda'] = '978';
// $_POST['firma']	= convierte256($_POST['comercio'], $_POST['transaccion'], $_POST['importe'], $_POST['moneda'], $_POST['operacion']);
/**************************************************************************************************************** */

// if (!($dirIp = GetIP())) exit();
$correoMi .= "DIR IP - ".$dirIp . "<br>";

$d = $_POST;
$correoMi .= json_encode($d). "<br>";

if (isset($d['comercio']) && isset($d['transaccion']) && isset($d['importe']) && isset($d['moneda']) && isset($d['operacion']) && isset($d['firma'])) {

	$arrEnt = array();
	if (!($arrEnt['comer'] = $ent->isAlfanumerico($d['comercio'], 15))) {
		muestraError ("falla por comercio", $correoMi);
	}
	if (!($arrEnt['tran'] = $ent->isUrl($d['transaccion'], 19))) {
		muestraError ("falla por transaccion", $correoMi);
	}
	if (!($arrEnt['imp'] = $ent->isNumero($d['importe'], 9)) && $arrEnt['imp'] != 0) {
		muestraError ("falla por importe", $correoMi);
	}
	$arrEnt['opr'] = strtoupper($d['operacion']);
	if (!$arrEnt['opr'] == 'L' || !$arrEnt['opr'] == 'A') {
		muestraError ("falla por operacion", $correoMi);
	}
	if (!($frma = $ent->isAlfanumerico($d['firma']))) {
		muestraError ("falla por firma", $correoMi);
	}
	if (!($arrEnt['mon'] = $ent->isReal($d['moneda'], 3))) {
		muestraError ("falla por moneda", $correoMi);
	} else {
		$q = sprintf("select count(idmoneda) total from tbl_moneda where idmoneda = '%s' and activo = 1",$arrEnt['mon']);
		$temp->query($q);
		if ($temp->f('total') != 1) {
			muestraError ("falla por moneda", $correoMi);
		}
	}
	if ($frma != convierte256($arrEnt['comer'],$arrEnt['tran'],$arrEnt['imp'],$arrEnt['mon'],$arrEnt['opr'])) {
		$correoMi .= "cadena= ".$arrEnt['comer']." .".$arrEnt['tran'] . " ." . $arrEnt['imp'] . " ." . $arrEnt['mon'] . " ." . $arrEnt['opr']."<br>";
		$correoMi .= "firma enviada= $frma<br>firma generada = ". convierte256($arrEnt['comer'], $arrEnt['tran'], $arrEnt['imp'], $arrEnt['mon'], $arrEnt['opr'])."<br>";
		muestraError ("falla por firma", $correoMi);
	}

	$q = sprintf("select t.idtransaccion, t.pasarela, p.idcenauto, m.factmult, t.moneda, c.comercio, c.clave, c.terminal, a.datos, case p.estado when 'P' then a.urlPro else a.urlDes end url, t.identificadorBnco, t.tarjetas, t.valor_inicial, t.tipoEntorno from tbl_transacciones t, tbl_pasarela p, tbl_colPasarMon c, tbl_cenAuto a, tbl_moneda m where m.idmoneda = t.moneda and p.idcenauto = a.id and p.idPasarela = c.idpasarela and c.idmoneda = t.moneda and c.estado = 1 and p.idPasarela = t.pasarela and t.idcomercio = '%s' and t.identificador = '%s' and t.tipoOperacion = 'A' and t.estado in ('E')", $arrEnt['comer'], $arrEnt['tran']);
	$correoMi .= $q."<br>";
	$temp->query($q);
	
	if ($temp->num_rows() != 1) muestraError("Datos no responden a una Preautorizacion", $correoMi);
	$arrVal = $temp->loadAssocList();
	$arrVal = $arrVal[0];
	$arrIdent = explode('&', $arrVal['identificadorBnco']);

	$correoMi .= json_encode($arrVal)."<br>";

	//preparación de los datos de salida
	$arrDisp["estado"] = 'E'; //la operación no ha cambiado
	$arrDisp["importe"] = $arrEnt['imp'];
	$arrDisp["error"] = '';
	$arrDisp["autorizo"] = '';
	$arrDisp["transaccion"] = $arrVal['idtransaccion'];
	$arrDisp["comercio"] = $arrEnt['comer'];
	$arrDisp["moneda"] = $arrEnt['mon'];
	$arrDisp["codigo"] = $arrEnt['tran'];
	$arrDisp["operacion"] = $_POST['operacion'];
	$arrDisp["fecha"] = urlencode(date('d/m/y h:i:s', time()));

	if ($arrVal['tipoEntorno'] == 'D') {
		$arrDisp["error"]			= '';
		if ($arrEnt['opr'] == 'A') {
			$arrDisp["importe"] 	= $arrEnt['imp'];
			$arrDisp["estado"] 		= 'A';
			$arrDisp['autorizo'] 	= '555555';
		} else {
			$arrDisp["importe"] 	= 0;
			$arrDisp["estado"] 		= 'L';
		}

		$sale = actualiza($arrDisp);
		echo $sale;
		$correoMi .= $sale;

	} else {

		if ($arrVal['idcenauto'] == '19') { //va a Xilema
			if ($arrEnt['opr'] == 'L') {
				$arrVal['url'] .= 'cancel';

				$json = '{"merchant":{"clientId":"'. $arrVal['comercio']. '","clientSecret":"'. $arrVal['clave']. '"},"customer":{"card":{ }},"trx":{"datetime":"'. $arrIdent[0]. '","reference":'.$arrVal['idtransaccion']. '","reason":"Error","originalReference":{"id":"' . $arrIdent[2] . '"}}}';

				$json = json_encode(
					array(
						"merchant" => array(
							"clientId"			=> $arrVal['comercio'],
							"clientSecret"		=> $arrVal['clave']
						),
						"customer" => array(
							// "isPresent"			=> false,
							"card" => array(
								// "type"			=> 'KEYED',
								// "token"			=> $arrIdent[1],
								// "isPresent"		=> false
							),
						),
						"trx" => array(
							"datetime"			=> $arrIdent[0],
							// "reference"			=> $arrVal['idtransaccion'] . "00000000",
							"reference"			=> $arrVal['idtransaccion'],
							"reason"			=> 'Error',
							"originalReference"	=> array (
								// "id"			=> $arrVal['idtransaccion']
								"id"			=> $arrIdent[2]
							)
						)
					)
				);

			} elseif ($arrEnt['opr'] == 'A') {
				$arrVal['url'] .= 'confirmation';

				$json = json_encode(
					array(
						"merchant" => array(
							"clientId"			=> $arrVal['comercio'],
							"clientSecret"		=> $arrVal['clave']
						),
						"customer" => array(
							"isPresent"			=> false,
							"card" => array(
								"type"			=> 'KEYED',
								"token"			=> $arrIdent[1],
								"isPresent"		=> false
							),
						),
						"trx" => array(
							"amount"			=> number_format(($arrDisp["importe"]/100), 2, '.', ''),
							"datetime"			=> $arrIdent[0],
							// "reference"			=> $arrVal['idtransaccion'] . "00000000",
							"reference"			=> $arrVal['idtransaccion'],
							"source"			=> 'eCommerce',
							"originalReference"	=> array(
								// "id"			=> $arrVal['idtransaccion']
								"id"			=> $arrIdent[2]
							)
						)
					) 
				);
			}
			$method = 'POST';
			$correoMi .= "url=". $arrVal['url']."<br>metodo=$method<br>";
			$correoMi .= "$json<br>";

			$header = array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($json)
			);

			$recibe = json_decode(envioDat($header, $json, $arrVal['url'], $method));
			echo "<br>";
			var_dump($recibe);
			echo "<br>";
			
			if ($recibe->status == 'Error') $arrDisp["error"] = $recibe->errors[0]->message;

			//El banco autorizo
			if (isset($recibe->trx->authCode) && strlen($recibe->trx->authCode) > 1) {
				if ($arrEnt['opr'] == 'L') {
					$arrDisp["estado"] 		= 'E';
					$arrDisp["importe"] 	= '0';
				} elseif ($arrEnt['opr'] == 'A') {
					$arrDisp["estado"] 		= $arrEnt['opr'];
					$arrDisp["autorizo"] 	= $recibe->trx->authCode;
					$arrDisp["importe"] 	= $arrDisp["importe"];
				}
			}
			
			echo json_encode($arrDisp). "<br><br><br>";
			actualiza($arrDisp);

		} elseif ($arrVal['idcenauto'] == '13') { //va a Tefpay

			if ($arrEnt['opr'] == 'L') $tipoTrans = '18'; elseif ($arrEnt['opr'] == 'A') $tipoTrans = '7'; 
			$arrVal['idtransaccion'] .= '000000000';

			$message = $arrDisp["importe"] . $arrVal['comercio'] . $arrVal['idtransaccion'] . $arrVal['clave'];
			// $message = $arrEnt['imp'] . $arrVal['comercio'] . $arrVal['idtransaccion'] . $urlcomercio . $arrVal['clave'];
			$correoMi .= "message = $message<br>";
			//$arrVal['url'] = "https://intesecure02.tefpay.com/paywebv1.4.26rc10/INPUT.php"; //Desarrollo
			$arrVal['url'] = "https://secure02.tefpay.com/paywebv1.4.26rc10/INPUT.php";	//Producción

			$arrDatos["Ds_Merchant_TransactionType"] 	= $tipoTrans;
			$arrDatos["Ds_Merchant_ResponseFormat"]		= 'xml';
			$arrDatos["Ds_Merchant_Lang"] 				= 'en';
			$arrDatos["Ds_Merchant_MatchingData"] 		= $arrVal['idtransaccion'];
			$arrDatos["Ds_Merchant_MerchantCode"] 		= $arrVal['comercio'];
			$arrDatos["Ds_Merchant_Amount"] 			= $arrDisp["importe"];
			$arrDatos["Ds_Merchant_PanMask"] 			= str_replace("*", "", $arrVal['tarjetas']);
			$arrDatos["Ds_Date"] 						= $arrVal['identificadorBnco'];
			$arrDatos["Ds_Merchant_MerchantSignature"] 	= sha1($message);

			// $arrDatos = 'Ds_Merchant_TransactionType:'.$tipoTrans.'&Ds_Merchant_MatchingData:'.$arrVal['idtransaccion'].'&Ds_Merchant_MerchantCode:'.$arrVal['comercio'].'&Ds_Merchant_Amount:'. $arrEnt['imp'] .'&Ds_Date:'.$arrVal['identificadorBnco'].'&Ds_Merchant_PanMask:'. str_replace("*", "", $arrVal['tarjetas']).'&Ds_Merchant_MerchantSignature'.sha1($message);
			$method = 'POST';

			$header = array(
				'Connection: keep-alive',
				'Content-Type: application/x-www-form-urlencoded'
			);

			// $recibe = envioDat($header,$arrDatos, $arrVal['url'], $method);


			/** $recibe = '<?xml version="1.0" encoding="UTF-8"?>\n <response><Ds_Message>Accepted</Ds_Message><Ds_Merchant_MatchingData>201207164884000000000</Ds_Merchant_MatchingData><Ds_Bank>0081</Ds_Bank><Ds_Date>201209203623</Ds_Date><Ds_AuthorisationCode>0400</Ds_AuthorisationCode><Ds_PanMask>4242</Ds_PanMask><Ds_TransactionId>c2JmdghAEFRJU0pUB0BcVBQJDx9GBF4VFREEABQHDUECEw4SRBYPQgNXRFpRAURMUQZRExVQBEMVAFJCFVdREhFUU0YWUQVFQ1ZRE0dbBUVAQBs=</Ds_TransactionId><Ds_Merchant_TransactionType>18</Ds_Merchant_TransactionType><Ds_Amount>300</Ds_Amount><Ds_Code>100</Ds_Code><Ds_Merchant_NumTransaction>53</Ds_Merchant_NumTransaction><Ds_Merchant_Guarantees>0</Ds_Merchant_Guarantees><Ds_Merchant_MerchantCode>V98000250</Ds_Merchant_MerchantCode><Ds_Signature>4fb1891acd3f21812640f55d0115c7dcbfc24314</Ds_Signature><Ds_TransactionTime>0.48</Ds_TransactionTime><Ds_TransactionTimeRec>0.001</Ds_TransactionTimeRec></response>';
			 */
			$arrResp = parse_xml(envioDat($header, $arrDatos, $arrVal['url'], $method));
			// $arrResp = parse_xml($recibe);

			$correoMi .= "<br>resp->".json_encode($arrResp)."<-resp Ds-message: ".$arrResp['Ds_Message']."<br>";

			if ($arrResp['Ds_Message'] == 'Accepted') { //respuesta aceptada de tefpay
				$arrDisp["error"] = '';
				if ( $arrEnt['opr'] == 'A') {
					$arrDisp["importe"] = $arrDisp["importe"];
					$arrDisp["estado"] = 'A';
				} else { 
					$arrDisp["importe"] = 0;
					$arrDisp["estado"] = 'L';
				}
			
				$arrDisp['autorizo'] 	= $arrResp['Ds_AuthorisationCode'];
			} else { //respuesta denegada
				$arrDisp["estado"] = 'E';
				$arrDisp["importe"] = '0';
				$arrDisp["error"] = $arrResp['Ds_Message'];
			}

			$sale = actualiza($arrDisp);
			echo $sale;
			$correoMi .= $sale;
		}
	}
	


	$temp->query("insert into tbl_traza (titulo,traza,fecha) values ('Modificacion Preutorizacion', '". str_replace("'", "", $correoMi) ."', unix_timestamp())");
	// echo $correoMi;

} else {
	muestraError("Invalid",$correoMi);
}

/**
 * Parsea la respuesta XML de TefPay
 */
function parse_xml($xmldata) {
	if (empty($xmldata))
	return false;
	$reader = new XMLReader();
	if ($reader->xml($xmldata) == false) {
		return false;
	}
	$data = array();
	$ack = false;
	while ($reader->read()) {
		if ($reader->nodeType == XMLREADER::ELEMENT) {
			$name = $reader->localName;
			if ($name == "response") {
				$ack = true;
				continue;
			}
			if ($reader->read() == false)
			continue;
			$value = $reader->value;
			if (empty($name) || is_null($value))
			continue;
			$data[$name] = $value;
		}
	}
	return ($ack ? $data : $ack);
}

// function send_trans($url, $postData) {
// 	$headers = array( 'Connection: Keep-Alive',
// 	'Content-Type: application/x-www-form-urlencoded');
// 	$ch = curl_init($url);
// 	curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko
// 	/20080311 Firefox/2.0.0.13');
// 	curl_setopt($ch, CURLOPT_URL, $url);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
// 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// 	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
// 	curl_setopt($ch, CURLOPT_TIMEOUT, 40); // timeout en 40 segundos
// 	curl_setopt($ch, CURLOPT_POST, true);
// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
// 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// 	$xml = curl_exec($ch);
// 	curl_close($ch);
// 	return ($xml);
// }

function envioDat($header, $arrDatos, $url, $method) {
	global $correoMi;

	$correoMi .= "url: " . $url . "<br>\n<br>\n";
	$correoMi .= "header: ". json_encode($header) . "<br>\n<br>\n";
	$correoMi .= "datos: " . json_encode($arrDatos) . "<br>\n<br>\n";
	// echo $correoMi; exit;
//muestraError('Candela', $correoMi);
	$options = array(
		CURLOPT_URL				=> $url,
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_HEADER 			=> false,
		CURLOPT_USERAGENT		=> 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
		CURLOPT_SSL_VERIFYPEER	=> true,
		CURLOPT_CONNECTTIMEOUT	=> 30,
		// CURLOPT_SSL_VERIFYHOST  => 1,
		CURLOPT_TIMEOUT			=> 120,
		CURLOPT_POST			=> true,
		CURLOPT_FOLLOWLOCATION	=> true,
		// CURLOPT_VERBOSE			=> 1,
		CURLOPT_POSTFIELDS		=> $arrDatos
		// CURLOPT_CUSTOMREQUEST	=> $method,
		// CURLOPT_HTTPHEADER		=> $header
	);

	$ch = curl_init();
	curl_setopt_array($ch, $options);
	$correoMi .= "options:<br>\n";
	foreach ($options as $key => $value) {
		$correoMi .= "$key => $value<br>\n";
	}
	$correoMi .= "<br>\n";

	$output = curl_exec($ch);
	$responseInfo = curl_getinfo($ch);
	curl_close($ch);

	// if ($responseInfo["http_code"] != 200 && $responseInfo["http_code"] != 201) { //devuelve error
	// 	$output .= "Error en la respuesta: <br>\n".json_encode($responseInfo)."<br>\n";

	// }
	// $correoMi .= "Respuesta: ".$output."<br>\n";
	// muestraError('Modificacion de preautorizo', $correoMi);
	return $output;
}

function actualiza ($arrDisp) {
	global $correoMi, $temp;

	$correoMi .= "arrDisp-".json_encode($arrDisp)."<br>\n";

	$q = "update tbl_transacciones set  id_error = '". $arrDisp["error"] ."' ";

	if ($arrDisp['estado'] != 'E') {
		if ($arrDisp["estado"] == 'A') {//actualiza la operación

			($arrDisp["moneda"] == '978') ? $cambioRate = 1 : $cambioRate = leeSetup ($arrDisp["moneda"]);
			$temp->query("select factmult from tbl_moneda where idmoneda = ". $arrDisp["moneda"]);
			$factmult = $temp->f('factmult');

			$q .= ", valor = ". ($arrDisp["importe"]*$factmult) .", codigo = concat(codigo, '%', '". $arrDisp["autorizo"] ."'), tasa = ".$cambioRate.", euroEquiv = ". (($arrDisp["importe"]/100*$factmult)/($cambioRate));
		} else $q .= ", valor = 0 ";
	}

	$q .= ", estado = '". $arrDisp["estado"] . "', fecha_mod = " . time() . " where idtransaccion = '". $arrDisp["transaccion"] ."' and estado in ('E')";
	$correoMi .= $q."<br>\n";
	$temp->query($q);

	$arrDisp["firma"] = convierte256($arrDisp['comercio'], $arrDisp['idtransaccion'], $arrDisp['importe'], $arrDisp['moneda'], $arrDisp['operacion']);
	$correoMi .= "firma={$arrDisp['comercio']}, {$arrDisp['idtransaccion']}, {$arrDisp['importe']}, {$arrDisp['moneda']}, {$arrDisp['operacion']}<br>";

	//devuelvo el resultado
	return json_encode(array(
		"comercio" 		=> $arrDisp['comercio'], 
		"idtransaccion" => $arrDisp['transaccion'],
		'importe' 		=> $arrDisp['importe'],
		'moneda' 		=> $arrDisp['moneda'],
		'operacion' 	=> $arrDisp['operacion'],
		"firma"			=> $arrDisp["firma"],
		"estado"		=> $arrDisp["estado"],
		"error"			=> $arrDisp['error'],
		"codigo"		=> $arrDisp["autorizo"]
	));

}

function muestraError ($tit, $tex){
	global $temp;
	$temp->query("insert into tbl_traza (titulo,traza,fecha) values ('$tit', '". str_replace("'", "", str_replace('"', "", html_entity_decode($tex, ENT_QUOTES)))."', unix_timestamp())");
	echo json_encode(array("error" => $tit));
	exit;
}
?>