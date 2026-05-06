-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 08:14 PM
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
-- Database: `clothes1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_master`
--

CREATE TABLE `admin_master` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_master`
--

INSERT INTO `admin_master` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `brand_master`
--

CREATE TABLE `brand_master` (
  `bid` int(11) NOT NULL,
  `bname` varchar(100) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand_master`
--

INSERT INTO `brand_master` (`bid`, `bname`, `logo`, `status`) VALUES
(4, 'Armani Exchange', '1773116980_armani exchange logo.webp', 1),
(5, 'Hugo Boss', '1773117000_hugo boss logo.webp', 1),
(6, 'Fred Perry', '1773117025_fred perry logo.webp', 1),
(7, 'Ralph Lauren', '1773117045_ralph-lauren logo.jpg', 1),
(8, 'Louis Vuitton', '1773117070_lv logo.webp', 1),
(9, 'Hackett', '1773117082_hackett logo.webp', 1),
(10, 'Gucci', '1773117168_Gucci loogo.webp', 1),
(12, 'Prada', '1776670084_prada logo.webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_name`, `price`, `quantity`, `created_at`, `status`) VALUES
(14, 3, 5, 'Tshirt', 2500.00, 1, '2026-04-20 08:10:27', 1);

-- --------------------------------------------------------

--
-- Table structure for table `category_master`
--

CREATE TABLE `category_master` (
  `cid` int(11) NOT NULL,
  `cname` varchar(100) NOT NULL,
  `photo` varchar(100) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_master`
--

INSERT INTO `category_master` (`cid`, `cname`, `photo`, `status`) VALUES
(15, 'men\'s wear', '1773120412_mens wear.webp', 1),
(16, 'women\'s wear', '1773120478_womens wear.jpg', 1),
(17, 'wedding wear', '1773120490_wedding wear.webp', 1),
(18, 'wedding', '1776672793_wedding wear.webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_history`
--

CREATE TABLE `order_history` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `zipcode` varchar(10) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `order_date` datetime NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_history`
--

INSERT INTO `order_history` (`order_id`, `user_id`, `user_name`, `address`, `zipcode`, `phone`, `email`, `total`, `order_date`, `status`) VALUES
(1, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 2026.00, '1970-01-01 01:00:00', 1),
(2, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 2026.00, '1970-01-01 01:00:00', 1),
(3, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 2026.00, '1970-01-01 01:00:00', 1),
(4, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 2026.00, '1970-01-01 01:00:00', 1),
(5, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 2026.00, '1970-01-01 01:00:00', 1),
(6, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 2026.00, '1970-01-01 01:00:00', 1),
(7, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 2026.00, '1970-01-01 01:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_master`
--

CREATE TABLE `order_master` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_master`
--

INSERT INTO `order_master` (`order_id`, `user_id`, `user_name`, `address`, `zipcode`, `phone`, `email`, `total`, `order_date`, `status`) VALUES
(1, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 2500.00, '2026-04-11 00:00:00', 0),
(2, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 2500.00, '2026-04-11 00:00:00', 0),
(3, 2, 'mujeeb', 'surat', '395005', '5256156122', 'muju@gmail.com', 7500.00, '2026-04-19 00:00:00', 0),
(4, 2, 'mujeeb', 'surat', '395005', '5256156122', 'muju@gmail.com', 7500.00, '2026-04-19 00:00:00', 0),
(5, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 5000.00, '2026-04-20 00:00:00', 0),
(6, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 7000.00, '2026-04-20 00:00:00', 1),
(7, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 7000.00, '2026-04-20 00:00:00', 1),
(8, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 4500.00, '2026-04-20 00:00:00', 0),
(9, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 4500.00, '2026-04-20 00:00:00', 0),
(10, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 4500.00, '2026-04-20 00:00:00', 0),
(11, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 7000.00, '2026-04-20 00:00:00', 0),
(12, 3, 'aftab', 'surat', '395005', '5256156111', 'aftab@gmail.com', 2500.00, '2026-04-20 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_master`
--

CREATE TABLE `product_master` (
  `pid` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `pname` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `size` varchar(100) NOT NULL,
  `price` double NOT NULL,
  `qty` int(11) NOT NULL,
  `photo` varchar(100) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_master`
--

INSERT INTO `product_master` (`pid`, `bid`, `sid`, `pname`, `description`, `size`, `price`, `qty`, `photo`, `status`) VALUES
(4, 10, 3, 'Tshirt', 'top wear', '', 2000, 1, '1773121213_69afaebdd6abb.jpeg', 1),
(5, 8, 3, 'Tshirt', 'top wear', '', 2500, 1, '1773121263_69afaeefa3ca1.jpeg', 1),
(6, 4, 3, 'Tshirt', 'This is Men\'s Top\'s wear', '', 2500, 1, '1773128857_69afcc9929c4b.jpeg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subcategory_master`
--

CREATE TABLE `subcategory_master` (
  `sid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `sname` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `photo` varchar(100) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcategory_master`
--

INSERT INTO `subcategory_master` (`sid`, `cid`, `sname`, `description`, `photo`, `status`) VALUES
(3, 15, 'Top\'s Wear', 'This is Men\'s Top\'s wear', '1773120755_WhatsApp Image 2026-03-10 at 9.44.18 AM.jpeg', 1),
(4, 15, 'Bottom\'s wear', 'This is Men\'s Bottom wear', '1773120861_WhatsApp Image 2026-03-10 at 11.04.09 AM.jpeg', 1),
(5, 17, 'For Men', 'This is Men\'s Wedding wear', '1773120976_OIP.webp', 1),
(6, 17, 'For Women', 'This is Women\'s Wedding wear', '1773121027_OIP (1).webp', 1),
(7, 16, 'Top wear', 'This is Women\'s Top wear', '1773130962_OIP (1).webp', 1),
(8, 16, 'Bottom Wear', 'bottom wear ', '1776669441_womens wear.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--

CREATE TABLE `user_master` (
  `uid` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobno` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_master`
--

INSERT INTO `user_master` (`uid`, `fname`, `lname`, `email`, `mobno`, `pass`, `gender`, `status`) VALUES
(2, 'muju', 'chana', 'muju@gmail.com', '5256156122', 'mmmm1111', 'male', 1),
(3, 'aftab', 'amreli', 'aftab@gmail.com', '5256156111', 'aaaa1111', 'male', 1),
(4, 'anas', 'adari', 'anas@gmail.com', '5965426541', 'nnnn1111', 'male', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_master`
--
ALTER TABLE `admin_master`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `brand_master`
--
ALTER TABLE `brand_master`
  ADD PRIMARY KEY (`bid`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_master`
--
ALTER TABLE `category_master`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_master`
--
ALTER TABLE `order_master`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `product_master`
--
ALTER TABLE `product_master`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `bid` (`bid`),
  ADD KEY `sid` (`sid`);

--
-- Indexes for table `subcategory_master`
--
ALTER TABLE `subcategory_master`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `cid` (`cid`);

--
-- Indexes for table `user_master`
--
ALTER TABLE `user_master`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_master`
--
ALTER TABLE `admin_master`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brand_master`
--
ALTER TABLE `brand_master`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `category_master`
--
ALTER TABLE `category_master`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_history`
--
ALTER TABLE `order_history`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_master`
--
ALTER TABLE `order_master`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_master`
--
ALTER TABLE `product_master`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subcategory_master`
--
ALTER TABLE `subcategory_master`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_master`
--
ALTER TABLE `product_master`
  ADD CONSTRAINT `product_master_ibfk_1` FOREIGN KEY (`bid`) REFERENCES `brand_master` (`bid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_master_ibfk_2` FOREIGN KEY (`sid`) REFERENCES `subcategory_master` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subcategory_master`
--
ALTER TABLE `subcategory_master`
  ADD CONSTRAINT `subcategory_master_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `category_master` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
