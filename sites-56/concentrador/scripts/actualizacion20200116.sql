ALTER TABLE `tbl_admin`
ADD `telefono` varchar(10) COLLATE 'latin1_spanish_ci' NULL;

CREATE TABLE `tbl_sms` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nombre` varchar(100) COLLATE 'latin1_spanish_ci' NOT NULL,
  `observaciones` varchar(250) COLLATE 'latin1_spanish_ci' NULL
) ENGINE='InnoDB' COLLATE 'latin1_spanish_ci';

CREATE TABLE `tbl_colSmsAdmin` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `idadmin` int(11) NOT NULL,
  `idsms` int(11) NOT NULL,
  `fecha` int NOT NULL,
  FOREIGN KEY (`idadmin`) REFERENCES `tbl_admin` (`idadmin`) ON DELETE CASCADE,
  FOREIGN KEY (`idsms`) REFERENCES `tbl_sms` (`id`) ON DELETE CASCADE
) ENGINE='InnoDB' COLLATE 'latin1_spanish_ci';

update tbl_admin set telefono = '5352738723' where idadmin = 10;
update tbl_admin set telefono = '5353159964' where idadmin = 275;
update tbl_admin set telefono = '5352543704' where idadmin = 523;
update tbl_admin set telefono = '5358049134' where idadmin = 301;
update tbl_admin set telefono = '5354136487' where idadmin = 396;

insert into tbl_sms values (null,'Tickets','Envío del sistema de tickets del Concentrador');

insert into tbl_colSmsAdmin values (null, 10, 1, unix_timestamp());
insert into tbl_colSmsAdmin values (null, 275, 1, unix_timestamp());
insert into tbl_colSmsAdmin values (null, 523, 1, unix_timestamp());
insert into tbl_colSmsAdmin values (null, 301, 1, unix_timestamp());
