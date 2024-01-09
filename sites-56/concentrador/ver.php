<?php define('_VALID_ENTRADA', 1);
require_once( 'configuration.php' );
include 'include/mysqli.php';

$temp = new ps_DB;

if ($_GET['id'] > 0) {
	$q = "select fecha, traza from tbl_traza where id = '{$_GET['id']}'";
	
} else {
	$q = "select fecha, traza from tbl_traza order by fecha desc limit 0,1";
}


$temp->query($q);
echo date('Y-m-d H:i:s',$temp->f('fecha')) . " ";
echo "<br><br>".$temp->f('traza');

?>