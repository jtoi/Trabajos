$(document).ready(function(){
   
    $.post('datos.php',{
        dato:	'6',
        tipo:    'doc',
        beneficiario: 0
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#persona");
        options.empty();
        options.append($("<option />").val('').text('Seleccione..'));
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
        });
    });

    $("#persona").change(function(){
        if ($("#persona").val() != '') {
            $.post('datos.php',{
                idpersona:	$("#persona").val(),
                dato:       '22'
            },function(data){
                var datos = eval('(' + data + ')');
                
                var options = $("#docalias");
                options.empty();
                options.append($("<option />").val('').text('Seleccione..'));
                $.each(datos, function(index,vale) {
                    options.append($("<option />").val(vale.Id).text(vale.Documento));
                });

                // $("#alias").val(datos)
            });
        } else {
            $("#fecha").val('');
            $("#alias").val('');
            $("#idtitanes").val('');
        }
    });

    $("#docalias").change(function(){
        if ($("#persona").val() != ''){
            if ($("#docalias").val() != '') {
                $.post('datos.php',{
                    idpersona:	$("#persona").val(),
                    iddoc: $("#docalias").val(),
                    dato:       '22'
                },function(data){
                    var datos = eval('(' + data + ')');
                    datos = datos[0];
                    
                    $("#fecha").val(datos.fexp);
                    $("#alias").val(datos.DocAlias);
                    $("#idtitanes").val(datos.idTitanes);

                    if (datos.isDefault == 1){
                        $("[name=defecto][value=true]").prop('checked', true);
                    } else {
                        $("[name=defecto][value=false]").prop('checked', true);
                    }

                });
            } else {
                alert('Debe seleccionar el documento a cambiar');
                    
                $("#fecha").val('');
                $("#alias").val('');
                $("#idtitanes").val('');
            }
        } else {
            alert("Debe seleccionar la persona primeramente");
                    
            $("#fecha").val('');
            $("#alias").val('');
            $("#idtitanes").val('');
        }
    });

    $("#envia").on('click', function () {
        var pase = 1;

        if ($("#alias").val().length < 3 && $("#alias").val().length > 60) {
            alert('Error en el alias del documento');
            $("#alias").focus();
            pase = 0;
        }
        //chequeo de fechas
        var patFecha = new RegExp("(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-(?:19|20)[0-9]{2}");
        if (!patFecha.test($("#fecha").val())) {
            pase = 0;
            $("#fecha").focus();
            alert("La fecha no cumple con el patr\u00F3n recomendado 'dd-mm-yyyy'");
        }
        var date = new Date();
        fechaArr = $("#fecha").val().split('-');

        if (fechaArr[2] < date.getFullYear() ) {
            alert('Error en la fecha: es una fecha vencida');
            $("#fecha").focus();
            pase = 0;
        }
        
        if (fechaArr[2] == date.getFullYear() && fechaArr[1] <= date.getMonth())  {
            alert('Error en la fecha: es una fecha vencida o est\u00e1 pr\u00f3xima a vencerse');
            $("#fecha").focus();
            pase = 0;
        }

        if (pase == 1) {
			esperafn();
            $.post('datos.php',{
                persona:    $("#persona").val(),
                IdTitanes:  $("#idtitanes").val(),
                alias:      $("#alias").val(),
                defecto:    $('select[name=defecto] :selected').val(),
                fecha:      $("#fecha").val(),
                dato:      '21'
            },function(data){
                var datos = eval('(' + data + ')');
                if(datos.error.length > 3) alert(utf8Decode(datos.error));
                else alert("El documento ha sido correctamente actualizada con Id: "+datos.pase);
                esperafn();
            });
        }
    });

});
