<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$ent = new entrada;
$corCreo = new correo();

$d = $_REQUEST;
$id = $ent->isEntero($d['tf'], 12);
$idtr = $ent->isEntero($d['idtr'], 12);
$devol = $ent->isNumero($d['devol'], 12);

//Llega la operación a reclamar
if ($idtr) {
	// var_dump($d);

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
	
	$query = "update tbl_transacciones set solRec = '1' where idtransaccion = '$idtr'";
	// echo "<br>$query";
	$temp->query($query);

	$tira = explode('/', $d['fecha1']);
	$encoge = explode('/', $d['fecha2']);
	$fecha1 = mktime(17, 0, 0, $tira[1], $tira[0], $tira[2]);
	$fecha2 = mktime(17, 0, 0, $encoge[1], $encoge[0], $encoge[2]);
	$query = "insert into tbl_reclamaciones (idtransaccion, impReclam, fechaLim, fechaNot, motivo, documentos) values ('$idtr', '".($devol * 100)."', '$fecha1', '$fecha2', '".stripslashes($d['observ'])."', '".stripslashes($d['docu'])."')";
	// echo "<br>$query";
	$temp->query($query);
	$edit = $temp->last_insert_id();

	$q = "select c.nombre, c.id, from_unixtime(t.fecha, '%d/%m/%Y') fec, (t.valor_inicial/100) val, m.moneda, t.codigo from tbl_comercio c, tbl_transacciones t, tbl_moneda m where t.idcomercio = c.idcomercio and t.moneda = m.idmoneda and t.idtransaccion = '$idtr'";
	// echo "<br>$q";
	$temp->query($q);
	$com = $temp->f('nombre');
	$idc = $temp->f('id');
	$fec = $temp->f('fec');
	$val = $temp->f('val');
	$mon = $temp->f('moneda');
	$cod = $temp->f('codigo');

	// $cuerpo = "Estimado cliente:<br><br>
	// Les adjuntamos documento con petición de información relativa a una reclamación recibida de un pago realizado a su comercio.<br><br>
	// Necesitamos revisen lo indicado en el mismo y respondan en los términos especificados para ello.<br><br>
	// Para responder, acceda a través de la opción de <a href='../docs.php?idtr=$idtr' style='font-style: italic; '>Gestión de Reclamaciones</a> en nuestra plataforma.<br><br>
	// Por favor, no responda a este mensaje.<br><br>

	// Atentamente,<br><br>
	// Atención a Clientes<br>
	// <span style='font-weight:bold'>Bidaiondo S.L.</span><br>
	// <a href='mailto:atencionaclientes@bidaiondo.com'>atencionaclientes@bidaiondo.com</a><br>
	// Tel (53) 7 204 4424 <br><br>
	
	// <span style='color:#888; font-size:8px'>El contenido de este correo electrónico y sus anexos son estrictamente confidenciales, secretos y restringidos. La divulgación o el suministro, en todo o en parte, a cualquier tercero, no podrá ser realizada sin el previo, expreso y escrito consentimiento de BIDAIONDO S.L..   Las opiniones contenidas en este mensaje y en los archivos adjuntos, pertenecen exclusivamente a su remitente y no representan la opinión de BIDAIONDO S.L.  , salvo que se diga expresamente y el remitente esté autorizado para ello BIDAIONDO S.L. advierte expresamente que el envío de correos electrónicos a través de Internet no garantiza ni la confidencialidad de los mensajes, ni su integridad y correcta recepción, por lo que BIDAIONDO S.L., no asume responsabilidad alguna por dichas circunstancias.
	// En caso que no sea el destinatario y haya recibido este mensaje por error, agradecemos lo comunique inmediatamente al remitente sin difundir, almacenar o copiar su contenido.<br>
	// En cumplimiento con el RGPD (UE) 679/2016, le informamos de que sus datos personales son incluidos en ficheros particulares de BIDAIONDO S.L., con la finalidad de mejorar nuestros servicios y productos, así como mantenerle informado sobre estos y realizar comunicaciones comerciales. Para ejercitar los derechos previstos en la ley puede dirigirse mediante un correo electrónico a: <a href='mailto:atencionaclientes@bidaiondo.com'>atencionaclientes@bidaiondo.com</a></span>";

	$cuerpo = str_replace('{fecha1}', $d['fecha1'], 
				str_replace('{edit}', $edit, 
				str_replace('{fec2}', $d['fecha2'], 
				str_replace('{docu}', $d['docu'], 
				str_replace('{observ}', $d['observ'], 
				str_replace('{cod}', $cod, 
				str_replace('{mon}', $mon, 
				str_replace('{devol}', number_format($devol, 2), 
				str_replace('{val}', number_format($val, 2), 
				str_replace('{fec}', $fec, 
				str_replace('{com}', $com, 
				str_replace('{idtr}', $idtr, _CORREO_NOTRECLAM)
		)))))))))));

	$q = "select a.email from tbl_admin a, tbl_colAdminComer c where c.idAdmin = a.idadmin and c.idComerc = '$idc' and a.reclamaciones = 1 and a.activo = 'S'";
	// echo "<br>$query";
	$temp->query($q);
	$arrTo = $temp->loadResultArray();
	$i=0;
	foreach ($arrTo as $toto) {
		if (!strlen($corCreo->to)) $corCreo->to = $toto;
		else $corCreo->set_headers("Cc: " . $toto);
	}

	//envío de los correos de aviso
	// $corCreo->to = implode(';', );
	// $corCreo->to = "jtoirac@gmail.com";
	// echo "<br>"	. implode(';', $temp->loadResultArray());
	// echo "<br>" . $com . ' Notificación de Reclamación Operación ' . $idtr;
	$corCreo->todo(63, $com .' Notificación de Reclamación Operación '.$idtr, $cuerpo);

	//poniendo la noticia
	$mensaje = "<b>AVISO DE RECLAMACIÓN ¡!!</b> . .. Se ha actualizado una operación que se encuentra en proceso de reclamación.";
	$query = "insert into tbl_mensajes (mensaje, fechaInicio, fechaFin, fecha, idcomercio) values ('".$mensaje."', {$fecha2}, {$fecha1}, unix_timestamp(), '$idc')";
	$temp->query($query);

	echo "<script language=\"JavaScript\" type=\"text/javascript\">window.open('index.php?componente=comercio&pag=reporte','_self');</script>";
}

if ($id) {

	$q = "select id from tbl_reclamaciones where idtransaccion = '$id'";
	$temp->query($q);
	if ($temp->f('id') > 0 ) {
		echo "<script language=\"JavaScript\" type=\"text/javascript\">window.open('index.php?componente=comercio&pag=reclamaciones&cambiar=".$temp->f('id')."','_self');</script>";
		exit;
	}

	$arrayTo = array();
	$fecha1 = date('d/m/Y', time());
	
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
	$html->inTextb(_COMPRUEBA_TRANSACCION, $id, 'idtr', null, null, "readonly=true");
	$html->inTextb(_INICIO_VALOR, formatea_numero($val), 'vali', null, null, "readonly=true");
	$html->inTextb("Cantidad reclamada", formatea_numero($val), 'devol');
	$html->inFecha('Fecha Notificaci&oacute;n', $fecha1, 'fecha2', null, null, null, null, $ver);
	$html->inFecha('Fecha L&iacute;mite', $fecha1, 'fecha1', null, null, null, null, $ver);
	$html->inTexarea("Motivo de la reclamaci&oacute;n", null, 'observ', 7, null, null, null, 47);
	$html->inTexarea("Documentaci&oacute;n requerida", null, 'docu', 7, null, null, null, 47);
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
