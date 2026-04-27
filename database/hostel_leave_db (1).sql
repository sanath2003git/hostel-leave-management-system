-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2026 at 06:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

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
(3, 18, '2026-03-30', 'Present', 'Normal', 3, '2026-03-30 18:40:08'),
(7, 6, '2026-04-27', 'Present', 'Normal', 3, '2026-04-27 12:41:35'),
(8, 7, '2026-04-27', 'Absent', 'Unauthorized', 3, '2026-04-27 12:41:35'),
(9, 18, '2026-04-27', 'Absent', 'Unauthorized', 3, '2026-04-27 12:41:35');

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
  `returned_at` datetime DEFAULT NULL,
  `mess_cut` tinyint(1) NOT NULL DEFAULT 0,
  `return_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostel_leaves`
--

INSERT INTO `hostel_leaves` (`id`, `student_id`, `leave_type_id`, `from_datetime`, `to_datetime`, `reason`, `status`, `applied_at`, `returned_at`, `mess_cut`, `return_status`) VALUES
(9, 6, 8, '2026-03-03 16:10:00', '2026-03-04 05:09:00', 'group study', 'Approved', '2026-03-03 17:40:31', '2026-03-05 15:21:23', 0, NULL),
(10, 6, 6, '2026-03-05 16:08:00', '2026-03-08 15:08:00', 'sick', 'Approved', '2026-03-05 09:39:00', '2026-03-05 15:21:21', 0, NULL),
(12, 6, 6, '2026-03-05 16:07:00', '2026-03-20 16:07:00', 'home visit', 'Approved', '2026-03-05 10:37:31', '2026-03-31 00:03:16', 0, NULL);

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
  `id` int(11) NOT NULL,
  `register_number` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `parent_email` varchar(100) DEFAULT NULL,
  `teacher_email` varchar(100) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_list__mca_2k25`
--

INSERT INTO `student_list__mca_2k25` (`id`, `register_number`, `name`, `email`, `parent_email`, `teacher_email`, `department`, `year`, `phone`) VALUES
(1, '2515', 'CS SANATH SREEKUMAR', '2515@tkmce.ac.in', '2511@tkmce.ac.in', '2558@tkmce.ac.in', 'MCA', 1, '9999999999'),
(2, '2558', 'B SANJAY RAM  ', '2558@tkmce.ac.in', '2558@tkmce.ac.in', '2558@tkmce.ac.in', 'MCA', 1, '9999999999'),
(3, '2511', 'BRIAN PETER BERNARD', '2511@tkmce.ac.in', '2511@tkmce.ac.in', '2511@tkmce.ac.in', 'MCA', 1, '9999999999'),
(4, '2544', 'ASWIN  S', '2544@tkmce.ac.in', '2544@tkmce.ac.in', '2544@tkmce.ac.in', 'MCA', 1, '9999999999');

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
(2, 6, '2520', 'mca', 2025, '1011', NULL);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `register_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `username`, `password`, `email`, `parent_email`, `teacher_email`, `created_at`, `register_number`) VALUES
(3, 2, 'Main Warden', 'warden', '$2y$10$93Ul/A1Q2WdhaPnuGZMzJebt.1pCh.uprf/0DvBRbxclxx9l6rkSe', NULL, NULL, NULL, '2026-03-03 13:31:52', NULL),
(6, 1, 'sanath', 'sanath', '$2y$10$ttEqidY.X5eZGcETjSHliuH61oIloxTsaXCqWT1EaAqFNkRhgnIGG', 'sanathsreekumar18@gmail.com', 'brianpeterbernard5665@gmail.com', '2515@tkmce.ac.in', '2026-03-03 17:38:47', NULL);

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
-- Indexes for table `student_list__mca_2k25`
--
ALTER TABLE `student_list__mca_2k25`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `register_number` (`register_number`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
-- AUTO_INCREMENT for table `student_list__mca_2k25`
--
ALTER TABLE `student_list__mca_2k25`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
