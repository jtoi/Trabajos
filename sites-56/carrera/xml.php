<?php

$file = fopen('xml.xml','r');
$contents = fread($file, filesize('xml.xml'));
fclose($handle);
echo $contents

?>
