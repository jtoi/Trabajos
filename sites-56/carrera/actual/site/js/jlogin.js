/**
 * jquery for login
 */
$(document).ready(function(){

$("#go").bind("click", function(){

	var usr= $("#user").val();
	var pas = $("#pwd").val();
	if(usr == ""){

		$("#output").text("No ha escrito un nombre de usuario.").fadeIn().fadeOut(3000);
		$("#user").focus();
	
	}else if(pas == ""){
		$("#output").html("No ha escrito una contrase&ntilde;a para el usuario ["+usr+"].").fadeIn().fadeOut(3000);
		$("#pwd").focus();	
	}else{
		$("#output").load("check.php",{usern: $("#user").val(),pass: $("#pwd").val()},
				function(result){
				$("#output").html(result).fadeIn().fadeOut(2000,
					function(){
						if(nlog) document.location="villa14.php";
					});
		});

	}
});

});
