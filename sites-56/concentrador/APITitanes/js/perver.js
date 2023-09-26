
function envia() {
    esperafn();
    if ($("#nombre").val() == '') nombre = '%';
    else nombre = $("#nombre").val();
    $.post('datos.php', {
        nom: nombre,
        ben: $("#ben").val(),
        tipo: $("#tipo").val(),
        dato: '31'
    }, function (data) {
        var datos = eval('(' + data + ')');
        var texto = '';
        $("#cont tbody").html('');
        for (i=0; i<datos.length; i++){
            $("#cont tbody").append('<tr><td><a href="index.php?var=persummary&id='+datos[i].tit+'">'+ datos[i].persona +'</a></td><td>'+ datos[i].tipo +'</td><td>'+ datos[i].ben +'</td></tr>');
        }
    });
    esperafn();
}

// console.log(document.cookie.replace("PHPSESSID=",''));

$(document).ready(function () {

    $.post('datos.php',{
        func: 'getCode'
    }, function(data){
        var datos = eval('(' + data + ')');
        if (datos.code){
            $("#code").val(datos.code);
        }
    });

    $.post('datos.php', {
        dato: '30'
    }, function (data) {
        var datos = eval('(' + data + ')');
        $("#totPer").html(datos[0].personas);
        $("#totCli").html(datos[0].cliente);
        $("#totBen").html(datos[0].beneficiario);
    });

});


