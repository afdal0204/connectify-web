-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 02, 2025 at 11:07 AM
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
-- Database: `connectifydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `errors`
--

CREATE TABLE `errors` (
  `id` int(11) NOT NULL,
  `errorCode` varchar(50) NOT NULL,
  `symptom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `feedback` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `feedback`, `name`) VALUES
(1, 'Please add all user can chat with others', 'EDI'),
(2, 'Add new user please.', 'JHON12');

-- --------------------------------------------------------

--
-- Table structure for table `models`
--

CREATE TABLE `models` (
  `id` int(11) NOT NULL,
  `modelName` varchar(100) NOT NULL,
  `lineArea` varchar(100) NOT NULL,
  `owners` varchar(100) NOT NULL,
  `members` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `models`
--

INSERT INTO `models` (`id`, `modelName`, `lineArea`, `owners`, `members`) VALUES
(1, 'F5685LG', '15', 'Tolie', 'Limbong, Lastri'),
(2, 'F5899TA', '9', 'Alif', 'Rizky, Ryan, Bastian'),
(3, 'F3896LG Auto', '13', 'Doni', 'Imam'),
(4, 'F3896LG Manual', '2', 'Jimmy', 'Sahrul'),
(5, 'Superpod', '10', 'Ile', 'Deksan, Putra');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `model` varchar(100) NOT NULL,
  `station` varchar(100) NOT NULL,
  `groupName` varchar(100) DEFAULT NULL,
  `deviceId` varchar(100) NOT NULL,
  `shift` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `timeStart` text NOT NULL,
  `timeFinish` text NOT NULL,
  `result` varchar(50) NOT NULL,
  `errorCode` int(11) NOT NULL,
  `symptom` varchar(255) NOT NULL,
  `rootCause` varchar(255) NOT NULL,
  `actionTaken` varchar(255) NOT NULL,
  `workId` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `remark` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `model`, `station`, `groupName`, `deviceId`, `shift`, `date`, `timeStart`, `timeFinish`, `result`, `errorCode`, `symptom`, `rootCause`, `actionTaken`, `workId`, `name`, `remark`) VALUES
(1, 'Model A', 'Station A', 'Group A', 'Device A', '', '2025-08-29', '14:20:22', '14:30:22', 'PASS', 0, 'Cable has NG', 'cable NG', 'Change cable', '', '', 'Normal after changed cable'),
(4, 'Model A', 'Station A', 'Group A', 'Device A', '', '2025-08-29', '00:00:00', '14:30:22', 'PASS', 0, 'Cable has NG', 'cable NG', 'Change cable', '', '', 'Normal after changed cable'),
(5, 'Model A', 'Station A', 'Group A', 'Device A', '', '2025-08-29', '11.20', '14:30:22', 'PASS', 0, 'Cable has NG', 'cable NG', 'Change cable', '', '', 'Normal after changed cable'),
(6, 'F5899TA', 'WIFI', '', '611829', '', '0000-00-00', '10:46', '13:00', 'PASS', 0, 'NG on the board', 'cable NG', 'Change cable', '', '', 'please change for new cable'),
(7, 'F3896LG Auto', 'PMAC', '2', '61111', '', '2025-09-01', '13.00', '13.30', 'PASS', 0, 'Error', 'error', 'restart program', '', '', 'done'),
(8, 'F3896LG Manual', 'FINAL', '4', '6111111', '', '2025-08-31', '12.00', '13.45', 'PASS', 0, 'error', 'error', 'check program', '', '', 'done'),
(9, 'F3896LG Manual', 'FINAL', '4', '622222', 'DAY SHIFT', '2025-09-01', '12.00', '13.45', 'PASS', 0, 'error', 'error', 'check program', 'MW1234', 'jhon due', 'done'),
(10, 'F3896LG Manual', 'FINAL', '4', '622222', 'DAY SHIFT', '2025-09-01', '12.00', '13.45', 'PASS', 0, 'error', 'error', 'check program', 'MW1234', 'jondue', 'done'),
(11, 'F3896LG Auto', 'pmac', '', '12345', 'DAY SHIFT', '2025-09-01', '10.00', '10.05', 'PASS', 0, 'error', 'error', 'test', 'MW12345', 'jhon', 'well done'),
(12, 'F3896LG Auto', 'FINAL', '', '1212', 'NIGHT SHIFT', '2025-09-02', '22.00', '22.06', 'PASS', 0, 'cable issue', 'cable issue', 'new cable ', 'MW12345', 'jhon', 'ok');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `workId` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `department` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Id`, `name`, `workId`, `password`, `department`, `role`) VALUES
(15, 'JHON456', 'MW1556', '$2y$10$GsOYpnu9fcjTki2EQwHFLeZsMKpqyy8JYqZRNcRElNTxtMFzyiRK.', '10', 'TECHNICIAN'),
(19, 'JHON12', 'MW1', '$2y$10$U4EUuPBP/LAg/vGYFVpUSuIJIE9dLV3ueC8O2PScf7gzqszu0KM8e', '10', 'TECHNICIAN');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `errors`
--
ALTER TABLE `errors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `models`
--
ALTER TABLE `models`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `errors`
--
ALTER TABLE `errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `models`
--
ALTER TABLE `models`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
