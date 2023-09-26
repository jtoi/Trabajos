DROP trigger IF EXISTS `tr_inscomer`;
DROP trigger IF EXISTS `tr_uptcomer`;

update tbl_comercio set resp = 0;
update tbl_comercio set resp = 1 where idcomercio in ('140778652871', '129025985109', '140784511377', '135334103888', '146161323238', '151560722836', '138668374494', '144172448713', '159136992102 ', '159171392542', '527341458854');


delimiter ;;
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
delimiter ;
