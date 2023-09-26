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
$arrCat = array(63=>'Txupete', 64=>'Pre-benjamin', 65=>'Benjamin', 66=>'Alevin', 67=>'Infantil y Cadete', 68=>'Juvenil Junior', 69=>'Abierta', 
		70=>'Veteranos / as Federados');

include_once $d['lan'].'.php';
$q = "insert into participantes (idevento, atleta, nombre, sexo, fnac, licencia_num, tipoDoc, idprueba, ".
			" cp, correo, fechaInsc, club, telfm, localidad, licencia) values ".
			"('".$d['evento']."', 'S', '".$d['nombre']."', '".$d['sexo']."', '".$d['fn']."', '".$d['lic']."','".$d['dni']."', '".$d['catego']."', '"
			.$d['postal']."', '".$d['correo']."', UNIX_TIMESTAMP(), '".$d['club']."', '".$d['movil']."', '".$d['colegio']."', '".$d['corrpl']."')";
$conn->execute($q);
$idPart = $conn->lastId();
$mensaje = "Gracias ".$d['nombre']." por inscribirte en la X Milla Internacional de Bilbao, en la categor�a:\r\n".
			$arrCat[$d['catego']]. "\r\n".
			"El d�a 28 de marzo puedes pasar por la carpa de secretaria situada en la calle Gran V�a frente al edificio de la Diputaci�n Foral, ".
			" en la secci�n de recogida de dorsales, para retirarlos hasta 1 hora antes del inicio de tu prueba.\r\n".
			"Consulta los horarios en esta misma web. www.bilbaoatletismosantutxu.com. \r\n".
			"Tambi�n podr�s ver las fotos y v�deos de la carrera en nuestra web la semana despu�s de celebrarse esta. \r\n".
			"Si por cualquier motivo despu�s de haber realizado tu inscripci�n tuvieras que modificar alguno de los datos o anular tu participaci�n, ".
			"por favor hazlo a trav�s de la web www.bilbaoatletismosantutxu.com en el formulario de ANULAR O MODIFICAR INSCRIPCI�N, no te vuelvas a ".
			"inscribir de nuevo. \r\n Agradeciendo por anticipado vuestra participaci�n y esperando veros a todos/as el pr�ximo 28 de marzo de 2015, ".
			"se despide atentamente,\n\n". 
			"Club Bilbao Atletismo Santutxu.";

$headers = 'From: info@bilbaoatletismosantutxu.com' . "\r\n" .
	 'Reply-To: info@bilbaoatletismosantutxu.comm' . "\r\n" .
	 'X-Mailer: PHP/' . phpversion();
$to      = $d['correo'];
$subject = 'Confirmaci�n Inscripci�n X Milla Inter. de Bilbao 2015';
//echo "envio-".
mail($to, $subject, $mensaje, $headers);
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

echo "Datos correctamente guardados";
?>
