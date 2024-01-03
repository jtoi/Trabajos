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
	data: <input type="text" name="data" value="W3wSCXNQeG+F++0zGCHkAC48H9dMu0HymVTXohgjKauD+HExVRRbRSdyVDjt9iQaotzZbO9nu3OamKZ9CmRhQZZcfzufDSqlyDEFMonegn/H82oTMJ/ieX56iPEBIDOv4ymOmX9oTRnLgKbPkN0/LhWYef5ltzZFx5qc9fy+mJgXilMGwlvQzvIA/usfSDevZRs1lO7A9CuBIDmzcC3TiME2pl+ISy21vAbgAdHKCNXX9jS+Dc0bETe9kDbLxFo39Qu6gaH8unjTxgFUyfyj0RT7EEkAj3z1cuFPO5bGKT3kEZjfiJPLoDRcEs6qC04leXcs72z1LpeJZP2QbD0Ev25wVkjf+6XVibAwinW0CC9cNJpzynEFOBCsgZb13zSg"><br />
	sig: <input type="text" name="sig" value="oRA82z8FJe9YnVvq5xoMow==|mPPgPQ9vlIE31QxiAeSh7i7K+zkH04C8Yzq4+VGmyD0="><br />
	<input type="submit" value="Enviar">
</form>

</body>
</html>