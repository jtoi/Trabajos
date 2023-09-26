<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

/**
 * Busca el iso2 del país en base al id de la tabla pais
 * @param integer $cod
 */
function paisCod($cod) {
	$temp = new ps_DB();
	$q = "select iso2 from tbl_paises where id = ".$cod;
	$temp->query($q);
	return $temp->f('iso2');
}

/**
 * Crea el voucher de la operación de Titanes lo salva a pdf y lo envía
 * @param varchar $idtran
 * @return boolean
 */
function creatitVou($idtran){
	$TCPDF = new TCPDF();
	$temp = new ps_DB();
	
	//saco los datos de la operación y el cliente
	$q = "select concat(c.nombre, ' ', c.papellido, ' ', c.sapellido) cliente, c.idtitanes, concat(b.nombre, ' ', b.papellido, ' ', b.sapellido) beneficiario, ".
				"t.identificador id, from_unixtime(t.fecha_mod, '%d/%m/%Y') fec, t.valor_inicial, m.moneda, o.recibe, o.comision, o.titOrdenId, o.envia ".
		" from tbl_moneda m, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t ".
		" where t.moneda = m.idmoneda ".
			"and o.idtransaccion = t.idtransaccion ".
			"and o.idtransaccion = '$idtran' ".
			"and o.idcliente = c.id ".
			"and o.idbeneficiario = b.id";
	$temp->query($q);
	$cliente 		= ucfirst($temp->f('cliente'));
	$beneficiario 	= ucfirst($temp->f('beneficiario'));
	$id 			= $temp->f('id');
	$fec 			= $temp->f('fec');
	$moneda 		= $temp->f('moneda');
	$val 			= number_format($temp->f('valor_inicial') / 100,2)." ".$moneda;
	$recibe 		= number_format($temp->f('recibe') / 100,2)." CUC";
	$comision 		= number_format($temp->f('comision') / 100,2)." ".$moneda;
	$idtitanes 		= $temp->f('idtitanes');
	$ordtit 		= $temp->f('titOrdenId');
	$tasa			= number_format($temp->f('recibe')/$temp->f('envia'),4);
	
	$arrIdio = array(
			array('Id','Id')
			, array('Fecha','Date')
			, array('Cliente','Sender')
			, array('Beneficiario','Beneficiary')
			, array('Total a pagar','Total amount to pay')
			, array('Beneficiario recibe','Beneficiary receives')
			, array(utf8_encode('Comisión'),'Fee amount')
			, array('Comprobante de pago','Voucher')
			, array('Detalles de la Transacción','Transaction details')
			, array('Orden','Order')
			, array('Tasa','Rate')
	);
	$idio = 0; //para usar el idioma español
	
	// Extend the TCPDF class to create custom Header and Footer
	class MYPDF extends TCPDF {
	
		//Page header
		public function Header() {
			// Logo
			$image_file = K_PATH_IMAGES.'aisLogo.jpg';
			$this->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			// Set font
			$this->SetFont('helvetica', 'B', 20);
			// Title
			$this->Cell(90, 15, 'AISRemesas '.$arrIdio[7][$idio], 10, false, 'C', 0, '', 0, false, 'M', 'M');
		}
	}
	
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
	$pdf->SetCreator('');
	$pdf->SetAuthor('AISRemesas');
	$pdf->SetTitle('AISRemesas Voucher');
	
	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
	$pdf->setFooterData();
	
	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
	// ---------------------------------------------------------
	
	// set default font subsetting mode
	$pdf->setFontSubsetting(true);
	$pdf->SetFont('dejavusans', '', 10, '', true);
	
	// Add a page
	// This method has several options, check the source code documentation for more information.
	$pdf->AddPage();
	$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
	$pdf->SetFont('helvetica', 'B', 14);
	$pdf->Ln(15);
	$pdf->Write(0, utf8_encode($arrIdio[8][$idio]), '', 0, '', true, 0, false, false, 0);
	$pdf->SetFont('helvetica', '', 11);
	
	$pdf->SetLineWidth(0);
	
	trigger_error($q."\n".$arrIdio[0][$idio].':'.$id."\n".$arrIdio[9][$idio].':'.$ordtit."\n".$arrIdio[1][$idio].':'.$fec."\n".$arrIdio[2][$idio].':'.$cliente
					."\n".$arrIdio[3][$idio].':'.$beneficiario."\n".$arrIdio[4][$idio].':'.$val." ".$moneda."\n".$arrIdio[5][$idio].':'.$recibe.' CUC'
					."\n".$arrIdio[6][$idio].':'.$comision, E_USER_WARNING);
	$pdf->Ln(5);
	$border = array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
	$pdf->Cell(90, 0, $arrIdio[0][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $id, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[9][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $ordtit, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[1][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $fec, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[2][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $cliente, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[3][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $beneficiario, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[4][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $val, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[5][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $recibe, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[6][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $comision, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $arrIdio[10][$idio].':', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
	$pdf->Cell(90, 0, $tasa, $border, 1, 'L', 1, '', 0, false, 'T', 'C');
	$pdf->SetFont('helvetica', 'I', 10);
	$pdf->Ln(10);
	$sitiene = utf8_encode('Si tiene alguna pregunta acerca de la entrega o el seguimiento de su transacción, por favor póngase en contacto con nosotros en:');
	$pdf->Write(0, $sitiene, '', 0, '', true, 0, false, false, 0);
	$pdf->Ln(5);
	$pdf->writeHTML('Email: <a href="mailto:info@aisremesascuba.com">info@aisremesascuba.com</a>', true, false, true, false, '');
	
	// ---------------------------------------------------------
	
// 	$file_url = $_SERVER['DOCUMENT_ROOT'].'desc/';
	$file_url = str_replace("rep", "desc", _PATH_SITIO);
// 	$file_url = _PATH_SITIO."desc";
// trigger_error($file_url, E_USER_WARNING);
	$file = 'transac'.$idtran.".pdf";
	$pdf->Output($file_url.$file, 'F');
	
	$eol = "\r\n"; //default line-break for mime type
	$BOUNDARY = md5(time()); //random boundaryid, is a separator for each param on my post curl function
	$BODY=""; //init my curl body
	$BODY.= '--'.$BOUNDARY. $eol; //start param header
	$BODY .= 'Content-Disposition: form-data; name="uploadType"' . $eol ; // last Content with 2 $eol, in this case is only 1 content.
	$BODY .= 'Content-Type: text/html' . $eol. $eol;
	$BODY .= "7" . $eol;//param data in this case is a simple post data and 1 $eol for the end of the data
	
	$BODY.= '--'.$BOUNDARY. $eol; // start 2nd param,
	$BODY.= 'Content-Disposition: form-data; name="uploadFile1"; filename="'.$file.'"'. $eol ; //first Content data for post file, remember you only put 1 when you are going to add more Contents, and 2 on the last, to close the Content Instance
	$BODY.= 'Content-Type: '. mime_content_type($file_url.$file) . $eol; //Same before row
	$BODY.= 'Content-Transfer-Encoding: base64' . $eol . $eol; // we put the last Content and 2 $eol,
	$BODY.= file_get_contents($file_url.$file) . $eol; // we write the Base64 File Content and the $eol to finish the data,
	
	$BODY.= '--'.$BOUNDARY .'--' . $eol. $eol; // we close the param and the post width "--" and 2 $eol at the end of our boundary header.
	return fichTitan($BODY, $BOUNDARY, $idtitanes);
}

/**
 * Envía los ficheros a Titanes
 * @param unknown $BODY
 * @param unknown $BOUNDARY
 * @param unknown $titId
 * @return boolean
 */
function fichTitan($BODY, $BOUNDARY, $titId = null){
	global $correoMi;
	$temp = new ps_DB();
	$correoMi .= "Entra en ficTitan\n<br>";
	
	$q = "select estado from tbl_pasarela where idPasarela = 37";
	$temp->query($q);
	if ($temp->f('estado') == 'D') $ul = "https://195.57.91.186:8555/APITest/Customer/$titId/Upload";
	else $ul = "https://www.grupotitanes.es:8555/APITitanes/Customer/$titId/Upload";
	
	if ($titId) $url = $ul;

	$ch = curl_init(); //init curl
	$options = array(
			CURLOPT_USERAGENT			=> 'Mozilla/1.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0'
			,CURLOPT_URL 				=> $url
			,CURLOPT_HTTPHEADER			=> array(
					"Content-Type: multipart/form-data; boundary=".$BOUNDARY //setting our mime type for make it work on $_FILE variable
					,"Authorization: Bearer ".titToken()
			)
			,CURLOPT_SSL_VERIFYHOST		=> false
			,CURLOPT_SSL_VERIFYPEER		=> false
			,CURLOPT_RETURNTRANSFER		=> true
			,CURLOPT_BINARYTRANSFER		=> true
			,CURLOPT_FOLLOWLOCATION		=> false
			,CURLOPT_POST				=> true
			,CURLOPT_POSTFIELDS			=> $BODY
	);
	
	foreach ($options as $key => $value) {
		$correoMi .= "$key => $value\n<br>";
	}
	curl_setopt_array($ch, $options);
	
	$sale = curl_exec($ch);
	$correoMi .= $sale."\n<br>";
	$crlerror = curl_error($ch);
	$correoMi .= $crlerror."\n<br>";
	// echo $correoMi;
	if (strstr($sale, '"Status":200')) {
		ini_set('safe_mode', $smod);
		$correoMi .= "TODO OK subidos los ficheros\n<br>";
		return true;
	
		// 		enviaError("Todo bien en la subida de ficheros AIS $usuario, $file, $sale $crlerror");
	} else {
		$curl_info = curl_getinfo($ch);
		trigger_error("Error AIS en la subida de ficheros a Titanes ".$sale, E_USER_WARNING);
		foreach ($curl_info as $key => $value) {
			$correoMi .=  $key." = ".$value."<br>\n";
		}
		enviaError("Error subiendo ficheros de Ais\n<br>". $sale . $crlerror."\n<br> ** ".$correoMi);
		return false;
	}
}

/**
 * Verifica que el token de Titanes esté actualizado de no ser así solicita uno nuevo
 * @return string valor del token
 */
function titToken(){
	$temp = new ps_DB();
	
	$q = "select estado from tbl_pasarela where idPasarela = 37";
	$temp->query($q);
	if ($temp->f('estado') == 'D') {
		$pa = "amfpass1234";
		$ur = "https://195.57.91.186:8555/APITest/Token";
	}
	else {
		$pa = "mafpass98765";
		$ur = "https://www.grupotitanes.es:8555/APITitanes/Token";
	}
	$correoMi .=  "pass=$pa\n<br>";
	
	$q = "select fecha from tbl_setup where nombre = 'tiToken'";
	$temp->query($q);
	if (time()-(60*60) > $temp->f('fecha')) {
		//buscando el token si la fecha del último es menor de 24 horas
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Content-Type: application/json;charset=UTF-8";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: "; // browsers keep this blank.
	
		$options = array(
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_SSL_VERIFYHOST	=> false,
				CURLOPT_SSL_VERIFYPEER	=> false,
				CURLOPT_POST			=> true,
				CURLOPT_VERBOSE			=> true,
				CURLOPT_FRESH_CONNECT 	=> true,
				CURLOPT_URL				=> $ur,
				CURLOPT_POSTFIELDS		=> "grant_type=password&username=".urlencode('info@amfglobalitems.com')."&password=$pa",
				CURLOPT_HEADER			=> false,
				CURLOPT_HTTPHEADER		=> $header,
				CURLOPT_RETURNTRANSFER	=> 1,
				CURLOPT_FOLLOWLOCATION	=> 1,
				CURLOPT_CONNECTTIMEOUT	=> 100,
				CURLOPT_TIMEOUT			=> 100,
				CURLOPT_USERAGENT		=> "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.54"
		);
	
		$ch = curl_init();
		curl_setopt_array($ch , $options);
		$salida = curl_exec($ch);
		
		if (curl_errno($ch)) {
			$correoMi .=  trigger_error("Error en la resp de Titanes: ".curl_strerror(curl_errno($ch)));
			enviaError("Error en la resp de Titanes para el Token: ".curl_strerror(curl_errno($ch)));
			$crlerror = curl_error($ch);
		}
		
		$curl_info = curl_getinfo($ch);
		curl_close($ch);
		
		$arrCurl = json_decode($salida);
	
		actSetup($arrCurl->access_token, 'tiToken');
	
		$access_token = $arrCurl->access_token;
	} else $access_token = leeSetup('tiToken');
	return $access_token;
}

/**
 * Formatea los números en dependencia del formato escogido por el usuario
 * @param number $num número a formatear
 * @return string número formateado
 */
function formatea_numero($num,$mon=true) {
	if($mon)
		return number_format($num, $_SESSION['cantdec'], convertir_especiales_html($_SESSION['sepdecim']), 
			convertir_especiales_html($_SESSION['sepmiles']));
	else
		return number_format($num, 0, convertir_especiales_html($_SESSION['sepdecim']),	convertir_especiales_html($_SESSION['sepmiles']));
}

/**
 * Formatea la fecha en dependencia del formato del usuario
 * @param number $fecha en unix timestamp
 * @return string fecha formateada
 */
function formatea_fecha($fecha) {
	return date($_SESSION['formtfecha'], $fecha);
}

/**
 * Genera los identificadores de transaccion para el concentrador y para los comercios
 * @param boolean $tipo true genera para tbl_transacciones
 * @param boolean $rand true adiciona el random al final de la cadena
 * @param string $comr identificador de las operaciones para el comercio
 * @return string
 */
function trIdent ($comr, $tipo=true, $rand=true) {
	$temp = new ps_DB();
    $salida = 1;
    $cant = 8;
    $timeZone = date_default_timezone_get();
    date_default_timezone_set('Europe/Berlin');
    while ($salida > 0) {
        $idTran = (string)(date("mdHi"));
        if ($rand) { //se pide generar el último par de valores
            $idTran .= (string)(rand (10, 99));
            $can = 10;
        }
		
		date_default_timezone_set($timeZone);
		if ($comr) $idTran = date ('y').$idTran; //se le agregan los dos primeros lugares si se envían (identificador del comercio)
	
        $query = "select count(*) total from ";
        ($tipo) ? $query .= "tbl_transacciones where idtransaccion = '$idTran'":
                $query .= "tbl_reserva where codigo = '$idTran'";
//        echo $query."<br>";
        $temp->query($query);
        $salida = $temp->f('total');
//        echo $salida."<br>";
    }
    return $idTran;
}

/**
 * Completar una cadena de largo $cant con el caracter $letr
 * @param string $cad cadena a completar
 * @param int $cant cantidad de lugares total
 * @param char $letr letra para completar
 * @param char $dir l o r lado hacia el que se pone el completamiento
 * @return string cadena formada
 */
function complLargo($cad,$cant=10,$letr='0',$dir='l') {
	$num = strlen($cad);$sale='';
	if ($num < ($cant)) {
		for ($i=0;$i<($cant-$num);$i++){
			$sale .= $letr;
		}
		($dir == 'l') ? $sale = $sale.$cad : $sale = $cad.$sale;
	}
	return $sale;
}

/**
 * Obtiene la información para construir los accesos
 * directos en base a las costumbres de los clientes
 */
function Accesos() {
	$temp = new ps_DB();
	//Accesos directos por defecto para el usuario que aún no tenga las costumbres almacenadas
	$accArr = array(
			array("url" => 'index.php?componente=comercio&pag=reporte', "texto" => _MENU_ADMIN_TRANSACCIONES, 
					"parentid" => 4, "id" => 16),
			array("url" => 'index.php?componente=comercio&pag=comparacion', "texto" => _MENU_ADMIN_COMPARACION, 
					"parentid" => 4, "id" => 19),
			array("url" => 'index.php?componente=core&pag=personal', "texto" => _MENU_ADMIN_PERSONALES, 
					"parentid" => 1, "id" => 7),
			array("url" => 'index.php?componente=ticket&pag=ticketin', "texto" => _MENU_ADMIN_INSTICKET, 
					"parentid" => 6, "id" => 23),
			array("url" => 'index.php?componente=comercio&pag=cliente', "texto" => _MENU_ADMIN_PAGO, 
					"parentid" => 4, "id" => 25)
	);
	$q = "select m.id, m.link, title, parentid from tbl_cantAccesos c, tbl_menu m where m.id = c.idmenu and idadmin = "
			. $_SESSION['id'] ." and m.id not in (12) order by c.cant desc limit 0,5";
//	 echo $q;
	$temp->query($q);
	$menu3 = $temp->loadObjectList();
	$j = 0;
	$acdArr = array();
	//	print_r($accArr);
	//	print_r($menu3);
	for ($i = 0; $i < count($menu3); $i++) {
		if ($menu3[$i]->link) {
			$url = $menu3[$i]->link;
			$tex = constant($menu3[$i]->title);
			$par = $menu3[$i]->parentid;
			$id = $menu3[$i]->id;
		} else {
			$url = $accArr[$j]['url'];
			$tex = $accArr[$j]['texto'];
			$par = $accArr[$j]['parentid'];
			$id = $accArr[$j]['id'];
			$j++;
		}
		$acdArr[$i] = array("url" => $url, "txt" => $tex, "pid" => $par, "id" => $id);
	}
	return $acdArr;
}

/**
 * Función que construye la página llamada
 */
function hotel_puts($val=null) {
	$temp = new ps_DB();
	global $ent;
    global $posc;
    $posc = $val;
//echo "entra";
	if (strlen($_GET['componente']) > 1) {
		if (($comp = $ent->isAlfabeto($_GET['componente'], 13)) && ($pag = $ent->isAlfabeto($_GET['pag'], 13))) {
			$camino_busc = "componente/".$comp."/". $pag .".php";

			//actualiza la tabla cantAccesos
			$query = "select count(*) tot from tbl_cantAccesos c, tbl_menu m where idadmin = ".$_SESSION['id']
						. " and m.id = c.idmenu and link like '%componente=".$comp."&pag=". $pag."%'" ;
			$temp->query($query);
			//				echo $query."<br>";

			if ($temp->f('tot') > 0) $query = "update tbl_cantAccesos c, tbl_menu m set cant = cant + 1, fecha = ".time().
						" where idadmin = ".$_SESSION['id']." and m.id = c.idmenu and link like '%componente="
							.$comp."&pag=". $pag."%'" ;
			else $query = "insert into tbl_cantAccesos values (null, ".$_SESSION['id'].", (select id from tbl_menu "
						. " where link like '%componente=".$comp."&pag=". $pag."%'), 1, ".time().")";
			
			$temp->query($query);

			include_once ($camino_busc);
		}
	} else {
		include_once ("componente/comercio/inicio.php");
	}
}

/* creates a compressed zip file, ejemplo de uso:
 *  $files_to_zip = array(
	'preload-images/1.jpg',
	'preload-images/2.jpg',
	'preload-images/5.jpg',
	'kwicks/ringo.gif',
	'rod.jpg',
	'reddit.gif'
);
if true, good; if false, zip creation failed
$result = create_zip($files_to_zip,'my-archive.zip');
 */
function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,$file);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

function leeFicheros($fichero) {
	 if (is_file($fichero)) {
		 if ($stream = fopen($fichero, 'r')) return stream_get_contents($stream);
	 }
	 return false;
 }
 
function leeFic($fich){
	if (is_file($fich)) {
		$buffer = "";
		$handle = fopen($fich, "r");
		while (($buffer .= fgets($handle, 4096)) !== false) {
			$yo='';
		}
		if (!feof($handle)) {
			echo "Error: unexpected fgets() fail\n";
		} return $buffer;
    fclose($handle);
		$contents = fread($handle, filesize($fich));
		fclose($handle);
		return $contents;
	} return FALSE;
}

function leeFichero($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}

/**
 * Función para enviar imágenes incrustadas en los correos
 * @param type $to
 * @param type $subject
 * @param type $encabezado
 * @param type $contenido
 * @param type $file Fichero de la Imágen
 * @param type $from
 * @return boolean
 */
function correoImag($to, $subject, $encabezado, $contenido, $file, $from) {

//	error_reporting(E_ALL);
//	define('DISPLAY_XPM4_ERRORS', true);
	require_once 'classes/MIME.php';

	$id = MIME::unique();
	$text = MIME::message('Text version of message.', 'text/plain');
	$contenido = str_replace('{id}', "cid:".$id, $contenido);
	$html = MIME::message($contenido,'text/html');
	if (is_file("logos/".$file)) {
		$at[] = MIME::message(file_get_contents("logos/".$file), FUNC::mime_type("logos/".$file), "logos/".$file, null, 'base64', 'inline', $id);
		$mess = MIME::compose($text, $html, $at);
		//echo $mess['header'];
		//echo "<br>dsfasdf".$from;
		$send = mail($to, $subject, $mess['content'], 'From: '.$from."\n".$mess['header']);
	
		return $send;
	}
	return false;
}


/**
 * Funciï¿½n para insertar las cantidades de productos en la tabla productosCant
 * @param <type> $prod
 * @param <type> $fecha1
 * @param <type> $fecha2
 * @param <type> $cant
 */
function insertaCantidad($prod, $fecha1, $fecha2, $cant) {
	$temp = new ps_DB();
	$ini = new ps_DB();
	$ini2 = new ps_DB();
	
	if ($_SESSION['id']) {
		$usuario = $_SESSION['id'];
	} else {
		$query = "select idadmin from tbl_productos p, tbl_admin a where a.idcomercio = p.idCom and idrol = 11 and "
				. " p.id = $prod limit 0,1";
//echo "$query<br>";
		$temp->query($query);
		$usuario = $temp->f('idadmin');
	}
	
	//	Chequeo si ya existe precio para ese intervalo y dï¿½ la posiciï¿½n
	$query = "select id from tbl_productosCant where idProd = $prod and fechaFin >= $fecha1 or fechaIni <= $fecha2";
	$temp->query($query);

	if ($temp->num_rows() != 0) { //si hay interferencias de cantidades anteriores

//echo'		Chequea y borra todos los cantidades que quedan dentro del intervalo de fechas<br>';
		$query = "select id from tbl_productosCant where idProd = $prod and fechaIni >= $fecha1 and fechaFin <= $fecha2";
		$temp->query($query);
		if ($temp->num_rows() > 0) {
//echo"		Borra todos los cantidades contenidos dentro del intervalo de fechas del nuevo precio<br>";
			$arraProd = implode(",", $temp->loadResultArray());
			$query = "delete from tbl_productosCant where id in ($arraProd)";
			$temp->query($query);
		}

//echo		"Chequea si existe un precio con un intervalo de fechas que contenga las fechas del nuevo precio, si es asï¿½ debe picarlo en 2<br>";
		$query = "select * from tbl_productosCant where idProd = $prod and fechaIni <= $fecha1 and fechaFin >= $fecha2";
		$temp->query($query);
		for ($i=0;$i<$temp->num_rows();$i++) {

//echo			"Inserta el primer pedazo por delante del nuevo a insertar<br>";
			$arrVals = $temp->loadRow();
			$query = "insert into tbl_productosCant (id, idProd, idAdm, cant, fecha, fechaIni, fechaFin) 
						values (null, {$arrVals[1]}, {$arrVals[2]}, {$arrVals[3]}, {$arrVals[4]}, {$arrVals[5]}, "
							. ($fecha1-86400).")";
			$temp->query($query);

//echo			"Modifica el grande existente para que sea el segundo pedazo por detrï¿½s del nuevo<br>";
			$query = "update tbl_productosCant set fechaIni = ".($fecha2+86400)." where id = {$arrVals[0]}";
			$temp->query($query);
		}

//echo		"Chequea si hay interferencia por delante<br>";
		$query = "select id from tbl_productosCant where idProd = $prod and fechaIni > $fecha1 and fechaFin > $fecha2 "
				. " and fechaIni <= $fecha2";
		$temp->query($query);
		for ($i=0;$i<$temp->num_rows();$i++) { 
//echo			"Modifica el precio existente<br>";
			$query = "update tbl_productosCant set fechaIni = ".($fecha2+86400)." where id = ".$temp->f('id');
			$temp->query($query);
		}

//echo		"Chequea si hay interferencia por detrï¿½s<br>";
		$query = "select id from tbl_productosCant where idProd = $prod and fechaIni < $fecha1 and fechaFin < $fecha2 "
				. " and fechaFin >= $fecha1";
		$temp->query($query);
		for ($i=0;$i<$temp->num_rows();$i++) { 
//echo			"Modifica el precio existente<br>";
			$query = "update tbl_productosCant set fechaFin = ".($fecha1-86400)." where id = ".$temp->f('id');
			$temp->query($query);
		}
	}

//echo	"Por ï¿½ltimo, inserta el nuevo precio<br>";
	$query = "insert into tbl_productosCant  (id, idProd, idAdm, cant, fecha, fechaIni, fechaFin) values (null, $prod, "
			. " $usuario, $cant, unix_timestamp(), $fecha1, $fecha2)";
//echo $query."<br>";
	$temp->query($query);
//echo	"y borra los cantidades con las fechas invertidas<br>";
	$query = "delete from tbl_productosCant where fechaFin < fechaIni";
	$temp->query($query);
//echo	"borra las entradas con cant = 0 <br>";
	$query = "delete from tbl_productosCant where cant = 0";
	$temp->query($query);

//echo 'Une todas las entradas del mismo producto con la misma cant que estï¿½n contï¿½guas<br>';
	$query = "select id, idProd, fechaIni, fechaFin, cant from tbl_productosCant order by idProd, fechaIni";
//	echo "$query<br>";
	$temp->query($query);

	while ($temp->next_record()) {
		$id = $temp->f('id');
		$idProd = $temp->f('idProd');
		$fecha2 = $temp->f('fechaFin');
		$cant = $temp->f('cant');

		while (true) {
			$query = "select id, idProd, fechaIni, fechaFin, cant from tbl_productosCant
						where cant = $cant and fechaIni = ".($fecha2 + 86400)." and idProd = $idProd and id != $id "
							. " order by idProd, fechaIni";
//			echo "$query<br>";
			$ini->query($query);
//			echo $ini->num_rows()." ";
			if ($ini->num_rows() == 0) break;

			$query = "update tbl_productosCant set fechaFin = ".$ini->f('fechaFin') ." where id = $id";
//			echo "$query<br>";
			$ini2->query($query);
			$fecha2 = $ini->f('fechaFin');

			$query = "delete from tbl_productosCant where id = ".$ini->f('id');
//			echo "$query<br>";
			$ini2->query($query);
			break;
		}
	}
	return true;
}

function validaContrasena($usuario) {
	$temp = new ps_DB();
	$salida = false;
	while (!$salida) {
		$contras = suggestPassword(16);
		$calc_md5 = sha1($usuario.$contras.'Lo que los hace hermoso es algo invisible...los ojos no siempre ven. Hay que buscar con el corazón.');
		$query = "select count(*) total from tbl_admin where md5 = '$calc_md5'";
		$temp->query($query);
		if ($temp->f('total') == 0) $salida = true;
	}
//echo "usuario=$usuario<br>";
//echo "contras=$contras<br>";
//echo "calc_md5=$calc_md5<br>";

	return array($contras,$calc_md5);
}

/*
 * Lee de la tabla setup los valores que le piden
 */
function leeSetup($variab) {
	$temp = new ps_DB();
	$query = "select valor from tbl_setup where nombre = '$variab'";
	$temp->query( $query );
	return $temp->f('valor');
}

/**
 * Hace el update de la tabla Setup con los valores enviados
 * @param type $valor
 * @param type $nombre
 * @return type
 */
function actSetup($valor, $nombre) {
	$temp = new ps_DB();
	$query = "update tbl_setup set valor = '$valor', fecha = ".time()." where nombre = '$nombre'";
	return $temp->query( $query );
}

//genera un codigo de 20 caracteres
//retorna un array con dos elementos
//el primero de 11 caracteres de largo
//el segundo de 18
function generaCod() {
	list($usec, $sec) = explode(" ", microtime());
	return (array ((microtime(true)*100), str_replace('0.', '', $sec.$usec)));
}

function generaCodEmp() {
    $eti = str_replace('.', '', (string)microtime(false));
	return (time().substr($eti, 1, 2));
}

//borra de las tablas de los idiomas los titulos y descripciones
function borra_idioma($clave) {
	$temp = new ps_DB();
	$sel_idioma = "select titulo from tbl_idioma order by ididioma";
	$temp->query($sel_idioma);

	while ($temp->next_record()){
		$nombre_idioma[++$j] = $temp->f('titulo');
	}

	for ($num = 0; $num < count($clave); $num++) {
		for ($x=1; $x<=count($nombre_idioma); $x++) {
			$idiomas = "delete from tbl_idioma_".strtolower($nombre_idioma[$x])." where ididioma = ".$clave[$num];
			$temp->query($idiomas);

			$idiomas = "delete from tbl_idioma_".strtolower($nombre_idioma[$x])." where ididioma = ".$clave[$num];
			$temp->query($idiomas);
		}
	}
}

//borra subdirectorios y todo su contenido
function borra_dir($dir) {
	if (false !== ($handle = opendir($dir))) {
		$files = glob($dir .'*.{???}', GLOB_BRACE);
		for ($x=0; $x<count($files); $x++) unlink($files[$x]);
	}
    closedir($handle);
	rmdir($dir);
}

//retorna un float convertido en moneda
function moneda($num) {
	return number_format($num, 2, '.', ' ');
}

//returns safe code for preloading in the RTE
function RTESafe($strText) {

	$tmpString = '';

	$tmpString = trim($strText);

	//convert all types of single quotes
	$tmpString = str_replace(chr(145), chr(39), $tmpString);
	$tmpString = str_replace(chr(146), chr(39), $tmpString);
	$tmpString = str_replace("'", "&#39;", $tmpString);

	//convert all types of double quotes"
	$tmpString = str_replace(chr(147), chr(34), $tmpString);
	$tmpString = str_replace(chr(148), chr(34), $tmpString);
//	$tmpString = replace($tmpString, """", "\""")

	//replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ", $tmpString);
	$tmpString = str_replace(chr(13), " ", $tmpString);

	return $tmpString;
}

//genera pares de options en base a una query
//las columnas en la query pasada deben tener como alias
//id y nombre
function opciones_sel_Arr($select, $id = null) {
	$cadena_orden = '';
//print_r($id);
	$temp = new ps_DB();
	$temp->query($select);
	while ($temp->next_record()){
//		echo $temp->f('id').", $id";
//		echo (array_search($temp->f('id'), $id));
		if (is_array($id) && array_search($temp->f('id'), $id) !== false )
			$cadena_orden .= '<option selected value="'.$temp->f('id').'">'.$temp->f('nombre').'</option>'."\n";
		else
			$cadena_orden .= '<option value="'.$temp->f('id').'">'.$temp->f('nombre').'</option>'."\n";
	}
	return $cadena_orden;
}

//genera pares de options en base a una query
//las columnas en la query pasada deben tener como alias
//id y nombre
function opciones_sel($select, $id = null) {
	$cadena_orden = '';

	$temp = new ps_DB();
	$temp->query($select);
	while ($temp->next_record()){
		if ($temp->f('id') == $id)
			$cadena_orden .= '<option selected value="'.$temp->f('id').'">'.$temp->f('nombre').'</option>'."\n";
		else
			$cadena_orden .= '<option value="'.$temp->f('id').'">'.$temp->f('nombre').'</option>'."\n";
	}
	return $cadena_orden;
}

//genera pares de options en base a arrays pasados
function opciones_arr ($valores_arr, $val_sel) {
	$cadena_orden = '';
	for ($x = 0; $x < count($valores_arr); $x++) {
		if ($valores_arr[$x][0] == $val_sel)
			$cadena_orden .= '<option selected="true" value="'.$valores_arr[$x][0].'">'.$valores_arr[$x][1].'</option>'."\n";
		else
			$cadena_orden .= '<option value="'.$valores_arr[$x][0].'">'.$valores_arr[$x][1].'</option>'."\n";
	}
	return $cadena_orden;
}


//genera pares de options en base a dos n&uacute;meros
function opciones($inicio, $final, $id) {
	$cadena_orden = '';
	for ($x=$inicio; $x<=$final; $x++){
		if ($x == $id) $cadena_orden .= '<option selected value="'.$x.'">'.$x.'</option>'."\n";
		else $cadena_orden .= '<option value="'.$x.'">'.$x.'</option>'."\n";
	}
	return $cadena_orden;
}


//Hace el login al sitio de administraci&oacute;n
function verifica_entrada($login, $contras){
	$calc_md52 = sha1($login.$contras.'Lo que los hace hermoso es algo invisible...los ojos no siempre ven. Hay que buscar con el corazón.');
// echo $calc_md52."<br>";
	$temp = new ps_DB();
	$Session = new SecureSession;
	$pase = false;

	$q = "select idadmin, a.nombre, a.idrol, r.orden, a.idcomercio, a.email, ip, usequery, formatFecha, cantDec, "
				. " separMiles, separDecim, case a.idcomercio when 'todos' then a.idcomercio else (select pasarelaAlMom "
				. " from tbl_comercio c where c.idcomercio = a.idcomercio) end pasarelaAlMom, case a.idcomercio "
				. " when 'todos' then 'todos' else (select nombre from tbl_comercio where idcomercio = a.idcomercio) end comercio
			from tbl_admin a, tbl_roles r
			where a.idrol = r.idrol
			and a.activo = 'S'
			and md5 in ('$calc_md52')";
//echo "<br>".$q;
	$temp->query($q);
	$arrAdm = $temp->loadAssocList();
	foreach ($arrAdm as $value) {
		$pase = true;
		$q = "select idComerc from tbl_colAdminComer where idAdmin = ".$value['idadmin'];
		$temp->query($q);
		$idcomStr = implode(",",$temp->loadResultArray());

		if ($value['ip'] == 'inicio') {

			$ip = str_replace('inicio', '', $value['ip']);
			$query = "update tbl_admin set ip = '".$_SERVER['REMOTE_ADDR']."' where idadmin = ".$value['idadmin'];
			$temp->query($query);

		} else { if ($value['idrol'] <= 100 || stripos($value['ip'], $_SERVER['REMOTE_ADDR']) !== false ) $pase = true; }
//		echo $pase;
		if ($pase) {
			$Session->SetFingerPrint(60);
			$_SESSION['LoggedIn'] = true;
			$_SESSION['id'] = $value['idadmin'];
			$_SESSION['admin_nom'] = $value['nombre'];
			$_SESSION['comercio'] = $value['idcomercio'];//poner esto obsoleto, empezar a usar idcomStr
			$_SESSION['comercioNom'] = $value['comercio'];
			$_SESSION['idcomStr'] = $idcomStr;
			$_SESSION['rol'] = $value['idrol'];
			$_SESSION['grupo_rol'] = $value['orden'];
			$_SESSION['email'] = $value['email'];
			$_SESSION['pasarelaAlMom'] = $value['pasarelaAlMom'];
			$_SESSION['sesionId'] = session_id();
			$_SESSION['usequery'] = $value['usequery'];
			$_SESSION['cantdec'] = $value['cantDec'];
			$_SESSION['sepdecim'] = $value['separDecim'];
			$_SESSION['sepmiles'] = $value['separMiles'];
			$_SESSION['formtfecha'] = $value['formatFecha'];
			$query = "update tbl_admin set fecha_visita = ".time().", ident = '".$_SESSION['sesionId']."' where idadmin = ".$_SESSION['id'];
			$temp->query($query);

			return 'pag=inicio';
		}	
	}
	return false;
}

//construye las tablas de resultados
//

/**
 * 
 * Tabla de muestra de resultados
 * @param integer $tabla_ancho ancho de la tabla que muestra los resultados
 * @param char(1) $idioma Idioma de entrada, permite prefijar valores de mensajes que de otra forma habrï¿½a que entrarlos
 * @param unknown_type $vista
 * @param unknown_type $orden
 * @param unknown_type $busquedaInicio
 * @param unknown_type $colEsp
 * @param unknown_type $busqueda
 * @param unknown_type $columnas
 * @return string
 */
function tabla( $tabla_ancho, $idioma='E', $vista, $orden='', $busquedaInicio='', $colEsp='', $busqueda='', $columnas) {
/*
vista - Vista de la BD contra la cual se realizar&aacute; todo el trabajo
orden -  ordenamiento de los valores de salida de la vista por default
busquedaInicio - Columnas de la vista a buscar por defult
colEsp - array de array para las columnas especiales (editar, ver, borrar..) en la forma (tipocolumna, textoalt, caminoimagen, titulocolumna)
busqueda - array de array para las busquedas en la forma (nombrecampo, columnatabla)
columnas - array de array con los datos de (titulocolumna, campotabla, anchocolumna, posicion)
*/

//$idioma = 'I';
if ($_SESSION['idioma'] == "english") {
	$alerta = "You are about to delete a record and all the data asociated with.\\nAre you sure?";
	$alerta2 = "Are you sure to cancel the transaction?\\nThis operation can`t be reverted.";
	$alerta3 = "The transaction must be Accepted or Refunded and or has a value bigger than 0.";
	$alerta4 = "The transacction will revert it`s status of paid to the commerce.";
	$alerta5 = "The transacction will not been requested for refund.";
} else {
	$alerta = "Se borrar\u00e1 este registro y todos los datos asociados a \u00e9l.\\nEst\u00e1 seguro?";
	$alerta2 = "Est\u00e1 usted seguro que la transacci\u00f3n es una reclamaci\u00f3n?\\nEsta operaci\u00f3n no puede ser revertida.";
	$alerta3 = "La transacci\u00f3n debe ser Aceptada y tener valor mayor que 0.";
	$alerta4 = "La transacci\u00f3n revertir\u00e1 su estado de pagada al comercio.";
	$alerta5 = "La transacci\u00f3n no debe estar solicitada para devolver.";
}

foreach ($columnas as $item) {
	if ($item[2]) $ancho += $item[2];
	else {
		$ancho = '';
		break;
	}
}

if ($ancho) {
	$ancho += 30 * count($colEsp) + (count($colEsp)*3+1) + (count($columnas)*3+1);
	$tabla_ancho = $ancho;
}

?>
<div id="tabInf">
  <form action="<?php echo $GLOBALS['sitio_url'].'index.php?'. $_SERVER['QUERY_STRING'] ?>" method="post" name="pag" id="pag">
<table align="center" width="<?php echo $tabla_ancho; ?>" border="0" cellspacing="0" cellpadding="0">
<?php
  			if (strlen($_REQUEST["orden"]) > 0) $ordenar = $_REQUEST["orden"];
			else $ordenar = $orden;

			$buscarStr = $busquedaInicio;
			if (strlen($_REQUEST["buscar"]) > 0) $buscarStr = $_REQUEST["buscar"];

			for ($cont = 0; $cont<=count($busqueda); $cont++) {
				if (strlen($busqueda[$cont]) > 0 && strlen($buscarStr) > 0) $buscarStr .= " and ".$busqueda[$cont];
				elseif (strlen($busqueda[$cont]) > 0 and strlen($buscarStr) == 0) $buscarStr = "where ".$busqueda[$cont];
			}

			$limite = 30;
			$mPage = 1;

			if (strlen($_REQUEST["btnPageNext"]) > 0 && strlen($_REQUEST["cmbPageSelect"]) > 0 )
				$mPage = $_REQUEST["cmbPageSelect"] + 1;
			elseif (strlen($_REQUEST["btnPagePrev"]) > 0 && strlen($_REQUEST["cmbPageSelect"]) > 0 )
				$mPage = $_REQUEST["cmbPageSelect"] - 1;
			elseif (strlen($_REQUEST["cmbPageSelect"]) > 0 && strlen($_REQUEST["btnPagePrev"]) == 0 && strlen($_REQUEST["btnPageNext"]) == 0 )
				$mPage = $_REQUEST["cmbPageSelect"];

			$nPage = ($mPage - 1) * $limite;

  			$sql = stripslashes($vista." ".$buscarStr." order by ".$ordenar. " limit $nPage, $limite");
  			$sql_cont = stripslashes($vista." ".$buscarStr);
// 			echo $sql_cont;
//correoAMi("Querys", $sql_cont);
		    $usr = new ps_DB();
			$usr->query($sql_cont);//&& $_SESSION['id'] == 19
// 			if ($usr->getErrorMsg() ) {
//				$corr = new correo();
//				$corr->todo(43, "Error de Mysql", $usr->getErrorMsg());
// 			}
			$num_records = $usr->num_rows();

			$usr->query($sql);
			$cantColumnas = (count($columnas) * 2 + count($colEsp) * 2) - 1;
//			$num_records = $usr->getRecords();
//			$usr->reset();
			
//echo $sql;
		 ?>
		<input name="cmbPageSelect" type="hidden">
		<input name="orden" type="hidden" value="<?php echo $_REQUEST["orden"]; ?>">
		<input name="buscar" type="hidden" value="<?php echo stripslashes($buscarStr); ?>">
		<input name="borrar" type="hidden">
		<input name="cambiar" type="hidden">
		<input name="pagar" type="hidden">
		<input name="factura" type="hidden">
		<input name="devol" type="hidden">
      <tr>
		  <td id="dale" colspan="<?php echo $cantColumnas; ?>"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr class="encabezamiento">
          <td width="100" align="left">&nbsp;<?php echo $num_records; ?> Record(s)</td>
          <td align="center">Pages:
            <?php

	  if (($mPage - 4) < 1) $inicio = 1;
	  else $inicio = $mPage - 3;

	  if ($num_records >= (($mPage + 3) * $limite)) $final = $mPage + 3;
	  else $final = ceil($num_records/$limite);

	  for ($x = $inicio; $x <= $final; $x++) {
		if ($mPage == $x) {
			echo "<label style=\"font-size:11px \"><strong> ";
			echo $x."</strong></label>&nbsp;";
		} else {
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=$x; document.pag.submit()\" href=\"#\"> ";
			echo "$x</a>&nbsp;";
		}
	}
	 ?></td>
          <td width="120" align="right"><?php

		if ($mPage != 1)
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage-1) ."; document.pag.submit()\" href=\"#\">&lt;&lt;..</a>"; ?>
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
		if ($mPage != ceil($num_records/$limite))
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage+1) ."; document.pag.submit()\" href=\"#\">..&gt;&gt;</a>"; ?>
</td>
        </tr>
      </table>
        </td>
      </tr>
    <tr>
      <td height="1" colspan="<?php echo $cantColumnas; ?>"></td>
    </tr>
    <tr class="encabezamiento2">
	<?php
//columnas especiales
	for ($cont = 0; $cont < count($colEsp); $cont++) { ?>
    <td class="celdas separa" width="30" align="center">&nbsp;<?php //echo $colEsp[$cont][3]; ?>&nbsp;</td>
	<?php }
//columnas normales
	 for ($cont = 0; $cont < count($columnas); $cont++) {
		if ($cont == (count($columnas) - 1)) {?>
			<td class="celdas" width="<?php echo $columnas[$cont][2] ?>" align="<?php echo $columnas[$cont][3] ?>"><a class="paglink" href="#" onClick="document.pag.orden.value = '<?php
			if (strstr($ordenar, $columnas[$cont][1]." desc")) echo $columnas[$cont][1]." asc";
			else echo $columnas[$cont][1]." desc";
			?>'; document.pag.submit();"><?php echo $columnas[$cont][0]; ?></a><?php
			if (strstr($ordenar, $columnas[$cont][1]." desc")) echo "<span class=\"css_down\"></span>";
			elseif (strstr($ordenar, $columnas[$cont][1]." asc")) echo "<span class=\"css_up\"></span>";
//			if (strstr($ordenar, $columnas[$cont][1]." desc")) echo "<img src=\"../images/down.gif\" width=\"15\" height=\"16\" />";
//			elseif (strstr($ordenar, $columnas[$cont][1]." asc")) echo "<img src=\"../images/up.gif\" width=\"15\" height=\"16\" />";
			echo "</td>";
		} else {?>
		<td class="celdas separa" width="<?php echo $columnas[$cont][2] ?>" align="<?php echo $columnas[$cont][3] ?>"><a class="paglink" href="#" onClick="document.pag.orden.value = '<?php
		if (strstr($ordenar, $columnas[$cont][1]." desc")) echo $columnas[$cont][1]." asc";
		else echo $columnas[$cont][1]." desc";
		?>'; document.pag.submit();"><?php echo $columnas[$cont][0]; ?></a><?php
		if (strstr($ordenar, $columnas[$cont][1]." desc")) echo "<span class=\"css_down\"></span>";
		elseif (strstr($ordenar, $columnas[$cont][1]." asc")) echo "<span class=\"css_up\"></span>";
		
		?></td>
	<?php }} ?>

  </tr>

  <?php
	while($usr->next_record()) { ?>
  <tr class="cuerpo" id="campo<?php echo $usr->f("id") ?>" onMouseOver="javascript:cambia(this.id, 'over');" onMouseOut="javascript:cambia(this.id, null)">
<?php for ($cont = 0; $cont < count($colEsp); $cont++) {
		if ($colEsp[$cont][0] == "e" || $colEsp[$cont][0] == "v")
			$oncli = "document.pag.cambiar.value='". $usr->f("id") ."'; document.pag.submit();";
		elseif ($colEsp[$cont][0] == "d")
			$oncli = "return alerta('".str_replace (',', '', $usr->f("valor{val}"))."', '".$usr->f("estado")."',  '".$usr->f("id")."', 'R' );";
		elseif ($colEsp[$cont][0] == "p")
			$oncli = "return alerta('".str_replace (',', '', $usr->f("valor{val}"))."', '".$usr->f("estado")."',  '".$usr->f("id")."', 'P' );";
		elseif ($colEsp[$cont][0] == "x")
			$oncli = "return alerta('".str_replace (',', '', $usr->f("valor{val}"))."', '".$usr->f("estado")."',  '".$usr->f("id")."', 'S', '".$usr->f("solDe")."' );";
		elseif ($colEsp[$cont][0] == "t")
			$oncli = "return alerta(1, '".$usr->f("tipo")."',  '".$usr->f("id")."', 'T' );";
		elseif ($colEsp[$cont][0] == "i")
			$oncli = "window.open('imprimeest.asp?us=". $usr->f("id") ."','nueva','menubar=no,scrollbars=yes')";
		elseif ($colEsp[$cont][0] == "b")
			$oncli = "if (confirm('". $alerta ."')) {document.pag.borrar.value='". $usr->f("id") ."'; document.pag.submit();}";
		elseif ($colEsp[$cont][0] == "c")
			$oncli = "return alerta('".str_replace (',', '', $usr->f("valor{val}"))."', '".$usr->f("estado")."',  '".$usr->f("id")."', 'A' );";
		elseif ($colEsp[$cont][0] == "m")
			$oncli = "window.open('index.php?componente=comercio&pag=pago&identf=". $usr->f("id") ."','_self')";
		elseif ($colEsp[$cont][0] == "f")
			$oncli = "return alerta('', '',  '".$usr->f("id")."', 'F' );";
		elseif ($colEsp[$cont][0] == "n")
			$oncli = "return transf('".$usr->f("id")."');";
		?>
    <td class="separa" align="center"><span class="<?php echo $colEsp[$cont][2] ?>" onClick="<?php echo $oncli ?>" 
    	alt="<?php echo $colEsp[$cont][1] ?>" title="<?php echo $colEsp[$cont][1] ?>"></span></td>
	<?php }
  for ($cont = 0; $cont < count($columnas); $cont++) {
  	if ($cont == (count($columnas)-1)) { ?>
	     <td style="padding-left:7px; padding-right:7px; padding-top:4px; padding-bottom:4px" align="<?php echo $columnas[$cont][4]; ?>"><?php
		if (preg_match('/fecha/', $columnas[$cont][1])) {
			if ( $usr->f($columnas[$cont][1]) != 0 )
				echo str_replace(' 00:00:00', '', formatea_fecha($usr->f($columnas[$cont][1])));
			else echo '-';
		}
		elseif ( preg_match('/{val}/',  $columnas[$cont][1])) echo formatea_numero($usr->f($columnas[$cont][1]));
		elseif ( preg_match('/{ip}/',  $columnas[$cont][1])) echo "<a target='_blank' href='http://legacytools.dnsstuff.com/tools/ipall.ch?ip=".
				$usr->f($columnas[$cont][1])."'>".$usr->f($columnas[$cont][1])."</a>";
		elseif ( preg_match('/{geoip}/',  $columnas[$cont][1])) {
			if (function_exists("geoip_country_name_by_name"))
				echo "<span title='".geoip_country_name_by_name($usr->f($columnas[$cont][1]))."' alt='".geoip_country_name_by_name($usr->f($columnas[$cont][1]))."' >".
						geoip_country_code3_by_name($usr->f($columnas[$cont][1]))."</span>";
			else $usr->f($columnas[$cont][1]);
		}
	    elseif ( preg_match('/{col}/',  $columnas[$cont][1])) {
	        $jav = "<script>
	                    document.getElementById('campo".$usr->f("id")."').style.color='".$usr->f($columnas[$cont][1])."';
	                </script>";
	        echo $jav;
	    }
		else
		echo $usr->f($columnas[$cont][1]);
	 ?></td>
	<?php } else { ?>
    <td class="separa" style="padding-left:7px; padding-right:7px; padding-top:4px; padding-bottom:4px" align="<?php echo $columnas[$cont][4]; ?>"><?php
	if (preg_match('/fecha/', $columnas[$cont][1])) {
		if ( $usr->f($columnas[$cont][1]) != 0 )
			echo str_replace(' 00:00:00', '', formatea_fecha($usr->f($columnas[$cont][1])));
		else echo '-';
	}
	elseif ( preg_match('/{val}/',  $columnas[$cont][1])) echo formatea_numero($usr->f($columnas[$cont][1]));
	elseif ( preg_match('/{ip}/',  $columnas[$cont][1])) echo "<a target='_blank' href='http://legacytools.dnsstuff.com/tools/ipall.ch?ip=".
				$usr->f($columnas[$cont][1])."'>".$usr->f($columnas[$cont][1])."</a>";
	elseif ( preg_match('/{geoip}/',  $columnas[$cont][1])) {
		if (function_exists("geoip_country_name_by_name")) 
				echo "<span title='".geoip_country_name_by_name($usr->f($columnas[$cont][1]))."' alt='".geoip_country_name_by_name($usr->f($columnas[$cont][1]))."' >".
						geoip_country_code3_by_name($usr->f($columnas[$cont][1]))."</span>";
			else $usr->f($columnas[$cont][1]);
	}
    elseif ( preg_match('/{col}/',  $columnas[$cont][1])) {
        $jav = "<script>
                    document.getElementById('campo".$usr->f("id")."').style.color='".$usr->f($columnas[$cont][1])."';
                </script>";
        echo $jav;
    }
    elseif (is_float($usr->f($columnas[$cont][1]))) echo 'hola';
	else
	echo $usr->f($columnas[$cont][1]);
	 ?></td>
	<?php }} ?>
  </tr>
	<?php
}
?>
<script language="JavaScript" type="text/JavaScript">
function cambia(renglon, acc) {
	if (acc == 'over') document.getElementById(renglon).bgColor='#CCCCCC';
	else document.getElementById(renglon).bgColor='';
}

function alerta(valor=null, estado=null, id=null, accion, solDe=null) {

	 if (accion == 'T') {
		if (estado == "T") {
			/*document.pag.factura.value=id;
			envia(this.form);*/
			window.open('index.php?componente=comercio&pag=factura&tf='+id, "_self");
//			return true;

		} else alert('<?php echo _GRUPOS_ALERTA_FACT; ?>');

		return false;
	 }

	 if (accion == 'M') {
		document.forms[0].inserta.value=id;
		document.forms[0].submit();
		return true;
	} else if (accion == 'F') {
		document.pag.pagar.value=id;
		document.pag.submit();
		return true;
	} else if (accion == 'P' && (estado != 'P')){
				if (confirm('<?php echo $alerta4 ?>')) {
					document.pag.pagar.value=id;
					document.pag.submit();
					return true;
				}
	} else {
	 	if (valor > 0 && (estado == 'V' || estado == 'A' ) && solDe == 1) {
			alert('<?php echo $alerta5 ?>');
		} else if (valor > 0 && (estado == 'V' || estado == 'A' )) {
			if (accion == 'A') {
				if (confirm('<?php echo $alerta2 ?>')) {
					document.pag.borrar.value = id;
					document.pag.submit();
					return true;
				}
			} else if (accion == 'S') {
				window.open('index.php?componente=comercio&pag=solde&tf='+id, "_self");
			} else {
				document.pag.cambiar.value=id;
				document.pag.submit();
				return true;
			}
		} else {
			alert('<?php echo $alerta3 ?>');
		}
	}
	return false;
}

//$(document).ready(function(){
	function transf(cierre) {
		$(".alerti").esperaDiv('muestra');
		$.post('componente/comercio/ejec.php',{
			fun:'inscierre',
			cie:cierre
		},function(data){
			var datos = eval('(' + data + ')');
			$('.alerti').esperaDiv('cierra');
			$("#enviaForm").show();
			if (datos.error.length > 0) alert(datos.error);
			if (datos.pase.length > 0) {
				$("#salCierre").html(datos.pase[1]);
			}
		});
	}
//});
</script>
        <tr>
		  <td id="dale" colspan="<?php echo $cantColumnas; ?>"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr class="encabezamiento">
          <td width="100" align="left">&nbsp;<?php echo $num_records; ?> Record(s)</td>
          <td align="center">Pages:
            <?php

	  if (($mPage - 4) < 1) $inicio = 1;
	  else $inicio = $mPage - 3;

	  if ($num_records >= (($mPage + 3) * $limite)) $final = $mPage + 3;
	  else $final = ceil($num_records/$limite);

	  for ($x = $inicio; $x <= $final; $x++) {
		if ($mPage == $x) {
			echo "<label style=\"font-size:11px \"><strong> ";
			echo $x."</strong></label>&nbsp;";
		}else {
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=$x; document.pag.submit()\" href=\"#\"> ";
			echo "$x</a>&nbsp;";
		}
	}
	 ?></td>
          <td width="120" align="right"><?php

		if ($mPage != 1)
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage-1) ."; document.pag.submit()\" href=\"#\">&lt;&lt;..</a>"; ?>
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
		if ($mPage != ceil($num_records/$limite))
			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage+1) ."; document.pag.submit()\" href=\"#\">..&gt;&gt;</a>"; ?>
</td>
        </tr>
      </table>
        </td>
      </tr>

</table>
  </form></div>
<?php
return $sql;
}

//construye las tablas de resultados
//Tabla de muestra de resultados
function tablanp( $query, $columnas) {
/*
tabla_ancho - ancho de la tabla que muestra los resultados
idioma - Idioma de entrada, permite prefijar valores de mensajes que de otra forma habr&iacute;a que entrarlos
vista - Vista de la BD contra la cual se realizar&aacute; todo el trabajo
orden -  ordenamiento de los valores de salida de la vista por default
busquedaInicio - Columnas de la vista a buscar por defult
colEsp - array de array para las columnas especiales (editar, ver, borrar..) en la forma (tipocolumna, textoalt, caminoimagen, titulocolumna)
busqueda - array de array para las busquedas en la forma (nombrecampo, columnatabla)
columnas - array de array con los datos de (titulocolumna, campotabla, anchocolumna, posicion)
*/

?>
<form action="<?php echo $GLOBALS['sitio_url'].'index.php?'. $_SERVER['QUERY_STRING'] ?>" method="post" name="pag" id="pag">
<table align="center" width="<?php echo $tabla_ancho; ?>" border="0" cellspacing="0" cellpadding="0">
<?php

		    $usr = new ps_DB();
			$usr->query($query);
			$num_records = $usr->num_rows();
			$tot = 0;

			$usr->query($query);
			$cantColumnas = (count($columnas) * 2 + count($colEsp) * 2) - 1;
// 			$usr->reset();

		 ?>
      <tr>
      <td colspan="<?php echo $cantColumnas; ?>"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr class="encabezamiento">
          <td width="100" align="left">&nbsp;<?php echo $num_records; ?> Record(s)</td>
          <td align="center"></td>
          <td width="120" align="right"></td>
        </tr>
      </table>
        </td>
      </tr>
    <tr>
      <td height="1" colspan="<?php echo $cantColumnas; ?>"></td>
    </tr>
    <tr class="encabezamiento2">
	<?php
//columnas normales
	 for ($cont = 0; $cont < count($columnas); $cont++) {
		if ($cont != 0) {
		?>
		<td width="1" class="separador1"></td>
		<?php } ?>
		<td class="celdas" width="<?php echo $columnas[$cont][2] ?>" align="<?php echo $columnas[$cont][3] ?>"><?php echo $columnas[$cont][0]; ?></td>
	<?php }

//columnas especiales
	for ($cont = 0; $cont < count($colEsp); $cont++) { ?>
	<td width="1" class="separador1"></td>
    <td class="celdas" width="50" align="center">&nbsp;<?php echo $colEsp[$cont][3]; ?>&nbsp;</td>
	<?php } ?>
  </tr><?php
	while($usr->next_record()) { ?>
  <tr class="cuerpo" id="campo<?php echo $usr->f("id") ?>" onMouseOver="cambia(this.id, 'over');" onMouseOut="cambia(this.id, '')">
  <?php for ($cont = 0; $cont < count($columnas); $cont++) {
  	if ($cont != 0) { ?>
	    <td width="1" class="separador2"></td>
	<?php } ?>
    <td style="padding-left:7px; padding-right:7px; padding-top:4px; padding-bottom:4px" align="<?php echo $columnas[$cont][4]; ?>"><?php
	if (preg_match('/fecha/',  $columnas[$cont][1])) {
		if ( $usr->f($columnas[$cont][1]) != 0 )
			echo date('d/m/Y H:i:s', $usr->f($columnas[$cont][1]));
		else echo '-';
	}
	elseif ( preg_match('/{val}/',  $columnas[$cont][1])) { 
		echo formatea_numero($usr->f($columnas[$cont][1]));
		if ( preg_match('/{tot}/',  $columnas[$cont][1])) {
				$tot += number_format($usr->f($columnas[$cont][1]),2,'.','');
		}
	} 
	elseif ( preg_match('/{col}/',  $columnas[$cont][1])) {
        $jav = "<script>
                    document.getElementById('campo".$usr->f("id")."').style.color='".$usr->f($columnas[$cont][1])."';
                </script>";
        echo $jav;
    }
	else
	echo $usr->f($columnas[$cont][1]);
	 ?></td>
	<?php }
	for ($cont = 0; $cont < count($colEsp); $cont++) {
		if ($colEsp[$cont][0] == "e" || $colEsp[$cont][0] == "v")
			$oncli = "document.pag.cambiar.value='". $usr->f("id") ."'; envia(this.form);";
		elseif ($colEsp[$cont][0] == "i")
			$oncli = "window.open('imprimeest.asp?us=". $usr->f("id") ."','nueva','menubar=no,scrollbars=yes')";
		elseif ($colEsp[$cont][0] == "b")
			$oncli = "if (confirm('". $alerta ."')) {document.pag.borrar.value='". $usr->f("id") ."'; envia(this.form)}";
		?>
    <td width="1" class="separador2"></td>
    <td align="center"><input src="<?php echo $colEsp[$cont][2] ?>" name="u" type="image" onClick="<?php echo $oncli ?>" alt="<?php echo $colEsp[$cont][1] ?>" /></td>
    <?php } ?>
  </tr><?php
}
?>
<script language="JavaScript" type="text/JavaScript">
function cambia(renglon, acc) {
	if (acc == 'over') document.getElementById(renglon).bgColor='#CCCCCC';
	else document.getElementById(renglon).bgColor='';
}
</script>
      <tr>
		  <td id="dale" colspan="<?php echo $cantColumnas; ?>"><table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr class="encabezamiento">
          <td width="100" align="left">&nbsp;<?php echo $num_records; ?> Record(s)</td>
          <td align="center">
            <?php
			echo _COMERCIO_EUROSC." "._REPORTE_TOTAL.": ".formatea_numero($tot);
// 	  if (($mPage - 4) < 1) $inicio = 1;
// 	  else $inicio = $mPage - 3;

// 	  if ($num_records >= (($mPage + 3) * $limite)) $final = $mPage + 3;
// 	  else $final = ceil($num_records/$limite);

// 	  for ($x = $inicio; $x <= $final; $x++) {
// 		if ($mPage == $x) {
// 			echo "<label style=\"font-size:11px \"><strong> ";
// 			echo $x."</strong></label>&nbsp;";
// 		}else {
// 			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=$x; document.pag.submit()\" href=\"#\"> ";
// 			echo "$x</a>&nbsp;";
// 		}
// 	}
	 ?></td>
          <td width="120" align="right"><?php

// 		if ($mPage != 1)
// 			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage-1) ."; document.pag.submit()\" href=\"#\">&lt;&lt;..</a>"; ?>
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
// 		if ($mPage != ceil($num_records/$limite))
// 			echo "<a class=\"paglink\" onClick=\"document.pag.cmbPageSelect.value=". ($mPage+1) ."; document.pag.submit()\" href=\"#\">..&gt;&gt;</a>"; ?>
</td>
        </tr>
      </table>
        </td>
      </tr>
</table></form>
<?php
}

function enviaError ($textoCorreo) {
	global $correo;
	$correo->todo(9, "Error", $textoCorreo);
	// 	exit;
}


 ?>
