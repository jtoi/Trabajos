<?php
//Iteration type constants for HTMLFilterIterator
define("HTML_SEARCH_DESCENDANT",1);				//Search in element's descendant nodes
define("HTML_SEARCH_CHILDREN",2);				//Search in element's children
define("HTML_SEARCH_NEXT_SIBLING",3);			//Search only element's next sibling
define("HTML_SEARCH_ALL_NEXT_SIBLINGS",4);		//Search in element's next siblings
define("HTML_SEARCH_PREVIOUS_SIBLING",5);		//Search only element's previous sibling
define("HTML_SEARCH_ALL_PREVIOUS_SIBLINGS",6);	//Search in element's previous siblings
define("HTML_SEARCH_SIBLINGS",7);				//Search in all element's siblings (next and previous)
define("HTML_SEARCH_PARENT",8);					//Search in element's parent node
define("HTML_SEARCH_ANCESTOR",9);				//Search in element's ancestor nodes

/**
 * HTML filter iterator class
 * @name HTMLCollection
 * @package HTMLPP
 * @subpackage HTMLFilterIterator
 */
class HTMLFilterIterator
{
	/**
	 * Return a collection of elements that return true if passed in the given function
	 * @param	object		$context			The node from which to start searching
	 * @param	function	$function			Filter function. Two parameters will be passed: the element (as reference), the index of the element relative to the same level allowed elments
	 * @param	int			$searchtype			One of the search groups constants
	 * @param	array		$allowedElements	Array of node type constants to get only desidered node
	 * @return	object		A dom collection containing the matched elements
	 * @access	private
	 */
	function &find(&$context,$function,$searchtype=HTML_SEARCH_DESCENDANT,$allowedElements=array(ELEMENT_NODE))
	{
		if(!is_array($context))
		{
			if(is_a($context,"HTMLCollection")) $context= & $context->elements;
			else{
				$contextpass=& $context;
				unset($context);			
				$context=array(& $contextpass);
			}
		}
		$collection=new HTMLCollection($context[0]);
		if(count($context))
		foreach($context as $k=>$node)
		{						
			$element=& $context[$k];
			$index=0;
			switch($searchtype)
			{
				case HTML_SEARCH_DESCENDANT:	if(count($element->childNodes))
												foreach($element->childNodes as $k=>$sub)
												{
													$test=& $element->childNodes[$k];
													if(!is_array($allowedElements) || in_array($test->nodeType,$allowedElements))
														if(call_user_func_array($function,array(&$test,$index))) $collection->merge($test);
													$subcoll=& HTMLFilterIterator::find($test, $function,$searchtype,$allowedElements);
													$collection->merge($subcoll);
													$index++;
												}
											break;
				case HTML_SEARCH_CHILDREN: 	if(count($element->childNodes))
												foreach($element->childNodes as $k=>$sub)
												{
													$test=& $element->childNodes[$k];
													if(is_array($allowedElements) && !in_array($test->nodeType,$allowedElements)) continue;
													if(call_user_func_array($function,array(&$test,$index))) $collection->merge($test);
													$index++;
												}
											break;
				case HTML_SEARCH_NEXT_SIBLING:	$check=& $element->parentNode->childNodes;
												if(count($check))
													foreach($check as $child)
														if($child->_internalID==$element->_internalID) break;
														elseif(!is_array($allowedElements) || in_array($child->nodeType,$allowedElements)) $index++;
												$index++;
												$test=& $element->nextSibling;
												while($test!==null && is_array($allowedElements) && !in_array($test->nodeType,$allowedElements)) 
													$test=& $test->nextSibling;
												if($test!==null && call_user_func_array($function,array(&$test,$index))) $collection->merge($test);
												break;
				case HTML_SEARCH_ALL_NEXT_SIBLINGS:	$check=& $element->parentNode->childNodes;
													if(count($check))
													foreach($check as $child)
														if($child->_internalID==$element->_internalID) break;
														elseif(!is_array($allowedElements) || in_array($child->nodeType,$allowedElements)) $index++;
													$index++;
													$test=& $element->nextSibling;
													while($test!==null) 
													{
														if(!is_array($allowedElements) || in_array($test->nodeType,$allowedElements))
														{
															if(call_user_func_array($function,array(&$test,$index))) $collection->merge($test);
															$index++;
														}
														$test=& $test->nextSibling;
													}									
													break;
				case HTML_SEARCH_ALL_PREVIOUS_SIBLINGS:	$test=& $element->parentNode->childNodes[0];
														while($test->_internalID!=$element->_internalID) 
														{
															if(!is_array($allowedElements) || in_array($test->nodeType,$allowedElements))
															{
																if(call_user_func_array($function,array(&$test,$index))) $collection->merge($test);
																$index++;
															}
															$test=& $test->nextSibling;
														}									
														break;
				case HTML_SEARCH_PREVIOUS_SIBLING:	$test=& $element->previousSibling;
													while($test!==null && is_array($allowedElements) && !in_array($test->nodeType,$allowedElements)) 
														$test=& $test->previousSibling;
													$check=& $test->previousSibling;
													while($check!==null && is_array($allowedElements) && !in_array($test->nodeType,$allowedElements))
														$index++;
													if($test!==null && call_user_func_array($function,array(&$test,$index))) $collection->merge($test);
													break;
				case HTML_SEARCH_SIBLINGS:	$test=& $element->parentNode->childNodes[0];
											while($test!==null)
											{
												if($test->_internalID!=$element->_internalID)
												{
													if(!is_array($allowedElements) || in_array($test->nodeType,$allowedElements))
														if(call_user_func_array($function,array(&$test,$index)))
															$collection->merge($test);
												}
												$index++;
												$test=& $test->nextSibling;
											}
											break;
				case HTML_SEARCH_PARENT:	$test=&$element->parentNode;
											if($test!==null)
											{
												$check=&$element->parentNode->previousSibling;
												while($check!==null)
												{
													if(!is_array($allowedElements) || in_array($check->nodeType,$allowedElements)) $index++;
													$check=&$check->previousSibling;
												}
												if(call_user_func_array($function,array(&$test,$index))) $collection->merge($test);
											}
											break;
				case HTML_SEARCH_ANCESTOR:	$test=&$element->parentNode;
											while($test!==null)
											{
												$index=0;
												if(!is_array($allowedElements) || in_array($test->nodeType,$allowedElements))
												{
													$check=&$test->previousSibling;
													while($check!==null)
													{
														if(!is_array($allowedElements) || in_array($check->nodeType,$allowedElements)) $index++;
														$check=&$check->previousSibling;
													}
													if(call_user_func_array($function,array(&$test,$index))) $collection->merge($test);
												}
												$test=&$test->parentNode;
											}
											break;
			}
		}
		return $collection;
	}
}
?>