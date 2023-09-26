<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

function cambiaSol ($id) {
	global $temp;
	$sale = '';
	$temp->query("select idtransaccion from tbl_transacciones where estado = 'A' and solDev = 1 and idtransaccion = '".$id."'");
	if ($temp->num_rows() == 1) {
		$temp->query("delete from tbl_devoluciones where idtransaccion = '".$id."'");
		if ($temp->getErrorMsg()) {
			$sale = "Error en la operación - ".$temp->getErrorMsg();
		} else {
			$temp->query("update tbl_transacciones set solDev = 0 where idtransaccion = '".$id."'");
			if ($temp->getErrorMsg()) {
				$sale = "Error en la operación - ".$temp->getErrorMsg();
			} else $sale = 'Operación correctamente Actualizada.';
		}
	} else $sale = "La operación requerida no se encuentra o no está solicitada a devolver";
	return $sale;
}

function enviamiError ($textoCorreo) {
	global $temp;

	$temp->query("insert into tbl_traza (titulo,traza,fecha) values ('Error en ejecución','".  $textoCorreo ."',".time().")");
	return true;

}

/**
 * envía mensajes a Telegram
 */
function sendTelegram ($texto,$otro,$bot='G') {
	if ($otro != null) $id = $otro;

	//echo "$texto<br>$otro<br>$bot<br>";

	if ($bot == 'G') {
		$bot_token = "5038443834:AAHk906Tuj0zWRt1kITZtWk1XXX4vZeDnGc"; //envío a mí
		$id="-1001652358529";
	} else {
		$bot_token = "5012285430:AAFH7rZLqOy9BIWf_cs6nrAI7LFLt7oY0a0"; //envio a ticket
		$id="-1001518921053";
	}

	if (strlen($bot_token) == 46) {

		$parameters = array(
			"chat_id" 		=> $id,
			'parse_mode'    => 'HTML',
			"text" 			=> utf8_encode(str_replace("<br>", "\n", str_replace("<br />", "\n", str_replace("</tr>", "", $texto))))
		);

		$url = "https://api.telegram.org/bot$bot_token/sendMessage";

		if (!$curl = curl_init()) {
			exit();
		}

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$output = curl_exec($curl);
		return $output;
}
}

/**
 * Envíos de SMS desde la plataforma Esendex
 *
 * @param [int] $id identificador del mensaje
 * @param [varchar] $mens texto del mensaje
 * @return void
 */
function envioSMS($id, $mens) {
	include "../include/Esendex/autoload.php";
	global $temp;
	$sale = '';
	sendTelegram($mens,null,'T');

	$sumi = leeSetup('sumiSms'); //1=IDM SMS, 2=DescomSMS, 3=Esendex
	//echo('sumiSms= '.$sumi);

	$q = "select a.telefono from tbl_admin a, tbl_colSmsAdmin c where c.idadmin = a.idadmin and c.idsms = $id";
	$temp->query($q);
	$arrUsr = $temp->loadResultArray();
	// $arrUsr = array('5352738723');
	// array_push($arrUsr, '34617731855');
	// var_dump($arrUsr);

	if ($sumi == 3) { //Envío a través de Esendex

		foreach ($arrUsr as $telefono) {

			$message = new \Esendex\Model\DispatchMessage(
				"Bidaiondo", // Send from
				$telefono, // Send to any valid number
				$mens,
				\Esendex\Model\Message::SmsType
			);
			$authentication = new \Esendex\Authentication\LoginAuthentication(
				"EX0304912", // Your Esendex Account Reference
				"serv.tecnico@bidaiondo.com", // Your login email address
				"Bidaiondo#50" // Your password
			);
			$service = new \Esendex\DispatchService($authentication);
			$result = $service->send($message);

			if (strlen($result->id()) > 5) {
				error_log("mensaje enviado a $telefono");
			} else return "Hubo error en el envío de los SMS Esendex";
		}
	} else if ($sumi == 1) { #Envío de los mensajes a IDM SMS i-digital-m
		$saliMensaje = '';
		$arrSali = array();
		//print_r($arrUsr);
		foreach ($arrUsr as $telf) {
			//echo "<br>$telf";
			$cadenaEnvia = "https://smsc.i-digital-m.com/smsgw/sendsms.php?user=GLIshDY2&password=J74Gfte2&sender=5372044424&recipients={$telf}&message=" .urlencode($mens) . "&dlr=1";
			$ch = curl_init($cadenaEnvia);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$saliMensaje .= curl_exec($ch);

			error_log("mensaje i-digital=" . $saliMensaje);
			error_log ("saliMensaje=" . $saliMensaje);
			$arrSali[] = implode(", ", curl_getinfo($ch));
			// error_log ("psaliMensaje=" . print_r($arrSali));
		}

		$cadenaEnvia = "https://smsc.i-digital-m.com/clients/balance.php?username=GLIshDY2&password=J74Gfte2";
		$ch = curl_init($cadenaEnvia);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$saliMensaje .= "<br>Balance de la cuenta: " . curl_exec($ch);
		curl_close($ch);


	} elseif ($sumi == 2) {#Envío de mensajes a DescomSMS
		$arrTo = array();
		$url = "https://api.descomsms.com/api/sms/send";
		$arrTo['to'] = array(implode(',', $arrUsr));


		$json = json_encode(array('messages' => $arrTo, "text" => utf8_encode($mens), "senderID" => 'Info'));
		error_log("Ticket-->".$json);

		$options = array(
				CURLOPT_POST			=> true,
				CURLOPT_VERBOSE			=> true,
				CURLOPT_URL				=> $url,
				CURLOPT_POSTFIELDS		=> $json,
				CURLOPT_CUSTOMREQUEST	=> 'POST',
				CURLOPT_HTTPHEADER		=> array(
						'Content-Type: application/json',
						'DSMS-User: amfglobalitems',
						'DSMS-Pass: Mario107 '
				)
		);
		$ch = curl_init();
		curl_setopt_array($ch , $options);
		error_log("SMSjson=$json");

		$saliMensaje = curl_exec($ch);

		if (strpos($saliMensaje, 'id')){ //si sale el mensaje chequeo el saldo
			$url = "https://api.descomsms.com/api/balance/";
			$options = array(
					CURLOPT_POST			=> true,
					CURLOPT_VERBOSE			=> true,
					CURLOPT_URL				=> $url,
					CURLOPT_POSTFIELDS		=> '',
					CURLOPT_CUSTOMREQUEST	=> 'POST',
					CURLOPT_HTTPHEADER		=> array(
							'Content-Type: application/json',
							'DSMS-User: amfglobalitems',
							'DSMS-Pass: Mario107 '
					)
			);
			$ch = curl_init();
			curl_setopt_array($ch , $options);
			error_log("json=$json");

			$output = curl_exec($ch);

			if (strpos($output, 'balance')) {
				$balance = json_decode($output);

				$saliMensaje .= "<br>Balance de la cuenta: ". $balance['balance'];
			}
		}
	}

	error_log("SMS enviado");
	return "Mensaje enviado";
}

/**
 * Envía las invitaciones de Pago
 * @param array $arrEnt
 * @return string
 */
function envPago($arrEnt){
	global $temp;
	global $ent;
	$corr = new correo();
	$fechaNow = time ();
	$error = '';
	$docroot = str_replace("admin/", "", $arrEnt['dir']);

	$arrEnt['pasarela'] = rtrim($arrEnt['pasarela'],',');
	foreach ($arrEnt as $key => $value) {
		error_log($key." => ".$value);
	}

	$arrayTo = array ();
	$tiemEsper = 1; // horas de espera entre dos operaciones seguidas por la misma pasarela
	if (strlen ( $arrEnt ['comercio'] ) > 3)
		$query = "select * from tbl_comercio where id in ({$arrEnt ['comercio']})";
	else
		$query = "select * from tbl_comercio where id = {$arrEnt ['comercio']}";
	// echo $query;
	$temp->query ( $query );
	$comercioN = $temp->f ( 'nombre' );
	$palabra = $temp->f ( 'palabra' );
	$estCom = $temp->f ( 'estado' );
	$correoMas = $temp->f ( 'correoMas' );
	$correo = str_replace ( '"', '', str_replace ( "'", "", str_replace ( " ", "", $arrEnt ['email'] ) ) );
	$pasAlMom = $temp->f ( 'pasarelaAlMom' );
	$comId = $temp->f ( 'idcomercio' );
	$usdxamex = $temp->f ( 'usdxamex' );
	// echo "pasAlMom = ".$pasAlMom."<br>";
	$paso = true;

// error_log("PASO1=$paso");

	//verifico que el comercio tenga habilitados los pagos en usd por amex
	if ($arrEnt ['amex'] == 1) {
		if ($arrEnt ['moneda'] != 978 && $arrEnt ['moneda'] != 840) return "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">No está permitido operaciones en esa divisa con tarjetas American Express</div>";

		if ($arrEnt ['moneda'] != 978 && $usdxamex == 0) {
			return "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">El comercio no tiene permitido operaciones en esa divisa con tarjetas American Express</div>";
		} elseif ($usdxamex == 2) {
			return "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">El comercio no tiene permitido operaciones con tarjetas American Express</div>";
		}
	}

	if ($arrEnt ['pasarela'] == '')
		$pasarela = 1;
	else {
		if (! strstr ( $pasAlMom, $arrEnt ['pasarela'] ) && $arrEnt ['comercio'] != 1) { // evito que los usuarios de varios comercios al pagar por uno de ellos usen una pasarela
			// que ese comercio no tenga
			$q = "select idPasarela from tbl_pasarela where secure = (select distinct secure from tbl_pasarela where idPasarela in ({$arrEnt ['pasarela']})) and idPasarela in ($pasAlMom) and activo = 1 limit 0,1";
			$temp->query ( $q );
			error_log($q);
			$pasarela = $temp->f ( 'idPasarela' );
		} else
			$pasarela = $arrEnt ['pasarela'];
	}

	//
	$q = "select * from tbl_pasarela where idPasarela in ($pasarela) and activo = 1";
	error_log($q);
	$temp->query ( $q );
	if ($temp->num_rows () == 0) $paso = false;
// error_log("PASO2=$paso");

	$secur = $temp->f ( 'secure' );

	if (($arrEnt ['moneda'] * 1) > 0) {
		if ($arrEnt ['eur'] != 1) {
			$query = "select * from tbl_colPasarMon where idmoneda = {$arrEnt ['moneda']} and idpasarela in ({$arrEnt ['pasarela']}) and estado = 1";
			$temp->query ( $query );
			if ($temp->num_rows () == 0) {
				$paso = false;
// error_log("PASO3=$paso");
				$alert = "La moneda definida no existe";
			}
		}
	} else {
		$paso = false;
// error_log("PASO4=$paso");
		$alert = "No se definió la moneda de la operación";
	}

	if ($palabra == 'lore') { // si el comercio no ha generado su palabra secreta se la genero
		$q = "update tbl_comercio set palabra = '" . suggestPassword ( 20 ) . "' where idcomercio = {$arrEnt ['comercio']}";
		$temp->query ( $q );
	}

	if (! $arrEnt ['trans']) {
		$trans = trIdent ( '', false );
	} else {
		if (! ($trans = $ent->isUrl ( $arrEnt ['trans'], 19 ))) {
			$paso = false;
// error_log("PASO5=$paso");
			$alert = _COMERCIO_CODEVALID;
		}
	}

		// Verifico que no se realice un pago del mismo cliente en menos de $tiemEsper
//		if ($arrEnt ['pago'] == 'S' && $paso == true && $secur == 0) {
//			$temp->query("select count(*) total from tbl_pasarela where idcenauto != 4 and idPasarela = '$pasarela' ");
//			if ($temp->f('total') > 0) {
//				$q = "select count(*) total from tbl_reserva where email = '{$correo}' and pasarela = '$pasarela' and fecha > " . (time () - 60 * 60 * $tiemEsper) . " and estado = 'A'";
//				$temp->query ( $q );
//				if ($temp->f ( 'total' ) > 0) {
//					$paso = false;
//					$alert = "Este mismo usuario ha realizado anteriormente un pago en menos de $tiemEsper hora, por favor espere un tiempo antes de intentar nuevamente";
//				}
//			}
//		}
// error_log("PASO=$paso");
		if ($paso == true) {

			$query = "select count(*) total from tbl_reserva where codigo = '$trans' and id_comercio = '{$comId}'";
			$temp->query ( $query );

			if ($temp->f ( 'total' ) == 0) {
				$query = "select count(*) total from tbl_transacciones where identificador = '$trans' and idcomercio = '{$comId}'";
				$temp->query ( $query );

				if ($temp->f ( 'total' ) == 0) {
					$ser = str_replace ( "\n", " ", str_replace ( ';', ',', htmlentities ( $arrEnt ['servicio'], ENT_QUOTES ) ) );

					$nmbr = htmlentities ( $arrEnt ['nombre'], ENT_QUOTES );
					if (strstr($pasarela, ',')) $pasarela = substr($pasarela, 0, strpos($pasarela, ','));

					//si es un cobro convertido a Euros
					if ($arrEnt['usd'] > 0 && $arrEnt['eur'] > 0) {
						if ($arrEnt ['moneda'] != '978') {
							$q = "select cambOperEuro from tbl_comercio where id = ".$arrEnt['comercio'];
							$temp->query($q);
							$euroCamb = $temp->f('cambOperEuro');//0- no pone las tasas para las oper sólo en Euros, 1- Si pone las tasas

							//Busca la tasa de cambio a aplicar
							if ($euroCamb == 1){//busca las tasas de cambio que debe haber puesto el comercio
								$q = "select t.tasa from tbl_tasaComercio t, tbl_moneda m where t.monedaBas = m.idmoneda and t.idcomercio = ".$arrEnt['comercio']." and monedaBas = ".$arrEnt['moneda']." and monedaCamb = 978 and t.fecha = (select max(fecha) from tbl_tasaComercio where t.idcomercio = ".$arrEnt['comercio']." )";
							} else {// trabaja con las tasas de cambio que ha puesto el BCC para
								$q = "select t.tasa from tbl_colCambBanco t, tbl_moneda m where t.idmoneda = m.idmoneda and t.idbanco = 26 and t.idmoneda = ".$arrEnt['moneda']." order by t.fecha desc";
							}
							$temp->query($q);
							$tasaApl = $temp->f('tasa');
error_log("tasa aplic=".$tasaApl);

							$q = "insert into tbl_operacEuro (identificador, idcomercio, idmoneda, tasa, monto) values ('$trans', '{$comId}', ".$arrEnt['moneda'].", '{$tasaApl}', ({$arrEnt ['usd']} * 100))";
							$temp->query($q);
							$error .= $temp->getErrorMsg ();

error_log(($arrEnt['usd']*100)."/".$tasaApl);

							//cambia los valores de entrada para que la operación salga en euros
							$arrEnt ['importe'] = intval($arrEnt['usd']*100/$tasaApl);
							$arrEnt ['importe'] = $arrEnt ['importe']/100;
							$arrEnt ['moneda'] = '978';
						} else {
							$arrEnt ['importe'] = intval($arrEnt['usd']);
						}
					} else {
						if ($arrEnt['usd'] > 0) {
							$arrEnt ['importe'] = ($arrEnt['usd']);
						}
					}

					$idlote = null;
					($arrEnt['pago'] == 'S') ? $pagoT = 'S' : $pagoT = 'N';
					if ($arrEnt['lote'] > 0){
						$pagoT = 'L';
						$idlote = $arrEnt['lote'];
					}

					if (!isset($arrEnt ['amex'])) {
						$arrEnt ['amex'] = 2;
					}

					$query = "insert into tbl_reserva (id_admin, id_comercio, est_comer, codigo, nombre, email, servicio, valor_inicial, moneda, fecha, pMomento, idioma, pasarela, tiempoV, url, amex, idlote) values ({$_SESSION['id']}, '{$comId}', '$estCom', '$trans', '{$nmbr}', '{$correo}', '{$ser}', '" . str_replace ( ",", ".", $arrEnt ['importe'] ) . "', '{$arrEnt ['moneda']}', $fechaNow, '$pagoT', '{$arrEnt ['idioma']}', $pasarela, {$arrEnt ['tiempo']}, '{$_SERVER["SERVER_NAME"]}', {$arrEnt ['amex']}, '$idlote')";
					error_log("InsertaOpercReserva->$query");
					$temp->query ( $query );
					$error = $temp->getErrorMsg ();

					if (strlen ( $error ) > 0) {
						$subject = "Error al insertar la invitación de pago";
						$mensaje = "SQL: " . $query . "<br>\nError: " . $error;
						$corr->todo ( 24, $subject, $mensaje );
						echo "<div style='text-align:center;color:red;'>" . _COMERCIO_ERROR_INVIT . "</div>";
					} else {
						if ($_SESSION ['codProdReserv']) {
							$query = "update tbl_productosReserv set codVenta = '$trans' where codigo = '{$_SESSION['codProdReserv']}'";
							$temp->query ( $query );
						}

						$query = "select moneda from tbl_moneda where idmoneda = {$arrEnt ['moneda']}";
						$temp->query ( $query );
						$moneda = $temp->f ( 'moneda' );

						if ($arrEnt ['pago'] == 'S') { // Pago al momento
							$adform = '';

							// revisa que la pasarela tenga fidelización de cliente
							$q = "select idcenauto from tbl_pasarela where idPasarela = '$pasarela'";
							$temp->query ( $q );
							if ($temp->f ( 'idcenauto' ) == 12) {
								$adform = "<input type='hidden' name='email' value='$correo'/>";
							}
							$importe = ($arrEnt ['importe'] * 100);
//error_log("$comId . $trans . $importe . ".$arrEnt ['moneda']." . 'P' . $palabra");
							$firma =convierte256( $comId, $trans, $importe, $arrEnt ['moneda'], 'P' );
							$form = "
                                <form name='envPago' method='post' action='" . _ESTA_URL . "/index.php'>
                                <input type='hidden' name='pasarela' value='{$pasarela}'/>
                                <input type='hidden' name='comercio' value='{$comId}'/>
                                <input type='hidden' name='transaccion' value='$trans'/>
                                <input type='hidden' name='importe' value='$importe'/>
                                <input type='hidden' name='moneda' value='{$arrEnt ['moneda']}'/>
                                <input type='hidden' name='operacion' value='P'/>
                                <input type='hidden' name='idioma' value='{$arrEnt ['idioma']}'/>
                                <input type='hidden' name='amex' value='{$arrEnt ['amex']}'/>
                                <input type='hidden' name='firma' value='$firma'/>
                                $adform
                                </form>
                                <script>document.envPago.submit();</script>
                                ";
							return $form;
						} else { // Pago por correo
							// Invitación de Pago que se envía al cliente y a mí

							include $docroot.'admin/lang/correo' . $arrEnt ['idioma'] . ".php";
							if ($arrEnt ['idioma'] == 'es')
								include $docroot."admin/lang/spanish.php";
							elseif ($arrEnt ['idioma'] == 'en')
								include $docroot."admin/lang/english.php";
							else
								include $docroot."admin/lang/italiano.php";

							$arrayTo = array ();
							$query = "select ";
							$message = "<style>.boton{background-color:#5EBEEF;color:white;display:block;border:2px solid navy;font-weight:bold;height:30px;" . "padding-top:5px;text-align:center;text-decoration:none;vertical-align:middle;width:90px;line-height:26px;" . "margin:0 auto;}</style>";
							// $query = "select ";
							if ($arrEnt ['idioma'] == 'en') {
								$query .= "correo_eng correo ";
								$iga = "clickhere.png";
								$subject = ' Invitation from ' . $comercioN . ' to make the payment through Ecommerce Administrator';
							} elseif ($arrEnt ['idioma'] == 'it') {
								$query .= "correo_eng correo ";
								$iga = "cliccaqui.png";
								$subject = ' Invito a ' . $comercioN . ' per effettuare il pagamento tramite Ecommerce Amministratore';
							} else {
								$iga = "clicaqui.png";
								$query .= "correo_esp correo ";
								$subject = ' Invitacion de ' . $comercioN . ' a realizar el pago a traves del Administrador de Comercios';
							}
							$query .= " from tbl_comercio where idcomercio = '{$comId}'";
							$temp->query ( $query );
							$adic = $temp->f ( 'correo' );
//							$message .= _INVITACION_DEPAGO . $adic;
							$message .= $adic;

							if ($arrEnt ['idioma'] == 'it') {
								$message = _INVITACION_DEPAGO;
							}


							$url = "<BR /><a class='boton' href='" . _ESTA_URL . "/pagoOnline.php?cod=$trans&com=" . $comId . "'>" . _CLICK_AQUI . "</a>";
							$urla = "<BR />\n" . _ESTA_URL . "/pagoOnline.php?cod=$trans&com=" . $comId;

							$message = str_replace ( '{importe}', number_format ( $arrEnt ['importe'], 2, '.', ' ' ) . ' ' . $moneda, $message );
							$message = str_replace ( '{servicio}', $arrEnt ['servicio'], $message );
							$message = str_replace ( '{comercio}', $comercioN, $message );
							$message = str_replace ( '{urla}', $urla, $message );
							$message = str_replace ( '{url}', $url, $message );
							$message = str_replace ( '{cliente}', $arrEnt ['nombre'], $message ); // Nombre completo del cliente a quién va dirigida la invitación
							$message = str_replace ( '{correo}', '<a href="mailto:' . $_SESSION ['email'] . '">' . $_SESSION ['email'] . '</a>', $message ); // correo de la persona que pone la invitación de pago
							$message = str_replace ( '{tiempo}', $arrEnt ['tiempo'], $message );

							if (strstr ( $correo, ";" )) $corrArr = explode ( ";", $correo );
							elseif (strstr ( $correo, "," )) $corrArr = explode ( ",", $correo );
							else $corrArr [0] = $correo;

							if (_MOS_CONFIG_DEBUG) {
								echo "correoArr=";
								print_r ( $corrArr );
								echo "<br>";
							}

							foreach ( $corrArr as $item ) {
								$arrayTo [] = array (
										$arrEnt ['nombre'],
										$item
								);
							}

							if (_MOS_CONFIG_DEBUG) {
								print_r ( $arrayTo );
								echo "<br>";
							}

							$est = true;
							foreach ( $arrayTo as $todale ) {
								if ($est) {
									$corr->to ( $todale [1] );
									$est = false;
								} else
									$corr->add_headers ( "Cc: " . $todale [1] );

								if (_MOS_CONFIG_DEBUG) echo "header = $headers<br>to = $to<br>mensaje = $message<br>";
							}
							$corr->from = 'tpv@administracomercios.com';
							$corr->reply = 'noreply@administracomercios.com';
							$corr->todo ( 23, $subject, $message );

							// Aviso de envío de la invitación de pago
							$arrayTo = array ();
							if ($correoMas == 1) {
								$q = "select nombre, email from tbl_admin where idcomercio = '{$comId}' and correoT = 1 and activo = 'S'";
								if (_MOS_CONFIG_DEBUG)
									echo $q . "<br>";
									$temp->query ( $q );
									$arrayTo = $temp->loadRowList ();
							}
							$arrayTo [] = array (
									$_SESSION ['admin_nom'],
									$_SESSION ['email']
							);
							if (_MOS_CONFIG_DEBUG) {
								echo "<br>**************************************************************<br>";
								print_r ( $arrayTo );
								echo "<br>**************************************************************<br>";
								print_r ( $_SESSION );
								echo "<br>**************************************************************<br>";
								echo "<br>";
							}

							$subject = _COMERCIO_EMAIL_SUBJECT;
							$message = _COMERCIO_EMAIL_MES;
							$q = "select nombre from tbl_pasarela where idPasarela = '$pasarela'";
							$temp->query ( $q );
							$pasa = $temp->f ( 'nombre' );
							$message = str_replace ( '{trans}', $trans, $message );
							$message = str_replace ( '{servicio}', $arrEnt ['servicio'], $message );
							$message = str_replace ( '{nombre}', $arrEnt ['nombre'], $message );
							$message = str_replace ( '{importe}', number_format ( $arrEnt ['importe'], 2, '.', ' ' ), $message );
							$message = str_replace ( '{moneda}', $moneda, $message );
							$message = str_replace ( '{pasarela}', $pasa, $message );

							$est = true;
							foreach ( $arrayTo as $todale ) {
								if ($est) {
									$corr->to ( $todale [1] );
									$est = false;
								} else
									$corr->add_headers ( "Cc: " . $todale [1] );
										
								if (_MOS_CONFIG_DEBUG) echo "header = $headers<br>to = ".$todale [1]."<br>mensaje = $message<br>";
							}
							$corr->todo ( 24, $subject, $message );
						}

					return "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">" . _COMERCIO_SOLC_SI . "</div>";
				}
			} else
				return "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">" . _COMERCIO_CODE_YA . "</div>";
		} else
			return "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">" . _COMERCIO_CODE_YA . "</div>";
	} else
		return "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">" . $alert . "</div>";
}

/**
 * Envía las transferencias tanto para comercio/transferencia.php como para template/tpv.php
 * @param array $arrEnt
 * @return string
 */
function envTransf($arrEnt) {
	global $temp;
	global $ent;
	$corr = new correo();
	$importMax = leeSetup('maxTransf');
	$corrreoMi = $firma = '';

// 		var_dump($_SERVER);
// 		var_dump($arrEnt);
	// 	exit;
	foreach ($arrEnt as $key => $value) {
		$corrreoMi .= "$key => $value\n";
	}

	$error = '';
	if (! $ent->isAlfanumerico ( $arrEnt['nombre'] )) $error .= "Cliente incorrecto";
	if (! $ent->isCorreo ( $arrEnt['email'] )) $error .= "Correo incorrecto";
	if (! $ent->isNumero ( $arrEnt['importe'] )) $error .= "Importe incorrecto";
	if (stristr ( $arrEnt['servicio'], 'cuba' ) !== false) $error .= "No debe aparecer la palabra Cuba";
	if (strlen($error) > 0) return "<script type='text/javascript'>alert('{$error} no se envi\u00f3 la transferencia')</script>";

	$comer = $arrEnt['comercio'];

	if ($comer == '139601511426') $importMax = 40000; //a solicitud de Ivett 20_01_21 por chat para cubatur VI

	$docroot = str_replace("admin/", "", $arrEnt['dir']);

	$query = "select * from tbl_comercio where idcomercio in ({$comer})";

	$temp->query($query);
	$comercioN = $temp->f('nombre');
	$estCom = $temp->f('estado');
	$datos = $temp->f('datos');
	$prefijo = $temp->f('prefijo_trans');
	$datos = $temp->f('datos');
	$idCom = $temp->f('id');
	$valMin = $temp->f('minTransf');
	foreach ($temp->loadAssocList() as $key => $value) {
		$corrreoMi .= "$key => $value\n";
	}

	if (strlen ( $temp->f ( 'fijo' ) ) > 0) {
		$fijo = $temp->f ( 'fijo' );
		$corrido = $temp->f ( 'corrido' );
		$etiqueta = $temp->f ( 'etiqueta' );

		$cuenta = strlen ( $corrido );
		$cantVal = $corrido + rand ( 1, 7 );
		if (strlen ( $cantVal ) == $cuenta ) $cuenta ++;

		$corrido = str_pad ( $cantVal, $cuenta, "0", STR_PAD_LEFT );
		$q = "update tbl_comercio set corrido = '$corrido' where idcomercio = $comer";
		$temp->query ( $q );

		$fijo = str_replace ( "%Y%", date ( "Y" ), str_replace ( "%y%", date ( "y" ), str_replace ( "%d%", date ( "d" ), str_replace ( "%m%", date ( "m" ), str_replace ( "%#%", $corrido, $fijo ) ) ) ) );
		$ante = $fijo . "\n<br>";
	}

	$arrEnt['servicio'] = $ante . $arrEnt['servicio'] . $etiqueta;

	$arrayTo = array ();
	if (strlen ( $error ) == 0) {
		// solo envía facturas si el comercio esta en producción
		if ($estCom == 'P') {

			// Calculo del ID de la operación
			$trans = trIdent ( $prefijo );

			$pasarela = damePasarA ( $idCom, $arrEnt['monedas'] );
			error_log("PASARELA TRANSFERENCIAS=".$pasarela);
			if ($pasarela == null || $pasarela == '' ) {
				return '<script language="JavaScript" type="text/javascript">alert("Hubo un error en la selecci\u00F3n de pasarela. Contactar con Bidaiondo");</script>';
				exit;
			}
			$query = "select cuenta, comercio from tbl_pasarela where idPasarela = $pasarela";
			$temp->query ( $query );

			if ($pasarela != 0) {
				if ($arrEnt['importe'] >= $valMin) {

					// Chequeo por el monto máximo
					$q = "select moneda from tbl_moneda where idmoneda = '".$arrEnt['monedas']."' and activo = 1";
					$temp->query($q);
					$mno = $temp->f('moneda');
					$tasamno = leeSetup($mno);
					if (($arrEnt['importe'] / $tasamno) <= $importMax)  {

						// $pasarela = 5;
						$cuenta = $temp->f ( 'cuenta' );
						$comercio = $temp->f ( 'comercio' );
						$pais = $arrEnt['pais'];
						$importe = $arrEnt['importe'] * 100;
						$compa = array (
								'000000',
								'00000',
								'0000',
								'999999',
								'99999',
								'9999',
								'99900',
								'999900'
						);
						for($i = 0; $i < count ( $compa ); $i ++) {
							if (stripos ( $importe, $compa [$i] ) > - 1) {
								return '<script language="JavaScript" type="text/javascript">alert("El importe entrado no procede");</script>';
								exit ();
							}
						}
						if (! $fijo) {
							$fijo = $trans;
							$corrido = '';
						}
						// inserta valores en la tabla de las transacciones
						$hora = time ();
						$query = "insert into tbl_transacciones	(idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor_inicial, tipoEntorno, moneda, estado, sesion, pasarela, idioma, tipoPago) values ('$trans', '$comer', '" . $fijo . "', 'T', $hora, $hora, '$importe', 'P', '{$arrEnt['monedas']}', 'P', '$firma', '$pasarela', '{$arrEnt['idioma']}', 'T')";
						// echo $query."<br>";
						$temp->query ( $query );

						$correo = str_replace ( " ", "", $arrEnt['email'] );

						// inserta los valores en la tabla de transferencias
						$query = "insert into tbl_transferencias (idTransf, cliente, cuentaB, idcomercio, idCom, facturaNum, email, fecha, fechaTransf, valor, moneda,
									concepto, idioma, idPasarela, idadmin) values
									('$trans', '{$arrEnt['nombre']}', '{$arrEnt['cuenta']}', '$comer', '$idCom', '" . $fijo . "', '{$correo}', '{$hora}',
									{$hora}, '$importe', '{$arrEnt['monedas']}', '{$arrEnt['servicio']}', '{$arrEnt['idioma']}', $pasarela, {$_SESSION['id']})";
						$temp->query ( $query );
						// echo $temp->_sql;

						$query = "select moneda from tbl_moneda where idmoneda = " . $arrEnt['monedas'];
						$temp->query ( $query );
						$moneda = $temp->f ( 'moneda' );

						// Envía por correo la factura al cliente y al comercio
						$arrayTo [] = array (
								$arrEnt['nombre'],
								$correo
						);
						$arrayTo [] = array (
								$_SESSION ['admin_nom'],
								$_SESSION ['email']
						);

						// Carga la factura y la prepara con los datos
	// 					$sFilename = $docroot.'admin/factura.ini';

	// 					if ($f = fopen ( $sFilename, "r" )) {
	// 						while ( ! feof ( $f ) )
	// 							$message .= fread ( $f, filesize ( $sFilename ) );
	// 							fclose ( $f );
	// 					} else return "<script type='text/javascript'>Ocurrio un error al cargar el fichero.</script>";

	// 					$image = $comer . ".jpg";

						include $docroot."admin/lang/correo{$arrEnt['idioma']}.php";
						if ($arrEnt['idioma'] == 'es')
							include_once $docroot."admin/lang/spanish.php";
						else
							include_once $docroot."admin/lang/english.php";

						$texto2 = '';

						$url = _ESTA_URL."/orden.php?op=".$trans;

						$message = str_replace ( '{url}', $url, _TBIO_TEXTCORREO);

	// 					$message = str_replace ( '{idfactura}', $trans, $message );
	// 					$message = str_replace ( '{clientenombre}', $arrEnt['nombre'], $message );
	// 					$message = str_replace ( '{servicio}', $arrEnt['servicio'], $message );
	// 					$message = str_replace ( '{serv}', _SERV, $message );
	// 					$message = str_replace ( '{val}', _VAL, $message );
	// 					$message = str_replace ( '{valor}', number_format ( ($importe / 100), 2 ) . ' ' . $moneda, $message );
	// 					$message = str_replace ( '{pagara}', $cuenta, $message );
	// 					$message = str_replace ( '{fecha}', date ( 'd/m/Y H:i', $hora ), $message );
	// 					$message = str_replace ( '{factura}', _FACTURA, $message );
	// 					$message = str_replace ( '{cliente}', _CLIENTE, $message );
	// 					$message = str_replace ( '{fechaHora}', _REPORTE_FECHAHORA, $message );
	// 					$message = str_replace ( '{nota}', _NOTA, $message );
	// 					$message = str_replace ( '{texto5}', _TEXTO5, $message );

						$subject = _TBIO_SUBJECT;

						// Hace el envío del correo
						$des = true;
						foreach ( $arrayTo as $todale ) {
							if ($des) {
								$corr->to ( $todale [1] );
								$des = false;
							} else
								$corr->add_headers ( "Cc: " . $todale [1] );
						}
						$corr->to ( _CORREO_SITE );
						// $corr->reply = $_SESSION['email'];
						$corr->from = 'tpv@administracomercios.com';
						$corr->reply = 'noreply@administracomercios.com';

						if (_MOS_CONFIG_DEBUG) echo ($message);

						$corr->todo ( 25, $subject, $message );
	// 					$corr->to ( _CORREO_SITE );
	// 					$corr->from = 'tpv@bidaiondo.com';
	// 					$corr->reply = 'noreply@bidaiondo.com';
	// 					$corr->todo ( 50, $subject . " " . $comercioN, $message );

						return "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">" . _COMERCIO_FACT_SI . "</div>";
					} else
						return "<script type='text/javascript'>alert('Sobrepasa el monto m\u00e1ximo permitido que es de ".($tasamno*$importMax)." para la moneda ".$mno."')</script>";
				} else
					return "<script type='text/javascript'>alert('El comercio no est\u00e1 autorizado para hacer transferencias menores de $valMin')</script>";
			} else
				return "<script type='text/javascript'>alert('El comercio no est\u00e1 autorizado para hacer transferencias')</script>";
		} else
			return "<script type='text/javascript'>alert('El comercio debe estar en producci\u00f3n para poder hacer transferencias')</script>";
	} else
		return "<script type='text/javascript'>alert('{$error} no se envi\u00f3 la transferencia')</script>";
}

/**
 * Envía las transferencias tanto para comercio/transferencia.php como para template/tpv.php
 * @param array $arrEnt
 * @return string
 */
function envTransfA($arrEnt) {
	global $temp;
	global $ent;
	$corr = new correo();
	$corrreoMi = $firma = '';

// 		var_dump($_SERVER);
// 		var_dump($arrEnt);
	// 	exit;
	foreach ($arrEnt as $key => $value) {
		$corrreoMi .= "$key => $value\n";
	}

	$error = '';
	if (! $ent->isAlfanumerico ( $arrEnt['nombre'] )) $error .= "Cliente incorrecto";
	if (! $ent->isCorreo ( $arrEnt['email'] )) $error .= "Correo incorrecto";
	if (! $ent->isNumero ( $arrEnt['importe'] )) $error .= "Importe incorrecto";
	if (stristr ( $arrEnt['servicio'], 'cuba' ) !== false) $error .= "No debe aparecer la palabra Cuba";
	if (strlen($error) > 0) return "<script type='text/javascript'>alert('{$error} no se envi\u00f3 la transferencia')</script>";

	$comer = $arrEnt['comercio'];
	$docroot = str_replace("admin/", "", $arrEnt['dir']);

	$query = "select * from tbl_comercio where idcomercio in ({$comer})";

	$temp->query($query);
	$comercioN = $temp->f('nombre');
	$estCom = $temp->f('estado');
	$datos = $temp->f('datos');
	$prefijo = $temp->f('prefijo_trans');
	$datos = $temp->f('datos');
	$idCom = $temp->f('id');
	$valMin = $temp->f('minTransf');
	foreach ($temp->loadAssocList() as $key => $value) {
		$corrreoMi .= "$key => $value\n";
	}

	if (strlen ( $temp->f ( 'fijo' ) ) > 0) {
		$fijo = $temp->f ( 'fijo' );
		$corrido = $temp->f ( 'corrido' );
		$etiqueta = $temp->f ( 'etiqueta' );

		$cuenta = strlen ( $corrido );
		$cantVal = $corrido + rand ( 1, 7 );
		if (strlen ( $cantVal ) == $cuenta ) $cuenta ++;

		$corrido = str_pad ( $cantVal, $cuenta, "0", STR_PAD_LEFT );
		$q = "update tbl_comercio set corrido = '$corrido' where idcomercio = $comer";
		$temp->query ( $q );

		$fijo = str_replace ( "%Y%", date ( "Y" ), str_replace ( "%y%", date ( "y" ), str_replace ( "%d%", date ( "d" ), str_replace ( "%m%", date ( "m" ), str_replace ( "%#%", $corrido, $fijo ) ) ) ) );
		$ante = $fijo . "\n<br>";
	}

	$arrEnt['servicio'] = $ante . $arrEnt['servicio'] . $etiqueta;

	$arrayTo = array ();
	if (strlen ( $error ) == 0) {
		// solo envía facturas si el comercio esta en producción
		if ($estCom == 'P') {

			// Calculo del ID de la operación
			$trans = trIdent ( $prefijo );

			if (
					$comer == '411691546810' // Soy Cubano
					|| $comer == '141624678529' // Bufete Internacional
			) {
				$pasarela = damePasar ( "47,48" ); // TransfAndBank y TransfBancaSabadell
			} elseif (
					$comer == '145813844929' // CIGB Camaguey
			) {
				$pasarela = "70"; // TransfIngDirect
			} elseif (
					$comer == '131886731156' // Adastravel
			) {
				$pasarela = damePasar ( "47,66,48", $arrEnt['monedas'] ); // TransfAndBank y TransfVallBank, BancaSabadellAndorra
			} elseif (
					$comer == '127066943003' // GESVIATUR (TopCuba)
					) {
						$pasarela = damePasar ( "47,66,48,74", $arrEnt['monedas'] ); // TransfAndBank y TransfVallBank, BancaSabadellAndorra, Triodos
			} elseif (
					$comer == '147145461846' // Servicios Médicos Docentes
					|| $comer == '132019689176' //Servicios Médicos
			) {
				$pasarela = damePasar ( "47,48", $arrEnt['monedas'] ); // TransfAndBank, TransfBancaSabadell
			} elseif (
					$comer == '139602115230' //Amistur
					|| $comer == '139333436635' //Gaviota Tours
					) {
						$pasarela = damePasar ( "54,55,62", $arrEnt['monedas'] ); // TransfIbercaja, TransfLabKutxa, TrasnfCaixaGeral
			} elseif (
					$comer == '123904543319' //Grupo Gira
					) {
						$pasarela = damePasar ( "55,54,62,69,74", $arrEnt['monedas'] ); // Laboral, Ibercaja, Caixa Geral y BancaMarch, Triodos
			} else {
				$pasarela = damePasar ( "54,55,62,69" ); // TransfIbercaja, TransfLabKutxa, TrasnfCaixaGeral y TranfBancaMarch
			}

			$query = "select cuenta, comercio from tbl_pasarela where idPasarela = $pasarela";
			$temp->query ( $query );

			if ($pasarela != 0) {
				if ($arrEnt['importe'] >= $valMin) {
					// $pasarela = 5;
					$cuenta = $temp->f ( 'cuenta' );
					$comercio = $temp->f ( 'comercio' );
					$pais = $arrEnt['pais'];
					$importe = $arrEnt['importe'] * 100;
					$compa = array (
							'000000',
							'00000',
							'0000',
							'999999',
							'99999',
							'9999',
							'99900',
							'999900'
					);
					for($i = 0; $i < count ( $compa ); $i ++) {
						if (stripos ( $importe, $compa [$i] ) > - 1) {
							return '<script language="JavaScript" type="text/javascript">alert("El importe entrado no procede");</script>';
							exit ();
						}
					}
					if (! $fijo) {
						$fijo = $trans;
						$corrido = '';
					}
					// inserta valores en la tabla de las transacciones
					$hora = time ();
					$query = "insert into tbl_transacciones	(idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor_inicial, tipoEntorno,
								moneda, estado, sesion, pasarela, idioma) values
								('$trans', '$comer', '" . $fijo . "', 'T', $hora, $hora, '$importe',
								'P', '{$arrEnt['monedas']}', 'P', '$firma', '$pasarela', '{$arrEnt['idioma']}')";
					// echo $query."<br>";
					$temp->query ( $query );

					$correo = str_replace ( " ", "", $arrEnt['email'] );

					// inserta los valores en la tabla de transferencias
					$query = "insert into tbl_transferencias (idTransf, cliente, cuentaB, idcomercio, idCom, facturaNum, email, fecha, fechaTransf, valor, moneda,
								concepto, idioma, idPasarela, idadmin) values
								('$trans', '{$arrEnt['nombre']}', '{$arrEnt['cuenta']}', '$comer', '$idCom', '" . $fijo . "', '{$correo}', '{$hora}',
								{$hora}, '$importe', '{$arrEnt['monedas']}', '{$arrEnt['servicio']}', '{$arrEnt['idioma']}', $pasarela, {$_SESSION['id']})";
					$temp->query ( $query );
					// echo $temp->_sql;

					$query = "select moneda from tbl_moneda where idmoneda = " . $arrEnt['monedas'];
					$temp->query ( $query );
					$moneda = $temp->f ( 'moneda' );

					// Envía por correo la factura al cliente y al comercio
					$arrayTo [] = array (
							$arrEnt['nombre'],
							$correo
					);
					$arrayTo [] = array (
							$_SESSION ['admin_nom'],
							$_SESSION ['email']
					);

					// Carga la factura y la prepara con los datos
// 					$sFilename = $docroot.'admin/factura.ini';

// 					if ($f = fopen ( $sFilename, "r" )) {
// 						while ( ! feof ( $f ) )
// 							$message .= fread ( $f, filesize ( $sFilename ) );
// 							fclose ( $f );
// 					} else return "<script type='text/javascript'>Ocurrio un error al cargar el fichero.</script>";

// 					$image = $comer . ".jpg";

					include $docroot."admin/lang/correo{$arrEnt['idioma']}.php";
					if ($arrEnt['idioma'] == 'es')
						include_once $docroot."admin/lang/spanish.php";
					else
						include_once $docroot."admin/lang/english.php";

					$texto2 = '';

					$url = _ESTA_URL."/orden.php?op=".$trans;

					$message = str_replace ( '{url}', $url, _TBIO_TEXTCORREO);

// 					$message = str_replace ( '{idfactura}', $trans, $message );
// 					$message = str_replace ( '{clientenombre}', $arrEnt['nombre'], $message );
// 					$message = str_replace ( '{servicio}', $arrEnt['servicio'], $message );
// 					$message = str_replace ( '{serv}', _SERV, $message );
// 					$message = str_replace ( '{val}', _VAL, $message );
// 					$message = str_replace ( '{valor}', number_format ( ($importe / 100), 2 ) . ' ' . $moneda, $message );
// 					$message = str_replace ( '{pagara}', $cuenta, $message );
// 					$message = str_replace ( '{fecha}', date ( 'd/m/Y H:i', $hora ), $message );
// 					$message = str_replace ( '{factura}', _FACTURA, $message );
// 					$message = str_replace ( '{cliente}', _CLIENTE, $message );
// 					$message = str_replace ( '{fechaHora}', _REPORTE_FECHAHORA, $message );
// 					$message = str_replace ( '{nota}', _NOTA, $message );
// 					$message = str_replace ( '{texto5}', _TEXTO5, $message );

					$subject = _TBIO_SUBJECT;

					// Hace el envío del correo
					$des = true;
					foreach ( $arrayTo as $todale ) {
						if ($des) {
							$corr->to ( $todale [1] );
							$des = false;
						} else
							$corr->add_headers ( "Cc: " . $todale [1] );
					}
					$corr->to ( _CORREO_SITE );
					// $corr->reply = $_SESSION['email'];
					$corr->from = 'tpv@bidaiondo.com';
					$corr->reply = 'noreply@bidaiondo.com';

					if (_MOS_CONFIG_DEBUG) echo ($message);

					$corr->todo ( 25, $subject, $message );
// 					$corr->to ( _CORREO_SITE );
// 					$corr->from = 'tpv@bidaiondo.com';
// 					$corr->reply = 'noreply@bidaiondo.com';
// 					$corr->todo ( 50, $subject . " " . $comercioN, $message );

					return "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">" . _COMERCIO_FACT_SI . "</div>";
				} else
					return "<script type='text/javascript'>alert('El comercio no est\u00e1 autorizado para hacer transferencias menores de $valMin')</script>";
			} else
				return "<script type='text/javascript'>alert('El comercio no est\u00e1 autorizado para hacer transferencias')</script>";
		} else
			return "<script type='text/javascript'>alert('El comercio debe estar en producci\u00f3n para poder hacer transferencias')</script>";
	} else
		return "<script type='text/javascript'>alert('{$error} no se envi\u00f3 la transferencia')</script>";
}


/**
 * Da la pasarela por la que van a transitar las transferencias
 * SIIIIIIIIIII vale
 * @param string $strPas
 * @param string $mon
 * @return string
 */
function damePasarA ($idcom, $mon = '840'){
	global $temp, $d;

	error_log("ENTRAAA");
	//saco las pasarelas asociadas al comercio
	$q = "select idpasarela from tbl_colPasarComTran where idcomercio = $idcom";
	$temp->query($q);
	$arrPas = $temp->loadResultArray();
	$strPas = implode(',', $arrPas);
	error_log("arrPas=".$strPas);

	if ($mon != '978') {//si la moneda no es euros quito TransfVallBank
		if (stripos($strPas, ',66')) $strPas = str_replace(',66', '', $strPas);
		if (stripos($strPas, '66,')) $strPas = str_replace('66,', '', $strPas);
	}

	//determina de las pasarelas del comercio la última por la que pasó una operación
	$q = "select idPasarela from tbl_transferencias where vista = 1 and idPasarela in ($strPas) order by fechaTransf desc limit 0,1";
	$temp->query($q);
	$pasarr = $temp->f('idPasarela');//pasarela de este comercio por la que pasó la última transferencia general
	error_log("UltimPAsarUsada=".$pasarr);

	//ultimas pasarelas usadas por el comercio
	$temp->query("select idPasarela from tbl_transferencias where idPasarela in ($strPas) and idCom = $idcom and vista = 1 order by fechaTransf desc limit 0,". count($arrPas));
	$arrPasUsed = $temp->loadResultArray();
	error_log("arrPasUsed=".implode(',', $arrPasUsed));

	//determina la diferencia entre las últimas pasarelas usadas por el comercio y
	//las que tiene asignadas para saber si hay alguna por la que no ha circulado
	// $arrDiff = array_merge(array_diff($arrPas, $arrPasUsed));
	// error_log("arrDiff=".implode(',', $arrDiff));
	// if (count($arrDiff) == 1){//si la diferencia es de una solamente
	// 	if ($arrDiff[0] != $pasarr) {	//si la pasarela diferente es distinta a la última usada
	// 		return $arrDiff[0];			//retorno la pasarela diferente
	// 	}
	// }
	error_log("arrPas=".implode(',',$arrPas));

	if ($pasarr == null) {
		$pasarr = $arrPas[0];
	}
	for ($i=0; $i<count($arrPas); $i++) {
		if ($arrPas[$i] == $pasarr) {
			if ($i == (count($arrPas))-1) {
				$pasarel = $arrPas[0];
				$cont = 0;
			}
			else {
				$pasarel = $arrPas[$i+1];
				$cont = $i;
			}
			error_log($pasarel);
			if ($pasarel == '66') {
				if ($mon == '978') return $pasarel;
				else {
					if ($cont == (count($arrPas))-1) return $arrPas[0];
					else return $arrPas[$cont+1];
				} 
			}
			if ($pasarel == '177' ) {
				if ($mon == '978') return $pasarel;
				else {
					if ($cont == (count($arrPas))-1) return $arrPas[0];
					else return $arrPas[$cont+1];
				} 
			}
			if ($pasarel == '200' ) {
				if ($mon == '978') return $pasarel;
				else {
					if ($cont == (count($arrPas))-1) return $arrPas[0];
					else return $arrPas[$cont+1];
				} 
			}
			//if ($pasarel == '55' && $mon == '840') $pasarel = '211';
			if ($pasarel == '55' && $mon == '840') {
					if ($cont == (count($arrPas))-1) return $arrPas[0];
					else return $arrPas[$cont+1];
			}
			if ($pasarel == '204' && $mon == '840') $pasarel = '212';
			error_log($pasarel);
			if (($pasarel == '66' || $pasarel == '177' || $pasarel == '200') && $mon != '978') {error_log("ERRROOOOR en pasarela - moneda");exit;}
			return $pasarel;
		}
	}

}


/**
 * Da la pasarela por la que van a transitar las transferencias
 * @param string $strPas
 * @param string $mon
 * @return string
 */
function damePasar ($strPas, $mon = '840'){
	global $temp, $d;

	$q = "select pasarela from tbl_transacciones where tipoOperacion = 'T' and pasarela in ($strPas) order by fechaTransf desc limit 0,1";
	$temp->query($q);
	$pasarr = $temp->f('pasarela');
	$arrPas = explode(",", $strPas);
	for ($i=0; $i<count($arrPas); $i++) {
		if ($arrPas[$i] == $pasarr) {
			if ($i == (count($arrPas))-1) {
				$pasarel = $arrPas[0];
				$cont = 0;
			}
			else {
				$pasarel = $arrPas[$i+1];
				$cont = $i;
			}
			if ($pasarel == 66 && $mon == '978') return $pasarel;
			elseif ($pasarel == 66){
				if ($cont == (count($arrPas))-1) return $arrPas[0];
				else return $arrPas[$cont+1];
			} else return $pasarel;
		}
	}

}

/**
 * Cambia los caracteres latinos
 */
function cambiaCart($texto) {
	// return html_entity_decode($texto, ENT_QUOTES | ENT_HTML401, "UTF-8");
	return str_replace("Ã", "&iacute;",
				str_replace("é", "&eacute;",
				str_replace("í", "&iacute;",
				str_replace("ó", "&oacute;",
				str_replace("ú", "&uacute;",
				str_replace("ñ", "&ntilde;",
				str_replace("Ã¡", "&aacute;",
				str_replace("Ã©", "&eacute;",
				str_replace("Ã³", "&oacute;",
				str_replace("Ã±", "&ntilde;", str_replace("á", "&aacute;", $texto)))))))))));
}

function token($token = '') {
	return hash_hmac('sha256', session_id(), 'b527bae683562a289d2f247aa937ca6704327b7f55e1cebc66664206b83417aaa56c116baa24084c6ab0d17742a594a9154101daf3bdb5c7364bf12b94de2240');
}

/** IIIIIIIIIIIIMMMMMMMMMMMMMMMPPPPPPPPPPPPPPPPOOOOOOOOOOOOOOORRRRRRRRRRTTTTTTTTTTTAAAAAAAAAANNNNNNNNNTTTTTTTTTEEEEEEEEE
 * Envia los datos para la Confirmación de Orden
 * y Cancelación de Orden
 * @param array $datos
 * @param string $accion
 * @return string
 */
function datATitanes ($datos, $accion, $pasar = 37) {
// 	global $correoMi;
	$temp = new ps_DB();

	$correoM = "Entra en datATitanes\n<br>";
	$url = "https://www.grupotitanes.es:8555/";

	$clave = titToken(91);
	if ($pasar == 37) {
		$llave = 'b527bae683562a289d2f247aa937ca6704327b7f55e1cebc66664206b83417aaa56c116baa24084c6ab0d17742a594a9154101daf3bdb5c7364bf12b94de2240';
		$url .= "APITitanes/";
	} elseif ($pasar == 91) {
		$llave = 'b527bae683562a289d2f247aa937ca6704327b7f55e1cebc66664206b83417aaa56c116baa24084c6ab0d17742a594a9154101daf3bdb5c7364bf12b94de2240';
		$url .= "APITitanes/";
//		$url .= "APIDesarrollo/";
	} elseif ($pasar == 191)  {
//		$url .= "APIDesarrollo2/";
		$url .= "APITitanes/";
	}

//	$pago[1] = substr($pago[1], 0, strpos($pago[1], '.')+3);
	$correoM .= "<br>pasarela=".$pasar;
	$correoM .= "<br>accion=".$accion;
	$correoM .= "<br>datos=".json_encode($datos);
	$correoM .= "<br>token=".$clave."<br>";

	if ($accion == 'P') {// Confirmaciones de operaciones
		$url .= 'Order/'.$datos['OrdenId'].'/Paid';
		$correoM .= "Signature=sha512 . {$datos['Signature']} . $llave <br>";
		$datos['Signature'] = strtoupper(hash("sha512", $datos['Signature'].$llave));
		$title = "Confirmación de operación AisRemesas";
		unset($datos['OrdenId']);
		$method = 'POST';
//		$data = array(
//				"BeneficiaryId"		=> $pago[0],
//				"AmountToReceive"	=> $pago[1],
//				"PaymentDate"		=> $pago[2],
//				"DocumentNumber"	=> $pago[3],
//				"Signature"			=> strtoupper(hash("sha512", $pago[4].$pago[0].$pago[1].$llave))
//		);

	} elseif ($accion == 'D') { // Devoluciones - Cancelación de operaciones
		$url .= 'Order/'.$datos['id'].'/Cancel';
		$title = 'Devolución de operación AisRemesas';
		$datos['Signature'] = hash("sha512", $datos['Signature'].$llave);
		$method = 'POST';

	} elseif ($accion == 'C') { // Insertar Cliente
		$url .= "Customer";
		$correoM .= "Signature=sha512 . {$datos['Signature']} . $llave <br>";
		$datos['Signature'] = hash("sha512", $datos['Signature'].$llave);
		$title = 'Inscripción de Cliente AisRemesas';
		$method = 'POST';

	} elseif ($accion == 'U') {// Update Cliente
		$url .= "Customer/".$datos['IdTitanes'];
		$correoM .= "Signature=sha512 . {$datos['Signature']} . $llave <br>";
		$datos['Signature'] = hash("sha512", $datos['Signature'].$llave);
		$title = 'Actualización de Cliente AisRemesas';
		unset($datos['IdTitanes']);
		$method = 'PUT';

	} elseif ($accion == 'B') {// Insertar Beneficiario
		$url .= "Customer/".$datos['ClientId']."/Beneficiary";
		$correoM .= "Signature=sha512 . {$datos['Signature']} . $llave <br>";
		$datos['Signature'] = hash("sha512", $datos['Signature'].$llave);
		$title = 'Inscripción de Beneficiario AisRemesas';
		unset($datos['ClientId']);
		unset($datos['BeneficiaryId']);
		$method = 'POST';

	} elseif ($accion == 'F') {// Update Beneficiario
		$url .= "Customer/".$datos['ClientId']."/Beneficiary/".$datos['BeneficiaryId'];
		$correoM .= "Signature=sha512 . {$datos['Signature']} . $llave <br>";
		$datos['Signature'] = hash("sha512", $datos['Signature'].$llave);
		$title = 'Actualización de Beneficiario AisRemesas';
		unset($datos['ClientId']);
		unset($datos['BeneficiaryId']);
		$method = 'PUT';

	} elseif ($accion == 'E') { // Get Beneficiario obtener los datos del beneficiario
		$url .= "Customer/" . $datos['ClientId'];
		$title = 'Verifica si existe el Cliente';
		unset($datos['ClientId']);
		$method = 'GET';

	} elseif ($accion == 'G') {// Get Beneficiario obtener los datos del beneficiario
		$url .= "Customer/".$datos['ClientId']."/Beneficiary/".$datos['BeneficiaryId'];
		$title = 'Obtención de los datos del Beneficiario';
		unset($datos['ClientId']);
		unset($datos['BeneficiaryId']);
		$method = 'GET';

	} else if ($accion == 'R') {
		$url .= "Customer/".$datos['ClientId']."/Beneficiary/".$datos['BeneficiaryId'];
		$title = 'Verificación de relación Cliente - Beneficiario AisRemesas';
		unset($datos['ClientId']);
		$method = 'GET';

	} else if ($accion == 'J') {
		$url .= "Customer/".$datos['customerId']."/Beneficiary/".$datos['beneficiaryId'];
		$title = 'Fijando relación Cliente - Beneficiario AisRemesas';
		$method = 'POST';

	} else if ($accion == 'O') {
		$url .= "Order";
		$title = 'Inscribe Orden AisRemesas';
		$datos['Signature'] = hash("sha512", $datos['Signature'].$llave);
		$method = 'POST';

	} else if ($accion == 'N') {
		$url .= "T086/PayCode";
		$title = 'Confirma el abono de la Orden al Beneficiario AisRemesas';
		$method = 'POST';

	} else if ($accion == 'V') {
		$url .= "T086/Transaction/".$datos['idtransaccion']."/StatusCode";
		$title = "Verificación si la operación ".$datos['idtransaccion']." tiene el id de Titanes";
		$method = 'GET';
	}

error_log("title= $title");
	$json = json_encode($datos);
	$correoM .= "json=$json<br>";
	$correoM .= "url=$url<br>";
	$correoM .= "method=$method<br>";
error_log("json=$json");
	foreach ($datos as $key => $value) {
		$correoM .=  $key." = ".$value."<br>\n";
	}

	$options = array(
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_SSL_VERIFYPEER	=> false,
			CURLOPT_SSL_VERIFYHOST  => false,
			CURLOPT_POST			=> true,
			CURLOPT_VERBOSE			=> true,
			CURLOPT_URL				=> $url,
			CURLOPT_POSTFIELDS		=> $json,
			CURLOPT_CUSTOMREQUEST	=> $method,
			CURLOPT_HTTPHEADER		=> array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($json),
					'Authorization: Bearer '.$clave
			)
	);

	error_log("opciones: ".json_encode($options));
	error_log("HttpHeader: ".json_encode(array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($json),
					'Authorization: Bearer '.$clave
			)));

	$correoM .= "
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_SSL_VERIFYPEER	=> false,
				CURLOPT_SSL_VERIFYHOST	=> false,
				CURLOPT_POST			=> true,
				CURLOPT_VERBOSE			=> true,
				CURLOPT_CUSTOMREQUEST	=> $method,
				CURLOPT_URL				=> ".$url.",
				CURLOPT_POSTFIELDS		=> ".($json)."
				CURLOPT_HTTPHEADER		=> ".array('Content-Type: application/json', 'Content-Length: ' .
								strlen($json), "Authorization: Bearer ".$clave);
	$ch = curl_init();
	curl_setopt_array($ch , $options);

// 		if (_MOS_CONFIG_DEBUG != 1)
	$output = curl_exec($ch);
// 		else
// 			$output = '{"Id":4844778,"StatusCode":4,"Status":"Paid","Description":"Order has been paid to the beneficiary."}';

//	if (curl_errno($ch)) $correoM .=  "Error en la comunicación al comercio:".curl_strerror(curl_errno($ch))."<br>\n";
	$crlerror = curl_error($ch);
	// 						echo "otroerror=".$crlerror;
	if ($crlerror) {
		$correoM .=  "La comunicación al comercio ha dado error:".$crlerror."<br>\n";
	}
	$curl_info = curl_getinfo($ch);
	curl_close($ch);

	if ($accion == 'P') {
		if (stripos($output, "Id") > 0) {
			$arrSal = json_decode($output,true);
			foreach ($arrSal as $key => $value) {
				$correoM .=  $key." = ".$value."<br>\n";
			}
			$q = "update tbl_aisOrden set subida = 1 where titOrdenId = '".$arrSal['Id']."'";
			$correoM .= $q."<br>\n";
			$temp->query($q);
		}
	}


	foreach ($curl_info as $key => $value) {
		$correoM .=  $key." = ".$value."<br>\n";
	}
	$correoM .=  "respuCurl=$output||<br><br><br>\n";

	error_log($correoM);
	$temp->query("insert into tbl_traza (titulo, traza, fecha) values ('$title', '".str_replace("'", "", str_replace('"', "", $correoM))."', ". time() .")");

	if ($accion == 'D' && !curl_errno($ch)) return $output;
	else return $output;

	return $correoM;
}

function regCoxData() {
	global $temp;
	global $ip;
    global $user_agent;
	$temp->query("insert into tbl_adminSO (idadmin, fecha, so, browser, ip, idpais, userAgent, TZ, DST, nonDST) values ('".$_SESSION['id']."', '".time()."', '".getOS()."', '".getBrowser()."', '$ip', '".damepais($ip)."', '$user_agent', '".$_SESSION['TZ']."', '".$_SESSION['DST']."', '".$_SESSION['nonDST']."')");

}

/**
 * Busca el país desde donde se está haciendo la operación
 *
 * @return string
 */
function damepais($ip) {
	global $temp;
	$idpais = 1960;

	//$temp->query("insert into debug (valor, fecha) values ('" . geoip_country_code3_by_name ( $ip ) . "', unix_timestamp())");
	if ($ip == '127.0.0.1') return 55;

	if (function_exists ( geoip_country_code3_by_name )) {
		if (strlen ( geoip_country_code3_by_name ( $ip ) ) > 0) {
			$temp->query ( "select id from tbl_paises where iso = '" . geoip_country_code3_by_name ( $ip ) . "'" );
			$idpais = $temp->f ( 'id' );

			if ($temp->num_rows () === 0) {
				$accN = $temp->query ( "insert into tbl_paises (nombre, iso) values ('" . geoip_country_name_by_name ( $ip ) . "', '" . geoip_country_code3_by_name ( $ip ) . "')" );
				if ($accN === false)
					return FALSE;

				$accN = $temp->query ( "select id from tbl_paises where iso = '" . geoip_country_code3_by_name ( $ip ) . "'" );
				if ($accN === false)
					return FALSE;
				$idpais = $this->temp->f ( 'id' );
			} else return $idpais;
		} else return "2001";
	} else return "2000";
	return $idpais;
}

/**
 * Detecta el browser del usuario
 *
 * @return string
 */
function getBrowser() {

    global $user_agent;

    $browser        = "Unknown Browser";

    $browser_array = array(
                            '/msie/i'      => 'Internet Explorer',
                            '/firefox/i'   => 'Firefox',
                            '/safari/i'    => 'Safari',
                            '/trident/i'   => 'Internet Explorer 11',
                            '/chrome/i'    => 'Chrome',
                            '/edge/i'      => 'Edge',
                            '/opera/i'     => 'Opera',
                            '/netscape/i'  => 'Netscape',
                            '/maxthon/i'   => 'Maxthon',
                            '/konqueror/i' => 'Konqueror',
                            '/mobile/i'    => 'Handheld Browser'
                     );

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $browser = $value;

    return $browser;
}

/**
 * Detecta el SO usado por los usuarios
 *
 * @return string
 */
function getOS() {

	global $user_agent;
	error_log("user_agent: ".$user_agent);

    $os_platform  = "Unknown OS Platform";

    $os_array     = array(
                          '/windows nt 10/i'      =>  'Windows 10',
                          '/windows nt 6.3/i'     =>  'Windows 8.1',
                          '/windows nt 6.2/i'     =>  'Windows 8',
                          '/windows nt 6.1/i'     =>  'Windows 7',
                          '/windows nt 6.0/i'     =>  'Windows Vista',
                          '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                          '/windows nt 5.1/i'     =>  'Windows XP',
                          '/windows xp/i'         =>  'Windows XP',
                          '/windows nt 5.0/i'     =>  'Windows 2000',
                          '/windows me/i'         =>  'Windows ME',
                          '/win98/i'              =>  'Windows 98',
                          '/win95/i'              =>  'Windows 95',
                          '/win16/i'              =>  'Windows 3.11',
                          '/macintosh|mac os x/i' =>  'Mac OS X',
                          '/mac_powerpc/i'        =>  'Mac OS 9',
                          '/linux/i'              =>  'Linux',
                          '/ubuntu/i'             =>  'Ubuntu',
                          '/iphone/i'             =>  'iPhone',
                          '/ipod/i'               =>  'iPod',
                          '/ipad/i'               =>  'iPad',
                          '/android/i'            =>  'Android',
                          '/blackberry/i'         =>  'BlackBerry',
                          '/webos/i'              =>  'Mobile'
                    );

    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

	return $os_platform;
}

/**
 * Verifica que el código de desbloque de IP sea el correcto
 *
 * @param [type] $micod
 * @param [type] $ip
 * @return void
 */
function vermiCod($micod, $ip, $email, $usr = null) {
	global $temp;
	$corCreo = new correo();
	error_log ("$micod, $ip, $email, $usr");
	error_log("select count(*) total from tbl_admin where ident = '".md5($ip.$email.$micod)."'");
	$temp->query("select count(*) total from tbl_admin where ident = '".md5($ip.$email.$micod)."'");
	error_log($temp->f('total'));
	if ($temp->f('total') > 0) {
		error_log("delete from tbl_ipBL where ip = '$ip'");
		$temp->query("delete from tbl_ipBL where ip = '$ip'");
		$temp->query("select count(*) total from tbl_ipblancas where ip = '$ip'");

		if ($temp->f('total') == 0) $temp->query("insert into tbl_ipblancas (ip, fecha, idAdmin, idComercio) values ('$ip', '".time()."', '10', '1')");
		else $temp->query("update tbl_ipblancas set fecha = ".time()." where ip = '$ip'");

		if (strlen($usr) > 3) {
			$contras = validaContrasena($usr);
			$temp->query(sprintf("update tbl_admin set activo = 'S', md5 = '".$contras[1]."' WHERE email='%s' and login = '%s'", $email, $usr));

			$imprim = '<div style=\"text-align:center; font-family:Arial san-serif; font-size:11px\">A usted se le ha concedido el acceso a la
			administración del Concentrador. Puede entrar con los siguientes datos:<br>
			<br>Usuario: '.$usr.'<br>Contraseña: '.$contras[0].'</div>';
			$subject = 'Cambio de contraseña por solicitud!';
			$corCreo->to($email);
			$corCreo->todo(19, $subject, $imprim);
		}
		return true;
	} else return false;
}

/**
 * Verifica que el correo enviado desde verifUser.php sea el correcto
 *
 * @param [type] $email
 * @return void
 */
function verificacCorreo($email, $ip){
	global $temp;
	$corCreo = new correo();

	$temp->query("select count(*) total from tbl_admin where email = '$email'");

	if ($temp->f('total') > 0) {
		$code = rand(100000,999999);
		error_log ($ip." / ".$code. " / ".$email. " / ".md5($ip.$email.$code));
		$temp->query("update tbl_admin set ident = '".md5($ip.$email.$code)."'  where email = '$email'");
		$corCreo->to($email);
		$corCreo->todo(11, 'Administracomercios Code','<span style="font-size:14px;">Code: '.$code.'</span>');
		return 'ok';
	}
	return 'ko';
}

/**
 * Busca el iso2 del país en base al id de la tabla pais
 * @param integer $cod
 */
function paisCod($cod) {
	global $temp;
	$q = "select iso2 from tbl_paises where id = ".$cod;
	$temp->query($q);
	return $temp->f('iso2');
}

/**
 * Crea el voucher de la operación de Titanes lo salva a pdf y lo envía
 * @param string $idtran
 * @return boolean
 */
function creatitVou($idtran){
	$TCPDF = new TCPDF();
	$temp = new ps_DB();

	//saco los datos de la operación y el cliente
	$q = "select concat(c.nombre, ' ', c.papellido, ' ', c.sapellido) cliente, c.idtitanes, concat(b.nombre, ' ', b.papellido, ' ', b.sapellido) beneficiario, t.identificador id, from_unixtime(t.fecha_mod, '%d/%m/%Y') fec, t.valor_inicial, m.moneda, o.recibe, o.comision, o.titOrdenId, o.envia  from tbl_moneda m, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t where t.moneda = m.idmoneda and o.idtransaccion = t.idtransaccion and o.idtransaccion = '$idtran' and o.idcliente = c.id and o.idbeneficiario = b.id";
	$temp->query($q);
	$cliente 		= ucfirst($temp->f('cliente'));
	$beneficiario 	= ucfirst($temp->f('beneficiario'));
	$id 			= $temp->f('id');
	$fec 			= $temp->f('fec');
	$moneda 		= $temp->f('moneda');
	$val 			= number_format($temp->f('valor_inicial') / 100,2)." ".$moneda;
	$recibe 		= number_format($temp->f('recibe') / 100,2)." CUC";
	$comision 		= number_format($temp->f('comision') / 100,2)." ".$moneda;
	$idtitanes 		= $temp->f('idtitanes');
	$ordtit 		= $temp->f('titOrdenId');
	$tasa			= number_format($temp->f('recibe')/$temp->f('envia'),4);

	$arrIdio = array(
			array('Id','Id')
			, array('Fecha','Date')
			, array('Cliente','Sender')
			, array('Beneficiario','Beneficiary')
			, array('Total a pagar','Total amount to pay')
			, array('Beneficiario recibe','Beneficiary receives')
			, array(utf8_encode('Comisión'),'Fee amount')
			, array('Comprobante de pago','Voucher')
			, array('Detalles de la Transacción','Transaction details')
			, array('Orden','Order')
			, array('Tasa','Rate')
	);
	$idio = 0; //para usar el idioma español

	// Extend the TCPDF class to create custom Header and Footer
	class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			// Logo
			$image_file = K_PATH_IMAGES.'aisLogo.jpg';
			$this->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			// Set font
			$this->SetFont('helvetica', 'B', 20);
			// Title
			$this->Cell(90, 15, 'AISRemesas '.$arrIdio[7][$idio], 10, false, 'C', 0, '', 0, false, 'M', 'M');
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetCreator('');
	$pdf->SetAuthor('AISRemesas');
	$pdf->SetTitle('AISRemesas Voucher');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
	$pdf->setFooterData();

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// ---------------------------------------------------------

	// set default font subsetting mode
	$pdf->setFontSubsetting(true);
	$pdf->SetFont('dejavusans', '', 10, '', true);

	// Add a page
	// This method has several options, check the source code documentation for more information.
	$pdf->AddPage();
	$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
	$pdf->SetFont('helvetica', 'B', 14);
	$pdf->Ln(15);
	$pdf->Write(0, utf8_encode($arrIdio[8][$idio]), '', 0, '', true, 0, false, false, 0);
	$pdf->SetFont('helvetica', '', 11);

	$pdf->SetLineWidth(0);

	trigger_error($q."\n".$arrIdio[0][$idio].':'.$id."\n".$arrIdio[9][$idio].':'.$ordtit."\n".$arrIdio[1][$idio].':'.$fec."\n".$arrIdio[2][$idio].':'.$cliente
					."\n".$arrIdio[3][$idio].':'.$beneficiario."\n".$arrIdio[4][$idio].':'.$val." ".$moneda."\n".$arrIdio[5][$idio].':'.$recibe.' CUC'
					."\n".$arrIdio[6][$idio].':'.$comision, E_USER_WARNING);
	$pdf->Ln(5);
	$border = array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
	$pdf->Cell(90, 0, $arrIdio[0][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $id, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[9][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $ordtit, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[1][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $fec, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[2][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $cliente, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[3][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $beneficiario, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[4][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $val, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[5][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $recibe, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[6][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $comision, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[10][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $tasa, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->SetFont('helvetica', 'I', 10);
	$pdf->Ln(10);
	$sitiene = utf8_encode('Si tiene alguna pregunta acerca de la entrega o el seguimiento de su transacción, por favor póngase en contacto con nosotros en:');
	$pdf->Write(0, $sitiene, '', 0, '', true, 0, false, false, 0);
	$pdf->Ln(5);
	$pdf->writeHTML('Email: <a href="mailto:info@aisremesascuba.com">info@aisremesascuba.com</a>', true, false, true, false, '');

	// ---------------------------------------------------------

// 	$file_url = $_SERVER['DOCUMENT_ROOT'].'desc/';
	$file_url = str_replace("rep", "desc", _PATH_SITIO);
// 	$file_url = _PATH_SITIO."desc";
// trigger_error($file_url, E_USER_WARNING);
	$file = 'transac'.$idtran.".pdf";
	$pdf->Output($file_url.$file, 'F');

	$eol = "\r\n"; //default line-break for mime type
	$BOUNDARY = md5(time()); //random boundaryid, is a separator for each param on my post curl function
	$BODY=""; //init my curl body
	$BODY.= '--'.$BOUNDARY. $eol; //start param header
	$BODY .= 'Content-Disposition: form-data; name="uploadType"' . $eol ; // last Content with 2 $eol, in this case is only 1 content.
	$BODY .= 'Content-Type: text/html' . $eol. $eol;
	$BODY .= "7" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data

	$BODY.= '--'.$BOUNDARY. $eol; // start 2nd param,
	$BODY.= 'Content-Disposition: form-data; name="uploadFile1"; filename="'.$file.'"'. $eol ; //first Content data for post file, remember you only put 1 when you are going to add more Contents, and 2 on the last, to close the Content Instance
	$BODY.= 'Content-Type: '. mime_content_type($file_url.$file) . $eol; //Same before row
	$BODY.= 'Content-Transfer-Encoding: base64' . $eol . $eol; // we put the last Content and 2 $eol,
	$BODY.= file_get_contents($file_url.$file) . $eol; // we write the Base64 File Content and the $eol to finish the data,

	$BODY.= '--'.$BOUNDARY .'--' . $eol. $eol; // we close the param and the post width "--" and 2 $eol at the end of our boundary header.
	return fichTitan($BODY, $BOUNDARY, $idtitanes);
}

function cambiaTilJS($teto){
	error_log($teto);
	return str_replace('ñ', '\u00f1', str_replace('Ñ', '\u00d1', str_replace('Ú', '\u00da', str_replace('Ó', '\u00d3', str_replace('Í', '\u00cd', str_replace('É', '\u00c9', str_replace('Á', '\u00c1', str_replace('ú', '\u00fa', str_replace('ó', '\u00f3', str_replace('í', '\u00ed', str_replace('é', '\u00e9', str_replace('á', '\u00e1', $teto))))))))))));
}

/**
 * Envía los ficheros a Titanes
 * @param string $BODY
 * @param string $BOUNDARY
 * @param string $titId
 * @return boolean
 */
function fichTitan($BODY, $BOUNDARY, $titId = null){
	global $correoMi;
	$temp = new ps_DB();
	$correoMi .= "Entra en ficTitan\n<br>";
	$corrreoMi .= "boundary=".$BOUNDARY."<br>";
	//$corrreoMi .= "body=".$BODY."<br>";

	$q = "select estado from tbl_pasarela where idPasarela = 91";
	$temp->query($q);
	if ($temp->f('estado') == 'D') $ul = "https://195.57.91.186:8555/APITest/Customer/$titId/Upload";
	else $ul = "https://www.grupotitanes.es:8555/APITitanes/Customer/$titId/Upload";

	if ($titId) $url = $ul;

	$ch = curl_init(); //init curl
	$options = array(
			CURLOPT_USERAGENT			=> 'Mozilla/1.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0'
			,CURLOPT_URL 				=> $url
			,CURLOPT_HTTPHEADER			=> array(
					"Content-Type: multipart/form-data; boundary=".$BOUNDARY //setting our mime type for make it work on $_FILE variable
					,"Authorization: Bearer ".titToken()
			)
//			,CURLOPT_SSL_VERIFYHOST		=> false
//			,CURLOPT_SSL_VERIFYPEER		=> false
			,CURLOPT_RETURNTRANSFER		=> true
			,CURLOPT_BINARYTRANSFER		=> true
			,CURLOPT_FOLLOWLOCATION		=> false
			,CURLOPT_POST				=> true
			,CURLOPT_POSTFIELDS			=> $BODY
	);
	$correoMi .= "<br>".json_encode($options)."<br>";
	foreach ($options as $key => $value) {
		if ($key != '10015')
			$correoMi .= "$key => $value\n<br>";
	}
	curl_setopt_array($ch, $options);

	$sale = curl_exec($ch);

	foreach ($sale as $key => $value) {
		if ($key != '10015')
			$correoMi .= "$key => $value\n<br>";
	}
	//$correoMi .= $sale."\n<br>";
	$crlerror = curl_error($ch);
	if (strlen($crlerror)) $correoMi .= "\n<br>Error devuelto por Titanes=".$crlerror." con el cliente idtitanes: $titId\n<br>";

	// echo $correoMi;
	if (strstr($sale, '"Status":200')) {
		ini_set('safe_mode', $smod);
		$correoMi .= "TODO OK subidos los ficheros\n<br>";
		// $correoMi .= $BODY;
		// enviaError("Todo superbien en la subida de ficheros AIS $usuario. $file, $sale $crlerror, $correoMi");
		return true;

		// 		enviaError("Todo bien en la subida de ficheros AIS $usuario, $file, $sale $crlerror");
	} else {
		$curl_info = curl_getinfo($ch);
		trigger_error("Error AIS en la subida de ficheros a Titanes ".$sale, E_USER_WARNING);
		// if (stripos($sale, "Code:201") > )
		$sale .= str_replace("{", "", str_replace("}", "", str_replace("[", "", str_replace("]", "", str_replace(":", "", $sale)))));
		foreach ($curl_info as $key => $value) {
			$correoMi .=  $key." = ".$value."<br>\n";
		}
		$sale = "Error subiendo ficheros de Ais con el clientes idtitanes: $titId\n<br>". $sale ." -- ". $crlerror."\n<br> ** ";
		if (stripos($sale,"Code201SSS") > -1) {
			$q = "select id, nombre, papellido, sapellido, usuario, idcimex, fnacimiento, numDocumento, fechaDocumento, correo, telf1, (select iso2 from tbl_paises where id = paisResidencia) presi, (select iso2 from tbl_paises where id = paisNacimiento) pnac, ciudad, direccion, CP, case sexo when 2 then 1 else 0 end sex, ocupacion from tbl_aisCliente where idtitanes = $titId";
			$temp->query($q);
			$idese = $temp->f('id');
			$clnombre = $temp->f('nombre');
			$clpapellido = $temp->f('papellido');
			$clsapellido = $temp->f('sapellido');
			$clusuario = $temp->f('usuario');
			$clidcimex = $temp->f('idcimex');
			$clfnacimiento = date('d/m/Y',$temp->f('fnacimiento'));
			$clnumDocumento = $temp->f('numDocumento');
			$clfechaDocumento = date('d/m/Y', $temp->f('fechaDocumento'));
			$clcorreo = $temp->f('correo');
			$cltelf1 = $temp->f('telf1');
			$clpresi = $temp->f('presi');
			$clpnac = $temp->f('pnac');
			$clciudad = $temp->f('ciudad');
			$cldireccion = $temp->f('direccion');
			$clCP = $temp->f('CP');
			$clsex = $temp->f('sex');
			$clocupacion = $temp->f('ocupacion');

			$sale .= "$q<br> El id es $idese<br>";

			$q = "update tbl_aisCliente set idtitanes = null where id = $idese";

			$sale .= "$q<br>";
		}
		// $sale = "El errores es $crlerror\n<br>Marcas=".strpos($sale, 'Code:101')."\n<br>".$sale;
		// if (strpos($sale, 'Code:101')) {
		// 	$temp->query("insert into tbl_traza (titulo,traza,fecha) values ('Error subiendo ficheros de AIS','".  str_replace('"', '', str_replace("'", "", $sale)) ."',".time().")");
		// } else
		enviaError(str_replace('"', '', str_replace("'", "", $sale)));
		return false;
	}
}

/**
 * Verifica que el token de Titanes esté actualizado de no ser así solicita uno nuevo
 * @return string valor del token
 */
function titToken($pas=37){
error_log("entra en token con pasarela=".$pas);
	$temp = new ps_DB();
	$correoMi = '';

	$q = "select estado from tbl_pasarela where idPasarela = $pas";
	$temp->query($q);
	if ($pas == 37) {
		$pa = "mafpass98765";
		$ur = "https://www.grupotitanes.es:8555/APITitanes/Token";
		$sr = urlencode('info@amfglobalitems.com');
		$titoken = "tiToken";
	} elseif($pas == 91) {
		$pa = "mafpass98765";
		$ur = "https://www.grupotitanes.es:8555/APITitanes/Token";
		$sr = urlencode('info@amfglobalitems.com');
		$titoken = "tiToken";
//		$pa = "t!Ad08KbC";
//		$ur = "https://www.grupotitanes.es:8555/APIDesarrollo/Token";
//		$sr = urlencode('prueba@tefpay.com');
//		$titoken = "tiToken91";
	}
	$correoMi .=  "pass=$pa\n<br>";
error_log("pa=".$pa);
error_log("ur=".$ur);
error_log("sr=".$sr);

	$q = "select fecha from tbl_setup where nombre = '$titoken'";
	$temp->query($q);
	if (time()-(60*60) > $temp->f('fecha')) {
error_log("entra a buscar el token nuevo");
		//buscando el token si la fecha del último es menor de 24 horas
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Content-Type: application/json;charset=UTF-8";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: "; // browsers keep this blank.

		$options = array(
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_SSL_VERIFYHOST	=> false,
				CURLOPT_SSL_VERIFYPEER	=> false,
				CURLOPT_POST			=> true,
				CURLOPT_VERBOSE			=> true,
				CURLOPT_FRESH_CONNECT 	=> true,
				CURLOPT_URL				=> $ur,
				CURLOPT_POSTFIELDS		=> "grant_type=password&username=$sr&password=$pa",
				CURLOPT_HEADER			=> false,
				CURLOPT_HTTPHEADER		=> $header,
				CURLOPT_RETURNTRANSFER	=> 1,
				CURLOPT_FOLLOWLOCATION	=> 1,
				CURLOPT_CONNECTTIMEOUT	=> 100,
				CURLOPT_TIMEOUT			=> 100,
				CURLOPT_USERAGENT		=> "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.54"
		);

		$ch = curl_init();
		curl_setopt_array($ch , $options);
		$salida = curl_exec($ch);

		if (curl_errno($ch)) {
//			error_log("Error en la resp de Titanes: ".curl_strerror(curl_errno($ch)));
			$correoMi .=  trigger_error("Error en la resp de Titanes: ".(curl_errno($ch)));
			enviaError("Error en la resp de Titanes para el Token: ".(curl_errno($ch)));
			$crlerror = curl_error($ch);
		}

		$curl_info = curl_getinfo($ch);
		curl_close($ch);

error_log("Respuesta de Titanes para actualizar el Token".$salida);
		$arrCurl = json_decode($salida);

		actSetup($arrCurl->access_token, $titoken);
	}
	return leeSetup($titoken);
}

/**
 * Formatea los números en dependencia del formato escogido por el usuario
 * @param number $num número a formatear
 * @return string número formateado
 */
function formatea_numero($num,$mon=true) {
	if($mon)
		return number_format($num, $_SESSION['cantdec'], convertir_especiales_html($_SESSION['sepdecim']),
			convertir_especiales_html($_SESSION['sepmiles']));
	else
		return number_format($num, 0, convertir_especiales_html($_SESSION['sepdecim']),	convertir_especiales_html($_SESSION['sepmiles']));
}

/**
 * Formatea la fecha en dependencia del formato del usuario
 * @param number $fecha en unix timestamp
 * @return string fecha formateada
 */
function formatea_fecha($fecha) {
	return date($_SESSION['formtfecha'], $fecha);
}

/**
 * Genera los identificadores de transaccion para el concentrador y para los comercios
 * @param boolean $tipo true genera para tbl_transacciones
 * @param boolean $rand true adiciona el random al final de la cadena
 * @param string $comr identificador de las operaciones para el comercio
 * @return string
 */
function trIdent ($comr, $tipo=true, $rand=true) {
	$temp = new ps_DB();
    $salida = 1;
    $cant = 8;
    $timeZone = date_default_timezone_get();
    date_default_timezone_set('Europe/Berlin');
    while ($salida > 0) {
        $idTran = (string)(date("mdHi"));
        if ($rand) { //se pide generar el último par de valores
            $idTran .= (string)(rand (10, 99));
            $can = 10;
        }

		date_default_timezone_set($timeZone);
		if ($comr) $idTran = date ('y').$idTran; //se le agregan los dos primeros lugares si se envían (identificador del comercio)

        $query = "select count(*) total from ";
        ($tipo) ? $query .= "tbl_transacciones where idtransaccion = '$idTran'":
                $query .= "tbl_reserva where codigo = '$idTran'";
//        echo $query."<br>";
        $temp->query($query);
        $salida = $temp->f('total');
//        echo $salida."<br>";
    }
    return $idTran;
}

/**
 * Completar una cadena de largo $cant con el caracter $letr
 * @param string $cad cadena a completar
 * @param int $cant cantidad de lugares total
 * @param string $letr letra para completar
 * @param string $dir l o r lado hacia el que se pone el completamiento
 * @return string cadena formada
 */
function complLargo($cad,$cant=10,$letr='0',$dir='l') {
	$num = strlen($cad);$sale='';
	if ($num < ($cant)) {
		for ($i=0;$i<($cant-$num);$i++){
			$sale .= $letr;
		}
		($dir == 'l') ? $sale = $sale.$cad : $sale = $cad.$sale;
	}
	return $sale;
}

/**
 * Obtiene la información para construir los accesos
 * directos en base a las costumbres de los clientes
 */
function Accesos() {
	$temp = new ps_DB();
	//Accesos directos por defecto para el usuario que aún no tenga las costumbres almacenadas
	$accArr = array(
			array("url" => 'index.php?componente=comercio&pag=reporte', "texto" => _MENU_ADMIN_TRANSACCIONES,
					"parentid" => 4, "id" => 16),
			array("url" => 'index.php?componente=comercio&pag=comparacion', "texto" => _MENU_ADMIN_COMPARACION,
					"parentid" => 4, "id" => 19),
			array("url" => 'index.php?componente=core&pag=personal', "texto" => _MENU_ADMIN_PERSONALES,
					"parentid" => 1, "id" => 7),
			array("url" => 'index.php?componente=ticket&pag=ticketin', "texto" => _MENU_ADMIN_INSTICKET,
					"parentid" => 6, "id" => 23),
			array("url" => 'index.php?componente=comercio&pag=cliente', "texto" => _MENU_ADMIN_PAGO,
					"parentid" => 4, "id" => 25)
	);
	$q = "select m.id, m.link, title, parentid from tbl_cantAccesos c, tbl_menu m where m.id = c.idmenu and idadmin = "
			. $_SESSION['id'] ." and m.id not in (12) order by c.cant desc limit 0,5";
//	 echo $q;
	$temp->query($q);
	$menu3 = $temp->loadObjectList();
	$j = 0;
	$acdArr = array();
	//	print_r($accArr);
	//	print_r($menu3);
	for ($i = 0; $i < count($menu3); $i++) {
		if ($menu3[$i]->link) {
			$url = $menu3[$i]->link;
			$tex = constant($menu3[$i]->title);
			$par = $menu3[$i]->parentid;
			$id = $menu3[$i]->id;
		} else {
			$url = $accArr[$j]['url'];
			$tex = $accArr[$j]['texto'];
			$par = $accArr[$j]['parentid'];
			$id = $accArr[$j]['id'];
			$j++;
		}
		$acdArr[$i] = array("url" => $url, "txt" => $tex, "pid" => $par, "id" => $id);
	}
	return $acdArr;
}

/**
 * Función que construye la página llamada
 */
function hotel_puts($val=null) {
	$temp = new ps_DB();
	global $ent;
    global $posc;
    $posc = $val;
//echo "entra";
	if (strlen($_GET['componente']) > 1) {
		if (($comp = $ent->isAlfabeto($_GET['componente'], 13)) && ($pag = $ent->isAlfabeto($_GET['pag'], 13))) {
			$camino_busc = "componente/".$comp."/". $pag .".php";

			//actualiza la tabla cantAccesos
			$query = "select count(*) tot from tbl_cantAccesos c, tbl_menu m where idadmin = ".$_SESSION['id']
						. " and m.id = c.idmenu and link like '%componente=".$comp."&pag=". $pag."%'" ;
			$temp->query($query);
			//				echo $query."<br>";

			if ($temp->f('tot') > 0) $query = "update tbl_cantAccesos c, tbl_menu m set cant = cant + 1, fecha = ".time().
						" where idadmin = ".$_SESSION['id']." and m.id = c.idmenu and link like '%componente="
							.$comp."&pag=". $pag."%'" ;
			else $query = "insert into tbl_cantAccesos values (null, ".$_SESSION['id'].", (select id from tbl_menu "
						. " where link like '%componente=".$comp."&pag=". $pag."%'), 1, ".time().")";

			$temp->query($query);

			include_once ($camino_busc);
		}
	} else {
		include_once ("componente/comercio/inicio.php");
	}
}

/* creates a compressed zip file, ejemplo de uso:
 *  $files_to_zip = array(
	'preload-images/1.jpg',
	'preload-images/2.jpg',
	'preload-images/5.jpg',
	'kwicks/ringo.gif',
	'rod.jpg',
	'reddit.gif'
);
if true, good; if false, zip creation failed
$result = create_zip($files_to_zip,'my-archive.zip');
 */
function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,$file);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

		//close the zip -- done!
		$zip->close();

		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

function leeFicheros($fichero) {
	 if (is_file($fichero)) {
		 if ($stream = fopen($fichero, 'r')) return stream_get_contents($stream);
	 }
	 return false;
 }

function leeFic($fich){
	if (is_file($fich)) {
		$buffer = "";
		$handle = fopen($fich, "r");
		while (($buffer .= fgets($handle, 4096)) !== false) {
			$yo='';
		}
		if (!feof($handle)) {
			echo "Error: unexpected fgets() fail\n";
		} return $buffer;
    fclose($handle);
		$contents = fread($handle, filesize($fich));
		fclose($handle);
		return $contents;
	} return FALSE;
}

function leeFichero($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}

/**
 * Función para enviar imágenes incrustadas en los correos
 * @param string $to
 * @param string $subject
 * @param string $encabezado
 * @param string $contenido
 * @param string $file Fichero de la Imágen
 * @param string $from
 * @return boolean
 */
function correoImag($to, $subject, $encabezado, $contenido, $file, $from) {

//	error_reporting(E_ALL);
//	define('DISPLAY_XPM4_ERRORS', true);
	require_once 'classes/MIME.php';

	$id = MIME::unique();
	$text = MIME::message('Text version of message.', 'text/plain');
	$contenido = str_replace('{id}', "cid:".$id, $contenido);
	$html = MIME::message($contenido,'text/html');
	if (is_file("logos/".$file)) {
		$at[] = MIME::message(file_get_contents("logos/".$file), FUNC::mime_type("logos/".$file), "logos/".$file, null, 'base64', 'inline', $id);
		$mess = MIME::compose($text, $html, $at);
		//echo $mess['header'];
		//echo "<br>dsfasdf".$from;
		$send = mail($to, $subject, $mess['content'], 'From: '.$from."\n".$mess['header']);

		return $send;
	}
	return false;
}


/**
 * Funciï¿½n para insertar las cantidades de productos en la tabla productosCant
 * @param <type> $prod
 * @param <type> $fecha1
 * @param <type> $fecha2
 * @param <type> $cant
 */
function insertaCantidad($prod, $fecha1, $fecha2, $cant) {
	$temp = new ps_DB();
	$ini = new ps_DB();
	$ini2 = new ps_DB();

	if ($_SESSION['id']) {
		$usuario = $_SESSION['id'];
	} else {
		$query = "select idadmin from tbl_productos p, tbl_admin a where a.idcomercio = p.idCom and idrol = 11 and "
				. " p.id = $prod limit 0,1";
//echo "$query<br>";
		$temp->query($query);
		$usuario = $temp->f('idadmin');
	}

	//	Chequeo si ya existe precio para ese intervalo y dï¿½ la posiciï¿½n
	$query = "select id from tbl_productosCant where idProd = $prod and fechaFin >= $fecha1 or fechaIni <= $fecha2";
	$temp->query($query);

	if ($temp->num_rows() != 0) { //si hay interferencias de cantidades anteriores

//echo'		Chequea y borra todos los cantidades que quedan dentro del intervalo de fechas<br>';
		$query = "select id from tbl_productosCant where idProd = $prod and fechaIni >= $fecha1 and fechaFin <= $fecha2";
		$temp->query($query);
		if ($temp->num_rows() > 0) {
//echo"		Borra todos los cantidades contenidos dentro del intervalo de fechas del nuevo precio<br>";
			$arraProd = implode(",", $temp->loadResultArray());
			$query = "delete from tbl_productosCant where id in ($arraProd)";
			$temp->query($query);
		}

//echo		"Chequea si existe un precio con un intervalo de fechas que contenga las fechas del nuevo precio, si es asï¿½ debe picarlo en 2<br>";
		$query = "select * from tbl_productosCant where idProd = $prod and fechaIni <= $fecha1 and fechaFin >= $fecha2";
		$temp->query($query);
		for ($i=0;$i<$temp->num_rows();$i++) {

//echo			"Inserta el primer pedazo por delante del nuevo a insertar<br>";
			$arrVals = $temp->loadRow();
			$query = "insert into tbl_productosCant (id, idProd, idAdm, cant, fecha, fechaIni, fechaFin)
						values (null, {$arrVals[1]}, {$arrVals[2]}, {$arrVals[3]}, {$arrVals[4]}, {$arrVals[5]}, "
							. ($fecha1-86400).")";
			$temp->query($query);

//echo			"Modifica el grande existente para que sea el segundo pedazo por detrï¿½s del nuevo<br>";
			$query = "update tbl_productosCant set fechaIni = ".($fecha2+86400)." where id = {$arrVals[0]}";
			$temp->query($query);
		}

//echo		"Chequea si hay interferencia por delante<br>";
		$query = "select id from tbl_productosCant where idProd = $prod and fechaIni > $fecha1 and fechaFin > $fecha2 "
				. " and fechaIni <= $fecha2";
		$temp->query($query);
		for ($i=0;$i<$temp->num_rows();$i++) {
//echo			"Modifica el precio existente<br>";
			$query = "update tbl_productosCant set fechaIni = ".($fecha2+86400)." where id = ".$temp->f('id');
			$temp->query($query);
		}

//echo		"Chequea si hay interferencia por detrï¿½s<br>";
		$query = "select id from tbl_productosCant where idProd = $prod and fechaIni < $fecha1 and fechaFin < $fecha2 "
				. " and fechaFin >= $fecha1";
		$temp->query($query);
		for ($i=0;$i<$temp->num_rows();$i++) {
//echo			"Modifica el precio existente<br>";
			$query = "update tbl_productosCant set fechaFin = ".($fecha1-86400)." where id = ".$temp->f('id');
			$temp->query($query);
		}
	}

//echo	"Por ï¿½ltimo, inserta el nuevo precio<br>";
	$query = "insert into tbl_productosCant  (id, idProd, idAdm, cant, fecha, fechaIni, fechaFin) values (null, $prod, "
			. " $usuario, $cant, unix_timestamp(), $fecha1, $fecha2)";
//echo $query."<br>";
	$temp->query($query);
//echo	"y borra los cantidades con las fechas invertidas<br>";
	$query = "delete from tbl_productosCant where fechaFin < fechaIni";
	$temp->query($query);
//echo	"borra las entradas con cant = 0 <br>";
	$query = "delete from tbl_productosCant where cant = 0";
	$temp->query($query);

//echo 'Une todas las entradas del mismo producto con la misma cant que estï¿½n contï¿½guas<br>';
	$query = "select id, idProd, fechaIni, fechaFin, cant from tbl_productosCant order by idProd, fechaIni";
//	echo "$query<br>";
	$temp->query($query);

	while ($temp->next_record()) {
		$id = $temp->f('id');
		$idProd = $temp->f('idProd');
		$fecha2 = $temp->f('fechaFin');
		$cant = $temp->f('cant');

		while (true) {
			$query = "select id, idProd, fechaIni, fechaFin, cant from tbl_productosCant
						where cant = $cant and fechaIni = ".($fecha2 + 86400)." and idProd = $idProd and id != $id "
							. " order by idProd, fechaIni";
//			echo "$query<br>";
			$ini->query($query);
//			echo $ini->num_rows()." ";
			if ($ini->num_rows() == 0) break;

			$query = "update tbl_productosCant set fechaFin = ".$ini->f('fechaFin') ." where id = $id";
//			echo "$query<br>";
			$ini2->query($query);
			$fecha2 = $ini->f('fechaFin');

			$query = "delete from tbl_productosCant where id = ".$ini->f('id');
//			echo "$query<br>";
			$ini2->query($query);
			break;
		}
	}
	return true;
}

function validaContrasena($usuario) {
	$temp = new ps_DB();
	$salida = false;
	while (!$salida) {
		$contras = suggestPassword(16);
		$calc_md5 = sha1($usuario.$contras.'Lo que los hace hermoso es algo invisible...los ojos no siempre ven. Hay que buscar con el corazón.');
		$query = "select count(*) total from tbl_admin where md5 = '$calc_md5'";
		$temp->query($query);
		if ($temp->f('total') == 0) $salida = true;
	}
//echo "usuario=$usuario<br>";
//echo "contras=$contras<br>";
//echo "calc_md5=$calc_md5<br>";

	return array($contras,$calc_md5);
}

/*
 * Lee de la tabla setup los valores que le piden
 */
function leeSetup($variab) {
	$temp = new ps_DB();
	$query = "select valor from tbl_setup where nombre = '$variab'";
	$temp->query( $query );
	return $temp->f('valor');
}

/**
 * Hace el update de la tabla Setup con los valores enviados
 * @param string $valor
 * @param string $nombre
 * @return boolean
 */
function actSetup($valor, $nombre) {
	$temp = new ps_DB();
	$query = "update tbl_setup set valor = '$valor', fecha = ".time()." where nombre = '$nombre'";
	return $temp->query( $query );
}

//genera un codigo de 20 caracteres
//retorna un array con dos elementos
//el primero de 11 caracteres de largo
//el segundo de 18
function generaCod() {
	list($usec, $sec) = explode(" ", microtime());
	return (array ((microtime(true)*100), str_replace('0.', '', $sec.$usec)));
}

function generaCodEmp() {
    $eti = str_replace('.', '', (string)microtime(false));
	return (time().substr($eti, 1, 2));
}

//borra de las tablas de los idiomas los titulos y descripciones
function borra_idioma($clave) {
	$temp = new ps_DB();
	$sel_idioma = "select titulo from tbl_idioma order by ididioma";
	$temp->query($sel_idioma);
	$j = 0;

	while ($temp->next_record()){
		$nombre_idioma[++$j] = $temp->f('titulo');
	}

	for ($num = 0; $num < count($clave); $num++) {
		for ($x=1; $x<=count($nombre_idioma); $x++) {
			$idiomas = "delete from tbl_idioma_".strtolower($nombre_idioma[$x])." where ididioma = ".$clave[$num];
			$temp->query($idiomas);

			$idiomas = "delete from tbl_idioma_".strtolower($nombre_idioma[$x])." where ididioma = ".$clave[$num];
			$temp->query($idiomas);
		}
	}
}

//borra subdirectorios y todo su contenido
function borra_dir($dir) {
	if (false !== ($handle = opendir($dir))) {
		$files = glob($dir .'*.{???}', GLOB_BRACE);
		for ($x=0; $x<count($files); $x++) unlink($files[$x]);
	}
    closedir($handle);
	rmdir($dir);
}

//retorna un float convertido en moneda
function moneda($num) {
	return number_format($num, 2, '.', ' ');
}

//returns safe code for preloading in the RTE
function RTESafe($strText) {

	$tmpString = '';

	$tmpString = trim($strText);

	//convert all types of single quotes
	$tmpString = str_replace(chr(145), chr(39), $tmpString);
	$tmpString = str_replace(chr(146), chr(39), $tmpString);
	$tmpString = str_replace("'", "&#39;", $tmpString);

	//convert all types of double quotes"
	$tmpString = str_replace(chr(147), chr(34), $tmpString);
	$tmpString = str_replace(chr(148), chr(34), $tmpString);
//	$tmpString = replace($tmpString, """", "\""")

	//replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ", $tmpString);
	$tmpString = str_replace(chr(13), " ", $tmpString);

	return $tmpString;
}

//genera pares de options en base a una query
//las columnas en la query pasada deben tener como alias
//id y nombre
function opciones_sel_Arr($select, $id = null) {
	$cadena_orden = '';
//print_r($id);
	$temp = new ps_DB();
	$temp->query($select);
	while ($temp->next_record()){
//		echo $temp->f('id').", $id";
//		echo (array_search($temp->f('id'), $id));
		if (is_array($id) && array_search($temp->f('id'), $id) !== false )
			$cadena_orden .= '<option selected value="'.$temp->f('id').'">'.$temp->f('nombre').'</option>'."\n";
		else
			$cadena_orden .= '<option value="'.$temp->f('id').'">'.$temp->f('nombre').'</option>'."\n";
	}
	return $cadena_orden;
}


//genera pares de options en base a una query
//las columnas en la query pasada deben tener como alias
//id y nombre
function opciones_sel($select, $id = null) {
	$cadena_orden = '';

	$temp = new ps_DB();
	$temp->query($select);
	while ($temp->next_record()){
		if ($temp->f('id') == $id)
			$cadena_orden .= '<option selected value="'.$temp->f('id').'">'.$temp->f('nombre').'</option>'."\n";
		else
			$cadena_orden .= '<option value="'.$temp->f('id').'">'.$temp->f('nombre').'</option>'."\n";
	}
	return $cadena_orden;
}

//genera pares de options en base a arrays pasados
function opciones_arr ($valores_arr, $val_sel) {
	$cadena_orden = '';
	for ($x = 0; $x < count($valores_arr); $x++) {
		if ($valores_arr[$x][0] == $val_sel)
			$cadena_orden .= '<option selected="true" value="'.$valores_arr[$x][0].'">'.$valores_arr[$x][1].'</option>'."\n";
		else
			$cadena_orden .= '<option value="'.$valores_arr[$x][0].'">'.$valores_arr[$x][1].'</option>'."\n";
	}
	return $cadena_orden;
}


//genera pares de options en base a dos n&uacute;meros
function opciones($inicio, $final, $id) {
	$cadena_orden = '';
	for ($x=$inicio; $x<=$final; $x++){
		if ($x == $id) $cadena_orden .= '<option selected value="'.$x.'">'.$x.'</option>'."\n";
		else $cadena_orden .= '<option value="'.$x.'">'.$x.'</option>'."\n";
	}
	return $cadena_orden;
}


//Hace el login al sitio de administraci&oacute;n
function verifica_entrada($login, $contras){
	// echo $login."-".$contras."<br>";
	$calc_md52 = sha1($login.$contras.'Lo que los hace hermoso es algo invisible...los ojos no siempre ven. Hay que buscar con el corazón.');
	// echo("LOGIN->".$calc_md52);
	if (stripos(_ESTA_URL, 'localhost') > 0) {
		if (_MOS_CONFIG_DEBUG) error_log("md5=".$calc_md52);
	}
	$temp = new ps_DB();
	$Session = new SecureSession;
	$pase = false;

	$q = "select idadmin, a.nombre, a.idrol, r.orden, a.idcomercio, a.email, ip, usequery, formatFecha, cantDec, "
			. " separMiles, separDecim, case a.idcomercio when 'todos' then a.idcomercio else (select pasarelaAlMom "
			. " from tbl_comercio c where c.idcomercio = a.idcomercio) end pasarelaAlMom, case a.idcomercio "
			. " when 'todos' then 'todos' else (select nombre from tbl_comercio where idcomercio = a.idcomercio) end comercio, "
			. " a.ventacuc, a.reclamaciones from tbl_admin a, tbl_roles r where a.idrol = r.idrol and a.activo = 'S' and md5 in ('$calc_md52')";
	$temp->query($q);
	error_log($q);
	error_log($temp->getErrorMsg());
	$arrAdm = $temp->loadAssocList();
	foreach ($arrAdm as $value) {
		$pase = true;
		$q = "select idComerc from tbl_colAdminComer where idAdmin = ".$value['idadmin'];
		$temp->query($q);
		$idcomStr = implode(",",$temp->loadResultArray());

		if ($value['ip'] == 'inicio') {

			$ip = str_replace('inicio', '', $value['ip']);
			$query = "update tbl_admin set ip = '".$_SERVER['REMOTE_ADDR']."' where idadmin = ".$value['idadmin'];
			$temp->query($query);

		} else { if ($value['idrol'] <= 100 || stripos($value['ip'], $_SERVER['REMOTE_ADDR']) !== false ) $pase = true; }
		if ($pase) {
			$arrTZ = explode("|",$_COOKIE['TZ']);
			$Session->SetFingerPrint(60);
			$_SESSION['LoggedIn'] = true;
			$_SESSION['id'] = $value['idadmin'];
			$_SESSION['admin_nom'] = utf8_decode($value['nombre']);
			$_SESSION['comercio'] = $value['idcomercio'];//poner esto obsoleto, empezar a usar idcomStr
			$_SESSION['comercioNom'] = $value['comercio'];
			$_SESSION['idcomStr'] = $idcomStr;
			$_SESSION['rol'] = $value['idrol'];
			$_SESSION['grupo_rol'] = $value['orden'];
			$_SESSION['email'] = $value['email'];
			$_SESSION['pasarelaAlMom'] = $value['pasarelaAlMom'];
			$_SESSION['sesionId'] = session_id();
			$_SESSION['usequery'] = $value['usequery'];
			$_SESSION['cantdec'] = $value['cantDec'];
			$_SESSION['sepdecim'] = $value['separDecim'];
			$_SESSION['sepmiles'] = $value['separMiles'];
			$_SESSION['formtfecha'] = $value['formatFecha'];
			$_SESSION['vendecuc'] = $value['ventacuc'];
			$_SESSION['reclamaciones'] = $value['reclamaciones'];
			$_SESSION['TZ'] = $arrTZ[0];
			$_SESSION['DST'] = $arrTZ[1];
			$_SESSION['nonDST'] = $arrTZ[2];
			$query = "update tbl_admin set fecha_visita = ".time().", ident = '".$_SESSION['sesionId']."' where idadmin = ".$_SESSION['id'];
			$temp->query($query);

			return 'pag=inicio';
		}
	}
	return false;
}

function tablaM( $tabla_ancho, $idioma='E', $vista, $orden='', $busquedaInicio='', $colEsp='', $busqueda='', $columnas) {
	/*
	vista - Vista de la BD contra la cual se realizar&aacute; todo el trabajo
	orden -  ordenamiento de los valores de salida de la vista por default
	busquedaInicio - Columnas de la vista a buscar por defult
	colEsp - array de array para las columnas especiales (editar, ver, borrar..) en la forma (tipocolumna, textoalt, caminoimagen, titulocolumna)
	busqueda - array de array para las busquedas en la forma (nombrecampo, columnatabla)
	columnas - array de array con los datos de (titulocolumna, campotabla, anchocolumna, posicion)
	*/

	//$idioma = 'I';

	$temp = new ps_DB();

	$ancho = '';
	if ($_SESSION['idioma'] == "english") {
		$alerta = "You are about to delete a record and all the data asociated with.\\nAre you sure?";
		$alerta2 = "Are you sure to cancel the transaction?\\nThis operation can`t be reverted.";
		$alerta3 = "The transaction must be Accepted or Refunded and or has a value bigger than 0.";
		$alerta4 = "The transacction will revert it`s status of paid to the commerce.";
		$alerta5 = "The transacction will not been requested for refund.";
	} else {
		$alerta = "Se borrar\u00e1 este registro y todos los datos asociados a \u00e9l.\\nEst\u00e1 seguro?";
		$alerta2 = "Est\u00e1 usted seguro que la transacci\u00f3n es una reclamaci\u00f3n?\\nEsta operaci\u00f3n no puede ser revertida.";
		$alerta3 = "La transacci\u00f3n debe ser Aceptada y tener valor mayor que 0.";
		$alerta4 = "La transacci\u00f3n revertir\u00e1 su estado de pagada al comercio.";
		$alerta5 = "La transacci\u00f3n no debe estar solicitada para devolver.";
	}

	$conColumn = 1;
	for ($i = 1; $i<count($columnas); $i++){
		if ($columnas[$i][5] == 1) {
			$conColumn++;
			if ($columnas[$i][2]) $ancho += $columnas[$i][2];
			else {
				$ancho = '';
				break;
			}
		}
	}


	if ($ancho) {
		$ancho += 30 * count($colEsp) + (count($colEsp)*3+1) + ($conColumn*3+1);
		$tabla_ancho = $ancho;
	}


	if (strlen($_REQUEST["orden"]) > 0) $ordenar = $_REQUEST["orden"];
	else $ordenar = $orden;

	$buscarStr = $busquedaInicio;
	if (strlen($_REQUEST["buscar"]) > 0) $buscarStr = $_REQUEST["buscar"];

	for ($cont = 0; $cont<=count($busqueda); $cont++) {
		if (strlen($busqueda[$cont]) > 0 && strlen($buscarStr) > 0) $buscarStr .= " and ".$busqueda[$cont];
		elseif (strlen($busqueda[$cont]) > 0 and strlen($buscarStr) == 0) $buscarStr = "where ".$busqueda[$cont];
	}

	$limite = 30;
	$mPage = 1;

	if (strlen($_REQUEST["btnPageNext"]) > 0 && strlen($_REQUEST["cmbPageSelect"]) > 0 )
		$mPage = $_REQUEST["cmbPageSelect"] + 1;
	elseif (strlen($_REQUEST["btnPagePrev"]) > 0 && strlen($_REQUEST["cmbPageSelect"]) > 0 )
		$mPage = $_REQUEST["cmbPageSelect"] - 1;
	elseif (strlen($_REQUEST["cmbPageSelect"]) > 0 && strlen($_REQUEST["btnPagePrev"]) == 0 && strlen($_REQUEST["btnPageNext"]) == 0 )
		$mPage = $_REQUEST["cmbPageSelect"];

	$nPage = ($mPage - 1) * $limite;

	$sql = stripslashes($vista." ".$buscarStr." order by ".$ordenar. " limit $nPage, $limite");
	$sql_cont = stripslashes($vista." ".$buscarStr);
	// if (_MOS_CONFIG_DEBUG) echo $sql;

	$temp->query($sql_cont);
	$num_records = $temp->num_rows();

	$temp->query($sql);
	$arrArr = $temp->loadAssocList();
	$cantColumnas = ($conColumn * 2 + count($colEsp) * 2);

	$td = '<script language="JavaScript" src="../js/tabla.js"></script><div id="tabInf">
	  <form action="'.$GLOBALS['sitio_url'].'index.php?'. $_SERVER['QUERY_STRING'].'" method="post" name="pag" id="pag">
	<table align="center" width="'.$tabla_ancho.'" border="0" cellspacing="0" cellpadding="0">

		<input name="cmbPageSelect" type="hidden">
		<input name="orden" type="hidden" value="'.$_REQUEST["orden"].'">
		<input name="buscar" type="hidden" value="'. stripslashes($buscarStr).'">
		<input name="borrar" type="hidden">
		<input name="cambiar" type="hidden">
		<input name="pagar" type="hidden">
		<input name="factura" type="hidden">
		<input name="devol" type="hidden">
		<tr>
			<td id="dale" colspan="'. $cantColumnas.'">
				<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
					<tr class="encabezamiento">
			  			<td width="100" align="left">&nbsp;'. $num_records.' Record(s)</td>
			  			<td align="center">Pages:';

		if (($mPage - 4) < 1) $inicio = 1;
		else $inicio = $mPage - 3;

		if ($num_records >= (($mPage + 3) * $limite)) $final = $mPage + 3;
		else $final = ceil($num_records/$limite);

		for ($x = $inicio; $x <= $final; $x++) {
			if ($mPage == $x) {
				$td .= "<label style=\"font-size:11px \"><strong> ";
				$td .= $x."</strong></label>&nbsp;";
			} else {
				$td .= "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=$x; document.pag.submit()\" href=\"#\"> ";
				$td .= "$x</a>&nbsp;";
			}
		}
		$td .='</td>
					<td width="120" align="right">';

			if ($mPage != 1)
				$td .= "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage-1) ."; document.pag.submit()\" href=\"#\">&lt;&lt;..</a>";
				  ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
			if ($mPage != ceil($num_records/$limite))
				$td .= "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage+1) ."; document.pag.submit()\" href=\"#\">..&gt;&gt;</a>";
			$td .= '</td>
				</tr>
		  	</table>
			</td>
		</tr>
		<tr>
		  <td height="1" colspan="'. $cantColumnas.'"></td>
		</tr>
		<tr class="encabezamiento2">';
		/**Encabezado y título de las columnas */
		//columnas especiales
		for ($cont = 0; $cont < count($colEsp); $cont++) {
			$td .= '<td class="celdas separa" width="30" align="center">&nbsp;&nbsp;</td>';
		}

		//columnas normales
		for ($cont = 0; $cont < $conColumn; $cont++) {
			if ($cont == ($conColumn - 1)) {
				$td .= '<td class="celdas" width="'.$columnas[$cont][2].'" align="<'.$columnas[$cont][3].'"><a class="paglink" href="#" onClick="document.pag.orden.value = \'';
				if (strstr($ordenar, $columnas[$cont][1]." desc")) $td .= $columnas[$cont][1]." asc";
				else $td .= $columnas[$cont][1]." desc";
				$td .= '\'; document.pag.submit();">'.$columnas[$cont][0].'</a>';
				if (strstr($ordenar, $columnas[$cont][1]." desc")) $td .=  "<span class=\"css_down\"></span>";
				elseif (strstr($ordenar, $columnas[$cont][1]." asc")) $td .=  "<span class=\"css_up\"></span>";
				$td .=  "</td>";
			} else {
				$td .= '<td class="celdas separa" width="'.$columnas[$cont][2].'" align="'. $columnas[$cont][3] .'"><a class="paglink" href="#" onClick="document.pag.orden.value = \'';
				if (strstr($ordenar, $columnas[$cont][1]." desc")) $td .=  $columnas[$cont][1]." asc";
				else $td .=  $columnas[$cont][1]." desc";
				$td .= '\'; document.pag.submit();">'. $columnas[$cont][0]. '</a>';
				if (strstr($ordenar, $columnas[$cont][1]." desc")) $td .=  "<span class=\"css_down\"></span>";
				elseif (strstr($ordenar, $columnas[$cont][1]." asc")) $td .=  "<span class=\"css_up\"></span>";

				$td .= '</td>';
			}
		}
		$td .= '</tr>';
/**Fin de encabezado y título de las columnas */
/**Tabla */
		$pase = -1;
		for($i=0; $i<count($arrArr); $i++) {
			//columnas especiales
			$td .= '<tr class="cuerpo" id="campo'.$arrArr[$i]["id"].'" onMouseOver="javascript:cambia(this.id, \'over\');" onMouseOut="javascript:cambia(this.id, null)">';
			for ($cont = 0; $cont < count($colEsp); $cont++) {
				switch ($colEsp[$cont][0]) {
					case 'e':
					case 'v':
						$oncli = "document.pag.cambiar.value='". $arrArr[$i]["id"] ."'; document.pag.submit();";
					break;
					case 'd':
						$oncli = "return alerta('".str_replace (',', '', $arrArr[$i]["valor{val}"])."', '".$arrArr[$i]["estado"]."',  '".$arrArr[$i]["id"]."', 'R' );";
					break;
					case 'p':
						$oncli = "return alerta('".str_replace (',', '', $arrArr[$i]["valor{val}"])."', '".$arrArr[$i]["estado"]."',  '".$arrArr[$i]["id"]."', 'P' );";
					break;
					case 'x':
						$oncli = "return alerta('".str_replace (',', '', $arrArr[$i]["valor{val}"])."', '".$arrArr[$i]["estado"]."',  '".$arrArr[$i]["id"]."', 'S', '".$arrArr[$i]["solDe"]."' );";
					break;
					case 'z':
						$oncli = "window.open('index.php?componente=comercio&pag=reclamacion&tf=". $arrArr[$i]["id"] ."','_self')";
					break;
					case 't':
						$oncli = "return alerta(1, '".$arrArr[$i]["tipo"]."',  '".$arrArr[$i]["id"]."', 'T' );";
					break;
					case 'i':
						$oncli = "window.open('imprimeest.asp?us=". $arrArr[$i]["id"] ."','nueva','menubar=no,scrollbars=yes')";
					break;
					case 'b':
						$oncli = "if (confirm('". $alerta ."')) {document.pag.borrar.value='". $arrArr[$i]["id"] ."'; document.pag.submit();}";
					break;
					case 'c':
						$oncli = "return alerta('".str_replace (',', '', $arrArr[$i]["valor{val}"])."', '".$arrArr[$i]["estado"]."',  '".$arrArr[$i]["id"]."', 'A' );";
					break;
					case 'm':
						$oncli = "window.open('index.php?componente=comercio&pag=pago&identf=". $arrArr[$i]["id"] ."','_self')";
					break;
					case 'f':
						$oncli = "return alerta('', '',  '".$arrArr[$i]["id"]."', 'F' );";
					break;
					case 'n':
						$oncli = "return transf('".$arrArr[$i]["id"]."');";
					break;
				}

				$td .= '<td class="separa" align="center"><span class="'. $colEsp[$cont][2] .'" onClick="'. $oncli.'" alt="'. $colEsp[$cont][1] .'" title="'. $colEsp[$cont][1] .'"></span></td>';
			}
				//columnas normales
			$cuen = 0;
			while ($cuen < $conColumn ) {
				$td .= "<td class=";
				if (preg_match('/index.php/', $arrArr[$i][$columnas[$cuen][1]]))
					$td .= "'separa'";
				else {
					$td .= "'separa desplC'" ;
					$td .= " onclick =\"$('#despl".$arrArr[$i][$columnas[1][1]]."').toggle()\" ";
				}
				$td .= ' align="'.$columnas[$cuen][4].'">';

				if (preg_match('/fecha/', $columnas[$cuen][1])) {
					if ( $arrArr[$i][$columnas[$cuen][1]] != 0 ) $td .= str_replace(' 00:00:00', '', formatea_fecha($arrArr[$i][$columnas[$cuen][1]]));
					else $td .= '-';
				}
				elseif ( preg_match('/{val}/',  $columnas[$cuen][1])) $td .= formatea_numero($arrArr[$i][$columnas[$cuen][1]]);
				elseif ( preg_match('/{ip}/',  $columnas[$cuen][1])) $td .= "<a target='_blank' href='http://whois.domaintools.com/".$arrArr[$i][$columnas[$cuen][1]]."'>".$arrArr[$i][$columnas[$cuen][1]]."</a>";
				elseif ( preg_match('/{geoip}/',  $columnas[$cuen][1])) {
					if (function_exists("geoip_country_name_by_name")) $td .= "<span title='".geoip_country_name_by_name($arrArr[$i][$columnas[$cuen][1]])."' alt='".geoip_country_name_by_name($arrArr[$i][$columnas[$cuen][1]])."' >". geoip_country_code3_by_name($arrArr[$i][$columnas[$cuen][1]])."</span>";
					else $td .= $arrArr[$i][$columnas[$cuen][1]];
				}
				elseif ( preg_match('/{col}/',  $columnas[$cuen][1])) {
					$td .= "<script>document.getElementById('campo".$arrArr[$i]["id"]."').style.color='".$arrArr[$i][$columnas[$cuen][1]]."';</script>";
				}
				elseif (is_float($arrArr[$i][$columnas[$cuen][1]])) $td .= 'hola';
				else $td .= $arrArr[$i][$columnas[$cuen][1]];
				$td .= "</td>";

				$cuen++;
			}
			   ?></tr><?php
			   //datos en la linea inferior

			if ($pase != $i) {
				for ($j=0; $j < $cantColumnas; $j++) {
					if (strlen($columnas[$j][0]) > 1) {
							$sal .= "<div class='valtd'>";
						if ($columnas[$j][5] == 0) {
							$sal .= "<div class='vall'><span class='spmtd'>".$columnas[$j][0].": </span><span class='spmtt'>".$arrArr[$i][$columnas[$j][1]]."</span></div>";
						}
						$j++;
						if ($columnas[$j][5] == 0) {
							$sal .= "<div class='vald'><span class='spmtd'>".$columnas[$j][0].": </span><span class='spmtt'>".$arrArr[$i][$columnas[$j][1]]."</span></div>";
						}
							$sal .= "</div>";
					}
				// echo "ja";
				}

				$td .= '<tr class="desplA" id="despl'.$arrArr[$i][$columnas[1][1]].'"><td class="separa" colspan="'. (count($colEsp) + 1).'" ></td><td class="separa" colspan="'.$cantColumnas.'">'.$sal.'</td></tr>';
				$sal = '';
			}
			$pase = $i;
		}

	$td .= '<tr>
				<td id="dale" colspan="'. $cantColumnas.'">
					<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
						<tr class="encabezamiento">
							<td width="100" align="left">&nbsp;'. $num_records.' Record(s)</td>
							<td align="center">Pages:';

		  if (($mPage - 4) < 1) $inicio = 1;
		  else $inicio = $mPage - 3;

		  if ($num_records >= (($mPage + 3) * $limite)) $final = $mPage + 3;
		  else $final = ceil($num_records/$limite);

		  for ($x = $inicio; $x <= $final; $x++) {
			if ($mPage == $x) {
				$td .=  "<label style=\"font-size:11px \"><strong> ";
				$td .=  $x."</strong></label>&nbsp;";
			}else {
				$td .=  "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=$x; document.pag.submit()\" href=\"#\"> ";
				$td .=  "$x</a>&nbsp;";
			}
		}
		$td .= '</td>
			  <td width="120" align="right">';

			if ($mPage != 1)
				$td .=  "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage-1) ."; document.pag.submit()\" href=\"#\">&lt;&lt;..</a>".'
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			if ($mPage != ceil($num_records/$limite))
				$td .= "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage+1) ."; document.pag.submit()\" href=\"#\">..&gt;&gt;</a>".'
	</td>
			</tr>
		  </table>
			</td>
		  </tr>

	</table>
	  </form></div>';
	echo $td;
	return $sql;
}



/**
 *
 * Tabla de muestra de resultados
 * @param integer $tabla_ancho ancho de la tabla que muestra los resultados
 * @param char(1) $idioma Idioma de entrada, permite prefijar valores de mensajes que de otra forma habrï¿½a que entrarlos
 * @param string $vista
 * @param string $orden
 * @param string $busquedaInicio
 * @param string $colEsp
 * @param string $busqueda
 * @param string $columnas
 * @return string
 */
function tabla( $tabla_ancho, $idioma='E', $vista, $orden='', $busquedaInicio='', $colEsp='', $busqueda='', $columnas) {
/*
vista - Vista de la BD contra la cual se realizar&aacute; todo el trabajo
orden -  ordenamiento de los valores de salida de la vista por default
busquedaInicio - Columnas de la vista a buscar por defult
colEsp - array de array para las columnas especiales (editar, ver, borrar..) en la forma (tipocolumna, textoalt, caminoimagen, titulocolumna)
busqueda - array de array para las busquedas en la forma (nombrecampo, columnatabla)
columnas - array de array con los datos de (titulocolumna, campotabla, anchocolumna, posicion)
*/

//$idioma = 'I';
$ancho = '';
if ($_SESSION['idioma'] == "english") {
	$alerta = "You are about to delete a record and all the data asociated with.\\nAre you sure?";
	$alerta2 = "Are you sure to cancel the transaction?\\nThis operation can`t be reverted.";
	$alerta3 = "The transaction must be Accepted or Refunded and or has a value bigger than 0.";
	$alerta4 = "The transacction will revert it`s status of paid to the commerce.";
	$alerta5 = "The transacction will not been requested for refund.";
} else {
	$alerta = "Se borrar\u00e1 este registro y todos los datos asociados a \u00e9l.\\nEst\u00e1 seguro?";
	$alerta2 = "Est\u00e1 usted seguro que la transacci\u00f3n es una reclamaci\u00f3n?\\nEsta operaci\u00f3n no puede ser revertida.";
	$alerta3 = "La transacci\u00f3n debe ser Aceptada y tener valor mayor que 0.";
	$alerta4 = "La transacci\u00f3n revertir\u00e1 su estado de pagada al comercio.";
	$alerta5 = "La transacci\u00f3n no debe estar solicitada para devolver.";
}

foreach ($columnas as $item) {
	if ($item[2]) $ancho += $item[2];
	else {
		$ancho = '';
		break;
	}
}

if ($ancho) {
	$ancho += 30 * count($colEsp) + (count($colEsp)*3+1) + (count($columnas)*3+1);
	$tabla_ancho = $ancho;
}

// echo "<br><br>busquedaInicio=".$busquedaInicio."<br><br>";

?>
<div id="tabInf">
  <form action="<?php echo $GLOBALS['sitio_url'].'index.php?'. $_SERVER['QUERY_STRING'] ?>" method="post" name="pag" id="pag">
<table align="center" width="<?php echo $tabla_ancho; ?>" border="0" cellspacing="0" cellpadding="0">
<?php
  			if (strlen($_REQUEST["orden"]) > 0) $ordenar = $_REQUEST["orden"];
			else $ordenar = $orden;

			$buscarStr = $busquedaInicio;
			if (strlen($_REQUEST["buscar"]) > 0) $buscarStr = $_REQUEST["buscar"];

			for ($cont = 0; $cont<=count($busqueda); $cont++) {
				if (strlen($busqueda[$cont]) > 0 && strlen($buscarStr) > 0) $buscarStr .= " and ".$busqueda[$cont];
				elseif (strlen($busqueda[$cont]) > 0 and strlen($buscarStr) == 0) $buscarStr = "where ".$busqueda[$cont];
			}

			$limite = 30;
			$mPage = 1;

			if (strlen($_REQUEST["btnPageNext"]) > 0 && strlen($_REQUEST["cmbPageSelect"]) > 0 )
				$mPage = $_REQUEST["cmbPageSelect"] + 1;
			elseif (strlen($_REQUEST["btnPagePrev"]) > 0 && strlen($_REQUEST["cmbPageSelect"]) > 0 )
				$mPage = $_REQUEST["cmbPageSelect"] - 1;
			elseif (strlen($_REQUEST["cmbPageSelect"]) > 0 && strlen($_REQUEST["btnPagePrev"]) == 0 && strlen($_REQUEST["btnPageNext"]) == 0 )
				$mPage = $_REQUEST["cmbPageSelect"];

			$nPage = ($mPage - 1) * $limite;

  			$sql = stripslashes($vista." ".$buscarStr." order by ".$ordenar. " limit $nPage, $limite");
  			$sql_cont = stripslashes($vista." ".$buscarStr);
			if (_MOS_CONFIG_DEBUG) echo "sql=".$sql;
			error_log(str_replace("\t", "", str_replace("\n"," ",$sql_cont)));

//correoAMi("Querys", $sql_cont);
		    $usr = new ps_DB();
			$usr->query($sql_cont);//&& $_SESSION['id'] == 19
// 			if ($usr->getErrorMsg() ) {
//				$corr = new correo();
//				$corr->todo(43, "Error de Mysql", $usr->getErrorMsg());
// 			}
			$num_records = $usr->num_rows();

//			error_log($sql);
			$usr->query($sql);
			$cantColumnas = (count($columnas) * 2 + count($colEsp) * 2) - 1;
//			$num_records = $usr->getRecords();
//			$usr->reset();

//echo $sql;
		 ?>
		<input name="cmbPageSelect" type="hidden">
		<input name="orden" type="hidden" value="<?php echo $_REQUEST["orden"]; ?>">
		<input name="buscar" type="hidden" value="<?php echo stripslashes($buscarStr); ?>">
		<input name="borrar" type="hidden">
		<input name="cambiar" type="hidden">
		<input name="pagar" type="hidden">
		<input name="factura" type="hidden">
		<input name="devol" type="hidden">
      <tr>
		  <td id="dale" colspan="<?php echo $cantColumnas; ?>"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr class="encabezamiento">
          <td width="100" align="left">&nbsp;<?php echo $num_records; ?> Record(s)</td>
          <td align="center">Pages:
            <?php

	  if (($mPage - 4) < 1) $inicio = 1;
	  else $inicio = $mPage - 3;

	  if ($num_records >= (($mPage + 3) * $limite)) $final = $mPage + 3;
	  else $final = ceil($num_records/$limite);

	  for ($x = $inicio; $x <= $final; $x++) {
		if ($mPage == $x) {
			echo "<label style=\"font-size:11px \"><strong> ";
			echo $x."</strong></label>&nbsp;";
		} else {
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=$x; document.pag.submit()\" href=\"#\"> ";
			echo "$x</a>&nbsp;";
		}
	}
	 ?></td>
          <td width="120" align="right"><?php

		if ($mPage != 1)
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage-1) ."; document.pag.submit()\" href=\"#\">&lt;&lt;..</a>"; ?>
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
		if ($mPage != ceil($num_records/$limite))
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage+1) ."; document.pag.submit()\" href=\"#\">..&gt;&gt;</a>"; ?>
</td>
        </tr>
      </table>
        </td>
      </tr>
    <tr>
      <td height="1" colspan="<?php echo $cantColumnas; ?>"></td>
    </tr>
    <tr class="encabezamiento2">
	<?php
//columnas especiales
	for ($cont = 0; $cont < count($colEsp); $cont++) { ?>
    <td class="celdas separa" width="30" align="center">&nbsp;<?php //echo $colEsp[$cont][3]; ?>&nbsp;</td>
	<?php }
//columnas normales
	 for ($cont = 0; $cont < count($columnas); $cont++) {
		if ($cont == (count($columnas) - 1)) {?>
			<td class="celdas" width="<?php echo $columnas[$cont][2] ?>" align="<?php echo $columnas[$cont][3] ?>"><a class="paglink" href="#" onClick="document.pag.orden.value = '<?php
			if (strstr($ordenar, $columnas[$cont][1]." desc")) echo $columnas[$cont][1]." asc";
			else echo $columnas[$cont][1]." desc";
			?>'; document.pag.submit();"><?php echo $columnas[$cont][0]; ?></a><?php
			if (strstr($ordenar, $columnas[$cont][1]." desc")) echo "<span class=\"css_down\"></span>";
			elseif (strstr($ordenar, $columnas[$cont][1]." asc")) echo "<span class=\"css_up\"></span>";
//			if (strstr($ordenar, $columnas[$cont][1]." desc")) echo "<img src=\"../images/down.gif\" width=\"15\" height=\"16\" />";
//			elseif (strstr($ordenar, $columnas[$cont][1]." asc")) echo "<img src=\"../images/up.gif\" width=\"15\" height=\"16\" />";
			echo "</td>";
		} else {?>
		<td class="celdas separa" width="<?php echo $columnas[$cont][2] ?>" align="<?php echo $columnas[$cont][3] ?>"><a class="paglink" href="#" onClick="document.pag.orden.value = '<?php
		if (strstr($ordenar, $columnas[$cont][1]." desc")) echo $columnas[$cont][1]." asc";
		else echo $columnas[$cont][1]." desc";
		?>'; document.pag.submit();"><?php echo $columnas[$cont][0]; ?></a><?php
		if (strstr($ordenar, $columnas[$cont][1]." desc")) echo "<span class=\"css_down\"></span>";
		elseif (strstr($ordenar, $columnas[$cont][1]." asc")) echo "<span class=\"css_up\"></span>";

		?></td>
	<?php }} ?>

  </tr>

  <?php
	while($usr->next_record()) { ?>
  <tr class="cuerpo" id="campo<?php echo $usr->f("id") ?>" onMouseOver="javascript:cambia(this.id, 'over');" onMouseOut="javascript:cambia(this.id, null)">
<?php for ($cont = 0; $cont < count($colEsp); $cont++) {
		if ($colEsp[$cont][0] == "e" || $colEsp[$cont][0] == "v")
			$oncli = "document.pag.cambiar.value='". $usr->f("id") ."'; document.pag.submit();";
		elseif ($colEsp[$cont][0] == "r")
			$oncli = "return alerta('".str_replace (',', '', $usr->f("valor{val}"))."', '".$usr->f("estado")."',  '".$usr->f("id")."', 'B', '".$usr->f("solDe")."');";
		elseif ($colEsp[$cont][0] == "d")
			$oncli = "return alerta('".str_replace (',', '', $usr->f("valor{val}"))."', '".$usr->f("estado")."',  '".$usr->f("id")."', 'R' );";
		elseif ($colEsp[$cont][0] == "p")
			$oncli = "return alerta('".str_replace (',', '', $usr->f("valor{val}"))."', '".$usr->f("estado")."',  '".$usr->f("id")."', 'P' );";
		elseif ($colEsp[$cont][0] == "x") {
			if ($_SESSION['rol'] != 14 && $_SESSION['rol'] != 15) //se prohibe que Vendedores y Contables de los comercios soliciten devolución
				$oncli = "return alerta('".str_replace (',', '', $usr->f("valor{val}"))."', '".$usr->f("estado")."',  '".$usr->f("id")."', 'S', '".$usr->f("solDe")."', '".$usr->f("tipo")."' );";
			else $oncli = "alert('Usted no tiene permitido la solicitud de devoluciones');";
		} elseif ($colEsp[$cont][0] == "z") {
			if ($_REQUEST['pag'] == 'reporten') $oncli = "window.open('index.php?componente=comercio&pag=reclaman&tf=". $usr->f("id") ."','_self')";
			else $oncli = "window.open('index.php?componente=comercio&pag=reclama&tf=". $usr->f("id") ."','_self')";
		} elseif ($colEsp[$cont][0] == "t")//ver transferencia o preautorizo
			$oncli = "return alerta(1, '".$usr->f("tipo")."',  '".$usr->f("id")."', 'T' );";
		elseif ($colEsp[$cont][0] == "i")
			$oncli = "window.open('imprimeest.asp?us=". $usr->f("id") ."','nueva','menubar=no,scrollbars=yes')";
		elseif ($colEsp[$cont][0] == "b")
			$oncli = "if (confirm('". $alerta ."')) {document.pag.borrar.value='". $usr->f("id") ."'; document.pag.submit();}";
		elseif ($colEsp[$cont][0] == "c")
			$oncli = "return alerta('".str_replace (',', '', $usr->f("valor{val}"))."', '".$usr->f("estado")."',  '".$usr->f("id")."', 'A' );";
		elseif ($colEsp[$cont][0] == "m")
			$oncli = "window.open('index.php?componente=comercio&pag=pago&identf=". $usr->f("id") ."','_self')";
		elseif ($colEsp[$cont][0] == "f")
			$oncli = "return alerta('', '',  '".$usr->f("id")."', 'F' );";
		elseif ($colEsp[$cont][0] == "n")
			$oncli = "return transf('".$usr->f("id")."');";
		?>
    <td class="separa" align="center"><span class="<?php echo $colEsp[$cont][2] ?>" onClick="<?php echo $oncli ?>"
    	alt="<?php echo $colEsp[$cont][1] ?>" title="<?php echo $colEsp[$cont][1] ?>"></span></td>
	<?php }
  for ($cont = 0; $cont < count($columnas); $cont++) {
  	if ($cont == (count($columnas)-1)) { ?>
	     <td style="padding-left:7px; padding-right:7px; padding-top:4px; padding-bottom:4px" align="<?php echo $columnas[$cont][4]; ?>"><?php
		if (preg_match('/fecha/', $columnas[$cont][1])) {
			if ( $usr->f($columnas[$cont][1]) != 0 )
				echo str_replace(' 00:00:00', '', formatea_fecha($usr->f($columnas[$cont][1])));
			else echo '-';
		}
		elseif ( preg_match('/{val}/',  $columnas[$cont][1])) echo formatea_numero($usr->f($columnas[$cont][1]));
		elseif ( preg_match('/{ip}/',  $columnas[$cont][1])) echo "<a target='_blank' href='http://whois.domaintools.com/".
				$usr->f($columnas[$cont][1])."'>".$usr->f($columnas[$cont][1])."</a>";
		elseif ( preg_match('/{geoip}/',  $columnas[$cont][1])) {
			if (function_exists("geoip_country_name_by_name"))
				echo "<span title='".geoip_country_name_by_name($usr->f($columnas[$cont][1]))."' alt='".geoip_country_name_by_name($usr->f($columnas[$cont][1]))."' >".
						geoip_country_code3_by_name($usr->f($columnas[$cont][1]))."</span>";
			else $usr->f($columnas[$cont][1]);
		}
	    elseif ( preg_match('/{col}/',  $columnas[$cont][1])) {
	        $jav = "<script>
	                    document.getElementById('campo".$usr->f("id")."').style.color='".$usr->f($columnas[$cont][1])."';
	                </script>";
	        echo $jav;
	    }
		else
		echo utf8_decode($usr->f($columnas[$cont][1]));
	 ?></td>
	<?php } else { ?>
    <td class="separa" style="padding-left:7px; padding-right:7px; padding-top:4px; padding-bottom:4px" align="<?php echo $columnas[$cont][4]; ?>"><?php
	if (preg_match('/fecha/', $columnas[$cont][1])) {
		if ( $usr->f($columnas[$cont][1]) != 0 )
			echo str_replace(' 00:00:00', '', formatea_fecha($usr->f($columnas[$cont][1])));
		else echo '-';
	}
	elseif ( preg_match('/{val}/',  $columnas[$cont][1])) echo formatea_numero($usr->f($columnas[$cont][1]));
	elseif ( preg_match('/{ip}/',  $columnas[$cont][1])) echo "<a target='_blank' href='http://whois.domaintools.com/".
				$usr->f($columnas[$cont][1])."'>".$usr->f($columnas[$cont][1])."</a>";
	elseif ( preg_match('/{geoip}/',  $columnas[$cont][1])) {
		if (function_exists("geoip_country_name_by_name"))
				echo "<span title='".geoip_country_name_by_name($usr->f($columnas[$cont][1]))."' alt='".geoip_country_name_by_name($usr->f($columnas[$cont][1]))."' >".
						geoip_country_code3_by_name($usr->f($columnas[$cont][1]))."</span>";
			else $usr->f($columnas[$cont][1]);
	}
    elseif ( preg_match('/{col}/',  $columnas[$cont][1])) {
        $jav = "<script>
                    document.getElementById('campo".$usr->f("id")."').style.color='".$usr->f($columnas[$cont][1])."';
                </script>";
        echo $jav;
    }
    elseif (is_float($usr->f($columnas[$cont][1]))) echo 'hola';
	else
	echo utf8_decode($usr->f($columnas[$cont][1]));
	 ?></td>
	<?php }} ?>
  </tr>
	<?php
}
?>
<script language="JavaScript" type="text/JavaScript">
function cambia(renglon, acc) {
	if (acc == 'over') document.getElementById(renglon).bgColor='#CCCCCC';
	else document.getElementById(renglon).bgColor='';
}

function alerta(valor=null, estado=null, id=null, accion, solDe=null, tipo=null) {

	 if (accion == 'T') {
		if (estado == "Transferencia") {
			/*document.pag.factura.value=id;
			envia(this.form);*/
			window.open('index.php?componente=comercio&pag=factura&tf='+id, "_self");
//			return true;

		} else alert('<?php echo _GRUPOS_ALERTA_FACT; ?>');

		return false;
	 }

	 if (accion == 'M') {
		document.forms[0].inserta.value=id;
		document.forms[0].submit();
		return true;
	} else if (accion == 'B') {
		
		if (estado == 'A' && solDe == 1){
			if (confirm('La operación cambiará el estado a Aceptada, está de acuerdo?')) {
				window.open('index.php?componente=comercio&pag=reporte&soldeve='+id, '_self');
				// return solDev(id);
			}
		} else if (estado == 'N') {
			if (confirm('Procederá a cambiar el estado de una operación de\n No Procesada a Aceptada \n deberá registrar como dato el código de autorizo del banco y la tasa (si es una operación distinta de Euros).'))
				window.open('index.php?componente=comercio&pag=campros&tf='+id, "_self");
		} else{
			alert("La operación no está en solicitud de devolucion ni No Procesada");
		}

	} else if (accion == 'F') {
		document.pag.pagar.value=id;
		document.pag.submit();
		return true;
	} else if (accion == 'P' && (estado != 'P')){
				if (confirm('<?php echo $alerta4 ?>')) {
					document.pag.pagar.value=id;
					document.pag.submit();
					return true;
				}
	} else {
	 	if (valor > 0 && (estado == 'V' || estado == 'A' ) && solDe == 1) {
			alert('<?php echo $alerta5 ?>');
		} else if (valor > 0 && (estado == 'V' || estado == 'A' )) {
			if (accion == 'A') {
				if (confirm('<?php echo $alerta2 ?>')) {
					document.pag.borrar.value = id;
					document.pag.submit();
					return true;
				}
			} else if (accion == 'S') {
				if (tipo == 'Transferencia')
					alert ('Esta operaci\u00F3n es una Transferencia y no admite devoluciones');
				else
					window.open('index.php?componente=comercio&pag=solde&tf='+id, "_self");
			} else {
				document.pag.cambiar.value=id;
				document.pag.submit();
				return true;
			}
		} else {
			alert('<?php echo $alerta3 ?>');
		}
	}
	return false;
}

//$(document).ready(function(){
	function solDev(id){
		// $(".alerti").esperaDiv('muestra');
		$.post('componente/comercio/ejec.php',{
			fun:'cambSol',
			cie:id
		},function(data){
			var datos = eval('(' + data + ')');
			// $('.alerti').esperaDiv('cierra');
			// $("#enviaForm").show();
			if (datos.error.length > 0) alert(datos.error);
			if (datos.cont.length > 0) {
				window.open('index.php?componente=comercio&pag=reporte&nombre='+datos.cont, "_self")
			}
		});

	}

	function transf(cierre) {
		$(".alerti").esperaDiv('muestra');
		$.post('componente/comercio/ejec.php',{
			fun:'inscierre',
			cie:cierre
		},function(data){
			var datos = eval('(' + data + ')');
			$('.alerti').esperaDiv('cierra');
			$("#enviaForm").show();
			if (datos.error.length > 0) alert(datos.error);
			if (datos.pase.length > 0) {
				$("#salCierre").html(datos.pase[1]);
			}
		});
	}
//});
</script>
        <tr>
		  <td id="dale" colspan="<?php echo $cantColumnas; ?>"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr class="encabezamiento">
          <td width="100" align="left">&nbsp;<?php echo $num_records; ?> Record(s)</td>
          <td align="center">Pages:
            <?php

	  if (($mPage - 4) < 1) $inicio = 1;
	  else $inicio = $mPage - 3;

	  if ($num_records >= (($mPage + 3) * $limite)) $final = $mPage + 3;
	  else $final = ceil($num_records/$limite);

	  for ($x = $inicio; $x <= $final; $x++) {
		if ($mPage == $x) {
			echo "<label style=\"font-size:11px \"><strong> ";
			echo $x."</strong></label>&nbsp;";
		}else {
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=$x; document.pag.submit()\" href=\"#\"> ";
			echo "$x</a>&nbsp;";
		}
	}
	 ?></td>
          <td width="120" align="right"><?php

		if ($mPage != 1)
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage-1) ."; document.pag.submit()\" href=\"#\">&lt;&lt;..</a>"; ?>
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
		if ($mPage != ceil($num_records/$limite))
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage+1) ."; document.pag.submit()\" href=\"#\">..&gt;&gt;</a>"; ?>
</td>
        </tr>
      </table>
        </td>
      </tr>

</table>
  </form></div>
<?php
return $sql;
}

//construye las tablas de resultados
//Tabla de muestra de resultados
function tablanp( $query, $columnas, $colEsp = null) {
/*
tabla_ancho - ancho de la tabla que muestra los resultados
idioma - Idioma de entrada, permite prefijar valores de mensajes que de otra forma habr&iacute;a que entrarlos
vista - Vista de la BD contra la cual se realizar&aacute; todo el trabajo
orden -  ordenamiento de los valores de salida de la vista por default
busquedaInicio - Columnas de la vista a buscar por defult
colEsp - array de array para las columnas especiales (editar, ver, borrar..) en la forma (tipocolumna, textoalt, caminoimagen, titulocolumna)
busqueda - array de array para las busquedas en la forma (nombrecampo, columnatabla)
columnas - array de array con los datos de (titulocolumna, campotabla, anchocolumna, posicion)
*/

?>
<form action="<?php echo $GLOBALS['sitio_url'].'index.php?'. $_SERVER['QUERY_STRING'] ?>" method="post" name="pag" id="pag">
<table align="center" border="0" cellspacing="0" cellpadding="0">
<?php
			$alerta="";
		    $usr = new ps_DB();
			$usr->query($query);
			$num_records = $usr->num_rows();
			$tot = 0;

			$usr->query($query);
			$cantColumnas = (count($columnas) * 2 + count($colEsp) * 2) - 1;
// 			$usr->reset();

		 ?>
      <tr>
      <td colspan="<?php echo $cantColumnas; ?>"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr class="encabezamiento">
          <td width="100" align="left">&nbsp;<?php echo $num_records; ?> Record(s)</td>
          <td align="center"></td>
          <td width="120" align="right"></td>
        </tr>
      </table>
        </td>
      </tr>
    <tr>
      <td height="1" colspan="<?php echo $cantColumnas; ?>"></td>
    </tr>
    <tr class="encabezamiento2">
	<?php
//columnas normales
	 for ($cont = 0; $cont < count($columnas); $cont++) {
		if ($cont != 0) {
		?>
		<td width="1" class="separador1"></td>
		<?php } ?>
		<td class="celdas" width="<?php echo $columnas[$cont][2] ?>" align="<?php echo $columnas[$cont][3] ?>"><?php echo $columnas[$cont][0]; ?></td>
	<?php }

//columnas especiales
	for ($cont = 0; $cont < count($colEsp); $cont++) { ?>
	<td width="1" class="separador1"></td>
    <td class="celdas" width="50" align="center">&nbsp;<?php echo $colEsp[$cont][3]; ?>&nbsp;</td>
	<?php } ?>
  </tr><?php
	while($usr->next_record()) { ?>
  <tr class="cuerpo" id="campo<?php echo $usr->f("id") ?>" onMouseOver="cambia(this.id, 'over');" onMouseOut="cambia(this.id, '')">
  <?php for ($cont = 0; $cont < count($columnas); $cont++) {
  	if ($cont != 0) { ?>
	    <td width="1" class="separador2"></td>
	<?php } ?>
    <td style="padding-left:7px; padding-right:7px; padding-top:4px; padding-bottom:4px" align="<?php echo $columnas[$cont][4]; ?>"><?php
	if (preg_match('/fecha/',  $columnas[$cont][1])) {
		if ( $usr->f($columnas[$cont][1]) != 0 )
			echo date('d/m/Y H:i:s', $usr->f($columnas[$cont][1]));
		else echo '-';
	}
	elseif ( preg_match('/{val}/',  $columnas[$cont][1])) {
		echo formatea_numero($usr->f($columnas[$cont][1]));
		if ( preg_match('/{tot}/',  $columnas[$cont][1])) {
				$tot += number_format($usr->f($columnas[$cont][1]),2,'.','');
		}
	}
	elseif ( preg_match('/{col}/',  $columnas[$cont][1])) {
        $jav = "<script>
                    document.getElementById('campo".$usr->f("id")."').style.color='".$usr->f($columnas[$cont][1])."';
                </script>";
        echo $jav;
    }
	else
	echo $usr->f($columnas[$cont][1]);
	 ?></td>
	<?php }
	for ($cont = 0; $cont < count($colEsp); $cont++) {
		if ($colEsp[$cont][0] == "e" || $colEsp[$cont][0] == "v")
			$oncli = "document.pag.cambiar.value='". $usr->f("id") ."'; envia(this.form);";
		elseif ($colEsp[$cont][0] == "i")
			$oncli = "window.open('imprimeest.asp?us=". $usr->f("id") ."','nueva','menubar=no,scrollbars=yes')";
		elseif ($colEsp[$cont][0] == "b")
			$oncli = "if (confirm('". $alerta ."')) {document.pag.borrar.value='". $usr->f("id") ."'; envia(this.form)}";
		?>
    <td width="1" class="separador2"></td>
    <td align="center"><input src="<?php echo $colEsp[$cont][2] ?>" name="u" type="image" onClick="<?php echo $oncli ?>" alt="<?php echo $colEsp[$cont][1] ?>" /></td>
    <?php } ?>
  </tr><?php
}
?>
<script language="JavaScript" type="text/JavaScript">
function cambia(renglon, acc) {
	if (acc == 'over') document.getElementById(renglon).bgColor='#CCCCCC';
	else document.getElementById(renglon).bgColor='';
}
</script>
      <tr>
		  <td id="dale" colspan="<?php echo $cantColumnas; ?>"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr class="encabezamiento">
          <td width="100" align="left">&nbsp;<?php echo $num_records; ?> Record(s)</td>
          <td align="center">
            <?php
			echo _COMERCIO_EUROSC." "._REPORTE_TOTAL.": ".formatea_numero($tot);
// 	  if (($mPage - 4) < 1) $inicio = 1;
// 	  else $inicio = $mPage - 3;

// 	  if ($num_records >= (($mPage + 3) * $limite)) $final = $mPage + 3;
// 	  else $final = ceil($num_records/$limite);

// 	  for ($x = $inicio; $x <= $final; $x++) {
// 		if ($mPage == $x) {
// 			echo "<label style=\"font-size:11px \"><strong> ";
// 			echo $x."</strong></label>&nbsp;";
// 		}else {
// 			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=$x; document.pag.submit()\" href=\"#\"> ";
// 			echo "$x</a>&nbsp;";
// 		}
// 	}
	 ?></td>
          <td width="120" align="right"><?php

// 		if ($mPage != 1)
// 			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage-1) ."; document.pag.submit()\" href=\"#\">&lt;&lt;..</a>"; ?>
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
// 		if ($mPage != ceil($num_records/$limite))
// 			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage+1) ."; document.pag.submit()\" href=\"#\">..&gt;&gt;</a>"; ?>
</td>
        </tr>
      </table>
        </td>
      </tr>
</table></form>
<?php
}


/**
 * Solicita la devolución de las operaciones
 */
function solDevOper($id, $monto, $comerc, $correoMi, $obser = null) {
	global $temp;
	$corCreo = new correo();
	$web = false;

	//reviso si la operación está en tramites de reclamación
	$q = "select count(*) total from tbl_reclamaciones where idtransaccion = $id";
	$correoMi .= $q . "<br>";
	$temp->query($q);
	if ($temp->f('total') > 0) return json_encode(array("result" => 'E', "comen" => utf8_encode("La operación está en proceso de reclamación, no puede devolverse."), "correoMi" => utf8_encode($correoMi)));

	//saco los datos de la operación
	$q = "select from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i') fec, m.moneda, c.nombre comer, t.valor, t.codigo, t.identificador, t.pasarela, c.id idc from tbl_transacciones t, tbl_comercio c, tbl_moneda m where t.idcomercio = c.idcomercio and c.idcomercio = $comerc and t.idtransaccion = $id and t.moneda = m.idmoneda and t.solDev = 0";
	$correoMi .= $q . "<br>";
	$temp->query($q);
	if ($temp->num_rows()) {
		$fec 		= $temp->f('fec');
		$moneda 	= $temp->f('moneda');
		$codigo		= $temp->f('codigo');
		$comer		= $temp->f('comer');
		$identif	= $temp->f('identificador');
		$idpass		= $temp->f('pasarela');
		$valor		= $temp->f('valor');
		$idc		= $temp->f('idc');

	} else return json_encode(array("result" => 'E', "comen" => utf8_encode("La operación no aparece en nuestra Base de datos."), "correoMi" => utf8_encode($correoMi)));

	//verifico los montos
	// error_log($correoMi);
	$correoMi .= "Monto actual= $valor<br>Monto a devolver= $monto<br>";
	if ($valor < $monto) return json_encode(array("result" => 'E', "comen" => utf8_encode("El valor a devolver no puede ser mayor al valor que posee la operación."), "correoMi" => utf8_encode($correoMi)));

	if ($_SESSION['admin_nom']) {
		$nomAdm 	= $_SESSION['admin_nom'];
		$emaAdm		= $_SESSION['email'];
		$idAdmin	= $_SESSION['id'];
		$arrayTo[] = array($_SESSION['admin_nom'], $_SESSION['email']);
	} else { //la solicitud se produce desde una Web
		$q = "select a.nombre, a.email, a.idadmin from tbl_admin a, tbl_colAdminComer c where c.idComerc = " . $idc . " and c.idAdmin = a.idadmin and a.idrol = 11 limit 0,1";
		$correoMi .= $q . "<br>";
		$temp->query($q);
		$nomAdm 	= utf8_decode($temp->f('nombre'));
		$emaAdm		= $temp->f('email');
		$idAdmin	= $temp->f('idadmin');

		if ($comerc == '122327460662' || $comerc == '163474342422') {
			$nomAdm 	= 'Julio';
			$emaAdm		= 'serv.tecnico@bidaiondo.com';
			$idAdmin	= '10';
		}

		//verifico que la operación no esté en la tabla reserva para confirmar que fué
		//una operación realizada desde la web
		$q = "select count(*) total from tbl_reserva where id_transaccion = $id";
		$correoMi .= $q . "<br>";
		$temp->query($q);

		if ($temp->f('total') > 0) return json_encode(array('result' => 'E', 'comen' => utf8_encode('La operación no fué realizada desde la Web'), 'correoMi' => utf8_encode($correoMi)));
		$web = true;
	}

	//se envia correo a la persona a cargo de las devoluciones en AMF
	$text = "El administrador " . $nomAdm . " con correo " . $emaAdm . " del comercio " . $comer . " con identificador " .
		$comerc . ", solicita la devolución de " . number_format($monto/100, 2) . " {$moneda} de la transacción número $id " .
		"que posee código de comercio " . $identif . " y de autorización del banco " . $codigo .
		", la misma fué realizada el día " . $fec;
	if ($obser) $text .= "\n\nDebe tener en cuenta \n" . $obser;
	$correoMi .= $text . "<br>";

	$subject = 'Solicitud de devolución de Transacción ' . $id;
	$des = true;
	foreach ($arrayTo as $todale) {
		if ($des) {
			$corCreo->to($todale[1]);
			$des = false;
		} else $corCreo->add_headers("Cc: " . $todale[1]);
	}
	$corCreo->todo(29, $subject, $text);

	//se pone la operación como solicitada a devolver
	$q = "update tbl_transacciones set solDev = 1 where idtransaccion = '$id'";
	$correoMi .= $q . "<br>";
	$temp->query($q);

	//escribe la tabla de devoluciones para insertar la solicitud
	if ($idpass == 37) {
		$q = "select descripcion from tbl_aisRazonCancel where idtitanes = " . $obser;
		$temp->query($q);
		$obser = $obser . "#" . $temp->f('descripcion');
	}
	$q = "insert into tbl_devoluciones (id, idtransaccion, idadmin, devpor, fecha, fechaDev, valorDev, observacion)
				values (null, '$id', '" . $idAdmin . "', '0', '" . time() . "', '0', '" . ($monto/100) . "', '{$obser}')";
	$correoMi .= $q . "<br>";
	$temp->query($q);

	if (!$web) {
		$q = "select nombre, email, idioma, codigo, servicio from tbl_reserva where id_transaccion = '$id'";
		$temp->query($q);
		$nom = $temp->f('nombre');
		$cor = $temp->f('email');
		$idi = $temp->f('idioma');


		($idi != 'en') ? include_once 'lang/correoes.php' : include_once 'lang/correo' . $idi . ".php";
		if (strlen($idi) > 1);

		if ($nom) {
			$tes = str_replace("&date&", date("d/m/Y H:i"), str_replace("&id&", $id, str_replace("&idc&", $identif, str_replace("&dia&", $fec, str_replace("&motivo&", $obser, str_replace("&comercio&", $comer, _COR_DEVCLI))))));

			$corCreo->todo(42, _LAB_DEVCLI, $tes);

			$impr = str_replace("&date&", date("d/m/Y H:i"), str_replace("&comnn&", $val['comercio'], str_replace("&serv&", $temp->f('servicio'), str_replace("&nom&", $nom, str_replace("&nom&", $nom, str_replace("&id&", $id, str_replace("&idc&", $idc, str_replace("&aut&", $codigo, str_replace("&adev&", number_format($monto, 2), str_replace("&mon&", $moneda, _IMP_DEVCLI))))))))));

			echo "<script language=\"JavaScript\" type=\"text/javascript\">window.open('impTick.php?text=$impr','_new')</script>";
		}

		echo "<script language=\"JavaScript\" type=\"text/javascript\">window.open('index.php?componente=comercio&pag=reporte','_self')</script>";
	} else return json_encode(array("result" => 'A', 'transaccion' => $id, "comen" => utf8_encode("La solicitud de devolución ha sido Aceptada"), "correoMi" => utf8_encode($correoMi)));
}

function enviaError ($textoCorreo, $etiq = 'Error') {
	global $correo;
	$correo->todo(9, $etiq, $textoCorreo);
	// 	exit;
}


 ?>
