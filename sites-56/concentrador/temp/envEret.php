<script>
if (parent.frames.length > 0) {
parent.location.href = self.document.location;
}
</script>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define( '_VALID_ENTRADA', 1 );
require_once( '../configuration.php' );
require_once( '../include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( '../include/ps_database.php' );
require_once( '../admin/classes/entrada.php' );
require_once( '../include/hoteles.func.php' );
require_once( '../include/correo.php' );

$correo = new correo();
$temp = new ps_DB;
$ent = new entrada;
$subject = 'Envío de datos a Amadeus';

$d = $_REQUEST;
$correoMi = "";
$correoMi .= "fecha=".date('d/m/Y H:i:s')."<br>\n";

//print_r($d);
foreach ($_REQUEST as $key => $value) {
	$correoMi .= "$key=$value<br>\n";
}

if (!($comercio = $ent->isEntero($d['comercio']))) {$correoMi .= "falla por comercio inválido - ".$d['comercio'];  echo "falla por comercio inválido"; $correo->todo(18, $subject, $correoMi); exit;}
if (!($codigo = $ent->isEntero($d['codigo']))) {$correoMi .= "falla por transacción - ".$d['codigo'];  echo "falla por transacción"; $correo->todo(18, $subject, $correoMi); exit;}
//if (!($importe = $ent->isEntero($d['importe']))) {$correoMi .= "falla por importe";  echo "falla por importe"; $correo->todo(18, $subject, $correoMi); exit;}
if (!($moneda = $ent->isEntero($d['moneda']))) {$correoMi .= "falla por moneda - ".$d['moneda'];  echo "falla por moneda"; $correo->todo(18, $subject, $correoMi); exit;}
if (!($resultado = $ent->isAlfabeto($d['resultado']))) {$correoMi .= "falla por el resultado de la transacción - ".$d['resultado'];  echo "falla por el resultado de la transacción"; 
    $correo->todo(18, $subject, $correoMi); exit;}
if (!($transaccion = $ent->isAlfanumerico($d['transaccion']))) {$correoMi .= "falla por el código del banco - ".$d['transaccion'];  echo "falla por el código del banco"; 
    $correo->todo(18, $subject, $correoMi); exit;}
if (!($fecha = $ent->isDate($d['fecha']))) {$correoMi .= "falla por la fecha - ".$d['fecha'];  echo "falla por la fecha"; $correo->todo(18, $subject, $correoMi); exit;}
if (!($firma = $ent->isAlfanumerico($d['firma']))) {$correoMi .= "falla por firma - ".$d['firma'];  echo "falla por firma"; $correo->todo(18, $subject, $correoMi); exit;}
// $comercio = $ent->isEntero($d['comercio']);
// $codigo = $ent->isEntero($d['codigo'], 12);
// $moneda = $ent->isEntero($d['moneda'],3);
// $resultado = $ent->isAlfabeto($d['resultado'],1);
// $transaccion = $ent->isAlfanumerico($d['transaccion']);
// $fecha = $ent->isDate($d['fecha']);
// $firma = $ent->isAlfanumerico($d['firma']);
// $correoMi .= "comercio=$comercio<br>\n";
// $correoMi .= "codigo=$codigo<br>\n";
// $correoMi .= "moneda=$moneda<br>\n";
// $correoMi .= "resultado=$resultado<br>\n";
// $correoMi .= "transaccion=$transaccion<br>\n";
// $correoMi .= "fecha=$fecha<br>\n";
// $correoMi .= "firma=$firma<br>\n";

$q = "select palabra from tbl_comercio where idcomercio = ".$comercio;
$correoMi .= "<br>\n". $q; 
$temp->query($q);
$palabrasecret = $temp->f('palabra');

$correoMi .= "<br>\n".$_SERVER['HTTP_REFERER'];
//chequea la firma
//if ($firma != (md5($comercio.$transaccion.$importe.$moneda.$resultado.$codigo.$fecha.$palabrasecret))) {$correoMi .= "firma inválida";  echo "firma inválida"; $correo->todo(18, $subject, $correoMi); exit;}
$correoMi .= "<br>\nCalcufirma = ". md5($comercio.$transaccion.$importe.$moneda.$resultado.$codigo.$fecha.$palabrasecret);
$correoMi .= "<br>\nCalcufirma2 = ". md5($comercio.$transaccion.$importe.$moneda.$resultado.$codigo.$fecha);

//Actualiza la tabla amadeus con los datos de la transacción
$q = "update tbl_amadeus set idtransaccion = '$codigo', estado = '$resultado', fechamod = ".time().", codigo = '$codigo' where idcomercio = '$comercio' and rl = '$transaccion'";
$correoMi .= "<br>\n". $q;
$temp->query($q);

//busca en la tabla amadeus los datos para enviar de vuelta a amadeus
$q = "select urlko url, ";
if ($resultado == 'A') $q = "select urlok url, ";
$q .= " sesion from tbl_amadeus  where idcomercio = '$comercio' and rl = '$transaccion'";
$correoMi .= "<br>\n". $q;
$temp->query($q);
$urlEnv = $temp->f('url')."&FINAL_CONF=FALSE";
$sessionid = $temp->f('sesion');

$encTime = date('YmdHis',time());
$arrMerc = array('129025985109' => 'AAWOAAWO'); //array para site
$arrClave = array('129025985109' => 'fgrt34sdsw2');

$url = "https://www.concentradoramf.com/cubanaLand.php?fac=$transaccion&com=$comercio";

$calmd5 = strtoupper(md5($sessionid.$codigo.urlencode($url).$arrClave[$comercio]));
$correoMi .= "<br>\n md5($sessionid.$codigo.urlencode($url).$arrClave[$comercio])";

$correoMi .= "<br>\nurlEnv=$urlEnv"; 
$correoMi .= "<br>\nCHECKSUM=$calc_md5"; 
$correoMi .= "<br>\nSITE=".$arrMerc[$comercio]; 
$correoMi .= "<br>\nENC_TYPE=1"; 
$correoMi .= "<br>\nENC="; 
$correoMi .= "<br>\nENC_TIME=".$encTime; 
$correoMi .= "<br>\nPAYMENT_REFERENCE=".$codigo; 
$correoMi .= "<br>\nACKNOWLEDGEMENT_URL=".$url; 
$correoMi .= "<br>\nCHECKSUM=".$calmd5;
$correoMi .= "<br>\nAPPROVAL_CODE=".$codigo; 
$correoMi .= "<br>\nCANCELLATION_URL=https://www.concentradoramf.com/amadeus/cancel.php"; 

if (
		$comercio == '122327460662'		//Comentar esta línea para deshabilitar Prueba
//		|| $comercio == '129025985109'	//Descomentar esta línea para habilitar Cubana
		) {
	$correoMi .=  "<br>\nEnvío del formulario por curl a Amadeus<br>\n";
	//Envío del formulario por Cdonts a Amadeus
	$data = array(
		"SITE"=>$arrMerc[$comercio],
		"ENC_TYPE"=>'1',
		"ENC"=>"",
		"ENC_TIME"=>$encTime,
		"PAYMENT_REFERENCE"=>$codigo,
		"ACKNOWLEDGEMENT_URL"=>$url,
		"CHECKSUM"=>$calmd5,
		"APPROVAL_CODE"=>$codigo,
		"CANCELLATION_URL"=>"https://www.concentradoramf.com/amadeus/cancel.php"
			);
	
	$urlEnv = "https://www.concentradoramf.com/cubanaLand.php"; //Comentar esta línea para dejar el desarrollo normal
	$correoMi .=  $urlEnv."<br>\n";
	$ch = curl_init($urlEnv);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$correoMi .=  "Ejecutó la llamada por curl - ".curl_exec($ch)."<br>\n";
	curl_close($ch);
	
} else {
?>
<form action="<?php echo $urlEnv; ?>" method="post">
	<input type="hidden" value="<?php echo $arrMerc[$comercio]; ?>" name="SITE" />
	<input type="hidden" value="1" name="ENC_TYPE" />
	<input type="hidden" value="" name="ENC" />
	<input type="hidden" value="<?php echo $encTime; ?>" name="ENC_TIME" />
	<input type="hidden" value="<?php echo $codigo; ?>" name="PAYMENT_REFERENCE" />
	<input type="hidden" value="<?php echo $url; ?>" name="ACKNOWLEDGEMENT_URL" />
	<input type="hidden" value="<?php echo $calmd5; ?>" name="CHECKSUM" />
	<input type="hidden" value="<?php echo $codigo; ?>" name="APPROVAL_CODE" />
	<input type="hidden" value="https://www.concentradoramf.com/amadeus/cancel.php" name="CANCELLATION_URL" />
	<!--<input type="submit" value="Envia"  />-->
</form>
<script type="text/javascript">
	document.forms[0].submit();
</script>
<?php
}

$correo->todo(18, $subject, $correoMi);

?>

