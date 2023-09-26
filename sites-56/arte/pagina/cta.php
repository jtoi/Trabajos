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
function eepg($id) {
	global $ent, $temp, $fun;
	
	if (!($id = $ent->isEntero($id, 3))) {return json_encode (array("error"=>$fun->idioma("Error en identificador del usuario")));}
	
	$temp->query("select idartista, idmoneda, texto, nombre, montoInicial from tbl_cuentaBanc where id = $id");
	$arrSal[] = $id;
	$arrSal[] = $temp->f('idartista');
	$arrSal[] = $temp->f('idmoneda');
	$arrSal[] = html_entity_decode($temp->f('texto'), ENT_COMPAT, 'UTF-8');
	$arrSal[] = $temp->f('nombre');
	$arrSal[] = $temp->f('montoInicial');
	
	
	return json_encode(array("error"=>'', "data"=>$arrSal));
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
	
	
	if (strlen($arrDatos[0])) {
		if (!($ida = $ent->isEntero($arrDatos[0], 10))) {
			return json_encode(array("error" => $fun->idioma("Error en la entrada del id")));
		}
	}
	
	if (!($acc = $ent->isAlfabeto($arrDatos[1], 10))) {
		return json_encode(array("error" => $fun->idioma("Error en la accion")));
	}
	
	if (!($nom = $ent->isLetraNumero($arrDatos[2], 100))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada del nombre")));
	}
	
	if (!($mon = $ent->isEntero($arrDatos[3], 3))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada de la moneda")));
	}
	
	if (!($text = $ent->isUrl(htmlentities($arrDatos[4], ENT_QUOTES, 'UTF-8'), 500))) {
		return json_encode(array("error" => $fun->idioma("Error en la entrada del texto")));
	}
	
	if ($arrDatos[5] != 0) {
		if (!($mot = $ent->isNumero($arrDatos[5], 9))) {
			return json_encode(array("error" => $fun->idioma("Error en la entrada del monto inicial")));
		}
	} else $mot = 0;
	
	if ($acc == 'inserta') {// se va a insertar el estado
//		error_log('entra inserta');
		
		//inserta el nuevo valor en la tabla correspondiente
		if (!$temp->query("insert into tbl_cuentaBanc (idartista, idmoneda, texto, nombre, montoInicial) values ('".$_SESSION['idartista']."', '$mon', '".$text."', '$nom', '$mot')")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
		
		
	} elseif ($acc == 'modifica') {// se actualiza el estado
		
		if (!$temp->query("update tbl_cuentaBanc set idmoneda = '$mon', texto = '".$text."', nombre = '$nom', montoInicial = '$mot' where id = $ida")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
		
	}
	
	if (!$temp->query("insert into tbl_bitacora (idadmin, texto) values ({$_SESSION['id']}, 'Se inserta o modifica la cuenta bancaria $nom, los datos son: ". html_entity_decode($_POST['datos'], ENT_QUOTES)."')")) {
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
//	error_log($datos);
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
//	error_log("trad arriba=".$tabla->trad);
	echo json_encode(array("error" => '', "data" => $tabla->tabla()));
}

//Evalua si existe la función que le pasan desde javascript de ser así la invoca
if (function_exists($d['fun'])) {
	echo call_user_func($d['fun'], $d['datos']);
	exit;
}

/************************************************************************************************************************************************/

$titag = $fun->idioma("Cuenta Bancaria");
$anchTit = "320px";

$columnas = "";

$html->inicio();
$html->classAncho = 'col-sm-12';
$html->inHide('cta', 'pag');

$html->inHide("c.id '".$fun->idioma('Editar')."{edit}'¬c.nombre '".$fun->idioma('Cuenta Bancaria')."'¬denominacion '".$fun->idioma('Moneda')."'¬(select ifnull(sum(facturado),0) from tbl_facturacion where idcuenta = c.id and idestado in (2,4)) '".$fun->idioma('Facturado')."{diner}'¬c.montoInicial '".$fun->idioma('Monto Inicial')."{diner}'¬(select ifnull(sum(pagado),0) from tbl_facturacion where idcuenta = c.id and idestado in (3,4)) '".$fun->idioma('Vendido')."{diner}'", 'columnas');
$html->inHide('tbl_cuentaBanc c, tbl_moneda m', 'tablas');
$html->inHide('c.idmoneda = m.id and c.idartista = '.$_SESSION['idartista'], 'buscar');
$html->inHide("'{$fun->idioma('Moneda')}' asc", 'orden');

$html->inHide('1', 'numpag');
$html->inHide('', 'accion');
$html->inHide('', 'id');


$html->inTextb($fun->idioma('Nombre'), $nombre, 'nombre', '', '', '', 'required autofocus');
$html->inTextb($fun->idioma('Monto Inicial'), 0, 'monto', '', '', '', 'required autofocus');

$arrIni = "select id, denominacion nombre from tbl_moneda order by nombre";
$html->inSelect($fun->idioma("Moneda"), "moneda", 2, $arrIni, "840");

//$temp->query("select idioma, iso2 from tbl_idioma order by idioma ");
//$arrIdiom = $temp->loadAssocList();
//for($i=0;$i<count($arrIdiom);$i++) {
//	$html->inTexarea($fun->idioma('Moneda')." - ".$arrIdiom[$i]['idioma'], '', $arrIdiom[$i]['iso2'],10);
//}

$html->inTexarea($fun->idioma('Datos para transferencias de dinero'), '', 'texto' ,10);

$formulario = "<div id='form' class='hide'>" . $html->salida() . "</div>";


$js = $homepage = file_get_contents('pagina/js/cta.js');
$scriptInf = "
<script type='text/javascript'>
$js
	function borraform(){";
		$temp->query("select iso2 from tbl_idioma order by idioma");
		$arrIdiom = $temp->loadResultArray();
		for($i = 0; $i<count($arrIdiom); $i++) {
			$scriptInf .= "$('#{$arrIdiom[$i]}').val('');";
		}
		$scriptInf .= "$('#iso').val('');$('#codigo').val('');";
$scriptInf .= "}
</script> ";

?>