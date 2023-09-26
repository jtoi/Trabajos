<?php

class banco {

	function __construct(){}

	function cambioCUC () {

		if (ini_get('allow_url_include') == 0) ini_set('allow_url_include', 1);
		if (ini_get('allow_url_fopen') == 0) ini_set('allow_url_fopen', 1);
		//	allow_url_include = 1;

		$handle = fsockopen("www.cubacurrency.com", 80, $errno, $errstr, 12);
		if (strlen($errstr) == 0) {

			fputs($handle, "GET /exchange_rates.html HTTP/1.0\r\n");
			fputs($handle, "Host: www.cubacurrency.com\r\n");
			fputs($handle, "Referer: http://www.cubacurrency.com\r\n");
			fputs($handle, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n");
				//echo $viart_xml;

				//$handle = fopen('http://www.banco-metropolitano.com/tasasn.htm', 'r');
				if ($handle) {
					while (!feof($handle)) {
						$buffer .= trim(fgets($handle))."|";
					}
					fclose($handle);
				} else {trigger_error('No habre la url', E_USER_WARNING);}

				// para banco nacional de cuba
				$buffers = substr(strip_tags($buffer), strripos(strip_tags($buffer), 'GBP'));
				$buffers = substr($buffers, 0,  strpos($buffers, 'CUC') -24);
				$buffers = str_replace('Canadian dollar', '%', $buffers);
				$buffers = str_replace('Swiss Franc', '%', $buffers);
				$buffers = str_replace('Japanese Yen (*)', '%', $buffers);
				$buffers = str_replace('American dollar (**)', '%', $buffers);
				$buffers = str_replace('Mexican Peso', '%', $buffers);
				$buffers = str_replace('Danish Krone', '%', $buffers);
				$buffers = str_replace('Norway Krone', '%', $buffers);
				$buffers = str_replace('Sweden Krone', '%', $buffers);
				$buffers = str_replace('Euro', '%', $buffers);
				$buffers = str_replace(' ', '', $buffers);
				$buffers = str_replace(',', '.', $buffers);

				$ArrSpl = explode('%',$buffers);
			//	return $buffers;
				$buffers = '';
				foreach($ArrSpl as $lin) {
					$sig = substr($lin, 0, 3);
					$val = substr($lin, 3);
					$buffers .= $sig.'||'.$val.'||';
				}

				return $buffers;
		}
		/*
		Para Banco Metropolitano
		$buffers = substr(strip_tags($buffer), strripos(strip_tags($buffer), 'CAD'));
		//echo $buffers;
		//echo "algo=".strripos($buffers, '(*)');
		$buffers = substr($buffers, 0, strripos($buffers, '|(*)'));
		while (strripos($buffers, '||')) {
			$buffers = str_replace('||', '|', $buffers);
		}
		$buffers = str_replace('ar|Can', 'ar Can', str_replace('ina|(*)', 'ina (*)', $buffers));
		return strip_tags($buffers);
		*/
	}

	function cambio($moneda) {
		$cambio = $this->cambioCUC();
		
//		$handle = @fopen("http://localhost/cubatravel/desc/cambio.txt", "r");
//		$cambio = fgets($handle);
		
		$arrVals = explode("||", $cambio);
		$cont = 0;
		foreach ($arrVals as $val){
			$cont++;
			if ($val == $moneda) {
				return $arrVals[$cont++];
			}
		}

		
	}


}
?>
