<?php define( '_VALID_ENTRADA' , 1);
require_once("../../classes/SecureSession.class.php");
$Session = new SecureSession(3600);
include_once( '../../../configuration.php' );
include_once( '../../classes/entrada.php' );
require_once( '../include/mysqli.php' );
require_once( '../../../include/hoteles.func.php' );
include( "../../lang/{$_SESSION['idioma']}.php" );
include( "../../adminis.func.php" );
$temp = new ps_DB;
$ent = new entrada;

$d = $_REQUEST;
//print_r($d);
//$d['fun'] = 'instrf';$d['com'] ='34';$d['cli'] ='Julio';$d['imp'] ='56';$d['mon'] ='840';$d['cmb'] ='1';$d['eur'] ='56';$d['mtv'] ='dsfgasdf';$d['fec'] ='26/02/2014';
if ($d['fun'] == 'insidiom') { //inserta las traducciones de invitaciones, concendiciones y vouchers
    $error = $tex = '';
    if ($d['ins']) { 
	$tex = _IDIOMA_SALIDAOK;
//        $error = $d['tex']."\n";
        $q = "update tbl_traducciones set texto = '".$d['tex']."', fecha = unix_timestamp() where idIdioma = '{$d['idi']}' and idcomercio = '{$d['com']}' and tipo = '{$d['tipo']}'" ;
//        $q = "update tbl_traducciones set texto = '".utf8_encode($d['tex'])."', fecha = unix_timestamp() where idIdioma = '{$d['idi']}' and idcomercio = '{$d['com']}' and tipo = '{$d['tipo']}'" ;
//        $error .= $q;
        $temp->query($q);
		if ( $temp->getErrorMsg())	{$error = $temp->getErrorMsg();$tex='';}
    }
    $q = "select texto, from_unixtime(fecha,'%d/%m/%Y %H:%i:%s')fec from tbl_traducciones where idIdioma = {$d['idi']} and tipo = {$d['tipo']} and idcomercio = {$d['com']}";
//    $error .= "\n".$q;
    $temp->query($q);
    $arrSal = array("tex"=>  utf8_encode(html_entity_decode($temp->f('texto'),ENT_HTML5)), "fec"=>$temp->f('fec'));
    
//    mail('jtoirac@gmail.com', 'Inserta idioma', $error);
    
    echo json_encode(array("cont"=>$arrSal, "error"=>$error, "tex"=>$tex));
    
} elseif ($d['fun'] == 'cambUSD') {
		
	$filenames = '/home/jtoirac/temp/correo.txt';
	$filename = 'http://localhost/cubatravelse/administrator/';
	$filename = 'http://localhost/cubatravelse';
	$filename = 'http://localhost/consumer_ex_results.jsp.html';
	$filename = 'http://corporate.visa.com/pd/consumer_services/consumer_ex_results.jsp?actualDate='.date("m/d/Y").'&homCur=USD&forCur=EUR&fee=0';
	$cont = "";
	$error = "";
	$error = filesize($filename);
//	$conts = file_get_contents($filenames);
//	echo "conts=$conts\n";
	
	if (ini_get('allow_url_include') == 0) ini_set('allow_url_include', 1);
	if (ini_get('allow_url_fopen') == 0) ini_set('allow_url_fopen', 1);

//		$handle = fsockopen("corporate.visa.com", 80, $errno, $errstr, 12);
//
//	   fputs($handle, "GET /pd/consumer_services/consumer_ex_rates.jsp?src=ex_rez HTTP/1.0\r\n");
//	   fputs($handle, "Host: corporate.visa.com\r\n");
//	   fputs($handle, "Referer: http://corporate.visa.com\r\n");
//	   fputs($handle, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n");
//		//echo $viart_xml;
//
//		//$handle = fopen('http://www.banco-metropolitano.com/tasasn.htm', 'r');
//		if ($handle) {
//			while (!feof($handle)) {
//				$cont .= trim(fgets($handle))."|";
//			}
//			fclose($handle);
//		} else {$error = 'No habre la url';}
	
//	$body = file_get_contents('http://corporate.visa.com/pd/consumer_services/consumer_ex_rates.jsp?src=ex_rez');
	
	
//	if (floatval(phpversion()) >= 4.3) {
//        $cont = file_get_contents($filename);
//    } else {
//        if (!file_exists($filename)) return -3;
//        $rHandle = fopen($filename, 'r');
//        if (!$rHandle) return -2;
//
//        $cont = '';
//        while(!feof($rHandle))
//            $cont .= fread($rHandle, filesize($filename));
//        fclose($rHandle);
//    }
	$handle = fopen($filename,'rb');
//	echo "handle=$handle\n";
	if ($handle) {
//		echo "entr\n";
		while (!feof($handle)) {
				$cont = trim(fgets($handle));
				if (strpos($cont, '<span class="results">') > -1) break;
			}
	echo $cont."va\n";
		fclose($handle);
	} else $error = "no se abrió";
//	$cont = file_get_contents($filename);
//	$cont = substr($cont, strpos($cont, "<body"));
//	$cont = substr($cont, 0, strrpos($cont, "</body>"));
	
	echo json_encode(array("filename"=>$filename,"cont"=>  utf8_encode($cont), "error"=>$error));
} elseif ($d['fun'] == 'vouc') {
	if (strlen($d['htm']) < 1) {echo json_encode(array("error"=>_COMERCIO_ERROR_HTML)); exit;}
	if ($d['idio'] != 'En' && $d['idio'] != 'Es') {echo json_encode(array("error"=>_COMERCIO_ERROR_IDI)); exit;}
	if (strlen($d['com']) < 1) {echo json_encode(array("error"=>_COMERCIO_ERROR_COM)); exit;}
	
	$q = "update tbl_comercio set voucher".$d['idio']." = '".htmlentities($d['htm'], ENT_QUOTES, 'UTF-8')."' where idcomercio = ".$d['com'];
	$temp->query($q);
	
	echo json_encode(array('error'=>_COMERCIO_DAT));
} elseif ($d['fun'] == 'instrf') {
		sleep(1);
	$error=$pase="";
//	echo $ent->isDate($d['fec']);
	if (!$ent->isEntero($d['com'])) $error .= "Comercio incorrecto\n";
	if (!$ent->isAlfanumerico($d['cli'])) $error .= "Cliente incorrecto\n";
	if (!$ent->isNumero($d['imp'])) $error .= "Importe inválido\n";
	if (!$ent->isEntero($d['mon'])) $error .= "Moneda incorrecto\n";
	if (!$ent->isNumero($d['cmb'])) $error .= "Cambio incorrecto\n";
	if (!$ent->isNumero($d['eur'])) $error .= "Importe Total incorrecto\n";
	if (!$ent->isAlfanumerico($d['mtv'])) $error .= "Motivo incorrecto\n";
	if (!$ent->isDate($d['fec'])) $error .= "Fecha incorrecto\n";

	if (strlen($error) == 0) {
        $query = "select * from tbl_comercio where id = {$d['com']}";
        $temp->query($query);
//        $comercioN = $temp->f('nombre');
//        $estCom = $temp->f('estado');
//        $datos = $temp->f('datos');
        $prefijo = $temp->f('prefijo_trans');
//        $datos = $temp->f('datos');
        $idCom = $temp->f('id');
        $idcomercio = $temp->f('idcomercio');
//        $valMin = $temp->f('minTransf');
        
//		$q = "select count(id) t from tbl_transferencias where facturaNum = '{$d['trf']}'";
//		$temp->query($q);
//		
//		if ($temp->f('t') == 0) {
        $trans = trIdent($prefijo);
//			$salida = false;
//			$fecTr = to_unix($d['fec']);
//			while (!$salida) {
//				$trans = (string)($prefijo).(date("mdHis"));//.(rand (10, 99));
//				$query = "select count(*) total from tbl_transacciones where idtransaccion = '$trans'";
//				$temp->query($query);
//				if ($temp->f('total') == 0) $salida = true;
//
//				$query = "select count(*) from tbl_transacciones_old where idtransaccion = '$trans'";
//				$temp->query($query);
//				if ($temp->loadResult() != 0) $salida = false;
//			}
			
//			inserta valores en la tabla de las transacciones
			$hora = time();
			$query = "insert into tbl_transacciones	(idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor_inicial, valor, tipoEntorno,
						moneda, estado, pasarela, idioma, tasa, euroEquiv)
					values ('$trans', '$idcomercio', '$trans', 'T', $hora, $hora, '".($d['imp']*100)."', '".($d['imp']*100)."', 'P', '{$d['mon']}', 'A', '5', 'es', '{$d['cmb']}', '{$d['eur']}')";
//			echo $query;
			$temp->query($query);
			if (!$temp->error) $pase = 'Transferencia correctamente insertada.';
			else {
					$pase = '';
					$error = $temp->error;
			}

		//	inserta los valores en la tabla de transferencias
			if (strlen($pase) > 0) {
				$query = "insert into tbl_transferencias (idTransf, cliente, idcomercio, idCom, facturaNum, fecha, fechaTransf, valor, moneda, concepto, idioma, idPasarela, email, idadmin)
							values ('$trans', '{$d['cli']}', '$idcomercio', '{$d['com']}', '$trans', '{$hora}', '{$fecTr}',
								'".($d['imp']*100)."', '{$d['mon']}', '{$d['mtv']}', 'es', '5', '{$d['correo']}', {$_SESSION['id']})";
	//			echo $query;
				$temp->query($query);
				if (!$temp->error) $pase = 'Transferencia correctamente insertada.';
				else {
						$pase = '';
						$error = $temp->error;
				}
			}
			
//		} else $error = "Una transferencia con el mismo número ya se encuentra registrada";
	}
	
	echo json_encode(array('error'=>utf8_encode($error),'pase'=>utf8_encode($pase)));
}

?>
