INSERT INTO tbl_menu (title, link, parentid, movil, mlink, orden)
SELECT '_MENU_ADMIN_CLONPAS', 'index.php?componente=comercio&pag=clonpas', '6', '0', NULL, '4'
FROM tbl_menu
WHERE ((id = '22'));

INSERT INTO tbl_accesos (idrol, idmenu, fecha)
SELECT '1', '71', '1579804631'
FROM tbl_accesos
WHERE ((id = '1'));

UPDATE tbl_menu SET orden = '0' WHERE id = '21';
UPDATE tbl_menu SET orden = '1' WHERE id = '22';
UPDATE tbl_menu SET orden = '2' WHERE id = '63';
UPDATE tbl_menu SET orden = '3' WHERE id = '42';
UPDATE tbl_menu SET orden = '4' WHERE id = '44';
UPDATE tbl_menu SET orden = '5' WHERE id = '46';
UPDATE tbl_menu SET orden = '6' WHERE id = '47';
UPDATE tbl_menu SET orden = '7' WHERE id = '57';
UPDATE tbl_menu SET orden = '8' WHERE id = '71';
UPDATE tbl_menu SET orden = '9' WHERE id = '59';
UPDATE tbl_menu SET orden = '10' WHERE id = '60';
UPDATE tbl_menu SET orden = '11' WHERE id = '61';
UPDATE tbl_menu SET orden = '12' WHERE id = '68';
