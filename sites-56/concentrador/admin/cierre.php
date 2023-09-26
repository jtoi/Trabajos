<?php
defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
/*
 * Fichero que se ejecuta para la realización de los cierres
 * 
 */

global $temp;
global $fechaHoy;
//$fechaHoy = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
/********************************************************************************************************/
//for ($i = 1; $i<129; $i++) {
//$fechaHoy = mktime(5, 0, 0, 7, 23+$i, 2010); //comentar esta línea para que coja la fecha de la máquina

/********************************************************************************************************/
//echo "Fecha en que está corriendo : ".date("d/m/y H:i:s",$fechaHoy)."<br>";

//Lee los datos de los comercios de la tabla de los comercios, de todos los comercios ya que pueden haber comercios que hayan pasado de producción a
//desarrollo o de activo a inactivo y aún tengan transacciones válidas 
$query = "select c.idcomercio, c.nombre, c.cierrePer, c.horIniCierre, c.minCierre, c.maxCierre, c.cierreAnt, c.cuota, c.mensConcentr, c.cuotaTarjeta,
			c.retropago, c.transfr, c.swift, c.cbancario, c.minbancario from tbl_comercio c";
//echo $query."<br>";
$temp->query($query);
$comercios = $temp->loadObjectList();

foreach ($comercios as $com) {
//	Para cada comercio 
//echo $com->nombre."<br>";
//echo $com->idcomercio."<br>";
	
//	buscar la fecha del último cierre realizado
	$query = "select fechaFin from tbl_cierres where idcomercio = '$com->idcomercio' order by fechaFin desc limit 0,1";
//echo $query."<br>";
	$temp->query($query);
	if ($temp->num_rows() > 0) {
		$ultiFecha = $temp->f('fechaFin');
		$ultiMes = date('m',$ultiFecha);
		if ($ultiMes == 12) $ultiMes = 0; //Pasa el mes a cero para que corra en Enero
	} else {
		$ultiMes = '';
		$ultiFecha = 0;
	}

//	Si la fecha del último cierre es menor que 24 horas atrás o el comercio es nuevo y nunca se ha hecho cierre
	if ($ultiFecha < ($fechaHoy - 24 * 60 * 60) || $temp->num_rows() == 0) {
		
		$mesantprim = mktime($com->horIniCierre, 0, 0, date('m', $fechaHoy)-1, 1, date('y', $fechaHoy)); //inicio del mes anterior
		$mesantfin = mktime($com->horIniCierre, 0, 0, date('m', $fechaHoy), 1, date('y', $fechaHoy))-1; //fin del mes anterior
		
//		si estamos a día primero en vez de realizar el cierre por valor máximo hago el cierre del mes lo mismo con el quincenal, semanal, y diario
		if (date('d', $fechaHoy) != 1 && date('d', $fechaHoy) != 16) {
			/*
			* Cierre por valor màximo (V)
			*/

			if ($temp->num_rows() == 0) $fecha1 = 1257836040;
			else $fecha1 = $ultiFecha + 1;
			$fecha2 = $fechaHoy;

//			lo primero es lo primero, ver si el comercio ha hecho guaniquiqui para hacer el cierre no vamos a trabajar para el inglés
			$valor = chequea($com->idcomercio, $fecha1, $fecha2);
			
//			echo "valor=$valor<br>";
			if ($valor > 0) {
		//		Ahora si, compruebo si el comercio tiene definido monto maximo para realizar el cierre por monto máximo
				if ($com->maxCierre != '' && ($com->maxCierre > 0 && $com->maxCierre < 1000000000)) {

		//			compruebo a ver si el monto de las transacciones en producción llegan a esa cifra
					if ($com->maxCierre < $valor) {
						cierre($com, $fecha1, $fecha2, 'V');
					}
				}
			}
		}
		
		/*
		 * Cierre Mensual (M) Lo realiza siempre aunque los comercios tengan otra periodicidad indicada
		 * debido a que este cierre es el que termina las operaciones del mes y concilia lo que se debe
		 */
		
		if (date('d', $fechaHoy) == 1) {
			
//			fecha1 y fecha2 son el final del cierre anterior (fecha1) y el final de este cierre, fechaAux es el comienzo del mes
//			que estamos cerrando y que puede o no coincidir con fecha1
			$fecha1 = $ultiFecha + 1; //un segundo despues del fin del cierre anterior
			$fechaAux = mktime($com->horIniCierre, 0, 0, date('m', $fechaHoy)-1, 1, date('y', $fechaHoy)); //inicio del mes anterior
			$fecha2 = mktime($com->horIniCierre, 0, 0, date('m', $fechaHoy), 1, date('y', $fechaHoy))-1; //fin del mes anterior
		
//			lo primero es lo primero, ver si el comercio ha hecho guaniquiqui para hacer el cierre no vamos a trabajar para el inglés
			$valor = chequea($com->idcomercio, $fecha1, $fecha2);
			
			if ($valor > 0 && $ultiMes < date('m',$fecha2)) {
//				Wao, coño hay dinero y no se ha hecho el cierre vamos a hacerlo
				cierre($com, $fecha1, $fecha2, 'M', $fechaAux);
			}
		}
		
		
		/*
		 * Cierre Mensual (M) Lo realiza siempre aunque los comercios tengan otra periodicidad indicada
		 * debido a que este cierre es el que termina las operaciones del mes y concilia lo que se debe
		 */
		
		if (date('d', $fechaHoy) == 16 && $com->cierrePer == 'Q') {
			
//			fecha1 y fecha2 son el final del cierre anterior (fecha1) y el final de este cierre, fechaAux es el comienzo del mes
//			que estamos cerrando y que puede o no coincidir con fecha1
			$fecha1 = $ultiFecha + 1; //un segundo despues del fin del cierre anterior
			$fecha2 = mktime($com->horIniCierre, 0, 0, date('m', $fechaHoy), 15, date('y', $fechaHoy))-1; //fin del mes anterior
		
//			lo primero es lo primero, ver si el comercio ha hecho guaniquiqui para hacer el cierre no vamos a trabajar para el inglés
			$valor = chequea($com->idcomercio, $fecha1, $fecha2);
			
			if ($valor > 0 && $ultiMes < date('m',$fecha2)) {
//				Wao, coño hay dinero y no se ha hecho el cierre vamos a hacerlo
				cierre($com, $fecha1, $fecha2, 'Q');
			}
		}
		
		
	}
//}
}

function cierre($comerIn, $fechaIn, $fechaOut, $tipoCierre, $fechaApoll = null) {
	global $temp;
	global $fechaHoy;
	$numDCom = $totDCom = $devDCom = $numACom = $totACom = 0;

	if ($tipoCierre == 'M') {
		$whereM = "idcomercio = '".$comerIn->idcomercio."' and tipoEntorno = 'P' and fecha_mod between $fechaApoll and $fechaOut";
	}
	$whereIn = "idcomercio = '".$comerIn->idcomercio."' and tipoEntorno = 'P' and fecha_mod between $fechaIn and $fechaOut";
	
//	buscar las todas las transacciones aceptadas en el período
	$where = "$whereIn and estado = 'A'";
	$query = "select idtransaccion, moneda, valor/100 valor, valor_inicial/100 valor_inicial, tasa, euroEquiv, estado from tbl_transacciones where $where";
	$temp->query($query);
	$arrTransA = $temp->loadAssocList();
//	print_r($arrTransA);
	
	//busca cuanto hay por transacciones aceptadas en el período del cierre
	$query = "select count(*) total, sum((valor/100)/tasa) suma from tbl_transacciones where $where";
	$temp->query($query);
	$numA = $temp->f('total');
	$totA = $temp->f('suma');
	if ($tipoCierre == 'M') { //busca cuanto hay por transacciones aceptadas en el mes completo
		$query = "select count(*) total, sum((valor/100)/tasa) suma from tbl_transacciones where $whereM and estado A";
		$temp->query($query);
		$numACom = $temp->f('total');
		$totACom = $temp->f('suma');
	}
	
	
//	buscar las todas las transacciones devueltas que fueron generadas en este cierre
	$where = "$whereIn and estado in ('B', 'V') and fecha between $fechaIn and $fechaOut";
	$query = "select idtransaccion, moneda, valor/100 valor, valor_inicial/100 valor_inicial, tasa, euroEquiv, estado from tbl_transacciones where $where";
	$temp->query($query);
	$arrTransD = $temp->loadAssocList();
	
	//busca cuanto hay por transacciones devueltas que fueron generadas en este cierre
	$query = "select count(*) total, sum((valor/100)/tasa) suma, sum(((valor_inicial-valor)/100)/tasa) dif from tbl_transacciones where $where";
	$temp->query($query);
	$numD = $temp->f('total');
	$totD = $temp->f('suma');
	$devD = $temp->f('dif');
	if ($tipoCierre == 'M') {//busca cuanto hay por transacciones devueltas que fueron generadas en este mes completo
		$query = "select count(*) total, sum((valor/100)/tasa) suma, sum(((valor_inicial-valor)/100)/tasa) dif from tbl_transacciones where $whereM 
			and estado in ('B', 'V') and fecha between $fechaApoll and $fechaOut";
		$temp->query($query);
		$numDCom = $temp->f('total');
		$totDCom = $temp->f('suma');
		$devDCom = $temp->f('dif');
	}
	
	
//	buscar las todas las transacciones devueltas que fueron generadas en cierres anteriores
	$where = "$whereIn and estado in ('B', 'V') and fecha < $fechaIn";
	$query = "select idtransaccion, moneda, valor/100 valor, valor_inicial/100 valor_inicial, tasa, euroEquiv, estado from tbl_transacciones where $where";
	$temp->query($query);
	$arrTransG = $temp->loadAssocList();
	
	$query = "select count(*) total, sum(((valor_inicial-valor)/100)/tasa) dif from tbl_transacciones where $where";
	$temp->query($query);
	$numG = $temp->f('total');
	$devG = $temp->f('dif');
	
//	busco los datos que faltan	
//	sumo de los cierres anteriores abarcados por el mes el descuento que se ha realizado por los porcientos resbalantes
	$query = "select sum(vtransacciones) total from tbl_cierres where idcomercio = '".$comerIn->idcomercio."' and fechaInicio >= $fechaApoll and fechaFin <= $fecha2";
	$temp->query($query);
	$montoDebitad = $temp->f('total');
	
	$montoTotal = $totA+$totD-$devG; //monto del cierre
	$montoCompensad = $totACom+$totDCom-$devG; //monto del mes completo
	if ($tipoCierre == 'M') $montoBusc = $montoCompensad; else $montoBusc = $montoTotal;

	$query = "select monto from tbl_cobroTarjeta where idcomercio = '".$comerIn->idcomercio."' and minCobro <= ".$montoBusc." and maxCobro >= ".$montoBusc;
	$temp->query($query);
	
//	Valores
	$cuota = $comerIn->cuota;
	if ($tipoCierre == 'M')	$mensualidad = $comerIn->mensConcentr; else $mensualidad = 0;
	$pagxTran = $comerIn->cuotaTarjeta*($numA+$numD);
	if ($tipoCierre == 'M') $porTarj = (($temp->f('monto') * $montoCompensad)/100) - $montoDebitad;
	else $porTarj = ($temp->f('monto') * $montoTotal)/100;
	$query = "select case when max(consecutivo) > 0 then max(consecutivo) + 1 else 1 end cons from tbl_cierres where idcomercio = '".$comerIn->idcomercio."'";
	$temp->query($query);
	$consec = $temp->f('cons');
	$devol = $comerIn->retropago * (($devD+$devG)/100);
	$trans = $montoTotal * $comerIn->transfr;
	$swift = $comerIn->swift;
	if (($montoTotal * ($comerIn->cbancario / 100)) < $comerIn->minbancario) $costBan = 12; else $costBan = $montoTotal * ($comerIn->cbancario / 100);
	$vtotal = $cuota + $mensualidad + $pagxTran + $porTarj + $devol + $trans + $swift;
	
//	Ya tengo todos los datos, voy que jodo a hacer el cierre para coger el id del mismo
	$query = "insert into tbl_cierres (idcomercio, fecha, fechaInicio, fechaFin, consecutivo, vinstal, vmenconc, vcosttarje, vtransacciones, vretrocobros,
					vtransf, vswift, vcostobanc, vtotal, totalsdesc, total, fichero, tipo, totalretro) 
				values ('".$comerIn->idcomercio."', $fechaHoy, $fechaIn, $fechaOut, $consec, $cuota, $mensualidad, $pagxTran, $porTarj, $devol, $trans,
					$swift, $costBan, $vtotal, $montoTotal, ".($montoTotal - $vtotal).", '".$tipoCierre.$comerIn->idcomercio."-$fechaHoy', '$tipoCierre',
					".($devD+$devG).")";
//echo $query."<br>";
	$temp->query($query);
//	Capturo el id
	$idcierre = $temp->last_insert_id();
	
	//pongo en cero la cuota de integración para que no vuelva a cobrarse
	$query = "update tbl_comercio set cuota = 0 when idcomercio = '".$comerIn->idcomercio."'";
	if ($cuota > 0) $temp->query ($query);

//	a guardar las transacciones que pertenecen al cierre que ya estoy hecho leña de tanto numerito
	foreach ($arrTransA as $item) {
		$query = "insert into tbl_cierreTransac values ($idcierre, '".$item['idtransaccion']."')";
		$temp->query($query);
	}
	foreach ($arrTransD as $item) {
		$query = "insert into tbl_cierreTransac values ($idcierre, '".$item['idtransaccion']."')";
		$temp->query($query);
	}
	foreach ($arrTransG as $item) {
		$query = "insert into tbl_cierreTransac values ($idcierre, '".$item['idtransaccion']."')";
		$temp->query($query);
	}
	
//echo "<br><br>";
	
}

function chequea ($comer, $fechaIn, $fechaOut) {
	global $temp;

	$query = "select sum(euroEquiv) valor from tbl_transacciones where idcomercio = '".$comer."' and tipoEntorno = 'P' and fecha_mod between $fechaIn and $fechaOut ";
	$temp->query($query);
	$valor = $temp->f('valor');
	
	return $valor;
}


?>
