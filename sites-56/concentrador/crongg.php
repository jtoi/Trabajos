<?php
/* 
 * Fichero para ejecutar cada hora
 * 
 */
define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';
include_once( 'admin/adminis.func.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'include/correo.php' );
require_once( 'include/scanner.class.php' );

$ini = new ps_DB;
$temp = new ps_DB;
$temp2 = new ps_DB;
$corCreo = new correo();
$scan = new scanner();

$fechaHoy = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$fechaAyer = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
$fechaMAnte = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));
//$hora15 = mktime(15, 0, 0, date("m"), date("d"), date("Y"));
$hora2 = mktime(2, 0, 0, date("m"), date("d"), date("Y"));
$hora4 = mktime(4, 0, 0, date("m"), date("d"), date("Y"));
$horaTasa = mktime(14, 0, 0, date("m"), date("d"), date("Y"));
$ahora = time();
//echo date('H');
$ayer = leeSetup('fechaMod');
$fTasa = leeSetup('fechaTasa');
$mesesBitacora = leeSetup('mesesBitacora');
$correoMi = '';
$correoMi .= "fechaHoy=$fechaHoy"."\n<br>";
$correoMi .= "fechaAyer=$fechaAyer"."\n<br>";
$correoMi .= "fechaMAnte=$fechaMAnte"."\n<br>";
$correoMi .= "hora2=$hora2"."\n<br>";
$correoMi .= "hora4=$hora4"."\n<br>";
$correoMi .= "horaTasa=$horaTasa"."\n<br>";
$correoMi .= "ahora=$ahora"."\n<br>";

$texto = 'Script lanzado desde '.$_SERVER['SCRIPT_FILENAME']."\n<br>";

//sincroniza las pagadas en transacciones con las pendientes en reserva
$query = "update tbl_reserva r, tbl_transacciones t
			set r.id_transaccion = t.idtransaccion, r.valor = r.valor_inicial, r.bankId = t.codigo, r.fechaPagada = t.fecha_mod, r.estado = 'A'
			where t.identificador = r.codigo and r.estado = 'P' and t.estado = 'A' and r.id_comercio = t.idcomercio";
$temp->query( $query );
$texto .= $query."\n<br>";

/**
* Pone los paises a las IPs que aún les falte
*/
if($hora4 <= time() && $horaTasa > time() ) { //entre las 10pm y las 8am hora de cuba
	if (function_exists(geoip_country_code3_by_name)) {
		$texto .= "Pone la ip de los países que aún le falten\n<br>";
		$q = "select ip from tbl_transacciones where (ip != '127.0.0.1' or ip != null) and idpais is null";
		$temp->query($q);
	
		while ($temp->next_record()) {
			$q = "select id from tbl_paises where iso = '".geoip_country_code3_by_name($temp->f('ip'))."'";
			$temp2->query($q);
			$id = $temp2->f('id');
			if ($id > 0) {
				$q = "update tbl_transacciones set idpais = $id where ip = '".$temp->f('ip')."'";
				$temp2->query($q);
			} else {
				$correoMi = "La ip ".$temp->f('ip')." no tiene país asociado, hablar con con K para que actualice el módulo geoip.\n<br>";
			}
		}
		$texto .= "\n<br>".$correoMi;
        if(strlen($correoMi) > 0) {
            $corCreo->todo(13,"IP que no resuelve",$correoMi);
            
//            $q = 'insert into tbl_traza (titulo,traza,fecha) values ("IP que no resuelve","'.html_entity_decode($correoMi).'",'.time().')';
//            $temp->query($q);
        }
	}
	
}

/**
 * Realiza el informe Diario y Mensual a las 00:00
 */
$texto .= "\n<br>".leeSetup('fechaInf')." + 86390 < ".time();
if ((leeSetup('fechaInf') ) < time() 
		|| 1==1
		)  {
//    Actualiza a Alex para que le llegue el correo
    $q = "update tbl_admin set activo = 'S' where idadmin = 166";
    $temp->query($q);
//    Actualiza a Luis para que no le llegue el correo
    $q = "update tbl_admin set activo = 'N' where idadmin = 13";
    $temp->query($q);
    
	//prepara el informe a info diario o mensual
		$texto .= "\n<br>Informe Diario:\n<br>";
		$mensage = '';

		for($d = 0; $d<2; $d++) {
			if ($d == 0 || date('d') == 1) { //si d=0 hace el diario si no chequea si es día 1 para hacer el del mes
				if ($d == 0) {
					$var = 'diario';
					$fecMod = $fechaAyer;
					$fe = date('d/m/Y', $fechaAyer);
					$fecha30Dante = mktime(0, 0, 0, date("m"), date("d")-30, date("Y"));
					$text = '% -30d';
				} else {
					$var = 'mensual';
					$fecMod = $fechaMAnte;
					$fe = date('M - Y',$fechaMAnte);
					$fecha30Dante = mktime(0, 0, 0, date("m")-12, date("d"), date("Y"));
					$text = '% -12m';
				}
//echo "fechaAyer=$fechaAyer<br>";
//echo "fecha30Dante=$fecha30Dante<br>";
				$elem = "case t.estado when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/t.tasa)), 
						(t.valor/100/t.tasa)) when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/t.tasa)),  
						t.valor/100/t.tasa) when 'A' then (t.valor/100/t.tasa) else '0.00' end";

				$mensage .= "<br />Cantidad de transacciones:";
				$q = "select count(idtransaccion) from tbl_transacciones where fecha between $fecMod and $fechaHoy and tipoEntorno = 'P' ";
//			echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
//			print_r($arrR);
				foreach ($arrR as $item) {
					$mensage .= " {$item[0]}";
				}
				$mensage .= "<br />";
//$mensage .='sdfgsdfhgdfh';
				$mensage .= "<br />Porciento de transacciones<br /><table><tr><td>Estado</td><td style='text-align:center;width:60px'>%</td>
				<td style='text-align:center;width:60px'>$text</td></tr>";
				$q = "select estado, format((count(t.idtransaccion)*100/(select count(idtransaccion) from tbl_transacciones where fecha "
						. " between $fecMod and $fechaHoy and tipoEntorno = 'P')),2) '%', format(((select count(idtransaccion) from "
						. " tbl_transacciones f where f.fecha between $fecha30Dante and $fechaHoy and f.estado = t.estado and "
						. " f.tipoEntorno = 'P') * 100 / (select count(idtransaccion) from tbl_transacciones where fecha between "
						. " $fecha30Dante and $fechaHoy and tipoEntorno = 'P')),2) '% -30' from tbl_transacciones t where "
						. " t.fecha between $fecMod and $fechaHoy and t.tipoEntorno = 'P' group by t.estado";
//			echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td><td "
							. "style='text-align:center;width:60px'>{$item[2]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Porciento de valores por monedas<br /><table><tr><td>Moneda</td><td style='text-align:center;width:60px'>"
						. "%</td><td style='text-align:center;width:60px'>$text</td></tr>";
				$q = "select m.moneda, format((sum(euroEquiv)*100/(select sum(euroEquiv) from tbl_transacciones r where "
						. " t.estado in ('A','V') and t.tipoEntorno = 'P' and fecha between $fecMod and $fechaHoy)),2), "
						. " format(((select sum(euroEquiv) from tbl_transacciones a where a.moneda = m.idmoneda and "
						. " a.estado in ('A','V') and a.tipoEntorno = 'P' and a.fecha between $fecha30Dante and $fechaHoy)*100/ "
						. " (select sum(euroEquiv) from tbl_transacciones a where a.estado in ('A','V') and a.tipoEntorno = 'P' "
						. " and a.fecha between $fecha30Dante and $fechaHoy)),2) from tbl_transacciones t, tbl_moneda m ".
						" where t.moneda = m.idmoneda and t.estado in ('A','V') and t.tipoEntorno = 'P' and t.fecha "
						. " between $fecMod and $fechaHoy group by t.moneda order by m.moneda";
//				echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td>"
							. "<td style='text-align:center;width:60px'>{$item[2]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Porciento de transacciones Aceptadas por Monedas<br /><table><tr><td>Moneda</td>"
						. "<td style='text-align:center;width:60px'>%</td><td style='text-align:center;width:60px'>$text</td></tr>";
				$q = "select m.moneda, format((count(idtransaccion)*100/(select count(idtransaccion) from tbl_transacciones r "
						. " where r.moneda = t.moneda and r.fecha between $fecMod and $fechaHoy and r.tipoEntorno = 'P')),2) '%', "
						. " format(((select count(idtransaccion) from tbl_transacciones f where f.moneda = t.moneda and "
						. " f.estado = 'A' and f.fecha between $fecha30Dante and $fechaHoy and f.tipoEntorno = 'P')*100/"
						. " (select count(idtransaccion) from tbl_transacciones r where r.moneda = t.moneda and r.fecha "
						. " between $fecha30Dante and $fechaHoy and r.tipoEntorno = 'P')),2) '% -30' "
								. " from tbl_transacciones t, tbl_moneda m "
								. " where t.moneda = m.idmoneda "
										. " and t.tipoEntorno = 'P' "
										. " and t.estado = 'A' "
										. " and t.fecha between $fecMod and $fechaHoy "
								. " group by t.moneda";
//			echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td>"
							. " <td style='text-align:center;width:60px'>{$item[2]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Porciento de transacciones Aceptadas por Pasarelas<br /><table><tr><td>Pasarela</td>"
							. " <td style='text-align:center;width:60px'>%</td><td style='text-align:center;width:60px'>$text</td></tr>";
				$q = "select p.nombre, format((count(idtransaccion)*100/(select count(idtransaccion) from tbl_transacciones r "
						. " where r.pasarela = t.pasarela and r.fecha between $fecMod and $fechaHoy and r.tipoEntorno = 'P')),2) '%', "
						. " format(((select count(idtransaccion) from tbl_transacciones f where f.pasarela = t.pasarela and "
						. " f.tipoEntorno = 'P' and f.estado = 'A' and f.fecha between $fecha30Dante and $fechaHoy) * 100 / "
						. " (select count(idtransaccion) from tbl_transacciones s where s.tipoEntorno = 'P' and s.pasarela = "
						. " t.pasarela and s.fecha between $fecha30Dante and $fechaHoy)),2) '% -30' "
								. " from tbl_transacciones t, tbl_pasarela p "
								. " where t.pasarela = p.idPasarela "
										. " and t.tipoEntorno = 'P' "
										. " and t.estado = 'A' "
										. " and t.fecha between $fecMod and $fechaHoy "
								. " group by pasarela";
//echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td>"
							. " <td style='text-align:center;width:60px'>{$item[2]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Monto de transacciones Aceptadas por Pasarelas<br /><table><tr><td>Pasarela</td>"
						. " <td style='text-align:center;width:60px'>Euros</td></tr>";
				$q = "select p.nombre, format(sum($elem),2) "
								. " FROM tbl_pasarela p, tbl_transacciones t "
								. " where t.pasarela = p.idPasarela "
										. " and t.estado in ('A','V','B') "
										. " and t.tipoEntorno = 'P' "
										. " and t.fecha BETWEEN $fecMod AND $fechaHoy "
								. " group by t.pasarela "
								. " order by p.nombre;";
//echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Porciento de transacciones Aceptadas por Comercios<br /><table><tr><td>Comercio</td>"
						. " <td>Eur/tr</td><td style='text-align:center;width:60px'>%</td><td style='text-align:center;width:60px'>$text</td></tr>";
				$q = "SELECT c.nombre, format(sum(case t.estado when 'B' then if (fecha_mod < fechaPagada, (-1 * ((valor_Inicial-valor)/100/tasa)), "
						. " (valor/100/tasa)) when 'V' then if (fecha_mod < fechaPagada, (-1 * ((valor_Inicial-valor)/100/tasa)),  valor/100/tasa) "
						. " when 'A' then (valor/100/tasa) else '0.00' end) / count(*), 2), format((count( idtransaccion ) *100 / ( "
						. " SELECT count( idtransaccion ) FROM tbl_transacciones r WHERE r.idcomercio = t.idcomercio AND "
						. " r.fecha BETWEEN $fecMod AND $fechaHoy AND r.tipoEntorno = 'P' )) , 2) '%', format((select "
						. " count(idtransaccion) from tbl_transacciones r where r.idcomercio = t.idcomercio and r.fecha > "
						. " $fecha30Dante and $fechaHoy and r.tipoEntorno = 'P' and r.estado = 'A')*100/(select "
						. " count(idtransaccion) from tbl_transacciones r where r.idcomercio = t.idcomercio and "
						. " r.fecha > $fecha30Dante and $fechaHoy and r.tipoEntorno = 'P') , 2) '% -30' " 
								. " FROM tbl_transacciones t, tbl_comercio c "
								. " WHERE t.idcomercio = c.idcomercio ".
										"AND t.estado IN ('A', 'V', 'B') ".
										"AND t.tipoEntorno = 'P' ".
										"AND t.fecha BETWEEN $fecMod AND $fechaHoy ".
								"GROUP BY t.idcomercio ".
								"ORDER BY sum(CASE t.estado WHEN 'A' THEN euroEquiv ELSE euroEquivDev END) DESC";
//			echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td>{$item[1]}</td><td style='text-align:center;width:60px'>{$item[2]}"
							. " </td><td style='text-align:center;width:60px'>{$item[3]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Montos de transacciones Aceptadas por Comercios<br /><table><tr><td>Comercio</td><td>Eur</td></tr>";
				$q = "select c.nombre, format(sum($elem),2) FROM tbl_comercio c, tbl_transacciones t where t.idcomercio = "
						. " c.idcomercio and t.estado in ('A','V','B') and t.tipoEntorno = 'P' AND t.fecha BETWEEN $fecMod AND "
						. " $fechaHoy group by t.idcomercio order by c.nombre;";
//			echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td>{$item[1]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Porciento de transacciones aceptadas por países<br /><table><tr><td>Pa&iacute;s</td>"
						. " <td style='text-align:center;width:60px'>%</td><td style='text-align:center;width:60px'>$text</td></tr>";
				$q = "select p.nombre, format(count(idtransaccion)*100/(select count(idtransaccion) from tbl_transacciones "
						. " where estado = 'A' and fecha between $fecMod and $fechaHoy),2) '%', format((select count(idtransaccion) "
						. " from tbl_transacciones f where f.idpais = p.id and f.estado = 'A' and fecha between $fecha30Dante "
						. " and $fechaHoy) *100 / (select count(idtransaccion) from tbl_transacciones s "
						. " where s.estado = 'A' and fecha between $fecha30Dante and $fechaHoy),2) '% -30' "
					. " from tbl_transacciones t, tbl_paises p "
					. " where t.idpais = p.id and estado in ('A','V','B') "
						. " and t.fecha between $fecMod and $fechaHoy "
					. " group by t.idpais order by count(idtransaccion) desc limit 0,10";
//			echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td><td style='text-align:center;width:60px'>{$item[2]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Porciento de transacciones aceptadas por países AIS<br /><table><tr><td>Pa&iacute;s</td>"
						. " <td style='text-align:center;width:60px'>%</td><td style='text-align:center;width:60px'>$text</td></tr>";
				$q = "select p.nombre, format(count(idtransaccion)*100/(select count(idtransaccion) from tbl_transacciones "
						. " where estado = 'A' and idcomercio in ('527341458854') and fecha between $fecMod and $fechaHoy),2) '%', "
						. " format((select count(idtransaccion) from tbl_transacciones f where  f.idcomercio in ('527341458854') "
						. " and f.idpais = p.id and f.estado = 'A' and fecha between $fecha30Dante and $fechaHoy) *100 / "
						. " (select count(idtransaccion) from tbl_transacciones s where  s.idcomercio in ('527341458854') "
						. " and s.estado = 'A'  and fecha between $fecha30Dante and $fechaHoy),2) '% -30' "
					. " from tbl_transacciones t, tbl_paises p "
					. " where t.idcomercio in ('527341458854') "
						. " and t.idpais = p.id "
						. " and t.estado = 'A' "
						. " and t.fecha between $fecMod and $fechaHoy "
					. " group by t.idpais "
					. " order by count(idtransaccion) desc "
					. " limit 0,10";
//			echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td>"
						. " <td style='text-align:center;width:60px'>{$item[2]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Porciento de transacciones aceptadas por países Cubana<br /><table><tr><td>Pa&iacute;s"
						. " </td><td style='text-align:center;width:60px'>%</td><td style='text-align:center;width:60px'>$text</td></tr>";
				$q = "select p.nombre, format(count(idtransaccion)*100/(select count(idtransaccion) from tbl_transacciones "
						. " where estado = 'A' and idcomercio in ('129025985109') and fecha between $fecMod and $fechaHoy),2) '%', "
						. " format((select count(idtransaccion) from tbl_transacciones f where  f.idcomercio in ('129025985109') "
						. " and f.idpais = p.id and f.estado = 'A' and fecha between $fecha30Dante and $fechaHoy) *100 / "
						. " (select count(idtransaccion) from tbl_transacciones s where  s.idcomercio in ('129025985109') "
						. " and s.estado = 'A'  and fecha between $fecha30Dante and $fechaHoy),2) '% -30' "
					. " from tbl_transacciones t, tbl_paises p "
					. " where t.idcomercio in ('129025985109') "
						. " and t.idpais = p.id "
						. " and t.estado = 'A' "
						. " and t.fecha between $fecMod and $fechaHoy "
					. " group by t.idpais "
					. " order by count(idtransaccion) desc "
					. " limit 0,10";
			//echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td>"
						. " <td style='text-align:center;width:60px'>{$item[2]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Estado de transacciones Cubana<br /><table><tr><td>Estado</td><td>Cant</td>"
						. " <td style='text-align:center;width:60px'>%</td></tr>";
				$q = "select estado, count(idtransaccion), format((count(idtransaccion)/(select count(idtransaccion) "
						. " from tbl_transacciones where idcomercio = '129025985109' and fecha between $fecMod and $fechaHoy)*100),2) from tbl_transacciones t where t.idcomercio in ('129025985109') and t.fecha between $fecMod and $fechaHoy group by t.estado order by estado";
//			echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td><td style='text-align:center;width:60px'>{$item[2]}</td></tr>";
				}
				$mensage .= "</table>";

				$mensage .= "<br />Estado de transacciones Ais<br /><table><tr><td>Estado</td><td>Cant</td><td style='text-align:center;width:60px'>%</td></tr>";
				$q = "select estado, count(idtransaccion), format((count(idtransaccion)/(select count(idtransaccion) from tbl_transacciones where idcomercio = '527341458854' and fecha between $fecMod and $fechaHoy)*100),2) from tbl_transacciones t where t.idcomercio in ('527341458854') and t.fecha between $fecMod and $fechaHoy group by t.estado order by estado";
//			echo $q;
				$temp->query($q);
				$arrR = $temp->loadRowList();
				foreach ($arrR as $item) {
					$mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td><td style='text-align:center;width:60px'>{$item[2]}</td></tr>";
				}
				$mensage .= "</table>";
			}
		}
//		echo $mensage; exit;
        
        $corCreo->set_subject("Informe estado $var del Concentrador $fe");
        $corCreo->set_message($mensage);
        
        $texto .= "\n<br>Informe envio - ".$corCreo->envia(1);
//        echo $texto;
		
		/**
		 * Comprobaciones diarias del trabajo
		 */
		//Porcientos de Aceptadas por pasarela - monedas no bajen del 20%
		$q = "select p.nombre, m.moneda, ("
				. "	select count(r.idtransaccion) from tbl_transacciones r where r.pasarela = t.pasarela and p.tipo = 'P' and r.moneda = t.moneda and r.estado = 'A' and r.fecha_mod > '$fechaAyer')/"
				. "count(idtransaccion)*100 "
			. "from tbl_transacciones t, tbl_pasarela p, tbl_moneda m "
			. "where t.moneda = m.idmoneda and t.pasarela = idPasarela and p.tipo = 'P' and t.tipoEntorno = 'P' and fecha_mod > '$fechaAyer' group by pasarela, t.moneda";
		$temp->query($q);
		if ($temp->getErrorMsg()) $texto .= "error: ".$q."\n<br>".$temp->getErrorMsg()."\n<br>";
		$arrR = $temp->loadRowList();
		$porcMin = 20;
		$sale = "";
		
		foreach ($arrR as $item) {
			if ($item[2] < $porcMin) {
				$sale .= "La combinación pasarela / moneda: ".$item[0]." / ".$item[1].", ha estado hoy al ".number_format($item[2],1)."% por debajo de los $porcMin% permitidos\n<br>";
			}
		}
		
		//El monto máx admitido para Sabadell al día debe ser de menos de 2500
		$arrMonto = array(
			array("Sabadell","29,31","2500")
		);
		
		foreach ($arrMonto as $pasarela) {
			$q = "select p.nombre, sum(case t.estado "
					. "when 'B' then if (fecha_mod < fechaPagada, (-1 * ((valor_Inicial-valor)/100/tasa)), (valor/100/tasa)) "
					. "when 'V' then if (fecha_mod < fechaPagada, (-1 * ((valor_Inicial-valor)/100/tasa)),  valor/100/tasa) "
					. "when 'A' then (valor/100/tasa) else '0.00' end)"
				. "FROM tbl_transacciones t, tbl_pasarela p "
				. "where t.pasarela = p.idPasarela and t.estado in ('A','V') and tipoEntorno = 'P' and fecha_mod > $fechaAyer and t.pasarela in (".$pasarela[1].")";
			$temp->query($q);
			if ($temp->getErrorMsg()) $texto .= "error: ".$q."\n<br>".$temp->getErrorMsg()."\n<br>";
			$arrR = $temp->loadRowList();
//			print_r($arrR);
//			echo $arrR[0][1]." > ".$pasarela[2];
			if ($arrR[0][1] > $pasarela[2]) $sale .= "La pasarela ".$arrR[0][0]." hoy a facturado ".number_format ($arrR[0][1],2)." EUR lo que se va por encima de los ".
					number_format ($pasarela[2],2)." EUR que debe facturar\n<br>";
		}
		
		//Avisar de alguna pasarela que no tenga operaciones
		$q = "select p.nombre, (select count(t.idtransaccion) from tbl_transacciones t where t.pasarela = p.idPasarela and t.estado = 'A' and t.fecha_mod > "
					. "$fechaHoy-86400) from tbl_pasarela p where p.activo = 1 and p.tipo = 'P'";
		$temp->query($q);

		if ($temp->getErrorMsg()) $texto .= "error: ".$q."\n<br>".$temp->getErrorMsg()."\n<br>";
		$arrR = $temp->loadRowList();
//			print_r($arrR);
		foreach ($arrR as $value) {
			if ($value[1] == 0) $sale .= "La pasarela ".$value[0]." no ha tenido hoy operaciones Aceptadas\n<br>";
		}
		
		if ($sale) {
//			$sale = "";
			$corCreo->todo(40,"Comprobaciones diarias", $sale);
			$texto .= $sale;
		}
		
		/* Fina de las comprobaciones diarias del trabajo */
        
        /**
		 * Revisa el estado diario de los ficheros
		 */
		//carpetas a revisar
		$sale = '';
		$arrCarp = array(
			"/",
			"/include/",
			"/amadeus/",
			"/rep/",
			"/admin/",
			"/admin/componente/",
			"/admin/componente/core/",
			"/admin/componente/ticket/",
			"/admin/componente/comercio/",
			"/admin/classes",
			"/admin/lang/",
			"/admin/template/",
		);
		$inic = str_replace("/cron.php", "", $_SERVER['SCRIPT_FILENAME']);

		$q = "select count(id) total from tbl_files";
		$temp->query($q);
		$texto .= $q."/n<br>";
		if ($temp->f('total') == 0) {
			$pase = 1;
		}

		$testo = '';$arrBrr = array();
		foreach ($arrCarp as $dir) {
			$dir = $inic.$dir;

			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if (end(explode('.', $dir.$file)) == 'php') {
							$cad = $dir.$file." - ".  sha1(sha1_file($dir.$file)."la ciudad esta llena de luces y yo estoy apagado que cojones sera");
							$arrCad = explode(' - ', $cad);
							$arrBrr[] = $arrCad[0];
							$arrStat = stat($dir.$file);

							if ($pase == 1) { //la tabla de los ficheros no tiene ningún dato, paso a llenarla
								$q = "insert into tbl_files (fichero, fecha) values ('".$arrCad[0]."', unix_timestamp())";
								$temp->query($q);
								$q = "insert into tbl_colFilesMod (idfile, size, mtime, md, fecha) values "
										. "(last_insert_id(), '".($arrStat['size']/1000)."', '".$arrStat['mtime']."', '".$arrCad[1]."', unix_timestamp())";
								$temp->query($q);
								if ($temp->getErrorMsg()) $texto .= $temp->getErrorMsg ();
								$paso = 1;
							} else { // la BD tiene datos paso a revisar los ficheros
								//revisar los ficheros modificados
								$q = "select m.idfile, m.md, m.size, from_unixtime(mtime,'%d/%m/%Y %H:%i:%s') mods from tbl_colFilesMod m "
										. "where m.idfile = (select id from tbl_files where fichero = '{$arrCad[0]}') order by fecha desc limit 0,1";
								$temp->query($q);
								$idR = $temp->f('idfile');
								$mdR = $temp->f('md');
								$sizR = $temp->f('size');
								$mtimeR = $temp->f('mods');
								$catR = $temp->num_rows();
								if ($catR == 1) {//el fichero existe en la BD, se comprueba el estado
									if ($mdR != $arrCad[1]){
										$testo .= "El fichero ".$arrCad[0]." ha sufrido cambios, su firma era\n$mdR y ahora es\n{$arrCad[1]}. La última vez que se modificó fué el ".$mtimeR
										. " con un tamaño de $sizR KB y ahora aparece modificado el ".date('d/m/Y H:i:s', $arrStat['mtime'])." con ".($arrStat['size']/1000)." KB.\n<br>";

										//se insertan los nuevos datos
										$q = "insert into tbl_colFilesMod (idfile, size, mtime, md, fecha) values "
												. "($idR, '".($arrStat['size']/1000)."', '".$arrStat['mtime']."', '".$arrCad[1]."', unix_timestamp())";
										$temp->query($q);
										if ($temp->getErrorMsg()) $texto .= $temp->getErrorMsg ();
									}
								} else {//El fichero se ha adicionado nuevo
									$testo .= "El fichero ".$arrCad[0]." no se encontraba en la BD, se ha adicionado nuevo. Sus datos son: md5 = ".$arrCad[1].
											", fecha de modificación = ".date('d/m/Y H:i:s', $arrStat['mtime'])." y tamaño de ".($arrStat['size']/1000)." KB\n<br>";

									//inserto en la BD los datos del "nuevo"
									$q = "insert into tbl_files (fichero, fecha) values ('".$arrCad[0]."', unix_timestamp())";
									$temp->query($q);
									$q = "insert into tbl_colFilesMod (idfile, size, mtime, md, fecha) values "
											. "(last_insert_id(), '".($arrStat['size']/1000)."', '".$arrStat['mtime']."', '".$arrCad[1]."', unix_timestamp())";
									$temp->query($q);
									if ($temp->getErrorMsg()) $texto .= $temp->getErrorMsg ();
								}
							}
						}
					}
					closedir($dh);
					if ($paso) {
						$testo = "La BD se ha llenado con todos los datos de ficheros .php";
					}
				}
			}
		}

		//revisa que no se haya borrado algún fichero
		$strBrr = implode("','", $arrBrr);
		$q = "select fichero, md, mtime, size, f.id from tbl_files f, tbl_colFilesMod m where f.id = idfile and fichero not in ('$strBrr')";
//			echo $q;
		$temp->query($q);
		if ($temp->getErrorMsg()) $texto .= $temp->getErrorMsg ();
		$arrFB = $temp->loadRowList();

// 		if (count($arrFB) > 0) {//hay ficheros borrados
// 			foreach ($arrFB as $fic) {
// 				$testo .= "El fichero ".$fic[0]." con fecha de modificación ".$fic[2]." y tamaño de {$fic[3]} KB fué borrado del directorio\n<br>";

// 				//se borran los datos en la BD de los ficheros borrados en directorio
// 				$q = "delete from tbl_ficheros where id = ".$fic[4];
// 				$temp->query($q);
// 				if ($temp->getErrorMsg()) $texto .= $temp->getErrorMsg ();
// 				$q = "delete from tbl_colFilesMod where idfile = ".$fic[4];
// 				$temp->query($q);
// 				if ($temp->getErrorMsg()) $texto .= $temp->getErrorMsg ();
// 			}
// 		}

		if ($testo) {
			$testo = "\n\nAlerta se han modificado ficheros!!\n".$testo."\n\n";
			$corCreo->todo(41,"Revisión de ficheros en disco", $testo);
			$texto .= $testo;
		}
		/* Fin de la revisión diaria del estado de los ficheros*/
		
		/*
		 * Calcula y Avisa de la realización de algún cierre que se necesite hacer
		 */
		$texto .= "\n<br>Haciendo los cierres\n<br>";
		$sale = '';
		$q = "select id, idcomercio, nombre, cierrePer, maxCierre from tbl_comercio where activo = 'S'";
		$temp->query($q);
		$texto .= "$q\n<br>";
// 		echo "$q\n<br>";
		$arrCom = $temp->loadRowList();
		
		foreach ($arrCom as $com) {
			$men = '';
			$q = "select idcierre, r.idtransaccion, r.fechaCierre "
					. " from tbl_cierreTransac r, tbl_transacciones t "
					. " where t.idtransaccion = r.idtransaccion and r.idcomercio = '{$com[0]}' order by t.fecha desc limit 0,1";
			$texto .= "$com[2]\n<br>";
// echo "$q\n<br>";
			$texto .= "$q\n<br>";
			$temp->query($q);
			if ($temp->getErrorMsg()) $texto .= "\n<br>$q \n<br>Error: ".$temp->getErrorMsg();
			$arrSal = $temp->loadRowList();
//			print_r($arrSal);
			
			foreach ($arrSal as $item) {
				$menNimp = ''; //mensaje no inmediato
				$menImp = ''; //mensaje inmediato
				$ffeUn = $item[2];
				$dias = floor((time()-$ffeUn)/(60*60*24));
// 				$texto .= $com[2]." - ".$com[3]." - ".$dias."\n<br>";
				
				/*Comienza variación*/
				if($com[3] == 'M') { //realizar el cierre al comienzo de mes
					if (date('j') >= 1 && date('j') < 5 && $dias > date('j')) {
						$q = "select count(idtransaccion) cant from tbl_transacciones "
								. " where estado = 'A' "
									. " and tipoEntorno = 'P' "
									. " and idcomercio = '".$com[1]."'"
									. " and fecha between $ffeUn and ".time();
					}
				} elseif($com[3] == 'Q') { //realizar el cierre cada 15 días
					if ($dias > 14) {
						$q = "select count(idtransaccion) cant from tbl_transacciones "
								. " where estado = 'A' "
									. " and tipoEntorno = 'P' "
									. " and idcomercio = '".$com[1]."'"
									. " and fecha > ".(time()-15*24*60*60);
					}
				} elseif($com[3] == 'S') { //realizar el cierre a la semana del anterior
					if ( $dias > 6) {
						$q = "select count(idtransaccion) cant from tbl_transacciones "
								. " where estado = 'A' "
								. " and tipoEntorno = 'P' "
										. " and idcomercio = '".$com[1]."'"
												. " and fecha > ".(time()-7*24*60*60);
					}
				} elseif($com[3] == 'D') { //realizar los cierres diarios
						$q = "select count(idtransaccion) cant from tbl_transacciones "
								. " where estado = 'A' "
								. " and tipoEntorno = 'P' "
										. " and idcomercio = '".$com[1]."'"
												. " and fecha > ".(time()-24*60*60);
				} 
// 				$texto .= $com[2]." - dias=".$com[3]." - hasta hoy=".$dias."<br>\n";
				
				/*Termina variación*/
/*				
				if($com[3] == 'M') $dcon = 30;
				elseif($com[3] == 'D') $dcon = 1;
				elseif($com[3] == 'Q') $dcon = 15;
				elseif($com[3] == 'S') $dcon = 7;
				else $dcon = 100000000;

				$dias = floor((time()-$ffeUn)/(60*60*24));

				$q = "select count(idtransaccion) cant from tbl_transacciones where fecha between (select unix_timestamp(from_unixtime(t.fecha,'%Y-%m-%d')) "
						. " from tbl_transacciones t "
						. "where t.idtransaccion = '".$item[1]."')+24*60*60 and ".time()." and estado = 'A' and tipoEntorno = 'P' and idcomercio = '".$com[1]."'";
*/
//				$texto .= "$q\n<br>";
				$temp->query($q);
				$cant = $temp->f('cant');
				if ($temp->getErrorMsg()) $texto .= "\n<br>$q \n<br>Error: ".$temp->getErrorMsg();

				if ($cant > 0) {
					$texto .= "tiene $cant de transacciones Aceptadas\n<br>";
					//llegó al número de días o está pasado
					$menImp .= "El comercio <strong>".$com[2]."</strong> ha llegado al límite por tiempo, $com[3], necesita se"
														." le realice el Cierre de inmediato.\n<br>";
					//llegegará en las próximas 24 hr al número de días.
// 					elseif ($dcon <= $dias+1) $menNimp .= "El comercio <strong>".$com[2]."<strong/> llegará en las próximas 24 hrs al límite máximo "
// 															." por días para que se le realice el Cierre.\n<br>";
//					$texto .= $menImp;
				}

				$q = "select sum(euroEquiv) suma from tbl_transacciones where estado = 'A' and idcomercio = '".$com[1]."' and fecha > $ffeUn";
// if ($com[0] == 23) echo "$q\n<br>";
				$temp->query($q);
				$valeT = $temp->f('suma');
				$texto .= $q."<br>\n";
				$texto .= $com[2]." - valor=".$com[4]." - tiene en realidad=".$valeT."<br>\n";

					//llegó al monto límite o está pasado
				if ($valeT >= $com[4]) $menImp .= "El comercio <strong>".$com[2]."</strong> tiene acumulado hasta este momento $valeT Euros, "
							. "lo que hace que haya llegado al límite por monto, necesita se le realice el Cierre de inmediato.\n<br>";
					//llegará en las próximas 24 hr .
				elseif ($valeT >= $com[4]+($valeT/$dias)) $menNimp .= "El comercio <strong>".$com[2]."</strong> tiene acumulado hasta este momento "
							." $valeT Euros, llegará en las próximas hrs al límite máximo por monto para que se le realice el Cierre.\n<br>";

				if(strlen($menImp) > 0) $men .= $menImp."\n<br>";
				elseif(strlen($menNimp) > 0)  $men .= $menNimp."\n<br>";
//		if (strlen($men) == 0) $men .= "No hay ningún cierre pendiente";
				if (strlen($men) > 0) $sale .= "$men\n<br>";
			}
		
			/*Aviso de transacción reclamada en tres meses*/
			$q = "select idtransaccion from tbl_transacciones where estado = 'R' "
							. " and (fecha_mod >= unix_timestamp('2015-02-01 00:00:00') and fecha_mod >= ".(time()-90*24*60*60).")"
							. " and idcomercio = {$com[1]}";
// 			echo $q;
			$temp->query($q);
			$arrRecl = $temp->loadResultArray();
// 			print_r($arrRecl);
			if (count($arrRecl) > 2) {
				//se han producido mas de tres reclamaciones en menos de 3 meses
				$corCreo->todo(45,"Aviso de 3 reclamaciones en menos de 3 meses", "El comercio {$com[2]} tiene 3 o más reclamaciones en el período de "
						."3 meses. Las operaciones son ".implode(", ", $$arrRecl));
			}
			
		}
		if (strlen($sale) == 0) $sale .= "No hay ningún cierre pendiente";
		$corCreo->todo(37,"Avisos de Cierres", $sale);
		
		echo $texto;
		actSetup(($fechaHoy + 60*60*24), 'fechaInf');
}
//echo $sale.$texto;
//echo leeSetup('fechaTasa')." <= ".$horaTasa;
/**
 * Busca y pone la tasa de cambio del día
 */
$texto .= "\n<br>$horaTasa <= ".time()."\n<br>";
if ($horaTasa <= time()
//        || 1==1
        ) {
	//Revisa que no se haya puesto la hora ya 
 	if (leeSetup('fechaTasa') <= $horaTasa) {
//		echo "entra";
		$texto .= "\n<br>Busca y pone la tasa de cambio del día\n<br>";
		//Pone la tasa del día
		$error = array();
		actSetup('0', 'envioSMScamb'); //actualiza la tabla setup caso que haya que enviarme sms
		$q = "select moneda from tbl_moneda where idmoneda != 978";
        $texto .= $q."<br>";
		$temp->query($q);
		$den = $temp->loadResultArray();
		$tasaInc = leeSetup('incBCE'); //incremento sobre la tasa de cambio
		
		/**
		* Pone el cambio de moneda según Visa
		*/
		$texto .= "\n<br>Pone el cambio de moneda según Visa<br>";
		foreach($den as $item) {
			$q = "select count(*) total from tbl_cambio where fecha >= $horaTasa and moneda = '$item'";
            $texto .= $q."<br>";
			$temp->query($q);
			if ($temp->f('total') == 0) {

				$rat = rates($item);
				if (!$rat > 0) {
					$rat = 0;
					$error[] = "Error cambio Visa\n<br>";
				} else $rat = $rat+$tasaInc;

				$q = "insert into tbl_cambio (visa, moneda, fecha) values ($rat, '$item', $horaTasa)";
                $texto .= $q."<br>";
				$temp->query($q);
			}
		}
		
		/**
		* Pone el cambio de moneda según Xe.com
		*/
		$texto .= "\n<br>Pone el cambio de moneda según Xe.com<br>";
		$q = "select count(*) total from tbl_cambio where from_unixtime(fecha,'%d/%m%/%Y') = from_unixtime($horaTasa,'%d/%m%/%Y') ".
				" and moneda = 'USD' and (xe is null or xe = 0)";
        $texto .= $q."<br>";
		$temp->query($q);
		
		if($temp->f('total') == 1) {
			foreach($den as $item) {
				$rat = ratesXe($item);
				if (!$rat > 0) {
					$rat = 0;
					$error[] = "Error cambio Xe\n<br>";
				} else $rat = $rat+$tasaInc;

				$q = "update tbl_cambio set xe = $rat where moneda = '$item' and from_unixtime(fecha,'%d/%m%/%Y') = from_unixtime($horaTasa,'%d/%m%/%Y')";
                $texto .= $q."<br>";
				$temp->query($q);
			}
		}

		/**
		* Busca las tasas de cambio de las monedas CUC, USD, GBP y CAD
		*/
		$texto .= "\n<br>Busca las tasas de cambio de las monedas CUC, USD, GBP, CAD y JPY<br>";
		$q = "select count(*) total from tbl_cambio where from_unixtime(fecha,'%d/%m%/%Y') = from_unixtime($horaTasa,'%d/%m%/%Y')".
				" and moneda = 'USD' and (bce is null or bce = 0)";
        $texto .= $q."<br>";
		$temp->query($q);

		if ($temp->f('total') > 0) {
			$XMLContent= file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
			$cambio = 0;

			$q = "select count(*) total from tbl_cambio where from_unixtime(fecha,'%d/%m%/%Y') = from_unixtime($fechaHoy,'%d/%m%/%Y') and moneda = 'USD'";
            $texto .= $q."<br>";
			$temp->query($q);
			$temp->f('total') > 0 ? $pase = true : $pase = false;

			if ($XMLContent) {
				foreach ($XMLContent as $line) {
						if (ereg("currency='([[:alpha:]]+)'",$line,$currencyCode)) {
							if (ereg("rate='([[:graph:]]+)'",$line,$rate)) {
									//Output the value of 1 EUR for a currency code
									if (in_array($currencyCode[1], $den)) {
										if ($rate[1] != '' && $rate[1] != 0) {
											$cambio = $rate[1] + leeSetup('incBCE');
											if ($pase) {
												$q = "update tbl_cambio set bce = $cambio where moneda = '{$currencyCode[1]}'".
														" and from_unixtime(fecha,'%d/%m%/%Y') = from_unixtime($fechaHoy,'%d/%m%/%Y')";
                                                $texto .= $q."<br>";
												$temp->query( $q );
											}
										}
									}
							}
						}
				}
			} else {
                $texto .= "Error cambio BCE<br>";
				$error[] = "Error cambio BCE\n<br>";
			}
		}

		/**
		* Banco central de cuba 
		*/
		$texto .= "\n<br>Banco central de cuba<br>";
		include 'admin/classes/banco.php';
		$banc = new banco();
		
		if (!$banc->cambio("EUR") > 0 || is_null($banc->cambio("EUR")) ) {
			$cuc = 0;
			$error[] = "Error cambio BNC\n<br>";
		} else 
			$cuc = $banc->cambio("EUR");

		$q = "select count(*) total from tbl_cambio where from_unixtime(fecha,'%d/%m%/%Y') = from_unixtime($horaTasa,'%d/%m%/%Y') and moneda = 'CUC'";
        $texto .= $q."<br>";
		$temp->query($q);
		if ($temp->f('total') == 0) {
			$q = "insert into tbl_cambio (bnc, moneda, fecha) values ($cuc, 'CUC', $horaTasa)";
            $texto .= $q."<br>";
			$temp->query($q);
		}

		/**
		* actualiza la tabla setup con el banco solicitado o con el mayor valor 
		*/
		$texto .= "\n<br>actualiza la tabla setup con el banco solicitado o con el mayor valor <br>";
		$banco = leeSetup('bancoUsado');
		foreach ($den as $mon) {
			if (strlen($banco) == 0) {
				$q = "select visa, bce, xe from tbl_cambio where moneda = '$mon' order by fecha desc limit 0,1";
                $texto .= $q."<br>";
				$temp->query($q);
				$arrVal = $temp->loadRow();
				rsort($arrVal,SORT_NUMERIC);
				$camb = $arrVal[0];
			} else {
				$q = "select $banco as cam from tbl_cambio where moneda = '$mon' order by fecha desc limit 0,1";
                $texto .= $q."<br>";
				$temp->query($q);
				$camb = $temp->f('cam');
			}
            $texto .= "\n<br>Actualiza Setup con la tasa $camb para la moneda $mon<br>";
			actSetup($camb, $mon);
		}
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
//            $q = "insert into tbl_traza (titulo,traza,fecha) values ('Problemas al obtener las tasas de cambio','".html_entity_decode($texto)."',".time().")";
//            $temp->query($q);
            
			actSetup('1', 'envioSMScamb');
			#Verificar Resultado
			if ($dms->autentificacion->error){
				#Error de autentificacion con la plataforma
				$saliMensaje .= $dms->autentificacion->mensajeerror;

			}
		}
 	}
}

/****************************************Comienzo de chequeo de operaciones en monedas distintas a EUR, USD, CAD para Cubana*************************************************/

/* $q = "select t.identificador, a.sesion, m.denominacion from tbl_transacciones t, tbl_amadeus a, tbl_moneda m "
		. " where t.idcomercio = '129025985109' and t.moneda not in (978, 840, 124) and t.fecha between ".(time()-(60*60))." and ".time()." "
		. " and t.identificador = a.rl and t.idcomercio = a.idcomercio and t.moneda = m.idmoneda";
$temp->query($q);
$arrVals = $temp->loadAssocList();
$texto .= $q."\n<br>";
if ($temp->getErrorMsg()) $texto .= "\n<br>".$q."\n<br>".$temp->getErrorMsg()."\n<br>";

for ($i=0; $i<count($arrVals); $i++) {
	$texto .= "\n<br>Se envía aviso de operación en moneda no reconocida\n<br>";
	$texto .= "Moneda ".$arrVals[$i]['denominacion']."\n<br>";
	$texto .= "RL = ".$arrVals[$i]['identificador']."\n<br>";
	
	$messs = "Hola Alejandro\n<br>\n<br>"
			. "Se ha producido una operación en ".$arrVals[$i]['denominacion'].", la misma tiene como PNR - ".$arrVals[$i]['identificador'].",\n<br>"
			. "ha llegado desde Amadeus con la sesión: ".$arrVals[$i]['sesion']."\n<br>\n<br>Saludos\n<br>\n<br>Administrador de Comercios\n<br>AMFGlobalitems" ;
	
	$corCreo->to('Alejandro <alejandro.garcia@cubana.avianet.cu>');
	$corCreo->todo(38,'Operación en moneda desconocida', $messs);
} */

/******************************************Fin de chequeo de operaciones en monedas distintas a EUR, USD, CAD para Cubana************************************************/



/***************************************************Comienzo Trabajo de las pasarelas para AIS*****************************************************************/

//if (time() >= mktime(23, 55, 0, 6, 1, 14)) { //deshabila Ais el domingo en la noche
//	$texto .= "Deshabilitado AIS por completo...\n<br>";
//	$q = "update tbl_comercio set activo = 'N' where id = 39";
//	$temp->query($q);
//}

//if (time() >= mktime(18, 55, 0, 6, 12, 14)) {
//	$q = "update tbl_comercio set pasarela = '32', pasarelaAlMom = '31, 32, 36', activo = 'S' where id = 39";//31
//	$temp->query($q);
//}

$q ="select pasarela from tbl_comercio where id = 24";
$temp->query($q);
$pasr = $temp->f('pasarela');
$texto .= $pasr. " - ".$q. "\n<br>";

/* Cambia Cubana de pasarelas */
if (time() >= leeSetup('horaAis')) {
	$texto .= "Cambiando Cubana entre pasarelas\n<br>";
	switch ($pasr) {
		case '41': // si la pasarela es Bankia5 3D la paso a Sabadell 3D
			$pasr = '31';
			$pas = 2;
			$texto .= "Cambiando Cubana a Sabadell2 3D durante $pas horas\n<br>";
		break;
		case '31': // si la pasarela es Sabadell2 3D la paso a Bankia4 3D
			$pasr = '32';
			$pas = 2;
			$texto .= "Cambiando Cubana a Bankia4 3D durante $pas hora\n<br>";
		break;
		case '32': // si la pasarela es Bankia4 3D la paso a CaixaBnk2 3D
			$pasr = '38';
			$pas = 3;
			$texto .= "Cambiando Cubana a CaixaBnk2 3D durante $pas hora\n<br>";
		break;
		case '38': // si la pasarela es CaixaBnk2 3D la paso a Bankia5 3D
			$pasr = '41';
			$pas = 1;
			$texto .= "Cambiando Cubana a Bankia5 3D durante $pas hora\n<br>";
		break;
		default:
			$pasr = '41';
			$pas = 1;
			$texto .= "Cambiando Cubana a Bankia5 3D durante $pas hora\n<br>";
			
	}
	$q = "update tbl_comercio set pasarela = '$pasr' where id = 24";
	$texto .= "\n<br>".$q;
	$temp->query($q);
	actSetup(time()-(400)+(60*60*$pas), 'horaAis');//le quito 400 segundos para meterlo en la hora anterior
}


/* Cambia AIS de pasarela */
//$texto .= "Cambio pasarela: ".time()." >= ".leeSetup('horaAis')." && ".time()." < ".mktime(23, 55, 0, 6, 1, 14)."\n<br>";
//if (time() >= leeSetup('horaAis')) {
//	if (time() > mktime(0, 0, 1, date("m"), date("d"), date("Y")) && time() < mktime(9, 59, 59, date("m"), date("d"), date("Y"))) { // va cambiando AIS de pasarela entre las 12 pm a las 10 am
//		$texto .= "Cambiando AIS entre 3 pasarelas\n<br>";
//		if ($pasr == '12') { // si la pasarela es Evo 3D lo paso a Caixabank 3D y le pongo dos horas
//			$pasr = '38';
//			$pas = 1;
//			$texto .= "Cambiando AIS a Caixabank 3D durante 2 horas\n<br>";
//		} else if ($pasr == '38') {// si la pasarela es Caixabank 3D lo paso a Bankia4 3D y le pongo dos horas
//			$pasr = '32';
//			$pas = 2;
//			$texto .= "Cambiando AIS a Bankia4 3D durante 2 horas\n<br>";
//		} else if ($pasr == '32') {// si la pasarela es Bankia4 3D lo paso a Caixa2 3D y le pongo una hora
//			$pasr = '36';
//			$pas = 2;
//			$texto .= "Cambiando AIS a Caixa2 3D durante 1 hora\n<br>";
//		} else if ($pasr == '36') {// si la pasarela es Caixa2 3D lo paso a Evo 3D y le pongo una hora
//			$pasr = '12';
//			$pas = 2;
//			$texto .= "Cambiando AIS a Evo 3D durante 1 hora\n<br>";
//		}
//	} else { //para 14hrs
//		$texto .= "Cambiando AIS entre 2 pasarelas\n<br>";
//		if ($pasr == '12') { // si la pasarela es Evo 3D lo paso a Caixabank 3D y le pongo dos horas
//			$pasr = '38';
//			$pas = 1;
//			$texto .= "Cambiando AIS a Caixabank 3D durante 2 horas\n<br>";
//		} else if ($pasr == '38') {// si la pasarela es Caixabank 3D lo paso a Bankia4 3D y le pongo dos horas
//			$pasr = '32';
//			$pas = 1;
//			$texto .= "Cambiando AIS a Bankia4 3D durante 2 horas\n<br>";
//		} else if ($pasr == '32') {// si la pasarela es Bankia4 3D lo paso a Caixa2 3D y le pongo una hora
//			$pasr = '36';
//			$pas = 1;
//			$texto .= "Cambiando AIS a Caixa2 3D durante 1 hora\n<br>";
//		} else if ($pasr == '36') {// si la pasarela es Caixa2 3D lo paso a Evo 3D y le pongo una hora
//			$pasr = '12';
//			$pas = 1;
//			$texto .= "Cambiando AIS a Evo 3D durante 1 hora\n<br>";
//		}
//	}
//
//	$q = "update tbl_comercio set pasarela = '$pasr' where id = 39";
//	$texto .= "\n<br>".$q;
//	$temp->query($q);
//
//	actSetup(time()-(400)+(60*60*$pas), 'horaAis');//le quito 400 segundos para meterlo en la hora anterior
//} 
//
//
//else if (time() >= leeSetup('horaAis') && time() < mktime(23, 55, 0, 6, 1, 14)) {// va cambiando AIS y Cubana de pasarela hasta las 12 pm del 1/6
//	$texto .= "va cambiando AIS y Cubana de pasarela hasta las 12 pm del 1/6\n<br>";
//	$q ="select pasarela from tbl_comercio where id = 39";
//	$temp->query($q);
//	if ($temp->f('pasarela') == '31') {//si esta puesta la pasarela de sabadell en ais pasa a santander
//		$pasr1 = '13';
//		$pasr2 = '31';
//		$pas = 1;
//	} else { // si esta santander pasa ais a sabadell
//		$pasr1 = '31';
//		$pasr2 = '13';
//		$pas = rand(1, 5);
//	}
//	
//	$q = "update tbl_comercio set pasarela = '$pasr1' where id = 39";
//	$texto .= "\n<br>".$q;
//	$temp->query($q);
//	$q = "update tbl_comercio set pasarela = '$pasr2' where id = 24";
//	$texto .= "\n<br>".$q;
//	$temp->query($q);
//	
//	actSetup(time()-(400)+(60*60*$pas), 'horaAis');//le quito 400 segundos para meterlo en la hora anterior
//	
//} else if (time() >= leeSetup('horaAis') && time() >= mktime(23, 55, 0, 6, 1, 14)) {// va cambiando despues del domingo 12 pm, a Cubana solamente
//	$texto .= "va cambiando despues del domingo 12 pm a Cubana solamente\n<br>";
//	$q ="select pasarela from tbl_comercio where id = 24";
//	$temp->query($q);
//	if ($temp->f('pasarela') == '31') {
//		$pasr1 = '13';
//		$pas = 1;
//	}
//	else {
//		$pasr1 = '13';
//		$pas = rand(1, 5);
//	}
//	
//	$q = "update tbl_comercio set pasarela = '$pasr1' where id = 24";
//	$texto .= "\n<br>".$q;
//	$temp->query($q);
//	
//	actSetup(time()-(400)+(60*60*$pas), 'horaAis');//le quito 400 segundos para meterlo en la hora anterior
//	
//}

//if ($ahora >= mktime(4, 0, 0, date("m"), date("d"), date("Y")) && $ahora < mktime(14, 0, 0, date("m"), date("d"), date("Y"))) {
//	$texto .= "\n<br>Cambia AIS para la pasarela Santander";
//	$q = "update tbl_comercio set pasarela = '13' where id = 39 and pasarela = '26'";
//	$texto .= "\n<br>".$q;
//	$temp->query($q);
//}

/*Cambia AIS para la pasarela BBVA*/
//if ($ahora >= mktime(14, 0, 0, date("m"), date("d"), date("Y"))) {
//	$texto .= "\n<br>Cambia AIS para la pasarela BBVA";
//	$q = "update tbl_comercio set pasarela = '26' where id = 39 and pasarela = '13'";
//	$texto .= "\n<br>".$q;
//	$temp->query($q);
//}
/****************************************************Fin de Trabajo de las pasarelas para AIS*****************************************************************/



if (($ayer + 86400) < $ahora
//        || 1==1
        ) {
// $temp->query("Paso4");
	/**
	* Borra los datos de la tabla bitacora y traza que son $mesesBitacora de viejos
	*/
// 	$texto .= "\n<br>Borra los datos de la tabla bitacora y traza que son $mesesBitacora de viejos";
// 	$query = "delete from tbl_baticora where fecha < ".mktime(0, 0, 0, date("m")-$mesesBitacora, date("d"), date("Y"));
// 	$temp->query( $query );
// $temp->query("Paso5");
// 	$query = "delete from tbl_traza where fecha < '".date("m")-$mesesBitacora."/".date("d")."/". date("Y")."'";
// 	$temp->query( $query );
// $temp->query("Paso6");

	/**
	 * Cambia la contraseña de acceso 
	 */
	$texto .= "\n<br>Cambia la contraseña de acceso";
	// Calcula la fecha para el cambio en dependencia de los meses para el cambio de la contrase?a
	$fechaCamb = mktime(0, 0, 0, date("m"), date("d")-leeSetup('mesesContrs'), date("Y"));
	//lee la fecha de los cambios de contrase?a para ver si lleg? al vencimiento
	$query = "select idadmin, login, nombre, email, md5, md5Old from tbl_admin where activo = 'S' and fechaPass < $fechaCamb";
	$temp->query( $query );
	
	while ($temp->next_record()) {

//		actualiza las contraseñas en la BD
		$nombre =	$temp->f('nombre');
		$id =		$temp->f('idadmin');
		$correo =	$temp->f('email');
		$login =	$temp->f('login');
		$contras =	validaContrasena($login);
//echo $contras[0];
		$query = "update tbl_admin set md5Old = md5, fechaPass = $fechaHoy, md5 = '".$contras[1]."' where idadmin = $id";
		if (!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/')) $ini->query( $query );
//		echo "$query<br>";

		$imprim = '<div style=\"text-align:center; font-family:Arial san-serif; font-size:11px\">Estimado Cliente,<br><br>
					Por motivos de seguridad su contraseña de acceso al Panel de Administración del Concentrador de Transacciones de Travels and Discovery<br>
					ha sido cambiada. Usted puede cambiar la misma antes de que vuelva a caducar automáticamente cada '.leeSetup('mesesContrs').' días.<br><br>
					Los nuevos datos de acceso son:<br>
					Usuario: '.$login.'<br>
					Contraseña: '.$contras[0].'<br><br>
					Disculpe las molestias y muchas gracias por preferirnos.<br><br>
					Administración de Sistemas<br>
					Travels and Discovery.';
//echo "$imprim<br>";exit;
        
		$corCreo->to = $nombre.' <'.$correo.'>';
        $corCreo->todo(6,'Cambio de acceso a la Administración '.$GLOBALS['titulo_sitio'], $imprim);
        $texto .= "\n<br>Envía contraseña a $to";
        
//		if (!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/')) mail($to, $subject, $imprim, $headers);
	}
	/**
	 * Desactiva los usuarios que no entran por primera vez al cabo de diasEntrada de creado
	 */
	$query = "select idadmin from tbl_admin where activo = 'S' and fecha_visita = 0 and fecha < ".($fechaHoy - leeSetup('diasEntrada') * 86400);
//	echo $query;
	$temp->query( $query );
	$texto .= "\n<br>Desactiva los usuarios que no entran por primera vez al cabo de ".leeSetup('diasEntrada')." de creado";

	while ($temp->next_record()) {
		$query = "update tbl_admin set activo = 'N' where idadmin = ".$temp->f('idadmin');
		$ini->query( $query );
	}
	
	/**
	 * Desactiva los usuarios que no entran desde hace mas de 2 meses
	 */
	$q = "update tbl_admin set activo = 'N' fecha_visita < ($fechaHoy-60*60*24*60) and activo = 'S'";
	$temp->query($q);
	
	/**
	*Da por terminado los tickets de mas de tres d?as sin  modificar
	*/	
	$query = "update tbl_ticket set fechaTerminada = ".$ahora.", estado = 'T' where fechaModificada < ".($ahora - leeSetup('venceTicket') * 86400);
	$temp->query($query);

	/**
	 * Borra lo que est? en la cesta que tenga + de venceInvitacionPago
	 */
	$query = "select id, idProd, cant, fechaIni, fechaFin from tbl_productosReserv where fecha < ".($ahora - leeSetup('venceInvitacionPago') * 86400);
	$ini->query($query);
//	echo $query."<br>";

	while ($ini->next_record()) {
		$prod = $ini->f('idProd');
		$fecha2 = $ini->f('fechaFin');
		$fecha = $fecha1 = $ini->f('fechaIni');
		$cant = $ini->f('cant');
		$id = $ini->f('id');
		$cantCheq = $idCant = 0;
		$paso = false;
		while ($fecha <= $fecha2) {
			$query = "select id, cant from tbl_productosCant where idProd = ".$prod." and fechaIni <= $fecha and fechaFin >= $fecha";
//				echo $query."<br>";
			$temp->query($query);
			$cantObt = $temp->f('cant');
			$idObt = $temp->f('id');
			if ($cantCheq == 0) $cantCheq = $cantObt;
			if ($cantCheq != $cantObt) {
//echo "$prod, $fecha1, ($fecha2-86400), ($cantCheq-$cant)<br>";
				if (insertaCantidad($prod,$fecha1,$fecha-86400,$cantCheq+$cant)) $paso = true;
				$fecha1 = $fecha;
				$cantCheq = $cantObt;
			}
			$fecha += 86400;
		}
		if (insertaCantidad($prod,$fecha1,$fecha-86400,$cantCheq+$cant)) $paso = true;
//echo "segundo: $prod, $fecha1, ".($fecha2-86400).", ".($cantCheq-$cant)."<br>";
		if ($paso) {
			$query = "delete from tbl_productosReserv where id = $id";
			$temp->query($query);
//			echo $query."<br>";
		}
	}

	/**
	 * Env?a correo a los administradores de los comercios avis?ndole que tienen transacciones a punto de vencer (12 horas del plazo)
	 * por si desean reenviarlas 
	 */
	$query = "select count(id_reserva) total, r.id_admin, (select nombre from tbl_admin where idcomercio = r.id_comercio limit 0,1)  nombre,
				(select email from tbl_admin where idcomercio = r.id_comercio limit 0,1)  correo
				from tbl_reserva r
				where r.fecha < (unix_timestamp() - tiempoV * 86400)".
				"	and r.estado = 'P'
				group by r.id_comercio";
//	echo "query= ".$query."<br>";
	$ini->query($query);
	
	for ($i=0; $i < $ini->num_rows(); $i++) {
		//global $send_m;

		$arrayTo[] = array($ini->f('nombre'), $ini->f('email'));
//if (_MOS_CONFIG_DEBUG) {print_r($arrayTo); echo "<br>";}
		
		$message = "<div style=\"text-align:center; font-family:Arial san-serif; font-size:11px\">Estimado Usuario,<br><br>
					Su comercio ha enviado ".$ini->f('total')." invitaciones de pago que han vencido hoy<br>
					Evalúe la posibilidad de entrar al Concentrador y volverlas a enviar.<br><br>
					Disculpe las molestias y muchas gracias por preferirnos.<br><br>
					Administración de Sistemas<br>
					Travels and Discovery.";        
        
        $corCreo->set_subject('Aviso de vencimiento de Invitaciones de Pago');
        $corCreo->set_message($message);

		foreach ($arrayTo as $todale) {
            if (!strlen($corCreo->to))
                $corCreo->to = $todale[0]."<".$todale[1].">";
            else 
                $corCreo->set_headers ("Cc: ". $todale[0]."<".$todale[1].">");
		}
		$ini->next_record();
        $corCreo->envia(7);
	}
	
	/**
	 * Borra las invitaciones para el pago que tengan + de venceInvitacionPago
	 */
	$query = "delete from tbl_reserva where fecha < (unix_timestamp() - tiempoV * 86400) and estado = 'P'";
	$ini->query($query);
	//echo $query."<br>";


	/**
	 * Escribe la fecha de hoy en la tabla setup fechaMod
	 */
	actSetup($fechaHoy, 'fechaMod');

}

/**
 * Listado de IPs con más de 15 accesos al Concentrador sin envío de datos en el mes anterior
 */
$mes = explode('/',leeSetup('fechaMes'));
$mes = $mes[1];
if (($mes * 1) < (date('m') * 1)) {
	
	$q = "select ip, count(*) as veces from tbl_listaIp where fecha >= ".mktime(0, 0, 0, date("m")-1, 1, date("Y"))." group by ip order by veces desc";
	$temp->query($q);
	$listado = '';
    $cant = $temp->num_rows();
	
	for ($i=0; $i < $temp->num_rows(); $i++) {
		if ($temp->f('veces') > 15) {
			if (strlen($listado) > 0) $listado .= ', ';
			$listado .= $temp->f("ip");
		} else break;
		$temp->next_record();
	}
	
    if ($cant > 0) {
        $imprim = 'Este es el listado de las IPs con más de 15 accesos al Concentrador sin envío de datos en el mes anterior:'."\n<br>\n<br>".$listado
                ."\n<br>\n<br>Nota: Eliminar las IPs de Cuba para no cerrarnos nosotros mismos\n<br>";
    //echo "$imprim<br>";

        $corCreo->set_subject('Listado IPs con mas de 15 accesos al concentrador');
        $corCreo->set_message($imprim);
        $corCreo->envia(8);
        $texto .= "\n$subject";
    }
	actSetup("1/".date('m')."/".date('Y'), 'fechaMes');
}

function rates ($denom) {
//	include_once 'admin/classes/class.Html.php';
//	$resultad = '';
//	
//	$url = 'http://corporate.visa.com/pd/consumer_services/consumer_ex_results.jsp?actualDate='.date('m/d/Y').'&homCur='.$denom.'&forCur=EUR&fee=0';
////	$url = 'localhost/'.$denom.'.html';
//
//	$objHtmlParser = new Html($url);
//
//	$description = '';
//	$objHtmlParser->Clean();
//	
//	$objHtmlParser->Parse($description);
//	
//	$all_tags = array();
//	$objHtmlParser->FindAllTags($objHtmlParser->tree,$all_tags);
//	if (count($all_tags) > 1) {
//		$resultad = $all_tags['p'][5]['text'];
//		if (strlen($resultad) > 6) {
//			$resultad = str_replace('1 EUR = ', '', $resultad);
//			$resultad = str_replace(' '.$denom, '', $resultad);
//		}
//	}
    $url = "http://download.finance.yahoo.com/d/quotes.csv?s=EUR$denom=X&f=sl1d1t1ba&e=.csv";
    echo "!!!!!!!!!!!!!!!!!!!!!!!!!!$url";
    $chx = curl_init($url);
    curl_setopt($chx, CURLOPT_POST, false);
    curl_setopt($chx, CURLOPT_HEADER, false);
    curl_setopt($chx, CURLOPT_RETURNTRANSFER, true);
    $sale = curl_exec($chx);
    curl_close($chx);
    $arrEx = explode(',', $sale);
    return $arrEx[1];
//	return $resultad;
}

function ratesXe ($denom) {
	include_once 'admin/classes/class.Html.php';
	$resultad = '';
	
	$url = "http://www.xe.com/ucc/convert.cgi?template=pca-new&Amount=1&From=EUR&To=$denom&image.x=36&image.y=11&image=Submit";
//	$url = 'http://localhost/XE.com%20-%20Personal%20Currency%20Assistant'.$denom.'.htm';

	$objHtmlParser = new Html($url);

	$description = '';
	$objHtmlParser->Clean();
	
	$objHtmlParser->Parse($description);
	
	$all_tags = array();
	$objHtmlParser->FindAllTags($objHtmlParser->tree,$all_tags);
	if (count($all_tags) > 1) {
		$resultad = $all_tags['span'][3]['text'];
		if (strlen($resultad) > 6) {
			$resultad = str_replace('1 EUR = ', '', $resultad);
			$resultad = str_replace(' '.$denom, '', $resultad);
		}
	}
	return $resultad;
}

$corCreo->set_message($texto."\n<br>Ejecutado satisfactoriamente a las ".date('d/m/Y H:i'));
$corCreo->set_subject("Ejecución del Cron");
$corCreo->envia(5);
echo "ok";
?>