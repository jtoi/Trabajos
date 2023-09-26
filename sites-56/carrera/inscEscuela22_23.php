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
// error_log( $q);
$conn->execute($q);
$idPart = $conn->lastId();
// 		echo $q;
$q = "insert into representantes (idparticipante, nombre, apellido, correo, tel) values ($idPart, '{$d['tutor']}', '', '', '{$d['tdni']}')";
// 		echo $q;
$conn->execute($q);

switch ($d['catego']) {
	case '99':
		$cat = 'Prebenjamin';
		break;
	
	case '100':
		$cat = 'Benjamin';
		break;
	
	case '101':
		$cat = 'Alevin';
		break;
	
	case '102':
		$cat = 'Infantil';
		break;
	
	case '103':
		$cat = 'Sub-16';
		break;
	
	case '104':
		$cat = 'Sub-18';
		break;
	
	case '105':
		$cat = 'Sub-20';
		break;
	
	default:
		$cat = '';
		break;
}

//print_r($d);
echo "Datos correctamente guardados";
$mensaje = "Estimado ".$d['nomb']." ".$d['ape']." / $cat<br><br>Desde el <strong>Club Bilbao Atletismo Santutxu</strong> queremos daros las gracias por haber pensado en nosotros y preinscribir a vuestro hijo/a en la <strong>temporada 2023/2024 de la Escuela de Atletismo.</strong><br><br>

El comienzo del curso: <br>
<b>ESCOLARES:</b> Opci&oacute;n mensual y de prueba: <br>
Del a&ntilde;o 2010 al 2017: 3 de Octubre (martes y jueves) de 18:00hrs a 19:15hrs<br><br>

<b>FEDERADOS/AS:</b> Solo Opci&oacute;n prueba ";
$otra = "Las inscripciones est&aacute;n cerradas, pero en septiembre se podr&aacute; realizar una prueba de acceso seleccionando la opci&oacute;n de periodo de prueba. Esta prueba ser&aacute; un viernes y se confirmar&aacute; el d&iacute;a y la hora por correo a los interesados. Despu&eacute;s de dicha prueba el entrenador/a determinar&aacute; si es admitido/a.<br>
La prueba ser&aacute; un viernes y os comunicaremos el d&iacute;a y hora por correo";
$mensaje .= "<br><br>

<b>Del a&ntilde;o 2009 y anteriores:</b> desde el 04 de Septiembre del 2023<br><br>
Las inscripciones est&aacute;n cerradas, pero hasta <b>Diciembre</b> se podr&aacute; realizar una prueba de acceso seleccionando la opci&oacute;n de periodo de prueba. Esta prueba ser&aacute; un viernes y se confirmar&aacute; el d&iacute;a y la hora por correo a los interesados. Despu&eacute;s de dicha prueba el entrenador/a determinar&aacute; si es admitido/a.

Cualquier duda que pudieras tener la comunicas a trav&eacute;s del correo <a href='mailto:info@bilbaoatletismosantutxu.com'>info@bilbaoatletismosantutxu.com</a><br><br>

Una vez comenzados los entrenamientos enviaremos un correo con los puntos de la reuni&oacute;n informativa.<br><br>

Se girar&aacute; el recibido desde el mes de inicio del entrenamiento entre los d&iacute;as 1 y 5 del mes.<br><br>

Un saludo,<br><br>

Club Bilbao Atletismo Santutxu<br>";
// echo $mensaje;

$headers  = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= "Reply-To: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "Return-Path: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "From: info@bilbaoatletismosantutxu.com\n";
$headers .= "X-Mailer: PHP". phpversion() ."\n";

$subject = 'Inscripción en la Escuela de Atletismo de Bilbao temporada 2023/2024';
//echo "envio-".

$arrTo = array();
$arrTo[] = 'Info Atletismo<info@bilbaoatletismosantutxu.com>';
$arrTo[] = $d['nomb'].' '.$d['ape'].'<'.$d['correo'].'>';

foreach($arrTo as $to){
	mail($to, $subject, $mensaje, $headers);
}
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

?>
