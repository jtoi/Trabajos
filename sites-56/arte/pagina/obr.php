<?php

/* 
 * Pagina para el tratamiendo de los artistas en el sitio
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
function artb($id) {
	global $ent, $temp, $fun;
	
	if (!($id = $ent->isEntero($id, 3))) return json_encode (array("error"=>$fun->idioma("Error en identificador del usuario")));
	
	if ($temp->query("update tbl_artista set activo = 0 where id = $id")) {
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
function arte($id) {
	global $ent, $temp, $fun;
	
	if (!($id = $ent->isEntero($id, 3))) return json_encode (array("error"=>$fun->idioma("Error en identificador del usuario")));
	
	$temp->query("select a.nombre, a.correo, a.activo, a.seudonimo, a.direccion, a.coordenadas from tbl_artista a where id = $id");
	$arrSal[] = $id;
	$arrSal[] = $temp->f('nombre');
	$arrSal[] = $temp->f('correo');
	$arrSal[] = $temp->f('activo');
	$arrSal[] = $temp->f('seudonimo');
	$arrSal[] = $temp->f('direccion');
	$arrSal[] = $temp->f('coordenadas');
	return json_encode(array("error"=>'', "data"=>$arrSal));
}

function imgs(){
	global $ent, $temp, $fun, $subir;
	foreach ($_FILES as $key => $value) {
		error_log("$key => $value");
	}
}

/**
 * Inserta el nombre del usuario
 * @global object $ent
 * @global object $temp
 * @param string $param
 * @return string
 */
function arti($param) {
	global $ent, $temp, $fun, $subir;
	//$("#nombre").val() + '|' + $("#email").val() + '|' + $("#seudo").val() + '|' + $('#direcc').val() + '|' + $("input[name='activo']:checked").val() + '|' + $('#id').val()  + '|' + coord + '|' + $('#idioma').val() + '|' + $('#accion').val() + '|' + $('#imagen').val()

	
	$dir = $coord = $report = '';

	$arrDatos = explode("|", $_POST['datos']);

	if (!($nomb = $ent->isAlfabeto($arrDatos[0], 100))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada del nombre")));
	}
	if (!($idi = $ent->isEntero($arrDatos[7], 2))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada del idioma")));
	}
	if (strlen($arrDatos[2])) {
		if (!($seud = $ent->isAlfabeto($arrDatos[2], 100))) {
			return json_encode(array("error" => $fun->idioma("Error en la entrada del nombre")));
		}
	} else $seud = $nomb;
	if (!($email = $ent->isCorreo($arrDatos[1], 150))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada del correo")));
	}
	if (!($acc = $ent->isAlfabeto($arrDatos[8], 10))) {
		return json_encode(array("error" => $fun->idioma("Error en la accion")));
	}
	if (!($act = $ent->isAlfanumerico($arrDatos[4], 200))) {
		return json_encode(array("error" => $fun->idioma("Error cambiando activo")));
	}
	if (strlen($arrDatos[3]) && !($dir = $ent->isAlfanumerico($arrDatos[3], 200))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada de la dirección")));
	}
//	error_log("numero=".$ent->isNumero($arrDatos[6], 26));
	if (strlen($arrDatos[6]) && !($coord = $ent->isNumero(str_replace(" ", "", $arrDatos[6]), 26))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada de las coordenadas")));
	}
	
	$ida = $arrDatos[5];
	$envCorr = 0;
	$temp->query("select iso2 from tbl_idioma where id = $idi");
	$idi2 = $temp->f('iso2');
	$rol = 3;
	
	//verifica que el correo no se repita
	$temp->query("select count(*) tot from tbl_admin where email = '$email'");
	$md5 = $fun->suggestPassword(8);
	if ($temp->f('tot') > 0) {return json_encode(array("error" => $fun->idioma("Error: Esta direccion de correo ya existe en la base de datos")));}

	if ($acc == 'inserta') {// se va a insertar el artista
		
		//lo inserta como artista
		if (!$temp->query("insert into tbl_artista (nombre, seudonimo, correo, direccion, coordenadas, activo) values ('$nomb', '$seud', '$email', '$dir', '$coord', '$act')")) 
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));
		$ida = $temp->last_insert_id();
		
		//lo inserta como usuario
		if (!$temp->query("insert into tbl_admin (idrol, nombre, email, ididioma, md5) values ('$rol', '$nomb', '$email', '$idi', '$md5')")) 
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));
		$id = $temp->last_insert_id();
		
		$envCorr = 1;
		$encb = $fun->idioma('correo bienvenida al sitio');
		$subj = $fun->idioma('Inscripcion en el sitio ArteOrganizer',$idi2);
		
	} elseif ($acc == 'modifica') {// se actualiza el usuario
		if (!$temp->query("update tbl_artista set nombre = '$nomb', correo = '$email', activo = '$act', seudonimo = '$seud', coordenadas = '$coord', direccion = '$dir' where id = $ida")) 
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));
	}
	
	if (!$temp->query("insert into tbl_bitacora (idadmin, texto) values ({$_SESSION['id']}, 'Se inserta o modifica el artista $nomb, los datos son: nombre = $nomb, email = $email, activo = $act')")) 
		return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));

	//sube la imagen
	if (count($_FILES['file']) > 0) {
		$subir->_width = 215;
		$subir->_name = "profile.".ltrim($_FILES['file']['type'],'image/');
		$subir->_dest = "images/artista/$ida/";
		$subir->_size = 3500000;
		error_log("llega=".$subir->_dest.$subir->_name);
		$accion = $subir->init($_FILES['file']);
		if (strlen($accion) == 0) {
			$temp->query("delete from tbl_imagenes where id = (select idimg from tbl_artista where id = $ida)");
			$temp->query("insert into tbl_imagenes (tipo, direccion) values (2,'".$subir->_dest.$subir->_name."')");

			$temp->query("update tbl_artista set idimg = '".$temp->last_insert_id()."' where id = $ida");
		} else {
			error_log("accions=".$accion);
			$report = $fun->idioma("La imagen dio error - ").$fun->idioma($accion).$fun->idioma("Puede intentar subirla nuevamente modificando el artista");
		}
	}
error_log("report=".$report);
	echo json_encode(array("error" => "", "data" => $fun->idioma("Datos correctamente guardados").$report));

	if ($envCorr = 1) {
		$temp->query("select iso2 from tbl_idioma where id = $idi");
		$idi2 = $temp->f('iso2');
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
	error_log($datos);
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



$titag = $fun->idioma("Obra");
$anchTit = "320px";
$coord = 'ex. 23.090125, -82.32810';

$columnas = "";
$activo = 1;
$roldef = 4;

$html->inicio();
$html->classAncho = 'col-sm-12';
$html->inHide('obr', 'pag');

$html->inHide("a.id '".$fun->idioma('Editar')."{edit}', a.id '".$fun->idioma('Desactivar')."{borr}', a.nombre '".$fun->idioma('Artista')."', a.seudonimo '".$fun->idioma('Seudonimo')."', a.correo '".$fun->idioma('Correo')."{mail}', case a.activo when '1' then '".$fun->idioma('Si')."' else '".$fun->idioma('No')."' end '".$fun->idioma('Activo')."', a.fechamod '".$fun->idioma('Fecha Mod')."{fec}', case a.activo when '1' then 'black' else 'navy' end '{col}'", 'columnas');
$html->inHide('tbl_artista a', 'tablas');
$html->inHide('', 'buscar');
$html->inHide("'{$fun->idioma('Fecha Mod')}' desc", 'orden');
$html->inHide('1', 'numpag');
$html->inHide('', 'accion');
$html->inHide('', 'id');

if ($_SESSION['grupo_rol'] <= 5) {
	$temp->query("select id, nombre from tbl_artista order by nombre");
	$arrIni = $temp->loadRowList();
	array_unshift($arrIni, array("todos", $fun->idioma("Todos")));
	$html->inSelect($fun->idioma("Artistas"), "arti", 3, $arrIni, '');
} else {
	$html->inHide($_SESSION["idartista"], 'arti');
}


$temp->query("select idioma, iso2 from tbl_idioma order by idioma ");
$arrIdiom = $temp->loadAssocList();
for($i=0;$i<count($arrIdiom);$i++) {
	$html->inTextb($fun->idioma('Nombre')." - ".$arrIdiom[$i]['idioma'], $nombre, 'nomb'.$arrIdiom[$i]['iso2'], 'text', '', '', 'required autofocus',null,' estsd');
}
$html->inTextb($fun->idioma('Inventario'), $inv, 'inv', 'text', '', '', 'required autofocus');
$valoIni = array(1,8);
$html->inSelect($fun->idioma('Cantidad en la Serie'), 'idioma', 4, $valoIni, 1);
$valoIni = array(1960, date('Y'));
$html->inSelect($fun->idioma('Ano Realizacion'), 'ano', 4, $valoIni, date('Y'));

for($i=0;$i<count($arrIdiom);$i++) {
	$html->inTexarea($fun->idioma('Declaracion')." - ".$arrIdiom[$i]['idioma'], '', 'decl'.$arrIdiom[$i]['iso2'] ,10);
}
for($i=0;$i<count($arrIdiom);$i++) {
	$html->inTexarea($fun->idioma('Observaciones')." - ".$arrIdiom[$i]['idioma'], '', 'obser'.$arrIdiom[$i]['iso2'] ,10);
}


$html->inTextb($fun->idioma('Imagen de la obra'), '', 'imagen', 'file', '', '', ' autofocus');

$formulario = "<div id='form' class='hide'>" . $html->salida() . "</div>";


$js = $homepage = file_get_contents('pagina/js/obr.js');
$scriptInf = "
<script type='text/javascript'>
$js
</script> ";

?>