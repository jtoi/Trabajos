<?php

// --------------------------------------------------------
// Nombre del programa					: resultado
// Autor								: Julio Toirac.
// Email								: jtoirac@gmail.com
// Fecha								: 07 de abril de 2016.
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
$q = "insert into participantes (idevento, atleta, nombre, apellidos, fnac, sexo, pais, licencia_num, doc, localidad, cp, 
			telf, correo, carnet, direccion, observaciones, pin, 
		fechaInsc) ".
		"values (17, 'S', '".$d['nomb']."', '".$d['ape']."', '".$feArr[2]."-".$feArr[1]."-".$feArr[0]."', '".$d['sex']."', '".$d['alt']."', '".
			$d['pes']."', '".$d['dni']."', '".$d['loc']."', '".$d['cp']."', '".$d['tel']."', '".$d['correo']."', '".$d['carnet']."', '".$d['entr']."', '".
			$d['obs']."', '".$d['pin']."', "
					.time().")";
// echo $q;
$conn->execute($q);
$idPart = $conn->lastId();
//		echo $q;
$q = "insert into representantes (idparticipante, nombre, tel) values ($idPart, '{$d['tutor']}', '{$d['tdni']}')";
//		echo $q;
$conn->execute($q);

//print_r($d);
echo "Datos correctamente guardados";
$mensaje = "Desde el Bilbao Runners queremos darte las gracias por haberte apuntado.<br><br>
		
				Hay un <strong>periodo de prueba totalmente gratuito del 23 al 31 de enero</strong>. 
				Los d�as de entrenamiento de este periodo ser�n los <strong>lunes, mi�rcoles y viernes a las 19:30h</strong> y 
				<strong>el lugar de encuentro en la pista de patinaje del Puente Euskalduna.</strong><br>
				La recepci�n de este correo es la confirmaci�n de la plaza en el periodo de prueba.<br><br>
 
				Despu�s de esta fecha, �sea desde el 1 de febrero comienza el curso con el abono de la correspondiente cuota.<br><br>

				La confirmaci�n de la plaza del curso es autom�tica en cuanto est� realizado el ingreso en la cuenta.<br><br>
		
				KUTXABANK<br>
				TITULAR:   	CLUB DE ATLETISMO SANTUTXU<br> 
				N�:		 ES72 2095 0158 3091 1643 1431<br><br>
				
				Cualquier duda que pudieras tener la comunicas a trav�s del correo <a href='mailto:info@bilbaorunners.com'>
				info@bilbaorunners.com</a><br><br>
				
				Un saludo,<br><br>
				
				Bilbao Runners";

$headers  = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= "Reply-To: info@bilbaorunners.com\n"; 
$headers .= "Return-Path: info@bilbaorunners.com\n"; 
$headers .= "From: info@bilbaorunners.com\n";
$headers .= "X-Mailer: PHP". phpversion() ."\n";

$subject = 'Solicitud de inscripci�n en la Bilbao runners';
//echo "envio-".

$arrTo = array();
$arrTo[] = 'Info Atletismo<info@bilbaorunners.com>';
$arrTo[] = $d['nomb'].' '.$d['ape'].'<'.$d['correo'].'>';

foreach($arrTo as $to){
	mail($to, $subject, $mensaje, $headers);
}
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

?>
