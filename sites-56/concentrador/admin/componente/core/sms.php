<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

$html = new tablaHTML;
global $temp;
$ent = new entrada;

$entorno = '1';
$numRecords = 300;
$valSelecPer = "S";
$desde = 1;

if ($_SESSION['grupo_rol'] < 2) {
	$html->java = $javascript;
	
	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = _MENU_ADMIN_SMS;
	$html->tituloTarea = 'SMS Enviados';
	$html->anchoTabla = 650;
	$html->tabed = true;
	$html->anchoCeldaI = 300;
	$html->anchoCeldaD = 340;
	
	$html->inHide($desde, 'pagina');
	$html->inHide($numRecords, 'records');
	$arrVal = array('1','2');
	$arrNom = array('Concentrador','Fincimex');
	$html->inRadio('Entorno', $arrVal, 'entorno', $arrNom, $entorno);
	$arrPer = array(
		array('D', 'Día actual'),
		array('S', 'Semana actual'),
		array('M', 'Mes actual'),
		array('T', 'Todo')
	);
	$html->inSelect('Período', 'period', 3, $arrPer, $valSelecPer);
			
	$botones = '<input type="button" id="env" value="Enviar" />&nbsp;&nbsp;&nbsp;<input type="reset" value="Cancelar" />';
	echo $html->salida($botones);
}


?>

<div id="sali">
	<img id="imgSMS" style="margin: 0 auto;" src="../images/ajax-loader3.gif" />
	<div id="SMSGen" style="display: 'none';" >
		<div class="title encabezamiento">
			<div id="encA"></div>
			<div id="encB"></div>
			<div id="encC"><a href="#" id="retroP"><<..</a>&nbsp&nbsp&nbsp<a href="#" id="avanzaP">..>></a></div>
		</div>
		<div class="title encaSms">
			<div class="colA">Fecha</div>
			<div class="colB">Teléfono</div>
			<div class="colC">Mensaje</div>
		</div>
		<?php for($i=0;$i<$numRecords; $i++) { ?>
		<div id="line<?php echo $i; ?>" class="elem">
			<div class="colA">&nbsp;</div>
			<div class="colB">&nbsp;</div>
			<div class="colC">&nbsp;</div>
		</div>
		<?php } ?>
	</div>
	
</div>

<script type="text/javascript" >
	$(document).ready(function(){
		envia();
		$("#env").click(function(){
			envia();
		});
		$("#avanzaP").click(function(){
			$("#pagina").val($("#pagina").val()*1+1);
			envia();
		})
		$("#retroP").click(function(){
			$("#pagina").val($("#pagina").val()*1-1);
			envia();
		})
	});
	
	function envia(){
		$("#imgSMS").css("display", "block");
		$("#SMSGen").css("display", "none");
		$(".elem div").html('');
		$.post('componente/core/var.php', {
				func: 'sms',
				pag: $("#pagina").val(),
				ent: $("input:radio:checked").val(),
				per: $("#period").val(),
				rec: <?php echo $numRecords; ?>
			}, function(data){
				var datos = eval('(' + data + ')');
				var arrRec = datos.repor;
				if ($.isEmptyObject(datos.error) === true) {
					$("#SMSGen").css('display', 'block');
					$("#imgSMS").css('display', 'none');
					$("#encA").html("Cant: "+datos.NTotal[0]);
					$("#encB").html("&nbsp;");
					var pag = $("#pagina").val()*1;
					var rec = $("#records").val()*1;
					var cRec = datos.NTotal[0]*1;
					(cRec > pag*rec) ? $("#avanzaP").css("display", "block") : $("#avanzaP").css("display", "none");
					pag > 1 ? $("#retroP").css("display", "block") : $("#retroP").css("display", "none");
					for(i=0;i<arrRec.length;i++) {
						var mens = '&nbsp;';
						$("#line"+i).css('display','block');
						$("#line"+i+" .colA").html(arrRec[i].fec[0]);
						$("#line"+i+" .colB").html(arrRec[i].tel[0]);
						if (arrRec[i].mens != null && arrRec[i].mens.length > 0) mens = arrRec[i].mens;
						$("#line"+i+" .colC").html(mens);
					}
					
					
				} else	alert(datos.error);
			});
	}
</script>
 