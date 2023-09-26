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

if (isset($d['event'])) {
	include_once $d['lan'].'.php';
	$fecha = $d['fn']."-01-01";
//	print_r($d);
	$q = "insert into participantes (idevento, nombre, apellidos, sexo, fnac, telfm, correo, club, licencia_num, observaciones, idprueba)
			values ({$d['event']}, '{$d['nombre']}', '{$d['apellido']}', '{$d['sexo']}', '$fecha', '{$d['movil']}', '{$d['correo']}', '{$d['club']}',
			'{$d['lic']}', '{$d['observ']}', {$d['catego']})";
//			echo $q;
	$conn->execute($q);
	$idPart = $conn->lastId();
	
// 	if ($d['atleta'] == 'N') {
// 		$q = "insert into representantes values (null, $idPart, '{$d['nombre']}', '{$d['apellido']}', '{$d['correo']}', '{$d['movil']}') ";
// 		$conn->execute($q);
// 	}
	
// 	$arrPrue = explode(",", $d['catego']);
// 	for ($i = 0; $i < count($arrPrue); $i++) {
// 		$q = "insert into registros values (null, $idPart, $arrPrue[$i], '{$d['marca1'.($i+1)]}', '{$d['marca2'.($i+1)]}', '{$d['marca3'.($i+1)]}')";
// 		$conn->execute($q);
// 	}
	
	if ($d['sexo'] == 'F') $sexo = 'Femenino';
	else $sexo = 'Masculino';
	
// 	$mensaje = $idiom[32];
	$q = "select nombre from prueba where id = {$d['catego']}";
	$conn->execute($q);
	$pruebas = $conn->f().", sexo $sexo.\n\n";
	
	
	$headers = 'From: Club Bilbao de Atletismo Santutxu<info@bilbaoatletismosantutxu.com>' . "\r<br>\n" .
			'Reply-To: info@bilbaoatletismosantutxu.comm' . "\r<br>\n" .
			'X-Mailer: PHP/' . phpversion();
	$to      = $d['correo']; 
	//$to = "jtoirac@gmail.com";
	$subject = 'Confirmación Inscripción XVII Reunión Internacional Villa de Bilbao 2017';
	// 		$mensaje = $idiom[32];
	$mensaje = "Gracias ".ucfirst($d['nombre'])." ".ucfirst($d['apellido'])." por inscribirte en la XVII Reunión Internacional Villa de Bilbao 2017, en la categoría \n\n$pruebas";
	$mensaje .= "Recogida de dorsales:\n\n";
	$mensaje .= "El día 24 de junio puedes pasar por la secretaria en la Pista de Atletismo de Zorroza (Bilbao) hasta 90 minutos antes de la prueba. ";
	$mensaje .= "Consulta los horarios en esta misma web www.bilbaoatletismosantutxu.com\n\n".
			'También podrás ver las fotos y videos de la prueba en nuestra web la semana después de celebrarse esta. '."\n\n".
			'Si por cualquier motivo después de haber realizado tu inscripción tuvieras que modificar alguno de los datos o anular tu participación, '.
			"por favor hazlo mandando un correo a la dirección info@bilbaoatletismosantutxu.com.\n\n".
			"Agradeciendo por anticipado vuestra participación y esperando veros a todos/as el próximo 24 de junio de 2016, se despide atentamente\n\n".
			'Club Bilbao de Atletismo Santutxu.';
// 	echo $mensaje;
	$correoMi .= "\n$mensaje";
	mail($to, $subject, $correoMi, $headers);

	
	echo "Datos correctamente guardados";
	
}
?>
