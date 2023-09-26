<?php

/* 
 * Este fichero se encarga de hacer una simulación de un sitio de un comercio
 */

define( '_VALID_ENTRADA', 1 );
require_once( 'configuration.php' );
require_once 'include/mysqli.php';
include_once("admin/adminis.func.php");
$temp = new ps_DB();

$d=$_REQUEST;



foreach ($d as $key => $value) {
	$lleg .= $key." = ".$value."<br>\n";
}

echo "datos de llegada: ".$lleg;

if ($d['ide']) {
	$com = $d['com'];
	$ide = $d['ide'];
	$imp = $d['imp']*100;
	$mon = $d['mon'];
	$pas = $d['pas'];
	$q = "select palabra from tbl_comercio where idcomercio = '$com'";
	$temp->query($q);
	$pal = $temp->f('palabra');
	$firma = md5($com . $ide . $imp . $mon . 'P' . $pal);
	
	$cadenSal = " 
		<form name=\"envia\" action=\"index.php\" method=\"post\">
		<input type=\"text\" name=\"comercio\" value=\"$com\"/>
		<input type=\"text\" name=\"transaccion\" value=\"$ide\"/>
		<input type=\"text\" name=\"importe\" value=\"$imp\"/>
		<input type=\"text\" name=\"moneda\" value=\"$mon\"/>
		<input type=\"text\" name=\"operacion\" value=\"P\"/>
		<input type=\"text\" name=\"firma\" value=\"$firma\"/>
		<input type=\"text\" name=\"pasarela\" value=\"$pas\"/>";
	if ($com == '527341458854') {
		$cadenSal .= "
		<input type=\"text\" name=\"pasarela\" value=\"37\">
		<input type=\"text\" name=\"idremitente\" value=\"SDF5WE4SE4\">
		<input type=\"text\" name=\"nombremitente\" value=\"NombreRemitente\">
		<input type=\"text\" name=\"aperemitente\" value=\"Apellidos Remitente\">
		<input type=\"text\" name=\"tipodoc\" value=\"01\">
		<input type=\"text\" name=\"numerodoc\" value=\"60092014942\">
		<input type=\"text\" name=\"direcremitente\" value=\"Una dirección un poquito larga a ver qué hace\">
		<input type=\"text\" name=\"iddestin\" value=\"DEST3454W3ZDF\">
		<input type=\"text\" name=\"nombredestin\" value=\"NombreDestinatario\">
		<input type=\"text\" name=\"apelldestin\" value=\"Apellido Destinatario\">
				";
	}
	$cadenSal .= "
		<input type=\"submit\" value=\"Enviar\" />
		</form>";
echo $cadenSal;
} else {
	
?>
<h2>Simulación de envío de operación desde un comercio</h2>
<form enctype="post" action="simComercio.php">
	Identificador del comercio: <input type="text" name="com" value="122327460662" /><br />
	Identificador de la operación: <input type="text" name="ide" value="<?php echo generaCodEmp(); ?>" /><br />
	Importe: <input type="text" name="imp" value="" /><br />
	Moneda: <select name="mon">
		<option selected="true" value="978">EUR</option>
		<option value="124">CAD</option>
		<option value="152">CLP</option>
		<option value="170">COP</option>
		<option value="32">ARS</option>
		<option value="356">INR</option>
		<option value="392">JPY</option>
		<option value="484">MXN</option>
		<option value="604">PEN</option>
		<option value="756">CHF</option>
		<option value="826">GBP</option>
		<option value="840">USD</option>
		<option value="937">VEF</option>
		<option value="949">TRY</option>
		<option value="986">BRL</option>
	</select><br>
	Pasarela: <select name="pas">
		<option value="">Ninguna</option>
	<?php 
	$q = " select idPasarela, nombre from tbl_pasarela where tipo = 'P' and activo = 1 order by nombre";
	$temp->query($q);
	$arrPa = $temp->loadAssocList();
	print_r($arrPa);
	foreach ($arrPa as $item) {
		echo '<option value="'.$item["idPasarela"].'">'.$item["nombre"].'</option>';
	}
	?>
	</select>
	<input type="submit" value="Enviar" />
</form>
<?php } ?>
);