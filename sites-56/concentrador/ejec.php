<?php define( '_VALID_ENTRADA', 1 );
error_log(json_encode($_REQUEST));
error_log($_SERVER['REMOTE_ADDR']);
//if ($_SERVER['REMOTE_ADDR'] != '217.160.140.131' && $_SERVER['REMOTE_ADDR'] != '152.206.69.166' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') die ("acceso prohibido");

//echo(json_encode($_REQUEST));

require_once( 'configuration.php' );
require_once 'include/mysqli.php';
$temp = new ps_DB;
require_once 'include/correo.php';
$correo = new correo();


$d = $_REQUEST;
//error_log(($_REQUEST));


$var = $d['var'];
$cod = $d['cod'];
$sql = remueve($d['sql']);
$rep = $d['rep'];


if (strripos($sql, 'insert') > -1 || stripos($sql, 'update') > -1) {
	$correo->todo(2, 'Envío de insert o update al concentrador '.date('d/m/y H:i:s'), 'Se envió la siguiente querysss<br>'.$sql);
	error_log($sql);
}

//error_log("var=".$var);
//error_log("cod=".$cod);
//error_log("sql=".$sql);
//error_log("rep=".$rep);
//error_log("fucn=".$d['fucn']);

//error_log(" ");

$difHora = 6;

if ($d['fucn'] == 'cargaAdminSO'){
	$sql = "select from_unixtime(o.fecha, '%d/%m/%Y %H:%i'), o.so, o.browser, o.ip, p.nombre, o.id from tbl_adminSO o, tbl_paises p where o.idpais = p.id and o.idadmin = ".$d['id']." order by o.fecha desc limit 0,10";
	$temp->query($sql);
	$arrEnt = $temp->loadRowList();

	$tab = '';
	for ($i=0; $i<count($arrEnt); $i++){
		$tab .= "<tr class='".$arrEnt[$i][5]." nmark'><td>".$arrEnt[$i][0]."</td><td>".$arrEnt[$i][1]."</td><td>".$arrEnt[$i][2]."</td><td>".$arrEnt[$i][3]."</td><td>".$arrEnt[$i][4]."</td></tr>";
	}

	$salida = "<table border='0' width='65%' align='center' id='tbEntr' ><tr><th>Fecha Hora</th><th>SO</th><th>Navegador</th><th>IP</th><th>País</th></tr>$tab</table>";

	error_log($salida);
	
	echo json_encode(array("salida" => utf8_encode($salida)));

} elseif ($d['fucn'] == 'entrada'){
	$temp->query(sprintf("select ip, idadmin from tbl_adminSO where id = %u",$d['elem']));
	if ($temp->num_rows() == 1) {
		$ip = $temp->f('ip');
		$ida = $temp->f('idadmin');

		//borro la ip si estaba en la tbl_ipblancas
		$temp->query("delete from tbl_ipblancas where ip = '$ip'");
		// borro la entradaen tbl_adminSO
		$temp->query("delete from tbl_adminSO where ip = '$ip' and idadmin = $ida");

		//busco si la ip estaba en la tbl_ipBL
		$temp->query("select cuenta from tbl_ipBL where ip = '$ip'");
		if ($temp->num_rows() == 0) {// si no aparece lo inserto con cuenta = 4 para que se bloquee
			$temp->query("insert into tbl_ipBL (ip, cuenta, fecha) values ('$ip', '4', '".time()."')");
		} else //si está le pongo cuenta = 4
			$temp->query("update tbl_ipBL set cuenta = '4', fecha = '".time()."' where ip = '$ip'");

	}
	echo json_encode(array("salida" => 'La IP ha sido bloqeada y la entrada se ha borrado.', 'sql'=>'va'));

} elseif ($d['fucn'] == 1){
	$inf = 0;
	if (stripos($sql,'dia=') > -1) {
		$sql = informe(str_replace("}","",str_replace("{dia=","",$sql)));
		// error_log("VERI = ".$sql);
		$inf = 1;
	}
		echo json_encode(array("salida" => firm($var, $sql), "sql" => $sql, 'inf' => $inf));
	exit;
} elseif ($d['fucn'] == 2) {
	if (stripos($sql,'dia=') > -1) {
		$sql = informe(str_replace("}","",str_replace("{dia=","",$sql)));
		// error_log($sql);
	}
	echo json_encode(array("salida" => '<style>table {font-size: 8px;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;}.verde{color:green;}.roja{color:red;}.violeta{color:violet;}.carmelita{color:brown;}.azul{color:blue;}.azulo{color:#002752;}}</style>'.utf8_encode(ejecuta($cod, $var, $sql))));
} else {
	$salida = '<head><title>Ejecuci&oacute;n de query</title><meta http-equiv="Content-Type" content="text/html;charset=utf-8"><style>body {font-size: 8px;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;margin-left: 0px;margin-top: 0px;background-color:#23232d;color:#fff38f;}.verde{color:green;}.roja{color:red;}.violeta{color:violet;}.carmelita{color:brown;}.azul{color:blue;}.azulo{color:#002752;}</style></head>';
	
	echo ejecuta($cod, $var, $sql, $salida);
}

function firm($var,$sql) {
	return sha1($var.$sql.'Lo que los hace hermoso es algo invisible...los ojos no siempre ven. Hay que buscar con el corazon.');
}


function informe($diaentr) {
	$var = 'diario';
	$dia = substr($diaentr,0,2);
	$mes = substr($diaentr,2,2);
	$ano = substr($diaentr,4,2);

	$fecha30Dante = mktime(0, 0, 0, $mes, $dia-30, '20'.$ano);
	$difHora = 6;
	$text = '% -30d';
	$varMes = 0;

	$diasMesAct = date("t", strtotime('20'.$ano . "-" . $mes . "-01"));
	$iniMes = mktime(0,0,0,$mes,1,'20'.$ano);
	$Mesatras = mktime(0,0,0,$mes,$dia-30,'20'.$ano);
	$iniDia = mktime(0,0,0,$mes,$dia,'20'.$ano);
	$finiDia = mktime(0,0,0,$mes,$dia+1,'20'.$ano);
	$elem = "case t.estado  when 'B' then (t.valor_inicial/100/t.tasa) + ((t.valor_inicial-t.valor)/100/t.tasaDev) when 'V' then (t.valor_inicial/100/t.tasa) + ((t.valor_inicial-t.valor)/100/t.tasaDev) when 'R' then (t.valor_inicial/100/t.tasa) + ((t.valor_inicial-t.valor)/100/t.tasaDev) when 'A' then (t.valor/100/t.tasa) else 0.0 end ";
	// $iniSemana = mktime(0,0,0,$mes,$dia-7,'20'.$ano);
	// $ini13A = mktime(0,0,0,$mes-13,1,'20'.$ano);
	// $iniAno = mktime(0,0,0,1,1,'20'.$ano);
	// $ini24h = mktime(0,0,0,$mes,$dia-1,'20'.$ano);
	// $horCorr = time()-$difHora*60*60;
	// $hor3pm = mktime(15,0,0,$mes,$dia,'20'.$ano);
	// $hoy = $dia.'/'.$mes.'/'.'20'.$ano;
	// $estaHora = $dia.'/'.$mes.'/'.'20'.$ano." ".date('H');
	// $ahora = time();
	// $hora1Ant = time()-(60*60*1);
	// $hora2Ant = time()-(60*60*2);

	return "select formateaO(sum($elem),2,10) Acumulado, formateaO(sum($elem) / ".date('j')." * $diasMesAct, 2,10) 'Estimado', formateaO(sum($elem) / ".date('j').", 2,10) 'Promedio' FROM tbl_transacciones t where estado in ('A') and tipoEntorno = 'P' and fecha_mod > $iniMes; select from_unixtime( t.fecha_mod, '%d/%m/%y - %W' ) as 'Día', formateaO(sum($elem),2,10) 'Valor', count(t.idtransaccion) 'Transacc Acep', formateaO(sum($elem) / count(t.idtransaccion), 2,10) 'val/trans', (select count(i.idtransaccion) from tbl_transacciones i where from_unixtime(t.fecha_mod,'%d%m%Y') = from_unixtime(i.fecha_mod,'%d%m%Y') and i.fecha_mod between ($iniDia-(86400*1)) and $finiDia and i.tipoEntorno = 'P') 'Tot trans', formateaO(count(t.idtransaccion)/(select count(i.idtransaccion) from tbl_transacciones i where from_unixtime(t.fecha_mod,'%d%m%Y') = from_unixtime(i.fecha_mod,'%d%m%Y') and i.fecha_mod between ($iniDia-(86400*1)) and $finiDia and i.tipoEntorno = 'P')*100,2,10) '% Acep' FROM tbl_transacciones t where t.estado in ('A') and t.tipoEntorno = 'P' and t.fecha_mod between ($iniDia-(86400*1)) and $finiDia GROUP BY from_unixtime( t.fecha_mod, '%d/%m/%y' ) order by t.fecha_mod desc; select case t.estado when 'P' then 'En Proceso' when 'A' then 'Aceptada' when 'D' then 'Denegada' when 'N' then 'No Procesada' when 'B' then 'Anulada' when 'V' then 'Devuelta' when 'R' then 'Reclamada' else '' end estad, count(t.idtransaccion) cant, formateaO((count(t.idtransaccion)*100/(select count(r.idtransaccion) from tbl_transacciones r where r.fecha_mod > '$iniDia' and r.tipoEntorno = 'P')),1,10) '%', formateaO((select count(n.idtransaccion) from tbl_transacciones n where n.estado = t.estado and t.tipoEntorno = 'P' and n.fecha_mod > '$Mesatras')*100/(select count(s.idtransaccion) from tbl_transacciones s  where s.tipoEntorno = 'P' and s.fecha_mod > '$Mesatras'),2,10) '% - 30' from tbl_transacciones t where t.tipoEntorno = 'P' and t.fecha_mod between '$iniDia' and '$finiDia' group by case t.estado when 'P' then 'P' when 'A' then 'A' when 'D' then 'D' when 'N' then 'N' when 'B' then 'B' when 'V' then 'V' when 'R' then 'R' end; select concat(p.nombre,' (',p.idPasarela, ') ') pasarela, case LimDiar when '100000000' then 'S/L' else formateaO(LimDiar,2,10) end 'Lim Diario', formateaO(sum($elem),2,10) 'Acum Dia', case LimMens when '100000000' then 'S/L' else formateaO(LimMens,2,10) end 'Lim Mensual', (select formateaO(sum(case n.estado when 'B' then if (n.fecha_mod < n.fechaPagada, (-1 * ((n.valor_inicial-n.valor)/100/n.tasa)), n.valor/100/n.tasa) when 'V' then if (n.fecha_mod < n.fechaPagada, (-1 * ((n.valor_inicial-n.valor)/100/n.tasa)), n.valor/100/n.tasa) when 'R' then if (n.fecha_mod < n.fechaPagada, (-1 * ((n.valor_inicial-n.valor)/100/n.tasa)), n.valor/100/n.tasa) when 'A' then (n.valor/100/n.tasa) else '0.00' end),2,10) from tbl_transacciones n where t.pasarela = n.pasarela and from_unixtime(n.fecha_mod, '%m%Y')='".date('mY')."')  'Acum Men', count(t.idtransaccion) cant, formateaO((count(t.idtransaccion)*100/(select count(r.idtransaccion) from tbl_transacciones r where r.pasarela = t.pasarela and r.estado != 'P' and r.fecha_mod between '$iniDia' and '$finiDia' and r.tipoEntorno = 'P')),2,10) '%', formateaO((select count(n.idtransaccion) from tbl_transacciones n where n.estado = 'A' and t.pasarela = n.pasarela and t.tipoEntorno = 'P' and n.fecha_mod > '$Mesatras')*100/(select count(s.idtransaccion) from tbl_transacciones s where t.pasarela = s.pasarela and s.tipoEntorno = 'P' and s.estado != 'P' and s.fecha_mod > '$Mesatras'),2,10) '% - 30' from tbl_transacciones t, tbl_pasarela p where t.estado = 'A' and t.pasarela = idPasarela and t.tipoEntorno = 'P' and t.fecha_mod between '$iniDia' and '$finiDia' group by p.nombre order by p.nombre; select concat(c.nombre,' ',c.id,'-',t.idcomercio) comercio, formateaO(sum($elem),2,10) 'Valor', count(t.idtransaccion) Transacc, formateaO(sum($elem) / count(*), 2,10) 'val/trans', formateaO(count(t.idtransaccion) *100/(select count(n.idtransaccion) from tbl_transacciones n where n.tipoEntorno = 'P' and n.fecha_mod between '$iniDia' and '$finiDia' and t.idcomercio = n.idcomercio),2,10) '% Acep', formateaO((select count(j.idtransaccion ) from tbl_transacciones j where j.idcomercio = t.idcomercio and j.tipoEntorno = 'P' and j.estado in ('A') and fecha_mod > '$Mesatras' ) *100/(select count(i.idtransaccion) from tbl_transacciones i where i.tipoEntorno = 'P' and i.fecha_mod > '$Mesatras' and t.idcomercio = i.idcomercio),2,10) '% -30' FROM tbl_comercio c, tbl_transacciones t where t.idcomercio = c.idcomercio and t.estado in ('A') and t.tipoEntorno = 'P' and fecha_mod between '$iniDia' and '$finiDia' group by t.idcomercio order by sum(case t.estado when 'A' then euroEquiv else euroEquivDev end) desc;";
}


function ejecuta($cod,$var,$sql,$salida='') {
	$temp = new ps_DB;
	$sale = $salida;
//	error_log("$cod == ".firm($var,$sql));
	if ($cod == firm($var,$sql)
	// 	|| 1==1
			){
		$arrSql = explode(';', $sql);
		$sale .= $_SERVER['REMOTE_ADDR']."<br>";
		$sale .= date('d/m/Y H:i:s');
		date_default_timezone_set('America/Havana');
		$sale .= " - ".date('H:i:s')."<br><br>";
		date_default_timezone_set('Europe/Madrid');
		for ($i=0; $i<count($arrSql); $i++){
			if (stripos($arrSql[$i], 'select ') > -1) {
				$sale .= renueva($arrSql[$i]);
			} else {
				$temp->query($arrSql[$i]);
			}
		}
		
		return $sale;
	}	
}

function renueva($sql){
	$sale = "";
	$temp = new ps_DB;
	$difHora = 6;

	$valores = array(
		'diasMesAct' => date("t", strtotime(date("Y") . "-" . date("m") . "-01")),
		'iniMes' => mktime(0,0,0,date('m'),1,date('Y')),
		'iniSemana' => mktime(0,0,0,date('m'),date('d')-7,date('Y')),
		'Mesatras' => mktime(0,0,0,date('m'),date('d')-30,date('Y')),
		'iniDia' => mktime(0,0,0,date('m'),date('d'),date('Y')),
		'ini13A' => mktime(0,0,0,date('m')-13,1,date('Y')),
		'iniAno' => mktime(0,0,0,1,1,date('Y')),
		'ini24h' => mktime(0,0,0,date('m'),date('d')-1,date('Y')),
		'horCorr' => time()-$difHora*60*60,
		'hor3pm' => mktime(15,0,0,date('m'),date('d'),date('Y')),
		'hoy' => date('d').'/'.date('m').'/'.date('Y'),
		'estaHora' => date('d').'/'.date('m').'/'.date('Y')." ".date('H'),
		'ahora' => time(),
		'hora1Ant' => time()-(60*60*1),
		'hora2Ant' => time()-(60*60*2),
//		'elem' => "case t.estado
//			when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
//			when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
//			when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
//			when 'A' then (t.valor/100/t.tasa)
//			else '0.00' end"
		'elem' => "case t.estado  
			when 'B' then (t.valor_inicial/100/t.tasa) + ((t.valor_inicial-t.valor)/100/t.tasaDev)
			when 'V' then (t.valor_inicial/100/t.tasa) + ((t.valor_inicial-t.valor)/100/t.tasaDev)
			when 'R' then (t.valor_inicial/100/t.tasa) + ((t.valor_inicial-t.valor)/100/t.tasaDev)
			when 'A' then (t.valor/100/t.tasa) else 0.0 end "
	);
	//error_log('va asi');
	foreach ($valores as $key => $value) {
		$sql = str_replace('{'.$key.'}', $value, $sql);
	}

	// error_log("VAYAAAA" . $sql);

	$temp->query($sql);
	if ($temp->getErrorMsg()) $sale .= $temp->getErrorMsg();
	
	$cant = $temp->num_rows();

	$sale .= "<br>Records: $cant";
	$sale .= "<table cellpadding=5 cellspacing=0 border=1 style='font-size:10px;'>";
	$rows = $temp->loadAssocList();
	//			print_r($rows);
	$sale .= "<tr>";
	foreach($rows[0] as $key => $value) {
		$sale .= "<th>$key</th>";
		if ($key == 'ip') $sale .= "<th>país</th>";
	}
	$sale .= "</tr>";
	foreach ($rows as $row) {
		$texto = implode($row);
//		error_log($texto);
		if (strpos($texto, 'En Proceso')) $sale .= "<tr class='verde'>";
		elseif (strpos($texto, 'Denegada')) $sale .= "<tr class='roja'>";
		elseif (strpos($texto, 'No Procesada')) $sale .= "<tr class='violeta'>";
		elseif (strpos($texto, 'Reclamada')) $sale .= "<tr class='carmelita'>";
		elseif (strpos($texto, 'Devuelta')) $sale .= "<tr class='azul'>";
		elseif (strpos($texto, 'Anulada')) $sale .= "<tr class='azulo'>";
		else 
			$sale .= "<tr>";
		foreach($row as $key => $data) {
			$data = str_replace('submit()', '', $data);
			$data = str_replace('width: 550px;', 'width: 550px;display:none;', $data);
			$data = str_replace('<script', '<scr|', $data);
			$data = str_replace('<!--', '', $data);
			$data = str_replace('//-->', '', $data);
			$data = str_replace('-->', '', $data);
			$sale .= "<td>".$data."</td>";$sale .= "";
			if ($key == 'ip') if( function_exists("geoip_country_name_by_name")) $sale .= "<td>".geoip_country_name_by_name($data)."</td>";else $sale .= "<td>".$data."</td>"; $sale .= "";
		}
		$sale .= "</tr>";
	}
	$sale .= "</table>";
	return $sale;
}

function remueve($sql) {
	$sql = str_replace("\n", " ", $sql);
	$sql = str_replace("\n\r", " ", $sql);
	$sql = str_replace("\r", " ", $sql);
	$sql = str_replace("	", "", $sql);
	return $sql;
}
	
if ($rep > 0) {
?>
<form method="post" name="envia" action="" >
	<input type="hidden" name="var" value="<?php echo $var; ?>" />
	<input type="hidden" name="sql" value="<?php echo $sql; ?>" />
	<input type="hidden" name="cod" value="<?php echo $cod; ?>" />
	<input type="hidden" name="rep" value="<?php echo $rep; ?>" />
	<!-- <input type="submit" value="envia" /> -->
</form>
<script language='javascript'>
	//document.envia.submit();
	var min = <?php echo $rep; ?> * 60000;
	setInterval("document.envia.submit()", min);
</script>
<?php } ?>
