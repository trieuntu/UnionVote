-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Mar 06, 2026 at 04:01 AM
-- Server version: 5.7.39
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `unionvote`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gpa` decimal(3,2) DEFAULT NULL COMMENT 'Điểm TB tích luỹ',
  `conduct_score` decimal(5,2) DEFAULT NULL COMMENT 'Điểm rèn luyện tích luỹ',
  `bio` text COLLATE utf8mb4_unicode_ci COMMENT 'Tóm tắt thông tin cá nhân',
  `display_order` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `election_id`, `full_name`, `class_name`, `student_id`, `gpa`, `conduct_score`, `bio`, `display_order`, `created_at`) VALUES
(25, 1, 'Nguyễn Văn A', '62CNTT1', '62130001', '3.25', '85.50', 'UV BCH Đoàn khoa khoá trước', 1, '2026-03-06 10:46:24'),
(26, 1, 'Nguyễn Văn B', '62CNTT1', '62130002', '3.25', '85.50', 'UV BCH Đoàn khoa khoá trước', 2, '2026-03-06 10:46:24'),
(27, 1, 'Nguyễn Văn C', '62CNTT1', '62130003', '3.25', '85.50', 'UV BCH Đoàn khoa khoá trước', 3, '2026-03-06 10:46:24');

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

CREATE TABLE `elections` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `min_votes` int(11) NOT NULL DEFAULT '1' COMMENT 'Số lượng tối thiểu phải chọn',
  `max_votes` int(11) NOT NULL DEFAULT '1' COMMENT 'Số lượng tối đa được chọn',
  `is_visible` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Hiển thị trên trang chủ',
  `show_result` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Hiển thị kết quả realtime',
  `status` enum('draft','active','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`id`, `title`, `description`, `start_time`, `end_time`, `min_votes`, `max_votes`, `is_visible`, `show_result`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'dev', 'dev', '2026-03-06 10:05:00', '2026-03-06 12:05:00', 1, 1, 1, 1, 'draft', 1, '2026-03-06 10:06:02', '2026-03-06 10:59:41');

-- --------------------------------------------------------

--
-- Table structure for table `email_tokens`
--

CREATE TABLE `email_tokens` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `voter_id` int(11) NOT NULL,
  `token_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SHA-256 hash của token',
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_tokens`
--

INSERT INTO `email_tokens` (`id`, `election_id`, `voter_id`, `token_hash`, `expires_at`, `is_used`, `created_at`) VALUES
(1, 1, 4, '67b30d7051b2e5007edcaca833a51822c76a890e631ef501f00d5e2809f8b10c', '2026-03-06 11:02:12', 1, '2026-03-06 10:47:12');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'smtp_host', 'smtp.gmail.com', '2026-03-06 10:16:59'),
(2, 'smtp_port', '587', '2026-03-06 09:27:07'),
(3, 'smtp_username', 'trieu.science@gmail.com', '2026-03-06 10:16:31'),
(4, 'smtp_password', 'tkppf2L5HANHpW/BimsUCTo6MjBwVklyd2V3WFFsQTZyYkRIT3IwcWw1cDczSTg3cmtTT2FXM3RUM21Jcz0=', '2026-03-06 10:16:31'),
(5, 'smtp_encryption', 'tls', '2026-03-06 09:27:07'),
(6, 'smtp_from_email', 'trieu.science@gmail.com', '2026-03-06 10:16:31'),
(7, 'smtp_from_name', 'UnionVote - ĐK CNTT NTU', '2026-03-06 09:27:07'),
(8, 'site_name', 'Hệ thống Bình chọn Online - Đoàn Khoa CNTT', '2026-03-06 09:27:07'),
(9, 'token_expiry_minutes', '15', '2026-03-06 09:27:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','editor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'editor',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@ntu.edu.vn', '$2y$12$qjW3IN4Aks6y9Jjx6aOhrOwUMmguBtDD25.CwZUBsPS6NZc/8BssC', 'Quản trị viên', 'admin', 1, '2026-03-06 10:34:57', '2026-03-06 09:27:07', '2026-03-06 10:34:57'),
(2, 'dev01', 'dev01@ntu.edu.vn', '$2y$10$CYyr.drbRwOW1WD0a5KsZe1eC7VkstZIzxQYp3eQc1Mpk/6JgsL42', 'dev01', 'editor', 1, '2026-03-06 10:58:54', '2026-03-06 10:58:20', '2026-03-06 10:58:54');

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

CREATE TABLE `voters` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `has_voted` tinyint(1) NOT NULL DEFAULT '0',
  `voted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`id`, `election_id`, `email`, `has_voted`, `voted_at`, `created_at`) VALUES
(4, 1, 'trieunh@ntu.edu.vn', 1, '2026-03-06 10:55:42', '2026-03-06 10:38:38'),
(5, 1, 'trieunh1@ntu.edu.vn', 0, NULL, '2026-03-06 10:38:38');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `token_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Hash token đã sử dụng để bỏ phiếu',
  `ip_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash IP người bỏ phiếu',
  `submitted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `election_id`, `token_hash`, `ip_hash`, `submitted_at`, `created_at`) VALUES
(1, 1, '67b30d7051b2e5007edcaca833a51822c76a890e631ef501f00d5e2809f8b10c', 'a729eda38dc2c91bd81af922bd2a903e1524af9a080fafb547e21ea964ab3660', '2026-03-06 10:55:42', '2026-03-06 10:55:42');

-- --------------------------------------------------------

--
-- Table structure for table `vote_details`
--

CREATE TABLE `vote_details` (
  `id` int(11) NOT NULL,
  `vote_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vote_details`
--

INSERT INTO `vote_details` (`id`, `vote_id`, `candidate_id`, `created_at`) VALUES
(1, 1, 25, '2026-03-06 10:55:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_candidate` (`election_id`,`student_id`);

--
-- Indexes for table `elections`
--
ALTER TABLE `elections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `email_tokens`
--
ALTER TABLE `email_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_id` (`election_id`),
  ADD KEY `voter_id` (`voter_id`),
  ADD KEY `idx_token_hash` (`token_hash`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_voter` (`election_id`,`email`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_token_vote` (`token_hash`),
  ADD KEY `election_id` (`election_id`);

--
-- Indexes for table `vote_details`
--
ALTER TABLE `vote_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote_candidate` (`vote_id`,`candidate_id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `email_tokens`
--
ALTER TABLE `email_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vote_details`
--
ALTER TABLE `vote_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `elections`
--
ALTER TABLE `elections`
  ADD CONSTRAINT `elections_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `email_tokens`
--
ALTER TABLE `email_tokens`
  ADD CONSTRAINT `email_tokens_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `email_tokens_ibfk_2` FOREIGN KEY (`voter_id`) REFERENCES `voters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `voters`
--
ALTER TABLE `voters`
  ADD CONSTRAINT `voters_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vote_details`
--
ALTER TABLE `vote_details`
  ADD CONSTRAINT `vote_details_ibfk_1` FOREIGN KEY (`vote_id`) REFERENCES `votes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vote_details_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
