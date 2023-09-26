-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 06-06-2023 a las 13:17:16
-- Versión del servidor: 10.3.39-MariaDB
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bilbaoatletismo_form`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

DROP TABLE IF EXISTS `equipo`;
CREATE TABLE IF NOT EXISTS `equipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `fecha` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participantes`
--

DROP TABLE IF EXISTS `participantes`;
CREATE TABLE IF NOT EXISTS `participantes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idevento` int(11) NOT NULL DEFAULT 1,
  `tipoDoc` varchar(12) NOT NULL DEFAULT 'null',
  `doc` varchar(32) NOT NULL DEFAULT 'null',
  `tipoDocTutor` varchar(12) NOT NULL DEFAULT 'null',
  `docTutor` varchar(32) NOT NULL DEFAULT 'null',
  `idprueba` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'T-txpete, P-prebenjamin, I-infantil, A-abierta',
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
  `observaciones` text DEFAULT NULL,
  `fechaInsc` int(11) DEFAULT NULL,
  `idequipo` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idevento` (`idevento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prueba`
--

DROP TABLE IF EXISTS `prueba`;
CREATE TABLE IF NOT EXISTS `prueba` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idevento` int(11) NOT NULL DEFAULT 1,
  `sexo` char(1) NOT NULL DEFAULT 'M',
  `nombre` varchar(150) NOT NULL,
  `corto` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros`
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representantes`
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `iduser` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id de usuario',
  `Nombre` varchar(150) NOT NULL COMMENT 'nombre del usuario',
  `user` varchar(30) NOT NULL COMMENT 'logn de usuario',
  `clave` varchar(40) NOT NULL COMMENT 'clave de usuario',
  `email` varchar(50) NOT NULL COMMENT 'email del cliente',
  `tipo` int(1) NOT NULL DEFAULT 2,
  PRIMARY KEY (`iduser`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
