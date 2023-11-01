Warning: World-writable config file '/etc/mysql/mariadb.conf.d/50-server.cnf' is ignored
-- MariaDB dump 10.19  Distrib 10.5.9-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: admincomercio_db
-- ------------------------------------------------------
-- Server version	10.5.9-MariaDB-1:10.5.9+maria~focal

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tit_Actividad`
--

DROP TABLE IF EXISTS `tit_Actividad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Actividad` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Actividad` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `inTitanes` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Actividad`
--

LOCK TABLES `tit_Actividad` WRITE;
/*!40000 ALTER TABLE `tit_Actividad` DISABLE KEYS */;
INSERT INTO `tit_Actividad` VALUES (1,'Non-profit and religious associations',1),(2,'Remittance agent',1),(3,'Agriculture',1),(4,'Caring for people / children',1),(5,'Unemployed - No activity',1),(6,'Construction / real estate manager',1),(7,'Catering (waiter)',1),(8,'Cleaning',1),(9,'Bar/commerce owner',1),(10,'Armament sector',1),(11,'Domestic Service',1),(12,'Construction Worker',1),(13,'Housekeeper',1),(14,'Clerk - Cashier',1),(15,'Student',1),(16,'Administrative',1),(17,'Bricklayer',1),(18,'Carpentry',1),(19,'Commercial - Sales',1),(20,'Household employee',1),(21,'Plumbing',1),(22,'Military',1),(23,'Factory Operator',1),(24,'Hairdresser',1),(25,'Pensioner / Retiree',1),(26,'Driver - Chauffeur - Truck driver',1),(27,'Mechanic',1),(28,'Painter',1),(29,'Carrier',1),(30,'Night Leisure',1),(31,'Other Activities (specify)',1),(32,'Mining',1),(33,'Industrial sector',1),(34,'Jewelry, gold and gemstones sector worker',1),(35,'Jewelry and gold sector manager/partner',1),(36,'Financial and insurance activities',1),(37,'Notary',1),(38,'Lawyer',1),(39,'Civil servant',1),(40,'Education sector',1),(41,'Health sector',1),(42,'Gaming, casino and betting worker',1),(43,'Gaming, casino and betting manager/partner',1),(44,'Non-profit and religious associations',1);
/*!40000 ALTER TABLE `tit_Actividad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Contacto`
--

DROP TABLE IF EXISTS `tit_Contacto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Contacto` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `idTitanes` int(11) DEFAULT NULL,
  `IdMetodoContacto` int(11) DEFAULT NULL,
  `idPerson` int(11) DEFAULT NULL,
  `dato` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `alias` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `isdefault` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `idPerson` (`idPerson`),
  KEY `IdMetodoContacto` (`IdMetodoContacto`),
  CONSTRAINT `tit_Contacto_ibfk_3` FOREIGN KEY (`idPerson`) REFERENCES `tit_Personas` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tit_Contacto_ibfk_4` FOREIGN KEY (`IdMetodoContacto`) REFERENCES `tit_MetodoContacto` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Contacto`
--

LOCK TABLES `tit_Contacto` WRITE;
/*!40000 ALTER TABLE `tit_Contacto` DISABLE KEYS */;
INSERT INTO `tit_Contacto` VALUES (1,317460,1,1,'567356745','telf',1),(2,317461,2,2,'546735734','mi telf',1),(3,323481,1,4,'5686437899','telef',1);
/*!40000 ALTER TABLE `tit_Contacto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Cuentas`
--

DROP TABLE IF EXISTS `tit_Cuentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Cuentas` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `idTitanes` int(11) DEFAULT NULL,
  `idPerson` int(11) DEFAULT NULL,
  `idTipoCuenta` int(11) DEFAULT NULL,
  `idpais` int(11) DEFAULT NULL,
  `idFormatoCuenta` int(11) DEFAULT NULL,
  `idMoneda` int(11) DEFAULT NULL,
  `isDefault` tinyint(1) DEFAULT NULL,
  `alias` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cuentaNum` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `balance` float NOT NULL DEFAULT 0,
  `disponible` float NOT NULL DEFAULT 0,
  `porAcreditar` float NOT NULL DEFAULT 0,
  `porDebitar` float DEFAULT 0,
  PRIMARY KEY (`Id`),
  KEY `idPerson` (`idPerson`),
  KEY `idpais` (`idpais`),
  KEY `idFormatoCuenta` (`idFormatoCuenta`),
  KEY `idTipoCuenta` (`idTipoCuenta`),
  KEY `idMoneda` (`idMoneda`),
  CONSTRAINT `tit_Cuentas_ibfk_1` FOREIGN KEY (`idPerson`) REFERENCES `tit_Personas` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `tit_Cuentas_ibfk_2` FOREIGN KEY (`idpais`) REFERENCES `tit_Pais` (`Id`),
  CONSTRAINT `tit_Cuentas_ibfk_4` FOREIGN KEY (`idFormatoCuenta`) REFERENCES `tit_FormatoCuenta` (`id`),
  CONSTRAINT `tit_Cuentas_ibfk_5` FOREIGN KEY (`idTipoCuenta`) REFERENCES `tit_TipoCuenta` (`id`),
  CONSTRAINT `tit_Cuentas_ibfk_6` FOREIGN KEY (`idMoneda`) REFERENCES `tit_Moneda` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Cuentas`
--

LOCK TABLES `tit_Cuentas` WRITE;
/*!40000 ALTER TABLE `tit_Cuentas` DISABLE KEYS */;
/*!40000 ALTER TABLE `tit_Cuentas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Direccion`
--

DROP TABLE IF EXISTS `tit_Direccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Direccion` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `IdTitanes` int(11) DEFAULT NULL,
  `IdPerson` int(11) DEFAULT NULL,
  `IdPais` int(11) DEFAULT NULL,
  `Direccion` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Ciudad` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `CP` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Provincia` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Alias` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `IsDefault` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`Id`),
  KEY `IdPerson` (`IdPerson`),
  KEY `IdPais` (`IdPais`),
  CONSTRAINT `tit_Direccion_ibfk_3` FOREIGN KEY (`IdPerson`) REFERENCES `tit_Personas` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `tit_Direccion_ibfk_4` FOREIGN KEY (`IdPais`) REFERENCES `tit_Pais` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Direccion`
--

LOCK TABLES `tit_Direccion` WRITE;
/*!40000 ALTER TABLE `tit_Direccion` DISABLE KEYS */;
INSERT INTO `tit_Direccion` VALUES (1,213098,1,226,'3843 Oak Lane Leavenworth','Mobile','66048','Kansas','direcc',1),(2,216856,4,26,'direccion1','la paz','456745674','la paz','casa',1),(3,213162,6,1,'2472 Brown Bear Drive','Rancho California','92390','California','este',1),(4,213164,7,26,'3005 Dane Street','New Bedford','02740','Masatam','direcc',1);
/*!40000 ALTER TABLE `tit_Direccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Documento`
--

DROP TABLE IF EXISTS `tit_Documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Documento` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `idTitanes` int(11) DEFAULT NULL,
  `idPerson` int(11) DEFAULT NULL,
  `idMime` int(11) DEFAULT NULL,
  `IdTipoDoc` int(11) DEFAULT NULL,
  `idPaisExped` int(11) DEFAULT NULL,
  `FechaExpir` date DEFAULT NULL,
  `Documento` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `DocAlias` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `isDefault` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdTipoDoc` (`IdTipoDoc`),
  KEY `idPaisExped` (`idPaisExped`),
  KEY `idPerson` (`idPerson`),
  KEY `idMime` (`idMime`),
  CONSTRAINT `tit_Documento_ibfk_1` FOREIGN KEY (`IdTipoDoc`) REFERENCES `tit_MimeType` (`Id`),
  CONSTRAINT `tit_Documento_ibfk_2` FOREIGN KEY (`IdTipoDoc`) REFERENCES `tit_TipoDocumento` (`Id`),
  CONSTRAINT `tit_Documento_ibfk_4` FOREIGN KEY (`idPaisExped`) REFERENCES `tit_Pais` (`Id`),
  CONSTRAINT `tit_Documento_ibfk_6` FOREIGN KEY (`idPerson`) REFERENCES `tit_Personas` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `tit_Documento_ibfk_7` FOREIGN KEY (`idMime`) REFERENCES `tit_MimeType` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Documento`
--

LOCK TABLES `tit_Documento` WRITE;
/*!40000 ALTER TABLE `tit_Documento` DISABLE KEYS */;
INSERT INTO `tit_Documento` VALUES (1,374705,1,5,1,226,'2025-02-23','dfghdf6345','lcencia',1),(2,374706,2,5,1,55,'2027-01-23','456735673','CIdent',1),(3,391801,4,5,1,26,'2027-12-10','456345634','carnet',1),(4,374781,8,5,1,55,'2024-12-12','567356745','eldocu',1);
/*!40000 ALTER TABLE `tit_Documento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_FormatoCuenta`
--

DROP TABLE IF EXISTS `tit_FormatoCuenta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_FormatoCuenta` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_FormatoCuenta`
--

LOCK TABLES `tit_FormatoCuenta` WRITE;
/*!40000 ALTER TABLE `tit_FormatoCuenta` DISABLE KEYS */;
INSERT INTO `tit_FormatoCuenta` VALUES (1,'IBAN'),(2,'Swift'),(3,'Card');
/*!40000 ALTER TABLE `tit_FormatoCuenta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_GrupoDocumento`
--

DROP TABLE IF EXISTS `tit_GrupoDocumento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_GrupoDocumento` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_GrupoDocumento`
--

LOCK TABLES `tit_GrupoDocumento` WRITE;
/*!40000 ALTER TABLE `tit_GrupoDocumento` DISABLE KEYS */;
INSERT INTO `tit_GrupoDocumento` VALUES (1,'Identification Document'),(2,'Proof of origin of funds'),(3,'Activity'),(4,'Bank account ownership'),(5,'Consumer'),(6,'Titanes Contract'),(7,'Identification Document'),(8,'Proof of origin of funds'),(9,'Activity'),(10,'Bank account ownership'),(11,'Consumer'),(12,'Titanes Contract');
/*!40000 ALTER TABLE `tit_GrupoDocumento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_InstrumentoPago`
--

DROP TABLE IF EXISTS `tit_InstrumentoPago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_InstrumentoPago` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_InstrumentoPago`
--

LOCK TABLES `tit_InstrumentoPago` WRITE;
/*!40000 ALTER TABLE `tit_InstrumentoPago` DISABLE KEYS */;
INSERT INTO `tit_InstrumentoPago` VALUES (1,'CreditCard'),(2,'DebitCard'),(3,'PrepaidCard'),(4,'Pos'),(5,'WireTransfer'),(6,'MoneyTransfer'),(7,'DirectDebit'),(8,'Cash'),(9,'Loan'),(10,'Transfer'),(11,'Purse'),(12,'Paylink'),(99,'None');
/*!40000 ALTER TABLE `tit_InstrumentoPago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_MetodoContacto`
--

DROP TABLE IF EXISTS `tit_MetodoContacto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_MetodoContacto` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_MetodoContacto`
--

LOCK TABLES `tit_MetodoContacto` WRITE;
/*!40000 ALTER TABLE `tit_MetodoContacto` DISABLE KEYS */;
INSERT INTO `tit_MetodoContacto` VALUES (1,'Fixed Phone'),(2,'Mobile Phone'),(3,'Email'),(4,'Postal Address'),(5,'Whatsapp'),(6,'Twitter'),(7,'Instagram'),(8,'Other'),(9,'Internal mailboxes');
/*!40000 ALTER TABLE `tit_MetodoContacto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_MimeType`
--

DROP TABLE IF EXISTS `tit_MimeType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_MimeType` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Extension` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Mime` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_MimeType`
--

LOCK TABLES `tit_MimeType` WRITE;
/*!40000 ALTER TABLE `tit_MimeType` DISABLE KEYS */;
INSERT INTO `tit_MimeType` VALUES (1,'BMP','image/bmp'),(2,'DOC','application/msword'),(3,'DOCX','application/vnd.openxmlformats-officedocument.wordprocessingml.document'),(4,'GIF','image/gif'),(5,'JPEG','image/jpeg'),(6,'JPG','image/jpg'),(7,'MP$','video/mp4'),(8,'OGG','video/ogg'),(9,'QUICKTIME','video/quicktime'),(10,'WEBM','video/webm'),(11,'X-MATROSKA','video/x-matroska'),(12,'ODT','application/vnd.oasis.opendocument.text'),(13,'PDF','application/pdf'),(14,'PNG','image/png'),(15,'TEX','application/x-tex'),(16,'TIFF','image/tiff'),(17,'TIF','image/tiff'),(18,'XML','application/xml');
/*!40000 ALTER TABLE `tit_MimeType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Moneda`
--

DROP TABLE IF EXISTS `tit_Moneda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Moneda` (
  `Id` int(11) NOT NULL,
  `denominacion` char(3) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Moneda`
--

LOCK TABLES `tit_Moneda` WRITE;
/*!40000 ALTER TABLE `tit_Moneda` DISABLE KEYS */;
INSERT INTO `tit_Moneda` VALUES (192,'CUP','Peso Cubano'),(840,'USD','Dólar Estados Unidos'),(978,'EUR','Euro');
/*!40000 ALTER TABLE `tit_Moneda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Pais`
--

DROP TABLE IF EXISTS `tit_Pais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Pais` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Iso3` char(3) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Iso2` char(2) COLLATE utf8_spanish_ci DEFAULT NULL,
  `IsoNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=317 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Pais`
--

LOCK TABLES `tit_Pais` WRITE;
/*!40000 ALTER TABLE `tit_Pais` DISABLE KEYS */;
INSERT INTO `tit_Pais` VALUES (1,'Afghanistan','AFG','AF',NULL),(2,'Albania','ALB','AL',NULL),(3,'Algeria','DZA','DZ',NULL),(4,'American Samoa','ASM','AS',NULL),(5,'Andorra','AND','AD',NULL),(6,'Angola','AGO','AO',NULL),(7,'Anguilla','AIA','AI',NULL),(8,'Antarctica','ATA','AQ',NULL),(9,'Antigua and Barbuda','ATG','AG',NULL),(10,'Argentina','ARG','AR',NULL),(11,'Armenia','ARM','AM',NULL),(12,'Aruba','ABW','AW',NULL),(13,'Australia','AUS','AU',NULL),(14,'Austria','AUT','AT',NULL),(15,'Azerbaijan','AZE','AZ',NULL),(16,'Bahamas','BHS','BS',NULL),(17,'Bahrain','BHR','BH',NULL),(18,'Bangladesh','BGD','BD',NULL),(19,'Barbados','BRB','BB',NULL),(20,'Belarus','BLR','BY',NULL),(21,'Belgium','BEL','BE',NULL),(22,'Belize','BLZ','BZ',NULL),(23,'Benin','BEN','BJ',NULL),(24,'Bermuda','BMU','BM',NULL),(25,'Bhutan','BTN','BT',NULL),(26,'Bolivia','BOL','BO',NULL),(27,'Bosnia and Herzegovina','BIH','BA',NULL),(28,'Botswana','BWA','BW',NULL),(29,'Bouvet Island','BVT','BV',NULL),(30,'Brazil','BRA','BR',NULL),(31,'British Indian Ocean Territory','IOT','IO',NULL),(32,'Brunei Darussalam','BRN','BN',NULL),(33,'Bulgaria','BGR','BG',NULL),(34,'Burkina Faso','BFA','BF',NULL),(35,'Burundi','BDI','BI',NULL),(36,'Cambodia','KHM','KH',NULL),(37,'Cameroon','CMR','CM',NULL),(38,'Canada','CAN','CA',NULL),(39,'Cape Verde','CPV','CV',NULL),(40,'Cayman Islands','CYM','KY',NULL),(41,'Central African Republic','CAF','CF',NULL),(42,'Chad','TCD','TD',NULL),(43,'Chile','CHL','CL',NULL),(44,'China','CHN','CN',NULL),(45,'Christmas Island','CXR','CX',NULL),(46,'Cocos (Keeling) Islands','CCK','CC',NULL),(47,'Colombia','COL','CO',NULL),(48,'Comoros','COM','KM',NULL),(49,'Congo','COG','CG',NULL),(50,'Congo, The Democratic Republic of The','COD','CD',NULL),(51,'Cook Islands','COK','CK',NULL),(52,'Costa Rica','CRI','CR',NULL),(53,'Cote D\'ivoire','CIV','CI',NULL),(54,'Croatia','HRV','HR',NULL),(55,'Cuba','CUB','CU',NULL),(56,'Cyprus','CYP','CY',NULL),(57,'Czech Republic','CZE','CZ',NULL),(58,'Denmark','DNK','DK',NULL),(59,'Djibouti','DJI','DJ',NULL),(60,'Dominica','DMA','DM',NULL),(61,'Dominican Republic','DOM','DO',NULL),(62,'Ecuador','ECU','EC',NULL),(63,'Egypt','EGY','EG',NULL),(64,'El Salvador','SLV','SV',NULL),(65,'Equatorial Guinea','GNQ','GQ',NULL),(66,'Eritrea','ERI','ER',NULL),(67,'Estonia','EST','EE',NULL),(68,'Ethiopia','ETH','ET',NULL),(69,'Falkland Islands (Malvinas)','FLK','FK',NULL),(70,'Faroe Islands','FRO','FO',NULL),(71,'Fiji','FJI','FJ',NULL),(72,'Finland','FIN','FI',NULL),(73,'France','FRA','FR',NULL),(74,'French Guiana','GUF','GF',NULL),(75,'French Polynesia','PYF','PF',NULL),(76,'French Southern Territories','ATF','TF',NULL),(77,'Gabon','GAB','GA',NULL),(78,'Gambia','GMB','GM',NULL),(79,'Georgia','GEO','GE',NULL),(80,'Germany','DEU','DE',NULL),(81,'Ghana','GHA','GH',NULL),(82,'Gibraltar','GIB','GI',NULL),(83,'Greece','GRC','GR',NULL),(84,'Greenland','GRL','GL',NULL),(85,'Grenada','GRD','GD',NULL),(86,'Guadeloupe','GLP','GP',NULL),(87,'Guam','GUM','GU',NULL),(88,'Guatemala','GTM','GT',NULL),(89,'Guinea','GIN','GN',NULL),(90,'Guinea-bissau','GNB','GW',NULL),(91,'Guyana','GUY','GY',NULL),(92,'Haiti','HTI','HT',NULL),(93,'Heard Island and Mcdonald Islands','HMD','HM',NULL),(94,'Vatican City','VAT','VA',NULL),(95,'Honduras','HND','HN',NULL),(96,'Hong Kong','HKG','HK',NULL),(97,'Hungary','HUN','HU',NULL),(98,'Iceland','ISL','IS',NULL),(99,'India','IND','IN',NULL),(100,'Indonesia','IDN','ID',NULL),(101,'Iran, Islamic Republic of','IRN','IR',NULL),(102,'Iraq','IRQ','IQ',NULL),(103,'Ireland','IRL','IE',NULL),(104,'Israel','ISR','IL',NULL),(105,'Italy','ITA','IT',NULL),(106,'Jamaica','JAM','JM',NULL),(107,'Japan','JPN','JP',NULL),(108,'Jordan','JOR','JO',NULL),(109,'Kazakhstan','KAZ','KZ',NULL),(110,'Kenya','KEN','KE',NULL),(111,'Kiribati','KIR','KI',NULL),(112,'Korea, Democratic People\'s Republic of','PRK','KP',NULL),(113,'Korea, Republic of','KOR','KR',NULL),(114,'Kuwait','KWT','KW',NULL),(115,'Kyrgyzstan','KGZ','KG',NULL),(116,'Lao People\'s Democratic Republic','LAO','LA',NULL),(117,'Latvia','LVA','LV',NULL),(118,'Lebanon','LBN','LB',NULL),(119,'Lesotho','LSO','LS',NULL),(120,'Liberia','LBR','LR',NULL),(121,'Libyan Arab Jamahiriya','LBY','LY',NULL),(122,'Liechtenstein','LIE','LI',NULL),(123,'Lithuania','LTU','LT',NULL),(124,'Luxembourg','LUX','LU',NULL),(125,'Macao','MAC','MO',NULL),(126,'Macedonia, the Former Yugoslav Republic of','MKD','MK',NULL),(127,'Madagascar','MDG','MG',NULL),(128,'Malawi','MWI','MW',NULL),(129,'Malaysia','MYS','MY',NULL),(130,'Maldives','MDV','MV',NULL),(131,'Mali','MLI','ML',NULL),(132,'Malta','MLT','MT',NULL),(133,'Marshall Islands','MHL','MH',NULL),(134,'Martinique','MTQ','MQ',NULL),(135,'Mauritania','MRT','MR',NULL),(136,'Mauritius','MUS','MU',NULL),(137,'Mayotte','MYT','YT',NULL),(138,'Mexico','MEX','MX',NULL),(139,'Micronesia, Federated States of','FSM','FM',NULL),(140,'Moldova, Republic of','MDA','MD',NULL),(141,'Monaco','MCO','MC',NULL),(142,'Mongolia','MNG','MN',NULL),(143,'Montserrat','MSR','MS',NULL),(144,'Morocco','MAR','MA',NULL),(145,'Mozambique','MOZ','MZ',NULL),(146,'Myanmar','MMR','MM',NULL),(147,'Namibia','NAM','NA',NULL),(148,'Nauru','NRU','NR',NULL),(149,'Nepal','NPL','NP',NULL),(150,'Netherlands','NLD','NL',NULL),(151,'Netherlands Antilles','ANT','AN',NULL),(152,'New Caledonia','NCL','NC',NULL),(153,'New Zealand','NZL','NZ',NULL),(154,'Nicaragua','NIC','NI',NULL),(155,'Niger','NER','NE',NULL),(156,'Nigeria','NGA','NG',NULL),(157,'Niue','NIU','NU',NULL),(158,'Norfolk Island','NFK','NF',NULL),(159,'Northern Mariana Islands','MNP','MP',NULL),(160,'Norway','NOR','NO',NULL),(161,'Oman','OMN','OM',NULL),(162,'Pakistan','PAK','PK',NULL),(163,'Palau','PLW','PW',NULL),(164,'Palestinian Territory, Occupied','PSE','PS',NULL),(165,'Panama','PAN','PA',NULL),(166,'Papua New Guinea','PNG','PG',NULL),(167,'Paraguay','PRY','PY',NULL),(168,'Peru','PER','PE',NULL),(169,'Philippines','PHL','PH',NULL),(170,'Pitcairn','PCN','PN',NULL),(171,'Poland','POL','PL',NULL),(172,'Portugal','PRT','PT',NULL),(173,'Puerto Rico','PRI','PR',NULL),(174,'Qatar','QAT','QA',NULL),(175,'Reunion','REU','RE',NULL),(176,'Romania','ROU','RO',NULL),(177,'Russian Federation','RUS','RU',NULL),(178,'Rwanda','RWA','RW',NULL),(179,'Saint Helena','SHN','SH',NULL),(180,'Saint Kitts and Nevis','KNA','KN',NULL),(181,'Saint Lucia','LCA','LC',NULL),(182,'Saint Pierre and Miquelon','SPM','PM',NULL),(183,'Saint Vincent and The Grenadines','VCT','VC',NULL),(184,'Samoa','WSM','WS',NULL),(185,'San Marino','SMR','SM',NULL),(186,'Sao Tome and Principe','STP','ST',NULL),(187,'Saudi Arabia','SAU','SA',NULL),(188,'Senegal','SEN','SN',NULL),(189,'Serbia and Montenegro','SCG','CS',NULL),(190,'Seychelles','SYC','SC',NULL),(191,'Sierra Leone','SLE','SL',NULL),(192,'Singapore','SGP','SG',NULL),(193,'Slovakia','SVK','SK',NULL),(194,'Slovenia','SVN','SI',NULL),(195,'Solomon Islands','SLB','SB',NULL),(196,'Somalia','SOM','SO',NULL),(197,'South Africa','ZAF','ZA',NULL),(198,'South Georgia and The South Sandwich Islands','SGS','GS',NULL),(199,'Spain','ESP','ES',NULL),(200,'Sri Lanka','LKA','LK',NULL),(201,'Sudan','SDN','SD',NULL),(202,'Suriname','SUR','SR',NULL),(203,'Svalbard and Jan Mayen','SJM','SJ',NULL),(204,'Swaziland','SWZ','SZ',NULL),(205,'Sweden','SWE','SE',NULL),(206,'Switzerland','CHE','CH',NULL),(207,'Syrian Arab Republic','SYR','SY',NULL),(208,'Taiwan, Province of China','TWN','TW',NULL),(209,'Tajikistan','TJK','TJ',NULL),(210,'Tanzania, United Republic of','TZA','TZ',NULL),(211,'Thailand','THA','TH',NULL),(212,'Timor-leste','TLS','TL',NULL),(213,'Togo','TGO','TG',NULL),(214,'Tokelau','TKL','TK',NULL),(215,'Tonga','TON','TO',NULL),(216,'Trinidad and Tobago','TTO','TT',NULL),(217,'Tunisia','TUN','TN',NULL),(218,'Turkey','TUR','TR',NULL),(219,'Turkmenistan','TKM','TM',NULL),(220,'Turks and Caicos Islands','TCA','TC',NULL),(221,'Tuvalu','TUV','TV',NULL),(222,'Uganda','UGA','UG',NULL),(223,'Ukraine','UKR','UA',NULL),(224,'United Arab Emirates','ARE','AE',NULL),(225,'United Kingdom','GBR','GB',NULL),(226,'United States','USA','US',NULL),(227,'United States Minor Outlying Islands','UMI','UM',NULL),(228,'Uruguay','URY','UY',NULL),(229,'Uzbekistan','UZB','UZ',NULL),(230,'Vanuatu','VUT','VU',NULL),(231,'Venezuela','VEN','VE',NULL),(232,'Viet Nam','VNM','VN',NULL),(233,'Virgin Islands, British','VGB','VG',NULL),(234,'Virgin Islands, U.S.','VIR','VI',NULL),(235,'Wallis and Futuna','WLF','WF',NULL),(236,'Western Sahara','ESH','EH',NULL),(237,'Yemen','YEM','YE',NULL),(238,'Zambia','ZMB','ZM',NULL),(239,'Zimbabwe','ZWE','ZW',NULL),(300,'Kosovo',NULL,'XK',NULL),(301,'Montenegro','MNE','ME',NULL),(302,'Easter Island',NULL,NULL,NULL),(303,'Guernsey','GGY','GG',NULL),(304,'Europe','EU',NULL,NULL),(305,'Serbia','SRB','RS',NULL),(306,'Satellite Provider','A2',NULL,NULL),(307,'Anonymous Proxy','A1',NULL,NULL),(308,'Isle of Man','IMN','IM',NULL),(309,'Jersey','JEY','JE',NULL),(310,'Asia/Pacific Region','AP',NULL,NULL),(312,'Saint Martin','MAF','MF',NULL),(313,'Bonaire, Saint Eustatius and Saba','BES','BQ',NULL),(314,'Curaçao','CUW','CW',NULL),(315,'Sint Maarten (Dutch part)','SXM','SX',NULL),(316,'Aland Islands','ALA',NULL,NULL);
/*!40000 ALTER TABLE `tit_Pais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Personas`
--

DROP TABLE IF EXISTS `tit_Personas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Personas` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `idTipo` int(11) DEFAULT NULL,
  `IdActividad` int(11) NOT NULL DEFAULT 1,
  `IdPaisOrigen` int(11) NOT NULL DEFAULT 55,
  `IdPaisDoc` int(11) NOT NULL DEFAULT 55,
  `idGender` int(11) NOT NULL DEFAULT 1,
  `IdTitanes` int(11) DEFAULT NULL,
  `IdTipoDoc` int(11) DEFAULT NULL,
  `IsBeneficiario` tinyint(1) NOT NULL DEFAULT 0,
  `Nombre` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `PApellido` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `SApellido` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `BusinessName` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `CommercialName` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `IsPublicOffice` tinyint(1) NOT NULL DEFAULT 0,
  `BusinessPerson` tinyint(1) NOT NULL DEFAULT 0,
  `Documento` varchar(60) CHARACTER SET utf8 DEFAULT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 0,
  `Risk` int(11) NOT NULL DEFAULT 0,
  `PersonProfile` int(11) DEFAULT NULL,
  `Validated` tinyint(1) NOT NULL DEFAULT 0,
  `Admited` tinyint(1) NOT NULL DEFAULT 0,
  `FechaInsc` date DEFAULT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1- Si se ejecuta Person/Delete',
  PRIMARY KEY (`Id`),
  KEY `idTipo` (`idTipo`),
  KEY `IdActividad` (`IdActividad`),
  KEY `IdPaisOrigen` (`IdPaisOrigen`),
  KEY `idGender` (`idGender`),
  KEY `IdPaisDoc` (`IdPaisDoc`),
  KEY `IdTipoDoc` (`IdTipoDoc`),
  CONSTRAINT `tit_Personas_ibfk_2` FOREIGN KEY (`idTipo`) REFERENCES `tit_TipoPersona` (`Id`),
  CONSTRAINT `tit_Personas_ibfk_3` FOREIGN KEY (`IdActividad`) REFERENCES `tit_Actividad` (`Id`),
  CONSTRAINT `tit_Personas_ibfk_4` FOREIGN KEY (`IdPaisOrigen`) REFERENCES `tit_Pais` (`Id`),
  CONSTRAINT `tit_Personas_ibfk_5` FOREIGN KEY (`idGender`) REFERENCES `tit_Sexo` (`Id`),
  CONSTRAINT `tit_Personas_ibfk_6` FOREIGN KEY (`IdPaisDoc`) REFERENCES `tit_Pais` (`Id`),
  CONSTRAINT `tit_Personas_ibfk_7` FOREIGN KEY (`IdTipoDoc`) REFERENCES `tit_TipoDocumento` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Personas`
--

LOCK TABLES `tit_Personas` WRITE;
/*!40000 ALTER TABLE `tit_Personas` DISABLE KEYS */;
INSERT INTO `tit_Personas` VALUES (1,1,7,226,226,2,226197,1,0,'Elizabeth J','Simmons','','','','1978-05-17',0,0,'dfghdf6345',1,1,2,1,1,'2023-09-27',0),(2,1,1,55,55,1,226198,NULL,1,'Pompeyo','Lozada','Valdivia','','',NULL,0,0,NULL,0,0,NULL,1,0,'2023-09-27',0),(3,1,9,226,226,2,229499,1,0,'Gina','Valenzuela','','','','1987-06-25',0,0,'45673457344',0,2,2,0,1,'2023-10-11',0),(4,1,1,26,26,1,229795,1,0,'nombre','apellido','','','','1996-05-23',0,0,'456345634',1,2,2,1,1,'2023-10-19',0),(5,1,1,55,55,1,229796,NULL,1,'el hijodel','su apellido','','','',NULL,0,0,NULL,0,0,NULL,0,0,'2023-10-19',0),(6,1,1,1,1,2,226267,1,0,'Arydea','Benavidez','Alaniz','','','1993-05-23',0,0,'657345634563',1,2,2,1,1,'2023-10-24',0),(7,1,3,26,26,1,226270,1,0,'Tíquico','Martínez','Zambrano','','','1973-05-05',0,0,'354673456345',0,1,2,0,1,'2023-10-26',0),(8,1,1,55,55,1,226278,NULL,1,'jacinto ','Benavides','','','',NULL,0,0,NULL,1,0,NULL,0,1,'2023-10-28',0);
/*!40000 ALTER TABLE `tit_Personas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Razon`
--

DROP TABLE IF EXISTS `tit_Razon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Razon` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Razon`
--

LOCK TABLES `tit_Razon` WRITE;
/*!40000 ALTER TABLE `tit_Razon` DISABLE KEYS */;
INSERT INTO `tit_Razon` VALUES (1,'Housing arrangement'),(2,'Financial assistance'),(3,'Family assistance'),(4,'Travel allowance'),(5,'Purchase of real estate'),(6,'Purchase of personal property'),(7,'Studies'),(8,'Sickness expenses'),(9,'Other'),(10,'Debt repayment'),(11,'Tourism'),(12,'Evangelistic and humanitarian activities'),(13,'Maintenance'),(14,'Widow`s pension');
/*!40000 ALTER TABLE `tit_Razon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Relacion`
--

DROP TABLE IF EXISTS `tit_Relacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Relacion` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `idCliente` int(11) DEFAULT NULL,
  `idBeneficiario` int(11) DEFAULT NULL,
  `idTipo` int(11) DEFAULT NULL,
  `idTitanes` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `idTipo` (`idTipo`),
  KEY `idCliente` (`idCliente`),
  KEY `idBeneficiario` (`idBeneficiario`),
  KEY `idTitanes` (`idTitanes`),
  CONSTRAINT `tit_Relacion_ibfk_3` FOREIGN KEY (`idTipo`) REFERENCES `tit_TipoRelacion` (`id`),
  CONSTRAINT `tit_Relacion_ibfk_4` FOREIGN KEY (`idCliente`) REFERENCES `tit_Personas` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tit_Relacion_ibfk_5` FOREIGN KEY (`idBeneficiario`) REFERENCES `tit_Personas` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Relacion`
--

LOCK TABLES `tit_Relacion` WRITE;
/*!40000 ALTER TABLE `tit_Relacion` DISABLE KEYS */;
INSERT INTO `tit_Relacion` VALUES (1,1,2,1,226314),(2,1,2,20,226315),(3,4,5,9,230722),(4,4,5,20,230723),(5,7,8,1,226460),(6,7,8,20,226461);
/*!40000 ALTER TABLE `tit_Relacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Sexo`
--

DROP TABLE IF EXISTS `tit_Sexo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Sexo` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Sexo` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Letra` char(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Sexo`
--

LOCK TABLES `tit_Sexo` WRITE;
/*!40000 ALTER TABLE `tit_Sexo` DISABLE KEYS */;
INSERT INTO `tit_Sexo` VALUES (1,'Masculino','M'),(2,'Femenino','F');
/*!40000 ALTER TABLE `tit_Sexo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Status`
--

DROP TABLE IF EXISTS `tit_Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Status` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Status`
--

LOCK TABLES `tit_Status` WRITE;
/*!40000 ALTER TABLE `tit_Status` DISABLE KEYS */;
INSERT INTO `tit_Status` VALUES (1,'Created'),(2,'Pending'),(3,'Prepared'),(4,'Recognized'),(5,'Completed'),(6,'Failed'),(7,'Cancelled'),(8,'Finished');
/*!40000 ALTER TABLE `tit_Status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_TipoCuenta`
--

DROP TABLE IF EXISTS `tit_TipoCuenta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_TipoCuenta` (
  `Id` int(11) NOT NULL,
  `nombre` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_TipoCuenta`
--

LOCK TABLES `tit_TipoCuenta` WRITE;
/*!40000 ALTER TABLE `tit_TipoCuenta` DISABLE KEYS */;
INSERT INTO `tit_TipoCuenta` VALUES (1,'TitanesEUR'),(2,'TitanesUSD'),(3,'TitanesPOS'),(4,'TitanesEURT'),(200,'NoTitanes'),(203,'AISCard');
/*!40000 ALTER TABLE `tit_TipoCuenta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_TipoCuentaBenef`
--

DROP TABLE IF EXISTS `tit_TipoCuentaBenef`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_TipoCuentaBenef` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_TipoCuentaBenef`
--

LOCK TABLES `tit_TipoCuentaBenef` WRITE;
/*!40000 ALTER TABLE `tit_TipoCuentaBenef` DISABLE KEYS */;
INSERT INTO `tit_TipoCuentaBenef` VALUES (1,'Savings Account'),(2,'Checking Account'),(3,'Card');
/*!40000 ALTER TABLE `tit_TipoCuentaBenef` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_TipoDocumento`
--

DROP TABLE IF EXISTS `tit_TipoDocumento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_TipoDocumento` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `IdGrupo` int(11) DEFAULT NULL,
  `Nombre` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdGrupo` (`IdGrupo`),
  CONSTRAINT `tit_TipoDocumento_ibfk_1` FOREIGN KEY (`IdGrupo`) REFERENCES `tit_GrupoDocumento` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_TipoDocumento`
--

LOCK TABLES `tit_TipoDocumento` WRITE;
/*!40000 ALTER TABLE `tit_TipoDocumento` DISABLE KEYS */;
INSERT INTO `tit_TipoDocumento` VALUES (1,1,'National Identity Card'),(2,1,'Passport'),(3,1,'Residence Card'),(4,1,'Fiscal Code (Italy)'),(5,1,'National ID'),(6,1,'Driving License'),(7,1,'Representation Authorization'),(8,1,'Tax Identification Code'),(9,1,'Memorandum of Association'),(10,1,'Articles of Incorporation'),(11,1,'Power of Attorney'),(12,1,'Actual Ownership'),(13,2,'Contract'),(14,3,'Payroll'),(15,3,'Autonomous Payment'),(16,2,'Invoices'),(17,2,'Bank Statement'),(18,4,'Account Ownership'),(19,2,'Purchase/Sale'),(20,2,'Loans'),(21,2,'Cancellation of Deposits/Investment'),(22,3,'Personal Income Tax Return'),(23,3,'Corporate Income Tax'),(24,3,'VAT Return'),(25,1,'Foreign Identity Number'),(26,2,'Economic activity registration document'),(27,6,'Contract with Titanes'),(28,2,'Contract with Booking'),(29,1,'Proof of life'),(30,2,'Other Sources of Funds'),(31,7,'Other Documents'),(32,3,'Self-Employed Code');
/*!40000 ALTER TABLE `tit_TipoDocumento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_TipoEntrega`
--

DROP TABLE IF EXISTS `tit_TipoEntrega`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_TipoEntrega` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_TipoEntrega`
--

LOCK TABLES `tit_TipoEntrega` WRITE;
/*!40000 ALTER TABLE `tit_TipoEntrega` DISABLE KEYS */;
INSERT INTO `tit_TipoEntrega` VALUES (1,'Cash'),(2,'Bank Deposit'),(3,'Home Delivery'),(4,'Card'),(5,'ATM');
/*!40000 ALTER TABLE `tit_TipoEntrega` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_TipoPersona`
--

DROP TABLE IF EXISTS `tit_TipoPersona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_TipoPersona` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Tipo` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_TipoPersona`
--

LOCK TABLES `tit_TipoPersona` WRITE;
/*!40000 ALTER TABLE `tit_TipoPersona` DISABLE KEYS */;
INSERT INTO `tit_TipoPersona` VALUES (1,'Physical'),(2,'Legal');
/*!40000 ALTER TABLE `tit_TipoPersona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_TipoRelacion`
--

DROP TABLE IF EXISTS `tit_TipoRelacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_TipoRelacion` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_TipoRelacion`
--

LOCK TABLES `tit_TipoRelacion` WRITE;
/*!40000 ALTER TABLE `tit_TipoRelacion` DISABLE KEYS */;
INSERT INTO `tit_TipoRelacion` VALUES (1,'Grandparent'),(2,'Friend'),(3,'Commercial'),(4,'Spouse'),(5,'Brother-in-law / Sister-in-law'),(6,'Ex-husband/ex-wife'),(7,'Stepbrother/stepsister'),(8,'Sibling'),(9,'Child'),(10,'Labor'),(11,'Grandchild'),(12,'Stepfather / Stepmother'),(13,'Father / Mother'),(14,'Cousin'),(15,'Nephew / Niece'),(16,'Father-in-law'),(17,'Uncle'),(18,'Son-in-law / Daughter-in-law'),(19,'Client / Beneficiary'),(20,'Beneficiary'),(21,'Beneficial owner'),(22,'Representative'),(23,'Authorized'),(24,'Others');
/*!40000 ALTER TABLE `tit_TipoRelacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_TipoTransaccion`
--

DROP TABLE IF EXISTS `tit_TipoTransaccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_TipoTransaccion` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_TipoTransaccion`
--

LOCK TABLES `tit_TipoTransaccion` WRITE;
/*!40000 ALTER TABLE `tit_TipoTransaccion` DISABLE KEYS */;
INSERT INTO `tit_TipoTransaccion` VALUES (1,'Payin'),(2,'Payout'),(3,'SaleExchange'),(4,'BankComission'),(5,'Fee'),(6,'PartnerComission'),(7,'PurchaseExchange'),(8,'Transfer'),(9,'PartnerFee');
/*!40000 ALTER TABLE `tit_TipoTransaccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Token`
--

DROP TABLE IF EXISTS `tit_Token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Token` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Token` text COLLATE utf8_spanish_ci DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Token`
--

LOCK TABLES `tit_Token` WRITE;
/*!40000 ALTER TABLE `tit_Token` DISABLE KEYS */;
INSERT INTO `tit_Token` VALUES (10,'UH9j_iQ0GUofUty9Ac7QM0u67YzrbS5r8Ou4iWY_LP9mXy5CivaT-BzSoonFz87oJ586832-nLkGuXU4XcbgR9C5wA8mvCoVXrpwRuqqEn6Vy1Sb8rYLpoNbQF36k-XAZ79EbdOiqn_6OrAj-shevxEW_OzNm1pQ3rTBmmjYAhmZzGGzrsdg503G9O9QK_ZeoQCRlN-tORYeivJdr5Csr8BGQDqh3WbC03woQfjEC7V5JCh288MFmYttqmedMt0C8-ffjC5iFqgmNeJMFyP6Akks19guGknVLD-4wZ2mdatNMB18HPaQ118wJIE913nTRt2EyTHoRrFEgZ-mEfs0TPF7Up_opF-oLjdPCtXvT_bLtgsOKDA0Nd-MNDjdh1ME8pCcM0r2qyMmPop-BTQB2hgTejqBdxFVVJqg3F-cpVDQmUvzq2lw_Li6pHenvaV4Gtr9puy_ON4Xnog0k-X9LYAdgfC5LMd6H81TMyjJgUun1dMcyGCrkr_vL-8fgLDD_Ml3M7NPFFM8KLp8r9Kq_SOnaCp7fOHRg5CgNkiytiTtilhtvGAKdCBhdn4mp3pOdutnWrxKwOxWdH2wuFwvfoGloi3FIYsFKD0d6qXnAHIbiUXQ-4PFoeW-Jggb6uWw','2023-10-28');
/*!40000 ALTER TABLE `tit_Token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tit_Transacciones`
--

DROP TABLE IF EXISTS `tit_Transacciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tit_Transacciones` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `idTitanes` int(11) DEFAULT NULL,
  `idCuenta` int(11) DEFAULT NULL,
  `idTipo` int(11) DEFAULT NULL,
  `idInstrumento` int(11) DEFAULT NULL,
  `idBeneficiario` int(11) DEFAULT NULL,
  `monto` float NOT NULL DEFAULT 0,
  `concepto` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `idMonedaOriginal` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `idCuenta` (`idCuenta`),
  KEY `idTipo` (`idTipo`),
  KEY `idInstrumento` (`idInstrumento`),
  CONSTRAINT `tit_Transacciones_ibfk_1` FOREIGN KEY (`idCuenta`) REFERENCES `tit_Cuentas` (`Id`),
  CONSTRAINT `tit_Transacciones_ibfk_2` FOREIGN KEY (`idTipo`) REFERENCES `tit_TipoTransaccion` (`Id`),
  CONSTRAINT `tit_Transacciones_ibfk_3` FOREIGN KEY (`idInstrumento`) REFERENCES `tit_InstrumentoPago` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tit_Transacciones`
--

LOCK TABLES `tit_Transacciones` WRITE;
/*!40000 ALTER TABLE `tit_Transacciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `tit_Transacciones` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-11-01 11:50:20
