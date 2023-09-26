<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
if (isset($_REQUEST['rol'])) {
	global $temp;
	$rol = $_REQUEST['rol'];

	$qs = "delete from tbl_accesos where idrol = $rol";
	$temp->query($qs);

	$tempso = $_REQUEST['acceso'];
//echo print_r($tempso);

	foreach($tempso as $item) {
		$sql = "select parentid from tbl_menu where id = $item";
		$temp->query($sql);
		$parentid = $temp->f("parentid");

		$sql = "select count(*) total from tbl_accesos where idmenu = $parentid and idrol = $rol";
//		echo $sql."<br>";
		$temp->query($sql);
		$totl = $temp->f("total");
		if ($totl == 0) {
			$sql = "insert into tbl_accesos (idrol, idmenu, fecha) values ($rol, $parentid, ".time().")";
//			echo $sql."<br>";
			$temp->query($sql);
		}
		$sql = "insert into tbl_accesos (idrol, idmenu, fecha) values ($rol, $item, ".time().")";
//		echo $sql."<br><br>";
		$temp->query($sql);
	}

}
 ?>

<table align="center" border="0" cellspacing="0" cellpadding="10">
	<tr>
		<td class="title_pag"><?php echo _ACCESOS_TITULO ?></td>
	</tr>
</table>
<table align="center" border="1" width="600" cellspacing="0" cellpadding="10"><?php
//$grup = new ps_DB;
//$tempso = new ps_DB;
//Cambiar esto
$q = "select idrol, nombre, caract from tbl_roles where orden > ".$_SESSION['grupo_rol']." order by orden";

$temp->query($q);
$arrRol = $temp->loadAssocList();
foreach ($arrRol as $item){
	$sql = 'SELECT m.id , m.link,'
        . ' case when (select count(*) from tbl_accesos where idmenu = m.id and idrol = '
		. $item['idrol']
		. ') > 0 then \'S\' else \'N\' end as enlace'
        . ' FROM tbl_menu m, tbl_accesos a '
        . ' where m.link != \'\''
		. ' and a.idmenu = m.id'
		. ' and a.idrol = '.$_SESSION['rol'];
//	echo $sql;
	$temp->query($sql);
	$arrlin = $temp->loadAssocList();
	?>
	<form id="form1" name="form1" method="post" action="">
	<input name="rol" type="hidden" value="<?php echo $item['idrol']; ?>" />
		<tr>
			<td><?php echo $item['caract']; ?></td>
			<td><?php echo $item['nombre']; ?></td>
			<td><select class="formul" name="acceso[]" size="5" multiple="multiple"><?php
				foreach($arrlin as $vale){
					$menus = $vale['link'];
//					$menus = str_replace(' /  / ', '', $menus);
//					if (substr(strrev($menus), 0, 1) == '/') $menus = strrev(substr(strrev($menus), 1, strlen($menus)));
					if ($vale['enlace'] == 'S') echo '<option selected value="'.$vale['id'].'">'.$menus.'</option>';
					else echo '<option value="'.$vale['id'].'">'.$menus.'</option>';
				}
			?>
			</select></td>
			<td align="center"><input type="submit" name="Submit" value="<?php echo _FORM_SEND ?>" /></td>
		</tr>
	</form><?php
}
?>
</table>