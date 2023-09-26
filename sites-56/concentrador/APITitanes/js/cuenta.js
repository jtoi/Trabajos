function envia() {
    var pase = 1;
        // alert(numero)
    var numero = $("#numero").val().toUpperCase().trim();
    // alert(numero)
    numero = numero.trim()
    var form = $("#cuentaform").val().toLowerCase();
    var forma = '';

    if ($("#alias").val().length < 5 && $("#alias").val().length > 40) {
        alert("Debe poner un alias a la cuenta para que sea m\u00e1s f\u00e1cil reconocerla");
        pase = 0;
        $("#alias").select();
    }

    if (numero.length > 30) {
        alert("Debe poner un n\u00famero de cuenta en dependencia del formato de cuenta escogido");
        pase = 0;
        $("#numero").select();
    }
	
	if (numero.length > 0){
		if (isValidCode(numero, form) == 'false'){
			alert("El n\u00famero de cuenta tiene problemas de formato, por favor rev\u00edselo");
			pase = 0;
			$("#numero").select();
		}
	}

    if (pase == 1) {
		esperafn();
        $.post('datos.php', {
            dato:3,
            formato: $("#cuentaform").val()
        }, function (data) {
            var datos = eval('(' + data + ')');
			var pais = forma = '';
			if ($("#cuentatipo").val() > 5) {
				pais = $("#pais").val();
            	forma = datos[0].Id;
			} else numero = '';

            $.post('datos.php',{
                PersonId:                       $("#persona").val(),
                AccountTypeId:                  $("#cuentatipo").val(),
                AccountIsoCurrencyTypeCode:     $("#moneda").val(),
                AccountAlias:                   $("#alias").val(),
                IsAccountDefault:               $('select[name=defecto] :selected').val(),
                AccountFormatId:                forma,
                AccountNumber:                  numero,
                AccountIsoCountryCode:          pais,
                func: 'creaCuenta'
            },function (data) {
                var datos = eval('(' + data + ')');
                if(datos.error.length > 3) alert(utf8Decode(datos.error))
                else alert("La Cuenta ha sido correctamente inscrita con Id: "+datos.pase);
                esperafn();
            });
        });
    }
}

function cambiaMon(){
	$.post('datos.php',{
        dato:	15
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#moneda");
        options.empty();
        $.each(datos, function(index,vale) {
            if (vale.nombre == 'EUR') options.append($("<option selected=selected />").val(vale.nombre).text(vale.nombre));
            else options.append($("<option />").val(vale.nombre).text(vale.nombre));
        });
    });
}

$(document).ready(function(){
    $("#cuentatipo select").val(2).change()
    $(".pais").hide();
    $(".formato").hide();
    $(".numcta").hide();
	

	$("#cuentatipo").change(function(){
		if ($("#cuentatipo").val() < 5) {
            $(".pais").hide();
            $(".formato").hide();
            $(".numcta").hide();
			if($("#cuentatipo").val() == 2 ) $("#moneda").empty().append($("<option />").val('USD').text('USD'));
			else $("#moneda").empty().append($("<option />").val('EUR').text('EUR'));
		}
		else {
            $(".pais").show();
            $(".formato").show();
            $(".numcta").show();
			if ($("#cuentatipo").val() == 203 ) $("#moneda").empty().append($("<option />").val('CUP').text('CUP'));
			else cambiaMon();

		}
	});

    $.post('datos.php',{
        dato:	'6'
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#persona");
        options.empty();
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
        });
    });


    $.post('datos.php',{
        dato:	13
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#cuentatipo");
        options.empty();
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.Id).text(vale.nombre));
        });
    });

    $.post('datos.php',{
        dato:	14
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#cuentaform");
        options.empty();
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.nombre).text(vale.nombre));
        });
    });

    $.post('datos.php',{
        dato:	2
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#pais");
        options.empty();
        $.each(datos, function(index,vale) {
            if (vale.Iso2 == 'US') options.append($("<option selected='selected' />").val(vale.Iso2).text(vale.Nombre));
            else options.append($("<option />").val(vale.Iso2).text(vale.Nombre));
        });
    });

    $("#moneda").empty().append($("<option />").val('EUR').text('EUR'));
   
});