-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-04-19 13:35:52
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
-- データベース: `sawa_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `sawa_an_table09`
--

CREATE TABLE `sawa_an_table09` (
  `id` int(12) NOT NULL,
  `name` varchar(64) NOT NULL,
  `email` varchar(256) NOT NULL,
  `age` varchar(64) NOT NULL,
  `satisfaction` int(3) NOT NULL,
  `naiyou` text DEFAULT NULL,
  `indate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `sawa_an_table09`
--

INSERT INTO `sawa_an_table09` (`id`, `name`, `email`, `age`, `satisfaction`, `naiyou`, `indate`) VALUES
(2, 'test1更新100', 'test1koushin2@test.jp', '20～39歳', 5, 'test　更新2', '2024-04-06 14:44:59'),
(3, 'test2', 'test2@test.jp', '20～39歳', 3, 'test', '2024-04-06 14:44:59'),
(4, 'test3', 'test3@test.jp', '60歳以上', 4, 'test', '2024-04-06 14:44:59'),
(5, 'test4', 'test4@test.jp', '20～39歳', 3, 'test', '2024-04-06 14:44:59'),
(6, 'test5', 'test5@test.jp', '60歳以上', 2, 'test', '2024-04-06 14:44:59'),
(7, 'test6', 'test6@test.jp', '20～39歳', 2, 'test', '2024-04-06 14:44:59'),
(8, 'test7', 'test7@test.jp', '20～39歳', 5, 'test', '2024-04-06 14:44:59'),
(9, 'test8', 'test8@test.jp', '40～59歳', 5, 'test', '2024-04-06 14:44:59'),
(10, 'test9', 'test9@test.jp', '20～39歳', 3, 'test', '2024-04-06 14:44:59'),
(11, 'あいうえおかきくけこ', 'sawa2@yahoo.co.jp', '20～39歳', 5, 'ああああああああああああああああああ', '2024-04-06 16:30:35');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `sawa_an_table09`
--
ALTER TABLE `sawa_an_table09`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `sawa_an_table09`
--
ALTER TABLE `sawa_an_table09`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
