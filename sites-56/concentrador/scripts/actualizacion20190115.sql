INSERT INTO tbl_cenAuto (id, nombre, urlPro, urlDes, tipo, fecha, datos, urlXml) VALUES
(16,	'Prueba2',	'https://sis.redsys.es/sis/realizarPago',	'https://sis-t.redsys.es:25443/sis/realizarPago',	'melt',	1414068825,	'{\"Ds_Merchant_Amount\":\";importe;\",\"Ds_Merchant_Currency\":\";moneda;\",\"Ds_Merchant_Order\":\";trans;\",\"Ds_Merchant_Titular\":\";titular;\",\"Ds_Merchant_MerchantCode\":\";idCom;\",\"Ds_Merchant_MerchantURL\":\";urlcomercio;\",\"Ds_Merchant_UrlOK\":\";urlok;\",\"Ds_Merchant_UrlKO\":\";urlko;\",\"Ds_Merchant_MerchantName\":\";comName;\",\"Ds_Merchant_PayMethods\":\";T;\",\"Ds_Merchant_ConsumerLanguage\":\";idioma;\",\"Ds_Merchant_MerchantSignature\":\";Digest;\",\"Ds_Merchant_Terminal\":\";terminal;\",\"Ds_Merchant_TransactionType\":\";tipoTrans;\"}',	'https://sis.redsys.es/sis/operaciones');

ALTER TABLE `tbl_agencias`
ADD `url` varchar(200) COLLATE 'latin1_spanish_ci' NULL;

update tbl_agencias set url = 'https://www.travelsandiscoverytours.com' where id = 1;
update tbl_agencias set url = 'https://www.caribeantravelweb.com' where id = 2;
update tbl_agencias set url = 'https://www.tropicalnatur.com' where id = 3;
update tbl_agencias set url = 'https://www.caribbeantravelway.com' where id = 4;
update tbl_agencias set url = 'https://www.bidaiondo.com' where id = 5;
update tbl_agencias set url = 'https://www.publinetservicios.com' where id = 6;
update tbl_agencias set url = 'https://www.bidaitravel.com' where id = 8;


/*A borrar cuando se suba final*/
INSERT INTO `tbl_agencias` (`nombre`, `dominio`, `fecha`, `url`)
VALUES ('prueba', 'localhost', '1547928764', 'http://localhost');

INSERT INTO `tbl_pasarela` (`nombre`, `tipo`, `cuenta`, `datos`, `fecha`, `activo`, `comercio`, `imagen`, `idcenauto`, `estado`, `secure`, `idbanco`, `devolucion`, `idempresa`, `LimMinOper`, `LimMaxOper`, `LimDiar`, `LimMens`, `LimAnual`, `LimOperIpDia`, `LimOperTarDia`, `LimOperDia`, `coefImporta`, `idagencia`, `amex`, `observacion`, `fechamod`, `usdxamex`, `pasarLim`)
SELECT 'prueba 3D', 'P', '', 'pasoA@firmaX', '1410363574', '1', 'Travels and Discovery', NULL, '16', 'P', '1', '6', '0', '1', '0', '5000', '30000', '100000000', '100000000', '20', '100', '200', '1.00', '9', '0', '', '1547570122', '0', '41'
FROM `tbl_pasarela`
WHERE ((`idPasarela` = '41'));

INSERT INTO `tbl_colPasarMon` (`idpasarela`, `idmoneda`, `terminal`, `clave`, `comercio`, `fecha`, `datos`, `estado`)
SELECT '112', `idmoneda`, `terminal`, `clave`, `comercio`, `fecha`, `datos`, `estado`
FROM `tbl_colPasarMon`
WHERE `idpasarela` IN (41,112) AND ((`id` = '106') OR (`id` = '108'));

INSERT INTO `tbl_colTarjPasar` (`idTarj`, `idPasar`)
SELECT `idTarj`, '112'
FROM `tbl_colTarjPasar`
WHERE `idPasar` IN (41,112) AND ((`id` = '1405') OR (`id` = '1409'));

UPDATE `tbl_comercio` SET
`id` = '1',
`idcomercio` = '122327460662',
`palabra` = '3T44WbXQHh2CtGTe4PM',
`nombre` = 'Prueba',
`activo` = 'S',
`estado` = 'P',
`url` = 'https://www.administracomercios.com/rep/',
`fechaAlta` = '1223295165',
`fechaMovUltima` = '1493914596',
`historico` = 'P=1493914596',
`prefijo_trans` = '14',
`condiciones_esp` = '',
`condiciones_eng` = '',
`correo_esp` = 'Estimado(a) {cliente}<br>Usted puede realizar el pago de {importe} correspondiente al servicio {servicio} solicitado a {comercio} en el siguiente enlace:<br>{url} <br><br>O copie y pegue en su navegador la siguiente url:<br>{urla}<br><br><br>Importante:<br>Debe conocer que esta solicitud es válida por una sola vez y en {tiempo} días quedará sin efecto.<br>Para pagos electrónicos seguros, después de introducir los datos de la tarjeta, su banco emisor lo identificará con un código o pin de seguridad asociado a la misma. En caso de no poseerlo contacte con su banco y solicítelo de forma gratuita.<br><br>Si tiene alguna duda para realizar el pago contacte con {correo}.<br><br>Muchas Gracias<br><br>AVISO LEGAL - Este correo electrónico es confidencial y para uso exclusivo de la(s) persona(s) a quien(es) se dirige. Si usted no es la persona destinataria designada y recibe este mensaje por error, por favor, notificar inmediatamente a la persona que lo envió y borrarlo definitivamente de su si',
`correo_eng` = 'Dear {cliente}<br> You can make the payment of {importe} corresponding to the service {servicio} requested to {comercio} on the following link:<br>{url} <br><br>or copy and paste into your browser the following url:<br>{urla}<br><br><br>Important:<br>Please notice that this request is valid only once and in {tiempo} days will be without effect.<br>For secure electronic payments, after entering the data on the card, your issuing bank will identify you with a security code or pin associated with it. If you do not have it, please contact your bank and ask for it for free.<br><br>If you have any doubts to make the payment, you may contact {correo}.<br><br>Thank you very much<br><br>LEGAL NOTICE - This email is confidential and for the exclusive use of the person (s) to whom it is addressed. If you are not the designated recipient and you receive this message in error, please notify the sender immediately and delete it from your system.',
`pasarela` = '12,68,98,76,45,85,67,46,23,88,32,41,52,77,63,86,80,50,82,83,89,90,51,93,110,58,59,71,72,29,31,87,44,75,81,37,91,73,79,64,94,92,112',
`pasarelaAlMom` = '12,68,98,76,45,85,67,99,46,23,100,88,32,41,102,52,77,103,63,86,80,50,82,83,89,90,95,51,97,93,110,58,59,71,72,29,101,31,87,44,108,75,81,104,37,91,73,106,79,64,94,92,105,112',
`sms` = '0',
`telf` = '',
`url_llegada` = 'https://www.administracomercios.com/rep/llegada.php',
`datos` = 'Travels & Discovery S.A.</span><br>Calle Miguel Brostella Oficina 46 Centro Comercial Camino de Cruces<br>Panamá, República de Panamá<br>Telefax: 0034 94 4530466',
`cierrePer` = '',
`horIniCierre` = '0',
`horFinCierre` = '',
`minCierre` = '0',
`maxCierre` = '50000',
`cierreAnt` = NULL,
`cuota` = '200',
`mensConcentr` = '20',
`cuotaTarjeta` = '1',
`retropago` = '4.50',
`transfr` = '0.00',
`swift` = '0',
`cbancario` = '0.25',
`minbancario` = '12',
`ciereAuto` = '0',
`voucherEs` = '',
`voucherEn` = '',
`correoMas` = '0',
`usarTasaCuc` = '0',
`minTransf` = '3000',
`tranfTpv` = '1',
`idpasTransf` = '0',
`cierrePor` = '1',
`pasaRot` = '0',
`vendventodo` = 'S',
`llevacierre` = '1',
`permnsec` = '0',
`fijo` = '',
`corrido` = '',
`etiqueta` = '',
`operEur` = '0',
`cambOperEuro` = '0',
`usdxamex` = '0'
WHERE `id` = '1';



