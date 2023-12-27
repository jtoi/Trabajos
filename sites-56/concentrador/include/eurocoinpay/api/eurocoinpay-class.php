<?php

if ( !class_exists( 'EurocoinPayClass' ) ) { 

    require_once "api/EurocoinPayApi.php";
 
    class EurocoinPayClass {

        public $eurocoinpay_real_mode;
        public $eurocoinpay_customer_number;
        public $eurocoinpay_terminal_number;
        public $eurocoinpay_shop_name;
        public $eurocoinpay_encryption_key;
        public $eurocoinpay_log_enabled;
        public $eurocoinpay_url_ok;
        public $eurocoinpay_url_fail;
        public $eurocoinpay_url_notif;

        public function __construct() {
            $this->id ='eurocoinpay_gateway';
            $this->my_plugin_version ='1.0.10';

            //$this->urlSrvTest = 'http://localhost:64514/public/TpvCli/ConfirmPayment.aspx';;
            $this->urlSrvTest = 'https://eurocoin.websganadoras.net/public/TpvCli/ConfirmPayment.aspx';
            $this->urlSrvProd = 'https://backoffice.eurocoinpay.io/public/TpvCli/ConfirmPayment.aspx';

        }

        public function init() {
            if ($this->eurocoinpay_log_enabled)
            initEcpLog();
            else
            deleteEcpLog();

            $err = $this->checkConfigVars(); 
            return $err;       
        }

        private function isVarNonEmpty($s)
        {
            $s = trim($s);
            return ($s != '');
        }
    
        private function isVarPositiveInteger($s) 
        {
            if (!isset($s))
                return false;
    
            $s = trim($s);
    
            if (!is_numeric($s))
                return false;
    
            $i = strval($s);
            if ( $i !== strval(intval($s)) ) 
            {
                return false;
            }
    
            return ($i > 0);
        }
        
        function checkConfigVars() {
            logEcp('checkConfigVars');
			
            $errs = array();

            $errTxt = 'PHP with OpenSSL required for this module';
            if ( (!function_exists("openssl_encrypt")) || (!function_exists("openssl_decrypt")) || (!defined("OPENSSL_RAW_DATA")) )
                $errs[] =  $errTxt;
    
    
            $errTxt = 'Missing or incorrect EurocoinPay customer number';
            if (!$this->isVarPositiveInteger($this->eurocoinpay_customer_number)) 
                $errs[] =  $errTxt;

            $errTxt = 'Missing or incorrect EurocoinPay terminal number';
            if (!$this->isVarPositiveInteger($this->eurocoinpay_terminal_number))
                $errs[] =  $errTxt;
                
            $errTxt = 'Missing Shop Name';
            if (!$this->isVarNonEmpty($this->eurocoinpay_shop_name))
                $errs[] =  $errTxt;
    
            $errTxt = 'Missing Encryption Key';
            if (!$this->isVarNonEmpty($this->eurocoinpay_encryption_key))
                $errs[] =  $errTxt;
            else
            {
                $ecp = new EurocoinPayApi();
                $ok = $ecp->checkSecretKeyB64(trim($this->eurocoinpay_encryption_key));
                $errTxt = 'Incorrect Encryption Key';
                if (!$ok)
                    $errs[] = $errTxt;
            }
            return $errs;
        }


        public function prepareDataForEcpServer($order_id,$total,$currency) 
        {
            logEcp('prepareDataForEcpServer');
            

            $total = round($total,2);
           
            $dl = new DatosLlamadaTpvCli();
            $dl->order_number = $order_id;
            $dl->merchant_name = $this->eurocoinpay_shop_name;
            $dl->terminal_nr = $this->eurocoinpay_terminal_number;
            $dl->merchant_user_nr = $this->eurocoinpay_customer_number;
            $dl->amount = $total;
            $dl->product = 'Order number' . ' ' . $dl->order_number;
            $dl->transaction_type = "P";
            $dl->url_ok = $this->eurocoinpay_url_ok;
            $dl->url_fail = $this->eurocoinpay_url_fail;
            $dl->url_notif = $this->eurocoinpay_url_notif;
            $dl->plugin = "PhpPlug v" .  $this->my_plugin_version  . " PHP v" . phpversion() ;
			$dl->order_currency = strtoupper($currency);
    
            $clavesecretaB64 = $this->eurocoinpay_encryption_key;
            
            $ecp = new EurocoinPayApi();
    
            if ($this->eurocoinpay_real_mode) 
                $srvUrl = $this->urlSrvProd;
            else
                $srvUrl = $this->urlSrvTest;
    
            $sndData = $ecp->generateSendData($dl, $clavesecretaB64);
            $sndData['srvUrl'] = $srvUrl;
    
            return $sndData;
        }
    
    }

    
}