CREATE TABLE `tbl_ipCubana` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `ipentrada` varchar(20) COLLATE 'utf8_spanish2_ci' NOT NULL,
  `ipfinal` varchar(20) COLLATE 'utf8_spanish2_ci' NOT NULL,
  `fecha` int NOT NULL
) COLLATE 'utf8_spanish2_ci';

ALTER TABLE `tbl_ipCubana`
ADD UNIQUE `ipentrada` (`ipentrada`);
