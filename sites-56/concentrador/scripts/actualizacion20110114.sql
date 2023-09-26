INSERT INTO tbl_pasarela (idPasarela, nombre, cuenta) VALUES (NULL, 'BBVA Amex', 'Pago con tarjetas Amex');
ALTER TABLE tbl_pasarela ADD datos VARCHAR( 500 ) NULL COMMENT 'cadena separada por comas con los datos de la pasarela ver index.php';
UPDATE tbl_pasarela SET datos = '47;57;5C;35;25;50;5C;2F;72;7D;05;70;02;03;75;73;79;1A;6C;1A,santaemi,B9550206800001,999999,234623452343,https://www.concentradoramf.com/rep/llegada.php,https://www.concentradoramf.com/rep/,https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_RecepOpModeloServidor' WHERE tbl_pasarela.idPasarela =1;
UPDATE tbl_pasarela SET datos = '45;52;5C;4C;57;23;5C;5B;71;0A;70;02;72;77;03;07;7A;1B;1A;6F,santaemi,B9550206800004,999999,234623452343,https://www.concentradoramf.com/rep/llegada.php,https://www.concentradoramf.com/rep/,https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_RecepOpModeloServidor' WHERE tbl_pasarela.idPasarela =3;
UPDATE tbl_pasarela SET datos = '45;57;5D;40;20;57;5F;2D;03;08;06;04;76;00;76;06;7D;18;6F;6F,santaemi,B9550206800005,999999,234623452343,https://www.concentradoramf.com/rep/llegada.php,https://www.concentradoramf.com/rep/,https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_RecepOpModeloServidor' WHERE tbl_pasarela.idPasarela =8;

ALTER TABLE tbl_pasarela ADD tipo CHAR( 1 ) NOT NULL DEFAULT 'P' COMMENT 'P- pagos, T- transferencias, R- prueba' AFTER nombre;
update tbl_pasarela set tipo = 'T' where idPasarela in (5,6,7);

insert into tbl_pasarela (tipo, nombre, cuenta, datos) values ('R', 'BBVA Prueba', 'Prueba', '43;52;28;35;22;57;5A;28;7B;09;01;03;74;70;73;04;79;13;1C;1D,santaemi,B9550206800002,999999,234623452343,https://www.concentradoramf.com/rep/llegada.php,https://www.concentradoramf.com/rep/,https://w3.grupobbva.com/TLPV/tlpv/TLPV_pub_RecepOpModeloServidor');

