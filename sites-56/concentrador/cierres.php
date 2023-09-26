<?php
/*
 * Fichero para ejecutar cada hora
 *
 */
define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include_once 'include/mysqli.php';
include_once( 'admin/adminis.func.php' );
require_once( 'include/hoteles.func.php' );
// require_once( 'include/correo.php' );
require_once( 'include/scanner.class.php' );

$ini = new ps_DB;
$temp = new ps_DB;
$temp2 = new ps_DB;
// $corCreo = new correo();
$scan = new scanner();
$feccc = date("j", strtotime('-1 day'));
date_default_timezone_set('Europe/Berlin');

$fechaHoy = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$fechaAyer = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
$fechaMAnte = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));
$diasMesAct = date("t", strtotime(date("Y") . "-" . date("m") . "-01"));
$iniMes = mktime(0,0,0,date('m'),1,date('Y'));
//$hora15 = mktime(15, 0, 0, date("m"), date("d"), date("Y"));
$hora2 = mktime(2, 0, 0, date("m"), date("d"), date("Y"));
$hora4 = mktime(4, 0, 0, date("m"), date("d"), date("Y"));
$horaTasa = mktime(14, 0, 0, date("m"), date("d"), date("Y"));
$ahora = time();
$horaG = date('G'); //hora actual 00 - 24

//$horaG = 14;

$texto = 'Script lanzado desde '.$_SERVER['SCRIPT_FILENAME']."\n<br>";
//echo date('H');
$ayer = leeSetup('fechaMod');
$fTasa = leeSetup('fechaTasa');
$mesesBitacora = leeSetup('mesesBitacora');
$correoMi = '';
$texto .= "fechaHoy=$fechaHoy"."\n<br>";
$texto .= "fechaAyer=$fechaAyer"."\n<br>";
$texto .= "fechaMAnte=$fechaMAnte"."\n<br>";
$texto .= "hora2=$hora2"."\n<br>";
$texto .= "hora4=$hora4"."\n<br>";
$texto .= "horaTasa=$horaTasa"."\n<br>";
$texto .= "ahora=$ahora"."\n<br>";
$texto .= "ahora actual=$horaG"."\n<br>";
$texto .= "timezone=".date_default_timezone_get()."\n<br>";

/**
 * Realiza el informe Diario y Mensual a las 00:00
 */
$texto .= "\n<br>".leeSetup('fechaInf')." + 86390 < ".time();
if ($horaG == 0
 		|| 1==1
		)  {

		/*
		 * Calcula y Avisa los Cierres
		 */
		$texto .= "\n<br>Haciendo los cierres\n<br>";
		$sale = '';
		// comercios que no se les realiza cierres (todos los comercios con nombre prueba y travels and discovery):
		$idNocierre = "1,71,72,91,114,115,119,130,141,124";

		//saco el listado de todos los comercios activos
		$q = "select id, idcomercio, nombre, cierrePer, maxCierre, cierrePor from tbl_comercio where activo = 'S' and llevacierre = 1 ".
				" and cierrePor = id and id not in ($idNocierre)";
// 		$q .= " and id = 24 "; //obligo al cierre de un comercio específico
		// and id in (select distinct cierrePor from tbl_comercio)";
		$temp->query($q);
// 		echo "$q\n<br>";
		$texto .= "$q\n<br>";
		$arrCom = $temp->loadRowList();

		//saco el listado de las empresas 
		$q = "select id, nombre from tbl_empresas where id != 5";
		$temp->query($q);
		$texto .= "$q\n<br>";
		$arrEmpr = $temp->loadRowList();
		$cmernom = '';

		foreach ($arrCom as $com) {
			foreach ($arrEmpr as $emp) {
				$strid = $strnom = $men = '';
				$texto .= "\n<br>Trabajando con ".$com[2]." y empresa ".$emp[1]."\n<br>";

				//busco identificadores y nombre de comercios agrupados
				$q = "select idcomercio, nombre from tbl_comercio where cierrePor = ".$com[5];
				$temp->query($q);
		// 		echo "$q\n<br>";
				$texto .= "$q\n<br>";
				$arrNom = $temp->loadRowList();
				foreach ($arrNom as $itnom){
					$strid .= $itnom[0]."','";
					$strnom .= $itnom[1].", ";
				}
				$strid = rtrim($strid,"','"); //identificador de comercios agrupados
				$strnom = rtrim($strnom,", "); //nombres de comercios agrupados

				//busco los datos del último cierre realizado al comercio
				$q = "select idcierre, r.idtransaccion, t.fecha_mod, t.fecha, t.estado from tbl_cierreTransac r, tbl_transacciones t, tbl_pasarela p where p.idPasarela = t.pasarela and t.idtransaccion = r.idtransaccion and t.idcomercio in ('{$strid}') and p.idempresa = ".$emp[0]." order by r.fecha desc limit 0,1";
				$texto .= "$q\n<br>";
	//error_log($q);
				$temp->query($q);
				if ($temp->getNumRows() == 0) {//Busco en la tabla de transacciones viejas
					$q = "select idcierre, r.idtransaccion, t.fecha_mod, t.fecha, t.estado "
							. " from tbl_cierreTransac r, tbl_transaccionesOld t, tbl_pasarela p where p.idPasarela = t.pasarela and t.idtransaccion = r.idtransaccion and t.idcomercio in ('{$strid}') and p.idempresa = ".$emp[0]." order by r.fecha desc limit 0,1";
					$texto .= "$q\n<br>";
					// echo "$q\n<br>";
					$temp->query($q);
				}

				if ($temp->getErrorMsg()) $texto .= "\n<br>$q \n<br>Error: ".$temp->getErrorMsg();
				$arrSal = $temp->loadRowList();
	//			print_r($arrSal);

				//si el comercio no tiene cierre pongo como fecha del último cierre unos min antes de la primera operación aceptada
				if (count($arrSal) == 0) {
					$q = "select idcomercio, idtransaccion, (fecha_mod - 60), (fecha - 60), estado from tbl_transacciones where idcomercio in ('{$strid}')"
							." and tipoEntorno = 'P' order by fecha_mod asc limit 0,1";
					$temp->query($q);
					$texto .= "$q\n<br>";
					if ($temp->getErrorMsg()) $texto .= "\n<br>$q \n<br>Error: ".$temp->getErrorMsg();
					$arrSal = $temp->loadRowList();
				}
	// 			print_r($arrSal);

				foreach ($arrSal as $items) {
					if ($items[4] == 'A') {
						$item[0] = $items[0];
						$item[1] = $items[1];
						$item[2] = $items[2];
					} else {
						$item[0] = $items[0];
						$item[1] = $items[1];
						$item[2] = $items[3];
					}

					$menNimp = ''; //mensaje no inmediato
					$menImp = ''; //mensaje inmediato
					$ffeUn = $item[2]; //fecha de la última transacción del cierre
					$operac = $item[1]; //Ultima operación a la que se realizó el cierre
					$dias = floor((time()-$ffeUn)/(60*60*24)); //días transcurridos entre el último cierre y hoy
					$texto .= $com[2]." - ".$com[3]." - ".$dias."\n<br>";

	//error_log("ffeUn = $ffeUn ");
	//error_log("operac = $operac ");
	//error_log("dias = $dias");
	//error_log($com[2]." - ".$com[3]);

					/*Comienza variación*/
					if($com[3] == 'Q') { //realizar el cierre quincenal
						$texto .= "\n<br>Verifica el cierre quincenal\n<br>";
						$texto .= date('j')." == 1 || (".mktime(0, 0, 0, date('n'), 1, date('Y'))." > ".$ffeUn." && ".date('n')." != ".date('n',$ffeUn).") ||<br>";
						$texto .= date('j')." == 16 || (".mktime(0, 0, 0, date('n'), 16, date('Y'))." > ".$ffeUn." && ".date('n')." != ".date('n',$ffeUn).")<br>";
						if (
	// 							(date('j') == 1 || (mktime(0, 0, 0, date('n'), 1, date('Y')) > $ffeUn && date('n') != date('n',$ffeUn)+1)) ||
								(date('j') == 16 || (mktime(0, 0, 0, date('n'), 16, date('Y')) > $ffeUn && date('n') != date('n',$ffeUn)+16))
								) {
							if (date('j') >= 16) $fecTop = mktime(0, 0, 0, date('n'), 16, date('Y'));
							else $fecTop = mktime(0, 0, 0, date('n'), 1, date('Y'));
							//Si es día 1ro o si la fecha del último cierre es menor que el día 1ro del mes pero no coinciden los meses del ult. cierre +1
							//y del mes actual
							$texto .= "Se realiza el cierre Quincenal\n<br>";
							$q = "select count(idtransaccion) cant from tbl_transacciones "
									. " where estado in ('A','B','V','R') "
										. " and tipoEntorno = 'P' "
										. " and idcomercio in ('{$strid}')"
										. " and fecha_mod between ".($ffeUn+10)." and ".$fecTop;//le sumo unos segundos para evitar tomar la últ. oper. del
																								//cierre anterior
						}
	// 					if ($dias > 14) {
	// 						$q = "select count(idtransaccion) cant from tbl_transacciones "
	// 								. " where estado = 'A' "
	// 									. " and tipoEntorno = 'P' "
	// 									. " and idcomercio in ('{$strid}')"
	// 									. " and fecha > ".(time()-15*24*60*60);
	// 					}
					} elseif($com[3] == 'S') { //realizar el cierre semanal
						$texto .= "\n<br>Verifica el cierre Semanal\n<br>";
	// 					if ( $dias > 6) {
						if ( date('j') == '8' || date('j') == '16' || date('j') == '23' || date('j') == '1' || $dias > 7) {
							$texto .= "Se realiza el cierre Semanal\n<br>";
							$q = "select count(idtransaccion) cant from tbl_transacciones "
									. " where estado in ('A','B','V','R') "
									. " and tipoEntorno = 'P' "
									. " and idcomercio in ('{$strid}')"
									. " and fecha_mod > ".(time()-7*24*60*60);
						}
					} elseif($com[3] == 'D') { //realizar los cierres diarios
						$texto .= "\n<br>Verifica el cierre Diario\n<br>";
							$q = "select count(idtransaccion) cant from tbl_transacciones "
									. " where estado in ('A','B','V','R') "
									. " and tipoEntorno = 'P' "
									. " and idcomercio in ('{$strid}')"
									. " and fecha_mod > ".(time()-24*60*60);
					}

					//se realiza el cierre mensual para todos los comercios
					$texto .= "\n<br>Verifica el cierre Mensual\n<br>";
					$texto .= date('j')." == 1 || (".mktime(0, 0, 0, date('n'), 1, date('Y'))." > ".$ffeUn." && ".date('n')." != ".date('n',$ffeUn).")<br>";
					if (date('j') == 1
	// 						|| 1==1
							)
					{
						//Si es día 1ro o si la fecha del último cierre es menor que el día 1ro del mes pero no coinciden los meses del ult. cierre +1 y
						// del mes actual
						$texto .= "Se realiza el cierre Mensual\n<br>";
						$fecTop = mktime(0, 0, 0, date('n'), 1, date('Y'));
						$q = "select count(idtransaccion) cant from tbl_transacciones "
								. " where estado in ('A','B','V','R') "
									. " and tipoEntorno = 'P' "
									. " and idcomercio in ('{$strid}')"
									. " and fecha_mod between ".($ffeUn+10)." and ".$fecTop;//le sumo unos segundos para evitar tomar la últ. oper. del
																								//cierre anterior
					}


					$texto .= "$q\n<br>";
					$temp->query($q);
					$cant = $temp->f('cant');
					if ($temp->getErrorMsg()) $texto .= "\n<br>$q \n<br>Error: ".$temp->getErrorMsg();

					/**Desvío al Cierre*/
					if ($cant > 0 && $strid == '129025985109') include ('cierra.php');
					/* */

					$q = "select * from tbl_empresas order by nombre";
					$temp->query($q);
					$arrEmp = $temp->loadRowList();
					$textAdd = "";

					//recorro todas las empresas nuestras para saber las operaciones que han pasado por cada una de ellas
					// foreach ($arrEmp as $item) {
					$acp = $dev = 0;
					$arraEsta = array('Aceptadas' => "'A'", 'Devueltas, anuladas y reclamadas' => "'B','V','R'", 'Todas' => "'A','B','V','R'");
					for ($i=0; $i<3; $i++) {
						switch ($i) {
							case '0': //Aceptadas en el período
								$q = "select truncate(sum(t.valor/100/t.tasa),2) suma, count(idtransaccion) total from tbl_transacciones t, tbl_pasarela p "
										." where t.estado in ('A') and t.pasarela = p.idPasarela and p.idempresa = {$emp[0]} and "
										." t.idcomercio in ('{$strid}') and t.fecha_mod > $ffeUn";
								$texto .= $q."\n<br>";
								$temp->query($q);
								if ($temp->f('suma') > 0) {
									$acp = $temp->f('suma');
									$tot = $temp->f('total');
									$textAdd .= " En {$emp[1]} para las Aceptadas la cantidad es de $acp\n<br>";
									$textAdd .= " En {$emp[1]} n&uacute;mero de operaciones Aceptadas es $tot\n<br>";
									$texto .= $textAdd."\n<br>";
								}
								break;
							case '1': //Devueltas, anuladas y reclamadas en el período
								$q = "select truncate(sum((t.valor_inicial-t.valor)/100/t.tasaDev),2) suma from tbl_transacciones t, tbl_pasarela p "
										." where t.estado in ('B','V','R') and t.pasarela = p.idPasarela and p.idempresa = {$emp[0]} and "
										." t.idcomercio in ('{$strid}') and t.fecha_mod > $ffeUn";
								$texto .= $q."\n<br>";
								$temp->query($q);
								if ($temp->f('suma') > 0) {
									$dev = $temp->f('suma');
									$textAdd .= " En {$emp[1]} para las Devueltas, anuladas y reclamadas la cantidad es de $dev\n<br>";
									$texto .= $textAdd."\n<br>";
								}
								break;
							case '2':  //Aceptadas - Devueltas
								$textAdd .= " En {$emp[1]} para Aceptadas - Devueltas la cantidad es de ". ($acp-$dev) ."\n<br>";
								$texto .= $textAdd."\n<br>";
						}


					}
					// }
// $sale .= "Comparación nombres $cmernom == $strnom<br>";
					if ($cant > 0 && $cmernom != $strnom) {
						$texto .= "tiene $cant de transacciones Aceptadas\n<br>";
						//llegó al número de días o está pasado
						$menImp .= "El(Los) comercio(s) <strong>".$strnom."</strong> ha(n) llegado al límite por tiempo, $com[3], necesita se"
										." le realice el Cierre de inmediato. La última operación procesada fué la No. $operac\n";
						//llegegará en las próximas 24 hr al número de días.
	// 					elseif ($dcon <= $dias+1) $menNimp .= "El comercio <strong>".$com[2]."<strong/> llegará en las próximas 24 hrs al límite máximo "
	// 															." por días para que se le realice el Cierre.\n<br>";
	//					$texto .= $menImp;
					}

					$texto .= "\n<br>Verifica el cierre por Montos\n<br>";
					$q = "select round(sum(valor/100/tasa),2) suma from tbl_transacciones where estado in ('A','B','V','R') and idcomercio in ('{$strid}') and ".
							" fecha_mod > $ffeUn";
	// if ($com[0] == 23) echo "$q\n<br>";
					$temp->query($q);
					$valeT = $temp->f('suma');

					/**Desvío al Cierre*/
					if ($temp->f('suma') > 0 && $strid == '129025985109') include ('cierra.php');
					/* */
	//					$texto .= $q."<br>\n";
					$texto .= $com[2]." - valor=".$com[4]." - tiene en realidad=".$valeT."<br>\n";

						//llegó al monto límite o está pasado
					if ($valeT >= $com[4] && $cmernom != $strnom) $menImp .= "El(Los) comercio(s) <strong>".$strnom."</strong> tiene(n) acumulado hasta este momento $valeT Euros, "
								. "lo que hace que haya llegado al límite por monto, necesita se le realice el Cierre de inmediato"
								. ". La última operación procesada fué la No. $operac.\n";
						//llegará en las próximas 24 hr .
					elseif ($valeT >= $com[4]+($valeT/$dias) && $cmernom != $strnom) $menNimp .= "El(Los) comercio(s) <strong>".$strnom."</strong> tiene(n) acumulado hasta este momento "
								." $valeT Euros, llegará en las próximas hrs al límite máximo por monto para que se le realice el Cierre"
								." . La última operación procesada fué la No. $operac.\n<br>";

					$cmernom = $strnom;

					$texto .= $men;
	//		if (strlen($men) == 0) $men .= "No hay ningún cierre pendiente";
				}

			}
					if(strlen($menImp) > 0) $men .= $menImp;
					elseif(strlen($menNimp) > 0)  $men .= $menNimp;
					if (strlen($men) > 0) $sale .= "$men<br>$textAdd\n<br>";
		}

		if (strlen($sale) == 0) $sale .= "No hay ningún cierre pendiente";
		echo $sale;
		// if ($corCreo->todo(37,"Avisos de Cierres", $sale)) $texto .= "\n<br>Avisos de cierre enviados correctamente";
// 		echo $sale;
		/* 	Fin del cálculo de los Cierres 	*/


	$texto .= "\n<br>Termina recorrido de las 6pm\n<br>";
}

// $corCreo->set_message($texto."\n<br>Ejecutado satisfactoriamente a las ".date('d/m/Y H:i'));
// $corCreo->set_subject("Ejecución del Cron");
// $corCreo->envia(5);
// echo "ok<br>".$texto;
?>
