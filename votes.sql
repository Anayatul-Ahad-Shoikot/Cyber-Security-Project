-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 28, 2025 at 11:49 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `voting_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int NOT NULL,
  `nid_id` int NOT NULL,
  `ip_id` int NOT NULL,
  `team` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `votes`
--
DELIMITER $$
CREATE TRIGGER `votes_delete` AFTER DELETE ON `votes` FOR EACH ROW BEGIN
    INSERT INTO audit_log (table_name, operation, old_data, ip)
    VALUES ('votes', 'DELETE', CONCAT('nid=', OLD.nid, ', ip=', OLD.ip, ', team=', OLD.team), CURRENT_USER());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `votes_insert` AFTER INSERT ON `votes` FOR EACH ROW BEGIN
    INSERT INTO audit_log (table_name, operation, new_data, ip)
    VALUES ('votes', 'INSERT', CONCAT('nid=', NEW.nid, ', ip=', NEW.ip, ', team=', NEW.team), USER());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `votes_update` AFTER UPDATE ON `votes` FOR EACH ROW BEGIN
    INSERT INTO audit_log (table_name, operation, old_data, new_data, ip)
    VALUES ('votes', 'UPDATE', CONCAT('nid=', OLD.nid, ', ip=', OLD.ip, ', team=', OLD.team), CONCAT('nid=', NEW.nid, ', ip=', NEW.ip, ', team=', NEW.team), CURRENT_USER());
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
