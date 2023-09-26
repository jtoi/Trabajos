ALTER TABLE `tbl_transacciones`
ADD `carDevCom` tinyint NOT NULL DEFAULT '1' COMMENT '0-No se carga la devolución al comercio';
ALTER TABLE `tbl_transaccionesOld`
ADD `carDevCom` tinyint NOT NULL DEFAULT '1' COMMENT '0-No se carga la devolución al comercio';
