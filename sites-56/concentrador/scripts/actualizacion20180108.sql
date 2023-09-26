ALTER TABLE `tbl_transacciones`
ADD `mtoMonBnc` int(11) NOT NULL DEFAULT '0' COMMENT 'Monto en la moneda del banco';
ALTER TABLE `tbl_transaccionesOld`
ADD `mtoMonBnc` int(11) NOT NULL DEFAULT '0' COMMENT 'Monto en la moneda del banco';

