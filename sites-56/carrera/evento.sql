-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `evento`;
CREATE TABLE `evento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nmombre` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `evento` (`id`, `nmombre`) VALUES
(1,	'Milla Internacional'),
(2,	'Villa de Bilbao'),
(3,	'8va. Milla Internacional 2013'),
(4,	'Villa de Bilbao / 2013'),
(5,	'1ra Milla Marina Femenina'),
(6,	'Escuela de Atletismo de Bilbao'),
(7,	'Campus de Navidad'),
(8,	'9na. Milla Internacional 2014'),
(9,	'Campus de Verano'),
(10,	'Villa de Bilbao 2014'),
(11,	'Escuela 2014 - 2015'),
(12,	'10ma. Milla Internacional 2015'),
(13,	'Villa de Bilbao 2015'),
(14,	'Escuela 2015 - 2016'),
(15,	'Milla Femenina 2015'),
(16,	'XI Milla Internacional de Bilbao 2016'),
(17,	'Bilbao Runners 2016'),
(18,	'III Campus de Verano'),
(19,	'XVI Villa de Bilbao 2016'),
(20,	'Escuela de Atletismo de Bilbao 2016/17'),
(21,	'XII Milla Inter. de Bilbao 2017'),
(22,	'IV Campus de Verano'),
(23,	'XVII Reunión Internacional Villa de Bilbao 2017'),
(24,	'Escuela de Atletismo Bilbao 2017/18'),
(25,	'V Milla Marina Femenina por Equipos'),
(26,	'XIII Milla Internacional de Bilbao'),
(27,	'V Campus de Verano'),
(28,	'Escuela de Atletismo Bilbao 2018/19'),
(29,	'Milla Marina Femenina 2018'),
(30,	'XIV Milla Internacional de Bilbao 2019'),
(31,	'VI Campus de Verano'),
(32,	'Escuela de Atletismo Bilbao 2019/2020'),
(33,	'Milla Marina Femenina 2019'),
(34,	'XV Milla Internacional de Bilbao 2020');

-- 2020-02-15 05:56:28
