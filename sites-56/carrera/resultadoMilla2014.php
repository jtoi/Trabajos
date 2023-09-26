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

//$d = array(
//	'evento' => '8',
//	'nombre' => 'Julio',
//	'sexo' => 'M',
//	'fn' => '1960-9-29',
//	'lic' => '11111111',
//	'dni' => '2222222222',
//	'catego' => '69',
//	'postal' => '10400',
//	'correo' => 'sdfgsdf@sdgsdfg.vv',
//	'loc' => 'la habana',	
//	'club' => 'mio'
//);

include_once $d['lan'].'.php';
//	print_r($d);
//	print_r($d['nombre']);

//	inserta el equipo
//$q = "insert into equipo values (null, '".$d['equipo']."', ".time().")";
////echo $q;
//$conn->execute($q);
//$idEqui = $conn->lastId();
//
//for ($i=0;$i<count($d['nombre']);$i++) {
//		inserta atleta
	$q = "insert into participantes (idevento, atleta, nombre, sexo, fnac, licencia_num, tipoDoc, idprueba, cp, correo, fechaInsc, club, telfm) values ".
			"('".$d['evento']."', 'S', '".$d['nombre']."', '".$d['sexo']."', '".$d['fn']."', '".$d['lic']."','".$d['dni']."', '".$d['catego']."', '".$d['postal']."', '".$d['correo']."', UNIX_TIMESTAMP(),"
			." '".$d['club']."',  '".$d['movil']."')";
	$conn->execute($q);
	$idPart = $conn->lastId();
//		echo $q;
//	$q = "insert into representantes (idparticipante, nombre, correo, tel) values ($idPart, '{$d['nombR']}', '{$d['correo']}', '{$d['movil']}')";
//		echo $q;
//		$conn->execute($q);
//}
//print_r($d);
$mensaje = "Tu solicitud de inscripción ha sido recibida correctamente, en los próximos días recibirás un correo del Club de Atletismo Santutxu  para aceptar tu participación.\n\n".
			"Gracias por tu interés.\n".
			"Club de Atletismo Santutxu.";

$headers = 'From: info@bilbaoatletismosantutxu.com' . "\r\n" .
	 'Reply-To: info@bilbaoatletismosantutxu.comm' . "\r\n" .
	 'X-Mailer: PHP/' . phpversion();
$to      = $d['correo'];
$subject = 'Inscripción en la carrera IX Milla Internacional de Bilbao';
//echo "envio-".
		mail($to, $subject, $mensaje, $headers);
//		mail('jtoirac@gmail.com','Mensaje Confirmando','hola');

echo "Datos correctamente guardados";
?>
