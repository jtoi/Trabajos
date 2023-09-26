<?php

/* 
 * Confirmación de orden a Titanes del pago a la tarjeta del Beneficiario
 * se manda a ejecutar cada 2 minutos por parte de un cron
 */

define( '_VALID_ENTRADA', 1 );
require_once( 'configuration.php' );
require_once( 'include/mysqli.php' );
require_once( 'admin/adminis.func.php' );
require_once( 'include/correo.php' );

$temp = new ps_DB;
$correo = new correo();

exit();

$correoMi = "Confirmación de la orden a titanes<br>";

//tiempo en minutos desde que la orden fué confirmada por Titanes
$tminutos = 10;

//busco las operaciones a partir del 15/12 que tengan mas de tminutos de haberlas puesta Aceptada
//$q = "select titOrdenId from tbl_aisOrden where subida = 0 and estado = 'A' and (".time(). " - fechaAct ) > ".($tminutos*60). " and fechaAct > unix_timestamp('2018-12-15 00:00:00')";
// $q = "select o.idtransaccion, c.numDocumento from tbl_aisOrden o, tbl_aisCliente c where o.idcliente = c.id and subida = 0 and o.reconfirmacion = 0 and estado = 'A' and (".time(). " - fechaAct ) > ".($tminutos*60). " and fechaAct > unix_timestamp('2019-01-01 00:00:00')";
$q = "select o.idtransaccion, c.numDocumento, o.titOrdenId from tbl_aisOrden o, tbl_aisCliente c where o.idcliente = c.id and subida = 0 and o.reconfirmacion = 0 and fechaAct > unix_timestamp()-(60*60*2) and (titOrdenId * 1) > 10";
$correoMi .= $q."<br>\n";
echo $q."<br>";
$temp->query($q);
$arrSal = $temp->loadAssocList();
// var_dump($arrSal);

for ($i = 0; $i < count($arrSal); $i++){
	$id = $arrSal[$i]['idtransaccion'];
	echo $id."<br>";

	$data = array(
		'Id'				=> $id,
		'Code'				=> $arrSal[$i]['titOrdenId'],
		'Date'				=> date('Y-m-d H:i:s'),
		'Document'			=> $arrSal[$i]['numDocumento']
	);

	$tipo = 'N';
	$correoMi .= "envía=". json_encode($data)."<br>\n";
	$reco = datATitanes($data,$tipo,91);
	$correoMi .= "recibe=".$reco."<br>\n";
	$sale = json_decode($reco);

	$correoMi .= "Id=".		$sale->Id.		"<br>";
	$correoMi .= "Code=".	$sale->Code.	"<br>";
	$correoMi .= "Status=".	$sale->Status.	"<br>";
	
	if ($sale->Status != 'Paid') {
		$etiqueta = 'Error en la Confirmación del pago a Titanes';
		$correoMi .= "$etiqueta<br>";
		$correo->todo(12, $etiqueta, "Se ha producido un error en la confirmación del pago a los Beneficiarios. Titanes devolvió $correoMi");
	} else {
		$temp->query("update tbl_aisOrden set reconfirmacion = 1 where idtransaccion = '$id'");
		$correoMi .= "Todo bien Titanes recibió correctamente los datos";
		$correo->todo(12, $etiqueta, "Se ha procesado correctamente: $correoMi");
	}

}

error_log($correoMi);
	
?>
