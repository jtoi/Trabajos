<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );


//genera un codigo de 20 caracteres
//retorna un array con dos elementos
//el primero de 11 caracteres de largo
//el segundo de 18
function generaCod() {
	list($usec, $sec) = explode(" ", microtime());
	return (array ((microtime(true)*100), str_replace('0.', '', $sec.$usec)));
}

function generaCodEmp() {
    $eti = str_replace('.', '', (string)microtime(false));
	return (time().substr($eti, 1, 2));
}

//borra de las tablas de los idiomas los titulos y descripciones
function borra_idioma($clave) {
	$temp = &new ps_DB;
	$sel_idioma = "select titulo from tbl_idioma order by ididioma";
	$temp->query($sel_idioma);

	while ($temp->next_record()){
		$nombre_idioma[++$j] = $temp->f('titulo');
	}

	for ($num = 0; $num < count($clave); $num++) {
		for ($x=1; $x<=count($nombre_idioma); $x++) {
			$idiomas = "delete from tbl_idioma_".strtolower($nombre_idioma[$x])." where ididioma = ".$clave[$num];
			$temp->query($idiomas);

			$idiomas = "delete from tbl_idioma_".strtolower($nombre_idioma[$x])." where ididioma = ".$clave[$num];
			$temp->query($idiomas);
		}
	}
}

//borra subdirectorios y todo su contenido
function borra_dir($dir) {
	if (false !== ($handle = opendir($dir))) {
		$files = glob($dir .'*.{???}', GLOB_BRACE);
		for ($x=0; $x<count($files); $x++) unlink($files[$x]);
	}
    closedir($handle);
	rmdir($dir);
}

//retorna un float convertido en moneda
function moneda($num) {
	return number_format($num, 2, '.', ' ');
}

//returns safe code for preloading in the RTE
function RTESafe($strText) {

	$tmpString = '';

	$tmpString = trim($strText);

	//convert all types of single quotes
	$tmpString = str_replace(chr(145), chr(39), $tmpString);
	$tmpString = str_replace(chr(146), chr(39), $tmpString);
	$tmpString = str_replace("'", "&#39;", $tmpString);

	//convert all types of double quotes"
	$tmpString = str_replace(chr(147), chr(34), $tmpString);
	$tmpString = str_replace(chr(148), chr(34), $tmpString);
//	$tmpString = replace($tmpString, """", "\""")

	//replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ", $tmpString);
	$tmpString = str_replace(chr(13), " ", $tmpString);

	return $tmpString;
}

//genera pares de options en base a una query
//las columnas en la query pasada deben tener como alias
//id y nombre
function opciones_sel($select, $id = null) {
	$cadena_orden = '';

	$grup = new ps_DB;
	$grup->query($select);
	while ($grup->next_record()){
		if ($grup->f('id') == $id)
			$cadena_orden .= '<option selected value="'.$grup->f('id').'">'.$grup->f('nombre').'</option>'."\n";
		else
			$cadena_orden .= '<option value="'.$grup->f('id').'">'.$grup->f('nombre').'</option>'."\n";
	}
	return $cadena_orden;
}

//genera pares de options en base a arrays pasados
function opciones_arr ($valores_arr, $val_sel) {
	$cadena_orden = '';
	for ($x = 0; $x < count($valores_arr); $x++) {
		if ($valores_arr[$x][0] == $val_sel)
			$cadena_orden .= '<option selected="true" value="'.$valores_arr[$x][0].'">'.$valores_arr[$x][1].'</option>'."\n";
		else
			$cadena_orden .= '<option value="'.$valores_arr[$x][0].'">'.$valores_arr[$x][1].'</option>'."\n";
	}
	return $cadena_orden;
}


//genera pares de options en base a dos n&uacute;meros
function opciones($inicio, $final, $id) {
	$cadena_orden = '';
	for ($x=$inicio; $x<=$final; $x++){
		if ($x == $id) $cadena_orden .= '<option selected value="'.$x.'">'.$x.'</option>'."\n";
		else $cadena_orden .= '<option value="'.$x.'">'.$x.'</option>'."\n";
	}
	return $cadena_orden;
}


//Hace el login al sitio de administraci&oacute;n
function verifica_entrada(){
	$calc_md5 = md5($_POST['dosmd2'].$_POST['unomd2']);
// error echo $calc_md5;
	$ini = new ps_DB;
	$idh = new ps_DB;

	$q = "select idadmin, a.nombre, a.idrol, r.orden, a.idcomercio, a.email
		from tbl_admin a, tbl_roles r
		where a.idrol = r.idrol
		and activo = 'S'
		and md5 = '$calc_md5'";

	$ini->query($q);
	if ($ini->next_record()){
		$_SESSION['id'] = $ini->f('idadmin');
		$_SESSION['admin_nom'] = $ini->f('nombre');
		$_SESSION['comercio'] = $ini->f('idcomercio');
		$_SESSION['rol'] = $ini->f('idrol');
		$_SESSION['grupo_rol'] = $ini->f('orden');
		$_SESSION['email'] = $ini->f('email');

		$query = "update tbl_admin set fecha_visita = ".time()." where idadmin = ".$_SESSION['id'];
		$idh->query($query);

		$query = "select visitado from tbl_admin where idadmin = ".$_SESSION['id'];
		$idh->query($query);
		$pagina_ult = $idh->f('visitado');
		return 'componente=comercio&pag=inicio';
	}
	return false;
}

//construye las tablas de resultados
//Tabla de muestra de resultados
function tabla( $tabla_ancho, $idioma='E', $vista, $orden='', $busquedaInicio='', $colEsp='', $busqueda='', $columnas) {
/*
tabla_ancho - ancho de la tabla que muestra los resultados
idioma - Idioma de entrada, permite prefijar valores de mensajes que de otra forma habr&iacute;a que entrarlos
vista - Vista de la BD contra la cual se realizar&aacute; todo el trabajo
orden -  ordenamiento de los valores de salida de la vista por default
busquedaInicio - Columnas de la vista a buscar por defult
colEsp - array de array para las columnas especiales (editar, ver, borrar..) en la forma (tipocolumna, textoalt, caminoimagen, titulocolumna)
busqueda - array de array para las busquedas en la forma (nombrecampo, columnatabla)
columnas - array de array con los datos de (titulocolumna, campotabla, anchocolumna, posicion)
*/

//$idioma = 'I';
if ($_SESSION['idioma'] == "english") {
	$alerta = "You are about to delete a record and all the data asociated with.\\nAre you sure?";
	$alerta2 = "Are you sure to cancel the transaction?\\nThis operation can`t be reverted.";
	$alerta3 = "The transaction must be Accepted or Refounded and or has a value bigger than 0.";
} else {
	$alerta = "Se borrar&aacute; este registro y todos los datos asociados a &eacute;l.\\nEst&aacute; seguro?";
	$alerta2 = "Está usted seguro de anular la transacción?\\nEsta operación no puede ser revertida.";
	$alerta3 = "La transacción debe ser Aceptada o Devuelta y tener valor mayor que 0.";
}

?>
  <form action="<?php echo $GLOBALS['sitio_url'].'index.php?'. $_SERVER['QUERY_STRING'] ?>" method="post" name="pag" id="pag">
<table align="center" width="<?php echo $tabla_ancho; ?>" border="0" cellspacing="0" cellpadding="0">
<?php
  			if (strlen($_REQUEST["orden"]) > 0) $ordenar = $_REQUEST["orden"];
			else $ordenar = $orden;

			$buscarStr = $busquedaInicio;
			if (strlen($_REQUEST["buscar"]) > 0) $buscarStr = $_REQUEST["buscar"];

			for ($cont = 0; $cont<=count($busqueda); $cont++) {
				if (strlen($busqueda[$cont]) > 0 && strlen($buscarStr) > 0) $buscarStr .= " and ".$busqueda[$cont];
				elseif (strlen($busqueda[$cont]) > 0 and strlen($buscarStr) == 0) $buscarStr = "where ".$busqueda[$cont];
			}

			$limite = 30;

			if (strlen($_REQUEST["btnPageNext"]) > 0 && strlen($_REQUEST["cmbPageSelect"]) > 0 )
				$mPage = $_REQUEST["cmbPageSelect"] + 1;
			elseif (strlen($_REQUEST["btnPagePrev"]) > 0 && strlen($_REQUEST["cmbPageSelect"]) > 0 )
				$mPage = $_REQUEST["cmbPageSelect"] - 1;
			elseif (strlen($_REQUEST["cmbPageSelect"]) > 0 && strlen($_REQUEST["btnPagePrev"]) == 0 && strlen($_REQUEST["btnPageNext"]) == 0 )
				$mPage = $_REQUEST["cmbPageSelect"];
			else
				$mPage = 1;

			$nPage = ($mPage - 1) * $limite;

  			$sql = stripslashes($vista." ".$buscarStr." order by ".$ordenar. " limit $nPage, $limite");
  			$sql_cont = stripslashes($vista." ".$buscarStr." order by ".$ordenar);

		    $usr = new ps_DB;
			$usr->query($sql_cont);
			$num_records = $usr->num_rows();

			$usr->query($sql);
			$cantColumnas = (count($columnas) * 2 + count($colEsp) * 2) - 1;
			$usr->reset();
//echo $sql;
		 ?>
		<input name="cmbPageSelect" type="hidden">
		<input name="orden" type="hidden" value="<?php echo $_REQUEST["orden"]; ?>">
		<input name="buscar" type="hidden" value="<?php echo stripslashes($buscarStr); ?>">
		<input name="borrar" type="hidden">
		<input name="cambiar" type="hidden">
      <tr>
      <td colspan="<?php echo $cantColumnas; ?>"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr class="encabezamiento">
          <td width="100" align="left">&nbsp;<?php echo $num_records; ?> Record(s)</td>
          <td align="center">Pages:
            <?php

	  if (($mPage - 4) < 1) $inicio = 1;
	  else $inicio = $mPage - 3;

	  if ($num_records >= (($mPage + 3) * $limite)) $final = $mPage + 3;
	  else $final = ceil($num_records/$limite);

	  for ($x = $inicio; $x <= $final; $x++) {
		if ($mPage == $x) {
			echo "<label style=\"font-size:11px \"><strong> ";
			echo $x."</strong></label>&nbsp;";
		}else {
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=$x; document.pag.submit()\" href=\"#\"> ";
			echo "$x</a>&nbsp;";
		}
	}
	 ?></td>
          <td width="120" align="right"><?php

		if ($mPage != 1)
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage-1) ."; document.pag.submit()\" href=\"#\">&lt;&lt;..</a>"; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
		if ($mPage != ceil($num_records/$limite))
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage+1) ."; document.pag.submit()\" href=\"#\">..&gt;&gt;</a>"; ?>
</td>
        </tr>
      </table>
        </td>
      </tr>
    <tr>
      <td height="1" colspan="<?php echo $cantColumnas; ?>"></td>
    </tr>
    <tr class="encabezamiento2">
	<?php
//columnas normales
	 for ($cont = 0; $cont < count($columnas); $cont++) {
		if ($cont != 0) {
		?>
		<td width="1" class="separador1"></td>
		<?php } ?>
		<td class="celdas" width="<?php echo $columnas[$cont][2] ?>" align="<?php echo $columnas[$cont][3] ?>"><a class="paglink" href="#" onClick="document.pag.orden.value = '<?php
		if (ereg($ordenar, $columnas[$cont][1]." desc")) echo $columnas[$cont][1]." asc";
		else echo $columnas[$cont][1]." desc";
		?>'; document.pag.submit();"><?php echo $columnas[$cont][0]; ?></a><?php
		if (ereg($ordenar, $columnas[$cont][1]." desc")) echo "<img src=\"../images/down.gif\" width=\"15\" height=\"16\" />";
		elseif (ereg($ordenar, $columnas[$cont][1]." asc")) echo "<img src=\"../images/up.gif\" width=\"15\" height=\"16\" />";
		?></td>
	<?php }

//columnas especiales
	for ($cont = 0; $cont < count($colEsp); $cont++) { ?>
	<td width="1" class="separador1"></td>
    <td class="celdas" width="50" align="center">&nbsp;<?php echo $colEsp[$cont][3]; ?>&nbsp;</td>
	<?php } ?>
  </tr>

  <?php
	while($usr->next_record()) { ?>
  <tr class="cuerpo" id="campo<?php echo $usr->f("id") ?>" onMouseOver="cambia(this.id, 'over');" onMouseOut="cambia(this.id, '')">
  <?php for ($cont = 0; $cont < count($columnas); $cont++) {
  	if ($cont != 0) { ?>
	    <td width="1" class="separador2"></td>
	<?php } ?>
    <td style="padding-left:7px; padding-right:7px; padding-top:4px; padding-bottom:4px" align="<?php echo $columnas[$cont][4]; ?>"><?php
	if (eregi('fecha', $columnas[$cont][1])) {
		if ( $usr->f($columnas[$cont][1]) != 0 )
			echo date('d/m/Y H:i:s', $usr->f($columnas[$cont][1]));
		else echo '-';
	}
	elseif ( eregi('{val}',  $columnas[$cont][1])) echo money_format('%.2n',  $usr->f($columnas[$cont][1]));
    elseif ( eregi('{col}',  $columnas[$cont][1])) {
        $jav = "<script>
                    document.getElementById('campo".$usr->f("id")."').style.color='".$usr->f($columnas[$cont][1])."';
                </script>";
        echo $jav;
    }
	else
	echo $usr->f($columnas[$cont][1]);
	 ?></td>
	<?php }
	for ($cont = 0; $cont < count($colEsp); $cont++) {
		if ($colEsp[$cont][0] == "e" || $colEsp[$cont][0] == "v")
			$oncli = "document.pag.cambiar.value='". $usr->f("id") ."'; envia(this.form);";
		elseif ($colEsp[$cont][0] == "d")
			$oncli = "return alerta(".$usr->f("valor{val}").", '".$usr->f("estado")."',  '".$usr->f("id")."', 'R' );";
		elseif ($colEsp[$cont][0] == "i")
			$oncli = "window.open('imprimeest.asp?us=". $usr->f("id") ."','nueva','menubar=no,scrollbars=yes')";
		elseif ($colEsp[$cont][0] == "b")
			$oncli = "if (confirm('". $alerta ."')) {document.pag.borrar.value='". $usr->f("id") ."'; envia(this.form)}";
		elseif ($colEsp[$cont][0] == "c")
			$oncli = "return alerta(".$usr->f("valor{val}").", '".$usr->f("estado")."',  '".$usr->f("id")."', 'A' );";
		?>
    <td width="1" class="separador2"></td>
    <td align="center"><input src="<?php echo $colEsp[$cont][2] ?>" name="u" type="image" onClick="<?php echo $oncli ?>" alt="<?php echo $colEsp[$cont][1] ?>" /></td>
    <?php } ?>
  </tr><?php
}
?>
<script language="JavaScript" type="text/JavaScript">
function cambia(renglon, acc) {
	if (acc == 'over') document.getElementById(renglon).bgColor='#CCCCCC';
	else document.getElementById(renglon).bgColor='';
}

function alerta(valor, estado, id, accion) {

	if (valor > 0 && (estado == 'V' || estado == 'A' )) {
		if (accion == 'A') {
			if (confirm('<?php echo $alerta2 ?>')) {
				document.pag.borrar.value = id;
				envia(this.form);
				return true;
			}
		} else {
			document.pag.cambiar.value=id;
			envia(this.form);
			return true;
		}
	} else {
		alert('<?php echo $alerta3 ?>');
	}
	return false;
}
</script>
</table></form>
<?php
return $sql;
}

//construye las tablas de resultados
//Tabla de muestra de resultados
function tablanp( $query, $columnas) {
/*
tabla_ancho - ancho de la tabla que muestra los resultados
idioma - Idioma de entrada, permite prefijar valores de mensajes que de otra forma habr&iacute;a que entrarlos
vista - Vista de la BD contra la cual se realizar&aacute; todo el trabajo
orden -  ordenamiento de los valores de salida de la vista por default
busquedaInicio - Columnas de la vista a buscar por defult
colEsp - array de array para las columnas especiales (editar, ver, borrar..) en la forma (tipocolumna, textoalt, caminoimagen, titulocolumna)
busqueda - array de array para las busquedas en la forma (nombrecampo, columnatabla)
columnas - array de array con los datos de (titulocolumna, campotabla, anchocolumna, posicion)
*/

?>
  <form action="<?php echo $GLOBALS['sitio_url'].'index.php?'. $_SERVER['QUERY_STRING'] ?>" method="post" name="pag" id="pag">
<table align="center" width="<?php echo $tabla_ancho; ?>" border="0" cellspacing="0" cellpadding="0">
<?php

		    $usr = new ps_DB;
			$usr->query($query);
			$num_records = $usr->num_rows();

			$usr->query($query);
			$cantColumnas = (count($columnas) * 2 + count($colEsp) * 2) - 1;
			$usr->reset();

		 ?>
      <tr>
      <td colspan="<?php echo $cantColumnas; ?>"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr class="encabezamiento">
          <td width="100" align="left">&nbsp;<?php echo $num_records; ?> Record(s)</td>
          <td align="center"></td>
          <td width="120" align="right"></td>
        </tr>
      </table>
        </td>
      </tr>
    <tr>
      <td height="1" colspan="<?php echo $cantColumnas; ?>"></td>
    </tr>
    <tr class="encabezamiento2">
	<?php
//columnas normales
	 for ($cont = 0; $cont < count($columnas); $cont++) {
		if ($cont != 0) {
		?>
		<td width="1" class="separador1"></td>
		<?php } ?>
		<td class="celdas" width="<?php echo $columnas[$cont][2] ?>" align="<?php echo $columnas[$cont][3] ?>"><?php echo $columnas[$cont][0]; ?></td>
	<?php }

//columnas especiales
	for ($cont = 0; $cont < count($colEsp); $cont++) { ?>
	<td width="1" class="separador1"></td>
    <td class="celdas" width="50" align="center">&nbsp;<?php echo $colEsp[$cont][3]; ?>&nbsp;</td>
	<?php } ?>
  </tr><?php
	while($usr->next_record()) { ?>
  <tr class="cuerpo" id="campo<?php echo $usr->f("id") ?>" onMouseOver="cambia(this.id, 'over');" onMouseOut="cambia(this.id, '')">
  <?php for ($cont = 0; $cont < count($columnas); $cont++) {
  	if ($cont != 0) { ?>
	    <td width="1" class="separador2"></td>
	<?php } ?>
    <td style="padding-left:7px; padding-right:7px; padding-top:4px; padding-bottom:4px" align="<?php echo $columnas[$cont][4]; ?>"><?php
	if (eregi('fecha', $columnas[$cont][1])) {
		if ( $usr->f($columnas[$cont][1]) != 0 )
			echo date('d/m/Y H:i:s', $usr->f($columnas[$cont][1]));
		else echo '-';
	}
	elseif ( eregi('{val}',  $columnas[$cont][1])) echo money_format('%.2n',  $usr->f($columnas[$cont][1]));
    elseif ( eregi('{col}',  $columnas[$cont][1])) {
        $jav = "<script>
                    document.getElementById('campo".$usr->f("id")."').style.color='".$usr->f($columnas[$cont][1])."';
                </script>";
        echo $jav;
    }
	else
	echo $usr->f($columnas[$cont][1]);
	 ?></td>
	<?php }
	for ($cont = 0; $cont < count($colEsp); $cont++) {
		if ($colEsp[$cont][0] == "e" || $colEsp[$cont][0] == "v")
			$oncli = "document.pag.cambiar.value='". $usr->f("id") ."'; envia(this.form);";
		elseif ($colEsp[$cont][0] == "i")
			$oncli = "window.open('imprimeest.asp?us=". $usr->f("id") ."','nueva','menubar=no,scrollbars=yes')";
		elseif ($colEsp[$cont][0] == "b")
			$oncli = "if (confirm('". $alerta ."')) {document.pag.borrar.value='". $usr->f("id") ."'; envia(this.form)}";
		?>
    <td width="1" class="separador2"></td>
    <td align="center"><input src="<?php echo $colEsp[$cont][2] ?>" name="u" type="image" onClick="<?php echo $oncli ?>" alt="<?php echo $colEsp[$cont][1] ?>" /></td>
    <?php } ?>
  </tr><?php
}
?>
<script language="JavaScript" type="text/JavaScript">
function cambia(renglon, acc) {
	if (acc == 'over') document.getElementById(renglon).bgColor='#CCCCCC';
	else document.getElementById(renglon).bgColor='';
}
</script>
</table></form>
<?php
}




 ?>
