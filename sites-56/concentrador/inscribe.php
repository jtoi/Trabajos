<?php
ini_set('display_errors', 0);
//error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('_VALID_ENTRADA', 1);
require_once( 'configuration.php' );
include 'include/mysqli.php';

$temp = new ps_DB;
$d = $_POST;
print_r($d);
if ($d['Nombre'] || $d['Nombreb'] ) {
	if (strlen($d['Idb']) > 2) {echo "entraca";
		$data = array(
				'Id' 						=> $d['Idb'],
				'IdCliente' 				=> $d['IdCliente'],
				'Nombre' 					=> $d['Nombreb'],
				'Apellido1'					=> $d['Apellido1b'],
				'Apellido2' 				=> $d['Apellido2b'],
				'Phone' 					=> $d['Phoneb'],
				'Address' 					=> $d['Addressb'],
				'City' 						=> $d['Cityb'],
				'CI' 						=> $d['CI'],
				'Relation' 					=> $d['Relation'],
				'Reason' 					=> $d['Reason']
		);
	} else {
		$data = array(
				'Id' 						=> $d['Id'],
				'Nombre' 					=> $d['Nombre'],
				'Apellido1' 				=> $d['Apellido1'],
				'Apellido2' 				=> $d['Apellido2'],
				'DocumentNumber' 			=> $d['DocumentNumber'],
				'DocumentExpirationDate' 	=> $d['DocumentExpirationDate'],
				'Email' 					=> $d['Email'],
				'PhoneNumber' 				=> $d['PhoneNumber'],
				'Country' 					=> $d['Country'],
				'Province' 					=> $d['Province'],
				'City' 						=> $d['City'],
				'Address' 					=> $d['Address'],
				'PostalCode' 				=> $d['PostalCode'],
				'CountryOfBirth' 			=> $d['CountryOfBirth'],
				'DateOfBirth' 				=> $d['DateOfBirth'],
				'Gender' 					=> $d['Gender'],
				'Profesion' 				=> $d['Profesion'],
				'MonthSalary' 				=> $d['MonthSalary'],
				'UsuarioCode' 				=> $d['UsuarioCode']
		);
	}

	$ch = curl_init('https://www.administracomercios.com/datInscr.php');
// 	$ch = curl_init('http://localhost/concentrador/datInscr.php');
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$correoMi .= "\n<br>";
	
	foreach ($data as $key => $value) {
		$correoMi .= "$key => $value\n<br>";
	}
	
	while (strlen($salidaCurl) == 0 && $i < 4) {
		$salidaCurl = curl_exec($ch);
		$curl_info = curl_getinfo($ch);
	
		foreach ($curl_info as $key => $value) {
			$correoMi .=  $key." = ".$value."<br>\n";
		}
	
		if(strlen(curl_error($ch))) $correoMi .= "Curl error: ".curl_error($ch)."<br>\n";
	
		echo "Enviado a TefPay, envío $i - ".htmlspecialchars_decode($salidaCurl)."<br>\n";
		$i++;
	}
	curl_close($ch);
} 
?>
<form action="" method="post" >
<h3>Cliente</h3>
Id: <input type="text" name="Id" value=""><br>
Nombre: <input type="text" name="Nombre" value=""><br>
Apellido1: <input type="text" name="Apellido1" value=""><br>
Apellido2: <input type="text" name="Apellido2" value=""><br>
DocumentNumber: <input type="text" name="DocumentNumber" value=""><br>
DocumentExpirationDate: <input type="text" name="DocumentExpirationDate" value=""><br>
Email: <input type="text" name="Email" value=""><br>
PhoneNumber: <input type="text" name="PhoneNumber" value=""><br>
Country: <input type="text" name="Country" value=""><br>
Province: <input type="text" name="Province" value=""><br>
City: <input type="text" name="City" value=""><br>
Address: <input type="text" name="Address" value=""><br>
PostalCode: <input type="text" name="PostalCode" value=""><br>
CountryOfBirth: <input type="text" name="CountryOfBirth" value=""><br>
DateOfBirth: <input type="text" name="DateOfBirth" value=""><br>
Gender: <input type="text" name="Gender" value=""><br>
Profesion: <input type="text" name="Profesion" value=""><br>
MonthSalary: <input type="text" name="MonthSalary" value=""><br>
UsuarioCode: <input type="text" name="UsuarioCode" value=""><br>
<input type="submit" value="Enviar">
</form>
<form action="" method="post" >
<h3>Beneficiario</h3>
Id: <input type="text" name="Idb" value="320"><br>
IdCliente: <input type="text" name="IdCliente" value=""><br>
Nombre: <input type="text" name="Nombreb" value="ADA"><br>
Apellido1: <input type="text" name="Apellido1b" value="INFANTES"><br>
Apellido2: <input type="text" name="Apellido2b" value="RODRIGUEZ"><br>
Phone: <input type="text" name="Phoneb" value="31398606"><br>
Address: <input type="text" name="Addressb" value="J.R NAPOLES N.30-A"><br>
City: <input type="text" name="Cityb" value="Las Tunas"><br>
CI: <input type="text" name="CI" value="52110708633"><br>
Relation: <input type="text" name="Relation" value="2"><br>
Reason: <input type="text" name="Reason" value="1"><br>
<input type="submit" value="Enviar">
</form>
