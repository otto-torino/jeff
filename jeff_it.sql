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
(1, 'SysAdmin', 'Gruppo dell''amministratore del sistema, possiede tutti i privilegi', '1'),
(2, 'Admin', 'Gruppo di sistema inteso come amministratore', '2,3,5'),
(3, 'Power', 'Gruppo di sistema inteso com utente con funzionalità avanzate', ''),
(4, 'User', 'Gruppo di sistema inteso come utente base', ''),
(5, 'Guest', 'Gruppo di sistema associato all''utente non autenticato', '3');

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
(1, 'Generali', 'main', 1, 'Amministrazione totale', 'Amministrazione completa del sito, senza limitazioni'),
(2, 'Generali', 'main', 2, 'Accesso area amministrativa', 'Accesso all''area amministrativa del sistema'),
(3, 'Generali', 'main', 3, 'Visualizzazione parti pubbliche', 'Accesso a tutte le risorse che non presentano forme di controllo proprie'),
(4, 'Generali', 'main', 4, 'Visualizzazione parti private', 'Accesso a tutte le risorse private che non presentano forme di controllo proprie'),
(5, 'Preferenze', 'siteSettings', 1, 'Amministrazione opzioni sito', 'Modifica delle impostazioni di base del sito (app name, app description,...)'),
(6, 'Preferenze', 'datetimeSettings', 1, 'Amministrazione opzioni date e ora', 'Modifica delle impostazioni di formato data e ora'),
(7, 'Utenze', 'user', 1, 'Amministrazione utenti', 'Inserimento, modifica ed eliminazione di account utente'),
(8, 'Utenze', 'group', 1, 'Amministrazione gruppi', 'Inserimento, modifica, eliminazione di gruppi e associazione a privilegi'),
(9, 'Utenze', 'privileges', 1, 'Visualizzazione privilegi', 'Visualizza elenco di privilegi associabili ai gruppi con relativa descrizione'),
(10, 'Lingue', 'language', 1, 'Amministrazione lingue', 'inserimento modifica ed eliminazione delle lingue gestite dal sistema'),
(11, 'Layout', 'layout', 1, 'Gestione temi', 'Gestione temi installati sul sistema, modifica tema corrente'),
(12, 'Menu', 'menu', 1, 'Gestione menu', 'Inserimento modifica ed eliminazione delle voci di menu');

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
(1, 'Amministratore', 'Utente', 'admin', '21232f297a57a5a743894a0e4a801fc3', '1', '1');

