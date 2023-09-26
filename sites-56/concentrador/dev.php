<?php
ini_set('display_errors', 0);
//error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('_VALID_ENTRADA', 1);
if (!session_start()) session_start();
require_once( 'configuration.php' );
include 'include/mysqli.php';
include 'include/correo.php';
include_once 'admin/classes/entrada.php' ;

$temp = new ps_DB;
$corr = new correo;
$ent = new entrada;

$d = $_REQUEST;
$corrMi = '';

foreach ($d as $key => $value) {
    $corrMi .= "$key = $value<br>\n";
}
trigger_error($corrMi, E_USER_WARNING);

//echo ("<br><br>idd=".$d['idd']."<br><br>ent=".$ent->isAlfanumerico($d['idd'], 13)."<br><br>");

if ($id = $ent->isAlfanumerico($d['idd'], 13)) {
    
    $temp->query("select distinct p.clave, p.comercio, t.identificadorBnco, t.tarjetas, case a.estado when 'D' then 'https://intesecure02.tefpay.com/paywebv1.4.20/INPUT.php' else 'https://secure02.tefpay.com/paywebv1.4.20/INPUT.php' end url from tbl_transacciones t, tbl_colPasarMon p, tbl_devoluciones d, tbl_pasarela a, tbl_cenAuto c where c.id = a.idcenauto and t.idtransaccion = t.idtransaccion and t.moneda = p.idmoneda and t.pasarela = p.idpasarela and t.pasarela = a.idPasarela and t.idtransaccion = $id");
    
    $clave = $temp->f('clave');
    $Ds_Merchant_MerchantCode = $temp->f('comercio');
    $Ds_Date = $temp->f('identificadorBnco');
    $Ds_Merchant_PanMask = str_replace("************", '', $temp->f('tarjetas'));
    $url = $temp->f('url');
    $Ds_Merchant_Amount  = $d['valor']*100;
    $Ds_Merchant_Url = _ESTA_URL.'/dev.php';
    $Ds_Merchant_MatchingData = $d['idd'].'000000000';
    
    $message = sha1("4". $Ds_Merchant_Amount . $Ds_Merchant_MerchantCode . $Ds_Merchant_MatchingData . $Ds_Merchant_Url . $clave);
    
    $datos = array(
        'Ds_Merchant_TransactionType' => '4',
        'Ds_Merchant_MatchingData' => $Ds_Merchant_MatchingData,
        'Ds_Merchant_MerchantCode' => $Ds_Merchant_MerchantCode,
        'Ds_Date' => $Ds_Date,
        'Ds_Merchant_PanMask' => $Ds_Merchant_PanMask,
        'Ds_Merchant_MerchantSignature' => $message,
        'Ds_Merchant_Amount' => $Ds_Merchant_Amount,
        'Ds_Merchant_Url' => $Ds_Merchant_Url
    );
    
    foreach ($datos as $key => $value) {
        $corrMi .= "$key = $value<br>";
    }
    
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
    $xml = curl_exec($ch);
    curl_close($ch);
    $corrMi .= $xml;
    echo $xml;
    
    $corrMi .= "<br>Aceptada=".stripos($xml, "aceptada")."ID=".stripos($xml, $d['idd'])."<br>";
    if (stripos($xml, "aceptada") && stripos($xml, $d['idd'])) $corrMi .= "<br><br>SI SI SI SI<br><br>";
    else $corrMi .= "<br><br>NO NO NO<br><br>";
    
    $corr->todo(13, "Resultado Devol Tefpay", $corrMi);
    $corrMi = '';

} else echo "no es válido el id";


if (is_array($d)) {
    foreach ($d as $key => $value) {
        $corrMi .= "$key = $value <br>\n";
    }
} else $corrMi .= $d;

$corr->todo(13, "Resultado Devol Tefpay Error 5xx", $corrMi);

?>