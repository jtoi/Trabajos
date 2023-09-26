<?php
ini_set('display_errors', 0);
//error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('_VALID_ENTRADA', 1);
if (!session_start()) session_start();
require_once( 'configuration.php' );
include 'include/mysqli.php';
include 'include/correo.php';

$temp = new ps_DB;
$corr = new correo;

$d = $_REQUEST;
$corrMi = '';

if (is_array($d)) {
    foreach ($d as $key => $value) {
        $corrMi .= "$key = $value <br>\n";
    }
} else $corrMi .= $d;

$corr->todo(13, "Resultado Devol Tefpay Error 5xx", $corrMi);

?>