-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '-04:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE arte_db;
ALTER DATABASE arte_db DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;

##utf8_spanish_ci

DROP TABLE IF EXISTS tbl_admin;
CREATE TABLE tbl_admin (
	id int(11) NOT NULL AUTO_INCREMENT,
	idrol int(11) NOT NULL,
	ididioma int(11) NOT NULL,
	nombre varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
	email varchar(150) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
	activo tinyint(1) NOT NULL DEFAULT '1',
	md5 varchar(256) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
	fechaPass int(11) NOT NULL DEFAULT '973835640',
	md5Old varchar(256) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
	fechamod int(11) NOT NULL DEFAULT '0',
	fecha_visita int(11) NOT NULL DEFAULT '0',
	ip varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
	PRIMARY KEY (id),
	KEY idrol (idrol),
	KEY activo (activo),
	KEY fechamod (fechamod),
	KEY fechaPass (fechaPass),
	KEY ididioma (ididioma),
	KEY md5 (md5(255)),
	CONSTRAINT tbl_admin_ibfk_1 FOREIGN KEY (idrol) REFERENCES tbl_roles (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT tbl_admin_ibfk_2 FOREIGN KEY (ididioma) REFERENCES tbl_idioma (id) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_adminIdioma;
CREATE TABLE tbl_adminIdioma (
	id int(11) NOT NULL AUTO_INCREMENT,
	idioma char(2) COLLATE utf8_spanish_ci NOT NULL,
	frase varchar(150) COLLATE utf8_spanish_ci NOT NULL,
	texto varchar(200) COLLATE utf8_spanish_ci NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY idioma_frase (idioma,frase)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_artista;
CREATE TABLE tbl_artista (
	id int(11) NOT NULL AUTO_INCREMENT,
	idimg int(11) DEFAULT NULL,
	nombre varchar(200) COLLATE utf8_spanish_ci NOT NULL,
	seudonimo varchar(100) COLLATE utf8_spanish_ci NOT NULL,
	correo varchar(150) COLLATE utf8_spanish_ci NOT NULL,
	direccion varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
	coordenadas varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'coordenadas del estudio para usar en mapa',
	activo tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-activo, 0-desactivado',
	fechamod int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	UNIQUE KEY seudonimo (seudonimo),
	UNIQUE KEY correo (correo),
	KEY idimg (idimg),
	KEY activo (activo),
	CONSTRAINT tbl_artista_ibfk_1 FOREIGN KEY (idimg) REFERENCES tbl_imagenes (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_bitacora;
CREATE TABLE tbl_bitacora (
	id int(11) NOT NULL AUTO_INCREMENT,
	idadmin int(11) NOT NULL,
	texto text COLLATE utf8_spanish_ci NOT NULL,
	fechamod int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	KEY idadmin (idadmin),
	CONSTRAINT tbl_bitacora_ibfk_2 FOREIGN KEY (idadmin) REFERENCES tbl_admin (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_cliente;
CREATE TABLE tbl_cliente (
	id int(11) NOT NULL AUTO_INCREMENT,
	idartista int(11) NOT NULL,
	nombre varchar(150) COLLATE utf8_spanish_ci NOT NULL,
	telfcliente varchar(20) COLLATE utf8_spanish_ci NULL,
	correo varchar(150) COLLATE utf8_spanish_ci NOT NULL,
	direccion varchar(250) COLLATE utf8_spanish_ci NULL,
	contacto varchar(150) COLLATE utf8_spanish_ci NOT NULL,
	telefcontacto varchar(20) COLLATE utf8_spanish_ci NULL,
	fechamod int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	KEY idartista (idartista),
	CONSTRAINT tbl_cliente_ibfk_1 FOREIGN KEY (idartista) REFERENCES tbl_artista (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_colArtistaAdmin;
CREATE TABLE tbl_colArtistaAdmin (
	id int(11) NOT NULL AUTO_INCREMENT,
	idartista int(11) NOT NULL,
	idadmin int(11) NOT NULL,
	PRIMARY KEY (id),
	KEY idartista (idartista),
	KEY idadmin (idadmin),
	CONSTRAINT tbl_colArtistaAdmin_ibfk_2 FOREIGN KEY (idartista) REFERENCES tbl_artista (id) ON DELETE CASCADE ON UPDATE no action,
	CONSTRAINT tbl_colArtistaAdmin_ibfk_3 FOREIGN KEY (idadmin) REFERENCES tbl_admin (id) ON DELETE CASCADE ON UPDATE no action
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


drop table if exists tbl_colArtistaIdioma;
create table tbl_colArtistaIdioma (
	id int(11) not null auto_increment,
	idartista int(11) not null,
	ididioma int(11) not null,
	primary key (id),
	key idartista (idartista),
	key ididioma (ididioma),
	CONSTRAINT tbl_colArtistaIdioma_ibfk_1 FOREIGN KEY (idartista) REFERENCES tbl_artista (id) on delete cascade,
	CONSTRAINT tbl_colArtistaIdioma_ibfk_2 FOREIGN KEY (ididioma) REFERENCES tbl_idioma (id) on delete cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_colImagenesObra;
CREATE TABLE tbl_colImagenesObra (
	id int(11) NOT NULL AUTO_INCREMENT,
	idobra int(11) NOT NULL,
	idimagen int(11) NOT NULL,
	PRIMARY KEY (id),
	KEY idobra (idobra),
	KEY idimagen (idimagen),
	CONSTRAINT tbl_colImagenesObra_ibfk_1 FOREIGN KEY (idobra) REFERENCES tbl_obra (id),
	CONSTRAINT tbl_colImagenesObra_ibfk_2 FOREIGN KEY (idimagen) REFERENCES tbl_imagenes (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_colOfertaObra;
CREATE TABLE tbl_colOfertaObra (
	id int(11) NOT NULL AUTO_INCREMENT,
	idobra int(11) NOT NULL,
	idoferta int(11) NOT NULL,
	precio float DEFAULT NULL,
	PRIMARY KEY (id),
	KEY idobra (idobra),
	KEY idoferta (idoferta),
	CONSTRAINT tbl_colOfertaObra_ibfk_1 FOREIGN KEY (idoferta) REFERENCES tbl_oferta (id),
	CONSTRAINT tbl_colOfertaObra_ibfk_2 FOREIGN KEY (idobra) REFERENCES tbl_obra (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_colSetupAdmin;
CREATE TABLE tbl_colSetupAdmin (
	id int(11) NOT NULL AUTO_INCREMENT,
	idsetup int(11) NOT NULL,
	idadmin int(11) NOT NULL,
	valor varchar(50) COLLATE utf8_spanish_ci NOT NULL,
	fechamod int(11) NOT NULL,
	PRIMARY KEY (id),
	KEY idadmin (idadmin),
	KEY idsetup (idsetup),
	CONSTRAINT tbl_colSetupAdmin_ibfk_2 FOREIGN KEY (idadmin) REFERENCES tbl_admin (id) ON DELETE cascade ON UPDATE NO ACTION,
	CONSTRAINT tbl_colSetupAdmin_ibfk_1 FOREIGN KEY (idsetup) REFERENCES tbl_setup (id) ON DELETE cascade ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_cuentaBanc;
CREATE TABLE tbl_cuentaBanc (
	id int(11) NOT NULL AUTO_INCREMENT,
	idartista int(11) NOT NULL,
	idmoneda int(11) NOT NULL,
	texto text COLLATE utf8_spanish_ci NULL,
	nombre varchar(100) COLLATE utf8_spanish_ci NOT NULL,
	montoInicial float NOT NULL DEFAULT '0',
	fechamod int(11) NOT NULL,
	PRIMARY KEY (id),
	KEY idartista (idartista),
	KEY idmoneda (idmoneda),
	CONSTRAINT tbl_cuentaBanc_ibfk_1 FOREIGN KEY (idartista) REFERENCES tbl_artista (id),
	CONSTRAINT tbl_cuentaBanc_ibfk_2 FOREIGN KEY (idmoneda) REFERENCES tbl_moneda (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_curriculo;
CREATE TABLE tbl_curriculo (
	id int(11) NOT NULL AUTO_INCREMENT,
	idartista int(11) NOT NULL,
	idadmin int(11) NOT NULL COMMENT 'ult usuario que alteró currículo',
	fechamod int(11) NOT NULL DEFAULT '0',
	cuentaBanc varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
	PRIMARY KEY (id),
	KEY idartista (idartista),
	KEY idadmin (idadmin),
	CONSTRAINT tbl_curriculo_ibfk_1 FOREIGN KEY (idartista) REFERENCES tbl_artista (id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT tbl_curriculo_ibfk_2 FOREIGN KEY (idadmin) REFERENCES tbl_admin (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_debug;
CREATE TABLE tbl_debug (
	id int(11) NOT NULL AUTO_INCREMENT,
	nombre varchar(100) COLLATE utf8_spanish_ci NOT NULL,
	valor varchar(300) COLLATE utf8_spanish_ci NOT NULL,
	fechamod int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_ediciones;
CREATE TABLE tbl_ediciones (
	id int(11) NOT NULL AUTO_INCREMENT,
	idobra int(11) NOT NULL,
	idestado int(11) NOT NULL,
	idpago int(11) NOT NULL DEFAULT '1',
	idmoneda int(11) NOT NULL,
	inventario varchar(20) COLLATE utf8_spanish_ci NOT NULL,
	ubicacion varchar(150) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'Estudio Artista',
	vendidoa varchar(200) COLLATE utf8_spanish_ci NOT NULL,
	vendidopor varchar(200) COLLATE utf8_spanish_ci NOT NULL,
	costo float NOT NULL DEFAULT '0',
	precio float NOT NULL DEFAULT '0',
	precioventa float NOT NULL DEFAULT '0',
	fecha int(11) NOT NULL,
	fechaventa int(11) DEFAULT NULL,
	fechamod int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	KEY idobra (idobra),
	KEY idestado (idestado),
	KEY idpago (idpago),
	KEY idmoneda (idmoneda),
	CONSTRAINT tbl_ediciones_ibfk_1 FOREIGN KEY (idestado) REFERENCES tbl_estado (id),
	CONSTRAINT tbl_ediciones_ibfk_2 FOREIGN KEY (idobra) REFERENCES tbl_obra (id),
	CONSTRAINT tbl_ediciones_ibfk_3 FOREIGN KEY (idpago) REFERENCES tbl_estadoPago (id),
	CONSTRAINT tbl_ediciones_ibfk_4 FOREIGN KEY (idmoneda) REFERENCES tbl_moneda (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_estado;
CREATE TABLE tbl_estado (
	id int(11) NOT NULL AUTO_INCREMENT,
	nombre varchar(150) COLLATE utf8_spanish_ci NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_estadoPago;
CREATE TABLE tbl_estadoPago (
	id int(11) NOT NULL AUTO_INCREMENT,
	nombre varchar(150) COLLATE utf8_spanish_ci NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_facturacion;
CREATE TABLE tbl_facturacion (
	id int(11) NOT NULL AUTO_INCREMENT,
	idedicion int(11) NOT NULL,
	idcliente int(11) NOT NULL,
	idcuenta int(11) NOT NULL,
	idestado int(11) NOT NULL,
	facturado float NOT NULL DEFAULT '0',
	pagado float NOT NULL DEFAULT '0',
	fecha int(11) NOT NULL DEFAULT '0',
	fechamod int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	KEY idartista (idedicion),
	KEY idcliente (idcliente),
	KEY idcuenta (idcuenta),
	KEY idestado (idestado),
	CONSTRAINT tbl_facturacion_ibfk_6 FOREIGN KEY (idestado) REFERENCES tbl_estadoPago (id),
	CONSTRAINT tbl_facturacion_ibfk_2 FOREIGN KEY (idcliente) REFERENCES tbl_cliente (id),
	CONSTRAINT tbl_facturacion_ibfk_3 FOREIGN KEY (idcuenta) REFERENCES tbl_cuentaBanc (id),
	CONSTRAINT tbl_facturacion_ibfk_5 FOREIGN KEY (idedicion) REFERENCES tbl_ediciones (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_idioma;
CREATE TABLE tbl_idioma (
	id int(11) NOT NULL AUTO_INCREMENT,
	idioma varchar(20) COLLATE utf8_spanish_ci NOT NULL,
	iso2 char(2) COLLATE utf8_spanish_ci NOT NULL,
	PRIMARY KEY (id),
	key iso2 (iso2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

DROP TABLE IF EXISTS tbl_imagenes;
CREATE TABLE tbl_imagenes (
	id int(11) NOT NULL AUTO_INCREMENT,
	tipo char(1) COLLATE utf8_spanish_ci NOT NULL DEFAULT '1' COMMENT '1-img de la obra, 2-img del artista',
	direccion varchar(150) COLLATE utf8_spanish_ci NOT NULL,
	PRIMARY KEY (id),
	KEY tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_medio;
CREATE TABLE tbl_medio (
	id int(11) NOT NULL AUTO_INCREMENT,
	nombre varchar(150) COLLATE utf8_spanish_ci NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_moneda;
CREATE TABLE tbl_moneda (
	id int(11) NOT NULL,
	moneda char(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
	denominacion varchar(40) COLLATE utf8_spanish_ci NOT NULL,
	fechamod int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_obra;
CREATE TABLE tbl_obra (
	id int(11) NOT NULL AUTO_INCREMENT,
	idartista int(11) NOT NULL,
	idserie int(11) NOT NULL,
	idmedio int(11) NOT NULL,
	ano varchar(4) null,
	inventario varchar(20) COLLATE utf8_spanish_ci not NULL,
	fecha int(11) NOT NULL,
	fechamod int(11) NOT NULL DEFAULT '0',
	cantEdiciones smallint(5) unsigned NOT NULL DEFAULT '1',
	PRIMARY KEY (id),
	KEY idartista (idartista),
	KEY idmedio (idmedio),
	KEY idserie (idserie),
	CONSTRAINT tbl_obra_ibfk_1 FOREIGN KEY (idartista) REFERENCES tbl_artista (id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT tbl_obra_ibfk_5 FOREIGN KEY (idmedio) REFERENCES tbl_medio (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT tbl_obra_ibfk_6 FOREIGN KEY (idserie) REFERENCES tbl_series_ibfk_1 (id) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_oferta;
CREATE TABLE tbl_oferta (
	id int(11) NOT NULL AUTO_INCREMENT,
	idartista int(11) NOT NULL,
	idcliente int(11) NOT NULL,
	idadmin int(11) NOT NULL COMMENT 'admin que puso la oferta',
	codigo varchar(50) COLLATE utf8_spanish_ci NOT NULL,
	duracion smallint(4) not null default 15 comment 'cantidad de días que estará válida la oferta',
	valida tinyint(1) not null default 1 comment '1-Si, 0-No',
	fechamod int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	KEY idartista (idartista),
	KEY idcliente (idcliente),
	KEY idadmin (idadmin),
	CONSTRAINT tbl_oferta_ibfk_1 FOREIGN KEY (idartista) REFERENCES tbl_artista (id),
	CONSTRAINT tbl_oferta_ibfk_2 FOREIGN KEY (idadmin) REFERENCES tbl_admin (id),
	CONSTRAINT tbl_oferta_ibfk_3 FOREIGN KEY (idcliente) REFERENCES tbl_cliente (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_roles;
CREATE TABLE tbl_roles (
	id int(11) NOT NULL AUTO_INCREMENT,
	orden smallint(6) NOT NULL DEFAULT '0',
	nombre varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
	caract varchar(500) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_series; 
CREATE TABLE tbl_series (
	id int(11) NOT NULL AUTO_INCREMENT,
	idartista int(11) NOT NULL,
	idtexto int(11) not null,
	ano varchar(14) null,
	fechamod int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	KEY idartista (idartista),
	CONSTRAINT tbl_series_ibfk_1 FOREIGN KEY (idartista) REFERENCES tbl_artista (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS tbl_setup;
CREATE TABLE tbl_setup (
	id int(11) NOT NULL AUTO_INCREMENT,
	tipo tinyint(4) NOT NULL DEFAULT '1' COMMENT '1- var para usuario, 2- var para artistas, 3- var generales',
	nombre varchar(60) COLLATE utf8_spanish_ci NOT NULL,
	valor varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
	descripcion varchar(200) COLLATE utf8_spanish_ci NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='tabla con las variables del setup';


DROP TABLE IF EXISTS tbl_textos;
CREATE TABLE tbl_textos (
	id int(11) NOT NULL AUTO_INCREMENT,
	idtipotexto int(11) NOT NULL,
	idartista int(11) NOT NULL,
	idtexto int(11) NOT NULL comment 'poner como id la fecha',
	idioma char(2) NOT NULL,
	descripción varchar(120) COLLATE utf8_spanish_ci NOT NULL,
	texto text COLLATE utf8_spanish_ci NOT NULL,
	fecha int(11) NOT NULL,
	PRIMARY KEY (id),
	key idtipotexto (idtipotexto),
	KEY idtexto (idtexto),
	KEY idioma (idioma),
	KEY idartista (idartista),
	UNIQUE KEY textos_uk (idartista, idioma, idtexto, idtipotexto),
	CONSTRAINT tbl_textos_ibfk_3 FOREIGN KEY (idioma) REFERENCES tbl_idioma (iso2) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT tbl_textos_ibfk_1 FOREIGN KEY (idartista) REFERENCES tbl_artista (id) ON DELETE NO ACTION ON UPDATE NO ACTION
	,CONSTRAINT tbl_textos_ibfk_2 FOREIGN KEY (idtipotexto) REFERENCES tbl_tipoTexto (id) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Los escritos de los Artistas en los idiomas dados';


DROP TABLE IF EXISTS tbl_tipoTexto;
CREATE TABLE tbl_tipoTexto (
	id int(11) NOT NULL AUTO_INCREMENT,
	descripcion varchar(140) COLLATE utf8_spanish_ci NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;



###### Procedimientos y Triggers

DELIMITER ;;
drop function if exists calcSha;;
create function calcSha(pass varchar(64), correo varchar(150)) 
 returns varchar(64) CHARSET utf8 COLLATE utf8_spanish_ci
		READS SQL DATA
		DETERMINISTIC
RETURN (sha2(concat(pass,correo,'lo realmente hermoso es invisible a los ojos'),256));;

DROP FUNCTION IF EXISTS fn_entrada;;
CREATE FUNCTION fn_entrada(md varchar(64)) 
 RETURNS varchar(200) CHARSET utf8 COLLATE utf8_spanish_ci
	READS SQL DATA
	DETERMINISTIC
RETURN (SELECT concat (r.orden, '|', a.id, '|', a.email, '|', a.nombre, '|', a.idrol, '|', a.ididioma) FROM tbl_admin a, tbl_roles r WHERE a.idrol = r.id and a.md5 = calcSha(md,a.email) and a.activo = 1);;

DROP FUNCTION IF EXISTS split_string;;
CREATE FUNCTION split_string(str VARCHAR(255), delim VARCHAR(12), pos INT) 
 RETURNS varchar(255) CHARSET utf8 COLLATE utf8_spanish_ci
 
 RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(str, delim, pos), LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1), delim, '');;

# Valida si el usuario existe en la base de datos entrando en la variable md la contraseña y la 
# ip en la variable ipent, devuelve los datos del usuario
DROP PROCEDURE IF EXISTS valida_usuario;;
CREATE PROCEDURE valida_usuario(
 IN md varchar(64), 
 IN ipent varchar(20), 
 out nombreU varchar(100), 
 out idusr int,
 out idartista int,
 out seudonimoA varchar(100),
 out email varchar(150),
 out Ordenrol int,
 out idrol int,
 out idioma char(2),
 out imagen varchar(150)
 )
	READS SQL DATA
BEGIN
	declare done tinyint DEFAULT 0;
	declare datosUsr varchar(200);
	
declare cur_login CURSOR for 
	 select r.id, r.seudonimo
	 from tbl_admin a, tbl_artista r, tbl_colArtistaAdmin c
	 where a.md5 = (select calcSha(md,a.email))
		and a.id = c.idadmin 
		and c.idartista = r.id 
		and a.activo = 1;
declare CONTINUE HANDLER FOR NOT FOUND SET done=1;
	
set idusr = idartista = 0;
set seudonimoA = 'Varios';

set datosUsr = fn_entrada(md);
set idusr = split_string(datosUsr, '|', 2);
set nombreU = split_string(datosUsr, '|', 4);
set Ordenrol = split_string(datosUsr, '|', 1);
set email = split_string(datosUsr, '|', 3);
set idrol = split_string(datosUsr, '|', 5);
select iso2 into idioma from tbl_idioma where id = split_string(datosUsr, '|', 6);

-- insert into tbl_debug (nombre,valor) values ('datosUsr', datosUsr);
-- insert into tbl_debug (nombre,valor) values ('nombreU', nombreU);
-- insert into tbl_debug (nombre,valor) values ('id', idusr);
-- insert into tbl_debug (nombre,valor) values ('email', email);
-- insert into tbl_debug (nombre,valor) values ('idioma', split_string(datosUsr, '|', 6));

# busca los datos del usuario conectado
if (Ordenrol > 5) then
		open cur_login;
		cursor_loop:LOOP
		fetch cur_login into idartista, seudonimoA;
		select i.direccion into imagen from tbl_artista r, tbl_imagenes i where i.id = r.idimg and r.id = idartista;

		IF done=1 THEN
			LEAVE cursor_loop;
		END IF;
		end LOOP;
		CLOSE cur_login;
	end if;
	
# insert into tbl_debug (nombre,valor) values ('imagen', imagen);
# insert into tbl_debug (nombre,valor) values ('sql', query);
		
		#salva la conexión
		if idusr is NOT null THEN
			update tbl_admin 
			set fecha_visita = unix_timestamp(), 
			 ip = ipent 
			where id = idusr;
		end IF;
	
end;;

# Inserta los artistas
DROP PROCEDURE IF EXISTS insertaArtista;;
CREATE PROCEDURE insertaArtista(
	IN nomb varchar(200),
	IN seud varchar(100),
	in corr varchar(150),
	in dire varchar(200),
	in coor varchar(20),
	in md varchar(20),
	in act int,
	in idi int,
	in idiw varchar(20),
	out idArt int
)
begin
	declare idAdm int;
	declare idit varchar(20) default idiw;
	declare lidit varchar(4);
	DECLARE i INT DEFAULT 1;

	# inserta el artista y obtiene el id
	insert into tbl_artista (nombre, seudonimo, correo, direccion, coordenadas, activo) values (nomb, seud, corr, dire, coor, act);
	select last_insert_id() into idArt;

	# inserta los idiomas de trabajo
	if instr(idit, ',') then
		idioma: while instr(idit, ',') do
			select left(idit, instr(idit, ',')) into lidit;
			insert into tbl_colArtistaIdioma (idartista, ididioma) values (idArt, lidit);
			select substr(idit,instr(idit, ',')+1) into idit;
			set i = i + 1;
		end while;
	end if;
	if length(idit) > 0 then
		insert into tbl_colArtistaIdioma (idartista, ididioma) values (idArt, idit);
	end if;

	# inserta al artista nuevo como usuario del sistema ligado a sí mismo como artista
	insert into tbl_admin (idrol, ididioma, nombre, email, md5, activo) values (3, idi, nomb, corr, md, act);
	select last_insert_id() into idAdm;

	# insert into tbl_debug (nombre, valor) values ('idadmin', idAdm);
	insert into tbl_colArtistaAdmin (idartista, idadmin) values (idArt, idAdm);

end;;


DROP EVENT IF EXISTS ejemplo;;
CREATE EVENT ejemplo ON SCHEDULE EVERY 5 MINUTE STARTS '2017-09-26 12:39:48' ENDS '2017-10-26 14:00:00' ON COMPLETION PRESERVE ENABLE DO begin
-- insert into tbl_debug (nombre, valor) values ('la fecha', now());
end;;


CREATE TRIGGER tr_admin_bi BEFORE INSERT ON tbl_admin FOR EACH ROW
begin
 set new.fechamod = unix_timestamp();
--	insert into tbl_debug (nombre, valor) values ('login', new.md5);
 insert into tbl_bitacora (idadmin, texto) values (1, concat('Se inserta el usuario ',new.nombre,' con correo ',new.email,' y contraseña ', new.md5));
 set new.md5 = calcSha(new.md5,new.email);
--	insert into tbl_debug (nombre, valor) values ('correo', new.email);
--	insert into tbl_debug (nombre, valor) values ('md5', new.md5);
end;;

## a los nuevos usuarios que se insertan le crea el entorno de variables
## de acuerdo a los valores de las variables generales y 
## asocia cada usuario con el artista si lo lleva
CREATE TRIGGER tr_admin_ai AFTER INSERT ON tbl_admin FOR EACH ROW
begin
	declare idAdm int default new.id;
	declare idArt int;
	declare done int default 0;
	declare varsetup varchar(60);
	declare idsetup int;

	declare cur_setup cursor for select id, nombre from tbl_setup where tipo = 1;
	declare CONTINUE HANDLER FOR NOT FOUND SET done=1;

# crea la configuración inicial de las variables para el nuevo cliente
# leyendo los valores de la misma en las variables generales
	open cur_setup;
	cursor_loop:LOOP
		fetch cur_setup into idsetup, varsetup ;
		IF done=1 THEN
			LEAVE cursor_loop;
		END IF;
# insert into tbl_debug (nombre, valor) values ('idsetup', idsetup);
# insert into tbl_debug (nombre, valor) values ('varsetup', varsetup);
		insert into tbl_colSetupAdmin (idsetup, idadmin, valor) values (idsetup, idAdm, (select valor from tbl_setup where nombre = varsetup and tipo = 3));
	end loop cursor_loop;
	close cur_setup;

 #inserta en la tbl_colArtistaAdmin
	if new.idrol<3 then
		insert into tbl_colArtistaAdmin (idartista, idadmin) (select id, idAdm from tbl_artista);
	end if;

end;;

CREATE TRIGGER tr_admin_bu BEFORE UPDATE ON tbl_admin FOR EACH ROW
begin
	if new.md5 != old.md5 then
# insert into tbl_debug (nombre, valor) values ('id', new.id);
# insert into tbl_debug (nombre, valor) values ('idrol', new.idrol);
# insert into tbl_debug (nombre, valor) values ('nombre', new.nombre);
# insert into tbl_debug (nombre, valor) values ('login', new.md5);
# insert into tbl_debug (nombre, valor) values ('login', new.email);

		set new.md5 = calcSha(new.md5,new.email);
		set new.md5Old = old.md5;
		set new.fechaPass = unix_timestamp();
	end if;

--	si el rol es menos de 3 asocio todos los artistas al admin que se está actualizando
	if new.idrol<3 then
		insert into tbl_colArtistaAdmin (idartista, idadmin) (select id, old.id from tbl_artista);
	end if;

--	si la fecha de visita es null pone la fecha de modificacion de los datos
	if new.fecha_visita is null then
		set new.fechamod = unix_timestamp();
	end if;
end;;


CREATE TRIGGER tr_artista_bi BEFORE INSERT ON tbl_artista FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
	if new.seudonimo is null then
		set new.seudonimo = new.nombre;
	end if;
end;;

CREATE TRIGGER tr_artista_ai AFTER INSERT ON tbl_artista FOR EACH ROW
begin
	declare idAdm int;
	declare rol int;
	declare idArt int default new.id;
	declare done int default 0;
	declare cur_admin cursor for select id, idrol from tbl_admin;
	declare CONTINUE HANDLER FOR NOT FOUND SET done=1;
	

	# inserta en la tbl_colArtistaAdmin a los administradores del sistema para que trabajen con el artista nuevo
	open cur_admin;
	cursor_loop:LOOP
		fetch cur_admin into idAdm, rol;
		IF done=1 THEN
			LEAVE cursor_loop;
		END IF;
		if rol<3 then
			insert into tbl_colArtistaAdmin (idartista, idadmin) values (idArt, idAdm);
		end if;
	END LOOP cursor_loop;
	close cur_admin;
	
end;;

CREATE TRIGGER tr_artista_bu BEFORE UPDATE ON tbl_artista FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
	if new.seudonimo is null then
		set new.seudonimo = old.seudonimo;
	end if;
end;;


CREATE TRIGGER tr_colSetupAdmin_bi BEFORE INSERT ON tbl_colSetupAdmin FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;

CREATE TRIGGER tr_colSetupAdmin_bu BEFORE UPDATE ON tbl_colSetupAdmin FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;


CREATE TRIGGER tr_bitacora_bi BEFORE INSERT ON tbl_bitacora FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;

CREATE TRIGGER tr_bitacora_bu BEFORE UPDATE ON tbl_bitacora FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;


CREATE TRIGGER tr_cliente_bi BEFORE INSERT ON tbl_cliente FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;

CREATE TRIGGER tr_cliente_bu BEFORE UPDATE ON tbl_cliente FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;


CREATE TRIGGER tr_facturacion_bi BEFORE INSERT ON tbl_facturacion FOR EACH ROW
begin
	if new.idestado is null then
		set new.idestado = 2;
	end if;

	if new.fecha is null then
		set new.fecha = unix_timestamp();
	end if;

	set new.fechamod = unix_timestamp();
end;;

CREATE TRIGGER tr_facturacion_bu BEFORE UPDATE ON tbl_facturacion FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;


CREATE TRIGGER tr_cuentaBanc_bi BEFORE INSERT ON tbl_cuentaBanc FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;

CREATE TRIGGER tr_cuentaBanc_bu BEFORE UPDATE ON tbl_cuentaBanc FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;


CREATE TRIGGER tr_curriculo_bi BEFORE INSERT ON tbl_curriculo FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;

CREATE TRIGGER tr_curriculo_bu BEFORE UPDATE ON tbl_curriculo FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;


CREATE TRIGGER tr_debug_bi BEFORE INSERT ON tbl_debug FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;


CREATE TRIGGER tr_ediciones_bi BEFORE INSERT ON tbl_ediciones FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
	if new.fecha is null then
		set new.fecha = unix_timestamp();
	end if;
end;;

CREATE TRIGGER tr_ediciones_bu BEFORE UPDATE ON tbl_ediciones FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;


CREATE TRIGGER tr_moneda_bi BEFORE INSERT ON tbl_moneda FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;

CREATE TRIGGER tr_moneda_bu BEFORE UPDATE ON tbl_moneda FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;


CREATE TRIGGER tr_oferta_bi BEFORE INSERT ON tbl_oferta FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
	set new.codigo = md5(new.fechamod);
end;;

CREATE TRIGGER tr_series_bi BEFORE INSERT ON tbl_series FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;

CREATE TRIGGER tr_series_bu BEFORE UPDATE ON tbl_series FOR EACH ROW
begin
	set new.fechamod = unix_timestamp();
end;;

CREATE TRIGGER tr_textos_bi BEFORE INSERT ON tbl_textos FOR EACH ROW
begin
	set new.fecha = unix_timestamp();
end;;

CREATE TRIGGER tr_textos_bu BEFORE UPDATE ON tbl_textos FOR EACH ROW
begin
	set new.fecha = unix_timestamp();
end;;

DELIMITER ;

SET GLOBAL event_scheduler = ON;



#### Insertando datos

INSERT INTO tbl_setup (nombre, tipo, valor, descripcion) VALUES
('pagtabl', 3, '30', 'cant. de lineas a mostrar en las tablas'),
('separdecim', 3, '.', 'separador de decimales'),
('separmiles', 3, ' ', 'separador de miles'),
('formfecha', 3, 'd/m/Y','formato de las fechas'),
('formhoras', 3, 'H:i', 'formato de las horas'),
('pagtabl', 1, null, 'cant. de lineas a mostrar en las tablas'),
('separdecim', 1, null, 'separador de decimales'),
('separmiles', 1, null, 'separador de miles'),
('formfecha', 1, null, 'formato de las fechas'),
('formhoras', 1, null, 'formato de las horas');

INSERT INTO tbl_estado (id, nombre) VALUES
(1, 'Vendido'),
(2, 'Disponible'),
(3, 'Donado'),
(4, 'Intercambiado'),
(5, 'Reservado'),
(6, 'Archivo'),
(7, 'Destruida'),
(8, 'Consignada'),
(9, 'Expuesta');

insert into tbl_tipoTexto (descripcion) values
('Currículo del artista tbl_curriculo'),
('Factura del artista tbl_curriculo'),
('Observaciones de las ediciones tbl_ediciones'),
('Observaciones de la obra tbl_obra'),
('Statment de las obras tbl_obra'),
('Nombre de las obras tbl_obra'),
('Nombre de las series tbl_series');

INSERT INTO tbl_estadoPago (nombre) VALUES
('No pactado'),
('Pendiente'),
('Pagado'),
('Pago parcial');

INSERT INTO tbl_roles (orden, nombre, caract) VALUES
(0, 'Yo', NULL),
(5, 'Administradores', 'Administradores del Sistema'),
(10,'Usuario', 'Artistas o sus asistentes podrán editar los datos del artista'),
(15,'Invitado', 'Sólo podrán ver no editar ');

INSERT INTO tbl_medio (nombre) VALUES
('Fotografía'),
('Escultura'),
('Pintura'),
('Dibujo'),
('Grabado'),
('Impresión'),
('Varios'),
('Audio Visual'),
('Site Specific'),
('Instalación'),
('Proyecto'),
('Performance');

INSERT INTO tbl_moneda (id, moneda, denominacion) VALUES
('124', 'CAD', 'Dolar Canadiense'),
('152', 'CLP', 'Peso Chileno'),
('170', 'COP', 'Peso Colombiano'),
('32', 'ARS', 'Peso Argentino'),
('356', 'INR', 'Rupia India'),
('392', 'JPY', 'Yen Japones'),
('484', 'MXN', 'Peso Mexicano'),
('604', 'PEN', 'Peso Peruano'),
('756', 'CHF', 'Franco Suizo'),
('826', 'GBP', 'Libra Esterlina'),
('840', 'USD', 'Dolar USA'),
('937', 'VEF', 'Bolivar Venezolano'),
('949', 'TRY', 'Lira Turca'),
('978', 'EUR', 'Euro'),
('192', 'CUP', 'Peso Cubano'),
('931', 'CUC', 'Peso Cubano Convertible'),
('986', 'BRL', 'Real Brasileno');

insert into tbl_idioma (idioma, iso2) values
('Español', 'es'),
('English', 'en');

INSERT INTO tbl_imagenes (id, tipo, direccion) VALUES
(1, '2', 'images/artista/2/profile.jpeg'),
(2, '2', 'images/artista/1/profile.jpeg');

call insertaArtista ('Humberto Díaz', 'Humbe', 'hdiaz@gmail.com', '18 #564 e/ 17 y 15 Vedado, La Habana, Cuba', '23.128221,-82.405088', 'hhhhhh', '1', '1', '1,2', @idArt);
update tbl_artista set idimg = 2 where id = (select @idArt);
call insertaArtista ('Mabel Poblet', 'Mpoblet', 'mpoblet@gmail.com', '11 #564 e/J e I, Vedado, La Habana', '23.143112,-82.390208', 'mmmmmm', '1', '2', '2', @idArt);
update tbl_artista set idimg = 1 where id = (select @idArt);

INSERT INTO tbl_admin (idrol, ididioma, nombre, email, activo, md5, fechaPass, md5Old, fechamod, fecha_visita, ip) VALUES
(3, 2, 'Julio', 'jtoirac@gmail.com', 1, 'dddddd', 973842840, NULL, 0, 0, NULL),
(2, 1, 'Taimi Antunez', 'taimioro@nauta.cu', 1, 'gggggg', 973842840, NULL, 0, 0, NULL),
(2, 1, 'ghjdfg dfghdfg', 'sdfgf@fdgsdf.fgd', 1, 'uno', 973835640, NULL, 0, 0, NULL),
(2, 2, 'tyuertyvert', 'sdfgf@gerteq.fgd', 1, 'dos', 973835640, NULL, 0, 0, NULL),
(2, 1, 'drtysrtyrtsh', 'dghnmnmjd@sdfhgsad.fgsa', 1, 'tres', 973835640, NULL, 0, 0, NULL),
(3, 2, 'gnertgaertg', 'zsdfsdfgd@fdghasd.fg', 1, 'cuatro', 973835640, NULL, 0, 0, NULL),
(4, 1, 'rtkrtfykryu', 'ouioykuy@sdfgsd.dfg', 1, 'cinco', 973835640, NULL, 0, 0, NULL),
(3, 2, 'rfnbernjj s gg', 'rtyjetyery@sdfgasdf.dfx', 1, 'seis', 973835640, NULL, 0, 0, NULL),
(2, 1, 'hetyherthert', 'fghjdgj@sdfgsdf.fg', 1, 'siete', 973835640, NULL, 0, 0, NULL),
(2, 2, 'dhsfghsfdg', 'dsfghsdfh@srdtgsdfg.fgh', 1, 'ocho', 973835640, NULL, 0, 0, NULL),
(2, 1, 'dfghsdfhsdftyku', 'dsfghsdfh@srdtsdfgs.fgh', 1, 'nueve', 973835640, NULL, 0, 0, NULL),
(4, 2, 'dfghsd sdfgg sdf', 'fhjdfsdfgh@rdtydgx.fgh', 1, 'diez', 973835640, NULL, 0, 0, NULL);

INSERT INTO tbl_cliente (idartista, nombre, correo, telfcliente, fechamod) VALUES
('1', 'cliente de Humbe', 'hdfhgsd@gmail.com', '35463543', unix_timestamp());

INSERT INTO tbl_colArtistaAdmin (idartista, idadmin) VALUES
(2, 3),
(1, 6),
(2, 7),
(2, 8),
(2, 12);

INSERT INTO tbl_adminIdioma (idioma, frase, texto) VALUES
('es', 'Datos Personales', 'Datos Personales'),
('en', 'Datos Personales', 'Personal Data'),
('es', 'Error en la entrada del nombre', 'Error en la entrada del nombre'),
('en', 'Error en la entrada del nombre', 'Error typing the name'),
('es', 'Error en la entrada del correo', 'Error en la entrada del correo'),
('en', 'Error en la entrada del correo', 'Error typing email'),
('es', 'Error en la entrada de la contrasena debe poner hasta 12 caracteres que sean digitos o letras', 'Error en la entrada de la contraseña debe poner hasta 12 caracteres que sean digitos o letras'),
('en', 'Error en la entrada de la contrasena debe poner hasta 12 caracteres que sean digitos o letras', 'Error typing password, type until 12 characters digits or letters'),
('es', 'Error en la entrada de la fecha', 'Error en la entrada de la fecha'),
('en', 'Error en la entrada de la fecha', 'Error typing date'),
('es', 'Error en la entrada del texto', 'Error en la entrada del texto'),
('en', 'Error en la entrada del texto', 'Error typing text'),
('es', 'Error en la entrada de la hora', 'Error en la entrada de la hora'),
('en', 'Error en la entrada de la hora', 'Error typing time'),
('es', 'Error en identificador del usuario', 'Error en identificador del usuario'),
('en', 'Error en identificador del usuario', 'Error user identification'),
('es', 'Error en la entrada del separador decimal', 'Error en la entrada del separador decimal'),
('en', 'Error en la entrada del separador decimal', 'Error typing decimal point'),
('es', 'Error en la entrada de la moneda', 'Error en la entrada de la moneda'),
('en', 'Error en la entrada de la moneda', 'Erro typing currency'),
('es', 'Error en la entrada del separador de miles', 'Error en la entrada del separador de miles'),
('en', 'Error en la entrada del separador de miles', 'Erro typing thousands separator'),
('es', 'Error: Se produjo un error al salvar los datos llame al desarrollador', 'Error: Se produjo un error al salvar los datos llame al desarrollador'),
('en', 'Error: Se produjo un error al salvar los datos llame al desarrollador', 'Error: there is an error please call to developer'),
('es', 'Datos salvados correctamente.', 'Datos salvados correctamente.'),
('en', 'Datos salvados correctamente.', 'Data saved'),
('es', 'Error: No es un formato de fecha valido', 'Error: No es un formato de fecha valido'),
('en', 'Error: No es un formato de fecha valido', 'Error: Invalid date format'),
('es', 'Nombre', 'Nombre'),
('en', 'Nombre', 'Name'),
('es', 'Contrasena', 'Contraseña'),
('en', 'Contrasena', 'Password'),
('es', 'Vuelva a escribirla', 'Vuelva a escribirla'),
('en', 'Vuelva a escribirla', 'Write it again'),
('es', 'Formato de fechas', 'Formato de fechas'),
('en', 'Formato de fechas', 'Date format'),
('es', 'Formato de Horas', 'Formato de Horas'),
('en', 'Formato de Horas', 'Time format'),
('es', 'Punto (.)', 'Punto (.)'),
('en', 'Punto (.)', 'Dot (.)'),
('es', 'Coma (,)', 'Coma (,)'),
('en', 'Coma (,)', 'Comma (,)'),
('es', 'Separador de decimales', 'Separador de decimales'),
('en', 'Separador de decimales', 'Separator for decimal point'),
('es', 'Sin separador', 'Sin separador'),
('en', 'Sin separador', 'Without separator'),
('es', 'Espacio ( )', 'Espacio ( )'),
('en', 'Espacio ( )', 'Blank space ( )'),
('es', 'Separador de miles', 'Separador de miles'),
('en', 'Separador de miles', 'Thousands separator'),
('es', 'Obra', 'Obra'),
('en', 'Obra', 'Art Work'),
('es', 'curriculo', 'currículo'),
('en', 'curriculo', 'curriculum'),
('es', 'cliente', 'cliente'),
('en', 'cliente', 'client'),
('es', 'contacto', 'contacto'),
('en', 'contacto', 'contac'),
('es', 'admin', 'admin'),
('en', 'admin', 'admin'),
('es', 'Usuarios', 'Usuarios'),
('en', 'Usuarios', 'Users'),
('es', 'Artistas', 'Artistas'),
('en', 'Artistas', 'Artists'),
('es', 'Artista', 'Artista'),
('en', 'Artista', 'Artist'),
('es', 'Estado Obra', 'Estado Obra'),
('en', 'Estado Obra', 'Art Work Status'),
('es', 'Estado Pago', 'Estado Pago'),
('en', 'Estado Pago', 'Payment Status'),
('es', 'Medio', 'Medio'),
('en', 'Medio', 'Media'),
('es', 'Moneda', 'Moneda'),
('en', 'Moneda', 'Currency'),
('es', 'Roles', 'Roles'),
('en', 'Roles', 'Rols'),
('es', 'Configuracion', 'Configuración'),
('en', 'Configuracion', 'Configuration'),
('es', 'Bienvenido al sitio', 'Bienvenido a ArteOrganizer'),
('en', 'Bienvenido al sitio', 'Welcome to ArteOrganizer'),
('es', 'Error en la entrada del artista', 'Error en la entrada del artista'),
('en', 'Error en la entrada del artista', 'Error typing Artist'),
('es', 'Error en la entrada del rol', 'Error en la entrada del rol'),
('en', 'Error en la entrada del rol', 'Error typing rol'),
('es', 'Error en la accion', 'Error en la acción'),
('en', 'Error en la accion', 'Error in action'),
('es', 'Error cambiando activo', 'Error cambiando activo'),
('en', 'Error cambiando activo', 'Error changing active'),
('es', 'Si', 'Si'),
('en', 'Si', 'Yes'),
('es', 'No', 'No'),
('en', 'No', 'No'),
('es', 'Datos correctamente guardados', 'Datos correctamente guardados'),
('en', 'Datos correctamente guardados', 'Data saved'),
('es', 'Activo', 'Activo'),
('en', 'Activo', 'Active'),
('es', 'Todo', 'Todo'),
('en', 'Todo', 'All'),
('es', 'Error: Esta direccion de correo ya existe en la base de datos', 'Error: Esta dirección de correo ya existe en la base de datos'),
('en', 'Error: Esta direccion de correo ya existe en la base de datos', 'Error: This email is already in Data Base'),
('es', 'Editar', 'Editar'),
('en', 'Editar', 'Edit'),
('es', 'Desactivar', 'Desactivar'),
('en', 'Desactivar', 'Desactive'),
('es', 'Usuario', 'Usuario'),
('en', 'Usuario', 'User'),
('es', 'Correo', 'Correo'),
('en', 'Correo', 'Email'),
('es', 'Rol', 'Rol'),
('en', 'Rol', 'Rol'),
('es', 'Fecha Mod', 'Fecha Mod'),
('en', 'Fecha Mod', 'Mod. Date'),
('es', 'Fecha Visita', 'Fecha Visita'),
('en', 'Fecha Visita', 'Visit Date'),
('es', 'Idiomas de trabajo', 'Idiomas de trabajo'),
('en', 'Idiomas de trabajo', 'Work Languages'),
('es', 'Idioma personal', 'Idioma personal'),
('en', 'Idioma personal', 'Personal Language'),
('es', 'Error en la entrada del idioma', 'Error en la entrada del idioma'),
('en', 'Error en la entrada del idioma', 'Error selecting language'),
('es', 'Error en la entrada del idioma de trabajo', 'Error en la entrada del idioma de trabajo'),
('en', 'Error en la entrada del idioma de trabajo', 'Error selecting work language'),
('es', 'Insertar', 'Insertar'),
('en', 'Insertar', 'Insert'),
('es', 'Buscar', 'Buscar'),
('en', 'Buscar', 'Search'),
('es', 'Previo', 'Previo'),
('en', 'Previo', 'Back'),
('es', 'Proximo', 'Próximo'),
('en', 'Proximo', 'Next'),
('es', 'Error: El cuerpo del correo no puede estar vacio', 'Error: El cuerpo del correo no puede estar vacío'),
('en', 'Error: El cuerpo del correo no puede estar vacio', 'Error: Message body cant left blank'),
('es', 'Inscripcion en el sitio ArteOrganizer', 'Inscripción en el sitio ArteOrganizer'),
('en', 'Inscripcion en el sitio ArteOrganizer', 'ArteOrganizer Inscription'),
('es', 'Generar contrasena', 'Generar contraseña'),
('en', 'Generar contrasena', 'Regenerate Password'),
('es', 'Error cambiando contrasena', 'Error cambiando contraseña'),
('en', 'Error cambiando contrasena', 'Error changing password'),
('es', 'nohtml', 'Para ver el mensaje use un lector de correos compatible HTML!'),
('en', 'nohtml', 'To view the message, please use an HTML compatible email viewer!'),
('es', 'correo bienvenida al sitio', 'Usted ha sido inscrito en ArteOrganizer, la aplicación que mejorará tu desempeño organizando tu trabajo diario y haciéndote más productivo.'),
('en', 'correo bienvenida al sitio', 'You have been registered to ArteOrganizer, the application that will improve your efforts organizing your daily work and making you more productive.'),
('es', 'correo renovacion contrasena', 'A usted se le ha renovado las credeciales para la entrada a la web de ArteOrganizer.'),
('en', 'correo renovacion contrasena', 'Your credentials for entering at ArteOrganizer application has been renewed.'),
('es', 'Renovacion de credenciales', 'Renovación de credenciales'),
('en', 'Renovacion de credenciales', 'Credentials renewal'),
('es', 'Seudonimo', 'Seudónimo'),
('en', 'Seudonimo', 'Pseudonym'),
('es', 'Direccion', 'Dirección'),
('en', 'Direccion', 'Address'),
('es', 'Coordenadas', 'Coordenadas Geográficas del Estudio'),
('en', 'Coordenadas', 'Studios Geographical Coordinates'),
('es', 'Error en la entrada de la direccion', 'Error en la entrada de la dirección'),
('en', 'Error en la entrada de la direccion', 'Error in address'),
('es', 'Error en la entrada de las coordenadas', 'Error en la entrada de las coordenadas'),
('en', 'Error en la entrada de las coordenadas', 'Error in Geographical Coordinates'),
('es', 'Imagen del artista', 'Imagen del artista'),
('en', 'Imagen del artista', 'Artists photo'),
('es', 'Tipo de archivo no valido', 'Error: Tipo de archivo no válido'),
('en', 'Tipo de archivo no valido', 'Error: Not a valid file'),
('es', 'La imagen excede el tamano maximo soportado', 'Error: La imagen excede el tamaño máximo soportado'),
('en', 'La imagen excede el tamano maximo soportado', 'Error: Image exceeds the maximum supported size'),
('es', 'La imagen no se subio correctamente', 'Error: La imagen no se subió correctamente'),
('en', 'La imagen no se subió correctamente', 'Error: The image did not upload correctly'),
('es', 'Se debe seleccionar un archivo', 'Error: Se debe seleccionar un archivo'),
('en', 'Se debe seleccionar un archivo', 'Error: A file must be selected'),
('es', 'La imagen es muy pesada', 'Error: La imagen es muy pesada'),
('en', 'La imagen es muy pesada', 'Error: Image too heavy'),
('es', 'La imagen dio error', '. La imagen dió error - '),
('en', 'La imagen dio error', '. There is an error with image - '),
('es', 'Puede intentar subirla nuevamente modificando el artista', '. . Puede intentar subirla nuevamente modificando el artista'),
('en', 'Puede intentar subirla nuevamente modificando el artista', '. You can try uploading it again by editing artists data'),
('es', 'Estado de la Obra', 'Estado de la Obra'),
('en', 'Estado de la Obra', 'Art Work Status'),
('es', 'Estado', 'Estado'),
('en', 'Estado', 'Status'),
('es', 'Estado del Pago', 'Estado del Pago'),
('en', 'Estado del Pago', 'Payment Status'),
('es', 'Medio de expresion', 'Medio de expresión'),
('en', 'Medio de expresion', 'Expression Way'),
('es', 'Error en identificador del medio', 'Error en identificador del medio'),
('en', 'Error en identificador del medio', 'Error in expression way identificator'),
('es', 'Enviar', 'Enviar'),
('en', 'Enviar', 'Send'),
('es', 'Cancelar', 'Cancelar'),
('en', 'Cancelar', 'Reset'),
('es', 'Pago Parcial', 'Pago Parcial'),
('en', 'Pago Parcial', 'Partial Payment'),
('es', 'Pagado', 'Pagado'),
('en', 'Pagado', 'Payed'),
('es', 'Pendiente', 'Pendiente'),
('en', 'Pendiente', 'Pending'),
('es', 'No pactado', 'No pactado'),
('en', 'No pactado', 'Not Agreed'),
('es', 'Codigo', 'Código'),
('en', 'Codigo', 'Code'),
('es', 'Varios', 'Varios'),
('en', 'Varios', 'Varies'),
('es', 'Site Specific', 'Site Specific'),
('en', 'Site Specific', 'Site Specific'),
('es', 'Proyecto', 'Proyecto'),
('en', 'Proyecto', 'Project'),
('es', 'Pintura', 'Pintura'),
('en', 'Pintura', 'Paint'),
('es', 'Performance', 'Performance'),
('en', 'Performance', 'Performance'),
('es', 'Instalación', 'Instalación'),
('en', 'Instalación', 'Installation'),
('es', 'Impresión', 'Impresión'),
('en', 'Impresión', 'Print'),
('es', 'Fotografía', 'Fotografía'),
('en', 'Fotografía', 'Photo'),
('es', 'Escultura', 'Escultura'),
('en', 'Escultura', 'Sculpture'),
('es', 'Dibujo', 'Dibujo'),
('en', 'Dibujo', 'Draw'),
('es', 'Grabado', 'Grabado'),
('en', 'Grabado', 'Grabado'),
('es', 'Yen Japones', 'Yen Japonés'),
('en', 'Yen Japones', 'Japanese Yen'),
('es', 'Rupia India', 'Rupia India'),
('en', 'Rupia India', 'Indian Rupee'),
('es', 'Real Brasileno', 'Real Brasileño'),
('en', 'Real Brasileno', 'Brazilian Real'),
('es', 'Peso Cubano Convertible', 'Peso Cubano Convertible'),
('en', 'Peso Cubano Convertible', 'Cuban Peso Convertible'),
('es', 'Peso Cubano', 'Peso Cubano'),
('en', 'Peso Cubano', 'Cuban Peso'),
('es', 'Peso Colombiano', 'Peso Colombiano'),
('en', 'Peso Colombiano', 'Colombian Peso'),
('es', 'Peso Chileno', 'Peso Chileno'),
('en', 'Peso Chileno', 'Chilean Peso'),
('es', 'Peso Argentino', 'Peso Argentino'),
('en', 'Peso Argentino', 'Argentine Peso'),
('es', 'Peso Peruano', 'Peso Peruano'),
('en', 'Peso Peruano', 'Peruvian Peso'),
('es', 'Peso Mexicano', 'Peso Mexicano'),
('en', 'Peso Mexicano', 'Mexican Peso'),
('es', 'Lira Turca', 'Lira Turca'),
('en', 'Lira Turca', 'Turkish Lira'),
('es', 'Libra Esterlina', 'Libra Esterlina'),
('en', 'Libra Esterlina', 'Pound Sterling'),
('es', 'Franco Suizo', 'Franco Suizo'),
('en', 'Franco Suizo', 'Swiss Franc'),
('es', 'Euro', 'Euro'),
('en', 'Euro', 'Euro'),
('es', 'Dolar USA', 'Dólar USA'),
('en', 'Dolar USA', 'USA Dollar'),
('es', 'Dolar Canadiense', 'Dólar Canadiense'),
('en', 'Dolar Canadiense', 'Canadian Dollar'),
('es', 'Bolivar Venezolano', 'Bolívar Venezolano'),
('en', 'Bolivar Venezolano', 'Venezuelan Bolívar'),
('es', 'Donado', 'Donado'),
('en', 'Donado', 'Donated'),
('es', 'Expuesta', 'Expuesta'),
('en', 'Expuesta', 'Exposed'),
('es', 'Intercambiado', 'Intercambiado'),
('en', 'Intercambiado', 'Interchanged'),
('es', 'Reservado', 'Reservado'),
('en', 'Reservado', 'Reserved'),
('es', 'Vendido', 'Vendido'),
('en', 'Vendido', 'Sold'),
('es', 'Disponible', 'Disponible'),
('en', 'Disponible', 'Available'),
('es', 'Destruida', 'Destruida'),
('en', 'Destruida', 'Destroyed'),
('es', 'Consignada', 'Consignada'),
('en', 'Consignada', 'Consigned'),
('es', 'Archivo', 'Archivo'),
('en', 'Archivo', 'Archived'),
('es', 'Cuenta Bancaria', 'Cuenta Bancaria'),
('en', 'Cuenta Bancaria', 'Bank Account'),
('es', 'Ediciones', 'Ediciones'),
('en', 'Ediciones', 'Editions'),
('es', 'Facturacion', 'Facturación'),
('en', 'Facturacion', 'Billing'),
('es', 'Oferta', 'Oferta'),
('en', 'Oferta', 'Offer'),
('es', 'Series', 'Series'),
('en', 'Series', 'Series'),
('es', 'Datos para transferencias de dinero', 'Datos para transferencias de dinero'),
('en', 'Datos para transferencias de dinero', 'Money Transfer Data'),
('es', 'Monto Inicial', 'Monto Inicial'),
('en', 'Monto Inicial', 'Initial Amount'),
('es', 'Error en la entrada del monto inicial', 'Error en la entrada del monto inicial'),
('en', 'Error en la entrada del monto inicial', 'Error typing Initial Amount'),
('es', 'Ano de realizada', 'Año de realizada'),
('en', 'Ano de realizada', 'Creation Year'),
('es', 'Error en el ano', 'Error en el año'),
('en', 'Error en el ano', 'Error in year'),
('es', 'Ano', 'Año'),
('en', 'Ano', 'Year'),
('es', 'Declaracion', 'Declaración'),
('en', 'Declaracion', 'Statement'),
('es', 'Observaciones', 'Observaciones'),
('en', 'Observaciones', 'Remarks'),
('es', 'Seleccione', 'Seleccione'),
('en', 'Seleccione', 'Select'),
('es', 'Facturado', 'Facturado'),
('en', 'Facturado', 'Billed'),
('es', 'Salir', 'Salir'),
('en', 'Salir', 'Exit'),
('es', 'Inventario', 'Inventario'),
('en', 'Inventario', 'Inventory'),
('es', 'Cantidad en la Serie', 'Cantidad en la Serie'),
('en', 'Cantidad en la Serie', 'Qtty in Serie'),
('es', 'Imagen de la obra', 'Imagen de la obra'),
('en', 'Imagen de la obra', 'Art Work photo'),
('es', 'Ano Realizacion', 'Año Realización'),
('en', 'Ano Realizacion', 'Year of Creation'),
('es', 'Error: La imagen supera el tamano maximo que es de', 'Error: La imagen supera el tamaño máximo que es de '),
('en', 'Error: La imagen supera el tamano maximo que es de', 'Error: Image is greater than ');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');
-- ('es', 'sdfgdfg', 'dfgdsf'),
-- ('en', 'dfsgsdfg', 'sdsdfgf');

-- 2017-09-26 15:30:08
