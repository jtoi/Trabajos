insert into tbl_menu (title, link, parentid, orden) values ('_MENU_ADMIN_COMPAS', 'index.php?componente=comercio&pag=pascom', '3','7');
update tbl_menu set orden = '8' where id = 57;
update tbl_menu set orden = '9' where id = 71;
update tbl_menu set orden = '10' where id = 59;
update tbl_menu set orden = '11' where id = 60;
update tbl_menu set orden = '12' where id = 61;
update tbl_menu set orden = '13' where id = 68;
update tbl_menu set orden = '14' where id = 77;

insert into tbl_accesos (idrol, idmenu, fecha) values (1, 78, unix_timestamp());
