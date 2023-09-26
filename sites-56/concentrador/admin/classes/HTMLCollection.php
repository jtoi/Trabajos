<?php
require_once 'HTMLFilterIterator.php';

/**
 * HTML collection class
 * This class is based on the search of elements by css 3 selectors. It supports:
 * - tag						Elements with the given tag name
 * - #id						Elements with the given id
 * - .class						Elements with the given class name
 * - [attribute]				Elements that have the given attribute
 * - [attribute=value]			Elements that have the given attribute and it's equal to the given value
 * - [attribute!=value]			Elements that have the given attribute and it's different from the given value
 * - [attribute$=value]			Elements that have the given attribute and it ends with the given value
 * - [attribute|=value]			Elements that have the given attribute and it ends with the value with a hypen before or it starts with the value with a hypen after
 * - [attribute^=value]			Elements that have the given attribute and it starts with the given value
 * - [attribute~=value]			Elements that have the given attribute and it's equal to the given value or it contains the value with whitespaces before and/or after
 * - [attribute*=value]			Elements that have the given attribute and it contains the given value
 * - :first-child				Elements that are first child of their parent
 * - :last-child				Elements that are last child of their parent
 * - :only-child				Elements that are the only child of their parent
 * - :nth-child(exp)			Elements that have an+b-1 siblings before them in the document tree (http://www.w3.org/TR/css3-selectors/#structural-pseudos)
 * - :nth-last-child(exp)		Elements that have an+b-1 siblings after them in the document tree (http://www.w3.org/TR/css3-selectors/#structural-pseudos)
 * - :odd						Odd elements
 * - :even						Even elements
 * - :only-of-type				Elements that are the only child with the given tag name of their parent (this require a tag selector before them)
 * - :last-of-type				Elements that are last child with the given tag name of their parent (this require a tag selector before them)
 * - :first-of-type				Elements that are first child with the given tag name of their parent (this require a tag selector before them)
 * - :nth-of-type				Elements that have an+b-1 siblings of their type before them in the document tree (http://www.w3.org/TR/css3-selectors/#structural-pseudos)
 * - :nth-last-of-type(exp)		Elements that have an+b-1 siblings of their type after them in the document tree (http://www.w3.org/TR/css3-selectors/#structural-pseudos)
 * - :contains(text)			Elements that contain the given text
 * - :has(selector)				Elements that contain at least one element that matches the given selector
 * - :not(selector)				Elements that don't match the given selector
 * - :empty						Elements that don't contain any element or text node
 * - :parent					Elements that are parent of at least one element or text node
 * - :header					H1,H2,H3,H4,H5,H6 elements
 * - :button					Button elements or input elements with type=button
 * - :input						Input, button, select and textarea elements
 * - :text						input elements with type=text
 * - :reset						input elements with type=reset
 * - :file						input elements with type=file
 * - :radio						input elements with type=radio
 * - :password					input elements with type=password
 * - :submit					input elements with type=submit
 * - :image						input elements with type=image
 * - :hidden					input elements with type=hidden
 * - :checkbox					input elements with type=checkbox
 * - :selected					Selected elements
 * - :checked					Checked elements
 * - :disabled					Disabled elements
 * - :readonly					Read-only elements
 * - :enabled					Not disabled elements
 * - :lang(value)				Elements with the lang attribute equal to the given value
 * - :root						Element with HTML tag name
 * Note that attributes and pseudo selectors with parenthesis support other escaped parenthesis inside them.
 * Combinators:
 * - " "		Search in elements subtree
 * - ">"		Search in elements child nodes
 * - "+"		Search only in elements next sibling
 * - "~"		Search in all elements next sibling
 * For a reference on css3 selectors look at: http://www.w3.org/TR/css3-selectors/
 * @name HTMLCollection
 * @package HTMLPP
 * @subpackage HTMLCollection
 */
class HTMLCollection
{
	/**
	 * Array of elements inside the collection
	 * @var		array
	 * @access	public
	 */
	var $elements=array();
	/**
	 * Number of elements inside the collection
	 * @var		int
	 * @access	public
	 */
	var $length=0;
	/**
	 * The node that generates the collection
	 * @var		object
	 * @access	public
	 */
	var $context=null;
	/**
	 * This var can be specified to search in a different context when searching by selector
	 * @var		object
	 * @access	private
	 */
	var $_alternativeContext=null;
	/**
	 * Regular expression for split selector in smaller parts
	 * @var		regex
	 * @access	private
	 */
	var $splitter="@([\s>+~,]?)\s*((?:[\.\[:#]?)(?:[\w\-\d]+|\*)(?:(?:(?:[\^\$*!|~]?=)(?:(?:\\\\?.)*?))?\]|\((?:(?:\\\\?.)*?)\))?)+@";
	/**
	 * Regular expression for css selectors
	 * @var		regex
	 * @access	private
	 */
	var $chunker="@([\.\[:#]?)([\w\-\d]+|\*)(?:(?:([\^\$*!|~]?=)((?:\\\\?.)*?))?\]|\(((?:\\\\?.)*?)\))?@";
	/**
	 * Class constructor
	 * @param	object	$context	The node from which to start searching
	 * @param	string	$selector	An optional selector for immediatly fill the collection
	 * @access	public
	 */
	function HTMLCollection(&$context,$selector=null)
	{
		$this->context=& $context;
		if($selector) $this->add($selector);
	}
	/**
	 * Merge the current collection with another element, array of element or another collection
	 * @param	object|array	$element	Element,array of element, collection, array of collections
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function merge(& $element)
	{
		if(is_a($element,"HTMLCollection") && count($element->elements))
			foreach($element->elements as $k=>$el)
				$this->merge($element->elements[$k]);
		elseif(is_a($element,"HTMLNode"))
			$this->elements[]=& $element;
		elseif(is_array($element) && count($element))
			foreach($element as $k=>$el)
				$this->merge($element[$k]);
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Add another set of elements that match the selector to the current collection
	 * @param	string	$selector	Selector string
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function add($selector="*")
	{
		$collection=&$this->_query($selector);
		$this->merge($collection);
		return $this;
	}
	/**
	 * Remove all duplicated elements from the collection
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function unique()
	{
		if(count($this->elements)<2) return;
		$internals=$elements=array();
		foreach($this->elements as $el)
			$internals[]=$el->_internalID;
		$unique=array_unique($internals);
		if(count($unique)==count($internals)) return;
		foreach($this->elements as $k=>$el)
		{
			$key=array_search($el->_internalID,$unique);
			if($key===false) continue;
			unset($unique[$key]);
			$elements[]=& $this->elements[$k];
		}
		$this->elements=$elements;
		return $this;
	}
	/**
	 * Apply a function to all the elements of the collection
	 * @param	function	$fn		Function returned by create_function. It receives two parameters: the element passed as reference and it's index in the collection
	 * @params	array		$args	An array of optional arguments to pass into the function
	 * @return	array		an array containig all the results returned by each function execution
	 * @access	public
	 */
	function each($fn,$args=array())
	{
		$ret=array();
		if(count($this->elements))
			foreach($this->elements as $k=>$e)
				$ret[]=@call_user_func_array($fn,array_merge(array(&$this->elements[$k],$k),$args));
		return $ret;
	}
	/**
	 * Set multiple attributes to all the elements in the collection
	 * @param 	array|string|object		$attributes	Array or object of attributes in attribute=>value format or html attribute string
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function setAttributes($attributes=array())
	{
		$this->each(create_function('$element,$index,$attributes','$element->setAttributes($attributes);'),array($attributes));
		return $this;
	}
	/**
	 * Set an attribute for all the elements in the collection
	 * @param 	string		$name	Attribute name
	 * @param 	string		$val	Attribute value
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function setAttribute($name,$val=true)
	{
		$this->each(create_function('$element,$index,$name,$val','$element->setAttribute($name,$val);'),array($name,$val));
		return $this;
	}
	/**
	 * Get an attribute from every elements
	 * @param 	string		$name	Attribute name
	 * @return	array		Attribute value of all elements
	 * @access	public
	 */
	function getAttribute($name)
	{
		return $this->each(create_function('$element,$index,$name','return $element->getAttribute($name);'),array($name));
	}
	/**
	 * Remove an attribute from every elements
	 * @param 	string		$name	Attribute name
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function removeAttribute($name)
	{
		$this->each(create_function('$element,$index,$name','$element->removeAttribute($name);'),array($name));
		return $this;
	}
	/**
	 * Empty the attributes object from every elements
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function emptyAttributes()
	{
		$this->each(create_function('$element,$index','$element->emptyAttributes();'));
		return $this;
	}
	/**
	 * Set multiple style properties for every element
	 * @param 	array|string|object		$style	Array or object of style properties in style=>value format or css style string
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function setStyles($styles=array())
	{
		$this->each(create_function('$element,$index,$styles','$element->setStyles($styles);'),array($styles));
		return $this;
	}
	/**
	 * Set a style property for every element
	 * @param 	string		$name	Style property name
	 * @param 	string		$val	Style property value
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function setStyle($name,$val)
	{
		$this->each(create_function('$element,$index,$name,$val','$element->setStyle($name,$val);'),array($name,$val));
		return $this;
	}
	/**
	 * Return a style property value from every element
	 * @param 	string		$name	Style property name
	 * @return	array		Property value of all elements
	 * @access	public
	 */
	function getStyle($name)
	{
		return $this->each(create_function('$element,$index,$name','return $element->getStyle($name);'),array($name));
	}
	/**
	 * Remove a style property value from every element
	 * @param 	string		$name	Style property name
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function removeStyle($name)
	{
		$this->each(create_function('$element,$index,$name','$element->removeStyle($name);'),array($name));
		return $this;
	}
	/**
	 * Empty the style object
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function emptyStyle()
	{
		$this->each(create_function('$element,$index','$element->emptyStyles();'));
		return $this;
	}
	/**
	 * Add the context of the collection at the beginning of the collection's elements array
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function andSelf()
	{
		$elements=array();
		$elements[]=& $this->context;
		if(count($this->elements))
			foreach($this->elements as $k=>$el)
				$elements[]=& $this->elements[$k];
		$this->elements=$elements;
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Return the node at the specified index or null if there isn't any node at that index
	 * @param 	int		$index	Index of the element
	 * @return	object	Element
	 * @access	public
	 */
	function &get($index=0)
	{
		return isset($this->elements[$index]) ? $this->elements[$index] : null;
	}
	/**
	 * Return a copy of the current HTMLCollection
	 * @return	object	copy of the current collection
	 * @access	public
	 */
	function &copy()
	{
		$collection=new HTMLCollection($this->context);
		$collection->merge($this->elements);
		return $collection;
	}
	/**
	 * Reduce the collection to only one element that is positioned at the given index
	 * @param	int			$index		Index of the element
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function eq($index=0)
	{
		$element=& $this->get($index);
		$this->elements=array();
		if($element!==null) $this->elements[]=& $element;
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Reduce the collection by setting a range of elements positions. Elements at the start and end positions are included.
	 * @param	int			$start		Start position. If it's a negative number it indecates the number of elements that must be taken before the position indicated by the second parameter
	 * @param	int			$end		End position. If it's not set the last element's position will be taken
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function slice($start=0,$end=null)
	{
		if($end===null) $end=count($this->elements)-1;
		if($start<0) $start=($end+$start)+1;
		$newels=array();
		for($i=$start;$i<=$end;$i++)
			if(isset($this->elements[$i]))
				$newels[]=& $this->elements[$i];
		$this->elements=$newels;
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Filter the elements in the collection by passing them to a given function. If this function returns false the relative element is removed
	 * @param	function	$fn		Filter function
	 * @param	array		$args	Optional arguments to pass into the function
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function filterByFunction($fn,$args=array())
	{
		if(count($this->elements))
		{
			$ret=$this->each($fn,$args);
			$elements=array();
			foreach($ret as $k=>$result)
				if($result!==false)
					$elements[]=& $this->elements[$k];
			$this->elements=$elements;
		}
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Remove every element in the collection that doesn't match the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function filter($selector="*")
	{
		$collection=& $this->_query($selector);
		$internals=$elements=array();
		if(count($collection->elements))
		{
			foreach($collection->elements as $el)
				$internals[]=$el->_internalID;
			if(count($this->elements))
				foreach($this->elements as $k=>$el)
					if(in_array($el->_internalID,$internals))
						$elements[]=& $this->elements[$k];
		}
		$this->elements=$elements;
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Return true if at least one element matches the given selector
	 * @param	string		$selector		Selector
	 * @return	bool		true if at least one element matches the given selector, otherwise false
	 * @access	public
	 */
	function is($selector="*")
	{
		$collection=& $this->_query($selector);
		$internals=array();
		if(count($collection->elements))
		{
			foreach($collection->elements as $el)
				$internals[]=$el->_internalID;
			if(count($this->elements))
				foreach($this->elements as $k=>$el)
					if(in_array($el->_internalID,$internals)) 
						return true;
		}
		return false;
	}
	/**
	 * Return true if every element matches the given selector
	 * @param	string		$selector		Selector
	 * @return	bool		true if every element matches the given selector, otherwise false
	 * @access	public
	 */
	function all($selector="*")
	{
		$collection=& $this->_query($selector);
		$internals=array();
		if(count($collection->elements))
		{
			foreach($collection->elements as $el)
				$internals[]=$el->_internalID;
			if(count($this->elements))
				foreach($this->elements as $k=>$el)
					if(!in_array($el->_internalID,$internals)) 
						return false;
		}
		return true;
	}
	/**
	 * Remove every element in the collection that matches the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function remove($selector="*")
	{
		$collection=& $this->_query($selector);
		$internals=$elements=array();
		if(count($collection->elements))
		{
			foreach($collection->elements as $el)
				$internals[]=$el->_internalID;
			if(count($this->elements))
				foreach($this->elements as $k=>$el)
					if(!in_array($el->_internalID,$internals))
						$elements[]=& $this->elements[$k];
			$this->elements=$elements;
		}
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Replace the current collection with every direct child of each element in the collection that matches the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function children($selector="*")
	{		
		$this->_alternativeContext=& $this->elements;
		$collection=& $this->_query($selector,false,HTML_SEARCH_CHILDREN);
		$this->_alternativeContext=null;
		$this->elements=array();
		$this->merge($collection);
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Replace the current collection with every descendant child of each element in the collection that matches the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function find($selector="*")
	{		
		$this->_alternativeContext=& $this->elements;
		$collection=& $this->_query($selector);
		$this->_alternativeContext=null;
		$this->elements=array();
		$this->merge($collection);
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Replace the current collection with every first next sibling of each element in the collection that matches the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function next($selector="*")
	{		
		$this->_alternativeContext=& $this->elements;
		$collection=& $this->_query($selector,false,HTML_SEARCH_NEXT_SIBLING);
		$this->_alternativeContext=null;
		$this->elements=array();
		$this->merge($collection);
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Replace the current collection with every next sibling of each element in the collection that matches the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function nextAll($selector="*")
	{		
		$this->_alternativeContext=& $this->elements;
		$collection=& $this->_query($selector,false,HTML_SEARCH_ALL_NEXT_SIBLINGS);
		$this->_alternativeContext=null;
		$this->elements=array();
		$this->merge($collection);
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Replace the current collection with every first previous sibling of each element in the collection that matches the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function prev($selector="*")
	{		
		$this->_alternativeContext=& $this->elements;
		$collection=& $this->_query($selector,false,HTML_SEARCH_PREVIOUS_SIBLING);
		$this->_alternativeContext=null;
		$this->elements=array();
		$this->merge($collection);
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Replace the current collection with every previous sibling of each element in the collection that matches the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function prevAll($selector="*")
	{		
		$this->_alternativeContext=& $this->elements;
		$collection=& $this->_query($selector,false,HTML_SEARCH_ALL_PREVIOUS_SIBLINGS);
		$this->_alternativeContext=null;
		$this->elements=array();
		$this->merge($collection);
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Replace the current collection with every sibling of each element in the collection that matches the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function siblings($selector="*")
	{		
		$this->_alternativeContext=& $this->elements;
		$collection=& $this->_query($selector,false,HTML_SEARCH_SIBLINGS);
		$this->_alternativeContext=null;
		$this->elements=array();
		$this->merge($collection);
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Replace the current collection with the parent node of each element in the collection that matches the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function parent($selector="*")
	{		
		$this->_alternativeContext=& $this->elements;
		$collection=& $this->_query($selector,false,HTML_SEARCH_PARENT);
		$this->_alternativeContext=null;
		$this->elements=array();
		$this->merge($collection);
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Replace the current collection with every ancestor node of each element in the collection that matches the given selector
	 * @param	string		$selector		Selector
	 * @return	object		reference to the current collection
	 * @access	public
	 */
	function parents($selector="*")
	{		
		$this->_alternativeContext=& $this->elements;
		$collection=& $this->_query($selector,false,HTML_SEARCH_ANCESTOR);
		$this->_alternativeContext=null;
		$this->elements=array();
		$this->merge($collection);
		$this->length=count($this->elements);
		return $this;
	}
	/**
	 * Private function for match element by the given selector
	 * @param	string	$selector		Selector string
	 * @param	bool	$onlyFunction	If it's true return the first simple selector's filter function and stop before start searching
	 * @param	int		$whereSearch	One of the search groups constants of HTMLFilterIterator. It indicates where to start the search.
	 * @return	object	A dom collection containing the matched elements
	 * @access	private
	 */
	function &_query($selector="*",$onlyFunction=false, $whereSearch=HTML_SEARCH_DESCENDANT)
	{		
		$selector=trim($selector);
		preg_match_all($this->splitter,$selector,$splittedMatches,PREG_SET_ORDER);
		$collectionFound=new HTMLCollection($this->context);
		foreach($splittedMatches as $groups)
		{			
			$combinator=$groups[1];
			$searchtag=null;
			$group=preg_replace("#^\s*\\".$combinator."\s*#","",$groups[0]);
			if(!preg_match_all($this->chunker,$group,$matches,PREG_SET_ORDER)) continue;
			$condition=$body=array();
			foreach($matches as $chunk)
			{
				$type=isset($chunk[1]) ? $chunk[1] : "";
				$name=isset($chunk[2]) ? $chunk[2] : "";
				$sign=isset($chunk[3]) ? $chunk[3] : "";
				$value=isset($chunk[4]) ? trim($chunk[4],'"\'') : "";
				$pseudo=isset($chunk[5]) ? $chunk[5] : "";
				switch($type)
				{
					case "#": 	$condition[]='$element->getAttribute("id")=="'.$name.'"'; break;
					case ".": 	$condition[]='$element->getAttribute("class")=="'.$name.'"'; break;
					case "[": 	switch($sign)
								{
									case "=":	$condition[]='$element->getAttribute("'.$name.'")=="'.$value.'"'; break;
									case "$=":	$condition[]='preg_match("#'.preg_quote($value).'$#",$element->getAttribute("'.$name.'"))'; break;
									case "|=":	$condition[]='preg_match("#^'.preg_quote($value).'\-|^'.preg_quote($value).'$#",$element->getAttribute("'.$name.'"))'; break;
									case "^=":	$condition[]='preg_match("#^'.preg_quote($value).'#",$element->getAttribute("'.$name.'"))'; break;
									case "*=":	$condition[]='preg_match("#'.preg_quote($value).'#",$element->getAttribute("'.$name.'"))'; break;
									case "~=":	$condition[]='preg_match("#(^|\s)'.preg_quote($value).'($|\s)#",$element->getAttribute("'.$name.'"))'; break;
									case "!=":	$condition[]='$element->getAttribute("'.$name.'")!="'.$value.'"'; break;
									default:	$condition[]='$element->hasAttribute("'.$name.'")'; break;
								}
								break;
					case ":": 	switch($name)
								{
									case "only-child":	$body[]='$check=$element->parentNode->childNodes[0];$sc=true;while($check!==null){if($check->nodeType==='.ELEMENT_NODE.') if($sc) $sc=false; else return false; $check=$check->nextSibling;};'; break;
									case "only-of-type":if($searchtag) $body[]='$check=$element->parentNode->childNodes[0];$sc=true;while($check!==null){if($check->nodeType==='.ELEMENT_NODE.' && $check->tagName=="'.$searchtag.'") if($sc) $sc=false; else return false; $check=$check->nextSibling;};'; break;
									case "first-child": $condition[]='$index==0'; break;
									case "first-of-type":if($searchtag) $body[]='$check=$element->previousSibling;while($check!==null){if($check->nodeType==='.ELEMENT_NODE.' && $check->tagName=="'.$searchtag.'") return false; $check=$check->previousSibling;};'; break;
									case "last-child": 	$body[]='$check=$element->nextSibling;while($check!==null){if($check->nodeType==='.ELEMENT_NODE.') return false; $check=$check->nextSibling;};'; break;
									case "last-of-type":if($searchtag) $body[]='$check=$element->nextSibling;while($check!==null){if($check->nodeType==='.ELEMENT_NODE.' && $check->tagName=="'.$searchtag.'") return false; $check=$check->nextSibling;};'; break;
									case "contains": 	$condition[]='preg_match("#'.preg_quote(str_replace("#","\#",trim($pseudo,'"\''))).'#",$element->getTextContent())'; 
														break;
									case "even":		$condition[]='($index+1)%2==0'; break;
									case "lang":		$condition[]='$element->getAttribute("lang")=="'.$pseudo.'"'; break;
									case "odd": 		$condition[]='($index+1)%2!=0'; break;
									case "root": 		$condition[]='strtolower($element->tagName)=="html"'; break;
									case "empty":		$body[]='if($element->textContent) return false; if(count($element->childNodes)) foreach($element->childNodes as $child) if(in_array($element->nodeType,array('.ELEMENT_NODE.','.TEXT_NODE.'))) return false;';
														break;
									case "button":		$condition[]='(strtolower($element->getAttribute("type"))=="button" || strtolower($element->tagName)=="button")'; break;
									case "input":		$condition[]='in_array(strtolower($element->tagName),array("input","select","textarea","button"))'; break;
									case "text":
									case "reset":
									case "file":
									case "radio":
									case "password":
									case "submit":
									case "image":
									case "hidden":
									case "checkbox":	$condition[]='strtolower($element->tagName)=="input" && strtolower($element->getAttribute("type"))==strtolower("'.$name.'")'; break;
									case "checked":
									case "selected":
									case "disabled":
									case "readonly":	$condition[]='$element->getAttribute("'.$name.'")===true';break;
									case "enabled":		$condition[]='in_array(strtolower($element->tagName),array("input","select","textarea","button")) && !$element->getAttribute("disabled")';break;
									case "parent":		$body[]='$found=false; if(count($element->childNodes)) foreach($element->childNodes as $child) if(in_array($element->nodeType,array('.ELEMENT_NODE.','.TEXT_NODE.'))){$found=true;break;}; if(!$found) return false;';
														break;
									case "header":		$condition[]='preg_match("#^h[1-6]$#",$element->tagName)'; break;
									case "not":			if($pseudo)
														{
															$parts=$this->_query($pseudo,true);
															$condition[]='!('.implode(' && ',$parts[1]).')';
															if(count($parts[0])) $body[]='if(call_user_func_array(create_function(\'$element,$index\',\''.implode("\n",$parts[0]).'\'),array(&$element,$index))) return false;';
														}
														break;
									case "has":			$body[]='$coll=new HTMLCollection($element,"'.$pseudo.'");if(!count($coll->elements)) return false;';break;
									case "nth-last-of-type":
									case "nth-of-type":	if($searchtag) $body[]='$check=& $element->previousSibling;while($check!==null){if($check->nodeType==='.ELEMENT_NODE.' && $check->tagName!="'.$searchtag.'") $index--; $check=& $check->previousSibling;};';
									case "nth-last-child": if($name!=="nth-of-type") $body[]='$checkc=& $element->nextSibling;$conto=$index;while($checkc!==null){if($checkc->nodeType==='.ELEMENT_NODE.($name=="nth-last-of-type" ? '&& $checkc->tagName=="'.$searchtag.'"' : '').') $conto++; $checkc=& $checkc->nextSibling;};$index=$conto-$index;';										
									case "nth-child": 	switch($pseudo){
															case "2n":
															case "even":$condition[]='($index+1)%2==0'; break;
															case "2n+1":
															case "odd": $condition[]='($index+1)%2!=0'; break;
															default: 	if(strpos($pseudo,"n")===false) $condition[]='($index+1)=='.$pseudo;
																		elseif($pseudo!="n" && preg_match("#\s*(-?)(\d*)n([+-]\d+)?\s*#",$pseudo,$exp))
																		{
																				$mlt=isset($exp[2]) && $exp[2]!="" ? (int)$exp[2] : 1;
																				if(isset($exp[1]) && $exp[1]=="-") $mlt*=-1;
																				if(!isset($exp[3])) $exp[3]=0;
																				$mat=(((int) $exp[3])*-1)."";													
																				if($mat[0]!="-") $mat="+".$mat;
																				$condition[]='eval("return '.$mlt.'<0 ? (($index+1'.$mat.')<=0 ? (($index+1'.$mat.')*-1)%'.$mlt.' : 1) : ('.$mlt.'!=0 ? ($index+1'.$mat.')%'.$mlt.' : $index+1'.$mat.');")===0'; break;
																		}
																		break;
														}
														break;
								}
								break;
					default: 	if($name!="*") {
									$condition[]='strtolower($element->tagName)==strtolower("'.$name.'")';
									$searchtag=$name;
								}
								break;
				}
			}
			if($onlyFunction) return array($body,$condition);
			if(count($condition)) $body[]='if(!('.implode(' && ',$condition).')) return false;';
			$body[]='return true;';
			$filterfunction=create_function('$element,$index',implode("\n",$body));
			switch($combinator)
			{
				case ">":	$context=& $collectionFound;
							$where=HTML_SEARCH_CHILDREN;
							break;
				case "~":	$context=& $collectionFound;
							$where=HTML_SEARCH_ALL_NEXT_SIBLINGS;
							break;
				case "+":	$context=& $collectionFound;
							$where=HTML_SEARCH_NEXT_SIBLING;
							break;
				case " ":	$context=& $collectionFound;
							$where=HTML_SEARCH_DESCENDANT;
							break;
				default:	if($this->_alternativeContext!==null) $context=& $this->_alternativeContext;
							else $context=& $this->context;
							$where=$whereSearch;
							break;
			}
			if($combinator!=",") $collectionFound=& HTMLFilterIterator::find($context,$filterfunction,$where);
			else{
				$nextcollection=& HTMLFilterIterator::find($context,$filterfunction,$where);
				$collectionFound->merge($nextcollection);
			}
		}
		$collectionFound->unique();
		return $collectionFound;
	}
}
?>