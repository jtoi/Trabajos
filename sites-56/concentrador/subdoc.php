<?php define( '_VALID_ENTRADA' , 1);
include_once( 'configuration.php' );
require_once( 'include/mysqli.php' );
include_once( "admin/adminis.func.php" );

$temp = new ps_DB ();

$access_token = titToken();

$file_url = realpath("ficTitan/350/");
// echo "file_url=$file_url<br>";
$file = "i129_2012.jpg";
$titId = "256441";
// echo "mime=".mime_content_type($file_url."/".$file)."<br>";//exit;

// 	$file_url = "test.txt";  //here is the file route, in this case is on same directory but you can set URL too like "http://examplewebsite.com/test.txt"
$eol = "\r\n"; //default line-break for mime type
$BOUNDARY = md5(time()); //random boundaryid, is a separator for each param on my post curl function
$BODY=""; //init my curl body
$BODY.= '--'.$BOUNDARY. $eol; //start param header
// 	$BODY .= 'Content-Disposition: form-data; name="sometext"' . $eol . $eol; // last Content with 2 $eol, in this case is only 1 content.
// 	$BODY .= "Some Data" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
$BODY .= 'Content-Disposition: form-data; name="uploadType"' . $eol ; // last Content with 2 $eol, in this case is only 1 content.
$BODY .= 'Content-Type: text/html' . $eol. $eol;
$BODY .= "1" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
$BODY.= '--'.$BOUNDARY. $eol; // start 2nd param,
$BODY.= 'Content-Disposition: form-data; name="uploadFile1"; filename="'.$file.'"'. $eol ; //first Content data for post file, remember you only put 1 when you are going to add more Contents, and 2 on the last, to close the Content Instance
$BODY.= 'Content-Type: '. mime_content_type($file_url."/".$file) . $eol; //Same before row
// 	$BODY.= 'Content-Type: multipart/form-data' . $eol; //Same before row
$BODY.= 'Content-Transfer-Encoding: base64' . $eol . $eol; // we put the last Content and 2 $eol,
$BODY.= chunk_split(base64_encode(file_get_contents($file_url."/".$file))) . $eol; // we write the Base64 File Content and the $eol to finish the data,
$BODY.= '--'.$BOUNDARY .'--' . $eol. $eol; // we close the param and the post width "--" and 2 $eol at the end of our boundary header.

$ch = curl_init(); //init curl
$options = array(
		CURLOPT_USERAGENT			=> 'Mozilla/1.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0'
// 		,CURLOPT_URL 				=> "https://195.57.91.186:8555/APITest/Customer/$titId/Upload"
		,CURLOPT_URL 				=> "https://www.grupotitanes.es:8555/APITitanes/Customer/$titId/Upload"
		,CURLOPT_HTTPHEADER			=> array(
// 				'X_PARAM_TOKEN : Bearer'.$access_token, //custom header for my api validation you can get it from $_SERVER["HTTP_X_PARAM_TOKEN"] variable
				"Content-Type: multipart/form-data; boundary=".$BOUNDARY //setting our mime type for make it work on $_FILE variable
				,"Authorization: Bearer ".$access_token
		)
		,CURLOPT_SSL_VERIFYHOST		=> false
		,CURLOPT_SSL_VERIFYPEER		=> false
		,CURLOPT_FRESH_CONNECT 		=> true
		,CURLOPT_RETURNTRANSFER		=> true
		,CURLOPT_FOLLOWLOCATION		=> true
		,CURLOPT_POST				=> true
		,CURLOPT_POSTFIELDS			=> $BODY
);

print_r($options);
curl_setopt_array($ch, $options);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
// // 		'X_PARAM_TOKEN : 71e2cb8b-42b7-4bf0-b2e8-53fbd2f578f9', //custom header for my api validation you can get it from $_SERVER["HTTP_X_PARAM_TOKEN"] variable
// 		"Content-Type: multipart/form-data; boundary=".$BOUNDARY //setting our mime type for make it work on $_FILE variable
// 		,"Authorization: Bearer".$access_token
// 	)
// );
// curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/1.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0'); //setting our user agent
// curl_setopt($ch, CURLOPT_URL, "https://195.57.91.186:8555/APITest/Customer/$titId/Upload"); //setting our api post url
// // 	curl_setopt($ch, CURLOPT_COOKIEJAR, $BOUNDARY.'.txt'); //saving cookies just in case we want
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // call return content
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //navigate the endpoint
// curl_setopt($ch, CURLOPT_POST, true); //set as post
// curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY); // set our $BODY

$sale = curl_exec($ch);
$crlerror = curl_error($ch);

// echo "Sale=$sale<br>";
echo "Error=$crlerror<br>";

?>