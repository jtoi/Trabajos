function imprimirArbol(array) {

    var linea = "<div class='linea linea$esp$'><div class='clave'>$clave$: </div><div class='valor'>$valor$</div></div>";
    var keys = Object.keys(array);

    for (var i = 0; i < keys.length; i++) {
        if (keys[i].toLowerCase().indexOf('personid') >= 0) {
            array[keys[i]] = "<span class='enla' onClick='envia(" + array[keys[i]] + ")'>" + array[keys[i]] + "</span>";
        }

        if (Array.isArray(array[keys[i]])) {

            var lineas = linea.replace('$clave$', keys[i]).replace('$valor$', ' ').replace('$esp$', esp);
            $("#todomas").append(lineas)
            if (array[keys[i]][0] != null) {
                esp++;
                imprimirArbol(array[keys[i]][0], esp);
            } else {
                if (esp > 1) esp--;
            }
        } else {
            var lineas = linea.replace('$clave$', keys[i]).replace('$valor$', array[keys[i]]).replace('$esp$', esp);
            $("#todomas").append(lineas)
        }
    }
    if (esp > 1) esp--;
}


var esp = 1;


function envia(perId = '') {
    $("#todomas").html('');
    esperafn();
    if (perId == '') perId = $("#persona").val()
    $.post("datos.php", {
        PersonId: perId,
        func: 'personSummary'
    }, function (data) {
        var datos = eval('(' + data + ')');
        // var entrada = datos.entrada[0];
        var salida = datos.salida;

        if (typeof datos.error !== 'undefined' && datos.error !== null) alert(utf8Decode(datos.error));
        else {
            // imprimirArbol(datos.salida.data);
            $("#todomas").append(utf8Decode(datos.salida));
            $(".formul").hide();
        }
        esperafn();
    });
}

$(document).ready(function () {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    if (urlParams.get('id')) {
        envia(urlParams.get('id'))
    }

    $.post('datos.php', {
        dato: '6'
    }, function (data) {
        var datos = eval('(' + data + ')');
        var options = $("#persona");
        options.empty();
        $.each(datos, function (index, vale) {
            options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
        });
    });

});


