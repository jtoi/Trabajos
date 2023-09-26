<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$temp = new ps_DB;
$html = new tablaHTML;
$corCreo = new correo();
global $send_m;
$timing = new Timing("Reporte");

$timing->start();


$d = $_REQUEST;
// print_r($_SESSION);
//  print_r($d);
if ($d['cambiar']) { //devolución
	
	if ($_SESSION['grupo_rol'] <= 5  && $_SESSION['grupo_rol'] != 3) {

		$query = "select t.valor, t.idcomercio, c.nombre, d.valorDev, t.idtransaccionMod, t.pasarela, p.devolucion, t.fecha_mod
					from tbl_transacciones t, tbl_comercio c, tbl_pasarela p, tbl_devoluciones d
					where p.idPasarela = t.pasarela 
						and t.idtransaccion = d.idtransaccion
						and t.idcomercio = c.idcomercio 
						and t.idtransaccion = '".$d['cambiar']."'";
		$temp->query($query);
		$valo = money_format('%i', $temp->f('valor')/100);
		$valodev = money_format('%i', $temp->f('valorDev'));
		$paseAutom = false;
		
		if($temp->f('fecha_mod') >= (time()-(119*60*60*24)) && $temp->f('devolucion') == 1) {$paseAutom = true;}

		$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
					function verifica() {
                  //      $('#valor').val($('#valante').val());
						if (
								(checkField (document.admin_form.valor, isMoney, ''))
							) {

							if ((document.admin_form.valante.value - document.admin_form.valor.value) >= 0) {
								return true;
							}
							else {
								alert('"._REPORTE_NOPUEDO."');
							}
						}
						return false;
					}
					</script>";

		$html->idio = $_SESSION['idioma'];
		$html->tituloPag = _CIERRE_RETROC;
		$html->tituloTarea = "&nbsp;";
		$html->anchoTabla = 400;
		$html->anchoCeldaI = 200;
		$html->anchoCeldaD = 190;
			$html->inHide($valo, 'valante');
		if ($paseAutom == true) {
			$html->inTextoL('Este TPV permite devolución automática directo en el banco');
		} else {
			$html->inHide(false, 'devolAutom');
		}
		$html->inTextb(_REPORTE_IDENTIFTRANS, $d['cambiar'], 'iddentinf', null, null, "readonly='true'");
		$html->inTextb("Nuevo identificador", $temp->f('idtransaccionMod'), 'iddentinfN', null, null);
		$html->inHide($temp->f('idcomercio'), 'nomdbre');
		$html->inHide(true, 'devolc');
		$html->inTextb(_INICIO_COMERCIO, $temp->f('nombre'), 'nomdbreCOM', null, null, "readonly='true'");
		$html->inTextb("Valor de la operación", $valo, 'valorop', null, null, "readonly='true'");
		$html->inTextb("Valor solicitado a Devolver", $valodev, 'valordev', null, null, "readonly='true'");
		$arrIn = array('1','0');
		$arrLa = array('Si','No');
		$html->inRadio('Se cobra al comercio', $arrIn, 'cobrocom', $arrLa, '1');
		if ($temp->f('pasarela') != 37) {
			if ($paseAutom == true) {
				$html->inRadio('Devolución automática', $arrIn, 'devolAutom', $arrLa, '0');
			}
			$html->inTextb("Valor a Devolver", null, 'valor');
		} else {
			$html->inHide($valodev, 'valor');
			$html->inHide('1', 'devolAutom');
		}
		$contenido .=  $html->salida();
		
	} else {
		$contenido = _AUTENT_NOSEPUEDE;
	}
	echo $contenido;

	
} else {
	if ($d['iddentinf']) {
		/*
		 *  Va a devolver la transaccion
		 */
        if ($_SESSION['rol'] != 3)
            include_once( 'componente/comercio/devolucion.php' );
	}

	if ($d['borrar']){
		/*
		 * Va a anular la transacciï¿½n
		 */
		if ($_SESSION['grupo_rol'] <= 5 && $_SESSION['grupo_rol'] != 3 ) { //Rechaza si el administrador no es del grupito del chuchuchú
			$query = "select moneda from tbl_transacciones where idtransaccion = '".$d['borrar']."'";
			$temp->query($query);
			$mon = $temp->f('moneda');
			
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
			
			$query = "update tbl_transacciones set euroEquivDev = valor * 0.01 / $cambio, valor = 0, fecha_mod = ".time()
						. " , estado = 'R', tasaDev = $cambio, solDev = 0, idtransaccionMod = '".
                        $d['iddentinfN']."' where idtransaccion = '".$d['borrar']."'";
			$temp->query($query);

			$query = "update tbl_reserva set valor = 0, fechaCancel = ".time().", estado = 'B'
						where id_transaccion = '".$d['borrar']."'";
			$temp->query($query);


            $query = "select c.nombre, t.fecha, t.valor_inicial valor
                        from tbl_comercio c, tbl_transacciones t
                        where  idtransaccion = '".$d['borrar']."'
                        and t.idcomercio = c.idcomercio ";
            $temp->query($query);
            $fecha = $temp->f('fecha');
            $comnombre = $temp->f('nombre');
            $valor = $temp->f('valor');

            $subject = 'Descuento / Devolución';

            $message = "transacción: ".$d['borrar']." \r\n
                        comercio: $comnombre \r\n
                        valor descontado: $valor \r\n
                        fecha: ". date('d/m/Y h:m a', $fecha);

            $corCreo->todo(27, $subject, $message);

		} else $contenido = _AUTENT_NOSEPUEDE;
	}

	if ($d['pagar'] && $_SESSION['grupo_rol'] != 3) {
		/*
		* Paga la transaccion al comercio
		*/
		if ($_SESSION['grupo_rol'] <= 5  && $_SESSION['grupo_rol'] != 3) { //Rechaza si el administrador no es del grupito del chuchuchï¿½
			$query = "select pago from tbl_transacciones where idtransaccion = '".$d['pagar']."'";
			$temp->query($query);
			if ($temp->f('pago') == 0) $pagar = 1; else $pagar = 0; //revierte el pago de la transacciï¿½n por si 'hay metï¿½ la pata'!!
			$query = "update tbl_transacciones set pago = $pagar, fechaPagada = ".time()." where idtransaccion = '".$d['pagar']."'";
			$temp->query($query);


            $query = "select c.nombre, t.fecha, t.valor_inicial valor
                        from tbl_comercio c, tbl_transacciones t
                        where  idtransaccion = '".$d['pagar']."'
                        and t.idcomercio = c.idcomercio ";
            $temp->query($query);
            $fecha = $temp->f('fecha');
            $comnombre = $temp->f('nombre');
            $valor = $temp->f('valor');

			$query = "select a.nombre, c.nombre comercio, email from tbl_comercio c, tbl_transacciones t, tbl_admin a
						where  idtransaccion = '".$d['pagar']."'
                        and t.idcomercio = c.idcomercio
						and  c.idcomercio = a.idcomercio
						and a.idrol = 11
						limit 0,1";
			$temp->query($query);
			$nombre = $temp->f('nombre');
			$email = $temp->f('email');
			$comercioN = $temp->f('comercio');

		} else $contenido = _AUTENT_NOSEPUEDE;
	}

	/*
	 * Preparaciï¿½n de los datos por defecto a mostrar en el Buscar
	 */
//	Pasarela
	$query = "select idPasarela from tbl_pasarela";
	$temp->query($query);
//	$listPasar = $temp->loadResultArray();
	$listPasar = implode(", ", $temp->loadResultArray()).", 0";
//echo $listPasar."<br>";

//	Comercio
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
	
	if ($_SESSION['rol'] != 16) $query = "select id from tbl_empresas";
	else $query = "select idempresa from tbl_admin where idadmin = ".$_SESSION['id'];
	$temp->query($query);
	$empresas = implode("','", $temp->loadResultArray());
	
// 	if ($_SESSION['rol'] != 16) {
		if (isset ($d['empresa'])) $empresaid = implode(",", $d['empresa']);
		else $empresaid = $empresas;
// 	}
	

	if(is_array($comercId)) $comercId = implode(',', $comercId);
	$comercId = str_replace("'", "", trim($comercId, ','));

//	Fechas y Horas
	if ($d['buscar']) {
//		echo $d['buscar'];
		$tira = explode('and', $d['buscar']);
		if (strlen($tira[3]) == 0 ) {
			$fecha1 = date('d/m/Y', substr($tira[3], strlen($tira[3])-11));
			$fecha2 = date('d/m/Y', substr($tira[4], 0, 11));
		} else {
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, date("m"), 1, date("Y")));
			$fecha2 = date('d/m/Y', time());
		}
	} else {
		$fecha1 = date('d/m/Y', mktime(0, 0, 0, date("m"), 1, date("Y")));
		$fecha2 = date('d/m/Y', time());
		if ($d['fecha1']) $fecha1 = $d['fecha1'];
		if ($d['fecha2']) $fecha2 = $d['fecha2'];
		
	}
	$valini = $d['valoi'];

	$modoVal = 'P';
	$nombreVal = '';

	$mes1 = explode('/', $fecha1);
	$mes1 = 1*$mes1[1];
	$mes2 = explode('/', $fecha2);
	$mes2 = 1*$mes2[1];
    $usrTr = ',';
    $tipofecha = 1;
    
    $query = "select id from tbl_tarjetas order by nombre";
    $temp->query($query);
    $tarjetas = implode("','", $temp->loadResultArray());
    if ($d['tarjeta']) $tarjetas = $d['tarjeta'];

	$d['horasin']? $hora1 = $d['horasin']:$hora1 = '00:00';
	$d['horasout']? $hora2 = $d['horasout']:$hora2 = '24:00';
	$d['estado']? $esta = $d['estado']:$esta = "V','B','A','R";
	$d['monedas']? $monedaid = $d['monedas']:$monedaid = "978', '124', '840', '826";
	if (isset($d['pasarela'])){ 
		if (is_array($d['pasarela'])) $pasarelaid = implode(',',$d['pasarela']);
		else $pasarelaid = $d['pasarela'];
	} else {$pasarelaid = $listPasar;}
	$d['idTit']? $idTit = $d['idTit'] : $idTit = '';
	if ($d['usrTr']) $usrTr = $d['usrTr'];
	if ($d['modo']) $modoVal = stripslashes($d['modo']);
	if ($d['nombre']) $nombreVal = $d['nombre'];
	if ($d['cod']) $cod = $d['cod'];
	if ($d['codBanc']) $codBanc = $d['codBanc'];
	if ($d['soldev']) $soldev = $d['soldev'];
	if ($d['ip']) $ip = $d['ip'];
	if ($d['tipofecha']) $tipofecha = $d['tipofecha'];
	if ($monedaid == "978', '124', '840', '826") $monedaid = "978', '124', '','840', '826";
	//echo $pasarelaid;
	if ($pasarelaid == "4, 1, 3, 8, 9, 10, 2, 6, 5, 7") $pasarelaid = "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 0";
	$pasarelaid = str_replace("'", "", $pasarelaid);
	$pasarelaid = rtrim($pasarelaid,',');
	//echo "<br>".$pasarelaid;
	
	if (strlen($nombreVal)) {
		$q = "select * from tbl_transacciones where idtransaccion = '$nombreVal' and pasarela = 0";
		$temp->query($q);
		if ($temp->num_rows() > 0) {
			echo "<script type=\"text/javascript\" >window.open('index.php?componente=comercio&pag=avisoRep&id=$nombreVal')</script>";
		}
	}
	
	/*
	 * Construye el formulario de Buscar
	 */
	$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
					function verifica() {
						return true;
					}
					</script>
                    <style> #usrTr{width:250px;}  </style>";

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = _REPORTE_TITLE;
	$html->tituloTarea = _REPORTE_TASK;
	$html->hide = true;
	$html->anchoTabla = 600;
	$html->anchoCeldaI = 170; $html->anchoCeldaD = 420;
	
	$html->inHide(true, 'query');
	$html->inTextb(_REPORTE_IDENTIFTRANS, $nombreVal, 'nombre');
	$html->inTextb(_REPORTE_REF_COMERCIO, $cod, 'cod');
	$html->inTextb(_REPORTE_REF_BBVA, $codBanc, 'codBanc');
	$html->inTextb(IP, $ip, 'ip');
	$html->inCheckBox(_REPORTE_SOLDEVOL, 'soldev', 5, '1');
	
	if ($_SESSION['rol'] != 16) {
	//búsqueda de la empresa
		$query = "select distinct e.id, e.nombre from tbl_empresas e, tbl_transacciones t, tbl_pasarela p, tbl_comercio c where t.pasarela = p.idPasarela 
					and e.id = p.idempresa and t.idcomercio = c.idcomercio and c.id in ($comer) order by e.nombre";
		$html->inSelect('Empresa', 'empresa', 5, $query,  str_replace(",", "', '", $empresaid), null, null, "multiple size='3'");
	}
	
	if ($comer == 'todos') {
		$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
		$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query,  str_replace(",", "', '", $comercId), null, null, "multiple size='5'");
	} elseif (strpos ($comer, ",")) {
		$query = "select a.idadmin id, concat(a.nombre, ' (',(case idcomercio when 'todos' then 'todos' when 'varios' then 'varios' else "
                . "(select c.nombre from tbl_comercio c where c.idcomercio = a.idcomercio) end), ')' ) nombre "
                . "from tbl_admin a where a.activo = 'S' "
                . "order by (case idcomercio when 'todos' then 'todos' when 'varios' then 'varios' else (select c.nombre "
                	. " from tbl_comercio c where c.idcomercio = a.idcomercio) end)";
		$html->inSelect('Transferencia impuesta por: ', 'usrTr', 5, $query,  null, null, null, null, "style='width:100px'");
		$query = "select id, nombre from tbl_comercio where id in (".$comer.") and activo = 'S' order by nombre";
		$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query,  str_replace(",", "', '", $comercId), null, null, "multiple size='5'");
	} else $html->inHide ($comercId, 'comercio');
	$modoArr = array(
		array("D', 'P", _REPORTE_TODOS),
		array('D', _COMERCIO_ACTIVITY_DES),
		array('P', _COMERCIO_ACTIVITY_PRO),
	);
	$html->inSelect(_COMERCIO_ACTIVITY, 'modo', 3, $modoArr, $modoVal);
	$query = "select idmoneda id, moneda nombre from tbl_moneda";
	$html->inSelect(_COMERCIO_MONEDA, 'monedas', 5, $query, $monedaid);
// 	echo $empresaid;
//	print_r($_SESSION);
// 	$query = "select distinct p.idPasarela id, p.nombre from tbl_pasarela p, tbl_comercio c, tbl_transacciones t where "
// 			. " t.idcomercio = c.idcomercio and t.pasarela = p.idPasarela and c.id in ($comercId) order by nombre";
// 	$temp->query($query);
// 	$arrP = $temp->loadAssocList();
// 	$arrIdP = $temp->loadResultArray();
// 	$arrIdP[] = (0);
// //	$arrP[] = array('id' => 0, 'nombre' => 'Avisos');
// 	$arrPasar = array();
// 	$arrPasar[] = array(implode(',', $arrIdP), _REPORTE_TODOS);
// 	for($i=0; $i<count($arrP); $i++) {
// 		$arrPasar[] = array($arrP[$i]['id'], $arrP[$i]['nombre']);
// 	}
	$query = "select id, nombre from tbl_tarjetas order by nombre";
	$html->inSelect("Tarjetas", 'tarjeta', 5, $query,  str_replace(",", "', '", $tarjetas), null, null, "multiple size='5'");
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 24 || $_SESSION['rol'] == 16 || $_SESSION['rol'] == 10 || $_SESSION['rol'] == 19) {
	$html->inTextb('Id Titanes', $idtit, 'idTit');
	$valInicio = "select idPasarela id, nombre from tbl_pasarela where idempresa in ('$empresaid') order by nombre asc";
	$html->inSelect(_COMERCIO_PASARELA, 'pasarela', 5, $valInicio,null,null,null,'multiple=true' );
	$html->inTextb("Valor Inicial", "0.00", "valoi", null, null, null, "0,00 ó 0.00");
	
} else {
	
	$query = "select distinct t.pasarela from tbl_comercio c, tbl_transacciones t where c.id in (" . $comer . ") and c.idcomercio = t.idcomercio";
	$temp->query($query);
	$pasar = implode(',', $temp->loadResultArray());
	$arrPSE = $arrPNSE =$arrPTR = array();
    
    if ($pasar != '') {
		$lis = '';
	    $q = "select p.idPasarela from tbl_pasarela p where p.idPasarela in ($pasar) and tipo = 'P' and secure = 1 order by idPasarela";
		$temp->query($q);
		if ($temp->num_rows()) {
			$arrPSE = $temp->loadResultArray();
			$pasarSe = implode(',', $arrPSE);
		}

	    $q = "select p.idPasarela from tbl_pasarela p where p.idPasarela in ($pasar) and tipo = 'P' and secure = 0 order by idPasarela";
		$temp->query($q);
		if ($temp->num_rows()) {
			$arrPNSE = $temp->loadResultArray();
			$pasarNS = implode(',', $arrPNSE);
		}
		
	    $q = "select p.idPasarela from tbl_pasarela p where p.idPasarela in ($pasar) and p.tipo = 'T' order by idPasarela";
		$temp->query($q);
		if ($temp->num_rows()) {
			$arrPTR = $temp->loadResultArray();
			$pasarTR = implode(',', $arrPTR);
		}
	}
	
	$arrPTOT = array_merge($arrPSE, $arrPNSE, $arrPTR);
	$lis = implode(",", $arrPTOT);
    
    $arrPasar = array(
    		array($lis, 'Todas'),
    		array($pasarSe, 'Segura'),
    		array($pasarNS, 'NO Segura'),
    		array($pasarTR, 'Transferencia'),
    );
	$html->inSelect(_COMERCIO_PASARELA, 'pasarela', 3, $arrPasar, "$pasarSe.','.$pasarNS.','.$pasarTR");
	

// 	$valInicio = "select p.idPasarela id, case secure when 1 then 'Segura' else 'NO Segura' end nombre from tbl_pasarela p where p.idPasarela in ($pasar)"
// 					." order by idPasarela";
// 	echo $valInicio;
}
	$estadoArr = array(
		array("P','A','D','N','B','V','R", _REPORTE_TODOS),
		array('P', _REPORTE_PROCESO),
		array('A', _REPORTE_ACEPTADA),
		array('D', _REPORTE_DENEGADA),
		array('N', _REPORTE_PROCESADA),
		array('B', _REPORTE_ANULADA),
		array('V', _REPORTE_DEVUELTA),
		array('R', _REPORTE_RECLAMADA),
		array("V','B','R", _REPORTE_ANULADA." - "._REPORTE_DEVUELTA." - "._REPORTE_RECLAMADA),
		array("V','B','A','R", _REPORTE_ACEPTADA.' - '._REPORTE_DEVUELTA.' - '._REPORTE_ANULADA." - "._REPORTE_RECLAMADA)
	);
	if ($_SESSION['grupo_rol'] < 2) {
		array_push($estadoArr, array("V','B','A','R','1", _REPORTE_ACEPTADA.' - '._REPORTE_DEVUELTA.' - '._REPORTE_ANULADA." - "._REPORTE_RECLAMADA." - No cargadas"));
	}
	$html->inSelect(_REPORTE_STATUS, 'estado', 3, $estadoArr, $esta);
	$horasArr = array(
		array('00:00', '00:00'),array('01:00', '01:00'),array('02:00', '02:00'),array('03:00', '03:00'),array('04:00', '04:00'),
		array('05:00', '05:00'),array('06:00', '06:00'),array('07:00', '07:00'),array('08:00', '08:00'),array('09:00', '09:00'),
		array('10:00', '10:00'),array('11:00', '11:00'),array('12:00', '12:00'),array('13:00', '13:00'),array('14:00', '14:00'),
		array('15:00', '15:00'),array('16:00', '16:00'),array('17:00', '17:00'),array('18:00', '18:00'),array('19:00', '19:00'),
		array('20:00', '20:00'),array('21:00', '21:00'),array('22:00', '22:00'),array('23:00', '23:00'),array('24:00', '24:00')
	);
	$ver = "&nbsp;<select name='horasin'>";
	$ver .= opciones_arr($horasArr, $hora1);
	$ver .= "</select>";
	$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', null, null, null, null, $ver);
	$ver = "&nbsp;<select name='horasout'>";
	$ver .= opciones_arr($horasArr, $hora2);
	$ver .= "</select>";
	$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', null, null, null, null, $ver);
	$arrVal1 = array(1,2,3);
	$arrVal2 = array("Ambas fechas", "Fecha inicio", "Fecha terminaci&oacute;n");
	$html->inRadio("B&uacute;scar por", $arrVal1, 'tipofecha', $arrVal2, $tipofecha);

	echo $html->salida();
	/*
	 * Termina el formulario de buscarva
	 */
//echo $pasarelaid;
	$tabView = $pasarView = $whereView = '';
	if ($_SESSION['grupo_rol'] > 3 && $_SESSION['rol'] != 19) { 
		$pasarView = "case (select secure from tbl_pasarela where idPasarela = t.pasarela and tipo = 'P') when 1 then 'Segura' 
						when 0 then 'NO Segura' else 'Transferencia' end pasarelaN";
	} else {
		$pasarView = "p.nombre pasarelaN, e.nombre 'Empresa'";
// 		$tabView = ", tbl_empresas e ";
// 		$whereView = " and e.id = p.idempresa ";
	}
	$vista = "select t.idtransaccion id,c.nombre comercio,
	case t.estado 
		when 'B' then round(t.tasaDev,4)
		when 'V' then round(t.tasaDev,4)
		when 'R' then round(t.tasaDev,4)
		else round(t.tasa,4) end tasaM, t.estado, t.fecha, formateaF(t.fecha_mod,".$_SESSION['id'].") 'Fecha Modificada', round(t.tasa,4),
			(t.valor_inicial/100) 'valIni{val}', c.idcomercio,t.pasarela,t.tipoEntorno tipoE,t.moneda idmoneda,
			t.codigo,round(t.tasaDev,4) tasaDev, $pasarView, m.moneda,p.tipo, t.tarjetas, t.identificadorBnco, 
	case t.solDev when 1 then 1 else 0 end solDe, "
// 	." case t.estado
// 			when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
// 			when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
// 			when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
// 			when 'A' then (t.valor/100/tasa)
// 			else '0.00' end 'euroEquiv{val}', "
	// ." case t.estado
	// 		when 'B' then (t.valor/100/tasaDev)
	// 		when 'V' then (t.valor/100/tasaDev)
	// 		when 'R' then (t.valor/100/tasaDev)
	// 		when 'A' then (t.valor/100/tasa)
	// 		else '0.00' end 'euroEquiv{val}',
	." formateaO((t.valor/100/round(t.tasa,4)), 2,".$_SESSION['id'].") 'euroEquiv{val}', CASE (select count(*) from tbl_reserva r where r.codigo = t.identificador) 
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
	case t.ip 
		when '127.0.0.1' then 'no record' 
		else t.ip end 'ip{ip}',
	case t.ip 
		when '127.0.0.1' then 'no record' 
		else t.ip end 'geo{geoip}',
	case t.idpais when null then 'no record' else (select i.nombre from tbl_paises i where i.id = t.idpais) end 'País',
	case t.pago 
		when 0 then 'No' 
		else 'Si' end pagada,
	case t.estado
		when 'M' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		when 'J' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		when 'Q' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		else (t.valor / 100) end 'valor{val}',
	case t.tipoEntorno 
		when 'P' then 'Producci&oacute;n' 
		else 'Desarrollo' end tipoEntorno,
	case t.estado 
		when 'P' then 'En Proceso' 
		when 'A' then if (solDev = 0, 'Aceptada', 'Sol. Devolc.')
		when 'D' then 'Denegada' 
		when 'N' then 'No Procesada' 
		when 'B' then 'Anulada' 
		when 'R' then 'Reclamada' 
		else 'Devuelta' end estad,
	case t.tipoOperacion
		when 'T' then '-'
		else j.nombre  end tjta,
	case t.tipoOperacion
		when 'T' then 'Transferencia'
		else 'Tarjeta' end tipo,
	case t.estado when 'B' or 'V' or 'R' then formateaO(((t.valor_Inicial - t.valor)/100), 2,".$_SESSION['id'].") else 0 end 'valDevDiv',
	case t.estado when 'B' or 'V' or 'R' then round(t.tasaDev,4) else 0 end 'tasaDev',
	case t.tasaDev when 0 then 0 else formateaO(((t.valor_Inicial - t.valor)/100/round(t.tasaDev,4)), 2,".$_SESSION['id'].") end 'valDevEu',
		formateaO((t.valor/100), 2,".$_SESSION['id'].") 'valAct', formateaO((t.valor/100/round(t.tasa,4)), 2,".$_SESSION['id'].") 'valActEu', t.id_error 'Error'
		
		
		  
		
		";


	if (($_SESSION['rol'] < 11 || $_SESSION['rol'] == 19) && $comercId == '39') 
		$vista .= ", (select titOrdenId from tbl_aisOrden o where o.idtransaccion = t.idtransaccion) 'titord' ";


// 	echo $tabView."<br>";
	$vista1 = "select t.idtransaccion id,c.nombre comercio,round(t.tasa,4) tasaM,t.tasa, t.estado,t.fecha,t.fecha_mod,(t.valor_inicial / 100) 'valIni{val}',
	c.idcomercio,t.solDev,t.pasarela,t.tipoEntorno tipoE,t.moneda idmoneda,t.codigo,t.id_error error,round(t.tasaDev,4) tasaDev, j.nombre tjta,
	$pasarView, m.moneda, identificador,
	case t.estado
			when 'B' then if (t.fecha_mod < t.fechaPagada , (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when 'V' then if (t.fecha_mod < t.fechaPagada , (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when 'R' then if (t.fecha_mod < t.fechaPagada , (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when 'A' then (t.valor/100/tasa)
			else '0.00' end 'euroEquiv{val}{tot}',
	case t.estado
		when 'B' then ((t.valor_inicial - t.valor) / 100)
		when 'V' then ((t.valor_inicial - t.valor) / 100) 
		when 'R' then ((t.valor_inicial - t.valor) / 100) 
		else 0 end valorDev,
	case (select count(*) from tbl_reserva r where (r.codigo = t.identificador)) 
		when 1 then (select r.nombre nombre from tbl_reserva r where (r.codigo = t.identificador)) 
		else ' - ' end cliente,
	case t.ip 
		when '127.0.0.1' then 'no record' 
		else t.ip end ip,
	case t.ip 
		when '127.0.0.1' then 'no record' 
		else t.ip end 'geo{geoip}',
	case t.pago 
		when 0 then 'No' 
		else 'Si' end pagada,
	case t.estado 
		when 'B' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100)) 
		when 'V' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100)) 
		when 'R' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		else (t.valor / 100) end 'valor{val}',
	case t.tipoEntorno 
		when 'P' then 'Producci&oacute;n' 
		else 'Desarrollo' end tipoEntorno,
	case t.estado 
		when 'P' then 'En Proceso'
		when 'A' then if (solDev = 0, 'Aceptada', 'Sol. Devolc.')
		when 'D' then 'Denegada'
		when 'N' then 'No Procesada'
		when 'B' then 'Anulada'
		when 'R' then 'Reclamada'
		else 'Devuelta' end estad,
	case t.tipoOperacion
		when 'T' then 'Transferencia'
		else 'Tarjeta' end tipo,
	case t.tipoOperacion
		when 'T' then '-'
		else j.nombre  end tjta";
$vista3 = "select t.idtransaccion 'NUM_COMP',from_unixtime(t.fecha_mod, '%d/%m/%Y') 'FECH_CONT', 'TIT_COMP', m.moneda 'COD_MONE', round(t.tasa,4) 'TASA', 'Descrip_General', 'COD_CUEN', 'COD_CTGT', 'DEBI', (t.valor_Inicial/100) 'IMPORTE',concat(t.idtransaccion,'|',t.identificador) 'DOCU', from_unixtime(t.fecha_mod, '%d/%m/%Y') 'FECH_VALO', 'COD_ACRE_DEUD', (select servicio from tbl_reserva r where t.idtransaccion = r.id_transaccion) 'DESCRIPCION'";
if (($_SESSION['rol'] < 11 || $_SESSION['rol'] == 19) && $comercId == '39')
		$vista1 .= ", (select titOrdenId from tbl_aisOrden o where o.idtransaccion = t.idtransaccion) 'titord'";
    
    $where = " where j.id = t.id_tarjeta and c.idcomercio = t.idcomercio and t.moneda = m.idmoneda and p.idPasarela = t.pasarela$whereView and e.id = p.idempresa ";
    $from = " from tbl_tarjetas j, tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_pasarela p, tbl_empresas e ";
    
//    echo "ho$usrTr la".(strstr($usrTr, ','))."<br>";
    if (strstr($usrTr, ',') === false) {
    	$from .= ", tbl_transferencias r ";
        $where .= " and t.idtransaccion = r.idTransf and r.idadmin = '$usrTr'";
    }

//     $vista .= $tabView;
//     $vista1 .= $tabView;
    
//     echo $tabView."<br>";
//    echo $vista."<br>";

//	echo "NombreVal= $nombreVal<br>";
	if ($nombreVal) { //echo "entra";
		$where .= " and (t.idtransaccion like '%$nombreVal%' or identificadorBnco like '%$nombreVal%')
					and c.id in ($comercId) ";
	} elseif ($cod) { //echo "entra";
		$where .= " and t.identificador like '%$cod%'
					and c.id in ($comercId) ";
	} elseif ($codBanc) { //echo "entra";
		$where .= " and t.codigo like '%$codBanc%'
					and c.id in ($comercId) ";
	} elseif ($ip) {
		$where .= " and ip like '%$ip%'
					and c.id in ($comercId) ";
	} elseif ($soldev) {
	    $where .= " and solDev = 1
					and c.id in ($comercId) ";
	} else {
		$fecha1 = $fecha1." ".$hora1.":00";
		$fecha2 = $fecha2." ".$hora2.":59";
//		echo "fecha2=$fecha2";
		$par = '';
		if (stripos($esta, '1') > 0) {
			$esta = str_replace ("','1", "", $esta);
			$par = " and t.carDevCom = 1 ";
		}
		if ($d['tarjeta']) {
			//var_dump($d['tarjeta']);
			$tarj = implode(',',$d['tarjeta']);
			$where .= " and t.tipoOperacion = 'P'
						and j.id in ('{$tarj}') ";
		}
		$where .= stripslashes(" and t.estado in ('$esta')
					and t.tipoEntorno in ('$modoVal')
					and c.id in ($comercId)
					and t.moneda in ('$monedaid')
					and t.pasarela in ($pasarelaid)
					and e.id in ('$empresaid')$par
				");
	}
	
	if ($idTit) {
		$where .= " and c.id = $comercId having titord = '$idTit' ";
	}

	//Si así lo pide el comercio los vendedores sólo ven sus operaciones
	if ($_SESSION['rol'] == 14 || $_SESSION['rol'] == 12) {
		$q = "select distinct vendventodo from tbl_comercio where id in (" . $comer . ")";
		$temp->query($q);
		$arrSal = implode(",", $temp->loadResultArray());
		if (strstr($arrSal, 'N')) {
			$from .= ", tbl_reserva v ";
			$where .= " and t.idtransaccion = v.id_transaccion and v.id_admin = ".$_SESSION['id']." ";
		}
	}
	if (strlen($valini)>1) {
		$valini = str_replace(".", "", str_replace(",", "", $valini));
		if ($valini*1 > 0){
			$where .= " and t.valor_inicial = $valini ";
		}
	}
	
	if ($d['buscar']) $where = $d['buscar'];
	$orden = 't.fecha_mod desc, comercio';
// echo $where;

	$colEsp[] = array("t", _GRUPOS_FACTURA, "css_transf", _TAREA_ANULAR);
	$colEsp[] = array("x", _GRUPOS_SOLDEVOL, "css_reload", _TAREA_SOLDEVO);
	if ($_SESSION['grupo_rol'] <= 5) {
		$colEsp[] = array("d", _GRUPOS_DEVUELVE_DATA, "css_edit", _TAREA_DEVUELTA);
		$colEsp[] = array("p", _GRUPOS_PAGA_COMERCIO, "css_dollar3", _TAREA_PAGADA);
		$colEsp[] = array("z", 'Poner transacción en Proceso de Reclamación', "css_borra", "Transacción Reclamada");
	}
	$busqueda = array();

//	Salva o llama la query salvada
// 	if (!$d['query'] && !$d["orden"]) {
// //		Si no se hace la busqueda, cargo la query salvada en la BD 
// 		$query = "select query from tbl_admin where idadmin = ". $_SESSION['id'];
// 		$temp->query($query);
		
// 		if (strlen($temp->f('query')) > 0 && $_SESSION['usequery'] == 'S') {
// 			$salQuery = html_entity_decode($temp->f('query'), ENT_QUOTES);

// 			$pos = strripos($salQuery, ' order by ');
// 			$pos2 = strripos($salQuery, ' where ');
// 			$pos3 = strripos($salQuery, ' from ');
// //			$arrWhe = spliti(' where ', $salQuery);
// //			print_r($arrWhe);
// //			echo $pos2;
// //			echo '??'.$where.'??';
// 			$orden = substr($salQuery, $pos+10);
// 			$where = str_replace(" order by ".$orden, '',substr($salQuery, $pos2));
// 			$vista = substr($salQuery, 0, $pos2);
// 			$from = substr(substr($salQuery, $pos3, $pos2), 0, strpos($salQuery, ' where '));
// 			echo "<br><br>$from<br><br>";
// 		}
// 	} else {
		
// 		if (strlen($d["orden"]) == 0)$arrOrden = " order by ".$orden;
// 		else $arrOrden = " order by ".$d['orden'];
// 		$conve = htmlentities($vista . $from . $where . $arrOrden, ENT_QUOTES);
// 		$query = "update tbl_admin set query = '$conve' where idadmin = ". $_SESSION['id'];
// 		$temp->query($query);
// 	}

// 	echo "hola$fecha2";
	if (strlen($nombreVal) == 0 && strlen($cod) == 0 && strlen($codBanc) == 0 && strlen($ip) == 0 && strlen($soldev) == 0) {
//		$where .= "and (fecha between ".to_unix($fecha1)." and ".(to_unix($fecha2))."
//					or t.fecha_mod between ".to_unix($fecha1)." and ".(to_unix($fecha2)).")";
		if ($tipofecha == 1)
			$where .= "and ((t.fecha_mod between ".to_unix($fecha1)." and ".(to_unix($fecha2)).") or (t.fecha between ".
						to_unix($fecha1)." and ".(to_unix($fecha2))."))";
		elseif ($tipofecha == 2)
			$where .= "and ((t.fecha between ".
						to_unix($fecha1)." and ".(to_unix($fecha2))."))";
		else
			$where .= "and ((t.fecha_mod between ".to_unix($fecha1)." and ".(to_unix($fecha2))."))";
	}
//	$wherea = "";
	
	$q = "select t.moneda, round(sum(case t.estado 
		when 'B' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		when 'V' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		when 'R' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		else (t.valor / 100) end),2) totalMon,
		sum(case t.estado
			when 'B' then if (t.fecha_mod < t.fechaPagada , (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when 'V' then if (t.fecha_mod < t.fechaPagada , (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when 'R' then if (t.fecha_mod < t.fechaPagada , (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when 'A' then (t.valor/100/tasa)
			else '0.00' end) 'euroEquiv'
			$from $tabView ".stripslashes($where)." group by t.moneda";
	$temp->query($q);
// 	$corCreo->todo(43, 'Select de monedas', $q);
	$arrMon = $temp->loadAssocList();
	$tote = 0;
	foreach ($arrMon as $item) {
		$tote += $item['euroEquiv'];
		switch ($item['moneda']) {
			case '124':
				$sumaCad = $item['totalMon'];
				break;
			case '826':
				$sumaLib = $item['totalMon'];
				break;
			case '978':
				$sumaEuros = $item['totalMon'];
				break;
			case '840':
				$sumaUsd = $item['totalMon'];
				break;
			case '392':
				$sumaYen = $item['totalMon'];
				break;
			case '32':
				$sumaArs = $item['totalMon'];
				break;
			case '152':
				$sumaClp = $item['totalMon'];
				break;
			case '170':
				$sumaCop = $item['totalMon'];
				break;
			case '356':
				$sumaInr = $item['totalMon'];
				break;
			case '484':
				$sumaMxn = $item['totalMon'];
				break;
			case '604':
				$sumaPen = $item['totalMon'];
				break;
			case '937':
				$sumaVef = $item['totalMon'];
				break;
			case '949':
				$sumaTry = $item['totalMon'];
				break;
			default:
				break;
		}
	}

	$ancho = 1700;
    
    if ($_SESSION['rol'] != 17) {
        echo "<div style='float:left; width:100%' ><table class='total1' width=\"100%\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
        <tr>
            <td><div class='total2'>"
            . "<strong>"._REPORTE_TOTAL.": &#8364; </strong>".formatea_numero($sumaEuros)."&nbsp;&nbsp; ";
	if ($sumaUsd > 0) echo "<strong>$ </strong>".formatea_numero($sumaUsd)."&nbsp;&nbsp; ";
	if ($sumaLib > 0) echo "<strong>&pound; </strong>".formatea_numero($sumaLib)."&nbsp;&nbsp; ";
	if ($sumaCad > 0) echo "<strong>CAD </strong>".formatea_numero($sumaCad)."&nbsp;&nbsp; ";
	if ($sumaYen > 0) echo "<strong>JPY </strong>".formatea_numero($sumaYen)."&nbsp;&nbsp; ";
	if ($sumaArs > 0) echo "<strong>ARS </strong>".formatea_numero($sumaArs)."&nbsp;&nbsp; ";
	if ($sumaClp > 0) echo "<strong>CLP</strong>".formatea_numero($sumaClp)."&nbsp;&nbsp; ";
	if ($sumaCop > 0) echo "<strong>COP </strong>".formatea_numero($sumaCop)."&nbsp;&nbsp; ";
    if ($sumaInr > 0) echo "<strong>INR </strong>".formatea_numero($sumaInr)."&nbsp;&nbsp; ";
	if ($sumaMxn > 0) echo "<strong>MXP </strong>".formatea_numero($sumaMxn)."&nbsp;&nbsp; ";
	if ($sumaPen > 0) echo "<strong>PEN </strong>".formatea_numero($sumaPen)."&nbsp;&nbsp; ";
	if ($sumaVef > 0) echo "<strong>VEF </strong>".formatea_numero($sumaVef)."&nbsp;&nbsp; ";
	if ($sumaTry > 0) echo "<strong>TRY </strong>".formatea_numero($sumaTry)."&nbsp;&nbsp; ";
	echo "<strong>"._COMERCIO_EUROSC." </strong>".formatea_numero($tote)."&nbsp;&nbsp; ";
	if ($_SESSION['rol'] < 11 || $comercId == '56') {
		echo "</div></td>
				<td width='140'><span class='css_document-print' onclick='document.imprime.submit()' onmouseover='this.style.cursor=\"pointer\"' alt=\"".
						_REPORTE_PRINT."\" title=\""._REPORTE_PRINT."\"></span>&nbsp;&nbsp;&nbsp;
					<span class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='".
						_REPORTE_CSV."1' title='"._REPORTE_CSV."1'></span>&nbsp;&nbsp;&nbsp;
					<span class='css_x-office-document' onclick='document.exporta2.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='".
						_REPORTE_CSV."2' title='"._REPORTE_CSV."2'></span>&nbsp;&nbsp;&nbsp;
					<span class='css_x-office-document' onclick='document.exporta3.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='Exportar a Excell' title='Exportar a Excell H.Nac.'></span></td>
			</tr>
		</table></div>";
	} else {
		
		echo "</div></td>
				<td width='140'><span class='css_document-print' onclick='document.imprime.submit()' onmouseover='this.style.cursor=\"pointer\"' alt=\"".
						_REPORTE_PRINT."\" title=\""._REPORTE_PRINT."\"></span>&nbsp;&nbsp;&nbsp;
					<span class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='".
						_REPORTE_CSV."1' title='"._REPORTE_CSV."1'></span>&nbsp;&nbsp;&nbsp;
					<span class='css_x-office-document' onclick='document.exporta2.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='".
						_REPORTE_CSV."2' title='"._REPORTE_CSV."2'></span></td>
			</tr>
		</table></div>";
		}
    }
	
	//columnas a mostrar
	$columnas = array(
				array('', "color{col}", "1", "center", "center", 1),
				array(_COMERCIO_ID, "id", "50", "center", "left", 1 ));
	if ($_SESSION['rol'] < 11 && $comercId == '39') array_push($columnas, array('TitOrden', "titord", "90", "center", "left", 1 ));
	if ($_SESSION['rol'] < 2 || strpos($comer, ',')) array_push($columnas, array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left", 1 ));
	if ($_SESSION['grupo_rol'] < 4 || $_SESSION['rol'] == 19) array_push($columnas, array('Empresa', "Empresa", "150", "center", "left", 1 ));
	array_push($columnas, array(_REPORTE_REF_COMERCIO, "identificador", "95", "center", "left", 1 ),
					array(_REPORTE_REF_BBVA, "codigo", "75", "center", "left", 1 ),
					array(_COMERCIO_PASARELA, "pasarelaN", "75", "center", "left", 1 ),
					array(_REPORTE_FECHA, "fecha", "135", "center", "center", 1 ),
					array(_COMERCIO_MONEDA, "moneda", "60", "center", "center", 1 ),
					array(_REPORTE_VALOR_INICIAL, "valIni{val}", "65", "center", "right", 1 ),
					array(_COMERCIO_TASA, "tasaM", "60", "center", "right", 1),
					array(_COMERCIO_EUROSC, "euroEquiv{val}", "60", "center", "right", 1 ),
					array(_REPORTE_ESTADO, "estad", "83", "center", "center", 1 ),
					array("T.tarjeta", "tjta", "80", "center", "left", 0 ),
					array(_REPORTE_FECHA_MOD, "Fecha Modificada", "135", "center", "center", 0 ),
					array('Valor Devuelto Divisa', 'valDevDiv','', 'center', 'right', 0 ),
					array('Tasa Devoluci&oacute;n', 'tasaDev','', 'center', 'right', 0 ),
					array('Valor Devuelto Eur Cambio', 'valDevEu','', 'center', 'right', 0 ),
					array(_REPORTE_VALOR, "valAct", "65", "center", "right", 0 ),
					array("Valor Actual Euros", "valActEu", "65", "center", "right", 0 ),
					array(_COMERCIO_ACTIVITY, "tipoEntorno", "75", "center", "center", 0 ),
					array(_REPORTE_ERROR, "Error", "200", "center", "center", 0 ));
	array_push($columnas, array(_REPORTE_IP, "ip{ip}", "75", "center", "center", 0 ),
					array(_REPORTE_PAIS, "Pa&iacute;s", "40", "center", "center", 0 ),
					array(_REPORTE_ALCOMERCIO, "pagada", "60", "center", "center", 0));
	if ($_SESSION['rol'] < 2 || $_SESSION['rol'] == 10 || $_SESSION['rol'] == 19) array_push($columnas, array("tarjeta", "tarjetas", "80", "center", "left", 0 ),
					array("Ident Banco", "identificadorBnco", "60", "center", "left", 0 ));
					
	$querys = tablaM( $ancho, 'E', $vista.$from, $orden, $where, $colEsp, $busqueda, $columnas );

	if (strlen($_REQUEST["orden"]) > 0) $orden = $_REQUEST["orden"];
	else $orden = $orden;

	$querCvs = '';
	
	$wherea = " and idmoneda in ('$monedaid') ";
	
// 	$corCreo->todo(43, 'Select final', $vista.$where.$wherea." order by ".$orden);
// if (_MOS_CONFIG_DEBUG) 	echo $vista1.$from.$where.$wherea." order by ".$orden;
 error_log(str_replace("\t", "", $vista1.$from.$where.$wherea." order by ".$orden));
//	mail('jtoirac@gmail.com', 'Ver que', $vista.$where.$wherea." order by ".$orden);
}

// Stop/end timing
//	$timing->stop();

	// Print only total execution time
	$timing->printTotalExecutionTime();

	// Print full stats
//	$timing->printFullStats();
?>
<form target="_blank" name="imprime" action="componente/comercio/print.php" method="POST">
	<input type="hidden" name="querys" value="<?php echo stripslashes($vista1.$from.$where.$wherea)." order by ".$orden ?>">
	<input type="hidden" name="salida" value="1">
	<input type="hidden" name="idioma" value="<?php echo $_SESSION['idioma'] ?>">
</form>
<form name="exporta" action="impresion.php" method="POST">
	<input type="hidden" name="querys61" value="<?php echo stripslashes($vista1.$from.$where.$wherea)." order by ".$orden ?>">
	<input type="hidden" name="fecha1a" value="<?php echo $d['fecha1'] ?>">
	<input type="hidden" name="fecha2a" value="<?php echo $d['fecha2'] ?>">
	<input type="hidden" name="moneda" value="<?php echo stripslashes($d['moneda']) ?>">
	<input type="hidden" name="comercio" value="<?php echo ($d['comercio']) ?>">
	<input type="hidden" name="modo" value="<?php echo stripslashes($d['modo']) ?>">
	<input type="hidden" name="nombre" value="<?php echo $d['nombre'] ?>">
	<input type="hidden" name="pag" value="reporte">
</form>
<form name="exporta2" action="impresion.php" method="POST">
	<input type="hidden" name="pag" value="reporte">
	<input type="hidden" name="querys21" value="<?php echo stripslashes($vista1.$from.$where.$wherea)." order by ".$orden ?>">
	<input type="hidden" name="fecha1b" value="<?php echo $d['fecha1'] ?>">
	<input type="hidden" name="fecha2b" value="<?php echo $d['fecha2'] ?>">
	<input type="hidden" name="moneda" value="<?php echo stripslashes($d['moneda']) ?>">
	<input type="hidden" name="comercio" value="<?php echo ($d['comercio']) ?>">
	<input type="hidden" name="modo" value="<?php echo stripslashes($d['modo']) ?>">
	<input type="hidden" name="nombre" value="<?php echo $d['nombre'] ?>">
</form>
<form name="exporta3" action="impresion.php" method="POST">
	<input type="hidden" name="pag" value="reporte">
	<input type="hidden" name="xls" value="<?php echo stripslashes($vista3.$from.$where.$wherea)." order by ".$orden ?>">
	<input type="hidden" name="fecha1b" value="<?php echo $d['fecha1'] ?>">
	<input type="hidden" name="fecha2b" value="<?php echo $d['fecha2'] ?>">
	<input type="hidden" name="moneda" value="<?php echo stripslashes($d['moneda']) ?>">
	<input type="hidden" name="comercio" value="<?php echo ($d['comercio']) ?>">
	<input type="hidden" name="modo" value="<?php echo stripslashes($d['modo']) ?>">
	<input type="hidden" name="nombre" value="<?php echo $d['nombre'] ?>">
</form>
