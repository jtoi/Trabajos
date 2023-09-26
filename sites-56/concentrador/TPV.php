<?php

/*Entrada a los TPVV*/

define('_VALID_ENTRADA', 1);
//error_reporting(E_ALL);
require_once("admin/classes/SecureSession.class.php");
$Session = new SecureSession(3600);
include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';
$temp = new ps_DB;
$ent = new entrada;

include_once( 'admin/adminis.func.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'include/param.xml.php' );
require_once( 'include/correo.php' );
include_once( "include/sendmail.php" );
include_once( "admin/classes/class_dms.php" );
include_once( "admin/classes/class_tablaHtml.php" );
include_once( "admin/lang/spanish.php" );
//include_once 'admin/classes/class_tabla2Html.php';

$usuario = null;
$contr = null;
$corCreo = new correo();

$d = $_POST;
// var_dump($d);
if ($_REQUEST['pag'] == 'logout') {
	session_unset();
	session_destroy();
	setcookie("rs", "", time() - (3600 * 6));
	header('Location: ' . _ESTA_URL . '/TPV.php');
}

if ($d['login'] && $d['password']) {
	if ($ent->isAlfanumerico($d['password'], 32) && $ent->isAlfanumerico($d['login'], 32)) {
		$contr = $ent->isAlfanumerico($d['password'], 32);
		$usuario = $ent->isAlfanumerico($d['login'], 32);

		if (!verifica_entrada($usuario, $contr)) {
			include_once 'admin/template/login.php';
			exit;
		}
		$idUsr = $_SESSION['id'];
		$admin = $_SESSION['admin_nom'];
		$idcomercio = $_SESSION['comercio'];
		$comercio = $_SESSION['comercioNom'];
		$idcomStr = $_SESSION['idcomStr'];
		$rol = $_SESSION['rol'];
		$grupoRol = $_SESSION['grupo_rol'];
		$corrAdmin = $_SESSION['email'];

		$caden = $usuario . "|" . $contr . "|" . $idUsr . "|" . $admin . "|" . $idcomercio . "|" . $comercio . "|" . $idcomStr . "|" . $rol
				. "|" . $grupoRol . "|" . $corrAdmin;
//		$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
//		$verCook = setcookie("rs", $caden, time() + (3600 * 6), '/', $domain, false);
//		echo $verCook;
		setcookie("rs", $caden, time() + (3600 * 6));
	} else {
		include_once 'admin/template/login.php';
		exit;
	}
} else {
	//Verifica si tiene la cookie necesaria para entrar
	if ($_COOKIE['rs']) {
		$arrCok = explode("|", $_COOKIE['rs']);
		$usuario = $arrCok[0];
		$contr = $arrCok[1];
	} else {
		include_once 'admin/template/login.php';
		exit;
	}
}

if ($contr && $usuario) {
	if (verifica_entrada($usuario, $contr)) {
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
		$pasarela = $_SESSION['pasarelaAlMom'];
// 		print_r($_SERVER);
		//print_r($_SESSION);
		include_once 'admin/template/tpv.php';
	} else {
		include_once 'admin/template/login.php';
		exit;
	}
} else {
	include_once 'admin/template/login.php';
	exit;
}
?>
