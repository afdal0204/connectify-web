-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2025 at 04:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `connectify-web`
--

-- --------------------------------------------------------

--
-- Table structure for table `model_members`
--

CREATE TABLE `model_members` (
  `id` int(11) NOT NULL,
  `model_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `model_members`
--

INSERT INTO `model_members` (`id`, `model_id`, `member_id`) VALUES
(1, 10, 77),
(2, 11, 99),
(3, 12, 80),
(4, 13, 59),
(5, 14, 58),
(7, 17, 62),
(8, 18, 72),
(9, 19, 74),
(10, 20, 68),
(11, 21, 135),
(12, 22, 67),
(13, 23, 135),
(14, 24, 146),
(16, 28, 131),
(17, 29, 131),
(18, 30, 152),
(19, 31, 140),
(20, 32, 121),
(21, 33, 152),
(22, 34, 152),
(23, 35, 146),
(24, 38, 113),
(25, 39, 108),
(26, 40, 130),
(27, 41, 109),
(28, 42, 117),
(29, 43, 112),
(31, 49, NULL),
(32, 10, 91),
(33, 10, 83),
(34, 10, 88),
(35, 10, 85),
(36, 11, 84),
(37, 11, 94),
(38, 12, 103),
(39, 12, 66),
(40, 13, 66),
(41, 14, 64),
(43, 17, 75),
(44, 18, 70),
(45, 19, 60),
(46, 20, 69),
(47, 20, 125),
(48, 21, 111),
(49, 21, NULL),
(50, 22, 76),
(51, 22, 71),
(52, 49, 140),
(53, 22, 63),
(54, 23, 111),
(55, 24, 127),
(56, 28, 147),
(57, 28, 113),
(58, 29, 147),
(59, 29, 113),
(60, 30, 120),
(61, 31, 112),
(62, 31, 120),
(63, 32, 134),
(64, 32, 116),
(65, 32, 118),
(66, 32, 131),
(67, 33, 120),
(68, 34, 120),
(69, 35, 127),
(70, 39, 126),
(71, 39, 110),
(72, 40, 114),
(73, 40, 109),
(74, 40, 123),
(75, 41, 123),
(76, 41, 139),
(77, 41, 140),
(78, 41, 115),
(79, 42, 112),
(80, 42, 141),
(81, 42, 141),
(82, 49, 129),
(83, 49, 120);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `model_members`
--
ALTER TABLE `model_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `model_members_ibfk_1` (`member_id`),
  ADD KEY `model_members_ibfk_2` (`model_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `model_members`
--
ALTER TABLE `model_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_members`
--
ALTER TABLE `model_members`
  ADD CONSTRAINT `model_members_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `model_members_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
