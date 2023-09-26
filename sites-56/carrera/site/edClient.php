<?php ini_set("error_reporting",E_ALL);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta name="author" content="Alejandro D&iacute;z Cadavid modificado por Julio Toirac (jtoirac@gmail.com)diazc@gmail.com)" />
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
	<link rel="stylesheet" href="images/style.css" type="text/css" />
        <title>Inscripci?n de Atletas</title>
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript">
                       function checkform(){
               var go=false;
				var email=$('#ecli').val();
                if($('#ncli').val() == ""){
                    alert("El nombre del cliente no puede estar en blanco.");
                    $('#ncli').focus();
                }else if($('#ucli').val() == ""){
                    alert("El nombre de usuario no puede estar en blanco.");
                    $('#ucli').focus();
                }else if($('#ucli').val().length < 5){
                    alert("El nombre de usuario demasiado corto. Debe tener al menos cinco caracteres");
                    $('#ucli').focus();
                }else if($('#pcli').val() != "" && $('#pcli').val().length < 6){
	                    alert("La clave de usuario tiene menos de seis caracteres.");
	                    $('#pcli').focus();
                }else if($('#ecli').val() ==""){
                    alert("El email del cliente no puede estar en blanco.");
                    $('#ecli').focus();
                }else if(!email.match(/^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i)){
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
            <div class="subheader">
                <?php
                   require "class_mysql.php";
                   if(!isset($_REQUEST["cli"]) && $_REQUEST["cli"]==""){
                $sql="select * from users";
                $result=$conn->execute($sql) or die(mysql_error());
                while($rs=  mysql_fetch_object($result)){
                    echo "<div id='lista'>$rs->iduser&nbsp;&nbsp;&nbsp;$rs->Nombre<span style='text-align:right;float:right'><input type='button'  value='Editar' onclick=\"document.location='edClient.php?cli=$rs->iduser'\"></span></div>";
                }}else{
					if($_SESSION["iduser"]==1)
						echo "<div style=\"text-align: right; position: absolute;margin-top: -25px;margin-left:-35px; width: 960px;\"><a href=\"edClient.php\">volver al listado de usuarios</a></div>";
					$usr=$_REQUEST["cli"];
					$sql="select * from users where iduser=$usr";
					$result=$conn->execute($sql) or die(mysql_error());
					$rs=mysql_fetch_object($result);
                
                ?>
            </div>
            <div class="listop">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr >
                        <td valign="top"><img src="images/lc.gif" width="10" height="10" align="left"/></td>
                        <td><span class="white">Editando Cliente <?php echo $rs->Nombre;?></span></td>
                        <td valign="top"><img src="images/rc.gif" width="10" height="10" align="right" alt=""/></td>
                    </tr>
                </table>
            </div>
            
            <div class="subheader" style="text-align: center; padding: 5px;border:0; border-left:1px solid #c85e35; border-right: 1px solid #c85e35;">
                <div style="margin:0 auto;text-align:left;width:60%;">
                <form id="ncf" method="post" action="edClient.php">
                    Nombre: <input type="text" name="ncli" id="ncli" style="width:406px;margin-left:41px;" value="<?php echo $rs->Nombre?>"/><br /><br />
                    Usuario: <input type="text" name="ucli" id="ucli" style="width:150px;margin-left:41px;" value="<?php echo $rs->user?>"/>&nbsp;&nbsp;&nbsp;&nbsp;
                    Contrase&ntilde;a: <input type="text" name="pcli" id="pcli" style="width:150px;margin-left:17px;" /><span style="color:red; font-weight: bold;">*</span><br /><br />
                    Email: <input type="text" name="ecli" id="ecli" style="width:406px;margin-left:53px;" value="<?php echo $rs->email?>"/><br /><br />
                    Tipo de Usuario: <select name="tucli" id="tucli">
                        <option value="2" <?php if($rs->tipo ==2) echo "selected"?>>Cliente</option>
                        <option value="1" <?php if($rs->tipo ==1) echo "selected"?>>Administrador</option>
                    </select>
                    <p style="padding: 6px 6px 0; text-align: right; margin-top: 15px;border-top: 1px dotted #444;"><input type="button" name="sfc" id="sfc" value="Modificar" title="Modificar cliente" onclick="checkform();"/>&nbsp;&nbsp;&nbsp;<input type="reset" value="Cancelar" title="Restablecer el formulario"/></p>
                <input type="hidden" name="pmark" value="1" />
				<input type="hidden" name="idu" value="<?php echo $usr;?>" />
                <span style="color:red;">* Para mantener la contrase&ntilde;a deje en blanco ese campo.<br /> Si desea cambiarla escriba la nueva contrase&ntilde;a en el campo de texto correspondiente.</span>
                </form>
				
                </div>
            </div>
            <div class="lisbot"></div>
            <?php }?>
           
        </div>
		<div id="terminado" style="display:none;text-align:center;" >
                    <h2 class="red">Cliente modificadoado correctamente...</h2>&nbsp;&nbsp;
                    <input type="button" value="Volver al inicio.." onclick="document.location='index.php'"/>
                    <input type="button" value="Editar otro cliente.." onclick="document.location='edClient.php'"/>
                </div>
         <div class="footer">
                &copy; Copyright 2011 Inscripci&oacute;n en la Prueba.
            </div>
    </body>

</html>

<?php
if(isset($_REQUEST['pmark']) && $_REQUEST['pmark'] != "" ){
	$campos="";
    //definir la consulta de parametros basicos de proyecto
    if($_REQUEST['ncli']!=""){ 
		$ncli = $_REQUEST['ncli']; 
		$campos="Nombre='$ncli',";
	}
    if($_REQUEST['ucli']!=""){
		 $ucli = $_REQUEST['ucli'];
		$campos.="user='$ucli',";
	}
	if($_REQUEST['pcli']!=""){ 
		$pcli = md5($_REQUEST['pcli'].$_REQUEST['ucli']); 
		$campos.="clave='$pcli',";
	}
	if($_REQUEST['ecli']!=""){ $ecli = $_REQUEST['ecli']; $campos.="email='$ecli',";}
	if($_REQUEST['tucli']!="") {$tucli = $_REQUEST['tucli']; $campos.="tipo=$tucli";}
	$iud = $_REQUEST['idu'];
    $sql="update users set $campos where iduser=".$iud	;
//echo $sql;
    $conn->execute($sql) or die(mysql_error());
    if(mysql_errno ()==0){
    	echo "<script type='text/javascript'>$('#terminado').display();</script>";
    }
}
?>
