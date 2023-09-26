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

function envia() {
    var mto = $("#monto").val();
    // var mtd = $("#montod").val();
    // var tas = $("#tasa").val();
    // alert($("#PaymentInstrument").val());
    if (mto > 0) {
        esperafn();
        $.post("datos.php", {
            TransactionType: 1,
            PaymentInstrument: 11,
            TpvOperationInfo: '',
            Amount: mto,
            DestinationPersonId: $("#persona :selected").val(),
            DestinationAccountId: $("#cuenta :selected").val(),
            OriginCurrency: $("#monedad :selected").val(),
            Concept: $("#concepto").val(),
            func: 'setTransaccion'
        }, function (data) {
            var datos = eval('(' + data + ')');
            if (datos.error.length > 3) alert(utf8Decode(datos.error))
            else alert("La Transacci\u00f3n ha sido correctamente realizada con IdTitanes: " + datos.pase);
            esperafn();
        });
    }
}