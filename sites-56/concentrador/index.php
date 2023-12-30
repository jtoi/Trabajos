<?php

ini_set('display_errors', 0);
error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('_VALID_ENTRADA', 1);
//ini_set("display_errors", 1);
//error_reporting(E_ALL & ~E_NOTICE);
if (!session_start())
	session_start();
require_once( 'admin/classes/entrada.php' );
//require_once( '../include/sendmail.php' );
require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/hoteles.func.php' );
require_once( 'include/correo.php' );
require_once( 'admin/adminis.func.php' );
require_once( 'include/class.inicio.php' );
require_once( 'include/apiRedsys.php' );

//xdebug_break();

$miObj = new RedsysAPI;
$inicio = new inico();
$temp = new ps_DB;
$correo = new correo;
$ent = new entrada;
$correoMi = "fecha=".date('d/m/Y H:i:s')."<br>";
$titulo = 'Entrada de datos';
header('Content-Type: text/html; charset=utf-8');
$correoMi .= "referer=".$_SERVER['HTTP_REFERER']."<br>";

$d = $_POST;
$r = $_REQUEST;
$g = $_GET;

/****************************comentar**************************************************/
if (stripos(_ESTA_URL, 'localhost') > 0 ||
	stripos(_ESTA_URL, '192.168.0.243') > 0 ||
	stripos(_ESTA_URL, '192.168.0.1') > 0 ) {

/* Reina - esto es solo para pruebas desde el servidor
if (stripos(_ESTA_URL, 'localhost') > 0 ||
	stripos(_ESTA_URL, '192.168.0.243') > 0 ||
	stripos(_ESTA_URL, '192.168.0.1') > 0 ||
	stripos(_ESTA_URL, 'reina-concentrador.server') > 0 ) {

	$dirIp = $_SERVER['REMOTE_ADDR']; */

	// 	date_default_timezone_set('Europe/Berlin');
//	$dirIp = '127.0.0.1';
	$d['comercio']='122327460662'; //Prueba
	// $d['comercio']= '163430526040'; //Transtur
	// $d['transaccion']= '711301';
	$d['transaccion']=substr(time(), -8);
	$d['importe']='50';
	$d['moneda']='978';
	$d['operacion']='P';
	$d['idioma']='es';
	$d['pasarela']='223';
	$d['amex']='17';
	// $d['tipo']='P';
	 //$d['referencia'] = 'sdfgdfgsdfgsdfgsdfgdfgs' //referencia para el pago por referencia
//	/* Hace que se procesen pagos al momento */
//	$query = "insert into tbl_reserva (id_admin, id_comercio, est_comer, codigo, nombre, email, servicio, valor_inicial, moneda, fecha, pMomento, idioma, pasarela, tiempoV, url, amex) values (10, '{$d['comercio']}', 'P', '{$d['transaccion']}', 'jtoirac', 'jtoirac@gmail.com', 'serv', '" . str_replace ( ",", ".", $d['importe'] ) . "', '{$d['moneda']}', unix_timestamp(), 'S', 'es', {$d['pasarela']}, 3, 'admin.administracomercios.com', 2)";
//	$temp->query ( $query );
//  	$d['email']='jtoirac@mailinator.com';
// 	$d['usuario'] = '';
	// $d['IdCustomer']='79068';
	// $d['IdBeneficiary']='394135';
	// $d['AmountToSend']='19629';
	// $d['AmountToReceive']='20000';
	// $d['Charge']='1150';
	// $d['Reason']='1';
	// $q = "select palabra from tbl_comercio where idcomercio = '{$d['comercio']}'";
	// $temp->query($q);
	// $d['clave']	= $temp->f('palabra');
	$d['firma']	= convierte256($d['comercio'], $d['transaccion'], $d['importe'], $d['moneda'], $d['operacion']);
// 	$d['firma'] = 'dd1de16c27f26dd6ff0aedd26e77e5c6';

} else {

	if (!($dirIp = GetIP())) exit();
}
/****************************comentar*************************************************/
foreach ($d as $value => $item) {
//	$correoMi .= "POST- ".$value . "=" . $item . "<br>\n"; REina
	if($value == 'motivo'){
		$correoMi .= "POST- ".$value . "=" . utf8_decode($item) . "<br>\n";
	} else{
		$correoMi .= "POST- ".$value . "=" . $item . "<br>\n";
	}
}
foreach ($g as $value => $item) {
	$correoMi .= "GET- ".$value . "=" . $item . "<br>\n";
}
// foreach ($r as $value => $item) {
// 	$correoMi .= "REQUEST- ".$value . "=" . $item . "<br>\n";
// }

error_log($correoMi);

//$correoMi .= "_SERVER<br>\n";
foreach ($_SERVER as $value => $item) {
	if (
				$value == 'HTTP_USER_AGENT'
			|| 	$value == 'HTTP_ACCEPT_LANGUAGE'
			|| 	$value == 'HTTP_REFERER'
			|| 	$value == 'REMOTE_ADDR'
			) {
		$correoMi .= "entrada SERVER ".$value . "=" . $item . "<br>\n";
			}
}

//print_r($d);
$dirIp = '172.0.0.1';

$correoMi .= "DIR IP - ".$dirIp . "<br>\n";
error_log($correoMi);
if (_MOS_CONFIG_DEBUG) echo $correoMi."<br><br>";
$inicio->ip = $dirIp;
if(isset($d['tpv'])) $inicio->tpv = $d['tpv'];
//echo $inicio->ip;
//echo "ve";
// $dirIp = '200.55.128.45'; //ipcubana
//if (_MOS_CONFIG_DEBUG) $dirIp = '82.223.66.40'; //ipcualquiera
//echo "va";

/* Reina - esto se comenta porque se cambia el chequeo de la IP
if ($cuenta = ipBloqueada($dirIp)) {//verifica que la ip no est� bloqueada
    echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;"><div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;</div></div><div style="text-align:center; margin-top:100px; font-family:sans serif; font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">Su IP '.$dirIp.' est&aacute; bloqueada / Your IP '.$dirIp.' is banned</div>';
    if ($cuenta == 5) {
        $correo->todo(11, 'IP banned entrada '.$dirIp, ". Se ha bloqueado el acceso de la operaci�n desde la IP ".$dirIp."<br>".$correoMi);
    }
    exit();
} */

// Se verifica que la IP no este bloqueda por algun motivo
// 1. verifica que no sea una ipblanca
$q = sprintf("SELECT count(*) total FROM tbl_ipblancas WHERE ip='%s'", $dirIp);
$temp->query($q);
error_log($q);
error_log("total=".$temp->f('total'));
$correoMi .= $q;
$correoMi .= "total=".$temp->f('total');
if ($temp->f('total') > 0) { // Es una IP Blanca
	$temp->query("update tbl_ipblancas set fecha = ".time()." where ip = '$dirIp'");
} else{
	// 2. verifica que est� en la tabla de las ip bloqueadas por login
	$q = sprintf("SELECT login FROM tbl_ipBL WHERE ip='%s'", $dirIp);
	$temp->query($q);
	error_log($q);
	error_log("login=".$temp->f('login'));
	$correoMi .= $q;
	$correoMi .= "login=".$temp->f('login');
	if($temp->f('login') >= 3){ // Maximo de intentos por login
		echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;"><div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;</div></div><div style="text-align:center; margin-top:100px; font-family:sans serif; font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">Su IP '.$dirIp.' est&aacute; bloqueada / Your IP '.$dirIp.' is banned</div>';
		error_log('Intento de pago desde IP bloqueada');
		$correo->todo(11, 'IP bloqueada '.$dirIp, ". Se ha bloqueado el acceso de la operaci�n desde la IP ".$dirIp."<br>".$correoMi);
		exit();
	} else{
		// 3. verifica que no est� bloqueada por pagos denegados
		$q = sprintf("select idips from tbl_ipbloq where ip = '%s' and bloqueada = 1", $dirIp);
		$temp->query($q);
		error_log($q);
		error_log("num_rows=".$temp->num_rows());
		$correoMi .= $q;
		$correoMi .= "num_rows=".$temp->num_rows();
		if ($temp->num_rows() !== 0) {
			echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;"><div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;</div></div><div style="text-align:center; margin-top:100px; font-family:sans serif; font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">Su IP '.$dirIp.' est&aacute; bloqueada, contacte a su comercio / Your IP '.$dirIp.' is banned, contact to your e-commerce</div>';
			error_log('Intento de pago desde IP bloqueada');
			$correo->todo(11, 'IP bloqueada '.$dirIp, ". Se ha bloqueado el acceso de la operaci�n desde la IP ".$dirIp."<br>".$correoMi);
			exit();
		} else{
			// 4. verifica que no est� bloqueada por intentos de ataque u otros motivos
			$q = sprintf("SELECT cuenta FROM tbl_ipBL WHERE ip='%s'", $dirIp);
			$temp->query($q);
			error_log($q);
			error_log("cuenta=".$temp->f('cuenta'));
			$correoMi .= $q;
			$correoMi .= "cuenta=".$temp->f('cuenta');
			if ($temp->num_rows() > 0) { // Ya esta registrada la IP
				$cuenta = $temp->f('cuenta');
				if( $cuenta > 0){ // esta registrada por intentos de pago, ataques y otros
//					$q = sprintf("update tbl_ipBL set cuenta = (cuenta + 1), fecha = unix_timestamp() where '%s'", $dirIp);
//					$temp->query($q);
//					error_log($q);

					$newCuenta = floor($cuenta / 2);
					error_log("realCuenta=".$newCuenta);
					$correoMi .= "realCuenta=".$newCuenta;
					if($newCuenta >= 5){ // Maximo de intentos permitidos por IP
						echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;"><div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;</div></div><div style="text-align:center; margin-top:100px; font-family:sans serif; font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">Su IP '.$dirIp.' est&aacute; bloqueada, contacte a su comercio / Your IP '.$dirIp.' is banned, contact to your e-commerce</div>';
						error_log('Intento de pago desde IP bloqueada');
						$correo->todo(11, 'IP bloqueada '.$dirIp, ". Se ha bloqueado el acceso de la operaci�n desde la IP ".$dirIp."<br>".$correoMi);
						exit();
					}
				}
			}
		}
	}
}

/* Reina - temporal para pruebas */
//detecci�n de env�o de operaciones detr�s de proxy
$test_HTTP_proxy_headers = array('HTTP_VIA', 'VIA', 'Proxy-Connection', 'HTTP_X_FORWARDED_FOR', 'HTTP_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED', 'HTTP_CLIENT_IP', 'HTTP_FORWARDED_FOR_IP', 'X-PROXY-ID', 'MT-PROXY-ID', 'X-TINYPROXY', 'X_FORWARDED_FOR', 'FORWARDED_FOR', 'X_FORWARDED', 'FORWARDED', 'CLIENT-IP',  'CLIENT_IP', 'PROXY-AGENT', 'HTTP_X_CLUSTER_CLIENT_IP', 'FORWARDED_FOR_IP', 'HTTP_PROXY_CONNECTION');
foreach($test_HTTP_proxy_headers as $header){ 		
	if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
		// $correo->todo(11, 'IP banned entrada '.$dirIp, "Se ha bloqueado el acceso de la operaci�n desde la IP ".$dirIp." por usar proxy<br>".$correoMi);
		// sendTelegram('IP banned entrada '.$dirIp. "Se ha bloqueado el acceso de la operaci�n desde la IP ".$dirIp." por usar proxy<br>");
		exit("Please disable your proxy connection!");
	} 	
}
/* temporal */
// echo "DIME---".ipCuba($dirIp);


if ($ent->isReal($d['TotalAmount'], 9)) $d['importe'] = $d['TotalAmount'];
// if ($d['CurrencyToSend']) $d['moneda'] = $d['CurrencyToSend'];
if ($ent->isReal($d['moneda'], 3)) $d['CurrencyToSend'] = $d['moneda'];

if ($d['comercio'] && $d['transaccion'] && $d['importe'] && $d['moneda'] && $d['operacion'] && $d['firma']) {
    $correo->set_subject($titulo);
	//if ($d['importe'] > 500000) muestraError ("falla por importe excedido", $correoMi);

	if (!($inicio->comer = $ent->isAlfanumerico($d['comercio'], 15))) {
		muestraError ("falla por comercio", $correoMi);
	}

    $q = "select estado, nombre from tbl_comercio where idcomercio = '".$inicio->comer."'";
    $temp->query($q);
    if ($temp->getNumRows() == 1) {
		if ($temp->f('estado') == 'P') {
			$nc = $temp->f('nombre');
			if ($dirIp == '3.121.188.123') muestraError("falla por ip proxy gaviota", $correoMi); //verifica que la ip no sea del proxy de gaviota
	/**
			if ($dirIp == '104.248.174.146') muestraError("falla por ip baneada", $correoMi); //IP bloqueadas por Ivett correo 5/04/22
			if ($dirIp == '37.120.130.50') muestraError("falla por ip baneada", $correoMi); //IP bloqueadas por Ivett correo 5/04/22
			if ($dirIp == '142.93.144.72') muestraError("falla por ip baneada", $correoMi); //IP bloqueadas por Ivett correo 5/04/22
			if ($dirIp == '192.241.198.97') muestraError("falla por ip baneada", $correoMi); //IP bloqueadas por Ivett correo 5/04/22
			if ($dirIp == '167.99.179.174') muestraError("falla por ip baneada", $correoMi); //IP bloqueadas por Ivett correo 5/04/22
			if ($dirIp == '45.33.71.134') muestraError("falla por ip baneada", $correoMi); //IP bloqueadas por Ivett correo 5/04/22
			if ($dirIp == '192.241.201.66') muestraError("falla por ip baneada", $correoMi); //IP bloqueadas por Ivett correo 5/04/22
			if ($dirIp == '72.193.43.250') muestraError("falla por ip baneada", $correoMi); //IP bloqueadas por Ivett correo 5/04/22
	*/

			if (ipCuba($dirIp)) {//verifica que si la ip es de cuba
				echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;"><div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;</div></div><div style="text-align:center; margin-top:100px; font-family:sans serif; font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">Tenemos problemas para procesar su operaci&oacute;n, por favor intente m&aacute;s tarde.</div>';
				error_log("Se ha detenido la operaci�n {$d['transaccion']} del comercio {$nc} por provenir de IP cubana $dirIp");
				$correoMi .= "Se ha detenido la operaci�n {$d['transaccion']} del comercio {$nc} por provenir de IP cubana $dirIp<br>";
				$inicio->saltosPasar("falla por ip cubana");
				muestraError("falla por ip cubana", $correoMi);

			//    $correo->todo(11, 'Operaci�n de IP cubana detenida '.$dirIp, "Se ha detenido la operaci�n {$d['transaccion']} del comercio {$d['comercio']} por provenir de IP cubana");

				exit();
			}
		}
	} else muestraError ("falla por comercio2", $correoMi);

	if (!($inicio->tran = $ent->isUrl($d['transaccion'], 19))) {
		muestraError ("falla por transaccion", $correoMi);
	}
	if (!($inicio->imp = $ent->isReal($d['importe'], 9)) || $d['importe'] == 0) {
		muestraError ("falla por importe", $correoMi);
	}
	if (!($inicio->mon = $ent->isReal($d['moneda'], 3))) {
		muestraError ("falla por moneda", $correoMi);
	} else {
		$q = "select count(idmoneda) total from tbl_moneda where idmoneda = '" . $d['moneda'] . "' and activo = 1";
		$temp->query($q);
		if ($temp->f('total') != 1) {
			muestraError ("falla por moneda", $correoMi);
		}
	}
	if (isset($d['amex']) && $d['amex'] > 0) $inicio->amex = $d['amex'];
	else $inicio->amex = '2';
	if (isset($d['metodo']) && $d['metodo'] > 0) $inicio->amex = $d['metodo'];

	/**
	 * Las operaciones pueden ser:
	 * P - pago con Tarjeta
	 * A - Preautorizaciones y confirmacion de preautorizo (se congela en la tarjeta del Cliente el dinero de la operaci�n por un tiempo)
	 * R - Pagos recurrentes (se realiza un primer pago con la tarjeta y el resto de los pagos sin necesidad de poner la tarjeta)
	 * L - liberaci�n de una operaci�n de preautorizo
	 * D - devoluci�n autom�tica
	 * S - solicitud de devoluci�n
	 * Q - Quita la referencia anterior (asociada a otra tarjeta) y pide una referencia nueva
	 */
	$inicio->opr = strtoupper($d['operacion']);
	if (!$inicio->opr =='P' 
		|| !$inicio->opr =='A' 
		|| !$inicio->opr =='R' 
		|| !$inicio->opr == 'S' 
		|| !$inicio->opr == 'Q'
		) {
		muestraError ("falla por operacion", $correoMi);
	}
	if ($inicio->opr =='R' && strlen($d['codRec']) > 10) {
		if (!($inicio->refer = $ent->isAlfanumerico($d['referencia']))) {
			muestraError("falla por referencia", $correoMi);
		}
	}
	if (!($inicio->frma = $ent->isAlfanumerico($d['firma']))) {
		muestraError ("falla por firma", $correoMi);
	}
	if ($d['pasarela']) {
		if (!($inicio->pasa = $ent->isEntero($d['pasarela'], 3))) {
			muestraError ("falla por tipo de pasarela", $correoMi);
		}
	}
	if ($d['idioma']) {
		$inicio->idi = strtolower($d['idioma']);
		if (!$inicio->idi == 'es' || !$inicio->idi == 'en' || !$inicio->idi == 'it') {
			muestraError ("falla por idioma", $correoMi);
		}
	} else {$inicio->idi = 'es';}

//	chequeo que el comercio est� activo
	if (!$inicio->verComer()) muestraError ($inicio->err, $correoMi.$inicio->log);
	$correoMi .= $inicio->log;

	if (isset($d['tipo'])) {
		if (in_array($d['tipo'],array('D','W','P'))) $inicio->tipo = $d['tipo'];
		else muestraError ("falla por tipo de operacion ", $correoMi);
	}

	// reviso si el pago es presencial y se ejecuta a trav�s de las ips de nuestra VPN
	$q = "select count(*) total from tbl_reserva where codigo = '{$inicio->tran}' and id_comercio = '{$inicio->comer}' and pMomento = 'S'";
	// $Mi .= "<br>".$q."<br>";
	$temp->query($q);
	if ($temp->f('total') > 0 || $d['tipo'] == 'P'){//pago presencial analizo la ip

		$correoMi .= "<br><br>PREG".preg_match("/(android|Android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|iPhone|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
		$correoMi .= "<br><br>AGENT".$_SERVER["HTTP_USER_AGENT"];

		if(!preg_match("/(android|Android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|iPhone|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])) {
			// $Mi .= "PAGO PRESENCIAL analizo ip<br>";
			$q = "select count(*) total from tbl_ipsVPN where ip = '$dirIp'";
			// $Mi .= $q."<br>";
			$temp->query($q);
			if ($temp->f('total') == 0) {
				$q = "select nombre from tbl_comercio where idcomercio = '{$inicio->comer}'";
				$temp->query($q);
				// $Mi .= "La ip no se encuentra dentro de las ips de nuestra VPN<br>";
				sendTelegram("El comercio ".$temp->f('nombre')." est� tratando de pagar desde la IP ".$dirIp." que no es nuestra VPN<br>");
				echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;"><div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;</div></div><div style="text-align:center; margin-top:100px; font-family:sans serif; font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">Su IP '.$dirIp.' no corresponde a las de nuestra VPN</div>';
				muestraError ('Operacion presencial fuera de la VPN', $correoMi);
				exit();
			}
		}
	}

	//paguelofacil
	if ($d['email']) {
		if (!($inicio->email = $ent->isCorreo($d['email'],100))) muestraError ("falla por email", $correoMi);
		if (!$inicio->verfEmail()) muestraError ($inicio->err, $correoMi.$inicio->log);
		$correoMi .= $inicio->log;
	}
	if ($d['nomb']) if (!($inicio->nomb = $ent->isAlfabeto($d['nomb'],40))) muestraError ("falla por nombre", $correoMi);
	if ($d['apell']) if (!($inicio->apell = $ent->isAlfabeto($d['apell'],50))) muestraError ("falla por apellido", $correoMi);
	if ($d['secur']) if (!($inicio->secur = $ent->isEntero($d['secur'],3))) muestraError ("falla por c&oacute;digo de seguridad CVV2 incorrecto", $correoMi);
	if ($d['ano']) if (!($inicio->ano = $ent->isEntero($d['ano'],4))) muestraError ("falla por a&ntilde;o de vencimiento de la tarjeta incorrecto", $correoMi);
	if ($d['mes']) if (!($inicio->mes = $ent->isEntero($d['mes'],2))) muestraError ("falla por mes de vencimiento de la tarjeta incorrecto", $correoMi);
	if ($d['tel']) if (!($inicio->telf = $ent->isAlfanumerico($d['tel'],12))) muestraError ("falla por tel&eacute;fono incorrecto", $correoMi);
	if ($d['dir']) if (!($inicio->direcc = $ent->isAlfanumerico($d['dir'],150))) muestraError ("falla por direcci&oacute;n incorrecta", $correoMi);
	if ($d['tarj']) {
		if (!($inicio->tarj = $ent->isEntero($d['tarj'],16))) {
			muestraError ("falla por tarjeta incorrecta", $correoMi);
		} else {
			if (!$inicio->luhn($d['tarj'])) muestraError("falla por n�mero de tarjeta incorrecto", $correoMi);
		}
	}

	//paytpv
	if ($d['usuario']) {
		if(!($usuario = $ent->isUrl($d['usuario'],100))) muestraError ("falla por datos de usuario incorrectos", $correoMi);
		$inicio->verfTkusr($usuario);
	}

//	chequeo de firma
	if (!$inicio->verFir()) {muestraError ($inicio->err, $correoMi.$inicio->log);}
	$correoMi .= $inicio->log;

	// Si es un pago con Tarjetas, verificar si en la peticion viene una url de respuesta
	if ($inicio->opr =='P' && (isset($d['url']) || isset($d['urldirecta'])) ){
//		$q = "insert into tbl_ComerTransUrl (id, idcomercio, idOperacion, urlRetorno) values (NULL, '".$d['comercio']."', '".$d['transaccion']."', '".$d['url']."')"; Reina

		$q = "select id from tbl_ComerTransUrl where idcomercio = '".$d['comercio']."' and idOperacion = '".$d['transaccion']."'";
		$temp->query($q);
		$correoMi .= "\n<br>Se busca si la la operacion esta registrada para ese comercio <br>\n";
		$correoMi .= "$q<br>\n";
		if ($temp->num_rows() == 0) { // No esta registrada, entonces se inserta
			$q = "insert into tbl_ComerTransUrl (id, idcomercio, idOperacion, fecha";
			if(isset($d['url'])) $q .= ", urlRetorno";
			if(isset($d['urldirecta'])) $q .= ",urlLlegada";
			$q .= ") values (NULL, '".$d['comercio']."', '".$d['transaccion']."', unix_timestamp()";
			if(isset($d['url'])) $q .= ", '".$d['url']."' ";
			if(isset($d['urldirecta'])) $q .= ", '".$d['urldirecta']."' ";
			$q .= ")";

			$correoMi .= "\n<br>Se inserta la operacion <br>\n";
		} else{
			$id = $temp->f('id');
			$q = "update tbl_ComerTransUrl set fecha = ".time();
			if(isset($d['url'])) $q .= ", urlRetorno = '".$d['url']."' ";
			if(isset($d['urldirecta'])) $q .= ",urlLlegada = '".$d['urldirecta']."' ";
			$q .= "where id = ".$id;
			$correoMi .= "\n<br>Se actualiza la operacion <br>\n";
		}
		$temp->query($q);
		$correoMi .= "$q<br>\n";
	}

	//Modificaci�n para las solicitudes de devoluci�n
	if ($inicio->opr == 'S') { //si la operaci�n es una solicitud de devoluci�n

		$correoMi .= "<br>Es una solicitud de devoluci�n<br>";

		// Verificar que la operacion solicitada a devolver no este En Proceso de Reclamacion
		$q = "select solRec from tbl_transacciones where idtransaccion = '".$d['transaccion']."'";
		$temp->query($q);
		$solRec = $temp->f('solRec');
		$correoMi .= "\n<br>Se busca si la la operacion esta En Proceso de Reclamacion <br>\n";
		$correoMi .= "$q<br>\n";
		$correoMi .= "solRec=$solRec<br>\n";
		if($solRec == 1){
			muestraerror("Esta operaci�n est� en Proceso de Reclamaci�n y no admite devoluciones", $correoMi);
		}

		if (strlen($d['motivo']) < 8 || strcmp($d['motivo'], $d['firma']) == 0){
			muestraerror("falla por motivo de la solicitud de devoluci�n", $correoMi);
		}
		//salto a la solicitud de devoluci�n
//		$arrSal = json_decode(solDevOper($d['transaccion'], $d['importe'], $d['comercio'], $correoMi, $d['motivo'])); Reina
		$arrSal = json_decode(solDevOper($d['transaccion'], $d['importe'], $d['comercio'], $correoMi, utf8_decode($d['motivo'])));
		$correoMi = utf8_decode($arrSal->correoMi."<br>Resultado->".$arrSal->result."<br>Causa->".$arrSal->comen);


		// error_log($correoMi);
		$correo->todo(9, 'Entrada de datos', $correoMi);
		unset($arrSal->correoMi);
		$object = (array) $arrSal;
		$object = get_object_vars($arrSal);
		error_log(json_encode($object));

		echo json_encode($object);
		exit;
	}


	if ($inicio->comer == '527341458854' //Cimex
			|| $inicio->comer == '144172448713' //Prueba Cimex
			|| $inicio->comer == '163430526040' //tocopay
// 			|| $inicio->comer == '122327460662' //Prueba
			) {
		if ($d['IdCustomer'] && $d['AmountToReceive'] && $d['Charge'] && $d['AmountToSend'] && $d['IdBeneficiary'] && $d['Reason']){
			if ($d['NumCuenta'] == '') $numcta = '-1'; else $numcta = $d['NumCuenta'];
			if (!isset($d['monedaDeposito'])) $mor = '840'; else {
				$q = "select idmoneda from tbl_moneda where moneda = '".strtoupper($d['monedaDeposito'])."'";
				$correoMi .= $q."<br>";
				$temp->query($q);
				if ($temp->num_rows() == 0) muestraerror("falla por monedaDeposito", $correoMi);
				$mor = $temp->f('idmoneda');

				if ($mor == '192') $d['AmountToReceive'] = round($d['AmountToReceive'] / 24); //si la moneda es CUP divido entre 24
				$correoMi .= "AmountToReceive = ". $d['AmountToReceive']."<br>";
			}
			$inicio->datAis = array(
					'idremitente' => $d['IdCustomer'],
					'iddestin' => $d['IdBeneficiary'],
					'importerecive' => $d['AmountToReceive'],
					'comision' => $d['Charge'],
					'importenvia' => $d['AmountToSend'],
					'rason' => $d['Reason'],
					'numcta' => $numcta,
					'monrecibe' => $mor
			);

			//para el pago por referencia en Fincimex
			$correoMi .= "Es pago por referencia?->".leeSetup('pagoRec');
			if (leeSetup('pagoRec') == 1 
				// || $inicio->opr == 'R' 
				) {
				// $correoMi .= "PAGO POR REFERENCIA<br>";
				// if ((isset($d['cambTar']) && $d['cambTar'] == 1) || 1==1) { //si fincimex pide cambiar la tarjeta borro el identificador guardado quitar la igualdad entre 1
				// 	$q = "delete from tbl_referencia where idtransaccion in (select idtransaccion from tbl_aisOrden o, tbl_aisCliente c where o.idcliente = c.id and c.idcimex = '{$d['IdCustomer']}')";
				// 	$q = "delete from tbl_referencia where idtransaccion in (select idtransaccion from tbl_aisOrden o, tbl_aisCliente c where o.idcliente = c.id and c.idcimex not in (79068, 95107) and c.idcimex = '{$d['IdCustomer']}')";;//evito que se borren los ids de Tamara y Mela
				// 	$correoMi .= "$q<br>";
				// 	$temp->query($q);
				// } else { //si no es as�, leo el c�digo y lo pongo como si el comercio lo hubiera enviado en los datos
					// $q = "select r.codConc from tbl_aisCliente c, tbl_referencia r, tbl_aisOrden o where c.id = o.idcliente and c.idcimex = '{$d['IdCustomer']}' and r.idtransaccion = o.idtransaccion order by r.fecha desc limit 0,1";
					// $correoMi .= $q."<br>";
					// $temp->query($q);
					// if ($temp->num_rows() == 1)
					// 	$inicio->refer = $temp->f('codConc');
				// }
				// if ($d['IdCustomer'] == '79068' || $d['IdCustomer'] == '95107' || $d['IdCustomer'] == '262') {
				// 	$q = "select r.codConc from tbl_aisCliente c, tbl_referencia r, tbl_aisOrden o where c.id = o.idcliente and c.idcimex = '{$d['IdCustomer']}' and r.idtransaccion = o.idtransaccion order by r.fecha desc limit 0,1";
				// 	$correoMi .= $q."<br>";
				// 	$temp->query($q);
				// 	if ($temp->num_rows() == 1)
				// 		$inicio->refer = $temp->f('codConc');
					
                // 	$inicio->opr = 'R'; //para las pruebas 

				// } else $inicio->opr = 'P';

                // $inicio->opr = 'R'; //para las pruebas 
                //se obliga a todas las operaciones de Fincimex que sean pagos por referencia
                // $inicio->opr = 'P'; //descomentar esto para poner todas las oepraciones de Tocopay pagos por referencia
            }

		} else  {
			muestraError ("falla por datos insuficientes en AIS", $correoMi);
		}
	}

				$correoMi .= "referencia->".$inicio->refer."<br>";
				$correoMi .= "operacion->".$inicio->opr."<br>";

	//Pago por referencia implementaci�n verificaci�n de la referencia enviada
	//si la referencia enviada no es v�lida borro la referencia pero no detengo
	//el pago
	if ($inicio->opr == 'R') {
		//chequeo que el comercio tenga permitido los pagos por referencia
		$q = "select count(id) total from tbl_comercio where idcomercio = '$inicio->comer' and pagoxRef = 1";
		$temp->query($q);
		$correoMi .= $q."<br>";

		if ($temp->f('total') == 1) { //comercio autorizado a realizar los pagos por referencia
			if (strlen($inicio->refer) > 5) { //el comercio env�a la referencia
				$correoMi .= "PAGO POR REFERENCIA, referencia->".$inicio->refer." / ";
				// $inicio->refer = pagoxRef($inicio->comer, $inicio->opr, $inicio->refer);
				$correoMi .= " referencia->".$inicio->refer."<br>";
			}
		} else {//si el comercio no est� autorizado cambio el tipo de operaci�n a pago normal
			$inicio->opr = 'P';
			$inicio->refer = '';
		} 
		$correoMi .= "Referencia calculada ".$inicio->refer."<br>";
	}
	$correoMi .= "operacion->".$inicio->opr."<br>";

//	chequeo que la direccion IP no est� bloqueada
	if (!$inicio->verIP()) {muestraError($inicio->err, $correoMi.$inicio->log);}
	$correoMi .= $inicio->log;

//	chequeo por monto de la transaccion
	$inicio->alerSegur();
	$correoMi .= $inicio->log;

	//Modificaci�n para las solicitudes de devoluci�n
	if ($inicio->opr == 'S') { //si la operaci�n es una solicitud de devoluci�n

		$correoMi .= "<br>Es una solicitud de devoluci�n<br>";

		if (strlen($d['motivo']) < 8 || strcmp($d['motivo'], $d['firma']) == 0 ){
			muestraerror("falla por motivo de la solicitud de devoluci�n", $correoMi);
		}
		//salto a la solicitud de devoluci�n
//		$arrSal = json_decode(solDevOper($d['transaccion'], $d['importe'], $d['comercio'], $correoMi, $d['motivo'])); Reina
		$arrSal = json_decode(solDevOper($d['transaccion'], $d['importe'], $d['comercio'], $correoMi, utf8_decode($d['motivo'])));
		$correoMi = utf8_decode($arrSal->correoMi."<br>Resultado->".$arrSal->result."<br>Causa->".$arrSal->comen);

		// error_log($correoMi);
		$correo->todo(9, 'Entrada de datos', $correoMi);
		unset($arrSal->correoMi);
		$object = (array) $arrSal;
		$object = get_object_vars($arrSal);
		error_log(json_encode($object));

		echo json_encode($object);
		exit;
	}

//	Verifica que la transaccion no se repita
	if (!$inicio->verTran()) {
		if (count($inicio->arrCli)) {
			$correo->to($inicio->arrUsu[1]);
			$correo->todo(57,'Aviso de Transacci�n duplicada','Estimado(a) '.$inicio->arrUsu[0].'<br><br>
				El usuario <b>'.$inicio->arrCli[0].'</b> con correo <a href="mailto:'. $inicio->arrCli[1] .'">'. $inicio->arrCli[1] .'</a>
				est&aacute; intentando volver a pagar sobre la invitaci&oacute;n de pago ya vencida de la operaci&oacute;n con referencia de comercio <b>'.
				$inicio->tran.'.</b> y n�mero de transacci�n <b>'. $inicio->arrCli[2].'</b>. <br><br>
				Por favor comun&iacute;quese con &eacute;l y env&iacute;ele una nueva Invitaci&oacute;n de Pago.
				Para esto &uacute;ltimo, puede acceder a la operaci&oacute;n a trav&eacute;s de la opci&oacute;n del men&uacute; REPORTES / Clientes<br><br>
				Administrador de comercios');
			$correoMi .= "Se env�an el correo de transacci�n duplicada\n<br>";
		}
		muestraError($inicio->err, $correoMi.$inicio->log);
	}

	$correoMi .= $inicio->log;

//	chequeo la pasarela
	if (!$inicio->cheqPas()) muestraError ($inicio->err, $correoMi.$inicio->log);
	$correoMi .= $inicio->log;

	if ($inicio->pasa != 57 and $inicio->datPas ['tipo'] != 'melt') {
	echo '<div id="bannn" style="" >
			<img scr="'._ESTA_URL.'/images/Banners-visa-master.png" /></div><script>
			 var ancho = "background-image:url(\''._ESTA_URL.'/images/Banners-visa-master.png\'); width:400px; height:62px; margin-left:"+
			 	((window.innerWidth)-400)/2+"px; border: 1px solid #000;border-bottom: 0;margin-top: 40px;";
				document.getElementById("bannn").setAttribute("style",ancho);
				document.writeln("<div id=\"avisoIn\" style=\"margin:0 0 0 "+
			  ((window.innerWidth)-400)/2
			  +"px; width:400px; text-align:center;\">"
			  )</script>
			  <strong>Este comercio admite Comercio Electr&oacute;nico Seguro (CES).</strong><br /><br />
			  El banco de su tarjeta requiere que se registre para poder comprar en Internet.
			  En breves momentos le pondremos en contacto con su banco para que autorice el pago.<br /><br />
			  <span style="color:red;font-weight:bold;">Importante:</span>
			  La siguiente p&aacute;gina est&aacute; fuera del control de este establecimiento y es responsabilidad de su Banco,
			  por lo que, si tiene alg&uacute;n problema, por favor dirija sus
			  reclamaciones al Banco emisor de la tarjeta.<br /><br />
			  Algunos bancos no permiten realizar compras en Internet a sus clientes.
			  Por favor, si tiene problemas con su tarjeta intente de nuevo la compra con otra tarjeta.<br /><br />
			  ';
	if ($inicio->pasa != 1)
		echo 'El proceso de compra se va a completar a trav&eacute;s de la pasarela de Comercio Electr&oacute;nico Seguro (CES),
				garantiz&aacute;ndole la m&aacute;xima seguridad en su proceso de compra. Para completar su
				compra, ser&aacute; necesario que su tarjeta est&eacute; registrada para operar en Comercio
				Electr&oacute;nico Seguro; en caso de duda consulte con su entidad financiera.<br /><br />';
	echo '<strong>Su transacci&oacute;n est&aacute; siendo procesada...</strong><br><br>';
	echo '<strong>This trade supports Secure Electronic Commerce Gateway (CES).</strong><br /><br />
				The bank card registration is required to purchase online.
				In a moment we will contact your bank to authorize the payment.<br /><br />
				<span style="color:red;font-weight:bold;">Important: </span>The following site is outside the control
				of this property and responsibility of your bank, so if you have any problems, please direct your
				complaints to the card issuing bank. / La seguente pagina &egrave; fuori dal nostro controllo e fa parte dei sistemi di pagamento online attivati dalla sua banca. Se ha dei problemi per favore si diriga presso la sua filiale.<br /><br />
				Some banks do not allow online purchases to its customers.
				Please, if you have problems with your card purchase try again with another card.<br><br>';
	if ($inicio->pasa != 1)
		echo 'The purchase process will be completed through the Secure Electronic Commerce Gateway (CES),
				guaranteeing maximum safety in your buying process.
				To complete your purchase,
				you will need your card is registered to operate in Secure Electronic Commerce,
				in case of doubt check with your financial institution.<br><br>';
	echo '<strong>Your transacction is been processed...</strong></div>';
	} else {

		echo '<script>document.writeln("<div id=\"avisoIn\" style=\"margin:0 0 0 "+
			  ((window.innerWidth)-400)/2
			  +"px; width:400px; text-align:center;\">"
			  )</script><strong>Su transacci&oacute;n est&aacute; siendo procesada...</strong><br><br><strong>Your transacction is been processed...</strong></div>';
	}
	$correoMi .= "Ejecuta la transacci�n\n<br>";
//	Ejecuta la transacci�n
	$sale = $inicio->operacion();
	if (!$sale) muestraError ($inicio->err, $correoMi.$inicio->log);
	else {
		$correoMi .= $inicio->log;
		if ($inicio->pasa != 57) echo $sale;
		else {
			$dat = json_decode($sale);
			$options = array(
					CURLOPT_RETURNTRANSFER	=> true,
					CURLOPT_SSL_VERIFYPEER	=> false,
					CURLOPT_POST			=> true,
					CURLOPT_VERBOSE			=> true,
					CURLOPT_URL				=> _ESTA_URL."/rep/llegada.php",
					CURLOPT_POSTFIELDS		=> $sale,
					CURLOPT_SSL_VERIFYHOST	=> false
			);

			$ch = curl_init();
			curl_setopt_array($ch , $options);
			$salida = curl_exec($ch);
			echo "salida="._ESTA_URL."/rep/llegada.php".$salida;

			if (curl_errno($ch)) echo "Error en la resp de Sipay:".curl_strerror(curl_errno($ch))."<br>\n";
			$crlerror = curl_error($ch);

			if ($crlerror) echo "Error en la resp de Sipay:".$crlerror."<br>\n";
			$curl_info = curl_getinfo($ch);
			curl_close($ch);

			$arrCurl = json_decode($salida);
			print_r($curl_info);echo "<br><br>";
		}
	}

} else {
	$correoMi .= "invalid";
	echo "<!-- invalid -->";
	$ref = $_SERVER['HTTP_REFERER'];
	$query = "insert into tbl_listaIp (ip, fecha, refer) values ('$dirIp', " . time() . ", '$ref')";
	marcaIP($dirIp);
	$correoMi .= "<br>comercio=" . $d['comercio'] . " && transaccion=" . $d['transaccion'] . " && importe=" .
					$d['importe'] . " && moneda=" . $d['moneda'] . " && operaci&oacute;n=" .
					$d['operacion'] . " && firma=" . $d['firma'];
	$temp->query($query);
}

//correoAMi($titulo, $correoMi);
$correo->set_subject($titulo);
$correo->set_message($correoMi);
$correo->envia(9);
//echo $correoMi;

// Debugger
if (_MOS_CONFIG_DEBUG) {

	echo '<div id="debD"><pre style="font-size:11px">';
	echo "<hr /><hr /><br>Logs:<br>";
	echo $correoMi;
	echo "<hr /><hr /><br>Querys:<br>";
	echo $temp->log;
	echo "<hr /><hr /><br>Datos:<br>";
	echo "<hr /><hr /><br>Variables usadas:<br>";
	print_r(array_keys(get_defined_vars()));
	echo $textoCorreo;
	echo "</div>";
}

function muestraError ($etiqueta, $textoCorreo) {
	global $d, $temp, $inicio, $correo;
	$saleErrores = ''; $pase=false;	$errorId = 0;
	error_log("etiqueta=$etiqueta");
	error_log("textoCorreo=$textoCorreo");

	//Procesamiento de mensaje cuando error es por l�mites de la pasarela
	if (count($inicio->arrPD) > 0 && $pase == false) {
		$saleErrores = 'La operaci�n '.$d['transaccion'].' del comercio: '.$inicio->datCom['nombre'].'('.$inicio->datCom['id'].') se detuvo por l�mites de los TPVs:<br>';

		for($i=0; $i<count($inicio->arrMD); $i++){
			$temp->query("select nombre from tbl_pasarela where idPasarela = ".$inicio->arrPD[$i]);
			$saleErrores .= $temp->f('nombre')." (".$inicio->arrPD[$i].") - ".$inicio->arrMD[$i]."<br>";
		}
		$errorId = substr($inicio->arrMD[$i-1],strpos($inicio->arrMD[$i-1],'{')+1,strpos($inicio->arrMD[$i-1],'}')-strpos($inicio->arrMD[$i-1],'{')-1);
		$exp = explode(":",$inicio->arrMD[$i-1]);
		$inicio->arrMD = array();
		$pase = true;

	}

	//Procesamiento de mensaje cuando error es por l�mites del comercio
	if (count($inicio->arrMD) == 1 && $pase == false) {
		$saleErrores = 'La operaci�n '.$d['transaccion'].' del comercio: '.$inicio->datCom['nombre'].'('.$inicio->datCom['id'].') se detuvo por l�mites del Comercio:<br>';
		$saleErrores = $saleErrores.$inicio->arrMD[0];
		$errorId = substr($inicio->arrMD[0],strpos($inicio->arrMD[0],'{')+1,strpos($inicio->arrMD[0],'}')-strpos($inicio->arrMD[0],'{')-1);
		$exp = explode(":",$inicio->arrMD[0]);
		error_log("errorId=$errorId");
		$pase=true;
	}

	//Preparaci�n de mensaje a mostrar al cliente
	switch ($inicio->idi) {
		case 'en':
			$file = 'english';
		break;
		case 'it':
			$file = 'italiano';
		break;
		default:
			$file = 'spanish';
	}

	error_log("errorId=$errorId");
	error_log("admin/lang/".$file.".php");
	require_once("admin/lang/".$file.".php");
	switch ($errorId) {
		case '1':
			$teto = (_ERROR_UNO);
			$cam = "{1}";
		break;
		case '2':
			$teto =  (_ERROR_DOS).$exp[1];
			$cam = "{2}";
		break;
		case '3':
			$teto =  (_ERROR_TRES).$exp[1];
			$cam = "{3}";
		break;
		case '4':
			$teto =  (_ERROR_CUATRO).$exp[1];
			$cam = "{4}";
		break;
		case '5':
			$teto =  (_ERROR_CINCO).$exp[1];
			$cam = "{5}";
		break;
		case '6':
			$teto =  (_ERROR_SEIS).$exp[1];
			$cam = "{6}";
		break;
		case '7':
			$teto =  (_ERROR_SIETE).$exp[1];
			$cam = "{7}";
		break;
		case '8':
			$teto =  (_ERROR_OCHO).$exp[1];
			$cam = "{8}";
		break;
		case '9':
			$teto =  (_ERROR_NUEVE).$exp[1];
			$cam = "{9}";
		break;
	}

	$saleErrores = str_replace($cam,'',$saleErrores);
	error_log("saleErrores=".$saleErrores);

	//env�o de errores para p�gina Verificaci�n de errores en la administraci�n
	if ($pase) $correo->todo(9,'Verificaci�n de errores',$saleErrores);

	//Env�o de errores para trazas
	if (strlen($textoCorreo) > 1) {
		$textoCorreo .= $etiqueta;
		$correo->todo(9,'Entrada de datos',$textoCorreo);
	}

	error_log("teto=".$teto);
	$etiquetajs = cambiaTilJS($teto);
	error_log("etiquetajs=".$etiquetajs);
	$q = "select id_reserva from tbl_reserva where codigo = '".$d['transaccion']."' and id_comercio = '".$d['comercio']."' and pMomento = 'S'";
	error_log($q);
	$temp->query($q);

	$sal = '';
	if ($temp->num_rows()>0) {//si la operaci�n es al momento redirijo el error a la p�gina de pago
		error_log("Datos, com=".$d['comercio']."\ntrans=".$d['transaccion']."\netiq".$etiquetajs);
		$correo->todo(24, 'Operaci�n regresa a la p�gina de Pago', "Datos, com=" . $d['comercio'] . "<br>trans=" . $d['transaccion'] . "<br>etiq" . $etiquetajs ."<br>error: ". $inicio->err);
		// exit;
		$sal = "<form name='retorno' action='"._ESTA_URL."/admin/index.php?componente=comercio&pag=pago' method='post'>
		<input type='hidden' value='".$temp->f('id_reserva')."' name='identf'>
		<input type='hidden' value='". $etiqueta ."' name='etiqueta'>
		</form>
		<script>document.retorno.submit();</script>";
//		<input type='hidden' value='". $inicio->err ."' name='etiqueta'>
	} else { //muestro el error al Cliente
		$sal = '<!DOCTYPE HTML><html><META http-equiv="Content-Type" content="text/html" charset="utf-8"><head><script>document.getElementById("avisoIn").style.display="none";document.writeln("<div id=\"errDvd\" style=\"margin:20px 0 0 "+ ((window.innerWidth)-800)/2 +"px; width:800px; text-align:center;\">")</script>'.utf8_encode($teto).'</h3> por favor consulte a su comercio.<br /><br /><img src="images/pagina_error.png" width="247" height="204" alt="Error" title="Error" /><br /><br /></div><!-- '.$etiqueta.' -->';
		if ($inicio->comer == '527341458854') {
			$sal = '<!DOCTYPE HTML><html><META http-equiv="Content-Type" content="text/html" charset="utf-8"><head><script>document.getElementById("avisoIn").style.display="none";document.writeln("<div id=\"errDvd\" style=\"margin:20px 0 0 "+ ((window.innerWidth)-800)/2 +"px; width:800px; text-align:center;\">")</script>'.utf8_encode($teto).'</h3> por favor consulte a su comercio.<br /><br /><img src="images/pagina_error.png" width="247" height="204" alt="Error" title="Error" /><br /><br />'.$etiqueta.'</div>';
		}
	}
	echo $sal;
// 	echo '<script>document.getElementById("avisoIn").style.display="none";document.writeln("<div id=\"errDvd\" style=\"margin:20px 0 0 "+
// ((window.innerWidth)-800)/2
// +"px; width:800px; text-align:center;\">")</script>
// Se ha producido un <span style="color:red;font-weight:bold;">ERROR</span>
// en los datos enviados:<br /><h3>'.$etiqueta.'</h3>por favor consulte a su comercio.<br /><br />
// <img src="images/pagina_error.png" width="247" height="204" alt="Error" title="Error" /><br /><br /></div>
// <!-- '.$etiqueta.' -->';
	exit;
}

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
?>
