<?php


define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';
include_once( 'admin/adminis.func.php' );
// require_once( 'include/hoteles.func.php' );
// require_once( 'include/correo.php' );

$temp = new ps_DB;

// echo date('w')."<br>";
// echo date('w',1640614551)."<br>";

// foreach ($_SERVER as $value => $item) {
//  echo $value . "=" . $item . "<br>";
// }
// echo "<br><br>";

// $test_HTTP_proxy_headers = array(
// 	'HTTP_VIA',
// 	'VIA', 	
// 	'Proxy-Connection', 	
// 	'HTTP_X_FORWARDED_FOR',
// 	'HTTP_FORWARDED_FOR',
// 	'HTTP_X_FORWARDED',
// 	'HTTP_FORWARDED',
// 	'HTTP_CLIENT_IP',
// 	'HTTP_FORWARDED_FOR_IP',
// 	'X-PROXY-ID',
// 	'MT-PROXY-ID',
// 	'X-TINYPROXY',
// 	'X_FORWARDED_FOR',
// 	'FORWARDED_FOR',
// 	'X_FORWARDED',
// 	'FORWARDED',
// 	'CLIENT-IP',
// 	'CLIENT_IP',
// 	'PROXY-AGENT',
// 	'HTTP_X_CLUSTER_CLIENT_IP',
// 	'FORWARDED_FOR_IP',
// 	'HTTP_PROXY_CONNECTION'
// );
// foreach($test_HTTP_proxy_headers as $header){ 		
// 	if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
// 		exit("Please disable your proxy connection!");
// 	} 	
// }

// sendTelegram('Perfecto<br>hace<br>el envío1',null,'T');

// echo date('dmy');

// echo date('j' , mktime(0,0,0,date('m')+1,0,date('Y')));
echo  date("t", strtotime(date("Y") . "-" . date("m") . "-01"));

$parameters = array(
    "chat_id" => '-1001652358529',
    "text" => 'Hola Mundo otro 3'
);

// send ('sendMessage', $parameters);

function send($method, $parameters) {
    $bot_token = "5038443834:AAHk906Tuj0zWRt1kITZtWk1XXX4vZeDnGc";
    $url = "https://api.telegram.org/bot$bot_token/$method";

    if (!$curl = curl_init()) {
        exit();
    }

    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$output = curl_exec($curl);
	echo "<br>".$output;
    return $output;
}





// $verd = '';
// $q = "select c.usuario, c.id from tbl_aisCliente c where (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) = 0 and c.borrficheros = 0 and (idtitanes is not null or idtitanes != 0) and fecha > ".(time()-(3*24*60*60));
// $verd .= $q."<br>";
// $temp->query($q);
// $arrDir = $temp->loadRowList();

// echo json_encode($arrDir)."<br>";

// $ftp_serverAis = '82.223.110.245'; 
// $ftp_user_nameAis = 'www';
// $ftp_user_passAis = 'A1sr3m3s4s*';
// $conn_id = ftp_connect($ftp_serverAis);
// if (!ftp_login($conn_id, $ftp_user_nameAis, $ftp_user_passAis)) {
// 	$ver .= '<br>El FTP de Tocopay no está trabajando<br>';
// } else {
// 	foreach ($arrDir as $ftp_dirAis) {
// 		if (!ftp_chdir($conn_id, "/".$ftp_dirAis[0])){

// 			$dirlocal = "/var/www/vhosts/administracomercios.com/httpdocs/ficTitan/".$ftp_dirAis[0];
// 			if (is_dir($dirlocal)) {

// 				$files = scandir($dirlocal, 0);
// 				if (count($files) > 2 ) {
// 					$verd .= 'Trabajando con el usuario '.$ftp_dirAis[0]."<br>";
// 					$q = "delete from tbl_aisFicheros where idcliente = ".$ftp_dirAis[1];
// 					$verd .= $q."<br>";
// 					for($i = 2; $i < count($files); $i++) {
// 						$q = "insert into tbl_aisFicheros ";
// 						$verd .= $files[$i]."<br>";
// 					}
// 				}
// 			}
// 		}
// 		$verd .= "<br>";
// 	}
// 	//if(strlen($verd) > 3) $corCreo->todo(3, "Ftp Ais no existen las carpetas", $verd);
// }
// echo "<br>$verd";


// $q = "select idcimex from tbl_aisCliente c where c.idtitanes = '' or c.idtitanes is null or c.idtitanes = 0 limit 0,10";
// $temp->query($q);
// $arrId = $temp->loadResultArray();

// for ($i = 0; $i < count($arrId); $i++) {
// 	$q = "select distinct c.nombre, c.papellido, c.sapellido, c.usuario, from_unixtime(c.fnacimiento, '%d/%m/%Y') fnac, c.numDocumento, from_unixtime(c.fechaDocumento, '%d/%m/%Y') fdoc, c.correo, c.telf1, p1.iso2 pr, c.provincia, c.ciudad, c.direccion, c.CP, p2.iso2 pn, (c.sexo-1) sexo, c.ocupacion, c.salariomensual from tbl_aisCliente c, tbl_paises p1, tbl_paises p2 where c.paisResidencia = p1.id and c.paisNacimiento = p2.id and c.idcimex = ".$arrId[$i];
// 	$temp->query($q);
// 	$arrCli = $temp->loadAssocList();
// 	echo json_encode($arrCli);

// 	$url = "https://www.administracomercios.com/datInscr.php";
// 	// $url = "http://localhost:8080/datInscr.php";
	
// 				$options = array(
// 						CURLOPT_RETURNTRANSFER	=> true,
// 						CURLOPT_SSL_VERIFYPEER	=> false,
// 						CURLOPT_POST			=> true,
// 						CURLOPT_VERBOSE			=> true,
// 						CURLOPT_URL				=> $url,
// 						CURLOPT_POSTFIELDS		=> $arrCli
// 				);
// 				echo "<br><br>
// 					CURLOPT_RETURNTRANSFER	=> true,<br>
// 					CURLOPT_SSL_VERIFYPEER	=> false,<br>
// 					CURLOPT_POST			=> true,<br>
// 					CURLOPT_VERBOSE			=> true,<br>
// 					CURLOPT_URL				=> ".$url.",
// 					CURLOPT_POSTFIELDS		=> ".http_build_query($arrCli)."<br>";
// 				$ch = curl_init();
// 				curl_setopt_array($ch , $options);
// 				$output = curl_exec($ch);
// 	// 						echo "error=".curl_errno($ch);
// 				if (curl_errno($ch)) echo   "Error en la comunicación al comercio:".curl_strerror(curl_errno($ch))."<br>\n";
// 				$crlerror = curl_error($ch);
// 	// 						echo "otroerror=".$crlerror;
// 				if ($crlerror) {
// 					echo "La comunicación al comercio ha dado error:".$crlerror."<br>\n";
// 				}
// 				$curl_info = curl_getinfo($ch);
// 				curl_close($ch);
// }
?>

<!-- Start of Async Callbell Code -->

<!-- <script>

  window.callbellSettings = {

    token: "3YukmfahbcuRtYJT5yuD3Z7S"

  };

</script>

<script>

  (function(){var w=window;var ic=w.callbell;if(typeof ic==="function"){ic('reattach_activator');ic('update',callbellSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Callbell=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://dash.callbell.eu/include/'+window.callbellSettings.token+'.js';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()

</script> -->

<!-- End of Async Callbell Code -->