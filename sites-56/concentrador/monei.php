<?php

include("include/monei/MoneiClient.php");

$url = "https://api.monei.com/v1/payments";
$arrTo['to'] = array(implode(',', $arrUsr));


$json = json_encode(array(
	'amount' => 400, 
	"currency" => 'EUR', 
	"orderId" => '220209163085',
	'description' => 'Servicios Turisticos',
	'cuastomer' => array('email' => 'john.doe@bidaiondo.com'),
	'callbackUrl' => 'https://www.administracomercios.com/rep/llegada.php',
	'completeUrl' => 'https://www.administracomercios.com/rep/index.php?resp=220209163085&est=ok',
	'cancelUrl' => 'https://www.administracomercios.com/rep/index.php?resp=220209163085&est=ko'
));

echo $json."<br>";

$options = array(
		CURLOPT_POST			=> true,
		CURLOPT_VERBOSE			=> true,
		CURLOPT_URL				=> $url,
		CURLOPT_POSTFIELDS		=> $json,
		CURLOPT_CUSTOMREQUEST	=> 'POST',
		CURLOPT_HTTPHEADER		=> array(
				'Content-Type: application/json',
				'Authorization: pk_test_36cf3e8a15eff3f5be983562ea6b13ec'
		)
);
$ch = curl_init();
curl_setopt_array($ch , $options);

$saliMensaje = curl_exec($ch);
$curl_info = curl_getinfo($ch);

curl_close($ch);
echo $saliMensaje."<br>";
foreach ($curl_info as $key => $value) {
	echo  $key." = ".$value."<br>";
}
?>