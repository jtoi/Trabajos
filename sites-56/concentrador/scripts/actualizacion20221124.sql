delete from tbl_destinatario
where id in (69, 70, 71, 72);
ALTER TABLE `tbl_transferencias` CHANGE `estado` `estado` CHAR(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'T' COMMENT 'T-Pendiente; A-Pagada; P- En Proceso';
INSERT INTO `tbl_menu` (
		`id`,
		`title`,
		`link`,
		`parentid`,
		`movil`,
		`mlink`,
		`orden`
	)
VALUES (
		NULL,
		'Aprobación de trasferencia',
		'index.php?componente=comercio&pag=revisaFactura',
		'0',
		'0',
		NULL,
		'0'
	);
INSERT INTO `tbl_correos` (
		`id`,
		`accion`,
		`asunto`,
		`pagina`,
		`fecha`,
		`observ`
	)
VALUES (
		65,
		'4',
		'Revisión de solicitud de transferencia',
		'admin/component/comercio/revFactura',
		'1669306989',
		'Revisión de la solicitud de Transferencia elaborada por el comercio antes de enviarla al Cliente para pagar.'
	),
	(
		66,
		'4',
		'Revisión de solicitud de transferencia',
		'admin/component/comercio/revFactura',
		'1669306989',
		'Correo que se envía a los comercios una vez se hace la revisión de la solicitud de Transferencia'
	);
UPDATE `tbl_correos`
SET `observ` = 'Reenvío de la factura de la transferencia al Cliente luego de revisada',
	`pagina` = 'admin/component/comercio/revFactura'
WHERE `tbl_correos`.`id` = 25;
insert into tbl_destinatario (idcorreo, idadmin, para, fecha)
values (25, 301, '', 1669308159),
	(25, 275, '', 1669308159),
	(25, 1406, '', 1669308159),
	(25, 523, '', 1669308159);
insert into tbl_destinatario (idcorreo, idadmin, para, fecha)
values (65, 301, '', 1669308159),
	(65, 275, '', 1669308159),
	(65, 24, '', 1669308159),
	(65, 523, '', 1669308159);