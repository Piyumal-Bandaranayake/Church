-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2026 at 03:31 PM
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
-- Database: `church_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(3, 'admin', 'admin@gracechurch.org', '$2y$10$6ELB8s0s.91QBilMEB0jW.rbweZV76WynHkEfDt41/rVT8Qs3PLnK', '2026-02-19 16:37:09'),
(4, 'admin1', 'piyumalbandaranayake24790@gmail.com', '$2y$10$5k3pfhs6ilYata4C/HyJlOb3g8fVBGf.DUbhZ0bq2kh3d/5lJx2Dy', '2026-02-25 14:17:36');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `denomination` enum('Catholic','Christian') DEFAULT 'Christian',
  `catholic_by_birth` enum('Yes','No') DEFAULT NULL,
  `nic_number` varchar(20) NOT NULL,
  `christianization_year` int(11) DEFAULT NULL,
  `sacraments_received` text DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `dob` date NOT NULL,
  `age` int(11) NOT NULL,
  `nationality` varchar(50) NOT NULL,
  `language` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `hometown` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `height` varchar(20) NOT NULL,
  `occupation` varchar(100) NOT NULL,
  `edu_qual` text DEFAULT NULL,
  `add_qual` text DEFAULT NULL,
  `marital_status` enum('Unmarried','Divorced','Widowed') NOT NULL,
  `children` enum('Yes','No') DEFAULT 'No',
  `children_details` text DEFAULT NULL,
  `illness` text DEFAULT NULL,
  `habits` text DEFAULT NULL,
  `church` varchar(100) NOT NULL,
  `pastor_name` varchar(100) NOT NULL,
  `pastor_phone` varchar(20) NOT NULL,
  `parent_phone` varchar(20) NOT NULL,
  `my_phone` varchar(20) NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user') DEFAULT 'user',
  `partner_found` tinyint(1) DEFAULT 0,
  `partner_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `email`, `password`, `denomination`, `catholic_by_birth`, `nic_number`, `christianization_year`, `sacraments_received`, `fullname`, `sex`, `dob`, `age`, `nationality`, `language`, `address`, `hometown`, `district`, `province`, `height`, `occupation`, `edu_qual`, `add_qual`, `marital_status`, `children`, `children_details`, `illness`, `habits`, `church`, `pastor_name`, `pastor_phone`, `parent_phone`, `my_phone`, `photo_path`, `status`, `created_at`, `role`, `partner_found`, `partner_message`) VALUES
(9, 'piyumalshalinda@gmail.com', '$2y$10$9WpQy5vXUcYXSW65VVjsnuAppSxWNSjrer1dP5izl5fcx.DyUwjyK', 'Catholic', 'Yes', '200305001026', NULL, '', 'kasun silva', 'Male', '2000-02-22', 26, 'sinhala', 'sinhala', '112 kuriwela ukuwela', 'Matale', 'matale', 'central', '5.6', 'sssss', 'ssssssssssss', 'sssssssssssss', 'Unmarried', 'No', '', 'sssssssssssssssss', 'none', 'Christian Reformed Church', 'sssssssss', '0705765890', '0705765890', '0705765890', 'uploads/1772026107_4638e09c.jpg', 'approved', '2026-02-25 13:28:27', 'user', 0, NULL),
(10, 'piyumalshalinda24790@gmail.com', '$2y$10$KRm5D3Nd4Z31sIESUQxekukoS6AaC4PYH1Scs6duOQYlcfjOZUsKa', 'Christian', NULL, '200305601029', NULL, '', 'kalni kavindya', 'Male', '2000-02-22', 26, 'sinhala', 'sinhala', '112 kuriwela ukuwela', 'Matale', 'ssss', 'central', '5.2', 'sssss', 'ssssssssss', 'ssssssssssss', 'Unmarried', 'No', '', 'sssssssssssss', 'none', 'ssssssssss', 'ssssssssssssss', '0705765890', '0705765890', '0705765890', 'uploads/1772026253_88509707.jpg', 'approved', '2026-02-25 13:30:53', 'user', 0, NULL),
(12, 'kasunkalhara@gmial.com', '$2y$10$maQnpaAGDvpjlBpFCOi/nOxiF7N3CoxG8fFBiFzF8JvIkq5da/Xp.', 'Christian', NULL, '202205001226', NULL, '', 'kamala kumari', 'Male', '1995-02-11', 31, 'sinhala', 'sinhala', '12 kumara road kadwath', 'colombo', 'colombo', 'centrel', '5.6', 'sssss', 'ssssssssssssss', 'sssssssssssssssssss', 'Unmarried', 'No', '', 'sssssssssssssssssssssssss', 'none', 'ssssssss', 'sssssssss', '0705765890', '0705765890', '0705765890', 'uploads/1772026934_e425e723.png', 'approved', '2026-02-25 13:42:14', 'user', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `churches`
--

CREATE TABLE `churches` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pastor_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `churches`
--

INSERT INTO `churches` (`id`, `name`, `created_at`, `pastor_name`, `location`) VALUES
(1, 'Anglican Church (Church of Ceylon)', '2026-02-13 08:37:52', NULL, NULL),
(2, 'Antioch Fellowship Churches', '2026-02-13 08:37:52', NULL, NULL),
(3, 'Apostolic Church', '2026-02-13 08:37:52', NULL, NULL),
(4, 'Assemblies of God Sri Lanka (AOG)', '2026-02-13 08:37:52', NULL, NULL),
(5, 'Baptist Church', '2026-02-13 08:37:52', NULL, NULL),
(6, 'Believers\' Church (GFA)', '2026-02-13 08:37:52', NULL, NULL),
(7, 'Bethany Christian Life Centre', '2026-02-13 08:37:52', NULL, NULL),
(8, 'Bethel Church', '2026-02-13 08:37:52', NULL, NULL),
(9, 'Bethesda Hall Assembly Church', '2026-02-13 08:37:52', NULL, NULL),
(10, 'Calvary Church', '2026-02-13 08:37:52', NULL, NULL),
(11, 'Canaan Fellowship International Church', '2026-02-13 08:37:52', NULL, NULL),
(12, 'Christian Fellowship Centre', '2026-02-13 08:37:52', NULL, NULL),
(13, 'Christian Pentacostal Mission', '2026-02-13 08:37:52', NULL, NULL),
(14, 'Christian Reformed Church', '2026-02-13 08:37:52', NULL, NULL),
(15, 'Christ\'s Gospel Church (CGC)', '2026-02-13 08:37:52', NULL, NULL),
(16, 'Church of Christ', '2026-02-13 08:37:52', NULL, NULL),
(17, 'Colombo Gospel Tabernacle', '2026-02-13 08:37:52', NULL, NULL),
(18, 'Cornerstone Church', '2026-02-13 08:37:52', NULL, NULL),
(19, 'Emmanuel Church', '2026-02-13 08:37:52', NULL, NULL),
(20, 'Faith Church', '2026-02-13 08:37:52', NULL, NULL),
(21, 'Foursquare Gospel Churches', '2026-02-13 08:37:52', NULL, NULL),
(22, 'Gethsemane Gospel Church', '2026-02-13 08:37:52', NULL, NULL),
(23, 'Gethsemane Prayer Centre (GPC)', '2026-02-13 08:37:52', NULL, NULL),
(24, 'Glorious Church', '2026-02-13 08:37:52', NULL, NULL),
(25, 'Grace Evangelical Church', '2026-02-13 08:37:52', NULL, NULL),
(26, 'Harvest Church', '2026-02-13 08:37:52', NULL, NULL),
(27, 'House of Prayer Church', '2026-02-13 08:37:52', NULL, NULL),
(28, 'House of Prayer Revival Church', '2026-02-13 08:37:52', NULL, NULL),
(29, 'Jehovah\'s Witnesses', '2026-02-13 08:37:52', NULL, NULL),
(30, 'Kingdom Hall Church', '2026-02-13 08:37:52', NULL, NULL),
(31, 'Kings Revival Church', '2026-02-13 08:37:52', NULL, NULL),
(32, 'Lighthouse Church', '2026-02-13 08:37:52', NULL, NULL),
(33, 'Living Word Church', '2026-02-13 08:37:52', NULL, NULL),
(34, 'Lutheran Church', '2026-02-13 08:37:52', NULL, NULL),
(35, 'Methodist Church', '2026-02-13 08:37:52', NULL, NULL),
(36, 'Miracle-centre churches', '2026-02-13 08:37:52', NULL, NULL),
(37, 'Mount Zion Church', '2026-02-13 08:37:52', NULL, NULL),
(38, 'New Covenant Church', '2026-02-13 08:37:52', NULL, NULL),
(39, 'New Hope Church', '2026-02-13 08:37:52', NULL, NULL),
(40, 'New Life Church', '2026-02-13 08:37:52', NULL, NULL),
(41, 'People’s Church Assembly of God', '2026-02-13 08:37:52', NULL, NULL),
(42, 'Prayer Tower Church', '2026-02-13 08:37:52', NULL, NULL),
(43, 'Redeemed Churches', '2026-02-13 08:37:52', NULL, NULL),
(44, 'Seventh-day Adventist Church', '2026-02-13 08:37:52', NULL, NULL),
(45, 'The Christian Centre', '2026-02-13 08:37:52', NULL, NULL),
(46, 'The Church of Jesus Christ of Latter-day Saints', '2026-02-13 08:37:52', NULL, NULL),
(47, 'The Grace Evangelical Church', '2026-02-13 08:37:52', NULL, NULL),
(48, 'The Salvation Army', '2026-02-13 08:37:52', NULL, NULL),
(49, 'Trumpet of Revival Church', '2026-02-13 08:37:52', NULL, NULL),
(50, 'Trumpet Sound Church', '2026-02-13 08:37:52', NULL, NULL),
(51, 'Victory Life Church', '2026-02-13 08:37:52', NULL, NULL),
(52, 'Worldwide Church of God', '2026-02-13 08:37:52', NULL, NULL),
(53, 'Zion Christian Church', '2026-02-13 08:37:52', NULL, NULL),
(54, 'Zion Christian Community Centre', '2026-02-13 08:37:53', NULL, NULL),
(55, 'Zion Christian Fellowship Church', '2026-02-13 08:37:53', NULL, NULL),
(56, 'Zion Church of God', '2026-02-13 08:37:53', NULL, NULL),
(57, 'Zion Fountain Church', '2026-02-13 08:37:53', NULL, NULL),
(58, 'INDEPENDENT / FREE CHURCH', '2026-02-13 08:37:53', NULL, NULL),
(59, 'HOUSE - CHURCH', '2026-02-13 08:37:53', NULL, NULL),
(60, 'aaaa', '2026-02-13 09:30:50', 'aaaaa', 'aaaaa'),
(61, 'ssssssssssssss', '2026-02-13 10:29:16', 'ssssssssssssssss', 'ssssssssssss');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `image5` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `description`, `image1`, `image2`, `image3`, `image4`, `image5`, `status`, `created_at`) VALUES
(1, 'ssss', 'ssssssssssssssssssss', 'uploads/reviews/review1_6997553bcdd16.jpg', NULL, NULL, NULL, NULL, 'approved', '2026-02-19 18:23:56'),
(2, 'mmmm', 'mmmmm', 'uploads/reviews/review1_69975ab7d12d9.jpg', 'uploads/reviews/review2_69975ab7d30ea.jpg', NULL, NULL, NULL, 'approved', '2026-02-19 18:47:19'),
(3, 'mmmm', 'ssssssssssssssss', 'uploads/reviews/review1_699effd33db03.jpeg', 'uploads/reviews/review2_699effd344a0e.jpeg', NULL, NULL, NULL, 'approved', '2026-02-25 13:57:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`email`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nic_number` (`nic_number`);

--
-- Indexes for table `churches`
--
ALTER TABLE `churches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `churches`
--
ALTER TABLE `churches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
