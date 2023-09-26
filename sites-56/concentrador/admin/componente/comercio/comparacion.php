<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$html = new tablaHTML;
$temp = @ new ps_DB;

require_once "classes/class.graphic.php";
$PG = @ new PowerGraphic;
$d = $_REQUEST;

$query = "select idcomercio from tbl_comercio where id in (".$_SESSION['idcomStr'].")";
$temp->query($query);
$comercios = implode(",", $temp->loadResultArray());

$comer = $_SESSION['idcomStr'];
// print_r($_SESSION);
// echo $comer;
(isset($d['comercio'])) ? $comercId = str_replace ("'", "", $d['comercio']) : $comercId = $comercios;

if(is_array($comercId)) $comercId = implode(',', $comercId);

$fecha1 = date('d/m/Y', mktime(0, 0, 0, date("m")-14, 1, date("Y")));
$fecha2 = date('d/m/Y', time());

$esta = '\'A\'';
$modoVal = '\'P\'';
$nombreVal = '';
if ($d['fecha1']) $fecha1 = $d['fecha1'];
if ($d['fecha2']) $fecha2 = $d['fecha2'];
$d['moneda']? $esta = $d['moneda']:$esta = '\'A\'';
$d['monedas']? $monedaid = "'".$d['monedas']."'":$monedaid = '124, 978, 840, 826';
$d['tipo']? $tipo = $d['tipo']:$tipo = 4;
if ($d['modo']) $modoVal = stripslashes($d['modo']);
if ($d['nombre']) $nombreVal = $d['nombre'];

$javascript = "
	<script language=\"JavaScript\" type=\"text/javascript\">
	function verifica() {
		return true;
	}</script>";
$html->java = $javascript;

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_COMPARACION;
$html->tituloTarea = _REPORTE_TASK;
$html->anchoTabla = 650;
$html->tabed = true;
$html->anchoCeldaI = 300;
$html->anchoCeldaD = 340;

if (strpos($comer, ',')) {
	$query = "select idcomercio id, nombre from tbl_comercio where id in ($comer) order by nombre";
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query, str_replace(",", "', '", $comercId), null, null, "multiple size='5'");
}
$query = "select idmoneda id, moneda nombre from tbl_moneda";
$html->inSelect(_COMERCIO_MONEDA, 'monedas', 5, $query, $monedaid, null,'Todos = Euro equivalente, se toman todas las monedas y se llevan al cambio del Euro');
$arrGraf = array(array(1,_REPORTE_BARRASV), array(2,_REPORTE_BARRASH), array(3,_REPORTE_PUNTOS), array(4,_REPORTE_LINEAS));
$html->inSelect(_REPORTE_TIPOG, 'tipo', 3, $arrGraf, 4);

echo $html->salida();

$contenido = "";



if ($_SESSION['idioma'] == 'spanish') $arrMes = array('','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
elseif ($_SESSION['idioma'] == 'english') $arrMes = array('','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

// Anual Gráfico de Valores
$i=13;
$cuenta = 0;
while ($i!=-1) {
	if ($d['fecha']) {
		$fecha1 = mktime(0, 0, 0, date("m",$d['fecha'])-$i, 1, date("Y", $d['fecha']));
		$fecha2 = mktime(0, 0, 0, date("m",$d['fecha'])-$i+1, 1, date("Y", $d['fecha']));
	} else {
		$fecha1 = mktime(0, 0, 0, date("m")-$i, 1, date("Y"));
		$fecha2 = mktime(0, 0, 0, date("m")-$i+1, 1, date("Y"));
	}
	$mes = $arrMes[(date('m', $fecha1)*1)];
	$monArr = explode(", ", "'".$monedaid."'");
//	print_r($monArr);
	$cunt = count($monArr);
	$val = 0;

	for ($j=0; $j<$cunt; $j++) {
		switch (str_replace("'", "", $monArr[$j])) {
			case 840:
				$conv = leeSetup('USD');
				break;
			case 124:
				$conv = leeSetup('CAD');
				break;
			case 826:
				$conv = leeSetup('GBP');
				break;
			case 978:
				$conv = 1;
				break;
		}
		$query = "select sum(case t.estado
			when 'B' then if (t.fecha_mod < fechaPagada, (-1 * ((valor_Inicial-valor)/100/tasa)), (valor/100/tasa))
			when 'V' then if (t.fecha_mod < fechaPagada, (-1 * ((valor_Inicial-valor)/100/tasa)), (valor/100/tasa))
			when 'A' then (valor/100/tasa)
			else '0.00' end) valor
						from tbl_transacciones t, tbl_moneda m
						where t.moneda = m.idmoneda
							and fecha_mod between $fecha1 and $fecha2
							and tipoEntorno in ('P')
							and t.idcomercio in (".stripslashes($comercId).")
							and t.moneda in (".str_replace("'", "", $monArr[$j]).")";
		$temp->query($query);
		$val += $temp->f('valor');
	}

	$valMes = $val;
	if ((date("m")-$i) == (date("m")*1)) {
		$mesConv = date('d', mktime(0, 0, 0, date("m")+1, 1,   date("Y"))-1);
		$valMes = ($valMes / (date("d")*1))*$mesConv;
	}

	$total += $val;
	$prom = $total/($cuenta + 1);
//echo $query."<br>";
	$PG->x[$cuenta] = $mes;
	$PG->y[$cuenta] = $val;
	$PG->z[$cuenta] = $prom;
//    echo $PG->y[$cuenta]."<br>";
//	echo "moneda=".$monedaid;
	$i = $i-1;
	$cuenta++;

}

$contenido .= "<div style='text-align: center;'>"._REPORTE_ESTIMADO." ".number_format($valMes, 2)."<br><br></div>";
$PG->title     = _REPORTE_VALORES.": 13 "._REPORTE_MESES;
$PG->descy	   = _REPORTE_REALMENSUAL;
$PG->descz	   = _REPORTE_PROMMENSUAL;
$PG->axis_x    = _REPORTE_MESESM;
$PG->axis_y    = _REPORTE_VALOR;
$PG->type      = $tipo;
$PG->skin      = 4;
$PG->credits   = 0;

$contenido .= "<div style='text-align: center;'><img style='border-color:black' usemap='#anual' src='classes/class.graphic.php?" . $PG->create_query_string() . "'
							border='1' alt='".$PG->title."' title='".$PG->title."'  /></div>";
$contenido .= '<map name="anual">';
$xcoor = 104;
for ($j=0; $j<$cuenta;$j++) {
	$contenido .= '<area shape="rect" title="'.number_format($PG->y[$j],2).'" alt="'.number_format($PG->y[$j],2).'" href="#" coords="'.$xcoor.',58,'.($xcoor+6).',202" />';
	$xcoor += 40;
}
$contenido .= '</map>';

$PG->reset_values();

/*** Anual Gráfico de número de transacciones*/
$i=13;
$cuenta = 0;
while ($i!=-1) {
	if ($d['fecha']) {
		$fecha1 = mktime(0, 0, 0, date("m",$d['fecha'])-$i, 1, date("Y", $d['fecha']));
		$fecha2 = mktime(0, 0, 0, date("m",$d['fecha'])-$i+1, 1, date("Y", $d['fecha']));
	} else {
		$fecha1 = mktime(0, 0, 0, date("m")-$i, 1, date("Y"));
		$fecha2 = mktime(0, 0, 0, date("m")-$i+1, 1, date("Y"));
	}
	$mes = $arrMes[(date('m', $fecha1)*1)];
	$query = "select count(*) valor
				from tbl_transacciones t, tbl_moneda m ";
	$query .= "where t.moneda = m.idmoneda
				and t.moneda in ($monedaid)
				and fecha_mod between $fecha1 and $fecha2
				and t.estado in ('A', 'V', 'B')
				and tipoEntorno in ('P')
				and t.idcomercio in (".stripslashes($comercId).")";

	$temp->query($query);
	$val = $temp->f('valor');

	$query = "select count(*) valor
				from tbl_transacciones t, tbl_moneda m ";
	$query .= "where t.moneda = m.idmoneda
				and t.moneda in ($monedaid)
				and fecha_mod between $fecha1 and $fecha2
				and t.estado in ('D', 'N', 'P')
				and tipoEntorno in ('P')
				and t.idcomercio in (".stripslashes($comercId).")";

	$temp->query($query);
	$valo = $temp->f('valor');

//echo $query."<br>";
	$PG->x[$cuenta] = $mes;
	$PG->y[$cuenta] = $val;
	$PG->z[$cuenta] = $valo;
//echo $PG->y[$cuenta]."<br>";
	$i = $i-1;
	$cuenta++;

}

$PG->title     = _MENU_ADMIN_TRANSACCIONES.": 13 "._REPORTE_MESES;
$PG->descy	   = _REPORTE_ACEPTADA;
$PG->descz	   = _REPORTE_DENEGADA.", "._REPORTE_PROCESADA.", "._REPORTE_PROCESO;
$PG->axis_x    = _REPORTE_MESESM;
$PG->axis_y    = '# '._MENU_ADMIN_TRANSACCIONES;
$PG->type      = $tipo;
$PG->skin      = 4;
$PG->credits   = 0;

$contenido .= "<br><br><div style='text-align: center;'><img src='classes/class.graphic.php?" . $PG->create_query_string() . "'
						border='1' alt='".$PG->title."' title='".$PG->title."'  /></div>";


$PG->reset_values();

/*** Mensual Valores ***/
if ($_SESSION['idioma'] == 'spanish') $arrSem = array('Do','Lu','Ma','Mi','Ju','Vi','Sa');
elseif ($_SESSION['idioma'] == 'english') $arrSem = array('Su','Mo','Tu','We','Th','Fr','Sa');
$i=29;
$cuenta = 0;
while ($i!=-1) {
	if ($d['fecha']) {
		$fecha1 = mktime(0, 0, 0, date("m",$d['fecha']), date('d',$d['fecha'])-$i, date("Y",$d['fecha']));
		$fecha2 = mktime(0, 0, 0, date("m",$d['fecha']), date('d',$d['fecha'])-$i+1, date("Y",$d['fecha']));
	} else {
		$fecha1 = mktime(0, 0, 0, date("m"), date('d')-$i, date("Y"));
		$fecha2 = mktime(0, 0, 0, date("m"), date('d')-$i+1, date("Y"));
	}
	$sem = date('w', $fecha1);
	$mes = date('d', $fecha1)."".$arrSem[$sem];
	$monArr = explode(", ", $monedaid);
	$cunt = count($monArr);
	$val = 0;

	for ($j=0; $j<$cunt; $j++) {
		switch (str_replace("'", "", $monArr[$j])) {
			case '840':
				$conv = leeSetup('USD');
				break;
			case '124':
				$conv = leeSetup('CAD');
				break;
			case '826':
				$conv = leeSetup('GBP');
				break;
			case '978':
				$conv = 1;
				break;
		}
		
		$query = "select sum(case estado when 'A' then euroEquiv else (euroEquiv-euroEquivDev) end) valor
				from tbl_transacciones t, tbl_moneda m
				where t.moneda = m.idmoneda
					and fecha_mod between $fecha1 and $fecha2
					and tipoEntorno in ('P')
					and t.idcomercio in (".stripslashes($comercId).")
					and t.moneda in (".str_replace("'", "", $monArr[$j]).")";
//echo "$query<br>";
		$temp->query($query);
		$val += $temp->f('valor');
	}


	$total1 += $val;
	$prom = $total1/($cuenta + 1);
	$PG->x[$cuenta] = $mes;
	$PG->y[$cuenta] = $val;
//	$PG->z[$cuenta] = money_format('%.2n', $prom);
//    echo $PG->y[$cuenta]."<br>";
	$i = $i-1;
	$cuenta++;

}

$PG->title     = _REPORTE_VALORES.': 30 '._REPORTE_DIAS;
$PG->axis_x    = _REPORTE_DIASM;
$PG->axis_y    = _REPORTE_VALOR;
$PG->type      = $tipo;
$PG->skin      = 4;
$PG->credits   = 0;

$contenido .= "<br><br><div style='text-align: center;'><img  usemap='#mensual' src='classes/class.graphic.php?" . $PG->create_query_string() . "'
							border='1' style='border-color:black' alt='".$PG->title."'  title='".$PG->title."'  /></div>";
$contenido .= '<map name="mensual">';
$xcoor = 83;
for ($j=0; $j<$cuenta;$j++) {
	$contenido .= '<area style="border:solid green 1px" shape="rect" title="'.number_format($PG->y[$j],2).'" alt="'.number_format($PG->y[$j],2).'" href="#" coords="'.$xcoor.',58,'.($xcoor+6).',202" />';
	$xcoor += 40;
}
$contenido .= '</map>';

$PG->reset_values();


/*** Mensual Transacciones ***/
//$arrSem = array('Do','Lu','Ma','Mi','Ju','Vi','Sa');
$i=29;
$cuenta = 0;
while ($i!=-1) {
	if ($d['fecha']) {
		$fecha1 = mktime(0, 0, 0, date("m",$d['fecha']), date('d',$d['fecha'])-$i, date("Y",$d['fecha']));
		$fecha2 = mktime(0, 0, 0, date("m",$d['fecha']), date('d',$d['fecha'])-$i+1, date("Y",$d['fecha']));
	} else {
		$fecha1 = mktime(0, 0, 0, date("m"), date('d')-$i, date("Y"));
		$fecha2 = mktime(0, 0, 0, date("m"), date('d')-$i+1, date("Y"));
	}
	$sem = date('w', $fecha1);
	$mes = date('d', $fecha1)."".$arrSem[$sem];
	$query = "select count(*) valor
				from tbl_transacciones t, tbl_moneda m ";
	$query .= "where t.moneda = m.idmoneda
				and t.moneda in ($monedaid)
				and fecha_mod between $fecha1 and $fecha2
				and t.estado in ('A')
				and tipoEntorno in ('P')
				and t.idcomercio in (".stripslashes($comercId).")";

	$temp->query($query);
//	echo $query."<br>";
	$val = $temp->f('valor');

	$query = "select count(*) valor
				from tbl_transacciones t, tbl_moneda m ";
	$query .= "where t.moneda = m.idmoneda
				and t.moneda in ($monedaid)
				and fecha_mod between $fecha1 and $fecha2
				and t.estado in ('D', 'N', 'P')
				and tipoEntorno in ('P')
				and t.idcomercio in (".stripslashes($comercId).")";

	$temp->query($query);
	$valor = $temp->f('valor');

	$PG->x[$cuenta] = $mes;
	$PG->y[$cuenta] = $val;
	$PG->z[$cuenta] = $valor;
//    echo $PG->y[$cuenta]."<br>";
	$i = $i-1;
	$cuenta++;
}

$PG->title     = _MENU_ADMIN_TRANSACCIONES.': 30 '._REPORTE_DIAS;
$PG->descy	   = _REPORTE_ACEPTADA;
$PG->descz	   = _REPORTE_DENEGADA.", "._REPORTE_PROCESADA.", "._REPORTE_PROCESO;;
$PG->axis_x    = _REPORTE_DIASM;
$PG->axis_y    = '# '._MENU_ADMIN_TRANSACCIONES;
$PG->type      = $tipo;
$PG->skin      = 4;
$PG->credits   = 0;

$contenido .= "<br><br><div style='text-align: center;'><img src='classes/class.graphic.php?" . $PG->create_query_string() . "'
						border='1' alt='".$PG->title."'  title='".$PG->title."'  /></div>";

echo $contenido;
$PG->reset_values();


