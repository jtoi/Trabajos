<?php

// --------------------------------------------------------
// Nombre del programa					: resultado
// Autor								: Julio Toirac.
// Email								: jtoirac@gmail.com
// Fecha								: 11/04/2015.
// Descripcion							: chequea y salva en la BD los datos de los participantes.
// --------------------------------------------------------

include 'configuracion.php';
include 'site/class_mysql.php';

$conn=new conbd();

$d = $_POST;
// print_r($d);


$feArr = explode("/", $d['fn']);
$cuota = '';
if (stripos($d['cuota'], '1') > -1) $cuota .= '- 1ra Semana<br>';
if (stripos($d['cuota'], '2') > -1) $cuota .= '- 2da Semana<br>';
if (stripos($d['cuota'], '3') > -1) $cuota .= '- 3ra Semana<br>';
$cuota = trim($cuota, '<br>');
//		inserta atleta
$q = "insert into participantes (idevento, atleta, nombre, apellidos, sexo, fnac, direccion, localidad, cp,
			telf, correo, pais, doc, licencia_num, carnet, observaciones, provincia, fechaInsc) values".
		"(27, 'S', '".$d['nomb']."', '".$d['ape']."', 'F', '".$feArr[2]."-".$feArr[1]."-".$feArr[0]."', '".$d['direc']."', '".$d['loc']."', '".
		$d['cp']."', '".$d['tel']."', '".$d['correo']."', '".$d['cole']."', '".$d['dni']."', '".$d['cuota']."', '".$d['club']."', '".$d['obs'].
		"', '".$d['catego']."', ".time().")";
//echo $q;
$conn->execute($q);
$idPart = $conn->lastId();
//		echo $q;
$q = "insert into representantes (idparticipante, nombre, tel) values ($idPart, '{$d['tutor']}', '{$d['tdni']}')";
//		echo $q;
$conn->execute($q);

//print_r($d);
echo "Datos correctamente guardados";
$mensaje = "Estimado ".$d['nomb']." ".$d['ape']."<br><br>
			Desde el Club Bilbao Atletismo Santutxu queremos daros las gracias por haber pensado en nosotros y apuntar a vuestro hijo/a al ".
			"<span style='font-weight:bold;'>V Campus de Verano de nuestra Escuela de Atletismo.</span><BR>".
			"<br>".
			"<span style='color:red;font-weight:bold;'>"
			."La confirmaci&oacute;n de la plaza es autom&aacute;tica en cuanto est&aacute; realizado el ingreso en la cuenta. Poner el nombre y apellidos del ni&ntilde;o/a en el ingreso.</span><BR><BR>".
			"<span style='font-weight:bold;'>Titular: CLUB DE ATLETISMO SANTUTXU<BR>".
			"Nº CUENTA: ES67 2095 0212 01 9103156471</span><BR><BR>".
			"Cualquier duda que pudieras tener la comunicas a través del correo info@bilbaoatletismosantutxu.com<BR><BR>".
			"La semana del 19 de junio haremos una reunión informativa con los padres/ madres de los niños apuntados al campus. Esta reunión se ".
			"comunicará con la debida antelación por correo electrónico.<BR><BR>".
			"Un saludo,<BR><BR>".
			"Club Bilbao Atletismo Santutxu";

$headers  = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= "Reply-To: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "Return-Path: info@bilbaoatletismosantutxu.com\n"; 
$headers .= "From: info@bilbaoatletismosantutxu.com\n";
$headers .= "X-Mailer: PHP". phpversion() ."\n";

$subject = 'Solicitud de inscripción en la Escuela de Atletismo de Bilbao Campus de Verano';
//echo "envio-".

$arrTo = array();
$arrTo[] = 'Info Atletismo<info@bilbaoatletismosantutxu.com>';
// $arrTo[] = 'Info Atletismo<jtoirac@gmail.com>';
$arrTo[] = $d['nomb'].' '.$d['ape'].'<'.$d['correo'].'>';

for($i = 0; $i < count($arrTo); $i++){
	mail($arrTo[$i], $subject, $mensaje, $headers);
}
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

?>
