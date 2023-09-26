<?php defined('_VALID_ENTRADA') or die('Restricted access');

$html = new tablaHTML;
global $temp;
$ent = new entrada;
$corCreo = new correo();

/**
 *Covenio de estado de operaciones Preautorizadas Claudia 5/10/20

 * cuando no llega al banco - estado: P
 * cuando llega al banco y regresa ok: A
 * cuando llega al banco y deniega: D
 * cuando se libera (no se cobra): L
 * cuando se cobra: se queda con A y cambia de tipo de operación de A a P
 */

$d = $_REQUEST;
$id = $ent->isEntero($d['tf'], 12);


//	lee los datos de la Preautorizacion
$query = "select c.nombre comercio, t.* from tbl_comercio c, tbl_transacciones t where t.idcomercio = c.idcomercio and t.idtransaccion = '$id' and tipoOperacion = 'A'";

// echo "<br>".$query;
$temp->query($query);
$arrSal = $temp->loadAssocList();

if (count($arrSal) > 0) {
	$item = $arrSal[0];
	//        print_r($item);
	$id = $item['idtransaccion'];
	$code = $item['identificador'];
	$idComercio = $item['idcomercio'];
	$fecha = $item['fecha'];
	$fecha1 = $item['fecha_mod'];
	$moneda = $item['moneda'];
	$value = $item['valor_inicial'];
	$valor = $item['valor_inicial'] / 100;
	$valorEu = $item['valor_inicial'] / 100 / $item['tasa'];
	$comercio = $item['comercio'];
	$pais = $item['idpais'];
	//		$pasarela = $item['idPasarela'];
	$estado = $item['estado'];
	$tasa = $item['tasa'];
	$pasarela = $item['pasarela'];
	$activa = $item['activa'];
	//		$vista = $item['vista'];
	//		$enviada = $item['enviada'];
}
if ($tasa == '0.0000') $tasa = '';

$html->idio = $_SESSION['idioma'];
$html->tituloPag = "Preautorizo";
if ($_SESSION['grupo_rol'] <= 5 || $_SESSION['grupo_rol'] == 18) $html->tituloTarea = "Modificar Preautorizo";
else $html->tituloTarea = "&nbsp;";
$html->anchoTabla = 600;
$html->anchoCeldaI = $html->anchoCeldaD = 280;
$html->java = "
	<style type='text/css' media='screen'>
		#vista{color:blue;font-size:14px;font-weight:bold;}
		.uno{display:block;width:250px;position:relative;text-align:left;left:200px;height:20px;}
		.dos{display:block;width:120px;float:left;}
		.tres{font-weight:bold;width:91px!important;}
		.title_tarea33{margin-top:10px;}
		.derecha1{font-weight: bold;width: 93px !important;margin-left: 148px;}
		.izquierda1{width: 280px;padding-left: 45px;}
	</style>
	";
//echo "$idComercio, $code, $value, $moneda, A";
$html->inHide($idComercio, 'comercio');
$html->inHide($code, 'transaccion');
$html->inHide($moneda, 'moneda');
$html->inHide($estado, 'estado');
$html->inHide(convierte256($idComercio, $code, $value, $moneda, 'L'), 'firma0');
$html->inHide(convierte256($idComercio, $code, $value, $moneda, 'A'), 'firma1');
// if ($_SESSION['grupo_rol'] <= 1 || $_SESSION['grupo_rol'] == 18) {
$html->inTextoL("<span class='uno' ><span class='dos tres' >Id Operaci&oacute;n: </span><span class='dos' >" . $id . '</span></span>', '');
$html->inTextoL("<span class='uno' ><span class='dos tres' >Código: </span><span class='dos' >" . $code . '</span></span>', '');
if ($estado == 'E') {
	$html->inTextb('Importe a cobrar', formatea_numero($valor), 'importe');
} else {
	$html->inHide($value, 'importe');
	$html->inTextoL("<span class='uno' ><span class='dos tres' >" . _COMPRUEBA_IMPORTE . ": </span><span class='dos' >" . formatea_numero($valor) . '</span></span>', '');
}
$temp->query("select moneda from tbl_moneda where idmoneda = " . $moneda);
$html->inTextoL("<span class='uno' ><span class='dos tres' >" . _COMPRUEBA_MONEDA . ": </span><span class='dos' >" . $temp->f('moneda') . '</span></span>', '');
// $html->inTextoL("<span class='uno' ><span class='dos tres' >" . _AVISO_VALOREU . ": </span><span class='dos' >" . formatea_numero($valorEu) . '</span></span>', '');
// $html->inTextoL("<span class='uno' ><span class='dos tres' >" . _AVISO_TASA . ": </span><span class='dos' >" . formatea_numero($tasa) . '</span></span>', '');
$temp->query("select nombre from tbl_pasarela where idPasarela = $pasarela");
$html->inTextoL("<span class='uno' ><span class='dos tres' >" . _COMERCIO_PASARELA . ": </span><span class='dos' >" . $temp->f('nombre') . '</span></span>', '');
$temp->query("select nombre from tbl_comercio where idcomercio = $idComercio");
$html->inTextoL("<span class='uno' ><span class='dos tres' >" . _COMERCIO_TITULO . ": </span><span class='dos' >" . $temp->f('nombre') . '</span></span>', '');
switch ($estado) {
	case 'E':
		$estNom = "Preautorizada";
		break;

	case 'P':
		$estNom = "En Proceso";
		break;

	case 'N':
		$estNom = "No Procesada";
		break;

	case 'D':
		$estNom = "No Confirmada";
		break;

	case 'L':
		$estNom = "Cancelada";
		break;
}
$html->inTextoL("<span class='uno' ><span class='dos tres' >Estado: </span><span class='dos' >" . $estNom . '</span></span>', '');
$fecha = date('d/m/Y', $fecha);
$fecha1 = date('d/m/Y', $fecha1);
$html->inTextoL("<span class='uno' ><span class='dos tres' >Fecha: </span><span class='dos' >" . $fecha . '</span></span>', '');
$html->inTextoL("<span class='uno' ><span class='dos tres' >Fecha modificada: </span><span class='dos' >" . $fecha1 . '</span></span>', '');
$texto = '';

if ($estado == 'E') {
	$botones .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="volver" name="volver" onclick="window.history.back();" type="button" value="Volver" />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="libera" name="libera" type="button" value="Cancelar Preautorizo" />
				<!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="prorro" name="prorro" type="button" value="Prorrogar" />-->
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="cobrar" name="cobrar" type="button" value="Cobrar Preautorizo" />
	';
} else {
	$botones .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="volver" name="volver" onclick="window.history.back();" type="button" value="Volver" />
	';
}
echo $html->salida($botones, $texto);

// }


?>

<script type="text/javascript" src="../js/jquery.myfun_c00cc3a629e84ef270c28b0d705a1ce1.js"></script>
<script language="JavaScript" type="text/javascript">
	$(document).ready(function() {
		$('#libera').click(function() {
			envia(0);
		});
		$('#cobrar').click(function() {
			envia(1);
		});
	});

	function envia(opcion) {
		$(".title_tarea1").esperaDiv('muestra');
		var valor = $("#importe").val();
		var firma = $('#firma0').val();
		var variante = 'L';

		if (opcion == 1) {
			valor = $('#importe').val();
			firma = $('#firma1').val();
			variante = 'A';
		}

		if ($("#estado").val() == 'E') valor = valor*100;

		$.post('../../propre.php', {
			comercio: $('#comercio').val(),
			transaccion: $('#transaccion').val(),
			importe: valor,
			operacion: variante,
			moneda: $('#moneda').val(),
			firma: firma
		}, function(data) {
			var datos = eval('(' + data + ')');
			if (datos.estado == 'A' || datos.estado == 'L')
				alert('Operaci\u00F3n modificada satisfactoriamente.');
			else
				alert("La operaci\u00F3n ha devuelto un error: " + datos.error);
			$(".title_tarea1").esperaDiv('cierra');
			window.open("index.php?componente=comercio&pag=reporte&nombre=<?php echo $id; ?>", '_self');
		});
	}
</script>