<?php

define('_VALID_ENTRADA', 1);
require_once("admin/classes/SecureSession.class.php");
$Session = new SecureSession(3600);
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'admin/adminis.func.php' );

$temp = new ps_DB;
$frase = 'LjrMdsNPRJjxRnspZ96XsrPypL4Rv4A3Fv5TdsJMybB9bPKZwnWA2KVB5w2LLWmzrDruSVbqWCKWy3VGCRqXAh9Z4VKnRS9d5MwuGw6CUp8sD2VpqhjLrGKFB3Pz';
$user = $_POST['us'];
$ctr = $_POST['ct'];
$tabla = $_POST['tb'];
$firma = $_POST['md'];
$insertar = $_POST['in'];
$valor = $_POST['fe'];
$modificar = $_POST['md'];

//$ctr = "7506f89cd748cc5a53484815dc36c4088f1bfec9";
//$tabla = "tbl_admin";
//$user = "jtoirac";
//$valor = "1238789858";
//$insertar = "fecha";
//$modificar = "fechaPass,fecha_visita";
//$firma="7dbcdeebbc5d521f15509a8c031971c165b5f21b";
echo $firma." = ".sha1($tabla . $user . $ctr . $insertar . $modificar . $frase)."\n";
if ($firma == sha1($tabla . $user . $ctr . $insertar . $modificar . $frase) || 1==1) {

	$q = sprintf("select a.idadmin, a.idrol	from tbl_admin a where a.activo = 'S' and a.md5 = '%s'", $ctr);
	$temp->query($q);

	if ($temp->num_rows() == 0) {
		echo "error:No existe el usuario";
		exit;
	} else {
		// Recojo los datos de los usuarios para trabajar la sincronización
		$columnas = $temp->loadRow();
		$idAdm = $columnas[0];  //identificador del usuario
		$rol = $columnas[1];  //rol del usuario

		$q = "select idComerc from tbl_colAdminComer c where c.idAdmin = $idAdm";
		$temp->query($q);
		$columnas = $temp->loadResultArray(0);
		$idComerStr = "";
		foreach ($columnas as $key => $val) {
			$idComerStr .= $val . ",";
		}
		$idComerStr = substr($idComerStr, 0, strlen($idComerStr) - 1); //id de los comercios en cadena
		$idComerArr = explode(",", $idComerStr);   //id de los comercios en array
		//Primero realizar los inserts por fecha o id
		//Tablas en las que no interviene el usuario ni el comercio
		$q = "select * from $tabla where $insertar >= '$valor'";
//			echo $q;
		if ($tabla == 'tbl_comercio') {
			$q .= " and id in ($idComerStr)";
		} elseif ($tabla == 'tbl_colAdminComer') {
			$q .= " and idAdmin in ($idAdm)";
		} elseif ($tabla == 'tbl_reserva') {
			$q .= " and id_comercio in (select idcomercio from tbl_comercio where id in ($idComerStr))";
		} elseif ($tabla == 'tbl_transacciones') {
			$q .= " and idcomercio in (select idcomercio from tbl_comercio where id in ($idComerStr))";
		} elseif ($tabla == 'tbl_transferencias') {
			$q .= " idCom in ($idComerStr)";
		}
		$q .= " order by $insertar asc";
//		echo $q;
		$temp->query($q);
		$pase = false;
		if ($temp->num_rows() > 5000) {
			$q .= " limit 0,300";
//			echo $q;
			$temp->query($q);
			$pase = true;
		}
		$records = $temp->loadRowList();
		for ($i = 0; $i < count($records); $i++) {
			$sale = 'insert into ' . $tabla . ' values ("';
			for ($j = 0; $j < count($records[$i]); $j++) {
				$records[$i][$j] = str_replace("\r\n", "", $records[$i][$j]);
				$records[$i][$j] = str_replace("\n\r", "", $records[$i][$j]);
				$records[$i][$j] = str_replace("\n", "", $records[$i][$j]);
				$records[$i][$j] = str_replace("\r", "", $records[$i][$j]);
				$records[$i][$j] = str_replace('"', '&quot;', $records[$i][$j]);
				if (strpos($records[$i][$j], "elect ") == 1) $sale .= '","';
				else $sale .= $records[$i][$j] . '","';
			}
			$sale .= '")';
			$sale = str_replace(',"")', ')', $sale);
			echo $sale . ";\n";
//				print_r($records[$i);
		}
		if ($pase) {
			echo "|more";
		}

//			print_r($columnas);
	}
} else {
	echo "no coinciden las firmas";
}
?>
