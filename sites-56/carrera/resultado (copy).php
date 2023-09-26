<?php

// --------------------------------------------------------
// Nombre del programa					: resultado
// Autor								: Julio Toirac.
// Email								: jtoirac@gmail.com
// Fecha								: 22 de febrero de 2011.
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

if (isset($d['event']) && $d['event'] == 2) {
	include_once $d['lan'].'.php';
	$fecha = $d['fn']."-01-01";
//	print_r($d);
	$q = "insert into participantes (idevento, atleta, nombre, apellidos, sexo, fnac, telfm, correo, club, licencia_num, observaciones)
			values (2, '{$d['atleta']}', '{$d['nombreA']}', '{$d['apellidoA']}', '{$d['sexo']}', '$fecha', '{$d['movil']}', '{$d['correo']}', '{$d['club']}',
			'{$d['lic']}', '{$d['observ']}')";
	$conn->execute($q);
	$idPart = $conn->lastId();
	
	if ($d['atleta'] == 'N') {
		$q = "insert into representantes values (null, $idPart, '{$d['nombre']}', '{$d['apellido']}', '{$d['correo']}', '{$d['movil']}') ";
		$conn->execute($q);
	}
	
	$arrPrue = explode(",", $d['catego']);
	for ($i = 0; $i < count($arrPrue); $i++) {
		$q = "insert into registros values (null, $idPart, $arrPrue[$i], '{$d['marca1'.($i+1)]}', '{$d['marca2'.($i+1)]}', '{$d['marca3'.($i+1)]}')";
		$conn->execute($q);
	}
	
	
	echo "Datos correctamente guardados";
	$mensaje = $idiom[32];

	$headers = 'From: info@bilbaoatletismosantutxu.com' . "\r<br>\n" .
		 'Reply-To: info@bilbaoatletismosantutxu.comm' . "\r<br>\n" .
		 'X-Mailer: PHP/' . phpversion();
	$to      = $d['correo'];
	$subject = 'Inscripción';
	$correoMi .= "\n$mensaje";
	mail($to, $subject, $correoMi, $headers);

	
	
} else {
	switch ($d['dni']) {
	   case '1':
			$tipo = 'NIF';
		   break;
	   case '2':
			$tipo = 'DNI';
		   break;
	   default :
			$tipoT = 'Pasaporte';
		   break;
	}

	$fecha = explode('/', $d['fn']);
	$fecha = $fecha[2]."-".$fecha[1]."-".$fecha[0];

	$query = "insert into participantes value (null, '$tipo', '{$d['valdni']}', '$tipoT', '{$d['valdniT']}', '{$d['catego']}', null, '{$d['nombre']}',
						'{$d['apellido']}', '{$d['sexo']}', '$fecha', '{$d['direccion']}', '{$d['localidad']}', '{$d['cp']}', '{$d['provincia']}', '{$d['pais']}', 
						'{$d['nacional']}', '{$d['tel']}', '{$d['movil']}', '{$d['correo']}', '{$d['club']}', '{$d['lic']}', '{$d['carnet']}', '{$d['pin']}' )";

	$conn->execute($query) or die(mysql_error());
	if(mysql_errno ()==0) {
		echo "Datos correctamente guardados";
		$mensaje = "Estimado/a atleta, queda confirmada tu inscripción.\n\n
	El día 10 puedes pasar por la carpa de secretaria situada en la  calle Gran Vía frente al edificio de la Diputación Foral, en la sección de recogida de dorsales,".
		"para retirarlos hasta 1 hora antes del inicio de la prueba.\n\n
	En el caso de existir algún error en los datos de la inscripción, nos pondremos de nuevo en contacto contigo.\n\n
	Si por cualquier motivo después de realizar tu inscripción tuvieras que modificar alguno de los datos o anular tu participación, por favor hazlo en la Web www.bilbaoatletismosantutxu.com en el formulario de ANULAR O MODIFICAR INSCRIPCIÓN.\n\n
	Muchas gracias por tu participación.";

		$headers = 'From: info@bilbaoatletismosantutxu.com' . "\r<br>\n" .
			 'Reply-To: info@bilbaoatletismosantutxu.comm' . "\r<br>\n" .
			 'X-Mailer: PHP/' . phpversion();
		$to      = $d['correo'];
		$subject = 'Inscripción';
		$correoMi .= "\n$mensaje";
		mail($to, $subject, $correoMi, $headers);

	}
	else echo "Hubo un error. Realice la inscripción nuevamente.";
}
?>
