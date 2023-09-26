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
	//$("#nombre").val() + '|' + 0
	//$("#email").val() + '|' + 1
	//$("#seudo").val() + '|' + 2
	//$('#direcc').val() + '|' + 3
	//$("input[name='activo']:checked").val() + '|' + 4
	//$('#id').val()  + '|' + 5
	//coord + '|' + 6
	//$('#idiomaT').val() + '|' + 7
	//$('#accion').val() + '|' + 8
	//$('#idioma').val() 9

	$dir = $coord = $report = '';

	$arrDatos = explode("|", $_POST['datos']);
	foreach ($arrDatos as $value) {
		error_log($value);
	}

	if (!($nomb = $ent->isAlfabeto($arrDatos[0], 100))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada del nombre")));
	}
	
	if (is_array($arrDatos[7])) {
		$idiw = explode(',', $arrDatos[7]);
	} elseif ($arrDatos[7] > 0) {
		$idiw = $arrDatos[7];
	} else {return json_encode(array("error" => $fun->idioma("Error en la entrada del idioma de trabajo")));}
	if (!($idi = $ent->isEntero($arrDatos[9], 2))) {
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
	$rol = 3;
	
	

	if ($acc == 'inserta') {// se va a insertar el artista
		//verifica que el correo no se repita
		$temp->query("select count(*) tot from tbl_artista where correo = '$email'");
		//revisa si está en la tabla de los artistas
		//si no está revisaría en la de los administradores
		if ($temp->f('tot') == 0) {
			$temp->query("select count(*) tot from tbl_admin where email = '$email'");
			$md5 = $fun->suggestPassword(8);
			if ($temp->f('tot') > 0) {return json_encode(array("error" => $fun->idioma("Error: Esta direccion de correo ya existe en la base de datos")));}
		} else {return json_encode(array("error" => $fun->idioma("Error: Esta direccion de correo ya existe en la base de datos")));}
		
		//determina el password inicial
		$md5 = $fun->suggestPassword(8);
		error_log($md5);
		
		//lo inserta como artista
		if ($temp->query("call insertaArtista ('$nomb', '$seud', '$email', '$dir', '$coord', '$md5', '$act', '$idi', '$idiw', @idArt)"))  {
			$temp->query("select @idArt idArt");
			$ida = $temp->f('idArt');
		} else
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));
		
		$encb = $fun->idioma('correo bienvenida al sitio');
		$subj = $fun->idioma('Inscripcion en el sitio ArteOrganizer','es');

		$temp->query("select iso2 from tbl_idioma i, tbl_admin a where i.id = a.ididioma and a.id = $ida");
		$idi2 = $temp->f('iso2');
		error_log("{encb}=$encb");
		$cont = str_replace('{encb}', $encb, str_replace('{nomb}', $nomb, str_replace('{md5}', $md5, file_get_contents("pagina/correo_inscripcion_$idi2.html"))));
		$arrImg = array("images/logo.png", "images/icon-tweet.png", "images/icon-facebook.png");
		$nohtml = $fun->idioma('nohtml',$idi2);
		error_log($fun->smtpmail(array($email,$nomb), $subj, $cont, null, null, $arrImg, $nohtml));
	
		
	} elseif ($acc == 'modifica') {// se actualiza el artista
		if (!$temp->query("update tbl_artista set nombre = '$nomb', correo = '$email', activo = '$act', seudonimo = '$seud', coordenadas = '$coord', direccion = '$dir' where id = $ida")) 
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));
	}
	
	//pone los idiomas de trabajo
//	$temp->query("select iso2 from tbl_idioma where id in ($idi)");
//	$idi2 = $temp->loadResultArray();
	if ($ida > 0) {
		$temp->query("delete from tbl_colArtistaIdioma where idartista = $ida");
		for ($i = 0; $i < count($idi); $i++) {
			$temp->query("insert into tbl_colArtistaIdioma (idartista, ididioma) values ('$ida',  '$idi') ");
		}
	}
	
	if (!$temp->query("insert into tbl_bitacora (idadmin, texto) values ({$_SESSION['id']}, 'Se inserta o modifica el artista $nomb, los datos son: ". html_entity_decode($_POST['datos'], ENT_QUOTES)."')")) 
		return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));

	//sube la imagen
	if (count($_FILES['file']) > 0) {
//		var_dump($_FILES);
		$subir->_width = 215;
		$subir->_name = "profile.".ltrim($_FILES['file']['type'],'image/');
		$subir->_dest = "images/artista/$ida/";
		$subir->_size = 3500000;
		error_log($_FILES['file']['size'].' > '.$subir->_size);
		if ($_FILES['file']['size'] > $subir->_size) {return json_encode(array("error" => $fun->idioma("Error: La imagen supera el tamano maximo que es de").$subir->_size.'KB', "data" => ''));}
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

	return ;
}

/*********************************************************************************************************************************/

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



$titag = $fun->idioma("Artistas");
$anchTit = "320px";
$coord = 'ex. 23.090125, -82.32810';

$columnas = "";
$activo = 1;
$roldef = 4;

$html->inicio();
$html->classAncho = 'col-sm-12';
$html->inHide('art', 'pag');

$html->inHide("a.id '".$fun->idioma('Editar')."{edit}', a.id '".$fun->idioma('Desactivar')."{borr}', a.nombre '".$fun->idioma('Artista')."', a.seudonimo '".$fun->idioma('Seudonimo')."', a.correo '".$fun->idioma('Correo')."{mail}', case a.activo when '1' then '".$fun->idioma('Si')."' else '".$fun->idioma('No')."' end '".$fun->idioma('Activo')."', a.fechamod '".$fun->idioma('Fecha Mod')."{fec}', case a.activo when '1' then 'black' else 'navy' end '{col}'", 'columnas');
$html->inHide('tbl_artista a', 'tablas');
$html->inHide('', 'buscar');
$html->inHide("'{$fun->idioma('Fecha Mod')}' desc", 'orden');
$html->inHide('1', 'numpag');
$html->inHide('', 'accion');
$html->inHide('', 'id');
$html->inTextb($fun->idioma('Nombre'), $nombre, 'nombre', '', '', '', 'required autofocus');
$html->inTextb($fun->idioma('Correo'), $correo, 'email', 'email', '', '', 'required autofocus');
$html->inTextb($fun->idioma('Seudonimo'), $seudo, 'seudo', '', '', '', ' autofocus');
$html->inTextb($fun->idioma('Direccion'), $direcc, 'direcc', '', '', '', ' autofocus');
$html->inTextb($fun->idioma('Coordenadas'), $coord, 'coord', '', '', '', ' autofocus');

$temp->query("select id, idioma nombre from tbl_idioma order by nombre");
$valoIdi = $temp->loadRowList();
$html->inSelect($fun->idioma('Idioma personal'), 'idioma', 3, $valoIdi, 1);
$html->inSelect($fun->idioma('Idiomas de trabajo'), 'idiomaT', 3, $valoIdi, 1, null, null, 'multiple');

$valoIni = array(1, 0);
$etiq = array($fun->idioma('Si'), $fun->idioma('No'));
$html->inRadio($fun->idioma('Activo').'?', $valoIni, 'activo', $etiq, $activo);

$html->inTextb($fun->idioma('Imagen del artista'), '', 'imagen', 'file', '', '', ' autofocus');

$formulario = "<div id='form' class='hide'>" . $html->salida() . "</div>";


$js = $homepage = file_get_contents('pagina/js/art.js');
$scriptInf = "
<script type='text/javascript'>
$js
</script> ";

?>