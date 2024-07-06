-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-06-29 10:34:39
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
-- テーブルの構造 `albums`
--

CREATE TABLE `albums` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `albums`
--

INSERT INTO `albums` (`id`, `group_id`, `name`, `description`, `created_at`) VALUES
(1, 5, 'JAM', '', '2024-06-29 04:42:30'),
(2, 5, 'test', NULL, '2024-06-29 04:58:11');

-- --------------------------------------------------------

--
-- テーブルの構造 `album_photos`
--

CREATE TABLE `album_photos` (
  `album_id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `album_photos`
--

INSERT INTO `album_photos` (`album_id`, `photo_id`) VALUES
(1, 1),
(2, 1),
(2, 2),
(2, 3),
(2, 4);

-- --------------------------------------------------------

--
-- テーブルの構造 `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `groups`
--

INSERT INTO `groups` (`id`, `user_id`, `name`, `created_at`, `updated_at`) VALUES
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
  `group_id` int(11) DEFAULT NULL,
  `category` enum('買い物','スケジュール','やること','その他') NOT NULL,
  `content` text NOT NULL,
  `is_private` tinyint(1) DEFAULT 0,
  `is_completed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `importance` int(11) DEFAULT 3 COMMENT '重要度（1-5）',
  `due_date` date DEFAULT NULL COMMENT '期限日'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `memos`
--

INSERT INTO `memos` (`id`, `user_id`, `group_id`, `category`, `content`, `is_private`, `is_completed`, `created_at`, `updated_at`, `importance`, `due_date`) VALUES
(3, 11, 5, '買い物', 'なんでもいい\r\n1kgだよ', 0, 0, '2024-06-27 23:46:40', '2024-06-27 23:49:00', 2, '2024-07-05'),
(4, 11, 5, 'その他', 'ひみつ', 0, 0, '2024-06-28 00:15:08', '2024-06-28 00:15:08', 3, '2024-06-12'),
(5, 12, 5, 'その他', 'かおるテスト', 0, 0, '2024-06-28 00:27:22', '2024-06-28 01:34:31', 2, NULL),
(6, 12, 5, 'その他', 'かおるてすと佐和共有', 0, 0, '2024-06-28 00:27:41', '2024-06-28 00:48:43', 2, '0000-00-00');

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

--
-- テーブルのデータのダンプ `memo_shares`
--

INSERT INTO `memo_shares` (`id`, `memo_id`, `user_id`, `created_at`) VALUES
(4, 3, 12, '2024-06-28 00:14:48'),
(5, 3, 16, '2024-06-28 00:14:48'),
(8, 4, 12, '2024-06-28 01:34:04');

-- --------------------------------------------------------

--
-- テーブルの構造 `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `comment` text DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `photos`
--

INSERT INTO `photos` (`id`, `group_id`, `user_id`, `file_name`, `comment`, `upload_date`) VALUES
(1, 5, 11, '667f908e7652d_動画テロップ (8).png', '地球倫理', '2024-06-29 04:41:50'),
(2, 5, 11, '667f90a28e065_IMG_20240615_123327_623.jpg', '', '2024-06-29 04:42:10'),
(3, 5, 11, '667f944341f32_動画テロップ (7).png', '', '2024-06-29 04:57:39'),
(4, 5, 11, '667f9463145ef_動画テロップ (6).png', '', '2024-06-29 04:58:11');

-- --------------------------------------------------------

--
-- テーブルの構造 `photo_comments`
--

CREATE TABLE `photo_comments` (
  `id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `photo_tags`
--

CREATE TABLE `photo_tags` (
  `id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `tag` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `photo_tags`
--

INSERT INTO `photo_tags` (`id`, `photo_id`, `tag`) VALUES
(1, 1, 'JAM'),
(2, 1, '2024');

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
-- テーブルのインデックス `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- テーブルのインデックス `album_photos`
--
ALTER TABLE `album_photos`
  ADD PRIMARY KEY (`album_id`,`photo_id`),
  ADD KEY `photo_id` (`photo_id`);

--
-- テーブルのインデックス `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_groups_user_id` (`user_id`);

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
  ADD KEY `idx_memos_category` (`category`),
  ADD KEY `idx_memos_importance` (`importance`),
  ADD KEY `idx_memos_due_date` (`due_date`),
  ADD KEY `fk_memos_group` (`group_id`);

--
-- テーブルのインデックス `memo_shares`
--
ALTER TABLE `memo_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_memo_shares_memo_id` (`memo_id`),
  ADD KEY `idx_memo_shares_user_id` (`user_id`);

--
-- テーブルのインデックス `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `photo_comments`
--
ALTER TABLE `photo_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `photo_id` (`photo_id`),
  ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `photo_tags`
--
ALTER TABLE `photo_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `photo_id` (`photo_id`);

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
-- テーブルの AUTO_INCREMENT `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `memo_shares`
--
ALTER TABLE `memo_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- テーブルの AUTO_INCREMENT `photo_comments`
--
ALTER TABLE `photo_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `photo_tags`
--
ALTER TABLE `photo_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- テーブルの制約 `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `albums_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`);

--
-- テーブルの制約 `album_photos`
--
ALTER TABLE `album_photos`
  ADD CONSTRAINT `album_photos_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `album_photos_ibfk_2` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_memos_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `memos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `memo_shares`
--
ALTER TABLE `memo_shares`
  ADD CONSTRAINT `memo_shares_ibfk_1` FOREIGN KEY (`memo_id`) REFERENCES `memos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `memo_shares_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `photos_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `photo_comments`
--
ALTER TABLE `photo_comments`
  ADD CONSTRAINT `photo_comments_ibfk_1` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `photo_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `photo_tags`
--
ALTER TABLE `photo_tags`
  ADD CONSTRAINT `photo_tags_ibfk_1` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE;

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
