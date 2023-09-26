<?php

//ini_set('display_errors', 1);
//
define( '_VALID_ENTRADA', 1 );
//
//require_once("../classes/SecureSession.class.php");
//$Session = new SecureSession(3600);
//
//include_once('../classes/entrada.php' );
//include_once('../datos.php' );
//require_once('../classes/mysqli.php' );
//include_once("../classes/correo.php");
//include_once("../classes/funcion.php");


//var_dump($_SESSION);exit;

$temp = new ps_DB();
$temp->_debug = true;
$corCreo = new correo();
$fun = new funcion;
$ent = new entrada;


$d=$_POST;
$dirIp = $_SERVER['REMOTE_ADDR'];

//include '../template/dashboard';


if (!isset($_SESSION['LoggedIn'])) {
	echo "sale";
    $Session->Destroy();
	
	$host  = str_replace('artista/', '', $_SERVER['HTTP_HOST']);
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'index.php';
	echo "http://$host$uri/$extra";
	//header("Location: http://$host$uri/$extra");
    
} else {

	include('template/dashboard');
//	header("Location: http://localhost/arte/artista/dashboard");
	// Debugger
	if (!_MOS_CONFIG_DEBUG) {
		echo '<div id="debD"><pre style="font-size:11px">';
		echo "<hr /><hr /><br>Querys:<br>";
		echo $temp->log;
		echo "<hr /><hr /><br>Datos:<br>";
		var_dump(get_defined_vars());
		echo $database->_ticker . ' queries executed';
		foreach ($database->_log as $k=>$sql) {
			echo $k+1 . "\n" . $sql . '<hr />';
		}
		echo "<hr /><hr /><br>Archivos:<br>";
		$archivos_incluidos = get_included_files();

		foreach ($archivos_incluidos as $nombre_archivo) {
			echo "$nombre_archivo\n";
		}
		echo "<hr /><hr /><br>Variables usadas:<br>";
		var_dump(array_keys(get_defined_vars()));
		//
		echo "<hr /><hr /><br>Funciones usadas:<br>";
		$arr = get_defined_functions();
		print_r($arr['user']);

		// 			echo "<hr /><hr /><br>Clases usadas:<br>";
		// 			$arr = get_declared_classes();
		// 			print_r($arr);

		echo "<hr /><hr /><br>Constates definidas:<br>";
		print_r(get_defined_constants());
		echo "</div>";
	}
}
?>