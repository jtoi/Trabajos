<?php

/* 
 * Lista los SMS enviados a descomsms
 */

define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
include_once( 'admin/classes/class_dms.php' );

$temp = new ps_DB;

$periodo = $d['per'];

$dms=new dms_send;
$filtro=new dms_filtro;

$dms->autentificacion->idcli='185529';
$dms->autentificacion->username='Caribbeanonline';
$dms->autentificacion->passwd='Amfn0v1e';
$filtro->nfilas = 300;
$filtro->periodo = 'M';
$filtro->ndesde = 1000;
$dms->getReportsMensajes($filtro);

for ($n=0;$n<count($dms->reportes_envios->reportes);$n++){
    $r=$dms->reportes_envios->reportes[$n];
    if(strlen($r->fecha_entregado)) $nfecha = $r->fecha_entregado; else $nfecha = "&nbsp;";
    if(strlen($r->htexto)) $text = $r->htexto; else $text = "&nbsp;";
    if(strlen($r->movil)) $mov = $r->movil; else $mov = "&nbsp;";
    echo $nfecha.";".$mov.";".$text."<br />";
    //$repor[] = array("fec"=>$nfecha, "mens"=>$text, "tel"=>$mov, "mensaT"=>$r->total_mensajes, "credi"=>$r->total_creditos, "conf"=>$r->confirmados, "Sconf"=>$r->sin_confirmacion, "Sinf"=>$r->sin_informacion);
}

//print_r($repor);
?>