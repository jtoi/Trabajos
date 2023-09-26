<?php

//print_r($_SERVER);
ini_set('display_errors', 0);
//ini_set("session.save_path", '/var/www/vhost/concentradoramf.com/desc');
define( '_VALID_ENTRADA', 1 );
include_once( '../configuration.php' );

//error_reporting(E_ALL);
require_once("classes/SecureSession.class.php");
$Session = new SecureSession(_TIEMPOSES); //la sessión cambiada a una duración de 5 horas a partir del 17/01/18
//$tiempoSession = 1900; //la session dura 5 min

//if (!session_id()) {
//	session_start();
//	ini_set('session.use_only_cookies', 1);
//	ini_set('session.cookie_lifetime', $tiempoSession);
//}
//setcookie('PHPSESSID', $_SESSION['sesionId'], time()+$tiempoSession);
//		echo "<br>sess=".session_id()."<br>";

include_once( 'classes/entrada.php' );
require_once( '../include/mysqli.php' );
require_once( '../include/class.timing.php' );
global $temp;
global $ip;
$temp = new ps_DB();
// $usr = &new ps_DB();
$ent = new entrada;
$temp->_debug = true;
$timing = new Timing();

#chequea que la session escrita sea esta misma.
//		$query = "select count(*) tot from tbl_admin where ident = '".session_id()."' and fecha_visita > ".(time() - 1200);
//		$temp->query($query);
//		if ($temp->f('tot') == 0) session_unset();

include_once( 'adminis.func.php' );
include_once( 'impresion.php' );
require_once( '../include/hoteles.func.php' );
require_once( '../include/class.inicio.php' );
require_once( '../include/param.xml.php' );
include_once( "../include/sendmail.php" );
include_once("classes/class_dms.php");
include_once("classes/class_tablaHtml.php");
include_once("../include/correo.php");
global $html;
$html = new tablaHTML;
$corCreo = new correo();
$user_agent = $_SERVER['HTTP_USER_AGENT'];

//include_once("adminsitrd.php");
if (_ESTA_URL != 'http://localhost:8080/concentrador') {
	if (!($ip = GetIP())) exit();
} else {$ip = '192.168.0.244';}
// $ip = '127.0.0.1';


//verifica el recaptcha de google
$secret = "6Le65ngUAAAAACyjumH5HoNodp6o9dyErFamxOQ-";

//verifica que no estamos detrás de un firewall
// verProxy();
// if (ipBloqueada($ip)) {//verifica que la ip no esté bloqueada
// 	echo '<form action="verifUser.php" name="valcode" method="post"><input type="hidden" name="ip" value="'.$ip.'"/></form><script>
// 	document.valcode.submit();
// </script>';
// 	$Session->Destroy();
// 	error_log("Se ha bloqueado el acceso a la administración desde la IP ".$ip);
// //	$corCreo->todo ( 11, 'IP banned admin '.$ip, "Se ha bloqueado el acceso a la administración desde la IP ".$ip );
// 	exit();
// }

//Verifica la entrada al concentrador
if ($contras = $ent->isAlfanumerico($_POST['password'], 32)) {
	//verifica que no sea una ipblanca
	//$temp->query("select count(id) total from tbl_ipblancas where ip = '$ip'");

	if (ipblanca($ip) === false) {
		if (ipusada($ip) == false) {
			//verifica en google recaptcha
			$respuesta = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
			$retorno = json_decode($respuesta);
		//	var_dump($retorno);
//			if ($retorno->success == false || $retorno->score < 0.7) {
//				echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;"><div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;</div></div><div style="text-align:center; margin-top:100px; font-family:sans serif; font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">Su IP '.$ip.' estest&aacute; bloqueada / Your IP '.$ip.' is banned captcha</a></div>';
//				$corCreo->todo ( 11, 'IP '.$ip.' banned admin reCaptcha', "Se ha bloqueado el acceso a la administración desde la IP ".$ip ." con scrore de".$retorno->score);
//				$Session->Destroy();
//				marcaIP($ip,5);
//				exit();
//			}
		}
	}

	if (_ESTA_URL != 'http://localhost:8080/concentrador') {
		if (!($token = $ent->isAlfanumerico($_POST['token'], 64))) {
				echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;"><div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;</div></div><div style="text-align:center; margin-top:100px; font-family:sans serif; font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">Sesión terminada<br><a href="index.php">Volver</a></div>';
				$Session->Destroy();
				marcaIP($ip);
				exit();
		} else {
			if ($token != token()){
				echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;"><div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;</div></div><div style="text-align:center; margin-top:100px; font-family:sans serif; font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">Token no válido<br><a href="index.php">Volver</a></div>';
				$Session->Destroy();
				marcaIP($ip);
				exit();

			}
		}
	}

	$login = $ent->isAlfanumerico($_POST['login'], 32);
	$pag_salida = verifica_entrada($login, $contras);
	if (!$pag_salida) {
		echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;">
						<div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;
						</div>
					</div>
					<div style="text-align:center; margin-top:100px; font-family:sans serif;
					font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">
					Usuario / Contraseña desconocido<br><a href="index.php">Volver</a></div>';
		$Session->Destroy();
		marcaIP($ip);
		exit();

	} else regCoxData(); // registra los datos del usuario conectado
}


if (_ESTA_URL == 'http://localhost:8080/concentrador') $_SESSION['LoggedIn'] = true;

if (isset($_SESSION['LoggedIn'])) {
	$sessAnalisis = $Session->AnalyseFingerPrint($Analysis);
	if ( _ESTA_URL == 'http://localhost:8080/concentrador') {
		$sessAnalisis = true; 
		cambialog();
	}
	if  ($sessAnalisis === true ) {
		ini_set(SMTP, _CORREO_SERVIDOR_SMTP);
		$send_m = new sendmail();

		$dms=new dms_send;
		#Datos de acceso a la plataforma
// 		$dms->autentificacion->idcli='126560';
// 		$dms->autentificacion->username='amfglobalitems';
// 		$dms->autentificacion->passwd='Mario107';
		$dms->autentificacion->idcli='185529';
		$dms->autentificacion->username='Caribbeanonline';
		$dms->autentificacion->passwd='Amfn0v1e';

		if (!$_POST['password']) {
			$desc = '';
			foreach($_REQUEST as $nom => $valor) {
                if (is_array($valor)) $valor = implode (', ', $valor);
				$desc .= "$nom = ".htmlspecialchars($valor, ENT_QUOTES)." || ";
			}
		} else $desc = 'Entrada al sitio';
		if ($_SESSION['id'] > 0) {
			$query = "insert into tbl_baticora (idadmin, texto, fecha) values (".$_SESSION['id'].", '$desc', ".time().")";
			$temp->query($query);
		}
//		include_once('operini.php');

		if ($_SESSION['id']) {
//echo "pasa3"; exit;
			$query = "select param from tbl_admin where idadmin = {$_SESSION['id']}";
			$temp->query($query);
			$paramUser = $temp->loadResult();

			$param  = new mosParameters($paramUser);
			$_SESSION['idioma'] = $param->get('idioma', 'spanish');

			$query = "update tbl_admin set fecha_visita = ".time()." where idadmin = ".$_SESSION['id'];
			$temp->query($query);

		} else $_SESSION['idioma'] = 'spanish';

		include( "lang/{$_SESSION['idioma']}.php" );

		function hotel_put() {
			global $temp;
			global $ent;
		//			echo "entra";
			if (($comp = $ent->isAlfabeto($_GET['componente'], 13)) && ($pag = $ent->isAlfabeto($_GET['pag'], 13))) {
				$camino_busc = "componente/".$comp."/". $pag .".php";

				//actualiza la tabla cantAccesos
				$query = "select count(*) tot from tbl_cantAccesos c, tbl_menu m where idadmin = ".$_SESSION['id']." and m.id = c.idmenu and link like '%componente=".$comp."&pag=". $pag."%'" ;
				$temp->query($query);
//				echo $query."<br>";

				if ($temp->f('tot') > 0)
					$query = "update tbl_cantAccesos c, tbl_menu m set cant = cant + 1, fecha = ".time()."
								where idadmin = ".$_SESSION['id']." and m.id = c.idmenu and link like '%componente=".$comp."&pag=". $pag."%'" ;
				else
					$query = "insert into tbl_cantAccesos values (null, ".$_SESSION['id'].", (select id from tbl_menu where link like '%componente=".$comp."&pag=". $pag."'), 1, ".time().")";
//				$query = "update tbl_cantAccesos c, tbl_menu m set cant = cant + 1 where m.id = c.idmenu and link like '%componente=".$comp."&pag=". $pag."%'" ;
//				echo $query;
				$temp->query($query);

				include_once ($camino_busc);
			}
		}
//echo "pasa4"; exit;
		if (!$_SESSION['id']) include('template/login.php');
		else {
//echo "pasa5"; exit;
			if (strlen($pag_salida) < 5) include('template/index.php');
			else {
				if (strstr($pag_salida, 'inicio')) $pag_salida = 'componente=comercio&'.$pag_salida;
				$saltos = explode( '&', $pag_salida);
				for ($x = 0; $x < count($saltos); $x++) {
					$termino = explode( '=', $saltos[$x]);
					$_GET[$termino[0]] = $termino[1];
				}
				include('template/index.php');
			}
		}
	} else {
		$Session->Destroy();
		//include_once('operini.php');
		require_once( "lang/spanish.php" );
		include('template/login.php');
	}
} else {
//	$Session->Destroy();
//	include_once('operini.php');
	require_once( "lang/spanish.php" );
	include('template/login.php');
}

function cambialog(){
	$arrSess = json_decode('{"LoggedIn":true,"idioma":"spanish","_FingerPrint":"696136cfb5d10d73ea18674b14bffd2eeced9a9d1697d4b96f7f07a1b3df59ee","id":"10","admin_nom":"Jotate","comercio":"varios","comercioNom":null,"idcomStr":"47,1,2,3,44,45,42,4,5,6,7,48,8,9,43,46,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,40,41,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,96,97,98,99,100,101,102,106,107,108,109,110,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,172,173,174,175,176,177,178,179,180,181,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,216,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,270,271,272,273,274,275,276,277,278,279,280,281,282,283,284,285,286,287,288,289,290,291,292,293,294,295,296,297,298,299,300,301,302,303,304,38,39,49,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,384,385,386,387,388,389,390,391,392,393,394,395,396,397,398,399,400,401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,436,437,438,439,440,441,442,443,444,445,446,447,448,449,450,451,452,453,454,455,456,457,458,459,460,461,462,463,464,465,466,467,468,469,470,471,472,473,474,475,476,477,478,479,480,481,482,483,484,485,486,487,488,489,490,491,492,493,494,495,496,497,498,499,500,501,502,503,504,505,506,507,508,509,510,511,512,513,514,515,516,517,518,519,520,521,522,523,524,525,526,527,528,529,530,531,532,533,534,535,536,537,538,539,540,541,542,543,544,545,546,547,548,549,550,551,552,553,554,555,556,557,558,559,560,561,562,563,564,565,566,567,568,569,570,571,572,573,574,575,576,577,578,579,580,581,582,583,584,585,586,587,588,589,590,591,592,593,594,595,596,597,598,599,600,601,602,603,604,605,606,607,608,609,610,611,612,613,614,615,616,617,618,619,620,621,622,623,624,625,626,627,628,629,630,631,632,633,634,635,636,637,638,639,640,641,642,643,644,645,646,647,648,649,650,651,652,653,654,655,656,657,658,659,660,661,662,663,664,665,666,667,668,669,670,671,672,673,674,675,676,677,678,679,680,681,682,683,684,685,686,687,688,689,690,691,692,693,694,695,696,697,698,699,700,701,702,703,704,705,706,707,708,709,710,711,712,713,714,715,716,717,718,719,720,721,722,723,724","rol":"1","grupo_rol":"0","email":"serv.tecnico@bidaiondo.com","pasarelaAlMom":null,"sesionId":"e5d229eeccbb01ed19dba2cb41752aa5","usequery":"","cantdec":"2","sepdecim":".","sepmiles":",","formtfecha":"d\/m\/Y H:i:s","vendecuc":"0","reclamaciones":"1","TZ":"1","DST":"1","nonDST":"2"}');
	foreach ($arrSess as $key => $value) {
		$_SESSION[$key] = $value;
	}
	return;
}

// Debugger
if (_MOS_CONFIG_DEBUG) {
	echo '<div id="debD"><pre style="font-size:11px">';
	echo "<hr /><hr /><br>Querys:<br>";
	echo $temp->log;
	echo "<hr /><hr /><br>Datos:<br>";
//			var_dump(get_defined_vars());
	echo $database->_ticker . ' queries executed';
	// foreach ($database->_log as $k=>$sql) {
	// 	echo $k+1 . "\n" . $sql . '<hr />';
	// }
	echo "<hr /><hr /><br>Archivos:<br>";
	$archivos_incluidos = get_included_files();

	foreach ($archivos_incluidos as $nombre_archivo) {
		echo "$nombre_archivo\n";
	}
	echo "<hr /><hr /><br>Variables usadas:<br>";
	var_dump(array_keys(get_defined_vars()));
	echo "<hr /><hr /><br>Variables Session:<br>";
	// var_dump(array_keys($_SESSION));
	echo json_encode($_SESSION);
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

?>
