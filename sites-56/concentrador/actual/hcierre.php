<?php

define( '_VALID_ENTRADA', 1 );

// include_once( 'configuration.php' );
// include_once( 'admin/classes/entrada.php' );
// include 'include/mysqli.php';
// include_once( 'admin/adminis.func.php' );
// require_once( 'include/hoteles.func.php' );
// require_once( 'include/correo.php' );


// $temp = new ps_DB;
// $corCreo = new correo();
date_default_timezone_set('Europe/Berlin');

$texto = '';
$texto .= "\n<br>Haciendo los cierres\n<br>";
$sale = '';
// comercios que no se les realiza cierres (todos los comercios con nombre prueba y travels and discovery):
$idNocierre = "1,71,72,91,114,115,119,130,141,124";
$estado = "'A','B','V','R'"; //estado de las operaciones que se procesan en el cierre

//saco el listado de todos los comercios activos
$q = "select id, idcomercio, nombre, cierrePer, maxCierre, cierrePor from tbl_comercio where activo = 'S' and llevacierre = 1 and cierrePor = id and id not in ($idNocierre)";
// $q .= " and id = 24 "; //obligo al cierre de un comercio específico
// and id in (select distinct cierrePor from tbl_comercio)";
$temp->query($q);
// echo "$q<br>";exit;
// $texto .= "$q\n<br>";
$arrCom = $temp->loadRowList();
// echo json_encode($arrCom);exit;
// print_r($arrCom);

foreach ($arrCom as $com) {
	$strid = $strnom = $textoi =  $textAdd = '';
	$pasoq = 0;

	//busco identificadores y nombre de comercios agrupados
	$q = "select idcomercio, nombre from tbl_comercio where cierrePor = ".$com[5];
	$temp->query($q);
	// echo "$q\n<br>";
	// $textoi .= "$q\n<br>";
	$arrNom = $temp->loadRowList();
	foreach ($arrNom as $itnom){
		$strid .= $itnom[0]."','";
		$strnom .= $itnom[1].", ";
	}
	$strid = rtrim($strid,"','"); //identificador de comercios agrupados
	$strnom = rtrim($strnom,", "); //nombres de comercios agrupados
	$textoi .= $strnom;

	if($com[3] == 'Q') {
		if (date('j') == 1 || date('j') == 16) {
			$textoi .= "\n<br>Trabajando con ".$com[2]."<br>y los comercios asociados: $strnom<br>";

			if (date('j') == 1) {
				$ffeUn 	= mktime(0, 0, 0, date('n')-1, 16, date('Y'));
				$fecTop	= mktime(0, 0, 0, date('n'), 1, date('Y'));
			} else {
				$ffeUn 	= mktime(0, 0, 0, date('n'), 1, date('Y'));
				$fecTop	= mktime(0, 0, 0, date('n'), 16, date('Y'));
			}

            $textoi .= "Se realiza el cierre Quincenal<br>";
			$pasoq = 1;

		}
	} elseif($com[3] == 'S') { //realizar el cierre semanal
		if ( date('j') == '8' || date('j') == '16' || date('j') == '23' || date('j') == '1') {
			$textoi .= "\n<br>Trabajando con ".$com[2]."<br>y los comercios asociados: $strnom<br>";
			switch (date('j')) {
				case '1':
					$ffeUn 	= mktime(0, 0, 0, date('n')-1, 23, date('Y'));
					$fecTop	= mktime(0, 0, 0, date('n'), 1, date('Y'));
					break;
				
				case '8':
					$ffeUn 	= mktime(0, 0, 0, date('n'), 1, date('Y'));
					$fecTop	= mktime(0, 0, 0, date('n'), 8, date('Y'));
					break;
				
				case '16':
					$ffeUn 	= mktime(0, 0, 0, date('n'), 8, date('Y'));
					$fecTop	= mktime(0, 0, 0, date('n'), 16, date('Y'));
					break;
				
				case '23':
					$ffeUn 	= mktime(0, 0, 0, date('n'), 16, date('Y'));
					$fecTop	= mktime(0, 0, 0, date('n'), 23, date('Y'));
					break;
				
			}

			$textoi .= "Se realiza el cierre Semanal<br>";
			$pasoq = 1;

		}
	} elseif($com[3] == 'M') { //realizar el cierre Mensual
		if ( date('j') == '1' ) {
			$textoi .= "\n<br>Trabajando con ".$com[2]."<br>y los comercios asociados: $strnom<br>";
			$ffeUn 	= mktime(0, 0, 0, date('n')-1, 1, date('Y'));
			$fecTop	= mktime(0, 0, 0, date('n'), 1, date('Y'));
		
			$textoi .= "Se realiza el cierre Mensual<br>";
			$pasoq = 1;

		}
	} elseif($com[3] == 'D') { //realizar el cierre Diario
		$textoi .= "\n<br>Trabajando con ".$com[2]."<br>y los comercios asociados: $strnom<br>";
		$ffeUn 	= mktime(0, 0, 0, date('n'), date('j')-1, date('Y'));
		$fecTop	= mktime(0, 0, 0, date('n'), date('j'), date('Y'));
	
		$textoi .= "Se realiza el cierre Diario<br>";
		$pasoq = 1;

	}

	if ($pasoq == 1) {
		$q = "select count(idtransaccion) cant from tbl_transacciones where estado in ($estado) and tipoEntorno = 'P' and idcomercio in ('{$strid}') and fecha_mod between ".($ffeUn)." and ".$fecTop;
		// $textoi .= " $q<br>";

		$temp->query($q);
		$cant = $temp->f('cant');
		$textoi .= "Total de operaciones en el período a cerrar: $cant<br>";

		if ($cant > 0) {// Si, el comercio tiene operaciones para cerrar
			$q = "select * from tbl_empresas where id not in (5) order by nombre";
			$temp->query($q);
			$arrEmp = $temp->loadRowList();
			$textAdd = "";

			// Determino los datos de las empresas
			foreach ($arrEmp as $item) {
				$arrPosc = array(
					array('Aceptadas', "'A'", "truncate(sum(t.valor/100/t.tasa),2)", "count(idtransaccion)"),
					array('Devueltas, anuladas y reclamadas', "'B','V','R'", "truncate(sum(t.valor/100/t.tasaDev),2)", "count(idtransaccion)"),
					array('Todas', "'A','B','V','R'", "0", "0"),
				);
				$acp = $tot = 0;
				for ($i=0; $i<count($arrPosc); $i++) {
					// if ($value == 'A')
						$q = "select ".$arrPosc[$i][2]." suma, ".$arrPosc[$i][3]." total from tbl_transacciones t, tbl_pasarela p where t.estado in (".$arrPosc[$i][1].") and t.pasarela = p.idPasarela and p.idempresa = {$item[0]} and t.idcomercio in ('{$strid}') and fecha_mod between ".($ffeUn)." and ".$fecTop;
					// if ($value == "'B','V','R'")
					// 	$q = "select truncate(sum(t.valor/100/t.tasaDev),2) suma, count(idtransaccion) total from tbl_transacciones t, tbl_pasarela p where t.estado in ($value) and t.pasarela = p.idPasarela and p.idempresa = {$item[0]} and t.idcomercio in ('{$strid}') and fecha_mod between ".($ffeUn)." and ".$fecTop;
					$temp->query($q);
					// $textAdd .= $q."<br>";
					if ($temp->f('suma') > 0) {
						$acp += $temp->f('suma');
						$tot += $temp->f('total');
						$textAdd .= " En {$item[1]} para ".$arrPosc[$i][0]." la cantidad es de ".$temp->f('suma')."\n<br>";
						$textAdd .= " En {$item[1]} n&uacute;mero de operaciones ".$arrPosc[$i][0]." es ".$temp->f('total')."\n<br>";
					}
				}
				if ($acp > 0) {
					$textAdd .= " En {$item[1]} para Todas la cantidad es de $acp\n<br>";
					$textAdd .= " En {$item[1]} n&uacute;mero de operaciones Todas es $tot\n<br>";
				}
			}
			$textoi .= $textAdd."\n<br>";
		} else $textoi = $textAdd = '';
	} else $textoi = $textAdd= '';
	$texto .= $textoi;
}
if (count($arrCom) == 0) $texto .= "No hay ningún cierre pendiente";
if ($corCreo->todo(37,"Avisos de Cierres", $texto)) $texto .= "\n<br>Avisos de cierre enviados correctamente";
// echo $texto;
?>