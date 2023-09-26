<?php
define('_VALID_ENTRADA', 1);
require_once( 'configuration.php' );
include 'include/mysqli.php';
include_once 'include/hoteles.func.php';
include_once 'admin/adminis.func.php';
$temp = new ps_DB;

$q = "select b.idcimex id, (select c.idcimex from tbl_aisClienteBeneficiario r, tbl_aisCliente c where r.idcliente = c.id and r.idbeneficiario = b.id order by r.fecha desc limit 0,1) 'idcliente', (select r.idrelacion from tbl_aisClienteBeneficiario r, tbl_aisCliente c where r.idcliente = c.id and r.idbeneficiario = b.id order by r.fecha desc limit 0,1) 'relacion', b.numDocumento ci, b.nombre, b.papellido, b.sapellido, b.direccion, b.ciudad, b.telf, b.idrazon 
from tbl_aisBeneficiario b
where (b.idtitanes = 0 or b.idtitanes = '') and b.idcimex not in (138004, 145366)
order by b.fecha desc
limit 0,10";
$temp->query($q);

$arrBen = $temp->loadAssocList();

for ($i=0; $i<count($arrBen); $i++){
	$Id = $arrBen[$i]['id'];
	$data = array(
		"Id"				=> $arrBen[$i]['id'],
		"IdCliente"			=> $arrBen[$i]['idcliente'],
		"Nombre"			=> $arrBen[$i]['nombre'],
		"Apellido1"			=> $arrBen[$i]['papellido'],
		"Apellido2"			=> $arrBen[$i]['sapellido'],
		"Phone"				=> $arrBen[$i]['telf'],
		"Address"			=> $arrBen[$i]['direccion'],
		"City"				=> $arrBen[$i]['ciudad'],
		"CI"				=> $arrBen[$i]['ci'],
		"Relation"			=> $arrBen[$i]['relacion'],
		"Reason"			=> $arrBen[$i]['idrazon']
	);
	
	$ch = curl_init(); //init curl
	$options = array(
			CURLOPT_USERAGENT			=> 'Mozilla/1.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0'
			,CURLOPT_URL 				=> 'https://www.administracomercios.com/datInscr.php'
			,CURLOPT_SSL_VERIFYHOST		=> false
			,CURLOPT_SSL_VERIFYPEER		=> false
			,CURLOPT_RETURNTRANSFER		=> true
			,CURLOPT_BINARYTRANSFER		=> true
			,CURLOPT_FOLLOWLOCATION		=> false
			,CURLOPT_POST				=> true
			,CURLOPT_POSTFIELDS			=> $data
	);
	
	foreach ($data as $key => $value) {
		error_log ("$key => $value");
	}
	curl_setopt_array($ch, $options);
	
	echo curl_exec($ch)."<br>";
	if (curl_error($ch)) error_log(curl_error($ch));

}


// Id=160687, enc=ASCII
// IdCliente=11288, enc=ASCII
// Nombre=YURITZA DE LOS MILAGROS, enc=ASCII
// Apellido1=HERNANDEZ, enc=ASCII
// Apellido2=MESA, enc=ASCII
// CI=82051606631, enc=ASCII
// Phone=+5353574955, enc=ASCII
// Address=+5353574955, enc=ASCII
// City=, enc=ASCII
// Relation=15, enc=ASCII
// Reason=2, enc=ASCII

?>