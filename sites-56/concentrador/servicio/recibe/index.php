<?php



$d = $_POST;
$d['resultado'] = 'A';
if ($d['resultado'] == 'A') {
	$texto = "El resultado de la operaci&oacute;n ha sido satisfactorio.";
} elseif ($d['resulatdo'] == 'D'){
	$texto = "La operaci&oacute;n fu&eacute; Denegada";
}


echo "$texto <br><br>Por favor, cierre esta ventana para volver a la apk Transfermovil<br><br>";
?>
