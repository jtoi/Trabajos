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
	$mensaje = "Estimado/a atleta, queda confirmada tu inscripci�n.\n\n
El d�a 10 puedes pasar por la carpa de secretaria situada en la  calle Gran V�a frente al edificio de la Diputaci�n Foral, en la secci�n de recogida de dorsales, para retirarlos hasta 1 hora antes del inicio de la prueba.\n\n
En el caso de existir alg�n error en los datos de la inscripci�n, nos pondremos de nuevo en contacto contigo.\n\n
Si por cualquier motivo despu�s de realizar tu inscripci�n tuvieras que modificar alguno de los datos o anular tu participaci�n, por favor hazlo en  la Web www.bilbaoatletismosantutxu.com en el formulario de ANULAR O MODIFICAR INSCRIPCI�N.\n\n
Muchas gracias por tu participaci�n.";
	
	$headers = 'From: info@bilbaoatletismosantutxu.com' . "\r<br>\n" .
		 'Reply-To: info@bilbaoatletismosantutxu.com' . "\r<br>\n" .
		 'X-Mailer: PHP/' . phpversion();
	$to      = $d['correo'];
	$subject = 'Inscripci�n';
	$correoMi .= "\n$mensaje";
	mail($to, $subject, $correoMi, $headers);
	
	}
else echo "Hubo un error. Realice la inscripci�n nuevamente."
?>
