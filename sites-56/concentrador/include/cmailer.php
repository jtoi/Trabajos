<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

class cMailer{
	var $_Direcciones;
	var $_cantidadDirecciones = 0;
	var $_ConexionSMTP;
	var $_Remitente;
	var $_ServidorSMTP = _CORREO_SERVIDOR_SMTP;
	var $_Asunto = "";
	var $_Mensaje;
	var $_NombreUsuario = _CORREO_CUENTA;
	var $_Contrasenya = _CORREO_CUENTA_PASS;
	var $_Autenticar = _CORREO_AUTENTICAR;
	
	function cMailer(){
	}
	
	function AgregaNombreUsuario($NombreUsuario){
		$this->_NombreUsuario = $NombreUsuario;
	}
	
	function AgregaContrasenya($Contrasenya){
		$this->_Contrasenya = $Contrasenya;
	}
	
	function Autenticar(){
		$this->_Autenticar = 1;
	}
	
	function NoAutenticar(){
		$this->_Autenticar = 0;
	}
	
	function AgregaDireccion($Direccion){
		$this->_Direcciones[$this->_cantidadDirecciones] = $Direccion;
		$this->_cantidadDirecciones++;
	}
	
	function AgregaRemitente($Remitente){
		$this->_Remitente = $Remitente;
	}
	
	function AgregaMensaje($Mensaje){
		$this->_Mensaje = $Mensaje;
	}
	
	function AgregaAsunto($Asunto){
		$this->_Asunto = $Asunto;
	}
	
	function Enviar() {
		$cadenaEHLO = "EHLO ".$this->_ServidorSMTP."\r\n";
	//echo 'cadenaEHLO='.$cadenaEHLO.'<br />';
		fputs($this->_ConexionSMTP, $cadenaEHLO);
		
		if($this->_Autenticar == 1) {
			$cadenaAUTH = "AUTH LOGIN\r\n";
			fputs($this->_ConexionSMTP, $cadenaAUTH);
			$cadenaNombreUsuario = base64_encode($this->_NombreUsuario)."\r\n";
			fputs($this->_ConexionSMTP, $cadenaNombreUsuario);
			$cadenaContrasenya = base64_encode($this->_Contrasenya)."\r\n";
			fputs($this->_ConexionSMTP, $cadenaContrasenya);
		}
	
		$cadenaMAIL = "MAIL FROM: ".$this->_Remitente."\r\n";
		fputs($this->_ConexionSMTP, $cadenaMAIL);
		
		for($i = 0; $i < $this->_cantidadDirecciones; $i++) {
			$cadenaRCPT .= "RCPT TO: ".$this->_Direcciones[$i]."\r\n";
		}
		
		$cadenaRCPT .= "\r\n";
		fputs($this->_ConexionSMTP, $cadenaRCPT);
		
		$cadenaDATA1 = "DATA\r\n";
		fputs($this->_ConexionSMTP, $cadenaDATA1);
		
		if($this->_Asunto != ""){
			$cadenaSubject = "Subject: ".$this->_Asunto."\r\n\r\n";
			fputs($this->_ConexionSMTP, $cadenaSubject);
		}
		
		
		$cadenaDATA2 = $this->_Mensaje."\r\n.\r\n";
		fputs($this->_ConexionSMTP, $cadenaDATA2);
		
		fputs($this->_ConexionSMTP, "QUIT\r\n");
		
		fclose($this->_ConexionSMTP);
	}
	
	function AgregaServidor($Servidor, $Puerto = 25){
		$this->_ConexionSMTP = fsockopen("$Servidor", "$Puerto", $e, $em, 5) or die("No puedo abrir el socket");
		echo fgets($this->_ConexionSMTP, 4096)."<br>\n";
		$this->_ServidorSMTP = $Servidor;
	}

}

?>
