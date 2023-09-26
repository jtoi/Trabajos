<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$ent = new entrada;
$corCreo = new correo();

$d = $_REQUEST;
$id = $ent->isEntero($d['tf'], 12);
// echo "<br>Session= ".json_encode($_SESSION);
// echo "<br>D=".json_encode($_REQUEST);

// if (strlen($_SESSION['admin_nom']) == 0 || !isset($_SESSION['admin_nom'])) {
// 	echo "<script language='JavaScript'>window.open('index.php?componente=core&pag=logout', '_self')</script>";
// 	exit;
// }


if ($id) {
	$arrayTo = array();
	
	$q = "select t.estado, t.moneda idmoneda, c.nombre comercio, t.valor_inicial, m.moneda, t.identificador from tbl_transacciones t, tbl_comercio c, tbl_moneda m where m.idmoneda = t.moneda and t.idcomercio = c.idcomercio and t.idtransaccion = '$id'";
	// echo $q;
	$temp->query($q);
	$moneda = $temp->f('moneda');
	$identif = $temp->f('identificador');
	$idmoneda = $temp->f('idmoneda');
	$comercio = $temp->f('comercio');
	$valor1 = $temp->f('valor_inicial')/100;

	if ($temp->num_rows() == 0 && $temp->f('estado') != 'N') {
		echo "<script language='JavaScript'>alert('La operación $id no está como No Procesada regreso..');window.history.back();</script>";
	}

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = "Cambio de estado de operación a Aceptada";
	$html->tituloTarea = "&nbsp;";
	$html->anchoTabla = 600;
	$html->anchoCeldaI = $html->anchoCeldaD = 245;
	$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
		function verifica() {
			if (
					(checkField (document.forms[0].codigo, isAlphanumeric, 0))&&
					(checkField (document.forms[0].idtr, isInteger, 0))&&
					(checkField (document.forms[0].tasa, isMoney, 0))
				) {
					
				//procede(document.forms[0].codigo.value, document.forms[0].idtr.value, document.forms[0].tasa.value);
				window.open('index.php?componente=comercio&pag=reporte&tcode='+document.forms[0].codigo.value+'&tranid='+document.forms[0].idtr.value+'&tassa='+document.forms[0].tasa.value,'_self');
			}
			return false;
		}
		</script>";
	$html->inTextoL("Operación $id / $identif lanzada desde $comercio con un valor inicial de $valor1 $moneda");
	$html->inHide($id, 'idtr');
	$html->inTextb("Código del banco", '', 'codigo');

	if ($idmoneda == '978')
		$html->inHide("1", 'tasa');
	else 
		$html->inTextb("Tasa de cambio", '', 'tasa');
	
	// echo $html->salida("<input type='button' value='Enviar' onclick='verifica();'>", "", true);
	echo $html->salida();
}

error_log("sesioo=".json_encode($_SESSION));
	
function muestraError ($textoCorreo) {
	global $correoMi, $corCreo;
	// $corCreo->todo(9, 'Error subiendo Cancelación de Orden de Ais a Titanes', $textoCorreo."\n<br> ** ".$correoMi);
	// 	exit;
}
?>

<script language="JavaScript" type="text/javascript">
function procede(codigo, idtr, tasa){
	tasa = (0 + (1*tasa));
	// alert(codigo + ' ' + idtr + ' ' + tasa);

	$.post('componente/comercio/ejec.php', {
				idss: idtr,
				fun: 'actlpend',
				tasa: tasa,
				code: codigo
			}, function(data) {
				var datos = eval('(' + data + ')');
				if (datos.error.length > 0) alert(datos.error);
				if (datos.sale.length > 0) alert(datos.sale);
				window.open('index.php?componente=comercio&pag=reporte&nombre='+idtr, '_self');
			});
}
</script>
