<!-- Calcula el md5 del(os) ficheros que se requieran -->

<form action="" method="post">
Entre el camino y nombre del fichero: <input type="text" name="fichero" value="<?php echo $fichero; ?>" /><br />
<input type="submit" value="Enviar" />
</form><br /><br />

<?php

if($_POST['fichero']) {
	$fichero = $_POST['fichero'];

	echo "El fichero ".$fichero." tiene el md5: ".md5_file($fichero)."<br /><br /><br />";
}


?>

<form action="" method="post">
Entre el camino al directorio: <input type="text" name="camino" value="<?php echo $camino; ?>" /><br />
<input type="submit" value="Enviar" />
</form>
<?php 


if($_POST['camino']) {
	$dir = $_POST['camino'];
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (!is_dir($file))
					echo "El fichero <strong>".$file."</strong> tiene el md5: ".md5_file($dir.$file)."<br />";
			}
			closedir($dh);
		}
	} else echo "no es directorio";
}
?>