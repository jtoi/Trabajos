<?php
//  require_once "/usr/share/pear/Mail.php";
define( '_VALID_ENTRADA', 1 );
// require_once "Mail.php";
include_once( 'configuration.php' );
include_once 'include/mysqli.php';
//require_once 'include/correo-20150908.php';
require_once 'include/correo.php';
 
// print_r($_SERVER);
$corCreo = new correo();

//$corCreo->from = "TPV <tpv@travelsdiscovery.com>";
// $to = "Julio Toirac <jtoirac@gmail.com>";
 $to = "Alejandro Diaz <admin.red@avc.tur.cu>";
$subject = "Subject Sujetado5!";
$body = "Hi,\n\nEsta es una prueba";

$corCreo->to = $to;
echo $corCreo->todo(11,$subject,$body);
 ?>