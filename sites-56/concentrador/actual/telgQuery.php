<?php define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' );
require_once 'include/mysqli.php';
require_once( 'include/correo.php' );
$temp = new ps_DB;
$corCreo = new correo();


$valores= file_get_contents('php://input');

/****************************************** */
// $valores = '{"update_id":536360392,"message":{"message_id":38,"from":{"id":707807600,"is_bot":false,"first_name":"Jtoi","username":"jtoirac","language_code":"es"},"chat":{"id":707807600,"first_name":"Jtoi","username":"jtoirac","type":"private"},"date":1657304916,"text":"/diario"}}';
/****************************************** */

$q = "insert into tbl_traza (titulo, traza, fecha) values ('Desde Telegram', '$valores', unix_timestamp())";
$temp->query($q);

$arrVals = json_decode($valores);

// evMesTele($valores);

if ($arrVals->message->chat->id == '707807600' && $arrVals->message->chat->username == "jtoirac") {
	$q = "select distinct comando from tbl_telgQuer";
	$temp->query($q);
	$arrCom = $temp->loadResultArray();

	$arrText = explode(' ', $arrVals->message->text);
	$command = $arrText[0];
	$value = $arrText[1];

	if (!in_array($command, $arrCom, true)) exit;


	$q = "select query from tbl_telgQuer where comando = '$command'";
	$temp->query($q);
	$arrQuer = $temp->loadResultArray();

	// echo $arrQuer[0];
	// exit;
	$i=1;
	foreach ($arrQuer as $query) {
		$mens .= (renueva($query, $arrText[$i++]));
	} 

	evMesTele($mens);
	

	
} else exit;

function renueva($sql, $valor=null){
	$sale = "";
	$temp = new ps_DB;
	$difHora = 6;

	$valores = array(
		'diasMesAct' => date("t", strtotime(date("Y") . "-" . date("m") . "-01")),
		'iniMes' => mktime(0,0,0,date('m'),1,date('Y')),
		// 'iniMes' => '1604205219',
		'iniMesDia' => date('j'),
		'iniSemana' => mktime(0,0,0,date('m'),date('d')-7,date('Y')),
		'Mesatras' => mktime(0,0,0,date('m'),date('d')-30,date('Y')),
		'iniDia' => mktime(0,0,0,date('m'),date('d'),date('Y')),
		// 'iniDia' => '1604554312',
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
		'valor' => $valor,
		// 'elem' => "case t.estado  
		// 	when 'B' then (t.valor_inicial/100/t.tasa) + ((t.valor_inicial-t.valor)/100/t.tasaDev)
		// 	when 'V' then (t.valor_inicial/100/t.tasa) + ((t.valor_inicial-t.valor)/100/t.tasaDev)
		// 	when 'R' then (t.valor_inicial/100/t.tasa) + ((t.valor_inicial-t.valor)/100/t.tasaDev)
		// 	when 'A' then (t.valor/100/t.tasa) else 0.0 end ",
		'elem' => "t.valor_Inicial/100/t.tasa"
	);
	
	foreach ($valores as $key => $value) {
		$sql = str_replace('{'.$key.'}', $value, $sql);
	}

	$temp->query($sql);
	if ($temp->getErrorMsg()) $sale .= $temp->getErrorMsg();
	// echo "$sql<br>";
	
	$cant = $temp->num_rows();
	$rows = $temp->loadAssocList();

	$sale .= "Records: $cant<br><br>";
		
	foreach($rows[0] as $key => $value) {
		$sale .= "   |   $key";
		if ($key == 'ip') $sale .= "-país";
	}
	$sale .= "<br>";
	
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
			$sale .= "   ";
		foreach($row as $key => $data) {
			$data = str_replace('submit()', '', $data);
			$data = str_replace('width: 550px;', 'width: 550px;display:none;', $data);
			$data = str_replace('<script', '<scr|', $data);
			$data = str_replace('<!--', '', $data);
			$data = str_replace('//-->', '', $data);
			$data = str_replace('-->', '', $data);
			$sale .= "|   ".$data."    ";$sale .= "";
			if ($key == 'ip') if( function_exists("geoip_country_name_by_name")) $sale .= "<td>".geoip_country_name_by_name($data)."</td>";else $sale .= "<td>".$data."</td>"; $sale .= "";
		}
		$sale .= "<br>";
	}
	$sale .= "<br><br>";
	// echo $sale;
	return $sale;
}

function remueve($sql) {
	$sql = str_replace("\n", " ", $sql);
	$sql = str_replace("\n\r", " ", $sql);
	$sql = str_replace("\r", " ", $sql);
	$sql = str_replace("	", "", $sql);
	return $sql;
}

function evMesTele($mensaje){
	$bot_token = "5489700071:AAEpUEBpMw_SrZt-mUO0W5uJjiilELaUXrM"; //envío al otro
	$id="707807600";
	$url = "https://api.telegram.org/bot$bot_token/sendMessage";

	// if (!$curl = curl_init()) {
	// 	exit();
	// }

	$parameters = array(
		"chat_id" => $id,
		"parse_mode" => 'html',
		"text" => utf8_encode(str_replace("<br>", "\n", "<br>.$mensaje"))
	);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$output = curl_exec($curl);
	curl_close($curl);
	return;
}
?>