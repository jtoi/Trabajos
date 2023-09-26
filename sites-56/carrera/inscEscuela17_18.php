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
$q = "insert into participantes (idevento, atleta, nombre, apellidos, sexo, fnac, direccion, localidad, cp, 
			telf, correo, pais, doc, licencia_num, carnet, observaciones, 
			idprueba, fechaInsc) values".
		"(14, 'S', '".$d['nomb']."', '".$d['ape']."', 'M', '".$feArr[2]."-".$feArr[1]."-".$feArr[0]."', '".$d['direc']."', '".$d['loc']."', '".$d['cp'].
			"', '".$d['tel']."', '".$d['correo']."', '".$d['cole']."', '".$d['dni']."', '".$d['cuota']."', '".$d['carnet']."', '".$d['obs'].
			"', '".$d['catego']."', ".time().")";
echo $q;
$conn->execute($q);
$idPart = $conn->lastId();
//		echo $q;
$q = "insert into representantes (idparticipante, nombre, tel) values ($idPart, '{$d['tutor']}', '{$d['tdni']}')";
		echo $q;
$conn->execute($q);

//print_r($d);
echo "Datos correctamente guardados";
$mensaje = "Tu solicitud de inscripci&oacute;n ha sido recibida correctamente.<br><br>
				Cuando est&eacute; realizado el ingreso te mandaremos un correo para aceptar tu<br>
				inscripci&oacute;n en la Escuela de Atletismo de Bilbao para la temporada 2015/ 2016.<br><br>
			El abono de la cuota elegida deberá estar realizado antes de que comiences <br>
				los entrenamientos. Os recordamos que no domiciliamos el pago, tenéis que hacer el <br>
				abono en la cuenta del club:<br><br>
			TITULAR: CLUB DE ATLETISMO SANTUTXU<br>
			Nº: 2095 0212 01 9103156471<br><br>
			Por favor en el concepto poner el nombre del niño o niña. Comentaros que si vais <br>
				a la ventanilla tenéis que ir antes de las 10:30 para que os dejen poner el nombre<br>
				en el concepto y no os cobren.<br><br>
			Gracias por tu interés.<br>
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

?>
