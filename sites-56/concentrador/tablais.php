<?php
/* 
 * Fichero para ejecutar las tablas que manda Enriquito
 * para insertar Clientes y beneficiarios. Exportar a csv con (:)
 * como separador de campos
 * 
 */
define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include 'include/mysqli.php';
include_once( 'admin/adminis.func.php' );
require_once( 'include/hoteles.func.php' );

$temp = new ps_DB;

print_r($_FILES);
if(strlen($_FILES['fichero']['tmp_name']) > 3) {
	if ($_FILES['fichero']['error']) {
			  switch ($_FILES['fichero']['error']){
					   case 1: // UPLOAD_ERR_INI_SIZE
							echo"El archivo sobrepasa el limite autorizado por el servidor(archivo php.ini) !";
					   break;
					   case 2:
							echo "El archivo sobrepasa el limite autorizado en el formulario HTML !";
					   break;
					   case 3: // UPLOAD_ERR_PARTIAL
							echo "El envio del archivo ha sido suspendido durante la transferencia!";
					   break;
					   case 4: // UPLOAD_ERR_NO_FILE
							echo "El archivo que ha enviado tiene un tamaño nulo !";
					   break;
			  }
	} else {
		$ruta_destino = "desc/";
		$fich_fuente = "csvFuente.csv";
		
		copy($_FILES['fichero']['tmp_name'], $ruta_destino.$fich_fuente);
		$handle = fopen($ruta_destino.$fich_fuente, 'r');
		echo $handle;
		if ($handle) {
			$buffer = fgets($handle);
			while (!feof($handle)) {
				$arrBuff = explode(':', str_replace('"', '', $buffer));
				if (($arrBuff[0]*1) > 5) {
					if (count($arrBuff) > 15) { // inscribe a los clientes
						$data = array(
								'Id' 						=> $arrBuff[0],
								'Nombre' 					=> $arrBuff[1],
								'Apellido1' 				=> $arrBuff[2],
								'Apellido2' 				=> $arrBuff[3],
								'DocumentNumber' 			=> $arrBuff[4],
								'DocumentExpirationDate' 	=> $arrBuff[5],
								'Email' 					=> $arrBuff[6],
								'PhoneNumber' 				=> $arrBuff[7],
								'Country' 					=> $arrBuff[8],
								'Province' 					=> $arrBuff[9],
								'City' 						=> $arrBuff[10],
								'Address' 					=> $arrBuff[11],
								'PostalCode' 				=> $arrBuff[12],
								'CountryOfBirth' 			=> $arrBuff[13],
								'DateOfBirth' 				=> $arrBuff[14],
								'Gender' 					=> $arrBuff[15],
								'Profesion' 				=> $arrBuff[16],
								'MonthSalary' 				=> $arrBuff[17],
								'UsuarioCode' 				=> rtrim(rtrim(ltrim($arrBuff[18],' '),' '),"\n")
						);
					} else { // inscribe a los Beneficiarios
						$data = array(
								'Id' 						=> $arrBuff[1],
								'IdCliente' 				=> $arrBuff[0],
								'Nombre' 					=> $arrBuff[2],
								'Apellido1'					=> $arrBuff[3],
								'Apellido2' 				=> $arrBuff[4],
								'Phone' 					=> $arrBuff[5],
								'Address' 					=> $arrBuff[6],
								'City' 						=> $arrBuff[7],
								'CI' 						=> $arrBuff[8],
								'Reason' 					=> $arrBuff[9],
								'Relation' 					=> rtrim($arrBuff[10],"\n")
						);
					}
					
					$ch = curl_init('https://www.administracomercios.com/datInscr.php');
					curl_setopt($ch, CURLOPT_HEADER, false);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					
					foreach ($data as $key => $value) {
						$correoMi .= "$key => $value\n<br>";
					}
					$salidaCurl = curl_exec($ch);
					$curl_info = curl_getinfo($ch);
					$correoMi .= $salidaCurl."<br><br>";
					
					if(strlen(curl_error($ch))) $correoMi .= "Curl error: ".curl_error($ch)."<br>\n";
					
				}
				$buffer = fgets($handle);
			}
		}
	}
}

echo $correoMi;

?>

<form action="" method="post" enctype="multipart/form-data" >
<input type="file" name="fichero" id="fichero"  />
<input type="submit" value="Enviar" /> 
</form>