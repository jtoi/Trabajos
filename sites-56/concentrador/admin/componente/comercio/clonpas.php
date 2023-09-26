<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$d = $_REQUEST;


if ($d['pasarela']) {
	//clona la pasarela
	$q = "select * from tbl_pasarela where idPasarela = ".$d['pasarela'];
	$temp->query($q);
	$arrDatPas = $temp->loadAssocList();
	$nom = $val = '';

	foreach ($arrDatPas[0] as $key => $value) {
		if ($key != 'idPasarela') {
			if ($key == 'nombre') {
				$nom .= "$key";
				$val .= "'".$d['nuev']."'";
			} else {
				$nom .= ", $key";
				$val .= ", '$value'";
			}
		}
	}
	$q = "insert into tbl_pasarela ($nom) values ($val)";
	// echo $q; exit;
	$temp->query($q);
	$nueId = $temp->last_insert_id();

	//clona las monedas de la pasarela padre
	$q = "select * from tbl_colPasarMon where idpasarela = ".$d['pasarela'];
	$temp->query($q);
	$arrPasMon = $temp->loadAssocList();
	$sq = $val = '';
	$q = "insert into tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values ";

	for ($i = 0; $i < count($arrPasMon); $i++) {
		foreach ($arrPasMon[$i] as $key => $value) {
			if ($key != 'id') {
				(strlen($val) == 0) ? $val .= "'$nueId'":$val .= ", '$value'";
			}
		}
		(strlen($sq) == 0)? $sq .= "($val)" : $sq .= ",($val)";
		$val = '';
	}
	// echo $q.$sq;
	$temp->query($q.$sq);

	//clona las tarjetas de la pasarela padre
	$q = "select idTarj from tbl_colTarjPasar where idPasar = ".$d['pasarela'];
	$temp->query($q);
	$arrTar = $temp->loadResultArray();
	
	$q = '';
	for($i=0;$i<count($arrTar);$i++){
		(strlen($q) == 0) ? $q .= "(".$arrTar[$i].", $nueId)" :  $q .= ",(".$arrTar[$i].", $nueId)";
	}
	$q = "insert into tbl_colTarjPasar (idTarj, idPasar) values $q";
	// echo $q;
	$temp->query($q);
}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_CLONPAS;
$html->tituloTarea = _REPORTE_TASK;
$html->hide = false;
$html->anchoTabla = 500;
$html->anchoCeldaI = 203;
$html->anchoCeldaD = 290;

$query = "select idPasarela id, nombre from tbl_pasarela where idPasarela not in (1) and activo = 1 order by nombre";
$html->inSelect("Pasarela padre", 'pasarela', 2, $query, '');
$html->inTextoL("<span style='color:red;'>Si la pasarela padre es 3D la hijo debe tener 3D en el nombre</span>");
$html->inTextb('Nombre de la nueva pasarela', '', 'nuev');

echo $html->salida();

?>

<script language='javascript'>
	$(document).ready(function(){cambiaNom();});
	$("#pasarela").change(function(){cambiaNom();});

function cambiaNom(){
	$("#nuev").val($("#pasarela :selected").text());
}
</script>