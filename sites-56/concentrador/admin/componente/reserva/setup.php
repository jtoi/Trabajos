<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/reserva/reserv.html.php'));
$partes = explode('{corte1}', $admin_mod);
$temp = @ new ps_DB;


$d = $_REQUEST;


if ($d['insertar']) {
	for ($x=1;$x<4;$x++) {
		if ($d['grupo_edad_'.$x.'_fin'] == '') $salida = 100;
		else $salida = $d['grupo_edad_'.$x.'_fin'];
		$sql = "update tbl_edad set edadin = ".$d['grupo_edad_'.$x.'_ini'].", edadout = $salida where idedad = $x";
		$temp->query($sql);
	}

	for ($x=0; $x < count($d['idhabord']); $x++){
		$query = "update tbl_habitacion set orden = ".$d['ordenhab'][$x]." where idhabitacion = ".$d['idhabord'][$x];
		$temp->query($query);
	}

	$cf = file( '../configuration.php' );
	foreach ($cf as $k=>$v) {
		if (eregi( '_DIAS_NO_SHOW', $v)) {
			$cf[$k] = "define (\"_DIAS_NO_SHOW\", 0);\n";
		} else if (eregi( '_VALOR_NO_SHOW', $v)) {
			$cf[$k] = "define (\"_VALOR_NO_SHOW\", 0);\n";
		} else if (eregi( '_CORREO_RESERVAS', $v)) {
			$cf[$k] = "define (\"_CORREO_RESERVAS\", \"".$d['correoReserva']."\");\n";
		} else if (eregi( '_CORREO_CONTACTO', $v)) {
			$cf[$k] = "define (\"_CORREO_CONTACTO\", \"".$d['correoContacto']."\");\n";
		} else if (eregi( '_MOS_PAGO_DIFERIDO', $v)) {
			$cf[$k] = "define (\"_MOS_PAGO_DIFERIDO\", ".$d['pagoDif'].");\n";
		} else if (eregi( '_CORREO_EMPLEO', $v)) {
			$cf[$k] = "define (\"_CORREO_EMPLEO\", \"".$d['correoEmpleo']."\");\n";
		} else if (eregi( '_CORREO_GRUPO', $v)) {
			$cf[$k] = "define (\"_CORREO_GRUPO\", \"".$d['correoGrupo']."\");\n";
		} else if (eregi( '_TPV_UTILIZADO', $v)) {
			$cf[$k] = "define (\"_TPV_UTILIZADO\", ".$d['banca'].");\n";
		} else if (eregi( '_MOS_TAX_VALOR', $v)) {
			$cf[$k] = "define (\"_MOS_TAX_VALOR\", ".$d['impuestoVal'].");\n";
		} else if (eregi( '_MODO_TPV_FUNCIONAMIENTO', $v)) {
			$cf[$k] = "define (\"_MODO_TPV_FUNCIONAMIENTO\", \"".$d['pasarela']."\");\n";
		} else if (eregi( '_HABITACIONES_MAX', $v)) {
			$cf[$k] = "define (\"_HABITACIONES_MAX\", ".$d['habitaciones'].");\n";
		} else if (eregi( '_MOS_CONFIG_DEBUG', $v)) {
			$cf[$k] = "define (\"_MOS_CONFIG_DEBUG\", '".$d['debug']."');\n";
		} else if (eregi( '_MOS_TAX', $v)) {
			$cf[$k] = "define (\"_MOS_TAX\", ".$d['impuesto'].");\n";
		}
	}
	$fichero = fopen( '../configuration.php', 'w' );
	fclose($fichero);

	$fichero = fopen( '../configuration.php', 'a+' );
	for ($k=0; $k<count($cf); $k++){
		fwrite ( $fichero, $cf[$k] );
	}
	fclose($fichero);
	echo '<script language="JavaScript" type="text/javascript">
			window.open(\'index.php?componente=reserva&pag=setup&ids=1\', \'_self\');
		</script>';
}

$tripa = '
<script language="JavaScript" type="text/javascript">
function verifica() {
	if (
//			(checkField (document.admin_form.diasNoShow, isReal, \'\'))&&
//			(checkField (document.admin_form.porcNoShow, isReal, \'\'))&&
			(checkField (document.admin_form.correoReserva, isEmail, \'\'))&&
			(checkField (document.admin_form.correoContacto, isEmail, \'\'))&&
			(checkField (document.admin_form.correoGrupo, isEmail, \'\'))&&
			(checkField (document.admin_form.correoEmpleo, isEmail, \'\'))&&
			(checkField (document.admin_form.habitaciones, isReal, \'\'))&&
			(checkField (document.admin_form.grupo_edad_1_ini, isReal, \'\'))&&
			(checkField (document.admin_form.grupo_edad_2_ini, isReal, \'\'))&&
			(checkField (document.admin_form.grupo_edad_3_ini, isReal, \'\'))&&
			(checkField (document.admin_form.grupo_edad_1_fin, isReal, \'\'))&&
			(checkField (document.admin_form.grupo_edad_2_fin, isReal, \'\'))&&
			(checkField (document.admin_form.impuestoVal, isMoney, \'\', \'Sólo se admite un número que represente porciento.\'))
		)
		return true;
	else return false;
}

function calculo(elemento) {
	punto = elemento.id.split(\'_\');
	if (punto[2] != 3) {
		idel = \'grupo_edad_\'+(punto[2]*1+1)+\'_ini\';
		document.getElementById(idel).value = elemento.value;
	}
}
</script>
<table style="display:\'\'" id="Contenido" width="100%" border="0" cellspacing="0" cellpadding="0">
<input name="insertar" type="hidden" value="true">
			<tr>
				<td width="50%" class="derecha">Debug:</td>
				<td class="izquierda"><label><input type="radio" ';
				if (_MOS_CONFIG_DEBUG == '1') $tripa .= 'checked';
					$tripa .= ' name="debug" value="1" /> Si</label>&nbsp;&nbsp;
									<label><input type="radio" ';
				if (_MOS_CONFIG_DEBUG == '') $tripa .= 'checked';
					$tripa .= ' name="debug" value="" /> No</label>
				</td>
			</tr>
<!--			<tr>
				<td width="50%" class="derecha">D&iacute;as de antelaci&oacute;n a la fecha de entrada para aplicar NO SHOW:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._DIAS_NO_SHOW.'" name="diasNoShow" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Porciento de descuento para NO SHOW:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._VALOR_NO_SHOW.'" name="porcNoShow" /></td>
			</tr>
-->			<tr>
				<td width="50%" class="derecha">Correo de la Reserva:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._CORREO_RESERVAS.'" name="correoReserva" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Correo Contacto:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._CORREO_CONTACTO.'" name="correoContacto" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Correo Grupo:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._CORREO_GRUPO.'" name="correoGrupo" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Correo Empleo:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._CORREO_EMPLEO.'" name="correoEmpleo" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">N&uacute;mero m&aacute;ximo de habitaciones por reserva:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._HABITACIONES_MAX.'" name="habitaciones" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Banca:</td>
				<td class="izquierda"><label><input type="radio" ';
				if (_TPV_UTILIZADO == 1) $tripa .= 'checked';
					$tripa .= ' name="banca" value="1" /> La Caixa</label>&nbsp;&nbsp;<br>
									<label><input type="radio" ';
				if (_TPV_UTILIZADO == 2) $tripa .= 'checked';
					$tripa .= ' name="banca" value="2" /> Caja Madrid</label>&nbsp;&nbsp;<br>
									<label><input type="radio" ';
				if (_TPV_UTILIZADO == 3) $tripa .= 'checked';
					$tripa .= ' name="banca" value="3" /> El Monte</label>
				</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Modo de funcionamiento pasarela:</td>
				<td class="izquierda"><label><input type="radio" ';
				if (_MODO_TPV_FUNCIONAMIENTO == 1) $tripa .= 'checked';
					$tripa .= ' name="pasarela" value="1" /> Modo Prueba local</label><br />
    								<label><input type="radio" ';
				if (_MODO_TPV_FUNCIONAMIENTO == 2) $tripa .= 'checked';
					$tripa .= ' name="pasarela" value="2" /> Modo Prueba Internet</label><br />
    								<label><input type="radio" ';
				if (_MODO_TPV_FUNCIONAMIENTO == 3) $tripa .= 'checked';
					$tripa .= ' name="pasarela" value="3" /> Modo producci&oacute;n</label><br />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table width="80%" align="center" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="33%" align="center"></td>
							<td width="33%" align="center">Inicio</td>
							<td width="33%" align="center">Fin</td>
						</tr>
';
$sql = "select * from tbl_edad order by edadin";
$temp->query($sql);

while ($temp->next_record()) {
	$x = $temp->f('idedad');
	$pal1 = $temp->f('edadin');
	if ($temp->f('edadout') == 100) $pal2 = '';
	else $pal2 = $temp->f('edadout');

	$tripa .= '<tr>
					<td width="33%" class="derecha">Grupo de Edad '.$x.':</td>
					<td width="33%"  align="center"><input ';
	if ($x == 1) $tripa .= 'readonly="true"';
	$tripa .= ' maxlength="3" size="7" class="formul" type="text" value="'.$pal1.'" name="grupo_edad_'.$x.'_ini" id="grupo_edad_'.$x.'_ini" /> a&ntilde;os</td>
					<td width="33%"  align="center"><input onblur="javascript:calculo(this)" maxlength="3" size="7" class="formul" type="text" value="'.$pal2.'" name="grupo_edad_'.$x.'_fin" id="grupo_edad_'.$x.'_fin" /> a&ntilde;os</td>
				</tr>';
}
$tripa .= '</table>
		</td>
			</tr>
			<tr>
				<td class="derecha">&#191;Aplicar cálculo de impuesto a los precios?</td>
				<td class="izquierda">
				<label><input class="formul" id="impuesto1" type="radio" value="1" ';
				if (_MOS_TAX == 1) $tripa .= 'checked';
					$tripa .= ' name="impuesto" onMouseOver="return overlib(\'Si hace esta elección y el valor del impuesto es > 0, se realiza el gravámen del impuesto en al cálculo de los precios.\', BELOW, RIGHT);" onMouseOut="return nd();" /> Si</label><br>
				<label><input class="formul" id="impuesto0" type="radio" value="0" ';
				if (_MOS_TAX == 0) $tripa .= 'checked';
					$tripa .= ' name="impuesto" onMouseOver="return overlib(\'Si hace esta elección y el valor del impuesto es > 0, no se realiza el gravámen del impuesto en al cálculo de los precios, pero aparece en la entrada de datos un alerta para que el cliente sepa que deberá realizar ese pago posteriormente en carpeta.\', BELOW, RIGHT);" onMouseOut="return nd();" /> No</label>
				</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Valor del Impuesto:</td>
				<td class="izquierda"><input size="7" maxlength="6" class="formul" type="text" value="'._MOS_TAX_VALOR.'" name="impuestoVal" onMouseOver="return overlib(\'Si el valor es > 0 ver opciones anteriores, si es cero, no se realiza ningún gravámen ni aparece alerta en la entrada de datos.\', BELOW, RIGHT);" onMouseOut="return nd();" /> %</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Activar pago diferido:</td>
				<td class="izquierda">
				<label><input class="formul" id="pagoDif1" type="radio" value="1" ';
				if (_MOS_PAGO_DIFERIDO == 1) $tripa .= 'checked';
					$tripa .= ' name="pagoDif" onMouseOver="return overlib(\'Permite la realización del pago diferido.\', BELOW, RIGHT);" onMouseOut="return nd();" /> Si</label><br>
				<label><input class="formul" id="pagoDif0" type="radio" value="0" ';
				if (_MOS_PAGO_DIFERIDO == 0) $tripa .= 'checked';
					$tripa .= ' name="pagoDif" onMouseOver="return overlib(\'No permite la realización del pago diferido, la cantidad de días mínimo de adelanto se fija en la siguiente variable.\', BELOW, RIGHT);" onMouseOut="return nd();" /> No</label>
				</td>
			</tr>
			<tr>
				<td width="50%" align="center" colspan="2"><strong>Orden de las habitaciones en el Planing.</strong></td>
			</tr>';
$query = "select idhabitacion, identificacion, orden from tbl_habitacion where idhotel = ".$_REQUEST['ids']." order by orden";
$temp->query($query);
$cantHab = $temp->num_rows();
for ($x = 0; $x < $cantHab; $x++) {
$temp->next_record();
$tripa .=	'<tr>
				<td width="50%" class="derecha">'.$temp->f('identificacion').':<input type="hidden" name="idhabord[]" value="'.$temp->f('idhabitacion').'"></td>
				<td class="izquierda"><input size="2" maxlength="1" type="text" name="ordenhab[]" value="'.$temp->f('orden').'"></td>
			</tr>';

}
$tripa .=	'<input type="hidden" id="canthabOrd" value="'.$cantHab.'">
			<!--<tr>
				<td width="50%" class="derecha">Días de adelanto del pago:</td>
				<td class="izquierda"><input onMouseOver="return overlib(\'Cantidad mínima de días antes de hacerse efectiva la reserva para realizar el pago, cero equivale a no realizar el pago en la web. La opción anterior debe estar activa.\', ABOVE, RIGHT);" onMouseOut="return nd();" size="7" maxlength="6" class="formul" type="text" value="'._MOS_DIAS_ADELANTO.'" name="diasAdel" /> días</td>
			</tr>
			<tr>
				<td width="50%" align="center" colspan="2">Necesitar&aacute; volver a refrescar la p&aacute;gina para visualizar los cambios realizados.</td>
			</tr>-->
		</table>';

$contenido = $partes[0].$tripa.$partes[10].$partes[12];

$contenido = str_replace('{titulo}', 'Setup', $contenido);
$contenido = str_replace('{tabed}', '', $contenido);
$contenido = str_replace('{campo}', '', $contenido);
$contenido = str_replace('{titulo_tarea}', 'Modificar Valores', $contenido);

echo $contenido;
?>