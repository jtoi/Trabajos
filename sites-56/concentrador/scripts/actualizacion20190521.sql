-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `tbl_rotPasarOperac`;
CREATE TABLE `tbl_rotPasarOperac` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcomercio` int(11) NOT NULL,
  `idpasarela` smallint(3) NOT NULL,
  `idmoneda` char(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `activo` int(11) NOT NULL default 1,
  `fecha` int(11) NOT NULL,
  `cantOperac` int(11) NOT NULL,
  `orden` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idcomercio` (`idcomercio`),
  KEY `idpasarela` (`idpasarela`),
  KEY `idmoneda` (`idmoneda`),
  CONSTRAINT `tbl_rotPasarOperac_ibfk_1` FOREIGN KEY (`idcomercio`) REFERENCES `tbl_comercio` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tbl_rotPasarOperac_ibfk_2` FOREIGN KEY (`idpasarela`) REFERENCES `tbl_pasarela` (`idPasarela`) ON DELETE CASCADE,
  CONSTRAINT `tbl_rotPasarOperac_ibfk_3` FOREIGN KEY (`idmoneda`) REFERENCES `tbl_moneda` (`idmoneda`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci COMMENT='tbl para realizar el cambio de pasarela por comercio - moneda';


CREATE TRIGGER `tbl_rotPasarOperacBI` BEFORE INSERT ON `tbl_rotPasarOperac` FOR EACH ROW
SET new.fecha = UNIX_TIMESTAMP(NOW());

CREATE TRIGGER `tbl_rotPasarOperacBU` BEFORE UPDATE ON `tbl_rotPasarOperac` FOR EACH ROW
SET new.fecha = UNIX_TIMESTAMP(NOW());


insert into tbl_rotPasarOperac values 
(null, 24, 23, '978', 1, null, 1, 1),
(null, 24, 51, '978', 1, null, 1, 2),
(null, 24, 29, '978', 1, null, 1, 3),
(null, 24, 58, '978', 1, null, 2, 4),
(null, 24, 63, '978', 1, null, 1, 5),
(null, 24, 67, '978', 1, null, 2, 6),
(null, 24, 73, '978', 1, null, 1, 7),
(null, 24, 92, '978', 1, null, 1, 8);

INSERT INTO `tbl_menu` VALUES (null, '_MENU_ADMIN_PCANT', 'index.php?componente=comercio&pag=pasOper', '3', '0', NULL, '11');
insert into tbl_accesos values (null, 1, 68, unix_timestamp());

-- 2019-05-21 12:28:28
