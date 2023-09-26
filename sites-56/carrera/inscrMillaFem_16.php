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
	$conn=new conbd();
    // Stripslashes
	echo "valentr".$value."\n";
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    // Quote if not a number or a numeric string
    if (!is_numeric($value)) {
        $value = "'" . mysqli_real_escape_string($conn, $value) . "'";
    } echo $value."\n";
    return $value;
}

$tipo = '';
$tipoT = '';
$licenTipo = '';

$d = $_POST;

//include_once $d['lan'].'.php';
// 	print_r($d);
//	print_r($d['nombre']);

//	inserta el equipo
$q = "insert into equipo values (null, '".$d['equipo']."', ".time().")";
//echo $q;
$conn->execute($q);
$idEqui = $conn->lastId();

if (strlen($d['equipo']) > 2) $nmb = $d['equipo']; else $nmb = $d['nombre'];
switch ($d['catego']) {
	case 100:
	$cag = 'Benjam&iacute;n';
	break;
	case 101:
	$cag = 'Alevin';
	break;
	case 102:
	$cag = 'Infantil';
	break;
	case 103:
	$cag = 'Cadete';
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
	if (strlen($d['nombre'][$i]) > 3) {
		$q = "insert into participantes (idevento, atleta, nombre, apellidos, telf, telfm, correo, localidad, cp, sexo, fnac, licencia_num, tipoDoc, doc, idprueba, idequipo, fechaInsc) values".
				"(".$d['evento'].", 'S', '".$d['nombre'][$i]."', '', '', '', '', '', '', 'F', '".$d['fn'][$i]."-01-01', '".$d['lic'][$i]."','DNI', '".$d['dni'][$i]."', ".$d['catego'].", $idEqui, unix_timestamp())";
		$conn->execute($q);
		$idPart = $conn->lastId();
		//echo $q;
		$q = "insert into representantes (idparticipante, nombre, apellido, correo, tel) values ($idPart, '{$d['nombR']}', '', '{$d['correo']}', '{$d['movil']}')";
		//echo $q;
		$conn->execute($q);
	}
}
//print_r($d);
echo "Datos correctamente guardados";
$mensaje = "Gracias ".strtoupper($d['nombR'])." por inscribirte en la <b>VII Milla Marina Femenina por Equipos</b>, en la categoría:<br>
			$cag<br><br>
			<span style='font-weight:bold'></span><br><br>
			<b>El día 26 de octubre puedes pasar por la carpa de secretaria situada en la explanada del Museo del Guggenheim, en la sección de recogida de dorsales, para retirarlos hasta 1 hora antes del inicio de tu prueba. La inscripción es gratuita.</b><br><br>
			Consulta los horarios en esta misma web. <a href='www.bilbaoatletismosantutxu.com'>www.bilbaoatletismosantutxu.com</a>.<br><br>
			También podrás ver las fotos y vídeos de la carrera en nuestra web la semana después de celebrarse esta. <br><br>
			Si por cualquier motivo después de haber realizado tu inscripción tuvieras que modificar alguno de los datos o anular tu participación, por favor hazlo mandando un correo a la dirección <a href='mailto:info@bilbaoatletismosantutxu.com'>info@bilbaoatletismosantutxu.com</a>.<br><br>
			Agradeciendo por anticipado vuestra participación y esperando veros a todos/as el próximo 26 de octubre de 2018, se despide atentamente,<br><br>
			Club Bilbao Atletismo Santutxu.";

$headers = 'From: info@bilbaoatletismosantutxu.com' . "\r\n" .
	 'Reply-To: info@bilbaoatletismosantutxu.com' . "\r\n" .
	 'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
	 'MIME-Version: 1.0' . "\r\n" .
	 'X-Mailer: PHP/' . phpversion();
$to      = $d['correo'];
$subject = 'Confirmación Inscripción VII Milla Marina Femenina por Equipos';
//echo $mensaje;
		mail($to, $subject, $mensaje, $headers);
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

?>
