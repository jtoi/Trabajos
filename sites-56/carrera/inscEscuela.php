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
//if ($d['cuota'] == 1) $cuota = '2 Semanas';
//if ($d['cuota'] == 2) $cuota = 'Semana 1';
//if ($d['cuota'] == 3) $cuota = 'Semana 2';
//		inserta atleta
$diass = implode(",", $d['diass']);
$q = "insert into participantes (idevento, atleta, nombre, apellidos, sexo, fnac, direccion, localidad, cp, 
			telf, correo, pais, doc, licencia_num, carnet, observaciones, idprueba, fechaInsc, tipoDoc, pin) values".
		"(20, 'S', '".$d['nomb']."', '".$d['ape']."', 'M', '".$feArr[2]."-".$feArr[1]."-".$feArr[0]."', '".$d['direc']."', '".$d['loc']."', '".$d['cp'].
			"', '".$d['tel']."', '".$d['correo']."', '".$d['cole']."', '".$d['dni']."', '".$d['cuota']."', '".$d['carnet']."', '".$d['obs'].
			"', '".$d['catego']."', ".time().", '".$d['precio']."', '".$diass."')";
// echo $q;
$conn->execute($q);
$idPart = $conn->lastId();
//		echo $q;
$q = "insert into representantes (idparticipante, nombre, tel) values ($idPart, '{$d['tutor']}', '{$d['tdni']}')";
//		echo $q;
$conn->execute($q);

//print_r($d);
echo "Datos correctamente guardados";
$mensaje = "Desde el <strong>Club Bilbao Atletismo Santutxu</strong> queremos daros las gracias por haber pensado en nosotros y apuntar a vuestro hijo/a la <strong>temporada 2016/2017 de la Escuela de Atletismo.</strong><br><br>
El comienzo del curso es el 3 de octubre del 2016, los d&iacute;as que correspondan dependiendo de la categor&iacute;a.<br><br>

<span style='color:red;'>La confirmaci&oacute;n de la plaza es autom&aacute;tica en cuanto est&aacute; realizado el ingreso en la cuenta.</span><br><br>

Monto a ingresar: {$d['precio']} &euro;<br>
Titular: CLUB DE ATLETISMO SANTUTXU<br>
N&ordm; CUENTA: ES67 2095 0212 01 9103156471<br><br>

Cualquier duda que pudieras tener la comunicas a trav&eacute;s del correo <a style='font-weight: bold' href='mailto:info@bilbaoatletismosantutxu.com' >info@bilbaoatletismosantutxu.com</a><br><br>

<strong>La semana del 12 de septiembre haremos una reuni&oacute;n informativa</strong> con los padres/ madres de los ni&ntilde;os apuntados a la escuela. 
Esta reuni&oacute;n se comunicar&aacute; con la debida antelaci&oacute;n por correo electr&oacute;nico.<br><br>

Un saludo,<br><br>

Club Bilbao Atletismo Santutxu";

$headers  = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= "Reply-To: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "Return-Path: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "From: info@bilbaoatletismosantutxu.com\n";
$headers .= "X-Mailer: PHP". phpversion() ."\n";

$subject = 'Solicitud de inscripción en la Escuela de Atletismo de Bilbao';
//echo "envio-".

$arrTo = array();
$arrTo[] = 'Info Atletismo<info@bilbaoatletismosantutxu.com>';
$arrTo[] = $d['nomb'].' '.$d['ape'].'<'.$d['correo'].'>';

foreach($arrTo as $to){
	mail($to, $subject, $mensaje, $headers);
}
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');
// echo $mensaje;
?>

