<?php defined('_VALID_ENTRADA') or die('Restricted access');
$html = new tablaHTML;
global $temp;

//global $temp;
$d = $_POST;
$fechaNow = time();
$alerta = "";
$ficheros = array();
$anoH = date("Y");
$anoC = 2010;
$idcom = '';


if (isset($d['ano'])) $ano = $d['ano'];
else $ano = $anoH;

if (isset($d['comercio']) && isset($_FILES["fileup"]) && $_FILES["fileup"]["error"] == 0) {
	$idcom = $d['comercio'];
	$q = "select nombre from tbl_comercio where id = " . $d['comercio'];
	$temp->query($q);
	$nomFil = $temp->f('nombre') . "_" . date('y') . date('m') . date('d') . date('H') . date('i') . date('s');

	$target_dir = "facturas/$ano/";
	if (!file_exists($carpeta)) {
		mkdir($target_dir, 0777, true);
	}
	$allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "pdf" => "application/pdf", "JPG" => "image/jpg", "JPEG" => "image/jpeg", "PDF" => "application/pdf");
	$target_file = $target_dir . $nomFil;
	$filename = $_FILES["fileup"]["name"];
	$filetype = $_FILES["fileup"]["type"];
	$filesize = $_FILES["fileup"]["size"];
	error_log("filename=$filename");
	error_log("filetype=$filetype");
	error_log("filesize=$filesize");

	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	error_log("ext=$ext");
	$nomb = $target_file . "." . $ext;
	if (!array_key_exists($ext, $allowed)) echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">Error: Por favor entre un fichero de factura válido, puede ser en formato jpg, jpeg o pdf.</div>";
	else {
		if (move_uploaded_file($_FILES["fileup"]["tmp_name"], $nomb))  echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">El fichero " . basename($_FILES["fileToUpload"]["name"]) . " ha subido correctamente y se ha renombrado a " . $nomFil . "</div>";
		else echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">Lo sentimos algo ha fallado en la subida, int&eacute;ntelo nuevamente</div>";
	}
}
//echo "cod=".generaCodEmp();
// $html->java = $javascript;

// $html->idio = $_SESSION['idioma'];
// $html->tituloPag = _MENU_ADMIN_SFACT;
// $html->tituloTarea = _REPORTE_TASK;
// $html->anchoTabla = 500;
// $html->tabed = true;
// $html->anchoCeldaI = 105;
// $html->anchoCeldaD = 340;
// //print_r ($_SESSION);

// if ($_SESSION['grupo_rol'] < 2 || strpos($_SESSION['idcomStr'], ',')) {

// 	if ($_SESSION['comercio'] == 'todos') $query = "select idcomercio id, nombre from tbl_comercio order by nombre";
// 	else $query = "select idcomercio id, nombre from tbl_comercio where id in (".$_SESSION['idcomStr'].") order by nombre";

// 	if ($d['comercio']) $comercId = $d['comercio'];
// 	else {
// 		$temp->query($query);
// 		$comercId = $temp->f('id');
// 	}

// 	$html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $query, $comercId, null, null);

// } else {
// 	$comercId = $_SESSION['comercio'];
// }

// $html->inSelect("Año", 'ano', 4, array($anoC,$anoH), $ano);
// echo $html->salida();

if ($_SESSION['grupo_rol'] <= 2 || $_SESSION['grupo_rol'] = 5) {
	$html = new tablaHTML;

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = "";
	$html->tituloTarea = "Buscar";
	$html->hide = true;
	$html->anchoTabla = 500;
	$html->anchoCeldaI = 105;
	$html->anchoCeldaD = 340;

	$query = "select id, nombre from tbl_comercio where  activo = 'S' and id in (" . $_SESSION['idcomStr'] . ") order by nombre";
	//		echo $query;
	$html->inSelect(_COMERCIO_TITULO, 'cambiar', 5, $query,  str_replace(",", "', '", $comercId));
	$html->inSelect("Año", 'ano', 4, array($anoC, $anoH), $ano);
	echo $html->salida('', null, true);
}

$html = new tablaHTML;
$html->idio = $_SESSION['idioma'];
$html->tituloPag = "";
$html->tituloTarea = "";
$html->anchoTabla = 500;
$html->anchoCeldaI = 105;
$html->anchoCeldaD = 340;
if (strpos($_SESSION['idcomStr'], ',')) {
	$query = "select id, nombre from tbl_comercio where activo = 'S' and id in (" . $_SESSION['idcomStr'] . ") order by nombre";
	// echo $query;
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $query,  str_replace(",", "', '", $comercId));
} else {
	$html->inHide($_SESSION['idcomStr'], comercio);
}
$html->inFile("Factura", "fileup");
$html->inTextoL("S&oacute;lo se admiten ficheros jpg ó pdf", 'aclar');
echo $html->salida(null, null, true);
// }

if (!isset($d['cambiar'])) $comercId = $_SESSION['idcomStr'];
else $comercId = $d['cambiar'];
?>
<style>
#aclar span{font-weight: bold; font-size: 1.1em;}
</style>
<script type='text/javascript'>
	function cambiaD(val, anno) {
		$('#resultC tbody').load('componente/comercio/resultado.php', {
			comercioId: val,
			trans: "G",
			ano: anno
		});
	}

	$(document).ready(function() {
		<?php if ($_SESSION['grupo_rol'] <= 2) {
			echo '$("form:first .botForm").hide()';
		}
		?>

		$("#cambiar").change(function() {
			// alert($("#ano :selected").val());
			cambiaD($("#cambiar").val(), $("#ano :selected").val());
		});

		$("#ano").change(function() {
			// alert($("#cambiar").val());
			cambiaD($("#cambiar").val(), $("#ano :selected").val());
		});

		$('#resultC tbody').load('componente/comercio/resultado.php', {
			comercioId: '<?php echo $comercId ?>',
			trans: "G",
			ano: <?php echo $ano; ?>
		});

	});
</script>
<div id="cierr" style="float: left;width: 100%">
	<table cellspacin="0" cellpadding="0" align="center" id="resultC" width="400">
		<thead>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">Trayendo la información. Espere unos segundos...</td>
			</tr>
		</tbody>
	</table>
</div>

<div id="trans" style="float: left;width: 100%;display:none;">
	<table cellspacin="0" cellpadding="0" align="center" id="resultT" width="400">
		<thead>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">Trayendo la información. Espere unos segundos...</td>
			</tr>
		</tbody>
	</table>
</div>