<?php
defined( '_VALID_ENTRADA' ) or die( 'Direct Access to this location is not allowed.' );

//Facturas
define(_CLIC_AQUI, ' Click here ');
define(_ASUNTO_INVIT, ' Invitation from {COMERCIO} to make the payment trought Ecomerce Administrator');
define(_LEE3D, "<br><br>Check the credit card have the code for secure epayment. Otherwise ask to your Bank.");
define(_FACTURA, 'Transfer Payment Order');
define(_DE, 'From');
define(_CLIENTE, 'Customer');
define(_CUENTA, 'Acount');
define(_SERVICIOPROD, 'Service or Product');
define(_PAGAFACTURA, 'Pay this Invoice to');
define(_REPORTE_FECHAHORA, 'Date and time');
define(_ESTIMADO, 'Dear');
define(_ENVIADO, 'This invoice has been sent in favour of ');
define(_REQUERIDO, ' in order to pay products or services described below');
define(_DIRIGIRLA, 'The Transfer will be send to');
define(_NOTA, 'Note');
define(_TEXTO1, 'This invoice will have to be paid in next 48 hours and always before receiving the service or product that it describes. In order to improve the processing of this transfer,');
define(_TEXTO2, 'specify that it must be paid to the sub-account <strong>EUR RB0J7</strong>, likewise ');
define(_TEXTO3, 'Include the following line in the transfer observations');
define(_TEXTO4, 'The request for transfer should not appear anywhere on the word Cuba or the name of any state enterprise or service that has to do with Cuba, if this happens, %comer% is not responsible for the transfer reaches its destination. Also, should indicate somewhere in the transfer order that the transfer is for the payment of INVOICE: %oper%.');
define(_TEXTO5, 'This document is for customer use only.');
define(_SERV, 'Service');
define(_VAL, 'Value');
define(_INVITACION_DEPAGO, 'Dear {cliente}<br> 
You can make the payment of {importe} corresponding to the service {servicio} requested to {comercio} on the following link:<br>
{url} <br><br>

or copy and paste into your browser the following url:<br>
{urla}<br><br><br>


Important:<br>
You must know that this request is valid for a single payment attempt, if you wish to make a second attempt you must request a new invitation in the email below. This invitation expires in {tiempo} days will be without effect.<br>
For secure electronic payments, after entering the data on the card, your issuing bank will identify you with a
security code or pin associated with it. If you do not have it, please contact your bank and ask for it for free.<br><br>


If you have any doubts to make the payment, you may contact {correo}.<br><br>

Thank you very much<br><br>

LEGAL NOTICE - This email is confidential and for the exclusive use of the person (s) to whom it is addressed.
If you are not the designated recipient and you receive this message in error, please notify the sender immediately and delete it from your system.');

//Avisos
define(_AVISO_CTA, 'The transfer will have to be realized to the account:<br /><br />Beneficiary: Caribbean Online S.A.<br>Account Number: 0182 1250 48 0201534300<br />Bank: Banco Bilbao Vizcaya Argentaria (BBVA)<br>Bank Address: Calle Iparraguirre 20, 48009, Bilbao, Viscaya, Spain.<br>Iban: ES060 182 1250 48 0201534300<br />Swift: BBVAESMMXXX');
define(_AVISO_CTA1, 'The transfer will have to be realized to the account:<br /><br />Beneficiary: Caribbean Online S.A.<br>Beneficiary Address: Calle Miguel Brostella. Ofic. 406. Centro Comercial Camino de Cruces. Panamá, República de Panamá.<br>Account Number: CHQ-003111071442<br />Bank: REPUBLIC BANK LIMITED<br>Bank Address: 59 Independence Square, Port of Spain, Trinidad & Tobago.<br />Swift: RBNKTTPX<br>Intermediary Bank: COMMERZBANK AG.<br>Intermediary Bank Address: Frankfurt, Germany.<br>Swift: COBADEFF<br>Intermediary Bank Account: 400885839100EUR<br>Payment Details: FFC: EUR RB0J7');
define(_AVISO_NOTA, 'Very important!!!: In the transfer, make be clear that it happens for request of the notice No. {aviso}.<br>In the request of transfer the word Cuba must not appear anywhere or the name of state company or some service that it has any relationship with Cuba, if this happened %comer% does not become responsible from that the transfer comes to his destination.');
define(_AVISO_SUBJECT, 'Funds Transfer Notice of ');
define(_AVISO_TEXTO1, 'The Trade {comercio}<br /> requests a transfer of funds {importe} in {moneda} in order to continue the operations.<br /><br />');
define(_TBIO_SUBJECT, "Payment Order");
define(_TBIOREV_SUBJECT, "Payment Order Request"); 
define(_TBIOREVREC_SUBJECT, "Cancelada la solicitud de Orden de Pago por Transferencia"); 
define(_TBIO_REV_APROV, "Dear Customer:<br><br>
Your request for an invitation to pay by transfer to the Client {client} for the payment of the service for a value of {valor} {moneda}, has been APPROVED<br><br>
The email with the payment invitation has been sent to your customer. You can track it in the Report/Transfers and Reports/Transactions options.<br><br>Equipo de Bidaiondo");
define(_TBIO_REV_REJ, "Dear Customer:<br><br>
Your request for a transfer payment invitation to the Customer {client} for the payment of the service {servicio}<br> for a value of {valor} {moneda}, has been CANCELED, for the following reason:<br> {motivo}.<br><br>
Review the reason for the cancellation so that you can successfully generate another request. Any questions contact our team.<br><br>Attentively<br><br>Equipo de Bidaiondo");
define(_INV_VIEJA_DOS, '<style>
	#encabVou {text-align: center;}
	#encabVou {width: 550px;float: left;}
	#encabVou h1 {margin: 0px;}
	#logoVou {background: url(https://www.administracomercios.com/admin/template/images/degrada.png) repeat-x top;min-height: 83px;float: left;width: 100%;}
	#logoVou img {margin: 0 auto;display: block;}
	.inf2 {background:url(https://www.administracomercios.com/admin/template/images/degrada5.png) repeat-x;border-bottom:#679BCC solid 1px;height:6px;float: left;width: 100%;}
	.inf {background:url(https://www.administracomercios.com/admin/template/images/degrada2.png) repeat-x;border-top:#679BCC solid 1px;height:6px;float: left;width: 100%;}
	body {font-size: 11px;font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;margin-left: 0px;margin-top: 0px;}
	.enca {float: right;width: 98%;margin: 10px 0;}
	.lef {text-align: left;}
	</style>
		
    <div id="encabVou">
		<div class="inf"></div>
		<div id="logoVou"><img src="https://www.administracomercios.com/admin/template/images/banner2.png" /></div>
		<h1>Error en la Orden de Pago</h1>
		<div class="enca lef">
			Dear Customer {CLIENTE}:<br /><br />
			This Payment Order of {IMPORTE} has expired. You must contact to {COMERCIO} through <a href="mailto:{CORREO}">{CORREO}</a>
		</div><br /><br />
		<div class="inf2"></div>
	</div>');
define(_TBIO_TEXTCORREO, 'Terms and conditions of payment through International Bank Transfer:
<ol>
	<li>The bank transfer must be made once the Transfer Payment Order, issued by our entity, has been received.</li>
	<li>The payment must be made for the exact amount and in the currency that appears in the Payment Order.</li>
	<li>The payment must be made to the bank account that appears in the Payment Order.</li>
	<li>The customer issuing the transfer and holder of the bank account must match with the name of Client that appears in the Payment Order.</li>
	<li>At the time of making the order, you must write on the transfer form of your bank the same concept appearing on the Payment Order you received.</li>
</ol>
 
<b>NOTE:</b> If you do not comply with any of the above terms, the payment will not be recognized and the money will be refunded, with the corresponding discounts.<br><br>

If within 10 business days, you do not accept these Terms and Conditions, a disagreement with them will be assumed and the consequent Payment Order will be disabled.<br><br>
 
To continue and access to the Payment Order, you must click on the link below "I have read and accept the terms and conditions of payment through International Bank Transfer", assuming the obligation to comply with them and accepting the content thereof.<br><br>
 
<a href="{url}">I have read and accept the terms and conditions of payment through International Bank Transfer.</a>');
define(_TBIOREV_TEXTCORREO, 'Dear Client:<br><br>
We have received your request for a payment invitation by Transfer to the client {cliente} for the payment of the service for the value of {valor} {moneda}.<br><br>
The review and approval process of the same by our team is 24 business hours. You will see the result reflected on our platform and through an email.<br><br>
For approved orders, an email will be automatically generated to your client with the corresponding Transfer Order.<br><br>
Equipo de Bidaiondo');

//Devolucion al cliente
define(_LAB_DEVCLI, 'Transaction Refund Notice');
define(_COR_DEVCLI, "Dear Customer,<br>\n<br>\nWe have received your refund request on &date& corresponding to operation &id& identifier &idc&, performed on &dia&. The reason for the refund is:\n<br>&motivo&\n<br>You will receive a notification on the next 48 hours indicating the your refund was processed with your  card-issuing bank.\n<br>\n<br>Best Regards, \n<br>\n<br>&comercio& ");
define(_IMP_DEVCLI, '--Virtual POS--<br>\n<br>\nYour refund request is in process. You will receive a notification in 48 hours.<br>\nMerchant: &comnn&<br>\nDate: &date&<br>\nCustomer: &nom&<br>\nID: &id&<br>\nTransaction: &idc&<br>\nAuthorization: &aut&<br>\nAmount to Refund: &adev&<br>\nCurrency: &mon&<br>\nSign:<br>\n');
define(_CLICK_AQUI, ' Click here ');

?>
