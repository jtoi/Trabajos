<?php

require '../vendor/autoload.php';

$config = parse_ini_file('../config.ini');

//header('Content-Type: application/json');

$stripe = new \Stripe\StripeClient(
  'sk_test_51JFmQBB31jcZ50sm8UC5VQSqjqZhxnMmZaM5Tg2nr5S9r5aQgVSQ2z2qEF88amXqYUVQfqW2W7fZrTxr5Jh9tVQ400nCwfZyKk'
);

//echo $stripe->products->all(['limit' => 30]);
//echo json_encode($stripe->products->all(['limit' => 30]), JSON_PRETTY_PRINT);

$data = $stripe->products->all(['limit' => 30]);
foreach ($data as $obj) {
//    echo "<form method=\"POST\" action=\"list_price.php?price=urlencode($obj->id)>";

//    echo "<form method=\"post\" action=\"list_price.php\">";
//    echo  "<input type=\"hidden\" name=\"price\" value=\"$obj->id\">";
//    echo  "<button type=\"submit\">Submit</button>";
//    echo "</form>";

    echo "<a href=\"list_price.php?price=$obj->id&name=$obj->name\">Price for $obj->name</a>";
    print "</br>";

//    print $obj->id;
//    print "</br>";
}