<?php
// Rellena la tabla tbl_rotComPasWeb con los datos de pasarela que 
// tiene el comercio en la tabla tbl_comercio
define('_VALID_ENTRADA', 1);

require_once( 'configuration.php' );
include 'include/mysqli.php';


$temp = new ps_DB;

$q = "select id, pasarela from tbl_comercio where activo = 'S'";
$temp->query($q);
$arrPas = $temp->loadRowList();

// var_dump($arrPas);

for ($i = 0; $i < count($arrPas); $i++) {
	if (strlen($arrPas[$i][1]) != 0 ){
		$arrS = explode(',', $arrPas[$i][1]);
		for ($j = 0; $j < count($arrS); $j++) {
			$temp->query("delete from tbl_rotComPasWeb where idcom = '".$arrPas[$i][0]."'");
			$q = "insert into tbl_rotComPasWeb (idcom, idpasarela, horas, orden, fecha) values ('".$arrPas[$i][0]."', '".$arrS[$j]."', '0', '".($j + 1)."', unix_timestamp())";
			$temp->query($q);
			echo $q."<br> ";
		}
	}
}
?>