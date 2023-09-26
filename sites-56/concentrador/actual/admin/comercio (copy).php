<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$corCreo = new correo();

if (stripos(_ESTA_URL, 'localhost') > 0) {
// $_POST['modifica'] = '122327460662';
// $_POST['prefijo'] = '14'; 
// $_POST['plantilla'] = '';
// $_POST['plantillaIng'] = '';
// $_POST['nombre'] = 'Prueba';
// $_POST['activo'] = 'S';
// $_POST['actividad'] = 'D'; 
// $_POST['historia'] = 'historia = P-14/09/15 21:29'; 
// $_POST['url'] = '';
// $_POST['direcurl'] = '';
// $_POST['s3d'] = 'N';
// $_POST['empresa'] = '1'; 
// $_POST['condiciones'] = 'Estas son las Condiciones de Pago que el cliente deberá aceptar para el pago online. Deberá sustiruir estas por las reales.'; 
// $_POST['condicionesIng'] = 'Those are the Payment Conditions that the costumer must accept before make it. Change this one for the reals one';
// $_POST['direccion'] = 'Travels & Discovery S.A.</span><br>Calle Miguel Brostella Oficina 46 Centro Comercial Camino de Cruces<br>Panamá, República de Panamá<br>Telefax: 0034 94 4530466';
// $_POST['codTransf'] = 'LT-%#%/%Y%'; 
// $_POST['formTransf'] = '003';
// $_POST['concTransf'] = 'Registro de patentes y marcas para el turismo'; 
// $_POST['minTransf'] = '3000';
// $_POST['tpvTransf'] = '1';
// $_POST['sms'] = '0';
// $_POST['vende'] = 'S'; 
// $_POST['cuccambio'] = '0'; 
// $_POST['pasarela'] = '12, 53, 68, 45, 67, 46, 23, 32, 41, 52, 1, 63, 50, 51, 58, 59, 60, 29, 31, 44, 37, 43, 64';
// $_POST['pasarela2'] = '12, 53, 68, 45, 67, 46, 23, 32, 41, 52, 1, 63, 50, 51, 58, 59, 60, 29, 31, 44, 37, 43, 64';
// $_POST['pasTransf'] = '55';
// $_POST['cierreA'] = '1';
// $_POST['periodicidad'] = 'M'; 
// $_POST['horain'] = '0';
// $_POST['valmin'] = '1';
// $_POST['valmax'] = '50000'; 
// $_POST['cuota'] = '200';
// $_POST['mensual'] = '20';
// $_POST['usoTarjeta'] = '1'; 
// $_POST['retro'] = '4.50';
// $_POST['transf'] = '0.50';
// $_POST['swift'] = '0';
// $_POST['bancar'] = '0.25'; 
// $_POST['bancarMin'] = '12';
// $_POST['pago'] = '4.5';
// $_POST['minCobro'] = '1'; 
// $_POST['maxCobro'] = '1000000000';
// $_POST['enviar'] = 'Enviar';
}

//global $temp;
$d = $_POST;
$fechaNow = time();
$incluye = "";
$sms=0;


//echo "cod=".generaCodEmp();
if (_MOS_CONFIG_DEBUG) var_dump($d);

/**
 * Pone las pasarelas con 3D en el orden en que serán ejecutadas
 * @param  [string] $strEnt [orden de pasarelas]
 */
function orden3DPasar($strEnt, $idcom) {
	global $temp;
	$arrPas3d = explode(',', rtrim($strEnt, ','));
	
	$q = "update tbl_rotComPas set activo = 0 where idcom = $idcom";
	$temp->query($q);

	for ($i=0; $i<count($arrPas3d); $i++) {
		$imas = $i+1;
		$temp->query("insert into tbl_rotComPas (idcom, idpasarela, horas, orden, fecha, activo) values ('$idcom', '{$arrPas3d[$i]}', 0, '$imas', unix_timestamp(), 1)");
	}

}

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
        if ($temp->f('total') == 0) {//si no está puesta la incluye
            $q = "insert into tbl_colComerPasar (idcomercio, $tipo, fechaIni, idadmin) values ('$id', $item, $fechaNow, {$_SESSION['id']})";
            $temp->query($q);
        }
    }
    //actualiza las fechas a fechas pasadas a las pasarelas que salen del comercio
    $q = "update tbl_colComerPasar set fechaFin = $fechaNow where idcomercio = $id and fechaFin = 2863700400 and $tipo not in (".implode(',', $arrayPs).")";
    $temp->query($q);
}

function ponetarjeta($comercio, $pago, $arrMin, $arrMax) {
	global $temp;
	$query = "delete from tbl_cobroTarjeta where idcomercio = '$comercio'";
	$temp->query($query);
	
	for ($i = 0; $i < 6; $i++) {
		$query = "insert into tbl_cobroTarjeta (idcomercio, monto, minCobro, maxCobro) values ('".$comercio."', '".$pago[$i]."', '".$arrMin[$i]."', '".$arrMax[$i]."')";
		$temp->query($query);
	}
}

//inserta Artículo
if ($d['inserta']) {
	$arrpasone = explode(',',$d['tpvidord']);

	$id = generaCodEmp();
	($d['s3d'] == 'S')? $permnsec = 1 : $permnsec = 0;

	$query = "insert into tbl_comercio (idcomercio, nombre, fechaAlta, fechaMovUltima, ".
				"historico, url, prefijo_trans, condiciones_esp, condiciones_eng, pasarela, pasarelaAlMom, url_llegada, ".
				"cierrePer, horIniCierre, horFinCierre, minCierre, maxCierre, cuota, mensConcentr, cuotaTarjeta, retropago, transfr, swift, ".
				"cbancario, minbancario, usarTasaCuc, minTransf, tranfTpv, idpasTransf, vendventodo, fijo, corrido, etiqueta, permnsec, pasaRot) ".
				"values ('$id', '".$d['nombre']."', $fechaNow, $fechaNow, 'D=$fechaNow', '".$d['url']."', UCASE('".$d['prefijo']."' ), '".leeSetup('condPagoEsp')."', '".
				leeSetup('condPagoIng')."', '".implode(',', $d['pasarela'])."', '".implode(',', $d['pasarela2']).
				"', '".$d['direcurl']."', '{$d['periodicidad']}', '{$d['horain']}', '{$d['horafin']}', '{$d['valmin']}', '{$d['valmax']}', '{$d['cuota']}', '{$d['mensual']}', ".
				"'{$d['usoTarjeta']}', '{$d['retro']}', '{$d['transf']}', '{$d['swift']}', '{$d['bancar']}', '{$d['bancarMin']}', ".
				"{$d['cuccambio']}, {$d['minTransf']}, {$d['tpvTransf']}, '', '{$d['vende']}', '{$d['codTransf']}', '{$d[formTransf]}', '{$d['concTransf']}', '$permnsec', '{$arrpasone[0]}')";

// 	$query = "insert into tbl_comercio (idcomercio, nombre, fechaAlta, fechaMovUltima, ".
// 			"historico, url, prefijo_trans, condiciones_esp, condiciones_eng, correo_esp, correo_eng, pasarela, pasarelaAlMom, url_llegada, ".
// 			"cierrePer, horIniCierre, horFinCierre, minCierre, maxCierre, cuota, mensConcentr, cuotaTarjeta, retropago, transfr, swift, ".
// 			"cbancario, minbancario, voucherEs, voucherEn, usarTasaCuc, minTransf, tranfTpv, idpasTransf, vendventodo, fijo, corrido, etiqueta) ".
// 			"values ('$id', '".$d['nombre']."', $fechaNow, $fechaNow, 'D=$fechaNow', '".$d['url']."', UCASE('".$d['prefijo']."' ), '".leeSetup('condPagoEsp')."', '".
// 			leeSetup('condPagoIng')."', '".leeSetup('correoClienteEsp')."', '".leeSetup('correoClienteIng')."', '".implode(',', $d['pasarela'])."', '".implode(',', $d['pasarela2']).
// 			"', '".$d['direcurl']."', '{$d['periodicidad']}', '{$d['horain']}', '{$d['horafin']}', '{$d['valmin']}', '{$d['valmax']}', '{$d['cuota']}', '{$d['mensual']}', ".
// 			"'{$d['usoTarjeta']}', '{$d['retro']}', '{$d['transf']}', '{$d['swift']}', '{$d['bancar']}', '{$d['bancarMin']}', '".leeSetup('voucherEs')."', '".leeSetup('voucherEn')."', ".
// 			"{$d['cuccambio']}, {$d['minTransf']}, {$d['tpvTransf']}, {$d['pasTransf']}, '{$d['vende']}', '{$d['codTransf']}', '{$d[formTransf]}', '{$d['concTransf']}')";
//if (_MOS_CONFIG_DEBUG) echo $query;
//                echo $query;
	$temp->query($query);
	$idE = $temp->last_insert_id();
	for ($i=0;$i<count($d['empresa']);$i++){
		$q = "insert into tbl_colEmpresasComercios values (null, ".$d['empresa'][$i].", $idE)";
		$temp->query($q);
	}
	$q = "update tbl_comercio set cierrePor = id where cierrePor = 0";
	$temp->query($q);
	
	//inserta las pasarelas para las transferencias
	if ($d['pasTransf'][0] != 0) {
		for ($i=0; $i<count($d['pasTransf']); $i++) {
			$q = "insert into tbl_colPasarComTran values (null, $idE, ".$d['pasTransf'][$i].", null)";
			$temp->query($q);
		}
	}

    //para las pasarelas a través de la web primero y por tpv después                 
//	ModifPasar($d['pasarela'], $id, 0);
//    ModifPasar($d['pasarela2'], $id, 1);
	$q = "select idComerc from tbl_colAdminComer where idAdmin = ".$_SESSION['id'];
	$temp->query($q);
	$_SESSION['idcomStr'] = implode(",",$temp->loadResultArray());
	ponetarjeta($id, $d['pago'], $d['minCobro'], $d['maxCobro']);

	//ejecuta la función para poner las pasarelas con 3D en orden
	orden3DPasar($d['tpvidord'], $idE);
}

// Modifica Artículo
if ($d['modifica']) {
	$arrpasone = explode(',',$d['tpvidord']);

	$pase = true;
	$query = "select nombre, estado, historico, pasarela, sms, telf, palabra from tbl_comercio where idcomercio = '".$d['modifica']."'";
	$temp->query($query);
	$estado = $temp->f('estado');
	$historico = $temp->f('historico');
	$comnombre = $temp->f('nombre');
	$palabra = $temp->f('palabra');
	($d['s3d'] == 'S')? $permnsec = 1 : $permnsec = 0;
	
	if ($_SESSION['grupo_rol'] <= 2) {
		$query = "update tbl_admin set activo = '".$d['activo']."' where idcomercio = '{$d['modifica']}'";
//if (_MOS_CONFIG_DEBUG) echo "query=$query<br>";
		$temp->query($query);
	}

	$query = "update tbl_comercio set url = '".$d['url']."', url_llegada = '".$d['direcurl']."',
				condiciones_esp = '".trim($d['condiciones'])."', condiciones_eng = '".trim($d['condicionesIng'])."',
                datos = '".trim($d['direccion'])."', vendventodo =  '{$d['vende']}'";
//if (_MOS_CONFIG_DEBUG) echo "query=$query<br>";

	if ($estado != $d['actividad']) {
// 			$quer = "select count(*) as total from tbl_transacciones where idcomercio = '{$d['modifica']}' and estado = 'A'
// 						and fecha < $fechaNow";
// 			$temp->query($quer);

// 			if ($temp->f("total") == 0) {
// 				$pase = false;
// 				$alerta = _COMERCIO_ACTIVITY_PRO_ALERT;
// 			}
// 		}

// 		if ($pase) {
			$historico = $historico."\n{$d['actividad']}=$fechaNow";
			$query .= ", estado = '".$d['actividad']."', historico='$historico', fechaMovUltima=$fechaNow, palabra = '".suggestPassword(20)."', pasaRot = '{$arrpasone[0]}'";
		
	}
//echo "<br>".$_SESSION['comercio']."<br>";
	$temp->query("select id from tbl_comercio where idcomercio = '".$d['modifica']."'");
	$idcom = $temp->f('id');
	
	if ($_SESSION['grupo_rol'] <= 2) {
		$query .= ", prefijo_trans = UCASE('".$d['prefijo']."'), activo = '".$d['activo']."', pasarela = '".implode(',', $d['pasarela']).
					"', pasarelaAlMom = '".implode(',', $d['pasarela2'])."', sms = '{$d['sms']}', telf = '{$d['telf']}', cierrePer = ".
					"'{$d['periodicidad']}', horIniCierre = '{$d['horain']}', horFinCierre = '{$d['horafin']}', minCierre = '{$d['valmin']}', ".
					"maxCierre = '{$d['valmax']}', cuota = '{$d['cuota']}', mensConcentr = '{$d['mensual']}', cuotaTarjeta = '{$d['usoTarjeta']}', ".
					"retropago = '{$d['retro']}', transfr = '{$d['transf']}', swift = '{$d['swift']}', cbancario = '{$d['bancar']}', ".
					"minbancario = '{$d['bancarMin']}', usarTasaCuc = '{$d['cuccambio']}', "."minTransf = '{$d['minTransf']}', tranfTpv = ".
					"'{$d['tpvTransf']}', idpasTransf = '', cierrePor =  '{$d['cierreA']}', fijo = '{$d['codTransf']}', ".
					"corrido = '{$d[formTransf]}', etiqueta = '{$d['concTransf']}', nombre = '{$d['nombre']}', permnsec = '$permnsec'";
		
		ponetarjeta($d['modifica'], $d['pago'], $d['minCobro'], $d['maxCobro']);
	    
	    ModifPasar($d['pasarela'], $d['modifica'], 0);
	    ModifPasar($d['pasarela2'], $d['modifica'], 1);
	
		//modifica las pasarelas de las transferencias
		$q = "delete from tbl_colPasarComTran where idcomercio = $idcom";
		$temp->query($q);
		if ($d['pasTransf'][0] != 0) {
			for ($i=0; $i<count($d['pasTransf']); $i++) {
				$q = "insert into tbl_colPasarComTran values (null, $idcom, ".$d['pasTransf'][$i].", null)";
				$temp->query($q);
			}
		}
	}
	$query .= " where idcomercio = '".$d['modifica']."'";
	
	//borra todas las empresas asociadas al comercio y las vuelve a recrear

	$q = "delete from tbl_colEmpresasComercios where idcomercio = (select id from tbl_comercio where idcomercio = '".$d['modifica']."')";
	$temp->query($q);
	for ($i=0;$i<count($d['empresa']);$i++){
		$q = "insert into tbl_colEmpresasComercios (id, idempresa, idcomercio) values (null, ".$d['empresa'][$i]. ", $idcom)";
		$temp->query($q);
	}
    
// echo "query=$query<br>";

	$temp->query($query);
	if ($temp->getErrorMsg()) $corCreo->todo(31, 'ver query', $query." - ".$temp->getErrorMsg());
	

	if ($estado != $d['actividad']) {
		$subject = 'Cambio de estado del comercio';
		$message = "comercio: $comnombre \r\n
					Cambio su estado para: ".$d['actividad'];
		$corCreo->todo(31, $subject, $message);
//if (_MOS_CONFIG_DEBUG) echo "MENSAJE=".$message."<br>";
	}

	//ejecuta la función para poner las pasarelas con 3D en orden siempre y cuando el comercios no se Cubana
	//para que no vaya a afectar la rotaci�n del mismo
	if ($d['modifica'] != '129025985109')
		orden3DPasar($d['tpvidord'], $idcom);
}

//Borra Artículo
if ($d['borrar']) {

//	$ql = "update tbl_admin set activo = 'N' where idcomercio = ".$d['borrar'];
//	$temp->query($ql);
	$ql = "update tbl_comercio set activo = 'N' where idcomercio = ".$d['borrar'];
	$temp->query($ql);
}

$contenido = $partes[0]. $partes[2]. $partes[3]. $partes[4];

if (!$d['cambiar'] && $_SESSION['grupo_rol'] <= 2) { // Valores para insertar nuevos Artículos
	$titulo_tarea = _TAREA_INSERTAR.' '._COMERCIO_TITULO;
	$campo_pase = '<input name="inserta" type="hidden" value="true" />';
	$personas = 1;
	$personasExt = 0;
	$activo = 'S';
	$vende = 'S';
	$arrEmp = array(1);
	$query = "select prefijo_trans from tbl_comercio order by prefijo_trans desc limit 0, 1";
	$temp->query($query);
	$prefij = $temp->f('prefijo_trans') + 1;
	$period = 'M';
	$horasIn = '00:00';
	$horasFin = '24:00';
	$valorMin = 1;
	$valorMax = '50000';
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
    $val3d = 'N';
} else { // Valores para modificar el artículo seleccionado
	if ($d['cambiar']) $comercio = $d['cambiar'];
	else  $comercio = $_SESSION['comercio'];

	$q = "select c.idempresa id from tbl_colEmpresasComercios c, tbl_comercio o where o.id = c.idcomercio and o.idcomercio in ($comercio)";
	$temp->query($q);
	$arrEmp = $temp->loadResultArray();

	$query = "select * from tbl_comercio where idcomercio = '$comercio'";
	$temp->query($query);
// 	echo $temp->_sql;
	$idcom = $temp->f('id');
	$activo = $temp->f('activo');
	$urlCom = $temp->f('url');
	$urlComD = $temp->f('url_llegada');
	$sms = $temp->f('sms');
	$vende = $temp->f('vendventodo');
	$telf = $temp->f('telf');
	if ($temp->f('permnsec') == 1) $val3d = 'S';
	else $val3d = 'N';
	global $param;
	$paso1 = explode("\n", $temp->f('historico'));

	$salidaHist = "";
	foreach($paso1 as $valor){
		$paso2 = explode('=', $valor);
		$salidaHist .= "{$paso2[0]}-".date('d/m/y H:i', $paso2[1])."\n";
	}

	$titulo_tarea = _TAREA_MODIFICAR.' '._COMERCIO_TITULO;
	$nombre_form = $temp->f('nombre');
	$prefij = $temp->f('prefijo_trans');
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
	if ($temp->f('maxCierre') == 50000) $valorMax = '50000';
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
    $idpasTransf = $temp->f('idpasTransf');
    $cierA = $temp->f('cierrePor');
    $codTransf = $temp->f('fijo');
    $formTransf = $temp->f('corrido');
    $concTransf = $temp->f('etiqueta');
    $q = "select r.idpasarela, p.nombre from tbl_rotComPas r, tbl_pasarela p where p.idPasarela = r.idpasarela and r.idcom = '{$idcom}' and r.tipo = 0 and r.activo = 1 order by orden";
    $temp->query($q);
    $arrOrdPas = $temp->loadRowList();
	for ($i=0;$i<count($arrOrdPas);$i++){
		$idor .= $arrOrdPas[$i][0].",";
		$nomord .= $arrOrdPas[$i][1]."\n";
	}
	
	$q = "select idpasarela from tbl_colPasarComTran where idcomercio = $idcom";
	$temp->query($q);
	$idpasTransf = $temp->loadResultArray();
	
	if (_MOS_CONFIG_DEBUG) var_dump($arrPasTran);
    if (_MOS_CONFIG_DEBUG) var_dump($arrOrdPas);
}

//javascript
$javascript = "
	<script language=\"JavaScript\" type=\"text/javascript\"  charset=\"utf-8\">
	function verifica() {
		if ((checkField (document.forms[0].nombre, isAlphanumeric, ''))) {
			return true;
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
if (!$d['cambiar'] && $_SESSION['grupo_rol'] <= 2) $javascript .= "$(document).ready(function(){ $('textarea').attr('value', ''); });";
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


if (!$d['cambiar'] && $_SESSION['grupo_rol'] <= 2) {
	$html->inHide($activo, 'activo');
	$html->inHide(true, 'inserta'); 
	$html->inTextb(_MENU_ADMIN_COMERCIO, $nombre_form, 'nombre'); //nombre del comercio
} elseif ($d['cambiar'] && $_SESSION['grupo_rol'] <= 2) {
	$html->inTextb(_MENU_ADMIN_COMERCIO, $nombre_form, 'nombre'); //nombre del comercio
	$html->inTexto(_COMERCIO_IDENTIF, $comercio); //identificador del comercio
	$html->inHide($comercio, 'modifica');
	$valorIni = array('S','N');
	$etiq = array(_FORM_YES, _FORM_NO);
	$html->inRadio(_COMERCIO_ACTIVO, $valorIni, 'activo', $etiq, $activoVal); //si el comercio está activo o no
	$valorIni = array('D', 'P');
	$etiq = array(_COMERCIO_ACTIVITY_DES, _COMERCIO_ACTIVITY_PRO);
	$html->inRadio(_COMERCIO_ACTIVITY, $valorIni, 'actividad', $etiq, $estadoVal); //si el comercio está en desarrollo o producción
	$html->inTexarea(_COMERCIO_HISTORIA, $salidaHist, 'historia', null, null, null, 'size="4" readonly="true"'); //historia del estado del comercio
} else {
	$html->inTexto(_MENU_ADMIN_COMERCIO, $nombre_form); //identificador del comercio
	$html->inTexto(_COMERCIO_IDENTIF, $comercio); //identificador del comercio
	$html->inHide($comercio, 'modifica');
	$valorIni = array('S','N');
	$etiq = array(_FORM_YES, _FORM_NO);
	$html->inRadio(_COMERCIO_ACTIVO, $valorIni, 'activo', $etiq, $activoVal); //si el comercio está activo o no
	$valorIni = array('S', 'N');
	$etiq = array(_FORM_YES, _FORM_NO);
	if ($_SESSION['grupo_rol'] <= 2) {
	}
	$valorIni = array('D', 'P');
	$etiq = array(_COMERCIO_ACTIVITY_DES, _COMERCIO_ACTIVITY_PRO);
	$html->inRadio(_COMERCIO_ACTIVITY, $valorIni, 'actividad', $etiq, $estadoVal); //si el comercio está en desarrollo o producción
	$html->inTexarea(_COMERCIO_HISTORIA, $salidaHist, 'historia', null, null, null, 'size="4" readonly="true"'); //historia del estado del comercio
}

$html->inTextb(_COMERCIO_URL, $urlCom, 'url'); //url ultima en recibir los datos
$html->inTextb(_COMERCIO_URL_DIRECTA, $urlComD, 'direcurl'); //url directa
if ($_SESSION['grupo_rol'] <= 2) { 
	$arrIn = array('S','N');
	$arrEtq = array(_FORM_YES, _FORM_NO);
	$html->inRadio('Permitir al comercio invitaciones sin 3D', $arrIn, 's3d', $arrEtq, $val3d);
	$html->inhide($prefij, 'prefijo'); //prefijo del comercio
	$valInicio = "select id, nombre from tbl_empresas order by nombre";
	$html->inSelect("Empresas firmadas", 'empresa', 1, $valInicio, $arrEmp, null, null, 'multiple'); //pasarela para la web
} else {
	$html->inHide ($prefij, 'prefijo');
}

$html->medio(2);

$html->inTexarea(_COMERCIO_CONDICIONES."(esp)", $condicEsp, 'condiciones', null); //condiciones en espanol
$html->inTexarea(_COMERCIO_CONDICIONES."(eng)", $condicEng, 'condicionesIng', null); //condiciones en inglés
$html->inHide("", 'plantilla'); //plantilla espanol
$html->inHide("", 'plantillaIng'); //plantilla inglés
$html->inTexarea(_COMERCIO_DIRECCION, $direccion, 'direccion', null); //datos del comercio para las transferencias

$html->medio(3);

if ($_SESSION['grupo_rol'] <= 2) {
	$html->inTextoL("Leyenda:<br> %Y% = A&ntilde;o (".Date('Y')."), %y% = A&ntilde;o (".Date('y')."), %m% = Mes (".Date('m')."); %d% = D&iacute;a (".Date('d')."); 
			%#% = Numeración");
	$html->inTextb("C&oacute;digo para la numeraci&oacute;n de las Transferencias", $codTransf, 'codTransf'); //código para la numeración de las transferencias
	$html->inTextb("Formato para la numeraci&oacute;n de las Transferencias (%#%)", $formTransf, 'formTransf'); //formato para la numeración de las transferencias
	$html->inTextb("Concepto para las Transferencias", $concTransf, 'concTransf'); //concepto de las transferencias
	$html->inTextb("Valor m&iacute;nimo permitido para Transferencias", $minTransf, 'minTransf'); //valor mínimo por transferencia
	$valorIni = array(1, 0);
	$etiq = array(_FORM_YES, _FORM_NO);
	$html->inRadio("Se aceptan Transferencias por TPVV", $valorIni, 'tpvTransf', $etiq, $tpvTransf); //valor mínimo por transferencia
	$html->inRadio(_COMERCIO_SMS, $valorIni, 'sms', $etiq, $sms); //si desea recibir sms por transacción aceptada
	$valorIni = array('S','N');
	$etiq = array(_FORM_YES, _FORM_NO);
	$html->inRadio(_COMERCIO_VENDE, $valorIni, 'vende', $etiq, $vende); //permitir que los vendedores vean lo vendido por otros
// 	$html->inTextb(_COMERCIO_TELEFONO, $telf, 'telf', null,"&nbsp;"._COMERCIO_FORMATO_INT); //teléf en que recibirá sms
	$modoArr = array(
		array("0", "No"),
//		array('1', "BNC"),
		array('2', "Fincimex")
	);
	$html->inSelect("Cambio de CUC", 'cuccambio', 3, $modoArr, $cmbCuc, null, '0-No, 2-Tasa de Fincimex'); //Poner tasa de cambio
	$valInicio = "select idPasarela id, nombre from tbl_pasarela where tipo = 'P' and activo = 1 and idPasarela != 1 order by nombre";
	$html->inSelect(_COMERCIO_PASARELAP, 'pasarela', 1, $valInicio, $tpvVal, null, null, 'multiple'); //pasarela para la web
	$html->inSelect(_COMERCIO_PASARELAM, 'pasarela2', 1, $valInicio, $tpv2Val, null, null, 'multiple'); //pasarela para pagos al momento
	
// 	$idor = rtrim($idor,",");
// 	$nomord= rtrim($nomord,"\n");
	$html->inTextb('Orden por ID',$idor,'tpvidord');
//  $html->inHide($idor,'tpvidord');
	$html->inTexarea("Orden pasarelas con 3D", $nomord, "pasaorden", 10, null, null, 'readonly="true"');
	$q = "select idPasarela id, nombre from tbl_pasarela where tipo = 'T' and activo = 1 order by nombre";
	$arrTra = array();
	$arrTra[0] = array(0,'Ninguna');
	$temp->query($q);
	$arrNuev = $temp->loadRowList();
	foreach ($arrNuev as $item) {
		$arrTra[] = array($item[0],$item[1]);
	}
	$html->inSelect('Pasarela para las Transferencias', 'pasTransf', 3, $arrTra, $idpasTransf, null, null, 'multiple'); //pasarela para transferencias
	$html->medio(4);
} else {
	$valorIni = array('S','N');
	$etiq = array(_FORM_YES, _FORM_NO);
	$html->inRadio(_COMERCIO_VENDE, $valorIni, 'vende', $etiq, $vende); //permitir que los vendedores vean lo vendido por otros
// 	echo "tpv2=$tpv2<br>";
	$html->inHide(explode(',', $tpv), "pasarela");
	$html->inHide(explode(',', $tpv2), "pasarela2");
	$html->inHide($idpasTransf, "pasTransf");
// 	$query = "select nombre from tbl_pasarela where idPasarela in ($tpv)";
// 	$temp->query($query);
// 	$sali = implode(", ", $temp->loadResultArray());
// 	$html->inTexto(_COMERCIO_PASARELAP, $sali);
// 	$query = "select nombre from tbl_pasarela where idPasarela in ($tpv2)";
// 	$temp->query($query);
// 	$sali = implode(", ", $temp->loadResultArray());
// 	$html->inTexto(_COMERCIO_PASARELAM, $sali);
}

if ($_SESSION['grupo_rol'] <= 2) {

	$valInicio = "select id, nombre from tbl_comercio where activo = 'S' and llevacierre = 1 order by nombre asc";
	// 		echo "cierreA:".$cierA;
	$html->inSelect('El cierre va a', 'cierreA', 2, $valInicio, $cierA);
	
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

	
	$arrVals = array_fill(0, 6, '');
	if ($d['cambiar']) {
		$q = "select monto, minCobro, maxCobro from tbl_cobroTarjeta where idcomercio = '".$d['cambiar']."'";
		$temp->query($q);
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
	
	if (is_array($arrVals)) {
		foreach ($arrVals as $item) {
			if ($item['minCobro']) {
				$texFin = "&nbsp;Cant. Mín.<input type='text' name='minCobro[]' value='".$item['minCobro']."' size='8'>";
				$texFin .= "&nbsp;Cant. Max.<input type='text' name='maxCobro[]' value='".$item['maxCobro']."' size='8'>";
				$html->inTextb('% por Pagos con Tarjetas', $item['monto'], 'pago[]', "pago".$i++, $texFin, 'size=6');
			}
		}
	}
	
}

if ($_SESSION['grupo_rol'] <= 2 || !strpos ($_SESSION['comercio'], ",")) {
	echo $html->salida();
} elseif (strpos ($_SESSION['comercio'], ",") && $d['cambiar']) {
	echo $html->salida();
}

if ($_SESSION['grupo_rol'] <= 2 ) {
	$html = new tablaHTML;

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = "";
	$html->tituloTarea = "Buscar";
	$html->hide = true;
	$html->anchoTabla = 500;
	$html->anchoCeldaI = 170; $html->anchoCeldaD = 320;

	$query = "select idcomercio id, nombre from tbl_comercio where  activo = 'S' order by nombre";
	//		echo $query;
	$html->inSelect(_COMERCIO_TITULO, 'cambiar', 2, $query,  str_replace(",", "', '", $comercId));
	echo $html->salida(null,null,true);
}

if ( strpos($_SESSION['idcomStr'], ',') ) {
	$vista = 'select idcomercio as id, a.nombre,
				a.fechaAlta, a.prefijo_trans prefijo,
				case a.estado when \'D\' then \''._COMERCIO_ACTIVITY_DES.'\' else \''._COMERCIO_ACTIVITY_PRO.'\' end as estado,
				a.fechaMovUltima,
				case activo when \'S\' then \''._FORM_YES.'\' else \''._FORM_NO.'\' end as activo, a.url
				from tbl_comercio a ';
// 	if ($_SESSION['comercio'] == 'todos') $where = '';
// 	else 
		$where = 'where id in ('.$_SESSION['idcomStr'].')';
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
			<td><span style='float:right' class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"'
					src=\"../images/x-office-document.png\" alt='"._REPORTE_CSV."'
				title='"._REPORTE_CSV."'></span></td>
		</tr>
	</table></div>";
// 	echo $vista.$where." order by ".$orden;
	tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
}
?>
<form name="exporta" action="impresion.php" method="POST">
	<input type="hidden" name="querys5" value="<?php echo $vista.$where." order by ".$orden ?>">
</form>

<script type="text/javascript"  charset="utf-8">

//función para poner el orden de las pasarelas con 3D del comercio
$("#pasarela2 option").click(function(){

	var tpv = $(this);
	var strTpv = tpv.text();
	var valTpv = tpv.val();

	//reviso todas las opciones del select para identificar las que no estén seleccionadas y borrarlas
	$("#pasarela2 option").each(function(){
		if ($(this).is(":selected")) {}
		else {
			var opcionstr = $(this).text();
			var opcionval = $(this).val();

			$("#tpvidord").val($("#tpvidord").val().replace(opcionval+',', ''));
			$("#pasaorden").val($("#pasaorden").val().replace(opcionstr+'\n',''));
		}

	});

	if(tpv.is(':selected')) {//si la opción marcada está seleccionada 

		if (strTpv.indexOf(' 3D') > 0) {//la opción seleccionada es un 3D

			if ($("#tpvidord").val().indexOf(valTpv+',') < 0) {//si el tpv no se había seleccionado anteriormente lo agrego

				$("#tpvidord").val($("#tpvidord").val()+valTpv+',');
				$("#pasaorden").val($("#pasaorden").val()+strTpv+'\n');

			}

		}

	} else {//si la opción marcada NO está seleccionada 

		if (strTpv.indexOf(' 3D') > 0) {//borro los tpv quitados

			$("#tpvidord").val($("#tpvidord").val().replace(valTpv+',', ''));
			$("#pasaorden").val($("#pasaorden").val().replace(strTpv+'\n',''));

		}
	}
});


//función para determinar la cantidad de pasarelas sin 3D que se ha puesto al comercio
$("#pasarela2").blur(function() {

	if ($("#pasarela2 :selected").length == 0) {
		$('#divFormHid4').css('display', 'none');
		$('#divFormHid3').css('display', 'block');
		alert ('El comercio debe tener al menos 1 TPV Seguro en "Pasarela para Pagos Diferidos y al Momento"');
		return false;
	}

	var valeNS = []; 
	$('#pasarela2 :selected').each(function(i, selected){ 
		valeNS[i] = $(selected).val(); 
	});

	$.post('componente/comercio/ejec.php', {
		fun: 'cheqNsec',
		cod: valeNS
	}, function(data){
		var datos = eval('(' + data + ')');
		if (datos.error.length > 0) alert(datos.error);
		if (datos.cont) {
			if (datos.cont > 1) return true;
			else if (datos.cont == "0") return true; 
			else {
				$('#divFormHid4').css('display', 'none');
				$('#divFormHid3').css('display', 'block');
				alert ('El comercio debe tener al menos 2 TPV No Seguros en "Pasarela para Pagos Diferidos y al Momento"');
				return 'false';
			}
		}
	});

});

</script>
