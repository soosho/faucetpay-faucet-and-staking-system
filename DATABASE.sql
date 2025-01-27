-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 27, 2025 at 02:23 AM
-- Server version: 10.6.20-MariaDB-cll-lve-log
-- PHP Version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gemscsmy_faucethub`
--

-- --------------------------------------------------------

--
-- Table structure for table `betting_history`
--

CREATE TABLE `betting_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bet_amount` decimal(16,8) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'trx',
  `client_seed` varchar(64) NOT NULL,
  `server_seed` varchar(64) NOT NULL,
  `dice_roll` int(11) NOT NULL,
  `bet_choice` varchar(10) NOT NULL,
  `result` varchar(10) NOT NULL,
  `payout` decimal(16,8) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `amount_change` varchar(20) NOT NULL,
  `server_seed_hash` varchar(64) NOT NULL,
  `range_start` int(11) NOT NULL DEFAULT 0,
  `range_end` int(11) NOT NULL DEFAULT 9999
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crypto_rates`
--

CREATE TABLE `crypto_rates` (
  `id` int(11) NOT NULL,
  `crypto_name` varchar(255) NOT NULL,
  `rate_usd` decimal(16,8) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `crypto_rates`
--

INSERT INTO `crypto_rates` (`id`, `crypto_name`, `rate_usd`, `last_updated`) VALUES
(1, 'binancecoin', 730.83000000, '2025-01-07 08:54:03'),
(2, 'bitcoin', 101923.00000000, '2025-01-07 08:54:03'),
(3, 'bitcoin-cash', 478.08000000, '2025-01-07 08:54:03'),
(4, 'cardano', 1.13000000, '2025-01-07 08:54:03'),
(5, 'dash', 43.02000000, '2025-01-07 08:54:03'),
(6, 'digibyte', 0.01480684, '2025-01-07 08:54:03'),
(7, 'dogecoin', 0.39645500, '2025-01-07 08:54:03'),
(8, 'ethereum', 3676.30000000, '2025-01-07 08:54:03'),
(9, 'feyorra', 0.01160738, '2025-01-07 08:54:03'),
(10, 'litecoin', 113.39000000, '2025-01-07 08:54:03'),
(11, 'matic-network', 0.52328100, '2025-01-07 08:54:03'),
(12, 'monero', 201.77000000, '2025-01-07 08:54:03'),
(13, 'ripple', 2.45000000, '2025-01-07 08:54:03'),
(14, 'solana', 216.57000000, '2025-01-07 08:54:03'),
(15, 'stellar', 0.46012800, '2025-01-07 08:54:03'),
(16, 'taraxa', 0.00664902, '2025-01-07 08:54:03'),
(17, 'tether', 1.00000000, '2025-01-07 08:54:03'),
(18, 'the-open-network', 5.75000000, '2025-01-07 08:54:03'),
(19, 'tron', 0.27039700, '2025-01-07 08:54:03'),
(20, 'usd-coin', 0.99995600, '2025-01-07 08:54:03'),
(21, 'zcash', 57.92000000, '2025-01-07 08:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `daily_claims`
--

CREATE TABLE `daily_claims` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `claim_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount` decimal(16,8) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposit_requests`
--

CREATE TABLE `deposit_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(16,8) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faucetpay_balances`
--

CREATE TABLE `faucetpay_balances` (
  `id` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `balance` decimal(20,8) NOT NULL,
  `balance_usd` decimal(20,8) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_earnings`
--

CREATE TABLE `referral_earnings` (
  `id` int(11) NOT NULL,
  `referrer_id` int(11) NOT NULL,
  `referred_user_id` int(11) NOT NULL,
  `amount` decimal(18,8) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `bonus` decimal(18,8) NOT NULL,
  `earned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staking`
--

CREATE TABLE `staking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `amount` decimal(16,8) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `lock_period` int(11) NOT NULL,
  `refund` tinyint(1) DEFAULT 0,
  `status` enum('active','completed') DEFAULT 'active',
  `reward_rate` decimal(5,2) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `swap_history`
--

CREATE TABLE `swap_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `from_currency` varchar(10) DEFAULT NULL,
  `to_currency` varchar(10) DEFAULT NULL,
  `swap_amount` decimal(16,10) DEFAULT NULL,
  `fee` decimal(16,10) DEFAULT NULL,
  `final_amount` decimal(16,10) DEFAULT NULL,
  `swap_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `banned` tinyint(1) DEFAULT 0,
  `level` int(11) DEFAULT 1,
  `exp` int(11) DEFAULT 1,
  `is_verified` tinyint(1) DEFAULT 0,
  `balance` decimal(16,8) DEFAULT 0.00000000,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `btc_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `eth_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `doge_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `ltc_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `bch_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `dash_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `dgb_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `trx_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `usdt_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `fey_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `zec_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `bnb_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `sol_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `xrp_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `matic_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `ada_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `ton_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `xlm_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `usdc_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `xmr_balance` decimal(16,15) NOT NULL DEFAULT 0.000000000000000,
  `tara_balance` decimal(24,15) NOT NULL DEFAULT 0.000000000000000,
  `last_active` datetime DEFAULT current_timestamp(),
  `referrer_id` int(11) DEFAULT NULL,
  `device_fingerprint` varchar(255) DEFAULT NULL,
  `fingerprint` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `last_verification_request` timestamp NULL DEFAULT NULL,
  `login_token` varchar(255) DEFAULT NULL,
  `dark_mode` tinyint(1) DEFAULT 0,
  `last_reward_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `ban_reason` varchar(255) DEFAULT NULL,
  `banned_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_log`
--

CREATE TABLE `user_activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` enum('logged_in','logged_out') DEFAULT 'logged_in',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fingerprint` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_detection_log`
--

CREATE TABLE `user_detection_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `detected_fingerprint` text DEFAULT NULL,
  `detected_ip` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_levels`
--

CREATE TABLE `user_levels` (
  `level` int(11) NOT NULL,
  `experience_required` int(11) NOT NULL,
  `claim_bonus` decimal(5,2) NOT NULL DEFAULT 0.00
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_levels`
--

INSERT INTO `user_levels` (`level`, `experience_required`, `claim_bonus`) VALUES
(1, 1, 0.00),
(2, 10, 1.00),
(3, 25, 1.50),
(4, 50, 2.00),
(5, 85, 2.50),
(6, 130, 3.00),
(7, 185, 3.50),
(8, 250, 4.00),
(9, 325, 4.50),
(10, 410, 5.00),
(11, 505, 5.50),
(12, 610, 6.00),
(13, 725, 6.50),
(14, 850, 7.00),
(15, 985, 7.50),
(16, 1130, 8.00),
(17, 1285, 8.50),
(18, 1450, 9.00),
(19, 1625, 9.50),
(20, 1810, 10.00),
(21, 2005, 10.50),
(22, 2210, 11.00),
(23, 2425, 11.50),
(24, 2650, 12.00),
(25, 2885, 12.50),
(26, 3130, 13.00),
(27, 3385, 13.50),
(28, 3650, 14.00),
(29, 3925, 14.50),
(30, 4210, 15.00),
(31, 4505, 16.00),
(32, 4810, 17.00),
(33, 5125, 18.00),
(34, 5450, 19.00),
(35, 5785, 20.00),
(36, 6130, 21.00),
(37, 6485, 22.00),
(38, 6850, 23.00),
(39, 7225, 24.00),
(40, 7610, 25.00),
(41, 8005, 26.00),
(42, 8410, 27.00),
(43, 8825, 28.00),
(44, 9250, 29.00),
(45, 9685, 30.00),
(46, 10130, 31.00),
(47, 10585, 32.00),
(48, 11050, 33.00),
(49, 11525, 34.00),
(50, 12010, 35.00),
(51, 12505, 36.00),
(52, 13010, 37.00),
(53, 13525, 38.00),
(54, 14050, 39.00),
(55, 14585, 40.00),
(56, 15130, 41.00),
(57, 15685, 42.00),
(58, 16250, 43.00),
(59, 16825, 44.00),
(60, 17410, 45.00),
(61, 18005, 46.00),
(62, 18610, 47.00),
(63, 19225, 48.00),
(64, 19850, 49.00),
(65, 20485, 50.00),
(66, 21130, 51.00),
(67, 21785, 52.00),
(68, 22450, 53.00),
(69, 23125, 54.00),
(70, 23810, 55.00),
(71, 24505, 56.00),
(72, 25210, 57.00),
(73, 25925, 58.00),
(74, 26650, 59.00),
(75, 27385, 60.00),
(76, 28130, 61.00),
(77, 28885, 62.00),
(78, 29650, 63.00),
(79, 30425, 64.00),
(80, 31210, 65.00),
(81, 32005, 66.00),
(82, 32810, 67.00),
(83, 33625, 68.00),
(84, 34450, 69.00),
(85, 35285, 70.00),
(86, 36130, 71.00),
(87, 36985, 72.00),
(88, 37850, 73.00),
(89, 38725, 74.00),
(90, 39610, 75.00),
(91, 40505, 76.00),
(92, 41410, 77.00),
(93, 42325, 78.00),
(94, 43250, 79.00),
(95, 44185, 80.00),
(96, 45130, 81.00),
(97, 46085, 82.00),
(98, 47050, 83.00),
(99, 48025, 84.00),
(100, 64500, 85.00);

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_requests`
--

CREATE TABLE `withdraw_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(20,8) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `payout_user_hash` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `betting_history`
--
ALTER TABLE `betting_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crypto_rates`
--
ALTER TABLE `crypto_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crypto_name` (`crypto_name`);

--
-- Indexes for table `daily_claims`
--
ALTER TABLE `daily_claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `deposit_requests`
--
ALTER TABLE `deposit_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `faucetpay_balances`
--
ALTER TABLE `faucetpay_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currency` (`currency`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `referral_earnings`
--
ALTER TABLE `referral_earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referrer_id` (`referrer_id`),
  ADD KEY `referred_user_id` (`referred_user_id`);

--
-- Indexes for table `staking`
--
ALTER TABLE `staking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `swap_history`
--
ALTER TABLE `swap_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_detection_log`
--
ALTER TABLE `user_detection_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_levels`
--
ALTER TABLE `user_levels`
  ADD PRIMARY KEY (`level`);

--
-- Indexes for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `betting_history`
--
ALTER TABLE `betting_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crypto_rates`
--
ALTER TABLE `crypto_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `daily_claims`
--
ALTER TABLE `daily_claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposit_requests`
--
ALTER TABLE `deposit_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faucetpay_balances`
--
ALTER TABLE `faucetpay_balances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_earnings`
--
ALTER TABLE `referral_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staking`
--
ALTER TABLE `staking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `swap_history`
--
ALTER TABLE `swap_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_detection_log`
--
ALTER TABLE `user_detection_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
