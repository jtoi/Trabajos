
<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
// define(_VALID_ENTRADA, 1);
// include 'https://www.administracomercios.com/configuration.php';
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html lang="en">
	<head>
		<title>Administraci&oacute;n</title>
		<meta charset="ISO-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link href="<?php echo _ESTA_URL; ?>/admin/template/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo _ESTA_URL; ?>/admin/template/css/login1.css" rel="stylesheet" type="text/css" />
		<script	src="<?php echo _ESTA_URL; ?>/admin/js/jquery.js" ></script>
		<script	src="<?php echo _ESTA_URL; ?>/admin/js/popper.min.js"></script>
		<script	src="<?php echo _ESTA_URL; ?>/admin/js/bootstrap.min.js" ></script>
		<script src='https://www.google.com/recaptcha/api.js?render=6Le65ngUAAAAAHGtnvEYRAjDV7HnbG8SydxbaIX4'></script>
		<!--<script type="text/javascript" src="../js/iepngfix_tilebg_8ae20f759c2ada143353161efe600bbf.js"></script>
		<script async="true" type="text/javascript" src="<?php echo _ESTA_URL; ?>/jsd/jquery.myfun.mod_201208110635.js"></script>-->
		<script async="true" type="text/JavaScript">
<?php if (!strpos(_ESTA_URL, 'localhost')) { ?>
//		grecaptcha.ready(function() {
//			grecaptcha.execute('6Le65ngUAAAAAHGtnvEYRAjDV7HnbG8SydxbaIX4', {action: 'login'})
//			.then(function(token) {
//			// Verify the token on the server.
//				document.getElementById('g-recaptcha-response').value=token;
//			});
//		});
<?php } ?>
		function reserva() {
			if (
					(document.form1.login.value.length > 0)&&
					(document.form1.password.value.length > 0)
				) {
				document.form1.submit();
			}
			return false;
		}
		$(document).ready(function(){
			var visitortime = new Date();
			var visitortimezone = -visitortime.getTimezoneOffset()/60;
			var arr = [];
			for (var i = 0; i < 365; i++) {
				var d = new Date();
				d.setDate(i);
				newoffset = -d.getTimezoneOffset()/60;
				arr.push(newoffset);
			}
			DST = Math.min.apply(null, arr);
			nonDST = Math.max.apply(null, arr);
			document.cookie = "TZ="+visitortimezone+'|'+DST+'|'+nonDST;

			$("#fg").mouseover(function(){$("#fg").css({'color':'blue', 'text-decoration':'blue blink underline'})});
			$("#fg").mouseleave(function(){$("#fg").css({'color':'#af0000', 'text-decoration':'none'})});

			$("#noscript").hide();
			$("#botonLog").click(function(){
				reserva();
			});
			$("#pase").focus(function(){
				$("#password").show().focus();
				$("#pase").hide();
			});
			$("#password").blur(function(){
				if ($("#password").val().length == 0) { 
					$("#password").hide();
					$("#pase").css('display','block');
				}
			}).hide();
			$("#login").focus(function(){
				if ($("#login").val()=='Usuario') {$("#login").val('');}
			}).blur(function(){
				if ($("#login").val()=='') {$("#login").val('Usuario');}
			});

			$("#fg").click(function(){
				alert ("Ingrese su nombre de usuario y correo");
				$("#botlg").show();
				$("#botonLog").hide();
				$("#pase").hide();
				$("#email").show();
				$("#password").hide();
// 				$("#cr").text('Correo:');
				$("#botlg").click(function(){
					if($("#login").val().length > 0 && $("#email").val().length > 0) {
						$("#botlg").hide();
// 						$("#med").esperaDiv('muestra');
						$.post('eje.php',{
							fun:'log',
							usr:$("#login").val(),
							cor:$("#email").val()
						},function(data){
							var datos = eval('(' + data + ')');
							alert(datos.alerta);
							if (datos.sale == 'ko') window.open('https://www.google.com','_self');
							else if (datos.sale == 'na') {
								$("#usuariover").val($("#login").val());
								$("#oemail").val($("#email").val())
								document.actusr.submit();
							}
							else {
	// 							$("#med").esperaDiv('cierra');
								$("#botlg").hide();
								$("#botonLog").show();
								$("#email").hide();
								$("#password").show();
								$("#cr").text('Contraseña:');
								window.open('<?php echo _ESTA_URL; ?>/admin','_self');
							}
						});
					} else {
						alert('Revise los datos puestos');
					}
				});
			});
		});
		</script>
	</head>
	<body onload="">
		<div class="container-fluid">
			<div class="row">
				<div id="noscript" style="font-size: 14px;text-align: center;color: white;">Si al terminar de cargar la página no desaparece este cartel<br>Su navegador no tiene habilitado Javascript le sugiero que no siga adelante hasta que lo habilite.</div>
				<div id="paso1"></div>
			</div>
			<div id="paso2">
				<div class="row">
					<div class="col">
						<div id="tasto" class="col-10 col-sm-4 col-md-8">
							<span class="fra1"><span class="fra2">TPV VIRTUAL</span> Panel de Administraci&oacute;n</span><br>
							UNA FORMA SENCILLA Y SEGURA DE PAGAR POR INTERNET
						</div>
					</div>
				</div>
				<div class="row">
					<div id="med2">
						<form id="form1" name="form1" method="post" action="">
							<input name="dosmd2" type="hidden" id="dosmd2" />
							<input name="unomd2" type="hidden" id="unomd2" />
							<div class="log">
								<div class="der">
									<input type="hidden" name="token" value="<?php echo token(); ?>">
									<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
									<input maxlength="40" name="login" type="text" class="log_form" id="login" value="<?php echo _AUTENT_LOGIN ?>" />
									<input class="log_form" type="text" id="pase" value="<?php echo _AUTENT_PASS ?>" />
									<input maxlength="40" class="log_form" name="password" type="password" autocomplete="off" id="password" />
									<input maxlength="40" id="email" type="text" class="log_form" value="<?php echo _FORM_CORREO ?>" style="display: none"/>
								<div id="botonLog">ENVIAR</div>
								<div id="botlg" style="display: none;">ENVIAR</div><span id="fg">Olvidé mi clave o mi usuario está bloqueado</span>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row bl">
					<div class="aju">
						<img class="d-block-inline" src="<?php echo _ESTA_URL; ?>/admin/template/images/master1.jpg">
						<img class="d-block-inline" src="<?php echo _ESTA_URL; ?>/admin/template/images/visa1.jpg">
						<img class="d-block-inline" src="<?php echo _ESTA_URL; ?>/admin/template/images/amex1.jpg">
						<img class="d-block-inline" src="<?php echo _ESTA_URL; ?>/admin/template/images/vervis1.jpg">
						<img class="d-block-inline" src="<?php echo _ESTA_URL; ?>/admin/template/images/mcsec1.jpg">
						<img class="d-block-inline" src="<?php echo _ESTA_URL; ?>/admin/template/images/3dsec1.jpg">
					</div>
				</div>
			</div>
			
			
			</div>
			
			<div id="todo">
				
				<div id="paso4">
					<div class="infClp">
						<?php if(!strpos(_ESTA_URL, 'localhost')){ ?>
						<!-- GeoTrust QuickSSL [tm] Smart  Icon tag. Do not edit. -->
						<table width="135" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose GeoTrust SSL for secure e-commerce and confidential communications.">
	<tr>
	<td width="135" align="center" valign="middle"><script type="text/javascript"> //<![CDATA[
  var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
  document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
//]]></script>
<script language="JavaScript" type="text/javascript">
  TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_lg_222x54.png", "POSDV", "none");
</script></td>
	</tr>
	</table>
						<!-- end  GeoTrust Smart Icon tag -->
						<?php } ?>
					</div>
				</div>
				<div id="paso5">
					<div id="mainInf">Administrador de comercios <span>Todos los derechos reservados &reg;</span> <?php echo date('Y', time()); ?></div>
				</div>
			</div>
		</div>
		<form name="actusr" method="post" action="verifUser.php"><input type="hidden" name="usuariover" id="usuariover" value=""><input type="hidden" name="email" id="oemail" value=""><input type="hidden" name="ip" value="<?php echo $ip; ?>"></form>
	</body>
</html>
