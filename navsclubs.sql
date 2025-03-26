-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2025 at 03:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25


-- Bagoh naneh nga Database nah Nakah FOREIGN key nah tanan
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `navsclubs`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkout_course`
--

CREATE TABLE `checkout_course` (
  `checkout_id` int(11) NOT NULL,
  `usersid` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `course_name` varchar(255) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `course_price` varchar(255) NOT NULL,
  `course_instructor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_instructor` varchar(255) NOT NULL,
  `course_price` decimal(10,2) NOT NULL,
  `course_level` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `course_duration` time DEFAULT NULL,
  `course_image` mediumblob NOT NULL,
  `course_desc` varchar(255) NOT NULL,
  `course_lessons` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_cart`
--

CREATE TABLE `course_cart` (
  `cart_id` int(11) NOT NULL,
  `usersid` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `course_price` decimal(10,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_orders`
--

CREATE TABLE `course_orders` (
  `courseorder_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_name` varchar(255) NOT NULL,
  `student_email` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_sections`
--

CREATE TABLE `course_sections` (
  `section_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `position` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `data` longblob NOT NULL,
  `downloads` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `my_courses`
--

CREATE TABLE `my_courses` (
  `mycourses_id` int(11) NOT NULL,
  `usersid` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrolled_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(4) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_instructor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `usersid` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `card_number` varchar(16) NOT NULL,
  `expiry_date` varchar(5) NOT NULL,
  `cvv` varchar(3) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `course_name` varchar(255) NOT NULL,
  `payment_status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_information`
--

CREATE TABLE `personal_information` (
  `usersid` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `confirm_password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `rank` varchar(100) DEFAULT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `usertype` enum('admin','user') NOT NULL DEFAULT 'user',
  `role` varchar(50) NOT NULL DEFAULT 'member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section_videos`
--

CREATE TABLE `section_videos` (
  `video_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `vdocipher_id` varchar(255) NOT NULL,
  `video_title` varchar(255) NOT NULL,
  `duration` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checkout_course`
--
ALTER TABLE `checkout_course`
  ADD PRIMARY KEY (`checkout_id`),
  ADD KEY `fk_checkout_usersid` (`usersid`),
  ADD KEY `fk_checkout_course_id` (`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `course_cart`
--
ALTER TABLE `course_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_course_cart_usersid` (`usersid`),
  ADD KEY `fk_course_cart_course_id` (`course_id`);

--
-- Indexes for table `course_orders`
--
ALTER TABLE `course_orders`
  ADD PRIMARY KEY (`courseorder_id`),
  ADD KEY `fk_course_orders_course_id` (`course_id`);

--
-- Indexes for table `course_sections`
--
ALTER TABLE `course_sections`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `my_courses`
--
ALTER TABLE `my_courses`
  ADD PRIMARY KEY (`mycourses_id`),
  ADD KEY `fk_my_courses_usersid` (`usersid`),
  ADD KEY `fk_my_courses_course_id` (`course_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payments_usersid` (`usersid`),
  ADD KEY `fk_payments_course_id` (`course_id`);

--
-- Indexes for table `personal_information`
--
ALTER TABLE `personal_information`
  ADD PRIMARY KEY (`usersid`);

--
-- Indexes for table `section_videos`
--
ALTER TABLE `section_videos`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `section_id` (`section_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checkout_course`
--
ALTER TABLE `checkout_course`
  MODIFY `checkout_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_cart`
--
ALTER TABLE `course_cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_orders`
--
ALTER TABLE `course_orders`
  MODIFY `courseorder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_sections`
--
ALTER TABLE `course_sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `my_courses`
--
ALTER TABLE `my_courses`
  MODIFY `mycourses_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_information`
--
ALTER TABLE `personal_information`
  MODIFY `usersid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `section_videos`
--
ALTER TABLE `section_videos`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checkout_course`
--
ALTER TABLE `checkout_course`
  ADD CONSTRAINT `fk_checkout_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_checkout_usersid` FOREIGN KEY (`usersid`) REFERENCES `personal_information` (`usersid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_cart`
--
ALTER TABLE `course_cart`
  ADD CONSTRAINT `fk_course_cart_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_course_cart_usersid` FOREIGN KEY (`usersid`) REFERENCES `personal_information` (`usersid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_orders`
--
ALTER TABLE `course_orders`
  ADD CONSTRAINT `fk_course_orders_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_sections`
--
ALTER TABLE `course_sections`
  ADD CONSTRAINT `course_sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `my_courses`
--
ALTER TABLE `my_courses`
  ADD CONSTRAINT `fk_my_courses_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_my_courses_usersid` FOREIGN KEY (`usersid`) REFERENCES `personal_information` (`usersid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payments_usersid` FOREIGN KEY (`usersid`) REFERENCES `personal_information` (`usersid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `section_videos`
--
ALTER TABLE `section_videos`
  ADD CONSTRAINT `section_videos_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `course_sections` (`section_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
