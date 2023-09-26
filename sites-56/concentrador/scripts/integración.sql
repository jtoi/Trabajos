INSERT INTO tbl_pasarela (nombre, tipo, cuenta, datos, fecha, activo, comercio, imagen, idcenauto, estado, secure, idbanco, devolucion, idempresa, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia, LimOperDia, coefImporta, idagencia, amex, observacion, fechamod, usdxamex, pasarLim)
values ('Sabadell CSC 3D', 'P', '', 'pasoA@firmaX', '1610470737', '1', 'Cubashopping Center', '', '17', 'D', '1', '2', '0', '1', '0', '9500', '100000000', '100000000', '100000000', '1000', '20', '1000', '1.00', '3', '0', '', '1607635068', '0', '165');
INSERT INTO tbl_pasarela (nombre, tipo, cuenta, datos, fecha, activo, comercio, imagen, idcenauto, estado, secure, idbanco, devolucion, idempresa, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia, LimOperDia, coefImporta, idagencia, amex, observacion, fechamod, usdxamex, pasarLim)
values ('Sabadell CSC', 'P', '', 'pasoA@firmaX', '1610470737', '1', 'Cubashopping Center', '', '17', 'D', '0', '2', '0', '1', '0', '9500', '100000000', '100000000', '100000000', '1000', '20', '1000', '1.00', '3', '0', '', '1607635068', '0', '166');

insert into tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values (165, 978, '001', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', '159238617', unix_timestamp(), '', 1);
insert into tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values (165, 840, '002', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', '159238617', unix_timestamp(), '', 1);
insert into tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values (165, 124, '004', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', '159238617', unix_timestamp(), '', 1);
insert into tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values (165, 826, '003', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', '159238617', unix_timestamp(), '', 1);


