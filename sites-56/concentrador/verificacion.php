<?php
define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/correo.php' );
require_once( 'admin/adminis.func.php' );
$temp = new ps_DB();
$corCreo = new correo();

envioSMS('1', "Hola mundo");

// include "include/Esendex/autoload.php";

// $message = new \Esendex\Model\DispatchMessage(
//     "Bidaiondo", // Send from
//     "5352738723", // Send to any valid number
//     "My Web App is SMS posible!",
//     \Esendex\Model\Message::SmsType
// );
// $authentication = new \Esendex\Authentication\LoginAuthentication(
//     "EX0304912", // Your Esendex Account Reference
//     "serv.tecnico@bidaiondo.com", // Your login email address
//     "Bidaiondo#50" // Your password
// );
// $service = new \Esendex\DispatchService($authentication);
// $result = $service->send($message);

// print $result->id();
// echo "<br><br>";
// print $result->uri();
// echo "Final";

// require "include/Twilio/autoload.php";
// use Twilio\Rest\Client;

// $account_sid = 'AC0d42a6080e43dd83ad95b90284c4c5b3';
// $auth_token = 'da1357437942a34f321e4ec2c68bebfe';

// $twilio_number = "+15017122661";

// $client = new Client($account_sid, $auth_token);
// $client->messages->create(
//     // Where to send a text message (your cell phone?)
//     '+5352738723',
//     array(
//         'from' => $twilio_number,
//         'body' => 'I sent this message in under 10 minutes!'
//     )
// );


// include "include/restPHPAltiria.php";
// AltiriaSMS("5352738723", "Me dices si te entra.. el mensaje. Julio", 'TestAltiria', true);

exit;

function connect($host, $port, $timeOut = 5) {
    $fp = fsockopen("ssl://".$host, $port, $errno, $errstr, $timeOut);
    if (!$fp) {
        return "no lo tiene<br>";
        // return true;
    } else {
        fclose($fp); // we know it's listening
        return "Lo tiene<br>";
        // return false;
    }
}

$port = 443;
/**
 * Revisa que el listado de sitios siguientes estén trabajando	
 */
$arrhosts = array(
	'www.administracomercios.com',
	'www.aisremesascuba.com',
	// 'www.travelsandiscoverytours.com',
	// 'www.caribeantravelweb.com',
	// 'www.tropicalnatur.com',
	// 'www.caribbeantravelway.com',
	// 'www.bidaiondo.com',
	// 'www.publinetservicios.com',
	// 'www.bidaitravel.com',
	'www.tropicalnatur.com',
	'www.cubashoppingcenter.com'
);
$ver='';
foreach ($arrhosts as $host) {
	if(!$socket =@ fsockopen("ssl://".$host, 443, $errno, $errstr, 30)) {
		$ver .= 'Caido - '.$host."<br>" ;
		$ver .= "$errno $errstr<br>";
	// } else {
	// 	$ver .= $socket."<br>";
		// $ver .= "<br>".$host."<br>";

		
// $ver .= connect($host, $port);


		// $stream = stream_context_create ( array( "ssl" => array( "capture_peer_cert" => true ) ) );

		// // Bind the resource 'https://www.example.com' to $stream
		// $read   = fopen( "https://".$host, "rb", false, $stream );
		
		// // Get stream parameters
		// $params  = stream_context_get_params( $read );
		
		// // Check that SSL certificate is not null
		// // $cert should be for example "resource(4) of type (OpenSSL X.509)" 
		// $cert   = $params["options"]["ssl"]["peer_certificate"];

		// var_dump($params);

		// if ( !is_null( $cert ) )  echo("Si tiene<br>"); else echo("no tiene<br>");

// exit;
// 		$ch = curl_init();
// 		curl_setopt($ch, CURLOPT_URL, "https://".$host);
// 		curl_setopt($ch, CURLOPT_STDERR, $fp);
// 		curl_setopt($ch, CURLOPT_CERTINFO, 1);
// 		curl_setopt($ch, CURLOPT_VERBOSE, 1);
// 		curl_setopt($ch, CURLOPT_HEADER, 1);
// 		curl_setopt($ch, CURLOPT_NOBODY, 1);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 		$result = curl_exec($ch);
// 		fseek($fp, 0);//rewind
// 		$str='';
// 		while(strlen($str.=fread($fp,8192))==8192);
// echo "fp=$fp<br>";
// echo "result=$result<br>";
// 		// curl_errno($ch)==0 or die("Error:".curl_errno($ch)." ".curl_error($ch)."<br><br>");
// 		if (curl_errno($ch)!=0)  $ver .= "El sitio $host entrega Error: ".curl_errno($ch)." ".curl_error($ch)."<br><br>";

// 		if (!strpos($result, "Location: https://".$host)) {
// 			$ver .= "Problemas con el certificado del sitio $host:<br>$str <br><br>";
// 		}
// 		curl_close($ch);

	}
	// fclose($socket);
}
echo $ver;
exit;
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
	$ver .= '<br>El FTP de fincimex no está trabajando<br>';
} else {
	foreach ($arrDir as $ftp_dirAis) {
		if (!ftp_chdir($conn_id, "/".$ftp_dirAis[0])){
			$verd .= '<br>La carpeta del usuario '.$ftp_dirAis[0]." no fué encontrada.<br><br>";
		}
	}
	if(strlen($verd) > 3) $corCreo->todo(3, "Ftp Ais no existen las carpetas", $verd);
}

ftp_close($conn_id);

if (strlen($ver) > 3) {
	$corCreo->todo(3, "Alerta problemas con los servidores", $ver);
}
?>

