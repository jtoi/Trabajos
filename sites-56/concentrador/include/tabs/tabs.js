

function mouseOv(elemento) {
	var elementos;
	var x;
	elementos = document.getElementById('renglon').getElementsByTagName('TD');
	for (x=0; x<elementos.length;x++) {
		if (document.getElementById(elementos[x].id).className != 'tabs_active') {
			document.getElementById(elementos[x].id).className="tabs";
		}
	}
	if (elemento.className != "tabs_active") {
		elemento.className="tabs_hover";
	}
}

function mouseCl(elemento) {
	var elementos, tablas;
	var x;
	if (elemento.className != "tabs_active") {
		elementos = document.getElementById('renglon').getElementsByTagName('TD');
		for (x=0; x<elementos.length;x++) {
			if (elemento.id == elementos[x].id){
				document.getElementById(elementos[x].id).className="tabs_active";
			}else {
				document.getElementById(elementos[x].id).className="tabs";
			}
		}
		
		tablas = document.getElementById('nido').getElementsByTagName('TABLE');
		for (x=0; x<tablas.length;x++) {
			if (tablas[x].id.length > 0) {
				if (elemento.id.indexOf(tablas[x].id) > 0){
					document.getElementById(tablas[x].id).style.display="";
				} else {
//				alert(tablas[x].id+' - '+tablas[x].id.indexOf('Buttons'));
					if (tablas[x].id.indexOf('Buttons') == -1) {
						document.getElementById(tablas[x].id).style.display="none";
					}
				}
			}
		}
	}
}

function mouseOu(elemento) {
	if (elemento.className == 'tabs_hover') {
		elemento.className = 'tabs';
	}
}