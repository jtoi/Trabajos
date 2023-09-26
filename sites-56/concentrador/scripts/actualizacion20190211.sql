##poner los triggers en todoamf_db.sql

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS tbl_limites;
CREATE TABLE tbl_limites (
	id int(11) NOT NULL AUTO_INCREMENT,
	nombre varchar(20) COLLATE utf8_spanish2_ci NOT NULL,
	valMax int not null,
	descripcion varchar(200) COLLATE 'utf8_spanish2_ci' NULL,
	primary key (id)
) ENGINE='InnoDB' DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

insert into tbl_limites (nombre, valMax, descripcion) values
('LimMinOper', 0, 'Límite mínimo por operación en montos'),
('LimMaxOper', 100000000, 'Límite máximo por operación en montos'),
('LimDiar', 100000000, 'Límite diario en montos'),
('LimMens', 100000000, 'Límite mensual en montos'),
('LimAnual', 100000000, 'Límite anual en montos'),
('LimOperIpDia', 1000, 'Límite de operaciones por IP al día'),
('LimOperDia', 1000, 'Límite de operaciones al día');

drop table if exists tbl_colPasarLimite;
create table tbl_colPasarLimite (
	id int not null auto_increment,
	idPasar smallint not null,
	idLimite int not null,
	idmoneda char(3) collate 'utf8_spanish_ci' not null,
	valor int not null,
	fecha int not null,
	primary key (id),
	key idPasar (idPasar),
	key idLimite (idLimite),
	key idmoneda (idmoneda),
	constraint idPasarparlimFK foreign key (idPasar) references tbl_pasarela (idPasarela) on delete cascade on update no action,
	constraint idLimitepasarlimFK foreign key (idLimite) references tbl_limites (id) on delete cascade on update no action,
	constraint idmonedapasarlimFK foreign key (idmoneda) references tbl_moneda (idmoneda) on delete cascade on update no action
) engine='InnoDB' collate 'utf8_spanish_ci';


delimiter ;;

drop trigger if exists tbl_colPasarLimiteBI;;
CREATE TRIGGER tbl_colPasarLimiteBI BEFORE INSERT ON tbl_colPasarLimite FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());;
drop trigger if exists tbl_colPasarLimiteBU;;
CREATE TRIGGER tbl_colPasarLimiteBU BEFORE UPDATE ON tbl_colPasarLimite FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());;


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
delimiter ;

call concentramf_db.pr_llenaPasarLimite();

SET FOREIGN_KEY_CHECKS=1;

