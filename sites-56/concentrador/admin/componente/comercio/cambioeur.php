<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

/**
 * Inserta las tasas de cambio del USD con respecto al EUR
 */

$html = new tablaHTML;
global $temp;
$d = $_POST;

//var_dump($d);

//$q= "select idmoneda, moneda from tbl_moneda where activo = 1";
//$temp->query($q);
//$arrResult = $temp->loadAssocList();
$comer = $_SESSION['idcomStr'];

if ($d['inserta']) { //Procede a insertar los cambios
//	for($i=0;$i<count($arrResult);$i++){
	$ahora = time();
		$monArr = array('usd','cad','gbp');
		if (is_array($d['comercio'])) {
			foreach ($d['comercio'] as $com) {
				for ($i = 0; $i < count($monArr); $i++) {
					$mo = $monArr[$i];
					if ($d[$mo] > 0) {
						$q = "insert into tbl_tasaComercio (monedaBas, monedaCamb, idcomercio, idadmin, tasa, fecha) values ((select idmoneda from tbl_moneda where moneda = '$mo'), '978', '{$com}', '{$_SESSION['id']}', '".$d[$mo]."', $ahora)";
						$temp->query($q);
					}
				}
			}
		} else {
			for ($i = 0; $i < count($monArr); $i++) {
				$mo = $monArr[$i];
				if ($d[$mo] > 0) {
					$q = "insert into tbl_tasaComercio (monedaBas, monedaCamb, idcomercio, idadmin, tasa, fecha) values ((select idmoneda from tbl_moneda where moneda = '$mo'), '978', '{$d['comercio']}', '{$_SESSION['id']}', '".$d[$mo]."', $ahora)";
					$temp->query($q);
				}
			}
		}
//	}
		echo '<div style="text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px">Cambio introducido.</div>';
}

$javascript = "";
$html->java = $javascript;

$html->idio = $_SESSION['idioma'];
$html->tituloPag = 'Tasa de cambio Divisas al EUR';
$html->tituloTarea = 'Cambio';
$html->anchoTabla = 650;
$html->tabed = true;
$html->anchoCeldaI = 300;
$html->anchoCeldaD = 340;
$html->inHide(1, 'inserta');

if (strpos ($comer, ",")) {
	$query = "select id, nombre from tbl_comercio where id in (".$comer.") and activo = 'S' and operEur != 0 and cambOperEuro != 0 order by nombre";
//		echo $query;
	$html->inSelect('Comercio', 'comercio', 1, $query, explode(",",$comer), null, null, "multiple size='5'");
	$comerE = substr($comer, 0, strpos($comer, ","));
} else {
	$html->inHide ($comer, 'comercio');
	$comerE = $comer;
}

//for($i=0;$i<count($arrResult);$i++){
	$q = "select tasa from tbl_tasaComercio where idcomercio = '{$comerE}' and monedaBas = 'USD' and monedaCamb = 978 order by fecha desc limit 0,1";
// 	echo $q."<br>";
	$temp->query($q);
	$cabb = $temp->f('cambio');
//	$html->inTextb('Tasa de cambio USD -> '.$arrResult[$i]['moneda'], $cabb, $arrResult[$i]['moneda']);
//}
	$html->inTextb('Tasa de cambio USD -> EUR', '0', 'usd');
	$html->inTextb('Tasa de cambio CAD -> EUR', '0', 'cad');
	$html->inTextb('Tasa de cambio GBP -> EUR', '0', 'gbp');

echo $html->salida();

$vista = "select u.id, c.nombre comercio, mm.moneda, u.tasa cambio, from_unixtime(u.fecha,'%d/%m/%Y %H:%i') fec, a.nombre adm from tbl_tasaComercio u, tbl_comercio c, tbl_moneda m, tbl_moneda mm, tbl_admin a ";

$orden = "from_unixtime(u.fecha,'%y%m%d%H%i') desc, c.nombre ";
$where = "where a.idadmin = u.idadmin and u.idcomercio = c.id and u.monedaCamb = m.idmoneda and u.monedaBas = mm.idmoneda and u.idcomercio in ($comer) ";
//columnas a mostrar
$columnas = array();

if ($_SESSION['rol'] < 2 || strpos($comer, ',')) array_push($columnas, array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left" ));
array_push($columnas, //array('Moneda', "moneda", "", "center", "left" ),
				array('Moneda', "moneda", "75", "center", "left" ),
				array('Tasa de cambio', "cambio", "75", "center", "left" ),
				array('Usuario', "adm", "105", "center", "left" ),
				array('Fecha', "fec", "105", "center", "left" ));
//echo $vista.$where.$wherea." order by ".$orden;
tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );

?>
<script type="text/javascript">
	function verifica() {
		if ((checkField (document.forms[0].usd, isNumber, '')) 
				&& (checkField (document.forms[0].cad, isNumber, '')) 
				&& (checkField (document.forms[0].gbp, isNumber, '')) 
				) {
		
			if (document.forms[0].usd.value < 1) {
				if (!confirm('Est\u00e1 seguro que la el D\u00f3lar Americano est\u00e1 por encima del Euro?')) return false;
				return true;
			} else return true;
		} else return false;
	}
</script>