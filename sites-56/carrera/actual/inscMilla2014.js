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

var toDay = new Date();
var anoAct = toDay.getFullYear();
//var licencia = false;

var edad1 = (anoAct - 1);
var edad6 = (anoAct - 6);
var edad7 = (anoAct - 7);
var edad8 = (anoAct - 8);
var edad9 = (anoAct - 9);
var edad10 = (anoAct - 10);
var edad11 = (anoAct - 11);
var edad12 = (anoAct - 12);
var edad13 = (anoAct - 13);
var edad15 = (anoAct - 15);
var edad16 = (anoAct - 16);
var edad17 = (anoAct - 17);
var edad18 = (anoAct - 18);
var edad19 = (anoAct - 19);
var edad20 = (anoAct - 20);
var edad35 = (anoAct - 35);
var edad90 = (anoAct - 90);

var arrDias = Array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31);
var arrMes = Array(1,2,3,4,5,6,7,8,9,10,11,12);

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
//    alert( 'hola'+pattern.test(emailAddress) );
    return pattern.test(emailAddress);
};

function ValidDayMonth(dayNum, monthNum, yearNum){

	yearNum = parseInt(yearNum);
	if ((dayNum==0) || (monthNum==0) || (yearNum==0))return false;
	if ((isNaN(dayNum)) || isNaN(monthNum) || isNaN(yearNum))return false;
	if ((monthNum > 12) || (yearNum < 1900) || (yearNum > 2120))return false;
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
}

$(document).ready(function(){
	$('input').css('text-transform','uppercase');
	$("#tipoD2").attr('checked',false);
	$("#acepto").attr('checked',false);
	$("#reset").click();
//	$("#categoria8").attr('checked','checked');
	$("#equipon").attr('checked','checked');
	
	//poniendo el año al inicio en todas las casillas
	cambAno(ano(edad10,edad9));
	
	$(".cate").click(function(){
		switch ($(this).val()) {
			case '63':
				var arrAno = ano(edad6,edad1);
				$("#sexo1").attr("checked",false);
				$("#sexo2").attr("checked",false);
				break;
			case '64':
				var arrAno = ano(edad8,edad7);
				$("#sexo1").attr("checked",false);
				$("#sexo2").attr("checked",false);
				break;
			case '65':
				var arrAno = ano(edad10,edad9);
				$("#sexo1").attr("checked",false);
				$("#sexo2").attr("checked",false);
				break;
			case '66':
				var arrAno = ano(edad12,edad11);
				$("#sexo1").attr("checked",false);
				$("#sexo2").attr("checked",false);
				break;
			case '67':
				var arrAno = ano(edad15,edad13);
				$("#sexo1").attr("checked",'checked');
				$("#sexo2").attr("checked",'');
				camDni();
				break;
			case '68':
				var arrAno = ano(edad19,edad16);
				$("#sexo1").attr("checked",false);
				$("#sexo2").attr("checked",false);
				camDni();
				break;
			case '70':
				var arrAno = ano(edad90,edad20);
				$("#sexo1").attr("checked",'checked');
				$("#sexo2").attr("checked",'');
				camDni();
				break;
			case '71':
				var arrAno = ano(edad90,edad35);
				$("#sexo1").attr("checked",false);
				$("#sexo2").attr("checked",false);
				break;
			case '9':
				var arrAno = ano(edad19,edad16);
				$("#sexo2").attr("checked",'checked');
				$("#sexo1").attr("checked",'');
				break;
			case '10':
				var arrAno = ano(edad90,edad20);
				$("#sexo2").attr("checked",'checked');
				$("#sexo1").attr("checked",'');
				camDni();
				break;
			case '11':
				var arrAno = ano(edad90,edad20);
				$("#sexo2").attr("checked",'checked');
				$("#sexo1").attr("checked",'');
				camDni();
				break;
		}
		//cambia año para todos los drops
		cambAno(arrAno);
	});

	//cambia el año para todos los drops
	function cambAno(arr){
    j=0;
//		for (j=0;j<7;j++){
			$("#ano"+j).get(0).options.length = 0;
			for(i=0;i<arr.length;i++){
				$("#ano"+j).get(0).options[i] = new Option(arr[i]);
			}
//		}
	}
    j=0;
//    for (j=0;j<7;j++){
        $("#dia"+j).get(0).options.length = 0;
        for(i=0;i<arrDias.length;i++){
            $("#dia"+j).get(0).options[i] = new Option(arrDias[i]);
        }
        $("#mes"+j).get(0).options.length = 0;
        for(i=0;i<arrMes.length;i++){
            $("#mes"+j).get(0).options[i] = new Option(arrMes[i]);
        }
//    }
	
	function camDni(){
		$("#tipoD2").attr('checked','checked');
		$(".tipoD1").hide();
		$(".tipoD3").hide();
	}

	function ano(ini,fin){
		var j = 0;
		var arr = Array();
		for (i=ini;i<=fin;i++) {
			arr[j++] = i;
		}
		return arr;
	}
	
	$(".edad6").html(edad6);
	$(".edad7").html(edad7);
	$(".edad10").html(edad10);
	$(".edad11").html(edad11);
	$(".edad12").html(edad12);
	$(".edad15").html(edad15);
	$(".edad13").html(edad13);
	$(".edad16").html(edad16);
	$(".edad17").html(edad17);
	$(".edad8").html(edad8);
	$(".edad9").html(edad9);
	$(".edad18").html(edad18);
	$(".edad19").html(edad19);
	$(".edad20").html(edad20);
	$(".edad35").html(edad35);

	$("#categoria6").click(function(){$("#tipoD2").attr('checked',true);$(".aster").html('*');})
	$("#categoria7").click(function(){$("#tipoD2").attr('checked',true);$(".aster").html('*');})
	$("#categoria5").click(function(){$("#tipoD2").attr('checked',true);$(".aster").html('');})
	$("#categoria8").click(function(){$("#tipoD2").attr('checked',false);$(".aster").html('');})
	$("#categoria4").click(function(){$("#tipoD2").attr('checked',false);$(".aster").html('');})
	$("#categoria3").click(function(){$("#tipoD2").attr('checked',false);$(".aster").html('');})
	$("#categoria2").click(function(){$("#tipoD2").attr('checked',false);$(".aster").html('');})
	$("#categoria1").click(function(){$("#tipoD2").attr('checked',false);$(".aster").html('');})
	
	//funcionalidad para mostrar los textbox de las licencias deportivas
	$(".cate").click(function(){
		if($("#categoria6").attr("checked") || $("#categoria7").attr("checked")) $(".datGr").show();
		else $(".datGr").hide();
	});

	//muestra el botón de enviar
	$("#acepto").click(function(){
		if (this.checked == true) $("#envia").show(); else $("#envia").hide();
	})

	//clic en los radio de grupo
	$(".clsEqui").click(function(){
		//alert($(".clsEqui").val());
		if ($('#equipos').attr('checked')){
			$("#equipDat").show();
			$("#indvDat").hide();
		} else {
			$("#equipDat").hide();
			$("#indvDat").show();
		}
	});
	
	//pone el nombre del atleta como contacto
	$("#nombre0").blur(function(){$("#nomm").val($("#nombre0").val())});
	$("#nombre1").blur(function(){$("#nomm").val($("#nombre1").val())});

//	
	$("#envia").click(function(){
		var mensaje = '';
		var dni = false;
		var lic = false;
        var categoria = 0;
        var fechan = $("#ano0").val()+'-'+$("#mes0").val()+'-'+$("#dia0").val();
		
		//chequeo del nombre del equipo
		//chequeo de los datos personales
//		if ($("#equipon").attr("checked")) { //la inscripción es individual
//			if ($("#nombre0").val().length < 3) mensaje += "\nDebe poner el nombre y los apellidos del atleta";
//			if ($("#dni0").val().length < 3) mensaje += "\nDebe poner un DNI v\u00e1lido";
//			if (($("#categoria6").attr("checked") || $("#categoria7").attr("checked")) && $("#lic0").val().length < 3) mensaje += "\nDebe poner un n\u00famero de licencia v\u00e1lido";
//		} else { //la inscripción es por grupo
//		if ($("#equipo").val().length < 3) mensaje += "\nDebe poner el nombre del equipo";
//			for (var i=1;i<4;i++){
//				if ($("#nombre"+i).val().length < 3) mensaje += "\nDebe poner el nombre y los apellidos del atleta - "+i+"\nEl equipo como m\u00ednimo debe tener 3 integrantes";
//				if ($("#dni"+i).val().length < 3) mensaje += "\nDebe poner un DNI v\u00e1lido para el atleta - "+i;
//				if (($("#categoria6").attr("checked") || $("#categoria7").attr("checked")) && $("#lic"+i).val().length < 3) mensaje += "\nDebe poner un n\u00famero de licencia v\u00e1lido para el atleta - "+i;
//			}
//		}
		
		//categor\u00eda
        categoria = $(".cate:checked").val();
		if (categoria == 71) {lic = true;dni = true;}
		if (!categoria) {mensaje += 'La prueba no puede quedarse vac\u00eda\n';}
		
		//chequeo de los datos del representante o contacto
		if ($("#nombre0").val().length < 3) mensaje += "Debe poner el nombre y los apellidos del atleta\n";
        if ($("#sex").val() == '') mensaje += "Debe seleccionar el sexo del atleta\n";
		//chequeo del código postal
		if ($("#cp").val() != '') {
			if ($("#cp").val().length == 5) {
				if (isNaN($("#cp").val())) {mensaje += "El c\u00f3digo postal debe tener 5 caracteres numéricos\n";}
			} else {mensaje += "El c\u00f3digo postal debe tener 5 caracteres\n";}
		} else {mensaje += "El c\u00f3digo postal no puede quedar vac\u00edo\n";}
		if ($("#movil").val().length < 3) mensaje += "Debe poner el m\u00f3vil del contacto\n";
		if (!isValidEmailAddress($("#correo").val())) mensaje += "Debe poner el correo\n";
        if (dni && $("#dni0").val().length < 3) mensaje += "Debe poner el DNI\n";
        if (lic && $("#lic0").val().length < 3) mensaje += "Debe poner el No. Licencia\n";
		
		
		if (mensaje.length == 0) {
			espera(1);
//			var j = 0;
//			for (i=0;i<7;i++) {
//				if(i==0 && $("#nombre"+j).val().length == 0) j++;
//				if($("#nombre"+j).val().length == 0) break;
//				fechan[i] = $("#ano"+j).val();
//				dni[i] = $("#dni"+j).val();
//				lic[i] = $("#lic"+j).val();
//				nombre[i] = $("#nombre"+j).val();
//				if (i==j) break;
//				j++;
//			}
			
			$("#envia").hide();
            $("#result").load('resultadoMilla2014.php', {
                catego: categoria
                ,evento: '8'
                ,nombre: $("#nombre0").val()
//                ,equipo: $("#equipo").val()
//                ,nombR: $("#nomm").val()
                ,fn: fechan
                ,dni: $("#dni0").val()
                ,lic: $("#lic0").val()
                ,movil: $("#movil").val()
                ,sexo: $("#sex").val()
                ,postal: $("#cp").val()
                ,correo: $("#correo").val()
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