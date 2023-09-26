<?php
defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
/*
 * Class que trabaja todas las funciones del index
 */


/**
 * Description of newPHPClass
 *
 * @author julio
 */

class curlenv {
	private $curl;
    private $ssl = false; // conexion sin SSL
    private $post_array_type = 1; //tratamiento del array para POST normal, 2 tratamiento por json

    /**
     * Inicializa el objeto curl con las opciones por defecto.
     */
    public function init() {
        $this->curl=curl_init();
        // parametros globales
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, false);
    }
    /**
     * Establece el estado de la propiedad Post Array type
     * @param integer (1 o 2) $post_array_type
     */
    public function setPostArrayType($post_array_type){
        $this->post_array_type = $post_array_type;
    }
    /**
     * Devuelve el estado de la propiedad Post Array type
     */
    public function getPostArrayType(){
        return $this->post_array_type;
    }
    /**
     * Establece el estado de la propiedad SSL
     * @param boolean $ssl
     */
    public function setSSL($ssl, $cert=null, $certkey=null){
        $this->ssl = $ssl;
        if ($this->ssl) {
        	if ($cert) 		curl_setopt($this->curl, CURLOPT_SSLKEY, $cert);
        	if ($certkey) 	curl_setopt($this->curl, CURLOPT_SSLKEY, $certkey);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($this->curl, CURLOPT_SSLVERSION,3);
        } else {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        }
    }
    /**
     * Devuelve el estado de la propiedad SSL
     */
    public function getSSL(){
        return $this->ssl;
    }
    /**
     * Envía una peticion GET a la URL especificada
     * @param string $url
     * @param bool $has_return
     * @param bool $header
     * @return string Respuesta generada por el servidor
     */
    public function get( $url, $has_return=false, $header=false ) {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST,false);
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $has_return);
//		$retorno = curl_exec ($this->curl);
//		return $retorno;
        if ($has_return) {
            return curl_exec ($this->curl);
        }
        else {
            curl_exec ($this->curl);
        }
    }
    /**
     * Envía una petición POST a la URL especificada
     * @param string $url
     * @param array $post_elements
     * @param bool $has_return
     * @param bool $header
     * @return string Respuesta generada por el servidor
     */
    public function post( $url, $post_elements, $has_return=false, $header=false) {
        $elements=array();
        if ($this->post_array_type == 2) {
            $elements = json_encode($elements);
        } else {
            foreach ($post_elements as $name=>$value) {
                $elements[$name] = $value;
            }
        }
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST,true);
        curl_setopt($this->curl, CURLOPT_HEADER, $header);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $elements);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $has_return);
//		$retorno = curl_exec ($this->curl);
//		return $retorno;
        if ($has_return) {
            return curl_exec ($this->curl);
        }
        else {
            curl_exec ($this->curl);
        }
    }
    /**
     * Descarga un fichero binario en el buffer
     * @param string $url
     * @return string
     */
    public function getBinary($url){
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_BINARYTRANSFER,1);
        $result = curl_exec ($this->curl);
        return $result;
    }
    /**
     * Cierra la conexión
     */
    public function close() {
        curl_close($this->curl);
    }
    /**
     * Comprueba ejecucion del curl realizado
     */
    public function getcheckErrorConection() {
        if(curl_errno($this->curl))
        {
            $info = curl_getinfo($this->curl);
            return 'Problemas al ejecutar la peticion.  Toma ' . $info['total_time'] . ' segundos recibir respuesta de ' . 
            	$info['url']."\n ";
        }
        return "OK";
    }
    public function useragent(){
    	curl_setopt($this->curl,CURLOPT_USERAGENT,$this->getRandomUserAgent());
    }
    /**
     * Genera distintos userAgent
     * @return Ambigous <string>
     */
    private function getRandomUserAgent()
    {
    	$userAgents=array(
    			"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
    			"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
    			"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
    			"Opera/9.20 (Windows NT 6.0; U; en)",
    			"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50",
    			"Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.02 [en]",
    			"Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; fr; rv:1.7) Gecko/20040624 Firefox/0.9",
    			"Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/48 (like Gecko) Safari/48"
    	);
    	$random = rand(0,count($userAgents)-1);
    
    	return $userAgents[$random];
    }
    /**
     * Establece los parametros de la conexion a traves de un array
     * @param array datos
    // para establecer campos sin comilla, para valores de cadenas utilizar ' o " simple, ejemplo, $data = array ( CURLOPT_URL =>,'http://www.google.com');
     */
    public function set_options( $datos ) {
        curl_setopt_array($this->curl, $datos);
    }

    public function get_errno() {
        return curl_errno($this->curl);
    }

    public function get_error() {
        return curl_error($this->curl);
    }

    public function get_infocurl() {
        return curl_getinfo($this->curl);
    }

}

?>