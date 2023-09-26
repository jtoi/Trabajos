<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/comercio/comercio.html.php'));
$partes = explode('{corte1}', $admin_mod);
$temp = @ new ps_DB;


$d = $_POST;

if ($d['insertar']) {

	$cf = file( '../configuration.php' );
	foreach ($cf as $k=>$v) {
		if (eregi( '_CORREO_SITE', $v)) {
			$cf[$k] = "define (_CORREO_SITE, \"".$d['correoContacto']."\");\n";
		} elseif (eregi( '_MOS_CONFIG_DEBUG', $v)) {
			$cf[$k] = "define (_MOS_CONFIG_DEBUG, '".$d['debug']."');\n";
		} elseif (eregi( '_PALABR_OFUS', $v)) {
			$cf[$k] = "define (_PALABR_OFUS, \"".$d['palabra']."\");\n";
		} elseif (eregi( '_CONTRASENA_OFUS', $v)) {
			$cf[$k] = "define (_CONTRASENA_OFUS, \"".$d['contrasena']."\");\n";
		} elseif (eregi( '_ID_COMERCIO', $v)) {
			$cf[$k] = "define (_ID_COMERCIO, \"".$d['comercio']."\");\n";
		} elseif (eregi( '_ID_PTO', $v)) {
			$cf[$k] = "define (_ID_PTO, \"".$d['punto']."\");\n";
		} elseif (eregi( '_3DPALABR_OFUS', $v)) {
			$cf[$k] = "define (_3DPALABR_OFUS, \"".$d['3dpalabra']."\");\n";
		} elseif (eregi( '_3DCONTRASENA_OFUS', $v)) {
			$cf[$k] = "define (_3DCONTRASENA_OFUS, \"".$d['3dcontrasena']."\");\n";
		} elseif (eregi( '_3DID_COMERCIO', $v)) {
			$cf[$k] = "define (_3DID_COMERCIO, \"".$d['3dcomercio']."\");\n";
		} elseif (eregi( '_3DID_PTO', $v)) {
			$cf[$k] = "define (_3DID_PTO, \"".$d['3dpunto']."\");\n";
		} elseif (eregi( '_TESTPALABR_OFUS_TEST', $v)) {
			$cf[$k] = "define (_TESTPALABR_OFUS_TEST, \"".$d['palabra_test']."\");\n";
		} elseif (eregi( '_TESTCONTRASENA_OFUS', $v)) {
			$cf[$k] = "define (_TESTCONTRASENA_OFUS_TEST, \"".$d['contrasena_test']."\");\n";
		} elseif (eregi( '_TESTID_COMERCIO_TEST', $v)) {
			$cf[$k] = "define (_TESTID_COMERCIO_TEST, \"".$d['comercio_test']."\");\n";
		} elseif (eregi( '_TESTID_PTO_TEST', $v)) {
			$cf[$k] = "define (_TESTID_PTO_TEST, \"".$d['punto_test']."\");\n";
		} elseif (eregi( '_SABADEL_URL_PROD', $v)) {
			$cf[$k] = "define (_SABADEL_URL_PROD, \"".$d['urlSabProd']."\");\n";
		} elseif (eregi( '_SABADEL_URL_DESA', $v)) {
			$cf[$k] = "define (_SABADEL_URL_DESA, \"".$d['urlSabTest']."\");\n";
		} elseif (eregi( '_SABADEL_CLAVE_PROD', $v)) {
			$cf[$k] = "define (_SABADEL_CLAVE_PROD, \"".$d['claveSabProd']."\");\n";
		} elseif (eregi( '_SABADEL_CLAVE_DESA', $v)) {
			$cf[$k] = "define (_SABADEL_CLAVE_DESA, \"".$d['claveSabDesa']."\");\n";
/*		} elseif (eregi( '_LOCALIZADOR', $v)) {
			$cf[$k] = "define (_LOCALIZADOR, \"".$d['localizador']."\");\n";
		} elseif (eregi( '_URL_COMERCIO', $v)) {
			$cf[$k] = "define (_URL_COMERCIO, \"".$d['comercioUrl']."\");\n";
		} elseif (eregi( '_URL_DIR', $v)) {
			$cf[$k] = "define (_URL_DIR, \"".$d['dirUrl']."\");\n";
		} elseif (eregi( '_URL_TPV', $v)) {
			$cf[$k] = "define (_URL_TPV, \"".$d['tpvUrl']."\");\n";*/
		} elseif (eregi( '_MESES_BACKUP', $v)) {
			$cf[$k] = "define (_MESES_BACKUP, ".$d['meses'].");\n";
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
			window.open(\'index.php?componente=comercio&pag=setup&ids=1\', \'_self\');
		</script>';
	include('../configuration.php');

}

$contenido = $partes[0].$partes[1].$partes[2].$partes[3];

$contenido .= '
<script language="JavaScript" type="text/javascript">
function verifica() {
	if (
			(checkField (document.admin_form.correoContacto, isEmail, \'\'))&&
			(checkField (document.admin_form.contrasena, isAlphanumeric, \'\'))
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
<tr><td>
<table style="display:\'\'" id="Contenido" width="100%" border="0" cellspacing="0" cellpadding="0">
<input name="insertar" type="hidden" value="true">
			<tr>
				<td width="50%" class="derecha">Debug:</td>
				<td class="izquierda"><label><input type="radio" ';
				if (_MOS_CONFIG_DEBUG == '1') $contenido .= 'checked';
					$contenido .= ' name="debug" value="1" /> Si</label>&nbsp;&nbsp;
									<label><input type="radio" ';
				if (_MOS_CONFIG_DEBUG == '') $contenido .= 'checked';
					$contenido .= ' name="debug" value="" /> No</label>
				</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_EMAIL_CONT.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="info@caribbeanonlineweb.com" name="correoContacto" /></td>
			</tr>
			<tr>
				<td width="50%" style="font-weight:bold; font-size:1.2em; text-align:center;" colspan="2">BBVA:</td>
			</tr>
			<tr>
				<td width="50%" style="font-weight:bold; font-size:1.2em; text-align:center;" colspan="2">'._SETUP_DATOS_TPV.':</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_PALABR_OFUS.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._PALABR_OFUS.'" name="palabra" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_CONTRASENA.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="password" value="'._CONTRASENA_OFUS.'" name="contrasena" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_COMERCIO.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._ID_COMERCIO.'" name="comercio" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_PUNTO.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._ID_PTO.'" name="punto" /></td>
			</tr>
			<tr>
				<td width="50%" style="font-weight:bold; font-size:1.2em; text-align:center;" colspan="2">BBVA 3D:</td>
			</tr>
			<tr>
				<td width="50%" style="font-weight:bold; font-size:1.2em; text-align:center;" colspan="2">'._SETUP_DATOS_TPV.':</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_PALABR_OFUS.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._3DPALABR_OFUS.'" name="palabra" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_CONTRASENA.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="password" value="'._3DCONTRASENA_OFUS.'" name="contrasena" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_COMERCIO.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._3DID_COMERCIO.'" name="comercio" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_PUNTO.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._3DID_PTO.'" name="punto" /></td>
			</tr>
			<tr>
				<td width="50%" style="font-weight:bold; font-size:1.2em; text-align:center;" colspan="2">BBVA Pruebas:</td>
			</tr>
			<tr>
				<td width="50%" style="font-weight:bold; font-size:1.2em; text-align:center;" colspan="2">'._SETUP_DATOS_TPV_TEST.':</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_PALABR_OFUS.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._TESTPALABR_OFUS_TEST.'" name="palabra_test" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_CONTRASENA.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="password" value="'._TESTCONTRASENA_OFUS_TEST.'" name="contrasena_test" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_COMERCIO.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._TESTID_COMERCIO_TEST.'" name="comercio_test" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_PUNTO.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._TESTID_PTO_TEST.'" name="punto_test" /></td>
			</tr>
			
			<tr>
				<td width="50%" style="font-weight:bold; font-size:1.2em; text-align:center;" colspan="2">Sabadel:</td>
			</tr>
			<tr>
				<td width="50%" style="font-weight:bold; font-size:1.2em; text-align:center;" colspan="2">'._SETUP_DATOS_TPV.':</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">URL:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._SABADEL_URL_PROD.'" name="urlSabProd" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Clave:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._SABADEL_CLAVE_PROD.'" name="claveSabProd" /></td>
			</tr>
			<tr>
				<td width="50%" style="font-weight:bold; font-size:1.2em; text-align:center;" colspan="2">'._SETUP_DATOS_TPV_TEST.':</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">URL:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._SABADEL_URL_DESA.'" name="urlSabTest" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Clave:</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._SABADEL_CLAVE_DESA.'" name="claveSabDesa" /></td>
			</tr>




			<!--<tr>
				<td width="50%" class="derecha">'._SETUP_LOCALIZADOR.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._LOCALIZADOR.'" name="localizador" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_URL_COMERCIO.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._URL_COMERCIO.'" name="comercioUrl" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_URL_DIR.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._URL_DIR.'" name="dirUrl" /></td>
			</tr>
			<tr>
				<td width="50%" class="derecha">'._SETUP_URL_TPV.':</td>
				<td class="izquierda"><input maxlength="150" class="formul" type="text" value="'._URL_TPV.'" name="tpvUrl" /></td>
			</tr>-->
			<tr>
				<td width="50%" class="derecha">'._SETUP_MESES.':</td>
				<td class="izquierda"><select class="formul" name="meses" >';
$contenido .= opciones(1, 20, _MESES_BACKUP);
$contenido .= '	</select></td>
			</tr>
<!--			<tr>
				<td width="50%" class="derecha">Banca:</td>
				<td class="izquierda"><label><input type="radio" ';
				if (_TPV_UTILIZADO == 1) $contenido .= 'checked';
					$contenido .= ' name="banca" value="1" /> La Caixa</label>&nbsp;&nbsp;<br>
									<label><input type="radio" ';
				if (_TPV_UTILIZADO == 2) $contenido .= 'checked';
					$contenido .= ' name="banca" value="2" /> Caja Madrid</label>&nbsp;&nbsp;<br>
									<label><input type="radio" ';
				if (_TPV_UTILIZADO == 3) $contenido .= 'checked';
					$contenido .= ' name="banca" value="3" /> El Monte</label>
				</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">Modo de funcionamiento pasarela:</td>
				<td class="izquierda"><label><input type="radio" ';
				if (_MODO_TPV_FUNCIONAMIENTO == 1) $contenido .= 'checked';
					$contenido .= ' name="pasarela" value="1" /> Modo Prueba local</label><br />
    								<label><input type="radio" ';
				if (_MODO_TPV_FUNCIONAMIENTO == 2) $contenido .= 'checked';
					$contenido .= ' name="pasarela" value="2" /> Modo Prueba Internet</label><br />
    								<label><input type="radio" ';
				if (_MODO_TPV_FUNCIONAMIENTO == 3) $contenido .= 'checked';
					$contenido .= ' name="pasarela" value="3" /> Modo producci&oacute;n</label><br />
				</td>
			</tr>
			<tr>
				<td width="50%" class="derecha">D?as de adelanto del pago:</td>
				<td class="izquierda"><input onMouseOver="return overlib(\'Cantidad m?nima de d?as antes de hacerse efectiva la reserva para realizar el pago, cero equivale a no realizar el pago en la web. La opci?n anterior debe estar activa.\', ABOVE, RIGHT);" onMouseOut="return nd();" size="7" maxlength="6" class="formul" type="text" value="'._MOS_DIAS_ADELANTO.'" name="diasAdel" /> d?as</td>
			</tr>
			<tr>
				<td width="50%" align="center" colspan="2">Necesitar&aacute; volver a refrescar la p&aacute;gina para visualizar los cambios realizados.</td>
			</tr>-->
		</table></td></tr>
';

$contenido .= $partes[11].$partes[12].$partes[13].$partes[14];
$ancho = 500;
$contenido = str_replace('{titulo}', _SETUP_TITLE, $contenido);
$contenido = str_replace('{tabed}', '', $contenido);
$contenido = str_replace('{javascript}', '', $contenido);
$contenido = str_replace('{campo}', '', $contenido);
$contenido = str_replace('{_FORM_SEND}', _FORM_SEND, $contenido);
$contenido = str_replace('{_FORM_CANCEL}', _FORM_CANCEL, $contenido);
$contenido = str_replace('{titulo_tarea}', _TAREA_MODIFICAR.' '._SETUP_TITLE, $contenido);
$contenido = str_replace('{ancho_tabla}', $ancho, $contenido);
$contenido = str_replace('{anchoCelda}', ($ancho-14), $contenido);

echo $contenido;
?>
