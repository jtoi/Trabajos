<?php

/**
* Copyright (c) 2020 Magic Data Programs SL. Todos los Derechos Reservados.
*
*/

require_once "DatosLlamadaTpvCli.php";
require_once "random_compat/lib/random.php";
require_once "hmac.php";


function initEcpLog() 
{
    $logFile = dirname(__FILE__) . "/../log/eurocoinpay.log";
    if (file_exists($logFile))
        return;

    //deleteEcpLog();
    $fh = fopen($logFile, 'w');
    fwrite($fh, "--START OF LOG--\n");
    fclose($fh);    
}

function deleteEcpLog() 
{
    $logFile = dirname(__FILE__) . "/../log/eurocoinpay.log";
    if (file_exists($logFile))
        unlink($logFile);
}

function logEcp($txt) {

    //dbg
    //date_default_timezone_set("UTC");

    $msg = date("Y-m-d\TH:i:s") .  " " . $txt . "\n";
        
    // dbg
    //echo $msg;

    $logFile = dirname(__FILE__) . "/../log/eurocoinpay.log";
    if (!file_exists($logFile))
        return;

    $fh = fopen($logFile, 'a');
    fwrite($fh, $msg);
    fclose($fh);    
}

const confirmKeyBytes = 6 / 2;
const keyBytes = 256 / 8;
const ivBytes = 128 / 8;    

class EurocoinPayApi {


    function generaIv()
    {
        try
        {
            $tokenData = random_bytes(ivBytes);
            return base64_encode($tokenData);
        }
        catch (Exception $ex)
        {
            logEcp('Excepcion al generar la IV para encriptar: ' . $ex->getMessage());        
            return null;
        }
    
    }
    
    function  cliEncriptaDatosLlamada($strKeyB64, $strIvB64, $strDatos)
    {
        return $this->encryptAesUtf8StringB64($strKeyB64, $strIvB64, $strDatos);
    }

    
    function decryptAesUtf8String($strKeyB64, $strIvB64, $strDataB64)
    {
        $by = $this->decryptAes($strKeyB64, $strIvB64, $strDataB64);
        //$str = System.Text.Encoding.UTF8.GetString($by);
        $str = $by;
        return $str;
    }

    function decryptAes($strKeyB64, $strIvB64, $strDataB64)
    {
        $strKey = base64_decode($strKeyB64);
        $nonce = base64_decode($strIvB64);
        $ciphertext = base64_decode($strDataB64);
        $clr = openssl_decrypt($ciphertext, 'AES-256-CBC', $strKey, OPENSSL_RAW_DATA, $nonce);
        return $clr;
    }

    function encryptAesUtf8StringB64($strKeyB64, $strIvB64, $str)
    {
        $byData = $this->encryptAes($strKeyB64, $strIvB64, $str);
        return base64_encode($byData);
    }
    
    function encryptAes($strKeyB64,$strIvB64, $str)
    {
        $byData = $str;
        return $this->encryptAesBin($strKeyB64, $strIvB64, $byData);
    }
    
    function encryptAesBin($strKeyB64, $strIvB64, $byData)
    {
        logEcp('encryptAesBin');

        $strKey = base64_decode($strKeyB64);
        $nonce = base64_decode($strIvB64);

        //logEcp('strKeyB64:'.$strKeyB64 . ' => len:' .strlen($strKey));
        //logEcp('strIvB64'.$strIvB64 . ' => len:' .strlen($nonce));

        if (strlen($strKey) != keyBytes) {
            logEcp('strKeyB64 invalid length: ' .strlen($strKey));
            throw new InvalidArgumentException('Invalid Key Length:' . strlen($strKey));
        }
                 
        $res = openssl_encrypt($byData, 'AES-256-CBC', $strKey, OPENSSL_RAW_DATA, $nonce);
        //logEcp('res: => len:' .strlen($res));
        return $res;
    }
    
    function calculateSigB64($strKeyB64, $strData)
    {
        $bySig = $this->calculateSig($strKeyB64, $strData);
        $res = base64_encode($bySig);
        return $res;
    }

    function calculateSig($strKeyB64, $strData)
    {
        $byData = $strData;
        $res = $this->calculateSig_sb($strKeyB64, $byData);
        return $res;
    }

    function calculateSig_sb($strKeyB64, $data)
    {
        $key = base64_decode($strKeyB64);
        $res = $this->calculateSig_bb($key, $data);
        return $res;
    }

    function calculateSig_bb($key, $data)
    {
        $hash = hash_hmac('sha256', $data, $key, true); 
        return $hash;
    }

    function checkSecretKeyB64($clavesecretaB64)
    {
        try
        {
            $iv = $this->generaIv();
            $jsEnc = $this->cliEncriptaDatosLlamada($clavesecretaB64, $iv, "test-string");
            if (isset($jsEnc) && $jsEnc > "")
                return true;
        }
        catch (Exception $ex)
        {
        }
        catch (Error $er)
        {
        }
        return false;
    }

    function generateSendData($dl, $clavesecretaB64)
    {
        logEcp("generateSendData");
        $js = json_encode($dl);
        $iv = $this->generaIv();
        $jsEnc = $this->cliEncriptaDatosLlamada($clavesecretaB64, $iv, $js);
        $__strData = $jsEnc;
        $sig = $this->calculateSigB64($clavesecretaB64, $js);
        $__strSig = '' . $dl->merchant_user_nr . "|" . $dl->terminal_nr . "|" . $iv . "|" . $sig;
        logEcp("strSig:" . $__strSig);
        logEcp("strData:" . $__strData);
        return ["data" => $__strData, "sig" => $__strSig ];
    }

    function cliObtenParametrosPost($data, $sigAll, $clave_encriptar)
    {
        logEcp("cliObtenParametrosPost");

        try
        {

            if (empty($data))
            {
                logEcp("data no encontrado");
                return null;
            }

            if (empty($sigAll))
            {
                logEcp("sig no encontrado");
                return null;
            }

            logEcp("sigAll:");

            $sigAll2 = explode('|',$sigAll);
            if (sizeof($sigAll2) != 2)
            {
                logEcp("sig no tiene 2 elementos");
                return;
            }
            logEcp("data|" . $data);
            logEcp("sigAll|" . $sigAll);

            $strIv = trim($sigAll2[0]);
            $strSig = trim($sigAll2[1]);

            // desencriptamos los datos
            $datosJS = $this->decryptAesUtf8String($clave_encriptar, $strIv, $data);
            logEcp("datosJS:" . $datosJS);

            // chequeamos la firma
            logEcp("txtFirmadoComprobar|" . $datosJS . "|");
            $sigCalculada = $this->calculateSigB64($clave_encriptar, $datosJS);
            if (!$sigCalculada == $strSig)
            {
                logEcp("Firma no coincide|" . $strSig . "|" . $sigCalculada . "|" . $sigAll);
                return;
            }
            logEcp("Firma OK|" . $strSig . "|" . $sigCalculada . "|" . $sigAll);

            $res = null;

            try
            {
                $res = json_decode($datosJS);
            }
            catch (Exception $ex)
            {
                logEcp("Ex: No pudimos deserializar 1:" . $ex);
                return;
            }

            try
            {
                if (empty($res) || $res->merchant_user_nr == 0)
                {
                    logEcp("No pudimos deserializar 2");
                    return;
                }
            }
            catch (Exception $ex)
            {
                logEcp("Ex: No pudimos deserializar 3:" . $ex);
                return;
            }

            return $res;
        }
        catch (Exception $ex)
        {
            logEcp("Excepcion no prevista:" . $ex);
        }
    }

  
    
}

