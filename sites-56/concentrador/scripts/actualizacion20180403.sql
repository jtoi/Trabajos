DROP TABLE IF EXISTS `tbl_operacEuro`;
CREATE TABLE `tbl_operacEuro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identificador` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `idcomercio` varchar(20) collate utf8_spanish_ci not null,
  `idmoneda` char(3) COLLATE utf8_spanish_ci NOT NULL,
  `tasa` float(14,4) not null,
  `monto` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idmoneda` (`idmoneda`),
  KEY `identificador` (`identificador`),
  KEY `idcomercio` (`idcomercio`),
  CONSTRAINT `tbl_operacEuro_ibfk_1` FOREIGN KEY (`idmoneda`) REFERENCES `tbl_moneda` (`idmoneda`) ON DELETE NO ACTION,
  CONSTRAINT `tbl_operacEuro_ibfk_2` FOREIGN KEY (`idcomercio`) REFERENCES `tbl_comercio` (`idcomercio`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='operaciones en divisas convertidas a Euros';

