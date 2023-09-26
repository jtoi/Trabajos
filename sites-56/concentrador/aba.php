<?PHP

//instantiate the class
include('include/class.html.parser.php');
$parser = new html_parser;

$options = array(
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_SSL_VERIFYPEER	=> false,
		CURLOPT_POST			=> false,
		CURLOPT_VERBOSE			=> true,
		CURLOPT_URL				=> 'https://portal4.lacaixa.es/apl/divisas/index_es.html'
);
$ch = curl_init();
curl_setopt_array($ch , $options);
$output = curl_exec($ch);
curl_close($ch);
$parser->loadString($output);

$parseResult = $parser->processTagName('a',false);


$options = array(
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_SSL_VERIFYPEER	=> false,
		CURLOPT_POST			=> false,
		CURLOPT_VERBOSE			=> true,
		CURLOPT_URL				=> 'https://portal4.lacaixa.es/apl/divisas/verTodos_es.html?JSESSIONID='.
	str_replace('/apl/divisas/verTodos_es.html?JSESSIONID=', '', 
			$parseResult['[html][body][div3][div3][div1][div1][article3][div][p][span][a]']['attr'][1]['value'])
);
$ch = curl_init();
curl_setopt_array($ch , $options);
$output = curl_exec($ch);
curl_close($ch);
$parser->loadString($output);

$parseResult = $parser->processDocument();
print_r($parseResult);

$cade = '[html][body][div3][div3][div1][div1][article][div][table1][tbody]';
for ($i=1;$i<100;$i++){
	if(is_array($parseResult[$cade."[tr$i][td1]"])) {
		if (strstr($parseResult[$cade."[tr$i][td1]"]['text'][0],'USD')) echo $parseResult[$cade."[tr$i][td2]"]['text'][0];
	} else break;
}


?>
