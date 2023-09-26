<?php
define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/hoteles.func.php' );
require_once( 'admin/adminis.func.php' );
$temp = new ps_DB;

$d=$_REQUEST['tr'];
$c=$_REQUEST['co'];
$i=$_REQUEST['id'];

//$d = 'YCLHTO';
//$c = '129025985109';
//error_log($d);
if (strlen($d) < 20) {

    $query = "select r.id_transaccion, r.nombre cliente, r.codigo, t.codigo code, c.nombre comercio, c.datos, servicio, r.valor, r.idioma, from_unixtime(r.fechaPagada, '%e - %b - %Y %H:%i:%s') fechaPago, m.moneda, r.moneda idmoneda, c.palabra, c.voucherEn, a.nombre agencia, c.voucherEs, p.comercio, p.imagen from tbl_reserva r, tbl_comercio c, tbl_moneda m, tbl_transacciones t, tbl_agencias a, tbl_pasarela p where p.idagencia = a.id and  r.id_comercio = c.idcomercio and p.idPasarela = t.pasarela and r.id_transaccion = t.idtransaccion and r.moneda = m.idmoneda and r.codigo = '$d' and c.idcomercio = '$c'";
//error_log($query);
    $temp->query($query);
    $cliente = $temp->f('cliente');
    $comercio = $temp->f('comercio');
    $servicio = $temp->f('servicio');
    $importeTot = $temp->f('valor');
    $moneda = $temp->f('moneda');
    $codigo = $temp->f('codigo');
	$transac = $temp->f('id_transaccion');
	$datos = $temp->f('servicio');
	$fecha = $temp->f('fechaPago');
	$code = $temp->f('code');
    $tienda = $temp->f('agencia');
    $imag = "admin/logos/".$temp->f('imagen').".png";

	if (strlen($i) > 0) $idiom = $i;
	else $idiom = $temp->f('idioma');
    if ($idiom == 'es') $idiom = 'spanish';
    else $idiom = 'english';
    include_once "admin/lang/$idiom.php";
//    echo $idiom;

	//echo $voucher;
//	switch ($moneda) {
//		case 'EUR':
//			$mone = "&euro;";
//			break;
//		case 'USD':
//			$mone = "USD$";
//			break;
//		case 'CAD':
//			$mone = "CAD$";
//			break;
//		case 'GBP':
//			$mone = "&pound;";
//			break;
//	}
}
?>
<style>
	body{margin:0;padding:0}
    table{margin-bottom: 10px;display: block;width: 300px;}
    #uno{font-size: 20px; margin-bottom: 15px; text-align: center; word-wrap: normal;}
    .uno{font-size: 12px; margin-bottom: 15px; text-align: center;font-weight: bold;}
    #cero{margin: 15px 0; text-align: center;vertical-align: middle; word-wrap: normal;font-size: 20px;}
    #cero img{display: block;margin: 15px auto;}
    tr{display: block;margin: 3px 0;}
    .titule{font-weight:bold;vertical-align:top}
    .ult{margin-top: 35px;display: block;}
    .pas{text-align:center}
</style>

<table>
    <tr style="display:table-row;">
        <td id="cero" colspan="2" > -- TPV Virtual --</td>
    </tr>
    <!--<tr id="uno" style="display:table-row">
        <td colspan="2"><?php echo _TICKET_PAGO; ?></td>
    </tr>-->
    <tr class="uno" style="display:table-row">
        <td colspan="2"><?php echo _TICKET_SUPAGO; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _MENU_ADMIN_COMERCIO; ?>: </td>
        <td><?php echo $tienda; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _FORM_FECHA; ?>: </td>
        <td><?php echo $fecha; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _COMERCIO_SER; ?>: </td>
        <td><?php echo $datos; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _REPORTE_CLIENTE; ?>: </td>
        <td><?php echo $cliente; ?></td>
    </tr>
   <tr>
        <td class='titule'>ID: </td>
        <td><?php echo $codigo; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _MOVIL_TRANSACCION; ?>: </td>
        <td><?php echo $transac; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _TICKET_AUTOR; ?>: </td>
        <td><?php echo $code; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _COMPRUEBA_IMPORTE; ?>: </td>
        <td><?php echo $importeTot; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _COMERCIO_MONEDA; ?>: </td>
        <td><?php echo $moneda; ?></td>
    </tr>
    <tr style="display:table-row">
        <td class='titule ult pas' colspan="2">Identificación Cliente<br><br> ________________________________</td>
    </tr>
    <tr style="display:table-row">
        <td class='titule ult pas' colspan="2"><?php echo _TICKET_FIRMA; ?><br><br> ________________________________</td>
    </tr>
</table>
<br /><br /><br />
<table>
    <tr style="display:table-row;">
        <td id="cero" colspan="2" > -- TPV Virtual --</td>
    </tr>
    <!--<tr id="uno" style="display:table-row">
        <td colspan="2"><?php echo _TICKET_PAGO; ?></td>
    </tr>-->
    <tr class="uno" style="display:table-row">
        <td colspan="2"><?php echo _TICKET_SUPAGO; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _MENU_ADMIN_COMERCIO; ?>: </td>
        <td><?php echo $tienda; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _FORM_FECHA; ?>: </td>
        <td><?php echo $fecha; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _COMERCIO_SER; ?>: </td>
        <td><?php echo $datos; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _REPORTE_CLIENTE; ?>: </td>
        <td><?php echo $cliente; ?></td>
    </tr>
   <tr>
        <td class='titule'>ID: </td>
        <td><?php echo $codigo; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _MOVIL_TRANSACCION; ?>: </td>
        <td><?php echo $transac; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _TICKET_AUTOR; ?>: </td>
        <td><?php echo $code; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _COMPRUEBA_IMPORTE; ?>: </td>
        <td><?php echo $importeTot; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _COMERCIO_MONEDA; ?>: </td>
        <td><?php echo $moneda; ?></td>
    </tr>
    <tr style="display:table-row">
        <td class='titule ult pas' colspan="2">PARA EL CLIENTE</td>
    </tr>
</table>
<script type='text/javascript'>print();</script>
