<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
global $temp;
global $send_m;
$html = new tablaHTML;
$d = $_REQUEST;
$whe = '';
// echo json_encode($d);

if (_MOS_CONFIG_DEBUG) var_dump($d);

if ($d['fec']) {
	$fec = $d['fec'];
} else $fec = date('d/m/Y').' %';

if ($d['comercio']) {
	$whe .= " and s.idcomercio in ('".$d['comercio']."')";
}

if ($d['idtr']) {
	$whe .= " and s.identificador like '%".$d['idtr']."%'";
	$idtr = $d['idtr'];
}

if ($d['pasar'] && $d['pasar'] != '%') {
	$whe .= " and s.idpasarela in ('".$d['pasar']."')";
}

if ($d['tpasar'] && $d['tpasar'] != '%') {
	$whe .= " and s.idpasarela in (".$d['tpasar'].")";
}

if ($d['epasar'] && $d['epasar'] != '%') {
	$whe .= " and s.idpasarela in (".$d['epasar'].")";
}

if ($d['motivo']) {
	$whe .= " and s.motivo like '%".$d['motivo']."%'";
	$motivo = $d['motivo'];
}

if ($d['mon']) {
	$whe .= " and s.idmoneda in ('".$d['mon']."')";
}


$temp->query("select idPasarela from tbl_pasarela where activo = 1 and secure = 1");
$strSec = implode(',',$temp->loadResultArray());
$temp->query("select idPasarela from tbl_pasarela where activo = 1 and secure = 0");
$strNse = implode(',',$temp->loadResultArray());

$arrTipo = array(
	array("%","Cualquiera"),
	array($strSec, "Segura"),
	array($strNse, "No segura")
);

$arrEmp[] = array("%", "Cualquiera");
$temp->query("select id, nombre from tbl_empresas where id != 5");
$arrEmpre = $temp->loadAssocList();
foreach ($arrEmpre as $empre) {
	$temp->query("select idPasarela from tbl_pasarela where activo = 1 and idempresa = ".$empre['id']);
	$arrEmp[] = array(implode(',',$temp->loadResultArray()), $empre['nombre']);
}

$arrPa[] = array("%", "Cualquiera");
$temp->query("select idPasarela id, nombre from tbl_pasarela where activo = '1' order by nombre");
$arrPaase = $temp->loadAssocList();
foreach ($arrPaase as $pas) {
	$arrPa[] = array($pas['id'], $pas['nombre']);
}
// $temp->query("select e.nombre, p.idPasarela from tbl_empresa e, tbl_pasarela p where p.idempresa = e.id and p.activo = 1 group by e.id")

/*
	* Construye el formulario de Buscar
	*/
$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
				function verifica() {
					return true;
				}
				</script>";

$html->idio = $_SESSION['idioma'];
$html->tituloPag = "Rotaci&oacute;n";
$html->tituloTarea = "";
$html->hide = true;
$html->anchoTabla = 500;
$html->anchoCeldaI = 120;
$html->anchoCeldaD = 345;

$query = "select id, nombre from tbl_comercio where activo = 'S' order by nombre";
$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query, $comercId, null, null);
$html->inSelect('Pasarela', 'pasar', 3, $arrPa, $pasaId, null, null);
$query = "select idmoneda id, moneda nombre from tbl_moneda where activo = '1' order by moneda";
$html->inSelect('Moneda', 'mon', 5, $query, $mon, null, null);
$html->inSelect('Tipo Pasarela', 'tpasar', 3, $arrTipo, $pasaIdT, null, null);
$html->inSelect('Empresa', 'epasar', 3, $arrEmp, $pasaIdE, null, null);
$html->inTextb('Operación', $idtr, 'idtr');
$html->inTextb('Motivo', $motivo, 'motivo');
$html->classCss = 'formul fecc hasDatepicker';
$html->inTextb('Fecha', $fec, 'fec', null, ' Fecha en formato (dd/mm/yyyy HH:mm:ss)');
$html->classCss = 'formul';

echo $html->salida();
/*
	* Termina el formulario de buscar
	*/

 if (!$d['comb']) $d['comb'] = $_SESSION['idcomStr'];
$vista = "select m.moneda, if (s.idpasarela > 0, (select concat(nombre,' - ', idPasarela) from tbl_pasarela where s.idpasarela = idPasarela), '-' ) pasar, if (s.idpasarela > 0, (select e.nombre from tbl_empresas e, tbl_pasarela l where l.idempresa = e.id and l.idPasarela = s.idpasarela), '-' ) emp, s.identificador, formateaF(s.fecha,10) fec, c.nombre comer, motivo from tbl_saltosPasar s, tbl_moneda m, tbl_comercio c";
$where = " where c.id = s.idcomercio and m.idmoneda = s.idmoneda and from_unixtime(s.fecha,'%d/%m/%Y %H:%i:%s') like '$fec'".$whe ;
$from = "";
$orden = ' s.fecha desc';

$busqueda = array();

$columnas = array(
		array("Fecha", "fec", "120", "center", "left" ),
		array("Operaci&oacute;n", "identificador", "120", "center", "left" ),
		array("Moneda", "moneda", "120", "center", "left" ),
		array("Comercio", "comer", "120", "center", "left" ),
		array("Pasarela", "pasar", "120", "center", "left" ),
		array("Empresa", "emp", "120", "center", "left" ),
		array("Motivo", "motivo", "", "center", "left" ));

$ancho = 1100;

echo "<div style='float:left; width:100%' ><table class='total1' width=\"$ancho\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
<tr>
<td><span style='float:right' class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='"._REPORTE_CSV."' title='"._REPORTE_CSV."'></span></td>
		</tr>
	</table></div>";

// echo $vista.$where." order by ".$orden;
tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );


?>

<script type="text/javascript">
// $(document).ready(function(){
	$("[id^=fec]").val('<?php echo $fec;?>');
	// $(function(){
	// 	$( ".fecc" ).datepicker({dateFormat: "dd/mm/yy"});
	// });

// });

	$("#comb").change(function(){
		$("#buca").val('1');
		$('form:first').submit();
	});

	// $("[id^=fec]").click(function(){if ($(this).val() == 'dd/mm/yyyy') $(this).val('');});
	// $("[id^=fec]").blur(function(){if ($(this).val() == '') $(this).val('dd/mm/yyyy');});

$("#usus").change(function() {
	$.post('componente/core/ejec.php',{
		fun: 'econ',
		usr: $("#usus :selected").val()
	},function(data){
		var datos = eval('(' + data + ')');
		if (datos.cont.length > 0) {
			$("#nomb").val(datos.cont[0]);
			$("#email").val(datos.cont[1]);
		}
	});
});

function verifica(){
	if ($("#nomb").val().lenght > 0) {
		
	}
}
</script>