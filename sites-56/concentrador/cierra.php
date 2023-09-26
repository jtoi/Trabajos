<?php

defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
include_once 'admin/classes/class_cierre.php';
$temp = new ps_DB;
$corCreo = new correo;
$pase = false;

$q = "select * from tbl_empresas order by nombre";
$temp->query($q);
$arrEmp = $temp->loadRowList();
$textAdd = "";

define(PDF_PAGE_ORIENTATION, 'L');
$cierre = new MYPDF('L', PDF_UNIT, 'Letter', true, 'UTF-8', false);
$cierre->arrcom = $com;


//recorro todas las empresas nuestras para saber las operaciones que han pasado por cada una de ellas
foreach ($arrEmp as $item) {
	$acp = $dev = 0;
	$cierre->arremp = $item;
			
	$q = "select (numeracion + 1) 'num' from tbl_cierreTransac where idcomercio = '".$com[0]."' and idempresa = '".$item[0]."'
			order by numeracion desc limit 0,1";
	$temp->query($q);
	$cierre->numCier = $temp->f('num');
			
	$cierre->inicializa();
	
	for ($j=0; $j<3; $j++) {
		$j = $j;
		echo $j;
		if ($j == '0') {//Aceptadas en el período
				$q = "select distinct moneda from tbl_transacciones t, tbl_pasarela p "
						." where t.estado in ('A') and t.pasarela = p.idPasarela and p.idempresa = {$item[0]} and "
						." t.idcomercio in ('{$strid}') and t.fecha_mod between $ffeUn and ".mktime(0,0,0,date('m'),date('d'),date('Y'));
				$temp->query($q);
				$monedas =  implode(',', $temp->loadResultArray());
				
				$q = "select idmoneda, moneda, denominacion from tbl_moneda where idmoneda in ($monedas)";
				$temp->query($q);
				if ($temp->num_rows() > 0) {
					$pase = true;
					$arrMon = $temp->loadRowList();
					foreach ($arrMon as $moneda) {
						$cierre->estop = 'Aceptadas';
						$cierre->arrmon = $moneda;
						
// 						$cierre->EncTrans();
						
						$q = "select idtransaccion, c.nombre, identificador, (select nombre from tbl_reserva r where r.id_transaccion = t.idtransaccion), "
								." t.codigo, from_unixtime( t.fecha_mod, '%d/%m/%y - %H:%i' ), format(t.valor_inicial/100,2),"
								." format((t.valor_inicial/t.tasa)/100,2), format(t.tasa,4) "
								." from tbl_transacciones t, tbl_pasarela p, tbl_comercio c "
								." where t.estado in ('A') and t.idcomercio = c.idcomercio and t.pasarela = p.idPasarela and p.idempresa = {$item[0]} and "
								." t.idcomercio in ('{$strid}') and t.fecha_mod between $ffeUn and ".mktime(0,0,0,date('m'),date('d'),date('Y'))
								." and t.moneda = ".$moneda[0];
						$temp->query($q);
						$i=1;
						$tampag = 0;
						$cierre->EncTrans();
						foreach ($temp->loadRowList() as $oper) {
							$tampag++;
							array_unshift($oper, $i++);
							$cierre->llenaOper($oper, '1');
							if ($tampag == 40) {
								$cierre->EncTrans();
								$tampag = 0;
							}
						}
						$cierre->totales('1');
					}
				}
		} elseif ($j == '1') { //Devueltas, anuladas y reclamadas en el período
				$q = "select distinct moneda from tbl_transacciones t, tbl_pasarela p "
						." where t.estado in ('B','V','R') and t.pasarela = p.idPasarela and p.idempresa = {$item[0]} and "
						." t.idcomercio in ('{$strid}') and t.fecha_mod between $ffeUn and ".mktime(0,0,0,date('m'),date('d'),date('Y'));
				$temp->query($q);
				$monedas =  implode(',', $temp->loadResultArray());
				
				if (strlen($monedas) > 0) {
					$pase = true;
					$q = "select moneda from tbl_moneda where idmoneda in (".$monedas.")";
					$temp->query($q);
					$mdenom = $monedas =  implode(', ', $temp->loadResultArray());
					$cierre->estop = 'Devueltas y Anuladas en '.$mdenom;

					$q = "select idtransaccion, c.nombre, identificador, (select nombre from tbl_reserva r where r.id_transaccion = t.idtransaccion), "
							." t.codigo, from_unixtime( t.fecha, '%d/%m/%y - %H:%i' ), format(t.valor_inicial/100,2),"
							." format((t.valor_inicial/t.tasa)/100,2), format(t.tasa,4), from_unixtime( t.fecha_mod, '%d/%m/%y - %H:%i' ), "
							." format((t.valor_inicial - t.valor)/100,2), format(((t.valor_inicial - t.valor)/100)/t.tasaDev,2), format(t.tasaDev, 4), "
							." format(-1*((t.valor_inicial - t.valor)/100)/t.tasaDev,2), "
							." case t.estado 
									when 'P' then 'En Proceso' 
									when 'A' then if (solDev = 0, 'Aceptada', 'Sol. Devolc.')
									when 'D' then 'Denegada' 
									when 'N' then 'No Procesada' 
									when 'B' then 'Anulada' 
									when 'R' then 'Reclamada' 
									else 'Devuelta' end"
							." from tbl_transacciones t, tbl_pasarela p, tbl_comercio c "
							." where t.estado in ('B','V','R') and t.idcomercio = c.idcomercio and t.pasarela = p.idPasarela and p.idempresa = {$item[0]} and "
							." t.idcomercio in ('{$strid}') and t.fecha_mod between $ffeUn and ".mktime(0,0,0,date('m'),date('d'),date('Y'));
// echo "<br>$q<br>";


// if (t.fecha_mod < t.fechaPagada,(-1 * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100)), "
// 							." if (t.fecha_mod < t.fechaPagada,(-1 * ((t.valor_Inicial-t.valor)/100/tasaDev)), (t.valor/100/tasaDev)), round(t.tasaDev,4) tasaDev, "
// 							." if (t.fecha_mod < t.fechaPagada,(-1 * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100)),
							
							
					$temp->query($q);
					$i=1;
					$tampag = 0;
					$cierre->EncTransD();
					foreach ($temp->loadRowList() as $oper) {
						$tampag++;
						array_unshift($oper, $i++);
						$cierre->llenaOper($oper, '2');
						if ($tampag == 40) {
							$cierre->EncTransD();
							$tampag = 0;
						}
					}
						$cierre->totales('2');
				}
		} else {  //Aceptadas - Devueltas
				$textAdd .= " En {$item[1]} para Aceptadas - Devueltas la cantidad es de ". ($acp-$dev) ."\n<br>";
				$texto .= $textAdd."\n<br>";
		}
			

	}
// 	echo $cierre->tgEur." - "; print_r($cierre->arrTG);
	if ($pase)
		$cierre->Output($_SERVER['DOCUMENT_ROOT']."concentrador/desc/".utf8_encode($cierre->nombre), 'F');
}	

?>