<?php
defined( '_VALID_ENTRADA' ) or die( 'Direct Access to this location is not allowed.' );

//Autenticaci&oacute;n
define(_AUTENT_LOGIN,'Usuario');
define(_AUTENT_PASS,'Contrase&ntilde;a');
define(_AUTENT_TITLE,'Acceso a la Administración');
define(_AUTENT_NOSEPUEDE,'<span style="color:red; font-weight:bold">Usted no tiene acceso a realizar esta operaci&oacute;n,<br />por favor p&oacute;ngase en contacto con el administrador.</span>');

//Formulario
define(_FORM_SEND, 'Enviar');
define(_FORM_CANCEL, 'Cancelar');
define(_FORM_CUENTA, 'Cuenta Bancaria Cliente');
define(_FORM_CUENTA_ALT, 'Cuenta desde donde será emitido el dinero');
define(_FORM_NAME, 'Nombre y Apellidos');
define(_FORM_CORREO, 'Correo');
define(_FORM_SEARCH, 'Buscar');
define(_FORM_YES, 'Si');
define(_FORM_NO, 'No');
define(_FORM_SELECT, 'Seleccione');
define(_FORM_FECHA_INICIO, 'Fecha Inicio');
define(_FORM_FECHA_FINAL, 'Fecha Final');
define(_FORM_FECHA, 'Fecha');
define(_FORM_NOMBRE, 'Nombre y Apellidos');
define(_FORM_NOMB, 'Nombre');
define(_FORM_APELL, 'Apellidos');
define(_FORM_NOMBRE_CLIENTE, 'Nombre cliente');
define(_FORM_MOSTRAR, 'Mostrar');
define(_FORM_OCULTAR, 'Ocultar');
define(_FORM_SIGUIENTE, 'Continuar');
define(_FORM_REGRESA, 'Regresar');

//Tareas
define(_TAREA_MODIFICAR, 'Modificar');
define(_TAREA_INSERTAR, 'Insertar');
define(_TAREA_EDITAR, 'Editar');
define(_TAREA_BORRAR, 'Borrar');
define(_TAREA_ANULAR, 'Anular');
define(_TAREA_DEVUELTA, 'Devolver');
define(_TAREA_PAGADA, 'Pago a Comercio');
define(_TAREA_VER, 'Ver');
define(_TAREA_VERM, 'Ver data');
define(_TAREA_SOLDEVO, 'Solicitar devolver');

//Menu
define(_MENU_ADMIN_TBIO, 'An&aacute;lisis de TBIO');
define(_MENU_ADMIN_ADMINISTRACION, 'Administraci&oacute;n');
define(_MENU_ADMIN_COMERCIO, 'Comercio');
define(_MENU_ADMIN_SETUP, 'Configuraci&oacute;n');
define(_MENU_ADMIN_MENSAJE, 'Mensajes');
define(_MENU_ADMIN_ADMIN, 'Admin');
define(_MENU_ADMIN_PERSONALES, 'Datos Personales');
define(_MENU_ADMIN_GRUPOS, 'Grupos');
define(_MENU_ADMIN_ACCESOS, 'Accesos');
define(_MENU_ADMIN_USUARIO, 'Usuarios');
define(_MENU_ADMIN_BITACORA, 'Bit&aacute;cora');
define(_MENU_ADMIN_EXIT, 'Salir');
define(_MENU_ADMIN_REPORTE, 'Reportes');
define(_MENU_ADMIN_COMERCIOS, 'Comercios');
define(_MENU_ADMIN_PALABRA, 'Desc. Palabra secr.');
define(_MENU_ADMIN_COMPROBACION, 'Comprobaci&oacute;n firma');
define(_MENU_ADMIN_TRANSACCIONES, 'Transacciones');
define(_MENU_ADMIN_OLDTRANSACCIONES, 'Hist&oacute;rico de Transacciones');
define(_MENU_ADMIN_CONSOLIDADO, 'Consolidado');
define(_MENU_ADMIN_DOCUMENTACION, 'Documentaci&oacute;n');
define(_MENU_ADMIN_DOCLEGAL, 'Documentaci&oacute;n Legal');
define(_MENU_ADMIN_COMPARACION, 'Comparaci&oacute;n');
define(_MENU_ADMIN_PAGODIRECTO, 'Pago en línea');
define(_MENU_ADMIN_PAGO, 'Clientes');
define(_MENU_ADMIN_TICKET, 'Tickets');
define(_MENU_ADMIN_INSTICKET, 'Poner Ticket');
define(_MENU_ADMIN_VIETICKET, 'Ver Tickets');
define(_MENU_ADMIN_PAGOCLIENTE, 'Pago a Comercios');
define(_MENU_ADMIN_CENTRAL, 'Central de Venta');
define(_MENU_ADMIN_TEMPORADA, 'Temporadas');
define(_MENU_ADMIN_PRODUCTO, 'Producto');
define(_MENU_ADMIN_CARACTERISTICA, 'Característica');
define(_MENU_ADMIN_CANT, 'Cantidad');
define(_MENU_ADMIN_PRECIO, 'Precio');
define(_MENU_ADMIN_VENTA, 'Bur&oacute; de Ventas');
define(_MENU_ADMIN_TRANSFERENCIA, 'Transferencias');
define(_MENU_ADMIN_TRANSFERENCIA_MOD, 'Solicitud de Pago por Transferencia Bancaria');
define(_MENU_ADMIN_IPDENEGADA, 'IPs Bloqueadas');
define(_MENU_ADMIN_CIERRES, 'Cierres');
define(_MENU_ADMIN_VERCIERRES, 'Ver Cierres');
define(_MENU_ADMIN_AVISOS, 'Avisos de Transferencias');
define(_MENU_ADMIN_SMS, 'Control SMS');
define(_MENU_MENU, 'Menú');
define(_MENU_MENU_CAMBIO, 'Tasas de cambio');
define(_MENU_ADMIN_VOUCHER, 'Personalización Voucher');
define(_MENU_ADMIN_TRFINS, 'Inserta Transferencia');
define(_MENU_ADMIN_CMPTRN,'Compara transc / Bancos');
define(_MENU_ADMIN_CAMBIO,'Cambio');
define(_MENU_ADMIN_CAMBIOCUC,'Cambio del CUP');
define(_MENU_ADMIN_CAMBIOUSD,'Cambio de Divisa');
define(_MENU_ADMIN_PASARELA,'Comercio - TPV');
define(_MENU_ADMIN_IDIOMA,'Idiomas');
define(_MENU_ADMIN_INVITACION,'Invitación de pago');
define(_MENU_ADMIN_CONDICIONES, 'Condiciones de pago');
define(_MENU_ADMIN_VOUCHER, 'Comprobante de pago');
define(_MENU_ADMIN_PONCIERRE, 'Control de Cierres');
define(_MENU_ADMIN_ANALISIS, 'An&aacute;lisis de TPVs');
define(_MENU_ADMIN_SINCR, 'Sincr. Bancos');
define(_MENU_ADMIN_DATOS, 'Datos Varios');
define(_MENU_TRNSF_AMF, 'Transferencias Bidaiondo');
define(_MENU_TITFICH, 'Ficheros a Titanes');
define(_MENU_ADMIN_PASA, 'Configuración TPV');
define(_MENU_ADMIN_PAISLIM, 'Limites de países por pasarela');
define(_MENU_ADMIN_ECON, 'Destinatarios de Cierres');
define(_MENU_ADMIN_REPTRANS, 'Transferencias');
define(_MENU_ADMIN_CIERREADEL, 'Aviso de Cierre adelantado');
define(_MENU_ADMIN_IFRCSV, 'Cambia CSV');
define(_MENU_ADMIN_LOTE, 'Pago por Lotes');
define(_MENU_ADMIN_PCANT, 'Pasarela por num. operaciones');
define(_MENU_ADMIN_PTMPO, 'Pasarela por tiempo');
define(_MENU_ADMIN_SFACT, 'Subir Facturas');
define(_MENU_ADMIN_SRECLAM, 'Subir Reclamaciones');
define(_MENU_ADMIN_CLONPAS, 'Clonar Pasarela');
define(_MENU_ADMIN_PRUEBA, 'Prueba');
define(_MENU_ADMIN_ERRORES, 'Verificación de errores');
define(_MENU_ADMIN_RECLAMA, 'Gesti&oacute;n de Reclamaciones');
define(_MENU_ADMIN_ROTA, 'Rotaci&oacute;n de Pasarelas');
define(_MENU_ADMIN_COMPAS, 'Asocia Pasarelas a Comercios');
define(_MENU_ADMIN_TRAZA, 'Bit&aacute;cora T&eacute;cnica');
define(_MENU_ADMIN_TASATEMP, 'Fijar tasas a banco');


//SMS

//Datos personales
define(_PERSONAL_TITULO, 'Datos Personales');
define(_PERSONAL_IDENT, 'Usuario');
define(_PERSONAL_IDIOMA, 'Idioma');
define(_PERSONAL_FECHA, 'Formato de fecha');
define(_PERSONAL_HORA, 'Formato de hora');
define(_PERSONAL_NUM, 'Formato de n&uacute;mero');
define(_PERSONAL_ESP, 'Español');
define(_PERSONAL_ING, 'Inglés');
define(_PERSONAL_ITA, 'Italiano');
define(_PERSONAL_IDIOMA_ALT, 'Idioma del Cliente');
define(_PERSONAL_PASS, 'Contraseña');
define(_PERSONAL_REPASS, 'Reescribir Contraseña');
define(_PERSONAL_ALERT_CONTRAS, 'Las contraseñas no son iguales.');
define(_PERSONAL_TEXTO1, 'A usted se le ha concedido el acceso a la administración del Concentrador. Puede entrar con los siguientes datos:');
define(_PERSONAL_REC_CORREO, 'Recibir correo por<br />transacción');
define(_PERSONAL_REC_CORREO2, 'Recibir correo por transacción');
define(_PERSONAL_QUERY, 'Guardar consulta');
define(_PERSONAL_QUERYEXP, 'Guarda la última consulta corrida en la página Reporte');
define(_PERSONAL_MIPERF, 'mi perfil');

//Grupos de trabajo
define(_GRUPOS_TITULO, 'Grupos de Acceso');
define(_GRUPOS_NOMBRE, 'Nombre Grupo');
define(_GRUPOS_ORDEN, 'Orden');
define(_GRUPOS_EDIT_DATA, 'Editar Datos');
define(_GRUPOS_BORRA_DATA, 'Borrar Registro');
define(_GRUPOS_ANULA_DATA, 'Anular Transacci&oacute;n');
define(_GRUPOS_DEVUELVE_DATA, 'Devolver Transacci&oacute;n');
define(_GRUPOS_PAGA_COMERCIO, 'Pagar al Comercio');
define(_GRUPOS_GRUPO, 'Grupo');
define(_GRUPOS_FACTURA, 'Ver Transferencia');
define(_GRUPOS_FACTURA_VER, 'Ver Factura');
define(_GRUPOS_FACTURA_CANCEL, 'Cancelar Factura');
define(_GRUPOS_ALERTA_FACT, 'Esta transacción no es una transferencia o una preautorización');
define(_GRUPOS_ENVIA_MI, 'Envíamela');
define(_GRUPOS_ENVIA_CLI, 'Envía Cliente');
define(_GRUPOS_SOLDEVOL, 'Solicitar devolución');
define(_GRUPOS_SOLDEVOL_ERROR, 'Esta transferencia no se puede devolver');

//Accesos
define(_ACCESOS_TITULO, 'Accesos');

//IPs
define(_IP_BLOQUEADA, 'Bloqueada');
define(_IP_FECHA_DESBLOQUEADA, 'Fecha Desbloqueada');
define(_IP_DESBLOQUEADAPOR, 'Desbloqueada por');
define(_IP_VBLOQUEADA, 'Veces bloqueada');
define(_IP_TRNSACEPTADAS, 'Transacciones aceptadas');
define(_IP_TRNSDENEGADAS, 'Transacciones denegadas');

//Usuarios
define(_USUARIO_TITULO, 'Usuarios');
define(_USUARIO_ACTIVO, 'Activo');
define(_USUARIO_BORRA_PASS, 'Borra Contrase&ntilde;a');
define(_USUARIO_BORRA_PASS_ALERT, 'Marcar Si causa la pérdida del password.');
define(_USUARIO_FECHA_ULTIMA, '&Uacute;lt. Acceso');

//Bitacora
define(_BITACORA_TITULO, 'Bit&aacute;cora');
define(_BITACORA_ALERT_FECHAS, 'Para obtener la informaci&oacute;n correspondiente a un d&iacute;a, debe poner dicho d&iacute;a como Fecha Fnicio y el d&iacute;a siguiente en Fecha Final');
define(_BITACORA_ALERT_FECHASDIF, 'La fecha de inicio no puede ser mayor que la final.');
define(_BITACORA_TEXT, 'Texto');

//Comercios
define(_COMERCIO_TITULO, 'Comercio');
define(_COMERCIO_MONEDA_PAGO, 'Pago Comercio');
define(_COMERCIO_MONEDA, 'Moneda');
define(_COMERCIO_ID, 'Id');
define(_COMERCIO_ALTA, 'Fecha<br />Alta');
define(_COMERCIO_ESTADO, 'Estado');
define(_COMERCIO_MOVIMIENTO, 'F. Ult.<br />Movim.');
define(_COMERCIO_HISTOR, 'Hist&oacute;rico');
define(_COMERCIO_PALABRA, 'Palabra Secreta');
define(_COMERCIO_ACTIVO, 'Activo');
define(_COMERCIO_ACTIVITY, 'Entorno');
define(_COMERCIO_ACTIVITY_DES, 'Desarrollo');
define(_COMERCIO_ACTIVITY_PRO, 'Producci&oacute;n');
define(_COMERCIO_IDENTIF, 'Identificaci&oacute;n');
define(_COMERCIO_ACTIVITY_PRO_ALERT, 'El comercio debe tener realizada al menos una transacci&oacute;n en Desarrollo.');
define(_COMERCIO_HISTORIA, 'Historia');
define(_COMERCIO_URL, 'Url de la página que recibe el resultado de la transacci&oacute;n');
define(_COMERCIO_URL_DIRECTA, 'Url de la página que recibe el resultado de la transacci&oacute;n directamente');
define(_COMERCIO_URL_CORTA, 'Url');
define(_COMERCIO_PREFIJO, 'Prefijo del Comercio');
define(_COMERCIO_CONDICIONES, 'Condiciones de Pago');
define(_COMERCIO_CORREO_P, 'Plantilla de correo');
define(_COMERCIO_CARACTERES, 'Dos caracteres');
define(_COMERCIO_SMS, 'Envío de SMS');
define(_COMERCIO_VENDE, 'Permitir a mis usuarios/vendedores ver lo vendido por otros');
define(_COMERCIO_TELEFONO, 'Tel&eacute;fono');
define(_COMERCIO_FORMATO_INT, 'en formato internacional: 00codpais..');
define(_COMERCIO_PASARELAP, 'Pasarela para la Web');
define(_COMERCIO_PASARELAM, 'Pasarela para Pagos Diferidos y al Momento');
define(_COMERCIO_EMAIL_SUBJECT, 'Aviso de Transacción');
define(_COMERCIO_EMAIL_MES, "Estimado cliente,<br /><br />Se ha enviado por correo una operación de pago diferido con identificador "
		. "{trans}, por concepto de pago {servicio} al cliente {nombre} con un importe de {importe} {moneda}."
		. "<br /><br />Deberá esperar por el resultado del pago del cliente.<br /><br />Gracias por escogernos.<br /><br />Administrador de Comercios.");
define(_COMERCIO_SOLC_SI, 'Solicitud de Pago enviada satisfactoriamente.');
define(_COMERCIO_FACT_SI, 'Su solicitud de pago por transferencia bancaria ha sido enviada a revisión.');
define(_COMERCIO_CODE_YA, 'El No. de Transacción asignado ya existe.<br />Seleccione uno nuevo.');
define(_COMERCIO_CODEVALID, 'El No. de Transacción es inválido, debe ser un alfanumérico<br />sin espacios y de hasta 19 caracteres.');
define(_COMERCIO_SECRETA_NO, "El comercio a&uacute;n no tiene creada la palabra secreta,<br />por favor navegue a la opci&oacute;n del men&uacute; '"._MENU_ADMIN_COMERCIO." / "._MENU_ADMIN_PALABRA."' y desc&aacute;rguela");
define(_COMERCIO_ERROR_INVIT, "Se ha producido un error al enviar la invitación, por favor intente nuevamente.");
define(_COMERCIO_PAGO, 'Realizar Pago');
define(_COMERCIO_GENERA, 'Si la deja en blanco el sistema la genera');
define(_COMERCIO_PAGOA, 'Forma de Pago');
define(_COMERCIO_ALMOMENT, 'Al momento');
define(_COMERCIO_DIFERI, 'Diferido');
define(_COMERCIO_SER, 'Servicio');
define(_COMERCIO_PASARELA, 'Pasarela');
define(_COMERCIO_PAGAR, 'Pagada');
define(_COMERCIO_DIRECCION, 'Datos del comercio. Dirección, telf. fax. para transferencias bancarias');
define(_COMERCIO_INVACTIVA, 'Invitación Activa por');
define(_COMERCIO_INVACTIVAEXPL, ' días, solo para pagos diferidos');
define(_COMERCIO_TASA, 'Tasa');
define(_COMERCIO_EUROSC, 'Euros Cambio');
define(_COMERCIO_CODIGO, 'C&oacute;digo HTML');
define(_COMERCIO_VERVOUCHER, 'Cómo se verá?</span>  (Debe haber realizado al menos una trasacción a través del Concentrador)');
define(_COMERCIO_ERROR_HTML, 'No se introdujo código HTML');
define(_COMERCIO_ERROR_IDI, 'No se introdujo idioma válido');
define(_COMERCIO_ERROR_COM, 'No se introdujo comercio');
define(_COMERCIO_DAT, 'Datos correctamente guardados');
define(_COMERCIO_TARJETA, 'Tarjeta para el pago');
define(_COMERCIO_TARJETA_NUM, 'N&uacute;mero de la tarjeta');
define(_COMERCIO_TARJETA_EXPLICA, 'Escriba el n&uacute;mero de la tarjeta de pago (16 d&iacute;gitos)');
define(_COMERCIO_TARJETA_MES, 'Vencimiento - Mes');
define(_COMERCIO_TARJETA_ANO, 'A&ntilde;o');
define(_COMERCIO_TARJETA_CVV2, 'Código de seguridad');
define(_COMERCIO_TARJETA_CVV2_EXPLICA, 'Tres d&iacute;gitos que aparecen en el reverso de la tarjeta');
define(_COMERCIO_DIRECCION_USR, 'Direcci&oacute;n');
define(_COMERCIO_DIRECCION_EXPLICA, 'Direcci&oacute;n del due&ntilde;o de la tarjeta tal y como tiene registrado en su banco');
define(_COMERCIO_TELEFONO_EXPLICA, 'Tel&eacute;fono del due&ntilde;o de la tarjeta tal y como tiene registrado en su banco');
define(_COMERCIO_VISA, 'Visa, Mastercard, Otras');
define(_COMERCIO_AMEX, 'American Express');
define(_COMERCIO_EXPLICA, 'La selecci\u00f3n del pago con American Express implica que este s\u00f3lo se realice por pasarela Segura, por lo que deber\u00e1 chequear en la p\u00e1gina de inicio del Administrador de Comercios, que la tarjeta haya sido emitida en alguno de los pa\u00edses adscritos al programa de seguridad SafeKey.');
define(_COMERCIO_OPERACION, 'Operaci&oacute;n');

//descarga de palabra
define(_PALABRA_GENERAR, 'Generar');
define(_PALABRA_RETYPE, 'Reescriba Contrase&ntilde;a');
define(_PALABRA_EXPLICA, '<br />Al hacer clic en el bot&oacute;n -Generar- se desencadenar&aacute; el proceso para generar la palabra secreta que usar&aacute; su comercio para firmar las transacciones que se realicen a trav&eacute;s de nuestro TPV. Es importante que Usted guarde de forma segura la misma.<br /><br />La nueva palabra secreta generada invalida las anteriores si existieren, por lo que las transacciones de ahora en lo adelante deber&aacute;n ser firmadas con ella.<br /><br />');
define(_PALABRA_EXPLICA_NO, '<br />Hubo un error en la generaci&oacute;n de la palabra secreta, debe tratar de generarla nuevamente.<br /><br /> Si persiste el error debe ponerse en contacto con el <a href=\'mailto:'._CORREO_SITE.'\'>administrador.</a><br /><br />');
define(_PALABRA_EXPLICA_DESCARGA, '<br /><br />Si la descarga no comienza autom&aacute;ticamente puede seguir en siguiente <a href=\'componente/comercio/bajando.php?id={enlace}.txt\'>enlace.</a><br /><br />');

//Inicio
define(_INICIO_TITLE_ALL, 'Datos de los Comercios');
define(_INICIO_TITLE_MENOS, 'Datos');
define(_INICIO_CANT_TOTAL, 'Cantidad total de comercios activos: ');
define(_INICIO_CANT_PRODUCC, 'Cantidad de comercios en Producci&oacute;n: ');
define(_INICIO_CANT_DESARR, 'Cantidad total de comercios en Desarrollo: ');
define(_INICIO_NUMR_TRANSAC_ACEPT, 'Cantidad de transacciones aceptadas: ');
define(_INICIO_NUMR_TRANSAC_DENEG, 'Cantidad de transacciones denegadas: ');
define(_INICIO_VALR_TRANSAC, 'Valor de las transacciones aceptadas: ');
define(_INICIO_COMERCIO_NUMR, 'N&uacute;mero de comercio: ');
define(_INICIO_COMERCIO_MODO, 'Modo de trabajo del comercio: ');
define(_INICIO_FECHA_MODO, '&Uacute;ltima fecha de cambio de modo: ');
define(_INICIO_MES, 'Este mes');
define(_INICIO_SEM, 'Esta semana');
define(_INICIO_HOY, 'Hoy');
define(_INICIO_TODO, 'Acumulado hasta hoy');
define(_INICIO_COMERCIO, 'Comercio');
define(_INICIO_CANT_TRANSACCIONES, 'Transacciones<br />aceptadas');
define(_INICIO_VALOR, 'Valor');
define(_INICIO_CONECTADOS, 'Están conectados en este momento:');
define(_INICIO_NOCONECTADOS, 'No hay otro usuario conectado');
define(_INICIO_NOTICIA, 'Noticias');
define(_INICIO_TRBAJ, 'Trabajando');
define(_EUROA, '1 Euro a');
define(_VERTASA, 'Ver tasas de cambio');
define(_HORA_ESP, 'Hora de España');
define(_HORA_CUB, 'Hora de Cuba');
define(_VENTAS_X_TIENDA, '10 mejores Comercios');
define(_SALES_X_MES, '10 mejores Comercios en el mes');
define(_TIENDA_TIT, 'Tienda');
define(_INICIO_VALORDIA, 'valor d&iacute;a');
define(_INICIO_VALORMENSUAL, 'valor mensual');
define(_INICIO_VALORANUAL, 'valor anual');
define(_INICIO_CANTDIA, 'cantidad d&iacute;a');
define(_INICIO_CANTMES, 'cantidad mensual');
define(_INICIO_CANTANO, 'cantidad anual');

//Setup
define(_SETUP_TITLE, 'Configuraci&oacute;n');
define(_SETUP_EMAIL_CONT, 'Correo Contacto');
define(_SETUP_PALABR_OFUS, 'Palabra Secreta Ofuscada');
define(_SETUP_CONTRASENA, 'Contraseña de ofuscaci&oacute;n');
define(_SETUP_COMERCIO, 'Identificador del comercio');
define(_SETUP_PUNTO, 'Identificador del terminal');
define(_SETUP_LOCALIZADOR, 'Localizador');
define(_SETUP_URL_COMERCIO, 'Url Comercio');
define(_SETUP_URL_DIR, 'Url Directorio');
define(_SETUP_URL_TPV, 'Url TPV');
define(_SETUP_MESES, 'Meses a mantener las transacciones en la tabla principal');
define(_SETUP_DATOS_TPV, 'Datos TPV Producci&oacute;n');
define(_SETUP_DATOS_TPV_TEST, 'Datos TPV Integraci&oacute;n');
define(_SETUP_MENSAJE, 'Mensaje');

//Reportes
define(_REPORTE_TITLE, 'Reportes');
define(_REPORTE_TASK, 'Buscar por');
define(_REPORTE_FECHA_INI, 'Fecha inicio');
define(_REPORTE_FECHA_FIN, 'Fecha terminaci&oacute;n');
define(_REPORTE_REF_COMERCIO, 'Referencia del comercio');
define(_REPORTE_REF_BBVA, 'Referencia del Banco');
define(_REPORTE_VALOR, 'Valor');
define(_REPORTE_ALCOMERCIO, 'Pagada al comercio');
define(_REPORTE_VALOR_INICIAL, 'Valor Inicial');
define(_REPORTE_FECHA_MOD, 'Fecha modificada');
define(_REPORTE_ESTADO, 'Estado');
define(_REPORTE_TOTAL, 'Valor Total');
define(_REPORTE_IDENTIFTRANS, 'Identificador de transacci&oacute;n');
define(_REPORTE_TODOS, 'Todos');
define(_REPORTE_FECHA, 'Fecha');
define(_REPORTE_STATUS, 'Estado de la transacci&oacute;n');
define(_REPORTE_PRINT, 'Imprime Reporte');
define(_REPORTE_CSV, 'Exportar a CSV');
define(_CONSOLIDADO_TITLE, 'Consolidado');
define(_REPORTE_DESCUENTO, 'Valor a descontar');
define(_REPORTE_NOPUEDO, 'El valor a descontar tiene que ser menor o igual al valor actual de la transacción.');
define(_REPORTE_DESCUENTO_TITLE, 'Descuento');
define(_REPORTE_ERROR, "Caract. error");
define(_REPORTE_IP, "Dir IP");
define(_REPORTE_PAIS, "País");
define(_REPORTE_CANT, 'Cantidad');
define(_REPORTE_RECLAMADA, 'Reclamada');
define(_REPORTE_ACEPTADA, 'Aceptada');
define(_REPORTE_APOBADA, 'Aprobada');
define(_REPORTE_CANCELADA, 'Cancelada');
define(_REPORTE_REALMENSUAL, 'Real Mensual');
define(_REPORTE_PROMMENSUAL, 'Promedio Mensual');
define(_REPORTE_PENDIENTE, 'Pendiente');
define(_REPORTE_DENEGADA, 'Denegada');
define(_REPORTE_PROCESADA, 'No Procesada');
define(_REPORTE_ANULADA, 'Anulada');
define(_REPORTE_VENCIDA, 'Vencida');
define(_REPORTE_DEVUELTA, 'Devuelta');
define(_REPORTE_PROCESO, 'En Proceso');
define(_REPORTE_ACEPTDEV, _REPORTE_ACEPTADA.' y '._REPORTE_DEVUELTA);
define(_REPORTE_EUROE, 'Euro Equivalente');
define(_REPORTE_TIPOG, 'Tipo de gráfico');
define(_REPORTE_BARRASV, 'Barras verticales');
define(_REPORTE_BARRASH, 'Barras horizontales');
define(_REPORTE_PUNTOS, 'Puntos');
define(_REPORTE_LINEAS, 'Líneas');
define(_REPORTE_MESES, 'meses');
define(_REPORTE_MESESM, 'Meses');
define(_REPORTE_VALORES, 'Valores');
define(_REPORTE_DIAS, 'días');
define(_REPORTE_DIASM, 'Días');
define(_REPORTE_CLIENTE, 'Cliente');
define(_REPORTE_ESTIMADO, 'Estimado del último mes:');
define(_REPORTE_TRANSFERENCIA, 'Número de la Transferencia');
define(_REPORTE_TRANSFERENCIA_ID, 'Identificador de la Transferencia');
define(_REPORTE_TRANSFERENCIA_ESTADO, 'Estado de la Transferencia');
define(_REPORTE_SOLDEVOL, 'Solicitud de Devolución');
define(_REPORTE_SOLRECLAMACION, 'Solicitud de Reclamación');

//Comprobacion
define(_COMPRUEBA_TITLE, 'Comprobar firma');
define(_COMPRUEBA_COMPROBAR, 'Verificar');
define(_COMPRUEBA_COMERCIO, 'Identificador de Comercio');
define(_COMPRUEBA_TRANSACCION, 'No. Transacci&oacute;n');
define(_COMPRUEBA_IMPORTE, 'Importe');
define(_COMPRUEBA_MONEDA, 'Moneda');
define(_COMPRUEBA_OPERACION, 'Tipo de Operaci&oacute;n');
define(_COMPRUEBA_PAGO_OPERAC, 'de Pago');
define(_COMPRUEBA_CANCELA_OPERAC, 'de Cancelaci&oacute;n');
define(_COMPRUEBA_MD5, 'Firma Digital');

//Bitacora
define(_BITACORA_ALERT_FECHASDIF, 'Fecha de inicio posterior a fecha final.');

//Tickets
define(_TICKET_CONSULTA, "\n\nSu ticket tiene el número {numer}. Puede consultar el estado en:\n");
define(_TICKET_OK, 'Ticket enviado satisfactoriamente');
define(_TICKET_NOOK, 'Hubo un error en el envío del ticket al administrador<br />,por favor hágalo nuevamente o póngase en contacto con el mismo por correo a la dirección');
define(_TICKET_DESCR, 'Describa el problema');
define(_TICKET_ACTIVO, 'Activo');
define(_TICKET_CERRADO, 'Cerrado');
define(_TICKET_ID, 'Identificador del Ticket');
define(_TICKET_ASUNTO, 'Asunto');
define(_TICKET_FENTRADA, 'Fecha Entrada');
define(_TICKET_FCERRADO, 'Fecha Cerrado');
define(_TICKET_DESCRI, "Descripci&oacute;n");
define(_TICKET_SOLIC, "Solicitar al cliente");
define(_TICKET_CONT, 'Contiene');
define(_TICKET_PAGO, 'Comprobante de Pago');
define(_TICKET_AUTOR, 'Autorizo');
define(_TICKET_FIRMA, 'Firma');
define(_TICKET_SUPAGO, 'Su pago se ha procesado correctamente con los siguientes datos:');
define(_TICKET_TELEF, 'Tel&eacute;fono d&oacute;nde localizarle');

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
define(_PROD_NOMBRE_DESC, 'Nombre del producto, pj: habitación doble, habitación junior, excursión a Ciénaga de Zapata');
define(_PROD_DESCR_DESC, 'Descripción del producto, texto que puede servir de guía para la venta de dos productos con el mismo nombre');
define(_PROD_STOCK, 'Venta contra almacén');
define(_PROD_STOCK_DESC, 'Venta contra stock o almacén, el producto deberá tener existencias para poder venderse. 
	De lo contrario se podrá vender sin consultar almacén');
define(_PROD_DESC_FECHA1, 'Fecha de inicio a partir de la cual el producto estará disponible para la venta.');
define(_PROD_DESC_FECHA2, 'Fecha hasta la cual el producto estará disponible para la venta.');
define(_PROD_CARACT_DESC, 'Características a aplicar a los productos');
define(_PROD_MONEDA_DESC, 'Moneda en que se venderá el producto, los valores de las características afectarán a este producto en esta moneda.');
define(_PROD_CANT_DESC, 'Disponibilidades del producto para un intervalo de fechas determinado.');

//Buró de ventas
define(_BURO_TITULO1, "Wizard para la venta");
define(_BURO_DESC_FECHA1, 'Fecha de realizacion de la venta. Si vende productos no debe especificar la fecha Final. Si vende capacidades debe poner el inicio de la reserva.');
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
define(_CIERRE_DESDE, 'Desde');
define(_CIERRE_HASTA, 'Hasta');
define(_CIERRE_VALOR, 'Por valor');
define(_CIERRE_DIARIO, 'Diario');
define(_CIERRE_SEMANAL, 'Semanal');
define(_CIERRE_QUINCENAL, 'Quincenal');
define(_CIERRE_MENSUAL, 'Mensual');
define(_CIERRE_CONSECUTIVO, 'Consecutivo');
define(_CIERRE_FECHAINI, 'Fecha Inicio');
define(_CIERRE_FECHAFIN, 'Fecha Fin');
define(_CIERRE_INTEGRA, 'Integración');
define(_CIERRE_MENSUAL, 'Mensualidad');
define(_CIERRE_TARJETA, 'Uso tarjeta');
define(_CIERRE_COMIS, 'Comisión');
define(_CIERRE_RETROC, 'Devoluciones');
define(_CIERRE_TRANSF, 'Transferencias');
define(_CIERRE_SWIFT, 'Swift');
define(_CIERRE_COSTOB, 'Costo Bancario');
define(_CIERRE_DESC, 'Total descuentos');
define(_CIERRE_DEVOL, 'Total devol');
define(_CIERRE_TOTAL, 'Total sin desc.');
define(_CIERRE_PAGAR, 'A Pagar');
define(_CIERRE_VCIERRE, 'Ver Cierre');

//Avisos
define(_MENU_ADMIN_AVISO, 'Avisos de Transferencias de Fondos');
define(_AVISO_NOMBRE, 'Nombre Empresa');
define(_AVISO_REMITENTE, 'Datos del remitente');
define(_AVISO_OBSERVA, 'Concepto / Observaciones');
define(_AVISO_SI, 'El aviso ha sido enviado satisfactoriamente a su correo.');
define(_AVISO_CODIGO, 'Código de la transferencia');
define(_AVISO_NUMERO, 'Número transferencia');
define(_AVISO_VALOREU, 'Importe en Euros');
define(_AVISO_TASA, 'Tasa de cambio');

//Devoluciones
define(_DEVOL_TIT, "Solicitud de Devolución");
define(_DEVOL_MONT, "Cantidad a Devolver");

//movil
define(_MOVIL_OPERAC, "Cantidad de operaciones");
define(_MOVIL_OPERAC_ACEPT, "Aceptadas, Devueltas y Canceladas");
define(_MOVIL_OPERAC_RECHAZ, "Denegadas y Pendientes");
define(_MOVIL_VALORES, "Valor de las operaciones");
define(_MOVIL_VALORES_VALOR, "Valor");
define(_MOVIL_VALORES_PROM, "Promedio");
define(_MOVIL_ANO, "Año");
define(_MOVIL_MES, "Mes");
define(_MOVIL_SEMANA, "Semana");
define(_MOVIL_ACEPT_COMERC, "Cambio de estado realizado");
define(_MOVIL_TRANSACCION, "Transacción");
define(_MOVIL_COMERCIO, "Comercio");
define(_MOVIL_IDENTIFICADOR, "Identificador");
define(_MOVIL_MONEDA, "Moneda");
define(_MOVIL_VALOR_INI, "Valor Inicial");
define(_MOVIL_VALOR, "Valor");
define(_MOVIL_FECHA_INI, "Fecha Inicial");
define(_MOVIL_FECHA_MOD, "Fecha Mod");
define(_MOVIL_ESTADO, "Estado");
define(_MOVIL_ESTADO_TRANS, "Estado Trans");
define(_MOVIL_ENTORNO, "Entorno");
define(_MOVIL_ERROR, "Error");
define(_MOVIL_PASARELA, "Pasarela");
define(_MOVIL_CODIGO, "Código del Banco");
define(_MOVIL_PAIS, "País");
define(_MOVIL_PAGADO, "Pagado al Comercio");
define(_MOVIL_EUROEQ, "Euro Equivalente");
define(_MOVIL_NOCOINC, "No se encontraron coincidencias");
define(_MOVIL_MOSTRANDO, "Mostrando");
define(_MOVIL_A, "a");
define(_MOVIL_DE, "de");
define(_MOVIL_RECORDS, "records");
define(_MOVIL_PROCES, "Procesando la información espere...");
define(_MOVIL_PRIMERO, "Primero");
define(_MOVIL_ANTERIOR, "Anterior");
define(_MOVIL_PROXIMO, "Próximo");
define(_MOVIL_ULTIMO, "Último");
define(_MOVIL_NODATA, "No hay datos disponibles en la tabla");
define(_MOVIL_CLIENTE, "Cliente");
define(_MOVIL_FECHA, "Fecha");
define(_MOVIL_ADMINIST, "Usuario");
define(_MOVIL_EMAIL, "Correo");
define(_MOVIL_PAGO_MOM, "Forma de Pago");
define(_MOVIL_SERVIC, "Concepto");
define(_MOVIL_ROL, "Rol");
define(_MOVIL_FECHAV, "Última fecha de visita");
define(_MOVIL_IDIOMA, "Idioma");
define(_MOVIL_IDIOMA_ES, "Idioma Español");
define(_MOVIL_IDIOMA_EN, "English Language");
define(_MOVIL_EDITA, "Editar");
define(_MOVIL_NUEVO, "Nuevo");
define(_MOVIL_DESACT, "Desactivar");
define(_MOVIL_ACT, "Activar");
define(_MOVIL_BCONTR, "Borrar contraseña");
define(_MOVIL_ALMOMENT, 'Pago al momento');
define(_MOVIL_DIFERI, 'Pago diferido');
define(_MOVIL_DIA, 'día');
define(_MOVIL_HOY, 'Hoy');
define(_MOVIL_MES, 'Este mes');
define(_MOVIL_CANT_TRANS, 'Cantidad de Transacciones');
define(_MOVIL_VAL_ACEP, 'Valor de las Aceptadas');

//Transferencias
define(_TRNS_ORD, 'Ordenante');
define(_TRNS_NUM, 'Operación num.');
define(_TRNS_DIV, 'DIV');
define(_TRNS_CBO, 'CBO');
define(_TRNS_LQD, 'Liquido Total');
define(_TRNS_MTV, 'Motivo');
define(_INV_VIEJA_TIT, 'Invitación de transferencia ha caducado');
define(_INV_VIEJA, "Estimado (a){usuario}:<br><br>
 
Su cliente {cliente} no ha aceptado los Términos y Condiciones, de la Orden de Pago realizada por usted el {fecha} en el plazo establecido de 10 días hábiles 
y por ende no ha recibido la misma.<br><br>
 
En caso de proceder, puede realizar una nueva invitación de Transferencia.<br><br>
 
Administrador de Comercios");
define(_PAGO_TARJENO, "Si usted está cobrando una tarjeta no disponible en este listado, comuniquese con nosotros 
(<a href='https://www.administracomercios.com/admin/index.php?componente=ticket&pag=ticketin'>Poner Ticket</a>)");
define(_PAGO_TARJETA, "Escoja el m&eacute;todo de pago con el que el cliente pagará");

//Idiomas
define(_IDIOMA_SALIDAOK, 'Datos correctamente guardados');

//Errores
define(_ERROR_UNO, 'ERROR !!.. Su operación no pudo ser procesada satisfactoriamente. Inténtelo nuevamente.');
define(_ERROR_DOS, 'Error !!.. Usted ha excedido el límite máximo permisible por operación que es de ');
define(_ERROR_TRES, 'Error !!.. Usted no cumple con el límite mínimo permisible por operación que es de ');
define(_ERROR_CUATRO, 'Error !!.. Usted ha excedido el límite máximo acumulado por día que es de ');
define(_ERROR_CINCO, 'Error !!.. Usted ha excedido el límite máximo acumulado por mes que es de ');
define(_ERROR_SEIS, 'Error !!.. Usted ha excedido el límite máximo acumulado por año que es de ');
define(_ERROR_SIETE, 'Error !!.. Usted ha excedido el límite máximo permisible de operaciones por día que es de ');
define(_ERROR_OCHO, 'Error !!.. Usted ha excedido el límite máximo permisible de operaciones para el mes que es de ');
define(_ERROR_NUEVE, 'Error !!.. Usted ha excedido el límite máximo permisible de operaciones para el año que es de ');

//Correos
define(_CORREO_CAMRECLAM, "Estimado cliente:<br><br>
Se ha actualizado una operaci&oacute;n que se encuentra en proceso de reclamaci&oacute;n.<br><br>
Para m&aacute;s detalles y/o responder, acceda a trav&eacute;s de la opci&oacute;n de <a href='index.php?componente=comercio&pag=reclamaciones&cambiar={edit}' style='font-style: italic; '>Gesti&oacute;n de Reclamaciones</a> en nuestra plataforma.<br><br>
Por favor, no responda a este mensaje.<br><br>
Atentamente,<br><br>
Atenci&oacute;n a Clientes<br>
<span style='font-weight:bold'>Bidaiondo S.L.</span><br>
<a href='mailto:atencionaclientes@bidaiondo.com'>atencionaclientes@bidaiondo.com</a><br>
Tel (53) 7 204 4424 <br><br>

<span style='color:#888; font-size:8px; font-style:italic'>El contenido de este correo electr&oacute;nico y sus anexos son estrictamente confidenciales, secretos y restringidos. La divulgaci&oacute;n o el suministro, en todo o en parte, a cualquier tercero, no podr&aacute; ser realizada sin el previo, expreso y escrito consentimiento de BIDAIONDO S.L.   Las opiniones contenidas en este mensaje y en los archivos adjuntos, pertenecen exclusivamente a su remitente y no representan la opini&oacute;n de BIDAIONDO S.L., salvo que se diga expresamente y el remitente est&eacute; autorizado para ello BIDAIONDO S.L. advierte expresamente que el env&iacute;o de correos electr&oacute;nicos a trav&eacute;s de Internet no garantiza ni la confidencialidad de los mensajes, ni su integridad y correcta recepci&oacute;n, por lo que BIDAIONDO S.L., no asume responsabilidad alguna por dichas circunstancias.
En caso que no sea el destinatario y haya recibido este mensaje por error, agradecemos lo comunique inmediatamente al remitente sin difundir, almacenar o copiar su contenido.<br>
En cumplimiento con el RGPD (UE) 679/2016, le informamos de que sus datos personales son incluidos en ficheros particulares de BIDAIONDO S.L., con la finalidad de mejorar nuestros servicios y productos, as&iacute; como mantenerle informado sobre estos y realizar comunicaciones comerciales. Para ejercitar los derechos previstos en la ley puede dirigirse mediante un correo electr&oacute;nico a: <a href='mailto:atencionaclientes@bidaiondo.com'>atencionaclientes@bidaiondo.com</a></span>");
define(_CORREO_NOTRECLAM, "Estimado cliente:<br><br>
A continuaci&oacute;n la petici&oacute;n de informaci&oacute;n relativa a una reclamaci&oacute;n recibida de un pago realizado a su comercio.<br><br>
Necesitamos revisen lo indicado en el mismo y respondan en los t&eacute;rminos especificados para ello.<br><br>
Para responder, acceda a trav&eacute;s de la opci&oacute;n de <a href='https://www.administracomercios.com/index.php?componente=comercio&pag=reclamaciones&cambiar={edit}' style='font-style: italic; '>Gesti&oacute;n de Reclamaciones</a> en nuestra plataforma.<br><br>
Por favor, no responda a este mensaje.<br><br>

<span style='font-weight:bold; font-size:12px;'>SOLICITUD DE JUSTIFICANTE / DOCUMENTACION ADICIONAL</span><br><br>
Fecha: <b>{fec2}</b><br><br>

Expediente: <b>{idtr}</b><br><br>

Les informamos que hemos recibido una reclamaci&oacute;n de la siguiente operaci&oacute;n realizada en su comercio:<br><br>

Comercio: <b>{com}</b><br>
No. Operaci&oacute;n: <b>{idtr}</b><br>
Fecha: <b>{fec}</b><br>
Importe original: <b>{val}</b><br>
Importe reclamado: <b>{devol}</b><br>
Moneda: <b>{mon}</b><br>
Autorizo: <b>{cod}</b><br><br>

El banco emisor de la tarjeta manifiesta que su cliente <b>{observ}</b><br>
Si est&aacute;n de acuerdo con el cargo, rogamos acepten el mismo a trav&eacute;s de la herramienta de gesti&oacute;n de reclamaciones en nuestra plataforma.<br><br>

En caso contrario procederemos a disputar este importe a la entidad emisora, para lo cual deber&aacute;n remitir la siguiente documentaci&oacute;n:<br>
	<b>{docu}</b><br><br>

En el caso que la documentaci&oacute;n no obre en nuestro poder en el plazo mencionado, se entender&aacute; que el cargo realizado es conforme, sin posibilidad de resoluci&oacute;n posterior aun cuando facilitaran la documentaci&oacute;n indicada.<br>
La respuesta deber&aacute; enviarla a trav&eacute;s de la herramienta de gesti&oacute;n de reclamaciones en nuestra plataforma antes de <b>{fecha1}</b><br><br>

Atentamente,<br><br>
Atenci&oacute;n a Clientes<br>
<span style='font-weight:bold'>Bidaiondo S.L.</span><br>
<a href='mailto:atencionaclientes@bidaiondo.com'>atencionaclientes@bidaiondo.com</a><br>
Tel (53) 7 204 4424 <br><br>

<span style='color:#888; font-size:8px; font-style:italic'>El contenido de este correo electr&oacute;nico y sus anexos son estrictamente confidenciales, secretos y restringidos. La divulgaci&oacute;n o el suministro, en todo o en parte, a cualquier tercero, no podr&aacute; ser realizada sin el previo, expreso y escrito consentimiento de BIDAIONDO S.L.   Las opiniones contenidas en este mensaje y en los archivos adjuntos, pertenecen exclusivamente a su remitente y no representan la opini&oacute;n de BIDAIONDO S.L., salvo que se diga expresamente y el remitente est&eacute; autorizado para ello BIDAIONDO S.L. advierte expresamente que el env&iacute;o de correos electr&oacute;nicos a trav&eacute;s de Internet no garantiza ni la confidencialidad de los mensajes, ni su integridad y correcta recepci&oacute;n, por lo que BIDAIONDO S.L., no asume responsabilidad alguna por dichas circunstancias.
En caso que no sea el destinatario y haya recibido este mensaje por error, agradecemos lo comunique inmediatamente al remitente sin difundir, almacenar o copiar su contenido.<br>
En cumplimiento con el RGPD (UE) 679/2016, le informamos de que sus datos personales son incluidos en ficheros particulares de BIDAIONDO S.L., con la finalidad de mejorar nuestros servicios y productos, as&iacute; como mantenerle informado sobre estos y realizar comunicaciones comerciales. Para ejercitar los derechos previstos en la ley puede dirigirse mediante un correo electr&oacute;nico a: <a href='mailto:atencionaclientes@bidaiondo.com'>atencionaclientes@bidaiondo.com</a></span>");
?>
