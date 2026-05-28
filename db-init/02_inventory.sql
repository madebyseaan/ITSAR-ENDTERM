CREATE DATABASE IF NOT EXISTS `erp_inventory`;
USE `erp_inventory`;

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
-- Database: `erp_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `full_name`) VALUES
(1, 'John Doe'),
(2, 'Jane Smith'),
(3, 'F. Scott Fitzgerald'),
(4, 'Robert Kiyosaki'),
(5, 'Frank Herbert'),
(6, 'Robert C. Martin');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `isbn`, `title`, `author`, `author_id`, `category_id`, `price`, `stock_quantity`) VALUES
(1, '978-013', 'PHP for Beginners', 'Jon Duckett', 1, 1, 850.00, 39),
(2, '978-144', 'Advanced Microservices', 'Sam Newman', 2, 1, 1200.00, 5),
(3, '978-333', 'The Great Gatsby', 'F. Scott Fitzgerald', 3, 2, 550.00, 16),
(4, '978-444', 'Rich Dad Poor Dad', 'Robert Kiyosaki', 4, 3, 650.00, 44),
(5, '978-555', 'Dune', 'Frank Herbert', 5, 4, 750.00, 3),
(6, '978-666', 'Clean Code', 'Robert C. Martin', 6, 1, 950.00, 0),
(7, '978-777', '1984', 'George Orwell', 7, 2, 450.00, 99),
(21, '978-888', 'The Pragmatic Programmer', 'Andrew Hunt', 8, 1, 1150.00, 15),
(22, '978-999', 'Atomic Habits', 'James Clear', 9, 3, 599.00, 54),
(23, '978-101', 'Project Hail Mary', 'Andy Weir', 10, 4, 699.00, 20),
(24, '978-202', 'Thinking, Fast and Slow', 'Daniel Kahneman', 11, 3, 899.00, 35),
(25, '978-111', 'Pride and Prejudice', 'Jane Austen', 13, 2, 499.00, 45),
(26, '978-222', 'The Catcher in the Rye', 'J.D. Salinger', 14, 2, 450.00, 30),
(27, '978-606', 'The Hobbit', 'J.R.R. Tolkien', 17, 2, 750.00, 60),
(28, '978-909', 'Crime and Punishment', 'Fyodor Dostoevsky', 20, 2, 850.00, 15),
(29, '978-400', 'Designing Data-Intensive Applications', 'Martin Kleppmann', 21, 1, 1350.00, 10),
(30, '978-401', 'Deep Work', 'Cal Newport', 22, 3, 580.00, 40),
(31, '978-402', 'Brave New World', 'Aldous Huxley', 23, 2, 480.00, 35),
(32, '978-403', 'The Lean Startup', 'Eric Ries', 24, 3, 720.00, 25),
(33, '978-404', 'Neuromancer', 'William Gibson', 25, 4, 650.00, 18),
(34, '978-405', 'Python Crash Course', 'Eric Matthes', 26, 1, 980.00, 50),
(35, '978-406', 'Meditations', 'Marcus Aurelius', 27, 3, 420.00, 60),
(36, '978-407', 'Foundation', 'Isaac Asimov', 28, 4, 599.00, 22);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Programming'),
(2, 'Fiction'),
(3, 'Business'),
(4, 'Sci-Fi & Fantasy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
