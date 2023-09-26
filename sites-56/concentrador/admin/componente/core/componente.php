<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); 

$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/core/admin.html.php'));
$contenido = explode('{muestra}', $admin_mod);

$hot = new ps_DB;
$grup = new ps_DB;

$sel_grup = "select idhotel, nombre from tbl_hoteles order by categoria desc";
$hot->query($sel_grup);

while ($hot->next_record()){
	$id_hotel[++$i] = $hot->f('idhotel');
	$nombre_hotel[$i] = $hot->f('nombre');
}

if ($_REQUEST['inserta']) {
	$q = "insert into tbl_componente (nombre, idhotel) values ('".$_REQUEST['nombre']."', ".$_REQUEST['hotel'].")";
	$grup->query($q);
}

if ($_REQUEST['modifica']) {
	$q = "update tbl_componente set nombre = '".$_REQUEST['nombre']."', idhotel = ".$_REQUEST['hotel']." where idcomponente = ".$_REQUEST['modifica'];
	$grup->query($q);
}

if ($_REQUEST['borrar']) {
	$q = "delete from tbl_componente where idcomponente = ".$_REQUEST['borrar'];
	$grup->query($q);
}

$titulo = 'Componentes';
$ancho = '500';

if (!$_REQUEST['cambiar']) {
	$titulo_tarea = 'Insertar Componente';
	$tripa = '<table width="100%" cellspacing="0" cellpadding="0"><input name="inserta" type="hidden" value="true" /><tr><td class="derecha">Nombre Componente:</td><td class="izquierda"><input class="formul" name="nombre" id="nombre" type="text" /></td><td class="sepr"></td><td class="derecha">Hotel:</td><td class="izquierda"><select class="formul" name="hotel">';
	for ($x = 1; $x <= $i; $x++){
		$tripa .= '<option value="'.$id_hotel[$x].'">'.$nombre_hotel[$x].'</option>';
	}
	$tripa .= '</select></td></tr><tr><td width="50%" colspan="2"></td><td class="sepr"></td><td width="50%" colspan="2"></td></tr></table>';
}
else {
	$gr = new ps_DB;
	$qg = "select * from tbl_componente where idcomponente = ".$_REQUEST['cambiar'];
	$gr->query($qg);
	
	if ($gr->next_record()) {
		$titulo_tarea = 'Modificar Componente';
		$tripa = '<table width="100%" cellspacing="0" cellpadding="0"><input name="modifica" type="hidden" value="'.$_REQUEST['cambiar'].'" /><tr><td class="derecha">Nombre Componente:</td><td class="izquierda"><input class="formul" value="'.$gr->f('nombre').'" name="nombre" id="nombre" type="text" /></td><td class="sepr"></td><td class="derecha">Hotel:</td><td class="izquierda"><select class="formul" name="hotel">';
	for ($x = 1; $x <= $i; $x++){
		if ($id_hotel[$x] == $gr->f('idhotel')) $tripa .= '<option selected value="'.$id_hotel[$x].'">'.$nombre_hotel[$x].'</option>';
		else $tripa .= '<option value="'.$id_hotel[$x].'">'.$nombre_hotel[$x].'</option>';
	}
	$tripa .= '</select></td></tr><tr><td width="50%" colspan="2"></td><td class="sepr"></td><td width="50%" colspan="2"></td></tr></table>';
	}
}

echo str_replace('{titulo}', $titulo, $contenido[0]);
$contenido[2] = str_replace('{ancho_tabla}', $ancho, $contenido[2]);
$contenido[2] = str_replace('{titulo_tarea}', $titulo_tarea, $contenido[2]);
$contenido[2] = str_replace('{tripa}', $tripa, $contenido[2]);

echo "<script language=\"JavaScript\" type=\"text/javascript\">
function verifica() {
	if (
			(checkField (document.admin_form.nombre, isAlphanumeric, ''))
		) {
		return true;
	}
	return false;
}
</script>";
echo $contenido[2];

$vista = 'select idcomponente as id, c.idhotel, c.nombre, h.nombre as hotel'
        . ' from tbl_componente c, tbl_hoteles h ';
		
$where = 'where c.idhotel = h.idhotel';
$orden = 'nombre asc';

$colEsp = array(
				array("e", "Editar Datos", "../images/edit.gif", "Editar"),
				array("b", "Borrar Registro", "../images/borra.gif", "Borrar")
			);

$busqueda = array();

$columnas = array( 
				array("Componente", "nombre", "", "center", "left" ),
				array("Hotel", "hotel", "170", "center", "center")
			);

tabla( 500, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>