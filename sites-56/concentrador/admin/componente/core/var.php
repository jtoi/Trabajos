<?php
define( '_VALID_ENTRADA', 1 );
require_once("../../classes/SecureSession.class.php");
$Session = new SecureSession(3600);

include_once( '../../../configuration.php' );
include_once( '../../classes/entrada.php' );
require_once( '../../../include/database.php' );
$database = new database($host, $user, $pass, $db, $table_prefix);
require_once( '../../../include/ps_database.php' );
include_once( '../../classes/class_dms.php' );
$temp = new ps_DB;
$ent = new entrada;

$d = $_POST;
if(!$ent->isAlfabeto($d['func'], 3)) exit;
$fun = $d['func'];

if($fun == 'sms') {
	if($ent->isAlfanumerico($d['ent'],1)) $ent = $ent->isAlfanumerico($d['ent'],1); else exit ();
	$records = $d['rec'];
	$periodo = $d['per'];
	$pagina = $d['pag'];
	$dms=new dms_send;
	
	if ($ent == 1) {
		$dms->autentificacion->idcli='126560';
		$dms->autentificacion->username='amfglobalitems';
		$dms->autentificacion->passwd='Mario107';
	} else {
		$dms->autentificacion->idcli='185529';
		$dms->autentificacion->username='Caribbeanonline';
		$dms->autentificacion->passwd='Amfn0v1e';
	}
	$filtro=new dms_filtro;
	$filtro->nfilas = $records;
	$filtro->periodo = $periodo;
	$filtro->ndesde = $pagina;
	$dms->getReportsMensajes($filtro);

	$repor = array();
	if ($dms->autentificacion->error){
		//Error de autentificacion con la plataforma
		$error = $dms->autentificacion->mensajeerror;
	}else{

		for ($n=0;$n<count($dms->reportes_envios->reportes);$n++){
			$r=$dms->reportes_envios->reportes[$n];
			if(strlen($r->fecha_entregado)) $nfecha = $r->fecha_entregado; else $nfecha = "&nbsp;";
			if(strlen($r->htexto)) $text = $r->htexto; else $text = "&nbsp;";
			if(strlen($r->movil)) $mov = $r->movil; else $mov = "&nbsp;";
			$repor[] = array("fec"=>$nfecha, "mens"=>$text, "tel"=>$mov, "mensaT"=>$r->total_mensajes, "credi"=>$r->total_creditos, "conf"=>$r->confirmados, "Sconf"=>$r->sin_confirmacion, "Sinf"=>$r->sin_informacion);
		}
//		echo $dms->
	}
	$arrSal[] = $repor;
//	print_r($r);
	$arrSal = array("cred"=>$dms->reportes_envios->credito, "Mensajes enviados"=>$dms->mensajes->total_mensajes, "Mensajes errores"=>$dms->mensajes->total_error, "NTotal"=>$dms->reportes_envios->ntotal, "NFilas"=>$dms->reportes_envios->nfilas, "NDesde"=>$dms->reportes_envios->ndesde, "repor"=>$repor, 'error'=>$error);
	
	echo json_encode($arrSal);
}

?>