<?php defined( '_VALID_ENTRADA' ) or die( header("admin/index.php") );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$html = new tablaHTML;

global $temp, $d;
$corCreo = new correo();
$ent = new entrada();

if (_MOS_CONFIG_DEBUG) {
// $_REQUEST['componente'] = 'comercio';
// $_REQUEST['pag'] = 'transferencia';
// $_REQUEST['usuario'] = '440';
// $_REQUEST['comer'] = '141624678529';
// $_REQUEST['inserta'] = '1';
// $_REQUEST['nombre'] = 'itr';
// $_REQUEST['idioma'] = 'es';
// $_REQUEST['email'] = ' s.ravich@mechnikov.org';
// $_REQUEST['importe'] = '4012';
// $_REQUEST['monedas'] = '978';
// $_REQUEST['comercio'] = '139333436635';
// $_REQUEST['enviar'] = 'Enviar';
}

$d = $_REQUEST;
$fechaNow = time();
$error = $incluye = "";


//envTransf($arrEnt);

if ($_SESSION['comercio'] == 'todos') {
    $comer = '122327460662';
} elseif($_SESSION['comercio'] == 'varios') {
    $q = "select idcomercio from tbl_comercio where id in ({$_SESSION['idcomStr']}) limit 0,1";
    $temp->query($q);
    $comer = $temp->f('idcomercio');
} else {
    $comer = $_SESSION['comercio'];
}

if ($d['comercio']) $comer = $d['comercio'];
$query = "select * from tbl_comercio where idcomercio = {$comer}";
$temp->query($query);
$comercioN = $temp->f('nombre');
$estCom = $temp->f('estado');
$datos = $temp->f('datos');
$prefijo = $temp->f('prefijo_trans');
$datos = $temp->f('datos');
$idCom = $temp->f('id');
$valMin = $temp->f('minTransf');
$ante = '';

//inserta Art&iacute;culo
if ($d['inserta']) {
//	print_r($d);
//	echo $valMin;
	if ($d['importe'] >= $valMin) {
		$arrEnv = array(	
			'comercio' => $d['comercio'],
			'nombre' => $d['nombre'],
			'email' => $d['email'],
			'importe' => $d['importe'],
			'monedas' => $d['monedas'],
			'idioma' => $d['idioma'],
			'dir' => substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')+1)
		);
	
		echo envTransf($arrEnv);
	
	} else {
		echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">El comercio no está autorizado a hacer transferencias de menos de $valMin</div>";
	}

}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_TRANSFERENCIA_MOD;
$html->tituloTarea = "&nbsp;";
$html->anchoTabla = 600;
$html->anchoCeldaI = $html->anchoCeldaD = 245;
$html->java = "
	<script language=\"JavaScript\" type=\"text/javascript\">
	function verifica() {
		if (
				(checkField (document.forms[0].nombre, isAlphanumeric, ''))
				&&(checkField (document.forms[0].email, isEmail, ''))
				&&(checkField (document.forms[0].importe, isMoney, ''))
// 				&&(checkField (document.forms[0].servicio, isAlphanumeric, ''))
			) {
// 			if (document.forms[0].importe.value < ".$valMin.") {
// 				alert ('Sólo válido para montos iguales o mayores de ".$valMin." Euros.'); 
// 				document.forms[0].importe.focus(); 
// 				return false;
// 			} else {
				document.getElementById('comer').value = document.getElementById('comercio').value;
				document.getElementById('enviaForm').style.display='none';
				return true;
// 			}
		}
		return false;
	}

	$(function() {
		$('textarea').supertextarea({
		   maxw: 280
		  , maxh: 100
		  , minw: 130
		  , minh: 20
		  , dsrm: {use: false}
		  , tabr: {use: false}
		  , maxl: 1000
		});
	});
	
//	$(document).ready(function(){ $('textarea').attr('value', '');});
	
</script>";

$html->inHide($_SESSION['id'], 'usuario');
$html->inHide($comer, 'comer');
$html->inHide(true, 'inserta');
$html->inTextb(_FORM_NOMBRE_CLIENTE, $nombre, 'nombre', null, null, null);
$arrIdio = array ('es', 'en');
$arrEtiq = array(_PERSONAL_ESP, _PERSONAL_ING);
$html->inRadio(_PERSONAL_IDIOMA, $arrIdio, 'idioma', $arrEtiq, 'es', null, false);
$html->inTextb(_FORM_CORREO, '', 'email');
//$query = "select id, nombre from tbl_paises order by nombre";
//$html->inSelect(_REPORTE_PAIS, 'pais', 1, $query);
//$html->inTextb(_FORM_CUENTA, '', 'cuenta', null, null, null, _FORM_CUENTA_ALT);
$html->inTextb(_COMPRUEBA_IMPORTE, '', 'importe', null, null, null);
$query = "select idmoneda id, moneda nombre from tbl_moneda where activo = 1";
$html->inSelect(_COMERCIO_MONEDA, 'monedas', 2, $query, '978');
// $html->inTexarea(_COMERCIO_SER, $texx, 'servicio', 7, null, null, null, null, 'Aclarar con lujo de detalles el servicio prestado');
if ($_SESSION['comercio'] == 'todos') {
	$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 1, $query);
} elseif ($_SESSION['comercio'] = 'varios') {
	$query = "select idcomercio id, nombre from tbl_comercio where id in ({$_SESSION['idcomStr']}) and activo = 'S' order by nombre";
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 1, $query);
} else {
	$query = "select idcomercio id, nombre from tbl_comercio where id in ({$_SESSION['idcomStr']}) and activo = 'S' order by nombre";
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 1, $query);
}

echo $html->salida();


// function damePasar ($strPas, $mon = '840'){
// 	global $temp, $d;
// 	if ($mon != '978') {
// 		if (stripos($strPas, ',66')) $strPas = str_replace(',66', '', $strPas);
// 		if (stripos($strPas, '66,')) $strPas = str_replace('66,', '', $strPas);
// 	}
// 	$q = "select pasarela from tbl_transacciones where tipoOperacion = 'T' and pasarela in ($strPas) order by fecha desc limit 0,1";
// 	$temp->query($q);
// 	$pasarr = $temp->f('pasarela');
// 	$arrPas = explode(",", $strPas);
// 	for ($i=0; $i<count($arrPas); $i++) {
// 		if ($arrPas[$i] == $pasarr) {
// 			if ($i == (count($arrPas))-1) {
// 				$pasarel = $arrPas[0];
// 				$cont = 0;
// 			}
// 			else {
// 				$pasarel = $arrPas[$i+1];
// 				$cont = $i;
// 			}
// 			if ($pasarel == 66 && $mon == '978') return $pasarel;
// 			elseif ($pasarel == 66){
// 				if ($cont == (count($arrPas))-1) return $arrPas[0];
// 				else return $arrPas[$cont+1];
// 			} else return $pasarel;
// 		}
// 	}

// }
?>
<script type="text/javascript">
$("#importe").blur(function(){
	var valor = $("#importe").val();
	if (valor.indexOf('.') == -1) valor = valor+".00";
	compa = new Array('000.00','00.00','999.99','999.00','0000.00','9999.','9999.99','9999.00');
	for (var i=0; i<compa.length;i++) {
		if (valor.indexOf(compa[i]) > -1){
			alert("El importe entrado ha sido modificado. Verifíquelo");
			$("#importe").val(valor-(Math.floor((Math.random() * 100) + 1))).focus();
		}
	}
});
</script>




