<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$html = new tablaHTML;

global $temp;
$d = $_POST;
$fechaNow = time();
$incluye = "";

//print_r($_SESSION);
//print_r($_POST);

if ($d['cambiar']) {
	$query = "update tbl_ipbloq set bloqueada = 0, fecha_desbloq = $fechaNow, desbloq_por = ".$_SESSION['id']." where idips = ".$d['cambiar'];
	$temp->query($query);
}

isset($d['ip'])? $ip = $d['ip']:$ip = '';
isset($d['trans'])? $trans = $d['trans']:$trans = '';
//echo $d['comer'];
isset($d['comer'])? $comer = $d['comer']:$comer = explode(',',$_SESSION['idcomStr']);
isset($d['bloqu'])? $bloqu = $d['bloqu']: $bloqu = '0,1';
isset($d['fecha1'])? $fecha1 = $d['fecha1']: $fecha1 = '10/11/2010';
isset($d['fecha2'])? $fecha2 = $d['fecha2']: $fecha2 = date('d/m/Y', mktime(0,0,0,date("m"),date("d"),date("Y")));

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_IPDENEGADA;
$html->tituloTarea = _VENTA_BUSCAR;
$html->anchoTabla = 600;
$html->anchoCeldaI = $html->anchoCeldaD = 245;
$html->java = "";

//$html->inHide($comer, 'comer');
//$html->inHide(true, 'buscar');
$html->inTextb("IP", $ip, 'ip', null, null, null);
$html->inTextb(_REPORTE_IDENTIFTRANS, $trans, 'trans', null, null, null);
$q = "select id, nombre from tbl_comercio where id in (".$_SESSION['idcomStr'].") order by nombre";
if (strpos($_SESSION['idcomStr'], ',')) $html->inSelect(_MENU_ADMIN_COMERCIO, 'comer', 6, $q, $comer, null, null, "multiple='multiple'");
$arrVal = array(array('0,1','Cualquiera'),array('1','Bloqueadas'),array('0','Libres'));
$html->inSelect(_IP_BLOQUEADA,'bloqu',3, $arrVal, $bloqu);
$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', 'fecha1', _REPORTE_FECHA_INI, _VENTA_DESC_TEMP_FECHA1);
$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', 'fecha2', _REPORTE_FECHA_FIN, _VENTA_DESC_TEMP_FECHA2);


//if ($comer == 'todos') {
//	$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
//	$html->inSelect(_COMERCIO_TITULO, 'comercio', 1, $query);
//}

echo $html->salida();

//$vista = "select i.idips id, i.ip, i.ip 'geo{geoip}',
//				 i.fecha, i.identificador, c.nombre comercio, (select nombre from tbl_reserva where codigo = i.identificador ) cliente,
//				case bloqueada when 1 then '"._VENTA_BUSCAR_SI."' else '"._VENTA_BUSCAR_NO."' end bloq,
//				case bloqueada when 1 then 'red' else 'black' end `color{col}`,
//				(select count(*) from tbl_ipbloq where ip = i.ip) 'vbloq',
//				fecha_desbloq, (select login from tbl_admin where idadmin = i.desbloq_por) desbloq_por,
//				(select count(*) from tbl_transacciones where ip = i.ip and estado in ('A','V','B')) 'Trsaceptadas', 
//				(select count(*) from tbl_transacciones where ip = i.ip and estado in ('D','P')) 'Trsrechaz'
//			from tbl_ipbloq i, tbl_comercio c";
$vista = "select i.idips id, i.ip, i.ip 'geo{geoip}', (select distinct nombre from tbl_reserva r where i.identificador = r.codigo) nombre,
				 i.fecha, i.identificador, c.nombre comercio, 
				case bloqueada when 1 then '"._VENTA_BUSCAR_SI."' else '"._VENTA_BUSCAR_NO."' end bloq,
				case bloqueada when 1 then 'red' else 'black' end `color{col}`,
				(select count(*) from tbl_ipbloq where ip = i.ip) 'vbloq',
				fecha_desbloq, (select login from tbl_admin where idadmin = i.desbloq_por) desbloq_por
			from tbl_ipbloq i, tbl_comercio c ";
$orden = "bloqueada desc, fecha desc";
$where = "where i.idCom = c.id 
            and c.id in (".implode(",",$comer).") 
			and i.ip != '127.0.0.1'
			and i.fecha between ".to_unix($fecha1)." and ".(to_unix($fecha2) + (24*60*60))." 
			and i.bloqueada in ($bloqu)";
$d['ip']? $where .= " and ip like '%".$d['ip']."%'":$where .= "";
$d['trans']? $where .= " and identificador like '%".$d['trans']."%'":$where .= "";
//if ($d['buscar']) {
//	$where .= $d['buscar'];
//	
//	}
//echo "$vista $where $orden";

if ($_SESSION['grupo_rol'] <= 10) $colEsp[] = array("e", "Ver Data", "css_edit", "Ver");
$busqueda = $columnas = array();
$columnas[] = array('', "color{col}", "1", "center", "center" );
$columnas[] = array("IP", "ip", "100", "center", "center" );
$columnas[] = array(_REPORTE_PAIS, "geo{geoip}", "40", "center", "center" );
$columnas[] = array(_REPORTE_FECHA, "fecha", "120", "center", "center" );
$columnas[] = array(_REPORTE_IDENTIFTRANS, "identificador", "150", "center", "center" );
$columnas[] = array(_REPORTE_CLIENTE, "nombre", "150", "center", "center" );
if (strpos($_SESSION['idcomStr'], ',')) $columnas[] = array(_MENU_ADMIN_COMERCIO, "comercio", "", "center", "center" );
// $columnas[] = array(_REPORTE_CLIENTE, "cliente", "", "center", "center" );
$columnas[] = array(_IP_BLOQUEADA, "bloq", "50", "center", "center" );
$columnas[] = array(_IP_VBLOQUEADA, "vbloq", "", "center", "center" );
//$columnas[] = array(_IP_TRNSACEPTADAS, "Trsaceptadas", "", "center", "center" );
//$columnas[] = array(_IP_TRNSDENEGADAS, "Trsrechaz", "", "center", "center" );
$columnas[] = array(_IP_FECHA_DESBLOQUEADA, "fecha_desbloq", "120", "center", "center" );
$columnas[] = array(_IP_DESBLOQUEADAPOR, "desbloq_por", "50", "center", "center" );
//echo $vista.$where.$orden;
//correoAMi("Pag IPDen",$vista.$where.$orden);
tabla( 1100, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>
