/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


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
// 			(CompareField(document.forms[0].valCom,document.forms[0].importe,'mayor','El valor de la transacción debe ser menor de 5 000.00'))&&
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
			if (datos.cont == 'true') {
				return 'true';
			} else {
				alert ("El código de la operación está repetido");
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
		var mds = $.map($('#moneda option'), function(e) { return e.value; });
		mds.join(',')
	if (paso == 1) {

		$.post('componente/comercio/ejec.php',{
			fun: 'pagoEur',
			com: comid,
			mds: mds
		},function(data){
			var datos = eval('(' + data + ')');
			$(".title_tarea1").esperaDiv('cierra');
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
					if (datos.sale.length == 1 && datos.sale[0].moneda == 'EUR'){
						var sale = '<?php echo $jsonMon; ?>';
						var mon = eval('(' + sale + ')');
						$.each(mon, function(index,vale) {
							if (vale.idmoneda == '978')
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
			var datos = eval('(' + data + ')');
			$(".title_tarea1").esperaDiv('cierra');
			$("#enviaForm").show();
			if (datos.error.length > 0) alert(datos.error);
			if (datos.cont) {
				if (datos.cont.length > 0) {
					var options = $("#pasarela");
					options.empty();
					$.each(datos.cont, function(index,vale) {
						options.append(new Option(this.nombre, this.id));
					});
					busca();
					if (datos.euro == 1) {//obligado con cambio a Euros
						$("#div_eurs").show().html('<input type="hidden" name="eur" id="eurs" value="1" />'); 
						$("#importe").val('');
						$("#usd").val('');
						pagoEur(1);
					} else if (datos.euro == 2) {//opcional con cambios a Euros
						$("#importe").val('');
						$("#div_eurs").html('<div style="width:255px" class="derecha1">Operación con cambio a EUR?:</div><div style="width:325px" class="izquierda1"><input type="checkbox" id="eurs" value="1"  class="formul" name="eur" checked /></div>').show(); 
						$("#usd").val('');
						pagoEur(1);
					} else { //sin cambio a euros
						pagoEur(0);
						$("#div_eurs").show().html('<input type="hidden" name="eur" id="eurs" value="0" />');
						$("#div_importe .derecha1").html('Importe: ');
						$("#importe").val('');
						$("#usd").val('');
					}
					mimoneda = $("#moneda option:selected").val();
				}
			}
		});
	}
	
function busca() {
	$(".title_tarea1").esperaDiv('cierra');
	$(".title_tarea1").esperaDiv('muestra');
	$("#moneda").get(0).options.length = 0;
	var passs = $("#pasaresc").val();
	if ($("#pasarela").val() != '')  passs = $("#pasarela").val();
	$.post('componente/comercio/ejec.php',{
		fun:'instpago',
		email: $("#email").val(),
		pas:passs
	},function(data){
		var datos = eval('(' + data + ')');
		$(".title_tarea1").esperaDiv('cierra');
		$("#enviaForm").show();
		if (datos.error.length > 0) alert(datos.error);
		if (datos.sale) {
			if (datos.sale.length > 0) {
				var options = $("#moneda");
				options.empty();
				if (datos.sale.length == 1 && datos.sale[0].moneda == 'EUR'){
					var sale = '<?php echo $jsonMon; ?>';
					var mon = eval('(' + sale + ')');
					$.each(mon, function(index,vale) {
						if (vale.idmoneda == '978')
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
			if($("#eurs").attr("checked") == 'checked' || $("#eurs").val() == 1) pagoEur(1);
			else pagoEur(0);

			$("#moneda").val(mimoneda).change();

			var options = $("#tarjt");
			var texto = '<?php echo _PAGO_TARJETA."<br>"; ?><div id="tuto" >';
			var lin = '';

			$.each(datos.tar, function(index,vale) {
				lin = lin + '<input type="radio" name="tarjes" id="image'+this.id+'" value="'+this.id+'"' ;
				if (this.id == 2) lin = lin + ' checked="checked"';
				lin = lin + '><label class="image'+this.id+'" for="image'+this.id+'"></label>'
			});
			options.html(texto+lin+'</div>');
		}
	});
}

function cammbp() {
	var valPas = $("#pasaresc").val();
	$('#pasarela option[value='+valPas+']').attr("selected","selected");
	busca();
	setTimeout(function(){$('#moneda option[value='+$('#valmon').val()+']').attr('selected','selected');},3000);

}
		
$(document).ready(function(){
		
	cabiaPas();
	$("#pasarela").change(function(){busca()});
	
	setTimeout(cammbp,3000);

	$("#div_usd").hide();
	$("input[name=pago]").click(function(){cabiaPas()});
	$("#comercio").change(function(){cabiaPas();});

	$("#eurs").click(function(){
		if($(this).attr("checked") === 'checked') {
			alert('pagoEur');
		} else {
			alert('busca');
		}
	});

//	$("#eurs").click(function(){
//		alert('hola');
//		if($(this).attr("checked") == 'checked') {alert('pagoEur');pagoEur(1);}
//		else {alert('busca');busca();}
//	});
});