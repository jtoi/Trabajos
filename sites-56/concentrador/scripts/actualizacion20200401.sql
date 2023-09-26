
ALTER TABLE `tbl_transacciones`
ADD `solRec` tinyint(1) NOT NULL DEFAULT '0' comment '1- En proceso de Reclamación',
add `fechaAgen` int(11) not null default '0';

ALTER TABLE `tbl_transaccionesOld`
ADD `solRec` tinyint(1) NOT NULL DEFAULT '0' comment '1- En proceso de Reclamación',
add `fechaAgen` int(11) not null default '0';

