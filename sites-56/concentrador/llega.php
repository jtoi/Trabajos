<?php
define( '_VALID_ENTRADA','1' );
include 'configuration.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

print_r($_REQUEST);
echo "<br /><br />";
	$d				= $_REQUEST;
	$json			= json_encode($d);
	$comercio		= $d['MerchantID'];
	$AcquirerBIN	= $d['AcquirerBIN'];
	$TerminalID		= $d['TerminalID'];
	$idtrans		= $d['Num_operacion '];
	$importe		= $d['Importe '];
	$TipoMoneda		= $d['TipoMoneda '];
	$Exponente		= $d['Exponente'];
	$Referencia		= $d['Referencia '];
	$firma			= $d['Firma '];
	$Num_aut		= $d['Num_aut'];
	$Idioma			= $d['Idioma'];
	$Pais			= $d['Pais'];
	$Descripcion	= $d['Descripcion'];
	$clave			= _CLAVE_EVO;

//	$comercio		= "054252135";
//	$AcquirerBIN	= "0000554026";
//	$TerminalID		= "00000003";
//	$idtrans		= "031122161441";
//	$importe		= "100";
//	$TipoMoneda		= "978";
//	$Exponente		= "2";
//	$Referencia		= "120011172612112216161606007000";


	$sha1 = sha1($clave.$comercio.$AcquirerBIN.$TerminalID.$idtrans.$importe.$moneda.$Exponente.$Referencia);
	echo $sha1;
	mail("jtoirac@gmail.com", "Llegada a llega en Prueba", $json."\n\n".$sha1);
//	Clave_encriptacion+MerchantID+AcquirerBIN+TerminalID+Num_operacion+Importe+TipoMoneda+Exponente+Referencia
?>
