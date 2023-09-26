<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$temp = new ps_DB;
$html = new tablaHTML;
$corCreo = new correo();

$d = $_POST;
if ($d['comercio']) {
	
	if (is_array($d['comercio'])) {
		$arrCom = $d['comercio'];
	} else {
		$arrCom = array($d['comercio']);
	}
	
	foreach ($arrCom as $value) {
		$q = "select CONVERT(CAST(e.nombre as BINARY) USING latin1) nomb, e.email from tbl_comercio c, tbl_economicos e where e.idcomercio = c.id and c.idcomercio = ".$value;
//		echo $q."<br>";
		$temp->query($q);
		$arrDest = $temp->loadRowList();
		
		$asunto = "Notificación de Cierre";
		$textoCor = "Estimado (a) {usuario}:<br><br>El cierre contable correspondiente al per&iacute;odo a liquidar, est&aacute; disponible en el Administrador de Comercios. Usted puede descargarlo accediendo con su nombre de usuario y contrase&ntilde;a a trav&eacute;s de la opci&oacute;n Comercio/Ver cierres.<br><br>Por favor, recuerde enviar la(s) factura(s) al buz&oacute;n <a href='mailto:facturacion@bidaiondo.com'>facturacion@bidaiondo.com</a>.<br><br>Administrador de Comercios<br>Bidaiondo S.L.";
		
		foreach($arrDest as $dest) {
			$corCreo->to($dest[1]);
			if (!$corCreo->todo(55, $asunto, str_replace("{usuario}", $dest[0], $textoCor))) {
				$corCreo->todo(37, 'Error en el envío de Cierre', "El envío del cierre ".$d['cr']." a ".$dest[0]." ".$dest[1]." a dado error.");
				error_log(" El envío del cierre adelantado a ".$dest[0]." ".$dest[1]." a dado error.");
			} else {
				echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">Correo enviado satisfactoriamente a ".$dest[0]."</div>";
			}
		}
	}
}


$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
				function verifica() {
					return true;
				}
				</script>
				<style> #usrTr{width:250px;}  </style>";

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_CIERREADEL;
$html->tituloTarea = 'Enviar';
$html->hide = false;
$html->anchoTabla = 600;
$html->anchoCeldaI = 170; $html->anchoCeldaD = 420;


$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
$html->inSelect(_COMERCIO_TITULO, 'comercio', 1, $query,  str_replace(",", "', '", $comercId), null, null, "multiple size='12'");
echo $html->salida();
?>

