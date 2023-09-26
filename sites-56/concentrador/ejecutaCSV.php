<?php
define( '_VALID_ENTRADA', 1 );
/*
 * Este fichero abre los CSV del BBVA y carga los identificadores de las
 * transacciones, luego ejecuta la query contra la base de datos en Internet.
 */

//include_once( 'configuration.php' );
//include_once( 'admin/classes/entrada.php' );
//require_once( 'include/database.php' );
//$database = new database($host, $user, $pass, $db, $table_prefix);
//require_once( 'include/ps_database.php' );
//include_once( 'adminis.func.php' );
//require_once( '../include/hoteles.func.php' );
//require_once( '../include/param.xml.php' );
//include_once( "../include/sendmail.php" );
//include_once("classes/class_dms.php");
//include_once("classes/class_tablaHtml.php");
//$temp = new ps_DB;
//$ent = new entrada;

//if ($_FILES) {
	$file = $_FILES['file']['name'];
	$direct = '/home/julio/csv/';
	
	$di = new RecursiveDirectoryIterator($direct);
	foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
		$pase = false;
		if (!is_dir($file))	{
			echo $filename . ' - ' . $file->getSize() . ' bytes';
			
			$handle = fopen($file, "r");
			if ($handle) { 
				$sql = "select t.idtransaccion, t.identificador, t.codigo, c.nombre comercio, p.nombre pasarl, from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') fecha1, ".
						"from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i:%s') fecha2, t.valor_inicial, t.valor, t.moneda, t.estado, t.ip, a.nombre pais ".
						"from tbl_transacciones t, tbl_comercio c, tbl_pasarela p, tbl_paises a ".
						"where t.idpais = a.id and t.pasarela = p.idPasarela and t.idcomercio = c.idcomercio and t.estado != 'A' and t.idtransaccion in (";
				while (($str = fgets($handle)) !== false) {
					if (stristr($str, "'")) {
					$pase = true;
						$arr = explode(',', $str);
						$sql .= "'". substr(ltrim($arr[3],"'"),0,12)."', ";
		//				print_r($arr);
		//				echo('<tr><td align="center">'.$str.'</td></tr>');
					}
				}
				
				if ($pase) {
					$sql = rtrim($sql, ", ").")";
			//		echo $sql;
					$time = time();
					$firm = sha1($time.$sql.'Lo que los hace hermoso es algo invisible...los ojos no siempre ven. Hay que buscar con el corazón.');
					$data = array("var"=>$time, "sql"=>$sql, "cod"=>$firm);

					$ch = curl_init('https://www.concentradoramf.com/ejec.php');
					curl_setopt($ch, CURLOPT_HEADER, false);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					$sale = curl_exec($ch);
//					echo "largo: ".strlen($sale);
					if (strlen($sale) > 250) echo "<br />".$sale."<br /><br />";
					else echo " - Todo OK<br /><br />";
					curl_close($ch);

					if (!feof($handle)) {
						echo "Error: unexpected fgets() fail\n";
					}
					fclose($handle);
				}
			}
		}
	}
	
	
//} 
?>
<!--
<form action="ejecutaCSV.php" method="post" enctype="multipart/form-data">
	<label for="file">fichero:</label>
	<input type="file" name="file" id="file"><br>
	<input type="submit" name="submit" value="Submit">
</form>
-->