<?php

define( '_VALID_ENTRADA', 1 );
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);

require_once("../../classes/SecureSession.class.php");
$Session = new SecureSession(7500);
include_once( '../../../configuration.php' );
include '../../../include/mysqli.php';
// require_once( '../../../include/ps_database.php' );
$temp = new ps_DB;

/************************************************************************************/
// $_REQUEST['comercioId']='131886731156';
// $_REQUEST['trans']='S';
// $_REQUEST['ano']='2016';
/***********************************************************************************/

$comercId = $_REQUEST['comercioId'];
if (date('Y') == $_POST['ano']) $ano = ''; else $ano = "/".$_POST['ano'];

//if ($_POST['ficher']) {
//	if (strstr($_POST['ficher'], 'cierres')) $_POST['trans'] = 'N'; else $_POST['trans'] = 'S';
//	unlink($_POST['ficher']);
//}
$dir = "../../";
// $dir = "../../../admin/";

if ($_POST['trans'] === "F") {
	$query = "select nombre from tbl_comercio where idcomercio = '$comercId'";
	$temp->query($query);
	if ($temp->getErrorMsg) echo $temp->getErrorMsg();
	$comercio = $temp->f('nombre');
	$salida = "<tr class='encabezamiento2'><td>No.</td>";
	//	if ($_SESSION['comercio'] == 'todos')
	//		$salida .= "<td></td>";
	$salida .= "<td>Facturas</td></tr>";
	$i = 1;


	$comercio = str_replace('ó', 'o', $comercio);
	$comercio = str_replace('/', '-',
		$comercio
	);
	$dir .= "cierres/$comercio/facturas$ano/";
	// $dir .= "cierres/$comercio/transferencias$ano/";

	$ponte = getcwd($dir);

	if (file_exists($dir)) {
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if (is_file($dir . $file)) $ficheros[] = $file;
				}
				closedir($dh);
			}
		}
		$dir = substr($dir, 6);
		if (is_array($ficheros)) {
			natsort($ficheros);
			$ficheros = array_reverse($ficheros);
			foreach ($ficheros as $item) {
				$salida .= "<tr><td class='separa'>" . $i++ . "</td>";
				//				if ($_SESSION['comercio'] == 'todos')
				//					$salida .= "<td class='separa'><input type='image' style='cursor:pointer;padding-left:5px' src='../images/borra.gif' value='".$dir.$item."' title='Borrar fichero' alt='Borrar fichero' /></td>";
				$salida .= "<td style='padding:4px 7px;'><a href='" . $dir . $item . "'>" . $item . "</a></td></tr>";
			}
		}
	} else $salida = '<tr><td colspan="2" style="text-align:center;">El directorio de ' . $comercio . ' no existe. Contacte con el administrador.</td></tr>';

	echo $salida;
} elseif ($_POST['trans'] === "N") {
	$query = "select nombre from tbl_comercio where idcomercio = '$comercId'";
	$temp->query($query);
	if ($temp->getErrorMsg) echo $temp->getErrorMsg();
	$comercio = $temp->f('nombre');
	$salida = "<tr class='encabezamiento2'><td>No.</td>";
	//	if ($_SESSION['comercio'] == 'todos')
	//		$salida .= "<td></td>";
	$salida .= "<td>Cierres</td></tr>";
	$i = 1;


	$comercio = str_replace('ó', 'o', $comercio);
	$comercio = str_replace('/', '-', $comercio);
	$dir .= "cierres/" . $comercio . $ano . "/";

	$ponte = getcwd($dir);

	if (file_exists($dir)) {
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if (is_file($dir . $file)) $ficheros[] = $file;
				}
				closedir($dh);
			}
		}
		$dir = substr($dir, 6);
		if (is_array($ficheros)) {
			natsort($ficheros);
			$ficheros = array_reverse($ficheros);
			foreach ($ficheros as $item) {
				$salida .= "<tr><td class='separa'>" . $i++ . "</td>";
				//				if ($_SESSION['comercio'] == 'todos')
				//					$salida .= "<td class='separa'><input type='image' style='cursor:pointer;padding-left:5px' src='../images/borra.gif' value='".$dir.$item."' title='Borrar fichero' alt='Borrar fichero' /></td>";
				$salida .= "<td style='padding:4px 7px;'><a href='" . $dir . $item . "'>" . $item . "</a></td></tr>";
			}
		}
	} else $salida = '<tr><td colspan="2" style="text-align:center;">El directorio de ' . $comercio . ' no existe. Contacte con el administrador.</td></tr>';

	echo $salida;
} elseif ($_POST['trans'] === "G") {
	if (strlen($_POST['comercioId']) > 20) $query = "select nombre from tbl_comercio where id in ('" . $_POST['comercioId'] . "') and activo = 'S'";
	else $query = "select nombre from tbl_comercio where id in (".$_POST['comercioId'].") and activo = 'S'";
	error_log ($query);
	$temp->query($query);
	if ($temp->getErrorMsg) echo $temp->getErrorMsg();
	$arrCom = $temp->loadResultArray();
	$salida = "<tr class='encabezamiento2'>";
	$salida .= "<td>Facturas</td></tr>";
	$i=1;

	$dir = "../../facturas/".$_POST['ano']."/";
	error_log($dir);
	if (file_exists($dir)) {
		// $dir = "../mydir/";
		chdir($dir);
		$diral = str_replace("../..", _ESTA_URL."/admin", $dir);
		$files = array();
		$dir = new DirectoryIterator($dir);
		foreach ($dir as $fileinfo) {
			if ($fileinfo->isFile()) {
				$tiempo = $fileinfo->getMTime();
				$files[$tiempo] = $fileinfo->getFilename();
				// $files[$tiempo][] = $fileinfo->getFilename();
				// $files[$tiempo][] = $fileinfo->getExtension();
			}
		}
		krsort($files);

		foreach ($files as $key => $file) {
			if (is_file($file)) {
				for ($i=0;$i<count($arrCom);$i++){
					if (stripos($file,$arrCom[$i]) > -1)
						$salida .= "<td style='padding:4px 7px;display:inline;white-space:nowrap;'><div class='dicborr'><input type='button' class='brrfl'  onclick='borra(\"".$file."\")'/> - <a href='".$diral.$file."' target='_blank'> ".substr($file, 0, -4)."</a></div></td></tr>";
				}
			}
		}


	} else $salida = '<tr><td colspan="2" style="text-align:center;">El directorio para las facturas de '.$comercio.' no existe. Contacte con el administrador.</td></tr>';

	echo $salida;

} else { 
	$query = "select nombre from tbl_comercio where idcomercio = '$comercId'";
	$temp->query($query);
	$comercio = $temp->f('nombre');
	$salida = "<tr class='encabezamiento2'><td>No.</td>";
//	if ($_SESSION['comercio'] == 'todos')
//		$salida .= "<td></td>";
	$salida .= "<td>Transferencias</td></tr>";
	$i=1;

	$comercio = str_replace('ó', 'o', str_replace('á', 'a', str_replace('é', 'e', str_replace('í', 'i', str_replace('é', 'e', str_replace('ú', 'u', $comercio))))));
	$comercio = str_replace('/', '-', $comercio);
	$dir .= "cierres/$comercio/transferencias$ano/";
	
	if (file_exists($dir)) {
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if (is_file($dir.$file)) $ficheros[] =$file;
				}
				closedir($dh);
			}
		}
		$dir = substr($dir, 6);
		if (is_array($ficheros)) {
			natsort($ficheros) ;
			$ficheros = array_reverse($ficheros);
			foreach ($ficheros as $item){
				$salida .= "<tr><td class='separa'>".$i++."</td>";
//				if ($_SESSION['comercio'] == 'todos')
//					$salida .= "<td class='separa'><input type='image' style='cursor:pointer;padding-left:5px' src='../images/borra.gif' value='".$dir.$item."' title='Borrar fichero' alt='Borrar fichero' /></td>";
				$salida .= "<td style='padding:4px 7px;'><a href='".$dir.$item."'>".$item."</a></td></tr>";
			}
		}


	} else $salida = '<tr><td colspan="2" style="text-align:center;">El directorio de '.$comercio.' no existe. Contacte con el administrador.</td></tr>';
	echo $salida;
}

?>
<script language="javascipt">
	function borra(elem){
		// $(".title_tarea1").esperaDiv('cierra');
		// $(".title_tarea1").esperaDiv('muestra');

		$.post('componente/comercio/ejec.php',{
			fun: 'borraFac',
			elem: elem
		},function(data){
			var datos = eval('(' + data + ')');
			// $(".title_tarea1").esperaDiv('cierra');
			$("#enviaForm").show();
			if (datos.error.length > 0) alert(datos.error);
			if (datos.cont == 'true') {
				window.open('<?php echo _ESTA_URL."/admin/index.php?componente=comercio&pag=verfact";?>', '_self');
			}
		});
	}
</script>