INSERT INTO `tbl_pasarela` (`nombre`, `tipo`, `cuenta`, `datos`, `fecha`, `activo`, `comercio`, `imagen`, `idcenauto`, `estado`, `secure`, `idbanco`, `devolucion`, `idempresa`, `LimMinOper`, `LimMaxOper`, `LimDiar`, `LimMens`, `LimAnual`, `LimOperIpDia`, `LimOperTarDia`, `LimOperDia`, `coefImporta`, `idagencia`, `amex`, `observacion`, `fechamod`) values 
('IberoTef2', 'P', '', 'pasoK@firmaB', '1527598925', '1', 'Iberotravels', NULL, '13', 'P', '0', '25', '1', '4', '0', '1000', '20000', '100000000', '100000000', '100', '100', '500', '1.00', '8', '0', '', '1527598925'),
('IberoTef2 3D', 'P', '', 'pasoK@firmaB', '1527598925', '1', 'Iberotravels', NULL, '13', 'P', '1', '25', '1', '4', '0', '1000', '20000', '100000000', '100000000', '100', '100', '500', '1.00', '8', '0', '', '1527598925');

insert into tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values
(89,978,'4001','5b0402c0f35da5.17814505','031801673',1527598925,'',1),
(90,978,'4001','5b0402c0f35da5.17814505','031801673',1527598925,'',1);

insert into tbl_colPasarBancos (idpasarela, idbanco) values
(89,25),
(90,25)


