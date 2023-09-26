<?php
define('_VALID_ENTRADA', 1);
include_once('configuration.php');
include_once('include/mysqli.php');
include_once('admin/adminis.func.php');
require_once('include/correo.php');
require_once('include/class.filetotext.php');

//xdebug_break();
$temp = new ps_DB;
$corCreo = new correo();
date_default_timezone_set('Europe/Berlin');
$horaTasa = mktime(14, 0, 0, date("m"), date("d"), date("Y"));
$arrCamb = array();

	$correoMi = "<br>Pone la tasa de día $horaTasa <= ".time()."<br>";
    $correoMi .= "Entra en G14<br>";
	//Revisa que no se haya puesto la hora ya
	$correoMi .= leeSetup('fechaTasa')."<br>";
	 if (
		 leeSetup('fechaTasa') <= $horaTasa
		//   || 1==1
	 ) {
			$correoMi .= "Entra en horaTasa<br>";
//		echo "entra";
		$correoMi .= "<br>Busca y pone la tasa de cambio del día<br>";
		//Pone la tasa del día
		$error = $arrtaant = array();
		actSetup('0', 'envioSMScamb'); //actualiza la tabla setup caso que haya que enviarme sms
		$q = "select moneda from tbl_moneda where activo = 1";
        $correoMi .= $q."<br>";
		$temp->query($q);
		$den = $temp->loadResultArray();
		$tasaInc = leeSetup('incBCE'); //incremento sobre la tasa de cambio
		$correoMi .= "Incremento sobre la tasa de cambio $tasaInc<br>";

		/**
		 * BancaMarch
		 */
		if (1==0) {
			//carga el documento de BancaMarch en una variable para descargarlo una sóla vez
			$docObj = new Filetotext("https://www.bancamarch.es/recursos/doc/bancamarch/20141009/situcion-mercado/valores-cambio-de-moneda.pdf");
			// $docObj = new Filetotext("valores.pdf");
			$BMDoc = $docObj->convertToText();
            $correoMi .= "Trabaja con BancaMarch";
			$banco = "bancamarch";
			// echo $BMDoc."<br><br>";
			// var_dump($BMDoc);echo"<br><br>";
			$arrVals = explode("\n",$BMDoc);

			if (count($arrVals) < 5) {
                $correoMi .= "No devolvió valores en BancaMarch<br>";
                $error[] = "No devolvió valores en BancaMarch<br>";
			}

			$arrMone = array(
                '840'=>'USD',
                '124'=>'CAD',
                '826'=>'GBP'
                );
			foreach ($arrMone as $key => $moneda) {
				for ($i=0; $i<count($arrVals); $i++) {
					if (stripos($arrVals[$i], $moneda) > -1) $arrCamb[$banco][$moneda] = str_replace(",", ".", $arrVals[$i+3]);
				}
				// echo "$i->".$arrVals[$i]."<br>";
			}
            
		}

		/**
		 * Bankinter
		 */
        if (1==1) {
            $correoMi .= "Trabaja con Bankinter";
            $banco = "bankinter";
			// curl -X POST 'https://broker.bankinter.com/www/es-es/cgi/broker+divcalc?origen=001&destino=103&cantidad=1'

            $arrMone = array(
                '840'=>'USD',
                '124'=>'CAD',
                '826'=>'GBP'
                );

			foreach ($arrMone as $key => $moneda) {
				$url = "https://broker.bankinter.com/www/es-es/cgi/broker+divcalc?origen=001&cantidad=1";
				switch ($moneda) {
					case 'USD':
						$url .= "&destino=103";
						$quita = "DOLARES USA";
						break;
					case 'GBP':
						$url .= "&destino=102";
						$quita = "LIBRAS ESTERLINAS";
						break;
					case 'CAD':
						$url .= "&destino=104";
						$quita = "DOLARES CANADIENSES";
						break;
				}

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

				$res = curl_exec($ch);

				curl_close($ch);

				// $res = file_get_contents($url);
				$res = mb_convert_encoding($res, 'HTML-ENTITIES', 'UTF-8');
				libxml_use_internal_errors(true);
				$dom = new DomDocument('1.0', 'UTF-8');
				$dom->substituteEntities = TRUE;
				$dom->loadHTML($res);
				$xpath = new DOMXPath($dom);
				libxml_clear_errors();

				$node = $xpath->query('//div[@class="for_instruccion1_01"]');
				$childNodes = $xpath->query('div', $node[0]);
				$valorss = str_replace(":", "", trim(str_replace($quita, "", str_replace("Importe en la divisa de destino", "", str_replace("\t", "", str_replace("\n", "", $childNodes->item(2)->nodeValue)))), chr(0xC2).chr(0xA0)));
				$arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $valorss));
			}
		}

		/**
		 * Ingenieros
		 */
        if (1==1) {
            $correoMi .= "Trabaja con Ingenieros";
            $banco = "ingeniero";
			//la url desde donde se escoge la de abajo es: https://www.caixaenginyers.com/es/bolsas-mercados#c_817977-feature-tab2
			$url = "https://www.norbolsa.es/NASApp/norline/JumpServlet?PNBPOR=por3&PNBFMT=html&PNBINS=3025&PNBJMP=divisas";

			$res = file_get_contents($url);
			$res = mb_convert_encoding($res, 'HTML-ENTITIES', 'UTF-8');
			libxml_use_internal_errors(true);
			$dom = new DomDocument('1.0', 'UTF-8');
			$dom->substituteEntities = TRUE;
			$dom->loadHTML($res);
// echo $dom->saveHTML();
			$xpath = new DOMXPath($dom);
			libxml_clear_errors();

            $arrMone = array(
                '840'=>'USD',
                '124'=>'CAD',
                '826'=>'GBP'
                );

			$node = $xpath->query('//*[@class="columna1d2"]/table/tbody');
			$childNodes = $xpath->query('tr/td', $node[0]);
			foreach ($arrMone as $key => $moneda) {
				if ($moneda == 'USD') $arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $childNodes->item(2)->nodeValue));
				if ($moneda == 'GBP') $arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $childNodes->item(6)->nodeValue));
				if ($moneda == 'CAD') $arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $childNodes->item(42)->nodeValue));
			}
		}

		/**
		 * Kutxabank
		 */
        if (1==1) {
            $correoMi .= "Trabaja con Kutxabank";
            $banco = "kutxabank";
			$url = "https://portal.kutxabank.es/cs/jsp/divisas/divisas.jsp?primera=true&idioma=es&iframe=true";

			$res = file_get_contents($url);
			$res = mb_convert_encoding($res, 'HTML-ENTITIES', 'UTF-8');
			libxml_use_internal_errors(true);
			$dom = new DomDocument('1.0', 'UTF-8');
			$dom->substituteEntities = TRUE;
			$dom->loadHTML($res);
			$xpath = new DOMXPath($dom);
			libxml_clear_errors();

            $arrMone = array(
                '840'=>'USD',
                '124'=>'CAD',
                '826'=>'GBP'
                );

			$node = $xpath->query('//*[@id="divisas"]/table/tbody');
			$childNodes = $xpath->query('tr/td', $node[1]);

			foreach ($arrMone as $key => $moneda) {
				if ($moneda == 'USD') $arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $childNodes->item(65)->nodeValue));
				if ($moneda == 'GBP') $arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $childNodes->item(25)->nodeValue));
				if ($moneda == 'CAD') $arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $childNodes->item(5)->nodeValue));
			}
		}


		/**
		 * Triodos
		 */
        if (1==1) {
            $correoMi .= "Trabaja con Triodos";
            $banco = "triodos";
            $all_tags = parser("https://www.triodos.es/es/tipos-cambio-divisas/");
            $arrMone = array(
                '840'=>'USD',
                '124'=>'CAD',
                '826'=>'GBP',
                '756'=>'CHF',
                '392'=>'JPY',
                '484'=>'MXN'
                );

            if (count($all_tags) >= 1) {
                $resultad = $all_tags['span'];
                foreach ($arrMone as $key => $moneda) {
                    for ($i=0; $i<count($resultad); $i++) {
                        if (utf8_decode($resultad[$i]['text']) == $key){
                            $arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $resultad[($i+3)]['text']));
                        }
                    }
                }
            }
		}

		/**
		 * LaboralKutxa
		 */
        if (1==1) {
            $correoMi .= "Trabaja con Laboral";
            $banco = "laboral";
            $all_tags = parser("https://www.laboralkutxa.com/es/particulares/gestion-diaria/operar/cambio-de-moneda");
            $arrMone = array(
                '-DOLARES U.S.A.'=>'USD',
                '-DOLARES CANADIENSES'=>'CAD',
                '-LIBRAS ESTERLINAS'=>'GBP',
                '-FRANCOS SUIZOS'=>'CHF',
                '-YENS JAPONESES'=>'JPY',
                '-PESO MEXICANO'=>'MXN',
                '-RUPIA INDIA'=>'INR',
                '-SOL PERUANO'=>'PEN'
                );
            if (count($all_tags) >= 1) {
                $resultad = $all_tags['td'];
                foreach ($arrMone as $key => $moneda) {
                    for ($i=0; $i<count($resultad); $i++) {
                        if (utf8_decode($resultad[$i]['text']) == $key){
                            $arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $resultad[($i+1)]['text']));
                        }
                    }
                }
            }
		}

		/**
		 * Sabadell
         * IMPORTANTE NO SE TRABAJA CON SABADELL NO CAMBIAR LA DESIGUALDAD DE ABAJO
		 */
        if (1==0) {
            $correoMi .= "Trabaja con Sabadell<br>";
            $banco = 'sabadell';
            $all_tags = parser("https://www.sabadellconsumer.com/cs/Satellite?cid=1191409940011&pagename=BSMarkets2%2FPage%2FPage_Interna_WFG_Template&language=es_ES");
            $arrMone = array( 'USD', 'GBP', 'JPY', 'CHF', 'CAD');
    //		var_dump($all_tags);
            if (count($all_tags) > 1) {
                $resultad = $all_tags['td'];
                for ($i=0; $i<count($resultad); $i++) {
                    if ($i > 1 && $i < 7) {
                        $arrCamb[$banco][$arrMone[($i-2)]] = str_replace(",", ".", $resultad[($i)]['text']);
                    }
                }

            } else {
                $correoMi .= "No devolvió valores en all_tags Sabadell<br>";
                $error[] = "No devolvió valores en all_tags Sabadell<br>";
            }
		}
//		var_dump($arrCamb); exit;

		/**
		 * BSA
		 */
        if (1==1) {
            $correoMi .= "Trabaja con BSA<br>";
            $banco = 'bsa';
            $all_tags = parser("https://www.bsandorramarkets.com/cs/Satellite?cid=1191435294922&pagename=BSAndorramarkets2%2FPage%2FPage_Interna_WFG_Template&WEB=2&seccion=cambios_contado&idioma=es");
            $arrMone = array();
            $arrMone = array(
                'Dólar EEUU'=>'USD',
                'Dólar Canadiense'=>'CAD',
                'Libra Esterlina'=>'GBP',
                'Franco Suizo'=>'CHF',
                'Yen Japonés'=>'JPY',
                'Peso Mejicano' => 'MXN',
                'Lira Turca' => 'TRY'
                );
            if (count($all_tags) > 1) {
                $resultad = $all_tags['td'];
                foreach ($arrMone as $key => $moneda) {
                    for ($i=0; $i<count($resultad); $i++) {
                        if (utf8_decode($resultad[$i]['text']) == $key){
                            $arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $resultad[($i+1)]['text']));
                        }
                    }
                }
            } else {
                $correoMi .= "No devolvió valores en all_tags CajaRural<br>";
                $error[] = "No devolvió valores en all_tags CajaRural<br>";
            }
		}
//		var_dump($arrCamb);

		/**
		 * Trabaja con CaixaGeral
         * IMPORTANTE NO SE TRABAJA CON CAIXA NO CAMBIAR LA DESIGUALDAD DE ABAJO
		 */
        if (1==0) {
            $correoMi .= "Trabaja con CaixaGeral<br>";
            $banco = 'caixa';
            $all_tags = parser("https://www2.caixabank.es/apl/divisas/verTodos_es.html");

            if (count($all_tags) > 1) {
                //recorro todos los enlaces descargados
                for($i=0;$i<count($all_tags['a']);$i++){
                    $arrElem = $all_tags['a'][$i]['props'];
                    if (strtolower($arrElem['title']) == 'ver todos') {
                //		escojo el enlace de ver todos para seguirlo y bajar las tasas de más monedas
                        $url = "https://www2.caixabank.es".$arrElem['href'];
                        break;
                    }
                }
            } else {
                $correoMi .= "No devolvió valores en all_tags 1CaixaGeral<br>";
                $error[] = "No devolvió valores en all_tags 1CaixaGeral<br>";
            }

            $all_tags = parser($url);
            if (count($all_tags) > 1) {
                if (count($all_tags) > 1) {
                    $resultad = $all_tags['td'];
                    foreach ($den as $moneda) {
                            $num = 0;
                        for ($i=0; $i<count($resultad); $i++) {
                            if (substr($resultad[$i]['text'], 0,3) == $moneda) {
                                if ($num == 2) {
                                    $arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[$i+1]['text']);
                                }
                                $num++;
                            }
                        }
                    }
                }
            } else {
                $correoMi .= "No devolvió valores en all_tags 2CaixaGeral<br>";
                $error[] = "No devolvió valores en all_tags 2CaixaGeral<br>";
            }
        }

		/**
		 * Bankia
         * IMPORTANTE NO SE TRABAJA CON BANKIA NO CAMBIAR LA DESIGUALDAD DE ABAJO
		 */
        if (1==0) {
            $correoMi .= "Trabaja con Bankia<br>";
            $banco = 'bankia';
            $all_tags = parser("https://broker.bankia.es/BRK/comunes/cruce_cmb/0,0,45287,00.html?idPagina=45287");

            if (count($all_tags) > 1) {
                $resultad = $all_tags['td'];
                foreach ($den as $moneda) {
                    for ($i=0; $i<count($resultad); $i++) {
                        if (substr($resultad[$i]['text'], 0,3) == $moneda) {
            //				if ($num == 2) {
                                $arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[$i+1]['text']);

                            $num++;
                        }
                    }
                }
            } else {
                $correoMi .= "No devolvió valores en all_tags Bankia<br>";
            //	$error[] = "No devolvió valores en all_tags Bankia<br>";
            }
		}

		/**
		 * Ibercaja
		 */
        if (1==0) {
            $correoMi .= "Trabaja con Ibercaja<br>";
            $arrMone = array(
                'DOLARES USA'=>'USD',
                'DOLARES CANADIENSES'=>'CAD',
                'LIBRAS ESTERLINAS'=>'GBP',
                'FRANCOS SUIZOS'=>'CHF',
                'YENES JAPONESES'=>'JPY',
                'REALES BRASILEÑOS'=>'BRL',
                'PESOS MEJICANOS'=>'MXN',
                'LIRAS TURCAS'=>'TRY',
                'NUEVOS SOLES PERU'=>'PEN',
                'PESOS CHILENOS'=>'CLP'
                );
            $banco = 'ibercaja';
            $all_tags = parser("https://www.ibercaja.es/particulares/tarifas-cotizaciones/");

            if (count($all_tags) > 1) {
                $puntero = $all_tags['strong'];
                $resultad = $all_tags['td'];
                foreach ($arrMone as $key => $moneda) {
                    for ($i=0; $i<count($puntero); $i++) {
                        if (strtoupper($puntero[$i]['text']) == $key) {
                            if ($i == 1) {
                                $arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[2]['text']);
                            } else {
                                $arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[($i*4-2)]['text']);
                            }
                        }
                    }
                }
            } else {
                $correoMi .= "No devolvió valores en all_tags Ibercaja<br>";
                $error[] = "No devolvió valores en all_tags Ibercaja<br>";
            }
		}

		/**
		 * Abanca
		 */
        if (1==1) {
            $correoMi .= "Trabaja con Abanca<br>";
            $banco = 'abanca';
            $all_tags = file_get_contents("https://www.abanca.com/api/v1/moneychange?{}");
            $arrMone = array(
                'Dólares Americanos'	=> 'USD',
                'Dólares Canadienses'	=> 'CAD',
                'Peso Chileno'			=> 'CLP',
                'Pesos colombianos'		=> 'COP',
                'Peso argentino'		=> 'ARS',
                'Rupias Indias'			=> 'INR',
                'Pesos mejicanos'		=> 'MXN',
                'Nuevos soles'			=> 'PEN',
                'Libras esterlinas'		=> 'GBP',
                'Liras turcas'			=> 'TRY',
                'Francos suizos'		=> 'CHF',
                'Yenes'					=> 'JPY',
                'Real'					=> 'BRL'
                );
            $all_tags = json_decode($all_tags, true);

            $tods = count($all_tags['divisas']);

            if ($tods > 1) {
    //			$resultad = $all_tags['td'];
    //			$resultad = $all_tags;
                foreach ($arrMone as $key => $moneda) {
                    for ($i=0; $i<$tods; $i++) {
                        if (utf8_decode($all_tags['divisas'][$i]['divisa']) == $moneda){
                            $arrCamb[$banco][$moneda] = str_replace(",", ".", $all_tags['divisas'][$i]['compraDivisaOp']);
                        }
                    }
                }
            } else {
                $correoMi .= "No devolvió valores en all_tags Abanca<br>";
                $error[] = "No devolvió valores en all_tags Abanca<br>";
            }
		}

		/**
		 * Caja Rural
		 */
        if (1==1) {
            $correoMi .= "Trabaja con CajaRural<br>";
            $banco = 'rural';
            $all_tags = parser("https://asp.infobolsanet.com/ifblayout/Layout.aspx?layout=Divisas&client=ruralia&CLIENTE_EMPRESA=&ENTID=900&SessionID=RURALNET0002&ESTILOS_PORTAL=PUBLICO&scrt=y");
            $arrMone = array();
            $arrMone = array(
                'EURO/DOLAR USA'=>'USD',
                'EURO/DOLAR CANADIENS'=>'CAD',
                'EURO/LIBRA ESTERLINA'=>'GBP',
                'EURO/FRANCO SUIZO'=>'CHF',
                'EURO/YEN JAPONES'=>'JPY',
                'EURO/COLUMBIAN PESO' => 'COP',
                'EURO/PESO ARGENTINO' => 'ARS',
                'EURO/PESO MEXICANO' => 'MXN',
                'EURO/REAL BRASILEÑO' => 'BRL'
                );
            if (count($all_tags) > 1) {
                $resultad = $all_tags['td'];
                foreach ($arrMone as $key => $moneda) {
                    for ($i=0; $i<count($resultad); $i++) {
                        if (utf8_decode($resultad[$i]['text']) == $key){
                            $arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $resultad[($i+1)]['text']));
                        }
                    }
                }
            } else {
                $correoMi .= "No devolvió valores en all_tags CajaRural<br>";
                $error[] = "No devolvió valores en all_tags CajaRural<br>";
            }
		}

         /**BBVA*/
         //xdebug_break();
         if (1==1) {
             $correoMi .= "Trabaja con BBVA<br>";
//             echo "Trabaja con BBVA <br>"; Reina
             $banco = 'bbva';
             $flagged = true;
             if (!$flagged) {
                 //primera peticion para solicitar token de seguridad
                 $curl = curl_init();$retries = 0; $access_token="";
                 curl_setopt_array($curl, array(
                     CURLOPT_URL => 'https://www.bbva.es/ASO/TechArchitecture/grantingTicketsOauth/V01',
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_ENCODING => '',
                     CURLOPT_MAXREDIRS => 10,
                     CURLOPT_TIMEOUT => 60,
                     CURLOPT_FOLLOWLOCATION => true,
                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                     CURLOPT_CUSTOMREQUEST => 'POST',
                     CURLOPT_POSTFIELDS =>'grant_type=client_credentials',
                     CURLOPT_HTTPHEADER => array(
                         'authorization: Basic SU1NQjAwMTpTajhCbGpRYw=='
                     ),
                 ));

                 ask_for_token: //codigo vintage ;)
                 $response = curl_exec($curl);
                 if (curl_errno($curl)) {
                     $error_msg = curl_error($curl);
                 }

                 if (isset($error_msg)) {
                     error_log($error_msg);
                 }
                 if ($response){
                     $access_token = json_decode($response)->access_token;
                     curl_close($curl);
                 }else{
                     //doble asegurar que el goto se porta bien
                     if ($retries < 5){
                         $retries ++;
                         goto ask_for_token;
                     }
                 }

                 //--------------------------------------------SEGUNDO CURL
                 //utilizo token de seguridad inicial para pedir los valores

                 $curl = curl_init();
                 curl_setopt_array($curl, array(
                     CURLOPT_URL => 'https://www.bbva.es/ASO/exchangeRates/V01/?$filter=(bank.id==0182)',
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_ENCODING => '',
                     CURLOPT_MAXREDIRS => 10,
                     CURLOPT_TIMEOUT => 0,
                     CURLOPT_FOLLOWLOCATION => true,
                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                     CURLOPT_CUSTOMREQUEST => 'GET',
                     CURLOPT_HTTPHEADER => array(
                         'tsec:'.$access_token),
                 ));

                 $response = curl_exec($curl);
                 if (curl_errno($curl)) {
                     $error_msg = curl_error($curl);
                 }
//esto se puede hacer mas elegante
                 if (isset($error_msg)) {
                     error_log($error_msg);
                 }
                 $items =json_decode($response)->items;
                 $cant_items = count($items);

                 curl_close($curl);
//ahora recorrer el array de valores y que dios nos ayude a organizar eso
                 $firstArray = [];
                 for ($i=0;$i<$cant_items; $i++){
                     $firstArray[$items[$i]->originCurrency->id]= $items[$i]->purchaseCurrencyRatio;
                 }
                 if (count($firstArray) > 1) {
                     foreach ($firstArray as $currency=>$change) {
                         //$arrCamb[$banco][$currency]=$change;
                         $arrCamb[$banco][$currency]=strval($change);

                     }
                 } else {

                     $correoMi .= "No se logró obtener los valores en divisas de BBVA<br>";
                     $error[] = "Sin  valores en divisas BBVA<br>";
                 }
             }else{
                 $correoMi .= "Entra a buscar tasa fijada de BBVA<br>";
                 $bb=findFixedRates($banco);
                 $correoMi .= "Valor devuelto " . $bb[$banco]['USD'] . "<br>";
                 $correoMi .= "Valor registrado " . $arrCamb[$banco]['USD'] . "<br>";
                 if (!$bb){
                     $correoMi .= "No se logró obtener los valores en divisas de BBVA<br>";
                     $error[] = "Sin  valores en divisas BBVA<br>";
                 }
             }
         }

         /**  Caixabank 3D**/
         if (1==1) {
             $correoMi .= "Trabaja con Caixabank<br>";
//             echo "Trabaja con Caixabank <br>"; Reina
             $banco = 'Caixa Bank';
             $arrMoney = array(
                 'GBP - LIBRA ESTERLINA',
                 'USD - DóLARES USA',
                 'CAD - DóLAR CANADIENSE',
                 'CHF - FRANCO SUIZO',
                 'SEK - CORONA SUECA',
                 'NOK - CORONA NORUEGA',
                 'DKK - CORONA DANESA',
                 'JPY - YEN JAPONÉS',
                 'AUD - DÓLAR AUSTRALIANO',
                 'MAD - DIRHAM MARROQUÍ'
             );
             $all_tags = file_get_contents("https://www4.caixabank.es/apl/divisas/index_es.html#cotizaciones_de_divisas");

             //Recreo el doc a partir del string obtenido de la pagina
             $domdoc = new DOMDocument('1.0', 'utf-8');
             $domdoc->loadHTML($all_tags);

             //obtengo los valores de la seccion de las divisas
             $article = $domdoc->getElementById('cotizaciones_de_divisas');
             //a continuacion obtener texto con valores
             $values =  $article->textContent;
             //quedarse solo con las monedas y los cambios
             $fixedValues = substr($values, 63);
             $mixedArray = explode("\n", $fixedValues);
             $ratesArray = array();
             //por cada una de las monedas especificadas en el array obtener su tasa
             foreach ($arrMoney as $type){
                 //verifico que la moneda exista en el arreglo y su posicion
                 $code = substr($type, 0, 3);
                 foreach ($mixedArray as $item){
                     if (strlen($item) > 2){ //los campos con comilla vacia no analizarlos
                         if ($code == substr($item, 0, 3)){
                             $position = array_search($item, $mixedArray);
                             $ratesArray[$code]=$mixedArray[$position+2];
                         }
                     }

                 }
             }

             if (count($ratesArray) > 1) {
                 foreach ($ratesArray as $curr => $buyRate) {
                     $arrCamb[$banco][$curr] = $buyRate;
                 }
             } else {
                 $correoMi .= "No hay valores en divisas Caixa Bank<br>";
                 $error[] = "Sin  valores en divisas Caixa Bank<br>";
             }

         }

         /**  Santander(Solo trabaja con EUROS actualmente)* */

         /**
          * Plataforma XE
          */
         if (1==1) {
             $correoMi .= "Trabaja con xenon <br>";
//            echo "Trabaja con xenon <br>"; Reina
             $banco = 'xe';
             foreach($den as $item) {
                 $rateXe = ratesXe($item);
                 if ($rateXe == 0) {
                     $error[] = "Error cambio Xe en la moneda $item<br>";
                 } else $arrCamb[$banco][$item] = $rateXe;
             }

         }
// var_dump($arrCamb);echo "<br>$correoMi";
// exit;


		/**
		* Busca las tasas de cambio de las monedas CUC, USD, GBP y CAD
        */
        if (1==1) {
            $banco = 'bce';
            $correoMi .= "Trabaja con BCE<br>";
            $XMLContent= file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
            $cambio = 0;

            if ($XMLContent) {
                foreach ($XMLContent as $line) {
                    if (preg_match("/currency='([[:alpha:]]+)'/", $line, $currencyCode)) {
                        if (preg_match("/rate='([[:graph:]]+)'/", $line, $rate)) {
                            //Output the value of 1EUR for a currency code
                            $arrCamb[$banco][$currencyCode[1]] = $rate[1];
                        }
                    }
                }
            } else {
                $correoMi .= "Error cambio BCE<br>";
                $error[] = "Error cambio BCE<br>";
            }
		}

$correoMi = "";
		/**
		* BCC
        */
        if (1==1) {
            $banco = 'bnc';
            if (1==1) {
				$correoMi .= "Trabaja con BCC<br>";
				
				$all_tags = parser("https://www.bc.gob.cu/");
				// $all_tags = file_get_contents("https://www.bc.gob.cu/",true);
				


                if (count($all_tags) >= 1) {
					$resultad = $all_tags['td'];
				
				echo "<br>".print_r($resultad);
				// echo "<br><br>mira->".json_decode($all_tags)."<br><br>";
                    foreach ($den as $moneda) {
                        if ($moneda == 'USD')
                           $arrCamb[$banco][$moneda] = str_replace("</tr>","",str_replace("</td>","",str_replace("<td>","",str_replace("\n","",str_replace(" ","",substr($all_tags, stripos($all_tags, '<span class="flag-icon flag-icon-us"></span> USD')+48, 170))))));
                        if ($moneda == 'EUR')
                           $arrCamb[$banco][$moneda] = str_replace("</tr>","",str_replace("</td>","",str_replace("<td>","",str_replace("\n","",str_replace(" ","",substr($all_tags, stripos($all_tags, '<span class="flag-icon flag-icon-eu"></span> EUR')+48, 170))))));
                            // $correoMi .= "OYEEEEEEEEEEEEEEEEEEEEE".str_replace("\n","",str_replace(" ","",substr($all_tags, stripos($all_tags, '<span class="flag-icon flag-icon-eu"></span> EUR'), 170)))."TERMINACA";
                        // $num = 0;
                        for ($i=0; $i<count($resultad); $i++) {
                            if (substr($resultad[$i]['text'], 0, 3) == $moneda) {
                                // echo $resultad[$i]['text'] . " == " . $moneda . "<br>";
                                // echo $resultad[$i+1]['text'] . "1 == " . $moneda . "<br>";
                                // echo $resultad[$i+2]['text'] . "2 == " . $moneda . "<br><br>";
                                if ($moneda == 'USD') {
                                    $arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[$i + 1]['text']);
                                } elseif ($moneda == 'EUR') {
                                    $arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[$i + 2]['text']);
                                }

                                break;
                            }
                        }
                    }
                }
            }
// echo $arrCamb[$banco][$moneda]."<br>";
            if (1==0) {
                $correoMi .= "Trabaja con BNC<br>";
                $all_tags = parser("http://bnc.cu/");
                if (count($all_tags) >= 1) {
                    $resultad = $all_tags['td'];
                    foreach ($den as $moneda) {
                        $num = 0;
                        for ($i=0; $i<count($resultad); $i++) {
                            if (substr($resultad[$i]['text'], 0, 3) == $moneda) {
                                // echo $resultad[$i]['text'] . " == " . $moneda . "<br>";
                                // echo $resultad[$i+1]['text'] . "1 == " . $moneda . "<br>";
                                // echo $resultad[$i+2]['text'] . "2 == " . $moneda . "<br><br>";
                                if ($moneda == 'EUR') {
                                    $arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[$i + 2]['text']);
                                } else {
                                    $arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[$i + 1]['text']);
                                }

                                break;
                            }
                        }
                    }
                }
            }

		}

	$correoMi .= "<br>".json_encode($arrCamb)."<br>";
//	   Determino el valor máximo del usd
	   $maxUSD = 0;
	   $arrBanc = array(
		   'triodos',
		   'laboral',
		//    'caixa',
		   'rural',
		//    'sabadell',
		//    'bankia',
		//    'ibercaja',
		   'xe',
		   'bce',
		   'bnc',
		   'abanca',
		   'bsa',
		   'kutxabank',
		   'ingeniero',
		//    'bancamarch',
		   'bankinter'
	   );
	   for($i=0;$i<count($arrBanc);$i++){
		   if ($arrBanc[$i] != 'bnc' && $arrCamb[$arrBanc[$i]]['USD'] > $maxUSD){
			   $maxUSD = $arrCamb[$arrBanc[$i]]['USD'];
		   }
	}
	$correoMi .= "El valor maximo del usd es $maxUSD<br>";

	   if ($arrCamb['bnc']['EUR'] == 0 || $arrCamb['bnc']['EUR'] == '' || $arrCamb['bnc']['EUR'] == null) {
		   $q = "select tasa from tbl_colCambBanco where idbanco = 27 and idmoneda = 978 order by id desc limit 0,1";
		   $temp->query($q);
		   $arrCamb['bnc']['EUR'] = $temp->f('tasa');
		   $error[] = "Error cambio BNC se toma la tasa anterior<br>";
	   }

		/**
		* Cálculo de la tasa
		*/
if ($arrCamb['bnc']['USD'] < 10) $arrCamb['bnc']['USD'] = 24;
		$maxUSDperm = leeSetup('maxUSDperm'); //diferencia máxima entre el máximo de los bancos y la tasas del BFI
		$difBncBfi = 0.02; //Diferencia entre el BFI y el BNC
		$indice = $maxUSD - ($arrCamb['bnc']['EUR'] / $arrCamb['bnc']['USD'] - $difBncBfi);
		$correoMi .= "<br>indice=$indice";
		$tasaCUSD = $maxUSDperm - ($maxUSD - (($arrCamb['bnc']['EUR'] / $arrCamb['bnc']['USD']) - $difBncBfi));
		$correoMi .= "<br>$tasaCUSD = $maxUSDperm - ($maxUSD - ((".$arrCamb['bnc']['EUR']." / ".$arrCamb['bnc']['USD'].") - $difBncBfi))<br>";

        if ($tasaCUSD <= 0) $tasaCUSD = 0;

	//    $correoMi .= "<br>La relación queda: ".($maxUSD - $maxUSDperm)." >= ".($arrCamb['bnc']['EUR'] - $difBncBfi);
	//    if ($maxUSD - $maxUSDperm >= $arrCamb['bnc']['EUR'] - $difBncBfi) {
	// 		$tasaCUSD = 0;
	//    } else {
	// 		$tasaCUSD = ($arrCamb['bnc']['EUR'] - $difBncBfi) - ($maxUSD - $maxUSDperm);
	//    }

	//    $correoMi .= "<br>La tasa calculada es $tasaCUSD";
	//    if ($tasaCUSD > 0.01) {$tasaCUSD = 0.01;}
	//    $correoMi .= "<br>La tasa calculada normalizada es $tasaCUSD";

		/**
		* actualiza la tabla setup con el banco solicitado o con el mayor valor
		*/

		$conTCorr = "Hola<br><br>Las tasas de cambio para el USD son:<br><br>";
		$texmon = "<br><br>Tasas para el resto de las monedas (Moneda - Hoy / Ayer):";
		foreach ($den as $moneda) {
			$correoMi .= "<br><br> Trabaja con la moneda $moneda";
			if ($moneda == 'USD') {$tasa = $tasaCUSD;}
			else {$tasa = $tasaInc;}
			foreach ($arrBanc as $banco) {

					switch ($banco) {
						// case 'caixa':
						// 	$bnom = 'Caixa Geral';
						// 	$idb = '18';
						// 	break;
						case 'bce':
							$bnom = 'BCE';
							$idb = '26';
							break;
						case 'xe':
							$bnom = 'XE';
							$idb = '28';
							break;
						case 'rural':
							$bnom = 'Caja Rural';
							$idb = '17';
							break;
						case 'sabadell':
							$bnom = 'Sabadell';
							$idb = '2';
							break;
						case 'bankia':
							$bnom = 'Bankia';
							$idb = '6';
							break;
						case 'ibercaja':
							$bnom = 'Ibercaja';
							$idb = '15';
							break;
						case 'abanca':
							$bnom = 'Abanca';
							$idb = '7';
							break;
						case 'bsa':
							$bnom = 'Banca Sabadel de Andorra';
							$idb = '12';
							break;
						case 'laboral':
							$bnom = 'Laboral Kutxa';
							$idb = '13';
							break;
						case 'triodos':
							$bnom = 'Triodos';
							$idb = '23';
							break;
						case 'kutxabank':
							$bnom = 'Kutxabank';
							$idb = '37';
							break;
						case 'ingeniero':
							$bnom = 'Caja Ingenieros';
							$idb = '34';
							break;
						case 'bankinter':
							$bnom = 'Bankinter';
							$idb = '30';
							break;
						case 'bancamarch' :
							$bnom = 'BancaMarch';
							$idb = '22';
							break;
						default :
							$bnom = 'bnc';
							$idb = '27';
							break;
					}

				if ($arrCamb[$banco][$moneda] > 0) {

					$camB = $arrCamb[$banco][$moneda];
					$correoMi .= "<br>camB=$camB";
					$correoMi .= "<br>idb=$idb";
					$correoMi .= "<br>moneda=$moneda";

					if (($idb == '22' || $idb == '37' ) && $moneda == 'USD') {
						if (leeSetup('cambioCimex') != '0') $camB = leeSetup('cambioCimex');
						$correoMi .= "<br>camB=$camB";
					}
					$qx = "insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values ($idb, (select idmoneda from tbl_moneda where moneda = '$moneda'), ".$camB.", $horaTasa)";
					$correoMi .= "<br>$qx";
					$temp->query($qx);
				}

				if ($moneda == 'USD') {
					$cambio = number_format($arrCamb[$banco][$moneda],4);
					if (strlen($bnom) > 1 && $bnom != 'bnc') {$conTCorr .= "$bnom: $cambio<br>";}
				}
			}


			$arrtaant[$moneda] = leeSetup($moneda);
			$q = "select max(tasa) maxt from tbl_colCambBanco where fecha = $horaTasa and idbanco != 27 and  idmoneda = (select idmoneda from tbl_moneda where moneda = '$moneda')";
			$temp->query($q);
			if ($temp->num_rows() == 0){
				$correoMi .= "<br>No se actualizó la tasa de la moneda $moneda";
			} else {
				$maxMon = $temp->f('maxt');
				if (abs($maxMon - $arrtaant[$moneda]) > 2 && $moneda == 'CAD') {
					$q = "select tasa maxt from tbl_colCambBanco where fecha = $horaTasa and idbanco != 27 and  idmoneda = (select idmoneda from tbl_moneda where moneda = '$moneda') order by tasa desc limit 1,1";
					$temp->query($q);
					$maxMon = $temp->f('maxt');
				}
			}
			$maxMon = $temp->f('maxt');
			$correoMi .= "<br>Máximo de $moneda por tbl_colCambBanco: $maxMon";

			if ($moneda == 'USD') {
				$tasaInc = $tasaCUSD;
				$adcCon = "Concentrador: ". number_format(($maxMon + $tasaCUSD),4);
			}
			$arrtaant[$moneda] = leeSetup($moneda);
			if ($moneda == 'EUR' ) actSetup(1, $moneda);
			else {
				$correoMi .= "<br>Actualiza setup con la tasa nuevo cálculo ".($maxMon + $tasaInc)." de $moneda";
				actSetup(($maxMon + $tasaInc), $moneda);
				$texmon .= "<br>$moneda - ".($maxMon + $tasaInc)." / ".$arrtaant[$moneda];
			}
		}

		$conTCorr .= "BCC: ".number_format($arrCamb['bnc']['EUR'],4)."<br><br>$adcCon<br>Incremento de la tasa: ".number_format($tasaCUSD,4).$texmon;
		$corCreo->todo(58, 'Tasas de cambio '.date('d/m/Y'), $conTCorr);
		sendTelegram('Tasas de cambio '.date('d/m/Y')."\n$conTCorr",null);

		actSetup($ahora, 'fechaTasa');
		$correoMi .= "<br>Actualiza Setup con la fecha de la tasa: $ahora<br>";

		/**
		 * si hay error mando el aviso
		 */
		if (count($error) > 0 && leeSetup('envioSMScamb') == '0') {
			foreach ($error as $value) {
				$correoMi .= $value;
			}

            $corCreo->set_subject('Problemas al obtener las tasas de cambio.');
            $corCreo->set_message($correoMi.json_decode($error));
            $corCreo->envia(2);
			sendTelegram('Problemas al obtener las tasas de cambio.\n'.$correoMi.json_decode($error),null);

			actSetup('1', 'envioSMScamb');
			#Verificar Resultado
			if ($dms->autentificacion->error){
				#Error de autentificacion con la plataforma
				$saliMensaje .= $dms->autentificacion->mensajeerror;

			}
		}
}
//echo $conTCorr;
$correoMi .= "<br>Termina de poner la tasa del día<br>";
$corCreo->todo(5, "Ejecución del Cron", "Ejecutado satisfactoriamente a las " . date('d/m/Y H:i'). $correoMi);
// echo $correoMi;


function parser($url)
{
	include_once 'admin/classes/class.Html.php';
	$objHtmlParser = new Html($url);
	$description = '';
	$objHtmlParser->Clean();
	$objHtmlParser->Parse($description);
	$all_tags = array();
	$objHtmlParser->FindAllTags($objHtmlParser->tree, $all_tags);
	return $all_tags;
}

function rates($denom)
{

	$url = "http://download.finance.yahoo.com/d/quotes.csv?s=EUR$denom=X&f=sl1d1t1ba&e=.csv";
	$chx = curl_init($url);
	curl_setopt($chx, CURLOPT_POST, false);
	curl_setopt($chx, CURLOPT_HEADER, false);
	curl_setopt($chx, CURLOPT_RETURNTRANSFER, true);
	$sale = curl_exec($chx);
	curl_close($chx);

	$arrEx = explode(',', $sale);
	return $arrEx[1];
}

function ratesXe($denom){
	// if ($denom != 'USD') return;
	include_once 'admin/classes/class.Html.php';
	$resultad = '';
// echo "<br><br>";
	// $url = "http://www.xe.com/ucc/convert.cgi?template=pca-new&Amount=1&From=EUR&To=$denom&image.x=36&image.y=11&image=Submit";
    //	$url = 'http://localhost/XE.com%20-%20Personal%20Currency%20Assistant'.$denom.'.htm';
    $url = "https://xe.com/currencyconverter/convert/?Amount=1&From=EUR&To=$denom";
// echo $url."<br>";
$all_tags = parser($url);
$resultad = $all_tags['td'];

// echo $resultad[0]['text']."<br><br>";

// echo json_encode($resultad);

// for ($i = 0; $i < count($resultad); $i++) {
//     echo "<br>";
//     echo(str_replace(" USD", "", $resultad[8]['text']));
//     echo "<br>";
// }

return str_replace(" $denom", "", $resultad[0]['text']);

    // echo "<br>";
    // echo(str_replace(" $denom", "", $resultad[8]['text']));
    // echo "<br>";

	// $objHtmlParser = new Html($url);

	// $description = '';
	// $objHtmlParser->Clean();

	// $objHtmlParser->Parse($description);

	// $all_tags = array();
	// $objHtmlParser->FindAllTags($objHtmlParser->tree, $all_tags);
    //
	// if (count($all_tags) > 1) {
    //     // $resultad = $all_tags['span'][3]['text'];
    //     $resultad = $all_tags['p'][3]['text'];
	// 	if (strlen($resultad) > 6) {
	// 		$resultad = str_replace('1 EUR = ', '', $resultad);
	// 		$resultad = str_replace(' ' . $denom, '', $resultad);
	// 	}
    // }
    // echo "<br><br>";
	// return $resultad;
}
?>
