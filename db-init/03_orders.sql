CREATE DATABASE IF NOT EXISTS `erp_orders`;
USE `erp_orders`;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2026 at 12:48 PM
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
-- Database: `erp_orders`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `total_amount`, `status_id`, `created_at`) VALUES
(1, 1, 25.00, 2, '2026-05-24 09:17:47'),
(2, 1, 25.00, 2, '2026-05-24 09:17:47'),
(3, 2, 35.00, 2, '2026-05-24 09:17:47'),
(4, 7, 54.99, 2, '2026-05-24 09:17:47'),
(5, 2, 15.00, 2, '2026-05-24 09:17:47'),
(6, 1, 35.00, 2, '2026-05-24 09:17:47'),
(7, 2, 50.00, 2, '2026-05-24 09:17:47'),
(8, 1, 15.00, 2, '2026-05-24 09:17:47'),
(9, 1, 35.00, 2, '2026-05-24 09:17:47'),
(10, 1, 45.50, 2, '2026-05-24 09:17:47'),
(11, 2, 45.50, 2, '2026-05-24 09:17:47'),
(12, 2, 45.50, 2, '2026-05-24 09:17:47'),
(13, 2, 30.00, 2, '2026-05-24 09:17:47'),
(14, 2, 25.00, 2, '2026-05-24 09:17:47'),
(15, 2, 25.00, 2, '2026-05-24 09:17:47'),
(16, 2, 25.00, 2, '2026-05-24 09:17:47'),
(17, 9, 19.99, 1, '2026-05-24 09:17:47'),
(18, 1, 44.99, 1, '2026-05-24 09:17:47'),
(19, 9, 19.99, 1, '2026-05-24 09:39:12'),
(20, 9, 19.99, 1, '2026-05-24 09:39:25'),
(21, 3, 44.99, 2, '2026-05-24 09:44:26'),
(22, 9, 850.00, 2, '2026-05-24 10:23:25'),
(23, 3, 599.00, 1, '2026-05-24 10:24:08'),
(24, 9, 1200.00, 1, '2026-05-24 10:36:19');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `quantity`, `subtotal`) VALUES
(1, 3, 6, 1, 35.00),
(2, 4, 2, 1, 45.00),
(3, 4, 5, 1, 9.99),
(4, 5, 3, 1, 15.00),
(5, 6, 6, 1, 35.00),
(6, 7, 1, 2, 50.00),
(7, 8, 3, 1, 15.00),
(8, 9, 6, 1, 35.00),
(9, 10, 2, 1, 45.50),
(10, 11, 2, 1, 45.50),
(11, 12, 2, 1, 45.50),
(12, 13, 3, 2, 30.00),
(13, 14, 1, 1, 25.00),
(14, 15, 1, 1, 25.00),
(15, 16, 1, 1, 25.00),
(16, 17, 4, 1, 19.99),
(17, 18, 1, 1, 25.00),
(18, 18, 4, 1, 19.99),
(19, 19, 4, 1, 19.99),
(20, 20, 4, 1, 19.99),
(21, 21, 1, 1, 25.00),
(22, 21, 4, 1, 19.99),
(23, 22, 1, 1, 850.00),
(24, 23, 22, 1, 599.00),
(25, 24, 2, 1, 1200.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_statuses`
--

CREATE TABLE `order_statuses` (
  `id` int(11) NOT NULL,
  `status_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_statuses`
--

INSERT INTO `order_statuses` (`id`, `status_name`) VALUES
(1, 'Pending'),
(2, 'Completed');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
