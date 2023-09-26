set foreign_key_checks=0;
ALTER TABLE `tbl_admin`
ADD `reclamaciones` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-atiende reclamaciones, 0-no las atiende';

update tbl_admin set `reclamaciones` = '1' where idadmin in (10,438);
update `tbl_admin` set `activo` = 'S', `md5` = '4b60fb6818238e0b319b49c321181b7881671081', `fecha_visita` = '1587477323' where idadmin = 438;

INSERT INTO `tbl_menu` (`title`, `link`, `parentid`, `movil`, `mlink`, `orden`)
VALUES ('_MENU_ADMIN_RECLAMA', 'index.php?componente=comercio&pag=reclamaciones', '2', '0', NULL, '9');

update tbl_menu set orden = 10 where id = 43;
update tbl_menu set orden = 11 where id = 48;
update tbl_menu set orden = 12 where id = 66;

#INSERT INTO `tbl_accesos` (`idrol`, `idmenu`, `fecha`) VALUES ('1', '75', unix_timestamp());
insert into tbl_accesos  (select null, idrol, '75', unix_timestamp() from tbl_roles);

DROP TABLE IF EXISTS `tbl_reclamaciones`;
CREATE TABLE `tbl_reclamaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idtransaccion` varchar(14) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `estado` char(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'P' COMMENT 'P-por responder,S-cerrado sin respuesta,C-cerrado con respuesta,R-Reclamada',
  `impReclam` int(11) NOT NULL,
  `fechaNot` int(11) DEFAULT NULL comment 'Fecha en que entra la notificación del banco',
  `fechaLim` int(11) NOT NULL DEFAULT '0' comment 'Fecha límite para enviar al banco',
  `fechaCerr` int(11) NOT NULL DEFAULT '0' comment 'Fecha de Cerrada la reclamación',
  `fechaBan` int(11) NOT NULL DEFAULT '0' comment 'Fecha de respuesta al Banco',
  `fechaRec` int(11) NOT NULL DEFAULT '0' comment 'Fecha Reclamada',
  `motivo` text COLLATE 'latin1_spanish_ci' NULL,
  `documentos` text COLLATE 'latin1_spanish_ci' NULL,
  `subdoc` tinyint(1) not null default '1' comment '1- hay que subir doc, 0-ya está subida',
  PRIMARY KEY (`id`),
  KEY `idtransaccion` (`idtransaccion`),
  CONSTRAINT `tbl_reclamaciones_ibfk_1` FOREIGN KEY (`idtransaccion`) REFERENCES `tbl_transacciones` (`idtransaccion`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

ALTER TABLE `tbl_reclamaciones`
ADD UNIQUE `idtransaccionUK` (`idtransaccion`);

#insert into tbl_reclamaciones (select null, idtransaccion, 'R', (valor_inicial - valor), 0, 0, 0, 0, fecha_mod, null, null, 0 from tbl_transacciones where estado = 'R');
#update tbl_reclamaciones set fechaNot = fechaRec;

DROP TABLE IF EXISTS `tbl_reclamFich`;
CREATE TABLE `tbl_reclamFich` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `idreclama` int(11) NOT NULL,
  `idadmin` int(11) NOT NULL,
  `fichero` varchar(200) COLLATE 'utf8_spanish_ci' NOT NULL,
  `fecha` int NOT NULL,
  FOREIGN KEY (`idreclama`) REFERENCES `tbl_reclamaciones` (`id`) ON DELETE NO ACTION,
  FOREIGN KEY (`idadmin`) REFERENCES `tbl_admin` (`idadmin`) ON DELETE NO ACTION
) ENGINE='InnoDB' COLLATE 'utf8_spanish_ci';

DROP TRIGGER if exists `tbl_reclamFichBI`;
DELIMITER ;;
CREATE TRIGGER `tbl_reclamFichBI` BEFORE INSERT ON `tbl_reclamFich` FOR EACH ROW
if new.fecha is null or new.fecha = 0 then SET new.fecha = UNIX_TIMESTAMP(NOW()); end if;;
DELIMITER ;
set foreign_key_checks=1;


#www/concentrador/admin/template/css/admin.css-+
#www/concentrador/admin/index.php-+
#www/concentrador/admin/componente/comercio/reclamaciones.php+
#www/concentrador/admin/adminis.func.php-+
#www/concentrador/admin/clases/class_tablaHtml.php-+

#Faltan por actualizar estos ficheros
#www/concentrador/admin/componente/core/user.php-+
#www/concentrador/admin/componente/comercio/reclama.php-+
#www/concentrador/admin/componente/comercio/reporte.php-+
#correr la query a continuación para poner a Teresita a ver las reclamaciones
#update tbl_admin set `reclamaciones` = '1' where idadmin in (275);


