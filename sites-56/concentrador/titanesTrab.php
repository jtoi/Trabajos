<?php define( '_VALID_ENTRADA', 1 );

/* 
 * Inyecta los arreglos en los trabajos con Titanes
 */

require_once( 'configuration.php' );
include 'include/mysqli.php';
require_once( 'admin/adminis.func.php' );
$temp = new ps_DB;



$d = $_POST;

//Reenvío de transacciones a Titanes
if (isset($d['transaccion']) && $d['transaccion'] > 100000000 && strlen($d['transaccion']) == 12) {
	$temp->query("select o.envia 'AmountToSend', o.recibe 'AmountToReceive', o.comision 'Charge', t.valor_inicial 'TotalAmount', c.idtitanes 'CustomerId', b.idtitanes 'BeneficiaryId', b.ciudad 'City', m.moneda 'CurrencyToSend', o.idrazon 'Reason' from tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t, tbl_moneda m where o.idbeneficiario = b.id and t.idtransaccion = o.idtransaccion and t.moneda = m.idmoneda and o.idcliente = c.id and (o.titOrdenId is null or o.titOrdenId = '') and o.idtransaccion = ".$d['transaccion']);
	
	if ($temp->num_rows() == 1) {
		($temp->f('City') == NULL || $temp->f('City') == '') ? $city = 'La Habana' : $city = $temp->f('City');
		
		$data = array(
					'CustomerId'				=> $temp->f('CustomerId'),
					'BeneficiaryId'				=> $temp->f('BeneficiaryId'),
					'Country'					=> 'CU',
					'City'						=> $city,
					'DeliveryType'				=> '4',
					'AmountToSend'				=> number_format(($temp->f('AmountToSend')/100),2,".",""),
					'CurrencyToSend'			=> $temp->f('CurrencyToSend'),
					'AmountToReceive'			=> number_format(($temp->f('AmountToReceive')/100),2,".",""),
					'CurrencyToReceive'			=> 'CUC',
					'Charge'					=> number_format(($temp->f('Charge')/100),2,".",""),
					'TotalAmount'				=> number_format(($temp->f('TotalAmount')/100),2,".",""),
					'Correspondent'				=> 'T086',
					'SubCorrespondent'			=> '1',
					'Branch'					=> 'T0860001',
					'Reason'					=> $temp->f('Reason'),
					'BenefBankName'				=> '',
					'BenefBankCity'				=> '',
					'BenefBankAccountNumber'	=> '-1',
					'BenefBankAccountType'		=> '3',
					'BenefBankAccountAgency'	=> '',
					'Ds_Merchant_Order'			=> $d['transaccion']
				);
		
		$data = array_merge($data, array(
					"Signature"					=> $temp->f('CustomerId').$temp->f('BeneficiaryId').(number_format(($temp->f('AmountToReceive')/100),2,".","")).'CUC'));
		$tipo = 'O';
		$correoMi .= "<br>data=". json_encode($data);
		
		$sale = datATitanes($data,$tipo,91);
		
		$correoMi .= "<br>sale=$sale<br>\n";
		
		$arrVales = json_decode($sale);

		if ($arrVales->Id > 0) {
			$idTit			= $arrVales->Id;
			$StatusCode		= $arrVales->StatusCode;
			$Status			= $arrVales->Status;
			$Description	= $arrVales->Description;
			$Issues			= $arrVales->Issues;

			$correoMi .= "idTit=$idTit<br>";
			$correoMi .= "StatusCode=$StatusCode<br>";
			$correoMi .= "Status=$Status<br>";
			$correoMi .= "Description=$Description<br>";
			$correoMi .= "Issues=$Issues<br>";

			$q = "update tbl_aisOrden set titOrdenId = '$idTit' where idtransaccion = ".$d['transaccion'];
			$correoMi .= $q."<br>";
			$temp->query($q);

			if ($Status != 'Ready') {

				$coderror = $Description." - ".$Issues[0]." ".$Issues[1]." ".$Issues[2];

				echo("La operación ".$d['transaccion']." de Cimex ha devuelto error en Titanes. Error devuelto: $coderror");
			}
		} else {
				$coderror = $arrVales->Errors['Message'];

				echo("La operación ".$d['transaccion']." de Cimex ha sido Revocada por Titanes ver si se devuelve. Error devuelto ". $coderror);

		}
		
		echo $correoMi;
		
	} else echo "No tiene operación asociada";
}

?>

<form method="post" target="" >
	Transacción: <input type="text" name="transaccion" value="" /><br/><br/>
	<input type="submit" value="Enviar" />
</form>