-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 17, 2026 at 11:20 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cake_cafe_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `mobile`) VALUES
(1, 'Nirav Thakur', 'niravthakur2005@gmail.com', '09653472213'),
(2, 'Swara Thakur', 'swara2009@gmail.com', '09876543210'),
(3, 'Pushkar Sharma', 'pushkar@gmail.com', '09123456789'),
(4, 'Vikram Das', 'vikram.das@gmail.com', '09765432100'),
(5, 'Ramesh Yadav', 'rameshyadav@gmail.com', '09112233445'),
(6, 'Aarav Patel', 'aarav.patel@gmail.com', '09987654321'),
(7, 'Sanya Mehra', 'sanya.mehra@gmail.com', '09812345678'),
(8, 'Karan Verma', 'karan.verma@gmail.com', '09112233446'),
(9, 'Maya Joshi', 'maya.joshi@gmail.com', '09221133445'),
(10, 'Ishaan Reddy', 'ishaan.reddy@gmail.com', '09334455667'),
(11, 'Tanya Kapoor', 'tanya.kapoor@gmail.com', '09445566778'),
(12, 'Rohit Singh', 'rohit.singh@gmail.com', '09556677889'),
(13, 'Neha Sharma', 'neha.sharma@gmail.com', '09667788990'),
(14, 'Aditya Kumar', 'aditya.kumar@gmail.com', '09778899001'),
(15, 'Pooja Agarwal', 'pooja.agarwal@gmail.com', '09889900112'),
(16, 'Guest', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `customer_id`, `item_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 1, 5, 'The Red Velvet slice was heavenly! Best in the city.', '2025-12-16 14:22:00'),
(2, 2, 2, 4, 'Rich and decadent chocolate fudge, slightly too sweet for my taste.', '2025-12-17 10:45:00'),
(3, 3, 3, 5, 'Perfectly layered Black Forest, the cream was absolutely divine.', '2025-12-18 11:30:00'),
(4, 4, 5, 5, 'Smooth and creamy cheesecake, made my birthday celebration extra special!', '2026-01-03 16:10:00'),
(5, 5, 6, 4, 'Classic tiramisu done right, coffee flavour was perfect.', '2025-12-18 09:50:00'),
(6, 6, 7, 5, 'Buttery and flaky croissant, just like the ones in Paris!', '2025-12-25 13:15:00'),
(7, 7, 8, 4, 'Chocolate eclair was an absolute treat, filling was generous.', '2026-01-23 15:40:00'),
(8, 8, 9, 3, 'Good blueberry muffin but a bit dry on top, needs more moisture.', '2026-01-06 12:20:00'),
(9, 9, 11, 5, 'Strong and aromatic espresso, exactly how I like my morning shot.', '2025-12-23 08:55:00'),
(10, 10, 12, 4, 'Great foam art on the cappuccino, very smooth and creamy.', '2025-12-27 10:30:00'),
(11, 11, 13, 5, 'Creamy and well-balanced latte, this has become my go-to drink here.', '2025-12-29 16:45:00'),
(12, 12, 15, 5, 'Rich hot chocolate on a cold December evening, absolute perfection!', '2026-01-11 17:00:00'),
(13, 1, 4, 4, 'Light and airy vanilla buttercream cake, loved the subtle flavour.', '2026-01-03 14:35:00'),
(14, 2, 18, 4, 'Crispy club sandwich with fresh ingredients, great for a quick lunch.', '2025-12-21 12:10:00'),
(15, 3, 19, 5, 'Fresh tomatoes and basil on the bruschetta, absolutely wonderful.', '2026-01-13 11:25:00'),
(16, 6, 21, 5, 'Ordered a whole Red Velvet for my daughter\'s birthday, everyone loved it!', '2025-12-25 19:00:00'),
(17, 5, 10, 3, 'Decent chocolate muffin, but could use more chocolate chips inside.', '2026-01-21 10:15:00'),
(18, 9, 16, 5, 'Perfect iced coffee for a warm afternoon, refreshing and sweet.', '2025-12-23 14:50:00'),
(19, 7, 17, 4, 'Thick and creamy vanilla milkshake, all vanilla lovers should try this.', '2026-01-23 16:20:00'),
(20, 8, 1, 5, 'Second time ordering Red Velvet here, never disappointed!', '2026-01-06 13:40:00'),
(21, 9, 2, 5, 'The chocolate fudge icing is absolutely incredible, wow.', '2026-01-31 11:05:00'),
(22, 10, 6, 4, 'Great tiramisu to share with a friend on a lazy Sunday.', '2025-12-31 15:30:00'),
(23, 11, 14, 4, 'Clean and bold americano, perfect for coffee purists like me.', '2026-02-02 09:20:00'),
(24, 12, 3, 5, 'Beautiful Black Forest cake, the cherry on top sealed the deal!', '2026-01-11 18:10:00'),
(25, 13, 5, 5, 'New York style cheesecake done right, I could not stop eating.', '2026-01-13 14:55:00'),
(26, 14, 7, 4, 'Warm croissant paired with butter is truly unbeatable.', '2026-01-19 08:30:00'),
(27, 15, 8, 5, 'Melt-in-mouth chocolate eclair, definitely ordering again next week!', '2026-01-26 12:45:00'),
(28, 1, 12, 5, 'Best cappuccino I have ever had in Hyderabad, hands down.', '2026-02-01 10:00:00'),
(29, 2, 15, 4, 'Warm and comforting hot chocolate, nice little marshmallows on top.', '2026-01-16 17:20:00'),
(30, 3, 18, 4, 'Solid lunch option every time, always fresh and well-made.', '2026-01-09 13:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `issued_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `order_id`, `file_path`, `issued_at`) VALUES
(1, 1, 'invoices/invoice_1.pdf', '2025-12-15 14:30:00'),
(2, 2, 'invoices/invoice_2.pdf', '2025-12-16 11:20:00'),
(3, 3, 'invoices/invoice_3.pdf', '2025-12-17 10:45:00'),
(4, 5, 'invoices/invoice_5.pdf', '2025-12-20 16:10:00'),
(5, 7, 'invoices/invoice_7.pdf', '2025-12-24 12:00:00'),
(6, 8, 'invoices/invoice_8.pdf', '2025-12-26 13:25:00'),
(7, 9, 'invoices/invoice_9.pdf', '2025-12-28 15:50:00'),
(8, 10, 'invoices/invoice_10.pdf', '2025-12-30 11:15:00'),
(9, 15, 'invoices/invoice_15.pdf', '2026-01-12 14:00:00'),
(10, 19, 'invoices/invoice_19.pdf', '2026-01-22 10:30:00'),
(11, 23, 'invoices/invoice_23.pdf', '2026-02-01 09:45:00'),
(12, 24, 'invoices/invoice_24.pdf', '2026-02-02 16:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `cost` decimal(10,2) DEFAULT NULL,
  `margin` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `price`, `category`, `is_active`, `cost`, `margin`, `created_at`) VALUES
(1, 'Red Velvet Cake (Slice)', 280.00, 'Cakes', 1, 160.00, 120.00, '2025-11-10 09:00:00'),
(2, 'Chocolate Fudge Cake (Slice)', 250.00, 'Cakes', 1, 140.00, 110.00, '2025-11-10 09:00:00'),
(3, 'Black Forest Cake (Slice)', 260.00, 'Cakes', 1, 150.00, 110.00, '2025-11-10 09:00:00'),
(4, 'Vanilla Buttercream Cake (Slice)', 240.00, 'Cakes', 1, 130.00, 110.00, '2025-11-10 09:00:00'),
(5, 'Cheesecake (Slice)', 320.00, 'Cakes', 1, 200.00, 120.00, '2025-11-10 09:00:00'),
(6, 'Tiramisu', 350.00, 'Cakes', 1, 210.00, 140.00, '2025-11-10 09:00:00'),
(7, 'Croissant', 120.00, 'Pastries', 1, 60.00, 60.00, '2025-11-10 09:00:00'),
(8, 'Chocolate Eclair', 150.00, 'Pastries', 1, 80.00, 70.00, '2025-11-10 09:00:00'),
(9, 'Blueberry Muffin', 130.00, 'Pastries', 1, 65.00, 65.00, '2025-11-10 09:00:00'),
(10, 'Chocolate Muffin', 120.00, 'Pastries', 1, 60.00, 60.00, '2025-11-10 09:00:00'),
(11, 'Espresso', 180.00, 'Coffee', 1, 40.00, 140.00, '2025-11-10 09:00:00'),
(12, 'Cappuccino', 200.00, 'Coffee', 1, 50.00, 150.00, '2025-11-10 09:00:00'),
(13, 'Latte', 190.00, 'Coffee', 1, 45.00, 145.00, '2025-11-10 09:00:00'),
(14, 'Americano', 160.00, 'Coffee', 1, 35.00, 125.00, '2025-11-10 09:00:00'),
(15, 'Hot Chocolate', 220.00, 'Beverages', 1, 60.00, 160.00, '2025-11-10 09:00:00'),
(16, 'Iced Coffee', 210.00, 'Beverages', 1, 55.00, 155.00, '2025-11-10 09:00:00'),
(17, 'Vanilla Milkshake', 230.00, 'Beverages', 1, 70.00, 160.00, '2025-11-10 09:00:00'),
(18, 'Club Sandwich', 280.00, 'Snacks', 1, 140.00, 140.00, '2025-11-12 10:00:00'),
(19, 'Bruschetta', 200.00, 'Snacks', 1, 90.00, 110.00, '2025-11-12 10:00:00'),
(20, 'Packing Charge', 15.00, 'Misc', 1, 0.00, 15.00, '2025-11-10 09:00:00'),
(21, 'Whole Red Velvet Cake', 1800.00, 'Cakes', 1, 900.00, 900.00, '2025-11-15 11:00:00'),
(22, 'Whole Chocolate Cake', 1600.00, 'Cakes', 0, 800.00, 800.00, '2025-11-15 11:00:00'),
(23, 'hey hey', 11.00, NULL, 0, NULL, NULL, '2026-02-03 19:12:19');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_time` datetime DEFAULT current_timestamp(),
  `status` enum('Preparing','Completed','Pending','Cancelled') DEFAULT 'Pending',
  `total` decimal(10,2) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT 'Cash',
  `amount_taken` decimal(10,2) DEFAULT 0.00,
  `change_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `order_time`, `status`, `total`, `user_id`, `payment_mode`, `amount_taken`, `change_amount`) VALUES
(1, 1, '2025-12-15 14:20:00', 'Completed', 960.00, 3, 'Cash', 1000.00, 40.00),
(2, 3, '2025-12-16 11:10:00', 'Completed', 560.00, 7, 'Cash', 600.00, 40.00),
(3, 5, '2025-12-17 10:30:00', 'Completed', 480.00, 3, 'UPI', 480.00, 0.00),
(4, 7, '2025-12-18 09:40:00', 'Cancelled', 700.00, 4, 'Cash', 0.00, 0.00),
(5, 2, '2025-12-20 15:55:00', 'Completed', 610.00, 3, 'Cash', 700.00, 90.00),
(6, 9, '2025-12-22 13:20:00', 'Completed', 680.00, 7, 'UPI', 680.00, 0.00),
(7, 6, '2025-12-24 11:50:00', 'Completed', 1815.00, 3, 'Cash', 2000.00, 185.00),
(8, 10, '2025-12-26 13:15:00', 'Completed', 760.00, 4, 'Cash', 800.00, 40.00),
(9, 11, '2025-12-28 15:40:00', 'Completed', 640.00, 3, 'UPI', 640.00, 0.00),
(10, 1, '2025-12-30 11:00:00', 'Completed', 850.00, 7, 'Cash', 1000.00, 150.00),
(11, 4, '2026-01-02 10:25:00', 'Completed', 740.00, 3, 'Cash', 800.00, 60.00),
(12, 8, '2026-01-05 14:10:00', 'Completed', 820.00, 4, 'UPI', 820.00, 0.00),
(13, 3, '2026-01-08 12:45:00', 'Completed', 700.00, 7, 'Cash', 1000.00, 300.00),
(14, 12, '2026-01-10 16:30:00', 'Preparing', 760.00, 3, 'Cash', 800.00, 40.00),
(15, 13, '2026-01-12 13:50:00', 'Completed', 740.00, 3, 'UPI', 740.00, 0.00),
(16, 2, '2026-01-15 09:15:00', 'Completed', 620.00, 7, 'Cash', 700.00, 80.00),
(17, 14, '2026-01-18 17:20:00', 'Completed', 1080.00, 4, 'Cash', 1100.00, 20.00),
(18, 5, '2026-01-20 11:40:00', 'Pending', 680.00, 3, 'Cash', 700.00, 20.00),
(19, 6, '2026-01-22 10:20:00', 'Completed', 730.00, 7, 'UPI', 730.00, 0.00),
(20, 15, '2026-01-25 14:55:00', 'Completed', 590.00, 3, 'Cash', 600.00, 10.00),
(21, 16, '2026-01-28 12:00:00', 'Completed', 470.00, 4, 'Cash', 500.00, 30.00),
(22, 9, '2026-01-30 16:35:00', 'Completed', 595.00, 3, 'UPI', 595.00, 0.00),
(23, 1, '2026-02-01 09:30:00', 'Completed', 1040.00, 7, 'Cash', 1100.00, 60.00),
(24, 11, '2026-02-02 16:10:00', 'Completed', 740.00, 3, 'Cash', 800.00, 60.00),
(25, 7, '2026-02-03 10:45:00', 'Preparing', 1815.00, 4, 'Cash', 2000.00, 185.00),
(26, 16, '2026-02-03 19:08:47', 'Pending', 590.00, 9, 'Cash', 1500.00, 910.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`, `price`, `total`) VALUES
(1, 1, 1, 2, 280.00, 560.00),
(2, 1, 12, 2, 200.00, 400.00),
(3, 2, 2, 1, 250.00, 250.00),
(4, 2, 13, 1, 190.00, 190.00),
(5, 2, 7, 1, 120.00, 120.00),
(6, 3, 5, 1, 320.00, 320.00),
(7, 3, 14, 1, 160.00, 160.00),
(8, 4, 6, 2, 350.00, 700.00),
(9, 5, 4, 1, 240.00, 240.00),
(10, 5, 15, 1, 220.00, 220.00),
(11, 5, 8, 1, 150.00, 150.00),
(12, 6, 3, 1, 260.00, 260.00),
(13, 6, 16, 2, 210.00, 420.00),
(14, 7, 21, 1, 1800.00, 1800.00),
(15, 7, 20, 1, 15.00, 15.00),
(16, 8, 18, 2, 280.00, 560.00),
(17, 8, 12, 1, 200.00, 200.00),
(18, 9, 9, 2, 130.00, 260.00),
(19, 9, 13, 2, 190.00, 380.00),
(20, 10, 5, 1, 320.00, 320.00),
(21, 10, 6, 1, 350.00, 350.00),
(22, 10, 11, 1, 180.00, 180.00),
(23, 11, 1, 1, 280.00, 280.00),
(24, 11, 7, 2, 120.00, 240.00),
(25, 11, 15, 1, 220.00, 220.00),
(26, 12, 2, 2, 250.00, 500.00),
(27, 12, 14, 2, 160.00, 320.00),
(28, 13, 4, 1, 240.00, 240.00),
(29, 13, 17, 2, 230.00, 460.00),
(30, 14, 3, 1, 260.00, 260.00),
(31, 14, 8, 2, 150.00, 300.00),
(32, 14, 12, 1, 200.00, 200.00),
(33, 15, 6, 1, 350.00, 350.00),
(34, 15, 19, 1, 200.00, 200.00),
(35, 15, 13, 1, 190.00, 190.00),
(36, 16, 1, 1, 280.00, 280.00),
(37, 16, 9, 1, 130.00, 130.00),
(38, 16, 16, 1, 210.00, 210.00),
(39, 17, 5, 2, 320.00, 640.00),
(40, 17, 15, 2, 220.00, 440.00),
(41, 18, 18, 1, 280.00, 280.00),
(42, 18, 10, 2, 120.00, 240.00),
(43, 18, 14, 1, 160.00, 160.00),
(44, 19, 2, 1, 250.00, 250.00),
(45, 19, 11, 2, 180.00, 360.00),
(46, 19, 7, 1, 120.00, 120.00),
(47, 20, 4, 1, 240.00, 240.00),
(48, 20, 8, 1, 150.00, 150.00),
(49, 20, 12, 1, 200.00, 200.00),
(50, 21, 1, 1, 280.00, 280.00),
(51, 21, 13, 1, 190.00, 190.00),
(52, 22, 6, 1, 350.00, 350.00),
(53, 22, 17, 1, 230.00, 230.00),
(54, 22, 20, 1, 15.00, 15.00),
(55, 23, 3, 2, 260.00, 520.00),
(56, 23, 12, 2, 200.00, 400.00),
(57, 23, 7, 1, 120.00, 120.00),
(58, 24, 5, 1, 320.00, 320.00),
(59, 24, 19, 1, 200.00, 200.00),
(60, 24, 15, 1, 220.00, 220.00),
(61, 25, 21, 1, 1800.00, 1800.00),
(62, 25, 20, 1, 15.00, 15.00),
(63, 26, 5, 1, 320.00, 320.00),
(64, 26, 8, 1, 150.00, 150.00),
(65, 26, 7, 1, 120.00, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Admin','Staff') NOT NULL DEFAULT 'Staff',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `created_at`) VALUES
(3, 'nirav', '$2y$10$FLPM9oerbqdSxKPqdnG4ruZXvOfhHu9psPgtcuvq/91idm50PJg1m', 'Admin', '2025-11-01 09:00:00'),
(4, 'rahul', '$2y$10$Kp3nL8mQ2vTxYz5aB7cDeR9sWj1uI4oN6pHfG0kA8lX2yU5wE3tJ', 'Staff', '2025-11-02 09:00:00'),
(7, 'pushkar', '$2y$10$LQQdxT4GndLjYT9V3pc47.bf.Lxt.2blpnsGhqwD92T9sJoUjTaa2', 'Staff', '2025-11-03 09:00:00'),
(9, 'admin', '$2y$10$cRp.KnQ9JBCfAPjh07rTHuxxguI.OZDnuEPAwAStCRLXsZcrK.Fc6', 'Admin', '2026-02-03 17:53:46'),
(10, 'Rushab', '$2y$10$LcQW9EdcwFSkFeop.brtpud6uOcgkFUVfiMCreZnQivSGeIueCX22', 'Admin', '2026-02-03 17:54:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
