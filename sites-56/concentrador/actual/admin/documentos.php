<?php
defined( '_VALID_ENTRADA' ) or die( 'Direct Access to this location is not allowed.' );

$file = 'Manual Usuario.doc.zip';
$dir = '../documentos/';
header ("Content-Disposition: attachment; filename=".$dir.$file." ");
header ("Content-Type: application/x-zip-compressed");
//
header ("Content-Length: ".filesize($dir.$file));
readfile($dir.$file);
?>

