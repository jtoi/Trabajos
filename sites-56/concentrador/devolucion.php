<?php

/* 
 * Este fichero es como preparación para hacer las devoluciones
 * desde el concentrador
 */

define( '_VALID_ENTRADA', 1 );
session_start();
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'admin/classes/entrada.php' );

$temp = new ps_DB();
$correoMi = "";
$lleg = '';

$idtransac = $_REQUEST['transaccion'];//031130233865
$valor = $_REQUEST['valor'];
foreach ($_REQUEST as $key => $value) {
	$lleg .= $key." = ".$value."<br>\n";
}
$correoMi .= $lleg;

if ($idtransac) {
    
    $q = "select moneda, pasarela, idtransaccionMod, estado, tipoEntorno from tbl_transacciones where idtransaccion = '$idtransac'";
    $correoMi .= "<br>".$q;
    $temp->query($q);
    $arrLin = $temp->loadRowList();
    $moneda = $arrLin[0][0];
    $pasar = $arrLin[0][1];
    $estado = $arrLin[0][3];
    $trMod = $arrLin[0][2];
    $entorno = $arrLin[0][4];
    if ($trMod == '') $trMod = $idtransac;
    
    $correoMi .= "<br>".$moneda;
    $correoMi .= "<br>".$pasar;
    $correoMi .= "<br>".$estado;
    $correoMi .= "<br>".$trMod;
    $correoMi .= "<br>".$entorno;
    $correoMi .= "<br>".$idtransac;
    
    if ($pasar == 1) { //Pasarela del BBVA
        if ($entorno == 'P') { //comercio en producción
            $clave = desofuscar(_PALABR_OFUS, _CONTRASENA_OFUS);
            $urlcomercio = _URL_COMERCIO;
            $localizador = _LOCALIZADOR;
            $url_tpvv = _URL_TPV; // URL del TPV.
            $idcomercio = _ID_COMERCIO;
            $terminal = _ID_PTO;
            $pasoaBBVA = true;
        } else {//comercio en desarrollo
            $clave = desofuscar(_TESTPALABR_OFUS_TEST, _TESTCONTRASENA_OFUS_TEST);
            $urlcomercio = _URL_COMERCIO;
            $localizador = _LOCALIZADOR;
            $url_tpvv = _URL_TPV; // URL del TPV.
            $idcomercio = _TESTID_COMERCIO_TEST;
            $terminal = _TESTID_PTO_TEST;
            $pasoaBBVA = true;
        }
    } elseif ($pasar == 3) {//pasarela del BBVA 3D
        $clave = desofuscar(_3DPALABR_OFUS, _3DCONTRASENA_OFUS);
        $urlcomercio = _URL_COMERCIO;
        $localizador = _LOCALIZADOR;
        $url_tpvv = _URL_TPV; // URL del TPV.
        $idcomercio = _3DID_COMERCIO;
        $terminal = _3DID_PTO;
        $pasoaBBVA = true;
    } elseif ($pasar == 11) {//pasarela del BBVA 3D onL
        $clave = desofuscar(_3DOPALABR_OFUS, _3DOCONTRASENA_OFUS, _3DOID_COMERCIO);
        $urlcomercio = _URL_COMERCIO;
        $localizador = _LOCALIZADOR;
        $url_tpvv = _URL_TPV; // URL del TPV.
        $idcomercio = _3DOID_COMERCIO;
        $terminal = _3DOID_PTO;
        $pasoaBBVA = true;
    } elseif ($pasar == 8) {//pasarela del BBVAAMEX
        $clave = desofuscar(_MEXPALABR_OFUS, _MEXCONTRASENA_OFUS);
        $urlcomercio = _URL_COMERCIO;
        $localizador = _LOCALIZADOR;
        $url_tpvv = _URL_TPV; // URL del TPV.
        $idcomercio = _MEXID_COMERCIO;
        $terminal = _MEXID_PTO;
        $pasoaBBVA = true;
    } elseif ($pasar == 14) {//pasarela del BBVA3
        $clave = desofuscar(_3BBVAPALABR_OFUS, _3BBVACONTRASENA_OFUS);
        $urlcomercio = _URL_COMERCIO;
        $localizador = _LOCALIZADOR;
        $url_tpvv = _URL_TPV; // URL del TPV.
        $idcomercio = 'B9550206800006';
        $terminal = '999999';
        $pasoaBBVA = true;
    } elseif ($pasar == 15) {//pasarela del BBVA4
        $clave = desofuscar(_4BBVAPALABR_OFUS, _4BBVACONTRASENA_OFUS);
        $urlcomercio = _URL_COMERCIO;
        $localizador = _LOCALIZADOR;
        $url_tpvv = _URL_TPV; // URL del TPV.
        $idcomercio = _4BBVAID_COMERCIO;
        $terminal = _4BBVAID_PTO;
        $pasoaBBVA = true;
    } elseif ($pasar == 16) {//pasarela del BBVA4 3D
        $clave = desofuscar(_5BBVAPALABR_OFUS, _5BBVACONTRASENA_OFUS);
        $urlcomercio = _URL_COMERCIO;
        $localizador = _LOCALIZADOR;
        $url_tpvv = _URL_TPV; // URL del TPV.
        $idcomercio = _5BBVAID_COMERCIO;
        $terminal = _5BBVAID_PTO;
        $pasoaBBVA = true;
    } elseif ($pasar == 17) {//pasarela del BBVA9 3D
        $clave = desofuscar(_9BBVAPALABR_OFUS, _9BBVACONTRASENA_OFUS);
        $urlcomercio = _URL_COMERCIO;
        $localizador = _LOCALIZADOR;
        $url_tpvv = _URL_TPV; // URL del TPV.
        $idcomercio = _9BBVAID_COMERCIO;
        $terminal = _9BBVAID_PTO;
        $pasoaBBVA = true;
    } elseif ($pasar == 18) {//pasarela del BBVA10 3D
        $clave = desofuscar(_10BBVAPALABR_OFUS, _10BBVACONTRASENA_OFUS);
        $urlcomercio = _URL_COMERCIO;
        $localizador = _LOCALIZADOR;
        $url_tpvv = _URL_TPV; // URL del TPV.
        $idcomercio = _10BBVAID_COMERCIO;
        $terminal = _10BBVAID_PTO;
        $pasoaBBVA = true;
    }
    
    if ($pasoaBBVA) {
        $url_tpvv = "https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_rpcrouter";
		$firmaSal = strtoupper(SHA1($terminal . $idcomercio . $trMod . $idtransac . $moneda . $importe . $clave));
    }
    
    $lt = "&lt;";
		$gt = "&gt;";
		$xml.=$lt . "tpv" . $gt;
		$xml.=$lt . "opdevolucion" . $gt;
		$xml.=$lt . "idterminal" . $gt . $terminal . $lt . "/idterminal" . $gt;
		$xml.=$lt . "idcomercio" . $gt . $idcomercio . $lt . "/idcomercio" . $gt;
		$xml.=$lt . "idtransaccion" . $gt . $trMod . $lt . "/idtransaccion" . $gt;
		$xml.=$lt . "idtransaccionorig" . $gt . $idtransac . $lt . "/idtransaccionorig" . $gt;
		$xml.=$lt . "moneda" . $gt . $moneda . $lt . "/moneda" . $gt;
		$xml.=$lt . "importe" . $gt . $valor . $lt . "/importe" . $gt;
		$xml.=$lt . "firma" . $gt . $firmaSal . $lt . "/firma" . $gt;
		$xml.=$lt . "/opdevolucion" . $gt;
		$xml.=$lt . "/tpv" . $gt;
		$peticion = $xml;
		if (_MOS_CONFIG_DEBUG) echo "peticion=$peticion<br>";
//		$cadenSal = '
//		 <form name="envia" action="' . $url_tpvv . '" method="post">
//		 <input type="hidden" name="peticion" value="' . $peticion . '"/> ';
//        $cadenSal .= '</form>
//						 <script language=\'javascript\'>
//							 document.envia.submit();
//						 </script>';
		
//		$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";

        $headers = array(
                        "Content-type: text/xml",
                        "Content-length: " . strlen($peticion),
                        "Connection: close",
                    );
        
        $ch = curl_init($url_tpvv);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("peticion"=>$peticion));
        $correoMi .=  "Ejecutó la llamada por curl - ".curl_exec($ch)."<br>\n";
        curl_close($ch);

		//$correoMi .= "cadenSal= " . $cadenSal . "<br>\n";
		echo $correoMi;
    
//		echo $cadenSal;
    
}
?>
