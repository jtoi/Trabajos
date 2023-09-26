<?php  defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Recopila las funciones generales de todo el sitio

/**
 * Verifica si es una ip blanca o no
 * @param type $ip 
 */
function ipblanca($ip) {
	global $temp;
	
	$q = sprintf("SELECT count(*) total FROM tbl_ipblancas WHERE ip='%s'", $ip);
	$temp->query($q);
	if ($temp->f('total') == 0) return false; 
	else {
		$temp->query("update tbl_ipblancas set fecha = ".time()." where ip = '$ip'");
		return true;
	}
}

/**
 * Verificación de los pagos por referencia
 */
function pagoxRef($comer, $pago, $refer){
	if (leeSetup('pagoRec') == 1 || $pago == 'R') {
		//El comercio envía como tipo de operación pago por referencia
		$q = "select t.codigo, t.idtransaccion, t.identificador, r.codBanco from tbl_referencia r, tbl_transacciones t where r.idtransaccion = t.idtransaccion and t.idcomercio = '$comer' and r.codConc = '$refer'";
		$temp->query($q);

		$identConc = hash("sha1",$temp->f('idtransaccion').$temp->f('codigo').$comer.$temp->f('identificador'));
		if ($identConc == $refer) return $temp->f('codBanco');
		else return null;

	}
}

/**
 * Verifica si la ip ha sido usada anteriormente
 * @global class $temp
 * @param type $ip
 * @return boolean
 */
function ipusada($ip) {
	global $temp;
	
	$q = sprintf("SELECT count(*) total FROM tbl_transacciones WHERE ip='%s'", $ip);
	$temp->query($q);
	if ($temp->f('total') == 0) return false; else return true;
}

function caractEspecialesHtml($str) {
	return str_replace('Ñ', '&Ntilde;',
	str_replace('Ú', '&Uacute;',
	str_replace('Ó', '&Oacute;',
	str_replace('Í', '&Iacute;',
	str_replace('É', '&Eacute;', 
	str_replace('Á', '&Aacute;', 
	str_replace('ñ', '&ntilde;',
	str_replace('ú', '&uacute;',
	str_replace('ó', '&oacute;',
	str_replace('í', '&iacute;',
	str_replace('é', '&eacute;', 
	str_replace('á', '&aacute;', 
	$str))))))))))));
}

/**
 * Convierte los acentos en caracteres web
 * @param type $str
 * @return type
 */
function convertir_especiales_html($str){
   if (!isset($GLOBALS["carateres_latinos"])){
      $todas = get_html_translation_table(HTML_ENTITIES, ENT_NOQUOTES);
      $etiquetas = get_html_translation_table(HTML_SPECIALCHARS, ENT_NOQUOTES);
      $GLOBALS["carateres_latinos"] = array_diff($todas, $etiquetas);
   }
   $str = strtr($str, $GLOBALS["carateres_latinos"]);
   return $str;
}

/**
 * Evalúa un TZ si está completo 
 * o lo completa con puntos como separador
 *
 * @param [type] $var
 * @return void
 */
function evalua($var) {
	if (stripos($var, ":")) {
		$s = explode(":",$var);
		if (strlen($s) == 1) $var .= '0';
		if (strlen($s) == 0) $var .= '00';
	} else $var .= ":00";
	return str_replace(':', '.', $var);
}

/**
 * Verifica que la conexión del usuario del concentrador 
 * no se está realizando detrás de un proxy
 *
 * @return void
 */
function verProxy() {
	global $temp;
	global $ip;

	$arrTZ = explode("|",$_COOKIE['TZ']);
	$arrTZ[0] = $arrTZ[0].".00";
	error_log("TZ=".$arrTZ[0]);
	error_log("DST=".$arrTZ[1]);
	error_log("nonDST=".$arrTZ[2]);
	error_log("IP=$ip");
	$pais = damepais($ip);
	if ($pais == '' || $pais == null) $pais = 55;

	$temp->query("select TZ from tbl_timeZone where idpais = ".$pais);
	$arrtz = $temp->loadResultArray();
	for($i=0; $i<count($arrtz); $i++) {
		$tz = str_replace(":", ".", $arrtz[$i]);
		error_log("tz=$tz");
		error_log("arrTZ1=".evalua($arrTZ[1]));
		error_log("arrTZ2=".evalua($arrTZ[2]));
		if ($tz == evalua($arrTZ[1]) || $tz == evalua($arrTZ[2])) return true;
	}
	$temp->query("select count(*) total from tbl_ipBL where ip = '$ip'");
	if ($temp->f('total') == 0)	$temp->query("insert into tbl_ipBL (ip, cuenta, fecha) values ('$ip', '4', '".time()."')");
	return false;
	// $temp
}

/*
 * Verifica que la IP desde donde se está efectuando el pago
 * no sea un proxy anónimo.
 * 
 */
function ipBloqueada($ip) {
	global $temp;
	error_log("?????????????????????");
	
	//verifica que no sea una ipblanca
	$q = sprintf("SELECT count(*) total FROM tbl_ipblancas WHERE ip='%s'", $ip);
	$temp->query($q);
	error_log($q);
	error_log("total=".$temp->f('total'));
	if ($temp->f('total') > 0) {
		$temp->query("update tbl_ipblancas set fecha = ".time()." where ip = '$ip'");
		return false;
	}
	
	//verifica que esté en la tabla de las ip bloqueadas
	$q = sprintf("SELECT cuenta FROM tbl_ipBL WHERE ip='%s'", $ip);
	error_log($q);
	$temp->query($q);
	$cuenta = $temp->f('cuenta');
	error_log("cuenta=$cuenta");
	if ($cuenta > 2) {
		$temp->query(sprintf("update tbl_ipBL set cuenta = (cuenta + 1), fecha = unix_timestamp() where '%s'", $ip));
		$cuenta +1;
		if ($temp->f('cuenta') == 3) {
			$correoMi = "Intento de pago desde la IP Bloqueada: $ip";
	//		echo $correoMi;
			echo correoAMi('IP bloqueada ',$correoMi);
		}
		error_log(' ');
		error_log("LACUENTA=$cuenta");
		return $cuenta;
	} else return false;
}

function ipCuba($ip) {
    global $temp, $correoMi;
    error_log("CHEQUEA IP CUBANA");
    error_log("IP=".$ip);
    $correoMi .= "<br>Chequea si la ip es de Cuba<br>";
    $correoMi .= "Ip a chequear: $ip<br>";

    $q = "select ipentrada, ipfinal from tbl_ipCubana";
    $temp->query($q);
    $arrBloques = $temp->loadRowList();
    $iplong = ip2long($ip);
    // $correoMi .= "Bloques de Ips cubanas: ".json_encode($arrBloques)."<br>";

    foreach ($arrBloques as $bloque) {
        $correoMi .= "Analiza el bloque: ".$bloque[0]." - ". $bloque[1]."<br>";
        if ($iplong >= ip2long($bloque[0]) && $iplong <= ip2long($bloque[1])) {
            $correoMi .= "La IP est&aacute; en el Bloque retorno falso<br><br>";
            return true;
        }
    }
    $correoMi .= "La IP no es cubana, pasa<br><br>";
    return false;
}


/**
 * Obtiene la IP
 * @return string
 */
function GetIP()
{
    if ( getenv("HTTP_CLIENT_IP") ) {
        $ip = getenv("HTTP_CLIENT_IP");
    } elseif ( getenv("HTTP_X_FORWARDED_FOR") ) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
        if ( strstr($ip, ',') ) {
            $tmp = explode(',', $ip);
            $ip = trim($tmp[0]);
        }
    } else {
        $ip = getenv("REMOTE_ADDR");
    }
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) return false;
	
    return $ip;
}

/**
 * Marca la ip para seguirla si ya estaba en la tabla de las ipBL o la inserta
 * @global class $temp
 * @param string $ip
 * @return boolean
 */
function marcaIP($ip, $var = 1) {
	global $temp;
	
	if (!ipblanca($ip)) {
		error_log(sprintf("SELECT count(cuenta) total FROM tbl_ipBL WHERE ip='%s'", $ip));
		$temp->query(sprintf("SELECT count(cuenta) total FROM tbl_ipBL WHERE ip='%s'", $ip));
		error_log($temp->f('total'));
		if ($temp->f('total') == 0) {
		error_log(sprintf("insert into tbl_ipBL (ip, fecha, cuenta) values ('%s', unix_timestamp(), $var)", $ip));
			$temp->query(sprintf("insert into tbl_ipBL (ip, fecha, cuenta) values ('%s', unix_timestamp(), $var)", $ip));
		} else {
		error_log(sprintf("update tbl_ipBL set cuenta = (cuenta + $var), fecha = unix_timestamp() where ip = '%s'", $ip));
			$temp->query(sprintf("update tbl_ipBL set cuenta = (cuenta + $var), fecha = unix_timestamp() where ip = '%s'", $ip));
		}
		error_log('');
	} else error_log("IP blanca no se marca");
	return;
}

/*
 * Envía correos a mí
 */
function correoAMi($subject,$correoMi) {
//	echo  $correoMi;
	$root = $_SERVER['DOCUMENT_ROOT'];
	if(strpos($root, "wamp") !== FALSE) $root .= "concentrador/";else $root .= "/";
	$dir = $root."traza/";
// 	trigger_error("Directorio a escribir $dir", E_USER_WARNING);
	$correoMi .= "\n".$root."\n".$dir;
	$headers = 'From: tpv@caribbeanonlineweb.com' . "\n" .
		 'Reply-To: tpv@caribbeanonlineweb.com' . "\n" .
		 'X-Mailer: PHP/' . phpversion();
	$to      = 'jtoirac@gmail.com';
	mail($to, $subject, $correoMi, $headers);
	
	//salva los logs en la base de datos
//	$query = "insert into tbl_traza (titulo, traza, fecha) values ('$subject', '".htmlentities($correoMi)."', '".date('d/m/Y H:i:s')."')";
//	$temp->query($query);
//	echo  $correoMi;

	//salva en un fichero log diario
	$correoMi = "[ ".date("D M d H:i:s Y")." ]\n".$correoMi;
	$correoMi .= "\n\n********************************************************************************\n";
// 	trigger_error("dir=$dir", E_USER_NOTICE);
	if (!is_dir($dir)){
		//no está creado el directorio a crearlo
		if (!mkdir($dir)) trigger_error("No se puede crear el directorio", E_USER_WARNING);
	} else {
// 		trigger_error("Directorio existe", E_USER_NOTICE);
		$filename = $dir.date('Ymd').str_replace(" ", "", $subject).".txt";
// 		trigger_error("fichero: $filename", E_USER_NOTICE);
		//busco que esté creado el fichero ya si está lo escribo al final si no lo creo y escribo
		$handle = fopen($filename, 'a');
		if (is_writable($filename)) {
			if (fwrite($handle, $correoMi) === FALSE) {
				trigger_error("No se puede escribir el fichero ($filename)", E_USER_WARNING);
			}
		} else trigger_error("Fichero ($filename) no se deja escribir.", E_USER_WARNING);
	}
}


function quote_smart($value)
{
    // Stripslashes
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    // Quote if not a number or a numeric string
    if (!is_numeric($value)) {
        $value = "'" . mysql_real_escape_string($value) . "'";
    }
    return $value;
}

// retorna precio con s&iacute;mbolo de moneda
function precio_moneda ($precio) {
	$precio = number_format($precio,2);
	if (_POSICION_MONEDA == 0) return _MONEDA.' '.$precio;
	else return $precio.' '._MONEDA;
}

// retorna el número unix de una fecha en formato dd/mm/yyyy
function to_unix ($partes) {
	$div = explode(' ', $partes);
	$fecha = explode('/', $div[0]);
	if (count($div) > 0) {
		$tiempo = explode(':', $div[1]);
		$hora = $tiempo[0]*1;
		$min = $tiempo[1]*1;
		$seg = 0;
		if ($hora == 24 && $min == 0) {
			$hora = 23; $min = 59; $seg = 59;
		}
	} else $hora = $min = 0;
//	echo  "$partes hora=".mktime($hora,$min,0,$fecha[1],$fecha[0],$fecha[2])."<br>";
	return mktime($hora,$min,$seg,$fecha[1],$fecha[0],$fecha[2]);
}

function cambiaFecha ($fecha, $separ_entr='/', $separ_salida='-') {
	$fechastr = explode($separ_entr,$fecha);
	return $fechastr[2].$separ_salida.$fechastr[1].$separ_salida.$fechastr[0];
}

function getParam(&$var, $campo, $mask=0) {
	$return = null;

	if (isset( $var['name'] )) {
		$return = $var[$campo];

		if (is_string( $return )) {
			// trim data
			if (!($mask)) {
				$return = trim( $return );
			}

			// account for magic quotes setting
			if (!get_magic_quotes_gpc()) {
				$return = addslashes( $return );
			}
		}
		return $return;
	}
}

function read_file( $file ) {
	// open the HTML file and read it into $html
	if (file_exists( $file )) {
		$html_file = fopen( $file, "r" );
	}
	else {
		return;
	}
	$html = "";

	while (!feof($html_file)) {
		$buffer = fgets($html_file, 1024);
		$html .= $buffer;
	}
	fclose ($html_file);

	return( $html );
}

function hoteles_mod($ubic) {
	if ($_REQUEST['componente']) $componente = "componente/".$_REQUEST['componente'];
	if ($_REQUEST['modulo']) $modulo = "/".$_REQUEST['modulo'];
	if ($_REQUEST['pag']) $pag = "/".$_REQUEST['pag'].'.php';
	else $pag = "/index.php";

	$camino = $componente.$modulo.$pag;
	if ($ubic == 'front_page' && !$_REQUEST['componente']) echo require( "componente/front_page/index.php" );
	elseif ($ubic == 'front_page' && $_REQUEST['componente']) require_once( $camino );
	elseif ($ubic != 'front_page'){
		$cur = new ps_DB;

		$q = "select e.* from tbl_elemento e, tbl_ubicacion u, tbl_elemento_ubicacion eu where u.idubicacion = eu.idubicacion and e.idelemento = eu.idelemento and u.nombre = '$ubic' order by u.idubicacion, e.orden";

		$cur->query( $q );
		while ($cur->next_record()){
			if ($cur->f('tipo') == 'M') { //es un men&uacute;
				$men = $cur->f('nombre');
				hoteles_menu_mod($cur->f('nombre'));
			}
			else { // otro tipo de elemento
				$mod = $cur->f('nombre');
				require( "modulo/$mod.php" );
			}
		}
	}
}

function cuerpo() {
	if ($_REQUEST['componente']) include_once("componente/".$_REQUEST['componente']."/index.php");
	else include_once("componente/front_page/index.php");
}

function is_email_valid($email) {
	if(eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $email)) return TRUE;
	else return FALSE;
}

function correo ($t_email, $f_email, $subject, $message, $send) {

  if ( $t_email && $f_email && $subject && $message && $send ) {
    if (is_email_valid($t_email) && is_email_valid($f_email)) {
      mail($t_email, $subject, $message, "From: $f_email\r\n");
    } else {
      if ( !is_email_valid($t_email) ) {
        echo "<b>To</b> email address was <b>invalid</b><br />";
      }
      if ( !is_email_valid($f_email) ) {
        echo "<b>From</B> email address was <b>invalid</b><br />";
      }
    }
  }
}

function desofuscar ($pal_sec_ofuscada, $clave, $comer = null) {
// las siguientes dos líneas son ejemplos del formato  de entrada de datos
//	$pal_sec_ofuscada = "5D;7F;0A;27;09;0D;25;5D;04;01;0B;00;06;01;00;70;06;1C;19;19";
//	$clave_xor = "eH2dJ9gkB82915026***";

	if (!$comer) $comer = _ID_COMERCIO;
	$clave_xor = substr($clave,0,8).substr($comer,0,9)."***";
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

/*
 *Generador de password
 */
function suggestPassword($largo, $pass = true) {
	if ($pass) $pwchars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
	else $pwchars = "0123456789";
	$passwd = "";

	for ( $i = 0; $i < $largo; $i++ ) {
        $passwd .= substr($pwchars, rand(1, strlen($pwchars)), 1 );
    }

    return $passwd;
}

function convierte($comercio, $transaccion, $importe, $moneda, $operacion, $identif = null, $fecha = null) {
	global $temp;

	$query = "select palabra from tbl_comercio where idcomercio = $comercio";
	$temp->query($query);
	$palabra = $temp->loadResult();

	if ($palabra == 'lore') return null;

if (_MOS_CONFIG_DEBUG)	error_log("md5= $comercio.$transaccion.$importe.$moneda.$operacion.$palabra",0);
if (_MOS_CONFIG_DEBUG) 	echo 'md5= '.md5($comercio.$transaccion.$importe.$moneda.$operacion.$palabra)."<br>";//exit;
	if (!$identif) {return md5($comercio.$transaccion.$importe.$moneda.$operacion.$palabra);}
	else {return md5($comercio.$transaccion.$importe.$moneda.$operacion.$identif.$fecha.$palabra);}
}

function convierte256($comercio, $transaccion, $importe, $moneda, $operacion, $identif = null, $fecha = null) {
	global $temp;

	$query = "select palabra from tbl_comercio where idcomercio = $comercio";
	$temp->query($query);
	$palabra = $temp->loadResult();

	if ($palabra == 'lore') return null;

if (_MOS_CONFIG_DEBUG)	error_log("hash256= $comercio.$transaccion.$importe.$moneda.$operacion.$palabra",0);
if (_MOS_CONFIG_DEBUG) 	echo 'hash256= '.hash("sha256", $comercio.$transaccion.$importe.$moneda.$operacion.$identif.$fecha.$palabra)."<br>";//exit;
	if (!$identif) {return hash("sha256", $comercio.$transaccion.$importe.$moneda.$operacion.$palabra);}
	else {return hash("sha256", $comercio.$transaccion.$importe.$moneda.$operacion.$identif.$fecha.$palabra);}
}
?>
