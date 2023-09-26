drop trigger if exists tr_inscomer;
drop trigger if exists tr_uptcomer;

DELIMITER ;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `tr_inscomer` AFTER INSERT ON `tbl_comercio` FOR EACH ROW begin
	declare idAdm int;
	declare no_more_rows boolean;
	declare idCom int default new.id;
	declare num_rows int default 0;
	
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
end */;;

/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `tr_uptcomer` BEFORE UPDATE ON `tbl_comercio` FOR EACH ROW begin
	if (new.activo != old.activo) then call estadComer(old.id, new.activo); end if;
end */;;

DELIMITER ;