<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores

$html = new tablaHTML;
global $temp;
$d = $_POST;
$incluye = "";
$comercio = "";
$transaccion = "";
$importe = "";
$moneda = "";


if ($d['comercio']) $comercio = $d['comercio'];
if ($d['transaccion']) $transaccion = $d['transaccion'];
if ($d['importe']) $importe = $d['importe'];
if ($d['moneda']) $moneda = $d['moneda'];

$titulo_tarea = ' &nbsp; ';

$javascript = "
	<script language=\"JavaScript\" type=\"text/javascript\">
	function verifica() {
		if (
				(checkField (document.admin_form.transaccion, isAlphanumeric, '')) &&
				(checkField (document.admin_form.importe, isInteger, '')) 
			) {
			return true
		}
		return false;
	}</script>";

$html->java = $javascript;

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_COMPROBACION;
$html->tituloTarea = $titulo_tarea;
$html->anchoTabla = 650;
$html->tabed = true;
$html->anchoCeldaI = 300;
$html->anchoCeldaD = 340;

if (strpos($_SESSION['idcomStr'], ',') ) {
	$q = "select idcomercio as id, nombre from tbl_comercio where id in (".$_SESSION['idcomStr'].") order by nombre";
	$html->inSelect(_COMPRUEBA_COMERCIO, 'comercio', 2, $q);

} else
	$html->inTextb(_COMPRUEBA_COMERCIO, $_SESSION['comercio'], 'comercio', null, null, "readonly");
$html->inTextb(_COMPRUEBA_TRANSACCION, $transaccion, 'transaccion');
$html->inTextb(_COMPRUEBA_IMPORTE, $importe, 'importe');
$q = "select idmoneda id, moneda nombre from tbl_moneda";
$html->inSelect(_COMERCIO_MONEDA, 'moneda', 2, $q, '978');
$html->inTextb(_COMPRUEBA_OPERACION, 'P', 'operacion');


if ($d['comercio'] && $d['transaccion'] && $d['importe'] && $d['moneda'] ) {
	$comercio = $d['comercio'];
	$transaccion = $d['transaccion'];
	$importe = $d['importe'];
	$moneda = $d['moneda'];
	$operacion = $d['operacion'];

	for ($i=0; $i<strlen($importe);$i++){
		if (!strpos(' 0123456789', $importe{$i})) {$importe = false; break;}
	}
	for ($i=0; $i<strlen($moneda);$i++){
		if (!strpos(' 0123456789', $moneda{$i})) {$moneda = false; break;}
	}

	if ($importe < 1 || strlen($importe) > 9) {$label = "falla por importe";}
	if ($moneda < 1 || strlen($moneda) != 3) {$label = "falla por moneda"; }
	if (strlen($comercio) > 15) {$label = "falla por comercio"; }
	if (strlen($transaccion) > 12) {$label = "falla por transaccion"; }
	if (!$operacion == 'P' || !$operacion == 'C' ) {$label = "falla por operacion";}

if (_MOS_CONFIG_DEBUG) 	echo "comercio=$comercio<br>";
if (_MOS_CONFIG_DEBUG) 	echo "transaccion=$transaccion<br>";
if (_MOS_CONFIG_DEBUG) 	echo "importe=$importe<br>";
if (_MOS_CONFIG_DEBUG) 	echo "moneda=$moneda<br>";
if (_MOS_CONFIG_DEBUG) 	echo "operacion=$operacion<br>";

	if ($firmaCheck = convierte($comercio, $transaccion, $importe, $moneda, $operacion)) 
		$html->inTexto(_COMPRUEBA_MD5, $firmaCheck);
	else 
		$html->inTextoL('Debe descargar la palabra secreta primero primero');
	
}

echo $html->salida();


?>
