
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
    } else if ($("#alias").val().length < 3 || $("#alias").val().length > 100) {
        alert("El alias no es v\u00e1lida.");
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
            dato:                   26,
            func:                   'creaDirec' 
        }, function(data){
			data = data.replace('[]','');
            var datos = eval('(' + data + ')');
            if(datos.error.length > 3) alert(utf8Decode(datos.error));
            else alert("La direcci\u00f3n ha sido correctamente inscrita con Id: "+datos.pase);
            esperafn();
        });
    }
}

const cambiapais=(val)=>{
    $.post('datos.php',{
        dato:	'27',
        iso2: val
    },function(data){
        var datos = eval('(' + data + ')');
        var options = $("#pais");
        options.empty();
        $.each(datos, function(index,vale) {
            options.append($("<option />").val(vale.Iso2).text(vale.Nombre));
        });
    });
}

$(document).ready(function(){
   
   $.post('datos.php',{
       dato:	'6',
       beneficiario: 0
   },function(data){
       var datos = eval('(' + data + ')');
       var options = $("#persona");
       options.empty();
       $.each(datos, function(index,vale) {
           options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
       });
       cambiapais($("#persona :selected").val());
   });

   $("#persona").change(function (e) {
        cambiapais($(this).val())
   });
   
//    $.post('datos.php',{
//         dato:	'2'
//     },function(data){
//         var datos = eval('(' + data + ')');
//         var options = $("#pais");
//         options.empty();
//         $.each(datos, function(index,vale) {
//             options.append($("<option />").val(vale.Iso2).text(vale.Nombre));
//         });
//     });

})