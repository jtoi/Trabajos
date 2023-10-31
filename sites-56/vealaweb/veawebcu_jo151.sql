-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 26, 2011 at 06:29 PM
-- Server version: 5.0.92
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `veawebcu_jo151`
--

-- --------------------------------------------------------

--
-- Table structure for table `vea_banner`
--

DROP TABLE IF EXISTS `vea_banner`;
CREATE TABLE IF NOT EXISTS `vea_banner` (
  `bid` int(11) NOT NULL auto_increment,
  `cid` int(11) NOT NULL default '0',
  `type` varchar(30) NOT NULL default 'banner',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `imptotal` int(11) NOT NULL default '0',
  `impmade` int(11) NOT NULL default '0',
  `clicks` int(11) NOT NULL default '0',
  `imageurl` varchar(100) NOT NULL default '',
  `clickurl` varchar(200) NOT NULL default '',
  `date` datetime default NULL,
  `showBanner` tinyint(1) NOT NULL default '0',
  `checked_out` tinyint(1) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `custombannercode` text,
  `catid` int(10) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  `sticky` tinyint(1) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `tags` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY  (`bid`),
  KEY `viewbanner` (`showBanner`),
  KEY `idx_banner_catid` (`catid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `vea_banner`
--

INSERT INTO `vea_banner` (`bid`, `cid`, `type`, `name`, `alias`, `imptotal`, `impmade`, `clicks`, `imageurl`, `clickurl`, `date`, `showBanner`, `checked_out`, `checked_out_time`, `editor`, `custombannercode`, `catid`, `description`, `sticky`, `ordering`, `publish_up`, `publish_down`, `tags`, `params`) VALUES
(1, 1, 'banner', 'OSM 1', 'osm-1', 0, 43, 0, 'osmbanner1.png', 'http://www.opensourcematters.org', '2004-07-07 15:31:29', 1, 0, '0000-00-00 00:00:00', '', '', 13, '', 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(2, 1, 'banner', 'OSM 2', 'osm-2', 0, 49, 0, 'osmbanner2.png', 'http://www.opensourcematters.org', '2004-07-07 15:31:29', 1, 0, '0000-00-00 00:00:00', '', '', 13, '', 0, 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(3, 1, '', 'Joomla!', 'joomla', 0, 480, 0, '', 'http://www.joomla.org', '2006-05-29 14:21:28', 1, 0, '0000-00-00 00:00:00', '', '<a href="{CLICKURL}" target="_blank">{NAME}</a>\r\n<br/>\r\nJoomla! The most popular and widely used Open Source CMS Project in the world.', 14, '', 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(4, 1, '', 'JoomlaCode', 'joomlacode', 0, 480, 0, '', 'http://joomlacode.org', '2006-05-29 14:19:26', 1, 0, '0000-00-00 00:00:00', '', '<a href="{CLICKURL}" target="_blank">{NAME}</a>\r\n<br/>\r\nJoomlaCode, development and distribution made easy.', 14, '', 0, 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(5, 1, '', 'Joomla! Extensions', 'joomla-extensions', 0, 475, 0, '', 'http://extensions.joomla.org', '2006-05-29 14:23:21', 1, 0, '0000-00-00 00:00:00', '', '<a href="{CLICKURL}" target="_blank">{NAME}</a>\r\n<br/>\r\nJoomla! Components, Modules, Plugins and Languages by the bucket load.', 14, '', 0, 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(6, 1, '', 'Joomla! Shop', 'joomla-shop', 0, 475, 0, '', 'http://shop.joomla.org', '2006-05-29 14:23:21', 1, 0, '0000-00-00 00:00:00', '', '<a href="{CLICKURL}" target="_blank">{NAME}</a>\r\n<br/>\r\nFor all your Joomla! merchandise.', 14, '', 0, 4, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(7, 1, '', 'Joomla! Promo Shop', 'joomla-promo-shop', 0, 282, 1, 'shop-ad.jpg', 'http://shop.joomla.org', '2007-09-19 17:26:24', 1, 0, '0000-00-00 00:00:00', '', '', 33, '', 0, 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(8, 1, '', 'Joomla! Promo Books', 'joomla-promo-books', 0, 255, 0, 'shop-ad-books.jpg', 'http://shop.joomla.org/index.php?option=com_wrapper&Itemid=8', '2007-09-19 17:28:01', 1, 0, '0000-00-00 00:00:00', '', '', 33, '', 0, 4, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(9, 2, '', 'Web Empresa Hosting Joomla!', 'web-empresa', 0, 557, 1, '', 'http://www.webempresa.com/', '2008-07-09 01:15:06', 1, 0, '0000-00-00 00:00:00', '', '<a href="{CLICKURL}" target="_blank">{NAME}</a>\r\n<br/>\r\nServicios Profesionales para Joomla:\r\nHosting, Servicio Técnico, Formación y JoomlaFácil.', 14, '', 0, 5, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(10, 3, '', 'CompluSoft', 'complusoft', 0, 558, 1, '', 'http://www.complusoft.es/', '2009-11-05 15:43:19', 1, 0, '0000-00-00 00:00:00', '', '<a href="{CLICKURL}" target="_blank">{NAME}</a>\r\n<br/>\r\nEmpresa de informática situada en la zona este de la Comunidad de Madrid presta sus servicios en las más importantes consultoras y clientes finales.', 14, '', 0, 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `vea_bannerclient`
--

DROP TABLE IF EXISTS `vea_bannerclient`;
CREATE TABLE IF NOT EXISTS `vea_bannerclient` (
  `cid` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `contact` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `extrainfo` text NOT NULL,
  `checked_out` tinyint(1) NOT NULL default '0',
  `checked_out_time` time default NULL,
  `editor` varchar(50) default NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `vea_bannerclient`
--

INSERT INTO `vea_bannerclient` (`cid`, `name`, `contact`, `email`, `extrainfo`, `checked_out`, `checked_out_time`, `editor`) VALUES
(1, 'Open Source Matters', 'Administrator', 'admin@opensourcematters.org', '', 0, '00:00:00', NULL),
(2, 'Web Empresa', 'Web Empresa', 'info@webempresa.com', 'Patrocinador Joomla! Spanish', 0, '00:00:00', ''),
(3, 'CompluSoft', 'CompluSoft', 'info@complusoft.es', 'Patrocinador Joomla! Spanish', 0, '00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `vea_bannertrack`
--

DROP TABLE IF EXISTS `vea_bannertrack`;
CREATE TABLE IF NOT EXISTS `vea_bannertrack` (
  `track_date` date NOT NULL,
  `track_type` int(10) unsigned NOT NULL,
  `banner_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vea_categories`
--

DROP TABLE IF EXISTS `vea_categories`;
CREATE TABLE IF NOT EXISTS `vea_categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`section`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

--
-- Dumping data for table `vea_categories`
--

INSERT INTO `vea_categories` (`id`, `parent_id`, `title`, `name`, `alias`, `image`, `section`, `image_position`, `description`, `published`, `checked_out`, `checked_out_time`, `editor`, `ordering`, `access`, `count`, `params`) VALUES
(2, 0, 'Joomla! enlaces específicos', '', 'joomla-specific-links', 'clock.jpg', 'com_weblinks', 'left', 'Una selección de enlaces relacionados con el proyecto de Joomla!.', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, ''),
(4, 0, 'Joomla!', '', 'joomla', '', 'com_newsfeeds', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 2, 0, 0, ''),
(5, 0, 'Negocios: General', '', 'business-general', '', 'com_newsfeeds', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, ''),
(6, 0, 'Linux', '', 'linux', '', 'com_newsfeeds', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 6, 0, 0, ''),
(7, 0, 'Internet', '', 'internet', '', 'com_newsfeeds', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 7, 0, 0, ''),
(12, 0, 'Contactos', '', 'contacts', '', 'com_contact_details', 'left', 'Detalles de contacto para este sitio web', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0, 0, ''),
(13, 0, 'Joomla', '', 'joomla', '', 'com_banner', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0, 0, ''),
(14, 0, 'Anuncios', '', 'text-ads', '', 'com_banner', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0, 0, ''),
(15, 0, 'Características', '', 'features', '', 'com_content', 'left', '', 0, 0, '0000-00-00 00:00:00', NULL, 6, 0, 0, ''),
(17, 0, 'Beneficios', '', 'benefits', '', 'com_content', 'left', '', 0, 0, '0000-00-00 00:00:00', NULL, 4, 0, 0, ''),
(18, 0, 'Plataformas', '', 'platforms', '', 'com_content', 'left', '', 0, 0, '0000-00-00 00:00:00', NULL, 3, 0, 0, ''),
(19, 0, 'Otros recursos', '', 'other-resources', '', 'com_weblinks', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 2, 0, 0, ''),
(31, 0, 'General', '', 'general', '', '5', 'left', '<p>Preguntas generales sobre el CMS</p>', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, ''),
(33, 0, 'Joomla! Promoción', '', 'joomla-promo', '', 'com_banner', 'left', '', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, ''),
(34, 0, 'Joomla! Spanish', '', 'joomla-spanish', '', 'com_newsfeeds', 'left', 'Grupo Joomla! Spanish y patrocinadores expertos en Joomla!', 1, 0, '0000-00-00 00:00:00', NULL, 8, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `vea_components`
--

DROP TABLE IF EXISTS `vea_components`;
CREATE TABLE IF NOT EXISTS `vea_components` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `menuid` int(11) unsigned NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `admin_menu_link` varchar(255) NOT NULL default '',
  `admin_menu_alt` varchar(255) NOT NULL default '',
  `option` varchar(50) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  `admin_menu_img` varchar(255) NOT NULL default '',
  `iscore` tinyint(4) NOT NULL default '0',
  `params` text NOT NULL,
  `enabled` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `parent_option` (`parent`,`option`(32))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=60 ;

--
-- Dumping data for table `vea_components`
--

INSERT INTO `vea_components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(1, 'Banners', '', 0, 0, '', 'Banner Management', 'com_banners', 0, 'js/ThemeOffice/component.png', 0, 'track_impressions=0\ntrack_clicks=0\ntag_prefix=\n\n', 1),
(2, 'Banners', '', 0, 1, 'option=com_banners', 'Active Banners', 'com_banners', 1, 'js/ThemeOffice/edit.png', 0, '', 1),
(3, 'Clientes', '', 0, 1, 'option=com_banners&c=client', 'Manage Clients', 'com_banners', 2, 'js/ThemeOffice/categories.png', 0, '', 1),
(4, 'Enlaces Web', 'option=com_weblinks', 0, 0, '', 'Manage Weblinks', 'com_weblinks', 0, 'js/ThemeOffice/component.png', 0, 'show_comp_description=1\ncomp_description=\nshow_link_hits=1\nshow_link_description=1\nshow_other_cats=1\nshow_headings=1\nshow_page_title=1\nlink_target=0\nlink_icons=\n\n', 1),
(5, 'Enlaces', '', 0, 4, 'option=com_weblinks', 'View existing weblinks', 'com_weblinks', 1, 'js/ThemeOffice/edit.png', 0, '', 1),
(6, 'Categorías', '', 0, 4, 'option=com_categories&section=com_weblinks', 'Manage weblink categories', '', 2, 'js/ThemeOffice/categories.png', 0, '', 1),
(7, 'Contactos', 'option=com_contact', 0, 0, '', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/component.png', 1, 'contact_icons=2\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_headings=1\nshow_position=1\nshow_email=0\nshow_telephone=1\nshow_mobile=1\nshow_fax=1\nallow_vcard=0\nbanned_email=\nbanned_subject=\nbanned_text=\nvalidate_session=1\ncustom_reply=0\n\n', 1),
(8, 'Contactos', '', 0, 7, 'option=com_contact', 'Edit contact details', 'com_contact', 0, 'js/ThemeOffice/edit.png', 1, '', 1),
(9, 'Categorías', '', 0, 7, 'option=com_categories&section=com_contact_details', 'Manage contact categories', '', 2, 'js/ThemeOffice/categories.png', 1, '', 1),
(10, 'Encuesta', 'option=com_poll', 0, 0, 'option=com_poll', 'Manage Polls', 'com_poll', 0, 'js/ThemeOffice/component.png', 0, '', 1),
(11, 'News Feeds', 'option=com_newsfeeds', 0, 0, '', 'News Feeds Management', 'com_newsfeeds', 0, 'js/ThemeOffice/component.png', 0, '', 1),
(12, 'Feeds', '', 0, 11, 'option=com_newsfeeds', 'Manage News Feeds', 'com_newsfeeds', 1, 'js/ThemeOffice/edit.png', 0, '', 1),
(13, 'Categorías', '', 0, 11, 'option=com_categories&section=com_newsfeeds', 'Manage Categories', '', 2, 'js/ThemeOffice/categories.png', 0, '', 1),
(14, 'Usuario', 'option=com_user', 0, 0, '', '', 'com_user', 0, '', 1, '', 1),
(15, 'Buscar', 'option=com_search', 0, 0, 'option=com_search', 'Search Statistics', 'com_search', 0, 'js/ThemeOffice/component.png', 1, 'enabled=0\n\n', 1),
(16, 'Categorías', '', 0, 1, 'option=com_categories&section=com_banner', 'Categories', '', 3, '', 1, '', 1),
(17, 'Wrapper', 'option=com_wrapper', 0, 0, '', 'Wrapper', 'com_wrapper', 0, '', 1, '', 1),
(18, 'Mail para', '', 0, 0, '', '', 'com_mailto', 0, '', 1, '', 1),
(19, 'Administrador Media', '', 0, 0, 'option=com_media', 'Media Manager', 'com_media', 0, '', 1, 'upload_extensions=bmp,csv,doc,epg,gif,ico,jpg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,EPG,GIF,ICO,JPG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS\nupload_maxsize=10000000\nfile_path=images\nimage_path=images/stories\nrestrict_uploads=1\nallowed_media_usergroup=3\ncheck_mime=1\nimage_extensions=bmp,gif,jpg,png\nignore_extensions=\nupload_mime=image/jpeg,image/gif,image/png,image/bmp,application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,text/plain,application/x-zip\nupload_mime_illegal=text/html\nenable_flash=0\n\n', 1),
(20, 'Artículos', 'option=com_content', 0, 0, '', '', 'com_content', 0, '', 1, 'show_noauth=0\nshow_title=1\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=0\nfilter_tags=\nfilter_attritbutes=\n\n', 1),
(21, 'Administrar Configuración', '', 0, 0, '', 'Configuration', 'com_config', 0, '', 1, '', 1),
(22, 'Administrar Instalaciones', '', 0, 0, '', 'Installer', 'com_installer', 0, '', 1, '', 1),
(23, 'Gestor de idiomas', '', 0, 0, '', 'Idiomas', 'com_languages', 0, '', 1, 'administrator=en-GB\nsite=es-ES\n\n', 1),
(24, 'Mail masivo', '', 0, 0, '', 'Mass Mail', 'com_massmail', 0, '', 1, 'mailSubjectPrefix=\nmailBodySuffix=\n\n', 1),
(25, 'Menú del editor', '', 0, 0, '', 'Menu Editor', 'com_menus', 0, '', 1, '', 1),
(27, 'Mensajes', '', 0, 0, '', 'Messages', 'com_messages', 0, '', 1, '', 1),
(28, 'Administrar modulos', '', 0, 0, '', 'Modules', 'com_modules', 0, '', 1, '', 1),
(29, 'Administrar Plugin', '', 0, 0, '', 'Plugins', 'com_plugins', 0, '', 1, '', 1),
(30, 'Administrar plantilla', '', 0, 0, '', 'Templates', 'com_templates', 0, '', 1, '', 1),
(31, 'Administrar usuarios', '', 0, 0, '', 'Users', 'com_users', 0, '', 1, 'allowUserRegistration=0\nnew_usertype=Registered\nuseractivation=0\nfrontend_userparams=0\n\n', 1),
(32, 'Administrar la caché', '', 0, 0, '', 'Cache', 'com_cache', 0, '', 1, '', 1),
(33, 'Panel de Control', '', 0, 0, '', 'Control Panel', 'com_cpanel', 0, '', 1, '', 1),
(34, 'Administrador de Traducciones', 'option=com_translationsmanager', 0, 0, 'option=com_translationsmanager', 'Administrador de Traducciones', 'com_translationsmanager', 0, 'class:language', 0, '', 1),
(35, 'JCE', 'option=com_jce', 0, 0, 'option=com_jce', 'JCE', 'com_jce', 0, 'components/com_jce/img/logo.png', 0, '\npackage=1', 1),
(36, 'JCE MENU CPANEL', '', 0, 35, 'option=com_jce', 'JCE MENU CPANEL', 'com_jce', 0, 'templates/khepri/images/menu/icon-16-cpanel.png', 0, '', 1),
(37, 'JCE MENU CONFIG', '', 0, 35, 'option=com_jce&type=config', 'JCE MENU CONFIG', 'com_jce', 1, 'templates/khepri/images/menu/icon-16-config.png', 0, '', 1),
(38, 'JCE MENU GROUPS', '', 0, 35, 'option=com_jce&type=group', 'JCE MENU GROUPS', 'com_jce', 2, 'templates/khepri/images/menu/icon-16-user.png', 0, '', 1),
(39, 'JCE MENU PLUGINS', '', 0, 35, 'option=com_jce&type=plugin', 'JCE MENU PLUGINS', 'com_jce', 3, 'templates/khepri/images/menu/icon-16-plugin.png', 0, '', 1),
(40, 'JCE MENU INSTALL', '', 0, 35, 'option=com_jce&type=install', 'JCE MENU INSTALL', 'com_jce', 4, 'templates/khepri/images/menu/icon-16-install.png', 0, '', 1),
(47, 'Xmap', 'option=com_xmap', 0, 0, 'option=com_xmap', 'Xmap', 'com_xmap', 0, 'js/ThemeOffice/component.png', 0, '', 1),
(48, 'Joom!Fish', 'option=com_joomfish', 0, 0, 'option=com_joomfish', 'Joom!Fish', 'com_joomfish', 0, 'components/com_joomfish/assets/images/icon-16-joomfish.png', 0, 'noTranslation=2\ndefaultText=\noverwriteGlobalConfig=1\nstorageOfOriginal=md5\nfrontEndPublish=1\nfrontEndPreview=1\nshowDefaultLanguageAdmin=0\ncopyparams=1\ntranscaching=0\ncachelife=180\nqacaching=1\nqalogging=0\n', 1),
(49, 'Control Panel', '', 0, 48, 'option=com_joomfish', 'Control Panel', 'com_joomfish', 0, 'components/com_joomfish/assets/images/icon-16-cpanel.png', 0, '', 1),
(50, 'Translation', '', 0, 48, 'option=com_joomfish&task=translate.overview', 'Translation', 'com_joomfish', 1, 'components/com_joomfish/assets/images/icon-16-translation.png', 0, '', 1),
(51, 'Orphan Translations', '', 0, 48, 'option=com_joomfish&task=translate.orphans', 'Orphan Translations', 'com_joomfish', 2, 'components/com_joomfish/assets/images/icon-16-orphan.png', 0, '', 1),
(52, 'Manage Translations', '', 0, 48, 'option=com_joomfish&task=manage.overview', 'Manage Translations', 'com_joomfish', 3, 'components/com_joomfish/assets/images/icon-16-manage.png', 0, '', 1),
(53, 'Statistics', '', 0, 48, 'option=com_joomfish&task=statistics.overview', 'Statistics', 'com_joomfish', 4, 'components/com_joomfish/assets/images/icon-16-statistics.png', 0, '', 1),
(54, '', '', 0, 48, 'option=com_joomfish', '', 'com_joomfish', 5, 'components/com_joomfish/assets/images/icon-10-blank.png', 0, '', 1),
(55, 'Languages', '', 0, 48, 'option=com_joomfish&task=languages.show', 'Languages', 'com_joomfish', 6, 'components/com_joomfish/assets/images/icon-16-language.png', 0, '', 1),
(56, 'Content elements', '', 0, 48, 'option=com_joomfish&task=elements.show', 'Content elements', 'com_joomfish', 7, 'components/com_joomfish/assets/images/icon-16-extension.png', 0, '', 1),
(57, 'Plugins', '', 0, 48, 'option=com_joomfish&task=plugin.show', 'Plugins', 'com_joomfish', 8, 'components/com_joomfish/assets/images/icon-16-plugin.png', 0, '', 1),
(58, '', '', 0, 48, 'option=com_joomfish', '', 'com_joomfish', 9, 'components/com_joomfish/assets/images/icon-10-blank.png', 0, '', 1),
(59, 'Help', '', 0, 48, 'option=com_joomfish&task=help.show', 'Help', 'com_joomfish', 10, 'components/com_joomfish/assets/images/icon-16-help.png', 0, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vea_contact_details`
--

DROP TABLE IF EXISTS `vea_contact_details`;
CREATE TABLE IF NOT EXISTS `vea_contact_details` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `con_position` varchar(255) default NULL,
  `address` text,
  `suburb` varchar(100) default NULL,
  `state` varchar(100) default NULL,
  `country` varchar(100) default NULL,
  `postcode` varchar(100) default NULL,
  `telephone` varchar(255) default NULL,
  `fax` varchar(255) default NULL,
  `misc` mediumtext,
  `image` varchar(255) default NULL,
  `imagepos` varchar(20) default NULL,
  `email_to` varchar(255) default NULL,
  `default_con` tinyint(1) unsigned NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `catid` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `mobile` varchar(255) NOT NULL default '',
  `webpage` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vea_contact_details`
--

INSERT INTO `vea_contact_details` (`id`, `name`, `alias`, `con_position`, `address`, `suburb`, `state`, `country`, `postcode`, `telephone`, `fax`, `misc`, `image`, `imagepos`, `email_to`, `default_con`, `published`, `checked_out`, `checked_out_time`, `ordering`, `params`, `user_id`, `catid`, `access`, `mobile`, `webpage`) VALUES
(1, 'Contacto', 'contacto', 'Comerial', 'La Habana', 'La Habana', 'State', 'Cuba', 'Zip Code', 'Telephone', 'Fax', 'A través de este formulario de contacto, haga llegarnos sus comentarios, preguntas o sugerencias. Esto nos ayudará a conocer sus necesidades. \r\n\r\nUna vez enviado el formulario, en 24 horas le contactaremos.\r\n\r\nGracias por escribirnos. \r\nEquipo de VealaWebCuba.com', '', 'top', 'contacto@vealawebcuba.com', 0, 1, 0, '0000-00-00 00:00:00', 1, 'show_name=0\nshow_position=0\nshow_email=0\nshow_street_address=0\nshow_suburb=0\nshow_state=0\nshow_postcode=0\nshow_country=0\nshow_telephone=0\nshow_mobile=0\nshow_fax=0\nshow_webpage=0\nshow_misc=1\nshow_image=0\nallow_vcard=0\ncontact_icons=2\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_email_form=1\nemail_description=1\nshow_email_copy=1\nbanned_email=\nbanned_subject=\nbanned_text=', 0, 12, 0, '', ''),
(2, 'Ofertas', 'ofertas', '', '', '', '', '', '', '', '', 'A través de este formulario usted puede hacernos llegar sus requerimientos. Nosotros le presentaremos un presupuesto estimado. Por ello, es importante que nos detalle los servicios que van a contratar, para poder contar con la mayor información posible para poder hacerle llegar una correcta cotización de nuestros trabajos. Especifíquenos el tipo de moneda en que va a pagar (MN o CUC), para enviarle la cotización según sus requisitos.\r\n\r\nUna vez lleno el formulario, envíenos el mismo, En 24 horas nos pondremos en contacto con usted.', '', NULL, 'ofertas@vealawebcuba.com', 0, 1, 0, '0000-00-00 00:00:00', 2, 'show_name=0\nshow_position=0\nshow_email=0\nshow_street_address=0\nshow_suburb=0\nshow_state=0\nshow_postcode=0\nshow_country=0\nshow_telephone=0\nshow_mobile=0\nshow_fax=0\nshow_webpage=0\nshow_misc=1\nshow_image=0\nallow_vcard=0\ncontact_icons=0\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_email_form=1\nemail_description=\nshow_email_copy=0\nbanned_email=\nbanned_subject=\nbanned_text=', 0, 12, 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `vea_content`
--

DROP TABLE IF EXISTS `vea_content`;
CREATE TABLE IF NOT EXISTS `vea_content` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `title_alias` varchar(255) NOT NULL default '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL default '0',
  `sectionid` int(11) unsigned NOT NULL default '0',
  `mask` int(11) unsigned NOT NULL default '0',
  `catid` int(11) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL default '0',
  `created_by_alias` varchar(255) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` text NOT NULL,
  `version` int(11) unsigned NOT NULL default '1',
  `parentid` int(11) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0',
  `metadata` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_section` (`sectionid`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `vea_content`
--

INSERT INTO `vea_content` (`id`, `title`, `alias`, `title_alias`, `introtext`, `fulltext`, `state`, `sectionid`, `mask`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `version`, `parentid`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`) VALUES
(1, 'Qué es VealawebCuba', 'que-es-vealawebcuba', '', '<div id="annu">\r\n<div class="nU" id="anunnc1"><span class="titleAnunc"><br />¿Qué es VealawebCuba?</span> Fundado en enero del 2011, somos un grupo de trabajadores por cuenta propia (freelance), que ofertamos servicios de programación, diseño y promoción Web, con el deseo de ayudar a los clientes cubanos a promover sus servicios o&nbsp;productos&nbsp;en Internet. <br /><br />Con experiencia de más de 10 años en la actividad relacionada con el diseño, desarrollo y promoción Web, esperamos poder contribuir junto a usted, a lograr este objetivo.<br /><br />Nuestra meta: el <strong>100% de satisfacción</strong> de nuestros clientes.<br /><br />Consulte las <a href="index.php?option=com_content&amp;view=article&amp;id=19:vealawebcuba&amp;catid=31&amp;Itemid=59"><span style="text-decoration: underline;">ventajas </span></a>de utilizar nuestros servicios.</div>\r\n<div class="nU" id="anunnc2"><span class="titleAnunc">Diseño y Programación Web</span> Estudiamos sus necesidades de informatización, definimos las estructuras de&nbsp;la&nbsp;información y realizamos los diseños gráficos y de datos correspondientes a estas necesidades. Con su aprobación de esta primera etapa, comenzamos la programación necesaria para lograr estos objetivos, utilizando modernas herramientas. <br /><br />Utilizamos para nuestro trabajo &nbsp;PHP, MySQL, Postgress SQL, CMS Joomla, CMS WordPress y otras herramientas.<br /><br /><br />Consulte más sobre <span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=35:diseno-web&amp;catid=31&amp;Itemid=65">Diseño Web </a></span>y <span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=13:programacion-web&amp;catid=28&amp;Itemid=61">Programación Web</a></span>.</div>\r\n<div class="nU" id="anunnc3"><span class="titleAnunc">Optimización y Posicionamiento Web (SEO)</span>Millones de sitios en Internet pierden la oportunidad de crecer y hasta competir porque sencillamente no aparecen entre los primeros lugares, cuando sus clientes buscan información en los motores de búsqueda.<br /><br />VealawebCuba cuenta con especialistas que harán lo necesario para que su sitio web sea “amistoso” a los buscadores y que pueda ubicarse entre los primeros treinta, primeros veinte o primeros diez de los principales buscadores, Google, Yahoo y MSN, priorizando Google.<br /><br />Posicionamiento SEO no es más que colocar su sitio&nbsp;por encima&nbsp;de&nbsp;su competencia, que su negocio o empresa esté presente y visible (que sea encontrado) en la web.<br /><br />Consulte más sobre <a href="index.php?option=com_content&amp;view=article&amp;id=25&amp;Itemid=71"><span style="text-decoration: underline;">Posicionamiento Web (SEO)</span></a>.</div>\r\n<div class="nU" id="anunnc4"><span class="titleAnunc">Servicio Offshore</span>Usted puede contratar a VealawebCuba lo mismo proyectos a distancia, que personal especializado para alguna tarea.<br /><br />Ponemos a su disposición diseñadores, programadores o especialistas SEO, los que podrá contratar por&nbsp;el tiempo que sea necesario.<br /><br />Consulte más información sobre<span style="text-decoration: underline;"> <a href="index.php?option=com_content&amp;view=article&amp;id=34%3Aprogramacionoffshore&amp;catid=32&amp;Itemid=27">Servicio Offshore</a></span><strong><a href="index.php?option=com_content&amp;view=article&amp;id=13&amp;Itemid=70">.</a></strong></div>\r\n</div>', '', 1, 5, 0, 31, '2006-10-12 10:00:00', 62, '', '2011-02-12 04:36:49', 62, 0, '0000-00-00 00:00:00', '2006-01-03 01:00:00', '0000-00-00 00:00:00', '', '', 'show_title=0\nlink_titles=\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_vote=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nlanguage=\nkeyref=\nreadmore=', 107, 0, 9, '', '', 0, 1897, 'robots=\nauthor='),
(12, 'Paquetes', 'paquetes', '', '<div><br />Paquetes <strong>VealawebCuba</strong>, válidos sólo para sitios promocionales<br /><a name="Quiero estar en la Web"></a><br /><strong>#1- Quiero estar en la Web</strong><br />Lo conforma un diseño común a seleccionar de una lista de diseños predefinidos, al que se le adiciona el contenido y las imágenes que desea el cliente sobre su negocio. <br />Una vez terminado el sitio, se publica en Internet como un subdominio de VealawebCuba, con el nombre que el cliente escoja. Ejemplo: <a href="http://mi-paladar.vealawebcuba.com">http://mi-paladar.vealawebcuba.com</a><br /><strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Solicitar paquete</a></span></strong><br /><a name="Estoy presente con mi Web"></a><br /><strong>#2- Estoy presente con mi Web</strong><br />Lo conforma un diseño común a seleccionar de una lista de diseños predefinidos, al que se le adiciona el contenido y las imágenes que desea el cliente de su negocio. Se pone con dominio propio que escoge el cliente y hospedaje (hosting) propio. <br />Ejemplo: <a href="http://mi-paladar.com">http://mi-paladar.com</a> <br /><strong></strong><strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Solicitar paquete</a></span></strong><br /><a name="Dentro de los primeros de la Web"></a><br /><strong>#3- Dentro de los primeros de la Web</strong><br />Diseño a la medida, con el contenido y las imágenes que desea el cliente de su negocio. Se pone con dominio propio que escoge el cliente y hospedaje (hosting) propio. Incluye servicio de optimización y posicionamiento del sitio durante un año.<br /><strong></strong>&nbsp; <strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Solicitar paquete</a></span></strong><br /><a name="Mi Web actualizada"></a><br /><strong>#4- Mi Web actualizada</strong><br />Diseño a la medida, con el contenido y las imágenes que desea el cliente de su negocio. Se pone con dominio propio que escoge el cliente y hospedaje (hosting) propio. Incluye servicio de optimización y posicionamiento del sitio durante un año. Se le brinda el servicio de actualización del contenido del sitio durante un año. <br /><strong></strong> <strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Solicitar paquete</a></span></strong></div>', '', 1, 5, 0, 31, '2006-10-05 01:11:29', 62, '', '2011-09-23 23:43:27', 62, 0, '0000-00-00 00:00:00', '2006-10-03 10:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 25, 0, 21, '', '', 0, 286, 'robots=\nauthor='),
(13, 'Programación Web', 'programacion-web', '', '<div><img src="images/stories/imagenprogramacion.png" alt="imagenprogramacion" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" width="272" height="198" />Ya es común el uso de ''scripts'' y aplicaciones web para la informatización de procesos, haciéndola más interactiva,&nbsp;de aspecto profesional y mas fácil de mantener..<br /><br /><strong>VealawebCuba</strong>, ofrece sus servicios de programación con herramientas modernas,&nbsp;adaptandonos&nbsp;a los requisitos concretos de nuestros clientes. No importa qué tan pequeño o grande sea su proyecto, desde web para dar información estática, hasta grandes proyectos de comercio electrónico. Vealaweb Cuba, le podrá siempre ayudar.<br /><br />En <strong>VealawebCuba </strong>tenemos años de experiencia y conocimientos en <strong>PHP, ASP, ASP.NET, JAVA,</strong> etc. para satisfacer cualquier tipo de requisitos de los clientes. Nuestras aplicaciones personalizadas integran a la perfección los diseños seleccionados por los clientes, garantizando el 100% su satisfacción.<br /><br />Estamos comprometidos con la calidad, desde una consulta inicial del cliente, hasta la entrega y publicación del producto final. Somos flexibles en la elección de herramientas de programación web. Para cualquier proyecto, la herramienta de programación se selecciona&nbsp;sólo después de discutirlo con el cliente. Recomendamos a nuestros clientes&nbsp;el uso de herramientas libres.<br /><br />Tan pronto nos haga conocer sus necesidades, en el menor tiempo posible un especialista lo contactará para comenzar a coordinar los trabajos.<br /><br /><strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Solicite nuestros servicios aquí</a></span></strong>.<br /><br /></div>', '', 1, 5, 0, 31, '2006-10-06 16:47:35', 62, '', '2011-03-01 15:45:29', 62, 0, '0000-00-00 00:00:00', '2006-10-05 14:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 27, 0, 11, 'vealawebcuba, programación web, php, asp, java, asp.net, diseño web', 'Programación Web, vealawebcuba, desarrollo web cuba, diseño y programación web.', 0, 227, 'robots=\nauthor='),
(14, 'Diseño de Logos', 'disenologo', '', '<div>﻿<img height="198" width="272" src="images/stories/imagendisenologos.png" alt="imagendisenologos" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" />Un logo le da a su empresa una identidad única, tiene un gran atractivo e invita a su audiencia objetivo. Una vez posicionado el logo, se convierte en un gran conquistador de la mente de sus clientes. Un logo bien logrado, se convierte en la marca principal de la empresa y sus diferentes servicios.<br /><br />Sin embargo, un logotipo profesionalmente diseñado, no sólo representa los productos o servicios que tiene que tratar con él, es también una representación simbólica de la visión de su empresa y sus valores. Es útil para sus signos, membretes, tarjetas de presentación, sitio web, y mucho más. Por lo tanto, para tener un negocio exitoso, usted necesita tener un logo exitoso.<br /><br />En el mundo de hoy, las aplicaciones web se ha convertido en un medio rápido y eficaz para introducir su logo en el mundo y convertirlo cada día más popular.<br /><br />La era digital permite que estos logos puedan ser de naturaleza estática o un flash o gift animado.<br /><br /><strong>VealawebCuba</strong> le ayuda a crear esta identidad gráfica, y los diseños y precios de estos varia en dependencia de la ambición con que usted quiera llegar a sus clientes.﻿<br /><br />Haga <strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">clic aqui</a></span></strong>, para solicitar nuestros servicios.</div>', '', 1, 5, 0, 31, '2006-10-06 21:27:49', 62, '', '2011-02-16 03:53:50', 62, 0, '0000-00-00 00:00:00', '2006-10-05 16:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 13, 0, 19, '', '', 0, 124, 'robots=\nauthor='),
(15, 'Tiendas Virtuales', 'tiendasvirtuales', '', '<div><img src="images/stories/imagentiendavirtual.png" alt="imagentiendavirtual" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" width="272" height="198" />Le ayudamos en <strong>VealawebCuba</strong> no solo a promover sus servicios por la web, sino que también nos especializamos en crear las conocidas "Tiendas Virtuales". <br /><br />La mayoría de los clientes hoy ya no sólo buscan la información, sino que buscan además como pagar por los servicios que reciben por la propia web. <br /><br />Con <strong>VealawebCuba</strong>&nbsp; puede resolver este asunto de cobrarle a sus clientes por Internet y esto es útil para los clientes que puedan vender por Internet, como puede ser el caso de las casas particulares, artistas que vendan sus obras por la web y todo aquel cliente que necesite de estos servicios.<br /><br />Por esto, es necesario el desarrollo de carros o cestas de compra en su sitio web. He aquí la importancia de la solución de comercio electrónico carrito de compras entra en juego. <br /><br /><strong>VealawebCuba</strong> tiene la experiencia y el talento necesario para llevar su sitio web a este nivel de servicios, podemos preparar su sitio web y además gestionarle la posibilidad de cobrar sus servicios online.<br /><br />Si usted está interesado y si usted desea saber más acerca de estos servicios, sólo tiene que contactarnos.</div>', '', 1, 5, 0, 31, '2006-10-06 19:28:35', 62, '', '2011-02-11 18:10:59', 62, 0, '0000-00-00 00:00:00', '2006-10-05 14:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 17, 0, 17, 'Marketing online, empresas de cuba, promoción web, negocios online, tiendas virtuales en Cuba, vealawebcuba, pasarelas de pago, ventas online, soluciones informáticas en Cuba, ventas online en cuba.', 'Cuba tiendas virtuales, promocionar su empresa, comercializar su negocio en internet, ventas en cuba online, cobro online y facilidades de pago en nuestras propuestas para marketing online.', 0, 140, 'robots=\nauthor='),
(17, 'Diseño de Flash', 'disenoflash', '', '<p><img height="198" width="272" src="images/stories/imagendisenoflash.png" alt="imagendisenoflash" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" />La tecnología Flash en la Web es popular. Su popularidad está fundamentada en su atractivo.</p>\r\n<p><br />Aunque no lo recomendamos para un sitio que sus principales clientes radiquen en Cuba, motivado principalmente por la velocidad de navegación y que los sitios en Flash son muy lentos, si es atractivo utilizarlos en una sección o cabezal del sitio web, por su belleza.</p>\r\n<p><br />Una integración de una introducción en Flash o un banner Flash en una página web, puede llamar la mirada de los internautas hacia su página web y se puede aumentar la posibilidad de conseguir un flujo regular de tráfico.&nbsp;</p>\r\n<div><br /><strong>VealawebCuba</strong> le ofrece la fusión de la creación de un diseño Flash con conceptos de programación por diseñadores experimentados.</div>\r\n<div><br />Haga <strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">clic aqui</a></span></strong>, para solicitar nuestros servicios.</div>', '', 1, 5, 0, 31, '2006-10-07 09:30:37', 62, '', '2011-02-16 03:53:11', 62, 0, '0000-00-00 00:00:00', '2006-10-05 20:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 18, 0, 20, '', '', 0, 114, 'robots=\nauthor='),
(19, 'VealawebCuba', 'vealawebcuba', '', '<p><br /><img src="images/stories/imagengeneral.png" alt="imagengeneral" class="imgFlotante" style="margin-left: 20px; margin-bottom: 5px; float: right;" width="272" height="198" /><strong>Quienes Somos</strong><br /><br />Con una experiencia de más de 10 años en la actividad relacionada con el <a href="index.php?option=com_content&amp;view=article&amp;id=35:diseno-web&amp;catid=31&amp;Itemid=65">diseño web</a>, <a href="index.php?option=com_content&amp;view=article&amp;id=13:programacion-web&amp;catid=28&amp;Itemid=61">desarrollo web</a> y <a href="index.php?option=com_content&amp;view=article&amp;id=25:posicionamientoweb&amp;catid=31&amp;Itemid=71">promoción web</a>, nos hemos unido un grupo de trabajadores por cuenta propia, que esperamos poder contribuir a promover su negocio a Internet o sencillamente resolver sus problemas informáticos.</p>\r\n<p><br /><strong>Fundadores</strong></p>\r\n<table align="left" border="0">\r\n<tbody>\r\n<tr>\r\n<td style="width: 140px;"><img src="images/stories/fotoCamilo.jpg" alt="fotoCamilo" style="margin: 5px;" width="108" height="101" /></td>\r\n<td style="width: 140px;"><img src="images/stories/fotoJulio.jpg" alt="fotoJulio" style="margin: 5px;" width="108" height="101" /></td>\r\n</tr>\r\n<tr>\r\n<td align="center"><span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=44:camilosanchez&amp;catid=31">Camilo Sánchez</a></span><br />Licencia de trabajo por cuenta propia # 210206<br />NIT: 65120201581</td>\r\n<td align="center"><span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=45:juliotoirac&amp;catid=31">Julio Toirac</a></span><br />Licencia de trabajo por cuenta propia # 223654<br /></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p><br /><br /><br /><strong>&nbsp;</strong></p>\r\n<p><strong><br /></strong></p>\r\n<p><strong><br /></strong></p>\r\n<p><strong><br /><br /></strong></p>\r\n<p><strong><br /></strong></p>\r\n<p><strong><br /></strong></p>\r\n<p><strong><br />Qué hacemos</strong><br /><br />Nuestra fortaleza es el “<strong>saber hacer</strong>” en Internet, que junto con su experiencia en su actividad, lo guiamos a darla a conocer al resto de los cubanos y al mundo, utilizando Internet como vía de comunicación. En este proceso podemos: <a href="index.php?option=com_content&amp;view=article&amp;id=35:diseno-web&amp;catid=31&amp;Itemid=65">diseñar su web</a>, <a href="index.php?option=com_content&amp;view=article&amp;id=13:programacion-web&amp;catid=31&amp;Itemid=61">programarla</a>, gestionarle la adquisición de su identificación en Internet (nombre de dominio, ejemplo: www.mi-negocio.com) así como el hospedaje en servidores,<a href="index.php?option=com_content&amp;view=article&amp;id=25:posicionamientoweb&amp;catid=31&amp;Itemid=71"> optimizar y posicionar </a>su sitio en los buscadores (Google, Yahoo, MSN) entre otros servicios profesionales.</p>\r\n<hr class="system-pagebreak" alt="Página 2" title="Página 2" />\r\n<p><img src="images/stories/imagenposicionamiento.png" alt="imagenposicionamiento" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" width="272" height="198" /><br /><strong>Como usted se beneficia</strong><br /><br />Expande su negocio a todos los lugares del mundo, gana clientes, aumenta su posibilidad de ventas, da a conocer sus servicios o conocimientos. Con nuestra experiencia y junto <strong>VealawebCuba</strong> podrá lograr esto.<br /><br /><strong>Nuestra fortaleza</strong><br /><br />Nuestra experiencia. Esta nos permite ayudarle rápidamente a colocarle en la web, tal y como usted desearía y necesitaría, para que obtenga los beneficios esperados.&nbsp; <br /><br /><strong>Nuestros valores</strong><br /><br />Saber hacer + rapidez + calidad + deseos de ayudarle + saber interpretar desde la óptica web sus deseos, nos permiten brindar un servicio acorde con sus necesidades. <strong>Y sin que la fama se nos suba </strong>…, en nosotros encontrará un aliado ideal.</p>', '', 1, 5, 0, 31, '2006-10-09 07:49:20', 62, '', '2011-02-18 15:21:55', 62, 0, '0000-00-00 00:00:00', '2006-10-07 10:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 55, 0, 13, 'Desarrollo web Cuba, empresas online, marketing, posicionamiento SEO en Cuba, promoción en la web, creación de sitios web, diseño, programación, servicios offshores, optimizar y posicionar, hospedaje sitios web, marketing online en Cuba.', 'Cuba desarrollo web, Vealaweb soluciones informáticas, diseño, programación, promoción online de empresas y negocios. Publicidad de empresas en internet en Cuba. ', 0, 746, 'robots=\nauthor='),
(44, 'Camilo Sanchez', 'camilosanchez', '', '<p><strong><img height="101" width="108" src="images/stories/fotoCamilo.jpg" alt="Camilo Sánchez" title="Camilo Sánchez" style="margin: 0px 10px 10px; float: left;" />DATOS PERSONALES</strong><br /><br /><strong>Nombres y apellidos</strong>: Ernesto Camilo Sánchez Rodríguez<br /><strong>Especialidad</strong>: Sistemas Automatizados de Dirección SAD (actual carrera de Informática)<br /><strong>Año de graduación</strong>: 1989<br /><strong>Centro Universitario</strong>: Instituto Superior Politécnico de Lvov, República de Ucrania.</p>\r\n<p> </p>\r\n<p>Idiomas que domina:</p>\r\n<table cellpadding="5" border="0">\r\n<tbody>\r\n<tr>\r\n<td align="center" valign="middle"><strong>Idioma</strong></td>\r\n<td align="center" valign="middle"><strong>Hablar</strong></td>\r\n<td align="center" valign="middle"><strong>Leer</strong></td>\r\n<td align="center" valign="middle"><strong>Escribir</strong></td>\r\n</tr>\r\n<tr>\r\n<td align="center" valign="middle">Inglés</td>\r\n<td align="center" valign="middle">Regular</td>\r\n<td align="center" valign="middle">Regular</td>\r\n<td align="center" valign="middle">Mal</td>\r\n</tr>\r\n<tr>\r\n<td align="center" valign="middle">Ruso</td>\r\n<td align="center" valign="middle">Bien</td>\r\n<td align="center" valign="middle">Bien</td>\r\n<td align="center" valign="middle">Bien</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p><br /><strong>ESTUDIOS&nbsp; Y CURSOS DE POSGRADO REALIZADOS </strong><br /><strong>Técnico:</strong><br />- Curso Básico de Programación en C.<br />- Curso de Programación Orientada a Objetos.<br />- Análisis y Diseño Orientado a Objetos.<br />- Programación Avanzada en C++ .<br />- Primer encuentro de maestría en CAD/CAM.<br />- Curso de Visual Basic 3.0.<br />- Curso de Foxpro sobre Windows.</p>\r\n<hr alt="Página 2" title="Página 2" class="system-pagebreak" />\r\n<p><strong>ESTUDIOS&nbsp; Y CURSOS DE POSGRADO REALIZADOS </strong><br /><strong>Gerenciales:</strong><br />- Marketing de los servicios.<br />- I y II encuentros sobre Planes de Negocio, impartido por profesores brasileños.<br />- Gerencia Tecnológica.<br />- Curso de Dirección Estratégica.<br />- Curso para Cuadros de la Industria Informática impartido en la ISPJAE<br />- Seminario de Dirección Financiera impartido por profesores de la asociación de la micro y pequeña empresas de España.<br />- Taller de Negociaciones Efectivas.<br />- Taller de Habilidades de Comunicación.<br />- Manejo de redes sociales. Cibermambi II. Instituto de Periodismo, UH<br /><br /><strong>ESTUDIOS AUTODIDACTAS:</strong><br /><strong>Técnicos:</strong><br />- dBase IV, AutoCAD versiones 9.0 y 10.0, AutoLISP, CodeBase&nbsp; versiones 4.0 y 4.2.<br /><strong>Gerenciales:</strong><br />- Estudios sobre la pequeña y mediana empresa.<br />- Marketing en Internet<br />- Gestión negociadora<br />- Ventas en Internet<br />- Posicionamiento en Buscadores<br />- Google Adwords<br /><br /><strong>EVENTOS, SEMINARIOS Y CONGRESOS EN QUE PARTICIPADO.</strong><br />- En la mayoría de los eventos de Informática realizados en Cuba. El último fue en febrero del 2011 donde <a target="_blank" href="images/stories/foto_informatica2011.jpg" title="Informática 2011. Conferencia de Camilo Sánchez">presentó ponencia sobre Comercio Electrónico y Servicios Web</a><br />- Primer encuentro de CAD/CAM&nbsp; del SIME.<br />- Feria Expo Tecno Médica ’96. República Dominicana.<br />- Miembro del Comité Organizador del Primer Taller de Ingeniería del Software.<br />- Feria de la Pequeña y Mediana Empresa, Proyecto SOFTEX 2000, Brasil.</p>\r\n<hr alt="Página 3" title="Página 3" class="system-pagebreak" />\r\n<p><strong>MISIONES TECNICAS EN EL EXTRAJERO.</strong><br />- 1996&nbsp; República Dominicana. Atendió negocios de programación Offshore y participó en la Feria Expo Tecno Médica ’96. <br />- 1998&nbsp; Brasil. Delegación de la Dirección Nacional de Informática del SIME por invitación de Sociedad FUMSOFT.&nbsp; Estudió proyectos de incubadoras de software<br />- 2002 Canadá, acuerdos tecnológicos con proveedores y posibles nuevos negocios.<br /><br /><strong>LUGARES DONDE HA TRABAJADO</strong><br />Desde al año 1989 hasta 1994 - Combinado de Técnica Electrónica Copextel&nbsp; Trabajos de programación en el departamento de Informática.<br /><br />Desde 1994 a 2004 – SOFTEL. En estos 10 años ocupó los siguientes cargos: Sustituto y Jefe de Tecnología y Calidad de la empresa, Jefe del Grupo de Proyecto Offshore para República Dominicana, Jefe de Producción Cooperada para desarrollo de aplicaciones Web.<br /><br />En el 2004 a 2006 – DESOFT. Grupo de proyectos de Formación a Distancia.<br /><br />En mayo del 2006 hasta marzo 2011 Jefe del Grupo de Servicios Web.<br /><br />A paritr de marzo del 2011, trabajo por cuenta propia.</p>', '', 1, 5, 0, 31, '2006-10-10 23:13:33', 62, '', '2011-03-07 22:11:52', 62, 0, '0000-00-00 00:00:00', '2006-10-10 04:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 18, 0, 8, '', '', 0, 85, 'robots=\nauthor='),
(45, 'Julio Toirac', 'juliotoirac', '', '<p><strong><img title="Julio Toirac" style="margin: 0px 10px 10px; float: left;" alt="Julio Toirac" src="images/stories/fotoJulio.jpg" width="108" height="101" />DATOS PERSONALES</strong><br /><br /><strong>Nombres y apellidos</strong>: Julio A. Toirac Abelenda<br /><strong>Especialidad</strong>: Ingeniero Mecánico<br /><strong>Año de graduación</strong>: 1983<br /><strong>Centro Universitario</strong>: ISCAH</p>\r\n<p> </p>\r\n<p>Idiomas que domina:</p>\r\n<table border="0" cellpadding="5">\r\n<tbody>\r\n<tr>\r\n<td valign="middle" align="center"><strong>Idioma</strong></td>\r\n<td valign="middle" align="center"><strong>Hablar</strong></td>\r\n<td valign="middle" align="center"><strong>Leer</strong></td>\r\n<td valign="middle" align="center"><strong>Escribir</strong></td>\r\n</tr>\r\n<tr>\r\n<td valign="middle" align="center">Inglés</td>\r\n<td valign="middle" align="center">Regular</td>\r\n<td valign="middle" align="center">Bien</td>\r\n<td valign="middle" align="center">Bien</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p><strong>&nbsp;</strong><br /><strong>ESTUDIOS&nbsp; Y CURSOS DE POSGRADO REALIZADOS </strong><br /><strong>Técnico:</strong><br />- Curso de Autocad (diseño Asistido por computadora).<br />-Curso de dBase IV<br />-Curso de Base de Datos Oracle (New Horizonts, Santiago de Chile)</p>\r\n<hr class="system-pagebreak" alt="Página 2" title="Página 2" />\r\n<p><strong>ESTUDIOS AUTODIDACTAS:</strong><br /><strong>Técnicos:</strong><br />- Diseño y animación en 3D (3dMax)<br />- AutoCAD versiones 9.0 y 10.0, AutoLISP.<br />- HTML y XHTML (todas las versiones)<br />- Lenguaje ASP.<br />- Lenguaje PHP.<br />- Lenguaje CSS (1, 2 y 3)<br />- JavaScript y JavaScript orientado a objeto.<br />- Programación orientada a objetos y por capas.<br />- Diseño de Bases de Datos.<br />- Programación de Bases de Datos SQLServer y MySQL.<br />- Programación de componentes, módulos y plantillas para CMS.<br />- Programación de Pasarelas de Pagos y Carritos de compras<br /><br /><strong>TRABAJOS MÁS RELEVANTES.</strong><br />- En algunos de los eventos de Informática realizados en Cuba participé realizando los sitios webs de ellos y al frente del aula de Ponencias Virtuales<br />- Participé en el Diseño y Programación del Actual Sistema de Venta de Pasajes "Viajero" .<br />- Programación de decenas de sitios webs entre los que destaca <a target="_blank" href="http://www.cubaweb.cu">Cubaweb.cu</a>.<br /><br /><strong>LUGARES DONDE HA TRABAJADO</strong><br />- Desde al año 1983 hasta 1994 - Distintos centros como Ingeniero Mecánico.<br />- Desde 1993 a 2003 – SICS como especialista programando sitios webs y el Sistema de Venta de Pasajes "Viajero".<br />- Parte del 2004 al 2007 – Softcal y DESOFT. Grupo de proyectos de Comercio Electrónico.<br />- En septiembre del 2007 hasta 2011 Grupo de Servicios Web de GET.</p>', '', 1, 5, 0, 31, '2006-10-10 23:13:33', 62, '', '2011-02-09 19:08:08', 62, 0, '0000-00-00 00:00:00', '2006-10-10 04:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 11, 0, 7, '', '', 0, 48, 'robots=\nauthor='),
(25, 'Posicionamiento Web', 'posicionamientoweb', '', '<p><img height="198" width="272" src="images/stories/imagenposicionamiento.png" alt="imagenposicionamiento" class="imgFlotante" style="margin-left: 20px; margin-bottom: 5px; float: right;" /><br />Estar en la web no es suficiente, <strong>Ud. necesita estar y ser visto! </strong><br /><br />Millones de sitios en internet pierden la oportunidad de crecer, prosperar y hasta competir porque sencillamente no aparecen entre los primeros, no por gusto los primeros siempre llevan ventajas.<br /><br />En <strong>VealawebCuba</strong> optimizamos, que no es más que preparar su sitio para determinados criterios de búsqueda que pueden ser definidos por Ud., si lo desea, o sencillamente decididos por nosotros mediante análisis de los términos más afines a su sitio y que mayor posibilidad de competencia puedan tener.<br /><br />Realizamos adaptaciones a su sitio web para hacerlo “amistoso” a los buscadores y que pueda ubicarse en los primeros treinta, primeros veinte o primeros diez de los principales buscadores, Google, Yahoo y MSN, priorizando Google.<br /><br />Esta etapa comprende:<br />&nbsp;&nbsp; - Cambios en títulos.<br />&nbsp;&nbsp; - Inclusión de palabras claves.<br />&nbsp;&nbsp; - Posibles cambios en los textos para aumentar la densidad de dichas palabras claves.<br />&nbsp;&nbsp; - Alta en los buscadores.<br />&nbsp;&nbsp; - Alta en los directorios de la web según la categoría que corresponda.<br /><br />Podemos realizar también campañas pagadas en los buscadores, para asegurar que su web sea siempre vista en la primera página de los buscadores.<br /><br />Posicionamiento no es más que colocar su sitio dentro de la competencia. Que su negocio o empresa esté presente y visible en la web.</p>\r\n<hr alt="Página 2" title="Página 2" class="system-pagebreak" />\r\n<p><img height="198" width="272" src="images/stories/imagenposicionamiento.png" alt="imagenposicionamiento" class="imgFlotante" style="margin-left: 20px; margin-bottom: 5px; float: right;" /><strong>VealawebCuba</strong> le coloca las frases claves que garantizarán que sea de los primeros, un año junto a nuestro equipo de trabajo por una tarifa económica y el éxito justificará su elección por nosotros.<br /><br />Google, el buscador más importante de la web, realiza periódicamente un chequeo de sus índices, lo que implica cambios en el posicionamiento de su sitio, es&nbsp; por ello que un adecuado criterio de búsqueda que le identifique es imprescindible.<br /><br /><strong>VealawebCuba</strong> le envía información mensual sobre su Posicionamiento y esos Enlaces Populares que apunten a su sitio,&nbsp; el Page Rank de su home o página principal, todos estos detalles técnico son útiles para que conozca la valoración que tienen&nbsp; otros sobre su sitio que no es más que saber qué opinan de su empresa, cómo va su competencia, es algo así como un marketing virtual a su disposición.<br /><br />Garantizamos que su empresa, negocio o proyecto se encuentre entre los primeros treinta primeros sitios del principal buscador de la web, Google.com, en uno o dos meses aproximadamente.<br /><br /><strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Contáctenos</a></span></strong> para solicitar este servicio.</p>', '', 1, 5, 0, 31, '2006-10-11 00:42:31', 62, '', '2011-02-12 05:06:13', 62, 0, '0000-00-00 00:00:00', '2006-10-10 06:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 20, 0, 18, 'Cuba posicionamiento SEO, keywords, buscadores, palabras claves, page rank, popularidad de enlaces, cuba desarrollo web, promoción web, cuba marketing online, Google, Yahoo, MSN, vea la web Cuba, ve a la web Cuba.', 'Posicionamiento SEO, primeros lugares en Google, Cuba desarrollo web, soluciones informáticas para empresas, marketing online en Cuba. ', 0, 186, 'robots=\nauthor='),
(28, 'CMS WordPress', 'wordpress', '', '<div><img height="198" width="272" src="images/stories/imagenwordpress.png" alt="imagenwordpress" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" />\r\n<p>WordPress nació del deseo de construir un sistema de publicación personal, elegante y con una buena arquitectura ("Code is poetry").<br /><br />Basado en PHP, MySQL y licenciado bajo GPL, Wordpress pone especial atención a la estética, estándares web, y usabilidad.<br /><br />La estructura y diseño visual del sitio depende del sistema de plantillas.<br /><br />La filosofía de Wordpress apuesta decididamente por la elegancia, la sencillez y las recomendaciones del W3C.<br /><br />Separa el contenido y el diseño en XHTML y CSS, aunque, como se ha dicho, depende de la plantilla que se esté usando. No obstante, el código que se intenta generar en las entradas ("posts") apuesta por esta característica forzando -si así se elige- un marcado correcto. <br /><br />La gestión y ejecución corre a cargo del sistema de administración con los plugins y los widgets que usan las plantillas.<br /><br />Con <strong>VealawebCuba </strong>usted puede contar con especialistas en el uso de este potente CMS.</p>\r\n<hr alt="Página 2" title="Página 2" class="system-pagebreak" />\r\n<p><img height="198" width="272" src="images/stories/imagenwordpress.png" alt="imagenwordpress" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" /><strong>Ventajas del CMS WordPress</strong><br />- Fácil instalación, actualización y personalización.<br />- Posibilidad de actualización automática del sistema implementada en la versión 2.7.<br />- Múltiples autores o usuarios, junto con sus roles o perfiles que establecen distintos niveles de permisos desde la versión 2.0).<br />- Múltiples blogs o bitácoras (desde la versión 1.6).<br />- Capacidad de crear páginas estáticas (a partir de la versión 1.5).<br />- Permite ordenar artículos y páginas estáticas en categorías, subcategorías y etiquetas ("tags").<br />- Cuatro estados para una entrada ("post"): Publicado, Borrador, Esperando Revisión (nuevo en Wordpress 2.3) y Privado (sólo usuarios registrados), además de uno adicional: Protegido con contraseña.<br />- Editor WYSIWYG "What You See Is What You Get" en inglés, "lo que ves es lo que obtienes" (desde la versión 2.0).<br />- Publicación mediante email.<br />- Importación desde Blogger, Blogware, Dotclear, Greymatter, Livejournal, Movable Type y Typepad, <br />Textpattern y desde cualquier fuente RSS. Se está trabajando para poder importar desde pMachine y Nucleus además de la importación a través de scripts o directamente de base de datos.</p>\r\n<br />\r\n<p>y muchas más ventajas... <span style="text-decoration: underline;"><strong><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Contáctenos </a></strong></span>y solicite sus servicios a <strong>VealawebCuba</strong></p>\r\n<p> </p>\r\n</div>', '', 1, 5, 0, 31, '2006-10-11 01:10:59', 62, '', '2011-02-12 05:00:55', 62, 0, '0000-00-00 00:00:00', '2006-10-10 06:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 18, 0, 16, '', '', 0, 130, 'robots=\nauthor='),
(29, 'Servicio SEO (Search Engine Optimization)', 'servicio-seo', '', '<p><img src="images/stories/imagenposicionamiento.png" alt="imagenposicionamiento" class="imgFlotante" style="margin-left: 20px; margin-bottom: 5px; float: right;" width="272" height="198" /><br />Estar en la web no es suficiente, Ud. necesita<strong> estar y ser visto!</strong></p>\r\n<p>Millones de sitios en internet pierden la oportunidad de crecer, prosperar y hasta competir porque sencillamente no aparecen entre los primeros, no por gusto los primeros siempre llevan ventajas.</p>\r\n<p>En <strong>VealaWeb Cuba</strong> optimizamos, que no es más que preparar su sitio para determinados criterios de búsqueda que pueden ser definidos por Ud., si lo desea, o sencillamente decididos por nosotros mediante análisis de los términos más afines a su sitio y que mayor posibilidad de competencia puedan tener.</p>\r\n<p>Realizamos adaptaciones a su sitio web para hacerlo “amistoso” a los buscadores y que pueda ubicarse en los primeros treinta, primeros veinte o primeros diez de los principales buscadores, Google, Yahoo y MSN, priorizando Google.</p>\r\n<p>Esta etapa comprende:</p>\r\n<p>&nbsp;&nbsp; - Cambios en títulos.<br />&nbsp;&nbsp; - Inclusión de palabras claves.<br />&nbsp;&nbsp; - Posibles cambios en los textos para aumentar la densidad de dichas palabras claves.<br />&nbsp;&nbsp; - Alta en los buscadores.<br />&nbsp;&nbsp; - Alta en los directorios de la web según la categoría que corresponda.</p>\r\n<p>Posicionamiento no es más que colocar su sitio dentro de la competencia. Que su negocio o empresa esté presente y visible en la web.</p>\r\n<p><strong>VealawebCuba </strong>le coloca las frases claves que garantizarán que sea de los primeros, un año junto a nuestro equipo de trabajo por una tarifa económica y el éxito justificará su elección por nosotros.</p>\r\n<p>Google, el buscador más importante de la web, realiza periódicamente un chequeo de sus índices, lo que implica cambios en el posicionamiento de su sitio, es&nbsp; por ello que un adecuado criterio de búsqueda que le identifique es imprescindible.</p>\r\n<p><strong>VealawebCuba</strong> le envía información mensual sobre su Posicionamiento y esos Enlaces Populares que apunten a su sitio,&nbsp; el Page Rank de su home o página principal, todos estos detalles técnico son útiles para que conozca la valoración que tienen&nbsp; otros sobre su sitio que no es más que saber qué opinan de su empresa, cómo va su competencia, es algo así como un marketing virtual a su disposición.</p>\r\n<p>Garantizamos que su empresa, negocio o proyecto se encuentre entre los primeros treinta primeros sitios del principal buscador de la web, Google.com, en los primeros 6 meses.</p>', '', -2, 5, 0, 31, '2006-10-11 03:11:38', 62, '', '2011-02-10 15:29:48', 62, 0, '0000-00-00 00:00:00', '2006-10-10 08:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 11, 0, 0, '', '', 0, 13, 'robots=\nauthor='),
(32, 'Tienes preguntas?', 'tienespreguntas', '', '<p>Para ver las respuestas, haga clic encima de la pregunta. Aparecerá una ventana con la respuesta de la pregunta seleccionada. Haga clic encima de la respuesta, para cerrarla y volver al listado de pregunatas.<br />Si lo que quiere preguntar no aparece en el listado, contáctenos y háganos llegar todas sus dudas. Esta sección se irá alimentando de las propias cuestiones que más ustedes nos hagan llegar. Para escribirnos, seleccione la opción "<span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=1&amp;Itemid=58">Contactar</a></span>" del menú superior.<br /><br /></p>\r\n<div><strong><a href="#" class="pregT">¿Qué necesito para tener un sitio web?<span class="respT">\r\n<p>Para que tenga un sitio web funcionando en Internet, y sea visitado por sus potenciales clientes,&nbsp; usted necesita 5 elementos indispensables (contenido, página web, diseño, dirección web y hospedaje web) :</p>\r\n<p><strong>1. Contenido</strong></p>\r\n<p>Su sitio web necesita contenido como texto e imágenes.&nbsp; Con este contenido, usted va a presentarse usted o su negocio (sus productos y servicios). Es por esto que es muy importante tener un contenido muy bien elaborado, fácil de entender y resumido. Recuerde que los internautas que lleguen a su sitio web buscan información relevante para ellos, pero no tiene tiempo, y dependiendo de la información que encuentren, depende si se interesaran en continuar informándose, sobre sus productos o solicitar sus servicios.</p>\r\n<p><strong>2. Página web</strong></p>\r\n<p>Una vez que ya sabe lo que va a publicar en su nuevo sitio web, debemos organizarlo en diversas páginas web (VealawebCuba le ayudará). Las páginas web forman el contenido del sitio web. El sitio web trata varios temas (acerca de la empresa, servicios, productos, formas de contacto, etc.), y su contenido es colocado en diversas páginas.</p>\r\n<p><strong>3. Diseño y Presentación</strong></p>\r\n<p>El siguiente elemento es el diseño. Para que el sitio web sea visualmente atractivo al visitante, se montan los contenidos (imágenes y textos) con un diseño gráfico. El sitio web es diseñado con diversos colores, tipografías, imágenes y formas. Todos estos elementos forman parte de la presentación del sitio web.</p>\r\n<p><strong>4. Dirección web</strong></p>\r\n<p>Un sitio web se identifica por una dirección web (también conocido como dominio web o URL). Es un identificador único que lo diferencia de otros sitios web. Con este identificador, podemos localizar al sitio web y acceder a sus contenidos. El identificador de un sitio web tiene un nombre y una extensión. Por ejemplo: www.mi-casaparticular.com</p>\r\n<p><strong>5. Alojamiento web</strong></p>\r\n<p>Es el lugar donde el sitio web es colocado para que pueda ser ubicado y accedido por los visitantes. El alojamiento de un sitio web es una computadora llamada Servidor Web. Esta computadora está conectada a la red mundial y permite que cualquier visitante del mundo llegue a su sitio por medio de Internet.</p>\r\n</span></a></strong> <br /><a href="#" class="pregT">¿Cuáles son los beneficios y ventajas de tener un sitio web? <span class="respT">Existen muchas razones por las cuales usted debe tener un sitio web para su empresa&nbsp; <br />o negocio. Hoy en día el internet es una herramienta utilizada en todo el mundo y es importante que usted aproveche esta herramienta de comunicación para potenciar su negocio al máximo. Por ejemplo, solo en América Latina hay más de 400 millones de internautas.<br /><br />Con un sitio web, puede acceder a los siguientes beneficios:<br />Publicidad constante, 24 horas al día, 7 días a la semana, 365 días al año.<br />La información sobre su negocio está disponible para cualquier visitante.<br />Bajos costos de publicidad, con relación a los medios tradicionales de comunicación.<br />Acceso a millones de potenciales clientes de cualquier parte del mundo.<br />Atencion personalizada s sus clientes.<br />Imagen y prestigio.<br />Crecimiento en su cartera de de clientes y ventas.<br />…y muchos otros</span></a></div>', '', 1, 5, 0, 31, '2006-10-10 23:13:33', 62, '', '2011-02-11 13:39:00', 62, 0, '0000-00-00 00:00:00', '2006-10-10 04:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 41, 0, 12, '', '', 0, 262, 'robots=\nauthor='),
(33, 'CMS Joomla', 'joomla', '', '<p><img height="198" width="272" src="images/stories/imagenjoomla.png" alt="imagenjoomla" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" />Joomla es una aplicación de código abierto conocido como Sistema de Gestión de Contenidos (CMS en inglés) que es fácil de usar. Este tipo de gestor de contenidos permite añadir y editar contenido al sitio. Los diseños web con Joomla son ampliamente utilizados debido a sus herramientas de fácil manejo de contenido.<br /><br />Los especialistas de <strong>VealawebCuba</strong>, con gran experiencia en el uso de este CMS, ofrecen diseños Joomla tanto para pequeñas sitios, así como sitios de mayor complejidad por el volumen alto de información que manejan, como pueden ser empresas o grupos empresariales. <br /><br />Joomla permite la personalización de la página web y un diseñador web puede darle una mayor ventaja con su creatividad. Una serie de nuevas características están constantemente disponibles con este gestor de contenidos. Podemos crear diseños web, únicos y atractivos para usted. <br /><br />En nuestros años de experiencia hemos trabajado en una variedad de sitios web como: <br /><br />&nbsp;&nbsp; - Directorios Web <br />&nbsp;&nbsp; - Sitios web de comercio electrónico <br />&nbsp;&nbsp; - Sitios de Hoteles y Cadenas Hoteleras<br />&nbsp;&nbsp; - Galerías de arte <br /><br />Son preferidos por una serie de empresas por la facilidad del mantenimiento de contenido de sus páginas web.</p>\r\n<hr alt="Página 2" title="Página 2" class="system-pagebreak" />\r\n<p>Algunas de las ventajas de este tipo de web son: <br /><br />&nbsp;&nbsp; - La aplicación puede ser utilizada por usuarios no técnicos <br />&nbsp;&nbsp; - Es de fácil uso.<br />&nbsp;&nbsp; - El estilo y el color del diseño puede ser fácilmente alterado <br />&nbsp;&nbsp; - Ayuda a una mejor organización del contenido de la web <br />&nbsp;&nbsp; - Personalización funcional con plugins <br />&nbsp;&nbsp; - No se paga licencia por software por ser una aplicación de distribución libre<br />&nbsp;&nbsp; - Los sitios pueden ser de fácil mantenimiento <br />&nbsp;&nbsp; - Su sitio nunca quedará desactualizado ya que siempre lo mantendremos con la última versión de este CMS.<br />&nbsp;&nbsp; - Los sitios se pueden actualizar fácilmente<br />&nbsp;&nbsp; - Compatible con textos en varios idiomas<br /><br />Nuestros desarrolladores web ofrecen un excelente servicio de programación PHP para diseños web Joomla. <span style="text-decoration: underline;"><strong><a href="index.php?option=com_contact&amp;view=contact&amp;id=1&amp;Itemid=58">Consúltenos</a></strong></span> si tiene dudas o necesita conocer más detalles de las ventajas de utilizar este CMS para sus proyectos.<br /><br />Para contratar este servicio <span style="text-decoration: underline;"><strong><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">haga clic aquí</a></strong></span><br /><br /><strong>RECOMENDAMOS:</strong> Para atraer&nbsp;tráfico a su sitio web, le recomendamos contratarnos&nbsp;el&nbsp;<span style="text-decoration: underline;"><strong><a href="index.php?option=com_content&amp;view=article&amp;id=25&amp;Itemid=71">servicio de posicionamiento Web (SEO)</a></strong></span> para Joomla de <strong>VealawebCuba</strong>. Habilidades profesionales para el posicionamiento optimizado en motores de búsqueda, se aplicarán a su sitio web para obtener un buen ranking en los resultados de la búsqueda Google.</p>', '', 1, 5, 0, 31, '2006-10-11 15:14:11', 62, '', '2011-02-12 04:58:20', 62, 0, '0000-00-00 00:00:00', '2006-10-10 12:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 26, 0, 15, '', '', 0, 173, 'robots=\nauthor=');
INSERT INTO `vea_content` (`id`, `title`, `alias`, `title_alias`, `introtext`, `fulltext`, `state`, `sectionid`, `mask`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `version`, `parentid`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`) VALUES
(34, 'Programación Offshore', 'programacionoffshore', '', '<p><img height="198" width="272" src="images/stories/imagenoffshore.png" alt="imagenoffshore" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" /><br />Brindamos el servicio a distancia para todos los que estén planeando subcontratar el diseño y programación de su sitio web y los servicios asociados, a distancia. Podemos ofrecerle una tarifa fija mensual por especialista, si no quiere contratar los servicios puntuales por hora. Esto aplica para aquellas empresas que tengan proyectos de largo alcance, y esta tarifa plana le brinda la posibilidad de ahorrar mensualmente dinero. Esto es más rentable. Usted puede contratar diseñadores y programadores con este acuerdo de tarifa mensual.<br /><br />Podemos desplegar los diseñadores de alta calidad, animadores y programadores dentro de los 15 días de haber recibido la orden de servicio. Podemos poner a su disposición personal para:<br /><br />• <span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=35:diseno-web&amp;catid=31&amp;Itemid=65">Diseño Web</a></span> <br />• <span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=13:programacion-web&amp;catid=31&amp;Itemid=61">Programación</a></span><br />• <span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=25:posicionamientoweb&amp;catid=31&amp;Itemid=71">Optimizacion y Posicionamiento web (SEO)</a></span><br /><br /><b>Proceso de Contratación Offshore</b></p>\r\n<p><br />• Usted puede contratar a una personal a distancia, por un período mínimo de 2 meses. Los pagos se hacen de forma anticipada y mensualmente, ajustando para el próximo mes, cualquier ajuste necesario.<br />• El personal trabajara en exclusiva para usted durante 8 horas por día, 5 días a la semana. Total de horas de trabajo al mes es de 160.</p>\r\n<p>• Durante el período de contratación, estaremos en contante comunicación.<br /><br /><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Contáctenos </a></span>para recibir ofertas de este servicio.</p>', '', 1, 5, 0, 31, '2006-10-11 17:14:57', 62, '', '2011-02-12 05:04:32', 62, 0, '0000-00-00 00:00:00', '2006-10-10 14:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 19, 0, 10, '', '', 0, 99, 'robots=\nauthor='),
(35, 'Diseño Web', 'diseno-web', '', '<div><img src="images/stories/imagendiseno.png" alt="imagendiseno" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" width="272" height="198" /><br />Su sitio Web no sólo debe informar, sino que también debe ser agradable a la vista. Un buen sitio Web es el que tiene la capacidad de atraer y retener a los visitantes y este es uno de los factores más críticos para el éxito de su negocio. <br /><br />Por lo tanto, si usted quiere construir un nuevo sitio Web o rediseñar uno ya existente, podemos ayudarle. <strong>VealawebCuba</strong> le proporciona el diseño web que satisfaga sus intereses, con profesionalidad y acorde a sus recursos. <br /><br />Años de experiencia en la actividad hacen posible que usted se sienta satisfecho con las propuestas de diseño que le proponemos para crear su&nbsp; sitio Web, que cumplen todos los requisitos del cliente más allá de sus expectativas y con las tendencias modernas del diseño Web. <br /><br />Usted sólo tiene que describirnos su necesidad, le haremos un presupuesto adecuado a sus deseos.<br /><br />Haga <strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">clic aqui</a></span></strong>, para solicitar nuestros servicios.</div>', '', 1, 5, 0, 31, '2006-10-10 23:15:36', 62, '', '2011-03-01 15:45:57', 62, 0, '0000-00-00 00:00:00', '2006-10-10 04:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 21, 0, 14, 'Cuba desarrollo web, diseño de empresas y negocios, logos, diseño en flash y logos, identidades corporativas, tarjetas de presentación, sitios web, diseño para publicidad, anunciós, publicidad de empresas en Cuba.', 'Cuba vealaweb, desarrollo web, diseño en flash, diseño web, diseño de logos. ', 0, 152, 'robots=\nauthor='),
(36, 'Solicitar Ofertas', 'solicitarofertas', '', '<p>A través de este formulario usted puede hacernos llegar sus requerimientos. Nosotros le presentaremos un presupuesto estimado. <br /><br />Por ello, es importante que nos detalle los servicios que van a contratar, para poder contar con la mayor información posible para poder hacerle llegar una correcta cotización de nuestros trabajos. <br /><br />Especifíquenos el tipo de moneda en que va a pagar (MN o CUC), para enviarle la cotización según sus requisitos.</p>\r\n<p><br />Una vez lleno el formulario, envíenos el mismo, En 24 horas nos pondremos en contacto con usted.</p>\r\n<p> </p>\r\n<p><strong>Gracias por solicitar nuestros servicios.</strong></p>', '', -2, 5, 0, 31, '2006-10-10 23:16:20', 62, '', '2011-02-08 17:53:21', 62, 0, '0000-00-00 00:00:00', '2006-10-10 04:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 14, 0, 0, '', '', 0, 20, 'robots=\nauthor='),
(38, 'Precios', 'preciosddd', '', '<div><br />Cada uno de los servicios tiene precios por separado aunque hemos definido Paquetes económicos que incluyen todos los servicios a precios más bajos. Esto no significa que la suma de todos los precios seleccionados sean igual al precio total. Para más de un servicio, ajustaremos los precios, en dependencia del volumen contratado. <br /><br />Estos precios son en base a CUC y para aquellos que prefieran pagar en MN, se aplica la tasa de cambio de CADECA. Estos precios son exclusivos para Cuba.<br /><br />PAQUETES<br /> \r\n<table border="0" cellpadding="5">\r\n<tbody>\r\n<tr style="border: 1px solid #919c96;">\r\n<td style="width: 330px; border: 1px solid #919c96;">#1 Quiero estar en la Web&nbsp; &nbsp;&nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Quiero estar en la Web">vea detalles</a>)<br /></td>\r\n<td style="width: 180px; border: 1px solid #919c96;">120 cuc /anuales<br /></td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919c96;">#2 Estoy presente con mi Web &nbsp;&nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Estoy presente con mi Web">vea detalles</a>)</td>\r\n<td style="width: 180px; border: 1px solid #919c96;">250 cuc / anuales</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919c96;">#3 Dentro de los primeros de la Web &nbsp;&nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Dentro de los primeros de la Web">vea detalles</a>)</td>\r\n<td style="width: 180px; border: 1px solid #919c96;">350 cuc anuales + 70 cuc trimestrales</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919c96;">#4 Mi Web actualizada &nbsp; &nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Mi Web actualizada">vea detalles</a>)</td>\r\n<td style="width: 180px; border: 1px solid #919c96;">350 cuc / anuales + 95 cuc trimestrales</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<br />SERVICIOS<br /> \r\n<table border="0" cellpadding="5">\r\n<tbody>\r\n<tr style="border: 1px solid #919C96;">\r\n<td style="width: 330px; border: 1px solid #919C96;">Programación Web, Programación con uso de CMS y Programación de Carros de Compra</td>\r\n<td style="width: 180px; border: 1px solid #919C96;">4.50 cuc / hora<br /></td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919C96;">Campaña Google Adwords</td>\r\n<td style="width: 180px; border: 1px solid #919C96;">60.00 CUC /mes + Valor depositado en la campaña</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>', '', -2, 5, 0, 31, '2006-10-11 17:18:14', 62, '', '2011-09-01 12:56:13', 62, 0, '0000-00-00 00:00:00', '2006-10-10 14:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 29, 0, 0, '', '', 0, 249, 'robots=\nauthor='),
(43, 'Servicios', 'servicios', '', '<p>En <strong>VealawebCuba</strong>, con el objetivo de ayudarle a presentarse correctamente en la web, hemos definido un grupo de servicios necesarios para lograr este objetivo. Estos pueden ser contratados total o parcialmente según sus necesidades o las necesidades de su proyecto en la Web. <br /><br />A continuación le damos una breve explicación de cada uno de ellos y si desea ampliar la información de uno de ellos, visite en <strong>VealawebCuba</strong>, la página que detalla cada uno de ellos, dando clic en el enlace que está en cada uno de los nombres de los servicios que estan mas abajo.<br /><strong><br />Servicios ofertados por VealawebCuba:</strong><br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=35:diseno-web&amp;catid=31&amp;Itemid=65">Diseño Web</a>:</strong> Estudio de necesidades, definición de estructura de la información, propuesta gráfica del sitio web.<br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=13:programacion-web&amp;catid=28&amp;Itemid=61">Programación Web</a>:</strong> Una vez definido el diseño web, realizamos la programación, utilizando las herramientas o métodos que mejor se adapten al objetivo a lograr.<br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=33:joomla&amp;catid=31&amp;Itemid=62">CMS Joomla</a>: </strong>Generalmente utilizado en sitios con un volumen de información alto, permite crear sitios web, con administración de la información por el propio cliente de forma remota y utilizando internet, sin necesidad de conocer la programación del sitio web. Joomla es uno de los 3 CMS (Sistemas de Manejo de Contenido) más utilizados en el mundo.<br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=28:wordpress&amp;catid=31&amp;Itemid=63">CMS WordPress</a>:</strong> Generalmente utilizado en sitios con un volumen de información alto, permite crear sitios web, con administración de la información por el propio cliente de forma remota y utilizando internet, sin necesidad de conocer la programación del sitio web. Joomla es uno de los 3 CMS (Sistemas de Manejo de Contenido) más utilizados en el mundo.</p>\r\n<p><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55">Paquetes</a>:</strong> Diseñado para clientes que cuenten con bajos recursos, le brindamos un servicio tipo “maqueta”, pero que le permitirá igualmente no dejar de estar presente en la web con profesionalidad. Incluye a t todos los servicios necesarios para la publicación del sitio web.</p>\r\n<p> </p>\r\n<hr class="system-pagebreak" alt="Página 2" title="Página 2" />\r\n<p><strong><a href="index.php?option=com_content&amp;view=article&amp;id=15:tiendasvirtuales&amp;catid=31&amp;Itemid=64">Tiendas Virtuales</a>:</strong> Para clientes que tengan la posibilidad de poner en Internet servicios que puedan ser adquiridos o al menos reservados por los clientes utilizando carros de compra virtuales. Podemos gestionarle el cobro online si las condiciones del negocio lo permiten.</p>\r\n<p><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=25:posicionamientoweb&amp;catid=31&amp;Itemid=71">Posicionamiento Web (SEO</a>):</strong> Preparación del sitio para Optimización para Motores de Búsqueda (Google, Yahoo, MSN). <br />Diseño Flash: Si ya cuentas con sitio y quiere colocar nuevos anuncios promocionales, te hacemos los diseños de los anuncios según los requisitos del sitio.<br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=14:disenologo&amp;catid=31&amp;Itemid=67">Diseño de Logos</a>:</strong> Brindamos este servicio para aquellos que necesiten crear una identificación del negocio.<strong></strong><br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=34:programacionoffshore&amp;catid=32">Servicio Offshore</a>:</strong> Si no tiene forma de actualizar su sitio web, puede poner en nuestras manos esto, lo hacemos todo por usted, solo tiene que enviarnos los textos, fotos, noticias o cualquier información que desee cambiar, lo haremos por usted.<br /><br /><a href="index.php?option=com_content&amp;view=article&amp;id=46:webmaster&amp;catid=31&amp;Itemid=73">Webmaster:</a> Puede poner en nustras manos la actualziación de su sitio web, los webmaster de vealawebcuba se ocuparán por usted de hacerlo<br /><br /><a href="index.php?option=com_content&amp;view=article&amp;id=47:traduccioningles&amp;catid=31&amp;Itemid=74">Traducción al inglés:</a> Brindamos el servicio de traduccion al ingles de su sitio web.</p>', '', 1, 0, 0, 0, '2006-10-12 09:26:52', 62, '', '2011-08-30 22:18:52', 62, 0, '0000-00-00 00:00:00', '2006-10-11 10:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 36, 0, 1, 'Diseño y Programación Web, creacion de sitios web, posicionamiento seo, keywords, palabras claves, primeros diez, Google, cms joomla, cms word press, tiendas virtuales, Web positioning, logos design, webmaster, diseño de logos, actualización de contenidos.', 'Servicios web, vealawebcuba ofrece creacion de sitios web, atención a empresas online.', 0, 338, 'robots=\nauthor='),
(46, 'Webmaster', 'webmaster', '', '<div><br /><strong>VealawebCuba</strong> le ofrece mantener su&nbsp;página web actualizada si usted no puede hacerlo.<br /><br />Es necesario en este caso, que usted nos haga llegar por alguna vía (email o personalmente) los textos, imágenes, o cualquier cambio de información que quiera realizar en su sitio. El formato de los textos que nos envíe debe estar en .doc, .txt o algún otro formato que podamos copiar. NO debe enviarnos los textos en formatos como&nbsp;PDF.<br /><br />Las modificaciones&nbsp;a realizar no deben implicar cambios de programación, solo del contenido del sitio. Los cambios de programación serán cotizados aparte.<br /><br />Si usted tiene la posibilidad de enviar correo electrónico, puede utilizar esta vía para enviar la información a cambiar por nuestros Webmaster. Escribanos a <span style="text-decoration: underline;"><a href="mailto:webmaster@vealawebcuba.com">webmaster@vealawebcuba.com</a></span><br /><br />En un máximo de 48 horas a partir de recibir el reporte de actualización enviado por usted, su sitio quedará debidamente actualizado. Usted recibirá notificación por email de los trabajos concluidos.<br /><br />Si usted quiere que su sitio aparezca en inglés, podemos brindarle el servicio de traducción de los contenidos.<br /><br />Para contratar el servicio de Webmaster, <span style="text-decoration: underline;"><strong><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">haga clic aquí</a></strong></span></div>', '', 1, 5, 0, 31, '2006-10-06 16:47:35', 62, '', '2011-03-01 15:44:51', 62, 0, '0000-00-00 00:00:00', '2006-10-05 14:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 9, 0, 6, 'vealawebcuba, desarrollo web en Cuba, actualizar contenidos en sitios web, empresas cubanas, posicionamiento SEO, creación de sitios web, actualización de contenidos en internet.', 'Desarrollo web en Cuba, webmaster o servicios de actualización de contenidos en sitios web en Cuba.', 0, 93, 'robots=\nauthor='),
(47, 'Traducción al inglés', 'traduccioningles', '', '<div><br />Podemos ofertarle el servicio de traducción al inglés de su sitio web. <br /><br />Contáctenos para <span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">solicitar </a></span>este servicio.<br /><br /><strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56"></a></span></strong></div>', '', 1, 5, 0, 31, '2006-10-05 01:11:29', 62, '', '2011-03-01 15:44:25', 62, 0, '0000-00-00 00:00:00', '2006-10-03 10:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 5, 0, 5, 'Cuba traducciones online, servicios de traduccion al ingles de sitios web en Cuba, vealaweb, promocion online.', 'Servicios de traducción, su sitio web en versión al inglés, promocione su empresa en otros idiomas, traducciones en vealaweb para su negocio en internet, empresas cubanas en otros idiomas en la web.', 0, 75, 'robots=\nauthor='),
(48, 'Política de Privacidad', 'politica-de-privacidad', '', '<div><strong>¿Qué información recopilamos? </strong><br /><strong>VealawebCuba </strong>solo recopila la información que se solicita en los formularios de contacto, la que nos llega vía email, por lo que no queda registrada en ninguna base de datos automáticamente.<br /><br />Al solicitar esta información en nuestro sitio, se le puede pedir que introduzca su: nombre, dirección de correo electrónico, dirección postal o número de teléfono. Al enviarnos esta información, usted está emitiendo su autorización a poder utilizar estos datos para que nosotros le podamos prestar correctamente los servicios solicitados.<br /><br />Usted puede, sin embargo, visitar nuestro sitio de forma anónima.<br /><br /><strong>¿Cómo utilizamos su información? </strong><br />Toda la información que <strong>VealawebCuba </strong>obtiene de usted puede ser utilizada en una o varias de las siguientes formas:<br /><strong>- </strong>Para personalizar a los clientes. Su información nos ayuda a responder mejor a sus necesidades individuales de contratación.<br /><strong>- </strong>Para mejorar nuestro sitio web. Nos esforzamos continuamente para mejorar nuestro sitio web basándonos en la información y la retroalimentación que recibimos de usted.<br /><strong>- </strong>Para mejorar el servicio al cliente. Su información nos ayuda a responder más eficazmente a sus solicitudes de servicio al cliente y de información en general.<br /><strong>- </strong>La dirección de correo electrónico que usted proporcione para el procesamiento de pedidos, sólo se utilizará para enviarle la información y las actualizaciones correspondientes a su solicitud o algún nuevo servicio de <strong>VealawebCuba</strong>.<br /><br /><strong>¿Cómo protegemos su información? </strong><br />Ponemos en práctica una serie de medidas de seguridad para mantener la seguridad de su información personal cuando tiene acceso a su información personal.</div>\r\n<hr class="system-pagebreak" alt="Página 2" title="Página 2" />\r\n<div><strong>¿Utilizamos cookies? </strong><br /><strong>VealawebCuba </strong>utiliza cookies para entender y guardar sus preferencias para futuras visitas y recopilar datos adicionales sobre tráfico del sitio y la interacción del sitio para que podamos ofrecer mejores experiencia del sitio y herramientas en el futuro.<br /><br />Podemos contratar con terceros proveedores de servicios externos para que nos ayuden a comprender mejor a nuestros visitantes del sitio, como son el caso de los servicios de estadísticas de acceso a sitios web. Estos proveedores no tienen acceso a la información por usted enviada a <strong>VealawebCuba</strong>.<br /><br /><strong>Revelación de información a terceros </strong><br />Nosotros no vendemos, comercializamos o transferimos a terceros sus datos personales. Esto no incluye los terceros en confianza que nos ayudan en la operación de nuestro sitio web, quienes se comprometen a mantener esta información confidencial. También podemos revelar su información cuando creemos que es apropiado para cumplir con la ley, hacer cumplir las políticas de nuestro sitio, o para proteger la nuestra o de otros derechos, propiedad o seguridad. <br /><br /><strong>Ambito de Nuestra Política de Privacidad Online</strong><br />Esta política de privacidad online se aplica únicamente a la información recopilada a través de nuestra página web y no a la información recopilada fuera de línea.<br />Su Consentimiento <br />Al utilizar nuestro sitio, usted acepta nuestra política de privacidad del sitio web.﻿</div>', '', 1, 5, 0, 31, '2011-02-10 18:24:06', 62, '', '2011-02-10 18:44:42', 62, 0, '0000-00-00 00:00:00', '2011-02-10 18:24:06', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 8, 0, 4, '', '', 0, 83, 'robots=\nauthor='),
(49, 'Términos y Condiciones', 'terminos-y-condiciones', '', '<div>PRIMERO:<strong> EL CLIENTE</strong> contrata los servicios del VealawebCuba y es el responsable de proveer la información necesaria a los especialistas designados por <strong>VealawebCuba </strong>para la prestación de los servicios.<br /><br />SEGUNDO: <strong>VealawebCuba </strong>comenzará los trabajos contratados a partir del momento en que la información necesaria para comenzar el sitio ha sido enviada por <strong>EL CLIENTE</strong>. Los costos asociados de envió por correo o encomiendas, son de responsabilidad de <strong>EL CLIENTE</strong>.<br /><br />TERCERO: Concluídos los trabajos realizados por <strong>VealawebCuba</strong>, <strong>EL CLIENTE</strong> deberá notificarnos su aprobación por escrito o vía email. En todos los casos, <strong>EL CLIENTE</strong> tendrá acceso a ver los trabajos terminados a través de una dirección oculta, que será una versión Beta en el caso de un sitio o <strong>VealawebCuba </strong>les presentará los trabajos por medios digitales a los que tenga acceso <strong>EL CLIENTE</strong>. Una vez aprobado por <strong>EL CLIENTE</strong> los trabajos, cualquier modificación posterior, debe ser contratada.<br /><br />CUARTO: Los precios expresados en las páginas de <strong>VealawebCuba</strong>, están en CUC. <strong>EL CLIENTE</strong> pueden pagar todos o parte de los servicios en CUP, aplicando la tasa de cambio del día de contratación de CADECA. Las condiciones del pago de los servicios, se detallan en cada una de las páginas de dichos servicios. De existir algún servicio que sólo sea posible pagarlo en CUC, quedará explícitamente reflejada en la página del servicio correspondiente.<br /><br />QUINTO: <strong>VealawebCuba </strong>no se hace responsable de los contenidos e imágenes publicadas por <strong>EL CLIENTE</strong> en sus sitios, por lo que este último deberá contar con los derechos intelectuales de todo el contenido publicado. <strong>VealawebCuba </strong>se reserva el derecho de no publicar o cancelar el servicio a sitios con contenidos que atenten contra las normas éticas del uso de Internet. <br /><br />SEXTO: DOMINIOS: <strong>VealawebCuba </strong>actúa como representante de un dominio al momento de comprarlo a <strong>EL CLIENTE</strong>. La propiedad de dicho dominio es exclusiva de <strong>EL CLIENTE</strong>. Dicha información es pública. Por esta razón, <strong>VealawebCuba </strong>recomienda a sus clientes comprar el o los dominios por sus propios medios si tienen las condiciones para hacerlo. <strong>EL CLIENTE</strong> es responsable de mantener la renovación del pago del dominio. De no renovar el pago de este servicio,&nbsp; <strong>VealawebCuba </strong>cancelará los servicios asociados al mismo, siendo responsabilidad de <strong>EL CLIENTE</strong>, los daños y perjuicios que esto pueda ocasionarle.<br /><br />SEPTIMO: HOSTING: <strong>VealawebCuba </strong>actúa como representante de <strong>EL CLIENTE</strong> cuando este contrata un hosting propio. <strong>EL CLIENTE</strong> es responsable de la información que publique en dicho hosting y este debe cumplir con las normas internacionales del uso correcto de este tipo de servicio.</div>\r\n<hr class="system-pagebreak" alt="Página 2" title="Página 2" />\r\n<div>OCTAVO: DESARROLLO WEB: <strong>VealawebCuba </strong>se reserva el derecho de incorporar una mención de los desarrollados Web realizados a <strong>EL CLIENTE</strong> en su portafolio de clientes, así como <strong>EL CLIENTE</strong> acepta que en los sitios desarrollados por <strong>VealawebCuba</strong>, se haga mención en la página principal a "Desarrollado por VealawebCuba" con un enlace oculto a <a href="http://www.VealawebCuba.com">http://www.vealawebCuba.com</a>.<br /><br />NOVENO - Limite de los Trabajo: <strong>VealawebCuba </strong>se reserva el derecho de rechazar el desarrollo y diseño de un sitio web en cualquier momento, cuando se considere un sitio web como proyecto inviable, es decir, que lo cotizado inicialmente por <strong>VealawebCuba </strong>sea inferior a lo realmente entregado por <strong>EL CLIENTE</strong>. En este caso, <strong>VealawebCuba </strong>le enviará a <strong>EL CLIENTE</strong> una nueva cotización, la que deberá ser aceptada por este último, para comenzar y / o continuar los trabajos. <br /><br />DÉCIMO - <strong>EL CLIENTE</strong>, acepta los términos y condiciones (anteriormente expresados) al momento de contratar cualquiera de los servicios de <strong>VealawebCuba</strong>.<br />﻿</div>', '', 1, 5, 0, 31, '2011-02-10 18:24:36', 62, '', '2011-02-10 18:56:53', 62, 0, '0000-00-00 00:00:00', '2011-02-10 18:24:36', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 8, 0, 3, '', '', 0, 77, 'robots=\nauthor='),
(50, 'Portafolio', 'portafolio', '', '<div style="text-align: center; height:525px; overflow:scroll;"><br /> \r\n<table border="0">\r\n<tbody>\r\n<tr>\r\n<td width="190"><a target="_blank" href="http://www.lomisa.es"><img title="Portafolio VealawebCuba. www.lomisa.es" style="margin-right: 20px; margin-left: 20px;" alt="Portafolio VealawebCuba. www.lomisa.es" src="images/stories/portafolio/lomisa.jpg" height="164" width="190" /></a></td>\r\n<td width="190"><a target="_blank" href="http://www.chivichana.com"><img title="Portafolio VealawebCuba. www.chivichana.com" style="margin-right: 20px; margin-left: 20px;" alt="Portafolio VealawebCuba. www.chivichana.com" src="images/stories/portafolio/chivichana.jpg" height="164" width="190" /></a></td>\r\n<td width="190"><a target="_blank" href="http://www.theginroom.es"><img title="Portafolio VealawebCuba. www.theginroom.es" style="margin-left: 20px; margin-right: 20px;" alt="Portafolio VealawebCuba. www.theginroom.es" src="images/stories/portafolio/gim.jpg" height="164" width="190" /></a></td>\r\n</tr>\r\n<tr>\r\n<td colspan="3" width="190">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td align="center"><a target="_blank" href="http://www.cubartecontemporaneo.com"><img title="Portafolio VealawebCuba. www.cubartecontemporaneo.com" style="margin-left: 20px; margin-right: 20px;" alt="Portafolio VealawebCuba. www.cubartecontemporaneo.com" src="images/stories/portafolio/pintura.png" height="164" width="187" /></a></td>\r\n<td align="center"><a target="_blank" href="http://www.ulisestoirac.com"><img title="Portafolio VealawebCuba. La web de Ulises Toirac www.ulisestoirac.com" style="margin-left: 20px; margin-right: 20px;" alt="Portafolio VealawebCuba. La web de Ulises Toirac www.ulisestoirac.com" src="images/stories/portafolio/ulisestoirac.jpg" height="164" width="187" /></a></td>\r\n<td align="center"><a target="_blank" href="http://www.elbacura.com"><img title="Portafolio VealawebCuba. Restaurante paladar El Bacura" style="margin-left: 20px; margin-right: 20px;" alt="Portafolio VealawebCuba. Restaurante paladar El Bacura" src="images/stories/portafolio/elbacura.jpg" height="164" width="187" /></a></td>\r\n</tr>\r\n<tr>\r\n<td colspan="3" width="190">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td align="center"><a target="_blank" href="http://www.vacation2cuba.com"><img title="Portafolio VealawebCuba. Renta de casas particulares en Cuba" style="margin-left: 20px; margin-right: 20px;" alt="Portafolio VealawebCuba. Renta de casas particulares en Cuba" src="images/stories/portafolio/vacation2cuba.jpg" height="164" width="187" /></a></td>\r\n<td align="center"><a target="_blank" href="http://cubatravel.eu"><img title="Cuba Travel: Agencia de Viajes" style="margin-left: 20px; margin-right: 20px;" alt="Cuba Travel: Agencia de Viajes" src="images/stories/portafolio/cubatravel.jpg" height="164" width="187" /></a></td>\r\n</tr>\r\n<tr>\r\n<td colspan="3" width="190">&nbsp;</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>', '', 1, 5, 0, 31, '2006-10-11 17:14:57', 62, '', '2011-09-06 12:18:53', 62, 0, '0000-00-00 00:00:00', '2006-10-10 14:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 30, 0, 2, 'sitios web, desarrollo online en Cuba, diseño y programacién de sitios web.', 'Vealaweb, desarrollo web en Cuba, sitios web desarrollados por equipo de vealawebcuba.', 0, 218, 'robots=\nauthor='),
(51, 'Precios', 'precios', '', '<div><br />Cada uno de los servicios tiene precios por separado aunque hemos definido Paquetes económicos que incluyen todos los servicios a precios más bajos. Esto no significa que la suma de todos los precios seleccionados sean igual al precio total. Para más de un servicio, ajustaremos los precios, en dependencia del volumen contratado. <br /><br />Estos precios son en base a CUC y para aquellos que prefieran pagar en MN, se aplica la tasa de cambio de CADECA. Estos precios son exclusivos para Cuba.<br /><br />PAQUETES<br /> \r\n<table border="0" cellpadding="5">\r\n<tbody>\r\n<tr style="border: 1px solid #919c96;">\r\n<td style="width: 330px; border: 1px solid #919c96;">#1 Quiero estar en la Web&nbsp; &nbsp;&nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Quiero estar en la Web">vea detalles</a>)<br /></td>\r\n<td style="width: 180px; border: 1px solid #919c96;">180 cuc /anuales<br /></td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919c96;">#2 Estoy presente con mi Web &nbsp;&nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Estoy presente con mi Web">vea detalles</a>)</td>\r\n<td style="width: 180px; border: 1px solid #919c96;">350 cuc / anuales</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919c96;">#3 Dentro de los primeros de la Web &nbsp;&nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Dentro de los primeros de la Web">vea detalles</a>)</td>\r\n<td style="width: 180px; border: 1px solid #919c96;">450 cuc anuales + 95 cuc trimestrales</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919c96;">#4 Mi Web actualizada &nbsp; &nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Mi Web actualizada">vea detalles</a>)</td>\r\n<td style="width: 180px; border: 1px solid #919c96;">450 cuc / anuales + 140 cuc trimestrales</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<br />SERVICIOS<br /> \r\n<table border="0" cellpadding="5">\r\n<tbody>\r\n<tr style="border: 1px solid #919C96;">\r\n<td style="width: 330px; border: 1px solid #919C96;">Programación Web, Programación con uso de CMS y Programación de Carros de Compra</td>\r\n<td style="width: 180px; border: 1px solid #919C96;">4.50 cuc / hora<br /></td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919C96;">Campaña Google Adwords</td>\r\n<td style="width: 180px; border: 1px solid #919C96;">60.00 CUC /mes + Valor depositado en la campaña</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>', '', 1, 5, 0, 31, '2011-09-01 12:52:56', 62, '', '2011-09-06 03:00:23', 62, 0, '0000-00-00 00:00:00', '2011-09-01 12:52:56', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 5, 0, 1, '', '', 0, 44, 'robots=\nauthor=');

-- --------------------------------------------------------

--
-- Table structure for table `vea_content_frontpage`
--

DROP TABLE IF EXISTS `vea_content_frontpage`;
CREATE TABLE IF NOT EXISTS `vea_content_frontpage` (
  `content_id` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vea_content_frontpage`
--

INSERT INTO `vea_content_frontpage` (`content_id`, `ordering`) VALUES
(14, 2),
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vea_content_rating`
--

DROP TABLE IF EXISTS `vea_content_rating`;
CREATE TABLE IF NOT EXISTS `vea_content_rating` (
  `content_id` int(11) NOT NULL default '0',
  `rating_sum` int(11) unsigned NOT NULL default '0',
  `rating_count` int(11) unsigned NOT NULL default '0',
  `lastip` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vea_core_acl_aro`
--

DROP TABLE IF EXISTS `vea_core_acl_aro`;
CREATE TABLE IF NOT EXISTS `vea_core_acl_aro` (
  `id` int(11) NOT NULL auto_increment,
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `vea_section_value_value_aro` (`section_value`(100),`value`(100)),
  KEY `vea_gacl_hidden_aro` (`hidden`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `vea_core_acl_aro`
--

INSERT INTO `vea_core_acl_aro` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'users', '62', 0, 'Administrator', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vea_core_acl_aro_groups`
--

DROP TABLE IF EXISTS `vea_core_acl_aro_groups`;
CREATE TABLE IF NOT EXISTS `vea_core_acl_aro_groups` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `vea_gacl_parent_id_aro_groups` (`parent_id`),
  KEY `vea_gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `vea_core_acl_aro_groups`
--

INSERT INTO `vea_core_acl_aro_groups` (`id`, `parent_id`, `name`, `lft`, `rgt`, `value`) VALUES
(17, 0, 'ROOT', 1, 22, 'ROOT'),
(28, 17, 'USERS', 2, 21, 'USERS'),
(29, 28, 'Public Frontend', 3, 12, 'Public Frontend'),
(18, 29, 'Registered', 4, 11, 'Registered'),
(19, 18, 'Author', 5, 10, 'Author'),
(20, 19, 'Editor', 6, 9, 'Editor'),
(21, 20, 'Publisher', 7, 8, 'Publisher'),
(30, 28, 'Public Backend', 13, 20, 'Public Backend'),
(23, 30, 'Manager', 14, 19, 'Manager'),
(24, 23, 'Administrator', 15, 18, 'Administrator'),
(25, 24, 'Super Administrator', 16, 17, 'Super Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `vea_core_acl_aro_map`
--

DROP TABLE IF EXISTS `vea_core_acl_aro_map`;
CREATE TABLE IF NOT EXISTS `vea_core_acl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(100) NOT NULL,
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vea_core_acl_aro_sections`
--

DROP TABLE IF EXISTS `vea_core_acl_aro_sections`;
CREATE TABLE IF NOT EXISTS `vea_core_acl_aro_sections` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `vea_gacl_value_aro_sections` (`value`),
  KEY `vea_gacl_hidden_aro_sections` (`hidden`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `vea_core_acl_aro_sections`
--

INSERT INTO `vea_core_acl_aro_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'users', 1, 'Users', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vea_core_acl_groups_aro_map`
--

DROP TABLE IF EXISTS `vea_core_acl_groups_aro_map`;
CREATE TABLE IF NOT EXISTS `vea_core_acl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '',
  `aro_id` int(11) NOT NULL default '0',
  UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vea_core_acl_groups_aro_map`
--

INSERT INTO `vea_core_acl_groups_aro_map` (`group_id`, `section_value`, `aro_id`) VALUES
(25, '', 10);

-- --------------------------------------------------------

--
-- Table structure for table `vea_core_log_items`
--

DROP TABLE IF EXISTS `vea_core_log_items`;
CREATE TABLE IF NOT EXISTS `vea_core_log_items` (
  `time_stamp` date NOT NULL default '0000-00-00',
  `item_table` varchar(50) NOT NULL default '',
  `item_id` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vea_core_log_searches`
--

DROP TABLE IF EXISTS `vea_core_log_searches`;
CREATE TABLE IF NOT EXISTS `vea_core_log_searches` (
  `search_term` varchar(128) NOT NULL default '',
  `hits` int(11) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vea_dbcache`
--

DROP TABLE IF EXISTS `vea_dbcache`;
CREATE TABLE IF NOT EXISTS `vea_dbcache` (
  `id` varchar(32) NOT NULL default '',
  `groupname` varchar(32) NOT NULL default '',
  `expire` datetime NOT NULL default '0000-00-00 00:00:00',
  `value` mediumblob NOT NULL,
  PRIMARY KEY  (`id`,`groupname`),
  KEY `expire` (`expire`,`groupname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vea_groups`
--

DROP TABLE IF EXISTS `vea_groups`;
CREATE TABLE IF NOT EXISTS `vea_groups` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vea_groups`
--

INSERT INTO `vea_groups` (`id`, `name`) VALUES
(0, 'Public'),
(1, 'Registered'),
(2, 'Special');

-- --------------------------------------------------------

--
-- Table structure for table `vea_jce_groups`
--

DROP TABLE IF EXISTS `vea_jce_groups`;
CREATE TABLE IF NOT EXISTS `vea_jce_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `users` text NOT NULL,
  `types` varchar(255) NOT NULL,
  `components` text NOT NULL,
  `rows` text NOT NULL,
  `plugins` varchar(255) NOT NULL,
  `published` tinyint(3) NOT NULL,
  `ordering` int(11) NOT NULL,
  `checked_out` tinyint(3) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vea_jce_groups`
--

INSERT INTO `vea_jce_groups` (`id`, `name`, `description`, `users`, `types`, `components`, `rows`, `plugins`, `published`, `ordering`, `checked_out`, `checked_out_time`, `params`) VALUES
(1, 'Default', 'Default group for all users with edit access', '', '19,20,21,23,24,25', '', '5,6,7,8,9,10,11,12,13,14,15,16,17,18;19,20,21,22,23,24,25,26,27,29,30,31,34,46;35,36,37,38,39,40,41,42,43,44,45;47,48,49,50,51,52,53,55,56', '1,2,3,4,5,19,20,35,36,37,38,39,40,47,48,49,50,51,52,53,55,56', 1, 1, 0, '0000-00-00 00:00:00', ''),
(2, 'Front End', 'Sample Group for Authors, Editors, Publishers', '', '19,20,21', '', '5,6,7,8,9,12,13,14,15,16,17,18,26,27;19,20,24,25,29,30,34,41,42,43,45,46,48,49;23,31,37,38,40,44,47,50,51,52,53,55,56', '5,19,20,48,49,1,3,37,38,40,47,50,51,52,53,55,56', 0, 2, 0, '0000-00-00 00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `vea_jce_plugins`
--

DROP TABLE IF EXISTS `vea_jce_plugins`;
CREATE TABLE IF NOT EXISTS `vea_jce_plugins` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `layout` varchar(255) NOT NULL,
  `row` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(3) NOT NULL,
  `editable` tinyint(3) NOT NULL,
  `iscore` tinyint(3) NOT NULL,
  `elements` varchar(255) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `plugin` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=57 ;

--
-- Dumping data for table `vea_jce_plugins`
--

INSERT INTO `vea_jce_plugins` (`id`, `title`, `name`, `type`, `icon`, `layout`, `row`, `ordering`, `published`, `editable`, `iscore`, `elements`, `checked_out`, `checked_out_time`) VALUES
(1, 'Context Menu', 'contextmenu', 'plugin', '', '', 0, 0, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(2, 'File Browser', 'browser', 'plugin', '', '', 0, 0, 1, 1, 1, '', 0, '0000-00-00 00:00:00'),
(3, 'Inline Popups', 'inlinepopups', 'plugin', '', '', 0, 0, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(4, 'Media Support', 'media', 'plugin', '', '', 0, 0, 1, 1, 1, '', 0, '0000-00-00 00:00:00'),
(5, 'Help', 'help', 'plugin', 'help', 'help', 1, 1, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(6, 'New Document', 'newdocument', 'command', 'newdocument', 'newdocument', 1, 2, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(7, 'Bold', 'bold', 'command', 'bold', 'bold', 1, 3, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(8, 'Italic', 'italic', 'command', 'italic', 'italic', 1, 4, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(9, 'Underline', 'underline', 'command', 'underline', 'underline', 1, 5, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(10, 'Font Select', 'fontselect', 'command', 'fontselect', 'fontselect', 1, 6, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(11, 'Font Size Select', 'fontsizeselect', 'command', 'fontsizeselect', 'fontsizeselect', 1, 7, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(12, 'Style Select', 'styleselect', 'command', 'styleselect', 'styleselect', 1, 8, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(13, 'StrikeThrough', 'strikethrough', 'command', 'strikethrough', 'strikethrough', 1, 9, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(14, 'Justify Full', 'full', 'command', 'justifyfull', 'justifyfull', 1, 10, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(15, 'Justify Center', 'center', 'command', 'justifycenter', 'justifycenter', 1, 11, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(16, 'Justify Left', 'left', 'command', 'justifyleft', 'justifyleft', 1, 12, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(17, 'Justify Right', 'right', 'command', 'justifyright', 'justifyright', 1, 13, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(18, 'Format Select', 'formatselect', 'command', 'formatselect', 'formatselect', 1, 14, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(19, 'Paste', 'paste', 'plugin', 'cut,copy,paste', 'paste', 2, 1, 1, 1, 1, '', 0, '0000-00-00 00:00:00'),
(20, 'Search Replace', 'searchreplace', 'plugin', 'search,replace', 'searchreplace', 2, 2, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(21, 'Font ForeColour', 'forecolor', 'command', 'forecolor', 'forecolor', 2, 3, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(22, 'Font BackColour', 'backcolor', 'command', 'backcolor', 'backcolor', 2, 4, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(23, 'Unlink', 'unlink', 'command', 'unlink', 'unlink', 2, 5, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(24, 'Indent', 'indent', 'command', 'indent', 'indent', 2, 6, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(25, 'Outdent', 'outdent', 'command', 'outdent', 'outdent', 2, 7, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(26, 'Undo', 'undo', 'command', 'undo', 'undo', 2, 8, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(27, 'Redo', 'redo', 'command', 'redo', 'redo', 2, 9, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(28, 'HTML', 'html', 'command', 'code', 'code', 2, 10, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(29, 'Numbered List', 'numlist', 'command', 'numlist', 'numlist', 2, 11, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(30, 'Bullet List', 'bullist', 'command', 'bullist', 'bullist', 2, 12, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(31, 'Anchor', 'anchor', 'command', 'anchor', 'anchor', 2, 13, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(32, 'Image', 'image', 'command', 'image', 'image', 2, 14, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(33, 'Link', 'link', 'command', 'link', 'link', 2, 15, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(34, 'Code Cleanup', 'cleanup', 'command', 'cleanup', 'cleanup', 2, 16, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(35, 'Directionality', 'directionality', 'plugin', 'ltr,rtl', 'directionality', 3, 1, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(36, 'Emotions', 'emotions', 'plugin', 'emotions', 'emotions', 3, 2, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(37, 'Fullscreen', 'fullscreen', 'plugin', 'fullscreen', 'fullscreen', 3, 3, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(38, 'Preview', 'preview', 'plugin', 'preview', 'preview', 3, 4, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(39, 'Tables', 'table', 'plugin', 'tablecontrols', 'buttons', 3, 5, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(40, 'Print', 'print', 'plugin', 'print', 'print', 3, 6, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(41, 'Horizontal Rule', 'hr', 'command', 'hr', 'hr', 3, 7, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(42, 'Subscript', 'sub', 'command', 'sub', 'sub', 3, 8, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(43, 'Superscript', 'sup', 'command', 'sup', 'sup', 3, 9, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(44, 'Visual Aid', 'visualaid', 'command', 'visualaid', 'visualaid', 3, 10, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(45, 'Character Map', 'charmap', 'command', 'charmap', 'charmap', 3, 11, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(46, 'Remove Format', 'removeformat', 'command', 'removeformat', 'removeformat', 2, 1, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(47, 'Styles', 'style', 'plugin', 'styleprops', 'style', 4, 1, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(48, 'Non-Breaking', 'nonbreaking', 'plugin', 'nonbreaking', 'nonbreaking', 4, 2, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(49, 'Visual Characters', 'visualchars', 'plugin', 'visualchars', 'visualchars', 4, 3, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(50, 'XHTML Xtras', 'xhtmlxtras', 'plugin', 'cite,abbr,acronym,del,ins,attribs', 'xhtmlxtras', 4, 4, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(51, 'Image Manager', 'imgmanager', 'plugin', 'imgmanager', 'imgmanager', 4, 5, 1, 1, 1, '', 0, '0000-00-00 00:00:00'),
(52, 'Advanced Link', 'advlink', 'plugin', 'advlink', 'advlink', 4, 6, 1, 1, 1, '', 0, '0000-00-00 00:00:00'),
(53, 'Spell Checker', 'spellchecker', 'plugin', 'spellchecker', 'spellchecker', 4, 7, 1, 1, 1, '', 0, '0000-00-00 00:00:00'),
(54, 'Layers', 'layer', 'plugin', 'insertlayer,moveforward,movebackward,absolute', 'layer', 4, 8, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(55, 'Advanced Code Editor', 'advcode', 'plugin', 'advcode', 'advcode', 4, 9, 1, 0, 1, '', 0, '0000-00-00 00:00:00'),
(56, 'Article Breaks', 'article', 'plugin', 'readmore,pagebreak', 'article', 4, 10, 1, 0, 1, '', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `vea_jf_content`
--

DROP TABLE IF EXISTS `vea_jf_content`;
CREATE TABLE IF NOT EXISTS `vea_jf_content` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `language_id` int(11) NOT NULL default '0',
  `reference_id` int(11) NOT NULL default '0',
  `reference_table` varchar(100) NOT NULL default '',
  `reference_field` varchar(100) NOT NULL default '',
  `value` mediumtext NOT NULL,
  `original_value` varchar(255) default NULL,
  `original_text` mediumtext NOT NULL,
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL default '0',
  `published` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `combo` (`reference_id`,`reference_field`,`reference_table`),
  KEY `jfContent` (`language_id`,`reference_id`,`reference_table`),
  KEY `jfContentLanguage` (`reference_id`,`reference_field`,`reference_table`,`language_id`),
  KEY `reference_id` (`reference_id`),
  KEY `language_id` (`language_id`),
  KEY `reference_table` (`reference_table`),
  KEY `reference_field` (`reference_field`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=198 ;

--
-- Dumping data for table `vea_jf_content`
--

INSERT INTO `vea_jf_content` (`id`, `language_id`, `reference_id`, `reference_table`, `reference_field`, `value`, `original_value`, `original_text`, `modified`, `modified_by`, `published`) VALUES
(1, 1, 1, 'content', 'title', 'What is Vealawebcuba', '73ba2686526a67cb8f6965c721abecbf', '', '2011-03-11 16:25:22', 62, 1),
(2, 1, 1, 'content', 'alias', 'what-is-vealawebcuba', 'c20388c2b58f2ff403ba72ecaf84fc30', '', '2011-03-11 16:25:22', 62, 1),
(3, 1, 1, 'content', 'introtext', '<div id="annu">\r\n<div class="nU" id="anunnc1"><span class="titleAnunc">What is VealawebCuba? </span>Founded in January 2011, by a group of self-employed (freelance). We offer <a href="index.php?option=com_content&amp;view=article&amp;id=13&amp;Itemid=61">programming services</a>, <a href="index.php?option=com_content&amp;view=article&amp;id=35&amp;Itemid=65">Web design </a>and <a href="index.php?option=com_content&amp;view=article&amp;id=25&amp;Itemid=71">Web promotion</a>, with the desire to help&nbsp;clients to promote their services or products online. <br /><br />With experience of more than 10 years of website cration related to the design, development and online promotion, we hope to contribute with you to achieve this goal.<br /><br />Our goal: 99% satisfaction of our customers.<br /><br />Come and see the <a href="index.php?option=com_content&amp;view=article&amp;id=19&amp;Itemid=59">benefits</a> of using <a href="index.php?option=com_content&amp;view=article&amp;id=43&amp;Itemid=27">our services</a>.</div>\r\n<div class="nU" id="anunnc2"><span class="titleAnunc">Web Design and&nbsp;Web Programming</span> Web design and programming computerized study your needs, we define the structure of information and perform graphic design and data for these needs. With approval of this first stage, we began the program to achieve these objectives, using modern tools. <br /><br />We use our work PHP, MySQL, SQL Postgress, CMS Joomla, CMS WordPress and other tools.<br /><br />Find out more about <a href="index.php?option=com_content&amp;view=article&amp;id=35&amp;Itemid=65">Web Design</a> and <a href="index.php?option=com_content&amp;view=article&amp;id=13&amp;Itemid=61">Web Programming<span></span></a>.</div>\r\n<div class="nU" id="anunnc3"><span class="titleAnunc">Optimization and Search Engine Optimization (SEO)</span>Millions of Internet sites lose the opportunity to grow and to compete because they simply do not appear among the top when customers search for information on search engines.<br /><br /><strong>VealawebCuba </strong>has specialists who will ensure that your website is "friendly"search engines that can be fitted in the top thirty top twenty or top ten on major search engines, Google, Yahoo and MSN, Google priority. <br /><br />SEO is simply place your site above your competition, your business or company is present and visible (to be found) on the web.<br /><br />Find out more about <a href="index.php?option=com_content&amp;view=article&amp;id=25&amp;Itemid=71">Search Engine Optimization (SEO)</a>. <span></span><a></a>.</div>\r\n<div class="nU" id="anunnc4"><span class="titleAnunc">Offshore Service</span>You can hire VealawebCuba services for any distance projects.<br /><br />We provide designers, programmers, SEO specialists and the contract will be decided by you.<br /><br />More information about <span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=34%3Aprogramacionoffshore&amp;catid=32&amp;Itemid=27">Offshore Service</a></span><strong><a href="index.php?option=com_content&amp;view=article&amp;id=13&amp;Itemid=70">.</a></strong></div>\r\n</div>', 'e0663eb307fcba0663ebc783e57c796f', '', '2011-03-11 16:25:22', 62, 1),
(6, 1, 25, 'content', 'introtext', '<p><img height="198" width="272" src="images/stories/imagenposicionamiento.png" alt="imagenposicionamiento" class="imgFlotante" style="margin-left: 20px; margin-bottom: 5px; float: right;" /><br />Being on the web is not enough, you need to be and be seen!<strong> </strong><br /><br />Millions of websites lose the opportunity to grow, prosper and to compete because they simply do not appear among the first, remember that be the 1 in a race means be a winner.<br /><br />In <strong>VealawebCuba </strong>we optimize your website, which is&nbsp; prepare your site for certain search criteria defined by you or decided by us through analysis of the terms most relevant to your site in the web.<br /><br />We make adjustments to your website to make it "<strong>friendly</strong>" to search engines that can be fitted in the early thirties, first twenty or top ten on major search engines, Google, Yahoo and MSN, Google priority.<br /><br />This stage includes:<br />&nbsp;&nbsp; - Changes in page''s title.<br />&nbsp;&nbsp; - Inclusion of keywords.<br />&nbsp;&nbsp; - Possible changes in website contents to increase the density of those keywords.<br />&nbsp;&nbsp; - Search engines.<br />&nbsp;&nbsp; - Submission of the websites to web directories under the appropriate category.<br /><br />We can also make paid search engine campaigns, to ensure website positioning.<br /><br />Positioning is simply place your site in the competition. Make your business or enterprise visible on the web.</p>\r\n<hr class="system-pagebreak" alt="Página 2" title="Página 2" />\r\n<div><img height="198" width="272" src="images/stories/imagenposicionamiento.png" alt="imagenposicionamiento" class="imgFlotante" style="margin-left: 20px; margin-bottom: 5px; float: right;" /><br /><strong>VealawebCuba </strong>put on your website the key phrases to give you the opportunity to be among&nbsp; the firsts, a year with our team for the lowest prices and the success justify the choice for us<br /><br />Google, the most important search engine on the web, regularly performs a crawl of its indexes, which involves changes in the positioning of your site, for this purpose&nbsp; a proper search criteria is required.<br /><br /><strong>VealawebCuba </strong>sends a monthly report of your Popular Positioning and these links point to your site, the Page Rank of your home page or internal pages, all these technical details are useful to know the valuation that others have about your site. What your website means&nbsp; among competition is important..<br /><br />We guarantee that your company, business or project stand among the firsts in Google.com, after one or two months of Vealawebcuba''s work..<br /><br /><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Contact us</a> for this service.</div>', '600f210bf980687d8aeec48d9e6e604e', '', '2011-04-14 16:58:06', 62, 1),
(7, 1, 25, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-04-14 16:58:06', 62, 1),
(8, 1, 25, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-04-14 16:58:06', 62, 1),
(99, 1, 34, 'content', 'introtext', '<p><img style="margin-bottom: 5px; margin-left: 20px; float: right;" class="imgFlotante" alt="imagenoffshore" src="images/stories/imagenoffshore.png" width="272" height="198" /><br />We provide remote service to all who are planning the design and programming of websites as well as associated services. We offer a flat monthly fee for specialist, unless you want to hire point per hour. This applies to firms with long-term projects, and this flat fee gives you the ability to save monthly money. This is more profitable. You can hire designers and programmers with the agreement of monthly fee.<br /><br />We deliver high quality designers, animators and programmers within 15 days of receiving the service order. We can make available staff for:<br /><br />• <span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=35:diseno-web&amp;catid=31&amp;Itemid=65">Web Design</a></span> <br />• <span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=13:programacion-web&amp;catid=31&amp;Itemid=61">Programming</a></span><br />• <span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=25:posicionamientoweb&amp;catid=31&amp;Itemid=71">Optimization and SEO Services (SEO)</a></span><br /><br /><b>Offshore Recruitment Process</b></p>\r\n<p><br />• You can hire a personal distance for a minimum period of 2 months. Payments are made monthly in advance and, any other changes will be adjust for the next month.<br />• The staff will work exclusively for you for 8 hours per day, 5 days a week. Total hours of work per month is 160.</p>\r\n<p>• During the period of engagement, communication will be in cash<br /><br /><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Contact us </a></span>for rates for this service</p>', 'c956bd60916b13597d33a34cf75b30b0', '', '2011-04-14 16:55:54', 62, 1),
(9, 1, 1, 'menu', 'name', 'Home', 'bf89f6756d11a04ddc3bbac67a272020', '', '2011-03-07 21:19:46', 62, 1),
(10, 1, 1, 'menu', 'alias', 'home', '106a6c241b8797f52e1e77317b96a201', '', '2011-03-07 21:19:46', 62, 1),
(11, 1, 1, 'menu', 'params', 'page_title=Cuba Desarrollo Web, marketing online, creación de sitios web, diseño y programación, posicionamiento seo, soluciones informáticas, ve a la web Cuba\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=0\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', 'afa891054514334ecbe7c67c11d367c7', '', '2011-03-07 21:19:46', 62, 1),
(12, 1, 1, 'menu', 'link', 'index.php?option=com_content&view=article&id=1', 'dcef15d57ac1cffe1d547c1e97d4ab5a', '', '2011-03-07 21:19:46', 62, 1),
(13, 1, 72, 'menu', 'name', 'Map', '8bf8348d95a18192acc9d55564d9691f', '', '2011-03-07 21:20:07', 62, 1),
(14, 1, 72, 'menu', 'alias', 'map', '4437ad177c1679b1090c78835bb6c221', '', '2011-03-07 21:20:07', 62, 1),
(15, 1, 72, 'menu', 'params', 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', '0df681f43ece04ee5e49d142c7fd5ec7', '', '2011-03-07 21:20:07', 62, 1),
(16, 1, 72, 'menu', 'link', 'index.php?option=com_xmap&sitemap=1', '31b9b1a2a6d3706f28a0dae0bf64d2c1', '', '2011-03-07 21:20:07', 62, 1),
(17, 1, 77, 'menu', 'name', 'Portfolio', '4b4557ac036317d8e07a8db8d941cb67', '', '2011-03-07 21:20:40', 62, 1),
(18, 1, 77, 'menu', 'alias', 'portfolio', 'c60efcc311b447e3e5a908f1783c342c', '', '2011-03-07 21:20:40', 62, 1),
(19, 1, 77, 'menu', 'params', 'page_title=Diseño web, programación web y promoción web, Vealawebcuba, servicios informáticos de Cuba, su empresa visible en internet, marketing online\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', '9e5ba2dc66b25d7d32a8c6621bdc142d', '', '2011-03-07 21:20:40', 62, 1),
(20, 1, 77, 'menu', 'link', 'index.php?option=com_content&view=article&id=50', 'a210aed438473494a6d8238865ab8ebe', '', '2011-03-07 21:20:40', 62, 1),
(21, 1, 27, 'menu', 'name', 'Services', '0eccfe309366d289ed1ffab9930e7d9c', '', '2011-03-07 21:21:03', 62, 1),
(22, 1, 27, 'menu', 'alias', 'services', '5f82c4cc00aa13b4d16458481c75d39a', '', '2011-03-07 21:21:03', 62, 1),
(23, 1, 27, 'menu', 'params', 'page_title=Cuba servicios para diseño y promoción de empresas en internet, vea la web cuba, tiendas virtuales, diseño de logos, servicio offshores\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', 'ec1f5bc7d028fce43a0ab80809807267', '', '2011-03-07 21:21:03', 62, 1),
(24, 1, 27, 'menu', 'link', 'index.php?option=com_content&view=article&id=43', '9c8b174c345e1b8338301637501c71e0', '', '2011-03-07 21:21:03', 62, 1),
(25, 1, 64, 'menu', 'name', 'Virtual Stores', 'a2a48461c0fa1a6a062929bc4449df3d', '', '2011-03-07 21:21:48', 62, 1),
(26, 1, 64, 'menu', 'alias', 'virtualstores', 'a5ffd4aa3dcf3e4d989dad3346891501', '', '2011-03-07 21:21:48', 62, 1),
(27, 1, 64, 'menu', 'params', 'page_title=vealaweb, desarrollo web, tiendas virtuales, diseño y programación web, cuba comercio electrónico, cuba tiendas virtuales, servicios online, pasarelas de pago, cuba marketing online.\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', 'dfbf67125e13d6bea01fe21725988cbb', '', '2011-03-07 21:21:48', 62, 1),
(28, 1, 64, 'menu', 'link', 'index.php?option=com_content&view=article&id=15', '07e8cae66d7ed1bd8d666ace61a8f203', '', '2011-03-07 21:21:48', 62, 1),
(29, 1, 66, 'menu', 'name', 'Flash Design', 'aeaf40c6c1003fbf9b785a8219b87c2e', '', '2011-08-30 21:55:46', 62, 1),
(30, 1, 66, 'menu', 'alias', 'flashdesign', '3ce37357c974a2c2f74d37d6b6405fb0', '', '2011-08-30 21:55:46', 62, 1),
(31, 1, 66, 'menu', 'params', 'page_title=creación de sitios web, diseños flash, contenido y diseño web, flash, banner flash, desarrollo web en cuba\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', 'dc90bc9d8596624317457967b072f624', '', '2011-08-30 21:55:46', 62, 1),
(32, 1, 66, 'menu', 'link', 'index.php?option=com_content&view=article&id=17', 'c24bbc53efac18df9704bca2219ee7b0', '', '2011-08-30 21:55:46', 62, 1),
(33, 1, 58, 'menu', 'name', 'Contact Us', 'c0c549c74facb4a9636a805b57aa0a43', '', '2011-03-07 21:23:17', 62, 1),
(34, 1, 58, 'menu', 'alias', 'contactus', 'ee57144a14cb6026469384ad2702af7f', '', '2011-03-07 21:23:17', 62, 1),
(35, 1, 58, 'menu', 'params', 'show_contact_list=0\nshow_category_crumb=0\npage_title=Vealawebcuba contactos, sugerencias, informe sobre creación de sitios web en Cuba, datos, mensajes.\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\ncontact_icons=\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_headings=\nshow_position=\nshow_email=\nshow_telephone=\nshow_mobile=\nshow_fax=\nallow_vcard=\nbanned_email=\nbanned_subject=\nbanned_text=\nvalidate_session=\ncustom_reply=\n\n', 'e9276d0d2b4d1ba7791b415552bf4438', '', '2011-03-07 21:23:17', 62, 1),
(36, 1, 58, 'menu', 'link', 'index.php?option=com_contact&view=contact&id=1', '34c817cd03ae52412e73e4772c6d5dea', '', '2011-03-07 21:23:17', 62, 1),
(37, 1, 65, 'menu', 'name', 'Web Design', 'b8be4fb1da3072f0dd65a45daff3a375', '', '2011-03-07 21:23:44', 62, 1),
(38, 1, 65, 'menu', 'alias', 'webdesign', '5e0e5cc708c0d2419900f1c43d8fd843', '', '2011-03-07 21:23:44', 62, 1),
(39, 1, 65, 'menu', 'params', 'page_title=creación de sitios web, diseño web en cuba, contenido y diseño de sitios web, servicios online, desarrollo web cuba\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', '44c07a3667194605837b15205837ebca', '', '2011-03-07 21:23:44', 62, 1),
(40, 1, 65, 'menu', 'link', 'index.php?option=com_content&view=article&id=35', '0872b3957befad4b1d3a6dea71244b19', '', '2011-03-07 21:23:44', 62, 1),
(41, 1, 68, 'menu', 'name', 'Brochure Design', 'd15faed7adedc567e438424808cd94b7', '', '2011-03-07 21:24:17', 62, 1),
(42, 1, 68, 'menu', 'alias', 'brochuredesign', 'e9f08653d6e5d378140e589032c1169f', '', '2011-03-07 21:24:17', 62, 1),
(43, 1, 68, 'menu', 'params', 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', '9b19e486779638daf48fba52bf1e7263', '', '2011-03-07 21:24:17', 62, 1),
(44, 1, 68, 'menu', 'link', 'index.php?option=com_content&view=article&id=13', 'f8e328b9f9110c6fbe6831373b552291', '', '2011-03-07 21:24:17', 62, 1),
(45, 1, 24, 'menu', 'name', 'Close session', '0daed4d43928c4a72d4eabff46d766fe', '', '2011-03-07 21:24:50', 62, 1),
(46, 1, 24, 'menu', 'alias', 'clossession', '4236a440a662cc8253d7536e5aa17942', '', '2011-03-07 21:24:50', 62, 1),
(47, 1, 24, 'menu', 'params', 'show_login_title=1\nheader_login=\nlogin=\nlogin_message=0\ndescription_login=0\ndescription_login_text=\nimage_login=\nimage_login_align=right\nshow_logout_title=1\nheader_logout=\nlogout=\nlogout_message=1\ndescription_logout=1\ndescription_logout_text=\nimage_logout=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-03-07 21:24:50', 62, 1),
(48, 1, 24, 'menu', 'link', 'index.php?option=com_user&view=login', '423d97aec213f978bcf674ebbf276f7b', '', '2011-03-07 21:24:50', 62, 1),
(49, 1, 71, 'menu', 'name', 'Web Optimization', '9af663ec0dfec7bac79e2500da8e3024', '', '2011-03-07 21:26:32', 62, 1),
(50, 1, 71, 'menu', 'alias', 'weboptimization', '0c4054c7658308880f90effc05886012', '', '2011-03-07 21:26:32', 62, 1),
(51, 1, 71, 'menu', 'params', 'page_title=posicionamiento web, buscadores, Google, Yahoo, MSN, posicionamiento seo, palabras claves, keywords, alta en buscadores, primeros diez\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', '69e7dccc04b3b83c55fe43c53afa8ba3', '', '2011-03-07 21:26:32', 62, 1),
(52, 1, 71, 'menu', 'link', 'index.php?option=com_content&view=article&id=25', '6c4f1731abb455b01300f34f43ec3cce', '', '2011-03-07 21:26:32', 62, 1),
(53, 1, 54, 'menu', 'name', 'Prices', '41db38e118d590735911a377469dc659', '', '2011-09-01 12:58:59', 62, 1),
(54, 1, 54, 'menu', 'alias', 'prices', '404aed0c2f83509423acd015bfb83c63', '', '2011-09-01 12:58:59', 62, 1),
(55, 1, 54, 'menu', 'params', 'page_title=Precios para servicios web en Cuba, cotizaciones para creación de sitios web, tarifas, pasarela de pago, ppv, pago online servicios web\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', 'd7958a745d12724e2eea5a1764b1c622', '', '2011-09-01 12:58:59', 62, 1),
(56, 1, 54, 'menu', 'link', 'index.php?option=com_content&view=article&id=51', 'd87927464e8e89a4cb0ea73b21fb8ded', '', '2011-09-01 12:58:59', 62, 1),
(57, 1, 61, 'menu', 'name', 'Web Programming', '0784859bdcc10b5abf1252762eba43f1', '', '2011-03-07 21:27:20', 62, 1),
(58, 1, 61, 'menu', 'alias', 'webprogramming', '2659ad550383a46ace9063dac56b4e0c', '', '2011-03-07 21:27:20', 62, 1),
(59, 1, 61, 'menu', 'params', 'page_title=Programacion web, creacion de sitios web, desarrollo web en cuba, php, asp, asp.net, java\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', '1a7ab63080e9a6b63e253857e9bb410c', '', '2011-03-07 21:27:20', 62, 1),
(60, 1, 61, 'menu', 'link', 'index.php?option=com_content&view=article&id=13', 'f8e328b9f9110c6fbe6831373b552291', '', '2011-03-07 21:27:20', 62, 1),
(61, 1, 70, 'menu', 'name', 'Offshore Service', 'c0fbb7179a51bec8292189363222ac08', '', '2011-03-07 21:27:52', 62, 1),
(62, 1, 70, 'menu', 'alias', 'offshoreservice', 'b5760cb39e45a4d2e79842fdc817888b', '', '2011-03-07 21:27:52', 62, 1),
(63, 1, 70, 'menu', 'params', 'menu_image=-1\n\n', '3ef6831f9af5f28d2afd98718ec70745', '', '2011-03-07 21:27:52', 62, 1),
(64, 1, 56, 'menu', 'name', 'Offer Request', '72f58849baad92f9d1e1209baebf109e', '', '2011-03-07 21:28:30', 62, 1),
(65, 1, 56, 'menu', 'alias', 'offerrequest', 'ab4f71614bf5d1f129458f17e746509d', '', '2011-03-07 21:28:30', 62, 1),
(66, 1, 56, 'menu', 'params', 'show_contact_list=0\nshow_category_crumb=0\npage_title=Cuba ofertas web, promoción web de empresas cubanas, ofertas para cotizaciones, presupuestos de servicios web\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\ncontact_icons=2\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_headings=\nshow_position=\nshow_email=\nshow_telephone=\nshow_mobile=\nshow_fax=\nallow_vcard=\nbanned_email=\nbanned_subject=\nbanned_text=\nvalidate_session=\ncustom_reply=\n\n', '51b4f388d7052c70987103d1ccd7898e', '', '2011-03-07 21:28:30', 62, 1),
(67, 1, 56, 'menu', 'link', 'index.php?option=com_contact&view=contact&id=2', '9f235f8b2b7e7d7ddde393d7bc5fab23', '', '2011-03-07 21:28:30', 62, 1),
(68, 1, 76, 'menu', 'name', 'Terms and Conditions', 'c859a7f90ba7f8fb96f3aa762c82718d', '', '2011-03-07 21:29:11', 62, 1),
(69, 1, 76, 'menu', 'alias', 'termsconditions', 'c1d2dffe42c26bb85d070146154d6cd2', '', '2011-03-07 21:29:11', 62, 1),
(70, 1, 76, 'menu', 'params', 'page_title=\nshow_page_title=1\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\nshow_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\n\n', '64dff43628eafa0eb1bf53e4e932ba54', '', '2011-03-07 21:29:11', 62, 1),
(71, 1, 76, 'menu', 'link', 'index.php?option=com_content&view=article&id=49', '510bd6d104e9590fec174fa7696a30cc', '', '2011-03-07 21:29:11', 62, 1),
(72, 1, 57, 'menu', 'name', 'Any question?', '8bc74bde476ee5be91f506389117fafb', '', '2011-03-07 21:29:47', 62, 1),
(73, 1, 57, 'menu', 'alias', 'anyquestion', '208c99a20393a58baa935982a6b9271c', '', '2011-03-07 21:29:47', 62, 1),
(74, 1, 57, 'menu', 'params', 'page_title=Pagina web, contenido, diseño y presentación, dirección y alojamiento web, preguntas y respuestas empresas en la web de Cuba.\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', '2840039055cc47875fd86d69b06aeb6b', '', '2011-03-07 21:29:47', 62, 1),
(75, 1, 57, 'menu', 'link', 'index.php?option=com_content&view=article&id=32', '3f2400a8d0059f42681059799abf975c', '', '2011-03-07 21:29:47', 62, 1),
(76, 1, 74, 'menu', 'name', 'English translation', '8c3e5c50459f3260726a835e7b54e0bc', '', '2011-08-30 21:56:31', 62, 1),
(77, 1, 74, 'menu', 'alias', 'englishtranslation', '459fde9d960ee2155b0704d5eb994f6c', '', '2011-08-30 21:56:31', 62, 1),
(78, 1, 74, 'menu', 'params', 'page_title=\nshow_page_title=1\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', 'b213bf965e9e0aa7c403dac40aa6b466', '', '2011-08-30 21:56:31', 62, 1),
(79, 1, 74, 'menu', 'link', 'index.php?option=com_content&view=article&id=47', 'd4c0935c59e82bd4de5bb6e16195ff04', '', '2011-08-30 21:56:31', 62, 1),
(80, 1, 63, 'menu', 'name', 'WordPress', '9b8256d2132d3c55df8d564bce37ba3f', '', '2011-03-07 21:30:53', 62, 1),
(81, 1, 63, 'menu', 'alias', 'wordpress', '1870a829d9bc69abf500eca6f00241fe', '', '2011-03-07 21:30:53', 62, 1),
(82, 1, 63, 'menu', 'params', 'page_title=desarrollo web, cms word press, contenido web, php, diseño sitios web, diseño y contenido web, cms word press, desarrollo web cuba\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', '9d3b1943dc430806b15bd6e717b36ead', '', '2011-03-07 21:30:53', 62, 1),
(83, 1, 63, 'menu', 'link', 'index.php?option=com_content&view=article&id=28', '9c0b7d3a55adaced3a32efa0ec045bad', '', '2011-03-07 21:30:53', 62, 1),
(84, 1, 62, 'menu', 'name', 'Joomla', 'ef0d50c39c4c88cbef8dac1294549a60', '', '2011-03-07 21:31:13', 62, 1),
(85, 1, 62, 'menu', 'alias', 'joomla', '226776f356d7ecf58b60bab12a05d38f', '', '2011-03-07 21:31:13', 62, 1),
(86, 1, 62, 'menu', 'params', 'page_title=cms joomla, diseño web, directorios web, sitios web, comercio electrónico, hoteles, cadenas hoteleras, galerías de arte, cuba desarrollo web.\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', 'ac64b722931050bc43303cda83a42d9e', '', '2011-03-07 21:31:13', 62, 1),
(87, 1, 62, 'menu', 'link', 'index.php?option=com_content&view=article&id=33', '30405f9ef8a99825997307651ea5ee5d', '', '2011-03-07 21:31:13', 62, 1),
(88, 1, 55, 'menu', 'name', 'Packages', '7a66bb63a03fbbafa0c4582111144953', '', '2011-08-30 21:54:16', 62, 1),
(89, 1, 55, 'menu', 'alias', 'packages', '329dab76f073199794881140eba08d6e', '', '2011-08-30 21:54:16', 62, 1),
(90, 1, 55, 'menu', 'params', 'page_title=Cuba empresas en internet, estar en la web, negocios en la web, empresas de Cuba, diseño, actualización y posicionamiento.\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', 'c1bf866620d0750a40b4c034e3236266', '', '2011-08-30 21:54:16', 62, 1),
(91, 1, 55, 'menu', 'link', 'index.php?option=com_content&view=article&id=12', '19dbdc122956b81f53d06f365bc792e5', '', '2011-08-30 21:54:16', 62, 1),
(92, 1, 75, 'menu', 'name', 'Politics of Privacy', '584ec4e9a127ae3374d4ce27cc9b35b0', '', '2011-03-07 21:33:42', 62, 1),
(93, 1, 75, 'menu', 'alias', 'politics-of-privacy', '4ca88cb44d3509c490e10e5291f19aee', '', '2011-03-07 21:33:42', 62, 1),
(94, 1, 75, 'menu', 'params', 'page_title=\nshow_page_title=1\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\nshow_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\n\n', '64dff43628eafa0eb1bf53e4e932ba54', '', '2011-03-07 21:33:42', 62, 1),
(95, 1, 75, 'menu', 'link', 'index.php?option=com_content&view=article&id=48', 'd3a9f1e19bf4e0f17cc0fa163fa6b048', '', '2011-03-07 21:33:42', 62, 1),
(96, 1, 13, 'content', 'introtext', '<div><img height="198" width="272" src="images/stories/imagenprogramacion.png" alt="imagenprogramacion" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" />It''s common to use ''scripts'' and web applications for the computerization of processes, making it more interactive, professional looking and easier to maintain...<br /><br /><strong>VealawebCuba </strong>provides programming services using modern tools, adapting to the specific requirements of our customers. No matter how small or large your project is, from static or information provider website to&nbsp; large e-commerce projects. Vealaweb Cuba, can do it for you.<br /><br />In <strong>VealawebCuba </strong>have years of experience and knowledge in PHP, ASP, ASP.NET, JAVA... satisfying&nbsp; any customer requirements. Our applications are integrate by designs tailored by customers, ensuring 100% satisfaction..<br /><br />We are committed to quality and professionalism since the first consultation with client to the final project.&nbsp; Every project''s programming tool is previously selected&nbsp; with clients. We encourage our customers to use free tools.<br /><br />You and your enterprise are our goal so let us know&nbsp; your needs and our specialists will contact you to coordinate and start the work.<br /><br /><strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Solicite nuestros servicios aquí</a></span></strong>.<br /><br /></div>', '3f6bca52f8c806a1272261d763aed29b', '', '2011-04-14 16:55:11', 62, 1),
(97, 1, 13, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-04-14 16:55:11', 62, 1),
(98, 1, 13, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-04-14 16:55:11', 62, 1),
(100, 1, 34, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-04-14 16:55:54', 62, 1),
(101, 1, 34, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-04-14 16:55:54', 62, 1),
(4, 1, 1, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-03-11 16:25:22', 62, 1),
(5, 1, 1, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=0\nlink_titles=\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_vote=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nlanguage=\nkeyref=\nreadmore=\n\n', '23fcd08e6254196138ce44b5ddc810b0', '', '2011-03-11 16:25:22', 62, 1),
(102, 1, 12, 'content', 'title', 'Packages', '7a66bb63a03fbbafa0c4582111144953', '', '2011-09-23 23:47:00', 62, 1),
(103, 1, 12, 'content', 'alias', 'packages', '329dab76f073199794881140eba08d6e', '', '2011-09-23 23:47:00', 62, 1),
(104, 1, 12, 'content', 'introtext', '<div>VealawebCuba packages, valid only for promotional sites<br /><br />#1- I want to be on the Web<br />Constitutes  a common design to select from a list of predefined designs, which is  added the content and images to the customer about your business.<br /> Once the site is published online as a VealawebCuba subdomain with the name that the client chooses. <br />Example: <a target="_blank" href="http://www.buscaencuba.com/my-paladar">http://www.buscaencuba.com/my-paladar</a><br /><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56"><span style="text-decoration: underline;">Request package</span><br /></a><br />#2- I am present with my website<br />What  constitutes a common design to select from a list of predefined designs,  which is added the content and images to the client''s business. <br />Sets with self-control that the customer chooses and accommodation (hosting) itself.<br /> Example: <a target="_blank" href="http://my-paladar.com/">http://my-paladar.com</a><br /><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56"><span style="text-decoration: underline;">Request package</span><br /></a><br />#3- Among the first of the Web<br />Custom  design with content and images to the client''s business. It brings with  it its own domain and hosting customer chooses (hosting) itself. <br /> Service includes site optimization and positioning for a year.<br /><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Request package<br /></a></span><br />#4- My Web updated<br />Custom  design with content and images to the client''s business. It brings with  it its own domain and hosting customer chooses (hosting) itself. <br /> Service includes site optimization and positioning for a year. It  provides the service of updating the content of the site for one year.<br /><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Request package</a></span></div>', '54ce16aaab8b74f65212b80248192d20', '', '2011-09-23 23:47:00', 62, 1),
(105, 1, 12, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-09-23 23:47:00', 62, 1),
(106, 1, 12, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-09-23 23:47:00', 62, 1),
(107, 1, 13, 'content', 'title', 'Web programming', 'b86ac48cf490973de45df66d5c6ba594', '', '2011-04-14 16:55:11', 62, 1),
(108, 1, 13, 'content', 'alias', 'web-programming', '2659ad550383a46ace9063dac56b4e0c', '', '2011-04-14 16:55:11', 62, 1),
(109, 1, 34, 'content', 'title', 'Offshore programming', 'b63391b849ec5d4c8336ccdb5df0a2be', '', '2011-04-14 16:55:54', 62, 1),
(110, 1, 34, 'content', 'alias', 'offshore-programming', '6c4b8efa2d8cc6451f4722c60720d018', '', '2011-04-14 16:55:54', 62, 1),
(111, 1, 25, 'content', 'title', 'Web optimization', '2c08d9f112882f161fea215a5497efd4', '', '2011-04-14 16:58:06', 62, 1),
(112, 1, 25, 'content', 'alias', 'web-posiotioning', '9ba1facb371326e1f6ad4202ba68940b', '', '2011-04-14 16:58:06', 62, 1),
(113, 1, 43, 'content', 'title', 'Services', '0eccfe309366d289ed1ffab9930e7d9c', '', '2011-08-30 22:20:38', 62, 1),
(114, 1, 43, 'content', 'alias', 'services', '5f82c4cc00aa13b4d16458481c75d39a', '', '2011-08-30 22:20:38', 62, 1),
(115, 1, 43, 'content', 'introtext', '<p>In <strong>VealawebCuba</strong>, with the aim of helping to appear correctly on the web, we have defined a set of services needed to achieve this goal. These can be hired full or part depending on their needs or the needs of your project on the web. <br /><br />Here is a brief explanation of each of them and if you want more information of one of them, visit VealawebCuba, the page that details each of them, by clicking on the link that appears in each of the names of services are below.<br /><strong><br />Services offered by VealawebCuba:</strong><br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=35:diseno-web&amp;catid=31&amp;Itemid=65">Web Design</a>:</strong> Survey of needs, definition of information architecture, motion graphic web site.<br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=13:programacion-web&amp;catid=28&amp;Itemid=61">Web Programming</a>:</strong> Once you have created the web site graphic design, we programming, using the tools or methods that best suit the objective to achieve.<br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=33:joomla&amp;catid=31&amp;Itemid=62">CMS Joomla</a>: </strong>Generally used in sites with high information volume, you can create web sites with information management for the client remotely, using internet, without knowing the programming web site. Joomla is one of the 3 CMS (Content Management System) software in the world. <br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=28:wordpress&amp;catid=31&amp;Itemid=63">CMS WordPress</a>:</strong> Generally used in areas with a high volume of information, you can create web sites with information management for the client remotely, using internet, without knowing the programming web site. Joomla is one of the 3 CMS (Content Management System) software in the world.</p>\r\n<p><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55">Packages</a>:</strong> Designed for customers who have low income, we provide a service type "demo", but also lets you no longer be present on the web with professionalism. Includes all necessary services at the publication''s website..</p>\r\n<p> </p>\r\n<hr class="system-pagebreak" alt="Página 2" title="Página 2" />\r\n<p><strong><a href="index.php?option=com_content&amp;view=article&amp;id=15:tiendasvirtuales&amp;catid=31&amp;Itemid=64">Virtual Stores</a>:</strong> For customers who have the possibility of Internet services that may be acquired or at least reserved for customers using virtual shopping carts. We can arrange the collection online if business conditions permit.</p>\r\n<p><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=25:posicionamientoweb&amp;catid=31&amp;Itemid=71">SEO</a>:</strong> Preparation of the site for Search Engine Optimization (Google, Yahoo, MSN).<br />&nbsp;<br /><a href="index.php?option=com_content&amp;view=article&amp;id=17&amp;Itemid=66">Flash Design</a>: If you already have new site and want to place promotional ads, we make the designs of the ads according to site requirements.&nbsp;<br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=14:disenologo&amp;catid=31&amp;Itemid=67">Logo Design</a>:</strong> We offer this service for those who need to create an identification of the business<br /><br /><strong><a href="index.php?option=com_content&amp;view=article&amp;id=34:programacionoffshore&amp;catid=32">Offshore Services</a>:</strong> If you have no way to update your website, you can put our hands on this, we do everything for you, simply send text, pictures, news or any information you want change, do it for you.<br /><br /><a href="index.php?option=com_content&amp;view=article&amp;id=46:webmaster&amp;catid=31&amp;Itemid=73">Webmaster:</a> You can put in our hands the actualziación of your website, the webmaster of vealawebcuba will cover for you to do<br /><br /><a href="index.php?option=com_content&amp;view=article&amp;id=47:traduccioningles&amp;catid=31&amp;Itemid=74">English translation:</a> We provide translation services into English on its website.</p>\r\n<div class="mcePaste" id="_mcePaste" style="position: absolute; width: 1px; height: 1px; overflow: hidden; top: 0px; left: -10000px;">﻿</div>', 'c5a28d2d8e1c60bb82b82d3ae9bc34a8', '', '2011-08-30 22:20:38', 62, 1),
(172, 1, 38, 'content', 'title', 'Prices', '41db38e118d590735911a377469dc659', '', '2011-09-01 12:51:59', 62, 1),
(116, 1, 43, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-08-30 22:20:38', 62, 1),
(117, 1, 43, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-08-30 22:20:38', 62, 1),
(118, 1, 47, 'content', 'title', 'English translation', '8c3e5c50459f3260726a835e7b54e0bc', '', '2011-04-14 17:05:56', 62, 1),
(119, 1, 47, 'content', 'alias', 'english-translation', '459fde9d960ee2155b0704d5eb994f6c', '', '2011-04-14 17:05:56', 62, 1),
(120, 1, 47, 'content', 'introtext', '<div><br />We can offer translation services into English, for the content of your website. <br /><br /><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Contact us</a> for this service<br /><strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56"></a></span></strong></div>\r\n<div class="mcePaste" id="_mcePaste" style="position: absolute; width: 1px; height: 1px; overflow: hidden; top: 0px; left: -10000px;">﻿</div>', '7bbc3717862fe3bf4811422b1c44bd43', '', '2011-04-14 17:05:56', 62, 1),
(121, 1, 47, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-04-14 17:05:56', 62, 1),
(122, 1, 47, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-04-14 17:05:56', 62, 1),
(123, 1, 46, 'content', 'title', 'Webmaster', '4b220f866824d97c12e2eb9b44e3b5e6', '', '2011-04-14 17:07:08', 62, 1),
(124, 1, 46, 'content', 'alias', 'webmaster', '50a9c7dbf0fa09e8969978317dca12e8', '', '2011-04-14 17:07:08', 62, 1),
(125, 1, 46, 'content', 'introtext', '<div><br />VealawebCuba offers to keep your website up to date for you.<br /><br />To provide this service, it is necessary that you send us by any way (email or in person) texts, images, or any change of information you want to perform on your site. The format of the texts that you send us must be in. doc,. txt or other format that can be copied. NOT accept the texts, in formats such as PDF..<br /><br />The required changes should not involve programming changes, only the contents of the site. The new development work will be quoted separately.<br /><br />You can use email as a way to send the data to change. Write to <span style="text-decoration: underline;"><a href="mailto:webmaster@vealawebcuba.com">webmaster@vealawebcuba.com</a></span><span style="text-decoration: underline;"><a href="mailto:webmaster@vealawebcuba.com"></a></span><br /><br />In a maximum of 48 hours from receiving the update report submitted by you, your site will be duly updated. You will be notified by email of the work completed.<br /><br />If you want your site to appear in English, we can provide the service of translating the contents.<br /><br />To request the service of Webmaster, <span style="text-decoration: underline;"><strong><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">click here</a></strong></span></div>\r\n<div class="mcePaste" id="_mcePaste" style="position: absolute; width: 1px; height: 1px; overflow: hidden; top: 0px; left: -10000px;">﻿</div>', 'a2f7c572188d110482f6db32a100a575', '', '2011-04-14 17:07:08', 62, 1),
(126, 1, 46, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-04-14 17:07:08', 62, 1),
(127, 1, 46, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-04-14 17:07:08', 62, 1),
(128, 1, 35, 'content', 'title', 'Web design', 'e2b3cf024618d7b4770dc2b84f14fd96', '', '2011-04-14 17:10:03', 62, 1),
(129, 1, 35, 'content', 'alias', 'web-design', '5e0e5cc708c0d2419900f1c43d8fd843', '', '2011-04-14 17:10:03', 62, 1),
(130, 1, 35, 'content', 'introtext', '<div><img height="198" width="272" src="images/stories/imagendiseno.png" alt="imagendiseno" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" /><br />Your Web site should not only inform, but should also be pleasing to the eye. A good website is one that has the ability to attract and retain visitors and this is one of the most critical to the success of your business. . <br /><br />Therefore, if you want to build a new website or redesigning an existing one, we can help. VealawebCuba provides web design that meets your interests, professionally and according to their resources. <br /><br />Years of experience in the business make it possible for you to feel satisfied with the design proposals we propose to create your Web site, which meet all customer requirements beyond their expectations and modern trends of Web design. <br /><br />You only have to describe your need; we will make an appropriate budget for your wishes.<br /><br /><strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56"><span color="#506078" style="color: #506078;">Click here</span></a></span></strong>, to request our services.</div>\r\n<div class="mcePaste" id="_mcePaste" style="position: absolute; width: 1px; height: 1px; overflow: hidden; top: 0px; left: -10000px;">﻿</div>', 'bfb256a4444182150d8ab2362167467d', '', '2011-04-14 17:10:03', 62, 1),
(131, 1, 35, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-04-14 17:10:03', 62, 1),
(132, 1, 35, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-04-14 17:10:03', 62, 1),
(133, 1, 32, 'content', 'title', 'Any  Question?', '32d63eb31488c01f340939ed84613821', '', '2011-08-30 22:56:50', 62, 1),
(134, 1, 32, 'content', 'alias', 'any-question', '2cdad5542c571e6c6db9d43f2208f503', '', '2011-08-30 22:56:50', 62, 1);
INSERT INTO `vea_jf_content` (`id`, `language_id`, `reference_id`, `reference_table`, `reference_field`, `value`, `original_value`, `original_text`, `modified`, `modified_by`, `published`) VALUES
(135, 1, 32, 'content', 'introtext', '<div>For answers, click on the question. A window with the answer of the question selected. Click above for the answer, to close and return to the list of questions. <br />If that you want to ask is not on the list, contact us and send us all your questions. This section will be feeding on the very issues that you might wish for more. To write, select the "<a href="index.php?option=com_contact&amp;view=contact&amp;id=1&amp;Itemid=58">Contact</a>" from the top menu.</div>\r\n<div><strong><a href="#" class="pregT"><br />What do I need to have a website?<span class="respT">\r\n<p>To have a website running on the Internet, and be visited by potential clients, you need 5 essential elements (content, website design, hosting and domain names)::</p>\r\n<p><strong>1. Content</strong></p>\r\n<p>Your website needs content such as text and images. With this content, you''re going to present you or your business (your products and services). This is why it is very important to have a very well-crafted content, easy to understand and summarized. Remember that Internet users arrive at your site looking for information relevant to them, but do not have time, and depending on the information they find, depends if they are interested in inquiring further, their products or to request their services.</p>\r\n<p><strong>2. Website</strong></p>\r\n<p>Once you know what is going to publish on its new website, we organize it in several websites (VealawebCuba will help). Web pages form the content of the website. The website covers various topics (about the company, services, products, contact forms, etc.), and its contents are placed in different pages.</p>\r\n<p><strong>3. Design and Presentation</strong></p>\r\n<p>The next element is the design. For the website is visually appealing to visitors, are assembled content (images and text) with a graphic design. The website is designed with different colors, fonts, images and shapes. All these elements are part of the web site presentation..</p>\r\n<p><strong>4. Web address</strong></p>\r\n<p>A web site is identified by a web address (also known as web domain or URL). Is a unique identifier that differentiates it from other websites. With this identifier, we can find the website and access their content. The identifier of a web site has a name and extension. For example: www.my-casaparticular.com</p>\r\n<p><strong>5. Web Hosting</strong></p>\r\n<p>It''s where the site is placed so it can be located and accessed by visitors. The hosting of a website is a computer called a Web server. This computer is connected to the global network and allows any visitor to the world comes to your site through the Internet.</p>\r\n</span></a></strong><br /><a href="#" class="pregT">What are the benefits and advantages of having a web site? <span class="respT">There are many reasons why you should have a website for your company or business..&nbsp; Today the Internet is a tool used worldwide and it is important that you use this communication tool to promote your business to the fullest. For example, in Latin America alone there are over 400 million Internet users<br /><br />With a website you can access the following benefits :<br /><br />- Advertising 24 hours a day, 7 days a week, 365 days a year.<br />- The information about your business is available to any visitor.<br />- Lower advertising costs in relation to traditional media<br />- Access to millions of potential customers anywhere in the world.<br />- Personal attention to customers<br />- Image and prestige.<br />- Growth in its customer base and sales<br />…and many others</span></a></div>\r\n<div class="mcePaste" id="_mcePaste" style="position: absolute; width: 1px; height: 1px; overflow: hidden; top: 0px; left: -10000px;">﻿</div>', '3ee7c2a5951c31cb6ef7c2d880408073', '', '2011-08-30 22:56:50', 62, 1),
(136, 1, 32, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-08-30 22:56:50', 62, 1),
(137, 1, 32, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-08-30 22:56:50', 62, 1),
(138, 1, 19, 'content', 'title', 'VealawebCuba', '76ce781068aeee4bf79faec014b8060d', '', '2011-04-14 17:16:16', 62, 1),
(139, 1, 19, 'content', 'alias', 'vealawebcuba', 'efdfee36e2593e4092a9fea2317bd4ab', '', '2011-04-14 17:16:16', 62, 1),
(140, 1, 19, 'content', 'introtext', '<p><br /><img src="images/stories/imagengeneral.png" alt="imagengeneral" class="imgFlotante" style="margin-left: 20px; margin-bottom: 5px; float: right;" width="272" height="198" /><strong>About us</strong><br /><br />With an experience of over 10 years of activity related to web design, web development and web promotion, we have joined a group of self-employed, we hope to help promote your business online or simply solve your computer problems..</p>\r\n<p><br /><strong>Founders</strong></p>\r\n<table align="left" border="0">\r\n<tbody>\r\n<tr>\r\n<td style="width: 140px;"><img src="images/stories/fotoCamilo.jpg" alt="fotoCamilo" style="margin: 5px;" width="108" height="101" /></td>\r\n<td style="width: 140px;"><img src="images/stories/fotoJulio.jpg" alt="fotoJulio" style="margin: 5px;" width="108" height="101" /></td>\r\n</tr>\r\n<tr>\r\n<td align="center"><span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=44:camilosanchez&amp;catid=31">Camilo Sánchez</a></span><br /></td>\r\n<td align="center"><span style="text-decoration: underline;"><a href="index.php?option=com_content&amp;view=article&amp;id=45:juliotoirac&amp;catid=31">Julio Toirac</a></span><br /></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p><br /><br /><br /><strong>&nbsp;</strong></p>\r\n<p><strong><br /></strong></p>\r\n<p><strong><br /></strong></p>\r\n<p><strong><br /><br /></strong></p>\r\n<p><strong><br /></strong></p>\r\n<p><strong><br /></strong></p>\r\n<p><strong>What we do</strong><br /><br />Our strength is the know-how on the Internet, along with his experience in his activity, we guide it known to the rest of the Cubans and the world, using Internet as a means of communication. In this process we can: to design websites, program, manage the acquisition of identification on the Internet (domain name, ie <a href="http://www.my-negocio.com">www.my-negocio.com</a>) and the hosting servers, optimize and position your site in search engines (Google, Yahoo, MSN) among other professional services.</p>\r\n<hr class="system-pagebreak" alt="Página 2" title="Página 2" />\r\n<p><img src="images/stories/imagenposicionamiento.png" alt="imagenposicionamiento" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" width="272" height="198" /><br /><strong>How you benefit</strong><br /><br />Expand your business to all parts of the world, win customers, increase your sales potential, announces its services and expertise. With our experience and together VealawebCuba can accomplish this.<br /><br /><strong>Our strength</strong><br /><br />Our experience. This allows us to help you quickly put on the Web, just as you would want and need, to obtain the expected benefits..&nbsp; <br /><br /><strong>Our values</strong><br /><br />Know-how + fast + quality + + wishes to help be able to interpret from the standpoint web your wishes; we can offer a service that meets your needs. You&nbsp; found an ideal partner..</p>\r\n<div class="mcePaste" id="_mcePaste" style="position: absolute; width: 1px; height: 1px; overflow: hidden; top: 0px; left: -10000px;">﻿</div>', '7d02c5f09e784192cc5d9d8ea5d5d028', '', '2011-04-14 17:16:16', 62, 1),
(141, 1, 19, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-04-14 17:16:16', 62, 1),
(142, 1, 19, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-04-14 17:16:16', 62, 1),
(143, 1, 17, 'content', 'title', 'Flash design', '95cf0d3d7615841752f67f224649e493', '', '2011-04-14 17:17:16', 62, 1),
(144, 1, 17, 'content', 'alias', 'flash-design', '482a3b0bf4e05771a316b629cf6ca163', '', '2011-04-14 17:17:16', 62, 1),
(145, 1, 17, 'content', 'introtext', '<p><img height="198" width="272" src="images/stories/imagendisenoflash.png" alt="imagendisenoflash" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" />Flash technology is popular on the Web. Its popularity is based on its attractive graphic design..</p>\r\n<p><br />Although not recommended for a site that their main customers reside in Cuba, mainly due to the speed of navigation and Flash sites are very slow, if it is attractive to use in a head section of the website or by its beauty..</p>\r\n<p><br />Integration of a Flash intro or Flash banner on a web page, you can call the look of Internet users to your website and can increase the chance of getting a regular flow of traffic..&nbsp;</p>\r\n<div><br />VealawebCuba offers the creation of a Flash design, with experienced designers.</div>\r\n<div><br /><strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">Click here</a></span></strong>,&nbsp;to request our services.</div>\r\n<div class="mcePaste" id="_mcePaste" style="position: absolute; width: 1px; height: 1px; overflow: hidden; top: 0px; left: -10000px;">﻿</div>', '58e5fba7a3d4a0877551d2a1cf67b615', '', '2011-04-14 17:17:16', 62, 1),
(146, 1, 17, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-04-14 17:17:16', 62, 1),
(147, 1, 17, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-04-14 17:17:16', 62, 1),
(148, 1, 15, 'content', 'title', 'Online stores', '5267840b05142c0c26d23d436b3902c7', '', '2011-04-14 17:18:26', 62, 1),
(149, 1, 15, 'content', 'alias', 'online-stores', '4f47aaddbe3da0d17a0c973028e65954', '', '2011-04-14 17:18:26', 62, 1),
(150, 1, 15, 'content', 'introtext', '<div><img height="198" width="272" src="images/stories/imagentiendavirtual.png" alt="imagentiendavirtual" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" /><strong>VealawebCuba </strong>help you not only to promote your services online, but we also specialize in creating "virtual store.". <br /><br />Most customers today are looking not only the information but also looking for how to pay for the services received by the website. <br /><br />With <strong>VealawebCuba </strong>you can solve the online payment from your customers if you can sell your services or products online, such as houses for rent, sale of fine arts or those who need these services.<br /><br />Therefore, it is necessary to develop shopping carts or baskets on their website. <br /><br /><strong>VealawebCuba </strong>has the experience and talent to bring your website to this level of service; we can prepare your website and also manage the integration of online payment systems.<br /><br />To know more about these services, just <strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">contact us</a></span></strong>.</div>\r\n<div class="mcePaste" id="_mcePaste" style="position: absolute; width: 1px; height: 1px; overflow: hidden; top: 0px; left: -10000px;">﻿</div>', '12f37c1977e1a687d6058d77b871f020', '', '2011-04-14 17:18:26', 62, 1),
(151, 1, 15, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-04-14 17:18:26', 62, 1),
(152, 1, 15, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-04-14 17:18:26', 62, 1),
(153, 1, 14, 'content', 'title', 'Logo design', '689212c55725a827bfb9b211f12c8db6', '', '2011-04-14 17:19:40', 62, 1),
(154, 1, 14, 'content', 'alias', 'logo-design', 'a45b71ab24b2465d37c3a81a9b9c1fe0', '', '2011-04-14 17:19:40', 62, 1),
(155, 1, 14, 'content', 'introtext', '<div>﻿<img src="images/stories/imagendisenologos.png" alt="imagendisenologos" class="imgFlotante" style="margin-bottom: 5px; margin-left: 20px; float: right;" width="272" height="198" />A logo gives your company a unique identity is very attractive and inviting to your audience. Once positioned the logo, it becomes a great conqueror of the mind of your customers. A well done logo, becomes the company''s main brand and its various services.<br /><br />However, a professionally designed logo not only represents the products or services you deal with it, is also a symbolic representation of your company''s vision and values. It is useful for its signs, letterheads, business cards, website, and more. Therefore, to have a successful business, you need to have a successful logo.<br /><br />In today''s world, web applications has become a fast and effective way to introduce your logo in the world and become increasingly popular.<br /><br />The digital age allows these logos can be of a static or animated flash or gift.<br /><br /><strong>VealawebCuba</strong> helps you create the graphic identity and design and price of these varies depending on the ambition you want to reach their customers..﻿ <br /><br /><strong>Estimated price range:</strong> 100.00 - 150.00 CUC<br /><br />Click <strong><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=2&amp;Itemid=56">here</a></span></strong>, to request our services.</div>', '83e2e494859d7538c642eb282aaecf52', '', '2011-04-14 17:19:40', 62, 1),
(156, 1, 14, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-04-14 17:19:40', 62, 1),
(157, 1, 14, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-04-14 17:19:40', 62, 1),
(158, 1, 50, 'content', 'title', 'Portfolio', '4b4557ac036317d8e07a8db8d941cb67', '', '2011-06-27 17:06:28', 62, 1),
(159, 1, 50, 'content', 'alias', 'portfolio', 'c60efcc311b447e3e5a908f1783c342c', '', '2011-06-27 17:06:28', 62, 1),
(160, 1, 50, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-06-27 17:06:28', 62, 1),
(161, 1, 50, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-06-27 17:06:28', 62, 1),
(162, 1, 67, 'menu', 'name', 'Logo design', 'db55abcc9b3413e5b855a7437e034b4e', '', '2011-08-30 21:55:31', 62, 1),
(163, 1, 67, 'menu', 'alias', 'logo-design', '9629b1110d15a9288f2b7d42db1e85ab', '', '2011-08-30 21:55:31', 62, 1),
(164, 1, 67, 'menu', 'params', 'page_title=creación de sitios web, diseño de logos, identidad corporativa, diseños de empresas, flash, gift animados, vealawebcuba, empresas online\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\n\n', '7fc81419397cb6a62dfe0b9ca6f5f171', '', '2011-08-30 21:55:31', 62, 1),
(165, 1, 67, 'menu', 'link', 'index.php?option=com_content&view=article&id=14', '5b3dbcbece275fe2da1dc601756fc32b', '', '2011-08-30 21:55:31', 62, 1),
(166, 1, 44, 'modules', 'content', '<p><img alt="paginas" src="images/stories/paginas.png" height="78" width="138" /> <br /><br /> Web Desing,<br /> Web Programming,<br /> Offshore Services,<br /> Web Optimization<br /> &nbsp;&nbsp;&nbsp;&nbsp; <a href="index.php?option=com_content&amp;view=article&amp;id=43&amp;Itemid=27">See more</a></p>', '8ff75c5e600604410948599a81a0d6df', '', '2011-08-30 22:02:10', 62, 1),
(167, 1, 44, 'modules', 'params', 'moduleclass_sfx=_anuncio\n\n', '929cdde440edc91e7222cf0aef6ff9f2', '', '2011-08-30 22:02:10', 62, 1),
(168, 1, 27, 'modules', 'title', 'search', '71a45328063be6e4873e774db560a134', '', '2011-09-01 12:33:21', 62, 1),
(169, 1, 27, 'modules', 'params', 'moduleclass_sfx=_Buscar\nwidth=20\ntext=\nbutton=1\nbutton_pos=right\nimagebutton=\nbutton_text=Search\nset_itemid=\ncache=1\ncache_time=900\n\n', '405b923a4f0db44b3e9e39d91cf186c9', '', '2011-09-01 12:33:21', 62, 1),
(170, 1, 45, 'modules', 'title', 'Join to our VealawebCuba team', '6ff184bbad87bf1dab89e8450192e652', '', '2011-09-26 18:57:26', 62, 1),
(171, 1, 45, 'modules', 'params', 'dummy1=0\nemail_recipient=contacto@vealawebcuba.com\nfrom_name=VealaWeb\nfrom_email=contacto@velawebcuba.com\ndummy2=0\nemail_label=Your email:\nsubject_label=Subject:\nmessage_label=Your idea:\nbutton_text=SEND\npage_text=Gracias por su sugerencia\nerror_text=Su mensaje no puede ser enviado. Inténtelo nuevamente\nno_email=Escriba su correo por favor\ninvalid_email=Escriba una dirección válida\npre_text=\ndummy3=0\nthank_text_color=#FF0000\nerror_text_color=#FF0000\nemail_width=15\nsubject_width=15\nmessage_width=12\nbutton_width=100\ndummy4=0\nexact_url=1\ndisable_https=0\nfixed_url=0\nfixed_url_address=\ndummy5=0\nenable_anti_spam=0\nanti_spam_q=How many eyes has a typical person?\nanti_spam_a=2\nanti_spam_position=0\ndummy8=0\nmoduleclass_sfx=_correoIni\n\n', '7ae276af72fc71ee5b7dc0b293ab4b2a', '', '2011-09-26 18:57:26', 62, 1),
(173, 1, 38, 'content', 'alias', 'prices', '404aed0c2f83509423acd015bfb83c63', '', '2011-09-01 12:51:59', 62, 1),
(174, 1, 38, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-09-01 12:51:59', 62, 1),
(175, 1, 38, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-09-01 12:51:59', 62, 1),
(176, 1, 48, 'content', 'title', 'Politics of privacy', '584ec4e9a127ae3374d4ce27cc9b35b0', '', '2011-08-30 22:50:56', 62, 1),
(177, 1, 48, 'content', 'alias', 'politics-of-privacy', '4ca88cb44d3509c490e10e5291f19aee', '', '2011-08-30 22:50:56', 62, 1),
(178, 1, 48, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-08-30 22:50:56', 62, 1),
(179, 1, 48, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-08-30 22:50:56', 62, 1),
(180, 1, 49, 'content', 'title', 'Terms and conditiones', 'c859a7f90ba7f8fb96f3aa762c82718d', '', '2011-08-30 22:56:13', 62, 1),
(181, 1, 49, 'content', 'alias', 'terms-and-conditiones', 'c1d2dffe42c26bb85d070146154d6cd2', '', '2011-08-30 22:56:13', 62, 1),
(182, 1, 49, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-08-30 22:56:13', 62, 1),
(183, 1, 49, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-08-30 22:56:13', 62, 1),
(184, 1, 51, 'content', 'title', 'Prices', '41db38e118d590735911a377469dc659', '', '2011-09-06 02:55:38', 62, 1),
(185, 1, 51, 'content', 'alias', 'precios', '404aed0c2f83509423acd015bfb83c63', '', '2011-09-06 02:55:38', 62, 1),
(186, 1, 51, 'content', 'introtext', '<div>Prices in CUC only for cuban clients. For other contries, pleaase contact us to request an offer.<br /><br />PACKAGES<br /> \r\n<table cellpadding="5" border="0">\r\n<tbody>\r\n<tr style="border: 1px solid #919c96;">\r\n<td style="width: 330px; border: 1px solid #919c96;">#1 I want to be on the Web&nbsp; &nbsp;&nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Quiero estar en la Web">see details</a>)<br /></td>\r\n<td style="width: 180px; border: 1px solid #919c96;">180 cuc / year<br /></td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919c96;">#2 I am present with my website &nbsp;&nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Estoy presente con mi Web">see details</a>)</td>\r\n<td style="width: 180px; border: 1px solid #919c96;">350 cuc / year</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919c96;">#3 Among the first on the Web &nbsp;&nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Dentro de los primeros de la Web">see details</a>)</td>\r\n<td style="width: 180px; border: 1px solid #919c96;">450 cuc year +&nbsp;95 cuc each tree month<br /></td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919c96;">#4 My Web updated &nbsp; &nbsp; (<a href="index.php?option=com_content&amp;view=article&amp;id=12&amp;Itemid=55#Mi Web actualizada">vea detalles</a>)</td>\r\n<td style="width: 180px; border: 1px solid #919c96;">450 cuc / year +&nbsp;140 cuc each tree month</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<br />SERVICES<br /> \r\n<table cellpadding="5" border="0">\r\n<tbody>\r\n<tr style="border: 1px solid #919C96;">\r\n<td style="width: 330px; border: 1px solid #919C96;">Web Programming, CMS Programming (Joomla, WordPress), Virtual Stores programming</td>\r\n<td style="width: 180px; border: 1px solid #919C96;"><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=1&amp;Itemid=58">Contant us</a></span><br /></td>\r\n</tr>\r\n<tr>\r\n<td style="width: 330px; border: 1px solid #919C96;">Google Adwords Campain<br /></td>\r\n<td style="width: 180px; border: 1px solid #919C96;"><span style="text-decoration: underline;"><a href="index.php?option=com_contact&amp;view=contact&amp;id=1&amp;Itemid=58">Contact us</a></span><br /></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>', 'cf077d42b7b35f86dafb2767e858685a', '', '2011-09-06 02:55:38', 62, 1),
(187, 1, 51, 'content', 'fulltext', '', 'd41d8cd98f00b204e9800998ecf8427e', '', '2011-09-06 02:55:38', 62, 1),
(188, 1, 51, 'content', 'attribs', 'created_by=62\ncreated_by_alias=\naccess=0\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=\n\n', '56405df14c2814f631f9f82fcbfc75a2', '', '2011-09-06 02:55:38', 62, 1),
(189, 1, 2, 'contact_details', 'name', 'Offers', 'ab5307f93aa2fafcad60b5bdffb79aa6', '', '2011-09-25 13:16:27', 62, 1),
(190, 1, 2, 'contact_details', 'alias', 'offers', 'd8d510f34346567996b03f4c2e1fe7c1', '', '2011-09-25 13:16:27', 62, 1),
(191, 1, 2, 'contact_details', 'misc', 'Through this form you can send us your requirements. We present an estimate. It is therefore important that you detail the services to be recruited in order to have as much information as possible to convey a correct quote for our work. Detail us the currency in which you will pay to send the quote to your requirements. \r\n\r\nAfter filling the form, send it, In 24 hours we will contact you.', 'c7f30c61a88ac86e0441cd6387e90ba4', '', '2011-09-25 13:16:27', 62, 1),
(192, 1, 1, 'contact_details', 'misc', 'Through this contact form, send us your comments, questions or suggestions. This will help us understand your needs. \r\n\r\nOnce submitted the form, we will contact you within 24 hours. \r\n\r\nThanks for writing. \r\nTeam VealaWebCuba.com', '2913ba2ec8863dbca7636b7d0b9d9df9', '', '2011-09-01 14:04:26', 62, 1),
(193, 1, 1, 'contact_details', 'name', 'Contact', '0a25e3680d265c53368dde50f60c925f', '', '2011-09-01 14:04:26', 62, 1),
(194, 1, 1, 'contact_details', 'alias', 'contact', '0d87b5c76b4c480a107b621cc67f469d', '', '2011-09-01 14:04:26', 62, 1),
(195, 1, 51, 'modules', 'title', 'slogan', 'fac5e6f41f8c32399f1f006afc08247b', '', '2011-09-05 21:22:10', 62, 1),
(196, 1, 51, 'modules', 'content', '<div>We put your business online</div>', '8e8716760941f1be8f0154e7c8248710', '', '2011-09-05 21:22:10', 62, 1),
(197, 1, 51, 'modules', 'params', 'moduleclass_sfx=_slog\n\n', '230396e0033e94392174967b605ae812', '', '2011-09-05 21:22:10', 62, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vea_jf_tableinfo`
--

DROP TABLE IF EXISTS `vea_jf_tableinfo`;
CREATE TABLE IF NOT EXISTS `vea_jf_tableinfo` (
  `id` int(11) NOT NULL auto_increment,
  `joomlatablename` varchar(100) NOT NULL default '',
  `tablepkID` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2059 ;

--
-- Dumping data for table `vea_jf_tableinfo`
--

INSERT INTO `vea_jf_tableinfo` (`id`, `joomlatablename`, `tablepkID`) VALUES
(2045, 'banner', 'bid'),
(2046, 'bannerclient', 'cid'),
(2047, 'categories', 'id'),
(2048, 'contact_details', 'id'),
(2049, 'content', 'id'),
(2050, 'languages', 'id'),
(2051, 'menu', 'id'),
(2052, 'modules', 'id'),
(2053, 'newsfeeds', 'id'),
(2054, 'poll_data', 'id'),
(2055, 'polls', 'id'),
(2056, 'sections', 'id'),
(2057, 'users', 'id'),
(2058, 'weblinks', 'id');

-- --------------------------------------------------------

--
-- Table structure for table `vea_languages`
--

DROP TABLE IF EXISTS `vea_languages`;
CREATE TABLE IF NOT EXISTS `vea_languages` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `active` tinyint(1) NOT NULL default '0',
  `iso` varchar(20) default NULL,
  `code` varchar(20) NOT NULL default '',
  `shortcode` varchar(20) default NULL,
  `image` varchar(100) default NULL,
  `fallback_code` varchar(20) NOT NULL default '',
  `params` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `vea_languages`
--

INSERT INTO `vea_languages` (`id`, `name`, `active`, `iso`, `code`, `shortcode`, `image`, `fallback_code`, `params`, `ordering`) VALUES
(1, 'English (United Kingdom)', 1, 'en_GB.utf8, en_GB.UT', 'en-GB', 'en', '', '', '', 1),
(2, 'Español(Spanish Formal International)', 1, 'es_ES.UTF-8, spa_ES.', 'es-ES', 'es', '', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vea_menu`
--

DROP TABLE IF EXISTS `vea_menu`;
CREATE TABLE IF NOT EXISTS `vea_menu` (
  `id` int(11) NOT NULL auto_increment,
  `menutype` varchar(75) default NULL,
  `name` varchar(255) default NULL,
  `alias` varchar(255) NOT NULL default '',
  `link` text,
  `type` varchar(50) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `componentid` int(11) unsigned NOT NULL default '0',
  `sublevel` int(11) default '0',
  `ordering` int(11) default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `pollid` int(11) NOT NULL default '0',
  `browserNav` tinyint(4) default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `utaccess` tinyint(3) unsigned NOT NULL default '0',
  `params` text NOT NULL,
  `lft` int(11) unsigned NOT NULL default '0',
  `rgt` int(11) unsigned NOT NULL default '0',
  `home` int(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`,`access`),
  KEY `menutype` (`menutype`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;

--
-- Dumping data for table `vea_menu`
--

INSERT INTO `vea_menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
(1, 'mainmenu', 'Inicio', 'home', 'index.php?option=com_content&view=article&id=1', 'component', 1, 0, 20, 0, 9, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=0\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=Cuba Desarrollo Web, marketing online, creación de sitios web, diseño y programación, posicionamiento seo, soluciones informáticas, ve a la web Cuba\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 1),
(2, 'mainmenu', 'Licencia', 'joomla-license', 'index.php?option=com_content&view=article&id=5', 'component', -2, 0, 20, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'pageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1\n\n', 0, 0, 0),
(41, 'mainmenu', 'FAQ', 'faq', 'index.php?option=com_content&view=section&id=3', 'component', -2, 0, 20, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_page_title=1\nshow_description=0\nshow_description_image=0\nshow_categories=1\nshow_empty_categories=0\nshow_cat_num_articles=1\nshow_category_description=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\norderby=\nshow_noauth=0\nshow_title=1\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1', 0, 0, 0),
(11, 'recursos', 'VealaWebCuba,', 'vealawebcuba', 'index.php?option=com_content&view=article&id=19&Itemid=59', 'url', 1, 0, 0, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1\n\n', 0, 0, 0),
(12, 'recursos', 'Foros Joomla!', 'joomla-forums', 'http://forum.joomla.org', 'url', -2, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1\n\n', 0, 0, 0),
(13, 'recursos', 'Ayuda Joomla!', 'joomla-help', 'http://help.joomla.org', 'url', -2, 0, 0, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1\n\n', 0, 0, 0),
(14, 'recursos', 'OSM', 'osm-home', 'http://www.opensourcematters.org', 'url', -2, 0, 0, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 4, 'menu_image=-1\n\n', 0, 0, 0),
(15, 'recursos', 'Administración', 'administrator', 'administrator/', 'url', -2, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1\n\n', 0, 0, 0),
(20, 'usermenu', 'Tu Perfil', 'your-details', 'index.php?option=com_user&view=user&task=edit', 'component', 1, 0, 14, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 1, 3, '', 0, 0, 0),
(24, 'usermenu', 'Cerrar sesión', 'logout', 'index.php?option=com_user&view=login', 'component', 1, 0, 14, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 1, 3, '', 0, 0, 0),
(27, 'mainmenu', 'Servicios', 'servicios', 'index.php?option=com_content&view=article&id=43', 'component', 1, 0, 20, 0, 10, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=Cuba servicios para diseño y promoción de empresas en internet, vea la web cuba, tiendas virtuales, diseño de logos, servicio offshores\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(34, 'mainmenu', '¿Novedades en la 1.5?', 'what-is-new-in-1-5', 'index.php?option=com_content&view=article&id=22', 'component', -2, 0, 20, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'pageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_noauth=0\nshow_title=1\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1\n\n', 0, 0, 0),
(37, 'mainmenu', 'Más sobre Joomla!', 'more-about-joomla', 'index.php?option=com_content&view=section&id=4', 'component', -2, 0, 20, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_page_title=1\nshow_description=0\nshow_description_image=0\nshow_categories=1\nshow_empty_categories=0\nshow_cat_num_articles=1\nshow_category_description=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\norderby=\nshow_noauth=0\nshow_title=1\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1', 0, 0, 0),
(72, 'mainmenu', 'Mapa', 'mapa', 'index.php?option=com_xmap&sitemap=1', 'component', 0, 0, 47, 0, 17, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(48, 'mainmenu', 'Enlaces', 'web-links', 'index.php?option=com_weblinks&view=categories', 'component', -2, 0, 4, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'page_title=Weblinks\nimage=-1\nimage_align=right\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_comp_description=1\ncomp_description=\nshow_link_hits=1\nshow_link_description=1\nshow_other_cats=1\nshow_headings=1\nshow_page_title=1\nlink_target=0\nlink_icons=\n\n', 0, 0, 0),
(49, 'mainmenu', 'Servidor de noticias', 'news-feeds', 'index.php?option=com_newsfeeds&view=categories', 'component', -2, 0, 11, 0, 7, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_page_title=1\npage_title=Newsfeeds\nshow_comp_description=1\ncomp_description=\nimage=-1\nimage_align=right\npageclass_sfx=\nmenu_image=-1\nsecure=0\nshow_headings=1\nshow_name=1\nshow_articles=1\nshow_link=1\nshow_other_cats=1\nshow_cat_description=1\nshow_cat_items=1\nshow_feed_image=1\nshow_feed_description=1\nshow_item_description=1\nfeed_word_count=0\n\n', 0, 0, 0),
(50, 'mainmenu', 'Noticias', 'the-news', 'index.php?option=com_content&view=category&layout=blog&id=1', 'component', -2, 0, 20, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_page_title=1\npage_title=The News\nshow_description=0\nshow_description_image=0\nnum_leading_articles=1\nnum_intro_articles=4\nnum_columns=2\nnum_links=4\nshow_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\norderby_pri=\norderby_sec=\nshow_pagination=2\nshow_pagination_results=1\nshow_noauth=0\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1\n\n', 0, 0, 0),
(51, 'usermenu', 'Enviar artículo', 'submit-an-article', 'index.php?option=com_content&view=article&layout=form', 'component', 1, 0, 20, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 2, 0, '', 0, 0, 0),
(52, 'usermenu', 'Enviar enlace', 'submit-a-web-link', 'index.php?option=com_weblinks&view=weblink&layout=form', 'component', 1, 0, 4, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 2, 0, '', 0, 0, 0),
(53, 'recursos', 'Joomla! Spanish', ' Joomla! Spanish', 'http://www.joomlaspanish.org/', 'url', -2, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(54, 'mainmenu', 'Precios', 'precios', 'index.php?option=com_content&view=article&id=51', 'component', 0, 0, 20, 0, 11, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=Precios para servicios web en Cuba, cotizaciones para creación de sitios web, tarifas, pasarela de pago, ppv, pago online servicios web\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(55, 'mainmenu', 'Paquetes', 'paquetes', 'index.php?option=com_content&view=article&id=12', 'component', 1, 0, 20, 0, 12, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=Cuba empresas en internet, estar en la web, negocios en la web, empresas de Cuba, diseño, actualización y posicionamiento.\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(56, 'mainmenu', 'Solicitar Oferta', 'solicitar-oferta', 'index.php?option=com_contact&view=contact&id=2', 'component', 1, 0, 7, 0, 13, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_contact_list=0\nshow_category_crumb=0\ncontact_icons=2\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_headings=\nshow_position=\nshow_email=\nshow_telephone=\nshow_mobile=\nshow_fax=\nallow_vcard=\nbanned_email=\nbanned_subject=\nbanned_text=\nvalidate_session=\ncustom_reply=\npage_title=Cuba ofertas web, promoción web de empresas cubanas, ofertas para cotizaciones, presupuestos de servicios web\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(57, 'mainmenu', 'Tiene preguntas?', 'tiene-preguntas', 'index.php?option=com_content&view=article&id=32', 'component', 1, 0, 20, 0, 14, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=Pagina web, contenido, diseño y presentación, dirección y alojamiento web, preguntas y respuestas empresas en la web de Cuba.\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(58, 'mainmenu', 'Contactar', 'contactar', 'index.php?option=com_contact&view=contact&id=1', 'component', 1, 0, 7, 0, 15, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_contact_list=0\nshow_category_crumb=0\ncontact_icons=\nicon_address=\nicon_email=\nicon_telephone=\nicon_mobile=\nicon_fax=\nicon_misc=\nshow_headings=\nshow_position=\nshow_email=\nshow_telephone=\nshow_mobile=\nshow_fax=\nallow_vcard=\nbanned_email=\nbanned_subject=\nbanned_text=\nvalidate_session=\ncustom_reply=\npage_title=Vealawebcuba contactos, sugerencias, informe sobre creación de sitios web en Cuba, datos, mensajes.\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(59, 'mainmenu', 'VealaWebCuba', 'vealawebcuba', 'index.php?option=com_content&view=article&id=19', 'component', 1, 0, 20, 0, 16, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=Diseño web, programación web y promoción web, Vealawebcuba, servicios informáticos de Cuba, su empresa visible en internet, marketing online\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(60, 'mainmenu', 'Servicios', 'servicios', 'index.php?option=com_content&view=article&id=19', 'component', -2, 0, 20, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(61, 'recursos', 'Programación Web,', 'programacion-web', 'index.php?option=com_content&view=article&id=13', 'component', 1, 0, 20, 0, 7, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=Programacion web, creacion de sitios web, desarrollo web en cuba, php, asp, asp.net, java\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(62, 'recursos', 'Joomla,', 'joomla', 'index.php?option=com_content&view=article&id=33', 'component', 1, 0, 20, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=cms joomla, diseño web, directorios web, sitios web, comercio electrónico, hoteles, cadenas hoteleras, galerías de arte, cuba desarrollo web.\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(63, 'recursos', 'WordPress,', 'wordpress', 'index.php?option=com_content&view=article&id=28', 'component', 1, 0, 20, 0, 9, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=desarrollo web, cms word press, contenido web, php, diseño sitios web, diseño y contenido web, cms word press, desarrollo web cuba\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(64, 'recursos', 'Tiendas Virtuales,', 'tiendas-virtuales', 'index.php?option=com_content&view=article&id=15', 'component', 1, 0, 20, 0, 10, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=vealaweb, desarrollo web, tiendas virtuales, diseño y programación web, cuba comercio electrónico, cuba tiendas virtuales, servicios online, pasarelas de pago, cuba marketing online.\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(65, 'recursos', 'Diseño Web,', 'diseno-web', 'index.php?option=com_content&view=article&id=35', 'component', 1, 0, 20, 0, 11, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=creación de sitios web, diseño web en cuba, contenido y diseño de sitios web, servicios online, desarrollo web cuba\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(66, 'recursos', 'Diseño Flash,', 'diseno-flash', 'index.php?option=com_content&view=article&id=17', 'component', 1, 0, 20, 0, 12, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=creación de sitios web, diseños flash, contenido y diseño web, flash, banner flash, desarrollo web en cuba\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(67, 'recursos', 'Diseño de Logo,', 'diseno-de-logo', 'index.php?option=com_content&view=article&id=14', 'component', 1, 0, 20, 0, 13, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=creación de sitios web, diseño de logos, identidad corporativa, diseños de empresas, flash, gift animados, vealawebcuba, empresas online\nshow_page_title=0\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(68, 'recursos', 'Diseño de Brochure,', 'diseno-de-brochure', 'index.php?option=com_content&view=article&id=13', 'component', 0, 0, 20, 0, 14, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(69, 'recursos', 'Identidad Corporativa,', 'identidad-corporativa', 'index.php?option=com_content&view=article&id=13', 'component', 0, 0, 20, 0, 15, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(70, 'recursos', 'Servicio Offshore,', 'servicio-offshore', 'index.php?option=com_content&view=article&id=34%3Aprogramacionoffshore&catid=32&Itemid=27', 'url', 1, 0, 0, 0, 16, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'menu_image=-1\n\n', 0, 0, 0),
(71, 'recursos', 'Posicionamiento Web,', 'posicionamiento-web', 'index.php?option=com_content&view=article&id=25', 'component', 1, 0, 20, 0, 17, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=posicionamiento web, buscadores, Google, Yahoo, MSN, posicionamiento seo, palabras claves, keywords, alta en buscadores, primeros diez\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(73, 'recursos', 'Webmaster,', 'webmaster', 'index.php?option=com_content&view=article&id=46', 'component', 1, 0, 20, 0, 18, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=desarrollo web, vealawebcuba, comercio electronico, actualizar contenido web, programacion web, actualización de contenido web, desarrollo web cuba\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(74, 'recursos', 'Traducción al inglés', 'traduccioningles', 'index.php?option=com_content&view=article&id=47', 'component', 1, 0, 20, 0, 19, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(75, 'Menu-inferior', 'Política de Privacidad', 'politica-de-privacidad', 'index.php?option=com_content&view=article&id=48', 'component', 1, 0, 20, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(76, 'Menu-inferior', 'Términos y Condiciones', 'terminos-y-condiciones', 'index.php?option=com_content&view=article&id=49', 'component', 1, 0, 20, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(77, 'mainmenu', 'Portafolio', 'portafolio', 'index.php?option=com_content&view=article&id=50', 'component', 1, 0, 20, 0, 18, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'show_noauth=0\nshow_title=\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=0\nshow_create_date=0\nshow_modify_date=0\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=0\nshow_pdf_icon=0\nshow_print_icon=0\nshow_email_icon=0\nshow_hits=0\nfeed_summary=\npage_title=Diseño web, programación web y promoción web, Vealawebcuba, servicios informáticos de Cuba, su empresa visible en internet, marketing online\nshow_page_title=0\npageclass_sfx= noIndex\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `vea_menu_types`
--

DROP TABLE IF EXISTS `vea_menu_types`;
CREATE TABLE IF NOT EXISTS `vea_menu_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `menutype` varchar(75) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `menutype` (`menutype`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `vea_menu_types`
--

INSERT INTO `vea_menu_types` (`id`, `menutype`, `title`, `description`) VALUES
(1, 'mainmenu', 'Menú principal', 'Este es el menú principal del sitio'),
(2, 'usermenu', 'Menú de usuario', 'Menú para usuarios logueados'),
(4, 'recursos', 'Recursos', 'Enlaces adicionales'),
(7, 'Menu-inferior', 'Menú inferior', '');

-- --------------------------------------------------------

--
-- Table structure for table `vea_messages`
--

DROP TABLE IF EXISTS `vea_messages`;
CREATE TABLE IF NOT EXISTS `vea_messages` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `user_id_from` int(10) unsigned NOT NULL default '0',
  `user_id_to` int(10) unsigned NOT NULL default '0',
  `folder_id` int(10) unsigned NOT NULL default '0',
  `date_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` int(11) NOT NULL default '0',
  `priority` int(1) unsigned NOT NULL default '0',
  `subject` text NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `useridto_state` (`user_id_to`,`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vea_messages_cfg`
--

DROP TABLE IF EXISTS `vea_messages_cfg`;
CREATE TABLE IF NOT EXISTS `vea_messages_cfg` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `cfg_name` varchar(100) NOT NULL default '',
  `cfg_value` varchar(255) NOT NULL default '',
  UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vea_migration_backlinks`
--

DROP TABLE IF EXISTS `vea_migration_backlinks`;
CREATE TABLE IF NOT EXISTS `vea_migration_backlinks` (
  `itemid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` text NOT NULL,
  `sefurl` text NOT NULL,
  `newurl` text NOT NULL,
  PRIMARY KEY  (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vea_modules`
--

DROP TABLE IF EXISTS `vea_modules`;
CREATE TABLE IF NOT EXISTS `vea_modules` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `position` varchar(50) default NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `module` varchar(50) default NULL,
  `numnews` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `showtitle` tinyint(3) unsigned NOT NULL default '1',
  `params` text NOT NULL,
  `iscore` tinyint(4) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  `control` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `vea_modules`
--

INSERT INTO `vea_modules` (`id`, `title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) VALUES
(1, 'Menú principal', '', 1, 'top', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=mainmenu\nmenu_style=horiz_flat\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=_menu\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 1, 0, ''),
(2, 'Acceso', '', 1, 'login', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, '', 1, 1, ''),
(3, 'Popular', '', 3, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_popular', 0, 2, 1, '', 0, 1, ''),
(4, 'Artículos añadidos recientemente', '', 4, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_latest', 0, 2, 1, 'ordering=c_dsc\nuser_id=0\ncache=0\n\n', 0, 1, ''),
(5, 'Menú de estadísticas', '', 5, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_stats', 0, 2, 1, '', 0, 1, ''),
(6, 'Los mensajes no leídos', '', 1, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_unread', 0, 2, 1, '', 1, 1, ''),
(7, 'Usuarios Online', '', 2, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_online', 0, 2, 1, '', 1, 1, ''),
(8, 'Toolbar', '', 1, 'toolbar', 0, '0000-00-00 00:00:00', 1, 'mod_toolbar', 0, 2, 1, '', 1, 1, ''),
(9, 'Iconos rápidos', '', 1, 'icon', 0, '0000-00-00 00:00:00', 1, 'mod_quickicon', 0, 2, 1, '', 1, 1, ''),
(10, 'Usuarios identificados', '', 2, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_logged', 0, 2, 1, '', 0, 1, ''),
(11, 'Footer', '', 0, 'footer', 0, '0000-00-00 00:00:00', 1, 'mod_footer', 0, 0, 1, '', 1, 1, ''),
(12, 'Menú Admin', '', 1, 'menu', 0, '0000-00-00 00:00:00', 1, 'mod_menu', 0, 2, 1, '', 0, 1, ''),
(13, 'Admin SubMenu', '', 1, 'submenu', 0, '0000-00-00 00:00:00', 1, 'mod_submenu', 0, 2, 1, '', 0, 1, ''),
(14, 'Estado del usuario', '', 1, 'status', 0, '0000-00-00 00:00:00', 1, 'mod_status', 0, 2, 1, '', 0, 1, ''),
(15, 'Título', '', 1, 'title', 0, '0000-00-00 00:00:00', 1, 'mod_title', 0, 2, 1, '', 0, 1, ''),
(16, 'Encuestas', '', 1, 'right', 0, '0000-00-00 00:00:00', 0, 'mod_poll', 0, 0, 1, 'id=14\ncache=1', 0, 0, ''),
(17, 'Menú de usuario', '', 3, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_mainmenu', 0, 1, 1, 'menutype=usermenu\nmoduleclass_sfx=_menu\ncache=1', 1, 0, ''),
(18, 'Acceso', '', 6, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_login', 0, 0, 1, 'greeting=1\nname=0', 1, 0, ''),
(19, 'Últimas noticias', '', 4, 'user1', 0, '0000-00-00 00:00:00', 0, 'mod_latestnews', 0, 0, 1, 'cache=1', 1, 0, ''),
(20, 'Estadísticas', '', 4, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_stats', 0, 0, 1, 'serverinfo=1\nsiteinfo=1\ncounter=1\nincrease=0\nmoduleclass_sfx=', 0, 0, ''),
(21, '¿Quién está en línea?', '', 3, 'right', 0, '0000-00-00 00:00:00', 0, 'mod_whosonline', 0, 0, 1, 'online=1\nusers=1\nmoduleclass_sfx=', 0, 0, ''),
(22, 'Popular', '', 6, 'user2', 0, '0000-00-00 00:00:00', 0, 'mod_mostread', 0, 0, 1, 'cache=1', 0, 0, ''),
(23, 'Archivo', '', 7, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_archive', 0, 0, 1, 'cache=1', 1, 0, ''),
(24, 'Secciones', '', 8, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_sections', 0, 0, 1, 'cache=1', 1, 0, ''),
(25, 'Newsflash', '', 3, 'top', 0, '0000-00-00 00:00:00', 0, 'mod_newsflash', 0, 0, 1, 'catid=3\r\nstyle=random\r\nitems=\r\nmoduleclass_sfx=', 0, 0, ''),
(26, 'Items relacionados', '', 9, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_related_items', 0, 0, 1, '', 0, 0, ''),
(27, 'busqueda', '', 4, 'pos1', 0, '0000-00-00 00:00:00', 1, 'mod_search', 0, 0, 1, 'moduleclass_sfx=_Buscar\nwidth=20\ntext=\nbutton=1\nbutton_pos=right\nimagebutton=\nbutton_text=Buscar\nset_itemid=\ncache=1\ncache_time=900\n\n', 0, 0, ''),
(28, 'Imagen aleatoria', '', 9, 'right', 0, '0000-00-00 00:00:00', 0, 'mod_random_image', 0, 0, 1, '', 0, 0, ''),
(30, 'Anuncios', '', 1, 'footer', 0, '0000-00-00 00:00:00', 0, 'mod_banners', 0, 0, 0, 'target=1\ncount=1\ncid=1\ncatid=33\ntag_search=0\nordering=random\nheader_text=\nfooter_text=\nmoduleclass_sfx=\ncache=1\ncache_time=15\n\n', 1, 0, ''),
(31, 'Recursos', '', 1, 'pos1', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=recursos\nmenu_style=horiz_flat\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=_menuRecursos\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, ''),
(32, 'Wrapper', '', 10, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_wrapper', 0, 0, 1, '', 0, 0, ''),
(33, 'Pié de página', '', 2, 'footer', 0, '0000-00-00 00:00:00', 0, 'mod_footer', 0, 0, 0, 'cache=1\n\n', 1, 0, ''),
(34, 'Visor de noticias', '', 11, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_feed', 0, 0, 1, '', 1, 0, ''),
(35, 'Ruta', '', 1, 'breadcrumb', 0, '0000-00-00 00:00:00', 1, 'mod_breadcrumbs', 0, 0, 1, 'moduleclass_sfx=\ncache=0\nshowHome=1\nhomeText=Home\nshowComponent=1\nseparator=\n\n', 1, 0, ''),
(36, 'Syndication', '', 3, 'syndicate', 0, '0000-00-00 00:00:00', 0, 'mod_syndicate', 0, 0, 0, '', 1, 0, ''),
(37, 'Recursos', '', 5, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=recursos\nmenu_style=horiz_flat\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=_menuRecursos\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, ''),
(38, 'Anuncios', '', 3, 'right', 0, '0000-00-00 00:00:00', 0, 'mod_banners', 0, 0, 1, 'count=4\r\nrandomise=0\r\ncid=0\r\ncatid=14\r\nheader_text=Featured Links:\r\nfooter_text=<a href="http://www.joomla.org">Ads by Joomla!</a>\r\nmoduleclass_sfx=_text\r\ncache=0\r\n\r\n', 0, 0, ''),
(41, 'Bienvenido a Joomla!', '<div style="padding: 5px"><p>Felicidades por elegir Joomla! como tu sistema de gestión de contenido. Esperamos que puedas crear con éxito un sitio web con nuestro programa y quizá que puedas aportar algo a la comunidad más adelante.</p><p>Para hacer que empiceces con Joomla! lo mejor y más rapidamente posible, queremos darte unos cuantos puntos de referencia a la documentación, preguntas frecuentas y ayuda sobre la seguridad de tu servidor. Un buen lugar donde empezar es en &quot;<a href="http://www.joomlaspanish.org/foros/index.php" target="_blank">Foros de la comunidad de Joomla! spanish</a>&quot;.</p><p>Nota: Para eliminar este mensaje de &quot;Bienvenido a Joomla!&quot; accede al gestor de módulos (en el gestor de extensiones).  Aquí hay un <a href="index.php?option=com_modules&amp;client=1">enlace rápido</a> a esa pantalla.</p></div>', 1, 'cpanel', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 2, 1, 'moduleclass_sfx=\n\n', 1, 1, ''),
(42, 'Patrocinadores', '', 1, 'right', 0, '0000-00-00 00:00:00', 0, 'mod_banners', 0, 0, 1, 'target=1\ncount=1\ncid=2\ncatid=14\ntag_search=0\nordering=0\nheader_text=\nfooter_text=\nmoduleclass_sfx=\ncache=1\ncache_time=900\n\n', 0, 0, ''),
(43, 'Patrocinadores 1', '', 2, 'right', 0, '0000-00-00 00:00:00', 0, 'mod_banners', 0, 0, 0, 'target=1\ncount=1\ncid=3\ncatid=14\ntag_search=0\nordering=0\nheader_text=\nfooter_text=\nmoduleclass_sfx=\ncache=1\ncache_time=900\n\n', 0, 0, ''),
(44, 'Anuncio', '<p><img alt="paginas" src="images/stories/paginas.png" width="138" height="78" /> <br /><br /> Diseño Web,<br /> Programación Web,<br /> Servicio Offshore,<br /> Servicio SEO<br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php?option=com_content&amp;view=article&amp;id=43&amp;Itemid=27">Ver más</a></p>', 2, 'left', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 0, 'moduleclass_sfx=_anuncio\n\n', 0, 0, ''),
(45, 'Súmate al equipo y desarrolla VealaWebCuba con tus ideas', '', 2, 'pos1', 0, '0000-00-00 00:00:00', 1, 'mod_rapid_contact', 0, 0, 1, 'dummy1=0\nemail_recipient=contacto@vealawebcuba.com\nfrom_name=VealaWeb\nfrom_email=contacto@velawebcuba.com\ndummy2=0\nemail_label=Tu Correo:\nsubject_label=Subject:\nmessage_label=Tu Idea:\nbutton_text=ENVIAR\npage_text=Gracias por su sugerencia\nerror_text=Su mensaje no puede ser enviado. Inténtelo nuevamente\nno_email=Escriba su correo por favor\ninvalid_email=Escriba una dirección válida\npre_text=\ndummy3=0\nthank_text_color=#FF0000\nerror_text_color=#FF0000\nemail_width=15\nsubject_width=15\nmessage_width=12\nbutton_width=100\ndummy4=0\nexact_url=1\ndisable_https=0\nfixed_url=0\nfixed_url_address=\ndummy5=0\nenable_anti_spam=0\nanti_spam_q=How many eyes has a typical person?\nanti_spam_a=2\nanti_spam_position=0\ndummy8=0\nmoduleclass_sfx=_correoIni\n\n', 0, 0, ''),
(46, 'Lapiz', '<p><img alt="lapiz" src="images/stories/lapiz.gif" height="187" width="94" /></p>', 3, 'pos1', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 0, 'moduleclass_sfx=_lapiz\n\n', 0, 0, ''),
(47, 'Menú Inferior', '', 0, 'pos2', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=mainmenu\nmenu_style=horiz_flat\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=_menuInf\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, ''),
(48, 'Menú inferior', '', 0, 'pos3', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=Menu-inferior\nmenu_style=horiz_flat\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx= menuInf\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\\|\nend_spacer=\n\n', 0, 0, ''),
(49, 'Language Selection', '', 0, 'top', 0, '0000-00-00 00:00:00', 1, 'mod_jflanguageselection', 0, 0, 0, 'type=rawimages\nshow_active=0\ninc_jf_css=1\nmoduleclass_sfx=_lengua\ncache_href=1\n\n', 0, 0, ''),
(50, 'Direct Translation', '', 0, 'status', 0, '0000-00-00 00:00:00', 1, 'mod_translate', 0, 2, 0, '', 0, 1, ''),
(51, 'slogan', '<div>Le ponemos su negocio en internet</div>', 4, 'top', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 0, 'moduleclass_sfx=_slog\n\n', 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `vea_modules_menu`
--

DROP TABLE IF EXISTS `vea_modules_menu`;
CREATE TABLE IF NOT EXISTS `vea_modules_menu` (
  `moduleid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`moduleid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vea_modules_menu`
--

INSERT INTO `vea_modules_menu` (`moduleid`, `menuid`) VALUES
(1, 0),
(16, 1),
(17, 0),
(18, 1),
(19, 1),
(19, 2),
(19, 4),
(19, 27),
(19, 36),
(21, 1),
(22, 1),
(22, 2),
(22, 4),
(22, 27),
(22, 36),
(25, 0),
(27, 1),
(29, 0),
(30, 0),
(31, 1),
(32, 0),
(33, 0),
(34, 0),
(35, 0),
(36, 0),
(37, 11),
(37, 20),
(37, 24),
(37, 27),
(37, 51),
(37, 52),
(37, 54),
(37, 55),
(37, 56),
(37, 57),
(37, 58),
(37, 59),
(37, 61),
(37, 62),
(37, 63),
(37, 64),
(37, 65),
(37, 66),
(37, 67),
(37, 70),
(37, 71),
(37, 73),
(37, 74),
(37, 75),
(37, 76),
(37, 77),
(38, 1),
(39, 43),
(39, 44),
(39, 45),
(39, 46),
(39, 47),
(40, 0),
(42, 0),
(43, 0),
(44, 0),
(45, 1),
(46, 1),
(47, 0),
(48, 0),
(49, 0),
(51, 0);

-- --------------------------------------------------------

--
-- Table structure for table `vea_newsfeeds`
--

DROP TABLE IF EXISTS `vea_newsfeeds`;
CREATE TABLE IF NOT EXISTS `vea_newsfeeds` (
  `catid` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `alias` varchar(255) NOT NULL default '',
  `link` text NOT NULL,
  `filename` varchar(200) default NULL,
  `published` tinyint(1) NOT NULL default '0',
  `numarticles` int(11) unsigned NOT NULL default '1',
  `cache_time` int(11) unsigned NOT NULL default '3600',
  `checked_out` tinyint(3) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `rtl` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `vea_newsfeeds`
--

INSERT INTO `vea_newsfeeds` (`catid`, `id`, `name`, `alias`, `link`, `filename`, `published`, `numarticles`, `cache_time`, `checked_out`, `checked_out_time`, `ordering`, `rtl`) VALUES
(4, 1, 'Joomla! - Noticias oficiales', 'joomla-official-news', 'http://www.joomla.org/index.php?option=com_rss_xtd&feed=RSS2.0&type=com_frontpage&Itemid=1', '', 1, 5, 3600, 0, '0000-00-00 00:00:00', 8, 0),
(4, 2, 'Joomla! - Noticias de la comunidad', 'joomla-community-news', 'http://www.joomla.org/index.php?option=com_rss_xtd&feed=RSS2.0&type=com_content&task=blogcategory&id=0&Itemid=33', '', 1, 5, 3600, 0, '0000-00-00 00:00:00', 9, 0),
(6, 3, 'Linux hoy', 'linux-today', 'http://linuxtoday.com/backend/my-netscape.rdf', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 1, 0),
(5, 4, 'Noticias de negocios', 'business-news', 'http://headlines.internet.com/internetnews/bus-news/news.rss', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 2, 0),
(7, 5, 'Noticias de desarrolladores', 'web-developer-news', 'http://headlines.internet.com/internetnews/wd-news/news.rss', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 3, 0),
(6, 6, 'Linux Central:Nuevos productos', 'linux-central-news-products', 'http://linuxcentral.com/backend/lcnew.rdf', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 4, 0),
(6, 7, 'Linux Central:Los mejores', 'linux-central-best-selling', 'http://linuxcentral.com/backend/lcbestns.rdf', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 5, 0),
(6, 8, 'Linux Central:Especiales', 'linux-central-daily-specials', 'http://linuxcentral.com/backend/lcspecialns.rdf', '', 1, 3, 3600, 0, '0000-00-00 00:00:00', 6, 0),
(34, 9, 'Portal Joomla! Spanish', 'portal-joomla-spanish', 'http://www.joomlaspanish.org/component/option,com_rss/feed,RSS2.0/no_html,1/', NULL, 1, 5, 3600, 0, '0000-00-00 00:00:00', 1, 0),
(34, 10, 'Centro de Extensiones Joomla! Spanish', 'centro-de-extensiones-joomla-spanish', 'http://extensiones.joomlaspanish.org/index.php?option=com_remository&Itemid=27&func=rss&no_html=1', NULL, 1, 15, 3600, 0, '0000-00-00 00:00:00', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `vea_plugins`
--

DROP TABLE IF EXISTS `vea_plugins`;
CREATE TABLE IF NOT EXISTS `vea_plugins` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `element` varchar(100) NOT NULL default '',
  `folder` varchar(100) NOT NULL default '',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(3) NOT NULL default '0',
  `iscore` tinyint(3) NOT NULL default '0',
  `client_id` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_folder` (`published`,`client_id`,`access`,`folder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=49 ;

--
-- Dumping data for table `vea_plugins`
--

INSERT INTO `vea_plugins` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
(1, 'Autenticación - Joomla', 'joomla', 'authentication', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(2, 'Autenticación - LDAP', 'ldap', 'authentication', 0, 2, 0, 1, 0, 0, '0000-00-00 00:00:00', 'host=\nport=389\nuse_ldapV3=0\nnegotiate_tls=0\nno_referrals=0\nauth_method=bind\nbase_dn=\nsearch_string=\nusers_dn=\nusername=\npassword=\nldap_fullname=fullName\nldap_email=mail\nldap_uid=uid\n\n'),
(3, 'Autenticación - GMail', 'gmail', 'authentication', 0, 4, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(4, 'Autenticación - OpenID', 'openid', 'authentication', 0, 3, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(5, 'Usuario - Joomla!', 'joomla', 'user', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'autoregister=1\n\n'),
(6, 'Buscar - Contenido', 'content', 'search', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\nsearch_content=1\nsearch_uncategorised=1\nsearch_archived=1\n\n'),
(7, 'Buscar - Contactos', 'contacts', 'search', 0, 3, 1, 1, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n'),
(8, 'Buscar - Categorías', 'categories', 'search', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n'),
(9, 'Buscar - Secciones', 'sections', 'search', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n'),
(10, 'Buscar - Newsfeeds', 'newsfeeds', 'search', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n'),
(11, 'Buscar - Weblinks', 'weblinks', 'search', 0, 2, 1, 1, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n'),
(12, 'Contenido - Pagebreak', 'pagebreak', 'content', 0, 10000, 1, 1, 0, 0, '0000-00-00 00:00:00', 'enabled=1\ntitle=1\nmultipage_toc=1\nshowall=1\n\n'),
(13, 'Contenido - Votar', 'vote', 'content', 0, 4, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(14, 'Contenido - Email Cloaking', 'emailcloak', 'content', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', 'mode=1\n\n'),
(15, 'Contenido - Code Hightlighter (GeSHi)', 'geshi', 'content', 0, 5, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(16, 'Contenido - Cargar módulo', 'loadmodule', 'content', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', 'enabled=1\nstyle=0\n\n'),
(17, 'Contenido - Page Navigation', 'pagenavigation', 'content', 0, 2, 1, 1, 0, 0, '0000-00-00 00:00:00', 'position=1\n\n'),
(18, 'Editor - No Editor', 'none', 'editors', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(19, 'Editor - TinyMCE', 'tinymce', 'editors', 0, 2, 1, 1, 0, 0, '0000-00-00 00:00:00', 'mode=advanced\nskin=0\ncompressed=0\ncleanup_startup=0\ncleanup_save=2\nentity_encoding=raw\nlang_mode=0\nlang_code=es\ntext_direction=ltr\ncontent_css=1\ncontent_css_custom=\nrelative_urls=1\nnewlines=1\ninvalid_elements=applet\nextended_elements=\ntoolbar=top\ntoolbar_align=left\nhtml_height=550\nhtml_width=750\nelement_path=1\nfonts=1\npaste=1\nsearchreplace=1\ninsertdate=1\nformat_date=%Y-%m-%d\ninserttime=1\nformat_time=%H:%M:%S\ncolors=1\ntable=1\nsmilies=1\nmedia=1\nhr=1\ndirectionality=1\nfullscreen=1\nstyle=1\nlayer=1\nxhtmlxtras=0\nvisualchars=1\nnonbreaking=1\nblockquote=1\ntemplate=0\nadvimage=1\nadvlink=1\nautosave=0\ncontextmenu=1\ninlinepopups=1\nsafari=0\ncustom_plugin=\ncustom_button=\n\n'),
(20, 'Editor - XStandard Lite 2.0', 'xstandard', 'editors', 0, 3, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(21, 'Editor Botón - Imagen', 'image', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(22, 'Editor Botón - Pagebreak', 'pagebreak', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(23, 'Editor Botón - Leer más', 'readmore', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(24, 'XML-RPC - Joomla', 'joomla', 'xmlrpc', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', ''),
(25, 'XML-RPC - Blogger API', 'blogger', 'xmlrpc', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', 'catid=1\nsectionid=0\n\n'),
(27, 'Sistema - SEF', 'sef', 'system', 0, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(28, 'Sistema - Debug', 'debug', 'system', 0, 2, 1, 0, 0, 0, '0000-00-00 00:00:00', 'queries=1\nmemory=1\nlangauge=1\n\n'),
(29, 'Sistema - Legado', 'legacy', 'system', 0, 3, 0, 1, 0, 0, '0000-00-00 00:00:00', 'route=0\n\n'),
(30, 'Sistema - Cache', 'cache', 'system', 0, 4, 0, 1, 0, 0, '0000-00-00 00:00:00', 'browsercache=0\ncachetime=15\n\n'),
(31, 'Sistema - Log', 'log', 'system', 0, 5, 0, 1, 0, 0, '0000-00-00 00:00:00', ''),
(32, 'Sistema - Recordarme', 'remember', 'system', 0, 6, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(33, 'Sistema - Backlink', 'backlink', 'system', 0, 7, 0, 1, 0, 0, '0000-00-00 00:00:00', ''),
(34, 'System - Mootools Upgrade', 'mtupgrade', 'system', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(35, 'Editor - JCE', 'jce', 'editors', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', 'editor_gzip=0\neditor_verify_html=0\neditor_entity_encoding=raw\ncleanup_pluginmode=0\ncleanup_keep_nbsp=1\neditor_forced_root_block=div\neditor_newlines=1\neditor_body_class_type=custom\neditor_body_class_custom=\neditor_content_css=1\neditor_content_css_custom=templates/$template/css/editor_content.css\neditor_custom_config=\neditor_callback_file=\neditor_help_url=http://www.joomlacontenteditor.net/index.php?option=com_content&view=article\n\n'),
(36, 'Button - Xmap Link', 'xmaplink', 'editors-xtd', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(38, 'System - osolCaptcha', 'osolcaptcha', 'system', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'bgColor=#2c8007\ntextColor=#ffffff\nenableForModules=No\nimageFunction=Adv\nenableForContactUs=Yes\nenableForComLogin=Yes\nenableForRegistration=Yes\nenableForReset=Yes\nenableForRemind=Yes\nenableSecondLevelSecurity=No\nbotScoutProtection=Stop\nbotscoutAPIKey=\nreportBotscoutNegativeMail=\nredirectURLforSuspectedIPs=http://www.google.com/\nadminPassPhrase=\n\n'),
(39, 'System - Jfdatabase', 'jfdatabase', 'system', 0, -100, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(40, 'System - Jfrouter', 'jfrouter', 'system', 0, -101, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(41, 'Content - Jfalternative', 'jfalternative', 'content', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(42, 'Search - Jfcategories', 'jfcategories', 'search', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(43, 'Search - Jfcontacts', 'jfcontacts', 'search', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(44, 'Search - Jfcontent', 'jfcontent', 'search', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(45, 'Search - Jfnewsfeeds', 'jfnewsfeeds', 'search', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(46, 'Search - Jfsections', 'jfsections', 'search', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(47, 'Search - Jfweblinks', 'jfweblinks', 'search', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(48, 'Joomfish - Missing_translation', 'missing_translation', 'joomfish', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `vea_polls`
--

DROP TABLE IF EXISTS `vea_polls`;
CREATE TABLE IF NOT EXISTS `vea_polls` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `voters` int(9) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `access` int(11) NOT NULL default '0',
  `lag` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `vea_polls`
--

INSERT INTO `vea_polls` (`id`, `title`, `alias`, `voters`, `checked_out`, `checked_out_time`, `published`, `access`, `lag`) VALUES
(14, '¿Para qué usas Joomla!?', 'joomla-is-used-for', 11, 0, '0000-00-00 00:00:00', 1, 0, 86400);

-- --------------------------------------------------------

--
-- Table structure for table `vea_poll_data`
--

DROP TABLE IF EXISTS `vea_poll_data`;
CREATE TABLE IF NOT EXISTS `vea_poll_data` (
  `id` int(11) NOT NULL auto_increment,
  `pollid` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  `hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`,`text`(1))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `vea_poll_data`
--

INSERT INTO `vea_poll_data` (`id`, `pollid`, `text`, `hits`) VALUES
(1, 14, 'Comunidades-Grupos', 2),
(2, 14, 'Sitios públicos', 3),
(3, 14, 'Comercio electrónico', 1),
(4, 14, 'Blogs', 0),
(5, 14, 'Intranets', 0),
(6, 14, 'Fotos y sitios multimedia', 2),
(7, 14, 'Para todo lo anterior!', 3),
(8, 14, '', 0),
(9, 14, '', 0),
(10, 14, '', 0),
(11, 14, '', 0),
(12, 14, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vea_poll_date`
--

DROP TABLE IF EXISTS `vea_poll_date`;
CREATE TABLE IF NOT EXISTS `vea_poll_date` (
  `id` bigint(20) NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `vote_id` int(11) NOT NULL default '0',
  `poll_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `vea_poll_date`
--

INSERT INTO `vea_poll_date` (`id`, `date`, `vote_id`, `poll_id`) VALUES
(1, '2006-10-09 13:01:58', 1, 14),
(2, '2006-10-10 15:19:43', 7, 14),
(3, '2006-10-11 11:08:16', 7, 14),
(4, '2006-10-11 15:02:26', 2, 14),
(5, '2006-10-11 15:43:03', 7, 14),
(6, '2006-10-11 15:43:38', 7, 14),
(7, '2006-10-12 00:51:13', 2, 14),
(8, '2007-05-10 19:12:29', 3, 14),
(9, '2007-05-14 14:18:00', 6, 14),
(10, '2007-06-10 15:20:29', 6, 14),
(11, '2007-07-03 12:37:53', 2, 14);

-- --------------------------------------------------------

--
-- Table structure for table `vea_poll_menu`
--

DROP TABLE IF EXISTS `vea_poll_menu`;
CREATE TABLE IF NOT EXISTS `vea_poll_menu` (
  `pollid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pollid`,`menuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vea_sections`
--

DROP TABLE IF EXISTS `vea_sections`;
CREATE TABLE IF NOT EXISTS `vea_sections` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` text NOT NULL,
  `scope` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_scope` (`scope`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `vea_sections`
--

INSERT INTO `vea_sections` (`id`, `title`, `name`, `alias`, `image`, `scope`, `image_position`, `description`, `published`, `checked_out`, `checked_out_time`, `ordering`, `access`, `count`, `params`) VALUES
(5, 'General', '', 'general', '', 'content', 'left', '', 1, 0, '0000-00-00 00:00:00', 6, 0, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `vea_session`
--

DROP TABLE IF EXISTS `vea_session`;
CREATE TABLE IF NOT EXISTS `vea_session` (
  `username` varchar(150) default '',
  `time` varchar(14) default '',
  `session_id` varchar(200) NOT NULL default '0',
  `guest` tinyint(4) default '1',
  `userid` int(11) default '0',
  `usertype` varchar(50) default '',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `client_id` tinyint(3) unsigned NOT NULL default '0',
  `data` longtext,
  PRIMARY KEY  (`session_id`(64)),
  KEY `whosonline` (`guest`,`usertype`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vea_session`
--

INSERT INTO `vea_session` (`username`, `time`, `session_id`, `guest`, `userid`, `usertype`, `gid`, `client_id`, `data`) VALUES
('', '1317086055', '459bfe781dbd67acb520f383a84b40da', 1, 0, '', 0, 0, 'v7IkjNRH-_ZpmLWRYI5dnOBFvDGhkReVj_-kQo9x9pSraSFoxtXU5W7nqvofGTontiptwEioLTf8kJAMgl3zrXDTyvZ_fYWcE_IS6QtgsgA9EFoDJlOZ3hbdF1P6u3nf6hm48L2X3ML1CRc1opY6FKsBZhN5Xfi72I8lIaH2iBuMu4dE4YBFQ267PCs3nLGgBOTQiheJx2y-6hD3wzy7zyifo90G5kuUCw1Jqo9zah299irE-ICdU3SMlNuipGP-d-JMLP8BkOg10KYs0QlYlAwM5wdHp4dt_OTgF5IYgKDwOi7iXCaQU3Z-r4aLhMR9xllFWygvF-UXrVfLaojJjO4PkybyPCVLpzlSHB8Hl3WISPZqUmDAiN_BlC4TXRH-hEWk3hd_9e519IsEiW6ZaDtv6KN7af5vDerEgnyG2Hyoj4FaJMQQ-l3SyipdHXfcLN0bGGi8NVDayATtrKEohtkSKGbWViXWFKvASvCyx4G8-sq253foP1TLsU57vgE7CyR-Rbs9ULynJ-w8e8GjCla2gsp7L_NY63P4w5aF3qJPeRX6L0lbIJr8D4cAu2C3pqHY9c5P_T6Qxx8X7Hx-hML_XKcVh0tgwHhtXI6aCPjHLws8DkWR8kMyaBciuUkeQ526zIWiwHMlZWbjL-JfZmAwNRFKvYbDcaKDVB1oYE6V5xm_KBaTKn8-Nd652GHmm-18lSX0WRe1y0I_UcokMqti68v2zsCjcKuIB0aDwSPAksrjnz0RuGMozKM5109fyyDJQBCj3_eWVhnaJ1BgrgkpnNGrDQa6xU_-hEUHK1o_JaQuofTMLzNPrVx77o8Xe-uCuTVorZR-W4HaVwCByK3bkz4mnwXjeH3shD0LFbnGV9dHze03oQTlEWPvGRG7aDdB0gJusaVIZH35Up7hgmmLFDZedxpGMtKT-PuztfFuJEbSHJf5E9cH5hF2xBG9NZGAq2U1q0Wvj6bURDDz3tBfcvuVchg3fnN8GWpFAdsQV_3Rk3Ml7hJZ_ytuzKBpbEPFJbreCTvm0hTJrQTqFp_zaC3e61qJxZgkWWbZX8S0MKYssT8EEGYFytjnlG2niPuEKvnRm6O_cY7hMDLOkmEXzm_bUK8YP5zV8JwXNE5xzOHHvLMz6DfTFCNl7a8Socw7pVL1ewYdbDyiXVAMespMJo2i2XM1_cMQChMudAsyr9GP8poxOmPW29Z4ducZBnPWAp9KCI32o2V_hxFgkZj43RrBS3lvITX09IuATUzgZIVMW39SdAzascyPm6F1BkcCo4Hh-gPcTwl0mNe0yeXpfNoX0AaW9hTmu5Hx5zRbr7ygv0XWYTqCjQ97CWz2rSUB091wT9bldAlNDDMY4PfCxVpN1ofWUo9WmeACNqcTDz1Ak-sFXDDZyVH4TvvI7a1O8ocPby9PF8pCiMK104uLLiTd1mkClBVO5UWufobgOFY-x5NuKA5Y6-WkZOr5JG4R2j6NDPNjhVMGxNAjhmLKvUpBWZRK-Q34yC7UraraEHPcN9UjNaP2odAwH3oRR8-iLFNhrGZ4-xQod_Ql5Bcwpu0vLjdIoXpwP7XFbtzOto_GE9y8olaW17YtGMvEp2mh5_5L4ae2BT3raUE_IiOMa0YljtuFOLCkVUdmZRiajeAsoM7LwoNRJNPb_aGkstMHjP4q2HBK_FvREbbnaftvY73oTcjSrPXji2qR2cc.'),
('', '1317086075', '58d89f3d93c0e165d100afc6520659a8', 1, 0, '', 0, 1, 'IfuM4MgnzKGC-_WFStBRhylCfqVEEaJQhnHRyMnMFSrgpimzwnLgp7F6er1ousuv_wWNXs4WKFGfiNILpqY_Sc5qZAWNK3WrJgDn97sK8fMh9mZIW_IKSjdW2AKKUl1G2EFC4idCeYVlgd3f_susewPNrNU1bXMAxB3qGpvLRsRCEUdOK8ffXncDXyHFwYcdSlcDAOTNqBg95uZovxmax_qOTJPcgRD2JZG4dCV--hpM8qUdpyPIuchswzmK8Vy-M5p80xrmufZ-sK6CuCFjGVPqwxAhfYURHui7BsKnyQXpWQIzvaThSy0QQ8GLAxDnh4_Jjvomm60MlubGJYmGJXQpH9jorWQ2KqRlvOkQvh8hacMXqBukqeXLTWic4nPmsmQxNjqbQHq4alRV0xq6c1Z14YvF6ix02r38nCsWwVCzo4dGZq06YtIP3z7p67PiRPub-O6j8l7GuwRqkOopnrrAsk_46l1uwa8ZSjn7qScG5-zw-RjD3sxo1Bhx1zlAtnLuuqw1zeZ6PN8ZQLbBb2qay0RBYFsPUYZF0Li4edPJWZdMl1v8vyJdOt77igsoMe-EY9q43OtvAk6OP9aIADprzTh8ULefPr_VCpFoSEcuqE1Pqzvgan9kgpa4z5ym6ZJBWDfUUdInnOWQpSpjHFKcPvO_HF-RJ0LWzVV2v9148LadNRv7-CwI3P8ojz1RVM2ww8_8HHryT-M-zd3T-nnfab88bw5tTk9Bm6nDTAAAl9XqpCesSgOIjx56xu6oRudLcP50r8PfcYXS2aPxe5VW9MLcy-qFP6TNWjcdXtj5e2hOYQEykgOTp8S2XJVvx5cexskU4sBSjNQQuZS-aAZHBJx-MnTJR9qMN9grnQL-i174gmPBmc_MHEBnBQMK7PPrsRwUSKluzYJP1k4vPwRiqcGGiAetTnlj9ge4kQKvIkwz5J3KcDqa-mJDgYmD1xkhp7ga6er4jxiL-tWIFj7pfjIeku-LFeOdf9MVmZHMDnFPO_fGPE4Z840nZe_DoHGhpQGdWQ50slZxPN8KWgOWOSp8_8iY6zKg9QQpHii227BuMCDKXGlu-UfqL9vD0Ka3EtyhmHund-fiFwg7FBw8ojtzm3GbTjpINxjjJyAP-gf4OzP1TqpQ628LLRTpgzRt7BM7tioUdF4M1YuYzghr7CqjzwOsEF8xpVOnUf7bPrgL-4FYkwTv_j8lEuaVavyeqSGYuM0wVksX3PTYbXcxJAGCyxNlgndNLgLVxPEeTw5XeQB37hPG1im29MzxMllYlGII7iBuFNeVkgdrkEF6ZZIDeWPDFKHKP89l8KpR38t28fSNt1C0AepBhaKOHMIE7f9ulKSdq72RuG1Em4Sv-gh8UWXiCDUrOSdmDffP0mQ36G4V8PrxFnUafuv5yO70WpFrDeLnFa7NbTR2Mpz1aLmhnKoRLkHgM2RML3NXvKb7Qm6bKjMBjCI_c2pcYc5ljmWk-4nalIuD_aj74Pa8gsln7cUvm7Y3C1y8oVtZhaUTcUg9ohYLAOuseFmGu4pkkk7jTcWvOxX5QyiSnkoE7VTQ4hxkJQyEfkODfB-Kb3rGHgYFuAIcXwPmEcxf2P9jVqEPaN6mHEykdCWRNn5i7wFB1wplAdyzvmdlIuw6jDtawiB9xCJKqQ16H4ilDlYf18gFKYtuaebC9VGQ7xVy7pvAmvJMiXVXCWcx3r5odqRAFG8fECq4_CiGJDUh'),
('admin', '1317086621', '805aa384c33b1a373071c125a548d803', 0, 62, 'Super Administrator', 25, 1, '83xc7z-uC2HAnPZQBusp6tcuCvcDXYP0WZYvGldrT6lnvLBF7CMxzHd1QVdDPE9hEogjsVdAfI1Qu741Mmlw4uR48XdwihN-_cl1we2_El8QYVre6ngOTfZ-8TjcGghjr3GMg1WmmgEaVNtQ7JAjDMszNvQgmuyNc1mdpBk-2WVazR8N_GQ18WCdkygAOYiR1aVRhGBhG2wFR8iAYn96pTffmAJD_lfts3Gdq8xJ7f-zUc-eL4Lshiorp-0-9-C-GYwwa6dJXAgwxUvkFeue5bKVEM8fOSkqEyHr5YZA1dl7-XM03udxyuCBlmX91bKN9PW1O4WQoCk8fF02Qhfw2n18KSw9rt_PN32xiN7OY5L13If7v-NQFhYCq-V9XP6klW2OsSXTa1Dmu2pM5EXs-e5Ti3plM2Cm0aHv16LZfvXrZYdLEqAwrR45z2r1v4m_0eGkX-hEwdQ-2d1s42kAqsJpSrXjcmLmqmRO_DbMVk0VboFgPFjhIv6vc0EqRKl59dP8wRmyh62r24yFhiKl-akUY0qxeg5XmteBp38t98o7X2Ixw-TJE-MN15Gn9Y5oUYDZl-CzbdZeWnu8HX-mwM0A1ZezO_9IVHxAKtwSuIhCWSEZPbGT-r50PwMhXHJwqQWqvDOhFPE2z0Qus7NK6L5x-iPE4fa4D_D3t_5xd7PERywwAj0uYBqMIUE8zA5xXiLdnSANygS51LtK641bE3OF30ZC5-0czWcirHG4SIyQv6OWl96uUlxZ_7r5Hu6DtW5Gc_slrQ6SmZ4SvPExvfXjG95I170XUBLeeX_rTXbbALGGWAqvhp_QOEmLT-8V6iteXAjIx133VD-56Wg39hydTfd6lY0AQbOcsU3ckNnNUQ3bjTqZ5QV87x4viT_0zUQjoQ-nIQlwSJtqkG6L7dqsP8uffKeaC8wIzmnf2sDxb6-NklYqSheIdQ27aiGU9vmAfNh0OL6qeQiTxipytUfF3R0mGKjID0wn6ppQAEJkULAi6cOCEXukpPqRpM-nSmD-5Pdml5NtKoDUl0kqtPhv0wm_sYI60d5ocHDtnSEJj8BHN3ZzlGSltEnqfLc5tLcnJQLZCDr_yclenx_0GgDwnVrvNmWp3w-AGD_C9Bu9CQ2Y0e0ORwPlcF2spTDBBJyXAV7touOUHdDqaZxbWluMAqCz6-aYviM35g_5WjxZTCdK4trb003frMBupy3wbVkQ6rmkKn6Dr6QLsObrEEIh9L74dscPMap4E_8aEmZOHXCpocpJvrTPKWA171LYN0jQP0hAmagkoRl-rB5M7IgyzW326rQMfc91nzuf_ubAZxemVKRuXIqdVKkS08i7Ol38AcZ7f9gz7tJ7vgvoJ8icZRLuIMd9L6SihsDezYEOSz4RQta1-3_YT1of99m_rmoDZiMZtFNoZOzyZk8vZIVx4ir1t9kOgUFAtSWNyhrWrLNlB8rxWJISZxhQd5QX7N2P00LrE68ahW-HhINmjoWPy9u2BqQtv5pVa8r2ycu2rNiWEEKLTcQWqkrtukM5QFpv5ofsLnZE59St0SuCszgbh9Li7jiTlT0YJtGq_U-ANhcYnHftloPTNUruTM65EzPKzNcf1UdfuQmPeE5DHTZtwJrXcCJJipfXIEWvnRhTI0ED27M1paU2_ZO8tqYLBvtSOmiHtk_U900uLcck4Fcilj2Zg1cvn2nUkTLYvcSuMwE5KMEhupCOIYhuIjV4NI3O_E2IbSJswmsKVGcKaRc5qO1v44KAOZ9ONCaIp2DO0IeH30g_u88hXsUMahIZk-VPxU0IFEt_xLnV8E9O10FTgmUHY694kDhGC5XZk9F0gI9k3CRTAGj-pFn5aTa_3izQDB9g6y1QxlEv5mBRP9Em9EVwcYj3zc2Va1bW6klpHvd2TQIDpMkzI_nPFbsZ8DdGfaJ3xBOYmsCbvwKYsfS42J5SPgVOoLW65nxKLfAJv6DfOP-hvhK4bDKLFqgWQvWb98xa89RjqDBzA_8wUqQ4zeDbhS28sKlRveBMiwssoEkjvixiRblVDCiq64_0QYHSo3njVUMBRbLJd8oSIdzSNJhhRhAXOZbhSX_IjRygEQHL_xUnVXMDqTjJoiXJfl2lV5nXVLSudQ3pPtiJxKUBjfdaBV2XXets32anKmr2sC7_egTWEJuoVclhrz1TkQ6H80OGtbnJRLVp06-M89V-LvQ7zP19JWYskGf8BAWfnBMk2WG82C2127QX4nSLol5vC3oTIfj-Fn1roY7Q0K1CZ20NYUU-45Sac8zsVFRFTgOQO-Yu2JTUY1Vi--4cCdaLD9RYNp8qHfM1e49ztZI0z4txqod1MTvia93t8OgZ8xcWthcQy5hG-rQyFrViekXuTQmrDK0j-5yseuy59Iv1RlWyBcDJmIV-uQq4Q7a1Nw5QAjFUJnXhmGJLF_JE2jNGk59LzlN0UtG2LduZULbfHD1MyTAE6CzWWvqhIHt72JIASivWdkmcFzTO-i1C06K_mpyGEdGpBx26nwS14S3PZ7Jgw7I53B8Fec9QOky29IORNaEg_o1J05DmJTyZH16pVXsPii7ecNPpr7q38X9yDDEd3qoouwvNzRMlqaw.'),
('', '1317086557', '58085fca5718beaba5ad0bd62d0f8a31', 1, 0, '', 0, 0, 't9TCd6g5ft4Rjn3Hrtopr-zaft1icg4TRsztJBHVGf-RxLwMSEClskqlNLhqioFfKhiJBGLAwAHkGIxFOMR6C0yHOr8HEXxt-Sxz4wxYYlbvpDVI44aZcY45DFcdVBOVR5XSg2G8ec7CJgjQcQ0r2YVnYKv7UE7Y38hUdkVNsSfNITS5Z0TeaLzIkDhTUFeqtbJ6dnE-5bH6LMGUVxWwxs3Bt3M2Y611MwatnTmkWYIDOw_Y4PG0Wzy6mUzESQa-T55Qpd2cLNjF_W273gyWvAlbM65_tZf3ahlzQzYEQljecCp0YP_uBMO-Etb4H51UiQgQl9LKdkJrdmykg6ORiO53Y3MtrhQWdx36boinZpcnrQp4DnYkA48OwT8v31jODPOJ3-EcGM1oe7MFwNUF5KheUcfGOAH8WvZC4vxERSH5m_YXev-aks6rVPG5LoCWA_c2T-B95SZVW3Vwhc1yvWh7x80NB2ei8SQFPITcgDbTsWR8TK2T2lFMgXCwvz13dnymgC01HOq_g2Z19Fjl9LIG8EIgKWcc0sWbtcC4S3jLt5LxG3KP_xrGSup8ScN-pLz_b4V84n8pyB_ECODGMOjGwu_FItcuLDnf2Nqhj7gu9aZQ5UJU37uJqRyhm3PHAzwGvbD0UICg1LB9-wTIBGZiyIW7akQtevXdI3dIGgGKbVOIHbCwwsdymuTFdk24ROys51Fc2jAMw1SxXTliZDIRnMsyLzZ6huU9Wael-kTkWrnf7to0q-stfqMU4UWqab-KoBSSqD_P3QUIGvSlo0CV_hi4enNHljpthpLvaPhVduIjacc9X2o0SA2nk9Q8zpxZZ3rLcGbHnpYOduFSLwL0Ib35amz-w28LuvhHb2fIc9_F32AwaLkpfyedn3QoOZltI7Nnj-bD4_3JVg8rGz5QC4RFJ0vDbMHujaJF_ThIPJjmv5dryL7YakZR6VziFeMWvQL1AAcB01R9OOtdvWX8zI_N1_UyGzKmfgG4oacePlXLwpimPFZStqD6ee7gHE5sB0oprA9GxyJHaGJTAp7jbsUW5GXyYOnK6yDvP2I8PDzZ7uT-XNYkX8UFHrpV2I9Pgl3jh8Jf_DnSGN9P-4RSdUBxQzDQ6ifI7MGhrrHmDz-CFbPW8pGeD4aYd6LQeYIwcfA4KA6Lt49Ui6iAqOGgZDtr-_Gb-Umo8r42XBy9L750qoDi6NKwiPNHTZ8_Co6mOZk12X1jXpBjPiADbXtHaKQKM7LL82UdAGce7ifIFB2XxziT262KYdfox_yXkwd9XPHm80oyTBFQHkz_aRExadYGTnNUt9K7oQEISJOGyzTtWepFhjlCS9vUCHwbyncU0Xk5iiFvj9D8UDWc8a3ul3kOpih4O3PgRyeNh20zRcqYRgYZE8wW92ipb_MYTIbpXF_LYW2fUD5oEft6rH5N7Cj_yfe_rGtpmEo3KdXmkzTSrExP3mj2Psb_xveJ7a7yPeSBXfIpMoNA1BCt490qHh7rNnqFRalz_CLUuPw7ZRvnYBWEeu31OFX0X3GGEODkk4PBr3ap2rGnYQ6oUGqVVFkoSKZX-kWuP17AWehsLBmrzO7JHoOliv3gWMOVb4c_wo3Nz8BKe_6NZbiqIWhwYRuI533wnqRm9PgnGSMsslxJHOBTndZX3ucMyXKhImFOysVjbH4nGSX12vGjUDNvOkF1lQVZvChPpWbN3U0.');

-- --------------------------------------------------------

--
-- Table structure for table `vea_stats_agents`
--

DROP TABLE IF EXISTS `vea_stats_agents`;
CREATE TABLE IF NOT EXISTS `vea_stats_agents` (
  `agent` varchar(255) NOT NULL default '',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vea_templates_menu`
--

DROP TABLE IF EXISTS `vea_templates_menu`;
CREATE TABLE IF NOT EXISTS `vea_templates_menu` (
  `template` varchar(255) NOT NULL default '',
  `menuid` int(11) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`menuid`,`client_id`,`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vea_templates_menu`
--

INSERT INTO `vea_templates_menu` (`template`, `menuid`, `client_id`) VALUES
('vealaweb', 0, 0),
('khepri', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vea_users`
--

DROP TABLE IF EXISTS `vea_users`;
CREATE TABLE IF NOT EXISTS `vea_users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `username` varchar(150) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `usertype` varchar(25) NOT NULL default '',
  `block` tinyint(4) NOT NULL default '0',
  `sendEmail` tinyint(4) default '0',
  `gid` tinyint(3) unsigned NOT NULL default '1',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL default '',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`),
  KEY `gid_block` (`gid`,`block`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;

--
-- Dumping data for table `vea_users`
--

INSERT INTO `vea_users` (`id`, `name`, `username`, `email`, `password`, `usertype`, `block`, `sendEmail`, `gid`, `registerDate`, `lastvisitDate`, `activation`, `params`) VALUES
(62, 'Administrator', 'admin', 'jtoirac@gmail.com', 'cbb8ce19d0dade1256b1e95e6d1fb096:82CCwUKgQQNWtyLsgpIdr3DLqvQqZBps', 'Super Administrator', 0, 1, 25, '2011-01-21 18:08:32', '2011-09-27 01:14:35', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `vea_weblinks`
--

DROP TABLE IF EXISTS `vea_weblinks`;
CREATE TABLE IF NOT EXISTS `vea_weblinks` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `url` varchar(250) NOT NULL default '',
  `description` text NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `archived` tinyint(1) NOT NULL default '0',
  `approved` tinyint(1) NOT NULL default '1',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`,`archived`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `vea_weblinks`
--

INSERT INTO `vea_weblinks` (`id`, `catid`, `sid`, `title`, `alias`, `url`, `description`, `date`, `hits`, `published`, `checked_out`, `checked_out_time`, `ordering`, `archived`, `approved`, `params`) VALUES
(1, 2, 0, 'Joomla!', 'joomla', 'http://www.joomla.org', 'Joomla!', '2005-02-14 15:19:02', 3, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 'target=0'),
(2, 2, 0, 'php.net', 'php', 'http://www.php.net', 'El lenguaje de programación en el que está escrito Joomla!', '2004-07-07 11:33:24', 6, 1, 0, '0000-00-00 00:00:00', 3, 0, 1, ''),
(3, 2, 0, 'MySQL', 'mysql', 'http://www.mysql.com', 'La base de datos que usa Joomla!', '2004-07-07 10:18:31', 1, 1, 0, '0000-00-00 00:00:00', 5, 0, 1, ''),
(4, 2, 0, 'OpenSourceMatters', 'opensourcematters', 'http://www.opensourcematters.org', 'OSM', '2005-02-14 15:19:02', 11, 1, 0, '0000-00-00 00:00:00', 2, 0, 1, 'target=0'),
(5, 2, 0, 'Joomla! - Foros', 'joomla-forums', 'http://forum.joomla.org', 'Foros de Joomla!', '2005-02-14 15:19:02', 4, 1, 0, '0000-00-00 00:00:00', 4, 0, 1, 'target=0'),
(6, 2, 0, 'Ohloh Tracking de Joomla!', 'ohloh-tracking-of-joomla', 'http://www.ohloh.net/projects/20', 'Informes objetivos sobre el desarrollo de Joomla. Joomla! tiene algunos desarrolladores estrella.', '2007-07-19 09:28:31', 1, 1, 0, '0000-00-00 00:00:00', 6, 0, 1, 'target=0\n\n');

-- --------------------------------------------------------

--
-- Table structure for table `vea_xmap`
--

DROP TABLE IF EXISTS `vea_xmap`;
CREATE TABLE IF NOT EXISTS `vea_xmap` (
  `name` varchar(30) NOT NULL,
  `value` varchar(100) default NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vea_xmap`
--

INSERT INTO `vea_xmap` (`name`, `value`) VALUES
('version', '1.2.10'),
('classname', 'sitemap'),
('expand_category', '1'),
('expand_section', '1'),
('show_menutitle', '1'),
('columns', '1'),
('exlinks', '1'),
('ext_image', 'img_grey.gif'),
('exclmenus', ''),
('includelink', '1'),
('sitemap_default', '1'),
('exclude_css', '0'),
('exclude_xsl', '0');

-- --------------------------------------------------------

--
-- Table structure for table `vea_xmap_ext`
--

DROP TABLE IF EXISTS `vea_xmap_ext`;
CREATE TABLE IF NOT EXISTS `vea_xmap_ext` (
  `id` int(11) NOT NULL auto_increment,
  `extension` varchar(100) NOT NULL,
  `published` int(1) default '0',
  `params` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `vea_xmap_ext`
--

INSERT INTO `vea_xmap_ext` (`id`, `extension`, `published`, `params`) VALUES
(1, 'com_agora', 1, '-1{include_forums=1\ninclude_topics=1\nmax_topics=\nmax_age=\ncat_priority=-1\ncat_changefreq=-1\nforum_priority=-1\nforum_changefreq=-1\ntopic_priority=-1\ntopic_changefreq=-1\n}'),
(2, 'com_contact', 1, '-1{include_contacts=1\nmax_contacts=\ncat_priority=-1\ncat_changefreq=-1\ncontact_priority=-1\ncontact_changefreq=-1\n}'),
(3, 'com_content', 1, '-1{expand_categories=1\nexpand_sections=1\narticles_order=menu\nadd_pagebreaks=1\nadd_images=0\nmax_images=1000\nshow_unauth=0\nmax_art=0\nmax_art_age=0\ncat_priority=-1\ncat_changefreq=-1\nart_priority=-1\nart_changefreq=-1\nkeywords=1\n}'),
(4, 'com_eventlist', 1, '-1{include_events=1\nmax_events=\nmax_age=\ncat_priority=-1\ncat_changefreq=-1\nfile_priority=-1\nfile_changefreq=-1\n}'),
(5, 'com_g2bridge', 1, '-1{include_items=2\ncat_priority=-1\ncat_changefreq=-1\nitem_priority=-1\nitem_changefreq=-1\n}'),
(6, 'com_glossary', 1, '-1{include_entries=1\nmax_entries=\nletter_priority=0.5\nletter_changefreq=weekly\nentry_priority=0.5\nentry_changefreq=weekly\n}'),
(7, 'com_hotproperty', 1, '-1{include_properties=1\ninclude_companies=1\ninclude_agents=1\nproperties_text=Properties\ncompanies_text=Companies\nagents_text=Agents\nmax_properties=\ntype_priority=-1\ntype_changefreq=-1\nproperty_priority=-1\nproperty_changefreq=-1\ncompany_priority=-1\ncompany_changefreq=-1\nagent_priority=-1\nagent_changefreq=-1\n}'),
(8, 'com_jcalpro', 1, '-1{include_events=1\ncat_priority=-1\ncat_changefreq=-1\nevent_priority=-1\nevent_changefreq=-1\n}'),
(9, 'com_jdownloads', 1, '-1{include_files=1\nmax_files=\nmax_age=\ncat_priority=-1\ncat_changefreq=-1\nfile_priority=-1\nfile_changefreq=-1\n}'),
(10, 'com_jevents', 1, '-1{include_events=1\nmax_events=\ncat_priority=0.5\ncat_changefreq=weekly\nevent_priority=0.5\nevent_changefreq=weekly\n}'),
(11, 'com_jmovies', 1, '-1{include_movies=1\nmax_movies=\nmax_age=\ncat_priority=-1\ncat_changefreq=-1\nfile_priority=-1\nfile_changefreq=-1\n}'),
(12, 'com_jomres', 1, '-1{priority=0.5\nchangefreq=weekly\n}'),
(13, 'com_joomdoc', 1, '-1{include_docs=1\ndoc_task=\ncat_priority=0.5\ncat_changefreq=weekly\ndoc_priority=0.5\ndoc_changefreq=weekly\n}'),
(14, 'com_joomgallery', 1, '-1{include_pictures=1\nmax_pictures=\ncat_priority=-1\ncat_changefreq=-1\npictures_priority=-1\npictures_changefreq=-1\n}'),
(15, 'com_kb', 1, '-1{include_articles=1\ninclude_feeds=1\nmax_articles=\nmax_age=\ncat_priority=-1\ncat_changefreq=-1\nfile_priority=-1\nfile_changefreq=-1\n}'),
(16, 'com_kunena', 1, '-1{include_topics=1\nmax_topics=\nmax_age=\ncat_priority=-1\ncat_changefreq=-1\ntopic_priority=-1\ntopic_changefreq=-1\n}'),
(17, 'com_mtree', 1, '-1{cats_order=cat_name\ninclude_links=1\nlinks_order=ordering\nmax_links=\nmax_age=\ncat_priority=0.5\ncat_changefreq=weekly\nlink_priority=0.5\nlink_changefreq=weekly\n}'),
(18, 'com_myblog', 1, '-1{include_bloggers=1\ninclude_tag_clouds=1\ninclude_feed=2\ninclude_archives=2\nnumber_of_bloggers=8\ninclude_blogger_posts=1\nnumber_of_post_per_blogger=32\ntext_bloggers=Bloggers\nblogger_priority=-1\nblogger_changefreq=-1\nfeed_priority=-1\nfeed_changefreq=-1\nentry_priority=-1\nentry_changefreq=-1\ncats_priority=-1\ncats_changefreq=-1\narc_priority=-1\narc_changefreq=-1\ntag_priority=-1\ntag_changefreq=-1\n}'),
(19, 'com_rapidrecipe', 1, '-1{cats_order=cat_name\ninclude_links=1\nlinks_order=ordering\nmax_links=\nmax_age=\ncat_priority=-1\ncat_changefreq=-1\nrecipe_priority=-1\nrecipe_changefreq=-1\n}'),
(20, 'com_remository', 1, '-1{include_files=1\nmax_files=\nmax_age=\ncat_priority=-1\ncat_changefreq=-1\nfile_priority=-1\nfile_changefreq=-1\n}'),
(21, 'com_resource', 1, '-1{include_articles=1\nmax_articles=\ncat_priority=-1\ncat_changefreq=-1\narticle_priority=-1\narticle_changefreq=-1\n}'),
(22, 'com_rdautos', 1, '-1{include_vehicles=1\ncat_priority=0.5\ncat_changefreq=weekly\nvehicle_priority=0.5\nvehicle_changefreq=weekly\n}'),
(23, 'com_rokdownloads', 1, '-1{include_files=1\nmax_files=\nmax_age=\ncat_priority=-1\ncat_changefreq=-1\nfile_priority=-1\nfile_changefreq=-1\n}'),
(24, 'com_rsgallery2', 1, '-1{include_images=1\nmax_images=\nmax_age=\nimages_order=orderding\ncat_priority=0.5\ncat_changefreq=weekly\nimage_priority=0.5\nimage_changefreq=weekly\n}'),
(25, 'com_sectionex', 1, '-1{expand_categories=1\nexpand_sections=1\nshow_unauth=0\ncat_priority=-1\ncat_changefreq=-1\nart_priority=-1\nart_changefreq=-1\n}'),
(26, 'com_cmsshopbuilder', 1, '-1{include_items=1\nmax_items=\nmax_age=\ncat_priority=-1\ncat_changefreq=-1\nitem_priority=-1\nitem_changefreq=-1\n}'),
(27, 'com_sobi2', 1, '-1{include_entries=1\nmax_entries=\nmax_age=\nentries_order=a.ordering\nentries_orderdir=DESC\ncat_priority=-1\ncat_changefreq=weekly\nentry_priority=-1\nentry_changefreq=weekly\n}'),
(28, 'com_virtuemart', 1, '-1{include_products=1\ninclude_product_images=0\nproduct_image_license_url=\ncat_priority=0.5\ncat_changefreq=weekly\nprod_priority=0.5\nprod_changefreq=weekly\n}'),
(29, 'com_weblinks', 1, '-1{include_links=1\nmax_links=\ncat_priority=-1\ncat_changefreq=-1\nlink_priority=-1\nlink_changefreq=-1\n}');

-- --------------------------------------------------------

--
-- Table structure for table `vea_xmap_items`
--

DROP TABLE IF EXISTS `vea_xmap_items`;
CREATE TABLE IF NOT EXISTS `vea_xmap_items` (
  `uid` varchar(100) NOT NULL,
  `itemid` int(11) NOT NULL,
  `view` varchar(10) NOT NULL,
  `sitemap_id` int(11) NOT NULL,
  `properties` varchar(300) default NULL,
  PRIMARY KEY  (`uid`,`itemid`,`view`,`sitemap_id`),
  KEY `uid` (`uid`,`itemid`),
  KEY `view` (`view`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vea_xmap_sitemap`
--

DROP TABLE IF EXISTS `vea_xmap_sitemap`;
CREATE TABLE IF NOT EXISTS `vea_xmap_sitemap` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `expand_category` int(11) default NULL,
  `expand_section` int(11) default NULL,
  `show_menutitle` int(11) default NULL,
  `columns` int(11) default NULL,
  `exlinks` int(11) default NULL,
  `ext_image` varchar(255) default NULL,
  `menus` text,
  `exclmenus` varchar(255) default NULL,
  `includelink` int(11) default NULL,
  `usecache` int(11) default NULL,
  `cachelifetime` int(11) default NULL,
  `classname` varchar(255) default NULL,
  `count_xml` int(11) default NULL,
  `count_html` int(11) default NULL,
  `views_xml` int(11) default NULL,
  `views_html` int(11) default NULL,
  `lastvisit_xml` int(11) default NULL,
  `lastvisit_html` int(11) default NULL,
  `excluded_items` text,
  `compress_xml` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `vea_xmap_sitemap`
--

INSERT INTO `vea_xmap_sitemap` (`id`, `name`, `expand_category`, `expand_section`, `show_menutitle`, `columns`, `exlinks`, `ext_image`, `menus`, `exclmenus`, `includelink`, `usecache`, `cachelifetime`, `classname`, `count_xml`, `count_html`, `views_xml`, `views_html`, `lastvisit_xml`, `lastvisit_html`, `excluded_items`, `compress_xml`) VALUES
(1, 'New Sitemap', 0, 0, 1, 1, 1, 'img_grey.gif', 'mainmenu,0,1,1,0.5,daily,mod_mainmenu\nrecursos,1,0,1,0.5,daily,mod_mainmenu', '', 1, 0, 15, 'xmap', 19, 3, 6, 7, 1297282148, 1297099392, '', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
