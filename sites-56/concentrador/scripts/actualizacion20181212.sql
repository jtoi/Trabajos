ALTER TABLE `tbl_aisOrden`
ADD `estado` char(1) NOT NULL DEFAULT 'P',
ADD `fechaAct` int(11) NULL AFTER `estado`;

update tbl_aisOrden set estado = 'A', fechaAct = unix_timestamp() where titOrdenId != '' or titOrdenId != null;

