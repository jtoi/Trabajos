<?php define( '_VALID_ENTRADA', 1 );
require_once("../../classes/SecureSession.class.php");
$Session = new SecureSession(7500);
include_once( '../../../configuration.php' );
include_once( '../../classes/entrada.php' );
require_once( '../../../include/mysqli.php' );
require_once( '../../../include/hoteles.func.php' );


$temp = new ps_DB();
$d = $_REQUEST;
$dirPart = "../../../ficTitan/";

if (_MOS_CONFIG_DEBUG) {
// $d['dir'] = '350';
// $d['usr'] = '556';
// $d['fun'] = 'econ';
}
$desc = "pag = ejecutaCore || ";
foreach($_REQUEST as $nom => $valor) {
	if (is_array($valor)) $valor = implode (', ', $valor);
	$desc .= "$nom = ".htmlspecialchars($valor, ENT_QUOTES)." || ";
}
//$desc = "pag = ejecutaCore ".implode(' || ', $d);
if ($_SESSION['id']) {
	$query = "insert into tbl_baticora (idadmin, texto, fecha) values (".$_SESSION['id'].", '$desc', ".time().")";
	$temp->query($query);
} else echo "<script language='text/javascript'>window.open('index.php?componente=core&pag=logout', '_self')</script>";


if ($d['fun'] == 'econ') {
	$q = "select CONVERT(CAST(nombre as BINARY) USING latin1), email from tbl_admin where idadmin = ".$d['usr'];
	$error = $q;
	$temp->query($q);
	$arrsal = $temp->loadRow();
	echo json_encode(array("cont"=>$arrsal, "error"=>$error));
} elseif ($d['fun'] == 'paslim'){
	
	if ($d['est'])  $q = "insert into tbl_colPaisPasarelDeng values (null, '".$d['pais']."', '".$d['pas']."')";
	else $q = "delete from tbl_colPaisPasarelDeng where idpais = '".$d['pais']."' and idpasarela = ".$d['pas'];
	
	$temp->query($q);
	if ($temp->getErrorMsg()) $error = $temp->getErrorMsg();
	
	$query = "insert into tbl_baticora (idadmin, texto, fecha) values (".$_SESSION['id'].", '".str_replace('"', "", str_replace("'", "", $q))."', ".time().")";
	$temp->query($query);
	if ($temp->getErrorMsg()) $error = $temp->getErrorMsg();
	
	echo json_encode(array("error"=>$error));
	
} elseif ($d['fun'] == 'ftpsend'){
	$dir = $d['dir'];
	$fic = $d['fic'];
	$arrFic = explode(";", $fic);
// 	$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
// 	$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
// 	$header[] = "Cache-Control: max-age=0";
// 	$header[] = "Content-Type: application/json;charset=UTF-8";
// 	$header[] = "Connection: keep-alive";
// 	$header[] = "Keep-Alive: 300";
// 	$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
// 	$header[] = "Accept-Language: en-us,en;q=0.5";
// 	$header[] = "Pragma: "; // browsers keep this blank.
	
	$q = "select idtitanes from tbl_aisCliente where usuario = '$dir'";
	$temp->query($q);
	$titId = $temp->f('idtitanes');
	
	if (is_array($arrFic)) {
		foreach ($arrFic as $fichero) {
			postFile(realpath($dirPart.$file_url),$titId,$fichero);
// 			$options = array(
// 					CURLOPT_RETURNTRANSFER	=> true,
// 					CURLOPT_SSL_VERIFYHOST	=> false,
// 					CURLOPT_SSL_VERIFYPEER	=> false,
// 					CURLOPT_POST			=> true,
// 					CURLOPT_VERBOSE			=> true,
// 					CURLOPT_URL				=> "https://195.57.91.186:8555/APITest/Customer/$titId/Upload",
// 					CURLOPT_POSTFIELDS		=> "grant_type=password&username=".urlencode('info@amfglobalitems.com')."&password=amfpass1234",
// 					CURLOPT_HEADER			=> false,
// 					CURLOPT_HTTPHEADER		=> $header,
// 					CURLOPT_RETURNTRANSFER	=> 1,
// 					CURLOPT_FOLLOWLOCATION	=> 1,
// 					CURLOPT_CONNECTTIMEOUT	=> 100,
//  					CURLOPT_USERAGENT		=> "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.54"
// 			);
			
// 			$ch = curl_init();
// 			curl_setopt_array($ch , $options);
// 			$salida = curl_exec($ch);
// 			// 						echo "error=".curl_errno($ch);
// 			if (curl_errno($ch)) $correoMi .=  "Error en la resp de Titanes:".curl_strerror(curl_errno($ch))."<br>\n";
// 			$crlerror = curl_error($ch);
// 			// 						echo "otroerror=".$crlerror;
// 			if ($crlerror) {
// 				$correoMi .=  "Error en la resp de Titanes:".$crlerror."<br>\n";
// 			}
// 			$curl_info = curl_getinfo($ch);
// 			curl_close($ch);
			//			echo "<br><br>salida=".$salida."<br><br>";
			$arrCurl = json_decode($salida);
		}
	}
	
	echo json_encode(array("cont"=>$arrsal, "error"=>$error));
}

if ($d['fun'] == 'ftpver'){
	$arrsal = array();
	$dir = $d['dir'];
	
	if ($dir == '') {
		$q = "select usuario, subfichero from tbl_aisCliente where subfichero = 1 order by usuario";
		$temp->query($q);
		$arrNS = $temp->loadRowList();
		$q = "select usuario, subfichero from tbl_aisCliente where subfichero = 0 order by fecha desc";
		$temp->query($q);
		$arrSS = $temp->loadRowList();
		$contents = array_merge($arrNS,$arrSS);
	} else {
		$q = "select f.id, f.fichero, f.subido from tbl_aisFicheros f, tbl_aisCliente c ".
				" where f.idcliente = c.id and c.usuario = '$dir' and f.subido = 0".
				" order by f.fichero";
		$temp->query($q);
		$arrFNS = $temp->loadRowList();
// 		$q = "select f.id, f.fichero, f.subido from tbl_aisFicheros f, tbl_aisCliente c ".
// 				" where f.idcliente = c.id and c.usuario = '$dir' and f.subido = 1".
// 				" order by f.fichero";
// 		$temp->query($q);
// 		$arrFSS = $temp->loadRowList();
// 		$contents = array_merge($arrFNS,$arrFSS);
		$contents = array_merge($arrFNS);
	}
	
// 	$ftp_serverAis = 'ulisestoirac.com';
// 	$ftp_user_nameAis = 'ulises';
// 	$ftp_user_passAis = 'cPanel*6306';
// 	if (strstr($dir, '/..')) {
// 		$strdir = str_replace('/..', '', $dir);
// 		$arrDir = explode('/', $strdir);
// 		array_pop($arrDir);
// 		$dir = implode("/", $arrDir);
// 	}
// 	$ftp_dirAis = $dir;
	// $ftp_serverAis = '87.106.62.52';
	// $ftp_user_nameAis = 'root';
	// $ftp_user_passAis = 'p*RAh*r@13*2';
// 	$ftp_dirAis = '/var/www/vhosts/amfdesarrolloweb.com/httpdocs/cubashoppingcenter/images';
	$ftp_dirAis = _ESTA_URL."/ficTitan/".$dir;
	
	
	//Revisión de los ficheros en el FTP de AIS
// 	$conn_id = ftp_connect($ftp_serverAis);
// 	$login_result = ftp_login($conn_id, $ftp_user_nameAis, $ftp_user_passAis);
// 	if ((!$conn_id) || (!$login_result)) {
// 		$error = "FTP connection has failed!";
// 		echo "Attempted to connect to $ftp_serverAis for user $ftp_user_nameAis";
// 		exit;
// 	} else {
// 		echo "Connected to $ftp_serverAis, for user $ftp_user_nameAis";
// 	}
// 	ftp_chdir($conn_id, "/public_html".$ftp_dirAis);
	
// 	$contents = ftp_nlist($conn_id, ".");
	
	for ($i=0;$i<count($contents);$i++) {// Para los directorios
		if ($dir == '') {
			$value = $contents[$i][0];
			$vaaa = "../../../ficTitan/".$contents[$i][0];
			$vaaa = is_dir($vaaa);
// 			$vaaa = is_dir("/home/julio/www/concentrador");
			if (is_dir($dirPart.$value))
				$arrsal[] = "<a class=\"dirRec\" onclick=\"leedir('$value')\" >$value</a><br>";
			
			
		} else {
			$value = $contents[$i][1];
			$values = $ftp_dirAis."/mini/".$contents[$i][1];
			if ($value != '.' ) {
				if (strpos($value, ".")) {
					if ($value != "..") {
						$id = str_replace('.jpg', '', $contents[$i][1]);
						$arrsal[] = "<img src=\"$values\" class=\"dirRec\" id=\"$id\" onclick=\"enviafichero('".$contents[$i][1]."')\" /><br>";
					} else $arrsal[] = "<a class=\"dirRec\" onclick=\"leedir('$values')\">UP</a><br>";
				}
			}
		}
	}
	
	if ($ftp_dirAis != '/images/trabajos') {
		foreach ($contents as $key => $value) {// Para los ficheros
			if (strpos($value, ".") > 3)
				$arrsal[] = "<a href='http://$ftp_serverAis$ftp_dirAis/$value' target='_new'>$value</a><br>";
		}
	}
// 	print_r($contents);

// 	$arrsal = $arrNS;
	
	ftp_close($conn_id);
	echo json_encode(array("cont"=>$arrsal, "error"=>$error));
}

function postFile($file_url,$titId,$fichero) {
	
// 	$file_url = "test.txt";  //here is the file route, in this case is on same directory but you can set URL too like "http://examplewebsite.com/test.txt"
	$eol = "\r\n"; //default line-break for mime type
	$BOUNDARY = md5(time()); //random boundaryid, is a separator for each param on my post curl function
	$BODY=""; //init my curl body
	$BODY.= '--'.$BOUNDARY. $eol; //start param header
// 	$BODY .= 'Content-Disposition: form-data; name="sometext"' . $eol . $eol; // last Content with 2 $eol, in this case is only 1 content.
// 	$BODY .= "Some Data" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
	$BODY .= 'Content-Disposition: form-data; name="uploadType"' . $eol ; // last Content with 2 $eol, in this case is only 1 content.
	$BODY .= "Some Data" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
	$BODY.= '--'.$BOUNDARY. $eol; // start 2nd param,
	$BODY.= 'Content-Disposition: form-data; name="uploadFile1"; filename="'.$fichero.'"'. $eol ; //first Content data for post file, remember you only put 1 when you are going to add more Contents, and 2 on the last, to close the Content Instance
	$BODY.= 'Content-Type: '. mime_content_type($fichero) . $eol; //Same before row
// 	$BODY.= 'Content-Type: multipart/form-data' . $eol; //Same before row
	$BODY.= 'Content-Transfer-Encoding: base64' . $eol . $eol; // we put the last Content and 2 $eol,
	$BODY.= chunk_split(base64_encode(file_get_contents($file_url))) . $eol; // we write the Base64 File Content and the $eol to finish the data,
	$BODY.= '--'.$BOUNDARY .'--' . $eol. $eol; // we close the param and the post width "--" and 2 $eol at the end of our boundary header.

	$ch = curl_init(); //init curl
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'X_PARAM_TOKEN : 71e2cb8b-42b7-4bf0-b2e8-53fbd2f578f9' //custom header for my api validation you can get it from $_SERVER["HTTP_X_PARAM_TOKEN"] variable
			,"Content-Type: multipart/form-data; boundary=".$BOUNDARY) //setting our mime type for make it work on $_FILE variable
	);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/1.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0'); //setting our user agent
	curl_setopt($ch, CURLOPT_URL, "https://195.57.91.186:8555/APITest/Customer/$titId/Upload"); //setting our api post url
// 	curl_setopt($ch, CURLOPT_COOKIEJAR, $BOUNDARY.'.txt'); //saving cookies just in case we want
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // call return content
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //navigate the endpoint
	curl_setopt($ch, CURLOPT_POST, true); //set as post
	curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY); // set our $BODY

	$sale = curl_exec($ch);
	$crlerror = curl_error($ch);
	return true; //curl_exec($ch); // start curl navigation

// 	print_r($response); //print response

}
?>
