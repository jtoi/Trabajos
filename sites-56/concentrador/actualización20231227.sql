
-- phpMyAdmin SQL Dump
-- version 4.6.3
-- https://www.phpmyadmin.net/
-- droip
-- Host: localhost
-- Generation Time: Sep 03, 2016 at 01:33 PM
-- Server version: 5.7.13-0ubuntu0.16.04.2
-- PHP Version: 5.6.25-1+deb.sury.org~xenial+1


INSERT INTO tbl_cenAuto (nombre, urlPro, urlDes, tipo, fecha, datos, urlXml)
VALUES ('Eurocoinpay', 'True', 'False', 'form', '1703701031', '\"eurocoinpay_terminal_number\":\";terminal;\",\"eurocoinpay_customer_number\":\";idCom;\",\"eurocoinpay_encryption_key\":\";apikey;\",\"orderid\":\";trans;\",\"amount\":\";importe;\",\"currency\":\";moneda;\"', '');
#verificar el id con que se ha inscrito el este centro autorizador para cambiarlo en la pasarela, tiene puesto el 28

#La pasarela la puse al banco 1 que es BBVA no sé si es ese con el que esta pasarela trabaja
#si es otro que ya tienen en la tabla sustituir el 40 por el número que tenga
#si no existiera el banco, insertarlo y sustituir el 40 por el número que tenga
INSERT INTO tbl_pasarela (nombre, tipo, cuenta, datos, fecha, activo, comercio, imagen, idcenauto, estado, secure, idbanco, devolucion, idempresa, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia, LimOperDia, coefImporta, idagencia, amex, observacion, fechamod, usdxamex, pasarLim)
VALUES ('Eurocoinpay', 'P', NULL, 'pasoR@firma', '1703701031', '1', 'Bidaiondo', NULL, '28', 'D', '0', '1', '0', '1', '0', '100000000', '100000000', '100000000', '100000000', '100', '100', '500', '1.00', NULL, '0', NULL, '1703701031', '0', '0');

#Revisar el id de la pasarela que se acaba de insertar para poner acá el que corresponda
INSERT INTO tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado)
VALUES ('223', '978', '1', 'tT4HloLltnctVWzHEikTzwDfpv8rsgslVR/uycaKXzs=', '7', '1703701031', NULL, '1');

#En alguna parte leí que iba a trabajar con criptomonedas y no con tarjetas, si no es así cambiar el 17 por el correspondiente
insert into tbl_colTarjPasar values (null, '17', '223');



update tbl_comercio set pasarela = '223,151,182,50,93,122', pasarelaAlMom = '	93,122,71,179,176,223,185,215,224' where id = '1';




