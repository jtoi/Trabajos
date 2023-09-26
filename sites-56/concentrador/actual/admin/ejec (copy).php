<?php define( '_VALID_ENTRADA' , 1);
include_once( '../../../configuration.php' );
require_once("../../classes/SecureSession.class.php");
$Session = new SecureSession(_TIEMPOSES);
include_once( '../../classes/entrada.php' );
require_once( '../../../include/mysqli.php' );
require_once( '../../../include/hoteles.func.php' );
require_once( '../../../include/correo.php' );
include( "../../lang/spanish.php" );
include( "../../adminis.func.php" );
$temp = new ps_DB();
$ent = new entrada;
$cor = new correo();

$d = $_REQUEST;
if (_MOS_CONFIG_DEBUG) {
// $d['estado'] = 1;
// $d['fun']='revoper';
// $d['cod']='F0978';
// $d['com']='126350306709';
// $d['cier']='1930';
// $d['tipo']='t';
// $d['pas']='72';
// $d['email']='jtoirac@gmail.com';
// $d['fecha1'] = '1/2/2017';
// $d['fecha2'] = '7/2/2017';
}
$desc = "pag = ejecutaComer<br>";
foreach($_REQUEST as $nom => $valor) {
	if (is_array($valor)) $valor = implode (', ', $valor);
	$desc .= "$nom = ".htmlspecialchars($valor, ENT_QUOTES)."<br>";
}
//$desc = "pag = ejecutaComer ".implode('<br>', $d);
if ($_SESSION['id']) {
	$query = "insert into tbl_baticora (idadmin, texto, fecha) values (".$_SESSION['id'].", '$desc', ".time().")";
	$temp->query($query);
} else echo "<script language='text/javascript'>window.open('index.php?componente=core&pag=logout', '_self')</script>";


if ($d['fun'] == 'cheqNsec') {

	$temp->query("select count(idPasarela) total from tbl_pasarela where secure = 0 and idPasarela in (".implode(',', $d['cod']).")");
	echo json_encode(array("cont"=>$temp->f('total'), "error"=>""));

} elseif ($d['fun'] == 'revoper') {//de la pag pago, revisa que el cód de la oper puesto por los clientes no esté repetido
	$sal = false;
	//para la tabla de las reservas 
	$temp->query("select count(id_reserva) total from tbl_reserva r, tbl_comercio c where r.id_comercio = c.idcomercio and c.id = '".$d['com']."' and codigo = '". $d['cod'] ."'");
	if ($temp->f('total') == 0) {
		$sal = 'true';
		
		//para la tabla de las operaciones
		$temp->query("select count(idtransaccion) total from tbl_transacciones r, tbl_comercio c where r.idcomercio = c.idcomercio and c.id = '".$d['com']."' and r.identificador = '". $d['cod'] ."'");
		if ($temp->f('total') > 0) $sal = 'false';
	}
	
	echo json_encode(array("cont"=>$sal, "error"=>""));
	
} elseif ($d['fun'] == 'envcorr') {//Envia correos de aviso a los económicos de los comercios cuando el cierre fué subido
	
	$cc = false; $correoMi = '';
	foreach ($d as $key => $value) {
		$correoMi .= "$key => $value<br>";
	}
	
	if ($d['tipo'] == 'c') {
		$dias = 30;
		$idcor = '55';
		$query = "update tbl_cierreTransac set envcorreo = 1 where idcierre = ".$d['cier'];
		$message = "tiene disponible para la descarga, el";
		$titulo = "";
		$asunto = "Notificación de {cierre}";
		$textoCor = "Estimado (a) {usuario}:<br><br>El cierre contable correspondiente al per&iacute;odo a liquidar, est&aacute; disponible en el Administrador de Comercios. Usted puede descargarlo accediendo con su nombre de usuario y contrase&ntilde;a a trav&eacute;s de la opci&oacute;n Comercio/Ver cierres.<br><br>Por favor, recuerde enviar la(s) factura(s) al buz&oacute;n <a href='mailto:administrativa@bidaiondo.com'>administrativa@bidaiondo.com</a>.<br><br>Administrador de Comercios<br>Bidaiondo S.L.";
	} else {
		$dias = 7;
		$idcor = '56';
		$query = "update tbl_amfTransf set envcorreo = 1 where idfactura in (select id from tbl_factura where idcierre = ".$d['cier'].")";
		$message = "ha recibido la notificaci&oacute;n de transferencia bancaria correspondiente al";
		$titulo = "Transferencia Bancaria";
		$asunto = "Notificación de transferencia bancaria correspondiente al {cierre}";
		$textoCor = "Estimado (a) {usuario}:<br><br>La copia de la transferencia bancaria correspondiente al {cierre}, est&aacute; disponible en el Administrador de Comercios. Usted puede descargarla accediendo con su nombre de usuario y contrase&ntilde;a a trav&eacute;s de la opci&oacute;n Comercio/Ver cierres.<br><br>Administrador de Comercios<br>Bidaiondo S.L.";
	}
	
	$correoMi .= "dias = $dias<br>idcor = $idcor<br>query = $query<br>message = $message<br>titulo = $titulo<br>asunto = $asunto<br>textoCor = $textoCor<br>";
	
	//saco el nombre del cierre
	$q = "select cierre from tbl_cierreTransac where idcierre = ".$d['cier'];
	$correoMi .= "$q<br>";
	$temp->query($q);
	$nombCierre = $temp->f('cierre');
	$message = "AVISO !!! Su comercio $message Cierre No. $nombCierre";
	$correoMi .= "message = $message<br>";
	
	//determino el comercio del cierre
	$q = "select idcomercio from tbl_cierreTransac where idcierre = ".$d['cier'];
	$correoMi .= "$q<br>";
	$temp->query($q);
	$com = $temp->f('idcomercio');
	$correoMi .= "com = $com<br>";	
	
	//busco el nombre o los nombres de los económicos 
	$q = "select CONVERT(CAST(e.nombre as BINARY) USING latin1) nomb, e.email from tbl_cierreTransac c, tbl_economicos e where e.idcomercio = c.idcomercio and c.idcierre = ".$d['cier'];
	$correoMi .= "$q<br>";
	$temp->query($q);
	
	if ($temp->getErrorMsg()) {
		enviamiError($correoMi.$temp->getErrorMsg());
	}
	
	while($temp->next_record()) {
		$cor->to($temp->f('email'));
		$correoMi .= "Enviado correo a ".$temp->f('email')."<br>";
		$cor->todo($idcor, str_replace("{cierre}", $nombCierre, $asunto), str_replace("{usuario}", $temp->f('nomb'), str_replace("{cierre}", $nombCierre, $textoCor)));
	} 
	
	$cor->destroy();
	
	//inserto en la tabla de mensajes el aviso del cierre para que sea visto por el comercio
	$ahora =mktime(0, 0, 0, date("m"), date("d"), date("Y"));
	$futuro = mktime(0, 0, 0, date("m"), date("d")+$dias, date("Y"));
	$q = "insert into tbl_mensajes values (null, '$com', '$message', $ahora, $futuro, $ahora, '1')";
	$temp->query($q);
	if ($temp->getErrorMsg()) {
		enviamiError($correoMi.$temp->getErrorMsg());
	}
	
	//actualizo la tabla de cierres para que no se vuelva a enviar un aviso de este cierre
	$temp->query($query);
	if ($temp->getErrorMsg()) {
		enviamiError($correoMi.$temp->getErrorMsg());
	}

	//se inserta en la tabla traza 
	$temp->query("insert into tbl_traza (titulo,traza,fecha) values ('Envío de Cierre','".  $correoMi ."',".time().")");
	
	echo json_encode(array("cont"=>"Se ha enviado el aviso de Subida de Cierre", "error"=>''));
	
} elseif ($d['fun'] == 'cargcantt') {
	if ($d['num'] > 0 && $d['cie'] > 0) {
		$fes = to_unix($d['fes'].' 23:59:59');
		$q = "update tbl_cierreTransac set numFacturas = ".$d['num']." where idcierre = ".$d['cie'];
		$q = "update tbl_cierreTransac set idtransaccion = '".$d['tr']."', idcomercio = 
				(select cierrePor from tbl_comercio where idcomercio = ".$d['comerc']."), transferir = '".$d['tranfi']."',
				consolidado = '".$d['acumu']."', numFacturas = '".$d['num']."', cierre = '".$d['cr']."', valor = '".$d['valor']."', 
				idmoneda = '".$d['moneda']."', fechaCierre = $fes, observaciones = '".utf8_encode($d['observa'])."' 
			where idcierre = ".$d['cie'];
		$temp->query($q);
		echo json_encode(array("cont"=>$d['cie'], "error"=>''));
	}
} elseif ($d['fun'] == 'quiTR') {
	if ($d['tipo'] == 'T') {
		$q = "delete from tbl_amfTransf where id = ".$d['val'];
	} else $q = "delete from tbl_factura where id = ".$d['val'];
		$temp->query($q);
	echo json_encode(array("cont"=>'ok', "error"=>''));
	
} else if ($d['fun'] == 'carcom') {//Carga los cierres de un comercio en dependencia de si se manda el id o el idcomercio
	
	if (strlen($d['com']) > 5) {
		$q = "select id from tbl_comercio where idcomercio = ".$d['com'];
// 		$error .= $q;
		$temp->query($q);
		$idcom = $temp->f('id');
	} else $idcom = $d['com'];
	
	$q = "select c.idcierre id, convert(cast(convert(c.cierre using utf8) as binary) using latin1) nombre from tbl_cierreTransac c ".
			"where c.idcomercio = '".$idcom."' ".
				"and c.idcierre not in ".
						"(select f.idcierre from tbl_amfTransf t, tbl_colCierreFactura f ".
							"where f.idfactura = t.idfactura) order by c.fecha desc";
// 	$error .= $q;
	$temp->query($q);
	$error = $temp->num_rows();
	if ($temp->num_rows() == 0) $arrSal = '';
	else $arrSal = $temp->loadAssocList();
// 	$arrSal = array_map('utf8_encode', $arrSal);
// 	print_r($arrSal);
	echo utf8_encode(json_encode(array("cont"=>$arrSal, "error"=>$error)));
	
} else if ($d['fun'] == 'cambps') {
	
	$pass = str_replace(",,", ",", $d['pas']);
	
	if (strlen($d['com']) > 0) {
		$q = "select permnsec from tbl_comercio where id = ".$d['com'];
		$temp->query($q);
		$pan = $temp->f('permnsec');

		if (strlen($d['pas']) == 0){
			$q = "select idPasarela id, nombre from tbl_pasarela where tipo = 'P' and activo = 1 order by nombre";
		} else {
			$q = "select group_concat(p.idPasarela) id, case secure when 1 then 'Segura' else 'NO Segura' end nombre from tbl_pasarela p where p.idPasarela in ({$pass}) ";
			if ($d['pag'] == 'N' && $pan == 0) $q .= " and p.secure = 1 ";
			$q .= "group by secure order by idPasarela";
		}
		$temp->query($q);
		$arrSal = $temp->loadAssocList();
		
		// verifica si el comercio seleccionado tiene permitido el cambio a Eur
		$q = "select operEur from tbl_comercio where id = ".$d['com'];
		$temp->query($q);
		$euro = $temp->f("operEur");
		
	} else $q = "No aparece el comercio, comuníquelo por favor a Bidaiondo";
	$q = '';

	echo utf8_encode(json_encode(array("cont"=>$arrSal, "error"=>$q, "euro" => $euro)));
	
} elseif($d['fun'] == 'pagoEur') { //extrae la tasa de cambio para los comercios que tienen permitido el cambio de USD a EUR
	$q = "select tasa from tbl_tasaComercio where idcomercio = ".$d['com']." and monedaBas = 840 and monedaCamb = 978 order by fecha desc limit 0,1";
	$temp->query($q);
	$tasa = $temp->f('tasa');
	
	if ($tasa > 0) {$q = "";}
	else {$q = "Error: Su comercio no tiene definido Tasa de cambio del USD al EUR";}
	
	echo utf8_encode(json_encode(array("tasa"=>$tasa, "error"=>$q)));
	
} else if ($d['fun'] == "populavip") { //Rellena el dropdown con los usuario vip del copmercio
	$q = "select id_transaccion, nombre, email, servicio, referencia from tbl_reserva r, tbl_transacciones t where t.idtransaccion = r.id_transaccion and id_reserva = ".$d['vip'].
			" and fecha_exp >= ".date('ym');
// 	echo $q;
	$temp->query($q);
	$arrSal = $temp->loadAssocList();
	echo utf8_encode(json_encode(array("cont"=>$arrSal, "tex"=>$q)));
} else if ($d['fun'] == 'recCierre') { //carga los cierres en la pï¿½gina de poner transferencias
	$q = "select idcierre, cierre from tbl_cierreTransac where idcomercio = '{$d['com']}'
			order by fechaCierre desc limit 0,20";
	$temp->query($q);
	$arrSal = $temp->loadAssocList();
	echo json_encode(array("cont"=>$arrSal, "tex"=>$q));
} else if ($d['fun'] == 'datos') { //Carga los datos de la p&aacute;gina datos

	/*Convierto las fechas al inicio y final del dï¿½a, calculo los intervalos*/
	$arrFec1 = explode('/',$d['fecha1']);
	$arrFec2 = explode('/',$d['fecha2']);
	$fecha1Obj = new DateTime($arrFec1[2].'-'.$arrFec1[1].'-'.$arrFec1[0].' 00:00:00');
	$fecha2Obj = new DateTime($arrFec2[2].'-'.$arrFec2[1].'-'.$arrFec2[0].' 23:59:59');
	$fecha1 = mktime(0, 0, 0, $arrFec1[1], $arrFec1[0], $arrFec1[2]);
	$fecha2 = mktime(23, 59, 59, $arrFec2[1], $arrFec2[0], $arrFec2[2]);
	$datetime1 = new DateTime($arrFec1[2].'-'.$arrFec1[1].'-'.$arrFec1[0]);
	$datetime2 = new DateTime($arrFec2[2].'-'.$arrFec2[1].'-'.$arrFec2[0]);
	$difFec = $fecha2-$fecha1;
	$inter = date_diff($datetime1, $datetime2);
	$dif = date_diff($fecha1Obj, $fecha2Obj);
	$salida = "";

	if ((date('j',$fecha2)-date('j',$fecha1)) == 0 ){//la dif es menor de un dï¿½a, el intervalo es de horas
		//$salida .= "horas";
		$int = floor($difFec/3600)+1;
		if ($fecha2 > time()) $j = date('G');
		else $j = 24;
		$tit = "An&aacute;lisis del d&iacute;a";
		$inSql = "%H";
	} elseif ((date('n',$fecha2)-date('n',$fecha1)) == 0) {//la dif es mayor de un dï¿½a, el intervalo es de dias
		//$salida .= "dias";
		$int = $inter->format('%a')+1;
		if ($fecha2 > time()) $j = date('j');
		else {
			$j = $dif->format('%a')+1;
		}
		$tit = "An&aacute;lisis del mes";
		$inSql = "%d/%m/%y - %a";
	} else {//el intervalo es de meses
		// $salida .= "meses";
		$int = $inter->format('%m')+1;
		if ($fecha2 > time()) $j = date('n');
		else {
			$j = $dif->format('%m')+1;
		}
		$tit = "An&aacute;lisis de varios meses";
		$inSql = "%m/%y - %b";
	}

	$elem = "case t.estado when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa) when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa) when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa) when 'A' then (t.valor/100/t.tasa) else '0.00' end";
	$elemBR = "(t.valor_inicial/100/t.tasa)";
	$salida .= "<span class='titukl'>An&aacute;lisis en el per&iacute;odo de {$d['fecha1']} 00:00:00 a {$d['fecha2']} 23:59:59</span>"
				."<span class='momto'>Actualizada en: ".date('d/m/Y H:i:s')."</span>";

	/*	Estimados	*/
	if ($d['estim'] == 1) {
	$salida .= $tiempo;
	$salida .= "<span class='titule'>Estimado</span>";
		$q = "select sum($elem) / $j * $int estimado
			FROM tbl_transacciones t
			where estado in ('A','V','B','R')
			and tipoEntorno = 'P'
			and fecha_mod between $fecha1 and $fecha2";
			$temp->query($q);
	if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
	$arrVal = $temp->loadAssocList();
	// $salida .= $q;
	// print_r($arrVal);
	$salida .= "<div id='acoge'>";
	$salida .= "<ul class='respta' style='width:100px'><li>".formatea_numero($arrVal[0]['estimado'])."</li></ul>";
	$salida .= "</div>";
	}


	/*	Estimado Bruto	*/
	if ($d['estimBR'] == 1) {
	$salida .= $tiempo;
	$salida .= "<span class='titule'>Estimado Bruto</span>";
		$q = "select sum($elemBR) / $j * $int estimado
			FROM tbl_transacciones t
			where estado in ('A','V','B','R')
			and tipoEntorno = 'P'
			and fecha_mod between $fecha1 and $fecha2";
			$temp->query($q);
	if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
	$arrVal = $temp->loadAssocList();
	// $salida .= $q;
	// print_r($arrVal);
	$salida .= "<div id='acoge'>";
	$salida .= "<ul class='respta' style='width:100px'><li>".formatea_numero($arrVal[0]['estimado'])."</li></ul>";
	$salida .= "</div>";
	}

	/*Valores, cantidad de operaciones y porcientos por comercios en el intervalo de tiempo*/
	if ($d['comercios'] == 1) {
		$salida .= "<span class='titule'>Datos de los 8 mejores comercios en el intervalo de tiempo</span>";

		//determina los comercios
		$q = "select c.idcomercio, c.nombre
				from tbl_comercio c, tbl_transacciones t
				where t.idcomercio = c.idcomercio
					and t.fecha_mod between $fecha1 and $fecha2
					and t.estado in ('A','V','B','R')
					and t.tipoEntorno = 'P'
				group by t.idcomercio order by sum($elem) desc
				limit 0,8";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arrCome = $temp->loadAssocList();

		//determina las fechas en el perï¿½odo
		$q = "select max(t.fecha_mod) maximo, min(t.fecha_mod) minim, from_unixtime(t.fecha_mod, '$inSql' ) dia
				FROM tbl_transacciones t
				where t.estado in ('A','V','B','R')
				and t.tipoEntorno = 'P'
				and t.fecha_mod between $fecha1 and $fecha2
				GROUP BY from_unixtime(t.fecha_mod, '$inSql')
				ORDER BY t.fecha_mod desc;";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arrFec = $temp->loadAssocList();

		$ancho1 = "2950px";
		$ancho2 = "135px";
		$ancho3 = 62;
		$ancho2 = 4*$ancho3 + 2;
		$ancho1 = (count($arrCome)+1)*$ancho2;
		$ancho3 .= "px";
		$ancho2 .= "px";
		$ancho1 .= "px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys7\" value=\"1\">
					<input type=\"hidden\" name=\"fecha1\" value=\"$fecha1\">
					<input type=\"hidden\" name=\"fecha2\" value=\"$fecha2\">
					<input type=\"hidden\" name=\"elem\" value=\"$elem\">
					<input type=\"hidden\" name=\"inSql\" value=\"$inSql\">
					<input type=\"hidden\" name=\"pag\" value=\"reporte\">
				</form>
				<div id='acoge'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>
						<ul class='respta ttle' style='width:$ancho1'><li style='width:105px;'>&nbsp;</li>";
		for ($i=0; $i<count($arrCome); $i++) {
			$salida .= "<li style='width:$ancho2;'>".$arrCome[$i]['nombre']."</li>";
		}
		$salida .= "</ul><ul class='respta' style='width:$ancho1'><li style='width:105px;'>&nbsp;</li>";
		while ($i != 0) {
			$salida .= "<li style='width:$ancho3'>Valor</li><li style='width:$ancho3;'>Aceptadas</li><li style='width:$ancho3;'>Cant tot</li>
							<li style='width:$ancho3;'>%</li>";
			$i--;
		}
		$salida .= "</ul>";

		for ($i=0;$i<count($arrFec);$i++){
			$salida .= "<ul class='respta' style='width:$ancho1'><li style='width:105px;'>{$arrFec[$i]['dia']}</li>";
			for ($j=0;$j<count($arrCome);$j++) {
				$q = "select sum($elem) valor, count(t.idtransaccion) cant
						FROM tbl_transacciones t
						where t.estado in ('A','V','B','R')
							and t.idcomercio = ".$arrCome[$j]['idcomercio']."
							and t.tipoEntorno = 'P'
							and t.fecha_mod between ".$arrFec[$i]['minim']." and ".$arrFec[$i]['maximo'];
				$temp->query($q);
				if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
// 				if($j==7 && $i == 4) $salida .= $q.$arrBnc[$j]['banco']."<br>";
				$valor = $temp->f('valor');
				$cant = $temp->f('cant');

				$salida .= "<li style='width:$ancho3;'>".formatea_numero($valor,true)."</li>
								<li style='width:$ancho3;'>".formatea_numero($cant,false)."</li>";

				$q = "select count(t.idtransaccion) cant
						FROM tbl_transacciones t
						where  t.estado not in ('P')
							and t.idcomercio = ".$arrCome[$j]['idcomercio']."
							and t.tipoEntorno = 'P'
							and t.fecha_mod between ".$arrFec[$i]['minim']." and ".$arrFec[$i]['maximo'];
				$temp->query($q);
				if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();

				if ($temp->f('cant') > 0) {
					$totale = $temp->f('cant');
					$vale = $cant/$totale*100;
				}
				else {
					$vale = 0;
					$totale = 0;
				}
				$salida .= "<li style='width:$ancho3;'>".formatea_numero($totale,false)."</li>";
				$salida .= "<li style='width:$ancho3;'>".formatea_numero($vale,true)."</li>";

				$Tval[$j] 	+= $valor;
				$TAcep[$j]	+= $cant;
				$TTot[$j] 	+= $temp->f('cant');
			}
			$salida .= "</ul>";
// 			$Tval += $arrIn[$i]['valor'];
// 			$TAcep += $arrTrA[$i]['traceptada'];
// 			$TTot += $arrTrT[$i]['ttrans'];
		}
		$salida .= "<ul class='respta tot' style='width:$ancho1'><li style='width:105px;'>Total: ".count($Tval)."</li>";
		for($i=0; $i<count($Tval);$i++)
		$salida .= "<li style='width:$ancho3;'>".formatea_numero($Tval[$i],true)."</li>
					<li style='width:$ancho3;'>".$TAcep[$i]."</li>
					<li style='width:$ancho3;'>".$TTot[$i]."</li>
					<li style='width:$ancho3;'>".formatea_numero(($TAcep[$i]/$TTot[$i]*100),true)."</li>";
// 			.formatea_numero($Tval)."".formatea_numero($TAcep,false).""
// 			."".formatea_numero($Tval/$TAcep)."</li><li style='width:70px;'>"
// 			.formatea_numero($TTot,false).<li style='width:70px;'>"
// 			.formatea_numero($TAcep/$TTot*100)."</li>";
		$salida .= "</ul></div>";

	}

	/*	Valores, cantidad de operaciones y porcientos por bancos en el intervalo de tiempo	*/
	if ($d['bancos'] == 1) {
		$arrBnc = $arrIn = $arrTrT = array();
		$salida .= "<span class='titule'>Datos de Bancos en el intervalo de tiempo</span>";
		$salidcsv = "";

		//determina los bancos
		$q = "select id, banco from tbl_bancos where id not in (1,3,5,8,14)";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arrBnc = $temp->loadAssocList();

		//determina las fechas en el perï¿½odo
		$q = "select max(t.fecha_mod) maximo, min(t.fecha_mod) minim, from_unixtime(t.fecha_mod, '$inSql' ) dia
				FROM tbl_transacciones t
				where t.estado in ('A','V','B','R')
				and t.tipoEntorno = 'P'
				and t.fecha_mod between $fecha1 and $fecha2
				GROUP BY from_unixtime(t.fecha_mod, '$inSql')
				ORDER BY t.fecha_mod desc;";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arrFec = $temp->loadAssocList();
// 			$salida .= $q;

		$ancho1 = "1250px";
		$ancho2 = "125px";
		$ancho3 = 62;
		$ancho2 = 3*$ancho3 + 2;
		$ancho1 = (count($arrBnc)+1)*$ancho2;
		$ancho3 .= "px";
		$ancho2 .= "px";
		$ancho1 .= "px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat9}'>
				</form>
				<div id='acoge'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$salida .= "<div id='acoge'><ul class='respta ttle' style='width:$ancho1'><li style='width:105px;'> </li>";
		$salidcsv .= ";";
		for ($i=0; $i<count($arrBnc); $i++) {
			$salida .= "<li style='width:$ancho2;'>".$arrBnc[$i]['banco']."</li>";
			$salidcsv .= ";".$arrBnc[$i]['banco'].";;";
		}
		$salida .= "</ul><ul class='respta' style='width:$ancho1'><li style='width:105px;'>&nbsp;</li>";
		$salidcsv .= "{n}";
		while ($i != 0) {
			$salida .= "<li style='width:$ancho3'>Valor</li><li style='width:$ancho3;'>Aceptadas</li><li style='width:$ancho3;'>%</li>";
			$salidcsv .= "Valor;Aceptadas;%;";
			$i--;
		}

		$salida .= "</ul>";
		$salidcsv .= "{n}";

		for ($i=0;$i<count($arrFec);$i++){
			$salida .= "<ul class='respta' style='width:$ancho1'><li style='width:105px;'>{$arrFec[$i]['dia']}</li>";
			$salidcsv .= $arrFec[$i]['dia'];
			for ($j=0;$j<count($arrBnc);$j++) {
				$q = "select sum($elem) valor, count(t.idtransaccion) cant
						FROM tbl_transacciones t
						where t.estado in ('A','V','B','R')
							and t.pasarela in (select idpasarela from tbl_colPasarBancos where idbanco = ".$arrBnc[$j]['id'].")
							and t.tipoEntorno = 'P'
							and t.fecha_mod between ".$arrFec[$i]['minim']." and ".$arrFec[$i]['maximo'];
				$temp->query($q);
				if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
// 				if($j==7 && $i == 4) $salida .= $q.$arrBnc[$j]['banco']."<br>";
				$valor = $temp->f('valor');
				$cant = $temp->f('cant');

				$salida .= "<li style='width:$ancho3;'>".formatea_numero($valor)."</li>
							<li style='width:$ancho3;'>".formatea_numero($cant,false)."</li>";
				$salidcsv .= ";".formatea_numero($valor).";".formatea_numero($cant);

				$q = "select count(t.idtransaccion) cant
						FROM tbl_transacciones t
						where t.estado not in ('P')
							and t.pasarela in (select idpasarela from tbl_colPasarBancos where idbanco = ".$arrBnc[$j]['id'].")
							and t.tipoEntorno = 'P'
							and t.fecha_mod between ".$arrFec[$i]['minim']." and ".$arrFec[$i]['maximo'];
				$temp->query($q);
				if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();

				if ($temp->f('cant') > 0) $vale = formatea_numero ($cant/$temp->f('cant')*100,true);
				else $vale = 0;
				$salida .= "<li style='width:$ancho3;'>".$vale."</li>";
				$salidcsv .= ";".$vale;

				$Tval[$j] 	+= $valor;
				$TAcep[$j]	+= $cant;
				$TTot[$j] 	+= $temp->f('cant');
			}
			$salida .= "</ul>";
			$salidcsv .= "{n}";
// 			$Tval += $arrIn[$i]['valor'];
// 			$TAcep += $arrTrA[$i]['traceptada'];
// 			$TTot += $arrTrT[$i]['ttrans'];
		}
		$salida .= "<ul class='respta tot' style='width:$ancho1'><li style='width:105px;'>Total: ".formatea_numero(count($Tval))."</li>";
		$salidcsv .= "Total: ".formatea_numero(count($Tval));
		for($i=0; $i<count($Tval);$i++) {
		$salida .= "<li style='width:$ancho3;'>".formatea_numero($Tval[$i])."</li>
					<li style='width:$ancho3;'>".$TAcep[$i]."</li>
					<li style='width:$ancho3;'>".formatea_numero(($TAcep[$i]/$TTot[$i]*100),true)."</li>";
		$salidcsv .= ";".formatea_numero($Tval[$i]).";".formatea_numero($TAcep[$i]).";".formatea_numero($TAcep[$i]/$TTot[$i]*100);
		}
// 			.formatea_numero($Tval)."".formatea_numero($TAcep,false).""
// 			."".formatea_numero($Tval/$TAcep)."</li><li style='width:70px;'>"
// 			.formatea_numero($TTot,false).<li style='width:70px;'>"
// 			.formatea_numero($TAcep/$TTot*100)."</li>";
		$salida .= "</ul></div>";
		$salidcsv .= "{n}";
		$salida = str_replace("{dat9}", $salidcsv, $salida);
	}

	/*	Valores, cantidad de operaciones y porcientos en el intervalo de tiempo	*/
	if ($d['datos'] == 1) {
	$arrIn = $arrTrA = $arrTrT = array();
	$salidcsv = "";
	$salida .= "<span class='titule'>Datos en el intervalo de tiempo</span>";
		$q = "select from_unixtime(t.fecha_mod, '$inSql' ) dia,
			sum($elem) valor
			FROM tbl_transacciones t
			where t.estado in ('A','V','B','R')
				and t.tipoEntorno = 'P'
				and t.fecha_mod between $fecha1 and $fecha2
			GROUP BY from_unixtime(t.fecha_mod, '$inSql')
			ORDER BY t.fecha_mod desc;";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arrIn = $temp->loadAssocList();
		//print_r($arrVal);
		$q = "select count(t.idtransaccion) traceptada
			FROM tbl_transacciones t
			where t.estado in ('A')
				and t.tipoEntorno = 'P'
				and t.fecha_mod between $fecha1 and $fecha2
			GROUP BY from_unixtime(t.fecha_mod, '$inSql')
			ORDER BY t.fecha_mod desc;";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arrTrA = $temp->loadAssocList();
		//print_r($arrTrA);
		$q = "select count(t.idtransaccion) ttrans
			FROM tbl_transacciones t
			where t.tipoEntorno = 'P'
				and t.fecha_mod between $fecha1 and $fecha2
			GROUP BY from_unixtime(t.fecha_mod, '$inSql')
			ORDER BY t.fecha_mod desc;";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		//$salida .= $q;
		$arrTrT = $temp->loadAssocList();
		//print_r($arrTrT);
		$ancho = "470px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat10}'>
				</form>
				<div id='acoge'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";

		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'><li style='width:105px;'>&nbsp;</li><li style='width:70px;'>"
			."Valor &euro;</li><li style='width:70px;'>Aceptada</li>"
			."<li style='width:70px;'>Val/Trans</li><li style='width:70px;'>"
				."Tot. Trans.</li><li>"
				."% Aceptadas</li></ul>";
		$salidcsv .= ";Valor (Euro);Aceptada;Val/Trans;Tot. Trans.;% Aceptadas{n}";
		for ($i=0;$i<count($arrIn);$i++){
			$salida .= "<ul class='respta' style='width:$ancho'><li style='width:105px;'>{$arrIn[$i]['dia']}</li><li style='width:70px;'>"
				.formatea_numero($arrIn[$i]['valor'])."</li><li style='width: 70px;'>".formatea_numero($arrTrA[$i]['traceptada'],false)."</li>"
				."<li style='width:70px;'>".formatea_numero($arrIn[$i]['valor']/$arrTrA[$i]['traceptada'])."</li><li style='width:70px;'>"
				.formatea_numero($arrTrT[$i]['ttrans'],false)."</li><li style='width:70px;'>"
				.formatea_numero($arrTrA[$i]['traceptada']/$arrTrT[$i]['ttrans']*100)."</li></ul>";
			$Tval += $arrIn[$i]['valor'];
			$TAcep += $arrTrA[$i]['traceptada'];
			$TTot += $arrTrT[$i]['ttrans'];
		$salidcsv .= "{$arrIn[$i]['dia']};".formatea_numero($arrIn[$i]['valor']).";".formatea_numero($arrTrA[$i]['traceptada'],false).";"
				.formatea_numero($arrIn[$i]['valor']/$arrTrA[$i]['traceptada']).";".formatea_numero($arrTrT[$i]['ttrans'],false).";"
				.formatea_numero($arrTrA[$i]['traceptada']/$arrTrT[$i]['ttrans']*100)."{n}";
		}
		$salida .= "<ul class='respta tot' style='width:$ancho'><li style='width:105px;'>Total:</li><li style='width:70px;'>"
			.formatea_numero($Tval)."</li><li style='width: 70px;'>".formatea_numero($TAcep,false)."</li>"
			."<li style='width:70px;'>".formatea_numero($Tval/$TAcep)."</li><li style='width:70px;'>"
			.formatea_numero($TTot,false)."</li><li style='width:70px;'>"
			.formatea_numero($TAcep/$TTot*100)."</li></ul>";
		$salida .= "</div>";
		$salidcsv .= "Total:;".formatea_numero($Tval).";".formatea_numero($TAcep,false).";".formatea_numero($Tval/$TAcep).";"
			.formatea_numero($TTot,false).";".formatea_numero($TAcep/$TTot*100)."{n}";
		$salida = str_replace("{dat10}", $salidcsv, $salida);
	}

	/*	Valores, cantidad de operaciones y porcientos en el intervalo de tiempo	*/
	if ($d['acept'] == 1) {
		$Tval = $TAcep = $TTot = 0;
	$arrIn = $arrTrA = $arrTrT = array();
	$salidcsv = "";
	$salida .= "<span class='titule'>Valor de Ingresos ocurridos en el intervalo de tiempo seleccionado</span>";
		$q = "select from_unixtime(t.fecha, '$inSql' ) dia, ".
			" sum(valor_inicial/tasa/100) valor ".
			" FROM tbl_transacciones t ".
			" where t.estado in ('A','V','B','R') ".
				" and t.tipoEntorno = 'P' ".
				" and t.fecha between $fecha1 and $fecha2 ".
			" GROUP BY from_unixtime(t.fecha, '$inSql') ".
			" ORDER BY t.fecha desc";
		$temp->query($q);
//		error_log($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arrIn = $temp->loadAssocList();
		//print_r($arrVal);
		$q = "select count(t.idtransaccion) traceptada
			FROM tbl_transacciones t
			where t.estado in ('A','V','B','R')
				and t.tipoEntorno = 'P'
				and t.fecha between $fecha1 and $fecha2
			GROUP BY from_unixtime(t.fecha, '$inSql')
			ORDER BY t.fecha desc;";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arrTrA = $temp->loadAssocList();
		//print_r($arrTrA);
		$q = "select count(t.idtransaccion) ttrans
			FROM tbl_transacciones t
			where t.tipoEntorno = 'P'
				and t.fecha between $fecha1 and $fecha2
			GROUP BY from_unixtime(t.fecha, '$inSql')
			ORDER BY t.fecha desc;";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		//$salida .= $q;
		$arrTrT = $temp->loadAssocList();
		//print_r($arrTrT);
		$ancho = "470px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat15}'>
				</form>
				<div id='acoge'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";

		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'><li style='width:105px;'>&nbsp;</li><li style='width:70px;'>"
			."Valor &euro;</li><li style='width:70px;'>Aceptada</li>"
			."<li style='width:70px;'>Val/Trans</li><li style='width:70px;'>"
				."Tot. Trans.</li><li>"
				."% Aceptadas</li></ul>";
		$salidcsv .= ";Valor (Euro);Aceptada;Val/Trans;Tot. Trans.;% Aceptadas{n}";
		for ($i=0;$i<count($arrIn);$i++){
			$salida .= "<ul class='respta' style='width:$ancho'><li style='width:105px;'>{$arrIn[$i]['dia']}</li><li style='width:70px;'>"
				.formatea_numero($arrIn[$i]['valor'])."</li><li style='width: 70px;'>".formatea_numero($arrTrA[$i]['traceptada'],false)."</li>"
				."<li style='width:70px;'>".formatea_numero($arrIn[$i]['valor']/$arrTrA[$i]['traceptada'])."</li><li style='width:70px;'>"
				.formatea_numero($arrTrT[$i]['ttrans'],false)."</li><li style='width:70px;'>"
				.formatea_numero($arrTrA[$i]['traceptada']/$arrTrT[$i]['ttrans']*100)."</li></ul>";
			$Tval += $arrIn[$i]['valor'];
			$TAcep += $arrTrA[$i]['traceptada'];
			$TTot += $arrTrT[$i]['ttrans'];
			$salidcsv .= "{$arrIn[$i]['dia']};".formatea_numero($arrIn[$i]['valor']).";".formatea_numero($arrTrA[$i]['traceptada'],false).";"
				.formatea_numero($arrIn[$i]['valor']/$arrTrA[$i]['traceptada']).";".formatea_numero($arrTrT[$i]['ttrans'],false).";"
				.formatea_numero($arrTrA[$i]['traceptada']/$arrTrT[$i]['ttrans']*100)."{n}";
		}
		$salida .= "<ul class='respta tot' style='width:$ancho'><li style='width:105px;'>Total:</li><li style='width:70px;'>"
			.formatea_numero($Tval)."</li><li style='width: 70px;'>".formatea_numero($TAcep,false)."</li>"
			."<li style='width:70px;'>".formatea_numero($Tval/$TAcep)."</li><li style='width:70px;'>"
			.formatea_numero($TTot,false)."</li><li style='width:70px;'>"
			.formatea_numero($TAcep/$TTot*100)."</li></ul>";
		$salida .= "</div>";
		$salidcsv .= "Total:;".formatea_numero($Tval).";".formatea_numero($TAcep,false).";".formatea_numero($Tval/$TAcep).";"
			.formatea_numero($TTot,false).";".formatea_numero($TAcep/$TTot*100)."{n}";
		$salida = str_replace("{dat15}", $salidcsv, $salida);
	}

	/*	Valores, cantidad de operaciones y porcientos en el intervalo de tiempo por empresas	*/
	if ($d['ecept'] == 1) {
		$Tval = $TAcep = $TTot = 0;
		$arrIn = $arrTrA = $arrTrT = array();
		$salidcsv = $salida2 = "";
		$salida .= "<span class='titule'>Valor de Ingresos por empresas ocurridos en el intervalo de tiempo seleccionado</span>";
		
		$q = "select from_unixtime(t.fecha_mod, '$inSql' ) dia, sum(valor_inicial/tasa/100) valor FROM tbl_transacciones t where t.estado in ('A','V','B','R') and t.tipoEntorno = 'P' and t.fecha_mod between $fecha1 and $fecha2 GROUP BY from_unixtime(t.fecha_mod, '$inSql') ORDER BY t.fecha_mod desc";
//		error_log($q);
		$temp->query($q);
		$arr1 = $temp->loadAssocList();
//		print_r($arr1);
		$salida .= "
				<form name=\"queryspbanc\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{queryspbanc}'>
				</form>
				<div style='margin-left: -440px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.queryspbanc.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$q = "select e.id, e.nombre from tbl_transacciones t, tbl_pasarela p, tbl_empresas e where t.pasarela = p.idPasarela and p.idempresa = e.id and t.estado in ('A','V','B','R') and t.tipoEntorno = 'P' and t.fecha between $fecha1 and $fecha2 group by e.nombre order by e.nombre";
		$temp->query($q);
		$arr2 = $temp->loadAssocList();
		$aempr = 400;
		$ancho = 100 + ($aempr * (count($arr2)+2));
		$salida .= "<style>.total{background-color:#ffd75c;}.linea:hover{background-color:#FFEBD9;} .vr{width:".$ancho."px;}.vr, .vr div{height: 18px;float:left;padding:3px 0 !important;}.bord{font-weight:bold;}.cero{width:150px;}.bncs{width:".$aempr."px;}.medbncs{width:".($aempr/3)."px;}</style>";
		$salida0 = "<div class='vr'><div class='bord cero'>&nbsp;</div>";
		$salida01 = "<div class='vr'><div class='bord cero'>Días</div>";
		$salidcsv = $salidcsv1 = $salidcsv2 = $salida1 = "";
		for ($i=0;$i<count($arr2);$i++){
			$salida1 .= "<div class='bord bncs'>{$arr2[$i]['nombre']}</div>";
			$salida2 .= "<div class='bord medbncs'>Valor &euro;</div><div class='bord medbncs'>Aceptada</div><div class='bord medbncs'>Val/Trans</div>";
			$arrban[] = $arr2[$i]['id'];
			$salidcsv1 .= "{$arr2[$i]['nombre']};;;";
			$salidcsv2 .= "Valor;Aceptada;Val/Trans;";
		}
		$salida .= $salida0.$salida1."</div>".$salida01.$salida2."</div>";
		$salidcsv .= ";$salidcsv1{n};$salidcsv2{n}";
		$salida .= "</div>";
		$arrVol = $arrCant = array();
//		var_dump($arr1);
		for ($j= 0; $j<count($arr1);$j++) {
			$salida .= "<div class='vr linea'><div class='bord cero'>".$arr1[$j]['dia']."</div>";
			$salidcsv .= $arr1[$j]['dia']."";
			$sale = $salcsv = "";
			$cantTP = $volTP = $k = 0;
			foreach ($arrban as $banid) {
				$q = "select count(idtransaccion) cant, sum(valor_inicial/tasa/100) valor from tbl_transacciones t, tbl_pasarela p, tbl_empresas e where t.estado in ('A','V','B','R') and t.pasarela = p.idPasarela and p.idempresa = e.id and t.tipoEntorno = 'P' and from_unixtime(t.fecha_mod, '$inSql') = '".$arr1[$j]['dia']."' and e.id = (".$banid.") group by e.id order by e.nombre";
				//error_log($q);
				$temp->query($q);
				$arr3 = $temp->loadAssocList();
				if ($arr3[0]['cant'] == null || $arr3[0]['cant'] == '') $cantop = 0;
				else $cantop = $arr3[0]['cant'];
				$arrCant[$k] += $cantop;
				$arrVol[$k] = $arrVol[$k] + 1*(number_format($arr3[0]['valor'],2,'.',''));
				$cantTP += $cantop;
				$volTP += 1*(number_format($arr3[0]['valor'],2,'.',''));
				if ($cantop > 0) $res = formatea_numero($arr3[0]['valor']/$cantop);
				else $res = 0;
				$sale .= "<div class='medbncs'>".formatea_numero($arr3[0]['valor'])."</div><div class='medbncs'>".$cantop."</div><div class='medbncs'>".$res."</div>";
				$salcsv .= ";".formatea_numero($arr3[0]['valor']).";".$cantop.";".$res;
				$k++;
			}
			$salida .= "$sale</div>";
			$salidcsv .= $salcsv."{n}";
			
		}
		$salida .= "<div class='vr total'><div class='bord cero'>Totales</div>";
		$salidcsv .= "Totales;";
		for($i = 0; $i < count($arrCant); $i++){
			$salida .= "<div class='medbncs bord'>".formatea_numero($arrVol[$i])."</div><div class='medbncs bord'>$arrCant[$i]</div><div class='medbncs bord'>".formatea_numero($arrVol[$i]/$arrCant[$i])."</div>";
			if ($arrCant[$i] > 0) $res = formatea_numero($arrVol[$i]/$arrCant[$i]);
			else $res = 0;
			$salidcsv .= formatea_numero($arrVol[$i]).";".$arrCant[$i].";".$res.";";
		}
		$salidcsv .= "{n}";
		$salida .= "</div>";
		$salida .= "</div>";
		$salida .= "</div>";
		$salida = str_replace("{queryspbanc}", $salidcsv, $salida);
	}

			/*	Estado de las operaciones	*/
	if ($d['estado'] == 1) {
			$arrEsT = $arrTr = $arrTrT = array();
		$salidcsv = "";
		$salida .= "<span class='titule'>Estado de las operaciones en el intervalo de tiempo</span>";
		$q = "select case t.estado
				when 'P' then 'En Proceso'
				when 'A' then 'Aceptada'
				when 'D' then 'Denegada'
				when 'N' then 'No Procesada'
				when 'B' then 'Anulada'
				when 'V' then 'Devuelta'
				when 'R' then 'Reclamada'
				else '' end estad,
				count(t.idtransaccion) cantidad
			from tbl_transacciones t
			where t.fecha_mod between $fecha1 and $fecha2
				and t.tipoEntorno = 'P'
			group by t.estado order by t.estado";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		//$salida .= $q;
		$arrEsT = $temp->loadAssocList();
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat1}'>
				</form>
				<div style='margin-left: 94px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:260px'><li style='width:105px;'>Estado</li><li style='width:70px;'>"
					."Cantidad</li><li>% Aceptadas</li></ul>";
		$salidcsv .= "Estado;Cantidad;% Aceptadas{n}";
		
		for ($i=0;$i<count($arrEsT);$i++) {
			$nuT += $arrEsT[$i]['cantidad'];
		}
		$arrEsT[$i]['cantidad'] = $nuT;
		$arrEsT[$i]['estad'] = "Total";
		for ($i=0;$i<count($arrEsT);$i++){
			$salida .= "<ul class='respta' style='width:260px;'><li style='width:105px;'>{$arrEsT[$i]['estad']}</li><li style='width:70px;'>"
						.formatea_numero($arrEsT[$i]['cantidad'],false)."</li><li style='width:70px;'>"
						.formatea_numero($arrEsT[$i]['cantidad']/$nuT*100)."</li></ul>";
			$salidcsv .= $arrEsT[$i]['estad'].";".formatea_numero($arrEsT[$i]['cantidad'],false).";". formatea_numero($arrEsT[$i]['cantidad']/$nuT*100)."{n}";
		}
		$salida .= "</div>";
		$salida = str_replace("{ecept}", $salidcsv, $salida);
	}

	/*	An&aacute;lisis por moneda	*/
	if ($d['moneda'] == 1) {
// echo json_encode(array("salida"=>"pase"));
		$arr1 = array();
		$salidcsv = "";
		$salida .= "<span class='titule'>An&aacute;lisis por monedas</span>";
		$q = "select t.moneda id, m.moneda,
				sum($elem) valor,
				count(t.idtransaccion) cantt, t.moneda idm
				from tbl_transacciones t, tbl_moneda m
					where t.moneda = m.idmoneda
						and t.tipoEntorno = 'P'
						and t.estado in ('A','V','B','R','D')
						and t.fecha_mod between $fecha1 and $fecha2
					group by t.moneda order by t.moneda";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
// 		$salida .= $q;
		$arr1 = $temp->loadAssocList();

		$ancho = "400px";
			$salida .= "
					<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
						<input type=\"hidden\" name=\"querys10\" value=\"1\">
						<input type=\"hidden\" name=\"inSql\" value='{dat2}'>
					</form>
					<div style='margin-left: 17px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
							onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
					."<li style='width:105px;'>Moneda</li>"
					."<li style='width:70px;'>Valor &euro;</li>"
					."<li style='width:70px;'>Aceptadas</li>"
					."<li style='width:70px;'>Total</li>"
					."<li>% Aceptadas</li></ul>";
		$salidcsv .= "Moneda;Valor (Euro);Aceptadas;Total;% Aceptadas{n}";
		for ($i=0;$i<count($arr1);$i++){
			$q = "select count(idtransaccion) canta from tbl_transacciones where
					fecha_mod between $fecha1 and $fecha2
					and tipoEntorno = 'P'
					and estado in ('A','V','B','R')
					and moneda = {$arr1[$i]['idm']}";
			$temp->query($q);

			$salida .= "<ul class='respta' style='width:$ancho;'>"
							."<li style='width:105px;'>{$arr1[$i]['moneda']}</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor'])."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('canta'),false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['cantt'],false)."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('canta')/$arr1[$i]['cantt']*100)."</li></ul>";
			$salidcsv .= "{$arr1[$i]['moneda']};".formatea_numero($arr1[$i]['valor']).";".formatea_numero($temp->f('canta')).";".formatea_numero($arr1[$i]['cantt']).";". formatea_numero($temp->f('canta')/$arr1[$i]['cantt']*100)."{n}";
		}
		$salida .= "</div>";
		$salida = str_replace("{dat2}", $salidcsv, $salida);
	}

	/*	An&aacute;lisis por TPV	*/
	if ($d['tpv'] == 1) {
		$salidcsv = "";
		$arr1 = $arr2 = $arr3 = array();
// 		$j = (int)($fecha2-$fecha1)/86400;
		$salida .= "<span class='titule'>An&aacute;lisis por TPV</span>";
		$q = "select p.nombre,
					sum($elem) valor, count(t.idtransaccion) cantt, t.pasarela idpas
				from tbl_transacciones t, tbl_pasarela p
				where t.pasarela = p.idPasarela
					and t.tipoEntorno = 'P'
					and t.estado in ('A','V','B','R')
					and t.fecha_mod between $fecha1 and $fecha2
				group by t.pasarela order by p.nombre";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
// 		$salida .= $q;
		$arr1 = $temp->loadAssocList();


		$ancho = "550px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat3}'>
				</form>
				<div style='margin-left: -45px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
				."<li style='width:105px;'>Pasarela</li>"
				."<li style='width:70px;'>Valor &euro;</li>"
				."<li style='width:70px;'>Aceptadas</li>"
				."<li style='width:70px;'>Total</li>"
				."<li style='width:70px;'>Valor/Trans</li>"
				."<li style='width:70px;'>% Aceptadas</li>"
				."<li style='width:70px;'>Estimado</li></ul>";
		$salidcsv .= "Pasarela;Valor (Euro);Aceptadas;Total;Valor/Trans;% Aceptadas;Estimado{n}";
		for ($i=0;$i<count($arr1);$i++){
			$q = "select count(t.idtransaccion) cant
				from tbl_transacciones t, tbl_pasarela p
				where t.pasarela = p.idPasarela
					and t.tipoEntorno = 'P'
					and t.estado in ('A','V','B','R','D')
					and t.fecha_mod between $fecha1 and $fecha2
					and t.pasarela = {$arr1[$i]['idpas']}";
// 			echo $q;
			$temp->query($q);
			if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
			$salida .= "<ul class='respta' style='width:$ancho;'>"
						."<li style='width:105px;'>{$arr1[$i]['nombre']}</li>"
						."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor'])."</li>"
						."<li style='width:70px;'>".formatea_numero($arr1[$i]['cantt'],false)."</li>"
						."<li style='width:70px;'>".formatea_numero($temp->f('cant'),false)."</li>"
						."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor']/$arr1[$i]['cantt'])."</li>"
						."<li style='width:70px;'>".formatea_numero($arr1[$i]['cantt']*100/$temp->f('cant'))."</li>"
						."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor'] / $j * $int)."</li></ul>";
			$salidcsv .= "{$arr1[$i]['nombre']};".formatea_numero($arr1[$i]['valor']).";".formatea_numero($arr1[$i]['cantt']).";".formatea_numero($temp->f('cant')).";".
						formatea_numero($arr1[$i]['valor']/$arr1[$i]['cantt']).";".formatea_numero($arr1[$i]['cantt']*100/$temp->f('cant')).";".
						formatea_numero($arr1[$i]['valor'] / $j * $int)."{n}";
			$tot1 += formatea_numero($arr1[$i]['valor']);
			$tot2 += formatea_numero($temp->f('cant'),false);
			$tot3 += formatea_numero($arr1[$i]['cantt'],false);
		}
		$salida .= "<ul class='respta tot' style='width:$ancho'>
						<li style='width:105px;'>Total:</li>"
						."<li style='width:70px;'>".$tot1."</li>"
						."<li style='width:70px;'>".$tot3."</li>"
						."<li style='width:70px;'>".$tot2."</li>"
						."<li style='width:70px;'>".formatea_numero($tot1/$tot2)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot3*100/$tot2)."</li></ul>";
		$salidcsv .= "Total;".$tot1.";".$tot3.";".$tot2.";". formatea_numero($tot1/$tot2).";". formatea_numero($tot3*100/$tot2)."{n}";
		$salida .= "</div>";
		$salida = str_replace("{dat3}", $salidcsv, $salida);
	}

	/**
	 * Denegadas por TPV-Moneda
	 */
	if ($d['dpvm'] == 1) {
		$arr1 = array();
		$q = "select p.nombre pasarela, m.moneda, t.id_error, count(t.idtransaccion) cant, t.pasarela idpas, t.moneda idmoneda
			from tbl_transacciones t, tbl_pasarela p, tbl_moneda m
			where t.moneda = m.idmoneda
				and t.pasarela = p.idPasarela
				and t.tipoEntorno = 'P'
				and t.estado in ('D')
				and fecha_mod between $fecha1 and $fecha2
				and p.idcenauto not in (11,8)
			group by pasarela, t.moneda, t.id_error order by p.nombre, t.moneda, t.id_error";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();
		
		for ($i=0; $i<count($arr1); $i++){
			$q = "select count(r.idtransaccion) total from tbl_transacciones r where r.pasarela = ".$arr1[$i]['idpas']." and r.moneda = ".$arr1[$i]['idmoneda']."
					and r.fecha_mod between $fecha1 and $fecha2 and r.estado in ('D') and r.tipoEntorno = 'P'";

			$temp->query($q);

			if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
			$arr1[$i]['total'] = $temp->f('total');
			
			$q = "select count(i.idtransaccion) total from tbl_transacciones i where i.pasarela = ".$arr1[$i]['idpas']." and i.moneda = ".$arr1[$i]['idmoneda']."
					and i.fecha_mod between $fecha1 and $fecha2 and i.estado in ('A','V','B','R','D') and i.tipoEntorno = 'P'";
			$temp->query($q);

			if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
			$arr1[$i]['totG'] = $temp->f('total');
		}

		$ancho = "890px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat14}'>
				</form>
				<div style='margin-left: -30px;width: $ancho'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
						."<li style='width:105px;'>Pasarela</li>"
						."<li style='width:90px;'>Moneda</li>"
						."<li style='width:370px;'>Error</li>"
						."<li style='width:70px;'>Cantidad</li>"
						."<li style='width:70px;'>Total Den</li>"
						."<li style='width:70px;'>% En tot oper den</li>"
						."<li style='width:70px;'>Total Gen</li></ul>";
		$salidcsv .= "Pasarela;Moneda;Error;Cantidad;Total Den;% En tot oper;Total Gen.{n}";
		
		$tot1 = $tot2 = $tot3 = 0;
		for ($i=0;$i<count($arr1);$i++){

			$salida .= "<ul class='respta' style='width:$ancho;'>"
							."<li style='width:105px;'>{$arr1[$i]['pasarela']}</li>"
							."<li style='width:90px;'>{$arr1[$i]['moneda']}</li>"
							."<li style='width:370px;'>".$arr1[$i]['id_error']."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['cant'],false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['total'],false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['cant']*100/$arr1[$i]['total'])."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['totG'],false)."</li></ul>";
			$salidcsv .= $arr1[$i]['pasarela'].";".$arr1[$i]['moneda'].";".str_replace(";", "", $arr1[$i]['id_error']).";".formatea_numero($arr1[$i]['cant'],false).";"
							. formatea_numero($arr1[$i]['total'],false).";". formatea_numero($arr1[$i]['cant']*100/$arr1[$i]['total']).";".formatea_numero($arr1[$i]['totG'],false)."{n}";

			$tot1 += formatea_numero($arr1[$i]['cant'],false);
			$tot2 += formatea_numero($arr1[$i]['total'],false);
			$tot3 += formatea_numero($arr1[$i]['totG'],false);
			
		}
		$salida .= "<ul class='respta tot' style='width:$ancho'>
						<li style='width:105px;'>Total:</li>"
						."<li style='width:90px;'>&nbsp;</li>"
						."<li style='width:370px;'>&nbsp;</li>"
						."<li style='width:70px;'>".$tot1."</li>"
						."<li style='width:70px;'>".$tot2."</li>"
						."<li style='width:70px;'>".formatea_numero($tot1/$tot2*100)."</li>"
						."<li style='width:70px;'>".$tot3."</li></ul>";
		$salidcsv .= "Total;;;$tot1;$tot2;". formatea_numero($tot1/$tot2*100).",$tot3{n}";
		$salida .= "</div>";
		$salida = str_replace("{dat14}", $salidcsv, $salida);
	}

	/*	An&aacute;lisis por TPV - Moneda */
	if ($d['tpvm'] == 1) {
		$arr1 = $arr2 = $arr3 = array();
		$salidcsv = "";
		$salida .= "<span class='titule'>An&aacute;lisis por TPV - Moneda</span>";
		$q = "select p.nombre pasarela,
				m.moneda,
				sum($elem) valor, count(idtransaccion) cantt, t.pasarela idp, t.moneda idm
			from tbl_transacciones t, tbl_pasarela p, tbl_moneda m
			where t.moneda = m.idmoneda
				and t.pasarela = p.idPasarela
				and t.tipoEntorno = 'P'
				and t.estado in ('A','V','B','R','D')
				and fecha_mod between $fecha1 and $fecha2
			group by pasarela, t.moneda order by p.nombre, t.moneda";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();

		$ancho = "590px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat4}'>
				</form>
				<div style='margin-left: -70px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
						."<li style='width:105px;'>Pasarela</li>"
						."<li style='width:90px;'>Moneda</li>"
						."<li style='width:70px;'>Valor &euro;</li>"
						."<li style='width:70px;'>Aceptadas</li>"
						."<li style='width:70px;'>Total</li>"
						."<li style='width:70px;'>Valor/Trans</li>"
						."<li>% Aceptadas</li></ul>";
		$salidcsv .= "Pasarela;Moneda;Valor (Euro);Aceptadas;Total;Valor/Trans;% Aceptadas{n}";
		for ($i=0;$i<count($arr1);$i++){
			$q = "select count(idtransaccion) canta from tbl_transacciones where
					fecha_mod between $fecha1 and $fecha2
					and tipoEntorno = 'P'
					and estado in ('A','V','B','R')
					and pasarela = {$arr1[$i]['idp']}
					and moneda = {$arr1[$i]['idm']}";
			$temp->query($q);

			$salida .= "<ul class='respta' style='width:$ancho;'>"
							."<li style='width:105px;'>{$arr1[$i]['pasarela']}</li>"
							."<li style='width:90px;'>{$arr1[$i]['moneda']}</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor'])."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('canta'),false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['cantt'],false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor']/$temp->f('canta'))."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('canta')/$arr1[$i]['cantt']*100)."</li></ul>";
			$salidcsv .= $arr1[$i]['pasarela'].";".$arr1[$i]['moneda'].";".formatea_numero($arr1[$i]['valor']).";".formatea_numero($temp->f('canta'),false).";"
							.formatea_numero($arr1[$i]['cantt'],false).";".formatea_numero($arr1[$i]['valor']/$temp->f('canta')).";".formatea_numero($temp->f('canta')/$arr1[$i]['cantt']*100)."{n}";
			$tot1 += formatea_numero($arr1[$i]['valor']);
			$tot2 += formatea_numero($temp->f('canta',false));
			$tot3 += formatea_numero($arr1[$i]['cantt'],false);
		}
		$salida .= "<ul class='respta tot' style='width:$ancho'>
						<li style='width:105px;'>Total:</li>"
						."<li style='width:90px;'>&nbsp;</li>"
						."<li style='width:70px;'>".($tot1)."</li>"
						."<li style='width:70px;'>".($tot2)."</li>"
						."<li style='width:70px;'>".($tot3)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot1/$tot2)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot2/$tot3*100)."</li></ul>";
		$salidcsv .= "Total;;$tot1;$tot2;$tot3;".formatea_numero($tot1/$tot2).";".formatea_numero($tot2/$tot3*100)."{n}";
		$salida .= "</div>";
		$salida = str_replace("{dat4}", $salidcsv, $salida);
	}

	/*	An&aacute;lisis por Banco	*/
		if ($d['banc'] == 1) {
		$arr1 = $arr2 = $arr3 = array();
		$salidcsv = "";
		$salida .= "<span class='titule'>An&aacute;lisis por Banco</span>";
		$q = "select b.banco nombre,
			sum($elem) valor, count(t.idtransaccion) cantt, b.id idbnc
			from tbl_transacciones t, tbl_colPasarBancos c, tbl_bancos b
			where t.pasarela = c.idpasarela
			and c.idbanco = b.id
			and t.tipoEntorno = 'P'
			and t.estado in ('A','V','B','R','D')
			and t.fecha_mod between $fecha1 and $fecha2
			group by b.id order by b.banco";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();
		// print_r($arr1);
		$ancho = "550px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat5}'>
				</form>
				<div style='margin-left: -45px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
						."<li style='width:105px;'>Banco</li>"
						."<li style='width:70px;'>Valor &euro;</li>"
						."<li style='width:70px;'>Aceptadas</li>"
						."<li style='width:70px;'>Total</li>"
						."<li style='width:70px;'>Valor/Trans</li>"
						."<li style='width:70px;'>% Aceptadas</li>"
						."<li style='width:70px;'>Estimado</li></ul>";
		$salidcsv .= "Banco,Valor (Euro),Aceptadas,Total,Valor/Trans,% Aceptadas,Estimado{n}";
		for ($i=0;$i<count($arr1);$i++){
			$q = "select count(idtransaccion) cant from tbl_transacciones t, tbl_colPasarBancos c
					where t.estado in ('A','V','B','R')
						and t.pasarela = c.idpasarela
						and t.tipoEntorno = 'P'
						and t.fecha_mod between $fecha1 and $fecha2
						and c.idbanco = {$arr1[$i]['idbnc']}";
			$temp->query($q);
			$salida .= "<ul class='respta' style='width:$ancho;'>"
							."<li style='width:105px;'>{$arr1[$i]['nombre']}</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor'])."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('cant'),false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['cantt'],false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor']/$temp->f('cant'))."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('cant')/$arr1[$i]['cantt']*100)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor'] / $j * $int)."</li></ul>";
			$salidcsv .= $arr1[$i]['nombre'].",".($arr1[$i]['valor']).",".($temp->f('cant')).",".($arr1[$i]['cantt']).","
							.($arr1[$i]['valor']/$temp->f('cant')).",".($temp->f('cant')/$arr1[$i]['cantt']*100).","
							.($arr1[$i]['valor'] / $j * $int)."{n}";
			$tot1 += $arr1[$i]['valor'];
			$tot2 += $temp->f('cant');
			$tot3 += $arr1[$i]['cantt'];
		}
		$salida .= "<ul class='respta tot' style='width:$ancho'>
						<li style='width:105px;'>Total:</li>"
						."<li style='width:70px;'>".formatea_numero($tot1)."</li>"
						."<li style='width: 70px;'>".formatea_numero($tot2,false)."</li>"
						."<li style='width: 70px;'>".formatea_numero($tot3,false)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot1/$tot2)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot2/$tot3*100)."</li></ul>";
		$salidcsv .= "Total:,".($tot1).",".($tot2).",".($tot3).",".($tot1/$tot2).",".($tot2/$tot3*100)."{n}";
		$salida .= "</div>";
		$salida = str_replace("{dat5}", $salidcsv, $salida);
	}

	/*	Análisis de Países por Banco	*/
		if ($d['pbanc'] == 1) {
		$arr1 = $arr2 = $arr3 = $arrCant = $arrVol = array();
		$salidcsv = "";
		$salida .= "<span class='titule'>An&aacute;lisis de Países por Banco</span>";
		$q = "select t.idpais, p.nombre from tbl_transacciones t, tbl_paises p where t.idpais = p.id and t.tipoEntorno = 'P' and t.estado in ('A','V','B','R') and t.fecha_mod between $fecha1 and $fecha2 group by p.nombre order by count(*) desc, p.nombre";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();
		// print_r($arr1);
		$salida .= "
				<form name=\"queryspbanc\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{queryspbanc}'>
				</form>
				<div style='margin-left: -440px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.queryspbanc.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$q = "select b.id, b.banco from tbl_transacciones t, tbl_colPasarBancos c, tbl_bancos b where t.estado in ('A','V','B','R') and t.pasarela = c.idpasarela and c.idbanco = b.id and t.tipoEntorno = 'P' and t.fecha_mod between $fecha1 and $fecha2 group by b.id order by b.banco";
		$temp->query($q);
		$arr2 = $temp->loadAssocList();
		$aempr = 150;
		$ancho = 100 + ($abanco * (count($arr2)+2));
		$salida .= "<style>.total{background-color:#ffd75c;}.linea:hover{background-color:#FFEBD9;} .vr{width:".$ancho."px;}.vr, .vr div{height: 18px;float:left;padding:3px 0 !important;}.bord{font-weight:bold;}.cero{width:150px;}.bncs{width:".$abanco."px;}.medbncs{width:".($abanco/2)."px;}</style>";
		$salida .= "<div class='vr'><div class='bord cero'>&nbsp;</div><div class='bord bncs'>Totales</div>";
		$salidcsv .= ";Totales;;";
		for ($i=0;$i<count($arr2);$i++){
			$salida .= "<div class='bord bncs'>{$arr2[$i]['banco']}</div>";
			$arrban[] = $arr2[$i]['id'];
			$salidcsv .= "{$arr2[$i]['banco']};;";
		}
		$salida .= "</div>";
		$salidcsv .= "{n}";
		$listbnc = implode(",", $arrban);
		$salida .= "<div class='vr'><div class='bord cero'>Pa&iacute;ses</div><div class='bord medbncs'>Operaciones</div><div class='bord medbncs'>Valores</div>";
		$salidcsv .= "Países;Operaciones;Valores";
		for($i = 0; $i<count($arr2);$i++) {
			$salida .= "<div class='bord medbncs'>Operaciones</div><div class='bord medbncs'>Valores</div>";
			$salidcsv .= ";Operaciones;Valores";
		}
		$salidcsv .= "{n}";
		$salida .= "</div>";
		
//		var_dump($arr1);
		for ($j= 0; $j<count($arr1);$j++) {
			$salida .= "<div class='vr linea'><div class='bord cero'>".$arr1[$j]['nombre']."</div>";
			$salidcsv .= $arr1[$j]['nombre']."";
			$sale = $salcsv = "";
			$cantTP = $volTP = $k = 0;
			foreach ($arrban as $banid) {
				$q = "select count(idtransaccion) cant, sum($elem) valor from tbl_transacciones t, tbl_colPasarBancos c, tbl_bancos b where t.estado in ('A','V','B','R') and t.pasarela = c.idpasarela and c.idbanco = b.id and t.tipoEntorno = 'P' and t.fecha_mod between $fecha1 and $fecha2 and t.idpais = {$arr1[$j]['idpais']} and b.id in ($banid) group by b.id order by b.banco";
				$temp->query($q);
				$arr3 = $temp->loadAssocList();
				if ($arr3[0]['cant'] == null || $arr3[0]['cant'] == '') $cantop = 0;
				else $cantop = $arr3[0]['cant'];
				$arrCant[$k] += $cantop;
				$arrVol[$k] = $arrVol[$k] + 1*(number_format($arr3[0]['valor'],2,'.',''));
				$cantTP += $cantop;
				$volTP += 1*(number_format($arr3[0]['valor'],2,'.',''));
				$sale .= "<div class='medbncs'>{$cantop}</div><div class='medbncs'>".formatea_numero($arr3[0]['valor'])."</div>";
				$salcsv .= ";$cantop;".formatea_numero($arr3[0]['valor']);
				$k++;
			}
			$salida .= "<div class='medbncs total bord'>$cantTP</div><div class='medbncs total bord'>".formatea_numero($volTP)."</div>$sale</div>";
			$salidcsv .= ";$cantTP;".formatea_numero($volTP).$salcsv."{n}";
			
		}
		$salida .= "<div class='vr total'><div class='bord cero'>&nbsp;</div><div class='bncs bord'>Totales</div>";
		$salidcsv .= ";;Totales";
		for($i = 0; $i < count($arrCant); $i++){
			$salida .= "<div class='medbncs bord'>$arrCant[$i]</div><div class='medbncs bord'>".formatea_numero($arrVol[$i])."</div>";
			$salidcsv .= ";{$arrCant[$i]};".formatea_numero($arrVol[$i]);
		}
		$salidcsv .= "{n}";
		$salida .= "</div>";
		$salida .= "</div>";
		$salida .= "</div>";
		$salida = str_replace("{queryspbanc}", $salidcsv, $salida);
	}

	/*	Análisis de Países por Comercios */
		if ($d['pcom'] == 1) {
		$arr1 = $arr2 = $arr3 = $arrCant = $arrVol = array();
		$salidcsv = "";
		$salida .= "<span class='titule'>An&aacute;lisis de Países por Comercios</span>";
		$q = "select t.idpais, p.nombre from tbl_transacciones t, tbl_paises p where t.idpais = p.id and t.tipoEntorno = 'P' and t.estado in ('A','V','B','R') and t.fecha_mod between $fecha1 and $fecha2 group by p.nombre order by count(*) desc, p.nombre";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();
		// print_r($arr1);
		$salida .= "
				<form name=\"queryspbanc\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{queryspcom}'>
				</form>
				<div style='margin-left: -440px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.queryspbanc.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$q = "select t.idcomercio id, c.nombre from tbl_transacciones t, tbl_comercio c where t.estado in ('A','V','B','R') and t.idcomercio = c.idcomercio and t.tipoEntorno = 'P' and t.fecha_mod between $fecha1 and $fecha2 group by t.idcomercio order by c.nombre";
		$temp->query($q);
		$arr2 = $temp->loadAssocList();
		$abanco = 150;
		$ancho = 100 + ($abanco * (count($arr2)+2));
		$salida .= "<style>.total{background-color:#ffd75c;}.linea:hover{background-color:#FFEBD9;} .vr{width:".$ancho."px;}.vr, .vr div{height: 18px;float:left;padding:3px 0 !important;}.bord{font-weight:bold;}.cero{width:150px;}.bncs{width:".$abanco."px;}.medbncs{width:".($abanco/2)."px;}</style>";
		$salida .= "<div class='vr'><div class='bord cero'>&nbsp;</div><div class='bord bncs'>Totales</div>";
		$salidcsv .= ";Totales;;";
		for ($i=0;$i<count($arr2);$i++){
			$salida .= "<div class='bord bncs'>{$arr2[$i]['nombre']}</div>";
			$arrban[] = $arr2[$i]['id'];
			$salidcsv .= "{$arr2[$i]['nombre']};;";
		}
		$salida .= "</div>";
		$salidcsv .= "{n}";
		$listbnc = implode(",", $arrban);
		$salida .= "<div class='vr'><div class='bord cero'>Pa&iacute;ses</div><div class='bord medbncs'>Operaciones</div><div class='bord medbncs'>Valores</div>";
		$salidcsv .= "Países;Operaciones;Valores";
		for($i = 0; $i<count($arr2);$i++) {
			$salida .= "<div class='bord medbncs'>Operaciones</div><div class='bord medbncs'>Valores</div>";
			$salidcsv .= ";Operaciones;Valores";
		}
		$salidcsv .= "{n}";
		$salida .= "</div>";
		
//		var_dump($arr1);
		for ($j= 0; $j<count($arr1);$j++) {
			$salida .= "<div class='vr linea'><div class='bord cero'>".$arr1[$j]['nombre']."</div>";
			$salidcsv .= $arr1[$j]['nombre']."";
			$sale = $salcsv = "";
			$cantTP = $volTP = $k = 0;
			foreach ($arrban as $banid) {
				$q = "select count(idtransaccion) cant, sum($elem) valor from tbl_transacciones t, tbl_comercio c where t.estado in ('A','V','B','R') and t.idcomercio = c.idcomercio and t.tipoEntorno = 'P' and t.fecha_mod between $fecha1 and $fecha2 and t.idpais = {$arr1[$j]['idpais']} and t.idcomercio in ($banid) group by t.idcomercio order by c.nombre";
				$temp->query($q);
				$arr3 = $temp->loadAssocList();
				if ($arr3[0]['cant'] == null || $arr3[0]['cant'] == '') $cantop = 0;
				else $cantop = $arr3[0]['cant'];
				$arrCant[$k] += $cantop;
				$arrVol[$k] = $arrVol[$k] + 1*(number_format($arr3[0]['valor'],2,'.',''));
				$cantTP += $cantop;
				$volTP += 1*(number_format($arr3[0]['valor'],2,'.',''));
				$sale .= "<div class='medbncs'>{$cantop}</div><div class='medbncs'>".formatea_numero($arr3[0]['valor'])."</div>";
				$salcsv .= ";$cantop;".formatea_numero($arr3[0]['valor']);
				$k++;
			}
			$salida .= "<div class='medbncs total bord'>$cantTP</div><div class='medbncs total bord'>".formatea_numero($volTP)."</div>$sale</div>";
			$salidcsv .= ";$cantTP;".formatea_numero($volTP).$salcsv."{n}";
			
		}
		$salida .= "<div class='vr total'><div class='bord cero'>&nbsp;</div><div class='bncs bord'>Totales</div>";
		$salidcsv .= ";;Totales";
		for($i = 0; $i < count($arrCant); $i++){
			$salida .= "<div class='medbncs bord'>$arrCant[$i]</div><div class='medbncs bord'>".formatea_numero($arrVol[$i])."</div>";
			$salidcsv .= ";{$arrCant[$i]};".formatea_numero($arrVol[$i]);
		}
		$salidcsv .= "{n}";
		$salida .= "</div>";
		$salida .= "</div>";
		$salida .= "</div>";
		$salida = str_replace("{queryspcom}", $salidcsv, $salida);
	}

	/*	Análisis de Países por TPV	*/
		if ($d['ptpv'] == 1) {
		$arr1 = $arr2 = $arr3 = $arrCant = $arrVol = array();
		$salidcsv = "";
		$salida .= "<span class='titule'>An&aacute;lisis de Países por TPV</span>";
		$q = "select t.idpais, p.nombre from tbl_transacciones t, tbl_paises p where t.idpais = p.id and t.tipoEntorno = 'P' and t.estado in ('A','V','B','R') and t.fecha_mod between $fecha1 and $fecha2 group by p.nombre order by count(*) desc, p.nombre";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();
		// print_r($arr1);
		$salida .= "
				<form name=\"queryspbanc\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{querysptpv}'>
				</form>
				<div style='margin-left: -440px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.queryspbanc.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$q = "select t.pasarela id, p.nombre from tbl_transacciones t, tbl_pasarela p where t.estado in ('A','V','B','R') and t.pasarela = p.idPasarela and t.tipoEntorno = 'P' and t.fecha_mod between $fecha1 and $fecha2 group by t.pasarela order by p.nombre";
		$temp->query($q);
		$arr2 = $temp->loadAssocList();
		$abanco = 150;
		$ancho = 100 + ($abanco * (count($arr2)+2));
		$salida .= "<style>.total{background-color:#ffd75c;}.linea:hover{background-color:#FFEBD9;} .vr{width:".$ancho."px;}.vr, .vr div{height: 18px;float:left;padding:3px 0 !important;}.bord{font-weight:bold;}.cero{width:150px;}.bncs{width:".$abanco."px;}.medbncs{width:".($abanco/2)."px;}</style>";
		$salida .= "<div class='vr'><div class='bord cero'>&nbsp;</div><div class='bord bncs'>Totales</div>";
		$salidcsv .= ";Totales;;";
		for ($i=0;$i<count($arr2);$i++){
			$salida .= "<div class='bord bncs'>{$arr2[$i]['nombre']}</div>";
			$arrban[] = $arr2[$i]['id'];
			$salidcsv .= "{$arr2[$i]['nombre']};;";
		}
		$salida .= "</div>";
		$salidcsv .= "{n}";
		$listbnc = implode(",", $arrban);
		$salida .= "<div class='vr'><div class='bord cero'>Pa&iacute;ses</div><div class='bord medbncs'>Operaciones</div><div class='bord medbncs'>Valores</div>";
		$salidcsv .= "Países;Operaciones;Valores";
		for($i = 0; $i<count($arr2);$i++) {
			$salida .= "<div class='bord medbncs'>Operaciones</div><div class='bord medbncs'>Valores</div>";
			$salidcsv .= ";Operaciones;Valores";
		}
		$salidcsv .= "{n}";
		$salida .= "</div>";
		
//		var_dump($arr1);
		for ($j= 0; $j<count($arr1);$j++) {
			$salida .= "<div class='vr linea'><div class='bord cero'>".$arr1[$j]['nombre']."</div>";
			$salidcsv .= $arr1[$j]['nombre']."";
			$sale = $salcsv = "";
			$cantTP = $volTP = $k = 0;
			foreach ($arrban as $banid) {
				$q = "select count(idtransaccion) cant, sum($elem) valor from tbl_transacciones t, tbl_pasarela p where t.estado in ('A','V','B','R') and t.pasarela = p.idPasarela and t.tipoEntorno = 'P' and t.fecha_mod between $fecha1 and $fecha2 and t.idpais = {$arr1[$j]['idpais']} and t.pasarela in ($banid) group by t.pasarela order by p.nombre";
				$temp->query($q);
				$arr3 = $temp->loadAssocList();
				if ($arr3[0]['cant'] == null || $arr3[0]['cant'] == '') $cantop = 0;
				else $cantop = $arr3[0]['cant'];
				$arrCant[$k] += $cantop;
				$arrVol[$k] = $arrVol[$k] + 1*(number_format($arr3[0]['valor'],2,'.',''));
				$cantTP += $cantop;
				$volTP += 1*(number_format($arr3[0]['valor'],2,'.',''));
				$sale .= "<div class='medbncs'>{$cantop}</div><div class='medbncs'>".formatea_numero($arr3[0]['valor'])."</div>";
				$salcsv .= ";$cantop;".formatea_numero($arr3[0]['valor']);
				$k++;
			}
			$salida .= "<div class='medbncs total bord'>$cantTP</div><div class='medbncs total bord'>".formatea_numero($volTP)."</div>$sale</div>";
			$salidcsv .= ";$cantTP;".formatea_numero($volTP).$salcsv."{n}";
			
		}
		$salida .= "<div class='vr total'><div class='bord cero'>&nbsp;</div><div class='bncs bord'>Totales</div>";
		$salidcsv .= ";;Totales";
		for($i = 0; $i < count($arrCant); $i++){
			$salida .= "<div class='medbncs bord'>$arrCant[$i]</div><div class='medbncs bord'>".formatea_numero($arrVol[$i])."</div>";
			$salidcsv .= ";{$arrCant[$i]};".formatea_numero($arrVol[$i]);
		}
		$salidcsv .= "{n}";
		$salida .= "</div>";
		$salida .= "</div>";
		$salida .= "</div>";
		$salida = str_replace("{querysptpv}", $salidcsv, $salida);
	}

	/*	An&aacute;lisis por Banco - Moneda	*/
	if ($d['bancm'] == 1) {
		$arr1 = $arr2 = $arr3 = array();
		$salidcsv = "";
		$salida .= "<span class='titule'>An&aacute;lisis por Banco - Moneda</span>";
		$q = "select b.banco nombre,
					m.moneda,
					sum($elem) valor, count(t.idtransaccion) cantt, b.id idbnc , t.moneda idm
				from tbl_transacciones t, tbl_colPasarBancos c, tbl_bancos b, tbl_moneda m
				where t.moneda = m.idmoneda
					and t.pasarela = c.idpasarela
					and c.idbanco = b.id
					and t.tipoEntorno = 'P'
					and t.estado in ('A','V','B','R','D')
					and fecha_mod between $fecha1 and $fecha2
				group by b.id, t.moneda order by b.banco, t.moneda";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();
// $salida .=$q."<br>";

		$ancho = "570px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat6}'>
				</form>
				<div style='margin-left: -45px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
					."<li style='width:105px;'>Banco</li>"
					."<li style='width:90px;'>Moneda</li>"
					."<li style='width:70px;'>Valor &euro;</li>"
					."<li style='width:70px;'>Aceptadas</li>"
					."<li style='width:70px;'>Total</li>"
					."<li style='width:70px;'>Valor/Trans</li>"
					."<li>% Aceptadas</li></ul>";
		$salidcsv .= "Banco,Moneda,Valor (Euro),Aceptadas,Total,Valor/Trans,% Aceptadas{n}";
		for ($i=0;$i<count($arr1);$i++){
			$q = "select count(idtransaccion) canta from tbl_transacciones t, tbl_colPasarBancos c, tbl_bancos b where
					t.pasarela = c.idpasarela
					and c.idbanco = b.id
					and t.fecha_mod between $fecha1 and $fecha2
					and t.tipoEntorno = 'P'
					and t.estado in ('A','V','B','R')
					and c.idbanco = {$arr1[$i]['idbnc']}
					and t.moneda = {$arr1[$i]['idm']}";
			$temp->query($q);
// $salida .=$q."<br>";

			$salida .= "<ul class='respta' style='width:$ancho;'>"
							."<li style='width:105px;'>{$arr1[$i]['nombre']}</li>"
							."<li style='width:90px;'>{$arr1[$i]['moneda']}</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor'])."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('canta'),false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['cantt'],false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor']/$temp->f('canta'))."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('canta')/$arr1[$i]['cantt']*100)."</li></ul>";
			$salidcsv .= $arr1[$i]['nombre'].",".$arr1[$i]['moneda'].",".($arr1[$i]['valor']).",".($temp->f('canta')).","
							.($arr1[$i]['cantt']).",".($arr1[$i]['valor']/$temp->f('canta')).",".($temp->f('canta')/$arr1[$i]['cantt']*100)."{n}";
			$tot1 += $arr1[$i]['valor'];
			$tot2 += $temp->f('canta');
			$tot3 += $arr1[$i]['cantt'];
		}
		$salida .= "<ul class='respta tot' style='width:$ancho'>
						<li style='width:105px;'>Total:</li>"
						."<li style='width:90px;'>&nbsp;</li>"
						."<li style='width:70px;'>".formatea_numero($tot1)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot2,false)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot3,false)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot1/$tot2)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot2/$tot3*100)."</li></ul>";
		$salida .= "Total:,,".($tot1).",".($tot2).",".($tot3).",".($tot1/$tot2).",".($tot2/$tot3*100)."{n}";
		$salida .= "</div>";
		$salida = str_replace("{dat6}", $salidcsv, $salida);
	}

	/*	An&aacute;lisis por TPV - Comercio	*/
	if ($d['tpvc'] == 1) {
		$arr1 = $arr2 = $arr3 = array();
		$salidcsv = "";
		$salida .= "<span class='titule'>An&aacute;lisis por TPV - Comercio</span>";
		$q = "select p.nombre pasarela,
					c.nombre comercio,
					sum($elem) valor, count(idtransaccion) cantt, t.idcomercio, t.pasarela idp
				from tbl_transacciones t, tbl_pasarela p, tbl_comercio c
				where t.idcomercio = c.idcomercio
					and t.estado in ('A','V','B','R','D')
					and t.pasarela = idPasarela
					and t.tipoEntorno = 'P'
					and fecha_mod between $fecha1 and $fecha2
				group by t.pasarela, t.idcomercio order by p.nombre, c.nombre";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();

		$ancho = "650px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat8}'>
				</form>
				<div style='margin-left: -95px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
			."<li style='width:110px;'>Pasarela</li>"
			."<li style='width:120px;'>Comercio</li>"
			."<li style='width:70px;'>Valor &euro;</li>"
			."<li style='width:70px;'>Total</li>"
			."<li style='width:70px;'>Aceptadas</li>"
			."<li style='width:70px;'>Valor/Trans</li>"
			."<li>% Aceptadas</li></ul>";
		$salidcsv .= "Pasarela,Comercio,Valor (Euro),Total,Aceptadas,Valor/Trans,% Aceptadas{n}";
		
		for ($i=0;$i<count($arr1);$i++){
			$q = "select count(idtransaccion) canta from tbl_transacciones where
					fecha_mod between $fecha1 and $fecha2
					and tipoEntorno = 'P'
					and estado in ('A','V','B','R')
					and idcomercio = {$arr1[$i]['idcomercio']}
					and pasarela = {$arr1[$i]['idp']}";
			$temp->query($q);

			$salida .= "<ul class='respta' style='width:$ancho;'>"
							."<li style='width:110px;'>{$arr1[$i]['pasarela']}</li>"
							."<li style='width:120px;'>{$arr1[$i]['comercio']}</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor'])."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['cantt'],false)."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('canta'),false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor']/$temp->f('canta'))."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('canta')/$arr1[$i]['cantt']*100)."</li></ul>";
			$salidcsv .= $arr1[$i]['pasarela'].",{$arr1[$i]['comercio']},{$arr1[$i]['valor']},{$arr1[$i]['cantt']},{$temp->f('canta')},"
							.($arr1[$i]['valor']/$temp->f('canta')).",".($temp->f('canta')/$arr1[$i]['cantt']*100)."{n}";
			$tot1 += $arr1[$i]['valor'];
			$tot2 += $temp->f('canta');
			$tot3 += $arr1[$i]['cantt'];
		}
		$salida .= "<ul class='respta tot' style='width:$ancho'>
						<li style='width:110px;'>Total:</li>"
						."<li style='width:120px;'>&nbsp;</li>"
						."<li style='width:70px;'>".formatea_numero($tot1)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot3,false)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot2,false)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot1/$tot2)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot2/$tot3*100)."</li></ul>";
		$salidcsv .= "Total:,,$tot1,$tot3,$tot2,".($tot1/$tot2).",".($tot2/$tot3*100)."{n}";
		$salida .= "</div>";
		$salida = str_replace("{dat8}", $salidcsv, $salida);
	}

	/*	An&aacute;lisis por Comercio	*/
	if ($d['comerc'] == 1) {
	$arr1 = $arr2 = $arr3 = array();
		$salidcsv = "";
		$salida .= "<span class='titule'>An&aacute;lisis por Comercio</span>";
		$q = "select c.nombre comercio,
				sum($elem) valor, count(idtransaccion) cantt, t.idcomercio
			from tbl_transacciones t, tbl_comercio c
			where t.idcomercio = c.idcomercio
				and t.tipoEntorno = 'P'
				and fecha_mod between $fecha1 and $fecha2
			group by t.idcomercio order by c.nombre";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();

		$ancho = "1250px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat11}'>
				</form>
				<div style='margin-left: -330px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
						."<li style='width:120px;'>Comercio</li>"
						."<li style='width:100px;'>Valor Conc.</li>"
						."<li style='width:100px;'>Valor Web.</li>"
						."<li style='width:100px;'>Valor &euro;</li>"
						."<li style='width:100px;'>Valor/Trans</li>"
						."<li style='width:100px;'>Aceptadas Conc / %</li>"
						."<li style='width:100px;'>Aceptadas Web / %</li>"
						."<li style='width:100px;'>Aceptadas / %</li>"
						."<li style='width:100px;'>Denegadas / %</li>"
						."<li style='width:100px;'>En proceso / %</li>"
						."<li style='width:100px;'>Total</li>"
						."<li style='width:100px;'>Estimado</li></ul>";
		$salidcsv .= "Comercio,Valor Conc,Valor Web,Valor (Euro),Valor/Trans,Aceptadas / %,Denegadas / %,En proceso / %,Total,Estimado{n}";
		for ($i=0;$i<count($arr1);$i++){
		    $q = "select count(idtransaccion) canta from tbl_transacciones where
					fecha_mod between $fecha1 and $fecha2
					and tipoEntorno = 'P'
					and estado in ('A','V','B','R')
					and idcomercio = {$arr1[$i]['idcomercio']}";
		    $temp->query($q);
		    if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		    $tra = $temp->f('canta');//cant de operaciones Aceptadas general
		    
		    $temp->query("select count(t.idtransaccion) cant from tbl_transacciones t, tbl_reserva r where
					t.fecha_mod between $fecha1 and $fecha2
					and t.tipoEntorno = 'P'
                    and t.idtransaccion = r.id_transaccion
					and t.idcomercio = {$arr1[$i]['idcomercio']}");
		    if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		    $operc = $temp->f('cant'); //cant de operaciones desde el concentrador
		    $operw = $arr1[$i]['cantt'] - $operc;//cant de operaciones desde la web
		    
		    $temp->query("select sum($elem) valor, count(t.idtransaccion) canta from tbl_transacciones t, tbl_reserva r where
					t.fecha_mod between $fecha1 and $fecha2
					and t.tipoEntorno = 'P'
                    and t.idtransaccion = r.id_transaccion
					and t.estado in ('A','V','B','R')
					and t.idcomercio = {$arr1[$i]['idcomercio']}");
		    if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		    $valc = $temp->f('valor');
		    $trac = $temp->f('canta');//cant operaciones Aceptadas desde el Concentrador
		    $traw = $tra - $trac;//cant operaciones Aceptadas desde la web
			$valw = $arr1[$i]['valor'] - $valc;

			$q = "select count(idtransaccion) cantd from tbl_transacciones where
					fecha_mod between $fecha1 and $fecha2
					and tipoEntorno = 'P'
					and estado in ('D')
					and idcomercio = {$arr1[$i]['idcomercio']}";
			$temp->query($q);
			if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
			$trd = $temp->f('cantd');//cant de operaciones Denegadas general

			$q = "select count(idtransaccion) cantp from tbl_transacciones where
					fecha_mod between $fecha1 and $fecha2
					and tipoEntorno = 'P'
					and estado in ('P')
					and idcomercio = {$arr1[$i]['idcomercio']}";
			$temp->query($q);
			if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
			$trp = $temp->f('cantp');//cant de operaciones en Proceso general

			$salida .= "<ul class='respta' style='width:$ancho;'>"
							."<li style='width:120px;'>{$arr1[$i]['comercio']}</li>"
							."<li style='width:100px;'>".formatea_numero($valc)."</li>"
							."<li style='width:100px;'>".formatea_numero($valw)."</li>"
							."<li style='width:100px;'>".formatea_numero($arr1[$i]['valor'])."</li>"
							."<li style='width:100px;'>".formatea_numero($arr1[$i]['valor']/$tra)."</li>"
							."<li style='width:100px;'>".formatea_numero($trac,false).' / '.formatea_numero($trac/$operc*100)."</li>"
							."<li style='width:100px;'>".formatea_numero($traw,false).' / '.formatea_numero($traw/$operw*100)."</li>"
							."<li style='width:100px;'>".formatea_numero($tra,false).' / '.formatea_numero($tra/$arr1[$i]['cantt']*100)."</li>"
							."<li style='width:100px;'>".formatea_numero($trd,false).' / '.formatea_numero($trd/$arr1[$i]['cantt']*100)."</li>"
							."<li style='width:100px;'>".formatea_numero($trp,false).' / '.formatea_numero($trp/$arr1[$i]['cantt']*100)."</li>"
							."<li style='width:100px;'>".formatea_numero($arr1[$i]['cantt'],false)."</li>"
							."<li style='width:100px;'>".formatea_numero($arr1[$i]['valor']/$j*$int)."</li></ul>";
							$salidcsv .= "{$arr1[$i]['comercio']},{$valc},{$valw},{$arr1[$i]['valor']},".($arr1[$i]['valor']/$tra).",".$trac.' / '.($trac/$arr1[$i]['cantt']*100).",".$traw.' / '.($traw/$arr1[$i]['cantt']*100).",".$tra.' / '.($tra/$arr1[$i]['cantt']*100).",".$trd.' / '.($trd/$arr1[$i]['cantt']*100).",".$trp.' / '.($trp/$arr1[$i]['cantt']*100).",".$arr1[$i]['cantt'].",".($arr1[$i]['valor']/$j*$int)."{n}";
			$tot1 += $arr1[$i]['valor'];
			$tot2 += $tra;
			$tot3 += $arr1[$i]['cantt'];
			$tot4 += $trd;
			$tot5 += $trp;
			$tot6 += $trac;
			$tot7 += $traw;
			$tot8 += $valc;
			$tot9 += $valw;
			$tot10+= $operc;
			$tot11+= $operw;
	}
		$salida .= "<ul class='respta tot' style='width:$ancho'>
						<li style='width:120px;'>Total:</li>"
						."<li style='width:100px;'>".formatea_numero($tot8)."</li>"
						."<li style='width:100px;'>".formatea_numero($tot9)."</li>"
						."<li style='width:100px;'>".formatea_numero($tot1)."</li>"
						."<li style='width:100px;'>".formatea_numero($tot1/$tot2,false)."</li>"
						."<li style='width:100px;'>".formatea_numero($tot6,false).' / '.formatea_numero($tot6/$tot10*100)."</li>"
						."<li style='width:100px;'>".formatea_numero($tot7,false).' / '.formatea_numero($tot7/$tot11*100)."</li>"
						."<li style='width:100px;'>".formatea_numero($tot2,false).' / '.formatea_numero($tot2/$tot3*100)."</li>"
						."<li style='width:100px;'>".formatea_numero($tot4,false).' / '.formatea_numero($tot4/$tot3*100)."</li>"
						."<li style='width:100px;'>".formatea_numero($tot5,false).' / '.formatea_numero($tot5/$tot3*100)."</li>"
						."<li style='width:100px;'>".formatea_numero($tot3,false)."</li></ul>";
		$salidcsv .= "Total:,".($tot8).",".($tot9).",".($tot1).",".($tot2).",".($tot6).' / '.($tot6/$tot103*100).",".($tot7).' / '.($tot7/$tot11*100).",".($tot2).' / '.($tot2/$tot3*100).",".$tot4.' / '.($tot4/$tot3*100).",".$tot3.",".$tot5.' / '
						.($tot5/$tot3*100)."{n}";
		$salida .= "</div>";
		$salida = str_replace("{dat11}", $salidcsv, $salida);
	}
	
	/**
	 * Denegadas Comercio - moneda
	 */
	if ($d['dommon'] == 1) {
		$salidcsv = '';
		$arr1 = array();
		$salida .= "<span class='titule'>Denegadas por Comercio - Moneda</span>";
								
		$q = "select c.nombre comercio, m.moneda, t.id_error, count(t.idtransaccion) cant, t.idcomercio, t.moneda idmoneda
			from tbl_transacciones t, tbl_comercio c, tbl_moneda m
			where t.moneda = m.idmoneda
				and t.idcomercio = c.idcomercio
				and t.tipoEntorno = 'P'
				and t.estado in ('D')
				and fecha_mod between $fecha1 and $fecha2
			group by t.idcomercio, t.moneda, t.id_error order by c.nombre, t.moneda, t.id_error";
		$qq = '';
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();
		
		for ($i=0; $i<count($arr1); $i++){
			$q = "select count(r.idtransaccion) total from tbl_transacciones r where r.idcomercio = ".$arr1[$i]['idcomercio']." and r.moneda = ".$arr1[$i]['idmoneda']."
					and r.fecha_mod between $fecha1 and $fecha2 and r.estado in ('D') and r.tipoEntorno = 'P'";

			$temp->query($q);

			if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
			$arr1[$i]['total'] = $temp->f('total');
			
			$q = "select count(i.idtransaccion) total from tbl_transacciones i where i.idcomercio = ".$arr1[$i]['idcomercio']." and i.moneda = ".$arr1[$i]['idmoneda']."
					and i.fecha_mod between $fecha1 and $fecha2 and i.estado in ('A','V','B','R','D') and i.tipoEntorno = 'P'";
			$temp->query($q);

			if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
			$arr1[$i]['totG'] = $temp->f('total');
		}

		$ancho = "890px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat15}'>
				</form>
				<div style='margin-left: -30px;width: $ancho'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
						."<li style='width:105px;'>Comercio</li>"
						."<li style='width:90px;'>Moneda</li>"
						."<li style='width:370px;'>Error</li>"
						."<li style='width:70px;'>Cantidad</li>"
						."<li style='width:70px;'>Total Den</li>"
						."<li style='width:70px;'>% En tot oper den</li>"
						."<li style='width:70px;'>Total Gen</li></ul>";
		$salidcsv .= "Pasarela,Moneda,Error,Cantidad,Total Den,% En tot oper,Total Gen.{n}";
		
		$tot1 = $tot2 = $tot3 = 0;
		for ($i=0;$i<count($arr1);$i++){

			$salida .= "<ul class='respta' style='width:$ancho;'>"
							."<li style='width:105px;'>{$arr1[$i]['comercio']}</li>"
							."<li style='width:90px;'>{$arr1[$i]['moneda']}</li>"
							."<li style='width:370px;'>".$arr1[$i]['id_error']."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['cant'],false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['total'],false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['cant']*100/$arr1[$i]['total'])."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['totG'],false)."</li></ul>";
			$salidcsv .= $arr1[$i]['pasarela'].",".$arr1[$i]['moneda'].",".$arr1[$i]['id_error'].",".$arr1[$i]['cant'].","
							.($arr1[$i]['total']).",".($arr1[$i]['cant']*100/$arr1[$i]['total']).",".$arr1[$i]['totG']."{n}";

			$tot1 += $arr1[$i]['cant'];
			$tot2 += $arr1[$i]['total'];
			$tot3 += $arr1[$i]['totG'];
			
		}
		$salida .= "<ul class='respta tot' style='width:$ancho'>
						<li style='width:105px;'>Total:</li>"
						."<li style='width:90px;'>&nbsp;</li>"
						."<li style='width:370px;'>&nbsp;</li>"
						."<li style='width:70px;'>".formatea_numero($tot1,false)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot2,false)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot1/$tot2*100)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot3,false)."</li></ul>";
		$salidcsv .= "Total,,,$tot1,$tot2,".($tot1/$tot2*100).",$tot3{n}";
		$salida .= "</div>";
		$salida = str_replace("{dat15}", $salidcsv, $salida);
	}

	/*	An&aacute;lisis por Comercio - Moneda	*/
	if ($d['common'] == 1) {
		$salidcsv = '';
		$arr1 = $arr2 = $arr3 = array();
		$salida .= "<span class='titule'>An&aacute;lisis por Comercio - Moneda</span>";
		$q = "select c.nombre comercio,
				m.moneda,
				sum($elem) valor, count(idtransaccion) cantt, t.idcomercio idc, t.moneda idm
			from tbl_transacciones t, tbl_comercio c, tbl_moneda m
			where t.moneda = m.idmoneda
				and t.idcomercio = c.idcomercio
				and t.tipoEntorno = 'P'
				and t.estado in ('A','V','B','R','D','P')
				and fecha_mod between $fecha1 and $fecha2
			group by t.idcomercio, t.moneda order by c.nombre, t.moneda";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();

		$ancho = "500px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat12}'>
				</form>
				<div style='margin-left: -30px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = 0;
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
						."<li style='width:120px;'>Comercio</li>"
						."<li style='width:90px;'>Moneda</li>"
						."<li style='width:70px;'>Valor &euro;</li>"
						."<li style='width:70px;'>Aceptadas</li>"
						."<li style='width:70px;'>Valor/Trans</li>"
						."<li>% Aceptadas</li></ul>";
		$salidcsv .= "Comercio,Moneda,Valor (Euro),Aceptadas,Valor/Trans,% Aceptadas{n}";
		for ($i=0;$i<count($arr1);$i++){
			$q = "select count(idtransaccion) canta from tbl_transacciones where
					fecha_mod between $fecha1 and $fecha2
					and tipoEntorno = 'P'
					and estado in ('A','V','B','R')
					and idcomercio = {$arr1[$i]['idc']}
					and moneda = {$arr1[$i]['idm']}";
			$temp->query($q);

			$salida .= "<ul class='respta' style='width:$ancho;'>"
							."<li style='width:120px;'>{$arr1[$i]['comercio']}</li>"
							."<li style='width:90px;'>{$arr1[$i]['moneda']}</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor'])."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('canta'),false)."</li>"
							."<li style='width:70px;'>".formatea_numero($arr1[$i]['valor']/$temp->f('canta'))."</li>"
							."<li style='width:70px;'>".formatea_numero($temp->f('canta')/$arr1[$i]['cantt']*100)."</li></ul>";
			$salidcsv .= "{$arr1[$i]['comercio']},{$arr1[$i]['moneda']},{$arr1[$i]['valor']},{$temp->f('canta')},"
							.($arr1[$i]['valor']/$temp->f('canta')).",".($temp->f('canta')/$arr1[$i]['cantt']*100)."{n}";
			$tot1 += $arr1[$i]['valor'];
			$tot2 += $temp->f('canta');
			$tot3 += $arr1[$i]['cantt'];
		}
		$salida .= "<ul class='respta tot' style='width:$ancho'>
						<li style='width:120px;'>Total:</li>"
						."<li style='width:90px;'>&nbsp;</li>"
						."<li style='width:70px;'>".formatea_numero($tot1)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot2,false)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot1/$tot2)."</li>"
						."<li style='width:70px;'>".formatea_numero($tot2/$tot3*100)."</li></ul>";
		$salidcsv .= "Total:,,$tot1,$tot2,".($tot1/$tot2).",".($tot2/$tot3*100)."{n}";
		$salida .= "</div>";
		$salida = str_replace("{dat12}", $salidcsv, $salida);
	}

	/*	An&aacute;lisis por Comercio - Pagos seguros y no seguros	*/
	if ($d['comseg'] == 1) {
		$salidcsv = '';
		$arr1 = $arr2 = $arr3 = array();
		$elemn = str_replace("t.", "n.", $elem);
		$elemr= str_replace("t.", "r.", $elem);
		$salida .= "<span class='titule'>An&aacute;lisis por Comercio - Pagos seguros y no seguros</span>";
		$q = "select c.nombre comercio, count(t.idtransaccion) cantt, sum($elem) valort, t.idcomercio
			from tbl_comercio c, tbl_transacciones t
			where t.idcomercio = c.idcomercio
				and t.tipoEntorno = 'P'
				and t.tipoOperacion = 'P'
				and t.estado in ('A','V','B','R','D')
				and t.fecha_mod between $fecha1 and $fecha2
			group by t.idcomercio order by c.nombre";
			$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arr1 = $temp->loadAssocList();
// echo $q.";<br>";
		$ancho = "700px";
		$salida .= "
				<form name=\"exporta\" action=\"impresion.php\" method=\"POST\">
					<input type=\"hidden\" name=\"querys10\" value=\"1\">
					<input type=\"hidden\" name=\"inSql\" value='{dat13}'>
				</form>
				<div style='margin-left: -112px;width: 277px;'><span style=\"cursor: pointer;\" class=\"css_x-office-document\" onclick=\"document.exporta.submit()\"
						onmouseover=\"this.style.cursor=&quot;pointer&quot;\" alt=\"Exportar a CSV\" title=\"Exportar a CSV\"></span>";
		$tot1 = $tot2 = $tot3 = $tot4 = $tot5 = $tot6 = 0;
		$salida .= "<div id='acoge'>";
		$salida .= "<ul class='respta ttle' style='width:$ancho'>"
						."<li style='width:170px;'>Comercio</li>"
						."<li style='width:90px;'>Cantidad Tot</li>"
						."<li style='width:90px;'>Valor Tot (&euro;)</li>"
						."<li style='width:70px;'>Segura</li>"
						."<li style='width:90px;'>Valor Seg (&euro;)</li>"
						."<li style='width:70px;'>No Segura</li>"
						."<li style='width:90px;'>Valor NSeg (&euro;)</li></ul>";
		$salidcsv .= "Comercio,Cantidad Tot,Valor Tot,Segura,Valor Seg,No Segura,Valor NSeg{n}";
		for ($i=0;$i<count($arr1);$i++){
			if ($arr1[$i]['nsec'] == '') $arr1[$i]['nsec'] = 0;
			$q = "select count(r.idtransaccion) cans, sum($elemr) vals from tbl_transacciones r, tbl_pasarela p1
					where r.pasarela = p1.idPasarela
						and r.idcomercio = {$arr1[$i]['idcomercio']}
						and p1.secure = 1
						and r.tipoEntorno = 'P'
						and r.tipoOperacion = 'P'
						and r.estado in ('A','V','B','R','D')
						and r.fecha_mod between $fecha1 and $fecha2";
			$temp->query($q);
// echo $q.";<br>";
		$arr2 = $temp->loadAssocList();
			$q = "select count(n.idtransaccion) canns, sum($elemn) valns from tbl_transacciones n, tbl_pasarela p2
					where n.pasarela = p2.idPasarela
						and n.idcomercio = {$arr1[$i]['idcomercio']}
						and p2.secure = 0
						and n.tipoEntorno = 'P'
						and n.tipoOperacion = 'P'
						and n.estado in ('A','V','B','R','D')
						and n.fecha_mod between $fecha1 and $fecha2";
			$temp->query($q);
// echo $q.";<br>";
		$arr3 = $temp->loadAssocList();

			$salida .= "<ul class='respta' style='width:$ancho;'>"
							."<li style='width:170px;'>{$arr1[$i]['comercio']}</li>"
							."<li style='width:90px;'>{$arr1[$i]['cantt']}</li>"
							."<li style='width:90px;'>".number_format($arr1[$i]['valort'],2)."</li>"
							."<li style='width:70px;'>{$arr2[0]['cans']}</li>"
							."<li style='width:90px;'>".number_format($arr2[0]['vals'],2)."</li>"
							."<li style='width:70px;'>{$arr3[0]['canns']}</li>"
							."<li style='width:90px;'>".number_format($arr3[0]['valns'],2)."</li></ul>";
			$salidcsv .= "{$arr1[$i]['comercio']},{$arr1[$i]['cantt']},{$arr1[$i]['valort']},{$arr2[0]['cans']},
							{$arr2[0]['vals']},{$arr3[0]['canns']},{$arr3[0]['valns']}{n}";
			$tot1 += $arr1[$i]['cantt'];
			$tot2 += number_format($arr1[$i]['valort'],2);
			$tot3 += $arr2[0]['cans'];
			$tot4 += number_format($arr2[0]['vals'],2);
			$tot5 += $arr3[0]['canns'];
			$tot6 += number_format($arr3[0]['valns'],2);
			}
		$salida .= "<ul class='respta tot' style='width:$ancho'>
						<li style='width:170px;'>Total:</li>"
						."<li style='width:90px;'>".($tot1)."</li>"
						."<li style='width:90px;'>".($tot2)."</li>"
						."<li style='width:70px;'>".($tot3)."</li>"
						."<li style='width:90px;'>".($tot4)."</li>"
						."<li style='width:70px;'>".($tot5)."</li>"
						."<li style='width:90px;'>".($tot6)."</li></ul>";
		$salidcsv .= "Total:,$tot1,$tot2,".$tot3.",".$tot4.",".$tot5.",".$tot6."{n}";
		$salida .= "</div>";
		$salida = str_replace("{dat13}", $salidcsv, $salida);
	}

 	echo json_encode(array("salida"=>utf8_encode($salida),"error"=>$qq));

} elseif ($d['fun'] == 'insidiom') { //inserta las traducciones de invitaciones, concendiciones y vouchers
	$error = '';
	$tex = _IDIOMA_SALIDAOK;
    if ($d['ins']) {
//        $error = $d['tex']."\n";
        $q = "update tbl_traducciones set texto = '".$d['tex']."', fecha = unix_timestamp() where idIdioma = '{$d['idi']}' ".
        		"and idcomercio = '{$d['com']}' and tipo = '{$d['tipo']}'" ;
//        $q = "update tbl_traducciones set texto = '".utf8_encode($d['tex'])."', fecha = unix_timestamp() where idIdioma = '{$d['idi']}' and idcomercio = '{$d['com']}' and tipo = '{$d['tipo']}'" ;
//        $error .= $q;
        $temp->query($q);
		if ( $temp->getErrorMsg())	{$error = $temp->getErrorMsg();$tex='';}
    }
    $q = "select texto, from_unixtime(fecha,'%d/%m/%Y %H:%i:%s')fec from tbl_traducciones where idIdioma = {$d['idi']} ".
    		"and tipo = {$d['tipo']} and idcomercio = {$d['com']}";
   $error .= "\n".$q;
    $temp->query($q);
    $arrSal = array("tex"=>  utf8_encode(html_entity_decode($temp->f('texto'),ENT_HTML5)), "fec"=>$temp->f('fec'));

//    mail('jtoirac@gmail.com', 'Inserta idioma', $error);

    echo json_encode(array("cont"=>$arrSal, "error"=>$error, "tex"=>$tex));

} elseif ($d['fun'] == 'cambUSD') {

	$filenames = '/home/jtoirac/temp/correo.txt';
	$filename = 'http://localhost/cubatravelse/administrator/';
	$filename = 'http://localhost/cubatravelse';
	$filename = 'http://localhost/consumer_ex_results.jsp.html';
	$filename = 'http://corporate.visa.com/pd/consumer_services/consumer_ex_results.jsp?actualDate='.date("m/d/Y").
		'&homCur=USD&forCur=EUR&fee=0';
	$cont = "";
	$error = "";
	$error = filesize($filename);
//	$conts = file_get_contents($filenames);
//	echo "conts=$conts\n";

	if (ini_get('allow_url_include') == 0) ini_set('allow_url_include', 1);
	if (ini_get('allow_url_fopen') == 0) ini_set('allow_url_fopen', 1);

//		$handle = fsockopen("corporate.visa.com", 80, $errno, $errstr, 12);
//
//	   fputs($handle, "GET /pd/consumer_services/consumer_ex_rates.jsp?src=ex_rez HTTP/1.0\r\n");
//	   fputs($handle, "Host: corporate.visa.com\r\n");
//	   fputs($handle, "Referer: http://corporate.visa.com\r\n");
//	   fputs($handle, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n");
//		//echo $viart_xml;
//
//		//$handle = fopen('http://www.banco-metropolitano.com/tasasn.htm', 'r');
//		if ($handle) {
//			while (!feof($handle)) {
//				$cont .= trim(fgets($handle))."|";
//			}
//			fclose($handle);
//		} else {$error = 'No habre la url';}

//	$body = file_get_contents('http://corporate.visa.com/pd/consumer_services/consumer_ex_rates.jsp?src=ex_rez');


//	if (floatval(phpversion()) >= 4.3) {
//        $cont = file_get_contents($filename);
//    } else {
//        if (!file_exists($filename)) return -3;
//        $rHandle = fopen($filename, 'r');
//        if (!$rHandle) return -2;
//
//        $cont = '';
//        while(!feof($rHandle))
//            $cont .= fread($rHandle, filesize($filename));
//        fclose($rHandle);
//    }
	$handle = fopen($filename,'rb');
//	echo "handle=$handle\n";
	if ($handle) {
//		echo "entr\n";
		while (!feof($handle)) {
				$cont = trim(fgets($handle));
				if (strpos($cont, '<span class="results">') > -1) break;
			}
	echo $cont."va\n";
		fclose($handle);
	} else $error = "no se abriï¿½";
//	$cont = file_get_contents($filename);
//	$cont = substr($cont, strpos($cont, "<body"));
//	$cont = substr($cont, 0, strrpos($cont, "</body>"));

	echo json_encode(array("filename"=>$filename,"cont"=>  utf8_encode($cont), "error"=>$error));
} elseif ($d['fun'] == 'vouc') {
	if (strlen($d['htm']) < 1) {echo json_encode(array("error"=>_COMERCIO_ERROR_HTML)); exit;}
	if ($d['idio'] != 'En' && $d['idio'] != 'Es') {echo json_encode(array("error"=>_COMERCIO_ERROR_IDI)); exit;}
	if (strlen($d['com']) < 1) {echo json_encode(array("error"=>_COMERCIO_ERROR_COM)); exit;}

	$q = "update tbl_comercio set voucher".$d['idio']." = '".htmlentities($d['htm'], ENT_QUOTES, 'UTF-8')."' where idcomercio = ".$d['com'];
	$temp->query($q);

	echo json_encode(array('error'=>_COMERCIO_DAT));
} elseif ($d['fun'] == 'instrf') {
		sleep(1);
	$error=$pase="";
//	echo $ent->isDate($d['fec']);
	if (!$ent->isEntero($d['com'])) $error .= "Comercio incorrecto\n";
	if (!$ent->isAlfanumerico($d['cli'])) $error .= "Cliente incorrecto\n";
	if (!$ent->isNumero($d['imp'])) $error .= "Importe inv&aacute;lido\n";
	if (!$ent->isEntero($d['mon'])) $error .= "Moneda incorrecto\n";
	if (!$ent->isNumero($d['cmb'])) $error .= "Cambio incorrecto\n";
	if (!$ent->isNumero($d['eur'])) $error .= "Importe Total incorrecto\n";
	if (!$ent->isEntero($d['pas'])) $error .= "Pasarela incorrecto\n";
	if (!$ent->isAlfanumerico($d['mtv'])) $error .= "Motivo incorrecto\n";
	if (!$ent->isDate($d['fec'])) $error .= "Fecha incorrecto\n";

	if (strlen($error) == 0) {
        $query = "select * from tbl_comercio where id = {$d['com']}";
        $temp->query($query);
//        $comercioN = $temp->f('nombre');
//        $estCom = $temp->f('estado');
//        $datos = $temp->f('datos');
        $prefijo = $temp->f('prefijo_trans');
//        $datos = $temp->f('datos');
        $idCom = $temp->f('id');
        $idcomercio = $temp->f('idcomercio');
//        $valMin = $temp->f('minTransf');

//		$q = "select count(id) t from tbl_transferencias where facturaNum = '{$d['trf']}'";
//		$temp->query($q);
//
//		if ($temp->f('t') == 0) {
        $trans = trIdent($prefijo);
//			$salida = false;
//			$fecTr = to_unix($d['fec']);
//			while (!$salida) {
//				$trans = (string)($prefijo).(date("mdHis"));//.(rand (10, 99));
//				$query = "select count(*) total from tbl_transacciones where idtransaccion = '$trans'";
//				$temp->query($query);
//				if ($temp->f('total') == 0) $salida = true;
//
//				$query = "select count(*) from tbl_transacciones_old where idtransaccion = '$trans'";
//				$temp->query($query);
//				if ($temp->loadResult() != 0) $salida = false;
//			}

//			inserta valores en la tabla de las transacciones
			$hora = time();
			$query = "insert into tbl_transacciones	(idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor_inicial, valor, tipoEntorno,
						moneda, estado, pasarela, idioma, tasa, euroEquiv)
					values ('$trans', '$idcomercio', '$trans', 'T', $hora, $hora, '".($d['imp']*100)."', '".($d['imp']*100)."', 'P', '{$d['mon']}', 'A', '{$d['pas']}
						', 'es', '{$d['cmb']}', '{$d['eur']}')";
			$error =  $query;
			$temp->query($query);
			if (!$temp->error) $pase = 'Transferencia correctamente insertada.';
			else {
				$pase = '';
				$error = $temp->error;
			}

		//	inserta los valores en la tabla de transferencias
			if (strlen($pase) > 0) {
				$query = "insert into tbl_transferencias (idTransf, cliente, idcomercio, idCom, facturaNum, fecha, fechaTransf, valor, moneda, concepto, idioma,
								idPasarela, email, idadmin, estado)
							values ('$trans', '{$d['cli']}', '$idcomercio', '{$d['com']}', '$trans', '{$hora}', '{$hora}',
								'".($d['imp']*100)."', '{$d['mon']}', '{$d['mtv']}', 'es', '5', '{$d['correo']}', {$_SESSION['id']}, 'A')";
	//			echo $query;
				$temp->query($query);
				if (!$temp->error) $pase = 'Transferencia correctamente insertada.';
				else {
					$pase = '';
					$error = $temp->error;
				}
			}

//		} else $error = "Una transferencia con el mismo nï¿½mero ya se encuentra registrada";
	}

	echo json_encode(array('error'=>utf8_encode($error),'pase'=>utf8_encode($pase)));
} elseif ($d['fun'] == 'instpago') {
	/*Mantener comentado esto de aca
	error_log("server_origin=".$_SERVER['HTTP_ORIGIN']);
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}",
			'Access-Control-Allow-Credentials: true',
			'Access-Control-Max-Age: 86400');   
	}  

	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {  

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))  
			header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))  
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");  
	}

	hasta acá*/
	//if (_MOS_PHP_DEBUG) trigger_error("fun = ".$d['fun']."\npas = ".$d['pas']);
	$error = $arrSal = '';
	if (!$ent->isAlfanumerico($d['pas'])) $error .= "pasarela incorrecta\n";

	$q = "select distinct idPasarela, idcenauto from tbl_pasarela where tipo = 'P' and activo = 1 and idPasarela in (".$d['pas'].")";
	$temp->query($q);
	$arrCenauto = $temp->loadAssocList();

	if (count($arrCenauto) > 0) {

// 		echo "<br>arrSal solo<br>";
// 		var_dump($arrSal);
 		foreach ($arrCenauto as $key => $value) {
 		
			if ($arrCenauto['idcenauto'] == 12) {
				$q = "select r.nombre, r.email, r.iduser, r.tkuser from tbl_reserva r, tbl_pasarela p where r.pasarela = p.idPasarela and p.idcenauto = '".$arrCenauto['idcenauto']."' and email = '".$d['email']."' order by r.fecha desc limit 0,1";
				$temp->query($q);
				$arrUs = $temp->loadAssocList();
	// 			echo "<br>arrUs solo<br>";
	// 			var_dump($arrUs);
				if ($temp->num_rows())
					$arrSal[0] = $arrSal[0] + $arrUs[0];
			}
 		}
// 		echo "<br>arrSal todo<br>";
// 		var_dump($arrSal);
// 		
// 		//carga las monedas
		$q = "select distinct m.idmoneda, m.moneda from tbl_moneda m, tbl_colPasarMon c where c.idmoneda = m.idmoneda and m.activo = 1 and c.idpasarela in (".$d['pas'].")";
		$temp->query($q);
		$arrSal = $temp->loadAssocList();
		
		//carga las tarjetas de la(s) pasarela(s)
		$temp->query("select distinct t.id, convert(cast(convert(t.nombre using utf8) as binary) using latin1) 'nombre' from tbl_tarjetas t, tbl_colTarjPasar c where c.idTarj = t.id and c.idPasar in (".$d['pas'].")");
		$arrTarj = $temp->loadAssocList();
	} else $error .= 'Pasarela incorrecta u obsoleta';
	//if (_MOS_PHP_DEBUG) trigger_error(utf8_encode(json_encode(array('error'=>$error, 'sale'=>$arrSal, "tar"=>$arrTarj))));
	//$error = "select distinct t.id, convert(cast(convert(t.nombre using utf8) as binary) using latin1) from tbl_tarjetas t, tbl_colTarjPasar c where c.idTarj = t.id and c.idPasar in (".$d['pas'].")";

	echo json_encode(array('error'=>$error, 'sale'=>$arrSal, "tar"=>$arrTarj));
}

?>
