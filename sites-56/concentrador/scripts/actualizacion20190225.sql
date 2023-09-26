ALTER TABLE `tbl_aisClienteBeneficiario`
ADD `confirmada` tinyint(2) NOT NULL DEFAULT '0',
ADD INDEX `confirmada` (`confirmada`);
update tbl_aisClienteBeneficiario set confirmada = 1 where fecha < 1541047270;
