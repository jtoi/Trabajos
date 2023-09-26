<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$dato = 1;
$d = $_REQUEST;
$temp = new ps_DB;

if ($borra = $ent->isBoolean($d['borrar'])) {
	
	$query = "select idProd, fechaIni, fechaFin, cant from tbl_productosReserv where id = $borra";
	echo "$query<br>";
	$temp->query($query);

	$prod = $temp->f('idProd');
	$fecha2 = $temp->f('fechaFin');
	$fecha = $fecha1 = $temp->f('fechaIni');
	$cant = $temp->f('cant');
	$id = $borra;
	$cantCheq = $idCant = 0;
	$paso = false;
	while ($fecha <= $fecha2) {
		$query = "select id, cant from tbl_productosCant where idProd = ".$prod." and fechaIni <= $fecha and fechaFin >= $fecha";
//				echo $query."<br>";
		$temp->query($query);
		$cantObt = $temp->f('cant');
		$idObt = $temp->f('id');
		if ($cantCheq == 0) $cantCheq = $cantObt;
		if ($cantCheq != $cantObt) {
//echo "$prod, $fecha1, ($fecha2-86400), ($cantCheq-$cant)<br>";
			if (insertaCantidad($prod,$fecha1,$fecha-86400,$cantCheq+$cant)) $paso = true;
			$fecha1 = $fecha;
			$cantCheq = $cantObt;
		}
		$fecha += 86400;
	}
	if (insertaCantidad($prod,$fecha1,$fecha-86400,$cantCheq+$cant)) $paso = true;
//echo "segundo: $prod, $fecha1, ".($fecha2-86400).", ".($cantCheq-$cant)."<br>";
	if ($paso) {
		$query = "delete from tbl_productosReserv where id = $id";
		$temp->query($query);
//			echo $query."<br>";
	}

}


?>
<script type="text/javascript" src="../js/ajax.js"></script>
<script type="text/javascript" language="javascript1.3">
	function entrada(dato) {
		document.getElementById("divCarga").innerHTML = dato;
	}

	getagents("paso=1");
	hiddenVar = new Array();

	function cargaCaract(valores) {
		precio = document.getElementById('precioVal').innerHTML;
		valor = valores.value.split('|');
		cant = hiddenVar.length;

		if (cant > 0) {
			for (i=0;i<cant;i++) {
				if( hiddenVar.indexOf(valores.value) > -1 ) {
					if (hiddenVar[i][0] == 1 ) { //ya está marcado
						hiddenVar[i][0] = 0;
						valores.selected = false;
					} else {
						hiddenVar[i][0] = 1;
						valores.selected = true;
					}
				} else {
						hiddenVar[i+1] = Array(1,valores.value);
				}
			}
		} else {
			hiddenVar[0] = Array(1,valores.value);
		}
		
		for (i=0;i<hiddenVar.length;i++) {
			if (hiddenVar[i][0] == 1)
					document.getElementById(hiddenVar[i][1]).selected = true;
				else
					document.getElementById(hiddenVar[i][1]).selected = false;
		}

		if (valor[3] == 'S') mult = valor[4];
		else mult = 1

		if (valores.checked) {
			if (valor[1] == '+') total=(precio*1)+(valor[2]*mult);
			if (valor[1] == '-') total=(precio*1)-(valor[2]*mult);
			if (valor[1] == '%') total=precio*(valor[2]/100);
		} else {
			if (valor[1] == '+') total=(precio*1)-(valor[2]*mult);
			if (valor[1] == '-') total=(precio*1)+(valor[2]*mult);
			if (valor[1] == '%') total=precio/(valor[2]/100);
		}
		document.getElementById('precioVal').innerHTML=total;
		document.getElementById('precio').value=total;
	}

</script>
<div id="divCarga"></div>
<?php

$vista = 'select r.id, case p.codigo when "" then p.nombre else concat(p.nombre, " - ", p.codigo) end nombre, r.cant, r.precio, r.fechaIni, r.fechaFin, r.fecha
			from tbl_productos p, tbl_productosReserv r ';
$where = 'where p.id = r.idProd and r.codigo = '.$_SESSION['codProdReserv'];
$orden = 'fechaIni desc';

$ancho = 700;
$colEsp = array(array("b", "Borrar Registro", "../images/borra.gif", "Borrar"));
$busqueda = array();
$columnas = array(
				array("Producto/<br>Servicio", "nombre", "", "center", "left" ),
				array("Cant.", "cant", "", "center", "left" ),
				array("Precio", "precio", "", "center", "left" ),
				array("Fecha<br>Inicio", "fechaIni", "120", "center", "center"),
				array("Fecha<br>Final", "fechaFin", "120", "center", "center"),
				array("Fecha", "fecha", "120", "center", "center")
			);

	echo "<table class='total1' width=\"$ancho\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
		<tr>
			<td></td>
			<td><img onclick='redirect(\"index.php?componente=comercio&pag=pago\")' onmouseover='this.style.cursor=\"pointer\"' alt=\""._BURO_APAGAR."\"
				title=\""._BURO_APAGAR."\" src=\"../images/dollar3.gif\" width=\"22\" height=\"22\" /></td>
		</tr>
	</table>";

tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>