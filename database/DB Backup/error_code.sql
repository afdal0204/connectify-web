-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2025 at 04:38 AM
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
-- Table structure for table `error_code`
--

CREATE TABLE `error_code` (
  `id` int(11) NOT NULL,
  `error_code` varchar(50) NOT NULL,
  `symptom` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `error_code`
--

INSERT INTO `error_code` (`id`, `error_code`, `symptom`, `user_id`, `created_at`) VALUES
(9, 'BAFA03', 'Dut login telnet', 20, '2025-09-26 03:53:19'),
(10, 'E00LT000112', '5gnr', 139, '2025-09-26 07:39:13'),
(11, 'BDFI17', 'Antena2', 139, '2025-09-26 07:43:15'),
(12, 'BDFWOC', '5g pa trimming fail', 99, '2025-09-26 12:47:48'),
(13, 'PC STUCK', 'Pc no respon', 99, '2025-09-26 18:27:43'),
(14, 'BDFTOB', 'Instal sbox fail', 99, '2025-09-26 18:29:20'),
(15, 'BDFD27', 'D3.1 dip 0 downstream test mer fail', 99, '2025-09-26 18:30:14'),
(16, 'BDFU21', 'Upstream dip0 harmonic test failure', 99, '2025-09-26 18:30:47'),
(18, 'BDFW4Y', 'There is a leak in the shelling box', 77, '2025-09-26 20:26:26'),
(20, 'BDFW4Y', '5g on power fail', 77, '2025-09-26 20:32:36'),
(21, 'BDW4Y', '5g on power training fail', 77, '2025-09-26 20:34:34'),
(23, 'E00W003773', 'Iq fact run', 117, '2025-09-26 22:15:51'),
(24, 'CALPATH', 'Environtment verification', 117, '2025-09-26 22:32:37'),
(25, 'BDFR20', 'Can not get rg650v-tl.!', 101, '2025-09-27 04:45:18'),
(26, 'I5FB21', 'Vdsl test fail', 101, '2025-09-27 04:50:40'),
(27, 'E00WI002587', 'Txp_lte_b28_ch27435_antnum-lte_clt', 101, '2025-09-27 04:58:31'),
(28, 'E00WI0081', 'Tx1 pow 908 zwave', 118, '2025-09-27 05:23:50'),
(29, 'BSFWA5', 'Load zigbee fw', 112, '2025-09-27 05:46:44'),
(30, 'BSFOEM', 'Macom bt tune process gpon', 112, '2025-09-27 05:52:22'),
(32, 'BDFTOB', 'Unreadable sbox in the program', 77, '2025-09-27 16:28:05'),
(33, 'BDFB07', 'Check macom ld model', 110, '2025-09-27 21:12:21'),
(34, 'BDFTOW', 'Dmm no connection during testing', 83, '2025-09-29 02:04:28'),
(35, 'E00WI002589', 'Antena  lte_m', 115, '2025-09-29 06:22:31'),
(36, 'BSFOAD', 'Dut login telnet sgc', 110, '2025-09-29 07:04:51'),
(37, 'POWER OFF', 'Power OFF', 110, '2025-09-29 07:10:20'),
(38, 'BSFOEG', 'Open scope', 110, '2025-09-29 07:13:26'),
(39, 'PC RESTART SUDDENLY', 'Pc lag', 81, '2025-09-29 10:59:21'),
(40, 'TX4', 'Tprobe kotor', 81, '2025-09-29 11:02:27'),
(44, 'E00OT001979', 'Login_sgc_fw', 137, '2025-09-29 12:01:45'),
(45, 'BSFP88', 'Eth4 down', 141, '2025-09-29 13:17:26'),
(47, 'BSFI69', 'Ethernet test hping', 141, '2025-09-29 13:25:12'),
(48, 'BDFW02', 'Antena 2', 123, '2025-09-29 15:17:00'),
(49, 'BSFV19', 'Hdmi capture', 135, '2025-09-29 15:35:56'),
(50, '3849', 'Iqfact show result test fail', 112, '2025-09-29 17:08:23'),
(51, 'BSFT24', 'Bt tx edr 2402 ant3 power test_power', 127, '2025-09-29 18:07:28'),
(53, 'E00LT000119', 'Lte ex sensitivity antena mimo', 114, '2025-09-29 18:10:41'),
(54, 'BSFOFO', 'Bl_ont_link_olt_gpon', 112, '2025-09-29 20:22:54'),
(56, 'B00IF000092', 'Set ftm mode', 101, '2025-09-29 22:34:13'),
(57, 'BDFT01', 'Delete sbox fail', 80, '2025-09-29 22:51:35'),
(60, 'BDFB07', 'Telnet commaint fail', 139, '2025-09-29 23:11:23'),
(61, 'E0FW000319', 'Check zwave status tidak stabil', 118, '2025-09-29 23:17:09'),
(62, 'BSFOEJ', 'Dut disconnect ping', 118, '2025-09-29 23:20:59'),
(63, 'E00WI005695', 'Iq disconnection', 118, '2025-09-29 23:30:31'),
(64, 'BSFOFOB', 'Rx power ddmi chek olt gpon unstabil nilai rx ddmi', 118, '2025-09-29 23:44:45'),
(66, 'B00WL000327', '24c0c0:wifi 5180 mcs0 ht20 ant0 and ant1', 133, '2025-09-29 23:54:39'),
(68, 'BDFROE', 'Connect dut fail.\r\nwlannft wifi startftm and loadd', 109, '2025-09-30 01:08:37'),
(69, 'BDFWOD', 'Ant 2g not dont connect', 83, '2025-09-30 03:10:51'),
(70, 'BDFR07', 'Update shipping image fail', 109, '2025-09-30 05:04:53'),
(71, 'BSFL19', 'Led amber fixture', 110, '2025-09-30 06:39:16'),
(72, 'BSFI13', 'Fxs open consule', 108, '2025-09-30 09:19:16'),
(73, 'BSFR06', 'Button fixture', 108, '2025-09-30 09:22:47'),
(74, 'B00WL000334', '24c0c0:wifi 5180 mcs0 ht20 ant0 and ant1', 20, '2025-09-30 09:43:15'),
(75, 'NG TX 2 TPROBE', '-', 81, '2025-09-30 10:38:28'),
(76, 'BDFWOF', 'Wafi antena 2', 114, '2025-09-30 13:24:11'),
(77, 'B00WI000822', 'Wifi_tx2_pow_6115_11ag_ofdm6_b20', 121, '2025-09-30 15:41:10'),
(78, 'TX BDR2402', 'Tx bdr2402', 111, '2025-09-30 20:55:15'),
(79, 'TX BDR 2402', 'Bsft24', 111, '2025-09-30 21:00:30'),
(80, 'AFFW71', 'Bp3 boot up fail', 146, '2025-10-01 01:20:44'),
(81, 'BSFO19', 'Bot up check', 146, '2025-10-01 01:32:12'),
(82, 'BDFR11', 'Secure boot', 109, '2025-10-01 08:38:21'),
(83, 'SET FTM MODE', 'Ng set ftm mode', 81, '2025-10-01 09:07:12'),
(84, 'CLT', 'Nilai antena tidak stabil', 81, '2025-10-01 09:11:46'),
(85, 'CRT', 'Nilai antena tidak stabil', 81, '2025-10-01 09:11:55'),
(86, 'BAFA02', 'Dut boot up ping sgc', 141, '2025-10-01 11:06:36'),
(87, 'BSFI23', 'Ir check', 127, '2025-10-01 16:27:36'),
(88, 'BDFI13', 'Dut not login', 118, '2025-10-01 17:33:41'),
(89, 'BSFOEX', 'Rx_power_calibrati_gpon', 118, '2025-10-01 17:49:10'),
(90, 'BDFR08', 'Update shiping fw', 112, '2025-10-01 20:36:18'),
(91, 'E00OT002026', 'Hping3 192.168.6.2', 152, '2025-10-01 22:47:24'),
(92, 'BDFI18', 'Can not control the led fixture.!', 101, '2025-10-01 23:04:44'),
(93, 'BSFO40', 'Switch 2 is fail', 127, '2025-10-01 23:06:35'),
(94, '3647', 'Iqfact show result test fail', 112, '2025-10-01 23:13:09'),
(95, 'BDFW01', 'Antena 1', 139, '2025-10-01 23:33:01'),
(96, 'BDFWOA', 'Terjadinya kebocoran nilai', 77, '2025-10-01 23:40:47'),
(97, 'BSFO13', 'Open uart', 146, '2025-10-02 04:50:21'),
(98, 'BDFI22', 'Ping telnet problem /connection not normal.', 83, '2025-10-02 08:23:19'),
(99, 'ANT 2', 'Kabel antena terlepas', 81, '2025-10-02 10:05:06'),
(100, 'E0W10003647', 'Iqfact show test fail', 110, '2025-10-02 10:15:17'),
(101, 'CANNOT IQ FACT', 'Iqxel tidak bekerja dengan baik', 81, '2025-10-02 10:26:19'),
(102, 'BSFOEQ', 'Macom tx verify xgs', 141, '2025-10-02 14:30:02'),
(103, 'E00LT000118', 'Antena lte d', 114, '2025-10-02 21:48:29'),
(104, 'PERGERAKAN', 'Module error', 127, '2025-10-02 22:24:23'),
(105, 'BSVIE', 'Hdmi_out_edid', 127, '2025-10-02 22:44:24'),
(106, 'E00WI003689', 'Iqfact result test fail', 112, '2025-10-02 22:54:56'),
(107, 'E00LT00119', 'Mimo', 139, '2025-10-02 23:01:20'),
(108, 'B00OT0003999', 'Ethernet test linkup', 118, '2025-10-03 00:19:40'),
(109, 'BSFF57', 'Low tx power', 118, '2025-10-03 00:24:45'),
(110, 'BDFT14', 'Line 0>1 snr/gain check fail', 36, '2025-10-03 02:28:07'),
(111, 'BDFW4U', '5g-ibf-onpower-delta-ch40 fail.', 103, '2025-10-03 02:36:47'),
(112, 'E00LD000043', 'Check_led oren off_value is fail', 119, '2025-10-03 03:28:07'),
(113, 'BSFB23', '10n0:aon_5.0v_mainboard_pt15300_ni1-ai0', 146, '2025-10-03 07:19:36'),
(114, 'BDFI01', 'Ethernet throughput test fail', 36, '2025-10-03 07:51:22'),
(115, 'E0000001045', 'Wifi dut boot up multi', 110, '2025-10-03 08:41:49'),
(116, 'BDFW52', 'Zigbee rx verify', 110, '2025-10-03 08:47:28'),
(117, 'BSRF8N', 'Zwave rssi for packets received by dut is out of range', 62, '2025-10-03 08:50:28'),
(118, 'BSFTVP', 'Lte tx power is out of range!', 62, '2025-10-03 09:07:01'),
(119, 'BSFI71', 'Ethernet test hping', 141, '2025-10-03 14:14:23'),
(120, 'NO LOADING', 'Check route', 127, '2025-10-03 20:05:52'),
(121, 'E00WI005498', 'Can\'t get power from iq xel', 101, '2025-10-03 22:15:56'),
(122, 'E00WI005498.', '5g_rx_calibration', 101, '2025-10-03 22:18:00'),
(123, 'B00FX000034', 'Wifi_tx3_5300_  : \"3.99\" fail  <22,13>', 152, '2025-10-03 22:39:14'),
(124, 'BOOT UP', 'Cable eth rj45 tidak terhubng ke unit produksi', 152, '2025-10-03 22:48:57'),
(125, 'PROGRAM TIDAK JALAN', 'Program tidak mau jalan auto,jika di run manual,ak', 152, '2025-10-03 22:51:24'),
(126, 'E00OT002061', 'Current:\"0.768\" <2.1,0.9>\r\npower_consumption_curr ', 152, '2025-10-03 22:56:47'),
(127, 'B00WI000819', 'Tx_verify_6175_ant_1 =  \"22.89\" <21,11>', 152, '2025-10-03 23:04:39'),
(128, 'E00FX000022', 'Fixture_open_console', 152, '2025-10-03 23:09:51'),
(129, 'BSFO20', 'Enter fsos mode', 146, '2025-10-04 01:17:20'),
(130, 'E0000000040', 'Check_silabs_connection', 121, '2025-10-04 05:00:09'),
(131, 'B00FX000028', 'Wifi_tx1_fixture_or_dut_problem_pow_2437_11n_mcs0_', 127, '2025-10-04 05:35:32'),
(132, 'E00WI005403', 'Wifi_pa3_pow_old_6905_11be_mcs9_b320-2', 127, '2025-10-04 05:43:47'),
(133, 'SFIS NO CONECT', 'Kabel power cisco terlepas', 81, '2025-10-04 06:00:14'),
(134, 'B00FX000029', 'Wifi_tx2_fixture_or_dut_problem_pow_2437_11n_mcs0_', 121, '2025-10-04 09:02:32'),
(135, 'BVFB02', 'No tx power', 121, '2025-10-04 11:54:57'),
(136, '3847', 'Iqfact show results 6g test', 141, '2025-10-04 13:15:46'),
(137, 'BSFW75', 'Usb23 test port2 fail', 141, '2025-10-04 14:07:57'),
(138, 'BSFWA8', 'Get power 2g txo', 141, '2025-10-04 14:17:18'),
(139, 'E00WI003647', 'Iqfact show result test fail', 112, '2025-10-04 17:51:04'),
(140, 'PC NO RESPON', 'Bongkar pc lalu pembersihan pada pc', 77, '2025-10-04 20:00:29'),
(141, 'E00WI003770', 'Iqfact show results test fail', 112, '2025-10-04 20:23:00'),
(142, 'BDFR08.', 'Modem version fail.!', 101, '2025-10-04 20:58:19'),
(143, 'BSFI70', 'Zigbee xtal cal', 110, '2025-10-06 08:49:54'),
(144, 'DUT INSERT', 'Pengetesan sangat lama', 81, '2025-10-06 09:36:50'),
(145, 'E00W1002591', 'Nr5g lte m b77 tx faill', 114, '2025-10-06 22:11:24'),
(146, 'BDFR0E', 'Connect dut fail!!', 101, '2025-10-06 23:52:27'),
(147, 'POWER PC MATI', 'Tidak hidup pc', 77, '2025-10-07 11:37:53'),
(148, 'BSFI01', 'Ping test', 127, '2025-10-07 20:19:30'),
(149, 'BSFTVV', 'Simplelink rssi for packets received by gm is out of range!', 62, '2025-10-08 05:31:43'),
(150, 'BSFI15', 'Write_pp_dut', 110, '2025-10-08 09:50:04'),
(151, 'BSFPOW', 'Tx zwave ch1', 110, '2025-10-08 10:06:01'),
(152, 'BSFL13', 'Fxs open console', 126, '2025-10-08 16:26:01'),
(153, 'BSFOFB', 'Rx_power_ddmi_check_traffic_gpon', 112, '2025-10-08 21:30:14'),
(154, 'E00WI005413', '5g_tx_calibration', 101, '2025-10-09 00:19:44'),
(155, 'ERORR SOFLOR', 'Entelnet soflor not connect', 83, '2025-10-09 22:36:31'),
(156, '-', 'Long testing time', 101, '2025-10-09 23:30:34'),
(157, '--', '--', 101, '2025-10-09 23:31:10'),
(158, 'E00WI002589 -', 'Txp_lte_b3_ch19575_antnum-lte_ext1', 101, '2025-10-09 23:41:19'),
(159, 'IQ CONECTION', 'Iqxel', 81, '2025-10-10 08:50:54'),
(160, '5G MODUL DATA', 'Kabel dsl ng', 81, '2025-10-10 08:59:20'),
(161, 'SYSTEM ERORR', 'The data system is erorr not testing', 83, '2025-10-10 22:42:39'),
(162, 'BDFII8', 'Can control sg very well', 83, '2025-10-10 22:48:02'),
(163, 'BDFI07', '5g module data check fail!!', 101, '2025-10-11 00:04:49'),
(164, '5G TX CALIBRATION', 'Tx 2', 81, '2025-10-11 06:18:14'),
(165, 'E00FW000319', 'Check_zwave_status', 121, '2025-10-11 10:24:38'),
(166, 'B00WI000574', 'Tx_verify_power_5220mhz_ant_2', 121, '2025-10-11 10:29:41'),
(167, 'BSFOA8', 'Measure_temp', 121, '2025-10-11 12:23:31'),
(168, 'E00SF000052', 'Get_pp_sfis', 121, '2025-10-11 12:32:16'),
(169, 'B00PO000186', 'Psu_t4201_0v8', 121, '2025-10-11 12:43:28'),
(170, 'E00OT001981', 'Dut_boot_up_ping_sgc', 121, '2025-10-13 06:08:30'),
(171, 'BSFTVE', 'Zigbee tx power is out of range!', 62, '2025-10-13 07:56:00'),
(172, 'ETHERNET DUT FAIL', 'Tidak terhubung ke ethernet', 81, '2025-10-13 23:58:47'),
(173, 'B00FX000035', 'Wifi_tx4_fixture_or_dut_problem_pow_5300_11ac_mcs8_b20', 121, '2025-10-15 06:10:57'),
(174, 'B00LD000205', 'Camera_open', 121, '2025-10-15 06:17:24'),
(175, 'BSFOEI', 'Open iqs', 110, '2025-10-15 15:52:47'),
(176, 'BSFL21', 'Led red test telnet fixture', 110, '2025-10-15 15:57:27'),
(177, 'E00WI003768', 'Iqfact show results test', 110, '2025-10-15 16:04:07'),
(178, 'E00WI005531', 'Tx1_pow_2440_zigbee', 121, '2025-10-16 06:11:12'),
(179, 'BDFTOO', 'Ntd nonvol failed', 83, '2025-10-17 23:12:31'),
(180, 'BSFB01', 'Takeshot is fail', 111, '2025-10-18 10:28:20'),
(181, 'B00WL000331', 'Wifi 2412 mcs0 ht20 ant0 power test_power is fail !', 111, '2025-10-18 10:43:29'),
(182, 'E00WI003849', 'Iqfact show results', 110, '2025-10-20 15:26:14'),
(183, 'BSFOHD', 'All test aborted', 111, '2025-10-21 00:52:53'),
(184, 'BSFOHD>', 'Tx_calibration_test is fail !', 111, '2025-10-21 01:09:07'),
(185, 'BSFVIE', 'Hdmi out edid', 119, '2025-10-22 00:15:11'),
(186, 'BSFI30', 'Usb2.0_check is fail', 111, '2025-10-22 01:12:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `error_code`
--
ALTER TABLE `error_code`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `error_code`
--
ALTER TABLE `error_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `error_code`
--
ALTER TABLE `error_code`
  ADD CONSTRAINT `error_code_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
