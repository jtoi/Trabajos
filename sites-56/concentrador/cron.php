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

$texto = date('d/m/Y H:i:s').'Script lanzado desde '.$_SERVER['SCRIPT_FILENAME']."\n<br>";
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

//borra los ficheros de los Clientes con más de 60 días de creados
$texto .= "Borrando Directorios de Clientes sin actualizar<br>";
$temp->query("select count(id) total from tbl_aisCliente where fecha < (unix_timestamp()-60*60*24*60) and borrficheros = 0");
$texto .= "Clientes que faltan por borrar sus ficheros: ".$temp->f('total')."<br>";
$q = "delete from tbl_aisFicheros where idcliente in (select id from tbl_aisCliente where fecha < (unix_timestamp()-60*60*24*60) and borrficheros = 0 order by fecha) limit 200";
$temp->query($q);
$texto .= $q."<br>";

$q = "select usuario from tbl_aisCliente where id not in (select idcliente from tbl_aisFicheros) and fecha < (unix_timestamp()-60*60*24*60) and borrficheros = 0";
$temp->query($q);
$texto .= $q."<br>";
$arrCli = $temp->loadAssocList();

$texto .= json_encode($arrCli)."<br>";

/**
 * Descarga el fichero con las ipcubanas a las 20:00 y rellena una tabla con ellas
 */
if ($horaG == 20
    // || 1==1
) {
    $fp = fopen("https://www.nirsoft.net/countryip/cu.csv", 'r');
    // echo "<br><br>https://www.nirsoft.net/countryip/cu.csv<br><br>";
    while (!feof($fp)) {
        $linea = fgets($fp);
        $arrIp = explode(',',$linea);
        if (strlen($arrIp[0]) > 4){
            $q = "select count(*) total from tbl_ipCubana where ipentrada = '{$arrIp[0]}'";
            $temp->query($q);
            if ($temp->f('total') == 0) {
                $temp->query("insert into tbl_ipCubana (ipentrada, ipfinal, fecha) values ('{$arrIp[0]}', '{$arrIp[1]}', unix_timestamp())");
            }
            // echo ip2long($arrIp[0]). "--" . ip2long($arrIp[1]) . "<br />";
            // $entra = ip2long('200.55.128.45');
            // if ($entra >= ip2long($arrIp[0]) && $entra <= ip2long($arrIp[1])) echo "SIIIIIIIIIII";
        }
    }
    fclose($fp);

}


for ($i=0; $i<count($arrCli); $i++) {
	$q = "update tbl_aisCliente set borrficheros = 1 where binary usuario = '".$arrCli[$i]['usuario']."'";
	$temp->query($q);
	rrmdir ("/var/www/vhosts/administracomercios.com/httpdocs/ficTitan/".$arrCli[$i]['usuario']);
}

function rrmdir($dir) {

	if (is_dir($dir)) {
	$objects = scandir($dir);
	foreach ($objects as $object) {
		if ($object != "." && $object != "..") {
		if (filetype($dir."/".$object) == "dir") 
			rrmdir($dir."/".$object); 
		else unlink   ($dir."/".$object);
		}
	}
	reset($objects);
	rmdir($dir);
	}
 }

//sincroniza las pagadas en transacciones con las pendientes en reserva
$query = "update tbl_reserva r, tbl_transacciones t
			set r.id_transaccion = t.idtransaccion, r.valor = r.valor_inicial, r.bankId = t.codigo, r.fechaPagada = t.fecha_mod, r.estado = 'A'
			where t.identificador = r.codigo and r.estado = 'P' and t.estado = 'A' and r.id_comercio = t.idcomercio";
// $temp->query( $query );
$texto .= $query."\n<br>";

/**
 * Revisa los clientes y beneficiarios de AIS que no se hayan inscritos en Titanes en las últimas
 * 24hrs y trata de reinscribirlos
 */
$aBen = 0;
$arrTab = array("id, idcimex, nombre, papellido, sapellido, numDocumento, fechaDocumento, correo, telf1, paisResidencia, fecha, ".
					"provincia, ciudad, direccion, CP, paisNacimiento, fnacimiento, sexo, ocupacion, salariomensual, usuario ".
					"from tbl_aisCliente b where ",
				"b.id, b.idcimex, c.idcimex idcliente, b.nombre, b.papellido, b.sapellido, b.telf, b.direccion, b.ciudad, ".
				"b.fecha, b.numDocumento, b.idrazon, r.idrelacion from tbl_aisBeneficiario b, tbl_aisClienteBeneficiario r, tbl_aisCliente c ".
				"where r.idbeneficiario = b.id and r.idcliente = c.id and b.fecha > ".($ahora - 24*60*60)." and ");
foreach ($arrTab as $tabla) {
	$q = "select $tabla (b.idtitanes is null or b.idtitanes = 0)";
	$texto .=  $q."<br>\n";
	$temp->query($q);
	if ($temp->num_rows()>0){
		$arrCTi = $temp->loadAssocList();
// 		print_r($arrCTi);
		foreach ($arrCTi as $cliente) {
// 			if ($cliente['fecha'] >= (time()-(60*60*24))) {
				//los clientes han sido subidos hace menos de 24 hrs trato de reincribirlos en titanes
				if ($aBen == 1) {
					$texto .=  "trabaja con el beneficiario ".$cliente['idcimex']."<br>\n";
					$data = array(
							'Id' => $cliente['idcimex'],
							'IdCliente' => $cliente['idcliente'],
							'Nombre' => $cliente['nombre'],
							'Apellido1' => $cliente['papellido'],
							'Apellido2' => $cliente['sapellido'],
							'Phone' => $cliente['telf'],
							'Address' => $cliente['direccion'],
							'City' => $cliente['ciudad'],
							'CI' => $cliente['numDocumento'],
							'Relation' => $cliente['idrelacion'],
							'Reason' => $cliente['idrazon']
					);
				} else {
					$texto .=  "trabaja con el cliente ".$cliente['idcimex']."<br>\n";
					$data = array(
							'Id' => $cliente['idcimex'],
							'Nombre' => $cliente['nombre'],
							'Apellido1' => $cliente['papellido'],
							'Apellido2' => $cliente['sapellido'],
							'DocumentNumber' => $cliente['numDocumento'],
							'DocumentExpirationDate' => date('d/m/y',$cliente['fechaDocumento']),
							'Email' => $cliente['correo'],
							'PhoneNumber' => $cliente['telf1'],
							'Country' => paisCod($cliente['paisResidencia']),
							'Province' => $cliente['provincia'],
							'City' => $cliente['ciudad'],
							'Address' => $cliente['direccion'],
							'PostalCode' => $cliente['CP'],
							'CountryOfBirth' => paisCod($cliente['paisNacimiento']),
							'DateOfBirth' => date('d/m/y',$cliente['fnacimiento']),
							'Gender' => ($cliente['sexo']*1)-1,
							'Profesion' => $cliente['ocupacion'],
							'MonthSalary' => $cliente['salariomensual'],
							'UsuarioCode' => $cliente['usuario']
					);
				}
				foreach ($data as $key => $value) {
					$texto .= "$key => $value<br>";
				}
				$ch = curl_init(_ESTA_URL.'/datInscr.php');
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				$texto .= "\n<br>";

				// $salidaCurl = curl_exec($ch);
				$texto .= "Acá el error <br>\n";
				if (curl_error($ch)) $texto .=  "Error devuelto: ".curl_error($ch)."<br>\n";
				$texto .=  "Enviado a Titanes, envío $i - ".htmlspecialchars_decode($salidaCurl)."<br>\n";
// 			} else {// si al cabo de 24 horas tratando aún no se han inscrito los borro
// 				$texto .=  "Borra al cliente ".$cliente['idcimex']."<br>\n";
// 				if (stripos($tabla, 'Cliente')) $q = "delete from tbl_aisCliente id = ".$cliente['id'];
// 				else $q = "delete from tbl_aisBeneficiario id = ".$cliente['id'];
// 				$temp->query($q);
// 			}
		}
	}
	$aBen++;
}

/**
 * Compara lo que se ha hecho hasta esta hora en el día con lo mmismo de días anteriores
 */
$canD = 31; // cantidad de semanas para atrás
$q = "select format(sum(case t.estado
		when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
			when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
				when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
					when 'A' then (t.valor/100/t.tasa)
		else '0.00' end),2) 'Valor'
		FROM tbl_transacciones t
		where t.estado in ('A','V','B','R')
		and t.tipoEntorno = 'P'
				and from_unixtime( t.fecha_mod, '%w' ) = from_unixtime(unix_timestamp(),'%w')
				and from_unixtime( t.fecha_mod, '%H')*1 between 0 and from_unixtime(unix_timestamp(),'%H')*1
				GROUP BY from_unixtime( t.fecha_mod, '%d/%m/%y' )
				order by t.fecha_mod desc
				limit 0,$canD";
$temp->query($q);
$sumopr = 0;
$arrvale = $temp->loadRowList(); //el valor de $arrvale[0] corresponde al día actual
$valehoy = str_replace(",", "", $arrvale[0][0]);
for($i = 1; $i<count($arrvale); $i++){
	$sumopr = $sumopr + str_replace(",", "", $arrvale[$i][0]);
}
$prom = $sumopr/$i;

if ($valehoy/$prom < 0.85) {
	$texto .= "<br>Alerta!!!, Día por debajo del promedio\n<br>";
}elseif ($valehoy/$prom > 1.15) {
	$texto .= "<br>Alerta!!!, Día por encima del promedio\n<br>";
}

//Si la hora es impar borra las ips de la tabla de ips Bloqueadas ipBL que tengan
//5 o mas intentos pero aparezcan en la tabla de  transacciones
if ($horaG & 1) {
	$texto .= "Analiza las Ips y las borra\n<br>";
	$q = "delete from tbl_ipBL where ip != '3.121.188.123' and fecha > (". (time()-24*60*60).") and (ip in (select ip from tbl_transacciones ) or ip in (select ip from tbl_ipblancas ))";
	$texto .= $q."\n<br>";
	$temp->query($q);
}


if (date('dmH') == '220202') {
	$mens = "";
	$q = "select count(*) total from tbl_transacciones where idtransaccion in (select idtransaccion from tbl_transaccionesOld where idtransaccion != '220111153315')";
	$temp->query($q);
	$mens .= "Se van a eliminar ".$temp->f('total')." transacciones a la tabla tbl_transacciones\n<br>";
	$q = "delete from tbl_transacciones where idtransaccion in (select idtransaccion from tbl_transaccionesOld where idtransaccion != '220111153315')";
	$temp->query($q);
	$mens .= $q."<br>\n";
	sendTelegram($mens,null);

}
/**
 * Actualiza las transacciones en la tabla transaccionesOld y aligera transacciones a las 2am
 */

if (date('dmH') == '010102' //el 1ro de enero todos los años a las 2am
// 		|| 1==1
		) {
	$fecOld = mktime(23, 59, 59, 12, 31, date("Y")-2);
	$q = "select count(*) total from tbl_transacciones where fecha < ".$fecOld.
			" and idtransaccion not in (select idtransaccion from tbl_transaccionesOld)";
	$temp->query($q);
	$mess = "Se van a transferir ".$temp->f('total')." transacciones a la tabla tbl_transferenciasOld\n<br>";
	$q = "insert into tbl_transaccionesOld (select * from tbl_transacciones where fecha < ".$fecOld.
			" and idtransaccion not in (select idtransaccion from tbl_transaccionesOld))";
	$temp->query($q);
	$texto .= $q."<br>\n";

	$q = "select count(*) total from tbl_transacciones where idtransaccion in (select idtransaccion from tbl_transaccionesOld)";
	$temp->query($q);
	$mess .= "Se van a eliminar ".$temp->f('total')." transacciones a la tabla tbl_transacciones\n<br>";
	$q = "delete from tbl_transacciones where idtransaccion in (select idtransaccion from tbl_transaccionesOld)";
	$temp->query($q);
	$texto .= $q."<br>\n";
	$texto .= $mess;

	$corCreo->todo(8, "Actualiza las transacciones en la tabla transaccionesOld y aligera transacciones", $mess);
}



if($horaG == 2 //a las 2am
	// || 1 == 1
	) { 
	/**
	* Llena la tabla ResultDatos
	*/
	$fecanter = leeSetup('fecResultDatos');
	$q = "insert into resultDatos select t.idtransaccion, t.idcomercio, t.identificador, t.codigo,
			t.pasarela, t.tipoOperacion, t.idioma, t.fecha, t.fecha_mod, t.valor, t.valor_inicial,
			t.tipoEntorno, t.moneda, t.estado, t.estadoP, concat('sesion'), t.ip, t.tasa,
			case t.estado
				when 'A' then t.valor/100/t.tasa
				when 'B' then t.valor/100/t.tasa
				when 'V' then t.valor/100/t.tasa
				when 'R' then t.valor/100/t.tasa else 0 end'euroEquiv',
			t.pago, t.tasaDev, t.euroEquivDev, t.solDev, t.amenaza, t.repudiada, t.fechaPagada,
			t.tpv, t.idpais, t.estadoAMF, t.tarjetas, t.identificadorBnco, t.id_tarjeta, t.mtoMonBnc,
			t.carDevCom, p.idbanco, p.idempresa, FROM_UNIXTIME(t.fecha_mod) 'fecha_act', t.tipoPago

		from tbl_transacciones t, tbl_pasarela p
		where p.idPasarela = t.pasarela
			and t.fecha_mod between $fecanter and $fechaHoy";
	$texto .= $q."<br>\n";
	$temp->query($q);
	actSetup($fechaHoy, 'fecResultDatos');

	
	/**
	* Pone los paises a las IPs que aún les falte
	*/
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
				$texto .= "La ip ".$temp->f('ip')." no tiene país asociado, hablar con con K para que actualice el módulo geoip.\n<br>";
			}
		}
		$texto .= "\n<br>".$correoMi;
        if(strlen($correoMi) > 0) {
        	$corCreo->set_message($correoMi);
        	$corCreo->set_subject("IP que no resuelve");
        	$corCreo->envia(13);
//          $corCreo->todo(13,"IP que no resuelve",$correoMi);

           $q = 'insert into tbl_traza (titulo,traza,fecha) values ("IP que no resuelve","'.html_entity_decode($correoMi).'",'.time().')';
           $temp->query($q);
        }
	}
}

/**
 * Borra los datos de las tarjetas vencidas de la tbl_referencia
 */
if ($horaG == 0
 		// || 1==1
		)  {
	$temp->query("delete from tbl_referencia where fechavig < unix_timestamp()");
}

/**
 * Realiza el informe Diario y Mensual a las 00:00
 */
$texto .= "\n<br>".leeSetup('fechaInf')." + 86390 < ".time();
if ($horaG == 0
 		// || 1==1
		)  {

	//Revisa los cierres que no tienen asociados facturas y verifica su fecha para
	//realizar el Informe de Facturación
	$cierrMuela = '';
	$j = 1;
	//Para los comercios con cierre cada 15 días
	$q = "select i.cierre, c.nombre from tbl_cierreTransac i, tbl_comercio c where i.idcierre not in (select idcierrehijo from tbl_colCierreCierre) and c.id = i.idcomercio and idcierre not in (select idcierre from tbl_factura) and i.transferir = 1 and c.cierrePer in ('Q','M') and i.fechaCierre <= unix_timestamp() - 3*(60*60*24) order by c.id";
	$temp->query($q);
	$arrCrr = $temp->loadRowList();
	for ($i = 0; $i < count($arrCrr); $i++){
		$cierrMuela .= "$j - El comercio ".$arrCrr[$i][1]." est&aacute; atrasado en el env&iacute;o de la factura para el cierre " .$arrCrr[$i][0]." <br><br>";
		$j++;
	}
	//Para los comercios con cierre semanal
	$q = "select i.cierre, c.nombre from tbl_cierreTransac i, tbl_comercio c where i.idcierre not in (select idcierrehijo from tbl_colCierreCierre) and c.id = i.idcomercio and idcierre not in (select idcierre from tbl_factura) and i.transferir = 1 and c.cierrePer in ('S') and i.fechaCierre <= unix_timestamp() - 2*(60*60*24) order by c.id";
	$temp->query($q);
	$arrCrr = $temp->loadRowList();
	// var_dump($arrCrr);
	for ($i = 0; $i < count($arrCrr); $i++){
		$cierrMuela .= "$j - El comercio ".$arrCrr[$i][1]." est&aacute; atrasado en el env&iacute;o de la factura para el cierre " .$arrCrr[$i][0]." <br><br>";
		$j++;
	}

	$corCreo->todo(62, "Informe de Facturación", $cierrMuela);


	//    Actualiza a Alex para que le llegue el correo
    $q = "update tbl_admin set activo = 'S', fecha_visita = ". time() .", fechaPass = ". time() ."  where idadmin = 166";
    $temp->query($q);
    //    Actualiza a Luis para que no le llegue el correo
    $q = "update tbl_admin set activo = 'N' where idadmin = 13";
    $temp->query($q);

    //Chequea que no existan operaciones por devolver de Prueba
    $q = "select idtransaccion, from_unixtime(fecha,'%d/%m/%y %H') fec from tbl_transacciones where estado = 'A' and idcomercio = '122327460662'
    		and tipoEntorno = 'P'";
    $temp->query($q);
    if($temp->getNumRows() > 0) {
    	$arrPr = $temp->loadRowList();
    	$texto .= "\n<br>\n<br>Operaciones de Prueba a devolver\n<br>".$q;
		$mensage = "";
    	foreach ($arrPr as $item){
    		$mensage .= "\n<br>La operación {$item[0]} realizada el {$item[1]}";
    	}
    	$texto .= $mensage;
   	// $corCreo->to("gestorintegral@bidaiondo.com");
   	// $corCreo->todo(8, "Operaciones de Prueba que se deben devolver", $mensage);
    }

	//prepara el informe a info diario o mensual
	$mensage = $telText = '';

    for($d = 0; $d<2; $d++) {
        // if ($d == 0 || (date('d') == 1 && $d == 1)) { //si d=0 hace el diario si no chequea si es día 1 para hacer el del mes
        // 	if ($d == 0) {
        // 		$texto .= "\n<br>Informe Diario:\n<br>";
        // 		$var = 'diario';
        // 		$fecMod = $fechaAyer;
        // 		$fe = date('d/m/Y', $fechaAyer);
        // 		$fecha30Dante = mktime(0, 0, 0, date("m"), date("d")-30, date("Y"));
        // 		$text = '% -30d';
        // 	} else {
        // 		$texto .= "\n<br>Informe Mensual:\n<br>";
        // 		$var = 'mensual';
        // 		$fecMod = $fechaMAnte;
        // 		$fe = date('M - Y',$fechaMAnte);
        // 		$fecha30Dante = mktime(0, 0, 0, date("m")-12, date("d"), date("Y"));
        // 		$text = '% -12m';
        // 	}
        if (date('d') == 1 && $d == 1) {//Hace el mensual
            $texto .= "\n<br>Informe Mensual:\n<br>";
            $var = 'mensual';
            $fecMod = $fechaMAnte;
            $fe = date('M - Y',$fechaMAnte);
            $fecha30Dante = mktime(0, 0, 0, date("m")-12, date("d"), date("Y"));
            $text = '% -12m';
            $varMes = 1;
            $textMdo = "mes";

        } else {// hace el diario
            $texto .= "\n<br>Informe Diario:\n<br>";
            $var = 'diario';
            $fecMod = $fechaAyer;
            $fe = date('d/m/Y', $fechaAyer);
            $fecha30Dante = mktime(0, 0, 0, date("m"), date("d")-30, date("Y"));
            $text = '% -30d';
            $varMes = 0;
            $textMdo = "día";

        }

        if ($var == 'diario' && $d == 1) break;

        $texto .= "fecMod:$fecMod\n<br>";
        $texto .= "fe:$fe\n<br>";
        $texto .= "fecha30Dante:$fecha30Dante\n<br>";
        $texto .= "text:$text\n<br>";

        //echo "fechaAyer=$fechaAyer<br>";
        //echo "fecha30Dante=$fecha30Dante<br>";
        $elem = "case t.estado when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/t.tasa)),
                (t.valor/100/t.tasa)) when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/t.tasa)),
                t.valor/100/t.tasa) when 'A' then (t.valor/100/t.tasa) else '0.00' end";

        if ($varMes == 0) {
            $mensage .= "<br />Estimado en el mes:";
            $q = "select format(sum($elem) / $feccc * $diasMesAct, 2) 'Estimado'
                    FROM tbl_transacciones t
                    where estado in ('A')
                        and tipoEntorno = 'P'
                        and fecha_mod > $iniMes;";
        // echo $q;
            $temp->query($q);
            $arrR = $temp->loadRowList();
        // print_r($arrR);
            foreach ($arrR as $item) {
                $mensage .= " {$item[0]}";
            }
            $mensage .= "<br />";
        }

        $mensage .= "<br />Estado de las operaciones en el $textMdo<br /><table><tr><td>Día</td><td>Valor</td><td>Transacc Acep</td><td>val/trans</td>
        <td>Tot trans</td><td style='text-align:center;width:60px'>% Acep</td></tr>";
        $q = "select from_unixtime( t.fecha_mod, '%d/%m/%y - %W' ) as 'Día',
            format(sum($elem),2) 'Valor',
            count(t.idtransaccion) 'Transacc Acep',
            format(sum($elem) / count(t.idtransaccion), 2) 'val/trans',
            (select count(i.idtransaccion)
                from tbl_transacciones i
                where from_unixtime(t.fecha_mod,'%d%m%Y') = from_unixtime(i.fecha_mod,'%d%m%Y')
                    and i.fecha_mod > ($fecMod)
                    and i.tipoEntorno = 'P') 'Tot trans',
            format(count(t.idtransaccion)/(select count(i.idtransaccion)
                from tbl_transacciones i
                where from_unixtime(t.fecha_mod,'%d%m%Y') = from_unixtime(i.fecha_mod,'%d%m%Y')
                    and i.fecha_mod > ($fecMod)
                    and i.tipoEntorno = 'P')*100,2) '% Acep'
        FROM tbl_transacciones t
        where t.estado in ('A','V','B','R')
            and t.tipoEntorno = 'P'
            and t.fecha_mod > ($fecMod)
        GROUP BY from_unixtime( t.fecha_mod, '%d/%m/%y' )
        order by t.fecha_mod desc;";
    // echo $q;
        $temp->query($q);
        $arrR = $temp->loadRowList();
        // print_r($arrR);
        foreach ($arrR as $item) {
            $mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td>".
                    "<td style='text-align:center;width:60px'>{$item[2]}</td>".
                    "<td style='text-align:center;width:60px'>{$item[3]}</td>".
                    "<td style='text-align:center;width:60px'>{$item[4]}</td>".
                    "<td style='text-align:center;width:60px'>{$item[5]}</td>".
                    "</tr>";
        }
		$mensage .= "</table>";
		$telText = str_replace(array("<table>", "</table>", "<tr>", "<td>", "<td style='text-align:center;width:60px'>"),"",str_replace("</td>", chr(9), $mensage));

        $mensage .= "<br />Porcientos del estado de las operaciones en el $textMdo<br /><table><tr><td>Estado</td><td style='text-align:center;width:60px'>"
                . "cant.</td><td style='text-align:center;width:60px'>%</td></tr>";
        $q = "select case t.estado
                    when 'P' then 'En Proceso'
                    when 'A' then 'Aceptada'
                    when 'D' then 'Denegada'
                    when 'N' then 'No Procesada'
                    when 'B' then 'Anulada'
                    when 'V' then 'Devuelta'
                    when 'R' then 'Reclamada'
                    else '' end estad,
                count(t.idtransaccion) cant,
                format((count(t.idtransaccion)*100/(select count(r.idtransaccion)
                    from tbl_transacciones r
                    where r.fecha_mod > '$fecMod'
                        and r.tipoEntorno = 'P')),1) '%'
            from tbl_transacciones t
            where t.tipoEntorno = 'P'
                and t.fecha_mod > '$fecMod'
            group by case t.estado
                    when 'P' then 'P'
                    when 'A' then 'A'
                    when 'D' then 'D'
                    when 'N' then 'N'
                    when 'B' then 'B'
                    when 'V' then 'V'
                    when 'R' then 'R' end;";
        // echo $q;
        $temp->query($q);
        $arrR = $temp->loadRowList();
        foreach ($arrR as $item) {
            $mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td>"
                    . "<td style='text-align:center;width:60px'>{$item[2]}</td></tr>";
        }
        $mensage .= "</table>";


        $mensage .= "<br />Pasarelas en el $textMdo<br /><table><tr>
                            <td style='text-align:center;'>Pasarela</td>
                            <td style='text-align:center;'>Lim Diario</td>
                            <td style='text-align:center;'>Acum D&iacute;a</td>
                            <td style='text-align:center;'>Lim. Mens.</td>
                            <td style='text-align:center;'>Acum. Mens.</td>
                            <td style='text-align:center;'>Cant.</td>
                            <td style='text-align:center;'>%</td><td style='text-align:center;'>% -30</td></tr>";

        $q = "select concat(p.nombre,' (',p.idPasarela, ') ') pasarela,
                case format(LimDiar,2) when '100,000,000.00' then 'N/L' else format(LimDiar,2) end limd,
                format(sum($elem),2) acumd, case format(LimMens,2) when '100,000,000.00' then 'N/L' else format(LimMens,2) end limm,
                (select format(sum(case n.estado
                    when 'B' then if (n.fecha_mod < n.fechaPagada, (-1 * ((n.valor_inicial-n.valor)/100/n.tasa)), n.valor/100/n.tasa)
                    when 'V' then if (n.fecha_mod < n.fechaPagada, (-1 * ((n.valor_inicial-n.valor)/100/n.tasa)), n.valor/100/n.tasa)
                    when 'R' then if (n.fecha_mod < n.fechaPagada, (-1 * ((n.valor_inicial-n.valor)/100/n.tasa)), n.valor/100/n.tasa)
                    when 'A' then (n.valor/100/n.tasa)
                    else '0.00' end),2) from tbl_transacciones n where t.pasarela = n.pasarela and from_unixtime(n.fecha_mod, '%m%Y')='".date('mY')."')  acumm,
                count(t.idtransaccion) cant,
                format((count(t.idtransaccion)*100/(select count(r.idtransaccion)
                    from tbl_transacciones r
                    where r.pasarela = t.pasarela
                        and r.fecha_mod > '$fecMod'
                        and r.tipoEntorno = 'P')),2) '%',
                format((select count(n.idtransaccion)
                    from tbl_transacciones n
                    where n.estado = 'A'
                        and t.pasarela = n.pasarela
                        and t.tipoEntorno = 'P'
                        and n.fecha_mod > ($fecMod-(30*24*60*60)))*100/(select count(s.idtransaccion)
                    from tbl_transacciones s
                    where t.pasarela = s.pasarela
                        and s.tipoEntorno = 'P'
                        and s.fecha_mod > ($fecMod-(30*24*60*60))),2) '% - 30'
            from tbl_transacciones t, tbl_pasarela p
            where t.estado = 'A'
                and t.pasarela = idPasarela
                and t.tipoEntorno = 'P'
                and t.fecha_mod > '$fecMod'
            group by p.nombre
            order by p.nombre;";

        $temp->query($q);
        $arrR = $temp->loadRowList();
        foreach ($arrR as $item) {
            $mensage .= "<tr><td>{$item[0]}</td><td style='text-align:center;width:60px'>{$item[1]}</td>"
                    . " <td style='text-align:center;width:60px'>{$item[2]}</td>"
                    . " <td style='text-align:center;width:60px'>{$item[3]}</td>"
                    . " <td style='text-align:center;width:60px'>{$item[4]}</td>"
                    . " <td style='text-align:center;width:60px'>{$item[5]}</td>"
                    . " <td style='text-align:center;width:60px'>{$item[6]}</td>"
                    . " <td style='text-align:center;width:60px'>{$item[7]}</td></tr>";
        }
        $mensage .= "</table>";


        $mensage .= "<br />Montos por comercios en el $textMdo<br /><table><tr><td>Comercio</td>"
                . " <td>Valor</td><td style='text-align:center;width:60px'>Transacc</td><td>Valor/Trans</td>"
                . " <td style='text-align:center;width:60px'>% Acept</td><td style='text-align:center;width:60px'>% -30</td></tr>";
        $q = "select concat(c.nombre,'&nbsp;',c.id,'-',t.idcomercio) comercio,
                    format(sum($elem),2) 'Valor',
                    count(t.idtransaccion) Transacc,
                    format(sum($elem) / count(*), 2) 'val/trans',
                    format(count(t.idtransaccion) *100/(select count(n.idtransaccion)
                        from tbl_transacciones n
                        where n.tipoEntorno = 'P'
                            and n.fecha_mod > '$fecMod'
                            and t.idcomercio = n.idcomercio),2) '% Acep' ,
                    format((select count(j.idtransaccion )
                        from tbl_transacciones j
                        where j.idcomercio = t.idcomercio
                            and j.tipoEntorno = 'P'
                            and j.estado in ('A','V','B','R')
                    and fecha_mod > '$fechaMAnte' ) *100/(select count(i.idtransaccion)
                        from tbl_transacciones i
                        where i.tipoEntorno = 'P'
                            and i.fecha_mod > '$fechaMAnte'
                            and t.idcomercio = i.idcomercio),2) '% -30'
                FROM tbl_comercio c, tbl_transacciones t
                where t.idcomercio = c.idcomercio
                    and t.estado in ('A','V','B','R')
                    and t.tipoEntorno = 'P'
                    and fecha_mod > '$fecMod'
                group by t.idcomercio
                order by sum(case t.estado when 'A' then euroEquiv else euroEquivDev end) desc;";
        $temp->query($q);
        $arrR = $temp->loadRowList();
        foreach ($arrR as $item) {
            $mensage .= "<tr><td>{$item[0]}</td><td>{$item[1]}</td><td style='text-align:center;width:60px'>{$item[2]}"
                    . " </td><td style='text-align:center;width:60px'>{$item[3]}</td>"
                    . " </td><td style='text-align:center;width:60px'>{$item[4]}</td>"
                    . " </td><td style='text-align:center;width:60px'>{$item[5]}</td>
            </tr>";
        }
        $mensage .= "</table>";
        // }
    }
    // echo $mensage; exit;

    $corCreo->set_subject("Informe estado $var del Concentrador $fe");
    $corCreo->set_message($mensage);

	// $texto .= "\n<br>Informe envio - ".$corCreo->envia(1);
	// echo $message;
	sendTelegram("Informe diario $fe\n".$telText,null);
    //    echo $texto;

		/**
		 * Comprobaciones diarias del trabajo
		 */
		//Porcientos de Aceptadas por pasarela - monedas no bajen del 20%
		$q = "select p.nombre, m.moneda, ("
				. "	select count(r.idtransaccion) from tbl_transacciones r where r.pasarela = t.pasarela and p.tipo = 'P' and r.moneda = t.moneda
					and r.estado = 'A' and r.fecha_mod > '$fechaAyer')/"
				. "count(idtransaccion)*100 "
			. "from tbl_transacciones t, tbl_pasarela p, tbl_moneda m "
			. "where t.moneda = m.idmoneda and t.pasarela = idPasarela and p.activo = 1 and p.tipo = 'P' and t.tipoEntorno = 'P' and fecha_mod > '$fechaAyer'
				group by pasarela, t.moneda";
		$temp->query($q);
		if ($temp->getErrorMsg()) $texto .= "error: ".$q."\n<br>".$temp->getErrorMsg()."\n<br>";
		$arrR = $temp->loadRowList();
		$porcMin = 20;
		$sale = "";

		foreach ($arrR as $item) {
			if ($item[2] < $porcMin) {
				$sale .= "La combinación pasarela / moneda: ".$item[0]." / ".$item[1].", ha estado hoy al ".number_format($item[2],1).
							"% por debajo de los $porcMin% permitidos\n<br>";
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
				. "where t.pasarela = p.idPasarela and t.estado in ('A','V') and p.activo = 1 and tipoEntorno = 'P' and fecha_mod > $fechaAyer and t.pasarela in (".$pasarela[1].")";
			$temp->query($q);
			if ($temp->getErrorMsg()) $texto .= "error: ".$q."\n<br>".$temp->getErrorMsg()."\n<br>";
			$arrR = $temp->loadRowList();
			// print_r($arrR);
			// echo $arrR[0][1]." > ".$pasarela[2];
			if ($arrR[0][1] > $pasarela[2]) $sale .= "La pasarela ".$arrR[0][0]." hoy a facturado ".number_format ($arrR[0][1],2)." EUR lo que se va por encima de los ".
					number_format ($pasarela[2],2)." EUR que debe facturar\n<br>";
		}

		//Avisar de alguna pasarela que no tenga operaciones
		$q = "select p.nombre, (select count(t.idtransaccion) from tbl_transacciones t where t.pasarela = p.idPasarela and t.estado = 'A' and t.fecha_mod > "
					. "$fechaHoy-86400) from tbl_pasarela p where p.activo = 1 and p.tipo = 'P'";
		$temp->query($q);

		if ($temp->getErrorMsg()) $texto .= "error: ".$q."\n<br>".$temp->getErrorMsg()."\n<br>";
		$arrR = $temp->loadRowList();
			// print_r($arrR);
		foreach ($arrR as $value) {
			if ($value[1] == 0) $sale .= "La pasarela ".$value[0]." no ha tenido hoy operaciones Aceptadas\n<br>";
		}

		if ($sale) {
			// $sale = "";
			$corCreo->set_message($sale);
			$corCreo->set_subject("Comprobaciones diarias");
			$corCreo->envia(40);
			// $corCreo->todo(40,"Comprobaciones diarias", $sale);
			$texto .= $sale;
		}

		/* Fin de las comprobaciones diarias del trabajo */

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
						if (is_file($dir.$file)) {
							$arrF = explode('.', $dir.$file);
							if (end($arrF) == 'php') {
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
			// echo $q;
		$temp->query($q);
		if ($temp->getErrorMsg()) $texto .= $temp->getErrorMsg ();
		$arrFB = $temp->loadRowList();

		// if (count($arrFB) > 0) {//hay ficheros borrados
		// 	foreach ($arrFB as $fic) {
		// 		$testo .= "El fichero ".$fic[0]." con fecha de modificación ".$fic[2]." y tamaño de {$fic[3]} KB fué borrado del directorio\n<br>";

		// 		//se borran los datos en la BD de los ficheros borrados en directorio
		// 		$q = "delete from tbl_ficheros where id = ".$fic[4];
		// 		$temp->query($q);
		// 		if ($temp->getErrorMsg()) $texto .= $temp->getErrorMsg ();
		// 		$q = "delete from tbl_colFilesMod where idfile = ".$fic[4];
		// 		$temp->query($q);
		// 		if ($temp->getErrorMsg()) $texto .= $temp->getErrorMsg ();
		// 	}
		// }

		if ($testo) {
			$testo = "Alerta se han modificado ficheros!!<br>".$testo."";
			$corCreo->set_message($testo);
			$corCreo->set_subject("Revisión de ficheros en disco");
			$corCreo->envia(41);
			sendTelegram("Revisión de ficheros en disco<br>".$testo,null);
			// $corCreo->todo(41,"Revisión de ficheros en disco", $testo);
			$texto .= $testo;
		}
		/* Fin de la revisión diaria del estado de los ficheros*/


		/*Comienzo de avisos de Transferencias en proceso*/
		$texto .= "\n<br>Avisos de transferencias pendientes\n<br>";
		$ndia = 10; //número de días que estarán las Transferencias activas

		$q = "select r.idTransf, format((r.valor/100),2), m.moneda, r.cliente, c.nombre com, p.nombre, from_unixtime(r.fecha,'%d/%m/%Y'), r.fecha,
					a.param, a.nombre, a.email, r.id, r.vista, b.banco
				from tbl_transferencias r, tbl_comercio c, tbl_moneda m, tbl_pasarela p, tbl_admin a, tbl_bancos b ".
				// " where r.idPasarela = p.idPasarela and r.moneda = m.idmoneda and r.idCom = c.id and r.estado = 'P' and r.fecha >= ".(time()-45*24*60*60).
				" where a.idadmin = r.idadmin and r.idPasarela = p.idPasarela and r.moneda = m.idmoneda and r.idCom = c.id and r.estado = 'P' ".
				" and r.activa = 1 and p.idbanco = b.id order by b.banco, c.nombre";
		// echo $q;
		$temp->query($q);
		$arrTraf = $temp->loadRowList();

		if (count($arrTraf) > 0){
			$cade = "\n<br>Transferencias Pendientes\n<br>\n<br>";
			$banco = '';
			$i = 1;
			foreach ($arrTraf as $item) {

			    if ($item[7] < (time()-$ndia*24*60*60) && $item[12] == 0) {
					$texti = "Estimado (a){usuario}:<br><br>Su cliente {cliente} no ha aceptado los Términos y Condiciones, de la Orden de Pago realizada por usted el {fecha} en el plazo establecido de 10 días hábiles y por ende no ha recibido la misma.<br><br>En caso de proceder, puede realizar una nueva invitación de Transferencia.<br><br>Administrador de Comercios";

					$texto .= "\n<br>Se elimina la transferencia {$item[11]} del comercio {utf8_decode($item[4])}";
					$corCreo->to($item[10]);
					$corCreo->todo(47, "Invitación de transferencia ha caducado", str_replace("{fecha}", $item[6], str_replace("{cliente}", $item[3], str_replace("{usuario}", utf8_decode($item[9]), $texti))));
					$temp->query("update tbl_transferencias set activa = 0 where id = ".$item[11]);
					$cade .= "La Transferencia ".$item[0]." del TPV ".utf8_decode($item[5])." ha caducado, se le ha enviado un correo al usuario ".utf8_decode($item[9])." del comercio ".
								utf8_decode($item[4])." para avisarle.\n<br>\n<br>";
			    } elseif ($item[12] == 1){
					if ($banco != $item[13]) {$cade .= "<span style='font-weight:bold;'>Banco: ".$item[13]."</span><br>";$banco = $item[13];$i=1;}
					else $i++;
					$cade .= "$i.- El Comercio ".utf8_decode($item[4])." tiene puesta la Transferencia ".$item[0]." desde el pasado día ".$item[6].", al cliente ".$item[3].
							" por valor de ".$item[1]." ".$item[2]." por el TPV ".utf8_decode($item[5])."\n<br>\n<br>";
				}
			}
			// echo $cade;
			$corCreo->todo(47, 'Transferencias Pendientes de recibir', $cade);
		}
		/*Fin de avisos de Transferencias en proceso*/

    }


if ($horaG == 0
    // || 1==1
    )  {
    /*
        * Calcula y Avisa los Cierres
        */

	include_once("hcierre.php");

	if (1!=1) {
    $texto = '';
    $texto .= "\n<br>Haciendo los cierres\n<br>";
    $sale = '';
    // comercios que no se les realiza cierres (todos los comercios con nombre prueba y travels and discovery):
    $idNocierre = "1,71,72,91,114,115,119,130,141,124";

    //saco el listado de todos los comercios activos
    $q = "select id, idcomercio, nombre, cierrePer, maxCierre, cierrePor from tbl_comercio where activo = 'S' and llevacierre = 1 and cierrePor = id and id not in ($idNocierre)";
    // $q .= " and id = 24 "; //obligo al cierre de un comercio específico
    // and id in (select distinct cierrePor from tbl_comercio)";
    $temp->query($q);
    // echo "$q<br>";exit;
    $texto .= "$q\n<br>";
    $arrCom = $temp->loadRowList();
	
    foreach ($arrCom as $com) {
        $strid = $strnom = $men = '';
        $texto .= "\n<br>Trabajando con ".$com[2]."\n<br>";

        //busco identificadores y nombre de comercios agrupados
        $q = "select idcomercio, nombre from tbl_comercio where cierrePor = ".$com[5];
        $temp->query($q);
		// echo "$q\n<br>";
        $texto .= "$q\n<br>";
        $arrNom = $temp->loadRowList();
        foreach ($arrNom as $itnom){
            $strid .= $itnom[0]."','";
            $strnom .= $itnom[1].", ";
        }
        $strid = rtrim($strid,"','"); //identificador de comercios agrupados
        $strnom = rtrim($strnom,", "); //nombres de comercios agrupados

        //busco los datos del último cierre realizado al comercio
        $q = "select idcierre, r.idtransaccion, t.fecha_mod, t.fecha, t.estado from tbl_cierreTransac r, tbl_transacciones t where t.idtransaccion = r.idtransaccion and t.idcomercio in ('{$strid}') order by r.fecha desc limit 0,1";
        $texto .= "$q\n<br>";
        // echo $texto;exit;
        //error_log($q);
        $temp->query($q);
        if ($temp->getNumRows() == 0) {//Busco en la tabla de transacciones viejas
            $q = "select idcierre, r.idtransaccion, t.fecha_mod, t.fecha, t.estado "
                    . " from tbl_cierreTransac r, tbl_transaccionesOld t "
                    . " where t.idtransaccion = r.idtransaccion and t.idcomercio in ('{$strid}') order by r.fecha desc limit 0,1";
            $texto .= "$q\n<br>";
            // echo "$q\n<br>";
            $temp->query($q);
        }

        if ($temp->getErrorMsg()) $texto .= "\n<br>$q \n<br>Error: ".$temp->getErrorMsg();
        $arrSal = $temp->loadRowList();
        // print_r($arrSal);

        //si el comercio no tiene cierre pongo como fecha del último cierre unos min antes de la primera operación aceptada
        if (count($arrSal) == 0) {
            $q = "select idcomercio, idtransaccion, (fecha_mod - 60), (fecha - 60), estado from tbl_transacciones where idcomercio in ('{$strid}')"
                    ." and tipoEntorno = 'P' order by fecha_mod asc limit 0,1";
            $temp->query($q);
            $texto .= "$q\n<br>";
            if ($temp->getErrorMsg()) $texto .= "\n<br>$q \n<br>Error: ".$temp->getErrorMsg();
            $arrSal = $temp->loadRowList();
        }
        // print_r($arrSal);

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
            // $ffeUn = $item[2]; //fecha de la última transacción del cierre
            $operac = $item[1]; //Ultima operación a la que se realizó el cierre
            $dias = floor((time()-$ffeUn)/(60*60*24)); //días transcurridos entre el último cierre y hoy
            $texto .= $com[2]." - ".$com[3]." - ".$dias."\n<br>";

            // error_log("ffeUn = $ffeUn ");
            // error_log("operac = $operac ");
            // error_log("dias = $dias");
            // error_log($com[2]." - ".$com[3]);

            /*Comienza variación*/
            if($com[3] == 'Q') { //realizar el cierre quincenal
                $texto .= "\n<br>Verifica el cierre quincenal\n<br>";
				$ffeUn = mktime(0, 0, 0, date('n'), 1, date('Y'));
				$dias = floor((time()-$ffeUn)/(60*60*24)); //días transcurridos entre el último cierre y hoy
                $texto .= date('j')." == 1 || (".mktime(0, 0, 0, date('n'), 1, date('Y'))." > ".$ffeUn." && ".date('n')." != ".date('n',$ffeUn).") ||<br>";
                $texto .= date('j')." == 16 || (".mktime(0, 0, 0, date('n'), 16, date('Y'))." > ".$ffeUn." && ".date('n')." != ".date('n',$ffeUn).")<br>";
                if (
                        // (date('j') == 1 || (mktime(0, 0, 0, date('n'), 1, date('Y')) > $ffeUn && date('n') != date('n',$ffeUn)+1)) ||
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
                // if ($dias > 14) {
                // 	$q = "select count(idtransaccion) cant from tbl_transacciones "
                // 			. " where estado = 'A' "
                // 				. " and tipoEntorno = 'P' "
                // 				. " and idcomercio in ('{$strid}')"
                // 				. " and fecha > ".(time()-15*24*60*60);
                // }
            } elseif($com[3] == 'S') { //realizar el cierre semanal
                $texto .= "\n<br>Verifica el cierre Semanal\n<br>";
                // if ( $dias > 6) {
                if ( date('j') == '8' || date('j') == '16' || date('j') == '23' || date('j') == '1') {
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
			$ffeUn =  mktime(0, 0, 0, date('n',strtotime("-1 month")), 1, date('Y'));
			$dias = floor((time()-$ffeUn)/(60*60*24)); //días transcurridos entre el último cierre y hoy
            $texto .= date('j')." == 1 || (".mktime(0, 0, 0, date('n'), 1, date('Y'))." > ".$ffeUn." && ".date('n')." != ".date('n',$ffeUn).")<br>";
            if (date('j') == 1
                    // || 1==1
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

            $q = "select * from tbl_empresas where id not in (5) order by nombre";
            $temp->query($q);
            $arrEmp = $temp->loadRowList();
            $textAdd = "";

            //recorro todas las empresas nuestras para saber las operaciones que han pasado por cada una de ellas
            foreach ($arrEmp as $item) {
                $ffeUn1 = '';

                //busco los datos del último cierre realizado al comercio
                $q = "select t.fecha_mod from tbl_cierreTransac r, tbl_transacciones t where t.idtransaccion = r.idtransaccion and t.idcomercio in ('{$strid}') and r.idempresa = $item[0] order by r.fecha desc limit 0,1";
                $texto .= "$q\n<br>";
                // echo $texto;exit;
                $temp->query($q);

                if ($temp->getNumRows() == 0) {//Busco en la tabla de transacciones viejas
                    $q = "select t.fecha_mod from tbl_cierreTransac r, tbl_transaccionesOld t where t.idtransaccion = r.idtransaccion and t.idcomercio in ('{$strid}') and r.idempresa = $item[0] order by r.fecha desc limit 0,1";
                    $texto .= "$q\n<br>";
                    // echo "$q\n<br>";
                    $temp->query($q);
                }

                if ($temp->getErrorMsg()) $texto .= "\n<br>$q \n<br>Error: ".$temp->getErrorMsg();
                $ffeUn1 = $temp->f('fecha_mod');

                if ($ffeUn1 > 20) {
                    // $ffeUn = $ffeUn1;
                    $acp = $dev = 0;
                    $arraEsta = array('Aceptadas' => "'A'", 'Devueltas, anuladas y reclamadas' => "'B','V','R'", 'Todas' => "'A','B','V','R'");
                    for ($i=0; $i<3; $i++) {
                        switch ($i) {
                            case '0': //Aceptadas en el período
                                $q = "select truncate(sum(t.valor/100/t.tasa),2) suma, count(idtransaccion) total from tbl_transacciones t, tbl_pasarela p "
                                        ." where t.estado in ('A') and t.pasarela = p.idPasarela and p.idempresa = {$item[0]} and "
                                        ." t.idcomercio in ('{$strid}') and t.fecha_mod > $ffeUn1";
                                $texto .= $q."\n<br>";
                                $temp->query($q);
                                if ($temp->f('suma') > 0) {
                                    $acp = $temp->f('suma');
                                    $tot = $temp->f('total');
                                    $textAdd .= " En {$item[1]} para las Aceptadas la cantidad es de $acp\n<br>";
                                    $textAdd .= " En {$item[1]} n&uacute;mero de operaciones Aceptadas es $tot\n<br>";
                                    $texto .= $textAdd."\n<br>";
                                }
                                break;
                            case '1': //Devueltas, anuladas y reclamadas en el período
                                $q = "select truncate(sum((t.valor_inicial-t.valor)/100/t.tasaDev),2) suma from tbl_transacciones t, tbl_pasarela p "
                                        ." where t.estado in ('B','V','R') and t.pasarela = p.idPasarela and p.idempresa = {$item[0]} and "
                                        ." t.idcomercio in ('{$strid}') and t.fecha_mod > $ffeUn1";
                                $texto .= $q."\n<br>";
                                $temp->query($q);
                                if ($temp->f('suma') > 0) {
                                    $dev = $temp->f('suma');
                                    $textAdd .= " En {$item[1]} para las Devueltas, anuladas y reclamadas la cantidad es de $dev\n<br>";
                                    $texto .= $textAdd."\n<br>";
                                }
                                break;
                            case '2':  //Aceptadas - Devueltas
                                $textAdd .= " En {$item[1]} para Aceptadas - Devueltas la cantidad es de ". ($acp-$dev) ."\n<br>";
                                $texto .= $textAdd."\n<br>";
                        }


                    }
                }
            }

            if ($cant > 0) {
                $texto .= "tiene $cant de transacciones Aceptadas\n<br>";
                //llegó al número de días o está pasado
                $menImp .= "El(Los) comercio(s) <strong>".$strnom."</strong> ha(n) llegado al límite por tiempo, $com[3], necesita se"
                                ." le realice el Cierre de inmediato. La última operación procesada fué la No. $operac\n<br>$textAdd";
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
            if ($valeT >= $com[4]) $menImp .= "El(Los) comercio(s) <strong>".$strnom."</strong> tiene(n) acumulado hasta este momento $valeT Euros, "
                        . "lo que hace que haya llegado al límite por monto, necesita se le realice el Cierre de inmediato"
                        . ". La última operación procesada fué la No. $operac.\n<br>$textAdd";
                //llegará en las próximas 24 hr .
            elseif ($valeT >= $com[4]+($valeT/$dias)) $menNimp .= "El(Los) comercio(s) <strong>".$strnom."</strong> tiene(n) acumulado hasta este momento "
                        ." $valeT Euros, llegará en las próximas hrs al límite máximo por monto para que se le realice el Cierre"
                        ." . La última operación procesada fué la No. $operac.\n<br>";

            if(strlen($menImp) > 0) $men .= $menImp;
            elseif(strlen($menNimp) > 0)  $men .= $menNimp;

            $texto .= $men;
//		if (strlen($men) == 0) $men .= "No hay ningún cierre pendiente";
            if (strlen($men) > 0) $sale .= "$men\n<br>";
        }

    }
    if (strlen($sale) == 0) $sale .= "No hay ningún cierre pendiente";
    if ($corCreo->todo(37,"Avisos de Cierres", $sale)) $texto .= "\n<br>Avisos de cierre enviados correctamente";
// 		echo $sale;
    /* 	Fin del cálculo de los Cierres 	*/
	}
    /*Aviso de transacción reclamada en tres meses*/
    $q = "select idtransaccion from tbl_transacciones where estado = 'R' "
                    . " and (fecha_mod >= unix_timestamp('2015-02-01 00:00:00') and fecha_mod >= ".(time()-90*24*60*60).")"
                    . " and idcomercio = {$com[1]}";
// 		echo $q;
    $temp->query($q);
    $arrRecl = $temp->loadResultArray();
// 		print_r($arrRecl);
    if (count($arrRecl) > 2) {
        //se han producido mas de tres reclamaciones en menos de 3 meses
        $corCreo->todo(45, "Aviso de 3 reclamaciones en menos de 3 meses", "El comercio {$com[2]} tiene 3 o más reclamaciones en el período de "
                ."3 meses. Las operaciones son ".implode(", ", $arrRecl));
    }

    actSetup(($fechaHoy + 60*60*24), 'fechaInf');
    actSetup($fechaHoy, 'fechaMod');
	$texto .= "\n<br>Termina recorrido de las 6pm\n<br>";
}

/**
 * Busca y pone la tasa de cambio del día
 */
if ($horaG == 14
    //    || 1==1
        ) {

	$texto .= "Entra por acá<br>";
	include_once("buscaTasa.php");
}


if ($horaG == 1
//        || 1==1
        ) {

	/**
	 * Desactiva los usuarios que no entran por primera vez al cabo de diasEntrada de creado
	 */
	$query = "update tbl_admin set activo = 'N' where fecha_visita = 0 and fecha < ".($fechaHoy - leeSetup('diasEntrada') * 86400);
	$temp->query( $query );

	/**
	 * Desactiva los usuarios que no entran desde hace mas de 2 meses y que no fueron activados hace 72 horas
	 */
	$q = "update tbl_admin set activo = 'N' where fecha_visita < ($fechaHoy-60*60*24*60) and fechaPass < ($fechaHoy-60*60*24*3) and activo = 'S'";
	$temp->query($q);

	/**
	 * Envía alerta de desactivación a los clientes que se les activó el usuario, se cambió la contraseña
	 * pero llevan menos de 72 sin volver a entrar
	 */
	$query = "select nombre, email from tbl_admin where activo = 'S' and fechaPass >= ($fechaHoy-60*60*24*3) and fecha_visita < ($fechaHoy-60*60*24*60)";
	$temp->query($query);
	$arrCli = $temp->loadRowList();

	foreach ($arrCli as $clien) {
		$corCreo->to = $clien[1];
		$imprim = '<div style=\"text-align:center; font-family:Arial san-serif; font-size:11px\">Estimado(a) '.utf8_decode($clien[0]).',<br><br>
					El Usuario con el que usted accede a <a href="https://www.administracomercios.com/admin">nuestra plataforma</a> fué activado. <br>
					Usted deberá entrar usando las nuevas credenciales que se le envió para completar el proceso de activación<br><br>
					Administración de Sistemas<br>
					Administrador de Comercios.';
		$corCreo->todo(6,'Aviso de caducidad de Credenciales '.$GLOBALS['titulo_sitio'], $imprim);
	}


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
		$nombre =	utf8_decode($temp->f('nombre'));
		$id =		$temp->f('idadmin');
		$correo =	$temp->f('email');
		$login =	$temp->f('login');
		$contras =	validaContrasena($login);
//echo $contras[0];
		$query = "update tbl_admin set md5Old = md5, fechaPass = $fechaHoy, md5 = '".$contras[1]."' where idadmin = $id";
		if (!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/')) $ini->query( $query );
//		echo "$query<br>";

		$imprim = '<div style=\"text-align:center; font-family:Arial san-serif; font-size:11px\">Estimado Cliente,<br><br>
					Por motivos de seguridad su contraseña de acceso al Panel de Administración del Administrador de Comercios<br>
					ha sido cambiada. Usted puede cambiar la misma antes de que vuelva a caducar automáticamente cada '.leeSetup('mesesContrs').' días.<br><br>
					Los nuevos datos de acceso son:<br>
					Usuario: '.$login.'<br>
					Contraseña: '.$contras[0].'<br><br>
					Disculpe las molestias y muchas gracias por preferirnos.<br><br>
					Administración de Sistemas<br>
					Administrador de Comercios.';
//echo "$imprim<br>";exit;

		$corCreo->to = $correo;
		$corCreo->set_message($imprim);
		$corCreo->set_subject('Cambio de acceso a la Administración '.$GLOBALS['titulo_sitio']);
		$corCreo->envia(6);
//         $corCreo->todo(6,'Cambio de acceso a la Administración '.$GLOBALS['titulo_sitio'], $imprim);
        $texto .= "\n<br>Envía contraseña a $to";

//		if (!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/')) mail($to, $subject, $imprim, $headers);
	}

	/**
	*Da por terminado los tickets de mas de tres días sin  modificar
	*/
	$query = "update tbl_ticket set fechaTerminada = ".$ahora.", estado = 'T' where fechaModificada < ".($ahora - leeSetup('venceTicket') * 86400);
	$temp->query($query);

	/**
	 * Borra lo que est? en la cesta que tenga + de venceInvitacionPago
	 */
	// $query = "select id, idProd, cant, fechaIni, fechaFin from tbl_productosReserv where fecha < ".($ahora - leeSetup('venceInvitacionPago') * 86400);
	// $ini->query($query);
//	echo $query."<br>";

// 	while ($ini->next_record()) {
// 		$prod = $ini->f('idProd');
// 		$fecha2 = $ini->f('fechaFin');
// 		$fecha = $fecha1 = $ini->f('fechaIni');
// 		$cant = $ini->f('cant');
// 		$id = $ini->f('id');
// 		$cantCheq = $idCant = 0;
// 		$paso = false;
// 		while ($fecha <= $fecha2) {
// 			$query = "select id, cant from tbl_productosCant where idProd = ".$prod." and fechaIni <= $fecha and fechaFin >= $fecha";
// //				echo $query."<br>";
// 			$temp->query($query);
// 			$cantObt = $temp->f('cant');
// 			$idObt = $temp->f('id');
// 			if ($cantCheq == 0) $cantCheq = $cantObt;
// 			if ($cantCheq != $cantObt) {
// //echo "$prod, $fecha1, ($fecha2-86400), ($cantCheq-$cant)<br>";
// 				if (insertaCantidad($prod,$fecha1,$fecha-86400,$cantCheq+$cant)) $paso = true;
// 				$fecha1 = $fecha;
// 				$cantCheq = $cantObt;
// 			}
// 			$fecha += 86400;
// 		}
// 		if (insertaCantidad($prod,$fecha1,$fecha-86400,$cantCheq+$cant)) $paso = true;
// //echo "segundo: $prod, $fecha1, ".($fecha2-86400).", ".($cantCheq-$cant)."<br>";
// 		if ($paso) {
// 			$query = "delete from tbl_productosReserv where id = $id";
// 			$temp->query($query);
// //			echo $query."<br>";
// 		}
// 	}

	/**
	 * Envía correo a los administradores de los comercios avis?ndole que tienen transacciones a punto de vencer (12 horas del plazo)
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
					Administrador de Comercios.";

        $corCreo->set_subject('Aviso de vencimiento de Invitaciones de Pago');
        $corCreo->set_message($message);

		foreach ($arrayTo as $todale) {
            if (!strlen($corCreo->to))
                $corCreo->to = $todale[1];
            else
                $corCreo->set_headers ("Cc: ". $todale[1]);
		}
		$ini->next_record();
        $corCreo->envia(7);
	}

	/**
	 * Borra las invitaciones para el pago que tengan + de venceInvitacionPago
	 */
	$query = "update tbl_reserva set estado = 'C' where fecha < (unix_timestamp() - tiempoV * 86400) and estado in ('P')";
	$ini->query($query);
	//echo $query."<br>";


}

/**
 * Listado de IPs con más de 15 accesos al Concentrador sin envío de datos en el mes anterior
 */
// $mes = explode('/',leeSetup('fechaMes'));
// $mes = $mes[1];
// if (($mes * 1) < (date('m') * 1)) {

// 	$q = "select ip, count(*) as veces from tbl_listaIp where fecha >= ".mktime(0, 0, 0, date("m")-1, 1, date("Y"))." group by ip order by veces desc";
// 	$temp->query($q);
// 	$listado = '';
//     $cant = $temp->num_rows();

// 	for ($i=0; $i < $temp->num_rows(); $i++) {
// 		if ($temp->f('veces') > 15) {
// 			if (strlen($listado) > 0) $listado .= ', ';
// 			$listado .= $temp->f("ip");
// 		} else break;
// 		$temp->next_record();
// 	}

//     if ($cant > 0) {
//         $imprim = 'Este es el listado de las IPs con más de 15 accesos al Concentrador sin envío de datos en el mes anterior:'."\n<br>\n<br>".$listado
//                 ."\n<br>\n<br>Nota: Eliminar las IPs de Cuba para no cerrarnos nosotros mismos\n<br>";
//     //echo "$imprim<br>";

//         $corCreo->set_subject('Listado IPs con mas de 15 accesos al concentrador');
//         $corCreo->set_message($imprim);
//         $corCreo->envia(8);
//         $texto .= "\n$subject";
//     }
// 	actSetup("1/".date('m')."/".date('Y'), 'fechaMes');
// }

/**
 * Dispara la confirmación de orden de Titanes
 *
 */
if ($horaG == 14
//        || 1==1
        ) {
//	envia a titanes los ficheros de las Aceptadas del día anterior
	$texto .= "Envia a titanes los ficheros de las Aceptadas del día anterior si hay alguno\n<br>";
	include_once 'acepTitanesm.php';
}

/**
 * Si es la una de la mañana de los primeros días de cada trimestre
 * borro los ids que estén en la tabla setup de clientePerm y benefPerm
 * para comenzar el Trimestre sin Clientes ni Beneficiarios que puedan enviar
 * o recibir por encima de 3000.00
 */
if (
	date('dmH') == '010101'
	|| date('dmH') == '010401'
	|| date('dmH') == '010701'
	|| date('dmH') == '011001'
	) {
		$texto .= "Entra a borrar en la tabla Setup los Clientes y Beneficiarios puestos en el trimestre anterior";
		actSetup('', 'clientePerm');
		actSetup('', 'benefPerm');
}



//revisa el FTP de Ais
$q = "select usuario from tbl_aisCliente where idtitanes is not null and subfichero = 1 and fecha > ".(time()-(12*60*60));
$temp->query($q);
$arrDir = $temp->loadRowList();

$verd = '';
$ftp_serverAis = '82.223.110.245'; 
$ftp_user_nameAis = 'www';
$ftp_user_passAis = 'A1sr3m3s4s*';
$conn_id = ftp_connect($ftp_serverAis);
if (!ftp_login($conn_id, $ftp_user_nameAis, $ftp_user_passAis)) {
	$ver .= '<br>El FTP de Tocopay no está trabajando<br>';
} else {
	foreach ($arrDir as $ftp_dirAis) {
		if (!ftp_chdir($conn_id, "/".$ftp_dirAis[0])){
			$verd .= '<br>La carpeta del usuario '.$ftp_dirAis[0]." no fué encontrada.<br><br>";
		}
	}
	if(strlen($verd) > 3) $corCreo->todo(3, "Ftp Tocopay no existen las carpetas", $verd);
}

ftp_close($conn_id);

if (strlen($ver) > 3) {
	$corCreo->todo(3, "Alerta problemas con los servidores", $ver);
}

/**
 * Bloquea la ip del proxy de Gaviota
 */
$q = "select count(*) total from tbl_ipBL where ip = '3.121.188.123'";
$temp->query($q);
echo $temp->f('total');
if ($temp->f('total') == 0) {
	$q = "insert into tbl_ipBL values (null, '3.121.188.123', '', '9000', unix_timestamp())";
	$temp->query($q);

	sendTelegram("Bloqueada la IP de Gaviota -> 3.121.188.123",null);
}




// function rates ($denom) {

//     $url = "http://download.finance.yahoo.com/d/quotes.csv?s=EUR$denom=X&f=sl1d1t1ba&e=.csv";
//     $chx = curl_init($url);
//     curl_setopt($chx, CURLOPT_POST, false);
//     curl_setopt($chx, CURLOPT_HEADER, false);
//     curl_setopt($chx, CURLOPT_RETURNTRANSFER, true);
//     $sale = curl_exec($chx);
//     curl_close($chx);

// 	$arrEx = explode(',', $sale);
// 	return $arrEx[1];
// }

// function ratesXe ($denom) {
// 	include_once 'admin/classes/class.Html.php';
// 	$resultad = '';

// 	$url = "http://www.xe.com/ucc/convert.cgi?template=pca-new&Amount=1&From=EUR&To=$denom&image.x=36&image.y=11&image=Submit";
// //	$url = 'http://localhost/XE.com%20-%20Personal%20Currency%20Assistant'.$denom.'.htm';

// 	$objHtmlParser = new Html($url);

// 	$description = '';
// 	$objHtmlParser->Clean();

// 	$objHtmlParser->Parse($description);

// 	$all_tags = array();
// 	$objHtmlParser->FindAllTags($objHtmlParser->tree,$all_tags);
// 	if (count($all_tags) > 1) {
// 		$resultad = $all_tags['span'][3]['text'];
// 		if (strlen($resultad) > 6) {
// 			$resultad = str_replace('1 EUR = ', '', $resultad);
// 			$resultad = str_replace(' '.$denom, '', $resultad);
// 		}
// 	}
// 	return $resultad;
// }

/**
 * Función que parsea las páginas de los bancos
 * @param text $url
 * @return array
 */
// function parser($url) {
// 	include_once 'admin/classes/class.Html.php';
// 	$objHtmlParser = new Html($url);
// 	$description = '';
// 	$objHtmlParser->Clean();
// 	$objHtmlParser->Parse($description);
// 	$all_tags = array();
// 	$objHtmlParser->FindAllTags($objHtmlParser->tree,$all_tags);
// 	return $all_tags;
// }

$corCreo->set_message($texto."\n<br>Ejecutado satisfactoriamente a las ".date('d/m/Y H:i:s'));
$corCreo->set_subject("Ejecución del Cron");
$corCreo->envia(5);
// echo "ok<br>".$texto;
?>
