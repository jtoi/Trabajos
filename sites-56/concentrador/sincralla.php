<?php

/*
 * Para sincronizar dispositivos recibe usuario contraseña y fecha de la última actualización
 * devuelve las querys necesarias para tener las bases de datos actualizadas
 */
define( '_VALID_ENTRADA', 1 );
require_once("admin/classes/SecureSession.class.php");
$Session = new SecureSession(3600);
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'admin/adminis.func.php' );
//echo (suggestPassword(125));

$temp = new ps_DB;

$user = $_POST['rio'];
$ctr = $_POST['ctr'];
$md = $_POST['md'];
$str = $_POST['str'];
$modo = $_POST['mod'];
$filenameZip = "desc/SINR".date ("YmdHis").".sql.gz";
$filenameZip = "desc/actualiza.gz";
/***********************************/
//$user = "jtoirac";
//$ctr = "Santaemilia453";
//$md = "1d86c3a8cf76710570d211536190f110b6ccd157";
//$str = '[{"tabla":"tbl_accesos","val":{"ins":"217","mod":0}},{"tabla":"tbl_admin","val":{"ins":"287","mod":"1378381075"}},{"tabla":"tbl_amadeus","val":{"ins":"30109","mod":"1378380817"}},{"tabla":"tbl_baticora","val":{"ins":"90155","mod":0}},{"tabla":"tbl_cambio","val":{"ins":"3178","mod":0}},{"tabla":"tbl_cantAccesos","val":{"ins":"593","mod":"1378246061"}},{"tabla":"tbl_cobroTarjeta","val":{"ins":"464","mod":0}},{"tabla":"tbl_colAdminComer","val":{"ins":"747","mod":0}},{"tabla":"tbl_comercio","val":{"ins":"68","mod":"1378147083"}},{"tabla":"tbl_errores","val":{"ins":"313","mod":0}},{"tabla":"tbl_ipBL","val":{"ins":"967","mod":0}},{"tabla":"tbl_ipblancas","val":{"ins":"7","mod":0}},{"tabla":"tbl_ipbloq","val":{"ins":"2499","mod":"1378235338"}},{"tabla":"tbl_listaIp","val":{"ins":"5263","mod":0}},{"tabla":"tbl_mensajes","val":{"ins":"44","mod":"1372802400"}},{"tabla":"tbl_menu","val":{"ins":"45","mod":0}},{"tabla":"tbl_moneda","val":{"ins":"978","mod":0}},{"tabla":"tbl_nivelAmenaza","val":{"ins":"2","mod":0}},{"tabla":"tbl_paises","val":{"ins":"310","mod":0}},{"tabla":"tbl_pasarela","val":{"ins":0,"mod":0}},{"tabla":"tbl_reserva","val":{"ins":"48474","mod":"1378267093"}},{"tabla":"tbl_roles","val":{"ins":"15","mod":0}},{"tabla":"tbl_setup","val":{"ins":"26","mod":"1376280030"}},{"tabla":"tbl_sincr","val":{"ins":0,"mod":0}},{"tabla":"tbl_ticket","val":{"ins":"187","mod":"1378245662"}},{"tabla":"tbl_transacciones","val":{"ins":"1378267009","mod":"1378267093"}},{"tabla":"tbl_transferencias","val":{"ins":"112","mod":"1377701301"}}]';
//$modo = "1";
/**********************************/
$frase = 'LjrMdsNPRJjxRnspZ96XsrPypL4Rv4A3Fv5TdsJMybB9bPKZwnWA2KVB5w2LLWmzrDruSVbqWCKWy3VGCRqXAh9Z4VKnRS9d5MwuGw6CUp8sD2VpqhjLrGKFB3Pz';
//print_r($_POST);exit;

$horaCheq = 1349668800;
$arrJson = array();
$tabla = '';

//borro ficheros viejos de más de 15 min
$dir = "desc/";
if ($dh = opendir($dir)) {
	while (($file = readdir($dh)) !== false) {
		if (is_file($dir.$file) && (filemtime($dir . $file) < time()-15*60))
			unlink($dir.$file);
	}
	closedir($dh);
}
//echo "fichero=".$dir.$file;

if ($md == sha1($user.$ctr.$str.$frase) //|| 1==1
		) {
	
	verifica_entrada($user, $ctr);
//	echo "entra";
	if (isset($_SESSION['id'])) {
//		inserto la sincronización en la tbl_baticora
		$query = "insert into tbl_baticora (idadmin, texto, fecha) values (".$_SESSION['id'].", 'Sincronizando..', ".time().")";
		$temp->query($query);
		
//		despejo todos los datos enviados
		$arrDatEnv = json_decode($str);
//		print_r($arrDatEnv);

		//trato todos los datos enviados excepto el último que es la última fecha de modificación
		//es decir proceso todas las tablas que se actualizan por ids o por cantidad
		for ($i = 0;$i<count($arrDatEnv); $i++) {
//			$arrCmpo = $arrDatEnv[$i];
			$campoCheq = '';
			$tabla = $arrDatEnv[$i]->tabla;
			$ins = $arrDatEnv[$i]->val->ins;
			$mod = $arrDatEnv[$i]->val->mod;
//			echo $tabla." - ".$ins;
			
//			determino si el campo es un identificador o una cantidad leyendo el valor en la tabla sincr
			$q = "select insertar, modificar, identif from tbl_sincr where tabla = '$tabla'";
			$temp->query($q);
			$id = $temp->f('insertar');
			$mode = $temp->f('modificar');
//echo "<br>".$tabla;

			// si en la tbla sincr hay para insertar busca los nuevos inserts
			if (strlen($id) > 0 && $ins > 0) {
				$q = "select * from $tabla where $id > $ins";
//			echo "$q<br>";
				$temp->query($q);
				$arrResult = $temp->loadRowList();
				
//				si hay actualizaciones
				if (count($arrResult) > 0) {
					for ($h = 0; $h < count($arrResult); $h++) {
						$sale .= "insert into $tabla values ";
						
						$arrItem = array();
						foreach($arrResult[$h] as $item) {
							if (strpos($item,"elect ") == 1) $arrItem[] = "";
							else $arrItem[] = $item;
						}
						$strResult = implode("','", $arrItem);
						$sale .= "('{$strResult}');\n";
					}
					$sale = str_replace(");(", "),(", $sale);
				}
				
			}
			
//			verifica si hay modificaciones en la tabla
			if (strlen($mode) > 0 && $mod > 0) {// si en la tbla sincr hay para modificar busca los nuevos records modificados
//				echo " - ".$mode;
				$arrMod = explode(",", $mode);
				$q = "select * from $tabla where ";
				foreach($arrMod as $item) {
					$q .= "$item > $mod or ";
				}
				$q = rtrim($q, " or ");
//				echo $q;
				$temp->query($q);
				$arrResult = $temp->loadRowList();
//					print_r($arrResult);
				
				//si hay algún campo en la tabla que se debe modificar:
				if (count($arrResult) > 0) {
//					obtiene los campos de la tabla
					$q = "show columns from $tabla";
//			echo "$q<br>";
					$temp->query($q);
					$campos = $temp->loadResultArray();
					$strCampos = implode(",", $campos);
//					print_r($campos);
					
					for ($m=0;$m<count($arrResult);$m++) {
						$ve = "update $tabla set ";
						for ($h = 0; $h < count($campos); $h++) {
							if ($campos[$h] == $id) $we = " where ".$campos[$h]." = '". $arrResult[$m][$h] ."' ";
							else $ve .= $campos[$h] ." = '". $arrResult[$m][$h] ."', ";
						}
						$sale .= rtrim($ve, ", ").$we.";\n";
//						echo "<br><br>".$sale;exit;
					}
				}
			}
		}
					if ($modo == 1) {
						file_put_contents("compress.zlib://$filenameZip", str_replace(",;", ";", $sale));
					} else echo str_replace(",;", ";", $sale);
//		echo "modo=".$modo;
		if ($modo == 1) echo "<a href='$filenameZip'>$filenameZip</a>";
	}
}


?>
