-- phpMyAdmin SQL Dump
-- version 4.6.3
-- https://www.phpmyadmin.net/
-- droip
-- Host: localhost
-- Generation Time: Sep 03, 2016 at 01:33 PM
-- Server version: 5.7.13-0ubuntu0.16.04.2
-- PHP Version: 5.6.25-1+deb.sury.org~xenial+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `concentramf_db`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `CierreTransacciones`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CierreTransacciones` ()  begin
declare idcierres int;
declare idtransfs int;
declare idfacturas int;
declare fechafacts int;
declare fechatransfs int;
declare no_more_rows boolean;
declare num_rowscierre int default 0;
declare num_rowsfactura int default 0;
declare num_rowstransf int default 0;


declare cur_cierre cursor for select idcierre from tbl_cierreTransac;
declare cur_factura cursor for select id, fecha from tbl_factura where idcierre = idcierres;

declare continue handler for not found set no_more_rows = true;

open cur_cierre;
LOOP1: loop
fetch cur_cierre into idcierres;
if no_more_rows then
close cur_cierre;
leave LOOP1;
end if;


select count(*) into num_rowsfactura from tbl_factura where idcierre = idcierres;
if (num_rowsfactura > 0) then
open cur_factura;
LOOP2: loop
fetch cur_factura into idfacturas, fechafacts;
if no_more_rows then
set no_more_rows = false;
close cur_factura;
leave LOOP2;
end if;

insert into tbl_colCierreFactura values (null, idcierres, idfacturas, fechafacts);
end loop LOOP2;

end if;

end loop LOOP1;

end$$

DROP PROCEDURE IF EXISTS `estadComer`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `estadComer` (IN `idCom` INT, IN `estado` CHAR(1))  begin
	declare estPass char(1) default null;
	declare idAdm int;
	declare pase char(1) default 'S';
	declare no_more_rows boolean;
	declare totalC int default 0;
	declare totalN int default 0;
	declare num_rows int default 0;



	declare cur_usuarios cursor for select a.idadmin from tbl_admin a, tbl_colAdminComer o where o.idComerc = idCom and o.idAdmin = a.idadmin;
	declare continue handler for not found set no_more_rows = true;





	if (estado = 'S') then
		update tbl_admin a, tbl_colAdminComer o set activo = 'S' where o.idAdmin = a.idadmin and activo = 'N' and o.idComerc = idCom;
	else


		open cur_usuarios;
			select FOUND_ROWS() into num_rows;

			el_lazo: loop
				fetch cur_usuarios into idAdm;
				if no_more_rows then
					close cur_usuarios;
					leave el_lazo;
				end if;

				set totalC = 0;
				set totalN = 0;


				select count(idComerc) into totalC from tbl_colAdminComer where idAdmin = idAdm;
				select count(idComerc) into totalN from tbl_colAdminComer o, tbl_comercio c where idAdmin = idAdm and activo = 'N' and c.id = o.idComerc;

				if (totalC = (totalN + 1)) then


					update tbl_admin set activo = estado where idadmin = idAdm;
				end if;

			end loop el_lazo;


	end if;

end$$

DROP PROCEDURE IF EXISTS `fn_inspasar`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `fn_inspasar` (`com` INT, `pas` INT, `tipo` INT(1))  begin
	declare cant int default 0;
	declare hoy int default unix_timestamp();
	declare columna varchar(15);

	if (tipo = 0) then
		insert into tbl_colComerPasar (idpasarelaT, idcomercio, fechaIni, fechaFin) values (pas, com, hoy, 2863700400);
	else
		insert into tbl_colComerPasar (idpasarelaW, idcomercio, fechaIni, fechaFin) values (pas, com, hoy, 2863700400);
	end if;

end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `bitacora`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `bitacora`;
CREATE TABLE `bitacora` (
`login` varchar(20)
,`texto` text
,`fecha` datetime
,`idrol` int(11)
,`comercio` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `listadoIp`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `listadoIp`;
CREATE TABLE `listadoIp` (
`ip` char(16)
,`fecha` varchar(21)
,`fechatruc` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `listadoTransacciones`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `listadoTransacciones`;
CREATE TABLE `listadoTransacciones` (
`fecha_mod` int(11)
,`comercio` varchar(100)
,`fecha` varchar(19)
,`idtransaccion` varchar(14)
,`valor` decimal(14,4)
,`moneda` char(3)
,`estado` char(1)
,`euros` varchar(66)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `mobile_clientes`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `mobile_clientes`;
CREATE TABLE `mobile_clientes` (
`id_reserva` int(11)
,`id_transaccion` varchar(20)
,`comercio` varchar(100)
,`administr` varchar(100)
,`entorno` varchar(10)
,`identificador` varchar(20)
,`pasarela` varchar(100)
,`cliente` varchar(200)
,`email` varchar(100)
,`servicio` text
,`valor_inicial` decimal(8,2)
,`valor` decimal(8,2)
,`moneda` char(3)
,`fechaI` varchar(21)
,`bankId` varchar(50)
,`fechaII` varchar(21)
,`fechaIII` varchar(21)
,`fecha` int(11)
,`pMom` varchar(10)
,`estadoTr` varchar(12)
,`classe` varchar(6)
,`tiempoV` int(2)
,`id_comercio` varchar(14)
,`idcom` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `mobile_tickets`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `mobile_tickets`;
CREATE TABLE `mobile_tickets` (
`usuario` varchar(100)
,`email` varchar(150)
,`fechaModificada` int(11)
,`fecha` varchar(21)
,`asunto` varchar(200)
,`estadoP` varchar(9)
,`texto` text
,`idrol` int(11)
,`idticket` int(11)
,`idadmin` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `mobile_transacciones`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `mobile_transacciones`;
CREATE TABLE `mobile_transacciones` (
`idtransaccion` varchar(14)
,`comercio` varchar(100)
,`identificador` varchar(20)
,`fechaFin` varchar(21)
,`moneda` char(3)
,`fecha_mod` int(11)
,`valorIni` decimal(13,2)
,`fechaIni` varchar(21)
,`codigo` varchar(12)
,`fecha` int(11)
,`idcomercio` varchar(20)
,`idcom` int(11)
,`tasaM` double(21,4)
,`euroEquiv` varchar(19)
,`valorFin` varchar(17)
,`estadoTr` varchar(12)
,`tipoEntorno` varchar(17)
,`classe` varchar(6)
,`error` varchar(400)
,`pasarl` varchar(100)
,`ip` varchar(17)
,`tipoPag` varchar(2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `mobile_users`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `mobile_users`;
CREATE TABLE `mobile_users` (
`nombre` varchar(100)
,`email` varchar(150)
,`login` varchar(20)
,`fechaA` varchar(10)
,`fechaV` varchar(21)
,`idiom` mediumtext
,`idrol` int(11)
,`rol` varchar(50)
,`idadmin` int(11)
,`comercio` varchar(100)
,`fecha` int(11)
,`idcom` varchar(256)
,`activo` varchar(2)
);

--
-- Triggers `tbl_comercio`
--
DROP TRIGGER IF EXISTS `tr_inscomer`;
DELIMITER $$
CREATE TRIGGER `tr_inscomer` AFTER INSERT ON `tbl_comercio` FOR EACH ROW begin
	declare idAdm int;
	declare no_more_rows boolean;
	declare idCom int default new.id;
	declare num_rows int default 0;
    declare pasarIn varchar(100) default new.pasarela;
    declare pasarMm varchar(100) default new.pasarelaAlMom;
    declare pas int;

	declare cur_usuarios cursor for select a.idadmin from tbl_admin a where idrol < 11;
	declare continue handler for not found set no_more_rows = true;


	open cur_usuarios;
		select FOUND_ROWS() into num_rows;
			el_lazo: loop
				fetch cur_usuarios into idAdm;
				if no_more_rows then
					close cur_usuarios;
					leave el_lazo;
				end if;
				insert into tbl_colAdminComer (idComerc,idAdmin) values (idCom,idAdm);
			end loop el_lazo;



	update tbl_colComerPasar set fechaFin = unix_timestamp() where idcomercio = idCom and fechaFin = 2863700400;

    if (length(pasarIn) > 0 or length(pasarMm) > 0) then

        el_lazo2: loop
            if LOCATE(',', pasarIn) = 0 then
				if length(pasarIn) > 0 then
					call fn_inspasar (idCom, pasarIn, 0);
					if length(pasarMm) > 0 then
						set pasarIn = pasarMm;
						set pasarMm = '';
						iterate el_lazo2;
					end if;
				end if;

				leave el_lazo2;

			else
 				select substring_index(pasarIn, ',', 1) into pas;
				call fn_inspasar (idCom, pas, tipo);
				select concat(',', pasarIn) into pasarIn;
				select replace(pasarIn, concat(',',pas,','), '') into pasarIn;
			end if;
        end loop el_lazo2;

    end if;

end
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `tr_uptcomer`;
DELIMITER $$
CREATE TRIGGER `tr_uptcomer` BEFORE UPDATE ON `tbl_comercio` FOR EACH ROW begin
	declare idCom int default old.id;
    declare pasarIn varchar(100) default new.pasarela;
    declare pasarMm varchar(100) default new.pasarelaAlMom;
    declare pas int;
	declare tipo int default 0;

	if (new.activo != old.activo) then call estadComer(idCom, new.activo); end if;



	update tbl_colComerPasar set fechaFin = unix_timestamp() where idcomercio = idCom and fechaFin = 2863700400;

    if (length(pasarIn) > 0 or length(pasarMm) > 0) then

        el_lazo2: loop
            if LOCATE(',', pasarIn) = 0 then
				if length(pasarIn) > 0 then
					call fn_inspasar (idCom, pasarIn, tipo);
					if length(pasarMm) > 0 then
						set pasarIn = pasarMm;
						set pasarMm = '';
						set tipo = 1;
						iterate el_lazo2;
					end if;
				end if;

				leave el_lazo2;

			else
 				select substring_index(pasarIn, ',', 1) into pas;
				call fn_inspasar (idCom, pas, tipo);
				select concat(',', pasarIn) into pasarIn;
				select replace(pasarIn, concat(',',pas,','), '') into pasarIn;
			end if;
        end loop el_lazo2;

    end if;
end
$$
DELIMITER ;


DROP TRIGGER IF EXISTS `tbl_colPasarComTran_bi`;
DROP TRIGGER IF EXISTS `tbl_colPasarComTran_bu`;
delimiter ;;
CREATE TRIGGER `tbl_colPasarComTran_bi` BEFORE INSERT ON `tbl_colPasarComTran` FOR EACH ROW
begin
  if (new.fecha is null) then
    set new.fecha = unix_timestamp();
  end if;
end;;

CREATE TRIGGER `tbl_colPasarComTran_bu` BEFORE UPDATE ON `tbl_colPasarComTran` FOR EACH ROW
begin
  if (new.fecha is null) then
    set new.fecha = unix_timestamp();
  end if;
end;;

delimiter ;
-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_ipblancas`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_ipblancas`;
CREATE TABLE `vw_ipblancas` (
`comercio` varchar(100)
,`admin` varchar(100)
,`fecha` int(11)
,`fechaM` varchar(21)
,`ip` varchar(25)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_transacciones`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_transacciones`;
CREATE TABLE `vw_transacciones` (
`id` varchar(14)
,`comercio` varchar(100)
,`tasaM` double(21,4)
,`estado` char(1)
,`fecha` int(11)
,`fecha_mod` int(11)
,`valIni` decimal(13,2)
,`idcomercio` varchar(20)
,`pasarela` smallint(3)
,`tipoE` char(1)
,`idmoneda` char(3)
,`codigo` varchar(12)
,`error` varchar(400)
,`tasaDev` double(21,4)
,`pasarelaN` varchar(100)
,`moneda` char(3)
,`euroEquiv` varchar(19)
,`identificador` varchar(101)
,`color{col}` varchar(5)
,`ip` varchar(17)
,`geo{geoip}` varchar(17)
,`pagada` varchar(2)
,`valor` decimal(15,2)
,`tipoEntorno` varchar(17)
,`estad` varchar(12)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_transaccionesCSV`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_transaccionesCSV`;
CREATE TABLE `vw_transaccionesCSV` (
`id` varchar(14)
,`comercio` varchar(100)
,`tasaM` double(21,4)
,`estado` char(1)
,`fecha` int(11)
,`fecha_mod` int(11)
,`valIni` decimal(13,2)
,`idcomercio` varchar(20)
,`solDev` tinyint(1)
,`pasarela` smallint(3)
,`tipoE` char(1)
,`idmoneda` char(3)
,`codigo` varchar(12)
,`error` varchar(400)
,`tasaDev` double(21,4)
,`pasarelaN` varchar(100)
,`moneda` char(3)
,`identificador` varchar(20)
,`euroEquiv` varchar(19)
,`valorDev` decimal(14,2)
,`cliente` varchar(200)
,`color{col}` varchar(5)
,`ip` varchar(17)
,`geo{geoip}` varchar(17)
,`pagada` varchar(2)
,`valor` decimal(15,2)
,`tipoEntorno` varchar(17)
,`estad` varchar(12)
);

-- --------------------------------------------------------

--
-- Structure for view `bitacora`
--
DROP TABLE IF EXISTS `bitacora`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `bitacora`  AS  select `a`.`login` AS `login`,`b`.`texto` AS `texto`,from_unixtime(`b`.`fecha`) AS `fecha`,`a`.`idrol` AS `idrol`,(case `a`.`idcomercio` when _utf8'todos' then _utf8'todos' else (select `tbl_comercio`.`nombre` AS `nombre` from `tbl_comercio` where (`tbl_comercio`.`idcomercio` = `a`.`idcomercio`)) end) AS `comercio` from (`tbl_baticora` `b` join `tbl_admin` `a`) where ((`a`.`idadmin` = `b`.`idadmin`) and (`a`.`idrol` > 10) and (`a`.`login` like _utf8'%')) order by `a`.`login`,`b`.`fecha` desc ;

-- --------------------------------------------------------

--
-- Structure for view `listadoIp`
--
DROP TABLE IF EXISTS `listadoIp`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `listadoIp`  AS  select `tbl_listaIp`.`ip` AS `ip`,date_format(from_unixtime(`tbl_listaIp`.`fecha`),_utf8'%d/%m/%Y %H:%i') AS `fecha`,`tbl_listaIp`.`fecha` AS `fechatruc` from `tbl_listaIp` ;

-- --------------------------------------------------------

--
-- Structure for view `listadoTransacciones`
--
DROP TABLE IF EXISTS `listadoTransacciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `listadoTransacciones`  AS  select `t`.`fecha_mod` AS `fecha_mod`,`c`.`nombre` AS `comercio`,date_format(from_unixtime(`t`.`fecha_mod`),_utf8'%d/%m/%y %H:%i') AS `fecha`,`t`.`idtransaccion` AS `idtransaccion`,(`t`.`valor` / 100) AS `valor`,`t`.`moneda` AS `moneda`,`t`.`estado` AS `estado`,format(((`t`.`valor` / 100) / `t`.`tasa`),2) AS `euros` from (`tbl_transacciones` `t` join `tbl_comercio` `c`) where (`t`.`idcomercio` = `c`.`idcomercio`) order by `t`.`fecha_mod` desc ;

-- --------------------------------------------------------

--
-- Structure for view `mobile_clientes`
--
DROP TABLE IF EXISTS `mobile_clientes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `mobile_clientes`  AS  select `r`.`id_reserva` AS `id_reserva`,`r`.`id_transaccion` AS `id_transaccion`,`c`.`nombre` AS `comercio`,`a`.`nombre` AS `administr`,(case `r`.`est_comer` when _utf8'D' then _utf8'Desarrollo' else _utf8'Producción' end) AS `entorno`,`r`.`codigo` AS `identificador`,`p`.`nombre` AS `pasarela`,`r`.`nombre` AS `cliente`,`r`.`email` AS `email`,`r`.`servicio` AS `servicio`,`r`.`valor_inicial` AS `valor_inicial`,`r`.`valor` AS `valor`,`m`.`moneda` AS `moneda`,date_format(from_unixtime(`r`.`fecha`),_utf8'%d/%m/%Y %H:%i') AS `fechaI`,`r`.`bankId` AS `bankId`,date_format(from_unixtime(`r`.`fechaPagada`),_utf8'%d/%m/%Y %H:%i') AS `fechaII`,date_format(from_unixtime(`r`.`fechaCancel`),_utf8'%d/%m/%Y %H:%i') AS `fechaIII`,`r`.`fecha` AS `fecha`,(case `r`.`pMomento` when _utf8'S' then _utf8'Al momento' else _utf8'Diferido' end) AS `pMom`,(case `r`.`estado` when _utf8'P' then _utf8'En Proceso' when _utf8'A' then _utf8'Aceptada' when _utf8'D' then _utf8'Denegada' when _utf8'N' then _utf8'No Procesada' when _utf8'B' then _utf8'Anulada' else _utf8'Devuelta' end) AS `estadoTr`,(case `r`.`estado` when _utf8'P' then _utf8'cssPen' when _utf8'A' then _utf8'cssAce' when _utf8'D' then _utf8'cssDen' when _utf8'N' then _utf8'cssNop' when _utf8'B' then _utf8'cssCan' else _utf8'CssDev' end) AS `classe`,`r`.`tiempoV` AS `tiempoV`,`r`.`id_comercio` AS `id_comercio`,`c`.`id` AS `idcom` from ((((`tbl_reserva` `r` join `tbl_comercio` `c`) join `tbl_admin` `a`) join `tbl_pasarela` `p`) join `tbl_moneda` `m`) where ((`r`.`id_comercio` = `c`.`idcomercio`) and (`r`.`id_admin` = `a`.`idadmin`) and (`r`.`pasarela` = `p`.`idPasarela`) and (`r`.`moneda` = `m`.`idmoneda`)) order by `r`.`fecha` ;

-- --------------------------------------------------------

--
-- Structure for view `mobile_tickets`
--
DROP TABLE IF EXISTS `mobile_tickets`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `mobile_tickets`  AS  select `a`.`nombre` AS `usuario`,`a`.`email` AS `email`,`t`.`fechaModificada` AS `fechaModificada`,date_format(from_unixtime(`t`.`fechaModificada`),_utf8'%d/%m/%Y %H:%i') AS `fecha`,`t`.`asunto` AS `asunto`,(case `t`.`estado` when _utf8'T' then _utf8'Terminado' else _utf8'Activo' end) AS `estadoP`,`t`.`texto` AS `texto`,`a`.`idrol` AS `idrol`,`t`.`idticket` AS `idticket`,`t`.`idadmin` AS `idadmin` from (`tbl_ticket` `t` join `tbl_admin` `a`) where (`t`.`idadmin` = `a`.`idadmin`) order by `t`.`estado`,`t`.`fechaModificada` desc ;

-- --------------------------------------------------------

--
-- Structure for view `mobile_transacciones`
--
DROP TABLE IF EXISTS `mobile_transacciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `mobile_transacciones`  AS  select `t`.`idtransaccion` AS `idtransaccion`,`c`.`nombre` AS `comercio`,`t`.`identificador` AS `identificador`,date_format(from_unixtime(`t`.`fecha_mod`),_utf8'%d/%m/%Y %H:%i') AS `fechaFin`,`m`.`moneda` AS `moneda`,`t`.`fecha_mod` AS `fecha_mod`,round((`t`.`valor_inicial` / 100),2) AS `valorIni`,date_format(from_unixtime(`t`.`fecha`),_utf8'%d/%m/%Y %H:%i') AS `fechaIni`,`t`.`codigo` AS `codigo`,`t`.`fecha` AS `fecha`,`t`.`idcomercio` AS `idcomercio`,`c`.`id` AS `idcom`,round(`t`.`tasa`,4) AS `tasaM`,(case `t`.`estado` when _utf8'B' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'V' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'A' then round(((`t`.`valor` / 100) / `t`.`tasa`),2) else _utf8'0.00' end) AS `euroEquiv`,(case `t`.`estado` when _utf8'B' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),2),round((`t`.`valor` / 100),2)) when _utf8'V' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),2),round((`t`.`valor` / 100),2)) when _utf8'A' then round((`t`.`valor` / 100),2) else _utf8'0.00' end) AS `valorFin`,(case `t`.`estado` when _utf8'P' then _utf8'En Proceso' when _utf8'A' then _utf8'Aceptada' when _utf8'D' then _utf8'Denegada' when _utf8'N' then _utf8'No Procesada' when _utf8'B' then _utf8'Anulada' else _utf8'Devuelta' end) AS `estadoTr`,(case `t`.`tipoEntorno` when _utf8'P' then _utf8'Producci&oacute;n' else _utf8'Desarrollo' end) AS `tipoEntorno`,(case `t`.`estado` when _utf8'P' then _utf8'cssPen' when _utf8'A' then _utf8'cssAce' when _utf8'D' then _utf8'cssDen' when _utf8'N' then _utf8'cssNop' when _utf8'B' then _utf8'cssCan' else _utf8'CssDev' end) AS `classe`,(case `t`.`id_error` when NULL then _utf8'-' when _utf8'' then _utf8'-' else `t`.`id_error` end) AS `error`,(case `t`.`pasarela` when _utf8'0' then NULL else (select `p`.`nombre` AS `nombre` from `tbl_pasarela` `p` where ((`p`.`idPasarela` = `t`.`pasarela`) and (`p`.`idPasarela` = `t`.`pasarela`))) end) AS `pasarl`,(case `t`.`ip` when _utf8'127.0.0.1' then _utf8'no record' else `t`.`ip` end) AS `ip`,(case `t`.`pago` when 0 then _utf8'No' else _utf8'Si' end) AS `tipoPag` from (((`tbl_transacciones` `t` join `tbl_comercio` `c`) join `tbl_pasarela` `p`) join `tbl_moneda` `m`) where ((`t`.`idcomercio` = `c`.`idcomercio`) and (`t`.`pasarela` = `p`.`idPasarela`) and (`t`.`moneda` = `m`.`idmoneda`)) order by `t`.`fecha_mod` desc ;

-- --------------------------------------------------------

--
-- Structure for view `mobile_users`
--
DROP TABLE IF EXISTS `mobile_users`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `mobile_users`  AS  select `a`.`nombre` AS `nombre`,`a`.`email` AS `email`,`a`.`login` AS `login`,date_format(from_unixtime(`a`.`fecha`),_utf8'%d/%m/%Y') AS `fechaA`,date_format(from_unixtime(`a`.`fecha_visita`),_utf8'%d/%m/%Y %H:%i') AS `fechaV`,replace(`a`.`param`,_utf8'idioma=',_utf8'') AS `idiom`,`a`.`idrol` AS `idrol`,`r`.`nombre` AS `rol`,`a`.`idadmin` AS `idadmin`,(case when ((select count(0) AS `count(*)` from (`tbl_colAdminComer` `c` join `tbl_comercio` `o`) where ((`c`.`idAdmin` = `a`.`idadmin`) and (`c`.`idComerc` = `o`.`id`))) = (select count(0) AS `count(*)` from `tbl_comercio`)) then _utf8'todos' when ((select count(0) AS `count(*)` from (`tbl_colAdminComer` `c` join `tbl_comercio` `o`) where ((`c`.`idAdmin` = `a`.`idadmin`) and (`c`.`idComerc` = `o`.`id`))) = 1) then (select `o`.`nombre` AS `nombre` from (`tbl_colAdminComer` `c` join `tbl_comercio` `o`) where ((`c`.`idAdmin` = `a`.`idadmin`) and (`c`.`idComerc` = `o`.`id`))) else _utf8'varios' end) AS `comercio`,`a`.`fecha` AS `fecha`,(select group_concat(`c`.`idComerc` separator ',') AS `group_concat(c.idComerc separator ',')` from `tbl_colAdminComer` `c` where (`a`.`idadmin` = `c`.`idAdmin`)) AS `idcom`,(case `a`.`activo` when _utf8'S' then _utf8'Si' else _utf8'No' end) AS `activo` from (`tbl_admin` `a` join `tbl_roles` `r`) where (`r`.`idrol` = `a`.`idrol`) order by `a`.`fecha` desc ;

-- --------------------------------------------------------

--
-- Structure for view `vw_ipblancas`
--
DROP TABLE IF EXISTS `vw_ipblancas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vw_ipblancas`  AS  select `c`.`nombre` AS `comercio`,`a`.`nombre` AS `admin`,`i`.`fecha` AS `fecha`,date_format(from_unixtime(`i`.`fecha`),_utf8'%d/%m/%Y %H:%i') AS `fechaM`,`i`.`ip` AS `ip` from ((`tbl_ipblancas` `i` join `tbl_comercio` `c`) join `tbl_admin` `a`) where ((`i`.`idAdmin` = `a`.`idadmin`) and (`i`.`idComercio` = `c`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_transacciones`
--
DROP TABLE IF EXISTS `vw_transacciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vw_transacciones`  AS  select `t`.`idtransaccion` AS `id`,`c`.`nombre` AS `comercio`,round(`t`.`tasa`,4) AS `tasaM`,`t`.`estado` AS `estado`,`t`.`fecha` AS `fecha`,`t`.`fecha_mod` AS `fecha_mod`,round((`t`.`valor_inicial` / 100),2) AS `valIni`,`c`.`idcomercio` AS `idcomercio`,`t`.`pasarela` AS `pasarela`,`t`.`tipoEntorno` AS `tipoE`,`t`.`moneda` AS `idmoneda`,`t`.`codigo` AS `codigo`,`t`.`id_error` AS `error`,round(`t`.`tasaDev`,4) AS `tasaDev`,`p`.`nombre` AS `pasarelaN`,`m`.`moneda` AS `moneda`,(case `t`.`estado` when _utf8'B' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'V' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'A' then round(((`t`.`valor` / 100) / `t`.`tasa`),2) else _utf8'0.00' end) AS `euroEquiv`,(case (select count(0) AS `count(*)` from `tbl_reserva` `r` where (`r`.`codigo` = `t`.`identificador`)) when 1 then concat(_utf8'<a href="index.php?componente=comercio&pag=cliente&val=',`t`.`identificador`,_utf8'">',`t`.`identificador`,_utf8'</a>') else `t`.`identificador` end) AS `identificador`,(case `t`.`estado` when _utf8'P' then _utf8'green' when _utf8'A' then _utf8'black' when _utf8'D' then _utf8'red' when _utf8'N' then _utf8'red' when _utf8'B' then _utf8'blue' else _utf8'blue' end) AS `color{col}`,(case `t`.`ip` when _utf8'127.0.0.1' then _utf8'no record' else `t`.`ip` end) AS `ip`,(case `t`.`ip` when _utf8'127.0.0.1' then _utf8'no record' else `t`.`ip` end) AS `geo{geoip}`,(case `t`.`pago` when 0 then _utf8'No' else _utf8'Si' end) AS `pagada`,(case `t`.`estado` when _utf8'B' then round(if((`t`.`fecha_mod` < `t`.`fechaPagada`),(-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),(`t`.`valor` / 100)),2) when _utf8'V' then round(if((`t`.`fecha_mod` < `t`.`fechaPagada`),(-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),(`t`.`valor` / 100)),2) else round((`t`.`valor` / 100),2) end) AS `valor`,(case `t`.`tipoEntorno` when _utf8'P' then _utf8'Producci&oacute;n' else _utf8'Desarrollo' end) AS `tipoEntorno`,(case `t`.`estado` when _utf8'P' then _utf8'En Proceso' when _utf8'A' then _utf8'Aceptada' when _utf8'D' then _utf8'Denegada' when _utf8'N' then _utf8'No Procesada' when _utf8'B' then _utf8'Anulada' else _utf8'Devuelta' end) AS `estad` from (((`tbl_transacciones` `t` join `tbl_comercio` `c`) join `tbl_moneda` `m`) join `tbl_pasarela` `p`) where ((`c`.`idcomercio` = `t`.`idcomercio`) and (`t`.`moneda` = `m`.`idmoneda`) and (`p`.`idPasarela` = `t`.`pasarela`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_transaccionesCSV`
--
DROP TABLE IF EXISTS `vw_transaccionesCSV`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vw_transaccionesCSV`  AS  select `t`.`idtransaccion` AS `id`,`c`.`nombre` AS `comercio`,round(`t`.`tasa`,4) AS `tasaM`,`t`.`estado` AS `estado`,`t`.`fecha` AS `fecha`,`t`.`fecha_mod` AS `fecha_mod`,round((`t`.`valor_inicial` / 100),2) AS `valIni`,`c`.`idcomercio` AS `idcomercio`,`t`.`solDev` AS `solDev`,`t`.`pasarela` AS `pasarela`,`t`.`tipoEntorno` AS `tipoE`,`t`.`moneda` AS `idmoneda`,`t`.`codigo` AS `codigo`,`t`.`id_error` AS `error`,round(`t`.`tasaDev`,4) AS `tasaDev`,`p`.`nombre` AS `pasarelaN`,`m`.`moneda` AS `moneda`,`t`.`identificador` AS `identificador`,(case `t`.`estado` when _utf8'B' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'V' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'A' then round(((`t`.`valor` / 100) / `t`.`tasa`),2) else _utf8'0.00' end) AS `euroEquiv`,(case `t`.`estado` when _utf8'B' then round(((`t`.`valor_inicial` - `t`.`valor`) / 100),2) when _utf8'V' then round(((`t`.`valor_inicial` - `t`.`valor`) / 100),2) else 0 end) AS `valorDev`,(case (select count(0) AS `count(*)` from `tbl_reserva` `r` where (`r`.`codigo` = `t`.`identificador`)) when 1 then (select `r`.`nombre` AS `nombre` from `tbl_reserva` `r` where (`r`.`codigo` = `t`.`identificador`)) else _utf8' - ' end) AS `cliente`,(case `t`.`estado` when _utf8'P' then _utf8'green' when _utf8'A' then _utf8'black' when _utf8'D' then _utf8'red' when _utf8'N' then _utf8'red' when _utf8'B' then _utf8'blue' else _utf8'blue' end) AS `color{col}`,(case `t`.`ip` when _utf8'127.0.0.1' then _utf8'no record' else `t`.`ip` end) AS `ip`,(case `t`.`ip` when _utf8'127.0.0.1' then _utf8'no record' else `t`.`ip` end) AS `geo{geoip}`,(case `t`.`pago` when 0 then _utf8'No' else _utf8'Si' end) AS `pagada`,(case `t`.`estado` when _utf8'B' then round(if((`t`.`fecha_mod` < `t`.`fechaPagada`),(-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),(`t`.`valor` / 100)),2) when _utf8'V' then round(if((`t`.`fecha_mod` < `t`.`fechaPagada`),(-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),(`t`.`valor` / 100)),2) else round((`t`.`valor` / 100),2) end) AS `valor`,(case `t`.`tipoEntorno` when _utf8'P' then _utf8'Producci&oacute;n' else _utf8'Desarrollo' end) AS `tipoEntorno`,(case `t`.`estado` when _utf8'P' then _utf8'En Proceso' when _utf8'A' then _utf8'Aceptada' when _utf8'D' then _utf8'Denegada' when _utf8'N' then _utf8'No Procesada' when _utf8'B' then _utf8'Anulada' else _utf8'Devuelta' end) AS `estad` from (((`tbl_transacciones` `t` join `tbl_comercio` `c`) join `tbl_moneda` `m`) join `tbl_pasarela` `p`) where ((`c`.`idcomercio` = `t`.`idcomercio`) and (`t`.`moneda` = `m`.`idmoneda`) and (`p`.`idPasarela` = `t`.`pasarela`)) ;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
