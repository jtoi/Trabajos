<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>
{javascript}
<table align="center" border="0" cellspacing="0" cellpadding="10">
	<tr>
		<td class="title_pag">{titulo}</td>
	</tr>
</table>{corte1}<!--Termina partes 0-->
<table align="center" border="0" cellspacing="0" cellpadding="15">
	<tr>
		<td valign="top">
			<table width="{ancho_tabla}" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>{tabed}</td>
				</tr>
			</table>{corte1}<!--Termina partes 1-->
			<table width="{ancho_tabla}" align="center" class="tab_tarea" cellspacing="0" cellpadding="0">
			<form action="" method="post" enctype="multipart/form-data" name="admin_form" onsubmit="return(verifica());">{campo}
				<tr>
					<td width="7"><img src="template/images/esqizq.png" width="7" height="23"></td>
					<td width="{anchoCelda}" class="title_tarea">{titulo_tarea}</td>
					<td width="7"><img src="template/images/esqder.png" width="7" height="23"></td>
				</tr>
				<tr>
					<td colspan="3" id="nido" align="center">{corte1}<!--Termina partes 2-->
						<table id="Contenido" width="100%" border="0" cellspacing="0" cellpadding="0">
							{corte1}<!--Termina partes 3-->
							<tr>
								<td width="50%" class="derecha">{_FORM_NOMBRE}:</td>
								<td class="izquierda"><input maxlength="150" class="formul" type="text" value="{nombre}" name="nombre" /></td>
							</tr>
							{corte1}<!--Termina partes 4-->
							<tr>
								<td width="50%" class="derecha">{_COMERCIO_MONEDA}:</td>
								<td class="izquierda">
									<select class="formul" name="moneda">
										{monedaVal}
									</select>
								</td>
							</tr>
							{corte1}<!--Termina partes 5-->
							<tr>
								<td width="50%" class="derecha">{_COMERCIO_PALABRA}:</td>
								<td class="izquierda"><input maxlength="150" class="formul" readonly="{palabraRead}" type="text" value="{palabra}" name="palabra" /></td>
							</tr>
							{corte1}<!--Termina partes 6-->
							<tr>
								<td width="50%" class="derecha">{_COMERCIO_ACTIVO}:</td>
								<td class="izquierda">
								<input type="radio" {marcActivoS} id="activos" name="activo" value="S">
								<label for="activos" >{_FORM_YES}</label>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="radio" {marcActivoN} id="activon" name="activo" value="N">
								<label for="activon" >{_FORM_NO}</label></td>
							</tr>
							{corte1}<!--Termina partes 7-->
							<tr>
								<td width="50%" class="derecha">{_COMERCIO_ACTIVITY}:</td>
								<td class="izquierda" nowrap="true">
								<input type="radio" {marcEstadoS} id="activid" name="actividad" value="D">
								<label for="activid" >{_COMERCIO_ACTIVITY_DES}</label>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="radio" {marcEstadoN} id="activip" name="actividad" value="P">
								<label for="activip" >{_COMERCIO_ACTIVITY_PRO}</label></td>
							</tr>
							{corte1}<!--Termina partes 8-->
							<tr id="fecha1tr">
								<td class="derecha">{_REPORTE_FECHA_INI}:</td>
								<td class="izquierda"><input readonly="true" name="fecha1" value="{fecha1}" type="text" class="formul" id="fecha1" size="12" maxlength="10" /><input type="image" src="../images/almanaque2.gif" alt="Fecha de Entrada" name="entr" id="entra" onClick="return showCalendar('fecha1', 'dd/mm/y');" />&nbsp;(dd/mm/yyyy)</td>
							</tr>
							{corte1}<!--Termina partes 9-->
							<tr id="fecha2tr">
								<td class="derecha">{_REPORTE_FECHA_FIN}: </td>
								<td class="izquierda"><input readonly="true" name="fecha2" value="{fecha2}" type="text" class="formul" id="fecha2" size="12" maxlength="10" /><input type="image" src="../images/almanaque2.gif" alt="Fecha de Entrada" name="entr" id="entrb" onClick="return showCalendar('fecha2', 'dd/mm/y');" />&nbsp;(dd/mm/yyyy)</td>
							</tr>
							{corte1}<!--Termina partes 10-->
						</table>{corte1}<!--Termina partes 11-->
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td class="boton">
									{corte1}<!--Termina partes 12-->
									<input class="formul" id="enviaForm" name="enviar" type="submit" value="{_FORM_SEND}" />
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input name="reset" type="reset" class="formul" value="{_FORM_CANCEL}" />
									{corte1}<!--Termina partes 13-->
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</form>
			</table>
		</td>
	</tr>
</table>
<br /><br /><!--Termina partes 14-->
