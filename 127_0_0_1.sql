-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2025 at 08:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `food_management`
--
CREATE DATABASE IF NOT EXISTS `food_management` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `food_management`;

-- --------------------------------------------------------

--
-- Table structure for table `admin_table`
--

CREATE TABLE `admin_table` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_table`
--

INSERT INTO `admin_table` (`id`, `email`, `password`) VALUES
(3, 'sandwfmanagement@gmail.com', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `claimed_donations`
--

CREATE TABLE `claimed_donations` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `expiry_date` date NOT NULL,
  `claim_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `claimed_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claimed_donations`
--

INSERT INTO `claimed_donations` (`id`, `donation_id`, `expiry_date`, `claim_date`, `claimed_by`) VALUES
(35, 139, '0000-00-00', '2025-03-22 07:31:23', 'Admin'),
(36, 138, '0000-00-00', '2025-03-22 07:31:33', 'Admin'),
(39, 146, '0000-00-00', '2025-03-27 04:13:47', 'Admin'),
(40, 147, '0000-00-00', '2025-04-08 03:42:49', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `waste_center` varchar(255) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `claimed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donated`
--

CREATE TABLE `donated` (
  `id` int(11) NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `donation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `location` varchar(255) DEFAULT NULL,
  `donation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donated_donations`
--

CREATE TABLE `donated_donations` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `donation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donated_donations`
--

INSERT INTO `donated_donations` (`id`, `donation_id`, `food_name`, `quantity`, `donation_date`) VALUES
(65, 132, 'mandhi', 5, '2025-03-22 07:36:05'),
(66, 133, 'biriyani', 6, '2025-03-22 07:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_name` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `food_name` varchar(255) NOT NULL,
  `quantity` varchar(100) NOT NULL,
  `expiry_date` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `status` enum('pending','Claimed','Donated','Expired') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `food_type` varchar(50) DEFAULT NULL,
  `charges` decimal(10,2) DEFAULT 0.00,
  `payment_proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donor_name`, `contact_number`, `email`, `food_name`, `quantity`, `expiry_date`, `location`, `status`, `created_at`, `food_type`, `charges`, `payment_proof`) VALUES
(132, 'fesmi', '9022837477', '22gcs17@meaec.edu.in', 'mandhi', '5', '2025-03-20', 'kunnummal', 'Donated', '2025-03-17 18:21:03', 'Cooked Food', 0.00, NULL),
(133, 'mohammed amjad k', '7994767824', '22gcs03@meaec.edu.in', 'biriyani', '6', '2025-03-27', 'anagdipuram', 'Donated', '2025-03-17 18:23:51', 'Cooked Food', 0.00, NULL),
(138, 'fathima', '6238577400', '22gcs31@meaec.edu.in', 'fried rice', '3', '2025-03-20', 'kunnapally', 'Claimed', '2025-03-18 05:33:28', 'Cooked Food', 0.00, NULL),
(139, 'Dilsha', '9947952795', '22gcs18@meaec.edu.in', 'rice', '8', '2025-03-20', 'melmuri', 'Claimed', '2025-03-18 09:07:12', 'Cooked Food', 0.00, NULL),
(141, 'vafa', '9947952795', '22gcs18@meaec.edu.in', 'apples', '5', '2025-03-27', 'pattikkad', 'pending', '2025-03-22 07:32:29', 'Fruits', 0.00, NULL),
(142, 'safa', '9947952795', '22gcs18@meaec.edu.in', 'carrots', '4', '2025-03-26', 'angadipuram', 'pending', '2025-03-22 07:34:11', 'Vegetables', 0.00, NULL),
(143, 'minha', '9947952795', '22gcs18@meaec.edu.in', 'sweets', '7', '2025-04-16', 'perinthalanna', 'pending', '2025-03-22 07:45:19', 'Fruits', 0.00, NULL),
(144, 'ashmil', '9947952795', '22gcs18@meaec.edu.in', 'rice', '5', '2025-04-05', 'melmuri', 'pending', '2025-03-22 08:00:54', 'Cooked Food', 0.00, NULL),
(145, 'Asna', '9947952795', '22gcs18@meaec.edu.in', 'shawaya', '7', '2025-03-29', 'perinthalanna', 'pending', '2025-03-26 08:07:20', 'Cooked Food', 0.00, NULL),
(146, 'Dilsha', '9947952795', '22gcs18@meaec.edu.in', 'sweets', '6', '2025-03-30', 'melmuri', 'Claimed', '2025-03-27 04:12:24', 'Cooked Food', 0.00, NULL),
(147, 'Asna', '9947952795', '22gcs18@meaec.edu.in', 'apple', '3', '2025-04-17', 'angadipuram', 'Claimed', '2025-04-08 03:37:20', 'Fruits', 0.00, NULL),
(148, 'Asna', '9947952795', '22gcs18@meaec.edu.in', 'bread', '6', '2025-04-18', 'melmuri', 'pending', '2025-04-08 04:17:20', 'Packed Food', 0.00, NULL),
(149, 'Asna', '9947952795', '22gcs11@meaec.edu.in', 'sweets', '3', '2025-04-09', 'pmna', 'pending', '2025-04-08 04:23:45', 'Bakery Items', 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expired_donations`
--

CREATE TABLE `expired_donations` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `waste_center_id` varchar(255) DEFAULT NULL,
  `food_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_mode` enum('online','offline') DEFAULT NULL,
  `payment_status` enum('Pending','Completed') DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `payment_proof` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `waste_center_id`, `food_id`, `amount`, `payment_mode`, `payment_status`, `payment_date`, `payment_proof`, `transaction_id`) VALUES
(17, 'wastecenter1', 26, 80.00, 'online', 'Completed', '2025-03-22 13:36:52', 'uploads/payment_proofs/1742630812_Screenshot (9).png', NULL),
(18, 'wastecenter1', 29, 40.00, 'offline', 'Completed', '2025-03-22 13:37:02', NULL, NULL),
(19, 'asnaswastecenter', 28, 50.00, 'online', 'Completed', '2025-03-22 13:38:52', 'uploads/payment_proofs/1742630932_Screenshot (10).png', NULL),
(20, 'asnaswastecenter', 22, 60.00, 'offline', 'Pending', '2025-03-22 13:39:00', NULL, NULL),
(21, 'wastecenter1', 27, 60.00, 'offline', 'Pending', '2025-03-27 09:44:57', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `waste_centers`
--

CREATE TABLE `waste_centers` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `proof` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waste_centers`
--

INSERT INTO `waste_centers` (`id`, `username`, `password`, `location`, `proof`) VALUES
(6, 'wastecenter1', '$2y$10$6f/iMdjE1Qsmu.oRtCi76.GuY4VRuVApr.9UQLnaf.B4isRs9LJ3m', 'mpm', 'Screenshot (10).png'),
(7, 'wastecenter2', '$2y$10$PZ0na25.fyD0T4FWS5cjXuSt2voDkPd.vpwdAlegxQbWyWHviicWa', 'angadipuram', 'Screenshot (11).png'),
(8, 'wastecenter4', '$2y$10$N71RnulnfSj/9Z56ebEj1ujjZT2r56i4RHCGgZHdO.isPkOsuAYHO', 'melmuri', 'Screenshot (12).png'),
(9, 'asnaswastecenter', '$2y$10$Qz.vcDh4LkPaeUaA/wALaO3LzIXVPNqoHJZVfttcwYvXtF5/mNdaa', 'makkaraparamb', 'Screenshot 2025-03-02 150912.png');

-- --------------------------------------------------------

--
-- Table structure for table `waste_donations`
--

CREATE TABLE `waste_donations` (
  `id` int(11) NOT NULL,
  `donor_name` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `food_type` varchar(50) NOT NULL,
  `food_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `location` text NOT NULL,
  `charges` decimal(10,2) NOT NULL,
  `payment_mode` enum('online','offline') NOT NULL,
  `payment_proof` varchar(255) NOT NULL,
  `status` enum('pending','processing','completed','claimed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `claim_date` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waste_donations`
--

INSERT INTO `waste_donations` (`id`, `donor_name`, `contact_number`, `email`, `food_type`, `food_name`, `quantity`, `location`, `charges`, `payment_mode`, `payment_proof`, `status`, `created_at`, `claim_date`, `updated_at`) VALUES
(22, 'asna vk', '9947952795', '22gcs18@meaec.edu.in', 'Expired Fruits', '', 6.00, 'malappuram', 60.00, 'offline', '', 'completed', '2025-03-22 07:28:29', '2025-03-22 13:07:54', '2025-03-22 08:09:00'),
(23, 'zara', '9947952795', '22gcs18@meaec.edu.in', 'Expired Cooked Food', '', 6.00, 'pmna', 60.00, 'online', 'uploads/payment_proofs/1742628562_Screenshot (7).png', 'claimed', '2025-03-22 07:29:22', '2025-03-22 13:07:42', '2025-03-22 07:37:42'),
(24, 'abhiraj', '9947952795', '22gcs18@meaec.edu.in', 'Expired Cooked Food', '', 8.00, 'angadipuram', 80.00, 'offline', '', 'pending', '2025-03-22 08:01:39', NULL, '2025-03-22 08:01:39'),
(25, 'abhinav', '9947952795', '22gcs18@meaec.edu.in', 'Expired Packed Food', '', 7.00, 'perinthalanna', 70.00, 'online', 'uploads/payment_proofs/1742630547_Screenshot (8).png', 'pending', '2025-03-22 08:02:27', NULL, '2025-03-22 08:02:27'),
(26, 'fathima', '9947952795', '22gcs18@meaec.edu.in', 'Expired Bakery Items', '', 8.00, 'mpm', 80.00, 'offline', '', 'completed', '2025-03-22 08:03:09', '2025-03-22 13:36:04', '2025-03-22 08:06:52'),
(27, 'anoof', '9947952795', '22gcs18@meaec.edu.in', 'Expired Vegetables', '', 6.00, 'angadipuram', 60.00, 'offline', '', 'completed', '2025-03-22 08:03:50', '2025-03-22 13:35:54', '2025-03-27 04:14:57'),
(28, 'saleela', '9947952795', '22gcs18@meaec.edu.in', 'Expired Packed Food', '', 5.00, 'perinthalanna', 50.00, 'online', 'uploads/payment_proofs/1742630676_Screenshot 2025-03-14 210941.png', 'completed', '2025-03-22 08:04:36', '2025-03-22 13:35:43', '2025-03-22 08:08:52'),
(29, 'asmi', '9947952795', '22gcs18@meaec.edu.in', 'Expired Packed Food', '', 4.00, 'angadipuram', 40.00, 'offline', '', 'completed', '2025-03-22 08:05:04', '2025-03-22 13:35:31', '2025-03-22 08:07:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_table`
--
ALTER TABLE `admin_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `claimed_donations`
--
ALTER TABLE `claimed_donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `donated`
--
ALTER TABLE `donated`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donated_donations`
--
ALTER TABLE `donated_donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expired_donations`
--
ALTER TABLE `expired_donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `waste_centers`
--
ALTER TABLE `waste_centers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `unique_proof` (`proof`);

--
-- Indexes for table `waste_donations`
--
ALTER TABLE `waste_donations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_table`
--
ALTER TABLE `admin_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `claimed_donations`
--
ALTER TABLE `claimed_donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `donated`
--
ALTER TABLE `donated`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `donated_donations`
--
ALTER TABLE `donated_donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `expired_donations`
--
ALTER TABLE `expired_donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `waste_centers`
--
ALTER TABLE `waste_centers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `waste_donations`
--
ALTER TABLE `waste_donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `claimed_donations`
--
ALTER TABLE `claimed_donations`
  ADD CONSTRAINT `claimed_donations_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`);

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donated_donations`
--
ALTER TABLE `donated_donations`
  ADD CONSTRAINT `donated_donations_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`);

--
-- Constraints for table `expired_donations`
--
ALTER TABLE `expired_donations`
  ADD CONSTRAINT `expired_donations_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`);
--
-- Database: `hmisphp`
--
CREATE DATABASE IF NOT EXISTS `hmisphp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hmisphp`;

-- --------------------------------------------------------

--
-- Table structure for table `his_accounts`
--
-- Error reading structure for table hmisphp.his_accounts: #1932 - Table 'hmisphp.his_accounts' doesn't exist in engine
-- Error reading data for table hmisphp.his_accounts: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_accounts`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_admin`
--
-- Error reading structure for table hmisphp.his_admin: #1932 - Table 'hmisphp.his_admin' doesn't exist in engine
-- Error reading data for table hmisphp.his_admin: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_admin`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_assets`
--
-- Error reading structure for table hmisphp.his_assets: #1932 - Table 'hmisphp.his_assets' doesn't exist in engine
-- Error reading data for table hmisphp.his_assets: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_assets`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_docs`
--
-- Error reading structure for table hmisphp.his_docs: #1932 - Table 'hmisphp.his_docs' doesn't exist in engine
-- Error reading data for table hmisphp.his_docs: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_docs`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_equipments`
--
-- Error reading structure for table hmisphp.his_equipments: #1932 - Table 'hmisphp.his_equipments' doesn't exist in engine
-- Error reading data for table hmisphp.his_equipments: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_equipments`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_laboratory`
--
-- Error reading structure for table hmisphp.his_laboratory: #1932 - Table 'hmisphp.his_laboratory' doesn't exist in engine
-- Error reading data for table hmisphp.his_laboratory: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_laboratory`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_medical_records`
--
-- Error reading structure for table hmisphp.his_medical_records: #1932 - Table 'hmisphp.his_medical_records' doesn't exist in engine
-- Error reading data for table hmisphp.his_medical_records: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_medical_records`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_patients`
--
-- Error reading structure for table hmisphp.his_patients: #1932 - Table 'hmisphp.his_patients' doesn't exist in engine
-- Error reading data for table hmisphp.his_patients: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_patients`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_patient_transfers`
--
-- Error reading structure for table hmisphp.his_patient_transfers: #1932 - Table 'hmisphp.his_patient_transfers' doesn't exist in engine
-- Error reading data for table hmisphp.his_patient_transfers: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_patient_transfers`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_payrolls`
--
-- Error reading structure for table hmisphp.his_payrolls: #1932 - Table 'hmisphp.his_payrolls' doesn't exist in engine
-- Error reading data for table hmisphp.his_payrolls: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_payrolls`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_pharmaceuticals`
--
-- Error reading structure for table hmisphp.his_pharmaceuticals: #1932 - Table 'hmisphp.his_pharmaceuticals' doesn't exist in engine
-- Error reading data for table hmisphp.his_pharmaceuticals: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_pharmaceuticals`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_pharmaceuticals_categories`
--
-- Error reading structure for table hmisphp.his_pharmaceuticals_categories: #1932 - Table 'hmisphp.his_pharmaceuticals_categories' doesn't exist in engine
-- Error reading data for table hmisphp.his_pharmaceuticals_categories: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_pharmaceuticals_categories`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_prescriptions`
--
-- Error reading structure for table hmisphp.his_prescriptions: #1932 - Table 'hmisphp.his_prescriptions' doesn't exist in engine
-- Error reading data for table hmisphp.his_prescriptions: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_prescriptions`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_pwdresets`
--
-- Error reading structure for table hmisphp.his_pwdresets: #1932 - Table 'hmisphp.his_pwdresets' doesn't exist in engine
-- Error reading data for table hmisphp.his_pwdresets: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_pwdresets`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_surgery`
--
-- Error reading structure for table hmisphp.his_surgery: #1932 - Table 'hmisphp.his_surgery' doesn't exist in engine
-- Error reading data for table hmisphp.his_surgery: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_surgery`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_vendor`
--
-- Error reading structure for table hmisphp.his_vendor: #1932 - Table 'hmisphp.his_vendor' doesn't exist in engine
-- Error reading data for table hmisphp.his_vendor: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_vendor`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `his_vitals`
--
-- Error reading structure for table hmisphp.his_vitals: #1932 - Table 'hmisphp.his_vitals' doesn't exist in engine
-- Error reading data for table hmisphp.his_vitals: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `hmisphp`.`his_vitals`' at line 1
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--
-- Error reading structure for table phpmyadmin.pma__bookmark: #1932 - Table 'phpmyadmin.pma__bookmark' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__bookmark: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__bookmark`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--
-- Error reading structure for table phpmyadmin.pma__central_columns: #1932 - Table 'phpmyadmin.pma__central_columns' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__central_columns: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__central_columns`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--
-- Error reading structure for table phpmyadmin.pma__column_info: #1932 - Table 'phpmyadmin.pma__column_info' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__column_info: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__column_info`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--
-- Error reading structure for table phpmyadmin.pma__designer_settings: #1932 - Table 'phpmyadmin.pma__designer_settings' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__designer_settings: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__designer_settings`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--
-- Error reading structure for table phpmyadmin.pma__export_templates: #1932 - Table 'phpmyadmin.pma__export_templates' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__export_templates: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__export_templates`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--
-- Error reading structure for table phpmyadmin.pma__favorite: #1932 - Table 'phpmyadmin.pma__favorite' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__favorite: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__favorite`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--
-- Error reading structure for table phpmyadmin.pma__history: #1932 - Table 'phpmyadmin.pma__history' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__history: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__history`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--
-- Error reading structure for table phpmyadmin.pma__navigationhiding: #1932 - Table 'phpmyadmin.pma__navigationhiding' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__navigationhiding: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__navigationhiding`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--
-- Error reading structure for table phpmyadmin.pma__pdf_pages: #1932 - Table 'phpmyadmin.pma__pdf_pages' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__pdf_pages: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__pdf_pages`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--
-- Error reading structure for table phpmyadmin.pma__recent: #1932 - Table 'phpmyadmin.pma__recent' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__recent: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__recent`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--
-- Error reading structure for table phpmyadmin.pma__relation: #1932 - Table 'phpmyadmin.pma__relation' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__relation: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__relation`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--
-- Error reading structure for table phpmyadmin.pma__savedsearches: #1932 - Table 'phpmyadmin.pma__savedsearches' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__savedsearches: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__savedsearches`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--
-- Error reading structure for table phpmyadmin.pma__table_coords: #1932 - Table 'phpmyadmin.pma__table_coords' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__table_coords: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__table_coords`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--
-- Error reading structure for table phpmyadmin.pma__table_info: #1932 - Table 'phpmyadmin.pma__table_info' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__table_info: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__table_info`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--
-- Error reading structure for table phpmyadmin.pma__table_uiprefs: #1932 - Table 'phpmyadmin.pma__table_uiprefs' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__table_uiprefs`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--
-- Error reading structure for table phpmyadmin.pma__tracking: #1932 - Table 'phpmyadmin.pma__tracking' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__tracking: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__tracking`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--
-- Error reading structure for table phpmyadmin.pma__userconfig: #1932 - Table 'phpmyadmin.pma__userconfig' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__userconfig: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__userconfig`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--
-- Error reading structure for table phpmyadmin.pma__usergroups: #1932 - Table 'phpmyadmin.pma__usergroups' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__usergroups: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__usergroups`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--
-- Error reading structure for table phpmyadmin.pma__users: #1932 - Table 'phpmyadmin.pma__users' doesn't exist in engine
-- Error reading data for table phpmyadmin.pma__users: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `phpmyadmin`.`pma__users`' at line 1
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
