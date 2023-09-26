<?php
define('_VALID_ENTRADA', 1);
include_once( 'configuration.php' );
require_once( 'include/mysqli.php' );
$temp = &new ps_DB();
require_once 'include/correo.php';
$doc = new DOMDocument('1.0');
$doc->formatOutput = true;

$delimiter = '';
if ($_REQUEST['dia']) {
	$dia = substr($_REQUEST['dia'], 0, 8);
	if (strstr($dia, '/')) $dia = str_replace('/', '', $dia);
	elseif (strstr($dia, '-')) $dia = str_replace('-', '', $dia);
	if (strlen($dia) == 6){
		$d = substr($dia, 0, 2);
		$m = substr($dia, 2, 2);
		$y = substr($dia, 4, 2);
		$fec = existe($d, $m, $y);
	} elseif (strlen($dia) == 8) {
		$d = substr($dia, 0, 2);
		$m = substr($dia, 2, 2);
		$y = substr(substr($dia, 4, 4), 2, 2);
		$fec = existe($d, $m, $y);
	} else exit;
	
} else {
	$fec = date('dmy');
	$d = date('d');
	$m = date('m');
	$y = date('y');
}

if (!isset($fec)) exit();

$q = "select moneda, greatest(visa, bce, bnc, xe) cambio from tbl_cambio where from_unixtime(fecha, '%d%m%y') = '$fec' and moneda != 'CUC'";
$temp->query($q);
// echo $temp->_sql;

$arrT = $temp->loadRowList();
// print_r($arrT);

$fecha = $doc->createElement('fecha');
$fecha = $doc->appendChild($fecha);
$fecha->setAttribute('day', "$d/$m/$y");

$root = $doc->createElement('tasas');
$root = $doc->appendChild($root);

foreach ($arrT as $item){
	$title = $doc->createElement('moneda');
	$title = $root->appendChild($title);
	$text = $doc->createTextNode($item[1]);
	$text = $title->appendChild($text);
	$title->setAttribute('denominacion', $item[0]);
}

echo $doc->saveXML();


function existe($d, $m, $y) {
	if ($d>=1 && $d<32) true; else exit;
	if ($m>=1 && $m<13) true; else exit;
	if ($y>=1 && $y<=date('y')) true; else exit;
	return $d.$m.$y;
}

?>