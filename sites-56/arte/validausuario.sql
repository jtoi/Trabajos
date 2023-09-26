/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  julio
 * Created: Dec 9, 2017
 */

DELIMITER ;;

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

insert into tbl_debug (nombre,valor) values ('datosUsr', datosUsr);
insert into tbl_debug (nombre,valor) values ('nombreU', nombreU);
insert into tbl_debug (nombre,valor) values ('id', idusr);
insert into tbl_debug (nombre,valor) values ('email', email);
insert into tbl_debug (nombre,valor) values ('idioma', split_string(datosUsr, '|', 6));

#busca los datos del usuario conectado
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
  
#insert into tbl_debug (nombre,valor) values ('imagen', imagen);
#insert into tbl_debug (nombre,valor) values ('sql', query);
    
    #salva la conexión
    if idusr is NOT null THEN
      update tbl_admin 
      set fecha_visita = unix_timestamp(), 
       ip = ipent 
      where id = idusr;
    end IF;
  
end;;

DELIMITER ;