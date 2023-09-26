
##modificación para la integración con Titanes

INSERT INTO tbl_pasarela (nombre, tipo, cuenta, datos, fecha, activo, comercio, imagen, idcenauto, estado, secure, idbanco, devolucion, idempresa, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia, LimOperDia, coefImporta, idagencia, amex, observacion, fechamod, usdxamex)
values ('Titanes 3D', 'P', '', 'pasoA@firmaX', '1528123679', '1', 'Maf Servicios Integrales', NULL, '3', 'D', '1', '17', '0', '2', '0', '10000', '100000000', '100000000', '100000000', '1000', '20', '1000', '1.00', NULL, '0', '', '1528123679', '0');

INSERT INTO `tbl_colPasarMon` (`idpasarela`, `idmoneda`, `terminal`, `clave`, `comercio`, `fecha`, `datos`, `estado`) values
('91', '978', '7', 'P6hGHyKWwuL03Rpw8BudtYErs8Dbp2UZ', '022551493', '1528123927', '', '1'),
('91', '840', '8', 'P6hGHyKWwuL03Rpw8BudtYErs8Dbp2UZ', '022551493', '1528123927', '', '1');
