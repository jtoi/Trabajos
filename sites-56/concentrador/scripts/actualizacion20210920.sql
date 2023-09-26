INSERT INTO tbl_pasarela (idPasarela, nombre, tipo, cuenta, datos, fecha, activo, comercio, imagen, idcenauto, estado, secure, idbanco, devolucion, idempresa, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia, LimOperDia, coefImporta, idagencia, amex, observacion, fechamod, usdxamex, pasarLim) VALUES (NULL, 'Xilema PAYPAL', 'P', '', 'pasoL@firmaA', '1632146862', '1', '', '', '14', 'D', '0', '31', '0', '1', '0', '9500', '100000000', '100000000', '100000000', '100', '100', '1000', '1.00', '2', '0', '', '1632146862', '0', '94');

UPDATE tbl_pasarela SET pasarLim = '185' WHERE tbl_pasarela.idPasarela = 185;

INSERT INTO tbl_colPasarMon (id, idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado) VALUES (NULL, '185', '840', '7', 'ICPyqYQmFKvGguYPVyf22+hCKPPMOQXX', '9DF48E0118B259560396BF0175BAC731', '1632146862', '', '1');

UPDATE tbl_comercio SET pasarelaAlMom = '12,138,111,164,68,129,153,98,145,146,76,144,45,181,125,85,67,99,46,126,168,84,151,165,166,182,50,139,137,80,95,120,116,118,175,51,127,97,93,167,160,113,163,58,128,59,122,140,71,72,141,130,156,179,176,180,115,117,112,37,91,119,73,131,106,94,183,184,185' WHERE id = '1';

INSERT INTO tbl_tarjetas (nombre, imagen, activo, tipo)
VALUES ('Paypal', 'paypal', '1', 'M');

INSERT INTO tbl_colTarjPasar (idTarj, idPasar)
VALUES ('16', '185');
INSERT INTO tbl_colTarjPasar (idTarj, idPasar)
VALUES ('3', '185');
INSERT INTO tbl_colTarjPasar (idTarj, idPasar)
VALUES ('2', '185');
