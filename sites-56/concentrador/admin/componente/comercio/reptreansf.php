<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
global $temp;
$html = new tablaHTML;
global $send_m;

$d = $_REQUEST;
print_r($d);
if ($d['cambiar']) {echo "entraacá";

	if ($_SESSION['grupo_rol'] <= 5 ) {

		$query = "select t.valor, t.idcomercio, c.nombre from tbl_transacciones t, tbl_comercio c
							where t.idcomercio = c.idcomercio and idtransaccion = '".$d['cambiar']."'";
		$temp->query($query);
		$valo = money_format('%i', $temp->f('valor')/100);

		$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
					function verifica() {
						if (
								(checkField (document.admin_form.valor, isMoney, ''))
							) {

							if ((document.admin_form.valante.value - document.admin_form.valor.value) >= 0) {
								return true;
							}
							else {
								alert('"._REPORTE_NOPUEDO."');
							}
						}
						return false;
					}
					</script>";

		$html->idio = $_SESSION['idioma'];
		$html->tituloPag = _REPORTE_DESCUENTO_TITLE;
		$html->tituloTarea = "&nbsp;";
		$html->anchoTabla = 400;
		$html->anchoCeldaI = 200;
		$html->anchoCeldaD = 190;
		$html->inTextb(_REPORTE_IDENTIFTRANS, $d['cambiar'], 'iddentinf', null, null, "readonly='true'");
		$html->inHide($temp->f('idcomercio'), 'nomdbre');
		$html->inHide(true, 'devolc');
		$html->inTextb(_INICIO_COMERCIO, $temp->f('nombre'), 'nomdbreCOM', null, null, "readonly='true'");
		$html->inTextb(_REPORTE_VALOR, $valo, 'valante', null, null, "readonly='true'");
		$html->inTextb(_REPORTE_DESCUENTO, null, 'valor');
		$contenido .=  $html->salida();
		
	} else {
		$contenido = _AUTENT_NOSEPUEDE;
	}
	echo $contenido;

	
} else {

	if ($d['pagar']) {echo "entra";
		/*
		* Paga la transaccion al comercio
		*/
		if ($_SESSION['grupo_rol'] <= 5 ) { //Rechaza si el administrador no es del grupito del chuchuchï¿½
			$query = "select pago from tbl_transacciones where idtransaccion = '".$d['pagar']."'";
			$temp->query($query);
			if ($temp->f('pago') == 0) $pagar = 1; else $pagar = 0; //revierte el pago de la transacciÃ³n por si 'hay metÃ­Â­ la pata'
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
//	Pasarela
//	$query = "select idPasarela from tbl_pasarela";
//	$temp->query($query);
////	$listPasar = $temp->loadResultArray();
//	$listPasar = implode("', '", $temp->loadResultArray());

//	Comercio
	$query = "select idcomercio from tbl_comercio where activo = 'S'";
	$temp->query($query);
	$comercios = implode("','", $temp->loadResultArray());

	$comer = $_SESSION['comercio'];
	if (isset ($d['comercio'])) {
		$comercId = $d['comercio'];
	} else {
		if ($comer == 'todos') $comercId = $comercios;
		else if ($comer != 'todos') $comercId = $comer;
	}
	

	if(is_array($comercId)) $comercId = implode(',', $comercId);
	$comercId = str_replace("'", "", $comercId);

//	Fechas y Horas
	if ($d['buscar']) {
//		echo $d['buscar'];
		$tira = explode('and', $d['buscar']);
		$fecha1 = date('d/m/Y', substr($tira[3], strlen($tira[3])-11));
		$fecha2 = date('d/m/Y', substr($tira[4], 0, 11));
	} else {
		$fecha1 = date('d/m/Y', mktime(0, 0, 0, date("m"), 1, date("Y")));
		$fecha2 = date('d/m/Y', time());
		if ($d['fecha1']) $fecha1 = $d['fecha1'];
		if ($d['fecha2']) $fecha2 = $d['fecha2'];
		
	}

	$modoVal = 'P';
	$nombreVal = '';

	$mes1 = explode('/', $fecha1);
	$mes1 = 1*$mes1[1];
	$mes2 = explode('/', $fecha2);
	$mes2 = 1*$mes2[1];

	$d['horasin']? $hora1 = $d['horasin']:$hora1 = '00:00';
	$d['horasout']? $hora2 = $d['horasout']:$hora2 = '24:00';
	$d['estado']? $esta = $d['estado']:$esta = "P','A";
	$d['monedas']? $monedaid = $d['monedas']:$monedaid = "0";
	$d['pasarela']? $pasarelaid = $d['pasarela']:$pasarelaid = "0";
	if ($d['modo']) $modoVal = stripslashes($d['modo']);
	if ($d['nombre']) $nombreVal = $d['nombre'];
	if ($d['id']) $id = $d['id'];
	
	/*
	 * Construye el formulario de Buscar
	 */
	$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
					function verifica() {
						return true;
					}
					</script>";

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = _MENU_ADMIN_TRANSFERENCIA;
	$html->tituloTarea = _REPORTE_TASK;
	$html->hide = true;
	$html->anchoTabla = 500;
	$html->anchoCeldaI = $html->anchoCeldaD = 245;
	$html->inHide(true, 'query');
	$html->inTextb(_REPORTE_TRANSFERENCIA, $nombre, 'nombre');
	$html->inTextb(_REPORTE_TRANSFERENCIA_ID, $id, 'id');
	
	if ($comer == 'todos') {
		$query = "select idcomercio id, nombre from tbl_comercio order by nombre";
		$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query, $comercId, null, null, "multiple size='5'");
	} elseif (strpos($comer, ',')) {
		$query = "select idcomercio id, nombre from tbl_comercio where idcomercio in ($comer) order by nombre";
		$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query, $comercId, null, null, "multiple size='5'");
	} else $html->inHide ($comercId, 'comercio');
	
	$arrM[] = array('0', _REPORTE_TODOS);
	$query = "select idmoneda id, moneda nombre from tbl_moneda";
	$temp->query($query);
	while($temp->next_record()) {
		$arrM[] = array($temp->f('id'), $temp->f('nombre'));
	}
	$html->inSelect(_COMERCIO_MONEDA, 'monedas', 3, $arrM, $monedaid);
	
	$arrT[] = array('0', _REPORTE_TODOS);
	$query = "select idPasarela id, nombre from tbl_pasarela where tipo = 'T' order by nombre";
	$temp->query($query);
	while($temp->next_record()){
		$arrT[] = array($temp->f('id'), $temp->f('nombre'));
	}
	$html->inSelect(_COMERCIO_PASARELA, 'pasarela', 3, $arrT, $pasarelaid);
	
	$estadoArr = array(
		array("P', 'A", _REPORTE_TODOS),
		array('P', _REPORTE_PROCESO),
		array('A', _REPORTE_ACEPTADA)
	);
	$html->inSelect(_REPORTE_TRANSFERENCIA_ESTADO, 'estado', 3, $estadoArr, $esta);
//	$horasArr = array(
//		array('00:00', '00:00'),array('01:00', '01:00'),array('02:00', '02:00'),array('03:00', '03:00'),array('04:00', '04:00'),array('05:00', '05:00'),
//		array('06:00', '06:00'),array('07:00', '07:00'),array('08:00', '08:00'),array('09:00', '09:00'),array('10:00', '10:00'),array('11:00', '11:00'),
//		array('12:00', '12:00'),array('13:00', '13:00'),array('14:00', '14:00'),array('15:00', '15:00'),array('16:00', '16:00'),array('17:00', '17:00'),
//		array('18:00', '18:00'),array('19:00', '19:00'),array('20:00', '20:00'),array('21:00', '21:00'),array('22:00', '22:00'),array('23:00', '23:00'),
//		array('24:00', '24:00')
//	);
//	$ver = "&nbsp;<select name='horasin'>";
//	$ver .= opciones_arr($horasArr, $hora1);
//	$ver .= "</select>";
	$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', null, null, null, null, $ver);
//	$ver = "&nbsp;<select name='horasout'>";
//	$ver .= opciones_arr($horasArr, $hora2);
//	$ver .= "</select>";
	$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', null, null, null, null, $ver);

	echo $html->salida();
	/*
	 * Termina el formulario de buscar
	 */

	$vista = "select idTransf id, c.nombre comercio, cliente, transferNum, 
				case t.idPasarela when null then null else (select nombre from tbl_pasarela where idPasarela = t.idPasarela) end pasarela, 
				case t.moneda when null then null else (select moneda from tbl_moneda where idmoneda = t.moneda) end moneda, 
				case t.estado when 'P' then 'green' when 'A' then 'black' when 'D' then 'red' when 'N' then 'red' when 'B' then 'blue' else 'blue' end `color{col}`,
				t.fecha, t.fechaTransf, (t.valor/100) `valIni{val}`, 
				case tipoEntorno when 'P' then '"._COMERCIO_ACTIVITY_PRO."' else '"._COMERCIO_ACTIVITY_DES."' end tipoEntorno,
				case t.estado when 'P' then '"._REPORTE_PROCESO."' when 'A' then '"._REPORTE_ACEPTADA."' when 'D' then '"._REPORTE_DENEGADA."'
					when 'N' then '"._REPORTE_PROCESADA."' when 'B' then '"._REPORTE_ANULADA."' else '"._REPORTE_DEVUELTA."' end estad, t.estado
				from tbl_transferencias t, tbl_comercio c where 1  = 1 ";
	
//	$vista1 = "select idtransaccion id, c.nombre comercio, identificador, codigo, m.moneda, t.id_error error,
//				case (select count(*) from tbl_reserva r where r.codigo = t.identificador) when 1 then
//					(select nombre from tbl_reserva r where r.codigo = t.identificador ) else ' - ' end cliente,
//				t.fecha, t.fecha_mod, p.nombre pasarela,
//				case t.estado
//					when 'B' then (
//						if ($mes1 > FROM_UNIXTIME(t.fecha, '%c') , (-1 * ((valor_Inicial-valor)/100)) , (valor/100) ))
//					when 'V' then (
//						if ($mes1 > FROM_UNIXTIME(t.fecha, '%c') , (-1 * ((valor_Inicial-valor)/100)) , (valor/100) ) )
//					else (valor/100) end `valor{val}`,
//				(t.valor_inicial/100) `valIni{val}`, 
//				case t.estado when 'B' then ((valor_Inicial-valor)/100) when 'V' then ((valor_Inicial-valor)/100) else 0 end valorDev,
//				case tipoEntorno when 'P' then '"._COMERCIO_ACTIVITY_PRO."' else '"._COMERCIO_ACTIVITY_DES."' end tipoEntorno,
//				case t.estado when 'P' then '"._REPORTE_PROCESO."' when 'A' then '"._REPORTE_ACEPTADA."' when 'D' then '"._REPORTE_DENEGADA."'
//					when 'N' then '"._REPORTE_PROCESADA."' when 'B' then '"._REPORTE_ANULADA."' else '"._REPORTE_DEVUELTA."' end estad, t.estado, tasa, tasaDev,
//				euroEquiv
//				from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_pasarela p ";

//	echo "NombreVal= $nombreVal<br>";
	if ($nombreVal) { //echo "entra";
		$where = "	and idTransf like '%$nombreVal%'
					and t.idcomercio = c.idcomercio
					and t.idcomercio in ($comercId) ";
	} elseif ($nombreVal) { //echo "entra";
		$where = "	and idTransf like '%$nombreVal%'
					and t.idcomercio = c.idcomercio
					and t.idcomercio in ($comercId) ";
	} else {
		$fecha1 = $fecha1." ".$hora1.":00";
		$fecha2 = $fecha2." ".$hora2.":59";
//		echo "fecha2=$fecha2";
		$where = stripslashes(" and c.idcomercio = t.idcomercio");
		if ($pasarelaid != 0) $where .= stripslashes(" and t.idPasarela in ('$pasarelaid')");
		if ($monedaid != 0) $where .= stripslashes(" and t.moneda in ('$monedaid')");
		$where .= stripslashes(" and t.estado in ('$esta')
					and c.idcomercio in ($comercId)");}
	if ($d['buscar']) $where = $d['buscar'];
	$orden = 'fechaTransf desc, c.nombre';
//echo $where;

	$colEsp[] = array("t", _GRUPOS_FACTURA, "../images/transf.gif", _TAREA_ANULAR);
	if ($comer == 'todos') {
		$colEsp[] = array("d", _GRUPOS_DEVUELVE_DATA, "../images/edit.gif", _TAREA_DEVUELTA);
		$colEsp[] = array("p", _GRUPOS_PAGA_COMERCIO, "../images/dollar3.gif", _TAREA_PAGADA);
		$colEsp[] = array("c", _GRUPOS_ANULA_DATA, "../images/borra.gif", _TAREA_ANULAR);


	}
	$busqueda = array();
//
////	Salva o llama la query salvada
//	if (!$d['query'] && !$d["orden"]) {
////		Si no se hace la busqueda, cargo la query salvada en la BD 
//		$query = "select query from tbl_admin where idadmin = ". $_SESSION['id'];
//		$temp->query($query);
//		
//		if (strlen($temp->f('query')) > 0 && $_SESSION['usequery'] == 'S') {
//			$salQuery = html_entity_decode($temp->f('query'), ENT_QUOTES);
//
//			$pos = strrpos($salQuery, ' order by ');
//			$pos2 = strrpos($salQuery, ' where ');
//			$orden = substr($salQuery, $pos+10);
//			$where = str_replace(" order by ".$orden, '', substr($salQuery, $pos2));
//			$vista = substr($salQuery, 0, $pos2);
//		}
//	} else {
//		
//		if (strlen($d["orden"]) == 0)$arrOrden = " order by ".$orden;
//		else $arrOrden = " order by ".$d['orden'];
//		$conve = htmlentities($vista . $where . $arrOrden, ENT_QUOTES);
//		$query = "update tbl_admin set query = '$conve' where idadmin = ". $_SESSION['id'];
//		$temp->query($query);
//		
//	}

//	echo $fecha2;
	if (strlen($nombreVal) == 0 && strlen($ip) == 0) {
		$where .= "and (fecha between ".to_unix($fecha1)." and ".(to_unix($fecha2))."
					or t.fechaTransf between ".to_unix($fecha1)." and ".(to_unix($fecha2)).")";
	}
	
	$query = "select
				sum(case t.estado
					when 'B' then (
						if ($mes1 > FROM_UNIXTIME(t.fecha, '%c') , (-1 * ((valor_Inicial-valor)/100)) , (valor/100) ))
					when 'V' then (
						if ($mes1 > FROM_UNIXTIME(t.fecha, '%c') , (-1 * ((valor_Inicial-valor)/100)) , (valor/100) ) )
					else (valor/100) end) totalEuros
			from tbl_transferencias t, tbl_comercio c ".stripslashes($where)."
			and t.moneda = '978'";
	$temp->query($query);
	$sumaEuros += $temp->f('totalEuros');

	$query = "select
				sum(case t.estado
					when 'B' then (
						if ($mes1 > FROM_UNIXTIME(t.fecha, '%c') , (-1 * ((valor_Inicial-valor)/100)) , (valor/100) ))
					when 'V' then (
						if ($mes1 > FROM_UNIXTIME(t.fecha, '%c') , (-1 * ((valor_Inicial-valor)/100)) , (valor/100) ) )
					else (valor/100) end) totalUsd
			from tbl_transferencias t, tbl_comercio c ".stripslashes($where)."
			and t.moneda = '840'";
	$temp->query($query);
	$sumaUsd += $temp->f('totalUsd');

	$query = "select
				sum(case t.estado
					when 'B' then (
						if ($mes1 > FROM_UNIXTIME(t.fecha, '%c') , (-1 * ((valor_Inicial-valor)/100)) , (valor/100) ))
					when 'V' then (
						if ($mes1 > FROM_UNIXTIME(t.fecha, '%c') , (-1 * ((valor_Inicial-valor)/100)) , (valor/100) ) )
					else (valor/100) end) totalLib
			from tbl_transferencias t, tbl_comercio c ".stripslashes($where)."
			and t.moneda = '826'";
	$temp->query($query);
//	echo $query;
	$sumaLib += $temp->f('totalLib');
	$ancho = 1500;

	echo "<div style='float:left; width:100%' ><table class='total1' width=\"100%\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
		<tr>
			<td><div class='total2'><strong>"._REPORTE_TOTAL.": &#8364;</strong>".number_format($sumaEuros, 2)."
						&nbsp;&nbsp; <strong>$ </strong>".number_format($sumaUsd, 2)."
						&nbsp;&nbsp; <strong>&pound; </strong>".number_format($sumaLib, 2)."</div></td>
			<td><!--<img onclick='document.imprime.submit()' onmouseover='this.style.cursor=\"pointer\"' alt=\""._REPORTE_PRINT."\" title=\""._REPORTE_PRINT.
				"\" src=\"../images/document-print.png\" width=\"22\" height=\"22\" />&nbsp;&nbsp;&nbsp;
				<img onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' src=\"../images/x-office-document.png\" alt='"._REPORTE_CSV."1' title='"
				._REPORTE_CSV."1' width=\"22\" height=\"22\" />&nbsp;&nbsp;&nbsp;
				<img onclick='document.exporta2.submit()' onmouseover='this.style.cursor=\"pointer\"' src=\"../images/x-office-document.png\" alt='"._REPORTE_CSV."2' title='"
				._REPORTE_CSV."2' width=\"22\" height=\"22\" />--></td>
		</tr>
	</table></div>";
	
	//columnas a mostrar
	$columnas = array(
				array('', "color{col}", "1", "center", "center" ),
				array(_REPORTE_TRANSFERENCIA, "id", "50", "center", "left" ),
				array(_REPORTE_TRANSFERENCIA_ID, "transferNum", "50", "center", "left" ));

	if ($comer == 'todos' || strpos($comer, ',')) array_push($columnas, array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left" ));
	array_push($columnas, 
					array(_COMERCIO_PASARELA, "pasarela", "60", "center", "left" ),
					array(_REPORTE_FECHA, "fecha", "135", "center", "center" ),
					array(_REPORTE_FECHA_MOD, "fechaTransf", "135", "center", "center" ),
					array(_REPORTE_VALOR, "valor{val}", "65", "center", "right" ),
					array(_COMERCIO_MONEDA, "moneda", "60", "center", "center"),
					array(_REPORTE_ESTADO, "estad", "65", "center", "center" ));
	
	
	$querys = tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );

	if (strlen($_REQUEST["orden"]) > 0) $orden = $_REQUEST["orden"];
	else $orden = $orden;

	$querCvs = '';
}
?><!--
<form target="_blank" name="imprime" action="componente/comercio/print.php" method="POST">
	<input type="hidden" name="querys" value="<?php echo $vista1.$where." order by ".$orden ?>">
	<input type="hidden" name="salida" value="1">
	<input type="hidden" name="idioma" value="<?php echo $_SESSION['idioma'] ?>">
</form>
<form name="exporta" action="impresion.php" method="POST">
	<input type="hidden" name="querys6" value="<?php echo $vista1.$where." order by ".$orden ?>">
	<input type="hidden" name="fecha1a" value="<?php echo $d['fecha1'] ?>">
	<input type="hidden" name="fecha2a" value="<?php echo $d['fecha2'] ?>">
	<input type="hidden" name="moneda" value="<?php echo stripslashes($d['moneda']) ?>">
	<input type="hidden" name="comercio" value="<?php echo ($d['comercio']) ?>">
	<input type="hidden" name="modo" value="<?php echo stripslashes($d['modo']) ?>">
	<input type="hidden" name="nombre" value="<?php echo $d['nombre'] ?>">
	<input type="hidden" name="pag" value="reporte">
</form>
<form name="exporta2" action="impresion.php" method="POST">
	<input type="hidden" name="pag" value="reporte">
	<input type="hidden" name="querys2" value="<?php echo $vista1.$where." order by ".$orden ?>">
	<input type="hidden" name="fecha1b" value="<?php echo $d['fecha1'] ?>">
	<input type="hidden" name="fecha2b" value="<?php echo $d['fecha2'] ?>">
	<input type="hidden" name="moneda" value="<?php echo stripslashes($d['moneda']) ?>">
	<input type="hidden" name="comercio" value="<?php echo ($d['comercio']) ?>">
	<input type="hidden" name="modo" value="<?php echo stripslashes($d['modo']) ?>">
	<input type="hidden" name="nombre" value="<?php echo $d['nombre'] ?>">
</form>-->
