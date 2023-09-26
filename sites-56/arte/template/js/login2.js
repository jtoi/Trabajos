/**
 * Funciones relacionadas con el login del usuario
 */

$( document ).ready(function() {
    /*
     * To test the script you should discomment the function
     * testLocalStorageData and refresh the page. The function
     * will load some test data and the loadProfile
     * will do the changes in the UI
     */
	//testLocalStorageData();
    // Load profile if it exits
	$("#notremember").hide();
	$("#olvContr").hide();
	$("#respue_spam").hide();
	$("#renvctr").hide();
    loadProfile();
	
	/**
	 * Si marca la frase entrar como otro usuario
	 * borra todos los datos almacenados para cargar un profile limpio
	 */
	$("#notremember").click(function(){
		$("#respue_spam").html("");
		var arrprof = Array("PROFILE_IMG_SRC","PROFILE_NAME","PROFILE_REAUTH_EMAIL, PROFILE_IDIOMA");
		for (var i = 0; i<arrprof.length; i++){
			removeLocalStorageData(arrprof[i]);
		}
		window.open('index.php','_self');
	});
	
	/**
	 * si marca la frase olvide lña contraseña
	 * genero una nueva
	 */
	$("#olvContr").click(function(){
		$("#olvContr").hide();
		$("#notremember").hide();
		$("#profile-name").hide();
		$("#remember").hide();
		$("#inputPassword").hide();
		$("#reauth-email").hide();
		$("#entrada1").hide();
		$("#inputEmail").hide();
		$("#renvctr").show();
	});
});

/**
 * Function that gets the data of the profile in case
 * thar it has already saved in localstorage. Only the
 * UI will be update in case that all data is available
 *
 * A not existing key in localstorage return null
 *
 */
function getLocalProfile(callback){
	
    profileImgSrc		= readLocalStorageData("PROFILE_IMG_SRC");
    profileName			= readLocalStorageData("PROFILE_NAME");
    profileReAuthEmail	= readLocalStorageData("PROFILE_REAUTH_EMAIL");
    profileIdioma		= readLocalStorageData("PROFILE_IDIOMA");
    
    //alert(profileImgSrc);
//	checkImageExists(profileImgSrc, function(existsImage) {
//		if(existsImage === true) {
//			profileImgSrc;
//		} else {
//			profileImgSrc = 'images/noprofile.png'; 
//		}
//	});
//    if (!verFic(profileImgSrc))
//    	profileImgSrc = 'images/noprofile.png'; 
    
    if(profileName !== null
            && profileReAuthEmail !== null
            && profileImgSrc !== null
            && profileIdioma !== null) {
        callback(profileImgSrc, profileName, profileReAuthEmail, profileIdioma);
    }
}

/**
 * Main function that load the profile if exists
 * in localstorage
 */
function loadProfile() {
	    // we have to provide to the callback the basic
	    // information to set the profile
	    getLocalProfile(function(profileImgSrc, profileName, profileReAuthEmail, profileIdioma) {
	        //changes in the UI
	        $("#notremember").show();
			$("#profile-img").attr("src",doesFileExist(profileImgSrc,"images/noprofile.png"));
			$("#profile-name").html(profileName);
	        $("#reauth-email").html(profileReAuthEmail);
	        $("#hideemail").val(profileReAuthEmail);
	        $("#inputEmail").val(profileReAuthEmail);
	        $("#inputEmail").hide();
	        $("#remember").hide();
			$("#respue_spam").hide();
	    });
}

/**
 * Test data. This data will be safe by the web app
 * in the first successful login of a auth user.
 * To Test the scripts, delete the localstorage data
 * and comment this call.
 *
 * @returns {boolean}
function testLocalStorageData() {
    SaveLocalStorageData("PROFILE_IMG_SRC", "//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" );
    SaveLocalStorageData("PROFILE_NAME", "Julio Toirac");
    SaveLocalStorageData("PROFILE_REAUTH_EMAIL", "jtoirac@gmail.com");
}
 */


