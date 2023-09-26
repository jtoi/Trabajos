<?php
$url = "https://intesecure02.tefpay.com/paywebv1.4.26rc10/INPUT.php";
$sharedKey = "5970f635364486.10497355";

// $Ds_Merchant_Url = "https://botest.tefpay.com/callbacktest.php";
$Ds_Merchant_MerchantCode = "V98000250";
$Ds_Merchant_Amount = "300";
$Ds_Merchant_PanMask = "4242";
$Ds_Date = "201207165115";
$Ds_Merchant_MatchingData = "201207164884000000000";
/* **************************************************** */
$postData['Ds_Merchant_TransactionType'] = "18";
$postData['Ds_Merchant_ResponseFormat'] = 'xml';
$postData['Ds_Merchant_Lang'] = 'en';
$postData['Ds_Merchant_MatchingData'] = $Ds_Merchant_MatchingData;
$postData['Ds_Merchant_MerchantCode'] = $Ds_Merchant_MerchantCode;
$postData['Ds_Merchant_Amount'] = $Ds_Merchant_Amount;
$postData['Ds_Merchant_PanMask'] = $Ds_Merchant_PanMask;
$postData['Ds_Date'] = $Ds_Date;


if ($Ds_Merchant_Url != "") {
    $postData['Ds_Merchant_Url'] = $Ds_Merchant_Url;
    $cadena =
        $Ds_Merchant_Amount .
        $Ds_Merchant_MerchantCode .
        $Ds_Merchant_MatchingData .
        $Ds_Merchant_Url .
        $sharedKey;
} else {
    $cadena =
        $Ds_Merchant_Amount .
        $Ds_Merchant_MerchantCode .
        $Ds_Merchant_MatchingData .
        $sharedKey;
}
$postData['Ds_Merchant_MerchantSignature'] = sha1($cadena);

echo "<pre>";
print_r($postData);
echo "</pre>";
    
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // wait max 30 seconds to connect to Tefpay Gateway
curl_setopt($ch, CURLOPT_TIMEOUT, 120); // wait max 2 minutes for response from Tefpay Gateway
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
if ( is_null($postData) == false ) {
    // post fields are specified...
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
}

$xmldata = curl_exec($ch);
echo "$$".$xmldata;
exit;

?>
