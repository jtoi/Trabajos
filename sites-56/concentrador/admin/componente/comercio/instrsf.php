<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

global $temp;
$html = new tablaHTML;


$fecha = date('d/m/Y', time());
$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_TRFINS;
$html->tituloTarea = "&nbsp;";
$html->anchoTabla = 400;
$html->anchoCeldaI = 130;
$html->anchoCeldaD = 250;

$query = "select id, nombre from tbl_comercio order by nombre";
$html->inHide(true, 'inserta');
$html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $query, $comercId, null, null);
$html->inTextb(_TRNS_ORD, 'Julio', 'cliente',null,null,'style="width:240px"');
$html->inTextb(_FORM_CORREO, 'jtoirac@gmail.com', 'correo',null,null,'style="width:240px"');
$html->inSelect('Pasarela', 'pasarela', 2, "select idPAsarela id, nombre from tbl_pasarela where tipo = 'T' and activo = 1", '');
$html->inTextb(_COMPRUEBA_IMPORTE, '', 'importe',null,null,'style="width:240px"');
$query = "select idmoneda id, moneda nombre from tbl_moneda";
$html->inSelect(_TRNS_DIV, 'moneda', 2, $query, '840');
$html->inTextb(_TRNS_CBO, '', 'cambio',null,null,'style="width:240px"');
$html->inTextb(_TRNS_LQD, '', 'euros',null,null,'style="width:240px"');
$html->inTexarea(_TRNS_MTV, '', 'motivo', 10, null, null, null, 42);
$html->inFecha(_REPORTE_FECHA_INI, $fecha, 'fecha1');


echo $html->salida('<input class="formul" id="enviaForm" name="enviar" type="button" onclick="envia()" value="' . _FORM_SEND . '" />');
?>
<script type="text/javascript" src="../js/jquery.myfun_c00cc3a629e84ef270c28b0d705a1ce1.js"></script>
<script type="text/javascript" >
function verifica() {
	if (
			(checkField (document.getElementById('enviaForm').cliente, isAlphanumeric, '')) &&
			(checkField (document.getElementById('enviaForm').importe, isMoney, '')) &&
			(checkField (document.getElementById('enviaForm').cambio, isMoney, '')) &&
			(checkField (document.getElementById('enviaForm').euros, isMoney, ''))
		) {
			envia();
	}
	return false;
}
function envia() {
	$(".title_tarea1").esperaDiv('muestra');
	$("#enviaForm").hide();
	$.post('componente/comercio/ejec.php',{
			fun:'instrf',
			com:$("#comercio").val(),
			cli:$("#cliente").val(),
			imp:$("#importe").val(),
			mon:$("#moneda").val(),
			cmb:$("#cambio").val(),
			eur:$("#euros").val(),
			mtv:$("#motivo").val(),
			fec:$("#fecha1").val(),
			pas:$("#pasarela").val()
		},function(data){
			var datos = eval('(' + data + ')');
			$(".title_tarea1").esperaDiv('cierra');
			$("#enviaForm").show();
			if (datos.error.length > 0) alert(datos.error);
			if (datos.pase.length > 0) alert(datos.pase);
	});
}
</script>