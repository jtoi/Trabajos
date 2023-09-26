<?php
/*
 * Lanza las operaciones de Fincimex a Titanes cada 5 min
 */
define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';
include_once( 'admin/adminis.func.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'include/correo.php' );

$temp = new ps_DB ();
$correo = new correo;



function borraEso ($idcliente) {
	global $temp;
	$dirlocal = str_replace('borraficViejos.php', '',$_SERVER[SCRIPT_FILENAME])."ficTitan";
	// $dirlocal = "/var/www/vhosts/administracomercios.com/httpdocs/ficTitan";
	// if ($_SERVER['HTTP_HOST'] == 'localhost') 
	// 	$dirlocal = "/home/julio/www/concentrador/ficTitan";
	$correoMi = '';

	$temp->query("select usuario from tbl_aisCliente where id = $idcliente");
	echo "select usuario from tbl_aisCliente where id = $idcliente"."<br>";
	$dirlocal .= "/".$temp->f('usuario');
	echo $dirlocal."<br>";

	$temp->query("select fichero, id from tbl_aisFicheros where idcliente = $idcliente");
	$arrFics = $temp->loadAssocList();

		var_dump($arrFics);

	for ($i = 0; $i < count($arrFics); $i++) {
		$dirlocUsr = $dirlocal."/".$arrFics[$i]['fichero'];
		if (!is_file($dirlocUsr)) $temp->query("delete from tbl_aisFicheros where id = ".$arrFics[$i]['id']);
	}
}

?>