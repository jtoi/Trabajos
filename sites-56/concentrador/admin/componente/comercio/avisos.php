<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
$html = new tablaHTML;
$corCreo = new correo();

global $temp;
$d = $_POST;
$fechaNow = time();
$incluye = "";
$comer = $_SESSION['comercio'];

//print_r($d);
//inserta Art&iacute;culo
if ($d['inserta']) {

	$arrayTo = array();
	if ($comer == '0' or $comer == 'todos') $idcomercio = $d['comercio']; else $idcomercio = $comer;
    $query = "select * from tbl_comercio where idcomercio = {$idcomercio}";
    $temp->query($query);
    $comercioN = $temp->f('nombre');
	$estCom = $temp->f('estado');
	$datos = $temp->f('datos');
	$prefijo = $temp->f('prefijo_trans');
	$correo = $d['email'];
	$idCom = $temp->f('id');
	$pasarela = $temp->f('idpasTransf');

//	solo envía facturas si el comercio esta en producción
	if ($estCom == 'P') {

//		Calculo del ID de la operación
        $trans = trIdent($prefijo);

		$query = "select p.cuenta from tbl_pasarela p where p.idPasarela = ".$pasarela;
		$temp->query($query);
//		$pasarela = $temp->f('idPasarela');
		$cuenta = $temp->f('cuenta');
		$pais = $d['pais'];
		$importe = $d['importe']*100;

//		inserta valores en la tabla de las transacciones
		$hora = time();
		$query = "insert into tbl_transacciones	(idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor_inicial, tipoEntorno,
					moneda, estado, sesion, pasarela, idioma)
				values ('$trans', '$idcomercio', '$transC', 'T', $hora, $hora, '$importe', 'P', '{$d['monedas']}', 'P', '$firma', '$pasarela', '{$d['idioma']}')";
//echo $query;
		$temp->query($query);
		
		$q = "select moneda from tbl_moneda where idmoneda = ".$d['monedas'];
		$temp->query($q);
		$mone = $temp->f('moneda');

		$correo = str_replace(" ", "", $d['email']);
//print_r($d);
	//	inserta los valores en la tabla de transferencias
		$query = "insert into tbl_transferencias (idTransf, cliente, email, cuentaB, idcomercio, idCom, facturaNum, fecha, fechaTransf, valor, moneda, concepto, idioma, idpais, idPasarela)
					values ('$trans', '{$d['nombre']}', '$correo', '{$d['cuenta']}', '$idcomercio', '$idCom', '$transC', '{$hora}', {$hora},
						'$importe', '{$d['monedas']}', '{$d['servicio']}', '{$d['idioma']}', null, null)";
		$temp->query($query);
//		echo $query;

//		Envía por correo la factura al cliente y al comercio
		$from = 'tpv@caribbeanonlineweb.com';
		// global $send_m;

		$arrayTo[] = array($d['nombre'], $correo);
		$arrayTo[] = array($_SESSION['admin_nom'], $_SESSION['email']);

		include "lang/correo{$d['idioma']}.php";
		
		$message = str_replace('{comercio}', $comercioN, _AVISO_TEXTO1);
		$message = str_replace('{importe}', $d['importe'], $message);
		$message = str_replace('{moneda}', $mone, $message);
		$texto = _AVISO_CTA;
		$message .= $datos."<br /><br />".$d['servicio']."<br /><br />".$texto."<br /><br />".str_replace('{aviso}', $trans, _AVISO_NOTA);
if (_MOS_CONFIG_DEBUG) echo $message;


//		Hace el envío del correo
		$subject = _AVISO_SUBJECT.$comercioN;
		$des = true;
		foreach ($arrayTo as $todale) {
            if ($des) {
                $corCreo->to($todale[0]." <".$todale[1].">");
                $des = false;
            } else $corCreo->add_headers ("Cc: ".$todale[0]." <".$todale[1].">");
		}
		$corCreo->todo(26,$subject,$message);

		echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
			"._AVISO_SI."</div>";
	} else echo "<script type='text/javascript'>alert('El comercio debe estar en producción para poder hacer recibir transferencias')</script>";

}

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_AVISO;
$html->tituloTarea = "&nbsp;";
$html->anchoTabla = 600;
$html->anchoCeldaI = 250;$html->anchoCeldaD = 340;
$html->java = "
	<script language=\"JavaScript\" type=\"text/javascript\">
	function verifica() {
		if (
				(checkField (document.admin_form.nombre, isAlphanumeric, ''))
			) {
            document.getElementById('comer').value = document.getElementById('comercio').value;
            document.getElementById('enviaForm').style.display='none';
			return true;
		}
		return false;
	}

	$(function() {
		$('textarea').supertextarea({
		   maxw: 280
		  , maxh: 100
		  , minw: 130
		  , minh: 20
		  , dsrm: {use: false}
		  , tabr: {use: false}
		  , maxl: 1000
		});
	});
	
	$(document).ready(function(){ $('textarea').attr('value', '');});
	
</script>";

$html->inHide($_SESSION['id'], 'usuario');
$html->inHide($comer, 'comer');
$html->inHide(true, 'inserta');
$html->inTextb(_COMPRUEBA_TRANSACCION, "", "transC",null," "._COMERCIO_GENERA);
$html->inTextb(_AVISO_NOMBRE, $nombre, 'nombre', null, null, null);
$html->inTextb(_FORM_CORREO, '', 'email');
$arrIdio = array ('es', 'en');
$arrEtiq = array(_PERSONAL_ESP, _PERSONAL_ING);
$html->inRadio(_PERSONAL_IDIOMA, $arrIdio, 'idioma', $arrEtiq, 'es', null, false);
//$query = "select id, nombre from tbl_paises order by nombre";
//$html->inSelect(_REPORTE_PAIS, 'pais', 1, $query);
//$html->inTextb(_FORM_CUENTA, '', 'cuenta', null, null, null, _FORM_CUENTA_ALT);
$html->inTextb(_COMPRUEBA_IMPORTE, '3000.00', 'importe', null, null, null);
$query = "select idmoneda id, moneda nombre from tbl_moneda";
$html->inSelect(_COMERCIO_MONEDA, 'monedas', 2, $query, $monedaid);
$html->inTexarea(_AVISO_OBSERVA, null, 'servicio', 7, null, null, null, null, 'Aclarar con lujo de detalles el servicio prestado');
if ($comer == 'todos') {
	$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 1, $query);
} elseif (strpos($comer, ',')) {
	$query = "select idcomercio id, nombre from tbl_comercio where idcomercio in ($comer) and activo = 'S' order by nombre";
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 1, $query);
}

echo $html->salida();


?>
