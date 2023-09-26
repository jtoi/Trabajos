<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$html = new tablaHTML;
global $temp;
$d = $_POST;
$incluye = "";

$javascript = "
	<script language=\"JavaScript\" type=\"text/javascript\">
	function verifica() {
		return true;
	}</script>";

$html->java = $javascript;

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _COMERCIO_PALABRA;
$html->tituloTarea = $titulo_tarea;
$html->anchoTabla = 650;
$html->tabed = true;
$html->anchoCeldaI = 300;
$html->anchoCeldaD = 340;

if ($d['enviar']) {
	if ($d['comercio']) $comercio = $d['comercio'];
	else $comercio = $_SESSION['comercio'];
	$pass = suggestPassword(20);
	
	$urlPass = "../desc/$comercio.txt";
	
	if ($fichero = fopen( $urlPass, 'w' )) {
		if (fwrite ( $fichero, $pass )) {
			$java = "window.open('componente/comercio/bajando.php?id=$comercio.txt', '_blank')";
			$query = "update tbl_comercio set palabra='$pass' where idcomercio = $comercio";
			$temp->query($query);
			
			$html->inTextoL(str_replace('{enlace}', $comercio, _PALABRA_EXPLICA_DESCARGA));
		}
		fclose($fichero);
	} else $html->inTextoL(_PALABRA_EXPLICA_NO);
	
	echo $html->salida("&nbsp;");

} else {
	$html->inTextoL(_PALABRA_EXPLICA);
	if (strpos($_SESSION['idcomStr'], ',') ) {
		$q = "select idcomercio as id, nombre from tbl_comercio where id in (".$_SESSION['idcomStr'].")";
		$q .= " order by nombre";
		$html->inSelect (_MENU_ADMIN_COMERCIO, 'comercio', 2, $q);

	}
	echo $html->salida("<input class='formul' name='enviar' type='submit' value='"._PALABRA_GENERAR."' />");
}



?>




