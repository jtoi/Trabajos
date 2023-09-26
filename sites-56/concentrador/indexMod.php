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
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'include/correo.php' );
require_once( 'admin/adminis.func.php' );
require_once( 'include/class.inicio.php' );

$inicio = new inico();
$temp = new ps_DB;
$correo = new correo;
$ent = new entrada;
$correoMi = "fecha=".date('d/m/Y H:i:s')."<br>";
$titulo = 'Entrada de datos';
header('Content-Type: text/html; charset=utf-8');

$d = $_POST;
//$d = $_REQUEST;

/****************************comentar**************************************************/
//$PPTcomercio	= "122327460662";
//$PPTtransaccion = substr(time(), -8);
//$PPTimporte		= "1235";
//$PPTmoneda		= "978";
//$PPToperacion	= "P";
//$PPTidioma		= "es";
//$PPTclave		= "FjswqLm6rNu3F27nGrcM";
//$PPTfirma		= md5($PPTcomercio.$PPTtransaccion.$PPTimporte.$PPTmoneda.$PPToperacion.$PPTclave);
//$d = array("comercio"=>$PPTcomercio, "transaccion"=>$PPTtransaccion, "importe"=>$PPTimporte, "moneda"=>$PPTmoneda, "operacion"=>$PPToperacion, "idioma"=>$PPTidioma, "firma"=>$PPTfirma, "pasarela"=>"32");
/****************************comentar*************************************************/
foreach ($d as $value => $item) {
	$entrega .= $value . "=" . $item . "\n";
}

//print_r($d);

$dirIp = $_SERVER['REMOTE_ADDR'];
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

if ($d['comercio'] && $d['transaccion'] && $d['importe'] && $d['moneda'] && $d['operacion'] && $d['firma']) {
    $correo->set_subject($titulo);
	if (!($inicio->comer = $ent->isAlfanumerico($d['comercio'], 15))) {
		muestraError ("falla por comercio", $correoMi);
	}
	if (!($inicio->tran = $ent->isUrl($d['transaccion'], 12))) {
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
	
	$inicio->opr = strtoupper($d['operacion']);
	if (!$inicio->opr == 'P' || !$inicio->opr == 'C') {
		muestraError ("falla por operacion", $correoMi);
	}
	if (!($inicio->frma = $ent->isAlfanumerico($d['firma'], 32))) {
		muestraError ("falla por firma", $correoMi);
	}
	if ($d['pasarela']) {
		if (!($inicio->pasa = $ent->isReal($d['pasarela'], 2))) {
			muestraError ("falla por tipo de pasarela", $correoMi);
		}
	}
	if ($d['idioma']) {
		$inicio->idi = strtolower($d['idioma']);
		if (!$inicio->idi == 'es' || !$inicio->idi == 'en') {
			muestraError ("falla por idioma", $correoMi);
		}
	} else {$inicio->idi = 'es';}

	//chequeo que el comercio esté activo
	if (!$inicio->verComer()) muestraError ($inicio->err, $correoMi.$inicio->log);
	$correoMi .= $inicio->log;
	
	//Verifica que la transaccion no se repita
	if (!$inicio->verTran()) {muestraError($inicio->err, $correoMi.$inicio->log);}
	$correoMi .= $inicio->log;

//	chequeo de firma
	if (!$inicio->verFir()) {muestraError ($inicio->err, $correoMi-$inicio->log);}
	$correoMi .= $inicio->log;
	
//	chequeo que la direccion IP no esté bloqueada
	if (!$inicio->verIP()) {muestraError($inicio->err, $correoMi.$inicio->log);}
	$correoMi .= $inicio->log;


	//chequeo por monto de la transaccion
	$inicio->alerSegur();
	$correoMi .= $inicio->log;

	//chequeo la pasarela
	if (!$inicio->cheqPas()) muestraError ($inicio->err, $correoMi.$inicio->log);
	$correoMi .= $inicio->log;
	

	echo '<script>document.writeln("<div id=\"avisoIn\" style=\"margin:20px 0 0 "+
			  ((window.innerWidth)-800)/2
			  +"px; width:800px; text-align:center;\">"
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
				complaints to the card issuing bank.<br /><br />
				Some banks do not allow online purchases to its customers.
				Please, if you have problems with your card purchase try again with another card.<br><br>';
	if ($inicio->pasa != 1)
		echo 'The purchase process will be completed through the Secure Electronic Commerce Gateway (CES),
				guaranteeing maximum safety in your buying process.
				To complete your purchase, 
				you will need your card is registered to operate in Secure Electronic Commerce,
				in case of doubt check with your financial institution.<br><br>';
	echo '<strong>Your transacction is been processed...</strong></div>';

	//Ejecuta la transacción
	$sale = $inicio->operacion();
	if (!$sale) muestraError ($inicio->err, $correoMi.$inicio->log);
	else echo $sale;
	$correoMi .= $inicio->log;
	
} else {
	$correoMi .= "invalid";
	echo "<!-- invalid -->";
	$ref = $_SERVER['HTTP_REFERER'];
	$query = "insert into tbl_listaIp (ip, fecha, refer) values ('$dirIp', " . time() . ", '$ref')";
	$correoMi .= "<br>comercio=" . $d['comercio'] . " && transaccion=" . $d['transaccion'] . " && importe=" . $d['importe'] . " && moneda=" . $d['moneda'] . " && operaci&oacute;n=" .
			$d['operacion'] . " && firma=" . $d['firma'];
	$temp->query($query);
}

//correoAMi($titulo, $correoMi);
$correo->set_subject($titulo);
$correo->set_message($correoMi);
$correo->envia(9);
//echo $correoMi;

function muestraError ($etiqueta, $textoCorreo) {
	$correo = new correo();
	$textoCorreo .= $etiqueta;
	$correo->set_subject('Entrada de datos');
	$correo->set_message($textoCorreo);
	$correo->envia(9);
	echo '<script>document.getElementById("avisoIn").style.display="none";document.writeln("<div id=\"errDvd\" style=\"margin:20px 0 0 "+
((window.innerWidth)-800)/2
+"px; width:800px; text-align:center;\">")</script>
<img src="images/pagina_error.png" width="347" height="304" alt="Error" title="Error" /><br /><br />
Hubo un <span style="color:red;font-weight:bold;">Error</span>
en los datos enviados<br />por favor consulte a su comercio.<br />'.$etiqueta.'</div>
<!-- '.$etiqueta.' -->';
	exit;
}
?>