$(document).ready(function(){
   
    $.post('datos.php',{
        dato:	'6'
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#persona");
        options.empty();
        options.append($("<option />").val('').text('Seleccione..'));
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
        });
    });

    $("#envia").on('click', function () {
        var pase = 1;
        if ($("#persona").val() == '') {
            alert('Seleccione la Persona');
            $("#persona").focus();
            pase = 0;
        }

        if (pase == 1) {
            $.post('datos.php',{
                persona:    $("#persona").val(),
                dato:       23
            },function(data){
                var datos = eval('(' + data + ')');
                console.log(datos);
                alert("A esta Persona le falta(n) el(los) siguiente(s) documento(s):  "+datos.pase);
            });
        }

    });


});