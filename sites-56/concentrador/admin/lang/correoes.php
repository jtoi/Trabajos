<?php
defined( '_VALID_ENTRADA' ) or die( 'Direct Access to this location is not allowed.' );

//Facturas
define(_CLIC_AQUI, ' Clic aqu&iacute; ');
define(_ASUNTO_INVIT, ' Invitacion de {COMERCIO} a realizar el pago a traves del Administrador de Comercios');
define(_LEE3D, "<br><br>Chequee que la tarjeta de cr&eacute;dito tiene habilitado el código de seguridad para pagos electr&oacute;nicos. Si no es así consulte con su Banco.");
define(_FACTURA, 'Orden de Pago por Transferencia');
define(_DE, 'De');
define(_CLIENTE, 'Cliente');
define(_CUENTA, 'Cuenta');
define(_SERVICIOPROD, 'Servicio o Producto');
define(_PAGAFACTURA, 'Pagar esta Fatura a');
define(_REPORTE_FECHAHORA, 'Fecha Hora');
define(_ESTIMADO, 'Estimado');
define(_ENVIADO, 'Se le ha enviado esta factura a favor del comercio');
define(_REQUERIDO, 'del que usted ha requerido el servicio o producto descrito a continuación');
define(_DIRIGIRLA, 'La transferencia se dirigirá a');
define(_NOTA, 'Nota');
define(_TEXTO1, 'Esta factura deberá ser pagada dentro de las próximas 48 horas y siempre antes de recibir el servicio o producto que describe. Con el objeto de garantizar la tramitación de esta transferencia,');
define(_TEXTO2, 'especifique que debe ser abonada a la subcuenta <strong>EUR RB0J7</strong>, así mismo ');
define(_TEXTO3, 'le rogamos incluya el renglón a continuación en las observaciones de la transferencia');
define(_TEXTO4, 'En la solicitud de transferencia no debe aparecer en ninguna parte la palabra Cuba o el nombre de empresa estatal o servicio alguno que tenga que ver con Cuba, si esto ocurriera, %comer% no se hace responsable de que la transferencia llegue a su destino. Así mismo, debe indicar en algún lugar de la orden de transferencia que la transferencia corresponde al pago de la FACTURA: %oper%.');
define(_TEXTO5, 'Este documento es para uso exclusivo del cliente.');
define(_SERV, 'Concepto');
define(_VAL, 'Valor');
define(_INVITACION_DEPAGO, 'Estimado(a) {cliente}<br>
Usted puede realizar el pago de {importe} correspondiente al servicio {servicio} solicitado a {comercio} en el siguiente enlace:<br>
{url} <br><br>

O copie y pegue en su navegador la siguiente url:<br>
{urla}<br><br><br>


Importante:<br>
Debe conocer que esta solicitud es v&aacute;lida por un s&oacute;lo intento de pago, si desea realizar un segundo intento deber&aacute; solicitar una nueva invitaci&oacute;n en el correo m&aacute;s abajo. Esta invitaci&oacute;n caduca en {tiempo} d&iacute;as quedar&aacute; sin efecto.<br>
Para pagos electr&oacute;nicos seguros, despu&eacute;s de introducir los datos de la tarjeta, su banco emisor lo identificar&aacute; con un c&oacute;digo o pin de seguridad asociado a la misma. En caso de no poseerlo contacte con su banco y solic&iacute;telo de forma gratuita.<br><br>

Si tiene alguna duda para realizar el pago contacte con {correo}.<br><br>

Muchas Gracias<br><br>

AVISO LEGAL - Este correo electr&oacute;nico es confidencial y para uso exclusivo de la(s) persona(s) a quien(es) se dirige.
Si usted no es la persona destinataria designada y recibe este mensaje por error, por favor, notificar inmediatamente a la persona
que lo envi&oacute; y borrarlo definitivamente de su sistema.');

//Avisos
define(_AVISO_CTA, 'La transferencia se deber&aacute; realizar a la cuenta:<br /><br />Beneficiario: Caribbean Online S.A.<br>No. de cuenta: 0182 1250 48 0201534300<br />Banco: Banco Bilbao Vizcaya Argentaria (BBVA)<br>Dirección Banco: Calle Iparraguirre 20, 48009, Bilbao, Viscaya, Spain.<br>Iban: ES060 182 1250 48 0201534300<br />Swift: BBVAESMMXXX');
define(_AVISO_CTA1, 'La transferencia se deber&aacute; realizar a la cuenta:<br /><br />Beneficiario: Caribbean Online S.A.<br>Direcci&oacute;n Beneficiario: Calle Miguel Brostella. Ofic. 406. Centro Comercial Camino de Cruces. Panam&aacute;, Rep&uacute;blica de Panam&aacute;.<br>No. de cuenta: CHQ-003111071442<br />Banco: REPUBLIC BANK LIMITED<br>Direcci&oacute;n Banco: 59 Independence Square, Port of Spain, Trinidad & Tobago.<br />Swift: RBNKTTPX<br>Banco Intermediario: COMMERZBANK AG.<br>Direcci&oacute;n Banco Intermediario: Frankfurt, Germany.<br>Swift: COBADEFF<br>Cuenta en el Banco Intermediario: 400885839100EUR<br>Detalle de Pago: FFC: EUR RB0J7');
define(_AVISO_NOTA, 'Muy Importante!!!: En la transferencia, haga constar que ocurre por solicitud del aviso No. {aviso}.<br>En la solicitud de transferencia no debe aparecer en ninguna parte la palabra Cuba o el nombre de empresa estatal o servicio alguno que tenga que ver con Cuba, si esto ocurriera %comer% no se hace responsable de que la transferencia llegue a su destino.');
define(_AVISO_SUBJECT, 'Aviso de Transferencia de Fondos a favor de ');
define(_AVISO_TEXTO1, 'El comercio {comercio}<br /> Le solicita una transferencia de {importe} en {moneda} a su nombre a fin de continuar las operaciones.<br /><br />');
define(_TBIO_SUBJECT, "Orden de pago");
define(_TBIOREV_SUBJECT, "Solicitud de Orden de Pago"); 
define(_TBIOREVREC_SUBJECT, "Cancelada la solicitud de Orden de Pago por Transferencia"); 
define(_TBIO_REV_APROV, "Estimado Cliente:<br><br>
Su solicitud de invitaci&oacute;n de pago por transferencia al Cliente {cliente} para el pago del servicio por valor de {valor} {moneda}, ha sido APROBADA<br><br>
Se ha enviado el correo electr&oacute;nico con la invitaci&oacute;n de pago a su cliente. Usted puede darle seguimiento en las opciones Reportes/Transferencia y Reportes/Transacciones.<br><br>Equipo de Bidaiondo");
define(_TBIO_REV_REJ, "Estimado Cliente:<br><br>
Su solicitud de invitaci&oacute;n de pago por transferencia al Cliente {cliente} para el pago del servicio {servicio}<br> por valor de {valor} {moneda}, ha sido CANCELADA, por el siguiente motivo:<br> {motivo}.<br><br>
Revise el motivo de la cancelaci&oacute;n para que pueda generar otra solicitud de forma correcta. Cualquier duda p&oacute;ngase en contacto con nuestro equipo.<br><br>Atentamente,<br><br>Equipo de Bidaiondo");
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
			Estimado(a) {CLIENTE}:<br /><br />
			Esta Orden de Pago por {IMPORTE} ha caducado. Deber&aacute; ponerse en contacto con {COMERCIO} a trav&eacute;s del correo <a href="mailto:{CORREO}">{CORREO}</a>
		</div><br /><br />
		<div class="inf2"></div>
	</div>');
define(_TBIO_TEXTCORREO, 'T&eacute;rminos y condiciones de pago por Transferencia Bancaria Internacional:
<ol>
	<li>La transferencia bancaria debe efectuarse una vez se haya recibido la Orden de Pago de Transferencia, emitida por nuestra entidad.</li>
	<li>El pago debe realizarse por el importe exacto y en la moneda que aparece en la Orden de Pago recibida.</li>
	<li>El pago debe realizarse a la cuenta bancaria que aparece en la Orden de Pago recibida.</li>
	<li>El ordenante de la transferencia y titular de la cuenta bancaria, tiene que coincidir con el nombre de Cliente que aparece en la Orden de Pago recibida.</li>
	<li>El concepto que debe poner en el formulario de transferencia de su banco, en el momento de realizar la orden de la misma, es el que aparece en la Orden de Pago recibida.</li>
</ol>     
 
<b>OBSERVACI&Oacute;N:</b> Si no cumple alguno de los t&eacute;rminos anteriores, el pago no ser&aacute; reconocido y el dinero ser&aacute; devuelto, con los descuentos que correspondan.<br><br>
 
Si en un plazo de 10 d&iacute;as h&aacute;biles, no acepta estos T&eacute;rminos y Condiciones, se podr&aacute; asumir su desacuerdo con los mismos y se inhabilitar&aacute; la Orden de Pago consecuente.<br><br>
 
Para continuar y acceder a la Orden de Pago deber&aacute; hacer clic en el link a continuaci&oacute;n "He Le&iacute;do y Acepto los t&eacute;rminos y condiciones de pago por Transferencia Bancaria Internacional", asumiendo la obligaci&oacute;n de cumplirlos y prestando conformidad con el contenido de los mismos.<br><br>
 
<a href="{url}">He Le&iacute;do y Acepto los t&eacute;rminos y condiciones de pago por Transferencia Bancaria Internacional.</a>');
define(_TBIOREV_TEXTCORREO, 'Estimado Cliente:<br><br>
Hemos recibido su solicitud de invitaci&oacute;n de pago por Transferencia al cliente {cliente} para el pago del servicio por valor de {valor} {moneda}.<br><br>
El proceso de revisi&oacute;n y aprobaci&oacute;n de la misma por nuestro equipo es de 24 horas h&aacute;biles. El resultado lo ver&aacute; reflejado en nuestra plataforma y a trav&eacute;s de un correo electr&oacute;nico.<br><br>
Para las &oacute;rdenes aprobadas, se generar&aacute; autom&aacute;ticamente el correo a su cliente con la correspondiente Orden de Transferencia.<br><br>
Equipo de Bidaiondo');

//Devolucion al cliente
define(_LAB_DEVCLI, 'Aviso de devolución de transacción');
define(_COR_DEVCLI, "Estimado Cliente,<br>\n<br>\nHemos recibido su solicitud de devoluci&oacute;n con fecha &date& y que corresponde a la operaci&oacute;n &id& con identificador &idc&, realizada el d&iacute;a &dia&. El motivo de la devoluci&oacute;n es:\n<br>&motivo&\n<br>En el t&eacute;rmino de 48 horas se har&aacute; efectiva la misma le ser&aacute; enviada una notificaci&oacute;n indicando que su devoluci&oacute;n est&aacute; tramitada con su banco emisor.\n<br>\n<br>Atentamente, \n<br>\n<br>&comercio& ");
define(_IMP_DEVCLI, '--TPV VIRTUAL--<br>\n<br>\nSu solicitud de devoluci&oacute;n se est&aacute; tramitando. En 48 horas recibir&aacute; la notificaci&oacute;n.<br>\nComercio: &comnn&<br>\nFecha: &date&<br>\nCliente: &nom&<br>\nID: &id&<br>\nTransacci&oacute;n: &idc&<br>\nAutorizo: &aut&<br>\nImporte a devolver: &adev&<br>\nMoneda: &mon&<br>\nFirma:__________________________<br>\n');
define(_CLICK_AQUI, ' Click aqu&iacute; ');



?>
