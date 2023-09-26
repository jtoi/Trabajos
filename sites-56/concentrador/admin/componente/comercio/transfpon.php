<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Pone las transferencias que le hacemos a los comercios escogiendo los cierres que ampara
global $temp;
$html = new tablaHTML;
global $send_m;

$d = $_REQUEST;
//print_r($_SESSION);
print_r($_REQUEST);

if ($d['inserta'] == 0 && $d['valo'] > 0) {
	$fes = to_unix($d['fecTrans'].' 23:59:59');
	$q = "insert into tbl_amfTransf (idcomercio, nombre, valor, fechaEnt, fecha)
			values (".$d['comerc'].", '".$d['transs']."', ".$d['valo'].", ".$fes.", unix_timestamp()) ";
	$temp->query($q);
	$ident = $temp->last_insert_id();
	
	foreach ($d['ciere'] as $item) {
		$q = "insert into tbl_colAmfTransfCierre (idcierre, idtransf)
				values ($item, $ident)";
		$temp->query($q);
	}
}

/* Actualiza la transacción */
if ($d['inserta'] && $d['valo'] != null) {
	
}


/*
 * Preparación de los datos por defecto a mostrar en el Buscar
 */
//	Comercio
$comer = $_SESSION['idcomStr'];
if ($d['comercio']) $comercId = $d['comercio'];
else  $comercId = $comer;

if(is_array($comercId)) $comercId = implode('\', \'', $comercId);

//	Fechas y Horas
if ($d['buscar']) {
//		echo $d['buscar'];
	$tira = explode('and', $d['buscar']);
	$fecha1 = date('d/m/Y', substr($tira[3], strlen($tira[3])-1));
// 	$fecha2 = date('d/m/Y', substr($tira[4], 0, 11));
} else {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, 2008));
// 	$fecha2 = date('d/m/Y', time());
	if ($d['fecha1']) $fecha1 = $d['fecha1'];
// 	if ($d['fecha2']) $fecha2 = $d['fecha2'];
}

$mes1 = explode('/', $fecha1);
$mes1 = 1*$mes1[1];
$mes2 = explode('/', $fecha2);
$mes2 = 1*$mes2[1];

$cierre = '';
if($d['cierre']) $cierre = $d['cierre'];

$d['tipo']? $esta = $d['tipo']:$esta = "V', 'D', 'S', 'Q', 'M";
/* Construye el formulario de Buscar */
?>

<script type="text/javascript" src="../js/jquery.myfun_c00cc3a629e84ef270c28b0d705a1ce1.js"></script>
<script type="text/javascript">
function verifica() {
	if ($("#cr").val().length > 3) {
		if($("#tr").val() > 100) return true;
		else return alert('El valor de la transferencia no es correcto');
	} else alert('El identificador de la transferencia no es correcto');
	return true;
}

</script>
<style>
.centro1 span{font-size:12px;font-weight:bold;line-height:23px;}
.alerti{top:160px;position:relative;left:-17px;}
</style>
<div class="alerti"></div>
<?php 
$html->idio = $_SESSION['idioma'];
$html->tituloPag = 'Transferencias AMF';
$html->tituloTarea = _REPORTE_TASK;
$html->hide = true;
$html->anchoTabla = 500;
$html->anchoCeldaI = $html->anchoCeldaD = 245;

$html->inTextb('Transferencia', '', 'cierre');
if (strpos($_SESSION['idcomStr'], ',')) {
	$query = "select id id, nombre from tbl_comercio where activo = 'S' and id in (".$_SESSION['idcomStr'].") order by nombre";
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query, $comercId, null, null, "multiple size='5'");
}
else $html->inHide ($comercId, 'comercio');
// $html->inFecha("Fecha", $fecha1, 'fecha1');
// $html->inFecha(_CIERRE_HASTA, $fecha2, 'fecha2');
/* Termina el formulario de buscar */

/* Formulario insertar / editar */
$pag = 0;
$fec = 'dd/mm/yyyy';
if ($d['inserta'] && $d['valo'] == null) {
	$pag = $d['inserta'];
	$q = "select idcomercio, nombre, from_unixtime(fechaEnt,'%d/%m/%Y') fec from tbl_amfTransf where id = $pag";
	$temp->query($q);

	$nom = $temp->f('nombre');
	$fec = $temp->f('fec');
	$comercId = $temp->f('idcomercio');
	echo $q;
}
$html->inHide($pag, 'inserta');
$html->inTextoL('Insertar / Editar Transferencia');
$html->inTextb('Identificación de la transferencia', $nom, 'transs');
$html->inTextb('Valor de la transferencia', $val, 'valo');

$query = "select id, nombre from tbl_comercio where activo = 'S' and id in (".$_SESSION['idcomStr'].") order by nombre";
$html->inSelect(_COMERCIO_TITULO, 'comerc', 2, $query, $comercId, null, null);
$html->inSelect("Cierres", 'ciere', 1, '', '', null, null, "multiple size='5'");

$html->inTextb('Fecha de realizada la Transferencia', $fec, 'fecTrans', null, null, null, 'Fecha en formato (dd/mm/yyyy)');

$html->inTextoL('', 'salCierre');

/* Termina Formulario insertar / editar */
echo $html->salida();

/* Tabla de transferencias */
$vista = "select r.nombre, r.valor, r.fechaEnt, c.nombre comercio from tbl_amfTransf r, tbl_comercio c ";

//	echo "NombreVal= $nombreVal<br>";

$where = "where r.idcomercio = c.id";
if ($d['cierre']) $where .= " and r.nombre like '%{$d['cierre']}%'";
else $where .= " and c.id in ($comercId)";
// if ($d['buscar'])
// 	$where .= " and r.fechaEnt between ".to_unix($fecha1." 00:00:00")." and ".(to_unix($fecha1." 23:59:59"))." ";

$orden = 'r.fecha desc, c.nombre';
//echo $where;

// $colEsp[] = array("e", _CIERRE_VCIERRE, "../images/edit.gif", _CIERRE_VCIERRE);
$colEsp[] = array("m", "Editar Transferencia", "css_trns", _TAREA_PAGADA);

$busqueda = array();
$columnas = array(
				array("Transferencia", "nombre", "", "center", "left" ),
				array("Valor", "valor", "90", "center", "left" ),
				array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left" ),
				array('Fecha de Entrada', "fechaEnt", "", "center", "center" )
			);


//	echo $query;
$sumaLib += $temp->f('totalLib');
$ancho = 800;
echo $vista.$where." order by ".$orden;
$querys = tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
//	echo $rec[0];

if (strlen($_REQUEST["orden"]) > 0) $orden = $_REQUEST["orden"];
else $orden = $orden;

?>
<script type="text/javascript" >
$(document).ready(function(){
	cierr();
	$("#comerc").change(function(){cierr();});
});

function cierr(){
	$("#ciere").get(0).options.length = 0;
	$.post('componente/comercio/ejec.php',{
		fun:'recCierre',
		com:$("#comerc option:selected").val()
	},function(data){
		var datos = eval('(' + data + ')');
		if (datos.cont) {
			if (datos.cont.length > 0) {
				var options = $("#ciere");
				$.each(datos.cont, function(index,vale) {
					options.append($("<option />").val(vale.idcierre).text(vale.cierre));
				});
			}
		}
	});
}
</script>