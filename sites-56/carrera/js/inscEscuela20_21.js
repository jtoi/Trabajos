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
var mesFull = mesEsp[mes * 1];
var anos;

function ValidDayMonth(dayNum, monthNum, yearNum) {

    yearNum = parseInt(yearNum);
    if ((dayNum == 0) || (monthNum == 0) || (yearNum == 0)) return false;
    if ((isNaN(dayNum)) || isNaN(monthNum) || isNaN(yearNum)) return false;
    if ((monthNum > 12) || (yearNum < (aano - 18)) || (yearNum > (aano - 6))) return false;
    if ((dayNum > diasPorMes[monthNum]) || (monthNum == 2) && (dayNum > daysInFeb(yearNum))) return false;
    else return true;
}

function NotToday(dayNum, monthNum, yearNum, msg) {
    eldia = new Date();
    if ((dayNum == eldia.getDate()) && ((monthNum - 1) == eldia.getMonth()) && (yearNum == eldia.getYear())) return false;
    else return true;
}

function daysInFeb(year) {
    return (((year % 4 == 0) && ((!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28);
}

function isDate(s) {
    dia = s.substring(0, s.indexOf('/'));
    mes = s.substring(s.indexOf('/') + 1, s.lastIndexOf('/'));
    anos = s.substring(s.lastIndexOf('/') + 1, s.length);
    if (ValidDayMonth(dia, mes, anos)) return true;
    return false;
}

function cambiaPrecio() {
    var pase = 60;
    var vale = 0;
    if ($("#ssocio").attr("checked")) {
        $("#precio1").text("34,50");
        $("#precio5").text("30,50");
        // $("#preciom").text("15,50");
        //		$("#precio2").text("(50\u20ac)");
        //		$("#precio3").text("24");
    } else {
        $("#precio1").text("34,50");
        $("#precio5").text("30,50");
        // $("#preciom").text("18,50");
        //		$("#precio2").text("(60\u20ac)");
        //		$("#precio3").text("30");
    }

    if ($("#cuota1").attr('checked')) $("#precio4").text($("#precio1").text());
    if ($("#cuota2").attr('checked')) $("#precio4").text('');
    if ($("#cuota3").attr('checked')) $("#precio4").text($("#precio3").text());
    if ($("#cuota5").attr('checked')) $("#precio4").text($("#precio5").text());
    $("#precTot").val($("#precio4").text());
}

$(document).ready(function() {
    //pone la fecha en el permiso del documento
    if (dia * 1 == 1) $("#dia").text(dia);
    else $("#dia").text(dia);
    $("#mes").text(mesFull);
    $("#ano").text(aano);
    $("#cuota2").attr("checked", true);
    $("#nsocio").attr("checked", true);
    $("#kirolak2").show();
    $("#3di").hide();
    $("#2di").hide();
    $("#depago").hide();
    $("#deprueba").show();
    cambiaPrecio();


    //Pone el precio en base a la cuota y si es socio o no
    $(".kirol").click(function() {
        if (this.value == 'S') { $("#kirolak2").show(); } else { $("#kirolak2").hide(); }
        cambiaPrecio();
    });
    $(".cota").click(function() { cambiaPrecio(); });

    $("#fn").blur(function(e) {
        // e.preventDefault();
        if ($(this).val().length < 8 || !isDate($(this).val())) {
            $(this).focus();
            alert("\nNo es una fecha v\u00e1lida, debe poner la fecha en formato dd/mm/yyyy. El a\xf1o debe estar entre el " + (aano - 18) + " y el " + (aano - 6));
        } else {
            var s = $(this).val();
            anos = s.substring(s.lastIndexOf('/') + 1, s.length);
            switch (true) {
                case (anos == (aano - 6)):
                    $("#categoria8").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 7)):
                    $("#categoria8").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 8)):
                    $("#categoria4").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 9)):
                    $("#categoria4").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 10)):
                    $("#categoria2").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 11)):
                    $("#categoria2").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 12)):
                    $("#categoria3").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 13)):
                    $("#categoria3").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 14)):
                    $("#categoria1").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 15)):
                    $("#categoria5").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 16)):
                    $("#categoria5").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 17)):
                    $("#categoria6").attr("checked", "checked");
                    precioM();
                    break;
                case (anos == (aano - 18)):
                    $("#categoria6").attr("checked", "checked");
                    precioM();
                    break;

                default:
                    break;
            }
        }
    });
    $(".cate").click(function() {
        precioM();
    });

    $("#acepto").attr('checked', false);
    $("#reset").click();

    //muestra el bot�n de enviar
    $("#acepto").click(function() {
        // if (this.checked == true && $("#acepto2").attr('checked') == 'checked') $("#envia").show();
        if (this.checked == true) $("#envia").show();
        else $("#envia").hide();
    });
    // $("#acepto2").click(function () {
    //     if (this.checked == true && $("#acepto").attr('checked') == 'checked') $("#envia").show();
    //     else $("#envia").hide();
    // });

    //Revisa si la categoría marcada es válida para la fecha de nacimiento puesta
    function revisaGrupo() {
        console.log(anos);
        console.log(aano - 12);
        console.log(aano - 13);
        if ($("#categoria8").attr('checked') && (anos == (aano - 6) || anos == (aano - 7))) return false;
        if ($("#categoria4").attr('checked') && (anos == (aano - 8) || anos == (aano - 9))) return false;
        if ($("#categoria2").attr('checked') && (anos == (aano - 10) || anos == (aano - 11))) return false;
        if ($("#categoria3").attr('checked') && (anos == (aano - 12) || anos == (aano - 13))) return false;
        if ($("#categoria5").attr('checked') && (anos == (aano - 16) || anos == (aano - 15))) return false;
        if ($("#categoria6").attr('checked') && (anos == (aano - 18) || anos == (aano - 17))) return false;
        if ($("#categoria1").attr('checked') && (anos == (aano - 14))) return false;
        else return true
    }

    function precioM() {
        //alert($(".cate:checked").val());
        if ($(".cate:checked").val() == 99 || $(".cate:checked").val() == 100 || $(".cate:checked").val() == 101) {
            $("#3di").show();
            $("#1di").hide();
            $("#depago").show();
            $("#deprueba").show();
            $("#cuota1").attr("checked", "");
            $("#2di").hide();
            $("#cuota5").attr("checked", "checked");
        } else if ($(".cate:checked").val() == 102) {
            // $("#1di").show();
            $("#cuota1").attr("checked", "");
            $("#depago").hide();
            $("#2di").show();
            $("#3di").hide();
            $("#depago").show();
            $("#deprueba").show();
        } else {
            $("#3di").hide();
            $("#cuota2").attr("checked", "checked");
            $("#2di").hide();
            $("#1di").hide();
            $("#depago").hide();
            $("#deprueba").show();
        }

    }

    $("#envia").click(function() {
        var cate, cotta, club = '';
        var mensaje = '';

        //        $(".cota").each(function(){
        //			if ($(this).attr("checked")) alert($(this).val());
        //		});

        //chequeo de los datos personales
        if ($("#nombre").val().length < 3) mensaje += "\nDebe poner el nombre del ni\xf1o(a)";
        if ($("#ape").val().length < 3) mensaje += "\nDebe poner los apellidos del ni\xf1o(a)";
        if ($("#direccion").val().length < 3) mensaje += "\nDebe poner la direcci\xf3n";
        if ($("#cp").val().length === 5 && ($("#cp").val() * 1) > 1) true;
        else mensaje += "\nDebe poner un C\xf3digo Postal v\u00e1lido";
        if ($("#loc").val().length < 3) mensaje += "\nDebe poner la localidad";
        if ($("#fn").val().length < 8 || !isDate($("#fn").val())) mensaje += "\nNo es una fecha v\u00e1lida, debe poner la fecha en formato dd/mm/yyyy. " +
            "El a\xf1o debe estar entre el " + (aano - 18) + " y el " + (aano - 6);
        if (revisaGrupo()) mensaje += "\nDebe escoger una categor\u00eda acorde con el a\u00f1o de nacimiento";
        if ($("#cole").val().length < 3) mensaje += "\nDebe poner el nombre del colegio";
        if ($("#tel").val().length < 9 || $("#tel").val().length > 12) mensaje += "\nDebe poner un tel\xe9fono v\u00e1lido";
        //        if ($("#precTot").val() == 0) mensaje += "Debe seleccionar al menos una cuota";
        //		var res = /^\(?\+?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
        //		if(!res.test($("#tel").val())) mensaje += "\nDebe poner un tel\xe9fono v\u00e1lido";
        //		if (!$("#tel").val().match(/^\d{10}\s(-)/)) mensaje += "\nDebe poner un n\xfamero de tel\xe9fono v\xe1lido";
        //		if (!$("#tel").val().match(/^\[0-9\s(-)]*$/)) mensaje += "\nDebe poner un n\xfamero de tel\xe9fono v\xe1lido";
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (!re.test($("#correo").val())) mensaje += "\nDebe poner un correo v\u00e1lido";
        if ($("#ssocio").attr("checked") && $("#cnt").val().length < 3) mensaje += "\nDebe poner su n\xfamero de carnet si es asociado al Club";
        if ($("#tutor").val().length < 3) mensaje += "\nDebe poner el nombre del padre, madre o tutor";
        if ($("#tdni").val().length < 3) mensaje += "\nDebe poner el DNI del tutor";
        //alert($("input[name='cuota']:checked").val());
        // if ($("input[name='cuota']:checked").val() != 2) {
        if ($("#tcuenta").val().length < 3) mensaje += "\nDebe poner el Nombre del Titular de la Cuenta";
        if ($("#tdni").val().length < 3) mensaje += "\nDebe poner el DNI del Titular de la Cuenta";
        if ($("#iban").val().replace(/\s+/g, '').length != 24) mensaje += "\nDebe rectificar el IBAN.";
        // }

        //obteniendo valor de categor�a
        $(".cate").each(function() {
            if ($(this).attr("checked")) cate = $(this).val();
        });

        //obteniendo valor de sexo
        $(".sexo").each(function() {
            if ($(this).attr("checked")) sexo = $(this).val();
        });

        cotta = '';
        //obteniendo valor de cuota
        $(".cota").each(function() {
            if ($(this).attr("checked")) {
                if (cotta.length > 0) cotta += ',';
                cotta += $(this).val();
            }
        });

        // alert(cotta);

        //obteniendo valor club
        $(".kirol").each(function() {
            if ($(this).attr("checked")) club = $(this).val();
        });

        if (mensaje.length == 0) {
            espera(1);

            $("#envia").hide();
            //alert($("#tel").val());
            $("#result").load('inscEscuela22_23.php', {
                evento: $("#evento").val(),
                catego: cate,
                cuota: cotta,
                club: club,
                sexo: sexo,
                apagar: $("#precio" + cotta).html(),
                nomb: $("#nombre").val(),
                ape: $("#ape").val(),
                direc: $("#direccion").val(),
                cp: $("#cp").val(),
                loc: $("#loc").val(),
                fn: $("#fn").val(),
                cole: $("#cole").val(),
                tel: $("#tel").val(),
                correo: $("#correo").val(),
                carnet: $("#cnt").val(),
                obs: $("#obs").val(),
                tutor: $("#tutor").val(),
                tdni: $("#tdni").val(),
                dni: $("#dni").val(),
                tcuenta: $("#tcuenta").val(),
                ttdni: $("#ttdni").val(),
                iban: $("#iban").val()
            });
            espera(0);
            $("#result").css({
                'display': 'inline',
                'position': 'absolute',
                'width': 250,
                'height': 150,
                'left': ($(window).width() / 2) - 125,
                'top': ($(document).scrollTop() + ($(window).height() / 2)) - 75,
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
            $("#tipoD2").attr('checked', false);
            $("#acepto").attr('checked', false);
            $(".datGr").hide();
            //window.open('inscEscuela19_20.html', '_self');
        } else {
            alert(mensaje);
        }
    })

});

function espera(vis) {
    if (vis == 1)
        $("#espera").css({
            'left': ($(window).width() / 2) - 24,
            'top': ($(document).scrollTop() + ($(window).height() / 2)) - 24,
            'display': 'inline'
        });
    else $("#espera").css({
        'display': 'none'
    });
}