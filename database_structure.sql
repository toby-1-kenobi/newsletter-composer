-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 21, 2014 at 04:10 PM
-- Server version: 5.5.35-0ubuntu0.13.10.2
-- PHP Version: 5.5.3-1ubuntu2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `newslett_composer`
--

-- --------------------------------------------------------

--
-- Table structure for table `ListRecipient`
--

CREATE TABLE IF NOT EXISTS `ListRecipient` (
  `list` int(10) unsigned NOT NULL,
  `email` varchar(200) NOT NULL,
  `user` int(10) unsigned NOT NULL,
  PRIMARY KEY (`list`,`email`),
  KEY `user` (`user`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELATIONS FOR TABLE `ListRecipient`:
--   `list`
--       `RecipientLists` -> `id`
--   `user`
--       `Recipients` -> `user`
--   `email`
--       `Recipients` -> `email`
--

-- --------------------------------------------------------

--
-- Table structure for table `MailAccounts`
--

CREATE TABLE IF NOT EXISTS `MailAccounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `label` varchar(100) NOT NULL,
  `server` varchar(100) NOT NULL,
  `port` int(5) unsigned NOT NULL,
  `username` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `MailAccounts`:
--   `user`
--       `Users` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `NewsletterFuture`
--

CREATE TABLE IF NOT EXISTS `NewsletterFuture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newsletter` int(10) unsigned NOT NULL,
  `content` longtext NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `newsletter` (`newsletter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `NewsletterFuture`:
--   `newsletter`
--       `Newsletters` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `NewsletterHistory`
--

CREATE TABLE IF NOT EXISTS `NewsletterHistory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newsletter` int(10) unsigned NOT NULL,
  `content` longtext NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `newsletter` (`newsletter`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=115 ;

--
-- RELATIONS FOR TABLE `NewsletterHistory`:
--   `newsletter`
--       `Newsletters` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `Newsletters`
--

CREATE TABLE IF NOT EXISTS `Newsletters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL DEFAULT '1',
  `name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `issue` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `content` longtext CHARACTER SET utf8 NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_newsletter` (`name`,`issue`,`user`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- RELATIONS FOR TABLE `Newsletters`:
--   `user`
--       `Users` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `NewsletterSaves`
--

CREATE TABLE IF NOT EXISTS `NewsletterSaves` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newsletter` int(10) unsigned NOT NULL,
  `content` longtext NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `newsletter` (`newsletter`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- RELATIONS FOR TABLE `NewsletterSaves`:
--   `newsletter`
--       `Newsletters` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `RecipientLists`
--

CREATE TABLE IF NOT EXISTS `RecipientLists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `last_selected` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `last_selected` (`last_selected`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `RecipientLists`:
--   `user`
--       `Users` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `Recipients`
--

CREATE TABLE IF NOT EXISTS `Recipients` (
  `email` varchar(200) NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`email`,`user`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELATIONS FOR TABLE `Recipients`:
--   `user`
--       `Users` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `Sends`
--

CREATE TABLE IF NOT EXISTS `Sends` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `file` varchar(200) NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `success` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1 = success, 0 = fail',
  PRIMARY KEY (`id`),
  KEY `recipient` (`email`,`user`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `Sends`:
--   `user`
--       `Users` -> `id`
--   `email`
--       `Recipients` -> `email`
--

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `password` char(32) CHARACTER SET utf8 NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ListRecipient`
--
ALTER TABLE `ListRecipient`
  ADD CONSTRAINT `ListRecipient_ibfk_3` FOREIGN KEY (`user`) REFERENCES `Recipients` (`user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ListRecipient_ibfk_1` FOREIGN KEY (`list`) REFERENCES `RecipientLists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ListRecipient_ibfk_2` FOREIGN KEY (`email`) REFERENCES `Recipients` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `MailAccounts`
--
ALTER TABLE `MailAccounts`
  ADD CONSTRAINT `MailAccounts_ibfk_1` FOREIGN KEY (`user`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `NewsletterFuture`
--
ALTER TABLE `NewsletterFuture`
  ADD CONSTRAINT `NewsletterFuture_ibfk_1` FOREIGN KEY (`newsletter`) REFERENCES `Newsletters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `NewsletterHistory`
--
ALTER TABLE `NewsletterHistory`
  ADD CONSTRAINT `NewsletterHistory_ibfk_1` FOREIGN KEY (`newsletter`) REFERENCES `Newsletters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Newsletters`
--
ALTER TABLE `Newsletters`
  ADD CONSTRAINT `Newsletters_ibfk_1` FOREIGN KEY (`user`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `NewsletterSaves`
--
ALTER TABLE `NewsletterSaves`
  ADD CONSTRAINT `NewsletterSaves_ibfk_1` FOREIGN KEY (`newsletter`) REFERENCES `Newsletters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `RecipientLists`
--
ALTER TABLE `RecipientLists`
  ADD CONSTRAINT `RecipientLists_ibfk_1` FOREIGN KEY (`user`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Recipients`
--
ALTER TABLE `Recipients`
  ADD CONSTRAINT `Recipients_ibfk_1` FOREIGN KEY (`user`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Sends`
--
ALTER TABLE `Sends`
  ADD CONSTRAINT `Sends_ibfk_2` FOREIGN KEY (`email`) REFERENCES `Recipients` (`email`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `Sends_ibfk_1` FOREIGN KEY (`user`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
