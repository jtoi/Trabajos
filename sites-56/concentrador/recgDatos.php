<?php
/* 
 * 
 */

$correoMi = "Cadena de llegada:"."\n";

$correoMi .= implode("<br>", $_REQUEST);

$headers = 'From: info@amfglobalitems.com' . "\r\n" .
	 'Reply-To: info@amfglobalitems.com' . "\r\n" .
	 'X-Mailer: PHP/' . phpversion();
$to      = 'jtoirac@gmail.com';
$subject = 'Cadena de llegada ';
//mail($to, $subject, $correoMi, $headers);

if ($_REQUEST['comercio'] == '128562105623') {

	$correoMi = "Alipio estos son los datos que esta enviando el concentrador recogidas por
					una página que es similar a la que deberías tener allá: ".$correoMi;
	$headers = 'From: info@amfglobalitems.com' . "\r\n" .
		 'Reply-To: info@amfglobalitems.com' . "\r\n" .
		 'X-Mailer: PHP/' . phpversion();
	$to      = 'alipio@rrsol.com';
	$subject = 'Cadena de llegada ';
//	mail($to, $subject, $correoMi, $headers);
}

//echo $correoMi;

?>
