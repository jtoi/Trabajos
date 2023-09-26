<?php
defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
error_reporting(E_ALL & ~E_NOTICE);

ini_set('default_charset', 'utf-8');
ini_set('include_path', '/usr/share/php');
date_default_timezone_set('UTC');


$host='mariadb';
$user='root';
$pass='admin';
$db='arte_db';

define(_MOS_CONFIG_DEBUG, 1);
// $clienteEnt = 'dddddd';
// $correoEnt = 'jtoirac@gmail.com'



?>
