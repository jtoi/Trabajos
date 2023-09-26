<?php
ini_set('display_errors', 0);
//error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('_VALID_ENTRADA', 1);
if (!session_start()) session_start();
require_once( 'admin/classes/entrada.php' );
require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/correo.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'admin/adminis.func.php' );
require_once( 'include/class.inscTitanes.php' );

$temp = new ps_DB;
$correo = new correo;
$ent = new entrada;
$tit = new insTit();


/*********************************************/
if (stripos(_ESTA_URL, 'localhost') > 0) {
// CLIENTE
//$_REQUEST['Id']						= '1529499887';
//$_REQUEST['Nombre']					= 'nomb1529499887';
//$_REQUEST['Apellido1']				= 'ape1529499887';
//$_REQUEST['Apellido2']				= '';
//$_REQUEST['DocumentNumber']			= '';
//$_REQUEST['DocumentExpirationDate'] = '03/02/22';
//$_REQUEST['Email']					= '1529499887@yahoo.es';
//$_REQUEST['PhoneNumber']			= '';
//$_REQUEST['Country']				= 'mm';
//$_REQUEST['Province']				= 'Yangon';
//$_REQUEST['City']					= 'Naipyido';
//$_REQUEST['Address']				= '';
//$_REQUEST['PostalCode']				= '';
//$_REQUEST['CountryOfBirth']			= 'cu';
//$_REQUEST['DateOfBirth']			= '09/12/86';
//$_REQUEST['Gender']					= '1';
//$_REQUEST['Profesion']				= 'Teacher';
//$_REQUEST['MonthSalary']			= '10000';
//$_REQUEST['UsuarioCode']			= '';

// $q = "select (idcimex+1) id from tbl_aisCliente order by (idcimex*1) desc limit 0,1";
// $temp->query($q);  
// $_REQUEST['Id'] = $temp->f('id');
//$_REQUEST['Nombre']					= substr(strtolower(preg_replace('/[0-9_\/]+/','',base64_encode(sha1(suggestPassword(8,true))))),0,8);
//$_REQUEST['Apellido1']				= str_rot13($_REQUEST['Nombre']);
//$_REQUEST['Apellido2']				= '';
//$_REQUEST['DocumentNumber']			= date('ymdHis');
//$_REQUEST['DocumentExpirationDate'] = '03/02/22';
//$_REQUEST['Email']					= $_REQUEST['Nombre'].'@yahoo.es';
//$_REQUEST['PhoneNumber']			= suggestPassword(8,false);
//$_REQUEST['Country']				= 'mm';
//$_REQUEST['Province']				= 'Yangon';
//$_REQUEST['City']					= 'Naipyido';
//$_REQUEST['Address']				= substr(strtolower(preg_replace('/[0-9_\/]+/','',base64_encode(sha1(suggestPassword(8,true))))),0,15);
//$_REQUEST['PostalCode']				= suggestPassword(5,false);
//$_REQUEST['CountryOfBirth']			= 'cu';
//$_REQUEST['DateOfBirth']			= '09/12/86';
//$_REQUEST['Gender']					= '1';
//$_REQUEST['Profesion']				= 'Teacher';
//$_REQUEST['MonthSalary']			= '10000';
//$_REQUEST['UsuarioCode']			= date('ymdHi');

//$temp->query("delete from tbl_aisCliente where idcimex = ".$_REQUEST['Id']);

// $salidaCurl = 'xml version="1.0" encoding="UTF-8"
// <response><Ds_Message>Error de proceso</Ds_Message><Ds_Merchant_ClientId>453452f</Ds_Merchant_ClientId><Ds_Bank>0
// </Ds_Bank><Ds_Date>160113170406</Ds_Date><Ds_AuthorisationCode>0</Ds_AuthorisationCode><Ds_PanMask>0</Ds_PanMask><Ds_Merchant_TransactionType>0
// </Ds_Merchant_TransactionType><Ds_Amount>0</Ds_Amount><Ds_Code>207</Ds_Code><Ds_Merchant_Guarantees>0</Ds_Merchant_Guarantees>
// <Ds_Merchant_MerchantCode>V99008980</Ds_Merchant_MerchantCode><Ds_Signature>5f8248588cc3d240fb9af902096805444319dfcb</Ds_Signature></response>';

// BENEFICIARIO
//$_REQUEST['Id']					= '21957';
//$_REQUEST['IdCliente']			= '1000';
//$_REQUEST['Nombre']				= 'Rigoberto';
//$_REQUEST['Apellido1']			= 'Fresquet';
//$_REQUEST['Apellido2']			= 'Pedroso';
//$_REQUEST['Phone']				= '5377941393';
//$_REQUEST['Phone']				= '';
//$_REQUEST['Address']			= 'Coyula No 10 int.Entre Oscar Lunar y Nico Lopez Regla';
//$_REQUEST['City']				= 'Regla';
//$_REQUEST['CI']					= '60041113488';
//$_REQUEST['Relation']			= '13';
//$_REQUEST['Reason']				= '3';


//$q = "select (idcimex+1) id from tbl_aisBeneficiario order by (idcimex*1) desc limit 0,1";
//$temp->query($q); 
//$_REQUEST['Id'] = $temp->f('id');
//$_REQUEST['IdCliente']			= '4092';
//$_REQUEST['Nombre']				= substr(strtolower(preg_replace('/[0-9_\/]+/','',base64_encode(sha1(suggestPassword(8,true))))),0,8);
//$_REQUEST['Apellido1']			= str_rot13($_REQUEST['Nombre']);
//$_REQUEST['Apellido2']			= '';
//$_REQUEST['Phone']				= suggestPassword(8,false);
//$_REQUEST['Address']			= substr(strtolower(preg_replace('/[0-9_\/]+/','',base64_encode(sha1(suggestPassword(8,true))))),0,15);
//$_REQUEST['City']				= 'Regla';
//$_REQUEST['CI']					= substr(date('ymdHis'),0,11);
//$_REQUEST['Relation']			= '13';
//$_REQUEST['Reason']				= '3';
	
//$q = "select idcimex, nombre, papellido, sapellido, telf, direccion, ciudad, numDocumento from tbl_aisBeneficiario where idcimex = 130751";
//$temp->query($q);
//$_REQUEST['Id']					= $temp->f('idcimex');
//$_REQUEST['IdCliente']			= '4091';
//$_REQUEST['Nombre']				= $temp->f('nombre');
//$_REQUEST['Apellido1']			= $temp->f('papellido');
//$_REQUEST['Apellido2']			= $temp->f('sapellido');
//$_REQUEST['Phone']				= $temp->f('telf');
//$_REQUEST['Address']			= $temp->f('direccion');
//$_REQUEST['City']				= $temp->f('ciudad');
//$_REQUEST['CI']					= $temp->f('numDocumento');
//$_REQUEST['Relation']			= '13';
//$_REQUEST['Reason']				= '3';


// $salidaCurl = 'xml version="1.0" encoding="UTF-8"
// <response><Ds_Message>Error de proceso</Ds_Message><Ds_Merchant_BeneficiaryId>34f23</Ds_Merchant_BeneficiaryId><Ds_Bank>0</Ds_Bank>
// <Ds_Date>160113170406</Ds_Date><Ds_AuthorisationCode>0</Ds_AuthorisationCode><Ds_PanMask>0</Ds_PanMask><Ds_Merchant_TransactionType>0
// </Ds_Merchant_TransactionType><Ds_Amount>0</Ds_Amount><Ds_Code>207</Ds_Code><Ds_Merchant_Guarantees>0</Ds_Merchant_Guarantees>
// <Ds_Merchant_MerchantCode>V99008980</Ds_Merchant_MerchantCode><Ds_Signature>5f8248588cc3d240fb9af902096805444319dfcb</Ds_Signature></response>';
	
	
//	$idcom = 'V99008980';
	
//	if ($_REQUEST['insert'] == 1){
//		$q = "delete from tbl_aisCliente where idcimex = ".$_REQUEST['Id'];
//		$temp->query($q);
//		$q = "delete from tbl_aisBeneficiario where idcimex = ".$_REQUEST['Id'];
//		$temp->query($q);
//		error_log("*********** borra Cliente o beneficiario ******************");
//	}
}

/*********************************************/

error_log("------------------------------------------------------------------------------------------");
error_log("------------------------------------------------------------------------------------------");

$d=$_REQUEST;
global $correoMi;
$correoMi = $sale = $salidaCurl = '';
$cliente = false;
$pasarC = 91; //pasarela de la nueva integración

$correoMi .= "DIR IP - ". $_SERVER['REMOTE_ADDR'] . "<br>\n";
$correoMi .= "Pasarela usada". $pasarC . "<br>\n";
foreach ($d as $value => $item) {
	if (mb_detect_encoding($item) == 'UTF-8') $d[$value] = mb_convert_encoding($item, "UTF-8");
	else $d[$value] = utf8_encode($item);
	$correoMi .= $value . "=" . $item . ", enc=".mb_detect_encoding($item)."<br>\n";
}

//echo $correoMi; exit;

//$correo->todo(48, 'Insertando usuarios en Ais datos de entrada', $correoMi);

if (!isset($d['Id'])) muestraError ("No se envian datos", $correoMi);
//datos comunes
if (!($id = $ent->isReal($d['Id'], 12))) muestraError ("falla por Id", $correoMi);
if (!($nombre = ucwords(strtolower($ent->isAlfabeto($d['Nombre'], 50))))) muestraError ("falla por Nombre", $correoMi);
if (!($ape1 = ucwords(strtolower($ent->isAlfabeto($d['Apellido1'],50))))) muestraError ("falla por Apellido1", $correoMi);
//if (!($ape2 = ucwords(strtolower($d['Apellido2'])))) muestraError ("falla por Apellido2", $correoMi);
$d['Address'] = substr(str_replace("\"", "/", $d['Address']), 0,69);
$correoMi .= $d['Address']."<br>";
if (!($dir = substr($ent->isDeHtml($d['Address'], 100), 0, 69))) muestraError ("falla por Address", $correoMi);
$ciudad = utf8_decode($d['City']);
$nombre = utf8_decode($nombre);
$ape1 = utf8_decode($ape1);
$ape2 = utf8_decode($ape2);
$dir = substr(utf8_decode($dir),0,68);


if (!isset($d['IdCliente'])) {//Es un Cliente
	$correoMi .= "Cliente\n<br>";
	if (!($telf = $ent->isAlfanumerico($d['PhoneNumber'], 15))) muestraError ("falla por Phone", $correoMi);
	if (!($numid = $ent->isAlfanumerico($d['DocumentNumber'], 21))) muestraError ("falla por DocumentNumber", $correoMi);
	if (!($fechaDoc = to_unix($ent->isDate($d['DocumentExpirationDate'])))) muestraError ("falla por DocumentExpirationDate", $correoMi);
	if ($fechaDoc < (time()+60*60*24*2)) muestraError ("falla por fecha errónea en DocumentExpirationDate", $correoMi);
	if (!($email = strtolower($ent->isCorreo($d['Email'], 60)))) muestraError ("falla por Email", $correoMi);
	if (!($pais = strtoupper($ent->isAlfabeto($d['Country'], 2)))) muestraError ("falla por Country", $correoMi);
// 	if (!($prov = $ent->isAlfanumerico($d['Province'], 50))) muestraError ("falla por Province", $correoMi);
// 	$po = $d['PostalCode'];
	if ($d['PostalCode'] == '0') $po = '0';
	else if (stripos($d['PostalCode'], '--') > -1) $po = '0';
	else if (!($po = $ent->isAlfanumerico($d['PostalCode'], 11))) muestraError ("falla por PostalCode", $correoMi);
	if (!($paisNac = strtoupper($ent->isAlfabeto($d['CountryOfBirth'], 2)))) muestraError ("falla por CountryOfBirth", $correoMi);
	$arrFec = explode("/", $d['DateOfBirth']);
	if (!is_array($arrFec)) muestraError ("falla por DateOfBirth", $correoMi);
	if (strlen($arrFec[2]) == 2) {
		if ($arrFec[2]+16 > date('y')) $d['DateOfBirth'] = $arrFec[0]."/".$arrFec[1]."/19".$arrFec[2];
		else $d['DateOfBirth'] = $arrFec[0]."/".$arrFec[1]."/20".$arrFec[2];
	} else $d['DateOfBirth'] = $arrFec[0]."/".$arrFec[1]."/".$arrFec[2];
	if (!($fechaNac = to_unix($ent->isDate($d['DateOfBirth'])))) muestraError ("falla por DateOfBirth", $correoMi);
	if (!($sex = $ent->isReal('1'.$d['Gender'], 2))) muestraError ("falla por Gender", $correoMi);
// 	$sex = substr($sex, 1);
	if ($sex == '10') $sex = 1; //si viene masc 0 lo paso a 1
	if ($sex == '11') $sex = 2; //si viene fem 1 lo paso a 2
	if (!($prof = $ent->isAlfanumerico(str_replace(" ", "_", $d['Profesion']), 50))) muestraError ("falla por Profesion", $correoMi);
	//if (!($sal = $ent->isNumero($d['MonthSalary'], 50))) muestraError ("falla por Salario mensual", $correoMi);
	if (!($usur = $d['UsuarioCode'])) muestraError ("falla por Código de usuario", $correoMi);
	$cliente = true;

	$paise = pais($pais);
	$paisNace = pais($paisNac);
	$prof = utf8_decode($prof);
	$prov = utf8_decode($prov);
	$titSub = false;
	
	
	//verifico que el cliente existe o no para saber si es una actualización o inscripción nueva
	$q = "select id, idtitanes, usuario from tbl_aisCliente where idcimex = $id";
	$correoMi .= $q."\n<br>";
	$temp->query($q);
	$cantT = $temp->num_rows();
	if ($cantT != 0){
		$idcli = $temp->f('id');
		$usur = $temp->f('usuario');
		$idTit = $temp->f('idtitanes');
	}
	
	if ($cantT == 0){ // Cliente nuevo
error_log("Cliente nuevo");
		//Parece que el cliente es nuevo porque el id de cimex no lo tengo verifico que los datos no se repitan
		$q = "select id, idcimex, idtitanes from tbl_aisCliente where paisResidencia = '$paise' and numDocumento = '$numid'";
		$temp->query($q);
		if ($temp->num_rows()) {
			// los datos se repiten es un cliente que ya existe pero que viene con un id nuevo
			$idTit = $temp->f('idtitanes');
			$pase = false;
			$titSub = true;
			$q = "update tbl_aisCliente set idcimex = '$id', usuario = '$usur', fecha = '".time()."', subfichero = 1 where id = ".$temp->f('id');
		} else {
			//Verifico el teléfono y nombre
			$q = "select id, idcimex, idtitanes from tbl_aisCliente where telf1 = '$telf' and nombre = '$nombre'";
			$temp->query($q);
			$can = $temp->num_rows();
			$idTit = $temp->f('idtitanes');
			$idn = $temp->f('id');

			//Verifico nombre y apellido
//			$temp->query("select id, idcimex, idtitanes from tbl_aisCliente where lower(nombre) = lower('$nombre') and lower(papellido) = lower('$ape1')");
//			if ($can == 0) {
//				$can = $temp->num_rows();
//				$idTit = $temp->f('idtitanes');
//				$idn = $temp->f('id');
//			}

			//Verifico correo
			if ($can == 0) {
				$temp->query("select id, idcimex, idtitanes from tbl_aisCliente where correo = '$email'");
				$can = $temp->num_rows();
				$idTit = $temp->f('idtitanes');
				$idn = $temp->f('id');
			}

			if ($can > 0) {
				// los datos se repiten es un cliente que ya existe pero que viene con un id nuevo
				$pase = false;
				$titSub = true;
				$q = "update tbl_aisCliente set idcimex = '$id', usuario = '$usur', fecha = '".time()."', subfichero = 1 where id = ".$idn;
			} else {
				// no se repiten los datos el cliente es efectivamente nuevo lo inserto
				$idTit = 0;
				$q = "insert into tbl_aisCliente (idcimex, nombre, papellido, sapellido, fnacimiento, numDocumento, fechaDocumento, correo, telf1, paisResidencia, provincia, ciudad, direccion, CP, paisNacimiento, sexo, ocupacion, salariomensual, fecha, fechaAltaCimex, usuario) values ('$id', '$nombre', '$ape1', '$ape2', '$fechaNac', '$numid', '$fechaDoc', '$email', '$telf', '$paise', '$prov', '$ciudad', '$dir', '$po', '$paisNace', '$sex', '$prof', '$sal', '".time()."', '".time()."','$usur')";
				$pase = true;
				
			}
		}
	} else { //Cliente actualizando
error_log("Cliente actualizado");
error_log("idtitanes=$idTit");

		$q = "update tbl_aisCliente set nombre = '$nombre', papellido = '$ape1', sapellido = '$ape2', numDocumento = '$numid', fechaDocumento = '$fechaDoc', correo = '$email', telf1 = '$telf', paisResidencia = '$paise', provincia = '$prov', ciudad = '$ciudad', direccion = '$dir', CP = '$po', sexo = '$sex', ocupacion = '$prof', paisNacimiento = '$paisNace', salariomensual = '$sal', fecha = '".time()."', fnacimiento = '".$fechaNac."', subfichero = 1 where idcimex = '$id'";
		$pase = false;
	}
	
	$correoMi .= $q."\n<br>";
	$temp->query($q);
	if ($temp->getErrorMsg()) {
		$correoMi .= "Error insertando Cliente- ".$temp->getErrorMsg();
		muestraError ("Error insertando Cliente AIS", $correoMi);
	}
	
	if ($pase) $idcli = $temp->last_insert_id(); //si se inscribió un benef nuevo saco el id

	$resident 	= 'False';
	if ($pais == 'ES') $resident = 'True';
	
	$data = array(
			"Name"						=> $nombre,
			"LastName1"					=> $ape1,
			"LastName2"					=> $ape2,
			"DocumentType"				=> 2,
			"DocumentNumber"			=> $numid,
			"DocumentExpirationDate"	=> date('Y-m-d',$fechaDoc),
			"DocumentCountry"			=> $pais,
			"Email"						=> $email,
			"PhoneNumber"				=> $telf,
			"Country"					=> $pais,
			"Province"					=> $prov,
			"City"						=> $ciudad,
			"PostalCode"				=> $po,
			"CountryOfBirth"			=> $paisNac,
			"Sex"						=> $sex,
			"Occupation"				=> 99,
			"Activity"					=> $prof,
			"IsResident"				=> $resident,
			"IsPublicOffice"			=> 'False',
			"Address"					=> $dir,
			"DateOfBirth"				=> date('Y-m-d',$fechaNac)
       );

	if ($pase) {
		$data = array_merge($data, array(
			"Signature"					=> $nombre.$ape1.$numid));
		$tipo = 'C';
	} else {
		$data = array_merge($data, array(
			"IdTitanes"					=> $idTit,
			"Signature"					=> $ciudad.$pais.$numid)); //según la API
//			"Signature"					=> $nombre.$ape1.$numid)); //según comunicó Javier
		$tipo = 'U';
	}
	foreach ($data as $value => $item) {
		$correoMi .= $value . "=" . $item . "<br>\n";
	}
	
	$sale = datATitanes($data,$tipo,$pasarC);
	
} else { //Es un beneficiario
	$correoMi .= "Beneficiario\n<br>";
	$pase=true;
	if (!($idcliente = $ent->isReal($d['IdCliente'], 12))) muestraError ("falla por IdCliente", $correoMi);
	if (!($ci = $ent->isReal($d['CI'], 11))) muestraError ("falla por CI", $correoMi);
	if ($d['Relation'] > 0 && $d['Relation'] < 19) $rela = $d['Relation'];
	else muestraError ("falla por Relation", $correoMi);
	if ($d['Reason'] > -1 && $d['Reason'] < 10) $rea = $d['Reason'];
	else muestraError ("falla por Reason", $correoMi);
	$telf = $d['Phone'];
	if (strlen(($d['Phone'])) == 0 || $d['Phone'] == 0) $d['Phone'] = time();
	if (!($telf = $ent->isAlfanumerico($d['Phone'], 15))) muestraError ("falla por Phone", $correoMi);
	
	//verifico que el cliente existe o no
	$q = "select id, idtitanes from tbl_aisCliente where idcimex = $idcliente";
	$correoMi .= $q."\n<br>";
	$temp->query($q);
	
error_log("buscaCliente");
	
	if ($temp->num_rows() == 0)  muestraError ("falla por que no se encuentra el IdCliente en la BD", $correoMi);
	else {
		$idcli = $temp->f('id');
		$idTitCl = $temp->f('idtitanes');
	}
	
	//verifico que el beneficiario existe o no para saber si es una actualización o inscripción nueva
	$q = "select id, idtitanes from tbl_aisBeneficiario where idcimex = ".$id;
	$correoMi .= $q."\n<br>";
	$temp->query($q);
	$cantT = $temp->num_rows();
	if ($cantT != 0) {
		$iben = $temp->f('id');
		$idTit =  $temp->f('idtitanes');
	}
	
	if ($cantT == 0){
		//chequeo que hayan enviado un beneficiario duplicado buscando el CI en la BD
		$qs = "select id, idtitanes from tbl_aisBeneficiario where numDocumento = '$ci'";
		$temp->query($qs);
		if ($temp->num_rows()>0) {
			$correoMi .= "El beneficiario ya existía en la BD hago el update de sus datos\n<br>";
			$iben = $temp->f('id');
			$idTit =  $temp->f('idtitanes');
			$q = "update tbl_aisBeneficiario set nombre = '".$nombre."', papellido = '".$ape1."', sapellido = '".$ape2."', telf = '".$telf."', direccion = '".$dir."', ciudad = '".$ciudad."', numDocumento = '$ci', fecha = '".time()."', idcimex = '".$id."' where id = $iben";
			$pase = false;
		} else {
			$qs = "select id, idtitanes from tbl_aisBeneficiario where telf = '$telf' and nombre = '$nombre'";
			$temp->query($qs);
			if ($temp->num_rows()>0) {
				$correoMi .= "El beneficiario ya existía en la BD hago el update de sus datos2\n<br>";
				$iben = $temp->f('id');
				$idTit =  $temp->f('idtitanes');
				$q = "update tbl_aisBeneficiario set nombre = '".$nombre."', papellido = '".$ape1."', sapellido = '".$ape2."', telf = '".$telf."', direccion = '".$dir."', ciudad = '".$ciudad."', numDocumento = '$ci', fecha = '".time()."', idcimex = '".$id."' where id = $iben";
				$pase = false;
			} else { //Se inserta el beneficiario por primera vez
				$idTit = 0;
				$correoMi .= "Insertar beneficiario\n<br>";
				$q = "insert into tbl_aisBeneficiario (idcimex, idrazon, nombre, papellido, sapellido, telf, direccion, ciudad, numDocumento, fecha, fechaAltaCimex) values ('".$id."', '$rea', '".$nombre."', '".$ape1."', '".$ape2."', '".$telf."', '".$dir."', '".$ciudad."', '$ci', '".time()."', '".time()."')";
				$pase = true;
			}
			
		}
	} else {// se hace el update de los datos
		$correoMi .= "Update del beneficiario\n<br>";
		$q = "update tbl_aisBeneficiario set nombre = '".$nombre."', papellido = '".$ape1."', sapellido = '".$ape2."', telf = '".$telf."', direccion = '".$dir."', ciudad = '".$ciudad."', numDocumento = '$ci', fecha = '".time()."' where idcimex = '".$id."'";
		$pase = false;
	}
	$correoMi .= $q."\n<br>";
	$temp->query($q);
	if ($temp->getErrorMsg()) {
		$correoMi .= "Error insertando Beneficiario- ".$temp->getErrorMsg();
		muestraError ("Error insertando Beneficiario AIS", $correoMi);
	}
	
//	if ($idTit == '' || !$idTit)
//		$idTit = 0;
		
	if (stripos($q, "insert") > -1) $iben = $temp->last_insert_id(); //si se inscribió un benef nuevo saco el id
	
	//reviso si la rel Cliente - Benef existía, si es así es un update si no es un insert
	$q = "select count(id) total from tbl_aisClienteBeneficiario where idcliente = $idcli and idbeneficiario = $iben";
	$temp->query($q);
	if ($temp->f('total') == 0) {//No existe la relación... la creo
		$q = "insert into tbl_aisClienteBeneficiario values (null, '$idcli', '$iben', '$rela', ".time().")";
				$correoMi .= $q."\n<br>";
error_log("Inserta la nueva relación");
		$temp->query($q);
				
		$pase = true;
	} else $pase = false;
	
	$fen = substr($ci, 0, 6);
	
	$ext = "20";
	if(date('y') - (substr($fen, 0,2)) < 15) $ext = "19";

	$data = array(
			"ClientId"				=> $idTitCl,
			"BeneficiaryId"			=> $idTit,
			"Name"					=> utf8_encode($nombre),
			"LastName1"				=> utf8_encode($ape1),
			"LastName2"				=> utf8_encode($ape2),
			"DocumentNumber"		=> $ci,
			"PhoneNumber"			=> $telf,
			"City"					=> utf8_encode($ciudad),
			"Address"				=> utf8_encode($dir),
			"Relation"				=> $rela,
			"Country"				=> 'CU'
		);

	if ($pase) { //Se inscribe el Beneficiario
		$data = array_merge($data, array(
			"Signature"					=> $idTitCl.utf8_encode($nombre).$telf));
		$tipo = 'B';
	} else { // realiza una actualización de los datos del Beneficiario
		$data = array_merge($data, array(
			"IdTitanes"					=> $idTit,
			"Signature"					=> $idTitCl.$ciudad.$telf)); //según la API
//			"Signature"					=> $idTitCl.utf8_encode($nombre).$telf)); //según comunicó Javier
		$tipo = 'F';
	}
	foreach ($data as $value => $item) {
		$correoMi .= $value . "=" . $item . "<br>\n";
	}
	
	error_log("data=".json_encode($data));
	$sale = datATitanes($data,$tipo,$pasarC);
}
error_log("sale=$sale");

if (strlen($sale) > 2) {
	$correoMi .=  "Actualiza el id de Titanes<br>\n";
	$correoMi .= "sale=$sale<br>\n";
	$ok = false;

	if ($pase) {
		$correoMi .= "<br>\nInscripción de";
		$lab = 'Inscripción';
		$mes = "Se ha inscrito";
	} else {
		$correoMi .= "<br>\nActualización de";
		$lab = 'Actualización';
		$mes = "Se ha actualizado";
	}
	
	if ($cliente) {//para los clientes
	
		$q = "select idtitanes from tbl_aisCliente where idcimex = $id and idtitanes is not null";
		$correoMi .= $q."\n<br>";
		$temp->query($q);
		if ($temp->num_rows() == 0) {//si el cliente no se encontraba inscrito con el número de Titanes..
			$arrVales = json_decode($sale);
			
error_log("sale=$sale");
			
			if ($arrVales->Id > 0) {
				$idTit = $arrVales->Id;
			} elseif (strpos($arrVales->Errors[0]->Message,'Customer already exists') > -1) {
				$idTit = str_replace(")", "", str_replace("Customer already exists.(Code:", "", $arrVales->Errors[0]->Message));
			}
error_log("idTit=".$idTit);
				$tid = '';
			if ($idTit>2) {
				$tid = "idtitanes = '$idTit',";
				
				$q = "update tbl_aisCliente set subfichero = 1, $tid fechaAltaTitanes = ".time()." where idcimex = $id"; //para los clientes
				$temp->query($q);
				$correoMi .= $q."<br> cliente OK<br>\n";
				$ok = true;
				$lab .= " de Cliente";
				$mes .= " el Cliente ".$nombre." ".$ape1." ".$ape2;
				$mes .= "Viene de Ais con el identificador $id, en nosotros es el $idcli y en Titanes está inscrito con el $idTit.";
			} else {
				$correoMi .= " Se ha producido un error<br>\n";
				$errorinsc = true;
				//como dió error la inscripción lo borro
// 				$q = "delete from tbl_aisCliente where idcimex = $id";
// 				$temp->query($q);
			}
		} else {//el cliente ya se encontraba inscrito ya con el número de Titanes..
			$mes .= " el Cliente ".$nombre." ".$ape1." ".$ape2;
			if ($errorinsc) 
				$mes .= " ha dado error en la inscripción";
			else
				$mes .= ". Viene de Ais con el usuario $usur y el identificador $id, en nosotros es el $idcli 
						y en Titanes está inscrito con el ".$temp->f('idtitanes');
			$ok = true;
		}
		
		//
		
	} else {// para los beneficiarios
		$q = "select idtitanes from tbl_aisBeneficiario where idcimex = $id and idtitanes is not null";
		$correoMi .= $q."\n<br>";
		$temp->query($q);
		if ($temp->num_rows() == 0) {//el beneficiario no tenía idtitanes así que se lo ponemos
			
			$arrVales = json_decode($sale);
			if ($arrVales->Id > 0) {
				$idTit = $arrVales->Id;
			} elseif (strpos($arrVales->Errors[0]->Message,'Beneficiary already exists') > -1) {
				$idTit = str_replace(")", "", str_replace("Beneficiary already exists.(Code:", "", $arrVales->Errors[0]->Message));
			}
error_log("idTit=".$idTit);
				
				$q = "update tbl_aisBeneficiario set idtitanes = '$idTit', fechaAltaTitanes = ".time()." where idcimex = $id";// para los beneficiarios
				$temp->query($q);
				$correoMi .= " beneficiario OK<br>\n";
					$ok = true;
				$lab .= " de Beneficiario";
				$mes .= " el Beneficiario ".$nombre." ".$ape1." ".$ape2;
				$mes .= ". Viene de Ais con el identificador $id, en nosotros es el $iben y en Titanes está inscrito con el $idTit.";
	
				//borro la relación que exista entre ese cliente y ese beneficiario
				$q = "delete from tbl_aisClienteBeneficiario where idcliente = $idcli and idbeneficiario = $iben";
				$correoMi .= $q."\n<br>";
				$temp->query($q);

				//escribo la nueva relación
				$q = "insert into tbl_aisClienteBeneficiario values (null, '$idcli', '$iben', '$rela', ".time().")";
				$correoMi .= $q."\n<br>";
error_log("se inserta la nueva relación");
				$temp->query($q);
	
			if (!$idTit > 0) {
				$correoMi .= " Se ha producido un error<br>\n";
				//como dió error la inscripción lo borro
// 				$q = "delete from tbl_aisBeneficiario where idcimex = $id";
// 				$temp->query($q);
			}
		} else { //el beneficiario ya tenía istitanes 
			$ok = true;
			$mes .= " el Beneficiario ".$nombre." ".$ape1." ".$ape2;
			$mes .= ". Viene de Ais con el identificador $id, en nosotros es el $iben y en Titanes está inscrito con el $idTit.";
		}
		
		//buscamos los idtitanes de todos sus clientes
		$q = "select b.idtitanes from tbl_aisClienteBeneficiario r, tbl_aisCliente c, tbl_aisBeneficiario b where r.idcliente = c.id and c.idtitanes = $idTitCl and r.idbeneficiario = b.id";
		$temp->query($q);
		$arrBen = $temp->loadResultArray();
		
		unset($data["Name"],$data["LastName1"],$data["LastName2"],$data["DocumentNumber"],$data["PhoneNumber"],$data["City"],$data["Address"],$data["Relation"],$data["Country"],$data['BeneficiaryId'],$data['Signature']);
		
		$sale = datATitanes($data,'R',$pasarC); //Verificamos la relación y obtenemos un array con los id de los Beneficiarios
		
		$arrBenTit = json_decode($sale);
		sort($arrBenTit);
		sort($arrBen);
error_log("*****************");
error_log("sale=".$sale);
error_log("arrBenTit=". json_encode($arrBenTit));
error_log("arrBen=". json_encode($arrBen));
//		error_log(($arrBen)." == ".($arrBenTit));
		
		if ($arrBen == $arrBenTit) {//los arrays son iguales
			$correoMi .= "Todo bien con las relaciones<br>";
error_log($correoMi." De titanes se informan los Beneficiarios ". json_encode($arrBenTit). "<br>En la BD nnuestra aparecen ". json_encode($arrBen));
		} else {
			$correoMi .= "Se ha producido un error de Relación Cliente - Beneficiario <br> De titanes se informan los Beneficiarios ". json_encode($arrBenTit). "<br>En la BD nuestra aparecen ". json_encode($arrBen)."<br>";
		}
		
	}
//	$correoMi .= $q."\n<br>";
//	$temp->query($q);
	
// 	$correo->todo(48, $lab." en Ais", $mes);
	$correoMi .= $mes;
	if ($ok) echo $id;
	elseif ($titSub)  echo $id;
	else echo $sale;
// echo "generando un error en la inscripción";
}

$correo->todo(48, 'Insertando usuarios en Ais', $correoMi);

function muestraError ($etiqueta, $textoCorreo) {
	global $correoMi;
	$correo = new correo();
	$textoCorreo .= $etiqueta;
	$correo->set_subject('Error insertando usuario en Ais');
	$correo->set_message($textoCorreo);
	$correo->envia(9);
	echo '<!-- '.$etiqueta.' -->';
	//$correo->todo(48, 'Error en los datos', $textoCorreo." ** ".$correoMi);
	exit;
}

/**
 * Busca el identificador del país en base al ISO2
 * @param strin $codpais
 * @return integer
 */
 function pais ($codpais) {
 	global $temp;
	$q = "select id from tbl_paises where iso2 = '$codpais'";
	$temp->query($q);
	return $temp->f('id');
}
?>
