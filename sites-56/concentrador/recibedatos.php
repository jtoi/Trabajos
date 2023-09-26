<?php
define('_VALID_ENTRADA', 1);
require_once("admin/classes/SecureSession.class.php");
$Session = new SecureSession(3600);
include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';
include_once( 'admin/adminis.func.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'include/param.xml.php' );
require_once( 'include/correo.php' );
$temp = new ps_DB;
$ent = new entrada;
$cor = new correo();

$correoMi .= "Entra<br>\n";
$d = $_REQUEST;

foreach ($d as $key => $value) {
	$correoMi .= $key." = ". urldecode( $value )."<br>\n";
}
// echo $correoMi;

$cor->todo(52, "Recibe datos", $correoMi);


?>