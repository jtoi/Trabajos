<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

$html = new tablaHTML;
global $temp;

$d = $_POST;

if (_MOS_CONFIG_DEBUG) var_dump($d);

$fechaNow = time();
$incluye = "";
if ($d['fecha1']) $fecha1 = to_unix($d['fecha1']);
if ($d['fecha2']) $fecha2 = to_unix($d['fecha2'])+86400;
if ($d['mensaje']) $mensaje = convertir_especiales_html ($d['mensaje']);
// echo $mensaje."<br>";
// print_r ($GLOBALS["carateres_latinos"]);

//echo "cod=".generaCodEmp();
if ($d['comer']) {
	$q = "select id from tbl_comercio where activo = 'S' order by nombre";
	$temp->query($q);
	if ($d['comer'] == implode("', '", $temp->loadResultArray())) $comer = 'todos'; else $comer = str_replace("'", "", implode(',', $d['comer']));
		
}
//inserta Art&iacute;culo
if ($d['inserta'] && strlen($mensaje) > 5) {
	
	$query = "insert into tbl_mensajes (mensaje, fechaInicio, fechaFin, fecha, idcomercio)
				values ('".$mensaje."', $fecha1, $fecha2, $fechaNow, '$comer')";
    if (_MOS_CONFIG_DEBUG) echo $query;
	$temp->query($query);
}

// Modifica Art&iacute;culo
if ($d['modifica']) {
    if ($d['activo'] == 'S') $activo = 1;
    else $activo = 0;
//echo "mensaje=".htmlentities($mensaje, ENT_QUOTES, ISO8859-1);
	$query = "update tbl_mensajes set activo = ".$activo.", mensaje = '".$mensaje."', fechaInicio = ".$fecha1.",
				fechaFin = ".$fecha2.", idcomercio = '$comer' where idmensaje = '".$d['modifica']."'";

if (_MOS_CONFIG_DEBUG) echo "query=$query<br>";

	$temp->query($query);

}

//Borra Art&iacute;culo
if ($d['borrar']) {

	$ql = "delete from tbl_mensajes where idmensaje = ".$d['borrar'];
	$temp->query($ql);
}

if (!$d['cambiar'] && $_SESSION['comercio'] == 'todos') { // Valores para insertar nuevos Art&iacute;culos
	$titulo_tarea = _TAREA_INSERTAR.' '._SETUP_MENSAJE;
	$personas = 1;
	$personasExt = 0;
    $fecha1 = date('d/m/Y', time());
    $fecha2 = date('d/m/Y', mktime(0, 0, 0, date("m"), date("d")+20, date("Y")));
	$valorTarea = true; $nombreTarea = 'inserta';
	$activo = 'S';
}
else { // Valores para modificar el art&iacute;culo seleccionado
	if ($d['cambiar']) $mens = $d['cambiar'];
	else  $comercio = $_SESSION['comercio'];
	$query = 'select * from tbl_mensajes
			where idmensaje = '.$mens;
	$temp->query($query);
	$id = $temp->f("idmensaje");
	$comr = explode(',', $temp->f('idcomercio'));
	($temp->f('activo'))?$activo='S':$activo='N';
	$fecha1 = date('d/m/Y', $temp->f('fechaInicio'));
	$fecha2 = date('d/m/Y', $temp->f('fechaFin'));
	global $param;

	$valorTarea = $id; $nombreTarea = "modifica";
	$titulo_tarea = _TAREA_MODIFICAR.' '._SETUP_MENSAJE;
	$mensaje = $temp->f('mensaje');

}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _SETUP_MENSAJE;
$html->tituloTarea = "&nbsp;";
$html->anchoTabla = 400;
$html->anchoCeldaI = 130;
$html->anchoCeldaD = 250;

$html->inHide($valorTarea, $nombreTarea);
$html->inTexarea('Mensaje:', $mensaje, 'mensaje', 6,null,null,null,30);
$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1');
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2');
$q = "select id, nombre from tbl_comercio where activo = 'S' order by nombre";
$html->inSelect('Comercio', 'comer', 5, $q, $comr, '', '', 'multiple=multiple');
$arrVal = array('S','N');
$arrEtq = array('Si','No');
$html->inRadio("Activo", $arrVal, 'activo', $arrEtq, $activo);

echo $html->salida();
?>
<script type="text/javascript">
	function verifica() {
		document.admin_form.submit();
	}

	$(function() {
		$('textarea').supertextarea({
		   maxw: 230
		  , maxh: 100
		  , minw: 130
		  , minh: 20
		  , dsrm: {use: false}
		  , tabr: {use: false}
		  , maxl: 2000
		});
	});


</script>
<?php



$vista = 'select m.idmensaje as id, left(m.mensaje, 40) mensaje, m.fechaInicio, m.fechaFin, m.fecha,
            case m.activo when \'1\' then \''._FORM_YES.'\' else \''._FORM_NO.'\' end as activo, 
			case m.idcomercio when "todos" then "todos" else (select group_concat(c.nombre SEPARATOR ", ") 
				from tbl_comercio c where c.id = m.idcomercio) end comercio
            from tbl_mensajes m ';
$where = '';
$orden = ' m.activo desc, m.fecha desc';

$colEsp = array(
                array("e", _GRUPOS_EDIT_DATA, "css_edit", _TAREA_EDITAR),
                array("b", _GRUPOS_BORRA_DATA, "css_borra", _TAREA_BORRAR)
            );

$busqueda = array();

$columnas = array(
                array(_COMERCIO_ID, "id", "", "center", "left" ),
				array('Comercio', "comercio", "", "center", "left"),
                array(_SETUP_MENSAJE, "mensaje", "", "center", "left" ),
                array(_REPORTE_FECHA_INI, "fechaInicio", "", "center", "center" ),
                array(_REPORTE_FECHA_FIN, "fechaFin", "", "center", "center" ),
                array(_REPORTE_FECHA, "fecha", "", "center", "center" ),
                array(_USUARIO_ACTIVO, "activo", "", "center", "center" )
            );

if (_MOS_CONFIG_DEBUG) echo $vista.$where." order by ".$orden;
tabla( 900, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );

?>
