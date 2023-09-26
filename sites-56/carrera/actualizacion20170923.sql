ALTER TABLE `participantes`
CHANGE `localidad` `localidad` varchar(150) COLLATE 'latin1_swedish_ci' NOT NULL DEFAULT 'null' AFTER `direccion`,
CHANGE `cp` `cp` varchar(6) COLLATE 'latin1_swedish_ci' NOT NULL DEFAULT 'null' AFTER `localidad`,
CHANGE `telf` `telf` varchar(20) COLLATE 'latin1_swedish_ci' NOT NULL DEFAULT 'null' AFTER `nacionalidad`,
CHANGE `telfm` `telfm` varchar(20) COLLATE 'latin1_swedish_ci' NOT NULL DEFAULT 'null' AFTER `telf`,
CHANGE `correo` `correo` varchar(150) COLLATE 'latin1_swedish_ci' NOT NULL DEFAULT 'null' AFTER `telfm`,
CHANGE `apellidos` `apellidos` varchar(200) COLLATE 'latin1_swedish_ci' NOT NULL DEFAULT 'null' AFTER `nombre`;
ALTER TABLE `representantes`
CHANGE `apellido` `apellido` varchar(150) COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'null' AFTER `nombre`;
INSERT INTO `evento` (`nmombre`)
VALUES ('V Milla Marina Femenina por Equipos');
