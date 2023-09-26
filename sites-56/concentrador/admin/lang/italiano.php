<?php
defined( '_VALID_ENTRADA' ) or die( 'Direct Access to this location is not allowed.' );

//Autenticaci&oacute;n
define(_AUTENT_LOGIN,'Utente');
define(_AUTENT_PASS,'password');
define(_AUTENT_TITLE,'Accesso al Pannello di Amministrazione');
define(_AUTENT_NOSEPUEDE,'<span style="color:red; font-weight:bold">Non avete l\'autorizzazione a effettuare questa operazione,<br />por favor p&oacute;ngase en contacto con el administrador.</span>');

//Formulario
define(_FORM_SEND, 'Inviare');
define(_FORM_CANCEL, 'Cancellare');
define(_FORM_CUENTA, 'Conto Bancario del Cliente');
define(_FORM_CUENTA_ALT, 'Conto Corrente da dove sar&aacute; inviato il denaro');
define(_FORM_NAME, 'Nome e Cognome');
define(_FORM_CORREO, 'Indirizzo e-mail');
define(_FORM_SEARCH, 'Cercare');
define(_FORM_YES, 'Si');
define(_FORM_NO, 'No');
define(_FORM_SELECT, 'Selezionare');
define(_FORM_FECHA_INICIO, 'Data Inizio');
define(_FORM_FECHA_FINAL, 'Data Fine');
define(_FORM_FECHA, 'Data');
define(_FORM_NOMBRE, 'Nome e Cognome');
define(_FORM_NOMB, 'Nome');
define(_FORM_APELL, 'Cognome');
define(_FORM_NOMBRE_CLIENTE, 'Nome Cliente');
define(_FORM_MOSTRAR, 'Mostrare');
define(_FORM_OCULTAR, 'Nascondere');
define(_FORM_SIGUIENTE, 'Continuare');
define(_FORM_REGRESA, 'Regresar');

//Tareas
define(_TAREA_MODIFICAR, 'Modificare');
define(_TAREA_INSERTAR, 'Inserire');
define(_TAREA_EDITAR, 'Cambiare dati ');
define(_TAREA_BORRAR, 'Cancellare');
define(_TAREA_ANULAR, 'Annullare');
define(_TAREA_DEVUELTA, 'Rimborsare');
define(_TAREA_PAGADA, 'Pagamento al Commerciante');
define(_TAREA_VER, 'Vedere');
define(_TAREA_VERM, 'Vedere la data');
define(_TAREA_SOLDEVO, 'Richiedere rimborso');

//Menu
define(_MENU_ADMIN_TBIO, 'An&aacute;lisis de TBIO');
define(_MENU_ADMIN_ADMINISTRACION, 'Amministrazione');
define(_MENU_ADMIN_COMERCIO, 'Commerciante');
define(_MENU_ADMIN_SETUP, 'Configurazione');
define(_MENU_ADMIN_MENSAJE, 'Messaggi');
define(_MENU_ADMIN_ADMIN, 'Amministrazione');
define(_MENU_ADMIN_PERSONALES, 'Dati Personali');
define(_MENU_ADMIN_GRUPOS, 'Gruppi');
define(_MENU_ADMIN_ACCESOS, 'Accessi');
define(_MENU_ADMIN_USUARIO, 'Utenti');
define(_MENU_ADMIN_BITACORA, 'Diario');
define(_MENU_ADMIN_EXIT, 'Uscire');
define(_MENU_ADMIN_REPORTE, 'Rapporti');
define(_MENU_ADMIN_COMERCIOS, 'Commercianti');
define(_MENU_ADMIN_PALABRA, 'Descr. Parola segreta');
define(_MENU_ADMIN_COMPROBACION, 'Controllo Firma');
define(_MENU_ADMIN_TRANSACCIONES, 'Transazioni');
define(_MENU_ADMIN_OLDTRANSACCIONES, 'Old Transactions');
define(_MENU_ADMIN_CONSOLIDADO, 'Consolidato');
define(_MENU_ADMIN_DOCUMENTACION, 'Documentazione');
define(_MENU_ADMIN_DOCLEGAL, 'Documentazione Legale');
define(_MENU_ADMIN_COMPARACION, 'Comparazione');
define(_MENU_ADMIN_PAGODIRECTO, 'Pagamenti on line');
define(_MENU_ADMIN_PAGO, 'Clienti');
define(_MENU_ADMIN_TICKET, 'Tickests');
define(_MENU_ADMIN_INSTICKET, 'Aprire Ticket');
define(_MENU_ADMIN_VIETICKET, 'Vedere Ticket');
define(_MENU_ADMIN_PAGOCLIENTE, 'Pagamento al Commerciante');
define(_MENU_ADMIN_CENTRAL, 'Centro di vendita');
define(_MENU_ADMIN_TEMPORADA, 'Stagioni');
define(_MENU_ADMIN_PRODUCTO, 'Producto');
define(_MENU_ADMIN_CARACTERISTICA, 'Caratteristica');
define(_MENU_ADMIN_CANT, 'Quantità');
define(_MENU_ADMIN_PRECIO, 'Prezzo');
define(_MENU_ADMIN_VENTA, 'Banco Vendita');
define(_MENU_ADMIN_TRANSFERENCIA, 'Bonifici');
define(_MENU_ADMIN_TRANSFERENCIA_MOD, 'Richiesta di pagamento tramite bonifico bancario');
define(_MENU_ADMIN_IPDENEGADA, 'IPs Bloccati');
define(_MENU_ADMIN_CIERRES, 'Chiusura');
define(_MENU_ADMIN_VERCIERRES, 'Vedere Chiusure');
define(_MENU_ADMIN_AVISOS, 'Avviso di Bonifico');
define(_MENU_ADMIN_SMS, 'Controllo Sms');
define(_MENU_MENU, 'Menu');
define(_MENU_MENU_CAMBIO, 'Tasso di Cambio');
define(_MENU_ADMIN_VOUCHER, 'Voucher Personalization');
define(_MENU_ADMIN_TRFINS, 'Transfer Insertion');
define(_MENU_ADMIN_CMPTRN,'Confronta le opera. / Banks');
define(_MENU_ADMIN_CAMBIO,'Cambio');
define(_MENU_ADMIN_CAMBIOCUC,'Scambio CUP');
define(_MENU_ADMIN_CAMBIOUSD,'Scambio Divisa');
define(_MENU_ADMIN_PASARELA,'Commercio - PTV');
define(_MENU_ADMIN_IDIOMA,'Lingue');
define(_MENU_ADMIN_INVITACION,'Invito pagamento');
define(_MENU_ADMIN_CONDICIONES, 'Condizioni di pagamento');
define(_MENU_ADMIN_VOUCHER, 'Prova di pagamento');
define(_MENU_ADMIN_PONCIERRE, 'Control de Cierres'); //traducir
define(_MENU_ADMIN_ANALISIS, 'An&aacute;lisis de TPVs');
define(_MENU_ADMIN_SINCR, 'Sincr. Bancos');
define(_MENU_ADMIN_DATOS, 'Datos Varios');
define(_MENU_TRNSF_AMF, 'Transferencias Bidaiondo');
define(_MENU_TITFICH, 'Ficheros a Titanes');
define(_MENU_ADMIN_PASA, 'Configuración TPV');
define(_MENU_ADMIN_PAISLIM, 'Limitación de países por pasarela');
define(_MENU_ADMIN_ECON, 'Destinatarios de Cierres');
define(_MENU_ADMIN_REPTRANS, 'Transfers');
define(_MENU_ADMIN_CIERREADEL, 'Aviso de Cierre adelantado');
define(_MENU_ADMIN_IFRCSV, 'Cambia CSV');
define(_MENU_ADMIN_LOTE, 'Pagamento in Lotti');
define(_MENU_ADMIN_PCANT, 'Pasarela por num. operaciones');
define(_MENU_ADMIN_PTMPO, 'Pasarela por tiempo');
define(_MENU_ADMIN_SFACT, 'Caricare Fatture');
define(_MENU_ADMIN_SRECLAM, 'Caricare Reclami');
define(_MENU_ADMIN_CLONPAS, 'Clonar Pasarela');
define(_MENU_ADMIN_PRUEBA, 'Prueba');
define(_MENU_ADMIN_ERRORES, 'Verificación de errores');
define(_MENU_ADMIN_RECLAMA, 'Gestione Sinistri');
define(_MENU_ADMIN_TRAZA, 'Bitacora Tecnica');

//SMS

//Datos personales
define(_PERSONAL_TITULO, 'Dati Personali');
define(_PERSONAL_IDENT, 'Utente');
define(_PERSONAL_IDIOMA, 'Lingua');
define(_PERSONAL_FECHA, 'Formato data');
define(_PERSONAL_HORA, 'Formato ora');
define(_PERSONAL_NUM, 'Formato numero');
define(_PERSONAL_ESP, 'Spagnolo');
define(_PERSONAL_ING, 'Inglese');
define(_PERSONAL_ITA, 'Italiano');
define(_PERSONAL_IDIOMA_ALT, 'Lingua del cliente');
define(_PERSONAL_PASS, 'Password');
define(_PERSONAL_REPASS, 'Riscrivere Password');
define(_PERSONAL_ALERT_CONTRAS, 'Le Passwords non sono identiche.');
define(_PERSONAL_TEXTO1, 'Siete stati autorizzati all\'accesso al Pannello di Amministrazione del Concentratore. Potete entrare ed inserire i seguenti dati:');
define(_PERSONAL_REC_CORREO, 'Ricevere e-mail per<br />transazione');
define(_PERSONAL_REC_CORREO2, 'Ricevere e-mail per transazione');
define(_PERSONAL_QUERY, 'Salvare consultazione');
define(_PERSONAL_QUERYEXP, 'Salva l\'ultima consultazione nella pagina Rapporto');
define(_PERSONAL_MIPERF, 'Il mio profilo');

//Grupos de trabajo
define(_GRUPOS_TITULO, 'Gruppo di Accesso');
define(_GRUPOS_NOMBRE, 'Nome Gruppo');
define(_GRUPOS_ORDEN, 'Ordine');
define(_GRUPOS_EDIT_DATA, 'Inserire dati ');
define(_GRUPOS_BORRA_DATA, 'Cancellare Registro');
define(_GRUPOS_ANULA_DATA, 'Annullare Transazione');
define(_GRUPOS_DEVUELVE_DATA, 'Rimborsare Transazione');
define(_GRUPOS_PAGA_COMERCIO, 'Pagare al Commerciante');
define(_GRUPOS_GRUPO, 'Gruppo');
define(_GRUPOS_FACTURA, 'Vedere Bonifico');
define(_GRUPOS_FACTURA_VER, 'Vedere Fattura');
define(_GRUPOS_FACTURA_CANCEL, 'Cancel Invoice');
define(_GRUPOS_ALERTA_FACT, 'Questa Transazione non &eacute; un Bonificoo una Preautorizzazione');
define(_GRUPOS_ENVIA_MI, 'Inviamela');
define(_GRUPOS_ENVIA_CLI, 'Invia a Cliente');
define(_GRUPOS_SOLDEVOL, 'Richiedere Rimborso');
define(_GRUPOS_SOLDEVOL_ERROR, 'Questo Bonifico non si pu&eacute; Rimborsare');

//Accesos
define(_ACCESOS_TITULO, 'Accessi');

//IPs
define(_IP_BLOQUEADA, 'Bloccato');
define(_IP_FECHA_DESBLOQUEADA, 'Data Sbloccata');
define(_IP_DESBLOQUEADAPOR, 'Sbloccata da');
define(_IP_VBLOQUEADA, 'Bloqued times');
define(_IP_TRNSACEPTADAS, 'Accepted transactions');
define(_IP_TRNSDENEGADAS, 'Denied transactions');

//Usuarios
define(_USUARIO_TITULO, 'Utenti');
define(_USUARIO_ACTIVO, 'Attivo');
define(_USUARIO_BORRA_PASS, 'Cancella Password');
define(_USUARIO_BORRA_PASS_ALERT, 'Scegliere SI causa la perdita della Password.');
define(_USUARIO_FECHA_ULTIMA, 'Accesso');

//Bitacora
define(_BITACORA_TITULO, 'Diario');
define(_BITACORA_ALERT_FECHAS, 'Per ottenere la informazione richiesta deve inserire É..come data inizio e il É..seguente in data fine');
define(_BITACORA_ALERT_FECHASDIF, 'Per ottenere la informazione richiesta deve inserire É..come data inizio e il É..seguente in data fine .');
define(_BITACORA_TEXT, 'Testo');

//Comercios
define(_COMERCIO_TITULO, 'Commerciante');
define(_COMERCIO_MONEDA_PAGO, 'Pago Comercio');
define(_COMERCIO_MONEDA, 'Valuta');
define(_COMERCIO_ID, 'Id');
define(_COMERCIO_ALTA, 'Data<br />Inizio');
define(_COMERCIO_ESTADO, 'Stato');
define(_COMERCIO_MOVIMIENTO, 'Data Ult.<br />Movim.');
define(_COMERCIO_HISTOR, 'Storico');
define(_COMERCIO_PALABRA, 'Parola Segreta');
define(_COMERCIO_ACTIVO, 'Attivo');
define(_COMERCIO_ACTIVITY, 'Storno');
define(_COMERCIO_ACTIVITY_DES, 'Sviluppo');
define(_COMERCIO_ACTIVITY_PRO, 'Produzione');
define(_COMERCIO_IDENTIF, 'Identificativo');
define(_COMERCIO_ACTIVITY_PRO_ALERT, 'Il commerciante deve aver realizzato almeno una transazione di prova in Sviluppo.');
define(_COMERCIO_HISTORIA, 'Storico');
define(_COMERCIO_URL, 'Url della pagina che riceve il risultato della transazione');
define(_COMERCIO_URL_DIRECTA, 'Url della pagina che riceve direttamente il risultato della transazione');
define(_COMERCIO_URL_CORTA, 'Url');
define(_COMERCIO_PREFIJO, 'Prefisso del Commerciante');
define(_COMERCIO_CONDICIONES, 'Condizioni di pagamento');
define(_COMERCIO_CORREO_P, 'Lista e-mail');
define(_COMERCIO_CARACTERES, 'Due caratteri');
define(_COMERCIO_SMS, 'Invio Sms');
define(_COMERCIO_VENDE, 'consentono miei venditori/utenti vedere cosa fanno gli altri');
define(_COMERCIO_TELEFONO, 'Telefoni');
define(_COMERCIO_FORMATO_INT, 'in formato Internazionale..');
define(_COMERCIO_PASARELAP, 'Sistema di pagamento Online');
define(_COMERCIO_PASARELAM, 'Sistema Pagamento in Tempo Reale');
define(_COMERCIO_EMAIL_SUBJECT, 'Avviso di transazione');
define(_COMERCIO_EMAIL_MES, "Stimato Cliente, <br /><br />&Eacute; stata inviata una e-mail contenente una Operazione di Pagamento differito con Identificatore "
		. "{trans}, al vostro cliente finale {nombre} con una quantità di {importe} {moneda}.<br /><br />Dovrete attendere il risultato del pagamento "
		. "del vs cliente. <br /><br />Grazie per averci scelto.<br /><br />Ecomerce Administrator.");
define(_COMERCIO_SOLC_SI, ' La richiesta è stata inviata correttamente');
define(_COMERCIO_FACT_SI, 'La tua richiesta di pagamento tramite bonifico bancario è stata inviata per essere esaminata.');
define(_COMERCIO_CODE_YA, 'Il Numero transazione è già stata assegnata.<br />Selezionare una nuovo');
define(_COMERCIO_CODEVALID, 'Il numero di transazioni non è valido, deve essere alfanumerico<br />senza spazi e fino a 19 caratteri.');
define(_COMERCIO_SECRETA_NO, "Il Commerciante non ha ancora creato la password,<br />per favore tornare al menu nella Opzione corrispondente '"._MENU_ADMIN_COMERCIO." / "._MENU_ADMIN_PALABRA."' e scaricatela");
define(_COMERCIO_ERROR_INVIT, "Si è verificato un errore durante l'invio dell'invito, riprova.");
define(_COMERCIO_PAGO, 'Effettuare il Pagamento');
define(_COMERCIO_GENERA, 'Se lasciata in bianco &eacute; generata dal sistema');
define(_COMERCIO_PAGOA, 'Modo di pagamento');
define(_COMERCIO_ALMOMENT, 'In tempo reale');
define(_COMERCIO_DIFERI, 'Differito');
define(_COMERCIO_SER, 'Servizio');
define(_COMERCIO_PASARELA, 'Carrello');
define(_COMERCIO_PAGAR, 'Pagata');
define(_COMERCIO_DIRECCION, 'Dati del Commerciante. Indirizzo, Telef. fax per Bonifici Bancari');
define(_COMERCIO_INVACTIVA, 'Link Attivo per');
define(_COMERCIO_INVACTIVAEXPL, ' giorni, solo per pagamenti differiti');
define(_COMERCIO_TASA, 'Cambio');
define(_COMERCIO_EUROSC, 'Euro di scambi');
define(_COMERCIO_CODIGO, 'Codice HTML');
define(_COMERCIO_VERVOUCHER, 'Come sembra? </ Span> (deve avere almeno 1 operazione attraverso Concentrador)');
define(_COMERCIO_ERROR_HTML, 'Ci isn `t codice html');
define(_COMERCIO_ERROR_IDI, 'Non è valida la lingua');
define(_COMERCIO_ERROR_COM, 'Non commerce valido');
define(_COMERCIO_DAT, 'Dati salvati');
define(_COMERCIO_TARJETA, 'Payment Card');
define(_COMERCIO_TARJETA_NUM, 'Numero di carte di pagamento');
define(_COMERCIO_TARJETA_EXPLICA, 'Numero di carta di pagamento (16 cifre)');
define(_COMERCIO_TARJETA_MES, 'Scadenza - Mese');
define(_COMERCIO_TARJETA_ANO, 'Anno');
define(_COMERCIO_TARJETA_CVV2, 'Codice di sicurezza');
define(_COMERCIO_TARJETA_CVV2_EXPLICA, 'Tre cifre sul retro della carta');
define(_COMERCIO_DIRECCION_USR, 'Indirizzo');
define(_COMERCIO_DIRECCION_EXPLICA, 'Indirizzo della titolare della carta come si è registrato con la vostra banca');
define(_COMERCIO_TELEFONO_EXPLICA, 'Telefono della titolare della carta come si è registrato con la vostra banca');
define(_COMERCIO_VISA, 'Visa, Mastercard, Others');
define(_COMERCIO_AMEX, 'American Express');
define(_COMERCIO_EXPLICA, 'The selection of payment with American Express implies that it only can be made through the secure payment platform, so you must check on the Main Page of the Commerce Administrator to see if the card was issued by one of the countries assigned to the Safe Key security program.');
define(_COMERCIO_OPERACION, 'Operazione');

//descarga de palabra
define(_PALABRA_GENERAR, 'Generare');
define(_PALABRA_RETYPE, 'Ricevere Password');
define(_PALABRA_EXPLICA, '<br /> Cliccando su - Generare - si avvier&aacute; il processo per generare la parola segreta que userˆ la sua azienda per firmare le transazioni que si realizzano attraverso.<br /><br />il nostro sistema TPV. E\' importante salvare in modo sicuro questa password. La nuova parola segreta generata invalida le anteriori in caso esistano, quindi da ora in avanti le transazioni devono essere firmate con questa nuova password.<br /><br />');
define(_PALABRA_EXPLICA_NO, '<br />i à generato un errore nella creazione della nuova password, dovete crearne una nuova. Se l\'errore continua dovete contattare <a href=\'mailto:'._CORREO_SITE.'\'>l\'amministratore.</a><br /><br />');
define(_PALABRA_EXPLICA_DESCARGA, '<br /><br />Se il download non inicia automaticamente pote andante al seguente <a href=\'componente/comercio/bajando.php?id={enlace}.txt\'>lino.</a><br /><br />');

//Inicio
define(_INICIO_TITLE_ALL, 'Dati del Commerciante');
define(_INICIO_TITLE_MENOS, 'Dati');
define(_INICIO_CANT_TOTAL, 'Quantità totali di Commercianti attivi: ');
define(_INICIO_CANT_PRODUCC, 'Quantità di Commercianti in Produzione: ');
define(_INICIO_CANT_DESARR, 'Quantità di Commercianti in Sviluppo: ');
define(_INICIO_NUMR_TRANSAC_ACEPT, 'Quantità di transazioni accettate: ');
define(_INICIO_NUMR_TRANSAC_DENEG, 'Quantità di transazioni non accettate: ');
define(_INICIO_VALR_TRANSAC, 'Valore delle transazioni accettate: ');
define(_INICIO_COMERCIO_NUMR, 'Numero del Commerciante: ');
define(_INICIO_COMERCIO_MODO, 'Modo di lavoro del commerciante: ');
define(_INICIO_FECHA_MODO, 'Ultima data di cambio di modalità: ');
define(_INICIO_MES, 'Questo mese');
define(_INICIO_SEM, 'Questa settimana');
define(_INICIO_HOY, 'Oggi');
define(_INICIO_TODO, 'Accumulato sino ad oggi');
define(_INICIO_COMERCIO, 'Comemrciante');
define(_INICIO_CANT_TRANSACCIONES, 'Trabsazioni<br />accettate');
define(_INICIO_VALOR, 'Valore');
define(_INICIO_CONECTADOS, 'Collegati in questo momento:');
define(_INICIO_NOCONECTADOS, 'Nessun altro utente collegato');
define(_INICIO_NOTICIA, 'Notizie');
define(_INICIO_TRBAJ, 'Lavoro');
define(_EUROA, '1 Euro a');
define(_VERTASA, 'Mostra i tassi di cambio');
define(_HORA_ESP, 'Ora España');
define(_HORA_CUB, 'Ora Cuba');
define(_VENTAS_X_TIENDA, 'Top 10 Commerces');
define(_SALES_X_MES, 'Top 10 Month`s Commerces');
define(_TIENDA_TIT, 'Negozio');
define(_INICIO_VALORDIA, 'valore del giorno');
define(_INICIO_VALORMENSUAL, 'valore mensile');
define(_INICIO_VALORANUAL, 'valore annuale');
define(_INICIO_CANTDIA, 'cantidad d&iacute;a');
define(_INICIO_CANTMES, 'cantidad mensual');
define(_INICIO_CANTANO, 'cantidad anual');

//Setup
define(_SETUP_TITLE, 'Configurazione');
define(_SETUP_EMAIL_CONT, 'E-mail di contatto');
define(_SETUP_PALABR_OFUS, 'Parola segreta occulta');
define(_SETUP_CONTRASENA, 'Password di occultamento');
define(_SETUP_COMERCIO, 'Identificazione del Commerciante');
define(_SETUP_PUNTO, 'Identificazione del Terminale');
define(_SETUP_LOCALIZADOR, 'Localizzatore');
define(_SETUP_URL_COMERCIO, 'Url Commerciante');
define(_SETUP_URL_DIR, 'Url della Directory');
define(_SETUP_URL_TPV, 'Url sistema TPV');
define(_SETUP_MESES, 'Quantità di mesi per cui mantenere le transazioni nel Riassunto Principale');
define(_SETUP_DATOS_TPV, 'Dati produzione sistema TPV');
define(_SETUP_DATOS_TPV_TEST, 'Dati integrazione sistema TPV');
define(_SETUP_MENSAJE, 'Messaggio');

//Reportes
define(_REPORTE_TITLE, 'Rapporti');
define(_REPORTE_TASK, 'Cercare per');
define(_REPORTE_FECHA_INI, 'Data inizio');
define(_REPORTE_FECHA_FIN, 'Data fine');
define(_REPORTE_REF_COMERCIO, 'Riferimenti del Commerciante');
define(_REPORTE_REF_BBVA, 'Riferimenti del Banco');
define(_REPORTE_VALOR, 'Valore');
define(_REPORTE_ALCOMERCIO, 'Pagato al comemrciante');
define(_REPORTE_VALOR_INICIAL, 'Valore Iniziale');
define(_REPORTE_FECHA_MOD, 'Data modificara');
define(_REPORTE_ESTADO, 'Stato');
define(_REPORTE_TOTAL, 'Valore Totale');
define(_REPORTE_IDENTIFTRANS, 'Nr. Indentificazione di Transazione');
define(_REPORTE_TODOS, 'Tuti');
define(_REPORTE_FECHA, 'Data');
define(_REPORTE_STATUS, 'Stato della Transazione');
define(_REPORTE_PRINT, 'Stampa Rapporto');
define(_REPORTE_CSV, 'Esportare a CSV');
define(_CONSOLIDADO_TITLE, 'Consolidado');
define(_REPORTE_DESCUENTO, 'Valor a descontar');
define(_REPORTE_NOPUEDO, 'Il valore da scontare deve essere minore o uguale al valore della transazione.');
define(_REPORTE_DESCUENTO_TITLE, 'Sconto');
define(_REPORTE_ERROR, "Carattere errore");
define(_REPORTE_IP, "Dir IP");
define(_REPORTE_PAIS, "Stato");
define(_REPORTE_CANT, 'Quantità');
define(_REPORTE_RECLAMADA, 'Ha sostenuto');
define(_REPORTE_ACEPTADA, 'Accettata');
define(_REPORTE_APOBADA, 'Approvato');
define(_REPORTE_CANCELADA, 'Annullato');
define(_REPORTE_REALMENSUAL, 'Reale Mensile');
define(_REPORTE_PROMMENSUAL, 'Media Mensile');
define(_REPORTE_PENDIENTE, 'Orecchino');
define(_REPORTE_DENEGADA, 'Non accettata');
define(_REPORTE_PROCESADA, 'Non processata');
define(_REPORTE_ANULADA, 'Annullata');
define(_REPORTE_VENCIDA, 'Expired');
define(_REPORTE_DEVUELTA, 'Rimborsata');
define(_REPORTE_PROCESO, 'In processo');
define(_REPORTE_ACEPTDEV, _REPORTE_ACEPTADA.' y '._REPORTE_DEVUELTA);
define(_REPORTE_EUROE, 'Equivalente Euro');
define(_REPORTE_TIPOG, 'Tipo di grafico');
define(_REPORTE_BARRASV, 'Barre Verticali');
define(_REPORTE_BARRASH, 'Barre orizzontali');
define(_REPORTE_PUNTOS, 'Punti');
define(_REPORTE_LINEAS, 'Linee');
define(_REPORTE_MESES, 'mesi');
define(_REPORTE_MESESM, 'Mesi');
define(_REPORTE_VALORES, 'Valori');
define(_REPORTE_DIAS, 'giorni');
define(_REPORTE_DIASM, 'Giorni');
define(_REPORTE_CLIENTE, 'Cliente');
define(_REPORTE_ESTIMADO, 'Stimato dell\'ultimo mese:');
define(_REPORTE_TRANSFERENCIA, 'Numero Bonifico');
define(_REPORTE_TRANSFERENCIA_ID, 'Identificatore del Bonifico');
define(_REPORTE_TRANSFERENCIA_ESTADO, 'Stato del Bonifico');
define(_REPORTE_SOLDEVOL, 'Richieste di rimborso');

//Comprobacion
define(_COMPRUEBA_TITLE, 'Controllo Firma');
define(_COMPRUEBA_COMPROBAR, 'Verificare');
define(_COMPRUEBA_COMERCIO, 'Identificatore del Commerciante');
define(_COMPRUEBA_TRANSACCION, 'Numero Transazione');
define(_COMPRUEBA_IMPORTE, 'Importo');
define(_COMPRUEBA_MONEDA, 'Valuta');
define(_COMPRUEBA_OPERACION, 'Tipo di Operazione');
define(_COMPRUEBA_PAGO_OPERAC, 'di Pagamento');
define(_COMPRUEBA_CANCELA_OPERAC, 'Di Cancellazione');
define(_COMPRUEBA_MD5, 'Firma Digitale');

//Bitacora
define(_BITACORA_ALERT_FECHASDIF, 'Data inizio posteriore alla data finale.');

//Tickets
define(_TICKET_CONSULTA, "\n\nIl suo Ticket ha il numero {numer}. Potrete consultare il suo stato in :\n");
define(_TICKET_OK, 'Ticket inviato in modo corretto');
define(_TICKET_NOOK, 'Si á verificato un errore nell\'invio del Ticket all\'Amministratore. <br />Per favore ripetere l\'operazione o mettetevi in contatto con l\'Amministratore');
define(_TICKET_DESCR, 'Descrivete il problema');
define(_TICKET_ACTIVO, 'Attivo');
define(_TICKET_CERRADO, 'Chiuso');
define(_TICKET_ID, 'Identificativo del Ticket');
define(_TICKET_ASUNTO, 'Oggetto');
define(_TICKET_FENTRADA, 'Data creazione');
define(_TICKET_FCERRADO, 'Data chiusura');
define(_TICKET_DESCRI, "Descrizione");
define(_TICKET_SOLIC, "Richiedere al cliente");
define(_TICKET_CONT, 'Contiene');
define(_TICKET_PAGO, 'Ticket il Pago');
define(_TICKET_AUTOR, 'Autorizzare');
define(_TICKET_FIRMA, 'Firma');
define(_TICKET_SUPAGO, 'Il vostro pagamento è stato effettuato con successo con i seguenti dati:');
define(_TICKET_TELEF, 'Telefono disponibile');

//Central de ventas
define(_VENTA_DESC_NOMBRE, 'Nome della caratteristica: Es. Hotel, Negozio, Stagione');
define(_VENTA_DESC_DESC, 'Valore che assume la caratteristica : es.: nome del Hotel, Settore del negozio, Alta o Bassa.');
define(_VENTA_DESC_FECHA1, 'Data in cui comincerà a influenzare il prodotto questa caratteristica');
define(_VENTA_DESC_FECHA2, 'Data in cui terminerà di influenzare il prodotto questa caratteristica');
define(_VENTA_DESC_TEMP_NOMBRE, 'Nome della stagione');
define(_VENTA_DESC_TEMP_FECHA1, 'Data in cui comincerà a influenzare questa stagione');
define(_VENTA_DESC_TEMP_FECHA2, 'Data in cui terminerà di influenzare questa stagione');
define(_VENTA_DESC_VALOR, 'Valore con cui si modifichare il prezzo del prodotto');
define(_VENTA_VALOR, 'Valore');
define(_VENTA_MODIFICA, 'Modifica');
define(_VENTA_DESC_MODIFICA, 'Modo in cui si modifichare con il valore il prezzo del prodotto');
define(_VENTA_MODIFICA_SUM, 'Somma al prezzo il valore');
define(_VENTA_MODIFICA_RES, 'Sottrai al prezzo il valore');
define(_VENTA_MODIFICA_POR, 'Sconta con un valore del %');
define(_VENTA_USUARIOS, 'Utenti');
define(_VENTA_DESC_USUARIOS, 'Utenti autorizzati ad attivare questa caratteristica');
define(_VENTA_DESC_TEMPOR, 'Guida per le date di inizio e fine, se si sceglie si disattivano le date');
define(_VENTA_TEMP_INI, 'Selezionare');
define(_VENTA_BUSCAR, 'Cercare');
define(_VENTA_BUSCAR_SI, 'Si');
define(_VENTA_BUSCAR_NO, 'No');
define(_VENTA_DESC_BUSCAR, 'Selezionare la caratteristica come ricercabile, nella vendita del prodotto questo sará un campo che appare per recuperare il prodotto');
define(_VENTA_OPCIONAL, 'Opzionale');
define(_VENTA_DESC_OPCIONAL, '-Si- Ottiene che questa caratteristica solo influenza il prodotto nel momento della vendita se cosè ha deciso il venditore,
	-No- il prodotto raggiunge la vendita già influenzato per questa caratteristica ed il venditore non può cambiarla');
define(_VENTA_DIARIO, 'Giornaliero');
define(_VENTA_DESC_DIARIO, '-Si- Ottiene che questa caratteristica influenza il prodotto giornalmente, che si sconti o incrementi per la quantità di giorni.');

//Productos
define(_PROD_CODIGO, 'Codice');
define(_PROD_CODIGO_DESC, 'Codice del prodotto se esistente');
define(_PROD_NOMBRE_DESC, 'Nome del Prodotto, es.: Camera, camera junior, escursione alla cienaga de zapata');
define(_PROD_DESCR_DESC, 'Descrizione del prodotto che può servire da guida per la vendita di due prodotti con lo stesso nome');
define(_PROD_STOCK, 'Vendita da magazzino esistente');
define(_PROD_STOCK_DESC, 'Vendita da magazzino esistente o stock, il prodotto dovrè essere in esistenza per poter essere venduto. In caso contrario si potr˜ vendere senza consultare il magazzino'
	. 'Sale da magazzino o magazzino, il prodotto deve avere stock da vendere. Altrimenti si può vendere senza deposito consulenza');
define(_PROD_DESC_FECHA1, 'Data dalla quale il prodotto sarˆ disponibile alla vendita.');
define(_PROD_DESC_FECHA2, 'Data sino alla quale il prodotto sar˜ disponibile alla vendita.');
define(_PROD_CARACT_DESC, 'Caratteristica da applicare ai prodotti');
define(_PROD_MONEDA_DESC, 'Valuta nella quale si venderˆ il prodotto, i valori della caratteristica influenzeranno questo prodotto in questa moneta.');
define(_PROD_CANT_DESC, 'Disponibilitˆ del prodotto per un intervallo di date predeterminato.');

//Buró de ventas
define(_BURO_TITULO1, "Sistema per la vendita");
define(_BURO_DESC_FECHA1, 'Data di realizzazione della vendita. Se vende prodotti non deve specificare la data finale. Se vende disponibiltˆ debe inserire l\'inizio della prenotazione..');
define(_BURO_DESC_FECHA2, 'Se vende disponibilitˆ deve inserire la fine della prenotazione');
define(_BURO_CONTINUA_BOTON, 'Continuare');
define(_BURO_ATRAS_BOTON, 'Indietro');
define(_BURO_CANT, 'Quantità');
define(_BURO_CANT_DESC, 'Quantità del prodotto in venditˆ');
define(_BURO_CANT_ERROR, '<br />La quantità non ha il valore richiesto, deve essere un numero intero superiore a 0.');
define(_BURO_FECHA_ERROR, '<br />La data/e richiesta non è valida, controlli che la data iniziale sia maggiore o uguale a quella di oggi.');
define(_BURO_PROD_ERROR, '<br />Non esistono prodotti nelle date introdotte.');
define(_BURO_PRECIO, 'Prezzo');
define(_BURO_PROD_FALLA, 'Non è possibile acquistare il prodotto selezionato perchè non ha prezzo per la data indicata.');
define(_BURO_DIAS, 'Giorni');
define(_BURO_VENTAOK, 'Vendita aggiunta al carrello acquisti');
define(_BURO_APAGAR, 'Inviare il contenuto del Carrello Acquisti');

//Cierres
define(_CIERRE_TIPO, 'Tipo di chiusura');
define(_CIERRE_DESDE, 'Da');
define(_CIERRE_HASTA, 'A');
define(_CIERRE_VALOR, 'Per Valore');
define(_CIERRE_DIARIO, 'Giornaliero');
define(_CIERRE_SEMANAL, 'Settimanale');
define(_CIERRE_QUINCENAL, 'Quindicinale');
define(_CIERRE_MENSUAL, 'Mensile');
define(_CIERRE_CONSECUTIVO, 'Consecutivo');
define(_CIERRE_FECHAINI, 'Data Inizio');
define(_CIERRE_FECHAFIN, 'Data Finale');
define(_CIERRE_INTEGRA, 'Integrazione');
define(_CIERRE_MENSUAL, 'Mensilitè');
define(_CIERRE_TARJETA, 'Uso Carta Credito');
define(_CIERRE_COMIS, 'Commissione');
define(_CIERRE_RETROC, 'Rimborsi');
define(_CIERRE_TRANSF, 'Bonifici');
define(_CIERRE_SWIFT, 'Swift');
define(_CIERRE_COSTOB, 'Costo Bancario');
define(_CIERRE_DESC, 'Totale sconti');
define(_CIERRE_DEVOL, 'Totale rimborsato');
define(_CIERRE_TOTAL, 'Totale senza sconto.');
define(_CIERRE_PAGAR, 'Da pagare');
define(_CIERRE_VCIERRE, 'Vedere Chiusura');

//Avisos
define(_MENU_ADMIN_AVISO, 'Avviso di Trasferimento di Fondi');
define(_AVISO_NOMBRE, 'Nome della Ditta');
define(_AVISO_REMITENTE, 'Dati del Bonificante');
define(_AVISO_OBSERVA, 'Concetto / Osservazioni');
define(_AVISO_SI, 'L\'avviso stato inviato correttamente alla sua e-mail.');
define(_AVISO_CODIGO, 'Trasferimento codice');
define(_AVISO_NUMERO, 'Trasferimento numero');
define(_AVISO_VALOREU, 'Importo in Euro');
define(_AVISO_TASA, 'Tasso di cambio');

//Devoluciones
define(_DEVOL_TIT, "Richiesta di RImborso");
define(_DEVOL_MONT, "Quantità da restitutire");

//movil
define(_MOVIL_OPERAC, "Operazioni Quantità");
define(_MOVIL_OPERAC_ACEPT, "Accettato, Rimborso e Annullato");
define(_MOVIL_OPERAC_RECHAZ, "In attesa e negato");
define(_MOVIL_VALORES, "Valore dell'operazione");
define(_MOVIL_VALORES_VALOR, "Valore");
define(_MOVIL_VALORES_PROM, "Media");
define(_MOVIL_ANO, "Anno");
define(_MOVIL_MES, "Mese");
define(_MOVIL_SEMANA, "Settimana");
define(_MOVIL_ACEPT_COMERC, "Stato cambiato");
define(_MOVIL_TRANSACCION, "Transazione");
define(_MOVIL_COMERCIO, "Commercio");
define(_MOVIL_IDENTIFICADOR, "Identificare");
define(_MOVIL_MONEDA, "Moneta");
define(_MOVIL_VALOR_INI, "Valore iniziale");
define(_MOVIL_VALOR, "Valore");
define(_MOVIL_FECHA_INI, "Data iniziale");
define(_MOVIL_FECHA_MOD, "Data di modifica");
define(_MOVIL_ESTADO, "Stato");
define(_MOVIL_ESTADO_TRANS, "Transacction stato");
define(_MOVIL_ENTORNO, "Stato di Commercio");
define(_MOVIL_ERROR, "Errore");
define(_MOVIL_PASARELA, "Payment gateway");
define(_MOVIL_CODIGO, "Codice bancario");
define(_MOVIL_PAIS, "Paese");
define(_MOVIL_PAGADO, "Paid to Commerce");
define(_MOVIL_EUROEQ, "Euro conversione");
define(_MOVIL_NOCOINC, "Non ci sono risultati");
define(_MOVIL_MOSTRANDO, "Da");
define(_MOVIL_A, "a");
define(_MOVIL_DE, "di");
define(_MOVIL_RECORDS, "records");
define(_MOVIL_PROCES, "Processing, attendere prego...");
define(_MOVIL_PRIMERO, "Prima");
define(_MOVIL_ANTERIOR, "Davanti");
define(_MOVIL_PROXIMO, "Prossimo");
define(_MOVIL_ULTIMO, "Ultimo");
define(_MOVIL_NODATA, "Nessun dato disponibile");
define(_MOVIL_CLIENTE, "Cliente");
define(_MOVIL_FECHA, "Data");
define(_MOVIL_ADMINIST, "Utente");
define(_MOVIL_EMAIL, "Email");
define(_MOVIL_PAGO_MOM, "Pagamento");
define(_MOVIL_SERVIC, "Servizio");
define(_MOVIL_ROL, "Role");
define(_MOVIL_FECHAV, "Data ultima visita");
define(_MOVIL_IDIOMA, "Lingua");
define(_MOVIL_IDIOMA_ES, "Idioma Español");
define(_MOVIL_IDIOMA_EN, "English Language");
define(_MOVIL_EDITA, "Modifica");
define(_MOVIL_NUEVO, "Nuovo");
define(_MOVIL_DESACT, "Disattivare");
define(_MOVIL_ACT, "Attivare");
define(_MOVIL_BCONTR, "Reimpostazione della password");
define(_MOVIL_ALMOMENT, 'Pagamento Immediata');
define(_MOVIL_DIFERI, 'Pagamento Differito');
define(_MOVIL_DIA, 'giorno');
define(_MOVIL_HOY, 'Oggi');
define(_MOVIL_MES, 'Questo mese');
define(_MOVIL_CANT_TRANS, 'Transazioni Quantità');
define(_MOVIL_VAL_ACEP, 'Valore Accettato');

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
define(_PAGO_TARJETA, "Choose costumer`s card");

//Idiomas
define(_IDIOMA_SALIDAOK, 'Correttamente i dati memorizzati');

//Errores
define(_ERROR_UNO, 'ERROR! .. .. Your transaction could not be processed successfully. Try it again.');
define(_ERROR_DOS, 'Error !!.. You have exceeded the maximum allowable limit per operation. ');
define(_ERROR_TRES, 'Error !!.. You do not meet the minimum allowable limit per operation. ');
define(_ERROR_CUATRO, 'Error !!.. You have exceeded the maximum accumulated limit per day. ');
define(_ERROR_CINCO, 'Error !!.. You have exceeded the maximum cumulative limit per month. ');
define(_ERROR_SEIS, 'Error !!.. You have exceeded the maximum cumulative limit per year. ');
define(_ERROR_SIETE, 'Error !!.. You have exceeded the maximum allowable operations limit per day. ');
define(_ERROR_OCHO, 'Error !!.. You have exceeded the maximum allowable operations limit per month. ');
define(_ERROR_NUEVE, 'Error !!.. You have exceeded the maximum allowable operations limit per year. ');

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
