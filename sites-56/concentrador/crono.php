<?php define( '_VALID_ENTRADA' , 1);

// require_once("admin/classes/SecureSession.class.php");
// $Session = new SecureSession(3600);
include_once( 'configuration.php' );
//include_once( '../../classes/entrada.php' );
require_once( 'include/mysqli.php' );
include( "admin/adminis.func.php" );
require_once( 'include/correo.php' );

$temp = new ps_DB ();
$correo = new correo;
$dirlocal = "/var/www/vhosts/administracomercios.com/httpdocs/ficTitan";
if ($_SERVER['HTTP_HOST'] == 'localhost') 
	$dirlocal = "/home/julio/www/concentrador/ficTitan";
$correoMi = '';
// exit;
//verifico que el token tenga menos de 24hrs
$access_token = titToken();
$evita = "920727, 954160, 960446, 961501, 846713, 951405, 911321";

// echo $access_token;


$correoMi = date('d/m/Y H:i:s').'<br>';

$temp->query("select count(distinct c.usuario, c.idtitanes) cant from tbl_aisFicheros f, tbl_aisCliente c where f.idcliente = c.id and f.subido = 0 and c.idtitanes is not null and f.fecha > 1596256047 and c.idtitanes not in ($evita)"); 
$correoMi .= "Faltan ".$temp->f('cant')." Clientes<br>";
$temp->query("select count(*) cant from tbl_aisFicheros f where f.subido = 0 and  f.fecha > 1596256047 ");
$correoMi .= "Faltan ".$temp->f('cant')." Ficheros<br>";


$q = "select distinct c.usuario, c.idtitanes from tbl_aisFicheros f, tbl_aisCliente c where f.idcliente = c.id and f.subido = 0 and c.idtitanes is not null and c.subfichero = 0 and f.fecha > 1596256047 and c.idtitanes not in ($evita) order by f.fecha desc limit 0,20";
$q = "select distinct c.usuario, c.idtitanes from tbl_aisFicheros f, tbl_aisCliente c where f.idcliente = c.id and f.subido = 0 and c.idtitanes is not null and f.fecha > 1596256047 and c.idtitanes not in ($evita) order by f.fecha desc limit 0,20";
$correoMi .= $q."\n<br>";
$temp->query($q);
$arrCli = $temp->loadRowList();
$correoMi .= json_encode($arrCli)."<br>";
for ($j = 0; $j < count($arrCli); $j++) {
$correoMi .= "cliente={$arrCli[$j][0]}<br>";
	if (subficheros2($arrCli[$j][0], $arrCli[$j][1])) {// se subieron los ficheros a Titanes salvo en la BD
		$correoMi .= "Subidos los ficheros a Titanes<br>";
	} else $correoMi .= "No se subieron los ficheros a Titanes idcliente = '{$ftp_dirAis[1]}'\n<br>";
}


function subficheros2($usuario, $titId){
	global $correoMi, $dirlocal, $temp;
	
	$smod = ini_get('safe_mode');
	ini_set('safe_mode', false);
	global $correo, $correoMi, $access_token, $lastUs;
	
	if ($lastUs != $usuario) {
		$lastUs = $usuario;
		$tipo = 1;
	} else $tipo++;
	$strId = '';
	
	$correoMi .= "usuario=$usuario\n<br>";
	$correoMi .= "titId=$titId\n<br>";
	$pase = false;
				
	$q = "select count(*) total from tbl_aisFicheros where subido = 0 and idcliente = (select id from tbl_aisCliente where idtitanes = $titId)";
	$temp->query($q);
	$cantFil = $temp->f('total');
	$correoMi .= "Cantidad de ficheros por subir del Cliente $usuario: ".$cantFil."<br>";
	
	$file_url = $dirlocal."/$usuario/";
	$correoMi .= "file_url=$file_url<br>";
	
	// 	$file_url = "test.txt";  //here is the file route, in this case is on same directory but you can set URL too like "http://examplewebsite.com/test.txt"
	$eol = "\r\n"; //default line-break for mime type
	$BOUNDARY = md5(time()); //random boundaryid, is a separator for each param on my post curl function
	$BODY=""; //init my curl body
	$BODY.= '--'.$BOUNDARY. $eol; //start param header
	// 	$BODY .= 'Content-Disposition: form-data; name="sometext"' . $eol . $eol; // last Content with 2 $eol, in this case is only 1 content.
	// 	$BODY .= "Some Data" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
	$BODY .= 'Content-Disposition: form-data; name="uploadType"' . $eol ; // last Content with 2 $eol, in this case is only 1 content.
	$BODY .= 'Content-Type: text/html' . $eol. $eol;
	$BODY .= "$tipo" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
// 	$BODY .= "1" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data

	if ($handle = opendir($file_url)) {
		$filecount = 0; 
		$files2 = glob( $file_url ."*" );

		$q = "select count(*) total, idcliente from tbl_aisFicheros where idcliente = (select id from tbl_aisCliente where idtitanes = $titId)";
		$temp->query($q);
		$cantFil = $temp->f('total');
		$idd = $temp->f('idcliente');
		
		if( $files2 ) { 
			$filecount = count($files2); 
		}
		$correoMi .= "Cant de ficheros en el directorio: $filecount comparado con la tabla $cantFil<br>";
		if ($cantFil > $filecount) {
			//$dirlocal = str_replace('borraficViejos.php', '',$_SERVER[SCRIPT_FILENAME])."ficTitan";
			$correoMi .= 'Borrando ficheros viejos<br>';

			$temp->query("select usuario, id from tbl_aisCliente where idtitanes = $titId");
			$correoMi .=  "select usuario from tbl_aisCliente where idtitanes = $titId"."<br>";
			$dirlocal .= "/".$temp->f('usuario');
			$idcliente = $temp->f('id');
			$correoMi .=  $dirlocal."<br>";

			$temp->query("select fichero, id from tbl_aisFicheros where idcliente = $idcliente");
			$arrFics = $temp->loadAssocList();

			$correoMi .=  json_encode($arrFics);

			for ($i = 0; $i < count($arrFics); $i++) {
				$dirlocUsr = $dirlocal."/".$arrFics[$i]['fichero'];
				if (!is_file($dirlocUsr)) {
					$temp->query("delete from tbl_aisFicheros where id = ".$arrFics[$i]['id']);
					$correoMi .= "delete from tbl_aisFicheros where id = ".$arrFics[$i]['id']."<br>";
				}
			}
			$correoMi .= "Revisar tabla de ficheros de $idd<br>";
			//$correo->todo(9, "Error", $correoMi);
		}
		$i = 1;
		$pase = false;
		while (false !== ($file = readdir($handle)) ) {
			if ($file != '..' && $file != '.') {
				// $correoMi .= "pase1<br>";
				$q = "select * from tbl_aisFicheros where fichero = '$file' and subido = 0 and idcliente = (select id from tbl_aisCliente where idtitanes = $titId)";
				$temp->query($q);
				if ($temp->num_rows() > 0 ){
					$correoMi .= $q."\n<br>";
					if (is_file($file_url."/".$file)) {
						$correoMi .= "Subiendo el fichero $file<br>";
						$pase=true;
						$correoMi .= "contador=$i<br>";
						if ($i > 5) break;

						if ($titId == 0) break;
						$q = "select id from tbl_aisFicheros where fichero = '$file' and idcliente = (select id from tbl_aisCliente where idtitanes = $titId)";
						$correoMi .= $q."\n<br>";
						$temp->query($q);
						$strId .= $temp->f('id').",";
						$correoMi .= "cadena=$strId<br>";

						$correoMi .= $file_url."/".$file."\n<br>";
						$BODY.= '--'.$BOUNDARY. $eol; // start 2nd param,
						$BODY.= 'Content-Disposition: form-data; name="uploadFile'.$i.'"; filename="'.$file.'"'. $eol ; //first Content data for post file, remember you only put 1 when you are going to add more Contents, and 2 on the last, to close the Content Instance
						$BODY.= 'Content-Type: '. mime_content_type($file_url."/".$file) . $eol; //Same before row
						// 	$BODY.= 'Content-Type: multipart/form-data' . $eol; //Same before row
						$BODY.= 'Content-Transfer-Encoding: base64' . $eol . $eol; // we put the last Content and 2 $eol,
					// 	$BODY.= chunk_split(base64_encode(file_get_contents($file_url."/".$file))) . $eol; // we write the Base64 File Content and the $eol to finish the data,
						$BODY.= file_get_contents($file_url."/".$file) . $eol; // we write the Base64 File Content and the $eol to finish the data,
						$i++;
					} else {
						// Si el fichero no se ha subido físicamente lo borro de la BD
						$q = "delete from tbl_aisFichero where fichero = '$file' and idcliente = (select id from tbl_aisCliente where idtitanes = $titId)";
						$correoMi .= "$q<br>";
						$temp->query($q);
					}
				}
			}
		}
	}
	
	$BODY.= '--'.$BOUNDARY .'--' . $eol. $eol; // we close the param and the post width "--" and 2 $eol at the end of our boundary header.
	//$correoMi .= "pase=$pase<br>";

	if ($pase===true)
	$correoMi .= "entra<br>";
		// muestraError("Envío de documentos a Titanes", "Body: ".$BODY."<br><br>Border: ".$BOUNDARY);
		$correoMi .= "cadenaotra=$strId<br>";
		if (strlen($strId)>2) {
			$correoMi .= "strId=".$strId."<br>";
			if (fichTitan($BODY, $BOUNDARY, $titId)) {
				$strId = ltrim(rtrim(str_replace(",,",",",$strId),','),',');
				$correoMi .= "strId2=".$strId."<br>";
				$q = "update tbl_aisFicheros set subido = 1, fecha_sub = ".time()." where id in ($strId)";
				$correoMi .= $q."\n<br>";
				$temp->query($q);

				//verifico si al cliente le queda algún fichero por subir
				$q = "select count(*) total from tbl_aisFicheros where idcliente = (select id from tbl_aisCliente where idtitanes = $titId) and subido = 0";
				$temp->query($q);
				$correoMi .= "$q<br>";
				$correoMi .= "Cant de ficheros por subir ".$temp->f('total')."<br>";
				
				if ($temp->f('total') == 0) {// si se subieron todos los documentos marco al cliente como que subió todo
					$q = "update tbl_aisCliente set subfichero = 0 where idtitanes = '$titId'";
					$temp->query($q);
					$correoMi .= $q."\n<br>";
				}
				return true;
			} else return false; 
		} else return false; 
		

}
muestraError ("Subida documentos");
/**
 * Para el envío de los mensajes de correo y la escritura en el log de la BD
 *
 * @param [text] $etiqueta
 * @param [text] $textoCorreo
 * @return void
 */
function muestraError ($etiqueta, $textoCorreo=null) {
	global $correo, $correoMi;
// 	echo $correoMi;
// 	$textoCorreo .= $etiqueta;
$correo->todo(52, $etiqueta, $textoCorreo." crono**** ".$correoMi);
	//if (stripos($etiqueta, "error") > -1) $correo->todo(52, $etiqueta, $textoCorreo." ** ".$correoMi);
	//else $correo->todo(53, $etiqueta, $textoCorreo." ** ".$correoMi);
// 	if (stripos($etiqueta, 'error')) exit;
// 	else return;
	return;
// 	exit;
}

?>
