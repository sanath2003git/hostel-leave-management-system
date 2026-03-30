-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2026 at 08:42 PM
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
-- Database: `hostel_leave_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent','Leave') DEFAULT 'Present',
  `remark` enum('Normal','Unauthorized') DEFAULT 'Normal',
  `marked_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `date`, `status`, `remark`, `marked_by`, `created_at`) VALUES
(1, 6, '2026-03-30', 'Absent', 'Unauthorized', 3, '2026-03-30 18:40:08'),
(2, 7, '2026-03-30', 'Present', 'Normal', 3, '2026-03-30 18:40:08'),
(3, 18, '2026-03-30', 'Present', 'Normal', 3, '2026-03-30 18:40:08');

-- --------------------------------------------------------

--
-- Table structure for table `hostel_leaves`
--

CREATE TABLE `hostel_leaves` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `from_datetime` datetime NOT NULL,
  `to_datetime` datetime NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `returned_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostel_leaves`
--

INSERT INTO `hostel_leaves` (`id`, `student_id`, `leave_type_id`, `from_datetime`, `to_datetime`, `reason`, `status`, `applied_at`, `returned_at`) VALUES
(9, 6, 8, '2026-03-03 16:10:00', '2026-03-04 05:09:00', 'group study', 'Approved', '2026-03-03 17:40:31', '2026-03-05 15:21:23'),
(10, 6, 6, '2026-03-05 16:08:00', '2026-03-08 15:08:00', 'sick', 'Approved', '2026-03-05 09:39:00', '2026-03-05 15:21:21'),
(11, 7, 5, '2026-03-05 15:13:00', '2026-03-07 15:16:00', 'NULL', 'Approved', '2026-03-05 09:44:11', '2026-03-05 15:21:18'),
(12, 6, 6, '2026-03-05 16:07:00', '2026-03-20 16:07:00', 'home visit', 'Approved', '2026-03-05 10:37:31', '2026-03-31 00:03:16');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `type_name`) VALUES
(8, 'Academic Purpose'),
(4, 'Emergency'),
(6, 'Home Visit'),
(5, 'Medical'),
(9, 'Official Permission'),
(10, 'Other'),
(7, 'Personal Work');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `leave_id` int(11) NOT NULL,
  `recipient_email` varchar(100) DEFAULT NULL,
  `notification_type` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'student'),
(2, 'warden');

-- --------------------------------------------------------

--
-- Table structure for table `student_list__mca_2k25`
--

CREATE TABLE `student_list__mca_2k25` (
  `register_number` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `parent_email` varchar(100) DEFAULT NULL,
  `teacher_email` varchar(100) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `student_list__mca_2k25`
--

INSERT INTO `student_list__mca_2k25` (`register_number`, `name`, `email`, `parent_email`, `teacher_email`, `department`, `year`, `phone`) VALUES
('Register number', 'Student Name', 'Student email', 'Student email', 'Student email', 'Depatment', 0, 'phone number'),
('2567', 'ABEL JOSEPH BENNY', '2567@tkmce.ac.in', '2567@tkmce.ac.in', '2567@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2531', 'ABHAJ  KHAN', '2531@tkmce.ac.in', '2531@tkmce.ac.in', '2531@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2514', 'ABHIJITH  KARUN', '2514@tkmce.ac.in', '2514@tkmce.ac.in', '2514@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2509', 'ABHIRAM  HARI', '2509@tkmce.ac.in', '2509@tkmce.ac.in', '2509@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2519', 'ADITHYA  DEV', '2519@tkmce.ac.in', '2519@tkmce.ac.in', '2519@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2557', 'AHAMMED ASLAH M K', '2557@tkmce.ac.in', '2557@tkmce.ac.in', '2557@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2520', 'AJESH  C V', '2520@tkmce.ac.in', '2520@tkmce.ac.in', '2520@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2538', 'ALSAFA  M', '2538@tkmce.ac.in', '2538@tkmce.ac.in', '2538@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2556', 'ANAGHA S PRASAD', '2556@tkmce.ac.in', '2556@tkmce.ac.in', '2556@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2516', 'ANANDHU  I', '2516@tkmce.ac.in', '2516@tkmce.ac.in', '2516@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2547', 'ANSHAD ZAMAN A P  ', '2547@tkmce.ac.in', '2547@tkmce.ac.in', '2547@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2529', 'APARNA R NELLIKUNNEL', '2529@tkmce.ac.in', '2529@tkmce.ac.in', '2529@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2549', 'ARIFA  UP', '2549@tkmce.ac.in', '2549@tkmce.ac.in', '2549@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2542', 'ARUN  CHRISTOPHER', '2542@tkmce.ac.in', '2542@tkmce.ac.in', '2542@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2526', 'ASIF MOHAMMED ALI', '2526@tkmce.ac.in', '2526@tkmce.ac.in', '2526@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2505', 'ASWATHY  S', '2505@tkmce.ac.in', '2505@tkmce.ac.in', '2505@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2544', 'ASWIN  S', '2544@tkmce.ac.in', '2544@tkmce.ac.in', '2544@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2558', 'B SANJAY RAM  ', '2558@tkmce.ac.in', '2558@tkmce.ac.in', '2558@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2511', 'BRIAN PETER BERNARD', '2511@tkmce.ac.in', '2511@tkmce.ac.in', '2511@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2515', 'CS SANATH SREEKUMAR', '2515@tkmce.ac.in', '2515@tkmce.ac.in', '2515@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2504', 'DILSHA HAMZATHALI  ', '2504@tkmce.ac.in', '2504@tkmce.ac.in', '2504@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2546', 'GOKUL DEV A', '2546@tkmce.ac.in', '2546@tkmce.ac.in', '2546@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2525', 'GOPIKA S L  ', '2525@tkmce.ac.in', '2525@tkmce.ac.in', '2525@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2565', 'GOVIND  PRAKASH', '2565@tkmce.ac.in', '2565@tkmce.ac.in', '2565@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2518', 'HARIKRISHNAN M P  ', '2518@tkmce.ac.in', '2518@tkmce.ac.in', '2518@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2562', 'JENEESH K JAISON', '2562@tkmce.ac.in', '2562@tkmce.ac.in', '2562@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2543', 'JESSO  JOY', '2543@tkmce.ac.in', '2543@tkmce.ac.in', '2543@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2506', 'JOYAL JOHN MATHEW', '2506@tkmce.ac.in', '2506@tkmce.ac.in', '2506@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2563', 'KIRAN  SIVADAS', '2563@tkmce.ac.in', '2563@tkmce.ac.in', '2563@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2502', 'KRISHNA PRIYA S', '2502@tkmce.ac.in', '2502@tkmce.ac.in', '2502@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2501', 'KRISTINE VINU THOMAS', '2501@tkmce.ac.in', '2501@tkmce.ac.in', '2501@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2533', 'LAKSHMY  V', '2533@tkmce.ac.in', '2533@tkmce.ac.in', '2533@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2528', 'MARIYAM K HAMSA', '2528@tkmce.ac.in', '2528@tkmce.ac.in', '2528@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2564', 'MERSHAD  ', '2564@tkmce.ac.in', '2564@tkmce.ac.in', '2564@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2534', 'MIDHUN M PILLAI', '2534@tkmce.ac.in', '2534@tkmce.ac.in', '2534@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2522', 'MOHAMMED MAZIN CHERIYAN', '2522@tkmce.ac.in', '2522@tkmce.ac.in', '2522@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2559', 'MOHAMMED RAIHAN S', '2559@tkmce.ac.in', '2559@tkmce.ac.in', '2559@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2548', 'MUHAMMED ANAS A', '2548@tkmce.ac.in', '2548@tkmce.ac.in', '2548@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2517', 'NANDANA DINESH A', '2517@tkmce.ac.in', '2517@tkmce.ac.in', '2517@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2541', 'NANDHAKUMAR  M', '2541@tkmce.ac.in', '2541@tkmce.ac.in', '2541@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2569', 'NAVNEETH KRISHNA  ', '2569@tkmce.ac.in', '2569@tkmce.ac.in', '2569@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2530', 'NEERAJ  N', '2530@tkmce.ac.in', '2530@tkmce.ac.in', '2530@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2512', 'NEHMAL  N', '2512@tkmce.ac.in', '2512@tkmce.ac.in', '2512@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2568', 'NIDHISH  CHANDRAN', '2568@tkmce.ac.in', '2568@tkmce.ac.in', '2568@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2560', 'PATRICK DAVIS JERRY', '2560@tkmce.ac.in', '2560@tkmce.ac.in', '2560@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2507', 'R PRANAV  ', '2507@tkmce.ac.in', '2507@tkmce.ac.in', '2507@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2553', 'RAHMATHUL SANA C P ', '2553@tkmce.ac.in', '2553@tkmce.ac.in', '2553@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2510', 'ROOPAK  M', '2510@tkmce.ac.in', '2510@tkmce.ac.in', '2510@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2508', 'SAJEER  F M', '2508@tkmce.ac.in', '2508@tkmce.ac.in', '2508@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2521', 'SHYAM K  ', '2521@tkmce.ac.in', '2521@tkmce.ac.in', '2521@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2552', 'SIMNA MUHAMMED E C', '2552@tkmce.ac.in', '2552@tkmce.ac.in', '2552@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2540', 'SIYANA  HAKKIM', '2540@tkmce.ac.in', '2540@tkmce.ac.in', '2540@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2536', 'SOORYATHEERTH  S', '2536@tkmce.ac.in', '2536@tkmce.ac.in', '2536@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2566', 'SREEJEEV  V', '2566@tkmce.ac.in', '2566@tkmce.ac.in', '2566@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2532', 'SREELAKSHMI C S', '2532@tkmce.ac.in', '2532@tkmce.ac.in', '2532@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2537', 'TWINKLE TREESA ', '2537@tkmce.ac.in', '2537@tkmce.ac.in', '2537@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2524', 'VAISHNAV  JAYAN', '2524@tkmce.ac.in', '2524@tkmce.ac.in', '2524@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2527', 'VIGHNESH  B', '2527@tkmce.ac.in', '2527@tkmce.ac.in', '2527@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2539', 'VIJAY VASUDEV B L', '2539@tkmce.ac.in', '2539@tkmce.ac.in', '2539@tkmce.ac.in', 'MCA', 1, '9999999999'),
('2555', 'VINAY S NAIR', '2555@tkmce.ac.in', '2555@tkmce.ac.in', '2555@tkmce.ac.in', 'MCA', 1, '9999999999'),
('', '', '', '', '', '', 0, ''),
('', '', '', '', '', '', 0, ''),
('', '', '', '', '', '', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `register_number` varchar(50) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `user_id`, `register_number`, `department`, `year`, `room_number`, `phone`) VALUES
(2, 6, '2520', 'mca', 2025, '1011', NULL),
(3, 7, '252111', 'B tech', 2024, '1015', NULL),
(11, 18, '2511', 'MCA', 1, '205', '9999999999');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `parent_email` varchar(100) DEFAULT NULL,
  `teacher_email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `username`, `password`, `email`, `parent_email`, `teacher_email`, `created_at`) VALUES
(3, 2, 'Main Warden', 'warden', '$2y$10$93Ul/A1Q2WdhaPnuGZMzJebt.1pCh.uprf/0DvBRbxclxx9l6rkSe', NULL, NULL, NULL, '2026-03-03 13:31:52'),
(6, 1, 'sanath', 'sanath', '$2y$10$ttEqidY.X5eZGcETjSHliuH61oIloxTsaXCqWT1EaAqFNkRhgnIGG', 'sanathsreekumar18@gmail.com', 'brianpeterbernard5665@gmail.com', '2515@tkmce.ac.in', '2026-03-03 17:38:47'),
(7, 1, 'sajeer', 'sajeer', '$2y$10$SOn//Kg3.zLu661NjLvdGun1QluhvgzZf1zvl8rVQ1thwYmFCGsQO', 'sajeer@example.com', 'sajeer@example.com', 'sajeer@example.com', '2026-03-05 09:42:27'),
(18, 1, 'BRIAN PETER BERNARD', '2511', '$2y$10$43I4YKSZD0tAGk5fXggnX.WyKQBvzOTbwSo/1FJG6NL1yRHXHgtky', '2511@tkmce.ac.in', '2511@tkmce.ac.in', '2511@tkmce.ac.in', '2026-03-30 18:25:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`date`);

--
-- Indexes for table `hostel_leaves`
--
ALTER TABLE `hostel_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_id` (`leave_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hostel_leaves`
--
ALTER TABLE `hostel_leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hostel_leaves`
--
ALTER TABLE `hostel_leaves`
  ADD CONSTRAINT `hostel_leaves_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hostel_leaves_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`leave_id`) REFERENCES `hostel_leaves` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
