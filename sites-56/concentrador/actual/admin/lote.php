<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
$temp = new ps_DB();
$ent = new entrada;
global $temp;
$corCreo = new correo();
include 'classes/PHPExcel/IOFactory.php';

$d = $_POST;
$fechaNow = time();
$error = '';
$pase = 0; //entrada directa sin realizar ninguna tarea
if (_MOS_CONFIG_DEBUG) var_dump($d);
if (_MOS_CONFIG_DEBUG) var_dump($_SESSION);

/**
 * Revisa que no estén los ids de las operaciones repetidos
 *
 * @param [string] $idres
 * @param [integer] $idcom
 * @return void string
 */
function revisaLote ($idres, $idcom){
	$temp = new ps_DB();
	$error = '';

	$temp->query("select count(*) total from tbl_lotes where idreserva = '".$idres."' and idcomercio = '".$idcom."'");
	if ($temp->f('total') > 0) {
		$error .= "<br>Existe una operación en la BD con el mismo identificador de reserva '".$idres."', revise el fichero por favor."; 
	}
	return $error;
}

// Elimina las operaciones que no se van a llevar a término
if ($d['borrar'] > 0) {
	$temp->query("delete from tbl_lotes where id = ".$d['borrar']);
}

// Elimina todas las operaciones
if ($d['borrartodo'] > 0) {
	$temp->query("delete from tbl_lotes where fechaLanz = 0 and valida = 1 and idcomercio in (".$_SESSION['idcomStr'].")");
}

// Envia las operaciones para cobrar
if ($d['verif'] == 2) {
	$q = "select r.codigo from tbl_reserva r, tbl_comercio c where r.codigo like '%".$d['trans']."%' and r.id_comercio = c.idcomercio and c.id = '{$d['comercio']}' order by fecha desc limit 0,1";
	$temp->query($q);
	if ($temp->num_rows() > 0) {
		$codigo = $temp->f('codigo');
		$pos = stripos($codigo, '-');
		if ($pos > 0) {
			$letras = substr($codigo, $pos+1);
			$letras++;
		} else $letras = 'A';
		$d['trans'] = $d['trans']."-".$letras;
	}

	if ($d['confirmacion']) {
		$temp->query("update tbl_lotes set confirmacion = '".$d['confirmacion']."' where id = ".$d['idlote']);
		$d['servicio'] = $d['trans']." ".$d['confirmacion'];
	}

	$arrEnv = array(
		'comercio'	=> $d['comercio'],
		'nombre'	=> $d['nombre'],
		'email'		=> $d['email'],
		'importe'	=> $d['importe'],
		'moneda'	=> $d['moneda'],
		'amex'		=> $d['tarjes'],
		'tiempo'	=> 3,
		'idioma'	=> $d['idioma'],
		'trans'		=> $d['trans'],
		'pasarela'	=> $d['pasarela'],
		'servicio'	=> $d['servicio'],
		'pago'		=> $d['pago'],
		'usd'		=> $d['usd'],
		'moneda'	=> $d['moneda'],
		'eur'		=> $d['eur'],
		'tasaApl' 	=> $d['tasaApl'],
		'dir'		=> substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')+1),
		'lote'		=> $d['idlote']
	);
	// var_dump($arrEnv);exit;

	echo envPago($arrEnv);
}

//se sube el fichero a tratar
if(strlen($_FILES['fichero']['tmp_name']) > 3 && strlen($d['comercio']) > 0) {
	$pase = 1; //se procesan ficheros subidos

	//se borran todas las entradas que no hayan sido aprobadas para ese mismo comercio
	$temp->query("delete from tbl_lotes where valida = 0 and fechaLanz = 0 and idcomercio = ".$d['comercio']);

	if ($_FILES['fichero']['error']) {
			  switch ($_FILES['fichero']['error']){
					   case 1: // UPLOAD_ERR_INI_SIZE
							$error .= "El archivo sobrepasa el limite autorizado por el servidor(archivo php.ini) !";
					   break;
					   case 2:
							$error .= "El archivo sobrepasa el limite autorizado en el formulario HTML !";
					   break;
					   case 3: // UPLOAD_ERR_PARTIAL
							$error .= "El envio del archivo ha sido suspendido durante la transferencia!";
					   break;
					   case 4: // UPLOAD_ERR_NO_FILE
							$error .= "El archivo que ha enviado tiene un tamaño nulo !";
					   break;
			  }
	} else {
		$objPHPExcel = PHPExcel_IOFactory::load($_FILES['fichero']['tmp_name']);
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

		if (_MOS_CONFIG_DEBUG) echo "datoA=".$sheetData[1]['A']."<br>";
		if (_MOS_CONFIG_DEBUG) echo "posA=".stripos($sheetData[1]['A'], 'ID de la reserva')."<br>";
		//Chequeo que el fichero tiene el encabezado de los ficheros de Expedia
		if (
			stripos($sheetData[1]['A'], 'ID de la reserva') !== false
			&& stripos($sheetData[1]['C'], 'Entrada') !== false
			&& stripos($sheetData[1]['D'], 'Salida') !== false
			&& stripos($sheetData[1]['E'],'Fecha de reserva') !== false
			&& stripos($sheetData[1]['H'], 'Estado') !== false
		) {
			$fich = 1; //corresponde al fichero de Expedia
			for ($i = 2; $i <= count($sheetData); $i++) {
				if (!$ent->isEntero($sheetData[$i]['A'])) {$error .= "<br>Error en los datos del fichero, por favor revise el mismo."; $pase = 0; break;}
				if (!$ent->isAlfanumerico($sheetData[$i]['G'])) {$error .= "<br>Error en los datos del fichero, por favor revise el mismo."; $pase = 0; break;}

				$rev = revisaLote($sheetData[$i]['A'], $d['comercio']);
				if (strlen($rev) > 1) {
					$error .= $rev;
					$pase = 0; 
				} else  {
					$q = "insert into tbl_lotes (idcomercio, fecha, idreserva, confirmacion, cliente, email, tipo, tarjeta, valida) values ('".$d['comercio']."', '$fechaNow', '".$sheetData[$i]['A']."', '".$sheetData[$i]['G']."', '".$sheetData[$i]['B']."', '".$_SESSION['email']."', '0', '11', '0')";
				// echo $q."<br>";
					$temp->query($q);
				}
			}
		} else if(
			stripos($sheetData[1]['A'], 'de reserva') !== false
			&& stripos($sheetData[1]['C'], 'Nombre del cliente') !== false
		) {
			$fich = 2; // Fichero de Booking
			for ($i=2; $i <= count($sheetData); $i++) {
				if (!$ent->isEntero($sheetData[$i]['A'])) {$error .= "<br>Error en los datos del fichero, por favor revise el mismo."; $pase = 0; break;}

				$rev = revisaLote($sheetData[$i]['A'], $d['comercio']);
				if (strlen($rev) > 1) { $error .= $rev; $pase = 0; } 
				else {
					$temp->query("insert into tbl_lotes (idcomercio, fecha, idreserva, confirmacion, cliente, email, tipo, tarjeta, valida) values ('".$d['comercio']."', '$fechaNow', '".$sheetData[$i]['A']."', '', '".$sheetData[$i]['C']."', '".$_SESSION['email']."', '0', '11', '0')");
				}
				
			}

		} else {$error .= "<br>Error en el fichero, por favor suba el fichero correcto"; $pase = 0;}

		if (_MOS_CONFIG_DEBUG) var_dump($sheetData);
	}

	if (strlen($error)) {
		echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">$error</div>";
	} else {
		echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">Fichero preparado correctamente</div>";
	}
} else if (strlen($_FILES['fichero']['tmp_name']) == 0 && strlen($d['comercio']) > 0 && !isset($d['borrartodo'])){
	$q = "update tbl_lotes set valida = 1 where fecha = ".$d['verif']." and idcomercio = ".$d['comercio'];
	$temp->query($q);
}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = "Pago por Lotes";

if ($d['cambiar']) 
	$q = "select * from tbl_lotes where valida = 1 and idcomercio in (".$_SESSION['idcomStr'].") and id = ".$d['cambiar']." limit 0,1";
else 
	$q = "select * from tbl_lotes where valida = 1 and idcomercio in (".$_SESSION['idcomStr'].") limit 0,1";

$temp->query($q);
$arrLote = $temp->loadRowList();

if (count($arrLote) == 1 && $pase != 1) {
	$pase = 2;
	if (_MOS_CONFIG_DEBUG) var_dump($arrLote);

	$html = new tablaHTML;
	$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
	function verifica() {
		return true;
	}
	</script>
	<style> #usrTr{width:250px;}  </style>";

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = "";
	$html->tituloTarea = _REPORTE_TASK;
	$html->hide = true;
	$html->anchoTabla = 650;
	$html->anchoCeldaI = 300; $html->anchoCeldaD = 340;

	$html->inHide(true, 'query');
	$html->inTextb('Id de la Reserva', '', 'reservB');
	$html->inTextb('No. Confirmación', '', 'confB');
	echo $html->salida();
	

	if ($arrLote[0][9] == 0 && $pase != 1) { //si no hay valor muestro un formulario para completar los campos que falten

		$html = new tablaHTML;
		$html->tituloPag = "Pago por Lotes";
		$html->tituloTarea = "Lanzamiento de los pagos<br>Completar datos";
		$html->anchoTabla = 650;
		$html->tabed = true;
		$html->anchoCeldaI = 300;
		$html->anchoCeldaD = 340;

		$html->hide = false;
		$html->java = '<script type="text/javascript" lang="Javascript" >function verifica() { if (checkField (document.forms[0].importe, isMoney, "")) return true;else return false;}</script>';

		$arrME = array();
		$html->inHide($arrLote[0][1], 'comercio');
		$html->inHide('840', 'moneda');
		$html->inHide('11', 'tarjes');
		$html->inHide('es', 'idioma');
		$html->inHide('0', 'usd');
		$html->inHide('2', 'verif');
		$html->inHide($arrLote[0][4], 'trans');
		$html->inHide($arrLote[0][4]." ".$arrLote[0][5], 'servicio');
		$html->inHide('S', 'pago');
		$html->inHide($arrLote[0][0], 'idlote');

		$query = "select pasarelaAlMom from tbl_comercio where id in (" . $arrLote[0][1] . ")";
		$temp->query($query);
    	$pasar = implode(',', $temp->loadResultArray());
		$pasar = ltrim(rtrim($pasar, ','), ',');
		$temp->query("select p.idPasarela from tbl_pasarela p, tbl_colTarjPasar c, tbl_colPasarMon m where p.idPasarela = m.idpasarela and m.idmoneda = 840 and p.idPasarela = c.idPasar and idTarj = 11 and p.secure = 0 and p.idPasarela in ($pasar) limit 0,1");
		//$html->inHide($temp->f('idPasarela'), 'pasarela');
		$html->inHide('98', 'pasarela');

		$html->inTextb('Nombre', $arrLote[0][6], 'nombre');
		$html->inTextb('Correo', $arrLote[0][7], 'email');
		if (strlen($arrLote[0][5]) == 0) {
			$html->inTextb('No. Confirmación', '', 'confirmacion');
		}
		$html->inTextb('Importe', '', 'importe');
		echo $html->salida();
	}
}


if ($pase != 1) {
	//Formulario inicial para subir ficheros
	$html = new tablaHTML;
	$html->tituloPag = "Pago por Lotes";
	$html->tituloTarea = "Subir Archivo";
	$html->anchoTabla = 650;
	$html->tabed = true;
	$html->anchoCeldaI = 300;
	$html->anchoCeldaD = 340;

	$q = "select id, nombre from tbl_comercio where  activo = 'S' and lotes = 1 and id in (".$_SESSION['idcomStr'].") order by nombre";
	$temp->query($q);
	if ($temp->num_rows() > 1) {
		$html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $q,  str_replace(",", "', '", $comercId));
	} else {
		$html->inHide($temp->f('id'), 'comercio');
	}

	$html->inHide(0, 'verif');
	$valorIni = array('E', 'P');
	$etiq = array("Expedia", "Pago diferido");
	// $html->inRadio("Tipo de fichero", $valorIni, 'actividad', $etiq, 'E'); //Tipo de fichero a subir Expedia o Pago Diferido
	$html->inTextoL('<input type="file" name="fichero" id="fichero"  />');

	echo $html->salida();
}

// confirma el listado subido para empezar a cobrar
if ($pase == 1) { ?>
<style>.lineaT span{font-size: 12px;}</style>
<?php
	$html = new tablaHTML;
	$html->tituloPag = "Pago por Lotes";
	$html->tituloTarea = "Revisar los Datos";
	$html->anchoTabla = 650;
	$html->tabed = true;
	$html->anchoCeldaI = 300;
	$html->anchoCeldaD = 340;

	$html->inTextoL('Revise los datos que aparecen en la tabla inferior <strong>cuidadosamente</strong>, si está de acuerdo con ellos oprima "Enviar".<br>De lo contrario, cargue nuevamente el fichero corregido y oprima "Enviar".','Titu');

	$html->inHide($d['comercio'], 'comercio');
	$html->inHide($fechaNow, 'verif');
	$html->inTextoL('<input type="file" name="fichero" id="fichero"  />');
	echo $html->salida();
}
	
if ( $fich == 1 && $pase == 1) { //para los ficheros Expedia

	$vista = "select c.nombre, idreserva, confirmacion, cliente, fecha from tbl_lotes l, tbl_comercio c ";
// 	if ($_SESSION['comercio'] == 'todos') $where = '';
// 	else 
	$where = "where l.fecha = $fechaNow and l.idcomercio = c.id and c.id in (".$_SESSION['idcomStr'].")";
	$orden = 'l.fecha';

	$busqueda = array();

	$columnas = array(
					array('Comercio', "nombre", "", "center", "left" ),
					array('ID de la reserva', "idreserva", "", "center", "center" ),
					array('No. Confirmaci&oacute;n', "confirmacion", "", "center", "left" ),
					array('Cliente', "cliente", "", "center", "left" ),
					array('Fecha sub.', "fecha", "", "center", "left" )
				);

	$ancho = 900;

} elseif ($pase == 2) {
	
	$vista = "select l.id as id, c.nombre, idreserva, confirmacion, cliente, fecha from tbl_lotes l, tbl_comercio c ";
// 	if ($_SESSION['comercio'] == 'todos') $where = '';
// 	else 
	$where = "where l.idcomercio = c.id and c.id in (".$_SESSION['idcomStr'].") and fechaLanz = 0";
	$orden = 'l.fecha';

	if ($d['query']) {
		if ($d['reservB']) $where .= " and idreserva like '%".$d['reservB']."%'";
		if ($d['confB']) $where .= " and confirmacion like '%".$d['confB']."%'";
	}

	$busqueda = array();

	$colEsp = array(array("b", "Borrar Registro", "css_borra", "Borrar")
					, array("e", "Cargar esta Operación", "css_edit", "Ver")
				);
	$columnas = array(
					array('Comercio', "nombre", "", "center", "left" ),
					array('ID de la reserva', "idreserva", "", "center", "center" ),
					array('No. Confirmaci&oacute;n', "confirmacion", "", "center", "left" ),
					array('Cliente', "cliente", "", "center", "left" ),
					array('Fecha sub.', "fecha", "", "center", "left" )
				);

	$ancho = 900;
	echo "<div style='float:left; width:100%' ><table class='total1' width=\"100%\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
	<tr>
		<td align='center'><span onclick='if (confirm(\"Esta operaci\u00f3n borrar\u00e1 todos los records de la tabla y no podr\u00e1n ser recuperados. Est\u00e1 seguro de continuar?\")) {document.exporta2.submit();}' onmouseover='this.style.cursor=\"pointer\"' alt='Borrar todos los records' title='Borrar todos los records' style='text-align:center;color:red;font-size:12px;font-weight:bold;margin:0 5px;'>Borrar Todo</span></td>
			</tr>
		</table></div>";
}

if (strlen($vista) > 3) {
	// echo $vista.$where." order by ".$orden;
	tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
	?>
	<?php
	
} else {
	echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">No hay datos para mostrar</div>";
}
?>
<form name="exporta2" target="_self" name="imprime" action="" method="POST">
	<input type="hidden" name="borrartodo" value="1">
	<input type="hidden" name="comercio" value="1">
</form>

<script type="text/javascript"  charset="utf-8">


</script>


