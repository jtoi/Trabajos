//devuelve el objeto objXmlHttp en dependencia del browser del cliente
function GetXmlHttpObject(handler){
	var objXmlHttp=null
	if (navigator.userAgent.indexOf("Opera")>=0) {
		alert("No trabaja con Opera");
		return
	}
	if (navigator.userAgent.indexOf("MSIE")>=0) {
		var strName="Msxml2.XMLHTTP"
		if (navigator.appVersion.indexOf("MSIE 5.5")>=0) {
			strName="Microsoft.XMLHTTP"
		}
		try	{
			objXmlHttp=new ActiveXObject(strName)
			objXmlHttp.onreadystatechange=handler
			return objXmlHttp
		}
		catch(e) {
			alert("Error. Scripting for ActiveX might be disabled")
			return
		}
	}
	if (navigator.userAgent.indexOf("Mozilla")>=0) {
		objXmlHttp=new XMLHttpRequest()
		objXmlHttp.onload=handler
		objXmlHttp.onerror=handler
		return objXmlHttp
	}
}

// crea el handler para el objeto objXmlHttp
function getagents(params) {
	if (document.getElementById('divCarga')) document.getElementById('divCarga').style.display='inline';
	xmlHttp=GetXmlHttpObject(handleHttpResponse);
	xmlHttp.open("POST","componente/reserva/apoyo.php?" + params, true);
	xmlHttp.send(null);
}

//encuesta el handler del objeto para saber si ya está contruido (estado 4)
//envía la respuesta del objeto al hiddenDIV para que sea mostrado
//apaga el simbolo que indica que la página está cargando
function handleHttpResponse() {
	if (xmlHttp.readyState == 4) {
		entrada(xmlHttp.responseText);
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function facil(params){
		paso = params.paso.value;
		cant = params.cant.value;
		fecha1 = params.fecha1.value;
		fecha2 = params.fecha2.value;
		prod = params.prod.value;
		prec = params.precio.value;
//		caract = params.caract;
//		caraVal = Array();
//		if (caract) {
//			for (xi=0;xi<caract.length;xi++){
//				if (caract[xi].selected) caraVal[xi] = caract[xi].value;
//			}
//		}
//		params = "paso="+paso+"&cant="+cant+"&fecha1="+fecha1+"&fecha2="+fecha2+"&prod="+prod+"&caract="+caraVal;
		params = "paso="+paso+"&cant="+cant+"&fecha1="+fecha1+"&fecha2="+fecha2+"&prod="+prod+"&precio="+prec;
		getagents(params);
}



