<?php
defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('default_charset', 'iso-8859-1');
date_default_timezone_set('Europe/Madrid');

//$host='87.106.63.238';
$host='localhost';
$user='concenamf_usr';
$pass='AW7dtwmmFCmUvnAh';
$db='concentramf_db';

$GLOBALS['table_prefix'] ='tbl_';
$GLOBALS['titulo_sitio'] = '';
$GLOBALS['sitio_url'] = str_replace('index.php', '', 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
$GLOBALS['sitio_pago'] = 'https://www.administracomercios.com/';
$GLOBALS['idcli'] = '126560';
$GLOBALS['username'] = 'amfglobalitems';
$GLOBALS['passwd'] = 'Mario107';
$GLOBALS["carateres_latinos"] = array('&Aacute;'=>'Á', '&Eacute;'=>'É', '&Iacute;'=>'Í', '&Oacute;'=>'Ó', '&Uacute;'=>'Ú', 
		'&aacute;'=>'á', '&eacute;'=>'é', '&iacute;'=>'í', '&oacute;'=>'ó', '&uacute;'=>'ú', '&Uuml;'=>'Ü', '&uuml;'=>'ü', 
		'&Ntilde;'=>'Ñ', '&ntilde;'=>'ñ', '&Oslash;'=>'Ø', '&oslash;'=>'ø', '&frac14;'=>'OE', '&frac12;'=>'oe', '&frac34;'=>'?', 
		'&pound;'=>'£', '&copy;'=>'©', '&yen;'=>'¥', '&reg;'=>'®', '&ordf;'=>'ª', '&sup2;'=>'²', '&brvbar;'=>'?', '&sup3;'=>'³', 
		'&laquo;'=>'«', '&sup1;'=>'¹', '&ordm;'=>'º', '&para;'=>'¶', '&acute;'=>'?', '&deg;'=>'°', '&plusmn;'=>'±', 
		'&iquest;'=>'¿', '&cent;'=>'¢', '&nbsp;'=>' ', '&comma;'=>',', '&period;'=>'.' );
//60*60*5
define (_TIEMPOSES, '18000'); //5 horas

define (_PATH_SITIO, substr($_SERVER['SCRIPT_FILENAME'], 0, strripos($_SERVER['SCRIPT_FILENAME'], '/')+1));
define (_MOS_CONFIG_DEBUG, '0');
define (_LLAVE_TITANES, 'b527bae683562a289d2f247aa937ca6704327b7f55e1cebc66664206b83417aaa56c116baa24084c6ab0d17742a594a9154101daf3bdb5c7364bf12b94de2240');
define (_MOS_PHP_DEBUG, '0');
define (_DIF_HOR, '5');
define (_DIR_TRAB, str_replace("/admin/index.php", "", $_SERVER['SCRIPT_FILENAME']));
define (_DIR_TRAB2, str_replace("/adminis/index.php", "", $_SERVER['SCRIPT_FILENAME']));
define (_DIRECT_ALCAN, "");
define (_ESTA_URL, 'https://www.administracomercios.com');
define (_CORREO_SITE, "jotate@amfconcentrador.com");
define (_CLAVE_EVO, "T21RAFBM");//pruebas
define (_PALABR_OFUS, "47;57;5C;35;25;50;5C;2F;72;7D;05;70;02;03;75;73;79;1A;6C;1A");
define (_CONTRASENA_OFUS, "santaemi");
define (_ID_COMERCIO, "B9550206800001");
define (_ID_PTO, "999999");
define (_3DPALABR_OFUS, "45;52;5C;4C;57;23;5C;5B;71;0A;70;02;72;77;03;07;7A;1B;1A;6F");
define (_3DCONTRASENA_OFUS, "santaemi");
define (_3DID_COMERCIO, "B9550206800004");
define (_3DID_PTO, "999999");
define (_3DOPALABR_OFUS, "47;24;2C;30;20;5C;2E;2D;60;05;76;05;77;75;74;04;04;6B;1F;68");
define (_3DOCONTRASENA_OFUS, "santaemi");
define (_3DOID_COMERCIO, "P200211700001");
define (_3DOID_PTO, "999999");
define (_MEXPALABR_OFUS, "45;57;5D;40;20;57;5F;2D;03;08;06;04;76;00;76;06;7D;18;6F;6F");
define (_MEXCONTRASENA_OFUS, "santaemi");
define (_MEXID_COMERCIO, "B9550206800005");
define (_MEXID_PTO, "999999");
define (_3BBVAPALABR_OFUS, "47;54;28;30;53;5D;5A;2D;72;0C;00;71;04;77;04;07;7D;6F;6E;1D");
define (_3BBVACONTRASENA_OFUS, "santaemi");
define (_3BBVAID_COMERCIO, "B9550206800006");
define (_3BBVAID_PTO, "999999");
define (_4BBVAPALABR_OFUS, "47;57;56;36;27;54;55;28;06;08;05;04;01;76;06;07;0F;1A;6E;12");
define (_4BBVACONTRASENA_OFUS, "santaemi");
define (_4BBVAID_COMERCIO, "B9550206800007");
define (_4BBVAID_PTO, "999999");
define (_5BBVAPALABR_OFUS, "42;50;2D;45;52;5D;5D;5E;01;7B;71;70;03;71;00;00;0E;13;68;13");
define (_5BBVACONTRASENA_OFUS, "santaemi");
define (_5BBVAID_COMERCIO, "B9550206800008");
define (_5BBVAID_PTO, "999999");
//BBVA9 3D
define (_9BBVAPALABR_OFUS, "41;52;5E;42;20;50;5D;2D;72;00;70;06;06;06;00;70;7C;1A;6C;13");
define (_9BBVACONTRASENA_OFUS, "santaemi");
define (_9BBVAID_COMERCIO, "B9550206800009");
define (_9BBVAID_PTO, "999999");
//BBVA10 3D
define (_10BBVAPALABR_OFUS, "44;50;2A;43;53;55;5E;28;76;08;77;70;71;0A;01;72;0D;18;19;68");
define (_10BBVACONTRASENA_OFUS, "santaemi");
define (_10BBVAID_COMERCIO, "B9550206800010");
define (_10BBVAID_PTO, "999999");
//Prueba
define (_TESTPALABR_OFUS_TEST, "43;52;28;35;22;57;5A;28;7B;09;01;03;74;70;73;04;79;13;1C;1D");
define (_TESTCONTRASENA_OFUS_TEST, "santaemi");
define (_TESTID_COMERCIO_TEST, "B9550206800002");
define (_TESTID_PTO_TEST, "999999");
define (_SABADEL_URL_PROD, "https://sis.sermepa.es/sis/realizarPago");
define (_SABADEL_CLAVE_PROD, "987rt98w6he98r6t7985");
define (_SABADEL_URL_DESA, "https://sis-t.sermepa.es:25443/sis/realizarPago");
define (_SABADEL_CLAVE_DESA, "qwertyasdf0123456789");
define (_LOCALIZADOR, "234623452343");
define (_URL_COMERCIO, "https://www.administracomercios.com/rep/llegada.php");
define (_URL_DIR, "https://www.administracomercios.com/rep/");
define (_URL_TPV, "https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_RecepOpModeloServidor");
define (_BANESTO_URL_DESA, "https://tpv2.4b.es/simulador/teargral.exe");
define (_BANESTO_URL_PROD, "https://tpv.4b.es/tpvv/teargral.exe");
define (_BANESTO_CLAV_COMER, "PI00014587");
//Caja Madrid
define (_CAJAMADRID_URL_DESA, "https://sis-t.sermepa.es:25443/sis/realizarPago");
define (_CAJAMADRID_CLAVE_DESA, "qwertyasdf0123456789");
define (_CAJAMADRID_CLAVE_PROD, "987rt98w6he98r6t7985");
define (_CAJAMADRID_URL_PROD, "https://sis-t.sermepa.es:25443/sis/realizarPago");

define (_MESES_BACKUP, 14);

?>
