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

function ValidDayMonth(dayNum, monthNum, yearNum){

	yearNum = parseInt(yearNum);
	if ((dayNum==0) || (monthNum==0) || (yearNum==0))return false;
	if ((isNaN(dayNum)) || isNaN(monthNum) || isNaN(yearNum))return false;
	if ((monthNum > 12) || (yearNum < (aano-13)) || (yearNum > (aano-6)))return false;
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
	ano = s.substring(s.lastIndexOf('/')+1,s.length);
	if (ValidDayMonth(dia, mes, ano)) return true;
	return false;
}

function cambiaPrecio(){
	if ($("#ssocio").attr("checked")){
		$("#precio1").text("(36\u20ac)"); 
		$("#precio2").text("(24\u20ac)");
		$("#precio3").text("(24\u20ac)");
	} else {
		$("#precio1").text("(50\u20ac)"); 
		$("#precio2").text("(30\u20ac)");
		$("#precio3").text("(30\u20ac)");
	}
}

$(document).ready(function(){
	//pone la fecha en el permiso del documento
	if (dia*1 == 1) $("#dia").text(dia);
	else $("#dia").text("los "+dia+" d\xedas");
	$("#mes").text(mesFull);
	$("#ano").text(aano);
    cambiaPrecio();

	
	//Pone el precio en base a la cuota y si es socio o no
	$(".kirol").click(function(){cambiaPrecio();});
	
//	$("#ssocio").click(function(){$("#kirolak2").show();});
//	$("#nsocio").click(function(){$("#kirolak2").hide();});

	$("#acepto").attr('checked',false);
	$("#reset").click();
	
//	function camDni(){
//		$("#tipoD2").attr('checked','checked');
//		$(".tipoD1").hide();
//		$(".tipoD3").hide();
//	}

//	function ano(ini,fin){
//		var j = 0;
//		var arr = Array();
//		for (i=ini;i<=fin;i++) {
//			arr[j++] = i;
//		}
//		return arr;
//	}

	//muestra el botón de enviar
	$("#acepto").click(function(){
		if (this.checked == true) $("#envia").show(); 
		else $("#envia").hide();
	});

//	
	$("#envia").click(function(){
		var mensaje = '';
		var cate, cota, club;
		
		//chequeo de los datos personales
		if ($("#nombre").val().length < 3) mensaje += "\nDebe poner el nombre del ni\xf1o(a)";
		if ($("#ape").val().length < 3) mensaje += "\nDebe poner los apellidos del ni\xf1o(a)";
		if ($("#direccion").val().length < 3) mensaje += "\nDebe poner la direcci\xf3n";
		if ($("#cp").val().length === 5 && ($("#cp").val() * 1)>1) true; else mensaje += "\nDebe poner un C\xf3digo Postal v\u00e1lido";
		if ($("#loc").val().length < 3) mensaje += "\nDebe poner la localidad";
		if ($("#fn").val().length < 8 || !isDate($("#fn").val())) mensaje += "\nNo es una fecha v\u00e1lida, debe poner la fecha en formato dd/mm/yyyy. El a\xf1o debe estar entre el "+(aano-13)+" y el "
				+(aano-6);
		if ($("#cole").val().length < 3) mensaje += "\nDebe poner el nombre del colegio";
		if ($("#tel").val().length < 9 || $("#tel").val().length > 12) mensaje += "\nDebe poner un tel\xe9fono v\u00e1lido";
//		var res = /^\(?\+?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
//		if(!res.test($("#tel").val())) mensaje += "\nDebe poner un tel\xe9fono v\u00e1lido";
//		if (!$("#tel").val().match(/^\d{10}\s(-)/)) mensaje += "\nDebe poner un n\xfamero de tel\xe9fono v\xe1lido";
//		if (!$("#tel").val().match(/^\[0-9\s(-)]*$/)) mensaje += "\nDebe poner un n\xfamero de tel\xe9fono v\xe1lido";
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if(!re.test($("#correo").val())) mensaje += "\nDebe poner un correo v\u00e1lido";
//		if ($("#ssocio").attr("checked") && $("#cnt").val().length < 3) mensaje += "\nDebe poner su n\xfamero de carnet si es asociado al Club";
		if ($("#tutor").val().length < 3) mensaje += "\nDebe poner el nombre del padre, madre o tutor";
		if ($("#tdni").val().length < 3) mensaje += "\nDebe poner el DNI del tutor";
		
		//obteniendo valor de categoría
		$(".cate").each(function(){
			if ($(this).attr("checked")) cate = $(this).val();
		});
		
		//obteniendo valor de cuota
		$(".cota").each(function(){
			if ($(this).attr("checked")) cota = $(this).val();
		});
		
		//obteniendo valor club
		$(".kirol").each(function(){
			if ($(this).attr("checked")) club = $(this).val();
		});
		
		if (mensaje.length == 0) {
				espera(1);

			$("#envia").hide();
			$("#result").load('inscNavid.php', {
				catego: cate
				,cuota: cota
				,club: club
				,nomb: $("#nombre").val()
				,ape: $("#ape").val()
				,direc: $("#direccion").val()
				,cp: $("#cp").val()
				,loc: $("#loc").val()
				,fn: $("#fn").val()
				,cole: $("#cole").val()
				,tel: $("#tel").val()
				,correo: $("#correo").val()
				,carnet: $("#cnt").val()
				,obs: $("#obs").val()
				,tutor: $("#tutor").val()
				,tdni: $("#tdni").val()
				,dni: $("#dni").val()
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