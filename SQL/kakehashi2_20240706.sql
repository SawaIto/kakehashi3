-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-07-06 06:11:14
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
(4, 7, 'いらすとや', '', '2024-07-04 16:56:26');

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
(4, 11),
(4, 12),
(4, 13),
(4, 26);

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
(6, 17, 'テスト\'s Group', '2024-06-27 12:46:03', '2024-06-27 12:46:03'),
(7, 18, 'さわ\'s Group', '2024-07-04 15:43:32', '2024-07-04 15:43:32'),
(8, 21, 'さわこまる\'s Group', '2024-07-06 01:54:33', '2024-07-06 01:54:33');

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
(9, 6, 17, '2024-06-27 12:46:03', '2024-06-27 12:46:03'),
(10, 7, 18, '2024-07-04 15:43:32', '2024-07-04 15:43:32'),
(11, 7, 19, '2024-07-04 15:44:03', '2024-07-04 15:44:03'),
(12, 7, 20, '2024-07-04 15:48:00', '2024-07-04 15:48:00'),
(13, 8, 21, '2024-07-06 01:54:33', '2024-07-06 01:54:33');

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
(5, 12, 5, 'その他', 'かおるテスト', 0, 1, '2024-06-28 00:27:22', '2024-06-29 08:50:06', 2, NULL),
(6, 12, 5, 'その他', 'かおるてすと佐和共有', 0, 0, '2024-06-28 00:27:41', '2024-06-28 00:48:43', 2, '0000-00-00'),
(8, 12, 5, 'やること', 'ｍｍｍｍ', 0, 1, '2024-06-29 08:49:57', '2024-06-29 08:50:26', 3, '2024-07-09'),
(10, 18, 7, '買い物', 'あ\r\nあ\r\nあ', 0, 0, '2024-07-04 16:09:35', '2024-07-04 16:09:35', NULL, NULL),
(11, 18, 7, 'やること', 'あいうえお　かか\nっきい', NULL, 0, '2024-07-04 16:18:42', '2024-07-04 16:18:42', NULL, NULL),
(12, 18, 7, 'その他', 'あ', NULL, 0, '2024-07-04 16:23:54', '2024-07-04 16:23:54', NULL, NULL),
(13, 18, 7, '買い物', 'あ', NULL, 0, '2024-07-04 16:26:33', '2024-07-04 16:26:33', NULL, NULL),
(14, 18, 7, '買い物', 'あ', NULL, 0, '2024-07-04 16:32:43', '2024-07-04 16:32:43', NULL, NULL);

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
(10, 10, 19, '2024-07-04 16:09:35'),
(11, 10, 20, '2024-07-04 16:09:35');

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
(3, 5, 11, '667f944341f32_動画テロップ (7).png', '', '2024-06-29 04:57:39'),
(8, 5, 11, '667fc7e06eccc_window_amado_open.png', '', '2024-06-29 08:37:52'),
(9, 5, 12, '667fcb5aefd06_hakusyu_boy1-1.png', '', '2024-06-29 08:52:42'),
(10, 5, 12, '667fcb660c1b5_chizu_color.png', '', '2024-06-29 08:52:54'),
(11, 7, 18, '6686d21d1fced.jpg', '', '2024-07-04 16:47:25'),
(12, 7, 18, '6686d21d22d2e.jpg', '', '2024-07-04 16:47:25'),
(13, 7, 18, '6686d21d27258.jpg', '', '2024-07-04 16:47:25'),
(14, 7, 18, '6686d21d2994a.jpg', '', '2024-07-04 16:47:25'),
(15, 7, 18, '6686d21d2c33c.jpg', '', '2024-07-04 16:47:25'),
(16, 7, 18, '6686d21d2d004.jpg', '', '2024-07-04 16:47:25'),
(17, 7, 18, '6686d21d2eb82.jpg', '', '2024-07-04 16:47:25'),
(18, 7, 18, '6686d21d30373.jpg', '', '2024-07-04 16:47:25'),
(19, 7, 18, '6686d21d3177c.jpg', '', '2024-07-04 16:47:25'),
(20, 7, 18, '6686d21d3274e.jpg', '', '2024-07-04 16:47:25'),
(21, 7, 18, '6686d21d373f7.jpg', '', '2024-07-04 16:47:25'),
(22, 7, 18, '6686d21d38737.jpg', '', '2024-07-04 16:47:25'),
(23, 7, 19, '66887c29bfebd.jpg', '', '2024-07-05 23:05:13'),
(24, 7, 18, '6688b24709c31.jpg', '', '2024-07-06 02:56:07'),
(25, 7, 18, '6688b2478662b.jpg', '', '2024-07-06 02:56:07'),
(26, 7, 18, '6688b2869e9f0.jpg', '', '2024-07-06 02:57:11');

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
(2, 1, '2024'),
(3, 8, 'テスト');

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
(7, 5, 12, '2024-07-04', '僕だけの秘密', '2024-06-27 12:57:27', '2024-06-27 12:57:27'),
(8, 5, 12, '2024-07-02', 'ハローワーク', '2024-06-29 08:49:15', '2024-06-29 08:49:15'),
(9, 7, 18, '2024-07-18', 'あああ', '2024-07-04 16:34:17', '2024-07-04 16:34:17'),
(10, 7, 18, '2024-07-09', 'あ\r\nあ\r\nあ', '2024-07-04 16:34:33', '2024-07-04 16:34:33'),
(11, 7, 18, '2024-07-19', 'げんこつ山の狸さん\r\n最多最多チューリップの花が　　　咲いた', '2024-07-04 16:35:17', '2024-07-04 16:35:17'),
(12, 7, 18, '2025-06-06', 'aaa', '2024-07-06 03:04:36', '2024-07-06 03:04:36');

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
(8, 6, 12, '2024-06-27 12:56:04', '2024-06-27 12:56:04'),
(9, 9, 19, '2024-07-04 16:34:17', '2024-07-04 16:34:17');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','modify','view') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(11, '', NULL, '$2y$10$43sXvwpxeHvuyvyd0UQAlecvjBP/BEu4BhjozahTKmyoBRXvHARC2', 'admin', '2024-06-27 06:13:47', '2024-06-27 06:13:47'),
(12, '', NULL, '$2y$10$NkOZmlE4UM89ce13RbFRqOJPC487mdiyGp5JYE9Y.cb2skqwXH5LG', 'modify', '2024-06-27 06:20:26', '2024-06-27 06:20:26'),
(16, '', NULL, '$2y$10$fkPTJMQ.vnCQdo.opfsvcuZWkgWEuyHwOLBmD0JUmVavv7aIehWf6', 'view', '2024-06-27 07:50:37', '2024-07-02 11:51:04'),
(17, '', NULL, '$2y$10$WbVz.3uYtVD/S.XTVEaH1utZrG4QR3euTjk/BGa1QG5hQfor7I2g6', 'admin', '2024-06-27 12:46:03', '2024-06-27 12:46:03'),
(18, 'さわ', 'sawa110291@gmail.com', '$2y$10$6gKmxdLrxsV9xCoV8OJYUe0jFpKZc.TB19i8brwk.79LzPmAYKGpG', 'admin', '2024-07-04 15:43:32', '2024-07-04 15:52:08'),
(19, 'まさえ', NULL, '$2y$10$Z.x4mbDGtGPCkyidCy4b5OUE6IyR99tMuZKXvBB2.YxQvi/Mdtlce', 'view', '2024-07-04 15:44:03', '2024-07-04 15:44:03'),
(20, 'かおる', 'sawatin@yahoo.co.jp', '$2y$10$ahmgZM1zqtvtTaBhSkKD/.gIBt/Ckdtwctp8slIsh06K/ua.ezhc6', 'modify', '2024-07-04 15:48:00', '2024-07-06 02:10:43'),
(21, 'さわこまる', 'sawa1@yahoo.co.jp', '$2y$10$0d3FrqDxVx37LJPdyriI4u2eYi4JhXQIt7xqcGa6kEQEFWs71RqsG', 'admin', '2024-07-06 01:54:33', '2024-07-06 01:54:33');

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
  ADD KEY `idx_groups_user_id` (`user_id`) USING BTREE;

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
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- テーブルの AUTO_INCREMENT `memos`
--
ALTER TABLE `memos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- テーブルの AUTO_INCREMENT `memo_shares`
--
ALTER TABLE `memo_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- テーブルの AUTO_INCREMENT `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- テーブルの AUTO_INCREMENT `photo_comments`
--
ALTER TABLE `photo_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `photo_tags`
--
ALTER TABLE `photo_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルの AUTO_INCREMENT `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- テーブルの AUTO_INCREMENT `schedule_shares`
--
ALTER TABLE `schedule_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
