<?php 
define('_VALID_ENTRADA', 1);
// //ini_set("display_errors", 1);
// //error_reporting(E_ALL & ~E_NOTICE);
require_once( 'configuration.php' );
// include 'include/mysqli.php';
// require_once( 'include/correo.php' );

defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$temp = new ps_DB;
$corCreo = new correo;

$fechaAyer = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
$ayer = date('ymd',$fechaAyer);
// $ayer = '160930';
$q = "select c.idtitanes ctitanes, b.idtitanes btitanes, t.idtransaccion, o.titOrdenId, b.numDocumento, t.fecha".
		" from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b ".
		" where o.idtransaccion = t.idtransaccion ".
		" and o.idcliente = c.id".
		" and t.estado = 'A'".
		" and o.idbeneficiario = b.id".
		" and from_unixtime(t.fecha, '%y%m%d') = $ayer";
// 		" and from_unixtime(t.fecha, '%y%m%d') > '160630'";
$temp->query($q);
$arrOpr = $temp->loadRowList();
$correoMi .= "Sube ficheros de Confirmación de orden a Titanes\n<br>";
$correoMi .= "q=$q"."\n<br>";
if (count($arrOpr)>0) {
	$file = "T086_payment_confirm_".date('Ymd_His',time()).".txt";
	$camin = "/var/www/vhosts/".str_replace("https://", "", _ESTA_URL)."/httpdocs/desct/";
	$correoMi .= "file=$file"."\n<br>";
// 	array_map('unlink', glob("$camin*.txt"));
	$texLine = "";
	if ($fhan = fopen($camin.$file, 'a')) {
		foreach ($arrOpr as $pago){
			$texLine .= "FINCIMEX-MAF|T0860001|".$pago[3]."|0|".date('d/m/Y',$pago[5])."|".$pago[4]."|ID\n";
		}
		$correoMi .= "texLine2/$fhan=$texLine"."\n<br>";
		$correoMi .= "Escritura en fichero: ".fwrite($fhan, $texLine)."\n";
	} else {
		$correoMi .= "Se produce un error en la escritura del fichero ".$camin.$file." error: $fhan \n<br>";
		muestraError("Error en la escritura del fichero");
	}
	fclose($fhan);
	sleep(10);
	
	$ftp_server = "195.57.91.186";
	$ftp_user = "T086";
	$ftp_pass = "Tre322wqA";
	
	// set up a connection or die
	if ($conn_id = ftp_connect($ftp_server)) {
	
		// try to login
		if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
			ftp_pasv($conn_id, true);
			ftp_chdir($conn_id, "T086IN");
			
			//realiza 3 intentos de subida de ficheros 
			$cuenta = 0;
			while (!($ret = ftp_put($conn_id, $file, $camin.$file, FTP_ASCII)) && $cuenta < 3) {
				$correoMi .= "Se produce un error en la subida del fichero ".$camin.$file." error: $ret - intento #$cuenta ".date('H:i:s')."\n<br>";
				$arrEr = error_get_last();
				foreach ($arrEr as $key => $value) {
					$correoMi .= $key."=>".$value."\n<br>";
				}
				$cuenta++;
				sleep(60*$cuenta);
			}
			if ($cuenta > 2) muestraError("Error en la subida");
// 			else echo "ok";
			$correoMi .= "ret=$ret"."\n<br>";
		} else muestraError("Credenciales suministradas no son válidas");
		
		// close the connection
		ftp_close($conn_id);
	} else muestraError("No hubo conexión con el servidor, revisar internet");
}

muestraError("todo bien \n\n".$correoMi);

function muestraError ($textoCorreo) {
	global $correoMi, $corCreo;
	if (strstr($textoCorreo, "todo bien") > -1) $corCreo->todo(9, 'Confirmación de Orden de Ais a Titanes', $textoCorreo."\n<br> ** ".$correoMi);
	else $corCreo->todo(9, 'Error subiendo Confirmación de Orden de Ais a Titanes', $textoCorreo."\n<br> ** ".$correoMi);
	// 	exit;
}
?>