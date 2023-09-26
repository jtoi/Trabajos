<?php
ini_set('display_errors', 0);
error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('_VALID_ENTRADA', 1);
require_once('configuration.php');
include 'include/mysqli.php';
require_once('admin/adminis.func.php');
require_once('include/hoteles.func.php');
require_once('include/correo.php');
$temp = new ps_DB;
$correo = new correo;

$d = $_REQUEST;

if ($d['func'] == 'firma') {
	$Calc = convierte256($d['comercio'], $d['transaccion'], $d['importe'], $d['moneda'], $d['operacion']);
	echo json_encode(array("md5" => $Calc));
}

if ($d['func'] == 'curl') {
	// if ($d['firma']) {
	$url = "https://www.administracomercios.com/";
	$data = array(
		"comercio"		=> $d['comercio'],
		"transaccion"	=> $d['transaccion'],
		"importe"		=> $d['importe'],
		"moneda"		=> $d['moneda'],
		"operacion"		=> $d['operacion'],
		"codigo"		=> $d['codigo'],
		"firma"			=> $d['firma']
	);
	$options = array(
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_SSL_VERIFYPEER	=> false,
		CURLOPT_POST			=> true,
		CURLOPT_VERBOSE			=> true,
		CURLOPT_URL				=> $url,
		CURLOPT_POSTFIELDS		=> $data
	);
	$correoMi .= "<br><br>
		CURLOPT_RETURNTRANSFER	=> true,<br>
		CURLOPT_SSL_VERIFYPEER	=> false,<br>
		CURLOPT_POST			=> true,<br>
		CURLOPT_VERBOSE			=> true,<br>
		CURLOPT_URL				=> $url,
		CURLOPT_POSTFIELDS		=> " . http_build_query($data) . "<br>";
	$ch = curl_init();
	curl_setopt_array($ch, $options);
	$output = curl_exec($ch);
	echo $output;
}
if (!$d['func']) {
?>
	<form name="envia" action="https://www.administracomercios.com/" method="post">
		Comercio:
		<input type='text' name='comercio' id='comercio' value='122327460662' /><br>
		Transaccion:
		<input type='text' name='transaccion' id='transaccion' value='2102260922' /><br>
		Importe:
		<input type='text' name='importe' id='importe' value='300' /><br>
		Moneda:
		<input type='text' name='moneda' id='moneda' value='978' /><br>
		Operacion:
		<input type='text' name='operacion' id='operacion' value='P' /><br>
		Firma:
		<input type='text' name='firma' id='firma' value='' /><br>
		Enviar por curl?:
		<input type='radio' id='curls' name='curl' value='1' /> Si&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' id='curln' name='curl' value='0' checked='true' /> No<br><br>
		<input type='button' value='Envia' onClick='evalua();' /> </form><br><br><br><br>
	A BIPAY:<br>
	<form name="" action="http://192.168.0.124/views/bipay_form.php" method="post">
		DS_MERCHANT_MERCHANTCODE:
		<input type='text' name='DS_MERCHANT_MERCHANTCODE' id='DS_MERCHANT_MERCHANTCODE' value='5324536' /><br>
		DS_MERCHANT_ORDER:
		<input type='text' name='DS_MERCHANT_ORDER' id='DS_MERCHANT_ORDER' value='' /><br>
		DS_MERCHANT_AMOUNT:
		<input type='text' name='DS_MERCHANT_AMOUNT' id='DS_MERCHANT_AMOUNT' value='300' /><br>
		DS_MERCHANT_CURRENCY:
		<input type='text' name='DS_MERCHANT_CURRENCY' id='DS_MERCHANT_CURRENCY' value='978' /><br>
		DS_MERCHANT_LANG:
		<input type='text' name='DS_MERCHANT_LANG' id='DS_MERCHANT_LANG' value='es' /><br>
		DS_MERCHANT_TRANSACTIONTYPE:
		<input type='text' name='DS_MERCHANT_TRANSACTIONTYPE' id='DS_MERCHANT_TRANSACTIONTYPE' value='0' /><br>
		DS_MERCHANT_SIGNATURE:
		<input type='text' name='DS_MERCHANT_SIGNATURE' id='DS_MERCHANT_SIGNATURE' value='' /><br>
		DS_MERCHANT_URL:
		<input type='text' name='DS_MERCHANT_URL' id='DS_MERCHANT_URL' value='https://www.administracomercios.com/rep/llegada.php' /><br>
		DS_MERCHANT_URLOK:
		<input type='text' name='DS_MERCHANT_URLOK' id='DS_MERCHANT_URLOK' value='https://www.administracomercios.com/rep/index.php?resp=210311170288&est=ok' /><br>
		DS_MERCHANT_URLKO:
		<input type='text' name='DS_MERCHANT_URLKO' id='DS_MERCHANT_URLKO' value='https://www.administracomercios.com/rep/index.php?resp=210311170288&est=ko' /><br>
		DS_SECURE_PAYMENT:
		<input type='text' name='DS_SECURE_PAYMENT' id='DS_SECURE_PAYMENT' value='0' /><br>
		<br><input type='submit' value='Envia' /> </form>

	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript">
		function evalua() {
			$.post("formini.php", {
					comercio: $("#comercio").val(),
					transaccion: $("#transaccion").val(),
					importe: $("#importe").val(),
					moneda: $("#moneda").val(),
					operacion: $("#operacion").val(),
					func: 'firma'
				})
				.done(function(data) {
					var datos = eval('(' + data + ')');
					$("#firma").val(datos.md5);
					envias();
				});
		}

		function envia() {
			document.forms[0].submit();
		}

		function envias() {
			if ($("input[name=curl]:checked").val() == 1) {
				$.post("formini.php", {
						comercio: $("#comercio").val(),
						transaccion: $("#transaccion").val(),
						importe: $("#importe").val(),
						moneda: $("#moneda").val(),
						operacion: $("#operacion").val(),
						firma: $("#firma").val(),
						func: 'curl'
					})
					.done(function(data) {
						var datos = eval('(' + data + ')');
						alert('Resultado= ' + datos.result + '  Motivo: ' + datos.comen)
					});
			} else {
				document.forms[0].submit();
			}
		}
	</script>
<?php } ?>