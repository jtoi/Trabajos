<?php defined('_VALID_ENTRADA') or die('Restricted access');
$html = new tablaHTML;
global $temp;
$d = $_POST;

// var_dump($d);

if (count($d['comercio']) > 0 && count($d['pasarela']) > 0){
	$comer = $d['comercio'];
	$pasar = $d['pasarela'];
	$pago = $d['pago'];
	$acc = $d['accion'];

	$q = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'tbl_comercio'";
	$temp->query($q);
	$arrCol = $temp->loadResultArray();

	($pago == 1) ? $etq = 'pasarelaAlMom' : $etq = 'pasarela' ;

	for ($j=0; $j<count($comer); $j++) {
		$q = "select * from tbl_comercio where id = ".$comer[$j];
		$temp->query($q);
		$res = $temp->loadRowList();
		$i=0;
		foreach($arrCol as $colum) {
			$arrcolVal[$colum] = $res[0][$i];
			$i++;
		}

		$arrPa = explode(',',$arrcolVal[$etq]);
		if ($acc == 1) {
			foreach($pasar as $pasarad) {
				if (!in_array($pasarad, $arrPa)) {$arrPa[] = $pasarad;}
			}
		} else {
			$arrPa = array_diff($arrPa, $pasar);
		}

		sort($arrPa);
		$arrcolVal[$etq] = trim(implode(",",$arrPa), ',');
		$arrcolVal['fechaMovUltima'] = time();
		$q = "update tbl_comercio set ";
		foreach ($arrcolVal as $key => $value) {
			if ($key != 'id') $q .= "$key = '$value', ";
		}
		$q = trim($q,', ')."  where id = ".$comer[$j];
		if (!$temp->query($q)) $temp->getErrorMsg();
		else echo "<div style='text-align:center;color:green;margin: 5px 0;'>El comercio ".$arrcolVal['nombre']." ha sido modificado correctamente</div>";
	}
}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _COMERCIO_TITULO;
$html->tituloTarea = $titulo_tarea;
$html->anchoTabla = 810;
$html->tabed = true;
$html->anchoCeldaI = 300;
$html->anchoCeldaD = 340;

$q = "select id, nombre from tbl_comercio where activo = 'S' order by nombre";
$html->inSelect('Comercios', 'comercio', 1, $q, 0, null, null, 'size=10 multiple');

$q = "select idPasarela id, nombre from tbl_pasarela where activo = '1' and idPasarela not in (1,112) order by nombre";
$html->inSelect('Pasarelas', 'pasarela', 1, $q, 0, null, null, 'size=10 multiple');
	
$valorIni = array('1', '0');
$etiq = array('Adiciona Pasarela', 'Retira Pasarela');
$html->inRadio("Acci&oacute;n a realizar", $valorIni, 'accion', $etiq, '1');
	
$valorIni = array('1', '0');
$etiq = array('Pagos al Momento', 'Pagos desde la Web');
$html->inRadio("Tipo de pago", $valorIni, 'pago', $etiq, '1');

echo $html->salida();

// $q = "select c.id, c.nombre, c.pasarela pasarweb, c.pasarelaAlMom pasarmom from tbl_comercio c where activo = 'S' and c.id in (".implode(",",$comer).") order by c.nombre desc";
// $q = "select c.id, c.nombre, group_concat(pm.nombre separator ', ') pasarmom 
// 	from tbl_comercio c, tbl_pasarela pm 
// 	where pm.idPasarela in (c.pasarelaAlMom) 
// 		and c.id in (".implode(",",$comer).") 
// 	group by c.id order by c.nombre";
// $temp->query($q);
// echo $q;

// $arrCom = $temp->loadAssocList();
// var_dump($arrCom);

// for ($i=0; $i<count($arrCom); $i++){
// 	$temp->query("select nombre from tbl_pasarela where idPasarela in (".$arrCom[$i]['pasarweb'].")");
// 	$arrPasW = $temp->loadRow();
// 	var_dump($arrPasW);
// 	echo ($arrCom[$i]['nombre']."<br>");

// } 
// echo "sale";

?>