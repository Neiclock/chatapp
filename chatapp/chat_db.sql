-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:3306
-- 產生時間： 2024 年 03 月 22 日 00:05
-- 伺服器版本： 8.0.36-0ubuntu0.22.04.1
-- PHP 版本： 8.1.2-1ubuntu2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫: `chat_db`
--

-- --------------------------------------------------------

--
-- 資料表結構 `chat`
--

CREATE TABLE `chat` (
  `chatid` int NOT NULL,
  `userid` int NOT NULL,
  `chatroomid` int NOT NULL,
  `message` varchar(200) NOT NULL,
  `chat_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `chat`
--

INSERT INTO `chat` (`chatid`, `userid`, `chatroomid`, `message`, `chat_date`) VALUES
(100, 21, 82, 'Hello everyone', '2024-03-21 23:46:03'),
(101, 22, 82, 'Hi Mike', '2024-03-21 23:46:31'),
(102, 23, 82, 'Hi, I am Petter', '2024-03-21 23:47:14'),
(103, 24, 82, 'Tom&#39;s there', '2024-03-21 23:47:50'),
(104, 24, 82, '!!!', '2024-03-21 23:48:22'),
(105, 25, 82, 'I like comp3421!!!', '2024-03-21 23:49:22'),
(107, 27, 84, 'I love football!!!', '2024-03-21 23:55:22'),
(108, 28, 84, 'Which football team will you support?', '2024-03-21 23:56:22');

-- --------------------------------------------------------

--
-- 資料表結構 `chatroom`
--

CREATE TABLE `chatroom` (
  `chatroomid` int NOT NULL,
  `chat_title` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `chatroom`
--

INSERT INTO `chatroom` (`chatroomid`, `chat_title`, `description`, `userid`) VALUES
(82, 'comp3421', 'Welcome to comp3421', 21),
(83, 'School life', 'Share your polyu life here', 26),
(84, 'Hobby', 'Talk about your hobby!', 26),
(85, 'English Center', 'Learning English', 28),
(86, 'Math Center', 'Learning Math', 28),
(87, 'Video Game', 'Share you favorite game', 28),
(88, 'Animation', 'Share your favorite animation', 28);

-- --------------------------------------------------------

--
-- 資料表結構 `chat_member`
--

CREATE TABLE `chat_member` (
  `chat_memberid` int NOT NULL,
  `chatroomid` int NOT NULL,
  `userid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 傾印資料表的資料 `chat_member`
--

INSERT INTO `chat_member` (`chat_memberid`, `chatroomid`, `userid`) VALUES
(512, 82, 21),
(514, 82, 22),
(515, 82, 23),
(516, 82, 24),
(517, 82, 25),
(518, 82, 26),
(560, 82, 27),
(565, 82, 29),
(570, 82, 30),
(521, 83, 21),
(531, 83, 22),
(548, 83, 25),
(519, 83, 26),
(571, 83, 30),
(522, 84, 21),
(530, 84, 22),
(536, 84, 23),
(549, 84, 25),
(520, 84, 26),
(523, 84, 27),
(526, 84, 28),
(566, 84, 29),
(529, 85, 22),
(537, 85, 23),
(542, 85, 24),
(550, 85, 25),
(557, 85, 26),
(562, 85, 27),
(528, 85, 28),
(567, 85, 29),
(572, 85, 30),
(532, 86, 22),
(538, 86, 23),
(543, 86, 24),
(551, 86, 25),
(558, 86, 26),
(563, 86, 27),
(527, 86, 28),
(568, 86, 29),
(573, 86, 30),
(533, 87, 22),
(539, 87, 23),
(545, 87, 24),
(552, 87, 25),
(559, 87, 26),
(564, 87, 27),
(569, 87, 29),
(574, 87, 30),
(534, 88, 22),
(540, 88, 23),
(544, 88, 24),
(553, 88, 25),
(575, 88, 30);

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `user_ID` int NOT NULL,
  `Login_ID` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `NickName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `profile_images` varchar(255) NOT NULL DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`user_ID`, `Login_ID`, `Password`, `NickName`, `Email`, `profile_images`) VALUES
(21, '12345', '$2y$10$AqZli5/KalPZ79ptbQh.MOxvMpGYhHi/NzJhA7icZFdGGIDLHtxnG', 'Mike', 'MIke@gmail.com', 'uploads/77ec6c7454352a30e5f0f5d195682359.png'),
(22, '12346', '$2y$10$Rbp55nMiG5aIWftl71Y6Qe1RCSzwpqhupg9QDhvzzD4P63ffmKk6.', 'John', 'John@gmail.com', 'uploads/99ca86d5114500b29891ef1ac00b89df.png'),
(23, '12347', '$2y$10$xcIoKOaX0n0GXY1vkejqWemFBNFJWh3RjSfqvyn0kJN2lyAwrCdzy', 'Petter', 'Petter@gmail.com', 'uploads/8be3df97de61ba4aae67abbc8c278716.png'),
(24, '12348', '$2y$10$MWgul1NEAObb3jmdNBm1yue4HYcEmaI7dvAHrFvm39eMMKfFirUEy', 'Tom', 'Tom@gmail.com', 'uploads/3ffc7c0af48cd6707485c817e3b79481.png'),
(25, '12349', '$2y$10$Y8Lwa4kiLDxFJgN7BQw0sufvF3EI2Zzu8ZsTSTflfdXKXQGw0Rl9i', 'Helen', 'Helen@gmail.com', 'uploads/e3acce01df91f9edc194439898288e0a.png'),
(26, '12350', '$2y$10$zIkmL/QfwKqcthnqCx6P1uzpCDDjjR/D5Wd5xI9HUevAeh6T/uH.S', 'Alice', 'Alice@gmail.com', 'uploads/18e8f453de3f0f14404135c6d88fe558.png'),
(27, '12351', '$2y$10$SCq9zMM.ndxEMhDg/9CbKuivMKKZOS47kbRAPWTZ.677V3xg1ZxWe', 'Kitty', 'Kitty@gmail.com', 'uploads/b416e495d1b247a8344ba0b883d1d4d0.png'),
(28, '12352', '$2y$10$YurLTc9chhX/TKyB1rnf1eL8xWhBu96nvoXug/I.jEXATFA7ZHZka', 'Tommy', 'Tommy@gmail.com', 'uploads/f63274cbb8c56ac278f57d6617ef7a8b.png'),
(29, '12353', '$2y$10$G9ntvD8xS4OfxBVWI9Ba..xvrCxBlzIBN8kdASvtKVsav1.vnbYhm', 'Jack', 'Jack@gmail.com', 'uploads/aba72959510c319687dbedb30e67e5f7.png'),
(30, '12354', '$2y$10$7LBq.vRfF4ieLNv1Bwl5eeroHvWRUfe770J21jqAfSqm17wYmxX8i', 'Steven', 'Steven@gmail.com', 'uploads/a179c4247dac64d82e87ea882e6991db.png');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`chatid`);

--
-- 資料表索引 `chatroom`
--
ALTER TABLE `chatroom`
  ADD PRIMARY KEY (`chatroomid`);

--
-- 資料表索引 `chat_member`
--
ALTER TABLE `chat_member`
  ADD PRIMARY KEY (`chat_memberid`),
  ADD UNIQUE KEY `unique_chat_user` (`chatroomid`,`userid`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `chat`
--
ALTER TABLE `chat`
  MODIFY `chatid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `chatroom`
--
ALTER TABLE `chatroom`
  MODIFY `chatroomid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `chat_member`
--
ALTER TABLE `chat_member`
  MODIFY `chat_memberid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=579;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
