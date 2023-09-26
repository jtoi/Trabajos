<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$corCreo = new correo();

//global $temp;
$d = $_POST;
$fechaNow = time();
$incluye = "";
$sms=0;

//echo "cod=".generaCodEmp();
// print_r($d);

/**
 * Hace las modificaciones necesarias para que aparezca por qué pasarela
 * está transitando el comercio
 * @param type $arrayPs Array de pasarelas nuevas
 * @param type $id Identificador del comercio columna idcomercio tbl_comercio
 * @param type $tipo 0-Para pasarelas a través de la web; 1-para pagos al momento
 */
function ModifPasar($arrayPs, $id, $tipo) {
	global $temp;
    $fechaNow = time();
    ($tipo == 0) ? $tipo = 'idpasarelaW' : $tipo = 'idpasarelaT';
    foreach ($arrayPs as $item) {
//        revisa que la pasarela no estén ya puesta
        $q = "select count($tipo) total from tbl_colComerPasar where fechaFin = 2863700400 and $tipo = $item and idcomercio = '$id'";
        $temp->query($q);
// echo $q."<br>";
        if ($temp->f('total') == 0) {
            $q = "insert into tbl_colComerPasar (idcomercio, $tipo, fechaIni, idadmin) values ('$id', $item, $fechaNow, {$_SESSION['id']})";
            $temp->query($q);
echo $q."<br>";
        }
    }
    $q = "update tbl_colComerPasar set fechaFin = $fechaNow where idcomercio = $id and fechaFin = 2863700400 and $tipo not in (".implode(',', $arrayPs).")";
echo $q."<br>";
    $temp->query($q);
}

function ponetarjeta($comercio, $pago, $arrMin, $arrMax) {
	global $temp;
	$query = "delete from tbl_cobroTarjeta where idcomercio = '$comercio'";
	$temp->query($query);
	
	for ($i = 0; $i < 6; $i++) {
		$query = "insert into tbl_cobroTarjeta (idcomercio, monto, minCobro, maxCobro) values ('".$comercio."', ".$pago[$i].", ".$arrMin[$i].", ".$arrMax[$i].")";
		$temp->query($query);
	}
}

//inserta Art&iacute;culo
if ($d['inserta']) {
	$id = generaCodEmp();
	$query = "insert into tbl_comercio (idcomercio, nombre, fechaAlta, fechaMovUltima, historico, url, prefijo_trans, pasarela, pasarelaAlMom, url_llegada, ".
				"cierrePer, horIniCierre, horFinCierre, minCierre, maxCierre, cuota, mensConcentr, cuotaTarjeta, retropago, transfr, swift, ".
				"cbancario, minbancario, usarTasaCuc, minTransf, tranfTpv) values ".
				"('$id', '".$d['nombre']."', $fechaNow, $fechaNow, 'D=$fechaNow', '".$d['url']."', '".date('y')."', '".implode(',', $d['pasarela'])."', '".implode(',', $d['pasarela2']).
				"', '".$d['direcurl']."', '{$d['periodicidad']}', '{$d['horain']}', '{$d['horafin']}', '{$d['valmin']}', '{$d['valmax']}', '{$d['cuota']}', '{$d['mensual']}', ".
				"'{$d['usoTarjeta']}', '{$d['retro']}', '{$d['transf']}', '{$d['swift']}', '{$d['bancar']}', '{$d['bancarMin']}', {$d['cuccambio']}, {$d['minTransf']}, {$d['tpvTransf']})";
//if (_MOS_CONFIG_DEBUG) echo $query;
//                echo $query;
	$temp->query($query);
	$lastId = $temp->last_insert_id();
	if ($temp->getErrorMsg()) echo $temp->getErrorMsg();
	
	$arrTex = array(
		"invPago",
		"condPago",
		"voucher"
	);
	for ($j = 0; $j < count($arrTex); $j ++) { //documento: condiciones, correo, voucher
		$q = "select id, iso from tbl_idioma order by id";
		$temp->query($q);
		$arrId = $temp->loadRowList();
		for ($d = 0; $d < count($arrId); $d++) {
			$texto = '';
			if (strlen($texto) == 0) {
				$texto = htmlentities(leeSetup($arrTex[$j].ucfirst($arrId[$d][1])),ENT_QUOTES, 'ISO-8859-1');
			} 
			$q = "insert into tbl_traducciones (idIdioma, idcomercio, tipo, texto, fecha) values ('".$arrId[$d][0]."', '".$lastId."','$j', '$texto', unix_timestamp())";
			$temp->query($q);
		}
	}

    //para las pasarelas a través de la web primero y por tpv después                 
//	ModifPasar($d['pasarela'], $id, 0);
//    ModifPasar($d['pasarela2'], $id, 1);
	
	ponetarjeta($id, $d['pago'], $d['minCobro'], $d['maxCobro']);
}

// Modifica Articulo
if ($d['modifica']) {
	$pase = true;
	$query = "select nombre, estado, historico, pasarela, sms, telf from tbl_comercio where idcomercio = '".$d['modifica']."'";
	$temp->query($query);
	$estado = $temp->f('estado');
	$historico = $temp->f('historico');
	$comnombre = $temp->f('nombre');

//	if ($d['sms'] == '') $sms1 =  $temp->f('sms');
//	else $sms1 =  $d['sms'];
//	if ($d['telf'] == '') $telf =  $temp->f('telf');
//	else $telf =  $d['telf'];
//	if ($d['pasarela'] == '') $pasarela =  $temp->f('pasarela');
//	else $pasarela =  $d['pasarela'];

	if ($_SESSION['grupo_rol'] < 2) {
		$query = "update tbl_admin set activo = '".$d['activo']."' where idcomercio = '{$d['modifica']}'";
//if (_MOS_CONFIG_DEBUG) echo "query=$query<br>";
		$temp->query($query);
	}

	$query = "update tbl_comercio set url = '".$d['url']."', url_llegada = '".$d['direcurl']."',
				condiciones_esp = '".trim($d['condiciones'])."', condiciones_eng = '".trim($d['condicionesIng'])."',
                correo_esp = '".trim($d['plantilla'])."', correo_eng = '".trim($d['plantillaIng'])."', datos = '".trim($d['direccion'])."' ";
//if (_MOS_CONFIG_DEBUG) echo "query=$query<br>";

	if ($estado != $d['actividad']) {
		if ($d['actividad'] == 'P') {
			$quer = "select count(*) as total from tbl_transacciones where idcomercio = '{$d['modifica']}' and estado = 'A'
						and fecha < $fechaNow";
			$temp->query($quer);

			if ($temp->f("total") == 0) {
				$pase = false;
				$alerta = _COMERCIO_ACTIVITY_PRO_ALERT;
			}
		}

		if ($pase) {
			$historico = $historico."\n{$d['actividad']}=$fechaNow";
			$query .= ", estado = '".$d['actividad']."', historico='$historico', fechaMovUltima=$fechaNow";
		}
	}
//echo "<br>".$_SESSION['comercio']."<br>";
	if ($_SESSION['grupo_rol'] < 2) {
		$query .= ", prefijo_trans = UCASE('".$d['prefijo']."'), activo = '".$d['activo']."', pasarela = '".implode(',', $d['pasarela'])."', pasarelaAlMom = '".implode(',', $d['pasarela2'])."', 
					sms = {$d['sms']}, telf = '{$d['telf']}', cierrePer = '{$d['periodicidad']}', horIniCierre = '{$d['horain']}', horFinCierre = '{$d['horafin']}', 
					minCierre = '{$d['valmin']}', maxCierre = '{$d['valmax']}', cuota = '{$d['cuota']}', mensConcentr = '{$d['mensual']}', cuotaTarjeta = '{$d['usoTarjeta']}', 
					retropago = '{$d['retro']}', transfr = '{$d['transf']}', swift = '{$d['swift']}', cbancario = '{$d['bancar']}', minbancario = '{$d['bancarMin']}', usarTasaCuc = {$d['cuccambio']}, ".
                    "minTransf = {$d['minTransf']}, tranfTpv = {$d['tpvTransf']}";
	}
	$query .= " where idcomercio = '".$d['modifica']."'";
    
if (_MOS_CONFIG_DEBUG) echo "query=$query<br>";

	$temp->query($query);
	
	ponetarjeta($d['modifica'], $d['pago'], $d['minCobro'], $d['maxCobro']);
    
    ModifPasar($d['pasarela'], $d['modifica'], 0);
    ModifPasar($d['pasarela2'], $d['modifica'], 1);

	if ($estado != $d['actividad']) {
		$subject = 'Cambio de estado del comercio';
		$message = "comercio: $comnombre \r\n
					Cambio su estado para: ".$d['actividad'];
		$corCreo->todo(31, $subject, $message);
//if (_MOS_CONFIG_DEBUG) echo "MENSAJE=".$message."<br>";
	}
}

//Borra Artículo
if ($d['borrar']) {

//	$ql = "update tbl_admin set activo = 'N' where idcomercio = ".$d['borrar'];
//	$temp->query($ql);
	$ql = "update tbl_comercio set activo = 'N' where idcomercio = ".$d['borrar'];
	$temp->query($ql);
}

$contenido = $partes[0]. $partes[2]. $partes[3]. $partes[4];

if (!$d['cambiar'] && $_SESSION['grupo_rol'] < 2) { // Valores para insertar nuevos Art&iacute;culos
	$titulo_tarea = _TAREA_INSERTAR.' '._COMERCIO_TITULO;
	$campo_pase = '<input name="inserta" type="hidden" value="true" />';
	$personas = 1;
	$personasExt = 0;
	$activo = 'S';
	$period = 'M';
	$horasIn = '00:00';
	$horasFin = '24:00';
	$valorMin = 1;
	$valorMax = '1000000000';
	$cuota = 200;
	$mensual = 20;
	$usoTarjeta = 1;
	$retro = 4.5;
	$transf = 0;
	$bancar = 0.25;
	$bancarMin = 12;
	$swift = 0;
    $minTransf = 3000;
    $tpvTransf = 0;
} else { // Valores para modificar el art&iacute;culo seleccionado
	if ($d['cambiar']) $comercio = $d['cambiar'];
	else  $comercio = $_SESSION['comercio'];
	$query = 'select * from tbl_comercio where idcomercio = "'.$comercio.'"';
	$temp->query($query);

	$activo = $temp->f('activo');
	$urlCom = $temp->f('url');
	$urlComD = $temp->f('url_llegada');
	$sms = $temp->f('sms');
	$telf = $temp->f('telf');
	global $param;
	$paso1 = explode("\n", $temp->f('historico'));

	$salidaHist = "";
	foreach($paso1 as $valor){
		$paso2 = explode('=', $valor);
		$salidaHist .= "{$paso2[0]}-".date('d/m/y H:i', $paso2[1])."\n";
	}

	$titulo_tarea = _TAREA_MODIFICAR.' '._COMERCIO_TITULO;
	$nombre_form = $temp->f('nombre');
	$condicEsp = $temp->f('condiciones_esp');
	$condicEng = $temp->f('condiciones_eng');
	$plantillaEsp = $temp->f('correo_esp');
	$direccion = $temp->f('datos');
	$plantillaEng = $temp->f('correo_eng');
	$tpv = $temp->f('pasarela');
	$tpv2 = $temp->f('pasarelaAlMom');
	$cmbCuc = $temp->f('usarTasaCuc');
	$tpvVal = explode(',', $tpv);
	$tpv2Val = explode(',', $tpv2);
	$activoVal = $temp->f('activo');
	$estadoVal = $temp->f('estado');
	$period = $temp->f('cierrePer');
	$horasIn = $temp->f('horIniCierre');
	$horasFin = $temp->f('horFinCierre');
	$valorMin = $temp->f('minCierre');
	if ($temp->f('maxCierre') == 1000000000) $valorMax = '1000000000';
	else $valorMax = $temp->f('maxCierre');
	$cuota = $temp->f('cuota');
	$mensual = $temp->f('mensConcentr');
	$usoTarjeta = $temp->f('cuotaTarjeta');
	$retro = $temp->f('retropago');
	$transf = $temp->f('transfr');
	$bancar = $temp->f('cbancario');
	$bancarMin = $temp->f('minbancario');
	$swift = $temp->f('swift');
	$minTransf = $temp->f('minTransf');
    $tpvTransf = $temp->f('tranfTpv');
}

//javascript
$javascript = "
	<script language=\"JavaScript\" type=\"text/javascript\">
	function verifica() {
		if (
				(checkField (document.admin_form.nombre, isAlphanumeric, ''))
			) {
			return true
		}
		return false;
	}
	$(function() {
		$('#condiciones').supertextarea({
		   maxw: 300
		  , maxh: 100
		  , minw: 130
		  , minh: 20
		  , dsrm: {use: false}
		  , tabr: {use: false}
		  , maxl: 1000
		});
		$('#condicionesIng').supertextarea({
		   maxw: 300
		  , maxh: 100
		  , minw: 130
		  , minh: 20
		  , dsrm: {use: false}
		  , tabr: {use: false}
		  , maxl: 1000
		});
		$('#plantilla').supertextarea({
		   maxw: 300
		  , maxh: 100
		  , minw: 130
		  , minh: 20
		  , dsrm: {use: false}
		  , tabr: {use: false}
		  , maxl: 1000
		});
		$('#plantillaIng').supertextarea({
		   maxw: 300
		  , maxh: 100
		  , minw: 130
		  , minh: 20
		  , dsrm: {use: false}
		  , tabr: {use: false}
		  , maxl: 1000
		});
		$('#direccion').supertextarea({
		   maxw: 300
		  , maxh: 100
		  , minw: 130
		  , minh: 20
		  , dsrm: {use: false}
		  , tabr: {use: false}
		  , maxl: 1000
		});
		$('#historia').supertextarea({
		   maxw: 300
		  , maxh: 100
		  , minw: 130
		  , minh: 20
		  , dsrm: {use: false}
		  , tabr: {use: false}
		  , maxl: 1000
		});
	});
	";
if (!$d['cambiar'] && $_SESSION['grupo_rol'] < 2) $javascript .= "$(document).ready(function(){ $('textarea').attr('value', ''); });";
if ($alerta)
	$javascript .= "alert('$alerta');";
$javascript .= "</script>";

$html->java = $javascript;

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _COMERCIO_TITULO;
$html->tituloTarea = $titulo_tarea;
$html->anchoTabla = 650;
$html->tabed = true;
$html->anchoCeldaI = 300;
$html->anchoCeldaD = 340;


if (!$d['cambiar'] && $_SESSION['grupo_rol'] < 2) {
	$html->inHide($activo, 'activo');
	$html->inHide(true, 'inserta'); 
	$html->inTextb(_MENU_ADMIN_COMERCIO, $nombre_form, 'nombre'); //nombre del comercio
} else {
	$html->inTexto(_MENU_ADMIN_COMERCIO, $nombre_form); //identificador del comercio
	$html->inTexto(_COMERCIO_IDENTIF, $comercio); //identificador del comercio
	$html->inHide($comercio, 'modifica');
	$valorIni = array('S', 'N');
	$etiq = array(_FORM_YES, _FORM_NO);
	if ($_SESSION['grupo_rol'] < 2) $html->inRadio(_COMERCIO_ACTIVO, $valorIni, 'activo', $etiq, $activoVal); //si el comercio está activo o no
	$valorIni = array('D', 'P');
	$etiq = array(_COMERCIO_ACTIVITY_DES, _COMERCIO_ACTIVITY_PRO);
	$html->inRadio(_COMERCIO_ACTIVITY, $valorIni, 'actividad', $etiq, $estadoVal); //si el comercio está en desarrollo o producción
	$html->inTexarea(_COMERCIO_HISTORIA, $salidaHist, 'historia', null, null, null, 'size="4" readonly="true"'); //historia del estado del comercio
}

$html->inTextb(_COMERCIO_URL, $urlCom, 'url'); //url ultima en recibir los datos
$html->inTextb(_COMERCIO_URL_DIRECTA, $urlComD, 'direcurl'); //url directa
$html->inTexarea(_COMERCIO_CONDICIONES."(esp)", $condicEsp, 'condiciones', null); //condiciones en espanol
$html->inTexarea(_COMERCIO_CONDICIONES."(eng)", $condicEng, 'condicionesIng', null); //condiciones en inglés

$html->medio(2);

$html->inTexarea(_COMERCIO_CORREO_P."(esp)", $plantillaEsp, 'plantilla', null); //plantilla espanol
$html->inTexarea(_COMERCIO_CORREO_P."(eng)", $plantillaEng, 'plantillaIng', null); //plantilla inglés
$html->inTexarea(_COMERCIO_DIRECCION, $direccion, 'direccion', null); //datos del comercio para las transferencias
if ($_SESSION['grupo_rol'] < 2) {
	$html->inTextb("Valor m&iacute;nimo permitido para Transferencias", $minTransf, 'minTransf'); //valor mínimo por transferencia
	$valorIni = array(1, 0);
	$etiq = array(_FORM_YES, _FORM_NO);
	$html->inRadio("Se aceptan Transferencias por TPVV", $valorIni, 'tpvTransf', $etiq, $tpvTransf); //valor mínimo por transferencia
	$html->inRadio(_COMERCIO_SMS, $valorIni, 'sms', $etiq, $sms); //si desea recibir sms por transacción aceptada
	$html->inTextb(_COMERCIO_TELEFONO, $telf, 'telf', null,"&nbsp;"._COMERCIO_FORMATO_INT); //teléf en que recibirá sms
	$modoArr = array(
		array("0", "No"),
		array('1', "BNC"),
		array('2', "Fincimex")
	);
	$html->inSelect("Cambio de CUC", 'cuccambio', 3, $modoArr, $cmbCuc, null, '0-No, 1-Tasa de BNC, 2-Tasa de Fincimex'); //Poner tasa de cambio
	$valInicio = "select idPasarela id, nombre from tbl_pasarela where tipo = 'P' and activo = 1";
	$html->inSelect(_COMERCIO_PASARELAP, 'pasarela', 1, $valInicio, $tpvVal, null, null, 'multiple'); //pasarela para la web
	$html->inSelect(_COMERCIO_PASARELAM, 'pasarela2', 1, $valInicio, $tpv2Val, null, null, 'multiple'); //pasarela para pagos al momento
} else {
	$query = "select nombre from tbl_pasarela where idPasarela in ($tpv)";
	$temp->query($query);
	$sali = implode(", ", $temp->loadResultArray());
	$html->inTexto(_COMERCIO_PASARELAP, $sali);
	$query = "select nombre from tbl_pasarela where idPasarela in ($tpv2)";
	$temp->query($query);
	$sali = implode(", ", $temp->loadResultArray());
	$html->inTexto(_COMERCIO_PASARELAM, $sali);
}

if ($_SESSION['grupo_rol'] < 2) {
	$html->medio(3);

	$valorIni = array('D', 'S', 'Q', 'M');
	$etiq = array ('Diario', 'Semanal', 'Quincenal', 'Mensual');
	$html->inRadio('Periodicidad del Cierre', $valorIni, 'periodicidad', $etiq, $period, null, false);
	$horasArr = array(
		array('0', '00:00'),array('1', '01:00'),array('2', '02:00'),array('3', '03:00'),array('4', '04:00'),array('5', '05:00'),array('6', '06:00'),
		array('7', '07:00'),array('8', '08:00'),array('9', '09:00'),array('10', '10:00'),array('11', '11:00'),array('12', '12:00'),array('13', '13:00'),
		array('14', '14:00'),array('15', '15:00'),array('16', '16:00'),array('17', '17:00'),array('18', '18:00'),array('19', '19:00'),array('20', '20:00'),
		array('21', '21:00'),array('22', '22:00'),array('23', '23:00')
	);
	$html->inSelect('Hora de comienzo del Cierre', 'horain', 3, $horasArr, $horasIn);
//	$html->inSelect('Hora de fin del Cierre', 'horafin', 3, $horasArr, $horasFin);
	$html->inTextb('Valor mínimo para ejecutar el Cierre', $valorMin, 'valmin');
	$html->inTextb('Valor máximo que hace ejecutar el Cierre', $valorMax, 'valmax');
	$html->inTextb('Cuota de inscripción', $cuota, 'cuota');
	$html->inTextb('Cuota de mensual uso Concentrador', $mensual, 'mensual');
	$html->inTextb('Cuota por uso de la tarjeta', $usoTarjeta, 'usoTarjeta');
	$html->inTextb('% Retropagos y/o Retrocobros', $retro, 'retro');
	$html->inTextb('% por Transferencias', $transf, 'transf');
	$html->inTextb('Costo por Swift', $swift, 'swift');
	$html->inTextb('Costo Bancario', $bancar, 'bancar');
	$html->inTextb('Mínimo de Costo Bancario', $bancarMin, 'bancarMin');

	$html->medio(4);
	
	$arrVals = array_fill(0, 6, '');
	if ($d['cambiar']) {
		$query = "select monto, minCobro, maxCobro from tbl_cobroTarjeta where idcomercio = '".$d['cambiar']."'";
		$temp->query($query);
		$Vals = $temp->loadAssocList();
		if ($temp->num_rows() == 0) {
			$arrVals = array_fill(0, 6, '');
			$arrVals[0] = array('monto' => 4.5, 'minCobro' => 1, 'maxCobro' => 1000000000);
		} else {
			for ($i=0;$i<6;$i++) {
				$arrVals[$i] = $Vals[$i];
			}
		}
		
	} else {
		$arrVals = array_fill(0, 6, '');
		$arrVals[0] = array('monto' => 4.5, 'minCobro' => 1, 'maxCobro' => 1000000000);
	}
	$i=0;
	
	foreach ($arrVals as $item) {
		$texFin = "&nbsp;Cant. Mín.<input type='text' name='minCobro[]' value='".$item['minCobro']."' size='8'>";
		$texFin .= "&nbsp;Cant. Max.<input type='text' name='maxCobro[]' value='".$item['maxCobro']."' size='8'>";
		$html->inTextb('% por Pagos con Tarjetas', $item['monto'], 'pago[]', "pago".$i++, $texFin, 'size=6');
	}
	
}

if ($_SESSION['grupo_rol'] < 2 || !strpos ($_SESSION['comercio'], ",")) {
	echo $html->salida();
} elseif (strpos ($_SESSION['comercio'], ",") && $d['cambiar']) {
	echo $html->salida();
}

if ($_SESSION['comercio'] == 'todos' || strpos($_SESSION['comercio'], ',')) {
	$vista = 'select idcomercio as id, a.nombre,
				a.fechaAlta, a.prefijo_trans prefijo,
				case a.estado when \'D\' then \''._COMERCIO_ACTIVITY_DES.'\' else \''._COMERCIO_ACTIVITY_PRO.'\' end as estado,
				a.fechaMovUltima,
				case activo when \'S\' then \''._FORM_YES.'\' else \''._FORM_NO.'\' end as activo, a.url
				from tbl_comercio a ';
	if ($_SESSION['comercio'] == 'todos') $where = '';
	else $where = 'where idcomercio in ('.$_SESSION['comercio'].')';
	$orden = 'activo desc, a.nombre asc';

	$colEsp = array(array("e", _GRUPOS_EDIT_DATA, "css_edit", _TAREA_EDITAR));
	if ($_SESSION['comercio'] == 'todos') $colEsp[] = array("b", _GRUPOS_BORRA_DATA, "css_borra", _TAREA_BORRAR);

	$busqueda = array();

	$columnas = array(
					array(_COMERCIO_ID, "id", "", "center", "left" ),
					array(_MENU_ADMIN_COMERCIO, "nombre", "", "center", "left" ),
					array(_COMERCIO_PREFIJO, "prefijo", "", "center", "center" ),
					array(_COMERCIO_ALTA, "fechaAlta", "", "center", "left" ),
					array(_COMERCIO_URL_CORTA, "url", "", "center", "left" ),
					array(_COMERCIO_ACTIVITY, "estado", "", "center", "left" ),
					array(_COMERCIO_ACTIVO, "activo", "", "center", "left" ),
					array(_COMERCIO_MOVIMIENTO, "fechaMovUltima", "", "center", "left" )
				);

	$ancho = 900;

	echo "<div style='float:left; width:100%' ><table class='total1' width=\"$ancho\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
		<tr>
			<td><span style='float:right' class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' src=\"../images/x-office-document.png\" alt='"._REPORTE_CSV."'
				title='"._REPORTE_CSV."'></span></td>
		</tr>
	</table></div>";
	tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
}
?>
<form name="exporta" action="impresion.php" method="POST">
	<input type="hidden" name="querys5" value="<?php echo $vista.$where." order by ".$orden ?>">
</form>
