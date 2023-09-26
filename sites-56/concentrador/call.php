<?php
  include("include/payment.php");
  $a = new Payment("DEMO", "DEMO", "https://www.administracomercios.com/retour.php?estado=ok", "https://www.administracomercios.com/retour.php?estado=ko");
?>
<!doctype html>
<html>
  <head>
    <title>Exemple</title>
  </head>
  <body>
    <form method="post" action="https://secure.homologation.comnpay.com">
     <?php echo $a->buildSecretHTML("Demonstration",10);?>
     <input type="submit" value="Payer!" />
    </form>
  </body>
</html>