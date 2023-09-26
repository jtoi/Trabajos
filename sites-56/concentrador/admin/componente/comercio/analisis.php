<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
$temp = new ps_DB;
$d =$_REQUEST;
// print_r($_REQUEST);echo "<br>";

if ($d['fecha1']){
	$fecha1 = $d['fecha1'];
	$fecha2 = $d['fecha2'];
} else {
	$fecha1 = $fecha2 = date('d/m/Y', time());
}
if (!isset($d['estado'])) $d['estado'] = "'A','B','V','R'";

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_ANALISIS;
$html->tituloTarea = '';
$html->anchoTabla = 500;
$html->anchoCeldaI = 170; $html->anchoCeldaD = 320;

$estadoArr = array(
		array("'A','B','V','R'", _REPORTE_TODOS),
		array("'A'", _REPORTE_ACEPTADA)
);
$html->inSelect(_REPORTE_STATUS, 'estado', 3, $estadoArr, $d['estado']);
$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', null, null, null, null, $ver);
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', null, null, null, null, $ver);
echo $html->salida();

$fecha1 = $fecha1." 00:00:00";
$fecha2 = $fecha2." 23:59:59";

$q = "select idcomercio, nombre from tbl_comercio where idcomercio in 
		(select distinct idcomercio from tbl_transacciones t 
			where t.estado in ({$d['estado']}) 
				and tipoEntorno = 'P' 
				and t.fecha between ".to_unix($fecha1)." and ".to_unix($fecha2)." or t.fecha_mod between ".to_unix($fecha1)." and ".to_unix($fecha2).") 
				order by nombre";
$temp->query($q);
// echo $q."<br>";
$arrCom = $temp->loadRowList();
$q = "select idPasarela, nombre from tbl_pasarela p where tipo in ('T','P','R') and idPasarela in 
		(select distinct pasarela from tbl_transacciones t 
			where t.estado in ({$d['estado']}) 
				and tipoEntorno = 'P' 
				and estado = 'P'
				and t.fecha between ".to_unix($fecha1)." and ".to_unix($fecha2)." or t.fecha_mod between ".to_unix($fecha1)." and ".to_unix($fecha2).")
				order by nombre";
$temp->query($q);
// echo $q."<br>";
$arrPasa = $temp->loadRowList();

?>
<form name="exporta" action="impresion.php" method="POST">
	<input type="hidden" name="querys10" value="1">
	<input type="hidden" name="inSql" id="inSql" value='{dat13}'>
</form>
<div ><span style="cursor: pointer;" class="css_x-office-document" onclick="document.exporta.submit()" 
		onmouseover="this.style.cursor=&quot;pointer&quot;" alt="Exportar a CSV" title="Exportar a CSV"></span></div>
<?php 
		$salida .= "";
?>
<div id='Tbl' style="width: 1600px;">
<div>
<table cellpadding="0" cellspacing="0" >
	<tr>
	<th>Comercio \ Pasarela</th>
<?php
$envicsv = "Comercio \ Pasarela,";
for ($i = 0; $i < count($arrPasa); $i++) {
	$arrPasa[$i][2] = 0;
	if ($i % 2 == 0) echo "<th class='odd'>{$arrPasa[$i][1]}</th>";
	else echo "<th>{$arrPasa[$i][1]}</th>";
	$envicsv .= $arrPasa[$i][1].",";
}
?>
		<th>Total Comercio</th>
	</tr>
<?php
$envicsv .= "Total Comercio{n}";
$totGen = 0;
for ($i = 0; $i < count($arrCom); $i++) {
	$totCom = 0;
	echo "<tr class='pase'><td class='tdCom'>{$arrCom[$i][1]}</td>";
	$envicsv .= $arrCom[$i][1].",";
	for($j=0;$j<count($arrPasa);$j++) {
		
		$q = "select sum(case t.estado 
				when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/t.tasa)), (t.valor/100/t.tasa)) 
				when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/t.tasa)), (t.valor/100/t.tasa))
				when 'A' then (t.valor/100/t.tasa) 
				else '0.00' end) euroEqu
			from tbl_transacciones t where t.pasarela = {$arrPasa[$j][0]} 
				and t.idcomercio = ".$arrCom[$i][0]." 
				and t.tipoEntorno = 'P' 
				and t.estado in ({$d['estado']}) 
				and t.fecha_mod between ".to_unix($fecha1)." 
				and ".to_unix($fecha2);
//		echo $q. "<br>";
		$temp->query($q);
		if ($temp->f('euroEqu') == '' || $temp->f('euroEqu') == null) $val = 0; else $val = $temp->f('euroEqu');
		
		if ($j % 2 == 0) echo "<td class='odd'>". number_format($val,2,'.',' ')."</td>";
		else echo "<td>". number_format($val,2,'.',' ')."</td>";
		$envicsv .= $val.",";
		
		$totCom += $val;
		$totGen += $val;
		$arrPasa[$j][2] += $val;
	}
		echo "<td>".number_format($totCom,2,'.',' ')."</td></tr>";
		$envicsv .= $totCom."{n}";
}

?>
	<tr id="tt"><td style="width: 150px;">Total Pasarela</td>
	<?php
		$envicsv .= "Total Pasarela,";
	
for ($i = 0; $i < count($arrPasa); $i++) {
	if ($i % 2 == 0) echo "<td class='odd'>".number_format($arrPasa[$i][2],2,'.',' ')."</td>";
	else  echo "<td>".number_format($arrPasa[$i][2],2,'.',' ')."</td>";
	$envicsv .= $arrPasa[$i][2].",";
}
echo "<td>".number_format($totGen,2,'.',' ')."</td>";
$envicsv .= $totGen."{n}";
?></tr>
</table><br><br><br><br><br><br><br><br></div>

<?php
//$q = "select t.pasarela, t.idcomercio, sum(case t.estado 
//				when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/t.tasa)), (t.valor/100/t.tasa)) 
//				when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/t.tasa)), (t.valor/100/t.tasa))
//				when 'A' then (t.valor/100/t.tasa) 
//				else '0.00' end) euroEqu
//			from tbl_transacciones t where t.tipoEntorno = 'P' and t.estado in ('A','V','B') and t.fecha between ".to_unix($fecha1)." and ".to_unix($fecha2). " group by t.idcomercio, t.pasarela";
//$temp->query($q);
//$arrdat = $temp->loadAssocList();
////print_r($arrdat);echo "<br><br>";
//
//select p.nombre 'Pasarela', c.nombre 'Comercio', format(sum(case t.estado 
//			when 'B' then if (fecha_mod < fechaPagada, (-1 * ((valor_Inicial-valor)/100/tasa)), (valor/100/tasa)) 
//			when 'V' then if (fecha_mod < fechaPagada, (-1 * ((valor_Inicial-valor)/100/tasa)),  valor/100/tasa) 
//			when 'A' then (valor/100/tasa) else '0.00' end),2) 'Valor' 
//		FROM tbl_transacciones t, tbl_pasarela p, tbl_comercio c 
//		where t.idcomercio = c.idcomercio 
//			and t.pasarela = p.idPasarela 
//			and t.estado in ('A','V','B') 
//			and tipoEntorno = 'P'
//			and fecha_mod > 1409522400 
//		GROUP BY t.pasarela, t.idcomercio;
//
//$q = "select p.nombre pasarela, c.nombre comercio, format(sum(case t.estado 
//				when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/t.tasa)), (t.valor/100/t.tasa)) 
//				when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/t.tasa)), (t.valor/100/t.tasa))
//				when 'A' then (t.valor/100/t.tasa) 
//				else '0.00' end),2) euroEqu
//			from tbl_transacciones t, tbl_comercio c, tbl_pasarela p 
//			where t.idcomercio = c.idcomercio and p.idPAsarela = t.pasarela and t.tipoEntorno = 'P' and t.estado in ('A','V','B') 
//				and t.fecha_mod between ".to_unix($fecha1)." and ".to_unix($fecha2). " "
//		. "group by t.idcomercio, t.pasarela";
//$temp->query($q);
//$arrdat = $temp->loadAssocList();
//print_r($arrdat);
//
//foreach ($arrdat as $item) {
//	
//}
?>
<style>
	#Tbl{float:left;}
	.tdCom{padding-left: 10px;text-align: left !important;}
	#Tbl td{text-align:right;padding-right:5px;}
	tr td{padding: 3px 0;}
	th, #tt td{font-weight:bold;font-size: 12px;padding: 2px;width: 70px;border-top:1px solid black;border-bottom:1px solid black;}
	.odd{background-color:#ffd797;}
</style>
<script>//document.writeln("<div id='Tbl' style=\"margin:0 0 0 "+((window.innerWidth)-1300)/2+
		//"px; width:1300px; text-align:center;\">")
$(document).ready(function(){
	$(".pase").hover(function(){
		$(this).css("background-color", "#CCCCCC");
	});
	$(".pase").mouseleave(function(){
		$(this).css("background-color", "#ffffff");
	});
	$("#inSql").val('<?php echo $envicsv; ?>');
});

function verifica() {
	return true;
}
</script>

