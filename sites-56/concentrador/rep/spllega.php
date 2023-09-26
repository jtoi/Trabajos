<?php

define( '_VALID_ENTRADA', 1 );
$lleg = 'Valores: ';
if (count($_REQUEST) > 1) $data = $_REQUEST;
else $data = json_decode(file_get_contents('php://input'), true);
foreach ($data as $key => $value) {
	$lleg .= $key." = ".$value."<br>\n";
}

$lleg .= "<br>\n"."valor =".$data['data']['amount']."<br>\n";

mail('jtoirac@gmail.com', 'Datos Pagantis', $lleg);

?>
