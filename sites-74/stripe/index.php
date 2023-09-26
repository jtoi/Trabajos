<?php

require 'vendor/autoload.php';

// {"operid":";trans;","moneda":";moneda;","monto":";importe;","firma":";Digest;","locale":";idioma;"}

if (isset($_POST['operid'])) $operid = $_POST['operid'];
if (isset($_POST['moneda'])) $moneda = strtolower($_POST['moneda']);
if (isset($_POST['monto'])) $monto = $_POST['monto'];
if (isset($_POST['locale'])) $locale = strtolower($_POST['locale']);
if (isset($_POST['firma'])) $firma = $_POST['firma'];

foreach ($_POST as $key => $value) {
	error_log ("$key -> $value");
}

$message = $operid.$moneda.$monto.'ao5psiDHTnr6Hb3qZNdT870btgSgWaYz';
$firm = hash("sha512", $message);
$arrMon = ['usd', 'eur', 'cad', 'gbp'];
$arrLocale = ['es','en','it'];
error_log("$firma == $firm");
// echo $firm."<br>";
// echo $firma."<br>";

if ($firm != $firma) {echo "Invalid data1"; exit;}
if (!is_numeric($monto)) {echo "Invalid data2"; exit;}
if (!in_array($moneda, $arrMon)) {echo "Invalid data3"; exit;}
if (!in_array($locale, $arrLocale)) {echo "Invalid data3"; exit;}
if (strlen($operid) > 15) {echo "Invalid data4"; exit;}

$arrTrad = [
  "en" => [
    'orden' => 'Order to pay'
  ],
  "es" => [
    'orden' => 'Orden a pagar'
  ]
  ];


// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ );
$dotenv->load();

// For sample support and debugging. Not required for production:
\Stripe\Stripe::setAppInfo(
  "stripe-samples/checkout-one-time-payments",
  "0.0.2",
  "https://github.com/stripe-samples/checkout-one-time-payments"
);

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

$domain_url = $_ENV['DOMAIN'];

// Create new Checkout Session for the order
// Other optional params include:
// [billing_address_collection] - to display billing address details on the page
// [customer] - if you have an existing Stripe Customer ID
// [customer_email] - lets you prefill the email input in the form
// For full details see https://stripe.com/docs/api/checkout/sessions/create
// ?session_id={CHECKOUT_SESSION_ID} means the redirect will have the session ID set as a query param
$checkout_session = \Stripe\Checkout\Session::create([
  // 'success_url' => $domain_url . 'public/success.html?session_id={CHECKOUT_SESSION_ID}',
  // 'cancel_url' => $domain_url . 'public/success.html?session_id={CHECKOUT_SESSION_ID}',
  // 'success_url' => $domain_url . '/llegada.php',
  'success_url' => $domain_url . 'llegada.php?id={CHECKOUT_SESSION_ID}&di='.$operid,
  'cancel_url' => $domain_url . 'llegada.php?id={CHECKOUT_SESSION_ID}&di='.$operid,
  'payment_method_types' => explode(",", $_ENV['PAYMENT_METHOD_TYPES']),
  'submit_type' => 'pay',
  'locale' => $locale,
  'mode' => 'payment',
  'line_items' => [[
    'price_data' => [
      'currency' => $moneda,
      'unit_amount' => $monto,
    'product_data' => [
      'name' => $arrTrad[$locale]['orden'].': '.$operid,
      ],
    ],
    'quantity' => 1,
    ]],
    "payment_intent_data" => [
      "metadata" => ["order_id" => $operid],
      "description" => $operid,
    //  "setup_future_usage" => "off_session",
  ],
]);
// echo "sale=".$checkout_session."<br>";
// echo "sale=".$checkout_session->payment_intent."<br>";

$url = "https://www.administracomercios.com/stripeDat.php";

$arrDat = array(
  "trans" => $operid,
  "intent" => $checkout_session->payment_intent,
  "session" => $checkout_session->id
);

error_log(json_encode($arrDat));

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

if (curl_errno($ch)!=0)  $ver .= "El sitio $url entrega Error: ".curl_errno($ch)." ".curl_error($ch)."<br><br>";

curl_close($ch);

//exit;
header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
