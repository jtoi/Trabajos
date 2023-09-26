-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `tbl_colComerLim`;
CREATE TABLE `tbl_colComerLim` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcomercio` int(11) NOT NULL,
  `limxoper` int(11) NOT NULL DEFAULT '-1',
  `limxdia` int(11) NOT NULL DEFAULT '-1',
  `limxmes` int(11) NOT NULL DEFAULT '-1',
  `limxano` int(11) NOT NULL DEFAULT '-1',
  `cantxdia` int(11) NOT NULL DEFAULT '-1',
  `cantxmes` int(11) NOT NULL DEFAULT '-1',
  `cantxano` int(11) NOT NULL DEFAULT '-1',
  `fecha` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idcomercio` (`idcomercio`),
  CONSTRAINT `tbl_colComerLim_ibfk_1` FOREIGN KEY (`idcomercio`) REFERENCES `tbl_comercio` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

INSERT INTO `tbl_colComerLim` (`id`, `idcomercio`, `limxoper`, `limxdia`, `limxmes`, `limxano`, `cantxdia`, `cantxmes`, `cantxano`, `fecha`) VALUES
(1,	184,	-1,	52500,	1050000,	-1,	-1,	-1,	-1,	1582664057),
(2,	1,	-1,	-1,	-1,	-1,	-1,	-1,	-1,	1582664057);

-- 2020-02-27 11:52:40
