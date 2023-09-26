<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/ticket/ticket.html.php'));
$partes = explode('{corte1}', $admin_mod);

global $dms;
global $temp;
$corCreo = new correo();
$html = new tablaHTML;
$d = $_POST;
$fechaNow = time();
$incluye = "";
$comer = $_SESSION['comercio'];

// print_r($_SESSION);

//inserta Art&iacute;culo
if ($d['inserta']) {
	
	if (strlen($_SESSION['comercioNom']) == 0) {
		$q = "select nombre from tbl_comercio where id in ({$_SESSION['idcomStr']}) limit 0,1";
		$temp->query($q);
		$comerNom = $temp->f('nombre');
	} else 
		$comerNom = $_SESSION['comercioNom'];
	
	$query = "insert into tbl_ticket (idadmin, fechaEntrada, fechaModificada, texto, asunto)
				values (".$_SESSION['id'].", $fechaNow, $fechaNow, '".$d['contenido']."', '{$d['asunto']}')";
	$temp->query($query);
	$idTic = $temp->last_insert_id();

	$conten = _TICKET_CONSULTA;
	$conten = str_replace('{numer}', $idTic, $conten);
	$conten .= '<a href="'._ESTA_URL.'/closeTicket.php?tick='.$idTic.'">'._ESTA_URL."/closeTicket.php?tick=$idTic</a>";

	$query = "update tbl_ticket set texto = concat(texto, '$conten') where idticket = ".$idTic;
	$temp->query($query);

	$message = "Ticket: ".$idTic."<br>Impuesto por: ".$_SESSION['admin_nom']."<br>Teléfono: ".$d['telf']."<br>Email: ".
					$_SESSION['email']."<br>Del Comercio: ".$comerNom."<br> ".$d['asunto']."<br><br>".$d['contenido'].
					"<br><br>".$conten;

	//envío de aviso al móvil
	#Mensajes a enviar
	$contMensaje = "Ticket: ".$idTic."\nde: ".$_SESSION['admin_nom']."\nTeléfono: ".$d['telf']."\nCom: ".$comerNom
					."\n".$d['asunto']." - ".$d['contenido'];
	// echo $contMensaje;
if (_MOS_CONFIG_DEBUG)	echo $contMensaje."<br>";

	envioSMS('1', $contMensaje); //envío de SMS a 

   
	
// 	foreach($arrayDest as $destin) {
// if (_MOS_CONFIG_DEBUG) echo generaCodEmp()."/".$destin."/".$d['asunto'];
// 		$dms->mensajes->add(generaCodEmp(),$destin, $contMensaje, 'AMFAdmin');
// 	}

// 	#Enviar mensajes a plataforma
// 	$dms->send();

// 	#Verificar Resultado
// 	if ($dms->autentificacion->error){
// 		#Error de autentificacion con la plataforma
// 		$saliMensaje .= $dms->autentificacion->mensajeerror;

// 	}else{
// 		#Autentificacion correcta
// 		$saliMensaje .= $dms->autentificacion->saldo."\n";
// 		$saliMensaje .= (count($dms->mensajes->get())-$dms->mensajes->errores);
// 		if ($dms->mensajes->errores>0){
// 			$saliMensaje .= "Mensajes con errores: ".$dms->mensajes->errores."\n";
// 			$saliMensaje .= "Detalles:\n";
// 			foreach ($dms->mensajes->get() as $msg){
// 				if ($msg->error){
// 					$saliMensaje .= "   - " . $msg->destino . "->" . $msg->mensajeerror . "\n";
// 				}
// 			}
// 		}
// 	}
    

	//envío de correo
	// $from = 'info@amfglobalitems.com';
$subject = "Ticket No. $idTic ".$d['asunto'];

	// global $send_m;

  //  $arrayTo = array(
	//    array('Servicio Tecnico', _CORREO_SITE),
	//    array('Tere Espino', 'dir.general@bidaiondo.com'),
	//    array('Ivett Roig', 'marketing@bidaiondo.com'),
	//    array($_SESSION['admin_nom'], $_SESSION['email'])
  //  );
//echo $message;

	// foreach ($arrayTo as $todale) {
	// 	$to = $todale[1];

	// 	$headers  = 'MIME-Version: 1.0' . "\n";
	// 	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
	// 	$headers .= 'To: '.$todale[0].'<'. $to . ">\n";
	// 	$headers .= 'From: Administrador de Comercios- '.$comercioN.' <'. $from . ">\n";

	// 	if ($todale[0] == 'Tere Espino' || $todale[0] == 'Servicio Tecnico' || $todale[0] == 'Ivett Roig') $message .= "\n\n<br>".$saliMensaje;

		// $send_m->from($from);
		// $send_m->to($to);
		// $send_m->set_message($message);
		// $send_m->set_subject($subject);
		// $send_m->set_headers($headers);
		// $enviado = $send_m->send();


	if (_MOS_CONFIG_DEBUG) echo "mensaje$message<br>";
	if (_MOS_CONFIG_DEBUG) echo "header$headers<br>";
		// }

	// if (!stripos($message,'prueba')) {
		if ($corCreo->todo(61, $subject, $message."<br>".$saliMensaje)) {
			echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
			"._TICKET_OK."</div>";
			sendTelegram($subject. $message."<br>".$saliMensaje,null,'T');

		} else {
			echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">"._TICKET_NOOK.":
					<a href='mailto:"._CORREO_SITE."&subject=Error en el sistema de tickets.'>"._CORREO_SITE."</a></div>";
		}
	// }
}

//javascript
$javascript = "
	<script language=\"JavaScript\" type=\"text/javascript\">
	function verifica() {
		if (
				(checkField (document.forms[0].contenido, isAlphanumeric, ''))&&
				(checkField (document.forms[0].telf, isInteger, ''))
			) {
			return true;
		}
		return false;
	}

	$(function() {
		$('textarea').supertextarea({
		   maxw: 400
		  , maxh: 100
		  , minw: 300
		  , minh: 20
		  , dsrm: {use: false}
		  , tabr: {use: false}
		  , maxl: 1000
		});
	});
";
if ($alerta)
	$javascript .= "alert('$alerta');";
$javascript .= "</script>";



$html->java = $javascript;

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _COMERCIO_TITULO;
$html->tituloTarea = $titulo_tarea;
$html->anchoTabla = 550;
$html->tabed = true;
$html->anchoCeldaI = 200;
$html->anchoCeldaD = 340;

$html->inTextb(_TICKET_ASUNTO, '', 'asunto');
$html->inTextb(_TICKET_TELEF, '', 'telf');
$html->inTexarea(_TICKET_DESCR, '', 'contenido', 20);
$html->inHide('1', 'inserta');

echo $html->salida();


?>
