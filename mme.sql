-- MySQL dump 10.13  Distrib 5.5.22, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: mme_empty
-- ------------------------------------------------------
-- Server version	5.5.22-0ubuntu1

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
-- Table structure for table `collection`
--

DROP TABLE IF EXISTS `collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collection` (
  `collection_id` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL,
  `date_created` varchar(14) NOT NULL,
  `date_updated` varchar(14) NOT NULL,
  `date_accessioned` varchar(32) NOT NULL,
  `date_modified` varchar(32) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `id_local` varchar(32) NOT NULL,
  `id_purl` varchar(255) NOT NULL,
  `id_uri` varchar(255) NOT NULL,
  `name_primary` varchar(255) NOT NULL,
  `name_alternate` varchar(255) NOT NULL,
  `name_abbreviated` varchar(255) NOT NULL,
  `description_brief` text NOT NULL,
  `description_full` text NOT NULL,
  `description_significance` text NOT NULL,
  `description_rights` text NOT NULL,
  `description_access` text NOT NULL,
  `description_note` text NOT NULL,
  `index_description` text NOT NULL,
  `index_meta` text NOT NULL,
  PRIMARY KEY (`collection_id`),
  KEY `party_id` (`user_id`),
  KEY `name_primary` (`name_primary`),
  KEY `date_updated` (`date_updated`,`date_accessioned`,`date_modified`),
  KEY `date_created` (`date_created`),
  FULLTEXT KEY `name_primary_2` (`name_primary`,`name_alternate`,`name_abbreviated`,`index_description`,`index_meta`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collection`
--

LOCK TABLES `collection` WRITE;
/*!40000 ALTER TABLE `collection` DISABLE KEYS */;
/*!40000 ALTER TABLE `collection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collection_coverage`
--

DROP TABLE IF EXISTS `collection_coverage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collection_coverage` (
  `collection_id` int(11) NOT NULL,
  `mode` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `value` varchar(255) NOT NULL,
  KEY `collection_id` (`collection_id`,`type`),
  KEY `mode` (`mode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collection_coverage`
--

LOCK TABLES `collection_coverage` WRITE;
/*!40000 ALTER TABLE `collection_coverage` DISABLE KEYS */;
/*!40000 ALTER TABLE `collection_coverage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collection_related`
--

DROP TABLE IF EXISTS `collection_related`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collection_related` (
  `collection_id` int(10) unsigned NOT NULL,
  `key` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  KEY `collection_id` (`collection_id`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collection_related`
--

LOCK TABLES `collection_related` WRITE;
/*!40000 ALTER TABLE `collection_related` DISABLE KEYS */;
/*!40000 ALTER TABLE `collection_related` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collection_subject`
--

DROP TABLE IF EXISTS `collection_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collection_subject` (
  `collection_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `type` varchar(32) NOT NULL,
  KEY `collection_id` (`collection_id`,`subject_id`,`type`),
  KEY `subject_id` (`subject_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collection_subject`
--

LOCK TABLES `collection_subject` WRITE;
/*!40000 ALTER TABLE `collection_subject` DISABLE KEYS */;
/*!40000 ALTER TABLE `collection_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `session_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `cookie` varchar(32) NOT NULL DEFAULT '',
  `remember` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `logged_in` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `last_activity` int(11) unsigned NOT NULL DEFAULT '0',
  `last_check` int(11) unsigned NOT NULL DEFAULT '0',
  `remote_addr` varchar(16) NOT NULL DEFAULT '',
  `visit_count` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cookie`),
  KEY `user_id` (`user_id`),
  KEY `session_id` (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subject` (
  `subject_id` int(10) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  PRIMARY KEY (`subject_id`),
  KEY `subject` (`subject`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject`
--

LOCK TABLES `subject` WRITE;
/*!40000 ALTER TABLE `subject` DISABLE KEYS */;
/*!40000 ALTER TABLE `subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unique_id`
--

DROP TABLE IF EXISTS `unique_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unique_id` (
  `name` varchar(32) NOT NULL DEFAULT '',
  `id_value` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unique_id`
--

LOCK TABLES `unique_id` WRITE;
/*!40000 ALTER TABLE `unique_id` DISABLE KEYS */;
INSERT INTO `unique_id` VALUES ('session',1000),('collection',1000),('party',1000),('subject',1000),('user',1000);
/*!40000 ALTER TABLE `unique_id` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(10) unsigned NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `email_contact` varchar(128) NOT NULL,
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  `slug` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_alternate` varchar(255) NOT NULL,
  `title_abbreviated` varchar(255) NOT NULL,
  `national` tinyint(4) NOT NULL DEFAULT '0',
  `oai_url` varchar(255) NOT NULL,
  `oai_harvest_auto` tinyint(4) NOT NULL,
  `oai_key` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `address_street` varchar(255) NOT NULL,
  `address_city` varchar(255) NOT NULL,
  `address_state` varchar(255) NOT NULL,
  `address_postcode` varchar(255) NOT NULL,
  `address_country` varchar(255) NOT NULL,
  `postal_street` varchar(255) NOT NULL,
  `postal_city` varchar(255) NOT NULL,
  `postal_state` varchar(255) NOT NULL,
  `postal_postcode` varchar(255) NOT NULL,
  `postal_country` varchar(255) NOT NULL,
  `phone` varchar(64) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `title` (`title`),
  KEY `slug` (`slug`),
  KEY `username` (`username`),
  KEY `password` (`password`),
  KEY `priv_admin` (`admin`),
  KEY `oai_harvest_auto` (`oai_harvest_auto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1132,'admin','21232f297a57a5a743894a0e4a801fc3','email@example.com','email@example.com',1,'admin','Admin','','ADMIN',0,'',0,'','','','','','','','','','','','','','');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-05-26 17:00:37
