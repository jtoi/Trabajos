<?php

//namespace classes;

//use ps_DB;

/**
 * Class de funciones de apoyo
 *
 * @author jtoirac
 */
class funcion {
	
	var $login = '';
	var $pass = '';
	var $temp;
	
	function __construct() {
		$this->temp = new ps_DB();
	}
	
	function carcHange($frase) {
		return str_replace('ñ', 'n', str_replace('ú', 'u', str_replace('ó', 'o', str_replace('í', 'i', str_replace('é', 'e', str_replace('á', 'a', $frase))))));
	}
	
	/**
	 * Envía los correos
	 * @global type $mail
	 * @param type $to
	 * @param type $subj
	 * @param type $cont
	 * @param type $cc
	 * @param type $bcc
	 * @param type $att
	 * @param type $nohtml
	 * @return boolean
	 */
	function smtpmail($to, $subj, $cont, $cc = null, $bcc = null, $att = null, $nohtml = ''){
		global $mail;
//error_log("$to, $subj, $cont, $cc, $bcc, $att");
		if (!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/var/www/html') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/var/www/sites56') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/var/www/concentrador') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/home/julio/www') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/home/julio/www') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/wamp/www/')) {
			$from = array('noreply@arteorganizer.com', 'Julio Toirac');

			$mail->IsSMTP();
	//		if (_DEBUG_PROJECT) $mail->SMTPDebug = 2;
			$mail->AddAddress($to[0], $to[1]);
			$mail->SetFrom($from[0], $from[1]);
			$mail->AddReplyTo($from[0], $from[1]);
			$mail->SetLanguage($_SESSION['idioma']);
			$mail->AltBody = $nohtml;

			if (is_array($bcc)) {
				$mail->addBCC($bcc[0], $bcc[1]);
			} else $mail->addBCC('jtoirac@gmail.com', 'Julio Toirac');

			if (is_array($cc)) {
				$mail->addCC($cc[0], $cc[1]);
			}

			$mail->setSubject($subj);

			if (strlen($cont) > 10) {
				$mail->MsgHTML($cont);
			} else return $this->idioma ("Error: El cuerpo del correo no puede estar vacio");

			if (is_array($att)) {
				for ($i=0; $i<count($att); $i++){
					$mail->AddAttachment($att[$i]);
				}
			}

			return $mail->Send();
		} 
		return true;
	}


	/**
	 * Genera las contraseñas para la entrada al sitio en base al correo del usuario
	 * @param strin $login correo del usuario
	 * @return string
	 */
	function genContr($login) {
		$this->temp->query(sprintf("select count(*) total, id, ididioma, nombre from tbl_admin where email = '%s'", $login ));
//		echo sprintf("select id from tbl_admin where email = '%s'", $login );exit;
//		echo "pase=".$this->temp->f('total');exit;
		if ($this->temp->f('total')) {
			$id = $this->temp->f('id');
			$pass = $this->suggestPassword(8);
//			echo "update tbl_admin set md5 = '$pass' where id = $id";
			$salida = $pass."|".$this->temp->f('ididioma')."|".$this->temp->f('nombre');
			$this->temp->query("update tbl_admin set md5 = '$pass' where id = $id");
			return $salida;
		} else {return 'Error: La direccion de correos no aparece en la Base de Datos';}
	}

	/**
	 * env�a mensajes a Telegram
	 */
	function sendTelegram ($texto,$otro,$bot='G') {
		if ($otro != null) $id = $otro;

		//echo "$texto<br>$otro<br>$bot<br>";

		if ($bot == 'G') {
			$bot_token = "5038443834:AAHk906Tuj0zWRt1kITZtWk1XXX4vZeDnGc"; //env�o a m�
			$id="-1001652358529";
		}

		$parameters = array(
			"chat_id" => $id,
			"text" => utf8_encode(str_replace(array("<br />","<br/>","<br>","\n", "</tr>"),chr(10),$texto))
		);

		$url = "https://api.telegram.org/bot$bot_token/sendMessage";

		if (!$curl = curl_init()) {
			exit();
		}

		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$output = curl_exec($curl);
		return $output;

	}
	
	/**
	 * Cambia los textos en base al idioma del usuario
	 * @param string $frase
	 * @return string
	 */
	function idioma($frase, $idio = null) {
//error_log("entrada en idioma:$frase, $idio");
		if ($idio == null) {$idio = $_SESSION['idioma'];}
//error_log("select texto from tbl_adminIdioma where frase = '".utf8_decode($frase)."' and idioma = '$idio'");

		$this->temp->query("select texto from tbl_adminIdioma where frase = '".utf8_decode($frase)."' and idioma = '$idio'");
//		$sale = htmlentities($this->temp->f('texto'), ENT_QUOTES, "ISO-8859-1");
		$sale = $this->temp->f('texto');
		if (strlen($sale) == 0) {$sale = $frase;}
		
		return $sale;
	}

	/**
	 * Devuelve el código iso de los idiomas que tenga la aplicacion
	 * @return aray
	 */
	function idiomas () {
		$this->temp->query("select iso2 from tbl_idioma order by idioma ");
		return $this->temp->loadResultArray();
	}
	
	/**
	 * Generador de passwords
	 * @param type $largo
	 * @param type $pass
	 * @return type
	 */
	function suggestPassword($largo, $pass = true) {
		if ($pass) {$pwchars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";}
		else {$pwchars = "0123456789";}
		$passwd = "";

		for ( $i = 0; $i < $largo; $i++ ) {
			$passwd .= substr($pwchars, rand(1, strlen($pwchars)), 1 );
		}

		return $passwd;
	}
	
	/**
	 * Formatea el dinero segun el formato seleccionado por el usuario
	 * @param type $dinero
	 * @return type 
	 */
	function frdinero ($dinero) {
		return number_format($dinero, 2, $_SESSION['decims'], $_SESSION['miless']);
	}

	/**
	 * Valida la entrada de los usuarios al sitio y carga los datos de session
	 * @param string $pass contraseña del usuario
	 * @param string $login correo del usuario
	 * @param string $ip ip de la máquina del usuario
	 * @return boolean
	 */
	function verif($pass, $login, $ip){

		$this->temp->query("select id from tbl_admin where md5 = (select calcSha('".$pass ."','". $login ."' ))");
		
		if ($this->temp->num_rows()) {
			
			$this->temp->query("call valida_usuario('$pass', '$ip', @nombreU, @idusr, @idartista, @seudonimoA, @email, @Ordenrol,@idrol,@idioma, @imagen)");
			if ($this->temp->getErrorMsg()) {return $this->temp->getErrorMsg();}
			$this->temp->query("select @nombreU nombreU, @idusr idusr, @idartista idartista, @email correo, @Ordenrol Ordenrol, @seudonimoA seudonimoA, @idrol idrol, @idioma idioma, @imagen imagen");
			
// 			var_dump($this->temp->loadAssocList());
// 			exit;
			if ($this->temp->num_rows()) {
				$_SESSION['LoggedIn'] = true;
				$_SESSION['id'] = $this->temp->f('idusr');
				$_SESSION['admin_nom'] = $this->temp->f('nombreU');
				$_SESSION['idartista'] = $this->temp->f('idartista');
				$_SESSION['artista'] = $this->temp->f('seudonimo');
				$_SESSION['rol'] = $this->temp->f('idrol');
				$_SESSION['grupo_rol'] = $this->temp->f('Ordenrol');
				$_SESSION['email'] = $this->temp->f('correo');
				$_SESSION['mdses'] = $this->shaplus($_SESSION['id'].$_SESSION['admin_nom'].$_SESSION['idartista'].$_SESSION['rol'].$_SESSION['grupo_rol'].$_SESSION['email']);
				$_SESSION['idioma'] = $this->temp->f('idioma');
				$_SESSION['sesionId'] = session_id();
				$_SESSION['avatar'] = $this->temp->f('imagen');
				
 				//idiomas de trabajo
				$_SESSION['idioTrab'] = '';
				if ($_SESSION['idartista'] > 0) {
					$this->temp->query("select iso2 from tbl_idioma i, tbl_colArtistaIdioma c where i.id = c.ididioma and c.idartista = ".$_SESSION['idartista']);
					error_log($this->temp->getNumRows());
					if ($this->temp->getNumRows()) $_SESSION['idioTrab'] = implode(',', $this->temp->loadResultArray());
				}
				
				//paginado
				$this->temp->query("select valor from tbl_colSetupAdmin c where idsetup = 6 and idadmin = ".$_SESSION['id']);
				$_SESSION['pagin'] = $this->temp->f('valor');
				
				//separador decimales
				$this->temp->query("select valor from tbl_colSetupAdmin c where idsetup = 7 and idadmin = ".$_SESSION['id']);
				$_SESSION['decims'] = $this->temp->f('valor');
				
				//separador miles
				$this->temp->query("select valor from tbl_colSetupAdmin c where idsetup = 8 and idadmin = ".$_SESSION['id']);
				$_SESSION['miless'] = $this->temp->f('valor');
				
				//formato fecha
				$this->temp->query("select valor from tbl_colSetupAdmin c where idsetup = 9 and idadmin = ".$_SESSION['id']);
				$_SESSION['fechaf'] = $this->temp->f('valor');
				
				//formato horas
				$this->temp->query("select valor from tbl_colSetupAdmin c where idsetup = 10 and idadmin = ".$_SESSION['id']);
				$_SESSION['horaf'] = $this->temp->f('valor');
				
				if (_DEBUG_PROJECT) {
					foreach ($_SESSION as $key => $value) {
						error_log("$key => $value");
					}
				}
				
				
				
				return true;
			}
		} else return false;
		return false;
	}
	
	/**
	 * Devuelve la cadena sha256 para las verificaciones
	 * @param string $cadena Cadena a convertir 
	 * @return string
	 */
	function shaplus($cadena) {
		return hash('sha256',$cadena.'lo realmente hermoso es invisible a los ojos');
	}
	
}

