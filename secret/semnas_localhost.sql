-- Adminer 4.7.8 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `semnas_presence`;
CREATE TABLE `semnas_presence` (
  `sso_id` char(32) NOT NULL,
  `session_id` int(11) NOT NULL,
  `imp_msg` text NOT NULL,
  `suggestions` text NOT NULL,
  `speaker_req` text NOT NULL,
  PRIMARY KEY (`sso_id`,`session_id`),
  KEY `session_id` (`session_id`),
  KEY `sso_id` (`sso_id`),
  CONSTRAINT `semnas_presence_ibfk_1` FOREIGN KEY (`sso_id`) REFERENCES `semnas_users` (`sso_id`),
  CONSTRAINT `semnas_presence_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `semnas_sessions` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `semnas_sessions`;
CREATE TABLE `semnas_sessions` (
  `session_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `announcements` text NOT NULL,
  `time_open` bigint(20) NOT NULL,
  `time_close` bigint(20) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `semnas_users`;
CREATE TABLE `semnas_users` (
  `sso_id` char(32) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(254) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `institution` varchar(100) NOT NULL,
  PRIMARY KEY (`sso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2021-10-20 05:45:39
