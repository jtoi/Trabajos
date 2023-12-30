EurocoinPay PHP payment module

This module allows you to integrate payments with EurocoinPay into your custom PHP application.

A full example of use is provided in the files:
- eurocoinpay-example-payfail.php
- eurocoinpay-example-payment-page.php
- eurocoinpay-example-payok.php
- eurocoinpay-example-receive-notification.php
- settings.php

These files have instructions on where to insert your data / how to integrate it into your process.
The sections where you need to insert your data or add your code are market with //TODO:

Payment is done by redirecting your user to the EurocoinPay payment gateway through a HTTP POST call.
At the EurocoinPay payment gateway  the user will complete (or cancel) the payment.
  In the example the file "eurocoinpay-example-payment-page.php" performs this process

If the payment is successful, the user will be redirected to a "OK" URL you specify
  In the example this is the file "eurocoinpay-example-payok.php"
If the payment fails or is cancelled, the user will be redirected to a "Fail" URL you specify
  In the example this is the file "eurocoinpay-example-payfail.php"

Once the payment is completed (or cancelled), the EurocoinPay payment gateway will call you back
with all the data about it and the payment result at a URL you specify. It is a POST call.
  In the example, the file "eurocoinpay-example-receive-notification.php" is set up to receive the callback

In this callback file you need to check the data from the callback, compare it with the data in your database,
verify if the order has been correctly paid or if the payment failed,
and then do your "order paid" or "order cancelled/failed" process for that order in your system. 

For any questions with this module, please contact EurocoinPay directly