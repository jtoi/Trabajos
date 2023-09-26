<?php
include('include/class.html.parser.php');
$parser = new html_parser;

$options = array(
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_SSL_VERIFYPEER	=> false,
		CURLOPT_POST			=> false,
		CURLOPT_VERBOSE			=> true,
		CURLOPT_URL				=> 'https://www.bsmarkets.com/cs/Satellite?cid=1191409940341&pagename=BSMarkets2%2FPage%2FPage_Interna_WFG_Template&WEB=0&SITE=null&seccion=fixings&idioma=es&portal='
);
$ch = curl_init();
curl_setopt_array($ch , $options);
$output = curl_exec($ch);
curl_close($ch);
$parser->loadString($output);
$parseResult = $parser->processDocument();

// print_r($parseResult);
$tiron = 0;

$cade = '[html1][body][div2][div3][div][ul][li3][div][ul][li][div][li][div][li][div][li2][div][ul][li][div][li][div1][li][div2][div][div2][div1][div][div2][table][tbody]';
for ($i=1;$i<100;$i++){
// 	print_r($parseResult[$cade."[tr$i][td1][a]"]);
	if(is_array($parseResult[$cade."[tr$i][td1][a]"])) {
// 	echo $parseResult[$cade."[tr$i][td1][a]"]['attr'][0]['value']." - pasa2";
		$tiron = 0;
		if (strstr($parseResult[$cade."[tr$i][td1][a]"]['attr'][0]['value'],'USD')) echo $parseResult[$cade."[tr$i][td2]"]['text'][0];
	} else {
		if ($tiron == 3) break; else $tiron++;
	}
}

?>