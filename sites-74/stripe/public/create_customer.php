<?php

require '../vendor/autoload.php';

$config = parse_ini_file('../config.ini');

//header('Content-Type: application/json');


$stripe = new \Stripe\StripeClient(
  'sk_test_51JFmQBB31jcZ50sm8UC5VQSqjqZhxnMmZaM5Tg2nr5S9r5aQgVSQ2z2qEF88amXqYUVQfqW2W7fZrTxr5Jh9tVQ400nCwfZyKk'
);

$customer = $stripe->customers->create([
  'description' => 'My First Test Customer (created for API docs)',
  'name' => 'MrK',
  'email' => 'xvpf.2015@gmail.com',
]);

//echo $customer->getLastResponse()->headers['Request-Id'];
//echo $customer->getLastResponse()->headers['Request-Name'];
//echo $customer->getLastResponse()->headers['Request-Email'];
echo $customer;



