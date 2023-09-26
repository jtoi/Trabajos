<?php
define('_VALID_ENTRADA', 1);

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
$d['evento'] = '34';
 
// error_log("entra: ".json_encode($d));
$tipo = '';
$tipoT = '';
$licenTipo = '';

$arrCat = array(63=>'Txupete', 64=>'Pre-benjamin', 65=>'Benjamin', 66=>'Alevin', 67=>'Infantil y Cadete', 68=>'Juvenil Junior', 69=>'Abierta', 70=>'Veteranos / as Federados');

if (isset($d['dame'])) {
	$conn->execute('select count(*) total from participantes where idevento = '.$d['evento'].' and sexo = "M" and idprueba = 70');
	echo $conn->f('total');
	// error_log(json_encode($conn->f('total')));
	// echo 'select count(*) total from participantes where idevento = '.$d['evento'].' and sexo = "M" and idprueba = 70 -'.$conn->f('total');
}

if (isset($d['nombre']) && isset($d['evento'])) {
	$nombre = substr($d['nombre'], 0, strpos($d['nombre'], ' '));
	$apellido = substr($d['nombre'], strpos($d['nombre'], ' ')+1);

	//include_once $d['lan'].'.php';
	$q = "insert into participantes (idevento, idequipo, atleta, nombre, apellidos, sexo, fnac, licencia_num, tipoDoc, idprueba, ".
				" cp, correo, fechaInsc, club, telfm, telf, localidad, licencia) values ".
				"('".$d['evento']."', '1', 'S', '".$nombre."', '".$apellido."', '".$d['sexo']."', '".$d['fn']."', '".$d['lic']."','".$d['dni']."', '".$d['catego']."', '"
				.$d['postal']."', '".$d['correo']."', UNIX_TIMESTAMP(), '".$d['club']."', '".$d['movil']."', '".$d['movil']."', '".$d['colegio']."', '".$d['corrpl']."')";
	// error_log($q);
	$conn->execute($q);
	$idPart = $conn->lastId();
	$sexo = 'Masculino';
	if ($d['sexo'] == 'F') $sexo = 'Femenino';
	$mensaje = "Gracias ".$d['nombre']." por inscribirte en la XV Milla Internacional de Bilbao, en la categor&iacute;a:\r\n".
				$arrCat[$d['catego']]. " sexo ". $sexo ."\r\n\r\n".
				"El d&iacute;a 06 de abril puedes pasar por la carpa de secretaria situada en la calle Gran V&iacute;a frente al edificio de la Diputaci&oacute;n Foral, ".
				" en la secci&oacute;n de recogida de dorsales, para retirarlos hasta 1 hora antes del inicio de tu prueba.\r\n\r\n".
				"Consulta los horarios en esta misma web. www.bilbaoatletismosantutxu.com. \r\n\r\n".
				"Tambi&eacute;n podr&aacute;s ver las fotos y v&iacute;deos de la carrera en nuestra web la semana despu&eacute;s de celebrarse esta. \r\n\r\n".
				"Si por cualquier motivo despu&eacute;s de haber realizado tu inscripci&oacute;n tuvieras que modificar alguno de los datos o anular tu participaci&oacute;n, ".
				"por favor hazlo mandando un correo a la direcci&oacute;n info@bilbaoatletismosantutxu.com. \r\n\r\n".
				"Agradeciendo por anticipado vuestra participaci&oacute;n y esperando veros a todos/as el pr&oacute;ximo 21 de Abril de 2018, ".
				"se despide atentamente,\n\n". 
				"Club Bilbao Atletismo Santutxu.";

	$headers = 'From: info@bilbaoatletismosantutxu.com' . "\r\n" .
		'Reply-To: info@bilbaoatletismosantutxu.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	$to      = $d['correo'];
	$subject = 'Confirmación Inscripción XV Milla Internacional de Bilbao 2019';
	//echo "envio-".
	mail($to, $subject, $mensaje, $headers);
	//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

	echo "Datos correctamente guardados";
}
?>
