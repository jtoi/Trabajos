ALTER TABLE `tbl_transaccionesOld`
ADD `tipoPago` char(1) COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'W' COMMENT 'W- web, P-presencial, D-diferido';
ALTER TABLE `tbl_transaccionesOld`
ADD INDEX `tipoPago` (`tipoPago`);

ALTER TABLE `tbl_transacciones`
ADD `tipoPago` char(1) COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT 'W' COMMENT 'W- web, P-presencial, D-diferido';
ALTER TABLE `tbl_transacciones`
ADD INDEX `tipoPago` (`tipoPago`);

update tbl_transaccionesOld t, tbl_reserva r set t.tipoPago = 'P' where t.idtransaccion = r.id_transaccion and r.pMomento = 'S';
update tbl_transaccionesOld t, tbl_reserva r set t.tipoPago = 'D' where t.idtransaccion = r.id_transaccion and r.pMomento = 'N';
update tbl_transacciones t, tbl_reserva r set t.tipoPago = 'P' where t.idtransaccion = r.id_transaccion and r.pMomento = 'S';
update tbl_transacciones t, tbl_reserva r set t.tipoPago = 'D' where t.idtransaccion = r.id_transaccion and r.pMomento = 'N';
