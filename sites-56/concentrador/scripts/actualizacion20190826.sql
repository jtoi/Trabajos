DROP TABLE IF EXISTS `tbl_adminSO`;
CREATE TABLE `tbl_adminSO` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idadmin` int(11) NOT NULL,
  `fecha` int(11) NOT NULL,
  `so` varchar(200) COLLATE latin1_spanish_ci NOT NULL,
  `browser` varchar(200) COLLATE latin1_spanish_ci NOT NULL,
  `ip` varchar(20) COLLATE latin1_spanish_ci NOT NULL,
  `idpais` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idadmin` (`idadmin`),
  KEY `fecha` (`fecha`),
  KEY `idpais` (`idpais`),
  CONSTRAINT `tbl_adminSO_ibfk_1` FOREIGN KEY (`idadmin`) REFERENCES `tbl_admin` (`idadmin`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `tbl_adminSO_ibfk_2` FOREIGN KEY (`idpais`) REFERENCES `tbl_paises` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
