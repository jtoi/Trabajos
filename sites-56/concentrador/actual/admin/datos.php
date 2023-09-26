<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//efectúa diferentes queries contra la base de datos similar a enviadata
$temp = new ps_DB;
$html = new tablaHTML;
// $corCreo = new correo();
global $send_m;

$d = $_REQUEST;
$fecha1 = date('d/m/Y', mktime(0, 0, 0, date("m"), 1, date("Y")));
$fecha2 = date('d/m/Y', mktime(0, 0, 0, date('m')+1, 1-1, date('Y')));
if ($d['fecha1']) $fecha1 = $d['fecha1'];
if ($d['fecha2']) $fecha2 = $d['fecha2'];
?>

<script type="text/javascript" src="../js/jquery.myfun_c00cc3a629e84ef270c28b0d705a1ce1.js"></script>
<script type="text/javascript">

function ejecutaD(){
	$("#divFormHid").css("display", "none");
	$("#onhideText span").get(0).innerHTML = "Mostrar";
	$("#onhideText").removeClass("onhideTextShow");
	$("#onhideText").addClass("onshowTextShow");
    $(".alerti").esperaDiv('muestra');
	var dpvm = dommon = estim = estimBR = datos = estado = moneda = tpv = tpvm = banc = pbanc = pcom = ptpv = bancm = tpvc = comerc = common = bancos = comseg = comercios = acept = ecept = 0;
	if ($('#estimado').attr('checked') == 'checked') estim = 1;
	if ($('#estimadoBR').attr('checked') == 'checked') estimBR = 1;
	if ($('#datos').attr('checked') == 'checked') datos = 1;
	if ($('#bancos').attr('checked') == 'checked') bancos = 1;
	if ($('#estado').attr('checked') == 'checked') estado = 1;
	if ($('#moneda').attr('checked') == 'checked') moneda = 1;
	if ($('#tpv').attr('checked') == 'checked') tpv = 1;
	if ($('#tpvm').attr('checked') == 'checked') tpvm = 1;
	if ($('#dpvm').attr('checked') == 'checked') dpvm = 1;
	if ($('#banc').attr('checked') == 'checked') banc = 1;
	if ($('#pbanc').attr('checked') == 'checked') pbanc = 1;
	if ($('#pcom').attr('checked') == 'checked') pcom = 1;
	if ($('#ptpv').attr('checked') == 'checked') ptpv = 1;
	if ($('#bancm').attr('checked') == 'checked') bancm = 1;
	if ($('#tpvc').attr('checked') == 'checked') tpvc = 1;
	if ($('#comerc').attr('checked') == 'checked') comerc = 1;
	if ($('#common').attr('checked') == 'checked') common = 1;
	if ($('#dommon').attr('checked') == 'checked') dommon = 1;
	if ($('#comseg').attr('checked') == 'checked') comseg = 1;
	if ($('#comercios').attr('checked') == 'checked') comercios = 1;
	if ($('#acept').attr('checked') == 'checked') acept = 1;
	if ($('#ecept').attr('checked') == 'checked') ecept = 1;
	$.ajax({
		type: 'POST',
		url: 'componente/comercio/ejec.php',
		dataType: 'text',
		contentType: 'application/x-www-form-urlencoded; charset=iso-8859-1',
		data: ({
			fun:'datos',
			estim:estim,
			estimBR:estimBR,
			datos:datos,
			estado:estado,
			moneda:moneda,
			bancos:bancos,
			comercios:comercios,
			tpv:tpv,
			dpvm:dpvm,
			tpvm:tpvm,
			banc:banc,
			pbanc:pbanc,
			pcom:pcom,
			ptpv:ptpv,
			bancm:bancm,
			tpvc:tpvc,
			comerc:comerc,
			common:common,
			dommon:dommon,
			comseg:comseg,
			acept:acept,
			ecept:ecept,
			fecha1:$("#fecha1").val(),
			fecha2:$("#fecha2").val(),
		}),
		success: function (data) {
			var datos = eval('(' + data + ')');
			$('.alerti').esperaDiv('cierra');
			if ($("#compa").attr("checked") == 'checked') {
				$("#muestra2").html('');
				$("#muestra2").html(datos.salida);
				$("#mue").css("width",($("#muestra1").css("width").replace('px','')*1+$("#muestra2").css("width").replace('px','')*1)+20);
				$("#muestra1").css("margin-right","19");
			} else {
				$("#muestra1").html('');
				$("#muestra2").html('');
				$("#muestra1").html(datos.salida);
				$("#mue").css("width",$("#muestra1").css("width").replace('px',''));
				$("#compa").removeAttr("disabled");
			}
		}
	});
}

$(document).ready(function(){
	$("#compa").click(function(){
		if ($("#compa").attr('checked') == 'checked'){
			$('#estimado').attr('disabled', 'disabled');
			$('#estimadoBR').attr('disabled', 'disabled');
			$('#datos').attr('disabled', 'disabled');
			$('#estado').attr('disabled', 'disabled');
			$('#moneda').attr('disabled', 'disabled');
			$('#tpv').attr('disabled', 'disabled');
			$('#tpvm').attr('disabled', 'disabled');
			$('#dpvm').attr('disabled', 'disabled');
			$('#banc').attr('disabled', 'disabled');
			$('#pcom').attr('disabled', 'disabled');
			$('#pbanc').attr('disabled', 'disabled');
			$('#ptpv').attr('disabled', 'disabled');
			$('#bancm').attr('disabled', 'disabled');
			$('#tpvc').attr('disabled', 'disabled');
			$('#comerc').attr('disabled', 'disabled');
			$('#common').attr('disabled', 'disabled');
			$('#dommon').attr('disabled', 'disabled');
			$('#comseg').attr('disabled', 'disabled');
			$('#bancos').attr('disabled', 'disabled');
			$('#comercios').attr('disabled', 'disabled');
			$('#acept').attr('disabled', 'disabled');
		} else {
			$('#estimadoBR').removeAttr('disabled');
			$('#estimado').removeAttr('disabled');
			$('#datos').removeAttr('disabled');
			$('#estado').removeAttr('disabled');
			$('#moneda').removeAttr('disabled');
			$('#tpv').removeAttr('disabled');
			$('#tpvm').removeAttr('disabled');
			$('#dpvm').removeAttr('disabled');
			$('#banc').removeAttr('disabled');
			$('#pbanc').removeAttr('disabled');
			$('#pcom').removeAttr('disabled');
			$('#ptpv').removeAttr('disabled');
			$('#bancm').removeAttr('disabled');
			$('#tpvc').removeAttr('disabled');
			$('#comerc').removeAttr('disabled');
			$('#common').removeAttr('disabled');
			$('#dommon').removeAttr('disabled');
			$('#comseg').removeAttr('disabled');
			$('#bancos').removeAttr('disabled');
			$('#comercios').removeAttr('disabled');
			$('#acept').removeAttr('disabled');
		}
	});

	$("#enviaForm").click(function(){
		ejecutaD();
		setInterval('ejecutaD()', 1000*60*15); // actualiza los datos de las tablas cada 15 min
	});
});

</script>
<style>
		.titukl{display:block;font-size:14px;font-weight:bold !important;text-align:center;padding-bottom: 14px;}
		.titule{display:block;font-size:12px;font-weight:bold !important;text-align:center;padding-bottom: 4px;padding-top: 12px;}
		.respta{text-align:center;padding:7px;border-left:1px solid;margin:0px auto;border-right:1px solid;margin:0px auto;margin:0px auto;height:14px;}
		.respta li{list-style-image: none;display: block;float: left;}
		ul.ttle{margin: 0px auto;background:url('template/images/degrada4.png') repeat-x scroll center top transparent;font-weight:bold;padding:7px;height:20px;}
		#acoge ul:last-child{border-bottom:1px solid;margin-bottom:12px;}
		#acoge ul:first-child{border-top:1px solid;}
		.tot{font-weight:bold;}
		#muestraDat{float:left;width:100%;text-align:center;}
		#muestra1{}
		#muestra2{}
		.muestra{float:left;}
		#mue{margin:0 auto;}
		.alerti{top:160px;position:relative;left:-17px;}
		.momto{}
</style>
<div class="alerti"></div>

<?php
$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_DATOS;
$html->tituloTarea = '';
$html->hide = true;
$html->anchoTabla = 580;
$html->anchoCeldaI = 320; $html->anchoCeldaD = 250;

$html->inHide(true, 'query');
$html->inCheckBox("Estimado del valor bruto en el intervalo de tiempo seleccionado", 'estimadoBR', 5, 1);
$html->inCheckBox("Valor de Ingresos ocurridos en el intervalo de tiempo seleccionado", 'acept', 5, 1);
$html->inCheckBox("Valor de Ingresos por empresas ocurridos en el intervalo de tiempo seleccionado", 'ecept', 5, 1);
$html->inCheckBox("Estimado del valor en el intervalo de tiempo seleccionado", 'estimado', 5, 1);
$html->inCheckBox("Datos en el intervalo de tiempo seleccionado", 'datos', 5, 1);
$html->inCheckBox("Bancos en el intervalo de tiempo seleccionado", 'bancos', 5, 1);
$html->inCheckBox("Comercios en el intervalo de tiempo seleccionado", 'comercios', 5, 1);
$html->inCheckBox("Estado de las transacciones", 'estado', 5, 1);
$html->inCheckBox("Análisis por moneda", 'moneda', 5, 1);
$html->inCheckBox("Análisis por TPV", 'tpv', 5, 1);
$html->inCheckBox("Análisis por TPV - Moneda", 'tpvm', 5, 1);
$html->inCheckBox("Denegadas por TPV - Moneda", 'dpvm', 5, 1);
$html->inCheckBox("Análisis por Banco", 'banc', 5, 1);
$html->inCheckBox("Análisis por Banco - Moneda", 'bancm', 5, 1);
$html->inCheckBox("Análisis por TPV - Comercio", 'tpvc', 5, 1);
$html->inCheckBox("Análisis por Comercios", 'comerc', 5, 1);
$html->inCheckBox("Análisis Comercios - Monedas", 'common', 5, 1);
$html->inCheckBox("Denegadas Comercios - Monedas", 'dommon', 5, 1);
$html->inCheckBox("Análisis Comercios - Con y Sin 3D", 'comseg', 5, 1);
$html->inCheckBox("Análisis de Países por Banco", 'pbanc', 5, 1);
$html->inCheckBox("Análisis de Países por TPV", 'ptpv', 5, 1);
$html->inCheckBox("Análisis de Países por Comercios", 'pcom', 5, 1);

$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1');
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2');

$html->inCheckBox("Comparación", 'compa', 5, 1, null, null, null, 'disabled="disabled"');
echo $html->salida('<input class="formul" id="enviaForm" name="enviar" type="button" value="' . _FORM_SEND . '" />');


?>

<div id="muestraDat">
	<div id="mue">
		<div id="muestra1" class="muestra"></div>
		<div id="muestra2" class="muestra"></div>
	</div>
</div>

