-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Host: custsql-ipg47.eigbox.net
-- Generation Time: Mar 21, 2014 at 02:16 PM
-- Server version: 5.5.32
-- PHP Version: 4.4.9
-- 
-- Database: `newslett_composer`
-- 
CREATE DATABASE `newslett_composer` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `newslett_composer`;

-- --------------------------------------------------------

-- 
-- Table structure for table `NewsletterFuture`
-- 

CREATE TABLE `NewsletterFuture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newsletter` int(10) unsigned NOT NULL,
  `content` longtext NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `newsletter` (`newsletter`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `NewsletterHistory`
-- 

CREATE TABLE `NewsletterHistory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newsletter` int(10) unsigned NOT NULL,
  `content` longtext NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `newsletter` (`newsletter`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `NewsletterSaves`
-- 

CREATE TABLE `NewsletterSaves` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newsletter` int(10) unsigned NOT NULL,
  `content` longtext NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `newsletter` (`newsletter`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `Newsletters`
-- 

CREATE TABLE `Newsletters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL DEFAULT '1',
  `name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `issue` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `content` longtext CHARACTER SET utf8 NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_newsletter` (`name`,`issue`,`user`),
  KEY `user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `RecipientLists`
-- 

CREATE TABLE `RecipientLists` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `content` longtext NOT NULL,
  `current` tinyint(1) NOT NULL,
  `user` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `Users`
-- 

CREATE TABLE `Users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `password` char(32) CHARACTER SET utf8 NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
