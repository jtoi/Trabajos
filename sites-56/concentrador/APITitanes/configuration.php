<?php
defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
// Datos de inicialización de PHP
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('default_charset', 'utf-8');
date_default_timezone_set('Europe/Madrid');

// Datos de conexión con la BD
$host='mariadb';
$user='root';
$pass='root';
$db='admincomercio_db';

//Datos de configuración del proyecto
define("_TIT_CONFIG_DEBUG", '1'); //1-permite la salva de los datos en el fichero salva.log
// define("_URL_ENVIO", 'http://pre.apifenix.grupotitanes.es'); //url de pruebas volátil, los datos Titanes los almacena por 24 horas
define ("_URL_ENVIO", 'http://dev.apifenix.grupotitanes.es'); //url de pruebas, los datos quedan almacenados en Titanes por el tiempo de pruebas
define("_ESTA_URL", "http://localhost/concentrador/APITitanes/index.php?var="); // url a la que se accederá para trabajar 

#Titanes deberá definir el que corresponda a nosotros estos datos son de ejemplo para pruebas
define("_CORRESPONDANT", "T070");
define("_SUBCORRESPONDANT", "7");
define("_BRANCH", "T0700939");
?>
