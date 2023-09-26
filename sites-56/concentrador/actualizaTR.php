<?php
define('_VALID_ENTRADA', 1);
require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'include/hoteles.func.php' );
require_once( 'include/correo.php' );
$correo = new correo;
$temp = new ps_DB;

$d=$_REQUEST;

if ($d['idtr']) {
	$cheq = $d['ejecuta'];
	$q = "select t.idcomercio, t.identificador, t.valor_inicial, t.moneda, t.fecha_mod, m.moneda denom 
			from tbl_transacciones t, tbl_moneda m
			where t.moneda = m.idmoneda and idtransaccion = '".$d['idtr']."'";
	$temp->query($q);
	if ($temp->num_rows() > 0) {
		$idcom = $temp->f('idcomercio');
		$identf = $temp->f('identificador');
		$fecha = $temp->f('fecha_mod');
		$val = $temp->f('valor_inicial');
		$mon = $temp->f('moneda');
		$moneda = $temp->f('denom');
		echo $q."<br>";
		if ($moneda != 'EUR') {
			$q = "select greatest(visa, bce, bnc, xe) cambio, tur from tbl_cambio 
					where from_unixtime(fecha, '%d%m%y') = '".date('dmy',$fecha)."' and moneda like '$moneda';";
			$temp->query($q);
			$cambio = $temp->f('cambio');
			echo $q."<br>";
		} else $cambio = 1;
	} else {
		$idcom = $d['idcom'];
		$identf = $d['ident'];
		$fecha = $d['fecha'];
		$val = $d['valor'];
		$cambio = $d['tasa'];
	}

	$q = "select nombre from tbl_comercio where idcomercio = $idcom";
	$temp->query($q);
	echo "## ".$temp->f('nombre')."<br>";
	if (strlen($d['idban']) > 0){ // Aceptada
		$q = "update tbl_reserva set id_transaccion = '".$d['idtr']."', bankId = '".$d['idban']."',
				fechaPagada = ".($fecha+37).", estado = 'A', est_comer = 'P', valor = ".($val/100)
				." where codigo = '".$identf."' and id_comercio = '".$idcom."';";
		if ($cheq == 'S') $temp->query($q);
	echo $q."<br>";
		$q = "update tbl_transacciones set codigo = '".$d['idban']."', valor = ".$val.
				", id_error = null, tasa = $cambio, euroEquiv = (".$val."/100)/($cambio), estado = 'A', fecha_mod = ".($fecha+37)." where idtransaccion = '".$d['idtr']."';";
		if ($cheq == 'S') $temp->query($q);
	echo $q."<br>";
	} else { // Denegada
		$q = "update tbl_reserva set id_transaccion = '".$d['idtr']."', bankId = '', fechaPagada = ".($fecha+37).", estado = 'D', est_comer = 'P', valor = 0.0000 
				where codigo = '$identf' and id_comercio = $idcom;";
		if ($cheq == 'S') $temp->query($q);
	echo $q."<br>";
		$q = "update tbl_transacciones set id_error = '9104 - 9104 - Comercio con `titular seguro` y titular sin clave de compra segura', 
				estado = 'D', fecha_mod = ".($fecha+37)." where idtransaccion = '".$d['idtr']."';";
		if ($cheq == 'S') $temp->query($q);
	echo $q."<br>";
	}
}

?>

<form method="get" >
ID Transacción: <input type="text" name="idtr" value=""><br>
ID Banco: <input type="text" name="idban" value=""><br>
Valor: <input type="text" name="valor" value=""><br>
ID Comercio: <input type="text" name="idcom" value="411691546810"><br>
IDentificador: <input type="text" name="ident" value=""><br>
fecha: <input type="text" name="fecha" value=""><br>
Tasa: <input type="text" name="tasa" value=""><br>
Ejecuta la query: <label for="eje1"><input type="radio" name="ejecuta" id="eje1" value="S" checked="checked">SI</label>&nbsp;&nbsp;&nbsp;
<label for="eje2"><input type="radio" name="ejecuta" id="eje2" value="N">NO</label>
<input type="submit" value="Enviar">
</form>