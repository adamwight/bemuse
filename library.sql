-- MySQL dump 10.11
--
-- Host: localhost    Database: library
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
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `n` int(10) unsigned NOT NULL auto_increment,
  `ib5` varchar(5) default NULL,
  `isbn` varchar(10) default NULL,
  `ean` varchar(13) default NULL,
  `title` varchar(255) default NULL,
  `title_full` varchar(255) default NULL,
  `title_sort` varchar(255) default NULL,
  `subtitle` varchar(255) default NULL,
  `responsible` varchar(255) default NULL,
  `author` varchar(255) default NULL,
  `author_dates` varchar(32) default NULL,
  `fuller_name` varchar(255) default NULL,
  `call_h` varchar(127) default NULL,
  `call_dewey` varchar(64) default NULL,
  `d_class_number` varchar(64) default NULL,
  `d_item_number` varchar(64) default NULL,
  `call_lc` varchar(128) default NULL,
  `lc_class_number` varchar(64) default NULL,
  `lc_item_number` varchar(64) default NULL,
  `lccn` varchar(16) default NULL,
  `pub_place` varchar(64) default NULL,
  `pub_name` varchar(64) default NULL,
  `pub_dates` varchar(32) default NULL,
  `extent` varchar(32) default NULL,
  `dimensions` varchar(32) default NULL,
  `other_phys` varchar(32) default NULL,
  `language` char(3) default NULL,
  `source_id` int(10) unsigned default NULL,
  `source_query_id` int(10) unsigned default NULL,
  `score_match` int(11) default NULL,
  `score_complete` int(11) default NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modified` tinyint(3) unsigned NOT NULL default '0',
  `informed` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`n`),
  KEY `isbn` (`isbn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `call_h`
--

CREATE TABLE `call_h` (
  `n` int(10) unsigned NOT NULL auto_increment,
  `call_h` varchar(255) NOT NULL default '',
  `call_lc` varchar(255) default NULL,
  `call_dewey` varchar(255) default NULL,
  PRIMARY KEY  (`n`),
  UNIQUE KEY `call_h` (`call_h`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `call_h_heading`
--

CREATE TABLE `call_h_heading` (
  `n` int(10) unsigned NOT NULL auto_increment,
  `abbrev` char(3) default NULL,
  `name` varchar(255) default NULL,
  `author` varchar(255) default NULL,
  `heading_lc` varchar(63) default NULL,
  `heading_dewey` varchar(63) default NULL,
  PRIMARY KEY  (`n`),
  KEY `abbrev` (`abbrev`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `n` int(10) unsigned NOT NULL auto_increment,
  `book_id` int(11) default NULL,
  `location_id` int(11) default NULL,
  `instrument_id` int(10) unsigned default NULL,
  `mime` varchar(255) default NULL,
  `attr` varchar(64) default NULL,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `data` mediumblob NOT NULL,
  PRIMARY KEY  (`n`),
  KEY `book_id` (`book_id`),
  KEY `location_id` (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `instrument`
--

CREATE TABLE `instrument` (
  `n` int(10) unsigned NOT NULL auto_increment,
  `location_id` int(10) unsigned NOT NULL default '0',
  `home_location` int(10) unsigned NOT NULL default '0',
  `reference` tinyint(3) unsigned default '0',
  `sublocation` varchar(63) default NULL,
  `type` varchar(255) default NULL,
  `brand` varchar(255) default NULL,
  `year` varchar(32) default NULL,
  `comment` text,
  PRIMARY KEY  (`n`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `n` int(10) unsigned NOT NULL auto_increment,
  `city` varchar(128) default NULL,
  `description` varchar(255) default NULL,
  `librarian` varchar(255) default NULL,
  `contact` varchar(128) default NULL,
  `show_help` tinyint(4) default '1',
  `abbrev` char(3) default NULL,
  PRIMARY KEY  (`n`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `note`
--

CREATE TABLE `note` (
  `n` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(32) default NULL,
  `contents` text,
  PRIMARY KEY  (`n`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `note_link`
--

CREATE TABLE `note_link` (
  `book_id` int(10) unsigned NOT NULL default '0',
  `note_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`book_id`,`note_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `physical`
--

CREATE TABLE `physical` (
  `physical_id` int(10) unsigned NOT NULL auto_increment,
  `book_id` int(10) unsigned NOT NULL default '0',
  `location_id` int(10) unsigned NOT NULL default '0',
  `home_location` int(10) unsigned NOT NULL default '0',
  `sublocation` varchar(63) default NULL,
  `publicity` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`physical_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `query`
--

CREATE TABLE `query` (
  `query_id` int(10) unsigned NOT NULL auto_increment,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `source_book_id` int(10) unsigned NOT NULL default '0',
  `is_complete` tinyint(4) default '0',
  `error` varchar(255) default NULL,
  PRIMARY KEY  (`query_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `result`
--

CREATE TABLE `result` (
  `result_id` int(10) unsigned NOT NULL default '0',
  `result_batch_id` int(10) unsigned default NULL,
  `query_id` int(10) unsigned default NULL,
  `book_id` int(10) unsigned default NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `is_viewed` tinyint(1) default '0',
  `is_complete` tinyint(4) default '0',
  PRIMARY KEY  (`result_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `source`
--

CREATE TABLE `source` (
  `n` int(10) unsigned NOT NULL auto_increment,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`n`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `n` int(10) unsigned NOT NULL auto_increment,
  `topic` varchar(255) default NULL,
  `subordinate` varchar(255) default NULL,
  `location` varchar(255) default NULL,
  `date` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `affiliation` varchar(255) default NULL,
  `form` varchar(255) default NULL,
  `general` varchar(255) default NULL,
  `chronological` varchar(255) default NULL,
  `geographic` varchar(255) default NULL,
  PRIMARY KEY  (`n`),
  KEY `topic` (`topic`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `subject_link`
--

CREATE TABLE `subject_link` (
  `book_id` int(10) unsigned NOT NULL default '0',
  `subject_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`book_id`,`subject_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `wanted`
--

CREATE TABLE `wanted` (
  `book_id` int(10) unsigned NOT NULL default '0',
  `librarian_id` int(10) unsigned default NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `z_server`
--

CREATE TABLE `z_server` (
  `n` int(10) unsigned NOT NULL auto_increment,
  `host` varchar(255) NOT NULL default '',
  `port` int(11) default NULL,
  `db` varchar(255) default NULL,
  `enabled` tinyint(4) default '1',
  PRIMARY KEY  (`n`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-07-17  8:34:46
