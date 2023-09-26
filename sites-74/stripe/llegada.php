<?php
require 'vendor/autoload.php';

// echo json_decode($_REQUEST);

// foreach ($_REQUEST as $key => $value) {
// 	echo "$key -> $value<br>";
// }



$url = "https://www.administracomercios.com/stripeDat.php";

$arrDat = array(
  "di" => $_REQUEST['di'],
  "id" => $_REQUEST['id']
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $arrDat);
$result = curl_exec($ch);

error_log("result->".$result);
$ver = json_decode($result);

if (curl_errno($ch)!=0)  $ver .= "El sitio $url entrega Error: ".curl_errno($ch)." ".curl_error($ch)."<br><br>";

curl_close($ch);

// echo $ver->cant." - ".$ver->intent;

if ($ver->cant == 1) {

  // Retrive paymentIntent
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ );
  $dotenv->load();
  $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);

  $resStr = str_replace("Stripe\PaymentIntent JSON: ","",$stripe->paymentIntents->retrieve($ver->intent,[]));
  // error_log($resStr);

  $objStr = json_decode($resStr, true);
  // var_dump($objStr);
// echo "<br>";
// echo "<br>";

//   echo "estado=".$objStr['status']."<br>".
//   "tarjeta=".$objStr['charges']['data'][0]['payment_method_details']['card']['last4']."<br>".
//   "mensaje=".$objStr->last_payment_error->message."<br>".
//   "codigo=".$objStr->last_payment_error->code."<br>";

  if ($objStr['status']) { //datos recibidos
    $message = $objStr['last_payment_error']['message'];
    $tarjeta = $amount = $moneda = '';

    if ($objStr['status'] == 'succeeded') {
      $pag = 'ok'; 
      $tarjeta = "************".$objStr['charges']['data'][0]['payment_method_details']['card']['last4'];
      $amount = $objStr['amount'];
      $moneda = $objStr['currency'];

      // Calculo del hash
      $cade = $_REQUEST['di'].$moneda.$amount.'ao5psiDHTnr6Hb3qZNdT870btgSgWaYz';
      $firma = hash("sha512", $cade);
    }
    else {
      $pag = 'ko';
      if ($message == null && $objStr['status'] != 'succeeded') $message = "Payment abandoned";
    }
    
    $arrDat = [
      "status"    => $objStr['status'],
      "orderid"   => $_REQUEST['di'],
      "auth"      => $objStr['id'],
      "tarjeta"   => $tarjeta,
      "marca"     => $objStr['charges']['data'][0]['payment_method_details']['card']['brand'],
      "mensaje"   => $message,
      "code"      => $objStr['last_payment_error']['code'],
      "importe"   => $amount,
      "moneda"    => $moneda,
      "firma"     => $firma
    ];

    error_log("arrDat=".json_encode($arrDat));

    //envio los datos a llegada.php
    $url = "https://www.administracomercios.com/rep/llegada.php";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrDat);
    $result = curl_exec($ch);

    if (curl_errno($ch)!=0)  error_log ("El sitio $url entrega Error: ".curl_errno($ch)." ".curl_error($ch));

    curl_close($ch);

    $cadenSal = "<form name=\"envia\" action=\"https://www.administracomercios.com/rep/\" method=\"post\">
<input type=\"hidden\" name=\"status\" value=\"".$objStr['status']."\"/>
<input type=\"hidden\" name=\"resp\" value=\"".$_REQUEST['di']."\"/>
<input type=\"hidden\" name=\"auth\" value=\"".$objStr['id']."\"/>
<input type=\"hidden\" name=\"tarjeta\" value=\"$tarjeta\"/>
<input type=\"hidden\" name=\"marca\" value=\"".$objStr['charges']['data'][0]['payment_method_details']['card']['brand']."\"/>
<input type=\"hidden\" name=\"mensaje\" value=\"$message\"/>
<input type=\"hidden\" name=\"code\" value=\"".$objStr['last_payment_error']['code']."\"/>
<input type=\"hidden\" name=\"importe\" value=\"".$amount."\"/>
<input type=\"hidden\" name=\"moneda\" value=\"".$moneda."\"/>
<input type=\"hidden\" name=\"est\" value=\"$pag\"/>
<input type=\"hidden\" name=\"firma\" value=\"$firma\"/>";
		
		$cadenSal .= '</form><script language=\'javascript\'>document.envia.submit();</script>';
    error_log($cadenSal);
    echo $cadenSal;
  }

}
?>