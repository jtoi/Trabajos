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
	if (!$ent->isEntero($c, 12)) exit;
	if (!$ent->isAlfanumerico($d, 12)) exit;
//	if (!$ent->isAlfabeto($i, 3)) exit;

    $query = "select r.id_transaccion, r.nombre cliente, r.codigo, t.codigo code, c.nombre comercio, c.datos, servicio, r.valor, r.idioma, from_unixtime(r.fechaPagada, '%e - %b - %Y %H:%i') fechaPago,
                    m.moneda, r.moneda idmoneda, c.palabra, c.id
                from tbl_reserva r, tbl_comercio c, tbl_moneda m, tbl_transacciones t
                where r.id_comercio = c.idcomercio
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
	$idCom = $temp->f('id');

	if (strlen($i) > 0) $idiom = $i;
	else $idiom = $temp->f('idioma');
	
//	echo $idiom;
	
	$q = "select texto from tbl_traducciones t, tbl_idioma i where i.iso = '$idiom' and idIdioma = i.id and idcomercio = $idCom and tipo = 2";
//	echo $q;
	$temp->query($q);
	$voucher = html_entity_decode($temp->f('texto'), ENT_QUOTES, 'UTF-8');
	$voucher = utf8_decode($voucher);
//	echo $voucher;
//	$voucher = $temp->f('texto');
//	$voucher = str_replace("&quot;", '', $voucher);
//	$voucher = $temp->f('texto');

	$voucher = str_replace('www.travelsdiscovery.com', 'www.administracomercios.com', $voucher);
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
//	$subject = 'Voucher';
//	$correo .= $voucher;
//	correoAMi($subject,$correo);
	echo $voucher;
}
?>
