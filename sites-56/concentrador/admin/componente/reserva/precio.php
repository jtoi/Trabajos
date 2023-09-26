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
	$query = "delete from tbl_precio where id = $ident";
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
	$precio = $ent->isNumero($d['precio']);
	$usuario = $ent->isEntero($d['usuario']);

//	Chequeo si ya existe precio para ese intervalo y dá la posición
	$query = "select id from tbl_precio where idProd = $prod and fechaFin >= $fecha1 or fechaIni <= $fecha2";
	$temp->query($query);

	if ($temp->num_rows() != 0) { //si hay interferencias de precios anteriores

//		Chequea y borra todos los precios que quedan dentro del intervalo de fechas
		$query = "select id from tbl_precio where idProd = $prod and fechaIni >= $fecha1 and fechaFin <= $fecha2";
		$temp->query($query);
		if ($temp->num_rows() > 0) { //Borra todos los precios contenidos dentro del intervalo de fechas del nuevo precio
			$arraProd = implode(",", $temp->loadResultArray());
			$query = "delete from tbl_precio where id in ($arraProd)";
			$temp->query($query);
		}

//		Chequea si existe un precio con un intervalo de fechas que contenga las fechas del nuevo precio, si es así debe picarlo en 2
		$query = "select * from tbl_precio where idProd = $prod and fechaIni <= $fecha1 and fechaFin >= $fecha2";
		$temp->query($query);
		for ($i=0;$i<$temp->num_rows();$i++) {

//			Inserta el primer pedazo por delante del nuevo a insertar
			$arrVals = $temp->loadRow();
			$query = "insert into tbl_precio (id, idProd, idAdm, valor, fecha, fechaIni, fechaFin) values (null, {$arrVals[1]}, {$arrVals[2]}, {$arrVals[3]}, {$arrVals[4]}, {$arrVals[5]}, ".($fecha1-86400).")";
			$temp->query($query);

//			Modifica el grande existente para que sea el segundo pedazo por detrás del nuevo
			$query = "update tbl_precio set fechaIni = ".($fecha2+86400)." where id = {$arrVals[0]}";
			$temp->query($query);
		}

//		Chequea si hay interferencia por delante
		$query = "select id from tbl_precio where idProd = $prod and fechaIni > $fecha1 and fechaFin > $fecha2 and fechaIni <= $fecha2";
		$temp->query($query);
		for ($i=0;$i<$temp->num_rows();$i++) { 
//			Modifica el precio existente
			$query = "update tbl_precio set fechaIni = ".($fecha2+86400)." where id = ".$temp->f('id');
			$temp->query($query);
		}

//		Chequea si hay interferencia por detrás
		$query = "select id from tbl_precio where idProd = $prod and fechaIni < $fecha1 and fechaFin < $fecha2 and fechaFin >= $fecha1";
		$temp->query($query);
		for ($i=0;$i<$temp->num_rows();$i++) { 
//			Modifica el precio existente
			$query = "update tbl_precio set fechaFin = ".($fecha1-86400)." where id = ".$temp->f('id');
			$temp->query($query);
		}
	}

//	Por último, inserta el nuevo precio
	$query = "insert into tbl_precio (id, idProd, idAdm, valor, fecha, fechaIni, fechaFin) values (null, $prod, $usuario, $precio, unix_timestamp(), $fecha1, $fecha2)";
	$temp->query($query);
//	y borra los precios con las fechas invertidas
	$query = "delete tbl_precio where fechaFin < fechaIni";
	$temp->query($query);
}

//Para borrar
if ($borra = $ent->isBoolean($d['borrar'])) {

	//borra el producto
	$query = "delete from tbl_precio where id = ".$borra;
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
	$qg = 'select * from tbl_precio where id = '.$id;
	$temp->query($qg);
	if ($temp->next_record()) { 
		$fecha1 = date('d/m/Y', $temp->f('fechaIni'));
		$fecha2 = date('d/m/Y', $temp->f('fechaFin'));
		$idProd = $temp->f('idProd');
		$precio = $temp->f('valor');
		$moneda = $temp->f('idMon');
	}

}

//c&oacute;digo javascript
$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
function verifica() {
	if (
			(checkField (document.getElementById('precio'), isMoney, ''))&&
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
$html->tituloPag = _MENU_ADMIN_PRECIO;
$html->tituloTarea = $titulo2;
$html->anchoTabla = 500;
$html->anchoCeldaI = $html->anchoCeldaD = 245;
$html->inHide($_SESSION['id'], 'usuario');

$query = "select id, nombre from tbl_productos where activo = 'S' and fechaFin >= UNIX_TIMESTAMP() ";
if ($_SESSION['rol'] > 10 ) $query .= " and idCom = '".$_SESSION['comercio']."'";
$query .= " order by nombre";
$html->inSelect(_MENU_ADMIN_PRODUCTO, 'producto', 1, $query, $idProd);

$html->inTextb(_MENU_ADMIN_PRECIO, $precio, 'precio', null, null, null);

if (!$ent->isReal($d['cambiar'])) {
	$adicional = 'onchange="if (this.value !=\'\') document.getElementById(\'div_fecha1\').style.display=document.getElementById(\'div_fecha2\').style.display=\'none\'; else
					 document.getElementById(\'div_fecha1\').style.display=document.getElementById(\'div_fecha2\').style.display=\'\'"';
	$query = "select '' id, '"._VENTA_TEMP_INI."' nombre union all select id, nombre from tbl_temporada";
	$html->inSelect(_MENU_ADMIN_TEMPORADA, 'tempor', 2, $query, null, null, _VENTA_DESC_TEMPOR, $adicional);
}

$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', 'fecha1', _REPORTE_FECHA_INI, _PROD_DESC_FECHA1);
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', 'fecha2', _REPORTE_FECHA_FIN, _PROD_DESC_FECHA2);

echo $html->salida();

$vista = "select p.id, d.nombre, p.valor, p.fechaIni, p.fechaFin, a.nombre usuario, p.fecha
		from tbl_productos d, tbl_precio p, tbl_admin a ";

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
				array("Producto", "nombre", "", "center", "left" ),
				array("Precio", "valor", "60", "center", "center"),
				array("Fecha<br>Inicio", "fechaIni", "120", "center", "center"),
				array("Fecha<br>Final", "fechaFin", "120", "center", "center"),
				array("Modificado<br>por", "usuario", "120", "center", "center"),
				array("Fecha<br>Modif.", "fecha", "120", "center", "center")
			);

tabla( 900, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>