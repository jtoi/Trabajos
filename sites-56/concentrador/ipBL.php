<?php

define( '_VALID_ENTRADA', 1 );
require_once( 'admin/classes/entrada.php' );
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
$temp = new ps_DB;
$doc = new DOMDocument;
$doc->preserveWhiteSpace = FALSE;


for ($i = 1; $i<8;$i++) {
	$filename = "proxyLN$i.html";
	$fp = fopen($filename,'w');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"http://www.proxy4free.com/list/webproxy$i.html");
	curl_setopt($ch, CURLOPT_TIMEOUT, 90);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec ($ch);
	
	@$doc->loadHTMLFile($filename);
	$anchor_tags = $doc->getElementsByTagName('a');
	foreach ($anchor_tags as $tag) {
		if (strpos($tag->nodeValue, ".")) {
			$ip = gethostbyname($tag->nodeValue);
			$q = "select count(*) total from tbl_ipBL where ip = '$ip'";
			$temp->query($q);
			if ($temp->f('total') == 0) {
				$q = "insert into tbl_ipBL (ip,url,fecha) values ('$ip','".$tag->nodeValue."', ".time().")";
				$temp->query($q);
			}
		}
	}
	
	curl_close ($ch);
	fclose($fp);
}


?>
