-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 24, 2011 at 05:06 PM
-- Server version: 5.1.52
-- PHP Version: 5.3.3-pl3-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `db_jeff`
--

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `label` varchar(10) NOT NULL,
  `language` varchar(50) NOT NULL DEFAULT '',
  `code` varchar(5) NOT NULL DEFAULT '',
  `main` int(1) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `label`, `language`, `code`, `main`, `active`) VALUES
(1, 'I', 'italiano', 'it_IT', 1, 1),
(2, 'GB', 'english', 'en_EN', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `parent` int(4) DEFAULT NULL,
  `label` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` int(3) NOT NULL,
  `groups` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `parent`, `label`, `url`, `target`, `position`, `groups`) VALUES
(1, 0, 'Home', '/', '_self', 1, NULL),
(2, 0, 'Home Admin', '/admin/', '_self', 2, '1,2,3');

-- --------------------------------------------------------

--
-- Table structure for table `sys_datetime_settings`
--

CREATE TABLE IF NOT EXISTS `sys_datetime_settings` (
  `id` int(1) NOT NULL,
  `timezone` varchar(64) NOT NULL,
  `date_format` varchar(64) NOT NULL,
  `time_format` varchar(64) NOT NULL,
  `datetime_format` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sys_datetime_settings`
--

INSERT INTO `sys_datetime_settings` (`id`, `timezone`, `date_format`, `time_format`, `datetime_format`) VALUES
(1, 'Europe/Rome', '%d/%m/%Y', '%H:%i:%s', '%d/%m/%Y ore %H:%i:%s');

-- --------------------------------------------------------

--
-- Table structure for table `sys_groups`
--

CREATE TABLE IF NOT EXISTS `sys_groups` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `label` varchar(32) NOT NULL,
  `description` text,
  `privileges` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `sys_groups`
--

INSERT INTO `sys_groups` (`id`, `label`, `description`, `privileges`) VALUES
(1, 'SysAdmin', 'System administrator group. Holds all privileges', '1'),
(2, 'Admin', 'System group to be intended with administrative tasks', '2,3,5'),
(3, 'Power', 'System group to be intended with advanced functionalities', ''),
(4, 'User', 'System gorup to be intended with basic functionalities', ''),
(5, 'Guest', 'System group associated to the non authenticated user', '3');

-- --------------------------------------------------------

--
-- Table structure for table `sys_privileges`
--

CREATE TABLE IF NOT EXISTS `sys_privileges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(128) NOT NULL,
  `class` varchar(64) NOT NULL,
  `class_id` int(8) NOT NULL,
  `label` varchar(128) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `sys_privileges`
--

INSERT INTO `sys_privileges` (`id`, `category`, `class`, `class_id`, `label`, `description`) VALUES
(1, 'General', 'main', 1, 'Total administration', 'Complete webapplication administration, without limitations'),
(2, 'General', 'main', 2, 'Access to administrative area', 'Access to the system administrative area'),
(3, 'General', 'main', 3, 'Visualization public contents', 'Access to all resources which do not have specific access privileges'),
(4, 'General', 'main', 4, 'Visualization private contents', 'Access to all private resources which do not have specific access privileges'),
(5, 'Preferences', 'siteSettings', 1, 'Site preferences administration', 'Editing of the base preferences of the web application'),
(6, 'Preferences', 'datetimeSettings', 1, 'Date and time preferences administration', 'Editing of the date and time format preferences'),
(7, 'Users', 'user', 1, 'Users administration', 'Insertion, modification and deletion of user accounts'),
(8, 'Users', 'group', 1, 'Groups administration', 'Insertion, modification and deletion of groups and privilege associations'),
(9, 'Users', 'privileges', 1, 'Privilege visualization', 'Visualization and description of the sytem privileges that may be associated to system groups'),
(10, 'Languages', 'language', 1, 'Languages administration', 'Insertion, modification and deletion of the languages supported by the system'),
(11, 'Layout', 'layout', 1, 'Themes administration', 'Administration of the themes installed in the system. Selection of the current used theme'),
(12, 'Menu', 'menu', 1, 'Menu administration', 'Insertion, modification and deletion of menu voices');

-- --------------------------------------------------------

--
-- Table structure for table `sys_site_settings`
--

CREATE TABLE IF NOT EXISTS `sys_site_settings` (
  `id` int(1) NOT NULL,
  `app_title` varchar(64) NOT NULL,
  `app_description` text NOT NULL,
  `app_keywords` varchar(256) NOT NULL,
  `session_timeout` int(8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sys_site_settings`
--

INSERT INTO `sys_site_settings` (`id`, `app_title`, `app_description`, `app_keywords`) VALUES
(1, 'JEFF framework', 'A PHP framework', 'PHP,framework,LAMP,CMS');

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `themes`
--

INSERT INTO `themes` (`id`, `name`, `active`) VALUES
(1, 'default', 1),
(2, 'white', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL,
  `groups` varchar(64) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `lastname`, `firstname`, `username`, `password`, `groups`, `active`) VALUES
(1, 'Administrator', 'System', 'admin', '21232f297a57a5a743894a0e4a801fc3', '1', '1');

