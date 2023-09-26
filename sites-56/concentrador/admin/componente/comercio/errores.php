<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
global $temp;
global $send_m;
$html = new tablaHTML;
$d = $_REQUEST;
$whe = '';

// var_dump($d);

if (_MOS_CONFIG_DEBUG) var_dump($d);

if (isset($d['fec']) && ($d['fec'] != '%' && $d['fec'] != '')) {
	$fec = " and from_unixtime(fecha,'%d/%m/%Y') like '%".$d['fec']."%'";
} //else $fec = '%';

if ($d['comercio']) {
	if (strlen($d['comercio']) < 20) {
		$temp->query("select nombre from tbl_comercio where id = ".$d['comercio']);
		$whe .= " and traza like '%".$temp->f('nombre')."%'";
	}
}

if (isset($d['idtr']) && ($d['idtr'] != '%' && $d['idtr'] != '')) {
	$whe .= " and traza like '%".$d['idtr']."%'";
}

/*
	* Construye el formulario de Buscar
	*/
$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
				function verifica() {
					return true;
				}
				</script>";

$html->idio = $_SESSION['idioma'];
$html->tituloPag = "Errores";
$html->tituloTarea = _REPORTE_TASK;
$html->hide = true;
$html->anchoTabla = 500;
$html->anchoCeldaI = 120;
$html->anchoCeldaD = 345;

$query = "select id, nombre from tbl_comercio where activo = 'S' order by nombre";
$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query, $comercId, null, null);
$html->inTextb('ID. Transaccion', '%', 'idtr');
$html->classCss = 'formul fecc hasDatepicker';
$html->inTextb('Fecha', $fechaCierre, 'fec', null, null, null, 'Fecha en formato (dd/mm/yyyy)');
$html->classCss = 'formul';

echo $html->salida();
/*
	* Termina el formulario de buscar
	*/

 if (!$d['comb']) $d['comb'] = $_SESSION['idcomStr'];
$vista = "select id, formateaF(fecha,10) fec, traza  from tbl_traza ";
$where = " where titulo like '%de errores%' $fec".$whe ;
$from = "";
$orden = ' fecha desc';

$busqueda = array();

$columnas = array(
		array("Fecha", "fec", "120", "center", "left" ),
		array("Error", "traza", "", "center", "left" ));

$ancho = 1100;

echo "<div style='float:left; width:100%' ><table class='total1' width=\"$ancho\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
<tr>
<td><span style='float:right' class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='"._REPORTE_CSV."' title='"._REPORTE_CSV."'></span></td>
		</tr>
	</table></div>";

// echo $vista.$where." order by ".$orden;
// sendTelegram($vista.$where." order by ".$orden);
tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );


?>

<script type="text/javascript">
// $(document).ready(function(){
	$("[id^=fec]").val('<?php echo $fec;?>');
	// $(function(){
	// 	$( ".fecc" ).datepicker({dateFormat: "dd/mm/yy"});
	// });

// });

	$("#comb").change(function(){
		$("#buca").val('1');
		$('form:first').submit();
	});

	// $("[id^=fec]").click(function(){if ($(this).val() == 'dd/mm/yyyy') $(this).val('');});
	// $("[id^=fec]").blur(function(){if ($(this).val() == '') $(this).val('dd/mm/yyyy');});

$("#usus").change(function() {
	$.post('componente/core/ejec.php',{
		fun: 'econ',
		usr: $("#usus :selected").val()
	},function(data){
		var datos = eval('(' + data + ')');
		if (datos.cont.length > 0) {
			$("#nomb").val(datos.cont[0]);
			$("#email").val(datos.cont[1]);
		}
	});
});

function verifica(){
	if ($("#nomb").val().lenght > 0) {
		
	}
}
</script>