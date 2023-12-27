<?php
require_once "settings.php";


logEcp('payment_server_callback 1');


$data = $_POST['data'];
$sigAll = $_POST['sig'];

logEcp("payment_server_callback 2");

// HTML variables OK?
if (empty($data)||empty($sigAll))  {
logEcp("payment_server_callback - vars not OK. Err 400");
    $this->returnErr400();
}

logEcp("data:$data:sig:$sigAll");

$clavesecretaB64 = $ecpc->eurocoinpay_encryption_key;

$ecp = new EurocoinPayApi();

$res = $ecp->cliObtenParametrosPost($data, $sigAll, $clavesecretaB64);
logEcp("res:" . var_export ($res,TRUE));

// Payment NOT OK
if ($res->error != 'OK')
{
    logEcp("res->error:$res->error");
    return;
}


$srv_order_id = $res->order_number;
$srv_amount = $res->amount;
$srv_transaction_id = $res->operation_id;
$srv_error = $res->error;

$srv_currency = $res->order_currency;


logEcp("srv_order_id:$srv_order_id");
logEcp("srv_amount:$srv_amount");
logEcp("srv_transaction_id:$srv_transaction_id");
logEcp("srv_error:$srv_error");
logEcp("srv_currency:$srv_currency");

$confirmOk = true;
$message = "";

// Check for server error
if ($srv_error != 'OK') {
    $thserr = 'Server reports error:' . $srv_error;
    logEcp($thserr);
    $message .= $thserr;
    $confirmOk = false;
}



//TODO: Adapt the following code to your system and uncomment it

  // // Get order for order id from your database:
  // $order = GETORDER( $srv_order_id);
  // if (!isset($order))
  // {
  //     logEcp("no order");
  //     return;
  // }


  // get currency for the order from your database
  // $curr = $order->get_currency();
  // logEcp('currency:' . $curr);

  // if (strtoupper($curr) != strtoupper($srv_currency)) {
  //     $thserr = 'Currency error.' . " [CURRERR]:" .$srv_currency . ":" . $curr . ".  ";
  //     logEcp($thserr);
  //     $message .= $thserr;
  //     $confirmOk = false;
  // }

  // get total payment amount for the order from your database
  // logEcp("check amount");
  // $order_total = round($order->get_total(),2);
  // if ($srv_amount != $order_total) {
  //     $thserr = 'Order amount error.' . " [AMTERR]:" .$srv_amount . ":" . $order_total . ".  ";
  //     logEcp($thserr);
  //     $message .= $thserr;
  //     $confirmOk = false;
  // }


if (!$confirmOk) {
    logEcp("!$confirmOk");

    ////TODO: Call the function in your system to mark the order as payment failed

    exit();
}


////TODO: Call the function in your system to confirm the order as paid

exit();


