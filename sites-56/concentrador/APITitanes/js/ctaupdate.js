function envia() {
    var pase = 1;
    var forma = '';

    if ($("#alias").val().length < 5 && $("#alias").val().length > 40) {
        alert("Debe poner un alias a la cuenta para que sea m\u00e1s f\u00e1cil reconocerla");
        pase = 0;
        $("#alias").select();
    }

    if (pase == 1) {
		esperafn();
        $.post('datos.php',{
            PersonId:           $("#persona").val(),
            AccountTypeId:      $("#cuentatipo").val(),
            AccountId:          $("#idtitanes").val(),
            AccountAlias:       $("#alias").val(),
            IsAccountDefault:   $('select[name=defecto] :selected').val(),
            func:               'updateCuenta'
        },function (data) {
            var datos = eval('(' + data + ')');
            if(datos.error.length > 3) alert(utf8Decode(datos.error))
            else alert("La Cuenta con Id: "+datos.pase+" ha sido correctamente actualizada");
            esperafn();
        });
    }
}

$(document).ready(function(){

    $.post('datos.php',{
        dato:	'6',
        beneficiario: 0,
        tipo:       'cta'
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#persona");
        options.empty();
            options.append($("<option />").val('').text('Seleccione..'));
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

    $("#persona").change(function(){
        var valor = $("#persona").val();
        if (valor > 1){
            $.post("datos.php", {
                dato:       5,
                idpersona:  valor
            },function (data) {
                var datos = eval('(' + data + ')');
                $("#idtitanes").val(datos.idTitanes);

                var options = $("#cuenta");
                options.empty();
                options.append($("<option />").val('').text('Seleccione la cuenta..'));
                $.each(datos, function(index,vale) {
                    options.append($("<option />").val(vale.idTitanes).text(vale.cuentaNum + ' - ' +vale.alias));
                });
            });
        } else {
            var options = $("#cuenta");
            options.empty();
            options.append($("<option />").val('').text('Seleccione el Cliente..'));
        }
    });

    $("#cuenta").change(function(){
        var valor = $("#cuenta").val();
        if (valor > 1){
            $.post('datos.php', {
                dato:       11,
                idcuenta:   valor
            }, function(data) {
                var datos = eval('(' + data + ')');
                $("#cuentatipo").val(datos[0].idTipoCuenta).change();
                $("#idtitanes").val(datos[0].idTitanes);
                $('input:radio[name="defecto"]').filter('[value="'+datos[0].isDefault+'"]').attr('checked', true);
                $("#numero").val(datos[0].cuentaNum);
                $("#alias").val(datos[0].alias);
            });
        } else {

        }
    });


});