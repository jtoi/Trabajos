INSERT INTO `tbl_cenAuto` (`nombre`, `urlPro`, `urlDes`, `tipo`, `fecha`, `datos`, `urlXml`)
values ('Caribeantravelweb', 'https://www.caribeantravelweb.com/mod/entra.php', 'http://localhost/caribeantravelweb/entra.php', 'form', unix_timestamp(), '{\"Ds_Merchant_Amount\":\";importe;\",\"Ds_Merchant_Currency\":\";moneda;\",\"Ds_Merchant_Order\":\";trans;\",\"Ds_Merchant_ProductDescription\":\";producto;\",\"Ds_Merchant_Titular\":\";titular;\",\"Ds_Merchant_MerchantCode\":\";idCom;\",\"Ds_Merchant_MerchantURL\":\";urlcomercio;\",\"Ds_Merchant_UrlOK\":\";urlok;\",\"Ds_Merchant_UrlKO\":\";urlko;\",\"Ds_Merchant_MerchantName\":\";comName;\",\"Ds_Merchant_PayMethods\":\";T;\",\"Ds_Merchant_ConsumerLanguage\":\";idioma;\",\"Ds_Merchant_MerchantSignature\":\";Digest;\",\"Ds_Merchant_Terminal\":\";terminal;\",\"Ds_Merchant_TransactionType\":\";tipoTrans;\",\"M_CENAUTO\":\";cenauto;\"}', '');

INSERT INTO `tbl_pasarela` (`nombre`, `tipo`, `cuenta`, `datos`, `fecha`, `activo`, `comercio`, `imagen`, `idcenauto`, `estado`, `secure`, `idbanco`, `devolucion`, `idempresa`, `LimMinOper`, `LimMaxOper`, `LimDiar`, `LimMens`, `LimAnual`, `LimOperIpDia`, `LimOperTarDia`, `LimOperDia`, `coefImporta`, `idagencia`, `amex`, `observacion`, `fechamod`) values 
('IngCaribean', 'P', '', 'pasoA@firmaB', unix_timestamp(), '1', 'ING', NULL, '15', 'D', '1', '14', '0', '4', '0', '1000', '20000', '100000000', '100000000', '100', '100', '500', '1.00', '8', '0', '', '159200724');

insert into tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) values
(95,978,'001','sq7HjrUOBfKmC576ILgskD5srU870gJ7','031200173', unix_timestamp(),'',1);

insert into tbl_colPasarBancos (idpasarela, idbanco) values
(95,14);

insert into tbl_colTarjPasar (idTarj, idPasar) values ('2', '95'),('3', '95')

