<?php

defined('_VALID_ENTRADA') or die('Restricted access');


$html = new tablaHTML();
global $temp;
$corCreo = new correo();
$comer = $_SESSION['idcomStr'];
$d = $_REQUEST;
$ent = new entrada;
// print_r($_SESSION);


// print_r($d);

if (stripos(_ESTA_URL, 'localhost') > 0) {
//  $d['valmon'] ='';
//  $d['pasaresc'] ='';
//  $d['valCom'] = '5000';
//  $d['comercio'] = '18';
//  $d['batchPas'] = '63,64';
//  $d['tarjes'] = '2';
//	$d['tasaApl'] = '0.8700';
//	$d['inserta'] = 'true';
//	$d['comer'] = '1';
//	$d['nombre'] = 'julio';
//	$d['email'] = 'jtoirac@gmail.com';
//	$d['trans'] ='';
//	$d['servicio'] = 'zfsdfgsf';
//	$d['idioma'] = 'es';
//	$d['amex'] = '2';
//	$d['pago'] = 'S';
//	$d['tiempo'] = '3';
//	$d['pasarela'] = '32';
//	$d['moneda'] = '978';
//	$d['importe'] = '435';
//	$d['usd'] = '500';
//	$d['dir'] = '/home/julio/www/concentrador/admin/';
//	$d['enviar'] = 'Enviar';
}

$fechaNow = time();
if (_MOS_CONFIG_DEBUG) {
	echo "<br>";
	var_dump($d);
	echo "<br>";
}

//inserta Articulo
if ($d['inserta']) {

	$arrEnv = array(
			'comercio'	=> $d['comercio'],
			'nombre'	=> $d['nombre'],
			'email'		=> $d['email'],
			'importe'	=> $d['importe'],
			'moneda'	=> $d['moneda'],
			'amex'		=> $d['tarjes'],
			'tiempo'	=> $d['tiempo'],
			'idioma'	=> $d['idioma'],
			'trans'		=> $d['trans'],
			'pasarela'	=> $d['pasarela'],
			'servicio'	=> $d['servicio'],
			'pago'		=> $d['pago'],
			'usd'		=> $d['usd'],
			'moneda'	=> $d['moneda'],
			'eur'		=> $d['eur'],
			'tasaApl' => $d['tasaApl'],
			'dir' => substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')+1)
	);
//	var_dump($arrEnv);exit;
	
	echo envPago($arrEnv);
	
}
// $monedaid = '978';

if ($_SESSION['codProdReserv']) {
	$query = "select case p.codigo when '' then p.nombre else concat(p.nombre, ' - ', p.codigo) end nombre, r.precio
				from tbl_productosReserv r, tbl_productos p
				where p.id = r.idProd and r.codigo = " . $_SESSION['codProdReserv'];
//	echo "$query<br>";
	$temp->query($query);
	$precio = 0;
	$servicio = '';
	while ($temp->next_record()) {
		$precio += $temp->f('precio');
		$servicio .= $temp->f('nombre') . "\n";
	}
	$monedaid = $temp->f('idMon');
}

//carga la tasa de cambio del CUC si lleva la tasa del Mitur
//trabajar en la tasa del BNC
$usaTasa = false;
$q = "select count(id) total from tbl_comercio where id in ($comer) and usarTasaCuc = 2";
// echo $q;
$temp->query($q);
if ($temp->f('total') > 0)  {
	$usaTasa = true;
	$arrCamb = array();
	$q = "select moneda, idmoneda from tbl_moneda order by moneda";
	$temp->query($q);
	$arrMon = $temp->loadRowList();
	$strDal = "{";
	foreach ($arrMon as $item) {
		$q = "select cambio from tbl_cambioCUC u, tbl_comercio c where u.comercio = c.id and c.id in ($comer) and u.moneda = '{$item[1]}' 
				order by u.fecha desc limit 0,1";
		//		echo $q."<br>";
		$temp->query($q);
		$camb = $temp->f('cambio');
		//		$camb = leeSetup("minturCUC-".$item[0]);
		$arrCamb[] = array($item[0],$camb);
	}
	$strDal = json_encode($arrCamb);
// 		echo $strDal;

// 	$q = "select m.moneda, cambio from tbl_cambioCUC u, tbl_moneda m, tbl_comercio c where m.idmoneda = u.moneda and u.comercio = 
// 			c.id and c.idcomercio = $idcomercio order by m.moneda, u.fecha";
	//	echo $q;
}

//carga la tasa de cambio puesta del USD al EUR
// si no la tiene puesta d� error
$usaEUR = 0;
$temp->query("select max(operEur) total from tbl_comercio where id in ($comer)");
$usaEUR = $temp->f('total');
//	$arrCambEUR = array();
//	$q = "select moneda, idmoneda from tbl_moneda where idmoneda != 978 order by moneda";
//	$temp->query($q);
//	$arrMonEUR = $temp->loadRowList();
//	$strDal = "{";
//	foreach ($arrMonEUR as $item) {
//		$q = "select tasa from tbl_tasaComercio where idcomercio in ($comer) and monedaBas = '{$item[1]}' and monedaCamb = 978
//				order by fecha desc limit 0,1";
//		//		echo $q."<br>";
//		$temp->query($q);
//		if ($temp->num_rows()) {
//			$camb = $temp->f('cambio');
//		} else {
////			$camb = 1/leeSetup($item[0]);//coge la tasa del cambio del concentrador
//			$camb = 0;//no se permite que de no tener la tasa definida por ellos se haga la operaci�n de esta forma
//		}
//		$arrCambEUR[] = array($item[0],$camb);
//	}
//	$strDal = json_encode($arrCambEUR);
// 		echo $strDal;

// 	$q = "select m.moneda, cambio from tbl_cambioCUC u, tbl_moneda m, tbl_comercio c where m.idmoneda = u.moneda and u.comercio = 
// 			c.id and c.idcomercio = $idcomercio order by m.moneda, u.fecha";
	//	echo $q;
//}

?>
<style>
<!--
#tarjt label {width: 60px;height: 36px; margin:5px 5px; display:block; float:left;}
#tarjt input {display:block; float:left;}
#tuto {margin:10px auto; width: 285px;}
.image1 {background-image: url('images/amex.jpg');}
.image2 {background-image: url('images/visa.jpg');}
.image3 {background-image: url('images/mastercard.jpg');}
.image4 {background-image: url('images/dinners.jpg');}
.image5 {background-image: url('images/jcb.jpg');}
.image6 {background-image: url('images/4b.jpg');}
.image7 {background-image: url('images/euro6000.jpg');}
.image8 {background-image: url('images/servired.jpg');}
.image9 {background-image: url('images/maestro.jpg');}
.image10 {background-image: url('images/visa-electron.jpg');}
.image11 {background-image: url('images/virtual-card.jpg');}
-->
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$("#moncuc").blur(function(){calcCUC();});
<?php 
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 24 || $_SESSION['rol'] == 16 || $_SESSION['rol'] == 10) 
	echo "$(\"#importe\").prop('readonly', false);";
 elseif ($usaTasa && $_SESSION['vendecuc'] == 1)
 	echo "$(\"#importe\").prop('readonly', true);$(\"#moneda\").change(function(){calcCUC();});";
?>
		
		$("#div_tiempo").hide();
		$(".pago").click(function(){
			if($(this).val() == 'S') $("#div_tiempo").hide();
			else $("#div_tiempo").show();
		});

		$(function() {
			$('textarea').supertextarea({maxw: 280, maxh: 100, minw: 200, minh: 20, dsrm: {use: false}, tabr: {use: false}, maxl: 1000});
		});
	    
		function calcCUC(){
			var dato = jQuery.parseJSON('<?php echo $strDal; ?>');
			var mon = $("#moneda option:selected").text();
			if ($("#moncuc").val().length) {
				if (!$.isNumeric($("#moncuc").val())) alert('Debe entrar un n�mero en el campo de importe.');
				else {
					for (var i=0;i<dato.length;i++){
						var mnd = dato[i];
						if (mnd.indexOf(mon) == 0) {
							$("#importe").val(($("#moncuc").val() * mnd[1]).toFixed(2)).prop('readonly', true);
						}
					}
					if ($("#importe").val() == 0 && $("#importe").val() > 0) alert("No tiene habilitado el cambio para esa moneda, cambie la moneda para poder enviar el pago.");
				}
			}
		}
	});

<?php
if ($alerta) echo "alert('$alerta');";
echo "</script>";

//listado de monedas.
// [{"idmoneda":"978","moneda":"EUR"},{"idmoneda":"840","moneda":"USD"},{"idmoneda":"826","moneda":"GBP"}]
$temp->query("select idmoneda, moneda from tbl_moneda");
$arrMon = $temp->loadRowList();
$jsonMon = $jsonCamb = "[";
foreach ($arrMon as $moneda) {
	$jsonMon .= '{"idmoneda":"'.$moneda[0].'", "moneda":"'.$moneda[1].'"},';
	$jsonCamb .= '{"moneda":"'.$moneda[1].'", "cambio":"'.leeSetup($moneda[1]).'"},';
}
// 	echo $q."<br>";
$temp->query("select cambio from tbl_cambioCUC where comercio = '{$comerE}' and moneda = 978 order by fecha desc limit 0,1");
$jsonCamb .= '{"moneda":"CUC", "cambio":"'.$temp->f('cambio').'"},';


$jsonCamb = rtrim($jsonCamb,',');
$jsonCamb .= "]";
$jsonMon = rtrim($jsonMon,',');
$jsonMon .= "]";

//echo $jsonMon."<br>";
//echo $jsonCamb."<br>";

$validi = 'en';
$valamex = '0';
$valors = 'S';

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_PAGODIRECTO;
$html->anchoTabla = 600;
$html->anchoCeldaI = 255;
$html->anchoCeldaD = 325;
$html->tituloTarea = _COMERCIO_PAGO;
$html->java = $javascript;

if ($d['identf']) {
	$q = "select r.nombre, r.email, r.codigo, r.pasarela, r.idioma, r.servicio, c.id, r.amex, r.pMomento, r.valor_inicial, r.moneda, r.fecha
				from tbl_reserva r, tbl_comercio c 
				where r.id_comercio = c.idcomercio and id_reserva = {$d['identf']}";

	$temp->query($q);
	$nombre = $temp->f('nombre');
	$email = $temp->f('email');
	$pasaval = $temp->f('pasarela');
	$validi = $temp->f('idioma');
	$servicio = $temp->f('servicio');
	$valamex = $temp->f('amex');
	$valcom = $temp->f('id');
	$valors = $temp->f('pMomento');
	$precio = $temp->f('valor_inicial');
	$valmon = $temp->f('moneda');
	$fecamb = date('mdHi');
	$codigo = $temp->f('codigo');
	
// 	date_default_timezone_set("America/Havana");
// 	echo date_default_timezone_get();
	
// 	echo substr($codigo, 0, strlen($codigo)-2)." == ".$fecamb;
	if (substr($codigo, 0, strlen($codigo)-2) != $fecamb) {
		$arrvales = array('A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T',
							'U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9');
		$codlarg = strlen($codigo);
		if ($codlarg < 18) {
			if (strrpos($codigo, "/") && ($codlarg - (strrpos($codigo, "/"))) > 1) 
				$codigo = substr($codigo, 0, strrpos($codigo, "/")+1).$arrvales[array_search(substr($codigo, strrpos($codigo, "/")+1,1), $arrvales)+1];
			else 
				$codigo .= "/".$arrvales[0];
		}
	} else $codigo = '';
}

$html->inHide($valmon, 'valmon');
$html->inHide($pasaval, "pasaresc");
$html->inHide($comer, "comer");
$html->inHide("true", "inserta");
$html->inHide("5000", "valCom");
$html->inTextb(_FORM_NOMBRE, $nombre, "nombre");
$html->inTextb(_FORM_CORREO, $email, "email");
$html->maxLenght = 19;
$html->inTextb(_COMPRUEBA_TRANSACCION, $codigo, "trans", null, " " . _COMERCIO_GENERA);
$html->inTexarea(_COMERCIO_SER, $servicio, 'servicio', 7);
$valInicio = array('en', 'es', 'it');
$etiq = array(_PERSONAL_ING, _PERSONAL_ESP, _PERSONAL_ITA);
$html->inRadio(_PERSONAL_IDIOMA, $valInicio, 'idioma', $etiq, $validi);
//$valInicio = array('0', '1');
// $etiq = array(_COMERCIO_VISA, _COMERCIO_AMEX);
// $html->inRadio(_COMERCIO_TARJETA, $valInicio, 'amex', $etiq, $valamex);

if (strpos($_SESSION['idcomStr'], ',')) {
	$valInicio = "select id, nombre from tbl_comercio where id in ({$_SESSION['idcomStr']}) and activo = 'S' order by nombre";
	$html->inSelect(_COMERCIO_TITULO, "comercio", 2, $valInicio, $valcom);
} else
	$html->inHide($comer, "comercio");

$valInicio = array('S', 'N');
$etiq = array(_COMERCIO_ALMOMENT, _COMERCIO_DIFERI);
$html->inRadio(_COMERCIO_PAGOA, $valInicio, 'pago', $etiq, $valors);
$html->maxLenght = 2;
$html->inSelect(_COMERCIO_INVACTIVA, "tiempo", 4, array(1,5), '3', null, _COMERCIO_INVACTIVAEXPL);
$html->maxLenght = 15;


if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 24 || $_SESSION['rol'] == 16 || $_SESSION['rol'] == 10)
	$valInicio = "select idPasarela id, nombre from tbl_pasarela where tipo = 'P' and activo = 1 order by nombre";
else {
	$query = "select pasarelaAlMom from tbl_comercio where id in (" . $comer . ")";
	$temp->query($query);
    $pasar = implode(',', $temp->loadResultArray());
    $pasar = ltrim(rtrim($pasar, ','), ',');
	$valInicio = "select p.idPasarela id, case secure when 1 then 'Segura' else 'NO Segura' end nombre from tbl_pasarela p where p.idPasarela in (
					$pasar) group by secure order by p.idPasarela";
// 	echo $valInicio;
}
// if ($_SESSION['id'] == 10) echo $valInicio;
// $temp->query($valInicio);
// $arrPAs = $temp->loadAssocList();
$pasarela = array();
$html->inHide($pasar, 'batchPas');
$html->inSelect(_COMERCIO_PASARELA, "pasarela", 2, $pasarela);

//tarjetas
// $html->inSelect(_PAGO_TARJETA, 'tarjt', 2, $valInicio);
$html->inTextoL(_PAGO_TARJETA, 'tarjt');
$html->inTextoL(_PAGO_TARJENO);

//Adiciona cambio de USD a EUR para los comercios que lo tengan
//echo $usaEUR;
switch ($usaEUR) {
	case 2:
		$html->inCheckBox('Operaci�n con cambio a EUR?', 'eurs', 5, 1, null, null, null, 'checked');
		break;
	case 1:
		$html->inHide(1, 'eur');
		break;
	default:
		$html->inHide(0, 'eur');
		break;
}

//Adiciona Cuc para los comercios que lo lleven
if ($usaTasa && $_SESSION['vendecuc'] == 1) {
	$html->inTextb('Monto CUC', $valor, 'moncuc', null, ' Monto en CUC a convertir');
}
//$html->inTextoL('Tasa de cambio del USD al EUR: ', 'tasaEUR');
$html->inHide('', 'tasaApl');
$html->inSelect(_COMPRUEBA_MONEDA, 'moneda', 3);
$html->inTextb(_COMPRUEBA_IMPORTE, $precio, "importe");
//$html->inTextb(_COMPRUEBA_IMPORTE, $precio, "usd");

//if ($comer == 'todos') 
//	$query = "select idmoneda id, moneda nombre from tbl_moneda order by moneda";
//else $query = "select idmoneda id, moneda nombre from tbl_moneda where idmoneda in ('840','978','826','392','124') order by moneda";

echo $html->salida();


?>

<script type="text/javascript" src="../js/jquery.myfun_c00cc3a629e84ef270c28b0d705a1ce1.js"></script>
<script type="text/javascript" lang="Javascript" >
var mimoneda = '';

function verifica() {
	var alivio = revoper();

	if (alivio === 'false') return false;

	if (
			(checkField (document.forms[0].nombre, isAlphanumeric, ''))&&
			(checkField (document.forms[0].email, isEmail, ''))&&
			(checkField (document.forms[0].tiempo, isInteger, ''))&&
			(checkField (document.forms[0].importe, isMoney, ''))&&
			(checkField (document.forms[0].servicio, isAlphanumeric, '')) &&
			(checkField (document.forms[0].moneda, isInteger, ''))// &&
// 			(CompareField(document.forms[0].valCom,document.forms[0].importe,'mayor','El valor de la transacci�n debe ser menor de 5 000.00'))&&
//			(checkField (document.forms[0].moncuc, isMoney, 'true'))&&
			//(checkField (document.forms[0].trans, isAlphanumeric, 'true'))
		) {
		if (document.forms[0].trans.lenght > 1) {
			if (checkField (document.forms[0].trans, isUrl, '')) return true;
			else return false;
		} else {
			if ($("#trans").val().length > 19) return false;
			else return true;
		}
	}
	return false;
}

function revoper(){
	if ($("#trans").val().length) {

		var comid;
		if (!$("#comercio :selected").length) comid = $("#comercio").val(); else comid = $("#comercio :selected").val();
			$(".title_tarea1").esperaDiv('cierra');
			$(".title_tarea1").esperaDiv('muestra');
		$.post('componente/comercio/ejec.php',{
			fun: 'revoper',
			cod: $("#trans").val(),
			com: comid
		},function(data){
			var datos = eval('(' + data + ')');
			$(".title_tarea1").esperaDiv('cierra');
			$("#enviaForm").show();
			if (datos.error.length > 0) alert(datos.error);
			if (datos.cont === 'true') {
				return 'true';
			} else {
				alert ("El c�digo de la operaci�n est� repetido");
				$("#trans").focus();
				return 'false';
			}
		});
		
	} else return true;
}

function pagoEur(paso){
	var comid;
	if (!$("#comercio :selected").length) comid = $("#comercio").val(); else comid = $("#comercio :selected").val();
		$(".title_tarea1").esperaDiv('cierra');
//			$(".title_tarea1").esperaDiv('muestra');
	if (paso === 1) {
		$.post('componente/comercio/ejec.php',{
			fun: 'pagoEur',
			com: comid
		},function(data){
			$(".title_tarea1").esperaDiv('cierra');
			var datos = eval('(' + data + ')');
			$("#enviaForm").show();
			if (datos.error.length > 0) alert(datos.error);
			else {
				$("#tasaApl").val(datos.tasa);
				$("#div_importe .izquierda1").html("<input maxlength='15' class='formul' value='' name='usd' id='usd' type='text'>"+
					"<input type='hidden' id='importe' name='importe'>");
				$("#usd").change(function(){
					$("#importe").val(($("#usd").val()*datos.tasa).toFixed(2));
				});
				if (datos.sale.length > 0) {
					$("#moneda").get(0).options.length = 0;
					var options = $("#moneda");
					options.empty();
					if (datos.sale.length === 1 && datos.sale[0].moneda === 'EUR'){
						var sale = '<?php echo $jsonMon; ?>';
						var mon = eval('(' + sale + ')');
						$.each(mon, function(index,vale) {
							if (vale.idmoneda === '978')
								options.append($("<option />").val(vale.idmoneda).text(vale.moneda)).attr("selected", "selected");
							else
							options.append($("<option />").val(vale.idmoneda).text(vale.moneda));
						});
					} else {
						$.each(datos.sale, function(index,vale) {
							options.append($("<option />").val(vale.idmoneda).text(vale.moneda));
						});
					}
				}

			}
		});
	} else {
		$("#tasaApl").val(0);
		$("#div_importe .izquierda1").html('<input maxlength="15" class="formul" value="" name="importe" id="importe" type="text">');
		$("#importe").val('');
		$("#usd").val('');
	}
}

function cabiaPas() {
	var comid;
	if (!$("#comercio :selected").length) comid = $("#comercio").val(); else comid = $("#comercio :selected").val();
		$(".title_tarea1").esperaDiv('cierra');
		$(".title_tarea1").esperaDiv('muestra');
	$.post('componente/comercio/ejec.php',{
		fun: 'cambps',
		pas: $("#batchPas").val(),
		pag: $("input[name=pago]:checked").val(),
		com: comid
	},function(data){
		$(".title_tarea1").esperaDiv('cierra');
		var datos = eval('(' + data + ')');
		$("#enviaForm").show();
		if (datos.error.length > 0) alert(datos.error);
		if (datos.cont) {
			if (datos.cont.length > 0) {
				var options = $("#pasarela");
				options.empty();
				$.each(datos.cont, function(index,vale) {
					options.append(new Option(this.nombre, this.id));
				});
				if (datos.euro === '1') {//Opcional
					$("#div_eurs").show().html('<input type="hidden" name="eur" value="1" />'); 
					$("#importe").val('');
					$("#usd").val('');
					pagoEur(1);
				} else if (datos.euro === '2') {
					$("#importe").val('');
					$("#div_eurs").html('<div style="width:255px" class="derecha1">Operaci�n con cambio a EUR?:</div><div style="width:325px" class="izquierda1"><input type="checkbox" id="eurs" value="1"  class="formul" name="eur" checked /></div>').show(); 
					$("#usd").val('');
					pagoEur(1);
				} else {
					pagoEur(0);
					$("#div_eurs").show().html('<input type="hidden" name="eur" value="0" />');
					$("#div_importe .derecha1").html('Importe: ');
					$("#importe").val('');
					$("#usd").val('');
				}
				mimoneda = $("#moneda option:selected").val();
				busca();
			}
		}
	});
}
	
function busca() {
	$(".title_tarea1").esperaDiv('cierra');
	$(".title_tarea1").esperaDiv('muestra');
	$("#moneda").get(0).options.length = 0;
	var passs = $("#pasaresc").val();
	if ($("#pasarela").val() !== '')  passs = $("#pasarela").val();
		$.post('componente/comercio/ejec.php',{
				fun:'instpago',
				email: $("#email").val(),
				pas:passs
			},function(data){
				$(".title_tarea1").esperaDiv('cierra');
				var datos = eval('(' + data + ')');
				$("#enviaForm").show();
				if (datos.error.length > 0) alert(datos.error);
				if (datos.sale) {
					if (datos.sale.length > 0) {
						var options = $("#moneda");
						options.empty();
						if (datos.sale.length === 1 && datos.sale[0].moneda === 'EUR'){
							var sale = '<?php echo $jsonMon; ?>';
							var mon = eval('(' + sale + ')');
							$.each(mon, function(index,vale) {
								if (vale.idmoneda === '978')
									options.append($("<option />").val(vale.idmoneda).text(vale.moneda)).attr("selected", "selected");
								else
								options.append($("<option />").val(vale.idmoneda).text(vale.moneda));
							});
						} else {
							$.each(datos.sale, function(index,vale) {
								options.append($("<option />").val(vale.idmoneda).text(vale.moneda));
							});
						}
					}

					//verifica el tipo de pago y carga las monedas y las tasas
					if($("#eurs").attr("checked") === 'checked') pagoEur(1);
					else pagoEur(0);

					$("#moneda").val(mimoneda).change();

					var options = $("#tarjt");
					var texto = '<?php echo _PAGO_TARJETA."<br>"; ?><div id="tuto" >';
					var lin = '';
					
					$.each(datos.tar, function(index,vale) {
						lin = lin + '<input type="radio" name="tarjes" id="image'+this.id+'" value="'+this.id+'"' ;
						if (this.id === 2) lin = lin + ' checked="checked"';
						lin = lin + '><label class="image'+this.id+'" for="image'+this.id+'"></label>';
					});
					options.html(texto+lin+'</div>');
				}
		});
}

	function cammbp() {
		var valPas = $("#pasaresc").val();
// 		alert(valPas);
		$('#pasarela option[value='+valPas+']').attr("selected","selected");
		busca();
		setTimeout(function(){$('#moneda option[value='+$('#valmon').val()+']').attr('selected','selected');},3000);
		
	}
		
$(document).ready(function(){
	$("#eurs").click(function(){
		if($(this).attr("checked") === 'checked') {
			alert('pagoEur');
			pagoEur(1);
		} else {alert('busca');busca();}
	});
		
		
// 	busca();
	cabiaPas();
	$("#pasarela").change(function(){busca();});
	
	setTimeout(cammbp,3000);

	$("#div_usd").hide();
//	$("#tasaEUR").hide();
	$("input[name=pago]").click(function(){cabiaPas()});
	$("#comercio").change(function(){cabiaPas()});
	
});
</script>