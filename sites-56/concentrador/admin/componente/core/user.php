<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

$admin_mod = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file('componente/core/admin.html.php'));
$admin_mod = str_replace("{_FORM_SEARCH}", _FORM_SEARCH, $admin_mod);
$admin_mod = str_replace("{_FORM_CANCEL}", _FORM_CANCEL, $admin_mod);
$admin_mod = str_replace("{_FORM_SEND}", _FORM_SEND, $admin_mod);
$contenido = explode('{muestra}', $admin_mod);

global $temp;
$ent = new entrada;
$corCreo = new correo;
$html = new tablaHTML;
global $send_m;

//var_dump($_SESSION);

if (stripos(_ESTA_URL, 'localhost') > 0) {
// $_POST['componente'] = 'core';
// $_POST['pag'] = 'user';
// $_POST['dosmd2'] = '';
// $_POST['unomd2'] = '';
// $_POST['inserta'] = '1';
// $_POST['nombre'] = 'Ariana Sanchez';
// $_POST['correo'] = 'finanzas@lex-sa.cu';
// $_POST['login'] = 'finanzas';
// $_POST['grupo'] = '12';
// $_POST['activo'] = 'S';
// $_POST['enviar'] = 'Enviar';
}

//var_dump($_SESSION);

if ($ent->isReal($_POST['inserta'])) {
	if (!($email = $ent->isCorreo($_POST['correo']))) $cadError = 'Falló la modificación de los Datos, el correo no es válido.';
	if (!($nombre = $ent->isAlfanumerico($_POST['nombre']))) $cadError = 'Falló la modificación de los Datos, el nombre no es válido.';
	if (!($usuario = $ent->isAlfanumerico($_POST['login']))) $cadError = 'Falló la modificación de los Datos, el nombre de usuario no es válido.';
	if (!($grupo = $ent->isReal($_POST['grupo']))) $cadError = 'Falló la modificación de los Datos, el rol no es válido.';
	if (!($comercio = $ent->isAlfanumerico(implode(",", $_POST['comercio'])))) $cadError = 'Falló la modificación de los Datos, el comercio no es válido.';
	if ($comercio == '' || $comercio == null) $cadError = "Falló la modificación de Datos. Debe seleccionar un comercio";
	if (!($activo = $ent->isAlfabeto($_POST['activo']))) $cadError = 'Falló la modificación de los Datos, activo no es válido.';
	if ($usuario == 'prueba') $cadError = 'Falló la modificación de los Datos, el nombre de usuario no es válido.';
	$recla = $ent->isEntero($_POST['recla']);
	$vcuc = $_POST['pago'];

	$query = "select count(*) valor from tbl_admin where login = '$usuario'";
	$temp->query($query);
	if ($cadError == '') {
		if ($temp->f('valor') == 0 && $cadError == '') {
			$contras = validaContrasena($usuario);
			
			//Venta en CUC
			$qs = '0';
			$query = "select count(id) total from tbl_comercio where idcomercio in ($comercio) and usarTasaCuc != 0";
			$temp->query($query);
			if ($temp->f('total') > 0 && $vcuc == 1) $qs = "1";
			elseif ($temp->f('total') == 0 && $vcuc == 1) 
				echo "<script type='text/javascript'>alert('Aunque usted marcó que el usuario podía realizar la venta en CUP el comercio no lo tiene permitido')</script>";
	
			$q = "insert into tbl_admin (nombre, idrol, email, ip, login, activo, md5, param, idcomercio, fecha, fechaPass, ventacuc, reclamaciones)
					values ('$nombre', ".$grupo.", '$email', 'inicio', '$usuario', '".$activo."', '".$contras[1]."', 'idioma=spanish\n', '".$comercio."', ".time().", ".time().", $qs, '$recla')";
			if (!$cadError) $temp->query($q);
			$idAdmin = $temp->last_insert_id();
			
			if ($comercio == 'todos') 
				$q = "select id from tbl_comercio";
			else 
				$q = "select id from tbl_comercio where idcomercio in ($comercio)";
			$temp->query($q);
			$arrCom = $temp->loadResultArray();
			$comercio = implode(",", $arrCom);
			
			for ($i=0;$i<$tod = count($arrCom); $i++){
				$q = "insert into tbl_colAdminComer (idAdmin, idComerc, fecha) values ($idAdmin, $arrCom[$i], ".time().")";
	// 			echo $q;
				$temp->query($q);
			}
			
			$subject = 'Concedido acceso a la Administración '.$GLOBALS['titulo_sitio'];
			$corCreo->to($email);
			$imprim = "<div style=\"text-align:center; font-family:Arial san-serif; font-size:11px\">"._PERSONAL_TEXTO1."<br>
						<br>"._PERSONAL_IDENT.": ".$usuario."<br>"._PERSONAL_PASS.": ".$contras[0]."
						<br><br>La Documentación podrá ser descargada desde la página de entrada</div>";
	
	        $corCreo->todo(21, $subject, $imprim);
		} else {
			 $cadError = 'El nombre de usuario está registrado. Deberá escoger otro nombre de usuario.';
		}
	}
}

if ($modifica = $ent->isReal($_POST['modifica'])) {
	if (!($email = $ent->isCorreo($_POST['correo']))) $cadError = 'Falló la modificación de los Datos, el correo no es válido.';
	if (!($nombre = $ent->isAlfanumerico($_POST['nombre']))) $cadError = 'Falló la modificación de los Datos, el nombre no es válido.';
	if (!($usuario = $ent->isAlfanumerico($_POST['login']))) $cadError = 'Falló la modificación de los Datos, el nombre de usuario no es válido.';
	if (!($grupo = $ent->isReal($_POST['grupo']))) $cadError = 'Falló la modificación de los Datos, el rol no es válido.';
	if (!($comercio = $ent->isAlfanumerico(implode(",", $_POST['comercio'])))) $cadError = 'Falló la modificación de los Datos, el comercio no es válido.';
	if ($comercio == '' || $comercio == null) $cadError = "Falló la modificación de los Datos. Debe seleccionar un comercio";
	if (!($activo = $ent->isAlfabeto($_POST['activo']))) $cadError = 'Falló la modificación de los Datos, activo no es válido.';
	$recla = $ent->isEntero($_POST['recla']);
	$vcuc = $_POST['pago'];
	
	$strComer = $comercio;
	if ($cadError == '') {
		if ($comercio == 'todos'){
			$q = "select id from tbl_comercio";
			$temp->query($q);
			$arrCom = $temp->loadResultArray();
		} else {
			$q = "select id from tbl_comercio where idcomercio in ($comercio)";
			$temp->query($q);
			$arrCom = $temp->loadResultArray();
			if (strpos($comercio, ',') > -1){
				$comercio = 'varios';
			}
		}
	
		//actualiza datos en la tabla admin
		$q = "update tbl_admin set nombre = '$nombre', idrol = ".$grupo.", reclamaciones = '$recla', email = '$email', activo = '".$activo."', idcomercio = '". $comercio."' ";
		
		//puede vender o no en CUC
		$query = "select count(id) total from tbl_comercio where idcomercio in ($strComer) and usarTasaCuc != 0";
		$temp->query($query);
		if ($temp->f('total') > 0 && $vcuc == 1) $q .= ", ventacuc = 1";
		elseif ($temp->f('total') == 0 && $vcuc == 1) 
				echo "<script type='text/javascript'>alert('Aunque usted marcó que el usuario podía realizar la venta en CUP el comercio no lo tiene permitido')</script>";
	
		if ($ent->isAlfabeto($_POST['pass'], 1) == 'S'){
			$contras = validaContrasena($usuario);
			$q .= ", login = '$usuario', md5Old = md5, md5 = '".$contras[1]."', fechaPass = ".mktime(0, 0, 0, date("m"), date("d"), date("Y"))." ";
		}
		$q .= " where idadmin = ".$modifica;
		$temp->query($q);
		
		//actualiza datos en la tabla colAdminComer
		$q = "delete from tbl_colAdminComer where idAdmin = $modifica";
		$temp->query($q);
		for ($i=0;$i<$tod = count($arrCom); $i++){
			$q = "insert into tbl_colAdminComer (idAdmin, idComerc, fecha) values ($modifica, $arrCom[$i], ".time().")";
	//			echo $q."<br>";
			$temp->query($q);
		}
	
		if (strlen($usuario) > 0 && strlen($contras[0]) > 0) {
	//		echo "entra";
			$subject = 'Renovación de acceso a la Administración '.$GLOBALS['titulo_sitio'];
			$corCreo->to($email);
			$imprim = '<div style=\"text-align:center; font-family:Arial san-serif; font-size:11px\">'._PERSONAL_TEXTO1.'<br>
						<br>'._PERSONAL_IDENT.': '.$usuario.'<br>'._PERSONAL_PASS.': '.$contras[0].'</div>';
			
			$corCreo->todo(22, $subject, $imprim);
	//echo $imprim;
		}
	}
}

if ($borrar = $ent->isReal($_POST['borrar'])) {
	$q = "update tbl_admin set activo = 'N', email = '' where idadmin = ".$borrar;
	$temp->query($q);
}

$titulo = _USUARIO_TITULO;
$ancho = '600';
// var_dump($_SESSION);
if (!$ent->isReal($_POST['cambiar'])) {

	$titulo_tarea = _TAREA_INSERTAR.' '._USUARIO_TITULO;
	$tripa .= '<table width="100%" cellspacing="0" cellpadding="0">
				<input name="inserta" type="hidden" value="1" />
				<tr><td class="derecha">'._FORM_NAME.':</td><td class="izquierda"><input class="formul" name="nombre" id="nombre" type="text" /></td>
					<td class="sepr"></td><td class="derecha">'._FORM_CORREO.':</td><td class="izquierda">
							<input class="formul" name="correo" id="correo" type="text" /></td></tr>
				<tr><td class="derecha">'._PERSONAL_IDENT.':</td><td class="izquierda"><input class="formul" name="login" id="login" type="text" /></td>
					<td class="sepr"></td><td class="derecha">'._GRUPOS_GRUPO.':</td><td class="izquierda"><select class="formul" name="grupo">';
    $quita = '';
    if ($_SESSION['grupo_rol'] == 10) {
        $quita = " and idrol not in (20,21)";
    }

	$tripa .= opciones_sel("select idrol as id, nombre from tbl_roles where orden >= ".$_SESSION['grupo_rol']."$quita order by orden");
	$tripa .= '</select></td></tr><tr>';
	$tripa .= '<td class="derecha">'._COMERCIO_TITULO.':</td><td class="izquierda">';
	if ($_SESSION['comercio'] == 'todos') {
		$tripa .= '<select class="formul" name="comercio[]" multiple="true" size="4">';
		$tripa .= '<option selected value="todos">-TODOS-</option>';
		$tripa .= opciones_sel("select idcomercio as id, nombre from tbl_comercio order by nombre", '');
		$tripa .= '</select>';
	} else {
		$tripa .= '<select class="formul" name="comercio[]" multiple="true" size="4">';
		$tripa .= opciones_sel("select idcomercio id, nombre from tbl_comercio where id in (".$_SESSION['idcomStr'].") order by nombre", '');
		$tripa .= '</select>';
//	echo "select idcomercio id, nombre from tbl_comercio where id in (".$_SESSION['idcomStr'].") order by nombre";
	}
	$tripa .= '</td><td class="sepr"></td><td class="derecha">'._USUARIO_ACTIVO.':<br><br>Pagos en CUP:<br><br><br>Atenci&oacute;n Reclam.:</td><td class="izquierda">
    		<label for="radios"><input checked="checked" id="radios" type="radio" name="activo" value="S" /> '._FORM_YES.'</label>
			&nbsp;&nbsp;&nbsp;
    		<label for="radion"><input id="radion" type="radio" name="activo" value="N" /> '._FORM_NO.'</label></select><br><br>
			<label for="pagos"><input id="pagos" type="radio" name="pago" value="1" /> '._FORM_YES.'</label>
			&nbsp;&nbsp;&nbsp;
    		<label for="pagon"><input checked="checked" id="pagon" type="radio" name="pago" value="0" /> '._FORM_NO.'</label></select><br><br>
			<label for="recls"><input id="recls" type="radio" name="recla" value="1" /> '._FORM_YES.'</label>
			&nbsp;&nbsp;&nbsp;
    		<label for="reclno"><input checked="checked" id="reclno" type="radio" name="recla" value="0" /> '._FORM_NO.'</label></select></td></tr><tr>
    				<td width="50%" colspan="2">
				</td><td class="sepr"></td><td width="50%" colspan="2"></td></tr></table>';
} else {
	
	$qg = ' select a.idadmin, a.nombre, a.login, a.idcomercio, a.email, r.idrol, a.activo, a.ventacuc, a.reclamaciones '
        . ' from tbl_admin a , tbl_roles r'
		. ' where r.idrol = a.idrol'
		. ' and a.idadmin = '.$ent->isReal($_POST['cambiar']);
	$temp->query($qg);
	
	$titulo_tarea = _TAREA_MODIFICAR.' '._USUARIO_TITULO;
	$tripa .= '<table width="100%" cellspacing="0" cellpadding="0">
				<input name="modifica" type="hidden" value="'.$ent->isReal($_POST['cambiar']).'" />
				<tr>
					<td class="derecha">'._FORM_NAME.':</td>
					<td class="izquierda">
						<input class="formul" value="'.$temp->f('nombre').'" name="nombre" id="nombre" type="text" />
					</td>
					<td class="sepr"></td>
					<td class="derecha">'._FORM_CORREO.':</td>
					<td class="izquierda">
						<input class="formul" value="'.$temp->f('email').'" name="correo" id="correo" type="text" />
					</td>
				</tr>
				<tr>
					<td class="derecha">'._PERSONAL_IDENT.':</td>
					<td class="izquierda">
						<input class="formul" value="'.$temp->f('login').'" name="login" id="login" type="text" />
					</td>
					<td class="sepr"></td>
					<td class="derecha">'._GRUPOS_GRUPO.':</td>
					<td class="izquierda">
						<select class="formul" name="grupo">';
    $quita = '';
    if ($_SESSION['grupo_rol'] == 10) $quita = " and idrol not in (20,21)";
	$tripa .= opciones_sel("select idrol as id, nombre from tbl_roles where orden >= ".$_SESSION['grupo_rol']."$quita order by orden", $temp->f('idrol'));
			$tripa .= '</select>
					</td>
				</tr>
				<tr>
					<td class="derecha">'._USUARIO_BORRA_PASS.':</td>
					<td class="izquierda">
					<label for="passs"><input onClick="alert(\''._USUARIO_BORRA_PASS_ALERT.'\')" id="passs" type="radio" name="pass" value="S" /> '.
					_FORM_YES.'</label>&nbsp;&nbsp;&nbsp;<label for="passn">
					<input onClick="document.getElementById(\'clave\').value=\'\'" checked="checked" id="passn" type="radio" name="pass" value="N" /> '.
					_FORM_NO.'</label>';
		$tripa .= '</td><td class="sepr"></td>
					<td class="derecha">'._USUARIO_ACTIVO.':<br><br>Hace oper. en CUP:<br><br>Atiende Reclam.:</td>
					<td class="izquierda">';

	
		$tripa .= '<label for="radios"><input ';
	if ($temp->f('activo') == 'S')	$tripa .= 'checked="checked" ';
		$tripa .= 'id="radios" type="radio" name="activo" value="S" /> '._FORM_YES.'</label>
			&nbsp;&nbsp;&nbsp;
    		<label for="radion"><input ';
	if ($temp->f('activo') == 'N')	$tripa .= 'checked="checked" ';
		$tripa .= 'id="radion" type="radio" name="activo" value="N" /> '._FORM_NO.'</label></select><br><br>
				
		<label for="pagos"><input ';
if ($temp->f('ventacuc') == 1) $tripa .= 'checked="checked" ';
$tripa .= 'id="pagos" type="radio" name="pago" value="1" /> '._FORM_YES.'</label>
	&nbsp;&nbsp;&nbsp;
	<label for="pagon"><input ';
if ($temp->f('ventacuc') == 0) $tripa .= 'checked="checked" ';
$tripa .= 'id="pagon" type="radio" name="pago" value="0" /> '._FORM_NO.'</label></select><br><br>
				
<label for="recls"><input ';
if (($temp->f('reclamaciones')*1) > 0) $tripa .= 'checked="checked" ';
$tripa .= 'id="recls" type="radio" name="recla" value="1" /> '._FORM_YES.'</label>
&nbsp;&nbsp;&nbsp;
<label for="recln"><input ';
if (($temp->f('reclamaciones')*1) == 0) $tripa .= 'checked="checked" ';
$tripa .= 'id="recln" type="radio" name="recla" value="0" /> '._FORM_NO.'</label></select></td></tr>
			';
	$tripa .= '<tr><td class="derecha">'._COMERCIO_TITULO.':</td><td class="izquierda">';
	if ($_SESSION['comercio'] == 'todos') {
		$q = "select idcomercio as id, nombre from tbl_comercio order by nombre";
		$tripad = '<option value="todos">-TODOS-</option>';
	} else {
		$q = "select idcomercio as id, nombre from tbl_comercio where id in (".$_SESSION['idcomStr'].") order by nombre";
		$tripad = '';
	}

	$qs = "select idcomercio from tbl_comercio c, tbl_colAdminComer a where a.idComerc = c.id and a.idAdmin = ".$_POST['cambiar'];
	$temp->query($qs);
	$arrdCom = $temp->loadResultArray();
	$cadCom = '';
	foreach ($arrdCom as $item => $value) {
		$cadCom .= $value.',';
	}
	$cadCom = rtrim($cadCom, ',');
	
	$tripa .= '<select class="formul" name="comercio[]" multiple="true" size="4">'.$tripad;
	$tripa .= opciones_sel_Arr($q, explode(",", $cadCom));
	$tripa .= '</select>';
	
	$tripa .= '</td><td class="sepr"></td><td class="derecha"></td><td class="izquierda"></td></tr>
			';
//	$tripa .= '<tr><td class="derecha"></td><td class="izquierda"></td><td class="sepr"></td><td class="derecha">'._USUARIO_BORRA_PASS.':</td>
//	<td class="izquierda"><label for="passs"><input onClick="alert(\''._USUARIO_BORRA_PASS_ALERT.'\')" id="passs" type="radio" name="pass" value="S" /> '
//._FORM_YES.'
//	</label>&nbsp;&nbsp;&nbsp;<label for="passn"><input checked="checked" id="passn" type="radio" name="pass" value="N" /> '._FORM_NO.'</label></select>
//</td></tr>
//	<tr><td width="50%" colspan="2"></td><td class="sepr"></td><td width="50%" colspan="2"></td></tr>';
    $tripa .= '</table>';
}


echo str_replace('{titulo}', $titulo, $contenido[0]);
$contenido[2] = str_replace('{ancho_tabla}', $ancho, $contenido[2]);
$contenido[2] = str_replace('{titulo_tarea}', $titulo_tarea, $contenido[2]);
$contenido[2] = str_replace('{tripa}', $tripa, $contenido[2]);
$contenido = str_replace('{anchoCelda}', ($ancho-14), $contenido);

echo "
<script language=\"JavaScript\" src=\"../js/md5.js\" type=\"text/javascript\"></script>
<script language=\"JavaScript\" type=\"text/javascript\">
function verifica() {
	if (
			(checkField (document.admin_form.nombre, isAlphanumeric, ''))&&
			(checkField (document.admin_form.comercio, isAlphanumeric, ''))&&
			(checkField (document.admin_form.correo, isEmail, ''))&&
			(checkField (document.admin_form.login, isAlphanumeric, ''))
		) {
		return true;
	}
	return false;
}

function suggestPassword() {
    var pwchars = \"abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ\";
    var passwordlength = 16;    // do we want that to be dynamic? no, keep it simple :)
    var passwd = document.getElementById('clave');
    passwd.value = '';

    for ( i = 0; i < passwordlength; i++ ) {
        passwd.value += pwchars.charAt( Math.floor( Math.random() * pwchars.length ) )
    }
    return passwd.value;
}</script>";

echo $contenido[2];
//print_r($_SESSION);

if (stripos($_SESSION['idcomStr'],',') ) {
	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = "";
	$html->tituloTarea = "Buscar";
	$html->hide = true;
	$html->anchoTabla = 500;
	$html->anchoCeldaI = 170; $html->anchoCeldaD = 320;
	
	$html->inHide(true, 'buscr');
	$html->inTextb(_FORM_NAME, "", "nombre");
	$html->inTextb(_FORM_CORREO, "", "correo");
	$html->inTextb(_PERSONAL_IDENT, "", "usuario");
    $quita = '';
    if ($_SESSION['grupo_rol'] == 10) $quita = " and idrol not in (20,21)";
	$q = "select idrol as id, nombre from tbl_roles where orden >= ".$_SESSION['grupo_rol']."$quita order by orden";
	$html->inSelect("Grupo", "grupo", 5, $q);
	$comer = $_SESSION['idcomStr'];
	if (strpos ($comer, ",")) {
		$query = "select id, nombre from tbl_comercio where id in (".$comer.") and activo = 'S' order by nombre";
	//		echo $query;
		$html->inSelect(_COMERCIO_TITULO, 'come', 5, $query,  str_replace(",", "', '", $comercId), null, null, "multiple size='5'");
	} else $html->inHide ($comercId, 'come');
	//$arrAct = array(array('',"Cualquiera"),array(1,"Activo"),array(0,"Inactivo"));
	//$html->inSelect("Estado", "estado", 3, $arrAct,"");
	echo $html->salida(null,null,true);
}

$vista = "select distinct a.idadmin as id, a.nombre,a.login, a.email, r.nombre as rol, a.activo, a.fecha_visita,
			case when a.idcomercio = 'todos' then 'todos' when a.idcomercio = 'varios' then 'varios' else 
				(select nombre from tbl_comercio where idcomercio = a.idcomercio) end comercio"
        . ' from tbl_admin a , tbl_roles r, tbl_comercio c, tbl_colAdminComer l ';
$where = 'where r.idrol = a.idrol and c.id = l.idComerc and l.idAdmin = a.idadmin and r.orden >='.$_SESSION['grupo_rol'] ;

if ($_SESSION['grupo_rol'] >= 4) {
	$where .= " and r.orden not in ( 18 , 5)";
}

if ($_REQUEST['buscr']) {
	if (is_array($_REQUEST['come'])) $come = "'".implode(",", $_REQUEST['come'])."'";
	else {
		if (strlen($_REQUEST['come']) == 0) $come = $comer;
		else $come = $_REQUEST['come'];
	}
	$where .= " and c.id in (".$come.") and a.nombre like '%".$_REQUEST['nombre']."%' and a.email like '%".$_REQUEST['correo']."%' and a.login like '%".
					$_REQUEST['usuario']."%' and r.idrol in ('".$_REQUEST['grupo']."') ";
} else {
//echo $_SESSION['idcomStr'];
	if ($_SESSION['comercio'] != 'todos') {
		$where .= ' and c.id in ('.$_SESSION['idcomStr'].') ' ;
	}
}
$orden = 'a.activo desc, a.fecha desc';
	

$colEsp = array(
				array("e", "Editar Datos", "css_edit", "Editar"),
				array("b", "Borrar Registro", "css_borra", "Borrar"));

$busqueda = array();

$columnas = array(
				array(_FORM_NOMBRE, "nombre", "120", "center", "left" ),
				array(_MENU_ADMIN_COMERCIO, "comercio", "100", "center", "left" ),
				array(_GRUPOS_GRUPO, "rol", "100", "center", "left"),
				array(_PERSONAL_IDENT, "login", "", "center", "left"),
				array(_FORM_CORREO, "email", "", "center", "left"),
				array(_USUARIO_ACTIVO,"activo","","center","center"),
				array(_USUARIO_FECHA_ULTIMA,"fecha_visita","","center","center"));

$ancho = 900;

	echo "<div style='float:left; width:100%' ><table class='total1' width=\"$ancho\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
		<tr>
			<td><span style='float:right' class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='"._REPORTE_CSV."' title='"._REPORTE_CSV."'></span></td>
		</tr>
	</table></div>";

//echo $vista.$where." order by ".$orden;
	tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );

if ($cadError)
	echo '<script language="JavaScript" type="text/javascript">
			alert("'.$cadError.'");
		</script>';
?>
<form name="exporta" action="impresion.php" method="POST">
	<input type="hidden" name="querys4" value="<?php echo $vista.$where." order by ".$orden ?>">
</form>
