DELETE FROM `tbl_destinatario`
WHERE `idcorreo` = '25' ;
insert into tbl_destinatario (idcorreo, idadmin, para, fecha) values
(25, 275, '', unix_timestamp()),
(25, 301, '', unix_timestamp()),
(25, 523, '', unix_timestamp()),
(25, 1406, '', unix_timestamp());

insert into tbl_accesos (idrol, idmenu, fecha) values ('11', '62', unix_timestamp());
