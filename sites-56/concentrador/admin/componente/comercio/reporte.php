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
// print_r($d);

if ($d['soldeve']) {
	$sale = cambiaSol ( $d['soldeve']);
	$d['nombre'] = $d['soldeve'];
	echo "<script language=\"JavaScript\" type=\"text/javascript\">alert('$sale')</script>";
}

if ($d['tranid']) {// cambia la operaci�n no procesada a aceptada

	$sale = '';
	$q = "select idtransaccion from tbl_transacciones where idtransaccion = '".$d['tranid']."' and estado = 'N' ";
	error_log ($q);
	$temp->query($q);
	if ($temp->num_rows() == 1){
		$q = "update tbl_transacciones set id_error = '', codigo = '".$d['tcode']."', fecha_mod = unix_timestamp(), valor = valor_inicial, estado = 'A', tasa = '".$d['tassa']."', euroEquiv = (valor/100/tasa) where idtransaccion = '".$d['tranid']."'";
	error_log($q);
		$temp->query($q);

		if (!$temp->getErrorMsg()) {
			$sale = 'Operaci�n correctamente Actualizada.';
		} else $sale = $temp->getErrorMsg();
	} else $sale = 'No existe la operaci�n en la Base de Datos';
	$d['nombre'] = $d['tranid'];
	echo "<script language=\"JavaScript\" type=\"text/javascript\">alert('$sale')</script>";
}

if ($d['cambiar']) { //devoluci�n
	
	if ($_SESSION['grupo_rol'] <= 5  && $_SESSION['grupo_rol'] != 3) {

		$query = "select t.valor, t.idcomercio, c.nombre, d.valorDev, t.idtransaccionMod, t.pasarela, p.devolucion, t.fecha_mod
					from tbl_transacciones t, tbl_comercio c, tbl_pasarela p, tbl_devoluciones d
					where p.idPasarela = t.pasarela 
						and t.idtransaccion = d.idtransaccion
						and t.idcomercio = c.idcomercio 
						and t.idtransaccion = '".$d['cambiar']."'";
		$temp->query($query);
		$valo = number_format($temp->f('valor')/100, 2);
		$valodev = number_format($temp->f('valorDev'), 2);

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
			$html->inTextoL('Este TPV permite devoluci�n autom�tica directo en el banco');
		} else {
			$html->inHide(false, 'devolAutom');
		}
		$html->inTextb(_REPORTE_IDENTIFTRANS, $d['cambiar'], 'iddentinf', null, null, "readonly='true'");
		$html->inTextb("Nuevo identificador", $temp->f('idtransaccionMod'), 'iddentinfN', null, null);
		$html->inHide($temp->f('idcomercio'), 'nomdbre');
		$html->inHide(true, 'devolc');
		$html->inTextb(_INICIO_COMERCIO, $temp->f('nombre'), 'nomdbreCOM', null, null, "readonly='true'");
		$html->inTextb("Valor de la operaci�n", $valo, 'valorop', null, null, "readonly='true'");
		$html->inTextb("Valor solicitado a Devolver", $valodev, 'valordev', null, null, "readonly='true'");
		$arrIn = array('1','0');
		$arrLa = array('Si','No');
		$html->inRadio('Se cobra al comercio', $arrIn, 'cobrocom', $arrLa, '1');
		if ($temp->f('pasarela') != 37) {
			if ($paseAutom == true) {
				$html->inRadio('Devoluci�n autom�tica', $arrIn, 'devolAutom', $arrLa, '0');
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
        if ($_SESSION['rol'] != 3) {
			include_once( 'componente/comercio/devolucion.php' );
			$nombreVal = $d['iddentinf'];
			// echo "<script type=\"text/javascript\" >window.open('index.php?componente=comercio&pag=reporte&nombre=".$d['iddentinf']."','_self')</script>";
		}
	}

	if ($d['borrar']){
		/*
		 * Va a anular la transacci�n
		 */
		if ($_SESSION['grupo_rol'] <= 5 && $_SESSION['grupo_rol'] != 3 ) { //Rechaza si el administrador no es del grupito del chuchuch�
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

            $subject = 'Descuento / Devoluci�n';

            $message = "transacci�n: ".$d['borrar']." \r\n
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
		if ($_SESSION['grupo_rol'] <= 5  && $_SESSION['grupo_rol'] != 3) { //Rechaza si el administrador no es del grupito del chuchuch�
			$query = "select pago from tbl_transacciones where idtransaccion = '".$d['pagar']."'";
			$temp->query($query);
			if ($temp->f('pago') == 0) $pagar = 1; else $pagar = 0; //revierte el pago de la transacci�n por si 'hay met� la pata'!!
			$query = "update tbl_transacciones set pago = $pagar, fechaPagada = ".time()." where idtransaccion = '".$d['pagar']."'";
			$temp->query($query);


            $query = "select c.nombre, t.fecha, t.valor_inicial valor
                        from tbl_comercio c, tbl_transacciones t
                        where  idtransaccion = '".$d['pagar']."'
                        and t.idcomercio = c.idcomercio ";
            $temp->query($query);
            $fecha = $temp->f('fecha');
            $comnombre = utf8_decode($temp->f('nombre'));
            $valor = $temp->f('valor');

			$query = "select a.nombre, c.nombre comercio, email from tbl_comercio c, tbl_transacciones t, tbl_admin a
						where  idtransaccion = '".$d['pagar']."'
                        and t.idcomercio = c.idcomercio
						and  c.idcomercio = a.idcomercio
						and a.idrol = 11
						limit 0,1";
			$temp->query($query);
			$nombre = utf8_decode($temp->f('nombre'));
			$email = $temp->f('email');
			$comercioN = utf8_decode($temp->f('comercio'));

		} else $contenido = _AUTENT_NOSEPUEDE;
	}

	/*
	 * Preparaci�n de los datos por defecto a mostrar en el Buscar
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
	// echo $query."<br>";
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
	(!isset($d['tipo'])) ? $tipo = "'P','A','T','R'" : $tipo = $d['tipo'];
	(!isset($d['pago'])) ? $pago = "'W','P','D','T'" : $pago = $d['pago'];
	(!isset($d['metodo'])) ? $metodo = "'R','T','M'" : $metodo = $d['metodo'];

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
	// $d['estado']? $esta = $d['estado']:$esta = "V','B','A','R";
	$d['estado'] ? $esta = $d['estado'] : $esta = "V','B','A','E','R";
	$d['monedas']? $monedaid = $d['monedas']:$monedaid = "978', '124', '840', '826";
	// echo $d['pasarela'];
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
	if ($d['solrec']) $solrec = $d['solrec'];
	if ($d['ip']) $ip = $d['ip'];
	if ($d['tipofecha']) $tipofecha = $d['tipofecha'];
	if ($monedaid == "978', '124', '840', '826") $monedaid = "978', '124', '','840', '826";
	// echo $pasarelaid;
	if ($pasarelaid == "4, 1, 3, 8, 9, 10, 2, 6, 5, 7") $pasarelaid = "1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 0";
	$pasarelaid = str_replace("'", "", $pasarelaid);
	$pasarelaid = rtrim($pasarelaid,',');
	// echo "<br>".$pasarelaid;
	
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
	$html->inCheckBox(_REPORTE_SOLRECLAMACION, 'solrec', 5, '1');

	// if ($_SESSION['rol'] != 16) {
	//b�squeda de la empresa
		$query = "select distinct e.id, e.nombre from tbl_empresas e, tbl_transacciones t, tbl_pasarela p, tbl_comercio c where t.pasarela = p.idPasarela 
					and e.id = p.idempresa and t.idcomercio = c.idcomercio and c.id in ($comer) and e.id in ('$empresas') order by e.nombre";
		// echo $query;
		$html->inSelect('Empresa', 'empresa', 5, $query,  str_replace(",", "', '", $empresaid), null, null, "multiple size='3'");

	// }
	
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
//	$query = "select idmoneda id, moneda nombre from tbl_moneda"; Reina
	$query = "select idmoneda id, moneda nombre from tbl_moneda where activo = 1";
	$html->inSelect(_COMERCIO_MONEDA, 'monedas', 5, $query, $monedaid);

	// $q = "select id, nombre from tbl_paises order by nombre";
	// $html->inSelect(_REPORTE_PAIS, 'pais', 5, $q, $d['pais']);
	
	$query = "select id, nombre from tbl_tarjetas order by nombre";
	$html->inSelect("Tipo de Tarjetas<br>o M&eacute;todo de pago", 'tarjeta', 5, $query,  str_replace(",", "', '", $tarjetas), null, null, "multiple size='5'");

if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 24 || $_SESSION['rol'] == 16 || $_SESSION['rol'] == 10 || $_SESSION['rol'] == 19) {
	$html->inTextb('Id Titanes', $idtit, 'idTit');
	$valInicio = "select idPasarela id, nombre from tbl_pasarela where idempresa in ('$empresaid') order by nombre asc";
	$html->inSelect(_COMERCIO_PASARELA, 'pasarela', 5, $valInicio,null,null,null,'multiple=true' );
	$html->inTextb("Valor Inicial", "0.00", "valoi", null, null, null, "0,00 � 0.00");
	
} else {
	
	$query = "select distinct t.pasarela from tbl_comercio c, tbl_transacciones t where c.id in (" . $comer . ") and c.idcomercio = t.idcomercio";
	$temp->query($query);
	$pasar = implode(',', $temp->loadResultArray());
	$arrPSE = $arrPNSE =$arrPTR = array();
    
    if ($pasar != '') {
		$lis = '';
	    $q = "select p.idPasarela from tbl_pasarela p where p.idPasarela in ($pasar) and tipo in ('P','A','R') and secure = 1 order by idPasarela";
		$temp->query($q);
		if ($temp->num_rows()) {
			$arrPSE = $temp->loadResultArray();
			$pasarSe = implode(',', $arrPSE);
		}

	    $q = "select p.idPasarela from tbl_pasarela p where p.idPasarela in ($pasar) and tipo in ('P','A','R') and secure = 0 order by idPasarela";
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

    $arrTipo = array(
    		array("'P','A','T','R'", 'Todas'),
    		array("'P'", 'Pago'),
    		array("'A'", 'Preautorizo'),
    		array("'T'", 'Transferencia'),
    		array("'R'", 'P. Referencia')
    );
	$html->inSelect('Tipo de operaci&oacute;n', 'tipo', 3, $arrTipo, $tipo);

    $arrPago = array(
    		array("'P','W','D','T'", 'Todas'),
    		array("'P'", 'Presencial'),
    		array("'W'", 'Web'),
    		array("'T'", 'TF'),
    		array("'D'", 'Diferido')
    );
	$html->inSelect('Forma de pago', 'pago', 3, $arrPago, $pago);

    $arrMetodo = array(
    		array("'R','T','M'", 'Cualquiera'),
    		array("'R'", 'Transferencia'),
    		array("'M'", 'Pago Alternativo'),
    		array("'T'", 'Tarjeta')
    );
	$html->inSelect('M&eacute;todo de pago', 'metodo', 3, $arrMetodo, $metodo);


	$estadoArr = array(
		array("P','A','D','N','B','V','E','R','L','T", _REPORTE_TODOS)
		, array('E', 'Preautorizada')
		, array('P', _REPORTE_PROCESO)
		, array('PA', _REPORTE_APOBADA." (Transferencias)")
		, array('PR', _REPORTE_PROCESO." (Transferencias)")
		, array('PV', _REPORTE_VENCIDA)
		, array('T', _REPORTE_PENDIENTE)
		, array('A', _REPORTE_ACEPTADA)
		, array('D', _REPORTE_DENEGADA. " o Cancelada")
		, array('N', _REPORTE_PROCESADA)
		, array('B', _REPORTE_ANULADA)
		, array('V', _REPORTE_DEVUELTA)
		, array('R', _REPORTE_RECLAMADA)
		, array('L', "Cancelada")
		, array("V','B','R", _REPORTE_ANULADA." - "._REPORTE_DEVUELTA." - "._REPORTE_RECLAMADA)
		, array("V','B','A','E','R", _REPORTE_ACEPTADA.' - '._REPORTE_DEVUELTA.' - '._REPORTE_ANULADA." - "._REPORTE_RECLAMADA)
	);
	if ($_SESSION['grupo_rol'] < 2) {
		array_push($estadoArr, array("V','B','A','E','R','1", _REPORTE_ACEPTADA.' - '._REPORTE_DEVUELTA.' - '._REPORTE_ANULADA." - "._REPORTE_RECLAMADA. " - Preautorizada - No cargadas"));
	}
	//echo $esta;
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
		$pasarView = "p.nombre pasarelaN, e.nombre empresa";
// 		$tabView = ", tbl_empresas e ";
// 		$whereView = " and e.id = p.idempresa ";
	}
	$vista = "select t.idtransaccion id,c.nombre comercio,
	case t.estado 
		when 'B' then round(t.tasaDev,4)
		when 'V' then round(t.tasaDev,4)
		when 'R' then round(t.tasaDev,4)
		else round(t.tasa,4) end tasaM, t.estado,t.fecha,t.fecha_mod, t.tasa,
			(t.valor_inicial/100) 'valIni{val}', c.idcomercio,t.pasarela,t.tipoEntorno tipoE,t.moneda idmoneda,
			replace(t.codigo, '%', ' - ') codigo,t.id_error error,round(t.tasaDev,4) tasaDev, $pasarView, m.moneda,p.tipo, t.tarjetas, t.identificadorBnco, 
	case t.solDev when 1 then 1 else 0 end solDe, 
	case t.solRec when 1 then 1 else 0 end solRe,
	case
			when t.estado = 'B' then concat((t.valor/100/tasaDev))
			when t.estado = 'V' then concat((t.valor/100/tasaDev))
			when t.estado = 'R' then concat((t.valor/100/tasaDev))
			when t.estado = 'A' then concat((t.valor/100/tasa))
			else '0.00' end 'euroEquiv{val}',".
	// "CASE (select count(*) from tbl_reserva r where r.id_transaccion = t.idtransaccion)  when 1 then concat('<a href=\"index.php?componente=comercio&pag=cliente&val=', t.idtransaccion, '\">', t.identificador, '</a>') else identificador end identificador,".
	"CASE  
		when t.tipoPago not in ('W','T') and t.idcomercio != '159958099335' then concat('<a href=\"index.php?componente=comercio&pag=cliente&val=', t.idtransaccion, '\">', t.identificador, '</a>')
		else identificador end identificador,".
	"case 
		when t.estado = 'P' then 
			case 
				when t.tipoOperacion = 'T' and (select count(*) from tbl_transferencias where idTransf = t.idtransaccion and vista = 0 and (fecha + 10*86400) > unix_timestamp()) = 1 then 'olive'
				when t.tipoOperacion = 'T' and (select count(*) from tbl_transferencias where idTransf = t.idtransaccion and vista = 1 and (fecha + 10*86400) > unix_timestamp()) = 1 then 'green'
				when t.tipoOperacion = 'T' and (select count(*) from tbl_transferencias where idTransf = t.idtransaccion and (fecha + 10*86400 <= unix_timestamp())) = 1 then 'purple'
			else '' end
		when t.estado = 'A' then if (solDev = 0 and solRec = 0, 'black', if(solDev = 1, 'gray', '#ff8c00'))
		when t.estado = 'E' then '#4d1400'
		when t.estado = 'D' then 'red' 
		when t.estado = 'N' then 'violet' 
		when t.estado = 'T' then '#00597d' 
		when t.estado = 'R' then 'brown' ".
		// "when t.estado = 'V' then if (solDev = 0, 'blue', 'gray')
		// when t.estado = 'B' then '#65a3f9'
		// when t.solDev = 1 then 'gray'".
		"when t.estado = 'B' or t.estado = 'V' then if (solDev = 0 and solRec = 0, (select case count(*) when 1 then 'blue' else '#65a3f9' end 'color' from tbl_devoluciones where idtransaccion = t.idtransaccion), if(solDev = 1, 'gray', '#ff8c00'))".
		"else 'olive' end 'color{col}',
	case t.ip 
		when '127.0.0.1' then 'no record' 
		else t.ip end 'ip{ip}',
	case t.ip 
		when '127.0.0.1' then 'no record' 
		else t.ip end 'geo{geoip}',
	case t.pago 
		when 0 then 'No' 
		else 'Si' end pagada,
	case 
		when t.estado = 'M' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		when t.estado = 'J' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		when t.estado = 'Q' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		else (t.valor / 100) end 'valor{val}',
	case t.tipoEntorno 
		when 'P' then 'Producci&oacute;n' 
		else 'Desarrollo' end tipoEntorno,
	case t.tipoPago
		when 'W' then 'Web'
		when 'P' then 'Presencial'
		when 'T' then 'TF'
		when 'D' then 'Diferido' end 'tipopago',
	case 
		when t.tipoOperacion = 'A' then
			case
				when t.estado = 'A' then 'Aceptada' 
				when t.estado = 'P' then 'En Proceso' 
				when t.estado = 'E' then 'Preautorizada'
				when t.estado = 'D' then 'No Confirmada' 
				when t.estado = 'N' then 'No Procesada' 
				when t.estado = 'L' then 'Cancelada' 
				else '' end
		when t.tipoOperacion = 'P' or t.tipoOperacion = 'T' or t.tipoOperacion = 'R' then
			case
				when t.estado = 'P' then 
					case 
						when t.tipoOperacion = 'T' and (select count(*) from tbl_transferencias where idTransf = t.idtransaccion and vista = 0 and (fecha + 10*86400) > unix_timestamp()) = 1 then 'Aprobada'
						when t.tipoOperacion = 'T' and (select count(*) from tbl_transferencias where idTransf = t.idtransaccion and vista = 1 and (fecha + 10*86400) > unix_timestamp()) = 1 then 'En Proceso'
						when t.tipoOperacion = 'T' and (select count(*) from tbl_transferencias where idTransf = t.idtransaccion and (fecha + 10*86400 <= unix_timestamp())) = 1 then 'Vencida'
					else '' end
				when t.estado = 'A' then if (solDev = 0 and solRec = 0, 'Aceptada', if(solDev = 1, 'Sol. Devolc.', 'Proceso Recl.'))
				when t.estado = 'D' then 
					case
						when t.tipoOperacion = 'T' then 'Cancelada' 
					else 'Denegada' end
				when t.estado = 'N' then 'No Procesada' 
				when t.estado = 'B' then if (solRec = 0, 'Anulada', 'Proceso Recl.') 
				when t.estado = 'R' then 'Reclamada' 
				when t.estado = 'V' then if (solRec = 0, 'Devuelta', 'Proceso Recl.') 
				when t.estado = 'T' then 'Pendiente' 
				else '' end
		else ''
		end estad,
	case 
		when t.tipoOperacion = 'T' then '-'
		else j.nombre  end tjta,
	case
		when t.tipoOperacion = 'T' then 'Transferencia'
		when t.tipoOperacion = 'A' then 'Preautorizo'
		else 'Tarjeta' end tipo,
	case 
		when t.tipoOperacion = 'T' then 'Transferencia' 
		when j.tipo = 'M' then 'P. Alterna.'
		else 'Tarjeta' end 'metodo' ";

    //    when t.estado = 'A' then if (solDev = 0, 'black', 'gray') Reina - lo comentado iba arriba
    // 	  "when t.estado = 'B' or t.estado = 'V' then if (solDev = 0, (select case count(*) when 1 then 'blue' else '#65a3f9' end 'color' from tbl_devoluciones where idtransaccion = t.idtransaccion), 'gray')".
    //    when t.estado = 'A' then if (t.solDev = 0, 'Aceptada', 'Sol. Devolc.')

	if (($_SESSION['rol'] < 11 || $_SESSION['rol'] == 19) && ($comercId == '39' || $comercId == '633')) 
		$vista .= ", (select titOrdenId from tbl_aisOrden o where o.idtransaccion = t.idtransaccion) 'titord' ";

// 	echo $tabView."<br>";
	$vista1 = "select t.idtransaccion id,c.nombre comercio,round(t.tasa,4) tasaM,t.tasa, t.estado,t.fecha,t.fecha_mod,(t.valor_inicial / 100) 'valIni{val}',
	c.idcomercio,t.solDev,t.solRec,t.pasarela,t.tipoEntorno tipoE,t.moneda idmoneda, replace(t.codigo, '%', ' - ') codigo,t.id_error error,round(t.tasaDev,4) tasaDev, j.nombre tjta,
	$pasarView, m.moneda, identificador,
	case 
			when t.estado = 'B' then if (t.fecha_mod < t.fechaPagada , (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when t.estado = 'V' then if (t.fecha_mod < t.fechaPagada , (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when t.estado = 'R' then if (t.fecha_mod < t.fechaPagada , (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
			when t.estado = 'A' then (t.valor/100/tasa)
			else '0.00' end 'euroEquiv{val}{tot}',
	case t.estado
		when 'B' then ((t.valor_inicial - t.valor) / 100)
		when 'V' then ((t.valor_inicial - t.valor) / 100) 
		when 'R' then ((t.valor_inicial - t.valor) / 100) 
		else 0 end valorDev,
	case (select count(*) from tbl_reserva r where (r.codigo = t.identificador and r.id_comercio = t.idcomercio)) 
		when 1 then (select r.nombre nombre from tbl_reserva r where (r.codigo = t.identificador and r.id_comercio = t.idcomercio)) 
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
	case t.tipoPago
		when 'W' then 'Web'
		when 'P' then 'Presencial'
		when 'T' then 'TF'
		when 'D' then 'Diferido' end 'tipopago',
	case t.tipoOperacion
		when 'A' then
			case t.estado
				when 'A' then 'Confirmada' 
				when 'P' then 'En Proceso' 
				when 'E' then 'Preautorizada'
				when 'D' then 'No Confirmada' 
				when 'N' then 'No Procesada' 
				when 'L' then 'Liberada' 
				else '' end
		when 'P' or 'T' then
			case t.estado
				when 'P' then 'En Proceso' 
			    when 'A' then if (t.solDev = 0 and t.solRec = 0, 'Aceptada', if(t.solDev = 1, 'Sol. Devolc.', 'Proceso Recl.'))
				when 'D' then 'Denegada' 
				when 'N' then 'No Procesada' 
			    when 'B' then if (t.solRec = 0, 'Anulada', 'Proceso Recl.') 
				when 'R' then 'Reclamada' 
			    when 'V' then if (t.solRec = 0, 'Devuelta', 'Proceso Recl.')
				else '' end
		else ''
		end estad,
	case t.tipoOperacion
		when 'T' then 'Transferencia'
		when 'A' then 'Preautorizo'
		else 'Tarjeta' end tipo,
	case t.tipoOperacion
		when 'T' then '-'
		else j.nombre  end tjta,
	case t.tipoPago
	    when 'D' then (select servicio from tbl_reserva r where t.identificador = r.codigo)
	    else '-' end servicio";

//    when 'A' then if (t.solDev = 0, 'Aceptada', 'Sol. Devolc.') Reina - esto iba arriba
//    when 'B' then 'Anulada'
//    when 'V' then 'Devuelta'


$vista3 = "select t.idtransaccion 'NUM_COMP',from_unixtime(t.fecha_mod, '%d/%m/%Y') 'FECH_CONT', 'TIT_COMP', m.moneda 'COD_MONE', round(t.tasa,4) 'TASA', 'Descrip_General', 'COD_CUEN', 'COD_CTGT', 'DEBI', (t.valor_Inicial/100) 'IMPORTE',concat(t.idtransaccion,'|',t.identificador) 'DOCU', from_unixtime(t.fecha_mod, '%d/%m/%Y') 'FECH_VALO', 'COD_ACRE_DEUD', (select servicio from tbl_reserva r where t.idtransaccion = r.id_transaccion) 'DESCRIPCION'";
$vista2 = $vista1.", case t.tipoPago
	    when 'D' then (select servicio from tbl_reserva r where t.idtransaccion = r.id_transaccion)
	    else '-' end servicio";
if (($_SESSION['rol'] < 11 || $_SESSION['rol'] == 19) && ($comercId == '39' || $comercId == '633'))
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
		$where .= " and (t.idtransaccion like '%$nombreVal%')
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
	} elseif ($solrec) {
        $where .= " and solRec = 1
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
			$where .= " and t.tipoOperacion in ('P','A')
						and j.id in ('{$tarj}') ";
		}

		// echo "<br>metodo={$d['metodo']}";
		// echo "<br>isset=".isset($d['metodo']);
		if (isset($d['metodo']) && $d['metodo'] == "'R'") {//metodo transferencias
			// echo "<br>entraR";
			$where .= stripslashes(" and t.tipoOperacion = 'T' ");
			$tipo = "'T'";
		} elseif (isset($d['metodo']) && $d['metodo'] == "'M'") {//metodo alternativo
			$where .= stripslashes(" and j.tipo = 'M' ");
			$tipo = "'P','A','R'";
			// echo "<br>tipo1=$tipo";
		} elseif (isset($d['metodo']) && $d['metodo'] == "'T'") {// metodo tarjetas
			$where .= stripslashes(" and j.tipo = 'T' ");
			$tipo = "'P','A','R'";
		}

			// echo "<br>tipo2=$tipo";
		if ($esta == 'PA') {
			$where .= stripslashes(" and t.estado in ('P') and t.tipoOperacion = 'T' and (select count(r.id) from tbl_transferencias r where r.idTransf = t.idtransaccion and r.vista = 0 and (r.fecha + 10*86400) > unix_timestamp()) > 0");
		}else if ($esta == 'PR') {
			$where .= stripslashes(" and t.estado in ('P') and t.tipoOperacion = 'T' and (select count(r.id) from tbl_transferencias r where r.idTransf = t.idtransaccion and r.vista = 1 and (r.fecha + 10*86400) > unix_timestamp()) > 0");
		}else if ($esta == 'PV') {
			$where .= stripslashes(" and t.estado in ('P') and t.tipoOperacion = 'T' and (select count(r.id) from tbl_transferencias r where r.idTransf = t.idtransaccion and (r.fecha + 10*86400) <= unix_timestamp()) > 0");
		} else $where .= stripslashes(" and t.estado in ('$esta')");

		$where .= stripslashes(" and t.tipoOperacion in ($tipo)
					and t.tipoPago in ($pago)
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
	
	// if ($d['pais']) {
	// 	$where .= " and t.idpais in ('".$d['pais']."') ";
	// }

	//Si as� lo pide el comercio los vendedores s�lo ven sus operaciones
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
	$orden = 't.fecha_mod desc';
// echo $where;

	$colEsp[] = array("t", 'Ver Transferencia o Preautorizo', "css_transf", _TAREA_ANULAR);
	$colEsp[] = array("x", _GRUPOS_SOLDEVOL, "css_reload", _TAREA_SOLDEVO);
	if ($_SESSION['grupo_rol'] <= 5) {
		$colEsp[] = array("d", _GRUPOS_DEVUELVE_DATA, "css_edit", _TAREA_DEVUELTA);
		$colEsp[] = array("p", _GRUPOS_PAGA_COMERCIO, "css_dollar3", _TAREA_PAGADA);
		$colEsp[] = array("z", 'Poner la transacci�n Reclamada', "css_borra", "Transacci�n Reclamada");
		$colEsp[] = array("r", 'Cambiar la Transaccion', "css_cambia", "Cambia");
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
	if (strlen($nombreVal) == 0 && strlen($cod) == 0 && strlen($codBanc) == 0 && strlen($ip) == 0 && strlen($soldev) == 0 && strlen($solrec) == 0) {
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
	if ($_SESSION['rol'] < 11 || $comercId == '56') { // Hotel Nacional
		echo "</div></td>
				<td width='140'><span class='css_document-print' onclick='document.imprime.submit()' onmouseover='this.style.cursor=\"pointer\"' alt=\"".
						_REPORTE_PRINT."\" title=\""._REPORTE_PRINT."\"></span>&nbsp;&nbsp;&nbsp;
					<span class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='".
						_REPORTE_CSV."1' title='"._REPORTE_CSV."1'></span>&nbsp;&nbsp;&nbsp;
					<span class='css_x-office-document' onclick='document.exporta2.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='".
						_REPORTE_CSV."2' title='"._REPORTE_CSV."2'></span>&nbsp;&nbsp;&nbsp;
					<span class='css_x-office-document' onclick='document.exporta3.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='Exportar a Excell' title='Exportar a Excell H.Nac.'></span>
                </td>
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
				array('', "color{col}", "1", "center", "center" ),
				array(_COMERCIO_ID, "id", "50", "center", "left" ));
	if ($_SESSION['rol'] < 11 && ($comercId == '39' || $comercId == '633')) array_push($columnas, array('TitOrden', "titord", "90", "center", "left" ));
	if ($_SESSION['rol'] < 2 || strpos($comer, ',')) array_push($columnas, array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left" ));
	if ($_SESSION['grupo_rol'] < 4 || $_SESSION['rol'] == 19) array_push($columnas, array('Empresa', "empresa", "150", "center", "left" ));
	array_push($columnas, array(_REPORTE_REF_COMERCIO, "identificador", "95", "center", "left" ),
					array(_REPORTE_REF_BBVA, "codigo", "75", "center", "left" ),
					array(_COMERCIO_PASARELA, "pasarelaN", "75", "center", "left" ),
					array("Forma Pago", "tipopago", "95", "center", "center" ),
					array(_REPORTE_FECHA, "fecha", "135", "center", "center" ),
					array(_REPORTE_VALOR_INICIAL, "valIni{val}", "65", "center", "right" ),
					array(_REPORTE_FECHA_MOD, "fecha_mod", "135", "center", "center" ),
					array(_REPORTE_VALOR, "valor{val}", "65", "center", "right" ),
					array(_COMERCIO_MONEDA, "moneda", "60", "center", "center"),
					array(_COMERCIO_TASA, "tasaM", "60", "center", "right"),
					array(_COMERCIO_EUROSC, "euroEquiv{val}", "60", "center", "right"),
					array(_COMERCIO_ACTIVITY, "tipoEntorno", "75", "center", "center" ),
					array(_REPORTE_ESTADO, "estad", "83", "center", "center" ));
	array_push($columnas, array(_REPORTE_ERROR, "error", "200", "center", "center" ));
	array_push($columnas, array(_REPORTE_IP, "ip{ip}", "75", "center", "center" ),
					array(_REPORTE_PAIS, "geo{geoip}", "40", "center", "center" ),
					array("T.tarjeta<br>� T.pago", "tjta", "80", "center", "left" ),
					array("Met. Pago", "metodo", "80", "center", "left" ),
					array(_REPORTE_ALCOMERCIO, "pagada", "60", "center", "center"));
	if ($_SESSION['rol'] < 2 || $_SESSION['rol'] == 10 || $_SESSION['rol'] == 19) array_push($columnas, array("N. tarjeta", "tarjetas", "80", "center", "left" ),
	    array("Ident Banco", "identificadorBnco", "60", "center", "left" ));
    
	$querys = tabla( $ancho, 'E', $vista.$from, $orden, $where, $colEsp, $busqueda, $columnas );

	if (strlen($_REQUEST["orden"]) > 0) $orden = $_REQUEST["orden"];
	else $orden = $orden;

	$querCvs = '';
	
	$wherea = " and idmoneda in ('$monedaid') ";
	
// 	$corCreo->todo(43, 'Select final', $vista.$where.$wherea." order by ".$orden);
if (_MOS_CONFIG_DEBUG) 	echo $vista1.$from.$where.$wherea." order by ".$orden;
//  echo(str_replace("\t", "", $vista1.$from.$where.$wherea." order by ".$orden));
//  echo $vista1 . $from . $where . $wherea . " order by " . $orden;
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
	<input type="hidden" name="querys6" value="<?php echo stripslashes($vista1.$from.$where.$wherea)." order by ".$orden ?>">
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
	<input type="hidden" name="querys2" value="<?php echo stripslashes($vista1.$from.$where.$wherea)." order by ".$orden ?>">
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
