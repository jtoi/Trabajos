INSERT INTO `tbl_pasarela` (`nombre`, `tipo`, `cuenta`, `datos`, `fecha`, `activo`, `comercio`, `imagen`, `idcenauto`, `estado`, `secure`, `idbanco`, `devolucion`, `idempresa`, `LimMinOper`, `LimMaxOper`, `LimDiar`, `LimMens`, `LimAnual`, `LimOperIpDia`, `LimOperTarDia`, `LimOperDia`, `coefImporta`, `idagencia`, `amex`, `observacion`, `fechamod`, `usdxamex`, `pasarLim`)
values ('Kutxabank IT 3D', 'P', '', 'pasoA@firmaX', '1632756812', '1', 'Bidaitravel', '', '17', 'D', '1', '37', '0', '4', '0', '25000', '25000', '100000000', '100000000', '6', '100', '200', '1.00', '8', '0', '', '1632756812', '0', '182');

UPDATE `tbl_pasarela` SET `pasarLim` = '186' WHERE `idPasarela` = '186';

INSERT INTO `tbl_colPasarMon` (`idpasarela`, `idmoneda`, `terminal`, `clave`, `comercio`, `fecha`, `datos`, `estado`)
values ('186', '978', '001', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7', '059380949', '1632756812', '', '1');

insert into tbl_colTarjPasar (idTarj, idPasar) values
(2,186),
(3,186);

UPDATE `tbl_comercio` SET `pasarelaAlMom` = '12,138,111,164,68,129,153,98,145,146,76,144,45,181,125,85,67,99,46,126,168,84,151,165,166,182,50,139,137,80,95,120,116,118,175,51,127,97,93,167,160,113,163,58,128,59,122,140,71,72,141,130,156,179,176,180,115,117,112,37,91,119,73,131,106,94,183,184,185,186' WHERE `id` = '1';
