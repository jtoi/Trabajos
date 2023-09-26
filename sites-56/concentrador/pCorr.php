<?php
define( '_VALID_ENTRADA', 1 );
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'include/correo.php';
$corCreo = new correo();

//$corCreo->to('panchp@mailinator.com');
echo "va".$corCreo->todo(10,'Prueba','Correo de prueba')."va";
?>