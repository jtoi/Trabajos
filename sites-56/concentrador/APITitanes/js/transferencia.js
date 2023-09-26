$(document).ready(function () {
    $.post('datos.php', {
        dato: '6',
        tipo: 'cta'
    }, function (data) {
        var datos = eval('(' + data + ')');
        var options = $("#persona");
        options.empty();
        options.append($("<option />").val('').text('Seleccione..'));
        $.each(datos, function (index, vale) {
            options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
        });
    });

    $.post('datos.php', {
        dato: '6',
        tipo: 'cta'
    }, function (data) {
        var datos = eval('(' + data + ')');
        var options = $("#beneficiario");
        options.empty();
        options.append($("<option />").val('').text('Seleccione..'));
        $.each(datos, function (index, vale) {
            options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
        });
    });

    $("#persona").change(function () {
        if ($(this).val() > 1) {
            $.post("datos.php", {
                dato: 5,
                idpersona: $(this).val()
            }, function (data) {
                var datos = eval('(' + data + ')');
                $("#idtitanes").val(datos.idTitanes);

                var options = $("#cuenta");
                options.empty();
                options.append($("<option />").val('').text('Seleccione la cuenta..'));
                $.each(datos, function (index, vale) {
                    options.append($("<option />").val(vale.idTitanes).text(vale.cuentaNum + ' - ' + vale.alias));
                });
            });
        } else {
            var options = $("#cuenta");
            options.empty();
            options.append($("<option />").val('').text('Seleccione el Cliente..'));
        }
    });

    $("#beneficiario").change(function () {
        var selectedValue = $(this).val();
        var options = $("#cuentad");
        
        if (selectedValue > 1) {
            $.post("datos.php", {
                dato: 5,
                idpersona: selectedValue
            }, function (data) {
                var datos = JSON.parse(data);
                $("#idtitanes").val(datos.idTitanes);
                
                options.empty();
                options.append($("<option />").val('').text('Seleccione la cuenta..'));
                
                $.each(datos, function (index, vale) {
                    options.append($("<option />").val(vale.idTitanes).text(vale.cuentaNum + ' - ' + vale.alias));
                });
            });
        } else {
            options.empty();
            options.append($("<option />").val('').text('Seleccione el Cliente..'));
        }
    });

    $("#cuenta").change(function(){
        revisaCta()
    });

    $("#cuentad").change(function(){
        revisaCta()
    });



    $.post('datos.php', {
        dato: '28',
        beneficiario: 0
    }, function (data) {
        var datos = eval('(' + data + ')');
        var options = $("#moneda");
        options.empty();
        $.each(datos, function (index, vale) {
            options.append($("<option />").val(vale.nombre).text(vale.nombre));
        });
    });

    $("#cuenta").change(function () {
        $.post("datos.php", {
            dato: 29,
            cta: $(this).val()
        }, function (data) {
            var datos = eval('(' + data + ')');
            var options = $("#monedad");
            options.empty();
            $.each(datos, function (index, vale) {
                options.append($("<option />").val(vale.nombre).text(vale.nombre)); 
            });
        })
    });
});

function revisaCta(){
    if ($("#cuentad").val() == $("#cuenta").val()){
        alert (utf8Decode("La cuenta de origen de la operación y la de destino no pueden ser las mismas"));
        $("#cuentad").attr("selected", true);
        return false;
    } return true;
}

function envia() {
    var mto = $("#monto").val();
    // var mtd = $("#montod").val();
    // var tas = $("#tasa").val();

    var pase = revisaCta();


    if (mto > 0) {
        esperafn();
        $.post("datos.php", {
            TransactionType: 8, 
            PaymentInstrument: 10,
            Completed: 1,
            Amount: mto,
            OriginPersonId: $("#persona :selected").val(),
            OriginAccountId: $("#cuenta :selected").val(),
            DestinationPersonId: $("#beneficiario :selected").val(),
            DestinationAccountId: $("#cuentad :selected").val(),
            OriginCurrency: $("#monedad :selected").val(),
            Concept: $("#concepto").val(),
            func: 'setTransaccion'
        }, function (data) {
            var datos = eval('(' + data + ')');
            if (datos.error.length > 3) alert(utf8Decode(datos.error))
            else alert(utf8Decode("La Transacción ha sido correctamente realizada con IdTitanes: " + datos.pase));
            esperafn();
        });
    }
}