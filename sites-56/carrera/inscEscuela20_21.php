<?php

// --------------------------------------------------------
// Nombre del programa					: resultado
// Autor								: Julio Toirac.
// Email								: jtoirac@gmail.com
// Fecha								: 13 de febrero de 2011.
// Descripcion							: chequea y salva en la BD los datos de los participantes.
// --------------------------------------------------------

// error_log("direccion:".__DIR__."/configuracion.php");
include 'configuracion.php';
include 'site/class_mysql.php';

$conn=new conbd();

$d = $_POST;
// foreach ($d as $value => $item) {
// 	error_log("POST- ".$value . "=" . $item);
// }


$feArr = explode("/", $d['fn']);
//if ($d['cuota'] == 1) $cuota = '2 Semanas';
//if ($d['cuota'] == 2) $cuota = 'Semana 1';
//if ($d['cuota'] == 3) $cuota = 'Semana 2';
//		inserta atleta
$q = "insert into participantes (idevento, atleta, nombre, apellidos, sexo, fnac, direccion, localidad, cp, telf, correo, pais, doc, licencia_num, carnet, observaciones, idprueba, fechaInsc, telfm, idequipo, tipoDoc, club, provincia, pin) values ".
		"(".$d['evento'].", 'S', '".$d['nomb']."', '".$d['ape']."', '".$d['sexo']."', '".$feArr[2]."-".$feArr[1]."-".$feArr[0]."', '".$d['direc']."', '".$d['loc']."', '".$d['cp']."', '".$d['tel']."', '".$d['correo']."', '".$d['cole']."', '".$d['dni']."', '".$d['cuota']."', '".$d['carnet']."', '".$d['obs'].
			"', '".$d['catego']."', ".time().", '".$d['tel']."', '22', '".str_replace(",", ".", $d['apagar'])."', '".$d['tcuenta']."', '".$d['ttdni']."', '".$d['iban']."')";
error_log( $q);
$conn->execute($q);
$idPart = $conn->lastId();
// 		echo $q;
$q = "insert into representantes (idparticipante, nombre, apellido, correo, tel) values ($idPart, '{$d['tutor']}', '', '', '{$d['tdni']}')";
// 		echo $q;
$conn->execute($q);

//print_r($d);
echo "Datos correctamente guardados";
$mensaje = "Desde el <strong>Club Bilbao Atletismo Santutxu</strong> queremos daros las gracias por haber pensado en nosotros y apuntar a vuestro hijo/a en la <strong>temporada 2020/2021 de la Escuela de Atletismo.</strong><br><br>El comienzo del curso:<br><br>

Hasta que no finalicen las obras de la pista de atletismo de Zorroza:<br><br>

- los atletas del a&ntilde;o 2007 y posteriores no empezar&aacute;n los entrenamientos. <br>
- los atletas del a&ntilde;o 2006 y anteriores entrenar&aacute;n en otros lugares.<br><br>

El comienzo del curso: <br>
<b>ESCOLARES:</b> Opci&oacute;n mensual y de prueba: <br>
Del a&ntilde;o 2007 al 2014: en cuanto finalicen las obras de la pista de atletismo de Zorroza os enviaremos un correo para avisaros del inicio de los entrenamientos. (martes y jueves) de 18 a 19:15 h<br><br>

<b>FEDERADOS/AS:</b> Solo Opci&oacute;n prueba: <br>
Del a&ntilde;o 2006 y anteriores:  desde el 02 de septiembre del 2020 (lunes, mi&eacute;rcoles y viernes) de 18 a 19:30/20 h<br>
Las inscripciones est&aacute;n cerradas, pero en octubre y noviembre se podr&aacute; realizar una prueba de acceso seleccionando la opci&oacute;n de per&iacute;odo de prueba. Esta prueba ser&aacute; un viernes y se confirmar&aacute; el d&iacute;a por correo a los interesados. Despu&eacute;s de dicha prueba el entrenador/a determinar&aacute; si es admitido/a.<br><br>

Cualquier duda que pudieras tener la comunicas a trav&eacute;s del correo <a href='mailto:info@bilbaoatletismosantutxu.com'>info@bilbaoatletismosantutxu.com</a><br><br>

Esta temporada debido a la pandemia no se realizará la reunión informativa con los padres/ madres de los niños/as apuntados a la escuela. Una vez comenzados los entrenamientos enviaremos un correo con los puntos de la reunión informativa.<br><br>

Se girar&aacute; el recibido desde el mes de inicio del entrenamiento entre los d&iacute;as 1 y 5 del mes.<br><br>

Un saludo,<br><br>

Club Bilbao Atletismo Santutxu<br>";

$headers  = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= "Reply-To: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "Return-Path: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "From: info@bilbaoatletismosantutxu.com\n";
$headers .= "X-Mailer: PHP". phpversion() ."\n";

$subject = 'Solicitud de inscripci&oacute;n en la Escuela de Atletismo de Bilbao temporada 2020/2021';
//echo "envio-".

$arrTo = array();
$arrTo[] = 'Info Atletismo<info@bilbaoatletismosantutxu.com>';
$arrTo[] = $d['nomb'].' '.$d['ape'].'<'.$d['correo'].'>';

foreach($arrTo as $to){
	mail($to, $subject, $mensaje, $headers);
}
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

?>
