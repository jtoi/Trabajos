<?php
defined( '_VALID_ENTRADA' ) or die( 'Direct Access to this location is not allowed.' );

//Autenticacion
define(_AUTENT_LOGIN,'Login');
define(_AUTENT_PASS,'Password');
define(_AUTENT_TITLE,'Administration Access');
define(_AUTENT_NOSEPUEDE,'<span style="color:red; font-weight:bold">You have no rights to perform this operation,<br />please contact the administrator.</span>');

//Formulario
define(_FORM_SEND, 'Send');
define(_FORM_CANCEL, 'Reset');
define(_FORM_CUENTA, 'Customer account');
define(_FORM_CUENTA_ALT, 'Account where money come from');
define(_FORM_NAME, 'Name');
define(_FORM_CORREO, 'Email');
define(_FORM_SEARCH, 'Search');
define(_FORM_YES, 'Yes');
define(_FORM_NO, 'No');
define(_FORM_SELECT, 'Select');
define(_FORM_FECHA_INICIO, 'Start Date');
define(_FORM_FECHA_FINAL, 'End Date');
define(_FORM_FECHA, 'Date');
define(_FORM_NOMBRE, 'Name and Surname');
define(_FORM_NOMB, 'Name');
define(_FORM_APELL, 'Surname');
define(_FORM_NOMBRE_CLIENTE, 'Client`s Name');
define(_FORM_MOSTRAR, 'Show');
define(_FORM_OCULTAR, 'Hide');
define(_FORM_SIGUIENTE, 'Continuar');
define(_FORM_REGRESA, 'Regresar');

//Tareas
define(_TAREA_MODIFICAR, 'Modify');
define(_TAREA_INSERTAR, 'Insert');
define(_TAREA_EDITAR, 'Edit');
define(_TAREA_BORRAR, 'Delete');
define(_TAREA_ANULAR, 'Cancel');
define(_TAREA_DEVUELTA, 'Refound');
define(_TAREA_PAGADA, 'Commerce Paid');
define(_TAREA_VER, 'See');
define(_TAREA_VERM, 'See data');
define(_TAREA_SOLDEVO, 'Refound Request');

//Menu
define(_MENU_ADMIN_TBIO, 'An&aacute;lisis de TBIO');
define(_MENU_ADMIN_ADMINISTRACION, 'Administration');
define(_MENU_ADMIN_COMERCIO, 'Commerce');
define(_MENU_ADMIN_SETUP, 'Setup');
define(_MENU_ADMIN_MENSAJE, 'Mesages');
define(_MENU_ADMIN_ADMIN, 'Admin');
define(_MENU_ADMIN_PERSONALES, 'Personal Data');
define(_MENU_ADMIN_GRUPOS, 'Groups');
define(_MENU_ADMIN_ACCESOS, 'Access');
define(_MENU_ADMIN_USUARIO, 'Users');
define(_MENU_ADMIN_BITACORA, 'Log');
define(_MENU_ADMIN_EXIT, 'Exit');
define(_MENU_ADMIN_REPORTE, 'Reports');
define(_MENU_ADMIN_COMERCIOS, 'Commerces');
define(_MENU_ADMIN_PALABRA, 'Secret word downl.');
define(_MENU_ADMIN_COMPROBACION, 'Digital sign check');
define(_MENU_ADMIN_TRANSACCIONES, 'Transactions');
define(_MENU_ADMIN_OLDTRANSACCIONES, 'Old Transactions');
define(_MENU_ADMIN_CONSOLIDADO, 'Consolidate');
define(_MENU_ADMIN_DOCUMENTACION, 'User Manual');
define(_MENU_ADMIN_COMPARACION, 'Comparation');
define(_MENU_ADMIN_PAGODIRECTO, 'Online Payment');
define(_MENU_ADMIN_PAGO, 'Customers');
define(_MENU_ADMIN_TICKET, 'Tickets');
define(_MENU_ADMIN_INSTICKET, 'Insert Ticket');
define(_MENU_ADMIN_VIETICKET, 'See Tickets');
define(_MENU_ADMIN_PAGOCLIENTE, 'Commerce payment');
define(_MENU_ADMIN_CENTRAL, 'Central de Venta'); //traducir
define(_MENU_ADMIN_TEMPORADA, 'Seassons');
define(_MENU_ADMIN_PRODUCTO, 'Product');
define(_MENU_ADMIN_CARACTERISTICA, 'Property');
define(_MENU_ADMIN_CANT, 'Qantity');
define(_MENU_ADMIN_PRECIO, 'Price');
define(_MENU_ADMIN_VENTA, 'Sales Buro');
define(_MENU_ADMIN_TRANSFERENCIA, 'Bank transfers');
define(_MENU_ADMIN_IPDENEGADA, 'Banned IPs');
define(_MENU_ADMIN_CIERRES, 'Accounting closure');
define(_MENU_ADMIN_VERCIERRES, 'See Accounting closure');
define(_MENU_ADMIN_AVISOS, 'Transfers Request');
define(_MENU_ADMIN_SMS, 'SMS Control');
define(_MENU_MENU, 'Menu');
define(_MENU_MENU_CAMBIO, 'Exchange rates');
define(_MENU_ADMIN_VOUCHER, 'Voucher Personalization');
define(_MENU_ADMIN_TRFINS, 'Transfer Insertion');
define(_MENU_ADMIN_CMPTRN,'Compare transactions / Banks');
define(_MENU_ADMIN_CAMBIO,'Cambio');
define(_MENU_ADMIN_CAMBIOCUC,'CUC Exchange');
define(_MENU_ADMIN_CAMBIOUSD,'Currency Exchange');
define(_MENU_ADMIN_PASARELA,'Comercio - POS');
define(_MENU_ADMIN_IDIOMA,'Languages');
define(_MENU_ADMIN_INVITACION,'Payment invitation');
define(_MENU_ADMIN_CONDICIONES, 'Terms of payment');
define(_MENU_ADMIN_VOUCHER, 'Voucher');
define(_MENU_ADMIN_PONCIERRE, 'Control de Cierres');
define(_MENU_ADMIN_ANALISIS, 'An&aacute;lisis de TPVs');
define(_MENU_ADMIN_SINCR, 'Sincr. Bancos');
define(_MENU_ADMIN_DATOS, 'Datos Varios');
define(_MENU_TRNSF_AMF, 'Bidaiondo Transfers');
define(_MENU_TITFICH, 'Ficheros a Titanes');
define(_MENU_ADMIN_PASA, 'Configuración TPV');
define(_MENU_ADMIN_PAISLIM, 'Limitación de países por pasarela');
define(_MENU_ADMIN_ECON, 'Destinatarios de Cierres');
define(_MENU_ADMIN_REPTRANS, 'Transfers');
define(_MENU_ADMIN_CIERREADEL, 'Aviso de Cierre adelantado');

//SMS

//Datos personales
define(_PERSONAL_TITULO, 'Personal Data');
define(_PERSONAL_IDENT, 'User');
define(_PERSONAL_IDIOMA, 'Language');
define(_PERSONAL_FECHA, 'Date format');
define(_PERSONAL_HORA, 'Time format');
define(_PERSONAL_NUM, 'Number format');
define(_PERSONAL_ESP, 'Spanish');
define(_PERSONAL_ING, 'English');
define(_PERSONAL_ITA, 'Italian');
define(_PERSONAL_IDIOMA_ALT, 'Customer Language');
define(_PERSONAL_PASS, 'Password');
define(_PERSONAL_REPASS, 'Retype Password');
define(_PERSONAL_ALERT_CONTRAS, 'Passwords didn`t match.');
define(_PERSONAL_TEXTO1, 'You have been granted with access to the Concentrator Administration. Login with this data:');
define(_PERSONAL_REC_CORREO, 'Receive email by each<br />accepted transacction');
define(_PERSONAL_REC_CORREO2, 'Receive email by each accepted transacction');
define(_PERSONAL_QUERY, 'Save query');
define(_PERSONAL_QUERYEXP, 'Save the last query run in Report page');
define(_PERSONAL_MIPERF, 'my profile');

//Grupos de trabajo
define(_GRUPOS_TITULO, 'Access Groups');
define(_GRUPOS_NOMBRE, 'Group Name');
define(_GRUPOS_ORDEN, 'Order');
define(_GRUPOS_EDIT_DATA, 'Edit Data');
define(_GRUPOS_BORRA_DATA, 'Delete Record');
define(_GRUPOS_ANULA_DATA, 'Cancel Transaction');
define(_GRUPOS_DEVUELVE_DATA, 'Refound Transaction');
define(_GRUPOS_PAGA_COMERCIO, 'Commerce Paid');
define(_GRUPOS_GRUPO, 'Group');
define(_GRUPOS_FACTURA, 'Transfer');
define(_GRUPOS_FACTURA_VER, 'See Invoice');
define(_GRUPOS_ALERTA_FACT, 'This transacction isn\`t a transfer');
define(_GRUPOS_ENVIA_MI, 'Send to me');
define(_GRUPOS_ENVIA_CLI, 'Send to custom');
define(_GRUPOS_SOLDEVOL, 'Refund request');
define(_GRUPOS_SOLDEVOL_ERROR, 'This transacction can`t be refound');

//Accesos
define(_ACCESOS_TITULO, 'Access');

//IPs
define(_IP_BLOQUEADA, 'Banned');
define(_IP_FECHA_DESBLOQUEADA, 'Unbanned Date');
define(_IP_DESBLOQUEADAPOR, 'Unbanned by');
define(_IP_VBLOQUEADA, 'Bloqued times');
define(_IP_TRNSACEPTADAS, 'Accepted transactions');
define(_IP_TRNSDENEGADAS, 'Denied transactions');

//Usuarios
define(_USUARIO_TITULO, 'Users');
define(_USUARIO_ACTIVO, 'Active');
define(_USUARIO_BORRA_PASS, 'Delete Password');
define(_USUARIO_BORRA_PASS_ALERT, 'Marcar Si causa la p&eacute;rdida del password.');
define(_USUARIO_FECHA_ULTIMA, 'Last Access');

//Bitacora
define(_BITACORA_TITULO, 'Log');
define(_BITACORA_ALERT_FECHAS, 'To see the information about one day, you must write that date as Start Date and the next day as End Date');
define(_BITACORA_ALERT_FECHASDIF, 'Stat Date must to be lower than End Date.');
define(_BITACORA_TEXT, 'Text');

//Comercios
define(_COMERCIO_TITULO, 'Commerce');
define(_COMERCIO_MONEDA, 'Currency');
define(_COMERCIO_ID, 'Id');
define(_COMERCIO_ALTA, 'Date');
define(_COMERCIO_ESTADO, 'State');
define(_COMERCIO_MOVIMIENTO, 'Last Mov.<br />Date');
define(_COMERCIO_HISTOR, 'History');
define(_COMERCIO_PALABRA, 'Secret Word');
define(_COMERCIO_ACTIVO, 'Active');
define(_COMERCIO_ACTIVITY, 'Mode');
define(_COMERCIO_ACTIVITY_DES, 'Development');
define(_COMERCIO_ACTIVITY_PRO, 'Production');
define(_COMERCIO_IDENTIF, 'Identification');
define(_COMERCIO_ACTIVITY_PRO_ALERT, 'The commerce must have one development transaction at least.');
define(_COMERCIO_HISTORIA, 'History');
define(_COMERCIO_URL, 'Page url to receive transacction data');
define(_COMERCIO_URL_DIRECTA, 'Page url to receive transacction data directly');
define(_COMERCIO_URL_CORTA, 'Url');
define(_COMERCIO_PREFIJO, 'Commerce prefix');
define(_COMERCIO_CONDICIONES, 'Payment conditions');
define(_COMERCIO_CORREO_P, 'Email template');
define(_COMERCIO_CARACTERES, 'Two characters');
define(_COMERCIO_SMS, 'Send SMS');
define(_COMERCIO_VENDE, 'Allow my sellers/users see what others do');
define(_COMERCIO_TELEFONO, 'Phone');
define(_COMERCIO_FORMATO_INT, 'international format: 00countrycode..');
define(_COMERCIO_PASARELAP, 'Web`s payment gateway');
define(_COMERCIO_PASARELAM, 'Instant payment gateway');
define(_COMERCIO_EMAIL_SUBJECT, 'Transacction Notice');
define(_COMERCIO_EMAIL_MES, "Dear customer,<br /><br />The transacction Deferred with the ID {trans} was initiated, its pay {servicio} for your "
			. "customer {nombre} with a value {importe} {moneda}.<br /><br />You must wait for the result of client`s epayment precess.<br /><br />"
			. "Thanks for choosing us.<br /><br />Ecomerce Administrator.");
define(_COMERCIO_SOLC_SI, 'Payment application already sent.');
define(_COMERCIO_FACT_SI, 'Invoice already send.');
define(_COMERCIO_CODE_YA, 'The reserve code is already used.<br />Choose another.');
define(_COMERCIO_CODEVALID, 'The transaction number is invalid, it must be alphanumeric<br />with no spaces and up to 19 characters.');
define(_COMERCIO_SECRETA_NO, "The commerce doesn`t have the secret word,<br />browse to the menu option '"._MENU_ADMIN_COMERCIO." / "._MENU_ADMIN_PALABRA."' and download it");
define(_COMERCIO_ERROR_INVIT, "There is an error sending the Invitation, please try again");
define(_COMERCIO_PAGO, 'Do Payment');
define(_COMERCIO_GENERA, 'If blank the system generate it for you');
define(_COMERCIO_PAGOA, 'Payment way');
define(_COMERCIO_ALMOMENT, 'Now');
define(_COMERCIO_DIFERI, 'Deferred');
define(_COMERCIO_SER, 'Service');
define(_COMERCIO_PASARELA, 'Payment gateway');
define(_COMERCIO_PAGAR, 'Paid');
define(_COMERCIO_DIRECCION, 'Commerce address, phone, fax, etc for Bank transfers');
define(_COMERCIO_INVACTIVA, 'This invitation is active for');
define(_COMERCIO_INVACTIVAEXPL, ' days, just for deferred payments');
define(_COMERCIO_TASA, 'Rate');
define(_COMERCIO_EUROSC, 'Exchanged Euros');
define(_COMERCIO_CODIGO, 'HTML Code');
define(_COMERCIO_VERVOUCHER, 'How it looks?</span>  (Must had at least 1 transaction through Concentrador)');
define(_COMERCIO_ERROR_HTML, 'There isn`t html code');
define(_COMERCIO_ERROR_IDI, 'Not valid language');
define(_COMERCIO_ERROR_COM, 'Not valid commerce');
define(_COMERCIO_DAT, 'Saved data');
define(_COMERCIO_TARJETA, 'Payment Card');
define(_COMERCIO_TARJETA_NUM, 'Payment Card');
define(_COMERCIO_TARJETA_EXPLICA, 'Payment Card number (16 digits)');
define(_COMERCIO_TARJETA_MES, 'Expiration - Month');
define(_COMERCIO_TARJETA_ANO, 'Year');
define(_COMERCIO_TARJETA_CVV2, 'Secure code');
define(_COMERCIO_TARJETA_CVV2_EXPLICA, 'Three digits on the back of the card');
define(_COMERCIO_DIRECCION_USR, 'Address');
define(_COMERCIO_DIRECCION_EXPLICA, 'Card owner address as registered in your bank');
define(_COMERCIO_TELEFONO_EXPLICA, 'Card owner phone number as registered in bank');
define(_COMERCIO_VISA, 'Visa, Mastercard, Others');
define(_COMERCIO_AMEX, 'American Express');
define(_COMERCIO_EXPLICA, 'The selection of payment with American Express implies that it only can be made through the secure payment platform, so you must check on the Main Page of the Commerce Administrator to see if the card was issued by one of the countries assigned to the Safe Key security program.');
define(_COMERCIO_OPERACION, 'Work');

//descarga de palabra
define(_PALABRA_GENERAR, 'Create');
define(_PALABRA_RETYPE, 'Retype Password');
define(_PALABRA_EXPLICA, "<br />To obtain a secret word you must press -Generate- button and a process will be open for a commerce transaction with our Point of Sale Terminal.  It's important for you to keep well this password.<br /><br />Every new password created by you will invalidate the previous one.<br /><br />");
define(_PALABRA_EXPLICA_NO, '<br />There was an error in the secret word generation, you must try again. If continue with error  please contact with the <a href=\'mailto:'._CORREO_SITE.'\'>administrator.</a><br /><br />');
define(_PALABRA_EXPLICA_DESCARGA, '<br /><br />If automaticlly doesn`t start the download, follow this <a href=\'componente/comercio/bajando.php?id={enlace}.txt\'>link.</a><br /><br />');

//Inicio
define(_INICIO_TITLE_ALL, 'Commerces Data');
define(_INICIO_TITLE_MENOS, 'Data');
define(_INICIO_CANT_TOTAL, 'Active Commerces Total: ');
define(_INICIO_CANT_PRODUCC, 'Production Commerces Qtty.: ');
define(_INICIO_CANT_DESARR, 'Development Commerces Qtty.: ');
define(_INICIO_NUMR_TRANSAC_ACEPT, 'Number of accepted transactions: ');
define(_INICIO_NUMR_TRANSAC_DENEG, 'Number of denied transactions: ');
define(_INICIO_VALR_TRANSAC, 'Value of accepted transactions: ');
define(_INICIO_COMERCIO_NUMR, 'Commerce identificator: ');
define(_INICIO_COMERCIO_MODO, 'Commerce work mode: ');
define(_INICIO_FECHA_MODO, 'Work mode last date: ');
define(_INICIO_MES, 'This month');
define(_INICIO_SEM, 'This Week');
define(_INICIO_HOY, 'Today');
define(_INICIO_TODO, 'Today`s acumulate');
define(_INICIO_COMERCIO, 'Commerce');
define(_INICIO_CANT_TRANSACCIONES, 'Accepted<br />transactions');
define(_INICIO_VALOR, 'Value');
define(_INICIO_CONECTADOS, 'Now are connected:');
define(_INICIO_NOCONECTADOS, 'There is no one connected');
define(_INICIO_NOTICIA, 'News');
define(_INICIO_TRBAJ, 'Working');
define(_EUROA, '1 Euro to');
define(_VERTASA, 'View exchange rates');
define(_HORA_ESP, 'Spain Time');
define(_HORA_CUB, 'Cuba Time');
define(_VENTAS_X_TIENDA, 'Top 10 Commerces');
define(_SALES_X_MES, 'Top 10 Month`s Commerces');
define(_TIENDA_TIT, 'Store');
define(_INICIO_VALORDIA, 'valor d&iacute;a');
define(_INICIO_VALORMENSUAL, 'valor mensual');
define(_INICIO_VALORANUAL, 'valor anual');
define(_INICIO_CANTDIA, 'cantidad d&iacute;a');
define(_INICIO_CANTMES, 'cantidad mensual');
define(_INICIO_CANTANO, 'cantidad anual');

//Setup
define(_SETUP_TITLE, 'Setup');
define(_SETUP_EMAIL_CONT, 'Contact email');
define(_SETUP_PALABR_OFUS, 'Secret Word Obfuscated');
define(_SETUP_CONTRASENA, 'Obfuscation Password');
define(_SETUP_COMERCIO, 'Commerce id');
define(_SETUP_PUNTO, 'Terminal id');
define(_SETUP_LOCALIZADOR, 'Localizator');
define(_SETUP_URL_COMERCIO, 'Url Commerce');
define(_SETUP_URL_DIR, 'Url Directory');
define(_SETUP_URL_TPV, 'Url TPV');
define(_SETUP_MESES, 'Months to keep transactions in main table');
define(_SETUP_DATOS_TPV, 'Production TPV Data');
define(_SETUP_DATOS_TPV_TEST, 'Integration TPV Data');
define(_SETUP_MENSAJE, 'Message');

//Reportes
define(_REPORTE_TITLE, 'Reports');
define(_REPORTE_TASK, 'Search by');
define(_REPORTE_FECHA_INI, 'Start Date');
define(_REPORTE_FECHA_FIN, 'End Date');
define(_REPORTE_REF_COMERCIO, 'Commerce reference');
define(_REPORTE_REF_BBVA, 'Bank<br />reference');
define(_REPORTE_VALOR, 'Amount');
define(_REPORTE_ALCOMERCIO, 'Paid to Commerce');
define(_REPORTE_VALOR_INICIAL, 'Initial Value');
define(_REPORTE_FECHA_MOD, 'Modified Date');
define(_REPORTE_ESTADO, 'Status');
define(_REPORTE_TOTAL, 'Total Amount');
define(_REPORTE_IDENTIFTRANS, 'Transaction identifier');
define(_REPORTE_TODOS, 'All');
define(_REPORTE_FECHA, 'Date');
define(_REPORTE_STATUS, 'Transaction status');
define(_REPORTE_PRINT, 'Print Report');
define(_REPORTE_CSV, 'Export to CSV');
define(_CONSOLIDADO_TITLE, 'Consolidate');
define(_REPORTE_DESCUENTO, 'Discount value');
define(_REPORTE_NOPUEDO, 'The descount value must be equal or less than the actual transaction value.');
define(_REPORTE_DESCUENTO_TITLE, 'Descount');
define(_REPORTE_ERROR, "Error");
define(_REPORTE_IP, "IP Addr");
define(_REPORTE_PAIS, "Country");
define(_REPORTE_CANT, 'Quantity');
define(_REPORTE_RECLAMADA, 'Claimed');
define(_REPORTE_ACEPTADA, 'Accepted');
define(_REPORTE_REALMENSUAL, 'Monthly Real');
define(_REPORTE_PROMMENSUAL, 'Monthly Average');
define(_REPORTE_PENDIENTE, 'Pending');
define(_REPORTE_DENEGADA, 'Denied');
define(_REPORTE_PROCESADA, 'Not Processed');
define(_REPORTE_ANULADA, 'Canceled');
define(_REPORTE_DEVUELTA, 'Refunded');
define(_REPORTE_PROCESO, 'Processing');
define(_REPORTE_ACEPTDEV, _REPORTE_ACEPTADA.' and '._REPORTE_DEVUELTA);
define(_REPORTE_EUROE, 'Equivalent Euro');
define(_REPORTE_TIPOG, 'Chart');
define(_REPORTE_BARRASV, 'Vertical bars');
define(_REPORTE_BARRASH, 'Horizontal bars');
define(_REPORTE_PUNTOS, 'Dots');
define(_REPORTE_LINEAS, 'Lines');
define(_REPORTE_MESES, 'months');
define(_REPORTE_MESESM, 'Months');
define(_REPORTE_VALORES, 'Values');
define(_REPORTE_DIAS, 'days');
define(_REPORTE_DIASM, 'Days');
define(_REPORTE_CLIENTE, 'Customer');
define(_REPORTE_ESTIMADO, 'Last month estimate:');
define(_REPORTE_TRANSFERENCIA, 'Transfer number');
define(_REPORTE_TRANSFERENCIA_ID, 'Transfer Id');
define(_REPORTE_TRANSFERENCIA_ESTADO, 'Transfer Status');
define(_REPORTE_SOLDEVOL, 'Refund request');
define(_MENU_ADMIN_IFRCSV, 'Cambia CSV');

//Comprobacion
define(_COMPRUEBA_TITLE, 'Digital Sign Check');
define(_COMPRUEBA_COMPROBAR, 'Verify');
define(_COMPRUEBA_COMERCIO, 'Commerce Id.');
define(_COMPRUEBA_TRANSACCION, 'Transaction No.');
define(_COMPRUEBA_IMPORTE, 'Amount');
define(_COMPRUEBA_MONEDA, 'Currency');
define(_COMPRUEBA_OPERACION, 'Transaction Modality');
define(_COMPRUEBA_PAGO_OPERAC, 'Payment');
define(_COMPRUEBA_CANCELA_OPERAC, 'Cancelation');
define(_COMPRUEBA_MD5, 'Digital Sign');

//Bitacora
define(_BITACORA_ALERT_FECHASDIF, 'Start date is older than end date.');

//Tickets
define(_TICKET_CONSULTA, "\n\nYour ticket has the id {numer}. The ticket state can be consulted at:\n");
define(_TICKET_OK, 'Ticket send');
define(_TICKET_NOOK, 'There is an error sending ticket<br />send it again or mail to');
define(_TICKET_DESCR, 'Tell us about your problem');
define(_TICKET_ACTIVO, 'Active');
define(_TICKET_CERRADO, 'Cosed');
define(_TICKET_ID, 'Ticket Id');
define(_TICKET_ASUNTO, 'Subject');
define(_TICKET_FENTRADA, 'Write Date');
define(_TICKET_FCERRADO, 'Closed Date');
define(_TICKET_DESCRI, "Description");
define(_TICKET_SOLIC, "Ask to customer");
define(_TICKET_CONT, 'Content');
define(_TICKET_PAGO, 'Payment Ticket');
define(_TICKET_AUTOR, 'Autorization');
define(_TICKET_FIRMA, 'Signature');
define(_TICKET_SUPAGO, 'Your payment has been processed successfully with the following data:');
define(_TICKET_TELEF, 'Available phone');

//Central de ventas
define(_VENTA_DESC_NOMBRE, 'Nombre de la característica ej:Hotel, tienda, temporada');
define(_VENTA_DESC_DESC, 'Valores que toma la característica ej: nombre del hotel, dpto de la tienda, Alta o Baja');
define(_VENTA_DESC_FECHA1, 'Fecha en que comenzará a afectar al producto esta característica');
define(_VENTA_DESC_FECHA2, 'Fecha en que terminará de afectar al producto esta característica');
define(_VENTA_DESC_TEMP_NOMBRE, 'Nombre de la temporada');
define(_VENTA_DESC_TEMP_FECHA1, 'Fecha en que comenzará a afectar esta temporada');
define(_VENTA_DESC_TEMP_FECHA2, 'Fecha en que terminará de afectar esta temporada');
define(_VENTA_DESC_VALOR, 'Valor con el que se modificará el precio del producto');
define(_VENTA_VALOR, 'Valor');
define(_VENTA_MODIFICA, 'Modificación');
define(_VENTA_DESC_MODIFICA, 'Forma en que se modificará con el valor el precio del producto');
define(_VENTA_MODIFICA_SUM, 'Suma al precio el Valor');
define(_VENTA_MODIFICA_RES, 'Resta al precio el Valor');
define(_VENTA_MODIFICA_POR, 'Afecta con un Valor %');
define(_VENTA_USUARIOS, 'Usuarios');
define(_VENTA_DESC_USUARIOS, 'Usuarios autorizados a aplicar esta característica');
define(_VENTA_DESC_TEMPOR, 'Guía para las fechas de inicio y final, si se escoge se deshabilitan las fechas.');
define(_VENTA_TEMP_INI, 'Seleccione');
define(_VENTA_BUSCAR, 'Buscar');
define(_VENTA_BUSCAR_SI, 'Si');
define(_VENTA_BUSCAR_NO, 'No');
define(_VENTA_DESC_BUSCAR, 'Marca la característica como buscable, en la venta del producto este será un campo que aparece para recuperar el producto');
define(_VENTA_OPCIONAL, 'Opcional');
define(_VENTA_DESC_OPCIONAL, '-Si- hace que esta característica sólo afecte al producto a la hora de la venta si así lo desea el vendedor, -No- provoca que
	el producto llegue a la venta ya afectado por esta característica y el vendedor no puede cambiarla');
define(_VENTA_DIARIO, 'Diario');
define(_VENTA_DESC_DIARIO, '-Si- hace que esta característica afecte al producto diario, que se descuente o incremente por la cantidad de días.');

//Productos
define(_PROD_CODIGO, 'Código');
define(_PROD_CODIGO_DESC, 'Código del producto si lo tuviere');
define(_PROD_NOMBRE_DESC, 'Nombre del producto, pj: habitación doble, habitación junior, excursion a Ciénaga de Zapata');
define(_PROD_DESCR_DESC, 'Descripción del producto, texto que puede servir de guía para la venta de dos productos con el mismo nombre');
define(_PROD_STOCK, 'Venta contra almacén');
define(_PROD_STOCK_DESC, 'Venta contra stock o almacén, el producto deberá tener existencias para poder venderse. 
De lo contrario se podrá vender sin consultar almacén');
define(_PROD_DESC_FECHA1, 'Fecha de inicio a partir de la cual el producto estará disponible para la venta.');
define(_PROD_DESC_FECHA2, 'Fecha hasta la cual el producto estará disponible para la venta.');
define(_PROD_CARACT_DESC, 'Características a aplicar a los productos');
define(_PROD_MONEDA_DESC, 'Moneda en que se venderá el producto, los valores de las características afectarán a este producto en esta moneda.');
define(_PROD_CANT_DESC, 'Disponibilidades del producto para un intervalo de fechas determinado.');

//Buro de ventas
define(_BURO_TITULO1, "Wizard para la venta");
define(_BURO_DESC_FECHA1, 'Fecha de realización de la venta. Si vende productos no debe especificar la fecha Final. Si vende capacidades debe poner el inicio de la reserva.');
define(_BURO_DESC_FECHA2, 'Si vende capacidades debe poner el fin de la reserva. Altérela solamente para el caso de venta de capacidades.');
define(_BURO_CONTINUA_BOTON, 'Continuar');
define(_BURO_ATRAS_BOTON, 'Atrás');
define(_BURO_CANT, 'Cantidad');
define(_BURO_CANT_DESC, 'Cantidad del producto a vender');
define(_BURO_CANT_ERROR, '<br />La cantidad no tiene el valor requerido, debe ser un entero mayor que 0.');
define(_BURO_FECHA_ERROR, '<br />La fecha(s) escogida no es válida, chequee que la Fecha inicio sea mayor o igual que hoy.');
define(_BURO_PROD_ERROR, '<br />No existen productos para el entorno de fechas suministrado.');
define(_BURO_PRECIO, 'Precio');
define(_BURO_PROD_FALLA, 'No puede venderse el producto seleccionado porque no tiene precio para la fecha escogida.');
define(_BURO_DIAS, 'Días');
define(_BURO_VENTAOK, 'Venta pasada a la cesta de compras. Haga click en Continuar');
define(_BURO_APAGAR, 'Enviar el contenido de la Cesta a Pagar');

//Cierres
define(_CIERRE_TIPO, 'Tipo de cierre');
define(_CIERRE_DESDE, 'From');
define(_CIERRE_HASTA, 'To');
define(_CIERRE_VALOR, 'Por valor');
define(_CIERRE_DIARIO, 'Daily');
define(_CIERRE_SEMANAL, 'Weekly');
define(_CIERRE_QUINCENAL, 'Quincenal');
define(_CIERRE_MENSUAL, 'Monthly');
define(_CIERRE_CONSECUTIVO, 'Consecutivo');
define(_CIERRE_FECHAINI, 'Start Date');
define(_CIERRE_FECHAFIN, 'Ending Date');
define(_CIERRE_INTEGRA, 'Integración');
define(_CIERRE_MENSUAL, 'Mensualidad');
define(_CIERRE_TARJETA, 'Uso tarjeta');
define(_CIERRE_COMIS, 'Comisión');
define(_CIERRE_RETROC, 'Devoluciones');
define(_CIERRE_TRANSF, 'Transfers');
define(_CIERRE_SWIFT, 'Swift');
define(_CIERRE_COSTOB, 'Bank Costs');
define(_CIERRE_DESC, 'Total descuentos');
define(_CIERRE_DEVOL, 'Total devol');
define(_CIERRE_TOTAL, 'Total sin desc.');
define(_CIERRE_PAGAR, 'To Pay');
define(_CIERRE_VCIERRE, 'Ver Cierre');

//Avisos
define(_MENU_ADMIN_AVISO, 'Founds Transfers Request');
define(_AVISO_NOMBRE, 'Enterprise');
define(_AVISO_REMITENTE, 'Sender Data');
define(_AVISO_OBSERVA, 'Concept / Observations');
define(_AVISO_SI, 'The payment request has been succesfully send to your email inbox.');
define(_AVISO_CODIGO, 'Transfer Code');
define(_AVISO_NUMERO, 'Transfer Number');
define(_AVISO_VALOREU, 'Amount in Euros');
define(_AVISO_TASA, 'Change rate');

//Devoluciones
define(_DEVOL_TIT, "Refound Request");
define(_DEVOL_MONT, "Amount to Refund");

//movil
define(_MOVIL_OPERAC, "Operations Qtty");
define(_MOVIL_OPERAC_ACEPT, "Accepted, Refund and Cancelled");
define(_MOVIL_OPERAC_RECHAZ, "Pending and Denied");
define(_MOVIL_VALORES, "Operation's Value");
define(_MOVIL_VALORES_VALOR, "Value");
define(_MOVIL_VALORES_PROM, "Average");
define(_MOVIL_ANO, "Year");
define(_MOVIL_MES, "Month");
define(_MOVIL_SEMANA, "Week");
define(_MOVIL_ACEPT_COMERC, "Status changed");
define(_MOVIL_TRANSACCION, "Transaction");
define(_MOVIL_COMERCIO, "Commerce");
define(_MOVIL_IDENTIFICADOR, "Indentify");
define(_MOVIL_MONEDA, "Currency");
define(_MOVIL_VALOR_INI, "Initial value");
define(_MOVIL_VALOR, "Value");
define(_MOVIL_FECHA_INI, "Initial date");
define(_MOVIL_FECHA_MOD, "Modif date");
define(_MOVIL_ESTADO, "Status");
define(_MOVIL_ESTADO_TRANS, "Transaction status");
define(_MOVIL_ENTORNO, "Commerce status");
define(_MOVIL_ERROR, "Error");
define(_MOVIL_PASARELA, "Payment gateway");
define(_MOVIL_CODIGO, "Bank code");
define(_MOVIL_PAIS, "Country");
define(_MOVIL_PAGADO, "Paid to Commerce");
define(_MOVIL_EUROEQ, "Euro Conversion");
define(_MOVIL_NOCOINC, "There are no matches");
define(_MOVIL_MOSTRANDO, "From");
define(_MOVIL_A, "to");
define(_MOVIL_DE, "of");
define(_MOVIL_RECORDS, "records");
define(_MOVIL_PROCES, "Processing, please wait...");
define(_MOVIL_PRIMERO, "First");
define(_MOVIL_ANTERIOR, "Before");
define(_MOVIL_PROXIMO, "Next");
define(_MOVIL_ULTIMO, "Last");
define(_MOVIL_NODATA, "No data available");
define(_MOVIL_CLIENTE, "Customer");
define(_MOVIL_FECHA, "Date");
define(_MOVIL_ADMINIST, "User");
define(_MOVIL_EMAIL, "Email");
define(_MOVIL_PAGO_MOM, "Payment");
define(_MOVIL_SERVIC, "Service");
define(_MOVIL_ROL, "Rol");
define(_MOVIL_FECHAV, "Last visit date");
define(_MOVIL_IDIOMA, "Language");
define(_MOVIL_IDIOMA_ES, "Idioma Español");
define(_MOVIL_IDIOMA_EN, "English Language");
define(_MOVIL_EDITA, "Edit");
define(_MOVIL_NUEVO, "New");
define(_MOVIL_DESACT, "Deactivate");
define(_MOVIL_ACT, "Activate");
define(_MOVIL_BCONTR, "Password reset");
define(_MOVIL_ALMOMENT, 'Instant Payment');
define(_MOVIL_DIFERI, 'Deferred Payment');
define(_MOVIL_DIA, 'day');
define(_MOVIL_HOY, 'Today');
define(_MOVIL_MES, 'This month');
define(_MOVIL_CANT_TRANS, 'Transactions Qtty.');
define(_MOVIL_VAL_ACEP, 'Accepted Value');

//Transferencias
define(_TRNS_ORD, 'Ordenante');
define(_TRNS_NUM, 'Operación num.');
define(_TRNS_DIV, 'DIV');
define(_TRNS_CBO, 'CBO');
define(_TRNS_LQD, 'Liquido Total');
define(_TRNS_MTV, 'Motivo');
define(_INV_VIEJA_TIT, 'Transference Invitation expired');
define(_INV_VIEJA,"Dear{usuario}:<br><br>
		
Your customer {cliente} did not accepted the Terms and Conditions of the Payment Order made by you on {fecha} 
during the established period of 10 working days and therefore the order has not been delivered.<br><br>
		
In case it is appropriate, make a new transference invitation.<br><br>
		
Administrador de Comercios");
define(_PAGO_TARJENO, "If you are charging a card not available in this list, please contact us 
(<a href='https://www.administracomercios.com/admin/index.php?componente=ticket&pag=ticketin'>Set up a Ticket</a>)");
define(_PAGO_TARJETA, "Choose client`s card");

//Idiomas
define(_IDIOMA_SALIDAOK, 'Properly stored data');//traducir
?>
