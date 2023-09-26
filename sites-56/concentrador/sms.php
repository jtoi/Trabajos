<?php

$d = $_REQUEST;


if ($d['ver'] == 'Enviar') {
	include ('admin/classes/class_dms.php');
	$dms=new dms_send;

	
	//date_default_timezone_set('Europe/Berlin');
	$fecha1 = date('d/m/Y', mktime(0, 0, 1, 1, 1, date('Y')));
	$fecha2 = date('d/m/Y');

	$dms->remitente = 'AMFAdmin';
	$dms->mailError = 'jtoirac@gmail.com';
	$dms->debug = false;


	if ($d['cuenta'] == 1) {
		$dms->autentificacion->idcli="126560";
		$dms->autentificacion->username="amfglobalitems";
		$dms->autentificacion->passwd="Mario107";
	}
	if ($d['cuenta'] == 2) {
		$dms->autentificacion->idcli="185529";
		$dms->autentificacion->username="Caribbeanonlineo";
		$dms->autentificacion->passwd="Fincimex12";
	}

	if (strlen($d['mens']) > 1 && strlen($d['telf'][0]) > 1) {
		$dms->autentificacion->EmailNot = '';
		$i = 1;
		foreach ($d['telf'] as $telf) {
			$dms->mensajes->add($i++, $telf, $d['mens']);
		}
		
		$dms->send();
		
		if($dms->autentificacion->error) {
			$arrSal['error'] = $dms->autentificacion->mensajeerror;
		} else {
			$arrSal['respEnv'] = "<span class='dat'>Saldo: </span>".$dms->autentificacion->saldo."<br \>".
								"<span class='dat'>ID Envio Descom: </span>".$dms->mensajes->idenviodm."<br \>".
								"<span class='dat'>Mensajes enviados: </span".$dms->mensajes->total_mensajes."<br \>".
								"<span class='dat'>Mensajes OK: </span".$dms->mensajes->total_ok."<br \>".
								"<span class='dat'>Mensajes errores: </span".$dms->mensajes->total_error."<br \>".
								"<span class='dat'>Creditos gastados: </span".$dms->mensajes->total_creditos."<br \>";

			if ($dms->mensajes->total_error>0){
				$arrSal['error'] .= "Mensajes con errores: ".$dms->mensajes->errores."<br \>";

				foreach ($dms->mensajes->get() as $msg){
					if ($msg->error){
						$arrSal['error'] .= $msg->destino . " -> " . $msg->mensajeerror . "<br \>";
					}
				}
			}
		}
	}
	
	if ($d['sald'] == 1) {
		$dms->send();
		if($dms->autentificacion->error) {
			$arrSal['error'] = $dms->autentificacion->mensajeerror;
		} else {
			$arrSal['respEnv'] = $dms->autentificacion->saldo;
		}
	}

	if($d['con'] == 1){
		$filtro=new dms_filtro;
		$filtro->nfilas=50;
		$filtro->ndesde=1;
		$filtro->ordencampo='nombre';
		$dms->getContacts($filtro);

		if ($dms->autentificacion->error){
			//Error de autentificacion con la plataforma
			$arrSal['error'] = $dms->autentificacion->mensajeerror;
		}else{
			$arrSal['respEnv'] = "<span class='dat'>NTotal: </span>".$dms->contactos->ntotal."<br>".
								"<span class='dat'>NFilas: </span>".$dms->contactos->nfilas."<br>".
								"<span class='dat'>NDesde: </span>".$dms->contactos->ndesde."<br><br>";
			
			$arrSal['respEnv'] .= "<table align='center' cellpadding='5' class='tablRes'><tr>";
			$arrSal['respEnv'] .= "<th>No.</th><th>Id</th><th>Telf</th><th>Nombre</th></tr>";
			foreach ($dms->contactos->contactos as $contacto){
				$arrSal['respEnv'] .= "<tr><td>".$contacto->n."</td>";
				$arrSal['respEnv'] .= "<td>".$contacto->id."</td>";
				$arrSal['respEnv'] .= "<td>".$contacto->numero."</td>";
				$arrSal['respEnv'] .= "<td>".$contacto->nombre." ".$contacto->apellidos."</td></tr>";
			}
			$arrSal['respEnv'] .= "</table>";
		}

	}
	
	if (strlen($d['periodo']) > 0) {
		$filtro=new dms_filtro;
		$filtro->nfilas=50;
		$filtro->periodo = $d['periodo'];
		$filtro->ndesde=1;

		$dms->getReportsMensajes($filtro);

		if ($dms->autentificacion->error){
			$arrSal['error'] = $dms->autentificacion->mensajeerror;
		} else {
			$arrSal['respEnv'] = "<span class='dat'>NTotal: </span>".$dms->contactos->ntotal."<br>".
			"<span class='dat'>NFilas: </span>".$dms->contactos->nfilas."<br>".
			"<span class='dat'>NDesde: </span>".$dms->contactos->ndesde."<br><br>";
			
			$arrSal['respEnv'] .= "<table align='center' cellpadding='5' class='tablRes'><tr>";
			$arrSal['respEnv'] .= "<th>No.</th><th>Id Mensaje</th><th>Id Envio</th><th>Destinatario</th><th>Texto SMS</th><th>Num Mensajes Enviados</th><th>Remitente</th><th>Nombre del Contacto</th>
									<th>Apellidos del Contacto</th><th>Estado</th><th>Fecha Entregado</th><th>Fecha Confirmado</th></tr>";
			for ($n=0;$n<count($dms->reportes_envios->reportes);$n++){
				$r=$dms->reportes_envios->reportes[$n];
				$arrSal['respEnv'] .= "<tr><td>".$r->idreport." - ".$r->estado." ] ".$r->fecha_envio." </td>";
				$arrSal['respEnv'] .= "<td>".$r->idsend."</td>";
				$arrSal['respEnv'] .= "<td>".$r->idreport."</td>";
				$arrSal['respEnv'] .= "<td>".$r->movil."</td>";
				$arrSal['respEnv'] .= "<td>".$r->htexto."</td>";
				$arrSal['respEnv'] .= "<td>".$r->nmens."</td>";
				$arrSal['respEnv'] .= "<td>".$r->hremitente."</td>";
				$arrSal['respEnv'] .= "<td>".$r->hcname."</td>";
				$arrSal['respEnv'] .= "<td>".$r->hcappelidos."</td>";
				$arrSal['respEnv'] .= "<td>".$r->estadohdesc."</td>";
				$arrSal['respEnv'] .= "<td>".$r->fecha_entregado."</td>";
				$arrSal['respEnv'] .= "<td>".$r->fecha_confirmado."</td></tr>";
			}
			$arrSal['respEnv'] .= "</table>";
		}
	}
	echo json_encode($arrSal);
	
}
?>
