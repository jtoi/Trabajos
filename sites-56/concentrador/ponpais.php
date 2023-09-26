<?php

/*
 * To change this template, choose Tools | Templates
* and open the template in the editor.
*/
define( '_VALID_ENTRADA', 1 );
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );

$temp = new ps_DB;
$temp2 = new ps_DB;

$q = "select idtransaccion, ip from tbl_transacciones where (ip != '127.0.0.1' or ip != null) and idpais is null";
$temp->query($q);
echo "comenzando...";
while ($temp->next_record()) {
// 	$q = "update tbl_transacciones set pais = '".geoip_country_name_by_name($temp->f('ip')). "', paisCod = '".geoip_country_code3_by_name($temp->f('ip')).
// 			"' where idtransaccion = '".$temp->f('idtransaccion')."' and pais is null ";

	$q = "select id from tbl_paises where iso = '".geoip_country_code3_by_name($temp->f('ip'))."'";
	$temp2->query($q);
	$id = $temp2->f('id');
	if ($id > 0) {
		$q = "update tbl_transacciones set idpais = $id where ip = '".$temp->f('ip')."'";
		$temp2->query($q);
	} else {
		$correoMi = "La ip ".$temp->f('ip')." no tiene país asociado, hablar con con K para que actualice el módulo geoip.";
		correoAMi('IP que no resuelve', $correoMi);
		echo $correoMi;
	}
	
	
//	$q = "update tbl_transacciones set idpais = (select id from tbl_paises where nombre = '".geoip_country_name_by_name($temp->f('ip'))."')";
//	echo $q."<br />";
// 	$temp2->query($q);
}
echo "yap";

?>
