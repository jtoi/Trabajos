/**
 * Maneja la informacion almacenada en la máquina del cliente
 */

/**
 * Chequea si un fichero existe en el server
 * @param string urlToFile dirección del fichero a abrir
 * @param string fileDef dirección del fichero secundario cuando no se encuentre al ppal
 * @returns {@var;string}
 */
function doesFileExist(urlToFile,fileDef='')
{
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();

    if (xhr.status == "404") {
        return fileDef;
    } else {
        return urlToFile;
    }
}

/**
 * Divide una cadena separa con el caracter |
 * devolviendo el string que ocupa el el orden indicado
 * @param {string} cad
 * @param {integer} orden
 * @returns {string}
 */
function divideCad(cad, orden){
	var arr = cad.split("|");
	return arr[orden];
}

/**
 * Verifica la existencia o no de un fichero en el server
 * @string fic
 * @returns
 */
function checkImageExists(imageUrl, callBack) {
	var imageData = new Image();
	imageData.onload = function() {
		callBack(true);
	};
	imageData.onerror = function() {
		callBack(false);
	};
	imageData.src = imageUrl;
}

/**
 * Retorna el valor de la variable guardado en el cliente
 * @param nombre
 * @returns
 */
function readLocalStorageData(nombre){
	var profileVal = null;
	
    if(supportsHTML5Storage()) {
    	profileVal      = localStorage.getItem(nombre);

	} else {
		if (getCookie(nombre) ){
			profileVal      = getCookie(nombre);
		} 
	}
    
    return profileVal;
}


/**
 * function that checks if the browser supports HTML5
 * local storage
 *
 * @returns {boolean}
 */
function supportsHTML5Storage() {
    try {
        return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
        return false;
    }
}

function removeLocalStorageData(nombre){
	if(!supportsHTML5Storage()) setCookie(nombre, '', -7 );
	else localStorage.removeItem(nombre);
	return true;
}

/**
 * Salva los pares variable - valor en la máquina del cliente
 * @param nombre string Nombre de la variable
 * @param valor string Valor de la variable
 * @param tiempo entero Número de días de vida de la variable
 * @returns
 */
function SaveLocalStorageData(nombre, valor, tiempo) {
    if(supportsHTML5Storage()) { 
	    localStorage.setItem(nombre, valor );
    } else {
    	setCookie(nombre, valor, tiempo );
    }
}

/**
 * Escribe el valor de la cookie
 * @param cname Nombre de la cookie
 * @param cvalue Valor de la cookie
 * @param exdays Duración en días
 * @returns
 */
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

/**
 * lee el valor de la cookie
 * @param cname Nombre de la cookie
 * @returns string
 */
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

/**
 * Chequea la existencia de la cookie
 * @returns
 */
function checkCookie() {
    var user = getCookie("username");
    if (user != "") {
        alert("Welcome again " + user);
    } else {
        user = prompt("Please enter your name:", "");
        if (user != "" && user != null) {
            setCookie("username", user, 365);
        }
    }
}

