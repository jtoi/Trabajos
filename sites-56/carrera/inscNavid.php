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
//	print_r($d);


$feArr = explode("/", $d['fn']);
if ($d['cuota'] == 1) $cuota = '2 Semanas';
if ($d['cuota'] == 2) $cuota = 'Semana 1';
if ($d['cuota'] == 3) $cuota = 'Semana 2';
//		inserta atleta
$q = "insert into participantes (idevento, atleta, nombre, apellidos, sexo, fnac, direccion, localidad, cp, telf, correo,
			pais, doc, licencia_num, carnet, observaciones, idprueba, fechaInsc) values".
		"(7, 'S', '".$d['nomb']."', '".$d['ape']."', 'F', '".$feArr[2]."-".$feArr[1]."-".$feArr[0]."', '".$d['direc']."', '".$d['loc']."', '".$d['cp']."', '".$d['tel']."', '".$d['correo'].
			"', '".$d['cole']."', '".$d['dni']."', '$cuota', '".$d['club']."', '".$d['obs']."', '".$d['catego']."', ".time().")";
//echo $q;
$conn->execute($q);
$idPart = $conn->lastId();
//		echo $q;
$q = "insert into representantes (idparticipante, nombre, tel) values ($idPart, '{$d['tutor']}', '{$d['tdni']}')";
//		echo $q;
$conn->execute($q);

//print_r($d);
echo "Datos correctamente guardados";
$mensaje = "Tu solicitud de inscripción ha sido recibida correctamente, en los próximos días recibirás un correo del Club de Atletismo Santutxu para aceptar tu inscripción en el Campus de Navidad de la Escuela de ".
			"Atletismo de Bilbao.<br><br>".
			"Gracias por tu interés.<br>".
			"Club de Atletismo Santutxu.";

$headers  = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= "Reply-To: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "Return-Path: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "From: info@bilbaoatletismosantutxu.com\n";
$headers .= "X-Mailer: PHP". phpversion() ."\n";

$subject = 'Solicitud de inscripción en la Escuela de Atletismo de Bilbao Campus de Navidad';
//echo "envio-".

$arrTo = array();
$arrTo[] = 'Info Atletismo<info@bilbaoatletismosantutxu.com>';
$arrTo[] = $d['nomb'].' '.$d['ape'].'<'.$d['correo'].'>';

foreach($arrTo as $to){
	mail($to, $subject, $mensaje, $headers);
}
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

?>
