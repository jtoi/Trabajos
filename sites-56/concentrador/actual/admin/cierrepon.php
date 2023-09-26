<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
global $temp;
$html = new tablaHTML;
global $send_m;
$cor = new correo();

$d = $_REQUEST;
if (_MOS_CONFIG_DEBUG) {
// $d['cierremod'] = '1788';
// $d['idFact1'] = '1139';
// $d['transf1'] = '';
// $d['idfactTr1'] = '1139';
// $d['modifica'] = '1788';
// $d['cierre'] = '';
// $d['fecha1'] = '';
// $d['fecha2'] = '';
// $d['ctrr'] = '0';
// $d['fact'] = '0';
// $d['trns'] = '0';
// $d['trnsdif'] ='';
// $d['cr'] = '47 Cierre ERGOS Continental S.A. 28FEB17';
// $d['tr'] = '170225202039';
// $d['comerc'] = '132933118210';
// $d['emprm'] = '1';
// $d['cantt'] = '1';
// $d['cierr'] = 'Array';
// $d['tranfi'] = '1';
// $d['acumu'] = '0';
// $d['valor'] = '1372.05';
// $d['moneda'] = '978';
// $d['fecCierr'] = '28/02/2017';
// $d['observa'] = '';
// $d['nf1'] = '20170302-07';
// $d['vales1'] = '1372.05';
// $d['valest1'] = '1372.05';
// $d['bant1'] = '2';
// $d['fecEnt1'] = '28/02/2017';
// $d['enviar'] = 'Enviar';
//print_r($_SESSION);
 var_dump($d);
}
$moneda = $d['moneda'];

//inserta y modifica las facturas
if ($d['modifica']) {
	$i=0;
	
	//borro las facturas que ya existían para crear las nuevas..
	$q = "delete from tbl_factura where idcierre = ".$d['modifica'];
	$temp->query($q);
	
	while ($i < 20) {
		if (isset($d['nf'.$i])) {
	
		// 	if (!array_search($d['modifica'], $d['cierr']))  $d['cierr'][] = $d['modifica'];
		// 	print_r($d['cierr']);
			$pase = 1; $tot = 0;
			$q = "select idcomercio, valor, numFacturas, cantOper from tbl_cierreTransac where idcierre = ".$d['modifica'];
		// echo $q."<br>";
			$temp->query($q);
			if ($temp->_errorNum) $pase = 0;
			$idcom = $temp->f('idcomercio');
			$vale = $temp->f('valor');
			$numF = $temp->f('numFacturas');
			$numero = $temp->f('cantOper');
			
		// 	inserta las nuevas facturas
			if ($pase) {
				
				if (strlen($d['nf'.$i]) > 0) {
	
					//preparo la query para un insert de una factura nueva
					$qs = "insert into tbl_factura (idcomercio, idcierre, nombre, fecha, idmoneda, valor)
						values ('$idcom', '".$d['modifica']."', '".$d['nf'.$i]."', '".time()."',
						'$moneda', '".str_replace(',', '', $d['vales'.$i])."')";
					
					//si viene el identificador de una factura y ella se encuentra en la BD cambio la query a update para actualizarla
					if (isset($d['idFact'.$i]) && $d['idFact'.$i] > 0) {
						$temp->query("select id from tbl_factura where id = ".$d['idFact'.$i]);
						if ($temp->num_rows() > 0) {//Si existe la factura la actualizo
							
							$qs = "update tbl_factura set idcomercio = '$idcom', idcierre = '".$d['modifica']."', nombre = '".$d['nf'.$i]."', 
										fecha = '".time()."', idmoneda = '$moneda', valor = '".str_replace(',', '', $d['vales'.$i])."'
								 where id = ".$d['idFact'.$i];
						}
					}
					
					$temp->query($qs);
					if (strpos($qs, 'insert') > -1) 
						$d['idfactTr'.$i] = $temp->last_insert_id();
					
					if ($temp->_errorNum) {$pase = 0;break;}
					
				}
				
				//finaliza el aviso de cierre en el momento que es salvada la factura que lo respalda
				$q = "select cierre, numFacturas from tbl_cierreTransac where idcierre = ".$d['modifica'];
				$temp->query($q);
				$cierreNom = $temp->f('cierre');
				$cierreVal = $temp->f('numFacturas');
				
				$q = "select * from tbl_factura where idcierre = '".$d['modifica']."'";
				$temp->query($q);
				$facturaVal = $temp->num_rows();
				
				if ($facturaVal == $cierreVal) {
					$q = "update tbl_mensajes set activo = 0, fechaFin = ".mktime(0, 0, 0, date("m"), date("d"), date("Y"))." where mensaje like '%".$cierreNom."%'";
					$temp->query($q);
				}
			}
			
		// 	pone mensaje de salida
			if ($pase) echo "<div style=\"text-align:center;margin-top:20px;color:green;font-family:Arial sans-serif;font-size:11px\">Factura correctamente salvada.</div>";
			else echo "<div style=\"text-align:center;margin-top:20px;color:red;font-family:Arial sans-serif;font-size:11px\">Hubo un error, debe proceder nuevamente.
					Si este error se repitiera comuníquelo.</div>";
	
		}
			$i++;
	}
	
	//inserta las transferencias
// 		&& strlen($d['valest0']) > 0) {
	$pase = 1; 
	
	
// 	inserta las nuevas transferencias
	for ($i = 0; $i < 20; $i++) {
// 		if (!$d['transf'.$i]) $i++;
// 		echo "<br>valor='".$d['valest'.$i]."', banco='".$d['bant'.$i]."', FechaEntr='".$d['fecEnt'.$i].
// 				"', factura='".$d['idfactTr'.$i]."', transferencia='".$d['transf'.$i]."'<br>";
		if (strlen($d['valest'.$i]) > 0) {
			
			//borro las transferencias que tenga esa factura ya que la rel es 1 = 1
			$q = "delete from tbl_amfTransf where idfactura = ".$d['idfactTr'.$i];
			$temp->query($q);
			
// 			inserto la transferencia
			$q = "insert into tbl_amfTransf (idfactura, idbanco, valor, idmoneda, fechaEnt, fecha) values ('".$d['idfactTr'.$i]."', '".$d['bant'.$i]."',
						'".$d['valest'.$i]."', '$moneda', '".to_unix($d['fecEnt'.$i].' 23:59:59')."', '".time()."')";
			$temp->query($q);
			
			if ($temp->_errorNum) {$pase = 0;break;}
		} 
	}

	
// 	pone mensaje de salida
	if ($pase) echo "<div style=\"text-align:center;margin-top:20px;color:green;font-family:Arial sans-serif;font-size:11px\">Transferencia correctamente salvada.</div>";
	else echo "<div style=\"text-align:center;margin-top:20px;color:red;font-family:Arial sans-serif;font-size:11px\">Hubo un error en la transferencia, debe proceder nuevamente.
			Si este error se repitiera comuníquelo.</div>";
	
}

//inserta o modifica el cierre
if ($d['cr'] || $d['cierremod']) {
	$fes = to_unix($d['fecCierr'].' 23:59:59');
	$envia = true;
	
	//verifica que la última operación a cerrar efectivamente coíncida con el comercio puesto para el cierre
	$q = "select count(idtransaccion) total from tbl_transacciones t, tbl_comercio c, tbl_comercio o where o.id = c.cierrePor "
			. " and t.idcomercio = c.idcomercio and idtransaccion = '".$d['tr']."' and o.idcomercio = '".$d['comerc']."'";
	$temp->query($q);
	$total = $temp->f('total');
	if ($total == 0) {
		$q = "select count(idcierre) total from tbl_cierreTransac where idcomercio = ".$d['comerc'];
		$temp->query($q);
		if ($temp->f('total') == 0) $total = 1;
		
	}

	$q = "select cierrePor from tbl_comercio where idcomercio = ".$d['comerc'];
	$temp->query($q);
	$com = $temp->f('cierrePor');
	$numer = substr($d['cr'], 0, stripos($d['cr'], ' '));
	
	if ($total == 1 && !$d['cierremod']) {// la operación a cerrar y el comercio están bien pero no se envía el id del cierre se inserta
// 		$q = "select (numeracion + 1) 'num' from tbl_cierreTransac where idcomercio = '$com' and idempresa = '".$d['empr']."' order by numeracion desc limit 0,1";
// 		$temp->query($q);
// 		$numer = $temp->f('num');
	
// 		$q = "select * from tbl_cierreTransac where numeracion = '$numer' and idempresa = ".$d['empr']." and idcomercio = '$com'";
// 		$temp->query($q);
// 		if ($temp->num_rows() > 0) {
// 			echo "<div style=\"text-align:center;margin-top:20px;color:red;font-family:Arial sans-serif;font-size:11px\">La numeración del cierre ya se 
// 					encuentra en la base de datos.</div>";
// 		} else {
	
			$qz = "insert into tbl_cierreTransac (idtransaccion, idcomercio, numFacturas, cierre, valor, idmoneda, fechaCierre, fecha, 
						transferir, consolidado, observaciones, idempresa, numeracion, cantOper)
					values ('".$d['tr']."', $com, '".$d['cantt']."', '".$d['cr']."', '".$d['valor']."', '".$d['moneda']."', 
						$fes, unix_timestamp(), '".$d['tranfi']."', '".$d['acumu']."', '".utf8_encode($d['observa'])."', '".$d['empr']."', ".
						"'$numer', '".$d['numero']."')";
// 		}
	} elseif ($total == 1 && $d['cierremod']) {// la operación a cerrar y el comercio están bien pero se envía el id del cierre se actualiza
	
// 		$q = "select * from tbl_cierreTransac where numeracion = '$numer' and idempresa = ".$d['emprm']." and idcomercio = '$com'";
// 		$temp->query($q);
// 		if ($temp->num_rows() > 1) {
// 			echo "<div style=\"text-align:center;margin-top:20px;color:red;font-family:Arial sans-serif;font-size:11px\">La numeración del cierre ya se 
// 					encuentra en la base de datos</div>";
// 			$total = 0;
// 		} else {
		
			$qz = "update tbl_cierreTransac set idtransaccion = '".$d['tr']."', idcomercio = $com, transferir = '".$d['tranfi']."',
					consolidado = '".$d['acumu']."', numFacturas = '".$d['cantt']."', cierre = '".$d['cr']."', valor = '".$d['valor']."', idmoneda = '".
					$d['moneda']."', fechaCierre = $fes, observaciones = '".utf8_encode($d['observa'])."', idempresa = '".$d['emprm']."', numeracion = '$numer', cantOper = '".$d['numero']."'
							where idcierre = ".$d['cierremod'];
// 		}
	} else{ // error
		$envia = false;
		echo "<div style=\"text-align:center;margin-top:20px;color:red;font-family:Arial sans-serif;font-size:11px\">La combinación transacción - "
				. "comercio no se encuentra en la base de datos</div>";
	}
	
	//se procesa la query
// 	if (_MOS_CONFIG_DEBUG) echo $qz."<br>";
	if ($qz) {
// 		echo $qz;
		$temp->query($qz);
		if ($temp->getErrorMsg()) {
			echo "<div style=\"text-align:center;margin-top:20px;color:red;font-family:Arial sans-serif;font-size:11px\">Error: "
				. $temp->getErrorMsg()."</div>";
			$envia = false;
		} else {
			echo "<div style=\"text-align:center;margin-top:20px;color:green;font-family:Arial sans-serif;font-size:11px\">Cierre ".
				"correctamente salvado.</div>";
			// $d['cierremod'] = 
			$d['cambiar'] = '';
			if (strpos($qz, 'insert') > -1) $cierrPad = $temp->last_insert_id();
			else $cierrPad = $d['cierremod'];
		}
		
	}
	
	// se inserta cierre padre con los hijos
	if ($d['cierr']) {
		
		$q = "delete from tbl_colCierreCierre where idcierrepadre = ".$cierrPad;
		$temp->query($q);
		
		foreach ($d['cierr'] as $key => $value) {
			$q = "insert into tbl_colCierreCierre (idcierrepadre, idcierrehijo) values ($cierrPad, $value)";
			$temp->query($q);
// 			echo $q."<br>";
			$q = "update tbl_cierreTransac set transferir = 0 where idcierre = $value";
			$temp->query($q);
// 			echo $q."<br>";
		}
	}
	
	//Si todo ha ido bien y se quiere enviar el aviso de Cierre
	if ($envia && $d['avisom'] == 1) {
		
		$temp->query("update tbl_cierreTransac set envcorreo = 1 where idcierre = ".$cierrPad);
		$titulo = "";
		$asunto = "Notificación de {cierre}";
		$textoCor = "Estimado (a) {usuario}:<br><br>El cierre contable correspondiente al per&iacute;odo a liquidar, est&aacute; disponible en el Administrador de Comercios. Usted puede descargarlo accediendo con su nombre de usuario y contrase&ntilde;a a trav&eacute;s de la opci&oacute;n Comercio/Ver cierres.<br><br>Por favor, recuerde subir la(s) factura(s) a la opción del men&uacute; de la plataforma: Comercio / Subir factura.<br><br>Administrador de Comercios<br>Bidaiondo S.L.";
		
		$message = "AVISO !!! Su comercio tiene disponible para la descarga, el Cierre No. {$d['cr']}";
		
		$q = "select e.nombre nomb, e.email from tbl_cierreTransac c, tbl_economicos e where e.idcomercio = c.idcomercio and c.idcierre = ".$cierrPad;
		$correoMi .= "$q<br>";
//		error_log($q);
		$temp->query($q);
		$arrDest = $temp->loadRowList();

		foreach($arrDest as $dest) {
			$cor->to($dest[1]);
            $nom = utf8_decode($dest[0]);
//			error_log("Enviado correo a ".$dest[1]);
			$correoMi .= "Enviado correo a ".$dest[1]."<br>";
			if (!$cor->todo(55, str_replace("{cierre}", $d['cr'], $asunto), str_replace("{usuario}", $nom, str_replace("{cierre}", $d['cr'], $textoCor)))) {
				$cor->todo(37, 'Error en el envío de Cierre', "El envío del cierre ".$d['cr']." a ".$nom." ".$dest[1]." a dado error.");
				error_log($correoMi." El envío del cierre ".$d['cr']." a ".$nom." ".$dest[1]." a dado error.");
			}
		} 
	}
}

//borra el Cierre completo y todas las facturas y transferencias asociadas
if ($d['borrar']) {
	$q = "delete from tbl_cierreTransac where idcierre = ".$d['borrar'];
	$temp->query($q);
}

if ($d['pagar']) {
	$q = "update tbl_cierreTransac set factura = if(factura=1,0,1), fechaFac = ".time()." where idcierre = ".$d['pagar'];
	$temp->query($q);
}

/*
 * Preparación de los datos por defecto a mostrar en el Buscar
 */
//	Comercio
$query = "select idcomercio from tbl_comercio where activo = 'S'";
$temp->query($query);
$comercios = implode("', '", $temp->loadResultArray());

$comer = $_SESSION['comercio'];
if (($comer == 'todos' || $comer == 'varios') && $d['comercio']) $comercId = $d['comercio'];
else if (($comer == 'todos' || $comer == 'varios') && !$d['comercio']) $comercId = $comercios;
else if (($comer != 'todos' && $comer != 'varios')) $comercId = $comer;

if(is_array($comercId)) $comercId = implode('\', \'', $comercId);

//	Fechas y Horas
if ($d['buscar']) {
//		echo $d['buscar'];
	$tira = explode('and', $d['buscar']);
	$fecha1 = date('d/m/Y', substr($tira[3], strlen($tira[3])-1));
	$fecha2 = date('d/m/Y', substr($tira[4], 0, 11));
} else {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, date("m"), 1, date("Y")));
	$fecha2 = date('d/m/Y', time());
	if ($d['fecha1']) $fecha1 = $d['fecha1'];
	if ($d['fecha2']) $fecha2 = $d['fecha2'];
}

$mes1 = explode('/', $fecha1);
$mes1 = 1*$mes1[1];
$mes2 = explode('/', $fecha2);
$mes2 = 1*$mes2[1];

$cierre = '';
if($d['cierre']) $cierre = $d['cierre'];

$d['tipo']? $esta = $d['tipo']:$esta = "V', 'D', 'S', 'Q', 'M";

/* Construye el formulario de Buscar */
$html->java = "<style>.centro1 span{font-size:12px;font-weight:bold;line-height:23px;}</style>";

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_PONCIERRE;
$html->tituloTarea = _REPORTE_TASK;
if (!$d['cambiar']) $html->hide = true;
$html->anchoTabla = 700;
$html->anchoCeldaI = $html->anchoCeldaD = 345;

$html->inTextb('Cierre', '', 'cierre');
if (strpos($_SESSION['idcomStr'], ',')) {
	$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' and id in (".$_SESSION['idcomStr'].") order by nombre";
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query, $comercId, null, null, "multiple size='5'");
}
else $html->inHide ($comercId, 'comercio');
$estadoArr = array(
	array("V', 'D', 'S', 'Q', 'M", _REPORTE_TODOS),
	array('V', _CIERRE_VALOR),
	array('D', _CIERRE_DIARIO),
	array('S', _CIERRE_SEMANAL),
	array('Q', _CIERRE_QUINCENAL),
	array('M', _CIERRE_MENSUAL)
);
//	$html->inSelect(_CIERRE_TIPO, 'tipo', 3, $estadoArr, $esta);
$html->classCss = 'formul fecc';
$html->inTextb(_CIERRE_DESDE, $fecha1, 'fecha1');
$html->inTextb(_CIERRE_HASTA, $fecha2, 'fecha2');
$html->classCss = 'formul';
$valInicio = array(array('1','Si'));
$html->inCheckBox('Saltar las fechas', 'fechano', 3, $valInicio);
$arrIdio = array ('1', '0');
$arrEtiq = array('Cero', 'Cualquiera');
(isset($d['ceroF'])) ? $ceroF = $d['ceroF'] : $ceroF = '0';
(isset($d['ceroT'])) ? $ceroT = $d['ceroT'] : $ceroT = '0';
$html->inRadio('Valor en la factura', $arrIdio, 'ceroF', $arrEtiq, $ceroF);
$html->inRadio('Valor en la Transferencia', $arrIdio, 'ceroT', $arrEtiq, $ceroT);
$valInicio = array(array('0','Cualquiera'), array('1','Si'), array('2','No'));
$html->inSelect('Cierres que se van a transferir', 'ctrr', 3, $valInicio);
$factArr = array(
	array("0", "Todas"),
	array("1", "Con Facturas"),
	array("2", "Sin Facturas"),
);
$html->inSelect('Facturas?', 'fact', 3, $factArr, '0');
$transArr = array(
	array("0", "Todas"),
	array("1", "Con Transferencias"),
	array("2", "Sin Transferencias"),
);
$html->inSelect('Transferencias?', 'trns', 3, $transArr, '0');
$transArr = array(
	array("", "Todas"),
	array("1", "Con Diferencias"),
	array("2", "Sin Diferencias"),
);
$html->inSelect('Diferencias entre Cierre y Tranf', 'trnsdif', 3, $transArr, '0');
/* Termina el formulario de buscar */



if($d['cambiar']) {
	$q = "select count(*) total from tbl_factura where idcierre = ".$d['cambiar'];
	$temp->query($q);
	$cantF 			= $temp->f('total');
	
	$q = "select * from tbl_cierreTransac where idcierre = ".$d['cambiar'];
	$temp->query($q);
// 	$numFacturas 	= $temp->f('numFacturas');
	$vale 			= number_format(($temp->f('valor')),2,'.','');
	$moned 			= $temp->f('idmoneda');
	$idtransaccion	= $temp->f('idtransaccion');
	$idc			= $temp->f('idcomercio');
	$cierre			= $temp->f('cierre');
	$fechaCierre 	= date('d/m/Y', $temp->f('fechaCierre'));
	$tranfi			= $temp->f('transferir');
	$acumu			= $temp->f('consolidado');
	$observa		= $temp->f('observaciones');
	$numFacturas	= $temp->f('numFacturas');
	$empr			= $temp->f('idempresa');
	$envio			= $temp->f('envcorreo');
	$numero			= $temp->f('cantOper');
	$accion = "Modificar Cierre";
// 	print_r($temp->loadAssocList());
	$q = "select idcomercio from tbl_comercio where id = $idc";
	$temp->query($q);
	$idcomercio = $temp->f('idcomercio');
	$q = "select idcierrehijo from tbl_colCierreCierre where idcierrepadre = ".$d['cambiar']." and idcierrehijo != idcierrepadre";
	$temp->query($q);
	$arrCier = $temp->loadResultArray();
// 	print_r($arrCier);

	/*comienza formulario de modificación de cierres*/
	$html->inHide($d['cambiar'], 'cierremod');	
/* Cierres */

	$html->inTextoL($accion);
	$html->inTextb('Nombre del Cierre', $cierre, 'cr');
	$html->inTextb('&Uacute;ltima transacci&oacute;n del cierre', $idtransaccion, 'tr');
	if (strpos($_SESSION['idcomStr'], ',')) {
		$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' and id in (".$_SESSION['idcomStr'].
					") order by nombre";
		$html->inSelect(_COMERCIO_TITULO, 'comerc', 2, $query, $idcomercio, null, null);
	}
	else $html->inHide ($comercId, 'comerc');
	
	if (!$idc) {
		$q = "select idcomercio id from tbl_comercio where activo = 'S' and id in (".$_SESSION['idcomStr'].") order by nombre";
		$temp->query($q);
		$idc = $temp->f('id'); 
	}
	
	$q = "select id, nombre from tbl_empresas order by nombre";
	$html->inSelect("Empresa", 'emprm', 2, $q, $empr);
	
	$valInicio = array(0,10);
	$html->inSelect('Cant. de Transferencias a realizar', 'cantt', 4, $valInicio, $numFacturas);
	
	// los valores los llena el jquery
	$html->inSelect('Este Cierre también contiene los cierres', 'cierr', 1, null, null, null, null, 'multiple');
	
	$q = "select id from tbl_colCierreCierre where idcierrehijo = ".$d['cambiar'];
	$temp->query($q);
	if ($temp->num_rows()==0){
		$valorIni = array('1','0');
		$etiq = array(_FORM_YES, _FORM_NO);
		$html->inRadio("Se va a transferir?", $valorIni, 'tranfi', $etiq, $tranfi);
	}
	
	$valorIni = array('1','0');
	$etiq = array(_FORM_YES, _FORM_NO);
	$html->inRadio("Cierre Consolidado", $valorIni, 'acumu', $etiq, $acumu);
	
	$valInicio = array('Si');
	if (!$envio)
		$html->inRadio('Enviar el aviso de este cierre por correo', '1', 'aviso', 'aviso', $valInicio);
	
	//envio del aviso de transferencias
	$totFac = 0; $strid = '';
	$q = "select * from tbl_factura where idcierre = ".$d['cambiar'];
	$temp->query($q);
	$totFac = $temp->num_rows();
	$strid = $temp->f('id');
// 	if ($temp->num_rows() > 0) {
// 		$totFac = $totFac + $temp->f('valor');
// 		$strid .= $temp->f('id').",";
// 		while ($temp->next_record()){
// 			$totFac = $totFac + $temp->f('valor');
// 			$strid .= $temp->f('id').",";
// 		}
// 		$strid = trim($strid,",");
// 	}
	
	if ($totFac > 0) {
		$temp->query("select t.id from tbl_amfTransf t, tbl_factura f where t.idfactura = f.id and t.envcorreo = 0 and f.idcierre = ".$d['cambiar']);
		$temp->num = $temp->num_rows();
		if ($temp->num = $numFacturas) {
			$html->inRadio('Enviar el aviso de transferencias por correo', '1', 'avist', 'avist', $valInicio);
		}
	}
	
	$html->inTextb('Cantidad de operaciones', $numero, 'numero');
	$html->inTextb('Valor neto a percibir o total', $vale, 'valor');
	$query = "select idmoneda id, moneda nombre from tbl_moneda";
	$html->inSelect('Moneda de la transferencia', 'moneda', 2, $query, $moned);
	$html->classCss = 'formul fecc';
	$html->inTextb('Fecha de realizado el Cierre', $fechaCierre, 'fecCierr', null, null, null, 'Fecha en formato (dd/mm/yyyy)');
	$html->classCss = 'formul';
	$html->inTexarea('Observaciones', $observa, 'observa', 7, null, null, null, 27);
	
/* Termina Cierres */
	/*Termina formulario de modificación de cierres*/
	
	/* Comienza formulario de facturas */
	
	$q = "select id, nombre, valor, idmoneda from tbl_factura where idcierre = ".$d['cambiar'];
	$temp->query($q);
// 	echo $q;
// 	$numFacturas = $temp->num_rows();
	$arrFact = $temp->loadAssocList();
	$monTFact = $monTTransf = 0;
	$exis = $temp->num_rows();

// 	echo "<BR>numFact=$numFacturas";
	for ($i=0;$i<$numFacturas;$i++) {
		$val = $arrFact[$i]['valor'];
		$monTFact += $val;
		$idtrr = '';

		$html->inHide($arrFact[$i]['id'], 'idFact'.($i+1));
		$html->inTextoL('Factura '.($i+1));
		$html->inTextb('Número de la Factura', $arrFact[$i]['nombre'], 'nf'.($i+1));
		$html->inTextb('Valor', $val, 'vales'.($i+1));
// 		$query = "select idmoneda id, moneda nombre from tbl_moneda";
// 		$html->inSelect('Moneda', 'mon'.($i+1), 2, $query, $moned);
		if ($exis > 0)
			$html->inTextoL('<span onClick="borraelm('.$arrFact[$i]['id'].', \'F\')" class="borraTR">Borrar Factura</span>');
		
		/* Formulario de transferencias */
		if (strlen($arrFact[$i]['id']) > 1) {
			$q = "select id, idbanco, valor, m.moneda, fechaEnt from tbl_amfTransf t, tbl_moneda m
					where t.idmoneda = m.idmoneda and idfactura = ".$arrFact[$i]['id'];
			$temp->query($q);
			$idtrr = $temp->f('id');
	// 		echo "<br>$q<br>";
			if ($temp->f('fechaEnt') == null || $temp->f('fechaEnt') == '') $fechaTr = time();
			else $fechaTr = $temp->f('fechaEnt');
		} else $fechaTr = time();
		
		$monTTransf += $temp->f('valor');
		$html->inTextoL('Transferencia '.($i+1));
		$html->inHide($idtrr, 'transf'.($i+1));
		$html->inHide($arrFact[$i]['id'], 'idfactTr'.($i+1));
		$html->inTextb('Valor', $temp->f('valor'), 'valest'.($i+1), null, null);
		$query = "select id, banco nombre from tbl_bancos order by banco";
		$html->inSelect('Banco', 'bant'.($i+1), 2, $query, $temp->f('idbanco'));
		$html->classCss = 'formul fecc';
		$html->inTextb('Fecha entrada', date('d/m/Y', $fechaTr), 'fecEnt'.($i+1), null, null, null, 'Fecha en formato (dd/mm/yyyy)');
		$html->classCss = 'formul';
		if ($idtrr != '')
			$html->inTextoL('<span onClick="borraelm('.$idtrr.', \'T\')" class="borraTR">Borrar Transferencia</span>');

		/* Termina formulario de transferencias */
		
	}
	
		$html->inHide($d['cambiar'], 'modifica');
	/* Comienza formulario de las Facturas 
		$html->inHide($i+1, 'numFact');
	if ($monTFact < $vale) {
		$html->inTextoL('Nueva Factura');
	// 	$html->inHide($numFacturas, 'numFacturas');
	// 	$html->inTextoL('Factura '.$i+1);
		$html->inTextb('Número de la Factura', '', 'nf0');
		$html->inTextb('Valor', '', 'vales0');
		$query = "select idmoneda id, moneda nombre from tbl_moneda";
// 		$html->inSelect('Moneda', 'mon0', 2, $query, $moned);
	}

	/* Termina formulario de facturas 
	
	/*Comienza formulario de Transferencias
	if ($monTTransf < $vale) {
		$html->inTextoL('Nueva Transferencia');
		
		$q = "select count(id) total from tbl_factura where idcierre = ".$d['cambiar'];
		$temp->query($q);
		if ($temp->f('total') > 1) {
			$q = "select id, nombre from tbl_factura where idcierre = ".$d['cambiar'];
	// 		$html->inHide($arrFact[$i]['id'], 'idfactTr0');
			$html->inSelect('Factura asoc.', 'idfactTr0', 2, $q, '');
		} else 
			$html->inHide($arrFact[$i]['id'], 'idfactTr0');
		
		$html->inTextb('Valor', '', 'valest0', null, null);
// 		$html->inTextb('Moneda', $moned, 'mont0', null, null, 'readonly="true"');
		$query = "select id, banco nombre from tbl_bancos order by banco";
		$html->inSelect('Banco', 'bant0', 2, $query, '');
	$html->classCss = 'formul fecc';
		$html->inTextb('Fecha entrada', 'dd/mm/yyyy', 'fecEnt0', null, null, null, 'Fecha en formato (dd/mm/yyyy)');
	$html->classCss = 'formul';
	}
	
	Termina formulario de Transferencias*/


} else {

	/* Datos para insertar Cierre */
	$accion = "Insertar Cierre";
	$tranfi = '1';
	$mandi = '0';
	$vale = $acumu = $numero = '0';
	$observa = $idtransaccion = $cierre = '';
	$moned = '978';
	$fechaCierre = 'dd/mm/yyyy';
	/* Termina Datos para insertar Cierre */
	

	/* Cierres */
	
	$html->inTextoL($accion);
	$html->inTextb('Nombre del Cierre', $cierre, 'cr');
	$html->inTextb('&Uacute;ltima transacci&oacute;n del cierre', $idtransaccion, 'tr');
	if (strpos($_SESSION['idcomStr'], ',')) {
		$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' and id in (".$_SESSION['idcomStr'].
		") order by nombre";
		$html->inSelect(_COMERCIO_TITULO, 'comerc', 2, $query, $idcomercio, null, null);
	}
	else $html->inHide ($comercId, 'comerc');
	
	$q = "select id, nombre from tbl_empresas order by nombre";
	$html->inSelect("Empresa", 'empr', 2, $q);
	
	$valInicio = array(0,10);
	$html->inSelect('Cant. de Transferencias a realizar', 'cantt', 4, $valInicio, 1);
	
	if (!$idc) {
		$q = "select idcomercio id from tbl_comercio where activo = 'S' and id in (".$_SESSION['idcomStr'].") order by nombre";
		$temp->query($q);
		$idc = $temp->f('id');
	}
	
	// los valores los llena el jquery
	$html->inSelect('Este Cierre también contiene los cierres', 'cierr', 1, null, null, null, null, 'multiple');
	
// 	$q = "select id from tbl_colCierreCierre where idcierrehijo = ".$d['cambiar'];
// 	$temp->query($q);
// 	if ($temp->num_rows()==0){
	$valorIni = array('1','0');
	$etiq = array(_FORM_YES, _FORM_NO);
	$html->inRadio("Se va a transferir?", $valorIni, 'tranfi', $etiq, $tranfi);
// 	}
//	$valInicio = array(array(1,'Si'));
	$valorIni = array('1','0');
	$etiq = array(_FORM_YES, _FORM_NO);
 	$html->inRadio('Enviar el aviso de este cierre por correo', $valorIni, 'avisom', $etiq, $mandi);
	
	$valorIni = array('1','0');
	$etiq = array(_FORM_YES, _FORM_NO);
	$html->inRadio("Cierre Consolidado", $valorIni, 'acumu', $etiq, $acumu);
	
	$html->inTextb('Cantidad de operaciones', $numero, 'numero');
	$html->inTextb('Valor neto a percibir o total', $vale, 'valor');
	$query = "select idmoneda id, moneda nombre from tbl_moneda";
	$html->inSelect('Moneda de la transferencia', 'moneda', 2, $query, $moned);
	$html->classCss = 'formul fecc';
	$html->inTextb('Fecha de realizado el Cierre', $fechaCierre, 'fecCierr', null, null, null, 'Fecha en formato (dd/mm/yyyy)');
	$html->classCss = 'formul';
	$html->inTexarea('Observaciones', $observa, 'observa', 7, null, null, null, 27);
	
	/* Termina Cierres */
}



echo $html->salida();

/* Tabla de cierres */
//, case factura when 1 then 'Si' else 'No' end fact 
$vista = "select r.cierre, r.idcierre id, r.idtransaccion trasaccion, c.nombre comercio, formateaF(r.fechaCierre, ".$_SESSION['id'].") cles, r.numFacturas, formateaO(r.valor,2,".$_SESSION['id'].") 'valo', m.moneda,
			(select group_concat(nombre separator ',<br>') from tbl_factura f where f.idcierre = r.idcierre) fact,
			(select formateaO(sum(valor),2,".$_SESSION['id'].") from tbl_factura f where f.idcierre = r.idcierre) 'valfact',
			(select formateaO(sum(t.valor),2,".$_SESSION['id'].") from  tbl_amfTransf t, tbl_factura f where t.idfactura = f.id and f.idcierre = r.idcierre) 'vall', 
			(select b.banco from tbl_bancos b, tbl_amfTransf s, tbl_factura c where b.id = s.idbanco and s.idfactura = c.id and c.idcierre = r.idcierre limit 0,1) banco,
			concat(substring(r.observaciones,1,30),'..') observ, r.cantOper
		from tbl_cierreTransac r, tbl_comercio c, tbl_moneda m ";

//	echo "NombreVal= $nombreVal<br>";

$where = "where r.idcomercio = c.id "
	. " and r.idmoneda = m.idmoneda"
	. " and r.fecha between ".to_unix($fecha1." 00:00:00")." and ".(to_unix($fecha2." 23:59:59"))." "
	. " and c.idcomercio in ('$comercId')";

if (strlen($d['cierre']) > 2) $where .= " and r.cierre like '%".$d['cierre']."%'";

else {
	if ($d['ctrr'] == 1 ) //cierres que se van a transferir
		$where .= " and r.transferir != 0 ";
	elseif ($d['ctrr'] == 2) //cierres que NO se van a transferir
		$where .= " and r.transferir = 0 ";
	if ($d['fact'] == '1') //con facturas
		$where .= " and r.idcierre in (select idcierre from tbl_factura )";
	if ($d['fact'] == '2') //sin facturas
		$where .= " and r.transferir != 0 and r.idcierre not in (select idcierre from tbl_factura ) ";
	
	if ($d['trns'] == '1') //con transferencias
		$where .= " and r.idcierre in (select fg.idcierre from  tbl_amfTransf tf, tbl_factura fg where tf.idfactura = fg.id)";
	if ($d['trns'] == '2') //sin transferencias
		$where .= " and r.idcierre not in (select rf.idcierre from  tbl_amfTransf tra, tbl_factura rf where tra.idfactura = rf.id) and r.transferir != 0 ";
	
	if ($d['trnsdif'] == '1') //con diferencias
		$where .= " and r.transferir != 0 and (select sum(fr.valor) from tbl_factura fr where fr.idcierre = r.idcierre) !=
				(select sum(t.valor) from tbl_amfTransf t, tbl_factura ff where t.idfactura = ff.id and ff.idcierre = r.idcierre)";
	if ($d['trnsdif'] == '2') //sin diferencias
		$where .= "and r.transferir != 0 and (select sum(fr.valor) from tbl_factura fr where fr.idcierre = r.idcierre) =
				(select sum(t.valor) from tbl_amfTransf t, tbl_factura ff where t.idfactura = ff.id and ff.idcierre = r.idcierre)";
}

if (isset($d['ceroF']) && $d['ceroF'] == 1) {
	if (stripos($where, 'having'))
		$where .= " and valfact = '0.00'";
	else 
		$where .= " having valfact = '0.00'";
}

if (isset($d['ceroT']) && $d['ceroT'] == 1) {
	if (stripos($where, 'having'))
		$where .= " and vall = '0.00'";
	else 
		$where .= " having vall = '0.00'";
}

$where = stripslashes($where);
if ($d['buscar']) $where = $d['buscar'];
$orden = 'r.fecha desc, c.nombre';


if (strpos($where,'having') !== false) $where2 = substr($where, 0, strpos($where,'having'));
else $where2 = $where;
$q = "select sum(r.valor) tot from tbl_cierreTransac r, tbl_comercio c, tbl_moneda m ".$where2;
$temp->query($q);

$ancho = 1200;
echo "<div style='float:left; width:100%' ><table class='total1' width=\"100%\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
        <tr>
            <td><div class='total2'>";
		echo "<strong>Valor: </strong>".formatea_numero($temp->f('tot'))."&nbsp;&nbsp; ";
		echo "</div></td>
            <td width='100'><span class='css_x-office-document' onclick='document.exporta2.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='".
                _REPORTE_CSV."' title='"._REPORTE_CSV."'></span></td>
        </tr>
    </table></div>";
		
// echo "<div style='float:left;width:100%;' ><table class='total1' width=\"$ancho\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
//         <tr>
//             <td></td>
//             <td width='100'>
//                 <span class='css_x-office-document' onclick='document.exporta2.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='".
//                 _REPORTE_CSV."' title='"._REPORTE_CSV."'></span></td>
//         </tr>
//     </table></div>";

$colEsp = array(array("e", _GRUPOS_EDIT_DATA, "css_edit", _TAREA_EDITAR));
$colEsp[] = array("b", "Borrar Registro", "css_borra", "Borrar");
// $colEsp[] = array("t", _GRUPOS_FACTURA, "css_transf", _TAREA_ANULAR);
// $colEsp[] = array("x", _GRUPOS_SOLDEVOL, "css_reload", _TAREA_SOLDEVO);
// $colEsp[] = array("d", _GRUPOS_DEVUELVE_DATA, "css_edit", _TAREA_DEVUELTA);
// $colEsp[] = array("p", _GRUPOS_PAGA_COMERCIO, "css_dollar3", _TAREA_PAGADA);


$busqueda = array();
$columnas = array(
				array(_COMERCIO_ID, "id", "50", "center", "left" ),
				array("Cierre", "cierre", "", "center", "left" ),
				array("Ult Transc", "trasaccion", "90", "center", "left" ),
				array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left" ),
				array('Valor', "valo", "90", "center", "left" ),
				array('Cant.Oper', "cantOper", "50", "center", "left" ),
				array('Moneda', "moneda", "50", "center", "left" ),
				array('Cant Transf.', "numFacturas", "50", "center", "left" ),
				array(_REPORTE_FECHA." Cierre", "cles", "", "center", "center" ),
				array("Facturas", "fact", "100", "center", "center" ),
				array("Valor Facturas", "valfact", "90", "center", "center" ),
				array("Valor Transferencias", "vall", "90", "center", "center" ),
				array("Banco", "banco", "", "center", "center" ),
				array("Observaciones", "observ", "", "center", "center" )
			);


//	echo $query;
$sumaLib += $temp->f('totalLib');
// if (_MOS_CONFIG_DEBUG) echo $vista.$where." order by ".$orden;
$querys = tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
//	echo $rec[0];

if (strlen($_REQUEST["orden"]) > 0) $orden = $_REQUEST["orden"];
else $orden = $orden;

// echo "<br>vista2 $vista.$from.$where.$wherea";
?>
<form name="exporta2" action="impresion.php" method="POST">
	<input type="hidden" name="pag" value="reporte">
	<input type="hidden" name="querys8" value="<?php echo stripslashes($vista.$from.$where.$wherea)." order by ".$orden ?>">
</form>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.js"></script>
<link rel="stylesheet" href="https://www.administracomercios.com/js/jquery-ui-1121/jquery-ui.css">
<script type="text/javascript" src="https://www.administracomercios.com/js/jquery-ui-1121/jquery-ui.js"></script>
<script type="text/javascript" language="JavaScript">
// $(document).ready(function(){
	$(function(){
		$( ".fecc" ).datepicker({dateFormat: "dd/mm/yy"});
	});

	$("#cantt").blur(function(){
		if ($("#nf1").length) {
			$.post('componente/comercio/ejec.php',{
				fun: 	'cargcantt',
				cie: 	$("#modifica").val(),
				tr: 	$("#tr").val(),
				comerc: $("#comerc").val(),
				tranfi: $('input[name=tranfi]:checked').val(),
				acumu: 	$('input[name=acumu]:checked').val(),
				cr: 	$("#cr").val(),
				valor: 	$("#valor").val(),
				moneda: $("#moneda").val(),
				fes: 	$("#fecCierr").val(),
				observa: $("#observa").val(),
				num: $("#cantt").val()
			},function(data){
				var datos = eval('(' + data + ')');
				var options = $("#cierr");
				options.empty();
				if (datos.error.length > 0) alert(datos.error);
				console.log(datos.error);
				if (datos.cont) {
					if (datos.cont.length > 0) {
						window.open('index.php?componente=comercio&pag=cierrepon&cambiar='+datos.cont,'_self');
					}
				}
			});
		}
	});
	
	$("[id^=fecEnt]").click(function(){if ($(this).val() == 'dd/mm/yyyy') $(this).val('');})
	$("[id^=fecEnt]").blur(function(){if ($(this).val() == '') $(this).val('dd/mm/yyyy');})
	$("[id^=fecCierr]").click(function(){if ($(this).val() == 'dd/mm/yyyy') $(this).val('');})
	$("[id^=fecCierr]").blur(function(){if ($(this).val() == '') $(this).val('dd/mm/yyyy');})
	
	$("#fechano").click(function(){if($(this).is(":checked")) $("#fecha1").val('15/11/2015'); 
		else $("#fecha1").val('<?php echo $fecha1; ?>');});

	function evcorr(tipo) {
		$.post('componente/comercio/ejec.php',{
			fun: 'envcorr',
			tipo: tipo,
			cier: $("#cierremod").val()
		},function(data){
			var datos = eval('(' + data + ')');
			if (datos.error.length > 0) alert(datos.error);
			if (datos.cont) {
				if (datos.cont.length > 0) {
					alert(datos.cont);
				}
			}
		});
	}
	
	$("#0_avist").click(function(){
		if ($(this).is(":checked")){
			evcorr('t');
		}
	});


	$("#0_aviso").click(function(){
		if ($(this).is(":checked")){
			evcorr('c');
		}
	});

	(function() {
	    var ev = new $.Event('display'),
	        orig = $.fn.css;
	    $.fn.css = function() {
	        orig.apply(this, arguments);
	        $(this).trigger(ev);
	    }
	})();

	$('#divFormHid').bind('display', function(e) {
	    cambcom();
	});
	cambcom();

	
	// fija el comercio a trabajar si no está definido 
	$("#comerc").change(function (){cambcom();});
	//$("#divFormHid :visible").change(function (){cambcom();});

// });

function cambcom(){
	$("#div_cierr").hide();
	$.post('componente/comercio/ejec.php',{
		fun: 'carcom',
		com: $("#comerc :selected").val()
	},function(data){
		var datos = eval('(' + data + ')');
		var options = $("#cierr");
		options.empty();
		if (datos.error.length > 0) alert(datos.error);
		if (datos.cont) {
			if (datos.cont.length > 0) {
				$("#div_cierr").show();
				$.each(datos.cont, function(index,vale) {
					options.append(new Option(this.nombre, this.id));
				});
			}
		}
	});
	setTimeout(function(){
		var selected=<?php echo json_encode($arrCier); ?>;
		var obj=$('#cierr');
		for (var i in selected) {
		    var val=selected[i];
		   obj.find('option:[value='+val+']').attr('selected',1);
		}
	},1000);
}
	
function verifica() {

	numFact = $("#cantt").val();
	fecha = new Date();

	if ($("#cr").val().length > 0) {

		if (
				(!checkField (document.forms[0].cr, isAlphanumeric, false))
				||	(!checkField (document.forms[0].valor, isNumber, false))
				||	(!checkField (document.forms[0].fecCierr, isDate, false))
			) {
			return false;
		}

		if ($("#valor").val().length == 1) {
			alert("Revise el valor del cierre puesto");
			return false;
		}
	
		if($("#tr").val().length == 12) {
			if((fecha.getYear()-100)!=$("#tr").val().substr(0,2))
				if (confirm('La transacción no comienza con el año actual. Está correcto?')) var valoreeee = 1;
				else return false;
		} else {
			alert('El número de la transacción no tiene 12 caracteres');
			return false;
		}
	}

	if (numFact > 0) {
		var factAcum = 0;
		for (i=0;i<=numFact;i++) {
			if ($('#nf'+i).length) {
				if($('#nf'+i).val() == '' && $("#vales"+i).val() != '' ) {//No puso número de factura
			//			 if ($(\"#nf1\").val().length > 3) {
			//				alert('Al menos tiene que poner el número de la factura 1');
					alert('No ha puesto el número de la factura y sin embargo le ha puesto valor. Debe poner el número');
					$('#nf'+i).focus();
					return false;
			//			}
				} else if($('#nf'+i).val() != '') { //puso el número de factura
					if (
							($("#nf"+i).val().length < 3)
						||	($("#vales"+i).val().length < 3)
						) { //chequeo que esté bien el número de factura y el valor
						alert("Deberá revisar los datos entrados"); 
						$('#nf'+i).focus();
						return false;
					} else { //chequeo que el valor sea un número
						if ($("#vales"+i).val() * 1 > 0)
							factAcum += (($("#vales"+i).val())*1);
						else {
							alert("Deberá entrar números"); 
							$('#vales'+i).focus();
							return false;
						}
					}
				}
			}

			if ($("#valest"+i).val() > 10) {// hay valor para la transferencia
				ok = false;
				// recorro el conjunto de las facturas para ver si el valor de la transf coíncide con alguna
				for (j=0; j<=(numFact*1); j++) { 
						// console.log($("#valest"+i).val()+" = "+$("#vales"+j).val());
					if (($("#valest"+i).val()*1) == ($("#vales"+j).val()*1)) {
						// aparece la factura que ampara el monto
						$("#idfactTr"+i).val($("#idFact"+j).val());
						ok = true;
// 						return false;
						break;
					}
				}
				if (!ok) {
// 					alert("El valor entrado en la transferencia no coíncide con ninguna Factura.");
// 					return false;
					if (confirm("El valor entrado en la transferencia no coíncide con ninguna Factura. Seguimos?")) {
						
						return true; 
					} else return false;
				} else {
					if ($("#valest"+i).val().length) {
						if (!$("#fecEnt"+i).length || $("#fecEnt"+i).val() == 'dd/mm/yyyy') {
							alert("Debe poner una fecha válida");
							$("#fecEnt"+i).focus();
							return false;
						} else {
							arrfec = $("#fecEnt"+i).val().split("/");
							if ((arrfec[0]*1) > 0 && (arrfec[0]*1) < 32) null;
							else {
								alert("El día en la fecha de la transferencia está mal");
								$("#fecEnt"+i).focus();
								return false;
							}
							if ((arrfec[1]*1) > 0 && (arrfec[1]*1) < 13) null;
							else {
								alert("El mes en la fecha de la transferencia está mal");
								$("#fecEnt"+i).focus();
								return false;
							}
							if ((arrfec[2]*1) >= (<?php echo date('Y'); ?> -1) && (arrfec[2]*1) <= (<?php echo date('Y'); ?>)) null;
							else {
								alert("El año en la fecha de la transferencia está mal");
								$("#fecEnt"+i).focus();
								return false;
							}
						}
					}
				}
			}
		}

		//chequea que monto de todas las facturas no se vaya por encima del Cierre
		if (($("#valor").val()*1) < (factAcum.toFixed(2)*1)) {
			if (confirm("El valor total de las facturas puestas se va por encima del Cierre. Está correcto así?")) var disp = 18;
			else return false;
		}

// 		console.log(factAcum.toFixed(2)+" != "+$("#valor").val());
		if (factAcum.toFixed(2) != $("#valor").val()) {
			if ($("#valor").val() > 0 && $("#vales1").val() > 0) {
				if (
					confirm("El valor de la(s) factura(s) no coíncide con el del Cierre. Está correcto así?")
					) return true; else {$("#vales"+(i-1)).focus(); return false;}
			}
		}
		
	}
	return true;
}

function borraelm(ident, elem) {
	if (elem == 'T') {
		texto = "Se va a proceder a borrar esta Transferencia. Está seguro?";
	} else {
		texto = "Se va a proceder a borrar esta Factura y la Transferencia asociada a ella si tuviera. Está seguro?";
	}
	if (confirm(texto)) {
		$.post('componente/comercio/ejec.php',{
			fun: 'quiTR',
			val: ident,
			tipo: elem
		},function(data){
			var datos = eval('(' + data + ')');
			var options = $("#cierr");
			options.empty();
			if (datos.error.length > 0) alert(datos.error);
			if (datos.cont == 'ok') {
				location.reload();
			}
		});
		
	}
}

</script>
