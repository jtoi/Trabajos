<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;

//global $temp;
$d = $_POST;
$fechaNow = time();
$alerta = "";
$ficheros = array();
$anoH = date("Y");
$anoC = 2010;

if (isset ($d['ano'])) $ano = $d['ano'];else $ano = $anoH;

//echo "cod=".generaCodEmp();
$html->java = $javascript;

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_VERCIERRES;
$html->tituloTarea = _REPORTE_TASK;
$html->anchoTabla = 450;
$html->tabed = true;
$html->anchoCeldaI = 105;
$html->anchoCeldaD = 310;
//print_r ($_SESSION);

if ($_SESSION['grupo_rol'] < 2 || strpos($_SESSION['idcomStr'], ',')) {
	
	if ($_SESSION['comercio'] == 'todos') $query = "select idcomercio id, nombre from tbl_comercio order by nombre";
	else $query = "select idcomercio id, nombre from tbl_comercio where id in (".$_SESSION['idcomStr'].") order by nombre";

	if ($d['comercio']) $comercId = $d['comercio'];
	else {
		$temp->query($query);
		$comercId = $temp->f('id');
	}
	
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $query, $comercId, null, null);

} else {
	$comercId = $_SESSION['comercio'];
}

$html->inSelect("Año", 'ano', 4, array($anoC,$anoH), $ano);
echo $html->salida();
	

?>
<script type='text/javascript'>

function cambiaD(val) {
	$("#cierr").css('display','none');
	$("#trans").css('display','none');
	if (val == 'T') $("#trans").css('display','inline');
	else $("#cierr").css('display','inline');
}

$(document).ready(function(){
	$('#resultC tbody').load('componente/comercio/resultado.php', {
		comercioId: '<?php echo $comercId ?>',
		trans: "N",
		ano:<?php echo $ano; ?>
	});
	$('#resultT tbody').load('componente/comercio/resultado.php', {
		comercioId: '<?php echo $comercId ?>',
		trans: "S",
		ano:<?php echo $ano; ?>
	});
	
});


</script>
<div class="escoge" style="text-align: center;line-height: 24px;">Mostrar: 
	<input type="radio" value="C" onclick="cambiaD(this.value)" id="mostrC" name="mostr" checked="checked" /><label for="mostrC">Cierres</label>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" id="mostrT" onclick="cambiaD(this.value)" name="mostr" value="T" /><label for="mostrT">Transferencias</label> </div>
<div id="cierr" style="float: left;width: 100%">
	<table cellspacin="0" cellpadding="0" align="center" id="resultC" width="400">
		<thead>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">Trayendo la información. Espere unos segundos...</td>
			</tr>
		</tbody>
	</table>
</div>

<div id="trans" style="float: left;width: 100%;display:none;">
	<table cellspacin="0" cellpadding="0" align="center" id="resultT" width="400">
		<thead>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">Trayendo la información. Espere unos segundos...</td>
			</tr>
		</tbody>
	</table>
</div>

