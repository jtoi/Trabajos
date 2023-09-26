DROP TABLE IF EXISTS `tbl_colCambBanco`;
CREATE TABLE `tbl_colCambBanco` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idbanco` smallint(6) NOT NULL,
  `idmoneda` char(3) COLLATE utf8_spanish_ci NOT NULL,
  `tasa` float(10,5) NOT NULL,
  `fecha` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idbanco` (`idbanco`),
  KEY `idmoneda` (`idmoneda`),
  KEY `fecha` (`fecha`),
  CONSTRAINT `tbl_colCambBanco_ibfk_1` FOREIGN KEY (`idbanco`) REFERENCES `tbl_bancos` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `tbl_colCambBanco_ibfk_2` FOREIGN KEY (`idmoneda`) REFERENCES `tbl_moneda` (`idmoneda`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `tbl_bancos` (`banco`) VALUES 
('Banco Central Europeo'),
('Banco Nacional de Cuba'),
('XE'),
('Visa');

delimiter $$

drop procedure if exists recrea_tasas$$
create procedure recrea_tasas ()
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
end;
$$
delimiter ;

