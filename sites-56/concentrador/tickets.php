<?php
/**
 * Impresión del ticket de las operaciones que son por web
 * no pertenece al trabajo normal del concentrador
 * a solicitud de Ivet el 12/01/21
 */
define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/hoteles.func.php' );
require_once( 'admin/adminis.func.php' );
$temp = new ps_DB;

$d=$_REQUEST['tr'];

//$d = 'YCLHTO';
//$c = '129025985109';
//error_log($d);
if (strlen($d) < 20) {

    $query = "select t.idtransaccion, t.identificador, t.codigo, c.nombre comercio, c.datos, t.valor_inicial/100 valor, from_unixtime(t.fecha_mod, '%e - %b - %Y %H:%i:%s') fechaPago, m.moneda, t.moneda idmoneda, c.palabra, c.voucherEn, a.nombre agencia, c.voucherEs, p.comercio, p.imagen from tbl_comercio c, tbl_moneda m, tbl_transacciones t, tbl_agencias a, tbl_pasarela p where p.idagencia = a.id and t.idcomercio = c.idcomercio and p.idPasarela = t.pasarela and t.moneda = m.idmoneda and t.idtransaccion = '$d'";
//error_log($query);
    $temp->query($query);
    $cliente = '';
    $comercio = $temp->f('comercio');
    $servicio = '';
    $importeTot = $temp->f('valor');
    $moneda = $temp->f('moneda');
    $codigo = $temp->f('codigo');
	$transac = $temp->f('idtransaccion');
	$datos = $temp->f('servicio');
	$fecha = $temp->f('fechaPago');
	$code = $temp->f('identificador');
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
   <!-- <tr>
        <td class='titule'><?php echo _COMERCIO_SER; ?>: </td>
        <td><?php echo $datos; ?></td>
    </tr> -->
   <tr>
        <td class='titule'><?php echo _REPORTE_CLIENTE; ?>: </td>
        <td><?php echo $cliente; ?></td>
    </tr>
   <tr>
        <td class='titule'>ID: </td>
        <td><?php echo $code; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _MOVIL_TRANSACCION; ?>: </td>
        <td><?php echo $transac; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _TICKET_AUTOR; ?>: </td>
        <td><?php echo $codigo; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _COMPRUEBA_IMPORTE; ?>: </td>
        <td><?php echo number_format($importeTot,2); ?></td>
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
   <!-- <tr>
        <td class='titule'><?php echo _COMERCIO_SER; ?>: </td>
        <td><?php echo $datos; ?></td>
    </tr> -->
   <tr>
        <td class='titule'><?php echo _REPORTE_CLIENTE; ?>: </td>
        <td><?php echo $cliente; ?></td>
    </tr>
   <tr>
        <td class='titule'>ID: </td>
        <td><?php echo $code; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _MOVIL_TRANSACCION; ?>: </td>
        <td><?php echo $transac; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _TICKET_AUTOR; ?>: </td>
        <td><?php echo $codigo; ?></td>
    </tr>
   <tr>
        <td class='titule'><?php echo _COMPRUEBA_IMPORTE; ?>: </td>
        <td><?php echo number_format($importeTot,2); ?></td>
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
