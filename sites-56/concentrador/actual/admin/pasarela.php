<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$corCreo = new correo();
$error = '';
$pase = '';

$d = $_REQUEST;
if (_MOS_CONFIG_DEBUG) {
	foreach ($d as $key => $value) {
		echo "$key = $value<br>";
	}
}

/**Procesamiento del update*/
if ($d['modificar']) {
	if (strlen($d['pasnome']) < 4) $error .= 'El nombre de la pasarela no puede quedar vacío';
	$q = "update tbl_pasarela set nombre = '".$d['pasnome']."', tipo = '".$d['tipope']."', cuenta = '".$d['ctae']."', fechamod = ".time().", activo = '".$d['active']."', idcenauto = '".$d['cntroe']."', estado = '".$d['estae']."', secure = '".$d['segure']."', idbanco = '".$d['bancoe']."', idempresa = '".$d['emprese']."', idagencia = '".$d['agenciae']."', amex= '".$d['amexe']."', observacion = '".$d['observe']."', usdxamex = '".$d['usdAmex']."'";

	if (!_CAMB_LIM) {//verifico si el cambio de limites por moneda está habilitado
		if (strtolower($d['limini']) == 's/l') $limine = 0; else $limine = $d['limini'];
		if (strtolower($d['limaxi']) == 's/l') $limaxe = 100000000; else $limaxe = $d['limaxi'];
		if (strtolower($d['lidiai']) == 's/l') $lidiae = 100000000; else $lidiae = $d['lidiai'];
		if (strtolower($d['limeni']) == 's/l') $limene = 100000000; else $limene = $d['limeni'];
		if (strtolower($d['lianui']) == 's/l') $lianue = 100000000; else $lianue = $d['lianui'];
		if (strtolower($d['liopipi']) == 's/l') $liopipe = 1000; else $liopipe = $d['liopipi'];
		if (strtolower($d['liopdii']) == 's/l') $liopdie = 1000; else $liopdie = $d['liopdii'];
		if ($d['active'] == 0) {
			$limine = 0;
			$limaxe = 1;
		}
		
		$q .= ", LimMinOper = '".$limine."', LimMaxOper = '".$limaxe."', LimDiar = '".$lidiae."', LimMens = '".$limene."', LimAnual = '".$lianue."', LimOperIpDia = '".$liopipe."' , LimOperDia = '".$liopdie."'";

	} else {
			if (strtolower($d['Limm1']) == 's/l') $d['Limm1'] = 0;
			if (strtolower($d['Limm2']) == 's/l') $d['Limm2'] = 100000000;
			if (strtolower($d['Limm3']) == 's/l') $d['Limm3'] = 100000000;
			if (strtolower($d['Limm4']) == 's/l') $d['Limm4'] = 100000000;
			if (strtolower($d['Limm5']) == 's/l') $d['Limm5'] = 100000000;
			if (strtolower($d['Limm6']) == 's/l') $d['Limm6'] = 1000;
			if (strtolower($d['Limm7']) == 's/l') $d['Limm7'] = 1000;

		if (strpos($d['monedaE'],',')) $arrMon = explode("', '", $d['monedaE']);
		else {
			$arrMon = array($d['monedaE']);
			$d['cambia'] = $d['modificar'];
		}
			
		foreach ($arrMon as $moneda) {
			$temp->query("delete from tbl_colPasarLimite where idPasar = '".$d['modificar']."' and idmoneda = '".$moneda."'");
			for ($i = 1; $i<8; $i++) {
				$temp->query("insert into tbl_colPasarLimite (idPasar, idLimite, idmoneda, valor, fecha) values ('".$d['modificar']."', '$i', '".$moneda."', '".$d['Limm'.$i]."', unix_timestamp())");
			}
		}
	}

	$q .= " where idPasarela = ".$d['modificar'];
	$temp->query($q);
		
	$temp->query("delete from tbl_colTarjPasar where idPasar = '".$d['modificar']."'");
	
	foreach ($d['amexe'] as $tarj) {
		$temp->query("insert into tbl_colTarjPasar (idTarj, idPasar) values ('$tarj', '".$d['modificar']."')");
	}
	

	if (!$temp->error) $pase = 'Pasarela correctamente Actualizada.';
	else {
		$pase = '';
		$error = $temp->error;
	}
}
/**Fin del Procesamiento del insert*/

/**Procesamiento del insert*/
if ($d['insertar']) {
	if (strlen($d['pasnomi']) < 4) $error .= 'El nombre de la pasarela no puede quedar vacío';
	if (strtolower($d['limini']) == 's/l') $limini = 0; else $limini = $d['limini'];
	if (strtolower($d['limaxi']) == 's/l') $limaxi = 100000000; else $limaxi = $d['limaxi'];
	if (strtolower($d['lidiai']) == 's/l') $lidiai = 100000000; else $lidiai = $d['lidiai'];
	if (strtolower($d['limeni']) == 's/l') $limeni = 100000000; else $limeni = $d['limeni'];
	if (strtolower($d['lianui']) == 's/l') $lianui = 100000000; else $lianui = $d['lianui'];
	if (strtolower($d['liopipi']) == 's/l') $liopipi = 1000; else $liopipi = $d['liopipi'];
	if (strtolower($d['liopdii']) == 's/l') $liopdii = 1000; else $liopdii = $d['liopdii'];
	if ($d['activi'] == 0) {
		$limini = 0;
		$limaxi = 1;
	}
	
	$q = "insert into tbl_pasarela (nombre, tipo, cuenta, fecha, fechamod, activo, idcenauto, estado, secure, 
			idbanco, idempresa, idagencia, amex, observacion, LimMinOper, LimMaxOper, 
			LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperDia, usdxamex)
	values ('".$d['pasnomi']."', '".$d['tipopi']."', '".$d['ctai']."', ".time().", ".time().", '".$d['activi']."', '".$d['cntroi']."', 
			'".$d['estai']."', '".$d['seguri']."', '".$d['bancoi']."', '".$d['empresi']."', '".$d['agenciai']."', '".$d['amexi']."', 
			'".$d['observi']."', '".$limini."', '".$limaxi."', '".$lidiai."', '".$limeni."', '".$lianui."', '".$liopipi."' , '".$liopdii."', '".$d['usdAmex']."' )";
	$temp->query($q);
	$saleId = $temp->last_insert_id();
	
	foreach ($d['amexi'] as $tarj) {
	    $temp->query("insert into tbl_colTarjPasar (idTarj, idPasar) values ('$tarj', '$saleId')");
	}

	if (!$temp->error) $pase = 'Pasarela correctamente insertada.';
	else {
		$pase = '';
		$error = $temp->error;
	}
}
/**Fin del Procesamiento del insert*/

/** Tratamiento de los avisos */
if (strlen($error) > 3) 
	echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">$error</div>";
else 
	echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">$pase</div>";

/** Fin del Tratamiento de los avisos */

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_PASA;
$html->tituloTarea = '';
// $html->hide = true;
$html->anchoTabla = 500;
$html->anchoCeldaI = 170; $html->anchoCeldaD = 320;

$html->java = "<style>.centro1 span{font-size:12px;font-weight:bold;line-height:23px;}.botup{font-size:11px;margin:100px;color:blue;cursor:pointer}</style>";

$html->inTextoL('<span onClick="mBuscar()" class="botup">Buscar</span><span onClick="mInserta()" class="botup">Insertar</span>');

/**Buscar*/
$html->inCajaini('divBusc', null, null);
$html->inTextoL('Buscar');
$html->inHide('1', 'oper');
$html->inTextb('Nombre', '', 'pasnomb');
$valInicio = array(
		array('', 'Cualquiera'),
		array('P', 'Tarjetas'),
		array('T', 'Transferencia')
);
$html->inSelect('Tipo de pago', 'tipopb', 3, $valInicio);
$valInicio = array(
		array('', 'Cualquiera'),
		array('1', 'Activa'),
		array('0', 'Inactiva')
);
$html->inSelect('Estado de la pasarela', 'estab', 3, $valInicio);
$valInicio = "select id, nombre from tbl_empresas order by nombre";
$html->inSelect('Empresa', 'empresb', 5, $valInicio);
$valInicio = "select id, nombre from tbl_agencias order by nombre";
$html->inSelect('Agencia', 'agenciab', 5, $valInicio);
$html->inCajaout();
/**Fin de Buscar*/

/**Insertar*/
$html->inCajaini('divIns', null, null);
$html->inHide('', 'insertar');
$html->inTextoL('Insertar Pasarela');
$html->inTextb('Nombre', '', 'pasnomi');	//nombre

$valInicio = array('P', 'T');
$etiq = array('Tarjetas', 'Transferencia');
$html->inRadio('Forma de pago', $valInicio, 'tipopi', $etiq, 'P');	//forma de pago

$valInicio = array('1','0');
$etiq = array('Si', 'No');
$html->inRadio('Activa?', $valInicio, 'activi', $etiq, '1');	//Activa?

$valInicio = array('P', 'D');
$etiq = array('Producción', 'Desarrollo');
$html->inRadio('Estado', $valInicio, 'estai', $etiq, 'D');	//Estado

$valInicio = array('1','0');
$etiq = array('Si', 'No');
$html->inRadio('Pasarela Segura', $valInicio, 'seguri', $etiq, '0');	//Segura
$html->inHide('0', 'seguri', 'segurih');


$html->inRadio("Al Comercio se le permite hacer operacion en USD con AMEX", $valInicio, 'usdAmex', $etiq, '0'); //usd con tarjetas Amex

$valInicio = "select id, nombre from tbl_tarjetas order by nombre";
$html->inSelect('Tipo de Tarjeta', 'amexi', 1, $valInicio, array(2,3),null,null,'multiple');	//Amex

$valIni = array(0,'No definido');
$temp->query("select id, nombre from tbl_empresas order by nombre");
$valInicio = $temp->loadRowList();
$html->inSelect('Empresa', 'empresi', 3, $valInicio);	//Empresa

$temp->query("select id, nombre from tbl_agencias order by nombre");
$valInicio = $temp->loadRowList();
$html->inSelect('Agencia', 'agenciai', 3, $valInicio);	//Agencia

$temp->query("select id, nombre from tbl_cenAuto order by nombre");
$valInicio = $temp->loadRowList();
array_unshift($valInicio, $valIni);
$html->inSelect('Centro Autorizador', 'cntroi', 3, $valInicio);	//Centro Autorizador

$temp->query("select id, banco from tbl_bancos order by banco");
$valInicio = $temp->loadRowList();
$html->inSelect('Banco', 'bancoi', 3, $valInicio);	//Banco

if (!_CAMB_LIM) {//verifico si el cambio de limites por moneda está habilitado
	$html->inTextb('Límite mín por operación', '0', 'limini', null, ' S/L - Sin límite');
	$html->inTextb('Límite max por operación', 'S/L', 'limaxi', null, ' S/L - Sin límite');
	$html->inTextb('Límite diario', 'S/L', 'lidiai', null, ' S/L - Sin límite');
	$html->inTextb('Límite mensual', 'S/L', 'limeni', null, ' S/L - Sin límite');
	$html->inTextb('Límite anual', 'S/L', 'lianui', null, ' S/L - Sin límite');
	$html->inTextb('Límite operaciones por IP', 'S/L', 'liopipi', null, ' S/L - Sin límite');
	$html->inTextb('Límite operaciones por día', 'S/L', 'liopdii', null, ' S/L - Sin límite');
}

$html->inTexarea('Datos de la Cuenta Transferencias', '', 'ctai', 5, null, null, null, 40);	//Cuenta
$html->inTexarea('Observaciones', '', 'observi', 5, null, null, null, 40);	//Observacion
$html->inCajaout();

/**Fin de Insertar*/

/** Editar */
if ($d['cambiar'] > 0) {
	$q = "select idPasarela, nombre, tipo, cuenta, datos, activo, comercio, idcenauto, estado, secure, idbanco, idempresa, 
			case LimMinOper when 0 then 'S/L' else LimMinOper end LimMinOper, 
			case LimMaxOper when 100000000 then 'S/L' else LimMaxOper end LimMaxOper, 
			case LimDiar when 100000000 then 'S/L' else LimDiar end LimDiar, 
			case LimMens when 100000000 then 'S/L' else LimMens end LimMens, 
			case LimAnual when 100000000 then 'S/L' else LimAnual end LimAnual, 
			case LimOperIpDia when 1000 then 'S/L' else LimOperIpDia end LimOperIpDia, 
			case LimOperDia when 1000 then 'S/L' else LimOperDia end LimOperDia, idagencia, amex, observacion, usdxamex
from tbl_pasarela where idPasarela = ".$d['cambiar'];
	$temp->query($q);
	$arrDatE	= $temp->loadAssocList();
	$arrDatE	= $arrDatE[0];

	$html->inCajaini('divMod', null, null);
	$html->inHide($d['cambiar'], 'modificar');
	$html->inTextoL('Editar Pasarela');
	$html->inTextb('Nombre', $arrDatE['nombre'], 'pasnome');	//nombre
	
	$valInicio = array('P', 'T');
	$etiq = array('Tarjetas', 'Transferencia');
	$html->inRadio('Forma de pago', $valInicio, 'tipope', $etiq, $arrDatE['tipo']);	//forma de pago
	
	$valInicio = array('1','0');
	$etiq = array('Si', 'No');
	$html->inRadio('Activa?', $valInicio, 'active', $etiq, $arrDatE['activo']);	//Activa?
	
	$valInicio = array('P', 'D');
	$etiq = array('Producción', 'Desarrollo');
	$html->inRadio('Estado', $valInicio, 'estae', $etiq, $arrDatE['estado']);	//Estado
	
	$valInicio = array('1','0');
	$etiq = array('Si', 'No');
	$html->inRadio('Pasarela Segura', $valInicio, 'segure', $etiq, $arrDatE['secure']);	//Segura
	
	$html->inRadio("Pasarela que permite hacer operacion en USD con AMEX", $valInicio, 'usdAmex', $etiq, $arrDatE['usdxamex']); //usd con tarjetas Amex
	
	$valInicio = "select id, nombre from tbl_tarjetas order by nombre";
	$temp->query("select idTarj from tbl_colTarjPasar where idPasar = ".$d['cambiar']);
	$arrS = $temp->loadResultArray();
	$html->inSelect('Tipo de Tarjeta', 'amexe', 1, $valInicio, $arrS, null, null, 'multiple');	//Amex
	
	$valIni = array(0,'No definido');
	$temp->query("select id, nombre from tbl_empresas order by nombre");
	$valInicio = $temp->loadRowList();
	array_unshift($valInicio, $valIni);
	$html->inSelect('Empresa', 'emprese', 3, $valInicio, $arrDatE['idempresa']);	//Empresa

	$temp->query("select id, nombre from tbl_agencias order by nombre");
	$valInicio = $temp->loadRowList();
	array_unshift($valInicio, $valIni);
	$html->inSelect('Agencia', 'agenciae', 3, $valInicio, $arrDatE['idagencia']);	//Agencia

	$temp->query("select id, nombre from tbl_cenAuto order by nombre");
	$valInicio = $temp->loadRowList();
	array_unshift($valInicio, $valIni);
	$html->inSelect('Centro Autorizador', 'cntroe', 3, $valInicio, $arrDatE['idcenauto']);	//Centro Autorizador

	$temp->query("select id, banco from tbl_bancos order by banco");
	$valInicio = $temp->loadRowList();
	array_unshift($valInicio, $valIni);
	$html->inSelect('Banco', 'bancoe', 3, $valInicio, $arrDatE['idbanco']);	//Banco

	if (!_CAMB_LIM) {//verifico si el cambio de limites por moneda está habilitado
		$html->inTextb('Límite mín por operación', $arrDatE['LimMinOper'], 'limini', null, 'S/L - Sin límite');
		$html->inTextb('Límite max por operación', $arrDatE['LimMaxOper'], 'limaxi', null, 'S/L - Sin límite');
		$html->inTextb('Límite diario', $arrDatE['LimDiar'], 'lidiai', null, 'S/L - Sin límite');
		$html->inTextb('Límite mensual', $arrDatE['LimMens'], 'limeni', null, 'S/L - Sin límite');
		$html->inTextb('Límite anual', $arrDatE['LimAnual'], 'lianui', null, 'S/L - Sin límite');
		$html->inTextb('Límite operaciones por IP', $arrDatE['LimOperIpDia'], 'liopipi', null, 'S/L - Sin límite');
		$html->inTextb('Límite operaciones por día', $arrDatE['LimOperDia'], 'liopdii', null, 'S/L - Sin límite');
	} else {

		$temp->query("select c.idmoneda from tbl_colPasarMon c where c.idpasarela = ".$d['cambiar']." limit 0,1");
		$valSel = $temp->f('idmoneda');
		$valInicio = "select c.idmoneda id, m.moneda nombre from tbl_colPasarMon c, tbl_moneda m where c.idmoneda = m.idmoneda and c.idpasarela = ".$d['cambiar'];
		$html->inSelect('Moneda', 'monedaE', 5, $valInicio, $valSel);

		$q = "select c.idLimite, l.nombre, c.valor from tbl_colPasarLimite c, tbl_limites l where l.id = c.idLimite and idmoneda = '$valSel' and c.idPasar = ".$d['cambiar']." order by c.idLimite";
		// echo $q;
		$temp->query($q);
		$arrLims = $temp->loadRowList();
		// var_dump($arrLims);
		for ($i=0; $i<count($arrLims); $i++){
			if (($i >= 1 && $i <= 4) && $arrLims[$i][2] == '100000000') $arrLims[$i][2] = 'S/L';
			if ($i >4 && $arrLims[$i][2] == '1000') $arrLims[$i][2] = 'S/L';
			$html->inTextb($arrLims[$i][1], $arrLims[$i][2], 'Limm'.$arrLims[$i][0]);
		}
	}

	$html->inTexarea('Datos de la Cuenta Transferencias', $arrDatE['cuenta'], 'ctae', 5, null, null, null, 40);	//Cuenta
	$html->inTexarea('Observaciones', $arrDatE['observacion'], 'observe', 5, null, null, null, 40);	//Observacion
	$html->inCajaout();
}
/** Fin Editar */

echo $html->salida();

/**Listado de pasarelas*/
$vista = "select p.idPasarela id, p.nombre, ".
			"case p.tipo when 'T' then 'Transferencias' else 'Tarjetas' end tipo, ".
			"case p.activo when 1 then 'Si' else 'No' end activo, p.fecha, ".
			"case p.LimDiar when '100000000' then 'S/L' else p.LimDiar end LimDiar, ".
			"case p.LimMaxOper when '100000000' then 'S/L' else p.LimMaxOper end LimMaxOper, ".
			"case p.LimMens when '100000000' then 'S/L' else p.LimMens end LimMens, ".
			"p.LimAnual, p.LimMinOper, p.LimOperIpDia, ".
			"LimOperDia, p.idempresa, idagencia, amex, a.nombre agencia, e.nombre empre, c.nombre centauto ".
		"from tbl_pasarela p, tbl_empresas e, tbl_agencias a, tbl_cenAuto c ";
$where = "where p.idempresa =  e.id ".
		"and p.idagencia = a.id and c.id = p.idcenauto ";
if (strlen($d['pasnomb']) > 1) $where .= " and p.nombre like '%".$d['pasnomb']."%'";
else {
	if (strlen($d['tipopb']) > 0) $where .= " and p.tipo = '".$d['tipopb']."'";
	if (strlen($d['estab']) > 0)$where .= " and p.activo = '".$d['estab']."'";
	if (strlen($d['empresb']) > 0)$where .= " and p.idempresa in ('".$d['empresb']."')";
	if (strlen($d['agenciab']) > 0)$where .= " and p.idagencia in ('".$d['agenciab']."')";
	
}
$orden = " p.activo desc, p.tipo, p.nombre";

$colEsp = array(array("e", _GRUPOS_EDIT_DATA, "css_edit", _TAREA_EDITAR));

$busqueda = array();

$columnas = array(
		array('Id', "id", "", "center", "left" ),
		array('Pasarela', "nombre", "", "center", "left" ),
		array('Pago por', "tipo", "", "center", "center" ),
		array('Fecha', "fecha", "", "center", "left" ),
		array('Activo', 'activo', '', 'center', 'center'),
		array('Empresa', "empre", "", "center", "left" ),
		array('Agencia', "agencia", "", "center", "left" )
//		array('Cent Autor.', "centauto", "", "center", "left" ),
);
if (!_CAMB_LIM) {//verifico si el cambio de limites por moneda está habilitado
	array_push($columnas, 
			array('Lim x Oper', "LimMaxOper", "", "center", "left" ),
			array('Lim x Día', "LimDiar", "", "center", "left" ),
			array('Lim x mes', "LimMens", "", "center", "left" )
	);
}

$ancho = 900;
// echo $vista.$where." order by ".$orden;
tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
/**Fin listado de pasarelas*/

?>
<script type="text/javascript">
var formul = Wttodo = Wder = Wizq = '';

$(document).ready(function(){
	Wttodo = $(".tabTodo").width();
	Wder = $(".derecha1").width();
	Wizq = $(".izquierda1").width();
	Witype = $("input[type=text].formul").width();

	$("#monedaE").change(function(){
		$.post('componente/comercio/ejec.php',{
			fun: 'cambLim',
			pas: <?php echo $d['cambiar']; ?>,
			mon: $("#monedaE").val()
		},function(data){
			var datos = eval('(' + data + ')');
			if (datos.error.length > 0) {
				alert(datos.error);
			}
			if (datos.sale.length > 0) {
				$.each(datos.sale, function(index,vale) {
					$("#Limm"+vale.idLimite).val(vale.valor);
//					alert(vale.valor);
				});
			}
		});
	});

	// alert(Wttodo+' / '+ Wder+' / '+Wizq+' / '+Witype);

	if ($("#modificar").length) {
		$("#caj_divBusc").hide();
		$("#caj_divMod").show();
	} else
		$("#caj_divMod").hide();

	$("#caj_divBusc").hide();
	$("#caj_divIns").hide();
	

	$(".tipopi").click(function(){
		sacaDat('i');
	});
	$(".tipope").click(function(){
		sacaDat('e');
	});
});

function vvacio(campo) {
	if (campo.val() == '') {alert ('Este dato no puede quedar vac\u00edo'); campo.select(); return false;}
}

function verifica() {
	if ($("#pasnom").val().length == 0) {
		arrCampa = Array("#pasnom", "#limin", "#limax", "#lidia", "#limen", "#lianu", "#liopip", "#liopdi");
		for (var i=0;i<arrCampa.length;i++){
			if ($(arrCampa[i]+formul).val() == '') {
				alert ('Este dato no puede quedar vac\u00edo'); 
				$(arrCampa[i]+formul).select(); 
				return false;
			}
		}
	}

	arrCampa = Array("#limin", "#limax", "#lidia", "#limen", "#lianu", "#liopip", "#liopdi");
	for (var i=0;i<arrCampa.length;i++){
		if ($(arrCampa[i]+formul).val().toLowerCase() != 's/l') {
			if ($(arrCampa[i]+formul).val() != '0') {
				if (!parseInt($(arrCampa[i]+formul).val())) {
					alert ('Este dato debe ser un n\u00famero entero'); 
					$(arrCampa[i]+formul).select();
					 return false;
				}
			}
		}
	}

}

function sacaDat(tipo){
	if ($("input.tipop"+tipo+":checked").val() == 'T') { //La pasarela es para transferencias
		$("#div_cta"+tipo).show(); //muestra la cuenta
		$("#div_segur"+tipo).hide(); //oculta si la pasarela es segura o no
		$("#segur"+tipo+"h").show(); // muestra el campo hidden de segura
		$("#div_amex"+tipo).hide(); //oculta si la pasarela es segura o no
		$("#amex"+tipo+"h").show(); // muestra el campo hidden de segura
	} else { // Para pagos con tarjetas
		if ($("#cta"+tipo).val() == '')
			$("#div_cta"+tipo).hide(); //oculta el campo de la cuenta
			$("#div_segur"+tipo).show(); //oculta si la pasarela es segura o no
			$("#segur"+tipo+"h").hide(); // muestra el campo hidden de segura
			$("#div_amex"+tipo).show(); //oculta si la pasarela es segura o no
			$("#amex"+tipo+"h").hide(); // muestra el campo hidden de segura
	}
}

function mBuscar(){ //cambia el formulario a buscar
	$("#caj_divMod").hide();
	$("#caj_divIns").hide();
	$("#caj_divBusc").show();
}

function mInserta(){ //cambia el formulario a insertar
	$("#caj_divMod").hide();
	$("#caj_divBusc").hide();
	$("#caj_divIns").show();
	$("#insertar").val(1);
	formul = 'i';
	sacaDat('i');
}
</script>
