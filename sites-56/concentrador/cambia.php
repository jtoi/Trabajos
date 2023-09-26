<!DOCTYPE html>
	<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>Envio de querys</title>
<style>
<!--
body {
	font-size: 10px;
	font-family: sans-serif;
	background-color:#23232d;color:#fff38f;
}
div {
	float: left;
}
.largo{
	width: 100%;
	margin: 5px 0;
}
.largo div {
	width: 415px;
}
-->
</style>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".quer").click(function(){
		if($(this).prop('checked')) {//alert($("#text").val().length);
			if ($("#text").val().length == 0) {
				$("#text").val($("#text").val()+$(this).val());
			} else {
				$("#text").val($("#text").val()+';'+"\n"+$(this).val());
			}
		}
	});
});
</script>
</head>
<?php
define( '_VALID_ENTRADA', 1);
include('configuration.php');

$d = $_POST;
$difHora = 7;
echo "<br>";
echo "horaEsp = ".date('d/m/Y H:i:s')."<br>";
echo "horaCuba = ".date('d/m/Y H:i:s', time()-_DIF_HOR*60*60)." - ".time()."<br>";
echo "<br>";
$cadenarand = bin2hex(openssl_random_pseudo_bytes(20));
echo "cadenarand=$cadenarand<br>";

$diasMesAct = date("t", strtotime(date("Y") . "-" . date("m") . "-01"));

$iniMes = mktime(0,0,0,date('m'),1,date('Y'));
$iniSemana = mktime(0,0,0,date('m'),date('d')-7,date('Y'));
$Mesatras = mktime(0,0,0,date('m'),date('d')-30,date('Y'));
$iniDia = mktime(0,0,0,date('m'),date('d'),date('Y'));
//$iniDia = 1422507600;/*Borrar!!!!*/
$ini13A = mktime(0,0,0,date('m')-13,1,date('Y'));
$iniAno = mktime(0,0,0,1,1,date('Y'));
$ini24h = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
$horCorr = time()-$difHora*60*60;
$hor3pm = mktime(15,0,0,date('m'),date('d'),date('Y'));
$hoy = date('d').'/'.date('m').'/'.date('Y');
$estaHora = date('d').'/'.date('m').'/'.date('Y')." ".date('H');
$ahora = time();
$hora1Ant = time()-(60*60*1);
$hora2Ant = time()-(60*60*2);

echo "Inicio del d&iacute;a = $iniDia<br />";
echo "Inicio del mes = $iniMes<br />";
echo "Inicio del a&ntilde;o = $iniAno<br />";
echo "Desde hace 7 d&iacute;as= $iniSemana<br />";
echo "Desde hace 13 meses = $ini13A<br />";
echo "Hace 24 Hrs = $ini24h<br />";
echo "A las 3pm HE de hoy = $hor3pm<br />";
echo "Ahora mismo = $ahora<br />";
echo "<br>";

if (is_array($d) && count($d) > 0) {
	$sql = $d['pruebas'];
	$sql = str_replace("\n", " ", $sql);
	$sql = str_replace("\n\r", " ", $sql);
	$sql = str_replace("\r", " ", $sql);
	$sql = str_replace("	", "", $sql);
	$time = time();
	$firm = sha1($time.$sql.'Lo que los hace hermoso es algo invisible...los ojos no siempre ven. Hay que buscar con el corazon.');

//	$ch = curl_init();
	($d['lugar'] == 'alla') ? $url = _ESTA_URL.'/ejec.php' : $url = _ESTA_URL."/ejec.php";
//	curl_setopt($ch, CURLOPT_URL,$url);
//	curl_setopt($ch, CURLOPT_POST, 1);
//	curl_setopt($ch, CURLOPT_POSTFIELDS, "var=$time&sql=$sql&cod=$firm");
//
//	curl_exec ($ch);
//	curl_close ($ch);
//	echo $sql;
?>
<form method="post" name="envia" action="<?php echo $url; ?>" >
	<input type="hidden" name="var" value="<?php echo $time; ?>" />
	<input type="hidden" name="sql" value="<?php echo $sql; ?>" />
	<input type="hidden" name="cod" value="<?php echo $firm; ?>" />
	<input type="hidden" name="rep" value="<?php echo $d['repite']; ?>" />
	<!-- <input type="submit" value="envia" /> -->
</form>
<script language='javascript'>
	document.envia.submit();
</script>

<?php

echo "<br>";
echo "Inicio del d&iacute;a = $iniDia<br />";
echo "Inicio del mes = $iniMes<br />";
echo "Inicio del a&ntilde;o = $iniAno<br />";
echo "Desde hace 13 meses = $ini13A<br />";
echo "Hace 24 Hrs = $ini24h<br />";
echo "A las 3pm HE de hoy = $hor3pm<br />";
echo "<br>";

}
//$elem = "case t.estado when 'A' then euroEquiv else (euroEquiv - euroEquivDev) end";
$elem = "case t.estado
		when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
		when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
		when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
		when 'A' then (t.valor/100/t.tasa)
		else '0.00' end";

?>
<body>
<h4>Esta p&aacute;gina env&iacute;a sql al concentrador para su procesamiento all&aacute;</h4>
<form name="envAdm" method="post">
<div class="largo">
	<input type="radio" value="aca" id="acaL" name="lugar" /> <label for="acaL">Env&iacute;o de la Query local</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" value="alla" id="allaL" name="lugar" checked="checked" /> <label for="allaL">Env&iacute;o de la query Internet</label>
</div>
<div class="largo">
	<input type="text"id="repite" name="repite" value="0" /> Min para recargar la query autom&aacute;ticamente
</div>
<div class="largo">
	<div class="uno">
		<input type="radio" class="quer"
			value="UPDATE `tbl_admin` SET `md5` = '7506f89cd748cc5a53484815dc36c4088f1bfec9', fechaPass = unix_timestamp() WHERE `idadmin` = 10"
			id="ContrJTs" name="prueba" /> <label for="ContrJTs">Restablece mi contrase&ntilde;a</label><br />
		<input type="radio" class="quer"
			value="UPDATE `tbl_admin` SET `md5` = '4b60fb6818238e0b319b49c321181b7881671081', fechaPass = unix_timestamp() WHERE `idadmin` = '438'"
			id="ContrJTs" name="prueba" /> <label for="ContrJTs">Restablece la contraseña de uno</label><br />
		<input type="radio" class="quer"
			value="update tbl_ipbloq set bloqueada = 0, fecha_desbloq = unix_timestamp(), desbloq_por = 10
where ip in ('%') and bloqueada = 1 and idComercio like '%'"
			id="IPBloq" name="prueba" /> <label for="IPBloq">Desbloquear IPs</label><br />
		<input type="radio" class="quer"
			value="insert into tbl_ipblancas (ip, fecha, idAdmin, idComercio) values ('', unixtimestamp(), 10, '')"
			id="IPBloq" name="prueba" /> <label for="IPBloq">Pone IP blanca</label><br />
		<input type="radio" class="quer"
			value="select t.idtransaccion id,
	concat(c.nombre,'<br>',c.idcomercio) comercio,
	r.cliente,
	r.concepto,
	concat(case t.estado
		when 'P' then 'En Proceso'
		when 'A' then 'Aceptada'
		when 'D' then 'Denegada'
		when 'N' then 'No Procesada'
		when 'B' then 'Anulada' else 'Devuelta'
		end,t.estado )estad,
	from_unixtime(t.fecha,'%d/%m/%Y %H:%i:%s')fec,
	from_unixtime(t.fecha_mod,'%d/%m/%Y %H:%i:%s') fech_mod,
	format((t.valor_inicial / 100),2) valIni,
	format({elem}, 2) valor,
	round(t.tasa,4) tasaM,
	format({elem}, 2) euroEquiv,
	concat(m.moneda, t.moneda) moned,
	concat(p.nombre,t.pasarela) pasarelaN,
	vista, enviada
from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_pasarela p, tbl_transferencias r
where r.idTransf = t.idtransaccion
	and c.idcomercio = t.idcomercio
	and t.moneda = m.idmoneda
	and p.idPasarela = t.pasarela
	and p.tipo = 'T'
	and t.fecha > {Mesatras} and t.idcomercio like '%%' order by t.fecha desc"
			id="TRF" name="prueba" /> <label for="TRF">Transferencias</label><br />
		<input type="radio" class="quer" value="select p.idPasarela, p.nombre, c.idmoneda, c.terminal, c.clave, c.comercio
	from tbl_pasarela p, tbl_colPasarMon c
	where p.idPasarela = c.idpasarela
		and p.nombre like '%%' and c.comercio like '%%'"
			id="PasD" name="prueba" /> <label for="PasD">Buscar datos de la pasarela</label><br />
		<input type="radio" class="quer" value="select t.id_reserva, t.id_transaccion, concat(c.nombre, '<br>', t.id_comercio) comerc,
				concat(a.nombre, '<br>', t.id_admin) adminstr, t.est_comer, t.codigo, concat(p.nombre, '<br>', t.pasarela) pasarl, t.nombre,
				t.email, t.servicio, t.valor_inicial, t.valor, t.moneda, from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') fec, t.bankId,
				from_unixtime(t.fechaPagada, '%d/%m/%Y %H:%i:%s') 'fecha pagada', t.pMomento, t.estado, t.ventas, t.amex
				from tbl_pasarela p, tbl_reserva t, tbl_comercio c, tbl_admin a
				where t.pasarela = p.idPasarela and t.id_comercio = c.idcomercio and t.id_admin = a.idadmin
				order by t.fecha desc limit 0,20"
			id="ReesD" name="prueba" /> <label for="ReesD">Tabla reservas</label><br />
		<input type="radio" class="quer"
			value="select count(id) 'Cant. Beneficiarios' from tbl_aisBeneficiario;
select id, idcimex, idtitanes, concat(nombre,' ',papellido,' ',sapellido) benef, telf, numDocumento,
	from_unixtime(fecha, '%d/%m/%Y %H:%i:%s') fec
from tbl_aisBeneficiario
order by idcimex desc
limit 0,100"
			id="BeneficiAis" name="prueba" /> <label for="BeneficiAis">Beneficiarios de AISRemesas</label><br />
		<input type="radio" class="quer"
			value="select distinct concat(c.nombre, ' ', c.papellido) 'Cliente',  c.idcimex, c.usuario, c.correo,
	(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum'
from  tbl_aisCliente c,   tbl_aisClienteBeneficiario r, tbl_aisBeneficiario b
where  r.idcliente = c.id and r.idbeneficiario = b.id and c.idtitanes is not null
	and b.idtitanes is not null
order by c.idcimex desc"
			id="ContrJT" name="prueba" /> <label for="ContrJT">Clientes AISRemesas listos para pagar</label><br />
		<input type="radio" class="quer"
			value="select c.id cliente, f.id fic, c.idcimex, c.idtitanes, c.usuario, c.nombre, c.papellido, f.fichero,
	case f.subido when 1 then 'Subido-1' else 'No-0' end subio
from tbl_aisCliente c, tbl_aisFicheros f
where c.id = f.idcliente
order by c.idcimex desc"
			id="ContrPr" name="prueba" /> <label for="ContrPr">Ver los ficheros subidos por cliente de AISRemesas</label><br />
		<input type="radio" class="quer"
			value="SELECT t.idtransaccion, FROM_UNIXTIME(t.fecha), c.id 'idCliente', c.idcimex 'cli Cimex', c.idtitanes 'cli Titanes', concat (c.nombre, ' ', c.papellido, ' ', c.sapellido) 'Cliente', c.usuario, b.idcimex 'ben Cimex', b.idtitanes 'ben Titanes', concat (b.nombre, ' ', b.papellido, ' ', b.sapellido) 'Beneficiario', b.id 'idBenef'
FROM tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b
WHERE t.id_error LIKE '%Beneficiary%' 
	and t.idtransaccion = o.idtransaccion
	and c.id = o.idcliente
	and b.id = o.idbeneficiario
ORDER BY t.fecha_mod DESC"
			id="ErrrBen" name="prueba" /> <label for="ErrrBen">Ver operaciones Clientes y Beneficiarios con error de beneficiario</label><br />
		<input type="radio" class="quer" id="TodoAis" name="prueba" value="update tbl_aisCliente set activo = 1 where idcimex in (1077);
		select concat(c.nombre,' ',c.id,'-',t.idcomercio) comercio,
	format(sum({elem}),2) 'Valor',
	count(t.idtransaccion) Transacc,
	format(sum({elem}) / count(*), 2) 'val/trans',
	format(count(t.idtransaccion) *100/(select count(n.idtransaccion)
		from tbl_transacciones n
		where n.tipoEntorno = 'P'
			and n.fecha_mod > '{iniDia}'
			and t.idcomercio = n.idcomercio),2) '% Acep' ,
	format((select count(j.idtransaccion )
		from tbl_transacciones j
		where j.idcomercio = t.idcomercio
			and j.tipoEntorno = 'P'
			and j.estado in ('A')
	and fecha_mod > '{Mesatras}' ) *100/(select count(i.idtransaccion)
		from tbl_transacciones i
		where i.tipoEntorno = 'P'
			and i.fecha_mod > '{Mesatras}'
			and t.idcomercio = i.idcomercio),2) '% -30'
FROM tbl_comercio c, tbl_transacciones t
where t.idcomercio = c.idcomercio
	and t.estado in ('A')
	and t.tipoEntorno = 'P'
	and fecha_mod > '{iniDia}'
	and t.idcomercio = '527341458854'
group by t.idcomercio
order by sum(case t.estado when 'A' then euroEquiv else euroEquivDev end) desc;
		select (select count(id) from tbl_aisCliente) 'Cant. Clientes',
(select count(id) from tbl_aisCliente where activo = 1) 'Cant. Clientes Activos',
((select count(id) from tbl_aisCliente where activo = 1)/(select count(id) from tbl_aisCliente)) '% Activ.',
(select count(id) from tbl_aisCliente where activo = 0) 'Cant. Clientes NoActivos',
((select count(id) from tbl_aisCliente where activo = 0)/(select count(id) from tbl_aisCliente)) '% NoActiv.',
(select count(distinct o.idcliente) from tbl_aisOrden o, tbl_transacciones t where t.idtransaccion = o.idtransaccion) 'Clientes<br>intentado',
(select count(distinct o.idcliente) from tbl_aisOrden o, tbl_transacciones t where t.idtransaccion = o.idtransaccion and t.estado in ('A','B','V')) 'Clientes<br>pagado';
select t.idtransaccion, t.identificador, o.titOrdenId, format((t.valor_inicial/100),2) 'valor',
	t.moneda, c.idcimex 'IdCli', concat(c.nombre, ' ', c.papellido) cliente, c.usuario, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido) beneficiario,
	from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') 'fecha', from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i:%s') 'fechaMod', t.estado, t.id_error as error,
     case t.estado when 'A' then (case o.subida when '1' then 'Si' else 'No' end) else '-' end 'Confirm. Titanes'
from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b
where t.idtransaccion = o.idtransaccion
	and b.id = o.idbeneficiario
	and c.id = o.idcliente
order by t.fecha_mod desc
limit 0,200;
select id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario,
	correo, numDocumento, from_unixtime(fecha, '%d/%m/%Y %H:%i:%s') fec, from_unixtime(fechaAltaCimex, '%d/%m/%Y %H:%i:%s') 'fecCimex',
	telf1, (select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais,
	case activo when 1 then 'Activo' else 'Desactivado' end 'activo?',
	(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum',
	(select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf'
from tbl_aisCliente c
order by fecha desc
limit 0,200"
			id="borraP" name="prueba" /> <label for="TodoAis">Todo de AISRemesas</label><br />
		<input type="radio" class="quer" id="ActvCli" name="prueba" value="update tbl_aisCliente set activo = 1 where idcimex in (1077)" /> <label for="ActvCli">Activa Clientes de AIS</label><br />
		<input type="radio" class="quer" value="select (select count(id) from tbl_aisCliente) 'Cant. Clientes',
(select count(id) from tbl_aisCliente where activo = 1) 'Cant. Clientes Activos',
((select count(id) from tbl_aisCliente where activo = 1)/(select count(id) from tbl_aisCliente)) '% Activ.',
(select count(distinct o.idcliente) from tbl_aisOrden o, tbl_transacciones t where t.idtransaccion = o.idtransaccion) 'Clientes<br>intentado',
(select count(distinct o.idcliente) from tbl_aisOrden o, tbl_transacciones t where t.idtransaccion = o.idtransaccion and t.estado in ('A','B','V')) 'Clientes<br>pagado';
select id, idtitanes, concat(nombre,' ',papellido,' ',sapellido) cliente, idcimex, usuario,
	correo, numDocumento, from_unixtime(fecha, '%d/%m/%Y %H:%i:%s') fec, from_unixtime(fechaAltaCimex, '%d/%m/%Y %H:%i:%s') 'fecCimex',
	telf1, (select p.nombre from tbl_paises p where c.paisResidencia = p.id) pais,
	case activo when 1 then 'Activo' else 'Desactivado' end 'activo?',
	(select count(*) from tbl_aisFicheros f where f.idcliente = c.id) 'Docum',
	(select count(id) from tbl_aisClienteBeneficiario where c.id = idcliente) 'CantBenf'
from tbl_aisCliente c
order by fecha desc
limit 0,200"
			id="ClientesAis" name="prueba" /> <label for="ClientesAis">Cliente de AISRemesas</label><br />
		<input type="radio" class="quer" value="select t.idtransaccion, t.identificador, o.titOrdenId, format((t.valor_inicial/100),2) 'valor',
	t.moneda, c.idcimex 'IdCli', concat(c.nombre, ' ', c.papellido) cliente, c.usuario, b.idcimex 'IdBen', concat(b.nombre, ' ', b.papellido) beneficiario,
	from_unixtime(t.fecha, '%d/%m/%Y %H:%i:%s') 'fecha', from_unixtime(t.fecha_mod, '%d/%m/%Y %H:%i:%s') 'fechaMod', t.estado, t.id_error as error,
     case t.estado when 'A' then (case o.subida when '1' then 'Si' else 'No' end) else '-' end 'Confirm. Titanes'
from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b
where t.idtransaccion = o.idtransaccion
	and b.id = o.idbeneficiario
	and c.id = o.idcliente
	and t.identificador like '%%'
	and o.titOrdenId like '%%'
order by t.fecha_mod desc
limit 0,100"
			id="OPrrfnx" name="prueba" /> <label for="OPrrfnx">Operaciones Fincimex</label><br />
		<input type="radio" class="quer"
			value="select
     c.idcimex 'idCliente',
     c.usuario 'Usuario',
     concat(c.nombre,' ',c.papellido) 'Cliente',
     b.idcimex 'idBenef',
     concat(b.nombre,' ',b.papellido) 'Benefic'
from
    tbl_aisCliente c,
    tbl_aisBeneficiario b,
    tbl_aisClienteBeneficiario r
where b.id = r.idbeneficiario
    and r.idcliente = c.id
order by (c.idcimex *1) desc"
			id="borraP" name="prueba" /> <label for="borraP">Ver los Beneficiarios de cada Cliente de AISRemesas</label><br />
		<input type="radio" class="quer"
			value="update tbl_transacciones t, tbl_aisOrden o set t.valor = t.valor_inicial, t.id_error = '', t.tasa = 1, t.estado = 'A', o.titOrdenId = '222', t.euroEquiv = t.valor_inicial/t.tasa/100, t.fecha_mod = unix_timestamp() where t.idtransaccion = o.idtransaccion and t.idtransaccion = '555'"
			id="camb703" name="prueba" /> <label for="camb703">Poner Aceptada la operaci&oacute;n con Error 703 </label><br />
		<input type="radio" class="quer"
			value="UPDATE tbl_aisCliente SET fecha = '<?php echo $ahora; ?>', subfichero = '1' WHERE usuario = 'aaa';
delete from tbl_aisFicheros where idcliente = (select id from tbl_aisCliente where usuario = 'aaa')"
			id="ficsais" name="prueba" /> <label for="ficsais">Poner para que un Cliente vuelva a subir sus ficheros</label><br />
		<input type="radio" class="quer"
			value="UPDATE tbl_aisCliente SET subfichero = '0' WHERE usuario = '******';"
			id="subicero" name="prueba" /> <label for="subicero">Evita los intentos de subida de ficheros vac&iacute;os por usuario</label><br />
	</div>
	<div class="dos">
		<input type="radio" class="quer"
			value="OPTIMIZE TABLE `tbl_transacciones`;
					OPTIMIZE TABLE `tbl_transferencias`;
					OPTIMIZE TABLE `tbl_baticora`;
					OPTIMIZE TABLE `tbl_reserva`;
					OPTIMIZE TABLE `tbl_admin`;
					OPTIMIZE TABLE `tbl_comercio`;
					OPTIMIZE TABLE `tbl_traza`"
			id="optim" name="prueba" /> <label for="optim">Optimiza las tablas traza, bit&aacute;cora, transacciones, transferencias, reserva, admin y comercio</label><br />
		<input type="radio" class="quer"
			value="update tbl_comercio set estado = 'P' where nombre = 'Prueba'"
			id="pruebaP" name="prueba" /> <label for="pruebaP">Pone a Prueba en Producci&oacute;n</label><br />
		<input type="radio" class="quer"
			value="update tbl_comercio set estado = 'D' where nombre = 'Prueba'"
			id="pruebaD" name="prueba" /> <label for="pruebaD">Pone a Prueba en Desarrollo</label><br />
		<input type="radio" class="quer"
			value="select format(sum({elem}) / <?php echo date('j'); ?> * , 2) 'Estimado'
FROM tbl_transacciones t
where estado in ('A','V')
	and tipoEntorno = 'P'
	and fecha_mod > '{iniMes}' "
			id="estMes" name="prueba" /> <label for="estMes">Estimado en el mes</label><br />
		<input type="radio" class="quer"
			value="select from_unixtime( fecha_mod, '%d/%m/%y - %W' ) as 'D&iacute;a', format(sum({elem}),2) 'Valor', count(*) Transacc,
	format(sum({elem}) / count(*), 2) 'val/trans'
FROM tbl_transacciones t
where estado in ('A','V')
	and tipoEntorno = 'P'
	and fecha_mod > '{iniMes}'
GROUP BY from_unixtime( fecha_mod, '%d/%m/%y' )
ORDER BY fecha_mod desc"
			id="montTr" name="prueba" /> <label for="montTr">Monto de transacciones</label><br />
		<input type="radio" class="quer"
			value="select date_add(from_unixtime(fecha_mod,'%Y-%m-01 23:59'), interval -1 day) as 'Mes', format(sum({elem}),2) 'Valor',
	count(*) Transacc, format(sum({elem})/count(*), 2) 'val/trans'
FROM tbl_transacciones t
where estado in ('A','V')
	and tipoEntorno = 'P'
	and fecha_mod > '{ini13A}'
 group by date_add(date_add(from_unixtime(fecha_mod,'%Y-%m-01 23:59'), interval -1 day), interval 1 month)
 order by fecha_mod desc"
			id="sumEq" name="prueba" /> <label for="sumEq">Suma valor equivalente del mes</label><br />
		<input type="radio" class="quer"
			value="select date_add(from_unixtime(fecha_mod,'%Y-%m-01 23:59'), interval -1 day) as 'Mes', format(sum({elem}),2) 'Valor',
	count(*) Transacc, format(sum({elem})/count(*), 2) 'val/trans'
FROM tbl_transacciones t where estado in ('A','V') and tipoEntorno = 'P' and fecha_mod > '{iniAno}'
group by date_add(date_add(from_unixtime(fecha_mod,'%Y-%m-01 23:59'), interval -1 day), interval 1 month) desc with rollup"
			id="sumEqA" name="prueba" /> <label for="sumEqA">Suma valor equivalente del a&ntilde;o</label><br />
		<input type="radio" class="quer"
			value="select estado, count(idtransaccion) cantidad
from (select estado, idtransaccion from tbl_transacciones where estado in ('A','V')  and fecha_mod > '{iniDia}' and tipoEntorno = 'P'
union all select estado, idtransaccion from tbl_transacciones where estado != 'A' and fecha_mod > '{iniDia}' and tipoEntorno = 'P') c
group by c.estado"
			id="transDiaAcep" name="prueba" /> <label for="transDiaAcep">Estado de transacciones en el d&iacute;a</label><br />
		<input type="radio" class="quer"
			value="select estado, count(idtransaccion) cantidad
from (select estado, idtransaccion from tbl_transacciones where estado in ('A','V')  and fecha_mod > '{iniMes}' and tipoEntorno = 'P'
union all select estado, idtransaccion from tbl_transacciones where estado != 'A' and fecha_mod > '{iniMes}' and tipoEntorno = 'P') c
group by c.estado"
			id="transMAcep" name="prueba" /> <label for="transMAcep">Estado de transacciones en el mes</label><br />
		<input type="radio" class="quer"
			value="select c.nombre, format(sum({elem}),2) 'Valor', count(*) Transacc, format(sum({elem}) / count(*), 2) 'val/trans'
FROM tbl_comercio c, tbl_transacciones t
where t.idcomercio = c.idcomercio and t.estado in ('A','V') and t.tipoEntorno = 'P' and fecha_mod > '{iniDia}'
group by t.idcomercio order by sum(case t.estado when 'A' then euroEquiv else euroEquivDev end) desc"
			id="TraComD" name="prueba" /> <label for="TraComD">Valor de transacciones del d&iacute;a por comercio</label><br />
		<input type="radio" class="quer"
			value="select c.nombre, format(sum({elem}),2) 'Valor', count(*) Transacc, format(sum({elem}) / count(*), 2) 'val/trans'
FROM tbl_comercio c, tbl_transacciones t
where t.idcomercio = c.idcomercio and t.estado in ('A','V') and t.tipoEntorno = 'P' and fecha_mod > '{iniMes}'
group by t.idcomercio order by sum(case t.estado when 'A' then euroEquiv else euroEquivDev end) desc"
			id="TraComM" name="prueba" /> <label for="TraComM">Valor de transacciones del mes por comercio</label><br />
		<input type="radio" class="quer"
			value="select idtransaccion, format(valor/100,2) monto, moneda, estado, ip, from_unixtime(fecha_mod,'%d/%m/%Y %H:%i') fec
from tbl_transacciones where ip = '' and estado = 'A'
order by fecha_mod desc"
			id="TraPIP" name="prueba" /> <label for="TraPIP">Buscar transacciones por IP</label><br />
		<input type="radio" class="quer"
               value="select a.nombre, from_unixtime(b.fecha,'%d/%m/%Y %H:%i:%s') fech, b.texto
from tbl_baticora b, tbl_admin a
where a.idadmin = b.idadmin and a.nombre like '%%' and texto like '%%'
	and a.login like '%%' and from_unixtime(b.fecha,'%d/%m/%Y') like '{hoy}'
order by b.fecha desc"
			id="DatBita" name="prueba" /> <label for="DatBita">Buscar datos en la bitacora</label><br />
		<input type="radio" class="quer"
               value="select from_unixtime(fecha,'%d/%m/%Y %H:%i:%s') fec, titulo, traza from
tbl_traza where from_unixtime(fecha,'%d/%m/%Y %H') like '%' and titulo like '%%' and traza like '%%'
order by fecha desc limit 0,30;"
			id="DatOper" name="prueba" /> <label for="DatOper">Trazas</label><br />
		<input type="radio" class="quer"
               value="select moneda, from_unixtime(fecha, '%d/%m/%y') fec, greatest(visa, bce, bnc, xe) cambio, tur
from tbl_cambio where from_unixtime(fecha, '%d%m%y') = '' and moneda like '%'"
			id="Tasas" name="prueba" /> <label for="Tasas">Tasas de cambio</label><br />
		<input type="radio" class="quer"
               value="select @rownum:=@rownum+1 'No.', c.nombre 'comercio', from_unixtime(d.fecha, '%d/%m/%Y %H:%i') 'fecha sol.',
	t.idtransaccion, t.identificador, t.codigo, from_unixtime(t.fecha, '%d/%m/%Y %H:%i') 'fecha oper.', t.valor_inicial/100 'valor Ini',
	d.valorDev 'a devolver', m.moneda, a.nombre 'solicitada por', concat('<a href=\'mailto:',a.email,'\' >',a.email,'</a>') correo,
	p.nombre 'pasarela'
from (SELECT @rownum:=0) r, tbl_transacciones t, tbl_devoluciones d, tbl_admin a, tbl_pasarela p, tbl_comercio c, tbl_moneda m
where t.moneda = m.idmoneda
	and c.idcomercio = t.idcomercio
	and p.idPasarela = t.pasarela
	and t.idtransaccion = d.idtransaccion
	and t.tipoEntorno = 'P'
	and d.idadmin = a.idadmin
	and d.fechaDev = 0
order by d.fecha desc"
			id="Devol" name="prueba" /> <label for="Devol">Devoluciones sin procesar</label><br />
		<input type="radio" class="quer"
               value="select t.idtransaccion, t.codigo, t.identificador, a.nombre 'solicitada por', a.email,
	from_unixtime(d.fecha, '%d/%m/%Y %H:%i') 'fecha sol.', c.nombre 'comercio', t.valor_inicial/100 'valor Ini', d.valorDev 'a devolver',
	from_unixtime(t.fecha, '%d/%m/%Y %H:%i') 'fecha oper.', p.nombre 'pasarela', b.nombre 'devuelta por',
	from_unixtime(d.fechaDev, '%d/%m/%Y %H:%i') 'el d&iacute;a'
from tbl_transacciones t, tbl_devoluciones d, tbl_admin a, tbl_admin b, tbl_pasarela p, tbl_comercio c
where c.idcomercio = t.idcomercio
	and p.idPasarela = t.pasarela and t.idtransaccion = d.idtransaccion and d.idadmin = a.idadmin and d.devpor = b.idadmin
	and d.fechaDev != 0
order by d.fecha desc"
			id="Devolr" name="prueba" /> <label for="Devolr">Devoluciones realizadas</label><br />
		<input type="radio" class="quer"
               value="select r.idTransf, format((r.valor/100),2) 'valor', m.moneda, r.cliente, c.nombre com, p.nombre 'pasarela',
	from_unixtime(r.fecha,'%d/%m/%Y') 'fecha'
from tbl_transferencias r, tbl_comercio c, tbl_moneda m, tbl_pasarela p
where r.idPasarela = p.idPasarela and r.moneda = m.idmoneda and r.idCom = c.id and r.estado = 'P' and r.activa = 1
order by r.fecha desc"
			id="Trfp" name="prueba" /> <label for="Trfp">Transferencias Pendientes</label><br />
		<input type="radio" class="quer"
               value="rename table tbl_trazaBack to tbl_traza_<?php echo date('Ymd') ?>;
create table tbl_trazaBack like tbl_traza_<?php echo date('Ymd') ?>"
			id="Trzbk" name="prueba" /> <label for="Trzbk">Crea tbl_traza_<?php echo date('Ymd') ?> y pone tbl_trazaBack en 0</label><br />

	</div>
	<div class="dos">
		<input type="radio" class="quer"
			value="select c.nombre 'Comercio', r.nombre 'Cliente', format(valor_inicial,2) valor,
	case r.moneda when '978' then 'EUR' when '840' then 'USD' when '124' then 'CAD' else 'GBP' end moneda,
	format(case r.moneda when '978' then valor_inicial when '840' then (select valor_inicial/s.valor from tbl_setup s where idsetup = 6)
		else (select valor_inicial/s.valor from tbl_setup s where idsetup = 8) end, 2) valorE,  from_unixtime(r.fecha, '%d/%m/%Y %H:%i') fecha,
	tiempoV dias, r.pMomento
from tbl_reserva r, tbl_comercio c
where r.estado = 'P' and pMomento = 'N' and r.id_comercio = c.idcomercio
order by r.fecha DESC"
			id="TraClie" name="prueba" /> <label for="TraClie">Transacciones Pendientes por clientes</label><br />
		<input type="radio" class="quer"
			value="select ip, from_unixtime(i.fecha, '%d/%m/%Y') 'Fecha', identificador, c.nombre, c.id, bloqueada,
	from_unixtime(i.fecha_desbloq, '%d/%m/%Y') 'Fecha desb',
	case bloqueada when 0 then (select login from tbl_admin where idadmin = i.desbloq_por) else '-' end 'Desbloq. por',
		(select count(*) from tbl_ipbloq where ip = i.ip) 'veces bloq'
from tbl_ipbloq i, tbl_comercio c
where i.bloqueada = 1 and i.idComercio = c.idcomercio
order by bloqueada desc, i.fecha desc limit 0,10"
			id="ipBloq" name="prueba" /> <label for="ipBloq">Ver las IPs bloqueadas</label><br />
		<input type="radio" class="quer"
			value="select t.idtransaccion, c.nombre comercio, identificador, p.nombre pasarela, from_unixtime(fecha, '%d/%m/%y %H:%i:%s') 'Fecha',
	format(valor_inicial/100,2) valor_ini, from_unixtime(fecha_mod, '%d/%m/%y %H:%i:%s') 'Fecha Mod', format(valor/100,2) valor, m.moneda,
	format({elem},2) 'EuroEquiv', t.estado, id_error error, tipoEntorno, ip
from tbl_transacciones t, tbl_comercio c, tbl_pasarela p, tbl_moneda m
where t.idcomercio = c.idcomercio and t.pasarela = p.idPasarela and t.moneda = m.idmoneda and tipoEntorno like '%' and fecha_mod > '{ini24h}'
order by fecha_mod desc"
			id="tranMes" name="prueba" /> <label for="tranMes">Transacciones de las &uacute;ltimas 24h</label><br />
		<input type="radio" class="quer"
			value="select b.idbaticora as id, a.nombre, a.login, r.nombre rol, b.texto, from_unixtime(b.fecha,'%d/%m/%Y %H:%i:%s') fecha,
	a.idadmin, case a.idcomercio when 'todos' then 'todos' else (select nombre from tbl_comercio where idcomercio = a.idcomercio) end comercio
from tbl_admin a, tbl_baticora b, tbl_roles r
where b.fecha > {ini24h} and a.idadmin != 10 and b.idadmin = a.idadmin and a.idrol = r.idrol and orden >=1
order by b.fecha desc limit 0,40"
			id="Bitac" name="prueba" /> <label for="Bitac">Bit&aacute;cora de las &uacute;ltimas 24hr</label><br />
		<input type="radio" class="quer"
			value="select c.nombre, format(sum({elem}),2) 'Valor', count(*) Transacc, format(sum({elem}) / count(*), 2) 'val/trans'
FROM tbl_comercio c, tbl_transacciones t
where t.idcomercio = c.idcomercio and t.estado in ('A','V') and t.tipoEntorno = 'P' and fecha_mod > {iniAno}
group by c.nombre asc with rollup"
			id="TraCom" name="prueba" /> <label for="TraCom">Valor de transacciones del &uacute;ltimo a&ntilde;o por comercio</label><br />
		<input type="radio" class="quer"
			value="select (select count(*) from tbl_comercio where activo = 'S') Activos,
	(select count(*) from tbl_comercio where activo = 'S' and estado = 'P') Produccion,
	(select count(*) from tbl_comercio where activo = 'S' and estado = 'D') Desarrollo"
			id="CantCom" name="prueba" /> <label for="CantCom">Cantidad y estado de los comercios</label><br />
		<input type="radio" class="quer"
			value="select id, moneda, from_unixtime(fecha,'%d/%m/%Y %H:%i') fechaM, bnc, bce, visa,xe from tbl_cambio order by fecha desc limit 0,30;"
			id="TasCa" name="prueba" /> <label for="TasCa">Tasas de cambio de las monedas</label><br />
		<input type="radio" class="quer"
			value="select c.nombre 'Comercio', r.nombre 'Cliente', format(valor_inicial,2) valor,
	case r.moneda when '978' then 'EUR' when '840' then 'USD' when '124' then 'CAD' else 'GBP' end moneda,
	format(case r.moneda when '978' then valor_inicial when '840' then (select valor_inicial/s.valor from tbl_setup s where idsetup = 6) when '124'
	then (select valor_inicial/s.valor from tbl_setup s where idsetup = 23)
	else (select valor_inicial/s.valor from tbl_setup s where idsetup = 8) end, 2) valorE,  from_unixtime(r.fecha, '%d/%m/%Y %H:%i') fecha, tiempoV dias
from tbl_reserva r, tbl_comercio c
where r.estado = 'P' and pMomento = 'N' and r.id_comercio = c.idcomercio order by r.fecha DESC;"
			id="InvPag" name="prueba" /> <label for="InvPag">Invitaciones de pago enviadas</label><br />
		<input type="radio" class="quer"
			value="select ip, from_unixtime(i.fecha, '%d/%m/%Y') 'Fecha', identificador, c.nombre, bloqueada,
	from_unixtime(i.fecha_desbloq, '%d/%m/%Y %H:%i') 'Fecha desb',
	case bloqueada  when 0 then (select login from tbl_admin where idadmin = i.desbloq_por) else '-' end 'Desbloq. por',
	(select count(*) from tbl_ipbloq where ip = i.ip and fecha <= i.fecha) 'veces bloq',
	(select count(*) from tbl_transacciones where ip = i.ip and estado in ('A','V','B','R') and fecha_mod <= i.fecha) 'Trs aceptadas',
	(select count(*) from tbl_transacciones where ip = i.ip and estado in ('D','P') and fecha_mod <= i.fecha) 'Trs rechaz'
from tbl_ipbloq i, tbl_comercio c where i.idComercio = c.idcomercio
order by i.fecha desc limit 0,30;"
			id="IpsBl" name="prueba" /> <label for="IpsBl">IPs bloqueadas</label><br />
		<input type="radio" class="quer" value="select format(sum({elem}) / <?php echo date('j'); ?> * {diasMesAct}, 2) 'Estimado'
FROM tbl_transacciones t
where estado in ('A','V','B','R')
	and tipoEntorno = 'P'
	and fecha_mod > '{iniMes}';

select concat('Datos de los 30 d&iacute;as anteriores') 'Query:';
select from_unixtime(t.fecha_mod, '%d/%m/%y - %W' ) as 'D&iacute;a',
	format(sum({elem}),2) 'Valor',
	(select count(s.idtransaccion)
		from tbl_transacciones s
		where from_unixtime( t.fecha_mod, '%d/%m/%y' ) = from_unixtime( s.fecha_mod, '%d/%m/%y' )
		and s.tipoEntorno = 'P'
		and s.estado = 'A') 'Transacc Acep',
	format(sum({elem}) / count(t.idtransaccion), 2) 'val/trans',
	(select count(i.idtransaccion)
		from tbl_transacciones i
		where from_unixtime( t.fecha_mod, '%d/%m/%y' ) = from_unixtime( i.fecha_mod, '%d/%m/%y' )
			and i.tipoEntorno = 'P') 'Tot trans',
	format((select count(r.idtransaccion)
		from tbl_transacciones r
		where from_unixtime( t.fecha_mod, '%d/%m/%y' ) = from_unixtime( r.fecha_mod, '%d/%m/%y' )
			and r.tipoEntorno = 'P'
			and r.estado = 'A')*100/(select count(c.idtransaccion)
		from tbl_transacciones c
		where from_unixtime( t.fecha_mod, '%d/%m/%y' ) = from_unixtime( c.fecha_mod, '%d/%m/%y' )
			and c.tipoEntorno = 'P'),2) '%Acep'
FROM tbl_transacciones t
where t.estado in ('A','V','B','R')
	and t.tipoEntorno = 'P'
	and t.fecha_mod > '{Mesatras}'
GROUP BY from_unixtime( t.fecha_mod, '%d/%m/%y' )
ORDER BY t.fecha_mod desc;

select concat('Datos de los 12 meses anteriores') 'Query:';
select date_add(from_unixtime(fecha_mod,'%Y-%m-01 23:59'), interval -1 day) as 'Mes',
	format(sum({elem}),2) 'Valor',
	count(t.idtransaccion) Transacc,
	format(sum({elem})/count(t.idtransaccion), 2) 'val/trans'
FROM tbl_transacciones t
where t.estado in ('A','V','B','R')
	and t.tipoEntorno = 'P'
	and t.fecha_mod > '{ini13A}'
group by date_add(date_add(from_unixtime(t.fecha_mod,'%Y-%m-01 23:59'), interval -1 day), interval 1 month)
order by t.fecha_mod desc;

select concat('Datos por meses desde el 1ro enero') 'Query:';
select date_add(from_unixtime(fecha_mod,'%Y-%m-01 23:59'), interval -1 day) as 'Mes',
	format(sum({elem}),2) 'Valor',
	count(t.idtransaccion) Transacc,
	format(sum({elem})/count(idtransaccion), 2) 'val/trans'
FROM tbl_transacciones t
where t.estado in ('A','V','B','R')
	and t.tipoEntorno = 'P'
	and t.fecha_mod > '{iniAno}'
group by date_add(date_add(from_unixtime(t.fecha_mod,'%Y-%m-01 23:59'), interval -1 day), interval 1 month)desc
	with rollup;

select concat('Cantidad de transacciones de hoy') 'Query:';
select count(idtransaccion) 'Total Trans'
from tbl_transacciones
where fecha > '{iniDia}';

select concat('Estado de las transacciones de hoy') 'Query';
select case t.estado
		when 'P' then 'En Proceso'
		when 'A' then 'Aceptada'
		when 'D' then 'Denegada'
		when 'N' then 'No Procesada'
		when 'B' then 'Anulada'
		when 'V' then 'Devuelta'
		when 'R' then 'Reclamada'
		else '' end estad,
	count(t.idtransaccion) cantidad,
	(count(t.idtransaccion)*100/(select count(m.idtransaccion)
		from tbl_transacciones m
		where m.fecha_mod > '{iniDia}'
			and m.tipoEntorno = 'P'
			and m.pasarela = t.pasarela)) '%'
from tbl_transacciones t
where t.fecha_mod > '{iniDia}'
	and t.tipoEntorno = 'P'
group by t.estado;

select concat('Estado de las transacciones en el mes') 'Query';
select case t.estado
		when 'P' then 'En Proceso'
		when 'A' then 'Aceptada'
		when 'D' then 'Denegada'
		when 'N' then 'No Procesada'
		when 'B' then 'Anulada'
		when 'V' then 'Devuelta'
		when 'R' then 'Reclamada'
		else '' end estad,
	count(t.idtransaccion) cantidad,
	(count(t.idtransaccion)*100/(select count(m.idtransaccion)
		from tbl_transacciones m
		where m.fecha_mod > '{iniMes}'
			and m.tipoEntorno = 'P'
			and m.pasarela = t.pasarela)) '%'
from tbl_transacciones t
where t.fecha_mod > '{iniMes}'
	and t.tipoEntorno = 'P'
group by t.estado;

select concat('Porcientos del estado de las operaciones en el d&iacute;a') 'Query';
select m.moneda,
	case t.estado
		when 'P' then 'En Proceso'
		when 'A' then 'Aceptada'
		when 'D' then 'Denegada'
		when 'N' then 'No Procesada'
		when 'B' then 'Anulada'
		when 'V' then 'Devuelta'
		when 'R' then 'Reclamada'
		else '' end estad,
	count(t.idtransaccion) cant,
	(count(t.idtransaccion)*100/(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.moneda = t.moneda
			and r.fecha_mod > '{iniDia}'
			and r.tipoEntorno = 'P'
			and r.pasarela =t.pasarela)) '%'
from tbl_transacciones t, tbl_moneda m
where t.moneda = m.idmoneda
	and t.estado = 'A'
	and t.tipoEntorno = 'P'
	and t.fecha_mod > '{iniDia}'
group by t.moneda;

select concat('Porcientos de Aceptadas por monedas en el mes') 'Query:';
select m.moneda,
	t.estado,
	count(t.idtransaccion) cant,
	(count(t.idtransaccion)*100/(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.moneda = t.moneda
			and r.fecha_mod > '{iniMes}'
			and r.tipoEntorno = 'P')) '%'
from tbl_transacciones t, tbl_moneda m
where t.moneda = m.idmoneda
	and t.tipoEntorno = 'P'
	and t.estado = 'A'
	and t.fecha_mod > '{iniMes}'
group by t.moneda;

select concat('Montos por pasarela - comercio en el d&iacute;a') 'Query:';
select p.nombre 'Pasarela',
	c.nombre 'Comercio',
	format(sum({elem}),2) 'Valor'
FROM tbl_transacciones t, tbl_pasarela p, tbl_comercio c
where t.idcomercio = c.idcomercio
	and t.pasarela = p.idPasarela
	and t.estado in ('A','V','B','R')
	and t.tipoEntorno = 'P'
	and t.fecha_mod > {iniDia}
GROUP BY t.pasarela, t.idcomercio;

select concat('Montos por pasarela - comercio en el mes') 'Query:';
select p.nombre 'Pasarela',
	c.nombre 'Comercio',
	format(sum({elem}),2) 'Valor'
FROM tbl_transacciones t, tbl_pasarela p, tbl_comercio c
where t.idcomercio = c.idcomercio
	and t.pasarela = p.idPasarela
	and t.estado in ('A','V','B','R')
	and t.tipoEntorno = 'P'
	and t.fecha_mod > {iniMes}
GROUP BY t.pasarela, t.idcomercio;

select concat('Montos por pasarelas del d&iacute;a') 'Query:';
select p.nombre,
	format(sum({elem}),2) 'Valor'
FROM tbl_transacciones t, tbl_pasarela p
where t.pasarela = p.idPasarela
	and t.estado in ('A','V','B','R')
	and t.tipoEntorno = 'P'
	and t.fecha_mod > {iniDia}
GROUP BY t.pasarela
ORDER BY p.nombre;

select concat('Montos por pasarelas del mes actual') 'Query:';
select p.nombre,
	format(sum({elem}),2) 'Valor'
FROM tbl_transacciones t, tbl_pasarela p
where t.pasarela = p.idPasarela
	and t.estado in ('A','V','B','R')
	and t.tipoEntorno = 'P'
	and t.fecha_mod > {iniMes}
GROUP BY t.pasarela
ORDER BY p.nombre;

select concat('Distribuci&oacute;n por pasarelas - monedas del d&iacute;a') 'Query:';
select p.nombre pasarela,
	m.moneda,
	count(idtransaccion) cant,
	(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.pasarela = t.pasarela
			and p.tipo = 'P'
			and r.moneda = t.moneda
			and r.estado = 'A'
			and r.fecha_mod > '{iniDia}') 'cantA',
	(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.pasarela = t.pasarela
			and p.tipo = 'P'
			and r.moneda = t.moneda
			and r.estado = 'A'
			and r.fecha_mod > '{iniDia}')/count(idtransaccion)*100 '%'
from tbl_transacciones t, tbl_pasarela p, tbl_moneda m
where t.moneda = m.idmoneda
	and t.pasarela = idPasarela
	and p.tipo = 'P'
	and t.tipoEntorno = 'P'
	and fecha_mod > '{iniDia}'
group by pasarela, t.moneda;

select concat('Distribuci&oacute;n por pasarelas - monedas del mes en curso') 'Query:';
select p.nombre pasarela,
	m.moneda,
	count(t.idtransaccion) cant,
	(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.pasarela = t.pasarela
			and r.moneda = t.moneda
			and r.estado = 'A'
			and r.fecha_mod > '{iniMes}') 'cantA',
	(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.pasarela = t.pasarela
			and r.moneda = t.moneda
			and r.estado = 'A'
			and r.fecha_mod > '{iniMes}')/count(idtransaccion)*100 '%'
from tbl_transacciones t, tbl_pasarela p, tbl_moneda m
where t.moneda = m.idmoneda
	and t.pasarela = p.idPasarela
	and t.tipoEntorno = 'P'
	and fecha_mod > '{iniMes}'
group by t.pasarela, t.moneda;

select concat('Distribuci&oacute;n por pasarelas - monedas del d&iacute;a sin contar en Proceso') 'Query:';
select p.nombre pasarela,
	m.moneda,
	count(idtransaccion) cant,
	(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.pasarela = t.pasarela
			and p.tipo = 'P'
			and r.moneda = t.moneda
			and r.estado = 'A'
			and r.fecha_mod > '{iniDia}') 'cantA',
	(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.pasarela = t.pasarela
			and p.tipo = 'P'
			and r.moneda = t.moneda
			and r.estado = 'A'
			and r.fecha_mod > '{iniDia}')/count(idtransaccion)*100 '%'
from tbl_transacciones t, tbl_pasarela p, tbl_moneda m
where t.moneda = m.idmoneda
	and t.pasarela = idPasarela
	and p.tipo = 'P'
	and t.tipoEntorno = 'P'
	and t.estado != 'P'
	and fecha_mod > '{iniDia}'
group by pasarela, t.moneda;

select concat('Distribuci&oacute;n por pasarelas - monedas del mes en curso sin contar en Proceso') 'Query:';
select p.nombre pasarela,
	m.moneda,
	count(t.idtransaccion) cant,
	(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.pasarela = t.pasarela
			and r.moneda = t.moneda
			and r.estado = 'A'
			and r.fecha_mod > '{iniMes}') 'cantA',
	(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.pasarela = t.pasarela
			and r.moneda = t.moneda
			and r.estado = 'A'
			and r.fecha_mod > '{iniMes}')/count(idtransaccion)*100 '%'
from tbl_transacciones t, tbl_pasarela p, tbl_moneda m
where t.moneda = m.idmoneda
	and t.pasarela = p.idPasarela
	and p.tipo = 'P'
	and t.tipoEntorno = 'P'
	and t.estado != 'P'
	and fecha_mod > '{iniMes}'
group by t.pasarela, t.moneda;

select concat('Distribuci&oacute;n por pasarelas - monedas del d&iacute;a en curso montos') 'Query:';
select p.nombre pasarela,
	m.moneda,
	format(sum({elem}),2) 'Valor'
from tbl_transacciones t, tbl_pasarela p, tbl_moneda m
where t.moneda = m.idmoneda
	and t.pasarela = p.idPasarela
	and t.tipoEntorno = 'P'
	and fecha_mod > '{iniDia}'
group by t.pasarela, t.moneda;

select concat('Distribuci&oacute;n por pasarelas - monedas del mes en curso montos') 'Query:';
select p.nombre pasarela,
	m.moneda,
	format(sum({elem}),2) 'Valor'
from tbl_transacciones t, tbl_pasarela p, tbl_moneda m
where t.moneda = m.idmoneda
	and t.pasarela = p.idPasarela
	and t.tipoEntorno = 'P'
	and fecha_mod > '{iniMes}'
group by t.pasarela, t.moneda;

select p.nombre pasarela,
	t.estado,
	count(idtransaccion) cant,
	(count(idtransaccion)*100/(select count(idtransaccion)
		from tbl_transacciones r
		where r.pasarela = t.pasarela
			and r.fecha_mod > '{iniMes}'
			and r.tipoEntorno = 'P')) '%'
from tbl_transacciones t, tbl_pasarela p
where t.estado = 'A'
	and t.pasarela = idPasarela
	and t.tipoEntorno = 'P'
	and fecha_mod > '{iniMes}'
group by pasarela;

select concat('Montos por comercios en el d&iacute;a') 'Query:';
select c.nombre,
	format(sum({elem}),2) 'Valor',
	count(idtransaccion) Transacc,
	format(sum({elem}) / count(*), 2) 'val/trans',
	(count(idtransaccion) *100/(select count(idtransaccion)
		from tbl_transacciones n
		where tipoEntorno = 'P'
			and fecha_mod > '{iniDia}'
			and t.idcomercio = n.idcomercio)) '% Acep'
FROM tbl_comercio c, tbl_transacciones t
where t.idcomercio = c.idcomercio
	and t.estado in ('A','V','B','R')
	and t.tipoEntorno = 'P'
	and fecha_mod > '{iniDia}'
group by t.idcomercio
order by sum(case t.estado when 'A' then euroEquiv else euroEquivDev end) desc;

select concat('Montos por comercios en el mes') 'Query:';
select c.nombre,
	format(sum({elem}),2) 'Valor',
	count(idtransaccion) Transacc,
	format(sum({elem}) / count(*), 2) 'val/trans',
	(count(idtransaccion) *100/(select count(idtransaccion)
	from tbl_transacciones n
	where tipoEntorno = 'P'
		and fecha_mod > '{iniMes}'
		and t.idcomercio = n.idcomercio)) '% Acep'
FROM tbl_comercio c, tbl_transacciones t
where t.idcomercio = c.idcomercio
	and t.estado in ('A','V','B','R')
	and t.tipoEntorno = 'P'
	and fecha_mod > '{iniMes}'
group by t.idcomercio
order by sum(case t.estado when 'A' then euroEquiv else euroEquivDev end) desc;

select concat('TOP 10 Transacciones por IP en el d&iacute;a') 'Query:';
select ip,
	count(idtransaccion) cant
from tbl_transacciones
where fecha > {iniDia}
	and estado in ('A','V','B','R')
	and tipoEntorno = 'P'
group by ip
order by count(idtransaccion) desc
limit 0,10;

select concat('TOP 10 Transacciones por IP en el mes') 'Query:';
select ip,
	count(idtransaccion) cant
from tbl_transacciones
where fecha > {Mesatras}
	and estado in ('A','V','B','R')
	and tipoEntorno = 'P'
group by ip
order by count(idtransaccion) desc
limit 0,30;

select t.idtransaccion id,
	concat(c.nombre,'<br>',c.id,'-',t.idcomercio) comercio,
	t.identificador,
	concat(p.nombre,'<br>',t.pasarela) pasarelaN,
	from_unixtime(t.fecha, '%d/%m/%y %H:%i:%s') 'Fecha',
	format((t.valor_inicial / 100),2) valIni,
	from_unixtime(fecha_mod, '%d/%m/%y %H:%i:%s') 'Fecha Mod',
	format(case t.estado
		when 'B' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		when 'V' then if(fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
		else (t.valor / 100) end,2) valor,
	concat(m.moneda,'<br>',t.moneda) moneda,
	format(t.tasa,4) tasaM,
	format({elem},2) euroEquiv,
	case t.estado
		when 'P' then 'En Proceso'
		when 'A' then concat('Aceptada<br>',t.codigo)
		when 'D' then 'Denegada'
		when 'N' then 'No Procesada'
		when 'B' then 'Anulada'
		when 'V' then 'Devuelta'
		when 'R' then 'Reclamada'
		else '' end estad,
	case t.tipoEntorno
		when 'P' then 'Producci&oacute;n'
		else 'Desarrollo' end tipoEntorno,
	case t.ip
		when '127.0.0.1' then 'no record'
		else t.ip end ip,
	t.id_error as error
from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_pasarela p
where c.idcomercio = t.idcomercio
	and t.moneda = m.idmoneda
	and p.idPasarela = t.pasarela
	and fecha_mod > '{ini24h}'
order by fecha_mod desc;"
			id="todo" name="prueba" /> <label for="todo">Todo</label><br />
		<input type="radio" class="quer"
			value="insert into tbl_cambio (visa, moneda, fecha) values (1.3321,'USD',{hor3pm});
			insert into tbl_cambio (visa, moneda, fecha) values (0.82206, 'GBP',{hor3pm});
			insert into tbl_cambio (visa, moneda, fecha) values (1.4454, 'CAD',{hor3pm});
			update tbl_setup set valor = '1.3321', fecha = {ahora} where idsetup = 6;
			update tbl_setup set valor = '0.82206', fecha = {ahora} where idsetup = 8;
			update tbl_setup set valor = '1.4454', fecha = {ahora} where idsetup = 23;
			update tbl_setup set valor = '{hor3pm}', fecha = {ahora} where idsetup = 9;
			select visa, moneda, from_unixtime(fecha,'%d/%m/%Y') fechaE from tbl_cambio order by fecha desc limit 0,30;
			select * from tbl_setup;"
			id="cmbMon" name="prueba" /> <label for="cmbMon">Cambio Moneda</label><br />
		<input type="radio" class="quer"
			value="insert into tbl_transacciones (idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, valor_inicial, tipoEntorno,
	moneda, estado, pasarela, idioma)
values ('idtransaccion', 'idcomercio', 'idtransaccion', 'T', fecha, fecha, 'importe*100', 'P', '840 ï¿½ 978', 'P', '5', 'idioma');
insert into tbl_transferencias (idTransf, cliente, idcomercio, idCom, facturaNum, email, fecha, fechaTransf, valor, moneda, concepto, idioma, idpais, idPasarela)
values ('idtransaccion', 'nombre', 'idcomercio', 'idCom', 'idtransaccion', 'correo', 'fecha', 'fecha', 'importe*100', '840 ï¿½ 978', 'servicio',
	'idioma', 'pais', '5')"
			id="transInse" name="prueba" /> <label for="transInse">Insertar transferencia</label><br />
		<input type="radio" class="quer"
               value="select t.idtransaccion id, t.identificador, concat(c.nombre,'<br>',c.idcomercio) comercio, t.codigo,
	concat(case t.estado when 'P' then 'En Proceso' when 'A' then 'Aceptada' when 'D' then 'Denegada' when 'N' then 'No Procesada'
		when 'B' then 'Anulada' else 'Devuelta' end,'<br>',t.estado )estad,from_unixtime(t.fecha,'%d/%m/%Y %H:%i:%s')fec,
	from_unixtime(t.fecha_mod,'%d/%m/%Y %H:%i:%s') fech_mod, format((t.valor_inicial / 100),2) valIni,
	format(case t.estado when 'B' then if(t.fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100)) when 'V'
		then if(t.fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100)) else (t.valor / 100) end, 2) valor,
	round(t.tasa,4) tasaM, t.tarjetas,
	format(case t.estado when 'B' then if (t.fecha_mod < fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
		when 'V' then if (t.fecha_mod < fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/tasa)), (t.valor/100/tasa))
		when 'A' then (t.valor/100/tasa)
		else '0.00' end, 2) euroEquiv,
		concat(m.moneda,'<br>', t.moneda) moned, concat(p.nombre,'<br>',t.pasarela) pasarelaN,
	t.tipoEntorno tipoE,round(t.tasaDev,4) tasaDev, case t.ip when '127.0.0.1' then 'no record' else t.ip end ip,
		case t.pago when 0 then 'No' else 'Si' end pagada, case t.tipoEntorno when 'P' then 'Producci&oacute;n' else 'Desarrollo' end tipoEntorno,
	t.id_error error, tarjetas, identificadorBnco, solDev
from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_pasarela p
where c.idcomercio = t.idcomercio and t.moneda = m.idmoneda and p.idPasarela = t.pasarela and from_unixtime(t.fecha, '%d%m%y') = ''
	and t.estado not in ('D','P') and solDev like '%' and t.idcomercio like '%%' and t.pasarela like '%%'
order by t.fecha_mod desc"
			id="transBuscD" name="prueba" /> <label for="transBuscD">Buscar transacci&oacute;n por d&iacute;a espec&iacute;fico</label><br />
					<input type="radio" class="quer"
               value="select t.idtransaccion id, t.identificador, concat(c.nombre,'<br>',c.idcomercio) comercio, t.codigo,
	concat(case t.estado when 'P' then 'En Proceso' when 'A' then 'Aceptada' when 'D' then 'Denegada' when 'N' then 'No Procesada'
		when 'B' then 'Anulada' else 'Devuelta' end,'<br>',t.estado )estad,from_unixtime(t.fecha,'%d/%m/%Y %H:%i:%s')fec,
	from_unixtime(t.fecha_mod,'%d/%m/%Y %H:%i:%s') fech_mod, format((t.valor_inicial / 100),2) valIni,
	format(case t.estado when 'B' then if(t.fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100)) when 'V'
		then if(t.fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100)) else (t.valor / 100) end, 2) valor,
	round(t.tasa,4) tasaM, format(case t.estado when 'B' then if (t.fecha_mod < fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/tasa)),
		(t.valor/100/tasa)) when 'V' then if (t.fecha_mod < fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/tasa)), (t.valor/100/tasa)) when 'A'
		then (t.valor/100/tasa) else '0.00' end, 2) euroEquiv, concat(m.moneda,'<br>', t.moneda) moned, concat(p.nombre,'<br>',t.pasarela) pasarelaN,
	t.tipoEntorno tipoE,round(t.tasaDev,4) tasaDev, case t.ip when '127.0.0.1' then 'no record' else t.ip end ip,
	case t.pago when 0 then 'No' else 'Si' end pagada, case t.tipoEntorno when 'P' then 'Producci&oacute;n' else 'Desarrollo' end tipoEntorno,
	t.id_error error, tarjetas, identificadorBnco, solDev
from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_pasarela p
where c.idcomercio = t.idcomercio and t.moneda = m.idmoneda and p.idPasarela = t.pasarela and t.idtransaccion in ('') and solDev like '%'
order by t.fecha_mod desc"
			id="transBusc" name="prueba" /> <label for="transBusc">Buscar transacci&oacute;n</label><br />
					<input type="radio" class="quer"
               value="select format(sum({elem}),2) Acumulado, format(sum({elem}) / <?php echo date('j'); ?> *
               {diasMesAct}, 2) 'Estimado', format(sum({elem}) / <?php echo date('j'); ?>, 2) 'Promedio'
FROM tbl_transacciones t
where estado in ('A')
	and tipoEntorno = 'P'
	and fecha_mod > {iniMes};

select from_unixtime( t.fecha_mod, '%d/%m/%y - %W' ) as 'D&iacute;a',
	format(sum({elem}),2) 'Valor',
	count(t.idtransaccion) 'Transacc Acep',
	format(sum({elem}) / count(t.idtransaccion), 2) 'val/trans',
	(select count(i.idtransaccion)
		from tbl_transacciones i
		where from_unixtime(t.fecha_mod,'%d%m%Y') = from_unixtime(i.fecha_mod,'%d%m%Y')
			and i.fecha_mod > ({iniDia}-(86400*1))
			and i.tipoEntorno = 'P') 'Tot trans',
   	format(count(t.idtransaccion)/(select count(i.idtransaccion)
   		from tbl_transacciones i
   		where from_unixtime(t.fecha_mod,'%d%m%Y') = from_unixtime(i.fecha_mod,'%d%m%Y')
	   		and i.fecha_mod > ({iniDia}-(86400*1))
   			and i.tipoEntorno = 'P')*100,2) '% Acep'
FROM tbl_transacciones t
where t.estado in ('A')
	and t.tipoEntorno = 'P'
	and t.fecha_mod > ({iniDia}-(86400*1))
GROUP BY from_unixtime( t.fecha_mod, '%d/%m/%y' )
order by t.fecha_mod desc;

select concat('Porcientos del estado de las operaciones en el d&iacute;a') 'Query';
select case t.estado
		when 'P' then 'En Proceso'
		when 'A' then 'Aceptada'
		when 'D' then 'Denegada'
		when 'N' then 'No Procesada'
		when 'B' then 'Anulada'
		when 'V' then 'Devuelta'
		when 'R' then 'Reclamada'
		else '' end estad,
	count(t.idtransaccion) cant,
	format((count(t.idtransaccion)*100/(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.fecha_mod > '{iniDia}'
			and r.tipoEntorno = 'P')),1) '%',

	format((select count(n.idtransaccion)
		from tbl_transacciones n
		where n.estado = t.estado
			and t.tipoEntorno = 'P'
			and n.fecha_mod > '{Mesatras}')*100/(select count(s.idtransaccion)
		from tbl_transacciones s
		where s.tipoEntorno = 'P'
			and s.fecha_mod > '{Mesatras}'),2) '% - 30'

from tbl_transacciones t
where t.tipoEntorno = 'P'
	and t.fecha_mod > '{iniDia}'
group by case t.estado
		when 'P' then 'P'
		when 'A' then 'A'
		when 'D' then 'D'
		when 'N' then 'N'
		when 'B' then 'B'
		when 'V' then 'V'
		when 'R' then 'R' end;

select concat('Pasarelas en el d&iacute;a') 'Query:';
select concat(p.nombre,' (',p.idPasarela, ') ') pasarela, case LimDiar when '100000000' then 'S/L' else format(LimDiar,2) end 'Lim Diario',
	format(sum({elem}),2) 'Acum D&iacute;a' ,
	case LimMens when '100000000' then 'S/L' else format(LimMens,2) end 'Lim Mensual',
	(select format(sum(case n.estado
		when 'B' then if (n.fecha_mod < n.fechaPagada, (-1 * ((n.valor_inicial-n.valor)/100/n.tasa)), n.valor/100/n.tasa)
		when 'V' then if (n.fecha_mod < n.fechaPagada, (-1 * ((n.valor_inicial-n.valor)/100/n.tasa)), n.valor/100/n.tasa)
		when 'R' then if (n.fecha_mod < n.fechaPagada, (-1 * ((n.valor_inicial-n.valor)/100/n.tasa)), n.valor/100/n.tasa)
		when 'A' then (n.valor/100/n.tasa)
		else '0.00' end),2) from tbl_transacciones n where t.pasarela = n.pasarela and from_unixtime(n.fecha_mod, '%m%Y')='<?php echo date('mY')?>')  'Acum Men',
	count(t.idtransaccion) cant,
	format((count(t.idtransaccion)*100/(select count(r.idtransaccion)
		from tbl_transacciones r
		where r.pasarela = t.pasarela
			and r.estado != 'P'
			and r.fecha_mod > '{iniDia}'
			and r.tipoEntorno = 'P')),2) '%',
	format((select count(n.idtransaccion)
		from tbl_transacciones n
		where n.estado = 'A'
			and t.pasarela = n.pasarela
			and t.tipoEntorno = 'P'
			and n.fecha_mod > '{Mesatras}')*100/(select count(s.idtransaccion)
		from tbl_transacciones s
		where t.pasarela = s.pasarela
			and s.tipoEntorno = 'P'
			and s.estado != 'P'
			and s.fecha_mod > '{Mesatras}'),2) '% - 30'
from tbl_transacciones t, tbl_pasarela p
where t.estado = 'A'
	and t.pasarela = idPasarela
	and t.tipoEntorno = 'P'
	and t.fecha_mod > '{iniDia}'
group by p.nombre
order by p.nombre;

select concat('Montos por comercios en el d&iacute;a') 'Query:';
select concat(c.nombre,' ',c.id,'-',t.idcomercio) comercio,
	format(sum({elem}),2) 'Valor',
	count(t.idtransaccion) Transacc,
	format(sum({elem}) / count(*), 2) 'val/trans',
	format(count(t.idtransaccion) *100/(select count(n.idtransaccion)
		from tbl_transacciones n
		where n.tipoEntorno = 'P'
			and n.fecha_mod > '{iniDia}'
			and t.idcomercio = n.idcomercio),2) '% Acep' ,
	format((select count(j.idtransaccion )
		from tbl_transacciones j
		where j.idcomercio = t.idcomercio
			and j.tipoEntorno = 'P'
			and j.estado in ('A')
	and fecha_mod > '{Mesatras}' ) *100/(select count(i.idtransaccion)
		from tbl_transacciones i
		where i.tipoEntorno = 'P'
			and i.fecha_mod > '{Mesatras}'
			and t.idcomercio = i.idcomercio),2) '% -30'
FROM tbl_comercio c, tbl_transacciones t
where t.idcomercio = c.idcomercio
	and t.estado in ('A')
	and t.tipoEntorno = 'P'
	and fecha_mod > '{iniDia}'
group by t.idcomercio
order by sum(case t.estado when 'A' then euroEquiv else euroEquivDev end) desc;

select t.idtransaccion id,
	concat(c.nombre,'<br>',c.id,'-',t.idcomercio) comercio,
	t.identificador,
	concat(p.nombre,'<br>',t.pasarela) pasarelaN,
	from_unixtime(t.fecha, '%d/%m/%y %H:%i:%s') 'Fecha',
	format((t.valor_inicial / 100),2) valIni,
	from_unixtime(t.fecha_mod, '%d/%m/%y %H:%i:%s') 'Fecha Mod',
	format(case t.estado
			when 'B' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			when 'V' then if(t.fecha_mod < t.fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100))
			else (t.valor / 100) end,2) valor,
	concat(m.moneda,'<br>',t.moneda) moneda,
	format(t.tasa,4) tasaM,
	format({elem},2) euroEquiv,
	case t.estado
		when 'P' then 'En Proceso'
		when 'A' then concat('Aceptada<br>',t.codigo)
		when 'D' then 'Denegada'
		when 'N' then 'No Procesada'
		when 'B' then 'Anulada'
		when 'V' then 'Devuelta'
		when 'R' then 'Reclamada'
		else '' end estad,
	case t.tipoEntorno
		when 'P' then 'Producci&oacute;n'
		else 'Desarrollo' end tipoEntorno,
	case t.ip
		when '127.0.0.1' then 'no record'
		else t.ip end ip,
	t.id_error as error
from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_pasarela p
where c.idcomercio = t.idcomercio
	and t.moneda = m.idmoneda
	and p.idPasarela = t.pasarela
	and fecha_mod > '{ini24h}'
order by fecha_mod desc;"
			id="ResD" name="prueba" /> <label for="ResD">Resultado del d&iacute;a</label><br />
					<input type="radio" class="quer"
               value="select concat('Denegadas por Pasarela') 'Query:';
               select case when count(t.idtransaccion) >= 3 then concat(p.nombre, ' (', count(t.idtransaccion), ')') end 'Pasarela'  from tbl_pasarela p, tbl_transacciones t where t.fecha_mod > '{hora1Ant}' and t.estado in ('D','P') and t.pasarela = p.idPasarela group by t.pasarela order by t.fecha limit 0,8;
               select concat('Denegadas por Comercio') 'Query:';
               select case when count(t.idtransaccion) >= 3 then concat(c.nombre, ' (', count(t.idtransaccion), ')') end 'Comercio'  from tbl_comercio c, tbl_transacciones t where t.fecha_mod > '{hora1Ant}' and t.estado in ('D','P') and t.idcomercio = c.idcomercio group by t.idcomercio order by t.fecha limit 0,8;
               select concat('Operaciones + 1000 denegadas Comercio') 'Query:';
               select case when count(t.idtransaccion) >= 2 then concat(c.nombre, ' (', count(t.idtransaccion), ')') end 'Comercio'  from tbl_comercio c, tbl_transacciones t where t.fecha_mod > '{hora2Ant}' and t.valor > 99900 and t.estado in ('D') and t.idcomercio = c.idcomercio group by t.idcomercio order by t.fecha limit 0,8;"
			id="panale" name="prueba" /> <label for="panale">Panel de Alertas</label><br />
		*****************************************************************************************************
					<input type="radio" class="quer"
               value="SELECT moneda, from_unixtime(fecha) fec, visa, bce, bnc, xe, tur, caixa, rural, sabadell, bankia, ibercaja, tasa FROM tbl_cambio WHERE moneda IN ('USD','EUR') ORDER BY id DESC LIMIT 0,4;
SELECT nombre, valor, from_unixtime(fecha) fec FROM tbl_setup WHERE nombre IN ('USD','CUC','GBP','CAD','JPY','TRY','MXN')"
			id="comptasas" name="prueba" /> <label for="comptasas">Comprobación de las tasas de cambio</label><br />
					<input type="radio" class="quer"
               value="delete from tbl_ipBL where ip = '7ip';
insert into tbl_ipblancas (ip, fecha, idAdmin, idComercio) values ('7ip', unix_timestamp(), 10, 1)"
			id="despin" name="despin" /> <label for="despin">Desbloquear IP</label><br />
		
	</div>
</div>
<div class="largo">
	Otra query cualquiera:<br />
	<textarea id="text" rows="20" cols="160" name="pruebas"><?php echo $d['pruebas']; ?></textarea><br />
	<input type="submit" value="Enviar" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" value="Borrar" /><br /><br /><br />


select l.nombre 'Pasarela',
	count(a.idtransaccion) 'Aceptadas',
	count(t.idtransaccion) 'Total'
from tbl_transacciones t, tbl_pasarela l, tbl_transacciones a
where l.idPasarela = t.pasarela
	and t.idtransaccion = a.idtransaccion
	and a.estado = 'A'
	and t.fecha_mod > {ini24h}
group by t.pasarela
order by l.nombre;
</div>
</form>
</body>
</html>
