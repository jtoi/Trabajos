<?php

/* 
 * Página para cambiar los datos personales del usuario del sitio
 */

//var_dump($_SESSION);

global $temp;
/**
 * Salva los datos personales del Usuario
 * @global object $ent
 * @global ps_DB $temp
 * @param type $datos
 * @return type
 */
function salvDatPer ($datos) {
	global $ent, $temp, $fun;

	error_log($datos);
	$mil='';
	$arrDat = explode('|', $datos);
	error_log($arrDat[8]);
	if (!($nombre = $ent->isAlfabeto($arrDat[0], 100))) return json_encode (array("error"=>$fun->idioma("Error en la entrada del nombre")));
	if (!($email = $ent->isCorreo($arrDat[1], 150))) return json_encode (array("error"=>$fun->idioma("Error en la entrada del correo")));
	if (strlen($arrDat[2])) if (!($ctrs = $ent->isAlfanumerico($arrDat[2], 15))) return json_encode (array("error"=>$fun->idioma("Error en la entrada de la contrasena debe poner hasta 12 caracteres que sean digitos o letras")));
	if (!($fecha = $ent->isUrl($arrDat[3], 10))) return json_encode (array("error"=>$fun->idioma("Error en la entrada de la fecha")));
	if (!($hora = $ent->isUrl($arrDat[4], 5))) return json_encode (array("error"=>$fun->idioma("Error en la entrada de la hora")));
	if (!($id = $ent->isEntero($arrDat[5], 5))) return json_encode (array("error"=>$fun->idioma("Error en identificador del usuario")));
	if (!($dec = $ent->isAlfabeto($arrDat[6], 1))) return json_encode (array("error"=>$fun->idioma("Error en la entrada del separador decimal")));
	if (!($idi = $ent->isAlfabeto($arrDat[8], 2))) return json_encode (array("error"=>$fun->idioma("Error en la entrada del idioma")));
	if (strlen($arrDat[7])) if (!($mil = $ent->isAlfabeto($arrDat[7], 1))) return json_encode (array("error"=>$fun->idioma("Error en la entrada del separador de miles")));
	
//	salva los datos en tbl_admin
	$q = "update tbl_admin set nombre = '$nombre', email = '$email', ididioma = (select id from tbl_idioma where iso2 = '$idi') ";
	if (strlen($ctrs)) $q .= ", md5 = '$ctrs' ";
	$q .= "where id = ".$id;
	if (!$temp->query($q)) {
		return json_encode (array("error"=>$fun->idioma("Error: Se produjo un error al salvar los datos llame al desarrollador")."dpr37", "data"=>""));
	}
	$temp->query("insert into tbl_bitacora (idadmin, texto) values ('{$_SESSION['id']}', 'El usuario $nombre cambia sus datos; $email, $ctrs, $dec, $mil, $fecha, $hora')");
	
	$_SESSION['idioma'] = $idi;
	
	//salva los datos de setup
	if (!$temp->query("update tbl_colSetupAdmin set valor = '$dec' where idadmin = $id and idsetup = 11")) {
		return json_encode (array("error"=>$fun->idioma("Error: Se produjo un error al salvar los datos llame al desarrollador")."dpr42", "data"=>""));
	} else $_SESSION['decims'] = $dec;
	
	if (!$temp->query("update tbl_colSetupAdmin set valor = '$mil' where idadmin = $id and idsetup = 12")) {
		return json_encode (array("error"=>$fun->idioma("Error: Se produjo un error al salvar los datos llame al desarrollador")."dpr46", "data"=>""));
	} else $_SESSION['miless'] = $mil;
	
	if (!$temp->query("update tbl_colSetupAdmin set valor = '$fecha' where idadmin = $id and idsetup = 13")) {
		return json_encode (array("error"=>$fun->idioma("Error: Se produjo un error al salvar los datos llame al desarrollador")."dpr50", "data"=>""));
	} else $_SESSION['fechaf'] = $fecha;
	
	if (!$temp->query("update tbl_colSetupAdmin set valor = '$hora' where idadmin = $id and idsetup = 14")) {
		return json_encode (array("error"=>$fun->idioma("Error: Se produjo un error al salvar los datos llame al desarrollador")."dpr54", "data"=>""));
	} else $_SESSION['horaf'] = $hora;
	//error_log($q);
	return json_encode (array("error"=>"", "data"=>$fun->idioma("Datos correctamente guardados"), "idioma"=>$idi));
}

/**
 * Genera el número de ejemplo en la página de datios personalesç
 * para evaluar el formato numérico a usar
 * @global object $ent
 * @param type $datos
 * @return type
 */
function genNumEj($datos) {
	global $ent;
//	sleep(2);
	error_log($datos);
	$arrDat = explode('|', $datos);
	$mil='';
	if (!($dec = $ent->isAlfabeto($arrDat[0], 1))) return json_encode (array("error"=>$fun->idioma("Error en la entrada del separador decimal"))); 
	if (strlen($arrDat[1])) if (!($mil = $ent->isAlfabeto($arrDat[1], 1))) return json_encode (array("error"=>$fun->idioma("Error en la entrada del separador de miles"))); 
	
	return json_encode(array("error"=>"", "data"=> number_format(1234.56, 2, $dec, $mil)));
}

/**
 * Cambia en los datos personales como se visualiza la fecha - hora
 * al cambiar el usuario los dropbox con los distintos formatos
 * @global object $ent
 * @param string $formato Formato de la fecha y hora
 * @return string
 */
function genFecEj($formato) {
	global $ent;
	if ($formato = $ent->isUrl($formato, 12)) {
		return json_encode (array("error"=>"","data"=>date($formato)));
	}
	
	return json_encode(array("error"=>$fun->idioma('Error: No es un formato de fecha valido'),"data"=>""));
}

//Evalua si existe la función que le pasan desde javascript de ser así la invoca
if (function_exists($d['fun'])) {
	echo call_user_func ($d['fun'],$d['datos']);
	exit;
}


//Hace el dibujado de formularios y tablas de la página a mostrar
$titag = $fun->idioma('Datos Personales');
$anchTit = "360px";

$temp->query("select nombre, email from tbl_admin where id = {$_SESSION['id']}");
$nombre = $temp->f('nombre');
$correo = $temp->f('email');

//echo (date($_SESSION['fechaf']));
//var_dump($_SESSION);

$html->inicio();
$html->classAncho = 'col-sm-12';
$html->inHide($_SESSION['id'], 'id');
$html->inTextb($fun->idioma('Nombre'), $nombre, 'nombre', '', '', '', 'required autofocus');
$html->inTextb($fun->idioma('Correo'), $correo, 'email', 'email', '', '', 'required autofocus');
$html->inTextb($fun->idioma("Contrasena"), "", 'contr', 'password');
$html->classAncho = 'col-sm-7';
$html->inTextb($fun->idioma("Vuelva a escribirla"), "", 'contr2', 'password');
$arrIni = array(array("d/m/Y", "dd/mm/YYYY"), array("m/d/Y","mm/dd/YYYY"), array("M d, Y","M dd, YYYY"), array("d M Y","dd M YYYY"));
$html->inSelect($fun->idioma("Formato de fechas"), "fechaf", 3, $arrIni, $_SESSION['fechaf'] );
$html->classAncho = 'col-sm-12';
$html->inTextoL('','fecba');
$arrIni = array(array("H:i","24 horas"), array("h:i a","12 horas"));
$html->inSelect($fun->idioma("Formato de Horas"), "hrsf", 3, $arrIni, $_SESSION['horaf'] );

$arrIni = array(array(".",$fun->idioma("Punto (.)")), array(",",$fun->idioma("Coma (,)")));
$html->inSelect($fun->idioma("Separador de decimales"), "decs", 3, $arrIni, $_SESSION['decims'] );
$html->classAncho = 'col-sm-12';
$html->inTextoL('','numba');
$arrIni = array(array("",$fun->idioma("Sin separador")), array(",",$fun->idioma("Coma (,)")), array(" ",$fun->idioma("Espacio ( )")));
$html->inSelect($fun->idioma("Separador de miles"), "mils", 3, $arrIni, $_SESSION['miless'] );
$arrIni = "select iso2 id, idioma nombre from tbl_idioma";
$html->inSelect($fun->idioma("Idioma"), "idio", 2, $arrIni, $_SESSION['idioma'] );
$formulario =  $html->salida();


$js = $homepage = file_get_contents('pagina/js/dpr.js');
$scriptInf = "
<script type='text/javascript'>
$js
</script> ";

?>