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
// 	print_r($d);


$feArr = explode("/", $d['fn']);
//if ($d['cuota'] == 1) $cuota = '2 Semanas';
//if ($d['cuota'] == 2) $cuota = 'Semana 1';
//if ($d['cuota'] == 3) $cuota = 'Semana 2';
//		inserta atleta
$q = "insert into participantes (idevento, atleta, nombre, apellidos, sexo, fnac, direccion, localidad, cp, 
			telf, correo, pais, doc, licencia_num, carnet, observaciones, 
			idprueba, fechaInsc, telfm, idequipo, tipoDoc) values".
		"(24, 'S', '".$d['nomb']."', '".$d['ape']."', 'M', '".$feArr[2]."-".$feArr[1]."-".$feArr[0]."', '".$d['direc']."', '".$d['loc']."', '".$d['cp'].
			"', '".$d['tel']."', '".$d['correo']."', '".$d['cole']."', '".$d['dni']."', '".$d['cuota']."', '".$d['carnet']."', '".$d['obs'].
			"', '".$d['catego']."', ".time().", '".$d['tel']."', '22', '".str_replace(",", ".", $d['apagar'])."')";
// echo $q;
$conn->execute($q);
$idPart = $conn->lastId();
// 		echo $q;
$q = "insert into representantes (idparticipante, nombre, apellido, correo, tel) values ($idPart, '{$d['tutor']}', '', '', '{$d['tdni']}')";
// 		echo $q;
$conn->execute($q);

//print_r($d);
echo "Datos correctamente guardados";
$mensaje = "Desde el <strong>Club Bilbao Atletismo Santutxu</strong> queremos daros las gracias por haber pensado en nosotros y apuntar a 
			vuestro hijo/a en la <strong>temporada 2017/2018 de la Escuela de Atletismo.</strong><br><br>
El comienzo del curso:<br><br>
Del a�o 1999 al 2005: <strong>desde el 18 de septiembre del 2017 (lunes, mi�rcoles y viernes)</strong><br>
Esta quincena el importe a abonar es:<br>
. No socios de Bilbao kirolak: 	15,00 euros<br>
. Socios de Bilbao Kirolak: 	12,00 euros <br><br>
Del a�o 2006 al 2011: 	desde el 3 de octubre del 2017 (martes y jueves)<br><br>
La confirmaci�n de la plaza de <strong>".$d['nomb']." ".$d['ape']."</strong> es autom�tica en cuanto est� realizado el ingreso en la cuenta:<br><br>

Titular: CLUB DE ATLETISMO SANTUTXU<br>
N� CUENTA: ES67 2095 0212 01 9103156471<br><br>

Cualquier duda que pudieras tener la comunicas a trav�s del correo <a href='mailto:info@bilbaoatletismosantutxu.com'>info@bilbaoatletismosantutxu.com</a><br><br>

La semana del 11 de septiembre del 2017 haremos una reuni�n informativa con los padres/ madres de los ni�os apuntados a la escuela. Esta reuni�n se comunicar� con la debida antelaci�n por correo electr�nico.<br><br>

Si has seleccionado semana de prueba, podr�s probar 2 d�as que correspondan a tu categor�a una vez este rellenado el formulario de inscripci�n con esa opci�n. <br><br>

Un saludo,<br><br>

Club Bilbao Atletismo Santutxu<br>";

$headers  = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= "Reply-To: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "Return-Path: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "From: info@bilbaoatletismosantutxu.com\n";
$headers .= "X-Mailer: PHP". phpversion() ."\n";

$subject = 'Solicitud de inscripci�n en la Escuela de Atletismo de Bilbao';
//echo "envio-".

$arrTo = array();
$arrTo[] = 'Info Atletismo<info@bilbaoatletismosantutxu.com>';
$arrTo[] = $d['nomb'].' '.$d['ape'].'<'.$d['correo'].'>';

foreach($arrTo as $to){
	mail($to, $subject, $mensaje, $headers);
}
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

?>
