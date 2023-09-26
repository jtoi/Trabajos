$(document).ready(function(){
    cambiaTipo(1);
    
    $("#tipo").change(function(){
        cambiaTipo(this.value);
    });
   
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