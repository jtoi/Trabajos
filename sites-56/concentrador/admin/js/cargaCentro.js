var pag=0;


function pagina(avance, max) {
	if (avance == 1) pag=pag+1; else pag=pag-1;
	
	if (pag == max && document.getElementById('next')) document.getElementById('next').style.display='none'; else document.getElementById('next').style.display='';
	if (pag == 0 && document.getElementById('back')) document.getElementById('back').style.display='none'; else document.getElementById('back').style.display='';

	for (i=0;i<=max;i++) {
		document.getElementById('salida'+i).style.display='none';
	}
	document.getElementById('salida'+pag).style.display='';
}

//xajax_cargaTiempo('regionCentral');
xajax_evaluaUrl('comercio&inicio');

