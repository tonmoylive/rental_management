-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 27, 2025 at 06:53 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rentalmanagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@rentalportal.com', '2025-10-25 05:08:43');

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `building_name` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `address` text CHARACTER SET utf8mb4 NOT NULL,
  `total_floors` int(11) NOT NULL,
  `description` text CHARACTER SET utf8mb4,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` (`id`, `owner_id`, `building_name`, `address`, `total_floors`, `description`, `created_at`) VALUES
(1, 2, 'ABC', 'Nowhata', 5, '', '2025-10-26 05:02:26'),
(3, 2, 'Suborno Nir', 'Aam Chottor, Rajshahi', 3, '', '2025-10-27 16:35:50');

-- --------------------------------------------------------

--
-- Table structure for table `building_owners`
--

CREATE TABLE `building_owners` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4,
  `status` enum('active','inactive') DEFAULT 'active',
  `plan_id` int(11) DEFAULT NULL,
  `account_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `building_owners`
--

INSERT INTO `building_owners` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `address`, `status`, `plan_id`, `account_status`, `created_at`) VALUES
(2, 'owner', '$2y$10$Ydhlo1l2t9mSxcDtmMbG8O4bR4MxYYV83WTqJjGWCvbW3ZQC/wyCy', 'Owner', 'owner@gmail.com', '34324325435', 'Nowhata', 'active', 1, 'approved', '2025-10-25 18:06:46');

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `floor_number` int(11) NOT NULL,
  `floor_name` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `rent_amount` decimal(10,2) NOT NULL,
  `maintenance_fee` decimal(10,2) DEFAULT '0.00',
  `status` enum('occupied','vacant') DEFAULT 'vacant',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`id`, `building_id`, `floor_number`, `floor_name`, `rent_amount`, `maintenance_fee`, `status`, `created_at`) VALUES
(1, 1, 1, 'Floor 1', '20000.00', '5000.00', 'occupied', '2025-10-26 05:02:26'),
(2, 1, 2, 'Floor 2', '0.00', '0.00', 'vacant', '2025-10-26 05:02:26'),
(3, 1, 3, 'Floor 3', '0.00', '0.00', 'vacant', '2025-10-26 05:02:26'),
(4, 1, 4, 'Floor 4', '0.00', '0.00', 'vacant', '2025-10-26 05:02:26'),
(5, 1, 5, 'Floor 5', '0.00', '0.00', 'vacant', '2025-10-26 05:02:26'),
(10, 3, 1, 'Floor 1', '0.00', '0.00', 'vacant', '2025-10-27 16:35:50'),
(11, 3, 2, 'Floor 2', '0.00', '0.00', 'vacant', '2025-10-27 16:35:50'),
(12, 3, 3, 'Floor 3', '0.00', '0.00', 'vacant', '2025-10-27 16:35:50');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 NOT NULL,
  `content` text CHARACTER SET utf8mb4 NOT NULL,
  `notice_type` enum('general','urgent','maintenance') DEFAULT 'general',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `building_id`, `title`, `content`, `notice_type`, `created_by`, `created_at`) VALUES
(1, 1, 'Water Pump', 'বৈদ্যুতিক লাইনে কাজের জন্য আগামীকাল সকাল ৯ টা হইতে দুপুর ২ টা পর্যন্ত পানির পাম্প বন্ধ থাকিবে ', 'maintenance', 2, '2025-10-26 18:34:54'),
(2, 1, 'লিফট পরিষ্কার রাখা', 'বাসার পরিত্যাক্ত ময়লা - আবর্জনা ফেলার জন্য লিফট ব্যবহার থেকে বিরত থাকুন ', 'general', 2, '2025-10-26 19:10:15');

-- --------------------------------------------------------

--
-- Table structure for table `owner_payments`
--

CREATE TABLE `owner_payments` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `owner_payments`
--

INSERT INTO `owner_payments` (`id`, `owner_id`, `plan_id`, `amount`, `payment_date`, `transaction_id`, `status`) VALUES
(1, 2, 1, '99.00', '2025-10-25 18:06:46', NULL, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `floor_id` int(11) NOT NULL,
  `payment_type` enum('rent','maintenance','other') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_month` varchar(20) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'completed',
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `tenant_id`, `floor_id`, `payment_type`, `amount`, `payment_month`, `payment_date`, `payment_method`, `transaction_id`, `status`, `notes`, `created_at`) VALUES
(1, 1, 1, 'rent', '25000.00', '2025-10', '2025-10-26', 'bank_transfer', 'DW7382YH3G', 'completed', '', '2025-10-26 19:11:10');

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE `rules` (
  `id` int(11) NOT NULL,
  `building_id` int(11) NOT NULL,
  `rule_title` varchar(200) CHARACTER SET utf8mb4 NOT NULL,
  `rule_content` text CHARACTER SET utf8mb4 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service_plans`
--

CREATE TABLE `service_plans` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `duration_days` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `service_plans`
--

INSERT INTO `service_plans` (`id`, `name`, `description`, `price`, `duration_days`, `created_at`) VALUES
(1, 'Starter', 'Up to 10 Properties', '99.00', 30, '2025-10-25 18:00:54');

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `floor_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `nid_number` varchar(20) DEFAULT NULL,
  `move_in_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `floor_id`, `username`, `password`, `full_name`, `email`, `phone`, `nid_number`, `move_in_date`, `status`, `created_at`) VALUES
(1, 1, 'tenant1', '$2y$10$KwaPD1/A4KJ9DtjxDm..BuuYhDKtJPj8hxUCnL8OPK13RlFMc/PQG', 'Mr. Rohim', 'tenant@gmail.com', '3454643634', '43543545345', '2025-10-26', 'active', '2025-10-26 05:03:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `building_owners`
--
ALTER TABLE `building_owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_owner_plan` (`plan_id`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_floor` (`building_id`,`floor_number`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `building_id` (`building_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `owner_payments`
--
ALTER TABLE `owner_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `floor_id` (`floor_id`);

--
-- Indexes for table `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `building_id` (`building_id`);

--
-- Indexes for table `service_plans`
--
ALTER TABLE `service_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `floor_id` (`floor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `building_owners`
--
ALTER TABLE `building_owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `owner_payments`
--
ALTER TABLE `owner_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rules`
--
ALTER TABLE `rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_plans`
--
ALTER TABLE `service_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buildings`
--
ALTER TABLE `buildings`
  ADD CONSTRAINT `buildings_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `building_owners` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `building_owners`
--
ALTER TABLE `building_owners`
  ADD CONSTRAINT `fk_owner_plan` FOREIGN KEY (`plan_id`) REFERENCES `service_plans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `floors`
--
ALTER TABLE `floors`
  ADD CONSTRAINT `floors_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `notices_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notices_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `building_owners` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `owner_payments`
--
ALTER TABLE `owner_payments`
  ADD CONSTRAINT `owner_payments_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `building_owners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `owner_payments_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `service_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rules`
--
ALTER TABLE `rules`
  ADD CONSTRAINT `rules_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `tenants_ibfk_1` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
