<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); 

$temp = new ps_DB;
$ent = new entrada;
$html = new tablaHTML;

$d=$_REQUEST;
//print_r($_SESSION);
//print_r($d);

//Para insertar 
if ($ent->isBoolean($d['inserta'])) {
	if ($tempo = $ent->isEntero($d['tempor'], 4)) {
		$query = "select fechaIni, fechaFin from tbl_temporada where id = $tempo";
		$temp->query($query);
		$fecha1 = $temp->f('fechaIni');
		$fecha2 = $temp->f('fechaFin');
	} else {
		$fecha1 = to_unix($ent->isDate($d['fecha1']));
		$fecha2 = to_unix($ent->isDate($d['fecha2']));
	}
	$nombres = $ent->isAlfanumerico($d['nombre'], 150);
	$descrp = $ent->isAlfanumerico($d['descr'], 300);
	$mod = $d['modifi'];
	$val = $ent->isNumero($d['valor'], 9);
	$busc = $ent->isAlfabeto($d['busc'], 1);
	$opc = $ent->isAlfabeto($d['opcional'], 1);
	$diar = $ent->isAlfabeto($d['diario'], 1);
	if ($_SESSION['rol'] > 10 ) $comer = $_SESSION['comercio']; else  $comer = $ent->isEntero($d['comercio']);
	$usuar = implode(',', $d['usuarios']);

	$query = "insert into tbl_caracteristicas (id, idCom, idAdm, nombre, descripcion, precioModifica, precio, fechaIni, fechaFin, buscable, diario, fecha, usuarios, opcional) 
				values (null, '$comer', ".$_SESSION['id'].", '$nombres', '$descrp', '$mod', $val, $fecha1, $fecha2, '$busc', '$diar', ".time().", '$usuar', '$opc')";
//	echo "<br>$query";
	$temp->query($query);
	
}

//modificar 
if ($ident = $ent->isBoolean($d['modifica'])) {
	$fecha1 = to_unix($ent->isDate($d['fecha1']));
	$fecha2 = to_unix($ent->isDate($d['fecha2']));
	$nombres = $ent->isAlfanumerico($d['nombre'], 150);
	$descrp = $ent->isAlfanumerico($d['descr'], 300);
	$mod = $d['modifi'];
	$val = $ent->isNumero($d['valor'], 9);
	$busc = $ent->isAlfabeto($d['busc'], 1);
	$opc = $ent->isAlfabeto($d['opcional'], 1);
	$diar = $ent->isAlfabeto($d['diario'], 1);
	if ($_SESSION['rol'] > 10 ) $comer = $_SESSION['comercio']; else  $comer = $ent->isEntero($d['comercio']);
	$usuar = implode(',', $d['usuarios']);

	$query = "update tbl_caracteristicas set idCom = '$comer', nombre = '$nombres', fechaIni = $fecha1, fechaFin = $fecha2, descripcion = '$descrp', opcional = '$opc',
				precioModifica = '$mod', precio = $val, buscable = '$busc', fecha = ".time().", usuarios = '$usuar', idAdm = ".$_SESSION['id'].", diario = '$diar' where id = $ident";
//	echo "<br>$query";
	$temp->query($query);
}

//Para borrar
if ($borra = $ent->isBoolean($d['borrar'])) {

	//borra la temporada
	$query = "delete from tbl_temporada where id = ".$borra;
	$temp->query($query);
}

$fecha3 = date('d/m/Y', mktime(0,0,0,date("m"),date("d")-1,date("Y")));
//calculo de valores
if (!$ent->isReal($d['cambiar'])) { // Valores para insertar nuevos Art&iacute;culos
	$fecha1 = date('d/m/Y', mktime(0,0,0,date("m"),date("d"),date("Y")));
	$fecha2 = date('d/m/Y', mktime(0,0,0,date("m")+1,date("d"),date("Y")));
	$html->inHide("true", "inserta");
	$titulo2 = 'Insertar';
	$valor = 0;

	//llenar array para los usuarios autorizados
	$query = "select idadmin from tbl_admin ";
	if ($_SESSION['rol'] > 10 ) $query .= " where idcomercio = '".$_SESSION['comercio']."'";
	$temp->query($query);
	$arraUsua = $temp->loadResultArray();
	$busc = 'S';
	$opc = 'N';

}else { // Valores para modificar el art&iacute;culo seleccionado
	if (!$ent->isReal($d['cambiar'])) exit; else $id = $ent->isReal($d['cambiar']);
	$html->inHide($id, 'modifica');
//echo "id=$id";
	$titulo2 = 'Modificar';
	$qg = 'select * from tbl_caracteristicas t where id = '.$id;
	$temp->query($qg);
	if ($temp->next_record()) {
		$idCom = $temp->f('idCom');
		$fecha1 = date('d/m/Y', $temp->f('fechaIni'));
		$fecha2 = date('d/m/Y', $temp->f('fechaFin'));
		$nombre = $temp->f('nombre');
		$descr = $temp->f('descripcion');
		$valor = $temp->f('precio');
		$modif = $temp->f('precioModifica');
		$arraUsua = explode(',', $temp->f('usuarios'));
		$busc = $temp->f('buscable');
		$opc = $temp->f('opcional');
		$dia = $temp->f('diario');
	}
}

//c&oacute;digo javascript
$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
function verifica() {
	if (
			(checkField (document.admin_form.nombre, isAlphanumeric, ''))&&
			(checkField (document.admin_form.descr, isAlphanumeric, ''))&&
			(checkField (document.getElementById('fecha1'), isDate, ''))&&
			(checkField (document.getElementById('fecha2'), isDate, ''))
		) {
			if (comparaFecha2(document.admin_form.fecha1, document.admin_form.fecha2, 'La fecha de comienzo no puede ser mayor que la terminaci&oacute;n.')
			){
				if (comparaFecha2(document.admin_form.fechahoy, document.admin_form.fecha1, 'La fecha de comienzo no puede ser menor que hoy.')) {
					return true;
				}
			}
	}
	return false;
}
</script>";

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_CARACTERISTICA;
$html->tituloTarea = $titulo2;
$html->anchoTabla = 500;
$html->anchoCeldaI = $html->anchoCeldaD = 245;

$html->inHide($_SESSION['id'], 'usuario');
$html->inHide('S', 'busc');
$html->inTextb(_FORM_NOMBRE, $nombre, 'nombre', null, null, null, _VENTA_DESC_NOMBRE);
$html->inTextb(_TICKET_DESCRI, $descr, 'descr', null, null, null, _VENTA_DESC_DESC);

$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
if ($_SESSION['rol'] <= 10 ) $html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $query, $idCom);
else $html->inHide($_SESSION['comercio'], 'comercio');

$html->inTextb(_VENTA_VALOR, $valor, 'valor', null, null, null, _VENTA_DESC_VALOR);
$arrTMod = array(
				array('+', _VENTA_MODIFICA_SUM),
				array('-', _VENTA_MODIFICA_RES),
				array('%', _VENTA_MODIFICA_POR)
		);
$html->inSelect(_VENTA_MODIFICA, 'modifi', 3, $arrTMod, $modif, null, _VENTA_DESC_MODIFICA);
$query = "select idadmin id, nombre from tbl_admin ";
if ($_SESSION['rol'] > 10 ) $query .= " where idcomercio = '".$_SESSION['comercio']."'";
$query .= " order by nombre";
$html->inSelect(_VENTA_USUARIOS, 'usuarios', 1, $query, $arraUsua, null, _VENTA_DESC_USUARIOS, 'multiple size="4"');

if (!$ent->isReal($d['cambiar'])) {
	$adicional = 'onchange="if (this.value !=\'\') document.getElementById(\'div_fecha1\').style.display=document.getElementById(\'div_fecha2\').style.display=\'none\'; else
					 document.getElementById(\'div_fecha1\').style.display=document.getElementById(\'div_fecha2\').style.display=\'\'"';
	$query = "select '' id, '"._VENTA_TEMP_INI."' nombre union all select id, nombre from tbl_temporada";
	$html->inSelect(_MENU_ADMIN_TEMPORADA, 'tempor', 2, $query, null, null, _VENTA_DESC_TEMPOR, $adicional);
}

$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', 'fecha1', _REPORTE_FECHA_INI, _VENTA_DESC_FECHA1);
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', 'fecha2', _REPORTE_FECHA_FIN, _VENTA_DESC_FECHA2);

$arrEntr = array('S','N');
$arrEtiq = array(_VENTA_BUSCAR_SI, _VENTA_BUSCAR_NO);
//$html->inRadio(_VENTA_BUSCAR, $arrEntr, 'busc', $arrEtiq, $busc, _VENTA_DESC_BUSCAR, false);
$html->inRadio(_VENTA_OPCIONAL, $arrEntr, 'opcional', $arrEtiq, $opc, _VENTA_DESC_OPCIONAL, false);
$html->inRadio(_VENTA_DIARIO, $arrEntr, 'diario', $arrEtiq, $dia, _VENTA_DESC_DIARIO, false);

echo $html->salida();

$vista = 'select c.id, o.nombre comer, c.nombre caract, c.descripcion, c.diario,
			case precioModifica when "+" then "Adiciona" when "-" then "Sustrae" else "Por ciento" end modifica, precio, fechaIni, fechaFin, opcional,
			usuarios, c.fecha, a.nombre usuario'
        . ' from tbl_caracteristicas c, tbl_comercio o, tbl_admin a';

$where = 'where c.idCom = o.idcomercio
			and c.idAdm = a.idadmin ';
if ($_SESSION['rol'] > 10 ) $where .= ' and c.idCom = '.$_SESSION['comercio'];
$orden = 'comer, fechaIni desc';

$colEsp = array(
				array("e", "Editar Datos", "../images/edit.gif", "Editar"),
				array("b", "Borrar Registro", "../images/borra.gif", "Borrar")
			);

$busqueda = array();

$columnas = array( 
				array("Característica", "caract", "", "center", "left" ),
				array("Descripción", "descripcion", "", "center", "left" ),
				array("Modificación<br>Precio", "modifica", "80", "center", "center"),
				array("Precio", "precio", "60", "center", "center"),
				array("Fecha<br>Inicio", "fechaIni", "120", "center", "center"),
				array("Fecha<br>Final", "fechaFin", "120", "center", "center"),
				array("Opcional", "opcional", "60", "center", "center"),
				array("Diario", "diario", "60", "center", "center"),
				array("Usuarios", "usuarios", "60", "center", "center"),
				array("Modificado<br>por", "usuario", "120", "center", "center"),
				array("Fecha<br>Modif.", "fecha", "120", "center", "center")
			);
if ($_SESSION['rol'] <= 10 ) {
	$columnas = array(
				array("Comercio", "comer", "", "center", "left" ),
				array("Característica", "caract", "", "center", "left" ),
				array("Descripción", "descripcion", "", "center", "left" ),
				array("Modificación<br>Precio", "modifica", "80", "center", "center"),
				array("Precio", "precio", "60", "center", "center"),
				array("Fecha<br>Inicio", "fechaIni", "120", "center", "center"),
				array("Fecha<br>Final", "fechaFin", "120", "center", "center"),
				array("Opcional", "opcional", "60", "center", "center"),
				array("Diario", "diario", "60", "center", "center"),
				array("Usuarios", "usuarios", "60", "center", "center"),
				array("Modificado<br>por", "usuario", "120", "center", "center"),
				array("Fecha<br>Modif.", "fecha", "120", "center", "center")
			);

}


tabla( 1400, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>