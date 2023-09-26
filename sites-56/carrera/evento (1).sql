-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 06-06-2023 a las 13:16:12
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
-- Estructura de tabla para la tabla `evento`
--

DROP TABLE IF EXISTS `evento`;
CREATE TABLE IF NOT EXISTS `evento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nmombre` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `evento`
--

INSERT INTO `evento` (`id`, `nmombre`) VALUES
(1, 'Milla Internacional'),
(2, 'Villa de Bilbao'),
(3, '8va. Milla Internacional 2013'),
(4, 'Villa de Bilbao / 2013'),
(5, '1ra Milla Marina Femenina'),
(6, 'Escuela de Atletismo de Bilbao'),
(7, 'Campus de Navidad'),
(8, '9na. Milla Internacional 2014'),
(9, 'Campus de Verano'),
(10, 'Villa de Bilbao 2014'),
(11, 'Escuela 2014 - 2015'),
(12, '10ma. Milla Internacional 2015'),
(13, 'Villa de Bilbao 2015'),
(14, 'Escuela 2015 - 2016'),
(15, 'Milla Femenina 2015'),
(16, 'XI Milla Internacional de Bilbao 2016'),
(17, 'Bilbao Runners 2016'),
(18, 'III Campus de Verano'),
(19, 'XVI Villa de Bilbao 2016'),
(20, 'Escuela de Atletismo de Bilbao 2016/17'),
(21, 'XII Milla Inter. de Bilbao 2017'),
(22, 'IV Campus de Verano'),
(23, 'XVII Reunión Internacional Villa de Bilbao 2017'),
(24, 'Escuela de Atletismo Bilbao 2017/18'),
(25, 'V Milla Marina Femenina por Equipos'),
(26, 'XIII Milla Internacional de Bilbao'),
(27, 'V Campus de Verano'),
(28, 'Escuela de Atletismo Bilbao 2018/19'),
(29, 'Milla Marina Femenina 2018'),
(30, 'XIV Milla Internacional de Bilbao 2019'),
(31, 'VI Campus de Verano'),
(32, 'Escuela de Atletismo Bilbao 2019/2020'),
(33, 'Otro'),
(34, 'Mas'),
(35, 'Escuela Bilbao Atletismo 2020_2021'),
(36, 'Mas'),
(37, 'Escuela Bilbao Atletismo 2021_2022'),
(38, 'Escuela Bilbao Atletismo 2022_2023'),
(39, 'Escuela Bilbao Atletismo 2023_2024');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
