<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;

//print_r($_FILES);
//print_r($_REQUEST);
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
		$ruta_destino = "../desc/";
		$fich_fuente = "csvFuente.csv";
		$fich_destino = "csvDestino.csv";
//		echo $ruta_destino;
		copy($_FILES['fichero']['tmp_name'], $ruta_destino.$fich_fuente);
		$handle = fopen($ruta_destino.$fich_fuente, 'r');
		$handls = fopen($ruta_destino.$fich_destino, 'w');
		if ($handle) {
			if ($_REQUEST['banco'] == 1) { //se va a importar del BBVA
				$buffer = fgets($handle);
				if (strpos($buffer, ";")) { // el fichero viene con ;
					$buffer = str_replace('Moneda;Tipo;Estado', 'Moneda;Euro Equiv.;Tipo;Estado', $buffer);
					fwrite($handls, str_replace('Estado;Repudiable', 'Estado;Estado Concentrador;Repudiable', $buffer));
					$sepr = ";";
				} else {
					$buffer = str_replace('Moneda,Tipo,Estado', 'Moneda,Euro Equiv.,Tipo,Estado', $buffer);
					fwrite($handls, str_replace('Estado,Repudiable', 'Estado,Estado Concentrador,Repudiable', $buffer));
					$sepr = ",";
				}
				while (!feof($handle)) {
					$buffer = fgets($handle);
					$arrBuff = explode($sepr, $buffer);
	//				print_r($arrBuff);
					$id = quitaCom($arrBuff[3]);
					$str = '';
					$q = "select c.nombre, euroEquiv, case t.estado 
							when 'P' then 'En Proceso' 
							when 'A' then 'Aceptada' 
							when 'D' then 'Denegada' 
							when 'N' then 'No Procesada' 
							when 'B' then 'Anulada' 
							when 'V' then 'Devuelta' end estadoTr 
						from tbl_transacciones t, tbl_comercio c where c.idcomercio = t.idcomercio and idtransaccion like '%$id%'";
					$temp->query($q);
					(strlen($temp->f('nombre')) > 1) ? $comerc = $temp->f('nombre') : $comerc = ' - ';
					(strlen($temp->f('euroEquiv')) > 1) ? $EEquiv = $temp->f('euroEquiv') : $EEquiv = ' - ';
					(strlen($temp->f('estadoTr')) > 1) ? $estado = $temp->f('estadoTr') : $estado = ' - ';
	//				echo $q;
					for ($i=0;$i<count($arrBuff);$i++) {
						if ($i==1) $str .= $sepr.$comerc;
						elseif ($i == 3) $str .= $sepr.'"'.$arrBuff[$i].'"';
						else {
							if ($i==8) $str .= $sepr.$EEquiv;
							if ($i==10) $str .= $sepr.$estado;
							$strBuff = quitaCom($arrBuff[$i]);
							if (strlen($strBuff) == 0) $strBuff = " - ";
							$str .= $sepr.$strBuff;
						}
					}
					$str = substr($str, 1);
	//				$str = str_replace(',', '","', $str);
	//				$str = substr($str, 0, strlen($str)-1);
	//				echo($str);
					fwrite($handls, $str);
				}
			} else if ($_REQUEST['banco'] == 2) { //se va a importar del Sabadel o Caja Madrid
				$buffer = fgets($handle);
//				echo str_replace('n;Pedido;Tipo Pago;Importe (Euros);', 'n;Estado Concentrador;Pedido;Comercio;Tipo Pago;Importe (Euros);EuroEquiv;', $buffer);
				fwrite($handls, str_replace('n;Pedido;Tipo Pago;Importe (Euros);', 'n;Estado Concentrador;Pedido;Comercio;Tipo Pago;Importe (Euros);EuroEquiv;', $buffer));
				while (!feof($handle)) {
					$buffer = fgets($handle);
					$arrBuff = explode(";", $buffer);
//					print_r($arrBuff);
					$id = quitaCom($arrBuff[4]);
					$str = '';
					$q = "select c.nombre, euroEquiv, case t.estado 
							when 'P' then 'En Proceso' 
							when 'A' then 'Aceptada' 
							when 'D' then 'Denegada' 
							when 'N' then 'No Procesada' 
							when 'B' then 'Anulada' 
							when 'V' then 'Devuelta' end estadoTr 
						from tbl_transacciones t, tbl_comercio c where c.idcomercio = t.idcomercio and idtransaccion like '%$id%'";
					$temp->query($q);
					(strlen($temp->f('nombre')) > 1) ? $comerc = $temp->f('nombre') : $comerc = ' - ';
					(strlen($temp->f('euroEquiv')) > 1) ? $EEquiv = $temp->f('euroEquiv') : $EEquiv = ' - ';
					(strlen($temp->f('estadoTr')) > 1) ? $estado = $temp->f('estadoTr') : $estado = ' - ';
//					echo $q;
					for ($i=0;$i<count($arrBuff);$i++) {
						
						if ($i == 3) $str .= ';"'.$arrBuff[$i].'";'.$estado;
						else {
							if ($i==7) $str .= ";".$EEquiv;
							if ($i==5) $str .= ";".$comerc;
							$strBuff = quitaCom($arrBuff[$i]);
							if (strlen($strBuff) == 0) $strBuff = " - ";
							$str .= ";".$strBuff;
						}
					}
					$str = substr($str, 1);
	//				$str = str_replace(',', '","', $str);
	//				$str = substr($str, 0, strlen($str)-1);
	//				echo($str);
					fwrite($handls, $str);
				}
			} else if ($_REQUEST['banco'] == 4) { //se va a importar del Santander
				$Lin = 0;
				$arrElim = array(2,5);
				while (!feof($handle)) {
					$Arrbuffer = explode(';', fgets($handle));
					$buffer = '';
					$fec = explode('/', $Arrbuffer[0]);
					echo count($fec);
					if (count($fec) == 3 || $Lin == 0) {
						for ($i=0;$i<count($Arrbuffer);$i++){
							if (!in_array($i, $arrElim)) $buffer .= $Arrbuffer[$i].";";
							if ($i == 7) break 1;
							if ($i == 4) $id = $Arrbuffer[$i];
						}
						if ($Lin == 0) $buffer .= "Estado Concentrador;Comercio;Moneda;Importe;EuroEquiv;EuroEquivDev;\n";
						else {
							$q = "select case t.estado 
								when 'P' then 'En Proceso' 
								when 'A' then 'Aceptada' 
								when 'D' then 'Denegada' 
								when 'N' then 'No Procesada' 
								when 'B' then 'Anulada' 
								when 'V' then 'Devuelta' end, c.nombre, m.moneda, format(valor_inicial/100,2), euroEquiv, euroEquivDev
							from tbl_transacciones t, tbl_comercio c, tbl_moneda m where t.moneda = m.idmoneda and c.idcomercio = t.idcomercio and idtransaccion like '%$id%'";
							echo $q."<br>";
							$temp->query($q);
							$arrVls = $temp->loadRow();
							for ($j = 0; $j < count($arrVls); $j++) {
								$buffer .= $arrVls[$j].";";
							}
							$buffer .= "\n";
						}
	//					$buffer = str_replace('Tipo Operación;', '', str_replace('Tipo Pago;', '', $buffer));
	//					echo substr($buffer, 0, strpos($buffer, ';NºTarjeta'))."<br>";
	//					if ($Lin == 7) {echo $buffer;fwrite($handls, $buffer);exit;}
						fwrite($handls, $buffer);
					}
					$Lin ++;
				}
				
			} else if ($_REQUEST['banco'] == 3) { //se va a importar del Santander
				$Lin = 0;
				$arrElim = array(2,5);
				while (!feof($handle)) {
					$Arrbuffer = explode(';', fgets($handle));
					$buffer = '';
					for ($i=0;$i<count($Arrbuffer);$i++){
						
						if (!in_array($i, $arrElim)) $buffer .= $Arrbuffer[$i].";";
						if ($i == 7) break 1;
						if ($i == 4) $id = $Arrbuffer[$i];
					}
					if ($Lin == 0) $buffer .= "Estado Concentrador;Comercio;Moneda;Importe;EuroEquiv;EuroEquivDev;\n";
					else {
						$q = "select case t.estado 
							when 'P' then 'En Proceso' 
							when 'A' then 'Aceptada' 
							when 'D' then 'Denegada' 
							when 'N' then 'No Procesada' 
							when 'B' then 'Anulada' 
							when 'V' then 'Devuelta' end, c.nombre, m.moneda, format(valor_inicial/100,2), euroEquiv, euroEquivDev
						from tbl_transacciones t, tbl_comercio c, tbl_moneda m where t.moneda = m.idmoneda and c.idcomercio = t.idcomercio and idtransaccion like '%$id%'";
//						echo $q;
						$temp->query($q);
						$arrVls = $temp->loadRow();
						for ($j = 0; $j < count($arrVls); $j++) {
							$buffer .= $arrVls[$j].";";
						}
						$buffer .= "\n";
					}
//					$buffer = str_replace('Tipo Operación;', '', str_replace('Tipo Pago;', '', $buffer));
//					echo substr($buffer, 0, strpos($buffer, ';NºTarjeta'))."<br>";
//					if ($Lin == 7) {echo $buffer;fwrite($handls, $buffer);exit;}
					fwrite($handls, $buffer);
					$Lin ++;
				}
				
			}
				//echo $buffer."<br>";
			fclose($handle);
			fclose($handls);
			echo "<script type='text/javascript'>window.open('".$ruta_destino.$fich_destino."')</script>";
		}
		
	}
}

function quitaCom($str) {
	return str_replace(" ", "",  str_replace("'", "", $str));
}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_CMPTRN;
$html->tituloTarea = '&nbsp;';
$html->anchoTabla = 350;
$html->tabed = true;
$html->anchoCeldaI = 105;
$html->anchoCeldaD = 210;

$html->inHide('../desc', 'UPLOAD_TMP_DIR');
$html->inTextoL('<input type="file" name="fichero" id="fichero"  />');
$valInicio = array(array('1', 'BBVA'), array('2', 'Sabadel y Caja Madrid'), array('3', 'Santander'), array('4', 'Bankia'));
$html->inSelect('Banco', 'banco', 3, $valInicio, '1');


echo $html->salida();

//echo CURL_TIMECOND_LASTMOD;
?>
