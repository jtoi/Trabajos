<?php define('_VALID_ENTRADA', 1);
require_once('configuration.php');
require_once 'mysqli.php';
require_once 'funciones.php';
$temp = new ps_DB;


$hola = "Hola";


$d = $_REQUEST;
if (_TIT_CONFIG_DEBUG) setLog("Entrada de datos->" . json_encode($d));

if ($d['dato'] == '1') {

	echo retornaData("select Id, Actividad from tit_Actividad order by Id");
}

if ($d['dato'] == '2') {
	$where = "iso2 is not null";
	if ($d['pais'] * 1 > 0) $where .= " and Id = " . $d['pais'];
	echo retornaData("select Iso2, Nombre from tit_Pais where $where order by Nombre");
}

if ($d['dato'] == '3') {
	if (strlen($d['formato']) > 2 && strlen($d['formato']) < 6)
		echo retornaData("select Id from tit_FormatoCuenta where nombre like '%" . $d['formato'] . "%'");
}

if ($d['dato'] == '4') {
	echo retornaData("select Id, Nombre from tit_TipoDocumento");
}

if ($d['dato'] == 5) {
	if ($d['idpersona'] * 1 > 1)
		echo retornaData("select c.idTitanes, c.alias, c.cuentaNum from tit_Cuentas c, tit_Personas p where c.idPerson = p.Id and p.IdTitanes = '" . $d['idpersona'] . "'");
}

if ($d['dato'] == '6') {
	$where = " where ";

	if ($d['tipo'] == 'dir') $where = ", tit_Direccion d where d.IdPerson = p.Id and ";
	if ($d['tipo'] == 'cont') $where = ", tit_Contacto c where c.IdPerson = p.Id and ";
	if ($d['tipo'] == 'doc') $where = ", tit_Documento d where d.IdPerson = p.Id and ";
	if ($d['tipo'] == 'cta') $where = ", tit_Cuentas c where c.IdPerson = p.Id and ";

	if (isset($d['beneficiario']) && (isset($d['beneficiario']) == 0 || isset($d['beneficiario']) == 1)) $where .= " p.IsBeneficiario = '" . $d['beneficiario'] . "'";
	else $where .= " p.IsBeneficiario like '%'";

	$q = "select distinct p.IdTitanes, concat(p.Nombre,' ',p.PApellido,' ',p.SApellido, p.CommercialName) persona from tit_Personas p $where order by persona";

	echo retornaData($q);
}

if ($d['dato'] == '7') {
	if ($d['idPersona'] == '1') {
		$q = "select Id, nombre from tit_MetodoContacto";
	} elseif ($d['idPersona'] > 1) {
		$q = "select m.Id, m.nombre from tit_MetodoContacto m, tit_Contacto c, tit_Personas p where c.IdMetodoContacto = m.Id and c.idPerson = p.Id and p.IdTitanes = '" . $d['idPersona'] . "'";
	}

	echo retornaData($q);
}

if ($d['dato'] == 8) {
	if ($d['idTitanes'] > 0) {
		if ($d['idBen']) 
			$q = "select r.idTipo, r.idTitanes from tit_Relacion r, tit_Personas c, tit_Personas b where r.idCliente = c.id and r.idBeneficiario = b.id and r.idTipo != 20 and c.idTitanes = '".$d['idTitanes']."' and b.idTitanes =  '".$d['idBen']."' limit 0,1";
		else
			$q = "select distinct b.IdTitanes, concat(b.Nombre,' ',b.PApellido,' ',b.SApellido) beneficiario from tit_Personas p, tit_Relacion r, tit_Personas b where b.Id = r.idBeneficiario and r.idCliente = p.Id and p.IdTitanes = " . $d['idTitanes'];

		echo retornaData($q);
	}
}

if ($d['dato'] == '9') {
	if ($d['idmetodo'] > 0) {
		$error = $arrElem = '';
		$q = "select p.Id, idTipo, p.Nombre, PApellido, SApellido, BusinessName, CommercialName, date_format(DateOfBirth, '%d-%m-%Y') fechaNac, idActividad, a.Iso2, isPublicOffice from tit_Personas p, tit_Pais a where a.Id = p.IdPaisOrigen and p.IdTitanes = '" . $d['idmetodo'] . "' and IsBeneficiario = 0";
		$temp->query($q);

		if ($temp->num_rows() == 0) {
			$error = 'Error: No se encuenta la Persona seleccionada';
		} else {
			$arrElem = $temp->loadAssocList();
		}
		echo json_encode(array('error' => $error, 'pase' => $arrElem));
	}
}

if ($d['dato'] == '10') {
	$q = "select Id, Tipo from tit_TipoPersona order by Id";
	echo retornaData($q);
}

if ($d['dato'] == '11') {
	if ($d['idcuenta'] * 1 > 0) {
		echo retornaData("select idTitanes, idTipoCuenta, alias, cuentaNum, isDefault from tit_Cuentas where idTitanes = " . $d['idcuenta']);
	}
}

if ($d['dato'] == '12') {
	if ($d['idmetodo'] * 1 > 0) {
		echo retornaData("select c.idTitanes, c.dato, c.alias, c.isdefault from tit_Contacto c, tit_Personas p where p.id=c.idPerson and p.idTitanes = '" . $d['idPersona'] . "' and IdMetodoContacto = '" . $d['idmetodo'] . "'");
	} else {
		echo retornaData("select c.idTitanes, c.dato, c.alias, c.isdefault, c.IdMetodoContacto from tit_Contacto c, tit_Personas p where p.id=c.idPerson and p.idTitanes = '" . $d['idPersona'] . "'");
	}
}

if ($d['dato'] == 13) {
	if ($d['idtipo'] * 1 > 0) $q = "select Id, nombre from tit_TipoCuenta where Id = " . $d['idtipo'];
	else $q = "select Id, nombre from tit_TipoCuenta";
	echo retornaData($q);
}

if ($d['dato'] == 14) {
	echo retornaData("select Id, nombre from tit_FormatoCuenta");
}

if ($d['dato'] == 15) {
	echo retornaData("select Id, denominacion nombre from tit_Moneda");
}

if ($d['dato'] == '16') {
	if ($d['id'] * 1 > 0)
		echo retornaData("select d.Id, d.Alias from tit_Direccion d, tit_Personas p where p.Id = d.IdPerson and p.IdTitanes = '" . $d['id'] . "'");
}

if ($d['dato'] == '17') {
	if ($d['id'] * 1 > 0)
		echo retornaData("select d.IdTitanes, d.Alias, d.Direccion, d.Ciudad, d.CP, d.Provincia, d.IsDefault, p.Iso2 from tit_Direccion d, tit_Pais p where d.IdPais = p.Id and d.Id = '" . $d['id'] . "'");
}

if ($d['dato'] == 18) {
	echo retornaData("select distinct p.IdTitanes, concat(p.Nombre,' ',p.PApellido,' ',p.SApellido, p.CommercialName) persona from tit_Personas p where p.IsBeneficiario like '%' order by persona");
}

if ($d['dato'] == '19') {
	if ($d["def"]) $q = "select Id, Nombre from tit_TipoDocumento where defecto = 1";

	$q = "select Id, Nombre from tit_TipoDocumento";
	if ($d['tipo'] > 1 && !$d["def"]) $q .= " where Id = " . $d['tipo'];
	elseif ($d['tipo'] > 1 ) $q .= " where Id = " . $d['tipo'] . " and defecto = ".$d['def'];
	elseif ($d["def"]) $q .= " where defecto = ".$d['def'];
	echo retornaData($q);
}

if ($d['dato'] == '20') {
	echo json_encode(array("Id" => getMimeId($d['mime'])));
}

if ($d['dato'] == '21') {
	$pase = 1;
	$arrFech = explode("-", $d['fecha']);
	$fecha = $arrFech[2] . "-" . $arrFech[1] . "-" . $arrFech[0] . "T00:00:00";

	if (!$d['IdTitanes'] && $resp == '') {
		//chequeo de tamaño
		if ($_FILES['file']['size'] > 10000000) {
			echo json_encode(array('error' => "Error: sobrepasado el tamaño del fichero", 'pase' => ''));
			$pase = 0;
		}

		if ($d['persona'] == '') {
			echo json_encode(array('error' => "Error: La persona no tiene identificador asignado. Refresque la página e Intente nuevamente", 'pase' => ''));
			$pase = 0;
		}

		//chequeo del mime
		$mime = $_FILES['file']['type'];
		if (!getMimeId($mime) > 0) {
			echo json_encode(array('error' => "Error: no se acepta este tipo de fichero", 'pase' => ''));
			$pase = 0;
		}

		if ($pase == 0) exit;

		//convert a base64
		$base64 = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
		$str = json_encode(array(
			"PersonId"                  => $d['persona'],
			"DocumentType"              => $d['tipoDoc'],
			"MimeType"                  => $mime,
			"DocumentValue"             => $d['documento'],
			"DocumentExpirationDate"    => $fecha,
			"DocumentAlias"             => $d['alias'],
			"IsDocumentDefault"         => $d['defecto'],
			"ExpeditionCountryISO2"     => $d['pais'],
			"ImageData"                 => $base64
		));
		$resp = sendDoc($str);

		//salva el fichero en disco si se inscribe en titanes
		if ($resp > 0) {

			$q = "select concat(p.Nombre, '_', p.PApellido, '_', p.SApellido) nombre from tit_Personas p where IdTitanes = " . $d['persona'];
			$temp->query($q);

			$path = "documentos/" . $temp->f('nombre') . "/";
			if (_TIT_CONFIG_DEBUG) setLog("PATH->" . file_exists($path));

			if (!file_exists($path)) {
				mkdir($path, 0777, true);
			}
			$newname = $d['documento'] . "_" . date('Y_m_d') . ".";
			if (_TIT_CONFIG_DEBUG) setLog("FICHERO->$newname");

			$info = pathinfo($_FILES['file']['name']);
			$ext = $info['extension'];
			$newname .= $ext;

			$target = $path . $newname;
			if (_TIT_CONFIG_DEBUG) setLog("TARGET->$target");
			move_uploaded_file($_FILES['file']['tmp_name'], $target);
		}
	} else {

		if ($d['persona'] == '') {
			echo json_encode(array('error' => "Error: La persona no tiene identificador asignado. Refresque la página e Intente nuevamente", 'pase' => ''));
			$pase = 0;
		}

		if ($pase == 0) exit;

		$str = json_encode(array(
			"PersonId"                  => $d['persona'],
			"DocumentId"                => $d['IdTitanes'],
			"DocumentAlias"             => $d['alias'],
			"IsAccountDefault"          => $d['defecto'],
			"FechaCaducidad"            => $fecha
		));
		$resp = updateDoc($str);
	}

	if ($resp  > 0) {
		echo json_encode(array('error' => '', 'pase' => $resp));
	} else {
		echo json_encode(array('error' => $resp, 'pase' => ''));
	}
}

if ($d['dato'] == 22) {
	$q = "select d.Id, d.idTitanes, date_format(d.FechaExpir, '%d-%m-%Y') fexp, d.DocAlias, d.isDefault, d.Documento from tit_Documento d, tit_Personas p where p.Id = d.IdPerson and p.IdTitanes = '" . $d['idpersona'] . "'";

	if ($d['iddoc']) $q .= " and d.Id = '" . $d['iddoc'] . "'";

	echo retornaData($q);
}

if ($d['dato'] == 23) {
	$str = json_encode(array(
		"PersonId"                  => $d['persona']
	));
	$resp = faltaDoc($str);

	if (_TIT_CONFIG_DEBUG) setLog("LLEGANDO... $resp");
	echo json_encode(array('error' => '', 'pase' => $resp));
}

if ($d['dato'] == 24) {
	echo retornaData("select Id, nombre from tit_TipoRelacion");
}

if ($d['dato'] == 25) {
	echo retornaData("select IdPaisDoc, IdTipoDoc, Documento from tit_Personas where IdTitanes = '" . $d['id'] . "'");
}

if ($d['dato'] == 26) {
	echo retornaData("select IdPaisDoc, IdTipoDoc, Documento from tit_Personas where IdTitanes = '" . $d['id'] . "'");
}

if ($d['dato'] == 27) {
	if ($d['id'] > 0 && $d['activo'] < 3 && $d['admited'] < 3) {
		$temp->query("update tit_Personas set Active = '" . $d['activo'] . "', Admited = '" . $d['admited'] . "' where IdTitanes = " . $d['id']);
	}
}

if ($d['dato'] == 27) {
    if ($d['iso2']){
        echo retornaData("select a.Iso2, a.Nombre from tit_Pais a, tit_Personas p where a.id = p.IdPaisDoc and p.idTitanes = ".$d['iso2']);
    }
}

if ($d['dato'] == 28) {
	echo retornaData("select denominacion, denominacion nombre from tit_Moneda where denominacion != 'CUC' and denominacion != 'CUP'");
}

if ($d['dato'] == 29) {
    if ($d['cta']) {
        echo retornaData("select distinct denominacion, denominacion nombre from tit_Moneda m, tit_Cuentas c where c.idMoneda = m.id and denominacion != 'CUC' and denominacion != 'CUP' and c.id = ".$d['cta']);
    } else {
        echo retornaData("select distinct denominacion, denominacion nombre from tit_Moneda m, tit_Cuentas c where c.idMoneda = m.id and denominacion != 'CUC' and denominacion != 'CUP'");
	}
}

if ($d['dato'] == 30) {
    
    echo retornaData("select (select count(Id) from tit_Personas) personas, (select count(Id) from tit_Personas where IsBeneficiario = 0) cliente, (select count(Id) from tit_Personas where IsBeneficiario = 1) beneficiario ");
    
}


if ($d['dato'] == 31) {
    $where = '1=1';
    if ($d['ben'] != '&') $where .= " and IsBeneficiario = '".$d['ben']."'";
    if ($d['tipo'] != '&') $where .= " and idTipo = '".$d['tipo']."'";
    if ($d['nom'] != '%') $where .= " and (concat(Nombre, ' ', PApellido, ' ', SApellido) like '%".$d['nom']."%' or concat(BusinessName, ' ', CommercialName) like '%".$d['nom']."%')";
    echo retornaData("select distinct idTitanes tit, concat(Nombre, ' ', PApellido, ' ', SApellido, ' ', BusinessName, ' ', CommercialName) persona, case IsBeneficiario when 1 then 'Si' else 'No' end ben, case idTipo when 1 then 'Persona Física' else 'Persona Legal' end tipo from tit_Personas where $where ");
    
}

if ($d['dato'] == 32) {
	echo retornaData("select Id, nombre from tit_Razon ");
}

if ($d['dato'] == 33) {
	echo retornaData("select Id, Nombre from tit_TipoCuentaBenef ");
}


if ($d['func']) {
	$fun = $d['func'];
	unset($d['func']);
	unset($d['dato']);

	$resp = call_user_func($fun, json_encode($d));

	setLog("RESP-".json_encode($resp));
	setLog("FUNCION-".$fun);


    if (is_array($resp) && $fun != 'balanceCuenta' && $fun != 'personSummary') {
		$vari = vardump($resp['salida']['data']);
		// $vari = print_r($resp, true);
        // echo json_encode($resp);

		if (_TIT_CONFIG_DEBUG) setLog('SALIENDO->'.$vari);
		echo json_encode(array("salida" => $vari));

        exit;
    }

	if (_TIT_CONFIG_DEBUG) setLog("LLEGANDO... $resp");
    if (_TIT_CONFIG_DEBUG) setLog("COMPARA->".stripos($resp, 'error') );
	if (stripos($resp,'error')  > -1) {
		$salida = json_encode(array('error' => $resp, 'pase' => ''));
	} else {
		$salida = json_encode(array('error' => '', 'pase' => $resp));
	}
	if (_TIT_CONFIG_DEBUG) setLog("JSONSALIDA->" . $salida);
	echo $salida;
}

