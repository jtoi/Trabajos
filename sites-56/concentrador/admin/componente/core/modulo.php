<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); 

$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/core/admin.html.php'));
$contenido = explode('{muestra}', $admin_mod);
global $database;

$idioma = new ps_DB;
$grup = new ps_DB;
$grup1 = new ps_DB;

$sel_idioma = "select ididioma, titulo from tbl_idioma order by ididioma";
$idioma->query($sel_idioma);

while ($idioma->next_record()){
	$id_idioma[++$j] = $idioma->f('ididioma');
	$nombre_idioma[$j] = $idioma->f('titulo');
}

if ($_REQUEST['inserta']) {
	$query = "select titulo from tbl_idioma";
	$grup->query($query);
	while ($grup->next_record()){
		$q = "insert into tbl_idioma_".strtolower($grup->f('titulo'))." (texto) values ('".RTESafe($_REQUEST[strtolower($grup->f('titulo'))])."')";
		$database->setQuery($q);
		$database->query();
	}
	$q = "insert into tbl_modulo (idcomponente, nombre, titulo, orden, visible) values (".$_REQUEST['compte'].", '".$_REQUEST['nombre']."', ".$grup1->last_insert_id().", ".$_REQUEST['orden'].", '".$_REQUEST['visible']."') ";
	$grup1->query($q);
}

if ($_REQUEST['modifica']) {
	$q1 = "select titulo from tbl_modulo where idmodulo = ".$_REQUEST['modifica'];
	$grup->query($q1);
	$titulo = $grup->f('titulo');
	
	for ($x=1; $x<=count($nombre_idioma); $x++) {
		$idiomas = "update tbl_idioma_".strtolower($nombre_idioma[$x])." set texto = '".RTESafe($_REQUEST[strtolower($nombre_idioma[$x])])."' where ididioma = $titulo";
		$grup1->query($idiomas);
	}
	$ql = "update tbl_modulo set idcomponente = ".$_REQUEST['compte'].", nombre = '".$_REQUEST['nombre']."', orden = ".$_REQUEST['orden'].", visible = '".$_REQUEST['visible']."' where idmodulo = ".$_REQUEST['modifica'];
	$grup1->query($ql);
	
}

if ($_REQUEST['borrar']) {
	$q1 = "select titulo from tbl_modulo where idmodulo = ".$_REQUEST['borrar'];
	$grup->query($q1);
	$titulo = $grup->f('titulo');
	
	for ($x=1; $x<=count($nombre_idioma); $x++) {
		$q = "delete from tbl_idioma_".strtolower($nombre_idioma[$x])." where ididioma = ".$titulo;
		$grup->query($q);
	}
	
	$q = "delete from tbl_modulo where idmodulo = ".$_REQUEST['borrar'];
	$grup->query($q);
}

$titulo = 'M&oacute;dulos';
$ancho = '500';

if (!$_REQUEST['cambiar']) {
	$titulo_tarea = 'Insertar M&oacute;dulo';
	$campo_pase = '<input name="inserta" type="hidden" value="true" />';
}
else {
	$gr = new ps_DB;
	$qg = "select * from tbl_modulo where idmodulo = ".$_REQUEST['cambiar'];
	$gr->query($qg);
	$campo_pase = '<input name="modifica" type="hidden" value="'.$_REQUEST['cambiar'].'" />';
	
	if ($gr->next_record()) {
		$titulo_tarea = 'Modificar M&oacute;dulo';
		$idcomponente = $gr->f('idcomponente');
		$orden = $gr->f('orden');
		$nombre = $gr->f('nombre');
		$visible = $gr->f('visible');
		$idioma = $gr->f('titulo');
	}
	for ($x = 1; $x <= $j; $x++){
		$qg = "select texto from tbl_idioma_".$nombre_idioma[$x]." where ididioma = ".$idioma;
		$gr->query($qg);
		if ($gr->next_record())	$texto[$x] = $gr->f('texto');
	}
}

$tripa = '<table width="100%" cellspacing="0" cellpadding="0">'.$campo_pase.'<tr>
	<td class="derecha">Nombre:</td>
	<td class="izquierda"><input class="formul" type="text" name="nombre" value="'.$nombre.'" /></td><td class="sepr"></td>
	<td class="derecha">Componente:</td>
	<td class="izquierda"><select class="formul" name="compte">';
$tripa .= opciones_sel('select idcomponente as id, concat(c.nombre,\'-\',h.nombre) as nombre'
        . ' from tbl_componente c, tbl_hoteles h'
        . ' where h.idhotel = c.idhotel'
        . ' order by nombre desc', $idcomponente);
$tripa .= '</select></td></tr>
	<tr>
	<td class="derecha">Orden:</td>
	<td class="izquierda"><select name="orden" class="formul" id="orden">';
$tripa .= opciones(1, 10, $orden);
$tripa .= '</select></td>
	<td class="sepr"></td>
	<td class="derecha">Visible:</td>';
if (!$visible || $visible == 'S')
	$tripa .= '<td class="izquierda"><input name="visible" type="radio" id="visibles" value="S" checked="checked" />';
else
	$tripa .= '<td class="izquierda"><input name="visible" type="radio" id="visibles" value="S" />';
$tripa .= '<label for="visibles">Si</label><br />';
if ($visible == 'N')
	$tripa .= '<input name="visible" type="radio" value="N" id="visiblen" checked="checked" />';
else
	$tripa .= '<input name="visible" type="radio" value="N" id="visiblen" />';
$tripa .= '<label for="visiblen">No</label></td>
	</tr>
	<tr>
		<td colspan="5" align="center">T&iacute;tulo:</td>
	</tr>
	<tr>
	<td colspan="5"><table align="center" width="100%" border="1" cellspacing="0" cellpadding="0">';
for ($x = 1; $x <= $j; $x++){
	$tripa .= '<tr class="'.strtolower($nombre_idioma[$x]).'">
				<td id="idioma" width="20%" align="right">'.$nombre_idioma[$x].':</td>
				<td id="idioma" align="left"><script language="JavaScript" type="text/javascript">writeRichText(\''.strtolower($nombre_idioma[$x]).'\', \''.$texto[$x].'\', 400, 100, true, false);</script></td>
			</tr>';
}
$tripa .= '</table></td></tr></table>';


echo str_replace('{titulo}', $titulo, $contenido[0]);
$contenido[2] = str_replace('{ancho_tabla}', $ancho, $contenido[2]);
$contenido[2] = str_replace('{titulo_tarea}', $titulo_tarea, $contenido[2]);
$contenido[2] = str_replace('{tripa}', $tripa, $contenido[2]);

echo "
<script language=\"JavaScript\" type=\"text/javascript\" src=\"../js/html2xhtml.js\"></script>
<script language=\"JavaScript\" type=\"text/javascript\" src=\"../js/html2xhtml_es.js\"></script>
<script language=\"JavaScript\" type=\"text/javascript\" src=\"../js/richtext.js\"></script>

<script language=\"JavaScript\" type=\"text/javascript\">
initRTE(\"../images/rte/\", \"../js/\", \"\", true);

function verifica() {
	updateRTEs();
	if (
			(checkField (document.admin_form.nombre, isAlphanumeric, ''))
		) {
		return true;
	}
	return false;
}
</script>";
echo $contenido[2];

$vista = 'select idmodulo as id, m.nombre modulo, concat(c.nombre,\'-\',h.nombre) as componente, orden, visible'
        . ' from tbl_modulo m, tbl_componente c, tbl_hoteles h ';
		
$where = 'where m.idcomponente = c.idcomponente and c.idhotel = h.idhotel';
$orden = 'modulo asc';

$colEsp = array(
				array("e", "Editar Datos", "../images/edit.gif", "Editar"),
				array("b", "Borrar Registro", "../images/borra.gif", "Borrar")
			);

$busqueda = array();

$columnas = array( 
				array("Modulo", "modulo", "", "center", "left" ),
				array("Componente", "componente", "170", "center", "center"),
				array("Orden", "orden", "50", "center", "center"),
				array("Visible", "visible", "50", "center", "center")
			);

tabla( 500, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>