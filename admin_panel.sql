-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2026 at 06:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_panel`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `aadhaar_number` varchar(255) DEFAULT NULL,
  `pan_number` varchar(255) DEFAULT NULL,
  `driving_license` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`modules`)),
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `admin_id`, `image`, `aadhaar_number`, `pan_number`, `driving_license`, `status`, `name`, `email`, `password`, `role`, `phone`, `modules`, `remember_token`, `created_at`, `updated_at`, `role_id`) VALUES
(2, 'ADM0001', NULL, '5407870003245', '5490045580', '34878934', 'approved', 'Super Admin', 'admin@gmail.com', '$2y$12$glKWtYAToETkFKLVF9v6G.PgTJzDNFker8fUKP1lyWZjNiKGW3Eaq', NULL, NULL, '[\r\n\"dashboard\",\r\n\"manage-users\",\r\n\"manage-games\",\r\n\"manage-wallet\",\r\n\"manage-withdraw\",\r\n\"manage-payment-history\",\r\n\"manage-winning-history\",\r\n\"manage-notification\",\r\n\"manage-admin\"\r\n]', NULL, '2026-02-24 12:45:03', '2026-02-25 04:45:48', 1),
(4, NULL, NULL, NULL, NULL, NULL, 'accepted', 'Super Admin', 'admin1@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9A2c8LxR9f7bF5nY8J9H6G', NULL, '9999999999', NULL, NULL, '2026-02-24 14:12:55', '2026-02-24 14:12:55', 1),
(5, 'ADM0005', NULL, NULL, NULL, NULL, 'approved', 'Main Super Admin', 'superadmin@gmail.com', '$2y$12$NZz4pKXNbDEMbwfcqJgHOu.PyjG1hehpujqROLpIXcfunLPOGq5zi', NULL, NULL, NULL, NULL, '2026-02-25 00:15:00', '2026-02-25 00:15:00', 1),
(6, 'ADM0006', '1771999604.jpeg', '5497979453', '8934285798', '3767349', 'pending', 'hhfhf', 's@gmail.com', '$2y$12$njW5g3dHh7kZccCE2pyUQeTHCJJbhMQ8I7ZvHxSb4yflOwDHVgp0.', NULL, '78577338', NULL, NULL, '2026-02-25 00:36:44', '2026-02-25 00:36:44', 2),
(7, 'ADM0007', '1772003083.jpeg', '547878564', '54877854', '54u8754', 'rejected', 'hhhhiiiii', 'sv@gmail.com', '$2y$12$Klrx.2GpQPDeIGXkuUQ2l.N0RucWqbqNZoepnyGixUAbZ6mhCLq4G', NULL, '8934588', NULL, NULL, '2026-02-25 01:34:43', '2026-02-25 04:45:24', 2),
(9, 'ADM0009', '1772185213.png', '36767945987', '47987894378', '54y9879834', 'rejected', 'hiiiill', 'pk@gmail.com', '$2y$12$jHCgPsv63KIzxSnyCmyxDOhwlOwvdSAhidoBWAxSyAFixHb8ZMNeW', NULL, '347675479845', NULL, NULL, '2026-02-27 04:10:14', '2026-02-27 04:20:40', 2),
(10, 'ADM0010', '1772186935.png', '3567778998', '349877945379', '3576778473', 'accepted', 'kahhahi', 'phw@gmail.com', '$2y$12$JUIwb6SEn67xdYCNvYCeR.Ftk9AZxAzOtyKaMhIJLF2GvM8jRbHHC', NULL, '7557577575', NULL, NULL, '2026-02-27 04:35:02', '2026-02-27 04:38:55', 2);

-- --------------------------------------------------------

--
-- Table structure for table `andar_plays`
--

CREATE TABLE `andar_plays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `game_id` bigint(20) UNSIGNED NOT NULL,
  `number` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `win_amount` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','win','lose','cancelled') NOT NULL DEFAULT 'pending',
  `is_price_config` tinyint(1) NOT NULL DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `andar_plays`
--

INSERT INTO `andar_plays` (`id`, `user_id`, `game_id`, `number`, `amount`, `win_amount`, `status`, `is_price_config`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '7', 100, 50, 'win', 0, 2.00, '2026-02-28 10:43:37', '2026-02-28 10:43:37'),
(2, 1, 1, '7', 200, 100, 'win', 0, 2.00, '2026-02-28 10:43:37', '2026-02-28 10:43:37'),
(3, 1, 1, '6', 150, 0, 'lose', 0, 2.00, '2026-02-28 10:43:37', '2026-02-28 10:43:37'),
(4, 1, 1, '3', 80, 0, 'lose', 0, 2.00, '2026-02-28 10:43:37', '2026-02-28 10:43:37'),
(5, 2, 3, '4', 220, 110, 'win', 0, 2.00, '2026-02-28 11:23:38', '2026-02-28 11:23:38'),
(6, 3, 3, '9', 180, 0, 'lose', 0, 2.00, '2026-02-28 11:23:38', '2026-02-28 11:23:38');

-- --------------------------------------------------------

--
-- Table structure for table `bahar_plays`
--

CREATE TABLE `bahar_plays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `game_id` bigint(20) UNSIGNED NOT NULL,
  `number` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `win_amount` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','win','lose','cancelled') NOT NULL DEFAULT 'pending',
  `is_price_config` tinyint(1) NOT NULL DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bahar_plays`
--

INSERT INTO `bahar_plays` (`id`, `user_id`, `game_id`, `number`, `amount`, `win_amount`, `status`, `is_price_config`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '7', 200, 100, 'win', 0, 2.00, '2026-02-28 10:46:05', '2026-02-28 10:46:05'),
(2, 1, 1, '7', 230, 115, 'win', 0, 2.00, '2026-02-28 10:46:05', '2026-02-28 10:46:05'),
(3, 1, 1, '6', 36, 0, 'lose', 0, 2.00, '2026-02-28 10:46:05', '2026-02-28 10:46:05'),
(4, 1, 1, '8', 430, 215, 'win', 0, 2.00, '2026-02-28 10:46:05', '2026-02-28 10:46:05'),
(5, 1, 1, '2', 120, 0, 'lose', 0, 2.00, '2026-02-28 10:46:05', '2026-02-28 10:46:05'),
(6, 2, 3, '5', 500, 250, 'win', 0, 2.00, '2026-02-28 11:23:54', '2026-02-28 11:23:54'),
(7, 4, 3, '3', 300, 0, 'lose', 0, 2.00, '2026-02-28 11:23:54', '2026-02-28 11:23:54');

-- --------------------------------------------------------

--
-- Table structure for table `bank_details`
--

CREATE TABLE `bank_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('play','close') NOT NULL DEFAULT 'play',
  `result_time` time DEFAULT NULL,
  `correct_answer` varchar(255) DEFAULT NULL,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL,
  `play_next_day` enum('yes','no') NOT NULL DEFAULT 'no',
  `play_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`play_days`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `game_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `status`, `result_time`, `correct_answer`, `open_time`, `close_time`, `play_next_day`, `play_days`, `created_at`, `updated_at`, `game_name`) VALUES
(1, 'play', '22:00:00', '7', '10:00:00', '21:00:00', 'yes', '[\"Mon\",\"Tue\",\"Wed\"]', '2026-02-27 12:53:58', '2026-02-27 12:53:58', 'Teen Patti'),
(2, 'play', '23:00:00', '5', '11:00:00', '22:00:00', 'no', '[\"Thu\",\"Fri\"]', '2026-02-27 12:53:58', '2026-02-27 12:53:58', 'Andar Bahar'),
(3, 'play', '22:00:00', '7', '10:00:00', '21:00:00', 'yes', '[\"Mon\",\"Tue\",\"Wed\"]', '2026-02-28 10:47:18', '2026-02-28 10:47:18', 'Teen Patti'),
(4, 'play', '23:00:00', '5', '11:00:00', '22:00:00', 'no', '[\"Thu\",\"Fri\"]', '2026-02-28 10:47:18', '2026-02-28 10:47:18', 'Andar Bahar'),
(5, '', '21:30:00', '3', '09:00:00', '20:00:00', 'yes', '[\"Sat\",\"Sun\"]', '2026-02-28 10:47:18', '2026-02-28 10:47:18', 'Disawar');

-- --------------------------------------------------------

--
-- Table structure for table `game_plays`
--

CREATE TABLE `game_plays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `game_id` bigint(20) UNSIGNED NOT NULL,
  `play_type` enum('jodi','crossing','copy_paste') NOT NULL,
  `number` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `win_amount` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','win','lose','cancelled') NOT NULL DEFAULT 'pending',
  `is_price_config` tinyint(1) NOT NULL DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `game_plays`
--

INSERT INTO `game_plays` (`id`, `user_id`, `game_id`, `play_type`, `number`, `amount`, `win_amount`, `status`, `is_price_config`, `price`, `created_at`, `updated_at`) VALUES
(6, 1, 1, '', '5', 500, 250, 'win', 1, 2.00, '2026-02-28 06:35:26', '2026-02-28 06:35:26'),
(7, 1, 2, '', '3', 300, 0, 'lose', 1, 2.00, '2026-02-28 06:35:26', '2026-02-28 06:35:26'),
(8, 1, 1, '', '8', 1000, 500, 'win', 0, 2.00, '2026-02-28 06:35:26', '2026-02-28 06:35:26'),
(9, 1, 2, '', '1', 700, 0, 'lose', 1, 1.50, '2026-02-28 06:35:26', '2026-02-28 06:35:26'),
(10, 1, 1, 'jodi', '5', 500, 250, 'win', 1, 2.00, '2026-02-28 06:37:13', '2026-02-28 06:37:13'),
(11, 1, 2, 'crossing', '3', 300, 0, 'lose', 1, 2.00, '2026-02-28 06:37:13', '2026-02-28 06:37:13'),
(12, 1, 1, 'copy_paste', '8', 1000, 500, 'win', 0, 2.00, '2026-02-28 06:37:13', '2026-02-28 06:37:13'),
(13, 1, 2, 'jodi', '1', 700, 0, 'lose', 1, 1.50, '2026-02-28 06:37:13', '2026-02-28 06:37:13'),
(14, 1, 2, 'jodi', '5', 500, 250, 'win', 0, 2.00, '2026-02-28 11:05:04', '2026-02-28 11:05:04'),
(15, 1, 2, 'jodi', '5', 200, 100, 'win', 0, 2.00, '2026-02-28 11:05:04', '2026-02-28 11:05:04'),
(16, 1, 2, 'jodi', '8', 1000, 500, 'win', 0, 2.00, '2026-02-28 11:05:04', '2026-02-28 11:05:04'),
(17, 1, 2, 'jodi', '1', 700, 0, 'lose', 0, 2.00, '2026-02-28 11:05:04', '2026-02-28 11:05:04'),
(18, 1, 2, 'jodi', '3', 300, 0, 'lose', 0, 2.00, '2026-02-28 11:05:04', '2026-02-28 11:05:04'),
(19, 1, 3, 'jodi', '0', 100, 0, 'lose', 0, 2.00, '2026-02-28 11:06:19', '2026-02-28 11:06:19'),
(20, 1, 3, 'jodi', '0', 250, 125, 'win', 0, 2.00, '2026-02-28 11:06:19', '2026-02-28 11:06:19'),
(21, 1, 3, 'jodi', '4', 500, 250, 'win', 0, 2.00, '2026-02-28 11:06:19', '2026-02-28 11:06:19'),
(22, 1, 3, 'jodi', '7', 300, 0, 'lose', 0, 2.00, '2026-02-28 11:06:19', '2026-02-28 11:06:19'),
(23, 1, 3, 'jodi', '9', 900, 450, 'win', 0, 2.00, '2026-02-28 11:06:19', '2026-02-28 11:06:19'),
(24, 2, 3, 'jodi', '2', 600, 300, 'win', 0, 2.00, '2026-02-28 11:20:29', '2026-02-28 11:20:29'),
(25, 3, 3, 'jodi', '6', 450, 0, 'lose', 0, 2.00, '2026-02-28 11:20:29', '2026-02-28 11:20:29'),
(26, 4, 3, 'jodi', '8', 750, 375, 'win', 0, 2.00, '2026-02-28 11:20:29', '2026-02-28 11:20:29'),
(27, 5, 3, 'jodi', '1', 900, 0, 'lose', 0, 2.00, '2026-02-28 11:20:29', '2026-02-28 11:20:29');

-- --------------------------------------------------------

--
-- Table structure for table `game_prices`
--

CREATE TABLE `game_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `game_type` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `multiply` int(11) NOT NULL DEFAULT 10,
  `grand_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_16_080343_create_notifications_table', 1),
(5, '2026_02_16_101330_create_wallets_table', 1),
(6, '2026_02_16_111449_create_withdraws_table', 1),
(7, '2026_02_16_111726_create_winnings_table', 1),
(8, '2026_02_16_125237_create_payments_table', 1),
(9, '2026_02_17_054444_create_admins_table', 1),
(10, '2026_02_17_122038_add_role_phone_modules_to_admins_table', 1),
(11, '2026_02_18_063243_create_roles_table', 1),
(12, '2026_02_18_063344_add_role_id_to_admins_table', 1),
(13, '2026_02_18_074117_create_permissions_table', 1),
(14, '2026_02_18_074251_create_permission_role_table', 1),
(15, '2026_02_18_074347_add_role_id_to_admins_table', 1),
(16, '2026_02_20_063106_add_extra_fields_to_admins_table', 1),
(17, '2026_02_24_112937_create_games_table', 1),
(18, '2026_02_19_100537_create_personal_access_tokens_table', 2),
(19, '2026_02_19_110501_alter_email_nullable_in_users_table', 2),
(20, '2026_02_19_125621_create_bank_details_table', 3),
(21, '2026_02_20_103749_create_games_table', 4),
(22, '2026_02_20_111401_create_game_plays_table', 4),
(23, '2026_02_21_094929_update_withdraws_table', 4),
(24, '2026_02_23_104801_update_game_plays_status', 4),
(25, '2026_02_24_081147_remove_user_id_from_games_table', 4),
(26, '2026_02_25_045138_add_game_fields_to_games_table', 4),
(27, '2026_02_25_050903_remove_city_name_from_games_table', 4),
(28, '2026_02_26_092723_create_andar_plays_table', 4),
(29, '2026_02_26_092819_create_bahar_plays_table', 4),
(30, '2026_02_27_044747_create_user_wallets_table', 5),
(31, '2026_02_27_060543_add_cancelled_status_to_game_plays_table', 5),
(32, '2026_02_27_062041_add_cancelled_status_to_andar_plays_table', 5),
(33, '2026_02_27_062127_add_cancelled_status_to_bahar_plays_table', 5),
(34, '2026_02_27_130359_add_win_amount_to_all_plays_tables', 5),
(35, '2026_02_28_044310_add_price_columns_to_game_plays_tables', 5),
(36, '2026_02_28_071338_create_game_prices_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(2, 'Wallet Credited', '₹500 has been credited to your wallet.', 0, '2026-02-27 13:44:23', '2026-02-27 13:44:23'),
(3, 'Game Result', 'Teen Patti result declared successfully.', 1, '2026-02-27 13:44:23', '2026-02-27 13:44:23'),
(4, 'Withdrawal Approved', 'Your withdrawal request has been approved.', 0, '2026-02-27 13:44:23', '2026-02-27 13:44:23'),
(5, 'System Update', 'New feature added in dashboard.', 1, '2026-02-27 13:44:23', '2026-02-27 13:44:23'),
(6, 'hiii', 'ihii', 0, '2026-03-02 00:20:35', '2026-03-02 00:20:35'),
(7, 'hihi', 'whkfds', 0, '2026-03-02 00:24:22', '2026-03-02 00:24:22');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `amount`, `payment_method`, `transaction_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 5000.00, 'UPI', 'PAY1001', 'success', '2026-02-27 12:24:29', '2026-02-27 12:24:29'),
(2, 1, 2500.00, 'UPI', 'PAY1002', 'success', '2026-02-27 12:24:29', '2026-02-27 12:24:29'),
(3, 1, 1000.00, 'Card', 'PAY1003', 'success', '2026-02-27 12:24:29', '2026-02-27 12:24:29'),
(4, 1, 1500.00, 'UPI', 'PAY1004', 'pending', '2026-02-27 12:24:29', '2026-02-27 12:24:29'),
(5, 1, 2000.00, 'Card', 'PAY1005', 'failed', '2026-02-27 12:24:29', '2026-02-27 12:24:29');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard', '2026-02-24 11:52:14', '2026-02-24 11:52:14'),
(2, 'users.view', '2026-02-24 11:52:14', '2026-02-24 11:52:14'),
(3, 'game.view', '2026-02-24 11:52:14', '2026-02-24 11:52:14'),
(4, 'wallet.view', '2026-02-24 11:52:14', '2026-02-24 11:52:14'),
(5, 'withdraw.view', '2026-02-24 11:52:14', '2026-02-24 11:52:14'),
(6, 'payment.view', '2026-02-24 11:52:14', '2026-02-24 11:52:14'),
(7, 'winning.view', '2026-02-24 11:52:14', '2026-02-24 11:52:14'),
(8, 'notification.view', '2026-02-24 11:52:14', '2026-02-24 11:52:14'),
(9, 'admin.manage', '2026-02-24 11:52:14', '2026-02-24 11:52:14');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', NULL, '2026-02-24 12:26:05', '2026-02-24 12:26:05'),
(2, 'Sub Admin', NULL, '2026-02-24 12:26:05', '2026-02-24 12:26:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cin` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `cin`, `name`, `email`, `phone`, `role`, `status`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'ADMIN001', 'Super Admin', 'admin@gmail.com', '9999999999', 'admin', 'inactive', '2026-02-24 14:15:01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9A2c8LxR9f7bF5nY8J9H6G', NULL, '2026-02-24 14:15:01', '2026-02-27 06:06:20'),
(2, NULL, 'Demo User', 'demo@example.com', '1234567890\r\n', 'user', 'active', NULL, '$2y$10$1234567890123456789012', NULL, '2026-02-27 11:51:12', '2026-02-27 11:51:12'),
(3, NULL, 'User One', 'user1@test.com', NULL, 'user', 'active', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, '2026-02-28 06:00:03', '2026-02-28 06:00:03'),
(4, NULL, 'User Two', 'user2@test.com', NULL, 'user', 'active', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, '2026-02-28 06:00:03', '2026-02-28 06:00:03'),
(5, NULL, 'User Three', 'user3@test.com', NULL, 'user', 'active', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, '2026-02-28 06:00:03', '2026-02-28 06:00:03'),
(6, NULL, 'User Four', 'user4@test.com', NULL, 'user', 'active', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, '2026-02-28 06:00:03', '2026-02-28 06:00:03'),
(7, NULL, 'User Five', 'user5@test.com', NULL, 'user', 'active', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, '2026-02-28 06:00:03', '2026-02-28 06:00:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_wallets`
--

CREATE TABLE `user_wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_wallets`
--

INSERT INTO `user_wallets` (`id`, `user_id`, `balance`, `created_at`, `updated_at`) VALUES
(11, 1, 5000.00, '2026-02-28 06:00:38', '2026-02-28 06:00:38'),
(12, 2, 3000.00, '2026-02-28 06:00:38', '2026-02-28 06:00:38'),
(13, 3, 7000.00, '2026-02-28 06:00:38', '2026-02-28 06:00:38'),
(14, 4, 1500.00, '2026-02-28 06:00:38', '2026-02-28 06:00:38'),
(15, 5, 9200.00, '2026-02-28 06:00:38', '2026-02-28 06:00:38');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `cin` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL DEFAULT 'credit',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `cin`, `amount`, `type`, `created_at`, `updated_at`) VALUES
(11, 1, 'TXN002', 1000.00, 'credit', '2026-02-27 11:51:28', '2026-02-27 11:51:28'),
(12, 1, 'TXN003', 300.00, 'debit', '2026-02-27 11:51:28', '2026-02-27 11:51:28'),
(13, 1, 'TXN1001', 1500.00, 'credit', '2026-02-27 12:09:37', '2026-02-27 12:09:37'),
(14, 1, 'TXN1002', 2000.00, 'credit', '2026-02-27 12:09:37', '2026-02-27 12:09:37'),
(15, 1, 'TXN1003', 750.00, 'credit', '2026-02-27 12:09:37', '2026-02-27 12:09:37'),
(16, 1, 'TXN1004', 500.00, 'debit', '2026-02-27 12:09:37', '2026-02-27 12:09:37'),
(17, 1, 'TXN1005', 1200.00, 'debit', '2026-02-27 12:09:37', '2026-02-27 12:09:37'),
(18, 1, 'TXN1006', 3000.00, 'credit', '2026-02-27 12:09:37', '2026-02-27 12:09:37'),
(19, 1, 'TXN1007', 450.00, 'debit', '2026-02-27 12:09:37', '2026-02-27 12:09:37');

-- --------------------------------------------------------

--
-- Table structure for table `winnings`
--

CREATE TABLE `winnings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `game_name` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `winnings`
--

INSERT INTO `winnings` (`id`, `user_id`, `amount`, `game_name`, `status`, `created_at`, `updated_at`) VALUES
(7, 1, 500.00, 'Teen Patti', 'approved', '2026-02-27 12:31:38', '2026-02-27 12:31:38'),
(8, 1, 1200.00, 'Andar Bahar', 'approved', '2026-02-27 12:31:38', '2026-02-27 12:31:38'),
(9, 1, 800.00, 'Poker', 'approved', '2026-02-27 12:31:38', '2026-02-27 12:31:38'),
(10, 1, 300.00, 'Teen Patti', 'rejected', '2026-02-27 12:31:38', '2026-02-27 12:31:38'),
(11, 1, 1500.00, 'Andar Bahar', 'approved', '2026-02-27 12:31:38', '2026-02-27 12:31:38'),
(12, 1, 400.00, 'Poker', 'pending', '2026-02-27 12:31:38', '2026-02-27 12:31:38');

-- --------------------------------------------------------

--
-- Table structure for table `withdraws`
--

CREATE TABLE `withdraws` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `payment_mode` varchar(255) NOT NULL,
  `cin` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('processing','approved','rejected') NOT NULL DEFAULT 'processing',
  `detail` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`),
  ADD UNIQUE KEY `admins_admin_id_unique` (`admin_id`),
  ADD KEY `admins_role_id_foreign` (`role_id`);

--
-- Indexes for table `andar_plays`
--
ALTER TABLE `andar_plays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `andar_plays_user_id_foreign` (`user_id`),
  ADD KEY `andar_plays_game_id_foreign` (`game_id`);

--
-- Indexes for table `bahar_plays`
--
ALTER TABLE `bahar_plays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bahar_plays_user_id_foreign` (`user_id`),
  ADD KEY `bahar_plays_game_id_foreign` (`game_id`);

--
-- Indexes for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_details_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_plays`
--
ALTER TABLE `game_plays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_plays_user_id_foreign` (`user_id`),
  ADD KEY `game_plays_game_id_foreign` (`game_id`);

--
-- Indexes for table `game_prices`
--
ALTER TABLE `game_prices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  ADD KEY `payments_user_id_foreign` (`user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_role_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_cin_unique` (`cin`);

--
-- Indexes for table `user_wallets`
--
ALTER TABLE `user_wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_wallets_user_id_unique` (`user_id`),
  ADD KEY `user_wallets_user_id_index` (`user_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallets_cin_index` (`cin`),
  ADD KEY `wallets_created_at_index` (`created_at`),
  ADD KEY `wallets_user_id_index` (`user_id`);

--
-- Indexes for table `winnings`
--
ALTER TABLE `winnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `winnings_user_id_foreign` (`user_id`);

--
-- Indexes for table `withdraws`
--
ALTER TABLE `withdraws`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdraws_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `andar_plays`
--
ALTER TABLE `andar_plays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bahar_plays`
--
ALTER TABLE `bahar_plays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bank_details`
--
ALTER TABLE `bank_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `game_plays`
--
ALTER TABLE `game_plays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `game_prices`
--
ALTER TABLE `game_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_wallets`
--
ALTER TABLE `user_wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `winnings`
--
ALTER TABLE `winnings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `withdraws`
--
ALTER TABLE `withdraws`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `andar_plays`
--
ALTER TABLE `andar_plays`
  ADD CONSTRAINT `andar_plays_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `andar_plays_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bahar_plays`
--
ALTER TABLE `bahar_plays`
  ADD CONSTRAINT `bahar_plays_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bahar_plays_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD CONSTRAINT `bank_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_plays`
--
ALTER TABLE `game_plays`
  ADD CONSTRAINT `game_plays_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_plays_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_wallets`
--
ALTER TABLE `user_wallets`
  ADD CONSTRAINT `user_wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `winnings`
--
ALTER TABLE `winnings`
  ADD CONSTRAINT `winnings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdraws`
--
ALTER TABLE `withdraws`
  ADD CONSTRAINT `withdraws_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
