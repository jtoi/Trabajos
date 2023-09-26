<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );


$html = new tablaHTML();
global $temp;
$url = "#";

if ($_SESSION['grupo_rol'] <= 1) {
	$comercio = '122327460662';
} elseif ($_SESSION['grupo_rol'] >= 10 ) {
	$comercio = $_SESSION['comercio'];
}

if ($comercio) {
	$q = "select r.codigo, c.voucherEs, c.voucherEn from tbl_reserva r, tbl_comercio c where c.idcomercio = r.id_comercio and r.id_comercio = ".$comercio." limit 0,1";
	$temp->query($q);
//	echo $temp->_sql;
	$url = _ESTA_URL."/voucher.php?tr=".$temp->f('codigo')."&co=".$comercio;
	$conEs = str_replace("\n", "saltolinea", $temp->f('voucherEs'));
	$conEs = str_replace("\r", "", $conEs);
	$conEn = str_replace("\n", "saltolinea", $temp->f('voucherEn'));
	$conEn = str_replace("\r", "", $conEn);
}
//echo $conEn;
?>
<script type="text/javascript">
	var conEs = '<?php echo $conEs; ?>';
	var conEn = '<?php echo $conEn; ?>';
	var re = /saltolinea/gi;
//	function envia() {
//		$.post('componente/comercio/ejec.php',{
//				fun:'vouc',
//				htm:$("#codigo").val(),
//				idio:$("#idioma").val(),
//				com:$("#comer").val()
//			},function(data){
//				var datos = eval('(' + data + ')');
//				alert(datos.error);
//		});
//	}
	$(document).ready(function(){
		$("#codigo").val(conEs.replace(re,"\n"));
		$("#url").click(function(){
			if ($("#idioma").val() == 'Es') {
				idiom = 'es';
			} else {
				idiom = 'en';
			}
			window.open ('<?php echo $url; ?>&id='+idiom, '_blank');
		});
		$("#idioma").change(function(){
			if ($(this).val() == 'Es') $("#codigo").val(conEs.replace(re,"\n"));
			if ($(this).val() == 'En') $("#codigo").val(conEn.replace(re,"\n"));
		});
	});
</script>
<?php

//javascript
$javascript = "";
if ($alerta)
	$javascript .= "alert('$alerta');";
$javascript .= "</script>";


$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_VOUCHER;
$html->anchoTabla = 600;
$html->anchoCeldaI = 90;$html->anchoCeldaD = 490;
$html->tituloTarea = "&nbsp;";
$html->java = $javascript;

$html->inHide($comercio, "comer");
$html->inTextoL("<div style='text-align:left;padding-left:20px;'>Leyenda:<br><br> {COMERCIO}: Nombre del Comercio<br>{CLIENTE}: Nombre del Cliente que efectúa el pago.<br>
	{FECHA}: Fecha<br>{TRANSACCION}: Identificador de la transacción para el Administrador de Comercios<br>{IMPORTE}: Importe de la operación<br>{COMTPV}: Comercio al cual el cliente reporta el pago<br>
	{DATACOMERCIO}: Datos del Comercio<br>{DESCRIPCION}: Servicio a prestar al cliente<br>{OPERACION}: Identificador de la transacción para el comercio<br>
	{ANO}: Año actual para el Copyright<br><br>Estas cadenas son sustituídas automáticamente por el Concentrador para cada transacción.</div>");
$html->inTexarea(_COMERCIO_CODIGO, '', codigo, 30, '', '', '', 75);
$valInicio = array(array('Es', _PERSONAL_ESP), array('En', _PERSONAL_ING));
$html->inSelect(_PERSONAL_IDIOMA, 'idioma', 3, $valInicio, 'es');

echo $html->salida('<input class="formul" id="enviaForm" name="enviar" type="button" onclick="envia()" value="' . _FORM_SEND . '" />');

?>
<div id="cambioGBP" class="cambio"><span id="url" style="text-decoration:underline;cursor:pointer;"><?php echo _COMERCIO_VERVOUCHER; ?></span></div>
