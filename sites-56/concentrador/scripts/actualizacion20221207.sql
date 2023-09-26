SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


DROP TABLE IF EXISTS tbl_colComerPasaMon;
CREATE TABLE tbl_colComerPasaMon (
  id int(11) NOT NULL,
  idcomercio int(11) NOT NULL,
  idpasarela smallint(3) NOT NULL,
  idmoneda char(3) COLLATE utf8_spanish_ci NOT NULL default '978',
  fecha int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

ALTER TABLE tbl_colComerPasaMon
  ADD PRIMARY KEY (id),
  ADD KEY IDX_Com_ComerPasaMon (idcomercio),
  ADD KEY IDX_Pasa_ComerPasaMon (idpasarela),
  ADD KEY IDX_Mon_ComerPasaMon (idmoneda);

ALTER TABLE tbl_colComerPasaMon
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE tbl_colComerPasaMon
  ADD CONSTRAINT FK_Com_ComerPasaMon FOREIGN KEY (idcomercio) REFERENCES tbl_comercio (id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FK_Mon_ComerPasaMon FOREIGN KEY (idmoneda) REFERENCES tbl_moneda (idmoneda) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FK_PAS_ComerPasaMon FOREIGN KEY (idpasarela) REFERENCES tbl_pasarela (idPasarela) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

insert into tbl_menu (title, link, parentid, orden) values ('_COMERCIO_MONEDA_PAGO', 'index.php?componente=comercio&pag=monPago', '2', '1');
insert into tbl_accesos (idrol, idmenu, fecha) values ('1', '84', unix_timestamp());
insert into tbl_accesos (idrol, idmenu, fecha) values ('10', '84', unix_timestamp());


delimiter ;;

drop function if exists fn_countChar;;
create function fn_countChar(
    x varchar(500),
    delim varchar(5)
)
returns varchar(500)
return (LENGTH(x) - LENGTH(REPLACE(x, delim, '')));;

drop function if exists fn_splitStr;;
CREATE FUNCTION fn_splitStr(
  x VARCHAR(500),
  delim VARCHAR(12),
  pos INT
)
RETURNS VARCHAR(500)
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
       LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
       delim, '');;

drop procedure if exists pr_llenaColMonPasaCom;;
create procedure pr_llenaColMonPasaCom()
begin
    declare numRecords int default 0;
    declare strPasar varchar(800);
    declare idCom int;
    declare idPas int;
    declare largo int;
    declare idMon char(3);
	declare no_more_rows boolean;

	declare cur_comer cursor for select id, trim(both ',' from concat(pasarela,',',pasarelaAlMom)) from tbl_comercio where activo = 'S' and (length(pasarelaAlMom) > 0 or length(pasarela) > 0) ;
	declare continue handler for not found set no_more_rows = true;

    truncate debug;
	select count(id) into numRecords from tbl_colComerPasaMon;

	if numRecords = 0 then
        open cur_comer;
        loop1: loop
            fetch cur_comer into idCom, strPasar;

	        if no_more_rows then
		        leave LOOP1;
		        close cur_comer;
            end if;
            select fn_countChar(strPasar,',')+1 into largo;
            while largo > 0 do
                select convert(fn_splitStr(strPasar,',',largo), int) into idPas;
                select count(id) into numRecords from tbl_colComerPasaMon where idcomercio = idCom and idpasarela = idPas;
                if numRecords = 0 then
                    insert into tbl_colComerPasaMon (idcomercio, idpasarela, fecha) values (idCom, idPas, unix_timestamp());
                end if;
                set largo = largo - 1;
            end while;


        end loop loop1;
        close cur_comer;
    end if;
end;;

truncate tbl_colComerPasaMon;;
call pr_llenaColMonPasaCom;;

drop function if exists fn_splitStr;;
drop function if exists fn_countChar;;
drop procedure if exists pr_llenaColMonPasaCom;;

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

delimiter ;
