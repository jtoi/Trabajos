<?php
define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/hoteles.func.php' );
require_once( 'admin/adminis.func.php' );
require_once( 'admin/classes/entrada.php' );
include_once("include/correo.php");
$temp = new ps_DB;
$bd = new ps_DB;
$ent = new entrada;
$corCreo = new correo();

$d=$_REQUEST['cod'];
$c=$_REQUEST['com'];
if (_MOS_CONFIG_DEBUG){
// 	$d = '0605222835';
// 	$c = '139333436635';
}
$fec = time();
if (!($dirIp = GetIP())) exit();
error_log("DIR IP - ".$dirIp);
$muela3 = "";


if (ipBloqueada($dirIp)) {//verifica que la ip no esté bloqueada
	echo '<div style="background: url(\'template/images/degrada.png\') repeat-x scroll 0 0 transparent;"><div style="text-align:center; background: url(\'template/images/banner.jpg\') no-repeat scroll left center transparent; height: 73px; margin:0">&nbsp;</div></div><div style="text-align:center; margin-top:100px; font-family:sans serif; font-size:10px; background: url("images/banner.png") no-repeat scroll left center transparent; height: 73px;">Su IP est&aacute; bloqueada / Your IP is banned</div>'; 
	if ($cuenta == 5) $corCreo->todo ( 11, 'IP banned pagoOnline '.$dirIp, "Se ha bloqueado el acceso al pagoOnline desde la IP ".$dirIp );
	error_log("IP Bloqueada pagoOnline - ".$dirIp);
	exit();
}

if (strlen($d) < 20) {
	if (!($inicio->comer = $ent->isEntero($c, 15))) {
		muestraError ("falla por comercio");
	}
	if (!($inicio->tran = $ent->isUrl($d, 19))) {
		muestraError ("falla por transaccion");
	}

    $query = sprintf("select r.nombre cliente, r.id_comercio, r.id_transaccion, c.nombre comercio, servicio, valor_inicial, r.estado,
                    m.moneda, r.moneda idmoneda, c.palabra, c.condiciones_esp, c.condiciones_eng, idioma, r.pasarela, r.amex, c.id idcom
                from tbl_reserva r, tbl_comercio c, tbl_moneda m
                where r.id_comercio = c.idcomercio
                    and r.moneda = m.idmoneda
                    and r.fecha > $fec - (r.tiempoV*86400)
                    and r.codigo = '%s'
					and r.estado = 'P'
					and r.id_comercio = '%s'", $d, $c);
// echo $query;
	if ($_SERVER['SERVER_ADDR'] == '190.15.147.50' || $_SERVER['SERVER_ADDR'] == '217.160.140.131') {echo $query; echo "<br>";}
    $temp->query($query);
    $cliente = $temp->f('cliente');
    $comercio = $temp->f('comercio');
    $idcomercio = $temp->f('id_comercio');
    $servicio = $temp->f('servicio');
    $importeTot = $temp->f('valor_inicial')*100;
    $moneda = $temp->f('moneda');
    $trans = $d;
	$idioma = $temp->f('idioma');
    $palabra = $temp->f('palabra');
    $idmoneda = $temp->f('idmoneda');
	$pasarela = $temp->f('pasarela');
	$estado = $temp->f('estado');
	$amex = $temp->f('amex');
	$idcom = $temp->f('idcom');
	$numT = $temp->num_rows();

	$query = "select a.email mail from tbl_admin a where idrol = 11 and a.idcomercio = '$c' order by a.idadmin asc limit 0,1 ";
//echo "<br>".$query; 
	$bd->query($query);

	if ($numT != 0) {
		if ($idioma == 'es') {
			
			$condiciones = $temp->f('condiciones_esp');
			$muela = "Estimado(a) $cliente <br><br>
			Usted se encuentra en la Pasarela de Pagos de $comercio <br>
			para realizar el pago de ".money_format('%.2n', $importeTot/100)." $moneda<br>por concepto del servicio $servicio.<br><br>";
			if ($pasarela==3)
				$muela2 .= "Usted va a realizar el pago a través de una pasarela protegida por <strong>3D</strong>.<br><br>Si es poseedor de una tarjeta
					<a href='http://usa.visa.com/personal/security/visa_security_program/3_digit_security_code.html' target='_blank'>Visa</a> o una
					<a href='https://www.3dsecure.icicibank.com/ACSWeb/EnrollWeb/ICICIBank/main/secureCode.jsp' target='_blank'>Mastercard</a>
					y no tiene contratado este servicio o no está seguro de tenerlo,<br>
					contacte a su banco emisor antes de proceder al pago de los servicios.<br><br>
					Los trámites de solicitud del código <strong>3D</strong> en la mayoría de los bancos puede ser realizado por teléfono.<br><br>";
			$titulo = 'Pasarela de Pagos de '.$comercio;
			$muela2 .= "Para poder efectuar el pago, usted deberá aceptar los términos y condiciones siguientes:<br><br>";
			$muela2 .= "<h4>".leeSetup('titcondAMF')."</h4>";
			$muela2 .= str_replace("\n", "<br><br>", leeSetup('condAMF'));
				
			if (strlen($condiciones) > 3) $muela2 .= "<br><br><h4>T&eacute;rminos y condiciones del servicio</h4>".str_replace("\n", "<br>", $condiciones);
			$muela3 = "<h4>Seleccione el m&eacute;todo de pago:</h4>";
				
			$boton = "Acepto";
			$boton1 = "Acepto";
				
		} elseif ($idioma == 'it') {

			$condiciones = $temp->f('condiciones_eng');
			$muela = "Dear $cliente <br><br>
			You are in ".$comercio."'s Terminal Point of Sale <br>
			to make the payment of ".money_format('%.2n', $importeTot/100)." $moneda<br>to receive the service $servicio.<br>
			this payment will realized troght<br>";
			$titulo = $comercio. "'s Terminal Point of Sale";
			$muela2 = "To make the payment you must agree with:<br><br>";
			$muela2 .= "<h4>".leeSetup('titcondAMFit')."</h4>";
			$muela2 .= str_replace("\n", "<br><br>", leeSetup('condAMFit'));
			
			if (strlen($condiciones) > 3) $muela2 .= "<br><br><h4>Termini e Condizioni del Servizio</h4>".
					str_replace("\n", "<br>", $condiciones);
			$muela3 = "<h4>Seleziona il tuo metodo di pagamento:</h4>";
			
			$boton = "Accetto";
			$boton1 = "Accetto";
			
		} else {

			$condiciones = $temp->f('condiciones_eng');
			$muela = "Dear $cliente <br><br>
			You are in ".$comercio."'s Terminal Point of Sale <br>
			to make the payment of ".money_format('%.2n', $importeTot/100)." $moneda<br>to receive the service $servicio.<br>
			this payment will realized troght<br>";
			$titulo = $comercio. "'s Terminal Point of Sale";
			$muela2 = "To make the payment you must agree with:<br><br>";
			$muela2 .= "<h4>".leeSetup('titcondAMFeng')."</h4>";
			$muela2 .= str_replace("\n", "<br><br>", leeSetup('condAMFeng'));
			
			if (strlen($condiciones) > 3) $muela2 .= "<br><br><h4>Terms and Conditions of the Service</h4>".
					str_replace("\n", "<br>", $condiciones);
			$muela3 = "<h4>Select your payment method:</h4>";
	
			$boton = "Agree";
			$boton1 = "Agree";
	
		}
	} else {
		
		$muela2 = "Estimado Cliente / Dear Cliente <br><br>
		Esta transacción ha sido procesada anteriormente o eliminada por vencer la fecha. Contacte su proveedor<br><br>
		This transacction has been proccessed before or has been deleted by date. Contact your provider.<br><br>";

		
	}


    $imagen = "admin/template/images/banner2.png";
//echo "$firma = md5($idcomercio.$trans.$importeTot.$idmoneda.'P'.$palabra);";

    $firma = md5($idcomercio.$trans.$importeTot.$idmoneda.'P'.$palabra);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $titulo ?></title>
<link href="admin/template/css/admin.css" rel="stylesheet" type="text/css" />
<link href="admin/template/css/calendar.css" rel="stylesheet" type="text/css" />
<!-- <script type="text/javascript" src="admin/js/jquery.js" ></script> -->
<script>
function ejecCamb() {
	document.getElementById("cdp").style.display="block";
	document.getElementById("dac").style.display="none";
}

</script>
</head>
<body>
    <div id="encabPago">
        <div id="logoPago"><img src="<?php echo $imagen ?>" /> </div>
        <div class="inf"></div>
    </div>
    <div id="cuerpoPago" style="padding: 20px;">
        <?php echo $salida ?>
<?php
//if (strlen($condiciones) > 3) {
	echo $muela2;

?>
        
        <?php //echo str_replace("\n", "<br>", $condiciones) ?><br /><br />
<?php //}
if ($numT != 0) { ?>
		<input style="background-color:#5EBEEF;color:white;display:none;border:2px solid navy;font-weight:bold;height:30px;text-align:center;text-decoration:none;vertical-align:middle;width:90px;line-height:26px;margin:0 auto;" type="button" id="dac" onclick="ejecCamb();" value="<?php echo $boton1 ?>" />
		<div id="cdp" style="display: block;">
			<form name='envPago' id="forl" method='post' action='index.php'>
				<input type='hidden' name='pasarela' value='<?php echo $pasarela ?>'/>
				<input type='hidden' name='comercio' value='<?php echo $idcomercio ?>'/>
				<input type='hidden' name='transaccion' value='<?php echo $trans ?>'/>
				<input type='hidden' name='importe' value='<?php echo $importeTot ?>'/>
				<input type='hidden' name='moneda' value='<?php echo $idmoneda ?>'/>
				<input type='hidden' name='operacion' value='P'/>
				<input type='hidden' name='idioma' value='<?php echo $idioma ?>'/>
				<input type='hidden' name='firma' value='<?php echo $firma ?>'/>
				<?php echo $muela3; ?><br>
<?php 
$q = "select distinct t.id, t.nombre, t.tipo, t.imagen from tbl_tarjetas t, tbl_colTarjPasar c where c.idTarj = t.id and c.idPasar in (select r.idpasarela from tbl_rotComPas r where r.idcom = $idcom and r.activo = 1) and t.activo = 1 order by t.tipo desc, t.nombre";
error_log($q) ;
$temp->query($q);
$arrTar = $temp->loadRowList();
error_log(count($arrTar));
for ($i=0; $i<count($arrTar); $i++){
	if ($arrTar[$i][2] == 'T') {
		echo '<label for="t'.$arrTar[$i][3].'"><input type="radio" id="t'.$arrTar[$i][3].'" name="amex" onclick="document.getElementById(\'forl\').submit()" value="'.$arrTar[$i][0].'"><img src="admin/images/'.$arrTar[$i][3].'.jpg"></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	} elseif ($arrTar[$i][2] == 'M') {
		echo '<br><br><label for="t'.$arrTar[$i][3].'"><input type="radio" id="t'.$arrTar[$i][3].'" name="amex" onclick="document.getElementById(\'forl\').submit()" value="'.$arrTar[$i][0].'"><img src="admin/images/'.$arrTar[$i][3].'.jpg"></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	}
}

?>
			</form>
		</div>
    </div>
<?php } ?>
    <div id="cuerpoPago">
        <div class="inf2"></div>
        Copyright &copy; Administrador de Comercios, <?php echo date('Y', time()); ?><br /><br />
        <table width="10" border="0" cellspacing="0" align="center">
            <tr>
                <td>

                </td>
            </tr>
            <tr>
                <td height="0" align="center">
                   <!-- GeoTrust QuickSSL [tm] Smart  Icon tag. Do not edit. -->
						<!--<script language="javascript" type="text/javascript" src="//smarticon.geotrust.com/si.js"></script>-->
		<table width="135" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose GeoTrust SSL for secure e-commerce and confidential communications.">
<tr>
<td width="135" align="center" valign="top"><script type="text/javascript" src="https://seal.geotrust.com/getgeotrustsslseal?host_name=www.administracomercios.com&amp;size=S&amp;lang=en"></script><br />
<a href="http://www.geotrust.com/ssl/" target="_blank"  style="color:#000000; text-decoration:none; font:bold 7px verdana,sans-serif; letter-spacing:.5px; text-align:center; margin:0px; padding:0px;"></a></td>
</tr>
</table>
						<!-- end  GeoTrust Smart Icon tag -->
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php } 

function muestraError ($etiqueta) {
	marcaIP($dirIp);
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
