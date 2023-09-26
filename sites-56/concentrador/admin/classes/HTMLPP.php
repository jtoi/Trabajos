<?php
/**
 * This is a PHP4 library for parsing html documents and strings and provide a dom structure with methods and properties inherited from javascript
 * @name HTMLPP
 * @package HTMLPP
 * @version 1.0.3
 * @author Marco Marchiò
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 */
 
require_once 'HTMLParser.php';
require_once 'HTMLNode.php';
require_once 'HTMLCollection.php';

/**
 * Main HTMLPP class
 * @name HTMLPP
 * @package HTMLPP
 * @subpackage HTMLPP
 */
class HTMLPP
{
	/**
	 * HTML code
	 * @var		string
	 * @access	public
	 */
	var $code="";
	/**
	 * Document object
	 * @var		object
	 * @access	public
	 */
	var $document=null;
	/**
	 * Load HTML code from string.
	 * @param 	string		$HTML 	HTML code
	 * @access	public
	 */
	function loadHTML($HTML="")
	{
		$this->code=$HTML;
	}
	/**
	 * Load HTML code from a file.
	 * @param 	string		$path 		HTML file path
	 * @param 	bool		$useCurl 	If it's true(default) and the script cannot load the content directly, it tries to get the content with curl functions
	 * @param 	array		$curlOpt 	An array (format: option=>value) for curl settings
	 * @access	public
	 */
	function loadHTMLFile($path="",$useCurl=true,$curlOpt=array())
	{
		$HTML=@file_get_contents($path);
		if($HTML===false && $useCurl && function_exists("curl_init"))
		{
			$session=curl_init($path);
			if(count($curlOpt))
				foreach($curlOpt as $k=>$v)
					curl_setopt($session,$k,$v);
			$HTML=curl_exec($session);
			curl_close($session);
		}
		$this->code=$HTML;
	}
	/**
	 * Strips all the comments in the code. This must be used before the parsing with the getDocument function
	 * @access	public
	 */
	function stripComments()
	{
		static $parsevar;
		if(!isset($parsevar)) $parsevar=get_class_vars("HTMLParser");		
		$this->code=preg_replace($parsevar["_comment_capture"],"",$this->code);
	}
	/**
	 * Return the document object
	 * @return	object	reference to the document object
	 * @access	public
	 */
	function &getDocument()
	{	
		//If the document is already parsed return it
		if($this->document) return $this->document;	
		//Create the document instance
		$this->document=new HTMLDocument();
		$this->document->ownerDocument=& $this->document;
		//Parse the code
		$this->document->setInnerHTML($this->code);		
		return $this->document;
	}
	/**
	 * Return document HTML code as a string
	 * @return	string	document html code
	 * @access	public
	 */
	function render()
	{	
		return $this->document ? $this->document->getInnerHTML() : $this->code;
	}
}
?>