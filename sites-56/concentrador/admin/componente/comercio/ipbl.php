<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$html = new tablaHTML;

global $temp;
$d = $_POST;
$fechaNow = time();
$incluye = "";

if ($d['ip']) {
	if (filter_var($d['ip'], FILTER_VALIDATE_IP)) {
		$temp->query("delete from tbl_ipBL where ip = '".$d['ip']."'");
		$temp->query("select count(*) total from tbl_ipblancas where ip = '".$d['ip']."'");
		if ($temp->f('total') > 0) {
			$temp->query("update tbl_ipblancas set fecha = ".time()." where ip = '$ip'");
			echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">La IP ya ha sido desbloqueada, puede avisar al Cliente para que intente nuevamente su entrada al Administracomercios</div>";

		} else {
			$temp->query("insert into tbl_ipblancas (ip, fecha, idAdmin, idComercio) values ('".$d['ip']."', unix_timestamp(), '".$_SESSION['id']."', 1)");
			echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">La IP fué correctamente desbloqueada, puede avisar al Cliente para que intente nuevamente su entrada al Administracomercios</div>";
		}
	} else echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">".$d['ip']." No es un número de IP válido.</div>";
}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = "Ip Bloqueda a la entrada del Admin";
$html->tituloTarea = "A Desbloquear";
$html->anchoTabla = 600;
$html->anchoCeldaI = $html->anchoCeldaD = 245;
$html->java = "";

//$html->inHide($comer, 'comer');
//$html->inHide(true, 'buscar');
$html->inTextb("IP", $ip, 'ip', null, null, null);

echo $html->salida();

?>
