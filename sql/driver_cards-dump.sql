-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql207.infinityfree.com
-- Generation Time: Sep 28, 2026 at 05:01 PM
-- Server version: 11.4.13-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42703968_logisti`
--

-- --------------------------------------------------------

--
-- Table structure for table `driver_cards`
--

CREATE TABLE `driver_cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `card_number` varchar(50) NOT NULL,
  `driver_id_number` varchar(20) NOT NULL,
  `first_name_ar` varchar(100) NOT NULL,
  `family_name_ar` varchar(100) NOT NULL,
  `card_type_ar` text NOT NULL,
  `card_type_en` text NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_cards`
--

INSERT INTO `driver_cards` (`id`, `token`, `card_number`, `driver_id_number`, `first_name_ar`, `family_name_ar`, `card_type_ar`, `card_type_en`, `issue_date`, `expiry_date`, `created_at`) VALUES
(4, 'c84f12a7-5b9d-4e21-8c73-91f4d6ab2e58', '38.59274186', '2633286055', 'إيهاب', 'احمد', 'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - منشآت)\r\n\r\n', 'Light Truck Driver', '2026-09-02', '2027-09-04', '2026-09-02 20:09:17'),
(5, '9e4c7f12-a8d3-4b65-91fa-2c7d8e5b3f41', '39.68427513', '2481771133', 'عبدالرحمن', 'رجب', 'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - منشآت)\r\n', 'Light Truck Driver', '2026-09-07', '2027-09-09', '2026-09-09 20:25:12'),
(6, 'f3b7e91a-6c2d-4a85-9e31-b8d42f7c1a55', '40.73159284', '2518249400', 'احمد', 'ادم', 'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - منشآت)\r\n', 'Light Truck Driver', '2026-09-15', '2027-09-17', '2026-09-15 11:08:21'),
(7, '06ae1b26-b1e4-11f1-91a4-05b16afebdb8', '39.21587463', '2638771903', 'امير', 'عز الدين', 'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - منشآت)\r\n', 'Light Truck Driver', '2026-09-15', '2027-09-17', '2026-09-16 15:33:54'),
(8, 'cc8e2b78-55d7-4dcf-8fd0-a94dc512aa18', '39.14087415', '2640461500', 'محمود', 'عادل', 'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - منشآت)\r\n', 'Light Truck Driver', '2026-09-18', '2027-09-20', '2026-09-18 08:54:06'),
(9, '26d524fa-e415-4e49-824f-cc5ea6e3ec02', '39.56061229', '2640461500', 'محمود', 'عادل', 'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - منشآت)\r\n', 'Light Truck Driver', '2026-09-18', '2027-09-20', '2026-09-18 08:55:05'),
(11, 'f194d6b2-8c79-4d27-a20b-83aed081829c', '38.77436666', '2640461500', 'محمود', 'عادل', 'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - م)', 'Light Truck Driver', '2026-09-20', '2027-09-22', '2026-09-18 09:03:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `driver_cards`
--
ALTER TABLE `driver_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `driver_cards`
--
ALTER TABLE `driver_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
