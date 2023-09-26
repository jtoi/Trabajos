<?php
define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'admin/adminis.func.php' );
$temp = new ps_DB;
$ent = new entrada;

$d=$_REQUEST['tr'];
$c=$_REQUEST['co'];
$i=$_REQUEST['id'];

if (strlen($d) < 13) {
	if (!$ent->isAlfanumerico($d, 12)) exit;
	if (!$ent->isEntero($c, 12)) exit;
//	if (!$ent->isAlfabeto($i, 3)) exit;

    $query = "select r.id_transaccion, r.nombre cliente, r.codigo, t.codigo code, c.nombre comercio, c.datos, servicio, r.valor, r.idioma, from_unixtime(r.fechaPagada, '%e - %b - %Y %H:%i') fechaPago,
                    m.moneda, r.moneda idmoneda, c.palabra, c.voucherEn, c.voucherEs, p.comercio comTPV
                from tbl_reserva r, tbl_comercio c, tbl_moneda m, tbl_transacciones t, tbl_pasarela p
                where r.id_comercio = c.idcomercio
					and	p.idPasarela = t.pasarela
					and r.id_transaccion = t.idtransaccion
                    and r.moneda = m.idmoneda
                    and r.codigo = '$d'
                    and c.idcomercio = '$c'";
//	echo $query;
    $temp->query($query);
	
    $cliente = $temp->f('cliente');
    $comercio = $temp->f('comercio');
    $servicio = $temp->f('servicio');
    $importeTot = $temp->f('valor');
    $moneda = $temp->f('moneda');
    $codigo = $temp->f('codigo');
	$transac = $temp->f('id_transaccion');
	$datos = $temp->f('datos');
	$fecha = $temp->f('fechaPago');
	$code = $temp->f('code');
	$comTPV = $temp->f('comTPV');

	if (strlen($i) > 0) $idiom = $i;
	else $idiom = $temp->f('idioma');

	if ($idiom == 'es' && strlen($temp->f('voucherEs')) > 10) {
		$voucher = html_entity_decode($temp->f('voucherEs'),ENT_QUOTES,'ISO-8859-1');
	} elseif ($idiom == 'es' && strlen($temp->f('voucherEs')) <= 10) {
		$voucher = leeSetup('voucherEs');
	} elseif ($idiom == 'en' && strlen($temp->f('voucherEn')) > 10) {
		$voucher = html_entity_decode($temp->f('voucherEn'),ENT_QUOTES,'ISO-8859-1');
	} elseif ($idiom == 'en' && strlen($temp->f('voucherEn')) <= 10) {
		$voucher = leeSetup('voucherEn');
	}

	$voucher = str_replace('www.travelsdiscovery.com', 'www.concentradoramf.com', $voucher);
	$voucher = str_replace('{COMERCIO}', $comercio, $voucher);
	$voucher = str_replace('{CLIENTE}', $cliente, $voucher);
	$voucher = str_replace('{FECHA}', $fecha, $voucher);
	$voucher = str_replace('{TRANSACCION}', $transac, $voucher);
	$voucher = str_replace('{IMPORTE}', $importeTot.' '.$moneda, $voucher);
	$voucher = str_replace('{DATACOMERCIO}', $datos, $voucher);
	$voucher = str_replace('{DESCRIPCION}', $servicio, $voucher);
	$voucher = str_replace('{OPERACION}', $codigo, $voucher);
	$voucher = str_replace('{ANO}', date('Y'), $voucher);
	$voucher = str_replace('{AUTORIZO}', $code, $voucher);
	$voucher = str_replace('{COMTPV}', $comTPV, $voucher);
//	$subject = 'Voucher';
//	$correo .= $voucher;
//	correoAMi($subject,$correo);
	echo $voucher;
}
?>
