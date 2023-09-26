ALTER TABLE `tbl_cambio`
CHANGE `abanca` `rural` float(9,4) NOT NULL DEFAULT '0.0000' AFTER `caixa`,
ADD `bankia` float(9,4) NOT NULL DEFAULT '0',
ADD `ibercaja` float(9,4) NOT NULL DEFAULT '0' AFTER `bankia`,
ADD `tasa` float(9,4) NOT NULL DEFAULT '0.02' AFTER `ibercaja`;

insert into tbl_setup (nombre, valor, comentario, fecha) values ('maxUSDperm', '0.06', 'Valor de diferencia máximo entre las tasas de usd de los bancos y a como está comprando el eur el Banco Central de Cuba', unix_timestamp());
