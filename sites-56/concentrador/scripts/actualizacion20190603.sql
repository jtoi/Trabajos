ALTER TABLE `tbl_comercio`
CHANGE `usdxamex` `usdxamex` tinyint(1) NOT NULL DEFAULT '2' COMMENT '0-permitir eur x amex, 1-permitir usd x amex, 2- no permitir nada' AFTER `cambOperEuro`;

update tbl_comercio set usdxamex = 1;
