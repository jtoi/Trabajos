<?php

define( '_VALID_ENTRADA', 1 );
require_once( '../configuration.php' );
require_once( '../include/mysqli.php' );
require_once( '../include/hoteles.func.php' );
require_once( '../include/correo.php' );

$temp = new ps_DB;
$correo = new correo();

$d = $_REQUEST;

foreach ($d as $key => $value) {
	$lleg .= $key." = ".$value."<br>\n";
}
error_log("Entrada de estado Titanes");
error_log($lleg);

if (($d['Ds_Merchant_Order']*1) < 1000000000000) {
	
	if ($d['Estado'] == 'A' || $d['Estado'] == 'P') {
		
		$firma = hash('sha512', $d['idTitanes'].$d['Estado']._LLAVE_TITANES);
		
		if ($d['Firma'] == $firma) {
			
			//revisa que la orden exista
			$q = "select count(id) total from tbl_aisOrden where idtransaccion = '". $d['idTitanes'] ."'";
			error_log($q);
			$temp->query($q);
			error_log("hay operaci�n=".$temp->f('total'));
			
			if ($temp->f('total') == 1) {
				
				$q = "update tbl_aisOrden set estado = '". $d['Estado'] ."', fechaAct = unix_timestamp() where idtransaccion = '". $d['idTitanes'] ."'";
//				error_log($q);
				$temp->query($q);
				
				$correo->todo(12,"Cambio del estado de la orden ".$d['idTitanes'], "Se ha cambiado satisfactoriamente el estado de la operaci�n ".$d['idTitanes']." a ".$d['Estado']." revisar los datos");
				echo $d['idTitanes'];
				exit();
			
			} elseif ($temp->f('total') > 1) {
				errorF("Hay m�s de una operaci�n con este n�mero de orden ".$d['idTitanes']." revisar");
			} //elseif ($temp->f('total') == 0 ) {
//				errorF("No existe en la BD una operaci�n con este n�mero de orden: ".$d['idTitanes']." revisar");
//			}
			
		} else {
			
			error_log("Falla en el c�lculo de la firma enviada ". $d['Firma'] ." calculada $firma");
			errorF ("Falla en el c�lculo de la firma enviada ");
			
		}
		
	} else errorF ("Falla por Estado enviado ".$d['Estado']);
	
} else errorF("Falla por idTitanes enviado ".$d['idTitanes']);
		


function errorF($mensaje) {
	echo $mensaje;
	$correo = new correo();
	error_log($message);
	$correo->todo(12,"Error en la llegada de datos de Titanes",$mensaje);
}

?>
