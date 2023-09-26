drop table tbl_aisCleinteError if exists;
CREATE TABLE `tbl_aisClienteError` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `idcliente` int NOT NULL,
  `fecha` int NOT NULL,
  `fechaRev` int not null,
  `error` text COLLATE 'latin1_spanish_ci' NOT NULL
) ENGINE='InnoDB' COLLATE 'latin1_spanish_ci';

ALTER TABLE `tbl_aisClienteError`
ADD INDEX `idcliente` (`idcliente`),
ADD INDEX `fechaRev` (`fechaRev`),
ADD FOREIGN KEY (`idcliente`) REFERENCES `tbl_aisCliente` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
