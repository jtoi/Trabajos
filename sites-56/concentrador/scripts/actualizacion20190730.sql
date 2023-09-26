UPDATE `tbl_bancos` SET `id` = '31', `banco` = 'UniversalPay' WHERE `id` = '31';

INSERT INTO `tbl_cenAuto` (`nombre`, `urlPro`, `urlDes`, `tipo`, `fecha`, `datos`, `urlXml`)
VALUES ('ComNPay', 'https://secure.comnpay.com', 'https://secure.homologation.comnpay.com', 'form', '1564492083', '{"montant":";importe;","idTPE":";idCom;","idTransaction":";trans;","idCommande":";idCommande;","devise":";moneda;","lang":";idioma;","nom_produit":";producto;","source":";source;","urlRetourOK":";urlok;","urlRetourNKO":";urlko;","typeTr":";tipoTrans;","data":";trans;","porteur":";porteur;","abonnement":";abonnement;","codeTemplate":";codeTemplate;","panier":";panier;","urlIPN":";urlcomercio;","sec":";Digest;"}', '');

insert into tbl_bancos (banco) values ('AfonePaiement');

INSERT INTO `tbl_pasarela` (`nombre`, `tipo`, `cuenta`, `datos`, `fecha`, `activo`, `comercio`, `imagen`, `idcenauto`, `estado`, `secure`, `idbanco`, `devolucion`, `idempresa`, `LimMinOper`, `LimMaxOper`, `LimDiar`, `LimMens`, `LimAnual`, `LimOperIpDia`, `LimOperTarDia`, `LimOperDia`, `coefImporta`, `idagencia`, `amex`, `observacion`, `fechamod`, `usdxamex`, `pasarLim`)
VALUES ('Papam', 'P', '', 'pasoM@firmaF', '1564492651', '1', 'Caribeantravelweb', NULL, '18', 'D', '0', last_insert_id(), '0', '1', '0', '100000000', '100000000', '100000000', '100000000', '100', '100', '500', '1.00', 2, '0', NULL, '1564492651', '0', '0');

update tbl_pasarela set pasarLim = LAST_INSERT_ID() where idPasarela = last_insert_id();

INSERT INTO `tbl_colPasarMon` (`idpasarela`, `idmoneda`, `terminal`, `clave`, `comercio`, `fecha`, `datos`, `estado`) VALUES 
('115', '978', '', 'JTCbWRxd0tu74b2Bb86D', 'HOM-061-170', '1564577080', '', '1');

insert into tbl_colTarjPasar (idTarj, idPasar) values (2,115),(3,115);


