<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$temp = new ps_DB;
$html = new tablaHTML;
$corCreo = new correo();
global $send_m;
$timing = new Timing("Reporte");

$timing->start(); 

// var_dump($_FILES);

$d = $_REQUEST;
// if (_MOS_CONFIG_DEBUG) print_r($_SESSION);
//  print_r($d);
$id = $ent->isEntero($d['tf'], 12);

// if (_MOS_CONFIG_DEBUG) print_r($d);
//busca el o los comercios con los que se trabaja
$query = "select id from tbl_comercio where activo = 'S'";
$temp->query($query);
$comercios = implode("','", $temp->loadResultArray());
$comer = $_SESSION['idcomStr'];
if (isset ($d['comercio'])) {
	$comercId = $d['comercio'];
} else {
	if ($_SESSION['rol'] < 2) $comercId = $comercios;
	else if ($comer != 'todos') $comercId = $comer;
}


/**
 * Update de las reclamaciones
 */
if ($d['edit']) {

	$q = "select r.idtransaccion, c.id, c.nombre, (t.valor_inicial/100) val, m.moneda, t.codigo, t.idtransaccion, from_unixtime(t.fecha, '%d/%m/%Y') fec, r.estado, r.impReclam, from_unixtime(r.fechaNot, '%d/%m/%Y') 'fechaNot', from_unixtime(r.fechaLim, '%d/%m/%Y') 'fechaLim', from_unixtime(r.fechaCerr, '%d/%m/%Y') 'fechaCerr', from_unixtime(r.fechaBan, '%d/%m/%Y') 'fechaBan', from_unixtime(r.fechaRec, '%d/%m/%Y') 'fechaRec', r.motivo, r.documentos, r.subdoc from tbl_reclamaciones r, tbl_comercio c, tbl_transacciones t, tbl_moneda m where t.idcomercio = c.idcomercio and t.moneda = m.idmoneda and t.idtransaccion = r.idtransaccion and r.id = ".$d['edit'];
	$temp->query($q);

	if ($_SESSION['grupo_rol'] < 4 || $_SESSION['rol'] == 19) {

		$fechaNot = $temp->f('fechaNot');
		$fechaLim = $temp->f('fechaLim');
		$fechaCerr = $temp->f('fechaCerr');
		$fechaBan = $temp->f('fechaBan');
		$fechaRec = $temp->f('fechaRec');
		$estado = $temp->f('estado');
		$impReclam = $temp->f('impReclam');
		$motivo = $temp->f('motivo');
		$documentos = $temp->f('documentos');
		$com = $temp->f('nombre');
		$idc = $temp->f('id');
		$fec = $temp->f('fec');
		$val = $temp->f('val');
		$mon = $temp->f('moneda');
		$cod = $temp->f('codigo');
		$idtr = $temp->f('idtransaccion');
		$camb = $envio = $coma = $poneR = 0;

		$q = "update tbl_reclamaciones set ";
		if ($impReclam != ($d['importe']/100)) {
			($coma == 1) ? $q .= ", " : $q .= " ";
			$q .= "impReclam = ".($d['importe']/100);
			$coma = $camb = $poneR = 1;
		}
		
		if ($estado != $d['estado']){
			($coma == 1) ? $q .= ", " : $q .= " ";
			$q .= " estado = '".($d['estado']/100)."' ";
			$coma = 1;
			$envio = 2;
		}
		
		if ($fechaNot != $d['fecha1']){
			($coma == 1) ? $q .= ", " : $q .= " ";
			$fnot = explode("/", $d['fecha1']);
			$fnot = $fnot[2]."-".$fnot[1]."-".$fnot[0];
			$q .= " fechaNot = unix_timestamp('$fnot') ";
			$coma = 1;
		}

		if (isset( $d['fecha2']) && $fechaLim != $d['fecha2']){
			if ($d['fecha2'] != $fechaNot) {
				($coma == 1) ? $q .= ", " : $q .= " ";
				$fnot = explode("/", $d['fecha2']);
				$fnot = $fnot[2]."-".$fnot[1]."-".$fnot[0];
				$q .= " fechaLim = unix_timestamp('$fnot') ";
				$coma = 1;
			}
		}
		
		if (isset( $d['fecha3']) && $fechaCerr != $d['fecha3']){
			if ($d['fecha3'] != $fechaNot) {
				($coma == 1) ? $q .= ", " : $q .= " ";
				$fnot = explode("/", $d['fecha3']);
				$fnot = $fnot[2]."-".$fnot[1]."-".$fnot[0];
				$q .= " fechaCerr = unix_timestamp('$fnot') ";
				$coma = 1;
			}
		}
		
		if (isset( $d['fecha4']) && $fechaBan != $d['fecha4']){
			if ($d['fecha4'] != $fechaNot) {
				($coma == 1) ? $q .= ", " : $q .= " ";
				$fnot = explode("/", $d['fecha4']);
				$fnot = $fnot[2]."-".$fnot[1]."-".$fnot[0];
				$q .= " fechaBan = unix_timestamp('$fnot') ";
				$coma = 1;
			}
		}
		
		if (isset( $d['fecha5']) && $fechaRec != $d['fecha5']){
			if ($d['fecha5'] != $fechaNot) {
				($coma == 1) ? $q .= ", " : $q .= " ";
				$fnot = explode("/", $d['fecha5']);
				$fnot = $fnot[2]."-".$fnot[1]."-".$fnot[0];
				$q .= " fechaRec = unix_timestamp('$fnot') ";
				$coma = 1;
			}
		}

		if (isset($d['motivo']) && $d['motivo'] != $motivo) {
			($coma == 1) ? $q .= ", " : $q .= " ";
			$q .= " motivo = '".stripslashes($d['motivo'])."' ";
			$coma = 1;
		}

		if (isset($d['docu']) && $d['docu'] != $documentos) {
			($coma == 1) ? $q .= ", " : $q .= " ";
			$q .= " documentos = '".stripslashes($d['docu'])."', subdoc = 1 ";
			$coma = 1;
			$envio = 1;
		}
		$q .= " where id = ".$d['edit'];
		$temp->query($q); 

		//pone las operaciones en estado reclamada:
		if ($poneR == 1) {

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

			$query = "update tbl_transacciones set valor = ".($val - $impReclam).", euroEquivDev = $impReclam / $cambio, fecha_mod = ".time(). " , estado = 'R', tasaDev = $cambio, solDev = 0 where idtransaccion = '$idtr'";
			$temp->query($query);
		
			$query = "update tbl_reserva set valor = $val".($val - $impReclam).", fechaCancel = ".time().", estado = 'R' where id_transaccion = '$idtr'";
			$temp->query($query);
		}

		if ($envio == 1) {

			$cuerpo = _CORREO_NOTRECLAM;

			$asunto = $com .' Notificación de Reclamación Operación '.$idtr;

		} elseif ($envio == 2) {

			$cuerpo = _CORREO_CAMRECLAM;

			$asunto = $com ." - Aviso de reclamación: Se ha actualizado la reclamación de Operación ".$idtr;
		}

		$cuerpo = str_replace('{fecha1}', $d['fecha2'], 
				str_replace('{fec2}', $d['fecha1'], 
				str_replace('{docu}', $d['docu'], 
				str_replace('{observ}', $d['observ'], 
				str_replace('{cod}', $cod, 
				str_replace('{mon}', $mon, 
				str_replace('{devol}', number_format($d['importe'],2), 
				str_replace('{val}', number_format($val, 2), 
				str_replace('{fec}', $fec, 
				str_replace('{com}', $com, 
				str_replace('{idtr}', $idtr, $cuerpo)
		))))))))));

		$q = "select a.email from tbl_admin a, tbl_colAdminComer c where c.idAdmin = a.idadmin and c.idComerc = '$idc' and a.reclamaciones = 1 and a.activo = 'S'";
		$temp->query($q);

		// echo "<br><br>$cuerpo"; exit;
		$corCreo->to = implode(';', $temp->loadResultArray());
		$corCreo->todo(63, $asunto, $cuerpo);

	} else {
		if ($temp->num_rows() > 0 && count($_FILES)) {
			$nomFil = $d['edit']."_".date('y').date('m').date('d').date('H').date('i').date('s');

			$target_dir = "reclamaciones/".$d['edit']."/";
			if (!file_exists($target_dir)) {
				mkdir($target_dir, 0777, true);
			}
			$allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "pdf" => "application/pdf", "JPG" => "image/jpg", "JPEG" => "image/jpeg", "PDF" => "application/pdf");
			$target_file = $target_dir . $nomFil;
			$filename = $_FILES["fileup"]["name"];
			$filetype = $_FILES["fileup"]["type"];
			$filesize = $_FILES["fileup"]["size"];
			error_log("filename=$filename");
			error_log("filetype=$filetype");
			error_log("filesize=$filesize");

			$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
			error_log("ext=$ext");
			$nomb = $target_file.".".$ext;
			if(!array_key_exists($ext, $allowed)) echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">Error: Por favor entre un fichero de factura válido, puede ser en formato jpg, jpeg o pdf.</div>";
			else {
				if (move_uploaded_file($_FILES["fileup"]["tmp_name"], $nomb))  {
					$nomFil .= ".".$ext;
					$q = "insert into tbl_reclamFich (idreclama, idadmin, fichero) values ('".$d['edit']."', '".$_SESSION['id']."', '$nomFil')";
					$temp->query($q);

					echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">El fichero ". basename( $_FILES["fileToUpload"]["name"]). " ha subido correctamente y se ha renombrado a ".$nomFil."</div>";
				} else echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">Lo sentimos algo ha fallado en la subida, int&eacute;ntelo nuevamente</div>";
			}
		}
	}
}


/**
 * Editar las reclamaciones
 */
if ($d['cambiar'] || $d['edit']) {
	if ($d['edit']) $d['cambiar'] = $d['edit'];
	
	$q = "select r.idtransaccion, t.codigo, t.identificador, c.nombre comercio, b.banco, c.id comid, t.idcomercio, m.moneda, r.estado, formateaO(r.impReclam/100, 2, ".$_SESSION['id'].")impR, r.fechaNot, r.fechaLim, r.fechaCerr, r.fechaBan, r.fechaRec, r.motivo, r.documentos from tbl_reclamaciones r, tbl_transacciones t, tbl_comercio c, tbl_pasarela p, tbl_bancos b, tbl_moneda m where t.moneda = m.idmoneda and t.pasarela = p.idPasarela and p.idbanco = b.id and t.idcomercio = c.idcomercio and t.idtransaccion = r.idtransaccion and r.id = ".$d['cambiar'];
	// echo "<br><br>$q<br><br>";
	$temp->query($q);
	$arrRec = $temp->loadAssocList();
	$arrRec = $arrRec[0];

	// print_r($arrRec);

	$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
				function verifica() {
					return true;
				}
				</script>
				<style> #usrTr{width:250px;} #div_tipofecha .derecha1{width:250px !important;} #div_tipofecha .izquierda1{width:340px !important;} #div_fecha2{display:none;} #div_fecha3{display:none;} #div_fecha4{display:none;} #div_fecha5{display:none;}</style>";

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = 'Gesti&oacute;n de Reclamaciones';
	$html->tituloTarea = 'Editar Reclamación';
	$html->anchoTabla = 600;
	$html->anchoCeldaI = 170; $html->anchoCeldaD = 420;

	$html->inHide($d['cambiar'], 'edit');
	$html->inHide($arrRec['idcomercio'], 'idcomercio');
	$html->inTextb(_REPORTE_IDENTIFTRANS, $arrRec['idtransaccion'], 'trans', '', '', 'readonly');
	$html->inTextb(_REPORTE_REF_COMERCIO, $arrRec['identificador'], 'cod', '', '', 'readonly');
	$html->inTextb(_REPORTE_REF_BBVA, $arrRec['codigo'], 'codBanc', '', '', 'readonly');


	if ($_SESSION['grupo_rol'] < 4 || $_SESSION['rol'] == 19) {
		$html->inTextb(_COMERCIO_TITULO, $arrRec['comercio'], '', '', '', 'readonly size="50"');
		$html->inTextb('Banco', $arrRec['banco'], 'banco', '', '', 'readonly');
		$html->inTextb('Importe Reclamado', $arrRec['impR'], 'importe');
	} else 
		$html->inTextb('Importe Reclamado', $arrRec['impR'], 'importe', '', '', 'readonly');

	$html->inTextb(_COMERCIO_MONEDA, $arrRec['moneda'], 'monedas', '', '', 'readonly');
	if ($_SESSION['grupo_rol'] < 4 || $_SESSION['rol'] == 19) {
		$estadoArr = array(
			array('P', 'Por Responder'),
			array('S', 'Cerrado sin Respuesta'),
			array('C', 'Cerrado con Respuesta'),
			array('B', 'Respuesta al Banco'),
			array('R', _REPORTE_RECLAMADA),
		);
		$html->inSelect('Estado de la Reclamaci&oacute;n', 'estado', 3, $estadoArr, $arrRec['estado']);
	} else {
		switch ($arrRec['estado']) {
			case 'P':
				$est = 'Por Responder';
			break;
			case 'S':
				$est = 'Cerrado sin Respuesta';
			break;
			case 'C':
				$est = 'Cerrado con Respuesta';
			break;
			case 'B':
				$est = 'Respuesta al Banco';
			break;
			case 'R':
				$est = _REPORTE_RECLAMADA;
			break;
			
		}
		$html->inTextb('Estado de la Reclamaci&oacute;n', $est, '', '', '', 'readonly size="50"');
	}

	$fechaNot = date('d/m/Y', $arrRec['fechaNot']);
	if ($_SESSION['grupo_rol'] < 4 || $_SESSION['rol'] == 19) {
		$pase = '';
		$html->inFecha('Fecha Notificaci&oacute;n', $fechaNot, 'fecha1');

		if ($arrRec['fechaLim'] == 0) {
			$fechaLim = $fechaNot;
			$html->inTextb('Fecha Límite', $arrRec['fechaLim'], 'fakfecha2', '', '', '', '', 'fechaText');
			$html->inFecha('Fecha Límite', $fechaLim, 'fecha2');
		} else {
			$fechaLim = date('d/m/Y', $arrRec['fechaLim']);
			$html->inFecha('Fecha Límite', $fechaLim, 'fecha2');
			$pase .= "fecha2,";
		}

		if ($arrRec['fechaCerr'] == 0) {
			$fechaCerr = $fechaNot;
			$html->inTextb('Fecha Cerrada', $arrRec['fechaCerr'], 'fakfecha3', '', '', '', '', 'fechaText');
			$html->inFecha('Fecha Cerrada', $fechaCerr, 'fecha3');
		} else {
			$fechaCerr = date('d/m/Y', $arrRec['fechaCerr']);
			$html->inFecha('Fecha Cerrada', $fechaCerr, 'fecha3');
			$pase .= "fecha3,";
		}

		if ($arrRec['fechaBan'] == 0) {
			$fechaBan = $fechaNot;
			$html->inTextb('Fecha Env&iacute;o al Banco', $arrRec['fechaBan'], 'fakfecha4', '', '', '', '', 'fechaText');
			$html->inFecha('Fecha Env&iacute;o al Banco', $fechaBan, 'fecha4');
		} else {
			$fechaBan = date('d/m/Y', $arrRec['fechaBan']);
			$html->inFecha('Fecha Env&iacute;o al Banco', $fechaBan, 'fecha4');
			$pase .= "fecha4,";
		}
		
		if ($arrRec['fechaRec'] == 0) {
			$fechaRec = $fechaNot;
			$html->inTextb('Fecha Reclamada', $arrRec['fechaRec'], 'fakfecha5', '', '', '', '', 'fechaText');
			$html->inFecha('Fecha Reclamada', $fechaRec, 'fecha5');
		} else {
			$fechaRec = date('d/m/Y', $arrRec['fechaRec']);
			$html->inFecha('Fecha Reclamada', $fechaRec, 'fecha5');
			$pase .= "fecha5,";
		}

		$ver = '';
	} else {
		($arrRec['fechaLim'] == 0)? $fechaLim = '-' : $fechaLim = date('d/m/Y', $arrRec['fechaLim']);
		($arrRec['fechaCerr'] == 0)? $fechaCerr = '-' : $fechaCerr = date('d/m/Y', $arrRec['fechaCerr']);
		($arrRec['fechaBan'] == 0)? $fechaBan = '-' : $fechaBan = date('d/m/Y', $arrRec['fechaBan']);
		($arrRec['fechaRec'] == 0)? $fechaRec = '-' : $fechaRec = date('d/m/Y', $arrRec['fechaRec']);
		$ver = 'readonly';
		$html->inTextb('Fecha Notificaci&oacute;n', $fechaNot, 'fakfecha1', '', '', $ver);
		$html->inTextb('Fecha Límite', $fechaLim, 'fakfecha2', '', '', $ver);
		$html->inTextb('Fecha Cerrada', $fechaCerr, 'fakfecha3', '', '',  $ver);
		$html->inTextb('Fecha Env&iacute;o al Banco', $fechaBan, 'fakfecha4', '', '', $ver);
		$html->inTextb('Fecha Reclamada', $fechaRec, 'fakfecha5', '', '', $ver);
	}

	$html->inTexarea("Motivo de la reclamaci&oacute;n",$arrRec['motivo'], 'motivo', 7, null, null, $ver, 47);
	$html->inTexarea("Documentaci&oacute;n requerida", $arrRec['documentos'], 'docu', 7, null, null, $ver, 47);

	if (!$_SESSION['grupo_rol'] < 4 && $_SESSION['rol'] != 19) {
	// 	$html->inTextoL('Haga clic <a href="index.php?componente=comercio&pag=rdocs&r='.$d['cambiar'].'">aqu&iacute;</a> para subir documentaci&oacute;n');
	// } else {
		$html->inHide($d['cambiar'], 'file');
		$html->inFile("Documentaci&oacute;n", "fileup");
	}

	$q = "select fichero from tbl_reclamFich where idreclama = ".$d['cambiar'];
	$temp->query($q);
	$arrFic = $temp->loadResultArray();

	if (count($arrFic)) {
		$html->inTextoL("Visualizar o descargar los ficheros de esta Reclamaci&oacute;n");
		$con = '';
		for ($i=0; $i<count($arrFic); $i++) {
			$con .= "<a href='reclamaciones/".$d['cambiar']."/".$arrFic[$i]."' target='_blank'>".$arrFic[$i]."</a><br>";
		}
		$html->inTextoL("$con");
	}

	echo $html->salida();
	
	$arrPase = explode(",", $pase);
	
	$sale = "<script language=\"JavaScript\" type=\"text/javascript\">";
	for ($i=0; $i<count($arrPase); $i++){
		if (strlen($arrPase[$i]) > 0) {
			$sale .= "$('#div_".$arrPase[$i]."').show();";
		}
	}
	$sale .= "$('.fechaText').click(function(){
		var elem = $(this).attr('id');
		var corto = elem.substring(3,100);
		$('#div_'+elem).hide();
		$('#div_'+corto).show().focus();
	})";
	echo $sale . "</script>";

} else {
	// Update
	if ($d['edit']) {
		$q = "update tbl_reclamaciones set ";
	}

	//	Buscar
	if ($d['busc']) {
		$whe = '';
		if (strlen($d['trans']) == 12) {
			$trans = $d['trans'];
			$whe .= " and t.idtransaccion like '%$trans%' ";
		} elseif (strlen($d['cod']) < 20 && strlen($d['cod']) > 2) {
			$cod = $d['cod'];
			$whe .= " and t.identificador like '%$cod%' ";
		} elseif (strlen($d['codBanc']) < 20 && strlen($d['codBanc']) > 2) {
			$codBanc = $d['codBanc'];
			$whe .= " and t.codigo like '%$codBanc%' ";
		} else {
		
			if (count($d['comercio']) > 0) {
				$strco = implode(",",$d['comercio']);
				$whe .= " and t.idcomercio in ('$strco') ";
			}
			
			if (count($d['banco']) > 0) {
				$strba = implode(",",$d['banco']);
				$whe .= " and p.idbanco in ('$strba') ";
			}
			
			if (strlen($d['monedas']) > 0) {
				$strmo = $d['monedas'];
				$whe .= " and t.moneda in ('$strmo') ";
			}
			
			if (strlen($d['estado']) > 0) {
				$stres = $d['estado'];
				$whe .= " and r.estado in ('$stres') ";
			}

			// $html->inRadio("Fecha Notificaci&oacute;n", 'N', 'tipofecha', '','');
			// $html->inRadio("Fecha Límite", 'L', 'tipofecha', '','');
			// $html->inRadio("Fecha Cerrada", 'C', 'tipofecha', '','');
			// $html->inRadio("Fecha Envío al Banco", 'B', 'tipofecha', '','');
			// $html->inRadio("Fecha Reclamada", 'R', 'tipofecha', '','');
			// fechaNot	fechaLim	fechaCerr	fechaBan	
			if ($d['tipofecha'] != 'Q' ) {
				$fecha1 = $d['fecha1'];
				$arrF = explode('/',$d['fecha1']);
				$tipofecha = $d['tipofecha'];
				switch ($tipofecha) {
					case 'R':
						$whe .= " and fechaRec between ('".strtotime($arrF[1].'/'.$arrF[0].'/'.$arrF[2].' 00:00:00')."') and ('".strtotime($arrF[1].'/'.$arrF[0].'/'.$arrF[2].' 23:59:59')."')";
					break;
					case 'B':
						$whe .= " and fechaBan between ('".strtotime($arrF[1].'/'.$arrF[0].'/'.$arrF[2].' 00:00:00')."') and ('".strtotime($arrF[1].'/'.$arrF[0].'/'.$arrF[2].' 23:59:59')."')";
					break;
					case 'C':
						$whe .= " and fechaCerr between ('".strtotime($arrF[1].'/'.$arrF[0].'/'.$arrF[2].' 00:00:00')."') and ('".strtotime($arrF[1].'/'.$arrF[0].'/'.$arrF[2].' 23:59:59')."')";
					break;
					case 'L':
						$whe .= " and fechaLim between ('".strtotime($arrF[1].'/'.$arrF[0].'/'.$arrF[2].' 00:00:00')."') and ('".strtotime($arrF[1].'/'.$arrF[0].'/'.$arrF[2].' 23:59:59')."')";
					break;
					case 'N':
						$whe .= " and fechaNot between ('".strtotime($arrF[1].'/'.$arrF[0].'/'.$arrF[2].' 00:00:00')."') and ('".strtotime($arrF[1].'/'.$arrF[0].'/'.$arrF[2].' 23:59:59')."')";
					break;
				}
			}
		}
	}


	/*
	* Preparación de los datos por defecto a mostrar en el Buscar
	*/

	if(is_array($comercId)) $comercId = implode(',', $comercId);
	$comercId = str_replace("'", "", trim($comercId, ','));

	$modoVal = 'P';
	$nombreVal = '';

	if (!isset($fecha1)) $fecha1 = date('d/m/Y');
	$usrTr = ',';
	if (!isset($tipofecha)) $tipofecha = 'Q';

	$query = "select id from tbl_tarjetas order by nombre";
	$temp->query($query);
	$tarjetas = implode("','", $temp->loadResultArray());
	if ($d['tarjeta']) $tarjetas = $d['tarjeta'];

	$d['monedas']? $monedaid = $d['monedas']:$monedaid = "978', '124', '840', '826";
	if ($monedaid == "978', '124', '840', '826") $monedaid = "978', '124', '','840', '826";

	/*
	* Construye el formulario de Buscar
	*/
	$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
					function verifica() {
						return true;
					}
					</script>
					<style> #usrTr{width:250px;} #div_tipofecha .derecha1{width:250px !important;} #div_tipofecha .izquierda1{width:340px !important;}</style>";

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = 'Gesti&oacute;n de Reclamaciones';
	$html->tituloTarea = _REPORTE_TASK;
	$html->hide = true;
	$html->anchoTabla = 600;
	$html->anchoCeldaI = 170; $html->anchoCeldaD = 420;

	$html->inHide(true, 'busc');
	$html->inTextb(_REPORTE_IDENTIFTRANS, $trans, 'trans');
	$html->inTextb(_REPORTE_REF_COMERCIO, $cod, 'cod');
	$html->inTextb(_REPORTE_REF_BBVA, $codBanc, 'codBanc');

	if ($_SESSION['grupo_rol'] < 4 || $_SESSION['rol'] == 19) {
		$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
		$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query,  str_replace(",", "', '", $strco), null, null, "multiple size='5'");

		$query = "select id, banco nombre from tbl_bancos where id not in (26,27) order by banco";
		$html->inSelect('Banco', 'banco', 5, $query,  str_replace(",", "', '", $strba), null, null, "multiple size='5'");
	}

	$query = "select idmoneda id, moneda nombre from tbl_moneda";
	$html->inSelect(_COMERCIO_MONEDA, 'monedas', 5, $query, $strmo);

	$estadoArr = array(
		array("P','S','C','B','R", _REPORTE_TODOS),
		array('P', 'Por Responder'),
		array('S', 'Cerrado sin Respuesta'),
		array('C', 'Cerrado con Respuesta'),
		array('B', 'Respuesta al Banco'),
		array('R', _REPORTE_RECLAMADA),
	);
	$html->inSelect('Estado de la Reclamaci&oacute;n', 'estado', 3, $estadoArr, $stres);

	$html->inTextoL("Seleccione la fecha a buscar");
	$html->inRadio("Fecha Notificaci&oacute;n", 'N', 'tipofecha', '',$tipofecha);
	$html->inRadio("Fecha Límite", 'L', 'tipofecha', '',$tipofecha);
	$html->inRadio("Fecha Cerrada", 'C', 'tipofecha', '',$tipofecha);
	$html->inRadio("Fecha Envío al Banco", 'B', 'tipofecha', '',$tipofecha);
	$html->inRadio("Fecha Reclamada", 'R', 'tipofecha', '',$tipofecha);
	$html->inRadio("Cualquier fecha", 'Q', 'tipofecha', '',$tipofecha);
	$html->inFecha('Fecha', $fecha1, 'fecha1', null, null, null, null, $ver);

	echo $html->salida();
	/*
	* Termina el formulario de buscar
	*/
}

$tabView = $pasarView = '';
if ($_SESSION['grupo_rol'] > 3 && $_SESSION['rol'] != 19) { 
	$pasarView = "case (select secure from tbl_pasarela where idPasarela = t.pasarela and tipo = 'P') when 1 then 'Segura' 
					when 0 then 'NO Segura' else 'Transferencia' end pasarelaN";
} else {
	$pasarView = "p.nombre pasarelaN, e.nombre 'Empresa'";
}
// echo $whe;
$where = " where c.idcomercio = t.idcomercio and r.idtransaccion = t.idtransaccion and t.moneda = m.idmoneda and p.idPasarela = t.pasarela and (t.solRec = 1 or t.estado = 'R') and p.idbanco = b.id and m.idmoneda = t.moneda and c.id in ($comercId) $whe";
$from = " from tbl_reclamaciones r, tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_pasarela p, tbl_bancos b ";

$orden = 'r.fechaNot desc, comercio';

$colEsp[] = array("e", _GRUPOS_EDIT_DATA, "css_edit", _TAREA_EDITAR);
$busqueda = array();
	
    
// if ($_SESSION['rol'] != 17) {
// 	echo "<div style='float:left; width:100%' ><table class='total1' width=\"100%\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
// 	<tr>
// 		<td><div class='total2'></div></td>
// 			<td width='140'><span class='css_document-print' onclick='document.imprime.submit()' onmouseover='this.style.cursor=\"pointer\"' alt=\"".
// 					_REPORTE_PRINT."\" title=\""._REPORTE_PRINT."\"></span>&nbsp;&nbsp;&nbsp;
// 				<span class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='".
// 					_REPORTE_CSV."1' title='"._REPORTE_CSV."1'></span></td>
// 		</tr>
// 	</table></div>";
	
// }

$vista = "select r.id, r.idtransaccion, c.nombre 'comercio', t.identificador, t.codigo, t.fecha fechaopr, (t.valor_inicial/100) 'importe{val}', (r.impReclam/100) 'importRec{val}', b.banco, m.moneda, case r.fechaLim when 0 or '' or null then 0 else r.fechaLim end 'fechalimi', r.fechaNot, r.fechaCerr, r.fechaBan, r.fechaRec ";

$ancho = 1400;
//columnas a mostrar
$columnas = array(
			array('', "color{col}", "1", "center", "center" ),
			array('Id', "id", "30", "center", "left" ),
			array('Transacci&oacute;n', "idtransaccion", "50", "center", "left" ));
if ($_SESSION['rol'] < 2 || strpos($comer, ',')) {
	array_push($columnas, array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left" ));
}
array_push($columnas, array(_REPORTE_REF_COMERCIO, "identificador", "", "center", "left" ));
array_push($columnas, array(_REPORTE_REF_BBVA, "codigo", "", "center", "left" ));

if ($_SESSION['grupo_rol'] < 4 || $_SESSION['rol'] == 19) 
	array_push($columnas, array('Banco', "banco", "75", "center", "left" ));

	array_push($columnas, array('Fecha de la operaci&oacute;n', "fechaopr", "135", "center", "center" ),
				array(_REPORTE_VALOR_INICIAL, "importe{val}", "65", "center", "right" ),
				array('Valor reclamado', "importRec{val}", "65", "center", "right" ),
				array('Fecha Notificaci&oacute;n', "fechaNot", "135", "center", "center" ),
				array('Fecha L&iacute;mite Resp.', "fechalimi", "135", "center", "center" ),
				array('Fecha de Respuesta', "fechaCerr", "135", "center", "center"),
				array('Fecha Env&iacute;o Banco', "fechaBan", "135", "center", "center"),
				array('Fecha Reclamada', "fechaRec", "135", "center", "center" ));


if (strlen($_REQUEST["orden"]) > 0) $orden = $_REQUEST["orden"];
else $orden = $orden;

$querys = tabla( $ancho, 'E', $vista.$from, $orden, $where, $colEsp, $busqueda, $columnas );

$querCvs = '';

// Print only total execution time
$timing->printTotalExecutionTime();

// Print full stats
?>
<form target="_blank" name="imprime" action="componente/comercio/print.php" method="POST">
	<input type="hidden" name="querys" value="<?php echo stripslashes($vista1.$from.$where.$wherea)." order by ".$orden ?>">
	<input type="hidden" name="salida" value="1">
	<input type="hidden" name="idioma" value="<?php echo $_SESSION['idioma'] ?>">
</form>
<form name="exporta" action="impresion.php" method="POST">
	<input type="hidden" name="querys6" value="<?php echo stripslashes($vista1.$from.$where.$wherea)." order by ".$orden ?>">
	<input type="hidden" name="fecha1a" value="<?php echo $d['fecha1'] ?>">
	<input type="hidden" name="fecha2a" value="<?php echo $d['fecha2'] ?>">
	<input type="hidden" name="moneda" value="<?php echo stripslashes($d['moneda']) ?>">
	<input type="hidden" name="comercio" value="<?php echo ($d['comercio']) ?>">
	<input type="hidden" name="modo" value="<?php echo stripslashes($d['modo']) ?>">
	<input type="hidden" name="nombre" value="<?php echo $d['nombre'] ?>">
	<input type="hidden" name="pag" value="reporte">
</form>
