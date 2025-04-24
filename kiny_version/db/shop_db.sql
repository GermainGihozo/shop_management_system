-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2025 at 06:34 PM
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
-- Database: `shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`) VALUES
(1, 'Himbazwa'),
(2, 'Muhoza'),
(3, 'imbere'),
(4, 'b'),
(5, 'kwa Baptist');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `low_stock_threshold` int(11) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `branch_id`, `name`, `price`, `quantity`, `low_stock_threshold`, `created_at`) VALUES
(1, 2, 'perfum', 3000.00, 19, 5, '2025-04-07 18:30:00'),
(2, 2, 'boxers', 1500.00, 20, 5, '2025-04-07 18:31:22'),
(3, 2, 'socks', 500.00, 16, 5, '2025-04-07 18:42:33'),
(4, 2, 'isaha', 10000.00, 5, 5, '2025-04-10 14:45:39'),
(5, 4, 'Isengeri', 2000.00, 18, 5, '2025-04-10 16:59:14'),
(6, 2, 'Ti', 1500.00, 50, 5, '2025-04-11 11:42:02'),
(7, 4, 'flat cap', 5000.00, 14, 5, '2025-04-11 12:48:04'),
(8, 5, 'Ingofero flat cap', 5000.00, 8, 5, '2025-04-11 16:36:00'),
(9, 5, 'suve', 2000.00, 10, 5, '2025-04-11 16:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_each` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `sold_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `branch_id`, `quantity`, `price_each`, `total_price`, `sold_at`) VALUES
(1, 1, 2, 1, 3000.00, 3000.00, '2025-04-11 11:45:08'),
(2, 5, 4, 2, 2000.00, 4000.00, '2025-04-11 12:48:58'),
(3, 7, 4, 1, 5000.00, 5000.00, '2025-04-11 12:52:09'),
(4, 8, 5, 2, 5000.00, 10000.00, '2025-04-11 16:37:55'),
(5, 3, 2, 2, 500.00, 1000.00, '2025-04-15 14:38:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','branch') NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `branch_id`, `status`, `full_name`, `created_at`) VALUES
(1, 'Himbazwa', '$2y$10$7SV.OJeuKGIaubx.YC28b.juqqkBaxF/RMXe5tGbaFGZYClOm9zzm', 'admin', 1, 'active', 'Himbazwa Germain', '2025-04-07 13:37:53'),
(2, 'Muhoza', '$2y$10$DAwwCeBi3UQN9EhalK7RLuXrw7tPTeyUPf.k/Jx5feGDNzRpN7FJq', 'branch', 2, 'active', 'Muhoza', '2025-04-07 13:47:23'),
(3, 'Gihozo', '$2y$10$GPvx/ySbDNky/mMwzwHEYOiFK4gc0QzahNDz12TomkOP2pzV7RbMm', 'branch', 3, 'active', 'Gihozo', '2025-04-07 17:34:26'),
(4, 'simeon', '$2y$10$jqCqHVN4qFwcKcphuY.rIeMLdGT0ZowojF3UTAFjuR..UATav6De6', 'branch', 4, 'active', 'simeon', '2025-04-10 16:57:17'),
(5, 'Baptist', '$2y$10$GoVcZ1gWNVs/qd2vSVLj8uVot187m401fDZtdq7tASjKGiOphIWNW', 'branch', 5, 'active', 'Baptist', '2025-04-11 16:34:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `target_user_id` int(11) NOT NULL,
  `action` enum('activated','deactivated') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_logs`
--

INSERT INTO `user_logs` (`id`, `admin_id`, `target_user_id`, `action`, `timestamp`) VALUES
(1, 1, 2, 'deactivated', '2025-04-07 16:19:08'),
(2, 1, 2, 'activated', '2025-04-07 16:19:10'),
(3, 1, 2, 'deactivated', '2025-04-07 16:21:30'),
(4, 1, 2, 'activated', '2025-04-07 16:21:31'),
(5, 1, 2, 'deactivated', '2025-04-07 16:21:33'),
(6, 1, 2, 'activated', '2025-04-07 16:21:33'),
(7, 1, 1, 'deactivated', '2025-04-07 16:23:03'),
(8, 1, 1, 'activated', '2025-04-07 16:23:06'),
(9, 1, 2, 'deactivated', '2025-04-07 16:23:09'),
(10, 1, 2, 'activated', '2025-04-07 16:23:10'),
(11, 1, 1, 'deactivated', '2025-04-07 16:23:11'),
(12, 1, 1, 'activated', '2025-04-07 16:23:11'),
(13, 1, 1, 'deactivated', '2025-04-07 16:25:00'),
(14, 1, 1, 'activated', '2025-04-07 16:25:02'),
(15, 1, 2, 'deactivated', '2025-04-07 16:25:22'),
(16, 1, 2, 'activated', '2025-04-07 16:28:42'),
(17, 1, 2, 'deactivated', '2025-04-07 17:13:21'),
(18, 1, 2, 'activated', '2025-04-07 17:13:37'),
(19, 1, 3, 'deactivated', '2025-04-11 16:32:50'),
(20, 1, 4, 'deactivated', '2025-04-11 16:32:54'),
(21, 1, 4, 'activated', '2025-04-11 16:32:56'),
(22, 1, 3, 'activated', '2025-04-11 16:34:10'),
(23, 1, 2, 'deactivated', '2025-04-14 12:27:05'),
(24, 1, 2, 'activated', '2025-04-14 12:46:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `target_user_id` (`target_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD CONSTRAINT `user_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_logs_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
