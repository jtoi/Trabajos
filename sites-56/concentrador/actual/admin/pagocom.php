<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
global $temp;
$html = new tablaHTML;
include_once( 'componente/comercio/devolucion.php' );
global $send_m;

$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/comercio/comercio.html.php'));
$partes = explode('{corte1}', $admin_mod);

$d = $_REQUEST;

// print_r($d);

if ($d['pagar']) {
	if ($_SESSION['grupo_rol'] <= 5 ) {
// print_r($d);
		$tran = implode(',', $d['transaccion']);
		$query = "update tbl_transacciones set pago = 1, fechaPagada = '".time()."' where idtransaccion in (".$tran.")";
		$temp->query($query);
		
// 		este es el where de la query que busca todas las operaciones a devolver, el tema es que si alguna  
// 		where t.idcomercio = c.idcomercio
// 		and t.moneda = m.idmoneda
// 		and e.id = p.idempresa
// 		and p.idPasarela = t.pasarela
// 		and t.moneda in ('$monedaid')
// 		and c.id in ('$comercio')
// 		and e.id in ('$emp')
// 		and t.fecha_mod between $fecha1 and $fecha2
// 		and t.tipoEntorno = 'P'
// 				and t.pago = 0
// 				and t.estado = 'A'"
//echo $query;
	} else $contenido = _AUTENT_NOSEPUEDE;
}

if ($d['fecha1']) $fecha1 = $d['fecha1'];
else $fecha1 = date('d/m/Y', time());
if ($d['fecha2']) $fecha2 = $d['fecha2'];
else $fecha2 = date('d/m/Y', time());
$mes1 = explode('/', $fecha1);
$mes1 = 1*$mes1[1];
$mes2 = explode('/', $fecha2);
$mes2 = 1*$mes2[1];

if ($d['horasin']) $hora1 = $d['horasin'];
else $hora1 = '00:00';
if ($d['horasout']) $hora2 = $d['horasout'];
else $hora2 = '24:00';
if ($d['monedas']) $monedaid = $d['monedas'];
else $monedaid = '978,840,826';

if (isset ($d['comercio'])) {
	$comercId = $d['comercio'];
} else {
	if ($_SESSION['rol'] < 2) $comercId = $comercios;
	else if ($comer != 'todos') $comercId = $comer;
}


if(is_array($comercId)) $comercId = implode(',', $comercId);
$comercId = str_replace("'", "", $comercId);
if(is_array($empId)) $empId = implode(',', $empId);
$empId = str_replace("'", "", $empId);

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_PAGOCLIENTE;
$html->tituloTarea = "&nbsp;";
$html->anchoTabla = 500;
$html->anchoCeldaI = $html->anchoCeldaD = 245;
$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
					function verifica() {
						return true;
					}
					</script>";

$html->inHide('fecha_mod desc', 'orden');
$query = "select distinct id, nombre from tbl_comercio where id in (select cierrePor from tbl_comercio where activo = 'S') order by nombre";
$html->inSelect(_COMERCIO_TITULO, 'comerci', 5, $query, $comercId, null, null, "multiple size='5'");
$query = "select id, nombre from tbl_empresas order by nombre";
$html->inSelect('Empresa', 'emp', 5, $query,  str_replace(",", "', '", $empresaid), null, null, "multiple size='5'");
$query = "select idmoneda id, moneda nombre from tbl_moneda";
$html->inSelect(_COMERCIO_MONEDA, 'monedas', 5, $query, $monedaid);
$horasArr = array(
	array('00:00', '00:00'),array('01:00', '01:00'),array('02:00', '02:00'),array('03:00', '03:00'),array('04:00', '04:00'),array('05:00', '05:00'),
	array('06:00', '06:00'),array('07:00', '07:00'),array('08:00', '08:00'),array('09:00', '09:00'),array('10:00', '10:00'),array('11:00', '11:00'),
	array('12:00', '12:00'),array('13:00', '13:00'),array('14:00', '14:00'),array('15:00', '15:00'),array('16:00', '16:00'),array('17:00', '17:00'),
	array('18:00', '18:00'),array('19:00', '19:00'),array('20:00', '20:00'),array('21:00', '21:00'),array('22:00', '22:00'),array('23:00', '23:00'),
	array('24:00', '24:00')
);
$ver = "&nbsp;<select name='horasin'>";
$ver .= opciones_arr($horasArr, $hora1);
$ver .= "</select>";
$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', null, null, null, null, $ver);
$ver = "&nbsp;<select name='horasout'>";
$ver .= opciones_arr($horasArr, $hora2);
$ver .= "</select>";
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', null, null, null, null, $ver);

echo $html->salida();

if ($d['fecha2'] || $d['orden']) {

	$fecha = explode('/', $d['fecha1']);
	$hora = explode(':', $d['horasin']);
	$fecha1 = mktime($hora[0], $hora[1], 0, $fecha[1], $fecha[0], $fecha[2]);
	$fecha = explode('/', $d['fecha2']);
	$hora = explode(':', $d['horasout']);
	$fecha2 = mktime($hora[0], $hora[1], 0, $fecha[1], $fecha[0], $fecha[2]);
	if ($d['mayor'] == 'desc') $mayor = 'asc'; else $mayor = 'desc';
	if (!$d['orden']) $orden = 'fecha desc'; else $orden = $d['orden'];

	if ($d['comerci']) {
		$comercio = implode(",", $d['comerci']);
	} else {
		if ($d['comercio'][0] == 'todos') {
			$query = "select idcomercio from tbl_comercio";
			$temp->query($query);
			$comercio = $temp->loadResultArray();
		} else $comercio = $d['comercio'];
		$comercio = "'".implode("','", $comercio)."'";
	}
	if ($d['emp']) {
		$emp = implode(",", $d['emp']);
	} else {
		if ($d['emp'][0] == 'todos') {
			$query = "select id from tbl_empresas";
			$temp->query($query);
			$emp = $temp->loadResultArray();
		} else $emp = $d['emp'];
		$emp = "'".implode("','", $emp)."'";
	}
	
	$monedas = $d['monedas'];

	$where = "where t.idcomercio = c.idcomercio
					and t.moneda = m.idmoneda
					and e.id = p.idempresa
					and p.idPasarela = t.pasarela
					and t.moneda in ('$monedaid')
					and c.cierrePor in ($comercio)
					and e.id in ('$emp')
					and t.fecha_mod between $fecha1 and $fecha2
					and t.tipoEntorno = 'P'
					and t.pago = 0
					and t.estado in ('A','V','B','R')";
//echo $monedaid;
	if ($monedaid != "124', '152', '170', '32', '356', '392', '484', '604', '756', '826', '840', '937', '949', '978', '986") {
		$query = "select moneda from tbl_moneda where idmoneda in ('$monedaid')";
		$temp->query($query);
		$moneda = $temp->f('moneda');
	} else $moneda = 'Todas';

	$query = "select
				sum(case t.estado
					when 'B' then (
						if ($mes1 > FROM_UNIXTIME(t.fecha_mod, '%c') , (-1 * ((valor_Inicial-valor)/100)) , (valor/100) ))
					when 'V' then (
						if ($mes1 > FROM_UNIXTIME(t.fecha_mod, '%c') , (-1 * ((valor_Inicial-valor)/100)) , (valor/100) ) )
					else (valor/100) end) total
			from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_empresas e, tbl_pasarela p
			$where ";
	$temp->query($query);
	$valor = $temp->f('total');

	$query = "select t.idtransaccion, c.nombre, e.nombre emp, t.fecha_mod fecha, t.valor/100 valor, m.moneda, case t.estado 
					when 'P' then 'En Proceso' 
					when 'A' then if (solDev = 0, 'Aceptada', 'Sol. Devolc.')
					when 'D' then 'Denegada' 
					when 'N' then 'No Procesada' 
					when 'B' then 'Anulada' 
					when 'R' then 'Reclamada' 
					else 'Devuelta' end estad
				from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_empresas e, tbl_pasarela p
				$where
				order by $orden";
//    echo $query;
	$temp->query($query);
	$arrRe = $temp->loadAssocList();
	$cant = count($arrRe);
// 	print_r($arrRe);
	
?>
<div id="tablBajo" style="float:left;margin:0 auto;width:100%">
<form name="pagaTo" method="post" action="" >
	<input type="hidden" name="pagar" value="1" />
	<input type="hidden" name="orden" value="" />
	<input type="hidden" name="mayor" value="<?php echo $mayor; ?>" />
	<input type="hidden" name="fecha1" value="<?php echo $d['fecha1']; ?>" />
	<input type="hidden" name="fecha2" value="<?php echo $d['fecha2']; ?>" />
	<input type="hidden" name="horasin" value="<?php echo $d['horasin']; ?>" />
	<input type="hidden" name="horasout" value="<?php echo $d['horasout']; ?>" />
	<input type="hidden" name="comerci" value="<?php echo $comercio; ?>" />
	<input type="hidden" name="emp" value="<?php echo $emp; ?>" />
<table border="0" align="center" width="731" cellspacing="0" cellpadding="0">
	<tr><td colspan="15">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
				<tr class="encabezamiento">
					<td width="200" align="left"> <?php echo $cant; ?> Record(s)&nbsp;<?php echo number_format($valor,2).' '.$moneda; ?></td>
					<td align="center"><input type="button" value="A Pagar" onclick="document.pagaTo.submit();" class="formul" /></td>
					<td width="120" align="right"></td>
				</tr>
			</table>
		</td>
	</tr>
    <tr>
      <td height="1" colspan="13"/>
    </tr>
    <tr class="encabezamiento2">
		<td align="center" class="celdas"></td>
		<td width="1" class="separador1"/>
		<td align="center" width="50" class="celdas"><a onclick="document.pagaTo.orden.value = 'idtransaccion <?php echo $mayor; ?>'; document.pagaTo.submit();" href="#" class="paglink">Id</a></td>
		<td width="1" class="separador1"/>
		<td align="center" width="150" class="celdas"><a onclick="document.pagaTo.orden.value = 'nombre <?php echo $mayor; ?>'; document.pagaTo.submit();" href="#" class="paglink">Comercio</a></td>
		<td width="1" class="separador1"/>
		<td align="center" width="150" class="celdas"><a onclick="document.pagaTo.orden.value = 'emp <?php echo $mayor; ?>'; document.pagaTo.submit();" href="#" class="paglink">Empresa</a></td>
		<td width="1" class="separador1"/>
		<td align="center" width="" class="celdas"><a onclick="document.pagaTo.orden.value = 'fecha <?php echo $mayor; ?>'; document.pagaTo.submit();" href="#" class="paglink">Fecha</a></td>
		<td width="1" class="separador1"/>
		<td align="center" width="" class="celdas"><a onclick="document.pagaTo.orden.value = 'valor <?php echo $mayor; ?>'; document.pagaTo.submit();" href="#" class="paglink">Valor</a></td>
		<td width="1" class="separador1"/>
		<td align="center" width="" class="celdas"><a onclick="document.pagaTo.orden.value = 'moneda <?php echo $mayor; ?>'; document.pagaTo.submit();" href="#" class="paglink">Moneda</a></td>
		<td width="1" class="separador1"/>
		<td align="center" width="" class="celdas"><a onclick="document.pagaTo.orden.value = 'moneda <?php echo $mayor; ?>'; document.pagaTo.submit();" href="#" class="paglink">Estado</a></td>
	  </tr>
<?php
	foreach ($arrRe as $item) {
		$id = $item['idtransaccion'];
		$comer = $item['nombre'];
		$emp = $item['emp'];
		$fecha = formatea_fecha($item['fecha']);
		$valor = formatea_numero($item['valor']);
		$moneda = $item['moneda'];
		$estad = $item['estad'];
?>
    <tr onmouseout="cambia(this.id, '')" onmouseover="cambia(this.id, 'over');" id="campo<?php echo $id ?>" class="cuerpo">
		<td align="center" class="celdas"><input type="checkbox" name="transaccion[]" checked value="<?php echo $id ?>" /></td>
		<td width="1" class="separador1"/>
		<td align="left" style="padding: 4px 7px;"><?php echo $id ?></td>
		<td width="1" class="separador2"/>
	    <td align="left" style="padding: 4px 7px;"><?php echo $comer ?></td>
		<td width="1" class="separador2"/>
	    <td align="left" style="padding: 4px 7px;"><?php echo $emp ?></td>
		<td width="1" class="separador2"/>
	    <td align="center" style="padding: 4px 7px;"><?php echo $fecha ?></td>
		<td width="1" class="separador2"/>
	    <td align="right" style="padding: 4px 7px;"><?php echo $valor ?></td>
		<td width="1" class="separador2"/>
	    <td align="center" style="padding: 4px 7px;"><?php echo $moneda ?></td>
		<td width="1" class="separador2"/>
	    <td align="center" style="padding: 4px 7px;"><?php echo $estad ?></td>
	</tr>
<?php
	}
?>
</table>
</form>
<script type="text/JavaScript" language="JavaScript">
function cambia(renglon, acc) {
	if (acc == 'over') document.getElementById(renglon).bgColor='#CCCCCC';
	else document.getElementById(renglon).bgColor='';
}
</script>
</div>

<?php
}
?>
