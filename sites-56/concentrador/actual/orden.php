<?php
ini_set('display_errors', 0);
//error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('_VALID_ENTRADA', 1);
if (!session_start()) session_start();
require_once( 'admin/classes/entrada.php' );
require_once( 'admin/adminis.func.php' );
require_once( 'configuration.php' );
include 'include/mysqli.php';
include 'include/correo.php';

//if (stripos(_ESTA_URL, 'localhost') > 0) {
//	$_REQUEST['op'] = '180205214460';
//}

$temp = new ps_DB;
$ent = new entrada;
$corr = new correo;

$d = $_REQUEST;

//if (_MOS_CONFIG_DEBUG) $d['op'] = '170626194269';

if (($id = $ent->isReal($d['op'], 12))) {
	$estedir = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')+1);
	
	$sFilename = $estedir.'admin/factura.ini';
	
	if ($f = fopen ( $sFilename, "r" )) {
		while ( ! feof ( $f ) )
			$message .= fread ( $f, filesize ( $sFilename ) );
		fclose ( $f );
	} else return "<script type='text/javascript'>alert('Ocurrió un error al cargar el fichero.')</script>";
	
	$temp->query(sprintf("select t.cliente, t.concepto, t.idioma, t.valor, m.moneda, p.cuenta, t.fecha, c.nombre comercio, a.email, t.vista, t.idCom, t.moneda idmon, t.idPasarela
					from tbl_transferencias t, tbl_moneda m, tbl_pasarela p, tbl_comercio c, tbl_admin a
					where c.id = t.idCom and p.idPasarela = t.idPasarela and t.idadmin = a.idadmin and t.moneda = m.idmoneda and t.idTransf = '%d'",$id));

	if ($temp->num_rows() > 0) {
		
		/**
		 * Tere me pide 16/1/18 que en el momento que el cliente de click a la invitación es que se
		 * verifique por qué pasarela se lanza la transferencia para evitar que por hacer click en
		 * invitaciones intermedias, se vayan dos operaciones por la misma pasarela
		 */
		$cliente = $temp->f('cliente');
		$concepto = $temp->f('concepto');
		$idioma = $temp->f('idioma');
		$valor = $temp->f('valor');
		$moneda = $temp->f('moneda');
		$fecha = $temp->f('fecha');
		$comercio = $temp->f('comercio');
		$email = $temp->f('email');
		$vista = $temp->f('vista');
		$idCom = $temp->f('idCom');
		$idmon = $temp->f('idmon');
		$idpas = $temp->f('idPasarela');
		
		//busco la pasarela y la cambio en ambas tablas
//		$temp->query(sprintf("select idPasarela from tbl_transferencias where vista = 1 and idTransf = '%d'",$id));
//		if ($temp->num_rows() > 0) $idpas = $temp->f ('idPasarela');
//		else 
		if ($vista == 0) {
			$idpas = damePasarA($idCom, $idmon);
			error_log("PASARELA TRANSFERENCIA ORDEN=".$idpas);
		}
		$temp->query(sprintf("update tbl_transferencias t set t.idPasarela = $idpas where t.idTransf = '%d'",$id));
		$temp->query(sprintf("update tbl_transacciones t set t.pasarela = $idpas where t.idtransaccion = '%d'",$id));
		
		//leo la cuenta del banco que corresponde
		$temp->query("select cuenta from tbl_pasarela p where idPasarela = '$idpas'");
		$cuenta = $temp->f('cuenta');
		
		include $estedir."admin/lang/correo{$idioma}.php";
		
		if ($fecha+60*60*24*10 < time()) 
			echo str_replace("{CORREO}", $email, 
								str_replace("{IMPORTE}", number_format ( ($valor / 100), 2 ) . ' ' . $moneda, 
								str_replace("{COMERCIO}", $comercio, 
								str_replace ( '{CLIENTE}', $cliente, _INV_VIEJA_DOS))));//invitación con más de 10 días de antiguedad
		else {
		
			$mensaje = str_replace ( '{idfactura}', $id, 
					str_replace ( '{clientenombre}', $cliente, 
					str_replace ( '{servicio}', str_replace("<br>", "", str_replace("\n", "", $concepto)), 
					str_replace ( '{serv}', _SERV, 
					str_replace ( '{val}', _VAL, 
					str_replace ( '{valor}', number_format ( ($valor / 100), 2 ) . ' ' . $moneda, 
					str_replace ( '{pagara}', $cuenta, 
					str_replace ( '{fecha}', date ( 'd/m/Y H:i', time() ), 
					str_replace ( '{factura}', _FACTURA, 
					str_replace ( '{cliente}', _CLIENTE, 
					str_replace ( '{fechaHora}', _REPORTE_FECHAHORA, 
					str_replace ( '{nota}', _NOTA, 
					str_replace ( '{texto5}', _TEXTO5, $message )))))))))))));
			echo $mensaje;
			
			$subject = ' Invitacion de ' . $comercio . ' a realizar el pago a traves del Administrador de Comercios';
			$corr->from = 'tpv@administracomercios.com';
			$corr->reply = 'noreply@administracomercios.com';
			$corr->to = $email;
			if ($vista == 0) $corr->todo (50, $subject, $mensaje);
			else if (_MOS_CONFIG_DEBUG) echo "no se manda";

		}
	}
	
	$temp->query(sprintf("update tbl_transferencias t set t.fechaTransf = ".time().", vista = 1 where t.idTransf = '%d'",$id));
}
?>