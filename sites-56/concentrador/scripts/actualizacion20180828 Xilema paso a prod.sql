update tbl_colPasarMon set clave = 'H6ygu/O+99wl9Mxex9NQE5NJGbSHfhBn', fecha = unix_timestamp(), comercio = '982E5D8656C42361F16BEE9C7C4C5842'
where id in (313, 315);
insert into tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values
(92, 840, 7, 'WcutRVwjOC8NftgUD855BI3dGixY2Hbf', '84FF40238D11CBC67AB212DAF52B1A26', unix_timestamp(), '', 1),
(94, 840, 7, 'WcutRVwjOC8NftgUD855BI3dGixY2Hbf', '84FF40238D11CBC67AB212DAF52B1A26', unix_timestamp(), '', 1)






#Estas dos lineas a continuación es como estaba Xilema en Desarrollo
INSERT INTO `tbl_colPasarMon` (`id`, `idpasarela`, `idmoneda`, `terminal`, `clave`, `comercio`, `fecha`, `datos`, `estado`) VALUES
(313,	92,	'978',	'7',	'ri/IBNyPOCCYVsR7JSIWu5t37+Qt6owj',	'2A2E245F2B26108B33F88F4FD4A0FBAE',	1530639667,	'',	1),
(315,	94,	'978',	'7',	'ri/IBNyPOCCYVsR7JSIWu5t37+Qt6owj',	'2A2E245F2B26108B33F88F4FD4A0FBAE',	1530639667,	'',	1);
