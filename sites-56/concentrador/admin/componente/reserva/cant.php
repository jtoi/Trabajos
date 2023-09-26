<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); 

$temp = new ps_DB;
$ent = new entrada;
$html = new tablaHTML;

$d=$_REQUEST;
//print_r($_SESSION);
//echo "<br><br>";
//print_r($d);

//modificar 
if ($ident = $ent->isBoolean($d['modifica'])) {
//	Borra el precio a modificar
	$query = "delete from tbl_productosCant where id = $ident";
	$temp->query($query);

//	Hace que se inserte el precio a modificar como si fuese uno nuevo para el chequeo de fechas
	$d['inserta'] = true;
}

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
	$prod = $ent->isEntero($d['producto']);
	$cant = $ent->isNumero($d['cant']);
	$usuario = $ent->isEntero($d['usuario']);
	insertaCantidad($prod,$fecha1,$fecha2,$cant);

}

//Para borrar
if ($borra = $ent->isBoolean($d['borrar'])) {

	//borra el producto
	$query = "delete from tbl_productosCant where id = ".$borra;
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
	$precio = null;
	$moneda = 978;

}else { // Valores para modificar el art&iacute;culo seleccionado
	if (!$ent->isReal($d['cambiar'])) exit; else $id = $ent->isReal($d['cambiar']);
	$html->inHide($id, 'modifica');
//echo "id=$id";
	$titulo2 = 'Modificar';
	$qg = 'select * from tbl_productosCant where id = '.$id;
	$temp->query($qg);
	if ($temp->next_record()) { 
		$fecha1 = date('d/m/Y', $temp->f('fechaIni'));
		$fecha2 = date('d/m/Y', $temp->f('fechaFin'));
		$idProd = $temp->f('idProd');
		$cant = $temp->f('cant');
	}

}

//c&oacute;digo javascript
$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
function verifica() {
	if (
			(checkField (document.admin_form.cant, isInteger, ''))&&
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
$html->tituloPag = _MENU_ADMIN_CANT;
$html->tituloTarea = $titulo2;
$html->anchoTabla = 500;
$html->anchoCeldaI = $html->anchoCeldaD = 245;

$html->inHide($_SESSION['id'], 'usuario');
$html->inHide(date('d/m/Y', time()-86359), 'fechahoy');

$query = "select id, nombre from tbl_productos where activo = 'S' and fechaFin >= UNIX_TIMESTAMP() and stock = 'S'";
if ($_SESSION['rol'] > 10 ) $query .= " and idCom = '".$_SESSION['comercio']."'";
$query .= " order by nombre";
$html->inSelect(_MENU_ADMIN_PRODUCTO, 'producto', 1, $query, $idProd);

$html->inTextb(_MENU_ADMIN_CANT, $cant, 'cant', null, null, null, _PROD_CANT_DESC);

if (!$ent->isReal($d['cambiar'])) {
	$adicional = 'onchange="if (this.value !=\'\') document.getElementById(\'div_fecha1\').style.display=document.getElementById(\'div_fecha2\').style.display=\'none\'; else
					 document.getElementById(\'div_fecha1\').style.display=document.getElementById(\'div_fecha2\').style.display=\'\'"';
	$query = "select '' id, '"._VENTA_TEMP_INI."' nombre union all select id, nombre from tbl_temporada";
	$html->inSelect(_MENU_ADMIN_TEMPORADA, 'tempor', 2, $query, null, null, _VENTA_DESC_TEMPOR, $adicional);
}

$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', 'fecha1', _REPORTE_FECHA_INI, _PROD_DESC_FECHA1);
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', 'fecha2', _REPORTE_FECHA_FIN, _PROD_DESC_FECHA2);

echo $html->salida();

$vista = "select p.id, d.nombre, p.cant, p.fechaIni, p.fechaFin, a.nombre usuario, p.fecha
		from tbl_productos d, tbl_productosCant p, tbl_admin a";

$where = 'where p.idProd = d.id
			and p.idAdm = a.idadmin';
if ($_SESSION['rol'] > 10 ) $where .= ' and d.idCom = '.$_SESSION['comercio'];
$orden = 'd.nombre, fechaIni desc';

$colEsp = array(
				array("e", "Editar Datos", "../images/edit.gif", "Editar"),
				array("b", "Borrar Registro", "../images/borra.gif", "Borrar")
			);

$busqueda = array();

$columnas = array( 
				array("Id", "id", "", "center", "left" ),
				array("Producto", "nombre", "", "center", "left" ),
				array("Cant.", "cant", "60", "center", "center"),
				array("Fecha<br>Inicio", "fechaIni", "120", "center", "center"),
				array("Fecha<br>Final", "fechaFin", "120", "center", "center"),
				array("Modificado<br>por", "usuario", "120", "center", "center"),
				array("Fecha<br>Modif.", "fecha", "120", "center", "center")
			);

tabla( 900, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>