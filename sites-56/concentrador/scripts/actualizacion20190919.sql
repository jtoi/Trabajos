INSERT INTO `tbl_pasarela` (`nombre`, `tipo`, `cuenta`, `datos`, `fecha`, `activo`, `comercio`, `imagen`, `idcenauto`, `estado`, `secure`, `idbanco`, `devolucion`, `idempresa`, `LimMinOper`, `LimMaxOper`, `LimDiar`, `LimMens`, `LimAnual`, `LimOperIpDia`, `LimOperTarDia`, `LimOperDia`, `coefImporta`, `idagencia`, `amex`, `observacion`, `fechamod`, `usdxamex`, `pasarLim`)
VALUES ('Ingeniero 3D', 'P', NULL, 'pasoA@firmaX', unix_timestamp(), '1', 'Bidaiondo', NULL, '17', 'D', '1', '34', '0', '1', '0', '100000000', '100000000', '100000000', '100000000', '100', '100', '500', '1.00', '5', '0', NULL, '1568907324', '0', '0');
INSERT INTO `tbl_colPasarMon` (`idpasarela`, `idmoneda`, `terminal`, `clave`, `comercio`, `fecha`, `datos`, `estado`)
VALUES ('116', '978', '001', '0HS0dSet/7Xj2rqk+uFGmIlPG160JiYN', '185012515', unix_timestamp(), '', '1')
INSERT INTO `tbl_colPasarMon` (`idpasarela`, `idmoneda`, `terminal`, `clave`, `comercio`, `fecha`, `datos`, `estado`)
VALUES ('116', '840', '002', 'UlCvALnwyGB0oAvA/gtj7/kr7WuKrdxX', '185012515', unix_timestamp(), '', '1');
INSERT INTO `tbl_colTarjPasar` (`idTarj`, `idPasar`)
VALUES ('2', '116');
INSERT INTO `tbl_colTarjPasar` (`idTarj`, `idPasar`)
VALUES ('3', '116');
INSERT INTO `tbl_colComerPasar` (`idadmin`, `idpasarelaW`, `idpasarelaT`, `idcomercio`, `fechaIni`, `fechaFin`)
VALUES ('10', '116', NULL, '122327460662', unix_timestamp(), '2863700400');
INSERT INTO `tbl_colComerPasar` (`idadmin`, `idpasarelaW`, `idpasarelaT`, `idcomercio`, `fechaIni`, `fechaFin`)
VALUES ('10', null, '116', '122327460662', unix_timestamp(), '2863700400');

