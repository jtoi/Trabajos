ALTER TABLE `tbl_comercio`
ADD `idmonedaPago` CHAR(3) NOT NULL DEFAULT '978' COMMENT 'Moneda con la que Bidaiondo paga al comercio'
AFTER `pagoxRef`,
	ADD INDEX `monedaPago_IDX` (`idmonedaPago`);