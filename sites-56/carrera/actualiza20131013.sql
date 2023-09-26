INSERT INTO evento (id , nmombre) VALUES (NULL , 'Escuela de Atletismo de Bilbao');
INSERT INTO prueba (id, idevento, sexo, nombre, corto) VALUES (NULL, '6', 'M', 'Pre-benjamin', ''), (NULL, '6', 'M', 'Benjamin', '');
INSERT INTO prueba (id, idevento, sexo, nombre, corto) VALUES (NULL, '6', 'M', 'Alevin', ''), (NULL, '6', 'M', 'Infantil', '');
ALTER TABLE participantes CHANGE idprueba idprueba TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'T-txpete, P-prebenjamin, I-infantil, A-abierta'