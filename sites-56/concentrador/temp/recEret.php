
<?php 
define( '_VALID_ENTRADA', 1 );
session_start();
require_once( '../configuration.php' );
require_once '../include/mysqli.php';
// require_once( '../include/database.php' );
// $database = &new database($host, $user, $pass, $db, $table_prefix);
// require_once( '../include/ps_database.php' );
require_once( '../admin/classes/entrada.php' );
require_once( '../include/hoteles.func.php' );
require_once( '../include/correo.php' );

$correoC = new correo();
$temp = new ps_DB;
$ent = new entrada;

$d = $_REQUEST;
$merchant = 'Cubana';
$subject = 'Recibe datos de Amadeus';
$correoMi = "fecha=".date('d/m/Y H:i:s')."<br>\n";

// print_r($d);
//	correoAMi($subject,$correoMi);
foreach ($_REQUEST as $key => $value) {
	$correoMi .= "\n$key=$value<br>\n";
}

//$d['MERCHANT_ID'] = 'Cubana';

if (count($d) > 0) {
	if (isset($d['fac'])) {
		if (!($cod = $ent->isAlfanumerico($d['fac'], 8))) {$correoMi .= "<!-- falla por badnumber -->"; $correoC->todo(17, $subject, $correoMi); exit;}
		if (!($empr = $ent->isEntero($d['com'], 14))) {$correoMi .= "<!-- falla por badnumber -->"; $correoC->todo(17, $subject, $correoMi); exit;}
		
		$q = "update tbl_amadeus set recibida = 1 where idcomercio = '$empr' and rl = '$cod'";
		$correoMi .= "<br>\n". $q;
		$temp->query($q);
		
	} else {
		$sessionid = $d['SESSION_ID'];
		if (!($checksum = $ent->isAlfanumerico($d['CHECKSUM'], 128))) {$correoMi .= "falla por checksum";  echo "<!-- falla por checksum -->"; $correoC->todo(17, $subject, $correoMi); exit;}
//		if (!($sessionid = $d['SESSION_ID'])) {$correoMi .= "falla por sessionid";  echo "<!-- falla por sessionid -->"; correoAMi($subject,$correoMi); exit;}
		if (!($monto = $ent->isNumero($d['AMOUNT']))) {$correoMi .= "falla por monto";  echo "<!-- falla por monto -->"; $correoC->todo(17, $subject, $correoMi); exit;}
		if (!($currency = $ent->isAlfabeto($d['CURRENCY'], 3))) {$correoMi .= "falla por currency";  echo "<!-- falla por currency -->"; $correoC->todo(17, $subject, $correoMi); exit;}
		if (!($rl = $ent->isAlfanumerico($d['RL'], 10))) {$correoMi .= "falla por resorce locator";  echo "<!-- falla por resorce locator -->"; $correoC->todo(17, $subject, $correoMi); exit;}
		if (!($correo = $ent->isCorreo($d['USER_EMAIL']))) {$correoMi .= "falla por correo";  echo "<!-- falla por correo -->"; $correoC->todo(17, $subject, $correoMi); exit;}
		if (!($merchant = $ent->isAlfanumerico($d['MERCHANT_ID']))) {$correoMi .= "falla por merchant";  echo "<!-- falla por merchant -->"; $correoC->todo(17, $subject, $correoMi); exit;}
		$q = "select idmoneda from tbl_moneda where moneda = '".strtoupper($currency)."' and idmoneda in (124,840,978)";
		$temp->query($q);
		$correoMi .= "\n<br>$q";
		if ($temp->num_rows() == 0) {$correoMi .= "\n<br>moneda no soportada";  echo "<!-- moneda no soportada -->"; $correoC->todo(17, $subject, $correoMi); exit;}
		$idmoneda = $temp->f('idmoneda');
		
		if ($d['LANGUAGE'] == 'ES') $idio = 'es';
		else $idio = 'en';
		
		if($d['MERCHANT_ID'] == 'Cubana') {
			$arrMerc = array('Cubana' => '129025985109'); //array de correspondencia al idcomercio nuestro
			$arrClave = array('Cubana' => 'fgrt34sdsw2'); //array de correspondecia a la clave secreta Amadeus
		} elseif ($d['MERCHANT_ID'] == 'Prueba') {
			$arrMerc = array('Prueba' => '122327460662'); //array de correspondencia al idcomercio nuestro
			$arrClave = array('Cubana' => 'fgrt34sdsw2'); //array de correspondecia a la clave secreta Amadeus
		}


		$claveSecreta = $arrClave[$merchant];; //Clave secreta suministrada por Paul
		$idMerch = $arrMerc[$merchant];
		
		$correoMi .= "\n<br> md5($sessionid.$merchant.$monto.$currency.$rl.urlencode($correo).$claveSecreta)";

		if ($d['CHECKSUM_ALGORITHM'] != 'HMACSHA512')
			$md5 = strtoupper(md5($sessionid.$merchant.$monto.$currency.$rl.urlencode($correo).$claveSecreta));
		else 
			$md5 = hash_hmac('sha512', $sessionid.$merchant.$monto.$currency.$rl.urlencode($correo), $claveSecreta);
		$correoMi .= "\n<br>".$md5;
// 		echo $correoMi;

		if ($md5 == $checksum) {

//			echo "<br><br><span style='color:green'>MD5 and CHECKSUM MATCH!!!</span>";
			$nom = $d['USER_FIRST_NAME']." ".$d['USER_LAST_NAME'];
			
			$q = "select pasarela from tbl_comercio where idcomercio = '$idMerch'";
			$temp->query($q);
			$pasarela = $temp->f('pasarela');
			if (strpos($pasarela, ',') > 0){
				$arrP = explode(',', $pasarela);
				for ($i=0;$i<count($arrP);$i++){
					if ($arrP[$i] != 13) {$pasarela = $arrP[$i]; break 1;}
				}
				
			}
//			if ($idmoneda == '124') $pasarela = 31; //31-Sabadell2 3D
			if ($idmoneda == '124') $pasarela = 32; //32-Bankia4 3D
			$correoMi .= "\n<br> pasarela = ".$pasarela." <br>\n";
			
			/*comentar la linea de abajo para habilitar las pasarelas por la administracion*/
			//$pasarela = 13;
			
			$q = "select count(id) total from tbl_amadeus where idcomercio = '$idMerch' and rl = '$rl'";
			$temp->query($q);
			
			if ($temp->f('total') == 0) { //si la operación no aparece en la tabla amadeus la inserto
				$query = "insert into tbl_reserva (id_comercio, codigo, nombre, servicio, " .
							"valor_inicial, moneda, fecha, idioma, pasarela, tiempoV, email) " .
							"values ('$idMerch', '$rl', '".  htmlentities($nom, ENT_QUOTES)."', 'Pasaje Vuelos', " .
							"{$monto}, '{$idmoneda}', " . time() . ", '$idio', $pasarela, 1, '".$d['USER_EMAIL']." - ".$d['USER_MOBILE_PHONE']."')";
				$temp->query($query);
//				echo $query;
				if (strlen($temp->error)) {$correoMi .= "\n<br>".$temp->error;}
				$correoMi .= "\n<br>". $query;

				$q = "insert into tbl_amadeus (idcomercio, idmoneda, rl, transacc, monto, sesion, urlok, urlko, estado, fecha, urlKeepAlive, lastName) 
						values ($idMerch, $idmoneda, '$rl', '$md5', $monto, '$sessionid', '".$d['CONFIRMATION_URL']."', '".$d['CANCELLATION_URL']."', 'P', ".time().", '".$d['KEEPALIVE_URL']."', "
                        . "'".htmlentities($d['USER_LAST_NAME'],ENT_QUOTES)."')";
				$correoMi .= "\n<br>". $q;
				$temp->query($q);
				if (strlen($temp->error)) {$correoMi .= "\n<br>".$temp->error;}

				$q = "select palabra from tbl_comercio where idcomercio = $idMerch";
				$correoMi .= "\n<br>". $q; //;
				$temp->query($q);
				$palabra = $temp->f("palabra");

				$firma = md5($idMerch.$rl.($monto * 100).$idmoneda.'P'.$palabra);//echo $correoMi;
		?>
<form action="<?php echo _ESTA_URL; ?>/index.php" name="avanza" method="post">
		<input type="hidden" value="<?php echo $idMerch; ?>" name="comercio" />
		<input type="hidden" value="<?php echo $rl; ?>" name="transaccion" />
		<input type="hidden" value="<?php echo ($monto * 100); ?>" name="importe" />
		<input type="hidden" value="<?php echo $idmoneda; ?>" name="moneda" />
		<input type="hidden" value="P" name="operacion" />
		<input type="hidden" value="<?php echo $pasarela; ?>" name="pasarela" />
		<input type="hidden" value="<?php echo $firma; ?>" name="firma" />
	</form>
	<script type="text/javascript">
	document.avanza.submit();
	</script>

		<?php
			} else {
				$correoMi .= "\n<br>Operación repetida.";
				;
				echo "<span style='color:red;font-size:16px;display:block;text-align:center;margin-top:30px'>Esta es una operación repetida, por favor entre al sitio de Cubana si necesita volver a realizar una reserva<br /><br />This is a repeated operation, please enter the Cubana website if you need to make a reservation</span>";
			}
		} else {
			$correoMi = "El checksum no machea<br>\n".$correoMi;
			$correoC->todo(17, 'Falla en el Checksum', $correoMi);
				echo "<span style='color:red;font-size:16px;display:block;text-align:center;margin-top:30px'>Esta operación no está permitida<br /><br />This operation is not allowed.</span>";
			
			die();
		}
	}
}
$correoC->todo(17, $subject, $correoMi);
//mail('jtoirac@gmail.com',$subject, $correoMi,"MIME-Version: 1.0\n Content-type: text/html; charset=iso-8859-1\n From: ".$this->from."\n Reply-To: ". $this->from ."\n Return-Path: ". $this->from ."\n X-Mailer: PHP". phpversion() ."\n");
//;
?>


