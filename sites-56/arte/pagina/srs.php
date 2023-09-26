<?php

/* 
 * Pagina para el tratamiendo de las series de las obras en el sitio
 */
global $temp, $html, $tabla, $corCreo, $fun;

/**
 * Carga los datos de la serie para ponerlos en el formulario y editar
 * @global object $ent
 * @global object $temp
 * @global object $fun
 * @param integer $id
 * @return string
 */
function este($id) {
	global $ent, $temp, $fun;
	
	if (!($id = $ent->isEntero($id, 5))) return json_encode (array("error"=>$fun->idioma("Error en identificador del usuario")));
	
	$temp->query("select idartista, idtexto, ano from tbl_series where id = $id");
	$arrSal[] = $temp->f('idtexto');
	$arrSal[] = $temp->f('ano');
	$arrSal[] = $temp->f('idartista');
	$arrSal[] = $id;
	
	$arrIdiom = $fun->idiomas();
	for ($i=0; $i<count($arrIdiom);$i++){
		$temp->query("select texto from tbl_textos where idioma = '{$arrIdiom[$i]}' and idtexto = '{$arrSal[0]}' and idartista = '{$arrSal[2]}'");
		$arrSal[$arrIdiom[$i]] = $temp->loadResultArray();
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
			case 2:
				if (strlen($arrDatos[$i]) > 0 && $arrDatos[$i] != 'null'){
					if (!($ano = $ent->isEntero($arrDatos[$i], 14))) {
						return json_encode(array("error" => $fun->idioma("Error en el ano")));
					}
				}
				break;
			case 3:
				if (!($nomb = $ent->isLetraNumero($arrDatos[$i], 100))) {
					return json_encode(array("error" => $fun->idioma("Error en la entrada del nombre")));
				}
				break;
			case 4:
				if (!($declaen = $ent->isLetraNumero($arrDatos[$i]))) {
					return json_encode(array("error" => $fun->idioma("Error en el statemen en ingles")));
				}
				break;
			case 5:
				if (!($declaes = $ent->isLetraNumero($arrDatos[$i]))) {
					return json_encode(array("error" => $fun->idioma("Error en el statemen en espanol")));
				}
				break;
		}
	}
	
	//busca el idioma
	$arrIdiom = $fun->idiomas();

	for ($i = 0; $i < count($arrIdiom); $i++) {//crea una array de idioma con los nombres
		$arrIdi[$arrIdiom[$i]] = $nomb[$i];
	}
	
	if ($acc == 'inserta') {// se va a insertar la serie
		error_log('entra inserta');
		//genero el identificador que va a tener el texto
		$sale = 1;
		while ($sale != 0) {
			$idtexto = $fun->suggestPassword(10,false);
			$temp->query("select id from tbl_textos where idtexto = '$idtexto'");
			$sale = $temp->num_rows();
		}
		
		//inserta el mismo nombre de la serie para los dos idiomas
		foreach ($arrIdiom as $idioma) {
			if (!$temp->query("insert into tbl_textos (idioma, idtexto, idartista, texto, idtipotexto) values ('$idioma', '$idtexto', '$ida', '$nomb', '7')")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
		}
		
		//inserta el statement de la serie para cada idioma
		$temp->query("insert into tbl_textos (idioma, idtexto, idartista, texto, idtipotexto) values ('en', '$idtexto', '$ida', '$declaen', '8')");
		$temp->query("insert into tbl_textos (idioma, idtexto, idartista, texto, idtipotexto) values ('es', '$idtexto', '$ida', '$declaes', '8')");
		
		//inserta el nuevo valor en la tabla correspondiente
		if (!$temp->query("insert into tbl_series (idartista, idtexto, ano) values ('$ida', '$idtexto', '$ano')")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
		
		
	} elseif ($acc == 'modifica') {// se actualiza la serie
		
		if (!$temp->query("update tbl_series set ano = '$ano', idartista = $ida where idtexto = '".$arrDatos[6]."'")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
			
		//borro todas las traducciones que existían anteriormente
		//$temp->query("delete from tbl_textos where idtexto  = '$ida' and idartista = '".$_SESSION['idartista']."' and idtipotexto = 7");
		
		//inserta el nombre de la serie 
		foreach ($arrIdiom as $idioma) {
			$temp->query("update tbl_textos set idartista = '$ida', texto = '$nomb' where idioma = '$idioma' and  idtexto = '".$arrDatos[6]."' and idtipotexto = '7'");
		}
		
		//inserto los statement de la serie para cada idioma
		foreach ($arrIdiom as $idioma) {
			$nomb = '$'.'decla'.$idioma;
			error_log('\n'.eval('echo $nomb;').'\n');exit;
			error_log("\n".eval($nomb));
			if (!$temp->query("update tbl_textos set idartista = '$ida', texto = '$idioma' where idioma = '$idioma' and  idtexto = '".$arrDatos[6]."' and idtipotexto = '8'")) {
			return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
		}


		// if (!$temp->query("update tbl_textos set idartista = '$ida', texto = '$declaes' where idioma = 'es' and  idtexto = '".$arrDatos[6]."' and idtipotexto = '8'")) {
		// return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
		// if (!$temp->query("update tbl_textos set idartista = '$ida', texto = '$declaen' where idioma = 'en' and  idtexto = '".$arrDatos[6]."' and idtipotexto = '8'")) {
		// return json_encode(array("data" => '', "error" => "Error: {$temp->getErrorMsg()}"));}
	}
	
	if (!$temp->query("insert into tbl_bitacora (idadmin, texto) values ({$_SESSION['id']}, 'Se inserta o modifica la serie ".$arrIdi['es'].", los datos son: ".htmlentities($_POST['datos'], ENT_QUOTES)."')")) {
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

$titag = $fun->idioma("Series");
$anchTit = "320px";

$columnas = "";

$html->inicio();
$html->classAncho = 'col-sm-12';
$html->inHide('srs', 'pag');

$busc = "s.idtexto = t.idtexto and t.idioma = '".$_SESSION['idioma']."' and t.idtipotexto = 7";
if ($_SESSION['grupo_rol'] > 5) {
	$busc .= " and t.idartista = '{$_SESSION["idartista"]}'";
	$html->inHide("s.id '".$fun->idioma('Editar')."{edit}', t.texto '".$fun->idioma('Series')."', s.ano '".$fun->idioma('Ano')."'", 'columnas');
} else {
	$html->inHide("s.id '".$fun->idioma('Editar')."{edit}', (select nombre from tbl_artista where id = s.idartista) '". $fun->idioma('Artista') ."', t.texto '".$fun->idioma('Series')."', s.ano '".$fun->idioma('Ano')."'", 'columnas');
}

$html->inHide('tbl_series s, tbl_textos t', 'tablas');
$html->inHide($busc, 'buscar');
$html->inHide("'{$fun->idioma('Ano')}' asc", 'orden');

$html->inHide('1', 'numpag');
$html->inHide('', 'accion');
$html->inHide('', 'id');
$html->inHide('', 'idser');

if ($_SESSION['grupo_rol'] <= 5) {
	$temp->query("select id, nombre from tbl_artista order by nombre");
	$arrIni = $temp->loadRowList();
//	array_unshift($arrIni, array("", $fun->idioma("Seleccione")));
	$html->inSelect($fun->idioma("Artistas"), "arti", 3, $arrIni, '');
} else {
	$html->inHide($_SESSION["idartista"], 'arti');
}

$temp->query("select i.idioma, i.iso2 from tbl_idioma i where i.idioma in ('".str_replace(",", "','", $_SESSION['idioTrab'])."') order by i.idioma ");
$arrIdiom = $temp->loadAssocList();
for($i=0;$i<count($arrIdiom);$i++) {
	$html->inTextb($fun->idioma('Nombre')." - ".$arrIdiom[$i]['idioma'], $nombre, $arrIdiom[$i]['iso2'], 'text', '', '', 'required autofocus',null,' estsd');
}
$temp->query("select idioma, iso2 from tbl_idioma order by idioma ");
$arrIdiom = $temp->loadAssocList();

$html->inTextb($fun->idioma('Nombre de la Serie'), '', 'serieNombre', 'text', '', '', 'autofocus');
$arrAnos = array(2022,1950);
$html->inSelect($fun->idioma('Año de realizada'), 'ano', 4, $arrAnos, date("YYYY"));

for($i=0;$i<count($arrIdiom);$i++) {
	$html->inTexarea($fun->idioma('Declaracion')." - ".$arrIdiom[$i]['idioma'], '', 'decl'.$arrIdiom[$i]['iso2'] ,10);
}

$formulario = "<div id='form' class='hide'>" . $html->salida() . "</div>";


$js = $homepage = file_get_contents('pagina/js/srs.js');
$scriptInf = "
<script type='text/javascript'>
$js
	function borraform(){";
		$temp->query("select iso2 from tbl_idioma order by idioma");
		$arrIdiom = $temp->loadResultArray();
		$scriptInf .= "$('#ano').val('');";
		for($i = 0; $i<count($arrIdiom); $i++) {
			$scriptInf .= "$('#{$arrIdiom[$i]}').val('');";
		}
$scriptInf .= "}
</script> ";

?>