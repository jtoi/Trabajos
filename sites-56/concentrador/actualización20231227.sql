delimiter ;;
drop trigger if exists `tr_inmonPasar`;
CREATE TRIGGER `tr_inmonPasar` AFTER INSERT ON `tbl_colPasarMon` FOR EACH ROW begin
	declare num_rows int default 0;
	declare no_more_rows boolean;
	declare idL int default 0;
	declare VM int default 0;
    declare pasarIn int default new.idpasarela;

	declare cur_limites cursor for select id, valMax from tbl_limites;
	declare continue handler for not found set no_more_rows = true;

	open cur_limites;
	select FOUND_ROWS() into num_rows;
	lazo1: loop
		fetch next from cur_limites into idL, VM;
		if no_more_rows then
			close cur_limites;
			leave lazo1;
		end if;
		insert into tbl_colPasarLimite (idPasar, idLimite, idmoneda, valor,fecha) values (new.idpasarela, idL, new.idmoneda, VM, new.fecha);
	end loop lazo1;

end
;;
delimiter ;


INSERT INTO tbl_cenAuto (nombre, urlPro, urlDes, tipo, fecha, datos, urlXml)
VALUES ('Eurocoinpay', 'True', 'False', 'form', '1703701031', '\"eurocoinpay_terminal_number\":\";terminal;\",\"eurocoinpay_customer_number\":\";idCom;\",\"eurocoinpay_encryption_key\":\";apikey;\",\"orderid\":\";trans;\",\"amount\":\";importe;\",\"currency\":\";moneda;\"', '');

#La pasarela la puse al banco 40 que es Coingate no sé si es ese con el que esta pasarela trabaja
#si es otro que ya tienen en la tabla sustituir el 40 por el número que tenga
#si no existiera, insertar el nuevo banco y sustituir el 40 por el número que tenga
INSERT INTO tbl_pasarela (nombre, tipo, cuenta, datos, fecha, activo, comercio, imagen, idcenauto, estado, secure, idbanco, devolucion, idempresa, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia, LimOperDia, coefImporta, idagencia, amex, observacion, fechamod, usdxamex, pasarLim)
VALUES ('Eurocoinpay', 'P', NULL, 'pasoG@firma', '1703701031', '1', 'Bidaiondo', NULL, '26', 'D', '0', '40', '0', '1', '0', '100000000', '100000000', '100000000', '100000000', '100', '100', '500', '1.00', NULL, '0', NULL, '1703701031', '0', '0');

INSERT INTO tbl_colPasarMon (idpasarela, idmoneda, terminal, clave, comercio, fecha, datos, estado)
VALUES ('248', '978', '1', 'tT4HloLltnctVWzHEikTzwDfpv8rsgslVR/uycaKXzs=', '7', '1703701031', NULL, '1');


DROP TABLE IF EXISTS `tbl_comercio`;
CREATE TABLE `tbl_comercio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcomercio` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado` varchar(11) not NULL default 'P', 
  `palabra` varchar(40) NULL default 'estaeslapalabra',
  `fechaAlta` int(11) NULL,
  `fechaMovUltima` int(11) NULL,
  `historico` varchar(100) NULL,
  `url` varchar(100) NULL,
  `prefijo_trans` varchar(40) NULL,
  `condiciones_esp` varchar(100) NULL,
  `condiciones_eng` varchar(100) NULL,
  `pasarela` varchar(100) NULL,
  `pasarelaAlMom` varchar(100) NULL,
  `url_llegada` varchar(100) NULL,
  `cierrePer` int(11) NULL,
  `horIniCierre` int(11) NULL,
  `horFinCierre` int(11) NULL,
  `maxCierre` int(11) NULL,
  `cuota` int(11) NULL,
  `mensConcentr` varchar(100) NULL,
  `cuotaTarjeta` int(11) NULL,
  `retropago` int(11) NULL,
  `transfr` varchar(100) NULL,
  `swift` varchar(100) NULL,
  `cbancario` varchar(100) NULL,
  `minbancario` int(11) NULL,
  `usarTasaCuc` varchar(100) NULL,
  `minTransf` int(11) NULL,
  `tranfTpv` int(11) NULL,
  `idpasTransf` int(11) NULL,
  `vendventodo` varchar(100) NULL,
  `fijo` varchar(100) NULL,
  `corrido` varchar(100) NULL,
  `etiqueta` varchar(100) NULL,
  `permnsec` varchar(100) NULL,
  `pasaRot` varchar(100) NULL,
  `operEur` varchar(100) NULL,
  `cambOperEuro` varchar(100) NULL,
  `usdxamex` varchar(100) NULL,
  `correo_esp` varchar(100) NULL,
  `correo_eng` varchar(100) NULL,
  `lotes` varchar(100) NULL,
  `urlDevol` varchar(100) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into tbl_comercio (idcomercio, nombre) values ('122327460662','Prueba');

DROP TABLE IF EXISTS `tbl_colComerPasar`;
CREATE TABLE `tbl_colComerPasar` (
id int(11) NOT NULL AUTO_INCREMENT,
idpasarelaT int(11) NULL, 
idpasarelaW int(11) NULL,
idcomercio int(11) NOT NULL, 
fechaIni int(11) NOT NULL, 
fechaFin int(12) NOT NULL,
primary key (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into tbl_colComerPasar (idpasarelaT, idcomercio, fechaIni, fechaFin) values (248, 1, 1703746814, 1766905214);
insert into tbl_colComerPasar (idpasarelaW, idcomercio, fechaIni, fechaFin) values (248, 1, 1703746814, 1766905214);

drop table if exists tbl_ipblancas;
create table `tbl_ipblancas` (
id int(11) not null auto_increment,
ip varchar (100) not null,
fecha int(11) null,
primary key (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `tbl_ipblancas` (ip) values ('localhost');
insert into `tbl_ipblancas` (ip) values ('172.0.0.1');
