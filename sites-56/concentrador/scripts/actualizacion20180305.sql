DROP TABLE IF EXISTS `tbl_tasaComercio`;
CREATE TABLE `tbl_tasaComercio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcomercio` int(11) NOT NULL,
  `idadmin` int(11) NOT NULL,
  `monedaBas` char(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL COMMENT 'moneda base del cambio',
  `tasa` float(9,4) NOT NULL,
  `monedaCamb` char(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '978' COMMENT 'moneda a obtener del cambio',
  `fecha` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idcomercio` (`idcomercio`),
  KEY `monedaBas` (`monedaCamb`),
  KEY `monedaCamb` (`monedaBas`),
  KEY `fecha` (`fecha`),
  KEY `idadmin` (`idadmin`),
  CONSTRAINT `tbl_cambioEUR_ibfk_1` FOREIGN KEY (`idcomercio`) REFERENCES `tbl_comercio` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_cambioEUR_ibfk_2` FOREIGN KEY (`idadmin`) REFERENCES `tbl_admin` (`idadmin`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_cambioEUR_ibfk_4` FOREIGN KEY (`monedaCamb`) REFERENCES `tbl_moneda` (`idmoneda`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_cambioEUR_ibfk_5` FOREIGN KEY (`monedaBas`) REFERENCES `tbl_moneda` (`idmoneda`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Por cada moneda base cuanto vamos a obtener en la moneda de cambio';

ALTER TABLE `tbl_comercio`
ADD `operEur` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1- puede cambiar operaciones en divisas a euros, 0- no puede';

INSERT INTO `tbl_menu` (`title`, `link`, `parentid`, `movil`, `mlink`, `orden`)
VALUES ('_MENU_ADMIN_CAMBIOUSD', 'index.php?componente=comercio&pag=cambioeur', '3', '0', NULL, '2');

INSERT INTO `tbl_accesos` (`idrol`, `idmenu`, `fecha`)
VALUES ('1', '63', '1522175003');
