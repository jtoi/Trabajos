<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$ent = new entrada;
$corCreo = new correo();


$d = $_REQUEST;
$id = $ent->isEntero($d['tf'], 12);
// echo $GLOBALS['sitio_url']."<br>";
// if (strpos($GLOBALS['sitio_url'], 'admin')) $url = $GLOBALS['sitio_url']."index.php?componente=comercio&pag=reporte";
// else $url = $GLOBALS['sitio_url']."admin/index.php?componente=comercio&pag=reporte";
// echo $url;

// var_dump($d);exit;

if ($id) {
	//actualiza los datos de la transferencia modificados
	if ($d['inserta'] == 1) {
		// print_r($d);
		$sale = '';
        // echo $d['fecha1'].' '.date("H:i:s");

		$fecha1 = to_unix($ent->isDate($d['fecha1']).' '.date("H:i:s"));
		$importe = $d['importe'] * 100;
		$cambioRate = $d['tasa'];
		
		//	busca la conversion de moneda
		if ($cambioRate == '' || $cambioRate == 0) {
			switch ($d['moneda']) {
				case '840':
					$cambioRate = leeSetup('USD');
					break;
				case '124':
					$cambioRate = leeSetup('CAD');
					break;
				case '826':
					$cambioRate = leeSetup('GBP');
					break;
				default:
					$cambioRate = 1;
					break;
			}
		}

		if ($d['activa'] == 0 && $d['pagada'] == 'P') $d['pagada'] = 'N';
		// var_dump($d);
		
//		actualiza la tabla transferencias
		$query = "update tbl_transferencias set cliente = '{$d['nombre']}', email = '{$d['email']}', cuentaB = '{$d['cuenta']}', idcomercio = '{$d['comercio']}', facturaNum = '{$d['codigo']}', fechaTransf = '{$fecha1}', valor = '$importe', estado = '{$d['pagada']}', moneda = '{$d['moneda']}', idPasarela = '{$d['pasarela']}', activa = '{$d['activa']}' ";
		if ($d['enviada']) $query .= ", enviada = '{$d['enviada']}' ";
		$query .= " where idTransf = '$id'";
		$temp->query($query);
		$sale .= $query."<br>";
// echo $query."<br>";exit;
		if ($d['importeEu'] == '') $ue = $d['importe'] / $cambioRate;
		else {
			$ue = $d['importeEu'];
			$cambioRate = $d['importe'] / $d['importeEu'];
		}

//		actualiza la tabla transacciones
		if ($d['pagada'] == 'A')
			$query = "update tbl_transacciones set fecha_mod = $fecha1, tasa = '$cambioRate', euroEquiv = '$ue', identificador = '{$d['codigo']}',
						valor = '$importe', estado = '{$d['pagada']}', moneda = '{$d['moneda']}', pasarela = '{$d['pasarela']}'
					where idtransaccion = '$id'";
		else
			$query = "update tbl_transacciones set fecha_mod = $fecha1, tasa = '', euroEquiv = '', identificador = '{$d['codigo']}',
						valor_inicial = '$importe', pasarela = '{$d['pasarela']}', valor = 0, estado = '{$d['pagada']}', moneda = '{$d['moneda']}'
					where idtransaccion = '$id'";
		$temp->query($query); echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
			Datos actualizados</div>";
		$sale .= $query."<br>";

		if ($d['pagada'] == 'A') {

			$temp->query("select nombre from tbl_comercio where idcomercio = ".$d['comercio']);
			$comNom = $temp->f('nombre');
			$temp->query("select moneda from tbl_moneda where idmoneda = ".$d['moneda']);
			$monNom = $temp->f('moneda');
			
			$subject = "Transferencia Realizada al comercio ".$comNom." monto ".number_format(($importe/100),2,'.',' ') ." $monNom";
			$message = "Estimado,<br><br> El pago de la factura enviada al Cliente ha llegado<br>
				Cliente: ".$d['nombre']." <br>
				Referencia del Comercio: ".$d['codigo']."<br>
				N&uacute;mero de transaccion: ".$id." <br>
				Estado de la transferencia: Pagada <br>
				Fecha: ".date('d/m/y h:i:s', $fecha1)."<br>
				Valor: ".number_format(($importe/100),2,'.',' ') .$monNom;

			$correoMi .= "<br>\nCorreo Estado transaccion";
			$corCreo->todo(14, $subject, $message);
			$corCreo->destroy();
		}
		// echo $sale; exit;
		?>
		<form name="vuelve" action="">
			<input type="hidden" name="pag" value="reporte">
			<input type="hidden" name="componente" value="comercio">
			<input type="hidden" name="nombre" value="<?php echo $id; ?>">
		</form>
		<script type="text/javascript" lang="Javascript">
<!--
document.vuelve.submit();

//-->
</script>
		<?php 

	}

	if ($d['factura'] == 1 || $d['mifactura'] == 1 || $d['clifactura'] == 1 ) {

	//	lee los datos de la transferencia
		$query = "select m.moneda monedaSi, t.idPasarela, c.nombre comercio, c.datos, t.* from tbl_transferencias t, tbl_comercio c, tbl_moneda m
					where m.idmoneda = t.moneda and t.idCom = c.id and idTransf = '{$d['tf']}'";
//		echo $query;
		$temp->query($query);
		$arrSal = $temp->loadAssocList();
		$item = $arrSal[0];
		$item['concepto'] = str_replace("<br>", "", $item['concepto']);
// print_r($arrSal);
		//		Carga la factura y la prepara con los datos
		$sFilename = 'factura.ini';
		$f = fopen($sFilename, "r");
		while(!feof($f))
			$message .= fread($f, filesize($sFilename));
		fclose($f);
		$image = $item['idcomercio'].".jpg";
		
//		Determina el idioma de la factura según el destino
//		if ($d['factura'] == 1 || $d['mifactura'] == 1) {if ($d['idioma'] == "spanish") $idio = "es"; else $idio = "en";}
//		else $idio = $item['idioma'];
//		include "lang/correo".$idio.".php";
		include "lang/correo".$item['idioma'].".php";
//		echo $d['idioma'];
		
		
		$texto2 = '';
		if ($item['moneda'] == '840') {
			$cuenta = _AVISO_CTA;
		} else {
//			$cuenta = _AVISO_CTA1;
			$cuenta = _AVISO_CTA;
//			$texto2 = _TEXTO2;
		}
		$texto1 = _TEXTO1;
		$texto3 = _TEXTO3.":";

		if ($item['idPasarela'] == null) {
			$nota = str_replace('{aviso}', $d['tf'], _AVISO_NOTA);
			$texto1 = '';
			$texto2 = '';
			$texto3 = '';
		} else $nota = _NOTA.":";
		
//		modifico los corchetes de la factura
		$message = str_replace('{empresa}', $item['comercio'], $message);
		$message = str_replace('{datos}', $item['datos'], $message);
		$message = str_replace('{idfactura}', $d['tf'], $message);
		$message = str_replace('{clientenombre}', $item['cliente'], $message);
		$message = str_replace('{ctacliente}', $item['cuentaB'], $message);
		$message = str_replace('{servicio}', $item['concepto'], $message);
		$message = str_replace('{valor}', number_format(($item['valor'] / 100),2).' '.$item['monedaSi'], $message);
		$message = str_replace('{pagara}', $cuenta, $message);
		$message = str_replace('{fecha}', date('d/m/Y H:i', $item['fecha']), $message);
		$message = str_replace('{factura}', _FACTURA, $message);
		$message = str_replace('{de}', _DE, $message);
		$message = str_replace('{cliente}', _CLIENTE, $message);
		$message = str_replace('{cuenta}', _CUENTA, $message);
		$message = str_replace('{servicioProducto}', _SERVICIOPROD, $message);
		$message = str_replace('{pagarafavor}', _PAGAFACTURA, $message);
		$message = str_replace('{fechaHora}', _REPORTE_FECHAHORA, $message);
		$message = str_replace('{estimado}', _ESTIMADO, $message);
		$message = str_replace('{selehaenviado}', _ENVIADO, $message);
		$message = str_replace('{hasolicitado}', _REQUERIDO, $message);
		$message = str_replace('{dirigirla}', _DIRIGIRLA, $message);
		$message = str_replace('{nota}', $nota, $message);
		$message = str_replace('{texto1}', str_replace('%oper%', $d['tf'], _TEXTO4), $message);
		$message = str_replace('{texto2}', '', $message);
		$message = str_replace('{texto3}', '', $message);
			$messages = str_replace('{id}', "logos/".$image, $message);

//		la factura es para aparecer en pantalla
		if ($d['factura'] == 1) {
			echo "<div style='width:900px;margin:0 auto;'>$messages</div>";
		}
        $message = $messages;

//		la factura es para enviarla al clliente
		if ($d['clifactura'] == 1) {
//			Hace el envío del correo
			$arrayTo[] = array($d['nombre'], $correo);
			$arrayTo[] = array($_SESSION['admin_nom'], $_SESSION['email']);
			$subject = "Factura a favor de ".$item['comercio'];
            $des = true;
            foreach ($arrayTo as $todale) {
                if ($des) {
                    $corCreo->to($todale[0]." <".$todale[1].">");
                    $des = false;
                } else $corCreo->add_headers ("Cc: ".$todale[0]." <".$todale[1].">");
            }
            $corCreo->todo(28,$subject,$message);
            

			echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
				"._COMERCIO_FACT_SI."</div>";
			
		}

//		la factura es para enviármela
		if ($d['mifactura'] == 1) {

//			Hace el envío del correo
			$arrayTo[] = array($_SESSION['admin_nom'], $_SESSION['email']);
			$subject = "Factura a favor de ".$item['comercio'];
            $des = true;
            foreach ($arrayTo as $todale) {
                if ($des) {
                    $corCreo->to($todale[0]." <".$todale[1].">");
                    $des = false;
                } else $corCreo->add_headers ("Cc: ".$todale[0]." <".$todale[1].">");
            }
            $corCreo->todo(28,$subject,$message);

			echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
				"._COMERCIO_FACT_SI."</div>";
			
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
	$html->tituloPag = _MENU_ADMIN_TRANSFERENCIA;
	if ($_SESSION['grupo_rol'] <= 5 || $_SESSION['grupo_rol'] == 18) $html->tituloTarea = "Modificar transferencia"; else $html->tituloTarea = "&nbsp;";
	$html->anchoTabla = 600;
	$html->anchoCeldaI = 200;
	$html->anchoCeldaD = 360;
	$html->java = "
		<style type='text/css' media='screen'>
			#vista{color:blue;font-size:14px;font-weight:bold;}
		</style>
		<script language=\"JavaScript\" type=\"text/javascript\">
		function verifica() {";
	if ($pasarela == 0 ) {
		$html->java .= "return true;";
	} else {
	$html->java .= "if (
					(checkField (document.forms[0].nombre, isAlphanumeric, ''))&&
					(checkField (document.forms[0].email, isEmail, ''))&&
					(checkField (document.forms[0].importe, isMoney, ''))&&
					(checkField (document.forms[0].servicio, isAlphanumeric, ''))
				) {
				document.getElementById('comer').value = document.getElementById('comercio').value;
				document.getElementById('enviaForm').style.display='none';
				return true;
			}
			return false;";
	}
	$html->java .= "
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

		$(document).ready(function(){
			$('textarea').attr('value', '$concepto');
			$('#enviaboton').click(function(){
				$('#inserta').attr('value', '0');
				$('#factura').attr('value', '1');
				$('form').submit();
			});
			$('#enviami').click(function(){
				$('#inserta').attr('value', '0');
				$('#factura').attr('value', '0');
				$('#mifactura').attr('value', '1');
				$('#clifactura').attr('value', '0');
				$('form').submit();
			});
			$('#enviacli').click(function(){
				$('#inserta').attr('value', '0');
				$('#factura').attr('value', '0');
				$('#mifactura').attr('value', '0');
				$('#clifactura').attr('value', '1');
				$('form').submit();
			});
		});

	</script>";

	$html->inHide($_SESSION['id'], 'usuario');
	$html->inHide('1', 'inserta');
	$html->inHide('0', 'factura');
	$html->inHide('', 'comer');
	$html->inHide('0', 'mifactura');
	$html->inHide('0', 'clifactura');
	$html->inHide($id, 'tf');
	if ($_SESSION['grupo_rol'] <= 1 || $_SESSION['grupo_rol'] == 18) {
		if ($vista) $html->inTextoL('Vista por el Cliente','vista');
		$html->inTextb(_AVISO_NUMERO, $id, 'transferencia', null, null, null);
		$html->inTextb(_AVISO_CODIGO, $code, 'codigo', null, null, null);
		$arrIdio = array (1, 0);
		$arrEtiq = array('Si', 'No');
		$html->inRadio("Activa", $arrIdio, 'activa', $arrEtiq, $activa, null, false);
		if ($vista)
			$html->inRadio("Enviada al Banco", $arrIdio, 'enviada', $arrEtiq, $enviada, null, false);
        $html->inTexto('Transferencia impuesta por: ', $autor);
		$html->inTextb(_FORM_NOMBRE_CLIENTE, $cliente, 'nombre', null, null, null);
		$html->inTextb(_FORM_CORREO, $email, 'email');
		$html->inTextb(_COMPRUEBA_IMPORTE, $valor, 'importe', null, null, null);
		$query = "select idmoneda id, moneda nombre from tbl_moneda";
		$html->inSelect(_COMPRUEBA_MONEDA, 'moneda', 1, $query, $moneda);
		$html->inTextb(_AVISO_VALOREU, $valorEu, 'importeEu', null, ' EUR', null);
		$html->inTextb(_AVISO_TASA, $tasa, 'tasa', null, ' En blanco coge la tasa del día', null);
		$html->inTexarea(_AVISO_OBSERVA, null, 'servicio', 7, null, null, null, null, 'Aclarar con lujo de detalles el servicio prestado');
		$query = "select idPasarela id, nombre from tbl_pasarela where tipo = 'T' and activo = 1 order by nombre";
		$html->inSelect(_COMERCIO_PASARELA, 'pasarela', 1, $query, $pasarela);
		$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
		$html->inSelect(_COMERCIO_TITULO, 'comercio', 1, $query, $idComercio);
		$arrIdio = array ('P', 'A');
		$arrEtiq = array('En Proceso', 'Pagada');
		$html->inRadio(_COMERCIO_PAGAR, $arrIdio, 'pagada', $arrEtiq, $estado, null, false);
		$fecha1 = date('d/m/Y', $fecha1);
		$html->inFecha('Fecha transferida', $fecha1, 'fecha1', 'fecha1', 'Fecha transferida', _VENTA_DESC_TEMP_FECHA1);
		$texto = '';
	
		if ($pasarela == 0) {
			$botones .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviaboton" name="enviaboton" type="button" value="' . _GRUPOS_FACTURA_VER . '" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviami" name="enviami" type="button" value="' . _GRUPOS_ENVIA_MI . '" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviacli" name="enviacli" type="button" value="' . _GRUPOS_ENVIA_CLI . '" />
			';
		} else {
			$botones .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviaForm" name="enviar" type="submit" value="Salvar" />
	                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="reset" type="reset" class="formul" value="' . _FORM_CANCEL . '" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviaboton" name="enviaboton" type="button" value="' . _GRUPOS_FACTURA_VER . '" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviami" name="enviami" type="button" value="' . _GRUPOS_ENVIA_MI . '" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="formul" id="enviacli" name="enviacli" type="button" value="' . _GRUPOS_ENVIA_CLI . '" />
			';
		}
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
