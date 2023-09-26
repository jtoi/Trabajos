<?php

/*
 * Permite sincronizar la base de datos local con las entradas realizadas en
 * la Base de datos del sitio en internet.
 */
$start = microtime(1);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);

define( '_VALID_ENTRADA', 1 );
require_once( 'configuration.php' );
include 'include/mysqli.php';

$temp = new ps_DB;

$sql = "select * from tbl_sincr";
$temp->query($sql);
$salida = $temp->loadAssocList();
//$fechaHoraIns = 0;
//$fechaHoraMod = 0;
$mos = 1; //1-env�o por formulario, 0-env�o por curl
$url = "https://www.administracomercios.com/sincralla.php";
$url = "http://localhost/concentrador/sincralla.php";
$login = "jtoirac";
$contras = "Santaemilia453";
$frase = 'LjrMdsNPRJjxRnspZ96XsrPypL4Rv4A3Fv5TdsJMybB9bPKZwnWA2KVB5w2LLWmzrDruSVbqWCKWy3VGCRqXAh9Z4VKnRS9d5MwuGw6CUp8sD2VpqhjLrGKFB3Pz';
$arrSal = array();

//print_r($salida);exit;

//Determino para cada tabla los valores reales que tienen los campos guia de la sincronización
//lo escribo en la tabla sincr local y envío los datos a internet
for ($i=0;$i<count($salida);$i++) {
	$tabla = $salida[$i]['tabla'];
	$strColins = $salida[$i]['insertar'];
	$strColmod = $salida[$i]['modificar'];
//	$identif = $salida[$i]['identif'];
	$ins = $mod = 0;
	
//	print_r($salida[$i]);
	//para cada tipo de campo guía
	if (strlen($strColins) > 0 || strlen($strColmod) > 0) {
		if (strlen($strColins) > 0) {// tiene campo de insertar
			$q = "select max($strColins) id from $tabla";
//			echo $q;
			$temp->query($q);
			$ins = $temp->f('id');
		}
		if (strlen($strColmod) > 0 ) {// campo guía es de fecha
			//para cada columna busco el valor mayor
			$arrCol = explode(",", $strColmod);
			if (count($arrCol) > 1) {
				$q = "select greatest (";
				foreach ($arrCol as $columna) {
					$q .= "(select $columna from $tabla order by $columna desc limit 0,1)";
				}
				$q .= ") val";
				$q = str_replace(")(", "),(", $q);
			} else {
				$q = "select max($strColmod) val from $tabla";
			}
//			echo $q."\n";
			$temp->query($q);
//			echo $tabla." - ".$temp->f('fechaHora');
			$mod = $temp->f('val');
		}
	}
	$arrSal[] = array("tabla"=>$tabla,"val"=>array("ins"=>$ins,"mod"=>$mod));
//	echo "<br>";
}

//($fechaHoraIns > $fechaHoraMod) ? $strsal .= "fechaHora=".$fechaHoraIns : $strsal .= "fechaHora=".$fechaHoraMod;

//echo $strsal;
//print_r($arrSal);
$strsal = json_encode($arrSal);

$calc_md5 = sha1($login.$contras.$strsal.$frase);
echo "rio=$login&ctr=$contras&md=$calc_md5&str=$strsal&mod=$mos";
echo "exite-".function_exists('curl_version');
if (function_exists('curl_version') && $mos == 0) { //está la funcion curl
	echo "entre";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "rio=$login&ctr=$contras&md=$calc_md5&str=$strsal&mod=$mos");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0(Windows;U;WindowsNT5.1;ru;rv:1.9.0.4)Gecko/2008102920AdCentriaIM/1.7Firefox/3.0.4");
	
	$somecontent =  curl_exec ($ch);
	curl_close ($ch);
	$filename = 'actualBD'.date('YmdHi').'.sql';

	file_put_contents($filename, $somecontent);
	
	$temp->query($somecontent);
	
} else {
	echo "<form name='adm' action='$url' method='post'>
	<input type='hidden' value='$login' name='rio' />
	<input type='hidden' value='$contras' name='ctr' />
	<input type='hidden' value='$calc_md5' name='md' />
	<input type='hidden' value='$strsal' name='str' />
	<input type='hidden' value='$mos' name='mod' />
	<input type='submit' value='submit' />
	</form>
	<script type='text/javascript'>
		//document.adm.submit();
	</script>";
}


$end = microtime(1);
echo "<br><br>".($end - $start);
?>
