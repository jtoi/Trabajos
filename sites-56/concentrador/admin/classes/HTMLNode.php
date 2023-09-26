<?php
//Node type constants
define("ELEMENT_NODE",1);
define("TEXT_NODE",3);
define("COMMENT_NODE",8);
define("DOCUMENT_NODE",9);
define("DOCUMENT_TYPE_NODE",10);

/**
 * Html nodes base class
 * @name HTMLNode
 * @package HTMLPP
 * @subpackage HTMLNode
 */
class HTMLNode
{	
	/**
	 * Child nodes collection
	 * @var		array
	 * @access	public
	 */
	var $childNodes=array();
	/**
	 * Parent node object
	 * @var		object
	 * @access	public
	 */
	var $parentNode=null;
	/**
	 * Previous node
	 * @var		object
	 * @access	public
	 */
	var $previousSibling=null;
	/**
	 * Next node
	 * @var		object
	 * @access	public
	 */
	var $nextSibling=null;
	/**
	 * First child node
	 * @var		object
	 * @access	public
	 */
	var $firstChild=null;
	/**
	 * Last child node
	 * @var		object
	 * @access	public
	 */
	var $lastChild=null;
	/**
	 * Reference to the document that contains the element
	 * @var		object
	 * @access	public
	 */
	var $ownerDocument=null;
	/**
	 * List of attributes
	 * @var		object
	 * @access	public
	 */
	var $attributes=null;
	/**
	 * List of styles properties
	 * @var		object
	 * @access	public
	 */
	var $style=null;
	/**
	 * Element's index in its parent childNodes collection
	 * @var		int
	 * @access	public
	 */
	var $index=null;
	/**
	 * Node type
	 * @var		int
	 * @access	public
	 */
	var $nodeType=null;
	/**
	 * Element node name
	 * @var		string
	 * @access	public
	 */
	var $nodeName=null;
	/**
	 * Element node value
	 * @var		string
	 * @access	public
	 */
	var $nodeValue=null;
	/**
	 * Element tag name
	 * @var		string
	 * @access	public
	 */
	var $tagName=null;
	/**
	 * Text inside the elements. This will be implemented only on text nodes
	 * @var		string
	 * @access	public
	 */
	var $textContent="";
	/**
	 * Element internal ID. This is used for recognize the elements, so it mustn't be changed
	 * @var		int
	 * @access	protected
	 */
	var $_internalID=null;
	/**
	 * Class constructor
	 * @access	public
	 */
	function HTMLNode()
	{
		$this->style=new stdClass;
		$this->attributes=new stdClass;
		//Set the internal id of the node
		static $internal;
		if(!isset($internal)) $internal=0;	
		$this->_internalID=$this->nodeType."-".$internal;
		$internal++;
	}
	/**
	 * Set multiple element's attributes
	 * @param 	array|string|object		$attributes	Array or object of attributes in attribute=>value format or html attribute string
	 * @return	object					reference to the current element
	 * @access	public
	 */
	function setAttributes($attributes=array())
	{
		static $parsevar;
		if(!isset($parsevar)) $parsevar=get_class_vars("HTMLParser");
		if(is_string($attributes)) 
		{
			$attfound=preg_match_all($parsevar["_attributes_capture"],$attributes,$attributes_f,PREG_PATTERN_ORDER);		
			$attributes=array();
			if($attfound)
				foreach($attributes_f[1] as $k=>$key)
					//Get the attribute value: value inside quotes? take the 3rd match. Value with no quotes? take the 4th match. The 0 index match contains "="? Empty string. Otherwise parse no value as true
					$attributes[$key]=strlen(trim($attributes_f[3][$k]))>0 ? $attributes_f[3][$k] : ($attributes_f[4][$k] ? $attributes_f[4][$k] : ( strpos($attributes_f[0][$k],"=")!==false ? "" : true));
		}
		if((is_array($attributes) && count($attributes))||(is_object($attributes) && count(get_object_vars($attributes))))
			foreach($attributes as $name=>$val)
				$this->setAttribute($name,$val);
		return $this;
	}
	/**
	 * Set an element's attribute
	 * @param 	string		$name	Attribute name
	 * @param 	string		$val	Attribute value
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function setAttribute($name,$val=true)
	{
		$name=HTMLParser::camelCase($name);
		if($name=="style") $this->setStyles($val);
		else $this->attributes->$name=$val;
		return $this;
	}
	/**
	 * Return true if the attribute exists
	 * @param 	string		$name	Attribute name
	 * @return	bool		True if the attribute exists, false if not exists
	 * @access	public
	 */
	function hasAttribute($name)
	{
		$name=HTMLParser::camelCase($name);
		if($name=="style") return $this->hasStyle($name);
		else return isset($this->attributes->$name);
	}
	/**
	 * Get an element's attribute
	 * @param 	string		$name	Attribute name
	 * @return	string		Attribute value
	 * @access	public
	 */
	function getAttribute($cname)
	{
		$name=HTMLParser::camelCase($cname);
		return $this->hasAttribute($cname) ? $this->attributes->$name : null;
	}
	/**
	 * Remove an attribute
	 * @param 	string		$name	Attribute name
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function removeAttribute($name)
	{
		$name=HTMLParser::camelCase($name);
		if($name!="style") unset($this->attributes->$name);
		else $this->emptyStyle();
		return $this;
	}
	/**
	 * Return true if the element has at least one attribute
	 * @return	bool		True if the element has attributes, otherwise false
	 * @access	public
	 */
	function hasAttributes()
	{
		return ( count(get_object_vars($this->attributes))>0 );
	}
	/**
	 * Empty the attributes object
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function emptyAttributes()
	{
		$this->attributes=new stdClass;
		return $this;
	}
	/**
	 * Set multiple element's style properties
	 * @param 	array|string|object		$style	Array or object of style properties in style=>value format or css style string
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function setStyles($styles=array())
	{
		static $parsevar;
		if(!isset($parsevar)) $parsevar=get_class_vars("HTMLParser");

		if(is_string($styles)) 
		{
			$styfound=preg_match_all($parsevar["_styles_capture"],$styles,$style_f,PREG_PATTERN_ORDER);		
			$styles=array();
			if($styfound)
				foreach($style_f[1] as $k=>$key)
					$styles[$key]=$style_f[2][$k];
		}
		if((is_array($styles) && count($styles))||(is_object($styles) && count(get_object_vars($styles))))
			foreach($styles as $name=>$val)
				$this->setStyle($name,$val);
		return $this;
	}
	/**
	 * Set a style property
	 * @param 	string		$name	Style property name
	 * @param 	string		$val	Style property value
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function setStyle($name,$val)
	{
		$name=HTMLParser::camelCase($name);
		$this->style->$name=$val;
		return $this;
	}
	/**
	 * Return true if the style property exists
	 * @param 	string		$name	Style property name
	 * @return	bool		True if the property exists, false if not exists
	 * @access	public
	 */
	function hasStyle($name)
	{
		$name=HTMLParser::camelCase($name);
		return isset($this->style->$name);
	}
	/**
	 * Return true if the element has at least one style property
	 * @return	bool		True if the element has style properties, otherwise false
	 * @access	public
	 */
	function hasStyles()
	{
		return ( count(get_object_vars($this->style))>0 );
	}
	/**
	 * Return a style property value
	 * @param 	string		$name	Style property name
	 * @return	string		Property value
	 * @access	public
	 */
	function getStyle($cname)
	{
		$name=HTMLParser::camelCase($cname);
		return $this->hasStyle($cname) ? $this->style->$name : null;
	}
	/**
	 * Remove a style property value
	 * @param 	string		$name	Style property name
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function removeStyle($name)
	{
		$name=HTMLParser::camelCase($name);
		unset($this->style->$name);
		return $this;
	}
	/**
	 * This function works exactly like setStyles, but it empties the style object before insert new style properties
	 * @param 	string		$style	Style properties in css formatted string
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function setCssText($style="")
	{
		$this->emptyStyle();
		$this->setStyles($style);
		return $this;
	}
	/**
	 * Return a css formatted string that contains the css properties of the current element
	 * @return	string		Style properties in css formatted string
	 * @access	public
	 */
	function getCssText()
	{
		$vars=get_object_vars($this->style);
		if(!count($vars)) return "";
		$return=array();
		foreach($vars as $k=>$v)
			$return[]=HTMLParser::hyphenate($k).":".$v;
		return implode(";",$return);
	}
	/**
	 * Empty the style object
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function emptyStyle()
	{
		$this->style=new stdClass;
		return $this;
	}
	/**
	 * Set element's innerHTML
	 * @param 	string		$HTML	HTML code
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function setInnerHTML($HTML="")
	{
		static $parsevar;
		if(!isset($parsevar)) $parsevar=get_class_vars("HTMLParser");
		
		$this->emptyChildNodes();
		if(!trim($HTML))
		{
			$this->textContent="";
			return;
		}	
		//Set only the text content and stop the parsing if this is a special element and its content must not be parsed
		elseif(in_array($this->tagName,array("script","style")))
		{
			$this->textContent=$HTML;
			return;
		}
		//The code inside the textarea is its value
		elseif($this->tagName=="textarea")
		{
			$this->attributes->value=$HTML;
			return;
		}
		$HTML=preg_replace_callback($parsevar['_comment_capture'],create_function('$match','return "<comment>".htmlentities($match[1])."</comment>";'),$HTML);
		$parser=new HTMLParser($HTML);
		$parser->_parse();
		$structure=$parser->getStructure();
		if(count($structure))
		{
			foreach($structure as $child)
			{
				$functype="create";
				$appendToDifferent=false;
				switch($child->tag){
					case "":		$functype.="TextNode"; 
									$arg=$child->attributes;
									break;
					case "comment": $functype.="Comment"; 
									$arg=html_entity_decode($child->code);
									break;
					default: 		$functype.="Element";
									$arg=$child->tag;									
									switch($arg)
									{
										//Title, base and meta tags must be append to the head
										case "title": case "base": case "meta":
										if($this->tagName!="head")
										{
											$appendToDifferent=true;
											$DIFF=& $this->ownerDocument->childNodes[0]->childNodes[0];
										}
										break;
										//Th and td elements need to be contained in a tr element
										case "th": case "td":
										if($this->tagName!="tr")
										{
											$appendToDifferent=true;
											if(!isset($DIFF) || $DIFF->tagName!="tr")
											{
												$DIFF=& $this->ownerDocument->createElement("tr");
												if(!in_array($this->tagName,array("tbody","thead","tfoot")))
												{
													if(!isset($TBODY))
													{
														$TBODY=& $this->ownerDocument->createElement("tbody");
														$this->appendChild($TBODY);
													}
													$TBODY->appendChild($DIFF);
												}
												else $this->appendChild($DIFF);
											}
										}
										break;
										//Tr must be contained into a tbody, thead or tfoot element
										case "tr":
										if(!in_array($this->tagName,array("tbody","thead","tfoot")))
										{											
											$appendToDifferent=true;
											if(isset($TBODY)) $DIFF=& $TBODY;
											elseif(!isset($DIFF) || $DIFF->tagName!="tbody")
											{
												$DIFF=& $this->ownerDocument->createElement("tbody");
												$TBODY=& $DIFF;
												$this->appendChild($DIFF);
											}
										}
										break;
									}
									break;						
				}
				$element=& $this->ownerDocument->$functype($arg);
				if($functype=="createElement" && $child->attributes) $element->setAttributes($child->attributes);
				if(!$appendToDifferent) $this->appendChild($element);
				else $DIFF->appendChild($element);
				//Stop if the element is a text node or a comment, otherwise set its inner html
				if($element->nodeType===TEXT_NODE || $element->nodeType===COMMENT_NODE) continue;
				$element->setInnerHTML($child->code);
			}
		}
		return $this;
	}
	/**
	 * Get the HTML code inside the node
	 * @return	string	HTML code
	 * @access	public
	 */
	function getInnerHTML()
	{
		$string="";
		if(count($this->childNodes))
			foreach($this->childNodes as $childs)
				$string.=$childs->getOuterHTML();
		return $string;
	}
	/**
	 * This function is equal to getInnerHTML with the difference that also the current node HTML code is shown
	 * @return	string	HTML code
	 * @access	public
	 */
	function getOuterHTML()
	{
		static $parsevar;
		if(!isset($parsevar)) $parsevar=get_class_vars("HTMLParser");
		$string=$closure="";
		switch($this->nodeType)
		{
			case ELEMENT_NODE:		$NOCLOSURE=in_array($this->tagName,$parsevar["_HTML_TAGS_WITHOUT_ENCLOSURES"]);
									$string='<'.$this->tagName;
									$innnerContent=$this->textContent;
									if($this->hasStyles()) $string.=' style="'.$this->getCssText().'"';
									if($this->hasAttributes())
									{
										$attributes=array();
										foreach($this->attributes as $k=>$att)
										{
										  if($k=="value" && $this->tagName=="textarea")
											{
												$innnerContent=$att;
												continue;
											}
											$isbool=is_bool($att);
											if($isbool && !$att) continue;
											$attributes[]=HTMLParser::hyphenate($k).($isbool ? '' : '="'.preg_replace("#(\\?\")#","\\\1",$att).'"');
										}
										$string.=" ".implode(" ",$attributes);
									}
									$string.=($NOCLOSURE ? '/' : '').'>'.$innnerContent;
									$closure=$NOCLOSURE ? '' : '</'.$this->tagName.'>';
									break;
			case TEXT_NODE: 		$string=$this->textContent; 
									break;
			case COMMENT_NODE: 		$string='<!--'.$this->textContent; 
									$closure='-->';
									break;
			case DOCUMENT_TYPE_NODE:$string=trim($this->name.$this->publicId.$this->systemId) ? '<!DOCTYPE '.$this->name.' PUBLIC "'.$this->publicId.'" "'.$this->systemId.'">' : '';
									break;
		}
		return $string.$this->getInnerHTML().$closure;
	}
	/**
	 * Return the text content of the element and its children
	 * @return	string		Text content
	 * @access	public
	 */
	function getTextContent()
	{
		$string=$this->nodeType===TEXT_NODE ? $this->textContent : "";
		if(count($this->childNodes))
			foreach($this->childNodes as $child)
				$string.=$child->getTextContent();
		return $string;
	}
	/**
	 * Return the text content of the current element and its siblings
	 * @return	string		Text content
	 * @access	public
	 */
	function getOuterTextContent()
	{
		return $this->parentNode ? $this->parentNode->getTextContent() : $this->getTextContent();
	}
	/**
	 * Get the child node at the specified position or null if there's no element at that index
	 * @param 	int			$index	Index of the element
	 * @return	object		html element
	 * @access	public
	 */
	function &getChildAt($index=0)
	{
		if(isset($this->childNodes[$index])) $element=& $this->childNodes[$index];
		else $element=null;
		return $element;
	}
	/**
	 * Get elements inside the current node with the given tag name
	 * @param 	string		$tag	tag name
	 * @return	object		html collection
	 * @access	public
	 */
	function &getElementsByTagName($tag="*")
	{
		$collection=new HTMLCollection($this,$tag);
		return $collection;
	}
	/**
	 * Get elements inside the current node with the given class name
	 * @param 	string		$class	class name
	 * @return	object		html collection
	 * @access	public
	 */
	function &getElementsByClassName($class)
	{
		$collection=new HTMLCollection($this,$class[0]=="." ? $class : ".".$class);
		return $collection;
	}
	/**
	 * Get the first matched element with the given id
	 * @param 	string		$id		id of the element
	 * @return	object		html element or null if no element is found
	 * @access	public
	 */
	function &getElementById($id)
	{
		$collection=new HTMLCollection($this,$id[0]=="#" ? $id : "#".$id);
		if(count($collection->elements)) $element=& $collection->elements[0];
		else $element=null;
		return $element;
	}
	/**
	 * Get elements inside the current element that match the given css selector
	 * @param 	string		$selector		Selector string
	 * @return	object		html collection
	 * @access	public
	 */
	function &getElementsBySelector($selector="*")
	{
		$collection=new HTMLCollection($this,$selector);
		return $collection;
	}
	/**
	 * Return true if the given element and the current are the same node.
	 * @param 	object		$element 	element for the comparison
	 * @return	bool		True if the element passed as parameter and the current node are the same element, 	otherwise false
	 * @access	public
	 */
	function isSameNode(& $element)
	{
		return ( $element->_internalID===$this->_internalID && $element->tagName===$this->tagName && $element->nodeName===$this->nodeName && $element->nodeType===$this->nodeType );
	}
	/**
	 * Return a copy of the current element
	 * @param 	bool		$deep 	if it's true childnodes will be cloned too, otherwise only the element is cloned
	 * @return	bool		The element's copy
	 * @access	public
	 */
	function &cloneNode($deep=false)
	{
		$element=new stdClass;
		switch($this->nodeType)
		{
			case ELEMENT_NODE: $element=& $this->ownerDocument->createElement($this->tagName);break;
			case TEXT_NODE: $element=& $this->ownerDocument->createTextNode($this->textContent);break;
			case COMMENT_NODE: $element=& $this->ownerDocument->createComment($this->textContent);break;
			case DOCUMENT_TYPE_NODE: $element=& $this->ownerDocument->createDocumentType($this->name,$this->publicId, $this->systemId);break;
		}
		$element->attributes=$this->attributes;
		$element->style=$this->style;
		$element->textContent=$this->textContent;
		if($deep && $this->hasChildNodes())
			for($i=0;$i<count($this->childNodes);$i++)
			{
				$node=& $this->childNodes[$i]->cloneNode($deep);
				$element->appendChild($node);
			}
		return $element;
	}
	/**
	 * Return true if the element has at least one child node
	 * @return	bool		True if the element has child nodes, otherwise false
	 * @access	public
	 */
	function hasChildNodes()
	{
		return ( count($this->childNodes)>0 );
	}
	/**
	 * Add an element at the end of the current element child nodes collection
	 * @param 	object		$element	HTML element object
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function appendChild(&$element)
	{
		return $this->insertChildAt($element);
	}
	/**
	 * Add the current element at the beginning of the child nodes collection of the element passed in as argument 
	 * @param 	object		$element	HTML element object
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function appendTo(&$element)
	{
		$element->appendChild($this);
		return $this;
	}
	/**
	 * Add an element at the beginning of the current element child nodes collection
	 * @param 	object		$element	HTML element object
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function prependChild(&$element)
	{
		return $this->insertChildAt($element,0);
	}
	/**
	 * Add the current element at the end of the child nodes collection of the element passed in as argument 
	 * @param 	object		$element	HTML element object
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function prependTo(&$element)
	{
		$element->prependChild($this);
		return $this;
	}
	/**
	 * Insert the element passed as argument in the current element child nodes collection at the specified index
	 * @param 	object		$element	HTML element object
	 * @param 	int			$index		Index for the insertion, if no value is passed the element will be injected at the end of the child nodes array
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function insertChildAt(&$element,$index=null)
	{
		return $this->_DOM_Manipulation($element,"add",$index);
	}
	/**
	 * Insert the current element in the child node collection of the element passed as argument at the specified index
	 * @param 	object		$element	HTML element object
	 * @param 	int			$index		Index for the insertion, if no value is passed the element will be injected at the end of the child nodes array
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function insertAt(&$element,$index=null)
	{
		$element->insertChildAt($this,$index);
		return $this;
	}
	/**
	 * Insert the second element before the first in the current element child nodes collection
	 * @param 	object		$element	HTML element object
	 * @param 	object		$node		HTML element object to insert
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function insertBefore(&$element,&$node)
	{
		return $this->insertChildAt($node,$element->index);
	}
	/**
	 * Insert the current element before the element passed as argument in its parent node children collection
	 * @param 	object		$element	HTML element object
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function insertBeforeNode(&$element)
	{
		$element->parentNode->insertBefore($element, $this);
		return $this;
	}
	/**
	 * Insert the second element after the first in the current element child nodes collection
	 * @param 	object		$element	HTML element object
	 * @param 	object			$node		HTML element object to insert
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function insertAfter(&$element,&$node)
	{
		return $this->insertChildAt($node,$element->index+1);
	}
	/**
	 * Insert the current element after the element passed as argument in its parent node children collection
	 * @param 	object		$element	HTML element object
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function insertAfterNode(&$element)
	{
		$element->parentNode->insertAfter($element, $this);
		return $this;
	}
	/**
	 * Replace the first element with the second in the current element child nodes collection
	 * @param 	object		$element		HTML element object
	 * @param 	object		$replacement	HTML element object used to replace the first
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function replaceChild(&$element,&$replacement)
	{
		if(!$this->isSameNode($element->parentNode)) return;		
		$index=$element->index;
		$this->removeChild($element);
		return $this->insertChildAt($replacement,$index);
	}
	/**
	 * In the parent node children collection of the element passed as a argument replace it with the current element
	 * @param 	object		$element		HTML element object
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function replace(&$element)
	{
		$element->parentNode->replaceChild($element,$this);
		return $this;
	}
	/**
	 * Remove a child node
	 * @param 	object		$element	Node to be removed
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function removeChild(&$element)
	{
		return $this->_DOM_Manipulation($element,"remove");
	}
	/**
	 * Remove the child at the specified index
	 * @param 	int		$index	Index of the node that must be removed
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function removeChildAt($index=0)
	{		
		if(isset($this->childNodes[$index]))
		{
			$element=& $this->childNodes[$index];
			$this->_DOM_Manipulation($element,"remove");
		}
		return $this;
	}
	/**
	 * Remove the current element from parent's child node collection
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function remove()
	{
		$this->parentNode->removeChild($this);
		return $this;
	}
	/**
	 * Remove all element's child nodes
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function emptyChildNodes()
	{
		return $this->_DOM_Manipulation($this,"empty");
	}
	/**
	 * Base function for DOM manipulation operations
	 * @param 	object		$element	HTML element object
	 * @param	string		$operation	Operation type
	 * @param	int			$index		Index for the operation
	 * @return	object		reference to the current element
	 * @access	protected
	 */
	function _DOM_Manipulation(& $element,$operation="add",$index=null)
	{		
		switch($operation)
		{
			case "add": 	if($element->parentNode!==null) $element->remove();
							$element->parentNode=& $this;
							if($index===null || $index>count($this->childNodes)) $index=count($this->childNodes);
							$element->index=$index;
							if($index<count($this->childNodes))
								for($i=count($this->childNodes)-1;$i>=$index;$i--)
								{
									$this->childNodes[$i+1]=& $this->childNodes[$i];
									$this->childNodes[$i+1]->index=$i+1;
								}
							if(isset($this->childNodes[$index-1]))
							{
								$element->previousSibling=& $this->childNodes[$index-1];
								$this->childNodes[$index-1]->nextSibling=& $element;
							}
							if(isset($this->childNodes[$index+1]))
							{
								$element->nextSibling=& $this->childNodes[$index+1];
								$this->childNodes[$index+1]->previousSibling=& $element;
							}
							$this->childNodes[$index]=& $element;
							break;
			case "remove":  if($index===null) $index=$element->index;
							if($index!==null && isset($this->childNodes[$index]) && $this->childNodes[$index]->isSameNode($element))
							{
								if(count($this->childNodes)==1) $this->childNodes=array();
								else
								{
									if($index!==0)
									{
										if(isset($this->childNodes[$index+1])){
											$this->childNodes[$index-1]->nextSibling=& $this->childNodes[$index+1];
											$this->childNodes[$index+1]->previousSibling=& $this->childNodes[$index-1];
										}
										else $this->childNodes[$index-1]->nextSibling=null;
									}
									else $this->childNodes[$index+1]->previousSibling=null;
									if(isset($this->childNodes[$index+1]))
										for($i=$index;$i<count($this->childNodes)-1;$i++)
										{
											$this->childNodes[$i]=& $this->childNodes[$i+1];
											$this->childNodes[$i]->index=$i;
										}
									unset($this->childNodes[count($this->childNodes)-1]);
								}
								unset($element->parentNode);
								unset($element->previousSibling);
								unset($element->nextSibling);
								$element->parentNode=$element->previousSibling=$element->nextSibling=$element->index=null;								
							}							
							break;
			case "empty":	if(count($element->childNodes))
							{
								for($i=0;$i<count($element->childNodes);$i++)
								{
									$node=& $element->childNodes[$i];
									unset($node->parentNode);
									unset($node->previousSibling);
									unset($node->nextSibling);
									$node->parentNode=$node->previousSibling=$node->nextSibling=$node->index=null;
								}
								$element->childNodes=array();
							}
							break;
		}
		unset($this->firstChild);
		unset($this->lastChild);
		if(count($this->childNodes))
		{
			$this->firstChild=& $this->childNodes[0];
			$this->lastChild=& $this->childNodes[count($this->childNodes)-1];
		}
		else $this->firstChild=$this->lastChild=null;
		return $this;
	}
}

/**
 * HTML document class
 * @name HTMLDocument
 * @package HTML
 * @subpackage HTMLDocument
 */
class HTMLDocument extends HTMLNode
{		
	/**
	 * Element node Name
	 * @var		string
	 * @access	public
	 */
	var $nodeName="#document";
	/**
	 * Node type
	 * @var		int
	 * @access	public
	 */
	var $nodeType=DOCUMENT_NODE;
	/**
	 * Doctype object
	 * @var		object
	 * @access	public
	 */
	var $doctype=null;
	/**
	 * Head element
	 * @var		object
	 * @access	public
	 */
	var $head=null;
	/**
	 * Body element
	 * @var		object
	 * @access	public
	 */
	var $body=null;
	/**
	 * Class constructor
	 * @access	public
	 */
	function HTMLDocument()
	{		
		$this->ownerDocument=& $this;
		parent::HTMLNode();
	}
	/**
	 * Set element's innerHTML
	 * @param 	string		$HTML	HTML code
	 * @return	object		reference to the current element
	 * @access	public
	 */
	function setInnerHTML($HTML="")
	{
		static $parsevar;
		if(!isset($parsevar)) $parsevar=get_class_vars("HTMLParser");
		//Strip doctype
		preg_match($parsevar["_doctype_capture"],$HTML,$matches);
		$matches=array_pad($matches,4,"");
		$HTML=preg_replace($parsevar["_doctype_capture"],"",$HTML);
		//Strip html tags and re-add them later
		if(preg_match(str_replace("TAG","html",$parsevar["_equals_tags_capture"]),$HTML,$htmlmatch))
		{
			$HTML=preg_replace(str_replace("TAG","html",$parsevar["_equals_tags_capture"]),"",$HTML);
			$openhtml=$htmlmatch[0];
		}
		else $openhtml="<html>";
		//Add the body and the head if they are not present in the code. A frameset can replace the body so it won't insert the body if there's a frameset.
		$hasbody=preg_match(str_replace("TAG","(body|frameset)",$parsevar["_equals_tags_capture"]),$HTML);
		$hashead=preg_match(str_replace("TAG","head",$parsevar["_equals_tags_capture"]),$HTML);
		if(!$hasbody || !$hashead)
			if(!$hasbody && !$hashead) $HTML="<head></head><body>".$HTML."</body>";
			elseif(!$hasbody) $HTML.="<body></body>";
			else $HTML="<head></head>".$HTML;
		//Remove whitespaces between </head> and <body> or <frameset>
		$HTML=preg_replace("#(</head>)\s*(<(?:body|frameset))#i","\\1\\2",$HTML);
		//Add html tags
		$HTML=$openhtml.$HTML."</html>";
		parent::setInnerHTML($HTML);
		$doctype=& $this->createDocumentType($matches[1],$matches[2],$matches[3]);	
		$this->prependChild($doctype);
		//Add body, head and doctype shortcuts
		$this->doctype=& $this->childNodes[0];
		$this->head=& $this->childNodes[1]->childNodes[0];
		$this->body=& $this->childNodes[1]->childNodes[1];
		return $this;
	}
	/**
	 * Create a new element.
	 * @param 	string				$tag 		element tag name
	 * @return	object				new HTML element object
	 * @access	public
	 */
	function &createElement($tag)
	{
		$element=new HTMLElement($tag);
		$element->ownerDocument=& $this;
		return $element;
	}
	/**
	 * Create a new text node.
	 * @param 	string		$text	Text inside the node
	 * @return	object		new HTML comment object
	 * @access	public
	 */
	function &createTextNode($text="")
	{
		$element=new HTMLText($text);
		$element->ownerDocument=& $this;
		return $element;
	}
	/**
	 * Create a new comment node.
	 * @param 	string		$text	Text inside the node
	 * @return	object		new HTML comment object
	 * @access	public
	 */
	function &createComment($text="")
	{
		$element=new HTMLComment($text);
		$element->ownerDocument=& $this;
		return $element;
	}
	/**
	 * Create a new doctype node.
	 * @param 	string		$name 		doctype name
	 * @param 	string		$publicId 	doctype public Id
	 * @param 	string		$systemId 	doctype system Id
	 * @return	object		new HTML doctype object
	 * @access	public
	 */
	function &createDocumentType($name="",$publicId="", $systemId="")
	{
		$element=new HTMLDocumentType($name,$publicId,$systemId);
		$element->ownerDocument=& $this;
		return $element;
	}
}

/**
 * HTML element class
 * @name HTMLElement
 * @package HTMLPP
 * @subpackage HTMLElement
 */
class HTMLElement extends HTMLNode
{
	/**
	 * Node type
	 * @var		int
	 * @access	public
	 */
	var $nodeType=ELEMENT_NODE;
	/**
	 * Element node name
	 * @var		string
	 * @access	public
	 */
	var $nodeName="";
	/**
	 * Class constructor
	 * @param 	string		$tag 	element's tag name
	 * @access	public
	 */
	function HTMLElement($tag="")
	{
		$tag=strtolower(trim($tag));
		parent::HTMLNode();
		$this->tagName=$this->nodeName=$tag;
	}
}

/**
 * HTML text node class
 * @name HTMLText
 * @package HTMLPP
 * @subpackage HTMLText
 */
class HTMLText extends HTMLNode
{
	/**
	 * Node type
	 * @var		int
	 * @access	public
	 */
	var $nodeType=TEXT_NODE;
	/**
	 * Element node name
	 * @var		string
	 * @access	public
	 */
	var $nodeName="#text";
	/**
	 * Element node value
	 * @var		string
	 * @access	public
	 */
	var $nodeValue="";
	/**
	 * Class constructor
	 * @param 	string		$text 	element's textContent
	 * @access	public
	 */
	function HTMLText($text="")
	{
		parent::HTMLNode();
		$this->textContent=$this->nodeValue=$text;
	}
}

/**
 * HTML comment node class
 * @name HTMLComment
 * @package HTMLPP
 * @subpackage HTMLComment
 */
class HTMLComment extends HTMLText
{
	/**
	 * Node type
	 * @var		int
	 * @access	public
	 */
	var $nodeType=COMMENT_NODE;
	/**
	 * Element node name
	 * @var		string
	 * @access	public
	 */
	var $nodeName="#comment";
}

/**
 * HTML doctype class
 * @name HTMLDocumentType
 * @package HTMLPP
 * @subpackage HTMLDocumentType
 */
class HTMLDocumentType extends HTMLNode
{
	/**
	 * Node type
	 * @var		int
	 * @access	public
	 */
	var $nodeType=DOCUMENT_TYPE_NODE;
	/**
	 * Element node name
	 * @var		string
	 * @access	public
	 */
	var $nodeName="";
	/**
	 * Doctype name
	 * @var		string
	 * @access	public
	 */	
	var $name="";
	/**
	 * Doctype public id
	 * @var		string
	 * @access	public
	 */	
	var $publicId="";
	/**
	 * Doctype system id
	 * @var		string
	 * @access	public
	 */	
	var $systemId="";
	/**
	 * Class constructor
	 * @param 	string		$name 		doctype name
	 * @param 	string		$publicId 	doctype public Id
	 * @param 	string		$systemId 	doctype system Id
	 * @access	public
	 */
	function HTMLDocumentType($name="",$publicId="", $systemId="")
	{
		$this->nodeName=$this->name=$name;
		$this->publicId=$publicId;
		$this->systemId=$systemId;
		parent::HTMLNode();
	}
}
?>