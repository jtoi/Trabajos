<?php

require '../vendor/autoload.php';

$config = parse_ini_file('../config.ini');

//header('Content-Type: application/json');

$stripe = new \Stripe\StripeClient(
  'sk_test_51JFmQBB31jcZ50sm8UC5VQSqjqZhxnMmZaM5Tg2nr5S9r5aQgVSQ2z2qEF88amXqYUVQfqW2W7fZrTxr5Jh9tVQ400nCwfZyKk'
);

//echo $stripe->products->all(['limit' => 30]);
//echo json_encode($stripe->products->all(['limit' => 30]), JSON_PRETTY_PRINT);

$var_id = $_GET['price'];
$var_name = $_GET['name'];

echo $var_name;
print "</br>";

$price = $stripe->prices->all(['limit' => 30, 'product' => $var_id]);
foreach ($price as $obj) {
//    echo "<form method=\"POST\" action=\"list_price.php?price=urlencode($obj->id)>";

//    echo "<form method=\"post\" action=\"list_price.php\">";
//    echo  "<input type=\"hidden\" name=\"price\" value=\"$obj->id\">";
//    echo  "<button type=\"submit\">Submit</button>";
//    echo "</form>";

//    echo "<a href=\"list_price.php?price=$obj->id\">Price for $obj->id</a>";
//    print "</br>";
    $link = $stripe->checkout->sessions->create([
  'success_url' => 'http://192.168.0.25/success.html',
  'cancel_url' => 'http://192.168.0.25/cancel.html',
  'payment_method_types' => ['card'],
  'line_items' => [
    [
      'price' => $obj->id,
      'quantity' => 1,
    ],
  ],
  'mode' => 'payment',
]);
    $amount = $obj->unit_amount/100;
    print $amount;
    print " - ";
    $url = $link['url'];
    echo "<a href=\"$url\">Payment link for $obj->id</a>";
    print "</br>";
}
//print "</br>";
print "<a href=\"list_products.php\">Return to products</a>";



