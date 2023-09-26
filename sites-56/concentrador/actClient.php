<?php define('_VALID_ENTRADA', 1);
require_once( 'admin/classes/entrada.php' );
require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/correo.php' );

$temp = new ps_DB;
$ent = new entrada;
$correo = new correo;


$d = $_REQUEST;

if (stripos(_ESTA_URL, 'localhost') > 0 || stripos(_ESTA_URL, '192.168.0.243') > 0) {
	$d['IdCliente'] = '10127';
	$d['UsuarioCode'] = 'Marielmijares30@gmail.com';
}

foreach ($d as $value => $item) {
	if (mb_detect_encoding($item) == 'UTF-8') $d[$value] = mb_convert_encoding($item, "UTF-8");
	else $d[$value] = utf8_encode($item);
	$correoMi .= $value . "=" . $item . ", enc=".mb_detect_encoding($item)."<br>\n";
}

if (!$d['IdCliente']*1 > 1) {muestraError ("falla por id Cliente", $correoMi);exit;}
if (!($usur = $d['UsuarioCode'])) {muestraError ("falla por Código de usuario", $correoMi);exit;}

$temp->query(sprintf("update tbl_aisCliente set fecha = unix_timestamp(), subfichero = 1 where idcimex = %u and usuario = '%s'", $d['IdCliente'], $d['UsuarioCode']));

echo $d['IdCliente'];

function muestraError ($etiqueta, $textoCorreo) {
	global $correoMi;
	$correo = new correo();
	$textoCorreo .= $etiqueta;
	$correo->set_subject('Error Actualizando documentación en Ais');
	$correo->set_message($textoCorreo);
	$correo->envia(9);
	echo $etiqueta;
	exit;
}
?>