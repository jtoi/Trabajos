/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var diasPorMes = new Array(12);
diasPorMes[1] = 31;
diasPorMes[2] = 29;
diasPorMes[3] = 31;
diasPorMes[4] = 30;
diasPorMes[5] = 31;
diasPorMes[6] = 30;
diasPorMes[7] = 31;
diasPorMes[8] = 31;
diasPorMes[9] = 30;
diasPorMes[10] = 31;
diasPorMes[11] = 30;
diasPorMes[12] = 31;

var mesEsp = new Array(
		"Enero",
		"Febrero",
		"Marzo",
		"Abril",
		"Mayo",
		"Junio",
		"Julio",
		"Agosto",
		"Septiembre",
		"Octubre",
		"Noviembre",
		"Diciembre"
	);
var hoy = new Date();
var dia = hoy.getDate();
var mes = hoy.getMonth();
var aano = hoy.getFullYear();
var mesFull = mesEsp[mes*1+1];
var anos;

function ValidDayMonth(dayNum, monthNum, yearNum){

	yearNum = parseInt(yearNum);
	if ((dayNum==0) || (monthNum==0) || (yearNum==0))return false;
	if ((isNaN(dayNum)) || isNaN(monthNum) || isNaN(yearNum))return false;
	if ((monthNum > 12) || (yearNum > (aano-6)))return false;
	if((dayNum > diasPorMes[monthNum]) || (monthNum == 2) && (dayNum > daysInFeb(yearNum))) return false;
	else return true;
}

function NotToday(dayNum, monthNum, yearNum,msg){
	eldia = new Date();
	if ((dayNum==eldia.getDate())&&((monthNum-1)==eldia.getMonth())&&(yearNum==eldia.getYear())) return false;
	else return true;
}

function daysInFeb(year){
	return (((year % 4 == 0) && ((!(year % 100 == 0)) || (year % 400 == 0))) ? 29:28); 
}

function isDate(s) {   
	dia = s.substring(0,s.indexOf('/'));
	mes = s.substring(s.indexOf('/')+1,s.lastIndexOf('/'));
	anos = s.substring(s.lastIndexOf('/')+1,s.length);
	if (ValidDayMonth(dia, mes, anos)) return true;
	return false;
}

function cambEntrenamiento(){
	if ($("#odh").attr("checked")) {
		$("#hora2").show();
		$("#hora1").hide();
	} else {
		$("#hora1").show();
		$("#hora2").hide();
	}
}

function cambaster(){
	if ($("#datoa1").attr("checked")) $(".aster1").show();
	else $(".aster1").hide();
}

function tresunouno(){
	if ($("#datoh1").attr("checked")) $("#less").show();
	else $("#less").hide();
}

function kirola(){
	if ($("#nsocio").attr("checked")) $("#kirolak2").hide();
	else $("#kirolak2").show();
}

$(document).ready(function(){
	//pone la fecha en el permiso del documento
	if (dia*1 == 1) $("#dia").text(dia);
	else $("#dia").text(dia);
	$("#mes").text(mesFull);
	$("#ano").text(aano);
    $("#cuota1").attr("checked", true);
    $("#nsocio").attr("checked", true);
    $("#hora2").hide();
	$("#hora1").show();
	
    cambaster();
    tresunouno();
    kirola();
    
    $(".pasta").click(function(){
    	kirola();
    })
    
    $(".lesi").click(function(){
    	tresunouno();
    })
    
    $(".core").click(function(){
    	cambaster();
    })
    
    $(".dias").click(function(){
    	cambEntrenamiento();
    });

	$("#acepto").attr('checked',false);
	$("#reset").click();

	//muestra el bot�n de enviar
	$("#acepto").click(function(){
		if (this.checked == true) $("#envia").show(); 
		else $("#envia").hide();
	});

	$("#envia").click(function(){
		var cate, cotta, sexo, entr, dd, club = '';
		var mensaje = '';
		
		//chequeo de los datos personales
		if ($("#nombre").val().length < 3) mensaje += "\nDebe poner el Nombre del atleta";
		if ($("#ape").val().length < 3) mensaje += "\nDebe poner los Apellidos del del atleta";
		if ($("#cp").val().length === 5 && ($("#cp").val() * 1)>1) true; else mensaje += "\nDebe poner un C\xf3digo Postal v\u00e1lido";
		if ($("#loc").val().length < 3) mensaje += "\nDebe poner la Localidad";
		if ($("#fn").val().length < 8 || !isDate($("#fn").val())) mensaje += "\nLa Fecha no es v\u00e1lida, debe poner la fecha en formato dd/mm/yyyy. ";
		if ($("#tel").val().length < 9 || $("#tel").val().length > 12) mensaje += "\nDebe poner un Tel\xe9fono v\u00e1lido";
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if(!re.test($("#correo").val())) mensaje += "\nDebe poner un Correo v\u00e1lido";
		if ($("#ssocio").attr("checked") && $("#cnt").val().length < 3) mensaje += "\nDebe poner su n\xfamero de carnet si es asociado al Club";
		if ($("#dni").val().length < 3) mensaje += "\nDebe poner el DNI del atleta";
		if ($("#altura").val().length < 4 && ($("#altura").val() * 1)>1) true; else mensaje += "\nDebe poner el valor de su Altura en cms";
		if ($("#peso").val().length < 4 && ($("#peso").val() * 1)>1) true; else mensaje += "\nDebe poner el valor de su Peso en kgs";
		
		//chequeo de dias de enrtenamiento
		if ($("#odh").attr("checked")){
			if (!$("#odh1").attr("checked") && !$("#odh2").attr("checked") && !$("#odh3").attr("checked")) mensaje += "\nDebe seleccionar algunas de las sessiones de entrenamiento";
			if (!$("#odh4").attr("checked") && !$("#odh5").attr("checked")) mensaje += "\nDebe seleccionar alguna de las variantes de d\u00edas de entrenamiento";
		}
		
		//chequeo de datos deportivos
		if (!$("#datoa1").attr("checked") && !$("#datoa2").attr("checked")) mensaje += "\nDebe responder la pregunta 3.1";
		if ($("#datoa1").attr("checked")) {
			if (($("#datob").val() * 1)>1)true; else mensaje += "\nDebe poner la cantidad de minutos que corre habitualmente";
			if ($("#datog").val().length == 0) mensaje += "\nDebe poner la marca que tiene en a distancia seleccionada";
		}
        
		cotta = '';
		//obteniendo valor de cuota
		$(".cota").each(function(){
			if ($(this).attr("checked")) {
                if (cotta.length > 0) cotta += ',';
                cotta += $(this).val();
            }
		});
		
		if ($("#sexom").attr("checked")) sexo = "M";
		else sexo = "F";

		if ($("#lmv").attr("checked") && $("#lmv1").attr("checked")) entr = "21|1";
		else if ($("#lmv").attr("checked") && $("#lmv2").attr("checked")) entr = "21|2";
		else if ($("#odh").attr("checked")) {
			entr = "22|";
			if ($("#odh1").attr("checked")) entr += "A";
			if ($("#odh2").attr("checked")) entr += "D";
			if ($("#odh3").attr("checked")) entr += "T";
			entr += "|";
			if ($("#odh4").attr("checked")) entr += "MJS";
			if ($("#odh5").attr("checked")) entr += "LMV";
		}

		if ($("#datoa1").attr("checked")) dd = "Si|";
		else dd = "No|";
		dd += $("#datob").val()+"|";
		dd += $("#ritmo").val()+"|";
		if ($("#datob1").attr("checked")) dd += "Si|";
		else dd += "No|";
		if ($("#datoc1").attr("checked")) dd += "Si|";
		else dd += "No|";
		if ($("#datod1").attr("checked")) dd += "Si|";
		else dd += "No|";
		if ($("#datodd1").attr("checked")) dd += "Si|";
		else dd += "No|";
		if ($("#datoe1").attr("checked")) dd += "Si|";
		else dd += "No|";
		if ($("#datof1").attr("checked")) dd += "5Km|";
		if ($("#datof2").attr("checked")) dd += "10Km|";
		if ($("#datof3").attr("checked")) dd += "15Km|";
		if ($("#datof4").attr("checked")) dd += "media Maratón|";
		if ($("#datof5").attr("checked")) dd += "Maratón|";
		dd += $("#datog").val()+"|";
		if ($("#datoh1").attr("checked")) dd += "Si|";
		else dd += "No|";
		dd += $("#datoi").val()+"|";
		if ($("#datoj1").attr("checked")) dd += "Si|";
		else dd += "No|";
		if ($("#datok1").attr("checked")) dd += "Si|";
		else dd += "No|";
		if ($("#datol1").attr("checked")) dd += "Si|";
		else dd += "No|";
		if ($("#datom1").attr("checked")) dd += "Si|";
		else dd += "No|";
		if ($("#datos1").attr("checked")) dd += "Si|";
		else dd += "No|";

		if ($("#nsocio").attr("checked")) socio = "No";
		else socio = "Si";
		
		if (mensaje.length == 0) {
			espera(1);

			$("#envia").hide();
			$("#result").load('runners_16.php', {
				nomb: $("#nombre").val()
				,ape: $("#ape").val()
				,fn: $("#fn").val()
				,sex: sexo
				,alt: $("#altura").val()
				,pes: $("#peso").val()
				,dni: $("#dni").val()
				,loc: $("#loc").val()
				,cp: $("#cp").val()
				,tel: $("#tel").val()
				,correo: $("#correo").val()
				,entr: entr
				,carnet: $("#cnt").val()
				,obs: dd
				,pin: socio
			});
			espera(0);
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
			}).fadeOut(4000);
			$("#reset").click();
			$("#tipoD2").attr('checked',false);
			$("#acepto").attr('checked',false);
			$(".datGr").hide();
		} else {
			alert(mensaje);
		}
	})

});

function espera(vis) {
	if (vis == 1)
		$("#espera").css({
			'left': ($(window).width()/2)-24,
			'top': ($(document).scrollTop() + ($(window).height()/2))-24,
			'display':'inline'
		}); else $("#espera").css({
			'display':'none'
		});
}