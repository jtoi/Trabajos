<?php

/* 
 * Fichero para manejar los Clientes de Fincimex y revisar la documentación
 */

//ini_set('display_errors', 0);
//error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('_VALID_ENTRADA', 1);
require_once('configuration.php');
require_once("admin/classes/SecureSession.class.php");
$Session = new SecureSession(_TIEMPOSES); //la sessión cambiada a una duración de 5 horas a partir del 17/01/18	

require_once('admin/classes/entrada.php');
include 'include/mysqli.php';
require_once('include/hoteles.func.php');
// require_once( 'include/correo.php' );
require_once('admin/adminis.func.php');

$temp = new ps_DB;
// $correo = new correo;
$ent = new entrada;
$q = array();
$fechaR = date('d/m/Y', strtotime(date('m/d/Y')));
$fecs = $fecs2 = date('d/m/Y', strtotime('-1 days', strtotime(date('m/d/Y'))));
$d = $_REQUEST;
$fec = $fec2 = '%';

if (date('n') >= 1 && date('n') <= 3) { // fechas del primer trimestre
	$fec1Ais = mktime(0, 0, 0, 01, 01, date("Y"));
	$fec2Ais = mktime(0, 0, 0 - 1, 04, 01, date("Y"));
} elseif (date('n') >= 4 && date('n') <= 6) { // fechas del segundo trimestre
	$fec1Ais = mktime(0, 0, 0, 04, 01, date("Y"));
	$fec2Ais = mktime(0, 0, 0 - 1, 07, 01, date("Y"));
} elseif (date('n') >= 7 && date('n') <= 9) { // fechas del tercer trimestre
	$fec1Ais = mktime(0, 0, 0, 07, 01, date("Y"));
	$fec2Ais = mktime(0, 0, 0 - 1, 10, 01, date("Y"));
} elseif (date('n') >= 10 && date('n') <= 12) { // fechas del cuarto trimestre
	$fec1Ais = mktime(0, 0, 0, 10, 01, date("Y"));
	$fec2Ais = mktime(0, 0, 0 - 1, 01, 01, date("Y") + 1);
}

if (strlen($d['fecha'])) {
	$fecs = $d['fecha'];
}
if (strlen($d['fecha2'])) {
	$fecs2 = $d['fecha2'];
}

// echo json_encode($_FILES);
//Clientes con problemas para el envío de correos
if (strlen($_FILES['fichero']['tmp_name']) > 3 && $_FILES['fichero']['type'] == 'text/plain') {
	if ($_FILES['fichero']['error']) {
		switch ($_FILES['fichero']['error']) {
			case 1: // UPLOAD_ERR_INI_SIZE
				echo "El archivo sobrepasa el limite autorizado por el servidor(archivo php.ini) !";
				break;
			case 2:
				echo "El archivo sobrepasa el limite autorizado en el formulario HTML !";
				break;
			case 3: // UPLOAD_ERR_PARTIAL
				echo "El envio del archivo ha sido suspendido durante la transferencia!";
				break;
			case 4: // UPLOAD_ERR_NO_FILE
				echo "El archivo que ha enviado tiene un tamaño nulo !";
				break;
		}
	} else {
		$handle = fopen($_FILES['fichero']['tmp_name'], 'r');
		while (($line = fgets($handle)) !== false) {
			//			echo "<br>$line<br>";
			if (stripos($line, "(")) {
				$usuario = substr($line, stripos($line, "(") + 1, stripos($line, ')') - stripos($line, "(") - 1);
				$error = substr($line, stripos($line, '%') + 1);
				$error = trim(trim($error, '\t'));
				$arrF = explode('/', $d['fechaR']);
				$fecha = $arrF[2] . "-" . $arrF[1] . "-" . $arrF[0];
				$temp->query("select id from tbl_aisCliente where binary usuario = '$usuario'");
				$idusuario = $temp->f('id');
				if (strlen($idusuario) < 3) {
					echo "El usuario $usuario no se encuentra en la BD";
					error_log("El usuario $usuario no se encuentra en la BD");
					// break;
				} else {

					$temp->query("update tbl_aisCliente set correoenv = 1 where binary id = '$idusuario'");
					$temp->query("delete from tbl_aisClienteError where binary idcliente = '$idusuario'");
					$temp->query("insert into tbl_aisClienteError (idcliente, fecha, fechaRev, error) values ('$idusuario', unix_timestamp(), unix_timestamp('$fecha'), '$error')");
				}
			}
		}
	}
}

//Clientes para activar
if (strlen($_FILES['ficok']['tmp_name']) > 3 && $_FILES['ficok']['type'] == 'text/plain') {
	if ($_FILES['ficok']['error']) {
		switch ($_FILES['ficok']['error']) {
			case 1: // UPLOAD_ERR_INI_SIZE
				echo "El archivo sobrepasa el limite autorizado por el servidor(archivo php.ini) !";
				break;
			case 2:
				echo "El archivo sobrepasa el limite autorizado en el formulario HTML !";
				break;
			case 3: // UPLOAD_ERR_PARTIAL
				echo "El envio del archivo ha sido suspendido durante la transferencia!";
				break;
			case 4: // UPLOAD_ERR_NO_FILE
				echo "El archivo que ha enviado tiene un tamaño nulo !";
				break;
		}
	} else {
		$handle = fopen($_FILES['ficok']['tmp_name'], 'r');
		$i = 0;
		while (($line = fgets($handle)) !== false) {
			//			echo "<br>$line<br>";
			if (stripos($line, "(")) {
				$usuario = substr($line, stripos($line, "(") + 1, stripos($line, ')') - stripos($line, "(") - 1);
				// $error = substr($line, stripos($line, '%')+1);
				// $error = trim(trim($error,'\t'));
				// $arrF = explode('/',$d['fechaR']);
				// $fecha = $arrF[2]."-".$arrF[1]."-".$arrF[0];
				$temp->query("update tbl_aisCliente set activo = 1 where binary usuario = '$usuario'");
				$i++;
			}
		}
		echo "Se activaron $i remitentes<br>";
	}
}

?>
<style>
	body {
		font-family: Arial, sans-serif;
		font-size: 11px;
	}

	.css_x-office-document {
		background: url(images/iconosA201208112100.jpg) no-repeat -1px -1px transparent;
		height: 22px;
		width: 22px;
		display: block;
		float: left;
		margin: 0 2px;
	}

	.espec {
		cursor: pointer;
	}
</style>
<form action="" method="post" enctype="multipart/form-data">
	<div id='tuto'>
		<input type="text" name="valores" id="valores" /><label for="valores"> IdCimex para activar</label><br>
		<input type="checkbox" name="clientes" id="clientes" value="1" /><label for="clientes">Listado de Clientes</label><br>
		<input type="checkbox" name="nuevos" id="nuevos" value="1" /><label for="nuevos">Clientes inscritos nuevos</label><br>
		<input type="checkbox" name="actualizados" id="actualizados" value="1" /><label for="actualizados">Clientes que han actualizado documentaci&oacute;n o dato</label><br>
		<input type="checkbox" name="activos" id="activos" value="1" /><label for="activos">Clientes activos</label><br>
		<input type="checkbox" name="inactivos" id="inactivos" value="1" /><label for="inactivos">Clientes inactivos</label><br>
		<input type="checkbox" name="inscritos" id="inscritos" value="1" /><label for="inscritos">Clientes inscritos</label><br>
		<input type="checkbox" name="menos3" id="menos3" value="1" /><label for="menos3">Clientes inactivos con menos de 3 docs</label><br>
		<input type="checkbox" name="mas3" id="mas3" value="1" /><label for="mas3">Clientes inactivos con más de 3 docs</label><br>
		<input type="checkbox" name="envc" id="envc" value="1" /><label for="envc">Clientes para enviarles Correo</label><br>
		<input type="checkbox" name="revc" id="revc" value="1" /><label for="revc">Revisión de Clientes</label><br>
		<input type="checkbox" name="operaciones" id="operaciones" value="1" /><label for="operaciones">Listado de Operaciones</label><br>
		<input type="checkbox" name="operacionesa" id="operacionesa" value="1" /><label for="operacionesa">Listado de Operaciones Aceptadas</label><br>
		<input type="checkbox" name="operacionesg" id="operacionesg" value="1" /><label for="operacionesg">Listado de Operaciones Denegadas</label><br>
		<input type="checkbox" name="operacioneensd" id="operacioneensd" value="1" /><label for="operacioneensd">Listado de Operaciones en sol. devolución</label><br>
		<input type="checkbox" name="operdet" id="operdet" value="1" /><label for="operdet">Listado de Operaciones Detenidas</label><br>
		<input type="checkbox" name="operacionesd" id="operacionesd" value="1" /><label for="operacionesd">Listado de Operaciones Devueltas</label><br><br>
		<!-- <input type="button" id="botesp" value="Subir listado de Clientes con problemas" /> -->
		Subir listado de Clientes con problemas: <input type="file" name="fichero" id="fichero" />&nbsp;
		Fecha Revisión: <input type="text" name="fechaR" id="fechaR" value="<?php echo $fechaR; ?>"><br><br>
		Subir listado de Clientes para activar: <input type="file" name="ficok" id="ficok" /><br><br>
		<!-- <input type="checkbox" name="operdoc" id="operdoc" value="1" /><label for="operdoc">Denegadas por documentación</label><br> -->
		Desde: <input type="text" name="fecha" id="fecha" value="<?php echo $fecs; ?>" />&nbsp;&nbsp;Hasta: <input type="text" name="fecha2" id="fecha2" value="<?php echo $fecs2; ?>" /><br><br>
		Pa&iacute;ses: <select name="pais" id="pais">
			<?php
			$q = "select id, nombre from tbl_paises order by nombre";
			$temp->query($q);
			$arrPa = $temp->loadAssocList();
			$ies = "0";
			for ($i = 0; $i < count($arrPa); $i++) {
				$ies .= "," . $arrPa[$i]['id'];
			}
			echo  "<option value='" . $ies . "' selected='selected'>Todos</option>";
			for ($i = 0; $i < count($arrPa); $i++) {
				$sel = '';
				if ($d['pais'] == $arrPa[$i]['id']) $sel = "selected='selected'";
				echo "<option value='" . $arrPa[$i]['id'] . "' $sel>" . $arrPa[$i]['nombre'] . "</option>"; 
			}
			?>
		</select><br><br>
		Usuario, correo o idcimex: <input type="text" name="usuariover" id="usuariover" /><br><br>
		Id Transacción, Identificador ó cod Titanes: <input type="text" name="operacionid" id="operacionid" /><br><br>
		Permite que el Cliente pueda hacer envíos de +3000: <input type="text" name="usuariolib" id="usuariolib" /><br><br>
		Revisa el monto del Cliente en el trimestre (Poner usuario, correo o idcimex): <input type="text" name="montousuario" id="montousuario" /><br><br>
		Revisa el monto del Beneficiario en el trimestre (Poner CI): <input type="text" name="montobenef" id="montobenef" /><br><br>
		<input type="submit" value="Enviar">
	</div>
	<div id="prob" style="display:none;">
		Poner el listado de los Remitentes con problemas:<br>
		<textarea name="listadito" cols="100" rows="30" id="listadito"></textarea>
		<br><br>
		<input type="button" id="enviaR" value="Enviar">
	</div>
</form>


<script type="text/javascript">
	<?php
	$q = array();
	if (strlen($d['valores']) > 3
		//&& $ent->isEntero(str_replace(",", "", $d['valores']))
	) {
		$q[] = "update tbl_aisCliente set activo = 1 where idcimex in (" . $d['valores'] . ")";
	}
	$arrfecs = explode('/', $fecs);
	$arrfecs2 = explode('/', $fecs2);

	$q[] = "select (select count(id) from tbl_aisCliente) 'Cant. Clientes',
(select count(id) from tbl_aisCliente where activo = 1) 'Cant. Clientes Activos',
format(((select count(id) from tbl_aisCliente where activo = 1)/(select count(id) from tbl_aisCliente)),2) '% Activ.',
(select count(id) from tbl_aisCliente where activo = 0) 'Cant. Clientes Inactivos'";

	if ($d['operacionid']) {
		$d['operacionid'] = str_replace(' ', "", $d['operacionid']);
		if (strpos($d['operacionid'], ',')) $d['operacionid'] = str_replace(',', "','", $d['operacionid']);
		$enc = $q[] = "select t.idtransaccion, t.identificador, o.titOrdenId, format((t.valor/100),2) 'valor',
		t.moneda, c.idcimex 'IdCli', concat(c.nombre, ' ', c.papellido) cliente, c.usuario, c.correo, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido)beneficiario, b.numDocumento, from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') 'fecha', from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i:%s') 'fechaMod', t.estado, t.id_error as error, (select observacion from tbl_devoluciones d where t.idtransaccion = d.idtransaccion) motivo
	from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b
	where t.idtransaccion = o.idtransaccion
		and b.id = o.idbeneficiario
		and c.id = o.idcliente 
		and (t.idtransaccion in ('{$d['operacionid']}') or t.identificador in ('{$d['operacionid']}') or o.titOrdenId in ('{$d['operacionid']}'))";
		
		//var_dump($q);
		// echo $enc;
		// exit;
	?>
		document.getElementById('operacionid').value = '<?php echo $d['operacionid']; ?>';
	<?php
	} else {
	?>
		document.getElementById('operacionid').value = '';
	<?php
	}

	if ($d['montobenef']) {

		$temp->query("select id from tbl_aisBeneficiario where 	numDocumento = '" . $d['montobenef'] . "'");
		$id = $temp->f('id');

		if ($id*1 > 1) {
			$q[] = "select format(sum(t.valor_inicial)/100,2) 'Acumulado' from tbl_aisOrden o, tbl_transacciones t where o.idtransaccion = t.idtransaccion and o.idbeneficiario = $id and t.estado in ('A', 'B', 'V', 'R') and t.fecha between $fec1Ais and $fec2Ais ";
		}

	}

	if ($d['montousuario']) {

		$temp->query("select id from tbl_aisCliente where correo = '" . $d['montousuario'] . "' or usuario = '" . $d['montousuario'] . "' or idcimex = '" . $d['montousuario'] . "'");
		$id = $temp->f('id');

		if ($id*1 > 1) {
			$q[] = "select format(sum(t.valor_inicial)/100,2) 'Acumulado' from tbl_aisOrden o, tbl_transacciones t where o.idtransaccion = t.idtransaccion and o.idcliente = $id and t.estado in ('A', 'B', 'V', 'R') and t.fecha between $fec1Ais and $fec2Ais ";
		}

	}

	if ($d['usuariolib']) {
		$temp->query("select valor from tbl_setup where idsetup = 69");
			$val = $temp->f('valor');
		if ($d['usuariolib'] > 100) {
			if (strlen($val) < 3) $val = $d['usuariolib']; else $val .= ",".$d['usuariolib'];
			
			$enc = $q[] = "update tbl_setup set valor = '$val' where idsetup = 69";
		}
		$q[] = "select concat(nombre, ' ', papellido, ' ', sapellido) 'Cliente', idcimex from tbl_aisCliente where idcimex in ($val)";
	}

	if ($d['operdet']) {
		$enc = $q[] = "select t.idtransaccion, t.identificador, o.titOrdenId, formateaO((t.valor_inicial/100),2,10) 'valor',
		t.moneda, c.idcimex 'IdCli', c.idtitanes, concat(c.nombre, ' ', c.papellido) cliente, c.usuario, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido) beneficiario,
		formateaF(t.fecha, 10) 'fecha', formateaF(t.fecha_mod, 10) 'fechaMod', case t.estado
			when 'P' then 'En Proceso'
			when 'A' then 'Aceptada'
			when 'D' then 'Denegada'
			when 'N' then 'No Procesada'
			when 'B' then 'Anulada'
			when 'V' then 'Devuelta'
			when 'R' then 'Reclamada'
			else '' end 'Estado'
	from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b
	where t.idtransaccion = o.idtransaccion
		and b.id = o.idbeneficiario
		and c.id = o.idcliente
		and t.identificador like '%%'
		and o.estado = 'T'
		and t.idtransaccion not in (210423193968)
	order by t.idtransaccion
	limit 0,100";
	}

	if ($d['usuariover']) {
		// echo "VAVAVAVAV";
		$enc = $q[] = "select distinct concat('- (',usuario, ') ',nombre,' ',papellido,' ',sapellido,' %') remitente, c.id, idtitanes, idcimex, correo, 
			numDocumento, from_unixtime(fechaDocumento, '%d/%m/%Y') 'fec Docum.', from_unixtime(c.fecha, '%d/%m/%Y %H:%i:%s') fec, from_unixtime(c.fechaAltaCimex, '%d/%m/%Y %H:%i:%s') fecCimex, 
			(select format(sum(t.valor_inicial)/100,2) from tbl_aisOrden o, tbl_transacciones t where o.idtransaccion = t.idtransaccion and o.idcliente = c.id and t.estado in ('A', 'B', 'V', 'R') and t.fecha between $fec1Ais and $fec2Ais) 'Acumulado<br>Trimestre', 
			CP,
			(select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais,
			case activo when 1 then 'Si' else 'No' end 'activo?',
			(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum',
			(select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf'
		from tbl_aisCliente c
		where correo = '" . $d['usuariover'] . "' or usuario = '" . $d['usuariover'] . "' or idcimex = '" . $d['usuariover'] . "'
		order by c.fecha desc";

		$q[] = "select  t.idtransaccion, t.identificador, o.titOrdenId, formateaO((t.valor_inicial/100),2,10) 'valor', t.moneda, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido) beneficiario, formateaF(t.fecha, 10) 'fecha', formateaF(t.fecha_mod, 10) 'fechaMod', case t.estado when 'P' then 'En Proceso' when 'A' then 'Aceptada' when 'D' then 'Denegada' when 'N' then 'No Procesada' when 'B' then 'Anulada' when 'V' then 'Devuelta' when 'R' then 'Reclamada' else '' end 'Estado', t.id_error as error from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b where t.idtransaccion = o.idtransaccion  and b.id = o.idbeneficiario and c.id = o.idcliente and (c.correo = '" . $d['usuariover'] . "' or c.usuario = '" . $d['usuariover'] . "' or c.idcimex = '" . $d['usuariover'] . "') order by t.fecha desc limit 10";

		$q[] = "select concat(b.nombre, ' ', b.papellido, ' ', b.sapellido) 'nombre', b.idcimex, b.idtitanes, b.telf, b.numDocumento 'CI', 
		(select format(sum(t.valor_inicial)/100,2) 'Acumulado' from tbl_aisOrden o, tbl_transacciones t where o.idtransaccion = t.idtransaccion and o.idbeneficiario = b.id and t.estado in ('A', 'B', 'V', 'R') and t.fecha between $fec1Ais and $fec2Ais ) 'Acumulado<br>Trimestre',
		b.ciudad from tbl_aisBeneficiario b, tbl_aisClienteBeneficiario r, tbl_aisCliente c where r.idbeneficiario = b.id and c.id = r.idcliente and (c.correo = '" . $d['usuariover'] . "' or c.usuario = '" . $d['usuariover'] . "' or c.idcimex = '" . $d['usuariover'] . "')";

		// $q[] = "select t.identificador 'ordenToco', t.idtransaccion 'ordenConc', o.idtitanes 'ordenTit', format(t.valor/100,2) 'Valor', m.moneda from tbl_aisOrden o, tbl_transacciones t, tbl_aisBeneficiario b, tbl_aisClienteBeneficiario r, tbl_aisCliente c, tbl_moneda m where c.id = r.idcliente and r.idbeneficiario = b.id and o.idtransaccion = t.idtransaccion and o.idcliente = c.id and o.idbeneficiario = b.id and m.idmoneda = t.moneda and (c.correo = '" . $d['usuariover'] . "' or c.usuario = '" . $d['usuariover'] . "' or c.idcimex = '" . $d['usuariover'] . "') limit 10";
		// var_dump($q);
		// echo "pase";
		// exit;
	?>
		document.getElementById('usuariover').value = '<?php echo $d['usuariover']; ?>';
	<?php
	} else {
	?>
		document.getElementById('usuariover').value = '';
	<?php
	}

	if ($d['clientes']) {
		$enc = $q[] = "select distinct concat('- (',usuario, ') ',nombre,' ',papellido,' ',sapellido,' %') cadena, c.id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario, correo, direccion, 
			numDocumento, from_unixtime(fechaDocumento, '%d/%m/%Y') 'fec Docum.', from_unixtime(c.fecha, '%d/%m/%Y %H:%i:%s') fec, from_unixtime(c.fechaAltaCimex, '%d/%m/%Y %H:%i:%s') fecCimex, CP,
			(select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais,
			case activo when 1 then 'Si' else 'No' end 'activo?',
			(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum',
			(select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf'
		from tbl_aisCliente c, tbl_aisClienteBeneficiario e
		where e.idcliente = c.id and c.paisResidencia in (" . $d['pais'] . ")
			and (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) > 0
			and c.fecha between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59')
		order by c.fecha desc";
	?>
		document.getElementById('clientes').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('clientes').checked = false;
	<?php
	}

	if ($d['inactivos']) {
		$enc = $q[] = "select concat('- (',usuario, ') ',nombre,' ',papellido,' ',sapellido,' %') cadena, id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario, correo, 
			numDocumento, from_unixtime(fechaDocumento, '%d/%m/%Y') 'fec Docum.', from_unixtime(fecha, '%d/%m/%Y %H:%i:%s') fec, from_unixtime(c.fechaAltaCimex, '%d/%m/%Y %H:%i:%s') fecCimex, CP,
			(select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais,
			case activo when 1 then 'Si' else 'No' end 'activo?',
			(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum',
			(select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf'
		from tbl_aisCliente c
		where c.activo = 0
			and (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) > 0
			and c.fecha between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') and paisResidencia in (" . $d['pais'] . ")
		order by fecha desc";
	?>
		document.getElementById('inactivos').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('inactivos').checked = false;
	<?php
	}

	if ($d['inscritos']) {
		$enc = $q[] = "select count(*)
		from tbl_aisCliente c
		where c.fechaAltaCimex between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') ";
	?>
		document.getElementById('inactivos').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('inactivos').checked = false;
	<?php
	}

	if ($d['revc']) {
		$enc = $q[] = "select id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario, correo, numDocumento, from_unixtime(fechaDocumento,'%d/%m/%Y') fecDoc, CP, direccion, ciudad, formateaF(fecha, 10) fec, formateaF(fechaAltaCimex, 10) 'fecCimex', telf1, (select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais, 	case activo when 1 then 'Activo' else 'Desactivado' end 'activo?', (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum', (select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf' from tbl_aisCliente c where (idtitanes is null or idtitanes = '') and c.fechaAltaCimex > 1584072380 order by fecha desc limit 0,200";
		$q[] = "select id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario, correo, numDocumento, from_unixtime(fechaDocumento,'%d/%m/%Y') fecDoc, CP, direccion, ciudad, formateaF(fecha, 10) fec, formateaF(fechaAltaCimex, 10) 'fecCimex', telf1, (select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais, case activo when 1 then 'Activo' else 'Desactivado' end 'activo?', (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum', (select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf' from tbl_aisCliente c where (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) = 0 and c.fechaAltaCimex > (unix_timestamp()-(20*24*60*60)) and c.borrficheros = 0 order by fecha desc limit 0,200";
		$q[] = "select concat(nombre,' ',papellido,' ',sapellido,' (',usuario, ')') cliente from tbl_aisCliente c where (select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) = 0 and c.fecha > 1583930804 and id not in (25240, 25145, 24785, 22519, 21269) order by fecha desc limit 0,200";
	?>
		document.getElementById('revc').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('revc').checked = false;
	<?php
	}

	if ($d['envc']) {
		$enc = $q[] = "select c.id, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario, correo, 
			numDocumento, from_unixtime(fechaDocumento, '%d/%m/%Y') 'Fecha Docum.', from_unixtime(e.fechaRev, '%d/%m/%Y') 'Fecha revisado', e.error
		from tbl_aisCliente c, tbl_aisClienteError e
		where c.correoenv = 1 and c.id = e.idcliente
			and e.fechaRev between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') 
		order by e.fechaRev desc";
	?>
		document.getElementById('envc').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('envc').checked = false;
	<?php
	}

	if ($d['mas3']) {
		$enc = $q[] = "select concat('- (',usuario, ') ',nombre,' ',papellido,' ',sapellido,' %') cadena, id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario, correo, 
			numDocumento, from_unixtime(fechaDocumento, '%d/%m/%Y') 'fec Docum.', from_unixtime(fecha, '%d/%m/%Y %H:%i:%s') fec, from_unixtime(c.fechaAltaCimex, '%d/%m/%Y %H:%i:%s') fecCimex, CP,
			(select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais,
			case activo when 1 then 'Si' else 'No' end 'activo?',
			(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum',
			(select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf'
		from tbl_aisCliente c
		where c.activo = 0
			and c.fecha between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') and paisResidencia in (" . $d['pais'] . ")
			and (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) > 2
		order by fecha desc";
	?>
		document.getElementById('mas3').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('mas3').checked = false;
	<?php
	}

	if ($d['menos3']) {
		$enc = $q[] = "select concat('- (',usuario, ') ',nombre,' ',papellido,' ',sapellido,' %') cadena, id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario, correo, 
			numDocumento, from_unixtime(fechaDocumento, '%d/%m/%Y') 'fec Docum.', from_unixtime(fecha, '%d/%m/%Y %H:%i:%s') fec, from_unixtime(c.fechaAltaCimex, '%d/%m/%Y %H:%i:%s') fecCimex, CP,
			(select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais,
			case activo when 1 then 'Si' else 'No' end 'activo?',
			(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum',
			(select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf'
		from tbl_aisCliente c
		where c.activo = 0
			and (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) > 0
			and c.fecha between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') and paisResidencia in (" . $d['pais'] . ")
			and (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) < 3
		order by fecha desc";
	?>
		document.getElementById('menos3').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('menos3').checked = false;
	<?php
	}

	if ($d['activos']) {
		$enc = $q[] = "select concat('- (',usuario, ') ',nombre,' ',papellido,' ',sapellido,' %') cadena, id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario, correo, 
			numDocumento, from_unixtime(fechaDocumento, '%d/%m/%Y') 'fec Docum.', from_unixtime(fecha, '%d/%m/%Y %H:%i:%s') fec, from_unixtime(c.fechaAltaCimex, '%d/%m/%Y %H:%i:%s') fecCimex, CP,
			(select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais,
			case activo when 1 then 'Si' else 'No' end 'activo?',
			(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum',
			(select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf'
		from tbl_aisCliente c
		where c.activo = 1
			and (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) > 0
			and c.fecha between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') and paisResidencia in (" . $d['pais'] . ")
		order by fecha desc";
	?>
		document.getElementById('activos').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('activos').checked = false;
	<?php
	}

	if ($d['nuevos']) {
		$enc = $q[] = "select concat('- (',usuario, ') ',nombre,' ',papellido,' ',sapellido,' %') cadena, id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario, correo, 
			numDocumento, from_unixtime(fechaDocumento, '%d/%m/%Y') 'fec Docum.', from_unixtime(fechaAltaCimex, '%d/%m/%Y %H:%i:%s') fecCimex, CP,
			(select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais,
			case activo when 1 then 'Si' else 'No' end 'activo?',
			(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum',
			(select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf'
		from tbl_aisCliente c
		where c.fechaAltaCimex between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') 
			and paisResidencia in (" . $d['pais'] . ")
			and (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) > 0
		order by fecha desc";
	?>
		document.getElementById('nuevos').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('nuevos').checked = false;
	<?php
	}

	if ($d['actualizados']) {
		$enc = $q[] = "select concat('- (',usuario, ') ',nombre,' ',papellido,' ',sapellido,' %') cadena, id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario, correo, 
			numDocumento, from_unixtime(fechaDocumento, '%d/%m/%Y') 'fec Docum.', from_unixtime(c.fecha, '%d/%m/%Y %H:%i:%s') fechaActualizado, from_unixtime(fechaAltaCimex, '%d/%m/%Y %H:%i:%s') fecCimex, CP,
			(select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais,
			case activo when 1 then 'Si' else 'No' end 'activo?',
			(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum',
			(select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf'
		from tbl_aisCliente c
		where c.fecha between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') 
			and paisResidencia in (" . $d['pais'] . ")
			and (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) > 0
		order by fecha desc";
	?>
		document.getElementById('actualizados').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('actualizados').checked = false;
	<?php
	}

	if ($d['operaciones']) {
		$fec = time() - 60 * 60 * 24 * 60;
		$enc = $q[] = "select t.idtransaccion, t.identificador, o.titOrdenId, format((t.valor/100),2) 'valor',
		t.moneda, c.idcimex 'IdCli', concat(c.nombre, ' ', c.papellido) cliente, c.usuario, c.correo, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido)beneficiario, from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') 'fecha', from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i:%s') 'fechaMod', t.estado, t.id_error as error
	from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b
	where t.idtransaccion = o.idtransaccion
		and b.id = o.idbeneficiario
		and c.id = o.idcliente
		and t.fecha_mod between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') and c.paisResidencia in (" . $d['pais'] . ")
	order by t.fecha_mod desc";
	?>
		document.getElementById('operaciones').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('operaciones').checked = false;
	<?php
	}

	if ($d['operacionesd']) {
		$fec = time() - 60 * 60 * 24 * 60;
		$enc = $q[] = "select t.idtransaccion, t.identificador, o.titOrdenId, format((t.valor/100),2) 'valor',
		t.moneda, c.idcimex 'IdCli', concat(c.nombre, ' ', c.papellido) cliente, c.usuario, c.correo, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido)beneficiario, from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') 'fecha', from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i:%s') 'fechaMod', t.estado, d.observacion as motivo
	from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_devoluciones d
	where t.idtransaccion = o.idtransaccion
		and d.idtransaccion = t.idtransaccion
		and b.id = o.idbeneficiario
		and c.id = o.idcliente and t.estado in ('B', 'V')
		and t.fecha_mod between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') and c.paisResidencia in (" . $d['pais'] . ")
	order by t.fecha_mod desc";
	?>
		document.getElementById('operacionesd').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('operacionesd').checked = false;
	<?php
	}
	
	if ($d['operacioneensd']) {
		$fec = time() - 60 * 60 * 24 * 60;
		$enc = $q[] = "select t.idtransaccion, t.identificador, o.titOrdenId, format((t.valor/100),2) 'valor',
		t.moneda, c.idcimex 'IdCli', concat(c.nombre, ' ', c.papellido) cliente, c.usuario, c.correo, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido)beneficiario, from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') 'fecha', from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i:%s') 'fechaMod', t.estado, d.observacion as motivo 
	from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_devoluciones d
	where t.idtransaccion = o.idtransaccion
		and d.idtransaccion = t.idtransaccion
		and b.id = o.idbeneficiario
		and c.id = o.idcliente and t.soldev = 1
	order by t.fecha_mod desc";
	?>
		document.getElementById('operacioneensd').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('operacioneensd').checked = false;
	<?php
	}

	if ($d['operacionesg']) {
		$fec = time() - 60 * 60 * 24 * 60;
		$enc = $q[] = "select t.idtransaccion, t.identificador, o.titOrdenId, format((t.valor/100),2) 'valor',
		t.moneda, c.idcimex 'IdCli', concat(c.nombre, ' ', c.papellido) cliente, c.usuario, c.correo, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido)beneficiario, from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') 'fecha', from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i:%s') 'fechaMod', t.estado, convert(cast(convert(t.id_error using latin1) as binary) using utf8) as error
	from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b
	where t.idtransaccion = o.idtransaccion
		and b.id = o.idbeneficiario
		and c.id = o.idcliente and t.estado in ('D', 'N', 'P')
		and t.fecha_mod between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') and c.paisResidencia in (" . $d['pais'] . ")
	order by t.fecha_mod desc";
	?>
		document.getElementById('operacionesg').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('operacionesg').checked = false;
	<?php
	}

	if ($d['operacionesa']) {
		$fec = time() - 60 * 60 * 24 * 60;
		$enc = $q[] = "select t.idtransaccion, t.identificador, o.titOrdenId, format((t.valor/100),2) 'valor',
		t.moneda, c.idcimex 'IdCli', concat(c.nombre, ' ', c.papellido) cliente, c.usuario, c.correo, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido)beneficiario, from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') 'fecha', from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i:%s') 'fechaMod', t.estado, t.id_error as error
	from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b
	where t.idtransaccion = o.idtransaccion
		and b.id = o.idbeneficiario
		and c.id = o.idcliente and t.estado = 'A'
		and t.fecha_mod between unix_timestamp('{$arrfecs[2]}-{$arrfecs[1]}-{$arrfecs[0]} 00:00:00') and unix_timestamp('{$arrfecs2[2]}-{$arrfecs2[1]}-{$arrfecs2[0]} 23:59:59') and c.paisResidencia in (" . $d['pais'] . ")
	order by t.fecha_mod desc";
	?>
		document.getElementById('operacionesa').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('operacionesa').checked = false;
	<?php
	}

	if ($d['operdoc']) {
		$enc = $q[] = "select t.idtransaccion, t.identificador, o.titOrdenId, format((t.valor/100),2) 'valor',
		t.moneda, c.idcimex 'IdCli', concat(c.nombre, ' ', c.papellido) cliente, c.usuario, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido) beneficiario,
		from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') 'fecha', from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i:%s') 'fechaMod', t.id_error as error
	from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b
	where t.idtransaccion = o.idtransaccion
		and b.id = o.idbeneficiario
		and c.id = o.idcliente
		and t.id_error like '%scan documentation%'
	order by t.fecha_mod desc
	limit 0,500";
	?>
		document.getElementById('operdoc').checked = 'checked';
	<?php
	} else {
	?>
		document.getElementById('operdoc').checked = false;
	<?php
	}



	foreach ($q as $value) {
		echo "</script>" . ejec($value);
		// echo $value."<br>";
	}

	function ejec($vale) {
		// echo $vale."<br>";
		$x=1;
		$temp = new ps_DB;
		// if ($x!=2)
		$temp->query($vale);
		// else $x++;
		(stripos($vale, 'correoenv')) ? $pos = 1 : $pos = 0;

		if ($temp->getErrorNum()) {
			return $temp->getErrorMsg();
			exit;
		}

		$cant = $temp->num_rows();
		// error_log($cant);

		$sale = "<span >Cant: $cant</span>";
		$sale .= "<span class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='Descarga CSV' title='Descarga CSV'></span><table cellpadding=5 cellspacing=0 border=1 style='font-size:10px;'>";
		$rows = $temp->loadAssocList();
		//			print_r($rows);
		$sale .= "<tr>";
		foreach ($rows[0] as $key => $value) {
			$sale .= "<th>$key</th>";
			if ($key == 'ip') $sale .= "<th>país</th>";
		}
		$sale .= "</tr>";
		foreach ($rows as $row) {
			$sale .= "<tr>";
			$i = 0;
			foreach ($row as $key => $data) {
				$data = str_replace('submit()', '', $data);
				$data = str_replace('width: 550px;', 'width: 550px;display:none;', $data);
				$data = str_replace('<script', '<scr|', $data);
				$data = str_replace('<!--', '', $data);
				$data = str_replace('//-->', '', $data);
				$data = str_replace('-->', '', $data);
				if ($pos == 1 && $i == 0) {
					$sale .= "<td class='espec' id='$data'>" . $data . "</td>";
					echo "";
					$i++;
				} else {
					if (stripos($data, '@')) {
						$sale .= "<td><a href='mailto:$data?subject:Notificación de www.aisremesascuba.com'>" . $data . "</a></td>";
						echo "";
					} else {
						$sale .= "<td>" . $data . "</td>";
						echo "";
					}
				}
				if ($key == 'ip') if (function_exists("geoip_country_name_by_name")) $sale .= "<td>" . geoip_country_name_by_name($data) . "</td>";
				else $sale .= "<td>" . $data . "</td>";
				echo "";
			}
			$sale .= "</tr>";
		}
		$sale .= '</table><br>';
		return $sale;
	}

	?>
		<form name = "exporta" action = "admin/impresion.php" method = "POST" >
		<input type = "hidden" name = "querys12" value = "<?php echo $enc; ?>" >
		<input type = "hidden" name = "fecha1" value = "<?php echo $fec; ?>" >
		<input type = "hidden" name = "pag" value = "reporte" >
		</form>   <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" >
</script>
<script>
	$(document).ready(function() {

		// $("#botesp").click(function(){
		// 	$("#tuto").hide();
		// 	$("#prob").show();
		// });


		// $("#enviaR").click(function (e) { 
		// 	e.preventDefault();
		// 	alert($("#listadito").val());

		// 	$.post('ejecc.php',{
		// 		rem: $("#listadito").val(),
		// 		func: 'rem'
		// 	},function(data) {
		// 		var datos = eval('(' + data + ')');
		// 		if(datos.lim == 1) window.location.href = window.location.href + "?envc=1&fecha=<?php echo $d['fecha']; ?>&fecha2=<?php echo $d['fecha2']; ?>";
		// 	});
		// });


		$(".espec").click(function() { //Desmarca en los clientes el envío de correo
			$.post('ejecc.php', {
				idss: $(this).attr("id"),
				func: 'actli'
			}, function(data) {
				var datos = eval('(' + data + ')');
				if (datos.lim == 1) window.location.href = window.location.href + "?envc=1&fecha=<?php echo $d['fecha']; ?>&fecha2=<?php echo $d['fecha2']; ?>";
			});
		});
	});
</script>