<?php

defined ( '_VALID_ENTRADA' ) or die ( 'Restricted access' );

$html = new tablaHTML ();
$temp = new ps_DB ();

//verifico que el token tenga menos de 24hrs
$q = "select fecha from tbl_setup where nombre = 'tiToken'";
$temp->query($q);
if (time()-(24*60*60) > $temp->f('fecha')) {
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
			CURLOPT_URL				=> 'https://195.57.91.186:8555/APITest/Token',
			CURLOPT_POSTFIELDS		=> "grant_type=password&username=".urlencode('info@amfglobalitems.com')."&password=amfpass1234",
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
	// 						echo "error=".curl_errno($ch);
	if (curl_errno($ch)) $correoMi .=  "Error en la resp de Titanes:".curl_strerror(curl_errno($ch))."<br>\n";
	$crlerror = curl_error($ch);
	// 						echo "otroerror=".$crlerror;
	if ($crlerror) {
		$correoMi .=  "Error en la resp de Titanes:".$crlerror."<br>\n";
	}
	$curl_info = curl_getinfo($ch);
	curl_close($ch);
	//			echo "<br><br>salida=".$salida."<br><br>";
	$arrCurl = json_decode($salida);
	
// 	print_r($arrCurl);
	actSetup($arrCurl->access_token, 'tiToken');
	
	$access_token = $arrCurl->access_token;
} else $access_token = leeSetup('tiToken');


$html->idio = $_SESSION ['idioma'];
$html->tituloPag = "Subida de Ficheros a Titanes";
$html->tituloTarea = '';
$html->anchoTabla = 1150;
$html->tabed = true;
$html->anchoCeldaI = 300;
$html->anchoCeldaD = 340;
$html->inHide ( 1, 'inserta' );
$html->inHide ( '', 'dir' );
$html->inHide ( '', 'ficheros' );

$html->inTextoL ( 'asdfasdfasd', 'salida' );
header('Access-Control-Allow-Origin: *'); 
echo $html->salida ('<input class="formul" id="enviasdForm" name="enviar" onclick="salva()" type="button" value="' . _FORM_SEND . '" />');
?>
<script type="text/javascript">
$(document).ready(function(){
	var tokenKey = '';
	leedir('');
});

function salva() {
	var dir = $("#dir").val();
	var fic = $("#ficheros").val();
	if (fic.length > 0) {
		$("#salida").html('<img src="../images/circulo.gif" />')
		$.post('componente/core/ejec.php',{
			fun:'ftpsend',
			dir:dir,
			fic:fic
		},function(data){
			var datos = eval('(' + data + ')');
			var cadena = '';
			var max = 5;
			var j=0;
			if (max>datos.cont.length) max = datos.cont.length;
			var tablaenc = '<table width="100%" >';
			$("#salida").html('');
			for(i=0;i<datos.cont.length;i++) {
	// 			alert(datos.cont.length);
				var desd = datos.cont[i].indexOf('/..');
				if (datos.cont[i].indexOf('/..') > 1) {
					cadena = '<tr><td colspan="'+max+'">.'+datos.cont[i]+'</td>';
				} else {
					if (j==0) cadena = cadena + '</tr><tr>'
					cadena = cadena + '<td>'+datos.cont[i]+'</td>';
					(j+1==max)?j=0:j++;
				}
			}
			while (j<max){
				cadena = cadena+'<td></td>';
				j++;
			}
			//$("#salida").append(tablaenc+cadena+'</tr></table>');
		});
	} else alert("Debe seleccionar al menos un documento");
}

function enviafichero(ime) {
	$("#"+ime).toggleClass("selectt");
	if ($("#ficheros").val().indexOf(ime)>-1) {
		if ($("#ficheros").val().indexOf(';')>-1) {
			if ($("#ficheros").val().indexOf(';'+ime)>-1) 
				$("#ficheros").val($("#ficheros").val().replace(";"+ime,""));
			else $("#ficheros").val($("#ficheros").val().replace(ime+";",""));
		} else $("#ficheros").val($("#ficheros").val().replace(ime,""));
	} else {
		if ($("#ficheros").val().length > 0) $("#ficheros").val($("#ficheros").val()+";"+ime);
		else $("#ficheros").val(ime);
	}
}

function leedir(dir) {
	$("#salida").html('<img src="../images/circulo.gif" />')
	if (dir.length > 0) $("#dir").val(dir);
	$.post('componente/core/ejec.php',{
		fun:'ftpver',
		dir:dir
	},function(data){
		var datos = eval('(' + data + ')');
		var cadena = '';
		var max = 5;
		var j=0;
		if (max>datos.cont.length) max = datos.cont.length;
		var tablaenc = '<table width="100%" >';
		$("#salida").html('');
		for(i=0;i<datos.cont.length;i++) {
// 			alert(datos.cont.length);
			var desd = datos.cont[i].indexOf('/..');
			if (datos.cont[i].indexOf('/..') > 1) {
				cadena = '<tr><td colspan="'+max+'">.'+datos.cont[i]+'</td>';
			} else {
				if (j==0) cadena = cadena + '</tr><tr>'
				cadena = cadena + '<td>'+datos.cont[i]+'</td>';
				(j+1==max)?j=0:j++;
			}
		}
		while (j<max){
			cadena = cadena+'<td></td>';
			j++;
		}
		$("#salida").append(tablaenc+cadena+'</tr></table>');
	});
}
</script>
<style>
.selectt{border:4px solid #2aff00;}
.dirRec {
	color: blue;
	cursor: pointer;
}
</style>

