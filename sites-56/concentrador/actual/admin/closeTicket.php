<?php
define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' );
require_once( 'include/maysqli.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'include/sendmail.php' );
require_once("admin/classes/class_dms.php");
require_once("admin/adminis.func.php");
$send_m = new sendmail();
$temp = new ps_DB;

$dms=new dms_send;
#Datos de acceso a la plataforma
$dms->autentificacion->idcli='126560';
$dms->autentificacion->username='amfglobalitems';
$dms->autentificacion->passwd='Mario107';

$d=$_REQUEST['tick'];
$tic=$_POST['ticet'];
$ticc=$_POST['ticec'];
$fec = time();
$imagen = "admin/template/images/banner2.png";

if (strlen($ticc) >= 1 && strlen($ticc) <= 13) {

	$query = "update tbl_ticket set estado = 'T', fechaTerminada = ".time()." where idticket = $ticc";
	$temp->query($query);

	$ponerAhi = "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
					El Ticket No. $ticc ha sido cerrado correctamente.<br>Gracias por usar nuestro sistema de tickets.</div>";

} else {
	if (strlen($d) >= 1 && strlen($d) <= 13) {

		$query = "select t.*, a.nombre from tbl_ticket t, tbl_admin a
					 where t.idadmin = a.idadmin
						and estado = 'A'
						and idticket = ".$d;

	//    echo $query;
		$temp->query($query);
		if ($temp->num_rows() > 0) {

			$cliente = $temp->f('nombre');
			$fecha = date('d/m/Y H:i', $temp->f('fechaEntrada'));
			$fecham = date('d/m/Y H:i', $temp->f('fechaModificada'));
			$titulo = $temp->f('asunto');
			$textReem = "Su ticket tiene el n?mero ".$d.". Puede consultar el estado en:<br>".
					'<a href="'._ESTA_URL.'/closeTicket.php?tick='.$d.'">'._ESTA_URL."/closeTicket.php?tick=$d</a>";
			$texto = str_replace($textReem, "",  str_replace("\n", "<br>", $temp->f('texto')));
			$trans = $d;
			$idioma = $temp->f('idioma');
			$palabra = $temp->f('palabra');
			$idmoneda = $temp->f('idmoneda');

			$condiciones = $temp->f('condiciones_esp');
			$muela = "Estimado(a) $cliente usted abri? el ticket No. $d el pasado $fecha<br><br>Con asunto: $titulo<br><br>";
			if ($fecham != '31/12/1969 19:00')
				$muela .= "El mismo fu? modificado $fecham <br><br>";
			$muela .= "Lea detenidamente el desarrollo del mismo, si desea argumentar algo m?s escriba al final. Si por el contrario est? satisfecho<br>con la respuesta
						que el administrador le ha dado por favor ci?rrelo oprimiendo el bot?n Cerrar Ticket.<br><br>
						<span style='font-weight:bold;font-size:1.2em'>Desarrollo del Ticket</span><br><br>$texto<br><br>";
			$titulo = 'Ticket no. '.$d;
		} else
			$ponerAhi = "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
							El Ticket ya se encuentra cerrado, deber? entrar al sitio y crear uno nuevo.</div>";

	} else
			$ponerAhi = "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
							No es un ticket v?lido.</div>";
}

if (strlen($tic) >= 1 && strlen($tic) < 13) {
	$separador = "\n\n*********************************************************\n\n";

	$query = "update tbl_ticket set fechaModificada = ".time().", texto = concat('{$_POST['contiene']}', '$separador', texto)
				where idticket = $tic";
	$temp->query($query);
//	echo $query;

	$query = "select * from tbl_ticket where idticket = $tic";
	$temp->query($query);
//	echo $query;

	$asunto = $temp->f('asunto');
	$message = $temp->f('texto');

	$movl = "Ticket: ".$tic." - ".$asunto;

	//env?o de aviso al m?vil
	#Mensajes a enviar
	$arrayDest = array(
						'005352643646'
				);

	foreach($arrayDest as $destin) {
		$dms->mensajes->add(generaCodEmp(),$destin,$movl);
	}

	#Enviar mensajes a plataforma
	//$dms->send();

	//env?o de correo
	$from = 'tpv@caribbeanonlineweb.com';
	$subject = $movl;

	global $send_m;

   $arrayTo = array(
	   array('Julio Toirac', 'serv.tecnico@bidaiondo.com'),
	   array($_SESSION['admin_nom'], $_SESSION['email'])
   );

	foreach ($arrayTo as $todale) {
		$to = $todale[1];

		$headers  = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
		//$headers .= 'To: '.$todale[0].'<'. $to . ">\n";
		$headers .= 'From: Administrador de Comercios Travels and Discovery - '.$comercioN.' <'. $from . ">\n";

		$send_m->from($from);
		$send_m->to($to);
		$send_m->set_message($message);
		$send_m->set_subject($subject);
		$send_m->set_headers($headers);
		$enviado = $send_m->send();

if (_MOS_CONFIG_DEBUG) echo "mensaje$message<br>";
if (_MOS_CONFIG_DEBUG) echo "header$headers<br>";
	}

   if ($enviado) {
		$ponerAhi = "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
		Ticket actualizado satisfactoriamente</div>";


    } else {
        $ponerAhi = "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">
                Hubo un error en el envío del ticket al administrador,
				por favor hágalo nuevamente o póngase en contacto con el mismo por correo a la dirección:
				<a href='mailto:serv.tecnico@bidaiondo.com&subject=Error en el sistema de tickets.'>serv.tecnico@bidaiondo.com</a></div>";
    }

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $titulo ?></title>
<link href="admin/template/css/admin.css" rel="stylesheet" type="text/css" />
<link href="template/css/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	function quitaticet() {
		document.getElementById('ticet').value = '';
		document.getElementById('ticec').value = '<?php echo $d ?>';
		document.forms[0].submit();
	}
</script>
</head>
<body>
    <div id="encabPago">
        <div id="logoPago"><img src="<?php echo $imagen ?>" /> </div>
        <div class="inf"></div>
    </div>
    <div id="cuerpoPago" class="cuerpoPago">
        <?php if ($ponerAhi == '') {
		echo $muela ?>
   
        <form name='envTick' method='post' action=''>
			<input type="button" value="Cerrar Ticket" onclick="quitaticet();"><br><br><br>
			<span style="font-weight:bold">Argumentar el problema:</span><br><br>
			<textarea rows="20" cols="100" class="formul" name="contiene" ><?php echo $condiciones ?></textarea><br /><br />
            <input type='hidden' name='ticet' id='ticet' value='<?php echo $d ?>' />
            <input type='hidden' name='ticec' id='ticec' />
            <input type="submit" value="Enviar" />
        </form>
    </div>
	<?php } else echo $ponerAhi; ?>
    <div id="cuerpoPago1" class="cuerpoPago">
        <div class="inf2"></div>
        Copyright &copy; Travels and Discovery S.A., <?php echo date('Y', time()); ?><br /><br />
        <table width="10" border="0" cellspacing="0" align="center">
            <tr>
                <td>

                </td>
            </tr>
            <tr>
                <td height="0" align="center">
                    <script type="text/javascript" src="https://seal.verisign.com/getseal?host_name=www.concentradoramf.com&amp;size=S&amp;use_flash=NO&amp;use_transparent=NO&amp;lang=es"></script><br />
<a href="http://www.verisign.es/ssl-certificate/" target="_blank"  style="color:#000000; text-decoration:none; font:bold 7px verdana,sans-serif; letter-spacing:.5px; text-align:center; margin:0px; padding:0px;">Acerca de los certificados SSL</a>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
