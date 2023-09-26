$(document).ready(function() {
    $(".otract").hide();

    $("#tipo").change(function(){
        cambiaTipo (this.value) 
    });

    $.post('datos.php',{
        dato:	'6',
        beneficiario: 0
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#idpersona");
        options.empty();
        options.append($("<option />").val('').text('Seleccione..'));
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
        });
    });

    $("#activ").change(function () {
        if (this.value == '31') {
            $(".otract").show();
        } else {
            $("#activExtr").val('');
            $(".otract").hide();
        }
    })

    $("#idpersona").change(function(){
        if ($(this).val() > 0) {
            $.post('datos.php',{
                idmetodo:     $("#idpersona").val(),
                dato:	'9'
            },function(data){
                var datos = eval('(' + data + ')');
                if(datos.error.length > 3) alert(utf8Decode(datos.error))
                else {
                    $("#nombre").val(datos.pase[0].Nombre);
                    $("#papellido").val(datos.pase[0].PApellido);
                    $("#sapellido").val(datos.pase[0].SApellido);
                    $("#comercialName").val(datos.pase[0].CommercialName);
                    $("#dSocial").val(datos.pase[0].BusinessName);
                    $("#fecha").val(datos.pase[0].fechaNac);
                    var tipo = datos.pase[0].idTipo;
                    var actividad = datos.pase[0].idActividad;
                    var pais = datos.pase[0].Iso2;
                    var public = datos.pase[0].isPublicOffice;
                    $.post('datos.php',{
                        dato:	'1'
                    },function(data){
                        var datos = eval('(' + data + ')');
                        var options = $("#activ");
                        options.empty();
                        $.each(datos, function(index,vale) {
                            if (vale.Id == actividad)
                                options.append($("<option selected='selected' />").val(vale.Id).text(vale.Actividad));
                            else
                                options.append($("<option />").val(vale.Id).text(vale.Actividad));
                        });
                    });

                    $.post('datos.php',{
                        dato:	'2'
                    },function(data){
                        var datos = eval('(' + data + ')');
                        var options = $("#paiso");
                        options.empty();
                        $.each(datos, function(index,vale) {
                            if (vale.Iso2 == pais)
                                options.append($("<option selected='selected' />").val(vale.Iso2).text(vale.Nombre));
                            else
                                options.append($("<option />").val(vale.Iso2).text(vale.Nombre));
                        });
                    });

                    $.post('datos.php',{
                        dato:	'10'
                    },function(data){
                        var datos = eval('(' + data + ')');
                        var options = $("#tipo");
                        options.empty();
                        $.each(datos, function(index,vale) {
                            if (vale.Id == tipo)
                                options.append($("<option selected='selected' />").val(vale.Id).text(vale.Tipo));
                            else
                                options.append($("<option />").val(vale.Id).text(vale.Tipo));
                        });
                        cambiaTipo($("#tipo").val())
                    });

                    if (public == 1){
                        $("[name=persona][value=true]").prop('checked', true);
                    } else {
                        $("[name=persona][value=false]").prop('checked', true);
                    }
                }
                
            });
        }
    });
});