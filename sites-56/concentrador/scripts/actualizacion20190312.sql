##operaciones con tasas de cambio mal
select idtransaccion, valor, moneda, tasa, euroEquiv, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') from tbl_transacciones where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190305153100 and 190306140000 and idcomercio != '527341458854' order by fecha;
update tbl_transacciones set tasa = '1.1691', euroEquiv = (valor/100/tasa) where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190305153100 and 190306140000 and idcomercio != '527341458854';
select idtransaccion, valor, moneda, tasa, euroEquiv, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') from tbl_transacciones where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190305153100 and 190306140000 and idcomercio != '527341458854' order by fecha;

select idtransaccion, valor, moneda, tasa, euroEquiv, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') from tbl_transacciones where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190306153000 and 190307140000 and idcomercio != '527341458854' order by fecha;
update tbl_transacciones set tasa = '1.1671', euroEquiv = (valor/100/tasa) where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190306153000 and 190307140000 and idcomercio != '527341458854';
select idtransaccion, valor, moneda, tasa, euroEquiv, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') from tbl_transacciones where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190306153000 and 190307140000 and idcomercio != '527341458854' order by fecha;

select idtransaccion, valor, moneda, tasa, euroEquiv, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') from tbl_transacciones where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190308155000 and 190309140000 and idcomercio != '527341458854' order by fecha;
update tbl_transacciones set tasa = '1.1496', euroEquiv = (valor/100/tasa) where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190308155000 and 190309140000 and idcomercio != '527341458854';
select idtransaccion, valor, moneda, tasa, euroEquiv, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') from tbl_transacciones where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190308155000 and 190309140000 and idcomercio != '527341458854' order by fecha;

select idtransaccion, valor, moneda, tasa, euroEquiv, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') from tbl_transacciones where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190309151000 and 190310140000 and idcomercio != '527341458854' order by fecha;
update tbl_transacciones set tasa = '1.1591', euroEquiv = (valor/100/tasa) where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190309151000 and 190310140000 and idcomercio != '527341458854';
select idtransaccion, valor, moneda, tasa, euroEquiv, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') from tbl_transacciones where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190309151000 and 190310140000 and idcomercio != '527341458854' order by fecha;

select idtransaccion, valor, moneda, tasa, euroEquiv, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') from concentramf_db.tbl_transacciones where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190310230000 and 190311140000 and idcomercio != '527341458854' order by fecha;
update tbl_transacciones set tasa = '1.1591', euroEquiv = (valor/100/tasa) where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190310230000 and 190311140000 and idcomercio != '527341458854';
select idtransaccion, valor, moneda, tasa, euroEquiv, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') from tbl_transacciones where tipoOperacion = 'P' and estado = 'A' and moneda = 840 and idtransaccion between 190310230000 and 190311140000 and idcomercio != '527341458854' order by fecha;


