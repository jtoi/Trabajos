INSERT INTO `tbl_menu` (`id`, `title`, `link`, `parentid`, `movil`, `mlink`, `orden`) VALUES
(81, '_MENU_ADMIN_TRAZA', 'index.php?componente=comercio&pag=traza', 3, 0, NULL, 15);
delete from tbl_menu where id=64;
update tbl_admin set idrol=1 where idadmin in (2151, 2150);
update tbl_roles set nombre='Desarrolladores', caract = 'Desarrolladores de la empresa' where idrol = 1;
INSERT INTO `tbl_accesos` (`id`, `idrol`, `idmenu`, `fecha`) VALUES (NULL, '1', '81', '1664955389');
delete from tbl_accesos where id in (2587,	2589);
