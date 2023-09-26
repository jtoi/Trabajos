function envia(){
    if ($("#metodo").val() == 3){
        if (!validateEmail($("#contacto").val())){
            alert("Email is not valid");
            $("#contacto").focus();
            return false
        }
    } else if($("#metodo").val() == 1 || $("#metodo").val() == 2){
        if (!validateTelf($("#contacto").val())) {
            alert("Phone number is not valid");
            $("#contacto").focus();
            return false
        }
    } else if ($("#alias").val().length < 3 || $("#alias").val().length > 100) {
        alert("El alias no es v\u00e1lida.");
        $("#alias").focus();
        return false
    } 

    esperafn();
    $.post("datos.php", {
        PersonId:                   $("#persona").val(),
        ContactMethodTypeId:        $("#metodo").val(),
        ContactMethodValue:         $("#contacto").val(),
        ContactMethodAlias:         $("#alias").val(),
        IsContactMethodDefault:     $('select[name=defecto] :selected').val(),
        dato:                       26,
        func:                       'setContacto'
    }, function(data){
		data = data.replace('[]','');
        var datos = eval('(' + data + ')');
		console.log(data);
		console.log(datos.pase);
		console.log(datos.error)
        if(datos.error.length > 3) alert(utf8Decode(datos.error));
        else alert("El contacto ha sido correctamente inscrito con Id: "+datos.pase);
        esperafn();
    });
}
function validateEmail(email) {
  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test( email );
}
function validateTelf(telf) {
  var telfReg = /[0-9\-\(\)\s]+/;
  return telfReg.test( telf );
}

$(document).ready(function(){
   
   $.post('datos.php',{
       dato:	18
   },function(data){
       var datos = eval('(' + data + ')');
       var options = $("#persona");
       options.empty();
       $.each(datos, function(index,vale) {
           options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
       });
   });
   
   $.post('datos.php',{
        idPersona:1,
        dato:	'7'
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#metodo");
        options.empty();
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.Id).text(vale.nombre));
        });
    });

});

