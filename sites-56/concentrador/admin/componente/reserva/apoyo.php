<?php define( '_VALID_ENTRADA', 1 );
require_once("../../classes/SecureSession.class.php");
$Session = new SecureSession(3600);
			    
include_once("../../classes/class_tablaHtml.php");
include_once("../../lang/".$_SESSION['idioma'].".php");
include_once( '../../../configuration.php' );
include_once( '../../classes/entrada.php' );
require_once( '../../../include/database.php' );
$database = new database($host, $user, $pass, $db, $table_prefix);
require_once( '../../../include/ps_database.php' );
include_once( '../../adminis.func.php' );
require_once( '../../../include/hoteles.func.php' );
require_once( '../../adminis.func.php' );
include_once( "../../../include/sendmail.php" );

$temp = new ps_DB;
$html = new tablaHTML;
$ent = new entrada;

$salida = null;
$d = $_REQUEST;
//print_r($d);
$paso = $ent->isEntero($d['paso'], 1);

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_VENTA;
$html->pathJS = "../../../";
$html->java = '<script language="JavaScript" type="text/javascript" src="../../../js/calendar.js"></script>
				<script language="JavaScript" type="text/javascript" src="../../../js/calendar-' . $_SESSION['idioma'] . '.js"></script>
				<script language="JavaScript" type="text/javascript" src="../../../js/calendar-inicio.js"></script>';

switch ($paso) {
    case 1:
	$fecha1 = $fecha2 = date('d/m/Y', time());
	if ($d['fecha1']) $fecha1 = $ent->isDate($d['fecha1']);
	if ($d['fecha2']) $fecha2 = $ent->isDate($d['fecha2']);
	if ($d['cant']) $cant = $ent->isReal($d['cant']);
	else $cant = 1;
	$html->tituloTarea = _BURO_TITULO1;
	$html->anchoTabla = 600;
	$html->anchoCeldaI = $html->anchoCeldaD = 245;
	$html->inHide(2, 'paso');

	$html->inTextb(_BURO_CANT, $cant, 'cantidad', 'cant', null, null, _BURO_CANT_DESC);
	$html->inFecha(_REPORTE_FECHA_INI, $fecha1, 'fecha1', 'fecha1', _REPORTE_FECHA_INI, _BURO_DESC_FECHA1);
	$html->inFecha(_REPORTE_FECHA_FIN, $fecha2, 'fecha2', 'fecha2', _REPORTE_FECHA_FIN, _BURO_DESC_FECHA2);

	$boton = '<input class="formul" id="enviaForm" name="enviar" type="button" value="' . _BURO_CONTINUA_BOTON . '"
		onclick="getagents(\'paso=2&cant=\'+document.getElementById(\'cant\').value+\'&fecha1=\'+document.getElementById(\'fecha1\').value+
		\'&fecha2=\'+document.getElementById(\'fecha2\').value);"/>';
	echo $html->salida($boton);

	break;

    case 2:
	$error = false;
	$texto = '';
	if ($fecha1 = $ent->isDate($d['fecha1'])) $fecha1 = to_unix($ent->isDate($d['fecha1']));
	if ($fecha2 = $ent->isDate($d['fecha2'])) $fecha2 = to_unix($ent->isDate($d['fecha2']));
	if ($fecha1 > $fecha2) $fecha2 = $fecha1;
	if ($fecha1 == $fecha2) $texto .= _FORM_FECHA." = ".$d['fecha1'];
	else $texto = _REPORTE_FECHA_INI." = ".$d['fecha1']."<br>"._REPORTE_FECHA_FIN." = ".$d['fecha2'];
	if ($fecha1 < date('dd/mm/Y',time())) {$texto .= _BURO_FECHA_ERROR; $error = true;} //Error de fechas
	if ($ent->isReal($d['cant']) && $d['cant'] > 0) $texto .= "<br>"._BURO_CANT." = ".$cant = $d['cant'];
	else {$texto .= "<br>"._BURO_CANT." = ".$d['cant']._BURO_CANT_ERROR; $error = true;} //Error de cantidad
	if ($ent->isReal($d['prod']) && $d['prod'] > 0) $prod = $d['prod'];

	$html->tituloTarea = _BURO_TITULO1;
	$html->anchoTabla = 600;
	$html->anchoCeldaI = $html->anchoCeldaD = 245;

	$boton = '<input class="formul" id="enviaForm"
		onclick="getagents(\'paso=1&fecha1='.$d['fecha1'].'&fecha2='.$d['fecha2'].'&cant='.$cant.'\');"
		name="enviar" type="button" value="' . _BURO_ATRAS_BOTON . '" />';

	if (!$error) {
	    $html->inHide(3, 'paso');
	    $html->inHide($cant, 'cant');
	    $html->inHide($fecha1, 'fecha1');
	    $html->inHide($fecha2, 'fecha2');

	    $arrProd = existencias($fecha1, $fecha2, $cant);
	    if (count($arrProd) > 0) {
		$html->inSelect(_MENU_ADMIN_PRODUCTO, 'producto', 3, $arrProd, $prod, null, $aclar);

		$boton .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviaForm"
		    onclick="getagents(\'paso=3&cant='.$cant.'&fecha1='.$fecha1.'&fecha2='.$fecha2.'&prod=\'+document.getElementById(\'producto\').value);"
		    name="enviar" type="button" value="' . _BURO_CONTINUA_BOTON . '" />';

	    } else {
		$texto .= _BURO_PROD_ERROR;
	    }
	}
	echo $html->salida($boton, $texto);
	break;

    case 3:
	$error = false;
	$texto = '';
	if (!$fecha1 = $ent->isEntero($d['fecha1'])) $error = true; //Chequeo de la fecha inicio por injection
	if (!$fecha2 = $ent->isEntero($d['fecha2'])) $error = true; //Chequeo de la fecha final por injection
	if ($fecha1 == $fecha2) $texto .= _FORM_FECHA." = ".date('d/m/Y', $fecha1); //Poner la fecha cuando es sólo una
	else $texto = _REPORTE_FECHA_INI." = ".date('d/m/Y', $fecha1)."<br>"._REPORTE_FECHA_FIN." = ".date('d/m/Y', $fecha2); //Poner los entornos de fechas
	if ($fecha1 == $fecha2) $dias = 1;
	else {$dias = ($fecha2 - $fecha1) / 86400; $texto .= "<br>"._BURO_DIAS." = ".$dias;}
	if ($ent->isReal($d['cant']) && $d['cant'] > 0) $texto .= "<br>"._BURO_CANT." = ".$cant = $d['cant'];

	if ($ent->isReal($d['prod']) && $d['prod'] > 0) { //Chequeo de Productos
		
		$prod = $d['prod'];
		$query = "select p.nombre, p.codigo, r.valor from tbl_productos p, tbl_precio r
					where r.idProd = p.id and p.id = $prod and r.fechaIni <= $fecha1 and r.fechaFin >= $fecha2";
		$temp->query($query);
		if ($temp->num_rows() > 0) {
			$valorParc = $dias*$cant*$temp->f('valor');
			if (strlen($temp->f("codigo")) > 0) $nombre = $temp->f('nombre')." - ".$temp->f('codigo');
			else $nombre = $temp->f('nombre');
			$moneda = $temp->f('moneda');

			$query = "select precioModifica, precio, diario from tbl_productosRel p, tbl_caracteristicas ca
					where p.idCaract = ca.id and p.idProd = $prod and fechaIni <= $fecha1 and fechaFin >= $fecha2 and precio > 0 and opcional = 'N' order by ca.id";
			$temp->query($query);

			while($temp->next_record()) {
				$modifica = $temp->f('precioModifica');

				if ($temp->f('diario') == 'S') $valor = $temp->f('precio') * $dias;
				else $valor = $temp->f('precio');
				
				if ($modifica == '+') $valorParc = $valorParc+$valor;
				if ($modifica == '-') $valorParc = $valorParc-$valor;
				if ($modifica == '%') $valorParc = $valorParc*($valor/100);
			}

			$texto .= "<div id='divCaract'></div>";
			$texto .= "<br>"._MENU_ADMIN_PRODUCTO." = ".$nombre." <br><span style='font-weight:normal'>"._BURO_PRECIO.":</span>
						<span id='precioVal'>".$valorParc."</span> ".$moneda;
		} else { $texto .= "<br>"._BURO_PROD_FALLA; $error = true;}
		
	} else {$texto .= "<br>"._MENU_ADMIN_PRODUCTO." = ".$nombre._BURO_CANT_ERROR; $error = true;} //Error de Producto

	$html->tituloTarea = _BURO_TITULO1;
	$html->anchoTabla = 600;
	$html->anchoCeldaI = 145; $html->anchoCeldaD = 345;

	$boton = '<input class="formul" id="enviaForm"
		onclick="getagents(\'paso=2&fecha1='.date('d/m/Y',$d['fecha1']).'&fecha2='.date('d/m/Y',$d['fecha2']).'&cant='.$cant.'&prod='.$prod.'\');"
		name="enviar" type="button" value="' . _BURO_ATRAS_BOTON . '" />';

	if (!$error) {
	    $html->inHide(4, 'paso');
	    $html->inHide($cant, 'cant');
	    $html->inHide($fecha1, 'fecha1');
	    $html->inHide($fecha2, 'fecha2');
	    $html->inHide($d['prod'], 'prod');
	    $html->inHide($valorParc, 'precio');

		$query = "select ca.* from tbl_productosRel p, tbl_caracteristicas ca
					where p.idCaract = ca.id and p.idProd = $prod and fechaIni <= $fecha1 and fechaFin >= $fecha2 and precio > 0 and opcional = 'S'";
		$temp->query($query);
		if ($temp->num_rows() > 0) {
	    $query = "select concat(ca.id, '|', ca.precioModifica, '|', ca. precio, '|', ca.diario, '|', ".$dias.") id,
					concat(left(ca.nombre, 15), ' - ', left(ca.descripcion, 50), ' ', ca.precioModifica, ' ', ca. precio) nombre
						from tbl_productosRel p, tbl_caracteristicas ca
						where p.idCaract = ca.id and p.idProd = $prod and fechaIni <= $fecha1 and fechaFin >= $fecha2 and precio > 0 and opcional = 'S'";
//		echo "<br>query=$query<br>";
		$html->inCheckBox(_MENU_ADMIN_CARACTERISTICA, 'caract', 1, $query, $caract, null, _PROD_CARACT_DESC);


	    }
		$boton .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviaForm"
		    onclick="facil(this.form);"
		    name="enviar" type="button" value="' . _BURO_CONTINUA_BOTON . '" />';

	}
	echo $html->salida($boton, $texto);
	break;

    case 4:
	$error = false;
	$texto = '';
	if (!$fecha1 = $ent->isEntero($d['fecha1'])) $error = true; //Chequeo de la fecha inicio por injection
	if (!$fecha2 = $ent->isEntero($d['fecha2'])) $error = true; //Chequeo de la fecha final por injection
	if ($fecha1 == $fecha2) $dias = 1;
	else {$dias = ($fecha2 - $fecha1) / 86400;}
	if ($ent->isReal($d['cant']) && $d['cant'] > 0) $cant = $d['cant'];
	if (!$precio = $ent->isNumero($d['precio'])) $error = true; //Chequeo del precio por injection
	if (!$prod = $ent->isReal($d['prod'])) $error = true; //Error de Producto

	if (!$error) {
		$fecha = $fecha1;
		$cantCheq = $idCant = 0;
		$paso = false;
		while ($fecha <= $fecha2) {
			$query = "select id, cant from tbl_productosCant where idProd = ".$prod." and fechaIni <= $fecha and fechaFin >= $fecha";
//			echo $query."<br>";
			$temp->query($query);
			$cantObt = $temp->f('cant');
			$idObt = $temp->f('id');
			if ($cantCheq == 0) $cantCheq = $cantObt;
//			if ($idCant == 0) $cantCheq = $idObt;
			if ($cantCheq != $cantObt) {
//				echo "$prod, $fecha1, ($fecha2-86400), ($cantCheq-$cant)<br>";
				if (insertaCantidad($prod,$fecha1,$fecha-86400,$cantCheq-$cant)) $paso = true;
				$fecha1 = $fecha;
				$cantCheq = $cantObt;
			}
			$fecha += 86400;
		}
		if (insertaCantidad($prod,$fecha1,$fecha-86400,$cantCheq-$cant)) $paso = true;
		if ($paso) {
			if (!$_SESSION['codProdReserv']) $_SESSION['codProdReserv'] = generaCodEmp();
			$query = "insert into tbl_productosReserv (id, idProd, precio, cant, codigo, fecha, fechaIni, fechaFin, codVenta) values 
						(null, $prod, $precio, $cant, '{$_SESSION['codProdReserv']}', unix_timestamp(), $fecha1, ".($fecha-86400).", null)";
//			echo "$query<br>";
			$temp->query($query);


			$html->tituloTarea = _BURO_TITULO1;
			$html->anchoTabla = 600;
			$html->anchoCeldaI = 145; $html->anchoCeldaD = 345;

			$boton .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviaForm" onclick="redirect(\'index.php?componente=reserva&pag=venta\');"
						name="enviar" type="button" value="' . _BURO_CONTINUA_BOTON . '" />';
			$texto = _BURO_VENTAOK;
			echo $html->salida($boton, $texto);
		}
	}
	break;

}


function existencias($fecha1,$fecha2,$cant) {
    $temp = new ps_DB;
    
    $query = "select id, nombre, codigo, stock from tbl_productos where fechaIni <= $fecha1 and fechaFin >= $fecha2 ";
    if ($_SESSION['rol'] > 10 ) $query .= " and idCom = '".$_SESSION['comercio']."'";
    $query .= " order by nombre";
//	echo "$query<br>";
    $temp->query($query);
    $arrRes = $temp->loadObjectList();
    $arrPro = array();
    $i = 0;

    foreach ($arrRes as $item) {
	$fecha = $fecha1;
	$pase = true;
	if ($item->stock == 'S') {
	    while ($fecha <= $fecha2){
			$query = "select * from tbl_productosCant where idProd = ".$item->id." and fechaIni <= $fecha and fechaFin >= $fecha and cant >= $cant";
	//	echo "$query<br>";
			$temp->query($query);
			if ($temp->num_rows() == 0) $pase = false;
			$fecha += 86400;
	    }
	}
	if (strlen($item->codigo) > 0) $nombre = $item->nombre . " - " .$item->codigo;
	else $nombre = $item->nombre;
	if ($pase) $arrPro[$i++] = array($item->id, $nombre);
    }
    return $arrPro;

}

?>