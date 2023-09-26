<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );


/**
 * Hace el envío de correos desde el sitio
 *
 * @author julio
 */
require_once( 'class.phpmailer.php' );
require_once( 'class.smtp.php' );
class correo extends PHPMailer {
    //put your code here
    var $header;
    var $from;
    var $to;
    var $message;
    var $subject;
    
    function __construct() {
//        $this->from = "Administrador de Comercios Travels and Discovery <tpv@travelsdiscovery.com>";
//        $this->header = "MIME-Version: 1.0\n".
//					"Content-type: text/html; charset=iso-8859-1\n".
//					"From: ".$this->from."\n".
//					"Reply-To: ". $this->from ."\n".
//					"Return-Path: ". $this->from ."\n".
//					"X-Mailer: PHP". phpversion() ."\n";
        $this->dbConnect();
    }
    
/**
 ** initialize the database
 */
    function dbConnect() {
        global $user, $pass, $host, $db;
//        echo "usuario=$user";
        $this->dblink = mysql_connect($host,$user,$pass);
        if (!$this->dblink) {
            die ('No se puede conectar: ' . mysql_error());
        }
        // select the  database
        $this->db = mysql_select_db($db, $this->dblink);
        if (!$this->db) {
            die ("No puede usar $db: " . mysql_error());
        }
    }
    
/**
 ** Execute a sql query
 */
    private function query($query, $line=0) {
        $result = mysql_query($query);
        if (!$result) {
            die('Invalid query: ' . mysql_error().'<br>'.$query.'<br>'.basename(__FILE__).' '.$line.'<br></div>');
        }
        // returndata id appropriate
        if (substr(strtoupper ($query) , 0, 6) == 'SELECT' || substr(strtoupper ($query) , 0, 4) == 'SHOW') {
            if (mysql_num_rows($result) != 0) {
                $return = array();
                for ($i = 0; $i < mysql_num_rows($result); $i++) {
                    $return[] = mysql_fetch_assoc($result);
                }
                return $return;
            }
        }
        if (substr(strtoupper ($query) , 0, 6) == 'INSERT') {
            return mysql_insert_id();
        }
    }
    
	function from ($from) {
		$this->from = $from;
	}
	
	function to ($to) {
		$this->to = $to;
	}
	
	function set_message ($message = '') {
		$this->message = $message;
	}
	
	function add_message ($message) {
		$this->message .= $message."\n";
	}
	
	function set_subject ($subject) {
		$this->subject = $subject;
	}

	function set_headers ($headers = '') {
		$this->headers = $headers;
	}
	
	function add_headers ($headers) {
		$this->header .= $headers."\n";
	}
    
    function envia($idf, $from='', $to='', $subject='', $message='', $headers='') {
        if (empty ($from)) $from = $this->from;
		if (empty ($to)) $to = $this->to;
		if (empty ($message)) $message = $this->message;
		if (empty ($subject)) $subject = $this->subject;
		if (empty ($headers)) $headers = $this->header;
		
		
		$this->isSMTP();
		$this->isHTML(true);
		$this->SMTPDebug = 1;
		$this->SMTPAuth = true;
		$this->SMTPSecure = 'ssl';
		$this->Host = 'mail.amfglobalitems.com';
		$this->Port = 465;
		$this->Username = 'jotate@amfglobalitems.com';
		$this->Password = 'santaEmilia453';
		$this->SetFrom("tpv@travelsdiscovery.com", "Administrador de Comercios Travels and Discovery");
		$this->Subject = $subject;
		$this->Body = $message;
//		$this->From = 'tpv@amfglobalitems.com';
//		$this->FromName = 'AMF';
		
        
        //revisa qué hacer si envía correo o escribe en la BD o ambas cosas
        $q = "select accion from tbl_correos where id = $idf";
        //echo $q;
        $arrSal = $this->query($q, __LINE__);
        $accion = $arrSal[0]['accion'];
        //echo $accion;
        
        //Salva en la base de datos la traza
        if ($accion == 0 || $accion == 2) {
//            $mens = htmlentities($this->message,ENT_QUOTES);
//            if (strlen($mens) < 4) {$mens = $this->message;}
            $messages = str_replace("'", '&#039;', $message);
            $messages = str_replace('"', '&quot;', $messages);
//            echo "|".$message."|";
            $q = "insert into tbl_traza (titulo,traza,fecha) values ('".$subject."','".  ($messages)."',".time().")";
//            echo $q;
            $data = $this->query($q, __LINE__);
        }
        
        //Envía los correos
        if ($accion == 0 || $accion == 2) {
            $q = "select case when para = '' then (select concat(nombre, ' <', email, '>') from tbl_admin a where activo = 'S' and a.idadmin = d.idadmin) else para end dir from tbl_destinatario d ".
                    "where d.idcorreo = $idf";
//    echo $q;
            $arrEnv = $this->query($q, __LINE__);

    //            echo "pase=".strpos($arrEnv[0],'jotate');
            $paseto = 0;
            if (count($arrEnv) > 1) { //Si hay mas de una persona en la tabla de destinatarios del correo
                for ($i=0;$i<count($arrEnv); $i++) {
					$arrPto = explode('<', $arrEnv[$i]['dir']);
//					echo $arrEnv[$i]['dir']."\n";
                    if (!strpos($arrEnv[$i]['dir'], 'jotate')) {// no está mi correo dentro de esos a mandar
                        if ($paseto == 0) {//analizo el primer correo del listado
                            if (strlen($this->to) == 0) {// el "to" del correo está vacío
								echo "dale";
								$this->addAddress('teress@mailinator.com');
//                                $this->to($arrEnv[$i]['dir']);
                            } else {
								$this->addCC(str_replace('>', '', $arrPto[1]), $arrPto[0]);
//                                $this->add_headers("Cc: ".$arrEnv[$i]['dir']);
                            }
                            $paseto=1;
                        } else {
							$this->addCC(str_replace('>', '', $arrPto[1]), $arrPto[0]);
//                            $this->add_headers("Cc: ".$arrEnv[$i]['dir']);
                        }
                    }
                }
				$this->addBCC("serv.tecnico@amfglobalitems.com", 'Julio');
//                $this->add_headers("Bcc: Julio <serv.tecnico@amfglobalitems.com>");
            } elseif(count($arrEnv) == 1 && !strpos($arrEnv[0]['dir'],'jotate')) { //si sólo hay una persona en la tabla de destinatarios y no soy yo
				$arrPto = explode('<', $arrEnv[$i]['dir']);
				$this->addBCC("serv.tecnico@amfglobalitems.com", 'Julio');
//                $this->add_headers("Bcc: Julio <serv.tecnico@amfglobalitems.com>");
                if (!strpos($arrEnv[0]['dir'], 'jotate') && strlen($this->to) == 0) {
					$this->addAddress(str_replace('>', '', $arrPto[1]), $arrPto[0]);
//					$this->to($arrEnv[0]['dir']);
				}
                elseif (!strpos($arrEnv[0]['dir'], 'jotate') && strlen($this->to) != 0) {
					$this->addCC(str_replace('>', '', $arrPto[1]), $arrPto[0]);
//					$this->add_headers("Cc: ".$arrEnv[0]['dir']);
				}
            } else {
                if (strlen($this->to) == 0) {
					$this->addAddress("jotate@amfglobalitems.com", "Julio");
//					$this->to("Julio <serv.tecnico@amfglobalitems.com>");
				}
                else {
					$this->addBCC("serv.tecnico@amfglobalitems.com", 'Julio');
//					$this->add_headers("Bcc: Julio <serv.tecnico@amfglobalitems.com>");
				}
            }

//            if (
//				!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/') && 
//                !strstr($_SERVER['DOCUMENT_ROOT'], '/var/www/html') && 
//                !strstr($_SERVER['DOCUMENT_ROOT'], '/home/julio/www') && 
//                !strstr($_SERVER['DOCUMENT_ROOT'], '/wamp/www/')
//				) $sale = mail($this->to, $subject, $message, $this->header);
//            else echo $this->to." - ".$subject." - ".$message." - ".  $this->header."<br><br>";
			
			if(!$this->send()) {
				$error = 'Mail error: '.$this->ErrorInfo;
				return false;
			} else {
				$error = 'Message sent!';
				return true;
			}
			
        }
//        echo $this->header;
        $this->destroy();
        return $sale;
    }
    
    function todo ($ids, $lab, $mes){
        $this->set_subject($lab);
        $this->set_message($mes);
        return $this->envia($ids);
    }
            
    function destroy(){
    	$this->to = '';
        $this->from = "Administrador de Comercios Travels and Discovery < tpv@travelsdiscovery.com >";
      	$this->header = "MIME-Version: 1.0\n".
					"Content-type: text/html; charset=iso-8859-1\n".
					"From: ".$this->from."\n".
					"Reply-To: ". $this->from ."\n".
					"Return-Path: ". $this->from ."\n".
					"X-Mailer: PHP". phpversion() ."\n";
      	$this->set_subject('');
    }
}
?>
