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

</body>
</html>