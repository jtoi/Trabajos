<?php

ini_set('display_errors', 0);
error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");
	
define('_VALID_ENTRADA', 1);
//ini_set("display_errors", 1);
//error_reporting(E_ALL & ~E_NOTICE);
if (!session_start())
	session_start();
require_once( 'admin/classes/entrada.php' );
//require_once( '../include/sendmail.php' );
require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/hoteles.func.php' );
require_once( 'include/correo.php' );
require_once( 'admin/adminis.func.php' );
require_once( 'include/class.inicio.php' );
require_once( 'include/apiRedsys.php' );

$miObj = new RedsysAPI;
$inicio = new inico();
$temp = new ps_DB;
$correo = new correo;
$ent = new entrada;
$correoMi = "fecha=".date('d/m/Y H:i:s')."<br>";
$titulo = 'Entrada de datos';
header('Content-Type: text/html; charset=utf-8');
$correoMi .= "referer=".$_SERVER['HTTP_REFERER']."<br>";

$d = $_POST;
//$d = $_REQUEST;

/****************************comentar**************************************************/
if (stripos(_ESTA_URL, 'localhost') > 0) {
	
// 	date_default_timezone_set('Europe/Berlin');
// 	$d['comercio']='135334103888'; //H Nacional
// 	$d['transaccion']=substr(time(), -8);
// 	$d['importe']='42000';
// 	$d['moneda']='152';
// 	$d['operacion']='P';
// 	$d['idioma']='en';
// 	$d['pasarela']='52';
//	/* Hace que se procesen pagos al momento */
//	$query = "insert into tbl_reserva (id_admin, id_comercio, est_comer, codigo, nombre, email, servicio, valor_inicial, moneda, fecha, pMomento, idioma, pasarela, tiempoV, url, amex) values (10, '{$d['comercio']}', 'P', '{$d['transaccion']}', 'jtoirac', 'jtoirac@gmail.com', 'serv', '" . str_replace ( ",", ".", $d['importe'] ) . "', '{$d['moneda']}', unix_timestamp(), 'S', 'es', {$d['pasarela']}, 3, 'admin.administracomercios.com', 2)";
//	$temp->query ( $query );
//  	$d['amex']='1';
//  	$d['email']='jtoirac@mailinator.com';
// 	$d['usuario'] = '';
// 	$d['IdCustomer']='711';
// 	$d['IdBeneficiary']='1109';
// 	$d['AmountToSend']='9629';
// 	$d['AmountToReceive']='10000';
// 	$d['Charge']='1150';
// 	$d['Reason']='1';
// 	$q = "select palabra from tbl_comercio where idcomercio = '{$d['comercio']}'";
// 	$temp->query($q);
// 	$d['clave']	= $temp->f('palabra');
// 	$d['firma']	= md5($d['comercio'].$d['transaccion'].$d['importe'].$d['moneda'].$d['operacion'].$d['clave']);
// 	$d['firma'] = 'dd1de16c27f26dd6ff0aedd26e77e5c6';
	
}
/****************************comentar*************************************************/
foreach ($d as $value => $item) {
	$entrega .= $value . "=" . $item . "<br>\n";
	error_log("entrada de datos ".$value . "= " . $item);
}

foreach ($_SERVER as $value => $item) {
	if (
				$value == 'HTTP_USER_AGENT' 
			|| 	$value == 'HTTP_ACCEPT_LANGUAGE' 
			|| 	$value == 'HTTP_REFERER'
			)
	$entrega .= $value . "=" . $item . "<br>\n";
}

//print_r($d);
if (isset($d['client_ip_address']) && $d['client_ip_address'] != '') $dirIp = $d['client_ip_address'];
else $dirIp = $_SERVER['REMOTE_ADDR'];
$correoMi .= "DIR IP - ".$dirIp . "<br>\n";
$correoMi .= $entrega . "<br>\n";
if (_MOS_CONFIG_DEBUG) echo $correoMi."<br><br>";
$inicio->ip = $dirIp;
if(isset($d['tpv'])) $inicio->tpv = $d['tpv'];
//echo $inicio->ip;
//echo "ve";
//$dirIp = '199.180.128.45';
//echo "va";
//ipBloqueada($dirIp);
if ($d['TotalAmount']) $d['importe'] = $d['TotalAmount'];
// if ($d['CurrencyToSend']) $d['moneda'] = $d['CurrencyToSend'];
if ($d['moneda']) $d['CurrencyToSend'] = $d['moneda'];

if ($d['comercio'] && $d['transaccion'] && $d['importe'] && $d['moneda'] && $d['operacion'] && $d['firma']) {
    $correo->set_subject($titulo);
	//if ($d['importe'] > 500000) muestraError ("falla por importe excedido", $correoMi);
	
	if (!($inicio->comer = $ent->isAlfanumerico($d['comercio'], 15))) {
		muestraError ("falla por comercio", $correoMi);
	}
	
	if ($inicio->comer == '527341458854'
			|| $inicio->comer == '144172448713'
// 			|| $inicio->comer == '122327460662'
			) {
		if ($d['IdCustomer'] && $d['AmountToReceive'] && $d['Charge'] && $d['AmountToSend'] && $d['IdBeneficiary'] && $d['Reason']  ){
			if ($d['NumCuenta'] == '') $numcta = '-1'; else $numcta = $d['NumCuenta'];
			$inicio->datAis = array(
					'idremitente' => $d['IdCustomer'],
					'iddestin' => $d['IdBeneficiary'],
					'importerecive' => $d['AmountToReceive'],
					'comision' => $d['Charge'],
					'importenvia' => $d['AmountToSend'],
					'rason' => $d['Reason'],
					'numcta' => $numcta
			);
		} else  {
			muestraError ("falla por datos insuficientes en AIS", $correoMi);
		}
	}
	
	if (!($inicio->tran = $ent->isUrl($d['transaccion'], 19))) {
		muestraError ("falla por transaccion", $correoMi);
	}
	if (!($inicio->imp = $ent->isReal($d['importe'], 9)) || $d['importe'] == 0) {
		muestraError ("falla por importe", $correoMi);
	}
	if (!($inicio->mon = $ent->isReal($d['moneda'], 3))) {
		muestraError ("falla por moneda", $correoMi);
	} else {
		$q = "select count(idmoneda) total from tbl_moneda where idmoneda = '" . $d['moneda'] . "'";
		$temp->query($q);
		if ($temp->f('total') != 1) {
			muestraError ("falla por moneda", $correoMi);
		}
	}
	if (isset($d['amex']) && $d['amex'] > 0) $inicio->amex = $d['amex'];
	else $inicio->amex = '2';
	
	$inicio->opr = strtoupper($d['operacion']);
	if (!$inicio->opr == 'P' || !$inicio->opr == 'C') {
		muestraError ("falla por operacion", $correoMi);
	}
	if (!($inicio->frma = $ent->isAlfanumerico($d['firma'], 32))) {
		muestraError ("falla por firma", $correoMi);
	}
	if ($d['pasarela']) {
		if (!($inicio->pasa = $ent->isEntero($d['pasarela'], 2))) {
			muestraError ("falla por tipo de pasarela", $correoMi);
		}
	}
	if ($d['idioma']) {
		$inicio->idi = strtolower($d['idioma']);
		if (!$inicio->idi == 'es' || !$inicio->idi == 'en' || !$inicio->idi == 'it') {
			muestraError ("falla por idioma", $correoMi);
		}
	} else {$inicio->idi = 'es';}

//	chequeo que el comercio esté activo
	if (!$inicio->verComer()) muestraError ($inicio->err, $correoMi.$inicio->log);
	$correoMi .= $inicio->log;
	
	//paguelofacil
	if ($d['email']) {
		if (!($inicio->email = $ent->isCorreo($d['email'],100))) muestraError ("falla por email", $correoMi);
		if (!$inicio->verfEmail()) muestraError ($inicio->err, $correoMi.$inicio->log);
		$correoMi .= $inicio->log;
	}
	if ($d['nomb']) if (!($inicio->nomb = $ent->isAlfabeto($d['nomb'],40))) muestraError ("falla por nombre", $correoMi);
	if ($d['apell']) if (!($inicio->apell = $ent->isAlfabeto($d['apell'],50))) muestraError ("falla por apellido", $correoMi);
	if ($d['secur']) if (!($inicio->secur = $ent->isEntero($d['secur'],3))) muestraError ("falla por c&oacute;digo de seguridad CVV2 incorrecto", $correoMi);
	if ($d['ano']) if (!($inicio->ano = $ent->isEntero($d['ano'],4))) muestraError ("falla por a&ntilde;o de vencimiento de la tarjeta incorrecto", $correoMi);
	if ($d['mes']) if (!($inicio->mes = $ent->isEntero($d['mes'],2))) muestraError ("falla por mes de vencimiento de la tarjeta incorrecto", $correoMi);
	if ($d['tel']) if (!($inicio->telf = $ent->isAlfanumerico($d['tel'],12))) muestraError ("falla por tel&eacute;fono incorrecto", $correoMi);
	if ($d['dir']) if (!($inicio->direcc = $ent->isAlfanumerico($d['dir'],150))) muestraError ("falla por direcci&oacute;n incorrecta", $correoMi);
	if ($d['tarj']) {
		if (!($inicio->tarj = $ent->isEntero($d['tarj'],16))) {
			muestraError ("falla por tarjeta incorrecta", $correoMi);
		} else {
			if (!$inicio->luhn($d['tarj'])) muestraError("falla por número de tarjeta incorrecto", $correoMi);
		}
	}
	//paytpv
	if ($d['usuario']) {
		if(!($usuario = $ent->isUrl($d['usuario'],100))) muestraError ("falla por datos de usuario incorrectos", $correoMi);
		$inicio->verfTkusr($usuario);
	}
	
//	Verifica que la transaccion no se repita
	if (!$inicio->verTran()) {
		if (count($inicio->arrCli)) {
			$correo->to($inicio->arrUsu[1]);
			$correo->todo(57,'Aviso de Transacción duplicada','Estimado(a) '.$inicio->arrUsu[0].'<br><br>
				El usuario <b>'.$inicio->arrCli[0].'</b> con correo <a href="mailto:'. $inicio->arrCli[1] .'">'. $inicio->arrCli[1] .'</a> 
				est&aacute; intentando volver a pagar sobre la invitaci&oacute;n de pago ya vencida de la operaci&oacute;n con referencia de comercio <b>'.
				$inicio->tran.'.</b> y número de transacción <b>'. $inicio->arrCli[2].'</b>. <br><br>
				Por favor comun&iacute;quese con &eacute;l y env&iacute;ele una nueva Invitaci&oacute;n de Pago. 
				Para esto &uacute;ltimo, puede acceder a la operaci&oacute;n a trav&eacute;s de la opci&oacute;n del men&uacute; REPORTES / Clientes<br><br>
				Administrador de comercios');
			$correoMi .= "Se envían el correo de transacción duplicada\n<br>";
		}
		muestraError($inicio->err, $correoMi.$inicio->log);
	}
	$correoMi .= $inicio->log;

//	chequeo de firma
	if (!$inicio->verFir()) {muestraError ($inicio->err, $correoMi-$inicio->log);}
	$correoMi .= $inicio->log;
	
//	chequeo que la direccion IP no esté bloqueada
	if (!$inicio->verIP()) {muestraError($inicio->err, $correoMi.$inicio->log);}
	$correoMi .= $inicio->log;

//	chequeo por monto de la transaccion
	$inicio->alerSegur();
	$correoMi .= $inicio->log;

//	chequeo la pasarela
	if (!$inicio->cheqPas()) muestraError ($inicio->err, $correoMi.$inicio->log);
	$correoMi .= $inicio->log;
	
	if ($inicio->pasa != 57) {
	echo '<div id="bannn" style="" >
			<img scr="'._ESTA_URL.'/images/Banners-visa-master.png" /></div><script>
			 var ancho = "background-image:url(\''._ESTA_URL.'/images/Banners-visa-master.png\'); width:400px; height:62px; margin-left:"+
			 	((window.innerWidth)-400)/2+"px; border: 1px solid #000;border-bottom: 0;margin-top: 40px;";
				document.getElementById("bannn").setAttribute("style",ancho);
				document.writeln("<div id=\"avisoIn\" style=\"margin:0 0 0 "+
			  ((window.innerWidth)-400)/2
			  +"px; width:400px; text-align:center;\">"
			  )</script>
			  <strong>Este comercio admite Comercio Electr&oacute;nico Seguro (CES).</strong><br /><br />
			  El banco de su tarjeta requiere que se registre para poder comprar en Internet.
			  En breves momentos le pondremos en contacto con su banco para que autorice el pago.<br /><br />
			  <span style="color:red;font-weight:bold;">Importante:</span>
			  La siguiente p&aacute;gina est&aacute; fuera del control de este establecimiento y es responsabilidad de su Banco,
			  por lo que, si tiene alg&uacute;n problema, por favor dirija sus 
			  reclamaciones al Banco emisor de la tarjeta.<br /><br />
			  Algunos bancos no permiten realizar compras en Internet a sus clientes.
			  Por favor, si tiene problemas con su tarjeta intente de nuevo la compra con otra tarjeta.<br /><br />
			  ';
	if ($inicio->pasa != 1)
		echo 'El proceso de compra se va a completar a trav&eacute;s de la pasarela de Comercio Electr&oacute;nico Seguro (CES),
				garantiz&aacute;ndole la m&aacute;xima seguridad en su proceso de compra. Para completar su 
				compra, ser&aacute; necesario que su tarjeta est&eacute; registrada para operar en Comercio
				Electr&oacute;nico Seguro; en caso de duda consulte con su entidad financiera.<br /><br />';
	echo '<strong>Su transacci&oacute;n est&aacute; siendo procesada...</strong><br><br>';
	echo '<strong>This trade supports Secure Electronic Commerce Gateway (CES).</strong><br /><br />
				The bank card registration is required to purchase online. 
				In a moment we will contact your bank to authorize the payment.<br /><br />
				<span style="color:red;font-weight:bold;">Important: </span>The following site is outside the control
				of this property and responsibility of your bank, so if you have any problems, please direct your 
				complaints to the card issuing bank. / La seguente pagina è fuori dal nostro controllo e fa parte dei sistemi di pagamento online attivati dalla sua banca. Se ha dei problemi per favore si diriga presso la sua filiale.<br /><br />
				Some banks do not allow online purchases to its customers.
				Please, if you have problems with your card purchase try again with another card.<br><br>';
	if ($inicio->pasa != 1)
		echo 'The purchase process will be completed through the Secure Electronic Commerce Gateway (CES),
				guaranteeing maximum safety in your buying process.
				To complete your purchase, 
				you will need your card is registered to operate in Secure Electronic Commerce,
				in case of doubt check with your financial institution.<br><br>';
	echo '<strong>Your transacction is been processed...</strong></div>';
	} else {

		echo '<script>document.writeln("<div id=\"avisoIn\" style=\"margin:0 0 0 "+
			  ((window.innerWidth)-400)/2
			  +"px; width:400px; text-align:center;\">"
			  )</script><strong>Su transacci&oacute;n est&aacute; siendo procesada...</strong><br><br><strong>Your transacction is been processed...</strong></div>';
	}
	$correoMi .= "Ejecuta la transacción\n<br>";
//	Ejecuta la transacción
	$sale = $inicio->operacion();
	if (!$sale) muestraError ($inicio->err, $correoMi.$inicio->log);
	else {
		$correoMi .= $inicio->log;
		if ($inicio->pasa != 57) echo $sale;
		else {
			$dat = json_decode($sale);
			$options = array(
					CURLOPT_RETURNTRANSFER	=> true,
					CURLOPT_SSL_VERIFYPEER	=> false,
					CURLOPT_POST			=> true,
					CURLOPT_VERBOSE			=> true,
					CURLOPT_URL				=> _ESTA_URL."/rep/llegada.php",
					CURLOPT_POSTFIELDS		=> $sale,
					CURLOPT_SSL_VERIFYHOST	=> false
			);
			
			$ch = curl_init();
			curl_setopt_array($ch , $options);
			$salida = curl_exec($ch);
			echo "salida="._ESTA_URL."/rep/llegada.php".$salida;
			
			if (curl_errno($ch)) echo "Error en la resp de Sipay:".curl_strerror(curl_errno($ch))."<br>\n";
			$crlerror = curl_error($ch);
			
			if ($crlerror) echo "Error en la resp de Sipay:".$crlerror."<br>\n";
			$curl_info = curl_getinfo($ch);
			curl_close($ch);
			
			$arrCurl = json_decode($salida);
			print_r($curl_info);echo "<br><br>";
		}
	}
	
} else {
	$correoMi .= "invalid";
	echo "<!-- invalid -->";
	$ref = $_SERVER['HTTP_REFERER'];
	$query = "insert into tbl_listaIp (ip, fecha, refer) values ('$dirIp', " . time() . ", '$ref')";
	$correoMi .= "<br>comercio=" . $d['comercio'] . " && transaccion=" . $d['transaccion'] . " && importe=" . 
					$d['importe'] . " && moneda=" . $d['moneda'] . " && operaci&oacute;n=" .
					$d['operacion'] . " && firma=" . $d['firma'];
	$temp->query($query);
}

//correoAMi($titulo, $correoMi);
$correo->set_subject($titulo);
$correo->set_message($correoMi);
$correo->envia(9);
//echo $correoMi;

// Debugger
if (_MOS_CONFIG_DEBUG) {
	
	echo '<div id="debD"><pre style="font-size:11px">';
	echo "<hr /><hr /><br>Logs:<br>";
	echo $correoMi;
	echo "<hr /><hr /><br>Querys:<br>";
	echo $temp->log;
	echo "<hr /><hr /><br>Datos:<br>";
	echo "<hr /><hr /><br>Variables usadas:<br>";
	print_r(array_keys(get_defined_vars()));
	echo $textoCorreo;
	echo "</div>";
}

function muestraError ($etiqueta, $textoCorreo) {

	$correo = new correo();
	$textoCorreo .= $etiqueta;
	$correo->set_subject('Entrada de datos');
	$correo->set_message($textoCorreo);
	$correo->envia(9);
	echo '<script>document.getElementById("avisoIn").style.display="none";document.writeln("<div id=\"errDvd\" style=\"margin:20px 0 0 "+
((window.innerWidth)-800)/2
+"px; width:800px; text-align:center;\">")</script>
Se ha producido un <span style="color:red;font-weight:bold;">ERROR</span>
en los datos enviados:<br /><h3>'.$etiqueta.'</h3>por favor consulte a su comercio.<br /><br />
<img src="images/pagina_error.png" width="247" height="204" alt="Error" title="Error" /><br /><br /></div>
<!-- '.$etiqueta.' -->';
	exit;
}
?>
