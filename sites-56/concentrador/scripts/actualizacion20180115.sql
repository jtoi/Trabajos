insert into tbl_agencias (nombre, dominio, fecha) values ('BidaiTravel', 'bidaitravel.com', unix_timestamp());

INSERT INTO tbl_pasarela (nombre, tipo, cuenta, datos, fecha, activo, comercio, imagen, idcenauto, estado, secure, idbanco, devolucion, idempresa, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia, LimOperDia, coefImporta, idagencia, amex, observacion, fechamod) values 
('IberoTef', 'P', '', 'pasoK@firmaB', unix_timestamp(), '1', 'Iberotravels', NULL, '4', 'D', '0', '2', '1', '4', '0', '1000', '20000', '100000000', '100000000', '100', '100', '500', '1.00', '8', '0', '', unix_timestamp());

INSERT INTO tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values 
('82', '978', '00000001', '5a59c34aac6277.66125093', 'V99000281', unix_timestamp(), '', '1');
INSERT INTO tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values 
('82', '840', '00000002', '5a59c34aac6277.66125093', 'V99000281', unix_timestamp(), '', '1');

INSERT INTO tbl_colTarjPasar (idTarj, idPasar)
SELECT idTarj, '82'
FROM tbl_colTarjPasar
WHERE idPasar = '75';

INSERT INTO tbl_pasarela (nombre, tipo, cuenta, datos, fecha, activo, comercio, imagen, idcenauto, estado, secure, idbanco, devolucion, idempresa, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia, LimOperDia, coefImporta, idagencia, amex, observacion, fechamod) values 
('IberoTef 3D', 'P', '', 'pasoK@firmaB', unix_timestamp(), '1', 'Iberotravels', NULL, '4', 'D', '1', '2', '1', '4', '0', '1000', '20000', '100000000', '100000000', '100', '100', '500', '1.00', '8', '0', '', unix_timestamp());

INSERT INTO tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values 
('83', '978', '00000001', '5a59c34aac6277.66125093', 'V99000281', unix_timestamp(), '', '1');
INSERT INTO tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values 
('83', '840', '00000002', '5a59c34aac6277.66125093', 'V99000281', unix_timestamp(), '', '1');

INSERT INTO tbl_colTarjPasar (idTarj, idPasar)
SELECT idTarj, '83'
FROM tbl_colTarjPasar
WHERE idPasar = '75';

INSERT INTO `tbl_cenAuto` (`nombre`, `urlPro`, `urlDes`, `tipo`, `fecha`, `datos`, `urlXml`)
SELECT 'Tefpay', 'https://payments.tefpay.com', 'https://intepayments.tefpay.com', 'form', '1516714580', '{\"Ds_Merchant_MerchantCode\":\";idCom;\",\"Ds_Merchant_TransactionType\":\";tipoTrans;\",\"Ds_Merchant_MerchantSignature\":\";Digest;\",\"Ds_Merchant_Url\":\";urlcomercio;\",\"Ds_Merchant_UrlOK\":\";urlok;\",\"Ds_Merchant_UrlKO\":\";urlko;\",\"Ds_Merchant_Amount\":\";importe;\",\"Ds_Merchant_Currency\":\";moneda;\",\"Ds_Merchant_MatchingData\":\";trans;\",\"Ds_Merchant_Description\":\";producto;\",\"Ds_Merchant_Lang\":\";idioma;\",\"Ds_Merchant_Terminal\":\";terminal;\"}', ''
FROM `tbl_cenAuto`
WHERE ((`id` = '4'));

update tbl_cenAuto set nombre = 'TefPay Cimex' where id = 4;
update tbl_pasarela set idcenauto = 13 where idPasarela in (75,76,77,81,82,83);
