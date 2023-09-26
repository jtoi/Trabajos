<?php
define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );
$temp = new ps_DB;

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
