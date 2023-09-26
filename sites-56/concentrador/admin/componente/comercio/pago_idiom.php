<?php

defined('_VALID_ENTRADA') or die('Restricted access');


$html = new tablaHTML();
global $temp;
$corCreo = new correo();
$comer = $_SESSION['comercio'];
$d = $_POST;

//$d['comer'] = '131551467398'; $d['inserta'] = true; $d['comercio'] = '131551467398'; $d['nombre'] = 'June Baldry'; $d['email'] = 'eduardo@travelnet.cu'; $d['importe'] = '185.00'; $d['trans'] = ''; $d['tiempo'] = '3'; $d['moneda'] = '978'; $d['servicio'] = 'excursion'; $d['pago'] = 'N'; $d['idioma'] = 'es'; $d['pasarela'] = '21'; $d['enviar'] = 'Enviar';

$fechaNow = time();
if (_MOS_CONFIG_DEBUG) {
	echo "<br>";
	print_r($d);
	echo "<br>";
}

//inserta Articulo
if ($d['inserta']) {

	$arrayTo = array();
	$query = "select * from tbl_comercio where idcomercio = {$d['comer']}";
	$temp->query($query);
	$comercioN = $temp->f('nombre');
	$palabra = $temp->f('palabra');
	$estCom = $temp->f('estado');
	$idCom = $temp->f('id');
	$correoMas = $temp->f('correoMas');
	$correo = str_replace(" ", "", $d['email']);
	if ($d['pasarela'] == '')
		$pasarela = 1;
	else
		$pasarela = $d['pasarela'];

	if ($palabra != 'lore') {

		if (!$d['trans']) {
            $trans = trIdent('', false);
//			$salida = false;
//			while (!$salida) {
//				$trans = (string) (date("mdHis")); //.(rand (10, 99));
//				$query = "select count(*) total from tbl_reserva where codigo = '$trans'";
//				$temp->query($query);
//				if ($temp->f('total') == 0)
//					$salida = true;
//			}
		} else
			$trans = $d['trans'];

		$query = "select count(*) total from tbl_reserva where codigo = '$trans' and id_comercio = '{$d['comer']}'";
		$temp->query($query);
//		echo $query;
		if ($temp->f('total') == 0) {
			$query = "select count(*) total from tbl_transacciones where identificador = '$trans' and idcomercio = '{$d['comer']}'";
			$temp->query($query);
//			echo $query;

			if ($temp->f('total') == 0) {
//                echo "<br>".$d['servicio']."<br>";
                $ser = htmlentities($d['servicio'], ENT_QUOTES);
//                echo "<br>".htmlentities($d['servicio'], ENT_QUOTES)."<br>";
//                echo "<br>".$ser."<br>";
                $nmbr = htmlentities($d['nombre'], ENT_QUOTES);
				$query = "insert into tbl_reserva (id_admin, id_comercio, est_comer, codigo, nombre, email, servicio, valor_inicial, moneda, fecha, pMomento, idioma, pasarela, tiempoV, url)
							values ({$_SESSION['id']}, '{$d['comer']}', '$estCom', '$trans', '{$nmbr}', '{$correo}', '{$ser}', {$d['importe']},
								'{$d['moneda']}', $fechaNow, '{$d['pago']}', '{$d['idioma']}', $pasarela, {$d['tiempo']}, '{$_SERVER["SERVER_NAME"]}')";
				$temp->query($query);
                $error = $temp->getErrorMsg();
                if (strlen($error) > 0) {
                       $subject = "Error al insertar la invitación de pago";
                       $mensaje = "SQL: ".$query."<br>\nError: ".$error;
                       $corCreo->todo(24, $subject, $mensaje);
				       echo "<div style='text-align:center;color:red;'>"._COMERCIO_ERROR_INVIT."</div>";
                } else {
                    if ($_SESSION['codProdReserv']) {
                        $query = "update tbl_productosReserv set codVenta = '$trans' where codigo = '{$_SESSION['codProdReserv']}'";
                        $temp->query($query);
                    }

                    $query = "select moneda from tbl_moneda where idmoneda = {$d['moneda']}";
                    $temp->query($query);
                    $moneda = $temp->f('moneda');

                    if ($d['pago'] == 'S') { //Pago al momento
                        $importe = ($d['importe'] * 100);
                        $firma = md5($d['comer'] . $trans . $importe . $d['moneda'] . 'P' . $palabra);
                        //echo $firma;
                        $form = "
                                <form name='envPago' method='post' action='"._ESTA_URL."/index.php'>
                                    <input type='hidden' name='pasarela' value='{$pasarela}'/>
                                    <input type='hidden' name='comercio' value='{$d['comer']}'/>
                                    <input type='hidden' name='transaccion' value='$trans'/>
                                    <input type='hidden' name='importe' value='$importe'/>
                                    <input type='hidden' name='moneda' value='{$d['moneda']}'/>
                                    <input type='hidden' name='operacion' value='P'/>
                                    <input type='hidden' name='idioma' value='{$d['idioma']}'/>
                                    <input type='hidden' name='firma' value='$firma'/>
                                </form>
                                <script>document.envPago.submit();</script>
                        ";
                        echo $form;
                    } else { //Pago por correo
                        //Invitación de Pago que se envía al cliente y a mí
                        include 'lang/correo'.$d['idioma'].".php";
						echo 'lang/correo'.$d['idioma'].".php";
                        $arrayTo = array();
						$q = "select id from tbl_idioma where iso = '{$d['idioma']}'";
						$temp->query($q);
						$idId = $temp->f('id');
						$subject = str_replace('{COMERCIO}', $comercioN, _ASUNTO_INVIT);
						$adic = '';
						if ($pasarela != 15) $adic = _LEE3D;
                        $query = "select texto from tbl_traducciones where tipo = 0 and idcomercio = '{$idCom}' and idIdioma = $idId";
//						echo $query;
                        $temp->query($query);
                        $message = "<style>.boton{background-color:#5EBEEF;color:white;display:block;border:2px solid navy;font-weight:bold;height:30px;padding-top:5px;text-align:center;text-decoration:none;vertical-align:middle;width:90px;line-height:26px;margin:0 auto;}</style>";
                        $message .= $temp->f('texto');
                        $url = "<BR /><a class='boton' href='" . _ESTA_URL . "/pagoOnline.php?cod=$trans&com=" . $d['comer'] . "'>" . _CLICK_AQUI . "</a>";
                        $urla = "<BR />\n" . _ESTA_URL . "/pagoOnline.php?cod=$trans&com=" . $d['comer'];

                        $message = str_replace('{importe}', number_format($d['importe'], 2, '.', ' ') . ' ' . $moneda, $message);
                        $message = str_replace('{servicio}', $d['servicio'], $message);
                        $message = str_replace('{comercio}', $comercioN, $message);
                        $message = str_replace('{urla}', $urla, $message);
                        $message = str_replace('{url}', $url, $message) . $adic;

                        if (strstr($correo, ";"))
                            $corrArr = explode(";", $correo);
                        elseif (strstr($correo, ","))
                            $corrArr = explode(",", $correo);
                        else
                            $corrArr[0] = $correo;

                        if (_MOS_CONFIG_DEBUG) {
                            echo "correoArr=";
                            print_r($corrArr);
                            echo "<br>";
                        }

                        foreach ($corrArr as $item) {
                            $arrayTo[] = array($d['nombre'], $item);
                        }

                        if (_MOS_CONFIG_DEBUG) {
                            print_r($arrayTo);
                            echo "<br>";
                        }


                        $est = true;
                        foreach ($arrayTo as $todale) {
                            if ($est) {
                                $corCreo->to($todale[0]." <".$todale[1].">");
                                $est = false;
                            } else $corCreo->add_headers ("Cc: ".$todale[0]." <".$todale[1].">");

                            if (_MOS_CONFIG_DEBUG)
                                echo "header = $headers<br>";
                            if (_MOS_CONFIG_DEBUG)
                                echo "to = $to<br>";
                            if (_MOS_CONFIG_DEBUG)
                                echo "mensaje = $message<br>";
                        }
                        $corCreo->todo(23, $subject, $message);

                        //Aviso de envío de la invitación de pago
                        $arrayTo = array();
                        if ($correoMas == 1) {
                            $q = "select nombre, email from tbl_admin where idcomercio = '{$d['comer']}' and correoT = 1 and activo = 'S'";
                            if (_MOS_CONFIG_DEBUG) echo $q."<br>";
                            $temp->query($q);
                            $arrayTo = $temp->loadRowList();
                        }
                        $arrayTo[] = array($_SESSION['admin_nom'], $_SESSION['email']);
                        if (_MOS_CONFIG_DEBUG) {
                            echo "<br>**************************************************************<br>";
                            print_r($arrayTo);
                            echo "<br>**************************************************************<br>";
                            print_r($_SESSION);
                            echo "<br>**************************************************************<br>";
                            echo "<br>";
                        }

                        $subject = _COMERCIO_EMAIL_SUBJECT;
                        $message = _COMERCIO_EMAIL_MES;
                        $q = "select nombre from tbl_pasarela where idPasarela = '$pasarela'";
                        $temp->query($q);
                        $pasa = $temp->f('nombre');
                        $message = str_replace('{trans}', $trans, $message);
                        $message = str_replace('{servicio}', $d['servicio'], $message);
                        $message = str_replace('{nombre}', $d['nombre'], $message);
                        $message = str_replace('{importe}', number_format($d['importe'], 2, '.', ' '), $message);
                        $message = str_replace('{moneda}', $moneda, $message);
                        $message = str_replace('{pasarela}', $pasa, $message);

                        $est = true;
                        foreach ($arrayTo as $todale) {
                            if ($est) {
                                $corCreo->to($todale[0]." <".$todale[1].">");
                                $est = false;
                            } else $corCreo->add_headers ("Cc: ".$todale[0]." <".$todale[1].">");


                            if (_MOS_CONFIG_DEBUG)
                                echo "header = $headers<br>";
                            if (_MOS_CONFIG_DEBUG)
                                echo "to = $to<br>";
                            if (_MOS_CONFIG_DEBUG)
                                echo "mensaje = $message<br>";
                        }
                            $corCreo->todo(24, $subject, $message);
                    }

                    echo "<div style=\"text-align:center; margin-top: 20px; color:green; font-family:Arial sans-serif; font-size:11px\">
                        " . _COMERCIO_SOLC_SI . "</div>";
                }
			} else
				echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">
                " . _COMERCIO_CODE_YA . "</div>";
		} else
			echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">
                " . _COMERCIO_CODE_YA . "</div>";
	} else
		echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">
                " . _COMERCIO_SECRETA_NO . ".</div>";
}
$monedaid = '978';

if ($_SESSION['codProdReserv']) {
	$query = "select case p.codigo when '' then p.nombre else concat(p.nombre, ' - ', p.codigo) end nombre, r.precio
				from tbl_productosReserv r, tbl_productos p
				where p.id = r.idProd and r.codigo = " . $_SESSION['codProdReserv'];
//	echo "$query<br>";
	$temp->query($query);
	$precio = 0;
	$servicio = '';
	while ($temp->next_record()) {
		$precio += $temp->f('precio');
		$servicio .= $temp->f('nombre') . "\n";
	}
	$monedaid = $temp->f('idMon');
}

//javascript
$javascript = "
	<script language=\"JavaScript\" type=\"text/javascript\">
	function verifica() {
		if (
				(checkField (document.admin_form.nombre, isAlphanumeric, ''))&&
				(checkField (document.admin_form.email, isEmail, ''))&&
				(checkField (document.admin_form.tiempo, isInteger, ''))&&
				(checkField (document.admin_form.importe, isMoney, ''))&&
				(checkField (document.admin_form.trans, isUrl, 'true'))&&
				(checkField (document.admin_form.servicio, isAlphanumeric, ''))
			) {
            document.getElementById('comer').value = document.getElementById('comercio').value;
            document.getElementById('enviaForm').style.display='none';
			return true;
		}
		return false;
	}

	$(function() {
		$('textarea').supertextarea({maxw: 280, maxh: 100, minw: 130, minh: 20, dsrm: {use: false}, tabr: {use: false}, maxl: 1000});
	});
";
if ($alerta)
	$javascript .= "alert('$alerta');";
$javascript .= "</script>";


$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_PAGODIRECTO;
$html->anchoTabla = 600;
$html->anchoCeldaI = 255;
$html->anchoCeldaD = 325;
$html->tituloTarea = _COMERCIO_PAGO;
$html->java = $javascript;

$html->inHide($comer, "comer");
$html->inHide("true", "inserta");
$html->inTextb(_FORM_NOMBRE, "", "nombre");
$html->inTextb(_FORM_CORREO, "", "email");
$html->inTextb(_COMPRUEBA_IMPORTE, $precio, "importe");
$html->maxLenght = 12;
$html->inTextb(_COMPRUEBA_TRANSACCION, "", "trans", null, " " . _COMERCIO_GENERA);
$html->maxLenght = 150;
$html->inTextb(_COMERCIO_INVACTIVA, "3", "tiempo", null, _COMERCIO_INVACTIVAEXPL);
//if ($comer == 'todos') 
	$query = "select idmoneda id, moneda nombre from tbl_moneda order by moneda";
//else $query = "select idmoneda id, moneda nombre from tbl_moneda where idmoneda in ('840','978','826','392','124') order by moneda";
$html->inSelect(_COMPRUEBA_MONEDA, 'moneda', 2, $query, $monedaid);
$html->inTexarea(_COMERCIO_SER, $servicio, 'servicio', 7);
$valInicio = array('S', 'N');
$etiq = array(_COMERCIO_ALMOMENT, _COMERCIO_DIFERI);
$valor = 'S';
$html->inRadio(_COMERCIO_PAGOA, $valInicio, 'pago', $etiq, $valor);
$valor = 'en';
$q = "select iso id, nombre nombre from tbl_idioma order by id";
$html->inSelect(_PERSONAL_IDIOMA, 'idioma', 2, $q, $valor);

if ($comer == 'todos')
	$valInicio = "select idPasarela id, nombre from tbl_pasarela where nombre not like 'Trans%' ";
else {
	$query = "select pasarelaAlMom from tbl_comercio where idcomercio in (" . $comer . ")";
	$temp->query($query);
    $pasar = $temp->f('pasarelaAlMom');
//    if (is_array($arrPsr))
//    	$pasar = implode(',', $arrPsr);
	$valInicio = "select p.idPasarela id, p.nombre from tbl_pasarela p where p.idPasarela in ($pasar) order by idPasarela";
}

$html->inSelect(_COMERCIO_PASARELA, "pasarela", 2, $valInicio);

if ($comer == 'todos') {
	$valInicio = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
	$html->inSelect(_COMERCIO_TITULO, "comercio", 2, $valInicio);
} elseif (strpos($comer, ',')) {
	$valInicio = "select idcomercio id, nombre from tbl_comercio where idcomercio in ($comer) and activo = 'S' order by nombre";
	$html->inSelect(_COMERCIO_TITULO, "comercio", 2, $valInicio);
} else
	$html->inHide($comer, "comercio");

echo $html->salida();
?>
