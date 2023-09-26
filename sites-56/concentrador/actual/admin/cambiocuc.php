<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

/**
 * Inserta las tasas de cambio del CUC en la tabla de los cambios de las monedas
 */

$html = new tablaHTML;
global $temp;
$d = $_POST;


$q= "select idmoneda, moneda from tbl_moneda where activo = 1";
$temp->query($q);
$arrResult = $temp->loadAssocList();
$comer = $_SESSION['idcomStr'];

if ($d['inserta']) { //Procede a insertar los cambios
	error_log("arrayResult=".count($arrResult));
	for($i=0;$i<count($arrResult);$i++){
		error_log("isarray=".is_array($d['comercio']));
		if (is_array($d['comercio'])) {
			foreach ($d['comercio'] as $com) {
				$q = "insert into tbl_cambioCUC (moneda, comercio, admin, cambio, fecha) values ('{$arrResult[$i]['idmoneda']}', '{$com}', '{$_SESSION['id']}', 
						'{$d[$arrResult[$i]['moneda']]}', unix_timestamp())";
				error_log($q);
				$temp->query($q);
			}
		} else {
			$q = "insert into tbl_cambioCUC (moneda, comercio, admin, cambio, fecha) values ('{$arrResult[$i]['idmoneda']}', '{$d['comercio']}', '{$_SESSION['id']}', 
					'{$d[$arrResult[$i]['moneda']]}', unix_timestamp())";
				error_log($q);
			$temp->query($q);
		}
	}
		echo '<div style="text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px">Cambio introducido.</div>';
}

$javascript = "
	<script language=\"JavaScript\" type=\"text/javascript\">
	function verifica() {
		if (";
for($i=0;$i<count($arrResult);$i++){
	$javascript .= "	(checkField (document.admin_form.".$arrResult[$i]['moneda'].", isNumber, '')) &&\n";
}
$javascript = rtrim($javascript, " &&\n");
$javascript .= "			) {
			return true
		}
		return false;
	}
	</script>";
$html->java = $javascript;

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_CAMBIOCUC;
$html->tituloTarea = _MENU_ADMIN_CAMBIOCUC;
$html->anchoTabla = 650;
$html->tabed = true;
$html->anchoCeldaI = 300;
$html->anchoCeldaD = 340;
$html->inHide(1, 'inserta');

if (strpos ($comer, ",")) {
	$query = "select id, nombre from tbl_comercio where id in (".$comer.") and activo = 'S' order by nombre";
//		echo $query;
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 1, $query, explode(",",$comer), null, null, "multiple size='5'");
	$comerE = substr($comer, 0, strpos($comer, ","));
} else {
	$html->inHide ($comer, 'comercio');
	$comerE = $comer;
}

for($i=0;$i<count($arrResult);$i++){
	$q = "select cambio from tbl_cambioCUC where comercio = '{$comerE}' and moneda = {$arrResult[$i]['idmoneda']} order by fecha desc limit 0,1";
// 	echo $q."<br>";
	$temp->query($q);
	$cabb = $temp->f('cambio');
	$html->inTextb('Tasa de cambio CUC -> '.$arrResult[$i]['moneda'], $cabb, $arrResult[$i]['moneda']);
}

echo $html->salida();

$vista = "select c.nombre comercio, m.moneda, u.cambio, from_unixtime(u.fecha,'%d/%m/%Y %H:%i') fec, a.nombre adm from tbl_cambioCUC u, tbl_comercio c, tbl_moneda m, tbl_admin a ";

$orden = "from_unixtime(u.fecha,'%y%m%d%H%i') desc, c.nombre ";
$where = "where a.idadmin = u.admin and u.comercio = c.id and u.moneda = idmoneda and u.comercio in ($comer) ";
//columnas a mostrar
$columnas = array();

if ($_SESSION['rol'] < 2 || strpos($comer, ',')) array_push($columnas, array(_MENU_ADMIN_COMERCIO, "comercio", "150", "center", "left" ));
array_push($columnas, array('Moneda', "moneda", "", "center", "left" ),
				array('Tasa de cambio', "cambio", "75", "center", "left" ),
				array('Usuario', "adm", "105", "center", "left" ),
				array('Fecha', "fec", "105", "center", "left" ));
error_log ($vista.$where.$wherea." order by ".$orden);
tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );

?>
