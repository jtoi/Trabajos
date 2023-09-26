<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );


/**
 * Hace el envío de correos desde el sitio
 *
 * @author julio
 */
class correo {
    //put your code here
    var $header = array();
    var $from = '';
    var $to = '';
    var $message = '';
    var $subject = '';
    var $temp;
    var $host = '';
    var $user = '';
    var $pass = '';
    var $cc = '';
    var $bcc = '';
    var $sismtp = true;
    var $reply;
    
    function __construct() {
		require_once "Mail.php";
    	$this->temp = new ps_DB();
//      $this->from = "tpv@amfglobalitems.com";
     $this->from = "tpv@bidaiondo.com";
// 	$this->from = "tpv@administracomercios.com";
        
    }
    
    function mlDatos() {
    	if (strstr($this->from, 'noreply@bidaiondo.com')) {
    		$this->from = 'tpv@bidaiondo.com';
    		$this->host = 'mail.bidaiondo.com';
    		$this->user = 'tpv@bidaiondo.com';
    		$this->pass = '$1$8tAzDNOP$zXuTGlGibAjg5/kB.7isk1';
//     		$this->host = 'mail.bidaiondo.com';
//     		$this->user = 'noreply@bidaiondo.com';
//     		$this->pass = '$1$JzEkIHMd$48vMNN.EIWiLTkqWKSl7k.';
    	} elseif (strstr($this->from, '@travelsdiscovery.com')) {
    		$this->host = 'mail.travelsdiscovery.com';
    		$this->user = 'tpv@travelsdiscovery.com';
    		$this->pass = '$1$bm2Xn..3$4';
    	} elseif (strstr($this->from, '@publinetservicios.com')){
    		$this->host = 'mail.publinetservicios.com';
    		$this->user = 'tpv@publinetservicios.com';
    		$this->pass = 'Vu6ix^97';
    	} elseif (strstr($this->from, '@bidaiondo.com')){
    		$this->host = 'mail.bidaiondo.com';
    		$this->user = 'tpv@bidaiondo.com';
    		$this->pass = '$1$8tAzDNOP$zXuTGlGibAjg5/kB.7isk1';
    	} elseif (strstr($this->from, '@caribeantravelweb.com')){
    		$this->host = 'mail.caribeantravelweb.com';
    		$this->user = 'tpv@caribeantravelweb.com';
    		$this->pass = '$1$eCHcR8h2$UViriuObcbC6H2oU0cnBy';
    	} elseif (strstr($this->from, '@tropicalnatur.com')){
    		$this->host = 'mail.tropicalnatur.com';
    		$this->user = 'tpv@tropicalnatur.com';
    		$this->pass = '$1$cKOkwlWr$VUFUlm9cmkc9siEnyU/4L';
    	} elseif (strstr($this->from, '@caribeantravelway.com')){
    		$this->host = 'mail.caribeantravelway.com';
    		$this->user = 'tpv@caribeantravelway.com';
    		$this->pass = '$1$b3.YAiSj$XaQSTk3KrkdM.4vgB6P3g';
    	} elseif (strstr($this->from, '@travelsandiscovery.com')){
    		$this->host = 'mail.travelsandiscovery.com';
    		$this->user = 'tpv@travelsandiscovery.com';
    		$this->pass = '$1$XHk6pbXo$R0M2RCsRC4lLIs9qTVLAq';
    	} else {
//     		$this->host = 'mail.amfglobalitems.com';
//     		$this->user = 'tpv@amfglobalitems.com';
//     		$this->pass = 'fQjx8*49';
//     		$this->host = 'mail.administracomercios.com';
//     		$this->user = 'tpv@administracomercios.com';
//     		$this->pass = '$1$i3UtcCDX$Oc0f67GjHy.C08j58f.73';
    		$this->host = 'mail.bidaiondo.com';
    		$this->user = 'tpv@bidaiondo.com';
    		$this->pass = '$1$8tAzDNOP$zXuTGlGibAjg5/kB.7isk1';
    	}
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
    
    /**
     * Calcula la fecha del correo según el standard RFC
     * @return string
     */
    private function RFCDate() {
        $tz = date("Z");
        $tzs = ($tz < 0) ? "-" : "+";
        $tz = abs($tz);
        $tz = ($tz/3600)*100 + ($tz%3600)/60;
        $result = sprintf("%s %s%04d", date("D, j M Y H:i:s"), $tzs, $tz);

        return $result;
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
	
	function set_reply($reply) {
		$this->reply = $reply;
	}

	function set_headers ($headers = '') {
		$this->add_headers($headers);
	}
	
	function add_headers ($headers) {
		$hea = explode(": ", $headers);
		if (strstr(strtoupper($headers), "BCC: ")) if(strlen($this->bcc) > 5) $this->bcc .= ",".$hea[1]; else $this->bcc .= $hea[1];
		else if (strstr(strtoupper($headers), "CC: ")) if(strlen($this->cc) > 5) $this->cc .= ",".$hea[1]; else $this->cc .= $hea[1];
	}
    
    function envia($idf, $from='', $to='', $subject='', $message='', $headers='') {
        if (empty ($from)) $from = $this->from;
		if (empty ($to)) $to = $this->to;
		if (empty ($message)) $message = $this->message;
		if (empty ($subject)) $subject = $this->subject;
		if (empty ($headers)) $headers = $this->header;
		$this->mlDatos();
        
        //revisa qué hacer si envía correo o escribe en la BD o ambas cosas
        $q = "select accion from tbl_correos where id = $idf";
        $arrSal = $this->query($q);
        $accion = $arrSal[0][0];
        
        //Salva en la base de datos la traza
        if ($accion == 1 || $accion == 2) {
        	
            $messages = str_replace('"', '&quot;', str_replace("'", '&#039;', $message));
            
            $q = "insert into tbl_traza (titulo,traza,fecha) values ('".$subject."','".  strip_tags($messages,'<br><p><input>') ."',".time().")";
            $data = $this->query($q);
        }
        
        //Envía los correos
        if ($accion == 0 || $accion == 2) {
            $q = "select case when para = '' then (select email from tbl_admin a where activo = 'S' and a.idadmin = d.idadmin) else para end dir from tbl_destinatario d ".
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
                                $this->add_headers("Bcc: ".$arrEnv[0][$i]);
                            }
                            $paseto=1;
                        }
                        else {
                            $this->add_headers("Bcc: ".$arrEnv[0][$i]);
                        }
                    }
                }
                $this->add_headers("Bcc: serv.tecnico@amfglobalitems.com");
            } elseif(count($arrEnv[0]) == 1 && !strpos($arrEnv[0][0],'jotate')) {
                $this->add_headers("Bcc: serv.tecnico@amfglobalitems.com");
                if (!strpos($arrEnv[0][0], 'jotate') && strlen($this->to) == 0) {$this->to($arrEnv[0][0]);}
                elseif (!strpos($arrEnv[0][0], 'jotate') && strlen($this->to) != 0) {$this->add_headers("Cc: ".$arrEnv[0][0]);}
            } else {
                if (strlen($this->to) == 0) {$this->to("serv.tecnico@amfglobalitems.com");}
                else {$this->add_headers("Bcc: serv.tecnico@amfglobalitems.com");}
            }
            
            if (strlen($this->reply) == 0) $this->reply = $this->from;
            
            $this->header = array("MIME-Version"=>"1.0",
					"Content-type"=>"text/html; charset=iso-8859-1",
					"Message-ID" => "<" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">",
					"Date" => $this->RFCDate(),
					"From"=>$this->from,
					"Reply-To"=>$this->reply,
					"Return-Path"=>$this->reply,
            		"To"=>$this->to,
            		"Cc"=>$this->cc,
            		"Bcc"=>$this->bcc,
            		"Subject"=>$this->subject
            );
// 			print_r($this->header);

            if (!strstr($_SERVER['DOCUMENT_ROOT'], '/home/jtoirac/') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/var/www/html') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/var/www/concentrador') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/home/julio/www') && 
                    !strstr($_SERVER['DOCUMENT_ROOT'], '/wamp/www/')) {
// 				$sale = mail($this->to, $subject, $message, $this->header);
				$smtp = Mail::factory('smtp',
						array ('host' => $this->host,
								'auth' => true,
								'username' => $this->user,
								'password' => $this->pass));
				$a = $this->to;
				if ($this->bcc) $a .= ",".$this->bcc;
				if ($this->cc) $a .= ",".$this->cc;
				
				if ($this->sismtp) {
				
					$sale = $smtp->send($a, $this->header, $this->message);
					
					if (PEAR::isError($sale)) {
						foreach ($this->header as $item){
							$sa .= $item."\n<br>";
						}
						$mns = "Error enviando correo : \n<br>$sa\n<br>". $sale;
						error_log($mns);
						$q = "insert into tbl_traza (titulo,traza,fecha) values ('Error envío correo','".  $mns ."',".time().")";
						$this->query($q);
					}
				} else {
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					
					// Additional headers
					$headers .= 'To: '.$this->to . "\r\n";
					$headers .= 'From: '.$this->from . "\r\n";
					$headers .= 'Cc: '.$this->cc . "\r\n";
					$headers .= 'Bcc: '.$this->bcc . "\r\n";
					mail($this->to, $this->subject, $this->message, $headers);
				}
				
            }
//             else echo "\n<br>***********************************************<br>\n".
//             		$this->to." \n".$subject." \n".$message." \n".  $this->header.
//             		"\n<br>***********************************************<br><br>\n\n";
//             error_log('mensaje');
        }
        
        $this->destroy();
        return true;
    }
    
    function todo ($ids, $lab, $mes){
        $this->set_subject($lab);
        $this->set_message($mes);
        return $this->envia($ids);
    }
            
    function destroy(){
    	$this->host = $this->user = $this->pass = $this->to = $this->cc = $this->bcc = '';
        $this->from = "tpv@amfglobalitems.com";
      	$this->header = "";
      	$this->set_subject('');
      	$this->set_message ('');
    }
}
?>
