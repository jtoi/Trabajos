/* 
 * Trabajo con los artistas del sitio
 */

$(document).ready(function(){
	$('#botInsMod').removeClass('hide');
	$("#btnBusc").hide();
	$("#texto").markItUp(mySettings);
	constTabla();
});

$("#btnBusc").click(function(){
	borraform();
	$("#ttitle").html(_BUSCA);
	$("#form").removeClass('hide');
	$("#accion").val('busca');
	$("#caj_formBusc").removeClass('hide');
	$("#caj_formIns").addClass('hide');
	$("#nombre").attr("required", false).focus();
});

$('input[type=text]').click(function(){
	$(this).removeClass('has-error');
});

$("#btnInsrt").click(function(){
	borraform();
	$("#ttitle").html(_INSE);
	$("#accion").val('inserta');
	if ($("#form").hasClass('hide') == true) {
		$("#form").removeClass('hide');
		$("#caj_formIns").removeClass('hide');
	} else {
		$("#form").addClass('hide');
		$("#caj_formIns").addClass('hide');
	}
	$("#nombre").attr("required", true).focus();
});

/**
 * Carga los datos del estado para que sean editados
 * @param {type} id
 * @returns {undefined}
 */
function tipoEdit(id){
	$("#accion").val('modifica');
	$("#form").removeClass('hide');
	$("#caj_formIns").removeClass('hide');
	$("body").esperaDiv('muestra');
	$.post('index.php',{
		mdr: 'cta',
		pas: 'xtg',
		fun: 'eepg',
		datos: id
	}, function(data){
		var datos = eval('(' + data + ')');
		$("body").esperaDiv('cierra');
		if (datos.error.length > 0) muestraErr(datos.error);
		if (datos.data[1].length > 0) {
			$("#ttitle").html(_MODIF);
			var res = datos.data;
			borraform();//borro lo que esté puesto
			$("#id").val(res[0]);//pongo el id
			$("#nombre").val(res[4]);//pongo el nombre
			$("#monto").val(res[5]);//pongo el monto
			$("#moneda").val(res[2]);//pongo la moneda
			$("#texto").val(res[3]);//pongo el texto
			
			$("html, body").animate({ scrollTop: 0 }, "slow");
		}
	});
}

/**
 * Envía los datos del estado del sitio para su registro
 * @returns {Boolean}
 */
function verifica(){
	
	var valores = '';
	
	if ($("#accion").val() == 'busca') {
		var pend = ' 1=1';
		
		$("body").esperaDiv('muestra');
		$("html, body").animate({ scrollTop: 0 }, "slow");
		
		if ($("#nombre").val().length > 1) {
			pend += " and a.nombre like '%"+$("#nombre").val()+"%'";
		}
			
		$("#buscar").val(pend);
		constTabla();
		$("#reset").click();
		$("body").esperaDiv('cierra');
		$("#form").addClass('hide');
		return false;
	
	}
	
	$("body").esperaDiv('muestra');
	$("html, body").animate({ scrollTop: 0 }, "slow");
	
	$.post('index.php',{
		mdr: 'cta',
		pas: 'xtg',
		fun: 'iepg',
		datos: $('#id').val() + '|' + $('#accion').val() + '|' + $('#nombre').val() + '|' + $('#moneda').val() + '|' + $('#texto').val() + '|' + $('#monto').val()
	},function(data){
		var datos = eval('(' + data + ')');
		$("body").esperaDiv('cierra');
		if (datos.error.length > 0) muestraErr(datos.error);
		if (datos.data.length > 0) {
			muestraAcept(datos.data);
			borraform();
			$("#form").addClass('hide');
			constTabla();
		}
	});
	
	return false;
}
