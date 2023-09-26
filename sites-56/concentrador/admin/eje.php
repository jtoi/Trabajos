<?php define( '_VALID_ENTRADA' , 1);
include_once( '../configuration.php' );
include_once( 'classes/entrada.php' );
require_once( '../include/mysqli.php' );
require_once( 'adminis.func.php' );
require_once( '../include/hoteles.func.php' );
require_once( '../include/correo.php' );
$temp = new ps_DB;
$ent = new entrada;
$corr_C = new correo();


if ($_POST['fun'] == 'log') {
//		sleep(2);
	$usuario = $email = $pase = '';
	$error = "Falló la modificación de los Datos,\n";
	if (strlen($usuario = $ent->isAlfanumerico($_POST['usr'],15))==0) $error .= 'el nombre de usuario no es válido'."\n";
	if (strlen($email = $ent->isCorreo($_POST['cor']))==0) $error .= 'el correo no es válido';
	if (strlen($ip = $ent->isIp($_POST['ip']))==0) $error .= 'la ip no es válida';
	
	if (strlen($usuario) > 0 && strlen($email) > 0) {
		$error = '';
		$q = sprintf("SELECT count(idadmin) total FROM tbl_admin WHERE email='%s' and login = '%s'", $email, $usuario);
//		$error = $q;
		$temp->query($q);
		if ($temp->f('total') == 0) {
			$error = "No hay usuario registrado con esos datos.";
			$sale = "ko";
		} else {
			//revisa que el usuario no esté inactivo
			$temp->query(sprintf("SELECT activo FROM tbl_admin WHERE email='%s' and login = '%s'", $email, $usuario));
			if ($temp->f('activo') == 'N') {
				$sale = 'na';
				$error = "El usuario con el que usted accede a la plataforma está desactivado.\nPara activarlo nuevamente necesitamos realizar unas comprobaciones";
				$pase = $ip;
				error_log("ip=$ip");
			} else {

				$contras = validaContrasena($usuario);
				
				$q = sprintf("update tbl_admin set md5 = '".$contras[1]."' WHERE email='%s' and login = '%s'", $email, $usuario);
				$temp->query($q);

				$subject = 'Cambio de contraseña por solicitud';
				$corr_C->to($email);
				$from = 'Concentrador - Acceso <tpv@caribbeanonlineweb.com>';
				$headers  = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
				//$headers .= 'To: '. $to . "\n";
				$headers .= 'From: '. $from . "\n";
				$headers .= 'Bcc: jtoirac@gmail.com' . "\r\n";
				$imprim = '<div style=\"text-align:center; font-family:Arial san-serif; font-size:11px\">A usted se le ha concedido el acceso a la 
							administración del Concentrador. Puede entrar con los siguientes datos:<br>
							<br>Usuario: '.$usuario.'<br>Contraseña: '.$contras[0].'</div>';
				
				$corr_C->todo(19, $subject, $imprim);
	//$sale = $contras[0];
				$error = "Se han enviado las nuevas credenciales al correo entrado.\nSi en unos minutos no aparece en su bandeja de entrada\n, revise en la"; $error .= " bandeja de correo no deseado.";
				$sale = 'ok';
			}
		}
	}
	
	echo json_encode(array('alerta'=>utf8_encode($error),"sale"=>$sale,"pase"=>$pase));
	
} elseif ($_POST['fun'] == 'inscierre') {
	$q = "select idcierre, cierre from tbl_cierreTransac where idcierre = ".$_POST['cie'];
	$temp->query($q);
	echo json_encode(array('pase'=>array($temp->f('idcierre'),$temp->f('cierre')),"error"=>$q));
}
?>
