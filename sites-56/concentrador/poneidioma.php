<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('display_errors', 0);
error_reporting(0);

define('_VALID_ENTRADA', 1);
//ini_set("display_errors", 1);
//error_reporting(E_ALL & ~E_NOTICE);
if (!session_start())
	session_start();
require_once( 'admin/classes/entrada.php' );
//require_once( '../include/sendmail.php' );
require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );

$temp = new ps_DB;

$q = 'select id, condiciones_esp, condiciones_eng, correo_esp, correo_eng, voucherEs, voucherEn from tbl_comercio';
$temp->query($q);
$arrLin = $temp->loadRowList();

//while ($temp->next_record()) {
//    $arrLin = $temp->loadRowList();
//    echo "sale={$arrLin[2]}<br>";
//}
echo " ";
for ($i = 0; $i < count($arrLin); $i++) {
    $q = "insert into tbl_traducciones (idIdioma, idcomercio, tipo, texto, fecha) values ('1','{$arrLin[$i][0]}','0','{$arrLin[$i][1]}',unix_timestamp())";
    $temp->query($q);
    $q = "insert into tbl_traducciones (idIdioma, idcomercio, tipo, texto, fecha) values ('1','{$arrLin[$i][0]}','1','{$arrLin[$i][3]}',unix_timestamp())";
    $temp->query($q);
    $q = "insert into tbl_traducciones (idIdioma, idcomercio, tipo, texto, fecha) values ('1','{$arrLin[$i][0]}','2','{$arrLin[$i][5]}',unix_timestamp())";
    $temp->query($q);
    echo $arrLin[$i][0]."<br>";
    if ($arrLin[$i][0] == 32) {
        echo "entra por acá<br>";
        $q = "insert into tbl_traducciones (idIdioma, idcomercio, tipo, texto, fecha) values ('3','{$arrLin[$i][0]}','0','{$arrLin[$i][2]}',unix_timestamp())";
        $temp->query($q);
        $q = "insert into tbl_traducciones (idIdioma, idcomercio, tipo, texto, fecha) values ('3','{$arrLin[$i][0]}','1','{$arrLin[$i][4]}',unix_timestamp())";
        $temp->query($q);
        $q = "insert into tbl_traducciones (idIdioma, idcomercio, tipo, texto, fecha) values ('3','{$arrLin[$i][0]}','2','{$arrLin[$i][6]}',unix_timestamp())";
        $temp->query($q);
    }
    echo "sigue acá<br><br>";
    $q = "insert into tbl_traducciones (idIdioma, idcomercio, tipo, texto, fecha) values ('2','{$arrLin[$i][0]}','0','{$arrLin[$i][2]}',unix_timestamp())";
    $temp->query($q);
    $q = "insert into tbl_traducciones (idIdioma, idcomercio, tipo, texto, fecha) values ('2','{$arrLin[$i][0]}','1','{$arrLin[$i][4]}',unix_timestamp())";
    $temp->query($q);
    $q = "insert into tbl_traducciones (idIdioma, idcomercio, tipo, texto, fecha) values ('2','{$arrLin[$i][0]}','2','{$arrLin[$i][6]}',unix_timestamp())";
    $temp->query($q);
}
?>