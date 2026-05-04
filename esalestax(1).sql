-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2026 at 07:43 PM
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
-- Database: `esalestax`
--

-- --------------------------------------------------------

--
-- Table structure for table `installment`
--

CREATE TABLE `installment` (
  `installment_id` int(11) NOT NULL,
  `taxpayer_id` int(11) DEFAULT NULL,
  `tax_period` varchar(50) DEFAULT NULL,
  `total_due` decimal(10,2) DEFAULT NULL,
  `first_payment` decimal(10,2) DEFAULT NULL,
  `number_of_installments` int(11) DEFAULT NULL,
  `submit_date` date DEFAULT NULL,
  `installment_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `taxpayer_id` int(11) DEFAULT NULL,
  `e_payment_number` varchar(100) DEFAULT NULL,
  `description_of_movement` text DEFAULT NULL,
  `period` varchar(50) DEFAULT NULL,
  `installment_value` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registration_cancel`
--

CREATE TABLE `registration_cancel` (
  `cancel_id` int(11) NOT NULL,
  `taxpayer_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `request_id` int(11) NOT NULL,
  `taxpayer_id` int(11) DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `submit_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `declaration_id` int(11) DEFAULT NULL,
  `installment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taxpayers`
--

CREATE TABLE `taxpayers` (
  `taxpayer_id` int(11) NOT NULL,
  `taxpayer_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `trade_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `registration_type` varchar(50) DEFAULT NULL,
  `is_service` tinyint(1) DEFAULT NULL,
  `is_goods` tinyint(1) DEFAULT NULL,
  `designated_directorate` varchar(100) DEFAULT NULL,
  `nature_of_activity` varchar(255) DEFAULT NULL,
  `registration_number` varchar(50) DEFAULT NULL,
  `national_establishment_number` varchar(50) DEFAULT NULL,
  `date_of_establishment` date DEFAULT NULL,
  `taxpayer_classification` varchar(100) DEFAULT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taxpayers`
--

INSERT INTO `taxpayers` (`taxpayer_id`, `taxpayer_name`, `phone_number`, `email`, `trade_name`, `address`, `registration_date`, `registration_type`, `is_service`, `is_goods`, `designated_directorate`, `nature_of_activity`, `registration_number`, `national_establishment_number`, `date_of_establishment`, `taxpayer_classification`, `password`) VALUES
(69, 'عمر ', '09879', 'omar@gmail.com', 'asd', 'asd', NULL, 'تسجيل جديد', NULL, NULL, 'ششش', 'تجاري', '203847', '1234', '2026-05-05', 'تضامن توصية بسيطة', '$2y$10$tzpT6Yf4LlAQQuK1DOQ5PuoRZXPgtyuDSILgXkNoCBW3K3/JPHlBi');

-- --------------------------------------------------------

--
-- Table structure for table `tax_declaration`
--

CREATE TABLE `tax_declaration` (
  `declaration_id` int(11) NOT NULL,
  `taxpayer_id` int(11) DEFAULT NULL,
  `declaration_type` varchar(50) DEFAULT NULL,
  `period` varchar(50) DEFAULT NULL,
  `balance_previous_period` decimal(10,2) DEFAULT NULL,
  `sales_percent` decimal(15,3) DEFAULT NULL,
  `tax_on_percent` decimal(10,2) DEFAULT NULL,
  `amend_for_registration` decimal(10,2) DEFAULT NULL,
  `amend_for_department` decimal(10,2) DEFAULT NULL,
  `positive_tax_due` decimal(10,2) DEFAULT NULL,
  `negative_tax_due` decimal(10,2) DEFAULT NULL,
  `submit_date` date DEFAULT NULL,
  `declaration_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `taxpayer_id` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `installment`
--
ALTER TABLE `installment`
  ADD PRIMARY KEY (`installment_id`),
  ADD KEY `taxpayer_id` (`taxpayer_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `taxpayer_id` (`taxpayer_id`);

--
-- Indexes for table `registration_cancel`
--
ALTER TABLE `registration_cancel`
  ADD PRIMARY KEY (`cancel_id`),
  ADD KEY `taxpayer_id` (`taxpayer_id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `taxpayer_id` (`taxpayer_id`),
  ADD KEY `declaration_id` (`declaration_id`),
  ADD KEY `installment_id` (`installment_id`);

--
-- Indexes for table `taxpayers`
--
ALTER TABLE `taxpayers`
  ADD PRIMARY KEY (`taxpayer_id`);

--
-- Indexes for table `tax_declaration`
--
ALTER TABLE `tax_declaration`
  ADD PRIMARY KEY (`declaration_id`),
  ADD KEY `taxpayer_id` (`taxpayer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `taxpayer_id` (`taxpayer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `installment`
--
ALTER TABLE `installment`
  MODIFY `installment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registration_cancel`
--
ALTER TABLE `registration_cancel`
  MODIFY `cancel_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_declaration`
--
ALTER TABLE `tax_declaration`
  MODIFY `declaration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `installment`
--
ALTER TABLE `installment`
  ADD CONSTRAINT `installment_ibfk_1` FOREIGN KEY (`taxpayer_id`) REFERENCES `taxpayers` (`taxpayer_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`taxpayer_id`) REFERENCES `taxpayers` (`taxpayer_id`) ON DELETE CASCADE;

--
-- Constraints for table `registration_cancel`
--
ALTER TABLE `registration_cancel`
  ADD CONSTRAINT `registration_cancel_ibfk_1` FOREIGN KEY (`taxpayer_id`) REFERENCES `taxpayers` (`taxpayer_id`) ON DELETE CASCADE;

--
-- Constraints for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`taxpayer_id`) REFERENCES `taxpayers` (`taxpayer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_requests_ibfk_2` FOREIGN KEY (`declaration_id`) REFERENCES `tax_declaration` (`declaration_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_requests_ibfk_3` FOREIGN KEY (`installment_id`) REFERENCES `installment` (`installment_id`) ON DELETE SET NULL;

--
-- Constraints for table `tax_declaration`
--
ALTER TABLE `tax_declaration`
  ADD CONSTRAINT `tax_declaration_ibfk_1` FOREIGN KEY (`taxpayer_id`) REFERENCES `taxpayers` (`taxpayer_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`taxpayer_id`) REFERENCES `taxpayers` (`taxpayer_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
