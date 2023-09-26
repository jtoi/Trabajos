<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
if (isset($_SESSION)) {
	$_SESSION = array();
	$params = session_get_cookie_params();

	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), "", time() - 42000, 
			$params['path'], $params['domain'],
			true, $params['httponly']
			);

	}

	session_destroy();

}

?>
<script language="JavaScript">
window.open('index.php','_self');
</script>
<table border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="alerta" height="436" align="center" valign="middle">Variables de sessi&oacute;n destruidas. Cierre la p&aacute;gina.</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>