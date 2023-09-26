<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
global $temp;
$html = new tablaHTML;
global $send_m;

$d = $_REQUEST;

if ($d['cambiar']) {

	if ($_SESSION['grupo_rol'] <= 5 ) {

		$query = "select e.*, c.nombre comercio from tbl_cierres e, tbl_comercio c where e.idcomercio = c.idcomercio and e.id = '".$d['cambiar']."'";
		$temp->query($query);
		$comercio = $temp->f('comercio');
		$consecutivo = $temp->f('consecutivo');
		$ano = date('Y', $temp->f('fechaFin'));
		$mes1 = date('M', $temp->f('fechaIni'));
		$mes2 = date('M', $temp->f('fechaFin'));
		$dia1 = date('d', $temp->f('fechaIni'));
		$dia2 = date('d', $temp->f('fechaFin'));
		$hora1 = date('H:i', $temp->f('fechaIni'));
		$hora2 = date('H:i', $temp->f('fechaFin'));
		
		$contenido = '<div class="title_pag1">'._MENU_ADMIN_CIERRES.'</div>';
		
		$query = "select idmoneda, moneda from tbl_moneda";
		$temp->query($query);
		$arrMon = $temp->loadAssocList();
		print_r($arrMon);

		foreach ($arrMon as $value) {
		
			$query = "select t.idtransaccion, t.identificador, t.codigo, p.nombre banco, from_unixtime(fecha_mod, '%d/%m/%y %H:%i') fecha, t.valor_inicial/100 valor_inicial,
							valor/100 valor, case estado when 'A' then 'Aceptada' when 'V' then 'Devuelta' else 'Anulada' end esta, tasa, euroEquiv, t.idcomercio
						from tbl_transacciones t, tbl_cierreTransac c, tbl_pasarela p 
						where c.idtransaccion = t.idtransaccion and idcierre = ".$d['cambiar']." and p.idPasarela = t.pasarela and t.moneda = ".$value['idmoneda']."
							";
			$temp->query($query);
			$arrTra = $temp->loadAssocList();
			$totEuro += $totEurTran;
			$totalMon = $totDevMon = $totRemMon = $totEurTran = 0;

			if ($temp->num_rows() > 0 ) {
				$contenido .= '
							<table cellpadding="0" class="cierreTab" cellspacing="0" align="center" >
								<tr>
									<td colspan="11" class="cierretop1">Cierre No. '.$consecutivo.'</td>
									<td class="cierretop1">Moneda: </td>
									<td class="cierretop1">'.$value['moneda'].'</td>
								</tr>
								<tr class="cierretop2">
									<td colspan="6">Cliente: '.$comercio.'</td>
									<td colspan="7">Año: '.$ano.' &nbsp;&nbsp;Mes: '.$mes1.'-'.$mes2.'  &nbsp;&nbsp;
										Desde el Día: '.$dia1.' a las '.$hora2.'h  &nbsp;&nbsp;Hasta el Día: '.$dia2.'
										a las '.$hora2.'h</td>
								</tr>
								<tr class="cierretit">
									<td class="cierretd" width="30">No. Op</td>
									<td class="cierretd" width="85">Id</td>
									<td class="cierretd" width="75">Ref. Comer</td>
									<td class="cierretd" width="65">Banco</td>
									<td class="cierretd" width="65">Ref. Banco</td>
									<td class="cierretd" width="125">Cliente</td>
									<td class="cierretd" width="110">Fecha</td>
									<td class="cierretd" width="70">Valor Inicial</td>
									<td class="cierretd" width="70">Valor Devoluc.</td>
									<td class="cierretd" width="70">Valor</td>
									<td class="cierretd" width="70">Estado</td>
									<td class="cierretd" width="70">Tasa de cambio</td>
									<td class="cierretd" width="70">Cambio &euro;</td>
								</tr>';
				foreach ($arrTra as $value) {

					$query = "select nombre cliente from tbl_reserva where codigo = '{$value['identificador']}' and id_comercio '{$value['idcomercio']}'";
					$temp->query($query);
					$valRem = $value['valor_inicial'] - $value['valor'];
					$totalMon += $value['valor_inicial'];
					$totDevMon += $valRem;
					$totRemMon += $value['valor'];
					$totEurTran += $value['euroEquiv'];

					$contenido .= '	<tr>
										<td class="cierretd" align="center">1</td>
										<td class="cierretd" align="center">'.$value['idtransaccion'].'</td>
										<td class="cierretd" align="center">'.$value['identificador'].'</td>
										<td class="cierretd" align="center">'.$value['banco'].'</td>
										<td class="cierretd" align="center">'.$value['codigo'].'</td>
										<td class="cierretd">'.$temp->f('cliente').'</td>
										<td class="cierretd" align="center">'.$value['fecha'].'</td>
										<td class="cierretd" align="right">'.formatea_numero($value['valor_inicial']).'</td>
										<td class="cierretd" align="right">'.formatea_numero($valRem).'</td>
										<td class="cierretd" align="right">'.formatea_numero($value['valor']).'</td>
										<td class="cierretd" align="center">'.$value['esta'].'</td>
										<td class="cierretd" align="right">'.$value['tasa'].'</td>
										<td class="cierretd" align="right">'.formatea_numero($value['euroEquiv']).'</td>
									</tr>';
				}
				$contenido .= '	<tr class="cierrefdo">
									<td colspan="7" class="cierretd">TOTALES</td>
									<td class="cierretd">'.formatea_numero($totalMon).'</td>
									<td class="cierretd">'.formatea_numero($totDevMon).'</td>
									<td class="cierretd">'.formatea_numero($totRemMon).'</td>
									<td class="cierretd">&nbsp;</td>
									<td class="cierretd">&nbsp;</td>
									<td class="cierretd">'.formatea_numero($totEurTran).'</td>
								</tr>
								<tr>
									<td colspan="13" class="cierresepa">&nbsp;</td>
								</tr>
							</table>';
			}
			
		}
		
		
		echo "totEuro=$totEuro";
		
		$contenido .= '<div class="cierreSepara"></div>';

		
	} else {
		$contenido = _AUTENT_NOSEPUEDE;
	}
	echo $contenido;

	
} else {

	if ($d['borrar']){
		/*
		 * Va a anular la transacción
		 */
		if ($_SESSION['grupo_rol'] <= 5 ) { //Rechaza si el administrador no es del grupito del chuchuchú
			$query = "update tbl_transacciones set valor = 0, fecha_mod = ".time().", estado = 'B'
						where idtransaccion = '".$d['borrar']."'";
			$temp->query($query);

			$query = "update tbl_reserva set valor = 0, fechaCancel = ".time().", estado = 'B'
						where id_transaccion = '".$d['borrar']."'";
			$temp->query($query);


            $query = "select c.nombre, t.fecha, t.valor_inicial valor
                        from tbl_comercio c, tbl_transacciones t
                        where  idtransaccion = '".$d['borrar']."'
                        and t.idcomercio = c.idcomercio ";
            $temp->query($query);
            $fecha = $temp->f('fecha');
            $comnombre = $temp->f('nombre');
            $valor = $temp->f('valor');

            $headers = 'From: tpv@caribbeanonlineweb.com' . "\r\n" .
                        'Reply-To: tpv@caribbeanonlineweb.com' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

            $to      = 'jtoirac@gmail.com, contab@amfglobalitems.com, koldo@amfglobalitems.com';

            $subject = 'Descuento / Devolución';

            $message = "transaccion: ".$d['borrar']." \r\n
                        comercio: $comnombre \r\n
                        valor descontado: $valor \r\n
                        fecha: ". date('d/m/Y h:m a', $fecha);

            mail($to, $subject, $message, $headers);

		} else $contenido = _AUTENT_NOSEPUEDE;
	}

	if ($d['pagar']) {
		/*
		*Paga la transaccion al comercio
		*/
		if ($_SESSION['grupo_rol'] <= 5 ) { //Rechaza si el administrador no es del grupito del chuchuchú
			$query = "select pago from tbl_transacciones where idtransaccion = '".$d['pagar']."'";
			$temp->query($query);
			if ($temp->f('pago') == 0) $pagar = 1; else $pagar = 0; //revierte el pago de la transacción por si 'hay metí la pata'
			$query = "update tbl_transacciones set pago = $pagar where idtransaccion = '".$d['pagar']."'";
			$temp->query($query);


            $query = "select c.nombre, t.fecha, t.valor_inicial valor
                        from tbl_comercio c, tbl_transacciones t
                        where  idtransaccion = '".$d['pagar']."'
                        and t.idcomercio = c.idcomercio ";
            $temp->query($query);
            $fecha = $temp->f('fecha');
            $comnombre = $temp->f('nombre');
            $valor = $temp->f('valor');

			$query = "select a.nombre, c.nombre comercio, email from tbl_comercio c, tbl_transacciones t, tbl_admin a
						where  idtransaccion = '".$d['pagar']."'
                        and t.idcomercio = c.idcomercio
						and  c.idcomercio = a.idcomercio
						and a.idrol = 11
						limit 0,1";
			$temp->query($query);
			$nombre = $temp->f('nombre');
			$email = $temp->f('email');
			$comercioN = $temp->f('comercio');

		} else $contenido = _AUTENT_NOSEPUEDE;
	}

	/*
	 * Preparación de los datos por defecto a mostrar en el Buscar
	 */
//	Comercio
	$query = "select idcomercio from tbl_comercio where activo = 'S'";
	$temp->query($query);
	$comercios = implode("', '", $temp->loadResultArray());

	$comer = $_SESSION['comercio'];
	if ($comer == 'todos' && $d['comercio']) $comercId = $d['comercio'];
	else if ($comer == 'todos' && !$d['comercio']) $comercId = $comercios;
	else if ($comer != 'todos') $comercId = $comer;

	if(is_array($comercId)) $comercId = implode('\', \'', $comercId);

//	Fechas y Horas
	if ($d['buscar']) {
//		echo $d['buscar'];
		$tira = explode('and', $d['buscar']);
		$fecha1 = date('d/m/Y', substr($tira[3], strlen($tira[3])-11));
		$fecha2 = date('d/m/Y', substr($tira[4], 0, 11));
	} else {
		$fecha1 = date('d/m/Y', mktime(0, 0, 0, -1, 1, date("Y")));
		$fecha2 = date('d/m/Y', time());
		if ($d['fecha1']) $fecha1 = $d['fecha1'];
		if ($d['fecha2']) $fecha2 = $d['fecha2'];
		
	}

	$mes1 = explode('/', $fecha1);
	$mes1 = 1*$mes1[1];
	$mes2 = explode('/', $fecha2);
	$mes2 = 1*$mes2[1];

	$d['tipo']? $esta = $d['tipo']:$esta = "V', 'D', 'S', 'Q', 'M";
	
	/*
	 * Construye el formulario de Buscar
	 */
	$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
					function verifica() {
						return true;
					}
					</script>";

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = _MENU_ADMIN_CIERRES;
	$html->tituloTarea = _REPORTE_TASK;
	$html->hide = true;
	$html->anchoTabla = 500;
	$html->anchoCeldaI = $html->anchoCeldaD = 245;
	if ($comer == 'todos') {
		$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
		$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query, $comercId, null, null, "multiple size='5'");
	}
	else $html->inHide ($comercId, 'comercio');
	$estadoArr = array(
		array("V', 'D', 'S', 'Q', 'M", _REPORTE_TODOS),
		array('V', _CIERRE_VALOR),
		array('D', _CIERRE_DIARIO),
		array('S', _CIERRE_SEMANAL),
		array('Q', _CIERRE_QUINCENAL),
		array('M', _CIERRE_MENSUAL)
	);
	$html->inSelect(_CIERRE_TIPO, 'tipo', 3, $estadoArr, $esta);
	$html->inFecha(_CIERRE_DESDE, $fecha1, 'fecha1');
	$html->inFecha(_CIERRE_HASTA, $fecha2, 'fecha2');

	echo $html->salida();
	/*
	 * Termina el formulario de buscar
	 */

	$vista = "select e.id, c.idcomercio, c.nombre comercio, e.fecha, e.fechaInicio, e.fechaFin, e.consecutivo, e.vinstal `vinstal{val}`, e.vmenconc `vmenconc{val}`, 
					e.vcosttarje `vcosttarje{val}`, e.vtransacciones `vtransacciones{val}`, e.vretrocobros `vretrocobros{val}`, e.vtransf `vtransf{val}`, 
					e.vswift `vswift{val}`, e.vcostobanc `vcostobanc{val}`, e.vtotal `vtotal{val}`, e.totalretro `totalretro{val}`, e.totalsdesc `totalsdesc{val}`, 
					e.total `total{val}`, e.fichero, 
					case e.tipo when 'V' then '"._CIERRE_VALOR."' when 'D' then '"._CIERRE_DIARIO."' when 'S' then '"._CIERRE_SEMANAL."' when 'Q' 
						then '"._CIERRE_QUINCENAL."' else '"._CIERRE_MENSUAL."' end tipo
			from tbl_cierres e, tbl_comercio c";

//	echo "NombreVal= $nombreVal<br>";

	$where = stripslashes("where c.idcomercio = e.idcomercio
				and e.fecha between ".to_unix($fecha1)." and ".(to_unix($fecha2))."
				and e.tipo in ('$esta')
				and c.idcomercio in ('$comercId')");
	if ($d['buscar']) $where = $d['buscar'];
	$orden = 'e.fecha desc, c.nombre';
//echo $where;

	$colEsp[] = array("e", _CIERRE_VCIERRE, "../images/edit.gif", _CIERRE_VCIERRE);

	$busqueda = array();
	$columnas = array(
					array(_COMERCIO_ID, "id", "50", "center", "left" ),
					array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left" ),
					array(_CIERRE_CONSECUTIVO, "consecutivo", "", "center", "left" ),
					array(_CIERRE_TIPO, "tipo", "", "center", "center"),
					array(_REPORTE_FECHA, "fecha", "", "center", "center" ),
					array(_CIERRE_FECHAINI, "fechaInicio", "", "center", "center" ),
					array(_CIERRE_FECHAFIN, "fechaFin", "", "center", "center" ),
					array(_CIERRE_INTEGRA, "vinsta{val}", "", "center", "right" ),
					array(_CIERRE_MENSUAL, "vmenconc{val}", "", "center", "right" ),
					array(_CIERRE_TARJETA, "vcosttarje{val}", "", "center", "right" ),
					array(_CIERRE_COMIS, "vtransacciones{val}", "", "center", "right" ),
					array(_CIERRE_RETROC, "vretrocobros{val}", "", "center", "right" ),
					array(_CIERRE_TRANSF, "vtransf{val}", "", "center", "right" ),
					array(_CIERRE_SWIFT, "vswift{val}", "", "center", "right" ),
					array(_CIERRE_COSTOB, "vcostobanc{val}", "", "center", "right" ),
					array(_CIERRE_DESC, "vtotal{val}", "", "center", "right" ),
					array(_CIERRE_DEVOL, "totalretro{val}", "", "center", "right" ),
					array(_CIERRE_TOTAL, "totalsdesc{val}", "", "center", "right" ),
					array(_CIERRE_PAGAR, "total{val}", "", "center", "right" )
				);


//	echo $query;
	$sumaLib += $temp->f('totalLib');
	$ancho = 1500;
	
	$querys = tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
//	echo $rec[0];

	if (strlen($_REQUEST["orden"]) > 0) $orden = $_REQUEST["orden"];
	else $orden = $orden;

}
?>

<!--
<table cellpadding="0" class="cierreTab" cellspacing="0" align="center">
	<tr>
		<td colspan="6" class="cierretop1">Cierre nº 7</td>
	</tr>
	<tr class="cierretop2">
		<td colspan="6"><div style="float: left; width: 250px;">Cliente: '.$comercio.'</div>
			<div style="float: left; width: 700px;">Año: '.$ano.' &nbsp;&nbsp;Mes: '.$mes1.'-'.$mes2.'  &nbsp;&nbsp;
			Desde el Día: '.$dia1.' a las '.$hora2.'h  &nbsp;&nbsp;Hasta el Día: '.$dia2.' a las '.$hora2.'h</div></td>
	</tr>
	<tr>
		<td width="200px" colspan="2"></td>
		<td class="cierreEurosT title"></td>
		<td class="cierreEurosT title" align="center">Valor E Inicial</td>
		<td class="cierreEurosT title" align="center">Valor E Devol</td>
		<td class="cierreEurosT title" align="center">Valor E</td>
	</tr>
	<tr>
		<td colspan="2">1- Resultado de operaciones en USD al cambio en Euros</td>
		<td class="cierreEurosT title" align="center">TOTALES</td>
		<td class="cierreEurosT" align="right">0</td>
		<td class="cierreEurosT" align="right">0</td>
		<td class="cierreEurosT" align="right">0</td>
	</tr>
	<tr>
		<td colspan="2">2- Resultado de operaciones en GBP al cambio en Euros</td>
		<td class="cierreEurosT title" align="center">TOTALES</td>
		<td class="cierreEurosT" align="right">0</td>
		<td class="cierreEurosT" align="right">0</td>
		<td class="cierreEurosT" align="right">0</td>
	</tr>
	<tr>
		<td colspan="2">3- Resultado de operaciones en Euros</td>
		<td class="cierreEurosT title" align="center">TOTALES</td>
		<td class="cierreEurosT" align="right">0</td>
		<td class="cierreEurosT" align="right">0</td>
		<td class="cierreEurosT" align="right">0</td>
	</tr>
	<tr class="cierreTotgen">
		<td colspan="2">Suma de las operaciones 1, 2, 3</td>
		<td class="cierreEurosT title" align="center">TOTALES</td>
		<td class="cierreEurosT" align="right">0</td>
		<td class="cierreEurosT" align="right">0</td>
		<td class="cierreEurosT" align="right">0</td>
	</tr>
	<tr>
		<td colspan="6" height="20"></td>
	</tr>
	<tr>
		<td colspan="6">
			<table cellpadding="0" cellspacing="0">
				<tr class="cierrePrim">
					<td width="200"></td>
					<td width="150"></td>
					<td width="150" align="center" class="cierretd cierreleft cierreazul cierrearriba">comisión</td>
					<td width="150" align="center" class="cierretd cierreazul cierrearriba">descuento</td>
				</tr>
				<tr>
					<td class="cierretd cierreleft cierreazul cierrearriba">valor e inicial total</td>
					<td class="cierretd cierrearriba">24,873.87 ?</td>
					<td class="cierretd">4.5</td>
					<td class="cierretd">1119.32</td>
				</tr>
				<tr>
					<td class="cierretd cierreleft cierreazul">VALOR ? DEVOLUCION</td>
					<td class="cierretd">0.00</td>
					<td class="cierretd">4.5</td>
					<td class="cierretd">0.00</td>
				</tr>
				<tr>
					<td class="cierretd cierreleft cierreazul">VALOR ?</td>
					<td class="cierretd">24,873.87 ?</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td class="cierretd cierreleft cierreazul cierrearriba">Número de transacciones</td>
					<td class="cierretd cierreazul cierrearriba">Costo &euro; / transacción</td>
					<td class="cierretd cierreazul cierrearriba">Total &euro; Transaciones</td>
					<td></td>
				</tr>
				<tr>
					<td class="cierretd cierreleft">35</td>
					<td class="cierretd">1</td>
					<td class="cierretd">35</td>
					<td></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2" class="cierrerojo">total amf</td>
					<td align="right" class="cierrerojo">1169.00</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="6" height="20">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="6">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td width="200" style="text-align: left;" class="cierretd cierreleft cierreazul cierrearriba">VALOR &euro;</td>
					<td width="100" class="cierretd cierrearriba"></td>
					<td width="150" class="cierretd cierrearriba">24879.00</td>
					<td width="250"></td>
				</tr>
				<tr>
					<td style="text-align: left;" class="cierretd cierreleft cierreazul">VALOR &euro;</td>
					<td class="cierretd"></td>
					<td class="cierretd">24879.00</td>
					<td></td>
				</tr>
				<tr>
					<td style="text-align: left;" class="cierretd cierreleft cierreazul">VALOR &euro;</td>
					<td class="cierretd"></td>
					<td class="cierretd">24879.00</td>
					<td></td>
				</tr>
				<tr>
					<td style="text-align: left;" class="cierretd cierreleft cierreazul">VALOR &euro;</td>
					<td class="cierretd"></td>
					<td class="cierretd">24879.00</td>
					<td></td>
				</tr>
				<tr>
					<td style="text-align: left;" class="cierreEurosT title">Subtotal</td>
					<td class="cierreEurosT title"></td>
					<td style="text-align: right;" class="cierreEurosT title">24879.00</td>
					<td></td>
				</tr>
				<tr>
					<td style="text-align: left;" class="cierretd cierreleft cierreazul">VALOR &euro;</td>
					<td class="cierretd"></td>
					<td class="cierretd">24879.00</td>
					<td></td>
				</tr>
				<tr>
					<td colspan="3" style="text-align: left;" class="cierrerojo">TOTAL CIERRE nº 30  SERVICIOS GLOBAL  DÍA 15 DE NOVIEMBRE A LAS 24:00H </td>
					<td style="text-align: right;" class="cierrerojo">23,456.88</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</table>-->