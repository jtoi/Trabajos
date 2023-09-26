<?php
define( '_VALID_ENTRADA', 1 );
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once( 'configuration.php' );
require_once 'include/mysqli.php';
$temp = new ps_DB;
require_once 'admin/adminis.func.php';

$d=$_POST;


if ($d['usuario']) {
    $cliente = '780900';
    $beneficiario = '1487578';
    $cantidad = 60.19;

    $cadena = $cliente.$beneficiario.$cantidad.'CUC';
    echo $cadena."<br>";


    echo hash("sha512", $cadena.'b527bae683562a289d2f247aa937ca6704327b7f55e1cebc66664206b83417aaa56c116baa24084c6ab0d17742a594a9154101daf3bdb5c7364bf12b94de2240');
} elseif(strlen ($d['orden']) == 12){
    $temp->query("select o.envia 'AmountToSend', o.recibe 'AmountToReceive', o.comision 'Charge', t.valor_inicial 'TotalAmount', c.idtitanes 'CustomerId', b.idtitanes 'BeneficiaryId', b.ciudad 'City', m.moneda 'CurrencyToSend', o.idrazon 'Reason' from tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t, tbl_moneda m where o.idbeneficiario = b.id and t.idtransaccion = o.idtransaccion and t.moneda = m.idmoneda and o.idcliente = c.id and (o.titOrdenId is null or o.titOrdenId = '') and o.idtransaccion = ".$d['orden']);
    
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
            'Ds_Merchant_Order'			=> $decodec->Ds_Order
    );
	
	$data = array_merge($data, array(
			"Signature"					=> $temp->f('CustomerId').$temp->f('BeneficiaryId').(number_format(($temp->f('AmountToReceive')/100),2,".","")).'CUC'));
	$tipo = 'O';
	$sale = datATitanes($data,$tipo,91);
}
?>

<form name="envio" method="POST">
    Reenvia orden a titanes (idtransaccion):<input type="text" name="orden" value=""/>
</form>