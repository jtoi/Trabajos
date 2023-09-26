<?php 
/**
 * Envía la Confirmación de Orden a Titanes
 */
define('_VALID_ENTRADA', 1);
// //ini_set("display_errors", 1);
// //error_reporting(E_ALL & ~E_NOTICE);

require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/correo.php' );
require_once( 'admin/adminis.func.php' );

//defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
/******************************/

$fechaAyer = mktime(date('H')-25, 0, 0, date("m"), date("d"), date("Y"));
// $fechaAyer = mktime(date('H')-49, 0, 0, date("m"), date("d"), date("Y"));
$fechahoy = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$ayer = date('ymd',$fechaAyer);

$temp = new ps_DB;
$corCreo = new correo;

$q = "select b.idtitanes, (o.recibe/100), from_unixtime(t.fecha,'%Y-%m-%dT%H:%i:%s'), b.numDocumento, o.titOrdenId"
		." from tbl_transacciones t, tbl_aisOrden o, tbl_aisBeneficiario b "
		." where o.idtransaccion = t.idtransaccion "
		." and o.idbeneficiario = b.id"
		." and t.estado = 'A'"
		." and t.solDev = 0"
		." and t.fecha < $fechahoy"
		." and o.subida = 0"
//  		." and o.titOrdenId in ('4838062', '4838061' )"
				;
// if (_MOS_CONFIG_DEBUG) echo($q);

$temp->query($q);
if ($temp->num_rows() > 0) {
	$arrOpr = $temp->loadRowList();
	// print_r($arrOpr);
	$pago[1] = substr($pago[1], 0, strpos($pago[1], '.')+3);
	
	for ($i=0; $i<count($arrOpr); $i++) {
		$mto = substr($arrOpr[$i][1], 0, strpos($arrOpr[$i][1], '.')+3);
		$arrSend = array("BeneficiaryId"	=> $arrOpr[$i][0],
					"AmountToReceive"		=> $mto,
					"PaymentDate"			=> $arrOpr[$i][2],
					"DocumentNumber"		=> $arrOpr[$i][3],
					"Signature"				=> $arrOpr[$i][4].$arrOpr[$i][0].$mto,
					"OrdenId"				=> $arrOpr[$i][4]);
error_log("Envio de confirmación de Orden a Titanes = ". json_encode($arrSend));
		$resp = datATitanes ($arrSend, 'P');
		if (strstr($resp, "Error") > -1) {
			$resp .= "Orden con error: ".$arrOpr[$i][4];
			error_log($resp);
		}
		$correoMi .= $resp;
	}
} else $correoMi .= "No hay órdenes que confirmar<br>\n";
//echo $correoMi."<br>";

/*****************************/

muestraError($correoMi);

function muestraError ($textoCorreo) {
	global $correoMi, $corCreo;
	if (strstr($textoCorreo, "Error") > -1) $corCreo->todo(54, 'Error subiendo Confirmación de Orden de Ais a Titanes', $textoCorreo."\n<br> ** ".$correoMi);
	else $corCreo->todo(53, 'Confirmación de Orden de Ais a Titanes', $textoCorreo."\n<br> ** ".$correoMi);
	// 	exit;
}
?>