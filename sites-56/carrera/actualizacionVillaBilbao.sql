DROP TABLE IF EXISTS evento;
CREATE TABLE evento (
  id int(11) NOT NULL AUTO_INCREMENT,
  nmombre varchar(200) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table 'evento'
--

INSERT INTO evento VALUES(1, 'Milla Internacional');
INSERT INTO evento VALUES(2, 'Villa de Bilbao');


ALTER TABLE participantes
ADD idevento INT NOT NULL DEFAULT '1' AFTER id ,
add atleta char(1) not null default 'S' after licencia,
ADD observaciones TEXT NULL,
ADD INDEX ( idevento ) ;

alter table prueba 
add idevento int not null default '1' after id,
add sexo char(1) not null default'M' after idevento;

insert into prueba values (null, 2, 'M', '100m', '');
insert into prueba values (null, 2, 'M', '200m', '');
insert into prueba values (null, 2, 'M', '400m', '');
insert into prueba values (null, 2, 'M', '800m', '');
insert into prueba values (null, 2, 'M', '1 500m', '');
insert into prueba values (null, 2, 'M', '5 000m', '');
insert into prueba values (null, 2, 'M', 'Longitud', '');
insert into prueba values (null, 2, 'M', 'Triple', '');
insert into prueba values (null, 2, 'F', '200m', '');
insert into prueba values (null, 2, 'F', '800m', '');
insert into prueba values (null, 2, 'F', '5 000m', '');
insert into prueba values (null, 2, 'F', 'Triple', '');

DROP TABLE IF EXISTS representantes;
CREATE TABLE representantes (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
idparticipante INT NOT NULL ,
nombre VARCHAR( 150 ) NOT NULL ,
apellido VARCHAR( 150 ) NOT NULL ,
correo VARCHAR( 150 ) NOT NULL ,
tel VARCHAR( 20 ) NOT NULL
) ENGINE = MYISAM ;

DROP TABLE IF EXISTS registros;
CREATE TABLE registros (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
idparticipante INT NOT NULL ,
idprueba INT NOT NULL ,
reg1 VARCHAR( 100 ) NOT NULL ,
reg2 VARCHAR( 100 ) NOT NULL ,
reg3 VARCHAR( 100 ) NOT NULL
) ENGINE = MYISAM ;

