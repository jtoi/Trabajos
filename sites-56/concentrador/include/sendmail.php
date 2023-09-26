<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

class sendmail {
	
	var $self;
	
	function sendmail ($conf_file='') {
		// Configuration block
		if (file_exists($conf_file)) {
			@include($conf_file);
		}
		$this->self = $GLOBALS['PHP_SELF'];
		$this->internal_smtp = $smtp?$smtp:'127.0.0.1';
		$this->internal_port = (empty ($port))?25:$port;
		$this->internal_helo = (empty ($helo))?'a':$helo;
		// End of configuration block
		return true;
	}

	function errlog ($str) {
		if (!$fp = @fopen ("sendmail.errlog", "a")) return false;
		@fputs ($fp, date("Y-m-d H:i:s")." - ERROR: $str\n");
		@fclose ($fp);
		return false;
	}
	
	function oklog ($str) {
		if (!$fp = @fopen ("sendmail.log", "a")) return false;
		@fputs ($fp, date("Y-m-d H:i:s")." - $str\n");
		@fclose ($fp);
		return true;
	}
	
	function get_answer ($param='') {
		$str = chop(@fgets ($this->internal_sp, 1024));
		if ((intval ($str) != 220) && (intval ($str) != 250) && (intval ($str) != 354)) {
			@fclose ($this->internal_sp);
			$this->errlog ("Can't work with smtp. Open with message '$str'. Previos: $param");
		} else {
			$this->oklog ("Line send to server: $param; answer: $str");
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
		$this->headers .= $headers."\n";
	}
	
	function send ($from='', $to='', $subject='', $message='', $headers='') {
		if (empty ($from)) $from = $this->from;
		if (empty ($to)) $to = $this->to;
		if (empty ($mesage)) $message = $this->message;
		if (empty ($headers)) $headers = (!empty ($this->headers))?$this->headers:"Content-Type: text/html; charset=iso-8859-1\n";
		if ($this->internal_smtp == 'sendmail') {
			if (!$this->internal_sp = @fsockopen ($this->internal_smtp, $this->internal_port)) return $this->errlog ("Can't open socket");
			$this->get_answer('Connect');
			@fputs ($this->internal_sp, 'helo '.$this->internal_helo."\n");
			$this->get_answer('helo '.$this->internal_helo."\n");
			@fputs ($this->internal_sp, 'mail from: '.$from."\n");
			$this->get_answer('mail from: '.$from."\n");
			@fputs ($this->internal_sp, 'rcpt to: '.$to."\n");
			$this->get_answer('rcpt to: '.$to."\n");
			@fputs ($this->internal_sp, "data\n");
			$this->get_answer("data\n");
			if (!empty ($headers)) {
				$headers = chop ($headers);
				@fputs ($this->internal_sp, "$headers\n");
			}
			$m = 'From: '.$from."\n".'To: '.$to."\n".'Subject: '.$this->subject."\n\n$message\n.\n";
			@fputs ($this->internal_sp, $m);
			$this->get_answer("sent data:\n$m");
			@fputs ($this->internal_sp, "quit\n");
			$this->get_answer('quit');
			@fclose ($this->internal_sp);
		} else {
			@mail ($to, $this->subject, $message, $headers);
		}
		return true;
	}
}

?>
