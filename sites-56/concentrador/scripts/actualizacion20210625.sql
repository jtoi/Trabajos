-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `tbl_referencia`;
CREATE TABLE `tbl_referencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtransaccion` varchar(14) COLLATE utf8_spanish_ci NOT NULL,
  `codBanco` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `codConc` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codConc` (`codConc`),
  KEY `idtransaccion` (`idtransaccion`),
  CONSTRAINT `tbl_referencia_ibfk_2` FOREIGN KEY (`idtransaccion`) REFERENCES `tbl_transacciones` (`idtransaccion`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


-- 2021-06-25 16:00:31
