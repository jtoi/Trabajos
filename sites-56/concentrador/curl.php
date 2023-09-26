<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$PPTcomercio	= "122327460662";
$PPTtransaccion = substr(time(), -8);
$PPTimporte		= "1235";
$PPTmoneda		= "978";
$PPToperacion	= "P";
$PPTidioma		= "es";
$PPTclave		= "FjswqLm6rNu3F27nGrcM";
$PPTfirma		= md5($PPTcomercio.$PPTtransaccion.$PPTimporte.$PPTmoneda.$PPToperacion.$PPTclave);
//$d = array("comercio"=>$PPTcomercio, "pasarela"=>$PPTpasarela, "transaccion"=>$PPTtransaccion, "importe"=>$PPTimporte, "moneda"=>$PPTmoneda, "operacion"=>$PPToperacion, "idioma"=>$PPTidioma, "firma"=>$PPTfirma, "pasarela"=>"30");

print_r($_SERVER);

$theFields =
  array
    (
		"Ds_Merchant_Amount" => $PPTimporte,
		"Ds_Merchant_Currency" => $PPTmoneda,
		"Ds_Merchant_Order" => "$PPTtransaccion",
		"Ds_Merchant_ProductDescription" => 'Servicios',
		"Ds_Merchant_Titular" => 'JAcinto',
		"Ds_Merchant_MerchantCode" => '285772844',
		"Ds_Merchant_MerchantURL" => 'https://www.concentradoramf.com/rep/llegada.php',
		"Ds_Merchant_UrlOK" => 'https://www.concentradoramf.com/rep/index.php?resp=030429071138&est=ok',
		"Ds_Merchant_UrlKO" => 'https://www.concentradoramf.com/rep/index.php?resp=030429071138&est=ko',
		"Ds_Merchant_MerchantName" => 'CARIBEANTRAVELWEB',
		"Ds_Merchant_ConsumerLanguage" => '001',
		"Ds_Merchant_MerchantSignature" => $PPTfirma,
		"Ds_Merchant_Terminal" => '004',
		"Ds_Merchant_TransactionType" => '0'
    ) ;
$userAgent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0";
$cookieFile = 'cookie.txt';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://sis.redsys.es/sis/realizarPago");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
curl_setopt($ch, CURLOPT_SSLVERSION, 3);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_COOKIE, $_SERVER['HTTP_COOKIE']);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_POSTFIELDS, $theFields);
echo curl_exec($ch);
exit;


?>