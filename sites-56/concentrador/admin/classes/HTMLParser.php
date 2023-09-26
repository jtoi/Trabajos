<?php
/**
 * HTML Parser class
 * @name HTMLParser
 * @package HTMLPP
 * @subpackage HTMLParser
 */
class HTMLParser
{
	/**
	 * List of all html tags that don't require enclosures
	 * @var		array
	 * @access	protected
	 */	
	var $_HTML_TAGS_WITHOUT_ENCLOSURES=array(
		"area","base","basefont","br","command","frame","hr","img","input","isindex","link","meta","output","param","source","spacer","wbr"
	);
	/**
	 * List of tags with optional enclosures. All this tags (if they're not closed) will be automatically closed at the end of the parent element or when there are some other tags opened after them.
	 * @var		array
	 * @access	protected
	 */	
	var $_HTML_TAGS_OPTIONAL_ENCLOSURES=array(
		"p"=>array("address","article","aside","blockquote","dir","div","dl","fieldset","footer","form","h1","h2","h3","h4","h5","h6","header","hgroup","hr","menu","nav","ol","p","pre","section","table","ul"), 
		"dd"=>array("dt"), "dt"=>array("dd"), "li"=>array(), "rt"=>array("rp"), "rp"=>array("rt"), "optgroup"=>array(), "option"=>array("optgroup"), 
		"colgroup"=>array("tr"), "thead"=>array("tbody","tfoot"), "tbody"=>array("thead","tfoot"), "tfoot"=>array("tbody","thead"), "tr"=>array(), 
		"td"=>array("th"), "th"=>array("td"),"head"=>array("body"),"body"=>array()
	);
	/**
	 * List of tags with optional enclosures that can contain other children with the same tag name.
	 * @var		array
	 * @access	protected
	 */	
	var $_HTML_TAGS_OPTIONAL_ENCLOSURES_CONTAINERS=array(
		"dd"=>array("dl"), "dt"=>array("dl"), "li"=>array("ul","ol"), "rt"=>array("ruby"), "rp"=>array("ruby"),"thead"=>array("table"), "tbody"=>array("table"), 
		"tfoot"=>array("table"), "tr"=>array("table"), "td"=>array("table"), "th"=>array("table")
	);
	/**
	 * Regular expression for matching open tags. From: http://kev.coolcavemen.com/2007/03/ultimate-regular-expression-for-html-tag-parsing-with-php/
	 * @var		regex
	 * @access	protected
	 *///Old: #<(\w+)\s*([\w-]+(?:=(?:(['\"])(?:\\?[\s\S])*?\3)|(?:=[^\s>]+))?\s*)*\s*>#
	var $_tag_capture="#<([\w\d]+)((?:\s+(?:\w|\w[\w-]*\w)(?:\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)(\/?)>#s";
	/**
	 * Regular expression for matching determinate open or closed tags
	 * @var		regex
	 * @access	protected
	 *///Old: #<(/?)TAG\s*(?:[\w-]+(?:=(?:(['\"])(?:\\?[\s\S])*?\2)|(?:=[^\s>]+))?\s*)*\s*>#i
	var $_equals_tags_capture="#<(/?)TAG(?:(?:\s+(?:\w|\w[\w-]*\w)(?:\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>#is";
	/**
	 * Regular expression for matching attributes and their value
	 * @var		regex
	 * @access	protected
	 */
	var $_attributes_capture="#([\w\-]+)(?:\s*=\s*(?:(?:(['\"])((?:\\\\?[\s\S])*?)\\2)|([^\s'\"]+)))?#s";
	/**
	 * Regular expression for matching style properties and their value
	 * @var		regex
	 * @access	protected
	 */
	var $_styles_capture="#([\w-]+)\s*:\s*([^;]+)\s*(;)?#s";
	/**
	 * Regular expression for matching the doctype
	 * @var		regex
	 * @access	protected
	 */
	var $_doctype_capture="#<!DOCTYPE\s*([^\s>]+)(?:\s*\w+)?(?:\s*['\"]([^'\"]+)['\"])?(?:\s*['\"]([^'\"]+)['\"])?\s*>#i";
	/**
	 * Regular expression for matching comments
	 * @var		regex
	 * @access	protected
	 */
	var $_comment_capture="#<!--(.*?)-->#s";
	/**
	 * The structure result
	 * @var		array
	 * @access	protected
	 */
	var $_structure=array();
	/**
	 * The code to be parsed
	 * @var		string
	 * @access	protected
	 */
	var $_code="";
	/**
	 * Class constructor
	 * @param 	string		$HTML	HTML code
	 * @param 	bool		$asText	If is set to true the HTML will be parsed as simple text and returned as text node
	 * @access	public
	 */
	function HTMLParser($HTML="")
	{
		$this->_code=trim($HTML);
	}
	/**
	 * Function to parse the code
	 * @access	public
	 */
	function _parse()
	{
		if(!$this->_code) return;
		//Search for tags. If no result is found then add the code as a text node
		if(!preg_match($this->_tag_capture,$this->_code,$match,PREG_OFFSET_CAPTURE) || (isset($match[0][1]) && $match[0][1]!==0))
		{
			if(!count($match)) return $this->_add("",$this->_code);
			$this->_add("",substr($this->_code,0,$match[0][1]));
		}
		$tag=strtolower($match[1][0]);
		$attributes=$match[2][0];
		$index=$match[0][1];
		$nextstart=$index+strlen($match[0][0]);
		$noClose=false;
		$HTMLength=strlen($this->_code);
		$closelength=0;
		//If the tag doesn't require the closure, just add the element and skip this part
		if($match[3][0]!="/" && !in_array($tag,$this->_HTML_TAGS_WITHOUT_ENCLOSURES))
		{						
			//Look for the same tag (opened and closed) and loop through them to get the right closure tag
			$count=preg_match_all(str_replace("TAG",$tag,$this->_equals_tags_capture),$this->_code,$matches,PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE,$nextstart);
			$openContext=0;
			$found=false;
			$closedindex=$HTMLength+1;
			if($count)
				foreach($matches[1] as $k=>$tagtype)
				{
					if($tagtype[0]!="/") $openContext++;
					elseif($openContext!==0) $openContext--;
					else{
						$closedindex=$matches[0][$k][1];
						$closelength=strlen($matches[0][$k][0]);
						$found=true;
						break;
					}
				}
			//If the tag isn't closed and it's an element with optional closing tag execute this code
			if(!$found && isset($this->_HTML_TAGS_OPTIONAL_ENCLOSURES[$tag]))
			{
				$autoClosing=$this->_HTML_TAGS_OPTIONAL_ENCLOSURES[$tag];
				$autoClosing[]=$tag;
				$searchFrom=$nextstart;
				//Check if this tag contains a "container" like the <ul> for the <li>
				if(isset($this->_HTML_TAGS_OPTIONAL_ENCLOSURES_CONTAINERS[$tag]))
				{
					$regexp="#.*?".str_replace("#<(/?)TAG","<(".implode("|",array_merge($autoClosing,$this->_HTML_TAGS_OPTIONAL_ENCLOSURES_CONTAINERS[$tag])).")",$this->_equals_tags_capture);
					$hasContainerInside=preg_match($regexp,$this->_code,$ContainerInside,PREG_OFFSET_CAPTURE,$searchFrom);
					//Loop all the containers and find the end tag of the last one, after that the current element will be closed
					while($hasContainerInside && in_array($ContainerInside[1][0],$this->_HTML_TAGS_OPTIONAL_ENCLOSURES_CONTAINERS[$tag]))
					{
						$subCont=preg_match_all(str_replace("TAG",$ContainerInside[1][0],$this->_equals_tags_capture),$this->_code,$matches,PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE,$ContainerInside[0][1]+strlen($ContainerInside[0][0]));
						$openSubContext=0;
						if($subCont)
							foreach($matches[1] as $k=>$tagtype)
							{
								if($tagtype[0]!="/") $openSubContext++;
								elseif($openSubContext!==0) $openSubContext--;
								else{
									$searchFrom=$matches[0][$k][1]+strlen($matches[0][$k][0]);
									break;
								}
							}
						$hasContainerInside=preg_match($regexp,$this->_code,$ContainerInside,PREG_OFFSET_CAPTURE,$searchFrom);
					}
				}
				//Search for every start tag that can close the current tag or another closing tag of the current element, and take the first result
				$count=preg_match(str_replace(array("#<(/?)TAG","#i"),array("#<(?:".implode("|",$autoClosing).")","|(</".$tag.">)#is"),$this->_equals_tags_capture),$this->_code,$closingTag,PREG_OFFSET_CAPTURE,$searchFrom);
				//If there are no results close it at the end of the code, otherwise close it at the index of the first matched tag
				$closedindex=!$count ? $HTMLength : $closingTag[0][1];
				if($count && isset($closingTag[1]) && $closingTag[1][0]) $closelength=strlen($closingTag[1][0]);
			}
			elseif(!$found) $closedindex=$HTMLength;
		}
		else
		{
			$noClose=true;
			$closedindex=$index;
			$closelength=strlen($match[0][0]);
		}
		//If there's other code to be parsed re-call this function
		if($closedindex<=$HTMLength || $noClose)
		{
			//Get the tag inner code and add the element. If the tag doesn't require the closure the code will be an empty string
			$code=$noClose ? "" : substr($this->_code,$nextstart,$closedindex-$nextstart);
			$this->_add($tag,$attributes,$code);
			//Delete the code that has been already parsed and call this function
			$this->_code=substr($this->_code,$closedindex+$closelength);
			if($this->_code) $this->_parse();
		}
	}
	/**
	 * Add an element to the parser structure
	 * @param 	string		$tag		element's tag name
	 * @param 	string		$attributes	element's attributes string
	 * @param 	string		$code		HTML code inside the element
	 * @access	private
	 */
	function _add($tag="",$attributes="",$code="")
	{
		$element=new stdClass;
		$element->tag=$tag;
		$element->attributes=$attributes;
		$element->code=$code;
		$this->_structure[]=$element;
	}
	/**
	 * Get the parser structure
	 * @return	array	parser structure
	 * @access	public
	 */
	function getStructure()
	{
		return $this->_structure;
	}
	/**
	 * Convert a string into camel case format
	 * @param 	string	$str	string
	 * @return	string	the camel cased string
	 * @static
	 * @access	public
	 */
	 public static function camelCase($str)
	 {
		return is_string($str) ? preg_replace_callback("#[^\w]+(\w)?#",create_function('$matches','return isset($matches[1]) && $matches[1] ? strtoupper($matches[1]) : "";'),trim(strtolower($str))) : $str;
	 }
	 /**
	 * Convert a string into hyphenate format
	 * @param 	string	$str	string
	 * @return	string	the hyphenated string
	 * @static
	 * @access	public
	 */
	 public static function hyphenate($str){
		return is_string($str) ? strtolower(preg_replace("#([A-Z])#","-\\1",$str)) : $str;
	 }
}
?>