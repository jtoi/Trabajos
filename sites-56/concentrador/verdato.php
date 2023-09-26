<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

echo geoip_country_code3_by_name('152.207.61.74');

function getHeaders()
{
    $headers = array();
    foreach ($_SERVER as $k => $v)
    {
        if (substr($k, 0, 5) == "HTTP_")
        {
            $k = str_replace('_', ' ', substr($k, 5));
            $k = str_replace(' ', '-', ucwords(strtolower($k)));
            $headers[$k] = $v;
        }
    }
    return $headers;

}  

$proxy_headers = array(
    'HTTP_VIA',
    'HTTP_X_FORWARDED_FOR',
    'HTTP_FORWARDED_FOR',
    'HTTP_X_FORWARDED',
    'HTTP_FORWARDED',
    'HTTP_CLIENT_IP',
    'HTTP_FORWARDED_FOR_IP',
    'VIA',
    'X_FORWARDED_FOR',
    'FORWARDED_FOR',
    'X_FORWARDED',
    'FORWARDED',
    'CLIENT_IP',
    'FORWARDED_FOR_IP',
    'HTTP_PROXY_CONNECTION',
	'PHP_SELF',
	'GATEWAY_INTERFACE',
	'SERVER_ADDR',
	'SERVER_NAME',
	'SERVER_SOFTWARE',
	'SERVER_PROTOCOL',
	'REQUEST_METHOD',
	'REQUEST_TIME',
	'QUERY_STRING',
	'DOCUMENT_ROOT',
	'HTTP_ACCEPT',
	'HTTP_ACCEPT_CHARSET',
	'HTTP_ACCEPT_ENCODING',
	'HTTP_ACCEPT_LANGUAGE',
	'HTTP_CONNECTION',
	'HTTP_HOST',
	'HTTP_REFERER',
	'HTTP_USER_AGENT',
	'HTTPS',
	'REMOTE_ADDR',
	'REMOTE_HOST',
	'REMOTE_PORT',
	'SCRIPT_FILENAME',
	'SERVER_ADMIN',
	'SERVER_PORT',
	'SERVER_SIGNATURE',
	'PATH_TRANSLATED',
	'SCRIPT_NAME',
	'REQUEST_URI',
	'PHP_AUTH_USER',
	'PHP_AUTH_PW',
	'AUTH_TYPE',
	'PATH_INFO',
	'ORIG_PATH_INFO' 
        );
foreach($proxy_headers as $x){
    echo $x." - ".$_SERVER[$x]."<br>";
}
echo "<br><br>";

foreach (getallheaders() as $name => $value) {
    echo "$name: $value<br>";
}
echo "<br><br>";
print_r( getHeaders());
echo "<br><br>";
foreach ($_SERVER as $name => $value) {
    echo "$name: $value<br>";
}
?>
