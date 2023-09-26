<?PHP
/*
html parser class - parse html using DOMDocument
version 1.1 5/5/2015

Copyright (c) 2015, Wagon Trader

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class html_parser{
	
	//*********************************************************
	// Settings
	//*********************************************************
	
	//starting separator between element paths
	public $startSep = '[';
	
	//ending separator between element paths
	public $endSep = ']';
	
	public $doc;
	public $error;
	
	/* html parser class initialization
	usage: 	html_parser(void);

	This method is automatically called when the class is initialized.
	It will disable libxml notices, set instance of domDocument and/or
	initialize the htmlTree global.
	
	returns: void
	*/
	public function __construct(){
		
		libxml_use_internal_errors(true);
		$this->doc = new DOMDocument;

		$this->doc->preserveWhiteSpace = false;
		$this->doc->strictErrorChecking = false;
		
		$this->initializeGlobals();
	}
	
	/* method:	loadFile
	usage:	loadFile(string file);
	params:	file = full path to file to load
	
	This method will load the specified file for processing.
	
	returns: void
	*/
	public function loadFile($file){
		
		$filePath = realpath($file);
		if( is_readable($filePath) === false ){
			$this->error = 'Problem reading file: '.$file;
		}else{
			$this->doc->loadHTMLFile($filePath);
		}
	}
	
	/* method:	loadString
	usage:	loadString(string string);
	params:	string = string containing markup to process
	
	This method will load the specified string for processing.
	
	returns: void
	*/
	public function loadString($string){
		
		if( empty($string) ){
			$this->error = 'String is empty';
		}else{
			$this->doc->loadHTML($string);
		}
	}
	
	/* method:	processDocument
	usage:	processDocument(void);
	
	This method will parse the entire document into an array of elements.
	
	returns: array of elements or false on failure
	*/
	public function processDocument(){
		
		if( empty($this->error) ){
			$node = $this->doc->documentElement;
			$dim = $this->startSep.$node->nodeName.$this->endSep;
			$GLOBALS['htmlTree'][$dim]['element'] = $node->nodeName;
			if( $node->hasAttributes() ){
				$length = $node->attributes->length;
				for( $x=0;$x<$length;$x++ ){
					$GLOBALS['htmlTree'][$dim]['attr'][$x]['name'] = $node->attributes->item($x)->nodeName;
					$GLOBALS['htmlTree'][$dim]['attr'][$x]['value'] = $node->attributes->item($x)->nodeValue;
				}
			}
			$this->processNode($node);
			$return = $GLOBALS['htmlTree'];
			$this->initializeGlobals();
		}else{
			$return = false;
		}
		
		return $return;
	}
	
	/* method:	processTagName
	usage:	processTagName(string tagName[,bool includeDescendants]);
	params:	tagName = name of tag to return
			includeDescendants = true to include descendants of tag
			
	This method will parse the markup for the specified tag and return an array of the result.
	
	returns: array of elements matching the tag or false on failure
	*/
	public function processTagName($tagName,$includeDescendants=false){
		
		if( empty($this->error) ){
			$nodes = $this->doc->getElementsByTagName($tagName);

			foreach( $nodes as $node ){
				$xPath = $node->getNodePath();
				$xParts = $this->processPath($xPath);
				$dim='';
				foreach( $xParts as $part ){
					$dim .= $this->startSep.$part.$this->endSep;
				}
				$GLOBALS['htmlTree'][$dim]['element'] = $node->nodeName;
				$text = $node->nodeValue;
				$text = trim($text);
				if( strlen($text) > 0 ){
					$GLOBALS['htmlTree'][$dim]['text'][0] = $text;
				}
				if( $node->hasAttributes() ){
					$length = $node->attributes->length;
					for($x=0;$x<$length;$x++){
						$GLOBALS['htmlTree'][$dim]['attr'][$x]['name'] = $node->attributes->item($x)->nodeName;
						$GLOBALS['htmlTree'][$dim]['attr'][$x]['value'] = $node->attributes->item($x)->nodeValue;
					}
				}
				if( $includeDescendants ){
					
					$this->processNode($node);
				}
			}
			
			$return = $GLOBALS['htmlTree'];
			$this->initializeGlobals();
			
			return $return;
		}else{
			return false;
		}
	}
	
	/* method:	processElementID
	usage:	processElementID(string elementID);
	params:	elementID = unique id of element
	
	This method will find the tag for the specified element and return the tag name.
	
	returns: tag name for specified element or false on error
	*/
	public function processElementID($elementID){
		
		if( empty($this->error) ){
			$tagName = $this->doc->getElementById($elementID)->tagName;
			
			return $tagName;
		}else{
			return false;
		}
		
	}
	
	/* method:	showResult
	usage:	showResult(mixed $result);
	params:	$result = results returned after processing
	
	This method will return a human readable string of the processed results.
	
	returns: human readable string of the processed results.
	*/
	public function showResult($resultArray){
		if( is_array($resultArray) ){
			$return = '';
			foreach( $resultArray as $path=>$element ){
				$return .= '<strong>Element: </strong>'.$element['element'].'<br>';
				$return .= '<strong>Path: </strong>'.$path.'<br>';
				if( !empty($element['text']) ){
					foreach( $element['text'] as $key=>$value ){
						$return .= '<strong>Text '.$key.': </strong>'.htmlspecialchars($value).'<br>';
					}
				}
				if( !empty($element['attr']) ){
					foreach( $element['attr'] as $attribute ){
						$return .= '<strong>Attribute: </strong>'.$attribute['name'].' = '.htmlspecialchars($attribute['value']).'<br>';
					}
				}
				if( !empty($element['cdata']) ){
					$return .= '<strong>Cdata: </strong>'.htmlspecialchars($element['cdata']).'<br>';
				}
				$return .= '<br>';
			}
			
		}elseif( !empty($resultArray) ){
			$return = $resultArray;
		}else{
			$return = 'Parse returned an empty result';
		}
		
		return $return;
	}
	
	//only internal use methods follow
	public function initializeGlobals(){
		$GLOBALS['htmlTree'] = '';
	}
	
	public function processPath($xPath){
		$xPath=str_replace('[','',$xPath);
		$xPath=str_replace(']','',$xPath);
		$xPath=str_replace('(','',$xPath);
		$xPath=str_replace(')','',$xPath);
		$xParts = explode('/',$xPath);
		array_shift($xParts);
		
		return $xParts;
	}
	
	public function processNode($node){
		
		if( $node->hasChildNodes() ){
			$subNodes = $node->childNodes;
			foreach ($subNodes as $subNode){
				
				$xPath = $subNode->getNodePath();
				$xParts = $this->processPath($xPath);
				
				$dim = '';
				switch( $subNode->nodeType ){
					case 1:
						foreach( $xParts as $part ){
							$dim .= $this->startSep.$part.$this->endSep;
						}
						$GLOBALS['htmlTree'][$dim]['element'] = $subNode->nodeName;
						if( $subNode->hasAttributes() ){
							$length = $subNode->attributes->length;
							for( $x=0;$x<$length;$x++ ){
								$GLOBALS['htmlTree'][$dim]['attr'][$x]['name'] = $subNode->attributes->item($x)->nodeName;
								$GLOBALS['htmlTree'][$dim]['attr'][$x]['value'] = $subNode->attributes->item($x)->nodeValue;
							}
						}
						break;
					case 3:
						array_pop($xParts);
						foreach( $xParts as $part ){
							$dim .= $this->startSep.$part.$this->endSep;
						}
						$subNode->nodeValue = trim($subNode->nodeValue);
						if( !empty($subNode->nodeValue) ){
							$textCount = @count($GLOBALS['htmlTree'][$dim]['text']);
							$GLOBALS['htmlTree'][$dim]['text'][$textCount] = trim($subNode->nodeValue);
						}
						break;
					case 4:
						foreach( $xParts as $part ){
							$dim .= $this->startSep.$part.$this->endSep;
						}
						$GLOBALS['htmlTree'][$dim]['cdata'] = $subNode->nodeValue;
						break;
					case 8:
						foreach( $xParts as $part ){
							$dim .= $this->startSep.$part.$this->endSep;
						}
						$GLOBALS['htmlTree'][$dim]['element'] = 'comment';
						$GLOBALS['htmlTree'][$dim]['text'][0] = trim($subNode->nodeValue);
						break;
				}
				$this->processNode($subNode);
			}
		}
	}
	
}
/*
change log
1.1 fixed function return value in write text error when trimming value
*/
?>
