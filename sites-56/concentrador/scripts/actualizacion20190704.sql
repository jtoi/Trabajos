create table tbl_rotComPasWeb like tbl_rotComPas;
ALTER TABLE `tbl_rotComPasWeb`
ADD FOREIGN KEY (`idcom`) REFERENCES `tbl_comercio` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
ADD INDEX `idpasarela` (`idpasarela`),
ADD FOREIGN KEY (`idpasarela`) REFERENCES `tbl_pasarela` (`id_Pasarela`) ON DELETE CASCADE ON UPDATE NO ACTION;

alter table tbl_rotComPas
ADD INDEX `idpasarela` (`idpasarela`);
