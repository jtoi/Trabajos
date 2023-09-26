/* 
 * Trabajo con los artistas del sitio
 */

$(document).ready(function(){
	$('#botInsMod').removeClass('hide');
	$("#btnBusc").focus();
	$("#idvf-idioma").show();
	$("#nombre").attr("required", true);
	$("#email").attr("required", true).attr("type", "email");
	$("#idvf-activo").show();
	
//	$(':file').on('fileselect', function(event, numFiles, label) {
//        console.log(numFiles);
//        console.log(label);
//    });
	
	constTabla();
});

$("#coord").focus(function(){
	if ($(this).val() == 'ex. 23.090125, -82.32810') $(this).val('');
});
$("#coord").blur(function(){
	if ($(this).val() == '') $(this).val('ex. 23.090125, -82.32810');
});

$("#btnBusc").click(function(){
	borraform();
	$("#ttitle").html(_BUSCA);
	$("#form").removeClass('hide');
	$("#accion").val('busca');
	$("#caj_formBusc").removeClass('hide');
	$("#idvf-contr").addClass('hide');
	$("#caj_formIns").addClass('hide');
	$("#idvf-rolm").show();
	$("#idvf-roli").hide();
	$("#idvf-idioma").hide();
	$("#idvf-activo").hide();
	$("#idvf-coord").hide();
	$("#idvf-imagen").hide();
	$("#nombre").attr("required", false).focus();
	$("#email").attr("required", false).attr("type", "text");
});

$('input[type=text]').click(function(){
	$(this).removeClass('has-error');
});

////$("#btnBusc").focusout(function(){
////	$("#ttitle").html('');
////	$("#form").addClass('hide');
////});
//
//$("#0_contr").click(function(){
//	alert (_CAMB_CTR);
//});

$("#btnInsrt").click(function(){
	borraform();
	$("#ttitle").html(_INSE);
	$("#accion").val('inserta');
	$("#form").removeClass('hide');
	$("#caj_formIns").removeClass('hide');
	$("#idvf-contr").addClass('hide');
	$("#idvf-rolm").hide();
	$("#idvf-roli").show();
	$("#idvf-coord").show();
	$("#idvf-idioma").show();
	$("#idvf-imagen").show();
	$("#nombre").attr("required", true).focus();
	$("#email").attr("required", true).attr("type", "email");
	$("#idvf-activo").show();
});

/**
 * Desactiva el usuario
 * @param entero id
 * @returns cadena
 */
function tipoBorra(id) {
	$("html, body").animate({ scrollTop: 0 }, "slow");
	if (confirm(_CONF_BORRAR_ART)) {
		$("body").esperaDiv('muestra');
		$.post('index.php',{
		mdr: 'art',
		pas: 'xtg',
		fun: 'artb',
		datos: id
		}, function(data){
			var datos = eval('(' + data + ')');
			$("body").esperaDiv('cierra');
			if (datos.error.length > 0) muestraErr(datos.error);
			if (datos.data.length > 0) {
				muestraAcept(datos.data);
				constTabla();
			}
		});
	}
}

/**
 * Carga los datos del Usuario para que sean editados
 * @param {type} id
 * @returns {undefined}
 */
function tipoEdit(id){
	$("#accion").val('modifica');
	$("#form").removeClass('hide');
	$("#caj_formIns").removeClass('hide');
	$("#idvf-idioma").show();
	$("#idvf-activo").show();
	$("#idvf-imagen").show();
	$("body").esperaDiv('muestra');
	$.post('index.php',{
		mdr: 'art',
		pas: 'xtg',
		fun: 'arte',
		datos: id
	}, function(data){
		var datos = eval('(' + data + ')');
		$("body").esperaDiv('cierra');
		if (datos.error.length > 0) muestraErr(datos.error);
		if (datos.data.length > 0) {
			$("#ttitle").html(_MODIF);
			var res = datos.data;
			$("#id").val(res[0]);
			$("#nombre").val(res[1]);
			$("#email").val(res[2]);
			if (res[3] == 1) $("#0_activo").attr('checked', 'checked'); else  $("#1_activo").attr('checked', 'checked');
			$("#seudo").val(res[4]);
			$("#direcc").val(res[5]);
			$("#coord").val(res[6]);
			$("html, body").animate({ scrollTop: 0 }, "slow");
		}
	});
}

function borraform(){
	$("#nombre").val('');
	$("#email").val('');
	$("#seudo").val('');
	$('#direcc').val('');
	$('#id').val('');
	$("#coord").val('ex. 23.090125, -82.32810');
	$("#0_activo").attr('checked','checked');
	$("#idioma > option").removeAttr('selected');
	$('#idioma option[value=1]').attr('selected',1);
	$("#idvf-imagen input").val('');
	$("#form").addClass('hide');
}

/**
 * Envía los datos del usuario del sitio para su registro
 * @returns {Boolean}
 */
function verifica(){
	if ($("#accion").val() == 'busca') {
		var pend = ' 1=1';
		
		$("body").esperaDiv('muestra');
		$("html, body").animate({ scrollTop: 0 }, "slow");
		
		if ($("#nombre").val().length > 1) {
			pend += " and a.nombre like '%"+$("#nombre").val()+"%'";
		}
		if ($("#email").val().length > 1) {
			pend += " and a.correo like '%"+$("#email").val()+"%'"
		}
		if ($("#seudo").val().length > 1) {
			pend += " and a.seudonimo like '%"+$("#seudo").val()+"%'"
		}
		if ($("#direcc").val().length > 1) {
			pend += " and a.direccion like '%"+$("#direcc").val()+"%'"
		}
			
		$("#buscar").val(pend);
		constTabla();
		$("#reset").click();
		$("body").esperaDiv('cierra');
		$("#form").addClass('hide');
		return false;
	
	}
	
	if ($("#nombre").val().length < 4) {
		$("#nombre").focus().addClass('has-error');
		muestraErr(_ERR_NOMB);
		return false;
	}
	
	if ($("#email").val().length < 4) {
		$("#email").focus().addClass('has-error');
		muestraErr(_ERR_EMAIL);
		return false;
	}
	
	if ($("#coord").val() == 'ex. 23.090125, -82.32810') var coord = '';
	else var coord = $("#coord").val();
	
	$("body").esperaDiv('muestra');
	$("html, body").animate({ scrollTop: 0 }, "slow");
	

	var datos = $("#nombre").val() + '|' + $("#email").val() + '|' + $("#seudo").val() + '|' + $('#direcc').val() + '|' + $("input[name='activo']:checked").val() + '|' + $('#id').val()  + '|' + coord + '|' + $('#idiomaT').val() + '|' + $('#accion').val() + '|' + $('#idioma').val();
//	alert(datos);
	var file_data = $('#imagen').prop('files')[0];   
	var form_data = new FormData();                  
	form_data.append('file', file_data);
	form_data.append('mdr', 'art');
	form_data.append('pas', 'xtg');
	form_data.append('fun', 'arti');
	form_data.append('datos', datos);
//	alert(form_data);                         
	$.ajax({
		url: 'index.php', // point to server-side PHP script 
		dataType: 'text',  // what to expect back from the PHP script, if anything
		cache: false,
		contentType: false,
		processData: false,
		data: form_data,                         
		type: 'post',
		success: function(response){
			var datos = eval('(' + response + ')');
			if (datos.error.length > 0) muestraErr(datos.error);
			$("body").esperaDiv('cierra');
			if (datos.data.length > 0) {
				muestraAcept(datos.data);
				borraform();
				constTabla();
			}
		}
	 });	
	
	return false;
}
