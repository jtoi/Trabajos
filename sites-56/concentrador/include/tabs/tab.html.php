<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>
<link href="{camino}tabs.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="{camino}tabs.js"></script>
<div id="tabulador" align="left">
	<table border="0" cellspacing="0" cellpadding="0">
		<tr id="renglon">
	{corte}
			<td onMouseOut="mouseOu(this)" onclick="mouseCl(this)" onMouseOver="mouseOv(this)" id="tab{titulo}" class="tabs">{titulo}</td>
	{corte}
		</tr>
	</table>
</div>
