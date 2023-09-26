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


function quote_smart($value){
    // Stripslashes
	echo "valentr".$value."\n";
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    // Quote if not a number or a numeric string
    if (!is_numeric($value)) {
        $value = "'" . mysql_real_escape_string($value) . "'";
    } echo $value."\n";
    return $value;
}

$tipo = '';
$tipoT = '';
$licenTipo = '';

$d = $_POST;

include_once $d['lan'].'.php';
	print_r($d);
//	print_r($d['nombre']);

//	inserta el equipo
$q = "insert into equipo values (null, '".$d['equipo']."', ".time().")";
//echo $q;
$conn->execute($q);
$idEqui = $conn->lastId();

if (strlen($d['equipo']) > 2) $nmb = $d['equipo']; else $nmb = $d['nombre'];
switch ($d['catego']) {
	case 26:
	$cag = 'Benjam&iacute;n';
	break;
	case 27:
	$cag = 'Alevin';
	break;
	case 28:
	$cag = 'Infantil y Cadete';
	break;
	case 29:
	$cag = 'Juvenil Junior';
	break;
	case 30:
	$cag = 'Abierta Fem.';
	break;
	case 32:
	$cag = 'Federadas';
	break;
	default:
	$cag = 'Federadas en atletismo';
	break;
}

for ($i=0;$i<count($d['nombre']);$i++) {
//		inserta atleta
	$q = "insert into participantes (idevento, atleta, nombre, sexo, fnac, licencia_num, tipoDoc, doc, idprueba, idequipo, fechaInsc) values".
			"(15, 'S', '".$d['nombre'][$i]."', 'F', '".$d['fn'][$i]."-01-01', '".$d['lic'][$i]."','DNI', '".$d['dni'][$i]."', ".$d['catego'].", $idEqui, unix_timestamp())";
	$conn->execute($q);
	$idPart = $conn->lastId();
		//echo $q;
	$q = "insert into representantes (idparticipante, nombre, correo, tel) values ($idPart, '{$d['nombR']}', '{$d['correo']}', '{$d['movil']}')";
//		echo $q;
	$conn->execute($q);
}
//print_r($d);
echo "Datos correctamente guardados";
$mensaje = "Gracias <b>".strtoupper($d['nombR'])."</b> por inscribirte en la III Milla Marina Femenina por Equipos de Bilbao, en la categor&iacute;a: $cag<br><br>
 				La inscripci&oacute;n quedar&aacute; confirmada autom&aacute;ticamente en cuanto realices el abono en cuenta.<br><br>
 				El costo de la inscripci&oacute;n ser&aacute; de seis euros (6&euro;) por equipo y tres euros (3&euro;) por deportista individual; 
 				en este &uacute;ltimo caso la organizaci&oacute;n formar&aacute; los equipos con otras atletas inscritas. Al realizar el abono poner el nombre del equipo.<br><br>
 				Las inscripciones se realizar&aacute;n en la cuenta Bizkaialde:<br><br>
 				BBK No. 2095 0611 0091 1295 4160.<br><br>
 				El dinero que perciba Bizkaialde a trav&eacute;s de esta carrera popular, va destinado al proyecto Fundaci&oacute;n Bizkaia Bizkaialde Fundazioa, 
 				que apoya a los clubes y deportistas de referencia del Territorio Hist&oacute;rico de Bizkaia.<br><br>
 				El d&iacute;a 24 de octubre puedes pasar por la carpa de secretaria situada en la explanada del Museo Guggenheim, en la secci&oacute;n de recogida de 
 				dorsales, para retirarlos hasta 1 hora antes del inicio de tu prueba.<br><br>
 				Consulta los horarios en esta misma web. <a href='www.bilbaoatletismosantutxu.com'>www.bilbaoatletismosantutxu.com</a>. Tambi&eacute;n podr&aacute;s ver las fotos y v&iacute;deos de la carrera 
 				en nuestra web la semana despu&eacute;s de celebrarse esta.<br><br>
 				Agradeciendo por anticipado vuestra participaci&oacute;n y esperando veros a todas el pr&oacute;ximo 24 de octubre de 2015, se despide atentamente,<br><br>
				Club Bilbao Atletismo Santutxu.";

$headers = 'From: info@bilbaoatletismosantutxu.com' . "\r\n" .
	 'Reply-To: info@bilbaoatletismosantutxu.com' . "\r\n" .
	 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
	 'MIME-Version: 1.0' . "\r\n" .
	 'X-Mailer: PHP/' . phpversion();
$to      = $d['correo'];
$subject = 'Inscripción en la carrera III Milla Marina Femenina por Equipos';
//echo "envio-".
		mail($to, $subject, $mensaje, $headers);
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

?>
