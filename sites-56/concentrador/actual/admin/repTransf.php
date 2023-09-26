<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$temp = @ new ps_DB;
$html = new tablaHTML;

// print_r($_SESSION);

$d = $_REQUEST;

if ($d['buscar']) {
	$tira = explode('and', $d['buscar']);
// 	print_r($tira);
	$fecha1 = date('d/m/Y', substr($tira[5], strlen($tira[5])-11));
	$fecha2 = date('d/m/Y', substr($tira[6], 0, 11));
	$mes1 = explode('/', $fecha1);
	$mes1 = 1*$mes1[1];
	$mes2 = explode('/', $fecha2);
	$mes2 = 1*$mes2[1];
} else {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, date("m"), 1, date("Y")));
	$fecha2 = date('d/m/Y', time());
	if ($d['fecha1']) $fecha1 = $d['fecha1'];
	if ($d['fecha2']) $fecha2 = $d['fecha2'];
	$mes1 = explode('/', $fecha1);
	$mes1 = 1*$mes1[1];
	$mes2 = explode('/', $fecha2);
	$mes2 = 1*$mes2[1];
}

if ($d['cambiar'] || $d['val']) {

	if ($d['cambiar'])
		$query = "select r.*, c.nombre comercio, (select nombre from tbl_admin a where r.id_admin = a.idadmin) admin 
						from tbl_reserva r, tbl_comercio c 
						where c.idcomercio = r.id_comercio 
							and id_reserva = ".$d['cambiar'];
	else
		$query = "select r.*, c.nombre comercio, (select nombre from tbl_admin a where r.id_admin = a.idadmin) admin 
						from tbl_reserva r, tbl_comercio c 
						where c.idcomercio = r.id_comercio 
							and codigo = '".$d['val']."'";
//	echo $query;
	$temp->query($query);
	$valoi = number_format($temp->f('valor_inicial'), 2,'.','');
	$valo = number_format($temp->f('valor'), 2,'.','');
//echo $valoi."/".$valo;
	if ($valo == $valoi) $muestra = false; else if ($valo == 0) $muestra = false; else $muestra = true;
	if ($temp->f('est_comer') == 'P') $entorno = _COMERCIO_ACTIVITY_PRO; elseif ($temp->f('est_comer') == 'D') $entorno = _COMERCIO_ACTIVITY_DES; else $entorno = '-';
	if ($temp->f('moneda') == '978') $moneda = '&#8364; ';
	if ($temp->f('moneda') == '840') $moneda = 'USD$ ';
	if ($temp->f('moneda') == '826') $moneda = '&pound; ';
	$monedaid = $temp->f('moneda');
	if ($temp->f('estado') == 'A') $estado = _REPORTE_ACEPTADA; else if ($temp->f('estado') == 'P') $estado = _REPORTE_PROCESO;
	else if ($temp->f('estado') == 'D') $estado = _REPORTE_DENEGADA;
	else if ($temp->f('estado') == 'N') $estado = _REPORTE_PROCESADA; else if ($temp->f('estado') == 'B') $estado = _REPORTE_ANULADA;
	else if ($temp->f('estado') == 'V') $estado = _REPORTE_DEVUELTA;
	$esta = $temp->f('estado');
	if ($temp->f('idioma') == 'es') $idio = _PERSONAL_ESP; else $idio = _PERSONAL_ING;
	$temp->f('pMomento') == 'S'  ? $tipoPago = _COMERCIO_ALMOMENT : $tipoPago = _COMERCIO_DIFERI;
	$temp->f('fechaPagada') == 0 ? $fechaPagada = '-' : $fechaPagada = date('d/m/Y H:i:s', $temp->f('fechaPagada'));
	$comercId = $temp->f('id_comercio');
	$nombreVal = $temp->f('codigo');
	$strVa = '<span style=\'cursor:pointer;text-decoration:underline;color:blue;\' 
			onclick=\'window.open("'._ESTA_URL.'/voucher.php?tr='.$nombreVal.'&co='.$comercId.'","_new");window.open("'._ESTA_URL.'/ticket.php?tr='.$nombreVal.'&co='.$comercId.'","_new2");\'>Ver</span>';
	
	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = _MENU_ADMIN_PAGO;
	$html->tituloTarea = 'Datos';
	$html->anchoTabla = 550;
	$html->anchoCeldaI = 310;
	$html->anchoCeldaD = 233;
	
	$html->inTexto(_REPORTE_REF_COMERCIO, $nombreVal);
	if ($temp->f('id_transaccion') != '0') $html->inTexto(_REPORTE_IDENTIFTRANS, "<a href='index.php?componente=comercio&pag=reporte&nombre=".
																	$temp->f('id_transaccion')."'>".$temp->f('id_transaccion')."</a>");
	if ($_SESSION['grupo_rol'] <= 5 ) $html->inTexto('Comercio', $temp->f('comercio'));
	$html->inTexto('Entorno del Comercio en el momento de la transacci&oacute;n', $entorno);
	$html->inTexto("Valor inicial", $moneda.$valoi);
	$html->inTexto('Valor', $moneda.$valo);
	$html->inTexto('Cliente', $temp->f('nombre'));
	$html->inTexto('Elaborado por', $temp->f('admin'));
	$html->inTexto('Correo', $temp->f('email'));
	$html->inTexto('Fecha Situada', (date('d/m/Y H:i:s', $temp->f('fecha'))));
	$html->inTexto('Fecha Pagada', $fechaPagada);
	$html->inTexto('Forma de Pago', $tipoPago);
	$html->inTexto('Idioma', $idio);
	$html->inTexto('Servicio', $temp->f('servicio'));
	$html->inTexto('Voucher o Comprobante del pago', $strVa);
	
	
	echo $html->salida('&nbsp;');
} else {

	$query = "select idcomercio from tbl_comercio where id in (".$_SESSION['idcomStr'].")";
	$temp->query($query);
	$comercios = implode(",", $temp->loadResultArray());
	$comer = $comercios;

	if (isset($d['comercio'])) $comercId = str_replace ("'", "", $d['comercio']);
	else {
		if ($comer == 'todos') $comercId = $comercios;
		else $comercId = $comer;
	}
	
	$query = "select id from tbl_empresas";
	$temp->query($query);
	$empresas = implode("','", $temp->loadResultArray());
	
	if (isset ($d['empresa'])) $empresaid = implode(",", $d['empresa']);
	else $empresaid = $empresas;
	
	if (isset($d['usr'])) $usrId = implode(',',$d['usr']);
	else{
		$q = "select distinct a.idadmin id from tbl_admin a, tbl_colAdminComer c where c.idAdmin = a.idadmin and c.idComerc in ({$_SESSION['idcomStr']})";
		$temp->query($q);
		$usrId = implode(",", $temp->loadResultArray());
	}

	if(is_array($comercId)) $comercId = implode(',', $comercId);
	$comercId = str_replace ("'", "", $comercId);
	if(is_array($usrId)) $usrId = implode(',', $usrId);
	
	//Si así lo pide el comercio los vendedores sólo ven sus operaciones
	if ($_SESSION['rol'] == 12 || $_SESSION['rol'] == 14) {
		$q = "select distinct vendventodo from tbl_comercio where id in (".$_SESSION['idcomStr'].")";
		$temp->query($q);
		$arrSal = implode(",", $temp->loadResultArray());
		if (strstr($arrSal, 'N'))
			$usrId = $_SESSION['id'];
	}

	$modoVal = "'P'";
	$nombreVal = '';

	$mes1 = explode('/', $fecha1);
	$mes1 = 1*$mes1[1];
	$mes2 = explode('/', $fecha2);
	$mes2 = 1*$mes2[1];

//echo $d['moneda'];
	$d['estado']? $esta = $d['estado']:$esta = "V','B','A','D','P" ;
	$d['monedas']? $monedaid = $d['monedas']:$monedaid = "978', '840', '826";
	if ($d['modo']) $modoVal = stripslashes($d['modo']);
	if ($d['nombre']) $nombreVal = $d['nombre'];
	
	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = _MENU_ADMIN_REPTRANS;
	$html->tituloTarea = _REPORTE_TASK;
	$html->hide = true;
	$html->anchoTabla = 500;
	$html->anchoCeldaI = 243;
	$html->anchoCeldaD = 250;

	$html->inTextb(_REPORTE_REF_COMERCIO, $nombreVal, 'nombre');
	$html->inTextb('Cliente', '', 'nomcliente');
	if ($_SESSION['rol'] != 12 && $_SESSION['rol'] != 14){
		$html->inTextb('Usuario', '', 'nomusuario');
	}
	if ($comer == 'todos') {
		$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
		$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query,  str_replace(",", "', '", $comercId), null, null, "multiple size='5'");
	} elseif (strpos ($comer, ",")) {
		$query = "select idcomercio id, nombre from tbl_comercio where idcomercio in (".$comer.") and activo = 'S' order by nombre";
//		echo $query;
		$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query,  str_replace(",", "', '", $comercId), null, null, "multiple size='5'");
	} else $html->inHide ($comercId, 'comercio');
	
	
	$query = "select distinct e.id, e.nombre from tbl_empresas e, tbl_transacciones t, tbl_pasarela p, tbl_comercio c where t.pasarela = p.idPasarela 
				and e.id = p.idempresa and t.idcomercio = c.idcomercio and c.idcomercio in ($comer) order by e.nombre";
	$html->inSelect('Empresa', 'empresa', 5, $query,  str_replace(",", "', '", $empresaid), null, null, "multiple size='3'");
	
	$query = "select idmoneda id, moneda nombre from tbl_moneda";
	$html->inSelect(_COMERCIO_MONEDA, 'monedas', 5, $query, $monedaid);
//	$estadoArr = array(
//		array("P', 'A', 'D', 'N', 'B', 'V", _REPORTE_TODOS),
//		array('P', _REPORTE_PROCESO),
//		array('A', _REPORTE_ACEPTADA),
//		array('D', _REPORTE_DENEGADA),
//		array('N', _REPORTE_PROCESADA),
//		array('B', _REPORTE_ANULADA),
//		array('V', _REPORTE_DEVUELTA),
//		array("V','B','A", _REPORTE_ACEPTADA.' - '._REPORTE_DEVUELTA.' - '._REPORTE_ANULADA)
//	);
//	$html->inSelect(_REPORTE_STATUS, 'estado', 3, $estadoArr, $esta);
	$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1',null,null);
	$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2',null,null);
	
	echo $html->salida();

}

$tabView = $pasarView = $whereView = '';
if ($_SESSION['grupo_rol'] > 3) {
	$pasarView = "case (select secure from tbl_pasarela where idPasarela = t.pasarela) when 1 then 'Segura' else 'NO Segura' end pasarela";
} else {
	$pasarView = "p.nombre pasarela";
}
$usrId = str_replace("'", "", $usrId);
$vista = "select t.idTransf id, t.idCom, c.nombre 'comercio', t.idadmin, a.nombre 'usr', t.cliente, t.email, t.facturaNum, t.fecha, 
r.fecha_mod, (t.valor/100) 'valor{val}', (r.valor/100/r.tasa) 'euroEquiv{val}', case t.estado when 'P' then 'Pendiente' when 'A' then 'Aceptada' end 'estad', 
t.moneda, m.moneda, t.idPasarela, p.nombre 'pasarela', case t.activa when 1 then 'Si' else 'No' end 'activa', 
case t.estado when 'P' then if (t.enviada = 1, 'blue', 'olive') else 'black' end 'color{col}',
case t.vista when 1 then 'Vista' else '-' end 'vista', case t.enviada when 1 then 'Enviada' else 'Pendiente' end 'enviada '";
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 24 || $_SESSION['rol'] == 16 || $_SESSION['rol'] == 10) {
	$vista .= ", e.nombre empresa ";
}
$from = " from tbl_transferencias t, tbl_comercio c, tbl_admin a, tbl_moneda m, tbl_pasarela p, tbl_transacciones r, tbl_empresas e";
$where = " where t.idCom = c.id and t.idadmin = a.idadmin and t.moneda = m.idmoneda and p.idPasarela = t.idPasarela and r.idtransaccion = t.idTransf and e.id = p.idempresa ";
if ($nombreVal) $where .= " and t.facturaNum like '%$nombreVal%'
							and t.idcomercio in ($comercId)
							and e.id in ('$empresaid')";
else if ($d['nomcliente']) $where .= " and t.cliente like '%{$d['nomcliente']}%'
							and t.idcomercio in ($comercId)
							and e.id in ('$empresaid')";
else if ($d['nomusuario']) $where .= " and a.nombre like '%{$d['nomusuario']}%'
							and t.idcomercio in ($comercId)
							and e.id in ('$empresaid')";
else
	$where .= stripslashes(" and t.moneda in ('$monedaid')
				and (t.fecha between ".to_unix($fecha1)." and ".(to_unix($fecha2)+86400)." 
					or t.fechaTransf between ".to_unix($fecha1)." and ".(to_unix($fecha2)+86400).")
				and t.estado in ('$esta')
				and c.idcomercio in ($comercId)
				and t.idadmin in ($usrId)
				and e.id in ('$empresaid')");
$orden = ' t.fecha desc, c.nombre';
// echo $where;
//echo $vista.$from.$where;
$colEsp = array(
				array("e", "Ver Data", "css_edit", "Ver")
				, array("m", "Recarga Transferencia", "css_transf","Ver")
//				array("c", _GRUPOS_ANULA_DATA, "css_borra", _TAREA_ANULAR)
			);
$busqueda = array();
$columnas[] = array('', "color{col}", "", "center", "center" );
$columnas[] = array(_REPORTE_IDENTIFTRANS, "id", "100", "center", "center" );
$columnas[] = array(_REPORTE_REF_COMERCIO, "facturaNum", "", "center", "left" );
$columnas[] = array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left" );
$columnas[] = array(_REPORTE_CLIENTE, "cliente", "150", "center", "left" );
if ($_SESSION['rol'] != 12 && $_SESSION['rol'] != 14) $columnas[] = array(_PERSONAL_IDENT, "usr", "150", "center", "left" );
if ($_SESSION['grupo_rol'] < 4) array_push($columnas, array('Empresa', "empresa", "150", "center", "left" ));
$columnas[] = array(_REPORTE_FECHA, "fecha", "", "center", "center" );
$columnas[] = array(_REPORTE_FECHA_MOD, "fecha_mod", "", "center", "center" );
$columnas[] = array(_REPORTE_VALOR, "valor{val}", "60", "center", "right" );
$columnas[] = array(_COMERCIO_MONEDA, "moneda", "", "center", "center");
$columnas[] = array(_COMERCIO_EUROSC, "euroEquiv{val}", "60", "center", "right");
$columnas[] = array(_COMERCIO_PASARELA, "pasarela", "", "center", "left" );
$columnas[] = array("Activa?", "activa", "", "center", "center" );
$columnas[] = array(_REPORTE_ESTADO, "estad", "", "center", "center" );

$query = "select
			sum(case t.estado
				when 'B' then (
					if ($mes1 > FROM_UNIXTIME(t.fecha, '%c') , (-1 * ((r.valor_Inicial-r.valor))) , (r.valor) ))
				when 'V' then (
					if ($mes1 > FROM_UNIXTIME(t.fecha, '%c') , (-1 * ((r.valor_Inicial-r.valor))) , (r.valor) ) )
				else (r.valor) end) total
		".$from.stripslashes($where);
$query1 = $query."	and t.moneda = '978'";
$temp->query($query1);
$sumaEuros += $temp->f('total');

$query2 = $query."	and t.moneda = '840'";
$temp->query($query2);
$sumaUsd += $temp->f('total');

$query3 = $query."	and t.moneda = '826'";
$temp->query($query3);
$sumaLib += $temp->f('total');
$ancho = 1300;

if ($_SESSION['rol'] != 17) {
    echo "<div style='float:left; width:100%' ><table class='total1' width=\"$ancho\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
        <tr>
            <td><div class='total2'><strong>"._REPORTE_TOTAL.": &#8364;</strong>".number_format($sumaEuros, 2)."
                    &nbsp;&nbsp; <strong>$ </strong>".number_format($sumaUsd, 2)."
                    &nbsp;&nbsp; <strong>&pound; </strong>".number_format($sumaLib, 2)."</div></td>
            <td><span onclick='document.imprime.submit()' onmouseover='this.style.cursor=\"pointer\"' alt=\""._REPORTE_PRINT."\" title=\""._REPORTE_PRINT
            ."\" class=\"css_document-print\"></span>&nbsp;&nbsp;&nbsp;
                <span onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' class=\"css_x-office-document\" alt='"._REPORTE_CSV
            ."' title='"._REPORTE_CSV."'></span></td>
        </tr>
    </table></div>";
}

//echo $vista.$from.$where.' order by '.$orden;
$querys = tabla( $ancho, 'E', $vista.$from, $orden, $where, $colEsp, $busqueda, $columnas );

if (strlen($_REQUEST["orden"]) > 0) $orden = $_REQUEST["orden"];
else $orden = $orden;
$_SESSION['columnas'] = $columnas;

$querys1 = str_replace(' limit 0, 30', '', stripslashes($querys));
$querys1 = str_replace(' end estCom', ' end estCom, servicio', $querys1);
?>
<form target="_blank" name="imprime" action="componente/comercio/print.php" method="POST">
	<input type="hidden" name="querys" value="<?php echo $querys1 ?>">
	<input type="hidden" name="salida" value="2">
	<input type="hidden" name="idioma" value="<?php echo $_SESSION['idioma']?>">
</form>
<form name="exporta" action="impresion.php" method="POST">
	<input type="hidden" name="querys3" value="<?php echo $querys1 ?>">
	<input type="hidden" name="fecha1" value="<?php echo $d['fecha1'] ?>">
	<input type="hidden" name="fecha2" value="<?php echo $d['fecha2'] ?>">
	<input type="hidden" name="moneda" value="<?php echo stripslashes($d['moneda']) ?>">
	<a href="../../lang/spanish.php"></a>
	<input type="hidden" name="comercio" value="<?php echo ($d['comercio']) ?>">
	<input type="hidden" name="modo" value="<?php echo stripslashes($d['modo']) ?>">
	<input type="hidden" name="nombre" value="<?php echo $d['nombre'] ?>">
</form>