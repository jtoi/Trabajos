<?php
if ($_REQUEST['cont'])
echo md5($_REQUEST['cont']);
?>

<form action="" method="POST">
	Contenido: <textarea name="cont"></textarea><br />
	<input type="submit" value="Enviar" />
</form>