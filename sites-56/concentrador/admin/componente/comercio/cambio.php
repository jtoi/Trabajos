<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );


$html = new tablaHTML();
global $temp;
$d = $_POST;
$fechaNow = time();
$hor3pm = mktime(14,0,0,date('m'),date('d'),date('Y'));
$paso = 0;
if (_MOS_CONFIG_DEBUG) {echo "<br>"; print_r($d); echo "<br>";}

//inserta Art&iacute;culo
if ($d['inserta']) {
	$tasaInc = leeSetup('incBCE');
	$banco = leeSetup('bancoUsado');
	if (strlen($banco) == 0) $banco = "visa";
	
	(strpos(",",$d['usd'])) ? $usd = str_replace(",", ".", $d['usd']) + $tasaInc : $usd = round(($d['usd'] + $tasaInc),4);
	(strpos(",",$d['gbp'])) ? $gbp = str_replace(",", ".", $d['gbp']) + $tasaInc : $gbp = round(($d['gbp'] + $tasaInc),4);
	(strpos(",",$d['cad'])) ? $cad = str_replace(",", ".", $d['cad']) + $tasaInc : $cad = round(($d['cad'] + $tasaInc),4);
	
	$q = "select count(*) total from tbl_cambio where fecha = $hor3pm";
	$temp->query($q);
	$temp->f('total') > 0 ? $pase = true : $pase = false;
	
	($pase) ? $q = "update tbl_cambio set $banco = $usd where moneda = 'USD' and fecha = $hor3pm" :
			$q = "insert into tbl_cambio ($banco, moneda, fecha) values ($usd, 'USD', $hor3pm)";
	$temp->query($q);
	($pase) ? $q = "update tbl_cambio set $banco = $gbp where moneda = 'GBP' and fecha = $hor3pm" :
			$q = "insert into tbl_cambio ($banco, moneda, fecha) values ($gbp, 'GBP', $hor3pm)";
	$temp->query($q);
	($pase) ? $q = "update tbl_cambio set $banco = $cad where moneda = 'CAD' and fecha = $hor3pm" :
	$q = "insert into tbl_cambio ($banco, moneda, fecha) values ($cad, 'CAD', $hor3pm)";
	$temp->query($q);

	actSetup($usd, 'USD');
	actSetup($gbp, 'GBP');
	actSetup($cad, 'CAD');
	actSetup($hor3pm, 'fechaTasa');
	
	$paso=1;

}
($banco) ?
$q = "select count(*) total from tbl_cambio where $banco > 0 and fecha = $hor3pm" :
$q = "select (visa+bce+xe) total from tbl_cambio where moneda = 'USD' and fecha = $hor3pm";
$temp->query($q);
$temp->f('total') == 0 ? $pase = true : $pase = false;

$fec = leeSetup('fechaTasa');
$usd = leeSetup('USD');
$gbp = leeSetup('GBP');
$cad = leeSetup('CAD');
//echo $fec." <= ".mktime(15,0,0,date('m'),date('d')-1,date('Y'));
if ($pase) {
	
?>
<script type="text/javascript">
//	$.post('componente/comercio/ejec.php',{
//		fun:'cambUSD'
//	},function(data){
//		var datos = eval('(' + data + ')');
//		$("#cambioUSD").html(datos.cont);
//	});
$(document).ready(function(){
});
</script>
<?php

//javascript
$javascript = "";
if ($alerta)
	$javascript .= "alert('$alerta');";
$javascript .= "</script>";


$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_MENU_CAMBIO;
$html->anchoTabla = 600;
$html->anchoCeldaI = 255;$html->anchoCeldaD = 325;
$html->tituloTarea = "&nbsp;";
$html->java = $javascript;

$html->inHide("true", "inserta");
$html->inTextb("Valor para USD",$usd,"usd");
$html->inTextb("Valor para GBP", $gbp, "gbp");
$html->inTextb("Valor para CAD", $cad, "cad");

echo $html->salida();

?>
<div id="cambioUSD" class="cambio"><a target="_blank" href="http://corporate.visa.com/pd/consumer_services/consumer_ex_results.jsp?actualDate=<?php echo date('m/d/Y'); ?>&homCur=USD&forCur=EUR&fee=0">Para el cambio de USD</a></div>
<div id="cambioGBP" class="cambio"><a target="_blank" href="http://corporate.visa.com/pd/consumer_services/consumer_ex_results.jsp?actualDate=<?php echo date('m/d/Y'); ?>&homCur=GBP&forCur=EUR&fee=0">Para el cambio de GBP</a></div>
<div id="cambioCAD" class="cambio"><a target="_blank" href="http://corporate.visa.com/pd/consumer_services/consumer_ex_results.jsp?actualDate=<?php echo date('m/d/Y'); ?>&homCur=CAD&forCur=EUR&fee=0">Para el cambio de CAD</a></div>

<?php
} else {
	if ($paso==1) {
?>

<div id="novale" style="font-size:14px;font-weight:bold;margin:0 auto;position:relative;top:50px;width:252px;">Cambio introducido.</div>

<?php } else { ?>


<div id="novale" style="font-size:14px;font-weight:bold;margin:0 auto;position:relative;top:50px;width:252px;">Ya se introdujo el cambio para hoy.</div>

<?php } ?>
<?php } ?>