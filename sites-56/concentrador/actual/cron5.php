<?php
/*
 * Lanza las operaciones de Fincimex a Titanes cada 5 min
 */
define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';
include_once( 'admin/adminis.func.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'include/correo.php' );

$correo = new correo;
$temp = new ps_DB;
$tiempoAtras = time() - (leeSetup('horaOperTit')*60*60); //2 horas
$tiempoMin = time() - (60*6); //2 min
$error = 0;
$correoMi = '';


$content = "Runing....";
$fileverf = "verifica.txt";
if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/$fileverf")) {
	$correo->todo(52, "Fichero cron5.php no se corrió", "Se saltó la ejecución del fichero a las fecha= " . date('d/m/Y H:i:s') );
	// exit;
} else {
	$fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/$fileverf", "wb");
	fwrite($fp, $content);
	fclose($fp);
}



//Revisa que no existan operaciones de fincimex
//que estén aceptadas y no tengan el número de orden de Titanes si las
//hay las reenvía nuevamente a Titanes


$q = "select t.idtransaccion, o.envia 'AmountToSend', o.recibe 'AmountToReceive', o.comision 'Charge', t.valor_inicial 'TotalAmount', c.idtitanes 'CustomerId', b.idtitanes 'BeneficiaryId', b.ciudad 'City', m.moneda 'CurrencyToSend', o.idrazon 'Reason' from tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t, tbl_moneda m where o.idbeneficiario = b.id and t.idtransaccion = o.idtransaccion and t.moneda = m.idmoneda and o.idcliente = c.id and (o.titOrdenId is null or o.titOrdenId = '') and t.estado = 'A' and t.fecha_mod between $tiempoAtras and $tiempoMin";
// $q = "select t.idtransaccion, o.envia 'AmountToSend', o.recibe 'AmountToReceive', o.comision 'Charge', t.valor_inicial 'TotalAmount', c.idtitanes 'CustomerId', b.idtitanes 'BeneficiaryId', b.ciudad 'City', m.moneda 'CurrencyToSend', o.idrazon 'Reason' from tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t, tbl_moneda m where o.idbeneficiario = b.id and t.idtransaccion = o.idtransaccion and t.moneda = m.idmoneda and o.idcliente = c.id and (o.titOrdenId is null or o.titOrdenId = '') and t.estado = 'A' and (t.fecha_mod between $tiempoAtras and $tiempoMin or t.idtransaccion in (220117200487))";
// $q = "select t.idtransaccion, o.envia 'AmountToSend', o.recibe 'AmountToReceive', o.comision 'Charge', t.valor_inicial 'TotalAmount', c.idtitanes 'CustomerId', b.idtitanes 'BeneficiaryId', b.ciudad 'City', m.moneda 'CurrencyToSend', o.idrazon 'Reason' from tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t, tbl_moneda m where o.idbeneficiario = b.id and t.idtransaccion = o.idtransaccion and t.moneda = m.idmoneda and o.idcliente = c.id and (o.titOrdenId is null or o.titOrdenId = '') and t.estado = 'A' and t.fecha_mod between 1604673801 and $tiempoMin";
// $q = "select t.idtransaccion, o.envia 'AmountToSend', o.recibe 'AmountToReceive', o.comision 'Charge', t.valor_inicial 'TotalAmount', c.idtitanes 'CustomerId', b.idtitanes 'BeneficiaryId', b.ciudad 'City', m.moneda 'CurrencyToSend', o.idrazon 'Reason' from tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t, tbl_moneda m where o.idbeneficiario = b.id and t.idtransaccion = o.idtransaccion and t.moneda = m.idmoneda and o.idcliente = c.id and (o.titOrdenId is null or o.titOrdenId = '') and t.estado = 'A' and t.fecha_mod between 1597551017 and 1601320235";
$temp->query($q);
$correoMi .= "<br>$q";
$arrTran = $temp->loadAssocList();
$correoMi .= "<br>".json_encode($arrTran);

for ($i = 0; $i < count($arrTran); $i++) {
	//Primero se invoca el StatusCode de la operación a ver si ya estaba en Titanes
	$correoMi .= "<br><br>Trabaja con la operación ".$arrTran[$i]['idtransaccion']."<br>";
	$data = array('idtransaccion'			=> $arrTran[$i]['idtransaccion']);
	$tipo = 'V';
	$sale = datATitanes($data,$tipo,91);
	$correoMi .= "<br>sale=".$sale;

	$arrVa = json_decode($sale);
	$correoMi .= "<br>estado=".$arrVa->Status;

	if ($arrVa->Status == "Not Registered") {
		//la operación no se encuentra en Titanes se lanza el addOrder
		($arrTran[$i]['City'] == NULL || $arrTran[$i]['City'] == '') ? $city = 'La Habana' : $city = $arrTran[$i]['City'];

		$customerId =$arrTran[$i]['CustomerId'];
		$benefId = $arrTran[$i]['BeneficiaryId'];
		$value = $arrTran[$i]['idtransaccion'];
		$data = array(
					'CustomerId'				=> $customerId,
					'BeneficiaryId'				=> $benefId,
					'Country'					=> 'CU',
					'City'						=> $city,
					'DeliveryType'				=> '4',
					'AmountToSend'				=> number_format(($arrTran[$i]['AmountToSend']/100),2,".",""),
					'CurrencyToSend'			=> $arrTran[$i]['CurrencyToSend'],
					'AmountToReceive'			=> number_format(($arrTran[$i]['AmountToReceive']/100),2,".",""),
					'CurrencyToReceive'			=> 'CUC',
					'Charge'					=> number_format(($arrTran[$i]['Charge']/100),2,".",""),
					'TotalAmount'				=> number_format(($arrTran[$i]['TotalAmount']/100),2,".",""),
					'Correspondent'				=> 'T086',
					'SubCorrespondent'			=> '1',
					'Branch'					=> 'T0860001',
					'Reason'					=> $arrTran[$i]['Reason'],
					'BenefBankName'				=> '',
					'BenefBankCity'				=> '',
					'BenefBankAccountNumber'	=> '-1',
					'BenefBankAccountType'		=> '3',
					'BenefBankAccountAgency'	=> '',
					'Ds_Merchant_Order'			=> $value
				);
		$data = array_merge($data, array(
					"Signature"					=> $customerId.$benefId.(number_format(($arrTran[$i]['AmountToReceive']/100),2,".","")).'CUC'));
		$tipo = 'O';
		$correoMi .= "<br>data=". json_encode($data);

		$sale = datATitanes($data,$tipo,91);

		$correoMi .= "<br>sale=$sale<br>\n";

		$arrVales = json_decode($sale);

		if ($arrVales->Id > 0) {
			$idTit			= $arrVales->Id;
			$StatusCode		= $arrVales->StatusCode;
			$Status			= $arrVales->Status;
			$Description	= $arrVales->Description;
			$Issues			= $arrVales->Issues;

			$correoMi .= "idTit=$idTit<br>";
			$correoMi .= "StatusCode=$StatusCode<br>";
			$correoMi .= "Status=$Status<br>";
			$correoMi .= "Description=$Description<br>";
			$correoMi .= "Issues=$Issues<br>";

			$q = "update tbl_aisOrden set titOrdenId = '$idTit' where idtransaccion = ".$value;
			$correoMi .= $q."<br>";
			$temp->query($q);

			if (!($idTit*1) > 50000) {

				$coderror = $Description." - ".$Issues[0]." ".$Issues[1]." ".$Issues[2]." ".$sale;
				$correoMi .= "La operación ".$value." de Cimex ha devuelto error en Titanes. Error devuelto: $coderror";

				$error = 1;
			}
		} else {
			$error = 1;
			$correoMi .= "La operación ".$value." de Cimex ha sido Revocada por Titanes ver si se devuelve. Error devuelto ". $sale;

			if (stripos($sale, "Beneficiary or relation to customer were not found") > 0) {
				//Revisa las relaciones Clientes Beneficiarios y restaura las que no se encuentren

				$data = array(
					"ClientId"			=> $customerId,
					"BeneficiaryId"		=> $benefId
				);

				$sale = datATitanes($data,'R',91);
				$correoMi .= $sale."<br>";

				if (stripos($sale, 'error')) {//código para cuando la relación no existe

					$q = "select idrelacion from tbl_aisClienteBeneficiario r, tbl_aisCliente c, tbl_aisBeneficiario b where r.idcliente = c.id and r.idbeneficiario = b.id and b.idtitanes = $benefId and c.idtitanes = $customerId";
					$correoMi .= $q."<br>";
					$temp->query($q);

					$data = array(
						"customerId"		=> $customerId,
						"beneficiaryId"		=> $benefId,
						"relation"			=> $temp->f('idrelacion')
					);

					$sale = datATitanes($data,'J',91);
					$correoMi .= $sale."<br>";

				}

			}
		}
	} else if ($arrVa->Status == "Pending") {
		$q = "update tbl_aisOrden set titOrdenId = '".$arrVa->Id."' where idtransaccion = ".$arrVa->Code;
		$correoMi .= "<br>Se actualiza la operación con el Id desde Titanes";
		$correoMi .= "<br>$q";
		$temp->query($q);
	}
}

//pasa las operaciones pendientes de transacciones y reserva a no procesadas
$temp->query("update tbl_transacciones set estado = 'N', id_error = 'A peticion del usuario se ha cancelado el pago' where estado = 'P' and tipoOperacion in ('P','R') and fecha_mod < unix_timestamp() - 2*60*60 and valor = 0");
$temp->query("update tbl_reserva set estado = 'N' where id_transaccion in (select idtransaccion from tbl_transacciones where estado = 'P' and tipoOperacion in ('P','R') and fecha_mod < unix_timestamp() - 2*60*60 and valor = 0)");

//Revisa que no queden Beneficiarios sin identificador de Titanes
$q = "select b.id, b.idcimex, c.idtitanes, r.idrelacion, b.nombre, b.papellido, b.sapellido, b.telf, b.direccion, b.ciudad, b.CP, b.numDocumento from tbl_aisBeneficiario b, tbl_aisClienteBeneficiario r, tbl_aisCliente c where r.idbeneficiario = b.id and r.idcliente = c.id and (b.idtitanes is null or b.idtitanes = '') and length(c.idtitanes) > 3 and b.fecha > $tiempoAtras";
$correoMi .= "<br>$q";
$temp->query($q);
$arrBen = $temp->loadAssocList();

for ($i = 0; $i < count($arrBen); $i++) {
	$correoMi .= "<br><br>".json_encode($arrBen[$i]);
	if ($arrBen[$i]['telf'] == '') $arrBen[$i]['telf'] = time();

	$data = array(
		"ClientId"				=> $arrBen[$i]['idtitanes'],
		"Name"					=> utf8_encode($arrBen[$i]['nombre']),
		"LastName1"				=> utf8_encode($arrBen[$i]['papellido']),
		"LastName2"				=> utf8_encode($arrBen[$i]['sapellido']),
		"DocumentNumber"		=> $arrBen[$i]['numDocumento'],
		"PhoneNumber"			=> $arrBen[$i]['telf'],
		"City"					=> utf8_encode($arrBen[$i]['ciudad']),
		"Address"				=> utf8_encode($arrBen[$i]['direccion']),
		"Relation"				=> $arrBen[$i]['idrelacion'],
		"Country"				=> 'CU',
		"Signature"				=> $arrBen[$i]['idtitanes'].utf8_encode($arrBen[$i]['nombre']).$arrBen[$i]['telf']
	);


	$tipo = 'B';
	$correoMi .= "<br>tipo=$tipo<br>";
	foreach ($data as $value => $item) {
		$correoMi .= $value . "=" . $item . "<br>\n";
	}

	$sale = datATitanes($data,'B',91);
	$correoMi .= $sale."<br>";
	if (strlen($sale) > 2) {
		$arrVales = json_decode($sale);
		if ($arrVales->Id > 0) {
			$idTit = $arrVales->Id;
			$q = "update tbl_aisBeneficiario set idtitanes = '".$idTit."', fechaAltaTitanes = ".time()." where idcimex = ".$arrBen[$i]['idcimex'];// para los beneficiarios
			$temp->query($q);
			$correoMi .= " beneficiario OK<br>\n";
			$correoMi .= " El Beneficiario ".$arrBen[$i]['nombre']." ".$arrBen[$i]['papellido']." ".$arrBen[$i]['sapellido'];
			$correoMi .= ". Viene de Ais con el identificador {$arrBen[$i]['idcimex']}, en nosotros es el {$arrBen[$i]['id']} y en Titanes está inscrito con el $idTit.";
		} else {
			$error = 1;
			$correoMi .= "<br>Error no viene el id";
		}
	} else {
		$error = 1;
		$correoMi .= "<br>Error en la comunicación a Titanes";
	}
}

/**
 * Bloquea la ip del proxy de Gaviota
 */
$q = "select count(*) total from tbl_ipBL where ip = '3.121.188.123'";
$temp->query($q);
echo $temp->f('total');
if ($temp->f('total') == 0) {
	$q = "insert into tbl_ipBL values (null, '3.121.188.123', '', '9000', unix_timestamp())";
	$temp->query($q);

	sendTelegram("Bloqueada la IP de Gaviota -> 3.121.188.123",null);
}

if (strlen($correoMi) > 10) {
	$correo->set_subject("Ejecutado cron5");
	$correo->set_message($correoMi);
	($error == 1) ? $correo->envia (4) : $correo->envia(53);
}


if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/$fileverf")) {
	unlink($_SERVER['DOCUMENT_ROOT'] . "/$fileverf");
}
return;

?>
