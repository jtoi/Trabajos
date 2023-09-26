<?php
define( '_VALID_ENTRADA', 1 );

require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );
$temp = new ps_DB;
$bd = new ps_DB;

$d=$_REQUEST['cod'];
$c=$_REQUEST['com'];
$fec = time();

if (strlen($d) < 20) {

    $query = sprintf("select c.condiciones_esp, c.condiciones_eng, idioma
                from tbl_reserva r, tbl_comercio c
                where r.id_comercio = c.idcomercio
                    and r.codigo = '%s'
					and r.id_comercio = '%s'", $d, $c);
if ($_SERVER['SERVER_ADDR'] == '190.15.147.50' || $_SERVER['SERVER_ADDR'] == '217.160.140.131') {echo $query; echo "<br>";}

    $temp->query($query);
  
	$idioma = $temp->f('idioma');
	$numT = $temp->num_rows();

	if ($idioma == 'en') {
		if ($numT != 0) {
			$condiciones = $temp->f('condiciones_eng');
		} else {
			switch ($estado) {
				case 'P':
					$salida = "";
			}
		}
	} else {
		if ($numT != 0) {
			$condiciones = $temp->f('condiciones_esp');
		}
	}


    $imagen = "admin/template/images/banner2.png";


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $titulo ?></title>
<link href="admin/template/css/admin.css" rel="stylesheet" type="text/css" />
<link href="admin/template/css/calendar.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="encabPago">
        <div id="logoPago"><img src="<?php echo $imagen ?>" /> </div>
        <div class="inf"></div>
    </div>
    <div id="cuerpoPago">
        <?php echo $salida ?>
<?php
if (strlen($condiciones) > 3) {
	echo str_replace("\n", "<br>", $condiciones) ?><br /><br />
<?php }
if ($numT != 0) { ?>
    </div>
<?php } ?>
    <div id="cuerpoPago">
        <div class="inf2"></div>
        Copyright &copy; Administrador de Comercios, <?php echo date('Y', time()); ?><br /><br />
        <table width="10" border="0" cellspacing="0" align="center">
            <tr>
                <td>

                </td>
            </tr>
            <tr>
                <td height="0" align="center">
                   <!-- GeoTrust QuickSSL [tm] Smart  Icon tag. Do not edit. -->
						<!--<script language="javascript" type="text/javascript" src="//smarticon.geotrust.com/si.js"></script>-->
		<table width="135" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose GeoTrust SSL for secure e-commerce and confidential communications.">
<tr>
<td width="135" align="center" valign="top"><script type="text/javascript" src="https://seal.geotrust.com/getgeotrustsslseal?host_name=www.administracomercios.com&amp;size=S&amp;lang=en"></script><br />
<a href="http://www.geotrust.com/ssl/" target="_blank"  style="color:#000000; text-decoration:none; font:bold 7px verdana,sans-serif; letter-spacing:.5px; text-align:center; margin:0px; padding:0px;"></a></td>
</tr>
</table>
						<!-- end  GeoTrust Smart Icon tag -->
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php } ?>
