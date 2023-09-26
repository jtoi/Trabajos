ALTER TABLE tbl_colComerLim
ADD limadia int(11) NOT NULL DEFAULT '-1' AFTER limxdia,
CHANGE fecha fecha int(11) NULL AFTER cantxano;

drop trigger if exists tbl_colComerLimBI;
CREATE TRIGGER tbl_colComerLimBI BEFORE INSERT ON tbl_colComerLim FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());
drop trigger if exists tbl_colComerLimBU;
CREATE TRIGGER tbl_colComerLimBU BEFORE UPDATE ON tbl_colComerLim FOR EACH ROW SET new.fecha = UNIX_TIMESTAMP(NOW());
