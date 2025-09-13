-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2025 at 04:04 PM
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
-- Database: `food_donation_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_trail`
--

CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `role` enum('admin','donor','organization') DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_trail`
--

INSERT INTO `audit_trail` (`id`, `user_id`, `action`, `description`, `role`, `timestamp`) VALUES
(35, NULL, 'Undid approval for donation with ID: 50', '', 'admin', '2025-08-29 02:33:35'),
(36, 2, 'Undid approval for donation with ID: 49', '', 'admin', '2025-08-29 02:36:49'),
(37, 2, 'Undid approval for donation with ID: 48', '', 'admin', '2025-08-29 02:37:44'),
(38, 2, 'Undid approval for donation with ID: 45', '', 'admin', '2025-08-29 02:38:03'),
(39, 2, 'Approved donation with ID: 52', '', 'admin', '2025-08-29 02:45:25'),
(40, 2, 'Undid status change for user account with ID: 13', '', 'admin', '2025-08-29 04:04:29'),
(41, 2, 'Approved user account with ID: 13', '', 'admin', '2025-08-29 04:04:32'),
(42, 2, 'Undid status change for user account with ID: 13', '', 'admin', '2025-08-29 04:05:10'),
(43, 2, 'Approved post with ID: 8', '', 'admin', '2025-08-29 04:07:31'),
(44, 2, 'Approved post with ID: 7', '', 'admin', '2025-08-29 06:08:37'),
(45, 2, 'Approved post with ID: 6', '', 'admin', '2025-08-29 06:08:43'),
(46, 2, 'Undid approval/denial for post with ID: 10', '', 'admin', '2025-08-29 06:08:53'),
(47, 2, 'Denied post with ID: 10', '', 'admin', '2025-08-29 06:10:56'),
(48, 2, 'Approved user account with ID: 13', '', 'admin', '2025-08-29 06:25:48'),
(49, 2, 'Undid status change for user account with ID: 13', '', 'admin', '2025-08-29 06:25:51'),
(50, 2, 'Undid approval/denial for post with ID: 9', '', 'admin', '2025-08-29 06:26:03'),
(51, 2, 'Approved post with ID: 9', '', 'admin', '2025-08-29 06:26:06'),
(52, 2, 'Undid approval/denial for post with ID: 9', '', 'admin', '2025-08-29 06:26:20'),
(53, 2, 'Undid approval/denial for post with ID: 8', '', 'admin', '2025-08-29 11:04:38'),
(54, 2, 'Denied post with ID: 8', '', 'admin', '2025-08-29 11:04:41'),
(55, 2, 'Created a new user account for: DEVELOPER 2 with role: admin', '', 'admin', '2025-08-29 11:54:36'),
(56, 2, 'Approved donation with ID: 53', '', 'admin', '2025-08-29 13:09:38'),
(57, 2, 'Created a new user account for: Maffi with role: admin', '', 'admin', '2025-08-29 13:11:41'),
(58, 2, 'Deleted admin account for: maffi2@yahoo.com', '', 'admin', '2025-08-29 14:02:59'),
(59, 2, 'Undid deletion for: maffi2@yahoo.com', '', 'admin', '2025-08-29 14:03:07'),
(60, 2, 'Denied post with ID: 9', '', 'admin', '2025-08-29 14:09:25'),
(61, 2, 'Undid approval/denial for post with ID: 10', '', 'admin', '2025-08-29 14:09:29'),
(62, 2, 'Undid approval for donation with ID: 33', '', 'admin', '2025-08-29 15:21:39'),
(63, 2, 'Rejected donation with ID: 33', '', 'admin', '2025-08-29 15:22:47'),
(64, 2, 'Rejected donation with ID: 57', '', 'admin', '2025-09-01 15:49:55'),
(65, 2, 'Rejected donation with ID: 56', '', 'admin', '2025-09-01 15:50:14'),
(66, 2, 'Rejected donation with ID: 55', '', 'admin', '2025-09-01 15:50:26'),
(67, 2, 'Rejected donation with ID: 54', '', 'admin', '2025-09-01 15:52:04'),
(68, 2, 'Undid status change for user account with ID: 15', '', 'admin', '2025-09-01 15:54:17'),
(69, 2, 'Rejected user account with ID: 15', '', 'admin', '2025-09-01 15:54:22'),
(70, 2, 'Undid approval for donation with ID: 57', '', 'admin', '2025-09-01 15:55:46'),
(71, 2, 'Rejected donation with ID: 57', '', 'admin', '2025-09-01 15:55:50'),
(72, 2, 'Undid approval for donation with ID: 57', '', 'admin', '2025-09-01 15:56:01'),
(73, 2, 'Rejected donation with ID: 57', '', 'admin', '2025-09-01 15:56:05'),
(74, 2, 'Undid approval for donation with ID: 57', '', 'admin', '2025-09-01 16:01:36'),
(75, 2, 'Rejected donation with ID: 57', '', 'admin', '2025-09-01 16:01:49'),
(76, 2, 'Undid approval for donation with ID: 57', '', 'admin', '2025-09-01 16:07:56'),
(77, 2, 'Undid approval for donation with ID: 56', '', 'admin', '2025-09-01 16:08:07');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `delivery_mode` enum('Drop-off','Pickup','Courier') DEFAULT NULL,
  `status` enum('pending','approved','rejected','delivered','collected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donor_id`, `organization_id`, `caption`, `delivery_mode`, `status`, `created_at`, `updated_at`) VALUES
(30, 5, 3, 'create', 'Drop-off', 'collected', '2025-08-27 08:44:13', '2025-08-28 15:15:52'),
(31, 5, 10, 'DONATE #2', 'Drop-off', 'approved', '2025-08-27 11:15:38', '2025-08-27 11:15:49'),
(32, 1, 3, 'DONATE3', 'Drop-off', 'rejected', '2025-08-27 11:18:10', '2025-08-27 11:20:14'),
(33, 1, 10, 'donate4', 'Drop-off', 'rejected', '2025-08-27 11:18:24', '2025-08-29 15:22:47'),
(34, 1, 3, 'Donate2', 'Drop-off', 'rejected', '2025-08-27 11:23:42', '2025-08-27 11:36:43'),
(35, 1, 10, 'dnawdawd', 'Drop-off', 'rejected', '2025-08-27 11:23:51', '2025-08-27 11:36:42'),
(36, 1, 3, 'awdawd', 'Drop-off', 'rejected', '2025-08-27 11:24:02', '2025-08-27 11:36:41'),
(37, 1, 10, 'DONATE21', 'Drop-off', 'rejected', '2025-08-27 11:28:28', '2025-08-27 11:36:37'),
(38, 1, 3, 'awdcawdcawd', 'Drop-off', 'rejected', '2025-08-27 11:39:47', '2025-08-27 11:44:32'),
(39, 1, 10, '123c1', 'Drop-off', 'rejected', '2025-08-27 11:39:58', '2025-08-27 11:44:31'),
(40, 1, 10, 'awdcawdcawd', 'Drop-off', 'rejected', '2025-08-27 12:52:30', '2025-08-27 12:54:56'),
(41, 1, 3, 'awdcawdc', 'Drop-off', 'rejected', '2025-08-27 12:55:26', '2025-08-27 12:57:29'),
(42, 1, 3, '123123123', 'Drop-off', 'rejected', '2025-08-27 12:57:52', '2025-08-27 13:06:55'),
(43, 1, 3, 'awdcawdcawdcawdc', 'Drop-off', 'rejected', '2025-08-27 13:05:01', '2025-08-27 13:06:54'),
(44, 1, 3, 'aczsdawdawdc', 'Drop-off', 'rejected', '2025-08-27 13:19:52', '2025-08-27 14:20:55'),
(45, 1, 3, 'awdcawdcawdca', 'Drop-off', 'pending', '2025-08-27 13:20:09', '2025-08-29 02:38:03'),
(46, 1, 3, '123123', 'Drop-off', 'pending', '2025-08-27 14:20:30', '2025-08-28 17:03:46'),
(47, 1, 3, 'aa112121', 'Drop-off', 'collected', '2025-08-27 14:34:30', '2025-08-28 15:15:51'),
(48, 1, 3, 'asdsdg fsdfv', 'Drop-off', 'pending', '2025-08-27 14:55:13', '2025-08-29 02:37:44'),
(49, 1, 10, 'awdcawdcawdawcdaw2222', 'Drop-off', 'pending', '2025-08-27 15:24:29', '2025-08-29 02:36:49'),
(50, 1, 3, '1d2c3123c12c3', 'Drop-off', 'pending', '2025-08-27 15:24:52', '2025-08-29 02:33:35'),
(51, 1, 10, 'acwdcawdcawcdawcd2222', 'Drop-off', 'pending', '2025-08-27 15:49:37', '2025-08-28 17:03:44'),
(52, 1, 3, 'c12ec12c12', 'Pickup', 'collected', '2025-08-27 15:49:55', '2025-09-01 11:57:19'),
(53, 1, 3, 'DONATE#9', 'Drop-off', 'collected', '2025-08-29 13:08:18', '2025-08-29 13:10:04'),
(54, 1, 3, '21cc12ce12e', 'Drop-off', 'rejected', '2025-08-29 16:16:16', '2025-09-01 15:52:04'),
(55, 1, 3, 'DESCRIPTIONEEE', 'Drop-off', 'rejected', '2025-08-30 11:28:14', '2025-09-01 15:50:26'),
(56, 1, 10, 'jutsssssssssss', 'Drop-off', 'approved', '2025-08-30 11:33:27', '2025-09-01 16:08:13'),
(57, 1, 3, 'awdcawdcawdc', 'Drop-off', 'approved', '2025-08-30 13:02:54', '2025-09-01 16:07:59');

-- --------------------------------------------------------

--
-- Table structure for table `donation_conditions`
--

CREATE TABLE `donation_conditions` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) DEFAULT NULL,
  `within_expiration_date` varchar(100) DEFAULT NULL,
  `properly_stored` varchar(100) DEFAULT NULL,
  `no_damaged_packaging` varchar(100) DEFAULT NULL,
  `fresh_not_rotten` varchar(100) DEFAULT NULL,
  `no_contamination` varchar(100) DEFAULT NULL,
  `packaging_intact` varchar(100) DEFAULT NULL,
  `food_safely_prepared` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_conditions`
--

INSERT INTO `donation_conditions` (`id`, `donation_id`, `within_expiration_date`, `properly_stored`, `no_damaged_packaging`, `fresh_not_rotten`, `no_contamination`, `packaging_intact`, `food_safely_prepared`) VALUES
(24, 30, '1', '0', '1', '0', '1', '0', '0'),
(25, 31, '0', '1', '1', '1', '1', '1', '1'),
(26, 32, '0', '0', '0', '1', '1', '1', '0'),
(27, 33, '0', '1', '1', '1', '1', '0', '0'),
(28, 34, '0', '0', '1', '1', '0', '0', '0'),
(29, 35, '0', '1', '0', '1', '0', '0', '0'),
(30, 36, '0', '0', '0', '0', '0', '1', '1'),
(31, 37, '1', '0', '0', '0', '0', '0', '0'),
(32, 38, '0', '0', '0', '0', '0', '0', '0'),
(33, 39, '0', '0', '0', '0', '0', '0', '0'),
(34, 40, '1', '1', '1', '1', '1', '1', '1'),
(35, 41, '1', '1', '1', '1', '1', '1', '1'),
(36, 42, '0', '0', '0', '0', '0', '0', '0'),
(37, 43, '0', '0', '0', '0', '0', '0', '0'),
(38, 44, '0', '0', '0', '0', '0', '0', '0'),
(39, 45, '0', '0', '0', '0', '0', '0', '0'),
(40, 46, '1', '0', '0', '0', '0', '0', '0'),
(41, 47, '1', '1', '1', '0', '0', '0', '0'),
(42, 48, '1', '1', '1', '0', '0', '0', '0'),
(43, 49, '1', '1', '1', '0', '0', '1', '1'),
(44, 50, '0', '0', '0', '0', '1', '1', '1'),
(45, 51, '1', '0', '1', '0', '0', '1', '1'),
(46, 52, '1', '1', '0', '0', '0', '0', '0'),
(47, 53, '1', '1', '1', '0', '0', '0', '0'),
(48, 54, '1', '0', '0', '0', '1', '0', '0'),
(49, 55, '1', '1', '1', '1', '1', '1', '1'),
(50, 56, '1', '0', '1', '1', '1', '1', '1'),
(51, 57, '1', '1', '1', '0', '0', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `donation_images`
--

CREATE TABLE `donation_images` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_images`
--

INSERT INTO `donation_images` (`id`, `donation_id`, `image_path`, `uploaded_at`) VALUES
(7, 30, '1756284253_68aec55d16eef.jpg', '2025-08-27 08:44:13'),
(8, 31, '1756293338_68aee8da1f7c0.jpg', '2025-08-27 11:15:38'),
(9, 32, '1756293490_68aee972c0a9c.jpg', '2025-08-27 11:18:10'),
(10, 33, '1756293504_68aee98024227.jpg', '2025-08-27 11:18:24'),
(11, 37, '1756294108_68aeebdc94cd7.jpg', '2025-08-27 11:28:28'),
(12, 38, '1756294787_68aeee8332cb7.jpg', '2025-08-27 11:39:47'),
(13, 41, '1756299326_68af003e81faf.jpg', '2025-08-27 12:55:26'),
(14, 42, '1756299472_68af00d0116ac.jpg', '2025-08-27 12:57:52'),
(15, 43, '1756299901_68af027da4eca.jpg', '2025-08-27 13:05:01'),
(16, 45, 'uploads/donations/img_68af0609c21278.73673657.jpg', '2025-08-27 13:20:09'),
(17, 47, 'uploads/donations/img_68af17760acba9.23005197.png', '2025-08-27 14:34:30'),
(18, 49, 'uploads/donations/img_68af232d0c0846.96551077.jpg', '2025-08-27 15:24:29'),
(19, 50, 'uploads/donations/img_68af2344c14fb6.70630453.jpg', '2025-08-27 15:24:52'),
(20, 51, 'uploads/donations/img_68af2911843ed0.55534485.jpg', '2025-08-27 15:49:37'),
(21, 52, 'uploads/donations/img_68af292326f612.35360973.jpg', '2025-08-27 15:49:55'),
(22, 53, 'uploads/donations/img_68b1a642631537.48373599.jpg', '2025-08-29 13:08:18'),
(23, 54, 'uploads/donations/img_68b1d2507a8cc8.68983050.jpg', '2025-08-29 16:16:16'),
(24, 54, 'uploads/donations/img_68b1d2507ac466.73726200.jpg', '2025-08-29 16:16:16'),
(25, 55, 'uploads/donations/img_68b2e04e8c71a4.88762998.png', '2025-08-30 11:28:14'),
(26, 55, 'uploads/donations/img_68b2e04e8f2064.44703603.jpg', '2025-08-30 11:28:14'),
(27, 55, 'uploads/donations/img_68b2e04e8f8553.59428522.png', '2025-08-30 11:28:14'),
(28, 55, 'uploads/donations/img_68b2e04e8fda12.58480922.png', '2025-08-30 11:28:14'),
(29, 56, 'uploads/donations/img_68b2e187f0dbc7.37690357.png', '2025-08-30 11:33:27'),
(30, 56, 'uploads/donations/img_68b2e187f0fe96.78416716.jpg', '2025-08-30 11:33:27'),
(31, 57, 'uploads/donations/img_68b2f67e3bc652.30459410.png', '2025-08-30 13:02:54'),
(32, 57, 'uploads/donations/img_68b2f67e3cdb12.91708010.jpg', '2025-08-30 13:02:54'),
(33, 57, 'uploads/donations/img_68b2f67e3cf314.67571179.png', '2025-08-30 13:02:54');

-- --------------------------------------------------------

--
-- Table structure for table `donation_items`
--

CREATE TABLE `donation_items` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `item_type` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `expiration_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_items`
--

INSERT INTO `donation_items` (`id`, `donation_id`, `item_type`, `quantity`, `expiration_date`) VALUES
(47, 30, 'Fresh Produce', 22, '0000-00-00'),
(48, 31, 'Bottled Water', 30, '0000-00-00'),
(49, 32, 'Bottled Water', 22, '0000-00-00'),
(50, 33, 'Packaged Goods', 22, '0000-00-00'),
(51, 34, 'Fresh Produce', 5, '0000-00-00'),
(52, 35, 'Cooked Meats', 22, '0000-00-00'),
(53, 36, 'Fresh Produce', 5, '0000-00-00'),
(54, 36, 'Packaged Goods', 5, '0000-00-00'),
(55, 37, 'Fresh Produce', 5, '2025-08-29'),
(56, 38, 'Fresh Produce', 5, '2025-09-06'),
(57, 39, 'Cooked Meats', 5, '2025-07-31'),
(58, 40, 'Fresh Produce', 12, '0000-00-00'),
(59, 41, 'Cooked Meats', 5, '2025-09-03'),
(60, 42, 'Fresh Produce', 5, '2025-09-06'),
(61, 43, 'Fresh Produce', 5, '2025-08-22'),
(62, 46, 'Fresh Produce', 5, '2025-08-21'),
(63, 46, 'Packaged Goods', 5, '2025-08-21'),
(64, 47, 'Fresh Produce', 5, '2028-10-17'),
(65, 48, 'Cooked Meats', 5, '2025-08-20'),
(66, 49, 'Fresh Produce', 5, '2025-08-29'),
(67, 49, 'Packaged Goods', 5, '2025-08-21'),
(68, 49, 'Cooked Meats', 5, '2025-08-09'),
(69, 50, 'Fresh Produce', 5, '2025-08-08'),
(70, 50, 'Packaged Goods', 5, '2025-08-30'),
(71, 50, 'Bottled Water', 5, '2025-09-03'),
(72, 51, 'Fresh Produce', 5, '2030-10-17'),
(73, 52, 'Cooked Meats', 5, '2025-08-30'),
(74, 53, 'Fresh Produce', 5, '2025-09-05'),
(75, 54, 'Fresh Produce', 5, '0000-00-00'),
(76, 55, 'Fresh Produce', 5, '2025-08-30'),
(77, 55, 'Packaged Goods', 5, '2025-08-30'),
(78, 55, 'Cooked Meats', 5, '0000-00-00'),
(79, 55, 'Bottled Water', 5, '0000-00-00'),
(80, 55, 'Cooked Rice', 5, '0000-00-00'),
(81, 56, 'Fresh Produce', 5, '2025-08-30'),
(82, 57, 'Fresh Produce', 5, '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `donation_logs`
--

CREATE TABLE `donation_logs` (
  `id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message_content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `recipient_id`, `message_content`, `is_read`, `created_at`) VALUES
(1, 1, 10, 'Hello', 0, '2025-08-30 18:01:19'),
(2, 1, 3, 'hiya', 1, '2025-08-30 18:01:33'),
(3, 1, 3, 'awdcawdc]', 1, '2025-08-31 12:52:39'),
(4, 1, 3, 'awdc', 1, '2025-08-31 12:52:40'),
(5, 1, 3, 'awdc', 1, '2025-08-31 12:52:40'),
(6, 1, 3, 'aw', 1, '2025-08-31 12:52:40'),
(7, 1, 3, 'dc', 1, '2025-08-31 12:52:40'),
(8, 1, 3, 'awcd', 1, '2025-08-31 12:52:40'),
(9, 1, 3, 'try', 1, '2025-08-31 12:57:34'),
(10, 1, 3, 'awdc', 1, '2025-08-31 13:21:54'),
(11, 3, 1, 'hi', 1, '2025-08-31 16:06:56'),
(12, 1, 3, 'yow', 1, '2025-08-31 16:07:25'),
(13, 3, 1, 'try', 1, '2025-08-31 16:14:08'),
(14, 1, 3, 'aw', 1, '2025-08-31 17:04:55'),
(15, 3, 1, 'haha1', 1, '2025-08-31 17:05:45'),
(16, 3, 1, 'ano san ka', 1, '2025-08-31 17:05:55'),
(17, 1, 3, 'nandito lang', 1, '2025-08-31 17:06:02'),
(18, 1, 3, 'san', 1, '2025-08-31 17:06:19'),
(19, 3, 1, 'jaan', 1, '2025-08-31 17:06:30'),
(20, 3, 1, 'aw', 1, '2025-08-31 17:34:53'),
(21, 3, 1, 'awwww', 1, '2025-08-31 17:38:33'),
(22, 1, 3, 'htrt', 1, '2025-09-01 15:37:03'),
(23, 1, 3, 'aw', 1, '2025-09-02 15:58:54'),
(24, 3, 1, 'hiya', 0, '2025-09-02 16:09:54');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `donor_id`, `donation_id`, `message`, `is_read`, `created_at`) VALUES
(3, 1, 9999, 'Test notification: Your donation has been processed.', 0, '2025-07-12 07:24:06'),
(4, 1, 23, 'Your donation ID #23 has been collected.', 0, '2025-07-12 07:51:15'),
(5, 5, 24, 'Your donation ID #24 has been collected.', 0, '2025-08-17 10:45:13'),
(6, 5, 25, 'Your donation ID #25 has been collected.', 0, '2025-08-17 10:57:44'),
(7, 1, 50, 'Your donation ID #50 has been collected.', 0, '2025-08-28 15:15:49'),
(8, 1, 48, 'Your donation ID #48 has been collected.', 0, '2025-08-28 15:15:50'),
(9, 1, 47, 'Your donation ID #47 has been collected.', 0, '2025-08-28 15:15:51'),
(10, 5, 30, 'Your donation ID #30 has been collected.', 0, '2025-08-28 15:15:52'),
(11, 1, 53, 'Your donation ID #53 has been collected.', 0, '2025-08-29 13:10:04');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`) VALUES
(13, 1, 'f711caa1b890cc2cf30e740ce10593670bc8cc200b8d684c87c6206a7b6c96f48bbb1bae040eb8bdb9027ef1fb190ec0ce72', '2025-09-02 20:59:08'),
(14, 1, '63b47a88d25dc67a177c6256d620073856af91f22738a5a7abe9d25dd086a642b6bf230cd0d237fdf569b7d060ab5cd20469', '2025-09-02 21:04:24'),
(16, 1, 'd0043df6b8bf65cae8721aef4a9777f466781ea53a48a1b6f14ec1f2b3372583ea38ed460c5772201ac2e587d83cb342ff56', '2025-09-02 21:20:53');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('donor','organization') NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `role`, `description`, `location`, `featured_image`, `status`, `created_at`) VALUES
(1, 1, 'donor', 'Hi i would like to request a food donation for our community. 25 Rice', 'Las Pinas', '1752398403_2.png', 'approved', '2025-07-13 09:20:03'),
(2, 1, 'donor', 'Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor', 'Las Pinas', '1752421391_viber_image_2025-01-31_12-14-51-005.jpg', 'approved', '2025-07-13 15:43:11'),
(3, 1, 'donor', 'Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor', 'public_html', '1752421401_1.png', 'approved', '2025-07-13 15:43:21'),
(4, 1, 'donor', 'Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor', 'public_html', '1752421413_tierra4.png', 'approved', '2025-07-13 15:43:33'),
(5, 1, 'donor', 'Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor Lorem Ipsum Dolor', 'Las Pinas', '1752421420_fuji3.png', 'approved', '2025-07-13 15:43:40'),
(6, 1, 'donor', 'GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!GRAPE!!', 'Las Pinas', '1752426256_tierra9.png', 'approved', '2025-07-13 17:04:16'),
(7, 5, 'donor', 'Hi Grape!', 'Las Pinas CA', '', 'approved', '2025-08-17 10:42:44'),
(8, 5, 'donor', 'GRAPEEEEEEEEEEEEEEEEE1', '123123123123asdasdasdasdasd', '', 'rejected', '2025-08-17 10:54:41'),
(9, 1, 'donor', 'HELLO WORLD123', 'Las Pinas', '1756275492_20250827_1032_Smiling Through Hunger_simple_compose_01k3mntf6jf9grf1m29htmrx2t.png', 'rejected', '2025-08-27 06:18:12'),
(10, 1, 'donor', 'TYTT', 'Las Pinas', '', 'pending', '2025-08-27 11:25:42'),
(11, 3, 'donor', 'aawdcawdcawd', 'awd', '', 'pending', '2025-08-31 14:50:03');

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_comments`
--

INSERT INTO `post_comments` (`id`, `post_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 1, 'GREAT!!!', '2025-07-13 10:17:14'),
(2, 6, 1, 'GREAT!', '2025-07-13 17:06:07'),
(3, 6, 4, 'WHAT A GREAT DAY TO KISS DIANNE!', '2025-07-13 17:08:44'),
(4, 6, 3, 'MAFFI IS A DRAGON!', '2025-07-13 17:09:24'),
(5, 6, 5, 'Nice!', '2025-08-17 10:42:15'),
(6, 7, 3, 'Hello World!', '2025-08-17 10:45:27'),
(7, 6, 5, '123123!!!', '2025-08-17 10:55:52'),
(8, 8, 3, 'grape!', '2025-08-17 10:57:27'),
(9, 6, 1, 'YEYE BONEL!', '2025-08-28 15:24:05'),
(10, 8, 1, 'YEYE BONEL YEYE BONEL!', '2025-08-28 15:24:25'),
(11, 7, 1, 'Hiyaaaa!', '2025-08-30 16:55:15'),
(12, 5, 1, 'Hiyaaaa!', '2025-08-30 16:59:16'),
(13, 6, 1, 'Hello nanjan kayo?', '2025-08-30 17:02:12'),
(14, 7, 3, 'awd', '2025-08-31 15:29:21');

-- --------------------------------------------------------

--
-- Table structure for table `post_reactions`
--

CREATE TABLE `post_reactions` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction_type` varchar(50) NOT NULL,
  `reaction` enum('like','love','care','wow') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_reactions`
--

INSERT INTO `post_reactions` (`id`, `post_id`, `user_id`, `reaction_type`, `reaction`, `created_at`) VALUES
(9, 1, 1, 'love', 'like', '2025-07-13 10:34:44'),
(10, 6, 1, 'like', 'like', '2025-07-13 17:06:00'),
(11, 6, 4, 'like', 'like', '2025-07-13 17:08:33'),
(12, 6, 3, 'love', 'like', '2025-07-13 17:09:13'),
(13, 6, 5, 'love', 'like', '2025-08-17 10:42:10'),
(14, 7, 3, 'love', 'like', '2025-08-17 10:45:23'),
(15, 8, 3, 'like', 'like', '2025-08-17 10:57:22'),
(16, 7, 1, 'like', 'like', '2025-08-30 16:55:17'),
(17, 5, 1, 'like', 'like', '2025-08-30 16:59:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `org_id` varchar(50) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','donor','organization') NOT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `valid_id` varchar(255) DEFAULT NULL,
  `selfie` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `org_id`, `address`, `contact`, `email`, `password`, `role`, `status`, `created_at`, `valid_id`, `selfie`, `profile_picture`, `birthday`) VALUES
(1, 'EJ ARCIAGA', NULL, 'Langkaan 1, Dasmarinas Cavite', '012301203', 'ejj.arciaga@gmail.com', '$2y$10$Q9yrKJ5mD3JAkbSYVqy0xOg1.QGkhVI2SZsN/kzsN.ZoDv4iYHC02', 'donor', 'approved', '2025-07-10 15:14:46', NULL, NULL, '../uploads/profile_pictures/1_1756571072.jpg', NULL),
(2, 'Admin User', NULL, 'awdc', '', 'admin@food.com', '$2y$10$cW94MZTp30J/2Gt5OmRE2OGgBV1h2GEuZaIN9a.K4WN0z3TVjXHnu', 'admin', 'approved', '2025-07-10 15:16:05', NULL, NULL, '', NULL),
(3, 'Yanyan Community', '@yanyan', 'kilalaspinas', '010120312312', 'maffi@yahoo.com', '$2y$10$IkoBI.P5A75N.EAeC3DqHe3Ibcnw4mp/P7djAKdGsH5dwGaug.uci', 'organization', 'approved', '2025-07-10 15:26:43', NULL, NULL, '../uploads/profile_pictures/3_1756650359.png', '0000-00-00'),
(4, 'KUYA TUTOY', NULL, 'Mindoro', '1234568969', 'tutoy@gmail.com', '$2y$10$qNAef.9FIdMw86M5LICvKeRF2ZJkbhS6Wth1Y8rnU3/xBWHZraOka', 'donor', 'approved', '2025-07-13 17:07:56', NULL, NULL, '', NULL),
(5, 'Juan Dela Cruz', NULL, 'Las pinas CA', '1023012032', 'juandelacruz@yahoo.com', '$2y$10$tqPb179t0kCRDZcMWo6Se.Qx4NxuTzzJClVXMwPHx2K.7NQMeuru6', 'donor', 'approved', '2025-08-17 10:40:49', NULL, NULL, '', NULL),
(6, 'travolta', NULL, '1231231111', '123233333', '123213@yahoo.com', '$2y$10$bcV7mG0VADQ.4FgRh0p1eu5sGSXixrhl9XmbkadePGIcmJqbMtOGq', 'donor', '', '2025-08-19 07:23:54', NULL, NULL, '', NULL),
(8, 'Yanyan2 Community', NULL, '', '0123012033', 'yabyab@yahoo.com', '$2y$10$gmyy.puIRY7xarBiTXsuHeDu55S2czfvB6ecB0U55gGjTE8ixC2M2', '', '', '2025-08-19 09:03:17', '1755594197_EJJ.PNG', '1755594197_EJJ.PNG', '', '1995-01-26'),
(9, 'travolta2', NULL, 'JAN KILA MAFFI', '123123123123', 'travolta@yahoo.com', '$2y$10$QlK9k6woINQkpFMr77D8G.FOv2P/TZm3xUPgP994gczslHoE9x.Qm', 'donor', 'approved', '2025-08-19 09:13:55', '1755594835_EJJ.PNG', '1755594835_EJJ.PNG', '', '2005-01-22'),
(10, 'EDWARD JOHN ARCIAGA', NULL, 'Langkaan 1, Dasmarinas Cavite', '0123012032', 'org26@gmail.com', '$2y$10$Uifeh8zp3hVhKUG6K26cge00nXCbwqNghlDYBKsznk9ZqmWkx13R.', 'organization', 'approved', '2025-08-26 15:10:15', '1756221015_EJJ.PNG', '1756221015_EJJ.PNG', '', '1955-11-22'),
(11, 'MAMA MIA', NULL, 'HELLO WORLD', '12312312333', 'mamamia@gmail.com', '$2y$10$oknvzvOcP.VE5KjfvksBK.5H5DYmq22qjDY3eo5TCpQVKFJ9VrZx.', 'donor', '', '2025-08-27 06:16:03', '1756275363_anime-berserk-guts-kentaro-miura-wallpaper-preview.jpg', '1756275363_anime-berserk-guts-kentaro-miura-wallpaper-preview.jpg', '', '2011-11-11'),
(13, 'red', NULL, '123123123123', '123123123123123', 'red@yahoo.com', '$2y$10$OmyKesL.TrjaJLndh9Ew5ur.fbcJoOVvxxyDxsLCJgCzLD5OwSibO', 'donor', 'pending', '2025-08-28 15:43:10', '1756395790_20250827_1032_Smiling Through Hunger_simple_compose_01k3mntf6jf9grf1m29htmrx2t.png', '1756395790_20250827_1032_Smiling Through Hunger_simple_compose_01k3mntf6kekqsg3pkj4gqh3ry.png', '', '2025-08-08'),
(14, 'DEVELOPER 2', NULL, '', '', 'admin2@food.com', '$2y$10$INUo8cpwJFYMxMFRaGLBGemfqmFop9b9yexsOCE9A8G4rM6ME2zFC', 'admin', 'approved', '2025-08-29 11:54:36', NULL, NULL, NULL, NULL),
(15, 'Maffi', NULL, '', '', 'maffi2@yahoo.com', '$2y$10$iPz1gpXSM.KNyEASVsy4wuC5jMXYcTiaA3IAUuRjqUtos/DqbaRQe', 'admin', 'denied', '2025-08-29 13:11:41', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`),
  ADD KEY `organization_id` (`organization_id`),
  ADD KEY `idx_donation_status` (`status`);

--
-- Indexes for table `donation_conditions`
--
ALTER TABLE `donation_conditions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `donation_images`
--
ALTER TABLE `donation_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `donation_items`
--
ALTER TABLE `donation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `donation_logs`
--
ALTER TABLE `donation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`),
  ADD KEY `donation_id` (`donation_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_reactions`
--
ALTER TABLE `post_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reaction` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `donation_conditions`
--
ALTER TABLE `donation_conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `donation_images`
--
ALTER TABLE `donation_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `donation_items`
--
ALTER TABLE `donation_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `donation_logs`
--
ALTER TABLE `donation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `post_reactions`
--
ALTER TABLE `post_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD CONSTRAINT `audit_trail_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `donations_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `donation_conditions`
--
ALTER TABLE `donation_conditions`
  ADD CONSTRAINT `donation_conditions_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`),
  ADD CONSTRAINT `donation_conditions_ibfk_2` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`);

--
-- Constraints for table `donation_images`
--
ALTER TABLE `donation_images`
  ADD CONSTRAINT `donation_images_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`),
  ADD CONSTRAINT `donation_images_ibfk_2` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`);

--
-- Constraints for table `donation_items`
--
ALTER TABLE `donation_items`
  ADD CONSTRAINT `donation_items_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`);

--
-- Constraints for table `donation_logs`
--
ALTER TABLE `donation_logs`
  ADD CONSTRAINT `donation_logs_ibfk_1` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `donation_logs_ibfk_2` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD CONSTRAINT `post_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_reactions`
--
ALTER TABLE `post_reactions`
  ADD CONSTRAINT `post_reactions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
