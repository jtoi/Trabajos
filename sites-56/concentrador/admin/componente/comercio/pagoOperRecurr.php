<?php

defined('_VALID_ENTRADA') or die('Restricted access');


$html = new tablaHTML();
global $temp;
$corCreo = new correo();
$comer = $_SESSION['idcomStr'];
$d = $_POST;
$ent = new entrada;

// $d['comer'] = '131551467398'; $d['inserta'] = true; $d['comercio'] = '131551467398'; $d['nombre'] = 'June Baldry'; 
// $d['email'] = 'eduardo@travelnet.cu'; $d['importe'] = '185.00'; $d['trans'] = ''; $d['tiempo'] = '3'; $d['moneda'] = '978'; 
// $d['servicio'] = 'excursion'; $d['pago'] = 'N'; $d['idioma'] = 'es'; $d['pasarela'] = '21'; $d['enviar'] = 'Enviar';

$fechaNow = time();
if (_MOS_CONFIG_DEBUG) {
	echo "<br>";
	print_r($d);
	echo "<br>";
}

//inserta Articulo
if ($d['inserta']) {
		
	$arrayTo = array();
	if (strlen($d['comercio']) > 3) $query = "select * from tbl_comercio where idcomercio = {$d['comercio']}";
	else $query = "select * from tbl_comercio where id = {$d['comercio']}";
// 	echo $query;
	$temp->query($query);
	$comercioN = $temp->f('nombre');
	$palabra = $temp->f('palabra');
	$estCom = $temp->f('estado');
	$correoMas = $temp->f('correoMas');
	$correo = str_replace('"', '', str_replace("'", "", str_replace(" ", "", $d['email'])));
	$pasAlMom = $temp->f('pasarelaAlMom');
	$comId = $temp->f('idcomercio');
// 	echo "pasAlMom = ".$pasAlMom."<br>";

	if ($d['pasarela'] == '')
		$pasarela = 1;
	else {
		if (!strstr($pasAlMom, $d['pasarela'])) {//evito que los usuarios de varios comercios al pagar por uno de ellos usen una pasarela
			//que ese comercio no tenga
			$q = "select idPasarela from tbl_pasarela where secure = (select secure from tbl_pasarela where idPasarela in ({$d['pasarela']})) 
					and idPasarela in ($pasAlMom) limit 0,1";
			$temp->query($q);
			$pasarela =$temp->f('idPasarela');
		} else
			$pasarela = $d['pasarela'];
	}

	if ($palabra == 'lore') {//si el comercio no ha generado su palabra secreta se la genero
		$q = "update tbl_comercio set palabra = '".suggestPassword(20)."' where id = {$d['comercio']}";
		$temp->query($q);
	}

	$paso = true;
	if (!$d['trans']) {
        $trans = trIdent('', false);
	} else {
		if (!($trans = $ent->isLetraNumero($d['trans'], 15))) {
			$paso = false;
		}
	}
		
	if ($paso == true) {

		$query = "select count(*) total from tbl_reserva where codigo = '$trans' and id_comercio = '{$comId}'";
		$temp->query($query);
		
		if ($temp->f('total') == 0 ) {
			$query = "select count(*) total from tbl_transacciones where identificador = '$trans' and idcomercio = '{$comId}'";
			$temp->query($query);
	
			if ($temp->f('total') == 0) {
				$ser = str_replace("\n", " ", str_replace(';', ',', htmlentities($d['servicio'], ENT_QUOTES)));
	                
				$nmbr = htmlentities($d['nombre'], ENT_QUOTES);
				$query = "insert into tbl_reserva (id_admin, id_comercio, est_comer, codigo, nombre, email, servicio, valor_inicial, moneda, fecha, 
								pMomento, idioma, pasarela, tiempoV, url, amex)
							values ({$_SESSION['id']}, '{$comId}', '$estCom', '$trans', '{$nmbr}', '{$correo}', '{$ser}', '"
									.str_replace(",", ".", $d['importe'])."', '{$d['moneda']}', $fechaNow, '{$d['pago']}', '{$d['idioma']}', 
									$pasarela, {$d['tiempo']}, '{$_SERVER["SERVER_NAME"]}', {$d['amex']})";
				$temp->query($query);
				$error = $temp->getErrorMsg();
				if (strlen($error) > 0) {
					$subject = "Error al insertar la invitación de pago";
					$mensaje = "SQL: ".$query."<br>\nError: ".$error;
					$corCreo->todo(24, $subject, $mensaje);
					echo "<div style='text-align:center;color:red;'>"._COMERCIO_ERROR_INVIT."</div>";
				} else {
					if ($_SESSION['codProdReserv']) {
						$query = "update tbl_productosReserv set codVenta = '$trans' where codigo = '{$_SESSION['codProdReserv']}'";
						$temp->query($query);
					}
					
					$query = "select moneda from tbl_moneda where idmoneda = {$d['moneda']}";
					$temp->query($query);
					$moneda = $temp->f('moneda');
					
					if ($d['pago'] == 'S') { //Pago al momento
						$importe = ($d['importe'] * 100);
						$firma = md5($comId . $trans . $importe . $d['moneda'] . 'P' . $palabra);
						$form = "
	                                <form name='envPago' method='post' action='"._ESTA_URL."/index.php'>
	                                    <input type='hidden' name='pasarela' value='{$pasarela}'/>
	                                    <input type='hidden' name='comercio' value='{$comId}'/>
	                                    <input type='hidden' name='transaccion' value='$trans'/>
	                                    <input type='hidden' name='importe' value='$importe'/>
	                                    <input type='hidden' name='moneda' value='{$d['moneda']}'/>
	                                    <input type='hidden' name='operacion' value='P'/>
	                                    <input type='hidden' name='idioma' value='{$d['idioma']}'/>
	                                    <input type='hidden' name='amex' value='{$d['amex']}'/>
	                                    <input type='hidden' name='firma' value='$firma'/>
	                                </form>
	                                <script>document.envPago.submit();</script>
						";
						echo $form;
					} else { //Pago por correo
	                        //Invitación de Pago que se envía al cliente y a mí
						include 'lang/correo'.$d['idioma'].".php";
						$arrayTo = array();
						$query = "select ";
						if ($d['idioma'] == 'en') {
							$query .= "correo_eng correo ";
							$iga = "clickhere.png";
							$subject = ' Invitation from ' . $comercioN . ' to make the payment through Ecommerce Administrator';
							if ($pasarela != 1)
								$adic = "<br><br>For safe e-payments, after the submission of your card data, your issuing bank will assign a security
											code or PIN associated to the card. In case you don´t have the code, you can contact your issuing 
											bank and request it for free.";
						} else {
							$iga = "clicaqui.png";
							$query .= "correo_esp correo ";
							$subject = ' Invitacion de ' . $comercioN . ' a realizar el pago a traves del Administrador de Comercios';
							if ($pasarela != 1)
								$adic = "<br><br>Para pagos electr&oacute;nicos seguros, despu&eacute;s de introducir los datos de la tarjeta, su banco emisor 
												lo identificar&aacute; con un c&oacute;digo o pin de seguridad asociado a la misma. En caso de no poseerlo 
												contacte con su banco y solic&iacute;telo de forma gratuita.";
						}
						$query .= " from tbl_comercio where idcomercio = '{$comId}'";
						$temp->query($query);
						$message = "<style>.boton{background-color:#5EBEEF;color:white;display:block;border:2px solid navy;font-weight:bold;height:30px;"
								. "padding-top:5px;text-align:center;text-decoration:none;vertical-align:middle;width:90px;line-height:26px;"
								. "margin:0 auto;}</style>";
						$message .= $temp->f('correo');
						$url = "<BR /><a class='boton' href='" . _ESTA_URL . "/pagoOnline.php?cod=$trans&com=" . $comId . "'>" . _CLICK_AQUI . "</a>";
						$urla = "<BR />\n" . _ESTA_URL . "/pagoOnline.php?cod=$trans&com=" . $comId;
						
						$message = str_replace('{importe}', number_format($d['importe'], 2, '.', ' ') . ' ' . $moneda, $message);
						$message = str_replace('{servicio}', $d['servicio'], $message);
						$message = str_replace('{comercio}', $comercioN, $message);
						$message = str_replace('{urla}', $urla, $message);
						$message = str_replace('{url}', $url, $message) . $adic;
						
						if (strstr($correo, ";"))
							 $corrArr = explode(";", $correo);
						elseif (strstr($correo, ","))
							$corrArr = explode(",", $correo);
						else
							$corrArr[0] = $correo;
						
						if (_MOS_CONFIG_DEBUG) {
							echo "correoArr=";
							print_r($corrArr);
							echo "<br>";
						}
						
						foreach ($corrArr as $item) {
							$arrayTo[] = array($d['nombre'], $item);
						}
						
						if (_MOS_CONFIG_DEBUG) {
							print_r($arrayTo);
							echo "<br>";
						}
						
						$est = true;
						foreach ($arrayTo as $todale) {
							if ($est) {
								$corCreo->to($todale[1]);
								$est = false;
							} else $corCreo->add_headers ("Cc: ".$todale[1]);
							
							if (_MOS_CONFIG_DEBUG)
								echo "header = $headers<br>";
							if (_MOS_CONFIG_DEBUG)
								echo "to = $to<br>";
							if (_MOS_CONFIG_DEBUG)
								echo "mensaje = $message<br>";
						}
//						$corCreo->reply = $_SESSION['email'];
						$corCreo->todo(23, $subject, $message);
						
						//Aviso de envío de la invitación de pago
						$arrayTo = array();
						if ($correoMas == 1) {
							$q = "select nombre, email from tbl_admin where idcomercio = '{$comId}' and correoT = 1 and activo = 'S'";
							if (_MOS_CONFIG_DEBUG) echo $q."<br>";
							$temp->query($q);
							$arrayTo = $temp->loadRowList();
						}
						$arrayTo[] = array($_SESSION['admin_nom'], $_SESSION['email']);
						if (_MOS_CONFIG_DEBUG) {
							echo "<br>**************************************************************<br>";
							print_r($arrayTo);
							echo "<br>**************************************************************<br>";
							print_r($_SESSION);
							echo "<br>**************************************************************<br>";
							echo "<br>";
						}
						
						$subject = _COMERCIO_EMAIL_SUBJECT;
						$message = _COMERCIO_EMAIL_MES;
						$q = "select nombre from tbl_pasarela where idPasarela = '$pasarela'";
						$temp->query($q);
						$pasa = $temp->f('nombre');
						$message = str_replace('{trans}', $trans, $message);
						$message = str_replace('{servicio}', $d['servicio'], $message);
						$message = str_replace('{nombre}', $d['nombre'], $message);
						$message = str_replace('{importe}', number_format($d['importe'], 2, '.', ' '), $message);
						$message = str_replace('{moneda}', $moneda, $message);
						$message = str_replace('{pasarela}', $pasa, $message);
						
						$est = true;
						foreach ($arrayTo as $todale) {
							if ($est) {
								$corCreo->to($todale[1]);
								$est = false;
							} else $corCreo->add_headers ("Cc: ".$todale[1]);
							
							if (_MOS_CONFIG_DEBUG)
								echo "header = $headers<br>";
							if (_MOS_CONFIG_DEBUG)
								echo "to = $to<br>";
							if (_MOS_CONFIG_DEBUG)
								echo "mensaje = $message<br>";
						}
						$corCreo->todo(24, $subject, $message);
					}
					
					echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
	                        " . _COMERCIO_SOLC_SI . "</div>";
				}
			} else
				echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">
	                " . _COMERCIO_CODE_YA . "</div>";
		} else
			echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">
                " . _COMERCIO_CODE_YA . "</div>";
	} else
		echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">
                " . _COMERCIO_CODEVALID . "</div>";
}
$monedaid = '978';

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

?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#moncuc").blur(function(){calcCUC();});
<?php 
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 24 || $_SESSION['rol'] == 16 || $_SESSION['rol'] == 10) 
	echo "$(\"#importe\").prop('readonly', false);";
 elseif ($usaTasa)
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
			if (!$.isNumeric($("#moncuc").val())) alert('Debe entrar un número en el campo de importe.');
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
	});

<?php
if ($alerta) echo "alert('$alerta');";
echo "</script>";

$chevip = " <label for='chevip'><input id='chevip' type='checkbox'> Nuevo VIP</label>";

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_PAGODIRECTO;
$html->anchoTabla = 600;
$html->anchoCeldaI = 255;
$html->anchoCeldaD = 325;
$html->tituloTarea = _COMERCIO_PAGO;
$html->java = $javascript;

$html->inHide($comer, "comer");
$html->inHide("true", "inserta");
$html->inHide("5000", "valCom");
$html->inHide("", "viptrans");
$html->inHide("", "vipreferencia");
$q = "select id_reserva id, nombre from tbl_reserva r, tbl_transacciones t 
		where t.idtransaccion = r.id_transaccion and vip = 1 and fecha_exp >= ".date('ym')." and 
			id_comercio in (select idcomercio from tbl_comercio where id in (".$_SESSION['idcomStr'].")) 
		order by nombre";
$temp->query($q);
$arrSal = $temp->loadRowList();
if (count($arrSal[0]) > 0) {
	$arrVip[0] = array("","");
	foreach ($arrSal as $item) {
		$arrVip[] = array($item[0],$item[1]);
	}
	$html->inSelect("Cliente VIP", "nombrevip", 3, $arrVip);
}
$html->inTextb(_FORM_NOMBRE, "", "nombre", null, $chevip);
$html->inTextb(_FORM_CORREO, "", "email");
$html->maxLenght = 19;
$html->inTextb(_COMPRUEBA_TRANSACCION, "", "trans", null, " " . _COMERCIO_GENERA);
$html->inTexarea(_COMERCIO_SER, $servicio, 'servicio', 7);
$valInicio = array('en', 'es');
$etiq = array(_PERSONAL_ING, _PERSONAL_ESP);
$validi = 'en';
$html->inRadio(_PERSONAL_IDIOMA, $valInicio, 'idioma', $etiq, $validi);
$valInicio = array('0', '1');
$etiq = array(_COMERCIO_VISA, _COMERCIO_AMEX);
$validi = '0';
$html->inRadio(_COMERCIO_TARJETA, $valInicio, 'amex', $etiq, $validi);


if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 24 || $_SESSION['rol'] == 16 || $_SESSION['rol'] == 10)
	$valInicio = "select idPasarela id, nombre from tbl_pasarela where tipo = 'P' and activo = 1 order by nombre desc";
else {
	$query = "select pasarelaAlMom from tbl_comercio where id in (" . $comer . ")";
	$temp->query($query);
    $pasar = implode(',', $temp->loadResultArray());
	$valInicio = "select p.idPasarela id, case secure when 1 then 'Segura' else 'NO Segura' end nombre from tbl_pasarela p where p.idPasarela in (
					$pasar) group by secure order by idPasarela";
// 	echo $valInicio;
}
// if ($_SESSION['id'] == 10) echo $valInicio;
$temp->query($valInicio);
$arrPAs = $temp->loadAssocList();

$html->inSelect(_COMERCIO_PASARELA, "pasarela", 2, $valInicio);

//Adiciona Cuc para los comercios que lo lleven
if ($usaTasa) {
	$html->inTextb('Monto CUC', $valor, 'moncuc', null, ' Monto en CUC a convertir');
}
$html->inSelect(_COMPRUEBA_MONEDA, 'moneda', 3, null, null);
$html->inTextb(_COMPRUEBA_IMPORTE, $precio, "importe");

$valInicio = array('S', 'N');
$etiq = array(_COMERCIO_ALMOMENT, _COMERCIO_DIFERI);
$valor = 'S';
$html->inRadio(_COMERCIO_PAGOA, $valInicio, 'pago', $etiq, $valor);
$html->maxLenght = 2;
$html->inTextb(_COMERCIO_INVACTIVA, "3", "tiempo", null, _COMERCIO_INVACTIVAEXPL);

//if ($comer == 'todos') 
//	$query = "select idmoneda id, moneda nombre from tbl_moneda order by moneda";
//else $query = "select idmoneda id, moneda nombre from tbl_moneda where idmoneda in ('840','978','826','392','124') order by moneda";

if (strpos($_SESSION['idcomStr'], ',')) {
	$valInicio = "select id, nombre from tbl_comercio where id in ({$_SESSION['idcomStr']}) and activo = 'S' order by nombre";
	$html->inSelect(_COMERCIO_TITULO, "comercio", 2, $valInicio);
} else
	$html->inHide($comer, "comercio");

echo $html->salida();


?>

<script type="text/javascript" src="../js/jquery.myfun_c00cc3a629e84ef270c28b0d705a1ce1.js"></script>
<script type="text/javascript" >

function verifica() {
	if ($("#email").val().indexOf('[') != -1 ) {
		alert ("El correo escrito no es v\xe1lido"); 
		return false;
	}

	if (
			(checkField (document.forms[0].nombre, isAlphanumeric, ''))&&
			(checkField (document.forms[0].email, isEmail, ''))&&
			(checkField (document.forms[0].tiempo, isInteger, ''))&&
			(checkField (document.forms[0].importe, isMoney, ''))&&
			(checkField (document.forms[0].servicio, isAlphanumeric, '')) &&
// 			(CompareField(document.forms[0].valCom,document.forms[0].importe,'mayor','El valor de la transacción debe ser menor de 5 000.00'))&&
// 			(checkField (document.forms[0].moncuc, isMoney, 'true'))&&
			(checkField (document.forms[0].trans, isUrl, 'true'))
		) {
		if (document.forms[0].trans.lenght > 1) {
			if (checkField (document.forms[0].trans, isAlphanumeric, '')) return true;
			else return false;
		} else {
			if ($("#importe").val() > 5000) $("#nombrevip").attr("checked","checked");
			//return true;
		}
	}
	return false;
}

$(document).ready(function(){
	busca();
// 	populateVip();
	$("#pasarela").change(function(){busca()});
	$("#nombrevip").change(function(){populateVip()});

	function populateVip() {
		$(".title_tarea1").esperaDiv('muestra');
		$.post('componente/comercio/ejec.php',{
				fun:'populavip',
				vip:$("#nombrevip option:selected").val()
			},function(data){
				var datos = eval('(' + data + ')');
				$(".title_tarea1").esperaDiv('cierra');
				if (datos.tex.length > 0) alert(datos.tex);
				if (datos.cont.length > 0) {
					$.each(datos.cont, function(index,vale) {
						$("#nombre").val(vale.nombre)
						$("#email").val(vale.email)
						$("#servicio").val(vale.servicio)
					});
				}
		});
	}
	
	function busca() {
		$(".title_tarea1").esperaDiv('muestra');
		$("#moneda").get(0).options.length = 0;
		$.post('componente/comercio/ejec.php',{
				fun:'instpago',
				pas:$("#pasarela").val()
			},function(data){
				var datos = eval('(' + data + ')');
				$(".title_tarea1").esperaDiv('cierra');
				$("#enviaForm").show();
				if (datos.error.length > 0) alert(datos.error);
				if (datos.sale) {
					if (datos.sale.length > 0) {
						var options = $("#moneda");
						$.each(datos.sale, function(index,vale) {
							options.append($("<option />").val(vale.idmoneda).text(vale.moneda));
						});
					}
				}
		});
	}
})
</script>