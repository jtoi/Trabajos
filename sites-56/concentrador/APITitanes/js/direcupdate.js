function envia(){
    pase = 1;

    if ($("#direccion").val().length < 3 || $("#direccion").val().length > 100){
        alert("La direcci\u00f3n no es v\u00e1lida.");
        $("#direccion").focus();
        pase = 0;
    } else if ($("#ciudad").val().length < 3 || $("#ciudad").val().length > 60) {
        alert("La ciudad no es v\u00e1lida.");
        $("#ciudad").focus();
        pase = 0;
    } else if ($("#postalcod").val().length < 3 || $("#postalcod").val().length > 15) {
        alert("El c\u00f3digo postal no es v\u00e1lida.");
        $("#postalcod").focus();
        pase = 0;
    } else if ($("#provincia").val().length < 3 || $("#provincia").val().length > 30) {
        alert("La provincia no es v\u00e1lida.");
        $("#provincia").focus();
        pase = 0;
    } else if ($("#alias").val().length > 100) {
        alert("El alias de la direcci\u00f3n no es v\u00e1lida.");
        $("#alias").focus();
        pase = 0;
    } 

    if (pase == 1){
		esperafn();
        $.post("datos.php", {
            PersonId:               $("#persona").val(),
            Address:                $("#direccion").val(),
            City:                   $("#ciudad").val(),
            PostalCode:             $("#postalcod").val(),
            Province:               $("#provincia").val(),
            AddressISOCountryCode:  $("#pais").val(),
            AddressAlias:           $("#alias").val(),
            IsAddressDefault:       $('select[name=defecto] :selected').val(),
            update:                 $("#idtitanes").val(),
            func:                   'updDirec'
        }, function(data){
            var datos = eval('(' + data + ')');
            if(datos.error.length > 3) alert(utf8Decode(datos.error));
            else alert("La direcci\u00f3n ha sido correctamente actualizada con Id: "+datos.pase);
            esperafn();
        });
    }
}

$(document).ready(function(){
   
   $.post('datos.php',{
        tipo:'dir',
        dato:	'6'
   },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#persona");
        options.empty();
        options.append($("<option />").val('').text('Seleccionar persona'));
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
        });
   });
   
    //carga los alias de las direcciones de la persona escogida
    $("#persona").change(function(){
        if ($("#persona").val().length > 0) {
    
            $.post('datos.php', {
                id: $("#persona").val(),
                dato: '16'
            }, function(data){
                var datos = eval('(' + data + ')');
                var options = $("#otroAlias");
                options.empty();
                options.append($("<option />").val('').text('Seleccionar direcci\u00f3n a cambiar'));
                $.each(datos, function(index,vale) {
                    options.append($("<option />").val(vale.Id).text(vale.Alias));
                });
            });
        } else {
            $("#direccion").val('');
            $("#ciudad").val('');
            $("#postalcod").val('');
            $("#provincia").val('');
            $("#pais").val('');
            $("#alias").val('');
            $("#idtitanes").val('');
            alert("no hay persona seleccionada");
        }
    });

    //carga datos de la direccion escogida
    $("#otroAlias").change(function(){
        if ($("#otroAlias").val().length > 0) {
            $.post('datos.php', {
                id: $("#otroAlias").val(),
                dato: '17'
            }, function(data){
                var datos = eval('(' + data + ')');
                datos = datos[0];
                $("#direccion").val(datos.Direccion);
                $("#ciudad").val(datos.Ciudad);
                $("#provincia").val(datos.Provincia);
                $("#alias").val(datos.Alias);
                $("#idtitanes").val(datos.IdTitanes);
                $("#postalcod").val(datos.CP);
                var pais = datos.Iso2;

                $.post('datos.php',{
                    dato:	'2'
                },function(data){
                    var datos = eval('(' + data + ')');
                    var options = $("#pais");
                    options.empty();
                    $.each(datos, function(index,vale) {
                        if (vale.Iso2 == pais)
                            options.append($("<option selected='selected' />").val(vale.Iso2).text(vale.Nombre));
                        else
                            options.append($("<option />").val(vale.Iso2).text(vale.Nombre));
                    });
                });

                if (datos.IsDefault == 1){
                    $("[name=defecto][value=true]").prop('checked', true);
                } else {
                    $("[name=defecto][value=false]").prop('checked', true);
                }
                
            });
        } else {
            $("#direccion").val('');
            $("#ciudad").val('');
            $("#postalcod").val('');
            $("#provincia").val('');
            $("#pais").val('');
            $("#alias").val('');
            $("#idtitanes").val('');
            alert("no hay alias seleccionada");
        }
    });
   
   $.post('datos.php',{
        dato:	'2'
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#pais");
        options.empty();
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.Iso2).text(vale.Nombre));
        });
    });

})