<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>
<table align="center" border="0" cellspacing="0" cellpadding="10">
	<tr>
		<td class="title_pag">{titulo}</td>
	</tr>
</table>

{muestra}
<form name="buscar_form" onsubmit="return(verifica(this));" method="post" action="">
    <table width="{ancho_tabla}" align="center" class="tab_tarea" cellspacing="0" cellpadding="0">
  <tr>
    <td class="title_tarea">{_FORM_SEARCH}</td>
    </tr>
  <tr>
    <td>
	{tripa}
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td class="boton"><input class="formul" name="enviar" type="submit" value="{_FORM_SEARCH}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="reset" type="reset" class="formul" value="{_FORM_CANCEL}"></td>
	  </tr>
	</table>
	</td>
    </tr>
</table>
</form>
<br><br />
{muestra}
<form name="admin_form" onsubmit="return(verifica());" method="post" action="">
<input name="dosmd2" type="hidden" id="dosmd2" />
<input name="unomd2" type="hidden" id="unomd2" />
    <table width="{ancho_tabla}" align="center" class="tab_tarea" cellspacing="0" cellpadding="0">
  <tr>
  	<td width="7"><img src="template/images/esqizq.png" width="7" height="23"></td>
    <td width="{anchoCelda}" class="title_tarea">{titulo_tarea}</td>
    <td width="7"><img src="template/images/esqder.png" width="7" height="23"></td>
    </tr>
  <tr>
    <td colspan="3">
	{tripa}
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td class="boton"><input class="formul" name="enviar" type="submit" value="{_FORM_SEND}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="reset" type="reset" class="formul" value="{_FORM_CANCEL}"></td>
	  </tr>
	</table>
	</td>
    </tr>
</table>
</form><br /><br />
