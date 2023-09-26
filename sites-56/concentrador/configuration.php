<?php
defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('default_charset', 'iso-8859-1');
ini_set('include_path', '/usr/local/lib/php');
date_default_timezone_set('Europe/Madrid');


$host='mariadb';
$user='root';
$pass='admin';
$db='admincomercio_db';

$GLOBALS['table_prefix'] ='tbl_';
$GLOBALS['titulo_sitio'] = '';
$GLOBALS['sitio_url'] = str_replace('index.php', '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
// echo $GLOBALS['sitio_url'];
$GLOBALS['sitio_pago'] = 'http://localhost:8080/concentrador';
$GLOBALS['idcli'] = '126560';
$GLOBALS['username'] = 'amfglobalitems';
$GLOBALS['passwd'] = 'Mario107';
$GLOBALS["carateres_latinos"] = array('&Aacute;'=>'Á', '&Eacute;'=>'É', '&Iacute;'=>'Í', '&Oacute;'=>'Ó', '&Uacute;'=>'Ú',
		'&aacute;'=>'á', '&eacute;'=>'é', '&iacute;'=>'í', '&oacute;'=>'ó', '&uacute;'=>'ú', '&Uuml;'=>'Ü', '&uuml;'=>'ü',
		'&Ntilde;'=>'Ñ', '&ntilde;'=>'ñ', '&Oslash;'=>'Ø', '&oslash;'=>'ø', '&frac14;'=>'¼', '&frac12;'=>'½', '&frac34;'=>'¾',
		'&pound;'=>'£', '&copy;'=>'©', '&yen;'=>'¥', '&reg;'=>'®', '&ordf;'=>'ª', '&sup2;'=>'²', '&brvbar;'=>'¦', '&sup3;'=>'³',
		'&laquo;'=>'«', '&sup1;'=>'¹', '&ordm;'=>'º', '&para;'=>'¶', '&acute;'=>'´', '&deg;'=>'°', '&plusmn;'=>'±',
		'&iquest;'=>'¿', '&cent;'=>'¢', '&nbsp;'=>' ', '&comma;'=>',', '&period;'=>'.' );

define (_CHROME_DEBUGGING, true);
define (_LLAVE_TITANES, 'b527bae683562a289d2f247aa937ca6704327b7f55e1cebc66664206b83417aaa56c116baa24084c6ab0d17742a594a9154101daf3bdb5c7364bf12b94de2240');
define (_MOS_CONFIG_DEBUG, '0');
define (_MOS_PHP_DEBUG, '0');
define (_PATH_SITIO, substr($_SERVER['SCRIPT_FILENAME'], 0, strripos($_SERVER['SCRIPT_FILENAME'], '/')+1));
define (_DIF_HOR, '6 ');
define (_DIR_TRAB, str_replace("/admin/index.php", "", $_SERVER['SCRIPT_FILENAME']));
define (_DIR_TRAB2, str_replace("/adminis/index.php", "", $_SERVER['SCRIPT_FILENAME']));
define (_DIRECT_ALCAN, "");
define (_ESTA_URL, 'http://localhost:8080/concentrador');
define (_CORREO_SITE, "jotate@amfconcentrador.com");
define (_URL_COMERCIO, "https://www.administracomercios.com/rep/llegada.php");
define (_URL_DIR, "https://www.administracomercios.com/rep/");
define (_CAMB_LIM, true);

define (_MESES_BACKUP, 14);

?>
