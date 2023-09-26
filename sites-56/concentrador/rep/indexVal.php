<?php

define( '_VALID_ENTRADA', 1 );
require_once( '../configuration.php' );
require_once( '../include/mysqli.php' );
require_once( '../include/correo.php' );
require_once( '../include/hoteles.func.php' );
require_once( '../admin/adminis.func.php' );
$temp = new ps_DB;
$correo = new correo();

$d = $_REQUEST;

/**************************************************************************************/
// $d['Ds_SignatureVersion'] = "HMAC_SHA256_V1";
// $d['Ds_MerchantParameters'] = "eyJEc19EYXRlIjoiMjMlMkYxMCUyRjIwMTUiLCJEc19Ib3VyIjoiMTQlM0EyMSIsIkRzX1NlY3VyZVBheW1lbnQiOiIxIiwiRHNfQW1vdW50IjoiMTAwIiwiRHNfQ3VycmVuY3kiOiI5NzgiLCJEc19PcmRlciI6IjE1MTAyMzE0MTc1NSIsIkRzX01lcmNoYW50Q29kZSI6IjAzMDYzMTcyNSIsIkRzX1Rlcm1pbmFsIjoiMDA0IiwiRHNfUmVzcG9uc2UiOiIwMDAwIiwiRHNfVHJhbnNhY3Rpb25UeXBlIjoiMCIsIkRzX01lcmNoYW50RGF0YSI6IiIsIkRzX0F1dGhvcmlzYXRpb25Db2RlIjoiNDg4NjgxIiwiRHNfQ29uc3VtZXJMYW5ndWFnZSI6IjEiLCJEc19DYXJkX0NvdW50cnkiOiI3MjQifQ==";
// $d['Ds_Signature'] = "OECk1a2fvbSS7g0DA4cIAwCon1ktRHvIq7ted5Y6UpQ=";

$d['Ds_Date'] = '23/10/2015';
$d['Ds_Hour'] = '15:33';
$d['Ds_SecurePayment'] = '1';
$d['Ds_Amount'] = '100';
$d['Ds_Currency'] = '978';
$d['Ds_Order'] = '151023153225';
$d['Ds_MerchantCode'] = '285772844';
$d['Ds_Terminal'] = '005';
$d['Ds_Signature'] = '3C8FB7453B8F69E8AD6E226EC05D12E9E559078D';
$d['Ds_Response'] = '0000';
$d['Ds_TransactionType'] = '0';
$d['Ds_MerchantData'] = '';
$d['Ds_AuthorisationCode'] = '335771';
$d['Ds_ConsumerLanguage'] = '2';
$d['Ds_Card_Country'] = '724';
/**************************************************************************************/

$correoMi = "fecha=".date('d/m/Y H:i:s')."<br>\n";
$correoMi .= "Entrada de valores:<br>\n";
foreach ($d as $key => $value) {
	$correoMi .= $key." = ".$value."<br>\n";
}

$cookie = $d['resp'];
$sabad = $d['est'];
$arrayTo = array();
$codigo = '';
if (isset($d['pszPurchorderNum'])) { //Santander
	$cookie = $d['pszPurchorderNum'];
	if ($d['result'] == 0) {
		$codigo = $d['pszApprovalCode'];
		$sabad = 'ok';
	}
} elseif (isset ($d['Ds_Order'])) $codigo = $d['Ds_Order']; //bankia, caixa

$correoMi .= "Código de Aceptada $codigo<br>\n";


/**************************************************************************************/
//$cookie='140708051097';
//$codigo='994724';
//$sabad = 'ok';
/**************************************************************************************/


//Revisa si la operación no ha sido trabajada en llegada.php
$q = "select t.idcomercio, identificador, idioma, t.moneda idMon, m.moneda, t.estado, p.nombre pasarela, c.nombre comercio, c.url, t.tipoEntorno, t.valor_inicial/100 vale, c.url_llegada "
		. "from tbl_transacciones t, tbl_comercio c, tbl_pasarela p, tbl_moneda m "
		. "where m.idmoneda = t.moneda and t.idcomercio = c.idcomercio and p.idPasarela = t.pasarela and t.idtransaccion = '$cookie' and t.estado = 'P'";
$correoMi .= "$q<br>\n";
$temp->query($q);
$can = $temp->num_rows();
$moneda = $temp->f('moneda');
$vale = $temp->f('vale');
$identif = $temp->f('identificador');
$idcomercio = $temp->f('idcomercio');
$tasa = leeSetup($moneda);
$comercio = $temp->f('comercio');
$pasarr = $temp->f('pasarela');

if ($can == 1 && $sabad == 'ok') {
	$correoMi .= "<br>\nOperación Pendiente en Concentrador y Aceptada en banco<br>\n<br>\n";
	$q = "select nombre, email from tbl_reserva where codigo = '$identif' and id_comercio = '$idcomercio'";
	$correoMi .= $q."<br>\n";
	$temp->query($q);
	if ($temp->getErrorMsg()) {$correoMi .= "Error: ".$temp->getErrorMsg()."<br>\n<br>\n";}
	$clien = $temp->f('nombre');
	$corCli = $temp->f('email');
	$text = "La operación $cookie de $comercio con identificador $identif realizada ahora (".date('d/m/Y H:i:s').") está Pendiente en el Concentrador pero viene Aceptada del TPV $pasarr. "
			. "Debe devolverse en Banco y en el Concentrador a cargo nuestro. También se le debe avisar a $comercio que al cliente {$temp->f('nombre')} con correo {$temp->f('email')}, "
			. "se le devolvió el importe y que no tiene validez el pago que realizó";
//	$text = "Se necesita devolver completa esta operación en {$arrEntr[4]} del comercio ".$arrEntr[7]." con identificador ".$arrEntr[8].
//			", la misma tiene número ".$arrEntr[6]." y código de autorización ".$arrEntr[0]." en el TPV ".$arrEntr[9].", fué realizada el día ".$arrEntr[5].
//			"\n\nDebe tener en cuenta que se realiza por haber quedado en proceso en el Concentrador, por lo que los cargos de la devolución no deben correr a cuanta del comercio\n";
	$correo->todo(29,$subject,$text);
	
//	if (strlen($codigo) > 4) {
//		$correoMi .= "La operación estaba Pendiente trato de ponerla Aceptada<br>\n";

//		$q = "update tbl_transacciones set codigo = '$codigo', valor = valor_inicial, id_error = null, tasa = $tasa, euroEquiv = (valor/100)/(tasa), "
//				. "estado = 'A', fecha_mod = ".time()." where idtransaccion = '$cookie'";
//		$correoMi .= "$q<br>\n";
//		$temp->query($q);
//		if ($temp->getErrorMsg()) $correoMi .= $temp->getErrorMsg()."<br>\n";
//		$q = "update tbl_reserva set id_transaccion = '$cookie', bankId = '$codigo', fechaPagada = ".time().", estado = 'A', est_comer = 'P', valor = $vale "
//				. "where codigo = '$identif' and id_comercio = $idcomercio";
//		$correoMi .= "$q<br>\n";
//		$temp->query($q);
//		if ($temp->getErrorMsg()) $correoMi .= $temp->getErrorMsg()."<br>\n";
//		$q = "update tbl_amadeus set idtransaccion = '$cookie', estado = 'A', fechamod = ".time().", codigo = '$codigo' "
//				. "where idcomercio = '$idcomercio' and rl = '$identif'";
//		$correoMi .= "$q<br>\n";
//		$temp->query($q);
//		if ($temp->getErrorMsg()) $correoMi .= $temp->getErrorMsg()."<br>\n";
//	} else {
//		$correo->todo(16, 'Operación que está Pendiente y viene Aceptada del Banco', "Operación $cookie Pendiente en la Base de datos y entra Aceptada del banco pero viene sin código.");
//	}
}

//echo $correoMi;

$query = sprintf("select idtransaccion, t.idcomercio, identificador, codigo, idioma, fecha_mod, valor, moneda, t.estado, 
		c.nombre, c.url, t.tipoEntorno, t.valor/100, t.tpv, t.pasarela, t.tasa
			from tbl_transacciones t, tbl_comercio c
			where t.idcomercio = c.idcomercio
				and idtransaccion = '%s'",
			quote_smart($cookie));
$temp->query($query);
$valores = $temp->loadRow();
//echo "query=".$query;

$correoMi .= "$query<br>\n";
$correoMi .= "\n<br>Lectura de valores:<br>\n";
if(is_array($valores)) {
    foreach ($valores as $key => $value) {
        $correoMi .= $key." = ".$value."<br>\n";
    }
}

//carga el código javascript para destruir iframes excepto para el nuevo sitio de cubana
$dstrIframe = '';
if ($valores[1] != '140778652871') $dstrIframe = 'if (parent.frames.length > 0) parent.location.href = self.document.location;';

$correoMi .= "dstrIframe = $dstrIframe<br>\n";
$correoMi .= "comercio = ".$valores[1]."<br>\n";

if (count($valores) > 0) {
 	$correoMi .=  "\n<br>SIIIIIIIIIIIIIII<br>\n";
 	$correoMi .=  $firma."\n<br>cookies= ".$cookie."<br>\n";

    $query = "select * from tbl_reserva where id_comercio = '".$valores[1]."' and codigo = '".$valores[2]."'";
    $temp->query($query);
 //   echo $query;
    $pago = $temp->loadRow();
	$correoMi .=  "\n<br>$query<br><br>\n\n";
	$correoMi .= "\n<br>Lectura de pago:<br>\n";
    if (is_array($pago)) {
        foreach ($pago as $key => $value) {
            $correoMi .= $key." = ".$value."\n";
        }
	}
	
	if ($valores[14] == 12 || $valores[14] == 53 || $valores[14] == 50) {
		if ($sabad == 'ok') $pasEs = 'A'; else $pasEs = 'D';
		if ($valores[8] != $pasEs) {
			$correoMi .= "El estado de la operación no es el mismo que el que está en la tabla transacciones\n";
			if ($valores[8] != 'A') {
				$valores[8] = $pasEs;
				$q = "update tbl_transacciones set estado = '".$valores[8]."' where idtransaccion = '".$valores[0]."'";
				$temp->query($q);
				$correoMi .= $q."\n";
			}
			$correo->todo(16, 'Operación con estado distintos revisar', $correoMi);
		}
	}
	
	$query = "update tbl_reserva set id_transaccion = '".$valores[0]."', bankId = '".$valores[3]."', fechaPagada = ".$valores[5].",
					estado = '".$valores[8]."', est_comer = '".$valores[11]."', valor = ".$valores[12]."
				where codigo = '".$valores[2]."' and id_comercio = ".$valores[1];
	$temp->query($query);
	$correoMi .=  "\n<br> $query <br><br>\n\n";
	
	$correoMi .= "valores= {$valores[1]} && {$pago[18]} && {$valores[13]} <br>\n";

	if (count($pago) == 0 
            || ($valores[1] == '129025985109' && $pago[18] == 'S' && $valores[13] == 0) //Para pagos de Cubana
//            || ($valores[1] == '122327460662' && $pago[18] == 'S' && $valores[13] == 0)//Para pagos de Prueba haciéndose pasar como Cubana
            ) 
        { //no hay pago online o el comercio es Cubana

        if ($valores[1] != '122327460662' && $valores[1] != '129025985109') { //Camino de todos los comercios exceto Prueba y Cubana
            $firma = convierte($valores[1], $valores[2], $valores[6], $valores[7], $valores[8], $valores[0], date('d/m/y h:i:s', $valores[5]));
    $correoMi .=  "firma={$valores[1]}, {$valores[2]}, {$valores[6]}, {$valores[7]}, {$valores[8]}, {$valores[0]}, ".date('d/m/y h:i:s', $valores[5])."<br>";
            $cadena = "<script>$dstrIframe
                    </script>
                    <form id=\"envia\" action=\"".$valores[10]."\" method=\"post\">
                        <input type=\"hidden\" name=\"comercio\" value=\"".$valores[1]."\">
                        <input type=\"hidden\" name=\"transaccion\" value=\"".$valores[2]."\">
                        <input type=\"hidden\" name=\"importe\" value=\"".$valores[6]."\">
                        <input type=\"hidden\" name=\"moneda\" value=\"".$valores[7]."\">
                        <input type=\"hidden\" name=\"resultado\" value=\"".$valores[8]."\">
                        <input type=\"hidden\" name=\"codigo\" value=\"".$valores[0]."\">
                        <input type=\"hidden\" name=\"idioma\" value=\"".$valores[4]."\">
                        <input type=\"hidden\" name=\"fecha\" value=\"".date('d/m/y h:i:s', $valores[5])."\">
                        <input type=\"hidden\" name=\"tasa\" value=\"".$valores[15]."\">
                        <input type=\"hidden\" name=\"firma\" value=\"$firma\">
                    </form>";
            $cadena .= '<script>document.writeln("<div style=\"margin:"+
                       window.innerHeight/2
                       +"px 0 0 "+
                       ((window.innerWidth)-400)/2
                       +"px; width:400px; text-align:center;\">"
                       )</script>
                       Gracias por usar nuestra Pasarela...<br>Thanks for using our Point of Sale...';
            $correoMi .= $cadena."<br>";
            $cadena .= "<script language=\"JavaScript\">
                        document.forms[0].submit();
                    </script>";
            echo $cadena;
        } else {
        
            $arrMerc = array('122327460662' => 'ADMPADMP','129025985109' => 'AAWOAAWO'); //array para site Cubana
            echo "<script>$dstrIframe</script>";

            //lee los datos tanto para transacciones aceptadas como denegadas
            $q = "select lastName, urlko from tbl_amadeus where rl = '".$valores[2]."' and idcomercio = '".$valores[1]."'";
            $correoMi .= "<br>\n".$q;
            $temp->query($q);
            $ape = 	str_replace("ñ", "n", 
            		str_replace("ú", "u", 
            		str_replace("í", "i", 
            		str_replace("é", "e", 
          			str_replace("á", "a", 
            		str_replace("ó", "o", 
            					$temp->f('lastName')))))));
            $url = $temp->f('urlko');
            if ($valores[8] == 'A') {
                $correoMi .= "<br>\nTransacción aceptada";
                if ($valores[1] == '129025985109') $url  = "http://wftc1.e-travel.com/plnext/cubanaairlines/RetrievePNR.action?"; //Para Cubana
//                if ($valores[1] == '122327460662') $url = "https://siteacceptance.wftc1.e-travel.com/plnext/pspcuba/RetrievePNR.action?"; //Para Prueba
                $url .= "SITE=".$arrMerc[$valores[1]]."&LANGUAGE=ES&EXTERNAL_ID=CU&DIRECT_RETRIEVE=TRUE&REC_LOC=".$valores[2]."&DIRECT_RETRIEVE_LASTNAME=".$ape;
                
            } else $correoMi .= "<br>\nTransacción denegada";
            
            $correoMi .= "<br>\n".$url;
            $cadena = "<script language=\"JavaScript\">window.open('$url','_self');</script>";
            echo $cadena;
        }
	
    } else { //hay pago online
		
$correoMi .=  "valor=".$pago[18]."<br>\n" ;

        if ($valores[8] == 'A') {
            if ($pago[18] == 'N') { //no es pago al momento
                echo "<script language=\"JavaScript\">$dstrIframe"
						. "window.open('"._ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]."', '_self');
                    </script>";
            } else { //es pago al momento
				if ($valores[13] != '1') {
					$correoMi .= "Aceptada al Concentrador <br>\n";
                    (strstr($pago[23], "admin.admin")) ?
                        $sit = "https://".$pago[23]."/index.php?componente=comercio&pag=cliente":
                        $sit = "https://".$pago[23]."/admin/index.php?componente=comercio&pag=cliente";
					$correoMi .= $sit."<br>\n";
					echo "<script language=\"JavaScript\">$dstrIframe
						window.open('"._ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]."', '_new');
						window.open('"._ESTA_URL."/ticket.php?tr=".$valores[2]."&co=".$valores[1]."', '_new');
						window.open('".$sit."', '_self');
						</script>";
				} else {
					$correoMi .= "Aceptada al TPVV <br>\n";
					echo "<script language=\"JavaScript\">$dstrIframe
						window.open('"._ESTA_URL."/voucher.php?tr=".$valores[2]."&co=".$valores[1]."', '_new');
						window.open('"._ESTA_URL."/ticket.php?tr=".$valores[2]."&co=".$valores[1]."', '_new');
						window.open('https://tpvv.administracomercios.com', '_self');
						</script>";
				}
            }
        } else {
            if ($pago[18] == 'N') {  //no es pago al momento
				$q = "select nombre, datos from tbl_comercio where idcomercio = ".$valores[1];
				$temp->query($q);
				$comNom = $temp->f('nombre');
				$comDat = $temp->f('datos');
				?>
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
				<title><?php echo $titulo ?></title>
				<link href="../admin/template/css/admin.css" rel="stylesheet" type="text/css" />
				<!--<link href="../template/css/calendar.css" rel="stylesheet" type="text/css" />-->
				<script>
					<?php echo $dstrIframe; ?>
				</script>
				</head>
				<body>
					<div id="encabPago">
						<div id="logoPago"><img src="../admin/template/images/banner2.png" /> </div>
						<div class="inf"></div>
					</div>
					<div id="cuerpoPago">
						El pago realizado ha reportado un error. Contacte su proveedor en:<br />
						The Payment was reported as null. Contact your provider at:<br /><br />
						<?php echo $comNom; ?><br />
						<?php echo $comDat; ?>
					</div>
					<div>
						<div class="inf2"></div>
						Copyright &copy; Travels &amp; Discovery, <?php echo date('Y', time()); ?><br /><br />
					</div>
				</body>
				</html>
			<?php
            } else { //es denegada con pago al momento
					if ($valores[13] != '1') { 
						$correoMi .= "Denegada al Concentrador<br>";
		                echo "<script language=\"JavaScript\">$dstrIframe
		                       window.open('"._ESTA_URL."/admin/index.php?componente=comercio&pag=cliente', '_self');
		                    </script>";
                	} else {
						$correoMi .= 'Denegada al TPVV<br>';
						echo "<script language=\"JavaScript\">$dstrIframe
						window.open('https://tpvv.administracomercios.com', '_self');
						</script>";
					}
            }
        }

    }
}
$subject = "Llegada del banco a rep-index";

//correoAMi($subject,$correoMi);
$correo->todo(16, $subject, $correoMi);

//$query = "insert into tbl_traza (titulo, traza, fecha) values ('$subject', '".htmlentities($correoMiMi, ENT_QUOTES)."', '".date('d/m/Y H:i:s')."')";
//$correoMi .= "\n$query";
//mail($to, $subject, $correoMi, $headers);
//$temp->query($query);


echo "<script>$dstrIframe</script>";
?>
