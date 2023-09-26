<?php
session_start();
include '../configuracion.php';
include 'class_mysql.php';
$clave = md5($_REQUEST["pass"].$_REQUEST["usern"]);

//echo $clave;
$q = "select * from users where clave='$clave'";
$check = $conn->execute($q);

if($conn->numRows() == 1){
	$rs= mysql_fetch_object($check);
	$_SESSION['user'] = "$rs->Nombre";
    $_SESSION['iduser'] = "$rs->iduser";
	echo "<h3 style='color:green'>Acceso Autorizado.</h3>Bienvenido ".$_SESSION['user']."..";
	echo "<script>var nlog=true;</script>";
}else{
	echo "<h3 style='color:red'>Usuario y/o contrase&ntilde;a incorrectos.</h3>";
	echo "<script>var nlog=false;</script>";
}

?>
