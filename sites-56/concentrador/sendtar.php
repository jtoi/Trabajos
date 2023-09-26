<?php
define( '_VALID_ENTRADA' , 1);

// require_once("admin/classes/SecureSession.class.php");
// $Session = new SecureSession(3600);
include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
require_once( 'include/mysqli.php' );
//include( "admin/adminis.func.php" );

$temp = new ps_DB ();
$ent = new entrada;
$d = $_REQUEST;

	// foreach ($d as $key => $value) {
	// 	error_log("$key => $value");
	// }
//if (_MOS_CONFIG_DEBUG) {
//	$d['comercio'] = '135334103888';//Hotel Nacional
//}
if (!isset($d['comercio'])) {
	echo "<!-- Error no se ha enviado la variable requerida -->";
	exit;
}
if (!($comer = $ent->isEntero($d['comercio'], 13))) {
	echo "<!-- Error en el comercio enviado -->";
	exit;
}

// echo "entra-".$d['comercio'];
// error_log($d['comercio']);

$temp->query(sprintf("select pasarela from tbl_comercio where idcomercio = '%d'", $d['comercio']));
$pasa = $temp->f('pasarela');
$temp->query("select distinct idTarj from tbl_colTarjPasar where idPasar in ($pasa)");
echo json_encode( $temp->loadResultArray());
// $temp->query(sprintf("select distinct t.idTarj from tbl_comercio c, tbl_colTarjPasar t where c.idcomercio = '%d' and t.idPasar in (c.pasarela)",$d['comercio']));
// echo sprintf("select distinct t.idTarj from tbl_comercio c, tbl_colTarjPasar t where c.idcomercio = '%d' and t.idPasar in (c.pasarela)",$d['comercio'])."<br>";
// echo json_encode( $temp->loadResultArray());

?>
