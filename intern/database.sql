SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `pewosa`
--
CREATE DATABASE `pewosa` DEFAULT CHARACTER SET utf8 COLLATE utf8_german2_ci;
USE `pewosa`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `customer`
--

CREATE TABLE IF NOT EXISTS `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` text COLLATE utf8_german2_ci,
  `lastname` text COLLATE utf8_german2_ci,
  `street` text COLLATE utf8_german2_ci,
  `streetnumber` text COLLATE utf8_german2_ci,
  `zipcode` smallint(5) DEFAULT NULL,
  `city` text COLLATE utf8_german2_ci,
  `email` text COLLATE utf8_german2_ci NOT NULL,
  `company` text COLLATE utf8_german2_ci,
  `phone` text COLLATE utf8_german2_ci,
  `cellphone` text COLLATE utf8_german2_ci,
  `birthdate` date DEFAULT NULL,
  `notes` text COLLATE utf8_german2_ci,
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedate` datetime NOT NULL,
  `updateuserid` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `deleteuserid` int(11) NOT NULL,
  `activationcode` int(11) NOT NULL,
  KEY `id` (`id`)
) ENGINE=Aria  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci PAGE_CHECKSUM=1 AUTO_INCREMENT=866 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `customerNewsletter`
--

CREATE TABLE IF NOT EXISTS `customerNewsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` text COLLATE utf8_german2_ci NOT NULL,
  `lastname` text COLLATE utf8_german2_ci NOT NULL,
  `email` text COLLATE utf8_german2_ci NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '-1' COMMENT 'falls fehlerhaft: 0=nicht anschreiben, 1=anschreiben; -1 registriert aber noch nicht bestätigt',
  `activationcode` int(11) NOT NULL COMMENT 'Aktivierungscode',
  PRIMARY KEY (`id`)
) ENGINE=Aria  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci PAGE_CHECKSUM=1 AUTO_INCREMENT=65 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `customerdistribution`
--

CREATE TABLE IF NOT EXISTS `customerdistribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer` int(11) NOT NULL,
  `distribution` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=Aria  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci PAGE_CHECKSUM=1 AUTO_INCREMENT=994 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `list`
--

CREATE TABLE IF NOT EXISTS `list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_german2_ci NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updateuserid` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `deleteuserid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `distributionname` (`name`)
) ENGINE=Aria  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci PAGE_CHECKSUM=1 AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `newsletter`
--

CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` text COLLATE utf8_german2_ci NOT NULL COMMENT 'betreff und titel in einem',
  `body` longblob NOT NULL,
  `sendstate` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '-1=nicht versendet, 1=versendet',
  `senddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=Aria  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci PAGE_CHECKSUM=1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `poll`
--

CREATE TABLE IF NOT EXISTS `poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) COLLATE utf8_german2_ci NOT NULL,
  `radios` tinyint(2) NOT NULL DEFAULT '0',
  `radios2` tinyint(2) NOT NULL DEFAULT '0',
  `radios3` tinyint(2) NOT NULL DEFAULT '0',
  `radios4` tinyint(2) NOT NULL DEFAULT '0',
  `radios5` tinyint(2) NOT NULL DEFAULT '0',
  `radios6` tinyint(2) NOT NULL DEFAULT '0',
  `radios7` tinyint(2) NOT NULL DEFAULT '0',
  `radios8` tinyint(2) NOT NULL DEFAULT '0',
  `radios9` tinyint(2) NOT NULL DEFAULT '0',
  `radios10` tinyint(2) NOT NULL DEFAULT '0',
  `radios11` tinyint(2) NOT NULL DEFAULT '0',
  `radios12` tinyint(2) NOT NULL DEFAULT '0',
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=Aria  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci PAGE_CHECKSUM=1 AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pressrelease`
--

CREATE TABLE IF NOT EXISTS `pressrelease` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` text COLLATE utf8_german2_ci,
  `body` text COLLATE utf8_german2_ci,
  `contact` text COLLATE utf8_german2_ci,
  `tags` text COLLATE utf8_german2_ci,
  `source` text COLLATE utf8_german2_ci,
  `senddate` datetime DEFAULT NULL,
  `sendnow` tinyint(1) NOT NULL,
  `sendstate` int(11) NOT NULL DEFAULT '0' COMMENT '0= Entwurf: -1=pending -2=sent -3=readyforgo',
  `confirmationid1` int(11) DEFAULT '-1' COMMENT 'Ansprechpartner 1, der PM freigegeben hat.',
  `confirmationid1bypressagent` int(11) DEFAULT NULL COMMENT 'Zeigt an, wenn Freigabe über von Presseverantwortlichem im Namen von Ansprechpartner erfolgt',
  `confirmationid2` int(11) DEFAULT '-1' COMMENT 'Ansprechpartner der PM als zweites freigegeben hat.',
  `confirmationid2bypressagent` int(11) DEFAULT NULL COMMENT 'Zeigt an, wenn Freigabe über von Presseverantwortlichem im Namen von Ansprechpartner erfolgt',
  `pressagentid` int(11) DEFAULT '-1',
  `sendagent` int(1) NOT NULL DEFAULT '-1',
  UNIQUE KEY `id` (`id`)
) ENGINE=Aria  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci PAGE_CHECKSUM=1 AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pressreleaseconnection`
--

CREATE TABLE IF NOT EXISTS `pressreleaseconnection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pressreleaseID` int(11) NOT NULL,
  `listID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `tagID` int(11) DEFAULT NULL,
  `customerID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=Aria  DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1 AUTO_INCREMENT=1196 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sending`
--

CREATE TABLE IF NOT EXISTS `sending` (
  `email_adress` text COLLATE utf8_german2_ci NOT NULL,
  `pressrelease_id` int(11) NOT NULL,
  `senddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=Aria DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci PAGE_CHECKSUM=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sorting`
--

CREATE TABLE IF NOT EXISTS `sorting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `menu_id` smallint(2) NOT NULL DEFAULT '1' COMMENT '1=PMs, 2=Adressaten, 3=Verteilerlisten, 4=User',
  `menu_point` smallint(2) NOT NULL DEFAULT '1' COMMENT 'zB 1=nach Vorname',
  `menu_direction` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'zB 0=A-Z, 1=Z-A',
  PRIMARY KEY (`id`)
) ENGINE=Aria  DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci PAGE_CHECKSUM=1 COMMENT='Um für jeden User individuelle Sortierfunktionen zu ermöglichen und zu speichern' AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `jobtitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` text COLLATE utf8_unicode_ci NOT NULL,
  `cellphone` text COLLATE utf8_unicode_ci NOT NULL,
  `distributor` tinyint(1) DEFAULT '-1',
  `pressagent` tinyint(1) DEFAULT '-1',
  `admin` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL,
  `deleteuserid` int(11) NOT NULL,
  `lastaction` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

