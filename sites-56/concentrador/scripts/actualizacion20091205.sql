update tbl_ticket set fechaModificada = fechaEntrada where fechaModificada is null;
insert into tbl_setup values (null, 'venceTicket', '3');
ALTER TABLE `tbl_admin` ADD `TimeZone` VARCHAR( 100 ) NOT NULL DEFAULT 'Europe/Madrid';

