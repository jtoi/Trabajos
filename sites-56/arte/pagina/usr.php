<?php

/*
 * Tratamiento a los usuarios del sitio
 */

global $temp, $html, $tabla, $corCreo, $fun;

/**
 * Desactiva al usuario
 * @global object $ent
 * @global object $temp
 * @global object $fun
 * @param integer $id
 * @return string
 */
function usub($id) {
	global $ent, $temp, $fun;
	
	if (!($id = $ent->isEntero($id, 3))) return json_encode (array("error"=>$fun->idioma("Error en identificador del usuario")));
	
	if ($temp->query("update tbl_admin set activo = 0 where id = $id")) {
		return json_encode(array("error" => "", "data" => $fun->idioma("Datos correctamente guardados")));
	}
	return json_encode(array("Error: ". str_replace("'", "", preg_replace('/\"/', '', preg_replace("/\'/", "", ($temp->getErrorMsg())))), "data" =>""));
	
}

/**
 * Carga los datos del usuario para ponerlos en el formulario
 * @global object $ent
 * @global object $temp
 * @global object $fun
 * @param integer $id
 * @return string
 */
function usue($id) {
	global $ent, $temp, $fun;
	
	if (!($id = $ent->isEntero($id, 3))) return json_encode (array("error"=>$fun->idioma("Error en identificador del usuario")));
//error_log("select a.idrol, a.nombre, a.email, a.activo, case (select count(*) from tbl_colArtistaAdmin where idadmin = a.id) when 1 then (select idartista from tbl_colArtistaAdmin where idadmin = a.id) else '' end idart from tbl_admin a where id = $id");
	$temp->query("select a.idrol, a.nombre, a.email, a.activo, case (select count(*) from tbl_colArtistaAdmin where idadmin = a.id) when 1 then (select idartista from tbl_colArtistaAdmin where idadmin = a.id) else '' end idart, idtimezone from tbl_admin a where id = $id");
	$arrSal[] = $id;
	$arrSal[] = $temp->f('idrol');
	$arrSal[] = $temp->f('nombre');
	$arrSal[] = $temp->f('email');
	$arrSal[] = $temp->f('activo');
	$arrSal[] = $temp->f('idart');
	$arrSal[] = $temp->f('idtimezone');
	return json_encode(array("error"=>'', "data"=>$arrSal));
}

/**
 * Inserta el nombre del usuario
 * @global object $ent
 * @global object $temp
 * @param string $param
 * @return string
 */
function usur($param) {
	global $ent, $temp, $fun;
	//$("#nombrei").val() + '|' + $("#emaili").val() + '|' + $("#roli").val() + '|' + $('#arti').val() + '|' + $('#accion').val() + '|' + $("input[name='activo']:checked").val() + '|' + $('#id').val() + '|' + $("#idioma").val()
	error_log($param);
	$arrDatos = explode("|", $param);

	if (!($idi = $ent->isEntero($arrDatos[7], 2))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada del idioma")));
	}
	if (!($tmz = $ent->isEntero($arrDatos[9], 3))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada de la zona horaria")));
	}
	if (!($nomb = $ent->isAlfabeto($arrDatos[0], 100))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada del nombre")));
	}
	if (!($email = $ent->isCorreo($arrDatos[1], 150))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada del correo")));
	}
	if (strlen($arrDatos[3]) != 0) {
		if (!($art = $ent->isAlfanumerico($arrDatos[3], 7))) {
			return json_encode(array("error" => $fun->idioma("Error en la entrada del artista")));
		}
	} else {
		$art = $_SESSION['idartista'];
	}
	if (!($rol = $ent->isEntero($arrDatos[2], 7))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada del rol")));
	}
	if (!($acc = $ent->isAlfabeto($arrDatos[4], 10))) {
		return json_encode(array("error" => $fun->idioma("Error en la accion")));
	}
	if (!($act = $ent->isEntero($arrDatos[5], 4))) {
		return json_encode(array("error" => $fun->idioma("Error cambiando activo")));
	}
	if ($arrDatos[8] != 0) {
		if (!($ctr = $ent->isEntero($arrDatos[8], 2))) {
			return json_encode(array("error" => $fun->idioma("Error cambiando contrasena")));
		}
	} else $ctr = 0;
	
	$id = $arrDatos[6];
	$envCorr = 0;
	$temp->query("select iso2 from tbl_idioma where id = $idi");
	$idi2 = $temp->f('iso2');

	if ($acc == 'inserta') {// se va a insertar el usuario
		$temp->query("select count(*) tot from tbl_admin where email = '$email'");
		$md5 = $fun->suggestPassword(8);
		if ($temp->f('tot') > 0) {return json_encode(array("error" => $fun->idioma("Error: Esta direccion de correo ya existe en la base de datos")));}
		$q = "insert into tbl_admin (idrol, nombre, email, ididioma, md5, idtimezone) values ('$rol', '$nomb', '$email', '$idi', '$md5', '$tmz')";
		$envCorr = 1;
		$encb = $fun->idioma('correo bienvenida al sitio');
		$subj = $fun->idioma('Inscripcion en el sitio ArteOrganizer',$idi2);
	} elseif ($acc == 'modifica') {// se actualiza el usuario
		$q = "update tbl_admin set idrol = '$rol', nombre = '$nomb', email = '$email', activo = '$act', ididioma = '$idi', idtimezone = '$tmz' where id = $id";
		if ($ctr != 0) {
			$md5 = $fun->suggestPassword(8);
			$q = "update tbl_admin set idrol = '$rol', nombre = '$nomb', md5 = '$md5', email = '$email', activo = '$act', ididioma = '$idi', idtimezone = '$tmz' where id = $id";
			$envCorr = 1;
			$encb = $fun->idioma('correo renovacion contrasena');
			$subj = $fun->idioma('Renovacion de credenciales',$idi2);
		}
	}
	if (!$temp->query($q)) {
		return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));
	}
	
	if (strlen($id) == 0) {
		if (!$temp->query("select LAST_INSERT_ID() id")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));
		}
		$id = $temp->f('id');
	}
	
	$temp->query("insert into tbl_bitacora (idadmin, texto) values ({$_SESSION['id']}, 'Se inserta o modifica el usuario $nomb, los datos son: ". html_entity_decode($_POST['datos'], ENT_QUOTES)."')");

	if ($art != 'todos') {
		if (!$temp->query("delete from tbl_colArtistaAdmin where idadmin = $id")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));
		}
		if (!$temp->query("INSERT INTO tbl_colArtistaAdmin (idartista, idadmin) VALUES ('$art', '$id')")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));
		}
	}
	
	echo json_encode(array("error" => "", "data" => $fun->idioma("Datos correctamente guardados")));
	error_log("envcorreo=".$envCorr);
	if ($envCorr = 1) {
		error_log("{encb}=$encb");
		$cont = str_replace('{encb}', $encb, str_replace('{nomb}', $nomb, str_replace('{md5}', $md5, file_get_contents("pagina/correo_inscripcion_$idi2.html"))));
		$arrImg = array("images/logo.png", "images/icon-tweet.png", "images/icon-facebook.png");
		$nohtml = $fun->idioma('nohtml',$idi2);
		error_log($fun->smtpmail(array($email,$nomb), $subj, $cont, null, null, $arrImg, $nohtml));
	}

	return ;
}

/**
 * Construye la tabla inferior
 * @global object $tabla
 * @param string $datos
 */
function constrTabl($datos) {
	global $tabla;

	$arrDatos = explode("|", $datos);

	$tabla->columnas = $arrDatos[0];
	$tabla->tablas = $arrDatos[1];
	$tabla->buscar = $arrDatos[2];
	$tabla->orden = $arrDatos[3];
	$tabla->numpag = $arrDatos[4];
	$tabla->largpagina = $_SESSION['pagin'];
	$tabla->largpagina = 35;
//	$sale = $tabla->ejecQuery();
	echo json_encode(array("error" => '', "data" => $tabla->tabla()));
}

//Evalua si existe la función que le pasan desde javascript de ser así la invoca
if (function_exists($d['fun'])) {
	echo call_user_func($d['fun'], $d['datos']);
	exit;
}

/************************************************************************************************************************************************/

$titag = $fun->idioma("Usuarios");
$anchTit = "320px";

$columnas = "";
$activo = 1;
$roldef = 4;
$tmz = 115;

$html->inicio();
$html->classAncho = 'col-sm-12';
$html->inHide('usr', 'pag');

$buscar = "a.idrol = r.id and r.orden >= ".$_SESSION['grupo_rol'];
$tablas = 'tbl_admin a, tbl_roles r';
if ($_SESSION['grupo_rol'] > 5) {
	$buscar .= " and c.idartista = ".$_SESSION['idartista'];
	$tablas .= " tbl_colArtistaAdmin c ";
}

$html->inHide("a.id '".$fun->idioma('Editar')."{edit}', a.id '".$fun->idioma('Desactivar')."{borr}', a.nombre '".$fun->idioma('Usuario')."', a.email '".$fun->idioma('Correo')."{mail}', r.nombre '".$fun->idioma('Rol')."', case a.activo when '1' then '".$fun->idioma('Si')."' else '".$fun->idioma('No')."' end '".$fun->idioma('Activo')."', a.fechamod '".$fun->idioma('Fecha Mod')."{fec}', a.fecha_visita '".$fun->idioma('Fecha Visita')."{fec}', case a.activo when '1' then 'black' else 'navy' end '{col}'", 'columnas');
$html->inHide($buscar, 'buscar');
$html->inHide($tablas, 'tablas');
$html->inHide("Activo desc, ".$fun->idioma('Fecha Mod')." desc", 'orden');
$html->inHide('1', 'numpag');
$html->inHide('', 'accion');
$html->inHide('', 'id');
$html->inTextb($fun->idioma('Nombre'), $nombre, 'nombrei', '', '', '', 'required autofocus');
$html->inTextb($fun->idioma('Correo'), $correo, 'emaili', 'email', '', '', 'required autofocus');

$arrIni = "select id, nombre from tbl_roles where (orden >= {$_SESSION['grupo_rol']} ) order by orden";
$html->inSelect($fun->idioma("Roles"), "roli", 2, $arrIni, $roldef);

//$temp->query("select id, nombre from tbl_roles where (orden > {$_SESSION['grupo_rol']} or orden = {$_SESSION['grupo_rol']}) order by orden");
//$arrIni = $temp->loadRowList();
//array_unshift($arrIni, array("%", $fun->idioma("Cualquiera")));
//$html->inSelect($fun->idioma("Roles"), "rolm", 3, $arrIni, '');

if ($_SESSION['grupo_rol'] <= 5) {
	$temp->query("select id, nombre from tbl_artista order by nombre");
	$arrIni = $temp->loadRowList();
	array_unshift($arrIni, array("todos", $fun->idioma("Todos")));
	$html->inSelect($fun->idioma("Artistas"), "arti", 3, $arrIni, '');
} else {
	$html->inHide($_SESSION["idartista"], 'arti');
}

$temp->query("select id, idioma nombre from tbl_idioma order by nombre");
$valoIni = $temp->loadRowList();
$html->inSelect($fun->idioma('Idioma'), 'idioma', 3, $valoIni, 1);
$valoIni = array(1, 0);
$etiq = array($fun->idioma('Si'), $fun->idioma('No'));
$html->inRadio($fun->idioma('Activo').'?', $valoIni, 'activo', $etiq, $activo);

if ($_SESSION['grupo_rol'] <= 5) {
	$valoIni = array(1, 0);
	$etiq = array($fun->idioma('Si'), $fun->idioma('No'));
	$html->inRadio($fun->idioma('Generar contrasena').'?', $valoIni, 'contr', $etiq,'0');

	$arrIni = "select id, nombre from tbl_timezone order by nombre";
	$html->inSelect($fun->idioma("UsoHorario"), "tmz", 2, $arrIni, $tmz);
}

$formulario = "<div id='form' class='hide'>" . $html->salida() . "</div>";


$js = $homepage = file_get_contents('pagina/js/usr.js');
$scriptInf = "
<script type='text/javascript'>
$js
</script> ";
?>
