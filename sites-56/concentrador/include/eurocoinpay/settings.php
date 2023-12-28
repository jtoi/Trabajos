<?php

require_once "api/eurocoinpay-class.php"; 

$ecpc = new EurocoinPayClass();

//TODO: Set here your payment terminal parameters, provided by EurocoinPay
$ecpc->eurocoinpay_customer_number = 7;
// $ecpc->eurocoinpay_terminal_number = 2;
// $ecpc->eurocoinpay_encryption_key = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$ecpc->eurocoinpay_terminal_number = 1;
$ecpc->eurocoinpay_encryption_key = 'tT4HloLltnctVWzHEikTzwDfpv8rsgslVR/uycaKXzs=';
$ecpc->eurocoinpay_real_mode = false; // real or test payments
$ecpc->eurocoinpay_shop_name = 'Test shop'; // The name of your shop to be displayed
$ecpc->eurocoinpay_log_enabled = false; // Only activate this setting if instructed by EurocoinPay


$cur_page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$pos1 = strrpos($cur_page_url,"/");
$cur_page_dir = substr($cur_page_url,0,$pos1+1);

$ecpc->eurocoinpay_url_ok = $cur_page_dir . "eurocoinpay-example-payok.php";
$ecpc->eurocoinpay_url_fail = $cur_page_dir . "eurocoinpay-example-payfail.php";
$ecpc->eurocoinpay_url_notif = $cur_page_dir .'eurocoinpay-example-receive-notification.php';


$errs = $ecpc->init();
if (!empty($errs)) {
    echo '<p>';
    echo  'Error: EurocoinPay Payment Gateway not correctly configured:</p><p>';
        foreach($errs as $e) {
        echo '-' . $e . '<br/>';
    }
    echo '</p></div>';
    exit;
}    
