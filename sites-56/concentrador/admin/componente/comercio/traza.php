<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$temp = @ new ps_DB;
$html = new tablaHTML;

// print_r($_SESSION);

$d = $_REQUEST;

// echo json_encode($d);
// echo "<br>hola<br>";


$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_TRAZA;
$html->tituloTarea = _REPORTE_TASK;
$html->hide = false;
$html->anchoTabla = 500;
$html->anchoCeldaI = 243;
$html->anchoCeldaD = 250;

$html->inTextb("Titulo", $d['titulo'], 'titulo');
$html->inTextb('Buscar en la traza', $d['traza'], 'traza');
$html->inTextb("Fecha", $d['fecha'], 'fecha', null, null);

echo $html->salida();

$usrId = str_replace("'", "", $usrId);
$vista = "select id, from_unixtime(fecha, '%d/%m/%Y %H:%i:%s') fech, titulo, concat(substring(traza,1,200),'..') traza";
$from = " from tbl_traza";
// echo "<br>";
// print_r($d);
// echo "<br>isset=".$d['titulo']."<br>";strlen($d['titulo']);
if ( strlen($d['titulo']) > 3 || strlen($d['traza']) > 3 || strlen($d['fecha']) > 3 ) {
	$where = " where ";
	if (strlen($d['titulo']) > 3) {
		$where .= "titulo like '%".$d['titulo']."%'";
	}
	if (strlen($d['traza']) > 3) {
		if (strlen($where) > 7 ) {
			$where .= " and ";
		}
		$where .= "traza like '%".$d['traza']."%'";
	}
	if (strlen($d['fecha']) > 3) {
		if (strlen($where) > 7 ) {
			$where .= " and ";
		}
		$where .= "from_unixtime(fecha, '%d/%m/%Y') = '".$d['fecha']."'";
	}
}

$orden = ' id desc';

$colEsp = array();
$colEsp[] = array("a", 'Ver la traza', "css_cambia", "Cambia");

$busqueda = array();
$columnas[] = array(_REPORTE_FECHA, "fech", "", "center", "center" );
$columnas[] = array("Titulo", "titulo", "150", "center", "center" );
$columnas[] = array("Traza", "traza", "", "center", "left" );


$ancho = 1300;

// echo $vista.$from.$where.' order by '.$orden;
if ( strlen($d['titulo']) > 3 || strlen($d['traza']) > 3 || strlen($d['fecha']) > 3 ) {
	$querys = tabla( $ancho, 'E', $vista.$from, $orden, $where, $colEsp, $busqueda, $columnas );
}

if (strlen($_REQUEST["orden"]) > 0) $orden = $_REQUEST["orden"];
else $orden = $orden;
$_SESSION['columnas'] = $columnas;

$querys1 = str_replace(' limit 0, 30', '', stripslashes($querys));
$querys1 = str_replace(' end estCom', ' end estCom, servicio', $querys1);
?>
<style>
	#muestratraza{z-index: 6000;position:absolute;top:20px;left:30px;width:95vw;height:85vh;display:none;overflow:scroll;background-color:white;border:3px black solid;}
	#salir {position: fixed;width: 15px;font-size: 14px;float:right;top: 30px;right: 70px; cursor:pointer;	}
	#ttitle {font-size: 20px;padding: 2px;}
	#ttraza{font-size: 12px;margin-left: 10px;}
	#ffecha{font-size: 16px;}
	.datos{width: 100%;text-align: center;font-weight: bold;margin: 15px;}
</style>
<div id='muestratraza' >
	<div id='salir' onclick='cierra()'>X</div>
	<div id='ttitle' class='datos'></div>
	<div id='ffecha' class='datos'></div>
	<div id='ttraza' ></div>
</div> 

<script>

	function trazaView(id) {
		$.post('componente/comercio/ejec.php',{
			fun:	'vertraza',
			id:	id
		},function(data){
			data = data.replace("<script language='text/javascript'>window.open('index.php?componente=core&pag=logout', '_self')", '');
			data = data.replace('<\/script>', '');
			
			var vdatos = eval('(' + data + ')');
			$("#muestratraza").show();

				// alert(datos.sale.fecha);
			if (vdatos.sale.fecha && vdatos.sale.fecha.length > 0) {
			// 	alert(datos.sale.fecha)
				var fecha = vdatos.sale.fecha;
				var titulo = vdatos.sale.titulo;
				var traza = vdatos.sale.traza;
				$("#ttitle").html(titulo);
				$("#ttraza").html(traza);
				$("#ffecha").html(fecha);
			}
			
		});
	}
	function verifica(){return true;}
	function cierra(){
		$("#muestratraza").hide();
	}
</script>