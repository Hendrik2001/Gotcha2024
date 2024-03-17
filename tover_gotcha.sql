-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2018 at 01:14 AM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS ToverGotchaAdmin;
USE ToverGotchaAdmin;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tover_gotcha`
--

-- --------------------------------------------------------

--
-- Table structure for table `kills`
--

CREATE TABLE `kills` (
  `id` int(11) NOT NULL,
  `killer_id` int(11) NOT NULL,
  `deceased_id` int(11) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `kills`
--

INSERT INTO `kills` (`id`, `killer_id`, `deceased_id`, `time`) VALUES
(3, 1, 4, '2018-04-05 00:54:21'),
(4, 1, 3, '2018-04-05 00:55:31'),
(5, 1, 2, '2018-04-05 00:59:23'),
(6, 1, 30, '2018-04-05 01:00:52'),
(7, 1, 31, '2018-04-05 01:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `beer` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`beer`, `name`) VALUES
(200063, 'Pim de Vries'),
(6, 'Egbert Janssen');

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `beer` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `own_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_to_kill` int(11) DEFAULT NULL,
  `is_playing` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`id`, `beer`, `name`, `own_code`, `id_to_kill`, `is_playing`) VALUES
(1, 1, 'Jari', 'CLUB-PK-HOEK-TITS', 1, 1),
(2, 2, 'Jurgen', 'GM-LID-GAST-OOTJE', -1, 1),
(3, 3, 'Peter', 'PLOEG-PIANO-TITS', -1, 1),
(4, 4, 'Sharif', 'VO-KEI-CLUB-ADT', -1, 0),
(5, 200063, 'Pim de Vries', 'SUB3D-KNOR-BEER', -1, 0),
(31, 6, 'Egbert Janssen', 'STUCA-SFEER-KEI', -1, 1);



--
-- Indexes for dumped tables
--

--
-- Indexes for table `kills`
--
ALTER TABLE `kills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`beer`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `beer` (`beer`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kills`
--
ALTER TABLE `kills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
