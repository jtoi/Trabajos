<?php

/* Prueba de correos se puede borrar
 * 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include_once( 'admin/adminis.func.php' );
include 'include/mysqli.php';
$temp = new ps_DB;


$q = "select moneda from tbl_moneda";
$temp->query($q);
$den = $temp->loadResultArray();
$arrCamb = array();

		$texto .= "\n<br>Pone la tasa de día $horaTasa <= ".time()."\n<br>";
        $texto .= "Entra en G14\n<br>";
	//Revisa que no se haya puesto la hora ya 
			
        $texto .= "Entra en horaTasa\n<br>";
		$texto .= "\n<br>Busca y pone la tasa de cambio del día\n<br>";
		//Pone la tasa del día
		$error = array();
		actSetup('0', 'envioSMScamb'); //actualiza la tabla setup caso que haya que enviarme sms
		$q = "select moneda from tbl_moneda where activo = 1";
        $texto .= $q."<br>";
		$temp->query($q);
		$den = $temp->loadResultArray();
		$tasaInc = leeSetup('incBCE'); //incremento sobre la tasa de cambio

		/**
		 * Sabadell
		 */
//		$texto .= "Trabaja con Sabadell\n<br>";
//		$banco = 'sabadell';
//		$all_tags = parser("https://www.sabadellconsumer.com/cs/Satellite?cid=1191409940011&pagename=BSMarkets2%2FPage%2FPage_Interna_WFG_Template&language=es_ES");
//		$arrMone = array( 'USD', 'GBP', 'JPY', 'CHF', 'CAD');
//		if (count($all_tags) > 1) {
//			$resultad = $all_tags['td'];
//			for ($i=0; $i<count($resultad); $i++) {
//				if ($i > 1 && $i < 7) {
//					$arrCamb[$banco][$arrMone[($i-2)]] = str_replace(",", ".", $resultad[($i)]['text']);
//				}
//			}
//			
//		} else {
//			$texto .= "No devolvió valores en all_tags Sabadell\n<br>";
//			$error[] = "No devolvió valores en all_tags Sabadell\n<br>";
//		}
		
		/**
		 * BSA
		 */
//		$texto .= "Trabaja con BSA\n<br>";
//		$banco = 'bsa';
//		$all_tags = parser("https://www.bsandorramarkets.com/cs/Satellite?cid=1191435294922&pagename=BSAndorramarkets2%2FPage%2FPage_Interna_WFG_Template&WEB=2&seccion=cambios_contado&idioma=es");
//		$arrMone = array();
//		$arrMone = array(
//			'Dólar EEUU'=>'USD',
//			'Dólar Canadiense'=>'CAD',
//			'Libra Esterlina'=>'GBP',
//			'Franco Suizo'=>'CHF',
//			'Yen Japonés'=>'JPY',
//			'Peso Mejicano' => 'MXN',
//			'Lira Turca' => 'TRY'
//			);
//		if (count($all_tags) > 1) {
//			$resultad = $all_tags['td'];
//			foreach ($arrMone as $key => $moneda) {
//				for ($i=0; $i<count($resultad); $i++) {
//					if (utf8_decode($resultad[$i]['text']) == $key){
//						$arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $resultad[($i+1)]['text']));
//					}
//				}
//			}
//		} else {
//			$texto .= "No devolvió valores en all_tags CajaRural\n<br>";
//			$error[] = "No devolvió valores en all_tags CajaRural\n<br>";
//		}
		
		/**
		 * Trabaja con CaixaGeral
		 */
//		$texto .= "Trabaja con CaixaGeral\n<br>";
//		$banco = 'caixa';
//		$all_tags = parser("https://www2.caixabank.es/apl/divisas/verTodos_es.html");
//		
//		if (count($all_tags) > 1) {
//			//recorro todos los enlaces descargados
//			for($i=0;$i<count($all_tags['a']);$i++){
//				$arrElem = $all_tags['a'][$i]['props'];
//				if (strtolower($arrElem['title']) == 'ver todos') {
//			//		escojo el enlace de ver todos para seguirlo y bajar las tasas de más monedas
//					$url = "https://www2.caixabank.es".$arrElem['href'];
//					break;
//				}
//			}
//		} else {
//			$texto .= "No devolvió valores en all_tags 1CaixaGeral\n<br>";
//			$error[] = "No devolvió valores en all_tags 1CaixaGeral\n<br>";
//		}
//
//		$all_tags = parser($url);
//		if (count($all_tags) > 1) {
//			if (count($all_tags) > 1) {
//				$resultad = $all_tags['td'];
//				foreach ($den as $moneda) {
//						$num = 0;
//					for ($i=0; $i<count($resultad); $i++) {
//						if (substr($resultad[$i]['text'], 0,3) == $moneda) {
//							if ($num == 2) {
//								$arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[$i+1]['text']);
//							}
//							$num++;
//						} 
//					}
//				}
//			}
//		} else {
//			$texto .= "No devolvió valores en all_tags 2CaixaGeral\n<br>";
//			$error[] = "No devolvió valores en all_tags 2CaixaGeral\n<br>";
//		}

		/**
		 * Bankia
		 */
//		$texto .= "Trabaja con Bankia\n<br>";
//		$banco = 'bankia';
//		$all_tags = parser("https://broker.bankia.es/BRK/comunes/cruce_cmb/0,0,45287,00.html?idPagina=45287");
//
//		if (count($all_tags) > 1) {
//			$resultad = $all_tags['td'];
//			foreach ($den as $moneda) {
//				for ($i=0; $i<count($resultad); $i++) {
//					if (substr($resultad[$i]['text'], 0,3) == $moneda) {
//		//				if ($num == 2) {
//							$arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[$i+1]['text']);
//
//						$num++;
//					} 
//				}
//			}
//		} else {
//			$texto .= "No devolvió valores en all_tags Bankia\n<br>";
//			$error[] = "No devolvió valores en all_tags Bankia\n<br>";
//		}

		/**
		 * Ibercaja
		 */
//		$texto .= "Trabaja con Ibercaja\n<br>";
//		$arrMone = array(
//			'DOLARES USA'=>'USD',
//			'DOLARES CANADIENSES'=>'CAD',
//			'LIBRAS ESTERLINAS'=>'GBP',
//			'FRANCOS SUIZOS'=>'CHF',
//			'YENES JAPONESES'=>'JPY',
//			'REALES BRASILEÑOS'=>'BRL',
//			'PESOS MEJICANOS'=>'MXN',
//			'LIRAS TURCAS'=>'TRY',
//			'NUEVOS SOLES PERU'=>'PEN',
//			'PESOS CHILENOS'=>'CLP'
//			);
//		$banco = 'ibercaja';
//		$all_tags = parser("https://www.ibercaja.es/particulares/tarifas-cotizaciones/");
//
//		if (count($all_tags) > 1) {
//			$puntero = $all_tags['strong'];
//			$resultad = $all_tags['td'];
//			foreach ($arrMone as $key => $moneda) {
//				for ($i=0; $i<count($puntero); $i++) {
//					if (strtoupper($puntero[$i]['text']) == $key) {
//						if ($i == 1) {
//							$arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[2]['text']);
//						} else {
//							$arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[($i*4-2)]['text']);
//						}
//					}
//				}
//			}
//		} else {
//			$texto .= "No devolvió valores en all_tags Ibercaja\n<br>";
//			$error[] = "No devolvió valores en all_tags Ibercaja\n<br>";
//		}
		
		/**
		 * Abanca
		 */
//		$texto .= "Trabaja con Abanca\n<br>";
//		$banco = 'abanca';
//		$all_tags = file_get_contents("https://www.abanca.com/api/v1/moneychange?{}");
//		$arrMone = array();
//		$arrMone = array(
//			'Dólares Americanos'	=> 'USD',
//			'Dólares Canadienses'	=> 'CAD',
//			'Peso Chileno'			=> 'CLP',
//			'Pesos colombianos'		=> 'COP',
//			'Peso argentino'		=> 'ARS',
//			'Rupias Indias'			=> 'INR',
//			'Pesos mejicanos'		=> 'MXN',
//			'Nuevos soles'			=> 'PEN',
//			'Libras esterlinas'		=> 'GBP',
//			'Liras turcas'			=> 'TRY',
//			'Francos suizos'		=> 'CHF',
//			'Yenes'					=> 'JPY',
//			'Real'					=> 'BRL'
//			);
//		$all_tags = json_decode($all_tags, true);
//		
//		$tods = count($all_tags['divisas']);
//		
//		if ($tods > 1) {
//			foreach ($arrMone as $key => $moneda) {
//				for ($i=0; $i<$tods; $i++) {
//					if (utf8_decode($all_tags['divisas'][$i]['divisa']) == $moneda){
//						$arrCamb[$banco][$moneda] = str_replace(",", ".", $all_tags['divisas'][$i]['compraDivisaOp']);
//					}
//				}
//			}
//		} else {
//			$texto .= "No devolvió valores en all_tags Abanca\n<br>";
//			$error[] = "No devolvió valores en all_tags Abanca\n<br>";
//		}

		/**
		 * Caja Rural
		 */
//		$texto .= "Trabaja con CajaRural\n<br>";
//		$banco = 'rural';
//		$all_tags = parser("https://asp.infobolsanet.com/ifblayout/Layout.aspx?layout=Divisas&client=ruralia&CLIENTE_EMPRESA=&ENTID=900&SessionID=RURALNET0002&ESTILOS_PORTAL=PUBLICO&scrt=y");
//		$arrMone = array();
//		$arrMone = array(
//			'EURO/DOLAR USA'=>'USD',
//			'EURO/DOLAR CANADIENS'=>'CAD',
//			'EURO/LIBRA ESTERLINA'=>'GBP',
//			'EURO/FRANCO SUIZO'=>'CHF',
//			'EURO/YEN JAPONES'=>'JPY',
//			'EURO/COLUMBIAN PESO' => 'COP',
//			'EURO/PESO ARGENTINO' => 'ARS',
//			'EURO/PESO MEXICANO' => 'MXN',
//			'EURO/REAL BRASILEÑO' => 'BRL'
//			);
//		if (count($all_tags) > 1) {
//			$resultad = $all_tags['td'];
//			foreach ($arrMone as $key => $moneda) {
//				for ($i=0; $i<count($resultad); $i++) {
//					if (utf8_decode($resultad[$i]['text']) == $key){
//						$arrCamb[$banco][$moneda] = str_replace(",", ".", str_replace(".", "", $resultad[($i+1)]['text']));
//					}
//				}
//			}
//		} else {
//			$texto .= "No devolvió valores en all_tags CajaRural\n<br>";
//			$error[] = "No devolvió valores en all_tags CajaRural\n<br>";
//		}

		
		/**
		* Pone el cambio de moneda según Xe.com
		*/
//		$texto .= "\n<br>Trabaja con Xe.com<br>";
//		$banco = 'xe';
//			foreach($den as $item) {
//				$rat = ratesXe($item);
//				if (!$rat > 0) {
//					$rat = 0;
//					$error[] = "Error cambio Xe en la moneda $item\n<br>";
//				} else $arrCamb[$banco][$item] = str_replace (",", "", $rat);
//
//			}
//
//		/**
//		* Busca las tasas de cambio de las monedas CUC, USD, GBP y CAD
//		*/
//		$banco = 'bce';
//		$texto .= "\n<br>Trabaja con BCE<br>";
//			$XMLContent= file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
//			$cambio = 0;
//
//			if ($XMLContent) {
//				foreach ($XMLContent as $line) {
//						if (ereg("currency='([[:alpha:]]+)'",$line,$currencyCode)) {
//							if (ereg("rate='([[:graph:]]+)'",$line,$rate)) {
//									//Output the value of 1 EUR for a currency code
//									if (in_array($currencyCode[1], $den)) {
//										if ($rate[1] != '' && $rate[1] != 0) {
//											$arrCamb[$banco][$currencyCode[1]] = $rate[1];
//										}
//									}
//							}
//						}
//				}
//			} else {
//                $texto .= "Error cambio BCE<br>";
//				$error[] = "Error cambio BCE\n<br>";
//			}
			
		/**
		* BCC
		*/
		$texto .= "Trabaja con BNC\n<br>";
		$banco = 'bnc';
		$all_tags = parser("http://www.bc.gob.cu/");
		if (count($all_tags) >= 1) {
			$resultad = $all_tags['td'];
//			var_dump($resultad);
			foreach ($den as $moneda) {
				$num = 0;
				for ($i=0; $i<count($resultad); $i++) {
					if (substr($resultad[$i]['text'], 0,3) == $moneda) {
					echo $resultad[$i]['text']." - ".$resultad[$i+2]['text']."<br>";
						$arrCamb[$banco][$moneda] = str_replace(",", ".", $resultad[$i+2]['text']);
						break;
					} 
				}
			}
		}
//		exit;
	   
//	   Determino el valor máximo del usd
	   $maxUSD = 0;
	   $arrBanc = array(
		   'caixa',
		   'rural',
		   'sabadell',
		   'bankia',
		   'ibercaja',
		   'xe',
		   'bce',
		   'bnc',
		   'abanca',
		   'bsa'
	   );
	   for($i=0;$i<count($arrBanc);$i++){
		   if ($arrCamb[$arrBanc[$i]]['USD'] > $maxUSD){
			   $maxUSD = $arrCamb[$arrBanc[$i]]['USD'];
		   }
	   }
	   
	   $maxUSDperm = leeSetup('maxUSDperm'); //diferencia máxima entre el máximo de los bancos y la tasas del BFI
	   $difBncBfi = 0.02; //Diferencia entre el BFI y el BNC
	   $texto .= "\n<br>La relación queda: ".($maxUSD - $maxUSDperm)." >= ".($arrCamb['bnc']['EUR'] - $difBncBfi);
	   if ($maxUSD - $maxUSDperm >= $arrCamb['bnc']['EUR'] - $difBncBfi) {
		   $tasaCUSD = 0;
	   } else {
		   
		   $tasaCUSD = ($arrCamb['bnc']['EUR'] - $difBncBfi) - ($maxUSD - $maxUSDperm);
	   }
	   
	   
	   $texto .= "\n<br>La tasa calculada es $tasaCUSD";
	   if ($tasaCUSD > 0.01) {$tasaCUSD = 0.01;}
	   $texto .= "\n<br>La tasa calculada normalizada es $tasaCUSD";

		/**
		* actualiza la tabla setup con el banco solicitado o con el mayor valor 
		*/
		$texto .= "\n<br>actualiza la tabla setup con el banco solicitado o con el mayor valor <br>";
		
		$conTCorr = "Hola<br><br>Las tasas de cambio para el USD son:<br><br>";
		foreach ($den as $moneda) {
			$texto .= "\n\n<br><br> Trabaja con la moneda $moneda";
			if ($moneda == 'USD') {$tasa = $tasaCUSD;}
			else {$tasa = $tasaInc;}
			$q1 = "insert into tbl_cambio (fecha, tasa, moneda";
			$q2 = ") values ('$horaTasa', '$tasa', '$moneda'";
			foreach ($arrBanc as $banco) {
				$q1 .= ", $banco";
				$q2 .= ", '".$arrCamb[$banco][$moneda]."'";
				
					switch ($banco) {
						case 'caixa':
							$bnom = 'Caixa Geral';
							$idb = '18';
							break;
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
						default :
							$bnom = 'bnc';
							$idb = '27';
							break;
					}
				
				if ($arrCamb[$banco][$moneda] > 0) {
					$qx = "insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values ($idb, (select idmoneda from tbl_moneda where moneda = '$moneda'), ".$arrCamb[$banco][$moneda].", $horaTasa)";
					$texto .= "\n<br>$qx";
					$temp->query($qx);
				}
				
				if ($moneda == 'USD') {
					$cambio = number_format($arrCamb[$banco][$moneda],4);
					if (strlen($bnom) > 1 && $bnom != 'bnc') {$conTCorr .= "$bnom: $cambio<br>";}
				}
			}
			
			$q = "select max(tasa) maxt from tbl_colCambBanco where fecha = $horaTasa and idbanco != 27 and  idmoneda = (select idmoneda from tbl_moneda where moneda = '$moneda')";
			$temp->query($q);
			if ($temp->num_rows() == 0){
				$texto .= "\n<br>No se actualizó la tasa de la moneda $moneda";
			}
			$maxMon = $temp->f('maxt');
			$texto .= "\n<br>Máximo de $moneda por tbl_colCambBanco: $maxMon";
			
			if ($moneda == 'USD') {
				$tasaInc = $tasaCUSD;
				$adcCon = "Concentrador: ". number_format(($maxMon + $tasaCUSD),4);
			} 
			$texto .= "\n<br>Actualiza setup con la tasa nuevo cálculo ".($maxMon + $tasaInc)." de $moneda";
			actSetup(($maxMon + $tasaInc), $moneda);
		}
		
		$conTCorr .= "BCC: ".number_format($arrCamb['bnc']['EUR'],4)."<br><br>$adcCon<br>Incremento de la tasa: ".number_format($tasaCUSD,4);
		$corCreo->todo(58, 'Tasas de cambio '.date('d/m/Y'), $conTCorr);

		actSetup($ahora, 'fechaTasa');
		$texto .= "\n<br>Actualiza Setup con la fecha de la tasa: $ahora<br>";
		
		/**
		 * si hay error mando el aviso 
		 */
		if (count($error) > 0 && leeSetup('envioSMScamb') == '0') {
			foreach ($error as $value) {
				$texto .= $value;
			}
            
            $corCreo->set_subject('Problemas al obtener las tasas de cambio.');
            $corCreo->set_message($texto);
            $corCreo->envia(2);
            
			actSetup('1', 'envioSMScamb');
			#Verificar Resultado
			if ($dms->autentificacion->error){
				#Error de autentificacion con la plataforma
				$saliMensaje .= $dms->autentificacion->mensajeerror;

			}
		}
 	
	$texto .= "\n<br>Termina de poner la tasa del día\n<br>";
	
	
/**
 * Función que parsea las páginas de los bancos
 * @param text $url
 * @return array
 */
function parser($url) {
	include_once 'admin/classes/class.Html.php';
	$objHtmlParser = new Html($url);
	$description = '';
	$objHtmlParser->Clean();
	$objHtmlParser->Parse($description);
	$all_tags = array();
	$objHtmlParser->FindAllTags($objHtmlParser->tree,$all_tags);
	return $all_tags;
}
?>