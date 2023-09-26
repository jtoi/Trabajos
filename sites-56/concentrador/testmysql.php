<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('_VALID_ENTRADA', 1);
require_once( 'configuration.php' );
require_once( 'include/mysqli.php' );
$temp = &new ps_DB();
$temp->_debug = true;
require_once 'admin/adminis.func.php';

//require_once( 'include/database.php' );
//$database = &new database($host, $user, $pass, $db, $table_prefix);
//require_once( 'include/ps_database.php' );
//$temp = new ps_DB;


$temp->query("select * from tbl_admin");
echo $temp->num_rows()."<br><br>";
//print_r($temp->loadRow());echo "<br><br>";
print_r($temp->loadResultArray());
//print_r($temp->loadResult());
	echo $temp->f('nombre')."<br>";
while ($temp->next_record()) {
}

$hoy = time();
$_SESSION['formtfecha'] = 'd/m/y h:i:s';
echo $hoy."<br>";
echo formatea_fecha($hoy);

?>