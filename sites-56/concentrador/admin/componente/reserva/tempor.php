<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); 

$temp = new ps_DB;
$ent = new entrada;
$html = new tablaHTML;

$d=$_REQUEST;
//print_r($_SESSION);

//Para insertar la temporada
if ($ent->isBoolean($d['inserta'])) {
	$fecha1 = to_unix($ent->isDate($d['fecha1']));
	$fecha2 = to_unix($ent->isDate($d['fecha2']));
	$nombres = $ent->isAlfanumerico($d['nombre'], 150);
	if ($_SESSION['rol'] > 10 ) $comer = $_SESSION['comercio']; else  $comer = $ent->isEntero($d['comercio']);

	$query = "insert into tbl_temporada (id, idCom, nombre, fechaIni, fechaFin) values (null, '$comer', '$nombres', $fecha1, $fecha2)";
//	echo $query;
	$temp->query($query);
	
}

//modificar la temporada
if ($ident = $ent->isBoolean($d['modifica'])) {
	$fecha1 = to_unix($ent->isDate($d['fecha1']));
	$fecha2 = to_unix($ent->isDate($d['fecha2']));
	$nombres = $ent->isUrl($d['nombre'], 150);
	if ($_SESSION['rol'] > 10 ) $comer = $_SESSION['comercio']; else $comer = $ent->isEntero($d['comercio']);

	$query = "update tbl_temporada set idCom = '$comer', nombre = '$nombres', fechaIni = $fecha1, fechaFin = $fecha2 where id = $ident";
	$temp->query($query);
}

//Para borrar la temporada
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
}
else { // Valores para modificar el art&iacute;culo seleccionado
	if (!$ent->isReal($d['cambiar'])) exit; else $idtemp = $ent->isReal($d['cambiar']);
	$html->inHide($idtemp, 'modifica');

	$titulo2 = 'Modificar';
	$qg = 'select * from tbl_temporada t where id = '.$idtemp;
	$temp->query($qg);
	if ($temp->next_record()) {
		$idCom = $temp->f('idCom');
		$fecha1 = date('d/m/Y', $temp->f('fechaIni'));
		$fecha2 = date('d/m/Y', $temp->f('fechaFin'));
		$nombre = $temp->f('nombre');
	}
}

//c&oacute;digo javascript
$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
function fechatemp(valor) {
	if (valor) {
		document.getElementById('fecha1tr').display = 'none';
		document.getElementById('fecha2tr').display = 'none';
	} else {
		document.getElementById('fecha1tr').display = '';
		document.getElementById('fecha2tr').display = '';
	}
}

function verifica() {
	if (
			(checkField (document.admin_form.nombre, isAlphanumeric, ''))&&
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
$html->tituloPag = _MENU_ADMIN_TEMPORADA;
$html->tituloTarea = $titulo2;
$html->anchoTabla = 500;
$html->anchoCeldaI = $html->anchoCeldaD = 245;

$html->inHide($fecha3, 'fechahoy');
$html->inTextb(_FORM_NOMBRE, $nombre, 'nombre', null, null, null, _VENTA_DESC_TEMP_NOMBRE);
$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
if ($_SESSION['rol'] <= 10 ) $html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $query, $idCom);
else $html->inHide($_SESSION['comercio'], 'comercio');
$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', 'fecha1', _REPORTE_FECHA_INI, _VENTA_DESC_TEMP_FECHA1);
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', 'fecha2', _REPORTE_FECHA_FIN, _VENTA_DESC_TEMP_FECHA2);

echo $html->salida();

$vista = 'select t.id, t.nombre, c.nombre comerc, fechaIni, fechaFin'
        . ' from tbl_temporada t, tbl_comercio c';

$where = 'where c.idcomercio = t.idCom ';
if ($_SESSION['rol'] > 10 ) $where .= ' and t.idCom = '.$_SESSION['comercio'];
$orden = 'comerc, fechaIni desc';

$colEsp = array(
				array("e", "Editar Datos", "../images/edit.gif", "Editar"),
				array("b", "Borrar Registro", "../images/borra.gif", "Borrar")
			);

$busqueda = array();

$columnas = array( 
				array("Temporada", "nombre", "", "center", "left" ),
				array("Fecha<br>Inicio", "fechaIni", "120", "center", "center"),
				array("Fecha<br>Final", "fechaFin", "120", "center", "center")
			);
if ($_SESSION['rol'] <= 10 ) {
	$columnas = array(
				array("Temporada", "nombre", "", "center", "left" ),
				array("Comercio", "comerc", "", "center", "left" ),
				array("Fecha<br>Inicio", "fechaIni", "120", "center", "center"),
				array("Fecha<br>Final", "fechaFin", "120", "center", "center")
			);

}


tabla( 700, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>