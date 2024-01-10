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
	data: <input type="text" name="data" value="XxoHZwAeZ1WfH+syl1C3f07JnL2y3WQz2Xc9cnlI7tUDk7RGuzOtNb9w2nd+25NoUweg+qIAXMF3gjuzPpC4wuWGd2Uav57wAk7MgcHgDSrdaeAllyeLx6lwf+SMPxrFCfxO6TxItxLQUZBSn+J4hz69rtkx+nTmHJPu/+DCwBEaLNy4MoQQfcEPgmtN/ibBAgbuRMXQtscFi7KNa8t0RTkhGf1Jwt8R4LWP1z7wJsRdzVRHc+dB95uATtX6keVClAJEndJfKBXrlPID48F3FK0RvEehNZG2ovD4gt87KHH2WnH3auZ7LvXj5Y6ru2ks8BD1rWtR0SR2Cv6Obqu6Eew5kpq6cAfedoJHH+r59dCesMmnu5xTS5Ujrs0xjFgdBm8tFiomnG2sNDBXBAA9Hed0GGqj+JLsztl6mb4KwMU="><br />
	sig: <input type="text" name="sig" value="OQn7qHNt2n8n1RPeQ6lXKw==|8iDRCvSr38WBEQYW/MtwwBW1MGyjs1V2ARJU6d/xyRA="><br />
	<input type="submit" value="Enviar">
</form>

</body>
</html>
