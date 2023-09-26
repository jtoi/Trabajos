<?php

ini_set('display_errors', 1);
define( '_VALID_ENTRADA', 1 );
define('_DEBUG_PROJECT',2);

require_once("classes/SecureSession.class.php");
$tiempoSession = 7500; //la session dura 2:05 min
//$tiempoSession = 20; //la session menos de 5 min
$Session = new SecureSession($tiempoSession);
//$Session->Destroy();


include_once( 'datos.php' );
include_once( 'classes/entrada.php' );
require_once( 'classes/mysqli.php' );
global $temp;
$temp = new ps_DB();
$ent = new entrada;
$temp->_debug = true;

include 'classes/PHPMailer.php';
include("classes/SMTP.php");
include_once( 'classes/funcion.php' );
//include_once("classes/correo.php");
include_once("classes/class_formHtml.php");
include_once("classes/class_tablaHtml.php");
include_once("classes/class_imgUpldr.php");
global $html;
//$mail = new PHPMailer\PHPMailer\PHPMailer(_DEBUG_PROJECT);
$mail = new PHPMailer\PHPMailer\PHPMailer();
$tabla = new tablaHTML;
$html = new formHTML;
$subir = new imgUpldr;
//$corCreo = new correo();
$fun = new funcion;
$evals = '';
$d=$_POST;
$dirIp = $_SERVER['REMOTE_ADDR'];
//$d = array();
error_log(" ");
if (_DEBUG_PROJECT != 0) {
	foreach ($d as $key => $value) {
		error_log("$key => $value");
	}
}

if ($_REQUEST['r'] == 1){
	$Session->Destroy();
	echo "<script type='text/javascript'>window.open('index.php','_self');</script>";
	exit();
}


//Verifica la entrada al concentrador
if ($contras = $ent->isAlfanumerico($d['password'], 32)) {
	$login = $ent->isCorreo(strtolower($d['email']), 32);
	// echo "$login -> $contras";
	$pag_salida = $fun->verif($contras, $login, $dirIp);
	if (!$pag_salida) {
		$evals = '$("#respue_spam").removeClass("label-success").addClass("label-danger").html("Email / Contraseña desconocido").show();
				$("#inputEmail").focus();
				$("#olvContr").show();
				$("#inputPassword").val("");';
	} else {
		$Session->SetFingerPrint ();
		$evals = 'SaveLocalStorageData("PROFILE_IMG_SRC", "'.$_SESSION['avatar'].'" );
					SaveLocalStorageData("PROFILE_NAME", "'.$_SESSION['admin_nom'].'");
					SaveLocalStorageData("PROFILE_REAUTH_EMAIL", "'.$_SESSION['email'].'");
					SaveLocalStorageData("PROFILE_IDIOMA", "'.$_SESSION['idioma'].'");';
	}
}

if (isset($_SESSION['LoggedIn'])) {
	if ($Session->AnalyseFingerPrint($Analysis) === true) {

		if ('xtg' == $d['pas']) {
			include_once "pagina/{$d['mdr']}.php";
			exit();
		}

		if (!$_POST['password']) {
			$desc = '';
			foreach($_REQUEST as $nom => $valor) {
                if (is_array($valor)) $valor = implode (', ', $valor);
				$desc .= "$nom = ".htmlspecialchars($valor, ENT_QUOTES)." || ";
			}
		} else $desc = 'Entrada al sitio';
		
		if ($_SESSION['id'] > 0) {
			$query = "insert into tbl_bitacora (idadmin, texto) values (".$_SESSION['id'].", '$desc')";
			$temp->query($query);

			$query = "update tbl_admin set fecha_visita = ".time()." where id = ".$_SESSION['id'];
			$temp->query($query);
		}

//		function hotel_put() {
//			global $temp;
//			global $ent;
//		//			echo "entra";
//			if (($comp = $ent->isAlfabeto($_GET['componente'], 13)) && ($pag = $ent->isAlfabeto($_GET['pag'], 13))) {
//				$camino_busc = "componente/".$comp."/". $pag .".php";
//				
//				//actualiza la tabla cantAccesos
//				$query = "select count(*) tot from tbl_cantAccesos c, tbl_menu m where idadmin = ".$_SESSION['id']." and m.id = c.idmenu and link like '%componente=".$comp."&pag=". $pag."%'" ;
//				$temp->query($query);
////				echo $query."<br>";
//				
//				if ($temp->f('tot') > 0) 
//					$query = "update tbl_cantAccesos c, tbl_menu m set cant = cant + 1, fecha = ".time()."
//								where idadmin = ".$_SESSION['id']." and m.id = c.idmenu and link like '%componente=".$comp."&pag=". $pag."%'" ;
//				else
//					$query = "insert into tbl_cantAccesos values (null, ".$_SESSION['id'].", (select id from tbl_menu where link like '%componente=".$comp."&pag=". $pag."'), 1, ".time().")";
////				$query = "update tbl_cantAccesos c, tbl_menu m set cant = cant + 1 where m.id = c.idmenu and link like '%componente=".$comp."&pag=". $pag."%'" ;
////				echo $query;
//				$temp->query($query);
//				
//				include_once ($camino_busc);
//			}
//		}

		if (!$_SESSION['id']) include('template/login.php');
		else {
//			if (strlen($_REQUEST['p']) > 1 && strlen($_REQUEST['p']) < 5) {
//				if ($pag=$ent->isAlfabeto($_REQUEST['p'], 5) ) {
//					if (is_file("pagina/$pag.php"))
//						include "pagina/$pag.php";
//					else header("Status: 404 Not Found");
//				}
//			}
			if (strlen($pag_salida) < 5) include('template/inicio.php');
			else {
				if (strstr($pag_salida, 'inicio')) $pag_salida = 'componente=comercio&'.$pag_salida;
				$saltos = explode( '&', $pag_salida);
				for ($x = 0; $x < count($saltos); $x++) {
					$termino = explode( '=', $saltos[$x]);
					$_GET[$termino[0]] = $termino[1];
				}
				include('template/inicio.php');
			}
		}

		// Debugger
		if (_MOS_CONFIG_DEBUG) {
			echo '<div id="debD"><pre style="font-size:11px">';
			echo "<hr /><hr /><br>Datos enviados<br>";
			foreach ($_REQUEST as $key=>$value) {
				echo $key ." => " .$value. '<hr />';
			}
			echo "<hr /><hr /><br>Querys:<br>";
			echo $temp->log;
			echo "<hr /><hr /><br>Datos:<br>";
			// var_dump(get_defined_vars());
			echo $temp->_ticker . ' queries executed';
			foreach ($temp->_log as $k=>$sql) {
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
	} else {
		$Session->Destroy();
		//include_once('operini.php');
//		require_once( "lang/spanish.php" );
		include('template/login.php');
	}
} else {
	$Session->Destroy();
//	include_once('operini.php');
//	require_once( "lang/spanish.php" );
	include('template/login.php');
}
if (strlen($evals)) echo '<script type="text/JavaScript">
			$( document ).ready(function() {'
			.$evals.
		'}); </script>';

?>
