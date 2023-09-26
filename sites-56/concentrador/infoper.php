<?php
ini_set('display_errors', 0);
//error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('_VALID_ENTRADA', 1);
if (!session_start()) session_start();
require_once( 'admin/classes/entrada.php' );
require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/correo.php' );
$ent = new entrada;
$temp = new ps_DB;
$correo = new correo;

$d = $_POST;
$correoMi = json_encode($d)."<br>";
// muestraError("datos entrada",$correoMi);

if (!($comer = $ent->isAlfanumerico($d['comercio'], 15))) muestraError ("falla por comercio", $correoMi);
if (!($fechaini = $ent->isReal($d['fechaini'], 15))) muestraError ("falla por fecha de inicio", $correoMi);
if (!($fechafin = $ent->isReal($d['fechafin'], 15))) muestraError ("falla por fecha final", $correoMi);
if (!($clave = $ent->isAlfanumerico($d['firma']))) muestraError ("falla por firma", $correoMi);

$q = sprintf("select palabra from tbl_comercio where idcomercio = '%s'", $comer);
$correoMi .= "$q<br>";
$temp->query($q);
$palabra = $temp->f('palabra');
$fcalc = hash("sha256", $comer.$fechaini.$fechafin.$palabra);

$correoMi .= "$comer.$fechaini.$fechafin.$palabra<br>";
$correoMi .= "firma Calculada=$fcalc<br>";
$correoMi .= "firma Enviada=$clave<br>";
// echo $correoMi;

if ($clave !== $fcalc) muestraError ("falla por firma2", $correoMi);

$q = sprintf("select idtransaccion, identificador, estado, valor, moneda from tbl_transacciones where idcomercio = '%s' and fecha_mod between %d and %d order by fecha_mod desc", $comer, $fechaini, $fechafin);
$correoMi .= "$q<br>";
// echo $correoMi;
$temp->query($q);

echo json_encode($temp->loadAssocList());
//muestraError("todo bien",$correoMi);


function muestraError ($etiqueta, $textoCorreo) {
	error_log($etiqueta);
	error_log($textoCorreo);
	global $correoMi; 
	$correo = new correo();
	$textoCorreo .= $etiqueta;
	$correo->set_subject('Solicitando datos de las operaciones');
	$correo->set_message($textoCorreo);
	$correo->envia(9);
	//echo '<!-- '.$etiqueta.' -->';
	//$correo->todo(48, 'Error en los datos', $textoCorreo." ** ".$correoMi);
	exit;
}

?>