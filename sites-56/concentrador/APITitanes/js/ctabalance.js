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
            AccountId:          $("#idtitanes").val(),
            func:               'balanceCuenta'
        },function (data) {
            var datos = eval('(' + data + ')');
            if(datos.error.length > 3) alert(utf8Decode(datos.error))
            else alert("La Cuenta con Id: "+datos.pase[0]+" tiene los siguientes balances:\nBalance: "+datos.pase[1]+"\nBalance Disponible: "+datos.pase[2]+"\nBalance Pendiente Cr\u00e9dito: "+datos.pase[3]+"\nBalance Pendiente de D\u00e9bito: "+datos.pase[1]);
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
                $("#idtitanes").val(datos[0].idTitanes);
                $("#numero").val(datos[0].cuentaNum);
                $("#alias").val(datos[0].alias);
            });
        } else {

        }
    });

});