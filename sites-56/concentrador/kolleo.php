<?php
define( '_VALID_ENTRADA', 1 );
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'include/sendmail.php';
$from = 'info@amfglobalitems.com';

$headers  = 'MIME-Version: 1.0' . "\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
$headers .= "From: $from\n";
$headers .= "Reply-To: $from\n"; 
$headers .= "Return-Path: $from\n";

$correo = new sendmail();

$correo->from($from);
$correo->to('jtoirac@mailinator.com');
$correo->set_subject('Verificar');
$correo->set_message('Esta es una prueba de correo');
$correo->set_headers($headers);
$correo->send();


?>
