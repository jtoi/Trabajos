<?php


if (!isset($_REQUEST['lan'])) $lan = 'es';
else $lan = $_REQUEST['lan'];
include $lan.'.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
    <meta name="author" content="Julio Toirac (jtoirac@gmail.com)" />
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
	<title>Inscripción de Atletas</title>
	<link rel="stylesheet" href="css/villa.css" type="text/css" />
	<link rel="stylesheet" href="css/datepicker.css" type="text/css" />
	<link rel="stylesheet" href="css/<?php echo $lan; ?>.css" type="text/css" />

	<script type="text/javascript" src="site/js/jquery.js"></script>
	<script type="text/javascript">
		
		function pasa() {
			if ($("#atleta1").attr("checked")) {
				$("#nombreA").val($("#nombre").val());$("#apellidoA").val($("#apellido").val());
			}
			if ($("#atleta2").attr("checked")) {
				$("#nombreA").val("");$("#apellidoA").val("");
			}
		}
		
		function dev(arr, pas) {
			var pase = "";
			pase = arr.split("|");
			if (pas) return pase[0];
			else return pase[1];
		}
		
		function prueba() {
			var arrVal = new Array();
			var i = 0;
			$(".cambiCheq").each(function(){
				if ($(this).attr("checked")) arrVal[i++] = $(this).val();
			});
			$(".cambiCheq2").each(function(){
				if ($(this).attr("checked")) arrVal[i++] = $(this).val();
			});
			for(i=0;i<2;i++){
				if (arrVal[i]) {
					$("#prue"+i).html(dev(arrVal[i],true));
					$("#pr"+i).val(dev(arrVal[i],true));
				} else {
					$("#prue"+i).html("<?php echo $idiom[12]; ?> "+(i+1));
					$("#pr"+i).val("");
				}
			}
		}

		$(document).ready(function(){
			var medio = ($(window).width()/2);
			$("#imgFem").css("left",(medio + 320));
			$("#imgMasc").css("left",(medio - 380));
			$('input').css('text-transform','uppercase');
			$("#sexo1").attr("checked", "checked");
			$(".cambiCheq2").attr("disabled","true").attr("checked",false);
			$(".cambiCheq").attr("disabled",false).attr("checked",false);
			$("#conta").val("0");
			
			$("#nombre").blur(function(){pasa();});
			$("#apellido").blur(function(){pasa();});
			$("#atleta1").click(function(){pasa();});
			$("#atleta2").click(function(){pasa();});
			$(".mrca").blur(function(){$(this).val($(this).val().replace(".",","))});
			
			$(".cambiCheq").click(function(){
				if ($(this).attr("checked")) $("#conta").val(($("#conta").val()*1) + 1);
				else $("#conta").val($("#conta").val() - 1);
				prueba();
				if ( $("#conta").val() >= 2) {
					$(".cambiCheq").attr("disabled","true");
					$(".cambiCheq:checked").attr("disabled",false);
				}
				else {
					$(".cambiCheq").attr("disabled",false);
				}
				if ($(this).attr("class") == 'cambiCheq uniq' && $(this).attr("checked")) {
					$(".cambiCheq:checked").attr("checked",false);
					$(this).attr("checked",true);
					$(".cambiCheq").attr("disabled","true");
					$(".cambiCheq:checked").attr("disabled",false);
				}
				else {
					$(".cambiCheq").attr("disabled",false);
				}
			});
			$(".cambiCheq2").click(function(){
				if ($(this).attr("checked")) $("#conta").val(($("#conta").val()*1) + 1);
				else $("#conta").val($("#conta").val() - 1);
				prueba();
				if ( $("#conta").val() >= 2) {
					$(".cambiCheq2").attr("disabled","true");
					$(".cambiCheq2:checked").attr("disabled",false);
				}
				else {
					$(".cambiCheq2").attr("disabled",false);
				}
				if ($(this).attr("class") == 'cambiCheq2 uniq' && $(this).attr("checked")) {
					$(".cambiCheq2:checked").attr("checked",false);
					$(this).attr("checked",true);
					$(".cambiCheq2").attr("disabled","true");
					$(".cambiCheq2:checked").attr("disabled",false);
				}
				else {
					$(".cambiCheq2").attr("disabled",false);
				}
			});
			
			$(".sexo").click(function(){
				$(".cambiCheq").attr("disabled","true");
				$(".cambiCheq2").attr("disabled","true");
				$("#conta").val("0");
				$(".pr").val("");
				$("#prue0").html("<?php echo $idiom[12]; ?> "+1);
				$("#prue1").html("<?php echo $idiom[12]; ?> "+2);
				if ($(this).attr("id") == "sexo1") {
					$(".cambiCheq").attr("disabled",false);
					$(".cambiCheq2").attr("checked",false);
				} else {
					$(".cambiCheq2").attr("disabled",false);
					$(".cambiCheq").attr("checked",false);
				}
			})
			
			$("#acepto").click(function(){
				if (this.checked == true) $("#envia").show(); else $("#envia").hide();
			})
			
			$("#envia").click(function(){
				var categoria = '';
				var sexo = '';
				var salida = false;
				var dni = '';
				var mensaje = '';
				var atleta = '';
				var ano = $("#fn").val();
								
				if ($('#sexo1').attr('checked')) sexo = 'M';
				if ($('#sexo2').attr('checked')) sexo = 'F';
				if ($('#atleta1').attr('checked')) atleta = 'S';
				if ($('#atleta2').attr('checked')) atleta = 'N';
				
				if ($("#nombreA").val() != '') salida = true; else {salida = false; mensaje += "\n"+'El nombre del Atleta no puede estar vacío';}
				if ($("#apellidoA").val() != '') salida = true; else {salida = false; mensaje += "\n"+'El apellido del Atleta no puede estar vacío';}
				if ($("#fn").val() != '') salida = true; else {salida = false; mensaje += "\n"+'El año de nacimiento no puede quedar vacío';}
				if ($("#correo").val() != '') salida = true; else {salida = false; mensaje += "\n"+'El correo no puede quedar vacío';}
				if (
						$("#categoria1").attr("checked") ||
						$("#categoria2").attr("checked") ||
						$("#categoria3").attr("checked") ||
						$("#categoria4").attr("checked") ||
						$("#categoria5").attr("checked") ||
						$("#categoria6").attr("checked") ||
						$("#categoria7").attr("checked") ||
						$("#categoria8").attr("checked") ||
						$("#categoria9").attr("checked") ||
						$("#categoria10").attr("checked") ||
						$("#categoria11").attr("checked") ||
						$("#categoria12").attr("checked") ||
						$("#categoria13").attr("checked") ||
						$("#categoria14").attr("checked") ||
						$("#categoria17").attr("checked") ||
						$("#categoria18").attr("checked")
					) {
					if ($('#club').val() == '') {salida = false; mensaje += "\n"+'El nombre del club no puede quedar vacío';}
					if ($('#lic').val() == '') {salida = false; mensaje += "\n"+'El número de licencia no puede quedar vacío';}
				}
				
				$(".cambiCheq").each(function(){
					if ($(this).attr("checked")) categoria += dev($(this).val(),false)+",";
				});
				$(".cambiCheq2").each(function(){
					if ($(this).attr("checked")) categoria += dev($(this).val(),false)+",";
				});
				if (categoria.length == 0) {salida = false; mensaje += "\n"+'<?php echo $idiom[37]; ?>';}
				
				if ($("#pr0").val() != '' && $("#datos11").val() != '' && $("#datos21").val() != '' && $("#datos31").val() != '' ) {
					true;
				} else {salida = false; mensaje += "\n"+'Los registros personales no pueden quedar vacíos para '+$("#pr0").val();}
				if ($("#pr1").val() != '') {
					if ( $("#datos12").val() != '' && $("#datos22").val() != '' && $("#datos32").val() != '' ) {
						true;
					} else {salida = false; mensaje += "\n"+'Los registros personales no pueden quedar vacíos para '+$("#pr1").val();}
				}
                
                if ($("#datos11").val().length > 3 && $("#datos11").val().indexOf('.') > -1) {salida = false; mensaje += "\n"+'<?php echo $idiom[38]; ?>';}
                if ($("#datos12").val().length > 3 && $("#datos12").val().indexOf('.') > -1) {salida = false; mensaje += "\n"+'<?php echo $idiom[38]; ?>';}
                if ($("#datos21").val().length > 3 && $("#datos21").val().indexOf('.') > -1) {salida = false; mensaje += "\n"+'<?php echo $idiom[38]; ?>';}
                if ($("#datos22").val().length > 3 && $("#datos22").val().indexOf('.') > -1) {salida = false; mensaje += "\n"+'<?php echo $idiom[38]; ?>';}
                if ($("#datos31").val().length > 3 && $("#datos31").val().indexOf('.') > -1) {salida = false; mensaje += "\n"+'<?php echo $idiom[38]; ?>';}
                if ($("#datos32").val().length > 3 && $("#datos32").val().indexOf('.') > -1) {salida = false; mensaje += "\n"+'<?php echo $idiom[38]; ?>';}
				
				if (ano > 1921 && ano < 2011) salida = true; else {salida = false; mensaje += "\n"+'<?php echo $idiom[39]; ?>';}
				
				if ($("#categoria16").attr("checked") && (ano < 2003 || ano > 2006)) {salida = false; mensaje += "\n"+'<?php echo $idiom[40]; ?>';}
				if ($("#categoria20").attr("checked") && (ano < 2000 || ano > 2002)) {salida = false; mensaje += "\n"+'<?php echo $idiom[40]; ?>';}
				if ($("#categoria15").attr("checked") && (ano < 2003 || ano > 2006)) {salida = false; mensaje += "\n"+'<?php echo $idiom[40]; ?>';}
				if ($("#categoria19").attr("checked") && (ano < 2000 || ano > 2002)) {salida = false; mensaje += "\n"+'<?php echo $idiom[40]; ?>';}
                
				if (salida && mensaje == '') {
					$("#envia").hide();
					$("#result").load('resultado.php', {
						lan: '<?php echo $lan; ?>',
						atleta: atleta,
						catego: categoria,
						nombre: $("#nombre").val(),
						apellido: $("#apellido").val(),
						nombreA: $("#nombreA").val(),
						apellidoA: $("#apellidoA").val(),
						sexo: sexo,
						fn: $("#fn").val(),
						movil: $("#movil").val(),
						correo: $("#correo").val(),
						club: $("#club").val(),
						lic: $("#lic").val(),
						observ: $("#observ").val(),
						prueba1: $("#pr0").val(),
						marca11: $("#datos11").val(),
						marca21: $("#datos21").val(),
						marca31: $("#datos31").val(),
						prueba2: $("#pr1").val(),
						marca12: $("#datos12").val(),
						marca22: $("#datos22").val(),
						marca32: $("#datos32").val(),
						event:'10'
					});
					$("#result").css({
						'display': 'inline',
						'position': 'absolute',
						'width': 250,
						'height': 150,
						'left': ($(window).width()/2)-125,
						'top': ($(document).scrollTop() + ($(window).height()/2))-75,
						'background-color': '#ffffff',
						'border': '5px double red',
						'color': '#000000',
						'line-height': 11,
						'text-align': 'center',
						'-moz-border-radius': '6px 6px',
						'webkit-border-radius': 6,
						'border-radius': 6,
						'font-size': 14
					}).fadeOut(3000);
					$("#reset").click();
					
				} else alert(mensaje);
			})
			
		});
	</script>
  </head>
  <body>
	  <div id="result"></div>
	  <form action="" method="post">
<div id="todo">
	<div id="todo1">
		<div id="banner"><!--<img src="images/meeting_2013.jpg" alt="<?php echo $idiom[31]; ?>" title="<?php echo $idiom[31]; ?>" />--></div>
		<div id="div0">
			<a href="villa.php?lan=es"><img src="images/es.png" alt="Español" title="Español" /></a>
			<a href="villa.php?lan=eu"><img src="images/eu.png" alt="Euskera" title="Euskera" /></a>
			<a href="villa.php?lan=en"><img src="images/en.png" alt="English" title="English" /></a>
		</div>
		<div id="div1">
			<span class="demo uno"><?php echo $idiom[0]; ?></span>
			<span class="demo"><?php echo $idiom[1]; ?></span>
			<span class="intro"><?php echo $idiom[2]; ?></span>&nbsp;&nbsp;<span style="color: #808080;"><?php echo $idiom[3]; ?></span>
			<div class="intern">
				<div class="intForm">
					<input type="radio" checked="checked" name="atleta" id="atleta1" value="1" /><label for="atleta1" ><?php echo $idiom[4]; ?></label><br />
					<input type="radio" name="atleta" id="atleta2" value="2" /><label for="atleta2" ><?php echo $idiom[5]; ?></label><br />
					<hr />
					
					<div style="width: 150px;margin-right: 20px;"><?php echo $idiom[6]; ?> <br /><input style="width: 100%" type="text" id="nombre" /> </div>
					<div style="width: 320px"><?php echo $idiom[7]; ?><br /><input style="width: 100%" type="text" id="apellido" /></div>
					<div style="width: 320px;margin-right: 20px;"><?php echo $idiom[8]; ?><br /><input style="width: 100%" type="text" id="correo" /> </div>
					<div style="width: 150px"><?php echo $idiom[9]; ?><br /><input style="width: 100%" type="text" id="movil" /></div>
				</div>
			</div>
			<span class="oblig"><?php echo $idiom[10]; ?></span>
		</div>
		<div id="div2">
			<span class="intro"><?php echo $idiom[11]; ?></span>
			<div class="intern">
				<ul class="img">
					<li id="imgMasc"><img src="images/men<?php echo $lan; ?>.gif" /></li>
					<li id="imgFem"><img src="images/mujer<?php echo $lan; ?>.gif" /></li>
				</ul>
				<div class="divid masc">
					<input type="hidden" id="conta" value="0" />
					<!--<img src="images/men<?php echo $lan; ?>.gif" />-->
					<span class="prueba"><?php echo $idiom[12]; ?></span><span class="prueba"><?php echo $idiom[13]; ?></span><br />
					<input type="checkbox" name="categoria[]" class="cambiCheq" id="categoria1" value="100m|80" /><label class="catG" for="categoria1" >100m</label>10,60<br />
					<input type="checkbox" name="categoria[]" class="cambiCheq" id="categoria2" value="200m|81" /><label class="catG" for="categoria2" >200m</label>21,30<br />
					<!--<input type="checkbox" name="categoria[]" class="cambiCheq" id="categoria3" value="400m|10" /><label class="catG" for="categoria3" >400m</label>47,50<br />-->
					<input type="checkbox" name="categoria[]" class="cambiCheq uniq" id="categoria16" value="600m|82" /><label class="catG1" for="categoria16" >600m <?php echo $idiom[34]; ?></label><br />
					<input type="checkbox" name="categoria[]" class="cambiCheq uniq" id="categoria20" value="600m|83" /><label class="catG1" for="categoria20" >600m <?php echo $idiom[35]; ?></label><br />
					<input type="checkbox" name="categoria[]" class="cambiCheq" id="categoria4" value="800m|84" /><label class="catG" for="categoria4" >800m</label>1,48,50<br />
					<!--<input type="checkbox" name="categoria[]" class="cambiCheq" id="categoria13" value="1000m|20" /><label class="catG1" for="categoria13" >1000m <?php echo $idiom[34]; ?></label><br />-->
					<input type="checkbox" name="categoria[]" class="cambiCheq" id="categoria5" value="1500m|85" /><label class="catG" for="categoria5" >1500m</label>3,40,50<br />
					<input type="checkbox" name="categoria[]" class="cambiCheq" id="categoria6" value="3000m|86" /><label class="catG" for="categoria6" >3000m</label>15,50/8,10<br />
					<input type="checkbox" name="categoria[]" class="cambiCheq" id="categoria7" value="3000m obst|87" /><label class="catG" for="categoria7" >3000m obst</label>8,50<br />
					<!--<input type="checkbox" name="categoria[]" class="cambiCheq" id="categoria7" value="Longitud|14" /><label class="catG" for="categoria7" >Longitud</label>7,50m<br />-->
					<input type="checkbox" name="categoria[]" class="cambiCheq" id="categoria8" value="Triple|88" /><label class="catG" for="categoria8" >Triple</label>15,50m<br />
				</div>
				<div class="divid fem">
					<!--<img src="images/mujer<?php echo $lan; ?>.gif" />-->
					<span class="prueba"><?php echo $idiom[12]; ?></span><span class="prueba"><?php echo $idiom[13]; ?></span><br />
					<input type="checkbox" name="categoria[]" class="cambiCheq2" id="categoria9" value="200m|89" /><label class="catG" for="categoria9" >200m</label>24,50<br />
					<input type="checkbox" name="categoria[]" class="cambiCheq2 uniq" id="categoria15" value="600m|90" /><label class="catG1" for="categoria15" >600m <?php echo $idiom[34]; ?></label><br />
					<input type="checkbox" name="categoria[]" class="cambiCheq2 uniq" id="categoria19" value="600m|91" /><label class="catG1" for="categoria19" >600m <?php echo $idiom[35]; ?></label><br />
					<input type="checkbox" name="categoria[]" class="cambiCheq2" id="categoria10" value="800m|92" /><label class="catG" for="categoria10" >800m</label>2,06,00<br />
<!--					<input type="checkbox" name="categoria[]" class="cambiCheq2" id="categoria14" value="1000m|21" /><label class="catG1" for="categoria14" >1000m <?php echo $idiom[34]; ?></label><br />-->
					<input type="checkbox" name="categoria[]" class="cambiCheq2" id="categoria11" value="3000m|93" /><label class="catG" for="categoria11" >3000m</label>16,15,00/9,15<br />
					<input type="checkbox" name="categoria[]" class="cambiCheq2" id="categoria12" value="3000m obst|94" /><label class="catG" for="categoria12" >3000m obst</label>10,25<br />
					<input type="checkbox" name="categoria[]" class="cambiCheq2" id="categoria13" value="Triple|95" /><label class="catG" for="categoria13" >Triple</label>12,60m<br />
					<input type="checkbox" name="categoria[]" class="cambiCheq2" id="categoria18" value="Pertiga|96" /><label class="catG" for="categoria18" ><?php echo $idiom[36]; ?></label>3,80m<br />
				</div>
			</div>
			<span class="oblig"><?php echo $idiom[10]; ?></span>
		</div>
		<div id="div3">
			<span class="intro"><?php echo $idiom[15]; ?></span>
			<div class="intern">
				<div class="intForm">
					<div style="width: 179px;margin-right: 20px;"><?php echo $idiom[6]; ?> <br /><input style="width: 100%" type="text" id="nombreA" /> </div>
					<div style="width: 180px;margin-right: 20px;"><?php echo $idiom[7]; ?><br /><input style="width: 100%" type="text" id="apellidoA" /></div>
					<div style="width: 100px"><?php echo $idiom[18]; ?><input style="width: 100%" type="text" id="fn" /></div>
					<div style="width: 164px;margin-right: 20px;"><?php echo $idiom[16]; ?> <!--<span class="aster"></span>--><br /><input style="width: 100%" type="text" id="club" /></div>
					<div style="width: 120px;margin-right: 20px;"><?php echo $idiom[17]; ?> <!--<span class="aster"></span>--><br /><input style="width: 100%" type="text" id="lic" /></div>
					<div style="width: 176px"><?php echo $idiom[19]; ?> <br /><input checked="checked" type="radio" class="sexo" name="sexo" id="sexo1" value="M" />
						<label for="sexo1" ><?php echo $idiom[20]; ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" class="sexo" name="sexo" id="sexo2" value="F" /><label for="sexo2" ><?php echo $idiom[21]; ?></label></div>
				</div>
			</div>
			<span class="oblig"><?php echo $idiom[10]; ?></span>
		</div>
		<div id="div3">
			<span class="intro"><?php echo $idiom[22]; ?> <?php echo $idiom[33]; ?></span>
			<div class="intern">
				<div class="intForm">
					<div style="width: 90px;margin: 37px 20px 0 0;font-variant: small-caps;">
						<input type="hidden" id="pr0" class="pr" value="" /><input type="hidden" id="pr1" class="pr" value="" />
						<span id="prue0" class="prue"><?php echo $idiom[12]; ?> 1</span><br /><span id="prue1" class="prue"><?php echo $idiom[12]; ?> 2</span>
					</div>
					<div style="width: 115px;margin-right: 20px;">
						<?php echo $idiom[23]; ?><br /><input style="width: 100%" class="mrca" type="text" id="datos11" /><br /><input style="width: 100%" class="mrca" type="text" id="datos12" />
					</div>
					<div style="width: 115px;margin-right: 20px;">
						<?php echo $idiom[24]; ?><br /><input style="width: 100%" class="mrca" type="text" id="datos21" /><br /><input style="width: 100%" class="mrca" type="text" id="datos22" />
					</div>
					<div style="width: 115px;">
						<?php echo $idiom[25]; ?><br /><input style="width: 100%" class="mrca" type="text" id="datos31" /><br /><input style="width: 100%" class="mrca" type="text" id="datos32" />
					</div>
					
					<div style="width: 100%;"><br /><?php echo $idiom[26]; ?><br /><textarea style="width: 100%" id="observ" /></textarea></div>
				</div>
			</div>
			<span class="oblig"><?php echo $idiom[10]; ?></span>
		</div>
		<div id="div4">
			<span class="intro"><?php echo $idiom[27]; ?></span>
			<div class="intern">
				<div class="intForm">
					<div class="largo"><input type="checkbox" id="acepto" />
						<label for="acepto" ><?php echo $idiom[28]; ?></label><br /></div>
					<div class="ultimo largo">
						<input style="display: none" type="button" value="<?php echo $idiom[29]; ?>" id="envia" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input id="reset" type="reset" value="<?php echo $idiom[30]; ?>" />
					</div>
				</div>
			</div>
		</div>
	</div>
</div></form>
  </body>
</html>
