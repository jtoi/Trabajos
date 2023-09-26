CREATE TRIGGER `tr_artista_ai` AFTER INSERT ON `tbl_artista` FOR EACH ROW
begin
  declare idAdm int;
  declare rol int;
  declare idArt int default new.id;
  declare done int default 0;

  declare cur_admin cursor for select id, idrol from tbl_admin;
  declare CONTINUE HANDLER FOR NOT FOUND SET done=1;

#inserta en la tbl_colArtistaAdmin
  open cur_admin;
  cursor_loop:LOOP
    fetch cur_admin into idAdm, rol;
    IF done=1 THEN
      LEAVE cursor_loop;
    END IF;
    if rol<3 then
      insert into tbl_colArtistaAdmin (idartista, idadmin) values (idArt, idAdm);
    end if;
  END LOOP cursor_loop;
  close cur_admin;
  
end;
