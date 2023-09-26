INSERT INTO `tbl_pasarela` (`nombre`, `tipo`, `cuenta`, `datos`, `fecha`, `activo`, `comercio`, `imagen`, `idcenauto`, `estado`, `secure`, `idbanco`, `devolucion`, `idempresa`, `LimMinOper`, `LimMaxOper`, `LimDiar`, `LimMens`, `LimAnual`, `LimOperIpDia`, `LimOperTarDia`, `LimOperDia`, `coefImporta`, `idagencia`, `amex`, `observacion`, `fechamod`, `usdxamex`, `pasarLim`) values
('Kutxabank 3D', 'P', '', 'pasoA@firmaX', '1621952426', '1', 'Caribeantravel Web', '', '17', 'D', '1', '2', '0', '1', '0', '9500', '100000000', '100000000', '100000000', '1000', '20', '1000', '1.00', '2', '0', '', '1621953209', '0', '163');
update tbl_pasarela set pasarLim = 175 where idPasarela = 175;
insert into tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values
(175, '978', '001', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', '059373522', unix_timestamp(), '', 1),
(175, '840', '002', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', '059373522', unix_timestamp(), '', 1),
(175, '124', '003', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', '059373522', unix_timestamp(), '', 1),
(175, '826', '004', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', '059373522', unix_timestamp(), '', 1);
insert into tbl_colTarjPasar (idTarj, idPasar) values
(2, 175),
(3, 175);
