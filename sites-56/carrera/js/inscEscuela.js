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
var mesFull = mesEsp[mes*1];
var anos;
var precio;
var ano = 0;
var cate;
var diass;
var pases = 0;

function ValidDayMonth(dayNum, monthNum, yearNum){

	yearNum = parseInt(yearNum);
	dayNum = parseInt(dayNum);
	monthNum = parseInt(monthNum);
	
	if ((dayNum==0) || (monthNum==0) || (yearNum==0))return false;
	if ((isNaN(dayNum)) || isNaN(monthNum) || isNaN(yearNum))return false;
	if ((monthNum > 12) || (yearNum < (1998)) || (yearNum > (2010)))return false;
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

function cambiaPrecio(){
	
	if ($("#ssocio").attr("checked")){
		$(".socio").show();
		$(".nosocio").hide();
		if (ano > 2004 && ano < 2011) precio = 26;
		if (ano > 1997 && ano < 2005) precio = 30;
	} else {
		$(".nosocio").show();
		$(".socio").hide();
		if (ano > 2004 && ano < 2011) precio = 32;
		if (ano > 1997 && ano < 2005) precio = 36;
	}
}

$(document).ready(function(){
	//pone la fecha en el permiso del documento
	$(".socio").hide();
	if (dia*1 == 1) $("#dia").text(dia);
	else $("#dia").text(dia);
	$("#mes").text(mesFull);
	$("#ano").text(aano);
    $("#cuota1").attr("checked", true);
    $("#nsocio").attr("checked", true);
    $("#fn").blur(function(){
    	var fec = $("#fn").val();
    	var Arrfec = fec.split('/');
    	if (Arrfec.length != 3 && pases == 0) {
    		alert('Debe entrar la Fecha de nacimiento correctamente con el formato dd/mm/yyyy');
    		pases = 1;
    		//$("#fn").val('').focus();
    	} else {
    		ano = Arrfec[2];
    		if (ano.length !=4 || ano < 1998 || ano > 2010){
        		alert('El año de nacimiento debe tener el formato yyyy y ser mayor o igual que 1998 y menor o igual a 2010');
        		$("#fn").val('').focus();
    		} else {
    			if (ano == 1998 || ano == 1999) {
    				$(".divis").hide();
    				$("#cat7").show();
    				cate = 7;
    			} else if (ano == 2000 || ano == 2001) {
    				$(".divis").hide();
    				$("#cat6").show();
    				cate = 6;
    			} else if (ano == 2002) {
    				$(".divis").hide();
    				$("#cat5").show();
    				cate = 5;
    			} else if (ano == 2003 || ano == 2004) {
    				$(".divis").hide();
    				$("#cat4").show();
    				cate = 4;
    			} else if (ano == 2005 || ano == 2006) {
    				$(".divis").hide();
    				$("#cat3").show();
    				cate = 3;
    			} else if (ano == 2007 || ano == 2008) {
    				$(".divis").hide();
    				$("#cat2").show();
    				cate = 2;
    			} else if (ano == 2009 || ano == 2010) {
    				$(".divis").hide();
    				$("#cat1").show();
    				cate = 1;
    			} 
    		}
    	}
    	cambiaPrecio();
    });
    $(".dias").attr("enabled", "true");
    
    //cambia los días en la categoría Alevin
    $(".dias").click(function(){
    	if ($(".dias:checked").length == 2){
    		diass = $(".dias:checked").map(function(){
    			return this.value;
    		}).get();
    		if ($(".dias:not(':checked')").val() == "Mie") $("#lmie").hide();
    		if ($(".dias:not(':checked')").val() == "Lun") $("#llue").hide();
    		if ($(".dias:not(':checked')").val() == "Vie") $("#lvie").hide();    		
    	} else $(".nobox").show();
    });
	
	//Pone el precio en base a la cuota y si es socio o no
	$(".kirol").click(function(){
		if (this.value=='S'){$("#kirolak2").show();}
		else {$("#kirolak2").hide();}
		cambiaPrecio();
	});
	$(".cota").click(function(){cambiaPrecio();});

	$("#acepto").attr('checked',false);
	$("#reset").click();

	//muestra el bot�n de enviar
	$("#acepto").click(function(){
		if (this.checked == true) $("#envia").show(); 
		else $("#envia").hide();
	});
	
//	//Revisa si la categoría marcada es válida para la fecha de nacimiento puesta
//	function revisaGrupo(){
//		if ($("#categoria8").attr('checked') && (anos == '2007' || anos == '2008')) return false;
//		if ($("#categoria4").attr('checked') && (anos == '2005' || anos == '2006')) return false;
//		if ($("#categoria2").attr('checked') && (anos == '2003' || anos == '2004')) return false;
//		if ($("#categoria3").attr('checked') && (anos == '2001' || anos == '2002')) return false;
//		if ($("#categoria1").attr('checked') && (anos == '2000')) return false;
//		else return true
//	}

	$("#envia").click(function(){
		var cotta, club = '';
		var mensaje = ''; 
        
//        $(".cota").each(function(){
//			if ($(this).attr("checked")) alert($(this).val());
//		});
		
		//chequeo de los datos personales
		if ($("#nombre").val().length < 3) mensaje += "\nDebe poner el nombre del ni\xf1o(a)";
		if ($("#ape").val().length < 3) mensaje += "\nDebe poner los apellidos del ni\xf1o(a)";
		if ($("#direccion").val().length < 3) mensaje += "\nDebe poner la direcci\xf3n";
		if ($("#cp").val().length === 5 && ($("#cp").val() * 1)>1) true; else mensaje += "\nDebe poner un C\xf3digo Postal v\u00e1lido";
		if ($("#loc").val().length < 3) mensaje += "\nDebe poner la localidad";
		if ($("#fn").val().length < 8 || !isDate($("#fn").val())) mensaje += "\nNo es una fecha v\u00e1lida, debe poner la fecha en formato dd/mm/yyyy. "
//		"El a\xf1o debe estar entre el "+(aano-15)+" y el "+(aano-8);
//		if (revisaGrupo()) mensaje += "\nDebe escoger una categoría acorde con el año de nacimiento";
		if ($("#cole").val().length < 3) mensaje += "\nDebe poner el nombre del colegio";
		if ($("#tel").val().length < 9 || $("#tel").val().length > 12) mensaje += "\nDebe poner un tel\xe9fono v\u00e1lido";
//        if ($("#precTot").val() == 0) mensaje += "Debe seleccionar al menos una cuota";
//		var res = /^\(?\+?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
//		if(!res.test($("#tel").val())) mensaje += "\nDebe poner un tel\xe9fono v\u00e1lido";
//		if (!$("#tel").val().match(/^\d{10}\s(-)/)) mensaje += "\nDebe poner un n\xfamero de tel\xe9fono v\xe1lido";
//		if (!$("#tel").val().match(/^\[0-9\s(-)]*$/)) mensaje += "\nDebe poner un n\xfamero de tel\xe9fono v\xe1lido";
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if(!re.test($("#correo").val())) mensaje += "\nDebe poner un correo v\u00e1lido";
		if ($("#ssocio").attr("checked") && $("#cnt").val().length < 3) mensaje += "\nDebe poner su n\xfamero de carnet si es asociado al Club";
		if ($("#tutor").val().length < 3) mensaje += "\nDebe poner el nombre del padre, madre o tutor";
		if ($("#tdni").val().length < 3) mensaje += "\nDebe poner el DNI del tutor";
        
		var cotta = '';
		//obteniendo valor de cuota
		$(".cota").each(function(){
			if ($(this).attr("checked")) {
                if (cotta.length > 0) cotta += ',';
                cotta += $(this).val();
            }
		});
		
		//obteniendo valor club
		$(".kirol").each(function(){
			if ($(this).attr("checked")) club = $(this).val();
		});
		
		if (mensaje.length == 0) {
				espera(1);

			$("#envia").hide();
			$("#result").load('inscEscuela.php', {
				catego: cate
				,cuota: cotta
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
				,precio: precio
				,diass: diass
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