<?php
define( '_VALID_ENTRADA', 1 );
require_once( '../../../configuration.php' );
require_once( '../include/mysqli.php' );

global $temp;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if ($_POST["id"]) {
	$id = $_POST["id"];
	$cod = $_POST["cod"];
	$q = "select idcomercio, identificador, estado, fecha, moneda from tbl_transacciones where idtransaccion = '$id'";
	$temp->query($q);
	
	$com = $temp->f('idcomercio');
	$iden = $temp->f('identificador');
	$estado = $temp->f('estado');
	$fecha = $temp->f('fecha');
	$moneda = $temp->f('moneda');
	$fechaMod = date('dmy',$fecha);
	echo "estado=$estado";
	
	if ($estado == 'P') {
		$q = "select if (visa > bce, (if (visa > xe,visa,(if (xe > bce, xe, bce)))),(if(bce > xe,bce,(if(xe>visa,xe,visa))))) cambio from tbl_cambio c, "
				. "tbl_moneda m where c.moneda = m.moneda and m.idmoneda = $moneda and from_unixtime(c.fecha,'%d%m%y') = '$fechaMod'";
		$temp->query($q);
		if ($temp->getErrorMsg()) {echo $q."\n<br>";echo "Error: ".$temp->getErrorMsg()."\n<br><br>";}
		$camb = $temp->f('cambio');
		
		$q = "update tbl_transacciones set codigo = '$cod', valor = valor_inicial, id_error = null, tasa = $camb, euroEquiv = (valor/100)/(tasa), estado = 'A', "
				. "fecha_mod = (fecha + 567) where idtransaccion = '$id'";
		$temp->query($q);
		if ($temp->getErrorMsg()) {echo $q."\n<br>";echo "Error: ".$temp->getErrorMsg()."\n<br><br>";}
		$q = "update tbl_reserva set id_transaccion = '$id', bankId = '$cod', fechaPagada = (fecha + 567), estado = 'A', est_comer = 'P', valor = valor_inicial where codigo = '$iden' and id_comercio = $com;";
		$temp->query($q);
		if ($temp->getErrorMsg()) {echo $q."\n<br>";echo "Error: ".$temp->getErrorMsg()."\n<br><br>";}
		if ($com == '129025985109'){
			$q = "update tbl_amadeus set idtransaccion = '$id', estado = 'A', fechamod = (fecha + 567), codigo = '$id' where idcomercio = '$com' and rl = '$iden'";
			$temp->query($q);
			if ($temp->getErrorMsg()) {echo $q."\n<br>";echo "Error: ".$temp->getErrorMsg()."\n<br><br>";}
		}
	}
//} else {
//	$clave_encriptacion = 'T21RAFBM';
//	$merchantid = "054252135";
//	$acquirerbin = '0000554026';
//	$terminalid = "00000003";
//	$fecha = date('Ymd');
//	$firma = Encrypt($clave_encriptacion . $merchantid . $acquirerbin . $terminalid . $fecha, '');
//	
//	$data = array(
//            "merchantid"=>$merchantid,
//            "acquirerbin"=>$acquirerbin,
//            "terminalid"=>$terminalid,
//            "fecha"=>$fecha,
//            "firma"=>$firma
//        );
//	$i = 0;
//	$ch = curl_init('http://comercios.ceca.es/webapp/ConsTpvVirtWeb/ConsTpvVirtS?modo=cambioDivisas');
//	curl_setopt($ch, CURLOPT_HEADER, false);
//	curl_setopt($ch, CURLOPT_POST, true);
//	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//	while (strlen($salidaCurl) == 0 && $i < 4) {
//		$salidaCurl = curl_exec($ch);
//		$curl_info = curl_getinfo($ch);
//
//		foreach ($curl_info as $key => $value) {
////			$correoMi .=  $key." = ".$value."<br>\n";
//		}
//
//		if(strlen(curl_error($ch))) $correoMi .= "Curl error: ".curl_error($ch)."<br>\n";
//
//		$correoMi .= "Enviado a Amadeus por detrás, envío $i - ".$salidaCurl."<br>\n";
//		$i++;
//	}
//	curl_close($ch);
}

echo $correoMi;

function Encrypt($data, $secret)
{    
	$td = mcrypt_module_open('tripledes', '', 'ecb', '');
	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	mcrypt_generic_init($td, $secret, $iv);
	$encrypted_data = mcrypt_generic($td, $data);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);

  return $encrypted_data;

}

$q = "select t.idtransaccion id, t.identificador, concat(c.nombre,'<br>',c.idcomercio) comercio, t.codigo, concat(case t.estado when 'P' then 'En Proceso' when 'A' then 'Aceptada' when 'D' then 'Denegada' when 'N' then 'No Procesada' when 'B' then 'Anulada' else 'Devuelta' end,'<br>',t.estado )estad,from_unixtime(t.fecha,'%d/%m/%Y %H:%i:%s')fec,from_unixtime(t.fecha_mod,'%d/%m/%Y %H:%i:%s') fech_mod, format((t.valor_inicial / 100),2) valIni, format(case t.estado when 'B' then if(t.fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100)) when 'V' then if(t.fecha_mod < fechaPagada,(-(1) * ((t.valor_inicial - t.valor) / 100)),(t.valor / 100)) else (t.valor / 100) end, 2) valor,round(t.tasa,4) tasaM, format(case t.estado when 'B' then if (t.fecha_mod < fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa)) when 'V' then if (t.fecha_mod < fechaPagada, (-1 * ((t.valor_Inicial-t.valor)/100/tasa)), (t.valor/100/tasa)) when 'A' then (t.valor/100/tasa) else '0.00' end, 2) euroEquiv, concat(m.moneda,'<br>', t.moneda) moned, concat(p.nombre,'<br>',t.pasarela) pasarelaN,t.tipoEntorno tipoE,round(t.tasaDev,4) tasaDev, case t.ip when '127.0.0.1' then 'no record' else t.ip end ip, case t.pago when 0 then 'No' else 'Si' end pagada, case t.tipoEntorno when 'P' then 'Producción' else 'Desarrollo' end tipoEntorno, t.id_error error from tbl_transacciones t, tbl_comercio c, tbl_moneda m, tbl_pasarela p where c.idcomercio = t.idcomercio and t.moneda = m.idmoneda and p.idPasarela = t.pasarela and t.idtransaccion = '$id'";
?>

<form method="post" action="">
	Identificador de la operación: <input type="text" name="id"><br>
	Código de aceptada: <input type="text" name="cod"><br>
	<input type="submit" value="Enviar">
</form>
