-- phpMyAdmin SQL Dump
-- version 4.1.13
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生時間： 2017 年 04 月 07 日 02:00
-- 伺服器版本: 5.5.37-0ubuntu0.14.04.1
-- PHP 版本： 5.5.9-1ubuntu4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫： `youtube9loop`
--
CREATE DATABASE IF NOT EXISTS `youtube9loop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `youtube9loop`;

-- --------------------------------------------------------

--
-- 資料表結構 `video_count`
--

DROP TABLE IF EXISTS `video_count`;
CREATE TABLE IF NOT EXISTS `video_count` (
  `date` date NOT NULL,
  `continent_name` varchar(31) NOT NULL,
  `country_name` varchar(127) NOT NULL,
  `city_name` varchar(127) NOT NULL,
  `youtube_id` varchar(31) NOT NULL,
  `video_count` int(11) NOT NULL,
  PRIMARY KEY (`date`,`continent_name`,`country_name`,`youtube_id`,`city_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 資料表結構 `video_date`
--

DROP TABLE IF EXISTS `video_date`;
CREATE TABLE IF NOT EXISTS `video_date` (
  `youtube_id` varchar(31) NOT NULL,
  `first_view_date` datetime NOT NULL,
  `last_view_date` datetime NOT NULL,
  PRIMARY KEY (`youtube_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
