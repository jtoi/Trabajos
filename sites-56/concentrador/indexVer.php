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
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'include/correo.php' );
require_once( 'admin/adminis.func.php' );
//require_once( 'admin/classes/Domparser.class.php' );
//require_once "admin/classes/HTMLPP.php";                      /*include the file*/
//require_once "admin/classes/advance_html_dom.php";

//$dom=new AdvancedHtmlDom;
//$HTML=new HTMLPP; 
$temp = new ps_DB;
$correo = new correo;
$ent = new entrada;
$correoMi = "fecha=".date('d/m/Y H:i:s')."<br>";
$titulo = 'Entrada de datos';
header('Content-Type: text/html; charset=utf-8');

$d = $_POST;
//$d = $_REQUEST;

/****************************comentar**************************************************/
//$PPTcomercio	= "122327460662";
//$PPTtransaccion = substr(time(), -8);
//$PPTimporte		= "1235";
//$PPTmoneda		= "978";
//$PPToperacion	= "P";
//$PPTidioma		= "es";
//$PPTclave		= "FjswqLm6rNu3F27nGrcM";
//$PPTfirma		= md5($PPTcomercio.$PPTtransaccion.$PPTimporte.$PPTmoneda.$PPToperacion.$PPTclave);
//$d = array("comercio"=>$PPTcomercio, "pasarela"=>$PPTpasarela, "transaccion"=>$PPTtransaccion, "importe"=>$PPTimporte, "moneda"=>$PPTmoneda, "operacion"=>$PPToperacion, "idioma"=>$PPTidioma, "firma"=>$PPTfirma, "pasarela"=>"30");
/****************************comentar*************************************************/
foreach ($d as $value => $item) {
	$entrega .= $value . "=" . $item . "\n";
}

//print_r($d);

$dirIp = $_SERVER['REMOTE_ADDR'];
$correoMi .= "DIR IP - ".$dirIp . "<br>\n";
$correoMi .= $entrega . "<br>\n";
if (_MOS_CONFIG_DEBUG) echo $correoMi."<br><br>";
//echo "ve";
//$dirIp = '199.180.128.45';
//echo "va";
ipBloqueada($dirIp);

if ($d['comercio'] && $d['transaccion'] && $d['importe'] && $d['moneda'] && $d['operacion'] && $d['firma']) {
    $correo->set_subject($titulo);
	if (!($comercio = $ent->isAlfanumerico($d['comercio'], 15))) {
		muestraError ("falla por comercio", $correoMi);
	}
	if ($d['comercio'] == '527341458854') {
		muestraError ("falla por comercio Deshabilitado", $correoMi);
	}
	if (!($transaccion = $ent->isUrl($d['transaccion'], 12))) {
		muestraError ("falla por transaccion", $correoMi);
	}
	if (!($importe = $ent->isReal($d['importe'], 9)) || $d['importe'] == 0) {
		muestraError ("falla por importe", $correoMi);
	}
	if (!($moneda = $ent->isReal($d['moneda'], 3))) {
		muestraError ("falla por moneda", $correoMi);
	} else {
		$q = "select count(idmoneda) total from tbl_moneda where idmoneda = '" . $d['moneda'] . "'";
		$temp->query($q);
		if ($temp->f('total') != 1) {
			muestraError ("falla por moneda", $correoMi);
		}
	}
	$operacion = strtoupper($d['operacion']);
	if (!$operacion == 'P' || !$operacion == 'C') {
		muestraError ("falla por operacion", $correoMi);
	}
	if (!($firma = $ent->isAlfanumerico($d['firma'], 32))) {
		muestraError ("falla por firma", $correoMi);
	}
	if ($d['pasarela']) {
		if (!($pasar = $ent->isReal($d['pasarela'], 2))) {
			muestraError ("falla por tipo de pasarela", $correoMi);
		}
	}
	if ($d['idioma']) {
		$idioma = strtolower($d['idioma']);
		if (!$idioma == 'es' || !$idioma == 'en') {
			muestraError ("falla por idioma", $correoMi);
		}
	}
	switch ($idioma) {
		case '':
			$idioma = 'es';
			$pais = 'ES';
			break;
		case 'es':
			$pais = 'ES';
			break;
		default:
			$pais = 'GB';
	}

	if (_MOS_CONFIG_DEBUG)
		echo "$comercio, $transaccion, $importe, $moneda, $operacion<br>";
//	chequeea el md5 de los datos enviados
	$firmaCheck = convierte($comercio, $transaccion, $importe, $moneda, $operacion);
	$correoMi .= "firmaCheck=$firmaCheck<br>\n";
	$correoMi .= "firma=$firma<br>\n";

	if (_MOS_CONFIG_DEBUG)
		echo "$firmaCheck == $firma<br>";

	if ( $firmaCheck == $firma ) {
//		chequeo que la direccion IP no est� bloqueada
		$query = "select * from tbl_ipbloq where ip = '$dirIp' and bloqueada = 1";
		$correoMi .= "query=$query<br>\n";
		$temp->query($query);
		if ($temp->num_rows() >= 1) {
//			la IP est� bloqueda recoge alba�il que se acab� la mezcla
			$correoMi .= "IP $dirIp penalizada....<br>\n";
			muestraError ("Usted no puede realizar mas pagos. Contacte con su comercio.<br><br>
					  You can`t make more payments. Contact your commerce.", $correoMi);
		}
		
		//chequeo por monto de la transaccion
		$mensage = '';
		
		if ($importe >= (leeSetup('montoAlerta')*100)) {
			$q = "select nombre from tbl_comercio where idcomercio = '{$d['comercio']}'";
			$correoMi .= $q."\n";
			$temp->query($q);

			$correoMi .= "Mandando correo por transaccion de mas de 5000\n";

			$mensage .= "Se est&aacute; realizando una transacci&oacute;n por un monto de ".number_format(($importe/100),2,'.',' ');
			$mensage .= " correspondiente al comercio: ".$temp->f('nombre');
			$mensage .= "\n<br />Fecha - Hora: ".date('d')."/".date('m')."/".date('Y')." ".date('H').":".date('i').":".date('s');
			$mensage .= "\n<br /><br />";
			
		}
		
		//Chequeo por intento de pago desde la misma IP
		$tpoe = time() - leeSetup('tiemTrans');
		$q = "select count(idtransaccion) tot from tbl_transacciones where ip = '$dirIp' and estado = 'A' and fecha_mod > $tpoe";
		$temp->query($q);
		$correoMi .= "Cant = ".$temp->f('tot')."\n";
		$correoMi .= "query = ".$q."\n";
		if ($temp->f('tot') > 1) {
			$correoMi .= "Mandando correo por repetici&oacute;n de pago desde la misma IP\n";
			$mensage .= "Se est&aacute; intentando una transacci&oacute;n desde la misma IP ($dirIp) que anteriormente <br />";
			$mensage .= "se ha realizado un pago en menos de $tpo segundos.<br /><br />";
		}
		if (strlen($mensage) > 10) {
            $correo->set_subject('Alerta de vigilancia antifraude');
            $correo->set_message($mensage);
            $correo->envia(10);
        }

//		Creacion del identificador de la transaccion y recoleccion de datos del comercio
		$query = "select id, prefijo_trans, estado, pasarela, url from tbl_comercio where activo = 'S' and idcomercio = '$comercio'";
		$correoMi .= "query=$query<br>\n";
		$temp->query($query);
//			si la query anterior no retorna nada el comercio no es v�lido
		if ($temp->num_rows() == 0) {
			muestraError ("falla por comercio", $correoMi);
		}
		$prefijo = $temp->f('prefijo_trans');
		$estado = $temp->f('estado');
		$url = $temp->f('url');
		$idCom = $temp->f('id');
		$pasarela = $temp->f('pasarela'); // se usa la pasarela esta cuando no se env�a conjuntamente con la trasacci�n

		/*
		 * Chequeo de comprobaci�n si son reintentos de pago, primero reviso si es una ipblanca si lo es, me salto la comprobaci�n
		 */
		if (!ipblanca($dirIp)) {
			$query = "select * from tbl_transacciones where estado = 'D' and tipoEntorno = 'P' and idcomercio = '$comercio' and fecha_mod > " .
					(time() - (leeSetup('minReintento') * 60)) .
					" order by fecha_mod";
			$correoMi .= "query=$query<br>\n";
			$temp->query($query);
			$cant = $temp->num_rows();

			if ($cant > 0) {
				//Revisa que en 1 horas no hayan estado mandando transacciones denegadas desde esta IP
				$query = "select * from tbl_transacciones
								where estado = 'D'
									and idcomercio = '$comercio'
									and fecha_mod > " . (time() - (leeSetup('minReintento') * 60 * 60)) . "
									and ip = '$dirIp'
								order by fecha_mod";
				$correoMi .= "query=$query<br>\n";
				$temp->query($query);

				if ($temp->num_rows() >= leeSetup('cantReintentos')) {
					//Penaliza la IP desde donde se conectaron en los reintentos
					$query = "insert into tbl_ipbloq (idips, ip, fecha, identificador, idComercio, bloqueada, fecha_desbloq, desbloq_por, idCom) values
									(null, '" . $dirIp . "', " . time() . ", '$transaccion', '$comercio', 1, null, null, $idCom)";
					$correoMi .= "query=$query<br>\n";
					$temp->query($query);
					
					$correoMi .= "IP $dirIp penalizada....<br>\n";
					$q = "select email from tbl_admin where idcomercio = '$comercio' and idrol = 11 limit 0,1";
					$temp->query($q);

					$correos = "Estimado Administrador \n\nUn cliente ha tratado de realizar el pago de la transacci&oacute;n ".
								"$transaccion en repetidas ocasiones de forma infructuosa esto ha causado que la IP desde donde ha realizado los intentos ". 
								"($dirIp) se haya bloqueado.\n\nSi tiene posibilidad de contactarle, p&iacute;dale por favor que llame al banco emisor de la tarjeta ".
								"y aver&iacute;gue las causas de la negaci&oacute;n del pago.\n\nUna vez que este problema se solucione desbloquee esta IP (men&uacute;: Reportes ".
								"/ Ips Bloqueadas) para que el cliente pueda usarla nuevamente.\n\nAdminstrador de Sistemas\nTravels and Discovery";
                    
					$correo->to($temp->f('email'));
                    $correo->set_subject('IP bloqueada');
                    $correo->set_message($correos);
                    $correo->envia(11);
					muestraError ("Su Ip ha sido bloqueada, no puede realizar mas pagos. Contacte con su comercio.<br><br>Your IP has been banned, you can`t make more payments. Contact your commerce.", $correoMi);
					
				} else {

					//Se produce un intento de pago desde una IP que anteriormente fu� denegada
					echo '<script>document.writeln("<div style=\"margin:"+
						  window.innerHeight/2
						  +"px 0 0 "+
						  ((window.innerWidth)-400)/2
						  +"px; width:400px; text-align:center;\">"
						  )</script>
						  Usted est&aacute; reintentando un pago que fu&eacute; Denegado anteriormente. Elimine el problema y reintente m&aacute;s tarde.
						  Tiene una cantidad limitada de reintentos antes de que se bloquee<br><br>
						  You are trying again a payment that was Denied previously. Eliminate the problem and try again later.
						  You have a limited number of tries after that you will be banned';
				}

				$correoMi .= "Reintento de pago denegado previamente<br>\n";
                $correo->set_subject($titulo);
                $correo->set_message($correoMi);
                $correo->envia(9);
//					exit;
			}
		}

//			Chequeo si el comercio est� autorizado a usar la pasarela que viene en la transacci�n
//			comprobar si la transacci�n est� generada en el concentrador o viene desde el comercio,
//			chequeando si el identificador de la transacci�n que manda el comercio est� registrado en la tabla reserva
		$query = "select count(*) total from tbl_reserva where codigo = '$transaccion' and id_comercio = '$comercio'";
		$correoMi .= "query=$query<br>\n";
		$temp->query($query);
        
//			si se env�a la pasarela
		if ($pasar != '') {
			if ($temp->f('total') != 0) { //la transaccion ha sido realizada directamente en el concentrador
				$pasarela = $pasar;
			} else { // la transacci�n viene desde el comercio
//					La variable $pasarela puede traer una cadena delimitada por comas le pongo otra al final para compararla
				if (_MOS_CONFIG_DEBUG) echo $correoMi .= "pasarela=$pasarela<br>\n pasar=$pasar<br>\n";
				if (stristr($pasarela . ",", $pasar)) { // El comercio est� autorizado a usar la pasarela que est� enviando en la transacci�n
					$pasarela = $pasar;
				} else {
					muestraError ("falla por pasarela inv&aacute;lida", $correoMi);
				}
			}
		} elseif ($pasar == '' && strstr($pasarela, ",")) {
			muestraError ("falla por pasarela inv&aacute;lida, su comercio debe especificar pasarela", $correoMi);
		}
		
		$q = "select count(idPasarela) total from tbl_pasarela where activo = 1 and idPasarela = ".$d['pasarela'];
		$temp->query($q);
		if ($temp->f('total') == 0) muestraError ("falla, pasarela no existe o inv�lida", $correoMi);
        
		$hora = time();
		$importe100 = number_format(($importe / 100), 2, '.', '');
//			$importe100 = money_format('%.2n', $importe/100);
		if (_MOS_CONFIG_DEBUG)
			echo "number_format" . number_format(($importe / 100), 2) . "<br>";
		if (_MOS_CONFIG_DEBUG)
			echo "number_format" . number_format(($importe / 100), 2, '.', '') . "<br>";
//if (_MOS_CONFIG_DEBUG) echo "money_format".money_format('%.2n', $importe/100)."<br>";

		if (_MOS_CONFIG_DEBUG)
			echo "psasarela= " . $pasarela . "<br>";
		$correoMi .= "psasarela= " . $pasarela . "<br>\n";

        

		//Comprueba que la transaccion no se repita
		//esta firma es el md5 de la transacci�n por lo que es distinto para cada una
		$query = "select * from tbl_transacciones where identificador = '$transaccion' and idcomercio = '$comercio'";
		$temp->query($query);

//echo "num=".$temp->f('sesion');
		$correoMi .= "sesion= " . $temp->f('sesion') . "<br>\n";
		if ($temp->f('sesion') == $firma) {
			//La transaccion se repite, lee los valores de la transaccion que est�n en la BD
			$trans = $temp->f('idtransaccion');
			$hora = $temp->f('fecha');
			muestraError ("Transacci&oacute;n duplicada. P&iacute;dale a su comercio la genere nuevamente.<br>Duplicated transacction. Ask your commerce generates it again.", $correoMi);
		} else {
			//La transacci�n no se repite, se inserta en la BD
			$idpais = "null";
			if (function_exists(geoip_country_code3_by_name)) {
				if (strlen(geoip_country_code3_by_name($dirIp)) > 0) {
					$q = "select id from tbl_paises where iso = '".geoip_country_code3_by_name($dirIp)."'";
					$correoMi .= "Busca id del pais= " . $q . "<br>\n";
					$temp->query($q);
					$idpais = $temp->f('id');
					
					if(strlen($idpais) == 0){
						$q = "insert into tbl_paises (id, nombre, idPasarela, iso) values ".
						" (null, '".geoip_country_name_by_name($dirIp)."', '7', '".geoip_country_code3_by_name($dirIp)."')";
						$temp->query($q);
						
						$q = "select id from tbl_paises where iso = '".geoip_country_code3_by_name($dirIp)."'";
						$correoMi .= "Busca id del pais= " . $q . "<br>\n";
						$temp->query($q);
						$idpais = $temp->f('id');
					}
				}
			}
			if (strlen($idpais) == 0) $idpais = 'null';
            $error = 'error';
            while(strlen($error) > 0){
                $trans = trIdent($prefijo);
                $query = "insert into tbl_transacciones
                                (idtransaccion, idcomercio, identificador, tipoOperacion,
                                fecha, fecha_mod, valor_inicial, tipoEntorno, moneda, estado, sesion, idioma, pasarela, ip, idpais)
                             values
                                ('$trans', '$comercio', '$transaccion', '$operacion',
                                $hora, $hora, $importe, '$estado', $moneda, 'P', '$firma', '$idioma', $pasarela, '$dirIp', $idpais)";
                $correoMi .= "Inserta la transaccion= " . $query . "<br>\n";
                $temp->query($query);
                $error = $temp->getErrorMsg();
                $correoMi .= "Error insertando= " . $error . "<br>\n";
            }
			if ($d['tpv']) {
				$q = "update tbl_transacciones set tpv = 1 where idtransaccion = '$trans'";
				$correoMi .= "inserta al valor TPV= " . $q . "<br>\n";
				$temp->query($q);
			}
		}
//			 echo $query;
		$urlredir = _URL_DIR . "index.php?resp=$trans";
		$correoMi .= "urlredir= " . $urlredir . "<br>\n";

		echo '<script>document.writeln("<div id=\"avisoIn\" style=\"margin:20px 0 0 "+
				  ((window.innerWidth)-800)/2
				  +"px; width:800px; text-align:center;\">"
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
		if ($pasarela != 1)
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
					complaints to the card issuing bank.<br /><br />
					Some banks do not allow online purchases to its customers.
					Please, if you have problems with your card purchase try again with another card.<br><br>';
		if ($pasarela != 1)
			echo 'The purchase process will be completed through the Secure Electronic Commerce Gateway (CES),
					guaranteeing maximum safety in your buying process.
					To complete your purchase, 
					you will need your card is registered to operate in Secure Electronic Commerce,
					in case of doubt check with your financial institution.<br><br>';
		echo '<strong>Your transacction is been processed...</strong></div>';

		$pasoaBBVA = false;
		if ($estado == 'D') $pasarela = 1000; //cambiar la pasarela de PRUEBA
		$correoMi .= "pasarela=" . $pasarela . "<br>\n";
            
		if ($pasarela == 1) { //Pasarela del BBVA
			if ($estado == 'P') { //comercio en producci�n
				$clave = desofuscar(_PALABR_OFUS, _CONTRASENA_OFUS);
				$urlcomercio = _URL_COMERCIO;
				$localizador = _LOCALIZADOR;
				$url_tpvv = _URL_TPV; // URL del TPV.
				//$idCom = _ID_COMERCIO;
				$idPas = _ID_PTO;
				$pasoaBBVA = true;
			} else {//comercio en desarrollo
				$clave = desofuscar(_TESTPALABR_OFUS_TEST, _TESTCONTRASENA_OFUS_TEST);
				$urlcomercio = _URL_COMERCIO;
				$localizador = _LOCALIZADOR;
				$url_tpvv = _URL_TPV; // URL del TPV.
				$idCom = _TESTID_COMERCIO_TEST;
				$idPas = _TESTID_PTO_TEST;
				$pasoaBBVA = true;
			}
		} elseif ($pasarela == 3) {//pasarela del BBVA 3D
			$clave = desofuscar(_3DPALABR_OFUS, _3DCONTRASENA_OFUS);
			$urlcomercio = _URL_COMERCIO;
			$localizador = _LOCALIZADOR;
			$url_tpvv = _URL_TPV; // URL del TPV.
			//$idCom = _3DID_COMERCIO;
			$idPas = _3DID_PTO;
			$pasoaBBVA = true;
		} elseif ($pasarela == 11) {//pasarela del BBVA 3D onL
			$clave = desofuscar(_3DOPALABR_OFUS, _3DOCONTRASENA_OFUS, _3DOID_COMERCIO);
			$urlcomercio = _URL_COMERCIO;
			$localizador = _LOCALIZADOR;
			$url_tpvv = _URL_TPV; // URL del TPV.
			//$idCom = _3DOID_COMERCIO;
			$idPas = _3DOID_PTO;
			$pasoaBBVA = true;
		} elseif ($pasarela == 8) {//pasarela del BBVAAMEX
			$clave = desofuscar(_MEXPALABR_OFUS, _MEXCONTRASENA_OFUS);
			$urlcomercio = _URL_COMERCIO;
			$localizador = _LOCALIZADOR;
			$url_tpvv = _URL_TPV; // URL del TPV.
			//$idCom = _MEXID_COMERCIO;
			$idPas = _MEXID_PTO;
			$pasoaBBVA = true;
		} elseif ($pasarela == 14) {//pasarela del BBVA3
			$clave = desofuscar(_3BBVAPALABR_OFUS, _3BBVACONTRASENA_OFUS);
			$urlcomercio = _URL_COMERCIO;
			$localizador = _LOCALIZADOR;
			$url_tpvv = _URL_TPV; // URL del TPV.
			//$idCom = 'B9550206800006';
			$idPas = '999999';
			$pasoaBBVA = true;
		} elseif ($pasarela == 15) {//pasarela del BBVA4
			$clave = desofuscar(_4BBVAPALABR_OFUS, _4BBVACONTRASENA_OFUS);
			$urlcomercio = _URL_COMERCIO;
			$localizador = _LOCALIZADOR;
			$url_tpvv = _URL_TPV; // URL del TPV.
			//$idCom = _4BBVAID_COMERCIO;
			$idPas = _4BBVAID_PTO;
			$pasoaBBVA = true;
		} elseif ($pasarela == 16) {//pasarela del BBVA4 3D
			$clave = desofuscar(_5BBVAPALABR_OFUS, _5BBVACONTRASENA_OFUS);
			$urlcomercio = _URL_COMERCIO;
			$localizador = _LOCALIZADOR;
			$url_tpvv = _URL_TPV; // URL del TPV.
			//$idCom = _5BBVAID_COMERCIO;
			$idPas = _5BBVAID_PTO;
			$pasoaBBVA = true;
		} elseif ($pasarela == 17) {//pasarela del BBVA9 3D
			$clave = desofuscar(_9BBVAPALABR_OFUS, _9BBVACONTRASENA_OFUS);
			$urlcomercio = _URL_COMERCIO;
			$localizador = _LOCALIZADOR;
			$url_tpvv = _URL_TPV; // URL del TPV.
			//$idCom = _9BBVAID_COMERCIO;
			$idPas = _9BBVAID_PTO;
			$pasoaBBVA = true;
		} elseif ($pasarela == 18) {//pasarela del BBVA10 3D
			$clave = desofuscar(_10BBVAPALABR_OFUS, _10BBVACONTRASENA_OFUS);
			$urlcomercio = _URL_COMERCIO;
			$localizador = _LOCALIZADOR;
			$url_tpvv = _URL_TPV; // URL del TPV.
			//$idCom = _10BBVAID_COMERCIO;
			$idPas = _10BBVAID_PTO;
			$pasoaBBVA = true;
		} elseif ($pasarela == 2 || $pasarela == 31) {//Pasarela Sabadell y Sabadell2 3D
			$urlPasarela = 'https://sis.redsys.es/sis/realizarPago';
			if ($pasarela == 2) { // Sabadell
				$clave = _SABADEL_CLAVE_DESA;
				if ($moneda == '978') $terminal = '1';
				elseif ($moneda == '840') $terminal = '2';
				$idCom = '22551493';
			} else if ($pasarela == 31) { //Sabadell2 3D
				if		($moneda == '978')	{$terminal = '1'; $clave = 'xqcazqbcyzjh06406361';} //EUROS
				elseif	($moneda == '840')	{$terminal = '2'; $clave = 'ezgokljvyqto88197533';} //USD
				elseif	($moneda == '826')	{$terminal = '3'; $clave = 'fexirsuvdmet78471779';} //GBP
				elseif	($moneda == '392')	{$terminal = '4'; $clave = 'hswubyxxdwtg74976015';} //JPY
				elseif	($moneda == '32')	{$terminal = '5'; $clave = 'rsyffidtdxcj17331718';} //ARS
				elseif	($moneda == '124')	{$terminal = '6'; $clave = 'bentivpiqvxo75248810';} //CAD
				elseif	($moneda == '152')	{$terminal = '7'; $clave = 'siqlspsivoiq84418732';} //CLP
				elseif	($moneda == '170')	{$terminal = '8'; $clave = 'wvindwahvndc47707875';} //COP
				elseif	($moneda == '356')	{$terminal = '9'; $clave = 'svklykykgfmb68109250';} //INR
				elseif	($moneda == '484')	{$terminal = '10'; $clave = 'msyiuajeiikj38292540';} //MXN
				elseif	($moneda == '604')	{$terminal = '11'; $clave = 'eezzypvzugdr73194757';} //PEN
				elseif	($moneda == '756')	{$terminal = '12'; $clave = 'efkmqntiucvx10265287';} //CHF franco suizo
				elseif	($moneda == '986')	{$terminal = '13'; $clave = 'gdgphudnfcix26473574';} //BRL Real Brasile�o
				elseif	($moneda == '937')	{$terminal = '14'; $clave = 'lhqcacssmmeg39023616';} //VEF
				elseif	($moneda == '949')	{$terminal = '15'; $clave = 'ypcxuogmdbrd57365580';} //TRY
				$idCom = '327604799';
				
			}

			$query = "select nombre, servicio from tbl_reserva where codigo = '$transaccion' and id_comercio = '$comercio'";
			if (_MOS_CONFIG_DEBUG)
				echo "<br>" . $query;
			$correoMi .= "query= " . $query . "<br>\n";
			$temp->query($query);

			if (!$temp->num_rows() == 0) {
				$producto = $temp->f('servicio');
				$titular = $temp->f('nombre');
			} else {
				$producto = 'Servicio';
				$titular = 'Nombre';
			}

//			$query = "select c.nombre from tbl_transacciones t, tbl_comercio c where c.idcomercio = t.idcomercio and idtransaccion = '$trans'";
//			$correoMi .= "query= " . $query . "<br>\n";
//			$temp->query($query);
//			$comName = $temp->f('nombre');
			$comName = 'Caribeantravelweb';

			$moneda1 = '0' . $moneda;

			$tipoTrans = '0';
			$est = 'hidden';
			$urldirOK = $urlredir . '&est=ok';
			$urldirKO = $urlredir . '&est=ko';
			$urlcomercio = _URL_COMERCIO;
			if ($idioma == 'en')
				$idi = '002'; elseif ($idioma == 'es')
				$idi = '001';
			if (_MOS_CONFIG_DEBUG)
				$est = "text";
			if (_MOS_CONFIG_DEBUG)
				echo "idioma=$idioma<br>";
			if (_MOS_CONFIG_DEBUG)
				echo "$importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave<br>";
			$message = $importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave;
//                $message = $importe . $trans . $idCom . $moneda . $clave;
			if (_MOS_CONFIG_DEBUG)
				echo "tira=$message<br>";
			$Digest = strtoupper(sha1($message));

			$cadenSal = "<form name=\"envia\" action=\"$urlPasarela\" method=\"post\">
								<input type=\"$est\" name=\"Ds_Merchant_Amount\" value=\"$importe\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Currency\" value=\"$moneda\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Order\" value=\"$trans\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ProductDescription\" value=\"$producto\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Titular\" value=\"$titular\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantCode\" value=\"$idCom\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantURL\" value=\"$urlcomercio\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlOK\" value=\"$urldirOK\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlKO\" value=\"$urldirKO\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantName\" value=\"$comName\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ConsumerLanguage\" value=\"$idi\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantSignature\" value=\"$Digest\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Terminal\" value=\"$terminal\"/>
								<input type=\"$est\" name=\"Ds_Merchant_TransactionType\" value=\"$tipoTrans\"/>";
			if (_MOS_CONFIG_DEBUG) {
				$cadenSal .= '<input type="submit" />
								 </form>
								 ';
			} else {
				$cadenSal .= '</form>
								 <script language=\'javascript\'>
									 document.envia.submit();
								 </script>';
			}
			$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";
			echo $cadenSal;
		} elseif ($pasarela == 21 || $pasarela == 31) {//Pasarela Caixa con 3D, Caixa2 3D
			
			$urlPasarela = "https://sis.sermepa.es/sis/realizarPago";
			if ($pasarela == 21) { // Caixa 3D
				$clave = 'ddk03gfhj9rf394nfd02';
				if ($moneda == '978') $terminal = '2';
				else {
					muestraError ("falla por moneda en Caixa 3D", $correoMi);
				}
				$idCom = '33473539';
				
			} else if ($pasarela == 31) { //Caixa2 3D
				$clave = '587d33aff15bae14d120';
				if ($moneda == '978') $terminal = '5';
				else {
					muestraError ("falla por moneda en Caixa2 3D", $correoMi);
				}
				$idCom = '285772844';
				
			}

			$query = "select nombre, servicio from tbl_reserva where codigo = '$transaccion' and id_comercio = '$comercio'";
			if (_MOS_CONFIG_DEBUG)
				echo "<br>" . $query;
			$correoMi .= "query= " . $query . "<br>\n";
			$temp->query($query);

			if (!$temp->num_rows() == 0) {
				$producto = $temp->f('servicio');
				$titular = $temp->f('nombre');
			} else {
				$producto = 'Servicio';
				$titular = 'Nombre';
			}

			$query = "select c.nombre from tbl_transacciones t, tbl_comercio c where c.idcomercio = t.idcomercio and idtransaccion = '$trans'";
			$correoMi .= "query= " . $query . "<br>\n";
			$temp->query($query);
			$comName = $temp->f('nombre');
			$comName = 'Travels and Discovery';

			$moneda1 = '0' . $moneda;

			$tipoTrans = '0';
			$est = 'hidden';
			$urldirOK = $urlredir . '&est=ok';
			$urldirKO = $urlredir . '&est=ko';
			$urlcomercio = _URL_COMERCIO;
			if ($idioma == 'en')
				$idi = '002'; elseif ($idioma == 'es')
				$idi = '001';
			if (_MOS_CONFIG_DEBUG)
				$est = "text";
			if (_MOS_CONFIG_DEBUG)
				echo "idioma=$idioma<br>";
			if (_MOS_CONFIG_DEBUG)
				echo "$importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave<br>";
			$message = $importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave;
//                $message = $importe . $trans . $idCom . $moneda . $clave;
			if (_MOS_CONFIG_DEBUG)
				echo "tira=$message<br>";
			$Digest = strtoupper(sha1($message));

			$cadenSal = "<form name=\"envia\" action=\"$urlPasarela\" method=\"post\">
								<input type=\"$est\" name=\"Ds_Merchant_MerchantName\" value=\"$comName\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantCode\" value=\"$idCom\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Terminal\" value=\"$terminal\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Order\" value=\"$trans\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Amount\" value=\"$importe\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Currency\" value=\"$moneda\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ProductDescription\" value=\"$producto\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Titular\" value=\"$titular\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantURL\" value=\"$urlcomercio\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlOK\" value=\"$urldirOK\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlKO\" value=\"$urldirKO\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ConsumerLanguage\" value=\"$idi\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantSignature\" value=\"$Digest\"/>
								<input type=\"$est\" name=\"Ds_Merchant_TransactionType\" value=\"$tipoTrans\"/>";
			if (_MOS_CONFIG_DEBUG) {
				$cadenSal .= '<input type="submit" />
								 </form>
								 ';
			} else {
				$cadenSal .= '</form>
								 <script language=\'javascript\'>
									 document.envia.submit();
								 </script>';
			}
			$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";
			echo $cadenSal;
		} elseif ($pasarela == 20) {//Pasarela Caixa sin 3D
			
			$urlPasarela = "https://sis.sermepa.es/sis/realizarPago";
			
			$clave = 'ddk03gfhj9rf394nfd02';
			if ($moneda == '978') $terminal = '1'; // euros
			else if ($moneda == '840') $terminal = '4'; // d�lares usa
			else if ($moneda == '826') $terminal = '5'; //libras
			else if ($moneda == '392') $terminal = '6'; //yenes
			else if ($moneda == '32') $terminal = '7'; //austral argentino
			else if ($moneda == '124') $terminal = '8'; //d�lar canadiense
			else if ($moneda == '152') $terminal = '9'; //peso chileno
			else if ($moneda == '170') $terminal = '10'; //peso colombiano
			else if ($moneda == '356') $terminal = '11'; //rupia india
			else if ($moneda == '484') $terminal = '12'; //peso mexicano
			else if ($moneda == '604') $terminal = '13'; //nuevos soles peruanos
			else if ($moneda == '756') $terminal = '14'; //franco suizo
			else if ($moneda == '986') $terminal = '15'; //real brasile�o
			else if ($moneda == '837') $terminal = '16'; //bol�var fuerte
			else if ($moneda == '949') $terminal = '17'; //lira turca
			else {
					muestraError ("falla por moneda en Caixa", $correoMi);
			}
			$idCom = '33473539';
			

			$query = "select nombre, servicio from tbl_reserva where codigo = '$transaccion' and id_comercio = '$comercio'";
			if (_MOS_CONFIG_DEBUG)
				echo "<br>" . $query;
			$correoMi .= "query= " . $query . "<br>\n";
			$temp->query($query);

			if (!$temp->num_rows() == 0) {
				$producto = $temp->f('servicio');
				$titular = $temp->f('nombre');
			} else {
				$producto = 'Servicio';
				$titular = 'Nombre';
			}

			$query = "select c.nombre from tbl_transacciones t, tbl_comercio c where c.idcomercio = t.idcomercio and idtransaccion = '$trans'";
			$correoMi .= "query= " . $query . "<br>\n";
			$temp->query($query);
			$comName = $temp->f('nombre');
			$comName = 'Travels and Discovery';

			$moneda1 = '0' . $moneda;

			$tipoTrans = '0';
			$est = 'hidden';
			$urldirOK = $urlredir . '&est=ok';
			$urldirKO = $urlredir . '&est=ko';
			$urlcomercio = _URL_COMERCIO;
			if ($idioma == 'en')
				$idi = '002'; elseif ($idioma == 'es')
				$idi = '001';
			if (_MOS_CONFIG_DEBUG)
				$est = "text";
			if (_MOS_CONFIG_DEBUG)
				echo "idioma=$idioma<br>";
			if (_MOS_CONFIG_DEBUG)
				echo "$importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave<br>";
			$message = $importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave;
//                $message = $importe . $trans . $comercio . $moneda . $clave;
			if (_MOS_CONFIG_DEBUG)
				echo "tira=$message<br>";
			$Digest = strtoupper(sha1($message));

			$cadenSal = "<form name=\"envia\" action=\"$urlPasarela\" method=\"post\">
								<input type=\"$est\" name=\"Ds_Merchant_MerchantName\" value=\"$comName\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantCode\" value=\"$idCom\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Terminal\" value=\"$terminal\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Order\" value=\"$trans\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Amount\" value=\"$importe\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Currency\" value=\"$moneda\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ProductDescription\" value=\"$producto\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Titular\" value=\"$titular\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantURL\" value=\"$urlcomercio\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlOK\" value=\"$urldirOK\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlKO\" value=\"$urldirKO\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ConsumerLanguage\" value=\"$idi\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantSignature\" value=\"$Digest\"/>
								<input type=\"$est\" name=\"Ds_Merchant_TransactionType\" value=\"$tipoTrans\"/>";
			if (_MOS_CONFIG_DEBUG) {
				$cadenSal .= '<input type="submit" />
								 </form>
								 ';
			} else {
				$cadenSal .= '</form>
								 <script language=\'javascript\'>
									 document.envia.submit();
								 </script>';
			}
			$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";
			echo $cadenSal;
		} elseif ($pasarela == 10) {//Pasarela Caja Madrid Bankia1
			$urlPasarela = 'https://sis.sermepa.es/sis/realizarPago';

			//Una clave para cada terminal!!!
			if ($moneda == '978') {
				$terminal = '4';
				$clave = '0B81Q46902U73925';
			} elseif ($moneda == '840') {
				$terminal = '6';
				$clave = 'M0P7062T65014683';
			} elseif ($moneda == '826') {
				$terminal = '5';
				$clave = '0A4C407VP7792U93';
			}

			$query = "select nombre, servicio from tbl_reserva where codigo = '$transaccion' and id_comercio = '$comercio'";
			if (_MOS_CONFIG_DEBUG)
				echo "<br>" . $query;
			$correoMi .= "query= " . $query . "<br>\n";
			$temp->query($query);

			if (!$temp->num_rows() == 0) {
				$producto = $temp->f('servicio');
				$titular = $temp->f('nombre');
			} else {
				$producto = 'Servicio';
				$titular = 'Nombre';
			}

			$comName = 'Caribbean Online';
			$moneda1 = '0' . $moneda;
			$idCom = '22551493';

			$tipoTrans = '0';
			$est = 'hidden';
			$urldirOK = $urlredir . '&est=ok';
			$urldirKO = $urlredir . '&est=ko';
			$urlcomercio = _URL_COMERCIO;
			if ($idioma == 'en')
				$idi = '002'; elseif ($idioma == 'es')
				$idi = '001';
			if (_MOS_CONFIG_DEBUG)
				$est = "text";
			if (_MOS_CONFIG_DEBUG)
				echo "idioma=$idioma<br>";
			if (_MOS_CONFIG_DEBUG)
				echo "$importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave<br>";
			$message = $importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave;
//                $message = $importe . $trans . $idCom . $moneda . $clave;
			if (_MOS_CONFIG_DEBUG)
				echo "tira=$message<br>";
			$Digest = strtoupper(sha1($message));

			$cadenSal = "<form name=\"envia\" action=\"$urlPasarela\" method=\"post\">
								<input type=\"$est\" name=\"Ds_Merchant_Amount\" value=\"$importe\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Currency\" value=\"$moneda\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Order\" value=\"$trans\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ProductDescription\" value=\"$producto\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Titular\" value=\"$titular\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantCode\" value=\"$idCom\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantURL\" value=\"$urlcomercio\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlOK\" value=\"$urldirOK\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlKO\" value=\"$urldirKO\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantName\" value=\"$comName\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ConsumerLanguage\" value=\"$idi\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantSignature\" value=\"$Digest\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Terminal\" value=\"$terminal\"/>
								<input type=\"$est\" name=\"Ds_Merchant_TransactionType\" value=\"$tipoTrans\"/>";

			if (_MOS_CONFIG_DEBUG) {
				$cadenSal .= '<input type="submit" />
								 </form>
								 ';
			} else {
				$cadenSal .= '</form>
								 <script language=\'javascript\'>
									 document.envia.submit();
								 </script>';
			}
			$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";
			echo $cadenSal;
		} elseif ($pasarela == 19 || $pasarela == 22 || $pasarela == 23 || $pasarela == 25 || $pasarela == 26 || $pasarela == 27 || $pasarela == 28 || $pasarela == 29 || $pasarela == 30) {
            //Pasarela Caja Madrid || Bankia2 || BBVA11 3D || Bankia 3 || BBVA12 3D || BBVA13 3D || BBVA14 3D || BBVA15 3D
			$urlPasarela = 'https://sis.redsys.es/sis/realizarPago';
            $comName = 'CARIBEANTRAVELWEB';

			if ($pasarela == 30) {	//Caixa Bank
				$urlPasarela = "https://sis-t.redsys.es:25443/sis/realizarPago";
				if ($moneda == '978') {$terminal = '8'; $clave = 'qwertyasdf0123456789';} 
                else {
					muestraError ("falla por moneda en Caixa Bank", $correoMi);
                }
				$idCom = '22551493';
			} else if ($pasarela == 19) { //Caja Madrid
				//Una clave para cada terminal!!!
				if ($moneda == '978') {$terminal = '1'; $clave = '310SLP3953MUR632';} 
				elseif ($moneda == '840') {$terminal = '2'; $clave = '6S17113U6N73VU11';} 
				elseif ($moneda == '826') {$terminal = '3'; $clave = 'P9338P4900M9O552';} 
                else {
					muestraError ("falla por moneda en Caja Madrid", $correoMi);
                }
			$comName = 'Travels and Discovery';
			$idCom = '285772844';
			} elseif ($pasarela == 22) { //BBVA11 3D
//				$urlPasarela = 'https://sis-t.redsys.es:25443/sis/realizarPago';
				//Una clave para cada terminal!!!
				if ($moneda == '978') {$terminal = '1'; $clave = 'qwertyasdf0123456789';}       //EUR OK
				elseif ($moneda == '840') {$terminal = '3'; $clave = 'lkjhyuiopm0123456789';}   //USD OK
				elseif ($moneda == '826') {$terminal = '4'; $clave = 'njiuhbvgyt0123456789';}   //GBP OK
				elseif ($moneda == '392') {$terminal = '5'; $clave = 'poiuyasdfg0123456789';}   //JPY OK
				elseif ($moneda == '124') {$terminal = '6'; $clave = 'sdcvfderfg1245785235';}   //CAD OK
				elseif ($moneda == '152') {$terminal = '7'; $clave = 'fgbvcdfvgr2145785235';}   //CLP OK
				elseif ($moneda == '32') {$terminal = '9'; $clave = 'dfcvdefdcv2145852369';}	//ARS OK
				elseif ($moneda == '356') {$terminal = '10'; $clave = 'dfcvfgvrdf2145852358';}  //INR OK
				elseif ($moneda == '484') {$terminal = '11'; $clave = 'dfvcdfgbvf2145856358';}  //MXN OK
				elseif ($moneda == '604') {$terminal = '12'; $clave = 'dfvcderfgt2145856358';}  //PEN OK
				elseif ($moneda == '937') {$terminal = '13'; $clave = 'dfcvcxsder2145896358';}  //VEF OK
				elseif ($moneda == '949') {$terminal = '14'; $clave = 'dfvbgrdfer2145896523';}  //TRY OK
				elseif ($moneda == '170') {$terminal = '15'; $clave = 'dfcvdfersx2145235896';}  //COP OK
                else {
					muestraError ("falla por moneda en BBVA11 3D", $correoMi);
                }
                $idCom = '332031053';
			} elseif ($pasarela == 26) { //BBVA13 3D
//				$urlPasarela = 'https://sis-t.redsys.es:25443/sis/realizarPago';
				//Una clave para cada terminal!!!
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
                else {
					muestraError ("falla por moneda en BBVA13 3D", $correoMi);
                }
                $comName = 'TRAVELS AND DISCOVERY';
                $idCom = '323825620';
			} elseif ($pasarela == 27) { //BBVA14 3D
//				$urlPasarela = 'https://sis-t.redsys.es:25443/sis/realizarPago';
				//Una clave para cada terminal!!!
				if ($moneda == '978') {$terminal = '6'; $clave = 'dfgvbgfrtg2145785235';}       //OK EUR
				elseif ($moneda == '840') {$terminal = '7'; $clave = 'slokmnbzxs2145785358';}   //OK USD
//				elseif ($moneda == '826') {$terminal = '8'; $clave = 'dcvfdszwtg2147854258';}   //OK GBP Modificaci�n por Cubana
//				elseif ($moneda == '392') {$terminal = '9'; $clave = 'uytrvcxwed2457896325';}   //OK JPY
//				elseif ($moneda == '124') {$terminal = '10'; $clave = 'AZSXDCFVGB1010101010';}  //OK CAD
//				elseif ($moneda == '32') {$terminal = '11'; $clave = 'AZSXDCFVGB1111111111';}   //OK ARS
//				elseif ($moneda == '152') {$terminal = '12'; $clave = 'AZSXDCFVGB1212121212';}  //OK CLP
//				elseif ($moneda == '170') {$terminal = '13'; $clave = 'AZSXDCFVGB1313131313';}  //OK COP
//				elseif ($moneda == '356') {$terminal = '14'; $clave = 'AZSXDCFVGB1414141414';}  //OK INR
//				elseif ($moneda == '484') {$terminal = '15'; $clave = 'AZSXDCFVGB1515151515';}  //OK MXN
//				elseif ($moneda == '604') {$terminal = '16'; $clave = 'AZSXDCFVGB1616161616';}  //OK PEN
//				elseif ($moneda == '937') {$terminal = '17'; $clave = 'AZSXDCFVGB1717171717';}  //OK VEF
//				elseif ($moneda == '949') {$terminal = '18'; $clave = 'AZSXDCFVGB1818181818';}  //OK TRY
                else {
					muestraError ("falla por moneda en BBVA14 3D", $correoMi);
                }
                $comName = 'TRAVELS AND DISCOVERY';
                $idCom = '323825398';
			} elseif ($pasarela == 28) { //BBVA15 3D
//				$urlPasarela = 'https://sis.redsys.es/sis/realizarPago';
				//Una clave para cada terminal!!!
				if ($moneda == '978') {$terminal = '4'; $clave = 'dscvfderdf2145785635';}       //OK EUR
				elseif ($moneda == '840') {$terminal = '5'; $clave = 'vbghynsxcd2145368523';}   //OK USD 
				elseif ($moneda == '826') {$terminal = '6'; $clave = 'AZSXDCFVGB6666666666';}   //OK GBP
				elseif ($moneda == '392') {$terminal = '7'; $clave = 'AZSXDCFVGB7777777777';}   //OK JPY
				elseif ($moneda == '32') {$terminal = '8'; $clave = 'AZSXDCFVGB8888888888';}    //OK ARS
				elseif ($moneda == '124') {$terminal = '9'; $clave = 'AZSXDCFVGB9999999999';}   //OK CAD
				elseif ($moneda == '152') {$terminal = '10'; $clave = 'AZSXDCFVGB1010101010';}  //OK CLP
				elseif ($moneda == '170') {$terminal = '11'; $clave = 'AZSXDCFVGB1111111111';}  //OK COP
				elseif ($moneda == '356') {$terminal = '12'; $clave = 'AZSXDCFVGB1212121212';}  //OK INR
                elseif ($moneda == '484') {$terminal = '13'; $clave = 'azsxdcfvgb1313131313';}  //OK MXN
                elseif ($moneda == '604') {$terminal = '14'; $clave = 'azsxdcfvgb1414141414';}  //OK PEN
				elseif ($moneda == '937') {$terminal = '15'; $clave = 'azsxdcfvgb1515151515';}  //OK VEF
				elseif ($moneda == '949') {$terminal = '17'; $clave = 'AZSXDCFVGB1717171717';}  //OK TRY
                else {
					muestraError ("falla por moneda en BBVA15 3D", $correoMi);
                }
                $comName = 'CARIBBEAN ON LINE AMEX';
                $idCom = '323013854';
			} elseif ($pasarela == 23) { //Bankia 3
				//$urlPasarela = 'https://sis-t.redsys.es:25443/sis/realizarPago';
                if ($moneda == '978') {$terminal = '004'; $clave = '2P0D63VVM7R55264';} 
                else {
					muestraError ("falla por moneda en Bankia3 3D", $correoMi);
                }
                $comName = 'CARIBEANTRAVELWEB';
                $idCom = '285772844';
            } elseif ($pasarela == 25) { //BBVA12 3D
//				$urlPasarela = 'https://sis-t.redsys.es:25443/sis/realizarPago';
				if ($moneda == '978') {$terminal = '1'; $clave = '580N12S4468871P3';}
                else {
					muestraError ("falla por moneda en BBVA12 3D", $correoMi);
                }
                $comName = 'CARIBEANTRAVELWEB';
                $idCom = '332356393';
			} elseif ($pasarela == 29) { //Sabadel Plus
//				$urlPasarela = 'https://sis-t.redsys.es:25443/sis/realizarPago';
				if ($moneda == '978') {$terminal = '7'; $clave = 'qzhbmcxxlniu53048259';}
                else {
					muestraError ("falla por moneda en Sabadel Plus", $correoMi);
                }
                $comName = 'CARIBEANTRAVELWEB';
                $idCom = '022551493';
			}

			$query = "select nombre, servicio from tbl_reserva where codigo = '$transaccion' and id_comercio = '$comercio'";
			if (_MOS_CONFIG_DEBUG)
				echo "<br>" . $query;
			$correoMi .= "query= " . $query . "<br>\n";
			$temp->query($query);

			if (!$temp->num_rows() == 0) {
				$producto = $temp->f('servicio');
				$titular = $temp->f('nombre');
			} else {
				$producto = 'Servicio';
				$titular = 'Nombre';
			}
            
            $correoMi .= "producto=$producto<br>\ntitular=$titular<br>\n";

			$moneda1 = '0' . $moneda;
			$tipoTrans = '0';
			$est = 'hidden';
			$urldirOK = $urlredir . '&est=ok';
			$urldirKO = $urlredir . '&est=ko';
			$urlcomercio = _URL_COMERCIO;
            
			if ($idioma == 'en') $idi = '002'; 
            elseif ($idioma == 'es') $idi = '001';
            elseif ($idioma == 'fr') $idi = '004';
            elseif ($idioma == 'it') $idi = '007';
            elseif ($idioma == 'de') $idi = '005';
            elseif ($idioma == 'sv') $idi = '008';
            elseif ($idioma == 'nl') $idi = '006';
            elseif ($idioma == 'pt') $idi = '009';
			
//			if (_MOS_CONFIG_DEBUG) $idi = '002';
            
			if (_MOS_CONFIG_DEBUG)
				$est = "text";
			if (_MOS_CONFIG_DEBUG)
				echo "idioma=$idioma<br>";
			if (_MOS_CONFIG_DEBUG)
				echo "$importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave<br>";
//                $message = $importe . $trans . $idCom . $moneda . $clave;
            $correoMi .= "$importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave<br>\n";
                $message = $importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave;
			if (_MOS_CONFIG_DEBUG)
				echo "tira=$message<br>";
            if ($pasarela == 23 ) {
                $Digest = sha1($message);
            } else {
                $Digest = strtoupper(sha1($message));
            }
            $correoMi .= "Digest=$Digest<br>\n";
			$cadenSal = "<form name=\"envia\" action=\"$urlPasarela\" method=\"post\">
								<input type=\"$est\" name=\"Ds_Merchant_Amount\" value=\"$importe\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Currency\" value=\"$moneda\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Order\" value=\"$trans\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ProductDescription\" value=\"$producto\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Titular\" value=\"$titular\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantCode\" value=\"$idCom\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantURL\" value=\"$urlcomercio\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlOK\" value=\"$urldirOK\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlKO\" value=\"$urldirKO\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantName\" value=\"$comName\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ConsumerLanguage\" value=\"$idi\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantSignature\" value=\"$Digest\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Terminal\" value=\"$terminal\"/>
								<input type=\"$est\" name=\"Ds_Merchant_TransactionType\" value=\"$tipoTrans\"/>";

			if (_MOS_CONFIG_DEBUG) {
				$cadenSal .= '<input type="submit" />
								 </form>
								 ';
			} else {
				$cadenSal .= '</form>
								 <script language=\'javascript\'>
									 document.envia.submit();
								 </script>';
			}
			$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";
			echo $cadenSal;
		} elseif ($pasarela == 4 || $pasarela == 13) {//pasarela de Banesto y Santander
			$correoMi .= "entra en Santander\n";
			if ($pasarela == 4) {
				if ($estado == 'P') { //comercio en producci�n
					$url_tpvv = _BANESTO_URL_PROD;
				} else { //comercio en desarrollo
					$url_tpvv = _BANESTO_URL_DESA;
				}
				$clave = _BANESTO_CLAV_COMER;
			$cadenSal = '
						 <form name="envia" action="' . $url_tpvv . '" method="post">
						 <input type="hidden" name="referencia" value="' . $trans . '"/>
						 <input type="hidden" name="nombre_comercio" value="' . $clave . '"/>
						 <input type="hidden" name="resto" value="' . $idioma . '"/>
						 <input type="hidden" name="coste" value="M' .$moneda. $importe . '"/>
						 <input type="hidden" name="moneda" value="EUR"/>
						 </form>
						 <script language=\'javascript\'>
							 document.envia.submit();
						 </script>';
			} elseif ($pasarela == 13) {
				$url_tpvv = _BANESTO_URL_PROD;
				$clave = 'PI00024205';
				$producto = "Servicios asociados al turismo";
				$referencia = "M$moneda" . "$importe\r\n1\r\n$trans\r\n$producto\r\n1\r\n$importe\r\n";
				$cadenSal = '
						 <form name="envia" action="' . $url_tpvv . '" method="post">
						 <input type="hidden" name="referencia" value="' . $trans . '"/>
						 <input type="hidden" name="comercio" value="' . $clave . '"/>
						 <input type="hidden" name="resto" value="' . $idioma . '"/>
						 </form>
						 <script language=\'javascript\'>
							 document.envia.submit();
						 </script>';
			}
			if (_MOS_CONFIG_DEBUG)
				echo "transaccion=$trans<br>";
			if (_MOS_CONFIG_DEBUG)
				echo "comercio=" . $clave . "<br>";
			if (_MOS_CONFIG_DEBUG)
				echo "idioma=$idioma<br>";
			
			echo $cadenSal;
			$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";
		} else if ($pasarela == 12) {
			//Datos pasarela en desarrollo
//				$url = "http://tpv.ceca.es:8000/cgi-bin/tpv";
			//Datos pasarela en producci�n
			$url = "https://pgw.ceca.es/cgi-bin/tpv";
//				$clave_encriptacion = "T21RAFBM";

			$clave_encriptacion = _CLAVE_EVO;
			$MerchatId = "054252135";
			$AcquirerBin = "0000554026";
			$TerminalId = "00000003";
			$Exponente = "2";
			$Encriptacion = "SHA1";
			$urldirOK = $urlredir . '&est=ok';
			$urldirKO = $urlredir . '&est=ko';
			if ($idioma == 'es')
				$lang = '1';
			else if ($idioma == 'en')
				$lang = '6';

			if (_MOS_CONFIG_DEBUG)
				echo "<br>" . $clave_encriptacion . $MerchatId . $AcquirerBin . $TerminalId . $trans . $importe . $moneda . $Exponente . $Encriptacion . $urldirOK . $urldirKO . "<br>";
			$firma = sha1($clave_encriptacion . $MerchatId . $AcquirerBin . $TerminalId . $trans . $importe . $moneda . $Exponente . $Encriptacion . $urldirOK . $urldirKO);
			if (_MOS_CONFIG_DEBUG)
				echo $firma . "<br>";
			if (_MOS_CONFIG_DEBUG)
				echo "calcul-" . sha1("998888881119500280000554052000000031235009782SHA1http://www.ceca.eshttp://www.ceca.es") . "<br>";

			$cadenSal = '
						 <form name="envia" action="' . $url . '" method="post">
						 <input type="hidden"name="MerchantID" value="' . $MerchatId . '"/>
						 <input type="hidden"name="AcquirerBIN" value="' . $AcquirerBin . '"/>
						 <input type="hidden"name="TerminalID" value="' . $TerminalId . '"/>
						 <input type="hidden"name="Num_operacion" value="' . $trans . '"/>
						 <input type="hidden"name="Importe" value="' . $importe . '"/>
						 <input type="hidden"name="TipoMoneda" value="' . $moneda . '"/>
						 <input type="hidden"name="Exponente" value="' . $Exponente . '"/>
						 <input type="hidden"name="Cifrado" value="' . $Encriptacion . '"/>
						 <input type="hidden"name="URL_OK" value="' . $urldirOK . '"/>
						 <input type="hidden"name="URL_NOK" value="' . $urldirKO . '"/>
						 <input type="hidden"name="Firma" value="' . $firma . '"/>
						 <input type="hidden"name="Pago_soportado" value="SSL"/>
						 <input type="hidden"name="Idioma" value="' . $lang . '"/>
						 </form>
						 <script language=\'javascript\'>
							 document.envia.submit();
						 </script>';
			echo $cadenSal;
			$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";
		} elseif ($pasarela == 24) {//PAYTPV
            $url = 'https://www.paytpv.com/gateway/fsgateway.php';
            if ($moneda != '978' && $moneda != '826' && $moneda != '840' && $moneda != '392') {
					muestraError ("falla por moneda en PayTpv", $correoMi);
            }
            $q = "select moneda from tbl_moneda where idmoneda = $moneda";
            $temp->query($q);
            $mon = $temp->f('moneda');
            $account = 'g4fhcw8t';
            $usercode = 'gDp0rFNXPs3fYydQT6zn';
            $terminal = '2707';
            $operation = 1;
			$urldirOK = $urlredir . '&est=ok';
			$urldirKO = $urlredir . '&est=ko';
            $firma = md5($account . $usercode . $terminal . $operation . $trans . $importe . $mon . md5('pCF2s3TVtmhHSgX6MyvN'));
			$cadIfr = '<iframe title="titulo" src="https://www.paytpv.com/gateway/ifgateway.php?ACCOUNT='.$account.'&USERCODE='.$usercode.'&TERMINAL='.$terminal.'&OPERATION='.$operation.
				'&REFERENCE='.$trans.'&AMOUNT='.$importe.'&CURRENCY='.$mon.'&LANGUAGE='.$lang.'&URLOK='.$urldirOK.'&URLKO='.$urldirKO.'&SIGNATURE='.$firma.'" width="410" height="500" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" style="border: 1px solid #000000; padding: 0px; margin: 0px"></iframe>';
			
?>
<script src="js/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
	$("#avisoIn").html('<?php echo $cadIfr; ?>');
</script>

<?php
            $cadenSal = '
						 <form name="envia" action="' . $url . '" method="post">
						 <input type="hidden"name="ACCOUNT" value="'. $account .'"/>
						 <input type="hidden"name="USERCODE" value="'.$usercode.'"/>
						 <input type="hidden"name="TERMINAL" value="'.$terminal.'"/>
						 <input type="hidden"name="OPERATION" value="'.$operation.'"/>
						 <input type="hidden"name="REFERENCE" value="' . $trans . '"/>
						 <input type="hidden"name="AMOUNT" value="' . $importe . '"/>
						 <input type="hidden"name="CURRENCY" value="' . $mon . '"/>
						 <input type="hidden"name="LANGUAGE" value="' . $lang . '"/>
						 <input type="hidden"name="URLOK" value="' . $urldirOK . '"/>
						 <input type="hidden"name="URLKO" value="' . $urldirKO . '"/>
						 <input type="hidden"name="SIGNATURE" value="' . $firma . '"/>
						 </form>
						 <script language=\'javascript\'>
							 document.envia.submit();
						 </script>';
//			echo $cadenSal;
//			$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";
        } elseif ($pasarela == 60) {// especial
			
			$urlPasarela = 'https://sis.redsys.es/sis/realizarPago';
//				$urlPasarela = 'http://localhost/concentrador/realizarPagoBBVA.htm';
			if ($moneda == '978') {$terminal = '004'; $clave = '2P0D63VVM7R55264';} 
                else {
					muestraError ("falla por moneda en Especial", $correoMi);
                }
                $comName = 'CARIBEANTRAVELWEB';
                $idCom = '285772844';

			$query = "select nombre, servicio from tbl_reserva where codigo = '$transaccion' and id_comercio = '$comercio'";
			if (_MOS_CONFIG_DEBUG)
				echo "<br>" . $query;
			$correoMi .= "query= " . $query . "<br>\n";
			$temp->query($query);

			if (!$temp->num_rows() == 0) {
				$producto = $temp->f('servicio');
				$titular = $temp->f('nombre');
			} else {
				$producto = 'Servicio';
				$titular = 'Nombre';
			}
            
            $correoMi .= "producto=$producto<br>\ntitular=$titular<br>\n";

			$moneda1 = '0' . $moneda;
			$tipoTrans = '0';
			$est = 'hidden';
			$urldirOK = $urlredir . '&est=ok';
			$urldirKO = $urlredir . '&est=ko';
			$urlcomercio = _URL_COMERCIO;
            
			if ($idioma == 'en') $idi = '002'; 
            elseif ($idioma == 'es') $idi = '001';
            elseif ($idioma == 'fr') $idi = '004';
            elseif ($idioma == 'it') $idi = '007';
            elseif ($idioma == 'de') $idi = '005';
            elseif ($idioma == 'sv') $idi = '008';
            elseif ($idioma == 'nl') $idi = '006';
            elseif ($idioma == 'pt') $idi = '009';
			
//			if (_MOS_CONFIG_DEBUG) $idi = '002';
            
			if (_MOS_CONFIG_DEBUG)
				$est = "text";
			if (_MOS_CONFIG_DEBUG)
				echo "idioma=$idioma<br>";
			if (_MOS_CONFIG_DEBUG)
				echo "$importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave<br>";
//                $message = $importe . $trans . $idCom . $moneda . $clave;
            $correoMi .= "$importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave<br>\n";
                $message = $importe . $trans . $idCom . $moneda . $tipoTrans . $urlcomercio . $clave;
			if (_MOS_CONFIG_DEBUG)
				echo "tira=$message<br>";
            if ($pasarela == 23 ) {
                $Digest = sha1($message);
            } else {
                $Digest = strtoupper(sha1($message));
            }
            $correoMi .= "Digest=$Digest<br>\n";
			$data = array(
								"Ds_Merchant_Amount" => $importe,
								"Ds_Merchant_Currency" => $moneda,
								"Ds_Merchant_Order" => "$trans",
								"Ds_Merchant_ProductDescription" => $producto,
								"Ds_Merchant_Titular" => $titular,
								"Ds_Merchant_MerchantCode" => $idCom,
								"Ds_Merchant_MerchantURL" => $urlcomercio,
								"Ds_Merchant_UrlOK" => $urldirOK,
								"Ds_Merchant_UrlKO" => $urldirKO,
								"Ds_Merchant_MerchantName" => $comName,
								"Ds_Merchant_ConsumerLanguage" => $idi,
								"Ds_Merchant_MerchantSignature" => $Digest,
								"Ds_Merchant_Terminal" => $terminal,
								"Ds_Merchant_TransactionType" => $tipoTrans
			);

			foreach ($data as $key => $value) {
				echo  "<br>\n$key = $value";
			}
//			ini_set("max_input_time", 30);ini_set("max_execution_time", 30);ini_set("exit_on_timeout", "on");
			$correoMi .=  $urlPasarela."<br>\n";
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
			echo "<br>UserAgent=".$userAgent."<br>";
			$cookieFile = "cookie.txt";
			$salidaCurl = '';$i = 1;
			$cook = "s_cc=true; s_sq=bbvaniceprod%3D%2526pid%253DLanding/Plan%252520de%252520Pensiones/Home%2526pidt%253D1%2526oid%253Dfunctiononclick%252528event%252529%25257Bjavascript%25253Avalidar%252528%252529%25253B%25257D%2526oidt%253D2%2526ot%253DBUTTON";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $urlPasarela);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_COOKIE, $cook);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$response = curl_exec($ch);
			if ($response === false) {
			  fail(2, "Error de conexion remota");
			}	else {
				file_put_contents("entra", $response); // Debug
			}
			
			
			$cadenSal = "<form name=\"envia\" action=\"$urlPasarela\" method=\"post\">
								<input type=\"$est\" name=\"Ds_Merchant_Amount\" value=\"$importe\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Currency\" value=\"$moneda\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Order\" value=\"$trans\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ProductDescription\" value=\"$producto\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Titular\" value=\"$titular\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantCode\" value=\"$idCom\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantURL\" value=\"$urlcomercio\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlOK\" value=\"$urldirOK\"/>
								<input type=\"$est\" name=\"Ds_Merchant_UrlKO\" value=\"$urldirKO\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantName\" value=\"$comName\"/>
								<input type=\"$est\" name=\"Ds_Merchant_ConsumerLanguage\" value=\"$idi\"/>
								<input type=\"$est\" name=\"Ds_Merchant_MerchantSignature\" value=\"$Digest\"/>
								<input type=\"$est\" name=\"Ds_Merchant_Terminal\" value=\"$terminal\"/>
								<input type=\"$est\" name=\"Ds_Merchant_TransactionType\" value=\"$tipoTrans\"/>";

				$cadenSal .= '<input type="submit" />
								 </form>
								 ';
			echo $cadenSal;

			$curl_info = curl_getinfo($ch);

				foreach ($curl_info as $key => $value) {
					echo  $key." = ".$value."<br>\n";
				}

			if(strlen(curl_error($ch))) echo "Curl error: ".curl_error($ch)."<br>\n";

				echo "Ejecut&oacute; la llamada por curla, env&iacute;o $i - ".$response."<br>\n";
				
			curl_close($ch);exit;
			
			if (strlen($salidaCurl) > 3) {
				echo "entra<br />";
				$hd = fopen('nuevo.txt', 'w+');
				
				$arrCade = explode("<form",$salidaCurl);
				foreach ($arrCade as $value) {
					if (strstr($value, "formTarjeta")) {
						$strform = substr("<form".$value, 0, strpos($value, "</form>")+13);
					}
				}
				$sisjs = substr($strform, strpos($strform, "jsessionid=")+11, 37);
				fwrite($hd, $salidaCurl);
				fclose($hd);
				echo $strform;
			}
			
			
		}
	} else {
		muestraError ("falla por firma", $correoMi);
	}


	if ($pasoaBBVA == true) { //Para las pasarelas del BBVA
		$correoMi .= "\nfirma= $idPas . $idCom . $trans . $importe . $moneda . $localizador . $clave\n";
		$firmaSal = strtoupper(SHA1($idPas . $idCom . $trans . $importe . $moneda . $localizador . $clave));


		if (_MOS_CONFIG_DEBUG)
			echo "Terminal= " . $idPas . "<br>";
		if (_MOS_CONFIG_DEBUG)
			echo "Comercio= " . $idCom . "<br>";
		if (_MOS_CONFIG_DEBUG)
			echo "transaccion= " . $trans . "<br>";
		if (_MOS_CONFIG_DEBUG)
			echo "importe= " . $importe . "<br>";
		if (_MOS_CONFIG_DEBUG)
			echo "moneda= " . $moneda . "<br>";
		if (_MOS_CONFIG_DEBUG)
			echo "localizador= " . $localizador . "<br>";
		if (_MOS_CONFIG_DEBUG)
			echo "clave= " . $clave . "<br>";
		if (_MOS_CONFIG_DEBUG)
			echo "firmaSal= " . $firmaSal . "<br>";
		if (_MOS_CONFIG_DEBUG)
			echo "urlredir= " . $urlredir . "<br>";

		$lt = "&lt;";
		$gt = "&gt;";
		$xml.=$lt . "tpv" . $gt;
		$xml.=$lt . "oppago" . $gt;
		$xml.=$lt . "idterminal" . $gt . $idPas . $lt . "/idterminal" . $gt;
		$xml.=$lt . "idcomercio" . $gt . $idCom . $lt . "/idcomercio" . $gt;
		$xml.=$lt . "idtransaccion" . $gt . $trans . $lt . "/idtransaccion" . $gt;
		$xml.=$lt . "moneda" . $gt . $moneda . $lt . "/moneda" . $gt;
		$xml.=$lt . "importe" . $gt . $importe100 . $lt . "/importe" . $gt;
		$xml.=$lt . "urlcomercio" . $gt . $urlcomercio . $lt . "/urlcomercio" . $gt;
		$xml.=$lt . "idioma" . $gt . $idioma . $lt . "/idioma" . $gt;
		$xml.=$lt . "pais" . $gt . $pais . $lt . "/pais" . $gt;
		$xml.=$lt . "urlredir" . $gt . $urlredir . $lt . "/urlredir" . $gt;
		$xml.=$lt . "localizador" . $gt . $localizador . $lt . "/localizador" . $gt;
		$xml.=$lt . "firma" . $gt . $firmaSal . $lt . "/firma" . $gt;
		$xml.=$lt . "/oppago" . $gt;
		$xml.=$lt . "/tpv" . $gt;
		$peticion = $xml;
		if (_MOS_CONFIG_DEBUG)
			echo "peticion=$peticion<br>";
		$correoMi .= "peticion= " . $peticion . "<br>\n";


		$cadenSal = '
		 <form name="envia" action="' . $url_tpvv . '" method="post">
		 <input type="hidden" name="peticion" value="' . $peticion . '"/>
		 ';
		if (_MOS_CONFIG_DEBUG) {
			$cadenSal .= '<input type="submit" />
						 </form>';
		} else {
			$cadenSal .= '</form>
						 <script language=\'javascript\'>
							 document.envia.submit();
						 </script>';
		}
		$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";
		echo $cadenSal;
	}
	/* 	} else {
	  echo "<script>alert('Para pasar este punto debe habilitar las cookies.')</script>";
	  } */
} else {
	$correoMi .= "invalid";
	echo "<!-- invalid -->";
	$ref = $_SERVER['HTTP_REFERER'];
	$query = "insert into tbl_listaIp (ip, fecha, refer) values ('$dirIp', " . time() . ", '$ref')";
	$correoMi .= "<br>comercio=" . $d['comercio'] . " && transaccion=" . $d['transaccion'] . " && importe=" . $d['importe'] . " && moneda=" . $d['moneda'] . " && operaci&oacute;n=" .
			$d['operacion'] . " && firma=" . $d['firma'];
	$temp->query($query);
}

//correoAMi($titulo, $correoMi);
$correo->set_subject($titulo);
$correo->set_message($correoMi);
$correo->envia(9);
//echo $correoMi;

function muestraError ($etiqueta, $textoCorreo) {
	$correo = new correo();
	$textoCorreo .= $etiqueta;
	$correo->set_subject('Entrada de datos');
	$correo->set_message($textoCorreo);
	$correo->envia(9);
	echo '<script>document.writeln("<div id=\"errDvd\" style=\"margin:20px 0 0 "+
((window.innerWidth)-800)/2
+"px; width:800px; text-align:center;\">")</script>
<img src="images/pagina_error.png" width="347" height="304" alt="Error" title="Error" /><br /><br />
Hubo un  <span style="color:red;font-weight:bold;">Error</span>
en los datos enviados<br />por favor consulte a su comercio.<br />'.$etiqueta.'</div>
<!-- '.$etiqueta.' -->';
	exit;
}
?>