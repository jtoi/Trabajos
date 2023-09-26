DELIMITER ;;
DROP FUNCTION IF EXISTS `fn_entrada`;;
CREATE FUNCTION `fn_entrada`(`md` varchar(64)) RETURNS varchar(200) CHARSET utf8 COLLATE utf8_spanish_ci
    READS SQL DATA
    DETERMINISTIC
RETURN (SELECT concat (r.orden, '|', a.id, '|', a.email, '|', a.nombre, '|', a.idrol) FROM tbl_admin a, tbl_roles r WHERE a.idrol = r.id and a.md5 = sha2(concat(md,a.email,'lo importante es invisible para los ojos'),256) and a.activo = 1);;

DROP FUNCTION IF EXISTS `split_string`;;
CREATE FUNCTION `split_string`(str VARCHAR(255), delim VARCHAR(12), pos INT) RETURNS varchar(255) CHARSET utf8 COLLATE utf8_spanish_ci
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(str, delim, pos),
       LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1),
       delim, '');;

DROP PROCEDURE IF EXISTS `valida_usuario`;;
CREATE PROCEDURE `valida_usuario`(IN `md` varchar(64), IN `ipent` varchar(20))
    READS SQL DATA
BEGIN
  declare idusr int default 0;
  declare idartista int default 0;
  declare seudonimoA varchar(100) default 'Varios';
  declare nombreU varchar(100);
  declare email varchar(150);
  declare Ordenrol int;
  declare idrol int;
  declare done tinyint DEFAULT 0;
  declare datosUsr varchar(200);

  
  declare cur_login CURSOR for select r.id, r.seudonimo from tbl_admin a, tbl_artista r, tbl_colArtistaAdmin c where a.md5 = sha2(concat(md,a.email,'lo importante es invisible para los ojos'),256) and a.id = c.idadmin and c.idartista = r.id and a.activo = 1;
  declare CONTINUE HANDLER FOR NOT FOUND SET done=1;


	set datosUsr = fn_entrada(md);
	set idusr = split_string(datosUsr, '|', 2);
	set nombreU = split_string(datosUsr, '|', 4);
	set Ordenrol = split_string(datosUsr, '|', 1);
	set email = split_string(datosUsr, '|', 3);
	set idrol = split_string(datosUsr, '|', 5);

insert into tbl_debug (nombre,valor) values ('datosUsr', datosUsr);
insert into tbl_debug (nombre,valor) values ('usuario', usuario);
insert into tbl_debug (nombre,valor) values ('id', idusr);
insert into tbl_debug (nombre,valor) values ('nombreU', nombreU);
insert into tbl_debug (nombre,valor) values ('Ordenrol', Ordenrol);
insert into tbl_debug (nombre,valor) values ('email', email);

  #busca los datos del usuario conectado
  if (Ordenrol > 5) then
    open cur_login;
    cursor_loop:LOOP
      fetch cur_login into idartista, seudonimoA;

      IF done=1 THEN
        LEAVE cursor_loop;
      END IF;
    end LOOP;
    CLOSE cur_login;
  end if;
    
    #salva la conexión
    if idusr is NOT null THEN
      update tbl_admin set fecha_visita = unix_timestamp(), ip = ipent where id = idusr;
    end IF;
    
    #realiza la salida
  select idusr, idartista, email, Ordenrol, idartista, seudonimoA, idrol, nombreU;
  
end;;

DELIMITER ;
