<?php
function setTransaccion ($cade){
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);

	$entrada['Correspondent'] = _CORRESPONDANT;
	$entrada['SubCorrespondent'] = _SUBCORRESPONDANT;
	$entrada['Branch'] = _BRANCH;

	$cade = json_encode($entrada);
    $sale = '';

    if (!EnvPing('/Transaction/ping')) return "Error: La API no puede recibir inscripciones de Cuentas";
	$sale = EnvCurl($cade, '/Transaction/create', 'POST');

    $salida = json_decode(utf8_encode($sale), true);
    if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200' || $salida['httpstatus'] == '202') {

        $q = "insert into tit_Transacciones (idTitanes, idCuenta, idTipo, idInstrumento, idBeneficiario, monto, concepto, fecha, idMonedaOriginal) values ('".$salida['data']['TransactionId'] ."', (select id from tit_Cuentas where idTitanes = '".$salida['data']['DestinationAccountId'] ."'), '".$salida['data']['TransactionType'] ."', '".$salida['data']['PaymentInstrument'] ."', '".$salida['data']['DestinationPersonId'] ."', '".$salida['data']['Amount'] ."', '".$salida['data']['Concept'] ."', curdate(), (select id from tit_Moneda where denominacion = '".$salida['data']['OriginCurrency'] ."'))";

		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		$idtr = $temp->last_insert_id('Id');

        //lanza una llamada a balance cuenta para actualizar los datos acá tal y como los tiene Titanes
		if ($salida['data']['DestinationPersonId'] > 0) {
	        balanceCuenta('{"PersonId":'.$salida['data']['DestinationPersonId'].', "AccountId":'.$salida['data']['DestinationAccountId'].'}');
		}
		if ($salida['data']['OriginPersonId'] > 0) {
	        balanceCuenta('{"PersonId":'.$salida['data']['OriginPersonId'].', "AccountId":'.$salida['data']['OriginAccountId'].'}');
		}
        // return array_merge($arrCtaBen, $arrCtaCli);
		return $salida['data']['TransactionId'];

    }
}


function setTpvTransaccion($cade) {
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);

    $ArrOrden['creationDate'] = str_replace(' ', "T", strval(date('Y-m-d H:i:s')));
    $ArrOrden['tpvReference'] = $entrada['tpvReference'];
    $ArrOrden['tpvId'] = $entrada['tpvId'];
    $ArrOrden['tpvPSP'] = $entrada['tpvPSP'];
    $ArrOrden['tpvAuthCode'] = $entrada['tpvAuthCode'];
    $ArrOrden['originalCurrency'] = $entrada['originalCurrency'];
    $ArrOrden['destinationCurrency'] = $entrada['destinationCurrency'];
    $ArrOrden['originalAmount'] = $entrada['originalAmount'];
    $ArrOrden['destinationAmount'] = $entrada['destinationAmount'];
    $ArrOrden['tpvReference'] = $entrada['tpvReference'];
    $ArrOrden['rate'] = $entrada['rate'];
    $ArrOrden['cardBrand'] = $entrada['cardBrand'];
    $ArrOrden['cardType'] = '';
    $ArrOrdenoro['TpvOperationInfo'] = $ArrOrden;
    $ArrOrdenoro['Concept'] = $entrada['Concept'];
    $ArrOrdenoro['OriginCurrency'] = $entrada['OriginCurrency'];
    $ArrOrdenoro['DestinationAccountId'] = $entrada['DestinationAccountId'];
    $ArrOrdenoro['DestinationPersonId'] = $entrada['DestinationPersonId'];
    $ArrOrdenoro['Amount'] = $entrada['Amount'];
    $ArrOrdenoro['PaymentInstrument'] = $entrada['PaymentInstrument'];
    $ArrOrdenoro['TransactionType'] = $entrada['TransactionType'];

    $cade = json_encode($ArrOrdenoro);
    $cade = str_replace("\/", "/", $cade);
    $sale = '';

    if (!EnvPing('/Transaction/ping')) return "Error: La API no puede recibir inscripciones de Cuentas";
	$sale = EnvCurl($cade, '/Transaction/create', 'POST');

    $sale = json_decode(utf8_encode($sale), true);
    if ($sale['httpstatus'] == '201' || $sale['httpstatus'] == '200' || $sale['httpstatus'] == '202') {

        $q = "insert into tit_Transacciones (idTitanes, idCuenta, idTipo, idInstrumento, idBeneficiario, monto, concepto, fecha, idMonedaOriginal) values ('".$sale['data']['TransactionId'] ."', (select id from tit_Cuentas where idTitanes = '".$sale['data']['DestinationAccountId'] ."'), '".$sale['data']['TransactionType'] ."', '".$sale['data']['PaymentInstrument'] ."', '".$sale['data']['DestinationPersonId'] ."', '".$sale['data']['Amount'] ."', '".$sale['data']['Concept'] ."', curdate(), (select id from tit_Moneda where denominacion = '".$sale['data']['OriginCurrency'] ."'))";

		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		$idtr = $temp->last_insert_id('Id');

        //lanza una llamada a balance cuenta para actualizar los datos acá tal y como los tiene Titanes
		balanceCuenta('{"PersonId":'.$sale['data']['DestinationPersonId'].', "AccountId":'.$sale['data']['DestinationAccountId'].'}');

		return $sale['data']['TransactionId'];

    }
}

function borraCuenta($cade)
{

	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);

	if (!EnvPing('/Account/ping')) return "Error: La API no puede recibir inscripciones de Cuentas";

	$sale = EnvCurl($cade, '/Account/delete', 'DEL');

	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {

		//verificación que exista el Cliente
		$q = "select c.Id from tit_Cuentas c, tit_Personas p where c.idPerson = p.Id and p.IdTitanes = '" . $salida['data']['PersonId'] . "' and c.IdTitanes = '" . $salida['data']['EntityIdToDelete'] . "'";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		$idcta = $temp->f('Id');

		if ($idcta > 0) {

			$q = "delete from tit_Cuentas where Id = '$idcta'";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);

			return $salida['data']['EntityIdToDelete'];
		} else return "La Cuenta no se encuentra inscrita en la BD";
	} else return "Error, la API devolviÃ³ error. " . $salida['description'] . strError($salida['error']);
}

function balanceCuenta($cade)
{

	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);

	if (!EnvPing('/Account/ping')) return "Error: La API no puede recibir inscripciones de Cuentas";

	$sale = EnvCurl($cade, '/Account/balance', 'POST');

	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {

		//verificación que exista el Cliente
		$q = "select c.Id from tit_Cuentas c, tit_Personas p where c.idPerson = p.Id and p.IdTitanes = '" . $salida['data']['PersonId'] . "' and c.IdTitanes = '" . $salida['data']['AccountId'] . "'";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		$idcta = $temp->f('Id');

		if ($idcta > 0) {

			$q = "update tit_Cuentas set balance = '" . $salida['data']['Balance'] . "', disponible = '" . $salida['data']['BalanceAvailable'] . "', porAcreditar = '" . $salida['data']['BalancePendingIn'] . "', porDebitar = '" . $salida['data']['BalancePendingOut'] . "' where Id = '$idcta'";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);

			return array($salida['data']['AccountId'],$salida['data']['Balance'], $salida['data']['BalanceAvailable'], $salida['data']['BalancePendingIn'], $salida['data']['BalancePendingOut']);
		} else return "La Cuenta no se encuentra inscrita en la BD";
	} else return "Error, la API devolvió error. " . $salida['description'] . strError($salida['error']);
}

function updateCuenta($cade)
{

	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);

	if (!EnvPing('/Account/ping')) return "Error: La API no puede recibir inscripciones de Cuentas";

	$sale = EnvCurl($cade, '/Account/update', 'PUT');

	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {

		//verificación que exista el Cliente
		$q = "select c.Id from tit_Cuentas c, tit_Personas p where c.idPerson = p.Id and p.IdTitanes = '" . $salida['data']['PersonId'] . "' and c.IdTitanes = '" . $salida['data']['AccountId'] . "'";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		$idcta = $temp->f('Id');

		if ($idcta > 0) {
			($entrada['IsAccountDefault'] == true) ? $default = 1 : $default = 0;

			$q = "update tit_Cuentas set idTipoCuenta = '" . $entrada['AccountTypeId'] . "', isDefault = '" . $default . "', alias = '" . $entrada['AccountAlias'] . "' where Id = '$idcta'";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);

			return $salida['data']['AccountId'];
		} else return "La Cuenta no se encuentra inscrita en la BD";
	} else return "Error, la API devolvió error. " . $salida['description'] . strError($salida['error']);
}

function creaCuenta($cade)
{

	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);

	if ($entrada['AccountTypeId'] < 5) {
		unset($entrada['AccountFormatId']);
		unset($entrada['AccountIsoCountryCode']);
		unset($entrada['AccountNumber']);
	}
	$cade = json_encode($entrada);

	if (!EnvPing('/Account/ping')) return "Error: La API no puede recibir inscripciones de Cuentas";

	$sale = EnvCurl($cade, '/Account/create', 'POST');

	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {

		//verificación que exista el Cliente
		$q = "select Id from tit_Personas where IdTitanes = '" . $salida['data']['PersonId']."'";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		$idcli = $temp->f('Id');

		if ($idcli > 0) {
			($entrada['IsAccountDefault'] == true) ? $default = 1 : $default = 0;
			$campos = "idTitanes, idPerson, idTipoCuenta, idpais, idFormatoCuenta, idMoneda, isDefault, alias, cuentaNum";
			$valores = "'" . $salida['data']['AccountId'] . "', '$idcli', '" . $entrada['AccountTypeId'] . "', (select Id from tit_Pais where Iso2 = '" . $entrada['AccountIsoCountryCode'] . "'), '" . $entrada['AccountFormatId'] . "', (select Id from tit_Moneda where denominacion = '" . $entrada['AccountIsoCurrencyTypeCode'] . "'), '" . $default . "', '" . $entrada['AccountAlias'] . "', '" . $salida['data']['AccountNumber'] . "'";

			if (!isset($entrada['AccountFormatId'])) {
				$campos = "idTitanes, idPerson, idTipoCuenta, idpais, idMoneda, isDefault, alias, cuentaNum";
				$valores = "'" . $salida['data']['AccountId'] . "', '$idcli', '" . $entrada['AccountTypeId'] . "', (select Id from tit_Pais where Iso2 = '" . $entrada['AccountIsoCountryCode'] . "'), (select Id from tit_Moneda where denominacion = '" . $entrada['AccountIsoCurrencyTypeCode'] . "'), '" . $default . "', '" . $entrada['AccountAlias'] . "', '" . $salida['data']['AccountNumber'] . "'";
			}

			$q = "insert into tit_Cuentas ($campos) values ($valores)";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);

			return $salida['data']['AccountId'];
		} else return "El Cliente no se encuentra inscrito en la BD";
	} else {
		return "Error, la API devolvió error. " . $salida['description']  . strError($salida['error']);
	}
}

function personSummary($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);
	$sale = EnvCurl($cade, '/Person/datasummary/', 'POST');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {

		//verificación que exista la persona
		$q = "select * from tit_Personas where IdTitanes = '" . $salida['data']['Id'] . "'";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);

		if ($temp->num_rows() > 0) {
			$active = $admited = 0;
			if ($salida['data']['Active'] == true) $active = 1;
			if ($salida['data']['Admited'] == true) $admited = 1;

			$q = "update tit_Personas set Active = '$active', Admited = '$admited' where IdTitanes = '" . $salida['data']['Id'] . "'";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);

			$q = "select * from tit_Personas where IdTitanes = '" . $salida['data']['Id'] . "'";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);
			$pers = $temp->loadAssocList();

			for ($i=0; $i<count($salida['data']['Relations']); $i++) {
				$q = "update tit_Relacion r, tit_Personas c, tit_Personas b set r.idTitanes = '".$salida['data']['Relations'][$i]['Id']."' where r.idTipo = '".$salida['data']['Relations'][$i]['RelationTypeId']."' and r.idCliente = c.id and c.idTitanes = '".$salida['data']['Id']."' and r.idBeneficiario = b.id and b.idTitanes = '".$salida['data']['Relations'][$i]['RelatedPersonId']."'";
				if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
				$temp->query($q);
	
			}
            
            return array("entrada" => $pers, "salida" => $salida);
		} else return "Error, La persona no se encuentra inscrita en la BD";
	} else {if (_TIT_CONFIG_DEBUG) setLog("Error, la API devolvió error. " . $salida['description'] . strError($salida['error']));
		return "Error, la API devolvió error. " . $salida['description'] . strError($salida['error']);}
}

function setRelation($cade)
{
	$temp = new ps_DB();
	
	$entrada = json_decode(utf8_encode($cade), true);
	
	$cade = json_encode( $entrada);
	$sale = EnvCurl($cade, '/Person/createRelation/', 'POST');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {

		//verificación que exista el Cliente
		$q = "select Id from tit_Personas where IdTitanes = '" . $entrada['PersonId'] . "' and IsBeneficiario = 0";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		$idcli = $temp->f('Id');

		if ($idcli > 0) {
			//verificación que exista el Beneficiario
			$q = "select Id from tit_Personas where IdTitanes = '" . $entrada['RelatedPersonId'] . "' and IsBeneficiario = 1";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);
			$idben = $temp->f('Id');

			if ($idben > 0) {
				//revisa que la relacion no este, si esta hace el update del tipod e relación
				$q = "select Id from tit_Relacion where idCliente = '$idcli' and idBeneficiario = '$idben'";

				if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
				$temp->query($q);

				if ($temp->num_rows() > 0) { // la relación ya estó se hace update

					$q = "update tit_Relacion set idTipo = '" . $entrada['RelatedTypeId'] . "' where IdCliente = '$idcli' and IdBeneficiario = '$idben'";

					if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
					$temp->query($q);
				} else { //no estó se hace insert

					//inserta la relación con el Person - Cliente
					$q = "insert into tit_Relacion (idCliente, idBeneficiario, idTipo ) values ($idcli, $idben, '" . $entrada['RelatedTypeId'] . "')";

					if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
					$temp->query($q);
				}
				return $entrada['PersonId'];
			} else return "El Beneficiario no se encuentra inscrito en la BD";
		} else return "El Cliente no se encuentra inscrito en la BD";
	} else return "Error, la API devolvió error. " . $salida['description'] . strError($salida['error']);
}

function updateRelation($cade)
{
	$temp = new ps_DB();
	
	$entrada = json_decode(utf8_encode($cade), true);
	$cade = json_encode( $entrada);
	$sale = EnvCurl($cade, '/Person/updaterelation/'.$entrada['idRel'], 'PUT');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {

		//verificación que exista el Cliente
		$q = "select Id from tit_Personas where IdTitanes = '" . $entrada['PersonId'] . "' and IsBeneficiario = 0";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		$idcli = $temp->f('Id');

		if ($idcli > 0) {
			//verificación que exista el Beneficiario
			$q = "select Id from tit_Personas where IdTitanes = '" . $entrada['RelatedPersonId'] . "' and IsBeneficiario = 1";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);
			$idben = $temp->f('Id');

			if ($idben > 0) {
				//revisa que la relacion no este, si esta hace el update del tipod e relación
				$q = "select Id from tit_Relacion where idCliente = '$idcli' and idBeneficiario = '$idben'";

				if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
				$temp->query($q);

				if ($temp->num_rows() > 0) { // la relación ya estó se hace update

					$q = "update tit_Relacion set idTipo = '" . $entrada['RelatedTypeId'] . "' where IdCliente = '$idcli' and IdBeneficiario = '$idben'";

					if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
					$temp->query($q);
				} else { //no estó se hace insert

					//inserta la relación con el Person - Cliente
					$q = "insert into tit_Relacion (idCliente, idBeneficiario, idTipo ) values ($idcli, $idben, '" . $entrada['RelatedTypeId'] . "')";

					if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
					$temp->query($q);
				}
				return $entrada['PersonId'];
			} else return "El Beneficiario no se encuentra inscrito en la BD";
		} else return "El Cliente no se encuentra inscrito en la BD";
	} else return "Error, la API devolvió error. " . $salida['description'] . strError($salida['error']);
}

function setBeneficiario($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);
    $relacion = $entrada['RelatedTypeIds'];
	$entrada['RelatedTypeIds'] = array(intval($entrada['RelatedTypeIds']));
	$cade = json_encode($entrada);

	$sale = EnvCurl($cade, '/Beneficiary/createBeneficiary/', 'POST');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {

		$q = "select Id from tit_Personas where IdTitanes = '" . $salida['data']['PersonId'] . "'";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);

		if ($temp->num_rows() == 0) {

			$temp->query("select Id from tit_TipoPersona where Tipo = '" . $entrada['PersonType'] . "'");
			$tipo = $temp->f('Id');

			$q = "insert into tit_Personas (idTipo, IdTitanes, IsBeneficiario, Nombre, PApellido, SApellido, BusinessName, CommercialName, FechaInsc ) 
            values ('" . $tipo . "', '" . $salida['data']['PersonId'] . "', '1', '" . $entrada['Name'] . "', '" . $entrada['LastName1'] . "', '" . $entrada['LastName2'] . "', '" . $entrada['BusinessName'] . "', '" . $entrada['CommercialName'] . "', curdate())";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);
			$beneficiario = $salida['data']['PersonId'];
			$idBenf = $temp->last_insert_id();

			//inserta la relación con el Person - Cliente, de acuerdo a lo devuelto por Titanes
			foreach ($salida['data']['Relations'] as $relation) {
				$q = "insert into tit_Relacion (idCliente, idBeneficiario, idTipo, idTitanes) values ((select id from tit_Personas where idTitanes = '".$entrada['RelationatedPersonId']."'), '$idBenf', '$relation', '')";
				if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
				$temp->query($q);
			}

			// Evía el Cliente a personSummary para poner el id de la relación
			// porque no la están enviado en este método
			personSummary("{'PersonId':'".$entrada['RelationatedPersonId']."'}");

			return $beneficiario;
		} else return "Error, el Beneficiario ya se encuentra inscrito en la BD";
	} else return "Error, la API devolvió error. " . $salida['description'] . strError($salida['error']);
}

function faltaDoc($cade)
{
	$entrada = json_decode(utf8_encode($cade), true);
	$sale = EnvCurl($cade, '/Document/requiredByPersonTypeId/' . $entrada['PersonId'], 'GET');
	
	$salida = json_decode(utf8_encode($sale), true);
	return $sale;
}

function updateDoc($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);
	$sale = EnvCurl($cade, '/Document/update/', 'POST');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {

		$q = "select Id from tit_Documento where idTitanes = '" . $salida['data']['DocumentId'] . "'";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);

		if ($temp->num_rows() != 0) {
			if ($entrada['IsDocumentDefault'] == "true") {
				$defecto = 1;
				$temp->query("update tit_Documento set isDefault = 0 where idPerson = '" . getPersonId($entrada['PersonId']) . "'");
			} else $defecto = 0;

			$q = "update tit_Documento set FechaExpir = '" . $entrada['FechaCaducidad'] . "', DocAlias = '" . $entrada['DocumentAlias'] . "', isDefault = '" . $defecto . "' where IdTitanes = '" . $entrada['DocumentId'] . "'";

			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);
			return $salida['data']['DocumentId'];
		} else return "Error el documento no se encuentra en la BD";
	} else return "Error, los datos no pudieron cambiarse. " . $salida['description'];
}

function sendDoc($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);
	$sale = EnvCurl($cade, '/Document/upload/', 'POST');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {
		if (count($salida['data']['ValidationErrors']) > 0) {
			$error = '';
			foreach ($salida['data']['ValidationErrors'] as $efecto) { 
				$error .= "\n" . $efecto;
			}
			return "Error, El contacto no se inscribió debido a $error";
		}
		
		$temp->query("select Id from tit_Documento where idTitanes = '" . $salida['data']['DocumentId'] . "'");
		if ($temp->num_rows() == 0) {
			if ($entrada['IsDocumentDefault'] == "true") {
				$defecto = 1;
				$temp->query("update tit_Documento set isDefault = 0 where idPerson = '" . getPersonId($entrada['PersonId']) . "'");
			} else $defecto = 0;

			$q = "insert into tit_Documento (idTitanes, idPerson, idMime, IdTipoDoc, idPaisExped, FechaExpir, DocAlias, isDefault, Documento) values ('" . $salida['data']['DocumentId'] . "', '" . getPersonId($entrada['PersonId']) . "', '" . getMimeId($entrada['MimeType']) . "', '" . $entrada['DocumentType'] . "', '" . getPaisId($entrada['ExpeditionCountryISO2']) . "', '" . $entrada['DocumentExpirationDate'] . "', '" . $entrada['DocumentAlias'] . "', '" . $defecto . "', '" . $entrada['DocumentValue'] . "')";


			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);
			return $salida['data']['DocumentId'];
		} else return "Error el documento ya se encuentra en la BD";
	} else return "Error, el documento no podido ser subido. " . $salida['description'];
}

function borraDir($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);
	$sale = EnvCurl($cade, '/Address/delete/', 'DEL');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {
		$q = "delete c.* from tit_Direccion c, tit_Personas p where c.idTitanes = '" . $entrada['EntityIdToDelete'] . "' and c.idPerson = p.Id and p.IdTitanes = '" . $entrada['PersonId'] . "'";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		return $entrada['EntityIdToDelete'];
	} else return "Error, el borrado del contacto ha devuelto error. " . $salida['description'];
}

function creaDirec($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);
	if (!EnvPing('/Address/ping')) return "Error: La API no puede recibir Direcciones";
	$sale = EnvCurl($cade, '/Address/create/', 'POST');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {
		$q = "select d.Id from tit_Direccion d, tit_Personas p where d.IdTitanes = '" . $salida['data']['AddressId'] . "' and d.IdPerson = p.Id and p.IdTitanes = '" . $entrada['PersonId'] . "'";

		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);

		if ($temp->num_rows() == 0) {
			if ($entrada['IsAddressDefault'] == "true") {
				$default = 1;
				$temp->query("update tit_Direccion set IsDefault = 0 where IdPerson = '" . getPersonId($entrada['PersonId']) . "'");
			} else $default = 0;

			$q = "insert into tit_Direccion (IdTitanes, IdPerson, IdPais, Direccion, Ciudad, CP, Provincia, Alias, IsDefault) values ('" . $salida['data']['AddressId'] . "', '" . getPersonId($entrada['PersonId']) . "', '" . getPaisId($entrada['AddressISOCountryCode']) . "', '" . $entrada['Address'] . "', '" . $entrada['City'] . "', '" . $entrada['PostalCode'] . "', '" . $entrada['Province'] . "', '" . $entrada['AddressAlias'] . "', '" . $default . "')";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);
			return $salida['data']['AddressId'];
		} else return "Error, la direcciÃ³n se encuentra en la base de datos";
	} else return "Error, el Create Address ha devuelto error. " . $salida['description'];
}

function updDirec($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);
	$sale = EnvCurl($cade, '/Address/update/' . $entrada['update'], 'POST');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {
		$q = "select d.Id from tit_Direccion d, tit_Personas p where d.IdTitanes = '" . $entrada['update'] . "' and d.IdPerson = p.Id and p.IdTitanes = '" . $entrada['PersonId'] . "'";

		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);

		if ($temp->num_rows() == 1) {
			if ($entrada['IsAddressDefault'] == "true") {
				$default = 1;
				$temp->query("update tit_Direccion set IsDefault = 0 where IdPerson = '" . getPersonId($entrada['PersonId']) . "'");
			} else $default = 0;

			$q = "update tit_Direccion set IdPais = '" . getPaisId($entrada['AddressISOCountryCode']) . "', Direccion = '" . $entrada['Address'] . "', Ciudad = '" . $entrada['City'] . "', CP = '" . $entrada['PostalCode'] . "', Provincia = '" . $entrada['Province'] . "', Alias = '" . $entrada['AddressAlias'] . "', IsDefault = '" . $default . "' where Id = '" . $temp->f('Id') . "'";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);
			return $salida['data']['AddressId'];
		} else return "Error, la direcciÃ³n no se encuentra en la base de datos";
	} else return "Error, el Update Address ha devuelto error. " . $salida['description'];
}

function borraContact($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);
	$sale = EnvCurl($cade, '/Contact/delete/', 'DELETE');

	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {
		$q = "delete c.* from tit_Contacto c, tit_Personas p where c.idTitanes = '" . $entrada['EntityIdToDelete'] . "' and c.idPerson = p.Id and p.IdTitanes = '" . $entrada['PersonId'] . "'";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		return $entrada['EntityIdToDelete'];
	} else return "Error, el borrado del contacto ha devuelto error. " . $salida['description'];
}

function updateContact($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);
	$sale = EnvCurl($cade, '/Contact/update/' . $entrada['idTitanes'], 'POST');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {
		if (count($salida['data']['ValidationErrors']) > 0) {
			$error = '';
			foreach ($salida['data']['ValidationErrors'] as $efecto) {
				$error .= "\n" . $efecto;
			}
			return "Error, El contacto no se actualizÃ³ debido a $error";
		}

		if ($entrada['IsContactMethodDefault'] == "true") {
			$defecto = 1;
			$temp->query("update tit_Contacto set isdefault = 0 where IdPerson = '" . getPersonId($entrada['IdPersona']) . "'");
		} else $defecto = 0;

		$q = "update tit_Contacto set dato = '" . $entrada['ContactMethodValue'] . "', alias = '" . $entrada['ContactMethodAlias'] . "', isdefault = '" . $defecto . "' where IdTitanes = '" . $entrada['idTitanes'] . "'";

		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);

		$q = "update tit_Personas set Validated = '" . $salida['PersonValid'] . "' where IdTitanes = '" . $entrada['IdPersona'] . "'";

		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);

		return $entrada['idTitanes'];
	} else return "Error, la actualizaciÃ³n ha devuelto error. " . $salida['description'];
}

function updateUser($cade)
{
	$temp = new ps_DB();

	$entrada = json_decode(utf8_encode($cade), true);

	if (!EnvPing('/Person/ping')) return "Error: La API no puede recibir inscripciones de Personas";
	$sale = EnvCurl($cade, '/Person/update/' . $entrada['IdPersona'], 'POST');
	
	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {
		//cambiar fecha
		$arrFech = explode('-', $entrada['DateOfBirth']);
		$fecha = $arrFech[2] . '-' . $arrFech[1] . '-' . $arrFech[0];

		// si todo va bien, se actualizan en la BD los datos de la persona
		$q = "update tit_Personas set Nombre = '" . $entrada['Name'] . "', PApellido = '" . $entrada['LastName1'] . "', SApellido = '" . $entrada['LastName2'] . "', BusinessName = '" . $entrada['BusinessName'] . "', CommercialName = '" . $entrada['CommercialName'] . "', DateOfBirth = '" . $fecha . "', IdActividad = '" . $entrada['Activity'] . "', IdPaisOrigen = '" . getPaisId($entrada['OriginISOCountryCode']) . "', IsPublicOffice = '" . $entrada['IsPublicOffice'] . "' where IdTitanes = '" . $entrada['IdPersona'] . "'";

		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);

		return $entrada['IdPersona'];
	} elseif ($salida['httpstatus'] == '401' || $salida['httpstatus'] == '400') {
		return "Error, La actualizaciÃ³n ha devuelto el error '" . $salida['error'][0] . "'";
	} else return "Error, la actualizaciÃ³n ha devuelto error";
}

function setContacto($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);

	$q = "select c.IdTitanes from tit_Contacto c, tit_Personas p where p.id = c.idPerson and p.idTitanes = '" . $entrada['PersonId'] . "' and IdMetodoContacto = '" . $entrada['ContactMethodTypeId'] . "'";
	$temp->query($q);
	if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
	$idTit = $temp->f('IdTitanes');

	if ($temp->num_rows() == 0) {
		// si no existe el tipo de contacto para esa persona se inserta el nuevo contacto
		$url = '/Contact/create';
		$metodo = 'POST';
	} else {
		//Si existe se hace un update de sus datos
		$url = "/Contact/update/$idTit";
		$metodo = 'PUT';
		//borramos el contacto existente para hacer un insert en la respuesta e igualarlo al create
		$temp->query("delete from tit_Contacto where idTitanes = '" . $idTit . "'");
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->delete from tit_Contacto where idTitanes = '" . $idTit . "'");
	}

	$sale = EnvCurl($cade, $url, $metodo);

	$salida = json_decode(utf8_encode($sale), true);

	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {
		if (count($salida['data']['ValidationErrors']) > 0) {
			$error = '';
			foreach ($salida['data']['ValidationErrors'] as $efecto) {
				$error .= "\n" . $efecto;
			}
			return "Error, El contacto no se inscribiÃ³ debido a $error";
		}

		$personId = getPersonId($entrada['PersonId']);

		if ($entrada['IsContactMethodDefault'] == "true") {
			$defecto = 1;
			$temp->query("update tit_Contacto set isdefault = 0 where IdPerson = '$personId'");
		} else $defecto = 0;

		$q = "update tit_Personas set Validated = '" . $salida['data']['PersonValid'] . "' where Id = " . $personId;
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);

		if ($personId == 0) return "Error: La persona no se encuentra en la DB";

		$q = "select Id from tit_Contacto where idTitanes = " . $salida['data']['ContactId'];
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);

		if ($temp->num_rows() == 0) {

			$q = "insert into tit_Contacto (idTitanes, IdPerson, IdMetodoContacto, dato, alias, isdefault) values ('" . $salida['data']['ContactId'] . "', '" . $personId . "', '" . $entrada['ContactMethodTypeId'] . "', '" . $entrada['ContactMethodValue'] . "', '" . $entrada['ContactMethodAlias'] . "', '$defecto')";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);

			return $salida['data']['ContactId'];
		} else return "Error, el Contacto se encuentra inscrito anteriormente";
	} else return "Error, la inscripciÃ³n ha devuelto error. " . $salida['description'];
}

function setUser($cade)
{
	$temp = new ps_DB();
	$entrada = json_decode(utf8_encode($cade), true);
	if (!EnvPing('/Person/ping')) return "Error: La API no puede recibir inscripciones de Personas";

	$sale = EnvCurl($cade, '/Person/Create', 'POST');

	$salida = json_decode(utf8_encode($sale), true);
	if ($salida['httpstatus'] == '201' || $salida['httpstatus'] == '200') {
		if ($salida['data']['IdPersonaRepetida'] > 0) {
			//si mandan IdPersonaRepetida reviso quien es y lanzo error
			$temp->query("select concat(Nombre, ' ', PApellido) persona from tit_Personas where IdTitanes = '" . $salida['data']['IdPersonaRepetida'] . "'");

			if ($temp->num_rows() > 0) return "Error, Titanes ha devuelto que la persona ya se encuentra repetida y es " . $temp->f('persona') . " que tiene el IdTitanes " . $salida['data']['IdPersonaRepetida'];
			else return "Error, Titanes ha devuelto que tiene la persona repetida con IdTitanes " . $salida['data']['IdPersonaRepetida'] . " pero en la BD nuestra no se encuentra";
		}


		// reviso que no se haya inscrito anteriormente
		// echo "select count(*) total from tit_Personas where idTitanes = ".$salida['data']['Id']." and PersonProfile = ".$salida['data']['PersonProfile'];
		$temp->query("select count(*) total from tit_Personas where idTitanes = " . $salida['data']['Id'] . " and PersonProfile = " . $salida['data']['PersonProfile']);
		if ($temp->f('total') > 0) return "Error, la persona se encuentra inscrita anteriormente";
		else {

			$isbenef = 1;
			if ($salida['data']['PersonProfile'] == 2) $isbenef = 0; //si es Beneficiario o no

			//revisa si la actividad devuelta existe
			$temp->query("select count(*) total from tit_Actividad where Id = " . $entrada['Activity']);
			if ($temp->f('total') == 0) { //si no estó la inserta
				$temp->query("insert into tit_Actividad (Id, Actividad) values (" . $salida['data']['MainActivityId'] . ", '" . $entrada['MainActivityDesc'] . "')");
				$idActividad = $temp->last_insert_id();
			} else $idActividad = $entrada['Activity'];

			//paós Origen
			$q = "select Id from tit_Pais where Iso2 = '" . $entrada['OriginISOCountryCode'] . "'";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);
			$pOrig = $temp->f('Id');

			//paós Documento
			$q = "select Id from tit_Pais where Iso2 = '" . $entrada['ExpeditionCountryISO2'] . "'";
			if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
			$temp->query($q);
			$pDoc = $temp->f('Id');

			//sexo
			($entrada['Gender'] == 'M') ? $idSex = 1 : $idSex = 2;
			$temp->query("insert into tit_Personas (idTipo, IdActividad, IdPaisOrigen, idGender, 
            IdTitanes, IsBeneficiario, Nombre, PApellido, SApellido, BusinessName, CommercialName, DateOfBirth, IsPublicOffice, BusinessPerson, Risk, PersonProfile, FechaInsc, IdPaisDoc, IdTipoDoc, Documento) values ('" . $salida['data']['PersonType'] . "', '" . $idActividad . "', '$pOrig', '$idSex', '" . $salida['data']['Id'] . "', $isbenef, '" . $entrada['Name'] . "', '" . $entrada['LastName1'] . "', '" . $entrada['LastName2'] . "', '" . $salida['data']['BusinessName'] . "', '" . $salida['data']['ComercialName'] . "', '" . $entrada['DateOfBirth'] . "', '" . $salida['data']['PublicOffice'] . "',  '" . $salida['data']['BusinessPerson'] . "', '" . $salida['data']['Risk'] . "', '" . $salida['data']['PersonProfile'] . "', '" . $salida['data']['InsertDate'] . "', '$pDoc', '" . $entrada['DocumentType'] . "', '" . $entrada['Document'] . "')");

			$id = $temp->last_insert_id();

			if ($id > 0) {
				personSummary("{'PersonId':'".$salida['data']['Id']."'}");

				return $salida['data']['Id'];
			}
			else return "Error: Aunque la inscripciÃ³n en Titanes se realizÃ³ bien con el Id: " . $salida['data']['Id'] . ", en la base de datos nuestra ocurriÃ³ un error y no se inscribiÃ³";
		}
	} else return "Error, la inscripciÃ³n ha devuelto error. " . $salida['description'];
}

function EnvCurl($cade, $dir, $method)
{

	$url = _URL_ENVIO . $dir;
	$token = verToken();

	if (strlen($token) > 5) {
		$header[] = "Content-Type: application/json";
		// $header[] = "Content-Length: ' " . strlen($cade);
		$header[] = "Authorization: Bearer $token";
		if (_TIT_CONFIG_DEBUG) setLog("CURL_URL->$url");
		if (_TIT_CONFIG_DEBUG) setLog("CURL_METODO->$method");
		if (_TIT_CONFIG_DEBUG) setLog("CURL_DATOS->$cade");
		if (_TIT_CONFIG_DEBUG) setLog("CURL_HEADER->" . json_encode($header));
		$options = array(
			CURLOPT_RETURNTRANSFER    => true,
			CURLOPT_SSL_VERIFYPEER    => false,
			CURLOPT_SSL_VERIFYHOST  => false,
			CURLOPT_POST            => true,
			CURLOPT_VERBOSE            => true,
			CURLOPT_URL                => $url,
			CURLOPT_POSTFIELDS        => $cade,
			CURLOPT_CUSTOMREQUEST    => $method,
			CURLOPT_HTTPHEADER        => $header
		);

		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$output = curl_exec($ch);
		if (curl_errno($ch)) {

			trigger_error("Error en la resp de Titanes: " . (curl_errno($ch)));
			if (_TIT_CONFIG_DEBUG) setLog("Error en la resp de Titanes para el mÃ©todo $dir: " . (curl_errno($ch)));
			if (_TIT_CONFIG_DEBUG) setLog(curl_error($ch));
		}
		if (_TIT_CONFIG_DEBUG) setLog("CURL_RESPUESTA->$output");

		$curl_info = curl_getinfo($ch);
		curl_close($ch);
		return $output;
	} else {
		if (_TIT_CONFIG_DEBUG) setLog("Error no hay Token válido");
		return false;
	}
}

function EnvPing($dir)
{
	$url = _URL_ENVIO . $dir;

	$token = verToken();
	if (strlen($token) > 5) {
		$header[] = 'Authorization: Bearer ' . $token;
		if (_TIT_CONFIG_DEBUG) setLog("PING_URL->$url");
		if (_TIT_CONFIG_DEBUG) setLog("PING_HEADER->" . json_encode($header));
		$options = array(
			CURLOPT_RETURNTRANSFER  => true,
			CURLOPT_SSL_VERIFYHOST  => false,
			CURLOPT_SSL_VERIFYPEER  => false,
			CURLOPT_ENCODING        => '',
			CURLOPT_MAXREDIRS       => 10,
			CURLOPT_TIMEOUT         => 0,
			CURLOPT_FOLLOWLOCATION  => true,
			CURLOPT_URL             => $url,
			CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST   => 'GET',
			CURLOPT_HTTPHEADER      => $header
		);

		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$output = curl_exec($ch);
		if (curl_errno($ch)) {

			trigger_error("Error en la resp de Titanes: " . (curl_errno($ch)));
			if (_TIT_CONFIG_DEBUG) setLog("Error en la resp de Titanes para el mÃ©todo $dir: " . (curl_errno($ch)));
			if (_TIT_CONFIG_DEBUG) setLog(curl_error($ch));
			return false;
		}
		if (_TIT_CONFIG_DEBUG) setLog("PING->$output");

		$curl_info = curl_getinfo($ch);
		curl_close($ch);
		if (stripos($output, 'pong')) return true;
		else {
			return false;
			if (_TIT_CONFIG_DEBUG) setLog($output);
		}
	} else {
		if (_TIT_CONFIG_DEBUG) setLog("Error no hay Token válido");
		return false;
	}
}

function verToken()
{
	$temp = new ps_DB();

	$q = "select Token from tit_Token where datediff (CURDATE(),Fecha) < 1";
	$temp->query($q);
	if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
	$token = $temp->f('Token');
	if (strlen($token) < 10) {
		if (_TIT_CONFIG_DEBUG) setLog("Entra a buscar el Token en Titanes");
		//El token ya no estó vigente, hay que solicitarlo
		$pa = "LXXmfYv34n";
		$url = _URL_ENVIO . "/Token";
		$sr = urlencode('dir.general@bidaiondo.com');
		$cade = 'grant_type=password&username=' . $sr . '&password=' . $pa;
		$method = 'GET';
		$postdata = json_encode(array(
			'username' => $sr,
			'password' => $pa,
			'grant_type' => 'password'
		));
		$header[] = 'Content-Type: application/x-www-form-urlencoded';

		if (_TIT_CONFIG_DEBUG) setLog("URL->$url");
		if (_TIT_CONFIG_DEBUG) setLog("METODO->$method");
		if (_TIT_CONFIG_DEBUG) setLog("DATOS->$cade");
		if (_TIT_CONFIG_DEBUG) setLog("HEADER->" . json_encode($header));

		//buscando el token si la fecha del óltimo es menor de 24 horas
		if (_TIT_CONFIG_DEBUG) setLog("HEADER->" . json_encode($header));

		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST    => false,
			CURLOPT_SSL_VERIFYPEER    => false,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_URL => $url,
			CURLOPT_POSTFIELDS => $cade,
			CURLOPT_HTTPHEADER => $header,
		);

		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$salida = curl_exec($ch);

		if (curl_errno($ch)) {

			trigger_error("Error en la resp de Titanes: " . (curl_errno($ch)));
			if (_TIT_CONFIG_DEBUG) setLog("Error en la resp de Titanes para el Token: " . (curl_errno($ch)));
			if (_TIT_CONFIG_DEBUG) setLog(curl_error($ch));
		}

		if (_TIT_CONFIG_DEBUG) setLog("CURL_TOKEN->$salida");

		$curl_info = curl_getinfo($ch);
		curl_close($ch);
		$arrCurl = json_decode($salida);
		$temp->query("delete from tit_Token");
		$q = "insert into tit_Token (Token, Fecha) values ('" . $arrCurl->access_token . "', curdate())";
		if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
		$temp->query($q);
		return $arrCurl->access_token;
	} else return $token;
}

function setLog($valor)
{
	if (strlen($valor) > 0) {
		$linea = "\n" . date('d-M-Y H:i:s.u') . " ->" . $valor;
		$file = fopen('salva.log', 'a');
		fwrite($file, "\n" . $linea);
		fclose($file);
	}
}


function retornaData($q)
{
	$temp = new ps_DB();

	if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
	$temp->query($q);
	$arrElem = $temp->loadAssocList();
	if (_TIT_CONFIG_DEBUG) setLog("JSONNN->" . json_encode($arrElem));
	return json_encode($arrElem);
}

function getPaisId($iso2)
{
	$temp = new ps_DB();
	$q = "select Id from tit_Pais where Iso2 = '$iso2'";
	if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
	$temp->query($q);

	if ($temp->num_rows() > 0) return $temp->f('Id');
	else return 0;
}

function getMimeId($mime = null)
{
	$temp = new ps_DB();
	$q = "select Id from tit_MimeType";

	if ($mime != null) $q .= " where Mime like '%$mime%'";
	if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
	$temp->query($q);
	if ($temp->num_rows() > 0) return $temp->f('Id');
	else return 0;
}

function getPersonId($idTitanes)
{
	$temp = new ps_DB();
	$q = "select Id from tit_Personas where IdTitanes = '$idTitanes'";

	if (_TIT_CONFIG_DEBUG) setLog("QUERY->$q");
	$temp->query($q);
	if ($temp->num_rows() > 0) return $temp->f('Id');
	else return 0;
}

function strError($arrErr){
    $sale='';
    foreach ($arrErr as $error) {
        $sale .= "\n".$error;
    }
    return $sale;
}

function vardump($args){
	if(count($args)){
		if (_TIT_CONFIG_DEBUG) setLog('VARDUMP->'.json_encode($args));
		for ($i=0; $i<count($args['Relations']);$i++) {
			$args['Relations'][$i]['RelatedPersonId'] = '<a class="enla" href="index.php?var=persummary&id='.$args['Relations'][$i]['RelatedPersonId'].'">'.$args['Relations'][$i]['RelatedPersonId'].'</a>';
		}

		ob_start();
		var_dump($args);
		$sale = ob_get_clean();
		$sale = str_replace("/var/www/sites56/concentrador/APITitanes/funciones.php:1052:", "",
				str_replace("'<a", "<a",
				str_replace("a>'", "a>",
				str_replace("<small>int</small>", "",
				str_replace("<small>string</small>", "",
				str_replace('&lt;', '<', 
				str_replace('&quot;', '"',
				str_replace('&quot;&gt;', '">', 
				str_replace('a&gt;', 'a>', $sale)))))))));
		$arrIn = explode("\n",$sale);
		foreach ($arrIn as $value) {
			if (stripos($value, 'array') < 1) {$arrOut[] = $value;}
			
		}
		$sale = implode('<br>', $arrOut);
		return $sale;
	} return '';
}
