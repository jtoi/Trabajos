<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

global $temp;
$html = new tablaHTML;

$sel_grup = "select orden from tbl_roles";
$temp->query($sel_grup);
while ($temp->next_record()){
	$cadena_orden .= $temp->f('orden').',';
}

if ($_REQUEST['inserta']) {
	$q = "insert into tbl_roles (nombre, orden) values ('".$_REQUEST['nombre']."', ".$_REQUEST['ordi'].")";
//	echo $q;
	$temp->query($q);
//	echo $temp->_sql;
}

if ($_REQUEST['modifica']) {
	$q = "update tbl_roles set nombre = '".$_REQUEST['nombre']."', orden = ".$_REQUEST['ordi']." where idrol = ".$_REQUEST['modifica'];
	$temp->query($q);
}

if ($_REQUEST['borrar']) {
	$q = "delete from tbl_roles where idrol = ".$_REQUEST['borrar'];
	$temp->query($q);
}

$titulo = _GRUPOS_TITULO;
$ancho = '500';

if (!$_REQUEST['cambiar']) {
	$valorTarea = true; $nombreTarea = 'inserta';
} else {
	$valorTarea = $_REQUEST['cambiar']; $nombreTarea = 'modifica';
	$qg = "select * from tbl_roles where idrol = ".$_REQUEST['cambiar'];
	$temp->query($qg);
	
	$nombre = $temp->f('nombre');
	$ord = $temp->f('orden');
}

?>

<script language="JavaScript" type="text/javascript">
function verifica() {
	if (
			(checkField (document.admin_form.nombre, isAlphanumeric, ''))
		) {
		return true;
	}
	return false;
}
</script>
<?php

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _GRUPOS_TITULO;
$html->tituloTarea = "&nbsp;";
$html->anchoTabla = 500;
$html->anchoCeldaI = $html->anchoCeldaD = 245;

$html->inHide($valorTarea, $nombreTarea);
$html->inTextb(_GRUPOS_NOMBRE, $nombre, 'nombre');
$html->inSelect(_GRUPOS_ORDEN, 'ordi', 4, array(2,100), $ord);

echo $html->salida();

$vista = 'select idrol as id, nombre, orden from tbl_roles ';
$where = 'where orden >= '.$_SESSION['grupo_rol'];
$orden = 'orden asc';

$colEsp = array(
				array("e", _GRUPOS_EDIT_DATA, "css_edit", _TAREA_EDITAR),
				array("b", _GRUPOS_BORRA_DATA, "css_borra", _TAREA_BORRAR)
			);

$busqueda = array();

$columnas = array(
				array(_GRUPOS_GRUPO, "nombre", "", "center", "left" ),
				array(_GRUPOS_ORDEN, "orden", "70", "center", "left")
			);

tabla( 500, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>
