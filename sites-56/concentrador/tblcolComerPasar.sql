/*Crea el triger para cambiar las pasarelas y comercios*/
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
