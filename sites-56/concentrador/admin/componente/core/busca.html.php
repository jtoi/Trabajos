<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>
<table width="{ancho_tabla_buscar}" align="center" class="tab_tarea" cellspacing="0" cellpadding="0">
	<form name="busca_form" onsubmit="return(verifica());" method="post" action="">
  <tr>
    <td class="title_tarea">Buscar</td>
    </tr>
  <tr>
    <td><table width="100%" cellspacing="0" cellpadding="0">
	{tripa_buscar}
	  <tr>
		<td width="50" colspan="2"></td>
		<td class="sepr"></td>
		<td width="50" colspan="2"></td>
		</tr>
	</table>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td class="boton"><input class="formul" name="buscar" type="submit" value="Buscar">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="reset" type="reset" class="formul" value="Borrar"></td>
	  </tr>
	</table>
	</td>
    </tr></form>
</table>

