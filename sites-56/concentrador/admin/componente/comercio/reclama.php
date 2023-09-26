<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$ent = new entrada;
$corCreo = new correo();

$d = $_REQUEST;
$id = $ent->isEntero($d['tf'], 12);
$idtr = $ent->isEntero($d['idtr'], 12);
$devol = $ent->isNumero($d['devol'], 12);

if ($idtr) {
	//var_dump($d);

	$query = "select valor, moneda from tbl_transacciones where idtransaccion = '$idtr'";
	$temp->query($query);
	$mon = $temp->f('moneda');
	$val = $temp->f('valor') - ($devol * 100);
	
	if ($mon == '840') $cambio = leeSetup('USD');
	elseif ($mon == '826') $cambio = leeSetup('GBP');
	elseif ($mon == '124') $cambio = leeSetup('CAD');
	elseif ($mon == '32') $cambio = leeSetup('ARS');
	elseif ($mon == '152') $cambio = leeSetup('CLP');
	elseif ($mon == '170') $cambio = leeSetup('COP');
	elseif ($mon == '356') $cambio = leeSetup('INR');
	elseif ($mon == '392') $cambio = leeSetup('JPY');
	elseif ($mon == '484') $cambio = leeSetup('MXN');
	elseif ($mon == '604') $cambio = leeSetup('PEN');
	elseif ($mon == '756') $cambio = leeSetup('CHF');
	elseif ($mon == '937') $cambio = leeSetup('VEF');
	elseif ($mon == '949') $cambio = leeSetup('TRY');
	elseif ($mon == '986') $cambio = leeSetup('BRL');
	else $cambio = 1;
	
	$query = "update tbl_transacciones set valor = $val, euroEquivDev = $devol / $cambio, fecha_mod = ".time(). " , estado = 'R', tasaDev = $cambio, solDev = 0 where idtransaccion = '$idtr'";
	$temp->query($query);

	$query = "update tbl_reserva set valor = $val, fechaCancel = ".time().", estado = 'R' where id_transaccion = '$idtr'";
	$temp->query($query);

	echo "<script language=\"JavaScript\" type=\"text/javascript\">window.open('index.php?componente=comercio&pag=reporte','_self');</script>";
}

if ($id) {
	$arrayTo = array();
	
	$q = "select (valor/100) val from tbl_transacciones where idtransaccion = '$id'";
	$temp->query($q);
	$val = $temp->f('val');
	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = "Reclamación de la operación";
	$html->tituloTarea = "&nbsp;";
	$html->anchoTabla = 600;
	$html->anchoCeldaI = $html->anchoCeldaD = 245;
	$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
		function verifica() {
			if (
					(checkField (document.forms[0].idtr, isInteger, 0))&&
					(checkField (document.forms[0].devol, isMoney, 0))
				) {
				var val1 = document.forms[0].comp.value * 1;
				var val2 = document.forms[0].devol.value * 1;
				//val2 = val2.substr(0,val2.indexOf('.'));
				if ((val1 * 1) >= (val2 * 1)) {
					if (confirm('Se va a proceder a poner esta operaci\u00f3n como reclamada, est\u00e1 de acuerdo?')) 
						return true;
					else return false;
				} else alert('El monto de la reclamaci\u00f3n tiene que ser igual o menor que el de la transacci\u00f3n');
			}
			return false;
		}
		</script>";
	$html->inHide($val, 'comp');
	$html->inTextb(_COMPRUEBA_TRANSACCION, $id, 'idtr');
	$html->inTextb(_INICIO_VALOR, formatea_numero($val), 'vali', null, null, "readonly=true");
	$html->inTextb("Cantidad reclamada", formatea_numero($val), 'devol');
	// if ($idpass != 37)
	// 	$html->inTexarea(_AVISO_OBSERVA, null, 'observ', 6, null, null, null, 17);
	// else {
	// 	$q = "select idtitanes id, descripcion nombre from tbl_aisRazonCancel order by idtitanes";
	// 	$html->inSelect(_AVISO_OBSERVA, 'observ', 2, $q);
	// }
	
	echo $html->salida($botones, $texto);
}
	
function muestraError ($textoCorreo) {
	global $correoMi, $corCreo;
	$corCreo->todo(9, 'Error subiendo Cancelación de Orden de Ais a Titanes', $textoCorreo."\n<br> ** ".$correoMi);
	// 	exit;
}
?>
