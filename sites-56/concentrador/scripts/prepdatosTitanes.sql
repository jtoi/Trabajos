UPDATE tbl_pasarela SET estado = 'D', devolucion = 1 WHERE idPasarela = '37';

insert into tbl_transacciones (idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor, valor_inicial, tipoEntorno, moneda, estado, sesion, idioma, pasarela, ip, idpais, tpv, codigo, tasa,  euroEquiv, solDev) values ('16123014730A', '527341458854', '001', 'P', unix_timestamp(), unix_timestamp(), 1965, 1965, 'P', 978, 'A', '7984c5b946ba45cc913b024c73c76721', 'es', 37, '198.168.0.1', 199, '0', '095659', '1', valor, '1');
insert into tbl_transacciones (idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor, valor_inicial, tipoEntorno, moneda, estado, sesion, idioma, pasarela, ip, idpais, tpv, codigo, tasa,  euroEquiv, solDev) values ('16123014730B', '527341458854', '002', 'P', unix_timestamp(), unix_timestamp(), 2559, 2559, 'P', 978, 'A', '7984c5b946ba45cc913b024c73c76721', 'es', 37, '198.168.0.1', 199, '0', '095660', '1', valor, '1');
insert into tbl_transacciones (idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor, valor_inicial, tipoEntorno, moneda, estado, sesion, idioma, pasarela, ip, idpais, tpv, codigo, tasa,  euroEquiv, solDev) values ('16123014730C', '527341458854', '003', 'P', unix_timestamp(), unix_timestamp(), 2189, 2189, 'P', 978, 'A', '7984c5b946ba45cc913b024c73c76721', 'es', 37, '198.168.0.1', 199, '0', '095661', '1', valor, '1');
insert into tbl_transacciones (idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor, valor_inicial, tipoEntorno, moneda, estado, sesion, idioma, pasarela, ip, idpais, tpv, codigo, tasa,  euroEquiv, solDev) values ('16123014730D', '527341458854', '004', 'P', unix_timestamp(), unix_timestamp(), 3462, 3462, 'P', 978, 'A', '7984c5b946ba45cc913b024c73c76721', 'es', 37, '198.168.0.1', 199, '0', '095662', '1', valor, '1');

insert into tbl_devoluciones (id, idtransaccion, idadmin, devpor, fecha, fechaDev, valorDev, observacion) values (null, '16123014730A', '10', 0, unix_timestamp(), 0, '19.65', '1#Prueba de devolución');
insert into tbl_devoluciones (id, idtransaccion, idadmin, devpor, fecha, fechaDev, valorDev, observacion) values (null, '16123014730B', '10', 0, unix_timestamp(), 0, '25.59', '15#Prueba de devolución');
insert into tbl_devoluciones (id, idtransaccion, idadmin, devpor, fecha, fechaDev, valorDev, observacion) values (null, '16123014730C', '10', 0, unix_timestamp(), 0, '21.89', '5#Prueba de devolución');
insert into tbl_devoluciones (id, idtransaccion, idadmin, devpor, fecha, fechaDev, valorDev, observacion) values (null, '16123014730D', '10', 0, unix_timestamp(), 0, '34.62', '18#Prueba de devolución');

insert into tbl_aisDato (idtransaccion, fecha, idCliente, idBeneficiario) values ( '16123014730A', unix_timestamp(), '172', '245');
insert into tbl_aisDato (idtransaccion, fecha, idCliente, idBeneficiario) values ( '16123014730B', unix_timestamp(), '172', '245');
insert into tbl_aisDato (idtransaccion, fecha, idCliente, idBeneficiario) values ( '16123014730C', unix_timestamp(), '172', '245');
insert into tbl_aisDato (idtransaccion, fecha, idCliente, idBeneficiario) values ( '16123014730D', unix_timestamp(), '172', '245');

insert into tbl_aisOrden (idtransaccion, idcliente, idbeneficiario, titOrdenId, envia, recibe, comision) values ('16123014730A', '172', '245', '430', '3965', '3200', envia-recibe);
insert into tbl_aisOrden (idtransaccion, idcliente, idbeneficiario, titOrdenId, envia, recibe, comision) values ('16123014730B', '172', '245', '429', '4559', '4000', envia-recibe);
insert into tbl_aisOrden (idtransaccion, idcliente, idbeneficiario, titOrdenId, envia, recibe, comision) values ('16123014730C', '172', '245', '428', '5189', '4500', envia-recibe);
insert into tbl_aisOrden (idtransaccion, idcliente, idbeneficiario, titOrdenId, envia, recibe, comision) values ('16123014730D', '172', '245', '427', '3462', '3000', envia-recibe);




UPDATE tbl_devoluciones SET devpor = '0', fechaDev = '0' WHERE id = '1662';
UPDATE tbl_devoluciones SET devpor = '0', fechaDev = '0' WHERE id = '1663';
UPDATE tbl_devoluciones SET devpor = '0', fechaDev = '0' WHERE id = '1664';
UPDATE tbl_devoluciones SET devpor = '0', fechaDev = '0' WHERE id = '1665';

update tbl_transacciones set valor = valor_inicial, estado = 'A', tasaDev = 0, euroEquivDev = 0, solDev = 1, fecha_mod = '1483717103' where idtransaccion like '%16123014730%'


