<?php

/* 
 * Pagina para el tratamiendo de los artistas en el sitio
 */
global $temp, $html, $tabla, $corCreo, $fun;

/**
 * Carga los datos del usuario para ponerlos en el formulario
 * @global object $ent
 * @global object $temp
 * @global object $fun
 * @param integer $id
 * @return string
 */
function este($id) {
	global $ent, $temp, $fun;
	
	if (!($id = $ent->isEntero($id, 3))) return json_encode (array("error"=>$fun->idioma("Error en identificador del usuario")));
	
	$temp->query("select nombre from tbl_estado where id = $id");
	$arrSal[] = $id;
	$arrSal[] = $temp->f('nombre');
	
	$arrIdiom = $fun->idiomas();
	for ($i=0; $i<count($arrIdiom);$i++){
		$arrSal[$arrIdiom[$i]] = $fun->idioma($arrSal[1],$arrIdiom[$i]);
	}
	
	return json_encode(array("error"=>'', "data"=>$arrSal, "idioma"=>$arrIdiom));
}

/**
 * Inserta el nombre del usuario
 * @global object $ent
 * @global object $temp
 * @param string $param
 * @return string
 */
function iepg($param) {
	global $ent, $temp, $fun, $subir;
	//$("#nombre").val() + + '|' + $('#id').val()

	$dir = $coord = $report = '';

	$arrDatos = explode("|", $_POST['datos']);
	error_log($_POST['datos']);
	
	for($i = 0; $i < count($arrDatos); $i++) {
		switch ($i) {
			case 0:
				if ($arrDatos[$i] > 0) {
					if (!($ida = $ent->isEntero($arrDatos[$i], 10))) {
						return json_encode(array("error" => $fun->idioma("Error en la entrada del id")));
					}
				}
				break;
			case 1:
				if (!($acc = $ent->isAlfabeto($arrDatos[$i], 10))) {
					return json_encode(array("error" => $fun->idioma("Error en la accion")));
				}
				break;
			default:
				if (!($nomb[] = $ent->isAlfabeto($arrDatos[$i], 100))) {
					return json_encode(array("error" => $fun->idioma("Error en la entrada del nombre")));
				}
				break;
		}
	}
	
	//busca el idioma
	$arrIdiom = $fun->idiomas();

	for ($i = 0; $i < count($arrIdiom); $i++) {//crea una array de idioma con los nombres
		$arrIdi[$arrIdiom[$i]] = $nomb[$i];
	}
	
	if ($acc == 'inserta') {// se va a insertar el estado
		error_log('entra inserta');
		
		//verifica que en la tabla de idiomas no está ya puesto
		$temp->query("select count(*) total from tbl_adminIdioma where frase = '{$arrIdi["es"]}'");
		if ($temp->f('total') == 0) {
			//si no se encuentra lo pongo
			foreach ($arrIdiom as $idioma) {
				if (!$temp->query("insert into tbl_adminIdioma (idioma, frase, texto) values ('$idioma', '".$fun->carcHange($arrIdi["es"])."', '{$arrIdi[$idioma]}')")) {
				return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
			}
		}
		
		//inserta el nuevo valor en la tabla correspondiente
		if (!$temp->query("insert into tbl_estado (nombre) values ('".$fun->carcHange($arrIdi["es"])."')")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
		
		
	} elseif ($acc == 'modifica') {// se actualiza el estado
		//obtengo el nombre que tenía antiguamente
		$temp->query("select nombre from tbl_estado where id = $ida");
		$nombAnt = $temp->f('nombre');
		
		if (!$temp->query("update tbl_estado set nombre = '".$fun->carcHange($arrIdi["es"])."' where id = $ida")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
			
		//borro todas las traducciones que existían anteriormente
		$temp->query("delete from tbl_adminIdioma where frase  = '$nombAnt'");
		
		//inserto las nuevas traducciones
		foreach ($arrIdiom as $idioma) {
			if (!$temp->query("insert into tbl_adminIdioma (idioma, frase, texto) values ('$idioma', '".$fun->carcHange($arrIdi["es"])."', '{$arrIdi[$idioma]}')")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
		}
	}
	
	if (!$temp->query("insert into tbl_bitacora (idadmin, texto) values ({$_SESSION['id']}, 'Se inserta o modifica el estado de la obra ".$fun->carcHange($arrIdi["es"]).", los datos son: ". html_entity_decode($_POST['datos'], ENT_QUOTES)."')")) {
		return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}

	return json_encode(array("error" => "", "data" => $fun->idioma("Datos correctamente guardados")));
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
	$tabla->trad = true;
//	$sale = $tabla->ejecQuery();
	error_log("trad arriba=".$tabla->trad);
	echo json_encode(array("error" => '', "data" => $tabla->tabla()));
}

//Evalua si existe la función que le pasan desde javascript de ser así la invoca
if (function_exists($d['fun'])) {
	echo call_user_func($d['fun'], $d['datos']);
	exit;
}

/************************************************************************************************************************************************/

$titag = $fun->idioma("Estado de la Obra");
$anchTit = "320px";

$columnas = "";

$html->inicio();
$html->classAncho = 'col-sm-12';
$html->inHide('eobr', 'pag');

$html->inHide("id '".$fun->idioma('Editar')."{edit}', nombre '".$fun->idioma('Estado')."'", 'columnas');
$html->inHide('tbl_estado', 'tablas');
$html->inHide('', 'buscar');
$html->inHide("'{$fun->idioma('Estado')}' asc", 'orden');

$html->inHide('1', 'numpag');
$html->inHide('', 'accion');
$html->inHide('', 'id');

$temp->query("select idioma, iso2 from tbl_idioma order by idioma ");
$arrIdiom = $temp->loadAssocList();
for($i=0;$i<count($arrIdiom);$i++) {
	$html->inTextb($fun->idioma('Estado')." - ".$arrIdiom[$i]['idioma'], $nombre, $arrIdiom[$i]['iso2'], 'text', '', '', 'required autofocus',null,' estsd');
}

$formulario = "<div id='form' class='hide'>" . $html->salida() . "</div>";


$js = $homepage = file_get_contents('pagina/js/eobr.js');
$scriptInf = "
<script type='text/javascript'>
$js
	function borraform(){";
		$temp->query("select iso2 from tbl_idioma order by idioma");
		$arrIdiom = $temp->loadResultArray();
		for($i = 0; $i<count($arrIdiom); $i++) {
			$scriptInf .= "$('#{$arrIdiom[$i]}').val('');";
		}
$scriptInf .= "}
</script> ";

?>