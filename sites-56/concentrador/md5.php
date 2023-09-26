<?php
define( '_VALID_ENTRADA', 1 );
ini_set('display_errors', 1);
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );

$clave = desofuscar(_PALABR_OFUS, _CONTRASENA_OFUS);
if ($_REQUEST['idtransaccion']) {
	$trans = $_REQUEST['idtransaccion'];
	$importe =  $_REQUEST['importe'];
	$moneda = $_REQUEST['moneda'];
	$localizador = _LOCALIZADOR;
	$clave = $_REQUEST['palabra'];
	echo _ID_PTO."."._ID_COMERCIO.".$trans.$importe.$moneda.$localizador.$clave<br>";
	echo strtoupper(SHA1(_ID_PTO._ID_COMERCIO.$trans.$importe.$moneda.$localizador.$clave));
}

?>

<form action="" method="post">
	terminal: <input type="text" name="idterminal" value="<?php echo _ID_PTO; ?>" /><br />
	comercio: <input type="text" name="idcomercio" value="<?php echo _ID_COMERCIO; ?>" /><br />
	transaccion: <input type="text" name="idtransaccion" value="<?php echo $trans; ?>" /><br />
	importe: <input type="text" name="importe" value="<?php echo $importe; ?>" /><br />
	moneda: <input type="text" name="moneda" value="<?php echo $moneda; ?>" /><br />
	palabra: <input type="text" name="palabra" value="<?php echo $clave; ?>" /><br />
	<input type="submit" value="Enviar" />
</form>