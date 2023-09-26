<?php define( '_VALID_ENTRADA', 1 );
require_once( 'configuration.php' );
// require_once( 'include/database.php' );
// $database = new database($host, $user, $pass, $db, $table_prefix);
// require_once( 'include/ps_database.php' );
require_once 'include/mysqli.php';
$temp = new ps_DB;
require_once 'include/correo.php';

$d = $_POST;
$var = $d['var'];
$cod = $d['cod'];
$sql = $d['sql'];
//echo $cod."<br/>";
//echo $sql."<br/>";
?>
<head>
		<title>Ejecuci&oacute;n de query</title>
<style>
body {font-size: 11px;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;margin-left: 0px;margin-top: 0px;}
</style>
</head>
<?php
echo date('d/m/Y H:i');

$firm = sha1($var.$sql.'Lo que los hace hermoso es algo invisible...los ojos no siempre ven. Hay que buscar con el corazón.');
//echo "$firm==$cod";
if ($cod == $firm){
	//echo $sql;
	$arrSql = explode(';', $sql);
// 	print_r($arrSql);
	for ($i=0; $i<count($arrSql); $i++){
		if (stripos($arrSql[$i], 'select ') > -1) {
// 			echo $arrSql[$i];
			$temp->query($arrSql[$i]);
			echo $temp->getErrorMsg();
		
			echo "<table cellpadding=5 cellspacing=0 border=1 style='font-size:10px;'>";
			$rows = $temp->loadAssocList();
//			print_r($rows);
			echo "<tr>";
			foreach($rows[0] as $key => $value) {
				echo "<th>$key</th>";
				if ($key == 'ip') echo "<th>país</th>";
			}
			echo "</tr>";
			foreach ($rows as $row) {
				echo "<tr>";
				foreach($row as $key => $data) {
                    $data = str_replace('submit()', '', $data);
                    $data = str_replace('width: 550px;', 'width: 550px;display:none;', $data);
                    $data = str_replace('<script', '<scr|', $data);
                    $data = str_replace('<!--', '', $data);
                    $data = str_replace('//-->', '', $data);
                    $data = str_replace('-->', '', $data);
					echo "<td>".$data."</td>";echo "";
					if ($key == 'ip') if( function_exists("geoip_country_name_by_name")) echo "<td>".geoip_country_name_by_name($data)."</td>";else echo "<td>".$data."</td>"; echo "";
				}
				echo "</tr>";
 			}
 			echo "</table>";
			
		} else
			$temp->query($arrSql[$i]);
	}
}


?>
