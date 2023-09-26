<?php

/**
 * Carga los datos necesarios para trabajar con las páginas interiores saltando el inicio
 * */

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

// var_dump($_SERVER);exit;

//Array con los datos a pasar a la página
// $_POST = array(
// 'inserta' => 'true',
// 'pago' => 'N',
// 'opera' => 'S',
// 'nombre' => 'julio',
// 'email' => 'jtoirac@gmail.com',
// 'importe' => '5',
// 'moneda' => '978',
// 'amex' => '0',
// 'tiempo' => '3',
// 'idioma' => 'es',
// 'trans' => '',
// 'pasarela' => '60',
// 'servicio' => 'dfgdsfga'
// );


$_POST = array(
		'pone' => 'true',
		'pago' => 'S',
		'opera' => 'S',
		'nombreT' => 'sdfgsdfgsdf',
		'emailT' => 'sdfg@dfgdfsgsd.ffg',
		'importeT' => '9043',
		'moneda' => '978',
		'idioma' => 'es',
		'servicio' => 'ga ergasdg adfga dgasdg'
);

//Carga las variables de sesion
verifica_entrada('jtoirac', 'Santaemilia453');
if ($_SESSION['comercio'] == 'todos') {
	$_SESSION['comercio'] = '122327460662';//comercio Prueba
} elseif (stripos($_SESSION['comercio'], ',')) {
	$arrcom = explode(',', $_SESSION['comercio']);
	$_SESSION['comercio'] = $arrcom[0]; //cojo el primer comercio que tenga
}

$q = "select nombre from tbl_comercio where idcomercio = '" . $_SESSION['comercio'] . "'";
$temp->query($q);
$comercio = $temp->f('nombre');
$idcomercio = $_SESSION['comercio'];

$idUsr = $_SESSION['id'];
$admin = $_SESSION['admin_nom'];
$idcomStr = $_SESSION['idcomStr'];
$rol = $_SESSION['rol'];
$grupoRol = $_SESSION['grupo_rol'];
$corrAdmin = $_SESSION['email'];

// $comercio = "prueba";
// $idcomercio = '122327460662';

//poner acá la página a cargar
include 'admin/adminis.func.php';
envTransf($_POST);
?>