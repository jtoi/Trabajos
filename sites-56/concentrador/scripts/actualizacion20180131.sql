INSERT INTO `tbl_bancos` (`banco`)
VALUES ('Entidad de Pago TefPay');

update tbl_pasarela set idbanco = 18 where idPasarela in (62);
update tbl_pasarela set idbanco = 14 where idPasarela in (70);
update tbl_pasarela set idbanco = 23 where idPasarela in (74);
update tbl_pasarela set idbanco = 19 where idPasarela in (79);
update tbl_pasarela set idbanco = 15 where idPasarela in (80);
update tbl_pasarela set idbanco = 2 where idPasarela in (81);
update tbl_pasarela set idbanco = 25 where idPasarela in (82,83);
insert into tbl_colPasarBancos (idpasarela, idbanco) values
(62,18),
(70,14),
(74,23),
(81,2),
(82,25),
(83,25),
(77,6),
(78,24)
