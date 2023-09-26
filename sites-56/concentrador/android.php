<?php

// author: Raisel Alfonso Ledon
// email: raissell@gmail.com

	header("Cache-Control: no-cache");
	header("Pragma: no-cache");

	function extractString($response, $prefix, $sufix) {
	  $start = strpos($response, $prefix);
		if ($start !== false) {
		  $start += strlen($prefix);
			$end = strpos($response, $sufix, $start);
			if ($end !== false) 
				return substr($response, $start, $end - $start);
		}
		return false;
	}

  function fail($codigo, $mensaje) {
		echo "<result code=\"".$codigo."\">";
		echo "<message>".$mensaje."</message>";
		echo "</result>";
	}
	function parse6($response) {
		$importe = extractString($response, 'importe">', '<');
		if ($importe !== false) {
			$importe = str_replace('&nbsp;', ' ', $importe);
			$comercio = extractString($response, 'Comercio:</td><td class="gray">', '<');
			if ($comercio !== false) {
				$terminal = extractString($response, 'Terminal:</td><td class="gray">', '<');
				if ($terminal !== false) {
					$pedido = extractString($response, 'pedido:</td><td class="gray">', '<');
					if ($pedido !== false) {
						$tarjeta = extractString($response, 'Tarjeta:</td><td class="importe">', '<');
						if ($tarjeta !== false) {
							$tarjeta = trim($tarjeta);
							$fecha = extractString($response, 'Fecha:</td><td class="gray">', '<');
							if ($fecha !== false) {
								$fecha = str_replace("\n", "", $fecha);
								$fecha = str_replace("\t", "", $fecha);
								$hora = extractString($response, 'Hora:</td><td class="gray">', '<');
								if ($hora !== false) {
									$hora = str_replace("\n", "", $hora);									
									$hora = str_replace("\t", "", $hora);									
									$hora = str_replace(" ", "", $hora);									
									$message = extractString($response, "msgs\">\n<p>", '<');
									if ($message !== false) {
										$message = utf8_encode(html_entity_decode($message));
										echo '<result code="0">';
										echo '<message>'.$message.'</message>';
										echo '<items>';
										echo '<item name="Importe" value="'.$importe.'"/>';
										echo '<item name="Comercio" value="'.$comercio.'"/>';
										echo '<item name="Terminal" value="'.$terminal.'"/>';
										echo '<item name="Pedido" value="'.$pedido.'"/>';
										echo '<item name="Tarjeta" value="'.$tarjeta.'"/>';
										echo '<item name="Fecha" value="'.$fecha.'"/>';
										echo '<item name="Hora" value="'.$hora.'"/>';
										echo '</items>';
										echo '</result>';
										return true;					
									}
								}
							}
						}
					}
				}
			}	
		}	
		return false;
	}
	function post6($url, $fields) {
	  global $userAgent;
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$response = curl_exec($ch);
		if ($response === false) {
		  fail(2, "Error de conexion remota");
		}	else {
			file_put_contents("response6.html", $response); // Debug
			if (!parse6($response))
				fail(3, "Error procesando respuesta6");
		}
	}
	function parse5($response) {
	  $url = extractString($response, 'action="', '"');
		if ($url !== false) {
			$Sis_Resultado_Autenticacion = extractString($response, 'Sis_Resultado_Autenticacion" value="', '"');
			if ($Sis_Resultado_Autenticacion !== false) {
				$fields = "Sis_Resultado_Autenticacion=".$Sis_Resultado_Autenticacion;
				$Sis_Signature = extractString($response, 'Sis_Signature" value="', '"');
				if ($Sis_Signature !== false) {
					$fields .= "&Sis_Signature=".$Sis_Signature;
					post6($url, $fields);
					return true;
				}	
			}	
		}	
		return false;
	}
	function post5($fields) {
	  global $userAgent;
		
		$url = "https://sas.sermepa.es/sas/SerSvlFinanetDirecto";
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$response = curl_exec($ch);
		if ($response === false) {
		  fail(2, "Error de conexion remota");
		}	else {
			file_put_contents("response5.html", $response); // Debug
			if (!parse5($response))
				fail(3, "Error procesando respuesta5");
		}
	}
	function parse4($response) {
		$Sis_Numero_Tarjeta = extractString($response, 'Sis_Numero_Tarjeta" type="hidden" value="', '"');
		if ($Sis_Numero_Tarjeta !== false) {
		  $fields = "Sis_Numero_Tarjeta=".$Sis_Numero_Tarjeta;
			$Sis_Caducidad_Tarjeta = extractString($response, 'Sis_Caducidad_Tarjeta" type="hidden" value="', '"');
			if ($Sis_Caducidad_Tarjeta !== false) {
			  $fields .= "&Sis_Caducidad_Tarjeta=".$Sis_Caducidad_Tarjeta;
				$Sis_Signature = extractString($response, 'Sis_Signature" type="hidden" value="', '"');
				if ($Sis_Signature !== false) {
				  $fields .= "&Sis_Signature=".$Sis_Signature;
					$Sis_Resultado_Autenticacion = extractString($response, 'Sis_Resultado_Autenticacion" type="hidden" value="', '"');
					if ($Sis_Resultado_Autenticacion !== false) {
					  $fields .= "&Sis_Resultado_Autenticacion=".$Sis_Resultado_Autenticacion;
						$Ds_Merchant_MerchantSignature = extractString($response, 'Ds_Merchant_MerchantSignature" type="hidden" value="', '"');
						if ($Ds_Merchant_MerchantSignature !== false) {
						  $fields .= "&Ds_Merchant_MerchantSignature=".$Ds_Merchant_MerchantSignature;
							$Ds_Merchant_MerchantCode = extractString($response, 'Ds_Merchant_MerchantCode" type="hidden" value="', '"');
							if ($Ds_Merchant_MerchantCode !== false) {
							  $fields .= "&Ds_Merchant_MerchantCode=".$Ds_Merchant_MerchantCode;
								$Ds_Merchant_MerchantName = extractString($response, 'Ds_Merchant_MerchantName" type="hidden" value="', '"');
								if ($Ds_Merchant_MerchantName !== false) {
								  $fields .= "&Ds_Merchant_MerchantName=".urlencode($Ds_Merchant_MerchantName);
									$Ds_Merchant_Terminal = extractString($response, 'Ds_Merchant_Terminal" type="hidden" value="', '"');
									if ($Ds_Merchant_Terminal !== false) {
									  $fields .= "&Ds_Merchant_Terminal=".$Ds_Merchant_Terminal;
										$Ds_Merchant_Order = extractString($response, 'Ds_Merchant_Order" type="hidden" value="', '"');
										if ($Ds_Merchant_Order !== false) {
										  $fields .= "&Ds_Merchant_Order=".$Ds_Merchant_Order;
											$Ds_Merchant_Amount = extractString($response, 'Ds_Merchant_Amount" type="hidden" value="', '"');
											if ($Ds_Merchant_Amount !== false) {
											  $fields .= "&Ds_Merchant_Amount=".$Ds_Merchant_Amount;
												$Ds_Merchant_Currency = extractString($response, 'Ds_Merchant_Currency" type="hidden" value="', '"');
												if ($Ds_Merchant_Currency !== false) {
												  $fields .= "&Ds_Merchant_Currency=".$Ds_Merchant_Currency;
													$Sis_URLRetorno = extractString($response, 'Sis_URLRetorno" type="hidden" value="', '"');
													if ($Sis_URLRetorno !== false) {
													  $fields .= "&Sis_URLRetorno=".urlencode($Sis_URLRetorno);
														$Sis_Idioma = extractString($response, 'Sis_Idioma" type="hidden" value="', '"');
														if ($Sis_Idioma !== false) {
														  $fields .= "&Sis_Idioma=".$Sis_Idioma;
															post5($fields);
															return true;
														}	
													}	
												}	
											}	
										}	
									}	
								}	
							}	
						}	
					}	
				}	
			}	
		}
		return false;
	}
	function post4($url) {
	  global $entrada;
	  global $cookies;
	  global $userAgent;

		/*
		$headers = array(
			'Connection: keep-alive',
			'Accept-Encoding: gzip, deflate'
		);
		if (count($cookies) > 0) {
			array_push($cookies, "Cookie: " + $cookies[0]);
		}
		*/
		//$url = "https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_SimuladorSISAut3D.jsp";
		$url = "https://sis.sermepa.es/sis/entradaXMLBBVA";
		//$url = "http://localhost:8080/test.php";
		/*
		$fields = array(
			'entrada' => $entrada,
			'idioma' => "es",
			'pais' => "ES"
		);
		*/
		$fields = "entrada=".urlencode($entrada)."&idioma=es&pais=ES";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$response = curl_exec($ch);
		if ($response === false) {
		  fail(2, "Error de conexion remota");
		}	else {
			file_put_contents("response4.html", $response); // Debug
			if (!parse4($response))
				fail(3, "Error procesando respuesta4");
		}
		curl_close($ch);
	}
	function parse3($response, $headerSize) {
	  global $cookies;
		
		$headers = substr($response, 0, $headerSize);
		$prefix = "Set-Cookie: ";
		$start = strpos($headers, $prefix);
		while ($start !== false) {
		  $start += strlen($prefix);
			$end = strpos($headers, "\r\n", $start);
			if ($end !== false) {
				$line = substr($headers, $start, $end - $start);
				$fields = explode("; ", $line);
				for ($i = 0; $i < count($fields); $i++) {				  
					if (strncmp(strtolower($fields[$i]), "path=", 5) != 0) {
						array_push($cookies, $fields[$i]);
					}
				}
				$start = strpos($headers, $prefix, $end + 2);
			} else
				break;
		}	
	  //var_dump($cookies);
		return false;
	}
	function post3() {
	  global $peticion;
	  global $userAgent;
	  global $entrada;
		
		$fields = "numtarjeta=".@$_POST['numtarjeta']."&mescad=".@$_POST['mescad']."&aniocad=".@$_POST['aniocad']."&cvv2=".@$_POST['cvv2']."&peticion=".urlencode($peticion)."&accion=HACER_PAGO_PAGO3D&canal=1&soporte=1";
		$url = "https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_RecepOpModeloServidor";
		//$url = "http://localhost:8080/test.php";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($ch, CURLOPT_HEADER, true);
		//curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		//curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$response = curl_exec($ch);
		if ($response === false) {
		  fail(2, "Error de conexion remota");
		}	else {
			file_put_contents("response3.html", $response); // Debug
			
			//$info = curl_getinfo($ch);
			//parse3($response, $info['header_size']);
			
			$url = extractString($response, "ACTION=\"", "\"");
			$entrada = extractString($response, "value=\"", "\"");
			if ($url !== false && $entrada !== false) {
				post4($url);
			}	else
				fail(3, "Error procesando respuesta3");
		}
		curl_close($ch);
	}

	function parse2($response) {
		return strpos($response, "Introduzca sus datos") !== false;
	}
	
	function post2() {
	  global $peticion;
	  global $userAgent;
		
		$url = "https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_RecepOpModeloServidor";
		//$url = "http://localhost:8080/test.php";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		//curl_setopt($ch, CURLOPT_HEADER, true);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "peticion=".urlencode($peticion));
		$response = curl_exec($ch);
		if ($response === false) {
		  fail(2, "Error de conexion remota");
		}	else {
			file_put_contents("response2.html", $response); // Debug
			if (parse2($response)) {
				post3();
			}	else
				fail(3, "Error procesando respuesta2");
		}
		curl_close($ch);
	}

	function parse1($response) {
	  global $peticion;
		
		$peticion = extractString($response, "value=\"", "\"");
		if ($peticion !== false) {
			$peticion = str_replace("&lt;", "<", $peticion);
			$peticion = str_replace("&gt;", ">", $peticion);
			return true;
		}
		return false;
	}
	
  function post1() {
	  global $comercio;
		global $importe;
		global $moneda;
		global $palabra;
	  global $userAgent;
		
		$transaccion = "03".date("mdHis");
		$operacion = 'P';
		$firma = md5($comercio.$transaccion.$importe.$moneda.$operacion.$palabra);
		$pasarela = 15;
		$fields = array(
			'comercio' => $comercio,
			'transaccion' => $transaccion,
			'importe' => $importe,
			'moneda' => $moneda,
			'operacion' => $operacion,
			'pasarela' => $pasarela,
			'firma' => $firma
		);
		$url = "https://www.concentradoramf.com/index.php";
		//$url = "http://localhost:8080/test.php";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		//curl_setopt($ch, CURLOPT_HEADER, true);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$response = curl_exec($ch);
		if ($response === false) {
		  fail(2, "Error de conexion remota");
		}	else {
			file_put_contents("response1.html", $response); // Debug
			if (parse1($response)) {
			  post2();  
			}	else
				fail(3, "Error procesando respuesta1");
		}
		curl_close($ch);
	}

	$palabra = "FjswqLm6rNu3F27nGrcM";

	$comercio = @$_POST['comercio'];
	$importe = @$_POST['importe'];
	$moneda = @$_POST['moneda'];
	$firma = @$_POST['firma'];

	$cookies = array();
	$peticion = "";
	$entrada = "";
	$userAgent = "Mozilla/5.0 (Windows NT 6.1; rv:23.0) Gecko/20100101 Firefox/23.0";
	//$userAgent = "Mozilla/5.0 (Linux; U; Android 2.2; es-es; sdk Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1";
	//$userAgent = "Mozilla/5.0 (Linux; U; Android 2.3.7; es-es; MB501 Build/GRJ22; CyanogenMod-7.2.0) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1";

	header("Content-type: text/xml");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
	/*
	$codigo = 0;
	$mensaje = "La compra se ha realizado satisfactoriamente";

	echo "<result code=\"".$codigo."\">";
	echo "<message>".$mensaje."</message>";
	echo "<items>";
	echo "<item name=\"Comercio\" value=\"AMF\"/>";
	echo "<item name=\"Importe\" value=\"1.00 EUR\"/>";
	echo "<item name=\"Tarjeta\" value=\"0123456789\"/>";
	echo "</items>";
	echo "</result>";
	*/
	/*
	$response = file_get_contents("response6.html");
	if (parse6($response)) {// Debug
		
	}
	*/
	set_time_limit(0);
	if (strcmp($firma, md5($comercio.$importe.$moneda.$palabra)) == 0) {
		post1();
	}	else {
		fail(1, "Error de firma");
	}	
?>
