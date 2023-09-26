<?php
$d = array("text"=>"Hola Mundo");
$options = array(
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_SSL_VERIFYPEER	=> false,
		CURLOPT_SSL_VERIFYHOST 	=> false,
		CURLOPT_POST			=> true,
		CURLOPT_VERBOSE			=> true,
		CURLOPT_URL				=> 'https://www.cubashoppingcenter.com/paid/llegada.php',
		CURLOPT_POSTFIELDS		=> $d
);

$ch = curl_init();
curl_setopt_array($ch , $options);
echo curl_exec($ch);

?>