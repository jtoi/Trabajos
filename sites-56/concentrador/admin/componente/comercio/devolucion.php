<?php

defined('_VALID_ENTRADA') or die('Restricted access');

$corCreo = new correo();
$d = $_REQUEST;
$ok = false;
$correoMi='';
// var_dump($d);echo"<br>";

if (_MOS_CONFIG_DEBUG) {
//     $d['iddentinf'] = '171009145548';
//     $d['valor'] = '6.00';
//     $d['valante'] = '6.00';
//     $d['devolAutom'] = 1;
}
// $d['valante'] = str_replace(" ","",str_replace(",","",str_replace(".","",$d['valante'])))/100;
// $d['valor'] = str_replace(" ","",str_replace(",","",str_replace(".","",$d['valor'])))/100;
// $d['valorop'] = str_replace(" ","",str_replace(",","",str_replace(".","",$d['valorop'])))/100;
// $d['valordev'] = str_replace(" ","",str_replace(",","",str_replace(".","",$d['valordev'])))/100;
$d['valante'] = str_replace("%",".",str_replace(" ","",str_replace(",","",str_replace(".","%",$d['valante']))));
$d['valor'] = str_replace("%",".",str_replace(" ","",str_replace(",",".",str_replace(".","%",$d['valor']))));
$d['valorop'] = str_replace("%",".",str_replace(" ","",str_replace(",",".",str_replace(".","%",$d['valorop']))));
$d['valordev'] = str_replace("%",".",str_replace(" ","",str_replace(",",".",str_replace(".","%",$d['valordev']))));
$autorizo = null;
$todok = 0;
// var_dump($d);echo"<br>";

foreach ($d as $key => $value) {
	$correoMi .= "$key => $value<br>";
}

if ($d['iddentinf'] && $d['valor'] > 0) {
	
	$q = "select t.moneda, m.moneda siglas, t.pasarela, t.idcomercio "
			. " from tbl_transacciones t, tbl_moneda m where t.moneda = m.idmoneda and t.idtransaccion = '" . $d['iddentinf'] . "'";
	$correoMi .= "$q<br>";
	
	// echo $q;
	$temp->query($q);
	$mon = $temp->f('moneda');
	$sig = $temp->f('siglas');
	$pasa = $temp->f('pasarela');
    $comer = $temp->f('idcomercio');
	$temp->query("select idcenauto from tbl_pasarela where idPasarela = '$pasa'");
	$centauto = $temp->f('idcenauto');
	$correoMi .= "select idcenauto from tbl_pasarela where idPasarela = '$pasa'<br>";
	

	if ($d['valor'] == $d['valante']) {
		$esta = 'B';
		$valorpon = ($d['valante'] - $d['valor']);
	} elseif ($d['valor'] < $d['valante']) {
		$esta = 'V';
		$valorpon = ($d['valante'] - $d['valor']);
	} else {
		// echo "sale";
		exit;
	}
	
    if ($comer != '527341458854') {
        if ($mon == '840') $cambio = leeSetup ('USD');
        elseif ($mon == '826') $cambio = leeSetup ('GBP');
        elseif ($mon == '124') $cambio = leeSetup ('CAD');
        elseif ($mon == '392') $cambio = leeSetup ('JPY');
        elseif ($mon == '152') $cambio = leeSetup ('CLP');
        elseif ($mon == '32') $cambio = leeSetup ('ARS');
        elseif ($mon == '032') $cambio = leeSetup ('ARS');
        elseif ($mon == '356') $cambio = leeSetup ('INR');
        elseif ($mon == '484') $cambio = leeSetup ('MXN');
        elseif ($mon == '604') $cambio = leeSetup ('PEN');
        elseif ($mon == '937') $cambio = leeSetup ('VEF');
        elseif ($mon == '949') $cambio = leeSetup ('TRY');
        elseif ($mon == '170') $cambio = leeSetup ('COP');
        else $cambio = 1;
    } else {
        if ($mon != '978') {
            $q = "select tasa from tbl_colCambBanco where idmoneda = '$mon' and idbanco = 17 order by fecha desc limit 0,1";
            $temp->query($q);
            $ta = $temp->f('tasa');
            $cambio = $ta + leeSetup('descCimex');
        } else $cambio = 1;
    }

	// echo "dev".$d['devolAutom'];
	if ($d['devolAutom'] == 1) { // Para las operaciones a devolver de forma automática
		if (strstr(_ESTA_URL, 'admin.administracomercios')) {
			$estaurl = _ESTA_URL . '/index.php?componente=comercio&pag=reporte';
		} else {
			$estaurl = _ESTA_URL . "/admin/index.php?componente=comercio&pag=reporte";
		}
		$todok = 0;
		$correoMi .= "$estaurl<br>";
		$correoMi .= "centauto=$centauto<br>";
		
//echo $correoMi;
		
		if ($centauto == 20) {//Bipay
			$q = "select c.comercio, c.clave, p.secure, t.moneda, case p.estado when 'P' then a.urlPro when 'D' then a.urlDes end url from tbl_cenAuto a, tbl_devoluciones d, tbl_colPasarMon c, tbl_transacciones t, tbl_pasarela p where t.pasarela = p.idPasarela and d.fechaDev = 0 and t.solDev = 1 and a.id = p.idcenauto and t.moneda = c.idmoneda and t.pasarela = c.idpasarela and t.idtransaccion = d.idtransaccion and t.idtransaccion = '{$d['iddentinf']}'";
			$temp->query($q);

			// echo $q."<br>";

			$valor = $d['valor'] * 100;
			$comer = $temp->f('comercio');
			$clave = $temp->f('clave');
			$mon	= $temp->f('moneda');
			$secur	= $temp->f('secure');
			$url	= $temp->f('url');

			$firma = hash("sha512", $valor . $d['iddentinf'] . $comer . $mon . $secur . $clave);
//			echo "$valor . ".$d['iddentinf']." . $comer . $mon . $secur . $clave <br>";
			// echo "url=$url<br>";

			$datos = array(
				"DS_MERCHANT_MERCHANTCODE" 		=> $comer,
				"DS_MERCHANT_TRANSACTIONTYPE"	=> 3,
				"DS_MERCHANT_SIGNATURE"			=> $firma,
				"DS_MERCHANT_AMOUNT"			=> $valor,
				"DS_MERCHANT_CURRENCY"			=> $mon,
				"DS_MERCHANT_ORDER"				=> $d['iddentinf'],
				"DS_SECURE_PAYMENT"				=> $secur
			);

			// var_dump($datos);

			$ch = curl_init($url);
		    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		    curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
		   
			$salida = curl_exec($ch);
			// $salida = '{"status":"A","errorCode":"","authorizationCode":"619407"}';
			if (curl_errno($ch)) $correoMi .=  "Error en la resp de Bipay:".curl_strerror(curl_errno($ch))."<br>\n";
			$crlerror = curl_error($ch);
			if ($crlerror) {
				$correoMi .=  "Error en la resp de Bipay:".$crlerror."<br>\n";
				// muestraError ("Falla en datos a Bipay, contacte con su comercio", $correoMi);
			}
			$curl_info = curl_getinfo($ch);
			curl_close($ch);
			$arrCurl = json_decode($salida);

			// echo "curlifno=".$curl_info."<br>";

			// echo $correoMi."<br>";

			// var_dump ($arrCurl); 

			// echo "<br><br>status".$arrCurl->status."<br><br>";

			if ($arrCurl->status == 'A') {
				$autorizo = $arrCurl->authorizationCode;
				$todok = 1;
			} else {
				echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">
	                Error en Devoluci&oacute;n, c&oacute;digo ".$arrCurl->errorCode."</div>";
			}


		} elseif ($pasa == 91) {// Pasarela Titanes Fincimex
		    
			$q = "select d.observacion, o.titOrdenId, recibe from tbl_devoluciones d, tbl_aisOrden o 
					where d.idtransaccion = o.idtransaccion and d.idtransaccion = '{$d['iddentinf']}'";
			$correoMi .= "$q<br>";
			$temp->query($q);
			$observ = substr($temp->f('observacion'), 0, strpos($temp->f('observacion'),'#'));
			$valor = number_format($temp->f('recibe')/100,2,'.','');
			$orden = $temp->f('titOrdenId');

			$data = array(
				'AmountToReceive' 	=> $valor,
				'Reason'			=> $observ,
				'id'				=> $orden,
				"Signature"			=> $orden.$valor.$observ
			);
			
			$datATitanes = datATitanes ($data, 'D', 91);
			if (stripos($datATitanes, "Revoked to be returned") > -1) {
				$todok = 1;
			} 
			$corCreo->todo(13, "Resultado Devol Titanes", $datATitanes);
			
		} elseif ($centauto == 13) {//El resto de las integraciones con Tefpay
		    $q = "select distinct p.clave, p.comercio, t.identificadorBnco, t.tarjetas, case a.estado when 'D' then 'https://intesecure02.tefpay.com/paywebv1.4.21/INPUT.php' else 'https://secure02.tefpay.com/paywebv1.4.21/INPUT.php' end url, p.terminal, t.moneda from tbl_transacciones t, tbl_colPasarMon p, tbl_devoluciones d, tbl_pasarela a, tbl_cenAuto c where c.id = a.idcenauto and t.idtransaccion = t.idtransaccion and t.moneda = p.idmoneda and t.pasarela = p.idpasarela and t.pasarela = a.idPasarela and t.idtransaccion = ".$d['iddentinf'];
			
			$correoMi .= "$q<br>";
		    $temp->query($q);
			
//echo $correoMi;
			
			$clave						= $temp->f('clave');
			$Ds_Merchant_MerchantCode	= $temp->f('comercio');
			$Ds_Date					= $temp->f('identificadorBnco');
			$Ds_Merchant_PanMask		= substr($temp->f('tarjetas'), -4);
			$url						= $temp->f('url');
			$Ds_Merchant_Amount			= $d['valordev']*100;
			$Ds_Merchant_Url			= 'https://www.administracomercios.com/dev.php';
			$Ds_Merchant_MatchingData	= $d['iddentinf'].'000000000';
			$Ds_Merchant_Terminal		= $temp->f('terminal');
			$Ds_Merchant_Currency		= $temp->f('moneda');
		    
//		    $message = sha1("4". $Ds_Merchant_Amount . $Ds_Merchant_MerchantCode . $Ds_Merchant_MatchingData . $Ds_Merchant_Url . $clave);
		    $message = sha1("4". $Ds_Merchant_Amount . $Ds_Merchant_MerchantCode . $Ds_Merchant_MatchingData . $clave);
		    
		    $datos = array(
				'Ds_Merchant_TransactionType'		=> '4',
				'Ds_Merchant_MatchingData'			=> $Ds_Merchant_MatchingData,
				'Ds_Merchant_MerchantCode'			=> $Ds_Merchant_MerchantCode,
				'Ds_Date'							=> $Ds_Date,
				'Ds_Merchant_PanMask'				=> $Ds_Merchant_PanMask,
				'Ds_Merchant_MerchantSignature'		=> $message,
				'Ds_Merchant_Amount'				=> $Ds_Merchant_Amount,
//				'Ds_Merchant_Url'					=> $Ds_Merchant_Url,
//				'Ds_Merchant_Terminal'				=> $Ds_Merchant_Terminal,
				'Ds_Merchant_Currency'				=> $Ds_Merchant_Currency
		    );
		    
		    foreach ($datos as $key => $value) {
		        $correoMi .= "$key = $value<br>";
		    }
			$correoMi .= "url = ".$url."<br>";
		    
		    $headers = array( 'Connection: Keep-Alive',
		        'Content-Type: application/x-www-form-urlencoded');
		    $ch = curl_init($url);
		    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		    curl_setopt($ch, CURLOPT_TIMEOUT, 40); // timeout en 40 segundos
		    curl_setopt($ch, CURLOPT_POST, true);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
		    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//echo $correoMi;
		    $xml = curl_exec($ch);
		    curl_close($ch);
		    $correoMi .= $xml;
//echo $correoMi;
		    $correoMi .= "<br>Aceptada=".stripos($xml, "aceptada")."ID=".stripos($xml, $d['iddentinf'])."<br>";
		    if (stripos($xml, "aceptada") && stripos($xml, $d['iddentinf'])) {
		        $todok = 1;
		        $correoMi .= "<br><br>SI SI SI SI<br><br>";
		    }
		    else $correoMi .= "<br><br>NO NO NO<br><br>";
		    
		    $corCreo->todo(13, "Resultado Devol Tefpay en Devolución", $correoMi);
		    
		    
		} elseif ($pasa == 39) {//Pasarela Sipay
		    
			$url		= "https://sandbox.sipayecommerce.sipay.es:10010/api/v0/auth";
			$cert		= "../../SipayCertif/E-Commerce.cliente.AMF_2.pem";
			$certkey	= "../../SipayCertif/E-Commerce.cliente.AMF.key";

			$q = "select TransactionId, merchantid from tbl_dataSipay where idtransaccion = '{$d['iddentinf']}'";
			$temp->query($q);
			$idSip = $temp->f('TransactionId');
			$merchantid = $temp->f('merchantid');
			
			$data = array(
					"username"		=> "",
					"password"		=> "",
					"apikey"		=> "",
					"module"		=> "iframe",
					"authtype"		=> "apikey",
					"lang"			=> "0",
					"merchantid"	=> "$merchantid",
					"ticket"		=> "{$d['iddentinf']}",
					"amount"		=> complLargo($d['valor']*100),
					"currency"		=> "$mon",
					"css_url"		=> "",
					"dstpageid"		=> ""
			);
			$options = array(
					CURLOPT_RETURNTRANSFER	=> true,
					CURLOPT_SSL_VERIFYPEER	=> false,
					CURLOPT_POST			=> true,
					CURLOPT_VERBOSE			=> true,
					CURLOPT_URL				=> $url,
					CURLOPT_SSLCERT			=> $cert,
					CURLOPT_POSTFIELDS		=> json_encode($data),
					CURLOPT_SSLKEY			=> $certkey
			);
			$sale .= "CURLOPT_RETURNTRANSFER	=> true,<br>
					CURLOPT_SSL_VERIFYPEER	=> false,<br>
					CURLOPT_POST			=> true,<br>
					CURLOPT_VERBOSE			=> true,<br>
					CURLOPT_URL				=> $url,<br>
					CURLOPT_SSLCERT			=> $cert,<br>
					CURLOPT_POSTFIELDS		=> ".json_encode($data).",<br>
					CURLOPT_SSLKEY			=> $certkey<br>";
			$ch = curl_init();
			curl_setopt_array($ch , $options);
			$salida = curl_exec($ch);
// 						echo "error=".curl_errno($ch);
			if (curl_errno($ch)) $correoMi .=  "Error en la resp de Sipay:".curl_strerror(curl_errno($ch))."<br>\n";
			$crlerror = curl_error($ch);
// 						echo "otroerror=".$crlerror;
			if ($crlerror) {
				$correoMi .=  "Error en la resp de Sipay:".$crlerror."<br>\n";
				muestraError ("Falla en datos a Sipay, contacte con su comercio", $correoMi);
			}
			$curl_info = curl_getinfo($ch);
			curl_close($ch);
			//			echo "<br><br>salida=".$salida."<br><br>";
			$arrCurl = json_decode($salida);
// 			echo "<br><br>";
// 			print_r($arrCurl);
// 			echo "<br><br>";
			
			$data = array(
					"module"			=> "payments",
					"authtype"			=> "apikey",
					"lang"				=> "0",
					"merchantid"		=> "$merchantid",
					"merchantname"		=>"{$arrCurl->merchantname}",
					"idrequest"			=> "{$arrCurl->idrequest}",
					"currency"			=> "$mon",
					"amount"			=> complLargo($d['valor']*100),
					"ticket"			=> "{$d['iddentinf']}",
					"originalidrequest"	=>"$idSip"
			);
// 			$url = "https://sandbox.sipayecommerce.sipay.es/api/v0/refundsbyid";
			$url = "https://sandbox.sipayecommerce.sipay.es:10443/api/v0/refundsbyid";
// 			$url = $arrCurl->iframe_src;
			$options = array(
					CURLOPT_RETURNTRANSFER	=> true,
					CURLOPT_SSL_VERIFYPEER	=> false,
					CURLOPT_POST			=> true,
					CURLOPT_VERBOSE			=> true,
					CURLOPT_URL				=> $url,
					CURLOPT_SSLCERT			=> $cert,
					CURLOPT_POSTFIELDS		=> json_encode($data),
					CURLOPT_SSLKEY			=> $certkey
			);
			$sale .= "<br><br>
					CURLOPT_RETURNTRANSFER	=> true,<br>
					CURLOPT_SSL_VERIFYPEER	=> false,<br>
					CURLOPT_POST			=> true,<br>
					CURLOPT_VERBOSE			=> true,<br>
					CURLOPT_URL				=> ".$url.",<br>
					CURLOPT_SSLCERT			=> $cert,<br>
					CURLOPT_POSTFIELDS		=> ".json_encode($data).",<br>
					CURLOPT_SSLKEY			=> $certkey<br>";
// 			echo $sale;
			$chc = curl_init();
			curl_setopt_array($chc , $options);
			$salid = curl_exec($chc);
// 						echo "error=".curl_errno($chc);
			if (curl_errno($chc)) $correoMi .=  "Error en la resp de Sipay:".curl_strerror(curl_errno($chc))."<br>\n";
			$crlerror = curl_error($chc);
// 						echo "otroerror=".$crlerror;
			if ($crlerror) {
				$correoMi .=  "Error en la resp de Sipay:".$crlerror."<br>\n";
				muestraError ("Falla en datos a Sipay, contacte con su comercio", $correoMi);
			}
			$curl_info = curl_getinfo($chc);
			curl_close($chc);
			
			$arrResp = json_decode($salid);
// 			echo "<br><br>";
// 			print_r($arrResp);
// 			echo "<br><br>";
			
			if ($arrResp->ResultCode == '0') {//si se aceptó la devolución
				
				//actualiza la tabla de Sipay
				$q = "update tbl_dataSipay set TransactionIdDev = '{$arrResp->TransactionId}', uuid  = '{$arrResp->TransactionId}', 
						SequenceNumberDev = '{$arrResp->TransactionId}' where idtransaccion = {$d['iddentinf']}";
				$temp->query($q);
				$todok = 1;
			}
			
		}
		
		if ($todok) {
			
			//actualiza la tabla de transacciones
			$q = "update tbl_transacciones set "
					. " valor = ($valorpon * 100), fecha_mod = " . time() . ", estado = '$esta', euroEquivDev = " . $d['valor'] 
					. " / $cambio, tasaDev = $cambio, " . " solDev = 0, idtransaccionMod = '".$d['iddentinfN']."', carDevCom = ".$d['cobrocom']." where idtransaccion = '" 
					. $d['iddentinf']."'";
			// echo $q."<br>";
			$temp->query($q);
			
			//actualiza la tabla de devoluciones
			$q = "update tbl_devoluciones set devpor = ".$_SESSION['id'].", fechaDev = ".time().", autorizo = '$autorizo' where idtransaccion = '" 
					. $d['iddentinf']."' and fechaDev = 0 ";
			// echo $q."<br>";
			$temp->query($q);
			
			//actualiza la tabla de reserva
			$q = "update tbl_reserva set valor = $valorpon, fechaCancel = " . time() . ", estado = '$esta' where id_transaccion = '" . $d['iddentinf']."'";
			// echo $q."<br>";
			$temp->query($q);

			$q = "select c.nombre, c.urlDevol, t.moneda, t.idcomercio, c.palabra from tbl_transacciones t, tbl_comercio c where t.idcomercio = c.idcomercio and t.idtransaccion = " . $d['iddentinf'];
			$correoMi .= "$q<br>";
			$temp->query($q);
			$comNom = $temp->f('nombre');
			$idcome = $temp->f('idcomercio');
			$trans = $d['iddentinf'];
			$valRem = ($valorpon * 100);
			$monopr = $temp->f('moneda');
			$palabra = $temp->f('palabra');
			$urld = $temp->f('urlDevol');

			//envío de los datos de la devolución al comercio que tenga la url a devolver
			if (strlen($urld) > 5) {
				//el comercio tiene permitido la respuesta a las devoluciones
				$tex = "$idcome . $trans . $valRem . $monopr . $palabra<br>";
				$firma = hash("sha256", $idcome . $trans . $valRem . $monopr . $palabra);
				$arrvalues = array(
					"transaccion" 	=> $trans,
					"valor" 		=> $valRem,
					"moneda"		=> $monopr,
					"comercio"		=> $idcome,
					"firma"			=> $firma
				);
				$tex .= json_encode($arrvalues)."<br>";

				$options = array(
					CURLOPT_RETURNTRANSFER	=> true,
					CURLOPT_SSL_VERIFYPEER	=> false,
					CURLOPT_POST			=> true,
					CURLOPT_VERBOSE			=> true,
					CURLOPT_URL				=> $urld,
					CURLOPT_POSTFIELDS		=> $arrvalues
				);
				$correoMi .= $text."<br><br>
							CURLOPT_RETURNTRANSFER	=> true,<br>
							CURLOPT_SSL_VERIFYPEER	=> false,<br>
							CURLOPT_POST			=> true,<br>
							CURLOPT_VERBOSE			=> true,<br>
							CURLOPT_URL				=> $urld,<br>
							CURLOPT_POSTFIELDS		=> " . json_encode($arrvalues) . ",<br>";

				$chc = curl_init();
				curl_setopt_array($chc,
					$options
				);
				$correoMi .= "Entrega: " . curl_exec($chc) . "<br>";

				if (curl_errno($chc)) $correoMi .=  "Error en la entrega de la respuesta:" . curl_strerror(curl_errno($chc)) . "<br>\n";
				$crlerror = curl_error($chc);

				if ($crlerror) {
					$correoMi .=  "Error en la entrega de la respuesta:" . $crlerror . "<br>\n";
					sendTelegram('Error en la entrega de la respuesta de devolucion '.date('d/m/Y')."\n$$correoMi",null);
					// muestraError("Error en la entrega de la respuesta de devolucion ", $correoMi);
				}

				$curl_info = curl_getinfo($chc);
				curl_close($chc);
			}
			$corCreo->todo(7, "Devolucion estado", $correoMi);
?>
<style>
<!--
#resl{float: left;width:100%;text-align:center;margin-top:30px;}
#resl div{width:190px;margin:0 auto;}
#tit{margin-bottom:20px;}
.ttlo{display:block;margin:10px 0;color:#894D00;font-size:1.4em;font-weight:bold;}
#dats{margin-bottom:20px;}
td.nomDe{font-weight:bold;text-align:left;padding-left:5px;width:140px;padding:4px 0;}
table td{text-align:right;}
.cent{text-align:center !important;font-size:1.2em;}
.ffr{}
-->
</style>
<div id="resl">
	<div id="tit">
		<span class="ttlo">Resultado de la devolución</span>
		<table id="dats">
			<tr>
				<td class="nomDe">Importe:</td>
				<td><?php echo formatea_numero($d['valor']). " ".$sig; ?></td>
			</tr>
			<tr>
				<td class="nomDe">Comercio:</td>
				<td><?php echo $comNom; ?></td>
			</tr>
			<tr>
				<td class="nomDe">Código Comercio:</td>
				<td><?php echo $merchantid; ?></td>
			</tr>
			<tr>
				<td class="nomDe">Terminal:</td>
				<td><?php echo ''; ?></td>
			</tr>
			<tr>
				<td class="nomDe">Número de pedido:</td>
				<td><?php echo $d['iddentinf']; ?></td>
			</tr>
			<tr>
				<td class="nomDe">Fecha:</td>
				<td><?php echo date('d-m-Y',time()); ?></td>
			</tr>
			<tr>
				<td class="nomDe">Hora:</td>
				<td><?php echo date('H:i:s',time()); ?></td>
			</tr>
			<tr>
				<td class="nomDe cent" colspan="2">Devolución Aceptada</td>
			</tr>
		</table>
	</div>
<div class="ffr">
<form action="<?php echo $estaurl; ?>">
<input type="submit" value="Aceptar" class="botacept" />
</form>
</div>
</div>
<?php 
	$ok = true;
// 		$inicio = new inico();
// 		$inicio->ip = $_SERVER['REMOTE_ADDR'];
// 		$inicio->comer = $d['nomdbre'];
// 		$inicio->idTrn = $d['iddentinf'];
// 		$inicio->imp = $d['valor'];
// 		$inicio->mon = $mon;
// 		$inicio->opr = 'D';
// 		$inicio->pasa = $pasa;
// 		$inicio->idi == 'es';
// 		$inicio->verComer();
// 		$inicio->cheqPas();
// 		echo $inicio->log;
// 		$sale = $inicio->operacion();
// 		echo $sale."<br>";
// 		$data = array('entrada'=>$sale);
// // 		$ch = curl_init($inicio->datPas['url']);
// 		$ch = curl_init('http://localhost/concentrador/nuevo.php');
// 		curl_setopt($ch, CURLOPT_HEADER, false);
// 		curl_setopt($ch, CURLOPT_POST, true);
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// 		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.54");
// 		$output = curl_exec($ch);
// 		$curl_info = curl_getinfo($ch);
// 		curl_close($ch);
		
// 		foreach ($curl_info as $key => $value) {
// 			echo  $key." = ".$value."<br>\n";
// 		}
// 		echo "respuCurl=$output||<br>\n";
		} else 
				echo "<div style=\"text-align:center; margin-top: 20px; color:red; font-family:Arial sans-serif; font-size:11px\">
	                La devolución no se ha podido procesar. </div>";
	} else {

		$q = "update tbl_transacciones set valor = ($valorpon * 100), fecha_mod = " . time() . ", estado = '$esta', euroEquivDev = " . $d['valor'] . " / $cambio, tasaDev = $cambio,  solDev = 0, idtransaccionMod = '".$d['iddentinfN']."', carDevCom = ".$d['cobrocom']." where idtransaccion = '" . $d['iddentinf']."'";
		$correoMi .= "$q<br>";
	// echo $q."<br>";
		$temp->query($q);

		$q = "update tbl_reserva set valor = $valorpon, fechaCancel = " . time() . ", estado = '$esta' where id_transaccion = '" . $d['iddentinf']."'";
	// echo $q."<br>";
		$temp->query($q);
				
		//actualiza la tabla de devoluciones
		$q = "update tbl_devoluciones set devpor = ".$_SESSION['id'].", fechaDev = ".time()." where idtransaccion = '" . $d['iddentinf']."' and fechaDev = 0 ";
		$correoMi .= "$q<br>";
	// echo $q."<br>";
		$temp->query($q);

		$q = "select c.nombre, c.urlDevol, t.moneda, t.idcomercio, c.palabra from tbl_transacciones t, tbl_comercio c where t.idcomercio = c.idcomercio and t.idtransaccion = " . $d['iddentinf'];
		$correoMi .= "$q<br>";
		$temp->query($q);
		$comNom = $temp->f('nombre');
		$idcome = $temp->f('idcomercio');
		$trans = $d['iddentinf'];
		$valRem = ($valorpon * 100);
		$monopr = $temp->f('moneda');
		$palabra = $temp->f('palabra');
		$urld = $temp->f('urlDevol');

		//envío de los datos de la devolución al comercio que tenga la url a devolver
		if (strlen($urld) > 5) {
			//el comercio tiene permitido la respuesta a las devoluciones
			$firma = hash("sha256", $idcome . $trans . $valRem . $monopr . $palabra);
			$arrvalues = array(
				"transaccion" 	=> $trans,
				"valor" 		=> $valRem,
				"moneda"		=> $monopr,
				"comercio"		=> $idcome,
				"firma"			=> $firma
			);

			$options = array(
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_SSL_VERIFYPEER	=> false,
				CURLOPT_POST			=> true,
				CURLOPT_VERBOSE			=> true,
				CURLOPT_URL				=> $urld,
				CURLOPT_POSTFIELDS		=> $arrvalues
			);
			$correoMi .= "<br><br>
					CURLOPT_RETURNTRANSFER	=> true,<br>
					CURLOPT_SSL_VERIFYPEER	=> false,<br>
					CURLOPT_POST			=> true,<br>
					CURLOPT_VERBOSE			=> true,<br>
					CURLOPT_URL				=> $urld,<br>
					CURLOPT_POSTFIELDS		=> " . json_encode($arrvalues) . ",<br>";

			$chc = curl_init();
			curl_setopt_array($chc, $options);
			$correoMi .= "Entrega: " . curl_exec($chc) . "<br>";
			$responseInfo = curl_getinfo($ch);
			if ($responseInfo["http_code"] != 200 && $responseInfo["http_code"] != 201) {

				if (curl_errno($chc)) $correoMi .=  "Error en la entrega de la respuesta:" . curl_strerror(curl_errno($chc)) . "<br>\n";
				$crlerror = curl_error($chc);

				if ($crlerror) {
					$correoMi .=  "Error en la entrega de la respuesta:" . $crlerror . "<br>\n";
					muestraError("Error en la entrega de la respuesta, contacte con su comercio", $correoMi);
				}
			}

			$curl_info = curl_getinfo($chc);
			curl_close($chc);
		}
		$corCreo->todo(7, "Devolucion estado", $correoMi);
		
	//echo $query;
		$temp->query($query);
	
		$query = "select c.nombre, t.fecha, c.idcomercio
	                from tbl_comercio c, tbl_transacciones t
	                where  idtransaccion = '" . $d['iddentinf'] . "'
	                and t.idcomercio = c.idcomercio ";
		$temp->query($query);
		$fecha = $temp->f('fecha');
		$comnombre = $temp->f('nombre');
		$idcom = $temp->f('idcomercio');
	
		$subject = 'Descuento / Devolución';
	
		$message = "transacción: " . $d['iddentinf'] . " \r\n
	                comercio: $comnombre \r\n
	                valor descontado: " . $d['valor'] . " \r\n
					moneda: $sig\r\n
	                fecha: " . date('d/m/Y h:m a', $fecha);
	
		if ($correoMas == 1) {
			$q = "select nombre, email from tbl_admin where idcomercio = '{$idcom}' and correoT = 1 and activo = 'S'";
			$temp->query($q);
			$arrayTo = $temp->loadRowList();
		}
		
	    $des = true;
	    foreach ($arrayTo as $todale) {
	        if ($des) {
	            $corCreo->to($todale[1]);
	            $des = false;
	        } else $corCreo->add_headers ("Cc: ".$todale[1]);
	    }
// 	    $corCreo->todo(30,$subject,$message);
	    $ok = true;
	}
	
	if ($ok) {
		
		$q = "select distinct c.nombre comercio, t.identificador, t.codigo, from_unixtime(t.fecha_mod, '%d/%m/%Y/ %H:%i') fec, t.valor_inicial, t.valor, 
					m.moneda, p.comercio idc, a.comercio comnomb, p.terminal, a.idempresa, (select email from tbl_devoluciones d, tbl_admin n where 
							d.idadmin = n.idadmin and t.idtransaccion = d.idtransaccion order by d.fecha desc limit 0,1) correo
				from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_colPasarMon p, tbl_pasarela a
				where ".
// 				p.idmoneda = t.moneda and 
							" p.idpasarela = t.pasarela and 
							t.moneda = m.idmoneda and 
							a.idPasarela = t.pasarela and
							t.idcomercio = c.idcomercio and 
							t.idtransaccion = '".$d['iddentinf']."'";
		$temp->query($q);
		
		$subject = "Administrador de Comercios - Devolución a Comercio - ".utf8_decode($temp->f('comercio'))." Op. ".$d['iddentinf'];
		if ($temp->f('idempresa') == 3) $corCreo->from = "tpv@publinetservicios.com";
		$corCreo->to = $temp->f('correo');
		
		$message = "Estimado cliente,<br><br>
						Hemos procedido a realizar la devolución solicitada por su comercio con fecha ".date('d/m/Y')." y 
						que corresponde a la siguiente operación:<br><br>
		
		Datos de la Transacción:<br>
		ID Transacción: ".$d['iddentinf']."<br>
		ID Comercio: ".$temp->f('identificador')."<br>
		Cód. Autorización del Banco: ".$temp->f('codigo')."<br>
		Fecha de Operación: ".$temp->f('fec')."<br>
		Valor inicial: ".formatea_numero($temp->f('valor_inicial')/100)." ".$temp->f('moneda')."<br>
		Valor devuelto: ".formatea_numero($d['valor'])." ".$temp->f('moneda')."<br>
		Valor final: ".formatea_numero($temp->f('valor')/100)." ".$temp->f('moneda')."<br><br>
		
		La devolución se ha realizado satisfactoriamente. Puede chequear el estado de la misma en nuestra plataforma 
		<a href=\"https://www.administracomercios.com/admin\">https://www.administracomercios.com/admin</a>.<br><br>
		
		A continuación el comprobante correspondiente a la misma:<br><br>
		
		<style>
<!--
body{font-type:sans-serif;}
#resl{float:left;width:100%;text-align:center;margin-top:30px;}
#resl div{width:190px;margin:0 auto;}
#tit{margin-bottom:20px;}
.ttlo{display:block;margin:10px 0;color:#894D00;font-size:1.4em;font-weight:bold;}
#dats{margin-bottom:20px;}
td.nomDe{font-weight:bold;text-align:left;padding-left:5px;width:140px;padding:4px 0;}
table td{text-align:right;}
.cent{text-align:center !important;font-size:1.2em;}
.ffr{}
-->
</style>
<div id=\"resl\">
	<div id=\"tit\">
		<span style='display:block;margin:10px 0;color:#894D00;font-size:1.4em;font-weight:bold;'>Resultado de la devolución</span>
		<table id=\"dats\">
			<tr>
				<td style='font-weight:bold;text-align:left;padding-left:5px;width:140px;padding:4px 0;'>Importe:</td>
				<td>". formatea_numero($d['valor'])." ".$sig."</td>
			</tr>
			<tr>
				<td style='font-weight:bold;text-align:left;padding-left:5px;width:140px;padding:4px 0;'>Comercio:</td>
				<td>".$temp->f('comnomb')."</td>
			</tr>
			<tr>
				<td style='font-weight:bold;text-align:left;padding-left:5px;width:140px;padding:4px 0;'>Código Comercio:</td>
				<td>".$temp->f('idc')."</td>
			</tr>
			<tr>
				<td style='font-weight:bold;text-align:left;padding-left:5px;width:140px;padding:4px 0;'>Terminal:</td>
				<td>".$temp->f('terminal')."</td>
			</tr>
			<tr>
				<td style='font-weight:bold;text-align:left;padding-left:5px;width:140px;padding:4px 0;'>Número de pedido:</td>
				<td>".$d['iddentinf']."</td>
			</tr>
			<tr>
				<td style='font-weight:bold;text-align:left;padding-left:5px;width:140px;padding:4px 0;'>Fecha:</td>
				<td>".date('d-m-Y',time())."</td>
			</tr>
			<tr>
				<td style='font-weight:bold;text-align:left;padding-left:5px;width:140px;padding:4px 0;'>Hora:</td>
				<td>".date('H:i:s',time())."</td>
			</tr>
			<tr>
				<td style='font-weight:bold;text-align:left;padding-left:5px;width:140px;padding:4px 0;text-align:center !important;font-size:1.2em;' colspan=\"2\">Devolución Aceptada</td>
			</tr>
		</table>
	</div>
</div><br><br>
		
		Atentamente,<br><br>
		
		Administrador de Comercios";
		
		$corCreo->todo(46,$subject,$message);
	}
}
?>

