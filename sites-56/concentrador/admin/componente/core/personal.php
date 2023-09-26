<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML();
global $temp;
$ent = new entrada;

if ($_POST['modifica']) {
	$cadError = false;
//	if (!($idioma = $ent->isAlfabeto($_POST['idioma']))) $cadError = 'Falló la modificación de los Datos, el idioma no es válido.';
	if ($_POST['contrasena1']) {
		if (!($contrase1 = $ent->isAlfanumerico($_POST['contrasena1']))) $cadError = 'Falló la modificación de los Datos, la contraseña no es válida.';
		if (!($contrase2 = $ent->isAlfanumerico($_POST['contrasena2']))) $cadError = 'Falló la modificación de los Datos, la contraseña no es válida.';
	}
	if (!($nombre = $ent->isAlfanumerico($_POST['nombre']))) $cadError = 'Falló la modificación de los Datos, el nombre no es válido.';
	if (!($correo = $ent->isCorreo($_POST['correo']))) $cadError = 'Falló la modificación de los Datos, el correo no es válido.';
	if (!($idioma = $ent->isAlfabeto($_POST['idioma'], 8))) $cadError = 'Falló la modificación de los Datos por el idioma.';
	if (!($fecfor = $ent->isUrl($_POST['fecfor'], 12))) $cadError = 'Falló la modificación de los Datos por el formato de la fecha.';
	if (!($horfor = $ent->isUrl($_POST['horfor'], 20))) $cadError = 'Falló la modificación de los Datos por el formato de la hora.';
	$correot = $ent->isReal($_POST['correot']);
	$usequery = $ent->isAlfabeto($_POST['query'], 1);
	switch ($_POST['numfor']) {
		case 1:
			$arrF[0] = '&comma;';$arrF[1] = '&period;';
		break;
		case 2:
			$arrF[0] = '&nbsp;';$arrF[1] = '&period;';
		break;
		case 3:
			$arrF[0] = '&nbsp;';$arrF[1] = '&comma;';
		break;
		case 4:
			$arrF[0] = '&period;';$arrF[1] = '&comma;';
		break;
	}
	$fec = $fecfor." ".$horfor;

	if (($contrase1 && $contrase2) && ($contrase1 == $contrase2) ){
		$calc_md5 = sha1($_POST['login'].$contrase1.'Lo que los hace hermoso es algo invisible...los ojos no siempre ven. Hay que buscar con el corazón.');


		//comprueba que la contraseña no haya sido usada anteriormente
		$query = "select md5, md5Old from tbl_admin where idadmin = ".$_SESSION['id'];
		$temp->query($query);
		$cvieja = $temp->f('md5');
		if ($calc_md5 == $temp->f('md5') || $calc_md5 == $temp->f('md5Old')) {
			$cadError = "La nueva contraseña no puede haber sido usada anteriormente";
		} else {
			//hace el update de todos los datos
			$q = "update tbl_admin set nombre = '".$nombre."', email = '".$correo."', md5Old = md5, usequery = '$usequery', "
					. " fechaPass = ".mktime(0, 0, 0, date("m"), date("d"), date("Y")).", md5 = '".$calc_md5."', correoT = "
					. $correot.", param = 'idioma=$idioma', formatFecha = '$fec', cantDec = '2', separMiles = '".$arrF[0]."',"
					. "separDecim = '".$arrF[1]."' where idadmin = ".$_SESSION['id'];
		}
	} else
		$q = "update tbl_admin set nombre = '".$nombre."', email = '".$correo."', correoT = ".$correot.", "
				. "param = 'idioma=$idioma', usequery = '$usequery', formatFecha = '$fec', cantDec = '2', separMiles = '"
				. $arrF[0]."', separDecim = '".$arrF[1]."'"
				. " where idadmin = ".$_SESSION['id'];
	if (!$cadError) {
		$temp->query($q);
		$cadError = 'Sus nuevos datos han sido correctamente guardados';

		$_SESSION['sepdecim'] = $arrF[1];
		$_SESSION['sepmiles'] = $arrF[0];
		$_SESSION['formtfecha'] = $fec;
	}
}

$titulo = _PERSONAL_TITULO;
$ancho = '600';
//$temp->_debug=true;
$qg = ' select a.idadmin, a.nombre, a.login, a.email, r.idrol, a.activo, correoT, TimeZone, usequery, formatFecha, separMiles, separDecim'
	. ' from tbl_admin a , tbl_roles r'
	. ' where r.idrol = a.idrol'
	. ' and a.idadmin = '.$_SESSION['id'];
$temp->query($qg);
if ($temp->_errorMsg) echo $temp->_errorMsg;
$titulo_tarea = _TAREA_MODIFICAR." "._PERSONAL_TITULO;

$html->java = "
<script language=\"JavaScript\" src=\"../js/md5.js\" type=\"text/javascript\"></script>
<script language=\"JavaScript\" type=\"text/javascript\">
function verifica() {
	if (document.admin_form.contrasena1.value.length > 1) {
		if (
				(checkField (document.admin_form.contrasena1, isAlphanumeric, ''))&&
				(checkField (document.admin_form.contrasena2, isAlphanumeric, ''))
			) {
			if (document.admin_form.contrasena1.value == document.admin_form.contrasena2.value) {
				if (
						(checkField (document.admin_form.nombre, isAlphanumeric, ''))&&
						(checkField (document.admin_form.correo, isEmail, ''))&&
						(checkField (document.admin_form.login, isAlphanumeric, ''))
					) {
					return true;
				}
			}
			else alert('"._PERSONAL_ALERT_CONTRAS."');
		}
	} else {
			if (document.admin_form.contrasena1.value == document.admin_form.contrasena2.value) {
				if (
						(checkField (document.admin_form.nombre, isAlphanumeric, ''))&&
						(checkField (document.admin_form.correo, isEmail, ''))&&
						(checkField (document.admin_form.login, isAlphanumeric, ''))
					) {
					return true;
				}
		}
	}
	return false;
}
</script>";
$arrFormat = explode(' ', $temp->f('formatFecha'));
if (count($arrFormat) == 3) $arrFormat[1] = $arrFormat[1]." ".$arrFormat[2];

$html->idio = $_SESSION['idioma'];
$html->tituloPag = $titulo;
$html->tituloTarea = $titulo_tarea;
$html->anchoTabla = 500;
$html->anchoCeldaI = $html->anchoCeldaD = 245;

$html->inHide($_SESSION['id'], 'modifica');
$html->inTextb(_FORM_NAME, $temp->f('nombre'), 'nombre');
$html->inTextb(_FORM_CORREO, $temp->f('email'), 'correo');
$html->inTextb(_PERSONAL_IDENT, $temp->f('login'), 'login', null, null, 'readonly="true"');
$html->inPass(_PERSONAL_PASS, null, 'contrasena1');
$html->inPass(_PERSONAL_REPASS, null, 'contrasena2');
$valorIni = array(1,0);
$etiq = array(_FORM_YES, _FORM_NO);
$html->inRadio(_PERSONAL_REC_CORREO, $valorIni, 'correot', $etiq, $temp->f('correoT'));
$valorIni = array('english', 'spanish', 'italiano');
$etiq = array('English', 'Español', 'Italiano');
$html->inRadio(_PERSONAL_IDIOMA, $valorIni, 'idioma', $etiq, $_SESSION['idioma']);
// $valorIni = array('S','N');
// $etiq = array(_FORM_YES, _FORM_NO);
// $html->inRadio(_PERSONAL_QUERY, $valorIni, 'query', $etiq, $temp->f('usequery'), _PERSONAL_QUERYEXP);

$valorIni = array(
		array('d/m/Y', date('d/m/Y')), 
		array('d/m/y', date('d/m/y')), 
		array('d-m-Y', date('m-d-Y')),
		array('d-m-y', date('d-m-y')),
		array('m/d/Y', date('m/d/Y')),
		array('m/d/y', date('m/d/y')), 
		array('m-d-y', date('m-d-y'))
);
$html->inSelect(_PERSONAL_FECHA, 'fecfor', 3, $valorIni, $arrFormat[0]);
$valorIni = array(
		array('H:i:s', date('H:i:s')),
		array('g:i:s a', date('g:i:s a')),
);
$html->inSelect(_PERSONAL_HORA, 'horfor', 3, $valorIni, $arrFormat[1]);
$valorIni = array(
		array(1, number_format(1234567, 2, '.', ',')),
		array(2, number_format(1234567, 2, '.', ' ')),
		array(3, number_format(1234567, 2, ',', ' ')),
		array(4, number_format(1234567, 2, ',', '.'))
);
$html->inSelect(_PERSONAL_NUM, 'numfor', 3, $valorIni);

echo $html->salida();

if ($cadError)
	echo '<script language="JavaScript" type="text/javascript">
			alert("'.$cadError.'");
		</script>';
?>
