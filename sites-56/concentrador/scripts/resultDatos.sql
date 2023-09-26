-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `resultDatos`;
CREATE TABLE `resultDatos` (
  `idtransaccion` varchar(14) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `idcomercio` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `identificador` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'idde la transaccion desde el sitio cliente',
  `codigo` varchar(12) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'código devuelto por BBVA',
  `pasarela` smallint(3) NOT NULL DEFAULT '1' COMMENT '1-BBVA, 2-Sabadel3D, 3-BBVA3D, 4-Banesto3D',
  `tipoOperacion` char(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT 'P' COMMENT 'P - pago; D - descuento',
  `idioma` char(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT 'es',
  `fecha` int(11) NOT NULL DEFAULT '0',
  `fecha_mod` int(11) NOT NULL DEFAULT '0',
  `valor` int(11) NOT NULL DEFAULT '0',
  `valor_inicial` int(11) NOT NULL DEFAULT '0',
  `tipoEntorno` char(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT 'D' COMMENT 'D - desarrollo; P - produccion',
  `moneda` char(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '978',
  `estado` char(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT 'P' COMMENT 'P - proceso; A - aceptada; D - denegada; N - no procesada; B - Anulada; V - Devuelta',
  `estadoP` char(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `sesion` varchar(32) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `ip` varchar(17) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '127.0.0.1',
  `tasa` float(14,9) NOT NULL DEFAULT '0.000000000',
  `euroEquiv` float(11,2) NOT NULL DEFAULT '0.00',
  `pago` tinyint(1) NOT NULL DEFAULT '0',
  `tasaDev` float(9,4) NOT NULL DEFAULT '0.0000' COMMENT 'Tasa en el momento de devolucion',
  `euroEquivDev` float(11,2) NOT NULL DEFAULT '0.00' COMMENT 'Valor * tasaDev',
  `solDev` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Solicitud de devolucion: 1- solicitado',
  `amenaza` smallint(5) unsigned NOT NULL DEFAULT '0',
  `repudiada` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'marca cuando una transaccion es repudiada por el dueno de la tarjeta',
  `fechaPagada` int(11) NOT NULL DEFAULT '0',
  `tpv` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 - se realiza desde TPV externo',
  `idpais` int(11) DEFAULT NULL,
  `estadoAMF` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL DEFAULT 'J' COMMENT 'P - proceso; A - aceptada; D - denegada; N - no procesada; B - Anulada; V - Devuelta',
  `tarjetas` varchar(17) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `identificadorBnco` varchar(20) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `id_tarjeta` int(11) DEFAULT '2',
  `mtoMonBnc` int(11) NOT NULL DEFAULT '0' COMMENT 'Monto en la moneda del banco',
  `carDevCom` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0-No se carga la devoluciÃ³n al comercio',
  `idbanco` smallint(6) NOT NULL DEFAULT '1',
  `idempresa` tinyint(4) NOT NULL DEFAULT '1',
  `fecha_act` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2020-02-07 15:08:57
