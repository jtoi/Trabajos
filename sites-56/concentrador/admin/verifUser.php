<?php
//Realiza la verificación de los usuarios del Concentrador cdo
//los mismos tienen problemas de IP, contraseña perdida o 
//el usuarioha sido desactivado
define( '_VALID_ENTRADA', 1 );
include_once( '../configuration.php' );
include_once( 'classes/entrada.php' );
require_once( '../include/mysqli.php' );
include_once("../include/correo.php");
require_once( '../include/hoteles.func.php' );
include_once( 'adminis.func.php' );

$temp = new ps_DB();
$ent = new entrada;
$corCreo = new correo();

$d = $_REQUEST;
$muela2 = '';
$muela1 = "
<form name='envdat' method='post' action=''>
	<input type='hidden' name='ip' value='".$d['ip']."'/>
	Email: <input type='text' name='email' value=''/><br><br><br>
	<input style='background-color:#5EBEEF;color:white;display:block;border:2px solid navy;font-weight:bold;height:30px;text-align:center;text-decoration:none;vertical-align:middle;width:90px;line-height:26px;margin:0 auto;' type='submit' value='Enviar / Send' />
</form>";

if (_MOS_CONFIG_DEBUG) {
	foreach ($d as $key => $value) {
		echo "$key => $value<br>";
	}
}

if (count($d) > 0) {//se reciben datos
	if (isset($d['code']) && (isset($d['email']) || isset($d['emails'])) && isset($d['ip'])) //chequear si el código es el correcto
		$paso = 3;
	elseif (isset($d['email']) && isset($d['ip'])) //chequear si el email es correcto
		$paso = 2;
	elseif (isset($d['ip'])) //entrada
		$paso = 1;
	else 
		die;
		
	error_log("paso=".$paso);

	if (isset($d['code'])) {
		$d['code'] = trim($d['code']);
		if (!$code = $ent->isEntero($d['code'], 6)) {
			$muela2 = "C&oacute;digo incorrecto. Vuelva a intentarlo<br>Wrong code. Try it again<br><br><br>";
			$paso = 3;
		}
	}

	if (isset($d['email'])) $correo = $d['email'];
	if (isset($d['emails'])) $correo = $d['emails'];
	if (isset($correo)) {
		if (!$email = $ent->isCorreo($correo)) {
			$muela2 = "Email incorrecto. Vuelva a intentarlo<br>Wrong email. Try it again<br><br><br>$muela1";
			$paso = 1;
		}
	}

	if (isset($d['usuariover'])) {
		if (!$usr = $ent->isAlfanumerico($d['usuariover'], 15)) {
			echo "<script>window.open('index.php', '_self');</script>";
		}
	}

	if (isset($d['ip'])) {
		if (!$ip = $ent->isIp($d['ip'])) {
			die('Ip incorrecta');
		}
	}


$muela3 = "
<form name='reenv' method='post' action=''>
	<input type='hidden' name='email' value='".$correo."'/>
	<input type='hidden' name='ip' value='".$d['ip']."'/>
	<input type='hidden' name='usuariover' value='".$usr."'/>
</form>
<form name='envdat' method='post' action=''>
	<input type='hidden' name='emails' value='".$correo."'/>
	<input type='hidden' name='ip' value='".$d['ip']."'/>
	<input type='hidden' name='usuariover' value='".$usr."'/>
	Código / Code: <input type='text' name='code' value=''/><br><br><br>
	<input style='background-color:#5EBEEF;color:white;display:block;border:2px solid navy;font-weight:bold;height:30px;text-align:center;text-decoration:none;vertical-align:middle;width:90px;line-height:26px;margin:0 auto;' type='submit' value='Enviar / Send' />
</form><br><br><span id='reenvio' style='cursor:pointer;text-decoration:underline;' >Volver a reenviar el c&oacute;digo nuevamente<br> Resend code again</span>
<script>
	$(document).ready(function(){
		$('#reenvio').click(function(){document.reenv.submit();});
	});
</script>";
	error_log("paso=".$paso);

	if ($paso == 2){
		//revisar si el correo es el que debe ser y envío del código
		if (verificacCorreo($email, $ip) === 'ok') { //correo perfecto se muestra el formulario para el código
			$muela2 .= "Por favor espere unos minutos para recibir el c&oacute;digo en el correo indicado<br>Please wait for a few minutes to receive the code by email<br><br>".$muela3;
		} else {
			$paso = 2;
			$muela2 = "El email no lo tenemos registrado, verifique que haya escrito la direcci&oacute;n de correo con el que est&aacute; inscrito en el sistema<br>Email is not registered, please check it`s which you are registered in the system<br><br><br>".$muela1;
		}
	} elseif ($paso == 3) {
		if (vermiCod($code, $ip, $email, $usr))  {
			if (strlen($usr) > 3) echo "<script>alert('El usario ha sido activado nuevamente y se le han enviado las credenciales a su correo, revise en su bandeja de entrada o la de spam');window.open('index.php', '_self');</script>";
			else echo "<script>window.open('index.php', '_self');</script>";
		} $muela2 = "El c&oacute;digo suministrado no es el correcto, revise su correo (bandejas de entrada y correo no deseado) y teclee el c&oacute;digo del &uacute;ltimo correo recibido<br>Typed code is not correct, check your email (inbox and spam folders) and type the code of the last email received<br><br><br>".$muela3;
	} elseif ($paso == 1 && !isset($d['email'])) $muela2 = "Su IP $ip ha sido bloqueada. Para desbloquearla, indique su direcci&oacute;n de correo en nuestro sistema para enviarle un c&oacute;digo de validaci&oacute;n.<br>Your IP $ip is banned. Please write your email to send an unblock code<br><br><br>".$muela1;

	// if (strlen($d['email']) > 6) {// el dato que se recibe es el correo
		
	// } else if (isset($d['code'])) {
	// 	if (vermiCod($d['code'], $d['ip'], $))
	// 	$muela2 = "C&oacute;digo incorrecto. Vuelva a intentarlo<br>Wrong code. Try it again<br><br><br>$muela1";
	// } else $muela2 = "Email incorrecto. Vuelva a intentarlo<br>Wrong email. Try it again<br><br><br>$muela1";
		
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Verificar Usuario</title>
<link href="template/css/admin.css" rel="stylesheet" type="text/css" />
<link href="template/css/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery.js"></script>
</head>
<body>
    <div id="encabPago">
        <div id="logoPago"><img src="template/images/banner2.png" /> </div>
        <div class="inf"></div>
    </div>
    <div id="cuerpoPago" style="padding: 20px;text-align: center;">
        <?php echo $salida ?>
<?php
	echo $muela2;
?>
 </div>
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