<?php

defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

$temp = new ps_DB;
$html = new tablaHTML;

$d = $_REQUEST;
if (_MOS_CONFIG_DEBUG) {
	foreach ($d as $key => $value) {
		echo "$key = $value<br>";
	}
}

if (!$d['pasar']) {
	$q = "select idPasarela id from tbl_pasarela where activo = 1 and tipo = 'P' order by nombre limit 0,1";
	$temp->query($q);
	$pasar = $temp->f('id');
} else $pasar = $d['pasar']; 

/*Haciendo el formulario*/
$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_PAISLIM;
$html->tituloTarea = '';
// $html->hide = true;
$html->anchoTabla = 600;
$html->anchoCeldaI = 70; $html->anchoCeldaD = 520;

$html->java = "<style>.radElm{width:250px;float:left;}.botForm{display:none;}</style>";

$q = "select idPasarela id, nombre from tbl_pasarela where activo = 1 and tipo = 'P' order by nombre";
$html->inSelect('Pasarela', 'pasar', 1, $q, $pasar, null, null);

$html->classCss='selpas';
$ini = "select id, nombre from tbl_paises order by nombre";
$q = "select idpais from tbl_colPaisPasarelDeng where idpasarela = '$pasar'";
$temp->query($q);
$html->inCheckBox('Países','pais', 2, $ini, $temp->loadResultArray());

echo $html->salida();

?>
<div class="alerti"></div>
<script type="text/javascript">
$("#pasar").change(function(){

	document.forms[0].submit();
});
 $(".selpas").click(function(){
	 var pais = $(this).val();
	 var est = 0;
	 if ($(this).attr("checked")) est = 1
	 else est = 0;
	 
// 	$(".alerti").esperaDiv('muestra');
	$.ajax({
		type: 'POST',
		url: 'componente/core/ejec.php',
		dataType: 'text',
		contentType: 'application/x-www-form-urlencoded; charset=iso-8859-1',
		data: ({
			fun:'paslim',
			pas:$("#pasar").val(),
			pais:pais,
			est:est
		}),
		success: function (data) {
			var datos = eval('(' + data + ')');
// 			$('.alerti').esperaDiv('cierra');
			if (datos.error) alert(datos.error)
		}
	});
 });

</script>
