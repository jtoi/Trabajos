<?php        
// IncluÃ­mos la librerÃ­a nusoap.
require_once('lib/nusoap.php');
   
// Función que parsea el xml y devuelve un array.   
function simpleXMLToArray(SimpleXMLElement $xml,$attributesKey=null,$childrenKey=null,$valueKey=null){

     if($childrenKey && !is_string($childrenKey)){$childrenKey = '@children';}
     if($attributesKey && !is_string($attributesKey)){$attributesKey = '@attributes';}
     if($valueKey && !is_string($valueKey)){$valueKey = '@values';}

     $return = array();
     $name = $xml->getName();
     $_value = trim((string)$xml);
     if(!strlen($_value)){$_value = null;};

     if($_value!==null){
         if($valueKey){$return[$valueKey] = $_value;}
         else{$return = $_value;}
     }

     $children = array();
     $first = true;
     foreach($xml->children() as $elementName => $child){
         $value = simpleXMLToArray($child,$attributesKey, $childrenKey,$valueKey);
         if(isset($children[$elementName])){
             if(is_array($children[$elementName])){
                 if($first){
                     $temp = $children[$elementName];
                     unset($children[$elementName]);
                     $children[$elementName][] = $temp;
                     $first=false;
                 }
                 $children[$elementName][] = $value;
             }else{
                 $children[$elementName] = array($children[$elementName],$value);
             }
         }
         else{
             $children[$elementName] = $value;
         }
     }
     if($children){
         if($childrenKey){$return[$childrenKey] = $children;}
         else{$return = array_merge($return,$children);}
     }

     $attributes = array();
     foreach($xml->attributes() as $name=>$value){
         $attributes[$name] = trim($value);
     }
     if($attributes){
         if($attributesKey){$return[$attributesKey] = $attributes;}
         else{$return = array_merge($return, $attributes);}
     }

     return $return;
 } 
        
// Clase Model_BbvaClient.  
class Model_BbvaClient
{
    /** Datos conexión **/
    private $_url_webservice = "https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_rpcrouter";
    private $_nombre_servicio = "PeticionTPVSoapS";
    
    /** Configuración **/
    private $_palabra_secreta_ofuscada = "47;57;5C;35;25;50;5C;2F;72;7D;05;70;02;03;75;73;79;1A;6C;1A";
    private $_contrasena_palabra_secreta = "santaemi";
    
    private $_codigo_comercio = "B9550206800001";
    private $_id_terminal = "999999";
    private $_moneda = "978";
    
    private $_timeout = 90;
    
    public function sendPeticion($idtransaccion, $numtarjeta, $fechacaducidad, $cvv2, $importe) 
    {
        $xml = "<tpv><oppago>";
        $xml .= sprintf("<idterminal>%s</idterminal>", $this->_id_terminal);
        $xml .= sprintf("<idcomercio>%s</idcomercio>", $this->_codigo_comercio);
        $xml .= sprintf("<idtransaccion>%s</idtransaccion>", $idtransaccion);
        $xml .= sprintf("<moneda>%s</moneda>", $this->_moneda);
        $xml .= sprintf("<importe>%s</importe>", number_format($importe, 2, ".", ""));
        $xml .= sprintf("<numtarjeta>%s</numtarjeta>", $numtarjeta);
        $xml .= sprintf("<fechacaducidad>%s</fechacaducidad>", $fechacaducidad);
        $xml .= sprintf("<cvv2>%s</cvv2>", $cvv2);
        
        $importe_sin_comas = number_format($importe, 2, ".", "")*100;
        $firma = sha1($this->_id_terminal.$this->_codigo_comercio.$idtransaccion.$importe_sin_comas.$this->_moneda.$this->__desofuscarPalabraSecreta());
        
        $xml .= sprintf("<firma>%s</firma>", strtoupper($firma));
        $xml .= "</oppago></tpv>";
        
        $cliente_soap = new nusoap_client($this->_url_webservice, false);
        $err = $cliente_soap->getError();
        if ($err) {
            throw new Exception(__('Ocurrió un error al intentar procesar su pago, por favor inténtelo más tarde.', true));
        }
        $cliente_soap->setUseCurl(1);
        $cliente_soap->soap_defencoding = 'UTF-8';
        $params = array('peticion' => $xml);
        $result = $cliente_soap->call("procesarPago", $params, $this->_nombre_servicio);
        if ($cliente_soap->fault) {
        	// Ocurrió un error con la petición.
            throw new Exception(__('Ocurrió un error al intentar procesar su pago, por favor inténtelo más tarde.', true));
        } else {
            $err = $cliente_soap->getError();
            if ($err) {
        		// Ocurrió un error con la petición.
                throw new Exception(__('Ocurrió un error al intentar procesar su pago, por favor inténtelo más tarde.', true));
            } else {        
                $array_result = $this->parse($result);
                if (isset($array_result['respago'])) {
                    // PAGO CORRECTO.
                    if (!$this->validarFirma($array_result)) {
        				// La firma recibida no coincide con la que calculamos.
        				// La respuesta no es del TPV.
                        throw new Exception(__('Ocurrió un error al intentar procesar su pago, por favor inténtelo más tarde.', true));
                    } else {
                        if ($array_result['respago']['estado'] > 2) {
                        	// El estado es mayor que 2.
                        	// Ocurrió un error.
                            $pagado = false;
                            $devolver = array(
                                'pagado' => $pagado,
                                'idtransaccion' => $array_result['respago']['idtransaccion'],
                                'estado' => $array_result['respago']['estado'],
                                'coderror' => $array_result['respago']['coderror'],
                                'codautorizacion' => $array_result['respago']['codautorizacion'],
                                'deserror' => $array_result['respago']['deserror']
                            );
                        } else {
                        	// El pago ha sido procesado correctamente.
                            $pagado = true;
                            $devolver = array(
                                'pagado' => $pagado,
                                'idtransaccion' => $array_result['respago']['idtransaccion'],
                                'estado' => $array_result['respago']['estado'],
                                'coderror' => $array_result['respago']['coderror'],
                                'codautorizacion' => $array_result['respago']['codautorizacion']
                            );
                        }
                        return $devolver;
                    }
                } else {
                    // PAGO INCORRECTO.
                    if (isset($array_result['coderror'])) {
                        return array(
                            'pagado' => false,
                            'idtransaccion' => $array_result['oppago']['idtransaccion'],
                            'coderror' => $array_result['coderror'],
                            'deserror' => $array_result['deserror']
                        );
                    }                    
                }
            }
        }
    }
    
    /**
     * Desofusca la palabra secrete.
     * @return la palabra secreta string.
     */
    protected function __desofuscarPalabraSecreta() 
    {
        $clave_xor = $this->_contrasena_palabra_secreta.substr($this->_codigo_comercio, 0, 9)."***";
        
        $cad1_0 = "0";
        $cad2_0 = "00";
        $cad3_0 = "000";
        $cad4_0 = "0000";
        $cad5_0 = "00000";
        $cad6_0 = "000000";
        $cad7_0 = "0000000";
        $cad8_0 = "00000000";
        $pal_sec = "";
        
        $trozos = explode (";", $this->_palabra_secreta_ofuscada);
        $tope = count($trozos);
        
        for ($i=0; $i<$tope ; $i++) {
            $res = "";
            $pal_sec_ofus_bytes[$i] = decbin(hexdec($trozos[$i]));	
            if (strlen($pal_sec_ofus_bytes[$i]) == 7){ $pal_sec_ofus_bytes[$i] = $cad1_0.$pal_sec_ofus_bytes[$i]; }	
            if (strlen($pal_sec_ofus_bytes[$i]) == 6){ $pal_sec_ofus_bytes[$i] = $cad2_0.$pal_sec_ofus_bytes[$i]; }
            if (strlen($pal_sec_ofus_bytes[$i]) == 5){ $pal_sec_ofus_bytes[$i] = $cad3_0.$pal_sec_ofus_bytes[$i]; }
            if (strlen($pal_sec_ofus_bytes[$i]) == 4){ $pal_sec_ofus_bytes[$i] = $cad4_0.$pal_sec_ofus_bytes[$i]; }
            if (strlen($pal_sec_ofus_bytes[$i]) == 3){ $pal_sec_ofus_bytes[$i] = $cad5_0.$pal_sec_ofus_bytes[$i]; }
            if (strlen($pal_sec_ofus_bytes[$i]) == 2){ $pal_sec_ofus_bytes[$i] = $cad6_0.$pal_sec_ofus_bytes[$i]; }
            if (strlen($pal_sec_ofus_bytes[$i]) == 1){ $pal_sec_ofus_bytes[$i] = $cad7_0.$pal_sec_ofus_bytes[$i]; }
            $pal_sec_xor_bytes[$i] = decbin(ord($clave_xor[$i]));
            if (strlen($pal_sec_xor_bytes[$i]) == 7){ $pal_sec_xor_bytes[$i] = $cad1_0.$pal_sec_xor_bytes[$i]; }
            if (strlen($pal_sec_xor_bytes[$i]) == 6){ $pal_sec_xor_bytes[$i] = $cad2_0.$pal_sec_xor_bytes[$i]; }
            if (strlen($pal_sec_xor_bytes[$i]) == 5){ $pal_sec_xor_bytes[$i] = $cad3_0.$pal_sec_xor_bytes[$i]; }
            if (strlen($pal_sec_xor_bytes[$i]) == 4){ $pal_sec_xor_bytes[$i] = $cad4_0.$pal_sec_xor_bytes[$i]; }
            if (strlen($pal_sec_xor_bytes[$i]) == 3){ $pal_sec_xor_bytes[$i] = $cad5_0.$pal_sec_xor_bytes[$i]; }
            if (strlen($pal_sec_xor_bytes[$i]) == 2){ $pal_sec_xor_bytes[$i] = $cad6_0.$pal_sec_xor_bytes[$i]; }
            if (strlen($pal_sec_xor_bytes[$i]) == 1){ $pal_sec_xor_bytes[$i] = $cad7_0.$pal_sec_xor_bytes[$i]; }
            for ($j=0; $j<8; $j++)
            {
                (string)$res .= (int)$pal_sec_ofus_bytes[$i][$j] ^ (int)$pal_sec_xor_bytes[$i][$j];
            }
            $xor[$i] = $res;
            $pal_sec .= chr(bindec($xor[$i]));
        }
        
        return $pal_sec;
    }
    
    /**
     * Valida la firma recibida.
     * @return boolean.
     */
    public function validarFirma($array_result) {
        $importe_formateado = $array_result['respago']['importe'] * 100;
        $firma_calculada = strtoupper(sha1($array_result['respago']['idterminal'].$array_result['respago']['idcomercio'].$array_result['respago']['idtransaccion'].$importe_formateado.$array_result['respago']['moneda'].$array_result['respago']['estado'].$array_result['respago']['coderror'].$array_result['respago']['codautorizacion'].$this->__desofuscarPalabraSecreta()));        
        if ($firma_calculada == $array_result['respago']['firma']) {
            return true;
        }
        
        return false;
    }
    
    /**
    * Parsea el xml.
    * @param string $response
    * @return array Devuelve un array.
    */
    public static function parse($response) {
   
        $result = simplexml_load_string($response);
   
        if (empty($result))
            return NULL;  
        else {
            $result = simpleXMLToArray($result);
        }
   
        return $result;
   }
}