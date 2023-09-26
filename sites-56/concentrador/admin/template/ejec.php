<?php

/* 
 * Fichero para el cambio de las monedas y tarjetas en tpvv
 */

define( '_VALID_ENTRADA' , 1);
include_once( '../../configuration.php' );
include_once( '../classes/entrada.php' );
require_once( '../../include/mysqli.php' );

$temp = new ps_DB();
$ent = new entrada;

//if (!$ent->isAlfabeto($d['fun'], 10)) die('Invalid func');
error_log("fun=".$d['fun']);
if ($d['fun'] == 'instpago') {
	
	$error = $arrSal = '';
	if (!$ent->isAlfanumerico($d['pas'])) $error .= "pasarela incorrecta\n";

	$q = "select distinct idPasarela, idcenauto from tbl_pasarela where tipo = 'P' and activo = 1 and idPasarela in (".$d['pas'].")";
	$temp->query($q);
	$arrCenauto = $temp->loadAssocList();

	if (count($arrCenauto) > 0) {

// 		echo "<br>arrSal solo<br>";
// 		var_dump($arrSal);
 		foreach ($arrCenauto as $key => $value) {
 		
			if ($arrCenauto['idcenauto'] == 12) {
				$q = "select r.nombre, r.email, r.iduser, r.tkuser from tbl_reserva r, tbl_pasarela p where r.pasarela = p.idPasarela and p.idcenauto = '".$arrCenauto['idcenauto']."' and email = '".$d['email']."' order by r.fecha desc limit 0,1";
				$temp->query($q);
				$arrUs = $temp->loadAssocList();
	// 			echo "<br>arrUs solo<br>";
	// 			var_dump($arrUs);
				if ($temp->num_rows())
					$arrSal[0] = $arrSal[0] + $arrUs[0];
			}
 		}
// 		echo "<br>arrSal todo<br>";
// 		var_dump($arrSal);
// 		
// 		//carga las monedas
		$q = "select distinct m.idmoneda, m.moneda from tbl_moneda m, tbl_colPasarMon c where c.idmoneda = m.idmoneda and c.idpasarela in (".$d['pas'].")";
		$temp->query($q);
		$arrSal = $temp->loadAssocList();
		
		//carga las tarjetas de la(s) pasarela(s)
		$temp->query("select distinct t.id, convert(cast(convert(t.nombre using utf8) as binary) using latin1) 'nombre' from tbl_tarjetas t, tbl_colTarjPasar c where c.idTarj = t.id and c.idPasar in (".$d['pas'].")");
		$arrTarj = $temp->loadAssocList();
	} else $error .= 'Pasarela incorrecta u obsoleta';
	//if (_MOS_PHP_DEBUG) trigger_error(utf8_encode(json_encode(array('error'=>$error, 'sale'=>$arrSal, "tar"=>$arrTarj))));
	//$error = "select distinct t.id, convert(cast(convert(t.nombre using utf8) as binary) using latin1) from tbl_tarjetas t, tbl_colTarjPasar c where c.idTarj = t.id and c.idPasar in (".$d['pas'].")";

	echo json_encode(array('error'=>$error, 'sale'=>$arrSal, "tar"=>$arrTarj));
}