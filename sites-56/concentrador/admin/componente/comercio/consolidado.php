<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/comercio/comercio.html.php'));
$partes = explode('{corte1}', $admin_mod);

$temp = @ new ps_DB;
$d = $_POST;
$query = "select idcomercio from tbl_comercio";
$temp->query($query);
$comercios = implode("','", $temp->loadResultArray());

$comer = $_SESSION['comercio'];
if ($comer == 'todos' && $d['comercio']) $comercId = $d['comercio'];
else if ($comer == 'todos' && !$d['comercio']) $comercId = $comercios;
else if ($comer != 'todos') $comercId = $comer;
$fecha1 = date('d/m/Y', mktime(0, 0, 0, date("m"), 1, date("Y")));
$fecha2 = date('d/m/Y', time());
$esta = '\'A\'';
$modoVal = '\'P\'';
$nombreVal = '';
if ($d['fecha1']) $fecha1 = $d['fecha1'];
if ($d['fecha2']) $fecha2 = $d['fecha2'];


$contenido = $partes[0].$partes[1].$partes[2].$partes[3];


$javascript = '
<script language="JavaScript" type="text/javascript">
'.$java.'
function verifica() {
	return true;
}
</script>
';

$contenido .= $partes[9].$partes[10].$partes[11].$partes[12].$partes[13].$partes[14];
$ancho = 500;

$contenido = str_replace('{titulo}', _CONSOLIDADO_TITLE, $contenido);
$contenido = str_replace('{tabed}', '', $contenido);
$contenido = str_replace('{javascript}', $javascript, $contenido);
$contenido = str_replace('{campo}', '', $contenido);
$contenido = str_replace('{_FORM_SEND}', _FORM_SEND, $contenido);
$contenido = str_replace('{_FORM_CANCEL}', _FORM_CANCEL, $contenido);
$contenido = str_replace('{titulo_tarea}', _REPORTE_TASK, $contenido);
$contenido = str_replace('{ancho_tabla}', $ancho, $contenido);
$contenido = str_replace('{_REPORTE_FECHA_INI}', _REPORTE_FECHA_INI, $contenido);
$contenido = str_replace('{_REPORTE_FECHA_FIN}', _REPORTE_FECHA_FIN, $contenido);
$contenido = str_replace('{fecha1}', $fecha1, $contenido);
$contenido = str_replace('{fecha2}', $fecha2, $contenido);
$contenido = str_replace('{idioma}', $_SESSION['idioma'], $contenido);
$contenido = str_replace('{_FORM_NOMBRE}', _REPORTE_IDENTIFTRANS, $contenido);
$contenido = str_replace('{anchoCelda}', ($ancho-14), $contenido);


echo $contenido;

$tabEncab = '<table align="center" width="500" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="15">
						<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
							<tr class="encabezamiento">
								<td width="100" align="left"></td>
								<td align="center"><strong>'.$fecha1.'&nbsp;-&nbsp;'.$fecha2.'</strong></td>
								<td width="120" align="right">&nbsp;</td>
							</tr>
						</table>
					</td>
    			  </tr>';
$otroCont = '<tr class="encabezamiento2">';
$otroCont .= '<td class="celdas" width="150" align="center">'._COMERCIO_TITULO.'</td>';
$otroCont .= '<td width="1" class="separador1"></td>';
$otroCont .= '<td class="celdas" width="" align="center">'._COMERCIO_ESTADO.'</td>';
$otroCont .= '<td width="1" class="separador1"></td>';
$otroCont .= '<td class="celdas" width="" align="center">'._REPORTE_CANT.'</td>';
$otroCont .= '<td width="1" class="separador1"></td>';
$otroCont .= '<td class="celdas" width="" align="center">'._INICIO_VALOR.'</td>';
$otroCont .= '</tr>';

$arrEstado = array(array('A', _REPORTE_ACEPTADA), array('B', _REPORTE_ANULADA), array('D', _REPORTE_DENEGADA), array('N', _REPORTE_PROCESADA),
					array('P',  _REPORTE_PENDIENTE), array('V', _REPORTE_DEVUELTA));
//$arrEstado = array('Aceptada', 'Anulada', 'Denegada', 'No Aceptada', 'Pendiente', 'Devuelta');
$arrTodo = array();
$query = "select idcomercio, nombre from tbl_comercio where estado = 'P' and activo = 'S' order by nombre";
$temp->query($query);
$arrComer = $temp->loadResultArray(0);
$arrComer2 = $temp->loadResultArray(1);

$acumTot = 0;
$cuentaTot = 0;
$conta = 0;

foreach ($arrComer as $comer)  {
	$acumCom = 0;
	$cuentaCom = 0;
	foreach ($arrEstado as $estado) {
		$otroCont .= '<tr class="cuerpo" id="campo'.$arrComer2[$conta].$estado[0].'" onMouseOver="cambia(this.id, \'over\');" onMouseOut="cambia(this.id, \'\')">';

		$query = "select case t.estado
						when 'P' then '"._REPORTE_PENDIENTE."'
						when 'A' then '"._REPORTE_ACEPTADA."'
						when 'D' then '"._REPORTE_DENEGADA."'
						when 'N' then '"._REPORTE_PROCESADA."'
						when 'B' then '"._REPORTE_ANULADA."'
						when 'V' then '"._REPORTE_DEVUELTA."' end estado,
					count(idtransaccion) cuenta,
                    case t.estado
                        when 'V' then (sum(valor) / 100)
                    else (sum(valor_inicial) / 100) end `suma`
					from tbl_transacciones t
					where t.idcomercio = $comer
					and fecha between ".to_unix($fecha1)." and ".(to_unix($fecha2)+86400)."
					and t.estado = '$estado[0]'
                    and tipoEntorno = 'P'
					group by t.idcomercio, t.estado";
		$temp->query($query);
		if ($estado[1] == $temp->f('estado')) {
			$cuenta = $temp->f('cuenta');
//			$suma = substr( $temp->f('suma'), 0, strpos( $temp->f('suma'), ".") + 3 );
			$suma = money_format('%i', $temp->f('suma') );
		} else $cuenta = $suma = 0;
		if ($estado[0] == 'A' || $estado[0] == 'V') {
			$acumCom += $suma;
			$cuentaCom += $cuenta;
		}

		$otroCont .= "<td class=\"celdaVal\" align=\"left\">$arrComer2[$conta]</td><td width=\"1\" class=\"separador2\"></td>";
		$otroCont .= "<td class=\"celdaVal\" align=\"center\">$estado[1]</td><td width=\"1\" class=\"separador2\"></td>";
		$otroCont .= "<td class=\"celdaVal\" align=\"center\">$cuenta</td><td width=\"1\" class=\"separador2\"></td>";
		$otroCont .= "<td class=\"celdaVal\" align=\"right\">$suma</td>";
		$otroCont .= '</tr>';
	}
	$acumTot += $acumCom;
	$cuentaTot += $cuentaCom;
	$otroCont .= "<tr class=\"encabezamiento\" >";
	$otroCont .= "<td class=\"celdaVal\" align=\"left\">$arrComer2[$conta]</td><td width=\"1\" class=\"separador2\"></td>";
	$otroCont .= "<td class=\"\" align=\"center\">("._REPORTE_ACEPTDEV.")</td><td width=\"1\" class=\"separador2\"></td>";
	$otroCont .= "<td class=\"celdaVal\" align=\"center\">$cuentaCom</td><td width=\"1\" class=\"separador2\"></td>";
	$otroCont .= "<td class=\"celdaVal\" align=\"right\">$acumCom</td>";
	$otroCont .= '</tr>';

    $conta++;
}
$otroCont .= "<tr style=\"font-weight:bold; font-size:1.2em;\" >";
$otroCont .= "<td class=\"celdaVal\" align=\"left\">Total</td><td width=\"1\" class=\"separador2\"></td>";
$otroCont .= "<td style=\"font-weight:normal; font-size:0.85em; \" align=\"center\">(Aceptadas y devueltas)</td><td width=\"1\" class=\"separador2\"></td>";
$otroCont .= "<td class=\"celdaVal\" align=\"center\">$cuentaTot</td><td width=\"1\" class=\"separador2\"></td>";
$otroCont .= "<td class=\"celdaVal\" align=\"right\">$acumTot</td>";
$otroCont .= '</tr>';

$escrp = '<script language="JavaScript" type="text/JavaScript">
function cambia(renglon, acc) {
	if (acc == \'over\') document.getElementById(renglon).bgColor=\'#CCCCCC\';
	else document.getElementById(renglon).bgColor=\'\';
}
</script>
</table>';

echo "<table class='total1' width=\"$ancho\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
	<tr>
		<td><img onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' src=\"../images/x-office-document.png\" alt='"._REPORTE_CSV."' title='"._REPORTE_CSV."' width=\"22\" height=\"22\" /></td>
	</tr>
</table>";
echo $tabEncab.$otroCont.$escrp;

$otroCont = str_replace("</td>", ";", $otroCont);
$otroCont = strip_tags($otroCont);
$otroCont = str_replace(";;", "#", $otroCont);
$otroCont = str_replace("#&nbsp#;", "##", $otroCont);
$otroCont = str_replace("#", ",", $otroCont);
//$otroCont = str_replace(";", "\n", $otroCont);
?>
<form name="exporta" action="" method="POST">
	<input type="hidden" name="querys" value="<?php echo $otroCont ?>">
	<input type="hidden" name="fecha1" value="<?php echo $fecha1 ?>">
	<input type="hidden" name="fecha2" value="<?php echo $fecha2 ?>">
</form>
