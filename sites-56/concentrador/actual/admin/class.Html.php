<?php
	/**
	 * tag class. this class uses by Html class for creating tags objects in array
	 *
	 */
	class Tag{
		public $name = '';
		public $text = '';
		public $parrent = '';
		public $content = '';
		public $props = array();
		public $inner = array();
		public $level = 0;
		function __construct($name){
			$this->name = $name;
		}
		function SetContent($content){
			$this->content = $content;
		}
		function AddText($str){
			$this->text .= $str;
		}
		function SetName($str){
			$this->name = $str;
		}
		function SetParrent(&$pointer){
			$this->name = &$pointer;
		}
		
		/**
		 * setting properties of tag from tag text
		 *
		 * @return array;
		 */
		function SetProperties(){
			//$tag = str_replace('<','',$tag);
			//$tag = str_replace('>','',$tag);
			$prop_patern = "|([^ ]+)=\"(.*)\"|U";
			$arr_paced_tag = preg_match_all($prop_patern,$this->content,$tag_paced,PREG_SET_ORDER);
			for ($i=0; $i<count($tag_paced); $i++){
				if ($tag_paced[$i][1] && $tag_paced[$i][2])	$result[$tag_paced[$i][1]] = $tag_paced[$i][2];
			}
			$prop_patern = "|([^ ]+)='(.*)'|U";
			$arr_paced_tag = preg_match_all($prop_patern,$this->content,$tag_paced,PREG_SET_ORDER);
			for ($i=0; $i<count($tag_paced); $i++){
				if ($tag_paced[$i][1] && $tag_paced[$i][2])	$result[$tag_paced[$i][1]] = $tag_paced[$i][2];
			}
			if ($result) $this->props = $result;
			
			if (DEBUGGING==1) $mk_end_time = getmicrotime();
        	if (DEBUGGING==1) echo "SetProperties():".(($mk_end_time - $mk_start_time));
			return $result;
		}
	}
	
	
	/**
	 * main html classs. making parsing of any html page.
	 * returns tree type result. This class giving big hrlp for creating tool like keyword density etc
	 *
	 */
	class Html {
		
		/**
		 * parser will not search close tag for this tags
		 *
		 * @var array
		 */
		public $alone_tags = array('input','img','br','hr','link','meta','!doctype','?xml');
		
		
		/**
		 * if close tag not found than parser will add it automaticaly before next tag
		 *
		 * @var array
		 */
		public $autoclose_tags = array(
								'th'=>array('th'=>1,'tr'=>2,'/tr'=>2,'/table'=>3),
								'td'=>array('td'=>1,'tr'=>2,'/tr'=>2,'/table'=>3),
								'tr'=>array('tr'=>1,'table'=>2,'/table'=>2),
								'li'=>array('li'=>1,'ul'=>2,'ul'=>2),
								'ul'=>array('ul'=>1),
								'p'=>array('p'=>1,'td'=>2,'tr'=>3,'table'=>3)
								);
		public $tree = array();
		public $title = '';
		public $tag_list = array(
							);
		public $text = '';
		public $content;
		public $file_name;
		public $pointer_position = '';
		public $original_text = '';
		public $original_content = '';
		
		public $aStopWords = array(
								'to','or','and','was','-+','[&#8212;]+','in','if','it',
								'of','on','the','our','my','all','your','we',
								'for','where','them','will','us','&copy;','&nbsp;','&amp;', 'a'
								);
		public $aNotWords =  array(
								'&quote','&raquo;','&laquo;','-','&#8212;',"&nbsp;","\(","\)",'\.',',','\'','\&','\/','!','\?','\d*'
								);
		public $aContentIgnoreTags = array(
								'meta content-type'
								);
		public $FileSize = 0;
		
		public $ignore_tags = array("script","style");
		
		public $curl_info;
		
		public $http_code;
		
		public $patternStopWords;
		
		/**
		 * constructor
		 *
		 * @param string $url
		 * @return Html
		 */
		function __construct($url=''){
			if ($url){
				
	    		$result = '';
				$chx = curl_init();
				
				$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
				$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
				$header[] = "Cache-Control: max-age=0";
				$header[] = "Connection: keep-alive";
				$header[] = "Keep-Alive: 300";
				$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
				$header[] = "Accept-Language: en-us,en;q=0.5";
				$header[] = "Pragma: "; // browsers keep this blank.
				
				curl_setopt ($chx, CURLOPT_URL, $url);
				curl_setopt ($chx, CURLOPT_HEADER, 0);
				curl_setopt ($chx, CURLOPT_HTTPHEADER, $header);
				curl_setopt ($chx, CURLOPT_RETURNTRANSFER,1);
				curl_setopt ($chx, CURLOPT_FOLLOWLOCATION,1);
				curl_setopt ($chx, CURLOPT_CONNECTTIMEOUT, 100);
				curl_setopt ($chx, CURLOPT_TIMEOUT, 100);
				curl_setopt ($chx, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:58.0) Gecko/20100101 Firefox/58.0");
				curl_setopt ($chx, CURLOPT_VERBOSE,0);
				curl_setopt ($chx, CURLOPT_SSL_VERIFYPEER, 0);
				
				$buffer = parse_url($url);
				$this->file_name = $buffer['path'];
				
				$this->original_content = curl_exec ($chx);
				
				$curl_info = curl_getinfo($chx);
				
				$this->curl_info = $curl_info;
				$this->http_code = $curl_info['http_code'];
				
				if ($this->content = $this->original_content){
					$result = true;
				}else{
					$result = false;
				}
				
				
				$this->FileSize = $curl_info['size_download'];
				
				//stop words pattern
				$this->patternStopWords = "/".implode('$|',$this->aStopWords)."$|^".implode('|^',$this->aStopWords)."/";
			}
			
			
			return $result;
		}
		
		/**
		 * advanced striptags function.
		 * returns text and title
		 */
		function Clean() {
    		$result = '';
    		
    		
		
			//replace blank\null characters by space
			$this->content = mb_eregi_replace("(\r|\\\r|\n|\\\n|\t|\\\t)"," ",$this->content);
			$this->content = mb_eregi_replace("(\x00|\\\x00)"," ",$this->content);
			$this->content = mb_eregi_replace("(\x1a|\\\x1a)"," ",$this->content);
			
			//extract title
			$regs = array();
			if (mb_eregi('< *title *>(.*?)< */ *title *>',$this->content,$regs)) {
			    $title = trim($regs[1]);
			}else {
			    $title = "";
			}
			
			
			
			//delete content of head, script, and style tags
			//$str = mb_eregi_replace("<head[^>]*>.*</head>"," ",$str);
			$this->content = mb_eregi_replace("(<script[^>]*?>.*?</script>)"," ",$this->content);
			$this->content = mb_eregi_replace("(<style[^>]*>.*?</style>)"," ",$this->content);
			$this->content = mb_eregi_replace("(<!--.*?-->)"," ",$this->content);
			
			//replace tags by space
			/*$str = mb_eregi_replace("<[^>]*?>"," ",$str);*/
			
			//replace space entity by space
			$this->content = mb_eregi_replace("&nbsp;"," ",$this->content);
			
			
			
			//replace arrow brackets by entities
			//$str = mb_ereg_replace(">","&gt;",$str);
			//$str = mb_ereg_replace("<","&lt;",$str);
			
			//replace repeaters with space
			$this->content = mb_eregi_replace("[_*.-]{3,}"," ",$this->content);
			
			//strip characters used in highlighting with no space
			$this->content = str_replace("^#_","",str_replace("_#^","",$this->content));
			$this->content = str_replace("@@@","",str_replace("@#@","",$this->content));
			
			
			
			//replace multiple space with one space
			$this->content = mb_ereg_replace("[[:space:]]+"," ",$this->content);
			$this->title = $title;
			
			$regs = array();
			mb_eregi('< *body.*?>(.*?)< */ *body *>',$this->content,$regs);
			$this->original_text = strip_tags(trim($regs[1]));
			
			
			foreach ($this->aNotWords as $value){
				$this->original_text = mb_eregi_replace($value,'',$this->original_text);
			}
			$this->original_text = mb_ereg_replace("[[:space:]]+"," ",$this->original_text);
		}
		
		/**
		 * html parser. 
		 * $description will have all tags description. useful whan making indexing of page
		 * returns array type tree of parsed html
		 *
		 * @param array $description
		 * @return array
		 */
		function Parse(&$description){
			
			if (!is_array($description)){
				$description = array();
			}
    		$result = '';
			$tag_name = '';
			$last_tag = '';
			$tree = array();
			$text = '';
			$strlen = strlen($this->content);
			$pos = 0;
			$tree = array();
			$stack_counter = 0;
			$autoclose_tags = &$this->autoclose_tags;
			$alone_tags = &$this->alone_tags;
			mb_ereg_search_init($this->content);
			
			
			$objCurrentTagPointer = '';
			$objDocument = new Tag('main');
			unset($this->tree);
			$this->tree = &$objDocument;
			$objCurrentTagPointer = &$objDocument;
			$objTag = &$objDocument;
			$buffer = '';

			$last_tag_name = '';
			$level = 0;
			while (($arr_pos = mb_ereg_search_pos("<[^>]*?>"))){
				$regs = mb_ereg_search_getregs();
				$tag_name_full = $regs[0];
				$tag_name = $this->CorrectTagName($tag_name_full);	
				$text = trim(substr($this->content,$pos,$arr_pos[0]-$pos));
				
				if ($tag_name!='a' && $tag_name!='title' && $text!=''){
					//if (strlen($obj->inner[$i])>100) 
					//$description .= " ".Truncate($obj->inner[$i],50,' ...',true);
					//else
					$description = array_merge($description,explode('. ',$text));
					
				}
				//open tag
				if (array_key_exists($objCurrentTagPointer->name,$autoclose_tags) && 
					array_key_exists($tag_name,$autoclose_tags[$objCurrentTagPointer->name])){
					//echo $tag_name;
					$level--;
					$back_counter = $autoclose_tags[$tag_name][$objCurrentTagPointer->name];
					for ($i=0; $i<$back_counter; $i++){
						unset($buffer);
						$buffer = &$objCurrentTagPointer;
						unset($objCurrentTagPointer);
						//$last_tag_name = array_pop(&$stack);
						$objCurrentTagPointer = &$buffer->parrent;
					}
				}
				
				//close tag
				if ($this->isCloser($tag_name)){
					if (is_object($objCurrentTagPointer) && $text!=''){
						//$objCurrentTagPointer->AddText($text);
						$objCurrentTagPointer->inner[] = $text;
					}
					if ($this->isCloserBeforeTag($objCurrentTagPointer->name,$tag_name)){
						//print_r($objCurrentTagPointer);
						unset($buffer);
						$buffer = &$objCurrentTagPointer;
						unset($objCurrentTagPointer);
						$objCurrentTagPointer = &$buffer->parrent;
					}
					$level--;
				}elseif (in_array($tag_name,$alone_tags)){ //tags without closer (example input, select ..)
					if (is_object($objCurrentTagPointer) && $text!=''){
						//$objTag->AddText($text);
						$objCurrentTagPointer->inner[] = $text;
					}
					unset($objTag);
					$objTag = new Tag($tag_name);
					$objTag->level = $level;
					//$objTag->content = $tag_name_full;
					$objTag->props = $this->tagProperties($tag_name_full);
					$objTag->parrent = &$objCurrentTagPointer;
					$objCurrentTagPointer->inner[] = &$objTag;
				}else{
					//put in stack	
					//get text
					
					if (is_object($objCurrentTagPointer) && $text!=''){
						//$objTag->AddText($text);
						$objCurrentTagPointer->inner[] = $text;
					}
					
					unset($objTag);
					$objTag = new Tag($tag_name);
					$objTag->level = $level;
					//$objTag->content = $tag_name_full;
					$objTag->props = $this->tagProperties($tag_name_full);
					$objTag->parrent = &$objCurrentTagPointer;
					//print_r($objTag);
					
					$objCurrentTagPointer->inner[] = &$objTag;
					unset($objCurrentTagPointer);
					$objCurrentTagPointer = &$objTag;
					$last_tag_name = $tag_name;
					$stack_counter++;
					$level++;
				}
				//print_r($objDocument);
				$pos = $arr_pos[0]+$arr_pos[1];
				//if ($pos>=$strlen) break;
			}
			return $objDocument;
		}
		
		/**
		 * just taking name of tag
		 *
		 * @param string $tag
		 * @return string
		 */
		function CorrectTagName($tag){
			$result = '';
			
			$arr_temp = explode(' ',$tag);
			$str = $arr_temp[0];
			$str = str_replace('<--','',$str);
			$str = str_replace('<!--','',$str);
			$str = str_replace('-->','',$str);
			$str = str_replace('--!>','',$str);
			$str = str_replace('>','',$str);
			$str = str_replace('<','',$str);
			//$str = str_replace('','',$str);
			
	        return strtolower($str);
		}
		
		/**
		 * checking if tag closes himself or not
		 *
		 * @param string $open_tag
		 * @param string $closer_tag
		 * @return int
		 */
		function isCloserBeforeTag($open_tag,$closer_tag){

	    	$result = '';
			
			if (strcasecmp("/".$open_tag,$closer_tag)==0){
				$result = 1;
			}else{
				$result = 0;
			}
			return $result;
		}
		
		/**
		 * is tag closed or not
		 *
		 * @param string $tag
		 * @return int
		 */
		function isCloser($tag){
			
	    	$result = '';
			
			if ($tag[0]=="/") $result = 1;
			else $result = 0;
			return $result;
		}
		
		/**
		 * taking tag properties to easy-to-use array
		 *
		 * @param string $tag
		 * @return array
		 */
		function tagProperties($tag){
	    	$result = '';
			//$tag = str_replace('<','',$tag);
			//$tag = str_replace('>','',$tag);
			$prop_patern = "|([^ ]+)=\"(.*)\"|U";
			$arr_paced_tag = preg_match_all($prop_patern,$tag,$tag_paced,PREG_SET_ORDER);
			for ($i=0; $i<count($tag_paced); $i++){
				if ($tag_paced[$i][1] && $tag_paced[$i][2])	{
					if (strtolower($tag_paced[$i][1])=='name' || strtolower($tag_paced[$i][1])=='http-equiv') $tag_paced[$i][2] = strtolower($tag_paced[$i][2]);
					$result[strtolower($tag_paced[$i][1])] = $tag_paced[$i][2];
				}
			}
			$prop_patern = "|([^ ]+)='(.*)'|U";
			$arr_paced_tag = preg_match_all($prop_patern,$tag,$tag_paced,PREG_SET_ORDER);
			for ($i=0; $i<count($tag_paced); $i++){
				if ($tag_paced[$i][1] && $tag_paced[$i][2])	{
					if (strtolower($tag_paced[$i][1])=='name' || strtolower($tag_paced[$i][1])=='http-equiv') $tag_paced[$i][2] = strtolower($tag_paced[$i][2]);
					$result[strtolower($tag_paced[$i][1])] = $tag_paced[$i][2];
				}
			}
			$prop_patern = "|([^'\">< ]+)=([^'\">< ]+)|";
			$arr_paced_tag = preg_match_all($prop_patern,$tag,$tag_paced,PREG_SET_ORDER);
			for ($i=0; $i<count($tag_paced); $i++){
				if ($tag_paced[$i][1] && $tag_paced[$i][2])	{
					if (strtolower($tag_paced[$i][1])=='name' || strtolower($tag_paced[$i][1])=='http-equiv') $tag_paced[$i][2] = strtolower($tag_paced[$i][2]);
					$result[strtolower($tag_paced[$i][1])] = $tag_paced[$i][2];
				}
			}
			return $result;
		}
		
		
		/**
		 * finding all tag. 
		 * returns list of easy-to-read tags
		 * $result - Html->tree
		 *
		 * @param Tag $obj
		 * @param array $results
		 * @return array
		 */
		function FindAllTags($obj,&$results){
			if (is_object($obj)){
				if ($obj->name=='meta') {
					if (!$obj->props['name'] && $obj->props['http-equiv']) $obj->props['name'] = $obj->props['http-equiv'];
					
					if (in_array($obj->name." ".$obj->props['name'],$this->aContentIgnoreTags)){
						$obj->props['content'] = '';
					}
					
					$results[$obj->name." ".$obj->props['name']][] = array('text'=>$obj->props['content'],'props'=>$obj->props);
				} elseif ($obj->props['alt']){
				 	$results["alt"][] = array('text'=>$obj->props['alt'],'props'=>'');
				} elseif($obj->name=='a'){
					$results['a'][] = array('text'=>$obj->props['alt'],'props'=>$obj->props);
				}
				if (is_array($obj->inner) && count($obj->inner)>0){
					
					for ($i=0; $i<count($obj->inner); $i++){
						//echo $obj->name."\n";
						if (is_string($obj->inner[$i])){
							$results[$obj->name][] = array('text'=>$obj->inner[$i],'props'=>$obj->props);
							//if (strlen($description)<200){
							
						}
						elseif (is_object($obj->inner[$i])){
							$this->FindAllTags($obj->inner[$i],$results);
						}
					}
				}else{
					$results[$obj->name][] = array('text'=>'','props'=>$obj->props);
				}
			}
		}
		
		/**
		 * taking phrases from provided string
		 * count words in phrase can bechanged
		 * this function uses by keyword density function
		 *
		 * @param string $str
		 * @param int $minwords_count
		 * @param array $arr_stop_words
		 * @return array
		 */
		function GetPhrase($str,$minwords_count=1){
			$result = array();
			if ($str!=''){
				
				//fins all words from phrase
				$words = $this->GetWords($str);
				
				$words_count = count($words);
				
				
				
				//delete not words from array
				
				for ($i=0; $i<$words_count; $i++){
					$buffer = '';
					if ($minwords_count+$i<=$words_count){
						for ($j=$i; $j<$minwords_count+$i; $j++){
							if ($buffer=='')$buffer .= $words[$j];
							else $buffer .= " ".$words[$j];
						}
						//echo "\n".$this->patternStopWords."::".$buffer."\n";
						if (!in_array($buffer,$result) && !preg_match($this->patternStopWords,$buffer))	$result[] = $buffer;
					}
				}
			}
			return $result;
		}
		
		/**
		 * taking possible words from string
		 * 	
		 * @param string $str
		 * @return array
		 */
		function GetWords($str){
			$res = array();
			$str = trim(mb_ereg_replace('[,.()|!|-]',' ',$str));
			$str = trim(mb_ereg_replace('["|\'|\?]','',$str));
			
			$str = mb_ereg_replace("[[:space:]]+"," ",$str);
			if ($str!=''){
				$words = explode(' ',$str);
				foreach ($words as $word){
					if (!$this->isNotWord($word)){
						$res[] = $word;
					}
				}
			}
			return $res;
		}
		
		/**
		 * uses by keyword density function for calculating total entry of phrase
		 *
		 * @param array $arr
		 * @param string $include_key
		 * @param int $min
		 * @return int
		 */
		function SumValues($arr,$include_key='',$min=0){
			$result = 0;
			foreach ($arr as $key=>$item){
				if (is_numeric($item[$include_key]) && $key!=$include_key){
					if ($item[$include_key]>=$min)	$result +=$item[$include_key];
				}
			}
			return $result;
		}
		
		/**
		 * finding keyword density for each keyword on page
		 * for that uses result of FindAllTags function
		 * $stat_prepare - validates what types of prases need to find
		 *
		 * @param array $all_tags
		 * @param array $stat_prepare
		 * @param array $stop_words
		 * @return array
		 */
		function KeywordsDensity($all_tags,$stat_prepare=array(1,2,3,4)){
			$words_stat = array();
			foreach ($all_tags as $tagname=>$list){
				$list_count = count($list);
				for ($j=0; $j<$list_count; $j++){
					if ($list[$j]['text']!=''){
						for ($k=0; $k<count($stat_prepare); $k++){
							$words = $this->GetPhrase($list[$j]['text'],$stat_prepare[$k]);
							//print_r($words);
							$words_count = count($words);
							for ($i=0; $i<$words_count; $i++){
								if (strlen($words[$i])>1){
									
									//check if it is not stop word
									$current_word = mb_strtolower($words[$i]);
									if (!$this->isStopWord($current_word,$aStopWords)){
									
									
										$words_stat[$stat_prepare[$k]][$current_word][$tagname]['count'] += mb_substr_count($list[$j]['text'],$words[$i]);
										if ($words_stat[$stat_prepare[$k]][$current_word][$tagname]['text']=='') $words_stat[$stat_prepare[$k]][$current_word][$tagname]['text'] .= $list[$j]['text'];
										else $words_stat[$stat_prepare[$k]][$current_word][$tagname]['text'] .= "; ".$list[$j]['text'];
										$words_stat[$stat_prepare[$k]][$current_word]['__total__'] = $this->SumValues($words_stat[$stat_prepare[$k]][$current_word],'count');
									}
								}
							}
							if (is_array($words_stat[$stat_prepare[$k]]))	$words_stat[$stat_prepare[$k]]['__total__'] = $this->SumValues($words_stat[$stat_prepare[$k]],'__total__',1);
						}
					}
				}
			}
			return $words_stat;
		}
		
		/**
		 * find stopword
		 *
		 * @param string $word
		 * @return bool
		 */
		function isStopWord($word){
			foreach ($this->aStopWords as $value){
				if (preg_match("/^".$value."$/",$word)) return true;
			}
			return false;
		}
		
		/**
		 * is word or not
		 * for checking uses $this->aNotWords array
		 *
		 * @param string $word
		 * @return bool
		 */
		function isNotWord($word){
			foreach ($this->aNotWords as $value){
				if (preg_match("/^(".$value.")*$/",$word)) return true;
			}
			return false;
		}
		
		/**
		 * helper function. for making sort
		 *
		 * @param array $word_arr
		 * @param int $min_value
		 * @return array
		 */
		function getMaxTotalEl(&$word_arr,$min_value=2){
			$max_val = 0;
			$max_key = '';
			$objmax = '';
			foreach ($word_arr as $key=>$val){
				
				if ($val['__total__']>=$min_value && $val['__total__']>$max_val){
					$max_val = $val['__total__'];
					$max_key = $key;
					
				}
			}
			
			$result = $word_arr[$max_key];
			
			unset($word_arr[$max_key]);
			if ($max_val) return array($max_key,$result);
			else return false;
		}
		
		/**
		 * sort keyword density statistic by keywords entry count
		 *
		 * @param array $word_arr
		 * @param int $min_value
		 * @return array
		 */
		function SortWordsSataistic($word_arr,$min_value=2){
			$result = array();
			$total = 0;
			while ($element = $this->getMaxTotalEl($word_arr,$min_value)){
				
				$result[$element[0]] = $element[1];
				$total += $element[1]['__total__'];
			}
			$result['__total__'] = $total;
			return $result;
		}
		
		function deleteMinEntries($words_stat,$min_value=2){
			$result = array();
			foreach ($words_stat as $key1=>$row){
				foreach ($row as $key2 => $val){
					if ($key2=='__total__') continue;
					if ($val['__total__']<$min_value){
						unset($words_stat[$key1][$key2]);
					}
				}
			}
			return $words_stat;
		}
		
		function helpConvertEncoding($str,$from,$to="UTF-8"){
			$result = iconv($from, 'UTF-8', $str);
			if (!$result) return $str;
			return $result;
		}
		
		function ConvertEncoding($word_stat,$from,$to){
			$result_array = array();
			foreach ($word_stat as $key=>$row){
				$new_key = $this->helpConvertEncoding($key,$from,$to);
				if (is_array($row)){
					$result_array[$new_key] = $this->ConvertEncoding($row,$from,$to);
				}else{
					$result_array[$new_key] = $this->helpConvertEncoding($row,$from,$to);
				}
			}
			return $result_array;
		}
	}
?>