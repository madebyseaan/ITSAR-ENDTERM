CREATE DATABASE IF NOT EXISTS `erp_auth`;
USE `erp_auth`;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2026 at 12:47 PM
-- Server version: 12.2.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erp_auth`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_logs`
--

CREATE TABLE `access_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `login_time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
(1, 'Admin', 'Full Access'),
(2, 'Cashier', 'POS and Catalog Search'),
(3, 'Customer', 'Online Shopper'),
(4, 'Fulfillment', 'Pending Orders and Shipping'),
(5, 'Stock_Clerk', 'Inventory Viewer and Stock Counter'),
(6, 'Supervisor', 'All Staff features plus Refunds and Sales');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `action_message` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`id`, `action_message`, `created_at`) VALUES
(1, 'New order placed (Order ID: 17)', '2026-05-24 06:54:21'),
(2, 'New order placed (Order ID: 18)', '2026-05-24 08:43:07'),
(3, 'New order placed (Order ID: 19)', '2026-05-24 09:39:12'),
(4, 'New order placed (Order ID: 20)', '2026-05-24 09:39:25'),
(5, 'New order placed (Order ID: 21)', '2026-05-24 09:44:26'),
(6, 'New order placed (Order ID: 22)', '2026-05-24 10:23:25'),
(7, 'New order placed (Order ID: 23)', '2026-05-24 10:24:08'),
(8, 'New order placed (Order ID: 24)', '2026-05-24 10:36:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `username`, `password_hash`, `role_id`) VALUES
(2, 'Kate Nicole', 'katenicolebermejo84@gmail.com', 'hihello05', 'katenicolebermejo84@gmail.com', 'hihello05', 3),
(3, 'Jasmine Marie Bermejo', 'cashier@bookhive.com', 'password123', NULL, NULL, 2),
(4, 'Miguel Santos', 'admin@bookhive.com', 'password123', 'admin@bookhive.com', NULL, 1),
(5, 'Carmela Mendoza', 'cashier@bookhive.com', 'password123', 'cashier@bookhive.com', NULL, 2),
(6, 'Jeric Bautista', 'fulfillment@bookhive.com', 'password123', 'fulfillment@bookhive.com', NULL, 4),
(7, 'Paolo Reyes', 'stockclerk@bookhive.com', 'password123', 'stockclerk@bookhive.com', NULL, 5),
(8, 'Teresa Magbanua', 'supervisor@bookhive.com', 'password123', 'supervisor@bookhive.com', NULL, 6),
(9, 'James Dela Cruz', 'james@gmail.com', 'password123', 'james@gmail.com', NULL, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
