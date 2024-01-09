<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	
<?php
define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' ); 
require_once 'include/mysqli.php';
$temp = new ps_DB;
require_once( 'include/hoteles.func.php' );

$comercio = '122327460662';
$trans = suggestPassword(8);
$firma = convierte($comercio, $trans, '3534', '978', 'P');
?>
<form action="index.php" method="POST">
	Comercio: <input type="text" name="comercio" value="<?php echo $comercio; ?>"><br />
	Transacción: <input type="text" name="transaccion" value="<?php echo $trans; ?>"><br />
	Importe: <input type="text" name="importe" value="3534"><br />
	Moneda: <input type="text" name="moneda" value="978"><br />
	Operacion: <input type="text" name="operacion" value="P"><br />
	Firma: <input type="text" name="firma" value="<?php echo $firma; ?>"><br />
	<input type="submit" value="Enviar">
</form>

<form action="rep/llegada.php" method="POST">
	data: <input type="text" name="data" value="QL9iRK9XVvtjaJ63c4c9rJsbjY6EgP6zxmiBavM4PoB9FOw07uMS76w30cB2mvk5rQmgS7bSVmwpT3w39kn+xO7m3OCgyWryoTvBOUXmt6KAf57gmk22YRvXwHcigOIo92KTzfEHkRqnvuWTY3ceIE0Ae5r+XDwl57MvBAj+8ePR5ccNACTPT3eZ8A/BYDhzG9pElPKC+mcpHussiuYzBaJqkrxq+Vi07CxPLo5HYiVUXqy15J1u9slhQRDQ5Whq8VN5ljHt7Tp9Ux6j/Pa6GLF2PvbHsIi2c9dbyeJE7RW1HJCF/6iTnZcRgdwTmMB6LStqg0wwH0nruQBTvEGR809ZeMDNst8kiiR24lPlwCo5f+lmFWTGCuCgquL7CIfBHFo2Qz5TIrP1BpSpUoQXjwMe3KWfvt/lmBsBwgWEGxQ2r4a5cDlhJGBlENvTvgcEq8ZBbkPaPV35jOCJKuaXQA=="><br />
	sig: <input type="text" name="sig" value="T2pUE05FkeYslIHUGqQ1Ww==|leXzzhXBn8OccgZ4drtnW5Js/xF2O9Eqn90MC/x2MAQ="><br />
	<input type="submit" value="Enviar">
</form>

</body>
</html>
