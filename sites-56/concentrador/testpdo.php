<?php
define('_VALID_ENTRADA', 1);
require_once( 'configuration.php' );
require_once( 'class.pdo.php' );
$temp = &new pdos($host, $user, $pass, $db);

$temp->query("select * from tbl_admin");
echo $temp->num_rows()."<br><br>";
//echo $temp->row."<br>";
//echo $temp->f('idcomercio')."<br>";
//echo $temp->next_record();
//echo $temp->f('idcomercio')."<br>";
//echo $temp->row;
//echo $temp->f('nombre')."<br>";
//print_r($temp->results());
while ($temp->next_record()) {
	echo $temp->f('nombre')."<br>";
//	print_r($temp->loadRow());echo "<br><br>";
}
//print_r($temp->loadRow());
if ($temp->error()) echo $temp->error();

?>

