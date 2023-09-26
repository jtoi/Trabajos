-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: mariadb
-- Tiempo de generación: 04-03-2022 a las 20:07:21
-- Versión del servidor: 10.5.9-MariaDB-1:10.5.9+maria~focal
-- Versión de PHP: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `arte_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`%` PROCEDURE `insertaArtista` (IN `nomb` VARCHAR(200), IN `seud` VARCHAR(100), IN `corr` VARCHAR(150), IN `dire` VARCHAR(200), IN `coor` VARCHAR(20), IN `md` VARCHAR(20), IN `act` INT, IN `idi` INT, IN `idiw` VARCHAR(20), OUT `idArt` INT)  begin
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

end$$

CREATE DEFINER=`root`@`%` PROCEDURE `valida_usuario` (IN `md` VARCHAR(64), IN `ipent` VARCHAR(20), OUT `nombreU` VARCHAR(100), OUT `idusr` INT, OUT `idartista` INT, OUT `seudonimoA` VARCHAR(100), OUT `email` VARCHAR(150), OUT `Ordenrol` INT, OUT `idrol` INT, OUT `idioma` CHAR(2), OUT `imagen` VARCHAR(150))  READS SQL DATA
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
	
end$$

--
-- Funciones
--
CREATE DEFINER=`root`@`%` FUNCTION `calcSha` (`pass` VARCHAR(64), `correo` VARCHAR(150)) RETURNS VARCHAR(64) CHARSET utf8 COLLATE utf8_spanish_ci READS SQL DATA
    DETERMINISTIC
RETURN (sha2(concat(pass,correo,'lo realmente hermoso es invisible a los ojos'),256))$$

CREATE DEFINER=`root`@`%` FUNCTION `fn_entrada` (`md` VARCHAR(64)) RETURNS VARCHAR(200) CHARSET utf8 COLLATE utf8_spanish_ci READS SQL DATA
    DETERMINISTIC
RETURN (SELECT concat (r.orden, '|', a.id, '|', a.email, '|', a.nombre, '|', a.idrol, '|', a.ididioma) FROM tbl_admin a, tbl_roles r WHERE a.idrol = r.id and a.md5 = calcSha(md,a.email) and a.activo = 1)$$

CREATE DEFINER=`root`@`%` FUNCTION `split_string` (`str` VARCHAR(255), `delim` VARCHAR(12), `pos` INT) RETURNS VARCHAR(255) CHARSET utf8 COLLATE utf8_spanish_ci RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(str, delim, pos), LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1), delim, '')$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `idrol` int(11) NOT NULL,
  `ididioma` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `md5` varchar(256) COLLATE utf8_spanish_ci NOT NULL,
  `fechaPass` int(11) NOT NULL DEFAULT 973835640,
  `md5Old` varchar(256) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fechamod` int(11) NOT NULL DEFAULT 0,
  `fecha_visita` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `idtimezone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_admin`
--

INSERT INTO `tbl_admin` VALUES(1, 3, 1, 'Humberto Díaz', 'hdiaz@gmail.com', 1, '7aec08a9939582647c38ba82228f1ae5453f4ccd0b59177246a8ea2375e0695c', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(2, 3, 2, 'Mabel Poblet', 'mpoblet@gmail.com', 1, '176da326c15da8eeb2c70fddc641a4c40bb7ac04d5107cd201a81b005d970817', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(3, 1, 1, 'Julio', 'jtoirac@gmail.com', 1, 'c717284b466f0720d06fd61b2cfe313c2be1d2be4afc0e3dbb2050badd93abd1', 1646332117, 'c717284b466f0720d06fd61b2cfe313c2be1d2be4afc0e3dbb2050badd93abd1', 1646328036, 1646423607, '172.19.0.1', 115);
INSERT INTO `tbl_admin` VALUES(4, 2, 1, 'Taimi Antunez', 'taimioro@nauta.cu', 1, 'c828aa9d022056fede9c4068a9c4019de10d12ce607faa6b996e0772a87dcc29', 973842840, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(5, 2, 1, 'ghjdfg dfghdfg', 'sdfgf@fdgsdf.fgd', 1, 'e6d39d2c59c7c4c875edbf76512621757a99958c6e8e3d40f622b182043f802b', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(6, 2, 2, 'tyuertyvert', 'sdfgf@gerteq.fgd', 1, '329ae16abae162d6899fffd4ac496de4930a4e98f0e90a7390e91579610b65be', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(7, 2, 1, 'drtysrtyrtsh', 'dghnmnmjd@sdfhgsad.fgsa', 1, '13239f5f97c7e0157eba23ca69ebb1a68ad24c9159316d0bf258e5e6e123809a', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(8, 3, 2, 'gnertgaertg', 'zsdfsdfgd@fdghasd.fg', 1, '804473bc3371ce64f602273e3fb7c4f168b50557e8d6830193ac536a075dd35b', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(9, 4, 1, 'rtkrtfykryu', 'ouioykuy@sdfgsd.dfg', 1, 'fb0d8ea56dd27ca565e43a5368f8514e3006db098bc5266c77a3245df0086b92', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(10, 3, 2, 'rfnbernjj s gg', 'rtyjetyery@sdfgasdf.dfx', 1, 'd02e0eda8238ac92403eb9f5ca69f6910882c1fac016d724a35348227b042af4', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(11, 2, 1, 'hetyherthert', 'fghjdgj@sdfgsdf.fg', 1, '0690b88a798a45a29a4967a40802242a3b7a7886d174ed9028dca0ec61aa4a13', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(12, 2, 2, 'dhsfghsfdg', 'dsfghsdfh@srdtgsdfg.fgh', 1, 'dd4fd913366d69f26b8360a04f27a9ae1405bb6f09142c60d5401f5f730c9272', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(13, 2, 1, 'dfghsdfhsdftyku', 'dsfghsdfh@srdtsdfgs.fgh', 1, 'cf4d110cbbcca3d4c3009ce4d3bb045debf74ccd2cec755b464058fd3d27d26d', 973835640, NULL, 1646328036, 0, NULL, 115);
INSERT INTO `tbl_admin` VALUES(14, 4, 2, 'dfghsd sdfgg sdf', 'fhjdfsdfgh@rdtydgx.fgh', 1, 'c3e08c8365433eff0cc04b02988ff91f7a19d3fe0ce4cdc460bb9f36b2da46d7', 973835640, NULL, 1646328036, 0, NULL, 115);

--
-- Disparadores `tbl_admin`
--
DELIMITER $$
CREATE TRIGGER `tr_admin_ai` AFTER INSERT ON `tbl_admin` FOR EACH ROW begin
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

end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_admin_bi` BEFORE INSERT ON `tbl_admin` FOR EACH ROW begin
 set new.fechamod = unix_timestamp();
--	insert into tbl_debug (nombre, valor) values ('login', new.md5);
 insert into tbl_bitacora (idadmin, texto) values (1, concat('Se inserta el usuario ',new.nombre,' con correo ',new.email,' y contraseña ', new.md5));
 set new.md5 = calcSha(new.md5,new.email);
--	insert into tbl_debug (nombre, valor) values ('correo', new.email);
--	insert into tbl_debug (nombre, valor) values ('md5', new.md5);
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_admin_bu` BEFORE UPDATE ON `tbl_admin` FOR EACH ROW begin
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
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_adminIdioma`
--

CREATE TABLE `tbl_adminIdioma` (
  `id` int(11) NOT NULL,
  `idioma` char(2) COLLATE utf8_spanish_ci NOT NULL,
  `frase` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  `texto` varchar(200) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_adminIdioma`
--

INSERT INTO `tbl_adminIdioma` VALUES(1, 'es', 'Datos Personales', 'Datos Personales');
INSERT INTO `tbl_adminIdioma` VALUES(2, 'en', 'Datos Personales', 'Personal Data');
INSERT INTO `tbl_adminIdioma` VALUES(3, 'es', 'Error en la entrada del nombre', 'Error en la entrada del nombre');
INSERT INTO `tbl_adminIdioma` VALUES(4, 'en', 'Error en la entrada del nombre', 'Error typing the name');
INSERT INTO `tbl_adminIdioma` VALUES(5, 'es', 'Error en la entrada del correo', 'Error en la entrada del correo');
INSERT INTO `tbl_adminIdioma` VALUES(6, 'en', 'Error en la entrada del correo', 'Error typing email');
INSERT INTO `tbl_adminIdioma` VALUES(7, 'es', 'Error en la entrada de la contrasena debe poner hasta 12 caracteres que sean digitos o letras', 'Error en la entrada de la contraseña debe poner hasta 12 caracteres que sean digitos o letras');
INSERT INTO `tbl_adminIdioma` VALUES(8, 'en', 'Error en la entrada de la contrasena debe poner hasta 12 caracteres que sean digitos o letras', 'Error typing password, type until 12 characters digits or letters');
INSERT INTO `tbl_adminIdioma` VALUES(9, 'es', 'Error en la entrada de la fecha', 'Error en la entrada de la fecha');
INSERT INTO `tbl_adminIdioma` VALUES(10, 'en', 'Error en la entrada de la fecha', 'Error typing date');
INSERT INTO `tbl_adminIdioma` VALUES(11, 'es', 'Error en la entrada del texto', 'Error en la entrada del texto');
INSERT INTO `tbl_adminIdioma` VALUES(12, 'en', 'Error en la entrada del texto', 'Error typing text');
INSERT INTO `tbl_adminIdioma` VALUES(13, 'es', 'Error en la entrada de la hora', 'Error en la entrada de la hora');
INSERT INTO `tbl_adminIdioma` VALUES(14, 'en', 'Error en la entrada de la hora', 'Error typing time');
INSERT INTO `tbl_adminIdioma` VALUES(15, 'es', 'Error en identificador del usuario', 'Error en identificador del usuario');
INSERT INTO `tbl_adminIdioma` VALUES(16, 'en', 'Error en identificador del usuario', 'Error user identification');
INSERT INTO `tbl_adminIdioma` VALUES(17, 'es', 'Error en la entrada del separador decimal', 'Error en la entrada del separador decimal');
INSERT INTO `tbl_adminIdioma` VALUES(18, 'en', 'Error en la entrada del separador decimal', 'Error typing decimal point');
INSERT INTO `tbl_adminIdioma` VALUES(19, 'es', 'Error en la entrada de la moneda', 'Error en la entrada de la moneda');
INSERT INTO `tbl_adminIdioma` VALUES(20, 'en', 'Error en la entrada de la moneda', 'Erro typing currency');
INSERT INTO `tbl_adminIdioma` VALUES(21, 'es', 'Error en la entrada del separador de miles', 'Error en la entrada del separador de miles');
INSERT INTO `tbl_adminIdioma` VALUES(22, 'en', 'Error en la entrada del separador de miles', 'Erro typing thousands separator');
INSERT INTO `tbl_adminIdioma` VALUES(23, 'es', 'Error: Se produjo un error al salvar los datos llame al desarrollador', 'Error: Se produjo un error al salvar los datos llame al desarrollador');
INSERT INTO `tbl_adminIdioma` VALUES(24, 'en', 'Error: Se produjo un error al salvar los datos llame al desarrollador', 'Error: there is an error please call to developer');
INSERT INTO `tbl_adminIdioma` VALUES(25, 'es', 'Datos salvados correctamente.', 'Datos salvados correctamente.');
INSERT INTO `tbl_adminIdioma` VALUES(26, 'en', 'Datos salvados correctamente.', 'Data saved');
INSERT INTO `tbl_adminIdioma` VALUES(27, 'es', 'Error: No es un formato de fecha valido', 'Error: No es un formato de fecha valido');
INSERT INTO `tbl_adminIdioma` VALUES(28, 'en', 'Error: No es un formato de fecha valido', 'Error: Invalid date format');
INSERT INTO `tbl_adminIdioma` VALUES(29, 'es', 'Nombre', 'Nombre');
INSERT INTO `tbl_adminIdioma` VALUES(30, 'en', 'Nombre', 'Name');
INSERT INTO `tbl_adminIdioma` VALUES(31, 'es', 'Contrasena', 'Contraseña');
INSERT INTO `tbl_adminIdioma` VALUES(32, 'en', 'Contrasena', 'Password');
INSERT INTO `tbl_adminIdioma` VALUES(33, 'es', 'Vuelva a escribirla', 'Vuelva a escribirla');
INSERT INTO `tbl_adminIdioma` VALUES(34, 'en', 'Vuelva a escribirla', 'Write it again');
INSERT INTO `tbl_adminIdioma` VALUES(35, 'es', 'Formato de fechas', 'Formato de fechas');
INSERT INTO `tbl_adminIdioma` VALUES(36, 'en', 'Formato de fechas', 'Date format');
INSERT INTO `tbl_adminIdioma` VALUES(37, 'es', 'Formato de Horas', 'Formato de Horas');
INSERT INTO `tbl_adminIdioma` VALUES(38, 'en', 'Formato de Horas', 'Time format');
INSERT INTO `tbl_adminIdioma` VALUES(39, 'es', 'Punto (.)', 'Punto (.)');
INSERT INTO `tbl_adminIdioma` VALUES(40, 'en', 'Punto (.)', 'Dot (.)');
INSERT INTO `tbl_adminIdioma` VALUES(41, 'es', 'Coma (,)', 'Coma (,)');
INSERT INTO `tbl_adminIdioma` VALUES(42, 'en', 'Coma (,)', 'Comma (,)');
INSERT INTO `tbl_adminIdioma` VALUES(43, 'es', 'Separador de decimales', 'Separador de decimales');
INSERT INTO `tbl_adminIdioma` VALUES(44, 'en', 'Separador de decimales', 'Separator for decimal point');
INSERT INTO `tbl_adminIdioma` VALUES(45, 'es', 'Sin separador', 'Sin separador');
INSERT INTO `tbl_adminIdioma` VALUES(46, 'en', 'Sin separador', 'Without separator');
INSERT INTO `tbl_adminIdioma` VALUES(47, 'es', 'Espacio ( )', 'Espacio ( )');
INSERT INTO `tbl_adminIdioma` VALUES(48, 'en', 'Espacio ( )', 'Blank space ( )');
INSERT INTO `tbl_adminIdioma` VALUES(49, 'es', 'Separador de miles', 'Separador de miles');
INSERT INTO `tbl_adminIdioma` VALUES(50, 'en', 'Separador de miles', 'Thousands separator');
INSERT INTO `tbl_adminIdioma` VALUES(51, 'es', 'Obra', 'Obra');
INSERT INTO `tbl_adminIdioma` VALUES(52, 'en', 'Obra', 'Art Work');
INSERT INTO `tbl_adminIdioma` VALUES(53, 'es', 'curriculo', 'currículo');
INSERT INTO `tbl_adminIdioma` VALUES(54, 'en', 'curriculo', 'curriculum');
INSERT INTO `tbl_adminIdioma` VALUES(55, 'es', 'cliente', 'cliente');
INSERT INTO `tbl_adminIdioma` VALUES(56, 'en', 'cliente', 'client');
INSERT INTO `tbl_adminIdioma` VALUES(57, 'es', 'contacto', 'contacto');
INSERT INTO `tbl_adminIdioma` VALUES(58, 'en', 'contacto', 'contac');
INSERT INTO `tbl_adminIdioma` VALUES(59, 'es', 'admin', 'admin');
INSERT INTO `tbl_adminIdioma` VALUES(60, 'en', 'admin', 'admin');
INSERT INTO `tbl_adminIdioma` VALUES(61, 'es', 'Usuarios', 'Usuarios');
INSERT INTO `tbl_adminIdioma` VALUES(62, 'en', 'Usuarios', 'Users');
INSERT INTO `tbl_adminIdioma` VALUES(63, 'es', 'Artistas', 'Artistas');
INSERT INTO `tbl_adminIdioma` VALUES(64, 'en', 'Artistas', 'Artists');
INSERT INTO `tbl_adminIdioma` VALUES(65, 'es', 'Artista', 'Artista');
INSERT INTO `tbl_adminIdioma` VALUES(66, 'en', 'Artista', 'Artist');
INSERT INTO `tbl_adminIdioma` VALUES(67, 'es', 'Estado Obra', 'Estado Obra');
INSERT INTO `tbl_adminIdioma` VALUES(68, 'en', 'Estado Obra', 'Art Work Status');
INSERT INTO `tbl_adminIdioma` VALUES(69, 'es', 'Estado Pago', 'Estado Pago');
INSERT INTO `tbl_adminIdioma` VALUES(70, 'en', 'Estado Pago', 'Payment Status');
INSERT INTO `tbl_adminIdioma` VALUES(71, 'es', 'Medio', 'Medio');
INSERT INTO `tbl_adminIdioma` VALUES(72, 'en', 'Medio', 'Media');
INSERT INTO `tbl_adminIdioma` VALUES(73, 'es', 'Moneda', 'Moneda');
INSERT INTO `tbl_adminIdioma` VALUES(74, 'en', 'Moneda', 'Currency');
INSERT INTO `tbl_adminIdioma` VALUES(75, 'es', 'Roles', 'Roles');
INSERT INTO `tbl_adminIdioma` VALUES(76, 'en', 'Roles', 'Rols');
INSERT INTO `tbl_adminIdioma` VALUES(77, 'es', 'Configuracion', 'Configuración');
INSERT INTO `tbl_adminIdioma` VALUES(78, 'en', 'Configuracion', 'Configuration');
INSERT INTO `tbl_adminIdioma` VALUES(79, 'es', 'Bienvenido al sitio', 'Bienvenido a ArteOrganizer');
INSERT INTO `tbl_adminIdioma` VALUES(80, 'en', 'Bienvenido al sitio', 'Welcome to ArteOrganizer');
INSERT INTO `tbl_adminIdioma` VALUES(81, 'es', 'Error en la entrada del artista', 'Error en la entrada del artista');
INSERT INTO `tbl_adminIdioma` VALUES(82, 'en', 'Error en la entrada del artista', 'Error typing Artist');
INSERT INTO `tbl_adminIdioma` VALUES(83, 'es', 'Error en la entrada del rol', 'Error en la entrada del rol');
INSERT INTO `tbl_adminIdioma` VALUES(84, 'en', 'Error en la entrada del rol', 'Error typing rol');
INSERT INTO `tbl_adminIdioma` VALUES(85, 'es', 'Error en la accion', 'Error en la acción');
INSERT INTO `tbl_adminIdioma` VALUES(86, 'en', 'Error en la accion', 'Error in action');
INSERT INTO `tbl_adminIdioma` VALUES(87, 'es', 'Error cambiando activo', 'Error cambiando activo');
INSERT INTO `tbl_adminIdioma` VALUES(88, 'en', 'Error cambiando activo', 'Error changing active');
INSERT INTO `tbl_adminIdioma` VALUES(89, 'es', 'Si', 'Si');
INSERT INTO `tbl_adminIdioma` VALUES(90, 'en', 'Si', 'Yes');
INSERT INTO `tbl_adminIdioma` VALUES(91, 'es', 'No', 'No');
INSERT INTO `tbl_adminIdioma` VALUES(92, 'en', 'No', 'No');
INSERT INTO `tbl_adminIdioma` VALUES(93, 'es', 'Datos correctamente guardados', 'Datos correctamente guardados');
INSERT INTO `tbl_adminIdioma` VALUES(94, 'en', 'Datos correctamente guardados', 'Data saved');
INSERT INTO `tbl_adminIdioma` VALUES(95, 'es', 'Activo', 'Activo');
INSERT INTO `tbl_adminIdioma` VALUES(96, 'en', 'Activo', 'Active');
INSERT INTO `tbl_adminIdioma` VALUES(97, 'es', 'Todo', 'Todo');
INSERT INTO `tbl_adminIdioma` VALUES(98, 'en', 'Todo', 'All');
INSERT INTO `tbl_adminIdioma` VALUES(99, 'es', 'Error: Esta direccion de correo ya existe en la base de datos', 'Error: Esta dirección de correo ya existe en la base de datos');
INSERT INTO `tbl_adminIdioma` VALUES(100, 'en', 'Error: Esta direccion de correo ya existe en la base de datos', 'Error: This email is already in Data Base');
INSERT INTO `tbl_adminIdioma` VALUES(101, 'es', 'Editar', 'Editar');
INSERT INTO `tbl_adminIdioma` VALUES(102, 'en', 'Editar', 'Edit');
INSERT INTO `tbl_adminIdioma` VALUES(103, 'es', 'Desactivar', 'Desactivar');
INSERT INTO `tbl_adminIdioma` VALUES(104, 'en', 'Desactivar', 'Desactive');
INSERT INTO `tbl_adminIdioma` VALUES(105, 'es', 'Usuario', 'Usuario');
INSERT INTO `tbl_adminIdioma` VALUES(106, 'en', 'Usuario', 'User');
INSERT INTO `tbl_adminIdioma` VALUES(107, 'es', 'Correo', 'Correo');
INSERT INTO `tbl_adminIdioma` VALUES(108, 'en', 'Correo', 'Email');
INSERT INTO `tbl_adminIdioma` VALUES(109, 'es', 'Rol', 'Rol');
INSERT INTO `tbl_adminIdioma` VALUES(110, 'en', 'Rol', 'Rol');
INSERT INTO `tbl_adminIdioma` VALUES(111, 'es', 'Fecha Mod', 'Fecha Mod');
INSERT INTO `tbl_adminIdioma` VALUES(112, 'en', 'Fecha Mod', 'Mod. Date');
INSERT INTO `tbl_adminIdioma` VALUES(113, 'es', 'Fecha Visita', 'Fecha Visita');
INSERT INTO `tbl_adminIdioma` VALUES(114, 'en', 'Fecha Visita', 'Visit Date');
INSERT INTO `tbl_adminIdioma` VALUES(115, 'es', 'Idiomas de trabajo', 'Idiomas de trabajo');
INSERT INTO `tbl_adminIdioma` VALUES(116, 'en', 'Idiomas de trabajo', 'Work Languages');
INSERT INTO `tbl_adminIdioma` VALUES(117, 'es', 'Idioma personal', 'Idioma personal');
INSERT INTO `tbl_adminIdioma` VALUES(118, 'en', 'Idioma personal', 'Personal Language');
INSERT INTO `tbl_adminIdioma` VALUES(119, 'es', 'Error en la entrada del idioma', 'Error en la entrada del idioma');
INSERT INTO `tbl_adminIdioma` VALUES(120, 'en', 'Error en la entrada del idioma', 'Error selecting language');
INSERT INTO `tbl_adminIdioma` VALUES(121, 'es', 'Error en la entrada del idioma de trabajo', 'Error en la entrada del idioma de trabajo');
INSERT INTO `tbl_adminIdioma` VALUES(122, 'en', 'Error en la entrada del idioma de trabajo', 'Error selecting work language');
INSERT INTO `tbl_adminIdioma` VALUES(123, 'es', 'Insertar', 'Insertar');
INSERT INTO `tbl_adminIdioma` VALUES(124, 'en', 'Insertar', 'Insert');
INSERT INTO `tbl_adminIdioma` VALUES(125, 'es', 'Buscar', 'Buscar');
INSERT INTO `tbl_adminIdioma` VALUES(126, 'en', 'Buscar', 'Search');
INSERT INTO `tbl_adminIdioma` VALUES(127, 'es', 'Previo', 'Previo');
INSERT INTO `tbl_adminIdioma` VALUES(128, 'en', 'Previo', 'Back');
INSERT INTO `tbl_adminIdioma` VALUES(129, 'es', 'Proximo', 'Próximo');
INSERT INTO `tbl_adminIdioma` VALUES(130, 'en', 'Proximo', 'Next');
INSERT INTO `tbl_adminIdioma` VALUES(131, 'es', 'Error: El cuerpo del correo no puede estar vacio', 'Error: El cuerpo del correo no puede estar vacío');
INSERT INTO `tbl_adminIdioma` VALUES(132, 'en', 'Error: El cuerpo del correo no puede estar vacio', 'Error: Message body cant left blank');
INSERT INTO `tbl_adminIdioma` VALUES(133, 'es', 'Inscripcion en el sitio ArteOrganizer', 'Inscripción en el sitio ArteOrganizer');
INSERT INTO `tbl_adminIdioma` VALUES(134, 'en', 'Inscripcion en el sitio ArteOrganizer', 'ArteOrganizer Inscription');
INSERT INTO `tbl_adminIdioma` VALUES(135, 'es', 'Generar contrasena', 'Generar contraseña');
INSERT INTO `tbl_adminIdioma` VALUES(136, 'en', 'Generar contrasena', 'Regenerate Password');
INSERT INTO `tbl_adminIdioma` VALUES(137, 'es', 'Error cambiando contrasena', 'Error cambiando contraseña');
INSERT INTO `tbl_adminIdioma` VALUES(138, 'en', 'Error cambiando contrasena', 'Error changing password');
INSERT INTO `tbl_adminIdioma` VALUES(139, 'es', 'nohtml', 'Para ver el mensaje use un lector de correos compatible HTML!');
INSERT INTO `tbl_adminIdioma` VALUES(140, 'en', 'nohtml', 'To view the message, please use an HTML compatible email viewer!');
INSERT INTO `tbl_adminIdioma` VALUES(141, 'es', 'correo bienvenida al sitio', 'Usted ha sido inscrito en ArteOrganizer, la aplicación que mejorará tu desempeño organizando tu trabajo diario y haciéndote más productivo.');
INSERT INTO `tbl_adminIdioma` VALUES(142, 'en', 'correo bienvenida al sitio', 'You have been registered to ArteOrganizer, the application that will improve your efforts organizing your daily work and making you more productive.');
INSERT INTO `tbl_adminIdioma` VALUES(143, 'es', 'correo renovacion contrasena', 'A usted se le ha renovado las credeciales para la entrada a la web de ArteOrganizer.');
INSERT INTO `tbl_adminIdioma` VALUES(144, 'en', 'correo renovacion contrasena', 'Your credentials for entering at ArteOrganizer application has been renewed.');
INSERT INTO `tbl_adminIdioma` VALUES(145, 'es', 'Renovacion de credenciales', 'Renovación de credenciales');
INSERT INTO `tbl_adminIdioma` VALUES(146, 'en', 'Renovacion de credenciales', 'Credentials renewal');
INSERT INTO `tbl_adminIdioma` VALUES(147, 'es', 'Seudonimo', 'Seudónimo');
INSERT INTO `tbl_adminIdioma` VALUES(148, 'en', 'Seudonimo', 'Pseudonym');
INSERT INTO `tbl_adminIdioma` VALUES(149, 'es', 'Direccion', 'Dirección');
INSERT INTO `tbl_adminIdioma` VALUES(150, 'en', 'Direccion', 'Address');
INSERT INTO `tbl_adminIdioma` VALUES(151, 'es', 'Coordenadas', 'Coordenadas Geográficas del Estudio');
INSERT INTO `tbl_adminIdioma` VALUES(152, 'en', 'Coordenadas', 'Studios Geographical Coordinates');
INSERT INTO `tbl_adminIdioma` VALUES(153, 'es', 'Error en la entrada de la direccion', 'Error en la entrada de la dirección');
INSERT INTO `tbl_adminIdioma` VALUES(154, 'en', 'Error en la entrada de la direccion', 'Error in address');
INSERT INTO `tbl_adminIdioma` VALUES(155, 'es', 'Error en la entrada de las coordenadas', 'Error en la entrada de las coordenadas');
INSERT INTO `tbl_adminIdioma` VALUES(156, 'en', 'Error en la entrada de las coordenadas', 'Error in Geographical Coordinates');
INSERT INTO `tbl_adminIdioma` VALUES(157, 'es', 'Imagen del artista', 'Imagen del artista');
INSERT INTO `tbl_adminIdioma` VALUES(158, 'en', 'Imagen del artista', 'Artists photo');
INSERT INTO `tbl_adminIdioma` VALUES(159, 'es', 'Tipo de archivo no valido', 'Error: Tipo de archivo no válido');
INSERT INTO `tbl_adminIdioma` VALUES(160, 'en', 'Tipo de archivo no valido', 'Error: Not a valid file');
INSERT INTO `tbl_adminIdioma` VALUES(161, 'es', 'La imagen excede el tamano maximo soportado', 'Error: La imagen excede el tamaño máximo soportado');
INSERT INTO `tbl_adminIdioma` VALUES(162, 'en', 'La imagen excede el tamano maximo soportado', 'Error: Image exceeds the maximum supported size');
INSERT INTO `tbl_adminIdioma` VALUES(163, 'es', 'La imagen no se subio correctamente', 'Error: La imagen no se subió correctamente');
INSERT INTO `tbl_adminIdioma` VALUES(164, 'en', 'La imagen no se subió correctamente', 'Error: The image did not upload correctly');
INSERT INTO `tbl_adminIdioma` VALUES(165, 'es', 'Se debe seleccionar un archivo', 'Error: Se debe seleccionar un archivo');
INSERT INTO `tbl_adminIdioma` VALUES(166, 'en', 'Se debe seleccionar un archivo', 'Error: A file must be selected');
INSERT INTO `tbl_adminIdioma` VALUES(167, 'es', 'La imagen es muy pesada', 'Error: La imagen es muy pesada');
INSERT INTO `tbl_adminIdioma` VALUES(168, 'en', 'La imagen es muy pesada', 'Error: Image too heavy');
INSERT INTO `tbl_adminIdioma` VALUES(169, 'es', 'La imagen dio error', '. La imagen dió error - ');
INSERT INTO `tbl_adminIdioma` VALUES(170, 'en', 'La imagen dio error', '. There is an error with image - ');
INSERT INTO `tbl_adminIdioma` VALUES(171, 'es', 'Puede intentar subirla nuevamente modificando el artista', '. . Puede intentar subirla nuevamente modificando el artista');
INSERT INTO `tbl_adminIdioma` VALUES(172, 'en', 'Puede intentar subirla nuevamente modificando el artista', '. You can try uploading it again by editing artists data');
INSERT INTO `tbl_adminIdioma` VALUES(173, 'es', 'Estado de la Obra', 'Estado de la Obra');
INSERT INTO `tbl_adminIdioma` VALUES(174, 'en', 'Estado de la Obra', 'Art Work Status');
INSERT INTO `tbl_adminIdioma` VALUES(175, 'es', 'Estado', 'Estado');
INSERT INTO `tbl_adminIdioma` VALUES(176, 'en', 'Estado', 'Status');
INSERT INTO `tbl_adminIdioma` VALUES(177, 'es', 'Estado del Pago', 'Estado del Pago');
INSERT INTO `tbl_adminIdioma` VALUES(178, 'en', 'Estado del Pago', 'Payment Status');
INSERT INTO `tbl_adminIdioma` VALUES(179, 'es', 'Medio de expresion', 'Medio de expresión');
INSERT INTO `tbl_adminIdioma` VALUES(180, 'en', 'Medio de expresion', 'Expression Way');
INSERT INTO `tbl_adminIdioma` VALUES(181, 'es', 'Error en identificador del medio', 'Error en identificador del medio');
INSERT INTO `tbl_adminIdioma` VALUES(182, 'en', 'Error en identificador del medio', 'Error in expression way identificator');
INSERT INTO `tbl_adminIdioma` VALUES(183, 'es', 'Enviar', 'Enviar');
INSERT INTO `tbl_adminIdioma` VALUES(184, 'en', 'Enviar', 'Send');
INSERT INTO `tbl_adminIdioma` VALUES(185, 'es', 'Cancelar', 'Cancelar');
INSERT INTO `tbl_adminIdioma` VALUES(186, 'en', 'Cancelar', 'Reset');
INSERT INTO `tbl_adminIdioma` VALUES(187, 'es', 'Pago Parcial', 'Pago Parcial');
INSERT INTO `tbl_adminIdioma` VALUES(188, 'en', 'Pago Parcial', 'Partial Payment');
INSERT INTO `tbl_adminIdioma` VALUES(189, 'es', 'Pagado', 'Pagado');
INSERT INTO `tbl_adminIdioma` VALUES(190, 'en', 'Pagado', 'Payed');
INSERT INTO `tbl_adminIdioma` VALUES(191, 'es', 'Pendiente', 'Pendiente');
INSERT INTO `tbl_adminIdioma` VALUES(192, 'en', 'Pendiente', 'Pending');
INSERT INTO `tbl_adminIdioma` VALUES(193, 'es', 'No pactado', 'No pactado');
INSERT INTO `tbl_adminIdioma` VALUES(194, 'en', 'No pactado', 'Not Agreed');
INSERT INTO `tbl_adminIdioma` VALUES(195, 'es', 'Codigo', 'Código');
INSERT INTO `tbl_adminIdioma` VALUES(196, 'en', 'Codigo', 'Code');
INSERT INTO `tbl_adminIdioma` VALUES(197, 'es', 'Varios', 'Varios');
INSERT INTO `tbl_adminIdioma` VALUES(198, 'en', 'Varios', 'Varies');
INSERT INTO `tbl_adminIdioma` VALUES(199, 'es', 'Site Specific', 'Site Specific');
INSERT INTO `tbl_adminIdioma` VALUES(200, 'en', 'Site Specific', 'Site Specific');
INSERT INTO `tbl_adminIdioma` VALUES(201, 'es', 'Proyecto', 'Proyecto');
INSERT INTO `tbl_adminIdioma` VALUES(202, 'en', 'Proyecto', 'Project');
INSERT INTO `tbl_adminIdioma` VALUES(203, 'es', 'Pintura', 'Pintura');
INSERT INTO `tbl_adminIdioma` VALUES(204, 'en', 'Pintura', 'Paint');
INSERT INTO `tbl_adminIdioma` VALUES(205, 'es', 'Performance', 'Performance');
INSERT INTO `tbl_adminIdioma` VALUES(206, 'en', 'Performance', 'Performance');
INSERT INTO `tbl_adminIdioma` VALUES(207, 'es', 'Instalación', 'Instalación');
INSERT INTO `tbl_adminIdioma` VALUES(208, 'en', 'Instalación', 'Installation');
INSERT INTO `tbl_adminIdioma` VALUES(209, 'es', 'Impresión', 'Impresión');
INSERT INTO `tbl_adminIdioma` VALUES(210, 'en', 'Impresión', 'Print');
INSERT INTO `tbl_adminIdioma` VALUES(211, 'es', 'Fotografía', 'Fotografía');
INSERT INTO `tbl_adminIdioma` VALUES(212, 'en', 'Fotografía', 'Photo');
INSERT INTO `tbl_adminIdioma` VALUES(213, 'es', 'Escultura', 'Escultura');
INSERT INTO `tbl_adminIdioma` VALUES(214, 'en', 'Escultura', 'Sculpture');
INSERT INTO `tbl_adminIdioma` VALUES(215, 'es', 'Dibujo', 'Dibujo');
INSERT INTO `tbl_adminIdioma` VALUES(216, 'en', 'Dibujo', 'Draw');
INSERT INTO `tbl_adminIdioma` VALUES(217, 'es', 'Grabado', 'Grabado');
INSERT INTO `tbl_adminIdioma` VALUES(218, 'en', 'Grabado', 'Grabado');
INSERT INTO `tbl_adminIdioma` VALUES(219, 'es', 'Yen Japones', 'Yen Japonés');
INSERT INTO `tbl_adminIdioma` VALUES(220, 'en', 'Yen Japones', 'Japanese Yen');
INSERT INTO `tbl_adminIdioma` VALUES(221, 'es', 'Rupia India', 'Rupia India');
INSERT INTO `tbl_adminIdioma` VALUES(222, 'en', 'Rupia India', 'Indian Rupee');
INSERT INTO `tbl_adminIdioma` VALUES(223, 'es', 'Real Brasileno', 'Real Brasileño');
INSERT INTO `tbl_adminIdioma` VALUES(224, 'en', 'Real Brasileno', 'Brazilian Real');
INSERT INTO `tbl_adminIdioma` VALUES(225, 'es', 'Peso Cubano Convertible', 'Peso Cubano Convertible');
INSERT INTO `tbl_adminIdioma` VALUES(226, 'en', 'Peso Cubano Convertible', 'Cuban Peso Convertible');
INSERT INTO `tbl_adminIdioma` VALUES(227, 'es', 'Peso Cubano', 'Peso Cubano');
INSERT INTO `tbl_adminIdioma` VALUES(228, 'en', 'Peso Cubano', 'Cuban Peso');
INSERT INTO `tbl_adminIdioma` VALUES(229, 'es', 'Peso Colombiano', 'Peso Colombiano');
INSERT INTO `tbl_adminIdioma` VALUES(230, 'en', 'Peso Colombiano', 'Colombian Peso');
INSERT INTO `tbl_adminIdioma` VALUES(231, 'es', 'Peso Chileno', 'Peso Chileno');
INSERT INTO `tbl_adminIdioma` VALUES(232, 'en', 'Peso Chileno', 'Chilean Peso');
INSERT INTO `tbl_adminIdioma` VALUES(233, 'es', 'Peso Argentino', 'Peso Argentino');
INSERT INTO `tbl_adminIdioma` VALUES(234, 'en', 'Peso Argentino', 'Argentine Peso');
INSERT INTO `tbl_adminIdioma` VALUES(235, 'es', 'Peso Peruano', 'Peso Peruano');
INSERT INTO `tbl_adminIdioma` VALUES(236, 'en', 'Peso Peruano', 'Peruvian Peso');
INSERT INTO `tbl_adminIdioma` VALUES(237, 'es', 'Peso Mexicano', 'Peso Mexicano');
INSERT INTO `tbl_adminIdioma` VALUES(238, 'en', 'Peso Mexicano', 'Mexican Peso');
INSERT INTO `tbl_adminIdioma` VALUES(239, 'es', 'Lira Turca', 'Lira Turca');
INSERT INTO `tbl_adminIdioma` VALUES(240, 'en', 'Lira Turca', 'Turkish Lira');
INSERT INTO `tbl_adminIdioma` VALUES(241, 'es', 'Libra Esterlina', 'Libra Esterlina');
INSERT INTO `tbl_adminIdioma` VALUES(242, 'en', 'Libra Esterlina', 'Pound Sterling');
INSERT INTO `tbl_adminIdioma` VALUES(243, 'es', 'Franco Suizo', 'Franco Suizo');
INSERT INTO `tbl_adminIdioma` VALUES(244, 'en', 'Franco Suizo', 'Swiss Franc');
INSERT INTO `tbl_adminIdioma` VALUES(245, 'es', 'Euro', 'Euro');
INSERT INTO `tbl_adminIdioma` VALUES(246, 'en', 'Euro', 'Euro');
INSERT INTO `tbl_adminIdioma` VALUES(247, 'es', 'Dolar USA', 'Dólar USA');
INSERT INTO `tbl_adminIdioma` VALUES(248, 'en', 'Dolar USA', 'USA Dollar');
INSERT INTO `tbl_adminIdioma` VALUES(249, 'es', 'Dolar Canadiense', 'Dólar Canadiense');
INSERT INTO `tbl_adminIdioma` VALUES(250, 'en', 'Dolar Canadiense', 'Canadian Dollar');
INSERT INTO `tbl_adminIdioma` VALUES(251, 'es', 'Bolivar Venezolano', 'Bolívar Venezolano');
INSERT INTO `tbl_adminIdioma` VALUES(252, 'en', 'Bolivar Venezolano', 'Venezuelan Bolívar');
INSERT INTO `tbl_adminIdioma` VALUES(253, 'es', 'Donado', 'Donado');
INSERT INTO `tbl_adminIdioma` VALUES(254, 'en', 'Donado', 'Donated');
INSERT INTO `tbl_adminIdioma` VALUES(255, 'es', 'Expuesta', 'Expuesta');
INSERT INTO `tbl_adminIdioma` VALUES(256, 'en', 'Expuesta', 'Exposed');
INSERT INTO `tbl_adminIdioma` VALUES(257, 'es', 'Intercambiado', 'Intercambiado');
INSERT INTO `tbl_adminIdioma` VALUES(258, 'en', 'Intercambiado', 'Interchanged');
INSERT INTO `tbl_adminIdioma` VALUES(259, 'es', 'Reservado', 'Reservado');
INSERT INTO `tbl_adminIdioma` VALUES(260, 'en', 'Reservado', 'Reserved');
INSERT INTO `tbl_adminIdioma` VALUES(261, 'es', 'Vendido', 'Vendido');
INSERT INTO `tbl_adminIdioma` VALUES(262, 'en', 'Vendido', 'Sold');
INSERT INTO `tbl_adminIdioma` VALUES(263, 'es', 'Disponible', 'Disponible');
INSERT INTO `tbl_adminIdioma` VALUES(264, 'en', 'Disponible', 'Available');
INSERT INTO `tbl_adminIdioma` VALUES(265, 'es', 'Destruida', 'Destruida');
INSERT INTO `tbl_adminIdioma` VALUES(266, 'en', 'Destruida', 'Destroyed');
INSERT INTO `tbl_adminIdioma` VALUES(267, 'es', 'Consignada', 'Consignada');
INSERT INTO `tbl_adminIdioma` VALUES(268, 'en', 'Consignada', 'Consigned');
INSERT INTO `tbl_adminIdioma` VALUES(269, 'es', 'Archivo', 'Archivo');
INSERT INTO `tbl_adminIdioma` VALUES(270, 'en', 'Archivo', 'Archived');
INSERT INTO `tbl_adminIdioma` VALUES(271, 'es', 'Cuenta Bancaria', 'Cuenta Bancaria');
INSERT INTO `tbl_adminIdioma` VALUES(272, 'en', 'Cuenta Bancaria', 'Bank Account');
INSERT INTO `tbl_adminIdioma` VALUES(273, 'es', 'Ediciones', 'Ediciones');
INSERT INTO `tbl_adminIdioma` VALUES(274, 'en', 'Ediciones', 'Editions');
INSERT INTO `tbl_adminIdioma` VALUES(275, 'es', 'Facturacion', 'Facturación');
INSERT INTO `tbl_adminIdioma` VALUES(276, 'en', 'Facturacion', 'Billing');
INSERT INTO `tbl_adminIdioma` VALUES(277, 'es', 'Oferta', 'Oferta');
INSERT INTO `tbl_adminIdioma` VALUES(278, 'en', 'Oferta', 'Offer');
INSERT INTO `tbl_adminIdioma` VALUES(279, 'es', 'Series', 'Series');
INSERT INTO `tbl_adminIdioma` VALUES(280, 'en', 'Series', 'Series');
INSERT INTO `tbl_adminIdioma` VALUES(281, 'es', 'Datos para transferencias de dinero', 'Datos para transferencias de dinero');
INSERT INTO `tbl_adminIdioma` VALUES(282, 'en', 'Datos para transferencias de dinero', 'Money Transfer Data');
INSERT INTO `tbl_adminIdioma` VALUES(283, 'es', 'Monto Inicial', 'Monto Inicial');
INSERT INTO `tbl_adminIdioma` VALUES(284, 'en', 'Monto Inicial', 'Initial Amount');
INSERT INTO `tbl_adminIdioma` VALUES(285, 'es', 'Error en la entrada del monto inicial', 'Error en la entrada del monto inicial');
INSERT INTO `tbl_adminIdioma` VALUES(286, 'en', 'Error en la entrada del monto inicial', 'Error typing Initial Amount');
INSERT INTO `tbl_adminIdioma` VALUES(287, 'es', 'Ano de realizada', 'Año de realizada');
INSERT INTO `tbl_adminIdioma` VALUES(288, 'en', 'Ano de realizada', 'Creation Year');
INSERT INTO `tbl_adminIdioma` VALUES(289, 'es', 'Error en el ano', 'Error en el año');
INSERT INTO `tbl_adminIdioma` VALUES(290, 'en', 'Error en el ano', 'Error in year');
INSERT INTO `tbl_adminIdioma` VALUES(291, 'es', 'Ano', 'Año');
INSERT INTO `tbl_adminIdioma` VALUES(292, 'en', 'Ano', 'Year');
INSERT INTO `tbl_adminIdioma` VALUES(293, 'es', 'Declaracion', 'Declaración');
INSERT INTO `tbl_adminIdioma` VALUES(294, 'en', 'Declaracion', 'Statement');
INSERT INTO `tbl_adminIdioma` VALUES(295, 'es', 'Observaciones', 'Observaciones');
INSERT INTO `tbl_adminIdioma` VALUES(296, 'en', 'Observaciones', 'Remarks');
INSERT INTO `tbl_adminIdioma` VALUES(297, 'es', 'Seleccione', 'Seleccione');
INSERT INTO `tbl_adminIdioma` VALUES(298, 'en', 'Seleccione', 'Select');
INSERT INTO `tbl_adminIdioma` VALUES(299, 'es', 'Facturado', 'Facturado');
INSERT INTO `tbl_adminIdioma` VALUES(300, 'en', 'Facturado', 'Billed');
INSERT INTO `tbl_adminIdioma` VALUES(301, 'es', 'Salir', 'Salir');
INSERT INTO `tbl_adminIdioma` VALUES(302, 'en', 'Salir', 'Exit');
INSERT INTO `tbl_adminIdioma` VALUES(303, 'es', 'Inventario', 'Inventario');
INSERT INTO `tbl_adminIdioma` VALUES(304, 'en', 'Inventario', 'Inventory');
INSERT INTO `tbl_adminIdioma` VALUES(305, 'es', 'Cantidad en la Serie', 'Cantidad en la Serie');
INSERT INTO `tbl_adminIdioma` VALUES(306, 'en', 'Cantidad en la Serie', 'Qtty in Serie');
INSERT INTO `tbl_adminIdioma` VALUES(307, 'es', 'Imagen de la obra', 'Imagen de la obra');
INSERT INTO `tbl_adminIdioma` VALUES(308, 'en', 'Imagen de la obra', 'Art Work photo');
INSERT INTO `tbl_adminIdioma` VALUES(309, 'es', 'Ano Realizacion', 'Año Realización');
INSERT INTO `tbl_adminIdioma` VALUES(310, 'en', 'Ano Realizacion', 'Year of Creation');
INSERT INTO `tbl_adminIdioma` VALUES(311, 'es', 'Error: La imagen supera el tamano maximo que es de', 'Error: La imagen supera el tamaño máximo que es de ');
INSERT INTO `tbl_adminIdioma` VALUES(312, 'en', 'Error: La imagen supera el tamano maximo que es de', 'Error: Image is greater than ');
INSERT INTO `tbl_adminIdioma` VALUES(313, 'es', 'UsoHorario', 'Uso Horario');
INSERT INTO `tbl_adminIdioma` VALUES(314, 'en', 'UsoHorario', 'Timezone');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_artista`
--

CREATE TABLE `tbl_artista` (
  `id` int(11) NOT NULL,
  `idimg` int(11) DEFAULT NULL,
  `nombre` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `seudonimo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `correo` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  `direccion` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `coordenadas` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'coordenadas del estudio para usar en mapa',
  `activo` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-activo, 0-desactivado',
  `fechamod` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_artista`
--

INSERT INTO `tbl_artista` VALUES(1, 2, 'Humberto Díaz', 'Humbe', 'hdiaz@gmail.com', '18 #564 e/ 17 y 15 Vedado, La Habana, Cuba', '23.128221,-82.405088', 1, 1646328036);
INSERT INTO `tbl_artista` VALUES(2, 1, 'Mabel Poblet', 'Mpoblet', 'mpoblet@gmail.com', '11 #564 e/J e I, Vedado, La Habana', '23.143112,-82.390208', 1, 1646328036);

--
-- Disparadores `tbl_artista`
--
DELIMITER $$
CREATE TRIGGER `tr_artista_ai` AFTER INSERT ON `tbl_artista` FOR EACH ROW begin
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
	
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_artista_bi` BEFORE INSERT ON `tbl_artista` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
	if new.seudonimo is null then
		set new.seudonimo = new.nombre;
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_artista_bu` BEFORE UPDATE ON `tbl_artista` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
	if new.seudonimo is null then
		set new.seudonimo = old.seudonimo;
	end if;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_bitacora`
--

CREATE TABLE `tbl_bitacora` (
  `id` int(11) NOT NULL,
  `idadmin` int(11) NOT NULL,
  `texto` text COLLATE utf8_spanish_ci NOT NULL,
  `fechamod` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_bitacora`
--

INSERT INTO `tbl_bitacora` VALUES(1, 1, 'Se inserta el usuario Humberto Díaz con correo hdiaz@gmail.com y contraseña hhhhhh', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(2, 1, 'Se inserta el usuario Mabel Poblet con correo mpoblet@gmail.com y contraseña mmmmmm', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(3, 1, 'Se inserta el usuario Julio con correo jtoirac@gmail.com y contraseña dddddd', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(4, 1, 'Se inserta el usuario Taimi Antunez con correo taimioro@nauta.cu y contraseña gggggg', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(5, 1, 'Se inserta el usuario ghjdfg dfghdfg con correo sdfgf@fdgsdf.fgd y contraseña uno', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(6, 1, 'Se inserta el usuario tyuertyvert con correo sdfgf@gerteq.fgd y contraseña dos', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(7, 1, 'Se inserta el usuario drtysrtyrtsh con correo dghnmnmjd@sdfhgsad.fgsa y contraseña tres', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(8, 1, 'Se inserta el usuario gnertgaertg con correo zsdfsdfgd@fdghasd.fg y contraseña cuatro', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(9, 1, 'Se inserta el usuario rtkrtfykryu con correo ouioykuy@sdfgsd.dfg y contraseña cinco', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(10, 1, 'Se inserta el usuario rfnbernjj s gg con correo rtyjetyery@sdfgasdf.dfx y contraseña seis', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(11, 1, 'Se inserta el usuario hetyherthert con correo fghjdgj@sdfgsdf.fg y contraseña siete', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(12, 1, 'Se inserta el usuario dhsfghsfdg con correo dsfghsdfh@srdtgsdfg.fgh y contraseña ocho', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(13, 1, 'Se inserta el usuario dfghsdfhsdftyku con correo dsfghsdfh@srdtsdfgs.fgh y contraseña nueve', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(14, 1, 'Se inserta el usuario dfghsd sdfgg sdf con correo fhjdfsdfgh@rdtydgx.fgh y contraseña diez', 1646328036);
INSERT INTO `tbl_bitacora` VALUES(15, 3, 'Entrada al sitio', 1646328266);
INSERT INTO `tbl_bitacora` VALUES(16, 3, 'Entrada al sitio', 1646328318);
INSERT INTO `tbl_bitacora` VALUES(17, 3, 'p = obr || ', 1646328324);
INSERT INTO `tbl_bitacora` VALUES(18, 3, 'p = obr || ', 1646328390);
INSERT INTO `tbl_bitacora` VALUES(19, 3, 'p = obr || ', 1646328497);
INSERT INTO `tbl_bitacora` VALUES(20, 3, 'Entrada al sitio', 1646330943);
INSERT INTO `tbl_bitacora` VALUES(21, 3, 'Entrada al sitio', 1646331078);
INSERT INTO `tbl_bitacora` VALUES(22, 3, 'Entrada al sitio', 1646331093);
INSERT INTO `tbl_bitacora` VALUES(23, 3, 'Entrada al sitio', 1646331118);
INSERT INTO `tbl_bitacora` VALUES(24, 3, 'p = obr || ', 1646332065);
INSERT INTO `tbl_bitacora` VALUES(25, 3, 'p = dpr || ', 1646332087);
INSERT INTO `tbl_bitacora` VALUES(26, 3, 'El usuario Julio cambia sus datos; jtoirac@gmail.com, dddddd, .,  , d/m/Y, H:i', 1646332117);
INSERT INTO `tbl_bitacora` VALUES(27, 3, 'Entrada al sitio', 1646332131);
INSERT INTO `tbl_bitacora` VALUES(28, 3, 'p = ctc || ', 1646332144);
INSERT INTO `tbl_bitacora` VALUES(29, 3, 'p = clt || ', 1646332146);
INSERT INTO `tbl_bitacora` VALUES(30, 3, 'p = crr || ', 1646332147);
INSERT INTO `tbl_bitacora` VALUES(31, 3, 'p = obr || ', 1646332149);
INSERT INTO `tbl_bitacora` VALUES(32, 3, 'p = obr || pag = obr || columnas = a.id &#039;Editar{edit}&#039;, a.id &#039;Desactivar{borr}&#039;, a.nombre &#039;Artista&#039;, a.seudonimo &#039;Seudónimo&#039;, a.correo &#039;Correo{mail}&#039;, case a.activo when &#039;1&#039; then &#039;Si&#039; else &#039;No&#039; end &#039;Activo&#039;, a.fechamod &#039;Fecha Mod{fec}&#039;, case a.activo when &#039;1&#039; then &#039;black&#039; else &#039;navy&#039; end &#039;{col}&#039; || tablas = tbl_artista a || buscar =  || orden = &#039;Fecha Mod&#039; desc || numpag = 1 || accion = modifica || id = 1 || arti = 2 || nomben = esta || nombes = este || inv = 5 || idioma = 1 || ano = 2022 || declen =  || decles =  || obseren =  || obseres =  || enviar = Enviar || ', 1646332215);
INSERT INTO `tbl_bitacora` VALUES(33, 3, 'p = obr || ', 1646332239);
INSERT INTO `tbl_bitacora` VALUES(34, 3, 'p = clt || ', 1646332268);
INSERT INTO `tbl_bitacora` VALUES(35, 3, 'p = ctc || ', 1646332269);
INSERT INTO `tbl_bitacora` VALUES(36, 3, 'p = usr || ', 1646332272);
INSERT INTO `tbl_bitacora` VALUES(37, 3, 'p = srs || ', 1646332282);
INSERT INTO `tbl_bitacora` VALUES(38, 3, 'p = edc || ', 1646332286);
INSERT INTO `tbl_bitacora` VALUES(39, 3, 'p = fac || ', 1646332290);
INSERT INTO `tbl_bitacora` VALUES(40, 3, 'p = frt || ', 1646332292);
INSERT INTO `tbl_bitacora` VALUES(41, 3, 'p = cng || ', 1646332295);
INSERT INTO `tbl_bitacora` VALUES(42, 3, 'p = dpr || ', 1646332300);
INSERT INTO `tbl_bitacora` VALUES(43, 3, 'p = dpr || ', 1646334860);
INSERT INTO `tbl_bitacora` VALUES(44, 3, 'Entrada al sitio', 1646406777);
INSERT INTO `tbl_bitacora` VALUES(45, 3, 'p = dpr || ', 1646406784);
INSERT INTO `tbl_bitacora` VALUES(46, 3, 'Entrada al sitio', 1646407300);
INSERT INTO `tbl_bitacora` VALUES(47, 3, 'p = dpr || ', 1646407309);
INSERT INTO `tbl_bitacora` VALUES(48, 3, 'p = art || ', 1646407337);
INSERT INTO `tbl_bitacora` VALUES(49, 3, 'p = usr || ', 1646407340);
INSERT INTO `tbl_bitacora` VALUES(50, 3, 'p = eobr || ', 1646407384);
INSERT INTO `tbl_bitacora` VALUES(51, 3, 'p = epg || ', 1646407404);
INSERT INTO `tbl_bitacora` VALUES(52, 3, 'p = art || ', 1646407413);
INSERT INTO `tbl_bitacora` VALUES(53, 3, 'p = edc || ', 1646407420);
INSERT INTO `tbl_bitacora` VALUES(54, 3, 'p = srs || ', 1646407424);
INSERT INTO `tbl_bitacora` VALUES(55, 3, 'p = clt || ', 1646407435);
INSERT INTO `tbl_bitacora` VALUES(56, 3, 'p = crr || ', 1646407437);
INSERT INTO `tbl_bitacora` VALUES(57, 3, 'p = obr || ', 1646407439);
INSERT INTO `tbl_bitacora` VALUES(58, 3, 'p = crr || ', 1646407473);
INSERT INTO `tbl_bitacora` VALUES(59, 3, 'p = clt || ', 1646407477);
INSERT INTO `tbl_bitacora` VALUES(60, 3, 'p = ctc || ', 1646407479);
INSERT INTO `tbl_bitacora` VALUES(61, 3, 'p = dpr || ', 1646407488);
INSERT INTO `tbl_bitacora` VALUES(62, 3, 'p = usr || ', 1646407503);
INSERT INTO `tbl_bitacora` VALUES(63, 3, 'p = usr || ', 1646408694);
INSERT INTO `tbl_bitacora` VALUES(64, 3, 'p = usr || ', 1646409092);
INSERT INTO `tbl_bitacora` VALUES(65, 3, 'p = usr || ', 1646409526);
INSERT INTO `tbl_bitacora` VALUES(66, 3, 'p = usr || ', 1646409715);
INSERT INTO `tbl_bitacora` VALUES(67, 3, 'p = usr || ', 1646409776);
INSERT INTO `tbl_bitacora` VALUES(68, 3, 'p = usr || ', 1646409935);
INSERT INTO `tbl_bitacora` VALUES(69, 3, 'p = usr || ', 1646410119);
INSERT INTO `tbl_bitacora` VALUES(70, 3, 'Entrada al sitio', 1646410159);
INSERT INTO `tbl_bitacora` VALUES(71, 3, 'p = usr || ', 1646410165);
INSERT INTO `tbl_bitacora` VALUES(72, 3, 'p = usr || ', 1646411642);
INSERT INTO `tbl_bitacora` VALUES(73, 3, 'p = usr || ', 1646411805);
INSERT INTO `tbl_bitacora` VALUES(74, 3, 'p = usr || ', 1646412535);
INSERT INTO `tbl_bitacora` VALUES(75, 3, 'p = usr || ', 1646412873);
INSERT INTO `tbl_bitacora` VALUES(76, 3, 'p = usr || ', 1646413076);
INSERT INTO `tbl_bitacora` VALUES(77, 3, 'Entrada al sitio', 1646423194);
INSERT INTO `tbl_bitacora` VALUES(78, 3, 'Entrada al sitio', 1646423592);
INSERT INTO `tbl_bitacora` VALUES(79, 3, 'Entrada al sitio', 1646423607);

--
-- Disparadores `tbl_bitacora`
--
DELIMITER $$
CREATE TRIGGER `tr_bitacora_bi` BEFORE INSERT ON `tbl_bitacora` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_bitacora_bu` BEFORE UPDATE ON `tbl_bitacora` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_cliente`
--

CREATE TABLE `tbl_cliente` (
  `id` int(11) NOT NULL,
  `idartista` int(11) NOT NULL,
  `nombre` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  `telfcliente` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `correo` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  `direccion` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `contacto` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  `telefcontacto` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fechamod` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_cliente`
--

INSERT INTO `tbl_cliente` VALUES(1, 1, 'cliente de Humbe', '35463543', 'hdfhgsd@gmail.com', NULL, '', NULL, 1646328036);

--
-- Disparadores `tbl_cliente`
--
DELIMITER $$
CREATE TRIGGER `tr_cliente_bi` BEFORE INSERT ON `tbl_cliente` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_cliente_bu` BEFORE UPDATE ON `tbl_cliente` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_colArtistaAdmin`
--

CREATE TABLE `tbl_colArtistaAdmin` (
  `id` int(11) NOT NULL,
  `idartista` int(11) NOT NULL,
  `idadmin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_colArtistaAdmin`
--

INSERT INTO `tbl_colArtistaAdmin` VALUES(1, 1, 1);
INSERT INTO `tbl_colArtistaAdmin` VALUES(2, 2, 2);
INSERT INTO `tbl_colArtistaAdmin` VALUES(3, 1, 4);
INSERT INTO `tbl_colArtistaAdmin` VALUES(4, 2, 4);
INSERT INTO `tbl_colArtistaAdmin` VALUES(6, 1, 5);
INSERT INTO `tbl_colArtistaAdmin` VALUES(7, 2, 5);
INSERT INTO `tbl_colArtistaAdmin` VALUES(9, 1, 6);
INSERT INTO `tbl_colArtistaAdmin` VALUES(10, 2, 6);
INSERT INTO `tbl_colArtistaAdmin` VALUES(12, 1, 7);
INSERT INTO `tbl_colArtistaAdmin` VALUES(13, 2, 7);
INSERT INTO `tbl_colArtistaAdmin` VALUES(15, 1, 11);
INSERT INTO `tbl_colArtistaAdmin` VALUES(16, 2, 11);
INSERT INTO `tbl_colArtistaAdmin` VALUES(18, 1, 12);
INSERT INTO `tbl_colArtistaAdmin` VALUES(19, 2, 12);
INSERT INTO `tbl_colArtistaAdmin` VALUES(21, 1, 13);
INSERT INTO `tbl_colArtistaAdmin` VALUES(22, 2, 13);
INSERT INTO `tbl_colArtistaAdmin` VALUES(24, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(25, 1, 6);
INSERT INTO `tbl_colArtistaAdmin` VALUES(26, 2, 7);
INSERT INTO `tbl_colArtistaAdmin` VALUES(27, 2, 8);
INSERT INTO `tbl_colArtistaAdmin` VALUES(28, 2, 12);
INSERT INTO `tbl_colArtistaAdmin` VALUES(29, 1, 4);
INSERT INTO `tbl_colArtistaAdmin` VALUES(30, 2, 4);
INSERT INTO `tbl_colArtistaAdmin` VALUES(32, 1, 5);
INSERT INTO `tbl_colArtistaAdmin` VALUES(33, 2, 5);
INSERT INTO `tbl_colArtistaAdmin` VALUES(35, 1, 6);
INSERT INTO `tbl_colArtistaAdmin` VALUES(36, 2, 6);
INSERT INTO `tbl_colArtistaAdmin` VALUES(38, 1, 7);
INSERT INTO `tbl_colArtistaAdmin` VALUES(39, 2, 7);
INSERT INTO `tbl_colArtistaAdmin` VALUES(41, 1, 11);
INSERT INTO `tbl_colArtistaAdmin` VALUES(42, 2, 11);
INSERT INTO `tbl_colArtistaAdmin` VALUES(44, 1, 12);
INSERT INTO `tbl_colArtistaAdmin` VALUES(45, 2, 12);
INSERT INTO `tbl_colArtistaAdmin` VALUES(47, 1, 13);
INSERT INTO `tbl_colArtistaAdmin` VALUES(48, 2, 13);
INSERT INTO `tbl_colArtistaAdmin` VALUES(50, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(51, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(53, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(54, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(56, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(57, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(59, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(60, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(62, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(63, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(65, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(66, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(68, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(69, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(71, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(72, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(74, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(75, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(77, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(78, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(80, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(81, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(83, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(84, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(86, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(87, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(89, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(90, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(92, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(93, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(95, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(96, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(98, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(99, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(101, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(102, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(104, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(105, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(107, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(108, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(110, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(111, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(113, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(114, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(116, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(117, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(119, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(120, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(122, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(123, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(125, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(126, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(128, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(129, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(131, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(132, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(134, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(135, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(137, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(138, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(140, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(141, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(143, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(144, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(146, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(147, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(149, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(150, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(152, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(153, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(155, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(156, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(158, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(159, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(161, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(162, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(164, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(165, 2, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(167, 1, 3);
INSERT INTO `tbl_colArtistaAdmin` VALUES(168, 2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_colArtistaIdioma`
--

CREATE TABLE `tbl_colArtistaIdioma` (
  `id` int(11) NOT NULL,
  `idartista` int(11) NOT NULL,
  `ididioma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_colArtistaIdioma`
--

INSERT INTO `tbl_colArtistaIdioma` VALUES(1, 1, 1);
INSERT INTO `tbl_colArtistaIdioma` VALUES(2, 1, 2);
INSERT INTO `tbl_colArtistaIdioma` VALUES(3, 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_colImagenesObra`
--

CREATE TABLE `tbl_colImagenesObra` (
  `id` int(11) NOT NULL,
  `idobra` int(11) NOT NULL,
  `idimagen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_colOfertaObra`
--

CREATE TABLE `tbl_colOfertaObra` (
  `id` int(11) NOT NULL,
  `idobra` int(11) NOT NULL,
  `idoferta` int(11) NOT NULL,
  `precio` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_colSetupAdmin`
--

CREATE TABLE `tbl_colSetupAdmin` (
  `id` int(11) NOT NULL,
  `idsetup` int(11) NOT NULL,
  `idadmin` int(11) NOT NULL,
  `valor` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `fechamod` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_colSetupAdmin`
--

INSERT INTO `tbl_colSetupAdmin` VALUES(1, 6, 1, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(2, 7, 1, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(3, 8, 1, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(4, 9, 1, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(5, 10, 1, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(6, 6, 2, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(7, 7, 2, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(8, 8, 2, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(9, 9, 2, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(10, 10, 2, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(11, 6, 3, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(12, 7, 3, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(13, 8, 3, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(14, 9, 3, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(15, 10, 3, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(16, 6, 4, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(17, 7, 4, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(18, 8, 4, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(19, 9, 4, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(20, 10, 4, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(21, 6, 5, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(22, 7, 5, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(23, 8, 5, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(24, 9, 5, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(25, 10, 5, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(26, 6, 6, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(27, 7, 6, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(28, 8, 6, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(29, 9, 6, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(30, 10, 6, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(31, 6, 7, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(32, 7, 7, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(33, 8, 7, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(34, 9, 7, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(35, 10, 7, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(36, 6, 8, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(37, 7, 8, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(38, 8, 8, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(39, 9, 8, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(40, 10, 8, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(41, 6, 9, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(42, 7, 9, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(43, 8, 9, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(44, 9, 9, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(45, 10, 9, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(46, 6, 10, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(47, 7, 10, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(48, 8, 10, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(49, 9, 10, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(50, 10, 10, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(51, 6, 11, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(52, 7, 11, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(53, 8, 11, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(54, 9, 11, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(55, 10, 11, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(56, 6, 12, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(57, 7, 12, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(58, 8, 12, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(59, 9, 12, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(60, 10, 12, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(61, 6, 13, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(62, 7, 13, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(63, 8, 13, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(64, 9, 13, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(65, 10, 13, 'H:i', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(66, 6, 14, '30', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(67, 7, 14, '.', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(68, 8, 14, ' ', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(69, 9, 14, 'd/m/Y', 1646328036);
INSERT INTO `tbl_colSetupAdmin` VALUES(70, 10, 14, 'H:i', 1646328036);

--
-- Disparadores `tbl_colSetupAdmin`
--
DELIMITER $$
CREATE TRIGGER `tr_colSetupAdmin_bi` BEFORE INSERT ON `tbl_colSetupAdmin` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_colSetupAdmin_bu` BEFORE UPDATE ON `tbl_colSetupAdmin` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_cuentaBanc`
--

CREATE TABLE `tbl_cuentaBanc` (
  `id` int(11) NOT NULL,
  `idartista` int(11) NOT NULL,
  `idmoneda` int(11) NOT NULL,
  `texto` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `montoInicial` float NOT NULL DEFAULT 0,
  `fechamod` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `tbl_cuentaBanc`
--
DELIMITER $$
CREATE TRIGGER `tr_cuentaBanc_bi` BEFORE INSERT ON `tbl_cuentaBanc` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_cuentaBanc_bu` BEFORE UPDATE ON `tbl_cuentaBanc` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_curriculo`
--

CREATE TABLE `tbl_curriculo` (
  `id` int(11) NOT NULL,
  `idartista` int(11) NOT NULL,
  `idadmin` int(11) NOT NULL COMMENT 'ult usuario que alteró currículo',
  `fechamod` int(11) NOT NULL DEFAULT 0,
  `cuentaBanc` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `tbl_curriculo`
--
DELIMITER $$
CREATE TRIGGER `tr_curriculo_bi` BEFORE INSERT ON `tbl_curriculo` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_curriculo_bu` BEFORE UPDATE ON `tbl_curriculo` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_debug`
--

CREATE TABLE `tbl_debug` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `valor` varchar(300) COLLATE utf8_spanish_ci NOT NULL,
  `fechamod` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `tbl_debug`
--
DELIMITER $$
CREATE TRIGGER `tr_debug_bi` BEFORE INSERT ON `tbl_debug` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_ediciones`
--

CREATE TABLE `tbl_ediciones` (
  `id` int(11) NOT NULL,
  `idobra` int(11) NOT NULL,
  `idestado` int(11) NOT NULL,
  `idpago` int(11) NOT NULL DEFAULT 1,
  `idmoneda` int(11) NOT NULL,
  `inventario` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `ubicacion` varchar(150) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'Estudio Artista',
  `vendidoa` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `vendidopor` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `costo` float NOT NULL DEFAULT 0,
  `precio` float NOT NULL DEFAULT 0,
  `precioventa` float NOT NULL DEFAULT 0,
  `fecha` int(11) NOT NULL,
  `fechaventa` int(11) DEFAULT NULL,
  `fechamod` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `tbl_ediciones`
--
DELIMITER $$
CREATE TRIGGER `tr_ediciones_bi` BEFORE INSERT ON `tbl_ediciones` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
	if new.fecha is null then
		set new.fecha = unix_timestamp();
	end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_ediciones_bu` BEFORE UPDATE ON `tbl_ediciones` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_estado`
--

CREATE TABLE `tbl_estado` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_estado`
--

INSERT INTO `tbl_estado` VALUES(1, 'Vendido');
INSERT INTO `tbl_estado` VALUES(2, 'Disponible');
INSERT INTO `tbl_estado` VALUES(3, 'Donado');
INSERT INTO `tbl_estado` VALUES(4, 'Intercambiado');
INSERT INTO `tbl_estado` VALUES(5, 'Reservado');
INSERT INTO `tbl_estado` VALUES(6, 'Archivo');
INSERT INTO `tbl_estado` VALUES(7, 'Destruida');
INSERT INTO `tbl_estado` VALUES(8, 'Consignada');
INSERT INTO `tbl_estado` VALUES(9, 'Expuesta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_estadoPago`
--

CREATE TABLE `tbl_estadoPago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_estadoPago`
--

INSERT INTO `tbl_estadoPago` VALUES(1, 'No pactado');
INSERT INTO `tbl_estadoPago` VALUES(2, 'Pendiente');
INSERT INTO `tbl_estadoPago` VALUES(3, 'Pagado');
INSERT INTO `tbl_estadoPago` VALUES(4, 'Pago parcial');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_facturacion`
--

CREATE TABLE `tbl_facturacion` (
  `id` int(11) NOT NULL,
  `idedicion` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idcuenta` int(11) NOT NULL,
  `idestado` int(11) NOT NULL,
  `facturado` float NOT NULL DEFAULT 0,
  `pagado` float NOT NULL DEFAULT 0,
  `fecha` int(11) NOT NULL DEFAULT 0,
  `fechamod` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `tbl_facturacion`
--
DELIMITER $$
CREATE TRIGGER `tr_facturacion_bi` BEFORE INSERT ON `tbl_facturacion` FOR EACH ROW begin
	if new.idestado is null then
		set new.idestado = 2;
	end if;

	if new.fecha is null then
		set new.fecha = unix_timestamp();
	end if;

	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_facturacion_bu` BEFORE UPDATE ON `tbl_facturacion` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_idioma`
--

CREATE TABLE `tbl_idioma` (
  `id` int(11) NOT NULL,
  `idioma` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `iso2` char(2) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_idioma`
--

INSERT INTO `tbl_idioma` VALUES(1, 'Español', 'es');
INSERT INTO `tbl_idioma` VALUES(2, 'English', 'en');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_imagenes`
--

CREATE TABLE `tbl_imagenes` (
  `id` int(11) NOT NULL,
  `tipo` char(1) COLLATE utf8_spanish_ci NOT NULL DEFAULT '1' COMMENT '1-img de la obra, 2-img del artista',
  `direccion` varchar(150) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_imagenes`
--

INSERT INTO `tbl_imagenes` VALUES(1, '2', 'images/artista/2/profile.jpeg');
INSERT INTO `tbl_imagenes` VALUES(2, '2', 'images/artista/1/profile.jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_medio`
--

CREATE TABLE `tbl_medio` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_medio`
--

INSERT INTO `tbl_medio` VALUES(1, 'Fotografía');
INSERT INTO `tbl_medio` VALUES(2, 'Escultura');
INSERT INTO `tbl_medio` VALUES(3, 'Pintura');
INSERT INTO `tbl_medio` VALUES(4, 'Dibujo');
INSERT INTO `tbl_medio` VALUES(5, 'Grabado');
INSERT INTO `tbl_medio` VALUES(6, 'Impresión');
INSERT INTO `tbl_medio` VALUES(7, 'Varios');
INSERT INTO `tbl_medio` VALUES(8, 'Audio Visual');
INSERT INTO `tbl_medio` VALUES(9, 'Site Specific');
INSERT INTO `tbl_medio` VALUES(10, 'Instalación');
INSERT INTO `tbl_medio` VALUES(11, 'Proyecto');
INSERT INTO `tbl_medio` VALUES(12, 'Performance');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_moneda`
--

CREATE TABLE `tbl_moneda` (
  `id` int(11) NOT NULL,
  `moneda` char(3) COLLATE utf8_spanish_ci NOT NULL,
  `denominacion` varchar(40) COLLATE utf8_spanish_ci NOT NULL,
  `fechamod` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_moneda`
--

INSERT INTO `tbl_moneda` VALUES(32, 'ARS', 'Peso Argentino', 1646328036);
INSERT INTO `tbl_moneda` VALUES(124, 'CAD', 'Dolar Canadiense', 1646328036);
INSERT INTO `tbl_moneda` VALUES(152, 'CLP', 'Peso Chileno', 1646328036);
INSERT INTO `tbl_moneda` VALUES(170, 'COP', 'Peso Colombiano', 1646328036);
INSERT INTO `tbl_moneda` VALUES(192, 'CUP', 'Peso Cubano', 1646328036);
INSERT INTO `tbl_moneda` VALUES(356, 'INR', 'Rupia India', 1646328036);
INSERT INTO `tbl_moneda` VALUES(392, 'JPY', 'Yen Japones', 1646328036);
INSERT INTO `tbl_moneda` VALUES(484, 'MXN', 'Peso Mexicano', 1646328036);
INSERT INTO `tbl_moneda` VALUES(604, 'PEN', 'Peso Peruano', 1646328036);
INSERT INTO `tbl_moneda` VALUES(756, 'CHF', 'Franco Suizo', 1646328036);
INSERT INTO `tbl_moneda` VALUES(826, 'GBP', 'Libra Esterlina', 1646328036);
INSERT INTO `tbl_moneda` VALUES(840, 'USD', 'Dolar USA', 1646328036);
INSERT INTO `tbl_moneda` VALUES(931, 'CUC', 'Peso Cubano Convertible', 1646328036);
INSERT INTO `tbl_moneda` VALUES(937, 'VEF', 'Bolivar Venezolano', 1646328036);
INSERT INTO `tbl_moneda` VALUES(949, 'TRY', 'Lira Turca', 1646328036);
INSERT INTO `tbl_moneda` VALUES(978, 'EUR', 'Euro', 1646328036);
INSERT INTO `tbl_moneda` VALUES(986, 'BRL', 'Real Brasileno', 1646328036);

--
-- Disparadores `tbl_moneda`
--
DELIMITER $$
CREATE TRIGGER `tr_moneda_bi` BEFORE INSERT ON `tbl_moneda` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_moneda_bu` BEFORE UPDATE ON `tbl_moneda` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_obra`
--

CREATE TABLE `tbl_obra` (
  `id` int(11) NOT NULL,
  `idartista` int(11) NOT NULL,
  `idserie` int(11) NOT NULL,
  `idmedio` int(11) NOT NULL,
  `ano` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `inventario` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` int(11) NOT NULL,
  `fechamod` int(11) NOT NULL DEFAULT 0,
  `cantEdiciones` smallint(5) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_oferta`
--

CREATE TABLE `tbl_oferta` (
  `id` int(11) NOT NULL,
  `idartista` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idadmin` int(11) NOT NULL COMMENT 'admin que puso la oferta',
  `codigo` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `duracion` smallint(4) NOT NULL DEFAULT 15 COMMENT 'cantidad de días que estará válida la oferta',
  `valida` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1-Si, 0-No',
  `fechamod` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `tbl_oferta`
--
DELIMITER $$
CREATE TRIGGER `tr_oferta_bi` BEFORE INSERT ON `tbl_oferta` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
	set new.codigo = md5(new.fechamod);
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_roles`
--

CREATE TABLE `tbl_roles` (
  `id` int(11) NOT NULL,
  `orden` smallint(6) NOT NULL DEFAULT 0,
  `nombre` varchar(50) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `caract` varchar(500) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_roles`
--

INSERT INTO `tbl_roles` VALUES(1, 0, 'Yo', NULL);
INSERT INTO `tbl_roles` VALUES(2, 5, 'Administradores', 'Administradores del Sistema');
INSERT INTO `tbl_roles` VALUES(3, 10, 'Usuario', 'Artistas o sus asistentes podrán editar los datos del artista');
INSERT INTO `tbl_roles` VALUES(4, 15, 'Invitado', 'Sólo podrán ver no editar ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_series`
--

CREATE TABLE `tbl_series` (
  `id` int(11) NOT NULL,
  `idartista` int(11) NOT NULL,
  `idtexto` int(11) NOT NULL,
  `ano` varchar(14) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fechamod` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Disparadores `tbl_series`
--
DELIMITER $$
CREATE TRIGGER `tr_series_bi` BEFORE INSERT ON `tbl_series` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_series_bu` BEFORE UPDATE ON `tbl_series` FOR EACH ROW begin
	set new.fechamod = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_setup`
--

CREATE TABLE `tbl_setup` (
  `id` int(11) NOT NULL,
  `tipo` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1- var para usuario, 2- var para artistas, 3- var generales',
  `nombre` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `valor` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion` varchar(200) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='tabla con las variables del setup';

--
-- Volcado de datos para la tabla `tbl_setup`
--

INSERT INTO `tbl_setup` VALUES(1, 3, 'pagtabl', '30', 'cant. de lineas a mostrar en las tablas');
INSERT INTO `tbl_setup` VALUES(2, 3, 'separdecim', '.', 'separador de decimales');
INSERT INTO `tbl_setup` VALUES(3, 3, 'separmiles', ' ', 'separador de miles');
INSERT INTO `tbl_setup` VALUES(4, 3, 'formfecha', 'd/m/Y', 'formato de las fechas');
INSERT INTO `tbl_setup` VALUES(5, 3, 'formhoras', 'H:i', 'formato de las horas');
INSERT INTO `tbl_setup` VALUES(6, 1, 'pagtabl', NULL, 'cant. de lineas a mostrar en las tablas');
INSERT INTO `tbl_setup` VALUES(7, 1, 'separdecim', NULL, 'separador de decimales');
INSERT INTO `tbl_setup` VALUES(8, 1, 'separmiles', NULL, 'separador de miles');
INSERT INTO `tbl_setup` VALUES(9, 1, 'formfecha', NULL, 'formato de las fechas');
INSERT INTO `tbl_setup` VALUES(10, 1, 'formhoras', NULL, 'formato de las horas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_textos`
--

CREATE TABLE `tbl_textos` (
  `id` int(11) NOT NULL,
  `idtipotexto` int(11) NOT NULL,
  `idartista` int(11) NOT NULL,
  `idtexto` int(11) NOT NULL COMMENT 'poner como id la fecha',
  `idioma` char(2) COLLATE utf8_spanish_ci NOT NULL,
  `descripción` varchar(120) COLLATE utf8_spanish_ci NOT NULL,
  `texto` text COLLATE utf8_spanish_ci NOT NULL,
  `fecha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Los escritos de los Artistas en los idiomas dados';

--
-- Disparadores `tbl_textos`
--
DELIMITER $$
CREATE TRIGGER `tr_textos_bi` BEFORE INSERT ON `tbl_textos` FOR EACH ROW begin
	set new.fecha = unix_timestamp();
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_textos_bu` BEFORE UPDATE ON `tbl_textos` FOR EACH ROW begin
	set new.fecha = unix_timestamp();
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_timezone`
--

CREATE TABLE `tbl_timezone` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) CHARACTER SET utf8 NOT NULL,
  `hora` varchar(20) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_timezone`
--

INSERT INTO `tbl_timezone` VALUES(1, 'Africa/Abidjan', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(2, 'Africa/Accra', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(3, 'Africa/Addis_Ababa', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(4, 'Africa/Algiers', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(5, 'Africa/Asmara', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(6, 'Africa/Bamako', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(7, 'Africa/Bangui', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(8, 'Africa/Banjul', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(9, 'Africa/Bissau', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(10, 'Africa/Blantyre', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(11, 'Africa/Brazzaville', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(12, 'Africa/Bujumbura', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(13, 'Africa/Cairo', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(14, 'Africa/Casablanca', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(15, 'Africa/Ceuta', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(16, 'Africa/Conakry', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(17, 'Africa/Dakar', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(18, 'Africa/Dar_es_Salaam', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(19, 'Africa/Djibouti', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(20, 'Africa/Douala', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(21, 'Africa/El_Aaiun', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(22, 'Africa/Freetown', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(23, 'Africa/Gaborone', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(24, 'Africa/Harare', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(25, 'Africa/Johannesburg', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(26, 'Africa/Juba', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(27, 'Africa/Kampala', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(28, 'Africa/Khartoum', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(29, 'Africa/Kigali', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(30, 'Africa/Kinshasa', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(31, 'Africa/Lagos', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(32, 'Africa/Libreville', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(33, 'Africa/Lome', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(34, 'Africa/Luanda', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(35, 'Africa/Lubumbashi', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(36, 'Africa/Lusaka', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(37, 'Africa/Malabo', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(38, 'Africa/Maputo', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(39, 'Africa/Maseru', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(40, 'Africa/Mbabane', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(41, 'Africa/Mogadishu', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(42, 'Africa/Monrovia', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(43, 'Africa/Nairobi', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(44, 'Africa/Ndjamena', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(45, 'Africa/Niamey', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(46, 'Africa/Nouakchott', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(47, 'Africa/Ouagadougou', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(48, 'Africa/Porto-Novo', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(49, 'Africa/Sao_Tome', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(50, 'Africa/Tripoli', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(51, 'Africa/Tunis', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(52, 'Africa/Windhoek', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(53, 'America/Adak', 'UTC -10:00');
INSERT INTO `tbl_timezone` VALUES(54, 'America/Anchorage', 'UTC -09:00');
INSERT INTO `tbl_timezone` VALUES(55, 'America/Anguilla', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(56, 'America/Antigua', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(57, 'America/Araguaina', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(58, 'America/Argentina/Buenos_Aires', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(59, 'America/Argentina/Catamarca', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(60, 'America/Argentina/Cordoba', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(61, 'America/Argentina/Jujuy', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(62, 'America/Argentina/La_Rioja', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(63, 'America/Argentina/Mendoza', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(64, 'America/Argentina/Rio_Gallegos', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(65, 'America/Argentina/Salta', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(66, 'America/Argentina/San_Juan', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(67, 'America/Argentina/San_Luis', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(68, 'America/Argentina/Tucuman', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(69, 'America/Argentina/Ushuaia', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(70, 'America/Aruba', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(71, 'America/Asuncion', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(72, 'America/Atikokan', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(73, 'America/Bahia', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(74, 'America/Bahia_Banderas', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(75, 'America/Barbados', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(76, 'America/Belem', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(77, 'America/Belize', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(78, 'America/Blanc-Sablon', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(79, 'America/Boa_Vista', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(80, 'America/Bogota', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(81, 'America/Boise', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(82, 'America/Cambridge_Bay', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(83, 'America/Campo_Grande', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(84, 'America/Cancun', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(85, 'America/Caracas', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(86, 'America/Cayenne', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(87, 'America/Cayman', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(88, 'America/Chicago', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(89, 'America/Chihuahua', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(90, 'America/Costa_Rica', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(91, 'America/Creston', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(92, 'America/Cuiaba', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(93, 'America/Curacao', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(94, 'America/Danmarkshavn', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(95, 'America/Dawson', 'UTC -08:00');
INSERT INTO `tbl_timezone` VALUES(96, 'America/Dawson_Creek', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(97, 'America/Denver', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(98, 'America/Detroit', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(99, 'America/Dominica', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(100, 'America/Edmonton', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(101, 'America/Eirunepe', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(102, 'America/El_Salvador', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(103, 'America/Fort_Nelson', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(104, 'America/Fortaleza', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(105, 'America/Glace_Bay', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(106, 'America/Godthab', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(107, 'America/Goose_Bay', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(108, 'America/Grand_Turk', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(109, 'America/Grenada', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(110, 'America/Guadeloupe', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(111, 'America/Guatemala', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(112, 'America/Guayaquil', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(113, 'America/Guyana', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(114, 'America/Halifax', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(115, 'America/Havana', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(116, 'America/Hermosillo', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(117, 'America/Indiana/Indianapolis', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(118, 'America/Indiana/Knox', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(119, 'America/Indiana/Marengo', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(120, 'America/Indiana/Petersburg', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(121, 'America/Indiana/Tell_City', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(122, 'America/Indiana/Vevay', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(123, 'America/Indiana/Vincennes', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(124, 'America/Indiana/Winamac', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(125, 'America/Inuvik', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(126, 'America/Iqaluit', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(127, 'America/Jamaica', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(128, 'America/Juneau', 'UTC -09:00');
INSERT INTO `tbl_timezone` VALUES(129, 'America/Kentucky/Louisville', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(130, 'America/Kentucky/Monticello', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(131, 'America/Kralendijk', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(132, 'America/La_Paz', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(133, 'America/Lima', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(134, 'America/Los_Angeles', 'UTC -08:00');
INSERT INTO `tbl_timezone` VALUES(135, 'America/Lower_Princes', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(136, 'America/Maceio', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(137, 'America/Managua', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(138, 'America/Manaus', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(139, 'America/Marigot', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(140, 'America/Martinique', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(141, 'America/Matamoros', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(142, 'America/Mazatlan', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(143, 'America/Menominee', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(144, 'America/Merida', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(145, 'America/Metlakatla', 'UTC -09:00');
INSERT INTO `tbl_timezone` VALUES(146, 'America/Mexico_City', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(147, 'America/Miquelon', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(148, 'America/Moncton', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(149, 'America/Monterrey', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(150, 'America/Montevideo', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(151, 'America/Montserrat', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(152, 'America/Nassau', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(153, 'America/New_York', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(154, 'America/Nipigon', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(155, 'America/Nome', 'UTC -09:00');
INSERT INTO `tbl_timezone` VALUES(156, 'America/Noronha', 'UTC -02:00');
INSERT INTO `tbl_timezone` VALUES(157, 'America/North_Dakota/Beulah', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(158, 'America/North_Dakota/Center', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(159, 'America/North_Dakota/New_Salem', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(160, 'America/Ojinaga', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(161, 'America/Panama', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(162, 'America/Pangnirtung', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(163, 'America/Paramaribo', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(164, 'America/Phoenix', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(165, 'America/Port-au-Prince', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(166, 'America/Port_of_Spain', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(167, 'America/Porto_Velho', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(168, 'America/Puerto_Rico', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(169, 'America/Rainy_River', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(170, 'America/Rankin_Inlet', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(171, 'America/Recife', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(172, 'America/Regina', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(173, 'America/Resolute', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(174, 'America/Rio_Branco', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(175, 'America/Santarem', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(176, 'America/Santiago', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(177, 'America/Santo_Domingo', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(178, 'America/Sao_Paulo', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(179, 'America/Scoresbysund', 'UTC -01:00');
INSERT INTO `tbl_timezone` VALUES(180, 'America/Sitka', 'UTC -09:00');
INSERT INTO `tbl_timezone` VALUES(181, 'America/St_Barthelemy', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(182, 'America/St_Johns', 'UTC -03:30');
INSERT INTO `tbl_timezone` VALUES(183, 'America/St_Kitts', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(184, 'America/St_Lucia', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(185, 'America/St_Thomas', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(186, 'America/St_Vincent', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(187, 'America/Swift_Current', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(188, 'America/Tegucigalpa', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(189, 'America/Thule', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(190, 'America/Thunder_Bay', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(191, 'America/Tijuana', 'UTC -08:00');
INSERT INTO `tbl_timezone` VALUES(192, 'America/Toronto', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(193, 'America/Tortola', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(194, 'America/Vancouver', 'UTC -08:00');
INSERT INTO `tbl_timezone` VALUES(195, 'America/Whitehorse', 'UTC -08:00');
INSERT INTO `tbl_timezone` VALUES(196, 'America/Winnipeg', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(197, 'America/Yakutat', 'UTC -09:00');
INSERT INTO `tbl_timezone` VALUES(198, 'America/Yellowknife', 'UTC -07:00');
INSERT INTO `tbl_timezone` VALUES(199, 'Antarctica/Casey', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(200, 'Antarctica/Davis', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(201, 'Antarctica/DumontDUrville', 'UTC +10:00');
INSERT INTO `tbl_timezone` VALUES(202, 'Antarctica/Macquarie', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(203, 'Antarctica/Mawson', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(204, 'Antarctica/McMurdo', 'UTC +13:00');
INSERT INTO `tbl_timezone` VALUES(205, 'Antarctica/Palmer', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(206, 'Antarctica/Rothera', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(207, 'Antarctica/Syowa', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(208, 'Antarctica/Troll', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(209, 'Antarctica/Vostok', 'UTC +06:00');
INSERT INTO `tbl_timezone` VALUES(210, 'Arctic/Longyearbyen', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(211, 'Asia/Aden', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(212, 'Asia/Almaty', 'UTC +06:00');
INSERT INTO `tbl_timezone` VALUES(213, 'Asia/Amman', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(214, 'Asia/Anadyr', 'UTC +12:00');
INSERT INTO `tbl_timezone` VALUES(215, 'Asia/Aqtau', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(216, 'Asia/Aqtobe', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(217, 'Asia/Ashgabat', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(218, 'Asia/Atyrau', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(219, 'Asia/Baghdad', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(220, 'Asia/Bahrain', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(221, 'Asia/Baku', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(222, 'Asia/Bangkok', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(223, 'Asia/Barnaul', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(224, 'Asia/Beirut', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(225, 'Asia/Bishkek', 'UTC +06:00');
INSERT INTO `tbl_timezone` VALUES(226, 'Asia/Brunei', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(227, 'Asia/Chita', 'UTC +09:00');
INSERT INTO `tbl_timezone` VALUES(228, 'Asia/Choibalsan', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(229, 'Asia/Colombo', 'UTC +05:30');
INSERT INTO `tbl_timezone` VALUES(230, 'Asia/Damascus', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(231, 'Asia/Dhaka', 'UTC +06:00');
INSERT INTO `tbl_timezone` VALUES(232, 'Asia/Dili', 'UTC +09:00');
INSERT INTO `tbl_timezone` VALUES(233, 'Asia/Dubai', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(234, 'Asia/Dushanbe', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(235, 'Asia/Famagusta', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(236, 'Asia/Gaza', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(237, 'Asia/Hebron', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(238, 'Asia/Ho_Chi_Minh', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(239, 'Asia/Hong_Kong', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(240, 'Asia/Hovd', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(241, 'Asia/Irkutsk', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(242, 'Asia/Jakarta', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(243, 'Asia/Jayapura', 'UTC +09:00');
INSERT INTO `tbl_timezone` VALUES(244, 'Asia/Jerusalem', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(245, 'Asia/Kabul', 'UTC +04:30');
INSERT INTO `tbl_timezone` VALUES(246, 'Asia/Kamchatka', 'UTC +12:00');
INSERT INTO `tbl_timezone` VALUES(247, 'Asia/Karachi', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(248, 'Asia/Kathmandu', 'UTC +05:45');
INSERT INTO `tbl_timezone` VALUES(249, 'Asia/Khandyga', 'UTC +09:00');
INSERT INTO `tbl_timezone` VALUES(250, 'Asia/Kolkata', 'UTC +05:30');
INSERT INTO `tbl_timezone` VALUES(251, 'Asia/Krasnoyarsk', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(252, 'Asia/Kuala_Lumpur', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(253, 'Asia/Kuching', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(254, 'Asia/Kuwait', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(255, 'Asia/Macau', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(256, 'Asia/Magadan', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(257, 'Asia/Makassar', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(258, 'Asia/Manila', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(259, 'Asia/Muscat', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(260, 'Asia/Nicosia', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(261, 'Asia/Novokuznetsk', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(262, 'Asia/Novosibirsk', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(263, 'Asia/Omsk', 'UTC +06:00');
INSERT INTO `tbl_timezone` VALUES(264, 'Asia/Oral', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(265, 'Asia/Phnom_Penh', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(266, 'Asia/Pontianak', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(267, 'Asia/Pyongyang', 'UTC +08:30');
INSERT INTO `tbl_timezone` VALUES(268, 'Asia/Qatar', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(269, 'Asia/Qyzylorda', 'UTC +06:00');
INSERT INTO `tbl_timezone` VALUES(270, 'Asia/Riyadh', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(271, 'Asia/Sakhalin', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(272, 'Asia/Samarkand', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(273, 'Asia/Seoul', 'UTC +09:00');
INSERT INTO `tbl_timezone` VALUES(274, 'Asia/Shanghai', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(275, 'Asia/Singapore', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(276, 'Asia/Srednekolymsk', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(277, 'Asia/Taipei', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(278, 'Asia/Tashkent', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(279, 'Asia/Tbilisi', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(280, 'Asia/Tehran', 'UTC +03:30');
INSERT INTO `tbl_timezone` VALUES(281, 'Asia/Thimphu', 'UTC +06:00');
INSERT INTO `tbl_timezone` VALUES(282, 'Asia/Tokyo', 'UTC +09:00');
INSERT INTO `tbl_timezone` VALUES(283, 'Asia/Tomsk', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(284, 'Asia/Ulaanbaatar', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(285, 'Asia/Urumqi', 'UTC +06:00');
INSERT INTO `tbl_timezone` VALUES(286, 'Asia/Ust-Nera', 'UTC +10:00');
INSERT INTO `tbl_timezone` VALUES(287, 'Asia/Vientiane', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(288, 'Asia/Vladivostok', 'UTC +10:00');
INSERT INTO `tbl_timezone` VALUES(289, 'Asia/Yakutsk', 'UTC +09:00');
INSERT INTO `tbl_timezone` VALUES(290, 'Asia/Yangon', 'UTC +06:30');
INSERT INTO `tbl_timezone` VALUES(291, 'Asia/Yekaterinburg', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(292, 'Asia/Yerevan', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(293, 'Atlantic/Azores', 'UTC -01:00');
INSERT INTO `tbl_timezone` VALUES(294, 'Atlantic/Bermuda', 'UTC -04:00');
INSERT INTO `tbl_timezone` VALUES(295, 'Atlantic/Canary', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(296, 'Atlantic/Cape_Verde', 'UTC -01:00');
INSERT INTO `tbl_timezone` VALUES(297, 'Atlantic/Faroe', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(298, 'Atlantic/Madeira', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(299, 'Atlantic/Reykjavik', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(300, 'Atlantic/South_Georgia', 'UTC -02:00');
INSERT INTO `tbl_timezone` VALUES(301, 'Atlantic/St_Helena', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(302, 'Atlantic/Stanley', 'UTC -03:00');
INSERT INTO `tbl_timezone` VALUES(303, 'Australia/Adelaide', 'UTC +10:30');
INSERT INTO `tbl_timezone` VALUES(304, 'Australia/Brisbane', 'UTC +10:00');
INSERT INTO `tbl_timezone` VALUES(305, 'Australia/Broken_Hill', 'UTC +10:30');
INSERT INTO `tbl_timezone` VALUES(306, 'Australia/Currie', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(307, 'Australia/Darwin', 'UTC +09:30');
INSERT INTO `tbl_timezone` VALUES(308, 'Australia/Eucla', 'UTC +08:45');
INSERT INTO `tbl_timezone` VALUES(309, 'Australia/Hobart', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(310, 'Australia/Lindeman', 'UTC +10:00');
INSERT INTO `tbl_timezone` VALUES(311, 'Australia/Lord_Howe', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(312, 'Australia/Melbourne', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(313, 'Australia/Perth', 'UTC +08:00');
INSERT INTO `tbl_timezone` VALUES(314, 'Australia/Sydney', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(315, 'Europe/Amsterdam', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(316, 'Europe/Andorra', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(317, 'Europe/Astrakhan', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(318, 'Europe/Athens', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(319, 'Europe/Belgrade', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(320, 'Europe/Berlin', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(321, 'Europe/Bratislava', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(322, 'Europe/Brussels', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(323, 'Europe/Bucharest', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(324, 'Europe/Budapest', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(325, 'Europe/Busingen', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(326, 'Europe/Chisinau', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(327, 'Europe/Copenhagen', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(328, 'Europe/Dublin', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(329, 'Europe/Gibraltar', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(330, 'Europe/Guernsey', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(331, 'Europe/Helsinki', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(332, 'Europe/Isle_of_Man', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(333, 'Europe/Istanbul', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(334, 'Europe/Jersey', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(335, 'Europe/Kaliningrad', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(336, 'Europe/Kiev', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(337, 'Europe/Kirov', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(338, 'Europe/Lisbon', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(339, 'Europe/Ljubljana', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(340, 'Europe/London', 'UTC +00:00');
INSERT INTO `tbl_timezone` VALUES(341, 'Europe/Luxembourg', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(342, 'Europe/Madrid', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(343, 'Europe/Malta', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(344, 'Europe/Mariehamn', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(345, 'Europe/Minsk', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(346, 'Europe/Monaco', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(347, 'Europe/Moscow', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(348, 'Europe/Oslo', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(349, 'Europe/Paris', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(350, 'Europe/Podgorica', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(351, 'Europe/Prague', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(352, 'Europe/Riga', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(353, 'Europe/Rome', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(354, 'Europe/Samara', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(355, 'Europe/San_Marino', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(356, 'Europe/Sarajevo', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(357, 'Europe/Saratov', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(358, 'Europe/Simferopol', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(359, 'Europe/Skopje', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(360, 'Europe/Sofia', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(361, 'Europe/Stockholm', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(362, 'Europe/Tallinn', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(363, 'Europe/Tirane', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(364, 'Europe/Ulyanovsk', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(365, 'Europe/Uzhgorod', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(366, 'Europe/Vaduz', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(367, 'Europe/Vatican', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(368, 'Europe/Vienna', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(369, 'Europe/Vilnius', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(370, 'Europe/Volgograd', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(371, 'Europe/Warsaw', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(372, 'Europe/Zagreb', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(373, 'Europe/Zaporozhye', 'UTC +02:00');
INSERT INTO `tbl_timezone` VALUES(374, 'Europe/Zurich', 'UTC +01:00');
INSERT INTO `tbl_timezone` VALUES(375, 'Indian/Antananarivo', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(376, 'Indian/Chagos', 'UTC +06:00');
INSERT INTO `tbl_timezone` VALUES(377, 'Indian/Christmas', 'UTC +07:00');
INSERT INTO `tbl_timezone` VALUES(378, 'Indian/Cocos', 'UTC +06:30');
INSERT INTO `tbl_timezone` VALUES(379, 'Indian/Comoro', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(380, 'Indian/Kerguelen', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(381, 'Indian/Mahe', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(382, 'Indian/Maldives', 'UTC +05:00');
INSERT INTO `tbl_timezone` VALUES(383, 'Indian/Mauritius', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(384, 'Indian/Mayotte', 'UTC +03:00');
INSERT INTO `tbl_timezone` VALUES(385, 'Indian/Reunion', 'UTC +04:00');
INSERT INTO `tbl_timezone` VALUES(386, 'Pacific/Apia', 'UTC +14:00');
INSERT INTO `tbl_timezone` VALUES(387, 'Pacific/Auckland', 'UTC +13:00');
INSERT INTO `tbl_timezone` VALUES(388, 'Pacific/Bougainville', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(389, 'Pacific/Chatham', 'UTC +13:45');
INSERT INTO `tbl_timezone` VALUES(390, 'Pacific/Chuuk', 'UTC +10:00');
INSERT INTO `tbl_timezone` VALUES(391, 'Pacific/Easter', 'UTC -05:00');
INSERT INTO `tbl_timezone` VALUES(392, 'Pacific/Efate', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(393, 'Pacific/Enderbury', 'UTC +13:00');
INSERT INTO `tbl_timezone` VALUES(394, 'Pacific/Fakaofo', 'UTC +13:00');
INSERT INTO `tbl_timezone` VALUES(395, 'Pacific/Fiji', 'UTC +12:00');
INSERT INTO `tbl_timezone` VALUES(396, 'Pacific/Funafuti', 'UTC +12:00');
INSERT INTO `tbl_timezone` VALUES(397, 'Pacific/Galapagos', 'UTC -06:00');
INSERT INTO `tbl_timezone` VALUES(398, 'Pacific/Gambier', 'UTC -09:00');
INSERT INTO `tbl_timezone` VALUES(399, 'Pacific/Guadalcanal', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(400, 'Pacific/Guam', 'UTC +10:00');
INSERT INTO `tbl_timezone` VALUES(401, 'Pacific/Honolulu', 'UTC -10:00');
INSERT INTO `tbl_timezone` VALUES(402, 'Pacific/Johnston', 'UTC -10:00');
INSERT INTO `tbl_timezone` VALUES(403, 'Pacific/Kiritimati', 'UTC +14:00');
INSERT INTO `tbl_timezone` VALUES(404, 'Pacific/Kosrae', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(405, 'Pacific/Kwajalein', 'UTC +12:00');
INSERT INTO `tbl_timezone` VALUES(406, 'Pacific/Majuro', 'UTC +12:00');
INSERT INTO `tbl_timezone` VALUES(407, 'Pacific/Marquesas', 'UTC -09:30');
INSERT INTO `tbl_timezone` VALUES(408, 'Pacific/Midway', 'UTC -11:00');
INSERT INTO `tbl_timezone` VALUES(409, 'Pacific/Nauru', 'UTC +12:00');
INSERT INTO `tbl_timezone` VALUES(410, 'Pacific/Niue', 'UTC -11:00');
INSERT INTO `tbl_timezone` VALUES(411, 'Pacific/Norfolk', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(412, 'Pacific/Noumea', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(413, 'Pacific/Pago_Pago', 'UTC -11:00');
INSERT INTO `tbl_timezone` VALUES(414, 'Pacific/Palau', 'UTC +09:00');
INSERT INTO `tbl_timezone` VALUES(415, 'Pacific/Pitcairn', 'UTC -08:00');
INSERT INTO `tbl_timezone` VALUES(416, 'Pacific/Pohnpei', 'UTC +11:00');
INSERT INTO `tbl_timezone` VALUES(417, 'Pacific/Port_Moresby', 'UTC +10:00');
INSERT INTO `tbl_timezone` VALUES(418, 'Pacific/Rarotonga', 'UTC -10:00');
INSERT INTO `tbl_timezone` VALUES(419, 'Pacific/Saipan', 'UTC +10:00');
INSERT INTO `tbl_timezone` VALUES(420, 'Pacific/Tahiti', 'UTC -10:00');
INSERT INTO `tbl_timezone` VALUES(421, 'Pacific/Tarawa', 'UTC +12:00');
INSERT INTO `tbl_timezone` VALUES(422, 'Pacific/Tongatapu', 'UTC +13:00');
INSERT INTO `tbl_timezone` VALUES(423, 'Pacific/Wake', 'UTC +12:00');
INSERT INTO `tbl_timezone` VALUES(424, 'Pacific/Wallis', 'UTC +12:00');
INSERT INTO `tbl_timezone` VALUES(425, 'UTC', 'UTC +00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_tipoTexto`
--

CREATE TABLE `tbl_tipoTexto` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(140) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tbl_tipoTexto`
--

INSERT INTO `tbl_tipoTexto` VALUES(1, 'Currículo del artista tbl_curriculo');
INSERT INTO `tbl_tipoTexto` VALUES(2, 'Factura del artista tbl_curriculo');
INSERT INTO `tbl_tipoTexto` VALUES(3, 'Observaciones de las ediciones tbl_ediciones');
INSERT INTO `tbl_tipoTexto` VALUES(4, 'Observaciones de la obra tbl_obra');
INSERT INTO `tbl_tipoTexto` VALUES(5, 'Statment de las obras tbl_obra');
INSERT INTO `tbl_tipoTexto` VALUES(6, 'Nombre de las obras tbl_obra');
INSERT INTO `tbl_tipoTexto` VALUES(7, 'Nombre de las series tbl_series');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idrol` (`idrol`),
  ADD KEY `activo` (`activo`),
  ADD KEY `fechamod` (`fechamod`),
  ADD KEY `fechaPass` (`fechaPass`),
  ADD KEY `ididioma` (`ididioma`),
  ADD KEY `md5` (`md5`(255)),
  ADD KEY `idx_timezoneAdmin` (`idtimezone`);

--
-- Indices de la tabla `tbl_adminIdioma`
--
ALTER TABLE `tbl_adminIdioma`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idioma_frase` (`idioma`,`frase`);

--
-- Indices de la tabla `tbl_artista`
--
ALTER TABLE `tbl_artista`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seudonimo` (`seudonimo`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `idimg` (`idimg`),
  ADD KEY `activo` (`activo`);

--
-- Indices de la tabla `tbl_bitacora`
--
ALTER TABLE `tbl_bitacora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idadmin` (`idadmin`);

--
-- Indices de la tabla `tbl_cliente`
--
ALTER TABLE `tbl_cliente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idartista` (`idartista`);

--
-- Indices de la tabla `tbl_colArtistaAdmin`
--
ALTER TABLE `tbl_colArtistaAdmin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idartista` (`idartista`),
  ADD KEY `idadmin` (`idadmin`);

--
-- Indices de la tabla `tbl_colArtistaIdioma`
--
ALTER TABLE `tbl_colArtistaIdioma`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idartista` (`idartista`),
  ADD KEY `ididioma` (`ididioma`);

--
-- Indices de la tabla `tbl_colImagenesObra`
--
ALTER TABLE `tbl_colImagenesObra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idobra` (`idobra`),
  ADD KEY `idimagen` (`idimagen`);

--
-- Indices de la tabla `tbl_colOfertaObra`
--
ALTER TABLE `tbl_colOfertaObra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idobra` (`idobra`),
  ADD KEY `idoferta` (`idoferta`);

--
-- Indices de la tabla `tbl_colSetupAdmin`
--
ALTER TABLE `tbl_colSetupAdmin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idadmin` (`idadmin`),
  ADD KEY `idsetup` (`idsetup`);

--
-- Indices de la tabla `tbl_cuentaBanc`
--
ALTER TABLE `tbl_cuentaBanc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idartista` (`idartista`),
  ADD KEY `idmoneda` (`idmoneda`);

--
-- Indices de la tabla `tbl_curriculo`
--
ALTER TABLE `tbl_curriculo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idartista` (`idartista`),
  ADD KEY `idadmin` (`idadmin`);

--
-- Indices de la tabla `tbl_debug`
--
ALTER TABLE `tbl_debug`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tbl_ediciones`
--
ALTER TABLE `tbl_ediciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idobra` (`idobra`),
  ADD KEY `idestado` (`idestado`),
  ADD KEY `idpago` (`idpago`),
  ADD KEY `idmoneda` (`idmoneda`);

--
-- Indices de la tabla `tbl_estado`
--
ALTER TABLE `tbl_estado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tbl_estadoPago`
--
ALTER TABLE `tbl_estadoPago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tbl_facturacion`
--
ALTER TABLE `tbl_facturacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idartista` (`idedicion`),
  ADD KEY `idcliente` (`idcliente`),
  ADD KEY `idcuenta` (`idcuenta`),
  ADD KEY `idestado` (`idestado`);

--
-- Indices de la tabla `tbl_idioma`
--
ALTER TABLE `tbl_idioma`
  ADD PRIMARY KEY (`id`),
  ADD KEY `iso2` (`iso2`);

--
-- Indices de la tabla `tbl_imagenes`
--
ALTER TABLE `tbl_imagenes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo` (`tipo`);

--
-- Indices de la tabla `tbl_medio`
--
ALTER TABLE `tbl_medio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tbl_moneda`
--
ALTER TABLE `tbl_moneda`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tbl_obra`
--
ALTER TABLE `tbl_obra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idartista` (`idartista`),
  ADD KEY `idmedio` (`idmedio`),
  ADD KEY `idserie` (`idserie`);

--
-- Indices de la tabla `tbl_oferta`
--
ALTER TABLE `tbl_oferta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idartista` (`idartista`),
  ADD KEY `idcliente` (`idcliente`),
  ADD KEY `idadmin` (`idadmin`);

--
-- Indices de la tabla `tbl_roles`
--
ALTER TABLE `tbl_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orden` (`orden`);

--
-- Indices de la tabla `tbl_series`
--
ALTER TABLE `tbl_series`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idartista` (`idartista`);

--
-- Indices de la tabla `tbl_setup`
--
ALTER TABLE `tbl_setup`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tbl_textos`
--
ALTER TABLE `tbl_textos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `textos_uk` (`idartista`,`idioma`,`idtexto`,`idtipotexto`),
  ADD KEY `idtipotexto` (`idtipotexto`),
  ADD KEY `idtexto` (`idtexto`),
  ADD KEY `idioma` (`idioma`),
  ADD KEY `idartista` (`idartista`);

--
-- Indices de la tabla `tbl_timezone`
--
ALTER TABLE `tbl_timezone`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hora_idx` (`hora`);

--
-- Indices de la tabla `tbl_tipoTexto`
--
ALTER TABLE `tbl_tipoTexto`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `tbl_adminIdioma`
--
ALTER TABLE `tbl_adminIdioma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=315;

--
-- AUTO_INCREMENT de la tabla `tbl_artista`
--
ALTER TABLE `tbl_artista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_bitacora`
--
ALTER TABLE `tbl_bitacora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de la tabla `tbl_cliente`
--
ALTER TABLE `tbl_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_colArtistaAdmin`
--
ALTER TABLE `tbl_colArtistaAdmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT de la tabla `tbl_colArtistaIdioma`
--
ALTER TABLE `tbl_colArtistaIdioma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tbl_colImagenesObra`
--
ALTER TABLE `tbl_colImagenesObra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_colOfertaObra`
--
ALTER TABLE `tbl_colOfertaObra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_colSetupAdmin`
--
ALTER TABLE `tbl_colSetupAdmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `tbl_cuentaBanc`
--
ALTER TABLE `tbl_cuentaBanc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_curriculo`
--
ALTER TABLE `tbl_curriculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_debug`
--
ALTER TABLE `tbl_debug`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_ediciones`
--
ALTER TABLE `tbl_ediciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_estado`
--
ALTER TABLE `tbl_estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tbl_estadoPago`
--
ALTER TABLE `tbl_estadoPago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tbl_facturacion`
--
ALTER TABLE `tbl_facturacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_idioma`
--
ALTER TABLE `tbl_idioma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_imagenes`
--
ALTER TABLE `tbl_imagenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_medio`
--
ALTER TABLE `tbl_medio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `tbl_obra`
--
ALTER TABLE `tbl_obra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_oferta`
--
ALTER TABLE `tbl_oferta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_roles`
--
ALTER TABLE `tbl_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tbl_series`
--
ALTER TABLE `tbl_series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_setup`
--
ALTER TABLE `tbl_setup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tbl_textos`
--
ALTER TABLE `tbl_textos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_timezone`
--
ALTER TABLE `tbl_timezone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=426;

--
-- AUTO_INCREMENT de la tabla `tbl_tipoTexto`
--
ALTER TABLE `tbl_tipoTexto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD CONSTRAINT `tbl_admin_ibfk_1` FOREIGN KEY (`idrol`) REFERENCES `tbl_roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tbl_admin_ibfk_2` FOREIGN KEY (`ididioma`) REFERENCES `tbl_idioma` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tbl_artista`
--
ALTER TABLE `tbl_artista`
  ADD CONSTRAINT `tbl_artista_ibfk_1` FOREIGN KEY (`idimg`) REFERENCES `tbl_imagenes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tbl_bitacora`
--
ALTER TABLE `tbl_bitacora`
  ADD CONSTRAINT `tbl_bitacora_ibfk_2` FOREIGN KEY (`idadmin`) REFERENCES `tbl_admin` (`id`);

--
-- Filtros para la tabla `tbl_cliente`
--
ALTER TABLE `tbl_cliente`
  ADD CONSTRAINT `tbl_cliente_ibfk_1` FOREIGN KEY (`idartista`) REFERENCES `tbl_artista` (`id`);

--
-- Filtros para la tabla `tbl_colArtistaAdmin`
--
ALTER TABLE `tbl_colArtistaAdmin`
  ADD CONSTRAINT `tbl_colArtistaAdmin_ibfk_2` FOREIGN KEY (`idartista`) REFERENCES `tbl_artista` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `tbl_colArtistaAdmin_ibfk_3` FOREIGN KEY (`idadmin`) REFERENCES `tbl_admin` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tbl_colArtistaIdioma`
--
ALTER TABLE `tbl_colArtistaIdioma`
  ADD CONSTRAINT `tbl_colArtistaIdioma_ibfk_1` FOREIGN KEY (`idartista`) REFERENCES `tbl_artista` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_colArtistaIdioma_ibfk_2` FOREIGN KEY (`ididioma`) REFERENCES `tbl_idioma` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_colImagenesObra`
--
ALTER TABLE `tbl_colImagenesObra`
  ADD CONSTRAINT `tbl_colImagenesObra_ibfk_1` FOREIGN KEY (`idobra`) REFERENCES `tbl_obra` (`id`),
  ADD CONSTRAINT `tbl_colImagenesObra_ibfk_2` FOREIGN KEY (`idimagen`) REFERENCES `tbl_imagenes` (`id`);

--
-- Filtros para la tabla `tbl_colOfertaObra`
--
ALTER TABLE `tbl_colOfertaObra`
  ADD CONSTRAINT `tbl_colOfertaObra_ibfk_1` FOREIGN KEY (`idoferta`) REFERENCES `tbl_oferta` (`id`),
  ADD CONSTRAINT `tbl_colOfertaObra_ibfk_2` FOREIGN KEY (`idobra`) REFERENCES `tbl_obra` (`id`);

--
-- Filtros para la tabla `tbl_colSetupAdmin`
--
ALTER TABLE `tbl_colSetupAdmin`
  ADD CONSTRAINT `tbl_colSetupAdmin_ibfk_1` FOREIGN KEY (`idsetup`) REFERENCES `tbl_setup` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `tbl_colSetupAdmin_ibfk_2` FOREIGN KEY (`idadmin`) REFERENCES `tbl_admin` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tbl_cuentaBanc`
--
ALTER TABLE `tbl_cuentaBanc`
  ADD CONSTRAINT `tbl_cuentaBanc_ibfk_1` FOREIGN KEY (`idartista`) REFERENCES `tbl_artista` (`id`),
  ADD CONSTRAINT `tbl_cuentaBanc_ibfk_2` FOREIGN KEY (`idmoneda`) REFERENCES `tbl_moneda` (`id`);

--
-- Filtros para la tabla `tbl_curriculo`
--
ALTER TABLE `tbl_curriculo`
  ADD CONSTRAINT `tbl_curriculo_ibfk_1` FOREIGN KEY (`idartista`) REFERENCES `tbl_artista` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_curriculo_ibfk_2` FOREIGN KEY (`idadmin`) REFERENCES `tbl_admin` (`id`);

--
-- Filtros para la tabla `tbl_ediciones`
--
ALTER TABLE `tbl_ediciones`
  ADD CONSTRAINT `tbl_ediciones_ibfk_1` FOREIGN KEY (`idestado`) REFERENCES `tbl_estado` (`id`),
  ADD CONSTRAINT `tbl_ediciones_ibfk_2` FOREIGN KEY (`idobra`) REFERENCES `tbl_obra` (`id`),
  ADD CONSTRAINT `tbl_ediciones_ibfk_3` FOREIGN KEY (`idpago`) REFERENCES `tbl_estadoPago` (`id`),
  ADD CONSTRAINT `tbl_ediciones_ibfk_4` FOREIGN KEY (`idmoneda`) REFERENCES `tbl_moneda` (`id`);

--
-- Filtros para la tabla `tbl_facturacion`
--
ALTER TABLE `tbl_facturacion`
  ADD CONSTRAINT `tbl_facturacion_ibfk_2` FOREIGN KEY (`idcliente`) REFERENCES `tbl_cliente` (`id`),
  ADD CONSTRAINT `tbl_facturacion_ibfk_3` FOREIGN KEY (`idcuenta`) REFERENCES `tbl_cuentaBanc` (`id`),
  ADD CONSTRAINT `tbl_facturacion_ibfk_5` FOREIGN KEY (`idedicion`) REFERENCES `tbl_ediciones` (`id`),
  ADD CONSTRAINT `tbl_facturacion_ibfk_6` FOREIGN KEY (`idestado`) REFERENCES `tbl_estadoPago` (`id`);

--
-- Filtros para la tabla `tbl_obra`
--
ALTER TABLE `tbl_obra`
  ADD CONSTRAINT `tbl_obra_ibfk_1` FOREIGN KEY (`idartista`) REFERENCES `tbl_artista` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_obra_ibfk_5` FOREIGN KEY (`idmedio`) REFERENCES `tbl_medio` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tbl_obra_ibfk_6` FOREIGN KEY (`idserie`) REFERENCES `tbl_series_ibfk_1` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tbl_oferta`
--
ALTER TABLE `tbl_oferta`
  ADD CONSTRAINT `tbl_oferta_ibfk_1` FOREIGN KEY (`idartista`) REFERENCES `tbl_artista` (`id`),
  ADD CONSTRAINT `tbl_oferta_ibfk_2` FOREIGN KEY (`idadmin`) REFERENCES `tbl_admin` (`id`),
  ADD CONSTRAINT `tbl_oferta_ibfk_3` FOREIGN KEY (`idcliente`) REFERENCES `tbl_cliente` (`id`);

--
-- Filtros para la tabla `tbl_series`
--
ALTER TABLE `tbl_series`
  ADD CONSTRAINT `tbl_series_ibfk_1` FOREIGN KEY (`idartista`) REFERENCES `tbl_artista` (`id`);

--
-- Filtros para la tabla `tbl_textos`
--
ALTER TABLE `tbl_textos`
  ADD CONSTRAINT `tbl_textos_ibfk_1` FOREIGN KEY (`idartista`) REFERENCES `tbl_artista` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tbl_textos_ibfk_2` FOREIGN KEY (`idtipotexto`) REFERENCES `tbl_tipoTexto` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tbl_textos_ibfk_3` FOREIGN KEY (`idioma`) REFERENCES `tbl_idioma` (`iso2`) ON DELETE NO ACTION ON UPDATE NO ACTION;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`%` EVENT `ejemplo` ON SCHEDULE EVERY 5 MINUTE STARTS '2017-09-26 12:39:48' ENDS '2017-10-26 14:00:00' ON COMPLETION PRESERVE DISABLE DO begin
-- insert into tbl_debug (nombre, valor) values ('la fecha', now());
end$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
