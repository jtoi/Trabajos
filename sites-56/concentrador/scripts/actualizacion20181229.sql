INSERT INTO `tbl_menu` (`title`, `link`, `parentid`, `movil`, `mlink`, `orden`)
SELECT '_MENU_ADMIN_CAMBIO', 'index.php?componente=comercio&pag=datoso', '4', '1', '', '9'
FROM `tbl_menu`
WHERE ((`id` = '25'));
INSERT INTO `tbl_accesos` (`idrol`, `idmenu`, `fecha`)
VALUES ('1', '64', '1546093067');
