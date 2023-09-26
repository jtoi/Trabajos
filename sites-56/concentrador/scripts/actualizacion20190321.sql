INSERT INTO `tbl_menu` (`title`, `link`, `parentid`, `movil`, `mlink`, `orden`)
VALUES ('_MENU_ADMIN_LOTE', 'index.php?componente=comercio&pag=lote', '2', '1', NULL, '2');

insert into tbl_accesos values (null, 1, 67, unix_timestamp());
insert into tbl_accesos values (null, 10, 67, unix_timestamp());
insert into tbl_accesos values (null, 11, 67, unix_timestamp());

ALTER TABLE `tbl_comercio`
ADD `lotes` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-no tiene pago por lotes, 1- si tiene';

ALTER TABLE `tbl_reserva`
ADD `idlote` int(11) NULL COMMENT 'id de la tabla tbl_lotes';

drop TABLE if exists tbl_lotes;
create table tbl_lotes (
    id int(11) NOT NULL AUTO_INCREMENT,
    idcomercio int(11) not null,
    idtransaccion varchar(14) collate utf8_spanish_ci not null default 0,
    fecha int(11) not null,
    idreserva varchar(20) collate utf8_spanish_ci not null,
    confirmacion varchar(20) collate utf8_spanish_ci not null,
    cliente varchar(200) collate utf8_spanish_ci not null,
    email varchar(200) collate utf8_spanish_ci not null,
    tipo tinyint(1) not null default 0 COMMENT '0- pago al momento, 1- diferido',
    valor int(11) not null default 0,
    moneda char(3) collate utf8_spanish_ci null,
    fechaLanz int(11) not null default 0 COMMENT 'fecha de lanzada la operacion',
    tarjeta int(11) null comment 'Tipo de tarjeta ver tbl_tarjetas',
    valida tinyint(1) not null default 1 comment '1- válida se procesa, 0- no se procesa',
    primary key (id),
    key idtransaccion (idtransaccion),
    key idcomercio (idcomercio),
    key fecha (fecha),
    constraint idcomercioLotesFK foreign key (idcomercio) references tbl_comercio (id) on delete cascade on update no action
);


