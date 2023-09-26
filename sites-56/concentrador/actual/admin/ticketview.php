<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$temp = @ new ps_DB;
$html = new tablaHTML;

$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/ticket/ticket.html.php'));
$partes = explode('{corte1}', $admin_mod);

$d = $_REQUEST;

if ($d['buscar']) {
	$tira = explode('and', $d['buscar']);
	$fecha1 = date('d/m/Y', substr($tira[3], strlen($tira[3])-11));
	$fecha2 = date('d/m/Y', substr($tira[4], 0, 11));
} else {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, date("m")-1, date('d'), date("Y")));
	$fecha2 = date('d/m/Y', time());
	if ($d['fecha1']) $fecha1 = $d['fecha1'];
	if ($d['fecha2']) $fecha2 = $d['fecha2'];
}

if ( $d['contenidos'] ) { //salva y envia correo por la solicitud de aclaración o ciere del ticket

	//inserta la aclaración en la BD
	$separador = "\n\n*********************************************************\n\n";
	$query = "update tbl_ticket set texto = concat('{$d['contenidos']}', '$separador', texto), fechaModificada = ".time()." where idticket = ".$d['idtic'];
	$temp->query($query);
if (_MOS_CONFIG_DEBUG) echo $query."<br>";
	
	//lee los datos para mandar correo de aclaración al cliente
	$query = "select t.*, a.nombre, email from tbl_ticket t, tbl_admin a
				 where t.idadmin = a.idadmin and idticket = ".$d['idtic'];
	$temp->query($query);
if (_MOS_CONFIG_DEBUG) echo $query."<br>";


	$from = 'tpv@caribbeanonlineweb.com';
	$subject = "Re:/ Ticket No. {$temp->f('idticket')} ".$temp->f('asunto');
	$message = $temp->f('texto');

	global $send_m;

   $arrayTo = array(
	   array('Julio Toirac', 'jtoirac@gmail.com'),
	   array($temp->f('nombre'), $temp->f('email'))
   );

	foreach ($arrayTo as $todale) {
		$to = $todale[1];

		$headers  = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
//		$headers .= 'To: '.$todale[0].'<'. $to . ">\n";
		$headers .= 'From: Administrador de Comercios Travels and Discovery - '.$comercioN.' <'. $from . ">\n";

		$send_m->from($from);
		$send_m->to($to);
		$send_m->set_message($message);
		$send_m->set_subject($subject);
		$send_m->set_headers($headers);
		$enviado = $send_m->send();

if (_MOS_CONFIG_DEBUG) echo "header: $headers<br>";
if (_MOS_CONFIG_DEBUG) echo "subject: $subject<br>";
if (_MOS_CONFIG_DEBUG) echo "mensaje: $message<br>";
	}

   if ($enviado) {
		echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
		"._TICKET_OK."</div>";


    } else {
        echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">"._TICKET_NOOK.":
				<a href='mailto:jotate@amfglobals.com&subject=Error en el sistema de tickets.'>jotate@amfglobals.com</a></div>";
    }
}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_VIETICKET;
$html->tituloTarea = _REPORTE_TASK;
$html->anchoTabla = 500;
$html->anchoCeldaI = $html->anchoCeldaD = 245;

if (!$d['cambiar']) {
	
	$html->inTextb(_TICKET_ID, $idticket, 'idtic');
	$html->inTextb(_TICKET_CONT, '', 'contenido');
	$estadoArr = "select idadmin id, nombre from tbl_admin where activo = 'S' order by nombre";
	$html->inSelect(_AUTENT_LOGIN, 'admin', 5, $estadoArr);
	$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1');
	$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2');

} else { //Muestra los datos del ticket

	$query = "select t.*, a.nombre, a.email, case a.idcomercio when 'todos' then 'todos' when 'varios' then 'varios' else (select nombre from tbl_comercio c where c.idcomercio = a.idcomercio) end comercio 
				from tbl_ticket t, tbl_admin a
				where t.idadmin = a.idadmin and idticket = ".$d['cambiar'];
//	echo $query;
	$temp->query($query);
	$temp->f('fechaModificada') == 0 ? $fechaModificada = '-' : $fechaModificada = date('d/m/Y H:i:s', $temp->f('fechaModificada'));
	$temp->f('fechaTerminada') == 0 ? $fechaTerminada = '-' : $fechaTerminada = date('d/m/Y H:i:s', $temp->f('fechaTerminada'));
	$temp->f('fechaEntrada') == 0 ? $fechaEntrada = '-' : $fechaEntrada = date('d/m/Y H:i:s', $temp->f('fechaEntrada'));
	$temp->f('estado') == 'A' ? $estado = _TICKET_ACTIVO : $estado = _TICKET_CERRADO;
	$texto = str_replace("\n", "<br>", $temp->f('texto'));
	$idticket = $temp->f('idticket');
	$asunto = $temp->f('asunto');
	$nombre = $temp->f('nombre');
	$email = $temp->f('email');
	$comercio = $temp->f('comercio');
	
	$html->inHide($idticket, 'idtic');
	$html->inTexto(_TICKET_ID, $idticket);
	$html->inTexto(_TICKET_ASUNTO, $asunto);
	$html->inTexto(_REPORTE_CLIENTE, $nombre);
	$html->inTexto(_FORM_CORREO, $email);
	$html->inTexto(_MENU_ADMIN_COMERCIO, $comercio);
	$html->inTexto(_TICKET_FENTRADA, $fechaEntrada);
	$html->inTexto(_COMERCIO_ESTADO, $estado);
	$html->inTexto(_REPORTE_FECHA_MOD, $fechaModificada);
	$html->inTexto(_TICKET_FCERRADO, $fechaTerminada);
	$html->inTexto(_TICKET_DESCR, $texto);
	$html->inTexarea(_TICKET_SOLIC, '', 'contenidos', 5, null, null, null, 30);
}

echo $html->salida();


$vista = "select idticket id, a.nombre, asunto, fechaEntrada, fechaModificada, fechaTerminada,
				case t.estado when 'A' then 'Activo' when 'T' then 'Cerrado' end estado,
				case t.estado when 'A' then 'green' when 'T' then 'black' end `color{col}`
			from tbl_ticket t, tbl_admin a ";

$where = " where t.idadmin = a.idadmin ";
if ($_SESSION['grupo_rol'] > 1) $where .= " and a.idadmin = ".$_SESSION['id'];

if ($d['contenido'] == '' && $d['nombre'] == '') {
	$where .= " and fechaEntrada between ".to_unix($fecha1)." and ".((to_unix($fecha2)*1)+86400);
	if ($d['moneda'] != '') $where .= " and a.idadmin = ".$d['moneda'];
} elseif ($d['contenido'] != '' && $d['nombre'] == '') {
	$where .= " and (asunto like '%".$d['contenido']."%' or texto like '%".$d['contenido']."%')";
} else $where .= " and idticket = {$d['nombre']}";

$orden = 'estado asc, fechaEntrada asc';

$colEsp = array(
				array("e", _TAREA_VER, "css_edit", _TAREA_VER)
			);
$busqueda = array();
$columnas = array(
				array('', "color{col}", "1", "center", "center" ),
				array(_TICKET_ID, "id", "", "center", "center" ),
				array(_REPORTE_CLIENTE, "nombre", "", "center", "left" ),
				array(_TICKET_ASUNTO, "asunto", "250", "center", "left" ),
				array(_COMERCIO_ESTADO, "estado", "", "center", "center" ),
				array(_TICKET_FENTRADA, "fechaEntrada", "150", "center", "center" ),
				array(_REPORTE_FECHA_MOD, "fechaModificada", "", "center", "center" ),
				array(_TICKET_FCERRADO, "fechaTerminada", "", "center", "center" )
			);

$ancho = 1000.;

$querys = tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );


?>
