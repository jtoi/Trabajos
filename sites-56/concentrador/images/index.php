<?php

if (isset($_SERVER['SERVER_PORT']))
{
   ini_set('display_errors', 0);
   error_reporting(0);
   if ($_SERVER['SERVER_PORT'] == "80") //pasa a zona segura la transaccion
   {
       echo "<head><meta http-equiv=\"refresh\" content=\"0; url=https://www.amfconcentrador.com\"></head>";
   } else {
	  define( '_VALID_ENTRADA', 1 );
	  //ini_set("display_errors", 1);
	  //error_reporting(E_ALL & ~E_NOTICE);
	  if (!session_start()) session_start();

	  $d = $_POST;


	  if ($d['comercio'] && $d['transaccion'] && $d['importe'] && $d['moneda'] && $d['operacion'] && $d['firma'] ) {

		  $comercio = $d['comercio'];
		  $transaccion = $d['transaccion'];
		  $importe = $d['importe'];
		  $moneda = $d['moneda'];
		  $operacion = strtoupper($d['operacion']);
		  $firma = $d['firma'];

		  if ($_COOKIE['client']) {setcookie("client", null, time()-3600);}
		  setcookie("client", $firma, time()+3600);

         echo '<script>document.writeln("<div style=\"margin:"+
               window.innerHeight/2
               +"px 0 0 "+
               ((window.innerWidth)-400)/2
               +"px; width:400px; text-align:center;\">"
               )</script>
               <img src="images/circulo.gif" alt="Pagar en modo seguro" title="Pagar en modo seguro" /><br>
               Su transacci&oacute;n est&aacute; siendo procesada...<br>Your transacction is been processed...';

		  for ($i=0; $i<strlen($importe);$i++){
			  if (!strpos(' 0123456789', $importe{$i})) {$importe = false; break;}
		  }
		  for ($i=0; $i<strlen($moneda);$i++){
			  if (!strpos(' 0123456789', $moneda{$i})) {$moneda = false; break;}
		  }

		  if ($importe < 1 || strlen($importe) > 9) {echo "<!-- falla por importe -->"; exit;}
		  if ($moneda < 1 || strlen($moneda) != 3) {echo "<!-- falla por moneda -->"; exit;}
		  if (strlen($comercio) > 15) {echo "<!-- falla por comercio -->"; exit;}
		  if (strlen($transaccion) > 12) {echo "<!-- falla por transaccion -->"; exit;}
		  if (strlen($firma) > 32) {echo "<!-- falla por firma -->"; exit;}
		  if (!$operacion == 'P' || !$operacion == 'C' ) {echo "<!-- falla por operacion -->"; exit;}

		  require_once( 'configuration.php' );
		  require_once( 'include/database.php' );
		  $database = &new database($host, $user, $pass, $db, $table_prefix);
		  require_once( 'include/ps_database.php' );
		  require_once( 'include/hoteles.func.php' );

		  $temp = new ps_DB;

		  $firmaCheck = convierte($comercio, $transaccion, $importe, $moneda, $operacion);
		  if (_MOS_CONFIG_DEBUG) echo "$firmaCheck == $firma<br>";

			  if ($firmaCheck == $firma) {

                //Creación del identificador de la transaccion
                $query = "select prefijo_trans from #__comercio where idcomercio = '$comercio'";
                $temp->query($query);
                $prefijo = $temp->f('prefijo_trans');

				  $salida = false;
				  while (!$salida) {
                    $trans = (string)($prefijo).(date("mdHis"));//.(rand (10, 99));
					$query = "select count(*) from #__transacciones where idtransaccion = '$trans'";
					$temp->query($query);
					if ($temp->loadResult() == 0) $salida = true;

					  $query = "select count(*) from #__transacciones_old where idtransaccion = '$trans'";
					  $temp->query($query);
					  if ($temp->loadResult() != 0) $salida = false;
				  }

				  $query = "select idmoneda, estado, url from #__comercio where activo = 'S' and idcomercio = '$comercio'";
				  $temp->query($query);
				  $estado = $temp->f('estado');
				  $url = $temp->f('url');
				  $hora = time();

				  if ($temp->num_rows() == 0 ) {echo "<!-- falla por comercio -->"; exit;}
				  if ($moneda != $temp->f('idmoneda'))  {echo "<!-- falla por tipo de moneda -->"; exit;}

                  //Comprueba que la transaccion no se repita
                  $query = "select * from #__transacciones where sesion == '$firma'";
                  $temp->query($query);
                  if ($temp->loadResult()) {
                     //La transaccion se repite, lee los valores de la transaccion que están en la BD
                     $trans         = $temp->f('idtransaccion');
                     $hora          = $temp->f('fecha');
                  } else {
//                   La transacción no se repite, se inserta en la BD
                     $query = "insert into #__transacciones
                                 (idtransaccion, idcomercio, identificador, tipoOperacion,
                                 fecha, valor_inicial, tipoEntorno, moneda, estado, sesion)
                              values
                                 ('$trans', '$comercio', '$transaccion', '$operacion',
                                 $hora, $importe, '$estado', $moneda, 'P', '$firma')";
                     $temp->query($query);
                  }

				  if ( $estado == 'P' ) { //comercio en producción
					  $clave = desofuscar(_PALABR_OFUS, _CONTRASENA_OFUS);
					  $urlcomercio = _URL_COMERCIO;
					  $idioma = 'es';
					  $pais = 'ES';
					  $urlredir = _URL_DIR;
					  $localizador = _LOCALIZADOR;
					  $url_tpvv= _URL_TPV;// URL del TPV.
					  $firmaSal = strtoupper(SHA1(_ID_PTO._ID_COMERCIO.$trans.$importe.$moneda.$localizador.$clave));
			  //		$url_tpvv="";
		  // 			setlocale(LC_MONETARY, 'es_ES');
					  $importe100 = money_format('%.2n', $importe/100);

		  if (_MOS_CONFIG_DEBUG) echo "Terminal= "._ID_PTO."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "Comercio= "._ID_COMERCIO."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "transaccion= ".$trans."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "importe= ".$importe100."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "moneda= ".$moneda."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "clave= ".$clave."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "firmaSal= ".$firmaSal."<br>";

					  $lt="&lt;";
					  $gt="&gt;";
							  $xml.=$lt."tpv".$gt;
							  $xml.=$lt."oppago".$gt;
								  $xml.=$lt."idterminal".$gt._ID_PTO.$lt."/idterminal".$gt;
								  $xml.=$lt."idcomercio".$gt._ID_COMERCIO.$lt."/idcomercio".$gt;
								  $xml.=$lt."idtransaccion".$gt.$trans.$lt."/idtransaccion".$gt;
								  $xml.=$lt."moneda".$gt.$moneda.$lt."/moneda".$gt;
								  $xml.=$lt."importe".$gt.$importe100.$lt."/importe".$gt;
								  $xml.=$lt."urlcomercio".$gt.$urlcomercio.$lt."/urlcomercio".$gt;
								  $xml.=$lt."idioma".$gt.$idioma.$lt."/idioma".$gt;
								  $xml.=$lt."pais".$gt.$pais.$lt."/pais".$gt;
								  $xml.=$lt."urlredir".$gt.$urlredir.$lt."/urlredir".$gt;
								  $xml.=$lt."localizador".$gt.$localizador.$lt."/localizador".$gt;
								  $xml.=$lt."firma".$gt.$firmaSal.$lt."/firma".$gt;
							  $xml.=$lt."/oppago".$gt;
						  $xml.=$lt."/tpv".$gt;
					  $peticion=$xml;

					  $cadenSal = '
									<script>
						  function nameDefined(ckie,nme) {
                              var splitValues
                              var i
                              for (i=0;i<ckie.length;++i) {
                                  splitValues=ckie[i].split("=")
                                  if (splitValues[0]==nme) return true
                              }
                              return false
						  }

						  function delBlanks(strng) {
                              var result=""
                              var i
                              var chrn
                              for (i=0;i<strng.length;++i) {
                                  chrn=strng.charAt(i)
                                  if (chrn!=" ") result += chrn
                              }
                              return result
						  }

						  function getCookieValue(ckie,nme) {
                              var splitValues
                              var i
                              for(i=0;i<ckie.length;++i) {
                                  splitValues=ckie[i].split("=")
                                  if(splitValues[0]==nme) return splitValues[1]
                              }
                              return ""
						  }

						  function testCookie(cname, cvalue) {  //Tests to see if the cookie
							  var cookie=document.cookie           //with the name and value
							  var chkdCookie=delBlanks(cookie)  //are on the client computer
							  var nvpair=chkdCookie.split(";")
							  if(nameDefined(nvpair,cname))       //See if the name is in any pair
							  {
								  tvalue=getCookieValue(nvpair,cname)  //Gets the value of the cookie
								  if (tvalue == cvalue) return true
								  else return false
							  }
							  else return false
							  }
							  </script>
						  <form name="envia" action="'. $url_tpvv .'" method="post">

						  <input type="hidden" name="peticion" value="' .$peticion. '"/>
						  ';
						  if (_MOS_CONFIG_DEBUG){
							  $cadenSal .= '<input type="submit" />
										  </form>
										  <script language=\'javascript\'>
											  if (!testCookie("client", "'.$firma.'")) alert("Debe habilitar las cookies para pasar de este punto");
										  </script>';
						  } else {
							  $cadenSal .= '
										  <script language=\'javascript\'>
											  if (!testCookie("client", "'.$firma.'")) alert("Debe habilitar las cookies para pasar de este punto");
											  else document.envia.submit();
										  </script>';
						  }
					  echo $cadenSal;
				  } else { //comercio en desarrollo
					  $clave = desofuscar(_TESTPALABR_OFUS_TEST, _TESTCONTRASENA_OFUS_TEST);
					  $urlcomercio = _URL_COMERCIO;
					  $idioma = 'es';
					  $pais = 'ES';
					  $urlredir = _URL_DIR;
					  $localizador = _LOCALIZADOR;
					  $url_tpvv= _URL_TPV;// URL del TPV.
					  $firmaSal = strtoupper(SHA1(_TESTID_PTO_TEST . _TESTID_COMERCIO_TEST . $trans . $importe . $moneda . $localizador . $clave));
			  //		$url_tpvv="";
		  // 			setlocale(LC_MONETARY, 'es_ES');
					  $importe100 = money_format('%.2n', $importe/100);

		  if (_MOS_CONFIG_DEBUG) echo "Terminal= "._TESTID_PTO_TEST."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "Comercio= "._TESTID_COMERCIO_TEST."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "transaccion= ".$trans."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "importe= ".$importe100."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "moneda= ".$moneda."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "clave= ".$clave."<br>";
		  if (_MOS_CONFIG_DEBUG) echo "firmaSal= ".$firmaSal."<br>";

					  $lt="&lt;";
					  $gt="&gt;";
							  $xml.=$lt."tpv".$gt;
							  $xml.=$lt."oppago".$gt;
								  $xml.=$lt."idterminal".$gt._TESTID_PTO_TEST.$lt."/idterminal".$gt;
								  $xml.=$lt."idcomercio".$gt._TESTID_COMERCIO_TEST.$lt."/idcomercio".$gt;
								  $xml.=$lt."idtransaccion".$gt.$trans.$lt."/idtransaccion".$gt;
								  $xml.=$lt."moneda".$gt.$moneda.$lt."/moneda".$gt;
								  $xml.=$lt."importe".$gt.$importe100.$lt."/importe".$gt;
								  $xml.=$lt."urlcomercio".$gt.$urlcomercio.$lt."/urlcomercio".$gt;
								  $xml.=$lt."idioma".$gt.$idioma.$lt."/idioma".$gt;
								  $xml.=$lt."pais".$gt.$pais.$lt."/pais".$gt;
								  $xml.=$lt."urlredir".$gt.$urlredir.$lt."/urlredir".$gt;
								  $xml.=$lt."localizador".$gt.$localizador.$lt."/localizador".$gt;
								  $xml.=$lt."firma".$gt.$firmaSal.$lt."/firma".$gt;
							  $xml.=$lt."/oppago".$gt;
						  $xml.=$lt."/tpv".$gt;
					  $peticion=$xml;

					  $cadenSal = '
										  <script>
						  function nameDefined(ckie,nme)
						  {
						  var splitValues
						  var i
						  for (i=0;i<ckie.length;++i)
						  {
							  splitValues=ckie[i].split("=")
							  if (splitValues[0]==nme) return true
						  }
						  return false
						  }
						  function delBlanks(strng)
						  {
						  var result=""
						  var i
						  var chrn
						  for (i=0;i<strng.length;++i) {
							  chrn=strng.charAt(i)
							  if (chrn!=" ") result += chrn
						  }
						  return result
						  }
						  function getCookieValue(ckie,nme)
						  {
						  var splitValues
						  var i
						  for(i=0;i<ckie.length;++i) {
							  splitValues=ckie[i].split("=")
							  if(splitValues[0]==nme) return splitValues[1]
						  }
						  return ""
						  }
						  function testCookie(cname, cvalue) {  //Tests to see if the cookie
							  var cookie=document.cookie           //with the name and value
							  var chkdCookie=delBlanks(cookie)  //are on the client computer
							  var nvpair=chkdCookie.split(";")
							  if(nameDefined(nvpair,cname))       //See if the name is in any pair
							  {
								  tvalue=getCookieValue(nvpair,cname)  //Gets the value of the cookie
								  if (tvalue == cvalue) return true
								  else return false
							  }
							  else return false
							  }
							  </script>
						  <form name="envia" action="'. $url_tpvv .'" method="post">
						  <input type="hidden" name="peticion" value="' .$peticion. '"/>
						  ';
						  if (_MOS_CONFIG_DEBUG){
							  $cadenSal .= '<input type="submit" />
										  </form>
										  <script language=\'javascript\'>
											  if (!testCookie("client", "'.$firma.'")) alert("Debe habilitar las cookies para pasar de este punto");
										  </script>';
						  } else {
							  $cadenSal .= '
										  <script language=\'javascript\'>
											  if (!testCookie("client", "'.$firma.'")) alert("Debe habilitar las cookies para pasar de este punto");
											  else document.envia.submit();
										  </script>';
						  }
					  echo $cadenSal;


				  }
			  } else echo "<!-- falla por firma -->";
	  /*	} else {
			  echo "<script>alert('Para pasar este punto debe habilitar las cookies.')</script>";
		  }*/
	  } else {
		  echo "<!-- invalid -->";
	  }

   }
}

?>
