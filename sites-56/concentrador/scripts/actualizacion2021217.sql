#alter table tbl_pasarela auto_increment = 194;

INSERT INTO tbl_pasarela values
(null, 'TefPayCripto', 'P', '', 'pasoK@firmaB', unix_timestamp(), '1', 'Iberotravels', '', '13', 'D', '0', '17', '1', '4', '0', '9500', '20000', '100000000', '100000000', '100', '100', '500', '1.00', '8', '0', '', unix_timestamp(), '0', '194');

insert into tbl_colPasarMon values
(null, '194', '978', '00000001', '5970f635364486.10497355', 'V98000250', unix_timestamp(), '', '1');

INSERT INTO tbl_tarjetas VALUES
(NULL, 'Criptomoneda', 'cripto', '1', 'M');

INSERT INTO tbl_colTarjPasar VALUES
(NULL, '17', '194');
