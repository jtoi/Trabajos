<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
$temp = new ps_DB;
$correo = new correo;
$error = '';
include 'classes/PHPExcel/IOFactory.php';
// echo json_encode($_REQUEST);

//if (_MOS_CONFIG_DEBUG) var_dump($_FILES);
if (_MOS_CONFIG_DEBUG) var_dump($_REQUEST);

if(strlen($_FILES['fichero']['tmp_name']) > 3) {
	if ($_FILES['fichero']['error']) {
			  switch ($_FILES['fichero']['error']){
					   case 1: // UPLOAD_ERR_INI_SIZE
							$error .= "El archivo sobrepasa el limite autorizado por el servidor(archivo php.ini) !";
					   break;
					   case 2:
							$error .= "El archivo sobrepasa el limite autorizado en el formulario HTML !";
					   break;
					   case 3: // UPLOAD_ERR_PARTIAL
							$error .= "El envio del archivo ha sido suspendido durante la transferencia!";
					   break;
					   case 4: // UPLOAD_ERR_NO_FILE
							$error .= "El archivo que ha enviado tiene un tamaño nulo !";
					   break;
			  }
	} else {
		if ($_REQUEST['banco'] == 8) {
			// $nom = $_FILES['fichero']['tmp_name'];
			$nom = $_FILES["fichero"]["name"];
			$array = explode('.', $_FILES['fichero']['name']);
			$ext = end($array);

			if ($ext == 'csv')  {
				
				$handle = fopen($_FILES['fichero']['tmp_name'], 'r');
				if ($handle) {
					while (!feof($handle)) {
						$arrDat = array();
						$buffer = fgets($handle);
						if (strlen($buffer) > 3) {
							// echo "<br>bufer=".str_replace('"', '', str_replace('"', '', $buffer));
							$arrBuff = explode(";", str_replace('"', '', str_replace('"', '', $buffer)));
							if ($arrBuff[0] > 100){
								// echo "<********>".$arrBuff[4];
								

								$val = explode(" ", $arrBuff[4]);
								$tipo = explode(" ", $arrBuff[5]);
								$ffac = explode(" ", $arrBuff[6]);
								$fec = explode("/", $ffac[0]);

								$arrDat[0] = $arrBuff[10];													//codigo de autorizo
								$arrDat[3] = $arrBuff[6];													//fecha en human
								$arrDat[2] = $arrBuff[0];													//identificador de la operacion
								$arrDat[1] = strtotime($fec[1]."/".$fec[0]."/".$fec[2]);					//fecha en unix
								$arrDat[5] = $arrBuff[8];													//tarjeta
								$arrDat[7] = tarje('B',$arrBuff[0], $arrBuff[7]);							//tipo de tarjeta
								if ($arrBuff[15] == '200')  $arrDat[6] = 'A';
								else  $arrDat[6] = 'D';														//Estado de la Operación
								$arrDat[4] =  str_replace(",", ".",$val[0]);														//Euros en el banco

								$error .= buscatr($arrDat,888888);
							}
						}
					}
				}
			} else {

			$objPHPExcel = PHPExcel_IOFactory::load($nom);
				$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

				if (
					stripos($sheetData[2]['A'], 'Referencia') !== false
					&& stripos($sheetData[2]['B'], 'Cuenta') !== false
				) {
					for ($i = 3; $i <= count($sheetData); $i++) {
						$ffac = explode(" ", $sheetData[$i]['G']);
						$fec = explode("/", $ffac[0]);
						
						$arrDat[0] = $sheetData[$i]['K'];													//codigo de autorizo
						$arrDat[3] = $sheetData[$i]['G'];													//fecha en human
						$arrDat[2] = $sheetData[$i]['A'];													//identificador de la operacion
						$arrDat[1] = strtotime($fec[1]."/".$fec[0]."/".$fec[2]);							//fecha en unix
						$arrDat[5] = $sheetData[$i]['I'];													//tarjeta
						$arrDat[7] = tarje('B',$sheetData[$i]['A'], $sheetData[$i]['I']);					//tipo de tarjeta
						if ($sheetData[$i]['A'] == '200')  $arrDat[6] = 'A';
						else  $arrDat[6] = 'D';																//Estado de la Operación
						$arrDat[4] = substr($sheetData[$i]['E'], 0, stripos($sheetData[$i]['E'], ' '));		//Euros en el banco

						$error .= buscatr($arrDat);
					}
				}
			}

		} else {

			$psrla = null;
			$handle = fopen($_FILES['fichero']['tmp_name'], 'r');
			if ($_REQUEST['banco'] == 30) { //se va a importar del PayTPV
				$contents = stream_get_contents($handle);
				$dom = new DOMDocument;
	//			echo $contents;
				$dom->loadXML($contents);
				$s = simplexml_import_dom($dom);
	//			echo $s->getAttribute('id');
	//			print_r($s);
	//			echo count($s);
				for ($i=0; $i<count($s); $i++){
					$arrDat = array();
	//				echo $s->row[$i]->status." - ".$s->row[$i]->reference."<br>";
	// 				if ($s->row[$i]->status == "Completado") {
						$str = " ;"
								.to_unix($s->row[$i]->date).";"		//fecha en unix
								.$s->row[$i]->reference.";"			//identificador de la transaccion
								.$s->row[$i]->date.";"				//fecha en human
								.str_replace(",", ".", str_replace(".", "", $s->row[$i]->amountp)).";" //Euros en el banco
								.$s->row[$i]->pan; 					//Número de la tarjeta
	// 					echo $str."<br>";
						$arrDat = explode(";", $str);
	//					print_r($arrDat);
	//					$arrDat[1] = to_unix($s->row[$i]->date);	//fecha en unix
	//					$arrDat[2] = $s->row[$i]->reference;		//identificador de la transaccion
	//					$arrDat[3] = $s->row[$i]->date;				//fecha en human
						
						$error .= paytpvSet($arrDat);
	// 				}
				}
			} else {
				if ($handle) {
					while (!feof($handle)) {
						$arrDat = array();
						$buffer = fgets($handle);
						if (strlen($buffer) > 3) {
							// print_r($buffer);
							if ($_REQUEST['banco'] == 9) {// Stripe
								$buffer = str_replace(',,',',"",', str_replace(',,',',"",', $buffer));
								// $arrBuff = explode('","', $buffer);
								$arrCom = explode('"', $buffer);
								for ($i=0; $i<count($arrCom); $i++) {
									if ($i & 1) $arrCom[$i] = str_replace(",", ".", $arrCom[$i]);
								}
								// var_dump($arrCom);
								$buffer = str_replace('"', '', implode('"',$arrCom));

								$arrBuff = explode(',', $buffer);
								// var_dump($arrBuff);

								if (!stristr($arrBuff[0],'id') ) {
								// var_dump($arrBuff);echo "<br>";
									$fect = explode(" ",$arrBuff[3]);
									$fec = explode("-", $fect[0]);

									$arrDat[0] = $arrBuff[0];															//codigo de autorizo
									$arrDat[3] = $arrBuff[3];															//fecha en human
									$arrDat[1] = strtotime($fec[1]."/".$fec[2]."/".$fec[1]." {$fect[1]}");				//fecha en unix
									$arrDat[5] = str_pad($arrBuff[20], 16, "*", STR_PAD_LEFT);							//tarjeta
									$arrDat[7] = tarje($arrBuff[21], $arrDat[0]);										//tipo de tarjeta
									if (stripos($arrBuff[2], 'complete') > 0)
									$arrDat[6] = 'A';																	//Estado de la Operación
									else $arrDat[6] = 'D';	
									$arrDat[4] = str_replace(',', '.', $arrBuff[7]);									//Euros en el banco
									$arrDat[2] = substr($arrBuff[1], 0, 12);											//identificador de la transaccion
									// var_dump($arrDat);

									$temp->query("select idPasarela from tbl_pasarela where idcenauto = 14");
									$psrla = implode(",",$temp->loadResultArray());

									$error .= buscatr($arrDat, $psrla);
								}
							} else if ($_REQUEST['banco'] == 7) { // Se importa desde Xilema
								$arrBuff = explode(",", str_replace('"', '', str_replace('"', '', $buffer)));
								if (!stristr($arrBuff[0],'ID') ) {
									$fec = explode("/",$arrBuff[3]);

									$arrDat[0] = $arrBuff[25];															//codigo de autorizo
									$arrDat[3] = $arrBuff[3];															//fecha en human
									$arrDat[1] = strtotime($fec[1]."/".$fec[0]."/".$fec[2]);							//fecha en unix
									$arrDat[5] = $arrBuff[7];															//tarjeta
									$arrDat[7] = tarje($arrBuff[10], $arrDat[0]);										//tipo de tarjeta
									$arrDat[6] = 'A';																	//Estado de la Operación
									$arrDat[4] = str_replace(',', '.', $arrBuff[5]);									//Euros en el banco

									$temp->query("select idPasarela from tbl_pasarela where idcenauto = 14");
									$psrla = implode(",",$temp->loadResultArray());

									$error .= buscatr($arrDat, $psrla);
								}
							} else if ($_REQUEST['banco'] == 3) {// Se importa desde PayTpv
								$arrBuff = explode(";", str_replace('"', '', str_replace('"', '', $buffer)));
								if (!stristr($arrBuff[0],'ID') ) {
									$arrDat[0] = $arrBuff[21];															//codigo de autorizo
									$arrDat[1] = time('d/m/Y H:i:s',$arrBuff[1]);										//fecha en unix
									$arrDat[2] = substr($arrBuff[2], 0, 12);											//identificador de la transaccion
									$arrDat[3] = $arrBuff[1];															//fecha en human
									$arrDat[4] = str_replace(',', '.', str_replace(".", "", $arrBuff[10]));				//Euros en el banco
									$arrDat[5] = $arrBuff[6];															//tarjeta
									if ($arrBuff[3] == 'Autorización') {
										if ($arrBuff[4] == 'Completado') $arrDat[6] = 'A';								//Estado de la Operación
										if ($arrBuff[4] == 'Fallida') $arrDat[6] = 'D';
									}
									$arrDat[7] = tarje($arrBuff[16], $arrDat[2]);										//tipo de tarjeta
									$error .= buscatr($arrDat);
								}

							} else if ($_REQUEST['banco'] == 5) { //Se va a importar desde Tefpay
								$arrBuff = explode(",", str_replace('"', '', str_replace('"', '', $buffer)));
								// echo "<br>arrBuff: ";var_dump($arrBuff);
								if (!strstr($arrBuff[6],'Terminal') && !strstr($arrBuff[7],'Terminal')) {
										$arrDat[1] = time('d/m/Y H:i:s',$arrBuff[1]);										//fecha en unix
										$arrDat[3] = $arrBuff[1];															//fecha en human
									if (count($arrBuff) < 14) {
										$arrDat[2] = substr($arrBuff[4], 0, 12);											//identificador de la transaccion
										if (strstr($arrBuff[7], '*')) $arrDat[5] = $arrBuff[6];								//tarjeta
										if ($arrBuff[2] == 'Venta') $arrDat[0] = $arrBuff[7];								//codigo de autorizo
										elseif ($arrBuff[2] == 'Autenticación' && $arrBuff[2] == '-1') $arrDat[0] = '';
									} else {
										// echo "daledale";
										$arrDat[2] = substr($arrBuff[5], 0, 12);											//identificador de la transaccion
										if (strstr($arrBuff[9], '*')) $arrDat[5] = $arrBuff[9];								//tarjeta
										if ($arrBuff[2] == 'Venta') $arrDat[0] = $arrBuff[12];								//codigo de autorizo
										elseif ($arrBuff[2] == 'Autenticación' && $arrBuff[2] == '-1') $arrDat[0] = '';
									}
									$arrDat[7] = tarje('B', $arrDat[2], $arrDat[5]);										//tipo de tarjeta
									$error .= paytpvSet($arrDat);

								// echo "<br>arrDat: ";var_dump($arrDat);echo "<br>";exit;
								}
								
								if (isset($arrDat[0])) $error .= buscatr($arrDat);
							} elseif ($_REQUEST['banco'] == 1) { //se va a importar del Sabadell o Bankia, Caixa, CaixaBank, Navarra, LabKutxa
								// echo $buffer;
								if (!stripos($buffer, ";;;")) {
									$arrBuff = explode(";", $buffer);
									// echo "<br>".json_encode($arrBuff);

									if (!strstr($arrBuff[0],'Fecha')) {
										if (!strstr($arrBuff[6], 'Reintentada') ) {
											// echo "<br>Entra aca";
											// if (!strstr($arrBuff[4],'Pedido') && !strstr($arrBuff[5],'Pedido')) {
											if (strlen($arrBuff[0]) < 5) {
												// echo "<br>entra en uno";
												$arrDat[1] = to_unix($arrBuff[1]." ".$arrBuff[2]);								//fecha en unix
												$arrDat[3] = $arrBuff[1]." ".$arrBuff[2];										//fecha en human

												// if (stristr($arrBuff[3], 'Autorizac') > -1) {									//Estado de la Operación Aceptada o Devuelta
												// 	$arrDat[6] = 'A';
												// } else if (stristr($arrBuff[3], 'Devoluc') > -1) {
												// 	$arrDat[6] = 'B';
												// }

												if (strpos($arrBuff[4], 'Autorizada') > -1)
													$arrDat[0] = str_replace('Autorizada ', '', $arrBuff[4]);					//codigo de autorizo
												else $arrDat[0] = '';
											}
											else {
														// echo json_encode($arrBuff);
												$arrDat[1] = to_unix($arrBuff[0]);												//fecha en unix
													// $arrDat[3] = $arrBuff[0]." ".$arrBuff[1];									//fecha en human
												$arrDat[3] = $arrBuff[0];														//fecha en human

												if (stristr($arrBuff[4], 'Autorizac') > -1) {									//Estado de la Operación 
													$arrDat[6] = 'A';
													if (strpos($arrBuff[6], 'Autorizada') > -1){
														$arrDat[0] = str_replace('Autorizada ', '', $arrBuff[6]);					//codigo de autorizo
														$arrDat[2] = $arrBuff[5];
														if (strtolower($arrBuff[8]) == 'usd') {										//si la operación fué en USD tomo los euros puestos
															$arrDat[7] = $arrBuff[8];		//moneda
															$arrDat[8] = $arrBuff[9];		//euros puestos
														}
													} else {
														$arrDat[0] = '';
														$arrDat[2] = $arrBuff[5];
													}
												} else if (stristr($arrBuff[4], 'Devoluc') > -1) {
													$arrDat[6] = 'B';
													$arrDat[2] = $arrBuff[5];
												}														//fecha en human

												//cajamar
												if (stristr($arrBuff[3], 'Autorizac') > -1) {									//Estado de la Operación 
													$arrDat[6] = 'A';
													if (strpos($arrBuff[5], 'Autorizada') > -1) {
														$arrDat[0] = str_replace('Autorizada ', '', $arrBuff[5]);					//codigo de autorizo
														$arrDat[4] = str_replace(")", "", substr($arrBuff[8], strpos($arrBuff[8], "("))); //euros en el banco
														$arrDat[2] = $arrBuff[4];	
													} else {
														$arrDat[0] = '';
													$arrDat[2] = $arrBuff[4];}
												} else if (stristr($arrBuff[3], 'Devoluc') > -1) {
													$arrDat[6] = 'B';
													$arrDat[2] = $arrBuff[4];	
												}

											}
											// if (strpos($arrBuff[4], ' ')) $arrDat[2] = $arrBuff[5];
											// else $arrDat[2] = $arrBuff[4];														//identificador de la transaccion
											// $arrDat[2] = $arrBuff[5];
											// echo "<br>".$arrBuff[4]." - ".$arrDat[2];
											$arrBuff[9] = str_replace(",", ".", $arrBuff[9]);
											if (strpos($arrBuff[8], 'USD') || strpos($arrBuff[8], 'GBP') || strpos($arrBuff[8], 'CAD')) {
												$arrBuff[9] = str_replace(")", "", substr($arrBuff[9], stripos($arrBuff[9], "(")+1));
											}
											if (strpos($arrBuff[8], 'EUR')) {
												$arrDat[4] = str_replace(" EUR", "", $arrBuff[9]);								//monto de la operac
											// } else {
											// 	$arrDat[4] = str_replace(")", "", substr($arrBuff[9], strpos($arrBuff[9], "(")));				//euros en el banco
											}

											$arrDat[7] = tarje($arrBuff[11], $arrBuff[5]);													//tipo de tarjeta
											// echo "<br>arrDat=". json_encode($arrDat); exit;
											$error .= buscatr($arrDat);
										}
									}
								}
							} elseif ($_REQUEST['banco'] == 6) { //se va a importar desde Titanes
								$arrBuff = explode(";", $buffer);
								if (!strstr($arrBuff[4],'Pedido') && !strstr($arrBuff[5],'Pedido')) {
									$arrDat[1] = to_unix($arrBuff[0]." ".$arrBuff[1]);										//fecha en unix
									$arrDat[3] = $arrBuff[0]." ".$arrBuff[1];												//fecha en human
									if (stripos($arrBuff[3], 'Autorizada') > -1 ) $arrDat[0] = 'Autorizada';		
									else $arrDat[0] = '';																	//codigo de autorizo
									
									$arrDat[2] = substr($arrBuff[12],0,12);													//identificador de la transaccion
									$error .= buscatr($arrDat);
								}

							} else if ($_REQUEST['banco'] == 2) { //se va a importar del Evo
								$arrBuff = explode("; ", $buffer);
								$arrDat[1] = to_unix($arrBuff[6]);															//fecha en unix
								$arrDat[2] = $arrBuff[5];																	//identificador de la transaccion
								$arrDat[3] = $arrBuff[6];																	//fecha en human
								$arrDat[4] = str_replace(',', '.', str_replace(".", "", $arrBuff[16]));						//Euros en el banco
								if (stristr($arrBuff[10],'ERR')) {
									$arrDat[0] = '';																		//codigo de autorizo
									$arrDat[8] = str_replace('ERR-', '', $arrBuff[10]);											//codigo de error
								} else $arrDat[0] = $arrBuff[10];
								$arrDat[7] = tarje($arrBuff[13], $arrBuff[5]);													//tipo de tarjeta
								
								$error .= buscatr($arrDat);
							} else if ($_REQUEST['banco'] == 24) { //se va a importar desde el Caixa de paytpv (CXp 3D)
								$arrBuff = explode(";", $buffer);
									
								$arrDat[0] = str_replace('Autorizada ', '', $arrBuff[4]);									//codigo de autorizo
								$arrDat[1] = to_unix($arrBuff[1]." ".$arrBuff[2]);											//fecha en unix
								$arrDat[2] = $arrBuff[5];																	//identificador de la transaccion
								$arrDat[3] = $arrBuff[1]." ".$arrBuff[2];													//fecha en human
								if (strripos($arrBuff[7], ',') == 5) 
									$arrDat[4] = str_replace(',', '.', str_replace('.', '', $arrBuff[7]));
								else
									$arrDat[4] = str_replace(',', '.', str_replace('.', '', $arrBuff[7]));					//Euros en el banco
								if (strstr($arrBuff[9], '*')) $arrDat[5] = $arrBuff[9];
								elseif (strstr($arrBuff[11], '*')) $arrDat[5] = $arrBuff[11];								//tarjeta
								if (strstr($arrBuff[3], 'utoriza')) $arrDat[6] = 'A';										//Si es Aceptada o Devuelta
								else $arrDat[6] = 'D';
								$pas = "24,20,36,21";
								$error .= buscatr($arrDat, $pas);
							} else if ($_REQUEST['banco'] == 4) { //se va a importar desde Wirecard
								$arrBuff = explode(";", $buffer);
								
								if ($arrBuff[1] == 'cleared') { //Si está aceptada
								
									$arrDat[0] = $arrBuff[0];																//codigo de autorizo
									$arrDat[1] = to_unix($arrBuff[5]);														//fecha en unix
									$arrDat[2] = $arrBuff[10];																//identificador de la transaccion
									$arrDat[3] = $arrBuff[5];	
									$error .= buscatr($arrDat, $pas);														//fecha en human
									
								}
							}
						}
					}
						//echo $buffer."<br>";
					fclose($handle);
				}
			}
		}
	}
}

function paytpvSet($arrEnt) {
	$temp = new ps_DB;
	$error = '';
	
	$q = "update tbl_transacciones set tarjetas = '".$arrEnt[5]."' where idtransaccion = '".$arrEnt[2]."'";
	$temp->query($q);
	// echo $q."<br>";
// 	if ($temp->num_rows() == 0) $error = "ERROR: La operación {$arrEnt[2]} no se encuentra en el Concentrador<br>\n";
	return $error;
}

function tarje ($cont, $id, $num = null) {
	if (_MOS_CONFIG_DEBUG) echo "<br>tarje->$cont - $id - $num <br>";
	$temp = new ps_DB;
	$correo = new correo;

	$arrStr = explode(' ', $cont);

	if ($cont == 'B') {
		if (substr($num, 0, 2) == '34' ||  substr($num, 0, 2) == '37' || substr($num, 0, 3) == '034' ||  substr($num, 0, 3) == '037' ) 
			$cont = 'American Express';
		elseif (substr($num, 0, 1) == '4' ) 
			$cont = 'Visa';
		elseif (substr($num, 0, 2) == '50' ||  (substr($num, 0, 2) >= '56' && substr($num, 0, 2) <= '69') || substr($num, 0, 4) == '6759' ||  substr($num, 0, 6) == '676770' ||  substr($num, 0, 6) == '676774' ) 
			$cont = 'Mastercard Maestro';
		elseif (substr($num, 0, 4) >= '2221' && substr($num, 0, 4) <= '2720' || substr($num, 0, 2) >= '51' && substr($num, 0, 2) <= '55' ) 
			$cont = 'Mastercard';
		elseif (substr($num, 0, 4) >= '3528' && substr($num, 0, 4) <= '3589' ) 
			$cont = 'JCB';
		elseif (substr($num, 0, 2) == '36' ||  substr($num, 0, 2) == '38' ||  substr($num, 0, 2) == '39' ||  substr($num, 0, 4) == '3095' ||  (substr($num, 0, 3) >= '300' && substr($num, 0, 3) <= '305') ) 
			$cont = 'Dinners Club Internacional';
		else 
			$correo->todo(43, 'No se identifica la tarjeta', 'La tarjeta '.$num.' de la operación '.$id.' no se puede identificar');
	}

	if ($cont == 'SafeKey') return 1;
	
	$temp->query("select id from tbl_tarjetas where lower(nombre) = '".strtolower($cont)."'");
	if ($temp->num_rows()) return $temp->f('id');
	
	for ($i=0; $i<count($arrStr); $i++){
		$temp->query("select id from tbl_tarjetas where lower(nombre) like '%".strtolower($arrStr[$i])."%'");
		if ($temp->num_rows()) return $temp->f('id');
	}

	if (stripos(strtolower($cont), 'visa') > -1) return 2;
	if (stripos(strtolower($cont), 'master') > -1) return 3;
	if (stripos(strtolower($cont), 'amex') > -1 || stripos(strtolower($cont), 'american') > -1) return 1;

	if ($cont != 'Tradicional'
		&& $cont != 'ServiRed-Finanet'
		&& $cont != 'IUPAY!'
	) 
		$correo->todo(43, 'No se encuentra la tarjeta', 'La tarjeta '.$cont.' de la operación '.$id.' no se encuentra en el listado del Concentrador');

	return 0;
}

function buscatr($arrEnt, $pas=null) {
		// echo "<br>pas=$pas";print_r($arrEnt);echo"<br>";
		global $psrla;
		$temp = new ps_DB;
		$arrCub = array();
		$inst = false;
		$moneur = 0;
		$error = '';
//		var_dump($arrEnt);
		if ($pas == null) $pas = $psrla;
		$fecha1 = $arrEnt[1]-(60*60*24);
		$fecha2 = $arrEnt[1]+(60*60*24);
		if (stripos($pas, '94')) { //varío las fechas en +- un día para Xilema
			$fecha1 = $arrEnt[1]-(60*60*24*2);
			$fecha2 = $arrEnt[1]+(60*60*24*2);
		}
		$fec = date('dmy', $arrEnt[1]);
		if ($pas == 888888) {
			$q = "select m.moneda, from_unixtime(t.fecha,'%d/%m/%y') fec, t.identificador, t.idcomercio, c.nombre, t.estado, p.nombre pasar, valor, 
					t.euroEquiv, t.idtransaccion, t.pasarela, t.codigo, t.id_tarjeta, p.idcenauto "
					. "from tbl_transacciones t, tbl_moneda m, tbl_comercio c, tbl_pasarela p "
					. "where p.idPasarela = t.pasarela and c.idcomercio = t.idcomercio and m.idmoneda = t.moneda and t.idtransaccion = '".$arrEnt[2]."'";
		}elseif (strlen($arrEnt[2]) == 12) {
			$q = "select m.moneda, from_unixtime(t.fecha,'%d/%m/%y') fec, t.identificador, t.idcomercio, c.nombre, t.estado, p.nombre pasar, valor, 
					t.euroEquiv, t.idtransaccion, t.pasarela, t.codigo, t.id_tarjeta, p.idcenauto "
					. "from tbl_transacciones t, tbl_moneda m, tbl_comercio c, tbl_pasarela p "
					. "where p.idPasarela = t.pasarela and c.idcomercio = t.idcomercio and m.idmoneda = t.moneda and t.idtransaccion = '".$arrEnt[2]."'";
			//if (strlen($arrEnt[0]) > 1) $q .= " and t.codigo = '".$arrEnt[0]."' ";
		} elseif ($pas != 888888) {
			if ($pas == '') null; //sendTelegram(json_encode($arrEnt));
			else
			$q = "select m.moneda, from_unixtime(t.fecha,'%d/%m/%y') fec, t.identificador, t.idcomercio, c.nombre, t.estado, p.nombre pasar, valor,
					t.euroEquiv, t.idtransaccion, t.pasarela, t.id_tarjeta, p.idcenauto
					from tbl_transacciones t, tbl_moneda m, tbl_comercio c, tbl_pasarela p 
					where p.idPasarela = t.pasarela 
						and c.idcomercio = t.idcomercio and 
						m.idmoneda = t.moneda and 
						t.codigo = '".$arrEnt[0]."' and 
						t.pasarela in ($pas) and
						t.fecha between ".($fecha1)." and ".($fecha2);
		}
	// echo $q."<br>";
		$temp->query($q);
		if ($temp->num_rows() == 1) { // la transacción se encuentra en el Concentrador recojo sus datos
			$arrCub[0] = $temp->f('estado');
			$arrCub[1] = $temp->f('moneda');
			$arrCub[2] = $temp->f('identificador');
			$arrCub[3] = $temp->f('nombre');
			$arrCub[4] = $temp->f('idcomercio');
			$arrCub[5] = $temp->f('pasar');
			$arrCub[6] = $temp->f('euroEquiv');
			$arrCub[7] = $temp->f('idtransaccion');
			$psrla = $temp->f('pasarela');
			$arrCub[8] = $arrEnt[3];
			$arrCub[9] = $temp->f('codigo');
			$arrCub[10] = $temp->f('valor');
			$arrCub[11] = $temp->f('id_tarjeta');
			$arrCub[12] = $temp->f('idcenauto');

			//si la operación es de Tocopay calculo la tasa de cambio para el día siguiente
			if ($arrCub[4] == '163430526040' && $arrCub[1] = '840') {
				if ($arrEnt[8] > 0) {
					$cambio = $arrCub[10]/100/$arrEnt[8];
					$temp->query("select valor, from_unixtime(fecha, '%d%m%y') fecha from tbl_setup where nombre = 'cambioCimex'");
					$val = $temp->f('valor');
					$fec = $temp->f('fecha');
					// error_log("Val=$val / CAMBIO=$cambio / transaccion=".$arrCub[7]);

					if ($fec != date('dmy')) {
						$val = 0;
						actSetup($val, 'cambioCimex');
					}
					// error_log("CAMBIO=$cambio / fecha=$fec / date=".date('dmy')." / val=$val / transaccion=".$arrCub[7]);
					if ($val < $cambio) actSetup($cambio, 'cambioCimex');
					// echo "<br>".$q."<br>".$cambio;exit;
				}
			}

	// print_r($arrCub);
			$acep = false;
			if ($arrCub[0] == 'A' || $arrCub[0] == 'B' || $arrCub[0] == 'V' || $arrCub[0] == 'R') $acep = true;
			if (!$acep &&  strlen($arrEnt[0]) > 3) {// la operación no está Aceptada en el Concentrador pero viene Aceptada del banco
				$error .= "<strong>ERROR:</strong> La operación ".$arrCub[7]." de ".$arrCub[3]." que se realizó por {$arrCub[5]} está Aceptada en Banco pero no en el Concentrador!<br>\n";
			} elseif ($acep &&  strlen($arrEnt[0]) == 0) { //operación Aceptada en el Concentrador y denegada en Banco
				$error .= "<strong>ERROR:</strong> La operación ".$arrCub[7]." de ".$arrCub[3]." que se realizó por {$arrCub[5]} está Denegada en Banco y Aceptada en el Concentrador!<br>\n";
			}

			//procesa las operaciones que fueron Denegadas en banco pero se mantienen en proceso o no procesada en el Concentrador
			if ($acep == false && $arrCub[0] != 'D' && isset($arrEnt[8])) {
				// echo $q . "<br>";

				switch ($arrCub[12]) {
					case '8':
					case "16":
						$par = 12;
						break;
					case '17':
					case '3':
						$par = 100;
						break;
					default:
						# code...
						break;
				}
				// echo "cub=".$arrCub[12]."<br>par=$par<br>";
				$q = "update tbl_transacciones t, tbl_reserva r set t.estado = 'D', r.estado = 'D', t.id_error = (select texto from tbl_errores where codigo = ".($arrEnt[8]*1). " and idpasarela = $par limit 0,1) where t.idtransaccion = r.id_transaccion and t.idtransaccion = '" . $arrEnt[2] . "'";
				$temp->query($q);
				$error .= "<strong>Aviso:</strong> La operación " . $arrCub[7] . " de " . $arrCub[3] . " que se realizó por {$arrCub[5]} pasó de ". $arrCub[0]." a D como aparece en el Banco!<br>\n";
				// echo $q . "<br>";
			}
			
			//Verifica que la operación tenga el estado correcto
			if ($arrEnt[6] == 'B'){
				if ($arrCub[0] == 'A' ) {
					$error .= "<strong>ERROR:</strong> La operación ".$arrCub[7]." de ".$arrCub[3]." que se realizó por {$arrCub[5]} está Aceptada en el Concentrador pero devuelta en el Banco!<br>\n";
				}
			} elseif($arrEnt[6] == 'A') {
				if ($arrCub[0] == 'B' || $arrCub[0] == 'V' || $arrCub[0] == 'R' ) {
					$error .= "<strong>ERROR:</strong> La operación ".$arrCub[7]." de ".$arrCub[3]." que se realizó por {$arrCub[5]} está Devuelta en el Concentrador pero aceptada en el Banco!<br>\n";
				}
			}

			if ($psrla == 115 && $arrCub[0] == 'A' && $arrCub[9] == '') {
				$q = "update tbl_transacciones set codigo = '{$arrEnt[0]}' where idtransaccion = '".$arrEnt[2]."'";

			} 
	// echo "vale";
			if (isset($arrEnt[4]) && $arrCub[1] != 'EUR') {
				$moneur = $arrEnt[4]*100;
				if ( $arrCub[6] > $arrEnt[4] && $arrCub[0] == 'A') $error .= "La operación {$arrCub[7]} en {$arrCub[1]} tiene una diferencia de {$arrEnt[4]}"
						. " - $arrCub[6]= ".  number_format(($arrCub[6] - $arrEnt[4]),2)." Euros con respecto al banco $arrCub[5] se debe revisar las tasas de cambio.<br>\n";
			}
			
			if (strlen($arrCub[7]) > 1 ) {
				if ($arrEnt[7] > 0 && $arrCub[11] != 11) $sql1 = ", id_tarjeta = '{$arrEnt[7]}' ";
				if (strlen($arrCub[9]) == 0) $sql2 = ", codigo = '{$arrEnt[0]}'";
				if (strlen($arrEnt[5]) > 3) $sql3 = ", tarjetas = '".$arrEnt[5]."' ";
				$q = "update tbl_transacciones set mtoMonBnc = $moneur $sql1 $sql3 $sql2  where idtransaccion = '".$arrCub[7]."'";
				$temp->query($q);
			}
			
		} elseif ($temp->num_rows() == 0) $error .= "<strong>ERROR:</strong> La operación {$arrEnt[2]} no se encuentra en el Concentrador<br>\n";
//		echo $error;
		return $error;
	}

if (strlen($error)) {
	echo "<div style='color:red;text-align:center'> $error <br><br>Fué enviado un correo con todos estos errores a Julio</div>";
	$correo->todo(39, 'Transacciones con problemas en el banco', $error);
} elseif (strlen($_FILES['fichero']['tmp_name']) > 3) 
	echo "<div style='color:green;text-align:center'> La comprobación fué exitosa, no hay problemas</div>";


function acepAis($arrEntr) {
//	print_r($arrEntr);
	$temp = new ps_DB;
	$corCreo = new correo;
	
	$text = "Se necesita devolver completa esta operación en {$arrEntr[1]} del comercio ".$arrEntr[3]." con identificador ".$arrEntr[2].
			", la misma tiene número ".$arrEntr[7]." en el TPV ".$arrEntr[5].", fué realizada el día ".$arrEntr[8].
			"\n\nDebe tener en cuenta que se realiza por haber quedado en proceso en el Concentrador, por lo que los cargos de la devolución no deben correr a cuanta del comercio\n";
	$corCreo->todo(29,"Solicitud de devolución de Transacción",$text);
}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_SINCR;
$html->tituloTarea = '&nbsp;';
$html->anchoTabla = 750;
$html->tabed = true;
$html->anchoCeldaI = 105;
$html->anchoCeldaD = 610;

$html->inHide('../desc', 'UPLOAD_TMP_DIR');
$html->inTextoL('<input type="file" name="fichero" id="fichero"  />');
$valInicio = array(
				array('1', 'Bankia, Sabadell, Navarra, AndBank, BancaMarch, LabKutxa, VallBank, BancaSabadell, CaixaGeral, Triodos'), 
				array('2', 'Abanca o Ibercaja'), 
				array('5', 'Tefpay'),
				array('3', 'PayTPV'), 
				// array('4', 'Wirecard'), 
				// array('6', 'Titanes'), 
				array('7', 'Xilema'), 
				array('8', 'Papam'), 
				array('9', 'Stripe')
);
$html->inSelect('Banco', 'banco', 3, $valInicio, '1');
//$html->inHide(1, 'banco');


echo $html->salida();

//echo CURL_TIMECOND_LASTMOD;
?>
<script type="text/javascript">
function verifica(){
	return true;
}
</script>

