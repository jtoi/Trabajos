<?php
require_once "settings.php";

$_POST['orderid'] = "34562461346246";
$_POST['amount'] = "5.00";
$_POST['currency'] = "EUR";
$_SERVER['REQUEST_METHOD'] = "POST";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>EurocoinPay payment API Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<h1>Pay with EurocoinPay</h1>
<body>
<form xaction="to-eurocoinpay.php" method="post">
  <label for="orderid">Order ID:</label><br>
  <input type="text" id="orderid" name="orderid" value="1234"><br><br>
  <label for="amount">Amount:</label><br>
  <input type="text" id="amount" name="amount" value="10.00"><br><br>
  <label for="currency">Currency:</label><br>
  <input type="text" id="currency" name="currency" value="EUR"><br><br>
  <input type="submit" value="Pay it with EurocoinPay">
</form> 


<?php
}
else {

  $order_id = $_POST['orderid'];
  $total = $_POST['amount'];
  $currency = $_POST['currency'];
  $sndData = $ecpc->prepareDataForEcpServer($order_id,$total,$currency); 

  try
  {
    $sndData = $ecpc->prepareDataForEcpServer($order_id,$total,$currency); 
  }
  catch (Exception $ex)
  {
    echo 'Exception:' . $ex;
    exit;
  }
  catch (Error $er)
  {
    echo 'Error:' . $er;
    exit;
  }
    
    

  echo '<p>' . 'We are redirecting you to EurocoinPay to complete the payment. One moment, please.' . '</p>';
  
  echo '<form action="' . $sndData["srvUrl"] . '" method="post" id="ecpfrm" style="display:none;" >';
  echo '<input type="hidden" name="data" value="'.$sndData["data"].'" />';
  echo '<input type="hidden" name="sig" value="'.$sndData["sig"].'" />';
  echo '</form>';
  echo '<script>';
  echo '//document.getElementById("ecpfrm").submit();';
  echo '</script>';

}
