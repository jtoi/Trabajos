<?php
define( '_VALID_ENTRADA', 1 );

/* 
 * Simula las operaciones bancarias para la pasarela de Pruebas
 */
$d = $_REQUEST;
date_default_timezone_set("Europe/Berlin");
$lleg = '';
foreach ($d as $key => $value) {
	$lleg .= $key." = ".$value."<br>\n";
}
//echo $lleg;

if ($d['Idioma'] == 'en' || $d['idio'] == 'en') {
	$arrMon = array("January","February","March","April","May","June","July","August","September","October","November","December");
	include_once 'lenguaje/english.php';
} else {
	$arrMon = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	include_once 'lenguaje/spanish.php';
}

if ($d['Num_operacion']) {
$ident = $d['Num_operacion'];
$impo = number_format($d['Importe']/100,2);
$mon = $d['TipoMoneda'];
($mon == '978') ? $monS = '&euro;' : $monS = '&dollar;';
($d['Idioma'] == 'en') ? $idio = 'en_GB':$idio = 'es_ES';
$ok = $d['URL_OK'];
$nk = $d['URL_NOK'];
$llg = $d['lleg'];
?>

<html>
<head>
<title><?php echo _TPV_TITULO; ?></title>
<script language="JavaScript">
var imageURL = "images/TLPV_enviarpago_en_GB.gif";

if (document.images)
{
     var enviarpago = new Image();
     enviarpago.src = "images/TLPV_enviarpago_en_GB.gif";

     var enviando = new Image();
//     enviando.src = "images/TLPV_enviando_en_GB.gif";

	 var logoComercio = new Image();
     logoComercio.src = "images/TLPV_pixtrans_exp.gif";
}

function luhnCheck(str)
{
  var result = true;

  var sum = 0;
  var mul = 1;
  var strLen = str.length;

  for (i = 0; i < strLen; i++)
  {
    var digit = str.substring(strLen-i-1,strLen-i);
    var tproduct = parseInt(digit ,10)*mul;
    if (tproduct >= 10)
      sum += (tproduct % 10) + 1;
    else
      sum += tproduct;
    if (mul == 1)
      mul++;
    else
      mul--;
  }
  if ((sum % 10) != 0)
    result = false;

  return result;
}

//LNA 21/05/2004: Funcion para validar el tamaño minimo y maximo que debe tener una tarjeta,
//debe estar entre 13 y 19
function isSizeCardOk(card){

	if ((card.length <= 19) && (card.length >= 13)){
		return true;
	}else{
		return false;
	}
}

//EAG 15/12/2004: Funcion para validar el tamaño minimo y maximo que debe tener el CVV2/CVC2/CID,
//debe ser 3 para Visa/MasterCard y 4 para AMEX
function isSizeCVV2Ok(card){
	var esAmex = document.forms[0].numtarjeta.value.substring(0,2)=="37";
	if (!esAmex && card.length == 3){
		return true;
	}
	if (esAmex && card.length == 4){
		return true;
	}
	return false;
}

// ----------------------- aceptar() ---------------------------------------
function aceptar(){


    //se comprueba que los campos vienen correctamente rellenados

  var checkOK = "1234567890";
  var allValid = true;

  quitar_espacios2()


  if(document.forms[0].numtarjeta.value == "")
  {
       alert("You must provide a valid card number");
	   allValid = false;
	   return;
  }

  //LNA 21/05/2004: Se añade la validación del tamaño de la tarjeta

  if (!isSizeCardOk(document.forms[0].numtarjeta.value))
  {
  	alert("You must provide a valid card number");

  	allValid = false;
  	return;
  }


  //EAG 14/12/2004: Se añade la validación del tamaño del CVV2/CVC2/CID
  if (document.forms[0].cvv2 != 'undefined' && !isSizeCVV2Ok(document.forms[0].cvv2.value))
  {
  	alert("You must provide a valid CVV2/CVC2 number");

  	allValid = false;
  	return;
  }

  

  if (!luhnCheck(document.forms[0].numtarjeta.value))
  {
  	alert("You must provide a valid card number");
  	allValid = false;
  	return;
  }

  fecha = new Date();
  if ((document.forms[0].aniocad.value < fecha.getFullYear()) || (document.forms[0].mescad.value < 1) ) {
    allValid = false;
    alert('Card Expiration Date incorrect');
    return;
  }
  if (document.forms[0].aniocad.value > fecha.getFullYear()) {
    allValid = true;
  } else {
    if ((document.forms[0].aniocad.value == fecha.getFullYear()) && (document.forms[0].mescad.value*1 <= fecha.getMonth())) {
      allValid = false;
      alert('Card Expiration Date incorrect');
      return;
    }
  }



  if (allValid){
//	document.form1.accion.value="HACER_PAGO_PAGO3D";
	srcImage = document.enviar.src;
	document.form1.submit();
  }
}
// abrir ventana CVV2
function abrircvv2()
{
	var pag = "TLPV_pub_cvv2-cvc2.jsp?pais="+'GB'+"&idioma="+'en';
	window.open(pag,'miwin','width=450,height=350,top=100,left=210');
}

function abrir_info_3dSecure()
{
	var pag = "TLPV_pub_info_3dSecure.jsp?pais="+'GB'+"&idioma="+'en';
	window.open(pag,'miwin','width=470,height=570,top=80,left=210');
}


// Pago por referencia
function mobipay()
{
	f = document.form1
	f.accion.value = "HACER_PAGO_PAGOREF"
	f.submit()
}

function quitar_espacios(e)
{
	var keynum
	var keychar
	var numcheck
	if(window.event) // IE
	{
		keynum = e.keyCode
	}
	else if(e.which) // Netscape/Firefox/Opera
	{
		keynum = e.which
	}
	keychar = String.fromCharCode(keynum)
	numcheck = /\d/
	return numcheck.test(keychar)
}


function quitar_espacios2()
{
	var aux_str = document.forms[0].numtarjeta.value;
	var iaux = aux_str.indexOf(" ");
	
	while (iaux!=-1)
	{
		aux_str = aux_str.substring(0,iaux) + aux_str.substring(iaux+1,aux_str.length);
		iaux = aux_str.indexOf(" ");
	} 
	document.forms[0].numtarjeta.value = aux_str;
}

function mano()
{
	document.all("enviar").style.cursor = 'hand';
}
function abrirSeguro()
{
// 	var pag = "TLPV_pub_infosegBBVA.jsp?pais="+'GB'+"&idioma="+'en';
// 	window.open(pag,'miwin','width=390,height=400,top=100,left=210,scrollbars=yes');
}
// Para ocultar el codigo fuente. Deshabilitamos el boton derecho
function right(e) {
        if (navigator.appName == 'Netscape' &&  (e.which == 3 || e.which == 2))
                return false;
        else
                if (navigator.appName == 'Microsoft Internet Explorer' &&
                        (event.button == 2 || event.button == 3)) {
                        alert("Action not allowed");
                        return false;
                }

        return true;
}

document.onmousedown=right;
document.onmouseup=right;
if (document.layers) window.captureEvents(Event.MOUSEDOWN);
if (document.layers) window.captureEvents(Event.MOUSEUP);
window.onmousedown=right;
window.onmouseup=right;

</script>
<link rel="stylesheet" href="css/TLPV_estile_B9550206800002.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<form name=form1 method="post" action="simBanco.php">
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr valign="bottom">
      <td width="50%" nowrap>
        <!-- Logo TPV - BBVA opcional -->
		<img src="images/TLPV_pixtrans_exp.gif" width="318" height="48" vspace="0" hspace="0" border="0">
        <img src="images/TLPV_enpruebas_en_GB.gif" width="175" height="48" vspace="0" hspace="0" border="0">
      </td>
      <td align="right">
        <img name=logoComercio src="images/TLPV_pixtrans_exp.gif" vspace="0" hspace="0" border="0">
      </td>
    </tr>
    <tr align="right">
      <td colspan="2" class="l3p"> <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="99%" valign="bottom"><img src="images/TLPV_pixtrans_exp.gif" width="1" height="1"></td>
            <td width="1%" align="right" nowrap class="l3p">&nbsp;&nbsp;<?php echo $arrMon[date('n')-1]." ".date('j, Y - H:i'); ?></td>
          </tr>
        </table></td>
    </tr>
    <tr align="center">
      <td colspan="2" class="peqa"><table width="90%"  border="0" cellpadding="1" cellspacing="0" class="l3">
          <tr>
            <td><table width="100%"  border="0" cellpadding="2" cellspacing="0" class="l2">
                <tr>
                  <td colspan="3" class="fh">
                   &nbsp;&nbsp;<img src="images/TLPV_flech.gif" width="9" height="7" vspace="0" hspace="0">
                   <b>&nbsp;<?php echo _TPV_FORM; ?>:</b>
                  </td>
                </tr>
                <tr>
                  <td class="l2" nowrap>&nbsp; &nbsp;&nbsp;<?php echo _TPV_TRANSNUM; ?>:</td>
                  <td class="l2" colspan="2" >&nbsp; &nbsp;<?php echo $ident; ?>
                  </td>
                </tr>
                <tr>
                  <td height="22" class="l2">&nbsp; &nbsp;&nbsp;<?php echo _TPV_AMOUNT; ?>: </td>
                  <td class="l2" colspan="2">&nbsp; &nbsp;<?php echo $impo." ".$monS; ?>
                  </td>
                </tr>
              </table></td>
          </tr>
        </table>

    <tr align="center">
      <td colspan="2" class="peqa"><table width="90%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="100%" height="174" border="0" align="left" cellpadding="1" cellspacing="0" class="l3">
                <tr>
                  <td valign="top"> <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="1" class="l2">
                      <tr>
                        <td width="50%" valign="top"> <table width="100%"  border="0" cellpadding="3" cellspacing="0" class="l2">
                            <tr>
                              <td colspan="2" class="fh"><b>&nbsp;&nbsp;<img src="images/TLPV_flech.gif" width="9" height="7" vspace="0" hspace="0">
                               &nbsp;<?php echo _TPV_DATENTR; ?>:</b>
                              </td>
                            </tr>
                            <tr>
                              <td width="40%" nowrap  class="l2">&nbsp; &nbsp;&nbsp;<?php echo _TPV_NUMTARJETA; ?>:</td>
                              <td width="60%" nowrap class="l2" > <input name="numtarjeta" type="text" class="Formulario"  value="" size="25" maxlength="19" onChange="quitar_espacios2()" onKeypress="return quitar_espacios(event)" autocomplete="OFF">
                              </td>
                            </tr>
                            <tr>
                              <td height="28" nowrap class="l2">&nbsp; &nbsp;&nbsp;<?php echo _TPV_FECHA; ?>: &nbsp;</td>
                              <td class="l2" nowrap> <?php echo _TPV_MES; ?>:
                                <select name="mescad" class="Formulario">
								<option value="  " selected >  </option>
<?php
$arrMs = array("01","02","03","04","05","06","07","08","09","10","11","12");
foreach ($arrMs as $ms) {
?>
                                 <option value="<?php echo $ms;?>" ><?php echo $ms;?></option>
<?php } ?>
                                </select>

								&nbsp;&nbsp; <?php echo _TPV_ANO; ?>:
								<select name="aniocad" class="Formulario">
								<option value="  " selected >  </option>
<?php
$ann = date('y');
for ($i = $ann; $i<($ann + 51); $i++){
?>
								<option value="20<?php echo $i;?>" ><?php echo $i;?></option>
<?php } ?>
                                </select>


                              </td>
                            </tr>
                            
                            
							<tr align="center">
								
									  <td width="40%" align="left" nowrap class="l2">&nbsp;&nbsp;&nbsp;CVV2/CVC2: </td>
									  <td width="60%" align="left" nowrap class="l2"><input name="cvv2" type="text" class="Formulario"  value="" size="5" maxlength="4" autocomplete="OFF" S>&nbsp;&nbsp;</td>
									
                            </tr>
                            <tr align="center">
							  <td nowrap class="l2" >&nbsp;</td>
                              <td nowrap align="left" class="l2">
							   <!-- Espacio para enviar datos -->
								<img name="enviar" src="images/TLPV_enviarpago_en_GB.gif" onclick="aceptar()" onmouseover="mano()"  width="115" height="18" border="0">
				  <!-- fin Espacio para enviar datos --></td>
                            </tr>
                            <tr>
                              <td colspan="2" valign="top" nowrap class="l2">
                                <table width="1%"  border="0" align="center" cellpadding="0" cellspacing="0">
                                  <tr>

                                    <td>
									  <a href="javascript:abrir_info_3dSecure();">
										 <img src="images/TLPV_verifiedTranspa.gif" width="89" height="51" border="0">
									  </a>
								    </td>
									 <td>
									  <a href="javascript:abrir_info_3dSecure();">
										 <img src="images/TLPV_masterseTranspa.gif" width="89" height="51" border="0">
									  </a>
								    </td>


								   <td><img src="images/TLPV_pixtrans_exp.gif" width="1" height="47"></td>


								   <td><img src="images/TLPV_pixtrans_exp.gif" width="1" height="44"></td>


                                  </tr>
							    </table>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="3" nowrap class="l2"> </td>
                            </tr>
                          </table></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
         </table>
         </td>
         </tr>
          <tr>
		   <td colspan="2">

             &nbsp;

		   </td>
          </tr>
  </table>

  <br>
<input type="hidden" name="ident" value="<?php echo $ident; ?>">
<input type="hidden" name="impo" value="<?php echo $impo; ?>">
<input type="hidden" name="idio" value="<?php echo $idio; ?>">
<input type="hidden" name="ok" value="<?php echo $ok; ?>">
<input type="hidden" name="nk" value="<?php echo $nk; ?>">
<input type="hidden" name="llg" value="<?php echo $llg; ?>">
<input type="hidden" name="monS" value="<?php echo $monS ?>">
<input type="hidden" name="pas" value="1">

</script>
</form>
</body>
</html>
<?php 
} else if($d['pas'] == 1) {
	$ident = $d['ident'];
	$impo = $d['impo'];
	$idio = $d['idio'];
	$ok = $d['ok'];
	$nk = $d['nk'];
	$llg = $d['llg'];
?>
<html><head>
<title><?php echo _TPV_TITULO; ?></title>
<link rel="stylesheet" href="css/TLPV_estile_int.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<script languaje="JavaScript">
function validar() {
 if(document.clave3dsecure.textfield.value == '123456') {
 	document.clave3dsecure.submit();	
 } else {
 	alert("Card number is not valid");
 }
}
</script>
</head>
<body leftmargin="0" topmargin="0" scroll="auto" marginheight="0" marginwidth="0">
<form name="clave3dsecure" action="simBanco.php" method="post">
  <table width="100%" align="center" border="0" cellpadding="0" cellspacing="1">
    <tbody><tr> 
      <td colspan="2" height="52" nowrap="nowrap"><img src="images/TLPV_pixtrans_int.gif" vspace="0" width="318" height="48" hspace="0" border="0"></td>
    </tr>
    <tr align="right"> 
        
        <td colspan="2" class="l2" height="22" nowrap="nowrap"> <?php echo $arrMon[date('n')-1]." ".date('j, Y - H:i'); ?>
      </td>
    </tr>
    <tr align="center"> 
      <td colspan="2" class="boldaot" nowrap="nowrap"><?php echo _TPV_EMULADOR; ?><br>
      </td>
    </tr>
    <tr> 
      <td width="4%" nowrap="nowrap"><img src="images/TLPV_cuadrado_int.gif" width="10" height="11">&nbsp;</td>
      <td class="boldaot" width="96%"><?php echo _TPV_CONTRS; ?></td>
    </tr>
    <tr> 
      <td valign="top"><img src="images/TLPV_pixtrans_int.gif" vspace="0" width="1" height="1" hspace="0" border="0"></td>
      <td valign="top" bgcolor="#000066"><img src="images/TLPV_pixtrans_int.gif" vspace="0" width="150" height="1" hspace="0" border="0"></td>
    </tr>
    <tr align="center"> 
      <td colspan="2" valign="top"><br> <table class="fh" width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
          <tbody><tr align="center"> 
            <td class="fh"> <table width="100%" border="0" cellpadding="0" cellspacing="1">
                <tbody><tr> 
                  <td class="fh" nowrap="nowrap"> <table width="100%" border="0" cellpadding="1" cellspacing="3">
                      <tbody><tr> 
                        <td colspan="2" class="fh" align="center"><br> <br>
                          <?php echo _TPV_USRPASS; ?>:&nbsp; <input name="textfield" class="formulario" type="password"> 
                          <br> <br> </td>
                      </tr>
                    </tbody></table></td>
                </tr>
              </tbody></table></td>
          </tr>
        </tbody></table>
        <br> <table width="66" height="17" border="0" cellpadding="0" cellspacing="0">
          <tbody><tr> 
            <td width="66" height="17" align="center" nowrap="nowrap" background="images/TLPV_b_ac_int.gif"><img src="images/TLPV_pixtrans_int.gif" width="66" height="1"><br> 
              <a href="#" class="lnkazul3" onclick="validar()">Ok</a></td>
          </tr>
        </tbody></table></td>
    </tr>
  </tbody></table>
  <input type="hidden" name="ident" value="<?php echo $ident; ?>">
<input type="hidden" name="impo" value="<?php echo $impo; ?>">
<input type="hidden" name="idio" value="<?php echo $idio; ?>">
<input type="hidden" name="ok" value="<?php echo $ok; ?>">
<input type="hidden" name="nk" value="<?php echo $nk; ?>">
<input type="hidden" name="llg" value="<?php echo $llg; ?>">
<input type="hidden" name="numtarjeta" value="<?php echo $d['numtarjeta']; ?>">
<input type="hidden" name="mescad" value="<?php echo $d['mescad']; ?>">
<input type="hidden" name="aniocad" value="<?php echo $d['aniocad']; ?>">
<input type="hidden" name="cvv2" value="<?php echo $d['cvv2']; ?>">
<input type="hidden" name="monS" value="<?php echo $d['monS'] ?>">
<input type="hidden" name="pas" value="2">
</form>
</body></html>
<?php 
} else if($d['pas'] == 2) {
	$ident = $d['ident'];
	$impo = '0';
	$idio = $d['idio'];
	$ok = $d['ok'];
	$nk = $d['nk'];
	$llg = $d['llg'];
	$monS = $d['monS'];
	$tarj = substr($d['numtarjeta'], 0, 4).'*****'.substr($d['numtarjeta'], 9);
	$autor = '';
	if ($d['numtarjeta'] == '4940190000370787') {
		$autor = rand(000000, 999999);
		while(strlen($autor) < 6){
			$autor = '0'.$autor;
		}
		$impo = $d['impo'];
		$cartel = _TPV_ACEPTADO;
		$sale = $ok;
		$result = 0;
		$deserror = '';
		$coderror = '';
	} else {
		$sale = $nk;
		$result = 1;
		if($d['numtarjeta'] == '4548812908888885') {
			$deserror = $cartel = _TPV_DENEGADO;
			$coderror = '001';
		} else {
			$deserror = $cartel = _TPV_AJENA;
			$coderror = '003';
		}
	}
	
	$data = array(
            "result"=>$result,
            "pszPurchorderNum"=>$ident,
            "pszTxnDate"=>date('d/m/Y H:i:s'),
            "pszApprovalCode"=>$autor,
            "coderror"=>$coderror,
            "deserror"=>$deserror
        );
        
	foreach ($data as $key => $value) {
		$correoMi .=  "<br>\n$key = $value";
	}

	$correoMi .=  $llg."<br>\n";
	$salidaCurl = '';$i = 1;
	$ch = curl_init($llg);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_exec($ch);
	$curl_info = curl_getinfo($ch);
	curl_close($ch);
//	print_r($curl_info);
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=windows-1252">
<title><?php echo _TPV_TITULO; ?></title>
<!-- Comentario -->
<link rel="stylesheet" href="css/TLPV_estile_int.css" type="text/css">
<script>
function aceptar(){
  window.close() 
}  
</script>
</head>


<body scroll="auto">

<form method="post">
  <table width="100%" align="center" border="0" cellpadding="0" cellspacing="1">
      <tbody><tr>
      <td colspan="2" height="52" nowrap="nowrap"><img src="images/TLPV_pixtrans_int.gif" vspace="0" width="318" height="48" hspace="0" border="0"></td>
      </tr>
      <tr align="right">
        
        <td colspan="2" class="l2" height="22" nowrap="nowrap"> <?php echo $arrMon[date('n')-1]." ".date('j, Y - H:i'); ?>
        </td>
      </tr>
      <tr align="center">
      <td colspan="2" class="boldaot" nowrap="nowrap"><?php echo _TPV_SISEMUL; ?><br>
        </td>
      </tr>
      <tr>
        <td width="4%" nowrap="nowrap"><img src="images/TLPV_cuadrado_int.gif" width="10" height="11">&nbsp;</td>
      <td class="boldaot" width="96%"><?php echo _TPV_PAYRES; ?></td>
      </tr>
      <tr>
        <td valign="top"><img src="images/TLPV_pixtrans_int.gif" vspace="0" width="1" height="1" hspace="0" border="0"></td>
        <td valign="top" bgcolor="#000066"><img src="images/TLPV_pixtrans_int.gif" vspace="0" width="150" height="1" hspace="0" border="0"></td>
      </tr>
      <tr>
        <td colspan="2" valign="top"><br> <table class="fh" width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
            <tbody><tr align="center">
              <td class="fh"> <table width="100%" border="0" cellpadding="0" cellspacing="1">
                  <tbody><tr>
                    <td class="fh" nowrap="nowrap"> <table width="100%" border="0" cellpadding="1" cellspacing="3">
                      <tbody><tr>
					  
                        <td class="fh" width="35%">&nbsp; &nbsp;&nbsp; <?php echo _TPV_AMOUNT; ?>
                          : </td>
                        <td class="fh" width="65%" height="27"><?php echo number_format($impo,2)." ".$monS; ?> </td>
                      </tr>
                      <tr>
                        <td class="fh">&nbsp; &nbsp;&nbsp; <?php echo _TPV_TRANSNUM; ?>
                          : </td>
                        <td class="fh" height="27"><?php echo $ident; ?></td>
                      </tr>
                      <tr>
                        <td class="fh">&nbsp; &nbsp;&nbsp; <?php echo _TPV_NUMTARJETA; ?>
                          : </td>
					  
                        <td class="fh" height="27"><?php echo $tarj; ?></td>
                      </tr>
					  
                      <tr>
                        <td class="fh">&nbsp; &nbsp;&nbsp; <?php echo _TPV_AUTOR; ?>
                          : </td>
                        <td class="fh" height="27"><?php echo $autor; ?></td>
                      </tr>
                      <tr>
                        <td colspan="2" class="boldaot" height="27">&nbsp; &nbsp;&nbsp;
                          <?php echo $cartel; ?></td>
                      </tr>
					  
                    </tbody></table></td>
                  </tr>
                </tbody></table></td>
            </tr>
          </tbody></table>
        <br>
        <table width="250" align="center" border="0" cellpadding="0" cellspacing="0">
          <tbody><tr>
            <td height="17" align="center" nowrap="nowrap"> <table width="100" height="17" border="0" cellpadding="0" cellspacing="0">
                <tbody><tr>
                  <td width="100" height="17" align="center" nowrap="nowrap" background="images/TLPV_b_c_int.gif"><img src="images/TLPV_pixtrans_int.gif" width="75" height="1"><br>
                    <a href="javascript:print();" class="lnkazul3"><?php echo _TPV_PRINT; ?></a></td>
                </tr>
              </tbody></table></td>
            <td align="center" nowrap="nowrap"><table width="100" height="17" border="0" cellpadding="0" cellspacing="0">
                <tbody><tr>
                  <td width="100" height="17" align="center" nowrap="nowrap" background="images/TLPV_b_c_int.gif"><img src="images/TLPV_pixtrans_int.gif" width="75" height="1"><br>
                    <a href="<?php echo $sale; ?>" class="lnkazul3">Ok</a></td>
                </tr>
              </tbody></table></td>
          </tr>
        </tbody></table></td>
      </tr>
    </tbody></table>
</form>
</body></html>
<?php
}
?>
