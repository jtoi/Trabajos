<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of entrada
 *
 * @author jtoirac
 */
class entrada {

	var $digitos		= "0123456789";
	var $letrasMin		= "abcdefghijklmnopqrstuvwxyz";
	var $letrasNum		= "0123456789abcdefghijklmnopqrstuvwxyzåäöáæëéíóúñôðøüÆABCDEFGHIJKLMNOPQRSTUVWXYZÅÄÁÉÍØÖÓÚÜÑ_-., ()";
	var $letras			= "abcdefghijklmnopqrstuvwxyzåëäáæéíóúñüôöðôøÆABCDEFGHIJKLMNOPQRSTUVWXYZÅÄÁÉÍØÖÓÚÜÑ., !&()-_";
	var $correoChar		= ".@-_";
	var $urlChar		= ":/_-?&=.%+";
	var $delim			= '/'; //delimitador para las fechas usadas
	var $forFecha		= 'd/m/Y'; //formato de las fechas usado

	function __construct() {

	}
	
	function isDigito($c) {
		return (stristr($this->digitos, $c));
	}
	
	function isDigitoLetra($c) {
		return (stristr($this->letrasNum, $c));
	}

	function isLetra($c) {
		return (stristr($this->letras, $c));
	}

	function isCorreoVal($c) {
		return (stristr($this->letrasMin.$this->digitos.$this->correoChar, $c));
	}

	function isUrlVal($c) {
		return (stristr($this->letras.$this->digitos.$this->urlChar, $c));
	}
	
	function isLargo($s, $largo) {
		return (strlen($s) <= $largo);
	}

	function isBoolean($c) {
		return $c;
	}

	function isDate($c) {
		$arrFec = explode($this->delim, $c);
		if ($this->forFecha == 'd/m/Y') return date($this->forFecha, mktime(0, 0, 0, $arrFec[1], $arrFec[0], $arrFec[2]));
		return false;
	}


	function isNumero($s, $largo=null) { 
		if ($largo) { if (!$this->isLargo($s, $largo)) return false;}
		$dotAppeared = false;


//		if (!$this->isLargo($s, $largo)) return false;
		for ($i=0; $i<strlen($s); $i++) {
			$c = substr($s,$i,1);

			if ($i == 0) {
				if ($c == ".") { if ($dotAppeared) return false; }
				if (!$this->isDigito($c)) return false;
			} else {
				if ($c == ".") $dotAppeared = true;
				if (!$this->isDigito($c) && ($c != "-") && ($c != "+") && ($c != ".")) return false;
			}
		}
		return $s;
	}

	/**
	 * Chequea que sea una IP (ipv4)
	 *
	 * @param [type] $s
	 * @return boolean
	 */
	function isIp($s) {
		if (!$this->isLargo($s, 15)) return false;
		
		for ($i=0; $i<strlen($s); $i++) {
			$c = $s{$i};

			if (!$this->isDigito($c) && ($c != ".") ) return false;
		}
		return $s;
	}

	function isEntero($s, $largo=null) {
		if ($largo) { if (!$this->isLargo($s, $largo)) return false;}
		for ($i=0; $i<strlen($s); $i++) {
			$c = substr($s,$i,1);

			if ($i != 0) {
				if (!$this->isDigito($c)) return false;
			} else {
				if (!$this->isDigito($c) && ($c != "+") && ($c != "-")) return false;
			}
		}
		return $s;
	}

	function isReal($s, $largo=null) {
		if ($largo) { if (!$this->isLargo($s, $largo)) return false;}
		for ($i=0; $i<strlen($s); $i++) {
			$c = substr($s,$i,1);

			if (!$this->isDigito($c)) return false;

		}
		return $s;
	}

	function isAlfabeto($s, $largo=null) {
		if ($largo) { if (!$this->isLargo($s, $largo)) return false;}
		for ($i=0; $i<strlen($s); $i++) {
			$c = substr($s,$i,1);

			if (!$this->isLetra($c)) return false;

		}
		return $s;
	}
	
	function isLetraNumero($s, $largo=null) {
		if ($largo) { if (!$this->isLargo($s, $largo)) return false;}

		for ($i=0; $i<strlen($s); $i++) {
			$c = substr($s,$i,1);

			if (!($this->isDigitoLetra($c))) return false;

		}
		return $s;
	}
	
	function isAlfanumerico($s, $largo=null) {
		if ($largo) { if (!$this->isLargo($s, $largo)) return false;}

		for ($i=0; $i<strlen($s); $i++) {
			$c = substr($s,$i,1);

			if (!($this->isLetra($c) || ($this->isDigito($c)))) return false;

		}
		return $s;
	}

	function isCorreo($s, $largo=null) {
		if ($largo) { if (!$this->isLargo($s, $largo)) return false;}
		$atAppeared = false;

		for ($i=0; $i<strlen($s); $i++) {
			$c = substr($s, $i, 1);

			if ($i != 0) {
				if ($c == "@") { if ($atAppeared) return false; else $atAppeared = true; }
				if (!$this->isCorreoVal($c)) return false;
			} else {
				if (($c == ".") || ($c == '@')) return false;
				if (!$this->isCorreoVal($c)) return false;
			}
		}
		if ($atAppeared) return $s; else return false;
	}

	function isUrl($s, $largo=null) {
		if ($largo) { if (!$this->isLargo($s, $largo)) return false;}
		$atAppeared = false;

		for ($i=0; $i<strlen($s); $i++) {
			$c = substr($s,$i,1);

			if ($i != 0) {
				if (!$this->isUrlVal($c)) return false;
			} else {
				if (stristr($this->urlChar, $c)) return false;
				if (!$this->isUrlVal($c)) return false;
			}
		}
		return $s;
	}

	function isArreglo($s) {
		if (!is_array($s)) return false;
		return $s;
	}

	function isDeHtml($s, $largo=null) {
		if (!$this->isLargo($s, $largo)) return false;
		return htmlentities($s, ENT_QUOTES);
	}

	function isParaHtml($s, $largo=null) {
		if (!$this->isLargo($s, $largo)) return false;
		return html_entity_decode($s);
	}


}
?>
