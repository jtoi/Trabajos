<?php
defined ( '_VALID_ENTRADA' ) or die ( 'Restricted access' );
$d = $_POST;
global $temp;
$corCreo = new correo ();

$isbanner = false;
$banner = "var2/banners/" . $idcomercio . ".jpg";
$logo = "admin/logos/" . $idcomercio . ".jpg";
if (file_exists ( $banner )) {
	$isbanner = true;
}

$q = "select pasarelaAlMom, usarTasaCuc, tranfTpv from tbl_comercio where idcomercio in ('" . $idcomercio . "')";
// echo $q;
$temp->query ( $q );
$arrP = $temp->loadAssocList ();
$pa = $arrP [0] ['pasarelaAlMom'];
$usaTas = $arrP [0] ['usarTasaCuc'];
$tranfTpv = $arrP [0] ['tranfTpv'];

$query = "select format(valor,4) valor, nombre from tbl_setup where nombre in (select moneda from tbl_moneda) order by nombre";
$temp->query ( $query );
$arrVal = array ();
$con = 0;

while ( $temp->next_record () ) {
	$arrVal [$con ++] = array (
			$temp->f ( 'valor' ),
			$temp->f ( 'nombre' ) 
	);
}

// carga la tasa de cambio del CUC si lleva la tasa del Mitur
// trabajar en la tasa del BNC
if ($usaTas == 2) {
	$arrCamb = array ();
	$q = "select moneda, idmoneda from tbl_moneda order by moneda";
	$temp->query ( $q );
	$arrMon = $temp->loadRowList ();
	$strDal = "{";
	foreach ( $arrMon as $item ) {
		$q = "select cambio from tbl_cambioCUC u, tbl_comercio c where u.comercio = c.id and c.idcomercio = $idcomercio and u.moneda = '{$item[1]}' order by u.fecha desc limit 0,1";
		// echo $q."<br>";
		$temp->query ( $q );
		$camb = $temp->f ( 'cambio' );
		// $camb = leeSetup("minturCUC-".$item[0]);
		$arrCamb [] = array (
				$item [0],
				$camb 
		);
	}
	$strDal = json_encode ( $arrCamb );
	// echo $strDal;
	
	$q = "select m.moneda, cambio from tbl_cambioCUC u, tbl_moneda m, tbl_comercio c where m.idmoneda = u.moneda and u.comercio = c.id and c.idcomercio = $idcomercio order by m.moneda, u.fecha";
	// echo $q;
}
if ($_SESSION ['rol'] == 1 || $_SESSION ['rol'] == 24 || $_SESSION ['rol'] == 16 || $_SESSION ['rol'] == 10)
	$q = "select idPasarela id, nombre from tbl_pasarela where tipo = 'P' and activo = 1 order by nombre";
else {
	$query = "select pasarelaAlMom from tbl_comercio where id in (" . $_SESSION ['idcomStr'] . ")";
	$temp->query ( $query );
	$pasar = implode ( ',', $temp->loadResultArray () );
	$q = "select p.idPasarela id, case secure when 1 then 'Segura' else 'NO Segura' end nombre from tbl_pasarela p where p.idPasarela in (
					$pasar) group by secure order by idPasarela";
}

// if ($_SESSION['grupo_rol'] < 3) $q = "select idPasarela, nombre from tbl_pasarela where idPasarela in ($pa)";
// else $q = "select idPasarela, case secure when 1 then 'Segura' else 'NO Segura' end nombre from tbl_pasarela where idPasarela in ($pa)";
$temp->query ( $q );
$arrPa = $temp->loadRowList ();
// print_r($arrPa);
if (strlen ( $idcomercio ))
	$query = "select * from tbl_comercio where idcomercio in ({$idcomercio}) limit 0,1";
$temp->query ( $query );
$comercioN = $temp->f ( 'nombre' );
$estCom = $temp->f ( 'estado' );
$datos = $temp->f ( 'datos' );
$palabra = $temp->f ( 'palabra' );
$prefijo = $temp->f ( 'prefijo_trans' );
$idCom = $temp->f ( 'id' );
$valMin = $temp->f ( 'minTransf' );
$correoMas = $temp->f ( 'correoMas' );
$d ['comercio'] = $temp->f ( 'idcomercio' );
if (strlen ( $d ['email'] ))
	$correo = str_replace ( " ", "", $d ['email'] );
$fechaNow = time ();

if ($d ['inserta']) {

	$arrEnv = array(
			'comercio' => $d['comercio'],
			'nombre' => $d['nombre'],
			'email' => $d['email'],
			'importe' => $d['importe'],
			'moneda' => $d['moneda'],
			'amex' => $d['amex'],
			'tiempo' => $d['tiempo'],
			'idioma' => $d['idioma'],
			'trans' => $d['trans'],
			'pasarela' => $d['pasarela'],
			'servicio' => $d['servicio'],
			'pago' => $d['pago'],
			'dir' => substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')+1)
	);
	
	echo envPago($arrEnv);
		
}

/* Transferencias */
if ($d ['pone'] && $tranfTpv) {
	$arrEnv = array(	
			'comercio' => $idcomercio,
			'nombre' => $d['nombreT'],
			'email' => $d['emailT'],
			'importe' => $d['importeT'],
			'monedas' => $d['moneda'],
			'idioma' => $d['idioma'],
			'dir' => substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')+1)
	);
	foreach ($arrEnv as $key => $value) {
		$corrreoMi .= "$key => $value\n";
	}
// 	var_dump($arrEnv);
	echo envTransf($arrEnv);

}

$q = "select idmoneda, moneda from tbl_moneda order by moneda ";
$temp->query ( $q );
$arrMonC = $temp->loadRowList ();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>TPV Virtual</title>
<link href="<?php echo _ESTA_URL; ?>/admin/template/css/tpv.css"
	rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript"
	src="<?php echo _ESTA_URL; ?>/js/jquery.js"></script>
<script type="text/javascript"
	src="<?php echo _ESTA_URL; ?>/js/globalfunc-spanish.mod.min_201208101748.js"></script>
<script type="text/javascript"
	src="<?php echo _ESTA_URL; ?>/js/jquery_002_003_menu.min_201208111328.js"></script>
<!--<script type="text/javascript" src="<?php echo _ESTA_URL; ?>/js/iepngfix_tilebg_8ae20f759c2ada143353161efe600bbf.js"></script>-->
<script type="text/JavaScript">
	var mimoneda = '';
$(document).ready(function(){
            $("#trnf").hide();
			$("#pag").hide();
<?php if ($isbanner) { ?>
			$('#bannerCom').css('background', 'url(<?php echo $banner; ?>) no-repeat');
<?php } ?>
		$("#pagoS").click(function(){
			if ($(this).attr('checked')){
				$("#pag").hide();
			}
		});
		$("#pagoN").click(function(){
			if ($(this).attr('checked')){
				$("#pag").show();
			}
		});
<?php if ($usaTas != 0){ ?>
		$("#pote").blur(function(){calcCUC();});
		$("#moneda").change(function(){calcCUC();});
<?php } ?>
        $("#transf").click(function(){
            $("#trnf").show();
            $("#trns").hide();
        });
        $("#transa").click(function(){
            $("#trns").show();
            $("#trnf").hide();
        });
		
		$("#monLi").click(function(e){
			$("#divTasa").toggle();
			e.stopPropagation();
		});
		$("body").click(function(){$("#divTasa").hide();});

		$("input[name=pago]").click(function(){cabiaPas()});
		cabiaPas();
	});

	function cabiaPas() {
		$.post('admin/componente/comercio/ejec.php',{
			fun: 'cambps',
			pas: $("#batchPas").val(),
			pag: $("input[name=pago]:checked").val()
		},function(data){
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

					mimoneda = $("#moneda option:selected").val();
				}
			}
		});
	}
	
	function calcCUC(){
		var dato = jQuery.parseJSON('<?php echo $strDal; ?>');
		var mon = $("#moneda option:selected").text();
		if (!$.isNumeric($("#pote").val())) alert('Debe entrar un número en el campo de importe.');
		else {
			for (var i=0;i<dato.length;i++){
				var mnd = dato[i];
				if (mnd.indexOf(mon) == 0) {
					$("#importe").val($("#pote").val() * mnd[1]);
				}
			}
			if ($("#importe").val() == 0 && $("#importe").val() > 0) alert("No tiene habilitado el cambio para esa moneda, cambie la moneda para poder enviar el pago.");
		}
	}
	
	function verifica() {
		if ($("#pagoS").attr('checked')) {
			if (
				(checkField (document.admin_forma.nombre, isAlphanumeric, ''))&&
				(checkField (document.admin_forma.importe, isMoney, ''))
			) {
					if ($("#importe").val() == 0) {alert("El Importe deberá ser mayor que 0."); return false;}
					else document.admin_forma.submit();
			}
			return false;
		} else {
			if (
				(checkField (document.admin_forma.nombre, isAlphanumeric, ''))&&
				(checkField (document.admin_forma.email, isEmail, ''))&&
				(checkField (document.admin_forma.importe, isMoney, ''))
			) {
					if ($("#importe").val() == 0) {alert("El importe deberá ser mayor que 0."); return false;}
					else document.admin_forma.submit();
			}
			return false;
		}
	}
	
	function verificaT() {
		
			if (
				(checkField (document.admin_fTrn.nombreT, isAlphanumeric, ''))&&
				(checkField (document.admin_fTrn.emailT, isEmail, ''))&&
				(checkField (document.admin_fTrn.importeT, isMoney, ''))
			) {
					if ($("#importeT").val() == 0) {alert("El importe deberá ser mayor que 0."); return false;}
					if ($("#importeT").val() < <?php echo $valMin; ?>) {alert("El importe deberá ser mayor que <?php echo $valMin; ?>."); return false;}
					else document.admin_fTrn.submit();
			}
			return false;
		
	}
	</script>

<body>
	<div id="todo">
		<div id="banTop">
			<div id="ola">
				<div id="tpv">
					<span>TPV Virtual</span> Administración
				</div>
				<div id="tpv1">
					1 Euro a: <span id="monLi"><?php echo _VERTASA ?></span>
					<div id="divTasa">
						<?php
						foreach ( $arrVal as $valor ) {
							echo $valor [1] . " - " . $valor [0] . "<br>";
						}
						?>
                        </div>
				</div>
				<div id="datPer">
					Usuario: <span><?php echo $admin; ?></span> <img
						style="cursor: pointer;"
						src="<?php echo _ESTA_URL; ?>/var2/images/salida.png" width="15"
						height="17" onclick="window.open('TPV.php?pag=logout', '_self')" />
				</div>
				<div id="alerta"></div>
			</div>
		</div>
		<div style="float: left; width: 100%">
			<div class="ancho">
				<?php
				if ($isbanner) {
					?>
					<div id="bannerCom">
					<span class="pago">PAGO en LÍNEA</span><span class="ncom"><?php echo $comercio; ?></span>
						<?php
					if ($idcomercio == '129025985109')
						echo '<div id="banCubana">Ventas por Internet</div >';
					?>
					</div>
					<?php
				} else {
					?>
					<div id="baner">
					<div id="bannerL" class="banner">
						<img src="admin/logos/<?php echo $idcomercio ?>.jpg" width="75"
							height="75" />
					</div>
					<div class="banner" id="bannerR">
						<img src="admin/template/images/logoCaribbean.jpg" width="122"
							height="40" />
					</div>
				</div>
				<?php } ?>
                    <div id="trns" class="cuerpo">
					<form action="" method="post" enctype="multipart/form-data"
						name="admin_forma">
						<input type="hidden" id="batchPas" value="<?php echo $pasar; ?>">
						<div id="tabForm" class="tabForm">
							<div id="tabFlo">
								<input type="hidden" value="true" name="inserta" id="inserta" />
								<input type="hidden" value="S" name="pago" id="pago" />
<?php if($tranfTpv) { ?>
								<div id="div_nombre" class="lineaT1">
									<div class="derecha2">Operación:</div>
									<div class="izquierda izquierda4">

										<input type="radio" name="opera" id="transa" checked value="S" />
										<label for="transa">Transacción</label>&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="radio" name="opera" id="transf" value="N" /> <label
											for="transf">Transferencia</label>
									</div>
								</div>
<?php } ?>
								<div id="div_nombre" class="lineaT1">
									<div class="derecha2">Nombre y Apellidos:</div>
									<div class="izquierda izquierda4">
										<input maxlength="150" class="formun" type="text"
											name="nombre" id="nombre" />
									</div>
									<div class="derecha1">Correo:</div>
									<div class="izquierda izquierda3">
										<input maxlength="150" class="formun" type="email"
											name="email" id="email" />
									</div>
								</div>
							<?php if ($usaTas == 0){ //No hay cambios para el CUC se trabaja con las monedas normales ?>
								<div id="div_importe" class="lineaT">
									<div class="derecha1">Forma de Pago:</div>
									<div class="izquierda izquierda3">
										<input type="radio" name="pago" id="pagoS" checked value="S" />
										<label for="pagoS">Al momento</label>&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="radio" name="pago" id="pagoN" value="N" /> <label
											for="pagoN">Diferido</label>
									</div>
									<div class="derecha1I">Importe:</div>
									<div class="izquierda izquierda1">
										<input maxlength="10" class="formun" type="number"
											name="importe" id="importe" />
									</div>
									<div class="derecha1I">Moneda:</div>
									<div class="izquierda1 izquierda">
										<select class="formun" name="moneda" id="moneda">
											<?php
								
foreach ( $arrMonC as $mone ) {
									if ($mone [1] == 'EUR')
										echo "<option selected value='{$mone[0]}'>{$mone[1]}</option>";
									else
										echo "<option value='{$mone[0]}'>{$mone[1]}</option>";
								}
								?>
										</select>
									</div>
								</div>
								<div id="div_importe" class="lineaT">
									<div class="derecha1">Tarjeta a usar:</div>
									<div class="izquierda izquierda3 izz">
										<input type="radio" name="amex" id="amexS" checked value="0" />
										<label for="amexS">Visa o Mastercard</label>&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="radio" name="amex" id="amexN" value="1" /> <label
											for="amexN">American Express</label>
									</div>
								</div>
								<div id="pag">
									<div id="div_importe" class="lineaT">
										<div class="derecha2I">Invitación activa por:</div>
										<div class="izquierda izquierda1">
											<select class="formun" name="tiempo" id="tiempo">
												<option selected value="3">3 días</option>
												<option value="5">5 días</option>
												<option value="10">10 días</option>
												<option value="15">15 días</option>
												<option value="30">30 días</option>
											</select>
										</div>
										<div class="derecha1I">Idioma:</div>
										<div class="izquierda izquierda1">
											<select class="formun" name="idioma" id="idioma">
												<option selected value="es">Español</option>
												<option value="en">Inglés</option>
											</select>
										</div>
									</div>
								</div>
								<div id="div_importe" class="lineaT">
									<div class="derecha2">No. Operación:</div>
									<div class="izquierda izquierda2">
										<input maxlength="150" class="formun" type="text" name="trans"
											id="trans" />
									</div>
									<div class="derecha2I">Pasarela:</div>
									<div class="izquierda izquierda2">
										<select class="formun" name="pasarela" id="pasarela">
											<?php
								for($i = 0; $i < count ( $arrPa ); $i ++) {
									echo "<option value='" . $arrPa [$i] [0] . "'>" . $arrPa [$i] [1] . "</option>";
								}
								?>
										</select>
									</div>
								</div>
								<div id="div_importe" class="lineaT">
									<div class="derecha2">Servicio:</div>
									<div class="izquierda izquierda4">
										<textarea class="formun" type="text" name="servicio"
											id="servicio" /></textarea>
									</div>
								</div>
							<?php } else { //cambio del CUC ?>
								<div id="div_importe" class="lineaT">
									<div class="derecha1">Precio CUC:</div>
									<div class="izquierda izquierda1">
										<input maxlength="10" class="formun" type="number" name="pote"
											id="pote" />
									</div>
									<div class="derecha1I">Moneda:</div>
									<div class="izquierda1 izquierda">
										<select class="formun" name="moneda" id="moneda">
											<?php
								
foreach ( $arrMonC as $mone ) {
									if ($mone [1] == 'EUR')
										echo "<option selected value='{$mone[0]}'>{$mone[1]}</option>";
									else
										echo "<option value='{$mone[0]}'>{$mone[1]}</option>";
								}
								?>
										</select>
									</div>
									<div class="derecha1I">Importe:</div>
									<div class="izquierda1 izquierda">
										<input maxlength="10" class="formun" type="number"
											name="importe" id="importe" />
									</div>
								</div>
								<div id="div_importe" class="lineaT">
									<div class="derecha2I">Forma de Pago:</div>
									<div class="izquierda izquierda3">
										<input type="radio" name="pago" id="pagoS" checked value="S" />
										<label for="pagoS">Al momento</label>&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="radio" name="pago" id="pagoN" value="N" /> <label
											for="pagoN">Diferido</label>
									</div>
									<div class="derecha2">No. Operación:</div>
									<div class="izquierda izquierda2">
										<input maxlength="150" class="formun" type="text" name="trans"
											id="trans" />
									</div>
								</div>
								<div id="pag">
									<div id="div_importe" class="lineaT">
										<div class="derecha2I">Invitación activa por:</div>
										<div class="izquierda izquierda1">
											<select class="formun" name="tiempo" id="tiempo">
												<option selected value="3">3 días</option>
												<option value="5">5 días</option>
												<option value="10">10 días</option>
												<option value="15">15 días</option>
												<option value="30">30 días</option>
											</select>
										</div>
										<div class="derecha1I">Idioma:</div>
										<div class="izquierda izquierda1">
											<select class="formun" name="idioma" id="idioma">
												<option selected value="es">Español</option>
												<option value="en">Inglés</option>
											</select>
										</div>
									</div>
								</div>
								<div id="div_importe" class="lineaT">
									<div class="derecha2">Servicio:</div>
									<div class="izquierda izquierda4">
										<textarea class="formun" type="text" name="servicio"
											id="servicio" /></textarea>
									</div>
									<div class="derecha2I">Pasarela:</div>
									<div class="izquierda izquierda2">
										<select class="formun" name="pasarela" id="pasarela">
											<?php
								for($i = 0; $i < count ( $arrPa ); $i ++) {
									echo "<option value='" . $arrPa [$i] [0] . "'>" . $arrPa [$i] [1] . "</option>";
								}
								?>
										</select>
									</div>
								</div>
							<?php } ?>
								<div class="botForm">
									<input class="formulb envia" id="enviaForm" name="enviar"
										type="button" onclick="verifica()" value="Enviar" /> <input
										name="reset" type="reset" id="reset" class="formulb cancel"
										value="Cancelar" />
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="tabForm" id="trnf">
					<form action="" method="post" enctype="multipart/form-data"
						name="admin_fTrn">
						<div id="tabForm" class="tabForm">
							<div id="tabFlo">
								<input type="hidden" value="true" name="pone" id="pone" /> <input
									type="hidden" value="S" name="pago" id="pago" />
								<div id="div_nombre" class="lineaT1">
									<div class="derecha2">Operación:</div>
									<div class="izquierda izquierda4">

										<input type="radio" name="opera" id="transa" /> <label
											for="transa">Transacción</label>&nbsp;&nbsp;&nbsp;&nbsp; <input
											type="radio" name="opera" id="transf" checked value="S"
											value="N" /> <label for="transf">Transferencia</label>
									</div>
								</div>
								<div id="div_nombre" class="lineaT">
									<div class="derecha2">Nombre y Apellidos:</div>
									<div class="izquierda izquierda4">
										<input maxlength="150" class="formun" type="text"
											name="nombreT" id="nombreT" />
									</div>
									<div class="derecha1">Correo:</div>
									<div class="izquierda izquierda3">
										<input maxlength="150" class="formun" type="email"
											name="emailT" id="emailT" />
									</div>
								</div>
								<div id="div_importe" class="lineaT">
									<div class="derecha1I">Importe:</div>
									<div class="izquierda izquierda1">
										<input maxlength="10" class="formun" type="number"
											name="importeT" id="importeT" />
									</div>
									<div class="derecha1I">Moneda:</div>
									<div class="izquierda1 izquierda">
										<select class="formun" name="moneda" id="moneda">
											<option value="826">GBP</option>
											<option value="124">CAD</option>
											<option value="840">USD</option>
											<option selected value="978">EUR</option>
										</select>
									</div>
									<div class="derecha1I">Idioma:</div>
									<div class="izquierda izquierda1">
										<select class="formun" name="idioma" id="idioma">
											<option selected value="es">Español</option>
											<option value="en">Inglés</option>
										</select>
									</div>
								</div>
								<div id="div_importe" class="lineaT"></div>
								<div id="div_importe" class="lineaT">
									<div class="derecha2">Servicio:</div>
									<div class="izquierda izquierda4">
										<textarea class="formun" type="text" name="servicio"
											id="servicio" /></textarea>
									</div>
								</div>
								<div class="botForm">
									<input class="formulb envia" id="enviaForm" name="enviar"
										type="button" onclick="verificaT()" value="Enviar" /> <input
										name="reset" type="reset" id="reset" class="formulb cancel"
										value="Cancelar" />
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div id="pie">
			<div>
				<span>Travels and Discovery</span> &copy; Todos los derechos
				reservados
			</div>
		</div>
	</div>
</body>
</html>
<?php
// Debugger





if (_MOS_CONFIG_DEBUG) {
	
	echo '<div id="debD"><pre style="font-size:11px">';
	echo "<hr /><hr /><br>Logs:<br>";
	echo $correoMi;
	echo "<hr /><hr /><br>Querys:<br>";
	echo $temp->log;
	echo "<hr /><hr /><br>Datos:<br>";
	echo "<hr /><hr /><br>Variables usadas:<br>";
	print_r ( array_keys ( get_defined_vars () ) );
	echo $textoCorreo;
	echo "</div>";
}
?>