<?php
define('_VALID_ENTRADA', 1);
if (!session_start())
	session_start();
require_once( 'configuration.php' );
include 'include/mysqli.php';

$options = array(
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_SSL_VERIFYPEER	=> false,
		CURLOPT_POST			=> false,
		CURLOPT_VERBOSE			=> true,
		CURLOPT_URL				=> 'https://portal4.lacaixa.es/apl/divisas/index_es.html'
);
$ch = curl_init();
curl_setopt_array($ch , $options);
$output = curl_exec($ch);
curl_close($ch);

if (curl_errno($ch)) {
	echo curl_strerror(curl_errno($ch))."<br>\n";
	echo curl_strerror(curl_error($ch))."<br>\n";
}
$handle = fopen('date.txt', 'a');

$curl_info = curl_getinfo($ch);
	
$arr1 = explode('JSESSIONID=',$output);
$arr1 = explode('">', $arr1[1]);

$options = array(
	CURLOPT_RETURNTRANSFER	=> true,
	CURLOPT_SSL_VERIFYPEER	=> false,
	CURLOPT_POST			=> false,
	CURLOPT_VERBOSE			=> true,
	CURLOPT_URL				=> 'https://portal4.lacaixa.es/apl/divisas/verTodos_es.html?JSESSIONID='.$arr1[0]
);
$ch = curl_init();
curl_setopt_array($ch , $options);
$output = curl_exec($ch);

if (curl_errno($ch)) {
	echo curl_strerror(curl_errno($ch))."<br>\n";
	echo curl_strerror(curl_error($ch))."<br>\n";
}

$curl_info = curl_getinfo($ch);

$arr1 = preg_split('<table border="0">',$output);
$arr1 = preg_split('</table>', $arr1[1]);
$tabla = nl2br($arr1[0]);
$tabla = preg_replace( "/\r|\n/", "|", $arr1[0]);

$arrVals = array('USD', 'CAD', 'GBP');

foreach ($arrVals as $moneda) {
	$paso = explode('<tr>|<td>'.$moneda, $tabla);
	$paso = explode('</tr>', $paso[1]);
	$paso = str_replace('<td>', '', str_replace('</td>','', $paso[0]));
	$val = explode('|', $paso);
	fwrite($handle, $moneda." = ".$val[3]."\n");
}
fclose($handle);
?>