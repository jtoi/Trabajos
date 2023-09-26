##modificación para la integración con Xilema

INSERT INTO tbl_cenAuto (nombre, urlPro, urlDes, tipo, fecha, datos, urlXml)
VALUES ('Xilema', 'https://internal-pro.xilemapayments.com/estelaCore/gw/v1.0/startPayment', 'https://internal-uat.xilemapayments.com/estelaCore/gw/v1.0/startPayment', 'form', '1530638056', '{"Token":";Xitoken;"}', '');

INSERT INTO tbl_pasarela (nombre, tipo, cuenta, datos, fecha, activo, comercio, imagen, idcenauto, estado, secure, idbanco, devolucion, idempresa, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia, LimOperDia, coefImporta, idagencia, amex, observacion, fechamod, usdxamex)
VALUES ('Xilema1 3D', 'P', NULL, 'pasoL@firmaA', '1530638309', '1', NULL, NULL, '14', 'D', '1', '1', '0', '1', '0', '100000000', '100000000', '100000000', '100000000', '100', '100', '500', '1.00', '2', '0', NULL, '1530638309', '0');

INSERT INTO tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values
('92', '978', '7', 'ri/IBNyPOCCYVsR7JSIWu5t37+Qt6owj', '2A2E245F2B26108B33F88F4FD4A0FBAE', '1530639667', '', '1');

UPDATE tbl_comercio SET id = '1', idcomercio = '122327460662', palabra = '3T44WbXQHh2CtGTe4PM', nombre = 'Prueba', activo = 'S', estado = 'P', url = 'https://www.administracomercios.com/rep/', fechaAlta = '1223295165', fechaMovUltima = '1493914596', historico = 'P=1493914596', prefijo_trans = '14', condiciones_esp = '', condiciones_eng = '', correo_esp = '', correo_eng = '', pasarela = '12,76,45,85,67,46,84,23,88,32,41,52,77,86,63,50,82,83,89,90,51,58,59,71,72,29,87,31,44,75,81,37,92,73,79,64,91', pasarelaAlMom = '12,76,45,85,67,46,84,23,88,32,41,52,77,86,63,50,82,83,89,90,51,58,59,71,72,29,87,31,44,75,81,37,92,73,79,64,91', sms = '0', telf = '', url_llegada = 'https://www.administracomercios.com/rep/llegada.php', datos = 'Travels & Discovery S.A.</span><br>Calle Miguel Brostella Oficina 46 Centro Comercial Camino de Cruces<br>Panamá, República de Panamá<br>Telefax: 0034 94 4530466 ', cierrePer = '', horIniCierre = '0', horFinCierre = '', minCierre = '0', maxCierre = '50000', cierreAnt = NULL, cuota = '200', mensConcentr = '20', cuotaTarjeta = '1', retropago = '4.50', transfr = '0.00', swift = '0', cbancario = '0.25', minbancario = '12', ciereAuto = '0', voucherEs = '', voucherEn = '', correoMas = '0', usarTasaCuc = '0', minTransf = '3000', tranfTpv = '1', idpasTransf = '0', cierrePor = '1', pasaRot = '0', vendventodo = 'S', llevacierre = '1', permnsec = '0', fijo = '', corrido = '', etiqueta = '', operEur = '0', cambOperEuro = '0', usdxamex = '0' WHERE id = '1';

insert into tbl_colComerPasar (idadmin, idpasarelaW, idpasarelaT, idcomercio, fechaIni, fechaFin) values 
(10, 91, null, '122327460662', unix_timestamp(), 2863700400),
(10, 92, null, '122327460662', unix_timestamp(), 2863700400),
(10, null, 91, '122327460662', unix_timestamp(), 2863700400),
(10, null, 92, '122327460662', unix_timestamp(), 2863700400);
