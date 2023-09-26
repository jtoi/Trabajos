<?php
/**
 * Inserta los usuarios a los que les llegará el Newsletter a partir de un fichero CSV
 * separados por punto y coma
 */
include_once('conf.php');
include_once('mysqli.php');
$temp = new ps_DB;

if ($file = fopen("Email_List_Newsletter.csv", "r")) {
    while(!feof($file)) {
        $line = fgets($file,4096);
        $arrCli = explode(';',$line);
        $q = "select count(id) total from mp_newsletter_user where email = '".$arrCli[2]."'";
        $temp->query($q);
        echo $temp->f('total'); 
        if ($temp->f('total') == 0) {
            $q = "insert into mp_newsletter_user (nombre, email) values ('".$arrCli[0]."','".$arrCli[2]."')";
            $temp->query($q);
            
            echo "<br>";
        } else echo "->".$arrCli[2]."<br>";
    }
    fclose($file);
}



?>