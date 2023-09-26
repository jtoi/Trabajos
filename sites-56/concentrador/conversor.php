<?php
/*
 * Convierte los ficheros TRANSACTIONS.TXT de Caixa en ficheros Excell
 * para mejorar su entendimiento 
 * 
 */

$error = 1;
ini_set('display_errors', $error);
error_reporting($error);
header("Cache-Control: no-cache");
header("Pragma: no-cache");
	
define('_VALID_ENTRADA', 1);
include_once 'configuration.php';
include_once 'include/mysqli.php';
include_once 'include/correo.php';
$temp = new ps_DB();


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
		$handle = fopen($_FILES['fichero']['tmp_name'], 'r');
		if ($handle) {
			$ruta_destino = "desc/";
			$fich_destino = "Convertido.csv";
			$salida = $salidaL = "";
			$finLin = "\n\r";
			$pase=0;
			while (!feof($handle)) {
				$buffer = fgets($handle);
				if (substr($buffer, 0, 2) == '10') {
					if ($pase!=1) $salida .= "Tipo Registro;Fecha Proceso;Fecha Inicio;Fecha Fin".$finLin;$pase = 1;
					$salida .= substr($buffer, 0, 2).";".substr($buffer, 2, 10).";".substr($buffer, 12, 10).";".substr($buffer, 22, 10).$finLin;
				} elseif(substr($buffer, 0, 2) == '00') {
					if ($pase!=2) $salida .= "Tipo Registro;Número Contrato;FUC;Número Cta.;Oficina Gestora;Fecha Proceso;Fecha Inicio;Fecha Fin".$finLin;$pase=2;
					$salida .= substr($buffer, 0, 2).";".substr($buffer, 2, 18).";".substr($buffer, 20, 10).";".substr($buffer, 30, 18).";".substr($buffer, 48, 4).";"
							.substr($buffer, 52, 10).";".substr($buffer, 62, 10).";".substr($buffer, 72, 10).$finLin;
				} elseif(substr($buffer, 0, 2) == '01') {
					if ($pase!=3) {
						$salida .= "Tipo Registro;Fecha Liq.;Número Remesa;Número Factura;Oficina Remesa;" 	//5
							. "Número Tarj.;Tipo Tarj.;Fecha Operac.;Hora Oper.;Autorizo;"					//10
							. "Operac.;Tipo captura;Importe Liq.;% Desc.;Importe Desc.;"					//15
							. "Importe Abono;# del TPV;Código moneda Liq.;# Operac.;Cod. razón Cgbak;"		//20
							. "Importe mon. orig.;Código mon Orig.;Operacion;Comercio;Moneda;"				//25
							. "Val Inic.;Val Euros;Fecha;Hora".$finLin;										//29
					}
					$pase=3;
					$codigo = substr($buffer, 64, 6);
					$ident = substr($buffer, 165, 12);
					if (substr($buffer, 194, 3)*1 > 0) {
						$q = "select moneda from tbl_moneda where idmoneda = ".substr($buffer, 194, 3);
						$temp->query($q);
						$mon = $temp->f('moneda');
					} else $mon = 'EUR';
					$salida .= substr($buffer, 0, 2).";".substr($buffer, 2, 10).";".substr($buffer, 12, 5).";".substr($buffer, 17, 3).";" 	//4
							.substr($buffer, 20, 4).";".substr($buffer, 24, 22).";".substr($buffer, 46, 2).";".substr($buffer, 48, 10).";" 	//8
							.substr($buffer, 58, 2).":".substr($buffer, 60, 2).":".substr($buffer, 62, 2).";'".$codigo."';"					//10
							.substr($buffer, 70, 2).";".substr($buffer, 72, 3).";".(substr($buffer, 75, 11)/100).";"						//13
							.substr($buffer, 86, 5).";".(substr($buffer, 91, 9)/100).";".(substr($buffer, 100, 13)/100).";"					//16
							.substr($buffer, 113, 11).";".substr($buffer, 162, 3).";".$ident.";".substr($buffer, 177, 2).";"				//20
							.(substr($buffer, 181, 13)/100).";".$mon;																		//22
					if (substr($ident,0,2) == 14)
						$q = "select idtransaccion, format(t.valor_inicial/100,2) val, t.euroEquiv, c.nombre, m.moneda "
								. " from tbl_transacciones t, tbl_comercio c, tbl_moneda m "
								. " where m.idmoneda = t.moneda and t.idcomercio = c.idcomercio and idtransaccion = '$ident'";
					else
						$q = "select idtransaccion, format(t.valor_inicial/100,2) val, t.euroEquiv, c.nombre, m.moneda "
								. "from tbl_transacciones t, tbl_comercio c, tbl_moneda m "
								. " where m.idmoneda = t.moneda and t.idcomercio = c.idcomercio and t.pasarela in "
								. "(select idpasarela from tbl_colPasarBancos c where c.idbanco = 9) "
								. "and codigo = '$codigo'";
//  					echo "<br>".$q;
					$temp->query($q);
					if ($temp->getNumRows() > 1) {
						$q = "select idtransaccion, format(t.valor_inicial/100,2) val, t.euroEquiv, c.nombre, m.moneda "
								. "from tbl_transacciones t, tbl_comercio c, tbl_moneda m "
								. " where m.idmoneda = t.moneda and t.idcomercio = c.idcomercio and t.pasarela in "
										. "(select idpasarela from tbl_colPasarBancos c where c.idbanco = 9) "
												. "and codigo = '$codigo' and from_unixtime(t.fecha_mod,'%d-%m-%Y') = '".substr($buffer, 48, 10)."'";
						$temp->query($q);
					}
// 					var_dump($temp->getNumRows());
					if ($temp->getNumRows() == 0) echo $codigo."<br>";
					$valI = $temp->f('val');
					$eurE = $temp->f('euroEquiv');
					$come = $temp->f('nombre');
					$ident = $temp->f('idtransaccion');
					$mon = $temp->f('moneda');
					$salida .= ";$ident;$come;$mon;$valI;$eurE;".substr($buffer, 48, 10).";".substr($buffer, 58, 2).":"
							.substr($buffer, 60, 2).":".substr($buffer, 62, 2).$finLin;
				} elseif(substr($buffer, 0, 2) == '99') {
					if ($pase!=4) $salida .= "Tipo Registro;Total Operc.;Importe Total Euros.".$finLin;$pase=4;
					$salida .= substr($buffer, 0, 2).";".substr($buffer, 27, 9).";".substr($buffer, 49, 1).(substr($buffer, 36, 14)/100).$finLin;
				} elseif(substr($buffer, 0, 2) == '90') {
					if ($pase!=5) $salida .= "Tipo Registro;Total núm. comercios;Total Operac.;Importe Tot Euros".$finLin;$pase=5;
					$salida .= substr($buffer, 0, 2).";".substr($buffer, 2, 9).";".substr($buffer, 36, 9).";"
								.substr($buffer, 58, 1).(substr($buffer, 45, 14)/100).$finLin;
				}
				
				//Para el fichero de Leire
				
			}
			
		}
	}

	$handls = fopen($ruta_destino.$fich_destino, 'w');
	fwrite($handls,$salida);
	fclose($handle);
	fclose($handls);
	echo "<script type='text/javascript'>window.open('".$ruta_destino.$fich_destino."')</script>";
}


?>

<div id="rep" style="border: 1px solid #3A3A3A;padding: 5px; width: 500px;margin: 20px auto;font-family: arial">
	<form action="" enctype="multipart/form-data" method="post">
		<span style="font-size: 13px;font-weight: bold;display: block;text-align: center;margin-bottom: 10px;">Conversor de Ficheros TRANSACTION.TXT enviados por Caixa</span>
		<input id="fichero" type="file" name="fichero"><br /><br />
		<input type="submit" value="Enviar">
	</form>
</div>