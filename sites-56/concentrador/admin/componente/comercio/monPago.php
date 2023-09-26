<?php defined('_VALID_ENTRADA') or die('Restricted access');
$html = new tablaHTML;
global $temp;
$corCreo = new correo();

if (stripos(_ESTA_URL, 'localhost') > 0) {
	// $_POST['modifica'] = '159171392542';
	// $_POST['prefijo'] = '10'; 
	// $_POST['plantilla'] = 'sdfsdf';
	// $_POST['plantillaIng'] = 'sdfgsdfgsdf';
	// $_POST['nombre'] = 'Havanatursa.com';
	// $_POST['activo'] = 'S';
	// $_POST['actividad'] = 'D'; 
	// $_POST['historia'] = 'D-09/06/20 16:45'; 
	// $_POST['url'] = '';
	// $_POST['direcurl'] = '';
	// $_POST['lote'] = 0;
	// $_POST['s3d'] = 'N';
	// $_POST['empresa'] = array(1,2); 
	// $_POST['condiciones'] = 'Estas son las Condiciones de Pago que el cliente deberá aceptar para el pago online. Deberá sustiruir estas por las reales.'; 
	// $_POST['condicionesIng'] = 'Those are the Payment Conditions that the costumer must accept before make it. Change this one for the reals one';
	// $_POST['direccion'] = '';
	// $_POST['codTransf'] = ''; 
	// $_POST['formTransf'] = '';
	// $_POST['concTransf'] = ''; 
	// $_POST['minTransf'] = '3000';
	// $_POST['tpvTransf'] = '0';
	// $_POST['sms'] = '0';
	// $_POST['vende'] = 'S'; 
	// $_POST['convEuro'] = '0'; 
	// $_POST['camEur'] = '0'; 
	// $_POST['usdAmex'] = '1'; 
	// $_POST['cuccambio'] = '0'; 
	// $_POST['pasarela'] = array(12, 53, 68, 45);
	// $_POST['pasarela2'] = '12, 53, 68, 45, 67, 46, 23, 32, 41, 52, 1, 63, 50, 51, 58, 59, 60, 29, 31, 44, 37, 43, 64';
	// $_POST['tpvidord'] = ''; 
	// $_POST['pasaorden'] = ''; 
	// $_POST['pasTransf'] = '55';
	// $_POST['cierreA'] = '299';
	// $_POST['periodicidad'] = 'S'; 
	// $_POST['horain'] = '0';
	// $_POST['valmin'] = '1';
	// $_POST['valmax'] = '50000'; 
	// $_POST['cuota'] = '200';
	// $_POST['mensual'] = '20';
	// $_POST['usoTarjeta'] = '1'; 
	// $_POST['retro'] = '4.50';
	// $_POST['transf'] = '0.00';
	// $_POST['swift'] = '0';
	// $_POST['bancar'] = '0.25'; 
	// $_POST['bancarMin'] = '12';
	// $_POST['pago'] = '4.5';
	// $_POST['minCobro'] = '1'; 
	// $_POST['maxCobro'] = '1000000000';
	// $_POST['enviar'] = 'Enviar';
}

//global $temp;
$d = $_POST;
$fechaNow = time();
$incluye = "";
$sms = 0;
$sale = 1;


//echo "cod=".generaCodEmp();
if (_MOS_CONFIG_DEBUG) {
	var_dump($d);
	echo "<br>";
	// var_dump($_SESSION);
	echo "<br>pasMomento";
	var_dump($d['pm']);
	echo "<br>pasWeb";
	var_dump($d['pw']);
	echo "<br>";
}

if ($d['idcom'] && $_SESSION['grupo_rol'] <= 2) { //Modifica las monedas para los TPV del comercio dado
	$temp->query("delete from tbl_colComerPasaMon where idcomercio = '{$d['idcom']}'");
	for ($i=0; $i < count($d['monSel']); $i++) {
		$arrSal = explode('|',$d['monSel'][$i]);
		$temp->query("insert into tbl_colComerPasaMon (idcomercio, idpasarela, idmoneda, fecha) values ({$d['idcom']}, {$arrSal[0]}, {$arrSal[1]}, unix_timestamp())");
	}
}

if ($d['cambiar'] && $_SESSION['grupo_rol'] <= 2) { // Valores para modificar el art�culo seleccionado
	if ($d['cambiar']) $comercio = $d['cambiar'];
	$titulo_tarea = _TAREA_MODIFICAR . ' ' . _COMERCIO_TITULO;
	$temp->query("select id, nombre from tbl_comercio where idcomercio = '{$d['cambiar']}'");
	$nombre_form = $temp->f('nombre');
	$idcom = $temp->f('id');
}

//javascript
$javascript = "
	<script language=\"JavaScript\" type=\"text/javascript\"  charset=\"utf-8\">
	function verifica() {
		if (!sin3D()) return false;
		if ((checkField (document.forms[0].nombre, isAlphanumeric, ''))) {
			return true;
		}
		return false;
	}
	";
if (!$d['cambiar'] && $_SESSION['grupo_rol'] <= 2) $javascript .= "$(document).ready(function(){ $('textarea').attr('value', ''); });";
if ($alerta)
	$javascript .= "alert('$alerta');";
$javascript .= "</script>";

$html->java = $javascript;

$html->idio = $_SESSION['idioma'];
$html->tituloPag = "moneda de pago al comercio";
$html->tituloTarea = $titulo_tarea;
$html->anchoTabla = 510;
$html->hide = true;
if ($d['cambiar']) $html->hide = false;
$html->anchoCeldaI = 230;
$html->anchoCeldaD = 100;

/**Modificar */
if ($d['cambiar'] && $_SESSION['grupo_rol'] <= 2) {
	$html->inTexto(_MENU_ADMIN_COMERCIO, $nombre_form, 'nombre'); //nombre del comercio
	$html->inTexto(_COMERCIO_IDENTIF, $comercio); //identificador del comercio
	$html->inHide($idcom, 'idcom'); //identificador del comercio

	$temp->query("select p.idPasarela, p.nombre, o.idmoneda from tbl_pasarela p, tbl_colComerPasaMon o where p.idPasarela = o.idpasarela and o.idcomercio = $idcom order by p.nombre");
	$arrResult = $temp->loadRowList();

	for ($i= 0; $i < count($arrResult); $i++) {
		$query = "select concat({$arrResult[$i][0]}, '|', idmoneda) id, denominacion nombre from tbl_moneda where activo = 1 order by moneda";
		$html->inSelect($arrResult[$i][1], 'monSel[]', 2, $query, $arrResult[$i][0].'|'.$arrResult[$i][2]);
	}

	echo $html->salida();
}

/**Buscar */
if ($_SESSION['grupo_rol'] <= 2) {
	$html = new tablaHTML;

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = "";
	$html->tituloTarea = "Buscar";
	$html->hide = false;
	$html->anchoTabla = 500;
	$html->anchoCeldaI = 170;
	$html->anchoCeldaD = 320;

	$query = "select idcomercio id, nombre from tbl_comercio where  activo = 'S' order by nombre";
	//		echo $query;
	$html->inSelect(_COMERCIO_TITULO, 'cambiar', 2, $query,  str_replace(",", "', '", $comercId));
	echo $html->salida(null, null, true);
}

if (strpos($_SESSION['idcomStr'], ',')) {
	$vista = 'select idcomercio as id, a.nombre,
				a.fechaAlta, a.prefijo_trans prefijo,
				case a.estado when \'D\' then \'' . _COMERCIO_ACTIVITY_DES . '\' else \'' . _COMERCIO_ACTIVITY_PRO . '\' end as estado,
				a.fechaMovUltima,
				case activo when \'S\' then \'' . _FORM_YES . '\' else \'' . _FORM_NO . '\' end as activo, a.url
				from tbl_comercio a ';
	// 	if ($_SESSION['comercio'] == 'todos') $where = '';
	// 	else 
	$where = 'where id in (' . $_SESSION['idcomStr'] . ')';
	$orden = 'a.fechaMovUltima desc';

	$colEsp = array(array("e", _GRUPOS_EDIT_DATA, "css_edit", _TAREA_EDITAR));
	if ($_SESSION['comercio'] == 'todos') $colEsp[] = array("b", _GRUPOS_BORRA_DATA, "css_borra", _TAREA_BORRAR);

	$busqueda = array();

	$columnas = array(
		array(_COMERCIO_ID, "id", "", "center", "left"),
		array(_MENU_ADMIN_COMERCIO, "nombre", "", "center", "left"),
		array(_COMERCIO_ALTA, "fechaAlta", "", "center", "left"),
		array(_COMERCIO_ACTIVITY, "estado", "", "center", "left"),
		array(_COMERCIO_ACTIVO, "activo", "", "center", "left"),
		array(_COMERCIO_MOVIMIENTO, "fechaMovUltima", "", "center", "left")
	);

	$ancho = 900;

	echo "<div style='float:left; width:100%' ><table class='total1' width=\"$ancho\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
		<tr>
			<td><span style='float:right' class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"'
					src=\"../images/x-office-document.png\" alt='" . _REPORTE_CSV . "'
				title='" . _REPORTE_CSV . "'></span></td>
		</tr>
	</table></div>";
	// 	echo $vista.$where." order by ".$orden;
	tabla($ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas);
}
?>
<form name="exporta" action="impresion.php" method="POST">
	<input type="hidden" name="querys5" value="<?php echo $vista . $where . " order by " . $orden ?>">
</form>

<script type="text/javascript" charset="utf-8">
	// //función para poner el orden de las pasarelas con 3D del comercio
	// $("#pasarela2 option").click(function() {

	// 	var tpv = $(this);
	// 	var strTpv = tpv.text();
	// 	var valTpv = tpv.val();

	// 	//reviso todas las opciones del select para identificar las que no estén seleccionadas y borrarlas
	// 	$("#pasarela2 option").each(function() {
	// 		if ($(this).is(":selected")) {} else {
	// 			var opcionstr = $(this).text();
	// 			var opcionval = $(this).val();

	// 			$("#tpvidord").val($("#tpvidord").val().replace(opcionval + ',', ''));
	// 			$("#pasaorden").val($("#pasaorden").val().replace(opcionstr + '\n', ''));
	// 		}

	// 	});

	// 	if (tpv.is(':selected')) { //si la opción marcada está seleccionada 

	// 		if (strTpv.indexOf(' 3D') > 0) { //la opción seleccionada es un 3D

	// 			if ($("#tpvidord").val().indexOf(valTpv + ',') < 0) { //si el tpv no se hab&aacute;a seleccionado anteriormente lo agrego

	// 				$("#tpvidord").val($("#tpvidord").val() + valTpv + ',');
	// 				$("#pasaorden").val($("#pasaorden").val() + strTpv + '\n');

	// 			}

	// 		}

	// 	} else { //si la opción marcada NO está seleccionada 

	// 		if (strTpv.indexOf(' 3D') > 0) { //borro los tpv quitados

	// 			$("#tpvidord").val($("#tpvidord").val().replace(valTpv + ',', ''));
	// 			$("#pasaorden").val($("#pasaorden").val().replace(strTpv + '\n', ''));

	// 		}
	// 	}
	// });

	$(document).ready(function() {
		$("#webpasar").show();
		$("#mompasar").hide();
		$("#verwebpasar").addClass('botpasrsel');
		$("#vermompasar").removeClass('botpasrsel');

		$("#vermompasar").click(function() {
			$(this).addClass('botpasrsel');
			$("#verwebpasar").removeClass('botpasrsel');
			$("#webpasar").hide();
			$("#mompasar").show();
		});

		$("#verwebpasar").click(function() {
			$(this).addClass('botpasrsel');
			$("#vermompasar").removeClass('botpasrsel');
			$("#mompasar").hide();
			$("#webpasar").show();
		});
	});

	function sin3D() {
		return true;
	}

	//función para determinar la cantidad de pasarelas sin 3D que se ha puesto al comercio
	$("#pasarela2").blur(function() {

		if ($("#pasarela2 :selected").length == 0) {
			$('#divFormHid4').css('display', 'none');
			$('#divFormHid3').css('display', 'block');
			alert('El comercio debe tener al menos 1 TPV Seguro en "Pasarela para Pagos Diferidos y al Momento"');
			return false;
		}

		var valeNS = [];
		$('#pasarela2 :selected').each(function(i, selected) {
			valeNS[i] = $(selected).val();
		});

		$.post('componente/comercio/ejec.php', {
			fun: 'cheqNsec',
			cod: valeNS
		}, function(data) {
			var datos = eval('(' + data + ')');
			if (datos.error.length > 0) alert(datos.error);
			if (datos.cont) {
				if (datos.cont > 1) return true;
				else if (datos.cont == "0") return true;
				else {
					$('#divFormHid4').css('display', 'none');
					$('#divFormHid3').css('display', 'block');
					alert('El comercio debe tener al menos 2 TPV No Seguros en "Pasarela para Pagos Diferidos y al Momento"');
					return 'false';
				}
			}
		});

	});
</script>
<style>
	.botPasar {
		width: 400px;
		float: left;
		margin: 20px 0 20px 230px;
	}

	.botpasrsel {
		font-weight: bold;
		cursor: default !important;
	}

	.botonera {
		cursor: pointer;
		display: block;
		float: left;
	}

	#vermompasar {
		margin-left: 60px;
	}

	.linPas {
		float: left;
		padding-left: 30px;
	}

	.idPas {
		float: left;
		width: 191px;
		/* width: 254px; */
		text-align: left;
	}

	#pasSel {
		color: navy;
		font-weight: bold;
	}

	.divconpas {
		display: block;
		padding-bottom: 20px;
		float: left;
	}
</style>
