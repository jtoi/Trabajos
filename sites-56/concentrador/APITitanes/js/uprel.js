function envia() {
    var cli = $("#persona").val();
    var ben = $("#beneficiario").val();
    var tre = $("#trel").val();
    var idt = $("#idtitanes").val();
    if (tre > 0 && ben > 0 && cli > 0 && idt > 0) {
        esperafn();
        $.post("datos.php", {
            PersonId: cli,
            RelatedPersonId: ben,
            RelatedTypeId: tre,
            idRel: idt,
            func: 'updateRelation'
        }, function (data) {
            var datos = eval('(' + data + ')');
            if (datos.error.length > 3) alert(utf8Decode(datos.error))
            else alert("La Persona ha sido correctamente actualizada con IdTitanes: " + datos.pase);
            esperafn();
        });
    }
}

$(document).ready(function () {

    $.post('datos.php', {
        dato: '6',
        beneficiario: 0
    }, function (data) {
        var datos = eval('(' + data + ')');
        var options = $("#persona");
        options.empty();
        options.append($("<option />").val('').text('Seleccione..'));
        $.each(datos, function (index, vale) {
            options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
        });
    });

    $("#persona").change(function () {
        $.post('datos.php', {
            idTitanes:      $("#persona").val(),
            beneficiario:   $("#beneficiario").val(),
            dato:           8
        }, function (data) {
            var datos = eval('(' + data + ')');
            var options = $("#beneficiario");
            options.empty();
            options.append($("<option />").val('').text('Seleccione..'));
            $.each(datos, function (index, vale) {
                options.append($("<option />").val(vale.IdTitanes).text(vale.beneficiario));
            });
        });
        // cambiaRel();
    });

    $("#persona").attr("placeholder", "one");
    $("#beneficiario").change(function(){cambiaRel();});


    $.post('datos.php', {
        dato: 24
    }, function (data) {
        var datos = eval('(' + data + ')');
        var options = $("#trel");
        options.empty();
        $.each(datos, function (index, vale) {
            options.append($("<option />").val(vale.Id).text(vale.nombre));
        });
    });

});

const cambiaRel=()=>{
    if ($("#beneficiario").val() > 1 && $("#persona").val() > 1) {
        $.post('datos.php', {
            dato: 8,
            idTitanes: $("#persona").val(),
            idBen: $("#beneficiario").val(),
        }, function (data){
            var datos = eval('(' + data + ')');
            $("#trel").val(datos[0].idTipo);
            $("#idtitanes").val(datos[0].idTitanes);
        });
    }
}