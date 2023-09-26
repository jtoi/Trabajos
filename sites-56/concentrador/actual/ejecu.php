<?php define('_VALID_ENTRADA', 1);
require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'admin/adminis.func.php' );
require_once( 'include/hoteles.func.php' );
$temp = new ps_DB;


$d = $_REQUEST;

error_log(json_encode($d));
// sendTelegram(json_encode($d));
$traza = json_encode($d);
$titulo = 'Servicios';


if ($d['fun'] == 'cargDatos') {

	if (date('n') >= 1 && date('n') <= 3) { // fechas del primer trimestre
		$fec1Ais = mktime(0, 0, 0, 01, 01, date("Y"));
		$fec2Ais = mktime(0, 0, 0 - 1, 04, 01, date("Y"));
	} elseif (date('n') >= 4 && date('n') <= 6) { // fechas del segundo trimestre
		$fec1Ais = mktime(0, 0, 0, 04, 01, date("Y"));
		$fec2Ais = mktime(0, 0, 0 - 1, 07, 01, date("Y"));
	} elseif (date('n') >= 7 && date('n') <= 9) { // fechas del tercer trimestre
		$fec1Ais = mktime(0, 0, 0, 07, 01, date("Y"));
		$fec2Ais = mktime(0, 0, 0 - 1, 10, 01, date("Y"));
	} elseif (date('n') >= 10 && date('n') <= 12) { // fechas del cuarto trimestre
		$fec1Ais = mktime(0, 0, 0, 10, 01, date("Y"));
		$fec2Ais = mktime(0, 0, 0 - 1, 01, 01, date("Y") + 1);
	}

	if (isset($d['ben'])) {
		$q = "select concat(c.nombre, ' ', c.papellido, ' ', c.sapellido, ' (', c.usuario,')') cli, c.correo, concat(o.titOrdenId, ' ', o.idtransaccion, ' ',t.identificador) oper, concat(format(t.valor_inicial/100, 2), ' ', m.moneda) val, from_unixtime(t.fecha, '%d/%m/%Y') fec, concat(b.nombre, ' ', b.papellido, ' ', b.sapellido, ' (', b.numDocumento, ')') bene, (select format(sum(t.valor_inicial)/100,2) 'Acumulado' from tbl_aisOrden o, tbl_transacciones t where o.idtransaccion = t.idtransaccion and o.idcliente = c.id and t.estado in ('A', 'B', 'V', 'R') and t.fecha between $fec1Ais and $fec2Ais) acumCliente, (select format(sum(t.valor_inicial)/100,2) 'Acumulado' from tbl_aisOrden o, tbl_transacciones t where o.idtransaccion = t.idtransaccion and o.idbeneficiario = b.id and t.estado in ('A', 'B', 'V', 'R') and t.fecha between $fec1Ais and $fec2Ais) acumBen from tbl_aisOrden o, tbl_aisBeneficiario b, tbl_aisCliente c, tbl_transacciones t, tbl_moneda m where m.idmoneda = t.moneda and o.idtransaccion = t.idtransaccion and o.idcliente = c.id and o.idbeneficiario = b.id and (o.idtransaccion = '".$d['ben']."' OR o.titOrdenId = '".$d['ben']."' OR t.identificador = '".$d['ben']."')";
		// echo $q; exit;

		$temp->query($q);
		// echo $temp->num_rows();exit;
		if ($temp->num_rows() == 1) {
			$arrSal = $temp->loadAssocList();
			// echo json_encode($arrSal); exit;
			$arrSal[0]['error'] = '';
		} else $arrSal[0]['error'] = utf8_encode('No se encuentra esa operaciÔøΩn');
	} else $arrSal[0]['error'] = 'No se enviaron datos';
	// $arrSal[0]['otro'] = 'dfgsdfgs';
	// var_dump($arrSal);

	$sal =  json_encode($arrSal);

	error_log($sal);
	echo $sal;
	
}

if ($d['fun'] == 'cargInsc') {

	if (isset($d['ben'])) {
		$q = "select b.idrazon, b.nombre, b.papellido, b.sapellido, b.telf, b.direccion, b.ciudad, b.numDocumento, (select c.idcimex  from tbl_aisClienteBeneficiario r, tbl_aisCliente c where r.idcliente = c.id and r.idbeneficiario = b.id order by r.fecha desc limit 0,1) cliente, (select r.idrelacion  from tbl_aisClienteBeneficiario r where r.idbeneficiario = b.id order by r.fecha desc limit 0,1) relac from tbl_aisBeneficiario b where idcimex = ".$d['ben'];
	} elseif (isset($d['cli'])) {
		$q = "select c.nombre, c.papellido, c.sapellido, c.usuario, from_unixtime(c.fnacimiento, '%d/%m/%Y') fnac, c.numDocumento, from_unixtime(c.fechaDocumento, '%d/%m/%Y') fdoc, c.correo, c.telf1, p1.iso2 pr, c.provincia, c.ciudad, c.direccion, c.CP, p2.iso2 pn, (c.sexo-1) sexo, c.ocupacion, c.salariomensual from tbl_aisCliente c, tbl_paises p1, tbl_paises p2 where c.paisResidencia = p1.id and c.paisNacimiento = p2.id and c.idcimex =  ".$d['cli'];
	}
	
	// echo($q);
	
	$temp->query($q);
	$arrSal = $temp->loadAssocList();
	$sal = json_encode($arrSal);
	error_log($sal);
	echo $sal;
}

if ($d['metodo'] == 'consulta') {
	$titulo .= ' Consulta de operaci√≥n';
	error_log('Consulta de operacion');
	if (strlen($d['comercio']) < 20) {
		$q = sprintf("select palabra from tbl_comercio where idcomercio = '%s'",$d['comercio']);
		$traza .= "<br>".stripslashes($q);
		$temp->query($q);

		if ($temp->num_rows()) {
			$traza .= "<br>".$d['comercio'].".".$d['transaccion'].".".$temp->f('palabra');
			$fima = hash("sha256", $d['comercio'].$d['transaccion'].$temp->f('palabra'));

			// echo $traza."   ".$fima;

			if ($d['firma'] == $fima) {
				$q = sprintf("select idtransaccion transaccion, identificador, id_error 'error', codigo codBanco, from_unixtime(fecha) 'fechaini', valor_inicial, moneda, from_unixtime(fecha_mod) 'fechamod', estado, solDev soldev from tbl_transacciones where identificador = '%s' and idcomercio = '%s'", $d['transaccion'], $d['comercio']);
				// echo stripslashes($q);
				$traza .= "<br>".stripslashes($q);
				$temp->query($q);

				if ($temp->num_rows()) {
					$arrRes = $temp->loadAssoc();
					$traza .= "<br>".json_encode($arrRes);
					echo json_encode($arrRes);

				} else echo "la transacci&oacute;n no est&aacute; en la Base de datos";
			} else echo "Las firmas no coinciden: $fima";
		} else echo "El comercio no existe";
	} else echo "Datos no v·lidos";
}

if ($d['metodo'] == 'rem1') {
	if (strlen($d['comercio']) < 20) {
		$q = sprintf("select id, palabra from tbl_comercio where idcomercio = '%s'",$d['comercio']);
		$temp->query($q);

		if ($temp->num_rows()) {
			if ($d['firma'] == hash("sha256", $d['comercio'].$d['operacion'].$temp->f('palabra'))) {

				if ($temp->f('id') == '633') $com = '633,39'; else $com = $temp->f('id');//si el comercio es Tocopay se buscan los de AIS tambiÔøΩn

				if (date('w') != 1) $antienr = mktime(23, 15, 0, date('m'), date('d') - 2, date('Y'));
				else $antienr = mktime(23, 15, 0, date('m'), date('d') - 4, date('Y'));

				$ayer = mktime(23, 15, 0, date('m'), date('d') - 1, date('Y'));
				$hoy = time();

				$q = "select count(id) total from tbl_aisCliente where idcomercio in ($com)";
				$temp->query($q);
				$sale['clientes'] = $temp->f('total');

				$q = "select count(id) total from tbl_aisCliente where idcomercio in ($com) and activo = 1";
				$temp->query($q);
				$sale['activos'] = $temp->f('total');

				$q = "select count(id) total from tbl_aisCliente where idcomercio in ($com) and fechaAltaCimex between $antienr and $ayer";
				$temp->query($q);
				$sale['nuevos'] = $temp->f('total');

				$q = "select count(id) total from tbl_aisBeneficiario where idcomercio in ($com)";
				$temp->query($q);
				$sale['beneficiarios'] = $temp->f('total');

				$sale['fechas'] = date('d/m/Y H:i',$antienr)." - ". date('d/m/Y H:i',$ayer);

				echo json_encode($sale);

			} else echo "ERROR: Las firmas no coinciden";
		} else echo "ERROR: El comercio no existe";
	} else echo "ERROR: Datos no validos";
}

if ($d['metodo'] == 'rem3') {
	if (strlen($d['comercio']) < 20) {
		$q = sprintf("select id, palabra from tbl_comercio where idcomercio = '%s'",$d['comercio']);
		$temp->query($q);

		if ($temp->num_rows()) {
			if ($d['firma'] == hash("sha256", $d['comercio'].$d['transaccion'].$d['operacion'].$temp->f('palabra'))) {
				echo cambiaSol ($d['transaccion']);
			}
		}
	}
}

if ($d['metodo'] == 'tasa') {
	if (strlen($d['comercio']) < 20) {
		$q = sprintf("select palabra from tbl_comercio where activo = 'S' and idcomercio = '%s'",$d['comercio']);
		$temp->query($q);

		if ($temp->num_rows()) {
			if ($d['firma'] == hash("sha256", $d['comercio'].$d['fecha'].$d['operacion'].$temp->f('palabra'))) {
				if ($d['comercio'] == '163430526040') { //el comercio es tocopay
					$temp->query("select pasarela from tbl_comercio where idcomercio = '163430526040'");
					$pasHab = $temp->f('pasarela');

					$q = "select max(tasa) tasa from tbl_colCambBanco where idmoneda = 840 and idbanco in (select idbanco from tbl_pasarela where idPasarela in ($pasHab)) group by fecha desc limit 0,1";
					// $correoMi .=  "<br>".$q;
					$temp->query($q);
					if ($temp->num_rows() == 0) {
						$q = "select tasa from tbl_colCambBanco where idmoneda = '840' and idbanco = 17 order by fecha desc limit 0,1";
						// $correoMi .=  "<br>\n".$q;
						$temp->query($q);
					}
					$ta = $temp->f('tasa');
					// $correoMi .=  "<br>\nta=".$ta;
					$vale = $ta + leeSetup('descCimex');

					$sale['USD'] = substr($vale,0,stripos($vale,'.')+5);
				} else {
					$q = "select moneda from tbl_moneda where idmoneda not in ('978', '192', '937')";
					$temp->query($q);

					$moneda = $temp->loadAssocList();
					
					foreach($moneda as $moneda){
						
						$vale = leeSetup($moneda['moneda']);
						if (strlen($vale) > 2) {
							$sale[$moneda['moneda']] = substr($vale,0,stripos($vale,'.')+5);
						} 
					}
				}
				echo json_encode($sale);
			} else echo "ERROR: Las firmas no coinciden";
		} else echo "ERROR: El comercio no existe";
	} else echo "ERROR: Datos no v·lidos";
}

if ($d['metodo'] == 'rem2') {
	if (strlen($d['comercio']) < 20) {
		$q = sprintf("select id, palabra from tbl_comercio where idcomercio = '%s'",$d['comercio']);
		$temp->query($q);

		if ($temp->num_rows()) {
			if ($d['firma'] == hash("sha256", $d['comercio'].$d['remitente'].$d['estado'].$temp->f('palabra'))) {

				if ($temp->f('id') == '633') $com = '633,39'; else $com = $temp->f('id');//si el comercio es Tocopay se buscan los de AIS tambiÔøΩn

				$q = "select count(id) from tbl_aisCliente where idcomercio in ($com) and idcimex = ".$d['remitente'];
				$temp->query($q);

				if ($temp->num_rows()) {

					$q = "update tbl_aisCliente set  activo = ".$d['estado']." where idcomercio in ($com) and idcimex = ".$d['remitente'];
					$temp->query($q);
					echo 'OK';
				} else echo 'ERROR: Remitente no existe';

			} else echo "ERROR: Las firmas no coinciden";
		} else echo "ERROR: El comercio no existe";
	} else echo "ERROR: Datos no v·lidos";

}

if ($d['metodo'] == 'encuesta') {
	$texto = 'Libera la ip de la oficina ';
	$titulo .= ' '.$texto;
	$texto .= json_encode($d);
	if ($d['firma'] == hash("sha256", $d['encuesta'].$d['IP']."Te he respondido cualquier cosa para que te calles. Tengo que ocuparme de cosas serias")) {

		if (strlen($d['IP']) < 17) {

			$q = "select count(*) total from tbl_ipBL where ip = '".$d['IP']."'";
			$temp->query($q);
			$texto .= "<br>Existe la ip? ".$temp->f('total');

			if ($temp->f('total') == 1 ) {
				$q = "delete from tbl_ipBL where ip = '".$d['IP']."'";
				$temp->query($q);

				$texto .= "<br>La IP ".$d['IP']." se desbloqueo... ";

				sendTelegram($texto);
				$traza = $texto;
				actSetup($d['IP'], 'ipOfic');
			}

			$q = "select count(*) total from tbl_ipsVPN where ip = '".$d['IP']."'";
			$temp->query($q);
			$texto .= "<br>Existe la ip? ".$temp->f('total');

			if ($temp->f('total') == 0 ) {
				$q = "insert into tbl_ipsVPN values (null, '".$d['IP']."', unix_timestamp(), 'Oficina')";
				$temp->query($q);

				$texto .= "<br>La IP ".$d['IP']." no estaba en ipsVPN... ";

				sendTelegram($texto);
				$traza = $texto;
				actSetup($d['IP'], 'ipOfic');
			}
		}
	}
}

//cuando hay Clientes repetidos cambia los datos del id viejo por los que tiene el id mas nuevo
if ($d['fun'] == 'repet') {
	$texto = json_encode($d)."<br>";
	if ($d['viejo'] < $d['nuevo']) {
		$q = "select * from tbl_aisCliente where id = ".$d['nuevo'];
		$texto .= $q."<br>";
		$temp->query($q);
		if ($temp->num_rows() == 1 ) {
			$arrSal = $temp->loadAssocList();

			$texto .= "delete from tbl_aisCliente where id = ".$d['nuevo']."<br>";
			$temp->query("delete from tbl_aisCliente where id = ".$d['nuevo']);

			$q = "update tbl_aisCliente set idcimex = '".$arrSal[0]['idcimex']."', usuario = '".$arrSal[0]['usuario']."', nombre = '".$arrSal[0]['nombre']."', papellido = '".$arrSal[0]['papellido']."', sapellido = '".$arrSal[0]['sapellido']."', fnacimiento = '".$arrSal[0]['fnacimiento']."', tipoDocumento = '".$arrSal[0]['tipoDocumento']."', numDocumento = '".$arrSal[0]['numDocumento']."', fechaDocumento = '".$arrSal[0]['fechaDocumento']."', correo = '".$arrSal[0]['correo']."', telf1 = '".$arrSal[0]['telf1']."', telf2 = '".$arrSal[0]['telf2']."', paisResidencia = '".$arrSal[0]['paisResidencia']."', provincia = '".$arrSal[0]['provincia']."', ciudad = '".$arrSal[0]['ciudad']."', direccion = '".$arrSal[0]['direccion']."', CP = '".$arrSal[0]['CP']."', paisNacimiento = '".$arrSal[0]['paisNacimiento']."', sexo = '".$arrSal[0]['sexo']."', ocupacion = '".$arrSal[0]['ocupacion']."', oficiopublico = '".$arrSal[0]['oficiopublico']."', idioma = '".$arrSal[0]['idioma']."', salariomensual = '".$arrSal[0]['salariomensual']."', fecha = '".$arrSal[0]['fecha']."', fechaAltaCimex = '".$arrSal[0]['fechaAltaCimex']."', subfichero = '1', correoenv = '".$arrSal[0]['correoenv']."', borrficheros = '0', ficgrandes = '0', bloq = '".$arrSal[0]['bloq']."', codConc = '".$arrSal[0]['codConc']."', idcomercio = '".$arrSal[0]['idcomercio']."' where id = ".$d['viejo'];
			$texto .= $q."<br>";
		} else $texto .= "No existe el cliente."; 
	}
	
	sendTelegram($texto);
}


// $temp->query("insert into tbl_traza values (null, '$titulo', '$traza', unix_timestamp())");


?>
