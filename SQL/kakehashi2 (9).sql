-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-07-14 13:37:57
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
(6, 7, 'てすと', 'てすと', '2024-07-10 21:51:08'),
(7, 7, '20240714', NULL, '2024-07-14 01:58:40');

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
(6, 11),
(6, 12),
(6, 14),
(6, 15),
(6, 16),
(6, 17),
(6, 20),
(7, 32),
(7, 33),
(7, 34);

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
(7, 18, 'さわ\'s Group', '2024-07-04 15:43:32', '2024-07-04 15:43:32');

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
(10, 7, 18, '2024-07-04 15:43:32', '2024-07-04 15:43:32'),
(11, 7, 19, '2024-07-04 15:44:03', '2024-07-04 15:44:03'),
(12, 7, 20, '2024-07-04 15:48:00', '2024-07-04 15:48:00'),
(14, 7, 22, '2024-07-09 21:37:48', '2024-07-09 21:37:48');

-- --------------------------------------------------------

--
-- テーブルの構造 `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` enum('未対応','対応中','解決済み') DEFAULT '未対応',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(11, 18, 7, 'やること', 'あいうえお　かか\nっきい', NULL, 0, '2024-07-04 16:18:42', '2024-07-04 16:18:42', NULL, NULL),
(12, 18, 7, 'その他', 'あ', NULL, 0, '2024-07-04 16:23:54', '2024-07-11 09:05:03', 0, NULL),
(13, 18, 7, '買い物', 'いうああああ', NULL, 0, '2024-07-04 16:26:33', '2024-07-13 00:28:50', NULL, NULL);

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
(12, 11, 19, '2024-07-13 00:02:19'),
(13, 11, 20, '2024-07-13 00:02:19');

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
(25, 7, 18, '6688b2478662b.jpg', '', '2024-07-06 02:56:07'),
(26, 7, 18, '6688b2869e9f0.jpg', '', '2024-07-06 02:57:11'),
(27, 7, 18, '668e8a89dacdf.jpg', '', '2024-07-10 13:20:10'),
(28, 7, 18, '6691e3e74caaa.jpg', '', '2024-07-13 02:18:15'),
(29, 7, 18, 'photo_66932b9e42baf.jpg', 'あああ', '2024-07-14 01:36:30'),
(30, 7, 18, 'photo_66932b9ec9753.jpg', 'いいい', '2024-07-14 01:36:30'),
(31, 7, 18, 'photo_66932b9f58433.jpg', 'ううう', '2024-07-14 01:36:31'),
(32, 7, 18, 'photo_669330d018a67.jpg', '写真２　ですよ。', '2024-07-14 01:58:40'),
(33, 7, 18, 'photo_669330d02fecc.jpg', '写真３', '2024-07-14 01:58:40'),
(34, 7, 18, 'photo_669330d040f99.jpg', '写真１', '2024-07-14 01:58:40'),
(35, 7, 18, 'photo_6693338b97782.jpg', 'あ', '2024-07-14 02:10:19');

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
  `others` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `schedules`
--

INSERT INTO `schedules` (`id`, `group_id`, `user_id`, `date`, `content`, `others`, `created_at`, `updated_at`, `updated_by`) VALUES
(9, 7, 18, '2024-07-18', 'あああ', NULL, '2024-07-04 16:34:17', '2024-07-04 16:34:17', NULL),
(11, 7, 18, '2024-07-19', 'げんこつ山の狸さん\r\n最多最多チューリップの花が　　　咲いた', NULL, '2024-07-04 16:35:17', '2024-07-04 16:35:17', NULL),
(12, 7, 18, '2025-06-06', 'aaa', NULL, '2024-07-06 03:04:36', '2024-07-06 03:04:36', NULL),
(14, 7, 18, '2024-07-16', '雅恵：\r\n〇〇〇〇', '', '2024-07-07 01:58:46', '2024-07-10 11:53:09', 20),
(16, 7, 18, '2024-07-12', 'リハビリ。。。。。編集', '', '2024-07-09 22:17:55', '2024-07-13 00:38:27', 18),
(17, 7, 18, '2024-07-12', 'リハビリ', 'ああ', '2024-07-09 22:22:42', '2024-07-13 00:17:22', 18),
(18, 7, 18, '2024-07-13', 'えつこさんの予定を悦子さんにだけ見せる', '', '2024-07-09 22:37:52', '2024-07-09 22:37:52', NULL),
(21, 7, 20, '2024-07-12', '名古屋マリオット', '', '2024-07-10 11:54:17', '2024-07-10 12:19:27', 20),
(22, 7, 18, '2024-12-24', 'ｓｓ', '', '2024-07-10 13:07:32', '2024-07-10 13:07:32', 18);

-- --------------------------------------------------------

--
-- テーブルの構造 `schedule_for`
--

CREATE TABLE `schedule_for` (
  `id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `schedule_for`
--

INSERT INTO `schedule_for` (`id`, `schedule_id`, `user_id`, `created_at`) VALUES
(1, 9, 18, '2024-07-10 11:51:29'),
(3, 11, 18, '2024-07-10 11:51:29'),
(4, 12, 18, '2024-07-10 11:51:29'),
(6, 14, 18, '2024-07-10 11:51:29'),
(10, 18, 18, '2024-07-10 11:51:29'),
(20, 21, 18, '2024-07-10 12:19:27'),
(21, 21, 20, '2024-07-10 12:19:27'),
(22, 22, 18, '2024-07-10 13:07:32'),
(25, 17, 18, '2024-07-13 00:17:22'),
(28, 16, 18, '2024-07-13 00:38:27');

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
(9, 9, 19, '2024-07-04 16:34:17', '2024-07-04 16:34:17'),
(19, 18, 22, '2024-07-09 22:37:52', '2024-07-09 22:37:52'),
(20, 18, 22, '2024-07-09 22:37:52', '2024-07-09 22:37:52'),
(24, 14, 19, '2024-07-10 11:53:09', '2024-07-10 11:53:09'),
(25, 14, 19, '2024-07-10 11:53:09', '2024-07-10 11:53:09'),
(29, 21, 18, '2024-07-10 12:19:27', '2024-07-10 12:19:27'),
(30, 21, 19, '2024-07-10 12:19:27', '2024-07-10 12:19:27'),
(31, 22, 20, '2024-07-10 13:07:32', '2024-07-10 13:07:32'),
(37, 17, 19, '2024-07-13 00:17:22', '2024-07-13 00:17:22'),
(44, 16, 19, '2024-07-13 00:38:27', '2024-07-13 00:38:27'),
(45, 16, 20, '2024-07-13 00:38:27', '2024-07-13 00:38:27'),
(46, 16, 22, '2024-07-13 00:38:27', '2024-07-13 00:38:27');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`, `reset_token`, `token_expires`) VALUES
(18, 'さわ', 'sawa110291@gmail.com', '$2y$10$6gKmxdLrxsV9xCoV8OJYUe0jFpKZc.TB19i8brwk.79LzPmAYKGpG', 'admin', '2024-07-04 15:43:32', '2024-07-04 15:52:08', NULL, NULL),
(19, 'まさえ', NULL, '$2y$10$Z.x4mbDGtGPCkyidCy4b5OUE6IyR99tMuZKXvBB2.YxQvi/Mdtlce', 'view', '2024-07-04 15:44:03', '2024-07-04 15:44:03', NULL, NULL),
(20, 'かおる', 'sawatin@yahoo.co.jp', '$2y$10$ahmgZM1zqtvtTaBhSkKD/.gIBt/Ckdtwctp8slIsh06K/ua.ezhc6', 'modify', '2024-07-04 15:48:00', '2024-07-06 02:10:43', NULL, NULL),
(22, 'えつこ', NULL, '$2y$10$JX66SWhJUfL/m8kfYLHQa.bRFID84SsV7A2emaCEQoA4SX8eJ5bjK', 'view', '2024-07-09 21:37:48', '2024-07-09 21:37:48', NULL, NULL);

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
-- テーブルのインデックス `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
  ADD KEY `idx_schedules_date` (`date`),
  ADD KEY `updated_by` (`updated_by`);

--
-- テーブルのインデックス `schedule_for`
--
ALTER TABLE `schedule_for`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_schedule_for_schedule_id` (`schedule_id`),
  ADD KEY `idx_schedule_for_user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- テーブルの AUTO_INCREMENT `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `memos`
--
ALTER TABLE `memos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- テーブルの AUTO_INCREMENT `memo_shares`
--
ALTER TABLE `memo_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- テーブルの AUTO_INCREMENT `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- テーブルの AUTO_INCREMENT `schedule_for`
--
ALTER TABLE `schedule_for`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- テーブルの AUTO_INCREMENT `schedule_shares`
--
ALTER TABLE `schedule_shares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
-- テーブルの制約 `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `fk_inquiries_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `schedule_for`
--
ALTER TABLE `schedule_for`
  ADD CONSTRAINT `schedule_for_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_for_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
