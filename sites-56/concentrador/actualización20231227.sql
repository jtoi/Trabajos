INSERT INTO `tbl_cenAuto` (`id`, `nombre`, `urlPro`, `urlDes`, `tipo`, `fecha`, `datos`, `urlXml`) VALUES
(null, 'Eurocoinpay', 'True', 'False', 'form', 1703701031, '{\"data\":\";data;\",\"sig\":\";sig;\"}', '');
#verificar el id con que se ha inscrito el este centro autorizador para cambiarlo en la pasarela, tiene puesto el 28

#La pasarela la puse al banco 1 que es BBVA no sé si es ese con el que esta pasarela trabaja
#si es otro que ya tienen en la tabla sustituir el 40 por el número que tenga
#si no existiera el banco, insertarlo y sustituir el 40 por el número que tenga
INSERT INTO `tbl_pasarela` (`idPasarela`, `nombre`, `tipo`, `cuenta`, `datos`, `fecha`, `activo`, `comercio`, `imagen`, `idcenauto`, `estado`, `secure`, `idbanco`, `devolucion`, `idempresa`, `LimMinOper`, `LimMaxOper`, `LimDiar`, `LimMens`, `LimAnual`, `LimOperIpDia`, `LimOperTarDia`, `LimOperDia`, `coefImporta`, `idagencia`, `amex`, `observacion`, `fechamod`, `usdxamex`, `pasarLim`) VALUES
(223, 'Eurocoinpay', 'P', NULL, 'pasoR@firma', 1703701031, 1, 'Bidaiondo', NULL, 28, 'D', 0, 1, 0, 1, 0, 100000000, 100000000, 100000000, 100000000, 100, 100, 500, 1.00, NULL, 0, NULL, 1703701031, 0, 0);
#Al insertar la nueva pasarela en la tabla tbl_pasarela obtengo el id 223 e mi caso, debes revisar en el tuyo y en este scrip
#sustituirás el 223 por el id que obtengas

#Revisar el id de la pasarela que se acaba de insertar para poner acá el que corresponda
INSERT INTO `tbl_colComerPasaMon` (`id`, `idcomercio`, `idpasarela`, `idmoneda`, `fecha`) VALUES
(6070,	1,	223,	'978',	1703843848);

#En alguna parte leí que iba a trabajar con criptomonedas y no con tarjetas, si no es así cambiar el 17 por el correspondiente
INSERT INTO `tbl_colTarjPasar` (`id`, `idTarj`, `idPasar`) VALUES
(5074,	17,	223);

update tbl_comercio set pasarela = '223,', pasarelaAlMom = ',215,223,' where id = '1';

