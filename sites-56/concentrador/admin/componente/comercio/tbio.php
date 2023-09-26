<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
$temp = new ps_DB;
$d =$_REQUEST;
// print_r($_REQUEST);echo "<br>";

if ($d['fecha1']){
	$fecha1 = $d['fecha1'];
	$fecha2 = $d['fecha2'];
} else {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, date('m'), date('d')-30, date('Y')));
	$fecha2 = date('d/m/Y', time());
}
if ($d['est']) $est = $d['est']; else $est = "'A','B','V','P'";

$query = "select distinct idcomercio from tbl_transacciones t
			where tipoEntorno = 'P'
				and t.tipoOperacion = 'T'";

$temp->query($query);
$comercios = implode("','", $temp->loadResultArray());

if (isset ($d['comercio'])) $comercId = implode(',', $d['comercio']); else $comercId = $comercios;

$q = "select distinct pasarela from tbl_transacciones t
		where tipoEntorno = 'P'
		and estado = 'P'
		and t.tipoOperacion = 'T'";
$temp->query($q);
$pasarelas = implode("', '", $temp->loadResultArray());

if (isset ($d['pasarela'])) $pasarId = implode(',', $d['pasarela']); else $pasarId = $pasarelas;
				
$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_TBIO;
$html->tituloTarea = '';
$html->anchoTabla = 500;
$html->anchoCeldaI = 170; $html->anchoCeldaD = 320;

$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', null, null, null, null, $ver);
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', null, null, null, null, $ver);
$valInicio = array(array('1','Si'));
$html->inCheckBox('Saltar las fechas', 'fechano', 3, $valInicio);


$factArr = array(
		array("'A','P'", "Todas")
		,array("'A'", "Transferidas")
		,array("'P'", "Pendientes de Transferir")
// 		,array("3", "Canceladas")
);
$html->inSelect('Estado', 'est', 3, $factArr, $est);

$query = "select idcomercio id, nombre from tbl_comercio where idcomercio in ('$comercios') order by nombre";
$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query,  str_replace(",", "', '", $comercId), null, null, "multiple size='5'");


$valInicio = "select idPasarela id, nombre from tbl_pasarela where idPasarela in ('$pasarelas') order by nombre asc";
$html->inSelect(_COMERCIO_PASARELA, 'pasarela', 5, $valInicio, str_replace(",", "', '", $pasarId), null, null, "multiple size='5'" );

echo $html->salida();

$fecha1 = $fecha1." 00:00:00";
$fecha2 = $fecha2." 23:59:59";

$q = "select idcomercio, nombre from tbl_comercio where idcomercio in 
		(select distinct idcomercio from tbl_transacciones t 
			where t.estado in ($est) 
				and tipoEntorno = 'P'
				and t.tipoOperacion = 'T'
				and t.fecha between ".to_unix($fecha1)." and ".to_unix($fecha2)." or t.fecha_mod between ".to_unix($fecha1)." and ".to_unix($fecha2).") 
				order by nombre";
$temp->query($q);
// echo $q."<br>";
$arrCom = $temp->loadRowList();
$q = "select idPasarela, nombre from tbl_pasarela p where tipo in ('T') and idPasarela in 
		(select distinct pasarela from tbl_transacciones t 
			where t.estado in ($est) 
				and tipoEntorno = 'P' 
				and estado = 'P'
				and t.tipoOperacion = 'T'
				and t.fecha between ".to_unix($fecha1)." and ".to_unix($fecha2)." or t.fecha_mod between ".to_unix($fecha1)." and ".to_unix($fecha2).")
				order by nombre";
$temp->query($q);
// echo $q."<br>";
$arrPasa = $temp->loadRowList();

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
})
</script>
<div id='Tbl' style="width: 1300px; margin-bottom: 20px;">
<div>
<table cellpadding="0" cellspacing="0" align="center">
	<tr>
	<th>Comercio \ Pasarela</th>
<?php
for ($i = 0; $i < count($arrPasa); $i++) {
	$arrPasa[$i][2] = 0;
	if ($i % 2 == 0) echo "<th class='odd'>{$arrPasa[$i][1]}</th>";
	else echo "<th>{$arrPasa[$i][1]}</th>";
}
?>
		<th>Total Comercio</th>
	</tr>
<?php
$totGen = 0;
for ($i = 0; $i < count($arrCom); $i++) {
	$totCom = 0;
	$sale = "<tr class='pase'><td class='tdCom'>{$arrCom[$i][1]}</td>";
	for($j=0;$j<count($arrPasa);$j++) {
		
		$q = "select sum(case t.estado 
				when 'A' then (t.euroEquiv) 
				else (valor_inicial/100/(select valor from tbl_setup s, tbl_moneda m where t.moneda = m.idmoneda and s.nombre = m.moneda)) end) euroEqu
			from tbl_transacciones t, tbl_transferencias f where t.pasarela = {$arrPasa[$j][0]} 
				and t.idcomercio = ".$arrCom[$i][0]." 
				and f.idTransf = t.idtransaccion
				and f.activa = 1
				and t.tipoEntorno = 'P' 
				and t.estado in ($est) 
				and t.tipoOperacion = 'T'
				and t.fecha_mod between ".to_unix($fecha1)." and ".to_unix($fecha2);
//		echo $q. "<br>";
		$temp->query($q);
		$val = $temp->f('euroEqu');
	if ($j % 2 == 0) $sale .= "<td class='odd'>". number_format($val,2,'.',' ')."</td>";
	else $sale .= "<td>". number_format($val,2,'.',' ')."</td>";
		$totCom += $val;
		$totGen += $val;
		$arrPasa[$j][2] += $val;
	}
		$sale .= "<td>".number_format($totCom,2,'.',' ')."</td></tr>";
		if ($totCom != 0) echo $sale;
}

?>
	<tr id="tt"><td style="width: 150px;">Total Pasarela</td>
	<?php
for ($i = 0; $i < count($arrPasa); $i++) {
	if ($i % 2 == 0) echo "<td class='odd'>".number_format($arrPasa[$i][2],2,'.',' ')."</td>";
	else  echo "<td>".number_format($arrPasa[$i][2],2,'.',' ')."</td>";
}
echo "<td>".number_format($totGen,2,'.',' ')."</td>";
?></tr>
</table></div></div>

<?php 
$q = "select t.idtransaccion id,c.nombre comercio,p.nombre pasarelaN, round(t.tasa,4) tasaM,t.estado,t.fecha,t.fecha_mod, 
			(t.valor_inicial/100) 'valIni{val}', c.idcomercio,t.pasarela,t.moneda idmoneda,
			t.codigo,t.id_error error,round(t.tasaDev,4) tasaDev, m.moneda,p.tipo,
	case t.solDev when 1 then 1 else 0 end solDe,
	case t.estado
			when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when 'A' then (t.valor/100/tasa)
			else '0.00' end 'euroEquiv{val}',
	CASE (select count(*) from tbl_reserva r where r.codigo = t.identificador) 
		when 1 then concat('<a href=\"index.php?componente=comercio&pag=cliente&val=', t.identificador, '\">', t.identificador, '</a>')
		else identificador end identificador,
	case t.estado 
		when 'P' then 'green' 
		when 'A' then if (solDev = 0, 'black', 'gray')
		when 'D' then 'red' 
		when 'N' then 'violet' 
		when 'R' then 'brown' 
		when 'B' or 'V' then if (solDev = 0, (select case count(*) when 1 then 'blue' else '#65a3f9' end 'color' from tbl_devoluciones where idtransaccion = t.idtransaccion), 'gray')
		else 'olive' end 'color{col}',
	case t.pago 
		when 0 then 'No' 
		else 'Si' end pagada,
	case t.estado
		when 'B' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		when 'V' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		when 'R' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		else (t.valor / 100) end 'valor{val}',
	case t.estado 
		when 'P' then 'En Proceso' 
		when 'A' then if (solDev = 0, 'Aceptada', 'Sol. Devolc.')
		when 'D' then 'Denegada' 
		when 'N' then 'No Procesada' 
		when 'B' then 'Anulada' 
		when 'R' then 'Reclamada' 
		else 'Devuelta' end estad ";
	$from = "from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_pasarela p, tbl_transferencias f ";
	$where = " where c.idcomercio = t.idcomercio 
		and t.moneda = m.idmoneda 
		and p.idPasarela = t.pasarela
		and t.tipoOperacion = 'T' 
		and f.activa = 1
		and f.idTransf = t.idtransaccion
		and t.fecha_mod between ".to_unix($fecha1)." and ".to_unix($fecha2)." 
		and t.idcomercio in ('$comercId')
		and t.pasarela in ('$pasarId')
		and t.estado in ($est)";
	$orden = " t.fecha desc"; 
	
	//columnas a mostrar
	$ancho = 1300;
	$colEsp[] = array("t", _GRUPOS_FACTURA, "css_transf", _TAREA_ANULAR);
	$columnas = array(
				array('', "color{col}", "1", "center", "center" ),
				array(_COMERCIO_ID, "id", "50", "center", "left" ));

	if ($_SESSION['rol'] < 2 || strpos($comer, ',')) array_push($columnas, array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left" ));
	array_push($columnas, array(_REPORTE_REF_COMERCIO, "identificador", "95", "center", "left" ),
					array(_COMERCIO_PASARELA, "pasarelaN", "75", "center", "left" ),
					array(_REPORTE_FECHA, "fecha", "135", "center", "center" ),
					array(_REPORTE_VALOR_INICIAL, "valIni{val}", "65", "center", "right" ),
					array(_REPORTE_FECHA_MOD, "fecha_mod", "135", "center", "center" ),
					array(_REPORTE_VALOR, "valor{val}", "65", "center", "right" ),
					array(_COMERCIO_MONEDA, "moneda", "60", "center", "center"),
					array(_COMERCIO_TASA, "tasaM", "60", "center", "right"),
					array(_COMERCIO_EUROSC, "euroEquiv{val}", "70", "center", "right"),
					array(_REPORTE_ESTADO, "estad", "83", "center", "center" ));
	array_push($columnas, array(_REPORTE_ALCOMERCIO, "pagada", "60", "center", "center"));

// 	echo $q.$from.$where.$wherea." order by ".$orden;
	$querys = tabla( $ancho, 'E', $q.$from, $orden, $where, $colEsp, $busqueda, $columnas );
				
?>

<script type="text/javascript">
$(document).ready(function(){
	$("#fechano").click(function(){if($(this).is(":checked")) $("#fecha1").val('01/04/2008'); else $("#fecha1").val('<?php echo $fecha1; ?>');});
});
</script>
