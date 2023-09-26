ALTER TABLE `tbl_aisBeneficiario`
ADD `bloq` tinyint NOT NULL DEFAULT '0' COMMENT '0- puede recibir 1- nunca podrá recibir dinero';

ALTER TABLE `tbl_aisCliente`
ADD `bloq` tinyint NOT NULL DEFAULT '0' COMMENT '0- puede enviar 1- nunca podrá enviar dinero';
