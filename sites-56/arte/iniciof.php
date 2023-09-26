<?php

/* 
 * Fichero de apoyo al Java
 */
$correoMi = 'Llega';
//error_log($correoMi, 0);
//ini_set('display_errors', 0);
////ini_set("session.save_path", '/var/www/vhost/concentradoramf.com/desc');
define( '_VALID_ENTRADA', 1 );
//
include_once( 'datos.php' );
include_once( 'classes/entrada.php' );
include_once( 'classes/mysqli.php' );
include_once( 'classes/funcion.php' );
include 'classes/PHPMailer.php';
include("classes/SMTP.php");
//global $temp;
$temp = new ps_DB();
$ent = new entrada;
$temp->_debug = true;
$fun = new funcion;
$mail = new PHPMailer\PHPMailer\PHPMailer();

$correoMi .= "\nPasa entrada";

$d = $_REQUEST;

//$d['fun'] = 'genFecEj';
//$d['datos'] = 'd M Y h:i a';

foreach ($d as $key => $value) {
	$correoMi .= "\n$key => $value";
}
//error_log($correoMi, 0);


/**
 * Cambia la contraseña cuando al usuario se le olvidó
 * @global object $ent
 * @global object $fun
 * @param string $email Correo
 * @return string
 */
function genCotr($email) {
	global $ent, $fun, $temp;
	if ($login = $ent->isCorreo(strtolower($email), 32)) {
		$res = $fun->genContr($login);
		$resuelve = strpos($res, 'Error');
		if (strpos($res, 'Error') === 0) {
			return json_encode(array("error"=>$res,"data"=>""));
		} else {
			error_log($res);
			$arrstr = explode('|', $res);
			$res = $arrstr[0];
			$idi = $arrstr[1];
			$nomb = $arrstr[2];
			
			$temp->query("select iso2 from tbl_idioma where id = $idi");
			$idi2 = $temp->f('iso2');
			$subj = $fun->idioma('Inscripcion en el sitio ArteOrganizer',$idi2);
			$cont = str_replace('{nomb}', $nomb, str_replace('{md5}', $res, file_get_contents("pagina/correo_inscripcion_$idi2.html")));
			
			error_log($fun->smtpmail(array($email,$nomb), $subj, $cont, null, null, array("images/phpmailer.gif", "images/icon-tweet.png", "images/icon-facebook.png")));
			
			return json_encode (array("error"=>"","data"=>"Su contraseña ha sido cambiada satisfactoriamente, revise su correo"));
		}
	} return json_encode(array("error"=>'Error: No es un email valido',"data"=>""));
	exit;
}

if (function_exists($d['fun'])) {
	echo call_user_func ($d['fun'],$d['datos']);
} else {
	echo json_encode(array("error"=>'Error: No se encuentra la funcion',"data"=>""));
}

?>