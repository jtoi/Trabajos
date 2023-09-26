<?
class gcc {
	function __construct() {
		
	}

	function get_ccy($ccy1, $ccy2, $value=1){
		$query = "$ccy1+$ccy2";
		$result = file_get_contents("http://www.google.com/search?q=$query&hl=us-EN");
		preg_match_all("/ = (.*?) /i",$result,$matches);
		if (isset($matches[1][0])){
			$val = (float) $matches[1][0];
			$ret = $val * $value;
		} else {
			$ret = 0;
		}
		return $ret;
	}
}
