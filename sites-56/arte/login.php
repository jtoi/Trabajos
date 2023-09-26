<?php 
ini_set('display_errors', 1);

define( '_VALID_ENTRADA', 1 );

require_once("classes/SecureSession.class.php");
$Session = new SecureSession(3600);

include_once('classes/entrada.php' );
include_once('datos.php' );
require_once('classes/mysqli.php' );
include_once("classes/correo.php");
include_once("classes/funcion.php");

$temp = new ps_DB();
$temp->_debug = true;
$corCreo = new correo();
$fun = new funcion;
$ent = new entrada;



 ?>