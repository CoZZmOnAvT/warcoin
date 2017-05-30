-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Трв 30 2017 р., 08:44
-- Версія сервера: 5.7.14
-- Версія PHP: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `war_db`
--

-- --------------------------------------------------------

--
-- Структура таблиці `balance_add_log`
--

CREATE TABLE `balance_add_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `lotery_log`
--

CREATE TABLE `lotery_log` (
  `id` int(11) NOT NULL,
  `bank` int(11) NOT NULL DEFAULT '0',
  `winner_id` int(11) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `lotery_members`
--

CREATE TABLE `lotery_members` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lotery_id` int(10) UNSIGNED NOT NULL,
  `tikets_bought` int(11) NOT NULL DEFAULT '0',
  `won` tinyint(1) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `online_users`
--

CREATE TABLE `online_users` (
  `user_id` int(11) NOT NULL,
  `user_ip` varchar(20) NOT NULL DEFAULT '0',
  `unix` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `roulette_stats`
--

CREATE TABLE `roulette_stats` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `weapon_price` int(11) NOT NULL,
  `weapon_img` varchar(100) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `users_balance`
--

CREATE TABLE `users_balance` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `balance_kredits` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `balance_bonus` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `balance_freeze` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `users_info`
--

CREATE TABLE `users_info` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_screen_name` varchar(20) NOT NULL,
  `photo_200` varchar(200) NOT NULL,
  `photo_50` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `users_registered`
--

CREATE TABLE `users_registered` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `access_token` varchar(200) NOT NULL,
  `expires_in` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `referal` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `users_stats`
--

CREATE TABLE `users_stats` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `games_count` int(11) NOT NULL DEFAULT '0',
  `tickets_count` int(11) DEFAULT '0',
  `test_spin` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `god_mode` tinyint(1) NOT NULL DEFAULT '0',
  `youtube` tinyint(1) NOT NULL DEFAULT '0',
  `reg_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `withdraw_requests`
--

CREATE TABLE `withdraw_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `withdraw_email` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `request_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `balance_add_log`
--
ALTER TABLE `balance_add_log`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `lotery_log`
--
ALTER TABLE `lotery_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Індекси таблиці `lotery_members`
--
ALTER TABLE `lotery_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_2` (`id`),
  ADD KEY `id` (`id`);

--
-- Індекси таблиці `online_users`
--
ALTER TABLE `online_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Індекси таблиці `roulette_stats`
--
ALTER TABLE `roulette_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Індекси таблиці `users_balance`
--
ALTER TABLE `users_balance`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Індекси таблиці `users_info`
--
ALTER TABLE `users_info`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Індекси таблиці `users_registered`
--
ALTER TABLE `users_registered`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Індекси таблиці `users_stats`
--
ALTER TABLE `users_stats`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Індекси таблиці `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `balance_add_log`
--
ALTER TABLE `balance_add_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100023;
--
-- AUTO_INCREMENT для таблиці `lotery_log`
--
ALTER TABLE `lotery_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT для таблиці `lotery_members`
--
ALTER TABLE `lotery_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT для таблиці `roulette_stats`
--
ALTER TABLE `roulette_stats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17778;
--
-- AUTO_INCREMENT для таблиці `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
