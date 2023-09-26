<?php

define('_VALID_ENTRADA', 1);
require_once('configuration.php');
include_once 'include/mysqli.php';
include_once 'include/hoteles.func.php';
include_once 'admin/adminis.func.php';
$temp = new ps_DB;

// var_dump($_REQUEST);
if ($_REQUEST['Usr']) {
	$q = "update tbl_aisCliente SET fecha = unix_timestamp(), subfichero = '1', borrficheros = 0 where binary usuario = '".$_REQUEST['Usr']."'";
	$temp->query($q);
	$q = "delete from tbl_aisFicheros where idcliente = (select id from tbl_aisCliente where binary usuario = '".$_REQUEST['Usr']."')";
	$temp->query($q);
	echo "La actualización fué bien, no molestes a Julio, espera 15 min y revisa si le subieron los documentos al Cliente";
}

if ($_REQUEST['Iden']) {
	$cm = 'A';
	if ($_REQUEST['desh'] == '1') $cm = 'T';
	$q = "update tbl_aisBeneficiario b, tbl_aisOrden o, tbl_aisCliente c set b.bloq = '" . $_REQUEST['desh'] . "', c.bloq = '" . $_REQUEST['desh'] . "', o.estado = '$cm'  where o.idcliente = c.id and o.idbeneficiario = b.id and o.titOrdenId in (" . $_REQUEST['Iden']. ")";
	// echo $q;
	$temp->query($q);
	$q = "select case c.bloq when '0' then 'Habilita' when '1' then 'Deshabilita' end 'cliente', case b.bloq when '0' then 'Habilita' when '1' then 'Deshabilita' end  'benef' from tbl_aisBeneficiario b, tbl_aisOrden o, tbl_aisCliente c where o.idcliente = c.id and o.idbeneficiario = b.id and o.titOrdenId in (" . $_REQUEST['Iden'].")";
	$temp->query($q);
	echo "<br>Cliente: ".$temp->f('cliente'). "<br>Benef: " . $temp->f('benef') . "<br>";
}


if ($_REQUEST['donde'] == 2) {

	if (($_REQUEST['IdCustomer'] * 1) > 0 && ($_REQUEST['IdBeneficiary'] * 1) > 0) { //si envío ambos
		//saco ids de cliente
		$q = "select id, idtitanes from tbl_aisCliente where idcimex = '{$_REQUEST['IdCustomer']}'";
		$temp->query($q);
		$idCTit = $temp->f('idtitanes');
		$idC = $temp->f('id');

		//saco ids de Beneficiario
		$q = "select id, idtitanes from tbl_aisBeneficiario where idcimex = '{$_REQUEST['IdBeneficiary']}'";
		$temp->query($q);
		$idBTit = $temp->f('idtitanes');
		$idB = $temp->f('id');

		//chequeo que exista la relación entre ellos
		$q = "select id from tbl_aisClienteBeneficiario where idcliente = '$idC' and idbeneficiario = '$idB'";
		$temp->query($q);
		if ($temp->num_rows() == 0) {
			echo "Error no existe la relación entre ese Cliente y el Beneficiario";
			exit;
		}
	} elseif (($_REQUEST['IdCustomer'] * 1) > 0 && ($_REQUEST['IdBeneficiary'] * 1) == 0) { //se envía un cliente
		//busco los ids del Cliente
		$q = "select id, idtitanes from tbl_aisCliente where idcimex = '{$_REQUEST['IdCustomer']}'";
		$temp->query($q);
		$idCTit = $temp->f('idtitanes');
		$idC = $temp->f('id');

		$q = "select b.idcimex, concat(b.nombre,' ',b.papellido,' ',b.sapellido) benef from tbl_aisBeneficiario b, tbl_aisClienteBeneficiario r where r.idcliente = '$idC' and r.idbeneficiario = b.id";
		$temp->query($q);
		if ($temp->num_rows() == 0) {
			echo "Error no no hay Beneficiario para este Cliente";
			exit;
		} else {
			$arrBen = $temp->loadResultArray();
			var_dump($arrBen);
			exit;
		}
	} elseif (($_REQUEST['IdBeneficiary'] * 1) > 0 && ($_REQUEST['IdCustomer'] * 1) == 0) {
		$q = "select b.idcimex 'idB', b.idtitanes idBTit, c.idcimex 'idC', c.idtitanes 'idCTit'
		from tbl_aisBeneficiario b, tbl_aisClienteBeneficiario r, tbl_aisCliente c
		where r.idcliente = c.id
		and r.idbeneficiario = b.id
		and b.idcimex = '{$_REQUEST['IdBeneficiary']}' limit 0,1";
		$temp->query($q);
		// echo $q;
		if ($temp->num_rows() == 0) {
			echo "Error no hay Cliente para este Beneficiario";
		} else {
			$arrBen = $temp->loadAssocList();
			// var_dump($arrBen);
			$idCTit = $arrBen[0]['idCTit'];
			$idBTit = $arrBen[0]['idBTit'];
		}
	}
	// echo "$idCTit - $idBTit";
	// if ($idCTit*1 > 0 && $idBTit * 1 > 0) {
	$data = array(
		'ClientId'				=> $idCTit,
		'BeneficiaryId'			=> $idBTit
	);
	// var_dump($data);
	echo datATitanes($data, 'G', 91);
	// }

}

if ($_REQUEST['donde'] == 1) {
	$correoMi .= "****Entra en la pasarela de Titanes\n<br>";
	$q = "select o.envia 'AmountToSend', o.recibe 'AmountToReceive', o.comision 'Charge', t.valor_inicial 'TotalAmount', c.idtitanes 'CustomerId', b.idtitanes 'BeneficiaryId', b.ciudad 'City', m.moneda 'CurrencyToSend', o.idrazon 'Reason' from tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t, tbl_moneda m where o.idbeneficiario = b.id and t.idtransaccion = o.idtransaccion and t.moneda = m.idmoneda and o.idcliente = c.id and (o.titOrdenId is null or o.titOrdenId = '') and o.idtransaccion = " . $_REQUEST['transaccion'];
	$correoMi .= $q . "\n<br>";
	$correoMi .= "cantRecords=" . $temp->num_rows() . "\n<br>";
	$temp->query($q);
	$data = array(
		'CustomerId'				=> $temp->f('CustomerId'),
		'BeneficiaryId'				=> $temp->f('BeneficiaryId'),
		'Country'					=> 'CU',
		'City'						=> $temp->f('City'),
		'DeliveryType'				=> '4',
		'AmountToSend'				=> number_format(($temp->f('AmountToSend') / 100), 2, ".", ""),
		'CurrencyToSend'			=> $temp->f('CurrencyToSend'),
		'AmountToReceive'			=> number_format(($temp->f('AmountToReceive') / 100), 2, ".", ""),
		'CurrencyToReceive'			=> 'CUC',
		'Charge'					=> number_format(($temp->f('Charge') / 100), 2, ".", ""),
		'TotalAmount'				=> number_format(($temp->f('TotalAmount') / 100), 2, ".", ""),
		'Correspondent'				=> 'T086',
		'SubCorrespondent'			=> '1',
		'Branch'					=> 'T0860001',
		'Reason'					=> $temp->f('Reason'),
		'BenefBankName'				=> '',
		'BenefBankCity'				=> '',
		'BenefBankAccountNumber'	=> '-1',
		'BenefBankAccountType'		=> '3',
		'BenefBankAccountAgency'	=> ''
	);
	$data = array_merge($data, array(
		"Signature"					=> $temp->f('CustomerId') . $temp->f('BeneficiaryId') . (number_format(($temp->f('AmountToReceive') / 100), 2, ".", "")) . 'CUC'
	));
	$tipo = 'O';
	$correoMi .= "sale=" . json_encode($data) . "<br>\n";
	$sale = datATitanes($data, $tipo, 91);
	$correoMi .= "sale=$sale<br>\n";
	echo $correoMi;
}


?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Insertar Clientes y beneficiarios en AIS</title>
	<script type="text/javascript" src="https://www.administracomercios.com/js/jquery.js"></script>
	<script type="text/javascript">
		function rellena() {
			var texto = $("#textm").val();
			texto = texto.replace(/\r?\n|\r/g, ';');
			console.log(texto);
			texto = texto.replace('DIR IP - 212.227.137.18Pasarela usada91', '');
			texto = texto.replace('falla por Apellido2', '');
			texto = texto.replace(/, enc=ASCII;/gi, ';');
			texto = texto.replace(/, enc=UTF-8;/gi, ';');
			texto = texto.replace(/, enc=ASCII/gi, ';');
			texto = texto.replace(/, enc=UTF-8/gi, ';');
			// texto = texto.replace(',',';');
			texto = texto.replace('á', 'a');
			texto = texto.replace('é', 'e');
			texto = texto.replace('í', 'i');
			texto = texto.replace('ó', 'o');
			texto = texto.replace('ú', 'u');
			texto = texto.replace('ü', 'u');
			texto = texto.replace('ñ', 'n');
			texto = texto.replace('Á', 'A');
			texto = texto.replace('É', 'E');
			texto = texto.replace('Í', 'I');
			texto = texto.replace('Ó', 'O');
			texto = texto.replace('Ú', 'U');
			texto = texto.replace('Ü', 'U');
			texto = texto.replace('Ñ', 'N');
			console.log(texto);

			var arrDat = texto.split(';');

			if ($("input[name='tipos']:checked").val() == 1) {

				$.each(arrDat, function(index, value) {
					if (value.indexOf("Province=") > -1) $("#cprovince").val(value.substring(value.indexOf("Province=") + 9));
					if (value.indexOf("Gender=") > -1) $("#cgender").val(value.substring(value.indexOf("Gender=") + 7));
					if (value.indexOf("PhoneNumber=") > -1) $("#cphoneNumber").val(value.substring(value.indexOf("PhoneNumber=") + 12));
					if (value.indexOf("UsuarioCode=") > -1) $("#cusuarioCode").val(value.substring(value.indexOf("UsuarioCode=") + 12));
					if (value.indexOf("PostalCode=") > -1) $("#cpostalCode").val(value.substring(value.indexOf("PostalCode=") + 11));
					if (value.indexOf("Id=") > -1) $("#IdC").val(value.substring(value.indexOf("Id=") + 3));
					if (value.indexOf("CountryOfBirth=") > -1) $("#ccountryOfBirth").val(value.substring(value.indexOf("CountryOfBirth=") + 15));
					if (value.indexOf("Profesion=") > -1) $("#cprofesion").val(value.substring(value.indexOf("Profesion=") + 10));
					if (value.indexOf("Email=") > -1) $("#cemail").val(value.substring(value.indexOf("Email=") + 6));
					if (value.indexOf("Apellido2=") > -1) $("#capellido2").val(value.substring(value.indexOf("Apellido2=") + 10));
					if (value.indexOf("Apellido1=") > -1) $("#capellido1").val(value.substring(value.indexOf("Apellido1=") + 10));
					if (value.indexOf("DocumentExpirationDate=") > -1) $("#cdocumentExpirationDate").val(value.substring(value.indexOf("DocumentExpirationDate=") + 23));
					if (value.indexOf("Country=") > -1) $("#ccountry").val(value.substring(value.indexOf("Country=") + 8));
					if (value.indexOf("DocumentNumber=") > -1) $("#cdocumentNumber").val(value.substring(value.indexOf("DocumentNumber=") + 15));
					if (value.indexOf("Address=") > -1) $("#caddress").val(value.substring(value.indexOf("Address=") + 8));
					if (value.indexOf("City=") > -1) $("#ccity").val(value.substring(value.indexOf("City=") + 5));
					if (value.indexOf("Nombre=") > -1) $("#cnombre").val(value.substring(value.indexOf("Nombre=") + 7));
					if (value.indexOf("DateOfBirth=") > -1) $("#cdateOfBirth").val(value.substring(value.indexOf("DateOfBirth=") + 12));
				});
				document.getElementById('fcli').submit();

			} else {

				$.each(arrDat, function(index, value) {
					if (value.indexOf("Phone=") > -1) $("#bphone").val(value.substring(value.indexOf("Phone=") + 6));
					if (value.indexOf("Id=") > -1) $("#IdB").val(value.substring(value.indexOf("Id=") + 3));
					if (value.indexOf("Relation=") > -1) $("#relation").val(value.substring(value.indexOf("Relation=") + 9));
					if (value.indexOf("Reason=") > -1) $("#reason").val(value.substring(value.indexOf("Reason=") + 7));
					if (value.indexOf("Apellido2=") > -1) $("#bapellido2").val(value.substring(value.indexOf("Apellido2=") + 10));
					if (value.indexOf("Apellido1=") > -1) $("#bapellido1").val(value.substring(value.indexOf("Apellido1=") + 10));
					if (value.indexOf("CI=") > -1) $("#CI").val(value.substring(value.indexOf("CI=") + 3));
					if (value.indexOf("Address=") > -1) $("#baddress").val(value.substring(value.indexOf("Address=") + 8));
					if (value.indexOf("IdCliente=") > -1) $("#idcliente").val(value.substring(value.indexOf("IdCliente=") + 10));
					if (value.indexOf("City=") > -1) $("#bcity").val(value.substring(value.indexOf("City=") + 5));
					if (value.indexOf("Nombre=") > -1) $("#bnombre").val(value.substring(value.indexOf("Nombre=") + 7));
				});
				document.getElementById('fben').submit();
			}
		};
	</script>
</head>

<body>
	<h3>Deshabilita/Habilita Cliente y sus Beneficiarios</h3>
	<form action="https://www.administracomercios.com/insclibenef.php" method="post" id="fcli">
		<!-- <form action="http://192.168.0.1/concentrador/insclibenef.php" method="post" id="fcli"> -->
		<label>Identificador en Titanes de la operación realizada: </label><input type="text" name="Iden" id="Iden" /><br>
		<input type="radio" name="desh" value="1" id="deshc" /><label for="deshc">Deshabilita</label><br>
		<input type="radio" name="desh" value="0" id="habc" checked /><label for="habc">Habilita</label><br>
		<input type="submit" value="Enviar" />
	</form>
	<h3>Actualiza la documentación del Remitente</h3>
	<form action="https://www.administracomercios.com/insclibenef.php" method="post" id="Usrf">
		<!-- <form action="http://192.168.0.1/concentrador/insclibenef.php" method="post" id="fcli"> -->
		<label>Usuario: </label><input type="text" name="Usr" id="Usr" /><br>
		<input type="submit" value="Enviar" />
	</form>

	<h3>Inscripción de Clientes</h3>
	<input type="radio" name="tipos" value="1" id="tipoc" /><label for="tipoc">Clientes</label><br>
	<input type="radio" name="tipos" value="0" id="tipob" checked /><label for="tipob">Beneficiarios</label><br><br>
	<textarea id="textm" cols="20"></textarea><br>
	<input type="button" id="env" value="Completar" onclick="rellena()" /><br><br>
	<form action="https://www.administracomercios.com/datInscr.php" method="post" id="fcli">
		<!-- <form action="http://localhost/concentrador/datInscr.php" method="post" id="fcli"> -->
		<label>Identificador en Cimex: </label><input type="text" name="Id" id="IdC" value="<?php echo $IdC; ?>" /> <input type="button" value="Cargar Datos" id="cargC" onClick="carga('C')" /><br>
		<label>Nombre: </label><input type="text" name="Nombre" id="cnombre" value="" /><br>
		<label>P. Apellido: </label><input type="text" name="Apellido1" id="capellido1" value="" /><br>
		<label>S. Apellido: </label><input type="text" name="Apellido2" id="capellido2" value="" /><br>
		<label>Pasaporte: </label><input type="text" name="DocumentNumber" id="cdocumentNumber" value="" /><br>
		<label>Fecha Exp pasaporte: </label><input type="text" name="DocumentExpirationDate" id="cdocumentExpirationDate" value="" /><br>
		<label>Correo: </label><input type="text" name="Email" id="cemail" value="" /><br>
		<label>Teléfono: </label><input type="text" name="PhoneNumber" id="cphoneNumber" value="" /><br>
		<label>País: </label><input type="text" name="Country" id="ccountry" value="" /><br>
		<label>Provincia: </label><input type="text" name="Province" id="cprovince" value="" /><br>
		<label>Ciudad: </label><input type="text" name="City" id="ccity" value="" /><br>
		<label>Dirección: </label><input type="text" name="Address" id="caddress" value="" /><br>
		<label>C.P.: </label><input type="text" name="PostalCode" id="cpostalCode" value="" maxlength="5" /><br>
		<label>País de Nac: </label><input type="text" name="CountryOfBirth" id="ccountryOfBirth" value="" /><br>
		<label>Fecha Nac: </label><input type="text" name="DateOfBirth" id="cdateOfBirth" value="" /><br>
		<label>Sexo: </label><input type="text" name="Gender" id="cgender" value="" /><br>
		<label>Profesion: </label><input type="text" name="Profesion" id="cprofesion" value="" /><br>
		<!--<label>Salario Mensual: </label><input type="text" name="MonthSalary" id="cmonthSalary" value="1000" /><br>-->
		<label>Código del usuario: </label><input type="text" name="UsuarioCode" id="cusuarioCode" value="" /><br>
		<label for="insert1">Insertar Cliente nuevo: <input type="checkbox" id="insert1" value="1" name="insert"> </label>
		<input type="hidden" name="pase" value="1" />
		<input type="submit" value="Enviar Cliente">
	</form><br><br><br>

	<h3>Inscripción de Beneficiarios</h3>
	<form action="https://www.administracomercios.com/datInscr.php" method="post" id="fben">
		<!-- <form action="http://localhost/concentrador/datInscr.php" method="post"> -->
		<label>Identificador en Cimex: </label><input type="text" name="Id" id="IdB" value="<?php echo $IdB; ?>" /> <input type="button" value="Cargar Datos" id="cargB" onClick="carga('B')" /><br>
		<label>Identificador del Cliente: </label><input type="text" name="IdCliente" id="idcliente" value="" /><br>
		<label>Nombre: </label><input type="text" name="Nombre" id="bnombre" value="" /><br>
		<label>P. Apellido: </label><input type="text" name="Apellido1" id="bapellido1" value="" /><br>
		<label>S. Apellido: </label><input type="text" name="Apellido2" id="bapellido2" value="" /><br>
		<label>Teléfono: </label><input type="text" name="Phone" id="bphone" value="" /><br>
		<label>Dirección: </label><input type="text" name="Address" id="baddress" value="" /><br>
		<label>Ciudad: </label><input type="text" name="City" id="bcity" value="" /><br>
		<label>C.I.: </label><input type="text" name="CI" id="CI" value="" maxlength="11" /><br>
		<label>Relación: </label><input type="text" name="Relation" id="relation" value="" /><br>
		<label>Razón: </label><input type="text" name="Reason" id="reason" value="" /><br>
		<label for="insert2">Insertar Beneficiario nuevo: <input type="checkbox" id="insert2" value="1" name="insert"> </label>
		<input type="hidden" name="pase" value="1" />
		<input type="submit" value="Enviar Beneficiario">
	</form><br><br><br>

	<!-- <h3>Pago</h3>
	<form action="https://www.administracomercios.com/insclibenef.php" method="post">
		<form action="http://localhost/concentrador/insclibenef.php" method="post">
		<input type="hidden" value="1" name="donde" />
		<label>Comercio: </label><input type="text" name="comercio" value="<?php echo $comercio; ?>" /><br>
		<label>Transacción: </label><input type="text" name="transaccion" value="<?php echo $Nombre; ?>" /><br>
		<label>Importe: </label><input type="text" name="importe" value="<?php echo $importe; ?>" /><br>
		<label>Moneda: </label><input type="text" name="moneda" value="<?php echo $moneda; ?>" /><br>
		<label>Operación: </label><input type="text" name="operacion" value="P" /><br>
		<label>Idioma: </label><input type="text" name="idioma" value="es" /><br>
		<label>Cliente: </label><input type="text" name="IdCustomer" value="" /><br>
		<label>Beneficiario: </label><input type="text" name="IdBeneficiary" value="" /><br>
		<label>Cantidad a enviar: </label><input type="text" name="AmountToSend" value="" /><br>
		<label>Cantidad a recibir: </label><input type="text" name="AmountToReceive" value="" /><br>
		<label>Cargo: </label><input type="text" name="Charge" value="12" /><br>
		<label>Razón: </label><input type="text" name="Reason" value="3" /><br>
		<label>Pasarela: </label><input type="text" name="pasarela" value="91" /><br>
		<label>Firma: </label><input type="text" name="firma" value="<?php echo $firma; ?>" /><br>
		<input type="submit" value="Enviar Pago">
	</form><br><br><br>

	<h3>Pago</h3>
	 <form action="https://www.administracomercios.com/insclibenef.php" method="post"> 
	<form action="http://localhost/concentrador/insclibenef.php" method="post">
		<input type="hidden" value="2" name="donde" />
		<label>Cliente idFincimex: </label><input type="text" name="IdCustomer" value="<?php echo $_REQUEST['IdCustomer']; ?>" /><br>
		<label>Beneficiario idFincimex: </label><input type="text" name="IdBeneficiary" value="<?php echo $_REQUEST['IdBeneficiary']; ?>" /><br>
		<input type="submit" value="Enviar Pago">
	</form><br><br><br> -->

	<h3>Datos de operación</h3>
	<form action="https://www.administracomercios.com/datInscr.php" method="post">
		<!-- <form action="http://localhost/concentrador/datInscr.php" method="post"> -->
		<label>Identificador de la operación (titanes, tocopay o concentrador): </label><input type="text" name="melaa" id="melaa" value="" /> 
		<input type="button" value="Cargar Datos" id="mela" onClick="carga('D')" />
	</form><br><br><br>
	<div id="erroroper"></div>
	<div id="opertabla">
	<table id="mdatos">
		<tr>
			<th>Usuario</th>
			<th>Email</th>
			<th>Acumulado<br>Usuario</th>
			<th>Causa</th>
			<th>Operación detenida</th>
			<th>Monto</th>
			<th>Fecha</th>
			<th>Beneficiario</th>
			<th>Acumulado<br>Beneficiario</th>
		</tr>
		<tr>
			<td id="Usuario"></td>
			<td id="Email"></td>
			<td id="Acumcliente"></td>
			<td id="Causa">&nbsp;</td>
			<td id="detenida"></td>
			<td id="Monto"></td>
			<td id="Fecha"></td>
			<td id="Beneficiario"></td>
			<td id="Acumbene"></td>
		</tr>
	</table></div><br><br><br>


	<h3>Cambio de datos para Clientes iguales</h3>
	<form action="https://www.administracomercios.com/ejecu.php" method="post">
		<input type="hidden" name="fun" value="repet" />
		Id antiguo del Cliente: <input type="text" name="viejo"><br><br>
		Id nuevo del Cliente: <input type="text" name="nuevo"><br><br>
		<input type="submit" value="Envia" />
	</form>
</body>
<script lang="text/javascript">

	function carga(tipo) {
		if (tipo == 'C') {
			$.post('ejecu.php', {
				fun: 'cargInsc',
				cli: $("#IdC").val()
			}, function(data) {
				var datos = eval('(' + data + ')');
				$("#cnombre").val(datos[0].nombre);
				$("#capellido1").val(datos[0].papellido);
				$("#capellido2").val(datos[0].sapellido);
				$("#cusuarioCode").val(datos[0].usuario);
				$("#cmonthSalary").val(datos[0].salariomensual);
				$("#cprofesion").val(datos[0].ocupacion);
				$("#cgender").val(datos[0].sexo);
				$("#cdateOfBirth").val(datos[0].fnac);
				$("#ccountryOfBirth").val(datos[0].pn);
				$("#cpostalCode").val(datos[0].CP);
				$("#caddress").val(datos[0].direccion);
				$("#ccity").val(datos[0].ciudad);
				$("#cprovince").val(datos[0].provincia);
				$("#ccountry").val(datos[0].pr);
				$("#cphoneNumber").val(datos[0].telf1);
				$("#cemail").val(datos[0].correo);
				$("#cdocumentExpirationDate").val(datos[0].fdoc);
				$("#cdocumentNumber").val(datos[0].numDocumento);
			});
		} else if(tipo == 'D') {
			$("#opertabla").hide();
			$("#Usuario").text('');
			$("#Email").text('');
			$("#detenida").text('');
			$("#Monto").text('');
			$("#Fecha").text('');
			$("#Beneficiario").text('');
			$("#erroroper").text('Espera....').show();
			$.post('ejecu.php', {
				fun: 'cargDatos',
				ben: $("#melaa").val()
			}, function(data) {
				var datos = eval('(' + data + ')');
				$("#erroroper").text('').hide();
				if (datos[0].error.length > 1) {
					$("#opertabla").hide();
					$("#erroroper").text(datos[0].error).show();
				} else {
					$("#opertabla").show();
					$("#erroroper").hide();
					$("#Usuario").text(datos[0].cli);
					$("#Email").text(datos[0].correo);
					$("#detenida").text(datos[0].oper);
					$("#Monto").text(datos[0].val);
					$("#Fecha").text(datos[0].fec);
					$("#Beneficiario").text(datos[0].bene);
					$("#Acumcliente").text(datos[0].acumCliente);
					$("#Acumbene").text(datos[0].acumBen);
				}
			});
		} else if(tipo == 'B') {
			$.post('ejecu.php', {
				fun: 'cargInsc',
				ben: $("#IdB").val()
			}, function(data) {
				var datos = eval('(' + data + ')');
				$("#bnombre").val(datos[0].nombre);
				$("#bapellido1").val(datos[0].papellido);
				$("#bapellido2").val(datos[0].sapellido);
				$("#bphone").val(datos[0].telf);
				$("#baddress").val(datos[0].direccion);
				$("#bcity").val(datos[0].ciudad);
				$("#CI").val(datos[0].numDocumento);
				$("#razon").val(datos[0].razon);
				$("#relation").val(datos[0].relac);
				$("#reason").val(datos[0].idrazon);
				$("#idcliente").val(datos[0].cliente);
			});
		}
	}
</script>

<style>
body{
	font-family: sans-serif;
	font-size: 12px;
}
#mdatos tr th,td{
	width:150px;
	text-align: center;
}

#opertabla, #erroroper {
	display:none;
}

#erroroper {
	color:red;
}
</style>

</html>