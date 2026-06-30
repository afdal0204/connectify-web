-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 30, 2026 at 09:10 AM
-- Server version: 8.4.3
-- PHP Version: 8.5.5

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
-- Table structure for table `multi_dept_reports`
--

CREATE TABLE `multi_dept_reports` (
  `id` int NOT NULL,
  `model_id` int DEFAULT NULL,
  `station_id` int DEFAULT NULL,
  `device_id` int DEFAULT NULL,
  `shift` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_start` text COLLATE utf8mb4_general_ci,
  `time_finish` text COLLATE utf8mb4_general_ci,
  `error_code_id` int DEFAULT NULL,
  `failure_photo` text COLLATE utf8mb4_general_ci,
  `input_quantity` int DEFAULT NULL,
  `defect_quantity` int DEFAULT NULL,
  `failure_rate` int DEFAULT NULL,
  `root_cause` text COLLATE utf8mb4_general_ci,
  `action_taken` text COLLATE utf8mb4_general_ci,
  `short_term_solution` text COLLATE utf8mb4_general_ci,
  `long_term_solution` text COLLATE utf8mb4_general_ci,
  `preventive_action` text COLLATE utf8mb4_general_ci,
  `user_id` int DEFAULT NULL,
  `responsible_person` text COLLATE utf8mb4_general_ci,
  `status` text COLLATE utf8mb4_general_ci,
  `improvement_photo` text COLLATE utf8mb4_general_ci,
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `multi_dept_reports`
--

INSERT INTO `multi_dept_reports` (`id`, `model_id`, `station_id`, `device_id`, `shift`, `date`, `time_start`, `time_finish`, `error_code_id`, `failure_photo`, `input_quantity`, `defect_quantity`, `failure_rate`, `root_cause`, `action_taken`, `short_term_solution`, `long_term_solution`, `preventive_action`, `user_id`, `responsible_person`, `status`, `improvement_photo`, `remark`, `created_at`, `updated_at`) VALUES
(43, 59, 328, 985, 'Day Shift', '2026-06-30', NULL, NULL, 505, 'uploads/failure_photos/1782807220_6a437ab4d422c.jpg', 12, 12, 1, 'Test', NULL, 'Test', 'Test', NULL, 253, 'OP QA', 'Open', NULL, 'Test', '2026-06-30 08:13:40', '2026-06-30 08:13:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `multi_dept_reports`
--
ALTER TABLE `multi_dept_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `model_id` (`model_id`),
  ADD KEY `device_id` (`device_id`),
  ADD KEY `station_id` (`station_id`),
  ADD KEY `error_code_id` (`error_code_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `multi_dept_reports`
--
ALTER TABLE `multi_dept_reports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `multi_dept_reports`
--
ALTER TABLE `multi_dept_reports`
  ADD CONSTRAINT `multi_dept_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `multi_dept_reports_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `multi_dept_reports_ibfk_3` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `multi_dept_reports_ibfk_4` FOREIGN KEY (`station_id`) REFERENCES `stations` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `multi_dept_reports_ibfk_5` FOREIGN KEY (`error_code_id`) REFERENCES `error_code` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
