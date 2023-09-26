<?php
//Formulario para el envío de datos

$comercio = '131551467398'; //comercio
$transaccion = 'a2'; //identificador de la operación, debe cambiar para cada operación
$tipo = 'P'; //Operación de Pago
$importe = 300; //3.00
$moneda = 840; //USD
$clave = 'mpALhed3NGmPv7DLQWYn'; //Clave NO VIAJA
$amex = 2; //Tipo de tarjeta a usar ver listado que te envié
$md5 = md5($comercio.$transaccion.$importe.$moneda.$tipo.$clave);

echo " md5($comercio.$transaccion.$importe.$moneda.$tipo.$clave)";


/******************************************************************
IMPORTANTE: Los datos hay que cambiarlos editando este fichero, no modificando los valores en el formulario en el navegador
*******************************************************************/
?>

<form name='envPago' method='post' action='https://www.administracomercios.com/index.php'>
	comercio: <input type='text' name='comercio' value='<?php echo $comercio; ?>'/><br />
	transaccion: <input type='text' name='transaccion' value='<?php echo $transaccion; ?>'/><br />
	importe: <input type='text' name='importe' value='<?php echo $importe; ?>'/><br />
	moneda: <input type='text' name='moneda' value='<?php echo $moneda; ?>'/><br />
	operacion: <input type='text' name='operacion' value='<?php echo $tipo; ?>'/><br />
	idioma: <input type='text' name='idioma' value='es'/><br />
	tarjeta: <input type='text' name='amex' value='<?php echo $amex; ?>'/><br />
	firma: <input type='text' name='firma' value='<?php echo $md5; ?>'/><br />
	<input type='submit' value="Enviar"  />
</form>
