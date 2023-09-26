<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$ent = new entrada;
$corr = new correo();


$d = $_REQUEST;
$id = $ent->isEntero($d['tf'], 12);
// echo $GLOBALS['sitio_url']."<br>";
// if (strpos($GLOBALS['sitio_url'], 'admin')) $url = $GLOBALS['sitio_url']."index.php?componente=comercio&pag=reporte";
// else $url = $GLOBALS['sitio_url']."admin/index.php?componente=comercio&pag=reporte";
// echo $url;

function mandaCorreo($subject, $mensaje, $arrayTo, $identf){
	$corr = new correo();

	// $arrayTo[] = array('',_CORREO_SITE);
	$des = true;
	foreach ( $arrayTo as $todale ) {
		if ($des) {
			$corr->to ( $todale[1] );
			$des = false;
		} else
			$corr->add_headers ( "Cc: " . $todale [1] );
	}
	$corr->from = 'noreply@administracomercios.com';
	$corr->reply = 'noreply@administracomercios.com';

	if (_MOS_CONFIG_DEBUG) echo ($mensaje."<br>to-> ".$corr->to."<br>header-> ".$corr->headers);

	return $corr->todo ( $identf, $subject, $mensaje );
}


if ($id) {
	$docroot = str_replace("admin/", "", substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')+1));

	if ($d['inserta'] == 2) {
		//La transferencia se deniega
		include $docroot."admin/lang/correoes.php";

		$temp->query("update tbl_transacciones t, tbl_transferencias r set r.estado = 'D', t.estado = 'D', id_error = '{$d['servicio']}' where idTransf = t.idtransaccion and r.idTransf = '$id'");
		$temp->query("select r.cliente, a.nombre, a.email, r.valor/100 'valor', m.moneda, concepto from tbl_transferencias r, tbl_moneda m, tbl_admin a where a.idadmin = r.idadmin and m.idmoneda = r.moneda and r.idTransf = '$id'");
		$cliente = $temp->f('cliente');
		$valor = $temp->f('valor');
		$moneda = $temp->f('moneda');
		$concepto = $temp->f('concepto');

		$arrayTo[] = array(
				$temp->f('nombre'),
				$temp->f('email')
			);
		$arrayTo[] = array (
				$_SESSION ['admin_nom'],
				$_SESSION ['email']
			);

		$message = str_replace("{cliente}", $cliente, str_replace('{servicio}', $concepto, str_replace("{valor}", number_format($valor,2), str_replace("{moneda}", $moneda, str_replace("{motivo}", $d['servicio'], _TBIO_REV_REJ)))));
		
		
		if (mandaCorreo(_TBIOREVREC_SUBJECT, $message, $arrayTo, 66)) {


			echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
					La Transferencia fu&eacute; rechazada, se envi&oacute; un correo al usuario del comercio que la indic&oacute;</div>
					<script language=\"JavaScript\" type=\"text/javascript\">window.open('"._ESTA_URL."/admin/index.php?componente=comercio&pag=repTransf', '_self')</script>";
		}
	}
	
	if ($d['inserta'] == 1) {
		//La transferencia pasa a aprobarse
		//busca al Cliente final de la transferencia y a quién la puso
		$temp->query("select t.cliente, t.email, a.nombre, a.email 'correo', t.idioma, t.valor/100 valor, m.moneda ".
					"from tbl_transferencias t, tbl_admin a, tbl_moneda m ".
					"where t.idadmin = a.idadmin ".
					"and t.moneda = m.idmoneda ".
					"and t.idTransf = '$id'");
		$cliente = $temp->f('cliente');
		$valor = $temp->f('valor');
		$moneda = $temp->f('moneda');

		$arrayToCom[] = array (
						$temp->f('nombre'),
						$temp->f('correo')
		);
		$arrayTo[] = array(
						$cliente,
						$temp->f('email')
					);
		
		$idio = $temp->f('idioma');
		$temp->query("update tbl_transacciones t, tbl_transferencias r set r.estado = 'P', t.estado = 'P' where t.idtransaccion = r.idTransf and r.idTransf = '$id'");


		/** Envía por correo la factura al cliente y al comercio  */

		include $docroot."admin/lang/correo{$idio}.php";
		if ($arrEnt['idioma'] == 'es')
			include_once $docroot."admin/lang/spanish.php";
		else
			include_once $docroot."admin/lang/english.php";

		$url = _ESTA_URL."/orden.php?op=".$id;

		if (mandaCorreo(_TBIO_SUBJECT, str_replace ( '{url}', $url, _TBIO_TEXTCORREO), $arrayTo, 25)) {
				
			/** envía correo de confirmación al comercio */
			$message = str_replace("{cliente}", $cliente, str_replace("{valor}", number_format($valor,2), str_replace("{moneda}", $moneda, _TBIO_REV_APROV)));
			
			if (mandaCorreo(_TBIOREV_SUBJECT, $message, $arrayToCom, 66)) {

				echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
						La Transferencia fu&eacute; aprobada, se envi&oacute; una copia al Cliente final y otra al usuario del comercio que la indic&oacute;</div><script language=\"JavaScript\" type=\"text/javascript\">window.open('"._ESTA_URL."/admin/index.php?componente=comercio&pag=repTransf', '_self')</script>";
			}
		}
	}

//	lee los datos de la transferencia
	$query = "select c.nombre comercio, t.*, r.*, m.moneda 'monedan',
                case t.idadmin when null then null else (select a.nombre from tbl_admin a where a.idadmin = t.idadmin) end autor
				from tbl_transferencias t, tbl_comercio c, tbl_transacciones r, tbl_moneda m
				where t.moneda = m.idmoneda and r.idtransaccion = t.idTransf and t.idCom = c.id and idTransf = '$id'";
// 	echo "<br>".$query;
	$temp->query($query);
	$arrSal = $temp->loadAssocList();

	if (count($arrSal) > 0) {
		$item = $arrSal[0];
//        print_r($item);
		$id = $item['idTransf'];
		$monedan = $item['monedan'];
		$cliente = utf8_decode($item['cliente']);
		$email = $item['email'];
		$cuentaB = $item['cuentaB'];
		$id = $item['idTransf'];
		$code = $item['facturaNum'];
		$autor = utf8_decode($item['autor']);
		$idComercio = $item['idcomercio'];
		$fecha = $item['fecha'];
		$moneda = $item['moneda'];
		$valor = $item['valor_inicial'] / 100;
		$valorEu = $item['euroEquiv'];
		if ($moneda == '978' && $item['euroEquiv'] == '') $valorEu = $valor;
		$concepto = str_replace(" <br>", "", $item['concepto']);
		$idioma = $item['idioma'];
		$comercio = utf8_decode($item['comercio']);
		$pais = $item['idpais'];
		$pasarela = $item['idPasarela'];
		$estado = $item['estado'];
        ($estado == 'P') ? $fecha1 = time() : $fecha1 = $item['fechaTransf'];
		$tasa = $item['tasa'];
		$pasarela = $item['pasarela'];
		$activa = $item['activa'];
		$vista = $item['vista'];
		$enviada = $item['enviada'];
	}
	if ($tasa == '0.0000') $tasa = '';

	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = 'Revisar Transferencia';
	if ($_SESSION['grupo_rol'] <= 5 || $_SESSION['grupo_rol'] == 18) $html->tituloTarea = "Modificar transferencia"; else $html->tituloTarea = "&nbsp;";
	$html->anchoTabla = 600;
	$html->anchoCeldaI = 200;
	$html->anchoCeldaD = 360;
	$html->java = "
	<style type='text/css' media='screen'>
		#vista{color:blue;font-size:14px;font-weight:bold;}
	</style>
	<script language=\"JavaScript\" type=\"text/javascript\">
		function verifica() {
			if (
					(checkField (document.forms[0].servicio, isAlphanumeric, '')) 
                    && (document.forms[0].servicio.value.length > 5)
				) {
				$('#inserta').val('2');
				document.forms[0].submit();
			} else {alert('Debe escribir el motivo de la denegación');}
			return false;
		}
	</script>";

	$html->inHide($_SESSION['id'], 'usuario');
	$html->inHide('1', 'inserta');
	$html->inHide($id, 'tf');
	if ($_SESSION['grupo_rol'] <= 1 || $_SESSION['grupo_rol'] == 18) {
		if ($vista) $html->inTextoL('Vista por el Cliente','vista');
		$html->inTexto(_AVISO_NUMERO, $id);
		$html->inTexto(_AVISO_CODIGO, $code);
        $html->inTexto('Transferencia impuesta por: ', $autor);
		$html->inTexto(_FORM_NOMBRE_CLIENTE, $cliente);
		$html->inTexto(_FORM_CORREO, $email);
		$html->inTexto(_COMPRUEBA_IMPORTE, number_format($valor, 2));
		$temp->query("select moneda from tbl_moneda where idmoneda = $moneda");
		$html->inTexto(_COMPRUEBA_MONEDA, $temp->f('moneda'));
		$temp->query("select nombre from tbl_pasarela where idPasarela = $pasarela");
		$html->inTexto(_COMERCIO_PASARELA, $temp->f('nombre'));
		$temp->query("select nombre from tbl_comercio where idcomercio = $idComercio");
		$html->inTexto(_COMERCIO_TITULO, $temp->f('nombre'));
		$html->inTexarea('Motivo del rechazo', null, 'servicio', 10, null, null, null, 40, 'Si se deniega la Transferencia poner el motivo');
		$texto = 'Aprobar';
	
		$botones .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input onClick="document.forms[0].submit();" class="formul" id="enviaboton" name="enviaboton" type="button" value="Aprobar" />'.
					'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input onClick="verifica();" class="formul" id="enviaboton" name="enviaboton" type="button" value="Rechazar" />'.
					'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviaboton" name="enviaboton" type="reset" value="Cancelar" />';
		
		echo $html->salida($botones, $texto);
	} else {
		$sFilename = 'factura.ini';
		include "lang/correo".$item['idioma'].".php";
		
	if ($f = fopen ( $sFilename, "r" )) {
		while ( ! feof ( $f ) )
			$message .= fread ( $f, filesize ( $sFilename ) );
		fclose ( $f );
	} else return "<script type='text/javascript'>alert('Ocurrió un error al cargar el fichero.')</script>";
		
			$mensaje = str_replace ( '{idfactura}', $id, 
					str_replace ( '{clientenombre}', $cliente, 
					str_replace ( '{servicio}', $concepto, 
					str_replace ( '{serv}', _SERV, 
					str_replace ( '{val}', _VAL, 
					str_replace ( '{valor}', number_format ( $valor, 2 ) . ' ' . $monedan, 
					str_replace ( '{pagara}', $cuentaB, 
					str_replace ( '{fecha}', date ( 'd/m/Y H:i', time() ), 
					str_replace ( '{factura}', _FACTURA, 
					str_replace ( '{cliente}', _CLIENTE, 
					str_replace ( '{fechaHora}', _REPORTE_FECHAHORA, 
					str_replace ( '{nota}', _NOTA, 
					str_replace ( '{texto5}', _TEXTO5, $message )))))))))))));
			echo $mensaje;
	}
}

?>
