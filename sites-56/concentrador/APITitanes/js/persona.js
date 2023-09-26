

$(document).ready(function () {
    
    cambiaTipo(1); 

    $("#lactivExtr").hide()
    $("#activExtr").hide().val('')

    $("#tipo").change(function () {
        cambiaTipo(this.value);
    });

    $("#activ").change(function(){
        if ($("#activ").val() == 31){
            $("#lactivExtr").show()
            $("#activExtr").show().val('')
        } else{
            $("#lactivExtr").hide()
            $("#activExtr").hide().val('')
        }
    });

    $("#activ").change(function () {
        if (this.value == '31') {
            $("#escpe").show();
        } else {
            $("#activExtr").val('');
            $("#escpe").hide();
        }
    })

    $.post('datos.php', {
        dato: '1'
    }, function (data) {
        var datos = eval('(' + data + ')');
        var options = $("#activ");
        options.empty();
        $.each(datos, function (index, vale) {
            options.append($("<option />").val(vale.Id).text(vale.Actividad));
        });
    });

    $.post('datos.php', {
        dato: '2'
    }, function (data) {
        var datos = eval('(' + data + ')');
        var options = $("#paiso");
        options.empty();
        $.each(datos, function (index, vale) {
            options.append($("<option />").val(vale.Iso2).text(vale.Nombre));
        });
    });

    $.post('datos.php', {
        dato: '2'
    }, function (data) {
        var datos = eval('(' + data + ')');
        var options = $("#paise");
        options.empty();
        $.each(datos, function (index, vale) {
            options.append($("<option />").val(vale.Iso2).text(vale.Nombre));
        });
    });

    $.post('datos.php', {
        dato: '4'
    }, function (data) {
        var datos = eval('(' + data + ')');
        var options = $("#tdoc");
        options.empty();
        $.each(datos, function (index, vale) {
            options.append($("<option />").val(vale.Id).text(vale.Nombre));
        });
    });
})