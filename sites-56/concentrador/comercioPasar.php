<?php
define('_VALID_ENTRADA',1);
/* 
 * Rellena la tabla tbl_colComerPasar con los datos 
 * de las pasarelas que hasta el momento ha usado cada comercio
 */

require_once( 'configuration.php' );
require_once( 'include/database.php' );
$database = &new database($host, $user, $pass, $db, $table_prefix);
require_once( 'include/ps_database.php' );
require_once( 'include/hoteles.func.php' );
require_once( 'admin/adminis.func.php' );
$temp = new ps_DB;

$q = "select distinct idcomercio from tbl_transacciones";
$temp->query($q);
$arrComer = $temp->loadResultArray(0);


print_r($arrComer);

?>