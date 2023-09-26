<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$header = array(
    "MIME-Version: 1.0",
    "Content-type: text/html; charset=utf-8",
    "Accept-Language: us"
);

$url = 'http://www.visaeurope.com/en/cardholders/exchange_rates.aspx';
$data = array(
            'Template$ctl07$ctl00$ddlCardCurrency'=>'USD',
            'Template$ctl07$ctl00$ddlTransactionCurrency'=>'EUR',
            'Template$ctl07$ctl00$txtFee'=>'0',
            'Template$ctl07$ctl00$rdpDate'=>'2014-02-14',
            'Template_ctl07_ctl00_rdpDate_dateInput_text'=>'14/02/2014',
            'Template$ctl07$ctl00$rdpDate$dateInput'=>'2014-02-14-00-00-00',
            'Template$ctl07$ctl00$txtAmount'=>'1'
        );

$url = 'http://usa.visa.com/personal/card-benefits/travel/exchange-rate-calculator-results.jsp';
$data = array(
            'homCur'=>'USD',
            'forCur'=>'EUR',
            'fee'=>'0',
            'rate'=>'0',
            'firstDate'=>'02/15/2014',
            'date'=>'02/14/2014',
            'lastDate'=>'02/14/2014',
            'actualDate'=>'02-14-2014'
        );

$header = array (  
    'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.63 Safari/535.7' ,
    'Referer: http://usa.visa.com/' ,
    'Accept: text / HTML, application / xhtml + XML, application / XML; q = 0.9, *; q = 0.8' ,
    'Accept-Language: RU, en-us; q = 0.7, en; q = 0.3' ,
    'Accept-Encoding: Identity' ,
    'Accept-Charset: ISO-8859-1, utf-8; q = 0.7, *; q = 0.7'
) ;

$chx = curl_init($url);
curl_setopt ($chx, CURLOPT_HEADER, 1);
curl_setopt ($chx, CURLOPT_HTTPHEADER, $header);
curl_setopt($chx, CURLOPT_HEADER, false);
curl_setopt($chx, CURLOPT_POST, true);
curl_setopt($chx, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chx, CURLOPT_POSTFIELDS, $data);

header ( 'Content-Type: text / HTML; charset = ISO-8859-1' ) ;
$sale = curl_exec($chx);
$curl_info = curl_getinfo($chx);
echo 'Curl error: ' . curl_error($ch)."<br>\n";
curl_close($ch);

foreach ($curl_info as $key => $value) {
    echo $key." = ".$value."<br>\n";
}

echo $sale;
?>