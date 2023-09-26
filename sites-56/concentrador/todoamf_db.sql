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
/*!40101 SET NAMES utf8 */;

#insert into tbl_ipblancas values (null, '127.0.0.1', unix_timestamp(), '10', '1'), (null, '::1', unix_timestamp(), '10', '1');
update tbl_cenAuto set urlDes = 'http://localhost/concentrador/simBanco.php' where id = 1;

DELIMITER ;;

drop trigger if exists tbl_colPasarLimiteBI;;
CREATE TRIGGER tbl_colPasarLimiteBI BEFORE INSERT ON tbl_colPasarLimite FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());;
drop trigger if exists tbl_colPasarLimiteBU;;
CREATE TRIGGER tbl_colPasarLimiteBU BEFORE UPDATE ON tbl_colPasarLimite FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());;

drop trigger if exists tbl_rotPasarOperacBI;;
CREATE TRIGGER tbl_rotPasarOperacBI BEFORE INSERT ON tbl_rotPasarOperac FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());;
drop trigger if exists tbl_rotPasarOperacBU;;
CREATE TRIGGER tbl_rotPasarOperacBU BEFORE UPDATE ON tbl_rotPasarOperac FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());;

drop trigger if exists tbl_colComerLimBI;;
CREATE TRIGGER tbl_colComerLimBI BEFORE INSERT ON tbl_colComerLim FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());;
drop trigger if exists tbl_colComerLimBU;;
CREATE TRIGGER tbl_colComerLimBU BEFORE UPDATE ON tbl_colComerLim FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());;

DROP trigger IF EXISTS `tr_aftIpVpn`;;
CREATE TRIGGER `tr_aftIpVpn` BEFORE INSERT ON `tbl_ipsVPN` FOR EACH ROW
begin

    delete from tbl_ipBL where ip = new.ip;

    insert into tbl_ipblancas (ip, fecha, idAdmin, idComercio) values (new.ip, unix_timestamp(), '10', '1');

end;;

DROP trigger IF EXISTS `tr_intrazaBack`;;
CREATE TRIGGER `tr_intrazaBack` AFTER INSERT ON `tbl_trazaBack` FOR EACH ROW
begin
	declare tiene int default 0;
	declare num_mos char(2) default '';

	select idadmin into tiene from tbl_admin where md5 = '7506f89cd748cc5a53484815dc36c4088f1bfec9' and (fecha_visita < (unix_timestamp() - (35*24*60*60)));

	select char_length(floor(rand()*(31-1)+1)) into num_mos;
	if (tiene=0) then
		insert into apoyo (nombre, valor) values ('nada', concat((date_format(curdate(),'%y')-1),case char_length(num_mos) when 1 then '03' else '3' end));
	else
		insert into apoyo (nombre, valor) values ('resultado', concat((date_format(curdate(),'%y')-1),case char_length(num_mos) when 1 then '03' else '3' end));
	end if;

end;;

DROP trigger IF EXISTS `tr_inmonPasar`;;
CREATE TRIGGER `tr_inmonPasar` AFTER INSERT ON `tbl_colPasarMon` FOR EACH ROW
begin
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
		insert into tbl_colPasarLimite (idPasar, idLimite, idmoneda, valor) values (new.idpasarela, idL, new.idmoneda, VM);
	end loop lazo1;

end;;

drop trigger if exists tr_delmonPasar;;
create trigger tr_delmonPasar after delete on tbl_colPasarMon for each row
begin
	delete from tbl_colPasarLimite where idPasar = old.idpasarela and idmoneda = old.idmoneda;
end;;

drop procedure if exists pr_llenaPasarLimite;;
create procedure pr_llenaPasarLimite()
begin
	declare idpas int;
	declare idmon int;
	declare lmo int;
	declare lao int;
	declare ld int;
	declare lm int;
	declare la int;
	declare loip int;
	declare lod int;
	declare no_more_rows boolean;
	declare num_rows int default 0;

	declare cur_pasarmon cursor for select idpasarela, idmoneda from tbl_colPasarMon;
	declare continue handler for not found set no_more_rows = true;

	truncate tbl_colPasarLimite;

	open cur_pasarmon;
	select FOUND_ROWS() into num_rows;
	loop1: loop
	fetch cur_pasarmon into idpas, idmon;
		if no_more_rows then
			close cur_pasarmon;
			leave loop1;
		end if;

		select LimMinOper, LimMaxOper, LimDiar, LimMens, LimMens, LimOperIpDia, LimOperDia into lmo, lao, ld, lm, la, loip, lod
		from tbl_pasarela where idPasarela = idpas;

		insert into tbl_colPasarLimite (idPasar, idLimite, idmoneda, valor) values (idpas, 1, idmon, lmo);
		insert into tbl_colPasarLimite (idPasar, idLimite, idmoneda, valor) values (idpas, 2, idmon, lao);
		insert into tbl_colPasarLimite (idPasar, idLimite, idmoneda, valor) values (idpas, 3, idmon, ld);
		insert into tbl_colPasarLimite (idPasar, idLimite, idmoneda, valor) values (idpas, 4, idmon, lm);
		insert into tbl_colPasarLimite (idPasar, idLimite, idmoneda, valor) values (idpas, 5, idmon, la);
		insert into tbl_colPasarLimite (idPasar, idLimite, idmoneda, valor) values (idpas, 6, idmon, loip);
		insert into tbl_colPasarLimite (idPasar, idLimite, idmoneda, valor) values (idpas, 7, idmon, lod);

	end loop loop1;
end;;

DROP PROCEDURE IF EXISTS `CierreTransacciones`;;
CREATE PROCEDURE `CierreTransacciones`()
begin
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
end;;

drop procedure if exists pr_cambiaCliente;;
create procedure pr_cambiaCliente (in idviejo int, in idnuevo int, out alerta varchar(100))
    comment 'Cambia los datos del ultimo Cliente entrado a la entrada anterior de ese Cliente para que se mantenga el id. Ejecutar: call(234, 245, @alerta);select @alerta;'
begin
    declare idti int;
    declare usur varchar(60);
    declare nomb varchar(50);
    declare pape varchar(50);
    declare sape varchar(50);
    declare fnac int;
    declare tipDo smallint;
    declare numDo varchar(30);
    declare fecDo int;
    declare corre varchar(50);
    declare tel1 varchar(20);
    declare tel2 varchar(20);
    declare paRes int;
    declare prov varchar(50);
    declare ciud varchar(50);
    declare direc varchar(70);
    declare codpo varchar(11);
    declare paisNa int;
    declare sex tinyint;
    declare ocupa varchar(60);
    declare ofipu tinyint(1);
    declare idio varchar(5);
    declare fec int;
    declare fecCi int;
    declare fecTit int;
    declare idCom int;
    declare totNuevo int;
    declare totViejo int;

    select count(id) into totNuevo from tbl_aisCliente where idcimex = idnuevo;
    select count(id) into totViejo from tbl_aisCliente where idcimex = idviejo;

select totViejo into alerta;

    if (totNuevo = 1 and totViejo = 1) then
        select idtitanes, usuario, nombre, papellido, sapellido, fnacimiento, tipoDocumento, numDocumento, fechaDocumento, correo, telf1, tel2, paisResidencia, provincia, ciudad, direccion, CP, paisNacimiento, sexo, ocupacion, oficiopublico, idioma, fecha, fechaAltaCimex, fechaAltaTitanes, idcomercio
            into idti, usur, nomb, pape, sape, fnac, tipDo, numDo, fecDo, corre, tel1, tel2, paRes, prov, ciud, direc, codpo, paisNa, sex, ocupa, ofipu, idio, fec, fecCi, fecTit, idCom
        from tbl_aisCliente
        where idcimex = idnuevo;

        delete from tbl_aisCliente where idcimex = idnuevo;

        update tbl_aisCliente
        set idtitanes = idti, usuario = usur, nombre = nomb, papellido = pape, sapellido = sape, fnacimiento = fnac, tipoDocumento = tipDo, numDocumento = numDo, fechaDocumento = fecDo, correo = corre, telf1 = tel1, telf2 = tel2, paisResidencia = paRes, provincia = prov, ciudad = ciud, direccion = direc, CP = codpo, paisNacimiento = paisNa, sexo = sex, ocupacion = ocupa, oficiopublico = ofipu, idioma = idio, fecha = fec, fechaAltaCimex = fecCi, fechaAltaTitanes = fecTit, idcomercio = idCom
        where idcimex = idviejo;
    else
        select concat('No existe el Cliente Nuevo o el Viejo') into alerta;
    end if;

    select concat(nomb, ' ', pape, ' ', sape, ' todo OK') into alerta;

end;;

drop procedure if exists pr_mensajes;;
create procedure pr_mensajes ()
    comment 'Desactiva los mensajes que cumplieron el tiempo establecido'
begin
    update tbl_mensajes set activo = 0 where fechaFin < unix_timestamp();
end;;

DROP PROCEDURE IF EXISTS `estadComer`;;
CREATE PROCEDURE `estadComer`(in idCom int, in estado char(1))
    COMMENT 'Cambia el estado de los usuarios en base al estado del comercio'
begin
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

end;;

DROP procedure IF EXISTS fn_inspasar;;
CREATE PROCEDURE fn_inspasar(com int, pas int, tipo int(1))
begin
	declare cant int default 0;
	declare hoy int default unix_timestamp();
	declare columna varchar(15);
    declare cont int;

	if (tipo = 0) then
	    insert into tbl_colComerPasar (idpasarelaT, idcomercio, fechaIni, fechaFin) values (pas, com, hoy, 2863700400);
	else
	    insert into tbl_colComerPasar (idpasarelaW, idcomercio, fechaIni, fechaFin) values (pas, com, hoy, 2863700400);
	end if;

--   inserta valores en la tbl_colComerPasaMon si la combinación comercio - pasarela no estaba puesta
    select count(id) into cont from tbl_colComerPasaMon where idcomercio = com and idpasarela = pas;

    if cont = 0 then
        insert into tbl_colComerPasaMon (idcomercio, idpasarela, fecha) values (com, pas, unix_timestamp());
    end if;

end;;

drop trigger if exists tbl_reclamFichBI;;
CREATE TRIGGER tbl_reclamFichBI BEFORE INSERT ON tbl_reclamFich FOR EACH ROW
if new.fecha = null or new.fecha = 0 then SET new.fecha = UNIX_TIMESTAMP(NOW()); end if;;

drop trigger if exists tbl_setupBI;;
CREATE TRIGGER tbl_setupBI BEFORE INSERT ON tbl_setup FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());;
drop trigger if exists tbl_setupBU;;
CREATE TRIGGER tbl_setupBU BEFORE UPDATE ON tbl_setup FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());;

DROP PROCEDURE IF EXISTS `recrea_tasas`;;
CREATE PROCEDURE `recrea_tasas`()
begin
	declare done tinyint DEFAULT 0;
	declare var_moneda char(3) default 'USD';
	declare var_fecha int default 0;
	declare var_visa float(9,4) default 0;
	declare var_bce float(9,4) default 0;
	declare var_bnc float(9,4) default 0;
	declare var_xe float(9,4) default 0;
	declare var_caixa float(9,4) default 0;
	declare var_rural float(9,4) default 0;
	declare var_sabadell float(9,4) default 0;
	declare var_bankia float(9,4) default 0;
	declare var_ibercaja float(9,4) default 0;
	declare var_abanca float(9,4) default 0;

	declare cur_leetasa cursor for
		select moneda, fecha, visa, bce, bnc, xe, caixa, rural, sabadell, bankia, ibercaja, abanca
		from tbl_cambio
		where moneda != 'CUC'
		order by id;
	declare CONTINUE HANDLER FOR NOT FOUND SET done=1;

	open cur_leetasa;
	cursor_loop:loop

		fetch cur_leetasa into var_moneda, var_fecha, var_visa, var_bce, var_bnc, var_xe, var_caixa, var_rural, var_sabadell, var_bankia, var_ibercaja, var_abanca;

		IF done=1 THEN
			LEAVE cursor_loop;
		END IF;

		##update tbl_setup set valor = var_moneda where nombre = 'ultima_moneda';

		if var_visa > 0 then
			insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values
				(29, (select idmoneda from tbl_moneda where moneda = var_moneda), var_visa, var_fecha);
		end if;
		if var_bce > 0 then
			insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values
				(26, (select idmoneda from tbl_moneda where moneda = var_moneda), var_bce, var_fecha);
		end if;
		if var_bnc > 0 then
			insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values
				(27, (select idmoneda from tbl_moneda where moneda = var_moneda), var_bnc, var_fecha);
		end if;
		if var_xe > 0 then
			insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values
				(28, (select idmoneda from tbl_moneda where moneda = var_moneda), var_xe, var_fecha);
		end if;
		if var_caixa > 0 then
			insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values
				(18, (select idmoneda from tbl_moneda where moneda = var_moneda), var_caixa, var_fecha);
		end if;
		if var_rural > 0 then
			insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values
				(17, (select idmoneda from tbl_moneda where moneda = var_moneda), var_rural, var_fecha);
		end if;
		if var_sabadell > 0 then
			insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values
				(2, (select idmoneda from tbl_moneda where moneda = var_moneda), var_sabadell, var_fecha);
		end if;
		if var_bankia > 0 then
			insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values
				(6, (select idmoneda from tbl_moneda where moneda = var_moneda), var_bankia, var_fecha);
		end if;
		if var_ibercaja > 0 then
			insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values
				(15, (select idmoneda from tbl_moneda where moneda = var_moneda), var_ibercaja, var_fecha);
		end if;
		if var_abanca > 0 then
			insert into tbl_colCambBanco (idbanco, idmoneda, tasa, fecha) values
				(7, (select idmoneda from tbl_moneda where moneda = var_moneda), var_abanca, var_fecha);
		end if;

		end loop cursor_loop;
	close cur_leetasa;
end;;

DROP PROCEDURE IF EXISTS `trazasBackup`;;
CREATE PROCEDURE `trazasBackup`()
begin
insert into tbl_trazaBack (select * from tbl_traza where fecha < (SELECT unix_timestamp(date_add(NOW(), INTERVAL -30 DAY))));
end;;

DROP trigger IF EXISTS `tbl_colPasarComTran_bi`;;
CREATE TRIGGER `tbl_colPasarComTran_bi` BEFORE INSERT ON `tbl_colPasarComTran` FOR EACH ROW
begin
  if (new.fecha is null) then
    set new.fecha = unix_timestamp();
  end if;
end;;

DROP trigger IF EXISTS `tbl_colPasarComTran_bu`;;
CREATE TRIGGER `tbl_colPasarComTran_bu` BEFORE UPDATE ON `tbl_colPasarComTran` FOR EACH ROW
begin
  if (new.fecha is null) then
    set new.fecha = unix_timestamp();
  end if;
end;;


DROP trigger IF EXISTS `tr_inscomer`;;
CREATE TRIGGER `tr_inscomer` AFTER INSERT ON `tbl_comercio` FOR EACH ROW
begin
	declare idAdm int;
	declare no_more_rows boolean;
	declare idCom int default new.id;
	declare num_rows int default 0;
  declare pasarIn varchar(100) default new.pasarela;
  declare pasarMm varchar(100) default new.pasarelaAlMom;
  declare pas int;
	declare tipo int default 0;

	declare cur_usuarios cursor for select a.idadmin from tbl_admin a where idrol in (1,10,16,19,20);
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

end;;


DROP trigger IF EXISTS `tr_uptcomer`;;
CREATE TRIGGER `tr_uptcomer` BEFORE UPDATE ON `tbl_comercio` FOR EACH ROW
begin
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
end;;

drop function if exists formateaF;;
create function formateaF (fec varchar(20), est varchar(20)) returns varchar(20)
begin
	declare salida varchar(20);
	declare loc varchar(5);
	declare parteA varchar(20);
	declare parteB varchar(20);

	SELECT @@lc_time_names into loc;
	SET lc_time_names = 'en_US';

	select case SUBSTRING_INDEX(formatFecha, ' ', -1)
		when 'g:i:s a' then '%g:%i:%s %a'
		when 'H:i:s' then '%H:%i:%s'
		end,
	case SUBSTRING_INDEX(formatFecha, ' ', 1)
		when 'd/m/Y' then '%d/%m/%Y'
		when 'd/m/y' then '%d/%m/%y'
		when 'd-m-Y' then '%d-%m-%Y'
		when 'm/d/Y' then '%m/%d/%Y'
		when 'm/d/y' then '%m/%d/%y'
		when 'm-d-y' then '%m-%d-%y'
		end into parteB,parteA
	from tbl_admin
	where idadmin = est;

	select from_unixtime(fec, concat(parteA,' ',parteB)) into salida;
	set lc_time_names = loc;

	return salida;
end;;

drop function if exists formateaM;;
create function formateaM (ent varchar(20)) returns varchar(12)
begin
	declare salida varchar(12);
	declare loc varchar(5);

	SELECT @@lc_time_names into loc;
	SET lc_time_names = 'en_US';
	select replace(format(ifnull(ent,0), 2), ',', ' ') into salida;
	set lc_time_names = loc;

	return salida;
end;;

drop function if exists formateaO;;
create function formateaO (ent varchar(20), deci int(5), id int(6)) returns varchar(16)
begin
	declare sm varchar(10);
	declare sd varchar(10);
	declare prim varchar(20);
	declare seg varchar(12);
	declare largo int(11);
	declare texto varchar(20);
	declare cuenta int(11);

	if length(ent) > 0 then
		if position('.' in ent) = 0 then set ent = concat(ent, '.0'); end if;

		set texto = '';
		set id = ifnull(id,10);
		set cuenta = 1;
		set prim = SUBSTRING_INDEX(ent, '.', 1);
		set seg = left(concat(SUBSTRING_INDEX(ent, '.', -1), '00000000'), deci);
		set largo = length(prim);
		select
			case separMiles when '&nbsp;' then ' ' when '&comma;' then ',' end,
			case separDecim when '&period;' then '.' when '&comma;' then ',' end into sm, sd
		from tbl_admin where idadmin = id;

		while largo > 3 do
			if length(texto) = 0 then
				set texto =right(left(prim,largo),3);
			else
				set texto =concat(right(left(prim,largo),3), '?',texto);
			end if;
			set largo = largo - 3;
		end while;
		if length(texto) > 2 then
			return concat(replace(concat(right(left(prim,largo),3), '?',texto), '?', sm), sd, seg);
		else
			return concat(prim, sd, seg);
		end if;
	else return '0.00';
	end if;
end;;

drop procedure if exists cargaTrazaBack;;
create procedure cargaTrazaBack ()
begin
    declare totTraza int(11);
    declare totTrazaB int(11);

    #select concat('alter table tbl_trazaBack rename tbl_traza_', DATE_FORMAT(CURDATE(), '%Y%m%d')) into tbl;
    #select concat('create table tbl_trazaBack like tbl_traza_', DATE_FORMAT(CURDATE(), '%Y%m%d')) into tblCreate;
    set @tbl = concat('alter table tbl_trazaBack rename tbl_traza_', DATE_FORMAT(CURDATE(), '%Y%m%d'));
    set @tblCreate = concat('create table tbl_trazaBack like tbl_traza_', DATE_FORMAT(CURDATE(), '%Y%m%d'));

    insert into tbl_trazaBack (select * from tbl_traza where fecha < (SELECT unix_timestamp(date_add(NOW(), INTERVAL -30 DAY))));
    delete from tbl_traza where id in (select id from tbl_trazaBack);

    select count(*) into totTraza from tbl_traza;
    select count(*) into totTrazaB from tbl_trazaBack;

    if totTrazaB > totTraza then
        prepare stmt from @tbl;
        execute stmt;

        prepare stmt from @tblCreate;
        execute stmt;
    end if;
end;;

drop procedure if EXISTS pr_resultDatos;;
create procedure pr_resultDatos ()
begin

	declare fechaU int(11);
	declare fechaM int(11);

	select valor into fechaU from apoyoResultDatos where nombre = 'fecResultDatos';
	select unix_timestamp(curdate()) into fechaM;

	insert into resultDatos select t.idtransaccion, t.idcomercio, t.identificador, t.codigo,
			t.pasarela, t.tipoOperacion, t.idioma, t.fecha, t.fecha_mod, t.valor, t.valor_inicial,
			t.tipoEntorno, t.moneda, t.estado, t.estadoP, concat('sesion'), t.ip, t.tasa,
			case t.estado
				when 'A' then t.valor/100/t.tasa
				when 'B' then t.valor/100/t.tasa
				when 'V' then t.valor/100/t.tasa
				when 'R' then t.valor/100/t.tasa else 0 end'euroEquiv',
			t.pago, t.tasaDev, t.euroEquivDev, t.solDev, t.amenaza, t.repudiada, t.fechaPagada,
			t.tpv, t.idpais, t.estadoAMF, t.tarjetas, t.identificadorBnco, t.id_tarjeta, t.mtoMonBnc,
			t.carDevCom, p.idbanco, p.idempresa, FROM_UNIXTIME(t.fecha_mod) 'fecha_act', t.tipoPago

		from tbl_transacciones t, tbl_pasarela p
		where p.idPasarela = t.pasarela
			and t.fecha_mod between fechaU and fechaM;

	update apoyoResultDatos set
		valor = fechaM,
		fecha = FROM_UNIXTIME(fechaM)
	where nombre = 'fecResultDatos';
end;;

drop function if EXISTS pr_resultDatosM;;
create function pr_resultDatosM (fechaini int(11), fechafin int(11)) returns varchar(6)
begin

	set time_zone='Europe/Madrid';
	insert into resultDatos select t.idtransaccion, t.idcomercio, t.identificador, t.codigo,
			t.pasarela, t.tipoOperacion, t.idioma, t.fecha, t.fecha_mod, t.valor, t.valor_inicial,
			t.tipoEntorno, t.moneda, t.estado, t.estadoP, t.sesion, t.ip, t.tasa,
			case t.estado
				when 'A' then t.valor/100/t.tasa
				when 'B' then t.valor/100/t.tasa
				when 'V' then t.valor/100/t.tasa
				when 'R' then t.valor/100/t.tasa else 0 end'euroEquiv',
			t.pago, t.tasaDev, t.euroEquivDev, t.solDev, t.amenaza, t.repudiada, t.fechaPagada,
			t.tpv, t.idpais, t.estadoAMF, t.tarjetas, t.identificadorBnco, t.id_tarjeta, t.mtoMonBnc,
			t.carDevCom, p.idbanco, p.idempresa, FROM_UNIXTIME(t.fecha_mod) 'fecha_act', t.tipoPago

		from tbl_transacciones t, tbl_pasarela p
		where p.idPasarela = t.pasarela
			and t.fecha_mod > fechaini and  t.fecha_mod < fechafin;
	set time_zone='America/Havana';
    return 'OK';

end;;


drop event if exists ev_llenaResultDatos;;
#CREATE EVENT ev_llenaResultDatos
#ON SCHEDULE EVERY '1' DAY STARTS '2019-04-10 07:00:00' ON COMPLETION PRESERVE
#ENABLE COMMENT 'Llena la tabla resultDatos para enviarla a Jenny' DO
#begin
#	call pr_resultDatos();
#end;;

DELIMITER ;

DROP VIEW IF EXISTS `bitacora`;
CREATE TABLE `bitacora` (`login` varchar(20), `texto` text, `fecha` datetime, `idrol` int(11), `comercio` varchar(100));


DROP VIEW IF EXISTS `listadoIp`;
CREATE TABLE `listadoIp` (`ip` char(16), `fecha` varchar(21), `fechatruc` int(11));


DROP VIEW IF EXISTS `listadoTransacciones`;
CREATE TABLE `listadoTransacciones` (`fecha_mod` int(11), `comercio` varchar(100), `fecha` varchar(19), `idtransaccion` varchar(14), `valor` decimal(14,4), `moneda` char(3), `estado` char(1), `euros` varchar(66));


DROP VIEW IF EXISTS `mobile_clientes`;
CREATE TABLE `mobile_clientes` (`id_reserva` int(11), `id_transaccion` varchar(20), `comercio` varchar(100), `administr` varchar(100), `entorno` varchar(10), `identificador` varchar(20), `pasarela` varchar(100), `cliente` varchar(200), `email` varchar(100), `servicio` text, `valor_inicial` decimal(8,2), `valor` decimal(8,2), `moneda` char(3), `fechaI` varchar(21), `bankId` varchar(50), `fechaII` varchar(21), `fechaIII` varchar(21), `fecha` int(11), `pMom` varchar(10), `estadoTr` varchar(12), `classe` varchar(6), `tiempoV` int(2), `id_comercio` varchar(14), `idcom` int(11));


DROP VIEW IF EXISTS `mobile_tickets`;
CREATE TABLE `mobile_tickets` (`usuario` varchar(100), `email` varchar(150), `fechaModificada` int(11), `fecha` varchar(21), `asunto` varchar(200), `estadoP` varchar(9), `texto` text, `idrol` int(11), `idticket` int(11), `idadmin` int(11));


DROP VIEW IF EXISTS `mobile_transacciones`;
CREATE TABLE `mobile_transacciones` (`idtransaccion` varchar(14), `comercio` varchar(100), `identificador` varchar(20), `fechaFin` varchar(21), `moneda` char(3), `fecha_mod` int(11), `valorIni` decimal(13,2), `fechaIni` varchar(21), `codigo` varchar(12), `fecha` int(11), `idcomercio` varchar(20), `idcom` int(11), `tasaM` double(21,4), `euroEquiv` varbinary(19), `valorFin` varbinary(17), `estadoTr` varchar(12), `tipoEntorno` varchar(17), `classe` varchar(6), `error` varchar(400), `pasarl` varchar(100), `ip` varchar(17), `tipoPag` varchar(2));


DROP VIEW IF EXISTS `mobile_users`;
CREATE TABLE `mobile_users` (`nombre` varchar(100), `email` varchar(150), `login` varchar(20), `fechaA` varchar(10), `fechaV` varchar(21), `idiom` longtext, `idrol` int(11), `rol` varchar(50), `idadmin` int(11), `comercio` varchar(100), `fecha` int(11), `idcom` longblob, `activo` varchar(2));


DROP VIEW IF EXISTS `vw_ipblancas`;
CREATE TABLE `vw_ipblancas` (`comercio` varchar(100), `admin` varchar(100), `fecha` int(11), `fechaM` varchar(21), `ip` varchar(25));


DROP VIEW IF EXISTS `vw_transacciones`;
CREATE TABLE `vw_transacciones` (`id` varchar(14), `comercio` varchar(100), `tasaM` double(21,4), `estado` char(1), `fecha` int(11), `fecha_mod` int(11), `valIni` decimal(13,2), `idcomercio` varchar(20), `pasarela` smallint(3), `tipoE` char(1), `idmoneda` char(3), `codigo` varchar(12), `error` varchar(400), `tasaDev` double(21,4), `pasarelaN` varchar(100), `moneda` char(3), `euroEquiv` varbinary(19), `identificador` varchar(101), `color{col}` varchar(5), `ip` varchar(17), `geo{geoip}` varchar(17), `pagada` varchar(2), `valor` decimal(15,2), `tipoEntorno` varchar(17), `estad` varchar(12));


DROP VIEW IF EXISTS `vw_transaccionesCSV`;
CREATE TABLE `vw_transaccionesCSV` (`id` varchar(14), `comercio` varchar(100), `tasaM` double(21,4), `estado` char(1), `fecha` int(11), `fecha_mod` int(11), `valIni` decimal(13,2), `idcomercio` varchar(20), `solDev` tinyint(1), `pasarela` smallint(3), `tipoE` char(1), `idmoneda` char(3), `codigo` varchar(12), `error` varchar(400), `tasaDev` double(21,4), `pasarelaN` varchar(100), `moneda` char(3), `identificador` varchar(20), `euroEquiv` varbinary(19), `valorDev` decimal(14,2), `cliente` varchar(200), `color{col}` varchar(5), `ip` varchar(17), `geo{geoip}` varchar(17), `pagada` varchar(2), `valor` decimal(15,2), `tipoEntorno` varchar(17), `estad` varchar(12));


DROP TABLE IF EXISTS `bitacora`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `bitacora` AS select `a`.`login` AS `login`,`b`.`texto` AS `texto`,from_unixtime(`b`.`fecha`) AS `fecha`,`a`.`idrol` AS `idrol`,(case `a`.`idcomercio` when _utf8'todos' then _utf8'todos' else (select `tbl_comercio`.`nombre` AS `nombre` from `tbl_comercio` where (`tbl_comercio`.`idcomercio` = `a`.`idcomercio`)) end) AS `comercio` from (`tbl_baticora` `b` join `tbl_admin` `a`) where ((`a`.`idadmin` = `b`.`idadmin`) and (`a`.`idrol` > 10) and (`a`.`login` like _utf8'%')) order by `a`.`login`,`b`.`fecha` desc;

DROP TABLE IF EXISTS `listadoIp`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `listadoIp` AS select `tbl_listaIp`.`ip` AS `ip`,date_format(from_unixtime(`tbl_listaIp`.`fecha`),_utf8'%d/%m/%Y %H:%i') AS `fecha`,`tbl_listaIp`.`fecha` AS `fechatruc` from `tbl_listaIp`;

DROP TABLE IF EXISTS `listadoTransacciones`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `listadoTransacciones` AS select `t`.`fecha_mod` AS `fecha_mod`,`c`.`nombre` AS `comercio`,date_format(from_unixtime(`t`.`fecha_mod`),_utf8'%d/%m/%y %H:%i') AS `fecha`,`t`.`idtransaccion` AS `idtransaccion`,(`t`.`valor` / 100) AS `valor`,`t`.`moneda` AS `moneda`,`t`.`estado` AS `estado`,format(((`t`.`valor` / 100) / `t`.`tasa`),2) AS `euros` from (`tbl_transacciones` `t` join `tbl_comercio` `c`) where (`t`.`idcomercio` = `c`.`idcomercio`) order by `t`.`fecha_mod` desc;

DROP TABLE IF EXISTS `mobile_clientes`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `mobile_clientes` AS select `r`.`id_reserva` AS `id_reserva`,`r`.`id_transaccion` AS `id_transaccion`,`c`.`nombre` AS `comercio`,`a`.`nombre` AS `administr`,(case `r`.`est_comer` when _utf8'D' then _utf8'Desarrollo' else _utf8'Producción' end) AS `entorno`,`r`.`codigo` AS `identificador`,`p`.`nombre` AS `pasarela`,`r`.`nombre` AS `cliente`,`r`.`email` AS `email`,`r`.`servicio` AS `servicio`,`r`.`valor_inicial` AS `valor_inicial`,`r`.`valor` AS `valor`,`m`.`moneda` AS `moneda`,date_format(from_unixtime(`r`.`fecha`),_utf8'%d/%m/%Y %H:%i') AS `fechaI`,`r`.`bankId` AS `bankId`,date_format(from_unixtime(`r`.`fechaPagada`),_utf8'%d/%m/%Y %H:%i') AS `fechaII`,date_format(from_unixtime(`r`.`fechaCancel`),_utf8'%d/%m/%Y %H:%i') AS `fechaIII`,`r`.`fecha` AS `fecha`,(case `r`.`pMomento` when _utf8'S' then _utf8'Al momento' else _utf8'Diferido' end) AS `pMom`,(case `r`.`estado` when _utf8'P' then _utf8'En Proceso' when _utf8'A' then _utf8'Aceptada' when _utf8'D' then _utf8'Denegada' when _utf8'N' then _utf8'No Procesada' when _utf8'B' then _utf8'Anulada' else _utf8'Devuelta' end) AS `estadoTr`,(case `r`.`estado` when _utf8'P' then _utf8'cssPen' when _utf8'A' then _utf8'cssAce' when _utf8'D' then _utf8'cssDen' when _utf8'N' then _utf8'cssNop' when _utf8'B' then _utf8'cssCan' else _utf8'CssDev' end) AS `classe`,`r`.`tiempoV` AS `tiempoV`,`r`.`id_comercio` AS `id_comercio`,`c`.`id` AS `idcom` from ((((`tbl_reserva` `r` join `tbl_comercio` `c`) join `tbl_admin` `a`) join `tbl_pasarela` `p`) join `tbl_moneda` `m`) where ((`r`.`id_comercio` = `c`.`idcomercio`) and (`r`.`id_admin` = `a`.`idadmin`) and (`r`.`pasarela` = `p`.`idPasarela`) and (`r`.`moneda` = `m`.`idmoneda`)) order by `r`.`fecha`;

DROP TABLE IF EXISTS `mobile_tickets`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `mobile_tickets` AS select `a`.`nombre` AS `usuario`,`a`.`email` AS `email`,`t`.`fechaModificada` AS `fechaModificada`,date_format(from_unixtime(`t`.`fechaModificada`),_utf8'%d/%m/%Y %H:%i') AS `fecha`,`t`.`asunto` AS `asunto`,(case `t`.`estado` when _utf8'T' then _utf8'Terminado' else _utf8'Activo' end) AS `estadoP`,`t`.`texto` AS `texto`,`a`.`idrol` AS `idrol`,`t`.`idticket` AS `idticket`,`t`.`idadmin` AS `idadmin` from (`tbl_ticket` `t` join `tbl_admin` `a`) where (`t`.`idadmin` = `a`.`idadmin`) order by `t`.`estado`,`t`.`fechaModificada` desc;

DROP TABLE IF EXISTS `mobile_transacciones`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `mobile_transacciones` AS select `t`.`idtransaccion` AS `idtransaccion`,`c`.`nombre` AS `comercio`,`t`.`identificador` AS `identificador`,date_format(from_unixtime(`t`.`fecha_mod`),_utf8'%d/%m/%Y %H:%i') AS `fechaFin`,`m`.`moneda` AS `moneda`,`t`.`fecha_mod` AS `fecha_mod`,round((`t`.`valor_inicial` / 100),2) AS `valorIni`,date_format(from_unixtime(`t`.`fecha`),_utf8'%d/%m/%Y %H:%i') AS `fechaIni`,`t`.`codigo` AS `codigo`,`t`.`fecha` AS `fecha`,`t`.`idcomercio` AS `idcomercio`,`c`.`id` AS `idcom`,round(`t`.`tasa`,4) AS `tasaM`,(case `t`.`estado` when _utf8'B' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'V' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'A' then round(((`t`.`valor` / 100) / `t`.`tasa`),2) else _utf8'0.00' end) AS `euroEquiv`,(case `t`.`estado` when _utf8'B' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),2),round((`t`.`valor` / 100),2)) when _utf8'V' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),2),round((`t`.`valor` / 100),2)) when _utf8'A' then round((`t`.`valor` / 100),2) else _utf8'0.00' end) AS `valorFin`,(case `t`.`estado` when _utf8'P' then _utf8'En Proceso' when _utf8'A' then _utf8'Aceptada' when _utf8'D' then _utf8'Denegada' when _utf8'N' then _utf8'No Procesada' when _utf8'B' then _utf8'Anulada' else _utf8'Devuelta' end) AS `estadoTr`,(case `t`.`tipoEntorno` when _utf8'P' then _utf8'Producci&oacute;n' else _utf8'Desarrollo' end) AS `tipoEntorno`,(case `t`.`estado` when _utf8'P' then _utf8'cssPen' when _utf8'A' then _utf8'cssAce' when _utf8'D' then _utf8'cssDen' when _utf8'N' then _utf8'cssNop' when _utf8'B' then _utf8'cssCan' else _utf8'CssDev' end) AS `classe`,(case `t`.`id_error` when NULL then _utf8'-' when _utf8'' then _utf8'-' else `t`.`id_error` end) AS `error`,(case `t`.`pasarela` when _utf8'0' then NULL else (select `p`.`nombre` AS `nombre` from `tbl_pasarela` `p` where ((`p`.`idPasarela` = `t`.`pasarela`) and (`p`.`idPasarela` = `t`.`pasarela`))) end) AS `pasarl`,(case `t`.`ip` when _utf8'127.0.0.1' then _utf8'no record' else `t`.`ip` end) AS `ip`,(case `t`.`pago` when 0 then _utf8'No' else _utf8'Si' end) AS `tipoPag` from (((`tbl_transacciones` `t` join `tbl_comercio` `c`) join `tbl_pasarela` `p`) join `tbl_moneda` `m`) where ((`t`.`idcomercio` = `c`.`idcomercio`) and (`t`.`pasarela` = `p`.`idPasarela`) and (`t`.`moneda` = `m`.`idmoneda`)) order by `t`.`fecha_mod` desc;

DROP TABLE IF EXISTS `mobile_users`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `mobile_users` AS select `a`.`nombre` AS `nombre`,`a`.`email` AS `email`,`a`.`login` AS `login`,date_format(from_unixtime(`a`.`fecha`),_utf8'%d/%m/%Y') AS `fechaA`,date_format(from_unixtime(`a`.`fecha_visita`),_utf8'%d/%m/%Y %H:%i') AS `fechaV`,replace(`a`.`param`,_utf8'idioma=',_utf8'') AS `idiom`,`a`.`idrol` AS `idrol`,`r`.`nombre` AS `rol`,`a`.`idadmin` AS `idadmin`,(case when ((select count(0) AS `count(*)` from (`tbl_colAdminComer` `c` join `tbl_comercio` `o`) where ((`c`.`idAdmin` = `a`.`idadmin`) and (`c`.`idComerc` = `o`.`id`))) = (select count(0) AS `count(*)` from `tbl_comercio`)) then _utf8'todos' when ((select count(0) AS `count(*)` from (`tbl_colAdminComer` `c` join `tbl_comercio` `o`) where ((`c`.`idAdmin` = `a`.`idadmin`) and (`c`.`idComerc` = `o`.`id`))) = 1) then (select `o`.`nombre` AS `nombre` from (`tbl_colAdminComer` `c` join `tbl_comercio` `o`) where ((`c`.`idAdmin` = `a`.`idadmin`) and (`c`.`idComerc` = `o`.`id`))) else _utf8'varios' end) AS `comercio`,`a`.`fecha` AS `fecha`,(select group_concat(`c`.`idComerc` separator ',') AS `group_concat(c.idComerc separator ',')` from `tbl_colAdminComer` `c` where (`a`.`idadmin` = `c`.`idAdmin`)) AS `idcom`,(case `a`.`activo` when _utf8'S' then _utf8'Si' else _utf8'No' end) AS `activo` from (`tbl_admin` `a` join `tbl_roles` `r`) where (`r`.`idrol` = `a`.`idrol`) order by `a`.`fecha` desc;

DROP TABLE IF EXISTS `vw_ipblancas`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_ipblancas` AS select `c`.`nombre` AS `comercio`,`a`.`nombre` AS `admin`,`i`.`fecha` AS `fecha`,date_format(from_unixtime(`i`.`fecha`),_utf8'%d/%m/%Y %H:%i') AS `fechaM`,`i`.`ip` AS `ip` from ((`tbl_ipblancas` `i` join `tbl_comercio` `c`) join `tbl_admin` `a`) where ((`i`.`idAdmin` = `a`.`idadmin`) and (`i`.`idComercio` = `c`.`id`));

DROP TABLE IF EXISTS `vw_transacciones`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_transacciones` AS select `t`.`idtransaccion` AS `id`,`c`.`nombre` AS `comercio`,round(`t`.`tasa`,4) AS `tasaM`,`t`.`estado` AS `estado`,`t`.`fecha` AS `fecha`,`t`.`fecha_mod` AS `fecha_mod`,round((`t`.`valor_inicial` / 100),2) AS `valIni`,`c`.`idcomercio` AS `idcomercio`,`t`.`pasarela` AS `pasarela`,`t`.`tipoEntorno` AS `tipoE`,`t`.`moneda` AS `idmoneda`,`t`.`codigo` AS `codigo`,`t`.`id_error` AS `error`,round(`t`.`tasaDev`,4) AS `tasaDev`,`p`.`nombre` AS `pasarelaN`,`m`.`moneda` AS `moneda`,(case `t`.`estado` when _utf8'B' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'V' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'A' then round(((`t`.`valor` / 100) / `t`.`tasa`),2) else _utf8'0.00' end) AS `euroEquiv`,(case (select count(0) AS `count(*)` from `tbl_reserva` `r` where (`r`.`codigo` = `t`.`identificador`)) when 1 then concat(_utf8'<a href="index.php?componente=comercio&pag=cliente&val=',`t`.`identificador`,_utf8'">',`t`.`identificador`,_utf8'</a>') else `t`.`identificador` end) AS `identificador`,(case `t`.`estado` when _utf8'P' then _utf8'green' when _utf8'A' then _utf8'black' when _utf8'D' then _utf8'red' when _utf8'N' then _utf8'red' when _utf8'B' then _utf8'blue' else _utf8'blue' end) AS `color{col}`,(case `t`.`ip` when _utf8'127.0.0.1' then _utf8'no record' else `t`.`ip` end) AS `ip`,(case `t`.`ip` when _utf8'127.0.0.1' then _utf8'no record' else `t`.`ip` end) AS `geo{geoip}`,(case `t`.`pago` when 0 then _utf8'No' else _utf8'Si' end) AS `pagada`,(case `t`.`estado` when _utf8'B' then round(if((`t`.`fecha_mod` < `t`.`fechaPagada`),(-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),(`t`.`valor` / 100)),2) when _utf8'V' then round(if((`t`.`fecha_mod` < `t`.`fechaPagada`),(-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),(`t`.`valor` / 100)),2) else round((`t`.`valor` / 100),2) end) AS `valor`,(case `t`.`tipoEntorno` when _utf8'P' then _utf8'Producci&oacute;n' else _utf8'Desarrollo' end) AS `tipoEntorno`,(case `t`.`estado` when _utf8'P' then _utf8'En Proceso' when _utf8'A' then _utf8'Aceptada' when _utf8'D' then _utf8'Denegada' when _utf8'N' then _utf8'No Procesada' when _utf8'B' then _utf8'Anulada' else _utf8'Devuelta' end) AS `estad` from (((`tbl_transacciones` `t` join `tbl_comercio` `c`) join `tbl_moneda` `m`) join `tbl_pasarela` `p`) where ((`c`.`idcomercio` = `t`.`idcomercio`) and (`t`.`moneda` = `m`.`idmoneda`) and (`p`.`idPasarela` = `t`.`pasarela`));

DROP TABLE IF EXISTS `vw_transaccionesCSV`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_transaccionesCSV` AS select `t`.`idtransaccion` AS `id`,`c`.`nombre` AS `comercio`,round(`t`.`tasa`,4) AS `tasaM`,`t`.`estado` AS `estado`,`t`.`fecha` AS `fecha`,`t`.`fecha_mod` AS `fecha_mod`,round((`t`.`valor_inicial` / 100),2) AS `valIni`,`c`.`idcomercio` AS `idcomercio`,`t`.`solDev` AS `solDev`,`t`.`pasarela` AS `pasarela`,`t`.`tipoEntorno` AS `tipoE`,`t`.`moneda` AS `idmoneda`,`t`.`codigo` AS `codigo`,`t`.`id_error` AS `error`,round(`t`.`tasaDev`,4) AS `tasaDev`,`p`.`nombre` AS `pasarelaN`,`m`.`moneda` AS `moneda`,`t`.`identificador` AS `identificador`,(case `t`.`estado` when _utf8'B' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'V' then if((`t`.`fecha_mod` < `t`.`fechaPagada`),round((-(1) * (((`t`.`valor_inicial` - `t`.`valor`) / 100) / `t`.`tasaDev`)),2),round(((`t`.`valor` / 100) / `t`.`tasa`),2)) when _utf8'A' then round(((`t`.`valor` / 100) / `t`.`tasa`),2) else _utf8'0.00' end) AS `euroEquiv`,(case `t`.`estado` when _utf8'B' then round(((`t`.`valor_inicial` - `t`.`valor`) / 100),2) when _utf8'V' then round(((`t`.`valor_inicial` - `t`.`valor`) / 100),2) else 0 end) AS `valorDev`,(case (select count(0) AS `count(*)` from `tbl_reserva` `r` where (`r`.`codigo` = `t`.`identificador`)) when 1 then (select `r`.`nombre` AS `nombre` from `tbl_reserva` `r` where (`r`.`codigo` = `t`.`identificador`)) else _utf8' - ' end) AS `cliente`,(case `t`.`estado` when _utf8'P' then _utf8'green' when _utf8'A' then _utf8'black' when _utf8'D' then _utf8'red' when _utf8'N' then _utf8'red' when _utf8'B' then _utf8'blue' else _utf8'blue' end) AS `color{col}`,(case `t`.`ip` when _utf8'127.0.0.1' then _utf8'no record' else `t`.`ip` end) AS `ip`,(case `t`.`ip` when _utf8'127.0.0.1' then _utf8'no record' else `t`.`ip` end) AS `geo{geoip}`,(case `t`.`pago` when 0 then _utf8'No' else _utf8'Si' end) AS `pagada`,(case `t`.`estado` when _utf8'B' then round(if((`t`.`fecha_mod` < `t`.`fechaPagada`),(-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),(`t`.`valor` / 100)),2) when _utf8'V' then round(if((`t`.`fecha_mod` < `t`.`fechaPagada`),(-(1) * ((`t`.`valor_inicial` - `t`.`valor`) / 100)),(`t`.`valor` / 100)),2) else round((`t`.`valor` / 100),2) end) AS `valor`,(case `t`.`tipoEntorno` when _utf8'P' then _utf8'Producci&oacute;n' else _utf8'Desarrollo' end) AS `tipoEntorno`,(case `t`.`estado` when _utf8'P' then _utf8'En Proceso' when _utf8'A' then _utf8'Aceptada' when _utf8'D' then _utf8'Denegada' when _utf8'N' then _utf8'No Procesada' when _utf8'B' then _utf8'Anulada' else _utf8'Devuelta' end) AS `estad` from (((`tbl_transacciones` `t` join `tbl_comercio` `c`) join `tbl_moneda` `m`) join `tbl_pasarela` `p`) where ((`c`.`idcomercio` = `t`.`idcomercio`) and (`t`.`moneda` = `m`.`idmoneda`) and (`p`.`idPasarela` = `t`.`pasarela`));

delimiter ;;
DROP TABLE IF EXISTS `debug`;;
CREATE TABLE `debug` (
  `valor` text COLLATE utf8_spanish2_ci NOT NULL,
  `fecha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;;


drop procedure if exists pr_debug;;
CREATE PROCEDURE `pr_debug` (texto text)  begin
	insert into debug (valor, fecha) values (texto, unix_timestamp());
end;;

delimiter ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
