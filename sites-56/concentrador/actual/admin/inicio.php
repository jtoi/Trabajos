<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/comercio/comercio.html.php'));
$partes = explode('{corte1}', $admin_mod);
//echo count($partes);

require_once "classes/class.graphic.php";
$PG = new PowerGraphic;

error_log($_SESSION['admin_nom']." / ".ini_get('session.cookie_lifetime')." / ".ini_get('session.gc_maxlifetime'));
error_log("sesionSave=".ini_get("session.save_path"));

global $temp;
global $ip;
//$temp1 = new ps_DB;
$fechaNow = time();
//$incluye = "";
//$mes1 = 1 * date('m', $fechaNow);
//$whereEur = " and moneda = '978'";
//$whereUsd = " and moneda = '840'";
//$whereLib = " and moneda = '826'";

$contenido = $partes[0]. $partes[1]. $partes[2]. $partes[3];
$contenido .= "
		<tr>
		<td width=\"100%\" style=\"padding:0 10px 0 10px;\" align=\"left\"><br>";
$titulo_tarea = '&nbsp;';

$query = "select convert(mensaje USING utf8) from tbl_mensajes where fechaInicio <= $fechaNow and (idcomercio = 'todos' or idcomercio in (". $_SESSION['idcomStr'] .")) and fechaFin >= $fechaNow and activo = 1 order by fechaInicio desc";
// echo $query;
$temp->query($query);
$cant = $temp->num_rows();
$arrMens = $temp->loadResultArray();

if ($_SESSION['comercio'] == 'todos') {
	$titulo = _INICIO_TITLE_ALL;

	$q = "select count(id) total from tbl_comercio where activo = 'S'";
	$temp->query($q);
	$cantTot = $temp->f('total');

	$q = "select count(id) total from tbl_comercio where activo = 'S' and estado = 'P'";
	$temp->query($q);
	$cantProd = $temp->f('total');

	$q = "select count(idtransaccion) total, round(sum(case t.estado
			when 'B' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			when 'V' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			else (t.valor / 100) end),2), moneda
			from tbl_transacciones t, tbl_comercio c
			where t.estado in ('A', 'V', 'B') and t.idcomercio = c.idcomercio and tipoEntorno = 'P' and c.id in (".$_SESSION['idcomStr'].") group by moneda";
	$temp->query($q);
	$cantAcep1 = $temp->loadRowList();
	foreach($cantAcep1 as $item) {
		if ($item[2] == 840) $cantAcepU = array($item[0], $item[1]);
		elseif ($item[2] == 826) $cantAcepL = array($item[0], $item[1]);
		elseif ($item[2] == 124) $cantAcepC = array($item[0], $item[1]);
		else $cantAcep = array($item[0], $item[1]);
	}
	$cantAcepT = $cantAcep[0] + $cantAcepU[0] + $cantAcepL[0] + $cantAcepC[0];

	$q = "select count(idtransaccion) total
			from tbl_transacciones
			where estado in ('D') and tipoEntorno = 'P'";
	$temp->query($q);
	$cantDenT = $temp->f('total');

	$q = "select count(idtransaccion) total, round(sum(case t.estado
			when 'B' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			when 'V' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			else (t.valor / 100) end),2), moneda
			from tbl_transacciones t, tbl_comercio c
			where t.estado in ('A', 'V', 'B') and from_unixtime(t.fecha_mod, '%d-%m-%Y') = '".date("d-m-Y")."'
					and t.idcomercio = c.idcomercio and tipoEntorno = 'P' and c.id in (".$_SESSION['idcomStr'].") group by moneda";
	$temp->query($q);
	$arrSal = $temp->loadRowList();
	foreach($arrSal as $item) {
		if ($item[2] == 978) {
			$ArrDiarEu[0] = $item[0];
			$ArrDiarEu[1] = $item[1];
		} elseif ($item[2] == 124) {
			$ArrDiarCa[0] = $item[0];
			$ArrDiarCa[1] = $item[1];
		} elseif ($item[2] == 840) {
			$ArrDiarUs[0] = $item[0];
			$ArrDiarUs[1] = $item[1];
		} else {
			$ArrDiarLi[0] = $item[0];
			$ArrDiarLi[1] = $item[1];
		}
	}

	$q = "select count(idtransaccion) total, round(sum(case t.estado
			when 'B' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			when 'V' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			else (t.valor / 100) end),2), moneda
			from tbl_transacciones t, tbl_comercio c
			where t.estado in ('A', 'V', 'B') and from_unixtime(t.fecha_mod, '%m-%Y') = '".date("m-Y")."'
					and t.idcomercio = c.idcomercio and tipoEntorno = 'P' and c.id in (".$_SESSION['idcomStr'].") group by moneda";
	$temp->query($q);
	$arrSal = $temp->loadRowList();

	foreach($arrSal as $item) {
		if ($item[2] == 978) {
			$cant1Acep[0] = $item[0];
			$cant1Acep[1] = $item[1];
		} elseif ($item[2] == 840) {
			$cant1AcepU[0] = $item[0];
			$cant1AcepU[1] = $item[1];
		} elseif ($item[2] == 124) {
			$cant1AcepC[0] = $item[0];
			$cant1AcepC[1] = $item[1];
		} else {
			$cant1AcepL[0] = $item[0];
			$cant1AcepL[1] = $item[1];
		}
	}
	$cant1AcepT = $cant1Acep[0] + $cant1AcepU[0] + $cant1AcepL[0] + $cant1AcepC[0];

	$q = "select count(idtransaccion) total
			from tbl_transacciones t, tbl_comercio c
			where t.estado in ('D') and from_unixtime(fecha_mod, '%m-%Y') = '".date("m-Y")."'
					and t.idcomercio = c.idcomercio and tipoEntorno = 'P' and c.id in (".$_SESSION['idcomStr'].")";
//	echo $q;
	$temp->query($q);
	$cant1DenT = $temp->f('total');

	if ($cant > 0) {
		if ($cant == 1) $tituloN = "Noticia";
		else $tituloN = "Noticias";

		$contenido .= "<div id='noticiaA'><div id='noticiaT'>".($tituloN)."</div><ul>";

		for ($i = 0; $i < $cant; $i++) {
			//            print_r ($apse[1]);
			$contenido .= "<li>".str_replace("\n", "<br />", ($arrMens[$i])) ."</li>";
		}

		$contenido .= "</ul><hr /></div>";
	}

	$contenido .= "<div id='noticiaA'>Descarga el <a href='https://www.administracomercios.com/documentos/ManualUsuario.pdf'>Manual de Usuario</a><br />Descarga la <a href='https://www.administracomercios.com/documentos/GuiaUsuario.pdf'>Guía de Usuario para cobros online</a><hr /></div>";

	$PG->reset_values();

	$contenido .= "<strong style='font-size:11px'>" . _INICIO_MES . "</strong><br>"
			. "<strong>" . _INICIO_NUMR_TRANSAC_ACEPT . "</strong>" . $cant1AcepT . "<br>"
					. "<strong>" . _INICIO_NUMR_TRANSAC_DENEG . "</strong>" .$cant1DenT . "<br>"
							. "<strong>" . _INICIO_VALR_TRANSAC . "</strong>&euro; " . number_format($cant1Acep[1],2)." / $ ".number_format($cant1AcepU[1],2)." / &pound; ".number_format($cant1AcepL[1],2)." / CAD ".number_format($cant1AcepC[1],2);
	$mesConv = date('d', mktime(0, 0, 0, date("m")+1, 1,   date("Y"))-1);
	$contenido .= "<br><strong>Promedio diario: </strong><span class='dineroEnt'>&euro; " . number_format(($cant1Acep[1]/date('d')),2)." / $ ". number_format(($cant1AcepU[1]/date('d')),2)." / &pound; ". number_format(($cant1AcepL[1]/date('d')),2)." / CAD ". number_format(($cant1AcepC[1]/date('d')),2)."</span>";
	$contenido .= "<br><strong>Estimado fin de mes: </strong><span class='dineroEnt'>&euro; " . number_format(($cant1Acep[1]/date('d'))*$mesConv,2)." / $ " . number_format(($cant1AcepU[1]/date('d'))*$mesConv,2)." / &pound; " . number_format(($cant1AcepL[1]/date('d'))*$mesConv,2)." / CAD " . number_format(($cant1AcepC[1]/date('d'))*$mesConv,2)."</span>";
	$contenido .= "<br><strong>Hoy cant. Transacciones: </strong><span class='dineroEnt'>&euro; " . $ArrDiarEu[0]." / $ " . $ArrDiarUs[0]." / &pound; " . $ArrDiarLi[0]." / CAD " . $ArrDiarCa[0]."</span>";
	$contenido .= "<br><strong>Hoy valor: </strong><span class='dineroEnt'>&euro; " . number_format($ArrDiarEu[1],2)." / $ " . number_format($ArrDiarUs[1],2)." / &pound; " . number_format($ArrDiarLi[1],2)." / CAD " . number_format($ArrDiarCa[1],2)."</span>";
	//	if ($_SESSION['id'] == 10) {
	//		$contenido .= "<br><strong>Estimado a cobrar: </strong><span class='dineroEnt'> " . number_format((($cant1Acep[1]/date('d'))*$mesConv)*0.007,2)."</span>";
	//		$contenido .= "<br><strong>A cobrar: </strong><span class='dineroEnt'> " . number_format($cant1Acep[1]*0.007,2)."</span>";
	//	}
	$contenido .= "<br><br>";
	$contenido 	.= "<table class='Info' cellpadding=\"2\" cellspacing=\"2\" align=\"center\">"
			. "<tr class='tablTit'><td>". _INICIO_COMERCIO ."</td><td>". _INICIO_CANT_TRANSACCIONES ."<br>&euro; / $ / &pound; / CAD</td><td>". _INICIO_VALOR ."<br>&euro; / $ / &pound; / CAD</td></tr>";

	$q = "select count(idtransaccion), sum(case t.estado
			when 'B' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			when 'V' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			else (t.valor / 100) end), concat(c.prefijo_trans, ' - ', c.nombre) nombre, moneda,
			sum(format((case t.estado
			when 'B' then if(fecha_mod < fechaPagada,(-(1) * euroEquivDev),euroEquiv)
			when 'V' then if(fecha_mod < fechaPagada,(-(1) * euroEquivDev),euroEquiv)
			when 'A' then (euroEquiv)
			else '0.00' end),2))
			from tbl_transacciones t, tbl_comercio c
			where c.idcomercio = t.idcomercio
			and from_unixtime(fecha_mod, '%m-%Y') = '".date("m-Y")."'
					and t.estado in ('A', 'V', 'B') and tipoEntorno = 'P'
					group by c.id, moneda order by c.nombre";
	// 	echo $q;
	$temp->query($q);
	$arr = $temp->loadRowList();
// 	print_r($arr);
	$comerc = $idc = '';
	$sumaCa = $cuentaCa = $sumaDo = $cuentaDo = $sumaLi = $cuentaLi = $cuentaEu = $sumaEu = $i = $totE = 0;
	$PG->x = $PG->y = array();
	$PG->y = $PG->x = array_fill(0, count($arr), 0);
	for ($j=0;$j<count($arr);$j++) {
		if ($comerc != $arr[$j][2] && $comerc != '') {
			$contenido .= "<tr class='tablCont'><td align=\"left\">$comerc</td><td align=\"center\">$cuentaEu / $cuentaDo / $cuentaLi / $cuentaCa</td>
			<td align=\"center\">".number_format($sumaEu, 2)." / ".number_format($sumaDo, 2)." / ".number_format($sumaLi, 2)." / ".number_format($sumaCa, 2)."</td></tr>";
			if ($totE > 0) {
				asort($PG->y, 3);
				foreach ($PG->y as $key => $val) {
					if ($PG->y[$key] < $totE) {
						$PG->y[$key] = $totE;
						$PG->x[$key] = $comerc;
						break 1;
						$h = count($PG->x);
					}
				}
			}
			$comerc = $arr[$j][2];
			$sumaCa = $cuentaCa = $sumaDo = $cuentaDo = $sumaLi = $cuentaLi = $cuentaEu = $sumaEu = $totE = 0;
		} elseif ($comerc != $arr[$j][2] && $comerc == '') {
			$comerc = $arr[$j][2];
		}
		$totE =+ $arr[$j][4];
		switch ($arr[$j][3]) {
			case '124':
				$sumaCa = $arr[$j][1];
				$cuentaCa = $arr[$j][0];
				break;
			case '840':
				$sumaDo = $arr[$j][1];
				$cuentaDo = $arr[$j][0];
				break;
			case '826':
				$sumaLi = $arr[$j][1];
				$cuentaLi = $arr[$j][0];
				break;
			default:
				$sumaEu = $arr[$j][1];
				$cuentaEu = $arr[$j][0];
				break;
		}
	}
	$contenido .= "<tr class='tablCont'><td align=\"left\">$comerc</td><td align=\"center\">$cuentaEu / $cuentaDo / $cuentaLi / $cuentaCa	</td>
	<td align=\"center\">".number_format($sumaEu, 2)." / ".number_format($sumaDo, 2)." / ".number_format($sumaLi, 2)." / ".number_format($sumaCa, 2)."</td></tr>";


// 	$PG->title     = _SALES_X_MES;
	$PG->title     = "";
	$PG->axis_x    = _TIENDA_TIT;
	$PG->axis_y    = 'Euro$';
	$PG->type      = 5;
	$PG->skin      = 4;
	$PG->credits   = 0;

	$contenido .= "<tr><td colspan='3'><img src='classes/class.graphic.php?" . $PG->create_query_string() . "' border='1' alt='' width='420' />
			</td></tr></table><br><br>";
	$PG->reset_values();

	//	Histórico
	$contenido .= "<strong>" . _INICIO_CANT_TOTAL . "</strong>" . $cantTot . "<br>"
			. "<strong>" . _INICIO_CANT_PRODUCC . "</strong>" . $cantProd . "<br>"
			. "<strong>" . _INICIO_CANT_DESARR . "</strong>" . ($cantTot-$cantProd) . "<br><br>"
			. "<strong style='font-size:11px'>" . _INICIO_TODO . "</strong><br>"
			. "<strong>" . _INICIO_NUMR_TRANSAC_ACEPT . "</strong>" . $cantAcepT . "<br>"
			. "<strong>" . _INICIO_NUMR_TRANSAC_DENEG . "</strong>" . $cantDenT . "<br>"
			. "<strong>" . _INICIO_VALR_TRANSAC . "</strong>&euro; " . number_format($cantAcep[1],2) . " / $ ".number_format($cantAcepU[1],2)." / &pound; ".number_format($cantAcepL[1],2)." / CAD ".number_format($cantAcepC[1],2)."<br><br>"
			. "<table class='Info' cellpadding=\"2\" cellspacing=\"2\" align='center'>"
			. "<tr class='tablTit'><td>". _INICIO_COMERCIO ."</td><td>". _INICIO_CANT_TRANSACCIONES ."<br>&euro; / $ / &pound; / CAD</td><td>". _INICIO_VALOR ."<br>&euro; / $ / &pound; / CAD</td></tr>";

	$q = "select count(idtransaccion), sum(case t.estado
			when 'B' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			when 'V' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			else (t.valor / 100) end), concat(c.prefijo_trans, ' - ', c.nombre) nombre, moneda,
			sum(format((case t.estado
			when 'B' then if(fecha_mod < fechaPagada,(-(1) * euroEquivDev),euroEquiv)
			when 'V' then if(fecha_mod < fechaPagada,(-(1) * euroEquivDev),euroEquiv)
			when 'A' then (euroEquiv)
			else '0.00' end),2))
			from tbl_transacciones t, tbl_comercio c
			where c.idcomercio = t.idcomercio
			and t.estado in ('A', 'V', 'B') and tipoEntorno = 'P'
			group by c.id, moneda order by c.nombre";
	$temp->query($q);
	$arr = $temp->loadRowList();
	//	print_r($arr);
	$comerc = $idc = '';
	$sumaCa = $cuentaCa = $sumaDo = $cuentaDo = $sumaLi = $cuentaLi = $cuentaEu = $sumaEu = $i = $totE = 0;
	$PG->x = $PG->y = array_fill(0,count($arr),0);
	for ($j=0;$j<count($arr);$j++) {
		if ($comerc != $arr[$j][2] && $comerc != '') {
			$contenido .= "<tr class='tablCont'><td align=\"left\">$comerc</td><td align=\"center\">$cuentaEu / $cuentaDo / $cuentaLi / $cuentaCa</td>
			<td align=\"center\">".number_format($sumaEu, 2)." / ".number_format($sumaDo, 2)." / ".number_format($sumaLi, 2)." / ".number_format($sumaCa, 2)."</td></tr>";
			if ($totE > 0) {
				asort($PG->y, 3);
				foreach ($PG->y as $key => $val) {
					if ($PG->y[$key] < $totE) {
						$PG->y[$key] = $totE;
						$PG->x[$key] = $comerc;
						break 1;
						$h = count($PG->x);
					}
				}
			}
			//			if (($sumaEu + $sumaDo + $sumaLi) > 0) {
			//				$PG->x[$i] = $comerc;
			//				$PG->y[$i++] = $sumaEu + $sumaDo + $sumaLi;
			//			}
			$comerc = $arr[$j][2];
			$sumaCa = $cuentaCa = $sumaDo = $cuentaDo = $sumaLi = $cuentaLi = $cuentaEu = $sumaEu = $totE = 0;
		} elseif ($comerc != $arr[$j][2] && $comerc == '') {
			$comerc = $arr[$j][2];
		}
		$totE =+ $arr[$j][4];
		switch ($arr[$j][3]) {
			case '124':
				$sumaCa = $arr[$j][1];
				$cuentaCa = $arr[$j][0];
				break;
			case '840':
				$sumaDo = $arr[$j][1];
				$cuentaDo = $arr[$j][0];
				break;
			case '826':
				$sumaLi = $arr[$j][1];
				$cuentaLi = $arr[$j][0];
				break;
			default:
				$sumaEu = $arr[$j][1];
				$cuentaEu = $arr[$j][0];
				break;
		}
	}

// 	$PG->title     = _VENTAS_X_TIENDA;
	$PG->title     = '';
	$PG->axis_x    = _TIENDA_TIT;
	$PG->axis_y    = 'Euro$';
	$PG->type      = 5;
	$PG->skin      = 4;
	$PG->credits   = 0;

	$contenido .= "<tr><td colspan='3'><img src='classes/class.graphic.php?" . $PG->create_query_string() . "' border='1' alt='' width='420' />
			</td></tr></table><br><br><span class='conect'>";

	//Conectados
	$query = "select nombre from tbl_admin where fecha_visita + 1200 >= ".time()." and idadmin != ".$_SESSION['id'];
	$temp->query($query);

	if ($temp->num_rows() > 0) {
		$contenido .= _INICIO_CONECTADOS."</span>";
		while ($temp->next_record()) {
			$contenido .= "<br /><span class='conect2'>".utf8_decode($temp->f("nombre"))."</span>";
		}

	} else {
		$contenido .= _INICIO_NOCONECTADOS."</span>";
	}


} else {
	$titulo = _INICIO_TITLE_MENOS;

	$query = "select idcomercio, fechaMovUltima,
			case estado when 'D' then '"._COMERCIO_ACTIVITY_DES."' else '"._COMERCIO_ACTIVITY_PRO."' end as estado
			from tbl_comercio where id in ({$_SESSION['idcomStr']})";
	$temp->query($query);
	$fechaMov = $temp->f('fechaMovUltima');
	$estad = "";
	if (strpos($_SESSION['comercio'],",")) {
		$i=0;
		$estad = "<br />";
		$arrids = $temp->loadResultArray(0);
		$arrest = $temp->loadResultArray(2);
		foreach ($arrids as $ids) {
			$estad .= "$ids: ".$arrest[$i++]."<br />";
		}
	} else {
		$estad = $temp->f('estado');
	}

	$q = "select count(idtransaccion) total from tbl_transacciones t, tbl_comercio c where c.idcomercio = t.idcomercio
	and c.id in ({$_SESSION['idcomStr']}) and t.estado in ('D') and tipoEntorno = 'P'";
	$temp->query($q);
	$nuTrDenTot = $temp->f('total');
	$q .= " and fecha between ".mktime(0, 0, 0, date("m"), 1, date("Y"))." and $fechaNow";
	$temp->query($q);
	$nuTrDenMes = $temp->f('total');

	$q = "select count(idtransaccion) total from tbl_transacciones t, tbl_comercio c where c.idcomercio = t.idcomercio
	and c.id in ({$_SESSION['idcomStr']}) and t.estado in ('A', 'V', 'B') and tipoEntorno = 'P'";
	$temp->query($q);
	$nuTrAceTot = $temp->f('total');
	$q .= " and fecha between ".mktime(0, 0, 0, date("m"), 1, date("Y"))." and $fechaNow";
	$temp->query($q);
	$nuTrAceMes = $temp->f('total');

	$query = "select (sum(valor) / 100) valor, m.moneda
	from tbl_transacciones t, tbl_moneda m, tbl_comercio c
	where c.idcomercio = t.idcomercio
	and c.id in ({$_SESSION['idcomStr']}) and t.estado in ('A', 'V', 'B') and tipoEntorno = 'P'
	and t.moneda = m.idmoneda group by moneda";
	$temp->query($query);
	$arra = $temp->loadObjectList();

	$query = "select (sum(valor) / 100) valor, m.moneda
			from tbl_transacciones t, tbl_moneda m, tbl_comercio c
			where t.estado in ('A', 'V', 'B') and t.fecha between ".mktime(0, 0, 0, date("m"), 1, date("Y"))." and $fechaNow
			and c.idcomercio = t.idcomercio
			and c.id in ({$_SESSION['idcomStr']}) and tipoEntorno = 'P'
			and t.moneda = m.idmoneda group by moneda";
	$temp->query($query);
	$arraM = $temp->loadObjectList();

	
	if ($cant > 0 && $_SESSION['comercio'] != 'todos') {
		if ($cant == 1) $tituloN = "Noticia.";
		else $tituloN = "Noticias.";

		$contenido .= "<div id='noticiaA'><div id='noticiaT'>$tituloN</div><ul>";

		for ($i = 0; $i < $cant; $i++) {
			//            print_r ($apse[1]);
			$contenido .= "<li>".str_replace("\n", "<br />", utf8_decode($arrMens[$i])) ."</li>";
		}

		$contenido .= "</ul><hr /></div>";
	}

	$contenido .= "<div id='noticiaA'>Descarga el <a href='https://www.administracomercios.com/documentos/ManualUsuario.pdf'>Manual de Usuario</a><br />Descarga la <a href='https://www.administracomercios.com/documentos/GuiaUsuario.pdf'>Guía de Usuario para cobros online</a><hr /></div>";

	$contenido .= "<strong>" . _INICIO_COMERCIO_NUMR . "</strong>" . str_replace(',', ', ', $_SESSION['comercio']) . "<br>"
			. "<strong>" . _INICIO_COMERCIO_MODO . "</strong>" . $estad . "<br>"
			. "<strong>" . _INICIO_FECHA_MODO . "</strong>" . date('d/m/y H:i', $fechaMov) . "<br><br>"
			. "<strong>" . _INICIO_NUMR_TRANSAC_ACEPT . "</strong>" . $nuTrAceTot . "<br>"
			. "<strong>" . _INICIO_NUMR_TRANSAC_DENEG . "</strong>" . $nuTrDenTot . "<br>"
			. "<strong>" . _INICIO_VALR_TRANSAC . "</strong>";
	for ($i = 0; $i < count($arra); $i++) {
		$contenido .= number_format($arra[$i]->valor, 2). " ". $arra[$i]->moneda ."&nbsp;&nbsp;&nbsp;";
	}
	$contenido .= "<br><br>"
			. "<strong style='font-size:11px'>" . _INICIO_MES . "</strong><br>"
			. "<strong>" . _INICIO_NUMR_TRANSAC_ACEPT . "</strong>" . $nuTrAceMes . "<br>"
			. "<strong>" . _INICIO_NUMR_TRANSAC_DENEG . "</strong>" . $nuTrDenMes . "<br>"
			. "<strong>" . _INICIO_VALR_TRANSAC . "</strong>";
	for ($i = 0; $i < count($arraM); $i++) {
		$contenido .= number_format($arraM[$i]->valor, 2). " ". $arraM[$i]->moneda ."&nbsp;&nbsp;&nbsp;";
	}
}



$contenido .= "<br><br>
		</td>
		</tr>";
$temp->query("select from_unixtime(o.fecha, '%d/%m/%Y %H:%i'), o.so, o.browser, o.ip, p.nombre, o.id from tbl_adminSO o, tbl_paises p where o.idpais = p.id and o.idadmin = ".$_SESSION['id']." order by o.fecha desc limit 0,10");
$arrEnt = $temp->loadRowList();

$timeZ = date_default_timezone_get();
$tab = '';
for ($i=0; $i<count($arrEnt); $i++){
	$tab .= "<tr class='".$arrEnt[$i][5]." nmark'><td>".$arrEnt[$i][0]."</td><td>".$arrEnt[$i][1]."</td><td>".$arrEnt[$i][2]."</td><td>".$arrEnt[$i][3]."</td><td>".$arrEnt[$i][4]."</td></tr>";
}
// if ($_SESSION['grupo_rol'] <= 2) 
	$partes[13] = "<span class='secur1' id='secur'>Ver sus &uacute;ltimas entradas a la plataforma:</span><div id='entradas' style='display:none;'><table border='0' width='65%' align='center' id='tbEntr' ><tr><th>Fecha Hora</th><th>SO</th><th>Navegador</th><th>IP</th><th>País</th></tr>$tab</table></div>";
// else 
// 	$partes[13] = '';
	
$contenido .= $partes[11]. $partes[12]. $partes[13]. $partes[14];
date_default_timezone_set($timeZ);

//javascript
$javascript = "<script language=\"JavaScript\" type=\"text/javascript\">";
$javascript .= $java;
if ($alerta)
	$javascript .= "alert('$alerta');";
$javascript .= "</script>";
$ancho = 800;
//echo strlen($contenido);

$contenido = str_replace('{titulo}', $titulo, $contenido);
$contenido = str_replace('{tabed}', '', $contenido);
$contenido = str_replace('{campo}', $campo_pase, $contenido);
$contenido = str_replace('{titulo_tarea}', $titulo_tarea, $contenido);
$contenido = str_replace('{ancho_tabla}', $ancho, $contenido);
$contenido = str_replace('{_FORM_SEND}', _FORM_SEND, $contenido);
$contenido = str_replace('{_FORM_CANCEL}', _FORM_CANCEL, $contenido);
$contenido = str_replace('{javascript}', $javascript, $contenido);
$contenido = str_replace('{anchoCelda}', ($ancho - 14), $contenido);

echo $contenido;

?>

<script language="JavaScript" type="text/javascript">
function verfEntrada(){
	$.ajax({
		type: 'POST',
		url: '../ejec.php',
		dataType: 'text',
		contentType: 'application/x-www-form-urlencoded; charset=iso-8859-1',
		data: ({
			fucn:'cargaAdminSO',
			id:<?php echo $_SESSION['id']; ?>
		}),
		success: function (data) {
			var datos = eval('(' + data + ')');
			$("#entradas").html(datos.salida);
		}
	});

}

$(document).ready(function(){
	$("#secur").click(function(){
		if($("#entradas").css('display') == 'none') {
			$("#entradas").css('display','block');
			$("#secur").html('Si no reconoce alguna de estas entradas por favor haga click sobre ella.').toggleClass('secur1 secur2');
		}
	});

	$("#tbEntr tr").click(function(){
		if (confirm("Se procedera a bloquear la IP asociada a la entrada. Está seguro?")) {
			var elem = $(this).attr('class').replace(' mark', '');
			$("#entradas").html("<div style='font-size=12px !important;margin-bottom:20px;' >Procesando los datos... Espere unos minutos</div>");
			$.ajax({
				type: 'POST',
				url: '../ejec.php',
				dataType: 'text',
				contentType: 'application/x-www-form-urlencoded; charset=iso-8859-1',
				data: ({
					fucn:'entrada',
					elem:elem
				}),
				success: function (data) {
					var datos = eval('(' + data + ')');
					alert (datos.salida);
					window.open('index.php?componente=comercio&pag=inicio','_self');
					// location.reload();
					// verfEntrada();
				}
			});
		}
	});

	$("#tbEntr tr").mouseenter(function(){
		$(this).removeClass("nmark");
		$(this).addClass("mark");
	});

	$("#tbEntr tr").mouseleave(function(){
		$(this).removeClass("mark");
		$(this).addClass("nmark");
	});
});
</script>