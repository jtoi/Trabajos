-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `tbl_token`;
CREATE TABLE `tbl_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmenu` smallint(6) NOT NULL,
  `token` varchar(256) COLLATE utf8_spanish2_ci NOT NULL,
  `fecha` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idmenu` (`idmenu`),
  CONSTRAINT `tbl_token_ibfk_1` FOREIGN KEY (`idmenu`) REFERENCES `tbl_menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;


-- 2018-09-05 13:44:06
