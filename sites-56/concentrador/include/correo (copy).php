<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );


/**
 * Hace el env�o de correos desde el sitio
 *
 * @author julio
 */
class correo {
    //put your code here
    var $header;
    var $from;
    var $to;
    var $message;
    var $subject;
    var $temp;
    
    function __construct() {
    	$this->temp = new ps_DB();
    	
        $this->from = "Administrador de Comercios <tpv@travelsdiscovery.com>";
        $this->header = "MIME-Version: 1.0\n".
					"Content-type: text/html; charset=iso-8859-1\n".
					"From: ".$this->from."\n".
					"Reply-To: ". $this->from ."\n".
					"Return-Path: ". $this->from ."\n".
					"X-Mailer: PHP". phpversion() ."\n";
    }
    
/**
 ** initialize the database
 */
    function dbConnect() {
        global $user, $pass, $host, $db;
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
    	$this->temp->query($query);
    	
        // returndata id appropriate
        if (substr(strtoupper ($query) , 0, 6) == 'SELECT' || substr(strtoupper ($query) , 0, 4) == 'SHOW') {
        	$cant = $this->temp->num_rows();
        	if ($cant != 0) {
        		$return = array();
        		for ($i = 0; $i < $cant; $i++) {
        			$return[] = $this->temp->loadResultArray();
        		}
        		return $return;
        	}
        }
        if (substr(strtoupper ($query) , 0, 6) == 'INSERT') {
        	return $this->temp->last_insert_id();
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
        
        //revisa qu� hacer si env�a correo o escribe en la BD o ambas cosas
        $q = "select accion from tbl_correos where id = $idf";
        $arrSal = $this->query($q);
        $accion = $arrSal[0][0];
        
        //Salva en la base de datos la traza
        if ($accion == 1 || $accion == 2) {
        	
            $messages = str_replace('"', '&quot;', str_replace("'", '&#039;', $message));
            
            $q = "insert into tbl_traza (titulo,traza,fecha) values ('".$subject."','".  $messages ."',".time().")";
            $data = $this->query($q);
        }
        
        //Env�a los correos
        if ($accion == 0 || $accion == 2) {
            $q = "select case when para = '' then (select concat(nombre, ' <', email, '>') from tbl_admin a where activo = 'S' and a.idadmin = d.idadmin) else para end dir from tbl_destinatario d ".
                    "where d.idcorreo = $idf";
            $arrEnv = $this->query($q);
            $paseto = 0;
            if (count($arrEnv[0]) > 1) {
                for ($i=0;$i<count($arrEnv[0]); $i++) {
                    if (!strpos($arrEnv[0][$i], 'jotate')) {
                        if ($paseto == 0) {
                            if (strlen($this->to) == 0) {
                                $this->to($arrEnv[0][$i]);
                            } else {
                                $this->add_headers("Cc: ".$arrEnv[0][$i]);
                            }
                            $paseto=1;
                        }
                        else {
                            $this->add_headers("Cc: ".$arrEnv[0][$i]);
                        }
                    }
                }
                $this->add_headers("Bcc: Julio <serv.tecnico@amfglobalitems.com>");
            } elseif(count($arrEnv[0]) == 1 && !strpos($arrEnv[0][0],'jotate')) {
                $this->add_headers("Bcc: Julio <serv.tecnico@amfglobalitems.com>");
                if (!strpos($arrEnv[0][0], 'jotate') && strlen($this->to) == 0) {$this->to($arrEnv[0][0]);}
                elseif (!strpos($arrEnv[0][0], 'jotate') && strlen($this->to) != 0) {$this->add_headers("Cc: ".$arrEnv[0][0]);}
            } else {
                if (strlen($this->to) == 0) {$this->to("Julio <serv.tecnico@amfglobalitems.com>");}
                else {$this->add_headers("Bcc: Julio <serv.tecnico@amfglobalitems.com>");}
            }

            if (!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/var/www/html') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/home/julio/www') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/wamp/www/')) $sale = mail($this->to, $subject, $message, $this->header);
//             else echo $this->to." - ".$subject." - ".$message." - ".  $this->header."<br><br>";
        }
        
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
        $this->from = "Administrador de Comercios <tpv@travelsdiscovery.com>";
      	$this->header = "MIME-Version: 1.0\n".
					"Content-type: text/html; charset=iso-8859-1\n".
					"From: ".$this->from."\n".
					"Reply-To: ". $this->from ."\n".
					"Return-Path: ". $this->from ."\n".
					"Bcc:\n".
					"Cc:\n".
					"X-Mailer: PHP". phpversion() ."\n";
      	$this->set_subject('');
      	$this->set_message ('');
    }
}
?>
