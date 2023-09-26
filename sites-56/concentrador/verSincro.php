<?php define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' );
require_once 'include/mysqli.php';
require_once( 'include/correo.php' );
include_once( 'admin/adminis.func.php' );
$temp = new ps_DB;
$corCreo = new correo();
$d = $_REQUEST;

echo json_encode($d);

if ($d['vertexto']) {
	$arrText = explode("\n",$d['vertexto']);
	echo "lineas entradas: ".count($arrText)."<br>";
	$j=1;
	$sale = '';
	for($i=1; $i<count($arrText); $i++){
		if (strpos($arrText[$i], "ERROR: ") > -1) {
			$arrLin = explode(" ",$arrText[$i]);
			$temp->query("select t.idTransaccion, t.estado, concat(c.comercio, '-', c.terminal) fichero from tbl_transacciones t, tbl_colPasarMon c where t.pasarela = c.idpasarela and c.idmoneda = t.moneda and t.idtransaccion = '".$arrLin[3]."'");
			if ($temp->f('estado') != 'V' && $temp->f('estado') != 'B') {
				$sale .= "<br><br>".$arrLin[3]." - ".$temp->f('estado'). " - ".$temp->f('fichero')."<br>".$arrText[$i];
			}
			$j++;
		}
	}

	echo "cantidad de errores: $j<br>$sale";
}


?>

<form action='' method='post'>
	<textarea id="text" rows="20" cols="160" name="vertexto"></textarea><br />
	<input type="submit" value="Enviar" /><!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" value="Borrar" /> -->

</form>