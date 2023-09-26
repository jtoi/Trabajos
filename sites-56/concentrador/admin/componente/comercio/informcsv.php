<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;

//print_r($_FILES);
$ruta_destino = "desc/";
$fich_fuente = "csvFuente.csv";
$fich_destino = "csvDestino.csv";

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
        unlink($ruta_destino.$fich_fuente);
        unlink($ruta_destino.$fich_destino);
//		echo $ruta_destino;
		copy($_FILES['fichero']['tmp_name'], $ruta_destino.$fich_fuente);
		$handle = fopen($ruta_destino.$fich_fuente, 'r');
		$handls = fopen($ruta_destino.$fich_destino, 'w');
//        echo $handls;exit;
		if ($handle) {
			while (!feof($handle)) {
				$buffer = fgets($handle);
				if (strpos($buffer, ';ARN;;') > 0) {
					fwrite($handls, str_replace(';ARN;;', ';ARN;COMERCIO NO.;EURO EQUIV;EURO EQUIV DEV;;', $buffer));
					while (!feof($handle)) {
						$buffer = fgets($handle);
						$arrBuff = explode(";", $buffer);
						$str = '';
						if (strlen($arrBuff[4]) > 3) {
                            if (stristr($arrBuff[4], 'USD')) {
    							$id = str_replace('USD', '', $arrBuff[4]);
                            } elseif (stristr($arrBuff[4], 'GBP')) {
                                $id = str_replace('GBP', '', $arrBuff[4]);
                            } else $id = $arrBuff[4];
							$q = "select c.nombre, euroEquiv, euroEquivDev from tbl_transacciones t, tbl_comercio c where c.idcomercio = t.idcomercio and ";
                            if (stristr($arrBuff[3], 'DEV.') > -1) $q .= "idtransaccionMod like '%$id%'";
                            else $q .= "idtransaccion like '%$id%'";
//                            echo $q;exit;
							$temp->query($q);
							$comerc = $temp->f('nombre');
							$tasa = $temp->f('euroEquiv');
							$dev = $temp->f('euroEquivDev');
							for ($i=0;$i<11;$i++) {
								$str .= $arrBuff[$i].";";
							}
							fwrite($handls, str_replace(';;', ';', $str).$comerc.';'.$tasa.';'.$dev.';;'."\n");
						} else break;
					}
					$temp->query($q);
				} else {
					fwrite($handls, str_replace("\n", ";\n", $buffer));
				}
				//echo $buffer."<br>";
			}
			fclose($handle);
			fclose($handls);
			echo "<script type='text/javascript'>window.open('".$ruta_destino.$fich_destino."')</script>";
		}
		
	}
}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_IFRCSV;
$html->tituloTarea = '&nbsp;';
$html->anchoTabla = 350;
$html->tabed = true;
$html->anchoCeldaI = 105;
$html->anchoCeldaD = 210;

$html->inHide($ruta_destino, 'UPLOAD_TMP_DIR');
$html->inTextoL('<input type="file" name="fichero" id="fichero"  />');


echo $html->salida();

//echo CURL_TIMECOND_LASTMOD;
?>
