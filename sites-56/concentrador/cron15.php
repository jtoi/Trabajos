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


//libero las ipbloqueadas luego de 30 min
$temp->query("update tbl_ipbloq set bloqueada = 0, fecha_desbloq = unix_timestamp(), desbloq_por = 10 where (unix_timestamp() - fecha) > (45*60)");


$dirlocal = "/var/www/vhosts/administracomercios.com/httpdocs/ficTitan";
if ($_SERVER['HTTP_HOST'] == 'localhost') 
	$dirlocal = "/home/julio/www/concentrador/ficTitan";
$correoMi = 'cron15OTRO<br>';
$cantH = 5; //cantidad de horas que trato de subir los ficheros de los Clientes
$tamMaxFic = '6291556'; //tamaño máximo permitido para los ficheros 6MB

//verifico que el token tenga menos de 24hrs
$access_token = titToken();

// echo $access_token;

// crea el directorio local donde estarán todos los usuarios de AIS
if (!is_dir($dirlocal)) mkdir($dirlocal, 0755);
// muestraError ("interrupcion1");

$arrsal = array();
//recorre el listado de clientes buscando los que se inscribieron hace menos de 5 horas
// y aún no se les ha subido los ficheros 
$q = "select usuario, id, idtitanes from tbl_aisCliente where idtitanes is not null and subfichero = 1 and borrficheros = 0 and bloq = 0 and ficgrandes = 0 and fecha > ".(time()-($cantH*60*60));
$correoMi .= $q."<br>";
$temp->query($q);
$arrDir = $temp->loadRowList();

$correoMi .= json_encode($arrDir). "<br><br>";

$q = "select usuario, id, idtitanes from tbl_aisCliente c where idtitanes is not null and c.fechaAltaCimex > (unix_timestamp() - 60*60*24) and id not in (select id from tbl_aisCliente c where c.fechaAltaCimex > (unix_timestamp() - 60*60*24) and (select count(*) from tbl_aisFicheros f where f.idcliente = c.id) > 0) and ficgrandes = 0 and bloq = 0";
$correoMi .= $q . "<br>";
$temp->query($q);
$arrOtr = $temp->loadRowList();

$correoMi .= json_encode($arrOtr) . "<br><br>";
for ($i=0; $i<count($arrOtr); $i++){
	if (!in_array($arrOtr[$i], $arrDir)) $arrDir[] = $arrOtr[$i];
}
// $arrDir = array_merge($arrDir, $arrOtr);
$correoMi .= json_encode($arrDir) . "<br><br>";


$eco = false;
$mini = false; // deshabilitado la miniaturización de ficheros
// trigger_error ("interrupcion2 $q", E_USER_WARNING);

for ($i=0; $i<count($arrDir);$i++) {
	$q = "select count(id) total from tbl_aisFicheros where idcliente = '{$arrDir[$i][1]}' and subido = 0";
	$temp->query($q);
	$total = $temp->f('total');
	$correoMi .= "Cant de ficheros por subir ".$total."\n<br>";
		
	if ($total == 0) {// si se subieron más de dos ficheros pongo al Cliente como que se subieron todos los documentos
		$q = "update tbl_aisCliente set subfichero = 0 where id = '{$arrDir[$i][1]}'";
		$temp->query($q);
		$correoMi .= $q."\n<br>";
	}
}

/**
 * Revisa si hay operaciones lanzadas a las Agencias
 * que las mismas no hayan preguntado por la url, 
 * si esto ocurrre es que hay problemas en la agencia y me envía correo
 */
// $q = "select t.idtransaccion, a.url from tbl_transacciones t, tbl_pasarela p, tbl_agencias a where a.id = p.idagencia and t.estado = 'P' and p.idcenauto in (16,17,18) and t.pasarela = p.idPasarela and fechaAgen = 0 and t.fecha <= ".(time()-60*60)." limit 0, 3";
// $temp->query($q);
// $arrAgen = $temp->loadAssocList();
// if (count($arrAgen) > 0) {
// 	$tex = "Las Agencias relacionadas a continuación no han contestado a las operaciones siguientes:<br><br>";
// 	for ($i=0; $i<count($arrAgen); $i++){
// 		$url = str_replace("/paid", "", $arrAgen[$i]['url']);
// 		$tex .= "- Operación ".$arrAgen[$i]['idtransaccion']." cursada por la Agencia <a href='".$url."'>$url</a><br>";
// 	}
// 	$tex .= "<br>Revisar si están trabajando";
// 	$correo->todo(52, "Agencias caídas", $tex);
// }


if (count($arrDir) > 0) {

	//Revisión de los ficheros en el FTP de AIS 
	$ftp_serverAis = '82.223.110.245';
	$ftp_user_nameAis = 'www';
	$ftp_user_passAis = 'A1sr3m3s4s*';
	// $ftp_serverAis = '82.223.109.187';
	// $ftp_user_nameAis = 'julio';
	// $ftp_user_passAis = 'Jul10*';
	// $ftp_serverAis = 'ftp.aisremesascuba.com';
	// $ftp_user_nameAis = 'bidaiondo';
	// $ftp_user_passAis = '@hg*X:a2013$';
	$ftp_dir = "";
	$conn_id = ftp_connect($ftp_serverAis);
	$login_result = ftp_login($conn_id, $ftp_user_nameAis, $ftp_user_passAis);
	
	if ((!$conn_id) || (!$login_result)) muestraError("Error: Falló la conexión al FTP de AIS!");
	else {
		$correoMi .= "Se conecta al FTP\n<br>";
		foreach ($arrDir as $ftp_dirAis) { //para cada usuario cargado de la base de datos que aún no se le han subido todos los ficheros
			$grande = 0;
			$texto = '';
			$arrFlies = $contents = (array) null;
			$correoMi .= "Trabaja con el usuario ".$ftp_dirAis[0]."\n<br>";

			$conn_id = ftp_connect($ftp_serverAis);
			$login_result = ftp_login($conn_id, $ftp_user_nameAis, $ftp_user_passAis);
			if ($valeFTP = ftp_chdir($conn_id, $ftp_dir.$ftp_dirAis[0])) {
				ftp_pasv($conn_id, true);
				$contents = ftp_nlist($conn_id, ".");
				
				foreach ($contents as $key => $value) {
					$correoMi .= "key=$key => value=$value\n<br>";
				}
				
				$dirlocUsr = $dirlocal."/".$ftp_dirAis[0];
				
				//si el directorio local no existe lo creo
				$correoMi .= "existe directorio local $dirlocUsr?-".is_dir($dirlocUsr)."\n<br>";
				if (!is_dir($dirlocUsr)) {
					$dime = mkdir($dirlocUsr, 0777);
					$correoMi .= "crea directorio local-".$dime."\n<br>";
				}

				//actualiza la hora del directorio
				touch($dirlocUsr);
				
				//saco toda la info de los ficheros de ese directorio para compararla con los del FTP
				if ($dh = opendir($dirlocUsr)) {
					while (($fic = readdir($dh)) !== false) {
						//$correoMi .= "tamano del fichero= ". filesize($dirlocUsr . "/" . $fic)."<br>";
						if ($fic != "." && $fic != "..")
								$arrFlies[] = array("filename" => $fic, "size" => filesize($dirlocUsr."/".$fic), "modificado" => filemtime($dirlocUsr."/".$fic));
						
					}

					foreach ($contents as $key => $value) {// Revisa el contenido del directorio
						$ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
						
						// extensiones permitidas
//						$allowed = array('jpeg', 'pdf', 'jpg');
						$allowed = array('pdf', 'jpg', 'JPG', 'PDF');
						
						if (in_array($ext, $allowed)) { //las extensiones son las adecuadas
							$hash = md5_file("ftp://$ftp_user_nameAis:$ftp_user_passAis@$ftp_serverAis/" . $ftp_dirAis[0] . "/" . $value);
							$correoMi .= "Hash del fichero: ". $hash."\n<br>";
							
// 							if ($mime == 'image/jpeg' || $mime == 'application/pdf' || $ext == 'pdf') {//el contenido es adecuado
							
								$correoMi .= "Trabaja con el fichero ".$value."\n<br>";
								$q = "select count(*) total from tbl_aisFicheros where hash = '". $hash."' and idcliente = '{$ftp_dirAis[1]}'";
								$correoMi .= "$q<br>";
								$temp->query($q);
								if ($temp->f('total') == 0) {//el fichero no se ha subido, se sube ahora al concentrador
									$j=0;
									$subd = $desc = false; //
									while (!$desc && $j<3){ // intento la descarga a nuestro server del fichero desde AIS
										$correoMi .= "Intento de descarga $j \n<br>";
										ftp_pasv($conn_id, true);
										$filSize = ftp_size($conn_id, "/".$ftp_dirAis[0] . "/" . $value);
										if ($filSize <= $tamMaxFic) {
											if (ftp_get($conn_id, $dirlocUsr."/".$value, $value, FTP_BINARY)) {
												$correoMi .= "Fichero descargado<br>";

												$q = "select count(*) total from tbl_aisFicheros where fichero = '$value' and idcliente = '{$ftp_dirAis[1]}'";
												$correoMi .= $q."\n<br>";
												$temp->query($q);
												if ($temp->f('total') == 0) {
													$eco = true;
													$q = "insert into tbl_aisFicheros values (null,'{$ftp_dirAis[1]}','$value','0','".time(). "','".time()."', '$hash')";
													$temp->query($q);
												}
												$correoMi .= "Subido el fichero $value\n<br>$q\n<br>";
												$subd = true;

											} else {
												$correoMi .= "El fichero no ha podido ser descargado<br>";
											}
										} else {
											$correoMi .= "FICHERO - " . "/" . $ftp_dirAis[0] . "/" . $value." Tamaño del fichero $value= ". number_format($filSize / 1048576, 2) ."<BR>";
											$correoMi .= "NO SE DESCARGO EL FICHERO POR TAMANO<br>";
											$texto .= "  = {$value} con un tamaño de ". number_format($filSize / 1048576, 2) ." MB <br>";
											$grande = 1;
											break;
										}
										$j++;
									}
								} else $correoMi .= "El fichero ya está subido, saltamos este<br>";
// 							} else muestraError("Error en la subida del fichero (mime) $value $valeFTP del usuario ".$ftp_dirAis[0]);
						} else {
							if ($ext != 'txt' && $ext != '')
								muestraError("Error en la subida del fichero $value $valeFTP del usuario ".$ftp_dirAis[0]." la extensión no es la adecuada");
						}
					}
					//verificando que hayan subido todos los ficheros que dice la base de datos
					$q = "select count(*) total from tbl_aisFicheros where idcliente = '{$ftp_dirAis[1]}'";
					$correoMi .= $q."<br>";
					$temp->query($q);
					$canDB = $temp->f('total');

					$canFic = count(glob($dirlocUsr.'/{*.jpg,*.JPG,*.pdf,*.PDF}', GLOB_BRACE));
					$correoMi .= "cantBD= $canDB - cantCarpeta= $canFic<br>";

					if ($canDB > $canFic && $grande == 0) {
						$q = "delete from tbl_aisFicheros where idcliente = '{$ftp_dirAis[1]}'";
						$correoMi .= "$q<br>";
						// $temp->query($q);
						muestraError("Error en la subida desde FX", $correoMi);
					}

					closedir($dh);
				}
			} else {
				$q = "update tbl_aisCliente set subfichero = 0 where usuario = '".$ftp_dirAis[0]."'";
// 				$temp->query($q);
				$correoMi .= $q."<br>";
				muestraError("Error en la subida de ficheros AIS al Concentrador $valeFTP $sale $crlerror");
			}
			$subd = true;

			$q = "select * from tbl_aisFicheros where subido = 0 and idcliente = '{$ftp_dirAis[1]}'";
			$temp->query($q);
			if ($temp->num_rows()) {//el fichero no se ha subido, se sube ahora a Titanes
				
			$correoMi .= "se subieron los ficheros al Concentrador<br>";
			}
			if (strlen($texto)) {
				$q = "select concat(nombre, ' ', papellido, ' ', sapellido, ' (', usuario, ')') cliente from tbl_aisCliente where id = '{$ftp_dirAis[1]}'";
				$temp->query($q);
				$texto = "Hola Tama<br><br>El remitente {$temp->f('cliente')} ha intentado subir los siguientes ficheros que sobrepasan el límite de tama&ntilde;o:<br><br>".$texto;
				$correo->to('posicionamientoweb@bidaiondo.com');
				$correo->todo(52, 'Remitente con Fichero de más de 6MB ', $texto);
				$temp->query("update tbl_aisCliente set subfichero = 0, ficgrandes = 1 where id = '" . $ftp_dirAis[1] . "'");
				$correoMi .= "update tbl_aisCliente set subfichero = 0, ficgrandes = 1 where id = '" . $ftp_dirAis[1] . "'<br>";
			}
		}
	}
	ftp_close($conn_id);
}


//calculo el hash de los ficheros que no lo tengan aún
if (1==0) { //Detuve esto hasta nuevo aviso, mantenemos el cálculo cuando se suben los documentos
	$usuario = 'MB';
	$correoMi .= "<br>Cálculo del HASH de los ficheros<br>";
	$q = "select distinct c.usuario, c.id from tbl_aisFicheros f, tbl_aisCliente c where c.id = f.idcliente and (f.hash is null or f.hash = '')  order by c.fecha desc limit 0,18";
	$q = "select distinct c.usuario, c.id from tbl_aisFicheros f, tbl_aisCliente c where c.id = f.idcliente and c.usuario in ('$usuario') order by c.fecha desc limit 0,18";
	$correoMi .= $q . "<br>";
	$temp->query($q);
	$arrUSR = $temp->loadRowList();
	$directorio = "/var/www/vhosts/administracomercios.com/httpdocs/ficTitan/";
	$correoMi .= json_encode($arrUSR) . "<br>";
	foreach ($arrUSR as $uusr) {
		$q = "select fichero, id from tbl_aisFicheros where idcliente = " . $uusr[1] ." order by fecha desc";
		$correoMi .= $q . "<br>";
		$correoMi .= "<br>" . $uusr[0] . "<br>$q<br>";
		$temp->query($q);
		$arrFIC = $temp->loadRowList();
		foreach ($arrFIC as $fich) {
			$dirFic = $directorio . $uusr[0] . "/" . $fich[0];
			if (file_exists($dirFic)) {
				if (!$hasc = md5_file($dirFic)) $correoMi .= "Error al obtener el md5<br>";
				$fsize = filesize($dirFic);
				$correoMi .=  $fich[0] . " -> $hasc -> tamaño: $fsize<br>";

				//borro los ficheros que tienen tamaño 0
				if ($fsize == 0 || $fsize == '' || $fsize == null) {
					$q = "delete from tbl_aisFicheros where id =  " . $fich[1];
					$correoMi .= $q . "<br>";
					$temp->query($q);
					if (!unlink($dirFic)) $correoMi .= "Error al borrar el fichero por tamaño<br>";;
				}

				//verifica si hay algún otro fichero de ese usuario con ese hash
				$q = "select count(*) total from tbl_aisFicheros where idcliente = " . $uusr[1] . " and hash = '" . $hasc . "'";
				$correoMi .= $q . "<br>";
				$temp->query($q);
				if ($temp->f('total') == 0) { //si no lo hay actualiza el fichero con el hash
					$q = "update tbl_aisFicheros set hash = '$hasc' where id = " . $fich[1];
					$correoMi .= $q . "<br>";
					$temp->query($q);
				} else { //si se encuentra ya en la BD lo borro de ambos lugares
					$q = "delete from tbl_aisFicheros where id =  " . $fich[1];
					$correoMi .= $q . "<br>";
					$temp->query($q);
					if (!unlink($dirFic)) $correoMi .= "Error al borrar el fichero por repeticion<br>";;
				}
			} else { // Si el fichero no existe en el disco duro le pongo NE en la BD
				$correoMi .= "El fichero $dirFic no existe en el HD<br>";
				$q = "delete from tbl_aisFicheros where id =  " . $fich[1];
				$temp->query($q);
			}
		}
	}
}
// echo $correoMi;

muestraError("Subida documentos - 1");

$correoMi = '';

$evita = "920727, 954160, 960446, 961501, 846713, 951405, 911321";
$temp->query("select count(distinct c.usuario, c.idtitanes) cant from tbl_aisFicheros f, tbl_aisCliente c where f.idcliente = c.id and f.subido = 0 and c.idtitanes is not null and f.fecha > " . (time() - ($cantH * 60 * 60))." and c.idtitanes not in ($evita) and c.bloq = 0");
$correoMi .= "Faltan " . $temp->f('cant') . " Clientes<br>";
$temp->query("select count(*) cant from tbl_aisFicheros f where f.subido = 0 and  f.fecha > " . (time() - ($cantH * 60 * 60)));
$correoMi .= "Faltan " . $temp->f('cant') . " Ficheros<br>";

$q = "select distinct c.usuario, c.idtitanes from tbl_aisFicheros f, tbl_aisCliente c where f.idcliente = c.id and c.bloq = 0 and f.subido = 0 and c.idtitanes is not null and c.idtitanes not in ($evita) and f.fecha > " . (time() - ($cantH * 60 * 60));
$correoMi .= $q . "\n<br>";
$temp->query($q);
$arrFici = $temp->loadRowList();
$correoMi .= "arrFici: ".json_encode($arrFici)."<br>";

$q = "select distinct c.usuario, c.idtitanes from tbl_aisFicheros f, tbl_aisCliente c where f.idcliente = c.id and c.bloq = 0 and f.subido = 0 and c.idtitanes is not null and c.idtitanes not in ($evita) and c.fecha > " . (time() - ($cantH * 60 * 60));
$correoMi .= $q . "\n<br>";
$temp->query($q);
$arrCli = $temp->loadRowList();
$correoMi .= "arrCli: ".json_encode($arrCli)."<br>";

for ($i = 0; $i < count($arrFici); $i++) {
	if (!in_array($arrFici[$i], $arrCli)) $arrCli[] = $arrFici[$i];
}

$correoMi .= json_encode($arrCli)."<br>";

for ($j = 0; $j < count($arrCli); $j++) {
$correoMi .= "cliente={$arrCli[$j][0]}<br>";

	//verifica que el Cliente exista en Titanes
	$data = array('ClientId' => $arrCli[$j][1]);
	// $sale = datATitanes($data, 'E', 91);
	// $correoMi .= "sale: $sale<br>";

	if (subficheros2($arrCli[$j][0], $arrCli[$j][1])) {// se subieron los ficheros a Titanes salvo en la BD
		$correoMi .= "Subidos los ficheros a Titanes<br>";
	} else $correoMi .= "No se subieron los ficheros a Titanes idcliente = '{$ftp_dirAis[1]}'\n<br>";
}

/**
 * Revisa que el listado de sitios siguientes estén trabajando	
 */
$arrhosts = array(
	'www.administracomercios.com',
	'www.aisremesascuba.com',
	'www.travelsandiscoverytours.com',
	'www.caribeantravelweb.com',
	'www.tropicalnatur.com',
	//'www.caribbeantravelway.com',
	//'www.bidaiondo.com',
	//'www.publinetservicios.com',
	'www.bidaitravel.com',
	'www.cubashoppingcenter.com'
);
$ver='';
foreach ($arrhosts as $host) {
		// $ver .= $host."<br>" ;
	if(!$socket =@ fsockopen("ssl://".$host, 443, $errno, $errstr, 30)) {
		$ver .= 'Caido - '.$host."<br>" ;
		$ver .= "$errno $errstr<br>";
	} else {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch, CURLOPT_STDERR, $fp);
		curl_setopt($ch, CURLOPT_CERTINFO, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		if (curl_errno($ch)!=0)  $ver .= "El sitio $host entrega Error: ".curl_errno($ch)." ".curl_error($ch)."<br><br>";

		if (!strpos($result, "Location: https://".$host)) {
			$ver .= "Problemas con el certificado del sitio $host<br>$str <br><br>";
		}
		curl_close($ch);

	}
	fclose($socket);
}

if (strlen($ver)) $correo->todo(52, "Agencias caídas", $ver);

/**
 * Revisa los Clientes inscritos que tengan 0 ficheros subidos y lleven inscritos en Fincimex hace 4 días y los pone a subir
 */
// error_log("holaholahola");
// $q = "select c.id from tbl_aisCliente c where (select count(*) from tbl_aisFicheros where idcliente = c.id) = 0 and c.fechaAltaCimex > (".time()."-60*60*24*4) order by fecha desc limit 0,5";
// error_log($q);
// $temp->query($q);
// $arrLast = $temp->loadResultArray();
// foreach ($arrLast as $id) {
//     $q = "update tbl_aisCliente SET fecha = unix_timestamp(), subfichero = '1', borrficheros = 0 WHERE id = $id";
// error_log($q);
//     $temp->query($q);
//     $q = "delete from tbl_aisFicheros where idcliente = $id";
// error_log($q);
//     $temp->query($q);
// }


function resize_image($file, $diror, $dirdes, $crop=FALSE) {

	$w = 200;$h = 200;$compression=75;
	$l = getimagesize($diror."/".$file);
    list($width, $height) = $l;
    $type = $l['mime'];
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($diror."/".$file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    
    if( $type == 'image/jpeg' ) {
    	imagejpeg($dst,$dirdes."/".$file,$compression);
    } elseif( $type == 'image/gif' ) {
    	imagegif($dst,$dirdes."/".$file);
    } elseif( $type == 'image/png' ) {
    	imagepng($dst,$dirdes."/".$file);
    }
    if( $permissions != null) {
    	chmod($file,0755);
    }

    return $dst;
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
		$correoMi .= "pase1<br>";
		$i = 1;
		$pase = false;
		while (false !== ($file = readdir($handle)) ) {
			if ($file != '..' && $file != '.' && $titId > 0) {
				$q = "select * from tbl_aisFicheros where fichero = '$file' and subido = 1 and idcliente = (select id from tbl_aisCliente where idtitanes = $titId)";
				$correoMi .= $q."\n<br>";
				$temp->query($q);
				if ($temp->num_rows() > 0 ){
					$correoMi .= "El fichero $file ya fué subido a Titanes\n<br>";
	// 				$correoMi = '';
				} else {
					$file = str_replace('/','',$file);
					if (is_file($file_url."/".$file)) {
						$correoMi .= "hola";
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
	$correoMi .= "pase=$pase<br>";

	if ($pase===true)
	$correoMi .= "entra<br>";
		// muestraError("Envío de documentos a Titanes", "Body: ".$BODY."<br><br>Border: ".$BOUNDARY);
		$correoMi .= "cadenaotra=$strId<br>";
		if (strlen($strId)>2) {
			$correoMi .= "strId=".$strId."<br>";
			if (fichTitan($BODY, $BOUNDARY, $titId)) {
				$strId = ltrim(rtrim(str_replace(",,",",",$strId),','),',');
				$correoMi .= "strId2=".$strId."<br>";
				if (strlen(($strId))) {
					$q = "update tbl_aisFicheros set subido = 1, fecha_sub = ".time()." where id in ($strId)";
					$correoMi .= $q."\n<br>";
					$temp->query($q);
				}
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
	$correo->todo(52, $etiqueta, $textoCorreo."fecha= ".date('d/m/Y H:i:s')." - cron15**** <br>".$correoMi);
	//if (stripos($etiqueta, "error") > -1) $correo->todo(52, $etiqueta, $textoCorreo." ** ".$correoMi);
	//else $correo->todo(53, $etiqueta, $textoCorreo." ** ".$correoMi);
// 	if (stripos($etiqueta, 'error')) exit;
// 	else return;

// 	exit;
}

?>
