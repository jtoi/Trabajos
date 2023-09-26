<?php

/*
  SecureSession Extended class by Seth Carter <seth36@gmail.com>
  Based off original SecureSession class by Vagharshak Tozalakyan
  http://www.phpclasses.org/browse/package/2794.html

  Modifications to original class by Seth Carter:
    * Added a __construct method to handle session initialisation and lifetime
    * Updated hashing method from md5 to sha256 or users pref using hash() if available, else falls back to sha1 or md5
    * Force removal of session ids being passed to the query string
    * Fixed a bug with version_compare() function that only allowed v5.1.0 users to call session_regenerate_id(true)
    * Cleaned up FingerPrint() method and switched it to use hash() method where possible
    * Removed regenerate_id member option - Pages always call session_regenerate_id()
    * Added public member (bool)$DeleteOldSession set as true to use session_regenerate_id(true) to clear old session files
    * Added a pass by reference option AnalyseFingerPrint() method - copies results of fingerprint check for error handling / redirects etc
    * Added a Destroy() method to wipe the $_SESSION array, delete the cookie and destroy the session on demand (useful with AnalyseFingerPrint() pass by ref var)
    * Created PHP5 and PHP4 versions of the class (PHP5 version recommend, with PHP4 AnalyseFingerPrint() pass by ref var is required on each call - php4 bug

  Released under GNU Public License
*/

define("SESSION_SALT", $_SERVER['HTTP_HOST']);
define("SESSION_DIR", "{$_SERVER['HTTP_HOST']}_sessiondata");

class SecureSession {
	private $SecureWord, $UserAgent, $IPBlocks, $Algorithm;
	public $DeleteOldSession = false;

	public function __construct($Lifetime = null, $SecureWord = SESSION_SALT, $UserAgent = true, $IPBlocks = 3, $Algorithm = "sha256", $SessionDirName = SESSION_DIR) {

		if (!isset($_SESSION)) {
			$Seperator = (strstr(strtoupper(substr(PHP_OS, 0, 3)), "WIN")) ? "\\" : "/";
			$DirectoryPath = ini_get("session.save_path") . "{$Seperator}{$SessionDirName}";
//			is_dir($DirectoryPath) or mkdir($DirectoryPath);

			if (ini_get("session.use_trans_sid") == true) {
				ini_set("url_rewriter.tags", "");
				ini_set("session.use_trans_sid", false);

			}

			$Lifetime = ($Lifetime === null) ? 3600 : abs(intval($Lifetime));
			ini_set("session.gc_maxlifetime", $Lifetime);
			ini_set('session.use_only_cookies', 1);
			ini_set('session.cookie_lifetime', $Lifetime);
			ini_set("session.gc_divisor", "1");
			ini_set("session.gc_probability", "1");
//			ini_set("session.cookie_lifetime", "0");
//			ini_set("session.save_path", $DirectoryPath);

			session_start();

		}

		$this->SecureWord = (strlen($SecureWord) >= 4) ? str_replace(" ", "_", $SecureWord) : "SALT_";
		$this->UserAgent = ($UserAgent == true) ? true : false;
		$this->IPBlocks = (($IPBlocks = abs(intval($IPBlocks))) > 4) ? 4 : $IPBlocks;
		$this->Algorithm = (function_exists("hash") && in_array($Algorithm, hash_algos())) ? $Algorithm : null;

	}

	private function FingerPrint() {

		$FingerPrint = $this->SecureWord;

		if ($this->UserAgent === true) {
			$FingerPrint .= $_SERVER['HTTP_USER_AGENT'];

		}

		if ($this->IPBlocks > 0) {
			$Blocks = array();
			$IPComponents = explode(".", $_SERVER['REMOTE_ADDR']);

			for ($iBlocks = 0; $iBlocks < $this->IPBlocks; $iBlocks++) {
				$Blocks[] = $IPComponents[$iBlocks];

			}

			$FingerPrint .= implode(".", $Blocks);
			unset($Blocks, $IPComponents);

		}

		// We already checked the existance of hash() and hash_algos(), use sha256 or preferred cypher
		// if hash() is supported, otherwise fall back to sha1, md5

		if ($this->Algorithm !== null) {
			return hash($this->Algorithm, $FingerPrint);

		} else {
			return (function_exists("sha1")) ? sha1($FingerPrint) : md5($FingerPrint);

		}

	}

	private function RegenerateID() {

		if (function_exists("session_regenerate_id")) {
			// originally this was version_compare("5.1.0", phpversion(), ">=") which meant only users of 5.1.0 could use session_regenerate_id(true)

			if (version_compare(phpversion(), "5.1.0", ">=") && $this->DeleteOldSession == true) {
				session_regenerate_id(true);

			} else {
				if (!headers_sent()) session_regenerate_id();

			}

		}

	}

	public function SetFingerPrint() {
		$this->RegenerateID();
		$_SESSION['_FingerPrint'] = $this->FingerPrint();

	}

	// This is PHP5 we can just call $Session->AnalyseFingerPrint() if we want, but we wont know
	// why the check failed if it does, to get that info we need to pass a variable as the argument

	public function AnalyseFingerPrint(&$FingerPrintAnalysis = null) {
		$this->RegenerateID();

		if (isset($_SESSION['_FingerPrint'])) {
			return ($FingerPrintAnalysis = ($_SESSION['_FingerPrint'] === $this->FingerPrint()));

		} else {
			$FingerPrintAnalysis = null;
			return false;

		}

	}

	// We use this to kill the user's session if the fingerprint check fails

	public function Destroy() {

		if (isset($_SESSION)) {
			$_SESSION = array();

			if (isset($_COOKIE[session_name()])) {
				if (!headers_sent()) setcookie(session_name(), "", time() - 42000, "/");

			}

			@session_destroy();

		}

	}

}

?>
