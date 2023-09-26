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
//	print_r($d);
//	print_r($d['nombre']);

for ($i=0;$i<count($d['nombre']);$i++) {
//		inserta atleta
	$q = "insert into participantes (idevento, atleta, nombre, sexo, fnac, licencia_num, tipoDoc, doc, idprueba) values".
			"(5, 'S', '".$d['nombre'][$i]."', 'F', '".$d['fn'][$i]."-01-01', '".$d['lic'][$i]."','DNI', '".$d['dni'][$i]."', ".$d['catego'].")";
	$conn->execute($q);
	$idPart = $conn->lastId();
//		echo $q;
	$q = "insert into representantes (idparticipante, nombre, correo, tel) values ($idPart, '{$d['nombR']}', '{$d['correo']}', '{$d['movil']}')";
//		echo $q;
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
//	mail($to, $subject, $correoMi, $headers);

?>
