/* 
 * Trabajo con los usuarios del sitio
 */

$(document).ready(function(){
	$('#botInsMod').removeClass('hide');
	$("#btnBusc").focus();
	$("#idvf-contr").addClass('hide');
	$("#idvf-rolm").hide();
	$("#idvf-roli").show();
	$("#idvf-idioma").show();
	$("#nombrei").attr("required", true);
	$("#emaili").attr("required", true).attr("type", "email");
	$("#idvf-activo").show();
	constTabla();
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
	$("#nombrei").attr("required", false).focus();
	$("#emaili").attr("required", false).attr("type", "text");
});

$('input[type=text]').click(function(){
	$(this).removeClass('has-error');
});

$("#0_contr").click(function(){
	alert (_CAMB_CTR);
});

$("#btnInsrt").click(function(){
	borraform();
	$("#ttitle").html(_INSE);
	$("#accion").val('inserta');
	$("#form").removeClass('hide');
	$("#caj_formIns").removeClass('hide');
	$("#idvf-contr").addClass('hide');
	$("#idvf-rolm").hide();
	$("#idvf-roli").show();
	$("#idvf-idioma").show();
	$("#nombrei").attr("required", true).focus();
	$("#emaili").attr("required", true).attr("type", "email");
	$("#idvf-activo").show();
});

/**
 * Desactiva el usuario
 * @param entero id
 * @returns cadena
 */
function tipoBorra(id) {
	$("html, body").animate({ scrollTop: 0 }, "slow");
	if (confirm(_CONF_BORRAR_USR)) {
		$("body").esperaDiv('muestra');
		$.post('index.php',{
		mdr: 'usr',
		pas: 'xtg',
		fun: 'usub',
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
	$("#idvf-contr").removeClass('hide');
	$("#idvf-idioma").show();
	$("#idvf-rolm").hide();
	$("#idvf-roli").show();
	$("#idvf-activo").show();
	$("body").esperaDiv('muestra');
	$("#nombrei").val('');
	$("#id").val('');
	$("#emaili").val('');
	$("#0_activo").attr('checked', 'checked');
	$("#tmz > option").removeAttr('selected');
	$('#roli option[value=4]').attr('selected',1);
	$("#roli > option").removeAttr('selected');
	$("#arti > option").removeAttr('selected');
	$('#arti option[value="todos"]').attr('selected',1);
	$.post('index.php',{
		mdr: 'usr',
		pas: 'xtg',
		fun: 'usue',
		datos: id
	}, function(data){
		var datos = eval('(' + data + ')');
		$("body").esperaDiv('cierra');
		if (datos.error.length > 0) muestraErr(datos.error);
		if (datos.data.length > 0) {
			$("#ttitle").html(_MODIF);
			$("#idvf-contr").removeClass('hide');
			var res = datos.data;
			$("#nombrei").val(res[2]);
			$("#id").val(res[0]);
			$("#emaili").val(res[3]);
			if (res[4] == 1) $("#0_activo").attr('checked', 'checked'); else $("#1_activo").attr('checked', 'checked');
			$('#tmz option[value=' + res[6] + ']').attr('selected', 1);
			$('#roli option[value=' + res[1] + ']').attr('selected', 1);
			if (res[5] > 0) $('#arti option[value='+res[5]+']').attr('selected',1);
			$("#id").val(res[0]);
			$("html, body").animate({ scrollTop: 0 }, "slow");
		}
	});
}

/**
 * Comment
 */
function borraform() {
	$("#nombrei").val('');
	$("#emaili").val('');
	$("#0_activo").attr('checked', 'checked');
	$("#idioma > option").removeAttr('selected');
	$('#idioma option[value=1]').attr('selected', 1);
	$("#tmz > option").removeAttr('selected');
	$('#tmz option[value=115]').attr('selected', 1);
	$("#arti > option").removeAttr('selected');
	$('#arti option[value="todos"]').attr('selected',1);
	$("#form").addClass('hide');
	constTabla();
}

/**
 * EnvÃ­a los datos del usuario del sitio para su registro
 * @returns {Boolean}
 */
function verifica(){
	if ($("#accion").val() == 'busca') {
		
		$("body").esperaDiv('muestra');
		$("html, body").animate({ scrollTop: 0 }, "slow");
		
		var pend = 'a.idrol = r.id';
		if ($("#nombrei").val().length > 1) {
			pend += " and a.nombre like '%"+$("#nombrei").val()+"%'";
		}
		if ($("#emaili").val().length > 1) {
			pend += " and a.email like '%"+$("#emaili").val()+"%'"
		}
		if ($("#rolm").val() > 0) {
			pend += " and r.id like '%"+$("#rolm").val()+"%'"
		}
		if ($("#arti").val() > 0) {
			pend += " and a.id in (select idadmin from tbl_colArtistaAdmin where idartista = "+$("#arti").val()+")"
		}
		if ($("#arti").val() > 0) {
			pend += " and a.id in (select idadmin from tbl_colArtistaAdmin where idartista = "+$("#arti").val()+")"
		}
			
		$("#buscar").val(pend);
		constTabla();
		$("#reset").click();
		$("body").esperaDiv('cierra');
		$("#form").addClass('hide');
		return false;
	
	}
	
	if ($("#nombrei").val().length < 4) {
		$("#nombrei").focus().addClass('has-error');
		muestraErr(_ERR_NOMB);
		return false;
	}
	
	$("body").esperaDiv('muestra');
	$("html, body").animate({ scrollTop: 0 }, "slow");
	
	$.post('index.php',{
		mdr: 'usr',
		pas: 'xtg',
		fun: 'usur',
		datos: $("#nombrei").val() + '|' + $("#emaili").val() + '|' + $("#roli").val() + '|' + $('#arti').val() + '|' + $('#accion').val() + '|' + $("input[name='activo']:checked").val() + '|' + $('#id').val() + '|' + $("#idioma").val() + '|' + $("input[name='contr']:checked").val() + '|' + $("#tmz").val()
	},function(data){
		var datos = eval('(' + data + ')');
		$("body").esperaDiv('cierra');
		if (datos.error.length > 0) muestraErr(datos.error);
		if (datos.data.length > 0) {
			muestraAcept(datos.data);
			borraform();
			
//				if ($("#accion").val() == 'modifica')
//					$("#caj_formIns").addClass('hide').fadeOut(4000);
		}
	});
	
	
	return false;
}
