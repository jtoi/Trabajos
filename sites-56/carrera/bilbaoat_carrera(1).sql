-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 02, 2019 at 12:05 PM
-- Server version: 5.5.61-cll
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bilbaoat_carrera`
--

-- --------------------------------------------------------

--
-- Table structure for table `equipo`
--

DROP TABLE IF EXISTS `equipo`;
CREATE TABLE IF NOT EXISTS `equipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `fecha` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=509 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `evento`
--

DROP TABLE IF EXISTS `evento`;
CREATE TABLE IF NOT EXISTS `evento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nmombre` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `evento`
--

INSERT INTO `evento` VALUES(1, 'Milla Internacional');
INSERT INTO `evento` VALUES(2, 'Villa de Bilbao');
INSERT INTO `evento` VALUES(3, '8va. Milla Internacional 2013');
INSERT INTO `evento` VALUES(4, 'Villa de Bilbao / 2013');
INSERT INTO `evento` VALUES(5, '1ra Milla Marina Femenina');
INSERT INTO `evento` VALUES(6, 'Escuela de Atletismo de Bilbao');
INSERT INTO `evento` VALUES(7, 'Campus de Navidad');
INSERT INTO `evento` VALUES(8, '9na. Milla Internacional 2014');
INSERT INTO `evento` VALUES(9, 'Campus de Verano');
INSERT INTO `evento` VALUES(10, 'Villa de Bilbao 2014');
INSERT INTO `evento` VALUES(11, 'Escuela 2014 - 2015');
INSERT INTO `evento` VALUES(12, '10ma. Milla Internacional 2015');
INSERT INTO `evento` VALUES(13, 'Villa de Bilbao 2015');
INSERT INTO `evento` VALUES(14, 'Escuela 2015 - 2016');
INSERT INTO `evento` VALUES(15, 'Milla Femenina 2015');
INSERT INTO `evento` VALUES(16, 'XI Milla Internacional de Bilbao 2016');
INSERT INTO `evento` VALUES(17, 'Bilbao Runners 2016');
INSERT INTO `evento` VALUES(18, 'III Campus de Verano');
INSERT INTO `evento` VALUES(19, 'XVI Villa de Bilbao 2016');
INSERT INTO `evento` VALUES(20, 'Escuela de Atletismo de Bilbao 2016/17');
INSERT INTO `evento` VALUES(21, 'XII Milla Inter. de Bilbao 2017');
INSERT INTO `evento` VALUES(22, 'IV Campus de Verano');
INSERT INTO `evento` VALUES(23, 'XVII Reunión Internacional Villa de Bilbao 2017');
INSERT INTO `evento` VALUES(24, 'Escuela de Atletismo Bilbao 2017/18');
INSERT INTO `evento` VALUES(25, 'V Milla Marina Femenina por Equipos');
INSERT INTO `evento` VALUES(26, 'XIII Milla Internacional de Bilbao');
INSERT INTO `evento` VALUES(27, 'V Campus de Verano');
INSERT INTO `evento` VALUES(28, 'Escuela de Atletismo Bilbao 2018/19');
INSERT INTO `evento` VALUES(29, 'Milla Marina Femenina 2018');
INSERT INTO `evento` VALUES(30, 'XIV Milla Internacional de Bilbao 2019');

-- --------------------------------------------------------

--
-- Table structure for table `participantes`
--

DROP TABLE IF EXISTS `participantes`;
CREATE TABLE IF NOT EXISTS `participantes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idevento` int(11) NOT NULL DEFAULT '1',
  `tipoDoc` varchar(12) NOT NULL DEFAULT 'null',
  `doc` varchar(32) NOT NULL DEFAULT 'null',
  `tipoDocTutor` varchar(12) NOT NULL DEFAULT 'null',
  `docTutor` varchar(32) NOT NULL DEFAULT 'null',
  `idprueba` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'T-txpete, P-prebenjamin, I-infantil, A-abierta',
  `licencia` varchar(60) DEFAULT NULL,
  `atleta` char(1) NOT NULL DEFAULT 'S',
  `nombre` varchar(150) NOT NULL,
  `apellidos` varchar(200) NOT NULL DEFAULT 'null',
  `sexo` char(1) NOT NULL DEFAULT 'M',
  `fnac` date NOT NULL,
  `direccion` varchar(300) DEFAULT NULL,
  `localidad` varchar(150) NOT NULL DEFAULT 'null',
  `cp` varchar(6) NOT NULL DEFAULT 'null',
  `provincia` varchar(60) NOT NULL DEFAULT 'null',
  `pais` varchar(120) NOT NULL DEFAULT 'null',
  `nacionalidad` varchar(120) NOT NULL DEFAULT 'null',
  `telf` varchar(20) NOT NULL DEFAULT 'null',
  `telfm` varchar(20) NOT NULL DEFAULT 'null',
  `correo` varchar(150) NOT NULL DEFAULT 'null',
  `club` varchar(300) DEFAULT NULL,
  `licencia_num` varchar(150) DEFAULT NULL,
  `carnet` varchar(50) NOT NULL DEFAULT 'null',
  `pin` varchar(50) NOT NULL DEFAULT 'null',
  `observaciones` text,
  `fechaInsc` int(11) DEFAULT NULL,
  `idequipo` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idevento` (`idevento`)
) ENGINE=MyISAM AUTO_INCREMENT=11041 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prueba`
--

DROP TABLE IF EXISTS `prueba`;
CREATE TABLE IF NOT EXISTS `prueba` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idevento` int(11) NOT NULL DEFAULT '1',
  `sexo` char(1) NOT NULL DEFAULT 'M',
  `nombre` varchar(150) NOT NULL,
  `corto` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=105 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prueba`
--

INSERT INTO `prueba` VALUES(1, 1, 'M', 'Txupete (2004 y posteriores)', 'Txupete');
INSERT INTO `prueba` VALUES(2, 1, 'M', 'Pre-benjamin, Benjamin', 'Pre-benjamin, Benjamin');
INSERT INTO `prueba` VALUES(3, 1, 'M', 'Alevin', 'Alevin');
INSERT INTO `prueba` VALUES(4, 1, 'M', 'Infantil y Cadete', 'Infantil y Cadete');
INSERT INTO `prueba` VALUES(5, 1, 'M', 'Abierta', 'Abierta');
INSERT INTO `prueba` VALUES(6, 1, 'M', 'Veteran@s Federados', 'Veteran@s Federados');
INSERT INTO `prueba` VALUES(7, 1, 'M', 'Federad@s Elite', 'Federad@s Elite');
INSERT INTO `prueba` VALUES(8, 2, 'M', '100m', '');
INSERT INTO `prueba` VALUES(9, 2, 'M', '200m', '');
INSERT INTO `prueba` VALUES(10, 2, 'M', '400m', '');
INSERT INTO `prueba` VALUES(11, 2, 'M', '800m', '');
INSERT INTO `prueba` VALUES(12, 2, 'M', '1 500m', '');
INSERT INTO `prueba` VALUES(13, 2, 'M', '5 000m', '');
INSERT INTO `prueba` VALUES(14, 2, 'M', 'Longitud', '');
INSERT INTO `prueba` VALUES(15, 2, 'M', 'Triple', '');
INSERT INTO `prueba` VALUES(16, 2, 'F', '200m', '');
INSERT INTO `prueba` VALUES(17, 2, 'F', '800m', '');
INSERT INTO `prueba` VALUES(18, 2, 'F', '5 000m', '');
INSERT INTO `prueba` VALUES(19, 2, 'F', 'Triple', '');
INSERT INTO `prueba` VALUES(20, 2, 'M', '1000m Alevin', '');
INSERT INTO `prueba` VALUES(21, 2, 'F', '1000m Alevin', '');
INSERT INTO `prueba` VALUES(22, 2, 'M', '600m Infantil y Cadete', '');
INSERT INTO `prueba` VALUES(23, 2, 'F', '600m Infantil y Cadete', '');
INSERT INTO `prueba` VALUES(24, 3, 'M', 'Txupete', 'Txupete');
INSERT INTO `prueba` VALUES(25, 3, 'M', 'Pre-Benjamín', 'Pre-Benjamín');
INSERT INTO `prueba` VALUES(26, 3, 'M', 'Benjamín', 'Benjamín');
INSERT INTO `prueba` VALUES(27, 3, 'M', 'Alevin', 'Alevin');
INSERT INTO `prueba` VALUES(28, 3, 'M', 'Infantil y Cadete', 'Infantil y Cadete');
INSERT INTO `prueba` VALUES(29, 3, 'M', 'Juvenil Junior', 'Juvenil Junior');
INSERT INTO `prueba` VALUES(30, 3, 'M', 'Abierta Fem', 'Abierta Fem');
INSERT INTO `prueba` VALUES(31, 3, 'M', 'Abierta Masc', 'Abierta Masc');
INSERT INTO `prueba` VALUES(32, 3, 'M', 'Veteranos Federados', 'Veteranos Federados');
INSERT INTO `prueba` VALUES(33, 3, 'M', 'Veteranos Elite Fem.', 'Veteranos Elite Fem.');
INSERT INTO `prueba` VALUES(34, 3, 'M', 'Veteranos Elite Masc.', 'Veteranos Elite Masc.');
INSERT INTO `prueba` VALUES(35, 4, 'M', '100m', '');
INSERT INTO `prueba` VALUES(36, 4, 'M', '200m', '');
INSERT INTO `prueba` VALUES(37, 4, 'M', '800m', '');
INSERT INTO `prueba` VALUES(38, 4, 'M', '1 500m', '');
INSERT INTO `prueba` VALUES(39, 4, 'M', '5 000m', '');
INSERT INTO `prueba` VALUES(40, 4, 'M', 'Triple', '');
INSERT INTO `prueba` VALUES(41, 4, 'M', 'Pertiga', '');
INSERT INTO `prueba` VALUES(42, 4, 'F', '200m', '');
INSERT INTO `prueba` VALUES(43, 4, 'F', '800m', '');
INSERT INTO `prueba` VALUES(44, 4, 'F', '5 000m', '');
INSERT INTO `prueba` VALUES(45, 4, 'F', 'Triple', '');
INSERT INTO `prueba` VALUES(46, 4, 'F', 'Pertiga', '');
INSERT INTO `prueba` VALUES(47, 4, 'M', '600m Benj/Alevín', '');
INSERT INTO `prueba` VALUES(48, 4, 'M', '600m Inf/ Cad', '');
INSERT INTO `prueba` VALUES(49, 4, 'F', '600m Benj/Alevín', '');
INSERT INTO `prueba` VALUES(50, 4, 'F', '600m Inf/ Cad', '');
INSERT INTO `prueba` VALUES(51, 6, 'M', 'Pre-benjamin', '');
INSERT INTO `prueba` VALUES(52, 6, 'M', 'Benjamin', '');
INSERT INTO `prueba` VALUES(53, 6, 'M', 'Alevin', '');
INSERT INTO `prueba` VALUES(54, 6, 'M', 'Infantil', '');
INSERT INTO `prueba` VALUES(55, 7, 'M', '2000', '');
INSERT INTO `prueba` VALUES(56, 7, 'M', '2001', '');
INSERT INTO `prueba` VALUES(57, 7, 'M', '2002', '');
INSERT INTO `prueba` VALUES(58, 7, 'M', '2003', '');
INSERT INTO `prueba` VALUES(59, 7, 'M', '2004', '');
INSERT INTO `prueba` VALUES(60, 7, 'M', '2005', '');
INSERT INTO `prueba` VALUES(61, 7, 'M', '2006', '');
INSERT INTO `prueba` VALUES(62, 7, 'M', '2007', '');
INSERT INTO `prueba` VALUES(63, 8, 'M', 'Txupete', 'Txupete');
INSERT INTO `prueba` VALUES(64, 8, 'M', 'Pre-Benjamín', 'Pre-Benjamín');
INSERT INTO `prueba` VALUES(65, 8, 'M', 'Benjamín', 'Benjamín');
INSERT INTO `prueba` VALUES(66, 8, 'M', 'Alevin', 'Alevin');
INSERT INTO `prueba` VALUES(67, 8, 'M', 'Infantil y Cadete', 'Infantil y Cadete');
INSERT INTO `prueba` VALUES(68, 8, 'M', 'Juvenil Junior', 'Juvenil Junior');
INSERT INTO `prueba` VALUES(69, 8, 'M', 'Abierta', 'Abierta');
INSERT INTO `prueba` VALUES(70, 8, 'M', 'Veteranos Federados', 'Veteranos Federados');
INSERT INTO `prueba` VALUES(71, 8, 'M', 'Veteranos Elite', 'Veteranos Elite');
INSERT INTO `prueba` VALUES(72, 9, 'M', '2000', '2000');
INSERT INTO `prueba` VALUES(73, 9, 'M', '2001', '2001');
INSERT INTO `prueba` VALUES(74, 9, 'M', '2002', '2002');
INSERT INTO `prueba` VALUES(75, 9, 'M', '2003', '2003');
INSERT INTO `prueba` VALUES(76, 9, 'M', '2004', '2004');
INSERT INTO `prueba` VALUES(77, 9, 'M', '2005', '2005');
INSERT INTO `prueba` VALUES(78, 9, 'M', '2006', '2006');
INSERT INTO `prueba` VALUES(79, 9, 'M', '2007', '2007');
INSERT INTO `prueba` VALUES(80, 10, 'M', '100m', '');
INSERT INTO `prueba` VALUES(81, 10, 'M', '200m', '');
INSERT INTO `prueba` VALUES(82, 10, 'M', '600m Benjamin/Alevin', '');
INSERT INTO `prueba` VALUES(83, 10, 'M', '600m Infantil/Cadete', '');
INSERT INTO `prueba` VALUES(84, 10, 'M', '800m', '');
INSERT INTO `prueba` VALUES(85, 10, 'M', '1500m', '');
INSERT INTO `prueba` VALUES(86, 10, 'M', '3000m', '');
INSERT INTO `prueba` VALUES(87, 10, 'M', '3000m obst', '');
INSERT INTO `prueba` VALUES(88, 10, 'M', 'Triple', '');
INSERT INTO `prueba` VALUES(89, 10, 'F', '200m', '');
INSERT INTO `prueba` VALUES(90, 10, 'F', '600m Benjamin/Alevin', '');
INSERT INTO `prueba` VALUES(91, 10, 'F', '600m Infantil/Cadete', '');
INSERT INTO `prueba` VALUES(92, 10, 'F', '800m', '');
INSERT INTO `prueba` VALUES(93, 10, 'F', '3000m', '');
INSERT INTO `prueba` VALUES(94, 10, 'F', '3000m obst', '');
INSERT INTO `prueba` VALUES(95, 10, 'F', 'Triple', '');
INSERT INTO `prueba` VALUES(96, 10, 'F', 'P?rtiga', '');
INSERT INTO `prueba` VALUES(97, 6, 'M', 'Cadete', '');
INSERT INTO `prueba` VALUES(98, 9, 'M', '2008', '2008');
INSERT INTO `prueba` VALUES(99, 14, 'M', 'Prebenjamin', '');
INSERT INTO `prueba` VALUES(100, 14, 'M', 'Benjamin', '');
INSERT INTO `prueba` VALUES(101, 14, 'M', 'Alevin', '');
INSERT INTO `prueba` VALUES(102, 14, 'M', 'Infantil', '');
INSERT INTO `prueba` VALUES(103, 14, 'M', 'Cadete', '');
INSERT INTO `prueba` VALUES(104, 14, 'M', 'Juvenil', '');

-- --------------------------------------------------------

--
-- Table structure for table `registros`
--

DROP TABLE IF EXISTS `registros`;
CREATE TABLE IF NOT EXISTS `registros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idparticipante` int(11) NOT NULL,
  `idprueba` int(11) NOT NULL,
  `reg1` varchar(100) NOT NULL,
  `reg2` varchar(100) NOT NULL,
  `reg3` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1253 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `representantes`
--

DROP TABLE IF EXISTS `representantes`;
CREATE TABLE IF NOT EXISTS `representantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idparticipante` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `apellido` varchar(150) NOT NULL DEFAULT 'null',
  `correo` varchar(150) NOT NULL,
  `tel` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3096 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `iduser` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id de usuario',
  `Nombre` varchar(150) NOT NULL COMMENT 'nombre del usuario',
  `user` varchar(30) NOT NULL COMMENT 'logn de usuario',
  `clave` varchar(40) NOT NULL COMMENT 'clave de usuario',
  `email` varchar(50) NOT NULL COMMENT 'email del cliente',
  `tipo` int(1) NOT NULL DEFAULT '2',
  PRIMARY KEY (`iduser`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES(1, 'Alex Carneros', 'inscripcion', 'b233a63b23ff18e52c39e4896f7edfbb', 'alex.carneros@amfglobalitems.com', 1);
INSERT INTO `users` VALUES(4, 'ANE', 'ANEPIEDRITA', '88e0e3d299a8230f61023ea22f3057a2', 'HARRIZAN@HOTMAIL.COM', 1);
INSERT INTO `users` VALUES(5, 'JULIO', 'JULIOJOTATE', 'b67d89ee41b1868e7f2da80734033093', 'jotate@amfglobalitems.com', 1);
INSERT INTO `users` VALUES(6, 'Julio Toirac', 'jtoirac', '8840b7671172d82c2bb525f645be974d', 'jtoirac@gmail.com', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
