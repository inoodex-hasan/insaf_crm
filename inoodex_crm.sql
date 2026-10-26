-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 23, 2026 at 11:53 PM
-- Server version: 11.4.9-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ibravphe_crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounting_periods`
--

CREATE TABLE `accounting_periods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `month` tinyint(3) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `type` enum('fiscal_year','monthly','quarterly') NOT NULL DEFAULT 'monthly',
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `remarks` text DEFAULT NULL,
  `is_closed` tinyint(1) DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounting_periods`
--

INSERT INTO `accounting_periods` (`id`, `name`, `year`, `month`, `start_date`, `end_date`, `type`, `status`, `remarks`, `is_closed`, `closed_at`, `closed_by`, `created_at`, `updated_at`) VALUES
(1, 'FY-2026', NULL, NULL, '2026-01-01', '2026-12-31', 'fiscal_year', 'open', NULL, NULL, NULL, NULL, '2026-04-12 04:36:10', '2026-04-12 21:26:37');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` varchar(255) NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `university_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_intake_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tuition_fee` decimal(12,2) DEFAULT NULL,
  `tuition_fee_status` enum('pending','paid','partial') NOT NULL DEFAULT 'pending',
  `service_charge_status` enum('pending','paid','partial') NOT NULL DEFAULT 'pending',
  `application_priority` enum('normal','priority','vip') NOT NULL DEFAULT 'normal',
  `internal_notes` text DEFAULT NULL,
  `documents_checklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `final_status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `security_deposit_status` tinyint(1) NOT NULL DEFAULT 0,
  `cvu_fee_status` tinyint(1) NOT NULL DEFAULT 0,
  `admission_fee_status` tinyint(1) NOT NULL DEFAULT 0,
  `final_payment_status` tinyint(1) NOT NULL DEFAULT 0,
  `emgs_payment_status` tinyint(1) NOT NULL DEFAULT 0,
  `emgs_score` int(11) DEFAULT NULL,
  `total_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `offer_letter_received` tinyint(1) NOT NULL DEFAULT 0,
  `offer_letter_received_date` date DEFAULT NULL,
  `vfs_appointment` tinyint(1) NOT NULL DEFAULT 0,
  `vfs_appointment_date` date DEFAULT NULL,
  `vfs_result` varchar(255) NOT NULL DEFAULT 'pending',
  `vfs_note` text DEFAULT NULL,
  `file_submission` tinyint(1) NOT NULL DEFAULT 0,
  `file_submission_date` date DEFAULT NULL,
  `visa_status` enum('not_applied','pending','approved','rejected') NOT NULL DEFAULT 'not_applied',
  `visa_decision_date` date DEFAULT NULL,
  `visa_approval_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `application_id`, `student_id`, `university_id`, `course_id`, `course_intake_id`, `tuition_fee`, `tuition_fee_status`, `service_charge_status`, `application_priority`, `internal_notes`, `documents_checklist`, `final_status`, `security_deposit_status`, `cvu_fee_status`, `admission_fee_status`, `final_payment_status`, `emgs_payment_status`, `emgs_score`, `total_fee`, `status`, `offer_letter_received`, `offer_letter_received_date`, `vfs_appointment`, `vfs_appointment_date`, `vfs_result`, `vfs_note`, `file_submission`, `file_submission_date`, `visa_status`, `visa_decision_date`, `visa_approval_date`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(21, 'APP-2026-00001', 12, 3, 4, 6, NULL, 'pending', 'pending', 'normal', NULL, NULL, 'pending', 0, 0, 0, 0, 0, NULL, 0.00, 'pending', 0, NULL, 0, NULL, 'pending', NULL, 0, NULL, 'not_applied', NULL, NULL, NULL, 1, '2026-10-21 05:09:59', '2026-10-21 05:09:59');

-- --------------------------------------------------------

--
-- Table structure for table `bank_reconciliations`
--

CREATE TABLE `bank_reconciliations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `statement_date` date NOT NULL,
  `statement_balance` decimal(15,2) NOT NULL,
  `system_balance` decimal(15,2) NOT NULL,
  `difference` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','closed') NOT NULL DEFAULT 'draft',
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_reconciliation_items`
--

CREATE TABLE `bank_reconciliation_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reconciliation_id` bigint(20) UNSIGNED NOT NULL,
  `bank_statement_ref` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('matched','unmatched','adjustment') NOT NULL DEFAULT 'unmatched',
  `matched_at` timestamp NULL DEFAULT NULL,
  `matched_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `journal_entry_item_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chart_of_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `period` enum('monthly','yearly') NOT NULL DEFAULT 'monthly',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
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

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('admin-dashboard-cache-active_currencies', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:19:\"App\\Models\\Currency\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"currencies\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:4:\"Euro\";s:4:\"code\";s:3:\"EUR\";s:6:\"symbol\";s:3:\"€\";s:13:\"exchange_rate\";s:12:\"147.04000000\";s:9:\"is_active\";s:1:\"1\";s:10:\"is_default\";s:1:\"0\";s:10:\"created_at\";s:19:\"2026-05-06 20:10:59\";s:10:\"updated_at\";s:19:\"2026-05-13 07:04:56\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";s:1:\"2\";s:4:\"name\";s:4:\"Euro\";s:4:\"code\";s:3:\"EUR\";s:6:\"symbol\";s:3:\"€\";s:13:\"exchange_rate\";s:12:\"147.04000000\";s:9:\"is_active\";s:1:\"1\";s:10:\"is_default\";s:1:\"0\";s:10:\"created_at\";s:19:\"2026-05-06 20:10:59\";s:10:\"updated_at\";s:19:\"2026-05-13 07:04:56\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:13:\"exchange_rate\";s:9:\"decimal:2\";s:9:\"is_active\";s:7:\"boolean\";s:10:\"is_default\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:4:\"code\";i:2;s:6:\"symbol\";i:3;s:13:\"exchange_rate\";i:4;s:9:\"is_active\";i:5;s:10:\"is_default\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1783274414),
('admin-dashboard-cache-tyro:user-1:roles', 'a:1:{i:0;s:5:\"admin\";}', 1782971006),
('admin-dashboard-cache-tyro:user-10:privileges', 'a:3:{i:0;s:10:\"*marketing\";i:1;s:12:\"*application\";i:2;s:18:\"*digital_marketing\";}', 1778661386),
('admin-dashboard-cache-tyro:user-10:roles', 'a:3:{i:0;s:9:\"marketing\";i:1;s:11:\"application\";i:2;s:17:\"digital-marketing\";}', 1778661386),
('admin-dashboard-cache-tyro:user-11:privileges', 'a:3:{i:0;s:11:\"*consultant\";i:1;s:8:\"*invoice\";i:2;s:7:\"*editor\";}', 1778659395),
('admin-dashboard-cache-tyro:user-11:roles', 'a:2:{i:0;s:10:\"consultant\";i:1;s:6:\"editor\";}', 1778659395),
('admin-dashboard-cache-tyro:user-12:privileges', 'a:4:{i:0;s:11:\"*consultant\";i:1;s:8:\"*invoice\";i:2;s:7:\"*editor\";i:3;s:12:\"*application\";}', 1778667803),
('admin-dashboard-cache-tyro:user-12:roles', 'a:3:{i:0;s:10:\"consultant\";i:1;s:6:\"editor\";i:2;s:11:\"application\";}', 1778667803),
('admin-dashboard-cache-tyro:user-13:privileges', 'a:1:{i:0;s:10:\"*marketing\";}', 1778659810),
('admin-dashboard-cache-tyro:user-13:roles', 'a:1:{i:0;s:9:\"marketing\";}', 1778659810),
('admin-dashboard-cache-tyro:user-3:privileges', 'a:2:{i:0;s:11:\"*consultant\";i:1;s:8:\"*invoice\";}', 1778748779),
('admin-dashboard-cache-tyro:user-3:roles', 'a:1:{i:0;s:10:\"consultant\";}', 1778748779),
('admin-dashboard-cache-tyro:user-4:privileges', 'a:4:{i:0;s:11:\"*accountant\";i:1;s:8:\"*payment\";i:2;s:10:\"*comission\";i:3;s:8:\"*invoice\";}', 1778656126),
('admin-dashboard-cache-tyro:user-4:roles', 'a:1:{i:0;s:10:\"accountant\";}', 1778656126),
('admin-dashboard-cache-tyro:user-5:privileges', 'a:1:{i:0;s:7:\"*editor\";}', 1778737851),
('admin-dashboard-cache-tyro:user-5:roles', 'a:1:{i:0;s:6:\"editor\";}', 1778737851),
('admin-dashboard-cache-tyro:user-6:privileges', 'a:1:{i:0;s:12:\"*application\";}', 1778748912),
('admin-dashboard-cache-tyro:user-6:roles', 'a:1:{i:0;s:11:\"application\";}', 1778748912),
('admin-dashboard-cache-tyro:user-7:privileges', 'a:1:{i:0;s:18:\"*digital_marketing\";}', 1778744854),
('admin-dashboard-cache-tyro:user-7:roles', 'a:1:{i:0;s:17:\"digital-marketing\";}', 1778744854),
('admin-dashboard-cache-tyro:user-8:privileges', 'a:4:{i:0;s:10:\"*marketing\";i:1;s:11:\"*consultant\";i:2;s:8:\"*invoice\";i:3;s:7:\"*editor\";}', 1778660675),
('admin-dashboard-cache-tyro:user-8:roles', 'a:3:{i:0;s:9:\"marketing\";i:1;s:10:\"consultant\";i:2;s:6:\"editor\";}', 1778660675),
('admin-dashboard-cache-tyro:user-9:roles', 'a:9:{i:0;s:5:\"admin\";i:1;s:9:\"marketing\";i:2;s:10:\"consultant\";i:3;s:10:\"accountant\";i:4;s:6:\"editor\";i:5;s:11:\"application\";i:6;s:4:\"user\";i:7;s:11:\"super-admin\";i:8;s:17:\"digital-marketing\";}', 1778662656),
('inoodex-cache-active_currencies', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:19:\"App\\Models\\Currency\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"currencies\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:2;s:4:\"name\";s:4:\"Euro\";s:4:\"code\";s:3:\"EUR\";s:6:\"symbol\";s:3:\"€\";s:13:\"exchange_rate\";s:12:\"147.04000000\";s:9:\"is_active\";i:1;s:10:\"is_default\";i:0;s:10:\"created_at\";s:19:\"2026-05-07 00:10:59\";s:10:\"updated_at\";s:19:\"2026-05-13 11:04:56\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:2;s:4:\"name\";s:4:\"Euro\";s:4:\"code\";s:3:\"EUR\";s:6:\"symbol\";s:3:\"€\";s:13:\"exchange_rate\";s:12:\"147.04000000\";s:9:\"is_active\";i:1;s:10:\"is_default\";i:0;s:10:\"created_at\";s:19:\"2026-05-07 00:10:59\";s:10:\"updated_at\";s:19:\"2026-05-13 11:04:56\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:3:{s:13:\"exchange_rate\";s:9:\"decimal:2\";s:9:\"is_active\";s:7:\"boolean\";s:10:\"is_default\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:4:\"name\";i:1;s:4:\"code\";i:2;s:6:\"symbol\";i:3;s:13:\"exchange_rate\";i:4;s:9:\"is_active\";i:5;s:10:\"is_default\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1792818254),
('inoodex-cache-tyro_dashboard_heartbeat_1', 'i:1792568757;', 1792569357),
('inoodex-cache-tyro:user-1:roles', 'a:9:{i:0;s:5:\"admin\";i:1;s:9:\"marketing\";i:2;s:10:\"consultant\";i:3;s:10:\"accountant\";i:4;s:6:\"editor\";i:5;s:11:\"application\";i:6;s:4:\"user\";i:7;s:11:\"super-admin\";i:8;s:17:\"digital-marketing\";}', 1792569058),
('inoodex-cache-tyro:user-4:privileges', 'a:4:{i:0;s:11:\"*accountant\";i:1;s:8:\"*payment\";i:2;s:10:\"*comission\";i:3;s:8:\"*invoice\";}', 1792559890),
('inoodex-cache-tyro:user-4:roles', 'a:1:{i:0;s:10:\"accountant\";}', 1792559890);

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
-- Table structure for table `chart_of_accounts`
--

CREATE TABLE `chart_of_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('asset','liability','equity','revenue','expense') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chart_of_accounts`
--

INSERT INTO `chart_of_accounts` (`id`, `parent_id`, `code`, `name`, `type`, `is_active`, `is_default`, `created_at`, `updated_at`) VALUES
(1, NULL, '51001', 'Rent', 'expense', 1, 0, '2026-04-07 23:29:08', '2026-04-07 23:29:08'),
(2, NULL, '51002', 'Marketing', 'expense', 1, 0, '2026-04-07 23:30:52', '2026-04-07 23:30:52'),
(3, NULL, '51003', 'Salaries', 'expense', 1, 0, '2026-04-07 23:31:47', '2026-04-07 23:31:47'),
(4, NULL, '51004', 'Utilities', 'expense', 1, 0, '2026-04-07 23:31:47', '2026-04-07 23:31:47'),
(5, NULL, '51005', 'Office Supplies', 'expense', 1, 0, '2026-04-07 23:31:47', '2026-04-07 23:31:47'),
(6, NULL, '41001', 'Student Fees', 'revenue', 1, 0, '2026-04-07 23:31:47', '2026-04-07 23:31:47'),
(13, NULL, '30001', 'Current Liability', 'liability', 1, 0, '2026-04-25 06:19:08', '2026-04-25 06:19:08'),
(14, NULL, '30002', 'Long Term Liability', 'liability', 1, 0, '2026-04-25 06:19:51', '2026-04-25 06:19:51'),
(20, NULL, '10001', 'Office Cash', 'asset', 1, 0, '2026-05-06 00:23:45', '2026-05-06 00:23:45'),
(21, NULL, '10005', 'Inoodex - Brac Bank', 'asset', 1, 0, '2026-05-06 00:25:59', '2026-10-21 05:21:58'),
(22, NULL, '10006', 'Inoodex Pubali Bank', 'asset', 1, 0, '2026-05-06 00:27:40', '2026-10-21 05:21:37');

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `proposed_amount` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `workflow_status` varchar(255) NOT NULL DEFAULT 'draft',
  `claimed_at` timestamp NULL DEFAULT NULL,
  `claim_notes` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `code`, `currency`, `status`, `created_at`, `updated_at`) VALUES
(4, 'Malta', NULL, NULL, 1, '2026-04-14 23:35:04', '2026-04-14 23:35:04'),
(5, 'Malaysia', NULL, NULL, 1, '2026-04-14 23:48:04', '2026-04-14 23:48:04'),
(6, 'Russia', NULL, NULL, 1, '2026-04-27 03:58:15', '2026-04-27 03:58:15'),
(7, 'Finland', NULL, NULL, 1, '2026-06-18 20:57:21', '2026-06-18 20:57:21'),
(8, 'Australia', NULL, NULL, 1, '2026-06-18 20:57:39', '2026-06-18 20:57:39'),
(9, 'Denmark', NULL, NULL, 1, '2026-06-18 20:57:59', '2026-06-18 20:57:59'),
(10, 'Japan', NULL, NULL, 1, '2026-06-18 20:58:14', '2026-06-18 20:58:14'),
(11, 'South Korea', NULL, NULL, 1, '2026-06-18 20:58:39', '2026-06-18 20:58:39');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `university_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `degree_level` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `tuition_fee` decimal(12,2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `university_id`, `name`, `description`, `degree_level`, `duration`, `tuition_fee`, `status`, `created_at`, `updated_at`) VALUES
(3, 4, 'Law', NULL, 'Masters', '6 Month', NULL, 1, '2026-04-14 23:42:41', '2026-04-15 00:07:46'),
(4, 3, 'Cyber Security', '<p><em><u>This is a tes</u></em>t <strong>description</strong></p>', 'Masters', '6 Month', NULL, 1, '2026-04-15 00:06:14', '2026-05-13 23:46:33'),
(5, 5, 'Physics', NULL, 'Masters', '12', NULL, 1, '2026-04-27 04:01:46', '2026-04-27 04:01:46'),
(6, 7, 'OTHM LEVEL 4 DIPLOMA IN INFORMATION TECHNOLOGY (MQF LEVEL 5)', NULL, NULL, NULL, NULL, 1, '2026-05-13 12:10:11', '2026-05-13 12:10:38');

-- --------------------------------------------------------

--
-- Table structure for table `course_intakes`
--

CREATE TABLE `course_intakes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `intake_name` varchar(255) NOT NULL,
  `application_start_date` date DEFAULT NULL,
  `application_deadline` date DEFAULT NULL,
  `class_start_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_intakes`
--

INSERT INTO `course_intakes` (`id`, `course_id`, `intake_name`, `application_start_date`, `application_deadline`, `class_start_date`, `status`, `created_at`, `updated_at`) VALUES
(3, 3, 'Summer 2026', '2026-05-01', '2026-05-31', NULL, 1, '2026-04-14 23:47:34', '2026-04-14 23:47:34'),
(4, 4, 'Summer 2026', '2026-04-01', '2026-04-30', NULL, 1, '2026-04-15 00:11:26', '2026-04-15 00:11:56'),
(5, 5, 'Summer 2026', '2026-05-01', '2026-05-31', NULL, 1, '2026-04-27 04:02:11', '2026-04-27 04:02:11'),
(6, 4, 'February', NULL, NULL, NULL, 1, '2026-10-21 05:09:00', '2026-10-21 05:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(3) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `exchange_rate` decimal(16,8) NOT NULL DEFAULT 1.00000000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `code`, `symbol`, `exchange_rate`, `is_active`, `is_default`, `created_at`, `updated_at`) VALUES
(2, 'Euro', 'EUR', '€', 147.04000000, 1, 0, '2026-05-07 00:10:59', '2026-05-13 11:04:56');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chart_of_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `payment_method` enum('cash','bank_transfer','mobile_banking','cheque') DEFAULT NULL,
  `office_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `salary_id` bigint(20) UNSIGNED DEFAULT NULL,
  `journal_entry_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `chart_of_account_id`, `description`, `amount`, `expense_date`, `payment_method`, `office_account_id`, `created_by`, `notes`, `created_at`, `updated_at`, `salary_id`, `journal_entry_id`) VALUES
(1, 1, 'Office rent', 35000.00, '2026-10-21', 'cash', 4, 1, NULL, '2026-10-21 05:49:14', '2026-10-21 05:49:14', NULL, NULL),
(2, 5, 'Tonner Purchase', 4500.00, '2026-10-20', 'bank_transfer', 5, 1, NULL, '2026-10-21 05:50:12', '2026-10-21 05:50:12', NULL, NULL),
(3, 3, 'Salary Payment - Md. Abul Hasan Saidy (2026-10)', 60000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 166, NULL),
(4, 3, 'Salary Payment - Mohammad Faisal (2026-10)', 60000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 167, NULL),
(5, 3, 'Salary Payment - Insan Kamal Shafat (2026-10)', 40000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 168, NULL),
(6, 3, 'Salary Payment - Sakib Hasan (2026-10)', 46000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 169, NULL),
(7, 3, 'Salary Payment - Lutfur Kabir Rana (2026-10)', 40000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 170, NULL),
(8, 3, 'Salary Payment - Sharafat Ullah Mohim (2026-10)', 40000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 171, NULL),
(9, 3, 'Salary Payment - Mainul Hasan (2026-10)', 40000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 172, NULL),
(10, 3, 'Salary Payment - Singmay Chowdhury (2026-10)', 30000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 173, NULL),
(11, 3, 'Salary Payment - Arif Hossain Nayan (2026-10)', 17000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 174, NULL),
(12, 3, 'Salary Payment - Abdul Alim Shezan (2026-10)', 32000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 175, NULL),
(13, 3, 'Salary Payment - Moshraful Islam (2026-10)', 30000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 176, NULL),
(14, 3, 'Salary Payment - Harunur Rashid (2026-10)', 15000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 177, NULL),
(15, 3, 'Salary Payment - Abu haider (2026-10)', 13000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 178, NULL),
(16, 3, 'Salary Payment - Anta Tasnim Rafa (2026-10)', 17500.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 179, NULL),
(17, 3, 'Salary Payment - Kawcer Hossen Rakib (2026-10)', 25000.00, '2026-10-10', 'bank_transfer', 6, 1, NULL, '2026-10-21 05:53:58', '2026-10-21 05:53:58', 180, NULL);

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
-- Table structure for table `invitation_links`
--

CREATE TABLE `invitation_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `hash` varchar(32) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invitation_referrals`
--

CREATE TABLE `invitation_referrals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invitation_link_id` bigint(20) UNSIGNED NOT NULL,
  `referred_user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `application_id` bigint(20) UNSIGNED DEFAULT NULL,
  `university_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `status` enum('draft','sent','paid','partially_paid','void') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `student_id`, `application_id`, `university_id`, `invoice_number`, `date`, `due_date`, `total_amount`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(3, 12, 21, 3, 'INV-20261021-1000', '2026-10-21', '2026-10-30', 3000.00, 'draft', 'This payment is non refundable.\r\nThis Payment is Refundable According to University Policy.', '2026-10-21 05:15:28', '2026-10-21 05:15:28');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `chart_of_account_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `chart_of_account_id`, `description`, `quantity`, `unit_price`, `subtotal`, `tax_amount`, `total`, `created_at`, `updated_at`) VALUES
(3, 3, 6, 'Security fee', 1.00, 3000.00, 3000.00, 0.00, 3000.00, '2026-10-21 05:15:28', '2026-10-21 05:15:28');

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
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `period_id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `reference_number` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `status` enum('draft','posted','void') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entry_items`
--

CREATE TABLE `journal_entry_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `journal_entry_id` bigint(20) UNSIGNED NOT NULL,
  `chart_of_account_id` bigint(20) UNSIGNED NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `current_education` varchar(255) DEFAULT NULL,
  `preferred_country` bigint(20) UNSIGNED DEFAULT NULL,
  `preferred_course` bigint(20) UNSIGNED DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `priority` varchar(255) NOT NULL DEFAULT 'low',
  `notes` text DEFAULT NULL,
  `last_contacted_at` timestamp NULL DEFAULT NULL,
  `next_follow_up_at` timestamp NULL DEFAULT NULL,
  `follow_up_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `consultant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `student_name`, `email`, `phone`, `current_education`, `preferred_country`, `preferred_course`, `source`, `status`, `priority`, `notes`, `last_contacted_at`, `next_follow_up_at`, `follow_up_history`, `created_by`, `consultant_id`, `created_at`, `updated_at`) VALUES
(11, 'Demo 1', 'your.email+fakedata68073@gmail.com', '01234567890', 'HSC', 6, 5, 'Messenger', 'pending', 'low', NULL, NULL, '2026-10-23 00:00:00', '[{\"date\":\"2026-10-16\",\"notes\":\"211\"},{\"date\":\"2026-10-23\",\"notes\":null}]', 1, NULL, '2026-08-13 10:04:22', '2026-10-20 05:37:20'),
(12, 'Md Rahat', 'rahat@gmail.com', '01531385988', 'HSC', 5, NULL, 'Messenger', 'pending', 'medium', 'Want to visit office', NULL, '2026-10-22 00:00:00', '[{\"date\":\"2026-10-22\",\"notes\":\"Want to visit office\"}]', 1, NULL, '2026-10-21 04:33:57', '2026-10-21 04:33:57'),
(13, 'Shawon Ahmed', 'shawon@gmail.com', '01621816806', 'BSc', 4, NULL, 'Message', 'pending', 'low', NULL, NULL, '2026-10-30 00:00:00', '[{\"date\":\"2026-10-30\",\"notes\":null}]', 1, NULL, '2026-10-21 04:35:46', '2026-10-21 04:35:46');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_campaigns`
--

CREATE TABLE `marketing_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `boosting_status` enum('on','off') NOT NULL DEFAULT 'off',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketing_campaigns`
--

INSERT INTO `marketing_campaigns` (`id`, `name`, `start_date`, `end_date`, `boosting_status`, `created_by`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Victory Day Discount offer', '2026-11-01', '2026-12-31', 'on', 1, 'Victory Day Discount offer for all country', '2026-10-21 04:27:42', '2026-10-21 04:27:42');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_documents`
--

CREATE TABLE `marketing_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_type` enum('sop','cv','cl') NOT NULL,
  `status` enum('pending','received','not_received','ready','submitted') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_posters`
--

CREATE TABLE `marketing_posters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `poster_name` varchar(255) NOT NULL,
  `status` enum('pending','not_ready','designing','ready','uploaded') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketing_posters`
--

INSERT INTO `marketing_posters` (`id`, `poster_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Event October 26 USA', 'designing', '2026-10-21 04:31:11', '2026-10-21 04:31:11');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_videos`
--

CREATE TABLE `marketing_videos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `video_name` varchar(255) NOT NULL,
  `status` enum('pending','not_edited','editing','ready','uploaded') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketing_videos`
--

INSERT INTO `marketing_videos` (`id`, `video_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Student review Japan', 'pending', '2026-10-21 04:28:29', '2026-10-21 04:28:29'),
(2, 'Student review Korea', 'editing', '2026-10-21 04:30:07', '2026-10-21 04:30:07');

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
(4, '2022_05_17_181447_create_roles_table', 1),
(5, '2022_05_17_181456_create_user_roles_table', 1),
(6, '2025_01_01_000001_create_privileges_table', 1),
(7, '2025_01_01_000002_create_privilege_role_table', 1),
(8, '2025_01_01_000003_add_suspension_columns_to_users_table', 1),
(9, '2026_02_02_085518_create_personal_access_tokens_table', 1),
(10, '2026_02_03_073742_create_settings_table', 1),
(11, '2026_02_03_085903_add_is_active_to_roles_table', 1),
(12, '2026_02_03_100000_create_countries_table', 1),
(13, '2026_02_03_100001_create_universities_table', 1),
(14, '2026_02_03_100002_create_courses_table', 1),
(15, '2026_02_03_100003_create_course_intakes_table', 1),
(16, '2026_02_03_111812_create_leads_table', 1),
(17, '2026_02_03_123612_create_students_table', 1),
(18, '2026_02_03_133591_create_payments_table', 1),
(19, '2026_02_15_202417_create_applications_table', 1),
(20, '2026_02_17_131456_add_application_id_to_payments_table', 2),
(21, '2026_02_17_184206_create_notifications_table', 3),
(22, '2026_02_18_063017_create_expenses_table', 4),
(23, '2026_02_18_064510_create_office_accounts_table', 5),
(24, '2026_02_18_065315_create_office_transactions_table', 6),
(25, '2026_02_18_071448_create_budgets_table', 7),
(26, '2026_02_18_083029_create_finance_categories_table', 8),
(27, '2026_02_18_084023_add_total_fee_to_applications_table', 9),
(28, '2026_02_18_094859_add_currency_to_courses_table', 10),
(29, '2026_02_18_095243_create_currencies_table', 11),
(30, '2026_02_18_113730_add_tuition_fee_to_applications_table', 12),
(31, '2026_02_18_114225_add_currency_to_applications_table', 13),
(32, '2026_02_18_115623_add_exchange_rate_to_currencies_table', 14),
(33, '2026_02_19_100000_create_salaries_table', 15),
(34, '2024_01_01_000000_create_social_accounts_table', 16),
(35, '2024_01_01_000001_add_two_factor_columns_to_users_table', 16),
(36, '2024_01_01_000002_create_invitation_system_tables', 16),
(37, '2025_02_08_000000_add_profile_photo_to_users_table', 16),
(38, '2026_02_15_000000_create_tyro_audit_logs_table', 16),
(39, '2026_02_21_085827_create_commissions_table', 16),
(40, '2026_02_22_074425_add_is_template_to_salaries_table', 17),
(41, '2026_02_22_081212_add_office_account_id_to_payments_table', 17),
(42, '2026_02_22_081922_add_office_account_id_to_expenses_table', 18),
(43, '2026_02_22_081930_add_income_expense_to_office_transactions_type', 18),
(44, '2026_02_22_082610_add_basic_salary_to_users_table', 19),
(45, '2026_02_22_090213_add_account_details_to_users_table', 20),
(46, '2026_02_22_094533_add_commission_percentage_to_users_table', 21),
(47, '2026_02_22_102330_add_balance_to_office_accounts_table', 22),
(48, '2026_02_23_045430_add_notes_to_payments_table', 23),
(49, '2026_02_23_100000_add_salary_id_to_expenses', 24),
(50, '2026_02_23_110000_add_opening_balance_to_office_accounts', 25),
(51, '2026_04_07_000001_create_accounting_periods_table', 26),
(52, '2026_04_07_000002_create_chart_of_accounts_table', 26),
(53, '2026_04_07_000003_create_journal_entries_table', 26),
(54, '2026_04_07_000004_create_journal_entry_items_table', 26),
(55, '2026_04_07_000005_create_taxes_table', 26),
(56, '2026_04_07_000006_create_invoices_table', 26),
(57, '2026_04_07_000007_create_invoice_items_table', 26),
(58, '2026_04_07_000008_create_bank_reconciliations_table', 26),
(59, '2026_04_07_000009_create_bank_reconciliation_items_table', 26),
(60, '2026_04_07_000010_add_ledger_links_to_finance_tables', 27),
(61, '2026_04_07_000011_create_marketing_campaigns_table', 28),
(62, '2026_04_07_000012_create_marketing_videos_table', 28),
(63, '2026_04_07_000013_create_marketing_posters_table', 28),
(66, '2026_04_08_000000_add_missing_columns_to_accounting_periods_table', 29),
(68, '2026_04_08_000001_migrate_finance_categories_to_chart_of_accounts', 30),
(69, '2026_04_08_000002_convert_office_transactions_to_journal_entries', 30),
(70, '2026_04_08_000003_remove_currencies_table_and_columns', 31),
(71, '2026_04_08_100000_fix_payments_foreign_key_constraints', 32),
(72, '2026_04_08_100001_fix_accounting_periods_unique_constraint', 33),
(73, '2026_04_08_100002_remove_redundant_commission_settings', 34),
(74, '2026_04_08_100003_fix_leads_preferred_country_and_course_foreign_keys', 35),
(75, '2026_04_08_100004_add_application_id_to_invoices', 36),
(76, '2026_04_08_100005_fix_expenses_payment_method_to_enum', 37),
(78, '2026_04_08_100006_fix_remaining_foreign_key_constraints', 38),
(79, '2026_04_08_100007_add_student_portal_fields_to_students', 38),
(80, '2026_04_09_090000_add_follow_up_history_to_leads_table', 39),
(82, '2026_04_11_000000_add_application_tracking_fields_to_applications_table', 40),
(83, '2026_04_11_045646_add_payment_status_fields_to_applications_table', 41),
(84, '2026_04_11_050000_fix_journal_entries_and_office_accounts', 42),
(85, '2026_04_12_103504_fix_accounting_periods_old_columns', 43),
(86, '2026_04_13_075942_fix_bank_reconciliation_items_table', 44),
(87, '2026_04_13_104949_add_application_id_to_journal_entries', 45),
(88, '2026_04_15_040630_create_vfs_checklists_table', 46),
(89, '2026_04_15_180449_drop_commissions_table', 47),
(90, '2026_04_15_180930_create_commissions_table', 47),
(91, '2026_04_20_035455_add_emgs_score_to_applications_table', 48),
(92, '2026_04_21_000000_create_vfs_checklist_templates_table', 49),
(93, '2026_04_21_000001_add_country_to_vfs_checklist_templates', 50),
(94, '2026_04_25_100000_add_workflow_fields_to_commissions_table', 51),
(95, '2026_04_26_100000_add_dates_to_marketing_campaigns_table', 52),
(96, '2026_04_26_110000_remove_campaign_id_from_marketing_videos', 53),
(97, '2026_04_26_110001_remove_campaign_id_from_marketing_posters', 53),
(98, '2026_04_26_120000_create_marketing_documents_table', 54),
(99, '2026_04_26_130000_create_marketing_documents_table', 55),
(100, '2026_04_27_102940_add_plain_password_to_students_table', 56),
(101, '2026_05_06_042528_make_application_fields_nullable', 57),
(102, '2026_05_06_043851_fix_marketing_videos_status_enum', 58),
(103, '2026_05_06_044555_fix_marketing_posters_status_enum', 59),
(104, '2026_05_06_000000_backfill_chart_of_account_id_for_office_accounts', 60),
(105, '2026_05_06_000001_fix_office_accounts_coa_foreign_key', 61),
(106, '2026_05_06_120000_move_salary_account_fields_from_users_to_salaries', 62),
(107, '2026_05_06_130000_add_username_to_users_for_login', 63),
(108, '2026_05_07_115300_create_currencies_table', 64),
(109, '2026_05_11_045856_add_designation_to_users_table', 65),
(110, '2026_05_14_053132_add_description_to_courses_table', 66),
(111, '2026_05_14_081545_add_vfs_result_to_applications_table', 67),
(112, '2026_05_14_082321_add_vfs_note_to_applications_table', 68),
(113, '2025_01_01_000001_create_media_table', 69),
(114, '2025_01_01_000002_create_starred_import_images_table', 69),
(115, '2026_02_20_000000_create_smtp_presets_table', 69),
(116, '2026_03_01_000000_add_is_favorite_to_tyro_media_table', 69),
(117, '2026_03_26_000000_create_media_categories_tables', 69),
(118, '2026_06_11_103108_add_plain_password_to_users_table', 69),
(119, '2026_08_16_000001_add_priority_to_leads_table', 69),
(120, '2026_10_10_000001_add_package_amount_to_students_table', 69);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('00251a61-00f6-481c-89e4-964d3dce9f31', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 9, '{\"application_id\":13,\"application_number\":\"APP-2026-00001\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Abu Haider\",\"message\":\"New application APP-2026-00001 created by Abu Haider\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/applications\\/13\\/edit\"}', NULL, '2026-05-13 12:16:39', '2026-05-13 12:16:39'),
('002be76b-e03b-4334-8551-7dd852f6f7f0', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 10, '{\"application_id\":14,\"application_number\":\"APP-2026-00002\",\"student_name\":\"TANJU AKTER NILOY NILOY\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00002 created by Inoodex\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/14\\/edit\"}', NULL, '2026-05-14 01:47:01', '2026-05-14 01:47:01'),
('00c5e0e6-7fe5-4514-a671-3425d9e6b0af', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 12, '{\"application_id\":13,\"application_number\":\"APP-2026-00001\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Abu Haider\",\"message\":\"New application APP-2026-00001 created by Abu Haider\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/applications\\/13\\/edit\"}', NULL, '2026-05-13 12:16:39', '2026-05-13 12:16:39'),
('04e67e19-6d86-41c4-a742-5048c861b933', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 12, '{\"application_id\":14,\"application_number\":\"APP-2026-00002\",\"student_name\":\"TANJU AKTER NILOY NILOY\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00002 created by Inoodex\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/14\\/edit\"}', NULL, '2026-05-14 01:47:01', '2026-05-14 01:47:01'),
('095f7872-ac22-4286-9786-7f414a58b464', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 12, '{\"application_id\":19,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/19\\/edit\"}', NULL, '2026-05-14 02:49:39', '2026-05-14 02:49:39'),
('0e761c86-145a-463f-a864-6cee7fb317fb', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 10, '{\"application_id\":19,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/19\\/edit\"}', NULL, '2026-05-14 02:49:39', '2026-05-14 02:49:39'),
('0e9aa817-eca0-49e0-9f94-1fc60a760dd6', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 3, '{\"lead_id\":11,\"student_name\":\"Sequi denuncio vesper apto desolo. Virtus cumque unus arcus talus. Adimpleo nobis sollicito tepidus.\",\"phone\":\"758-252-1766\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/11\"}', NULL, '2026-08-13 10:04:22', '2026-08-13 10:04:22'),
('12dba115-e4c9-4fdf-a573-e5fc67dd37bd', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 6, '{\"application_id\":19,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/19\\/edit\"}', '2026-05-14 02:54:56', '2026-05-14 02:49:39', '2026-05-14 02:54:56'),
('14169c2e-f44b-4ed7-98b6-7bf6699077a0', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 3, '{\"lead_id\":12,\"student_name\":\"Md Rahat\",\"phone\":\"01531385988\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/12\"}', NULL, '2026-10-21 04:33:57', '2026-10-21 04:33:57'),
('1469b6b5-9ef0-49a4-be8d-343903740f87', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 9, '{\"application_id\":18,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/18\\/edit\"}', NULL, '2026-05-14 02:46:09', '2026-05-14 02:46:09'),
('150a2618-cddb-43b4-97a5-112c91946bd0', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 3, '{\"lead_id\":13,\"student_name\":\"Shawon Ahmed\",\"phone\":\"01621816806\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/13\"}', NULL, '2026-10-21 04:35:46', '2026-10-21 04:35:46'),
('1867b24d-ee33-48b9-999b-5a44fe368e31', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 3, '{\"lead_id\":10,\"student_name\":\"MD SABIT AL SHEIKH\",\"phone\":\"+880 1704-737875\",\"created_by\":\"Abu Haider\",\"message\":\"New lead submitted by Abu Haider\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/marketing\\/leads\\/10\"}', '2026-05-14 01:32:38', '2026-05-13 12:11:26', '2026-05-14 01:32:38'),
('217392cd-8fb3-4213-b580-8a20bdbeb468', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 10, '{\"application_id\":21,\"application_number\":\"APP-2026-00001\",\"student_name\":\"Jubayer Ahmed\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00001 created by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/applications\\/21\\/edit\"}', NULL, '2026-10-21 05:09:59', '2026-10-21 05:09:59'),
('29246aa8-69db-4679-aac8-4c8845287ee8', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 9, '{\"lead_id\":9,\"student_name\":\"asik\",\"phone\":\"01917640643\",\"created_by\":\"Abdullah\",\"message\":\"New lead submitted by Abdullah\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/marketing\\/leads\\/9\"}', NULL, '2026-05-13 12:05:11', '2026-05-13 12:05:11'),
('2f1302c0-5b40-45b4-9570-1de733a2d335', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 6, '{\"application_id\":20,\"application_number\":\"APP-2026-00001\",\"student_name\":\"Jubayer Ahmed\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00001 created by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/applications\\/20\\/edit\"}', NULL, '2026-10-21 05:02:06', '2026-10-21 05:02:06'),
('383e6ad0-ab59-4f06-b69c-5654d0533fdf', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 9, '{\"lead_id\":10,\"student_name\":\"MD SABIT AL SHEIKH\",\"phone\":\"+880 1704-737875\",\"created_by\":\"Abu Haider\",\"message\":\"New lead submitted by Abu Haider\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/marketing\\/leads\\/10\"}', NULL, '2026-05-13 12:11:26', '2026-05-13 12:11:26'),
('397d71e6-5c54-4bfd-9ca3-95bc7ced7a02', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 10, '{\"application_id\":17,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/17\\/edit\"}', NULL, '2026-05-14 02:41:07', '2026-05-14 02:41:07'),
('41ffb12e-0dd1-4b08-b7f9-e1b64b97ae76', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 9, '{\"application_id\":16,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/16\\/edit\"}', NULL, '2026-05-14 02:26:48', '2026-05-14 02:26:48'),
('43aa9bea-8947-4c02-ae28-eb9299a3c3bc', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 11, '{\"lead_id\":11,\"student_name\":\"Sequi denuncio vesper apto desolo. Virtus cumque unus arcus talus. Adimpleo nobis sollicito tepidus.\",\"phone\":\"758-252-1766\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/11\"}', NULL, '2026-08-13 10:04:22', '2026-08-13 10:04:22'),
('4c00fb5c-4c3d-4c2c-b53b-e34ddd7fd4fb', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 9, '{\"application_id\":17,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/17\\/edit\"}', NULL, '2026-05-14 02:41:07', '2026-05-14 02:41:07'),
('4e95f737-63ed-4e06-b172-f37c0a4c4ce2', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 6, '{\"application_id\":17,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/17\\/edit\"}', '2026-05-14 02:54:56', '2026-05-14 02:41:07', '2026-05-14 02:54:56'),
('4ee257f0-bee2-444e-9651-096d9408269b', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 6, '{\"application_id\":18,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/18\\/edit\"}', '2026-05-14 02:54:56', '2026-05-14 02:46:09', '2026-05-14 02:54:56'),
('5164e7ec-7e35-4d3f-a92f-91668bb50f93', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 9, '{\"lead_id\":12,\"student_name\":\"Md Rahat\",\"phone\":\"01531385988\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/12\"}', NULL, '2026-10-21 04:33:57', '2026-10-21 04:33:57'),
('524cc27c-8292-4045-94b0-c67de70bf9c0', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 14, '{\"application_id\":16,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/16\\/edit\"}', NULL, '2026-05-14 02:26:48', '2026-05-14 02:26:48'),
('55a62c73-5c86-49d9-b3a5-0d3c0c6247b8', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 12, '{\"application_id\":18,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/18\\/edit\"}', NULL, '2026-05-14 02:46:09', '2026-05-14 02:46:09'),
('57c30c7f-2840-402e-aad5-feda37ae3867', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 10, '{\"application_id\":16,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/16\\/edit\"}', NULL, '2026-05-14 02:26:48', '2026-05-14 02:26:48'),
('58609091-68a6-4675-ae43-0584bb72cd1a', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 9, '{\"application_id\":20,\"application_number\":\"APP-2026-00001\",\"student_name\":\"Jubayer Ahmed\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00001 created by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/applications\\/20\\/edit\"}', NULL, '2026-10-21 05:02:06', '2026-10-21 05:02:06'),
('598ad990-18b7-4080-8cbe-b70e1b556303', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 10, '{\"application_id\":20,\"application_number\":\"APP-2026-00001\",\"student_name\":\"Jubayer Ahmed\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00001 created by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/applications\\/20\\/edit\"}', NULL, '2026-10-21 05:02:06', '2026-10-21 05:02:06'),
('5a707c46-1a2d-4ab0-b5eb-ebb131eb38ac', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 9, '{\"application_id\":15,\"application_number\":\"APP-2026-00003\",\"student_name\":\"TANJU AKTER NILOY NILOY\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00003 created by Inoodex\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/15\\/edit\"}', NULL, '2026-05-14 01:53:51', '2026-05-14 01:53:51'),
('5b231eb1-5c50-47a7-b79c-6d26ce498c7a', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 9, '{\"lead_id\":11,\"student_name\":\"Sequi denuncio vesper apto desolo. Virtus cumque unus arcus talus. Adimpleo nobis sollicito tepidus.\",\"phone\":\"758-252-1766\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/11\"}', NULL, '2026-08-13 10:04:22', '2026-08-13 10:04:22'),
('5b796b8c-ce4f-4faa-a04e-5f9a1ca3e940', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 12, '{\"application_id\":16,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/16\\/edit\"}', NULL, '2026-05-14 02:26:48', '2026-05-14 02:26:48'),
('6b34e6d5-4e89-4dc8-88d2-d767c43dc708', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 9, '{\"application_id\":19,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/19\\/edit\"}', NULL, '2026-05-14 02:49:39', '2026-05-14 02:49:39'),
('70c9622f-b760-446c-9e09-538329f3edb7', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 6, '{\"application_id\":21,\"application_number\":\"APP-2026-00001\",\"student_name\":\"Jubayer Ahmed\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00001 created by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/applications\\/21\\/edit\"}', NULL, '2026-10-21 05:09:59', '2026-10-21 05:09:59'),
('71c57c0a-544f-448c-b7d4-4b23f61310fe', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 12, '{\"lead_id\":10,\"student_name\":\"MD SABIT AL SHEIKH\",\"phone\":\"+880 1704-737875\",\"created_by\":\"Abu Haider\",\"message\":\"New lead submitted by Abu Haider\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/marketing\\/leads\\/10\"}', NULL, '2026-05-13 12:11:26', '2026-05-13 12:11:26'),
('746de50e-c9ef-474f-a784-c998e52e1c65', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 11, '{\"lead_id\":12,\"student_name\":\"Md Rahat\",\"phone\":\"01531385988\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/12\"}', NULL, '2026-10-21 04:33:57', '2026-10-21 04:33:57'),
('77a3a4bd-eea1-42be-949b-2dc4f2ec9583', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 12, '{\"lead_id\":9,\"student_name\":\"asik\",\"phone\":\"01917640643\",\"created_by\":\"Abdullah\",\"message\":\"New lead submitted by Abdullah\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/marketing\\/leads\\/9\"}', NULL, '2026-05-13 12:05:11', '2026-05-13 12:05:11'),
('7a47dda6-9bc0-4f9d-8ccd-bdf1d17df17b', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 12, '{\"application_id\":20,\"application_number\":\"APP-2026-00001\",\"student_name\":\"Jubayer Ahmed\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00001 created by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/applications\\/20\\/edit\"}', NULL, '2026-10-21 05:02:06', '2026-10-21 05:02:06'),
('7ad0b531-b6b0-44e4-aea7-e09fe673db52', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 3, '{\"lead_id\":9,\"student_name\":\"asik\",\"phone\":\"01917640643\",\"created_by\":\"Abdullah\",\"message\":\"New lead submitted by Abdullah\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/marketing\\/leads\\/9\"}', '2026-05-14 01:32:38', '2026-05-13 12:05:11', '2026-05-14 01:32:38'),
('7e95380f-363e-4831-afb8-6b1487b2115a', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 11, '{\"lead_id\":9,\"student_name\":\"asik\",\"phone\":\"01917640643\",\"created_by\":\"Abdullah\",\"message\":\"New lead submitted by Abdullah\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/marketing\\/leads\\/9\"}', NULL, '2026-05-13 12:05:11', '2026-05-13 12:05:11'),
('8795237a-d75b-4acf-ae87-c42d57d9aa66', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 14, '{\"application_id\":14,\"application_number\":\"APP-2026-00002\",\"student_name\":\"TANJU AKTER NILOY NILOY\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00002 created by Inoodex\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/14\\/edit\"}', NULL, '2026-05-14 01:47:01', '2026-05-14 01:47:01'),
('96f9b491-ec02-4eeb-8956-82913055f75d', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 12, '{\"application_id\":21,\"application_number\":\"APP-2026-00001\",\"student_name\":\"Jubayer Ahmed\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00001 created by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/applications\\/21\\/edit\"}', NULL, '2026-10-21 05:09:59', '2026-10-21 05:09:59'),
('999eabce-d1d6-472c-9ce4-d6d7f2732a9b', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 9, '{\"lead_id\":13,\"student_name\":\"Shawon Ahmed\",\"phone\":\"01621816806\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/13\"}', NULL, '2026-10-21 04:35:46', '2026-10-21 04:35:46'),
('9d83a652-a00e-46a2-8ab1-06237bcd2842', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 10, '{\"application_id\":15,\"application_number\":\"APP-2026-00003\",\"student_name\":\"TANJU AKTER NILOY NILOY\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00003 created by Inoodex\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/15\\/edit\"}', NULL, '2026-05-14 01:53:51', '2026-05-14 01:53:51'),
('9d991dfe-543c-41e1-bd1e-8d600f9045b6', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 8, '{\"lead_id\":9,\"student_name\":\"asik\",\"phone\":\"01917640643\",\"created_by\":\"Abdullah\",\"message\":\"New lead submitted by Abdullah\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/marketing\\/leads\\/9\"}', NULL, '2026-05-13 12:05:11', '2026-05-13 12:05:11'),
('a96765ca-3667-48ff-a615-8a8076bc05e9', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 6, '{\"application_id\":13,\"application_number\":\"APP-2026-00001\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Abu Haider\",\"message\":\"New application APP-2026-00001 created by Abu Haider\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/applications\\/13\\/edit\"}', '2026-05-14 02:35:07', '2026-05-13 12:16:39', '2026-05-14 02:35:07'),
('ab79d9bf-b3c5-4343-b3d0-6e684dd52bac', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 10, '{\"application_id\":18,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/18\\/edit\"}', NULL, '2026-05-14 02:46:09', '2026-05-14 02:46:09'),
('abb8bff9-8850-4c1e-ae1a-ca8f7d38c3ba', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 12, '{\"application_id\":17,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/17\\/edit\"}', NULL, '2026-05-14 02:41:07', '2026-05-14 02:41:07'),
('ae36590b-2333-41d9-9b7d-e23379c67c88', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 12, '{\"lead_id\":12,\"student_name\":\"Md Rahat\",\"phone\":\"01531385988\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/12\"}', NULL, '2026-10-21 04:33:57', '2026-10-21 04:33:57'),
('bbd1700c-ac2f-4bcc-b9f5-108e5975119d', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 10, '{\"application_id\":13,\"application_number\":\"APP-2026-00001\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Abu Haider\",\"message\":\"New application APP-2026-00001 created by Abu Haider\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/applications\\/13\\/edit\"}', NULL, '2026-05-13 12:16:39', '2026-05-13 12:16:39'),
('bd1f99bf-eceb-464d-9773-844502082d60', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 11, '{\"lead_id\":10,\"student_name\":\"MD SABIT AL SHEIKH\",\"phone\":\"+880 1704-737875\",\"created_by\":\"Abu Haider\",\"message\":\"New lead submitted by Abu Haider\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/marketing\\/leads\\/10\"}', NULL, '2026-05-13 12:11:26', '2026-05-13 12:11:26'),
('be25e526-ed99-4275-ac7f-5747655ef725', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 14, '{\"application_id\":18,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/18\\/edit\"}', NULL, '2026-05-14 02:46:09', '2026-05-14 02:46:09'),
('c0f7450c-5c77-44d4-bd54-4bb82c8f50d6', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 12, '{\"lead_id\":11,\"student_name\":\"Sequi denuncio vesper apto desolo. Virtus cumque unus arcus talus. Adimpleo nobis sollicito tepidus.\",\"phone\":\"758-252-1766\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/11\"}', NULL, '2026-08-13 10:04:22', '2026-08-13 10:04:22'),
('c1487205-a5bb-4ffa-9e28-6e935d242a49', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 14, '{\"application_id\":15,\"application_number\":\"APP-2026-00003\",\"student_name\":\"TANJU AKTER NILOY NILOY\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00003 created by Inoodex\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/15\\/edit\"}', NULL, '2026-05-14 01:53:51', '2026-05-14 01:53:51'),
('c552b757-da8f-4d19-92e0-cc15557c46d5', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 6, '{\"application_id\":15,\"application_number\":\"APP-2026-00003\",\"student_name\":\"TANJU AKTER NILOY NILOY\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00003 created by Inoodex\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/15\\/edit\"}', '2026-05-14 02:35:07', '2026-05-14 01:53:51', '2026-05-14 02:35:07'),
('cd9499a1-da67-4301-9947-3fe7854c3f7a', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 8, '{\"lead_id\":10,\"student_name\":\"MD SABIT AL SHEIKH\",\"phone\":\"+880 1704-737875\",\"created_by\":\"Abu Haider\",\"message\":\"New lead submitted by Abu Haider\",\"link\":\"https:\\/\\/crm.insafimmigration.com\\/dashboard\\/marketing\\/leads\\/10\"}', NULL, '2026-05-13 12:11:26', '2026-05-13 12:11:26'),
('d8e2897d-f0ce-400d-8475-c97ddc175588', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 9, '{\"application_id\":14,\"application_number\":\"APP-2026-00002\",\"student_name\":\"TANJU AKTER NILOY NILOY\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00002 created by Inoodex\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/14\\/edit\"}', NULL, '2026-05-14 01:47:01', '2026-05-14 01:47:01'),
('de63f451-9401-4b94-ac27-ce04d32b064a', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 11, '{\"lead_id\":13,\"student_name\":\"Shawon Ahmed\",\"phone\":\"01621816806\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/13\"}', NULL, '2026-10-21 04:35:46', '2026-10-21 04:35:46'),
('e2b6f8b0-45cf-405b-b989-e391f59c96e8', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 14, '{\"application_id\":17,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/17\\/edit\"}', NULL, '2026-05-14 02:41:07', '2026-05-14 02:41:07'),
('e4b1d89b-b855-44fd-8eef-10b94fd631a6', 'App\\Notifications\\NewLeadSubmitted', 'App\\Models\\User', 12, '{\"lead_id\":13,\"student_name\":\"Shawon Ahmed\",\"phone\":\"01621816806\",\"created_by\":\"Inoodex\",\"message\":\"New lead submitted by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/marketing\\/leads\\/13\"}', NULL, '2026-10-21 04:35:46', '2026-10-21 04:35:46'),
('e5a41bff-c3d3-4802-865e-7fb0501e8837', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 9, '{\"application_id\":21,\"application_number\":\"APP-2026-00001\",\"student_name\":\"Jubayer Ahmed\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00001 created by Inoodex\",\"link\":\"https:\\/\\/crm.inoodex.com\\/dashboard\\/applications\\/21\\/edit\"}', NULL, '2026-10-21 05:09:59', '2026-10-21 05:09:59'),
('e9cea2ae-982b-41d9-8e15-4f52458d3f22', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 6, '{\"application_id\":16,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/16\\/edit\"}', '2026-05-14 02:35:07', '2026-05-14 02:26:48', '2026-05-14 02:35:07'),
('eb61f1c5-c7be-4426-bc8b-30f617e95d55', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 14, '{\"application_id\":19,\"application_number\":\"APP-2026-00003\",\"student_name\":\"MD SABIT AL SHEIKH SHEIKH\",\"created_by\":\"Application\",\"message\":\"New application APP-2026-00003 created by Application\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/19\\/edit\"}', NULL, '2026-05-14 02:49:39', '2026-05-14 02:49:39'),
('f04fa185-9026-4173-b87e-68021098a738', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 6, '{\"application_id\":14,\"application_number\":\"APP-2026-00002\",\"student_name\":\"TANJU AKTER NILOY NILOY\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00002 created by Inoodex\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/14\\/edit\"}', '2026-05-14 02:35:07', '2026-05-14 01:47:01', '2026-05-14 02:35:07'),
('f8ebe329-21a0-4f86-8c58-a291da37c729', 'App\\Notifications\\NewApplicationNotification', 'App\\Models\\User', 12, '{\"application_id\":15,\"application_number\":\"APP-2026-00003\",\"student_name\":\"TANJU AKTER NILOY NILOY\",\"created_by\":\"Inoodex\",\"message\":\"New application APP-2026-00003 created by Inoodex\",\"link\":\"http:\\/\\/192.168.0.107:401\\/dashboard\\/applications\\/15\\/edit\"}', NULL, '2026-05-14 01:53:51', '2026-05-14 01:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `office_accounts`
--

CREATE TABLE `office_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_type` enum('bank','mfs','cash') NOT NULL,
  `provider_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) NOT NULL,
  `chart_of_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `branch_name` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `office_accounts`
--

INSERT INTO `office_accounts` (`id`, `account_name`, `account_type`, `provider_name`, `account_number`, `chart_of_account_id`, `opening_balance`, `branch_name`, `status`, `created_by`, `notes`, `created_at`, `updated_at`) VALUES
(4, 'Office Cash', 'cash', NULL, '-', 20, 150000.00, NULL, 'active', 1, 'previous cash', '2026-05-06 00:23:45', '2026-05-06 00:30:10'),
(5, 'Inoodex - Brac Bank', 'bank', 'BRAC Bank PLC', '2076708660001', 21, 250000.00, 'PANTHAPATH', 'active', 1, NULL, '2026-05-06 00:25:59', '2026-10-21 05:21:58'),
(6, 'Inoodex Pubali Bank', 'bank', 'Pubali Bank PLC', '3781901011402', 22, 200000.00, 'PANTHAPATH', 'active', 1, NULL, '2026-05-06 00:27:40', '2026-10-21 05:21:37');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type` enum('advance','partial','final') NOT NULL,
  `payment_date` datetime NOT NULL,
  `collected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `office_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `journal_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `privileges`
--

CREATE TABLE `privileges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `privileges`
--

INSERT INTO `privileges` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Marketing', '*marketing', NULL, '2026-02-17 12:47:37', '2026-02-17 12:47:37'),
(2, 'Consultant', '*consultant', NULL, '2026-02-17 12:50:08', '2026-02-17 23:20:02'),
(3, 'Accountant', '*accountant', NULL, '2026-02-17 23:02:46', '2026-02-17 23:02:46'),
(4, 'Editor', '*editor', NULL, '2026-02-18 02:45:49', '2026-02-18 02:45:49'),
(5, 'Application', '*application', NULL, '2026-02-18 22:39:48', '2026-02-18 22:39:48'),
(6, 'Generate Reports', 'report.generate', 'Allows generating system-wide reports.', '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(7, 'Manage Users', 'users.manage', 'Allows creating, editing, and deleting users.', '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(8, 'Manage Roles', 'roles.manage', 'Allows editing Tyro roles.', '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(9, 'View Billing', 'billing.view', 'Allows viewing billing statements.', '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(10, 'Wildcard', '*', 'Grants every privilege.', '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(11, 'Payment', '*payment', NULL, '2026-04-07 21:53:18', '2026-04-07 21:55:35'),
(12, 'Comission', '*comission', NULL, '2026-04-07 21:55:21', '2026-04-07 21:55:21'),
(13, 'Invoice', '*invoice', NULL, '2026-04-11 04:25:47', '2026-04-11 04:25:47'),
(14, 'Digital Marketing', '*digital_marketing', NULL, '2026-04-25 22:34:35', '2026-04-25 22:34:35');

-- --------------------------------------------------------

--
-- Table structure for table `privilege_role`
--

CREATE TABLE `privilege_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `privilege_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `privilege_role`
--

INSERT INTO `privilege_role` (`id`, `role_id`, `privilege_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2026-02-17 12:47:37', '2026-02-17 12:47:37'),
(2, 3, 2, '2026-02-17 12:50:08', '2026-02-17 12:50:08'),
(3, 4, 3, '2026-02-17 23:02:46', '2026-02-17 23:02:46'),
(4, 5, 4, '2026-02-18 02:45:49', '2026-02-18 02:45:49'),
(5, 6, 5, '2026-02-18 22:39:48', '2026-02-18 22:39:48'),
(6, 1, 6, '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(7, 10, 6, '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(8, 1, 7, '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(9, 10, 7, '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(10, 10, 8, '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(11, 1, 9, '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(12, 7, 9, '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(14, 4, 11, '2026-04-07 21:53:18', '2026-04-07 21:53:18'),
(15, 4, 12, '2026-04-07 21:55:21', '2026-04-07 21:55:21'),
(16, 3, 13, '2026-04-11 04:25:47', '2026-04-11 04:25:47'),
(17, 4, 13, '2026-04-11 04:25:47', '2026-04-11 04:25:47'),
(18, 11, 14, '2026-04-25 22:34:35', '2026-04-25 22:34:35');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', 1, '2026-02-16 03:13:51', '2026-02-21 01:19:44'),
(2, 'Marketing', 'marketing', 1, '2026-02-16 03:16:47', '2026-02-16 03:16:47'),
(3, 'Consultant', 'consultant', 1, '2026-02-17 12:49:50', '2026-02-17 12:57:56'),
(4, 'Accountant', 'accountant', 1, '2026-02-17 23:02:22', '2026-02-17 23:02:22'),
(5, 'Editor', 'editor', 1, '2026-02-18 02:45:05', '2026-02-18 02:45:05'),
(6, 'Application', 'application', 1, '2026-02-18 22:39:28', '2026-02-18 22:52:44'),
(7, 'User', 'user', 1, '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(10, 'Super Admin', 'super-admin', 1, '2026-02-21 01:19:44', '2026-02-21 01:19:44'),
(11, 'Digital Marketing', 'digital-marketing', 1, '2026-04-25 22:33:54', '2026-04-25 22:33:54');

-- --------------------------------------------------------

--
-- Table structure for table `salaries`
--

CREATE TABLE `salaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_name` varchar(255) NOT NULL,
  `month` varchar(7) NOT NULL,
  `basic_salary` decimal(12,2) NOT NULL,
  `overtime_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bonus` decimal(10,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gross_salary` decimal(12,2) NOT NULL,
  `tax_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `insurance_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','partial','paid') NOT NULL DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `payment_method` enum('cash','bank_transfer','mobile_banking','cheque') DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_branch` varchar(255) DEFAULT NULL,
  `routing_number` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `journal_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salaries`
--

INSERT INTO `salaries` (`id`, `user_id`, `employee_name`, `month`, `basic_salary`, `overtime_amount`, `bonus`, `allowances`, `gross_salary`, `tax_deduction`, `insurance_deduction`, `other_deductions`, `net_salary`, `paid_amount`, `payment_status`, `payment_date`, `payment_method`, `account_number`, `bank_name`, `bank_branch`, `routing_number`, `transaction_id`, `journal_entry_id`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(141, NULL, 'Md. Abul Hasan Saidy', '2026-05', 60000.00, 0.00, 0.00, 0.00, 60000.00, 0.00, 0.00, 0.00, 60000.00, 0.00, 'pending', NULL, NULL, '3781-101-83523', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(142, NULL, 'Mohammad Faisal', '2026-05', 60000.00, 0.00, 0.00, 0.00, 60000.00, 0.00, 0.00, 0.00, 60000.00, 0.00, 'pending', NULL, NULL, '3781-101-83725', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(143, NULL, 'Insan Kamal Shafat', '2026-05', 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 'pending', NULL, NULL, '3781-101-83764', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(144, NULL, 'Sakib Hasan', '2026-05', 46000.00, 0.00, 0.00, 0.00, 46000.00, 0.00, 0.00, 0.00, 46000.00, 0.00, 'pending', NULL, NULL, '3781-101-83536', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(145, NULL, 'Lutfur Kabir Rana', '2026-05', 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 'pending', NULL, NULL, '3781-101-83501', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(146, NULL, 'Sharafat Ullah Mohim', '2026-05', 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 'pending', NULL, NULL, '3781-101-83756', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(147, NULL, 'Mainul Hasan', '2026-05', 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 'pending', NULL, NULL, '3781-101-83609', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(148, NULL, 'Singmay Chowdhury', '2026-05', 30000.00, 0.00, 0.00, 0.00, 30000.00, 0.00, 0.00, 0.00, 30000.00, 0.00, 'pending', NULL, NULL, '3781-101-83540', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(149, NULL, 'Arif Hossain Nayan', '2026-05', 17000.00, 0.00, 0.00, 0.00, 17000.00, 0.00, 0.00, 0.00, 17000.00, 0.00, 'pending', NULL, NULL, '3781-101-83710', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(150, NULL, 'Abdul Alim Shezan', '2026-05', 32000.00, 0.00, 0.00, 0.00, 32000.00, 0.00, 0.00, 0.00, 32000.00, 0.00, 'pending', NULL, NULL, '3781-101-83586', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(151, NULL, 'Moshraful Islam', '2026-05', 30000.00, 0.00, 0.00, 0.00, 30000.00, 0.00, 0.00, 0.00, 30000.00, 0.00, 'pending', NULL, NULL, '3781-101-83560', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(152, NULL, 'Harunur Rashid', '2026-05', 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 'pending', NULL, NULL, '3781-101-83684', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(153, NULL, 'Abu haider', '2026-05', 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 'pending', NULL, NULL, '3781-101-83783', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(154, NULL, 'Anta Tasnim Rafa', '2026-05', 17500.00, 0.00, 0.00, 0.00, 17500.00, 0.00, 0.00, 0.00, 17500.00, 0.00, 'pending', NULL, NULL, '3781-101-83555', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(155, NULL, 'Kawcer Hossen Rakib', '2026-05', 25000.00, 0.00, 0.00, 0.00, 25000.00, 0.00, 0.00, 0.00, 25000.00, 0.00, 'pending', NULL, NULL, '3781-101-83497', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(156, NULL, 'Md Shohan', '2026-05', 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 'pending', NULL, NULL, '3781-101-83706', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(157, NULL, 'Mahabub Hossain Alif', '2026-05', 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 'pending', NULL, NULL, '3781-101-83730', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(158, NULL, 'Shah Amanat Ullah', '2026-05', 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 'pending', NULL, NULL, '3781-101-83747', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(159, NULL, 'MOSHAROF RONY', '2026-05', 9000.00, 0.00, 0.00, 0.00, 9000.00, 0.00, 0.00, 0.00, 9000.00, 0.00, 'pending', NULL, NULL, '3781-101-83594', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:18', '2026-05-07 01:52:13'),
(160, NULL, 'Mohammed Abdullah', '2026-05', 11000.00, 0.00, 0.00, 0.00, 11000.00, 0.00, 0.00, 0.00, 11000.00, 0.00, 'pending', NULL, NULL, '3781-101-83693', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:19', '2026-05-07 01:52:13'),
(161, NULL, 'Chelsi Rema', '2026-05', 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 'pending', NULL, NULL, '3781-101-83779', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:19', '2026-05-07 01:52:13'),
(162, NULL, 'Emelia Ani Areng', '2026-05', 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 'pending', NULL, NULL, '3781-101-83667', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:19', '2026-05-07 01:52:13'),
(163, NULL, 'Rakesh Saha', '2026-05', 8000.00, 0.00, 0.00, 0.00, 8000.00, 0.00, 0.00, 0.00, 8000.00, 0.00, 'pending', NULL, NULL, '3781-101-83652', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:19', '2026-05-07 01:52:13'),
(164, NULL, 'Barsha Saha', '2026-05', 8000.00, 0.00, 0.00, 0.00, 8000.00, 0.00, 0.00, 0.00, 8000.00, 0.00, 'pending', NULL, NULL, '3781-101-83630', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:19', '2026-05-07 01:52:13'),
(165, NULL, 'Riad Mia', '2026-05', 21000.00, 0.00, 0.00, 0.00, 21000.00, 0.00, 0.00, 0.00, 21000.00, 0.00, 'pending', NULL, NULL, '3781-101-83822', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 01:49:19', '2026-05-07 01:52:13'),
(166, NULL, 'Md. Abul Hasan Saidy', '2026-10', 60000.00, 0.00, 0.00, 0.00, 60000.00, 0.00, 0.00, 0.00, 60000.00, 60000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83523', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(167, NULL, 'Mohammad Faisal', '2026-10', 60000.00, 0.00, 0.00, 0.00, 60000.00, 0.00, 0.00, 0.00, 60000.00, 60000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83725', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(168, NULL, 'Insan Kamal Shafat', '2026-10', 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 0.00, 0.00, 40000.00, 40000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83764', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(169, NULL, 'Sakib Hasan', '2026-10', 46000.00, 0.00, 0.00, 0.00, 46000.00, 0.00, 0.00, 0.00, 46000.00, 46000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83536', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(170, NULL, 'Lutfur Kabir Rana', '2026-10', 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 0.00, 0.00, 40000.00, 40000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83501', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(171, NULL, 'Sharafat Ullah Mohim', '2026-10', 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 0.00, 0.00, 40000.00, 40000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83756', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(172, NULL, 'Mainul Hasan', '2026-10', 40000.00, 0.00, 0.00, 0.00, 40000.00, 0.00, 0.00, 0.00, 40000.00, 40000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83609', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(173, NULL, 'Singmay Chowdhury', '2026-10', 30000.00, 0.00, 0.00, 0.00, 30000.00, 0.00, 0.00, 0.00, 30000.00, 30000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83540', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(174, NULL, 'Arif Hossain Nayan', '2026-10', 17000.00, 0.00, 0.00, 0.00, 17000.00, 0.00, 0.00, 0.00, 17000.00, 17000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83710', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(175, NULL, 'Abdul Alim Shezan', '2026-10', 32000.00, 0.00, 0.00, 0.00, 32000.00, 0.00, 0.00, 0.00, 32000.00, 32000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83586', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(176, NULL, 'Moshraful Islam', '2026-10', 30000.00, 0.00, 0.00, 0.00, 30000.00, 0.00, 0.00, 0.00, 30000.00, 30000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83560', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(177, NULL, 'Harunur Rashid', '2026-10', 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 0.00, 0.00, 15000.00, 15000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83684', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(178, NULL, 'Abu haider', '2026-10', 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 0.00, 0.00, 13000.00, 13000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83783', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(179, NULL, 'Anta Tasnim Rafa', '2026-10', 17500.00, 0.00, 0.00, 0.00, 17500.00, 0.00, 0.00, 0.00, 17500.00, 17500.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83555', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(180, NULL, 'Kawcer Hossen Rakib', '2026-10', 25000.00, 0.00, 0.00, 0.00, 25000.00, 0.00, 0.00, 0.00, 25000.00, 25000.00, 'paid', '2026-10-10', 'bank_transfer', '3781-101-83497', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:53:58'),
(181, NULL, 'Md Shohan', '2026-10', 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 'pending', NULL, NULL, '3781-101-83706', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:52:50'),
(182, NULL, 'Mahabub Hossain Alif', '2026-10', 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 'pending', NULL, NULL, '3781-101-83730', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:52:50'),
(183, NULL, 'Shah Amanat Ullah', '2026-10', 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 0.00, 0.00, 15000.00, 0.00, 'pending', NULL, NULL, '3781-101-83747', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:52:50'),
(184, NULL, 'MOSHAROF RONY', '2026-10', 9000.00, 0.00, 0.00, 0.00, 9000.00, 0.00, 0.00, 0.00, 9000.00, 0.00, 'pending', NULL, NULL, '3781-101-83594', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:52:50'),
(185, NULL, 'Mohammed Abdullah', '2026-10', 11000.00, 0.00, 0.00, 0.00, 11000.00, 0.00, 0.00, 0.00, 11000.00, 0.00, 'pending', NULL, NULL, '3781-101-83693', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:52:50'),
(186, NULL, 'Chelsi Rema', '2026-10', 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 'pending', NULL, NULL, '3781-101-83779', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:52:50'),
(187, NULL, 'Emelia Ani Areng', '2026-10', 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 0.00, 0.00, 13000.00, 0.00, 'pending', NULL, NULL, '3781-101-83667', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:52:50'),
(188, NULL, 'Rakesh Saha', '2026-10', 8000.00, 0.00, 0.00, 0.00, 8000.00, 0.00, 0.00, 0.00, 8000.00, 0.00, 'pending', NULL, NULL, '3781-101-83652', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:52:50'),
(189, NULL, 'Barsha Saha', '2026-10', 8000.00, 0.00, 0.00, 0.00, 8000.00, 0.00, 0.00, 0.00, 8000.00, 0.00, 'pending', NULL, NULL, '3781-101-83630', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:52:50'),
(190, NULL, 'Riad Mia', '2026-10', 21000.00, 0.00, 0.00, 0.00, 21000.00, 0.00, 0.00, 0.00, 21000.00, 0.00, 'pending', NULL, NULL, '3781-101-83822', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-10-21 05:52:50', '2026-10-21 05:52:50');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('39mSso71yKOm6KWYnwfNrJBrGCmLjLbA383WIvlN', NULL, '104.23.172.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSjFlVER3WlFrcmNoalpqRExBbm9iMHhxTWdMa3dobmsyaTR1OHhyaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792774901),
('93Xin1XEfMCdvKnRJk1SayitDeDOGTHd58kPWkNt', NULL, '172.71.183.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVTZJYnRrUDlhNXFxa2ZsejNHUERndHhKN1kwakZteG1yeXc5bVZVaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792774828),
('E1RapybExWb5JQ9DgpVHMeuXa22aQxtnB3Hh1DQD', NULL, '141.101.76.119', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM0JMTE1CdEtBZUhhSEhsSHFQMjJDRWMzcG1Dd2xGak1GdE9CUUVOOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792782903),
('e8o32Wz4Q40zt8y8X6mflE0awpcyRJYrdOu3SG0J', NULL, '162.158.108.19', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidEVYdnRQNU9xTjFqRkVRQkNMSXZ6MXJiN0ZzTVUyWVNGWWNhVGNHaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792792313),
('g3SkzdpNlVzBwj9NU6aRK3V6tBJribTH100gEEy3', NULL, '141.101.68.32', 'Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidVVVb3IwSUhXUlpvZ21MMkFFUno4UU4wZVFGVjVzOGx5V3hZNmc5ZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792778470),
('i5gGpxLZnMmjWbXzgsACzR1VPKiGiVijB8fBpYsf', NULL, '162.158.10.222', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaENJOXlRajV4ZVdWbzlHVXRxdGFpWE1KTUpWVnlYMUx1NE1nYmZ2ViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792769181),
('ONxdUqH3p3Im47Jkuwb6Ya0COOUv3DDvS7C6MyWp', NULL, '172.71.99.71', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidndCc2h1b3k1YlFBWHk1ZGtRaUxWU21xUlRqcWxEemVoaTZsaFRwRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792774904),
('Pa72T6kl361oYp5Za9EzuBu7m6bTOWbDBLDv6v0C', NULL, '104.23.209.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 (compatible; meta-webindexer/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler))', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOEpTaDRoY0pNak5tYlRkZjNMeUFBZk1UTXBGdDc4RlI2dTQ5cjlrdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792781798),
('q5F0pjA3LhyRmc2wHoggGOOtkdGgMeiarm5Pb5mr', NULL, '104.23.166.137', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidEF4cjRzbFZVQ29EUGRPcXJmSEtKbUYyMUxwbG1hdUhrbXhTWWh3RyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792774455),
('rCF4EEYaMwBuMOw97npCJJL1A4A9h7ETpyNk2XJE', NULL, '162.158.10.222', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTnBkZjVPWTNvOE4zMlRDNzhQNU1WZEdNMUR4OWdLaEZFZHR6bFVzRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792769167),
('RmijM5bduUqP00qORwgEVCUEbtWCJm6QLKO0vx3t', NULL, '141.101.68.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN1pidXIzeVQ4cGJPTEFkMVRsc041YTdyRU5LOEZST2hMU1VKcTRTcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792788645),
('T4eOUipuN5k3bsHpwakoYDU2e5oBMLkc1a7y6EcW', NULL, '104.22.20.5', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidTlrVGJjR0ZyaEZSbzhVNFVMY1VlOTNYWE9KdVNPSHFRR0MxZ3hlViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vY3JtLmlub29kZXguY29tIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792814654),
('wXJjrlXhDI62r4gfsTEVr9r10re0TCPHw3zF6uzi', NULL, '172.69.7.230', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVm56UzZRbW9WNEl2MGh5ak55TktPWUpQd0t4TWVvNVJBS0tsemlhRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vY3JtLmlub29kZXguY29tL2Rhc2hib2FyZC9zZXR0aW5ncyI7czo1OiJyb3V0ZSI7czoyMDoiYWRtaW4uc2V0dGluZ3MuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjEwOiJ0eXJvLWxvZ2luIjthOjE6e3M6NzoiY2FwdGNoYSI7YToxOntzOjU6ImxvZ2luIjtpOjE4O319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMzoiaHR0cHM6Ly9jcm0uaW5vb2RleC5jb20vZGFzaGJvYXJkIjt9fQ==', 1792807029),
('z8bZkl95ZKZdUPaWiAZngZ0ISlC81fWvVfBXnPLD', NULL, '34.236.123.207', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSHFqZ2VSdUhDanREYVJKd2dPR2RMeVdBYUNWODBZVzRNcU1CeFJBeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vd3d3LmNybS5pbm9vZGV4LmNvbSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1792801638);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'Inoodex', '2026-02-16 03:20:28', '2026-05-18 15:01:39'),
(2, 'contact_email', 'hello@inoodex.com', '2026-02-16 03:20:28', '2026-05-18 15:02:33'),
(3, 'contact_phone', '+8801531385988', '2026-02-16 03:20:28', '2026-05-18 15:02:33'),
(4, 'address', 'House: E1, Road: 16, Pallabi (2nd Phase), Dhaka-1216', '2026-02-16 03:20:28', '2026-05-18 15:02:33'),
(5, 'social_facebook', NULL, '2026-02-16 03:20:28', '2026-02-16 03:20:28'),
(6, 'social_twitter', NULL, '2026-02-16 03:20:28', '2026-02-16 03:20:28'),
(7, 'social_linkedin', NULL, '2026-02-16 03:20:28', '2026-02-16 03:20:28'),
(8, 'currency_symbol', NULL, '2026-02-16 03:20:28', '2026-02-22 00:06:27'),
(9, 'date_format', 'd/m/Y', '2026-02-16 03:20:28', '2026-02-16 03:20:28'),
(10, 'enable_registration', '0', '2026-02-16 03:20:28', '2026-02-16 03:20:28'),
(11, 'maintenance_mode', '0', '2026-02-16 03:20:28', '2026-02-16 03:20:28'),
(12, 'meta_title', NULL, '2026-02-16 03:20:28', '2026-02-16 03:20:28'),
(13, 'meta_description', NULL, '2026-02-16 03:20:28', '2026-02-16 03:20:28'),
(14, 'meta_keywords', NULL, '2026-02-16 03:20:28', '2026-02-16 03:20:28'),
(15, 'app_logo', 'uploads/settings/jSuakKfEOmsTShQGpn6cWuZLtTpWSZDjgS1n6iJh.png', '2026-02-17 22:37:17', '2026-07-06 14:46:38'),
(16, 'app_favicon', 'uploads/settings/aRC2ZEixdKMZ58M0BxPpwE7x9p2TOiuiwumQYQSx.png', '2026-02-17 22:37:17', '2026-07-06 14:46:38');

-- --------------------------------------------------------

--
-- Table structure for table `social_accounts`
--

CREATE TABLE `social_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `provider` varchar(255) NOT NULL,
  `provider_user_id` varchar(255) NOT NULL,
  `provider_email` varchar(255) DEFAULT NULL,
  `provider_avatar` varchar(255) DEFAULT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `passport_number` varchar(255) DEFAULT NULL,
  `passport_validity` date DEFAULT NULL,
  `translation_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `sponsor_phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `ssc_result` varchar(255) DEFAULT NULL,
  `hsc_result` varchar(255) DEFAULT NULL,
  `ielts_score` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `package_amount` decimal(12,2) DEFAULT NULL,
  `country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `university_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `course_intake_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_stage` enum('lead','counseling','payment','application','offer','visa','enrolled') DEFAULT NULL,
  `current_status` enum('pending','applied','rejected','withdrawn','visa_processing','enrolled') DEFAULT NULL,
  `assigned_marketing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_consultant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_application_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `last_name`, `father_name`, `mother_name`, `passport_number`, `passport_validity`, `translation_documents`, `email`, `password`, `plain_password`, `phone`, `sponsor_phone`, `address`, `dob`, `ssc_result`, `hsc_result`, `ielts_score`, `subject`, `package_amount`, `country_id`, `university_id`, `course_id`, `course_intake_id`, `current_stage`, `current_status`, `assigned_marketing_id`, `assigned_consultant_id`, `assigned_application_id`, `created_by`, `documents`, `created_at`, `updated_at`) VALUES
(11, 'Hasnain', 'Shishir', 'Md Rahman', 'Julekha Begum', 'A659697798', '2029-06-01', NULL, 'hasnain@gmail.com', '$2y$12$HCxHESHAqrT2hNpvLlJBJeNu2tplUOHAQn0XsV3Tn8uWZgVMxTLla', '12345678', '01904400205', '01925876944', 'Dhanmondi,Dhaka', '2002-01-11', NULL, NULL, NULL, NULL, 1500000.00, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '[{\"name\":\"bdpass.jfif\",\"path\":\"documents\\/students\\/XldKaaKqPA3L24VFJzeC3GD6yWpCbpGSJPADoZch.jpg\"}]', '2026-10-21 04:46:25', '2026-10-21 04:46:25'),
(12, 'Jubayer', 'Ahmed', 'Md Mizan', NULL, 'A89969779', '2030-07-27', '[]', 'jubayer@gmail.com', '$2y$12$uAMLBi1.OhHf.zezjzOTQu7WTKCE3jh.hPb675U8Sgl7kZ312Zbf2', '12345678', '01621867987', NULL, 'Rajshahi', '1998-02-27', NULL, NULL, NULL, NULL, NULL, 4, 3, 4, 6, NULL, NULL, NULL, NULL, NULL, 1, '[{\"name\":\"bdpass.jfif\",\"path\":\"documents\\/students\\/4dVvgIBM9y0lkZJ8d5D2X5rT7Fe1IWp7rYzjvavA.jpg\"}]', '2026-10-21 04:56:06', '2026-10-21 05:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chart_of_account_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `rate` decimal(5,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tyro_audit_logs`
--

CREATE TABLE `tyro_audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `auditable_type` varchar(255) DEFAULT NULL,
  `auditable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `tyro_audit_logs`
--

INSERT INTO `tyro_audit_logs` (`id`, `user_id`, `event`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `metadata`, `created_at`) VALUES
(1, 1, 'role.deleted', 'HasinHayder\\Tyro\\Models\\Role', 8, '{\"id\": 8, \"name\": \"Customer\", \"slug\": \"customer\", \"is_active\": 1}', NULL, '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0\"}', '2026-02-22 16:51:11'),
(2, 1, 'role.deleted', NULL, NULL, '{\"id\": 8, \"name\": \"Customer\", \"slug\": \"customer\"}', NULL, '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0\"}', '2026-02-22 16:51:11'),
(3, 1, 'role.deleted', 'HasinHayder\\Tyro\\Models\\Role', 9, '{\"id\": 9, \"name\": \"All\", \"slug\": \"*\", \"is_active\": 1}', NULL, '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0\"}', '2026-02-22 16:51:16'),
(4, 1, 'role.deleted', NULL, NULL, '{\"id\": 9, \"name\": \"All\", \"slug\": \"*\"}', NULL, '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0\"}', '2026-02-22 16:51:16'),
(5, 1, 'role.assigned', 'App\\Models\\User', 3, NULL, '{\"role_id\": 5, \"role_slug\": \"editor\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0\"}', '2026-02-23 07:13:12'),
(6, 1, 'role.removed', 'App\\Models\\User', 3, NULL, '{\"role_id\": 5, \"role_slug\": \"editor\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0\"}', '2026-02-23 07:15:13'),
(7, 1, 'user.suspended', 'App\\Models\\User', 5, '{\"suspended_at\": null, \"suspension_reason\": null}', '{\"suspended_at\": \"2026-02-23T07:15:26.738707Z\", \"suspension_reason\": \"test\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0\"}', '2026-02-23 07:15:26'),
(8, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-07 12:11:37'),
(9, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-07 12:11:57'),
(10, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:45:54'),
(11, 1, 'privilege.created', 'HasinHayder\\Tyro\\Models\\Privilege', 11, NULL, '{\"id\": 11, \"name\": \"payment\", \"slug\": \".payment\", \"description\": null}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:53:18'),
(12, 1, 'privilege.attached', 'HasinHayder\\Tyro\\Models\\Role', 4, NULL, '{\"privilege_id\": 11, \"privilege_slug\": \".payment\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:53:18'),
(13, 1, 'privilege.created', 'HasinHayder\\Tyro\\Models\\Privilege', 11, NULL, '{\"id\": 11, \"name\": \"payment\", \"slug\": \".payment\", \"roles\": [4], \"description\": null}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:53:18'),
(14, 1, 'privilege.updated', 'HasinHayder\\Tyro\\Models\\Privilege', 11, '{\"id\": 11, \"name\": \"payment\", \"slug\": \".payment\", \"created_at\": \"2026-04-08T03:53:18.000000Z\", \"updated_at\": \"2026-04-08T03:53:18.000000Z\", \"description\": null}', '{\"slug\": \"*payment\", \"updated_at\": \"2026-04-08 03:53:33\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:53:33'),
(15, 1, 'privilege.slug_changed', 'HasinHayder\\Tyro\\Models\\Privilege', 11, '{\"slug\": \".payment\"}', '{\"slug\": \"*payment\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:53:33'),
(16, 1, 'privilege.created', 'HasinHayder\\Tyro\\Models\\Privilege', 12, NULL, '{\"id\": 12, \"name\": \"Comission\", \"slug\": \"*comission\", \"description\": null}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:55:21'),
(17, 1, 'privilege.attached', 'HasinHayder\\Tyro\\Models\\Role', 4, NULL, '{\"privilege_id\": 12, \"privilege_slug\": \"*comission\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:55:21'),
(18, 1, 'privilege.created', 'HasinHayder\\Tyro\\Models\\Privilege', 12, NULL, '{\"id\": 12, \"name\": \"Comission\", \"slug\": \"*comission\", \"roles\": [4], \"description\": null}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:55:21'),
(19, 1, 'privilege.updated', 'HasinHayder\\Tyro\\Models\\Privilege', 11, '{\"id\": 11, \"name\": \"payment\", \"slug\": \"*payment\", \"created_at\": \"2026-04-08T03:53:18.000000Z\", \"updated_at\": \"2026-04-08T03:53:33.000000Z\", \"description\": null}', '{\"name\": \"Payment\", \"updated_at\": \"2026-04-08 03:55:35\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:55:35'),
(20, 1, 'privilege.name_changed', 'HasinHayder\\Tyro\\Models\\Privilege', 11, '{\"name\": \"payment\"}', '{\"name\": \"Payment\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:55:35'),
(21, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:55:55'),
(22, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:56:13'),
(23, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:57:34'),
(24, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:57:55'),
(25, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 03:58:49'),
(26, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 04:39:12'),
(27, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 04:39:22'),
(28, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-08 05:26:18'),
(29, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 10:42:19'),
(30, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 12:28:36'),
(31, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-08 12:28:50'),
(32, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-09 04:24:41'),
(33, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-09 04:25:26'),
(34, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-09 04:25:55'),
(35, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-09 04:28:31'),
(36, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-09 04:31:51'),
(37, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"192.168.0.173\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36\"}', '2026-04-09 04:42:23'),
(38, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"192.168.0.173\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36\"}', '2026-04-09 04:45:38'),
(39, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"192.168.0.173\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36\"}', '2026-04-09 04:45:59'),
(40, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"192.168.0.173\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36\"}', '2026-04-09 04:57:39'),
(41, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"192.168.0.173\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36\"}', '2026-04-09 04:58:14'),
(42, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"192.168.0.173\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36\"}', '2026-04-09 05:10:30'),
(43, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"192.168.0.173\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36\"}', '2026-04-09 05:10:50'),
(44, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-09 07:40:52'),
(45, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-09 07:42:45'),
(46, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-09 11:50:21'),
(47, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-09 11:50:35'),
(48, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-09 13:21:27'),
(49, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 03:38:23'),
(50, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 03:38:43'),
(51, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 03:38:57'),
(52, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 05:12:38'),
(53, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 05:12:48'),
(54, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 10:08:54'),
(55, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 10:25:11'),
(56, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 10:25:21'),
(57, 1, 'privilege.created', 'HasinHayder\\Tyro\\Models\\Privilege', 13, NULL, '{\"id\": 13, \"name\": \"Invoice\", \"slug\": \"*invoice\", \"description\": null}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 10:25:47'),
(58, 1, 'privilege.attached', 'HasinHayder\\Tyro\\Models\\Role', 3, NULL, '{\"privilege_id\": 13, \"privilege_slug\": \"*invoice\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 10:25:47'),
(59, 1, 'privilege.attached', 'HasinHayder\\Tyro\\Models\\Role', 4, NULL, '{\"privilege_id\": 13, \"privilege_slug\": \"*invoice\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 10:25:47'),
(60, 1, 'privilege.created', 'HasinHayder\\Tyro\\Models\\Privilege', 13, NULL, '{\"id\": 13, \"name\": \"Invoice\", \"slug\": \"*invoice\", \"roles\": [3, 4], \"description\": null}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 10:25:47'),
(61, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 10:26:07'),
(62, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-11 10:26:18'),
(63, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-12 10:14:29'),
(64, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 03:17:19'),
(65, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:04:35'),
(66, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:04:55'),
(67, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:05:17'),
(68, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:10:19'),
(69, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:10:42'),
(70, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:11:22'),
(71, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:11:54'),
(72, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:12:48'),
(73, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:13:39'),
(74, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:18:07'),
(75, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:18:34'),
(76, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:20:10'),
(77, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:27:34'),
(78, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:30:10'),
(79, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:36:02'),
(80, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:36:31'),
(81, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:45:43'),
(82, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 04:51:10'),
(83, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-13 05:39:40'),
(84, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-13 05:47:41'),
(85, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-13 06:05:04'),
(86, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-13 06:08:17'),
(87, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-13 06:09:13'),
(88, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-13 06:13:00'),
(89, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-13 06:14:20'),
(90, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-13 06:27:49'),
(91, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-13 06:27:59'),
(92, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-13 06:29:09'),
(93, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-13 06:29:17'),
(94, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 03:18:25'),
(95, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 04:00:42'),
(96, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 04:01:08'),
(97, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 05:34:14'),
(98, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 05:34:28'),
(99, 5, 'user.logout', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 06:12:33'),
(100, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 06:13:58'),
(101, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 07:33:12'),
(102, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 07:33:30'),
(103, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 07:43:36'),
(104, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 08:23:24'),
(105, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 08:23:42'),
(106, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 09:41:20'),
(107, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 09:41:32'),
(108, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 09:42:09'),
(109, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 09:42:20'),
(110, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 11:44:23'),
(111, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 11:44:34'),
(112, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 11:47:10'),
(113, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-15 11:47:19'),
(114, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-15 18:00:17'),
(115, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-15 18:15:33'),
(116, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-15 18:15:41'),
(117, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-17 10:15:10'),
(118, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-17 10:15:28'),
(119, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-18 07:27:42'),
(120, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-18 08:54:59'),
(121, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-18 08:55:11'),
(122, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-18 11:41:46'),
(123, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-18 11:41:58'),
(124, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-18 11:42:09'),
(125, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-18 11:57:15'),
(126, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-18 11:57:25'),
(127, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-18 12:01:46'),
(128, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-18 13:24:20'),
(129, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 03:39:10'),
(130, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 03:39:50'),
(131, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 03:39:59'),
(132, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 03:55:53'),
(133, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 03:56:00'),
(134, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:05:45'),
(135, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:05:54'),
(136, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:10:03'),
(137, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:10:12'),
(138, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:13:53'),
(139, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:14:02'),
(140, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:21:23'),
(141, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:21:32'),
(142, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:22:15'),
(143, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:22:28'),
(144, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:33:57'),
(145, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:34:05'),
(146, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:35:07'),
(147, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:35:19'),
(148, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:46:35'),
(149, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:46:50'),
(150, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:47:01'),
(151, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 04:47:08'),
(152, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 05:01:57'),
(153, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-19 05:02:05'),
(154, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 03:47:01'),
(155, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 03:49:31'),
(156, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 03:49:41'),
(157, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 05:14:30'),
(158, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 05:14:38'),
(159, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 06:27:58'),
(160, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 06:28:06'),
(161, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 06:28:36'),
(162, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 06:29:39'),
(163, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 06:44:42'),
(164, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 06:45:32'),
(165, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 06:55:52'),
(166, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 06:56:00'),
(167, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0\"}', '2026-04-20 08:17:54'),
(168, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-20 09:23:40'),
(169, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-21 10:20:00'),
(170, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-22 03:13:21');
INSERT INTO `tyro_audit_logs` (`id`, `user_id`, `event`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `metadata`, `created_at`) VALUES
(171, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-22 09:44:43'),
(172, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-23 07:54:34'),
(173, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-23 08:17:18'),
(174, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-23 08:17:31'),
(175, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 04:07:35'),
(176, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 04:22:42'),
(177, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 04:22:52'),
(178, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 06:13:24'),
(179, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 06:50:59'),
(180, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 06:51:07'),
(181, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 07:11:21'),
(182, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 07:11:30'),
(183, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 07:30:42'),
(184, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 07:30:52'),
(185, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 11:03:35'),
(186, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 11:46:51'),
(187, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 11:47:08'),
(188, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 11:47:31'),
(189, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 11:47:39'),
(190, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 12:20:36'),
(191, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-25 12:20:45'),
(192, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 03:42:07'),
(193, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 03:59:46'),
(194, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:31:43'),
(195, 1, 'user.unsuspended', 'App\\Models\\User', 5, '{\"suspended_at\": \"2026-02-23 07:15:26\", \"suspension_reason\": \"test\"}', '{\"suspended_at\": null, \"suspension_reason\": null}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:32:28'),
(196, 1, 'role.created', 'HasinHayder\\Tyro\\Models\\Role', 11, NULL, '{\"id\": 11, \"name\": \"Digital Marketing\", \"slug\": \"digital-marketing\", \"is_active\": true}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:33:54'),
(197, 1, 'privilege.created', 'HasinHayder\\Tyro\\Models\\Privilege', 14, NULL, '{\"id\": 14, \"name\": \"Digital Marketing\", \"slug\": \"*digital_marketing\", \"description\": null}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:34:35'),
(198, 1, 'privilege.attached', 'HasinHayder\\Tyro\\Models\\Role', 11, NULL, '{\"privilege_id\": 14, \"privilege_slug\": \"*digital_marketing\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:34:35'),
(199, 1, 'privilege.created', 'HasinHayder\\Tyro\\Models\\Privilege', 14, NULL, '{\"id\": 14, \"name\": \"Digital Marketing\", \"slug\": \"*digital_marketing\", \"roles\": [11], \"description\": null}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:34:35'),
(200, 1, 'role.assigned', 'App\\Models\\User', 7, NULL, '{\"role_id\": 11, \"role_slug\": \"digital-marketing\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:34:57'),
(201, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:35:07'),
(202, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:35:23'),
(203, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:43:58'),
(204, 7, 'user.login', 'App\\Models\\User', 7, NULL, '{\"email\": \"digital_marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 04:44:09'),
(205, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 08:14:40'),
(206, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 08:14:53'),
(207, 7, 'user.login', 'App\\Models\\User', 7, NULL, '{\"email\": \"digital_marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 08:15:04'),
(208, 7, 'user.logout', 'App\\Models\\User', 7, NULL, '{\"email\": \"digital_marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 08:17:14'),
(209, 7, 'user.login', 'App\\Models\\User', 7, NULL, '{\"email\": \"digital_marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-26 08:17:31'),
(210, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 03:49:20'),
(211, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 04:34:26'),
(212, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 04:35:08'),
(213, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Macintosh; Intel Mac OS X 11_0) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Safari/605.1.15\"}', '2026-04-27 05:17:09'),
(214, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Macintosh; Intel Mac OS X 11_0) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Safari/605.1.15\"}', '2026-04-27 05:20:47'),
(215, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 06:34:47'),
(216, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 06:44:13'),
(217, 5, 'user.logout', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 07:19:29'),
(218, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 07:19:40'),
(219, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 09:57:49'),
(220, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 11:53:24'),
(221, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 11:53:37'),
(222, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 12:25:25'),
(223, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-27 12:25:37'),
(224, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-30 03:55:52'),
(225, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-04-30 09:50:10'),
(226, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-02 04:11:26'),
(227, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-03 10:31:15'),
(228, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-03 11:17:57'),
(229, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 03:34:19'),
(230, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 03:35:04'),
(231, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 03:37:02'),
(232, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 03:42:05'),
(233, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 03:45:59'),
(234, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 04:35:21'),
(235, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 04:35:31'),
(236, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 04:37:23'),
(237, 7, 'user.login', 'App\\Models\\User', 7, NULL, '{\"email\": \"digital_marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 04:37:32'),
(238, 7, 'user.logout', 'App\\Models\\User', 7, NULL, '{\"email\": \"digital_marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 04:48:21'),
(239, 2, 'user.login', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 04:48:31'),
(240, 2, 'user.logout', 'App\\Models\\User', 2, NULL, '{\"email\": \"marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 04:52:18'),
(241, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 04:52:28'),
(242, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 09:43:44'),
(243, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 09:43:55'),
(244, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 09:44:18'),
(245, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 09:44:33'),
(246, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 10:09:47'),
(247, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 10:10:34'),
(248, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 10:11:51'),
(249, 7, 'user.login', 'App\\Models\\User', 7, NULL, '{\"email\": \"digital_marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 10:12:17'),
(250, 7, 'user.logout', 'App\\Models\\User', 7, NULL, '{\"email\": \"digital_marketing@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 10:12:24'),
(251, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 10:12:38'),
(252, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 11:14:18'),
(253, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 11:14:35'),
(254, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 11:26:09'),
(255, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 11:26:25'),
(256, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 11:26:40'),
(257, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36\"}', '2026-05-06 11:26:47'),
(258, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 03:28:40'),
(259, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 05:09:32'),
(260, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 05:09:45'),
(261, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:05:35'),
(262, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:05:45'),
(263, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:13:25'),
(264, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:13:34'),
(265, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:15:01'),
(266, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:15:08'),
(267, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:16:55'),
(268, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:17:04'),
(269, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:18:12'),
(270, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:18:20'),
(271, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:19:00'),
(272, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:19:16'),
(273, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:25:01'),
(274, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:25:11'),
(275, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:26:22'),
(276, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:26:29'),
(277, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:37:23'),
(278, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:37:32'),
(279, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:40:25'),
(280, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:40:35'),
(281, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:54:40'),
(282, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:55:09'),
(283, 5, 'user.logout', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:58:26'),
(284, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 06:58:35'),
(285, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\": \"accountant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 11:22:23'),
(286, NULL, 'role.assigned', 'App\\Models\\User', 8, NULL, '{\"role_id\": 7, \"role_slug\": \"user\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 11:23:02'),
(287, 8, 'user.login', 'App\\Models\\User', 8, NULL, '{\"email\": \"xizecuh@mailinator.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 11:23:30'),
(288, 8, 'user.logout', 'App\\Models\\User', 8, NULL, '{\"email\": \"xizecuh@mailinator.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 11:23:41'),
(289, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-07 11:43:17'),
(290, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-09 10:31:38'),
(291, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 04:53:06'),
(292, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:15:55'),
(293, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:16:07'),
(294, 5, 'user.logout', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:16:13'),
(295, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:16:21'),
(296, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\": \"consultant@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:30:14'),
(297, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:30:22'),
(298, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:31:08'),
(299, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:33:59'),
(300, 5, 'user.logout', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:48:06'),
(301, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:48:18'),
(302, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:48:57'),
(303, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 05:49:09'),
(304, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:52:09'),
(305, 5, 'user.logout', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:52:18'),
(306, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:52:27'),
(307, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:52:33'),
(308, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:52:41'),
(309, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\": \"application@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:56:52'),
(310, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:57:04'),
(311, 5, 'user.logout', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:57:37'),
(312, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:57:46'),
(313, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\": \"hello@inoodex.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:58:13'),
(314, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\": \"editor@example.com\"}', '{\"ip\": \"127.0.0.1\", \"is_console\": false, \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36\"}', '2026-05-11 09:58:21'),
(315, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.141.51\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-11 12:44:11'),
(316, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.141.51\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-11 12:47:19'),
(317, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/147.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 04:16:42'),
(318, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/147.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 04:24:11'),
(319, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\":\"application@example.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/147.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 04:24:41'),
(320, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 05:51:47'),
(321, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 05:57:24'),
(322, 1, 'role.assigned', 'App\\Models\\User', 8, NULL, '{\"role_id\":2,\"role_slug\":\"marketing\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 05:57:40'),
(323, 1, 'role.assigned', 'App\\Models\\User', 8, NULL, '{\"role_id\":3,\"role_slug\":\"consultant\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 05:57:40'),
(324, 8, 'user.login', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 05:58:45'),
(325, 8, 'user.login', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:00:45'),
(326, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:01:58'),
(327, 8, 'user.login', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:02:07'),
(328, 8, 'user.login', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:03:51'),
(329, 8, 'user.logout', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:08:14'),
(330, 8, 'user.login', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:08:28'),
(331, 8, 'user.logout', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:13:12'),
(332, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:14:00'),
(333, 8, 'user.logout', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:14:37'),
(334, 8, 'user.login', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:14:51');
INSERT INTO `tyro_audit_logs` (`id`, `user_id`, `event`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `metadata`, `created_at`) VALUES
(335, 1, 'role.assigned', 'App\\Models\\User', 9, NULL, '{\"role_id\":4,\"role_slug\":\"accountant\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:19:56'),
(336, 1, 'role.assigned', 'App\\Models\\User', 9, NULL, '{\"role_id\":1,\"role_slug\":\"admin\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:19:56'),
(337, 1, 'role.assigned', 'App\\Models\\User', 9, NULL, '{\"role_id\":6,\"role_slug\":\"application\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:19:56'),
(338, 1, 'role.assigned', 'App\\Models\\User', 9, NULL, '{\"role_id\":3,\"role_slug\":\"consultant\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:19:56'),
(339, 1, 'role.assigned', 'App\\Models\\User', 9, NULL, '{\"role_id\":11,\"role_slug\":\"digital-marketing\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:19:56'),
(340, 1, 'role.assigned', 'App\\Models\\User', 9, NULL, '{\"role_id\":5,\"role_slug\":\"editor\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:19:56'),
(341, 1, 'role.assigned', 'App\\Models\\User', 9, NULL, '{\"role_id\":2,\"role_slug\":\"marketing\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:19:56'),
(342, 1, 'role.assigned', 'App\\Models\\User', 9, NULL, '{\"role_id\":10,\"role_slug\":\"super-admin\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:19:56'),
(343, 1, 'role.assigned', 'App\\Models\\User', 9, NULL, '{\"role_id\":7,\"role_slug\":\"user\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:19:56'),
(344, 9, 'user.login', 'App\\Models\\User', 9, NULL, '{\"email\":\"mdfoysal103545@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:21:35'),
(345, 8, 'user.logout', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:27:12'),
(346, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\":\"accountant@example.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:27:24'),
(347, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\":\"accountant@example.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:31:20'),
(348, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\":\"editor@example.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:31:29'),
(349, 5, 'user.logout', 'App\\Models\\User', 5, NULL, '{\"email\":\"editor@example.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:34:55'),
(350, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\":\"accountant@example.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:35:02'),
(351, 9, 'user.logout', 'App\\Models\\User', 9, NULL, '{\"email\":\"mdfoysal103545@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:38:18'),
(352, 9, 'user.login', 'App\\Models\\User', 9, NULL, '{\"email\":\"mdfoysal103545@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:38:27'),
(353, 1, 'role.assigned', 'App\\Models\\User', 10, NULL, '{\"role_id\":2,\"role_slug\":\"marketing\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:45:51'),
(354, 1, 'role.assigned', 'App\\Models\\User', 10, NULL, '{\"role_id\":6,\"role_slug\":\"application\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:45:51'),
(355, 1, 'role.assigned', 'App\\Models\\User', 10, NULL, '{\"role_id\":11,\"role_slug\":\"digital-marketing\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:45:51'),
(356, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:46:04'),
(357, 10, 'user.login', 'App\\Models\\User', 10, NULL, '{\"email\":\"nayanarifhossain@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:46:19'),
(358, 10, 'user.logout', 'App\\Models\\User', 10, NULL, '{\"email\":\"nayanarifhossain@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:56:53'),
(359, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 06:57:05'),
(360, 8, 'user.logout', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:00:43'),
(361, 1, 'role.assigned', 'App\\Models\\User', 11, NULL, '{\"role_id\":3,\"role_slug\":\"consultant\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:00:43'),
(362, 8, 'user.login', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:01:56'),
(363, 11, 'user.login', 'App\\Models\\User', 11, NULL, '{\"email\":\"mosharafulislam5490@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/147.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:02:20'),
(364, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:05:57'),
(365, 9, 'role.assigned', 'App\\Models\\User', 11, NULL, '{\"role_id\":5,\"role_slug\":\"editor\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:06:35'),
(366, 9, 'role.assigned', 'App\\Models\\User', 8, NULL, '{\"role_id\":5,\"role_slug\":\"editor\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:07:31'),
(367, 9, 'role.assigned', 'App\\Models\\User', 12, NULL, '{\"role_id\":3,\"role_slug\":\"consultant\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:49:46'),
(368, 9, 'role.assigned', 'App\\Models\\User', 12, NULL, '{\"role_id\":5,\"role_slug\":\"editor\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:49:46'),
(369, 9, 'role.assigned', 'App\\Models\\User', 12, NULL, '{\"role_id\":6,\"role_slug\":\"application\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:49:46'),
(370, 9, 'role.assigned', 'App\\Models\\User', 13, NULL, '{\"role_id\":2,\"role_slug\":\"marketing\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:58:40'),
(371, 13, 'user.login', 'App\\Models\\User', 13, NULL, '{\"email\":\"abdullahalvee404@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 07:59:24'),
(372, 10, 'user.login', 'App\\Models\\User', 10, NULL, '{\"email\":\"nayanarifhossain@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/147.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:00:59'),
(373, 13, 'user.logout', 'App\\Models\\User', 13, NULL, '{\"email\":\"abdullahalvee404@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:07:13'),
(374, 10, 'user.login', 'App\\Models\\User', 10, NULL, '{\"email\":\"nayanarifhossain@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:08:19'),
(375, 9, 'role.assigned', 'App\\Models\\User', 14, NULL, '{\"role_id\":5,\"role_slug\":\"editor\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:11:15'),
(376, 10, 'user.logout', 'App\\Models\\User', 10, NULL, '{\"email\":\"nayanarifhossain@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:11:31'),
(377, 10, 'user.login', 'App\\Models\\User', 10, NULL, '{\"email\":\"nayanarifhossain@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:12:06'),
(378, 14, 'user.login', 'App\\Models\\User', 14, NULL, '{\"email\":\"mdtanimalmahmud0198@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:13:41'),
(379, 10, 'user.logout', 'App\\Models\\User', 10, NULL, '{\"email\":\"nayanarifhossain@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:18:36'),
(380, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.141.52\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:20:18'),
(381, 8, 'user.login', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:20:21'),
(382, 9, 'role.assigned', 'App\\Models\\User', 14, NULL, '{\"role_id\":6,\"role_slug\":\"application\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:29:55'),
(383, 8, 'user.logout', 'App\\Models\\User', 8, NULL, '{\"email\":\"abu221haider@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:31:04'),
(384, 10, 'user.login', 'App\\Models\\User', 10, NULL, '{\"email\":\"nayanarifhossain@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:31:25'),
(385, 10, 'user.logout', 'App\\Models\\User', 10, NULL, '{\"email\":\"nayanarifhossain@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:37:57'),
(386, 12, 'user.login', 'App\\Models\\User', 12, NULL, '{\"email\":\"sharafatullahmohim@gmail.com\"}', '{\"ip\":\"202.83.124.80\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-13 08:39:57'),
(387, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.141.58\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 04:12:23'),
(388, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\":\"application@example.com\"}', '{\"ip\":\"103.88.141.58\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 04:20:36'),
(389, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\":\"application@example.com\"}', '{\"ip\":\"103.88.141.58\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 04:22:10'),
(390, 7, 'user.login', 'App\\Models\\User', 7, NULL, '{\"email\":\"digital_marketing@example.com\"}', '{\"ip\":\"103.88.141.58\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 04:22:46'),
(391, 7, 'user.logout', 'App\\Models\\User', 7, NULL, '{\"email\":\"digital_marketing@example.com\"}', '{\"ip\":\"103.88.141.58\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 04:27:08'),
(392, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\":\"application@example.com\"}', '{\"ip\":\"103.88.141.58\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 04:27:28'),
(393, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\":\"application@example.com\"}', '{\"ip\":\"103.88.141.58\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 04:38:02'),
(394, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\":\"editor@example.com\"}', '{\"ip\":\"103.88.141.58\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 04:38:27'),
(395, 5, 'user.login', 'App\\Models\\User', 5, NULL, '{\"email\":\"editor@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 05:29:37'),
(396, 5, 'user.logout', 'App\\Models\\User', 5, NULL, '{\"email\":\"editor@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 07:31:51'),
(397, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\":\"consultant@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 07:32:07'),
(398, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\":\"consultant@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 07:36:59'),
(399, 7, 'user.login', 'App\\Models\\User', 7, NULL, '{\"email\":\"digital_marketing@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 07:37:29'),
(400, 7, 'user.logout', 'App\\Models\\User', 7, NULL, '{\"email\":\"digital_marketing@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 07:45:18'),
(401, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 07:45:27'),
(402, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 08:01:22'),
(403, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\":\"application@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 08:01:31'),
(404, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\":\"application@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 08:02:00'),
(405, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 08:02:17'),
(406, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 08:06:03'),
(407, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\":\"application@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 08:06:12'),
(408, 6, 'user.logout', 'App\\Models\\User', 6, NULL, '{\"email\":\"application@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 08:47:47'),
(409, 3, 'user.login', 'App\\Models\\User', 3, NULL, '{\"email\":\"consultant@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 08:47:57'),
(410, 3, 'user.logout', 'App\\Models\\User', 3, NULL, '{\"email\":\"consultant@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 08:48:25'),
(411, 6, 'user.login', 'App\\Models\\User', 6, NULL, '{\"email\":\"application@example.com\"}', '{\"ip\":\"192.168.0.107\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-14 08:48:36'),
(412, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.181\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:150.0) Gecko\\/20100101 Firefox\\/150.0\",\"is_console\":false}', '2026-05-18 11:01:01'),
(413, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.181\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-22 04:24:39'),
(414, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.181\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/148.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-05-22 11:21:01'),
(415, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.141.61\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-06-15 04:01:23'),
(416, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.0.235.62\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-06-18 16:55:34'),
(417, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.0.235.62\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-06-22 10:33:26'),
(418, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.0.235.62\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-06-22 13:09:16'),
(419, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.0.235.62\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/149.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-07-02 04:16:48'),
(420, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.180\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko\\/20100101 Firefox\\/152.0\",\"is_console\":false}', '2026-07-06 10:45:51'),
(421, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.180\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko\\/20100101 Firefox\\/152.0\",\"is_console\":false}', '2026-07-06 10:57:45'),
(422, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.176\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/150.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-07-13 09:53:40'),
(423, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.181\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/150.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-07-20 12:40:52'),
(424, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\":\"accountant@example.com\"}', '{\"ip\":\"103.88.140.177\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/150.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-07-20 12:52:04'),
(425, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.183\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/150.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-07-26 11:16:21'),
(426, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.176\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/150.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-08-02 05:36:29'),
(427, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.182\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-08-13 06:03:31'),
(428, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"103.88.140.177\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/151.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-08-18 04:33:22'),
(429, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/152.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-09-17 04:00:16'),
(430, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"172.68.132.154\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/152.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-09-17 04:14:50'),
(431, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/152.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-09-17 04:18:51'),
(432, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.193.115\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/152.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-09-17 09:47:40'),
(433, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.34.143\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/152.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-09-17 21:34:37'),
(434, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"172.68.132.154\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/153.0.0.0 Mobile Safari\\/537.36\",\"is_console\":false}', '2026-09-18 11:24:45'),
(435, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-09-18 18:34:10'),
(436, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-09-23 05:25:01'),
(437, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.134\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-09-23 05:25:20'),
(438, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"104.22.29.147\",\"user_agent\":\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-19 12:30:34'),
(439, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Mobile Safari\\/537.36\",\"is_console\":false}', '2026-10-19 12:37:42'),
(440, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko\\/20100101 Firefox\\/158.0\",\"is_console\":false}', '2026-10-20 05:17:19'),
(441, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 04:24:24'),
(442, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:05:24'),
(443, 4, 'user.login', 'App\\Models\\User', 4, NULL, '{\"email\":\"accountant@example.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:13:09'),
(444, 4, 'user.logout', 'App\\Models\\User', 4, NULL, '{\"email\":\"accountant@example.com\"}', '{\"ip\":\"162.158.9.134\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:17:58'),
(445, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:18:15'),
(446, 1, 'role.assigned', 'App\\Models\\User', 1, NULL, '{\"role_id\":4,\"role_slug\":\"accountant\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:18:59'),
(447, 1, 'role.assigned', 'App\\Models\\User', 1, NULL, '{\"role_id\":6,\"role_slug\":\"application\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:18:59'),
(448, 1, 'role.assigned', 'App\\Models\\User', 1, NULL, '{\"role_id\":3,\"role_slug\":\"consultant\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:18:59'),
(449, 1, 'role.assigned', 'App\\Models\\User', 1, NULL, '{\"role_id\":11,\"role_slug\":\"digital-marketing\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:18:59'),
(450, 1, 'role.assigned', 'App\\Models\\User', 1, NULL, '{\"role_id\":5,\"role_slug\":\"editor\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:18:59'),
(451, 1, 'role.assigned', 'App\\Models\\User', 1, NULL, '{\"role_id\":2,\"role_slug\":\"marketing\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:18:59'),
(452, 1, 'role.assigned', 'App\\Models\\User', 1, NULL, '{\"role_id\":10,\"role_slug\":\"super-admin\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:18:59'),
(453, 1, 'role.assigned', 'App\\Models\\User', 1, NULL, '{\"role_id\":7,\"role_slug\":\"user\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 05:18:59'),
(454, 1, 'user.logout', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.135\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 07:40:30'),
(455, 1, 'user.login', 'App\\Models\\User', 1, NULL, '{\"email\":\"hello@inoodex.com\"}', '{\"ip\":\"162.158.9.134\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"is_console\":false}', '2026-10-21 07:45:57');

-- --------------------------------------------------------

--
-- Table structure for table `tyro_media`
--

CREATE TABLE `tyro_media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `webp_path` varchar(255) DEFAULT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `disk` varchar(50) NOT NULL DEFAULT 'public',
  `mime_type` varchar(100) NOT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `is_favorite` tinyint(1) NOT NULL DEFAULT 0,
  `source_url` varchar(2048) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tyro_media_categories`
--

CREATE TABLE `tyro_media_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tyro_media_category_media`
--

CREATE TABLE `tyro_media_category_media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_category_id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tyro_smtp_presets`
--

CREATE TABLE `tyro_smtp_presets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `mailer` varchar(255) NOT NULL DEFAULT 'smtp',
  `host` varchar(255) NOT NULL DEFAULT '',
  `port` smallint(5) UNSIGNED NOT NULL DEFAULT 587,
  `encryption` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `from_address` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tyro_starred_import_images`
--

CREATE TABLE `tyro_starred_import_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `star_key` varchar(64) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `external_id` varchar(255) DEFAULT NULL,
  `alt` text DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `thumb_url` varchar(2048) DEFAULT NULL,
  `preview_url` varchar(2048) DEFAULT NULL,
  `download_url` varchar(2048) DEFAULT NULL,
  `download_location` varchar(2048) DEFAULT NULL,
  `source_url` varchar(2048) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `starred_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `universities`
--

CREATE TABLE `universities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `universities`
--

INSERT INTO `universities` (`id`, `country_id`, `name`, `short_name`, `website`, `email`, `phone`, `address`, `status`, `created_at`, `updated_at`) VALUES
(3, 4, 'University of Malta', NULL, 'https://www.um.edu.mt/', 'info@um.edu.mt', '+356 2340 2340', 'University of Malta, Msida MSD 2080, Malta', 1, '2026-04-14 23:36:55', '2026-04-14 23:36:55'),
(4, 5, 'Universiti Malaya', NULL, 'https://www.um.edu.my/', 'umcced@um.edu.my', '+603-2246 3633', 'Level 9, Chancellery Universiti Malaya, Lingkungan Budi, 50603 Kuala Lumpur,', 1, '2026-04-14 23:51:01', '2026-04-14 23:51:01'),
(5, 6, 'Moscow State University', NULL, 'https://msu.ru/en/', 'info@rector.msu.ru', '+7 (495) 939-10-00', NULL, 1, '2026-04-27 04:01:04', '2026-04-27 04:01:04'),
(6, 4, 'Learnkey Institue', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-13 11:51:33', '2026-05-13 11:51:33'),
(7, 4, 'CROSS COLLEGE MALTA', 'CCM', NULL, NULL, NULL, NULL, 1, '2026-05-13 12:07:41', '2026-05-13 12:09:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `suspension_reason` text DEFAULT NULL,
  `profile_photo_path` varchar(2048) DEFAULT NULL,
  `use_gravatar` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `designation`, `email_verified_at`, `password`, `plain_password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`, `suspended_at`, `suspension_reason`, `profile_photo_path`, `use_gravatar`) VALUES
(1, 'Inoodex', 'hello@inoodex.com', 'UID000001', NULL, NULL, '$2y$12$bZ9YAxTXRcahQbLiZ6d8q.cXUkxAbriZ.WtNmnNPrwcOA0PppnPJO', NULL, NULL, NULL, NULL, '20OzeOoprdKtBKGPph5RVsk0C52bgvY866bWrQ1LpmXfgL25Kmu4ZRRQf1xH', '2026-02-16 03:09:49', '2026-04-30 01:11:07', NULL, NULL, NULL, 0),
(2, 'Marketing', 'marketing@example.com', 'UID000002', NULL, NULL, '$2y$12$ei9LSrLrIhng1FNUsEZztOnKxDIZ3stVVswIXvKK8Dvphk3pTs5BO', NULL, NULL, NULL, NULL, NULL, '2026-02-17 12:46:55', '2026-04-30 01:11:07', NULL, NULL, NULL, 0),
(3, 'Consultant', 'consultant@example.com', 'UID000003', 'senior', NULL, '$2y$12$m3ZggFrGDw7maLPG1tU8DuKDOTSWUUeSzEFcW1fSfK7xCHJmwJTVW', NULL, NULL, NULL, NULL, NULL, '2026-02-17 12:49:33', '2026-05-11 16:46:07', NULL, NULL, NULL, 0),
(4, 'Accountant', 'accountant@example.com', 'UID000004', NULL, NULL, '$2y$12$eF1/DUs.OmbYrUHoLL0Z.uO5HCJSsdaYSknKdu6Sl1tnYF.dhRC.G', NULL, NULL, NULL, NULL, NULL, '2026-02-17 23:01:58', '2026-04-30 01:11:07', NULL, NULL, NULL, 0),
(5, 'Editor', 'editor@example.com', 'editor1', 'junior', NULL, '$2y$12$cmDPJgBl7B/V8acxTv3Ej.15/p9DkpiY/G/cSFL9j2iwTkKmbbjXW', NULL, NULL, NULL, NULL, NULL, '2026-02-18 02:45:29', '2026-05-11 16:46:20', NULL, NULL, NULL, 0),
(6, 'Application', 'application@example.com', 'UID000006', 'senior', NULL, '$2y$12$vZrAn3nHsyH1FOtRvuBW4elSdagHiBA4YnoI2.QdnHkYqeI6MmL1e', NULL, NULL, NULL, NULL, NULL, '2026-02-18 22:39:12', '2026-05-10 23:15:44', NULL, NULL, NULL, 0),
(9, 'Mohammad Faisal', 'mdfoysal103545@gmail.com', '1203', NULL, NULL, '$2y$12$/sf73.XGRKyDmdNc/fPCnOJiGBMVly57lEjBUNf/at755nRc5fehm', NULL, NULL, NULL, NULL, 'tkha67g7hitKoSDehu5yjH1mpxhMXliAPUnEAbpahnvNX45uuVEjORyLy8ib', '2026-05-13 10:19:56', '2026-05-13 10:19:56', NULL, NULL, NULL, 0),
(10, 'Arif Hossain Nayan', 'nayanarifhossain@gmail.com', '1204', 'senior', NULL, '$2y$12$TjoktVCVNlHT/LqWfU6NPuuNFQKCtcAyTtWjgjqOveQEoolurdyHG', NULL, NULL, NULL, NULL, 'Cd68bwEiUHW9TysLoGSuwpUal3XNmcsqLglvWFFkg4XaHLvxrvh0s9CCjmOJ', '2026-05-13 10:45:50', '2026-05-13 10:45:50', NULL, NULL, NULL, 0),
(11, 'Mosharaful Islam', 'mosharafulislam5490@gmail.com', '12012', NULL, NULL, '$2y$12$o37wSC5/qEjZpXF8crOLUuGcq/eYqP.7Rg0Ip13/4VVhFfsewAXlq', NULL, NULL, NULL, NULL, NULL, '2026-05-13 11:00:43', '2026-05-13 11:00:43', NULL, NULL, NULL, 0),
(12, 'Sharafat Ullah Mohim', 'sharafatullahmohim@gmail.com', '1207', NULL, NULL, '$2y$12$GsnHNIyZiBUYF9/ZMbsKC.grtAcfJ2bJ0MncHx.Anecncdrc4k3PK', NULL, NULL, NULL, NULL, NULL, '2026-05-13 11:49:46', '2026-05-13 12:39:25', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-02-16 03:14:04', '2026-02-16 03:14:04'),
(2, 2, 2, '2026-02-17 12:46:55', '2026-02-17 12:46:55'),
(3, 3, 3, '2026-02-17 13:02:10', '2026-02-17 13:02:10'),
(4, 4, 4, '2026-02-17 23:03:02', '2026-02-17 23:03:02'),
(5, 5, 5, '2026-02-18 02:45:29', '2026-02-18 02:45:29'),
(6, 6, 6, '2026-02-18 22:47:11', '2026-02-18 22:47:11'),
(11, 9, 1, '2026-05-13 10:19:56', '2026-05-13 10:19:56'),
(12, 9, 2, '2026-05-13 10:19:56', '2026-05-13 10:19:56'),
(13, 9, 3, '2026-05-13 10:19:56', '2026-05-13 10:19:56'),
(14, 9, 4, '2026-05-13 10:19:56', '2026-05-13 10:19:56'),
(15, 9, 5, '2026-05-13 10:19:56', '2026-05-13 10:19:56'),
(16, 9, 6, '2026-05-13 10:19:56', '2026-05-13 10:19:56'),
(17, 9, 7, '2026-05-13 10:19:56', '2026-05-13 10:19:56'),
(18, 9, 10, '2026-05-13 10:19:56', '2026-05-13 10:19:56'),
(19, 9, 11, '2026-05-13 10:19:56', '2026-05-13 10:19:56'),
(20, 10, 2, '2026-05-13 10:45:50', '2026-05-13 10:45:50'),
(21, 10, 6, '2026-05-13 10:45:50', '2026-05-13 10:45:50'),
(22, 10, 11, '2026-05-13 10:45:50', '2026-05-13 10:45:50'),
(23, 11, 3, '2026-05-13 11:00:43', '2026-05-13 11:00:43'),
(24, 11, 5, '2026-05-13 11:06:35', '2026-05-13 11:06:35'),
(26, 12, 3, '2026-05-13 11:49:46', '2026-05-13 11:49:46'),
(27, 12, 5, '2026-05-13 11:49:46', '2026-05-13 11:49:46'),
(28, 12, 6, '2026-05-13 11:49:46', '2026-05-13 11:49:46'),
(32, 1, 2, '2026-10-21 05:18:59', '2026-10-21 05:18:59'),
(33, 1, 3, '2026-10-21 05:18:59', '2026-10-21 05:18:59'),
(34, 1, 4, '2026-10-21 05:18:59', '2026-10-21 05:18:59'),
(35, 1, 5, '2026-10-21 05:18:59', '2026-10-21 05:18:59'),
(36, 1, 6, '2026-10-21 05:18:59', '2026-10-21 05:18:59'),
(37, 1, 7, '2026-10-21 05:18:59', '2026-10-21 05:18:59'),
(38, 1, 10, '2026-10-21 05:18:59', '2026-10-21 05:18:59'),
(39, 1, 11, '2026-10-21 05:18:59', '2026-10-21 05:18:59');

-- --------------------------------------------------------

--
-- Table structure for table `vfs_checklists`
--

CREATE TABLE `vfs_checklists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `checklist_item` varchar(255) NOT NULL,
  `is_checked` tinyint(1) NOT NULL DEFAULT 0,
  `checked_by` bigint(20) UNSIGNED DEFAULT NULL,
  `checked_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vfs_checklists`
--

INSERT INTO `vfs_checklists` (`id`, `application_id`, `checklist_item`, `is_checked`, `checked_by`, `checked_at`, `notes`, `created_at`, `updated_at`) VALUES
(185, 21, 'Visa Application', 0, NULL, NULL, NULL, '2026-10-21 05:19:37', '2026-10-21 05:19:37'),
(186, 21, 'Passport', 1, 1, '2026-10-21 05:20:20', NULL, '2026-10-21 05:19:37', '2026-10-21 05:20:20'),
(187, 21, 'Academic Transcripts (Education Board and ministry attestation)', 1, 1, '2026-10-21 05:20:20', NULL, '2026-10-21 05:19:37', '2026-10-21 05:20:20'),
(188, 21, 'CV', 0, NULL, NULL, NULL, '2026-10-21 05:19:37', '2026-10-21 05:19:37'),
(189, 21, 'Final Offer Letter and college others documents', 0, NULL, NULL, NULL, '2026-10-21 05:19:37', '2026-10-21 05:19:37'),
(190, 21, 'Birth Certificate (Notarize and attested)', 1, 1, '2026-10-21 05:20:20', NULL, '2026-10-21 05:19:37', '2026-10-21 05:20:20'),
(191, 21, 'Insurance', 0, NULL, NULL, NULL, '2026-10-21 05:19:37', '2026-10-21 05:19:37'),
(192, 21, 'Student Bank ATM Card', 0, NULL, NULL, NULL, '2026-10-21 05:19:37', '2026-10-21 05:19:37'),
(193, 21, 'Sponsor NID (Translation and notarize)', 1, 1, '2026-10-21 05:20:20', NULL, '2026-10-21 05:19:37', '2026-10-21 05:20:20'),
(194, 21, 'Applicant NID (Translation and notarize)', 1, 1, '2026-10-21 05:20:20', NULL, '2026-10-21 05:19:37', '2026-10-21 05:20:20'),
(195, 21, 'Sponsor Bank ATM Card', 0, NULL, NULL, NULL, '2026-10-21 05:19:37', '2026-10-21 05:19:37'),
(196, 21, 'Applicants ATM Card', 0, NULL, NULL, NULL, '2026-10-21 05:19:37', '2026-10-21 05:19:37'),
(197, 21, 'Financial Declaration Affidavit', 0, NULL, NULL, NULL, '2026-10-21 05:19:37', '2026-10-21 05:19:37');

-- --------------------------------------------------------

--
-- Table structure for table `vfs_checklist_templates`
--

CREATE TABLE `vfs_checklist_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vfs_checklist_templates`
--

INSERT INTO `vfs_checklist_templates` (`id`, `item_name`, `country_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'VFS Appointment', 5, 0, 1, '2026-04-21 05:24:49', '2026-04-22 03:49:26'),
(2, 'Visa Application', 4, 1, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(3, 'Photo 35X45', 5, 2, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(4, 'Passport', 4, 3, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(5, 'Academic Certificates (Education Board and ministry attestation)', 5, 4, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(6, 'Academic Transcripts (Education Board and ministry attestation)', 4, 5, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(7, 'English Proficiency (If any)', 5, 6, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(8, 'CV', 4, 7, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(9, 'Motivation Letter', 5, 8, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(10, 'Final Offer Letter and college others documents', 4, 9, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(11, 'Accommodation', 5, 10, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(12, 'Birth Certificate (Notarize and attested)', 4, 11, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(13, 'Insurance', 4, 12, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(14, 'Flight Booking', 5, 13, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(15, 'Student Bank ATM Card', 4, 14, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(16, 'Sponsor NID (Translation and notarize)', 4, 15, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(17, 'Applicant NID (Translation and notarize)', 4, 16, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(18, 'Sponsor Income Source (Trade License or Job Certificate) (Translation and notarize)', 5, 17, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(19, 'TIN certificate', 5, 18, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(20, 'TAX certificate 2 years', 5, 19, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(21, 'Bank Statement', 5, 20, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(22, 'Sponsor Bank ATM Card', 4, 21, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(23, 'Applicants ATM Card', 4, 22, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(24, 'Bank Account Cheque Book copy', 5, 23, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(25, 'Deposit Slip (If possible)', 5, 24, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34'),
(26, 'Financial Declaration Affidavit', 4, 25, 1, '2026-04-21 05:24:49', '2026-04-21 05:25:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounting_periods`
--
ALTER TABLE `accounting_periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounting_periods_year_month_type_unique` (`year`,`month`,`type`),
  ADD KEY `accounting_periods_closed_by_foreign` (`closed_by`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `applications_application_id_unique` (`application_id`),
  ADD KEY `applications_student_id_foreign` (`student_id`),
  ADD KEY `applications_university_id_foreign` (`university_id`),
  ADD KEY `applications_course_id_foreign` (`course_id`),
  ADD KEY `applications_course_intake_id_foreign` (`course_intake_id`),
  ADD KEY `applications_created_by_foreign` (`created_by`);

--
-- Indexes for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_reconciliations_account_id_foreign` (`account_id`),
  ADD KEY `bank_reconciliations_closed_by_foreign` (`closed_by`);

--
-- Indexes for table `bank_reconciliation_items`
--
ALTER TABLE `bank_reconciliation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_reconciliation_items_reconciliation_id_foreign` (`reconciliation_id`),
  ADD KEY `bank_reconciliation_items_matched_by_foreign` (`matched_by`),
  ADD KEY `bank_reconciliation_items_journal_entry_item_id_foreign` (`journal_entry_item_id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budgets_created_by_foreign` (`created_by`),
  ADD KEY `budgets_chart_of_account_id_foreign` (`chart_of_account_id`);

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
-- Indexes for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chart_of_accounts_code_unique` (`code`),
  ADD KEY `chart_of_accounts_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commissions_application_id_foreign` (`application_id`),
  ADD KEY `commissions_user_id_foreign` (`user_id`),
  ADD KEY `commissions_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courses_university_id_foreign` (`university_id`);

--
-- Indexes for table `course_intakes`
--
ALTER TABLE `course_intakes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_intakes_course_id_foreign` (`course_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currencies_code_unique` (`code`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_created_by_foreign` (`created_by`),
  ADD KEY `expenses_office_account_id_foreign` (`office_account_id`),
  ADD KEY `expenses_salary_id_foreign` (`salary_id`),
  ADD KEY `expenses_chart_of_account_id_foreign` (`chart_of_account_id`),
  ADD KEY `expenses_journal_entry_id_foreign` (`journal_entry_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invitation_links`
--
ALTER TABLE `invitation_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invitation_links_hash_unique` (`hash`),
  ADD KEY `invitation_links_user_id_index` (`user_id`);

--
-- Indexes for table `invitation_referrals`
--
ALTER TABLE `invitation_referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invitation_referrals_invitation_link_id_index` (`invitation_link_id`),
  ADD KEY `invitation_referrals_referred_user_id_index` (`referred_user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_application_id_foreign` (`application_id`),
  ADD KEY `invoices_student_id_foreign` (`student_id`),
  ADD KEY `invoices_university_id_foreign` (`university_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_items_chart_of_account_id_foreign` (`chart_of_account_id`);

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
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journal_entries_reference_number_unique` (`reference_number`),
  ADD KEY `journal_entries_period_id_foreign` (`period_id`),
  ADD KEY `journal_entries_created_by_foreign` (`created_by`),
  ADD KEY `journal_entries_application_id_foreign` (`application_id`);

--
-- Indexes for table `journal_entry_items`
--
ALTER TABLE `journal_entry_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entry_items_journal_entry_id_foreign` (`journal_entry_id`),
  ADD KEY `journal_entry_items_chart_of_account_id_foreign` (`chart_of_account_id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leads_created_by_foreign` (`created_by`),
  ADD KEY `leads_consultant_id_foreign` (`consultant_id`),
  ADD KEY `leads_preferred_country_foreign` (`preferred_country`),
  ADD KEY `leads_preferred_course_foreign` (`preferred_course`);

--
-- Indexes for table `marketing_campaigns`
--
ALTER TABLE `marketing_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_campaigns_created_by_foreign` (`created_by`);

--
-- Indexes for table `marketing_documents`
--
ALTER TABLE `marketing_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_documents_application_id_foreign` (`application_id`),
  ADD KEY `marketing_documents_created_by_foreign` (`created_by`);

--
-- Indexes for table `marketing_posters`
--
ALTER TABLE `marketing_posters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marketing_videos`
--
ALTER TABLE `marketing_videos`
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `office_accounts`
--
ALTER TABLE `office_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `office_accounts_created_by_foreign` (`created_by`),
  ADD KEY `office_accounts_chart_of_account_id_foreign` (`chart_of_account_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_collected_by_foreign` (`collected_by`),
  ADD KEY `payments_student_id_payment_type_payment_status_index` (`student_id`,`payment_type`,`payment_status`),
  ADD KEY `payments_application_id_foreign` (`application_id`),
  ADD KEY `payments_office_account_id_foreign` (`office_account_id`),
  ADD KEY `payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `payments_journal_entry_id_foreign` (`journal_entry_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `privileges`
--
ALTER TABLE `privileges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `privileges_slug_unique` (`slug`);

--
-- Indexes for table `privilege_role`
--
ALTER TABLE `privilege_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `privilege_role_role_id_privilege_id_unique` (`role_id`,`privilege_id`),
  ADD KEY `privilege_role_privilege_id_foreign` (`privilege_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `roles_slug_index` (`slug`);

--
-- Indexes for table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `salaries_user_id_month_unique` (`user_id`,`month`),
  ADD KEY `salaries_created_by_foreign` (`created_by`),
  ADD KEY `salaries_payment_status_month_index` (`payment_status`,`month`),
  ADD KEY `salaries_journal_entry_id_foreign` (`journal_entry_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `social_accounts`
--
ALTER TABLE `social_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `social_accounts_provider_provider_user_id_unique` (`provider`,`provider_user_id`),
  ADD KEY `social_accounts_provider_provider_user_id_index` (`provider`,`provider_user_id`),
  ADD KEY `social_accounts_user_id_index` (`user_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `students_country_id_foreign` (`country_id`),
  ADD KEY `students_university_id_foreign` (`university_id`),
  ADD KEY `students_course_id_foreign` (`course_id`),
  ADD KEY `students_course_intake_id_foreign` (`course_intake_id`),
  ADD KEY `students_assigned_consultant_id_foreign` (`assigned_consultant_id`),
  ADD KEY `students_assigned_application_id_foreign` (`assigned_application_id`),
  ADD KEY `students_created_by_foreign` (`created_by`),
  ADD KEY `students_current_stage_current_status_index` (`current_stage`,`current_status`),
  ADD KEY `students_assignment_idx` (`assigned_marketing_id`,`assigned_consultant_id`,`assigned_application_id`,`created_by`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `taxes_chart_of_account_id_foreign` (`chart_of_account_id`);

--
-- Indexes for table `tyro_audit_logs`
--
ALTER TABLE `tyro_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tyro_audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  ADD KEY `tyro_audit_logs_user_id_index` (`user_id`),
  ADD KEY `tyro_audit_logs_event_index` (`event`);

--
-- Indexes for table `tyro_media`
--
ALTER TABLE `tyro_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tyro_media_user_id_foreign` (`user_id`),
  ADD KEY `tyro_media_is_favorite_index` (`is_favorite`);

--
-- Indexes for table `tyro_media_categories`
--
ALTER TABLE `tyro_media_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tyro_media_categories_user_id_slug_index` (`user_id`,`slug`);

--
-- Indexes for table `tyro_media_category_media`
--
ALTER TABLE `tyro_media_category_media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_category_media_unique` (`media_category_id`,`media_id`),
  ADD KEY `tyro_media_category_media_media_id_foreign` (`media_id`);

--
-- Indexes for table `tyro_smtp_presets`
--
ALTER TABLE `tyro_smtp_presets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tyro_smtp_presets_name_unique` (`name`);

--
-- Indexes for table `tyro_starred_import_images`
--
ALTER TABLE `tyro_starred_import_images`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tyro_starred_import_images_user_id_star_key_unique` (`user_id`,`star_key`),
  ADD KEY `tyro_starred_import_images_user_id_starred_at_index` (`user_id`,`starred_at`);

--
-- Indexes for table `universities`
--
ALTER TABLE `universities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `universities_country_id_foreign` (`country_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`);

--
-- Indexes for table `vfs_checklists`
--
ALTER TABLE `vfs_checklists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vfs_checklists_application_id_foreign` (`application_id`),
  ADD KEY `vfs_checklists_checked_by_foreign` (`checked_by`);

--
-- Indexes for table `vfs_checklist_templates`
--
ALTER TABLE `vfs_checklist_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vfs_checklist_templates_country_id_foreign` (`country_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounting_periods`
--
ALTER TABLE `accounting_periods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_reconciliation_items`
--
ALTER TABLE `bank_reconciliation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `course_intakes`
--
ALTER TABLE `course_intakes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invitation_links`
--
ALTER TABLE `invitation_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invitation_referrals`
--
ALTER TABLE `invitation_referrals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entry_items`
--
ALTER TABLE `journal_entry_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_campaigns`
--
ALTER TABLE `marketing_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `marketing_documents`
--
ALTER TABLE `marketing_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `marketing_posters`
--
ALTER TABLE `marketing_posters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `marketing_videos`
--
ALTER TABLE `marketing_videos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `office_accounts`
--
ALTER TABLE `office_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privileges`
--
ALTER TABLE `privileges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `privilege_role`
--
ALTER TABLE `privilege_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `social_accounts`
--
ALTER TABLE `social_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tyro_audit_logs`
--
ALTER TABLE `tyro_audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tyro_media`
--
ALTER TABLE `tyro_media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tyro_media_categories`
--
ALTER TABLE `tyro_media_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tyro_media_category_media`
--
ALTER TABLE `tyro_media_category_media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tyro_smtp_presets`
--
ALTER TABLE `tyro_smtp_presets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tyro_starred_import_images`
--
ALTER TABLE `tyro_starred_import_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `universities`
--
ALTER TABLE `universities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `vfs_checklists`
--
ALTER TABLE `vfs_checklists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT for table `vfs_checklist_templates`
--
ALTER TABLE `vfs_checklist_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounting_periods`
--
ALTER TABLE `accounting_periods`
  ADD CONSTRAINT `accounting_periods_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `applications_course_intake_id_foreign` FOREIGN KEY (`course_intake_id`) REFERENCES `course_intakes` (`id`),
  ADD CONSTRAINT `applications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `applications_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`);

--
-- Constraints for table `bank_reconciliations`
--
ALTER TABLE `bank_reconciliations`
  ADD CONSTRAINT `bank_reconciliations_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `office_accounts` (`id`),
  ADD CONSTRAINT `bank_reconciliations_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `bank_reconciliation_items`
--
ALTER TABLE `bank_reconciliation_items`
  ADD CONSTRAINT `bank_reconciliation_items_journal_entry_item_id_foreign` FOREIGN KEY (`journal_entry_item_id`) REFERENCES `journal_entry_items` (`id`),
  ADD CONSTRAINT `bank_reconciliation_items_matched_by_foreign` FOREIGN KEY (`matched_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bank_reconciliation_items_reconciliation_id_foreign` FOREIGN KEY (`reconciliation_id`) REFERENCES `bank_reconciliations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `chart_of_accounts` (`id`),
  ADD CONSTRAINT `budgets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD CONSTRAINT `chart_of_accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `commissions`
--
ALTER TABLE `commissions`
  ADD CONSTRAINT `commissions_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commissions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `commissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_intakes`
--
ALTER TABLE `course_intakes`
  ADD CONSTRAINT `course_intakes_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `chart_of_accounts` (`id`),
  ADD CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`),
  ADD CONSTRAINT `expenses_office_account_id_foreign` FOREIGN KEY (`office_account_id`) REFERENCES `office_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_salary_id_foreign` FOREIGN KEY (`salary_id`) REFERENCES `salaries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invitation_referrals`
--
ALTER TABLE `invitation_referrals`
  ADD CONSTRAINT `invitation_referrals_invitation_link_id_foreign` FOREIGN KEY (`invitation_link_id`) REFERENCES `invitation_links` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `invoices_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`);

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `chart_of_accounts` (`id`),
  ADD CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `journal_entries_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_entries_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `accounting_periods` (`id`);

--
-- Constraints for table `journal_entry_items`
--
ALTER TABLE `journal_entry_items`
  ADD CONSTRAINT `journal_entry_items_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `chart_of_accounts` (`id`),
  ADD CONSTRAINT `journal_entry_items_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_consultant_id_foreign` FOREIGN KEY (`consultant_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `leads_preferred_country_foreign` FOREIGN KEY (`preferred_country`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_preferred_course_foreign` FOREIGN KEY (`preferred_course`) REFERENCES `courses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `marketing_campaigns`
--
ALTER TABLE `marketing_campaigns`
  ADD CONSTRAINT `marketing_campaigns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `marketing_documents`
--
ALTER TABLE `marketing_documents`
  ADD CONSTRAINT `marketing_documents_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_documents_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `office_accounts`
--
ALTER TABLE `office_accounts`
  ADD CONSTRAINT `office_accounts_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `office_accounts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_collected_by_foreign` FOREIGN KEY (`collected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  ADD CONSTRAINT `payments_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`),
  ADD CONSTRAINT `payments_office_account_id_foreign` FOREIGN KEY (`office_account_id`) REFERENCES `office_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `privilege_role`
--
ALTER TABLE `privilege_role`
  ADD CONSTRAINT `privilege_role_privilege_id_foreign` FOREIGN KEY (`privilege_id`) REFERENCES `privileges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `privilege_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `salaries`
--
ALTER TABLE `salaries`
  ADD CONSTRAINT `salaries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `salaries_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`),
  ADD CONSTRAINT `salaries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `social_accounts`
--
ALTER TABLE `social_accounts`
  ADD CONSTRAINT `social_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_assigned_application_id_foreign` FOREIGN KEY (`assigned_application_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_assigned_consultant_id_foreign` FOREIGN KEY (`assigned_consultant_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_assigned_marketing_id_foreign` FOREIGN KEY (`assigned_marketing_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_course_intake_id_foreign` FOREIGN KEY (`course_intake_id`) REFERENCES `course_intakes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_university_id_foreign` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `taxes`
--
ALTER TABLE `taxes`
  ADD CONSTRAINT `taxes_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `chart_of_accounts` (`id`);

--
-- Constraints for table `tyro_media`
--
ALTER TABLE `tyro_media`
  ADD CONSTRAINT `tyro_media_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tyro_media_categories`
--
ALTER TABLE `tyro_media_categories`
  ADD CONSTRAINT `tyro_media_categories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tyro_media_category_media`
--
ALTER TABLE `tyro_media_category_media`
  ADD CONSTRAINT `tyro_media_category_media_media_category_id_foreign` FOREIGN KEY (`media_category_id`) REFERENCES `tyro_media_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tyro_media_category_media_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `tyro_media` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tyro_starred_import_images`
--
ALTER TABLE `tyro_starred_import_images`
  ADD CONSTRAINT `tyro_starred_import_images_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `universities`
--
ALTER TABLE `universities`
  ADD CONSTRAINT `universities_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vfs_checklists`
--
ALTER TABLE `vfs_checklists`
  ADD CONSTRAINT `vfs_checklists_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vfs_checklists_checked_by_foreign` FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `vfs_checklist_templates`
--
ALTER TABLE `vfs_checklist_templates`
  ADD CONSTRAINT `vfs_checklist_templates_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
