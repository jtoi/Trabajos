-- MySQL dump 10.11
--
-- Host: localhost    Database: net2ftp
-- ------------------------------------------------------
-- Server version	5.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `net2ftp_log_access`
--

DROP TABLE IF EXISTS `net2ftp_log_access`;
CREATE TABLE `net2ftp_log_access` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `time` time NOT NULL default '00:00:00',
  `remote_addr` text NOT NULL,
  `remote_port` text NOT NULL,
  `http_user_agent` text NOT NULL,
  `page` text NOT NULL,
  `datatransfer` int(10) unsigned default '0',
  `executiontime` mediumint(8) unsigned default '0',
  `ftpserver` text NOT NULL,
  `username` text NOT NULL,
  `state` text NOT NULL,
  `state2` text NOT NULL,
  `screen` text NOT NULL,
  `directory` text NOT NULL,
  `entry` text NOT NULL,
  `http_referer` text NOT NULL,
  KEY `index1` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=917 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `net2ftp_log_access`
--

LOCK TABLES `net2ftp_log_access` WRITE;
/*!40000 ALTER TABLE `net2ftp_log_access` DISABLE KEYS */;
INSERT INTO `net2ftp_log_access` VALUES (880,'2013-03-05','17:19:43','190.15.145.171','49898','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'','','login','','1','','',''),(881,'2013-03-05','17:19:52','190.15.145.171','49897','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','browse','main','1','/httpsdocs','','https://concentradoramf.com/temp/net2/'),(882,'2013-03-05','17:23:49','190.15.145.171','49914','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','logout','main','1','/httpsdocs','','https://concentradoramf.com/temp/net2/index.php'),(883,'2013-03-05','17:24:01','190.15.145.171','49913','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'','','login','','1','','',''),(884,'2013-03-05','17:24:18','190.15.145.171','49920','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'travelsdiscovery.com','travels4','browse','main','1','/httpsdocs','','https://concentradoramf.com/temp/net2/index.php'),(885,'2013-03-05','17:25:17','190.15.145.171','49925','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'travelsdiscovery.com','travels4','browse','main','1','/httpsdocs','','https://concentradoramf.com/temp/net2/index.php'),(886,'2013-03-05','17:32:47','190.15.145.171','49972','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'travelsdiscovery.com','travels4','logout','main','1','/httpsdocs','','https://concentradoramf.com/temp/net2/index.php'),(887,'2013-03-05','17:33:04','190.15.145.171','49978','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'','','login','','1','','',''),(888,'2013-03-05','17:33:44','190.15.145.171','49980','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','browse','main','1','/httpsdocs/amadeus','','https://concentradoramf.com/temp/net2/index.php'),(889,'2013-03-05','17:33:56','190.15.145.171','49983','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','upload','','1','/httpsdocs/amadeus','','https://concentradoramf.com/temp/net2/index.php'),(890,'2013-03-05','17:34:30','190.15.145.171','49984','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','upload','','2','/httpsdocs/amadeus','','https://concentradoramf.com/temp/net2/index.php'),(891,'2013-03-07','19:11:03','190.15.147.51','50737','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'','','login','','1','','',''),(892,'2013-03-07','19:21:39','190.15.147.51','50828','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','browse','main','1','/httpsdocs','','https://concentradoramf.com/temp/net2/'),(893,'2013-03-07','19:22:44','190.15.147.51','50895','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','downloadfile','','1','/httpsdocs','index.php','https://concentradoramf.com/temp/net2/index.php'),(894,'2013-03-07','19:35:44','190.15.147.51','50949','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','upload','','1','/httpsdocs','','https://concentradoramf.com/temp/net2/index.php'),(895,'2013-03-07','19:37:52','190.15.147.51','50961','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','upload','','2','/httpsdocs','','https://concentradoramf.com/temp/net2/index.php'),(896,'2013-03-08','14:52:58','190.15.147.51','62411','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'','','login','','1','','',''),(897,'2013-03-08','14:53:03','190.15.147.51','62410','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','browse','main','1','/httpsdocs','','https://concentradoramf.com/temp/net2/'),(898,'2013-03-08','14:53:10','190.15.147.51','62412','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','upload','','1','/httpsdocs','','https://concentradoramf.com/temp/net2/index.php'),(899,'2013-03-08','14:53:28','190.15.147.51','62415','Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1145.0 Safari/537.1','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','upload','','2','/httpsdocs','','https://concentradoramf.com/temp/net2/index.php'),(900,'2013-03-11','19:30:23','190.15.147.51','60853','Mozilla/5.0 (Windows; U; Windows NT 6.2; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27','/temp/net2/index.php',0,0,'','','login','','1','','',''),(901,'2013-03-27','18:53:00','190.15.147.51','51037','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'','','login','','1','','',''),(902,'2013-03-27','18:53:47','190.15.147.51','51051','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','browse','main','1','/httpsdocs/var2/banners','','https://concentradoramf.com/temp/net2/'),(903,'2013-03-27','18:53:54','190.15.147.51','51077','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','upload','','1','/httpsdocs/var2/banners','','https://concentradoramf.com/temp/net2/index.php'),(904,'2013-03-27','18:54:18','190.15.147.51','51105','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','upload','','2','/httpsdocs/var2/banners','','https://concentradoramf.com/temp/net2/index.php'),(905,'2013-03-28','16:54:41','190.15.147.51','57856','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'','','login','','1','','',''),(906,'2013-03-28','16:54:59','190.15.147.51','57857','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','browse','main','1','/httpsdocs/admin/componente/comercio','','https://concentradoramf.com/temp/net2/'),(907,'2013-03-28','16:55:54','190.15.147.51','57903','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','upload','','1','/httpsdocs/admin/componente/comercio','','https://concentradoramf.com/temp/net2/index.php'),(908,'2013-03-28','16:56:59','190.15.147.51','57930','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','upload','','2','/httpsdocs/admin/componente/comercio','','https://concentradoramf.com/temp/net2/index.php'),(909,'2013-05-07','22:14:01','190.15.147.51','57606','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160 Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'','','login','','1','','',''),(910,'2013-05-07','22:14:31','190.15.147.51','57613','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160 Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','browse','main','1','/httpdocs','','https://concentradoramf.com/temp/net2/'),(911,'2013-05-07','22:14:40','190.15.147.51','57627','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160 Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','browse','main','1','/httpsdocs','','https://concentradoramf.com/temp/net2/index.php'),(912,'2013-05-07','22:15:11','190.15.147.51','57633','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160 Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','view','','1','/httpsdocs','configuration.php','https://concentradoramf.com/temp/net2/index.php'),(913,'2013-05-07','22:19:13','190.15.147.51','57719','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.22 (KHTML, like Gecko) Ubuntu Chromium/25.0.1364.160 Chrome/25.0.1364.160 Safari/537.22','/temp/net2/index.php',0,0,'concentradoramf.com','concentradoramf','browse','main','2','/httpsdocs','','https://concentradoramf.com/temp/net2/index.php'),(914,'2013-10-16','22:54:48','200.55.183.77','48611','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0','/temp/net2/index.php',0,0,'','','login','','1','','',''),(915,'2013-10-16','22:56:03','200.55.183.77','49045','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0','/temp/net2/index.php',0,0,'','','login','','1','','',''),(916,'2013-10-16','22:57:44','200.55.183.77','49618','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0','/temp/net2/index.php',0,0,'localhost','cierreconcentra','browse','main','1','','','https://www.concentradoramf.com/temp/net2/');
/*!40000 ALTER TABLE `net2ftp_log_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `net2ftp_log_consumption_ftpserver`
--

DROP TABLE IF EXISTS `net2ftp_log_consumption_ftpserver`;
CREATE TABLE `net2ftp_log_consumption_ftpserver` (
  `date` date NOT NULL default '0000-00-00',
  `ftpserver` varchar(255) NOT NULL default '0',
  `datatransfer` int(10) unsigned default '0',
  `executiontime` mediumint(8) unsigned default '0',
  PRIMARY KEY  (`date`,`ftpserver`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `net2ftp_log_consumption_ftpserver`
--

LOCK TABLES `net2ftp_log_consumption_ftpserver` WRITE;
/*!40000 ALTER TABLE `net2ftp_log_consumption_ftpserver` DISABLE KEYS */;
/*!40000 ALTER TABLE `net2ftp_log_consumption_ftpserver` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `net2ftp_log_consumption_ipaddress`
--

DROP TABLE IF EXISTS `net2ftp_log_consumption_ipaddress`;
CREATE TABLE `net2ftp_log_consumption_ipaddress` (
  `date` date NOT NULL default '0000-00-00',
  `ipaddress` varchar(15) NOT NULL default '0',
  `datatransfer` int(10) unsigned default '0',
  `executiontime` mediumint(8) unsigned default '0',
  PRIMARY KEY  (`date`,`ipaddress`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `net2ftp_log_consumption_ipaddress`
--

LOCK TABLES `net2ftp_log_consumption_ipaddress` WRITE;
/*!40000 ALTER TABLE `net2ftp_log_consumption_ipaddress` DISABLE KEYS */;
/*!40000 ALTER TABLE `net2ftp_log_consumption_ipaddress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `net2ftp_log_error`
--

DROP TABLE IF EXISTS `net2ftp_log_error`;
CREATE TABLE `net2ftp_log_error` (
  `date` date NOT NULL default '0000-00-00',
  `time` time NOT NULL default '00:00:00',
  `ftpserver` text NOT NULL,
  `username` text NOT NULL,
  `message` text NOT NULL,
  `backtrace` text NOT NULL,
  `state` text NOT NULL,
  `state2` text NOT NULL,
  `directory` text NOT NULL,
  `remote_addr` text NOT NULL,
  `remote_port` text NOT NULL,
  `http_user_agent` text NOT NULL,
  KEY `index1` (`date`,`time`,`ftpserver`(100),`username`(50))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `net2ftp_log_error`
--

LOCK TABLES `net2ftp_log_error` WRITE;
/*!40000 ALTER TABLE `net2ftp_log_error` DISABLE KEYS */;
/*!40000 ALTER TABLE `net2ftp_log_error` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `net2ftp_users`
--

DROP TABLE IF EXISTS `net2ftp_users`;
CREATE TABLE `net2ftp_users` (
  `ftpserver` varchar(255) NOT NULL default '0',
  `username` text NOT NULL,
  `homedirectory` text NOT NULL,
  KEY `index1` (`ftpserver`,`username`(50))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `net2ftp_users`
--

LOCK TABLES `net2ftp_users` WRITE;
/*!40000 ALTER TABLE `net2ftp_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `net2ftp_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-04-25 19:05:59
