$("#persona").change(function(){
    $("#contacto").val('');
    $("#alias").val('');
    $.post('datos.php',{
        idPersona: $("#persona").val(),
        dato:	'12'
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#alias");
        options.empty();
        options.append($("<option selected='selected' />").val('').text('Seleccione..'));
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.Id).text(vale.alias));
        });
    });
});

$("#alias").change(function(){
    $("#contacto").val('');
    $("#idtitanes").val("");
    $("#ContactMethodAlias").val("");
    if ($("#metodo").val() != ''){
        $.post('datos.php',{
            idPersona: $("#persona").val(),
            idmetodo: $("#metodo").val(),
            dato: '12'
        },function(data){
            var datos = eval('(' + data + ')');
            $("#idtitanes").val(datos[0].idTitanes);
            $("#contacto").val(datos[0].dato);
            $("#ContactMethodAlias").val(datos[0].alias);
            $("#ContactMethodTypeId").val(datos[0].IdMetodoContacto);
            if (datos[0].isdefault == 1){
                $("[name=defecto][value=true]").prop('checked', true);
            } else {
                $("[name=defecto][value=false]").prop('checked', true);
            }
        });
    }
});

function validateEmail(email) {
  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test( email );
}
function validateTelf(telf) {
  var telfReg = /[0-9\-\(\)\s]+/;
  return telfReg.test( telf );
}

function envia() {
    if ($("#metodo").val() == 3) {
        if (!validateEmail($("#contacto").val())) {
            alert("Email is not valid");
            $("#contacto").focus();
            return false
        }
    } else if ($("#metodo").val() == 1 || $("#metodo").val() == 2) {
        if (!validateTelf($("#contacto").val())) {
            alert("Phone number is not valid");
            $("#contacto").focus();
            return false
        }
    }

    esperafn();
    $.post("datos.php", {
        PersonId: $("#persona").val(),
        ContactMethodTypeId: $("#ContactMethodTypeId").val(),
        ContactMethodValue: $("#contacto").val(),
        ContactMethodAlias: $("#ContactMethodAlias").val(),
        IsContactMethodDefault: $('select[name=defecto] :selected').val(),
        idTitanes: $("#idtitanes").val(),
        func: 'updateContact'
    }, function (data) {
        data = data.replace('[]', '');
        var datos = eval('(' + data + ')');
        console.log(data);
        console.log(datos.pase);
        console.log(datos.error)
        if (datos.error.length > 3) alert(utf8Decode(datos.error));
        else alert("El contacto ha sido correctamente inscrito con Id: " + datos.pase);
        esperafn();
    });
}

$(document).ready(function(){
   
    $("#contacto").val('');
    $("#alias").val('');
    $.post('datos.php',{
        tipo: 'cont',
        dato:	'6',
        beneficiario: 0
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#persona");
        options.empty();
        options.append($("<option />").val('').text('Select a person..'));
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
        });
    });
   
});
