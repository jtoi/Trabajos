<?php define( '_VALID_ENTRADA', 1 );
if (!session_start()) session_start();
include_once( '../../../configuration.php' );
include '../../../include/mysqli.php';
require_once( '../../../include/hoteles.func.php' );
include( '../../adminis.func.php' );

$temp = new ps_DB;
$d = $_POST;
//print_r($d); exit;
include_once( '../../lang/'.$d['idioma'].'.php' );

$colEsp = array();
$busqueda = array();
if ($d['salida'] == 3) { 
	$columnas = array(
					array('', "color{col}", "", "center", "center" ),
					array(_COMERCIO_ID, "id", "50", "center", "left" ),
					array(_MENU_ADMIN_COMERCIO, "comercio", "100", "center", "left" ),
					array("Cliente", "cliente", "150", "center", "left"),
					array("Usuario", "usrL", "150", "center", "left"),
					array(_REPORTE_REF_COMERCIO, "codigo", "", "center", "left" ),
					array("Referencia<br>Concentrador", "id_transaccion", "", "center", "left" ),
					array(_REPORTE_FECHA, "fecha", "", "center", "center" ),
					array(_REPORTE_VALOR, "valor{val}", "", "center", "right" ),
					array(_COMERCIO_MONEDA, "moneda", "", "center", "center"),
					array(_REPORTE_ESTADO, "estad", "", "center", "center" )
				);

} elseif ($d['salida'] == 2) { 
	$columnas = array(
					array('', "color{col}", "", "center", "center" ),
					array(_COMERCIO_ID, "id", "50", "center", "left" ),
					array(_MENU_ADMIN_COMERCIO, "comercio", "100", "center", "left" ),
					array("Cliente", "cliente", "150", "center", "left" ),
					array(_REPORTE_REF_COMERCIO, "codigo", "", "center", "left" ),
					array("Referencia<br>Concentrador", "id_transaccion", "", "center", "left" ),
					array(_REPORTE_FECHA, "fecha", "", "center", "center" ),
					array(_REPORTE_VALOR, "valor", "", "center", "right" ),
					array(_COMERCIO_MONEDA, "moneda", "", "center", "center"),
					array(_REPORTE_ESTADO, "estad", "", "center", "center" )
				);

} else {
	$columnas = array(
					array(_COMERCIO_ID, "id", "50", "center", "left" ),
					array(_MENU_ADMIN_COMERCIO, "comercio", "", "center", "left" ),
					array(_REPORTE_REF_BBVA, "codigo", "75", "center", "left" ),
					array(_REPORTE_FECHA, "fecha_mod", "135", "center", "center" ),
					array(_REPORTE_VALOR, "valor{val}", "65", "center", "right" ),
					array(_COMERCIO_MONEDA, "moneda", "60", "center", "center"),
					array(_COMERCIO_TASA, "tasaM", "60", "center", "right"),
					array(_COMERCIO_EUROSC, "euroEquiv{val}{tot}", "60", "center", "right"),
					array(_REPORTE_ESTADO, "estad", "83", "center", "center" ));
}

?>
<link href="../../template/css/admin.css" rel="stylesheet" type="text/css" />
<body onload="javascript: printPag()">
<table id="princ" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="4" class="banner">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" class="inf"></td>
  </tr>
</table><br><br>
<?php tablanp( stripslashes($d['querys']), $columnas ); ?>
<script language="JavaScript">
function printPag() {
document.body.offsetHeight;window.print();}
</script>
</body>
