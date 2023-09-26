function envia(){
    var cli = $("#persona").val();
    var ben = $("#beneficiario").val();
    var tre = $("#trel").val();
    if (tre > 0 && ben > 0 && cli > 0) {
    	esperafn();
        $.post("datos.php", {
            PersonId:           cli,
            RelatedPersonId:    ben,
            RelatedTypeId:      tre,
            func:               'setRelation'
        },function(data){
            var datos = eval('(' + data + ')');
            if(datos.error.length > 3) alert(utf8Decode(datos.error))
            else alert("La Persona ha sido correctamente actualizada con IdTitanes: "+datos.pase);
            esperafn();
        });
    }
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
   });
   
    $("#persona").attr("placeholder", "one");

   $.post('datos.php',{
       dato:	'6',
       beneficiario: 1
   },function(data){
       var datos = eval('(' + data + ')');
       var options = $("#beneficiario");
       options.empty();
       $.each(datos, function(index,vale) {
           options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
       });
   });
   
   $.post('datos.php',{
       dato:	24
   },function(data){
       var datos = eval('(' + data + ')');
       var options = $("#trel");
       options.empty();
       $.each(datos, function(index,vale) {
           options.append($("<option />").val(vale.Id).text(vale.nombre));
       });
   });
   
});