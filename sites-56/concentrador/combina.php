<?php
/*
 * Esta página actualiza la tabla cantAccesos con la cantidsad de accesos que ha tenido cada
 * usuario según está reportado en la tabla baticora
*/

define( '_VALID_ENTRADA', 1 );
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );

$temp = new ps_DB;

$query = "select idadmin, idrol, nombre from tbl_admin";
//echo $query."<br>";
$temp->query($query);
$arrTodos = $temp->loadAssocList();

//print_r($arrTodos);

foreach ($arrTodos as $usuarios) {
//	echo $usuarios['nombre']."<br>";
	$query = "select link, a.idmenu from tbl_menu m, tbl_accesos a where a.idmenu = m.id and idrol = ".$usuarios['idrol'];
//	echo $query."<br>";
	$temp->query($query);
	$arrPaginas = $temp->loadAssocList();
	
	foreach ($arrPaginas as $pagina) {
		$pag = str_replace('index.php?', "", $pagina['link']);
		$pag = str_replace('componente=', "componente = ", $pag);
		$pag = str_replace('&', " || ", $pag);
		$pag = str_replace('pag=', "pag = ", $pag);
		
		$query = "select count(*) tot from tbl_cantAccesos where idadmin = ".$usuarios['idadmin']." and idmenu = ".$pagina['idmenu'];
//		echo $query."<br>";
		$temp->query($query);
		
		if ($temp->f('tot') == 0) {
			$query = "insert into tbl_cantAccesos values (null, ".$usuarios['idadmin'].", ".$pagina['idmenu'].", (
								select count(*) total from tbl_baticora where texto like '%$pag%' and idadmin = ".$usuarios['idadmin']."))";
//			echo $query."<br>";
			$temp->query($query);
		} else {
			$query = "update tbl_cantAccesos set cant = (
								select count(*) total from tbl_baticora where texto like '%$pag%' and idadmin = ".$usuarios['idadmin'].")
								where idadmin = ".$usuarios['idadmin']." and idmenu = ".$pagina['idmenu'];
//			echo $query."<br>";
			$temp->query($query);
		}
	}
//	echo"<br><br>";
}
//echo"<br>";
?>