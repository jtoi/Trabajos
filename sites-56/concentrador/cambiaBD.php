<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define( '_VALID_ENTRADA', 1 );
require_once( 'admin/classes/entrada.php' );
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
$temp = new ps_DB;

//RELLENA LA TABLA tbl_colAdminComer
$q = "select idadmin, idcomercio from tbl_admin";
$temp->query($q);
$arrAdmin = $temp->loadAssocList();

$q = "select id from tbl_comercio";
$temp->query($q);
$arrComercio = $temp->loadResultArray();
//print_r($arrComercio);

for ($i=0;$i<count($arrAdmin);$i++){
	if ($arrAdmin[$i]['idcomercio'] == 'todos') {
		for($j=0;$j<count($arrComercio);$j++){
			$q  = "insert into tbl_colAdminComer values (".$arrAdmin[$i]['idadmin'].",".$arrComercio[$j].", ".time().")";
//			echo $q."\n";
			$temp->query($q);
		}
	} else {
		$q = "insert into tbl_colAdminComer values (".$arrAdmin[$i]['idadmin'].", (select id from tbl_comercio where idcomercio = '".$arrAdmin[$i]['idcomercio']."'), ".time().")";
//		echo $q."\n";
		$temp->query($q);
	}
}
echo "Terminado el llenado de la tbl_colAdminComer \n";
?>
