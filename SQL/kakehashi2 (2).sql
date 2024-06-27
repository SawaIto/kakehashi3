-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-06-27 15:49:14
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `kakehashi2`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `groups`
--

INSERT INTO `groups` (`id`, `admin_id`, `name`, `created_at`, `updated_at`) VALUES
(5, 11, 'さわ\'s Group', '2024-06-27 06:13:47', '2024-06-27 06:13:47'),
(6, 17, 'テスト\'s Group', '2024-06-27 12:46:03', '2024-06-27 12:46:03');

-- --------------------------------------------------------

--
-- テーブルの構造 `group_members`
--

CREATE TABLE `group_members` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `group_members`
--

INSERT INTO `group_members` (`id`, `group_id`, `user_id`, `created_at`, `updated_at`) VALUES
(5, 5, 11, '2024-06-27 06:13:47', '2024-06-27 06:13:47'),
(6, 5, 12, '2024-06-27 06:20:26', '2024-06-27 06:20:26'),
(8, 5, 16, '2024-06-27 07:50:37', '2024-06-27 07:50:37'),
(9, 6, 17, '2024-06-27 12:46:03', '2024-06-27 12:46:03');

-- --------------------------------------------------------

--
-- テーブルの構造 `memos`
--

CREATE TABLE `memos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_private` tinyint(1) DEFAULT 0,
  `is_completed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `memos`
--

INSERT INTO `memos` (`id`, `user_id`, `category`, `title`, `content`, `is_private`, `is_completed`, `created_at`, `updated_at`) VALUES
(1, 11, '買い物', 'ｓｓ', 'ああああ\r\nあああ\r\nあああ\r\n', 0, 0, '2024-06-27 13:41:30', '2024-06-27 13:45:57');

-- --------------------------------------------------------

--
-- テーブルの構造 `memo_shares`
--

CREATE TABLE `memo_shares` (
  `id` int(11) NOT NULL,
  `memo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `schedules`
--

INSERT INTO `schedules` (`id`, `group_id`, `user_id`, `date`, `content`, `created_at`, `updated_at`) VALUES
(1, 5, 11, '2024-06-28', 'あああ', '2024-06-27 08:08:48', '2024-06-27 08:08:48'),
(3, 5, 11, '2024-06-29', 'あああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ', '2024-06-27 10:45:55', '2024-06-27 10:45:55'),
(4, 5, 11, '2024-07-05', 'テスト\r\nテスト', '2024-06-27 11:48:21', '2024-06-27 11:48:21'),
(5, 5, 11, '2024-07-04', 'あああ\r\nあああ\r\nあああ\r\nああ\r\n', '2024-06-27 12:50:33', '2024-06-27 12:50:33'),
(6, 5, 11, '2024-06-29', '名古屋一泊', '2024-06-27 12:56:04', '2024-06-27 12:56:04'),
(7, 5, 12, '2024-07-04', '僕だけの秘密', '2024-06-27 12:57:27', '2024-06-27 12:57:27');

-- --------------------------------------------------------

--
-- テーブルの構造 `schedule_shares`
--

CREATE TABLE `schedule_shares` (
  `id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `schedule_shares`
--

INSERT INTO `schedule_shares` (`id`, `schedule_id`, `user_id`, `created_at`, `updated_at`) VALUES
(5, 4, 12, '2024-06-27 11:48:21', '2024-06-27 11:48:21'),
(6, 5, 12, '2024-06-27 12:50:33', '2024-06-27 12:50:33'),
(7, 5, 16, '2024-06-27 12:50:33', '2024-06-27 12:50:33'),
(8, 6, 12, '2024-06-27 12:56:04', '2024-06-27 12:56:04');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','modify','view') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(11, 'さわ', 'sawa110291@gmail.com', '$2y$10$43sXvwpxeHvuyvyd0UQAlecvjBP/BEu4BhjozahTKmyoBRXvHARC2', 'admin', '2024-06-27 06:13:47', '2024-06-27 06:13:47'),
(12, 'かおる', 'sawatin@yahoo.co.jp', '$2y$10$NkOZmlE4UM89ce13RbFRqOJPC487mdiyGp5JYE9Y.cb2skqwXH5LG', 'modify', '2024-06-27 06:20:26', '2024-06-27 06:20:26'),
(16, 'まさえ', 'sawa291@yahoo.co.jp', '$2y$10$fkPTJMQ.vnCQdo.opfsvcuZWkgWEuyHwOLBmD0JUmVavv7aIehWf6', 'view', '2024-06-27 07:50:37', '2024-06-27 07:50:47'),
(17, 'テスト', 'sawasawa@gmail.com', '$2y$10$WbVz.3uYtVD/S.XTVEaH1utZrG4QR3euTjk/BGa1QG5hQfor7I2g6', 'admin', '2024-06-27 12:46:03', '2024-06-27 12:46:03');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_groups_admin_id` (`admin_id`);

--
-- テーブルのインデックス `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_group_members_group_id` (`group_id`),
  ADD KEY `idx_group_members_user_id` (`user_id`);

--
-- テーブルのインデックス `memos`
--
ALTER TABLE `memos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_memos_user_id` (`user_id`),
  ADD KEY `idx_memos_category` (`category`);

--
-- テーブルのインデックス `memo_shares`
--
ALTER TABLE `memo_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_memo_shares_memo_id` (`memo_id`),
  ADD KEY `idx_memo_shares_user_id` (`user_id`);

--
-- テーブルのインデックス `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_schedules_group_id` (`group_id`),
  ADD KEY `idx_schedules_user_id` (`user_id`),
  ADD KEY `idx_schedules_date` (`date`);

--
-- テーブルのインデックス `schedule_shares`
--
ALTER TABLE `schedule_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_schedule_shares_schedule_id` (`schedule_id`),
  ADD KEY `idx_schedule_shares_user_id` (`user_id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- テーブルの AUTO_INCREMENT `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- テーブルの AUTO_INCREMENT `memos`
--
ALTER TABLE `memos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `memo_shares`
--
ALTER TABLE `memo_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `schedule_shares`
--
ALTER TABLE `schedule_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `memos`
--
ALTER TABLE `memos`
  ADD CONSTRAINT `memos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `memo_shares`
--
ALTER TABLE `memo_shares`
  ADD CONSTRAINT `memo_shares_ibfk_1` FOREIGN KEY (`memo_id`) REFERENCES `memos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `memo_shares_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `schedule_shares`
--
ALTER TABLE `schedule_shares`
  ADD CONSTRAINT `schedule_shares_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_shares_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
