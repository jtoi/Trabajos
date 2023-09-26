delimiter ;;

drop procedure if EXISTS pr_resultDatos;;
create procedure pr_resultDatos ()
begin

	declare dias int(11) default 1;

	if (select dayofweek(current_date) = 2) then
		set dias = 3;
	end if;

	#drop table if exists resultDatos;
	
	set time_zone='Europe/Madrid';
	insert into resultDatos select t.idtransaccion, t.idcomercio, t.identificador, t.codigo, t.pasarela, t.tipoOperacion, t.idioma, t.fecha, t.fecha_mod, t.valor, t.valor_inicial, t.tipoEntorno, t.moneda, t.estado, t.estadoP, t.sesion, t.ip, t.tasa, t.euroEquiv, t.pago, t.tasaDev, t.euroEquivDev, t.solDev, t.amenaza, t.repudiada, t.fechaPagada, t.tpv, t.idpais, t.estadoAMF, t.tarjetas, t.identificadorBnco, t.id_tarjeta, t.mtoMonBnc, t.carDevCom, p.idbanco, p.idempresa, null
		from tbl_transacciones t, tbl_pasarela p
		where p.idPasarela = t.pasarela
			and t.fecha between unix_timestamp(date_sub(current_date, interval 1 day)) and unix_timestamp(date_sub(current_date, interval 0 day));

	UPDATE resultDatos SET fecha_act = FROM_UNIXTIME(fecha);
	set time_zone='America/Havana';

end;;


drop event if exists ev_llenaResultDatos;;
CREATE EVENT ev_llenaResultDatos
ON SCHEDULE EVERY '1' DAY STARTS '2019-04-10 07:00:00' ON COMPLETION PRESERVE
ENABLE COMMENT 'Llena la tabla resultDatos para enviarla a Jenny' DO
begin
	call pr_resultDatos();
end;;

DELIMITER ;
