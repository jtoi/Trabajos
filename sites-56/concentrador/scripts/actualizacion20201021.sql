ALTER TABLE `tbl_transacciones`
CHANGE `ip` `ip` varchar(40) COLLATE 'utf8_spanish_ci' NOT NULL DEFAULT '127.0.0.1' AFTER `sesion`;
