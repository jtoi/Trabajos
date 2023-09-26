<?php

//En la pestaña "Configuración" se incluirá el código PHP que permitirá la integración de la pasarela 3D Secure de BBVA. En negrita encontrará los valores que usted deberá cambiar en función de los datos que le ha proporcionado el banco al darse de alta del TPV Virtual.
//Con hacer un simple "copy/paste" de este texto a la zona "Configuración" y modificar los valores en negrita por los suyos (los proporcionados por el banco), tendrá activo y funcionando el método de pago. 

// Valores segun el comercio 
$url_tpvv="https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_RecepOpModeloServidor";// URL del TPV.
$obfuscated="**;**;**;**;**;**;**;**;**;**;**;**;**;**;**;**;**;**;**;**"; // Palabra secreta ofuscada - formato HEX
$clave="********";                                                         // Clave para descarga de palabra secreta (8 caracteres)
$comercio="**************";                                                // Codigo de comercio proporcionado.
$terminal="******";                                                        // Terminal usado.
$moneda="978";                                                             // 978=Euros.
$localizador;                                                               // Texto opcional
$idioma="es";
$pais="ES";
$urlcomercio="https://www.midominio.com/checkout-process.html";            // Para recibir los datos del TPV (sidesea)
$urlredir="https://www.midominio.com/index.php";                           //Donde se redireccionará después del pago

//Desofuscar la palabra secreta
$des_key=$clave.substr($comercio,0,9)."***";
$desobfuscated=desobfuscate($obfuscated, $des_key);

function desobfuscate($pal_sec_ofuscada,$clave_xor)
 {
    $trozos = explode (";", $pal_sec_ofuscada);
    $tope = count($trozos);
    $res="";
    for ($i=0; $i<$tope; $i++)
    {
        $x1=ord($clave_xor[$i]);
        $x2=hexdec($trozos[$i]);
        $r=$x1 ^ $x2;
        $res.=chr($r);
    }
    return($res);
 }
$desobfuscated=desobfuscate($obfuscated, $des_key);

// Calcular la firma

$order = '00'.$db->f("order_id"); //le pongo 2 "ceros" pq "order_id" son 10 dígitos en mi caso
$importe=$db->f("order_total");
$importe_formatado=$importe*100;
$datos_firma = $terminal.$comercio.$order.$importe_formatado.$moneda.$localizador.$desobfuscated;
$firma = strtoupper(sha1($datos_firma));
// echo $amount."-".$amount2."-".$firma."-".$order; --> Descomentar si desea ver si hace los cálculos.

// Montar "peción" en XML

 $lt="&lt;";
 $gt="&gt;";
        $xml.=$lt."tpv".$gt;
          $xml.=$lt."oppago".$gt;
            $xml.=$lt."idterminal".$gt.$terminal.$lt."/idterminal".$gt;
            $xml.=$lt."idcomercio".$gt.$comercio.$lt."/idcomercio".$gt;                    
            $xml.=$lt."idtransaccion".$gt.$order.$lt."/idtransaccion".$gt;
            $xml.=$lt."moneda".$gt.$moneda.$lt."/moneda".$gt;            
            $xml.=$lt."importe".$gt.$importe.$lt."/importe".$gt;                        
            $xml.=$lt."urlcomercio".$gt.$urlcomercio.$lt."/urlcomercio".$gt;                                
            $xml.=$lt."idioma".$gt.$idioma.$lt."/idioma".$gt;                        
            $xml.=$lt."pais".$gt.$pais.$lt."/pais".$gt;              
            $xml.=$lt."urlredir".$gt.$urlredir.$lt."/urlredir".$gt;                                             
            $xml.=$lt."localizador".$gt.$localizador.$lt."/localizador".$gt;                                                
            $xml.=$lt."firma".$gt.$firma.$lt."/firma".$gt;                                                            
          $xml.=$lt."/oppago".$gt;
      $xml.=$lt."/tpv".$gt;
$peticion=$xml;
?>

<!-- Enviar "petición" al TPVV --> 

<form action="<?php echo $url_tpvv ?>" method="post">
<input type="image" src="http://www.servired.es/espanol/images/logo.gif" name="submit" alt="Pagar en modo seguro" />
<input type="submit" />
<input type="hidden" name="peticion" value="<?php echo $peticion ?>"/>
</form>