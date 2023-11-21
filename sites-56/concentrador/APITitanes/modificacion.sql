ALTER TABLE `tit_TipoDocumento`
ADD `defecto` smallint(1) NOT NULL DEFAULT '0';

update `tit_TipoDocumento` set defecto = 1 where id in (1,2,3,4,5,6,7,8,9,10,11,12,25,29)
