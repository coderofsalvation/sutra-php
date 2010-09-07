-- MySQL dump 10.11
--
-- Host: localhost    Database: sutra_ezloopaz
-- ------------------------------------------------------
-- Server version	5.0.51a-3ubuntu5.4

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
-- Table structure for table `sutra_item`
--

DROP TABLE IF EXISTS `sutra_item`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sutra_item` (
  `id` int(6) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `item_category_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `sutra_item`
--

LOCK TABLES `sutra_item` WRITE;
/*!40000 ALTER TABLE `sutra_item` DISABLE KEYS */;
INSERT INTO `sutra_item` VALUES (27,'item 1',27),(28,'item 2',27);
/*!40000 ALTER TABLE `sutra_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sutra_item_category`
--

DROP TABLE IF EXISTS `sutra_item_category`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sutra_item_category` (
  `id` int(6) NOT NULL auto_increment,
  `name` varchar(16) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `sutra_item_category`
--

LOCK TABLES `sutra_item_category` WRITE;
/*!40000 ALTER TABLE `sutra_item_category` DISABLE KEYS */;
INSERT INTO `sutra_item_category` VALUES (27,'category 1'),(28,'category 2');
/*!40000 ALTER TABLE `sutra_item_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sutra_page`
--

DROP TABLE IF EXISTS `sutra_page`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sutra_page` (
  `id` int(6) NOT NULL auto_increment,
  `parent_id` int(6) NOT NULL,
  `weight` int(11) NOT NULL,
  `visible` tinyint(1) default '1',
  `date` date default NULL,
  `title_url` varchar(255) default NULL,
  `title_url_path` varchar(255) default NULL,
  `title` varchar(255) NOT NULL default '',
  `title_menu` varchar(255) NOT NULL default '',
  `meta_keywords` varchar(255) NOT NULL default '',
  `meta_description` varchar(255) NOT NULL default '',
  `type` text NOT NULL,
  `tpl_master` varchar(255) default NULL,
  `yaml` text,
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `sutra_page`
--

LOCK TABLES `sutra_page` WRITE;
/*!40000 ALTER TABLE `sutra_page` DISABLE KEYS */;
INSERT INTO `sutra_page` VALUES (1,0,0,1,NULL,'website-pages','/','','Website pages','Enter default meta keywords here','Enter default meta description here','root','index.tpl','---\n',0),(2,0,1,1,NULL,'other','/other','','Other','','','root','index.tpl','',0),(3,2,0,1,NULL,'blocks','/other/blocks','','Blocks','','','root','index.tpl','',0),(4,2,0,1,NULL,'modules','/other/modules','','Modules','','','root','index.tpl','',0),(35,1,4,1,'0000-00-00','home','/home','home','home','Enter default meta keywords here','Enter default meta description here','normal','index.tpl','---\n',1);
/*!40000 ALTER TABLE `sutra_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sutra_page_comment`
--

DROP TABLE IF EXISTS `sutra_page_comment`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sutra_page_comment` (
  `id` int(11) NOT NULL auto_increment,
  `author` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `html` text NOT NULL,
  `page_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `sutra_page_comment`
--

LOCK TABLES `sutra_page_comment` WRITE;
/*!40000 ALTER TABLE `sutra_page_comment` DISABLE KEYS */;
INSERT INTO `sutra_page_comment` VALUES (4,'ikke','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(5,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(6,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(7,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(8,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(9,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(10,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(11,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(12,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(13,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(14,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34),(15,'John doe','2010-01-01','your@email.com','http://www.google.com','Lorem ipsum',34);
/*!40000 ALTER TABLE `sutra_page_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sutra_user`
--

DROP TABLE IF EXISTS `sutra_user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sutra_user` (
  `id` int(6) NOT NULL auto_increment,
  `username` varchar(16) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `surname` varchar(10) NOT NULL,
  `lastname` varchar(25) NOT NULL,
  `group` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `sutra_user`
--

LOCK TABLES `sutra_user` WRITE;
/*!40000 ALTER TABLE `sutra_user` DISABLE KEYS */;
INSERT INTO `sutra_user` VALUES (1,'root','63a9f0ea7bb98050796b649e85481845','Root','','','root'),(2,'admin','21232f297a57a5a743894a0e4a801fc3','administrator','','','admin'),(3,'member','aa08769cdcb26674c6706093503ff0a3','member','','','member');
/*!40000 ALTER TABLE `sutra_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-07-30 15:38:58
