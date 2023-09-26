ALTER TABLE `tbl_transacciones` DROP INDEX `identificadorBnco`;
ALTER TABLE `tbl_transacciones` CHANGE `identificadorBnco` `identificadorBnco` text COLLATE 'utf8_spanish_ci' NULL AFTER `tarjetas`;

ALTER TABLE `tbl_transaccionesOld` DROP INDEX `identificadorBnco`;
ALTER TABLE `tbl_transaccionesOld` CHANGE `identificadorBnco` `identificadorBnco` text COLLATE 'utf8_spanish_ci' NULL AFTER `tarjetas`;


