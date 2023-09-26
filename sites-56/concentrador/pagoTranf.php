<?php
define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/hoteles.func.php' );
require_once( 'admin/adminis.func.php' );
require_once( 'admin/classes/entrada.php' );
require_once( 'include/correo.php' );
$temp = new ps_DB;
$correo = new correo;
$ent = new entrada;

$d=$_REQUEST['cod'];
if (_MOS_CONFIG_DEBUG){
	// 	$d = '0605222835';
	// 	$c = '139333436635';
}
$fec = time();

if (strlen($d) < 20) {
	if (!($inicio->tran = $ent->isUrl($d, 12))) {
		muestraError ("Error: falla por transaccion");
	}
	
	$query = sprintf("select r.nombre cliente, r.id_comercio, r.id_transaccion, c.nombre comercio, servicio, valor_inicial, r.estado,
			m.moneda, r.moneda idmoneda, c.palabra, c.condiciones_esp, c.condiciones_eng, idioma, r.pasarela, r.amex
			from tbl_reserva r, tbl_comercio c, tbl_moneda m
			where r.id_comercio = c.idcomercio
			and r.moneda = m.idmoneda
			and r.fecha > $fec - (r.tiempoV*86400)
			and r.codigo = '%d'
			and r.estado = 'P'
			and r.id_comercio = '%s'", $d, $c);
	// echo $query;
}

function muestraError ($etiqueta) {
	global $correo, $correoMi;	
	
	echo '<script>document.getElementById("avisoIn").style.display="none";document.writeln("<div id=\"errDvd\" style=\"margin:20px 0 0 "+
((window.innerWidth)-800)/2
+"px; width:800px; text-align:center;\">")</script>
Se ha producido un <span style="color:red;font-weight:bold;">ERROR</span>
en los datos enviados:<br /><h3>'.$etiqueta.'</h3>por favor consulte a su comercio.<br /><br />
<img src="images/pagina_error.png" width="247" height="204" alt="Error" title="Error" /><br /><br /></div>
<!-- '.$etiqueta.' -->';
	$correo->todo(52, $etiqueta, $textoCorreo." ** ".$correoMi);
	exit;
}
?>