<?php
defined( '_VALID_ENTRADA' ) or die( 'Direct Access to this location is not allowed.' );

//Facturas
define(_CLIC_AQUI, ' Clic aqu&iacute; ');
define(_ASUNTO_INVIT, ' Invitacion de {COMERCIO} a realizar el pago a traves del Administrador de Comercios');
define(_LEE3D, "<br><br>Chequee que la tarjeta de cr&eacute;dito tiene habilitado el código de seguridad para pagos electr&oacute;nicos. Si no es así consulte con su Banco.");
define(_FACTURA, 'Factura');
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

//Avisos
define(_AVISO_CTA, 'La transferencia se deberá realizar a la cuenta:<br /><br />Beneficiario: Caribbean Online S.A.<br>No. de cuenta: 0182 1250 48 0201534300<br />Banco: Banco Bilbao Vizcaya Argentaria (BBVA)<br>Dirección Banco: Calle Iparraguirre 20, 48009, Bilbao, Viscaya, Spain.<br>Iban: ES060 182 1250 48 0201534300<br />Swift: BBVAESMMXXX');
define(_AVISO_CTA1, 'La transferencia se deberá realizar a la cuenta:<br /><br />Beneficiario: Caribbean Online S.A.<br>Dirección Beneficiario: Calle Miguel Brostella. Ofic. 406. Centro Comercial Camino de Cruces. Panamá, República de Panamá.<br>No. de cuenta: CHQ-003111071442<br />Banco: REPUBLIC BANK LIMITED<br>Dirección Banco: 59 Independence Square, Port of Spain, Trinidad & Tobago.<br />Swift: RBNKTTPX<br>Banco Intermediario: COMMERZBANK AG.<br>Dirección Banco Intermediario: Frankfurt, Germany.<br>Swift: COBADEFF<br>Cuenta en el Banco Intermediario: 400885839100EUR<br>Detalle de Pago: FFC: EUR RB0J7');
define(_AVISO_NOTA, 'Muy Importante!!!: En la transferencia, haga constar que ocurre por solicitud del aviso No. {aviso}.<br>En la solicitud de transferencia no debe aparecer en ninguna parte la palabra Cuba o el nombre de empresa estatal o servicio alguno que tenga que ver con Cuba, si esto ocurriera %comer% no se hace responsable de que la transferencia llegue a su destino.');
define(_AVISO_SUBJECT, 'Aviso de Transferencia de Fondos a favor de ');
define(_AVISO_TEXTO1, 'El comercio {comercio}<br /> Le solicita una transferencia de {importe} en {moneda} a su nombre a fin de continuar las operaciones.<br /><br />');

//Devolucion al cliente
define(_LAB_DEVCLI, 'Aviso de devolución de transacción');
define(_COR_DEVCLI, "Estimado Cliente,<br>\n<br>\nHemos recibido su solicitud de devolución con fecha &date& y que corresponde a la operación &id& con identificador &idc&, realizada el día &dia&. El motivo de la devolución es:\n<br>&motivo&\n<br>En el término de 48 horas se hará efectiva la misma le será enviada una notificación indicando que su devolución está tramitada con su banco emisor.\n<br>\n<br>Atentamente, \n<br>\n<br>&comercio& ");
define(_IMP_DEVCLI, '--TPV VIRTUAL--<br>\n<br>\nSu solicitud de devolución se está tramitando. En 48 horas recibirá la notificación.<br>\nComercio: &comnn&<br>\nFecha: &date&<br>\nCliente: &nom&<br>\nID: &id&<br>\nTransacción: &idc&<br>\nAutorizo: &aut&<br>\nImporte a devolver: &adev&<br>\nMoneda: &mon&<br>\nFirma:__________________________<br>\n');


?>
