drop procedure if exists CierreTransacciones;
delimiter $$
create procedure CierreTransacciones () 
comment 'Rellena las tablas colCierreTransacciones y colCierreTransf con los datos existentes '
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
	
	-- busca todos los cierres 
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
	
		-- Verifico si la o las facturas que existen
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
delimiter ;

drop procedure if exists estadComer;
delimiter $$
create procedure estadComer (in idCom int, in estado char(1)) 
comment 'Cambia el estado de los usuarios en base al estado del comercio'
begin
	declare estPass char(1) default null;
	declare idAdm int;
	declare pase char(1) default 'S';
	declare no_more_rows boolean;
	declare totalC int default 0;
	declare totalN int default 0;
	declare num_rows int default 0;


	-- busca todos los usuarios del comercio para cambiarles el estado
	declare cur_usuarios cursor for select a.idadmin from tbl_admin a, tbl_colAdminComer o where o.idComerc = idCom and o.idAdmin = a.idadmin;
	declare continue handler for not found set no_more_rows = true;
	
	-- si el estado es S se le pone S a todos sus usuarios activos y se termina todo,
	-- si el estado es N entonces hay que buscar si los usuarios de este comercio son además
	-- usuarios de otros y se necesita ver si esos otros comercios están todos inactivos para cambiarle el estado al usuario
	-- si alguno de esos usuarios tiene comercios que están activos entonces no se tocan
	if (estado = 'S') then
		update tbl_admin a, tbl_colAdminComer o set activo = 'S' where o.idAdmin = a.idadmin and activo = 'N' and o.idComerc = idCom;
	else
		
-- 		insert into tbl_test (nombre,valor) values ('paso','entro');
		open cur_usuarios;
			select FOUND_ROWS() into num_rows;
-- 			insert into tbl_test (nombre,valor) values ('cant',num_rows);
			el_lazo: loop
				fetch cur_usuarios into idAdm;
				if no_more_rows then
					close cur_usuarios;
					leave el_lazo;
				end if;

				set totalC = 0;
				set totalN = 0;

				-- revisa los comercios que tiene el usuario y si todos ellos están en N
				select count(idComerc) into totalC from tbl_colAdminComer where idAdmin = idAdm;
				select count(idComerc) into totalN from tbl_colAdminComer o, tbl_comercio c where idAdmin = idAdm and activo = 'N' and c.id = o.idComerc;
-- 				insert into tbl_test (nombre,valor) values ('totalC = (totalN + 1)',concat(idAdm,' - ',totalC,' = (',totalN + 1,')'));
				if (totalC = (totalN + 1)) then
					-- todos los comercios están en N se actualiza el usuario
					-- se pone totalN + 1 porque al menos el comercio actual puede estar en S 
					update tbl_admin set activo = estado where idadmin = idAdm;
				end if;

			end loop el_lazo;

-- 		close cur_usuarios;
	end if;

end$$
delimiter ;

drop trigger if exists tr_uptcomer;
delimiter $$
create trigger tr_uptcomer before update on tbl_comercio
for each row begin
	if (new.activo != old.activo) then call estadComer(old.id, new.activo); end if;
end$$
delimiter ;

drop trigger if exists tr_inscomer;
delimiter $$
create trigger tr_inscomer after insert on tbl_comercio
for each row begin
	declare idAdm int;
	declare no_more_rows boolean;
	declare idCom int default new.id;
	declare num_rows int default 0;
	
	-- busca todos los usuarios de amf
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
-- 	close cur_usuarios;
end$$
delimiter ;

drop trigger if exists tr_inscomer;
drop trigger if exists tr_uptcomer;

DELIMITER ;;
CREATE TRIGGER tr_inscomer AFTER INSERT ON tbl_comercio FOR EACH ROW begin
	declare idAdm int;
	declare no_more_rows boolean;
	declare idCom int default new.id;
	declare num_rows int default 0;
    declare pasarIn varchar(100) default new.pasarela;
    declare pasarMm varchar(100) default new.pasarelaAlMom;
    declare pas int;
	
	declare cur_usuarios cursor for select a.idadmin from tbl_admin a where idrol < 11;
	declare continue handler for not found set no_more_rows = true;
	
##Pone los usuarios de AMF que ya existen para atender este comercio
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

##Pone las pasarelas en la tabla ColComerPasar
##finaliza todas las pasarelas que tiene el comercio para que luego sólo sea insertar las que se ponen nuevas
	update tbl_colComerPasar set fechaFin = unix_timestamp() where idcomercio = idCom and fechaFin = 2863700400;

    if (length(pasarIn) > 0 or length(pasarMm) > 0) then ##se modifica la columna pasarela
		
        el_lazo2: loop
            if LOCATE(',', pasarIn) = 0 then ##el comercio sólo tiene una pasarela
				if length(pasarIn) > 0 then
					call fn_inspasar (idCom, pasarIn, 0);
					if length(pasarMm) > 0 then
						set pasarIn = pasarMm;
						set pasarMm = '';
						iterate el_lazo2;
					end if;
				end if;
				
				leave el_lazo2;

			else ##el comercio viene con más de una pasarela, las voy procesando una a una hasta que sólo queda una
				select substring_index(pasarIn, ',', 1) into pas;
				call fn_inspasar (idCom, pas, 0);
				select replace(pasarIn, concat(pas,','), '') into pasarIn;
			end if;
        end loop el_lazo2;

    end if;

end;;

CREATE TRIGGER tr_uptcomer BEFORE UPDATE ON tbl_comercio FOR EACH ROW begin
	declare idCom int default old.id;
    declare pasarIn varchar(100) default new.pasarela;
    declare pasarMm varchar(100) default new.pasarelaAlMom;
    declare pas int;
	declare tipo int default 0;

	if (new.activo != old.activo) then call estadComer(idCom, new.activo); end if;

##Pone las pasarelas en la tabla ColComerPasar
##finaliza todas las pasarelas que tiene el comercio para que luego sólo sea insertar las que se ponen nuevas
	update tbl_colComerPasar set fechaFin = unix_timestamp() where idcomercio = idCom and fechaFin = 2863700400;

    if (length(pasarIn) > 0 or length(pasarMm) > 0) then ##se modifica la columna pasarela
		
        el_lazo2: loop
            if LOCATE(',', pasarIn) = 0 then ##el comercio sólo tiene una pasarela
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

			else ##el comercio viene con más de una pasarela, las voy procesando una a una hasta que sólo queda una
				select substring_index(pasarIn, ',', 1) into pas;
				call fn_inspasar (idCom, pas, tipo);
				select replace(pasarIn, concat(pas,','), '') into pasarIn;
			end if;
        end loop el_lazo2;

    end if;
end;;

drop procedure if exists fn_inspasar;;

create procedure fn_inspasar(com int, pas int, tipo int(1))
begin
	declare cant int default 0;
	declare hoy int default unix_timestamp();
	declare columna varchar(15);

	if (tipo = 0) then ##para cuando se inserta o modifica pasarela
		insert into tbl_colComerPasar (idpasarelaT, idcomercio, fechaIni, fechaFin) values (pas, com, hoy, 2863700400);
	else ##para cuando se inserta o modifica pasarelaAlMom
		insert into tbl_colComerPasar (idpasarelaW, idcomercio, fechaIni, fechaFin) values (pas, com, hoy, 2863700400);
	end if;

end;;

DELIMITER ;

