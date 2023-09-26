<?php

// --------------------------------------------------------
// Nombre del programa					: resultado
// Autor								: Julio Toirac.
// Email								: jtoirac@gmail.com
// Fecha								: 13 de febrero de 2011.
// Descripcion							: chequea y salva en la BD los datos de los participantes.
// --------------------------------------------------------

include 'configuracion.php';
include 'site/class_mysql.php';
$conn=new conbd();
$d = $_POST;


$tipo = '';
$tipoT = '';
$licenTipo = '';


include_once $d['lan'].'.php';
$q = "insert into participantes (idevento, atleta, nombre, sexo, fnac, licencia_num, tipoDoc, idprueba, ".
			" cp, correo, fechaInsc, club, telfm, localidad, licencia) values ".
			"('".$d['evento']."', 'S', '".$d['nombre']."', '".$d['sexo']."', '".$d['fn']."', '".$d['lic']."','".$d['dni']."', '".$d['catego']."', '"
			.$d['postal']."', '".$d['correo']."', UNIX_TIMESTAMP(), '".$d['club']."', '".$d['movil']."', '".$d['colegio']."', '".$d['corrpl']."')";
$conn->execute($q);
$idPart = $conn->lastId();
$mensaje = "Tu solicitud de inscripción ha sido recibida correctamente, en los próximos días recibirás un correo del Club Bilbao Atletismo Santutxu para aceptar tu participación.\n\n".
			"Gracias por tu interés.\n".
			"Club Bilbao Atletismo Santutxu.";

$headers = 'From: info@bilbaoatletismosantutxu.com' . "\r\n" .
	 'Reply-To: info@bilbaoatletismosantutxu.comm' . "\r\n" .
	 'X-Mailer: PHP/' . phpversion();
$to      = $d['correo'];
$subject = 'Inscripción en la carrera X Milla Internacional de Bilbao';
//echo "envio-".
mail($to, $subject, $mensaje, $headers);
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

echo "Datos correctamente guardados";
?>
