<?php
define( '_VALID_ENTRADA', 1 );
define(_MOS_CONFIG_DEBUG, 1);
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );

$d = $_REQUEST;

if (count($d) > 0) {
	$comercio = $d['idcomercio'];
	$trans = $d['idtransaccion'];
	$importe = $d['importe'];
	$moneda = $d['moneda'];
	$clave = $d['palabra'];
	
	echo "md5($comercio.$trans.$importe.$moneda.P.$clave)<br>";
	echo md5($comercio.$trans.$importe.$moneda.'P'.$clave)."<br>";
	echo convierte($comercio, $trans, $importe, $moneda, 'P');
	
}
?>

<form action="" method="post">
	comercio: <input type="text" name="idcomercio" value="<?php echo $comercio; ?>" /><br />
	transaccion: <input type="text" name="idtransaccion" value="<?php echo $trans; ?>" /><br />
	importe: <input type="text" name="importe" value="<?php echo $importe; ?>" /><br />
	moneda: <input type="text" name="moneda" value="<?php echo $moneda; ?>" /><br />
	operación: <input type="text" name="operac" value="P" /><br />
	palabra: <input type="text" name="palabra" value="<?php echo $clave; ?>" /><br />
	<input type="submit" value="Enviar" />
</form>