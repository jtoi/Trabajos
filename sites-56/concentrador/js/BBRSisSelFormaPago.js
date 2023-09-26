/****************************************************************************************************
* VARIABLES contenidas en RSisSelFormaPago.js :					COMUN 
*****************************************************************************************************/

// Para la traduccion de los errores. 
// Ver: anadirLiteral(etiqueta, valor)
//      getError(etiqueta)
var errores = new Array();

// Indica el número de formas de pago disponibles que hay en la página,
// seleccionables a través de radio buttons
var numFormasPago = 0;

// Almacena el id de sesión, necesario para las peticiones a los servlets
var sessionId = '';

// Almacena la ruta de contexto (/sis o /tpvv)
var contextPath = '/sis';

// Indica si se están enviando datos o no
var enviandoDatos = false;

// Almacena la forma de pago seleccionada
var formaPagoSel = '';

// Almacena los datos de una petición AJAX
var req;

/****************************************************************************************************
* FUNCIONES contenidas en RSisSelFormaPago.js :										
* 											 										
* [Ref0001]:	anadirLiteral(etiqueta, valor)	Creamos una referencia v2 en utilSis
* [Ref0002]:	getError(etiqueta)					Creamos una referencia v2 en utilSis
* [Ref0003]:	fijarNumFormasPago(valor)		
* [Ref0004]:	fijarDatosSesion(idSesion, raizContexto)
* [Ref0005]:	validar()	
* [Ref0006]:	solicitaDatosAJAX()		
* [Ref0007]:	respuestaConsultaAJAX()
* [Ref0013]:	cancelar()
*
*****************************************************************************************************/


/************************************************************
* 												[Ref0001]	
* Funcion: anadirLiteral								
* Parametros:												
*		etiqueta
*		valor													
* Funcion que guarda en una matriz los valores de los errores, el texto y los 
*	literales asociados a los mismos. Se hace una sola vez en la carga												
************************************************************/
function anadirLiteral(etiqueta, valor) {
	var error  = new Array();
	error[0]= etiqueta;
	error[1]= valor;
	errores[errores.length] = error;
}


/************************************************************
* 												[Ref0002]	
* Funcion: getError								
* Parametros:												
*		etiqueta													
* Devuelve:	el literal asociado la operacion seleccionada												
************************************************************/
function getError(etiqueta) {
	var noencontrado=true;
	var i =0;
	while(noencontrado &&  i < errores.length ) {
		if (errores[i][0] == etiqueta)
			return errores[i][1];
		i++;
	}
	return "";
}


/************************************************************
* 												[Ref0003]	
* Funcion: fijarNumFormasPago								
* Parametros:												
*		valor													
* Fija el número de formas de pago disponibles que hay en la página,
*	seleccionables a través de radio buttons
************************************************************/
function fijarNumFormasPago(valor) {
	numFormasPago = valor;
}


/************************************************************
* 												[Ref0004]	
* Funcion: fijarDatosSesion								
* Parametros:												
*		idSesion : Valor de sessionId
*		raizContexto : Valor de contextPath
* Fija el valor de sessionId para reescritura de URLs, y el valor de
* la raíz de contexto
************************************************************/
function fijarDatosSesion(idSesion, raizContexto) {
	sessionId = idSesion;
	contextPath = raizContexto;
}


/************************************************************
* 												[Ref0005]	
* Funcion: validar
* Devuelve:
* Valida el formulario de datos y realiza las acciones necesarias
************************************************************/
function validar()	{
	if (!enviandoDatos) {
		formaPagoSel = '';
		var i = 0;
		for(i = 0; i < numFormasPago; i++) {
			if (document.formModalidad.Ds_Merchant_PayMethod[i].checked) {
				formaPagoSel = document.formModalidad.Ds_Merchant_PayMethod[i].value;
			}
		}
		if (formaPagoSel == '') {
			alert(getError("msg16"));
			return;
		} else {	
			if (formaPagoSel == 'O') {
				// Para el pago Oasys lo trataremos como una llamada AJAX al SIS
				ocultaBoton();
				solicitaDatosAJAX();
			} else {
				ocultaBoton();
				fProcesandoPeticion('S');
				enviandoDatos = true;
				document.formModalidad.submit();
			}
		}
	}
}


/************************************************************
* 												[Ref0006]	
* Funcion: solicitaDatosAJAX
* Devuelve:
* Solicita al SIS los datos de un método de pago a través de AJAX
************************************************************/
function solicitaDatosAJAX() {
	if (!enviandoDatos) {
		var url = contextPath + "/formaPagoAJAX" + sessionId;
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
		} else if (window.ActiveXObject) {
			req = new ActiveXObject("Microsoft.XMLHTTP");
		}

		req.open("POST", url, true);
		
		req.onreadystatechange = respuestaConsultaAJAX;
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send("Ds_Merchant_PayMethod=" + formaPagoSel);
	}
}


/************************************************************
* 												[Ref0007]	
* Funcion: respuestaConsultaAJAX
* Devuelve:
* Ejecutada una vez que hay respuesta de una llamada AJAX
************************************************************/
function respuestaConsultaAJAX() {
	// Valores de "readyState"
	// 0: request not initialized
	// 1: server connection established
	// 2: request received
	// 3: processing request
	// 4: request finished and response is ready
	
	if (req.readyState == 4) {
		var hayError = true;

		if (req.status == 200) {
			if (formaPagoSel == 'O') {
				var urlOasys = dameValorElemento(req.responseXML, "urlOasys");
				var petOasys = dameValorElemento(req.responseXML, "petOasys");
				if (urlOasys && urlOasys != '' && petOasys && petOasys != '') {
					// Se reenvía a Oasys
					hayError = false;
					document.frmOASYS.dato.value = petOasys;
					document.frmOASYS.action = urlOasys;
					ocultaBoton();
					fProcesandoPeticion('S');
					enviandoDatos = true;
					document.frmOASYS.submit();
				}
			}

			if (formaPagoSel == 'V') {
				var urlVme = dameValorElemento(req.responseXML, "urlVme");
				if (urlVme && urlVme != '') {
				  var paramsVme = urlVme.substring(urlVme.indexOf("?"));
          var sessionIdVme = paramsVme.substring(paramsVme.indexOf("=")+1);
					// Se reenvía a Vme
					hayError = false;
					document.frmVme.action = urlVme;
					document.frmVme.sessionId.value = sessionIdVme;
					ocultaBoton();
					fProcesandoPeticion('S');
					enviandoDatos = true;
					document.frmVme.submit();
				}
			}

			if (formaPagoSel == 'S') {
				var urlSafetyPay = dameValorElemento(req.responseXML, "urlSafetyPay");
				
				if (urlSafetyPay && urlSafetyPay != '') {
				  var paramsSafetyPay = urlSafetyPay.substring(urlSafetyPay.indexOf("?"));
          var TokenID = paramsSafetyPay.substring(paramsSafetyPay.indexOf("=")+1);

					// Se reenvía a SafetyPay
					hayError = false;
					document.frmSAFETYPAY.action = urlSafetyPay;
					ocultaBoton();
					fProcesandoPeticion('S');
					enviandoDatos = true;
					document.frmSAFETYPAY.submit();
				}
			}

		}
		
		if (hayError) {
			muestraBoton();
			alert(getError("msg15"));
			return;
		}
	}
}


/************************************************************
* 												[Ref0013]	
* Funcion: cancelar								
* Devuelve:	
* Funcion para el botón de Cancelar para que llame y genere una 
* excepción con código SIS9915 -- 0915												
************************************************************/
function cancelar(){
		ocultaBoton();
		fProcesandoPeticion('S');
		document.formTarjeta.bcancel.value=1;
		document.formTarjeta.submit();
		
}