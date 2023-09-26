<?php

$id = $_REQUEST['id'];
$enlace = str_replace('/admin/componente/comercio/bajando.php', '', $_SERVER['SCRIPT_FILENAME'])."/desc/".$id;
header ("Content-Disposition: attachment; filename=".$id."\n\n");
header ("Content-Type: application/octet-stream");
header ("Content-Length: ".filesize($enlace));
readfile($enlace);

?>