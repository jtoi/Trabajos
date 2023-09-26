<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); 

$temp = new ps_DB;
$ent = new entrada;
$html = new tablaHTML;

$d=$_REQUEST;
//print_r($_SESSION);
//echo "<br><br>";
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
	$cod = $ent->isAlfanumerico($d['codprod'], 300);
	$busc = $ent->isAlfabeto($d['busc'], 1);
	if ($_SESSION['rol'] > 10 ) $comer = $_SESSION['comercio']; else  $comer = $ent->isEntero($d['comercio']);
	$usuario = $ent->isEntero($d['usuario']);
	$caract = $d['caracter'];

//	Inserta el producto en la BD
	$query = "insert into tbl_productos (id, idAdm, idCom, nombre, codigo, descripcion, fecha, fechaIni, fechaFin, activo, precio, stock) 
				values (null, ".$usuario.", '$comer', '$nombres',  '$cod', '$descrp', ".time().", $fecha1, $fecha2, 'S', 'S', '$busc')";
//	echo "<br>$query";
	$temp->query($query);
	$idProd = $temp->last_insert_id();

//	Inserta el producto y la característica
	foreach ($caract as $item) {
		$query = "insert into tbl_productosRel value ($idProd, $item)";
//echo "$query<br>";
		$temp->query($query);
	}

}

//modificar 
if ($ident = $ent->isBoolean($d['modifica'])) {
	$fecha1 = to_unix($ent->isDate($d['fecha1']));
	$fecha2 = to_unix($ent->isDate($d['fecha2']));
	$nombres = $ent->isAlfanumerico($d['nombre'], 150);
	$descrp = $ent->isAlfanumerico($d['descr'], 300);
	$cod = $ent->isAlfanumerico($d['codprod'], 300);
	$busc = $ent->isAlfabeto($d['busc'], 1);
	if ($_SESSION['rol'] > 10 ) $comer = $_SESSION['comercio']; else  $comer = $ent->isEntero($d['comercio']);
	$usuario = $ent->isEntero($d['usuario']);
	$caract = $d['caracter'];

	$query = "update tbl_productos set idCom = '$comer', nombre = '$nombres', fechaIni = $fecha1, fechaFin = $fecha2, descripcion = '$descrp',
				codigo = '$cod', stock = '$busc', fecha = ".time().", idAdm = ".$usuario." where id = $ident";
	$temp->query($query);

	$query = "delete from tbl_productosRel where idProd = $ident";
	$temp->query($query);

//	Inserta el producto y la característica
	foreach ($caract as $item) {
		$query = "insert into tbl_productosRel value ($ident, $item)";
		$temp->query($query);
	}
}

//Para borrar
if ($borra = $ent->isBoolean($d['borrar'])) {

	//borra el producto
	$query = "delete from tbl_productos where id = ".$borra;
	$temp->query($query);

	//borra la realción
	$query = "delete from tbl_productosRel where idProd = ".$borra;
	$temp->query($query);
}

$fecha3 = date('d/m/Y', mktime(0,0,0,date("m"),date("d")-1,date("Y")));
//calculo de valores
if (!$ent->isReal($d['cambiar'])) { // Valores para insertar nuevos Art&iacute;culos
	$fecha1 = date('d/m/Y', mktime(0,0,0,date("m"),date("d"),date("Y")));
	$fecha2 = date('d/m/Y', mktime(0,0,0,date("m"),date("d"),date("Y")+1));
	$html->inHide("true", "inserta");
	$titulo2 = 'Insertar';
	$busc = 'S';

}else { // Valores para modificar el art&iacute;culo seleccionado
	if (!$ent->isReal($d['cambiar'])) exit; else $id = $ent->isReal($d['cambiar']);
	$html->inHide($id, 'modifica');
//echo "id=$id";
	$titulo2 = 'Modificar';
	$qg = 'select * from tbl_productos t where id = '.$id;
	$temp->query($qg);
	if ($temp->next_record()) {
		$idCom = $temp->f('idCom');
		$fecha1 = date('d/m/Y', $temp->f('fechaIni'));
		$fecha2 = date('d/m/Y', $temp->f('fechaFin'));
		$nombre = $temp->f('nombre');
		$descr = $temp->f('descripcion');
		$codProd = $temp->f('codigo');
		$busc = $temp->f('stock');
	}
	$query = "select idCaract from tbl_productosRel where idProd = ".$id;
	$temp->query($query);
	$arraCarat = $temp->loadResultArray();

}

//c&oacute;digo javascript
$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
function verifica() {
	if (
			(checkField (document.admin_form.nombre, isAlphanumeric, ''))&&
			(checkField (document.admin_form.descr, isAlphanumeric, true))&&
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
$html->tituloPag = _MENU_ADMIN_PRODUCTO;
$html->tituloTarea = $titulo2;
$html->anchoTabla = 600;
$html->anchoCeldaI = $html->anchoCeldaD = 245;

$html->inHide($_SESSION['id'], 'usuario');
$html->inTextb(_FORM_NOMBRE, $nombre, 'nombre', null, null, null, _PROD_NOMBRE_DESC);
$html->inTextb(_PROD_CODIGO, $codProd, 'codprod', null, null, null, _PROD_CODIGO_DESC);
$html->inTextb(_TICKET_DESCRI, $descr, 'descr', null, null, null, _PROD_DESCR_DESC);

$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
if ($_SESSION['rol'] <= 10 ) $html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $query, $idCom);
else $html->inHide($_SESSION['comercio'], 'comercio');
$query = "select id, concat(nombre, ' - ', left(descripcion, 47)) nombre from tbl_caracteristicas where fechaFin >= UNIX_TIMESTAMP() ";
if ($_SESSION['rol'] > 10 ) $query .= " and idCom = '".$_SESSION['comercio']."'";
$query .= " order by nombre";
$html->inSelect(_MENU_ADMIN_CARACTERISTICA, 'caracter', 1, $query, $arraCarat, null, _PROD_CARACT_DESC, 'multiple size="4"');

if (!$ent->isReal($d['cambiar'])) {
	$adicional = 'onchange="if (this.value !=\'\') document.getElementById(\'div_fecha1\').style.display=document.getElementById(\'div_fecha2\').style.display=\'none\'; else
					 document.getElementById(\'div_fecha1\').style.display=document.getElementById(\'div_fecha2\').style.display=\'\'"';
	$query = "select '' id, '"._VENTA_TEMP_INI."' nombre union all select id, nombre from tbl_temporada";
	$html->inSelect(_MENU_ADMIN_TEMPORADA, 'tempor', 2, $query, null, null, _VENTA_DESC_TEMPOR, $adicional);
}

$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', 'fecha1', _REPORTE_FECHA_INI, _PROD_DESC_FECHA1);
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', 'fecha2', _REPORTE_FECHA_FIN, _PROD_DESC_FECHA2);

$arrEntr = array('S','N');
$arrEtiq = array(_VENTA_BUSCAR_SI, _VENTA_BUSCAR_NO);
$html->inRadio(_PROD_STOCK, $arrEntr, 'busc', $arrEtiq, $busc, _PROD_STOCK_DESC, false);

echo $html->salida();

$vista = "select p.id, a.nombre usuario, d.nombre comercio, p.nombre, p.codigo, p.descripcion, p.fecha, p.fechaIni, p.fechaFin, p.activo, p.precio,
			case p.stock when 'S' then 'Si' else 'No' end stock
		from tbl_productos p, tbl_admin a, tbl_comercio d
";

$where = 'where p.idAdm = a.idadmin
			and p.idCom = d.idcomercio ';
if ($_SESSION['rol'] > 10 ) $where .= ' and p.idCom = '.$_SESSION['comercio'];
$orden = 'comercio, fechaIni desc';

$colEsp = array(
				array("e", "Editar Datos", "../images/edit.gif", "Editar"),
				array("b", "Borrar Registro", "../images/borra.gif", "Borrar")
			);

$busqueda = array();

$columnas = array( 
				array("Producto", "nombre", "", "center", "left" ),
				array("Código", "codigo", "60", "center", "center"),
				array("Descripción", "descripcion", "", "center", "left" ),
				array("Fecha<br>Inicio", "fechaIni", "120", "center", "center"),
				array("Fecha<br>Final", "fechaFin", "120", "center", "center"),
				array("Contra<br>Almacén", "stock", "60", "center", "center"),
				array("Modificado<br>por", "usuario", "120", "center", "center"),
				array("Fecha<br>Modif.", "fecha", "120", "center", "center")
			);
if ($_SESSION['rol'] <= 10 ) {
	$columnas = array(
				array("Comercio", "comercio", "", "center", "left" ),
				array("Producto", "nombre", "", "center", "left" ),
				array("Código", "codigo", "60", "center", "center"),
				array("Descripción", "descripcion", "", "center", "left" ),
				array("Fecha<br>Inicio", "fechaIni", "120", "center", "center"),
				array("Fecha<br>Final", "fechaFin", "120", "center", "center"),
				array("Contra<br>Almacén", "stock", "60", "center", "center"),
				array("Modificado<br>por", "usuario", "120", "center", "center"),
				array("Fecha<br>Modif.", "fecha", "120", "center", "center")
			);

}

tabla( 1200, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>