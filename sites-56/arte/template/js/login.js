/**
 * Funciones relacionadas con el login del usuario real
 */



/**
 * al oprimir el botón Enviar en la página de login si está visible
 * el password funciona logeando al usuario, si no lo está,
 * funciona generando una nueva contraseña con el campo email
 * @returns {undefined}
 */
function postData(){
	$("body").esperaDiv('muestra');
	$.post('iniciof.php', {
		datos	: $("#inputEmailctr").val(),
		fun		: 'genCotr'
	}, function(data){
		var datos = eval('(' + data + ')');
		$("body").esperaDiv('cierra');
		if (datos.error.length > 0) {
			$("#respue_spam").removeClass("label-success").addClass("label-danger").html(datos.error).show().fadeOut(fadeout);
			$("#inputEmail").focus();
		}
		if (datos.data.length > 0) {
			$("#respue_spam").removeClass("label-danger").addClass("label-success").html(datos.data).show();
			$("#olvContr").hide();
			$("#notremember").hide();
			$("#profile-name").hide();
			$("#remember").hide();
			$("#inputPassword").show();
			$("#reauth-email").hide();
			$("#inputEmail").show().val($("#inputEmailctr").val());
			$("#inputEmailctr").hide();
			$("#entrada2").hide();
			$("#entrada1").show();
		}
	});
//	}
}

function postSaveData(r){
	SaveLocalStorageData("PROFILE_IMG_SRC", "images/artista/"+divideCad(r,2)+"/profile.jpeg" );
    SaveLocalStorageData("PROFILE_NAME", divideCad(r,1));
    SaveLocalStorageData("PROFILE_REAUTH_EMAIL", divideCad(r,5));
}

