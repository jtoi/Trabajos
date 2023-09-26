<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define('_VALID_ENTRADA', 1);
require_once("classes/SecureSession.class.php");
$Session = new SecureSession(7500);
include_once( '../configuration.php' );
include_once( 'classes/entrada.php' );
require_once( '../include/mysqli.php' );
global $temp;
$temp = new ps_DB();
include_once( 'adminis.func.php' );
include_once( '../include/hoteles.func.php' );

//$temp = new ps_DB;

//borra los ficheros previamente existentes
$dirDesc = '../desc/';
$dir = opendir($dirDesc);
while($fich=readdir($dir)){
	if ($fich != 'index.php' && !is_dir($fich)) {
		if (filemtime($dirDesc.$fich) < (time()-60)) unlink($dirDesc.$fich);
	}
}
closedir($dir);
// print_r($_REQUEST);

$file = trIdent('01').".csv";
$urlPassCSV = "../desc/".$urlPass.$file;
$pase = false;

if ($_REQUEST['xls']) {
	$urlPassCSV = str_replace(".csv", ".xls", $file);
	$q = str_replace(", comercio", "", str_replace("\t", "", str_replace("\r\n", " ", $_REQUEST['xls'])));
//	echo "Hola= $q";
	$temp->query($q);
	$arrRes = $temp->loadAssocList();
	
//	print_r($arrRes);
	header("Content-Disposition: attachment; filename=\"$urlPassCSV\"");
	header("Content-Type: application/vnd.ms-excel");
	
	$flag = false;
	$cons = 1;
	echo "<table border='1' cellpadding='2' cellspacing='0' width='100%'>";
	foreach($arrRes as $row) {
		if(!$flag) {
			// display field/column names as first row
//			echo implode("\t", array_keys($row)) . "\n";
			echo "<tr><td>NUM_COMP</td><td>FECH_CONT</td><td>TIT_COMP</td><td>COD_MONE</td><td>TASA</td><td>Descrip_General</td>"
			. "<td>COD_CUEN</td><td>COD_CTGT</td><td>DEBI</td><td>IMPORTE</td><td>DOCU</td><td>FECH_VALO</td><td>COD_ACRE_DEUD</td><td>DESCRIP</td></tr>";
			$flag = true;
		}
		array_walk($row, '\cleanData');
		$arrDoc = explode("|", $row['DOCU']);
//		echo ($cons)."\t".$row['FECH_CONT']."\tCOBRO PASARELA BIDAIONDO\t".$row['COD_MONE']."\t001\t \t 1352002099203000\t \tD\t".number_format(($row['IMPORTE']*.97),2,",","")."\t".$arrDoc[0]."\t".$row['FECH_VALO']."\t99203000\t".$row['DESCRIPCION']."\n";
//		echo ($cons++)."\t".$row['FECH_CONT']."\tCOBRO PASARELA BIDAIONDO\t".$row['COD_MONE']."\t001\t \t4302TTO01563\t \tH\t".number_format(($row['IMPORTE']*.97),2,",","")."\t".$arrDoc[1]."\t".$row['FECH_VALO']."\tTTO01563\t".$row['DESCRIPCION']."\n";
		
		echo "<tr><td>1</td><td>".$row['FECH_CONT']."</td><td>COBRO PASARELA BIDAIONDO</td><td>".$row['COD_MONE']."</td><td style='mso-number-format:\"@\";'>001</td><td></td><td style=\"mso-number-format:'@';\">1352002099203000</td><td></td><td>D</td><td style='mso-number-format:\"0.00\";'>".number_format(($row['IMPORTE']*1),2,",","")."</td><td style='mso-number-format:\"@\";'>".$arrDoc[0]."</td><td>".$row['FECH_CONT']."</td><td style='mso-number-format:\"@\";'>99203000</td><td>".$row['DESCRIPCION']."</td></tr>";
		echo "<tr><td>1</td><td>".$row['FECH_CONT']."</td><td>COBRO PASARELA BIDAIONDO</td><td>".$row['COD_MONE']."</td><td style=\"mso-number-format:'@';\">001</td><td></td><td style=\"mso-number-format:'@';\">4302TTO01563</td><td></td><td>H</td><td style='mso-number-format:\"0.00\";'>".number_format(($row['IMPORTE']/1.03),2,",","")."</td><td style='mso-number-format:\"@\";'>".$arrDoc[1]."</td><td>".$row['FECH_CONT']."</td><td>TTO01563</td><td>".$row['DESCRIPCION']."</td></tr>";
//		echo implode("\t", array_values($row)) . "\n<br>";
//		echo implode("\t", array_values($row)) . "\n<br>";
	}
	echo "</table>";
	exit;
	
}

if ($_REQUEST['querys21']) { //exporta el reporte transacciones tipo CSV2
	if ($_REQUEST['pag'] == 'reporte') {
		$urlPassCSV = str_replace(".csv", ".xls", $file);
		$conte =  "<table border='1' cellpadding='2' cellspacing='0' width='100%'>";
	
		$conte .=  "<tr><td>No.</td><td>Id</td><td>Comercio</td><td>Referencia del comercio</td><td>Banca</td><td>Cliente</td><td>Referencia Banco</td><td>Fecha</td><td>Valor Inicial</td><td>Valor Devuelto</td><td>Valor</td><td>Moneda</td><td>Error</td><td>Estado</td><td>Tasa deCambio</td><td>Conversión Euro</td><td>IP</td><td>País</td><td>Fecha Mod.</td><td>Tipo Oper.</td><td>Tipo Pago</td>" ;

		if (strpos($_REQUEST['querys21'], 'tbl_aisOrden') > 2)
			$conte .= "<td>Orden Titanes</td>" ;
		$conte .= "</tr>" ;

// echo str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys2']));exit;
		$temp->query(str_replace("{tot}", "", str_replace('{val}', '', str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys21'])))));
// 		echo $temp->num_rows();
		$arrRes = $temp->loadAssocList();
	//	print_r($arrRes);
		header("Content-Disposition: attachment; filename=\"$urlPassCSV\"");
		header("Content-Type: application/vnd.ms-excel");

		$i=1;
		foreach($arrRes as $row) {
			$pai = '';
			if (function_exists(geoip_country_name_by_name)) $pai = geoip_country_name_by_name($row['ip']);

			
			
			$conte .=  "<tr>
				<td>".$i++."</td>
				<td style='mso-number-format:\"@\";'>&#8203;".$row['id']."</td>
				<td>".$row['comercio']."</td>
				<td>".$row['identificador']."</td>
				<td style='mso-number-format:\"@\";'>".$row['pasarelaN']."</td>
				<td>".html_entity_decode($row['cliente'],ENT_QUOTES)."</td>
				<td style='mso-number-format:\"@\";'>&#8203;".$row['codigo']."</td>
				<td style=\"mso-number-format:'@';\">".date('d/m/Y H:i:s',$row['fecha'])."</td>
				<td style='mso-number-format:\"0.00\";'>".number_format(($row['valIni']*1),2,",","")."</td>
				<td style='mso-number-format:\"0.00\";'>".number_format($row['valorDev'],2)."</td>
				<td style='mso-number-format:\"0.00\";'>".number_format(($row['valor']*1),2,",","")."</td>
				<td style=\"mso-number-format:'@';\">".$row['moneda']."</td>
				<td style=\"mso-number-format:'@';\">".$row['error']."</td>
				<td style=\"mso-number-format:'@';\">".$row['estad']."</td>
				<td style='mso-number-format:\"0.0000\";'>".$row['tasa']."</td>";
	
			if ($row['tasa'] == 0) 
				$conte .= "<td style='mso-number-format:\"0.00\";'>0</td>"; 
			else 
				$conte .= "<td style='mso-number-format:\"0.00\";'>". number_format(($row['valor']/$row['tasa']),2) ."</td>";

			$conte .= "<td style='mso-number-format:\"@\";'>&#8203;".$row['ip']."</td>
			<td style=\"mso-number-format:'@';\">$pai</td>
			<td style=\"mso-number-format:'@';\">".date('d/m/Y H:i:s',$row['fecha_mod'])."</td>
				<td style=\"mso-number-format:'@';\">".$row['tipo']."</td>;
				<td style=\"mso-number-format:'@';\">".$row['tipopago']."</td>";
			if (strpos($_REQUEST['querys61'], 'tbl_aisOrden') > 2)
				$conte .= "<td style='mso-number-format:\"@\";'>&#8203;".$row['titord']."</td>";
			$conte .= "</tr>";
		}

		echo $conte ."</table>";
		exit;
	}

}

if ($_REQUEST['querys61']) {
	$urlPassCSV = str_replace(".csv", ".xls", $file);
	$conte =  "<table border='1' cellpadding='2' cellspacing='0' width='100%'>";

	$conte .=  "<tr><td>No.</td><td>Id</td><td>Comercio</td><td>Referencia del comercio</td><td>Banca</td><td>Cliente</td><td>Referencia Banco</td><td>Fecha Mod</td><td>Valor Inicial</td><td>Conversión Euro (VI)</td><td>Tasa deCambio (VI)</td><td>Fecha Devol</td><td>Valor Devuelto</td><td>Conversión Euros (VD)</td><td>Tasa de cambio(VD)</td><td>Fecha</td><td>Moneda</td><td>Valor</td><td>Estado</td><td>Empresa</td><td>Tipo Oper.</td><td>T. Tarjeta</td>" ;

	//Si aparecen las operaciones de Titanes agrego la columna
	if (strpos($_REQUEST['querys61'], 'tbl_aisOrden') > 2)
			$conte .= "<td>Orden Titanes</td>" ;
		
	//error_log("grupoRol=".$_SESSION['grupo_rol']);
	if ($_SESSION['grupo_rol'] < 2 || $_SESSION['rol'] == 19) { //Si es del grupo de bidaiondo
		$_REQUEST['querys61'] = str_replace("tjta from tbl_tarjetas j, tbl_transacciones t,", "tjta, (mtoMonBnc/100) eurBnc from tbl_tarjetas j, tbl_transacciones t,", $_REQUEST['querys61']);
		
		$conte .= "<td>Euros en el Banco</td>" ;
	}
	$conte .= "</tr>" ;

	$q = str_replace(", comercio", "", str_replace("\t", "", str_replace("\r\n", " ", $_REQUEST['querys61'])));
	// echo "Hola= $q";
	$temp->query($q);
	$arrRes = $temp->loadAssocList();
//	print_r($arrRes);
	header("Content-Disposition: attachment; filename=\"$urlPassCSV\"");
	header("Content-Type: application/vnd.ms-excel");

	$i=1;
	foreach($arrRes as $row) {
		
		$conte .=  "<tr>
			<td>".$i++."</td>
			<td style='mso-number-format:\"@\";'>&#8203;".$row['id']."</td>
			<td>".$row['comercio']."</td>
			<td>".$row['identificador']."</td>
			<td style='mso-number-format:\"@\";'>".$row['pasarelaN']."</td>
			<td>".html_entity_decode($row['cliente'],ENT_QUOTES)."</td>
			<td style='mso-number-format:\"@\";'>&#8203;".$row['codigo']."</td>
			<td style=\"mso-number-format:'@';\">".date('d/m/Y H:i:s',$row['fecha_mod'])."</td>
			<td style='mso-number-format:\"0.00\";'>".number_format(($row['valIni{val}']*1),2,",","")."</td>
			<td style='mso-number-format:\"0.00\";'>".number_format(($row['valIni{val}']*1 / $row['tasaM']),2)."</td>
			<td style='mso-number-format:\"0.0000\";'>".$row['tasaM']."</td>
			<td style=\"mso-number-format:'@';\">".date('d/m/Y H:i:s',$row['fecha_mod'])."</td>
			<td style='mso-number-format:\"0.00\";'>".number_format($row['valorDev'],2)."</td>";

		if ($row['tasaDev'] == 0) 
			$conte .= "<td style='mso-number-format:\"0.00\";'>0</td>"; 
		else 
			$conte .= "<td style='mso-number-format:\"0.00\";'>". number_format(($row['valorDev']/$row['tasaDev']),2) ."</td>";
		$conte .= "<td style='mso-number-format:\"0.0000\";'>".number_format($row['tasaDev'],4)."</td>
		<td style=\"mso-number-format:'@';\">".date('d/m/Y H:i:s',$row['fecha'])."</td>
			<td style=\"mso-number-format:'@';\">".$row['moneda']."</td>
			<td style='mso-number-format:\"0.00\";'>".number_format($row['valor'],2)."</td>
			<td style=\"mso-number-format:'@';\">".$row['estad']."</td>
			<td style=\"mso-number-format:'@';\">".$row['Empresa']."</td>
			<td style=\"mso-number-format:'@';\">".$row['tipo']."</td>
			<td style=\"mso-number-format:'@';\">".$row['tjta']."</td>";
		if (strpos($_REQUEST['querys61'], 'tbl_aisOrden') > 2)
			$conte .= "<td style='mso-number-format:\"@\";'>&#8203;".$row['titord']."</td>";
		if ($_SESSION['grupo_rol'] < 2 || $_SESSION['rol'] == 19) //Si es del grupo de bidaiondo
			$conte .= "<td style='mso-number-format:\"0.00\";'>".number_format($row['eurBnc'],2)."</td>";
		$conte .= "</tr>";
	}
	echo $conte ."</table>";
	exit;
	
}

if ($_REQUEST['querys11']) {
	pasaAExcell($_REQUEST['inSql']);
	exit;
}

function pasaAExcell($entra){
// echo $entra; exit;

	include_once( 'classes/PHPExcel.php' );
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Bidaiondo SL")
								 ->setLastModifiedBy("Bidaiondo SL");
	if (strlen($entra) > 10) {
		$lineas = explode("{n}",$entra);
	$numero = 1;
		foreach ($lineas as $linea) {
			$celdas = explode(";", $linea);
	$letra = "A";
			foreach ($celdas as $celda) {
				$objPHPExcel->setActiveSheetIndex(0)
        			->setCellValue($letra.$numero, $celda);
				$letra++;
			}
			$numero++;
		}
	}
	$objPHPExcel->getActiveSheet()->setTitle('Datos');
	$objPHPExcel->setActiveSheetIndex(0);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Datos Varios '.date('d/m/Y H:i').'.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
}

if ($_REQUEST['querys']) { //exporta el reporte transacciones tipo CSV1
	if ($_REQUEST['pag'] == 'reporte') {
		$conte = "Id,Comercio,Referencia del comercio,Cliente,Referencia del Banco,Banca,Fecha,Valor,Error,Entorno,Estado\n" ;

//echo str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys']));
//		echo str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys']));
		$temp->query(str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys'])));

		for ($i=0; $i<$temp->num_rows(); $i++) {
			$temp->next_record();
			$conte .= '"'.$temp->f('id').'","'.$temp->f('comercio').'","'.$temp->f('identificador').'","'.html_entity_decode($temp->f('cliente'),ENT_QUOTES).'","'.$temp->f('codigo').'","'.
					$temp->f('pasarela').'","'.date('d/m/Y H:i:s', $temp->f('fecha')).'","'.
					str_replace(".", ",", substr( $temp->f('valor{val}'), 0, strpos( $temp->f('valor{val}'), ".") + 3 )).'","'. $temp->f('error')
					.'","'. $temp->f('tipoEntorno') .'","'. $temp->f('estad') .'"'."\n";

		}

		if ($ficherw = fopen( $urlPassCSV, 'w' )) {
			fwrite ( $ficherw, $conte );
			fclose ( $ficherw );
			$pase = true;
		} else exit( "error" );

		$fichersw = fopen( $urlPassCSV, 'r+' );
		$contents = fread($fichersw, filesize($urlPassCSV));
		fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
		fclose($fichersw);
	}
}

if ($_REQUEST['querys2']) { //exporta el reporte transacciones tipo CSV2
	if ($_REQUEST['pag'] == 'reporte') {
		$conte = "No.,Id,Comercio,Referencia del comercio,Banca,Cliente,Referencia Banco,Fecha,Valor Inicial,Valor Devuelto,Valor,Moneda,Error,Estado,Tasa de cambio,Conversión Euro,IP,País,Fecha Mod.,Tipo Operc,Tipo Pago" ;
		if (strpos($_REQUEST['querys2'], 'tbl_aisOrden') > 2)
			$conte .= ",Orden Titanes" ;
		$conte .= "\n" ;

// echo str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys2']));exit;
		$temp->query(str_replace("{tot}", "", str_replace('{val}', '', str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys2'])))));
// 		echo $temp->num_rows();
		for ($i=0; $i<$temp->num_rows(); $i++) {
			$temp->next_record();
// 			echo $conte;
			$pai = '';
			if (function_exists(geoip_country_name_by_name)) $pai = geoip_country_name_by_name($temp->f('ip'));
			$conte .= '"'.($i+1). '","' .$temp->f('id'). '","' .$temp->f('comercio'). '","'. $temp->f('identificador') .'","'. $temp->f('pasarelaN') .'","'.
					html_entity_decode($temp->f('cliente'),ENT_QUOTES) .'","'.	$temp->f('codigo') .'","'. date('d/m/Y H:i:s', $temp->f('fecha')) .'","'.
					number_format($temp->f('valIni'),2).'","' . $temp->f('valorDev').'","' .
					number_format($temp->f('valor'), 2).'","' . $temp->f('moneda').'","' . $temp->f('error').'","' . $temp->f('estad').'","' . $temp->f('tasa').'","' ;
			if ($temp->f('tasa') == 0) $conte .= '0","'; else $conte .= number_format(($temp->f('valor')/$temp->f('tasa')),2).'","';
			// $conte .= $temp->f('euroEquiv').'","' . $temp->f('ip').'","' . $pai .'","' . date('d/m/Y H:i:s',$temp->f('fecha_mod')).'","'. $temp->f('tipo').'"';
			$conte .= $temp->f('ip').'","' . $pai .'","' . date('d/m/Y H:i:s',$temp->f('fecha_mod')).'","'. $temp->f('tipo').'","'. $temp->f('tipopago').'"';
			if (strpos($_REQUEST['querys2'], 'tbl_aisOrden') > 2)
	            $conte .= ',"' . $temp->f('titord').'"';
            $conte .= "\n";
		}

		if ($ficherwo = fopen( $urlPassCSV, 'w' )) {
			fwrite ( $ficherwo, $conte );
			fclose ( $ficherwo );
			$pase = true;
		} else exit( "error" );

		$fichersw = fopen( $urlPassCSV, 'r+' );
		$contents = fread($fichersw, filesize($urlPassCSV));
		fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
		fclose($fichersw);


	}

}

if ($_REQUEST['querys3']) { //exporta el reporte clientes

//echo $_REQUEST['querys'];

		$conte = "No.,Id,Comercio,Cliente,Usuario,Referencia del comercio,Referencia Concentrador,Fecha,Valor,Moneda,Estado,Servicio\n" ;

	//echo str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys']));
			$temp->query(str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys3'])));

			for ($i=0; $i<$temp->num_rows(); $i++) {
				$temp->next_record();
				$conte .= '"'.($i+1). '","' .$temp->f('id'). '","' .$temp->f('comercio'). '","' . html_entity_decode($temp->f('cliente'), ENT_QUOTES) . '","'. html_entity_decode($temp->f('usrL'),ENT_QUOTES).'","'.
						$temp->f('codigo') .'","'. $temp->f('id_transaccion') .'","'. date('d/m/Y H:i:s', $temp->f('fecha')) .'","'.
						number_format($temp->f('valor{val}'), 2, ',', '').'","'.
						$temp->f('moneda').'","'.$temp->f('estad').'","'.$temp->f('servicio').'"'
						 ."\n";

			}
//                    $conte .= '#%';
//                if (!file_put_contents($urlPassCSV, $conte))  exit( "error" );


		if ($ficherwo = fopen( $urlPassCSV, 'w' )) {
			fwrite ( $ficherwo, $conte );
			fclose ( $ficherwo );
			$pase = true;
		} else exit( "error" );

		$fichersw = fopen( $urlPassCSV, 'r+' );
		$contents = fread($fichersw, filesize($urlPassCSV));
		fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
		fclose($fichersw);


}

if ($_REQUEST['querys4']) { //exporta los usuarios
//echo $_REQUEST['querys'];

		$conte = "No.,Nombre,Comercio,Grupo,Usuario,Email,Activo,Último acceso\n" ;

	//echo str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys']));
			$temp->query(str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys4'])));

			for ($i=0; $i<$temp->num_rows(); $i++) {
				$temp->next_record();
				$conte .= '"'.($i+1). '","' .$temp->f('nombre'). '","' .$temp->f('comercio'). '","'. $temp->f('rol') .'","'.
						$temp->f('login') .'","'. $temp->f('email') .'","'. $temp->f('activo') .'","'. date('d/m/Y H:i:s', $temp->f('fecha_visita')) .'"'
						 ."\n";

			}
//                    $conte .= '#%';
//                if (!file_put_contents($urlPassCSV, $conte))  exit( "error" );


		if ($ficherwo = fopen( $urlPassCSV, 'w' )) {
			fwrite ( $ficherwo, $conte );
			fclose ( $ficherwo );
			$pase = true;
		} else exit( "error" );

		$fichersw = fopen( $urlPassCSV, 'r+' );
		$contents = fread($fichersw, filesize($urlPassCSV));
		fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
		fclose($fichersw);


}

if ($_REQUEST['querys5']) { //exporta los comercios
//echo $_REQUEST['querys'];

		$conte = "Identificador,Prefijo,Nombre,Fecha de Alta,Url,Entorno,Activo,Último movimiento\n" ;

	//echo str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys']));
			$temp->query(str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys5'])));

			for ($i=0; $i<$temp->num_rows(); $i++) {
				$temp->next_record();
				$conte .= '"'.$temp->f('id'). '","' .$temp->f('prefijo'). '","' .$temp->f('nombre'). '","' .date('d/m/Y H:i:s', $temp->f('fechaAlta')). '","'.
							$temp->f('url') .'","'.	$temp->f('estado') .'","'. $temp->f('activo') .'","'. date('d/m/Y H:i:s', $temp->f('fechaMovUltima')) .'"'
						 ."\n";

			}
//                    $conte .= '#%';
//                if (!file_put_contents($urlPassCSV, $conte))  exit( "error" );


		if ($ficherwo = fopen( $urlPassCSV, 'w' )) {
			fwrite ( $ficherwo, $conte );
			fclose ( $ficherwo );
			$pase = true;
		} else exit( "error" );

		$fichersw = fopen( $urlPassCSV, 'r+' );
		$contents = fread($fichersw, filesize($urlPassCSV));
		fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
		fclose($fichersw);

}

if ($_REQUEST['querys6']) { //exporta el reporte transacciones tipo CSV1 según la modificación de Anne
	if ($_REQUEST['pag'] == 'reporte') {
		$conte = "No.,Id,Comercio,Referencia del comercio,Banca,Cliente,Referencia Banco,Fecha Mod,Valor Inicial,Conversión Euro (VI),Tasa deCambio (VI),Fecha Devol,Valor Devuelto,Conversión Euros (VD),Tasa de cambio(VD),Fecha,Moneda,Valor,Estado,Empresa,Tipo Oper.,Tipo Pago,T. Tarjeta" ;
		if (strpos($_REQUEST['querys6'], 'tbl_aisOrden') > 2)
			$conte .= ",Orden Titanes" ;
		
		//error_log("grupoRol=".$_SESSION['grupo_rol']);
		if ($_SESSION['grupo_rol'] < 2 || $_SESSION['rol'] == 19) { //Si es del grupo de bidaiondo
			$_REQUEST['querys6'] = str_replace("tjta from tbl_tarjetas j, tbl_transacciones t,", "tjta, (mtoMonBnc/100) eurBnc from tbl_tarjetas j, tbl_transacciones t,", $_REQUEST['querys6']);
			
			$conte .= ",Euros en el Banco" ;
		}
		$conte .= "\n" ;
		
		error_log(stripslashes($_REQUEST['querys6']));

		//error_log( str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys6'])));
		$temp->query(str_replace("{tot}", "", str_replace('{val}', '', str_replace ('limit 0, 30', '', stripslashes($_REQUEST['querys6'])))));
		for ($i=0; $i<$temp->num_rows(); $i++) {
			$temp->next_record();
			$conte .= '"'.($i+1). '","' .$temp->f('id'). '","' .$temp->f('comercio'). '","'. $temp->f('identificador') .'","'. $temp->f('pasarelaN') .
						'","'.html_entity_decode($temp->f('cliente'),ENT_QUOTES).'","'.$temp->f('codigo').'","'.date('d/m/Y H:i:s',$temp->f('fecha_mod')).
						'","'. number_format($temp->f('valIni'),2).'","'.number_format(($temp->f('valIni') / $temp->f('tasa')),2).'","'.$temp->f('tasa').
						'","'.date('d/m/Y H:i:s', $temp->f('fecha_mod')).'","'.number_format($temp->f('valorDev'),2).'","';
			if ($temp->f('tasaDev') == 0) $conte .= '0","'; else $conte .= number_format(($temp->f('valorDev')/$temp->f('tasaDev')),2).'","';
            $conte .= number_format($temp->f('tasaDev'),4).'","'. date('d/m/Y H:i:s', $temp->f('fecha')) .'","'.$temp->f('moneda') .'","'.
					number_format($temp->f('valor'),2).'","' . $temp->f('estad').'","' . $temp->f('empresa').'","' . $temp->f('tipo').'","' . $temp->f('tipopago').'"';
	        $conte .= ',"' . $temp->f('tjta').'"';
			if (strpos($_REQUEST['querys6'], 'tbl_aisOrden') > 2)
	            $conte .= ',"' . $temp->f('titord').'"';
			if ($_SESSION['grupo_rol'] < 2 || $_SESSION['rol'] == 19) {
				$conte .= ',"' . number_format($temp->f('eurBnc'),2).'"';
			}
            $conte .= "\n";
		}

		if ($ficherwo = fopen( $urlPassCSV, 'w' )) {
			fwrite ( $ficherwo, $conte );
			fclose ( $ficherwo );
			$pase = true;
		} else exit( "error" );

		$fichersw = fopen( $urlPassCSV, 'r+' );
		$contents = fread($fichersw, filesize($urlPassCSV));
		fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
		fclose($fichersw);

	}
}

if ($_REQUEST['querys7']) { //exporte de tabla de la página datos
	$conse = $conte = ";";
	$fecha1 = $_REQUEST['fecha1'];
	$fecha2 = $_REQUEST['fecha2'];
	$elem = $_REQUEST['elem'];
	$inSql =$_REQUEST['inSql'];
	
	$q = "select c.idcomercio, c.nombre
				from tbl_comercio c, tbl_transacciones t 
				where t.idcomercio = c.idcomercio
					and t.fecha_mod between $fecha1 and $fecha2 
					and t.estado in ('A','V','B','R')
					and t.tipoEntorno = 'P'
				group by t.idcomercio order by sum($elem) desc";
// 	$conte = $q;
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arrCome = $temp->loadAssocList();
		foreach ($arrCome as $item){
			$conte .= utf8_encode($item['nombre']).";;;;";
			$conse .= "Valor;Aceptadas;Total;%;";
		}
		$conte .= "{n}".rtrim($conse,";")."{n}";
		
		//determina las fechas en el período
		$q = "select max(t.fecha_mod) maximo, min(t.fecha_mod) minim, from_unixtime(t.fecha_mod, '$inSql' ) dia
				FROM tbl_transacciones t
				where t.estado in ('A','V','B','R')
				and t.tipoEntorno = 'P'
				and t.fecha_mod between $fecha1 and $fecha2
				GROUP BY from_unixtime(t.fecha_mod, '$inSql')
				ORDER BY t.fecha_mod desc;";
		$temp->query($q);
		if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
		$arrFec = $temp->loadAssocList();
		
		for ($i=0;$i<count($arrFec);$i++){
			$lin = $arrFec[$i]['dia'].";";
			for ($j=0;$j<count($arrCome);$j++) {
				$q = "select sum($elem) valor, count(t.idtransaccion) cant
				FROM tbl_transacciones t
				where t.estado in ('A','V','B','R')
				and t.idcomercio = ".$arrCome[$j]['idcomercio']."
							and t.tipoEntorno = 'P'
							and t.fecha_mod between ".$arrFec[$i]['minim']." and ".$arrFec[$i]['maximo'];
				$temp->query($q);
				if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
				$valor = $temp->f('valor');
				$cant = $temp->f('cant');

				$q = "select count(t.idtransaccion) cant
						FROM tbl_transacciones t
						where  t.estado not in ('P')
							and t.idcomercio = ".$arrCome[$j]['idcomercio']."
							and t.tipoEntorno = 'P'
							and t.fecha_mod between ".$arrFec[$i]['minim']." and ".$arrFec[$i]['maximo'];
				$temp->query($q);
				$tota = $temp->f('cant');
					
				if ($temp->f('cant') > 0) $porc = $cant/$tota*100;
				else $porc = 0;
// 				$salida .= "<li style='width:$ancho3;'>".formatea_numero($vale,true)."</li>";
				$lin .= ($valor).";".($cant).";".($tota).";".($porc).";";
		
			}
			$conte .= rtrim($lin,";"). "{n}";
		}
// echo $conte; 
		pasaAExcell($conte);
		exit;

		// if ($ficherwo = fopen( $urlPassCSV, 'w' )) {
		// 	fwrite ( $ficherwo, $conte );
		// 	fclose ( $ficherwo );
		// 	$pase = true;
		// } else exit( "error" );

		// $fichersw = fopen( $urlPassCSV, 'r+' );
		// $contents = fread($fichersw, filesize($urlPassCSV));
		// fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
		// fclose($fichersw);
}

if ($_REQUEST['querys8']) { //exporte de tabla de la página poner cierres
	$conte = "Cierre,Id,Última Transacción,Comercio,Fecha,No. Facturas,Valor,Moneda,Factura,Valor Facturas,Valor Transferencias,Banco\n" ;
	
	$q = str_replace("separator ',<br>'", "separator ' / '", $_REQUEST['querys8']);
// 	echo $q;
// 	$conte = $q;
	$temp->query($q);
	if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
	$arrCome = $temp->loadAssocList();
// 	print_r($arrCome);
	foreach ($arrCome as $item){
		$conte .= '"'.$item['cierre']. '","'.$item['id']. '","'.$item['trasaccion']. '","'.$item['comercio']. '","'.$item['fecha']. '","'.$item['numFacturas']
					. '","'.$item['valo']. '","'.$item['moneda']. '","'.$item['fact']. '","'.$item['valfact']. '","'.$item['vall'].','.$item['banco'].'"'
					."\n";
	}
		

		if ($ficherwo = fopen( $urlPassCSV, 'w' )) {
			fwrite ( $ficherwo, $conte );
			fclose ( $ficherwo );
			$pase = true;
		} else exit( "error" );

		$fichersw = fopen( $urlPassCSV, 'r+' );
		$contents = fread($fichersw, filesize($urlPassCSV));
		fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
		fclose($fichersw);
}

if($_REQUEST['querys12']) { //datos de la página que ve Marilyn
	$q = str_replace('\t','',str_replace('\r\n','',$_REQUEST['querys12']));
	error_log("Query=".$q);
	$temp->query($q);
	$arrFec = $temp->loadAssocList();
	error_log("cuenta=".count($arrFec));
	$sale = "";
	foreach($arrFec[0] as $key => $value) {
		$sale .= "$key;";
		if ($key == 'ip') $sale .= "país;";
	}
	$sale = substr($sale,0,strlen($sale)-1)."{n}";
	// $sale = ";id;idtitanes;cliente;idcimex;usuario;Correo;numDocumento;fec Docum.;fec;CP;pais;activo?;Docum;CantBenf{n}";
// error_log($sale);
	foreach ($arrFec as $row) {
		$sale .= "";
		$i = 0;
		foreach($row as $key => $data) {
			$data = str_replace('submit()', '', $data);
			$data = str_replace('width: 550px;', 'width: 550px;display:none;', $data);
			$data = str_replace('<script', '<scr|', $data);
			$data = str_replace('<!--', '', $data);
			$data = str_replace('//-->', '', $data);
			$data = str_replace('-->', '', $data);
			$data = str_replace('á', 'a', str_replace('é', 'e', str_replace('í', 'i', str_replace('ó', 'o', str_replace('ú', 'u', str_replace('ñ', 'n', $data))))));
			if ($pos == 1 && $i == 0) {
				$sale .= $data.";";$i++;
			} else {
				// if (stripos($data,'@')) {
				// 	$sale .= "<a href='mailto:$data?subject:Notificación de www.aisremesascuba.com'>".$data."</a>;";
				// } else {
					$sale .= $data.";";
				// }
			}
			if ($key == 'ip') if( function_exists("geoip_country_name_by_name")) $sale .= geoip_country_name_by_name($data).";";else $sale .= $data.";";
		}
		$sale .= "{n}";
	}

	// for ($i=0; $i<count($arrFec);$i++) {
	// 	$sale .= "- (".$arrFec[$i]['usuario'].") ".$arrFec[$i]['cliente'].". % ;".$arrFec[$i]['id'].";".$arrFec[$i]['idtitanes'].";".$arrFec[$i]['cliente'].";".$arrFec[$i]['idcimex'].";".$arrFec[$i]['usuario'].";".$arrFec[$i]['correo'].";".$arrFec[$i]['numDocumento'].";".$arrFec[$i]['fec Docum.'].";".$arrFec[$i]['fec'].";".$arrFec[$i]['CP'].";".$arrFec[$i]['pais'].";".$arrFec[$i]['activo?'].";".$arrFec[$i]['Docum'].";".$arrFec[$i]['CantBenf']."{n}";
	// }
	if ($ficherwo = fopen( $urlPassCSV, 'w' )) {
		fwrite ( $ficherwo, $sale );
		fclose ( $ficherwo );
		$pase = true;
	} else exit( "error" );


	// $conte .= rtrim($lin,";"). "{n}";

	// echo $conte; 
	pasaAExcell($sale);
	exit;
	// $fichersw = fopen( $urlPassCSV, 'r+' );
	// $contents = fread($fichersw, filesize($urlPassCSV));
	// fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
	// fclose($fichersw);
}

if ($_REQUEST['querys9']) { //exporte de tabla de la página datos QUITAR
	$conse = $conte = ",";
	$fecha1 = $_REQUEST['fecha1'];
	$fecha2 = $_REQUEST['fecha2'];
	$elem = $_REQUEST['elem'];
	$inSql =$_REQUEST['inSql'];

	$q = "select id, banco nombre from tbl_bancos where id not in (1,3,5,8,14)";
	// 	$conte = $q;
	$temp->query($q);
	if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
	$arrCome = $temp->loadAssocList();
	foreach ($arrCome as $item){
		$conte .= $item['nombre'].",,,,";
		$conse .= "Valor,Aceptadas,Total,%,";
	}
	$conte .= "\n".rtrim($conse,",")."\n";

	//determina las fechas en el período
	$q = "select max(t.fecha_mod) maximo, min(t.fecha_mod) minim, from_unixtime(t.fecha_mod, '$inSql' ) dia
	FROM tbl_transacciones t
	where t.estado in ('A','V','B','R')
	and t.tipoEntorno = 'P'
	and t.fecha_mod between $fecha1 and $fecha2
	GROUP BY from_unixtime(t.fecha_mod, '$inSql')
	ORDER BY t.fecha_mod desc;";
	$temp->query($q);
	if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
	$arrFec = $temp->loadAssocList();

	for ($i=0;$i<count($arrFec);$i++){
		$lin = $arrFec[$i]['dia'].",";
		for ($j=0;$j<count($arrCome);$j++) {
			$q = "select sum($elem) valor, count(t.idtransaccion) cant
						FROM tbl_transacciones t
						where t.estado in ('A','V','B','R')
							and t.pasarela in (select idpasarela from tbl_colPasarBancos where idbanco = ".$arrCome[$j]['id'].")
							and t.tipoEntorno = 'P'
							and t.fecha_mod between ".$arrFec[$i]['minim']." and ".$arrFec[$i]['maximo'];
			$temp->query($q);
			if($temp->getErrorMsg()) $salida .= $temp->getErrorMsg();
			$valor = $temp->f('valor');
			$cant = $temp->f('cant');

			$q = "select count(t.idtransaccion) cant
						FROM tbl_transacciones t
						where t.estado not in ('P')
							and t.pasarela in (select idpasarela from tbl_colPasarBancos where idbanco = ".$arrCome[$j]['id'].")
							and t.tipoEntorno = 'P'
							and t.fecha_mod between ".$arrFec[$i]['minim']." and ".$arrFec[$i]['maximo'];
			$temp->query($q);
			$tota = $temp->f('cant');
				
			if ($temp->f('cant') > 0) $porc = $cant/$tota*100;
			else $porc = 0;
			// 				$salida .= "<li style='width:$ancho3;'>".formatea_numero($vale,true)."</li>";
			$lin .= number_format($valor,2,".","").",".$cant.",".$tota.",".$porc.",";

		}
		$conte .= rtrim($lin,","). "\n";
	}

	if ($ficherwo = fopen( $urlPassCSV, 'w' )) {
		fwrite ( $ficherwo, $conte );
		fclose ( $ficherwo );
		$pase = true;
	} else exit( "error" );

	$fichersw = fopen( $urlPassCSV, 'r+' );
	$contents = fread($fichersw, filesize($urlPassCSV));
	fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
	fclose($fichersw);
}

if ($_REQUEST['querys10']) { //exporte de tabla de la página datos
	// $conte = str_replace("{n}", "id	idtitanes	cliente	idcimex	usuario	numDocumento	fec Docum.	fec	CP	pais	activo?	Docum	CantBenf", $_REQUEST['inSql']);
	$conte = str_replace("{n}", "\n", $_REQUEST['inSql']);
	if ($ficherwo = fopen( $urlPassCSV, 'w' )) {
		fwrite ( $ficherwo, $conte );
		fclose ( $ficherwo );
		$pase = true;
	} else exit( "error" );

	$fichersw = fopen( $urlPassCSV, 'r+' );
	$contents = fread($fichersw, filesize($urlPassCSV));
	fwrite($fichersw, substr($contents, 0, strpos($contents, '#%')));
	fclose($fichersw);
	
}

if ($pase) {
	header ("Content-Type: application/octet-stream");
	header ("Content-Disposition: attachment; filename=".$file." ");
	header ("Content-Length: ".filesize($urlPassCSV));
	header ("Pragma: no-cache");
	header ("Expires: 0");
	readfile($urlPassCSV);
	
//	echo "<script type='text/javascript'>window.open('../desc/$file', '_blank')</script>";
//	include '../desc/'.$file;
}

function cleanData(&$str){
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);
	if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

?>
