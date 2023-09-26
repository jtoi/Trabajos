##Dia 12 - 13

##update de las operaciones Aceptadas día 12-13
update tbl_transacciones set tasa = 1.2446, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-12 14:01:01') and unix_timestamp('2021-05-13 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 12-13
update tbl_transacciones set tasa = 1.2446, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-12 14:01:01') and unix_timestamp('2021-05-13 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 12
update tbl_transacciones set tasaDev = 1.2446, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-12 14:01:01') and unix_timestamp('2021-05-13 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 13 - 14

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2446, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-13 14:01:01') and unix_timestamp('2021-05-14 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2446, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-13 14:01:01') and unix_timestamp('2021-05-14 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2446, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-13 14:01:01') and unix_timestamp('2021-05-14 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 14 - 15

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2460, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-14 14:01:01') and unix_timestamp('2021-05-15 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2460, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-14 14:01:01') and unix_timestamp('2021-05-15 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2460, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-14 14:01:01') and unix_timestamp('2021-05-15 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 15 - 16

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2460, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-15 14:01:01') and unix_timestamp('2021-05-16 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2460, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-15 14:01:01') and unix_timestamp('2021-05-16 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2460, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-15 14:01:01') and unix_timestamp('2021-05-16 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 16 - 17

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2460, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-16 14:01:01') and unix_timestamp('2021-05-17 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2460, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-16 14:01:01') and unix_timestamp('2021-05-17 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2460, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-16 14:01:01') and unix_timestamp('2021-05-17 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 17 - 18

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2525, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-17 14:01:01') and unix_timestamp('2021-05-18 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854 and idtransaccion not in ('210517200469');

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2525, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-17 14:01:01') and unix_timestamp('2021-05-18 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854 and idtransaccion not in ('210517200469');

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2525, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-17 14:01:01') and unix_timestamp('2021-05-18 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854 and idtransaccion not in ('210517200469');



##Dia 18 - 19

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2525, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-18 14:01:01') and unix_timestamp('2021-05-19 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2525, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-18 14:01:01') and unix_timestamp('2021-05-19 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2525, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-18 14:01:01') and unix_timestamp('2021-05-19 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 19 - 20

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2596, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-19 14:01:01') and unix_timestamp('2021-05-20 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2596, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-19 14:01:01') and unix_timestamp('2021-05-20 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2596, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-19 14:01:01') and unix_timestamp('2021-05-20 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 20 - 21

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2553, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-20 14:01:01') and unix_timestamp('2021-05-21 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2553, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-20 14:01:01') and unix_timestamp('2021-05-21 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2553, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-20 14:01:01') and unix_timestamp('2021-05-21 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 21 - 22

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2592, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-21 14:01:01') and unix_timestamp('2021-05-22 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2592, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-21 14:01:01') and unix_timestamp('2021-05-22 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2592, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-21 14:01:01') and unix_timestamp('2021-05-22 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 22 - 23

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2592, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-22 14:01:01') and unix_timestamp('2021-05-23 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2592, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-22 14:01:01') and unix_timestamp('2021-05-23 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2592, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-22 14:01:01') and unix_timestamp('2021-05-23 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 23 - 24

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2592, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-23 14:01:01') and unix_timestamp('2021-05-24 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2592, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-23 14:01:01') and unix_timestamp('2021-05-24 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2592, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-23 14:01:01') and unix_timestamp('2021-05-24 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 24 - 25

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2554, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-24 14:01:01') and unix_timestamp('2021-05-25 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2554, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-24 14:01:01') and unix_timestamp('2021-05-25 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2554, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-24 14:01:01') and unix_timestamp('2021-05-25 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 25 - 26

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2631, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-25 14:01:01') and unix_timestamp('2021-05-26 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2631, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-25 14:01:01') and unix_timestamp('2021-05-26 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2631, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-25 14:01:01') and unix_timestamp('2021-05-26 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 26 - 27

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2559, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-26 14:01:01') and unix_timestamp('2021-05-27 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2559, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-26 14:01:01') and unix_timestamp('2021-05-27 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2559, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-26 14:01:01') and unix_timestamp('2021-05-27 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 27 - 28

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2559, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-27 14:01:01') and unix_timestamp('2021-05-28 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2559, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-27 14:01:01') and unix_timestamp('2021-05-28 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2559, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-27 14:01:01') and unix_timestamp('2021-05-28 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;



##Dia 28 - 29

##update de las operaciones Aceptadas día 13-14
update tbl_transacciones set tasa = 1.2569, euroEquiv = valor_inicial/100/tasa
WHERE fecha_mod between unix_timestamp('2021-05-28 14:01:01') and unix_timestamp('2021-05-29 14:01:00') AND estado IN ('A')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones devueltas y reclamadas pero que se pagaron 13-14
update tbl_transacciones set tasa = 1.2569, euroEquiv = valor_inicial/100/tasa
WHERE fecha between unix_timestamp('2021-05-28 14:01:01') and unix_timestamp('2021-05-29 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;

##update de las operaciones realizadas otro día y devueltas el 13-14
update tbl_transacciones set tasaDev = 1.2569, euroEquivDev = valor/100/tasaDev
WHERE fecha_mod between unix_timestamp('2021-05-28 14:01:01') and unix_timestamp('2021-05-29 14:01:00') AND estado IN ('B','V','R')
AND moneda = '840' and tipoOperacion = 'P'  and idcomercio != 527341458854;
