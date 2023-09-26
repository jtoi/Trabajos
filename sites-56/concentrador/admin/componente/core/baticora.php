<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/core/admin.html.php'));
$admin_mod = str_replace("{_FORM_SEARCH}", _FORM_SEARCH, $admin_mod);
$admin_mod = str_replace("{_FORM_CANCEL}", _FORM_CANCEL, $admin_mod);
$admin_mod = str_replace("{_FORM_SEND}", _FORM_SEND, $admin_mod);
$contenido = explode('{muestra}', $admin_mod);

$titulo = _BITACORA_TITULO;
$ancho = '500';
$fecha2 = date('d/m/Y');

$tripa = '<table width="100%" cellspacing="0" cellpadding="0"><tr>
	<td class="derecha">'._FORM_NAME.':</td>
	<td class="izquierda"><select class="formul" name="nombre"><option value="">--'._FORM_SELECT.'--</option>';
$tripa .= opciones_sel("select a.idadmin as id, a.nombre from tbl_admin a, tbl_roles r where a.idrol = r.idrol and r.orden >=".$_SESSION['grupo_rol'], $id);
$tripa .= '</select></td><td class="sepr"></td>
	<td class="derecha">'._FORM_FECHA_INICIO.':</td>
	<td class="izquierda"><input class="formul" value="01/01/2008" name="fecha1" id="fecha1" type="text" size="12" maxlength="10" /><br />(dd/mm/yyyy)</td></tr>
	<tr>
		<td class="derecha">'._PERSONAL_IDENT.':</td>
		<td class="izquierda"><select class="formul" name="login"><option value="">--'._FORM_SELECT.'--</option>';
$tripa .= opciones_sel("select a.idadmin as id, a.login as nombre from tbl_admin a, tbl_roles r where a.idrol = r.idrol and r.orden >=".$_SESSION['grupo_rol'], $id);
$tripa .= '</select></td><td class="sepr"></td>
		<td class="derecha">'._FORM_FECHA_FINAL.':</td>
		<td class="izquierda"><input class="formul" id="fecha2" name="fecha2" value="'.$fecha2.'" type="text" size="12" maxlength="10" /><br />(dd/mm/yyyy)</td>
	</tr>
</table>';

echo str_replace('{titulo}', $titulo, $contenido[0]);
$contenido[1] = str_replace('{ancho_tabla}', $ancho, $contenido[1]);
$contenido[1] = str_replace('{tripa}', $tripa, $contenido[1]);
$contenido[1] = str_replace('{anchoCelda}', ($ancho-14), $contenido[1]);

echo $contenido[1];


echo "
<script language=\"JavaScript\" type=\"text/javascript\">
//var el = document.getElementById(id);
function verifica(formul) {
	if (
			(checkField (formul.fecha1, isDate, true))&&
			(checkField (formul.fecha2, isDate, true))
		) {
		if (comparaFecha2(formul.fecha1, formul.fecha2, '"._BITACORA_ALERT_FECHASDIF."'))
			return true;
	}
	return false;
}

setTimeout(\"window.open('index.php?componente=core&pag=baticora', '_self')\", 120000);

</script>";


$vista = "select b.idbaticora as id, a.nombre, a.login, r.nombre rol, b.texto, b.fecha, a.idadmin,
			case a.idcomercio when 'todos' then 'todos' else (select nombre from tbl_comercio where idcomercio = a.idcomercio) end comercio
			from tbl_admin a, tbl_baticora b, tbl_roles r";
$where = 'where a.idadmin != 10 and b.idadmin = a.idadmin and a.idrol = r.idrol and orden >='.$_SESSION['grupo_rol'];
$orden = 'b.fecha desc';

$colEsp = array();

if ($_REQUEST['fecha1']) {
	for ($x=1; $x<=2; $x++) {
		$cadena = explode('/', $_REQUEST['fecha'.$x]);
		$fecha[$x] = mktime(0,0,0,$cadena[1],$cadena[0],$cadena[2]);
	}
}


$busqueda = '';
if ($_REQUEST['nombre']) $busqueda .= "a.idadmin = ". $_REQUEST['nombre'] .",";
if ($_REQUEST['login']) $busqueda .= "a.idadmin = ".$_REQUEST['login'].",";
if ($fecha[1]) $busqueda .= "b.fecha >= ".$fecha[1].",";
if ($fecha[2]) $busqueda .= "b.fecha <= ".$fecha[2].",";

$busqueda = explode(',', trim($busqueda, ','));

$columnas = array(
				array(_FORM_NAME, "nombre", "120", "center", "left" ),
				array(_PERSONAL_IDENT, "login", "50", "center", "left"),
				array(_MENU_ADMIN_COMERCIO, "comercio", "", "center", "left"),
				array('Rol', "rol", "50", "center", "left"),
				array(_FORM_FECHA, "fecha", "70", "center", "center"),
				array(_BITACORA_TEXT, "texto", "", "center", "left")
			);

tabla( 800, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>
