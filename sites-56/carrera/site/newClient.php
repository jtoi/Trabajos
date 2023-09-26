<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta name="author" content="Alejandro D&iacute;z Cadavid modificado por Julio Toirac (jtoirac@gmail.com)" />
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
	<link rel="stylesheet" href="images/style.css" type="text/css" />
        <title>Inscripci?n de Atletas</title>
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript">
			function isUnsignedInteger(s) {
			  return (s.toString().search(/^[0-9]+$/) == 0);
			}

			function checkform(){
               var go=false;
                if($('#ncli').val() == ""){
                    alert("El nombre del cliente no puede estar en blanco.");
                    $('#ncli').focus();
                }else if($('#ucli').val() == ""){
                    alert("El nombre de usuario no puede estar en blanco.");
                    $('#ucli').focus();
                }else if($('#ucli').val().length < 5){
                    alert("El nombre de usuario demasiado corto. Debe tener al menos cinco caracteres");
                    $('#ucli').focus();
                }else if($('#pcli').val() == "" || $('#pcli').val().length < 6 ){
                    alert("La clave de usuario est? vac?a o tiene menos de seis caracteres.");
                    $('#pcli').focus();
                }else if($('#ecli').val()==""){
                    alert("El email del cliente no puede estar en blanco.");
                    $('#ecli').focus();
                }else if(!$('#ecli').val().match(/^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i)){
					alert("El email del cliente no es correcto.");
                    $('#ecli').focus();
                }else{
                    $('#ncf').submit();
                }
            }
        </script>
</head>
    <body style="margin:0 auto;">
        <div class="logo">
            <h1><a href="#" title="Inscripci&oacute;n de atletas">INSCRIPCI&Oacute;N DE <span class="red">ATLETAS</span></a></h1>
	</div>
        <div style="text-align: right; position: absolute;margin-top: -35px;margin-left:55px; width: 960px;;"><a href="index.php">volver al inicio</a></div>
        <div class="content2">
            <div class="listop">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr >
                        <td valign="top"><img src="images/lc.gif" width="10" height="10" align="left"/></td>
                        <td><span class="white">Agregar Nuevo Cliente</span></td>
                        <td valign="top"><img src="images/rc.gif" width="10" height="10" align="right" alt=""/></td>
                    </tr>
                </table>
            </div>
            
            <div class="subheader" style="text-align: center; padding: 5px;border:0; border-left:1px solid #c85e35; border-right: 1px solid #c85e35;">
                <div style="margin:0 auto;text-align:left;width:60%;">
                <form id="ncf" method="post" action="newClient.php">
                    Nombre: <input type="text" name="ncli" id="ncli" style="width:406px;margin-left:41px;"/><br /><br />
                    Usuario: <input type="text" name="ucli" id="ucli" style="width:150px;margin-left:41px;"/>&nbsp;&nbsp;&nbsp;&nbsp;
                    Contrase&ntilde;a: <input type="text" name="pcli" id="pcli" style="width:150px;margin-left:17px;"/><br /><br />
                    Email: <input type="text" name="ecli" id="ecli" style="width:406px;margin-left:53px;"/><br /><br />
                    Tipo de Usuario: <select name="tucli" id="tucli">
                        <option value="2">Cliente</option>
                        <option value="1">Administrador</option>
                    </select>
                    <p style="padding: 6px 6px 0; text-align: right; margin-top: 15px;border-top: 1px dotted #444;"><input type="button" name="sfc" id="sfc" value="Agregar" title="Agegar nuevo cliente" onclick="checkform();"/>&nbsp;&nbsp;&nbsp;<input type="reset" value="Cancelar" title="Restablecer el formulario"/></p>
                <input type="hidden" name="pmark" value="1" />
                </form>
		<div id="terminado"style="display:none;text-align:center;" >
                    <h2 class="red">Cliente agregado correctamente...</h2>&nbsp;&nbsp;
                    <input type="button" value="Volver al inicio.." onclick="document.location='index.php'"/>
                    <input type="button" value="Agregar otro.." onclick="document.location='newClient.php'"/>
                </div>
                </div>
            </div>
            
            <div class="lisbot"></div>
            <div class="footer">
                &copy; Copyright 2011 Gesti&oacute;n de Proyectos.
            </div>
        </div>
    </body>

</html>

<?php
if(isset($_REQUEST['pmark']) && $_REQUEST['pmark'] != "" ){
require "class_mysql.php";
    //definir la consulta de parametros basicos de proyecto
    if($_REQUEST['ncli']!="") $ncli = $_REQUEST['ncli'];
    if($_REQUEST['ucli']!="") $ucli = $_REQUEST['ucli'];
    if($_REQUEST['pcli']!="") $pcli = md5($_REQUEST['pcli'].$_REQUEST['ucli']);
    if($_REQUEST['ecli']!="") $ecli = $_REQUEST['ecli'];
    if($_REQUEST['tucli']!="") $tucli = $_REQUEST['tucli'];

    $sql="insert into users(Nombre,user,clave,email,tipo) values('$ncli','$ucli','$pcli','$ecli',$tucli)";
    $conn->execute($sql) or die(mysql_error());
    if(mysql_errno ()==0){
    	echo "<script>$('#terminado').display();</script>";
    }
}
?>
