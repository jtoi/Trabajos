<?php
defined('_VALID_ENTRADA') or die('Restricted access');

global $temp;
$html = new tablaHTML();

$file = 'GuiaUsuario.pdf';
$dir = '../documentos/';
// header ("Content-Disposition: attachment; filename=".$dir.$file." ");
// header ("Content-Type: application/x-zip-compressed");
// //
// header ("Content-Length: ".filesize($dir.$file));
//readfile($dir.$file);

$q = "select idcomercio, nombre from tbl_comercio where activo = 'S' and id in ({$_SESSION['idcomStr']}) order by nombre";
$temp->query($q);
$arrCom = $temp->loadRowList();
// echo json_encode($arrCom);

$html->idio = $_SESSION['idioma'];
$html->tituloPag = "Documentación";
$html->anchoTabla = 600;
$html->anchoCeldaI = 255;
$html->anchoCeldaD = 325;
$html->tituloTarea = "Descargar";
//$html->inTextoL("<a href=\"../documentos/ManualUsuario.pdf\" target=\"_blank\">Manual de usuario</a>");
//$html->inTextoL("<a href=\"../documentos/GuiaUsuario.pdf\" target=\"_blank\">Guía de Usuario para cobros online</a>");
//$html->inTextoL("<a href=\"../documentos/ManualInstalacionClienteVPN.pdf\" target=\"_blank\">Manual de istalación Cliente VPN</a>");
//$html->inTextoL("<a href=\"../documentos/clientevpn.exe.gz\" target=\"_blank\">Cliente VPN</a>");

$arrFic = scandir($dir);
foreach ($arrFic as $fic) {
    if (is_file($dir.$fic)){
        $html->inTextoL("<a href=\"".$dir."/".$fic."\" target=\"_blank\">".substr($fic,0,stripos($fic,'.'))."</a>");
    }
}

//verifico de los comercios a que el cliente pertenece cuántos tienen documentación especial
$arrComVer = array();
foreach ($arrCom as $comercio) {
	if (is_dir($dir.$comercio[0])) {
		$arrComVer[] = $comercio;
	}
}
if (count($arrComVer) == 1) {//cuando sólo hay un comercio
	$html->inCajaini('fic', 'lineaT', 'border-top: #ccc solid 1px;');
	$arrFic = scandir($dir.$arrComVer[0][0]."/");
	foreach ($arrFic as $fic) {
		if (is_file($dir.$arrComVer[0][0]."/".$fic)){
			$html->inTextoL("<a href=\"".$dir.$arrComVer[0][0]."/".$fic."\" target=\"_blank\">".substr($fic,0,stripos($fic,'.'))."</a>");
		}
	}
} else if (count($arrComVer) > 1 ){//cuando hay más de uno hago un select
	$html->inCajaini('fic', 'lineaT', 'border-top: #ccc solid 1px;');
	array_unshift($arrComVer, array("-", "Seleccione"));
	$html->inSelect("Comercio", 'comercio', 3, $arrComVer, '');
	$html->inCajaini('listafic', 'lineaT', '');
}

// echo json_encode($arrComVer);
echo $html->salida(" ");
?>
<script type="text/javascript">
$("#comercio").change(function(){
	var com = $(this).val();
	if (com != '-') {
		$.post('componente/comercio/ejec.php',{
			fun:	'damfic',
			com:	com
		},function(data){
			$("#caj_listafic").html('').show();
			var datos = eval('(' + data + ')');

			if (datos.error.length > 0) //alert(datos.error);

			if (datos.sale && datos.sale.length > 0) {
				$("#caj_listafic").html(datos.sale);
			}
				
		});
	}
});
</script>
